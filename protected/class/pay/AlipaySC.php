<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class AlipaySC extends mainClass{
	
	/**
	 * 获取优惠金额
	 * @param unknown $fund_bill_list
	 * @return multitype:Ambigous <number, unknown>
	 */
	public function getDiscounts($fund_bill_list) {
		//资金明细处理
		$merchant_discount = 0; //商家优惠
		$alipay_discount = 0; //支付宝优惠
		if (!empty($fund_bill_list)) {
			//1.0接口返回的xml中包含TradeFundBill节点，需要获取TradeFundBill的子节点信息
			if (isset($fund_bill_list['TradeFundBill'])) {
				$fund_bill_list = $fund_bill_list['TradeFundBill'];
				if(isset($fund_bill_list['fund_channel'])) {
					$fund_bill_list = array($fund_bill_list);
				}
			}
				
			//遍历明细数组
			foreach ($fund_bill_list as $trade_bill) {
				//支付宝1.0接口同步返回数据、支付宝2.0接口同步返回的数据、支付宝2.0接口异步返回的数据
				if ( (isset($trade_bill['fund_channel']) || isset($trade_bill['fundChannel'])) && isset($trade_bill['amount'])) {
					$fund_channel = isset($trade_bill['fund_channel']) ? $trade_bill['fund_channel'] : $trade_bill['fundChannel']; //支付宝支付渠道
					$amount = $trade_bill['amount']; //支付金额
					
					if ($fund_channel == ALIPAY_FUND_CHANNEL_MCOUPON ||
						$fund_channel == ALIPAY_FUND_CHANNEL_MCARD ||
						$fund_channel == ALIPAY_FUND_CHANNEL_MDISCOUNT ||
						$fund_channel === ALIPAY_FUND_CHANNEL_101 ||
						$fund_channel === ALIPAY_FUND_CHANNEL_102 ||
						$fund_channel === ALIPAY_FUND_CHANNEL_104 ) {
						//商家优惠总计
						$merchant_discount += $amount;
					}
					
					if ($fund_channel == ALIPAY_FUND_CHANNEL_COUPON ||
						$fund_channel == ALIPAY_FUND_CHANNEL_POINT ||
						$fund_channel == ALIPAY_FUND_CHANNEL_DISCOUNT ||
						$fund_channel === ALIPAY_FUND_CHANNEL_00 ||
						$fund_channel === ALIPAY_FUND_CHANNEL_30 ||
						$fund_channel === ALIPAY_FUND_CHANNEL_40 ) {
						//支付宝优惠总计
						$alipay_discount += $amount;
					}
				}
				
				//以下为支付宝1.0接口异步返回的数据
				if (isset($trade_bill[ALIPAY_FUND_CHANNEL_COUPON])) {
					$alipay_discount += $trade_bill[ALIPAY_FUND_CHANNEL_COUPON];
				}
				if (isset($trade_bill[ALIPAY_FUND_CHANNEL_POINT])) {
					$alipay_discount += $trade_bill[ALIPAY_FUND_CHANNEL_POINT];
				}
				if (isset($trade_bill[ALIPAY_FUND_CHANNEL_DISCOUNT])) {
					$alipay_discount += $trade_bill[ALIPAY_FUND_CHANNEL_DISCOUNT];
				}
				if (isset($trade_bill[ALIPAY_FUND_CHANNEL_MCOUPON])) {
					$merchant_discount += $trade_bill[ALIPAY_FUND_CHANNEL_MCOUPON];
				}
				if (isset($trade_bill[ALIPAY_FUND_CHANNEL_MCARD])) {
					$merchant_discount += $trade_bill[ALIPAY_FUND_CHANNEL_MCARD];
				}
				if (isset($trade_bill[ALIPAY_FUND_CHANNEL_MDISCOUNT])) {
					$merchant_discount += $trade_bill[ALIPAY_FUND_CHANNEL_MDISCOUNT];
				}
			}
		}
		
		return array('merchant_discount' => $merchant_discount, 'alipay_discount' => $alipay_discount);
	}
	
	/**
	 * 条码支付
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function barcodePay($order_no){
		$result = array();
		$transaction = Yii::app()->db->beginTransaction(); //开启事务
		try {
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) { //储值订单
				$cmd = Yii::app()->db->createCommand();
				$cmd->select('o.stored_id, o.store_id, o.num, o.user_code, s.stored_money'); //查询字段
				$cmd->from(array('wq_stored_order o', 'wq_stored s')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.stored_id = s.id', //联表
						'o.order_no = :order_no',
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取所有行数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				$store_id = $model['store_id']; //门店id
				//获取收款信息
				$store = new StoreC();
				$ret = $store->getAlipaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$seller = '';
				$partner = $info['alipay_pid'];
				$key = $info['alipay_key'];
				$appid = $info['alipay_appid'];
				$api_version = $info['alipay_api_version'];
				$alipay_store_id = $info['alipay_store_id'];
				$alipay_seller_id = $info['alipay_seller_id'];
				$auth_token = $info['alipay_auth_token'];
				$audit_status = $info['alipay_audit_status'];
				$category_id = $info['category_id'];
				
				$money = $model['stored_money'] * $model['num'];
				$user_code = $model['user_code'];
                                
				$api = new AlipayApi();
				if ($api_version == ALIPAY_API_VERSION_1_API_API) {
					$response = $api->barCodeApi($order_no, $money, $user_code, $seller, $partner, $key, $alipay_store_id);
				}
				if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
					$response = $api->barCodeApiV2($order_no, $money, '', $user_code, $appid, $alipay_seller_id, $alipay_store_id, $auth_token);
				}
				if (!$response) {
					$result['status'] = ERROR_EXCEPTION;
					throw new Exception('请求接口失败');
				}
				
				//设置订单的返佣信息
// 				$order1 = new OrderSC();
// 				$res = $order1->countOrderCommission($order_no, $api_version, $audit_status, $category_id);
// 				if ($res['status'] != ERROR_NONE) {
// 					throw new Exception($res['errMsg']);
// 				}
				
				//返回请求结果
				$result_code = $api->getVal($response, 'result_code'); //结果码
				$trade_no = $api->getVal($response, 'trade_no'); //流水号
				$detail_error_des = $api->getVal($response, 'detail_error_des'); //错误描述
				$gmt_payment = $api->getVal($response, 'gmt_payment');  //交易付款时间
				$buyer_logon_id = $api->getVal($response, 'buyer_logon_id');//买家支付宝账号
				$buyer_user_id = $api->getVal($response, 'buyer_user_id'); //买家支付宝用户号（pid）
				$error = $api->getVal($response, 'error'); //其他错误
				$fund_bill_list = $api->getVal($response, 'fund_bill_list'); //交易资金明细信息
				
				//资金明细处理
				$discounts = $this->getDiscounts($fund_bill_list);
				$merchant_discount = $discounts['merchant_discount']; //商家优惠
				$alipay_discount = $discounts['alipay_discount']; //支付宝优惠
					
				//下单成功并且支付成功
				if ($result_code == ALIPAY_ORDER_SUCCESS_PAY_SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) {
					//修改订单
					$order = new MemberStoredC();
					$ret = $order->orderPaySuccess($order_no, $gmt_payment, $trade_no, $buyer_logon_id, $merchant_discount, $alipay_discount, $buyer_user_id);
					if (empty($ret)) {
						$result['status'] = ERROR_EXCEPTION;
						throw new Exception('订单修改失败');
					}
					if ($ret['status'] == ERROR_NONE) {
						//订单修改成功
						$result['status'] = ERROR_NONE;
						$result['pay'] = 'done';
					}
				}
				//下单成功支付处理中
				if ($result_code == ALIPAY_ORDER_SUCCESS_PAY_INPROCESS || $result_code == ALIPAY_V2_CODE_INPROCESS) {
					//等待用户输入密码
					$result['status'] = ERROR_NONE;
					$result['pay'] = 'wait';
				}
				//其他
				if ($result_code == ALIPAY_UNKNOWN || 
					$result_code == ALIPAY_ORDER_SUCCESS_PAY_FAIL || 
					$result_code == ALIPAY_ORDER_FAIL || 
					$result_code == ALIPAY_V2_CODE_FAIL || 
					$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($detail_error_des);
				}
				//其他接口错误
				if (!empty($error)) {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($error);
				}
			}else { //订单
				$cmd = Yii::app()->db->createCommand();
				$cmd->select('o.id, o.store_id, o.user_code, o.online_paymoney money, o.undiscount_paymoney, o.stored_confirm_status'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no', //未使用
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取所有行数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				$store_id = $model['store_id']; //门店id
				//获取收款信息
				$store = new StoreC();
				$ret = $store->getAlipaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$seller = '';
				$partner = $info['alipay_pid'];
				$key = $info['alipay_key'];
				$appid = $info['alipay_appid'];
				$api_version = $info['alipay_api_version'];
				$alipay_store_id = $info['alipay_store_id'];
				$alipay_seller_id = $info['alipay_seller_id'];
				$auth_token = $info['alipay_auth_token'];
				$audit_status = $info['alipay_audit_status'];
				$category_id = $info['category_id'];
				
				$money = $model['money'];
				$undiscount = $model['undiscount_paymoney'];
				$user_code = $model['user_code'];
				
				$api = new AlipayApi();
				if ($api_version == ALIPAY_API_VERSION_1_API) {
					$response = $api->barCodeApi($order_no, $money, $user_code, $seller, $partner, $key, $alipay_store_id);
				}
				if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
					$response = $api->barCodeApiV2($order_no, $money, $undiscount, $user_code, $appid, $alipay_seller_id, $alipay_store_id, $auth_token);
				}
				if (!$response) {
					$result['status'] = ERROR_EXCEPTION;
					throw new Exception('请求接口失败');
				}
				
				//设置订单的返佣信息
				$order1 = new OrderSC();
				$res = $order1->countOrderCommission($order_no, $api_version, $audit_status, $category_id);
				if ($res['status'] != ERROR_NONE) {
					throw new Exception($res['errMsg']);
				}
				
				//返回请求结果
				$result_code = $api->getVal($response, 'result_code'); //结果码
				$trade_no = $api->getVal($response, 'trade_no'); //流水号
				$detail_error_des = $api->getVal($response, 'detail_error_des'); //错误描述
				$gmt_payment = $api->getVal($response, 'gmt_payment');  //交易付款时间
				$buyer_logon_id = $api->getVal($response, 'buyer_logon_id');//买家支付宝账号
				$buyer_user_id = $api->getVal($response, 'buyer_user_id'); //买家支付宝用户号（pid）
				$error = $api->getVal($response, 'error'); //其他错误
				$fund_bill_list = $api->getVal($response, 'fund_bill_list'); //交易资金明细信息
				
				//资金明细处理
				$discounts = $this->getDiscounts($fund_bill_list);
				$merchant_discount = $discounts['merchant_discount']; //商家优惠
				$alipay_discount = $discounts['alipay_discount']; //支付宝优惠
					
				//下单成功并且支付成功
				if (($result_code == ALIPAY_ORDER_SUCCESS_PAY_SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) && $model['stored_confirm_status'] != ORDER_PAY_WAITFORCONFIRM) {
					//修改订单
					$order = new OrderSC();
					$ret = $order->orderPaySuccess($order_no, $gmt_payment, $trade_no, $buyer_logon_id, $merchant_discount, $alipay_discount, $buyer_user_id);
					if (empty($ret)) {
						$result['status'] = ERROR_EXCEPTION;
						throw new Exception('订单修改失败');
					}
					if ($ret['status'] == ERROR_NONE) {
						//订单修改成功
						$result['status'] = ERROR_NONE;
						$result['pay'] = 'done';
					}
				}
				//支付宝下单成功且支付成功，储值支付待确认
				if (($result_code == ALIPAY_ORDER_SUCCESS_PAY_SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) && $model['stored_confirm_status'] == ORDER_PAY_WAITFORCONFIRM) {
					//等待用户储值支付确认
					$result['status'] = ERROR_NONE;
					$result['pay'] = 'wait';
				}
				//下单成功支付处理中
				if ($result_code == ALIPAY_ORDER_SUCCESS_PAY_INPROCESS || $result_code == ALIPAY_V2_CODE_INPROCESS) {
					//等待用户输入密码
					$result['status'] = ERROR_NONE;
					$result['pay'] = 'wait';
				}
				//其他
				if ($result_code == ALIPAY_UNKNOWN || 
					$result_code == ALIPAY_ORDER_SUCCESS_PAY_FAIL || 
					$result_code == ALIPAY_ORDER_FAIL || 
					$result_code == ALIPAY_V2_CODE_FAIL || 
					$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($detail_error_des);
				}
				//其他接口错误
				if (!empty($error)) {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($error);
				}
			}
			$transaction->commit(); //数据提交
		} catch (Exception $e) {
			$transaction->rollback(); //数据回滚
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 扫码支付
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function qrcodePay($order_no) {
		$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) { //储值订单查询
				$cmd->select('o.stored_id, o.store_id, o.num, s.stored_money'); //查询字段
				$cmd->from(array('wq_stored_order o', 'wq_stored s')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.stored_id = s.id', //联表
						'o.order_no = :order_no',
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取所有行数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				
				$money = $model['stored_money'] * $model['num'];
				
			}else { //订单查询
				$cmd->select('o.id, o.store_id, o.online_paymoney money, o.undiscount_paymoney'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no', //未使用
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取所有行数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				
				$money = $model['money'];
				$undiscount = $model['undiscount_paymoney'];
			}
			$store_id = $model['store_id']; //门店id
			//获取收款信息
			$store = new StoreC();
			$ret = $store->getAlipaySellerInfo($store_id);
			$seller_info = json_decode($ret, true);
			if ($seller_info['status'] != ERROR_NONE) {
				$result['status'] = $seller_info['status'];
				throw new Exception($seller_info['errMsg']);
			}
			$info = $seller_info['data'];
			$seller = '';
			$partner = $info['alipay_pid'];
			$key = $info['alipay_key'];
			$appid = $info['alipay_appid'];
			$api_version = $info['alipay_api_version'];
			$alipay_store_id = $info['alipay_store_id'];
			$alipay_seller_id = $info['alipay_seller_id'];
			$auth_token = $info['alipay_auth_token'];
			$audit_status = $info['alipay_audit_status'];
			$category_id = $info['category_id'];
			
			//请求支付宝接口
			$api = new AlipayApi();
			if ($api_version == ALIPAY_API_VERSION_1_API) {
				$response = $api->qrCodeApi($order_no, $money, $seller, $partner, $key, $alipay_store_id);
			}
			if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
				$response = $api->qrCodeApiV2($order_no, $money, $undiscount, $appid, $alipay_seller_id, $alipay_store_id, $auth_token);
			}
			if (!$response) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('请求接口失败');
			}
			
			//设置订单的返佣信息
			if (!$is_cz) {
				$order1 = new OrderSC();
				$res = $order1->countOrderCommission($order_no, $api_version, $audit_status, $category_id);
				if ($res['status'] != ERROR_NONE) {
					throw new Exception($res['errMsg']);
				}
			}
                        
			//返回请求结果
			$result_code = $api->getVal($response, 'result_code'); //结果码
			$out_trade_no = $api->getVal($response, 'out_trade_no'); //订单号
			$pic_url = $api->getVal($response, 'small_pic_url'); //图片地址
			$detail_error_des = $api->getVal($response, 'detail_error_des'); //详细报错信息
			$qr_code = $api->getVal($response, 'qr_code'); //二维码
			$error = $api->getVal($response, 'error'); //其他错误
			
			//成功
			if ($result_code == _SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) {
				//返回二维码图片地址
				$result['status'] = ERROR_NONE;
				$result['data'] = $pic_url;
				$result['code'] = $qr_code;
			}
			//失败或未知
			if ($result_code == _FAIL || 
				$result_code == _UNKNOWN || 
				$result_code == ALIPAY_V2_CODE_FAIL || 
				$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($detail_error_des);
			}
			//其他接口错误
			if (!empty($error)) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($error);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	//查询支付状态
	public function searchPayStatus($order_no) {
		
	}

    /**
     * 支付宝退款
     * @param $refund_order_no
     * @return string
     */
	public function alipayRefund($refund_order_no) {
        $result = array();
        try {
        	//创建sql语句
        	$cmd = Yii::app()->db->createCommand();
        	$cmd->select('r.refund_money, o.order_no, o.trade_no, o.store_id'); //查询字段
        	$cmd->from(array('wq_order o', 'wq_refund_record r')); //查询表名
        	$cmd->where(array(
        			'AND',  //and操作
        			'r.order_id = o.id',
        			'r.refund_order_no = :refund_order_no',
        	));
        	//查询参数
        	$cmd->params = array(
        			':refund_order_no' => $refund_order_no
        	);
        	//执行sql，获取所有行数据
        	$model = $cmd->queryRow();
        	if (empty($model)) {
        		$result['status'] = ERROR_NO_DATA;
        		throw new Exception('退款记录不存在');
        	}
        	$store_id = $model['store_id']; //门店id
        	//获取收款信息
        	$store = new StoreC();
        	$ret = $store->getAlipaySellerInfo($store_id);
        	$seller_info = json_decode($ret, true);
        	if ($seller_info['status'] != ERROR_NONE) {
        		$result['status'] = $seller_info['status'];
        		throw new Exception($seller_info['errMsg']);
        	}
        	$info = $seller_info['data'];
        	$partner = $info['alipay_pid'];
        	$key = $info['alipay_key'];
        	$appid = $info['alipay_appid'];
        	$api_version = $info['alipay_api_version'];
        	$auth_token = $info['alipay_auth_token'];
        	
        	$money = $model['refund_money'];
        	$order_no = $model['order_no'];
        	$trade_no = $model['trade_no']; //2.0接口需要传支付宝交易号
        	$rf_order_no = $refund_order_no;
        	
        	//支付宝接口请求
        	$api = new AlipayApi();
        	if ($api_version == ALIPAY_API_VERSION_1_API) {
        		$response = $api->refundApi($order_no, $money, $rf_order_no, $partner, $key);
        	}
        	if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
        		$response = $api->refundApiV2($trade_no, $money, $rf_order_no, $appid, $auth_token);
        	}
        	if (!$response) {
        		$result['status'] = ERROR_EXCEPTION;
        		throw new Exception('请求接口失败');
        	}
        	//返回请求结果
        	$result_code = $api->getVal($response, 'result_code');
        	$fund_change = $api->getVal($response, 'fund_change');
        	$detail_error_des = $api->getVal($response, 'detail_error_des');//详细错误描述
        	$gmt_refund_pay = $api->getVal($response, 'gmt_refund_pay');//退款时间
        	$trade_no = $api->getVal($response, 'trade_no'); //支付流水号
        	$out_trade_no = $api->getVal($response, 'out_trade_no'); //支付流水号
        	$error = $api->getVal($response, 'error'); //其他错误
        	
        	//成功
        	if (($result_code == _SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) && $fund_change == 'Y') {
        		$result['status'] = ERROR_NONE;
        		$result['trade_no'] = $trade_no;
        		$result['refund_time'] = $gmt_refund_pay;
        	}
        	//失败或未知
        	if ($result_code == _FAIL || 
        		$result_code == _UNKNOWN || 
        		$result_code == ALIPAY_V2_CODE_FAIL || 
        		$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
        		$result['status'] = ERROR_REQUEST_FAIL;
        		throw new Exception($detail_error_des);
        	}
        	//其他接口错误
        	if (!empty($error)) {
        		$result['status'] = ERROR_REQUEST_FAIL;
        		throw new Exception($error);
        	}
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
	}
	
	/**
	 * 撤销订单
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function alipayRevoke($order_no) {
		$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) {  //储值订单
				$cmd->select('o.order_no, o.store_id'); //查询字段
				$cmd->from(array('wq_stored_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no'
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				
				$order_no = $model['order_no'];
				
			}else {
				$cmd->select('o.order_no, o.store_id'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no'
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				
				$order_no = $model['order_no'];
			}
			$store_id = $model['store_id']; //门店id
			//获取收款信息
			$store = new StoreC();
			$ret = $store->getAlipaySellerInfo($store_id);
			$seller_info = json_decode($ret, true);
			if ($seller_info['status'] != ERROR_NONE) {
				$result['status'] = $seller_info['status'];
				throw new Exception($seller_info['errMsg']);
			}
			$info = $seller_info['data'];
			$partner = $info['alipay_pid'];
			$key = $info['alipay_key'];
			$appid = $info['alipay_appid'];
			$api_version = $info['alipay_api_version'];
			$auth_token = $info['alipay_auth_token'];
			
			//支付宝接口请求
			$api = new AlipayApi();
			if ($api_version == ALIPAY_API_VERSION_1_API) {
				$response = $api->cancelApi($order_no, $partner, $key);
			}
			if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
				$response = $api->cancelApiV2($order_no, $appid, $auth_token);
			}
			if (!$response) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('请求接口失败');
			}
			//返回请求结果
			$result_code = $api->getVal($response, 'result_code');
			$detail_error_des = $api->getVal($response, 'detail_error_des');//详细错误描述
			$trade_no = $api->getVal($response, 'trade_no'); //支付流水号
			$error = $api->getVal($response, 'error'); //其他错误
			
			//成功
			if ($result_code == _SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) {
				$result['status'] = ERROR_NONE;
				$result['trade_no'] = $trade_no;
			}
			//失败或未知
			if ($result_code == _FAIL || 
				$result_code == _UNKNOWN || 
				$result_code == ALIPAY_V2_CODE_FAIL || 
				$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($detail_error_des);
			}
			//其他接口错误
			if (!empty($error)) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($error);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 关闭订单
	 * @throws Exception
	 * @return string
	 */
	public function alipayClose($order_no) {
		$result = array();
		try {
			$cmd = Yii::app()->db->createCommand();
			$cmd->select('o.order_no, o.store_id'); //查询字段
			$cmd->from(array('wq_order o')); //查询表名
			$cmd->where(array(
					'AND',  //and操作
					'o.order_no = :order_no'
			));
			//查询参数
			$cmd->params = array(
					':order_no' => $order_no
			);
			//执行sql，获取数据
			$model = $cmd->queryRow();
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('订单不存在');
			}
			$store_id = $model['store_id']; //门店id
			//获取收款信息
			$store = new StoreC();
			$ret = $store->getAlipaySellerInfo($store_id);
			$seller_info = json_decode($ret, true);
			if ($seller_info['status'] != ERROR_NONE) {
				$result['status'] = $seller_info['status'];
				throw new Exception($seller_info['errMsg']);
			}
			$info = $seller_info['data'];
			$partner = $info['alipay_pid'];
			$key = $info['alipay_key'];
			$appid = $info['alipay_appid'];
			$api_version = $info['alipay_api_version'];
			$auth_token = $info['alipay_auth_token'];
			
			$order_no = $model['order_no'];
		
			$api = new AlipayApi();
			$response = $api->closeApi($order_no, $partner, $key); //2.0接口无关闭订单接口
			if (!$response) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('请求接口失败');
			}
			//返回请求结果
			$result_code = $api->getVal($response, 'result_code');
			$detail_error_des = $api->getVal($response, 'detail_error_des');//详细错误描述
			$trade_no = $api->getVal($response, 'trade_no'); //支付流水号
			$error = $api->getVal($response, 'error'); //其他错误
		
			//成功
			if ($result_code == _SUCCESS) {
				$result['status'] = ERROR_NONE;
				$result['trade_no'] = $trade_no;
			}
			//失败或未知
			if ($result_code == _FAIL || $result_code == _UNKNOWN) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($detail_error_des);
			}
			//其他接口错误
			if (!empty($error)) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($error);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 查询订单
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function alipaySearch($order_no) {
		$result = array();
		try {
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) { //储值订单
				$cmd = Yii::app()->db->createCommand();
				$cmd->select('o.id, o.store_id, o.pay_status, o.order_no'); //查询字段
				$cmd->from(array('wq_stored_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no'
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				$store_id = $model['store_id']; //门店id
				//获取收款信息
				$store = new StoreC();
				$ret = $store->getAlipaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$partner = $info['alipay_pid'];
				$key = $info['alipay_key'];
				$appid = $info['alipay_appid'];
				$api_version = $info['alipay_api_version'];
				$auth_token = $info['alipay_auth_token'];
				
				$order_no = $model['order_no'];
				
				$api = new AlipayApi();
				if ($api_version == ALIPAY_API_VERSION_1_API) {
					$response = $api->searchApi($order_no, $partner, $key);
				}
				if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
					$response = $api->searchApiV2($order_no, $appid, $auth_token);
				}
				if (!$response) {
					$result['status'] = ERROR_EXCEPTION;
					throw new Exception('请求接口失败');
				}
				//返回请求结果
				$result_code = $api->getVal($response, 'result_code');
				$trade_status = $api->getVal($response, 'trade_status'); //交易状态
				$detail_error_code = $api->getVal($response, 'detail_error_code');//详细错误码
				$detail_error_des = $api->getVal($response, 'detail_error_des');//详细错误描述
				$trade_no = $api->getVal($response, 'trade_no'); //支付流水号
				$buyer_user_id = $api->getVal($response, 'buyer_user_id');//买家支付宝用户号
				$send_pay_date = $api->getVal($response, 'send_pay_date');//本次交易打款到卖家账户的时间
				$buyer_logon_id = $api->getVal($response, 'buyer_logon_id');//买家支付宝账号
				$error = $api->getVal($response, 'error'); //其他错误
				$fund_bill_list = $api->getVal($response, 'fund_bill_list'); //交易资金明细信息
				
				//资金明细处理
				$discounts = $this->getDiscounts($fund_bill_list);
				$merchant_discount = $discounts['merchant_discount']; //商家优惠
				$alipay_discount = $discounts['alipay_discount']; //支付宝优惠
				
				//成功
				if ($result_code == _SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) {
					if ($model['pay_status'] == ORDER_STATUS_UNPAID && $trade_status == SEARCH_TRADE_SUCCESS) {
						//修改订单
						$order = new MemberStoredC();
						$ret = $order->orderPaySuccess($order_no, $send_pay_date, $trade_no, $buyer_logon_id, $merchant_discount, $alipay_discount, $buyer_user_id);
						if (empty($ret)) {
							$result['status'] = ERROR_EXCEPTION;
							throw new Exception('订单修改失败');
						}
						if ($ret['status'] != ERROR_NONE) {
							$result['status'] = ERROR_EXCEPTION;
							throw new Exception('订单修改失败');
						}
					}
				
					$result['status'] = ERROR_NONE;
					$result['order_id'] = $model['id'];
					$result['trade_no'] = $trade_no;
					$result['trade_status'] = $trade_status;
					$result['buyer_id'] = $buyer_user_id;
					$result['pay_time'] = $send_pay_date;
					$result['alipay_account'] = $buyer_logon_id;
				}
				//失败或未知
				if ($result_code == _FAIL || 
					$result_code == _UNKNOWN || 
					$result_code == ALIPAY_V2_CODE_FAIL || 
					$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
					if ($detail_error_code == 'TRADE_NOT_EXIST' || $detail_error_code == 'ACQ.TRADE_NOT_EXIST') {
						$result['status'] = ERROR_NONE;
						$result['order_id'] = $model['id'];
					}else {
						$result['status'] = ERROR_REQUEST_FAIL;
						throw new Exception($detail_error_des);
					}
				}
				//其他接口错误
				if (!empty($error)) {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($error);
				}
			}else {
				$cmd = Yii::app()->db->createCommand();
				$cmd->select('o.id, o.store_id, o.pay_status, o.order_no, o.stored_confirm_status'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no'
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				$store_id = $model['store_id']; //门店id
				//获取收款信息
				$store = new StoreC();
				$ret = $store->getAlipaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$partner = $info['alipay_pid'];
				$key = $info['alipay_key'];
				$appid = $info['alipay_appid'];
				$api_version = $info['alipay_api_version'];
				$auth_token = $info['alipay_auth_token'];
				
				$order_no = $model['order_no'];
				
				$api = new AlipayApi();
				if ($api_version == ALIPAY_API_VERSION_1_API) {
					$response = $api->searchApi($order_no, $partner, $key);
				}
				if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
					$response = $api->searchApiV2($order_no, $appid, $auth_token);
				}
				if (!$response) {
					$result['status'] = ERROR_EXCEPTION;
					throw new Exception('请求接口失败');
				}
				//返回请求结果
				$result_code = $api->getVal($response, 'result_code');
				$trade_status = $api->getVal($response, 'trade_status'); //交易状态
				$detail_error_code = $api->getVal($response, 'detail_error_code');//详细错误码
				$detail_error_des = $api->getVal($response, 'detail_error_des');//详细错误描述
				$trade_no = $api->getVal($response, 'trade_no'); //支付流水号
				$buyer_user_id = $api->getVal($response, 'buyer_user_id');//买家支付宝用户号
				$send_pay_date = $api->getVal($response, 'send_pay_date');//本次交易打款到卖家账户的时间
				$buyer_logon_id = $api->getVal($response, 'buyer_logon_id');//买家支付宝账号
				$error = $api->getVal($response, 'error'); //其他错误
				$fund_bill_list = $api->getVal($response, 'fund_bill_list'); //交易资金明细信息
				
				//资金明细处理
				$discounts = $this->getDiscounts($fund_bill_list);
				$merchant_discount = $discounts['merchant_discount']; //商家优惠
				$alipay_discount = $discounts['alipay_discount']; //支付宝优惠
				
				//成功
				if ($result_code == _SUCCESS || $result_code == ALIPAY_V2_CODE_SUCCESS) {
					if ($model['pay_status'] == ORDER_STATUS_UNPAID &&
					$model['stored_confirm_status'] != ORDER_PAY_WAITFORCONFIRM &&
					$trade_status == SEARCH_TRADE_SUCCESS) {
						//修改订单
						$order = new OrderSC();
						$ret = $order->orderPaySuccess($order_no, $send_pay_date, $trade_no, $buyer_logon_id, $merchant_discount, $alipay_discount, $buyer_user_id);
						if (empty($ret)) {
							$result['status'] = ERROR_EXCEPTION;
							throw new Exception('订单修改失败');
						}
						if ($ret['status'] != ERROR_NONE) {
							$result['status'] = ERROR_EXCEPTION;
							throw new Exception('订单修改失败');
						}
					}
				
					$result['status'] = ERROR_NONE;
					$result['order_id'] = $model['id'];
					$result['trade_no'] = $trade_no;
					$result['trade_status'] = $trade_status;
					$result['buyer_id'] = $buyer_user_id;
					$result['pay_time'] = $send_pay_date;
					$result['alipay_account'] = $buyer_logon_id;
				}
				//失败或未知
				if ($result_code == _FAIL || 
					$result_code == _UNKNOWN || 
					$result_code == ALIPAY_V2_CODE_FAIL || 
					$result_code == ALIPAY_V2_CODE_UNKNOWN ) {
					if ($detail_error_code == 'TRADE_NOT_EXIST' || $detail_error_code == 'ACQ.TRADE_NOT_EXIST') {
						$result['status'] = ERROR_NONE;
						$result['order_id'] = $model['id'];
					}else {
						$result['status'] = ERROR_REQUEST_FAIL;
						throw new Exception($detail_error_des);
					}
				}
				//其他接口错误
				if (!empty($error)) {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($error);
				}
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 支付宝异步通知验证
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function alipayVerifyNotify($order_no) {
		$result = array();
		try {
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) { //储值订单
				$cmd = Yii::app()->db->createCommand();
				$cmd->select('o.id, o.store_id, o.pay_status, o.order_no'); //查询字段
				$cmd->from(array('wq_stored_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no'
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
				
			}else {
				//订单查询
				$cmd = Yii::app()->db->createCommand();
				$cmd->select('o.id, o.store_id, o.pay_status, o.order_no, o.stored_confirm_status'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
						'o.order_no = :order_no'
				));
				//查询参数
				$cmd->params = array(
						':order_no' => $order_no
				);
				//执行sql，获取数据
				$model = $cmd->queryRow();
				if (empty($model)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单不存在');
				}
			}
			$store_id = $model['store_id']; //门店id
			//获取收款信息
			$store = new StoreC();
			$ret = $store->getAlipaySellerInfo($store_id);
			$seller_info = json_decode($ret, true);
			if ($seller_info['status'] != ERROR_NONE) {
				$result['status'] = $seller_info['status'];
				throw new Exception($seller_info['errMsg']);
			}
			$info = $seller_info['data'];
			$partner = $info['alipay_pid'];
			$key = $info['alipay_key'];
			$appid = $info['alipay_appid'];
			$api_version = $info['alipay_api_version'];
			$auth_token = $info['alipay_auth_token'];
			
			$api = new AlipayApi();
			if ($api_version == ALIPAY_API_VERSION_1_API) {
				$response = $api->verifyNotify($partner, $key);
			}
			if ($api_version == ALIPAY_API_VERSION_2_API || $api_version == ALIPAY_API_VERSION_2_AUTH_API) {
				$response = $api->verifyNotifyV2($_POST['seller_id'], $_POST['notify_id']);
			}
			if (!$response) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception('请求验证不通过');
			}
			
			$result['status'] = ERROR_NONE;
			$result['pay_status'] = $model['pay_status'];
			$result['errMsg'] = '';
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	
}