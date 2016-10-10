<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * 网页授权工具
 *
 */
class WapAuthHelper extends mainClass
{
    public $app_id = '';
    public $redirect_uri = '';
    public $state = '';

    /**
     * 构造函数
     * @param string $app_id
     * @param string $redirect_uri
     * @param string $state
     */
    public function __construct($app_id = null, $redirect_uri = null, $state = null)
    {
        if (!empty($app_id)) {
            $this->app_id = $app_id;
        }
        if (!empty($redirect_uri)) {
            $this->redirect_uri = $redirect_uri;
        }
        if (!empty($state)) {
            $this->state = $state;
        }
    }

    /**
     * 拼接url参数
     * @param unknown $urlObj
     * @return string
     */
    private function toUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 构造获取支付宝用户授权code的url链接
     * @param unknown $app_id
     * @param unknown $redirect_uri
     * @param unknown $state
     * @return string
     */
    private function createOauthUrlForAlipay($app_id, $redirect_uri, $state)
    {
        $urlObj["app_id"] = $app_id;
        $urlObj["redirect_uri"] = "$redirect_uri";
        $urlObj["scope"] = "auth_base";
        $urlObj["state"] = "$state";
        $bizString = $this->toUrlParams($urlObj);
        return "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?" . $bizString;
    }

    /**
     * 构造获取微信用户授权code的url链接
     * @param unknown $app_id
     * @param unknown $redirect_uri
     * @param unknown $state
     * @return string
     */
    private function createOauthUrlForWechat($app_id, $redirect_uri, $state)
    {
        $urlObj["appid"] = $app_id;
        $urlObj["redirect_uri"] = "$redirect_uri";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "$state" . "#wechat_redirect";
        $bizString = $this->toUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     * 支付宝授权地址
     * @return string
     */
    public function alipayAuth()
    {
        $app_id = $this->app_id;
        $redirect_uri = $this->redirect_uri;
        $state = $this->state;

        return $this->createOauthUrlForAlipay($app_id, $redirect_uri, $state);
    }

    /**
     * 微信授权地址
     * @return string
     */
    public function wechatAuth()
    {
        $app_id = $this->app_id;
        $redirect_uri = $this->redirect_uri;
        $state = $this->state;

        return $this->createOauthUrlForWechat($app_id, $redirect_uri, $state);
    }


}