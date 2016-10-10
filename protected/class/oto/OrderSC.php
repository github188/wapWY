<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/**
 * 订单类
 *
 */
class OrderSC extends mainClass{
	
	/**
	 * 生成订单号
	 * @return string
	 */
	private function createOrderNumber() {
		do {
			$random = mt_rand(10000000, 99999999); //生成8位随机数密码
			$order_no = date('Ymd', time()).$random;
			$criteria = new CDbCriteria();
			$criteria->addCondition('order_no = :order_no');
			$criteria->params[':order_no'] = $order_no;
			$model = Order::model()->find($criteria);
		}while (!empty($model));
		return $order_no;
	}
	
	/**
	 * 获取订单实收金额（及退款明细）
	 * @param unknown $order_no 订单编号
	 * @param string $detailArray 是否返回明细数组
	 * @return multitype:number unknown Ambigous <number, unknown> Ambigous <number, unknown, static, NULL> Ambigous <number, static, unknown, NULL> |Ambigous <number, static, unknown, NULL>
	 */
	public function getReceiptAmount($order_no, $detailArray = FALSE) {
		$receipt_money = 0; //实收金额
		$refund_money = 0; //已退金额
		$refund_count = 0; //退款笔数
		
		$other_receipt_money = 0; //非储值支付方式的实收金额
		$other_refund_money = 0; //非储值支付方式的退款金额
		$other_refund_count = 0; //非储值支付方式的退款笔数
		
		$stored_receipt_money = 0; //储值实收金额
		$stored_refund_money = 0; //储值退款金额
		$stored_refund_count = 0; //储值退款笔数
		
		$model = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
		if (!empty($model)) {
			$pay_channel = $model['pay_channel']; //支付方式
			//计算实收金额
			$order_money = $model['order_paymoney']; //订单总金额
			$stored_money = $model['stored_paymoney']; //储值支付金额
			$coupons_discount = $model['coupons_money']; //优惠券优惠金额
			$member_discount = $model['discount_money']; //会员优惠
			$merchant_discount = $model['merchant_discount_money']; //商家优惠
			$alipay_discount = $model['alipay_discount_money']; //支付宝优惠
			//订单实收金额（包含所有支付方式的实收金额）
			$receipt_money = $order_money - $coupons_discount - $member_discount - $merchant_discount;
			
			//计算订单的储值支付的金额和非储值支付的金额
			$stored_receipt_money = $stored_money;
			$other_receipt_money = $receipt_money - $stored_money;
			
			//查询退款记录
			$criteria = new CDbCriteria();
			$criteria->order = 'create_time asc';
			$criteria->addCondition('order_id = :order_id');
			$criteria->params[':order_id'] = $model['id'];
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->addCondition('type = :type');
			$criteria->params[':type'] = REFUND_TYPE_REFUND;
			$criteria->addCondition('status = :status1 or status = :status2');
			$criteria->params[':status1'] = REFUND_STATUS_SUCCESS;
			$criteria->params[':status2'] = REFUND_STATUS_PROCESSING;
			$refund_record = RefundRecord::model()->findAll($criteria);
			$record = array();
			foreach ($refund_record as $k => $v) {
				if (empty($v)) {
					continue;
				}
				$record_refund_money = $v['refund_money'];
				$receipt_money -= $record_refund_money; //订单实收金额统计
				$refund_money += $record_refund_money; //订单已退金额统计
				$refund_count ++; //订单退款笔数统计
				
				if (!$detailArray) { //是否需要统计退款明细
					continue;
				}
				//退款明细统计（储值支付与非储值支付两部分）
				if (round($stored_receipt_money, 2) > 0) { //有可退的储值金额
					$stored_receipt_money -= $record_refund_money; //储值支付实收统计
					$stored_refund_money += $record_refund_money; //储值支付已退统计
					$stored_refund_count ++; //储值支付退款笔数统计
					if (round($stored_receipt_money, 2) < 0) { //可退的储值金额不足，需要退非储值支付金额
						$other_receipt_money -= -($stored_receipt_money); //非储值支付实收统计
						$other_refund_money += -($stored_receipt_money); //非储值支付已退统计
						$other_refund_count ++; //非储值支付退款笔数统计
						
						$stored_receipt_money = 0; //储值支付实收归零
						$stored_refund_money = $stored_money; //储值支付已全退
					}
				}else {
					$other_receipt_money -= $record_refund_money; //非储值支付实收统计
					$other_refund_money += $record_refund_money; //非储值支付已退统计
					$other_refund_count ++; //非储值支付退款笔数统计
				}
			}
			
// 			if ($receipt_money < 0) { //实收金额为负则为零
// 				$receipt_money = 0;
// 			}
			if ($model['pay_status'] != ORDER_STATUS_PAID) { //订单不是已支付则为零
				$receipt_money = 0;
			}
		}
		
		if ($detailArray) {
			return array(
					'receipt_money' => $receipt_money, 
					'refund_money' => $refund_money, 
					'refund_count' => $refund_count,
					'stored_receipt_money' => $stored_receipt_money,
					'stored_refund_money' => $stored_refund_money,
					'stored_refund_count' => $stored_refund_count,
					'other_receipt_money' => $other_receipt_money,
					'other_refund_money' => $other_refund_money,
					'other_refund_count' => $other_refund_count,
			);
		}else {
			return $receipt_money;
		}
	}

    /**
     * 生成退款订单号
     * @return string
     */
    private function createRefundOrderNumber() {
        do {
            $random = mt_rand(10000000, 99999999); //生成8位随机数密码
            $order_no = date('Ymd', time()).$random;
            $criteria = new CDbCriteria();
            $criteria->addCondition('refund_order_no = :refund_order_no');
            $criteria->params[':refund_order_no'] = $order_no;
            $model = RefundRecord::model()->find($criteria);
        }while (!empty($model));
        return $order_no;
    }
	
