<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class WxpaySC extends mainClass{
	/*******************************微信接口调用方法**********************************/
	/**
	 * 调用微信条码(刷卡)支付接口
	 * @param unknown $order_no
	 * @param unknown $money
	 * @param unknown $user_code
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return multitype:
	 */
	private function barCodeApi($order_no, $money, $user_code, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php'; //引入自定义配置文件
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//日志记录
		//TODO
		
		//数据封装类
		$input = new WxPayMicroPay();
		//设置商户信息配置信息
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		$input->SetAuth_code($user_code);
		//设置商品描述
		$input->SetBody("条码支付");
		//设置商品详细
		//$input->SetDetail("条码支付");
		//设置附加数据
		$input->SetAttach("无");
		//设置订单号
		$input->SetOut_trade_no($order_no);
		//设置订单金额，单位分
		$input->SetTotal_fee($money * 100);
		//设置商品标识，针对代金券或立减优惠功能
		$input->SetGoods_tag("normal");
		
		//接口类
		$api = new WxPayApi();
		//设置商户信息配置信息
		$api->wxpay_config = $wxpay_config;
		//调用统一下单接口
		$result = $api->micropay($input);
		
		return $result;
	}
	
	/**
	 * 调用微信扫码支付接口
	 * @param unknown $order_no
	 * @param unknown $money
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
	private function qrCodeApi($order_no, $money, $appid, $mchid, $key, $type, $cert_path, $key_path, $notify_url=WXPAY_SYT_ASYNOTIFY) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php'; //引入自定义配置文件
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//日志记录
		//TODO
		
		//数据封装类
		$input = new WxPayUnifiedOrder();
		//设置商户信息配置信息
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		//设置商品描述
		$input->SetBody("扫码支付");
		//设置商品详细
		//$input->SetDetail("扫码支付");
		//设置附加数据
		$input->SetAttach("无");
		//设置订单号
		$input->SetOut_trade_no($order_no);
		//设置订单金额，单位分
		$input->SetTotal_fee($money * 100);
		//设置订单生成时间
		$input->SetTime_start(date("YmdHis"));
		//设置订单失效时间
		$input->SetTime_expire(date("YmdHis", time() + 600));
		//设置商品标识，针对代金券或立减优惠功能
		$input->SetGoods_tag("normal");
		//回调地址
		$input->SetNotify_url($notify_url);
		//设置交易类型：JSAPI，NATIVE，APP
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($order_no);
		//$result = $notify->GetPayUrl($input);
		
		//接口类
		$api = new WxPayApi();
		//设置商户信息配置信息
		$api->wxpay_config = $wxpay_config;
		//调用统一下单接口
		$result = $api->unifiedOrder($input);
		
		return $result;
	}
	
	/**
	 * 扫码支付模式一：组装商品id，生成二维码的url
	 * @param unknown $product_id
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return string
	 */
	private function prePayUrlApi($product_id, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php'; //引入自定义配置文件
	
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
	
		//日志记录
		//TODO
	
		//数据封装类
		$input = new WxPayBizPayUrl();
		//设置商户信息配置信息
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		//设置商品id
		$input->SetProduct_id($product_id);
	
		//接口类
		$api = new WxPayApi();
		//设置商户信息配置信息
		$api->wxpay_config = $wxpay_config;
		//生成二维码
		$values = $api->bizpayurl($input);
		//拼接地址
		$result = 'weixin://wxpay/bizpayurl?';
		foreach ($values as $k => $v) {
			$result .= $k . "=" . $v . "&";
		}
		$result = trim($result, "&");
		
		return $result;
	}
	
	/**
	 * 订单查询接口
	 * @param unknown $order_no
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
	private function searchApi($order_no, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//日志记录
		//TODO
		
		//数据封装类
		$input = new WxPayOrderQuery();
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		$input->SetOut_trade_no($order_no);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用订单查询接口
		$result = $api->orderQuery($input);
		
		return $result;
	}
	
	/**
	 * 退款查询接口
	 * @param unknown $refund_no
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
	private function refundQueryApi($refund_no, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//日志记录
		//TODO
		
		//数据封装类
		$input = new WxPayRefundQuery();
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			//$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		$input->SetOut_refund_no($refund_no);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用订单查询接口
		$result = $api->refundQuery($input);
		
		return $result;
	}
	
	/**
	 * 调用微信收单撤销接口（商户可撤销已经存在的交易，7天以内的交易单可调用撤销）
	 * @param unknown $order_no
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return multitype:
	 */
	private function cancelApi($order_no, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//数据封装类
		$input = new WxPayReverse();
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			//$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		$input->SetOut_trade_no($order_no);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用撤销订单接口
		$result = $api->reverse($input);
		
		return $result;
	}
	
	/**
	 * 调用微信收单关闭接口（商户可对已经存在且交易状态为待付款的的交易进行关闭）
	 * 订单生成后不能马上调用关单接口，最短调用时间间隔为5分钟
	 * @param unknown $order_no
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
	private function closeApi($order_no, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//数据封装类
		$input = new WxPayCloseOrder();
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			//$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		$input->SetOut_trade_no($order_no);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用关闭订单接口
		$result = $api->closeOrder($input);
		
		return $result;
	}
	
	/**
	 * 调用微信收单退款接口（商户可对交易进行退款）
	 * @param unknown $order_no
	 * @param unknown $rf_order_no
	 * @param unknown $total_money
	 * @param unknown $refund_money
	 * @param unknown $op_account
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
	private function refundApi($order_no, $rf_order_no, $total_money, $refund_money, $op_account, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//数据封装类
		$input = new WxPayRefund();
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			//$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		//订单号
		$input->SetOut_trade_no($order_no);
		//订单总金额
		$input->SetTotal_fee($total_money * 100);
		//退款金额
		$input->SetRefund_fee($refund_money * 100);
		//退款订单号
		$input->SetOut_refund_no($rf_order_no);
		//操作员账号，可为商户号MCHID
		$input->SetOp_user_id($op_account);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用退款接口
		$result = $api->refund($input);
		
		return $result;
	}
	
	/**
	 * 通知请求验证
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <boolean, multitype:>
	 */
	private function verifyNotify($appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
	
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
	
		//日志记录
// 		error_reporting(E_ERROR);
// 		//初始化日志
// 		$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
// 		$log = Log::Init($logHandler, 15);
		
		$msg = "OK";
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		$result = $api->notify(null, $msg);
		
		return array('result' => $result, 'msg' => $msg, 'type' => $type);
	}
	
	/**
	 * 通知回复
	 * @param unknown $result
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $needSign
	 */
	private function reply($result, $msg, $appid, $mchid, $key, $type, $needSign=TRUE) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
		}
		
		//数据类
		$notify = new WxPayNotifyReply();
		$notify->wxpay_config = $wxpay_config;
		
		if($result == true){
			$notify->SetReturn_code(_SUCCESS);
			$notify->SetReturn_msg($msg);
		} else {
			$notify->SetReturn_code(_FAIL);
			$notify->SetReturn_msg($msg);
		}
		
		
		//如果需要签名
		if($needSign == true && 
			$notify->GetReturn_code() == _SUCCESS)
		{
			$notify->SetSign();
		}
		WxpayApi::replyNotify($notify->ToXml());
	}
	
	/**
	 * 预支付交易会话标识回复
	 * @param unknown $result
	 * @param unknown $prepay_id
	 * @param unknown $msg
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param string $needSign
	 */
	private function prePayReply($result, $prepay_id, $msg, $appid, $mchid, $key, $type, $needSign=TRUE) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
	
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
		}
	
		//数据类
		$notify = new WxPayNotifyReply();
		$notify->wxpay_config = $wxpay_config;
	
		if($result == true){
			$notify->SetReturn_code(_SUCCESS);
			$notify->SetData("appid", $appid);
			$notify->SetData("mch_id", $mchid);
			$notify->SetData("nonce_str", WxPayApi::getNonceStr());
			$notify->SetData("prepay_id", $prepay_id);
			$notify->SetData("result_code", "SUCCESS");
			$notify->SetData("err_code_des", "OK");
		} else {
			$notify->SetReturn_code(_FAIL);
			$notify->SetReturn_msg($msg);
		}
	
	
		//如果需要签名
		if($needSign == true &&
		$notify->GetReturn_code() == _SUCCESS)
		{
			$notify->SetSign();
		}
		WxpayApi::replyNotify($notify->ToXml());
	}
	
	/**
	 * 获取数组下标的数据
	 * @param unknown $key
	 * @param unknown $array
	 * @return Ambigous <string, unknown>
	 */
	private function getArrayVal($key, $array) {
		$result   = '';
		if ($array && isset($array[$key])) {
			$result = $array[$key];
		}
		return $result;
	}
	
	/**************************类公开方法*****************************/
	
	/**
	 * 条码支付
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function barcodePay($order_no) {
		$result = array();
		$transaction = Yii::app()->db->beginTransaction(); //开启事务
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) { //储值订单查询
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$money = $model['stored_money'] * $model['num'];
				$user_code = $model['user_code'];
				
				$response = $this->barCodeApi($order_no, $money, $user_code, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				if (!$response || $response['return_code'] == _FAIL) {
					$result['status'] = ERROR_EXCEPTION;
					$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
					throw new Exception($msg);
				}
				//返回请求结果
				$result_code = $this->getArrayVal('result_code', $response); //业务结果码
				$trade_status = $this->getArrayVal('trade_state', $response); //交易状态
				$trade_no = $this->getArrayVal('transaction_id', $response); //微信支付订单号
				$open_id = $this->getArrayVal('openid', $response); //微信用户标识
				$sub_open_id = $this->getArrayVal('sub_openid', $response); //用户子标识
				$is_subscribe = $this->getArrayVal('is_subscribe', $response); //是否关注
				$buyer_user_id = '';//买家支付宝用户号
				$send_pay_date = $this->getArrayVal('time_end', $response);//订单支付时间
				$buyer_logon_id = '';//买家支付宝账号
				$err_code = $this->getArrayVal('err_code', $response); //错误代码
				$err_code_des = $this->getArrayVal('err_code_des', $response); //错误代码描述
				
				//openid处理
				if ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
					$wxpay_openid = $sub_open_id;
					$wxpay_p_openid = $open_id;
				}else {
					$wxpay_openid = $open_id;
					$wxpay_p_openid = NULL;
				}
				
				//下单成功并且支付成功
				if ($result_code == _SUCCESS) {
					//修改订单
					$order = new MemberStoredC();
					$ret = $order->orderPaySuccess($order_no, $send_pay_date, $trade_no, $buyer_logon_id, NULL, NULL, NULL, $wxpay_openid, $wxpay_p_openid);
					if (empty($ret)) {
						$result['status'] = ERROR_EXCEPTION;
						throw new Exception('订单修改失败');
					}
					if ($ret['status'] == ERROR_NONE) {
						//订单修改成功
						$result['status'] = ERROR_NONE;
						$result['pay'] = 'done';
					}
				}elseif ($err_code == 'SYSTEMERROR' || $err_code == 'BANKERROR' || $err_code == 'USERPAYING') {
					//等待处理中
					$result['status'] = ERROR_NONE;
					$result['pay'] = 'wait';
				}else {
					$result['status'] = ERROR_REQUEST_FAIL;
					$err_code_des = !empty($err_code_des) ? $err_code_des : '未知错误';
					throw new Exception($err_code_des);
				}
			}else { //订单查询
				$cmd->select('o.id, o.store_id, o.user_code, o.online_paymoney money, o.stored_confirm_status'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$money = $model['money'];
				$user_code = $model['user_code'];
				
				//支付通道保存
				$pay_channel = ORDER_PAY_PASSAGEWAY_NULL;
				if ($merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
					$pay_channel = ORDER_PAY_PASSAGEWAY_WECHAT1;
				}elseif ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
					$pay_channel = ORDER_PAY_PASSAGEWAY_WECHAT2;
				}
				$update = Order::model()->updateAll(array('pay_passageway' => $pay_channel), 'order_no = :order_no', array(':order_no' => $order_no));
				
				$response = $this->barCodeApi($order_no, $money, $user_code, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				if (!$response || $response['return_code'] == _FAIL) {
					$result['status'] = ERROR_EXCEPTION;
					$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
					throw new Exception($msg);
				}
				
				//返回请求结果
				$result_code = $this->getArrayVal('result_code', $response); //业务结果码
				$trade_status = $this->getArrayVal('trade_state', $response); //交易状态
				$trade_no = $this->getArrayVal('transaction_id', $response); //微信支付订单号
				$open_id = $this->getArrayVal('openid', $response); //微信用户标识
				$sub_open_id = $this->getArrayVal('sub_openid', $response); //用户子标识
				$is_subscribe = $this->getArrayVal('is_subscribe', $response); //是否关注
				$buyer_user_id = '';//买家支付宝用户号
				$send_pay_date = $this->getArrayVal('time_end', $response);//订单支付时间
				$buyer_logon_id = '';//买家支付宝账号
				$err_code = $this->getArrayVal('err_code', $response); //错误代码
				$err_code_des = $this->getArrayVal('err_code_des', $response); //错误代码描述
				
				//openid处理
				if ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
					$wxpay_openid = $sub_open_id;
					$wxpay_p_openid = $open_id;
				}else {
					$wxpay_openid = $open_id;
					$wxpay_p_openid = NULL;
				}
				
				//下单成功并且支付成功
				if ($result_code == _SUCCESS && $model['stored_confirm_status'] != ORDER_PAY_WAITFORCONFIRM) {
					//修改订单
					$order = new OrderSC();
					$ret = $order->orderPaySuccess($order_no, $send_pay_date, $trade_no, $buyer_logon_id, NULL, NULL, NULL, $wxpay_openid, $wxpay_p_openid);
					if (empty($ret)) {
						$result['status'] = ERROR_EXCEPTION;
						throw new Exception('订单修改失败');
					}
					if ($ret['status'] == ERROR_NONE) {
						//订单修改成功
						$result['status'] = ERROR_NONE;
						$result['pay'] = 'done';
					}
				}elseif ($result_code == _SUCCESS && $model['stored_confirm_status'] == ORDER_PAY_WAITFORCONFIRM) {
					//等待用户储值支付确认
					$result['status'] = ERROR_NONE;
					$result['pay'] = 'wait';
				}elseif ($err_code == 'SYSTEMERROR' || $err_code == 'BANKERROR' || $err_code == 'USERPAYING') {
					//等待处理中
					$result['status'] = ERROR_NONE;
					$result['pay'] = 'wait';
				}else {
					$result['status'] = ERROR_REQUEST_FAIL;
					$err_code_des = !empty($err_code_des) ? $err_code_des : '未知错误';
					throw new Exception($err_code_des);
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$money = $model['stored_money'] * $model['num'];
			}else { //订单查询
				$cmd->select('o.id, o.store_id, o.user_code, o.online_paymoney money'); //查询字段
				$cmd->from(array('wq_order o')); //查询表名
				$cmd->where(array(
						'AND',  //and操作
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$money = $model['money'];
			}
			
			if (!$is_cz) {
				//支付通道保存
				$pay_channel = ORDER_PAY_PASSAGEWAY_NULL;
				if ($merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
					$pay_channel = ORDER_PAY_PASSAGEWAY_WECHAT1;
				}elseif ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
					$pay_channel = ORDER_PAY_PASSAGEWAY_WECHAT2;
				}
				$update = Order::model()->updateAll(array('pay_passageway' => $pay_channel), 'order_no = :order_no', array(':order_no' => $order_no));
			}
				
			//请求微信接口
			$response = $this->qrCodeApi($order_no, $money, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			//提取请求数据
			$result_code = $this->getArrayVal('result_code', $response); //业务结果码
			$code = $this->getArrayVal('code_url', $response); //二维码
			//失败时有下列字段
			$err_code = $this->getArrayVal('err_code', $response); //错误代码
			$err_code_des = $this->getArrayVal('err_code_des', $response); //错误代码描述
			
			//成功
			if ($result_code == _SUCCESS) {
				//返回二维码图片地址
				$result['status'] = ERROR_NONE;
				//使用微信的接口得到二维码图片地址
				$result['data'] = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.$code;
				$result['code'] = $code;
			}
			//失败
			if ($result_code == _FAIL) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($err_code_des);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 微信扫码模式一
	 * @throws Exception
	 * @return string
	 */
	public function qrCodePay1() {
		$result = array();
		try {
			//生成随机订单号
			$order_no = 'T'.date('Ymd', time()).mt_rand(10000000, 99999999);
			$money = '5';
			//测试使用，支付信息暂时固定
			$appid = 'wxec84afe11d9da7c4';//'wx4ba03ea9716def82';
			$mchid = '1261382301';//'1252465301';
			$key = 'b0d7efdfb5fb925db13b2055dffs55dd';//'506d0f39ac342fbccb8a77759d49fe4d';
			$merchant_type = WXPAY_MERCHANT_TYPE_SELF;
			$cert_path = '';
			$key_path = '';
			
			$notify_url = SYT_DOMAIN.'/run/payNotify';
		
			//请求微信接口
			$response = $this->qrCodeApi($order_no, $money, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path, $notify_url);
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			//提取请求数据
			$result_code = $this->getArrayVal('result_code', $response); //业务结果码
			$code = $this->getArrayVal('code_url', $response); //二维码
			$prepay_id = $this->getArrayVal('prepay_id', $response); //预支付交易会话标识
			//失败时有下列字段
			$err_code = $this->getArrayVal('err_code', $response); //错误代码
			$err_code_des = $this->getArrayVal('err_code_des', $response); //错误代码描述
				
			//成功
			if ($result_code == _SUCCESS) {
				//返回二维码图片地址
				$result['status'] = ERROR_NONE;
				//使用微信的接口得到二维码图片地址
				$result['data'] = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.$code;
				$result['prepay_id'] = $prepay_id;
				$result['code'] = $code;
			}
			//失败
			if ($result_code == _FAIL) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($err_code_des);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 二维码地址
	 * @param unknown $product_id
	 * @return string
	 */
	public function prePayUrl($product_id) {
		$result = array();
		try {
			//测试使用，支付信息暂时固定
			$appid = 'wxec84afe11d9da7c4';//'wx4ba03ea9716def82';
			$mchid = '1261382301';//'1252465301';
			$key = 'b0d7efdfb5fb925db13b2055dffs55dd';//'506d0f39ac342fbccb8a77759d49fe4d';
			$merchant_type = WXPAY_MERCHANT_TYPE_SELF;
			$cert_path = '';
			$key_path = '';
			
			$response = $this->prePayUrlApi($product_id, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			
			$result['url'] = $response;
			$result['status'] = ERROR_NONE;
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 微信退款申请，申请成功后需通过退款查询接口查询
	 * @param unknown $refund_order_no
	 * @param unknown $op_account
	 * @throws Exception
	 * @return string
	 */
	public function wxpayRefund($refund_order_no, $op_account) {
		$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			$cmd->select('r.refund_money, o.order_no, o.store_id, o.online_paymoney'); //查询字段
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
			$ret = $store->getWxpaySellerInfo($store_id);
			$seller_info = json_decode($ret, true);
			if ($seller_info['status'] != ERROR_NONE) {
				$result['status'] = $seller_info['status'];
				throw new Exception($seller_info['errMsg']);
			}
			$info = $seller_info['data'];
			$appid = $info['wxpay_appid'];
			$mchid = $info['wxpay_mchid'];
			$key = $info['wxpay_api_key'];
			$merchant_type = $info['wxpay_merchant_type'];
			$cert_path = $info['wxpay_apiclient_cert'];
			$key_path = $info['wxpay_apiclient_key'];
			
			$refund_money = $model['refund_money'];
			$order_no = $model['order_no'];
			$rf_order_no = $refund_order_no;
			$total_money = $model['online_paymoney'];
			 
			//微信接口请求
			$response = $this->refundApi($order_no, $rf_order_no, $total_money, $refund_money, $op_account, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			//返回请求结果
			$result_code = $this->getArrayVal('result_code', $response); //业务结果码
			$err_code = $this->getArrayVal('err_code', $response); //错误码
			$err_code_des = $this->getArrayVal('err_code_des', $response); //错误描述
			$transaction_id = $this->getArrayVal('transaction_id', $response); //微信订单号
			$refund_id = $this->getArrayVal('refund_id', $response); //微信退款单号
			$refund_channel = $this->getArrayVal('refund_channel', $response); //退款渠道
			
			//申请成功
			if ($result_code == _SUCCESS) {
				$result['status'] = ERROR_NONE;
				$result['trade_no'] = $refund_id;
// 				$result['trade_no'] = $trade_no;
// 				$result['refund_time'] = $gmt_refund_pay;
			}
			//失败
			if ($result_code == _FAIL) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($err_code_des);
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
	public function wxpayRevoke($order_no) {
		$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			//订单判断
			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) {  //储值订单
				$cmd->select('o.order_no, o.store_id, o.pay_channel, o.stored_id, o.num'); //查询字段
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$order_no = $model['order_no'];
				
				//查询储值活动
				$stored = Stored::model()->findByPk($model['stored_id']);
				//计算储值订单的订单金额
				$refund_money = $model['num'] * $stored['stored_money'];
			}else {
				$cmd->select('o.order_no, o.store_id, o.pay_channel, o.online_paymoney'); //查询字段
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$order_no = $model['order_no'];
				
				//计算订单的线上支付金额
				$refund_money = $model['online_paymoney'];
			}
				
			//判断支付方式
			if ($model['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM) { //微信条码支付
				//微信撤单接口请求
				$response = $this->cancelApi($order_no, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			}
			if ($model['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM) { //微信扫码支付
				//调用微信查询订单接口，查询订单支付状态
				$search_response = $this->searchApi($order_no, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				if (!$search_response || $search_response['return_code'] == _FAIL) {
					$result['status'] = ERROR_EXCEPTION;
					$msg = isset($search_response['return_msg']) ? $search_response['return_msg'] : '接口请求失败';
					throw new Exception($msg);
				}
				//返回请求结果
				$result_code1 = $this->getArrayVal('result_code', $search_response); //业务结果码
				$trade_status1 = $this->getArrayVal('trade_state', $search_response); //交易状态
				//微信订单支付状态判断
				if ($trade_status1 == _SUCCESS) {
					//已支付的订单调用微信退款接口
					$response = $this->refundApi($order_no, $order_no, $refund_money, $refund_money, $mchid, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				}else {
					//未支付的订单调用微信关闭订单接口
					$response = $this->closeApi($order_no, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				}
			}
			
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			//返回请求结果
			$result_code = $this->getArrayVal('result_code', $response); //业务结果码
			$err_code = $this->getArrayVal('err_code', $response); //错误码
			$err_code_des = $this->getArrayVal('err_code_des', $response); //错误描述
			$recall = $this->getArrayVal('recall', $response); //是否需要重试
				
			//成功
			if ($result_code == _SUCCESS) {
				$result['status'] = ERROR_NONE;
				$result['trade_no'] = '';
			}
			//失败
			if ($result_code == _FAIL) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($err_code_des);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 订单查询
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function wxpaySearch($order_no) {
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$order_no = $model['order_no'];
		
				$response = $this->searchApi($order_no, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				if (!$response || $response['return_code'] == _FAIL) {
					$result['status'] = ERROR_EXCEPTION;
					$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
					throw new Exception($msg);
				}
				//返回请求结果
				$result_code = $this->getArrayVal('result_code', $response); //业务结果码
				$trade_status = $this->getArrayVal('trade_state', $response); //交易状态
				$trade_no = $this->getArrayVal('transaction_id', $response); //微信支付订单号
				$open_id = $this->getArrayVal('openid', $response); //微信用户标识
				$sub_open_id = $this->getArrayVal('sub_openid', $response); //用户子标识
				$is_subscribe = $this->getArrayVal('is_subscribe', $response); //是否关注
				$buyer_user_id = '';//买家支付宝用户号
				$send_pay_date = $this->getArrayVal('time_end', $response);//订单支付时间
				$buyer_logon_id = '';//买家支付宝账号
				$err_code = $this->getArrayVal('err_code', $response); //错误代码
				$err_code_des = $this->getArrayVal('err_code_des', $response); //错误代码描述
		
				//openid处理
				if ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
					$wxpay_openid = $sub_open_id;
					$wxpay_p_openid = $open_id;
				}else {
					$wxpay_openid = $open_id;
					$wxpay_p_openid = NULL;
				}
				
				//成功
				if ($result_code == _SUCCESS) {
					
					
					if ($model['pay_status'] == ORDER_STATUS_UNPAID && $trade_status == 'SUCCESS') {
						
						//修改订单
						$order = new MemberStoredC();
						$ret = $order->orderPaySuccess($order_no, $send_pay_date, $trade_no, $buyer_logon_id, NULL, NULL, NULL, $wxpay_openid, $wxpay_p_openid);
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
					$result['open_id'] = $open_id;
					$result['is_subscribe'] = $is_subscribe;
				}
				//失败
				if ($result_code == _FAIL) {
					
					if ($err_code == 'ORDERNOTEXIST') {
						$result['status'] = ERROR_NONE;
						$result['order_id'] = $model['id'];
					}else {
						$result['status'] = ERROR_REQUEST_FAIL;
						throw new Exception($err_code_des);
					}
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$order_no = $model['order_no'];
		
				$response = $this->searchApi($order_no, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
				if (!$response || $response['return_code'] == _FAIL) {
					$result['status'] = ERROR_EXCEPTION;
					$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
					throw new Exception($msg);
				}
				//返回请求结果
				$result_code = $this->getArrayVal('result_code', $response); //业务结果码
				$trade_status = $this->getArrayVal('trade_state', $response); //交易状态
				$trade_no = $this->getArrayVal('transaction_id', $response); //微信支付订单号
				$open_id = $this->getArrayVal('openid', $response); //微信用户标识
				$sub_open_id = $this->getArrayVal('sub_openid', $response); //用户子标识
				$is_subscribe = $this->getArrayVal('is_subscribe', $response); //是否关注
				$buyer_user_id = '';//买家支付宝用户号
				$send_pay_date = $this->getArrayVal('time_end', $response);//订单支付时间
				$buyer_logon_id = '';//买家支付宝账号
				$err_code = $this->getArrayVal('err_code', $response); //错误代码
				$err_code_des = $this->getArrayVal('err_code_des', $response); //错误代码描述
				
				//openid处理
				if ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
					$wxpay_openid = $sub_open_id;
					$wxpay_p_openid = $open_id;
				}else {
					$wxpay_openid = $open_id;
					$wxpay_p_openid = NULL;
				}
				
				//成功
				if ($result_code == _SUCCESS) {
					if ($model['pay_status'] == ORDER_STATUS_UNPAID &&
					$model['stored_confirm_status'] != ORDER_PAY_WAITFORCONFIRM &&
					$trade_status == _SUCCESS) {
						//修改订单
						$order = new OrderSC();
						$ret = $order->orderPaySuccess($order_no, $send_pay_date, $trade_no, $buyer_logon_id, NULL, NULL, NULL, $wxpay_openid, $wxpay_p_openid);
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
					$result['open_id'] = $open_id;
					$result['is_subscribe'] = $is_subscribe;
				}
				//失败或未知
				if ($result_code == _FAIL) {
					if ($err_code == 'ORDERNOTEXIST') {
						$result['status'] = ERROR_NONE;
						$result['order_id'] = $model['id'];
					}else {
						$result['status'] = ERROR_REQUEST_FAIL;
						throw new Exception($err_code_des);
					}
				}
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 微信退款查询
	 * @param unknown $refund_no
	 * @throws Exception
	 * @return string
	 */
	public function wxpayRefundQuery($refund_no) {
		$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			$cmd->select('r.refund_money, o.order_no, o.store_id, o.online_paymoney'); //查询字段
			$cmd->from(array('wq_order o', 'wq_refund_record r')); //查询表名
			$cmd->where(array(
					'AND',  //and操作
					'r.order_id = o.id',
					'r.refund_order_no = :refund_order_no',
			));
			//查询参数
			$cmd->params = array(
					':refund_order_no' => $refund_no
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
			$ret = $store->getWxpaySellerInfo($store_id);
			$seller_info = json_decode($ret, true);
			if ($seller_info['status'] != ERROR_NONE) {
				$result['status'] = $seller_info['status'];
				throw new Exception($seller_info['errMsg']);
			}
			$info = $seller_info['data'];
			$appid = $info['wxpay_appid'];
			$mchid = $info['wxpay_mchid'];
			$key = $info['wxpay_api_key'];
			$merchant_type = $info['wxpay_merchant_type'];
			$cert_path = $info['wxpay_apiclient_cert'];
			$key_path = $info['wxpay_apiclient_key'];
		
			//微信接口请求
			$response = $this->refundQueryApi($refund_no, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			//返回请求结果
			$result_code = $this->getArrayVal('result_code', $response); //业务结果码
			$err_code = $this->getArrayVal('err_code', $response); //错误码
			$err_code_des = $this->getArrayVal('err_code_des', $response); //错误描述
			$transaction_id = $this->getArrayVal('transaction_id', $response); //微信订单号
			$refund_id = $this->getArrayVal('refund_id_0', $response); //微信退款单号
			$refund_channel = $this->getArrayVal('refund_channel_0', $response); //退款渠道
			$refund_status = $this->getArrayVal('refund_status_0', $response); //退款状态
			
			//请求成功
			if ($result_code == _SUCCESS) {
				$result['status'] = ERROR_NONE;
				//是否退款成功
				if ($refund_status == _SUCCESS) { //退款成功
					$result['refund_status'] = 'success';
					$result['refund_no'] = $refund_id;
					//$result['refund_time'] = date('Y-m-d H:i:s');
				}elseif ($refund_status == _FAIL || $refund_status == 'NOTSURE') { //退款失败
					$result['refund_status'] = 'fail';
				}elseif ($refund_status == 'PROCESSING') { //处理中
					$result['refund_status'] = 'wait';
				}else {
					$result['refund_status'] = 'error'; //人工处理
				}
			}
			//失败
			if ($result_code == _FAIL) {
				if ($err_code == 'REFUNDNOTEXIST') { //微信因延迟原因可能会造成退款数据不存在
					$result['status'] = ERROR_NONE;
					$result['refund_status'] = 'wait';
				}else {
					$result['status'] = ERROR_REQUEST_FAIL;
					throw new Exception($err_code_des);
				}
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 微信支付异步通知验证
	 * @param unknown $order_no
	 * @throws Exception
	 * @return string
	 */
	public function wxpayVerifyNotify($order_no, $callback) {
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
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$pay_status = $model['pay_status'];
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
				$store_id = $model['store_id']; //门店id
				//获取收款信息
				$store = new StoreC();
				$ret = $store->getWxpaySellerInfo($store_id);
				$seller_info = json_decode($ret, true);
				if ($seller_info['status'] != ERROR_NONE) {
					$result['status'] = $seller_info['status'];
					throw new Exception($seller_info['errMsg']);
				}
				$info = $seller_info['data'];
				$appid = $info['wxpay_appid'];
				$mchid = $info['wxpay_mchid'];
				$key = $info['wxpay_api_key'];
				$merchant_type = $info['wxpay_merchant_type'];
				$cert_path = $info['wxpay_apiclient_cert'];
				$key_path = $info['wxpay_apiclient_key'];
				
				$pay_status = $model['pay_status'];
			}
				
			$response = $this->verifyNotify($appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			if (!$response || !$response['result']) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($response['msg']);
			}
			
			$flag = false;
			$msg = '';
			//回调函数
			if ($pay_status == ORDER_STATUS_UNPAID) {
				$return = call_user_func($callback);
				if (!$return || !isset($return['flag'])) {
					$result['status'] = ERROR_EXCEPTION;
					throw new Exception('回调失败');
				}
				$flag = $return['flag'];
				$msg = $return['msg'];
			}
			
			//响应通知
			$this->reply($flag, $msg, $appid, $mchid, $key, $merchant_type);
				
// 			$result['status'] = ERROR_NONE;
// 			$result['pay_status'] = $model['pay_status'];
// 			$result['errMsg'] = '';
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		//return json_encode($result);
	}
	
	/**
	 * 微信扫码支付模式一异步通知
	 * @param unknown $product_id
	 * @param unknown $callback
	 * @throws Exception
	 */
	public function wxpayNativeNotify($product_id, $callback) {
		$result = array();
		try {
			//测试使用，支付信息暂时固定
			$appid = 'wxec84afe11d9da7c4';//'wx4ba03ea9716def82';
			$mchid = '1261382301';//'1252465301';
			$key = 'b0d7efdfb5fb925db13b2055dffs55dd';//'506d0f39ac342fbccb8a77759d49fe4d';
			$merchant_type = WXPAY_MERCHANT_TYPE_SELF;
			$cert_path = '';
			$key_path = '';
		
			$response = $this->verifyNotify($appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			if (!$response || !$response['result']) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($response['msg']);
			}
				
			$flag = false;
			$msg = '';
			//回调函数
			$return = call_user_func($callback);
			if (!$return || !isset($return['flag'])) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('回调失败');
			}
			$flag = $return['flag'];
			$msg = $return['msg'];
			$prepay_id = $return['prepay_id'];
				
			//响应通知
			$this->prePayReply($flag, $prepay_id, $msg, $appid, $mchid, $key, $merchant_type);
		
			// 			$result['status'] = ERROR_NONE;
			// 			$result['pay_status'] = $model['pay_status'];
			// 			$result['errMsg'] = '';
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		//return json_encode($result);
	}
	
	/**
	 * 演示处理
	 * @param unknown $open_id
	 * @throws Exception
	 */
	public function wxpaySampleHandle($open_id) {
		$result = array();
		try {
			//测试使用，支付信息暂时固定
			$appid = 'wxec84afe11d9da7c4';//'wx4ba03ea9716def82';
			$mchid = '1261382301';//'1252465301';
			$key = 'b0d7efdfb5fb925db13b2055dffs55dd';//506d0f39ac342fbccb8a77759d49fe4d';
			$merchant_type = WXPAY_MERCHANT_TYPE_SELF;
			$cert_path = '';
			$key_path = '';
		
			$response = $this->verifyNotify($appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			if (!$response || !$response['result']) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($response['msg']);
			}
		
			//保存支付完成的用户openid
			$model = TempWechatOpenid::model()->find('openid = :openid and flag = :flag',
					array(':openid' => $open_id, ':flag' => FLAG_NO));
			if (empty($model)) {
				$model = new TempWechatOpenid();
				$model['openid'] = $open_id;
				$model['create_time'] = date('Y-m-d H:i:s');
				if (!$model->save()) {
					throw new Exception('数据保存失败');
				}
			}
		
			//响应通知
			$this->reply(TRUE, '', $appid, $mchid, $key, $merchant_type);
		
			// 			$result['status'] = ERROR_NONE;
			// 			$result['pay_status'] = $model['pay_status'];
			// 			$result['errMsg'] = '';
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 通知响应
	 * @param unknown $result
	 * @param unknown $msg
	 */
	public function notifyReply($result, $msg, $type) {
		//通知回复
		$this->reply($result, $msg, null, null, null, $type, false);
	}
	
}