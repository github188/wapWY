<?php
/**
 * User:  钮飞虎
 * Date: 2015/7/29
 * Time: 12:52
 */
class WappayController extends UCenterController
{

    public $layout = '';

    /**
     * Wap支付宝
     */
    public function actionWappay()
    {
        $encrypt_id = $_POST['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $payway = $_POST['pay'];
        $user = new UserUC();
        $data_stored = array();
        $merchant = $user->getMerchantWithId($encrypt_id);
        $merchantId = $merchant->id;

        //查询商户pid
        $result_merchant = $user->findMerchantPid($merchantId);
        $storedorder_id = Yii::app()->session['saved_storedorder_id'];

        //查询保存储值的记录和对应充值钱得到钱
        $rs = $user->queryWappayInfomation($storedorder_id);
        $result = json_decode($rs, true);
        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $data_stored = $result['data'];
            }
        } else {
            $status = $result['status'];
            $msg = $result['errMsg'];
        }

        $Wappay = new WappayC();
        $productName = '充' . $data_stored['stored_money'] . '送' . $data_stored['get_money'];
        $synNotifyUrl = WAPPAY_WQ_SYNNOTIFY;
        $asyNotifyUrl = WAPPAY_WQ_ASYNOTIFY;
        $showUrl = null;
        $sellerId = $result_merchant->partner;
        $key = $result_merchant->key;//安全校验码
        $num = $data_stored['num'];
        $money = floatval($data_stored['stored_money']) * intval($num);
        $order_no = $data_stored['order_no'];
        if ($payway == 1) {
            //选择支付宝支付
            $Wappay->ToWappay($order_no, $productName, $synNotifyUrl, $asyNotifyUrl, $showUrl, $sellerId, $data_stored['num'], $money, $key);
        } else if ($payway == 2) {
            //选择微信支付
        }
    }

    //商城支付宝支付
    public function actionScWappay()
    {
        $encrypt_id = $_POST['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $order_no = $_POST['order_no'];

        $orderMall = new OrderMall();
        $result = json_decode($orderMall->getOrderInfo('', $order_no));
        $money = $result->data->order_paymoney;

        //查询商户pid
        $user = new UserUC();
        $merchant = $user->getMerchantWithId($encrypt_id);
        $merchantId = $merchant->id;
        $result_merchant = $user->findMerchantPid($merchantId);

        $synNotifyUrl = WAPPAY_WQ_SYNNOTIFY;
        $asyNotifyUrl = WAPPAY_WQ_ASYNOTIFY;
        $sellerId = $result_merchant->partner;
        $key = $result_merchant->key;
        $num = 1;

        //调用支付宝接口
        $Wappay = new WappayC();
        $Wappay->ToWappay($order_no, '商城订单', $synNotifyUrl, $asyNotifyUrl, '', $sellerId, $num, $money, $key);
    }

}
?>