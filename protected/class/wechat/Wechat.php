<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/protected/components/Component.php';

class Wechat
{
    private static $access_token_suffix = '_access_token';
    
    /**
     * 根据商户ID获取商户(Web端)
     * @param string $merchant_id
     * @return static
     */
    public static function getMerchantById($merchant_id='')
    {
        $merchant = Merchant::model()->findByPk($merchant_id);
        return $merchant;
    }
    
    /**
     * 根据加密的商户ID获取商户(手机端)
     * @param string $encrypt_id
     * @return static
     */
    public static function getMerchantByEncryptId($encrypt_id='')
    {
        $merchant = Merchant::model() -> find('encrypt_id =:encrypt_id',array(':encrypt_id' => $encrypt_id));
        return $merchant;
    }
    
    /**
     * 根据Merchant获取Appid
     * @param unknown $merchant
     */
    public static function getAppIdByMerchant($merchant)
    {
        if(isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth==2)
        {
            return $merchant->wechat_thirdparty_authorizer_appid;
        }
        return $merchant->wechat_subscription_appid;
    }
    
    /**
     * 根据Merchant获取access_token
     * @param unknown $merchant
     * @return unknown|mixed
     */
    public static function getTokenByMerchant($merchant)
    {
        $access_token = '';
        //第三方授权模式
        if(isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth==2)
            $access_token = Component::getAuthAccessToken($merchant->wechat_thirdparty_authorizer_appid, $merchant->wechat_thirdparty_authorizer_refresh_token);
        //默认绑定Appid和Secret模式
        else
            $access_token = self::getAccessToken($merchant->wechat_subscription_appid, $merchant->wechat_subscription_appsecret);
        
        if(empty($access_token) || !(self::isOkStatus($access_token)))
        {
            if (isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth==2)
                $access_token = Component::refreshAuthAccessToken($merchant->wechat_thirdparty_authorizer_appid, $merchant->wechat_thirdparty_authorizer_refresh_token);
            else 
                $access_token = self::refreshAccessToken($merchant->wechat_subscription_appid, $merchant->wechat_subscription_appsecret);
        }
        return $access_token;
    }
    
    public static function isOkStatus($access_token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$access_token;
        
        $res = self::http_get($url);
        $obj = json_decode($res);
        $ip_list = $obj->ip_list;
        
        if(isset($ip_list) && !empty($ip_list))
            return true;
        else 
            return false;
    }
    
    /**
     * 根据Appid和Secret获取AccessToken
     * @param unknown $appid
     * @param unknown $secret
     * @return mixed
     */
    public static function getAccessToken($appid, $secret)
    {
        // 缓存key
        $key = $appid . self::$access_token_suffix;
        
        $access_token = Yii::app()->memcache->get($key);
        //判断缓存是否存在
        if (empty($access_token) || !isset($access_token)) {
            $access_token = self::refreshAccessToken($appid, $secret);
        }
        
        return $access_token;        
    }
    
    public static function refreshAccessToken($appid, $secret)
    {
        $key = $appid . self::$access_token_suffix;
        
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $res = json_decode(self::http_get($url), true);
        //格式{"access_token":"ACCESS_TOKEN","expires_in":7200}
        $access_token = $res['access_token'];
        //缓存-100s
        $expires_in = $res['expires_in']-100;
        Yii::app()->memcache->set($key, $access_token, $expires_in);
        return $access_token;
    }
    
    /**
     * 获取单个粉丝信息
     * @param unknown $merchant
     * @param unknown $openid
     * @return Ambigous <boolean, mixed>
     */
    public static function getSingleUserInfoByMerchant($merchant, $openid)
    {
        $access_token = self::getTokenByMerchant($merchant);
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
    
        $res = self::http_get($url);
    
        return $res;
    }
    
    /**
     * 获取多个粉丝信息
     * @param unknown $merchant
     * @param unknown $openids
     */
//     public static function getBatchUserInfoByMerchant($merchant, $openids)
//     {
//         $user_list = array();
//         foreach($openids as $k=>$v)
//         {
//             $user_list[$k]['openid'] = $v;
//             $user_list[$k]['lang'] = 'zh_CN';
//         }
        
//         $array = array(
//             'user_list'=>$user_list,
//         );
//         $url = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.self::getAppIdByMerchant($merchant);
//         $res = self::http_post($url, json_encode($array));
//         return $res;
//     }
    
//     public static function getUserListByMerchant($merchant)
//     {
//         $res = '';
//         $next_openid = '';
//         $result = '';
//         $solt = '';
        
//         do 
//         {
//             $res = self::getUserLists($merchant);
//             $obj = json_decode($res);
//             $solt .= $obj->data->openid;
//             $next_openid = $obj->next_openid;
//         } while ($obj->count == 10000);
        
//         $result = '{"total":'.$obj->total.',"count":'.$obj->total.',"data":{"openid":'.$solt.'}'.'}';
        
//         return $result;
//     }
    
//     private static function getUserLists($merchant, $next_openid='')
//     {
//         $access_token = self::getTokenByMerchant($merchant);
//         $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token.'&next_openid='.$next_openid;
//         $res = self::http_get($url);
//         return $res;
//     }
    
    /**
     * GET 请求
     * 
     * @param string $url            
     */
    public static function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * POST 请求
     * 
     * @param string $url            
     * @param array $param            
     * @param boolean $post_file
     *            是否文件上传
     * @return string content
     */
    public static function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
    
    /**
     * JSON数据转Obj对象
     * @param string $jsonStr
     * @return mixed
     */
    public static function JsonToObj($jsonStr='')
    {
        $obj = json_decode($jsonStr);
        return $obj;
    }
}