<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-查退款单</title>
</head>
<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once 'log.php';
require_once '../wxpay.custom.php'; //引入自定义配置文件

//牛牛配置
$wxpay_config['APPID'] = 'wx4ba03ea9716def82';
$wxpay_config['MCHID'] = '1252465301';
$wxpay_config['KEY'] = 'a50f4c15d282cfb807de37aa44d994b4';
$wxpay_config['APPSECRET'] = '506d0f39ac342fbccb8a77759d49fe4d';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);


function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#f00;'>$key</font> : $value <br/>";
    }
}

if(isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != ""){
	$transaction_id = $_REQUEST["transaction_id"];
	//数据类
	$input = new WxPayRefundQuery();
	$input->wxpay_config = $wxpay_config;
	$input->SetTransaction_id($transaction_id);
	//接口类
	$api = new WxPayApi();
	$api->wxpay_config = $wxpay_config;
	printf_info($api->refundQuery($input));
}

if(isset($_REQUEST["out_trade_no"]) && $_REQUEST["out_trade_no"] != ""){
	$out_trade_no = $_REQUEST["out_trade_no"];
	//数据类
	$input = new WxPayRefundQuery();
	$input->wxpay_config = $wxpay_config;
	$input->SetOut_trade_no($out_trade_no);
	//接口类
	$api = new WxPayApi();
	$api->wxpay_config = $wxpay_config;
	printf_info($api->refundQuery($input));
	exit();
}

if(isset($_REQUEST["out_refund_no"]) && $_REQUEST["out_refund_no"] != ""){
	$out_refund_no = $_REQUEST["out_refund_no"];
	//数据类
	$input = new WxPayRefundQuery();
	$input->wxpay_config = $wxpay_config;
	$input->SetOut_refund_no($out_refund_no);
	//接口类
	$api = new WxPayApi();
	$api->wxpay_config = $wxpay_config;
	printf_info($api->refundQuery($input));
	exit();
}

if(isset($_REQUEST["refund_id"]) && $_REQUEST["refund_id"] != ""){
	$refund_id = $_REQUEST["refund_id"];
	//数据类
	$input = new WxPayRefundQuery();
	$input->wxpay_config = $wxpay_config;
	$input->SetRefund_id($refund_id);
	//接口类
	$api = new WxPayApi();
	$api->wxpay_config = $wxpay_config;
	printf_info($api->refundQuery($input));
	exit();
}
	
?>
<body>  
	<form action="#" method="post">
        <div style="margin-left:2%;color:#f00">微信订单号、商户订单号、微信订单号、微信退款单号选填至少一个，微信退款单号优先：</div><br/>
        <div style="margin-left:2%;">微信订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="transaction_id" /><br /><br />
        <div style="margin-left:2%;">商户订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="out_trade_no" /><br /><br />
        <div style="margin-left:2%;">商户退款单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="out_refund_no" /><br /><br />
        <div style="margin-left:2%;">微信退款单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="refund_id" /><br /><br />
		<div align="center">
			<input type="submit" value="查询" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" />
		</div>
	</form>
</body>
</html>