<?php
//include_once(realpath(dirname(__FILE__).'/../../protected/config').'/constant.php');

include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/HttpRequst.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/function.inc.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicMessageCustomSendRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicMenuAddRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicMenuGetRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicMenuUpdateRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipaySystemOauthTokenRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicMessageTotalSendRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicMessageLabelSendRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicLabelAddRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicLabelDeleteRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicLabelQueryRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicLabelUserAddRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayUserUserinfoShareRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayPassTemplateAddRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayPassInstanceAddRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicFollowListRequest.php');
include_once(dirname(dirname(__FILE__)) . '/extensions/alifuwu/aop/request/AlipayMobilePublicQrcodeCreateRequest.php');
// include_once('HttpRequst.php');
// include_once('function.inc.php');
// include_once('aop/request/AlipayMobilePublicMessageCustomSendRequest.php');
// include_once('aop/request/AlipayMobilePublicMenuAddRequest.php');
// include_once('aop/request/AlipayMobilePublicMenuGetRequest.php');
// include_once('aop/request/AlipayMobilePublicMenuUpdateRequest.php');

class AliApi extends CController
{


    /*
     *
    * 获取菜单
    * chen:20150317
    */

    public function getMenu()
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicMenuGetRequest();
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * action：创建菜单
     * 菜单只能被创建一次，后续的修改全部由更新菜单接口完成
     */
    public function createMenu($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicMenuAddRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /*
     *
    * 更新菜单
    * chen:20150317
    */

    public function updateMenu($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicMenuUpdateRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * 创建卡券模板
     */
    public function createAliCoupons($biz_content)
    {
        $custom_send = new AlipayPassTemplateAddRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, '2015030600034164');
    }

    /**
     * 更新卡券模板
     */
    public function updateAliModelCoupons($biz_content)
    {
        $custom_send = new AlipayPassTemplateUpdateRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, '2015030600034164');
    }

    /**
     * 更新卡券
     */
    public function updateAliCoupons($biz_content)
    {
        $custom_send = new AlipayPassInstanceUpdateRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, '2015030600034164');
    }

    /**
     * 发放卡券
     */
    public function sendAliCoupons($biz_content)
    {
        $custom_send = new AlipayPassInstanceAddRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, '2015030600034164');
    }


    /**
     * 获取用户open_id
     */
    public function getOpenId($alipay_fuwu_id, $auth_code)
    {
        $custom_send = new AlipaySystemOauthTokenRequest();
        $custom_send->setGrantType("authorization_code");
        $custom_send->setCode($auth_code);
        return without_request_execute($custom_send, $alipay_fuwu_id);
    }

    /**
     * 群发广播-全部分组
     */
    public function messageTotalSend($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicMessageTotalSendRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * 群发广播-标签分组
     */
    public function messageLabelSend($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicMessageLabelSendRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * 创建标签
     */
    public function addLable($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicLabelAddRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * 删除标签
     */
    public function delLable($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicLabelDeleteRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * 查询标签
     */
    public function queryLable($biz_content = null)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicLabelQueryRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /**
     * 增加用户标签
     */
    public function lableUserAdd($biz_content = null)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicLabelUserAddRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }

    /** 返回用户信息
     * @param $token_str
     * @param $merchant_id
     * @return SimpleXMLElement[]
     */
    public function getUserInfo($token_str, $merchant_id)
    {
        //$token_str = Yii::app()->session['access_token'];
        Yii::log($token_str . '#token#', 'warning');
        $user_info = $this->requestUserInfo($token_str, $merchant_id);
        Yii::log(json_encode($user_info) . '#userInfo#', 'warning');
        if (isset ($user_info->alipay_user_userinfo_share_response)) {
            $user_info_resp = $user_info->alipay_user_userinfo_share_response;
            return $user_info_resp;
        }

    }

    /** 通过接口获取用户信息
     * @param $token
     * @param $merchant_id
     * @return mixed|SimpleXMLElement
     */
    public function requestUserInfo($token, $merchant_id)
    {
        $AlipayUserUserinfoShareRequest = new AlipayUserUserinfoShareRequest ();
        //$merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $result = without_request_execute($AlipayUserUserinfoShareRequest, $merchant['appid'], $token);
        return $result;
    }

    /*
     * 获取access_token
     */
    public function requestToken($auth_code, $merchant)
    {
        $AlipaySystemOauthTokenRequest = new AlipaySystemOauthTokenRequest ();
        $AlipaySystemOauthTokenRequest->setCode($auth_code);
        $AlipaySystemOauthTokenRequest->setGrantType("authorization_code");
        //$merchant_id = Yii::app ()->session ['merchant_id'];
        //$merchant = Merchant::model()->findByPk($merchant_id);
        $result = without_request_execute($AlipaySystemOauthTokenRequest, $merchant->appid);
        return $result;
    }

    /**
     * 获取关注着列表
     */
    public function getUserList($appid, $biz_content)
    {
        $custom_send = new AlipayMobilePublicFollowListRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $appid);
    }

    /**
     * 生成带参数的二维码
     */
    public function createQrcode($biz_content)
    {
        $merchant_id = Yii::app()->session ['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $custom_send = new AlipayMobilePublicQrcodeCreateRequest();
        $custom_send->setBizContent($biz_content);
        return without_request_execute($custom_send, $merchant['appid']);
    }
}

