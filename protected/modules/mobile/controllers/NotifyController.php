<?php
class NotifyController extends MobileController{
    public function init() {

    }

    /**
     *  Ԥ���΢��֪ͨ��Ϣ
     */
    public function actionWyPrestoreOrderNotify() {
        $xml = file_get_contents("php://input");
        //xml����
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $out_trade_no = $arr['out_trade_no']; //��ȡ������
        $result_code = $arr['result_code']; //ҵ������
        $trade_no = $arr['transaction_id']; //΢��֧��������
        $open_id = $arr['openid']; //�û���ʶ
        $sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //�û��ӱ�ʶ
        $pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');

        //��ȡ������Ϣ
        $prestoreOrder = new PrestoreOrderC();
        $result = json_decode($prestoreOrder->getPrestoreOrderInfo('','', $out_trade_no));
        if ($result->status == APPLY_CLASS_SUCCESS) {
            //������δ����
            if($result->data->pay_status == ORDER_STATUS_UNPAID) {
                //��ǩ
                $api = new WxpayNew();
                $verify_result = json_decode($api->wxpayVerifyNotify($result->data->merchant_id));
                if ($verify_result->status == ERROR_NONE) {
                    //ҵ���߼��޸�
                    $update_result = json_decode($prestoreOrder->updatePrestoreOrder($out_trade_no, ORDER_PAY_CHANNEL_WXPAY, $trade_no));
                    if ($update_result->status == APPLY_CLASS_SUCCESS) {
                       $update_flag = true;
                    } else {
                        $update_flag = false;
                    }

                    $api->wxpayReply($result->data->merchant_id, $update_flag, '');
                }
            }
        }
    }

    /**
     *ˮ�� ���΢��֪ͨ��Ϣ
     */
    public function actionWyOrderNotify() {
        $xml = file_get_contents("php://input");
        //xml����
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $out_trade_no = $arr['out_trade_no']; //��ȡ������
        $result_code = $arr['result_code']; //ҵ������
        $trade_no = $arr['transaction_id']; //΢��֧��������
        $open_id = $arr['openid']; //�û���ʶ
        $sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //�û��ӱ�ʶ
        $pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');

        //��ȡ������Ϣ
        $feeOrderC = new FeeOrderC();
        $result = json_decode($feeOrderC->getFeeOrderInfo('','', $out_trade_no));
        if ($result->status == APPLY_CLASS_SUCCESS) {
            //������δ����
            if($result->data->pay_status == ORDER_STATUS_UNPAID) {
                //��ǩ
                $api = new WxpayNew();
                $verify_result = json_decode($api->wxpayVerifyNotify($result->data->merchant_id));
                if ($verify_result->status == ERROR_NONE) {
                    //ҵ���߼��޸�
                    $update_result = json_decode($feeOrderC->editFeeOrder($out_trade_no, ORDER_PAY_CHANNEL_WXPAY, $trade_no));
                    if ($update_result->status == ERROR_NONE) {
                        $update_flag = true;
                    } else {
                        $update_flag = false;
                    }

                    $api->wxpayReply($update_flag, '', $result->data->merchant_id);
                }
            }
        }
    }

}