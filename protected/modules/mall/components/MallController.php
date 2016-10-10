<?php
include_once(realpath(dirname(__FILE__).'/../../../../protected/config').'/constant.php');
include_once(realpath(dirname(__FILE__).'/../../../../protected/class/config').'/constant.php');
include_once(realpath(dirname(__FILE__).'/../../../../protected/class/config').'/codeMsg.php');

include_once(realpath(dirname(__FILE__).'/../../../../protected/extensions/wxpay/lib').'/WxPay.Api.php');

class MallController extends CController
{
    public $layout='main';
    public $menu=array();
    public $breadcrumbs=array();
    
     /**
     * 增加访问记录 统计访问数据
     */
    protected function addVisitor(){
        $ip_address = Yii::app() -> request -> userHostAddress;//ip地址
        $res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip_address);
        $address = json_decode($res, true);//地址信息
        $pv_url = 'http://'.$_SERVER['HTTP_HOST'].Yii::app() -> request -> url; //pv页面url
        $come_url = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'无';//来源网站url
        $nowDate = date('Y-m-d',time());
        $merchantId = Yii::app()-> session['merchant_id'];

        $pv = Pv::model() -> find('visit_date like "%:visit_date%"',array(':visit_date' => $nowDate));

        if(!isset(Yii::app() -> session['comes'])){
            //如果session 中无comes
            Yii::app() -> session['comes'] = $ip_address.';'.$come_url.';'.$pv_url;
        }

        if(empty($pv)){//如果ip表当天没有该ip地址的记录
            $pv = new Pv();//新的pv对象
        }
        if(isset($address['code']) && $address['code'] == '0') {
            if($address['data']['region'] == ''){
                $pv -> address = $address['data']['country'];
            }else{
                $pv -> address = $address['data']['region'].$address['data']['city'];
            }
        }
        $pv -> ip = $ip_address;
        $pv -> come_url = $come_url;
        $pv -> pv_url = $pv_url;
        $pv -> head = json_encode($_SERVER);
        $pv -> merchant_id = $merchantId;
        $pv -> visit_date = new CDbExpression('NOW()');
        $pv -> save();
    }

    public function init(){
        //$this -> addVisitor();
    }
}