	/**
	 * 创建订单
	 * @param unknown $store_id 	门店id
	 * @param unknown $operator_id	操作员id
	 * @param unknown $user_id		用户id
	 * @param unknown $channel		支付渠道
	 * @param unknown $stored_pay	储值支付金额
	 * @param unknown $secret_money 已免密金额
	 * @param unknown $need_pay		需付金额
	 * @param unknown $use_stored   是否使用储值
	 * @param unknown $use_coupons	是否使用优惠券
	 * @param unknown $collect_points是否累计积分
	 * @param unknown $money		订单总金额
	 * @param unknown $undiscount   不打折金额
	 * @param unknown $red			红包使用金额
	 * @param unknown $cou		    优惠券使用金额（代金券和折扣券）
	 * @param unknown $dis		    会员折扣金额
	 * @param unknown $terminal_type 终端类型
	 * @param unknown $terminal_id	终端号
	 * @return $order_no			订单号 
	 */
	public function createOrder($store_id, $operator_id, $user_id, $channel, $stored_pay, $secret_money, $need_pay, $use_stored, $use_coupons, $collect_points, $money, $undiscount, $red, $cou, $dis, $terminal_type=TERMINAL_TYPE_WEB, $terminal_id=NULL) {

		//参数检查
		if (empty($store_id)) {
			throw new Exception('门店id不能为空');
		}
		if (empty($operator_id) && $operator_id != 'user') {
			throw new Exception('操作员id不能为空');
		}
		if (empty($channel)) {
			throw new Exception('支付渠道不能为空');
		}
		if ((empty($money) || $money == 0) && empty($user_id)) {
			throw new Exception('无效的订单金额');
		}
		
		//查询操作员信息
		if ($operator_id != 'user') {
			$operator = Operator::model()->findByPk($operator_id);
			if (empty($operator)) {
				throw new Exception('操作员不存在');
			}elseif ($operator['status'] == OPERATOR_STATUS_LOCK) {
				throw new Exception('操作员账号被锁定');
			}
		}
		
		//创建订单
		$model = new Order();
		$model['create_time'] = date('Y-m-d H:i:s');
		$model['store_id'] = $store_id;
		$model['operator_id'] = $operator_id != 'user' ? $operator_id : '0';
		if (!empty($store_id)) {
			$store = Store::model()->findByPk($store_id);
			if (!empty($store)) {
				$model['merchant_id'] = $store['merchant_id'];
			}
		}
		$model['user_id'] = $user_id;
		$model['order_type'] = ORDER_TYPE_CASHIER; //收银台订单
		$model['stored_paymoney'] = $stored_pay;
		if (empty($money) || $money == 0) {
			$channel = ORDER_PAY_CHANNEL_NO_MONEY;
		}
		$model['pay_channel'] = $channel;
		switch ($channel) {
			case ORDER_PAY_CHANNEL_ALIPAY_SM: {
				$model['online_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_ALIPAY_TM: {
				$model['online_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_WXPAY_SM: {
				$model['online_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_WXPAY_TM: {
				$model['online_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_CASH: {
				$model['cash_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_UNIONPAY: {
				$model['unionpay_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_GROUP: {
				$model['cash_paymoney'] = $need_pay;
				break;
			}
			case ORDER_PAY_CHANNEL_NO_MONEY: {
				break;
			}
			case ORDER_PAY_CHANNEL_STORED: {
				break;
			}
			default: {
				throw new Exception('无效的支付渠道');
				break;
			}
		}
		if ($need_pay > 0) {
			$model['pay_status'] = ORDER_STATUS_UNPAID;
		}else {
			$model['pay_status'] = ORDER_STATUS_PAID;
			$model['pay_time'] = date('Y-m-d H:i:s');
		}
		$model['order_status'] = ORDER_STATUS_NORMAL;
		if ($use_stored && $stored_pay > 0) {
			$user = User::model()->findByPk($user_id);
			if (empty($user)) {
				throw new Exception('未找到相关用户信息');
			}
			//储值余额不足
			if (bcsub($stored_pay, $user['money'], 2) >= 0) {
				throw new Exception('储值余额不足');
			}
			//是否只有储值支付
			if ($need_pay == 0) {
				$model['pay_channel'] = ORDER_PAY_CHANNEL_STORED; //修改支付渠道
			}
			//储值确认
			if (bcsub($user['free_secret'] - $secret_money, $stored_pay, 2) >= 0) {
				$model['stored_confirm_status'] = ORDER_PAY_NUCONFIRM;
			}else {
				$model['pay_status'] = ORDER_STATUS_UNPAID;
				$model['stored_confirm_status'] = ORDER_PAY_WAITFORCONFIRM;
			}
		}
		if ($collect_points) {
			$collect_points = IF_HAS_POINTS_YES;
		}else {
			$collect_points = IF_HAS_POINTS_NO;
		}
		$model['if_use_coupons'] = $use_coupons;
		$model['if_has_points'] = $collect_points;
		$model['order_paymoney'] = $money;
		$model['undiscount_paymoney'] = $undiscount;
		$model['hongbao_money'] = $red;
		$model['coupons_money'] = $cou;
		$model['discount_money'] = $dis;
		$model['terminal_type'] = $terminal_type;
		$model['terminal_id'] = $terminal_id;
		$model['order_no'] = $this->createOrderNumber();
		
		if ($model->save()) {
			$data = array(
					'id' => $model['id'], 
					'order_no' => $model['order_no'], 
					'stored_confirm_status' => $model['stored_confirm_status']
			);
			return $data;
		}else {
			throw new Exception('创建订单失败');
		}
	}
	
	/**
	 * 计算订单返佣比率
	 * @param unknown $order_no
	 * @param unknown $api_version
	 * @param unknown $audit_status
	 * @param unknown $category_id
	 * @throws Exception
	 * @return string
	 */
	public function countOrderCommission($order_no, $api_version, $audit_status, $category_id) {
		$result = array();
		try {
			if (empty($order_no)) {
				throw new Exception('参数不能为空');
			}
			
			$model = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
			if (empty($model)) {
				throw new Exception('数据不存在');
			}
			
			$pay_channel = ORDER_PAY_PASSAGEWAY_NULL; //默认非支付宝渠道
			$ratio = ORDER_COMMISSION_RATIO_NULL; //默认无返佣
			
			//接口版本
			if ($api_version == ALIPAY_API_VERSION_1_API) {
				$pay_channel = ORDER_PAY_PASSAGEWAY_ALIPAY1;
				//截止到2016-3-1之前，有返佣
				if (time() < strtotime("2016-03-01 00:00:00")) {
					//千分之六的返佣
					$ratio = ORDER_COMMISSION_RATIO_ALIPAY1;
				}
			}
			if ($api_version == ALIPAY_API_VERSION_2_API) {
				$pay_channel = ORDER_PAY_PASSAGEWAY_ALIPAY2;
			}
			if ($api_version == ALIPAY_API_VERSION_2_AUTH_API) {
				$pay_channel = ORDER_PAY_PASSAGEWAY_ALIPAY2;
				//该门店已同步到口碑，且门店类目符合返佣条件
				if ($audit_status == STORE_ALIPAY_SYNC_STATUS_PASS && in_array($category_id, $GLOBALS['__ALIPAY_KOUBEI_STORE_HAS_OWN_COMMISSION'])) {
					//千分之三的返佣
					$ratio = ORDER_COMMISSION_RATIO_ALIPAY2;
				}
			}
			
			//更新记录
			$model['pay_passageway'] = $pay_channel;
			$model['commission_ratio'] = $ratio;
			if (!$model->save()) {
				throw new Exception('订单修改失败');
			}
			 
			$result['status'] = ERROR_NONE;
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		 
		return $result;
	}
	
	/**
	 * 支付成功处理
	 * @param unknown $order_no
	 * @param unknown $pay_time
	 * @param unknown $trade_no
	 * @param unknown $alipay_account
	 * @param unknown $merchant_discount
	 * @param unknown $alipay_discount
	 * @param unknown $alipay_user_id
	 * @param unknown $wxpay_openid
	 * @param unkonwn $wxpay_p_openid
	 * @param unknown $ums_card_no
	 * @throws Exception
	 * @return multitype:string
	 */
	public function orderPaySuccess($order_no, $pay_time, $trade_no, $alipay_account, $merchant_discount=NULL, $alipay_discount=NULL, $alipay_user_id=NULL, $wxpay_openid=NULL, $wxpay_p_openid=NULL, $ums_card_no=NULL) {
		//参数检查
		if (empty($order_no)) {
			throw new Exception('订单编号不能为空');
		}
		
		//订单查找
		$criteria = new CDbCriteria();
		$criteria->addCondition('order_no = :order_no');
		$criteria->params[':order_no'] = $order_no;
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;
		$model = Order::model()->find($criteria);
		
		if (empty($model)) {
			throw new Exception('未找到相关订单信息');
		}
		
		//会员相关处理
		if (!empty($model['user_id']) && $model['if_has_points'] == IF_HAS_POINTS_YES) {
			$user = new UserSC();
			
			//实付金额
			$total_pay = $model['stored_paymoney'] + $model['online_paymoney'] + $model['unionpay_paymoney'] + $model['cash_paymoney'] + 0;
			//消费来源
			$from = USER_POINTS_DETAIL_FROM_TRADE;
			//更新用户的积分
			$result = $user->addUserPoints($model['user_id'], $total_pay, $from, $model['id']);
			if ($result['status'] != ERROR_NONE) {
				$msg = isset($result['errMsg']) ? $result['errMsg'] : '系统内部错误';
				throw new Exception($msg);
			}
		}
		
		//微信卡券核销
		$coupons = new CouponsSC();
		$ret = $coupons->getWxCoupons($model['id']);
		$result = json_decode($ret, true);
		if ($result['status'] != ERROR_NONE) {
			$msg = isset($result['errMsg']) ? $result['errMsg'] : '系统内部错误';
			throw new Exception($msg);
		}
		$list = $result['list'];
		$cardCoupons = new CardCouponsC();
		foreach ($list as $k => $v) {
			if (empty($v)) {
				continue;
			}
			//调用微信卡券核销接口
			$ret = $cardCoupons->consumeCoupons($v['code']);
			$result = json_decode($ret, true);
			if ($result['status'] != ERROR_NONE) {
				$msg = isset($result['errMsg']) ? $result['errMsg'] : '系统内部错误';
				throw new Exception($msg);
			}
		}
		
		//订单修改
		$model['trade_no'] = $trade_no;
		$model['pay_status'] = ORDER_STATUS_PAID;
		$model['pay_time'] = $pay_time;
		if (!empty($alipay_account)) {
			$model['alipay_account'] = $alipay_account;
		}
		if (!empty($alipay_user_id)) {
			$model['alipay_user_id'] = $alipay_user_id;
		}
		if (!empty($wxpay_openid)) {
			$model['wechat_user_id'] = $wxpay_openid;
		}
		if (!empty($wxpay_p_openid)) {
			$model['wechat_user_p_id'] = $wxpay_p_openid;
		}
		if (!empty($ums_card_no)) {
			$model['ums_card_no'] = $ums_card_no;
		}
		if (!empty($merchant_discount)) {
			$model['merchant_discount_money'] = $merchant_discount;
		}
		if (!empty($alipay_discount)) {
			$model['alipay_discount_money'] = $alipay_discount;
		}
		
		//订单保存
		if ($model->save()) {
			//微信支付通知
			if ($model['operator_id'] == '0') {
				$money = $model['order_paymoney'];
				$store_id = $model['store_id'];
				$store = Store::model()->findByPk($store_id);
				if (!empty($store)) {
					$store_name = $store['name'];
					if (!empty($store['branch_name'])) {
						$store_name .= '-'.$store['branch_name'];
					}
					$param = array(
							'first' => '收款成功',
							'keyword1' => $money.'元',
							'keyword2' => '微信支付',
							'keyword3' => $store_name,
							'keyword4' => $order_no,
							'keyword5' => date('Y-m-d H:i:s', strtotime($pay_time)),
							'remark' => '如有疑问，请咨询客服'
					);
					
// 					$merchant = Merchant::model()->findByPk($store['merchant_id']);
// 					$appid = $merchant['wechat_subscription_appid'];
// 					$appsecret = $merchant['wechat_subscription_appsecret'];
					
					//实例化时不设置为商户的appid和appsecret，使用系统公众号发送，后期可能需要改动
					$notice = new WechatNotice();
					$ret = $notice->send(WechatNotice::NOTICE_TYPE_PAY, $param, $store_id);
					//TODO
				}
			}
			
			$data = array('status' => ERROR_NONE);
			return $data;
		}else {
			throw new Exception('修改订单失败');
		}
	}

    public function cancelOrder() {

    }

    /**
     * 订单退款
     * @param $order_no
     * @param $refund_money
     * @param $operator_id
     * @param $pwd
     * @param $terminal_type
     * @param $terminal_id
     * @return array
     * @throws Exception
     */
    public function refundOrder($order_no, $refund_money, $operator_id, $pwd, $terminal_type=TERMINAL_TYPE_WEB, $terminal_id=NULL) {
        //参数检查
        if (empty($order_no)) {
        	throw new Exception('参数order_no不能为空');
        }
        if (empty($refund_money)) {
        	throw new Exception('参数refund_money不能为空');
        }
        if (empty($operator_id)) {
        	throw new Exception('参数operator_id不能为空');
        }
        
        //退款金额验证
        if (!preg_match('/^\d{1,10}\.{0,1}(\d{0,2})?$/', $refund_money)) {
        	throw new Exception('无效的退款金额');
        }
        $refund_money += 0;
        if ($refund_money <= 0) {
        	throw new Exception('退款金额必须大于零');
        }

        //订单查找
        $order = Order::model()->find('order_no = :order_no and flag = :flag', array(
            ':order_no' => $order_no,
            ':flag' => FLAG_NO
        ));

        if (empty($order)) {
            throw new Exception('未找到相关订单信息');
        }

        //操作员查找
        $operator = Operator::model()->find('id = :id and flag = :flag', array(
            ':id' => $operator_id,
            ':flag' => FLAG_NO
        ));
        if (empty($operator)) {
            throw new Exception('无效的操作员id');
        }
        
        $operator_refund_time = 0;
        $admin_refund_time = 0;
        //查询门店
        $store = Store::model()->findByPk($operator['store_id']);
        if (empty($store)) {
        	throw new Exception('未找到相关门店信息');
        }
        //退款操作员相关检查
        if ($operator['role'] == OPERATOR_ROLE_ADMIN) {
        	//当退款操作员身份为店长时，要求店长只能对自己所属的门店操作
        	if ($operator['store_id'] != $order['store_id']) {
        		throw new Exception('无法对其他门店的订单进行操作');
        	}
        }else {
        	//当退款操作员身份不是店长时，要求退款操作员只能对自己创建的订单操作
        	if ($order['operator_id'] != $operator_id) {
        		throw new Exception('无法对其他操作员的订单进行操作');
        	}
        }
        //查询商户信息
        $merchant = Merchant::model()->findByPk($store['merchant_id']);
        if (empty($merchant)) {
        	throw new Exception('未找到相关商户信息');
        }
        $operator_refund_time = $merchant['operator_refund_time'];
        $admin_refund_time = $merchant['dzoperator_refund_time'];

        $admin_id = ''; //店长id
        //订单创建时间
        $time = strtotime($order['create_time']);
        //是否超过店员可退时限
        if ($time < strtotime("-{$operator_refund_time} second")) {
            //是否超过店长可退时限
            if ($time < strtotime("-{$admin_refund_time} second")) {
                throw new Exception('订单超过可退时间，无法退款');
            }
            //检查退款密码
            //根据退款密码查找店长信息
            if (empty($pwd)) {
                throw new Exception('请输入管理员密码');
            }
            //是否md5加密
            if (preg_match("/^[a-z0-9]{32}$/", $pwd)) {
            	$admin = Operator::model()->find('store_id = :store_id and md5(admin_pwd) = :pwd and flag = :flag and role = :role', array(
            			':store_id' => $order['store_id'],
            			':pwd' => $pwd,
            			':flag' => FLAG_NO,
            			':role' => OPERATOR_ROLE_ADMIN
            	));
            }else {
            	$admin = Operator::model()->find('store_id = :store_id and admin_pwd = :pwd and flag = :flag and role = :role', array(
            			':store_id' => $order['store_id'],
            			':pwd' => $pwd,
            			':flag' => FLAG_NO,
            			':role' => OPERATOR_ROLE_ADMIN
            	));
            }
            
            if (empty($admin)) {
                throw new Exception('无效的管理员密码');
            }
            $admin_id = $admin['id'];
        }
        //订单支付状态检查
        if ($order['pay_status'] != ORDER_STATUS_PAID) {
            throw new Exception('无效的订单');
        }
        //订单状态检查
        if ($order['order_status'] != ORDER_STATUS_NORMAL && $order['order_status'] != ORDER_STATUS_PART_REFUND && $order['order_status'] != ORDER_STATUS_HANDLE_REFUND) {
            throw new Exception('无效的订单');
        }
        //部分退款或退款处理中情况下，计算退款记录的退款金额
        $back_money = 0; //已退金额
        if ($order['order_status'] == ORDER_STATUS_PART_REFUND || $order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
            //查询退款记录
            $record = RefundRecord::model()->findAll('order_id = :order_id and flag = :flag and status != :status', array(
                ':order_id' => $order['id'],
                ':flag' => FLAG_NO,
            	':status' => REFUND_STATUS_FAIL
            ));
            foreach ($record as $val) {
                $back_money += $val['refund_money'];
            }
        }
        //计算订单实付金额
        //$need_money = $order['stored_paymoney'] + $order['online_paymoney'] + $order['unionpay_paymoney'] + $order['cash_paymoney'];
        $need_money = $this->getReceiptAmount($order_no);
        //退款金额是否大于可退金额
        if (bcsub($refund_money, $need_money, 2) > 0) {
            throw new Exception('退款金额超过订单可退金额');
        }
        
        //先退储值金额:当使用了储值支付，且确认状态为已确认或无需确认
        $back_stored = 0;
        $confirm_status = $order['stored_confirm_status']; //储值支付的确认状态
        if (!empty($order['stored_paymoney']) && ($confirm_status == ORDER_PAY_NUCONFIRM || $confirm_status == ORDER_PAY_CONFIRM)) {
        	if (bcsub($order['stored_paymoney'], $back_money, 2) > 0) { //仍有未退的储值金额
        		if (bcsub($refund_money, $order['stored_paymoney'] - $back_money, 2) > 0) {
        			//退款金额大于储值支付金额,退还支付的储值
        			$back_stored = $order['stored_paymoney'] - $back_money;
        		}else {
        			$back_stored = $refund_money;
        		}
        	}
        	
        	//修改用户储值
        	$user = new UserSC();
        	$res = $user->updateUserStored($order['user_id'], $back_stored);
        	if ($res['status'] != ERROR_NONE) {
        		throw new Exception($res['errMsg']);
        	}
        }
        
        $back_money += $back_stored; //把储值退款的金额加到已退金额中
        
        //添加退款记录
        $record = new RefundRecord();
        $record['order_id'] = $order['id'];
        $record['merchant_id'] = $merchant['id'];
        $record['store_id'] = $store['id'];
        $record['operator_id'] = $operator_id;
        $record['operator_admin_id'] = $admin_id;
        $record['refund_money'] = $refund_money;
        $record['type'] = REFUND_TYPE_REFUND;
        $record['refund_channel'] = $order['pay_channel'];
        $record['refund_no'] = '';
        $record['terminal_type'] = $terminal_type;
        $record['terminal_id'] = $terminal_id;
        $record['status'] = REFUND_STATUS_SUCCESS; //退款处理中
        
        $record['refund_time'] = date('Y-m-d H:i:s');
        $record['create_time'] = date('Y-m-d H:i:s');
        $record['refund_order_no'] = $this->createRefundOrderNumber();
        if (!$record->save()) {
        	throw new Exception('退款记录保存失败');
        }
        
        //支付渠道为支付宝条码/扫码时，调用支付宝退款接口
        if (bcsub($refund_money, $back_stored, 2) > 0 && ($record['refund_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM || $record['refund_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM)) {
            $api = new AlipaySC();
            $ret = $api->alipayRefund($record['refund_order_no']);
            $result = json_decode($ret, true);
            if ($result['status'] != ERROR_NONE) {
                throw new Exception($result['errMsg']);
            }
            $record['status'] = REFUND_STATUS_SUCCESS; //退款成功
            $record['refund_no'] = $result['trade_no']; //退款流水号
            $record['refund_time'] = $result['refund_time']; //退款时间
            if (!$record->save()) {
                throw new Exception('退款记录修改失败');
            }
        }
        
        //支付渠道为微信条码/扫码时，调用微信退款接口
        if (bcsub($refund_money, $back_stored, 2) > 0 && ($record['refund_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM || $record['refund_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM)) {
        	$api = new WxpaySC();
        	//申请退款
        	$ret = $api->wxpayRefund($record['refund_order_no'], $operator['account']);
        	$result = json_decode($ret, true);
        	if ($result['status'] != ERROR_NONE) {
        		throw new Exception($result['errMsg']);
        	}
        	
        	//申请成功后调用退款查询接口
        	$ret = $api->wxpayRefundQuery($record['refund_order_no']);
        	$result = json_decode($ret, true);
        	if ($result['status'] != ERROR_NONE) {
        		throw new Exception($result['errMsg']);
        	}
        	$refund_status = $result['refund_status']; //退款状态
        	if ($refund_status == 'success') {
        		$record['status'] = REFUND_STATUS_SUCCESS; //退款成功
        		$record['refund_no'] = $result['refund_no']; //退款流水号
        		$record['refund_time'] = $result['refund_time']; //退款时间
        	}
        	if ($refund_status == 'fail') {
        		$record['status'] = REFUND_STATUS_FAIL;
        	}
        	if ($refund_status == 'wait') {
        		$record['status'] = REFUND_STATUS_PROCESSING;
        	}
        	if ($refund_status == 'error') {
        		throw new Exception('退款失败，请人工处理该笔退款');
        	}
        	if (!$record->save()) {
        		throw new Exception('退款记录修改失败');
        	}
        }
        //积分撤销
        if (!empty($order['user_id'])) {
        	$from = USER_POINTS_DETAIL_FROM_TRADE;
        	$user = new UserSC();
        	$res = $user->reduceUserPoints($order['user_id'], $refund_money, $from, $order['id']);
        	if ($res['status'] != ERROR_NONE) {
        		throw new Exception($res['errMsg']);
        	}
        }
		//查询退款记录中处理中的个数
		$num = RefundRecord::model()->count('order_id = :order_id and flag = :flag and status = :status',
				array(':order_id' => $order['id'], ':flag' => FLAG_NO, ':status' => REFUND_STATUS_PROCESSING));
		if ($num > 0 || $record['status'] == REFUND_STATUS_PROCESSING) { //退款处理中
			$order['order_status'] = ORDER_STATUS_HANDLE_REFUND;
		}elseif ($record['status'] == REFUND_STATUS_SUCCESS){ //退款成功
			//退款金额等于可退金额
			if (bcsub($refund_money, $need_money, 2) == 0) {
				//修改订单状态：已退款
				$order['order_status'] = ORDER_STATUS_REFUND;
			}else {
				//修改订单状态： 部分退款
				$order['order_status'] = ORDER_STATUS_PART_REFUND;
			}
		}

        //保存订单数据
        if (!$order->save()) {
            throw new Exception('修改订单失败');
        }

        return array(
        		'status' => ERROR_NONE, 
        		'refund_order_no' => $record['refund_order_no'], 
        		'refund_status' => $record['status'], 
        		'refund_money' => $record['refund_money'],
        		'refund_time' => $record['refund_time'],
        );
    }
    
    /**
     * 订单撤销
     * @param unknown $id
     * @param unknown $operator_id
     * @param unknown $terminal_type
     * @param unknown $terminal_id
     * @throws Exception
     * @return multitype:string
     */
    public function revokeOrder($id, $operator_id, $terminal_type=TERMINAL_TYPE_WEB, $terminal_id=NULL) {
    	//参数检查
    	//TODO
    	//查询订单信息
    	$order = Order::model()->find('id = :id and flag = :flag', array(
    		':id' => $id,
    		':flag' => FLAG_NO
    	));
    	//订单是否存在
    	if (empty($order)) {
    		throw new Exception('未找到相关订单信息');
    	}
    	
    	//检查订单状态
    	if ($order['order_status'] != ORDER_STATUS_NORMAL) {
    		throw new Exception('该订单无法进行撤销操作');
    	}
    	
    	//检查订单支付状态
    	if ($order['pay_status'] != ORDER_STATUS_UNPAID && $order['pay_channel'] != ORDER_PAY_CHANNEL_UNIONPAY) {
    		throw new Exception('该订单无法进行撤销操作');
    	}
    	
    	$operator = Operator::model()->findByPk($operator_id);
    	if (empty($operator)) {
    		throw new Exception('操作员不存在');
    	}
    	//撤单操作员相关检查
    	if ($operator['role'] == OPERATOR_ROLE_ADMIN) {
    		//当撤单操作员身份为店长时，要求店长只能对自己所属的门店操作
    		if ($operator['store_id'] != $order['store_id']) {
    			throw new Exception('无法对其他门店的订单进行操作');
    		}
    	}else {
    		//当撤单操作员身份不是店长时，要求撤单操作员只能对自己创建的订单操作
    		if ($order['operator_id'] != $operator_id) {
    			throw new Exception('无法对其他操作员的订单进行操作');
    		}
    	}
    	
    	//查询门店信息
    	$store = Store::model()->findByPk($operator['store_id']);
    	if (empty($store)) {
    		throw new Exception('门店不存在');
    	}
    	$store_id = $store['id'];
    	$merchant_id = $store['merchant_id'];
    	
    	//添加撤销记录
    	$record = new RefundRecord();
    	$record['order_id'] = $order['id'];
    	$record['merchant_id'] = $merchant_id;
    	$record['store_id'] = $store_id;
    	$record['operator_id'] = $operator_id;
    	$record['type'] = REFUND_TYPE_REVOKE;
    	$record['refund_channel'] = $order['pay_channel'];
    	$record['refund_no'] = '';
    	$record['terminal_type'] = $terminal_type;
    	$record['terminal_id'] = $terminal_id;
    	$record['status'] = REFUND_STATUS_SUCCESS;
    	
    	$record['refund_time'] = date('Y-m-d H:i:s');
    	$record['create_time'] = date('Y-m-d H:i:s');
    	$record['refund_order_no'] = $this->createRefundOrderNumber();
    	if (!$record->save()) {
    		throw new Exception('撤销记录保存失败');
    	}
    	
    	//储值退换:使用储值支付，且已确认或无需确认
    	$confirm_status = $order['stored_confirm_status']; //储值支付的确认状态
    	if (!empty($order['stored_paymoney']) && ($confirm_status == ORDER_PAY_NUCONFIRM || $confirm_status == ORDER_PAY_CONFIRM)) {
    		//修改用户储值
    		$user = new UserSC();
    		$res = $user->updateUserStored($order['user_id'], $order['stored_paymoney']);
    		if ($res['status'] != ERROR_NONE) {
    			throw new Exception($res['errMsg']);
    		}
    	}
    	
    	//支付渠道为支付宝条码/扫码时，调用支付宝撤销订单接口
    	if ($order['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM || $order['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM) {
    		$api = new AlipaySC();
    		$ret = $api->alipayRevoke($order['order_no']);
    		$result = json_decode($ret, true);
    		if ($result['status'] != ERROR_NONE) {
    			throw new Exception($result['errMsg']);
    		}
    		$record['refund_no'] = $result['trade_no'];
            if (!$record->save()) {
                throw new Exception('撤销记录修改失败');
            }
    	}
    	//支付渠道为微信条码/扫码时，调用微信撤销订单接口
    	if ($order['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM || $order['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM) {
    		$api = new WxpaySC();
    		$ret = $api->wxpayRevoke($order['order_no']);
    		$result = json_decode($ret, true);
    		if ($result['status'] != ERROR_NONE) {
    			throw new Exception($result['errMsg']);
    		}
    		$record['refund_no'] = $result['trade_no'];
    		if (!$record->save()) {
    			throw new Exception('撤销记录修改失败');
    		}
    	}
    	//修改订单状态：撤销
    	$order['order_status'] = ORDER_STATUS_REVOKE;
    	//修改订单取消时间
    	$order['cancel_time'] = date('Y-m-d H:i:s');
    	if (!$order->save()) {
    		throw new Exception('修改订单失败');
    	}
    	return array('status' => ERROR_NONE);
    }
    
    /**
     * 退款预检
     * @param unknown $order_no
     * @param unknown $refund_money
     * @param unknown $operator_id
     * @param unknown $pwd
     * @throws Exception
     * @return string
     */
    public function refundPreCheck($order_no, $refund_money, $operator_id, $pwd) {
    	$result = array();
    	try {
    		//参数检查
    		if (empty($order_no)) {
    			throw new Exception('参数order_no不能为空');
    		}
    		if (empty($refund_money)) {
    			throw new Exception('参数refund_money不能为空');
    		}
    		if (empty($operator_id)) {
    			throw new Exception('参数operator_id不能为空');
    		}
    		
    		//退款金额验证
    		if (!preg_match('/^\d{1,10}\.{0,1}(\d{0,2})?$/', $refund_money)) {
    			throw new Exception('无效的退款金额');
    		}
    		$refund_money += 0;
    		if ($refund_money <= 0) {
    			throw new Exception('退款金额必须大于零');
    		}
    		
    		//订单查找
    		$order = Order::model()->find('order_no = :order_no and flag = :flag', array(
    				':order_no' => $order_no,
    				':flag' => FLAG_NO
    		));
    		
    		if (empty($order)) {
    			throw new Exception('未找到相关订单信息');
    		}
    		
    		//操作员查找
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			throw new Exception('无效的操作员id');
    		}
    		
    		$operator_refund_time = 0;
    		$admin_refund_time = 0;
    		//查询门店
    		$store = Store::model()->findByPk($operator['store_id']);
    		if (empty($store)) {
    			throw new Exception('未找到相关门店信息');
    		}
    		//退款操作员相关检查
    		if ($operator['role'] == OPERATOR_ROLE_ADMIN) {
    			//当退款操作员身份为店长时，要求店长只能对自己所属的门店操作
    			if ($operator['store_id'] != $order['store_id']) {
    				throw new Exception('无法对其他门店的订单进行操作');
    			}
    		}else {
    			//当退款操作员身份不是店长时，要求退款操作员只能对自己创建的订单操作
    			if ($order['operator_id'] != $operator_id) {
    				throw new Exception('无法对其他操作员的订单进行操作');
    			}
    		}
    		//查询商户信息
    		$merchant = Merchant::model()->findByPk($store['merchant_id']);
    		if (empty($merchant)) {
    			throw new Exception('未找到相关商户信息');
    		}
    		$operator_refund_time = $merchant['operator_refund_time'];
    		$admin_refund_time = $merchant['dzoperator_refund_time'];
    		
    		$admin_id = ''; //店长id
    		//订单创建时间
    		$time = strtotime($order['create_time']);
    		//是否超过店员可退时限
    		if ($time < strtotime("-{$operator_refund_time} second")) {
    			//是否超过店长可退时限
    			if ($time < strtotime("-{$admin_refund_time} second")) {
    				throw new Exception('订单超过可退时间，无法退款');
    			}
    			//检查退款密码
    			//根据退款密码查找店长信息
    			if (empty($pwd)) {
    				throw new Exception('请输入管理员密码');
    			}
    			//是否md5加密
    			if (preg_match("/^[a-z0-9]{32}$/", $pwd)) {
    				$admin = Operator::model()->find('store_id = :store_id and md5(admin_pwd) = :pwd and flag = :flag and role = :role', array(
    						':store_id' => $order['store_id'],
    						':pwd' => $pwd,
    						':flag' => FLAG_NO,
    						':role' => OPERATOR_ROLE_ADMIN
    				));
    			}else {
    				$admin = Operator::model()->find('store_id = :store_id and admin_pwd = :pwd and flag = :flag and role = :role', array(
    						':store_id' => $order['store_id'],
    						':pwd' => $pwd,
    						':flag' => FLAG_NO,
    						':role' => OPERATOR_ROLE_ADMIN
    				));
    			}
    		
    			if (empty($admin)) {
    				throw new Exception('无效的管理员密码');
    			}
    			$admin_id = $admin['id'];
    		}
    		//订单支付状态检查
    		if ($order['pay_status'] != ORDER_STATUS_PAID) {
    			throw new Exception('无效的订单');
    		}
    		//订单状态检查
    		if ($order['order_status'] != ORDER_STATUS_NORMAL && $order['order_status'] != ORDER_STATUS_PART_REFUND && $order['order_status'] != ORDER_STATUS_HANDLE_REFUND) {
    			throw new Exception('无效的订单');
    		}
    		//部分退款或退款处理中情况下，计算退款记录的退款金额
    		$back_money = 0; //已退金额
    		if ($order['order_status'] == ORDER_STATUS_PART_REFUND || $order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
    			//查询退款记录
    			$record = RefundRecord::model()->findAll('order_id = :order_id and flag = :flag and status != :status', array(
    					':order_id' => $order['id'],
    					':flag' => FLAG_NO,
    					':status' => REFUND_STATUS_FAIL
    			));
    			foreach ($record as $val) {
    				$back_money += $val['refund_money'];
    			}
    		}
    		//计算订单实付金额
    		//$need_money = $order['stored_paymoney'] + $order['online_paymoney'] + $order['unionpay_paymoney'] + $order['cash_paymoney'];
    		$need_money = $this->getReceiptAmount($order_no);
    		//退款金额是否大于可退金额
    		if (bcsub($refund_money, $need_money, 2) > 0) {
    			throw new Exception('退款金额超过订单可退金额');
    		}
    		
    		$result['status'] = ERROR_NONE;
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }

    /**
     * 订单详情
     * @param $id
     * @param $order_no
     * @return string
     */
    public function getOrderDetail($id, $order_no) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		if (empty($id) && empty($order_no)) {
    			$result['status'] = ERROR_PARAMETER_MISS;
    			throw new Exception('参数缺失');
    		}
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->select('m.wq_m_name mname, m.wx_name, m.operator_refund_time, m.dzoperator_refund_time, s.name sname, s.branch_name bname, s.is_print, o.*,s.print_name'); //查询字段
    		$cmd->from(array('wq_order o','wq_store s', 'wq_merchant m', 'wq_operator op')); //查询表名
    		$where = array(
    				'AND',  //and操作
    				'o.store_id = s.id', //联表
    				's.merchant_id = m.id'
    		);
    		if (!empty($id)) {
    			array_push($where, 'o.id = :id');
    			$cmd->params[':id'] = $id;
    		}else {
    			array_push($where, 'o.order_no = :order_no');
    			$cmd->params[':order_no'] = $order_no;
    		}
    		$cmd->where($where);
    		//执行sql，获取数据
    		$model = $cmd->queryRow();
    		
    		if (empty($model)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('查询的数据不存在');
    		}
    		//数据封装
    		$data = array();
    		$data = $model;
    		
    		//操作员编号
    		$operator_id = $data['operator_id'];
    		$operator = Operator::model()->findByPk($operator_id);
    		if (!empty($operator)) {
    			$data['number'] = $operator['number'];
    		}else {
    			$data['number'] = '';
    		}
    		
    		//商户名的显示为显示非空的商户名
    		$data['mname'] = $data['mname'];
    		
    		//门店名设置
    		$bname = !empty($data['bname']) ? '-'.$data['bname'] : '';
    		$data['sname'] = $data['sname'].$bname;
    		
    		$data['account'] = '无';
    		$data['user_name'] = '';
    		$data['user_sex'] = '';
    		$data['user_avatar'] = '';
    		$data['user_grade'] = '';
    		$data['points'] = '';
    		//查询用户
    		$user = User::model()->findByPk($model['user_id']);
    		if (!empty($user)) {
    			$data['account'] = $user['account'];
    			$data['user_name'] = $user['name'];
    			$data['user_sex'] = $user['sex'];
    			$data['user_avatar'] = $user['avatar'];
    			//查询会员等级
    			$grade = UserGrade::model()->findByPk($user['membershipgrade_id']);
    			if (!empty($grade)) {
    				$data['user_grade'] = $grade['name'];
    			}
    			//查询消费积分
    			$record = UserPointsdetail::model()->find('user_id = :user_id and order_id = :order_id',
    					array(':user_id' => $model['user_id'], ':order_id' => $model['id']));
    			if (!empty($record)) {
    				$data['points'] = $record['points'];
    			}
    		}
    		
    		//订单退款是否需要店长密码
    		$data['pwd'] = 'noneed';
    		$time = strtotime($model['create_time']);
    		$operator_refund_time = $model['operator_refund_time'] + 0;
    		$admin_refund_time = $model['dzoperator_refund_time'] + 0;
    		if ($time < strtotime("-{$operator_refund_time} second") && $time > strtotime("-{$admin_refund_time} second")) {
    			$data['pwd'] = 'need';
    		}
    		
    		//查询退款记录
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('order_id = :order_id');
    		$criteria->params[':order_id'] = $model['id'];
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('type = :type');
    		$criteria->params[':type'] = REFUND_TYPE_REFUND;
    		$criteria->addCondition('status = :status1 or status = :status2');
    		$criteria->params[':status1'] = REFUND_STATUS_SUCCESS;
    		$criteria->params[':status2'] = REFUND_STATUS_PROCESSING;
    		$refund_record = RefundRecord::model()->findAll($criteria);
    		$record = array();
    		foreach ($refund_record as $k => $v) {
    			if (empty($v)) {
    				continue;
    			}
    			$refund_operator = Operator::model()->findByPk($v['operator_id']);
    			$number1 = !empty($refund_operator) ? $refund_operator['number'] : '';
    			$refund_admin = Operator::model()->findByPk($v['operator_admin_id']);
    			$number2 = !empty($refund_admin) ? $refund_admin['number'] : '';
    			 
    			$record[] = array(
    					'refund_operator' => $number1,
    					'refund_admin' => $number2,
    					'refund_money' => $v['refund_money'],
    					'refund_time' => $v['refund_time'],
    			);
    		}
    		$data['refund_record'] = $record; //退款记录
    		
    		//计算实收金额
    		$data['receipt_money'] = $this->getReceiptAmount($data['order_no']);
    	
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
	
    /**
     * 更新订单user_code,trade_no,ums_code
     * @param unknown $order_no
     * @param unknown $user_code
     * @throws Exception
     * @return string
     */
    public function updateCode($order_no, $user_code, $trade_no=NULL, $ums_code=NULL) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('order_no = :order_no');
    		$criteria->params[':order_no'] = $order_no;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$model = Order::model()->find($criteria);
    		if (empty($model)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('修改的数据不存在');
    		}
    		
    		$model['user_code'] = $user_code;
    		$model['trade_no'] = $trade_no;
    		//$model['ums_code'] = $ums_code;
    		if (!$model->save()) {
    			$result['status'] = ERROR_SAVE_FAIL;
    			throw new Exception('数据保存失败');
    		}
    	
    		$result['data'] = '';
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 未支付订单查询（查询第三方订单信息）
     * @param string $order_no
     * @return string
     */
    public function orderSearch($order_no = NULL) {
    	$result = array();
    	try {
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('pay_status = :pay_status');
    		$criteria->params[':pay_status'] = ORDER_STATUS_UNPAID;
    		$criteria->addCondition('order_status = :order_status');
    		$criteria->params[':order_status'] = ORDER_STATUS_NORMAL;
    		$criteria->addCondition('user_code IS NOT NULL');
    		$criteria->addCondition("user_code != ''");
    		//$criteria->params[':user_code'] = '';
    		$criteria->addCondition('pay_channel = :pay_channel');
    		$criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_WXPAY_TM;
    		if (!empty($order_no)) {
    			$criteria->addCondition('order_no = :order_no');
    			$criteria->params[':order_no'] = $order_no;
    		}
    		
    		$list = Order::model()->findAll($criteria);
    		
    		$api = new WxpaySC();
    		foreach ($list as $order) {
    			if (empty($order)) {
    				continue;
    			}
    			$order_id = $order['id']; //订单id
    			$tmp_order_no = $order['order_no']; //订单编号
    			$create_time = $order['create_time']; //创建时间
    			
    			//查询订单接口，如果第三方支付成功将会更新后台订单状态
    			$ret = $api->wxpaySearch($tmp_order_no);
    			$search_result = json_decode($ret, true);
    			if ($search_result['status'] != ERROR_NONE) {
    				if (!$order_no) {
    					continue;
    				}else {
    					$result['status'] = $search_result['status'];
    					throw new Exception($search_result['errMsg']);
    				}
    			}
    			//未返回trade_status说明该微信商户号下没有此订单号
    			if (!isset($search_result['trade_status'])) {
    				continue;
    			}
    			$trade_status = $search_result['trade_status']; //第三方交易状态
    			if ($trade_status == 'NOTPAY' || $trade_status == 'USERPAYING') { //用户未付款成功
    				//如果超时则撤销该订单
    				$interval = time() - strtotime($create_time); //订单产生到现在过去的秒数
    				$limit = 24 * 60 * 60; //超时时间，1天
    				if ($interval <= $limit) {
    					continue;
    				}
    				$transaction = Yii::app()->db->beginTransaction(); //开启事务
    				try {
    					$res = $this->revokeOrder($order_id, NULL);
    					if (!isset($res['status']) || $res['status'] != ERROR_NONE) {
    						throw new Exception('系统内部错误');
    					}
    					$transaction->commit();
    				} catch (Exception $e) {
    					$transaction->rollback();
    					$msg = $e->getMessage();
    				}
    			}
    		}
    		
    		//查询支付宝订单
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('pay_status = :pay_status');
    		$criteria->params[':pay_status'] = ORDER_STATUS_UNPAID;
    		$criteria->addCondition('order_status = :order_status');
    		$criteria->params[':order_status'] = ORDER_STATUS_NORMAL;
    		$criteria->addCondition('user_code IS NOT NULL');
    		$criteria->addCondition("user_code != ''");
    		$criteria->addCondition('pay_channel = :pay_channel');
    		$criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_ALIPAY_TM;
    		if (!empty($order_no)) {
    			$criteria->addCondition('order_no = :order_no');
    			$criteria->params[':order_no'] = $order_no;
    		}
    		
    		$list = Order::model()->findAll($criteria);
    		
    		$api = new AlipaySC();
    		foreach ($list as $order) {
    			if (empty($order)) {
    				continue;
    			}
    			$order_id = $order['id']; //订单id
    			$tmp_order_no = $order['order_no']; //订单编号
    			$create_time = $order['create_time']; //创建时间
    		
    			//查询订单接口，如果第三方支付成功将会更新后台订单状态
    			$ret = $api->alipaySearch($tmp_order_no);
    			$search_result = json_decode($ret, true);
    			if ($search_result['status'] != ERROR_NONE) {
    				if (!$order_no) {
    					continue;
    				}else {
    					$result['status'] = $search_result['status'];
    					throw new Exception($search_result['errMsg']);
    				}
    			}
    			//未返回trade_status说明该微信商户号下没有此订单号
    			if (!isset($search_result['trade_status'])) {
    				continue;
    			}
    			$trade_status = $search_result['trade_status']; //第三方交易状态
    			if ($trade_status == SEARCH_WAIT_BUYER_PAY) { //用户未付款成功
    				//如果超时则撤销该订单
    				$interval = time() - strtotime($create_time); //订单产生到现在过去的秒数
    				$limit = 24 * 60 * 60; //超时时间，1天
    				if ($interval <= $limit) {
    					continue;
    				}
    				$transaction = Yii::app()->db->beginTransaction(); //开启事务
    				try {
    					$res = $this->revokeOrder($order_id, NULL);
    					if (!isset($res['status']) || $res['status'] != ERROR_NONE) {
    						throw new Exception('系统内部错误');
    					}
    					$transaction->commit();
    				} catch (Exception $e) {
    					$transaction->rollback();
    					$msg = $e->getMessage();
    				}
    			}
    		}
    		
    		$result['data'] = '';
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 未支付订单查询（支付宝）
     * @param string $order_no
     * @throws Exception
     * @return string
     */
    public function orderSearchAli($order_no = NULL) {
    	$result = array();
    	try {
    		//查询支付宝订单
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('pay_status = :pay_status');
    		$criteria->params[':pay_status'] = ORDER_STATUS_UNPAID;
    		$criteria->addCondition('order_status = :order_status');
    		$criteria->params[':order_status'] = ORDER_STATUS_NORMAL;
    		$criteria->addCondition('pay_channel = :pay_channel');
    		$criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_ALIPAY_TM;
    		if (!empty($order_no)) {
    			$criteria->addCondition('order_no = :order_no');
    			$criteria->params[':order_no'] = $order_no;
    		}
    		
    		$list = Order::model()->findAll($criteria);
    		
    		$api = new AlipaySC();
    		foreach ($list as $order) {
    			if (empty($order)) {
    				continue;
    			}
    			$order_id = $order['id']; //订单id
    			$tmp_order_no = $order['order_no']; //订单编号
    			$create_time = $order['create_time']; //创建时间
    		
    			//查询订单接口，如果第三方支付成功将会更新后台订单状态
    			$ret = $api->alipaySearch($tmp_order_no);
    			$search_result = json_decode($ret, true);
    			if ($search_result['status'] != ERROR_NONE) {
    				if (!$order_no) {
    					continue;
    				}else {
    					$result['status'] = $search_result['status'];
    					throw new Exception($search_result['errMsg']);
    				}
    			}
    			//未返回trade_status说明该微信商户号下没有此订单号
    			if (!isset($search_result['trade_status'])) {
    				continue;
    			}
    			$trade_status = $search_result['trade_status']; //第三方交易状态
    			if ($trade_status == SEARCH_WAIT_BUYER_PAY) { //用户未付款成功
    				//如果超时则撤销该订单
    				$interval = time() - strtotime($create_time); //订单产生到现在过去的秒数
    				$limit = 24 * 60 * 60; //超时时间，1天
    				if ($interval <= $limit) {
    					continue;
    				}
    				$transaction = Yii::app()->db->beginTransaction(); //开启事务
    				try {
    					$res = $this->revokeOrder($order_id, NULL);
    					if (!isset($res['status']) || $res['status'] != ERROR_NONE) {
    						throw new Exception('系统内部错误');
    					}
    					$transaction->commit();
    				} catch (Exception $e) {
    					$transaction->rollback();
    					$msg = $e->getMessage();
    				}
    			}
    		}
    		
    		$result['data'] = '';
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
	
    /**
     * 退款查询
     * @param string $order_no
     * @throws Exception
     * @return string
     */
    public function refundQuery($order_no = NULL) {
    	$result = array();
    	try {
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('status = :status');
    		$criteria->params[':status'] = REFUND_STATUS_PROCESSING;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		if (!empty($order_no)) {
    			$model = Order::model()->find('order_no = :order_no and flag = :flag',
    					array(':order_no' => $order_no, ':flag' => FLAG_NO));
    			if (empty($model)) {
    				$result['status'] = ERROR_NO_DATA;
    				throw new Exception('订单不存在');
    			}
    			$order_id = $model['id']; //订单id
    			
    			$criteria->addCondition('order_id = :order_id');
    			$criteria->params[':order_id'] = $order_id;
    		}else {
    			$criteria->addCondition('TIMESTAMPDIFF(SECOND, create_time, NOW()) < :max_time');
    			$criteria->params[':max_time'] = 60 * 60 * 24 * 5; //5天以内
//     			$criteria->addCondition('TIMESTAMPDIFF(SECOND, create_time, NOW()) > :min_time');
//     			$criteria->params[':min_time'] = 0; //0秒之后
    		}
    		//查询所有处理中的退款记录
    		$list = RefundRecord::model()->findAll($criteria);
    		
    		foreach ($list as $record) {
    			//订单id
    			$order_id = $record['order_id'];
    			//退款渠道
    			$refund_channel = $record['refund_channel'];
    			//调用退款查询接口
    			$api = new WxpaySC();
    			$ret = $api->wxpayRefundQuery($record['refund_order_no']);
    			$query_result = json_decode($ret, true);
    			if ($query_result['status'] != ERROR_NONE) {
    				if (!$order_no) { //非指定订单号查询则忽略错误
    					continue;
    				}else {
    					$result['status'] = $query_result['status'];
    					throw new Exception($query_result['errMsg']);
    				}
    			}
    			$transaction = Yii::app()->db->beginTransaction(); //开启事务
    			try {
    				//该退款记录是否已处理
    				$is_handle = false;
    				//该退款记录是否退款成功
    				$is_success = false;
    				//查询订单信息
    				$order = Order::model()->findByPk($order_id);
    					
    				//退款状态
    				if ($query_result['refund_status'] == 'success') {
    					$is_handle = true;
    					$is_success = true;
    					$record['status'] = REFUND_STATUS_SUCCESS; //退款成功
    					$record['refund_no'] = $query_result['refund_no']; //退款流水号
    					$record['refund_time'] = $record['create_time']; //退款时间
    				}
    				if ($query_result['refund_status'] == 'fail' || $query_result['refund_status'] == 'error') {
    					$is_handle = true;
    					$record['status'] = REFUND_STATUS_FAIL; //退款失败
    				}
    					
    				//保存退款记录
    				if (!$record->save()) {
    					throw new Exception('退款记录修改失败');
    				}
    					
    				//查询退款记录中处理中的个数，返回值为字符串类型
    				$num = RefundRecord::model()->count('order_id = :order_id and flag = :flag and status = :status',
    						array(':order_id' => $order_id, ':flag' => FLAG_NO, ':status' => REFUND_STATUS_PROCESSING));
    				if ($num === '0') { //该退款记录对应的订单已全部处理完成
    					//查询并计算退款总和
    					//订单更新
    					//$total_pay = $order['stored_paymoney'] + $order['online_paymoney'] + $order['unionpay_paymoney'] + $order['cash_paymoney'];
    					$order_fund = $this->getReceiptAmount($order['order_no'], true);
    					$receipt_money = $order_fund['receipt_money']; //实收金额
    					$refund_money = $order_fund['refund_money']; //已退金额
    					
    					if (round($receipt_money, 2) == 0 && round($refund_money, 2) > 0) { //已退款
    						$order['order_status'] = ORDER_STATUS_REFUND;
    					}
    					if (round($receipt_money, 2) > 0 && round($refund_money, 2) > 0) { //部分退款
    						$order['order_status'] = ORDER_STATUS_PART_REFUND;
    					}
    					if (round($receipt_money, 2) > 0 && round($refund_money, 2) == 0) { //无退款，即正常
    						$order['order_status'] = ORDER_STATUS_NORMAL;
    					}
    					
    					if (!$order->save()) {
    						throw new Exception('订单修改失败');
    					}
    					Yii::log('$$$$$$','warning');
    				}
    					
    				$transaction->commit();
    			} catch (Exception $e) {
    				$transaction->rollback();
    			}
    		}
    		 
    		$result['data'] = '';
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 获取已退金额
     * @param unknown $order_id
     * @return string
     */
    public function getRefundAmount($order_id) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('order_id = :order_id');
    		$criteria->params[':order_id'] = $order_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addInCondition('status', array(REFUND_STATUS_PROCESSING, REFUND_STATUS_SUCCESS));
    		$criteria->addCondition('type = :type');
    		$criteria->params[':type'] = REFUND_TYPE_REFUND;
    		$model = RefundRecord::model()->findAll($criteria);
    		$refund_amount = 0;
    		foreach ($model as $k => $v) {
    			$refund_amount += $v['refund_money'];
    		}
    		 
    		$result['data'] = $refund_amount;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 获取订单列表 （app）
     * @param unknown $operator_id
     * @param unknown $limit_id 限制订单id，结果集订单id小于限制订单id
     * @param unknown $start_date 开始时间
     * @param unknown $end_date 结束时间
     * @param unknown $select_type 筛选的支付类型
     * @param unknown $select_operator 筛选的操作员
     * @param unknown $order_status 筛选的订单状态
     * @param unknown $pay_status 筛选的支付状态
     * @param unknown $keyword 搜索关键词
     */
    public function getOrderList4App($operator_id, $limit_id, $start_date, $end_date, $select_type, $select_operator, $order_status, $pay_status, $keyword) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
//     		$criteria = new CDbCriteria();
//     		if (! empty ( $operator_id )) {
//     			$operatprModel = Operator::model()->findByPk($operator_id);
//     			if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
//     				$criteria->addCondition ( 'operator_id=:operator_id and flag=:flag and store_id=:store_id' );
//     				$criteria->params = array (
//     						':operator_id' => $operator_id,
//     						':flag' => FLAG_NO,
//     						':store_id' => $store_id
//     				);
//     			}else{//如果操作员是店长(可以查看本店所有的订单记录)
//     				$criteria->addCondition ( 'flag=:flag and store_id=:store_id' );
//     				$criteria->params = array (
//     						':flag' => FLAG_NO,
//     						':store_id' => $store_id
//     				);
//     			}
//     		}else{
//     			$criteria->addCondition ( 'flag=:flag' );
//     			$criteria->params = array (
//     					':flag' => FLAG_NO
//     			);
//     		}
//     		$criteria->order = 'create_time  desc';

    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		//操作员角色
    		$role = $operator['role'];
    		//操作员所属门店id
    		$store_id = $operator['store_id'];
    		 
    		//查询订单记录列表
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('store_id = :store_id');
    		$criteria->params[':store_id'] = $store_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		//店员只能查看自己的记录
    		if ($role == OPERATOR_ROLE_NORMAL) {
    			$criteria->addCondition('operator_id = :operator_id');
    			$criteria->params[':operator_id'] = $operator_id;
    		}
    		
    		if (!empty($limit_id) && $limit_id != 'ALL') {
    			$criteria->addCondition('id < :id');
    			$criteria->params[':id'] = $limit_id;
    		}
    		if (!empty($start_date)) {
    			$criteria->addCondition('create_time >= :start_time');
    			$criteria->params[':start_time'] = $start_date;
    		}
    		if (!empty($end_date)) {
    			$criteria->addCondition('create_time <= :end_time');
    			$criteria->params[':end_time'] = $end_date;
    		}
    		if (!empty($select_type)) {
    			$criteria->addCondition('pay_channel = :pay_channel');
    			$criteria->params[':pay_channel'] = $select_type;
    		}
    		if (!empty($order_status)) {
    			$criteria->addCondition('order_status = :order_status');
    			$criteria->params[':order_status'] = $order_status;
    		}
    		if (!empty($pay_status)) {
    			$criteria->addCondition('pay_status = :pay_status');
    			$criteria->params[':pay_status'] = $pay_status;
    		}
    		if (!empty($select_operator)) {
    			$select_model = Operator::model()->find('store_id = :store_id AND number = :number AND flag = :flag',
    					array(':store_id' => $store_id, ':number' => $select_operator, ':flag' => FLAG_NO));
    			if (!empty($select_model)) {
    				$select_operator_id = $select_model['id'];
    				$criteria->addCondition('operator_id = :operator_id');
    				$criteria->params[':operator_id'] = $select_operator_id;
    			}
    		}
    		if (!empty($keyword)) {
    			//查询会员信息
    			$select_users = User::model()->findAll("account like :account", array(':account' => '%'.$keyword.'%'));
    			//$select_user_id = !empty($select_user) ? $select_user['id'] : '';
    			$id_str = '';
    			foreach ($select_users as $select_user) {
    				if (empty($id_str)) {
    					$id_str .= $select_user['id'];
    				}else {
    					$id_str .= ','.$select_user['id'];
    				}
    			}
    			if (!$id_str) {
    				$criteria->addCondition("(order_no like :order_no) OR (ABS(order_paymoney - :order_paymoney) < 1e-5)");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    				$criteria->params[':order_paymoney'] = $keyword + 0.00; //查询float字段采用差值比较
    			}else {
    				$criteria->addCondition("(order_no like :order_no) OR (ABS(order_paymoney - :order_paymoney) < 1e-5) OR (user_id IN ($id_str))");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    				$criteria->params[':order_paymoney'] = $keyword + 0.00; //查询float字段采用差值比较
    				//$criteria->params[':user_id'] = $id_str;
    			}
    		}
    		//是否获取只获取指定页码的数据，ALL：获取所有数据, 数字：指定页的数据
    		if ($limit_id != 'ALL') {
    			$page_size = Yii::app()->params['perPage'];
    			$criteria->limit = $page_size ? : 10;
    		}
    		//$pages = new CPagination(Order::model()->count($criteria));
    		//$pages->pageSize = isset($perPage) && $perPage ? $perPage : Yii::app() -> params['perPage'];
    		//$pages->applyLimit($criteria);
    		$list = Order::model()->findAll($criteria);
    		//$data = $this->returnOrder($list);
    		$data = array();
    		foreach($list as $v) {
    			$operator_id = $v['operator_id'];
    			$user_id = $v['user_id'];
    			
    			$operator_number = '';
    			if (!empty($operator_id)) {
    				$operator = Operator::model()->findByPk($operator_id);
    				if (!empty($operator)) {
    					$operator_number = $operator['number'];
    				}
    			}
    			$user_avatar = '';
    			$user_account = '';
    			$user_name = '';
    			$user_grade = '';
    			$points = '';
    			if (!empty($user_id)) {
    				$user = User::model()->findByPk($user_id);
    				if (!empty($user)) {
    					$user_avatar = $user['avatar'] ? : $user['alipay_avatar'];
    					$user_account = $user['account'];
    					$user_name = $user['name'];
    					$user_grade = $user['membershipgrade_id'];
    					//获取会员等级
    					if (!empty($user_grade)) {
    						$grade = UserGrade::model()->findByPk($user_grade);
    						if (!empty($grade)) {
    							$user_grade = $grade['name'];
    						}
    					}
    					//获取消费积分
    					$record = UserPointsdetail::model()->find('user_id = :user_id and order_id = :order_id',
    							array(':user_id' => $user_id, ':order_id' => $v['id']));
    					if (!empty($record)) {
    						$points = $record['points'];
    					}
    				}
    			}
    			$tmp = $v->getAttributes();
    			$tmp['operator_number'] = $operator_number;
    			$tmp['user_avatar'] = $user_avatar;
    			$tmp['user_account'] = $user_account;
    			$tmp['user_name'] = $user_name;
    			$tmp['user_grade'] = $user_grade;
    			$tmp['points'] = $points;
    			$info = $this->getReceiptAmount($v['order_no'], TRUE);
    			$tmp['receipt_money'] = $info['receipt_money'];
    			$tmp['refund_money'] = $info['refund_money'];
    			
    			$data[] = $tmp;
    		}
    		
    		$result['item_count'] = count($data);
    		$result['page_count'] = ''; 
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 获取订单列表 （pc）
     * @param unknown $operator_id
     * @param unknown $page_num
     * @param unknown $start_date 开始时间
     * @param unknown $end_date 结束时间
     * @param unknown $select_type 筛选的支付类型
     * @param unknown $select_operator 筛选的操作员
     * @param unknown $order_status 筛选的订单状态
     * @param unknown $pay_status 筛选的支付状态
     * @param unknown $keyword 搜索关键词
     */
    public function getOrderList4PC($operator_id, $page_num, $start_date, $end_date, $select_type, $select_operator, $order_status, $pay_status, $keyword) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		//操作员角色
    		$role = $operator['role'];
    		//操作员所属门店id
    		$store_id = $operator['store_id'];
    		 
    		//查询订单记录列表
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('store_id = :store_id');
    		$criteria->params[':store_id'] = $store_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		//店员只能查看自己的记录
    		if ($role == OPERATOR_ROLE_NORMAL) {
    			$criteria->addCondition('operator_id = :operator_id');
    			$criteria->params[':operator_id'] = $operator_id;
    		}
    		
    		if (!empty($start_date)) {
    			$criteria->addCondition('create_time >= :start_time');
    			$criteria->params[':start_time'] = $start_date;
    		}
    		if (!empty($end_date)) {
    			$criteria->addCondition('create_time <= :end_time');
    			$criteria->params[':end_time'] = $end_date;
    		}
    		if (!empty($select_type)) {
    			$criteria->addCondition('pay_channel = :pay_channel');
    			$criteria->params[':pay_channel'] = $select_type;
    		}
    		if (!empty($order_status)) {
    			$criteria->addCondition('order_status = :order_status');
    			$criteria->params[':order_status'] = $order_status;
    		}
    		if (!empty($pay_status)) {
    			$criteria->addCondition('pay_status = :pay_status');
    			$criteria->params[':pay_status'] = $pay_status;
    		}
    		if (!empty($select_operator)) {
    			$select_model = Operator::model()->find('store_id = :store_id AND number = :number AND flag = :flag',
    					array(':store_id' => $store_id, ':number' => $select_operator, ':flag' => FLAG_NO));
    			if (!empty($select_model)) {
    				$select_operator_id = $select_model['id'];
    				$criteria->addCondition('operator_id = :operator_id');
    				$criteria->params[':operator_id'] = $select_operator_id;
    			}
    		}
    		if (!empty($keyword)) {
    			//查询会员信息
    			$select_users = User::model()->findAll("account like :account", array(':account' => '%'.$keyword.'%'));
    			//$select_user_id = !empty($select_user) ? $select_user['id'] : '';
    			$id_str = '';
    			foreach ($select_users as $select_user) {
    				if (empty($id_str)) {
    					$id_str .= $select_user['id'];
    				}else {
    					$id_str .= ','.$select_user['id'];
    				}
    			}
    			if (!$id_str) {
    				$criteria->addCondition("(order_no like :order_no) OR (ABS(order_paymoney - :order_paymoney) < 1e-5)");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    				$criteria->params[':order_paymoney'] = $keyword + 0.00; //查询float字段采用差值比较
    			}else {
    				$criteria->addCondition("(order_no like :order_no) OR (ABS(order_paymoney - :order_paymoney) < 1e-5) OR (user_id IN ($id_str))");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    				$criteria->params[':order_paymoney'] = $keyword + 0.00; //查询float字段采用差值比较
    				//$criteria->params[':user_id'] = $id_str;
    			}
    		}
    		
    		//是否获取只获取指定页码的数据，ALL：获取所有数据, 数字：指定页的数据
    		$total_page = 1;
    		if ($page_num != 'ALL') {
    			//配置的每页显示数量
    			$page_size = Yii::app()->params['perPage'];
    			//计算总页数
    			$total_num = Order::model()->count($criteria);
    			$total_page = ceil($total_num / $page_size);
    			//翻页
    			$criteria->limit = $page_size ? : 10;
    			$page_num += 0;
    			if (!empty($page_num) && $page_num > 0) {
    				$criteria->offset = $page_size * ($page_num -1);
    			}
    		}
    		
    		//$pages = new CPagination(Order::model()->count($criteria));
    		//$pages->pageSize = isset($perPage) && $perPage ? $perPage : Yii::app() -> params['perPage'];
    		//$pages->applyLimit($criteria);
    		$list = Order::model()->findAll($criteria);
    		//$data = $this->returnOrder($list);
    		$data = array();
    		foreach($list as $v) {
    			$operator_id = $v['operator_id'];
    			$user_id = $v['user_id'];
    			 
    			$operator_number = '';
    			if (!empty($operator_id)) {
    				$operator = Operator::model()->findByPk($operator_id);
    				if (!empty($operator)) {
    					$operator_number = $operator['number'];
    				}
    			}
    			$user_avatar = '';
    			$user_account = '';
    			$user_name = '';
    			$user_grade = '';
    			$points = '';
    			if (!empty($user_id)) {
    				$user = User::model()->findByPk($user_id);
    				if (!empty($user)) {
    					$user_avatar = $user['avatar'] ? : $user['alipay_avatar'];
    					$user_account = $user['account'];
    					$user_name = $user['name'];
    					$user_grade = $user['membershipgrade_id'];
    					//获取会员等级
    					if (!empty($user_grade)) {
    						$grade = UserGrade::model()->findByPk($user_grade);
    						if (!empty($grade)) {
    							$user_grade = $grade['name'];
    						}
    					}
    					//获取消费积分
    					$record = UserPointsdetail::model()->find('user_id = :user_id and order_id = :order_id',
    							array(':user_id' => $user_id, ':order_id' => $v['id']));
    					if (!empty($record)) {
    						$points = $record['points'];
    					}
    				}
    			}
    			$tmp = $v->getAttributes();
    			$tmp['operator_number'] = $operator_number;
    			$tmp['user_avatar'] = $user_avatar;
    			$tmp['user_account'] = $user_account;
    			$tmp['user_name'] = $user_name;
    			$tmp['user_grade'] = $user_grade;
    			$tmp['points'] = $points;
    			$info = $this->getReceiptAmount($v['order_no'], TRUE);
    			$tmp['receipt_money'] = $info['receipt_money'];
    			$tmp['refund_money'] = $info['refund_money'];
    			 
    			$data[] = $tmp;
    		}
    
    		$result['item_count'] = count($data);
    		$result['page_count'] = $total_page;
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    
    	return json_encode($result);
    }
    
    /**
     * 获取订单详情 （app）
     * @param unknown $order_no
     * @throws Exception
     * @return string
     */
    public function getOrderDetail4App($order_no) {
    	$result = array();
    	try {
    		//参数验证
    		if (empty($order_no)) {
    			$result['status'] = ERROR_PARAMETER_MISS;
    			throw new Exception('参数order_no不能为空');
    		}
    		
    		$order = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
    		if (empty($order)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('订单不存在');
    		}
    		$data = array();
    		$data = $order->getAttributes();
    		
    		//查询门店信息
    		$store = Store::model()->findByPk($order['store_id']);
    		if (!empty($store)) {
    			$store_name = $store['name'];
    			//查询商户信息
    			$merchant = Merchant::model()->findByPk($store['merchant_id']);
    			if (!empty($merchant)) {
    				$merchant_name = $merchant['name'] ? $merchant['name'] : $merchant['wx_name'];
    				$operator_refund_time = $merchant['operator_refund_time'];
    				$manager_refund_time = $merchant['dzoperator_refund_time'];
    			}
    		}
    		//查询操作员信息
    		$operator = Operator::model()->findByPk($order['operator_id']);
    		if (!empty($operator)) {
    			$operator_number = $operator['number'];
    		}
    		//查询用户信息
    		$user = User::model()->findByPk($order['user_id']);
    		if (!empty($user)) {
    			$user_account = $user['account'];
    			$user_name = $user['name'].($user['sex'] == SEX_FEMALE ? '女士' : '先生');
    			$user_avatar = $user['avatar'];
    			//查询会员等级
    			$grade = UserGrade::model()->findByPk($user['membershipgrade_id']);
    			if (!empty($grade)) {
    				$user_grade = $grade['name'];
    			}
    		}
    		//查询退款记录
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('order_id = :order_id');
    		$criteria->params[':order_id'] = $order['id'];
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('type = :type');
    		$criteria->params[':type'] = REFUND_TYPE_REFUND;
    		$refund_record = RefundRecord::model()->findAll($criteria);
    		$record = array();
    		foreach ($refund_record as $k => $v) {
    			$refund_operator = Operator::model()->findByPk($v['operator_id']);
    			$number1 = !empty($refund_operator) ? $refund_operator['number'] : '';
    			$refund_admin = Operator::model()->findByPk($v['operator_admin_id']);
    			$number2 = !empty($refund_admin) ? $refund_admin['number'] : '';
    			
    			$record[] = array(
    				'refund_operator' => $number1,
    				'refund_admin' => $number2,
    				'refund_money' => $v['refund_money'],
    				'refund_time' => $v['refund_time'],
    			);
    		}
    		
    		$data['store_name'] = isset($store_name) ? $store_name : ''; //门店名称
    		$data['merchant_name'] = isset($merchant_name) ? $merchant_name : ''; //商户名称
    		$data['operator_refund_time'] = isset($operator_refund_time) ? $operator_refund_time : ''; //店员退款时间
    		$data['manager_refund_time'] = isset($manager_refund_time) ? $manager_refund_time : ''; //店长退款时间
    		$data['operator_number'] = isset($operator_number) ? $operator_number : ''; //操作员编号
    		$data['user_account'] = isset($user_account) ? $user_account : ''; //用户账号
    		$data['user_name'] = isset($user_name) ? $user_name : ''; //用户名称
    		$data['user_avatar'] = isset($user_avatar) ? $user_avatar : ''; //用户头像
    		$data['user_grade'] = isset($user_grade) ? $user_grade : ''; //用户会员等级
    		$data['refund_record'] = $record; //退款记录
    	
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 获取最新的指定个数的订单（app）
     * @param unknown $store_id
     * @param unknown $operator_id
     * @param number $num
     * @return string
     */
    public function getTopOrder4App($store_id, $operator_id, $num=0) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		$criteria = new CDbCriteria();
    		if (! empty ( $operator_id )) {
    			$operatprModel = Operator::model()->findByPk($operator_id);
    			if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
    				$criteria->addCondition ( 'operator_id=:operator_id and flag=:flag and store_id=:store_id' );
    				$criteria->params = array (
    						':operator_id' => $operator_id,
    						':flag' => FLAG_NO,
    						':store_id' => $store_id
    				);
    			}else{//如果操作员是店长(可以查看本店所有的订单记录)
    				$criteria->addCondition ( 'flag=:flag and store_id=:store_id' );
    				$criteria->params = array (
    						':flag' => FLAG_NO,
    						':store_id' => $store_id
    				);
    			}
    		}else{
    			$criteria->addCondition ( 'flag=:flag' );
    			$criteria->params = array (
    					':flag' => FLAG_NO
    			);
    		}
    		$criteria->order = 'create_time  desc';
    		$criteria->limit = $num;
    		$list = Order::model()->findAll($criteria);
    		//$data = $this->returnOrder($list);
    		$data = array();
    		foreach($list as $v) {
    			$data[] = array(
    					'orderNo' => $v['order_no']
    			);
    		}
    	
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 获取最后一个支付成功的订单（pos）
     * @param unknown $terminal_id
     * @throws Exception
     * @return string
     */
    public function getLastOrder4Pos($terminal_id) {
    	$result = array();
    	try {
    		//参数验证
    		if (empty($terminal_id)) {
    			$result['status'] = ERROR_PARAMETER_MISS;
    			throw new Exception('参数terminal_id不能为空');
    		}
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('terminal_id = :terminal_id');
    		$criteria->addCondition('pay_status = :pay_status');
    		//$criteria->addCondition('flag = :flag');
    		$criteria->order = 'pay_time  desc';
    		$criteria->params = array(
    			':terminal_id' => $terminal_id,
    			':pay_status' => ORDER_STATUS_PAID,
    		);
    		//$criteria->limit = $num; //记录数
    		$model = Order::model()->find($criteria);
    		//$data = $this->returnOrder($list);
    		$order_no = '';
    		if (!empty($model)) {
    			$order_no = $model['order_no'];
    		}
    		 
    		$result['order_no'] = $order_no;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 检查订单是否可退及是否需要店长密码
     * @param unknown $order_no
     * @throws Exception
     * @return string
     */
    public function checkRefundAndPwd($order_no) {
    	$result = array();
    	try {
    		//参数验证
    		if (empty($order_no)) {
    			$result['status'] = ERROR_PARAMETER_MISS;
    			throw new Exception('参数order_no不能为空');
    		}
    		$order = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
    		if (empty($order)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('订单不存在');
    		}
    		$order_status = $order['order_status']; //订单状态
    		$pay_status = $order['pay_status']; //支付状态
    		$create_time = $order['create_time']; //订单创建时间
    		$store_id = $order['store_id']; //门店id
    		
    		$refundable = true; //是否可退
    		$needPwd = false; //是否需要密码
    		
    		$operator_refund_time = 0;
    		$admin_refund_time = 0;
    		//查询门店
    		$store = Store::model()->findByPk($store_id);
    		if (empty($store)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('未找到相关门店信息');
    		}
    		//查询商户信息
    		$merchant = Merchant::model()->findByPk($store['merchant_id']);
    		if (empty($merchant)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('未找到相关商户信息');
    		}
    		$operator_refund_time = $merchant['operator_refund_time'];
    		$admin_refund_time = $merchant['dzoperator_refund_time'];
    		
    		//订单创建时间
    		$time = strtotime($create_time);
    		//是否超过店员可退时限
    		if ($time < strtotime("-{$operator_refund_time} second")) {
    			//需要密码
    			$needPwd = true;
    			//是否超过店长可退时限
    			if ($time < strtotime("-{$admin_refund_time} second")) {
    				$result['status'] = ERROR_EXCEPTION;
    				throw new Exception('超过退款时间');
    			}
    		}
    		//订单支付状态检查
    		if ($pay_status != ORDER_STATUS_PAID) {
    			$result['status'] = ERROR_EXCEPTION;
    			throw new Exception('未支付的订单无法退款');
    		}
    		//订单状态检查
    		if ($order['order_status'] != ORDER_STATUS_NORMAL && $order['order_status'] != ORDER_STATUS_PART_REFUND && $order['order_status'] != ORDER_STATUS_HANDLE_REFUND) {
    			$result['status'] = ERROR_EXCEPTION;
    			throw new Exception('订单已撤销或已退款');
    		}
    		 
    		$result['needPwd'] = $needPwd ? 'yes' : 'no';
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 获取订单汇总数据
     * @param unknown $operator_id
     * @param unknown $start_date
     * @param unknown $end_date
     * @param unknown $select_type
     * @param unknown $select_operator
     * @param unknown $order_status
     * @param unknown $pay_status
     * @param unknown $keyword
     * @throws Exception
     * @return string
     */
    public function getSummary($operator_id, $start_date, $end_date, $select_type, $select_operator, $order_status, $pay_status, $keyword) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		 
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		//操作员角色
    		$role = $operator['role'];
    		//操作员所属门店id
    		$store_id = $operator['store_id'];
    		 
    		//查询订单记录列表
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('store_id = :store_id');
    		$criteria->params[':store_id'] = $store_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		//店员只能查看自己的记录
    		if ($role == OPERATOR_ROLE_NORMAL) {
    			$criteria->addCondition('operator_id = :operator_id');
    			$criteria->params[':operator_id'] = $operator_id;
    		}
    		
    		if (!empty($start_date)) {
    			$criteria->addCondition('create_time >= :start_time');
    			$criteria->params[':start_time'] = $start_date;
    		}
    		if (!empty($end_date)) {
    			$criteria->addCondition('create_time <= :end_time');
    			$criteria->params[':end_time'] = $end_date;
    		}
    		if (!empty($select_type)) {
    			$criteria->addCondition('pay_channel = :pay_channel');
    			$criteria->params[':pay_channel'] = $select_type;
    		}
    		if (!empty($order_status)) {
    			$criteria->addCondition('order_status = :order_status');
    			$criteria->params[':order_status'] = $order_status;
    		}
    		if (!empty($pay_status)) {
    			$criteria->addCondition('pay_status = :pay_status');
    			$criteria->params[':pay_status'] = $pay_status;
    		}
    		if (!empty($select_operator)) {
    			$select_model = Operator::model()->find('store_id = :store_id AND number = :number and flag = :flag',
    					array(':store_id' => $store_id, ':number' => $select_operator, ':flag' => FLAG_NO));
    			if (!empty($select_model)) {
    				$select_operator_id = $select_model['id'];
    				$criteria->addCondition('operator_id = :operator_id');
    				$criteria->params[':operator_id'] = $select_operator_id;
    			}
    		}
    		if (!empty($keyword)) {
    			//查询会员信息
    			$select_users = User::model()->findAll("account like :account", array(':account' => '%'.$keyword.'%'));
    			//$select_user_id = !empty($select_user) ? $select_user['id'] : '';
    			$id_str = '';
    			foreach ($select_users as $select_user) {
    				if (empty($id_str)) {
    					$id_str .= $select_user['id'];
    				}else {
    					$id_str .= ','.$select_user['id'];
    				}
    			}
    			if (!$id_str) {
    				$criteria->addCondition("(order_no like :order_no) OR (ABS(order_paymoney - :order_paymoney) < 1e-5)");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    				$criteria->params[':order_paymoney'] = $keyword + 0.00; //查询float字段采用差值比较
    			}else {
    				$criteria->addCondition("(order_no like :order_no) OR (ABS(order_paymoney - :order_paymoney) < 1e-5) OR (user_id IN ($id_str))");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    				$criteria->params[':order_paymoney'] = $keyword + 0.00; //查询float字段采用差值比较
    				//$criteria->params[':user_id'] = $id_str;
    			}
//     			if (!$id_str) {
//     				$criteria->addCondition("order_no like :order_no");
//     				$criteria->params[':order_no'] = '%'.$keyword.'%';
//     			}else {
//     				$criteria->addCondition("(order_no like :order_no) OR (user_id IN ($id_str))");
//     				$criteria->params[':order_no'] = '%'.$keyword.'%';
//     			}
    		}
    		
    		//查询
    		$list = Order::model()->findAll($criteria);
    		 
    		$data = array(
    				'total_trade_money' => 0, //交易金额
    				'total_receipt_money' => 0, //实收金额
    				'total_discount_money' => 0, //优惠金额
    				'total_refund_money' => 0, //退款金额
    				'total_trade_count' => 0, //交易笔数
    				'total_refund_count' => 0, //退款笔数
    				'alipay_qrcode_trade_money' => 0, //支付宝扫码交易金额
    				'alipay_qrcode_refund_money' => 0, //支付宝扫码退款金额
    				'alipay_qrcode_discount_money' => 0, //支付宝扫码优惠金额
    				'alipay_qrcode_receipt_money' => 0, //支付宝扫码实收金额
    				'alipay_qrcode_trade_count' => 0, //支付宝扫码交易笔数
    				'alipay_qrcode_refund_count' => 0, //支付宝扫码退款笔数
    				'alipay_barcode_trade_money' => 0, //支付宝条码交易金额
    				'alipay_barcode_refund_money' => 0, //支付宝条码退款金额
    				'alipay_barcode_discount_money' => 0, //支付宝条码优惠金额
    				'alipay_barcode_receipt_money' => 0, //支付宝条码实收金额
    				'alipay_barcode_trade_count' => 0, //支付宝条码交易笔数
    				'alipay_barcode_refund_count' => 0, //支付宝条码退款笔数
    				'wxpay_qrcode_trade_money' => 0, //微信扫码交易金额
    				'wxpay_qrcode_refund_money' => 0, //微信扫码退款金额
    				'wxpay_qrcode_discount_money' => 0, //微信扫码优惠金额
    				'wxpay_qrcode_receipt_money' => 0, //微信扫码实收金额
    				'wxpay_qrcode_trade_count' => 0, //微信扫码交易笔数
    				'wxpay_qrcode_refund_count' => 0, //微信扫码退款笔数
    				'wxpay_barcode_trade_money' => 0, //微信条码交易金额
    				'wxpay_barcode_refund_money' => 0, //微信条码退款金额
    				'wxpay_barcode_discount_money' => 0, //微信条码优惠金额
    				'wxpay_barcode_receipt_money' => 0, //微信条码实收金额
    				'wxpay_barcode_trade_count' => 0, //微信条码交易笔数
    				'wxpay_barcode_refund_count' => 0, //微信条码退款笔数
    				'cashpay_trade_money' => 0, //现金支付交易金额
    				'cashpay_refund_money' => 0, //现金支付退款金额
    				'cashpay_discount_money' => 0, //现金支付优惠金额
    				'cashpay_receipt_money' => 0, //现金支付实收金额
    				'cashpay_trade_count' => 0, //现金支付交易笔数
    				'cashpay_refund_count' => 0, //现金支付退款笔数
    				'unionpay_trade_money' => 0, //银联支付交易金额
    				'unionpay_refund_money' => 0, //银联支付退款金额
    				'unionpay_discount_money' => 0, //银联支付优惠金额
    				'unionpay_receipt_money' => 0, //银联支付实收金额
    				'unionpay_trade_count' => 0, //银联支付交易笔数
    				'unionpay_refund_count' => 0, //银联支付退款笔数
    				'storedpay_trade_money' => 0, //储值支付交易金额
    				'storedpay_refund_money' => 0, //储值支付退款金额
    				'storedpay_discount_money' => 0, //储值支付优惠金额
    				'storedpay_receipt_money' => 0, //储值支付实收金额
    				'storedpay_trade_count' => 0, //储值支付交易笔数
    				'storedpay_refund_count' => 0, //储值支付退款笔数
    		);
    		foreach ($list as $k => $v) {
    			$order_no = $v['order_no']; //订单编号
    			$order_type = $v['order_type']; //订单类型
    			$pay_status = $v['pay_status']; //支付状态
    			$order_status = $v['order_status']; //订单状态
    			$pay_channel = $v['pay_channel']; //支付方式
    			$order_paymoney = $v['order_paymoney']; //订单总金额
    			$stored_paymoney = $v['stored_paymoney']; //储值支付的金额
    			$discount_money = $v['coupons_money'] + $v['discount_money'] + $v['merchant_discount_money']; //优惠金额
    			
    			//筛选必要订单
    			if ($pay_status != ORDER_STATUS_PAID) {
    				continue; //未支付的订单不计入汇总
    			}
    			if ($order_type != ORDER_TYPE_CASHIER) {
    				continue; //非收款订单不计入汇总
    			}
    			//计算实收金额和退款金额
    			$order_detail = $this->getReceiptAmount($order_no, TRUE);
    			$receipt_money = $order_detail['receipt_money']; //实收金额
    			$refund_money = $order_detail['refund_money']; //退款金额
    			$refund_count = $order_detail['refund_count']; //退款笔数
    			$stored_receipt_money = $order_detail['stored_receipt_money']; //储值支付实收
    			$stored_discount_money = $pay_channel == ORDER_PAY_CHANNEL_STORED ? $discount_money : 0; //储值支付优惠金额
    			$stored_refund_money = $order_detail['stored_refund_money']; //储值支付已退
    			$stored_refund_count = $order_detail['stored_refund_count']; //储值支付退款笔数
    			$stored_trade_money = $stored_paymoney; //储值支付交易金额
    			$stored_trade_count = round($stored_trade_money, 2) > 0 ? 1 : 0; //储值支付交易笔数
    			$other_receipt_money = $order_detail['other_receipt_money']; //非储值实收
    			$other_discount_money = $pay_channel != ORDER_PAY_CHANNEL_STORED ? $discount_money : 0; //非储值优惠金额
    			$other_refund_money = $order_detail['other_refund_money']; //非储值已退
    			$other_refund_count = $order_detail['other_refund_count']; //非储值退款笔数
    			$other_trade_money = $order_paymoney - $stored_paymoney; //非储值交易金额
    			$other_trade_count = round($other_trade_money, 2) > 0 ? 1 : 0; //非储值交易笔数
    			
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$pay_channel_prefix = 'alipay_qrcode';
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$pay_channel_prefix = 'alipay_barcode';
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$pay_channel_prefix = 'wxpay_qrcode';
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$pay_channel_prefix = 'wxpay_barcode';
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$pay_channel_prefix = 'cashpay';
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$pay_channel_prefix = 'unionpay';
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$pay_channel_prefix = 'storedpay';
    					break;
    				default:
    					$pay_channel_prefix = '';
    					break;
    			}
    			
    			//不符合指定支付方式的不计入汇总
    			if (empty($pay_channel_prefix)) {
    				continue;
    			}
    			$data[$pay_channel_prefix.'_trade_money'] += $other_trade_money;
    			$data[$pay_channel_prefix.'_refund_money'] += $other_refund_money;
    			$data[$pay_channel_prefix.'_discount_money'] += $other_discount_money;
    			$data[$pay_channel_prefix.'_receipt_money'] += $other_receipt_money;
    			$data[$pay_channel_prefix.'_trade_count'] += $other_trade_count;
    			$data[$pay_channel_prefix.'_refund_count'] += $other_refund_count;
    			
    			$data['storedpay_trade_money'] += $stored_trade_money;
    			$data['storedpay_refund_money'] += $stored_refund_money;
    			$data['storedpay_discount_money'] += $stored_discount_money;
    			$data['storedpay_receipt_money'] += $stored_receipt_money;
    			$data['storedpay_trade_count'] += $stored_trade_count;
    			$data['storedpay_refund_count'] += $stored_refund_count;
    			
    			$data['total_trade_money'] += $stored_trade_money + $other_trade_money;
    			$data['total_refund_money'] += $refund_money;
    			$data['total_discount_money'] += $discount_money;
    			$data['total_receipt_money'] += $receipt_money;
    			$data['total_trade_count'] += 1;
    			$data['total_refund_count'] += $refund_count;
    		}
    		
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 生成结算记录数据
     * @param unknown $operator_id
     * @throws Exception
     * @return string
     */
    public function generateSettlementRecord($operator_id) {
    	$result = array();
    	try {
    		$operator = Operator::model()->findByPk($operator_id);
    		if (empty($operator)) {
    			throw new Exception('操作员不存在');
    		}
    		
    		//操作员编号
    		$operator_no = $operator['number'];
    		
    		//获取上次结算时间
    		$last_time = '';
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('operator_id = :operator_id');
    		$criteria->params[':operator_id'] = $operator_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		$last_record = Settlement::model()->find($criteria);
    		if (!empty($last_record)) {
    			$last_time = $last_record['end_time'];
    		}
    		
    		//获取上次结算时间到现在的订单结算数据
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('t.flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('t.pay_status = :custom_pay_status');
    		$cmd->params[':custom_pay_status'] = ORDER_STATUS_PAID; //已支付订单
    		$cmd->andWhere('t.order_type = :custom_order_type');
    		$cmd->params[':custom_order_type'] = ORDER_TYPE_CASHIER; //收银订单
    		$cmd->andWhere('t.operator_id = :operator_id');
    		$cmd->params[':operator_id'] = $operator_id;
    		if (!empty($last_time)) {
    			$cmd->andWhere('t.pay_time > :pay_time');
    			$cmd->params[':pay_time'] = $last_time;
    		}
    		//分组
    		$cmd->group = 'pay_channel';
    		//指定查询表
    		$cmd->from = 'wq_order t';
    		
    		//退款查询
    		$cmd2 = clone $cmd; //深拷贝
    		
    		//查询计算1
    		$select1 = 'pay_channel';
    		$select1 .= ', MIN(pay_time) AS min_time';
    		$select1 .= ', MAX(pay_time) AS max_time';
    		$select1 .= ', SUM(order_paymoney) AS order_sum';
    		$select1 .= ', SUM(coupons_money) AS coupons_sum';
    		$select1 .= ', SUM(discount_money) AS discount_sum';
    		$select1 .= ', SUM(merchant_discount_money) AS m_discount_sum';
    		$select1 .= ', COUNT(pay_channel) AS trade_sum';
    		$cmd->select = $select1;
    		
    		//执行sql查询:统计订单金额，优惠金额，交易笔数
    		$list1 = $cmd->queryAll();
    		
    		//查询计算2
    		$select2 = 'pay_channel, SUM(r.refund_money) AS refund_sum, COUNT(r.order_id) AS record_sum';
    		$cmd2->select = $select2;
    		
    		//联表
    		$join = 'JOIN wq_refund_record r ON t.id = r.order_id';
    		$join .= ' AND r.flag = '.FLAG_NO.' AND r.status != '.REFUND_STATUS_FAIL.' AND r.type = '.REFUND_TYPE_REFUND;
    		$cmd2->join = $join;
    		
    		//执行sql查询:统计退款金额
    		$list2 = $cmd2->queryAll();
    		
    		$data = array(
    				'total_trade_money' => 0, //交易金额
    				'total_receipt_money' => 0, //实收金额
    				'total_discount_money' => 0, //优惠金额
    				'total_refund_money' => 0, //退款金额
    				'total_trade_count' => 0, //交易笔数
    				'total_refund_count' => 0, //退款笔数
    				'alipay_trade_money' => 0, //支付宝交易金额
    				'alipay_refund_money' => 0, //支付宝退款金额
    				'alipay_discount_money' => 0, //支付宝优惠金额
    				'alipay_receipt_money' => 0, //支付宝实收金额
    				'alipay_trade_count' => 0, //支付宝交易笔数
    				'alipay_refund_count' => 0, //支付宝退款笔数
    				'wxpay_trade_money' => 0, //微信交易金额
    				'wxpay_refund_money' => 0, //微信退款金额
    				'wxpay_discount_money' => 0, //微信优惠金额
    				'wxpay_receipt_money' => 0, //微信实收金额
    				'wxpay_trade_count' => 0, //微信交易笔数
    				'wxpay_refund_count' => 0, //微信退款笔数
    				'cashpay_trade_money' => 0, //现金支付交易金额
    				'cashpay_refund_money' => 0, //现金支付退款金额
    				'cashpay_discount_money' => 0, //现金支付优惠金额
    				'cashpay_receipt_money' => 0, //现金支付实收金额
    				'cashpay_trade_count' => 0, //现金支付交易笔数
    				'cashpay_refund_count' => 0, //现金支付退款笔数
    				'unionpay_trade_money' => 0, //银联支付交易金额
    				'unionpay_refund_money' => 0, //银联支付退款金额
    				'unionpay_discount_money' => 0, //银联支付优惠金额
    				'unionpay_receipt_money' => 0, //银联支付实收金额
    				'unionpay_trade_count' => 0, //银联支付交易笔数
    				'unionpay_refund_count' => 0, //银联支付退款笔数
    				'storedpay_trade_money' => 0, //储值支付交易金额
    				'storedpay_refund_money' => 0, //储值支付退款金额
    				'storedpay_discount_money' => 0, //储值支付优惠金额
    				'storedpay_receipt_money' => 0, //储值支付实收金额
    				'storedpay_trade_count' => 0, //储值支付交易笔数
    				'storedpay_refund_count' => 0, //储值支付退款笔数
    		);
    		
    		$start_time = ''; //结算开始时间
    		$end_time = ''; //结算结束时间
    		foreach ($list1 as $k => $v) {
    			$pay_channel = $v['pay_channel']; //支付方式
    			$order_money = $v['order_sum']; //订单总金额
    			$coupons_discount = $v['coupons_sum']; //优惠券优惠金额
    			$member_discount = $v['discount_sum']; //会员优惠
    			$merchant_discount = $v['m_discount_sum']; //商家优惠
    			$trade_count = $v['trade_sum']; //交易笔数
    			$min_time = $v['min_time']; //最小支付时间
    			$max_time = $v['max_time']; //最大支付时间
    			
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    			//优惠金额
    			$discount_money = $coupons_discount + $member_discount + $merchant_discount;
    			//实收金额
    			$receipt_money = $order_money - $discount_money;
    			//计算交易金额和实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data['alipay_trade_money'] += $order_money;
    					$data['alipay_discount_money'] += $discount_money;
    					$data['alipay_receipt_money'] += $receipt_money;
    					$data['alipay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data['alipay_trade_money'] += $order_money;
    					$data['alipay_discount_money'] += $discount_money;
    					$data['alipay_receipt_money'] += $receipt_money;
    					$data['alipay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data['wxpay_trade_money'] += $order_money;
    					$data['wxpay_discount_money'] += $discount_money;
    					$data['wxpay_receipt_money'] += $receipt_money;
    					$data['wxpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data['wxpay_trade_money'] += $order_money;
    					$data['wxpay_discount_money'] += $discount_money;
    					$data['wxpay_receipt_money'] += $receipt_money;
    					$data['wxpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data['cashpay_trade_money'] += $order_money;
    					$data['cashpay_discount_money'] += $discount_money;
    					$data['cashpay_receipt_money'] += $receipt_money;
    					$data['cashpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data['unionpay_trade_money'] += $order_money;
    					$data['unionpay_discount_money'] += $discount_money;
    					$data['unionpay_receipt_money'] += $receipt_money;
    					$data['unionpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data['storedpay_trade_money'] += $order_money;
    					$data['storedpay_discount_money'] += $discount_money;
    					$data['storedpay_receipt_money'] += $receipt_money;
    					$data['storedpay_trade_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data['total_trade_money'] += $order_money;
    			$data['total_discount_money'] += $discount_money;
    			$data['total_receipt_money'] += $receipt_money;
    			$data['total_trade_count'] += $trade_count;
    			
    			//结算时间范围统计
    			if (empty($start_time)) {
    				$start_time = $min_time;
    			}elseif (strtotime($start_time) > strtotime($min_time)) {
    				$start_time = $min_time;
    			}
    			if (empty($end_time)) {
    				$end_time = $max_time;
    			}elseif (strtotime($end_time) < strtotime($max_time)) {
    				$end_time = $max_time;
    			}
    		}
    		
    		foreach ($list2 as $k => $v) {
    			$pay_channel = $v['pay_channel']; //支付方式
    			$refund_money = $v['refund_sum']; //退款金额
    			$refund_count = $v['record_sum']; //退款笔数
    				
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    				
    			//计算实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data['alipay_refund_count'] += $refund_count;
						$data['alipay_refund_money'] += $refund_money;
    					$data['alipay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data['alipay_refund_count'] += $refund_count;
						$data['alipay_refund_money'] += $refund_money;
    					$data['alipay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data['wxpay_refund_count'] += $refund_count;
						$data['wxpay_refund_money'] += $refund_money;
    					$data['wxpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data['wxpay_refund_count'] += $refund_count;
						$data['wxpay_refund_money'] += $refund_money;
    					$data['wxpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data['cashpay_refund_count'] += $refund_count;
						$data['cashpay_refund_money'] += $refund_money;
    					$data['cashpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data['unionpay_refund_count'] += $refund_count;
						$data['unionpay_refund_money'] += $refund_money;
    					$data['unionpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data['storedpay_refund_count'] += $refund_count;
						$data['storedpay_refund_money'] += $refund_money;
    					$data['storedpay_receipt_money'] -= $refund_money;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data['total_refund_count'] += $refund_count;
				$data['total_refund_money'] += $refund_money;
    			$data['total_receipt_money'] -= $refund_money;
    		}
    		
    		$result['operator'] = $operator_no;
    		$result['start_time'] = $start_time;
    		$result['end_time'] = $end_time;
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 结算
     */
    public function settlement($operator_id) {
    	$result = array();
    	try {
    		$ret = $this->generateSettlementRecord($operator_id);
    		$res = json_decode($ret, true);
    		if ($res['status'] != ERROR_NONE) {
    			throw new Exception($res['errMsg']);
    		}
    		$operator_no = $res['operator'];
    		$start_time = $res['start_time'];
    		$end_time = $res['end_time'];
    		$data = $res['data'];
			//无可结算数据
			if(empty($start_time) && empty($end_time)) {
				throw new Exception('无可结算的记录');
			}
    		
    		//查询操作员信息
    		$operator = Operator::model()->findByPk($operator_id);
    		$store_id = $operator['store_id']; //门店id
    		
    		//保存结算记录
    		$settlement = new Settlement();
    		$settlement['create_time'] = date('Y-m-d H:i:s');
    		$settlement['operator_id'] = $operator_id;
    		$settlement['store_id'] = $store_id;
    		$settlement['start_time'] = $start_time;
    		$settlement['end_time'] = $end_time;
    		$settlement['total_money'] = $data['total_trade_money'];
    		$settlement['total_refund_money'] = $data['total_refund_money'];
    		$settlement['total_discount_money'] = $data['total_discount_money'];
    		$settlement['total_actual_money'] = $data['total_receipt_money'];
    		$settlement['total_num'] = $data['total_trade_count'];
    		$settlement['total_refund_num'] = $data['total_refund_count'];
    		
    		$settlement['alipay_money'] = $data['alipay_trade_money'];
    		$settlement['alipay_refund_money'] = $data['alipay_refund_money'];
    		$settlement['alipay_discount_money'] = $data['alipay_discount_money'];
    		$settlement['alipay_actual_money'] = $data['alipay_receipt_money'];
    		$settlement['alipay_num'] = $data['alipay_trade_count'];
    		$settlement['alipay_refund_num'] = $data['alipay_refund_count'];
    		
    		$settlement['wechat_money'] = $data['wxpay_trade_money'];
    		$settlement['wechat_refund_money'] = $data['wxpay_refund_money'];
    		$settlement['wechat_discount_money'] = $data['wxpay_discount_money'];
    		$settlement['wechat_actual_money'] = $data['wxpay_receipt_money'];
    		$settlement['wechat_num'] = $data['wxpay_trade_count'];
    		$settlement['wechat_refund_num'] = $data['wxpay_refund_count'];
    		
    		$settlement['unionpay_money'] = $data['unionpay_trade_money'];
    		$settlement['unionpay_refund_money'] = $data['unionpay_refund_money'];
    		$settlement['unionpay_discount_money'] = $data['unionpay_discount_money'];
    		$settlement['unionpay_actual_money'] = $data['unionpay_receipt_money'];
    		$settlement['unionpay_num'] = $data['unionpay_trade_count'];
    		$settlement['unionpay_refund_num'] = $data['unionpay_refund_count'];
    		
    		$settlement['cash_money'] = $data['cashpay_trade_money'];
    		$settlement['cash_refund_money'] = $data['cashpay_refund_money'];
    		$settlement['cash_discount_money'] = $data['cashpay_discount_money'];
    		$settlement['cash_actual_money'] = $data['cashpay_receipt_money'];
    		$settlement['cash_num'] = $data['cashpay_trade_count'];
    		$settlement['cash_refund_num'] = $data['cashpay_refund_count'];
    		
    		$settlement['stored_money'] = $data['storedpay_trade_money'];
    		$settlement['stored_refund_money'] = $data['storedpay_refund_money'];
    		$settlement['stored_discount_money'] = $data['storedpay_discount_money'];
    		$settlement['stored_actual_money'] = $data['storedpay_receipt_money'];
    		$settlement['stored_num'] = $data['storedpay_trade_count'];
    		$settlement['stored_refund_num'] = $data['storedpay_refund_count'];
    		
    		if (!$settlement->save()) {
    			throw new Exception('数据保存失败');
    		}
    		
    		$result['operator'] = $operator_no;
    		$result['start_time'] = $start_time;
    		$result['end_time'] = $end_time;
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 获取结算列表 （app）
     * @param unknown $operator_id
     * @param unknown $limit_id
     * @throws Exception
     * @return string
     */
    public function getSettlementList4App($operator_id, $limit_id) {
    	$result = array();
    	try {
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		$operator_no = $operator['number'];
    		
    		//查询结算记录
    		$criteria = new CDbCriteria();
    		$criteria->order = 'create_time desc';
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('operator_id = :operator_id');
    		$criteria->params[':operator_id'] = $operator_id;
    		if (!empty($limit_id) && $limit_id != 'ALL') {
    			$criteria->addCondition('id < :id');
    			$criteria->params[':id'] = $limit_id;
    		}
    		//是否获取只获取指定页码的数据，ALL：获取所有数据, 数字：指定页的数据
    		if ($limit_id != 'ALL') {
    			$page_size = Yii::app()->params['perPage'];
    			$criteria->limit = $page_size ? : 10;
    		}
    		
    		$list = Settlement::model()->findAll($criteria);
    		$data = array();
    		foreach($list as $v) {
    			$tmp = array();
    			$tmp['id'] = $v['id'];
    			$tmp['start_time'] = $v['start_time'];
    			$tmp['end_time'] = $v['end_time'];
    			$tmp['operator'] = $operator_no;
    			$tmp['total_trade_money'] = $v['total_money'];
    			$tmp['total_refund_money'] = $v['total_refund_money'];
    			$tmp['total_discount_money'] = $v['total_discount_money'];
    			$tmp['total_receipt_money'] = $v['total_actual_money'];
    			$tmp['total_trade_count'] = $v['total_num'];
    			$tmp['total_refund_count'] = $v['total_refund_num'];
    			 
    			$data[] = $tmp;
    		}
    		
    		$result['item_count'] = count($data);
    		$result['page_count'] = '';
    		$result['data'] = $data;
    		
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    /**
     * 获取结算详情
     * @param unknown $settlement_id
     * @throws Exception
     * @return string
     */
    public function getSettlementDetail($settlement_id) {
    	$result = array();
    	try {
    		//查询结算记录信息
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('id = :id');
    		$criteria->params[':id'] = $settlement_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$model = Settlement::model()->find($criteria);
    		if (empty($model)) {
    			throw new Exception('查询数据不存在');
    		}
    		
    		//查询操作员信息
    		$operator_id = $model['operator_id'];
    		$operator = Operator::model()->findByPk($operator_id);
    		$operator_no = $operator['number'];
    		
    		$data = array();
    		
    		$data['total_trade_money'] = $model['total_money'];
    		$data['total_refund_money'] = $model['total_refund_money'];
    		$data['total_discount_money'] = $model['total_discount_money'];
    		$data['total_receipt_money'] = $model['total_actual_money'];
    		$data['total_trade_count'] = $model['total_num'];
    		$data['total_refund_count'] = $model['total_refund_num'];
    		
    		$data['alipay_trade_money'] = $model['alipay_money'];
    		$data['alipay_refund_money'] = $model['alipay_refund_money'];
    		$data['alipay_discount_money'] = $model['alipay_discount_money'];
    		$data['alipay_receipt_money'] = $model['alipay_actual_money'];
    		$data['alipay_trade_count'] = $model['alipay_num'];
    		$data['alipay_refund_count'] = $model['alipay_refund_num'];
    		
    		$data['wxpay_trade_money'] = $model['wechat_money'];
    		$data['wxpay_refund_money'] = $model['wechat_refund_money'];
    		$data['wxpay_discount_money'] = $model['wechat_discount_money'];
    		$data['wxpay_receipt_money'] = $model['wechat_actual_money'];
    		$data['wxpay_trade_count'] = $model['wechat_num'];
    		$data['wxpay_refund_count'] = $model['wechat_refund_num'];
    		
    		$data['unionpay_trade_money'] = $model['unionpay_money'];
    		$data['unionpay_refund_money'] = $model['unionpay_refund_money'];
    		$data['unionpay_discount_money'] = $model['unionpay_discount_money'];
    		$data['unionpay_receipt_money'] = $model['unionpay_actual_money'];
    		$data['unionpay_trade_count'] = $model['unionpay_num'];
    		$data['unionpay_refund_count'] = $model['unionpay_refund_num'];
    		
    		$data['cashpay_trade_money'] = $model['cash_money'];
    		$data['cashpay_refund_money'] = $model['cash_refund_money'];
    		$data['cashpay_discount_money'] = $model['cash_discount_money'];
    		$data['cashpay_receipt_money'] = $model['cash_actual_money'];
    		$data['cashpay_trade_count'] = $model['cash_num'];
    		$data['cashpay_refund_count'] = $model['cash_refund_num'];
    		
    		$data['storedpay_trade_money'] = $model['stored_money'];
    		$data['storedpay_refund_money'] = $model['stored_refund_money'];
    		$data['storedpay_discount_money'] = $model['stored_discount_money'];
    		$data['storedpay_receipt_money'] = $model['stored_actual_money'];
    		$data['storedpay_trade_count'] = $model['stored_num'];
    		$data['storedpay_refund_count'] = $model['stored_refund_num'];
    		
			$result['start_time'] = $model['start_time'];
    		$result['end_time'] = $model['end_time'];
    		$result['operator'] = $operator_no;
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    
}