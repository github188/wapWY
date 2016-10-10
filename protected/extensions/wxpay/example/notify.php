<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		require_once '../wxpay.custom.php'; //引入自定义配置文件
		
		//牛牛配置
		$wxpay_config['APPID'] = 'wx4ba03ea9716def82';
		$wxpay_config['MCHID'] = '1252465301';
		$wxpay_config['KEY'] = 'a50f4c15d282cfb807de37aa44d994b4';
		$wxpay_config['APPSECRET'] = '506d0f39ac342fbccb8a77759d49fe4d';
		
		//数据类
		$input = new WxPayOrderQuery();
		$input->wxpay_config = $wxpay_config;
		$input->SetTransaction_id($transaction_id);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		$result = $api->orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
