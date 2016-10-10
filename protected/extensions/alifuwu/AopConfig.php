<?php
 $AopConfig = array (
		'alipay_public_key_file' => dirname ( __FILE__ ) . "/key/alipay_rsa_public_key.pem",
		'merchant_private_key_file' => dirname ( __FILE__ ) . "/key/rsa_private_key.pem",
		'merchant_public_key_file' => dirname ( __FILE__ ) . "/key/rsa_public_key.pem",
		'charset' => "GBK",
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
		'app_id' => "2015122901051244",
 		'pid' => "2088701036418655"
);
 $AlipayConfig = array(
 		'sign_type' => strtoupper('RSA'), //签名方式
 		'transport' => 'https', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
 		'cacert' => dirname(__FILE__).'/cacert.pem', //ca证书路径地址，用于curl中ssl校验
 		'https_verify_url' => 'https://mapi.alipay.com/gateway.do?service=notify_verify&', //HTTPS形式消息验证地址
 		'http_verify_url' => 'http://notify.alipay.com/trade/notify_query.do?', //HTTP形式消息验证地址
 );