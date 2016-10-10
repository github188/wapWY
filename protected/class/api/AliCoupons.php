<?php
include_once(dirname(__FILE__).'/../mainClass.php');
include_once(dirname(__FILE__) . '/../../extensions/alifuwu/aop/AopClient.php');
include_once(dirname(__FILE__) . '/../../extensions/alifuwu/function.inc.php');
include_once(dirname(__FILE__) . '/../../extensions/alifuwu/aop/request/AlipayPassTemplateAddRequest.php');

class AliCoupons extends mainClass
{
	/**
	 * 新建卡券模版
	 */
	function createCoupons(){
		
		$array = array();
		$array['tpl_content']['logo'] = 'https://tfsimg.alipay.com/images/kabaoprod/T1uoldXeVlXXaCwpjX';
		
		$content = array();
		
		$evoucherInfo = array();
		$evoucherInfo['title'] = '可乐兑换券';
		$evoucherInfo['startDate'] = '2015-12-11 21:19:50';
		$evoucherInfo['endDate'] = '2015-12-31 23:59:59';
		$evoucherInfo['type'] = 'coupon';
		$evoucherInfo['product'] = 'free';
		
		
		$operation = array();
		$operation['message'] = 'message';
		$operation['messageEncoding'] = 'UTF-8';
		$operation['format'] = 'barcode';
		$operation['altText'] = 'ackCode';
		
		//for($i=0;$i<2;$i++){
			$evoucherInfo['operation'][] = $operation;
		//}
		
		
		$einfo = array();
		$einfo['logoText'] = '兑换券';
		
		$second = array();
		$second['key'] = 'cinemaName';
		$second['value'] = 'ax';
		$second['label'] = '影院';
		$second['type'] = 'text';
		$einfo['secondaryFields'][] = $second;
		
		$evoucherInfo['einfo'] = $einfo;
		$content['evoucherInfo'] = $evoucherInfo;
		$content['fileInfo']['formatVersion'] = 2;
		$content['fileInfo']['serialNumber'] = $this->getSerialNumber();
		$content['merchant']['mname'] = '牛扣网';
		$content['platform']['channelID'] = '2015030600034164';
		$content['platform']['webServiceUrl'] = 'http://gj.51wanquan.com/mCenter/gateway?account=hma92h';
		$array['tpl_content']['content'] = $content;
		
		
		$json_menu = urldecode(json_encode($array));
		//调用ali接口
		$api = new AliApi('AliApi');
		$response = $api->createAliCoupons($json_menu);
		var_dump($response);exit;
		
	}
	
	/**
	 * 发放卡券
	 */
	function sendCoupons()
	{
		$array = array();
		$array['recognition_type'] = '3';
		$array['tpl_id'] = '2015121014371287913127299';
		
		$recognition_info = array();
		$recognition_info['mobile'] = '18758363280';
		
		$array['recognition_info'] = $recognition_info;
		$tpl_params = array();
		$tpl_params['tpl_params'] = '';
		$array['tpl_params'] = $tpl_params;
		$json_menu = json_encode($array);//echo $json_menu;exit;
		//调用ali接口
		$api = new AliApi('AliApi');
		$response = $api->sendAliCoupons($json_menu);
		var_dump($response);exit;
	}
	
