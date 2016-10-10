<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class WxpayC extends mainClass{
        
        
        /**
	 * 调用微信JS支付接口
	 */
    private function WxjsPayApi($order_no, $money, $appid, $mchid, $key,$appsecret, $type, $openId,$name,$notify_url) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php'; //引入自定义配置文件
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['APPSECRET'] = $appsecret;
		}
		//日志记录
		//TODO
		
		//数据封装类
		$input = new WxPayUnifiedOrder();
		//设置商户信息配置信息
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			//$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		//设置商品描述
		$input->SetBody($name);
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
		if(!empty($notify_url)){
		    $input->SetNotify_url($notify_url);
		}else{
		    $input->SetNotify_url(WXPAY_SYT_ASYNOTIFY);
		}
		
		//设置交易类型：JSAPI，NATIVE，APP
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		//$result = $notify->GetPayUrl($input);
		
		//接口类
		$api = new WxPayApi();
		//设置商户信息配置信息
		$api->wxpay_config = $wxpay_config;
		//调用统一下单接口
		$result = $api->unifiedOrder($input);
		
		return $result;
	}
        
	public function WxJsPay($order_no,$name,$merchant_id = '',$notify_url='', $openId=NULL){
    	Yii::import('application.extensions.wxpay.*');
    	require_once "lib/WxPay.Api.php";
    	require_once "example/WxPay.JsApiPay.php";
    	require_once 'log.php';
    	require 'wxpay.custom.php'; //引入自定义配置文件
    	$tools = new JsApiPay();
    	$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			//订单判断
 			$is_cz = strpos($order_no, STORED_ORDER_PREFIX) === 0 ? true : false;
			if ($is_cz) { //储值订单查询
				$cmd->select('o.stored_id, o.num, o.user_code, s.stored_money, s.merchant_id'); //查询字段
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
				$merchant_id = $model['merchant_id']; //商户id
				//查询商户信息
				$merchant = Merchant::model()->findByPk($merchant_id);
				if (empty($merchant)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('订单对应商户不存在');
				}
				$appid = $merchant['wechat_appid'];
				$mchid = $merchant['wechat_mchid'];
				$t_appid = $merchant['t_wx_appid'];
				$t_mchid = $merchant['t_wx_mchid'];
				$key = $merchant['wechat_key'];
				$appsecret = $merchant['wechat_appsecret'];
				$merchant_type = $merchant['wxpay_merchant_type'];
				
				$money = $model['stored_money'] * $model['num'];
                
			}else { 
				//商城订单
				if(strstr($order_no, 'SC')){
					$model = Order::model() -> find('order_no =:order_no and flag =:flag',array(
							':order_no' => $order_no,
							':flag' => FLAG_NO
					));
					$merchant = Merchant::model() -> findByPk($merchant_id);
					if(empty($model)){
						$result['status'] = ERROR_NO_DATA;
						throw new Exception('订单不存在');
					}
					$merchant_id = $model['merchant_id']; //商户id
					//查询商户信息
					$merchant = Merchant::model()->findByPk($merchant_id);
					if (empty($merchant)) {
						$result['status'] = ERROR_NO_DATA;
						throw new Exception('订单对应商户不存在');
					}
					$appid = $merchant['wechat_appid'];
					$mchid = $merchant['wechat_mchid'];
					$t_appid = $merchant['t_wx_appid'];
					$t_mchid = $merchant['t_wx_mchid'];
					$key = $merchant['wechat_key'];
					$appsecret = $merchant['wechat_appsecret'];
					$merchant_type = $merchant['wxpay_merchant_type'];
				
					$money = $model['order_paymoney'] - $model['coupons_money'];
					
				}else{
					//订单查询
					$cmd->select('o.id, o.merchant_id, o.store_id, o.online_paymoney money'); //查询字段
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
					$merchant_id = $model['merchant_id']; //商户id
					//查询商户信息
					$merchant = Merchant::model()->findByPk($merchant_id);
					if (empty($merchant)) {
						$result['status'] = ERROR_NO_DATA;
						throw new Exception('订单对应商户不存在');
					}
					$appid = $merchant['wechat_appid'];
					$mchid = $merchant['wechat_mchid'];
					$t_appid = $merchant['t_wx_appid'];
					$t_mchid = $merchant['t_wx_mchid'];
					$key = $merchant['wechat_key'];
					$appsecret = $merchant['wechat_appsecret'];
					$merchant_type = $merchant['wxpay_merchant_type'];
					
					$money = $model['money'];
				}
			}

			if ($merchant_type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
				//商户信息配置
				$wxpay_config['APPID'] = $appid;
				$wxpay_config['MCHID'] = $mchid;
				$wxpay_config['KEY'] = $key;
				$wxpay_config['APPSECRET'] = $appsecret;
			}else {//特约商户
				$appid = $t_appid;
				$mchid = $t_mchid;
			}
			$tools -> wxpay_config = $wxpay_config;
         	$user_id = Yii::app()->session['user_id'];
		    if ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
         		$openId = $tools->GetOpenid();
         	}
			//请求微信支付接口
			$response = $this->WxjsPayApi($order_no, $money, $appid, $mchid, $key,$appsecret,$merchant_type,$openId,$name,$notify_url);
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			
			//获取jsapi支付的参数
			$result['data'] = $tools->GetJsApiParameters($response);
			$result['status'] = ERROR_NONE;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}		
		return json_encode($result);
   	}
   	
   	//获取wechat_key
   	public function getkey($out_trade_no){
   		$result = array();
   		try {
            $order = Order::model()->find('order_no=:order_no and flag=:flag',array(
            		':order_no'=>$out_trade_no,
            		':flag'=>FLAG_NO
            ));
   			if(!empty($order)) {
         		$merchant = Merchant::model() -> find('id =:id and flag =:flag',array(
                       	':id' => $order['merchant_id'],
                       	':flag' => FLAG_NO
              	));
				if (!empty($merchant)){
                  	$result['data'] = $merchant -> wechat_key;
                  	$result['wxpay_merchant_type'] = $merchant -> wxpay_merchant_type;
                 	$result['status'] = ERROR_NONE; //状态码
                  	$result['errMsg'] = ''; //错误信息
             	}else {
                  	$result['status'] = ERROR_NO_DATA; //状态码
                  	$result['errMsg'] = '数据保存失败'; //错误信息
              	}
           	} else {
   				$result['status'] = ERROR_NO_DATA; //状态码
   				$result['errMsg'] = '订单不存在'; //错误信息
   			}
   				
   		} catch (Exception $e) {
   			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
   			$result['errMsg'] = $e->getMessage(); //错误信息
   		}
   	
   		return json_encode($result);
   	}
   	
   	
   	//获取wechat_key(储值活动)
   	public function getkeyForStoredOrder($out_trade_no){
   	    $result = array();
   	    try {
   	        $order = StoredOrder::model()->find('order_no=:order_no and flag=:flag',array(
   	            ':order_no'=>$out_trade_no,
   	            ':flag'=>FLAG_NO
   	        ));
   	        if(!empty($order)) {
   	            $stored = Stored::model() -> findByPk($order -> stored_id);
   	            
   	            $merchant = Merchant::model() -> find('id =:id and flag =:flag',array(
   	                ':id' => $stored['merchant_id'],
   	                ':flag' => FLAG_NO
   	            ));
   	            if (!empty($merchant)){
   	                $result['data'] = $merchant -> wechat_key;
   	                $result['wxpay_merchant_type'] = $merchant -> wxpay_merchant_type;
   	                $result['status'] = ERROR_NONE; //状态码
   	                $result['errMsg'] = ''; //错误信息
   	            }else {
   	                $result['status'] = ERROR_NO_DATA; //状态码
   	                $result['errMsg'] = '数据保存失败'; //错误信息
   	            }
   	        } else {
   	            $result['status'] = ERROR_NO_DATA; //状态码
   	            $result['errMsg'] = '订单不存在'; //错误信息
   	        }
   	        	
   	    } catch (Exception $e) {
   	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
   	        $result['errMsg'] = $e->getMessage(); //错误信息
   	    }
   	
   	    return json_encode($result);
   	}
   	
   	
   	public function getOpenId($merchant_id) {
   		Yii::import('application.extensions.wxpay.*');
   		require_once "lib/WxPay.Api.php";
   		require_once "example/WxPay.JsApiPay.php";
   		require_once 'log.php';
   		require 'wxpay.custom.php'; //引入自定义配置文件
   		
   		$tools = new JsApiPay();
   		
   		$result = array();
   		try {
   			if (empty($merchant_id)) {
   				$result['status'] = ERROR_PARAMETER_MISS;
   				throw new Exception('缺少参数');
   			}
   			$merchant = Merchant::model()->findByPk($merchant_id);
   			if (empty($merchant)) {
   				$result['status'] = ERROR_NO_DATA;
   				throw new Exception('商户不存在');
   			}
   			$appid = $merchant['wechat_appid'];
   			$mchid = $merchant['wechat_mchid'];
   			$key = $merchant['wechat_key'];
   			$appsecret = $merchant['wechat_appsecret'];
   			$merchant_type = $merchant['wxpay_merchant_type'];
   			if ($merchant_type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
   				//商户信息配置
   				$wxpay_config['APPID'] = $appid;
   				$wxpay_config['MCHID'] = $mchid;
   				$wxpay_config['KEY'] = $key;
   				$wxpay_config['APPSECRET'] = $appsecret;
   			}
   			$tools -> wxpay_config = $wxpay_config;
   			$open_id = $tools->GetOpenid();

   			$result['data'] = $open_id;
   			$result['status'] = ERROR_NONE;
   			
   		} catch (Exception $e) {
   			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
   			$result['errMsg'] = $e->getMessage(); //错误信息
   		}
   		
   		return json_encode($result);
   	}

	/**
	 * @param $order_no   订单编号
	 * @param $name
	 * @param string $merchant_id  商户id
	 * @param string $notify_url   支付异步通知地址
	 * @param null $openId         用户微信openid
	 * @return string
	 * JS 微信支付
	 */
	public function NewWxJsPay($order_no,$name,$merchant_id = '',$notify_url='', $openId=NULL, $money) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once "example/WxPay.JsApiPay.php";
		require_once 'log.php';
		require 'wxpay.custom.php'; //引入自定义配置文件
		$tools = new JsApiPay();
		$result = array();
		try {
			//查询商户信息
			$merchant = Merchant::model()->findByPk($merchant_id);
			if (empty($merchant)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('订单对应商户不存在');
			}
			$appid = $merchant['wechat_appid'];
			$mchid = $merchant['wechat_mchid'];
			$t_appid = $merchant['t_wx_appid'];
			$t_mchid = $merchant['t_wx_mchid'];
			$key = $merchant['wechat_key'];
			$appsecret = $merchant['wechat_appsecret'];
			$merchant_type = $merchant['wxpay_merchant_type'];
			//自助商户
			if ($merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
				//商户信息配置
				$wxpay_config['APPID'] = $appid;
				$wxpay_config['MCHID'] = $mchid;
				$wxpay_config['KEY'] = $key;
				$wxpay_config['APPSECRET'] = $appsecret;
			}else {//特约商户
				$appid = $t_appid;
				$mchid = $t_mchid;
			}
			$tools -> wxpay_config = $wxpay_config;
			if ($merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
				$openId = $tools->GetOpenid();
			}
			//请求微信支付接口
			$response = $this->WxjsPayApi($order_no, $money, $appid, $mchid, $key,$appsecret,$merchant_type,$openId,$name,$notify_url);
			if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}

			//获取jsapi支付的参数
			$result['data'] = $tools->GetJsApiParameters($response);
			$result['status'] = ERROR_NONE;

		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}

}

