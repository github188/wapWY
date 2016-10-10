<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/mobile/WapAuthHelper.php';

/** 金佳伟 收银台专用
 * Class SytWapController
 */
class SytWapController extends MobileController
{
    public function init()
    {}

    /**
     * 授权跳转
     */
    public function actionOauthRedirect()
    {
        $paramsValid = isset($_GET['store']) && isset($_GET['state']) && isset($_GET['app_id']);
        if (!$paramsValid) {
            exit();
        }

        $store_id = $_GET['store'];
        $app_id = $_GET['app_id'];
        $state = $_GET['state'];

        //跳转地址设置
        $redirect_uri = urlencode(WAP_DOMAIN . '/mobile/SytWap/AuthCallBack' . '?store=' . $store_id);
        //客户端检测
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') !== false) {
            $helper = new WapAuthHelper($app_id, $redirect_uri, $state);
            $url = $helper->wechatAuth();
            $this->redirect($url);
        }
        if (strpos($user_agent, 'AlipayClient') !== false) {
            $helper = new WapAuthHelper($app_id, $redirect_uri, $state);
            $url = $helper->alipayAuth();
            $this->redirect($url);
        }
    }

    /**
     * 授权回跳
     */
    public function actionAuthCallBack()
    {
        $paramsValid = (isset($_GET['auth_code']) || isset($_GET['code'])) && isset($_GET['state']) && isset($_GET['store']);
        if (!$paramsValid) {
            exit();
        }

        $url = SYT_DOMAIN . '/syt/wap/pay?' . http_build_query($_GET);
        $this->redirect($url);
    }
}