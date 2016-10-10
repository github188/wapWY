<?php
include_once(realpath(dirname(__FILE__).'/../../../protected/extensions/wxjssdk').'/jssdk.php');
/*
 * 微信js类
 */
class WechatJS extends WechatBase{
    
    public function Wxshare($merchant_id = '',$encrypt_id = ''){
        //数据库查询
        if(!empty($encrypt_id)){
            $model = Merchant::model() -> find('encrypt_id = :encrypt_id and flag =:flag',array(
                ':encrypt_id' => $encrypt_id,
                ':flag' => FLAG_NO
            ));
        }elseif (!empty($merchant_id)){
            $model = Merchant::model() -> find('id = :id and flag =:flag',array(
                ':id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
        }
        if (!empty($model)){
            $appid = $model -> wechat_subscription_appid;
            $appsecret = $model -> wechat_subscription_appsecret;
        }
        $merchant = $model;
        $jssdk = new JSSDK($merchant);
        $signPackage = $jssdk->GetSignPackage();
        return json_encode($signPackage);
    }

    /**
     * 上传图片
     */
    public function WxUploadImage($merchant_id='',$encrypt_id='') {
        //数据库查询
        if(!empty($encrypt_id)){
            $model = Merchant::model() -> find('encrypt_id = :encrypt_id and flag =:flag',array(
                ':encrypt_id' => $encrypt_id,
                ':flag' => FLAG_NO
            ));
        }elseif (!empty($merchant_id)){
            $model = Merchant::model() -> find('id = :id and flag =:flag',array(
                ':id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
        }
        if (!empty($model)){
            $appid = $model -> wechat_subscription_appid;
            $appsecret = $model -> wechat_subscription_appsecret;
        }
        $merchant = $model;
        $jssdk = new JSSDK($merchant);
        $signPackage = $jssdk->GetSignPackage();
        return json_encode($signPackage);
    }


}