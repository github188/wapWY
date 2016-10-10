<?php
function writeLog($text)
{
    // $text=iconv("GBK", "UTF-8//IGNORE", $text);
    $text = characet($text);
//    $log = fopen("/log/log.txt", "w") or die("Unable to open file!");
//    $txt = date("Y-m-d H:i:s") . "  " . $text . "\r\n";
//    fwrite($log, $txt);
//    fclose($log);
    file_put_contents(dirname(__FILE__) . "/log/log.txt", date("Y-m-d H:i:s") . "  " . $text . "\r\n", FILE_APPEND);
}

//转换编码
function characet($data)
{
    if (!empty ($data)) {
        $fileType = mb_detect_encoding($data, array(
            'UTF-8',
            'GBK',
            'GB2312',
            'LATIN1',
            'BIG5'
        ));
        if ($fileType != 'UTF-8') {
            $data = mb_convert_encoding($data, 'UTF-8', $fileType);
        }
    }
    return $data;
}

/**
 * 使用SDK执行接口请求
 * @param unknown $request
 * @param string $token
 * @return Ambigous <boolean, mixed>
 */
function aopclient_request_execute($request, $appid = NULL, $token = NULL, $app_auth_token = NULL)
{
    require(dirname(__FILE__) . '/AopConfig.php');
    require_once(dirname(__FILE__) . '/aop/AopClient.php');
    require_once(dirname(__FILE__) . '/aop/SignData.php');

    $aop = new AopClient ();
    $aop->gatewayUrl = $AopConfig ['gatewayUrl'];
    //$aop->appId = $aliconfig ['app_id'];
    $aop->appId = $appid ?: $AopConfig['app_id'];
    $aop->rsaPrivateKeyFilePath = $AopConfig ['merchant_private_key_file'];
    $aop->apiVersion = "1.0";
    $result = $aop->execute($request, $token, $app_auth_token);
    //writeLog("response: " . var_export($result, true));
    return $result;
}

/*
 *
 * 不经过网关执行接口
 * chen:20150317
 */

function without_request_execute($request, $appid, $token = NULL)
{	
    require(dirname(__FILE__) . '/AopConfig.php');
    require_once(dirname(__FILE__) . '/aop/AopClient.php');
    require_once(dirname(__FILE__) . '/aop/SignData.php');
   
    $aop = new AopClient ();
    $aop->appId = $appid;
    $aop->rsaPrivateKeyFilePath = $AopConfig ['merchant_private_key_file'];
    $aop->format = "json";
    $result = $aop->execute($request, $token);
    //writeLog("response: " . var_export($result, true));  
   
    return $result;
}

/**
 * 针对notify_url验证消息是否是支付宝发出的合法消息
 * @param unknown $partner
 * @param unknown $notify_id
 * @return boolean
 */
function verifyNotify($partner, $notify_id) {
	require(dirname(__FILE__) . '/AopConfig.php');
	require(dirname(__FILE__) . '/AlipaySign.php');
	
	if(empty($_POST)) {//判断POST来的数组是否为空
		return false;
	}
	//writeLog("notify: " . var_export($_POST, true));
	
	//验证是否支付宝请求
	$responseTxt = getResponse($partner, $notify_id);
	
	//sign验签
	$isSign = false;
	$alipay_sign = new AlipaySign();
	$data = createDataString($_POST);
	$sign = isset($_POST['sign']) ? $_POST['sign'] : '';
	$rsaPublicKeyFilePath = $AopConfig['alipay_public_key_file'];
	$isSign = $alipay_sign->rsa_verify($data, $sign, $rsaPublicKeyFilePath);
	
	if (preg_match("/true$/i",$responseTxt) && $isSign) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取远程服务器ATN结果,验证返回URL
 * @param $notify_id 通知校验ID
 * @return 服务器ATN结果
 * 验证结果集：
 * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
 * true 返回正确信息
 * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
 */
function getResponse($partner, $notify_id) {
	require(dirname(__FILE__) . '/AopConfig.php');
	
	$transport = strtolower(trim($AlipayConfig['transport']));
	//$partner = trim($this->alipay_config['partner']);
	$veryfy_url = '';
	if($transport == 'https') {
		$veryfy_url = $AlipayConfig['https_verify_url'];
	}
	else {
		$veryfy_url = $AlipayConfig['http_verify_url'];
	}
	$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
	$responseTxt = getHttpResponseGET($veryfy_url, NULL);

	return $responseTxt;
}

/**
 * 远程获取数据，GET模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
 * @param $url 指定URL完整路径地址
 * @param $cacert_url 指定当前工作目录绝对路径
 * return 远程输出的数据
 */
if (!function_exists('getHttpResponseGET')) {
	function getHttpResponseGET($url,$cacert_url=NULL) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
		//curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
		$responseText = curl_exec($curl);
		//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
		curl_close($curl);
	
		return $responseText;
	}
}

/**
 * 生成待签名字符串，用于异步返回结果的验签
 * @param unknown $params
 * @return string
 */
function createDataString($params) {
	$string = '';
	
	//1.除去sign、sign_type两个参数
	unset($params['sign']);
	unset($params['sign_type']);
	
	//2.对参数url_decode
	foreach ($params as $k => $v) {
		$decode_str = urldecode($v);
		$params[$k] = $decode_str;
	}
	
	//3.字典序排序
	ksort($params);
	reset($params);
	
	//4.拼接字符串
	foreach ($params as $k => $v) {
		$string .= $k.'='.$v.'&';
	}
	//去掉最后一个&字符
	$string = substr($string,0,count($string)-2);
	
	return $string;
}

