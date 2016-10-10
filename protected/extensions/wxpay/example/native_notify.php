<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class NativeNotifyCallBack extends WxPayNotify
{
	public function unifiedorder($openId, $product_id)
	{
		require_once '../wxpay.custom.php'; //引入自定义配置文件
		
		//牛牛配置
		$wxpay_config['APPID'] = 'wx4ba03ea9716def82';
		$wxpay_config['MCHID'] = '1252465301';
		$wxpay_config['KEY'] = 'a50f4c15d282cfb807de37aa44d994b4';
		$wxpay_config['APPSECRET'] = '506d0f39ac342fbccb8a77759d49fe4d';
		
		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->wxpay_config = $wxpay_config;
		$input->SetBody("test");
		$input->SetAttach("test");
		$input->SetOut_trade_no($wxpay_config['MCHID'].date("YmdHis"));
		$input->SetTotal_fee("1");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
		$input->SetTrade_type("NATIVE");
		$input->SetOpenid($openId);
		$input->SetProduct_id($product_id);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		$result = $api->unifiedOrder($input);
		Log::DEBUG("unifiedorder:" . json_encode($result));
		return $result;
	}
	
	public function NotifyProcess($data, &$msg)
	{
		//echo "处理回调";
		Log::DEBUG("call back:" . json_encode($data));
		
		if(!array_key_exists("openid", $data) ||
			!array_key_exists("product_id", $data))
		{
			$msg = "回调数据异常";
			return false;
		}
		 
		$openid = $data["openid"];
		$product_id = $data["product_id"];
		
		//统一下单
		$result = $this->unifiedorder($openid, $product_id);
		if(!array_key_exists("appid", $result) ||
			 !array_key_exists("mch_id", $result) ||
			 !array_key_exists("prepay_id", $result))
		{
		 	$msg = "统一下单失败";
		 	return false;
		 }
		
		$this->SetData("appid", $result["appid"]);
		$this->SetData("mch_id", $result["mch_id"]);
		$this->SetData("nonce_str", WxPayApi::getNonceStr());
		$this->SetData("prepay_id", $result["prepay_id"]);
		$this->SetData("result_code", "SUCCESS");
		$this->SetData("err_code_des", "OK");
		return true;
	}
}

Log::DEBUG("begin notify!");
$notify = new NativeNotifyCallBack();
$notify->Handle(true);