	/**
	 * 更新卡券模版(适用于未发布的卡券)
	 */
	public function updateModelCoupons()
	{
		$array = array();
		$array['tpl_content']['logo'] = 'https://tfsimg.alipay.com/images/kabaoprod/T1uoldXeVlXXaCwpjX';
		$array['tpl_id'] = '123';
		
		$evoucherInfo = array();
		$evoucherInfo['title'] = '可乐兑换券';
		$evoucherInfo['startDate'] = '2015-12-01 21:19:50';
		$evoucherInfo['endDate'] = '2015-12-31 23:59:59';
		$evoucherInfo['type'] = 'coupon';
		$evoucherInfo['product'] = 'price';
		
		
		$operation = array();
		$operation['message'] = 'message';
		$operation['messageEncoding'] = 'UTF-8';
		$operation['format'] = 'barcode';
		$operation['altText'] = 'ackCode';
		
	
		$evoucherInfo['operation'][] = $operation;
		
		
		$einfo = array();
		$einfo['logoText'] = '兑换券';
		
		$second = array();
		$second['key'] = 'cinemaName';
		$second['value'] = 'ax';
		$second['label'] = '影院';
		$second['type'] = 'text';
		$einfo['secondaryFields'][] = $second;
		
		$evoucherInfo['einfo'] = $einfo;
		$content['evoucherInfo'] = $evoucherInfo;
		$content['fileInfo']['formatVersion'] = 2;
		$content['fileInfo']['serialNumber'] = '46744781372017745734527475616758';
		$content['merchant']['mname'] = '牛扣网';
		$content['platform']['channelID'] = '2015030600034164';
		$content['platform']['webServiceUrl'] = 'http://gj.51wanquan.com/mCenter/gateway?account=hma92h';
		$array['tpl_content']['content'] = $content;
		
		$json_menu = urldecode(json_encode($array));
		//调用ali接口
		$api = new AliApi('AliApi');
		$response = $api->updateAliModelCoupons($json_menu);
		var_dump($response);exit;
		
	}
	
	/**
	 * 更新卡券（已经发布的卡券）
	 */
	public function updateCoupons()
	{

		$array = array();
		$array['serial_number'] = '29743990726646594649700310919619';
		
		//$tpl_params = array();
		
		$evoucherInfo = array();
		$evoucherInfo['title'] = '可乐兑换券';
		$evoucherInfo['startDate'] = '2015-12-11 21:19:50';
		$evoucherInfo['endDate'] = '2015-12-31 23:59:59';
		$evoucherInfo['type'] = 'coupon';
		$evoucherInfo['product'] = 'free';
		
		
		$operation = array();
		$operation['message'] = 'message';
		$operation['messageEncoding'] = 'UTF-8';
		$operation['format'] = 'barcode';
		$operation['altText'] = 'ackCode';
		
		
		$evoucherInfo['operation'][] = $operation;		
		
		$einfo = array();
		$einfo['logoText'] = '兑换券';
		
		$second = array();
		$second['key'] = 'cinemaName';
		$second['value'] = 'ax';
		$second['label'] = '影院';
		$second['type'] = 'text';
		$einfo['secondaryFields'][] = $second;
		
		$evoucherInfo['einfo'] = $einfo;
		$content['evoucherInfo'] = $evoucherInfo;
		$content['fileInfo']['formatVersion'] = 2;
		$content['fileInfo']['serialNumber'] = '29743990726646594649700310919619';
		$content['merchant']['mname'] = '牛扣网';
		$content['platform']['channelID'] = '2015030600034164';
		$content['platform']['webServiceUrl'] = 'http://gj.51wanquan.com/mCenter/gateway?account=hma92h';
		$array['tpl_params']['content'] = $content;
		
		$array['channel_id'] = '2015030600034164';
		$array['status'] = 'CLOSED';
		$array['verify_code'] = '8612231273';
		$array['verify_type'] = 'barcode';
		
		$json_menu = urldecode(json_encode($array));
		//调用ali接口
		$api = new AliApi('AliApi');
		$response = $api->updateAliCoupons($json_menu);
		var_dump($response);exit;
		
	}
	
	/**
	 * 产生32位序列号
	 */
	public function getSerialNumber()
	{
		$serialNumber = '';
		$strNum = '0,1,2,3,4,5,6,7,8,9';
		$list = explode(',',$strNum);
		for($i = 0; $i < 32; $i ++) {
			$serialNumber .= $list [rand ( 0, 9 )];
		}
		return $serialNumber;
	}
	
	
	/**
	 * post提交数据
	 * @param type $url
	 * @param type $data
	 * @return type
	 */
	function  postData($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过SSL证书检查  https方式
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE,不加这句返回始终是1
	
		curl_setopt($ch, CURLOPT_POST, 1); //启用POST提交
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$file_contents = curl_exec($ch);
		if (curl_errno($ch)) {
			return 'Errno' . curl_error($ch);
		}
		curl_close($ch);
	
		return $file_contents;
	}
}