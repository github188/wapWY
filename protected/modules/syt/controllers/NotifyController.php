<?php
/**
 * 异步通知控制器
 *
 */
class NotifyController extends sytController
{
	/**
	 * 重写init方法， 不执行父类的 未登录就提示登录程序
	 */
	public function init() {
	
	}
	/**
	 * 服务器支付宝异步通知页面路径
	 */
	public function actionAsyNotifyAli() {
		header("content-Type: text/html; charset=Utf-8");
		
		//商户订单号
		$out_trade_no = $_POST['out_trade_no'];
		//支付宝交易号
		$trade_no = $_POST['trade_no'];
		//交易状态
		$trade_status = $_POST['trade_status'];
		//买家支付宝邮箱
		$buyer_email = isset($_POST['buyer_email']) ? $_POST['buyer_email'] : '';
		//买家支付宝账号
		$buyer_logon_id = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : '';
		//买家支付宝用户号
		$buyer_user_id = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : '';
		
		$api = new AlipaySC();
		$ret = $api->alipayVerifyNotify($out_trade_no);
		$result = json_decode($ret, true);
		if ($result['status'] == ERROR_NONE) { //验证成功
			$pay_status = isset($result['pay_status']) ? $result['pay_status'] : '';
			if (($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') && $pay_status == ORDER_STATUS_UNPAID) { //支付宝返回已完成且订单为未支付状态时则修改订单支付状态
				$transaction = Yii::app()->db->beginTransaction(); //开启事务
				try {
					//订单判断
					$is_cz = strpos($out_trade_no, STORED_ORDER_PREFIX) === 0 ? true : false;
					if ($is_cz) { //储值订单
						$order = new MemberStoredC();
					}else { //收银订单
						$order = new OrderSC();
					}
					$pay_time = isset($_POST['gmt_payment']) ? $_POST['gmt_payment'] : date('Y-m-d H:i:s');
					$buyer_account = !empty($buyer_email) ? $buyer_email : $buyer_logon_id;
					
					//交易资金明细
					$list_string = isset($_POST['fund_bill_list']) ? $_POST['fund_bill_list'] : (isset($_POST['paytools_pay_amount']) ? $_POST['paytools_pay_amount'] : '');
					$list_array = json_decode($list_string, true);
					$discounts = $api->getDiscounts($list_array);
					$merchant_discount = $discounts['merchant_discount']; //商家优惠
					$alipay_discount = $discounts['alipay_discount']; //支付宝优惠
					
					$ret = $order->orderPaySuccess($out_trade_no, $pay_time, $trade_no, $buyer_account, $merchant_discount, $alipay_discount, $buyer_user_id);
					if (!empty($ret) && $ret['status'] == ERROR_NONE) {
						//订单修改成功
						$transaction->commit(); //数据提交
					}else {
						throw new Exception('订单修改失败');
					}
				} catch (Exception $e) {
					$transaction->rollback(); //数据回滚
					echo 'fail';
					return ;
				}
			}
			echo 'success'; //请不要修改或删除
		}else { //验证失败
			echo 'fail';
		}
	}
        
	/**
	 * 服务器微信异步通知页面路径
	 */
	public function actionAsyNotifyWx() {
		$xml = file_get_contents("php://input");
		//xml解析
		libxml_disable_entity_loader(true);
		$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		
		$out_trade_no = $arr['out_trade_no']; //获取订单号
		$result_code = $arr['result_code']; //业务结果码
		$trade_no = $arr['transaction_id']; //微信支付订单号
		$open_id = $arr['openid']; //用户标识
		$sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //用户子标识
		$pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');
		
		//openid处理,返回参数中有sub_mch_id表示是特约商户的订单
		if (isset($arr['sub_mch_id'])) {
			$wxpay_openid = $sub_open_id;
			$wxpay_p_openid = $open_id;
		}else {
			$wxpay_openid = $open_id;
			$wxpay_p_openid = NULL;
		}
		
		//处理结果
		$flag = false;
		$msg = 'OK';
		
		//回调函数
		$callback = function () use($result_code, $out_trade_no, $trade_no, $wxpay_openid, $wxpay_p_openid, $pay_time){
			$array = array('flag' => false, 'msg' => '');
			if ($result_code == _SUCCESS) { //微信返回成功时则修改订单支付状态
				$transaction = Yii::app()->db->beginTransaction(); //开启事务
				try {
					//订单判断
					$is_cz = strpos($out_trade_no, STORED_ORDER_PREFIX) === 0 ? true : false;
					if ($is_cz) { //储值订单
						$order = new MemberStoredC();
					}else { //收银订单
						$order = new OrderSC();
					}
					$ret = $order->orderPaySuccess($out_trade_no, $pay_time, $trade_no, NULL, NULL, NULL, NULL, $wxpay_openid, $wxpay_p_openid);
					if (!empty($ret) && $ret['status'] == ERROR_NONE) {
						//订单修改成功
						$transaction->commit(); //数据提交
						$array['flag'] = true;
					}else {
						throw new Exception('订单修改失败');
					}
				} catch (Exception $e) {
					$transaction->rollback(); //数据回滚
					$msg = $e->getMessage();
					$array['msg'] = $msg;
				}
			}else {
				$array['flag'] = true;
			}
			return $array;
		};
		
		
		$api = new WxpaySC();
		$api->wxpayVerifyNotify($out_trade_no, $callback);
	}
	
	/**
	 * 服务器银商异步通知页面路径（线下通知地址）
	 * @throws Exception
	 */
	public function actionAsyNotifyUms() {
		ini_set("display_errors", "On");
		
		error_reporting(E_ALL | E_STRICT);
		header("content-Type: text/html; charset=Utf-8");
		Yii::import('application.extensions.utility.*');
		require_once "crypt3des.php";
		require_once "ums_signature.php";
		
		$merchant = isset($_GET['merchant']) ? $_GET['merchant'] : ''; //商户标识
		$data = $_POST;
		
		$params = isset($data['params']) ? $data['params'] : '';
		$signature = isset($data['signature']) ? $data['signature'] : '';
		
		if (empty($merchant) || empty($params) || empty($signature)) {
			//exit();
		}
		
		//验签，解密signature和公钥来比对params
		$rsa = new UmsSignature();
		if (!$rsa->verify($params, $signature)) {
			exit();
		}
		
		//获取商户的3des密钥
// 		$merchantC = new MerchantC();
// 		$ret = $merchantC->getUms3DesKey($merchant);
// 		$result = json_decode($ret, true);
// 		if ($result['status'] != ERROR_NONE) {
// 			exit();
// 		}
// 		$key = $result['key'];
		$key = 'a135ca0734fac82c924f99d78ad41493'; //测试用的3des密钥
		
		//params解密处理
		$crypt3des = new Crypt3Des($key);
		$params_str = $crypt3des->decrypt($params);
		
		//获取数组数据
		$info = json_decode($params_str, true);
		
		//参数数据
		$order_status = $info['dealStatus']; //交易状态(1:支付成功、2:支付失败、3:支付中、 4:已撤销、5:撤销中、6:已退货、7:退货中、8:退货失败)
		$account = $info['account']; //银行卡号(前六后四,中间部分用*标识)
		$amount = $info['amount']; //交易金额(单位:分)
		$pos_seq_id = $info['posSeqId']; //POS 流水号
		$batch_no = $info['batchNo']; //批次号
		$currency_code = $info['currencyCode']; //交易币种
		$merchant_id = $info['merchantId']; //商户代码
		$merchant_name = $info['merchantName']; //商户名称
		$deal_date = $info['dealDate']; //受卡方所在日期(yyyy-MM-dd)
		$deal_time = $info['dealTime']; //受卡方所在时间(hh:mm:ss)
		$ref_id = $info['refId']; //参考号
		$term_id = $info['termId']; //终端号
		$trade_no = $info['orderId']; //银商订单号
		$order_no = $info['merchantOrderId']; //商户订单号
		$sub_inst = $info['subInst']; //分支机构
		$liq_date = $info['liqDate']; //清算日期(MMdd)
		$device_id = $info['deviceId']; //设备号
		
		if ($order_status == '1') {
			$flag = false;
			//支付成功
			$transaction = Yii::app()->db->beginTransaction(); //开启事务
			try {
				//订单判断
				$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
				if ($is_cz) { //储值订单
					$order = new MemberStoredC();
				}else { //收银订单
					$order = new OrderSC();
				}
				//$pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');
				$pay_time = $deal_date.' '.$deal_time;
				$ret = $order->orderPaySuccess($order_no, $pay_time, $trade_no, null);
				if (!empty($ret) && $ret['status'] == ERROR_NONE) {
					//订单修改成功
					$transaction->commit(); //数据提交
					$flag = true;
				}else {
					throw new Exception('订单修改失败');
				}
			} catch (Exception $e) {
				$transaction->rollback(); //数据回滚
			}
			
			//请求响应信息
			$response = array(
				'orderId' => $trade_no, //银商订单号
				'merchantMsgProcessId' => '000000000000000', //商户接收消息后的信息处理 ID
				'merchantMsgProcessTime' => date("Y-m-d H:i:s"), //商户消息处理时间(yyyy-MM-dd hh:mm:ss)
				'merchantRecMsgProcessState' => '0', //商户处理结果状态(0 失败, 1 成功)
			);
			if ($flag) {
				$response['merchantRecMsgProcessState'] = '1';
			}
			//写文件测试
			$myfile = fopen("umslog.txt", "a") or die("Unable to open file!");
			$txt = "order_no:".$order_no."\n";
			fwrite($myfile, $txt);
			$txt = "response:".json_encode($response)."\n";
			fwrite($myfile, $txt);
			fwrite($myfile, "------------------\n");
			fclose($myfile);
			
			exit(json_encode($response));
		}
		
	}
	
	/**
	 * 服务器银商异步通知页面路径（线上通知地址）
	 * @throws Exception
	 */
	public function actionAsyNotifyUms2() {
		header("content-Type: text/html; charset=Utf-8");
		$data = $_POST;
	
		$order_no = isset($data['MerOrderId']) ? $data['MerOrderId'] : '';
		$trade_no = isset($data['TransId']) ? $data['TransId'] : '';
		$trade_status = isset($data['TransState']) ? $data['TransState'] : '';
		$order_date = isset($data['OrderDate']) ? $data['OrderDate'] : '';
		$order_time = isset($data['OrderTime']) ? $data['OrderTime'] : '';
		$tmp = strtotime($order_date.$order_time);
		$pay_time = date('Y-m-d H:i:s', $tmp);
	
		if (empty($order_no)) {
			exit();
		}
	
		//回调函数
		$callback = function () use($order_no, $trade_no, $trade_status, $pay_time){
			if ($trade_status == UMS_ORDER_STATUS_SUCCESS) {
				$transaction = Yii::app()->db->beginTransaction(); //开启事务
				try {
					//订单判断
					$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
					if ($is_cz) { //储值订单
						$order = new MemberStoredC();
					}else { //收银订单
						$order = new OrderSC();
					}
						
					$ret = $order->orderPaySuccess($order_no, $pay_time, $trade_no, null);
					if (!empty($ret) && $ret['status'] == ERROR_NONE) {
						//订单修改成功
						$transaction->commit(); //数据提交
						return true;
					}else {
						throw new Exception('订单修改失败');
					}
				} catch (Exception $e) {
					$transaction->rollback(); //数据回滚
					$msg = $e->getMessage();
					exit() ;
				}
			}
		};
	
		$api = new UmspaySC();
		$api->umspayVerifyNotify($order_no, $data, $callback);
	}
	
}