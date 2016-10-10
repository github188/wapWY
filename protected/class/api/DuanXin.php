<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class Duanxin extends mainClass
{
    
    //短信接口
    /**
     * tel 手机号码 
     */    
    public function Sms($tel,$sendCode='')
    {
//         session_start();  
        $_SESSION['send_code'] = $this->random(6,1);        
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $mobile = $tel;
        $send_code = $_SESSION['send_code'];
        $mobile_code = $this->random(4,1);
        if(empty($mobile)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '手机号码不能为空';
            return json_encode($result);            
        }
        if(empty($_SESSION['send_code']) or $send_code!=$_SESSION['send_code']){
            //防用户恶意请求
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '请求超时，请刷新页面后重试';
            return json_encode($result);            
        }
        $post_data = "account=cf_lan&password=2013065&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
        //密码可以使用明文密码或使用32位MD5加密
        $gets =  $this->xml_to_array($this->Post($post_data, $target));
        if($gets['SubmitResult']['code']==2){
        	$result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
            $result['data']['msg_pwd'] = $mobile_code;
            $result['data']['phone_num'] = $mobile;
        	return json_encode($result);
        }else {
        	$result['status'] = ERROR_EXCEPTION;
        	$result['errMsg'] = isset($gets['SubmitResult']['msg']) ? $gets['SubmitResult']['msg'] : '验证码获取失败';
        	return json_encode($result);
        }     
    }
    
    function Post($curlPost,$url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }
    
    function xml_to_array($xml)
    {
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
				$subxml= $matches[2][$i];
				$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
    }
    
    function random($length = 6 , $numeric = 0) 
    {
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	if($numeric) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
    }
    
    
}