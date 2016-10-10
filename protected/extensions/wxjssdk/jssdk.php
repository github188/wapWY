<?php
include_once(dirname(__FILE__) . '/../../class/api/WxJssdk.php');
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/components/Component.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/Wechat.php';

class JSSDK
{
    private $appId;
    private $merchant;

    public function __construct($merchant)
    {
        if (isset($merchant->wechat_thirdparty_authorizer_if_auth) && $merchant->wechat_thirdparty_authorizer_if_auth == 2) {
            $this->appId = $merchant->wechat_thirdparty_authorizer_appid;
        } else {
            $this->appId = $merchant->wechat_subscription_appid;
        }
        $this->merchant = $merchant;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
        );
        return $signPackage;
    }

    public function getCardSignPackage($card_id, $code = '', $open_id = '')
    {
        $apiTicket = $this->getApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        $arr = array(
            $apiTicket,
            $timestamp,
            $nonceStr,
            $card_id
        );

        if (!empty($code)) {
            $arr[] = $code;
        }

        if (!empty($open_id)) {
            $arr[] = $open_id;
        }

        sort($arr, SORT_STRING);

        $string = '';
        foreach ($arr as $v) {
            $string .= $v;
        }

        $signature = sha1($string);
        
        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
        );

        if (!empty($code)) {
            $signPackage['code'] = $code;
        }

        if (!empty($open_id)) {
            $signPackage['openid'] = $open_id;
        }

        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

//   private function getJsApiTicket() {

//     $merchant_id = Yii::app()->session['merchant_id'];
//     $wxjssdk = new WxJssdk();
//     $data = $wxjssdk ->getJsapiTicket($merchant_id);

//     $user_id = $data['id']; 

//     if (empty($data['jsapi_ticket_json']) || $data['jsapi_ticket_json']->expire_time < time()) {
//       $accessToken = $this->getAccessToken($merchant_id);

//       CVarDumper::dump($accessToken);
//       exit();

//       $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
//       $res = json_decode($this->httpGet($url));
//       $ticket = $res->ticket;
//       if ($ticket) {
//         $save->expire_time = time() + 7000;
//         $save->jsapi_ticket = $ticket;
//         $jsapi_ticket_json = json_encode($save);
//         $wxjssdk ->setJsapiTicket($user_id, $jsapi_ticket_json);
//       }
//     } else {
//       $ticket = $data['jsapi_ticket_json']->jsapi_ticket;
//     }
//     return $ticket;
//   }

//   private function getAccessToken($merchant_id) {

//     $wxjssdk = new WxJssdk();
//     $data = $wxjssdk -> getWxAccessToken($merchant_id);
//     $user_id = $data['id'];

//     if (empty($data['access_token_json']) || $data['access_token_json']->expire_time < time()) {
//       $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
//       $res = json_decode($this->httpGet($url));

//       $access_token = $res->access_token;
//       if ($access_token) {
//         $save->expire_time = time() + 7000;
//         $save->access_token = $access_token;
//         $access_token_json = json_encode($save);
//         $wxjssdk -> setWxAccessToken($user_id, $access_token_json);
//       }
//     } else {
//       $access_token = $data['access_token_json']->access_token;
//     }
//     return $access_token;
//   }

    private function getJsApiTicket()
    {
        $accessToken = $this->getAccessToken();
        $ticket = $this->getJsapiTicketJson($accessToken);

        return $ticket;
    }

    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /*
    *百度地图坐标转中国GCJ02坐标，微信内腾讯地图使用GCJ02坐标。
    *@param lat 纬度
    *@param lng 经度
    */
    public function getLocation($lat, $lng)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);
        $location = array('lng' => $lng, 'lat' => $lat);
        return $location;
    }

    /**
     *  获取微信token
     */
    public function getAccessToken()
    {
        $merchant = $this->merchant;
        $access_token = Wechat::getTokenByMerchant($merchant);
        return $access_token;
    }

    public function getToken()
    {
        $accessToken = Yii::app()->memcache->get('access_token');
        $time = time();
        $expire = Yii::app()->memcache->get('expire');
        $isexpire = $time - $expire;
        if ($accessToken == null) {
            $accessToken = $this->setToken();
            return $accessToken;
        } else if ($isexpire > 7190) {
            $accessToken = $this->setToken();
            return $accessToken;
        }
        return $accessToken;
    }


//原token获取方式，现在改用以上获取方式
    /**
     * 把token存储到memcached
     */
    public function setToken()
    {
        $merchant = $this->merchant;
        $appid = $merchant['wechat_appid'];
        $appsecret = $merchant['wechat_appsecret'];
        //原来获取access_token方式
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode($this->httpGet($url));
        $access_token = $res->access_token;
        if (!empty($access_token)) {
            return $access_token;
            $expire = time();
            Yii::app()->memcache->set('access_token', $access_token, $expire);
        }
    }

    /**
     *  获取微信jsapi_ticket_json
     */
    public function getJsapiTicketJson($accessToken)
    {
        $ticket = '';
        $this->setJsapiTicketJson($accessToken);
        $ticket = Yii::app()->memcache->get('jsapi_ticket_json');
        return $ticket;
    }

    /**
     * 把jsapi_ticket_json存储到memcached
     */
    public function setJsapiTicketJson($accessToken)
    {
        $ticket = Yii::app()->memcache->get('jsapi_ticket_json');
        if ($ticket == null) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if (!empty($ticket)) {
                $expire = 7190;
                Yii::app()->memcache->set('jsapi_ticket_json', $ticket, $expire);
            }
        }
    }

    /**
     * 获取卡券api_ticket
     */
    public function getApiTicket()
    {
        $accessToken = $this->getAccessToken();
        $this->setApiTicket($accessToken);
        $api_ticket = Yii::app()->memcache->get('api_ticket');

        return $api_ticket;
    }

    /**
     * 设置卡券api_ticket
     */
    public function setApiTicket($accessToken)
    {
        $api_ticket = Yii::app()->memcache->get($this->merchant['wechat_appid'].'api_ticket');
        if ($api_ticket == null) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $accessToken . "&type=wx_card";
            $res = json_decode($this->httpGet($url));
            $api_ticket = $res->ticket;
            if (!empty($api_ticket)) {
                $expire = 7190;
                Yii::app()->memcache->set($this->merchant['wechat_appid'].'api_ticket', $api_ticket, $expire);
            }
        }
    }

}

