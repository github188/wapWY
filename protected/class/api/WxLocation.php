<?php
include_once(realpath(dirname(__FILE__) . '/../../../protected/extensions/wxjssdk') . '/jssdk.php');
include_once(dirname(__FILE__) . '/../mainClass.php');

class WxLocation extends mainClass
{

    //微信内置地图
    /*
     * 传入的经纬度数据为百度地图所使用标准
     * @param float $lat 维度
     * @param float $lng 经度
     */
    public function getWxLocation($lat, $lng, $merchant)
    {
        $jssdk = new JSSDK($merchant);
        $signPackage = $jssdk->GetSignPackage();
        $localtion = $jssdk->getLocation($lat, $lng);
        $result = array('signPackage' => $signPackage, 'location' => $localtion);

        return json_encode($result);
    }

    public function Wxshare($merchant)
    {
        $jssdk = new JSSDK($merchant);
        $signPackage = $jssdk->GetSignPackage();
        return json_encode($signPackage);
    }

    public function Wxcard($merchant, $card_id, $code = '', $open_id = '')
    {
        $jssdk = new JSSDK($merchant);
        $signPackage = $jssdk->GetCardSignPackage($card_id, $code, $open_id);
        return json_encode($signPackage);
    }
}

