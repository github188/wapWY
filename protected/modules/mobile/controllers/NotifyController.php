<?php
class NotifyController extends MobileController{
    public function init() {

    }

    /**
     *  预存金微信通知消息
     */
    public function actionWyPrestoreOrderNotify() {
        $xml = file_get_contents("php://input");
        //xml解析
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $out_trade_no = $arr['out_trade_no']; //获取订单号
        $result_code = $arr['result_code']; //业务结果码
        $trade_no = $arr['transaction_id']; //微信支付订单号
        $open_id = $arr['openid']; //用户标识
        $sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //用户子标识
        $pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');

        //获取订单信息
        $prestoreOrder = new PrestoreOrderC();
        $result = json_decode($prestoreOrder->getPrestoreOrderInfo('','', $out_trade_no));
        if ($result->status == APPLY_CLASS_SUCCESS) {
            //订单是未付款
            if($result->data->pay_status == ORDER_STATUS_UNPAID) {
                //验签
                $api = new WxpayNew();
                $verify_result = json_decode($api->wxpayVerifyNotify($result->data->merchant_id));
                if ($verify_result->status == ERROR_NONE) {
                    //业务逻辑修改
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
     *水费 电费微信通知消息
     */
    public function actionWyOrderNotify() {
        $xml = file_get_contents("php://input");
        //xml解析
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $out_trade_no = $arr['out_trade_no']; //获取订单号
        $result_code = $arr['result_code']; //业务结果码
        $trade_no = $arr['transaction_id']; //微信支付订单号
        $open_id = $arr['openid']; //用户标识
        $sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //用户子标识
        $pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');

        //获取订单信息
        $feeOrderC = new FeeOrderC();
        $result = json_decode($feeOrderC->getFeeOrderInfo('','', $out_trade_no));
        if ($result->status == APPLY_CLASS_SUCCESS) {
            //订单是未付款
            if($result->data->pay_status == ORDER_STATUS_UNPAID) {
                //验签
                $api = new WxpayNew();
                $verify_result = json_decode($api->wxpayVerifyNotify($result->data->merchant_id));
                if ($verify_result->status == ERROR_NONE) {
                    //业务逻辑修改
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