<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'log.php';
require_once '../wxpay.custom.php'; //引入自定义配置文件

//牛牛配置
$wxpay_config['APPID'] = 'wx4ba03ea9716def82';
$wxpay_config['MCHID'] = '1252465301';
$wxpay_config['KEY'] = 'a50f4c15d282cfb807de37aa44d994b4';
$wxpay_config['APPSECRET'] = '506d0f39ac342fbccb8a77759d49fe4d';

//模式一
/**
 * 流程：
 * 1、组装包含支付信息的url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
 * 5、支付完成之后，微信服务器会通知支付成功
 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$notify = new NativePay();
$url1 = $notify->GetPrePayUrl("123456789",$wxpay_config);

//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */


//数据封装类
$input = new WxPayUnifiedOrder();
//设置商户信息配置信息
$input->wxpay_config = $wxpay_config;
//设置商品描述
$input->SetBody("扫码支付");
//设置附加数据
$input->SetAttach("attach");
//设置订单号
$input->SetOut_trade_no("20150731777");
//设置订单金额，单位分
$input->SetTotal_fee("1");
//设置订单生成时间
$input->SetTime_start(date("YmdHis"));
//设置订单失效时间
$input->SetTime_expire(date("YmdHis", time() + 600));
//设置商品标识，针对代金券或立减优惠功能
$input->SetGoods_tag("normal");
//回调地址
$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
//设置交易类型：JSAPI，NATIVE，APP
$input->SetTrade_type("NATIVE");
$input->SetProduct_id("123456789");
//$result = $notify->GetPayUrl($input);

//接口类
$api = new WxPayApi();
//设置商户信息配置信息
$api->wxpay_config = $wxpay_config;
//调用统一下单接口
$result = $api->unifiedOrder($input);
$url2 = $result["code_url"];

?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<body>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div><br/>
	<img alt="模式一扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url1);?>" style="width:150px;height:150px;"/>
	<br/><br/><br/>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
	<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url2);?>" style="width:150px;height:150px;"/>
</body>
</html>