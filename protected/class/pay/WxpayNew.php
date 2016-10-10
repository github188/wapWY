<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class WxpayNew extends mainClass{
    /**
     * 微信支付异步通知验证
     * @param unknown $order_no
     * @throws Exception
     * @return string
     */
    public function wxpayVerifyNotify($merchant_id) {
        $result = array();
        try {
            //获取收款信息
            $ret = $this->getMerchantInfo($merchant_id);
            $seller_info = json_decode($ret, true);
            if ($seller_info['status'] != ERROR_NONE) {
                $result['status'] = $seller_info['status'];
                throw new Exception($seller_info['errMsg']);
            }
            $info = $seller_info['data'];
            $appid = $info['wxpay_appid'];
            $mchid = $info['wxpay_mchid'];
            $key = $info['wxpay_api_key'];
            $merchant_type = $info['wxpay_merchant_type'];
            $cert_path = $info['wxpay_apiclient_cert'];
            $key_path = $info['wxpay_apiclient_key'];

            $response = $this->verifyNotify($appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
            if (!$response || !$response['result']) {
                $result['status'] = ERROR_REQUEST_FAIL;
                throw new Exception($response['msg']);
            }
 			$result['status'] = ERROR_NONE;
 			$result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $merchant_id
     * @param $flag
     * @param $msg
     * @throws Exception
     */
    public function wxpayReply($merchant_id, $flag, $msg) {
        //获取收款信息
        $ret = $this->getMerchantInfo($merchant_id);
        $seller_info = json_decode($ret, true);
        if ($seller_info['status'] != ERROR_NONE) {
            $result['status'] = $seller_info['status'];
            throw new Exception($seller_info['errMsg']);
        }
        $info = $seller_info['data'];
        $appid = $info['wxpay_appid'];
        $mchid = $info['wxpay_mchid'];
        $key = $info['wxpay_api_key'];
        $merchant_type = $info['wxpay_merchant_type'];
        $cert_path = $info['wxpay_apiclient_cert'];
        $key_path = $info['wxpay_apiclient_key'];

        $this->reply($flag, $msg, $appid, $mchid, $key, $merchant_type);
    }

    /**
     * 获取商户信息
     */
    private function getMerchantInfo($merchant_id) {
        $merchant = Merchant::model()->findByPk($merchant_id);
        $result = array();
        $wxpay_merchant_type = ''; //微信商户类型
        $wxpay_appid = ''; //微信appid
        $wxpay_appsecret = ''; //微信应用密钥
        $wxpay_api_key = ''; //微信API密钥
        $wxpay_mchid = ''; //微信商户号
        $wxpay_apiclient_cert = ''; //cert文件路径
        $wxpay_apiclient_key = ''; //key文件路径

        if ($merchant) {
            $wxpay_merchant_type = $merchant['wxpay_merchant_type'];
            if ($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                $wxpay_appid = $merchant['wechat_appid'];
                $wxpay_appsecret = $merchant['wechat_appsecret'];
                $wxpay_api_key = $merchant['wechat_key'];
                $wxpay_mchid = $merchant['wechat_mchid'];
                $wxpay_apiclient_cert = UPLOAD_SYSTEM_PATH . 'cert/' . $merchant['wechat_apiclient_cert'] . '/apiclient_cert.pem';
                $wxpay_apiclient_key = UPLOAD_SYSTEM_PATH . 'cert/' . $merchant['wechat_apiclient_key'] . '/apiclient_key.pem';
            } else {
                $wxpay_appid = $merchant['t_wx_appid'];
                $wxpay_mchid = $merchant['t_wx_mchid'];
            }

            $data = array(
                'wxpay_merchant_type' => $wxpay_merchant_type,
                'wxpay_appid' => $wxpay_appid,
                'wxpay_appsecret' => $wxpay_appsecret,
                'wxpay_mchid' => $wxpay_mchid,
                'wxpay_api_key' => $wxpay_api_key,
                'wxpay_apiclient_cert' => $wxpay_apiclient_cert,
                'wxpay_apiclient_key' => $wxpay_apiclient_key,
            );

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        }

        return json_encode($result);
    }

    /**
     * 通知请求验证
     * @param unknown $appid
     * @param unknown $mchid
     * @param unknown $key
     * @param unknown $type
     * @param unknown $cert_path
     * @param unknown $key_path
     * @return Ambigous <boolean, multitype:>
     */
    private function verifyNotify($appid, $mchid, $key, $type, $cert_path, $key_path) {
        Yii::import('application.extensions.wxpay.*');
        require_once "lib/WxPay.Api.php";
        require_once 'log.php';
        require 'wxpay.custom.php';

        if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
            //商户信息配置
            $wxpay_config['APPID'] = $appid;
            $wxpay_config['MCHID'] = $mchid;
            $wxpay_config['KEY'] = $key;
            $wxpay_config['SSLCERT_PATH'] = $cert_path;
            $wxpay_config['SSLKEY_PATH'] = $key_path;
        }

        //日志记录
// 		error_reporting(E_ERROR);
// 		//初始化日志
// 		$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
// 		$log = Log::Init($logHandler, 15);

        $msg = "OK";
        //接口类
        $api = new WxPayApi();
        $api->wxpay_config = $wxpay_config;
        $result = $api->notify(null, $msg);

        return array('result' => $result, 'msg' => $msg, 'type' => $type);
    }

    /**
     * 通知回复
     * @param unknown $result
     * @param unknown $appid
     * @param unknown $mchid
     * @param unknown $key
     * @param unknown $type
     * @param unknown $needSign
     */
    private function reply($result, $msg, $appid, $mchid, $key, $type, $needSign=TRUE) {
        Yii::import('application.extensions.wxpay.*');
        require_once "lib/WxPay.Api.php";
        require_once 'log.php';
        require 'wxpay.custom.php';

        if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
            //商户信息配置
            $wxpay_config['APPID'] = $appid;
            $wxpay_config['MCHID'] = $mchid;
            $wxpay_config['KEY'] = $key;
        }

        //数据类
        $notify = new WxPayNotifyReply();
        $notify->wxpay_config = $wxpay_config;

        if($result == true){
            $notify->SetReturn_code(_SUCCESS);
            $notify->SetReturn_msg($msg);
        } else {
            $notify->SetReturn_code(_FAIL);
            $notify->SetReturn_msg($msg);
        }


        //如果需要签名
        if($needSign == true &&
            $notify->GetReturn_code() == _SUCCESS)
        {
            $notify->SetSign();
        }
        WxpayApi::replyNotify($notify->ToXml());
    }


}