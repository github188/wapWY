<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/extensions/wxopen/wxBizMsgCrypt.php';

class Component
{

    private static $appId = 'wx4de0ac035e5784ae';

    private static $appSecret = '1ba72043e23c37941975416798bfd28e';

    private static $token = 'wanquan';

    private static $aesKey = 'qweiyifadsqweiyifadsqweiyifadsqweiyifads123';
    
    private static $auth_access_token_suffix = '_auth_access_token';

    public static function getAppId()
    {
        return self::$appId;
    }

    public static function getAppSecret()
    {
        return self::$appSecret;
    }

    public static function getToken()
    {
        return self::$token;
    }

    public static function getAesKey()
    {
        return self::$aesKey;
    }
    
    function __construct()
    {
        
    }
    
    /**
     * 1、从缓存中获取component_verify_ticket
     */
    public static function getComponentVerfyTicket()
    {
        $component_verify_ticket = Yii::app()->memcache->get('component_verify_ticket');
        return $component_verify_ticket ? $component_verify_ticket : '';
    }

    /**
     * 2、获取第三方平台component_access_token
     */
    public static function getComponentAccessToken()
    {
        // 从缓存中读取
        $component_access_token = Yii::app()->memcache->get('component_access_token');
        // 成功返回
        if ($component_access_token)
        {
            return $component_access_token;
        }  // 缓存不存在重新获取
        else
        {
            
            $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
            $component_verify_ticket = self::getComponentVerfyTicket();
            $data = array(
                'component_appid' => self::$appId,
                'component_appsecret' => self::$appSecret,
                'component_verify_ticket' => $component_verify_ticket
            );
            $res = https_post_data($url, $data);
            
            $arr = json_decode($res, true);
            $component_access_token = $arr['component_access_token'];
            // 提前100s更新component_access_token
            $expires_in = $arr['expires_in'] - 100;
            Yii::app()->memcache->set('component_access_token', $component_access_token, $expires_in);
            
            return $component_access_token;
        }
    }

    /**
     * 3、获取预授权码pre_auth_code
     */
    public static function getPreAuthCode()
    {
        $component_access_token = self::getComponentAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $component_access_token;
        
        $res = https_post_data($url, array(
            'component_appid' => self::$appId
        ));
        $arr = json_decode($res, true);
        $pre_auth_code = $arr['pre_auth_code'];
        return $pre_auth_code;
    }

    /**
     * 4、使用授权码换取公众号的接口调用凭据和授权信息
     *
     * @param unknown $authorization_code            
     */
    public static function getApiQueryAuth($authorization_code)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=' . $component_access_token;
        $data = array(
            'component_appid' => self::$appId,
            'authorization_code' => $authorization_code
        );
        $res = https_post_data($url, $data);
        return $res;
    }

    /**
     * 5、获取（刷新）授权公众号的接口调用凭据（令牌）
     *
     * @param unknown $authorizer_appid            
     * @param unknown $refresh_token_value            
     * @return unknown
     */
    public static function getAuthAccessToken($authorizer_appid, $refresh_token_value)
    {
        $key = $authorizer_appid . self::$auth_access_token_suffix;
        // 从缓存中去读
        $authorizer_access_token = Yii::app()->memcache->get($key);
        if (empty($authorizer_access_token) || !isset($authorizer_access_token)) {
            $authorizer_access_token = self::refreshAuthAccessToken($authorizer_appid, $refresh_token_value);
        }
        return $authorizer_access_token;
    }
    
    public static function refreshAuthAccessToken($authorizer_appid, $refresh_token_value)
    {
        $key = $authorizer_appid . self::$auth_access_token_suffix;
        
        $component_access_token = self::getComponentAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $component_access_token;
        // 数据需更改
        $data = array(
            'component_appid' => self::$appId,
            'authorizer_appid' => $authorizer_appid,
            'authorizer_refresh_token' => $refresh_token_value
        );
        
        $res = json_decode(https_post_data($url, $data), true);
        $authorizer_access_token = $res['authorizer_access_token'];
        
        //缓存-100s
        $expires_in = $res['expires_in']-100;
        // 缓存到memcache key：appid+suffix value：authorizer_access_token
        Yii::app()->memcache->set($key, $authorizer_access_token, $expires_in);
        return $authorizer_access_token;
    }
    
    /**
     * 6、获取授权方的公众号帐号基本信息
     *
     * @param unknown $authorizer_appid            
     */
    public static function getAuthInfo($authorizer_appid)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $component_access_token;
        
        $data = array(
            'component_appid' => self::$appId,
            'authorizer_appid' => $authorizer_appid
        );
        
        $res = https_post_data($url, $data);
        return $res;
    }

    /**
     * 7、获取授权方的选项设置信息
     *
     * @param unknown $authorizer_appid            
     * @param unknown $option_name            
     * @return unknown
     */
    public static function getAuthOption($authorizer_appid, $option_name)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/ api_get_authorizer_option?component_access_token=' . $component_access_token;
        
        $data = array(
            'component_appid' => self::$appId,
            'authorizer_appid' => $authorizer_appid,
            'option_name' => $option_name
        );
        
        $res = https_post_data($url, $data);
        return $res;
    }

    /**
     * 8、设置授权方的选项信息
     *
     * @param unknown $authorizer_appid            
     * @param unknown $option_name            
     * @param unknown $option_value            
     * @return unknown
     */
    public static function setAuthOption($authorizer_appid, $option_name, $option_value)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/component/ api_set_authorizer_option?component_access_token=' . $component_access_token;
        
        $data = array(
            'component_appid' => self::$appId,
            'authorizer_appid' => $authorizer_appid,
            'option_name' => $option_name,
            'option_value' => $option_value
        );
        $res = https_post_data($url, $data);
        return $res;
    }
}

/**
 * https_curl并发送由数组组装的json数据
 *
 * @param string $url
 *            地址
 * @param unknown $array
 *            数组
 */
function https_post_data($url = '', $array = array())
{
    $data = json_encode($array);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));
    
    $result = curl_exec($ch);
    return $result;
}