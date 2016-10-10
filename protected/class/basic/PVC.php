<?php 
include_once(dirname(__FILE__).'/../mainClass.php');
/*
 * 时间：2015-7-21
* 创建人：顾磊
* */
class PVC extends mainClass{

	//记录pv
	public function addPv(){
		$pv = new Pv();
		$merchant_id = Yii::app() -> session['merchant_id'];
		$ip_address = Yii::app()->request->userHostAddress;
		$res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip_address);
		$address = json_decode($res, true);//地址信息
		$pv_url = 'http://'.$_SERVER['HTTP_HOST'].Yii::app() -> request -> url; //pv页面url
		$come_url = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'无';//来源网站url
		$pv = new Pv();//新的pv对象
		if(isset($address['code']) && $address['code'] == '0') {
			if($address['data']['region'] == ''){
				$pv -> address = $address['data']['country'];
			}else{
				$pv -> address = $address['data']['region'].$address['data']['city'];
			}
		}
		$pv -> merchant_id = $merchant_id;
		$pv -> ip = $ip_address;
		$pv -> come_url = $come_url;
		$pv -> pv_url = $pv_url;
		$pv -> head = json_encode($_SERVER);
		$pv -> from_platform = __FROM_PLATFORM_WAP;
		$pv -> visit_date = new CDbExpression('NOW()');
		$pv -> save();
	}

}