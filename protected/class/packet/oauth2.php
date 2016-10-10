<?php
class Wxapi {
    private $app_id = 'wxec84afe11d9da7c4';
    private $app_secret = 'd41af6b0d7efdfb5fb925db13b205533';
    private $app_mchid = '1261382301';
    function __construct(){
    //do sth here....
    }
    /**
     * 微信支付
     * 
     * @param string $openid 用户openid
     */
    public function pay($re_openid,$db=null)
    {
        include_once('WxHongBaoHelper.php');
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();

        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，勿长于 32 位
        $wxHongBaoHelper->setParameter("mch_billno", $this->app_mchid.date('YmdHis').rand(1000, 9999));//订单号
        $wxHongBaoHelper->setParameter("mch_id", $this->app_mchid);//商户号
        $wxHongBaoHelper->setParameter("wxappid", $this->app_id);
        $wxHongBaoHelper->setParameter("nick_name", '玩券');//提供方名称
        $wxHongBaoHelper->setParameter("send_name", '玩券管家');//红包发送者名称
        $wxHongBaoHelper->setParameter("re_openid", $re_openid);//相对于app_id的openid
        $wxHongBaoHelper->setParameter("total_amount", 880);//付款金额，单位分
        $wxHongBaoHelper->setParameter("min_value", 880);//最小红包金额，单位分
        $wxHongBaoHelper->setParameter("max_value", 880);//最大红包金额，单位分
        $wxHongBaoHelper->setParameter("total_num", 1);//红包収放总人数
        $wxHongBaoHelper->setParameter("wishing", '恭喜发财');//红包祝福诧
        $wxHongBaoHelper->setParameter("client_ip", '127.0.0.1');//调用接口的机器 Ip 地址
        $wxHongBaoHelper->setParameter("act_name", '发布会互动活动');//活劢名称
        $wxHongBaoHelper->setParameter("remark", '快来抢！');//备注信息
        $postXml = $wxHongBaoHelper->create_hongbao_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);

		$responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);

		return $responseObj;
    }
   	
    /**
     * Http方法
     * 
     */ 
    public function http($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);//输出内容
        curl_close($ch);
        return array($output);
    }   

    /**
     * 生成随机数
     * 
     */     
    public function great_rand(){
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;    
    }
}
?>