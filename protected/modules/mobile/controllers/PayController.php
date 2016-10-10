<?php
class PayController extends MobileController{
    /**
     * 物业 水费、电费 订单支付
     */
    public function actionWyOrderPay()
    {
        if (isset($_GET['order_id']) && !empty($_GET['order_id']) && isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            //订单id
            $order_id = $_GET['order_id'];
            //商户加密
            $encrypt_id = $_GET['encrypt_id'];
            //用户id
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            //用户openid
            $open_id = Yii::app()->session[$encrypt_id . 'open_id'];
            $jsApiParameters = '';
            $feeOrder = new FeeOrderC();
            $result = json_decode($feeOrder->getFeeOrderInfo($order_id, $user_id));
            //订单状态是未支付
            if ($result->status == APPLY_CLASS_SUCCESS && $result->data->pay_status == ORDER_STATUS_UNPAID) {
                
                $order_no      = $result->data->order_no;
                $merchant_id   = $result->data->merchant_id;
                $order_money   = $result->data->order_money;
                $pay_money = $result->data->pay_money;
                $order_type = $result->data->order_type;
                //用户预存金额
                $money = 0;
                $proprietorC = new ProprietorC();
                $result = $proprietorC -> getProprietorInfo($user_id);
                if ($result['status'] == APPLY_CLASS_SUCCESS) {
                    $money = $result['data']['money'];
                }
                //判断用户是否有预存金额
                if ($money >0) {
                    $transaction = Yii::app ()->db->beginTransaction ();
                    try {
                        //支付金额 小于等于  用户预存金额
                        if ($pay_money <= $money) {
                            //用户预存金额减少
                            $result = $proprietorC->editUserMoney($user_id, $pay_money);
                            //修改订单支付金额
                            $pay_money = 0;
                            $result1 = $feeOrder->editFeeOrderPayMoney($order_no, $pay_money);
                            //订单支付成功
                            $result2 = $feeOrder->editFeeOrder($order_no, ORDER_PAY_CHANNEL_STORED, '');
                        }

                        //支付金额  大于  用户预存金额
                        if ($pay_money > $money) {
                            //用户预存金额减少
                            $result = $proprietorC->editUserMoney($user_id, $money);
                            //支付金额 =  支付金额 - 用户预存金额
                            $pay_money = $pay_money - $money;
                            $feeOrder->editFeeOrderPayMoney($order_no, $pay_money);
                        }

                        $transaction->commit ();
                    } catch ( Exception $e ) {
                        $transaction->rollback ();
                    }
                }
                

                //如果支付金额大于0
                if($pay_money > 0) {
                    //调用微信支付
                    $url = WAP_DOMAIN.'/mobile/notify/WyOrderNotify';
                    $wxjspay = new WxpayC();
                    $ret = $wxjspay->NewWxJsPay($order_no, '物业订单', $merchant_id, $url, $open_id, $pay_money);
                    $result1 = json_decode($ret, true);

                    //水费
                    if($order_type == FEEORDER_TYPE_WATER_FEE) {
                        $goUrl = Yii::app()->createUrl('mobile/property/Fee/WaterFeeList', array('encrypt_id' => $encrypt_id));
                    }

                    //电费
                    if($order_type == FEEORDER_TYPE_ELECTRICITY_FEE) {
                        $goUrl = Yii::app()->createUrl('mobile/property/Fee/PowerFeeList', array('encrypt_id' => $encrypt_id));
                    }

                    if ($result1['status'] == ERROR_NONE) {
                        $jsApiParameters = $result1['data'];
                        $this->render('wyOrderPay', array(
                            'jsApiParameters' => $jsApiParameters,
                            'goUrl' => $goUrl
                        ));
                    } else {
                        $msg = $result1['errMsg'];
                    }
                } else {
                    //支付成功
                    $this->redirect(Yii::app()->createUrl('mobile/property/Fee/PaySuccess', array('encrypt_id' => $encrypt_id)));
                }
            }
        }
    }

