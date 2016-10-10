<?php

/**
 * 微信会员类
 */
class WechatUser extends WechatBase{
    public $page = null;
    //获取用户openid(需授权)
    public function getUserOpenId($code,$encrypt_id = '',$merchant_id = '') {
        $result = array();
        try {
            //获取商户信息
            if(!empty($merchant_id)){
                $merchant = Merchant::model() -> findByPk($merchant_id);
            }elseif (!empty($encrypt_id)){
                $merchant = Merchant::model() -> find('encrypt_id =:encrypt_id',array(
                    ':encrypt_id' => $encrypt_id
                ));
            }
            //用wechat_subscription_appid,wechat_subscription_appsecret,code获取openid
            $wechat_subscription_appid = $merchant -> wechat_subscription_appid;
            $wechat_subscription_appsecret = $merchant -> wechat_subscription_appsecret;
            $re = $this->getUserAccessCode($code, $wechat_subscription_appid, $wechat_subscription_appsecret, $merchant->id);
            $result['openid'] = $re['openid'];
            $result['access_token'] = $re['access_token'];
            $result['status'] = ERROR_NONE;
            
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    
  
}