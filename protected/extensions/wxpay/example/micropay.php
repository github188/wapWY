<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-查退款单</title>
</head>
<?php
require_once "../lib/WxPay.Api.php";
require_once "WxPay.MicroPay.php";
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

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

if(isset($_REQUEST["auth_code"]) && $_REQUEST["auth_code"] != ""){
	$auth_code = $_REQUEST["auth_code"];
	//数据类
	$input = new WxPayMicroPay();
	//设置商户信息配置信息
	$input->wxpay_config = $wxpay_config;
	$input->SetAuth_code($auth_code);
	$input->SetBody("刷卡测试样例-支付");
	$input->SetTotal_fee("1");
	$input->SetOut_trade_no($wxpay_config['MCHID'].date("YmdHis"));
	
	$microPay = new MicroPay();
	$microPay->wxpay_config = $wxpay_config;
	printf_info($microPay->pay($input));
}

/**
 * 注意：
 * 1、提交被扫之后，返回系统繁忙、用户输入密码等错误信息时需要循环查单以确定是否支付成功
 * 2、多次（一半10次）确认都未明确成功时需要调用撤单接口撤单，防止用户重复支付
 */

?>
<body>  
	<form action="#" method="post">
        <div style="margin-left:2%;">商品描述：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" readonly value="刷卡测试样例-支付" name="auth_code" /><br /><br />
        <div style="margin-left:2%;">支付金额：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" readonly value="1分" name="auth_code" /><br /><br />
        <div style="margin-left:2%;">授权码：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="auth_code" /><br /><br />
       	<div align="center">
			<input type="submit" value="提交刷卡" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" />
		</div>
	</form>
</body>
</html>