    /**
     * 物业 停车费、物业费 订单支付
     */
    public function actionWyFeeOrderPay()
    {
        if (isset($_GET['order_id']) && !empty($_GET['order_id']) && isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            //订单id
            $order_id = $_GET['order_id'];
            //商户加密
            $encrypt_id = $_GET['encrypt_id'];
            //用户id
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            //用户openid
            $open_id = Yii::app()->session[$encrypt_id . 'open_id'];
            $jsApiParameters = '';
            $feeOrder = new FeeOrderC();
            $result = json_decode($feeOrder->getFeeOrderInfo($order_id, $user_id));
            //订单状态是未支付
            if ($result->status == APPLY_CLASS_SUCCESS && $result->data->pay_status == ORDER_STATUS_UNPAID) {
                $order_no      = $result->data->order_no;
                $merchant_id   = $result->data->merchant_id;
                $order_money   = $result->data->order_money;
                $pay_money = $result->data->pay_money;
                $order_type = $result->data->order_type;

                //调用微信支付
                $url = WAP_DOMAIN.'/mobile/notify/WyOrderNotify';
                $wxjspay = new WxpayC();
                $ret = $wxjspay->NewWxJsPay($order_no, '物业订单', $merchant_id, $url, $open_id, $pay_money);
                $result1 = json_decode($ret, true);

                if ($result1['status'] == ERROR_NONE) {
                    //物业费
                    if($order_type == FEEORDER_TYPE_PROPERTY_FEE) {
                        $goUrl = Yii::app()->createUrl('mobile/property/Fee/PropertyFeeList', array('encrypt_id' => $encrypt_id));
                    }

                    //停车费
                    if($order_type == FEEORDER_TYPE_PARKING_FEE) {
                        $goUrl = Yii::app()->createUrl('mobile/property/Fee/PaySuccess', array('encrypt_id' => $encrypt_id));
                    }

                    $jsApiParameters = $result1['data'];
                    $this->render('wyFeeOrderPay', array(
                        'jsApiParameters' => $jsApiParameters,
                        'goUrl' => $goUrl
                    ));
                } else {
                    $msg = $result1['errMsg'];
                }
            } else {
                if($result->data->order_type == FEEORDER_TYPE_PROPERTY_FEE) {
                    $this->redirect(Yii::app()->createUrl('mobile/property/Fee/PropertyFeeList', array('encrypt_id' => $encrypt_id)));
                }

                if ($result->data->order_type == FEEORDER_TYPE_PARKING_FEE) {
                    $this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/ParkingFeeList', array('encrypt_id' => $encrypt_id)));
                }
            }
        }
    }

    /**
     * 物业预存金充值订单支付
     */
    public function actionWyPrestoreOrderPay() {
        if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
            //订单id
            $order_id = $_GET['order_id'];
            //商户加密
            $encrypt_id = $_GET['encrypt_id'];
            //用户id
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            //用户openid
            $open_id = Yii::app()->session[$encrypt_id . 'open_id'];
            $jsApiParameters = '';
            $prestoreOrder = new PrestoreOrderC();
            $result = json_decode($prestoreOrder->getPrestoreOrderInfo($order_id, $user_id));

            if ($result->status == APPLY_CLASS_SUCCESS) {
                $order_no    = $result->data->order_no;
                $merchant_id = $result->data->merchant_id;
                $money =  $result->data->order_money;

                $url = WAP_DOMAIN.'/mobile/notify/WyPrestoreOrderNotify';
                //调用微信支付
                $wxjspay = new WxpayC();
                $ret = $wxjspay->NewWxJsPay($order_no, '物业订单', $merchant_id, $url, $open_id,$money);
                $result1 = json_decode($ret, true);

                if ($result1['status'] == ERROR_NONE) {
                    $jsApiParameters = $result1['data'];

                    $goUrl = Yii::app()->createUrl('mobile/property/Prestore/PrestoreRecord', array('encrypt_id' => $encrypt_id));

                    $this->render('wyPrestoreOrderPay', array(
                        'jsApiParameters' => $jsApiParameters,
                        'goUrl' => $goUrl
                    ));
                } else {
                    $msg = $result1['errMsg'];
                }
            }
        }
    }
}