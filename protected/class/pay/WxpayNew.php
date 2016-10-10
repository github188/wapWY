<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class WxpayNew extends mainClass{
    /**
     * ΢��֧���첽֪ͨ��֤
     * @param unknown $order_no
     * @throws Exception
     * @return string
     */
    public function wxpayVerifyNotify($merchant_id) {
        $result = array();
        try {
            //��ȡ�տ���Ϣ
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
            $result['errMsg'] = $e->getMessage(); //������Ϣ
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
        //��ȡ�տ���Ϣ
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
     * ��ȡ�̻���Ϣ
     */
    private function getMerchantInfo($merchant_id) {
        $merchant = Merchant::model()->findByPk($merchant_id);
        $result = array();
        $wxpay_merchant_type = ''; //΢���̻�����
        $wxpay_appid = ''; //΢��appid
        $wxpay_appsecret = ''; //΢��Ӧ����Կ
        $wxpay_api_key = ''; //΢��API��Կ
        $wxpay_mchid = ''; //΢���̻���
        $wxpay_apiclient_cert = ''; //cert�ļ�·��
        $wxpay_apiclient_key = ''; //key�ļ�·��

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
            $result['status'] = ERROR_NONE; //״̬��
            $result['errMsg'] = ''; //������Ϣ
        }

        return json_encode($result);
    }

    /**
     * ֪ͨ������֤
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

        if ($type == WXPAY_MERCHANT_TYPE_SELF) { //�����̻�
            //�̻���Ϣ����
            $wxpay_config['APPID'] = $appid;
            $wxpay_config['MCHID'] = $mchid;
            $wxpay_config['KEY'] = $key;
            $wxpay_config['SSLCERT_PATH'] = $cert_path;
            $wxpay_config['SSLKEY_PATH'] = $key_path;
        }

        //��־��¼
// 		error_reporting(E_ERROR);
// 		//��ʼ����־
// 		$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
// 		$log = Log::Init($logHandler, 15);

        $msg = "OK";
        //�ӿ���
        $api = new WxPayApi();
        $api->wxpay_config = $wxpay_config;
        $result = $api->notify(null, $msg);

        return array('result' => $result, 'msg' => $msg, 'type' => $type);
    }

    /**
     * ֪ͨ�ظ�
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

        if ($type == WXPAY_MERCHANT_TYPE_SELF) { //�����̻�
            //�̻���Ϣ����
            $wxpay_config['APPID'] = $appid;
            $wxpay_config['MCHID'] = $mchid;
            $wxpay_config['KEY'] = $key;
        }

        //������
        $notify = new WxPayNotifyReply();
        $notify->wxpay_config = $wxpay_config;

        if($result == true){
            $notify->SetReturn_code(_SUCCESS);
            $notify->SetReturn_msg($msg);
        } else {
            $notify->SetReturn_code(_FAIL);
            $notify->SetReturn_msg($msg);
        }


        //�����Ҫǩ��
        if($needSign == true &&
            $notify->GetReturn_code() == _SUCCESS)
        {
            $notify->SetSign();
        }
        WxpayApi::replyNotify($notify->ToXml());
    }


}