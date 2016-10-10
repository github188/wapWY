<?php
include_once 'Wechat.php';


class WechatWebAuth extends Wechat
{
    //缓存Key值后缀
    private static $access_token_suffix     = '_user_access_token';
    private static $refresh_token_suffix    = '_user_refresh_token';
    
    /**
     * Step1 网页授权获取用户Code
     * @param unknown $merchant
     * @param unknown $redirect_uri
     * @param string $scope
     * @param unknown $state
     * @param unknown $obj
     */
    public static function getUserCode($merchant, $redirect_uri, $scope, $state, $obj)
    {
        if(empty($scope))
            $scope = 'snsapi_base';
        if(isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth == 2)
        {
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$merchant->wechat_thirdparty_authorizer_appid.'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope='.$scope.'&state='.$state.'&component_appid='.Component::getAppId().'#wechat_redirect';
        }
        else 
        {
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$merchant->wechat_subscription_appid.'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
        }
        $obj->redirect($url);
    }
    
    /**
     * Step2 网页授权获取access_token，refresh_token
     * @param unknown $merchant
     * @param unknown $code
     * @return Ambigous <boolean, mixed>
     * {
     *  "access_token":"ACCESS_TOKEN",
     *  "expires_in":7200,
     *  "refresh_token":"REFRESH_TOKEN",
     *  "openid":"OPENID",
     *  "scope":"SCOPE"
     * }
     */
    public static function getUserAccessTokenJSON($merchant, $code)
    {
        $url = '';
        //授权模式
        if(isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth == 2)
        {
            $url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid='.$merchant->wechat_thirdparty_authorizer_appid.'&code='.$code.'&grant_type=authorization_code&component_appid='.Component::getAppId().'&component_access_token='.Component::getComponentAccessToken();
        }
        //AppId, Secret未授权绑定模式
        else
        {
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$merchant->wechat_subscription_appid.'&secret='.$merchant->wechat_subscription_appsecret.'&code='.$code.'&grant_type=authorization_code';
        }
        $res = parent::http_get($url);
        $obj = json_decode($res);
        //refresh_token 30天缓存
        Yii::app()->memcache->set($obj->openid.self::$refresh_token_suffix, $obj->refresh_token, 2592000-100);
        //access_token 2小时缓存
        Yii::app()->memcache->set($obj->openid.self::$access_token_suffix, $obj->access_token, $obj->expires_in);
        return $res;
    }
    
    /**
     * Step3 刷新用户access_token
     * @param unknown $merchant
     * @param unknown $refresh_token
     * @return Ambigous <boolean, mixed>
     * {
     *  "access_token":"ACCESS_TOKEN",  
     *  "expires_in":7200,   
     *  "refresh_token":"REFRESH_TOKEN",   
     *  "openid":"OPENID",   
     *  "scope":"SCOPE" 
     * } 
     */
    public static function refreshUserAccessTokenJSON($merchant, $refresh_token)
    {
        $url = '';
        if(isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth == 2)
        {
            $url = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid='.$merchant->wechat_thirdparty_authorizer_appid.'&grant_type=refresh_token&component_appid='.Component::getAppId().'&component_access_token='.Component::getComponentAccessToken().'&refresh_token='.$refresh_token;
        }
        else
        {
            $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$merchant->wechat_subscription_appid.'&grant_type=refresh_token&refresh_token='.$refresh_token;
        }
        $res = parent::http_get($url);
        $obj = json_decode($res);
        //refresh_token 30天缓存
        Yii::app()->memcache->set($obj->openid.self::$refresh_token_suffix, $obj->refresh_token, 2592000);
        //access_token 2小时缓存
        Yii::app()->memcache->set($obj->openid.self::$access_token_suffix, $obj->access_token, $obj->expires_in);
        return $res;
    }
    
    /**
     * Step4 获取用户信息
     * @param unknown $access_token
     * @param unknown $openid
     */
    public static function getUserInfoJSON($access_token, $openid)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $res = parent::http_get($url);
        return $res;
    }
    
    /**
     * 检验授权凭证（access_token）是否有效
     * @param unknown $access_token
     * @param unknown $openid
     */
    public static function checkUserAccessToken($access_token, $openid)
    {
        $url = 'https://api.weixin.qq.com/sns/auth?access_token='.$access_token.'&openid='.$openid;
        $res = parent::http_get($url);
        $obj = json_decode($res);
        if($obj->errcode==0 && $obj->errmsg=='ok')
            return true;
        return false;
    }
    
    
}