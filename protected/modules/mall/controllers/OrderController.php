<?php

/**
 * 购物车
 * */
class OrderController extends MallController
{
    /** 验证是否登陆
     * @param $encrypt_id
     */
    public function isLogin($encrypt_id)
    {
        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/auth/login', array('encrypt_id' => $encrypt_id)));
            return;
        }
    }


    //创建商城订单
    public function actionCreateOrder()
    {
        //验证是否登录
        if (isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
            $encrypt_id = $_POST['encrypt_id'];
            $merchant = new MerchantC();
            $re = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $re->data->id;
        }

        //判断是否已登录
        $this->isLogin($encrypt_id);

        if (isset($_GET['sku_id']) && !empty($_GET['sku_id'])) {
            $sku_id = $_GET['sku_id'];
            $num = $_GET['num'];
            $address = $_POST['address_id'];
            $remark = $_POST['remark'];
            $is_cart = $_POST['is_cart'];
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            $orderMall = new OrderMall();
            $result = json_decode($orderMall->createOrder($user_id, $sku_id, $num, $address, $is_cart, $remark, $merchant_id), true);
            if ($result['status'] == ERROR_NONE) {
                $this->redirect(array('OrderPay',
                    'order_id' => $result['data'],
                    'encrypt_id' => $encrypt_id
                ));
            } else {
                $url = Yii::app()->createUrl('mall/Commodity/index', array('encrypt_id' => $encrypt_id));
                echo "<script>alert('" . $result->errMsg . "');window.location.href='$url'</script>";
            }
        }
    }


    //订单支付
    public function actionOrderPay()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
            $jsApiParameters = '';
            $order_id = $_GET['order_id'];
            $orderMall = new OrderMall();
            $result = json_decode($orderMall->getOrderInfo($order_id));

            if ($result->status == ERROR_NONE) {
                $order_no = $result->data->order_no;
                $merchant = new MerchantC();
                $re = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
                $merchant_id = $re->data->id;

                //调用微信支付
                if (Yii::app()->session['source'] == 'wechat') {
                    $wxjspay = new WxpayC();
                    $ret = $wxjspay->WxJsPay($order_no, '商城订单', $merchant_id, WAPPAY_WQ_WECHAT_ASYNOTIFY);
                    $result1 = json_decode($ret, true);
                    if ($result1['status'] == ERROR_NONE) {
                        $jsApiParameters = $result1['data'];
                    }
                }

                $this->render('orderPay', array(
                    'order' => $result->data,
                    'jsApiParameters' => $jsApiParameters,
                    'encrypt_id' => $encrypt_id
                ));
            }
        }
    }

    //订单列表
    public function actionOrderList()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        $orderUc = new OrderUC();
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $merchant = new MerchantC();
        $re = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $re->data->id;

        $list = array();

        // 订单状态
        $order_status = ORDER_STATUS_NORMAL;
        if (isset ($_GET ['order_status']) && $_GET ['order_status']) {
            $order_status = $_GET ['order_status'];
        }

        // 订单支付状态
        $pay_status = ORDER_STATUS_UNPAID;
        if (isset ($_GET ['pay_status']) && $_GET ['pay_status']) {
            $pay_status = $_GET ['pay_status'];
        }

        $result = $orderUc->getOrderList($user_id, $order_status, $pay_status, $merchant_id);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $list = $result['data']['list'];
        }

        $unPaidCount = $orderUc->getPayStatusCount($user_id, ORDER_STATUS_UNPAID, $merchant_id); //获取待付款数量
        $waitCount = $orderUc->getOrderStatusCount($user_id, ORDER_STATUS_WAITFORDELIVER, $merchant_id); //获取待发货数量
        $deliverCount = $orderUc->getOrderStatusCount($user_id, ORDER_STATUS_DELIVER, $merchant_id); //获取已发货数量
        $completeCount = $orderUc->getOrderStatusCount($user_id, ORDER_STATUS_ACCEPT, $merchant_id); //获取已完成(已收货)数量
        $this->render('orderList', array(
            'list' => $list,
            'unPaidCount' => $unPaidCount,
            'waitCount' => $waitCount,
            'deliverCount' => $deliverCount,
            'completeCount' => $completeCount,
            'encrypt_id' => $encrypt_id
        ));
    }

    /** 订单详情
     * @param $order_id
     */
    public function actionOrderDetail($order_id)
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $orderUc = new OrderUC ();
        $address_arr = array();
        $list = array();

        $result = $orderUc->getOrderDetail($order_id, $user_id);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $list = $result['data'];
            if (!empty($result['data']['address'])) {
                $address_arr = explode(',', $result['data']['address']); //把订单地址拆分放入数组里
            }
        }
        $logistics = $GLOBALS['LOGISTICS_COMPANY'];
        $this->render('orderDetail', array(
            'list' => $list,
            'logistics' => $logistics,
            'address_arr' => $address_arr,
            'encrypt_id' => $encrypt_id
        ));
    }

    /** 取消订单
     * @param $order_id
     */
    public function actionCancleOrder($order_id)
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        $orderMall = new OrderMall();

        $result = $orderMall->cancleOrder($order_id);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $this->redirect(array('orderList', 'encrypt_id' => $encrypt_id));
        }
    }

    /** 修改地址
     * @param $order_id
     */
    public function actionEditAddress($order_id)
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        $liveplaceProvince = ''; //省份
        $liveplaceCity = ''; //城市
        $liveplaceArea = ''; //区域
        $postCode = ''; //邮政编码
        $streetAddress = ''; //街道地址
        $userName = ''; //收件人姓名
        $userPhone = ''; //手机号码

        $flag = false;

        $orderUc = new OrderUC ();

        if (isset($_GET['Selector']['liveplaceProvince']) && $_GET['Selector']['liveplaceProvince']) {
            $liveplaceProvince = $_GET['Selector']['liveplaceProvince'];
        }

        if (isset($_GET['Selector']['liveplaceCity']) && $_GET['Selector']['liveplaceCity']) {
            $liveplaceCity = $_GET['Selector']['liveplaceCity'];
        }

        if (isset($_GET['Selector']['liveplaceArea']) && $_GET['Selector']['liveplaceArea']) {
            $liveplaceArea = $_GET['Selector']['liveplaceArea'];
        }

        if (isset($_GET['post_code']) && $_GET['post_code']) {
            $postCode = $_GET['post_code'];
        }

        if (isset($_GET['street_address']) && $_GET['street_address']) {
            $streetAddress = $_GET['street_address'];
        }

        if (isset($_GET['user_name']) && $_GET['user_name']) {
            $userName = $_GET['user_name'];
        }

        if (isset($_GET['user_phone']) && $_GET['user_phone']) {
            $userPhone = $_GET['user_phone'];
        }

        if (!$flag) {
            $result = $orderUc->editAddress($order_id, $liveplaceProvince, $liveplaceCity, $liveplaceArea, $postCode, $streetAddress, $userName, $userPhone);
            $result = json_decode($result, true);
            if ($result['status'] == ERROR_NONE) {
                $this->redirect(Yii::app()->createUrl('mall/order/orderDetail', array(
                    'order_id' => $order_id,
                    'encrypt_id' => $encrypt_id
                )));
            }
        }
    }

    /**
     * ajax 获得更多订单信息
     */
    public function actionGetMoreOrder()
    {
        $orderUc = new OrderUC ();
        $order_id = '';

        if (isset($_POST['order_id']) && $_POST['order_id']) {
            $order_id = $_POST['order_id'];
        }
        $result = $orderUc->getMoreOrder($order_id);
        echo json_encode($result);
    }

    /**
     * 用户申请退款(实物订单)
     * @param $order_id       订单id
     * @param $order_sku_id   订单sku  Id
     * @param $order_status   订单状态
     */
    public function actionApplyRefundObj($order_id, $order_sku_id, $order_status)
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        $apply_type = array();
        $apply_type[''] = '请选择处理方式';
        $apply_type[ORDER_REFUND_STATUS_APPLY_REFUND_NORETURN] = '我要退款，但不退货';

        $list = array();
        $orderUc = new OrderUC ();
        $order_sku = $orderUc->getOrderSkuForId($order_sku_id);
        $result = $orderUc->getOrderDetail($order_id, '');
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $list = $result['data'];
        }
        if ($list['order_status'] != ORDER_STATUS_WAITFORDELIVER) {
            $apply_type[ORDER_REFUND_STATUS_APPLY_REFUND_RETURN] = '我要退款，并退货';
        }

        if (isset($_POST['OrderSku']) && !empty($_POST['OrderSku'])) {
            $post = $_POST['OrderSku'];
            $flag = true;
            if (empty($post['refund_reason'])) {
                $flag = false;
                Yii::app()->user->setFlash('refund_reason_error', '退款原因必填');
            }
            if (empty($post['refund_money'])) {
                $flag = false;
                Yii::app()->user->setFlash('refund_money_error', '退款金额必填');
            } else {
                $price = $orderUc->getSkuPrice($order_sku_id);
                if ($price < $post['refund_money']) {
                    $flag = false;
                    Yii::app()->user->setFlash('refund_money_error', '退款金额不大于付款金额');
                }
            }
            if (empty($post['refund_tel'])) {
                $flag = false;
                Yii::app()->user->setFlash('refund_tel_error', '手机号码必填');
            } else {
                $phoneCheck = preg_match(PHONE_CHECK, $post['refund_tel']);
                if (!$phoneCheck) {
                    $flag = false;
                    Yii::app()->user->setFlash('refund_tel_error', '手机号格式不正确');
                }
            }
            if (empty($post['status'])) {
                $flag = false;
                Yii::app()->user->setFlash('status_error', '处理方式必填');
            }

            if ($flag) {
                $orderUc2 = new OrderUC ();
                $result2 = $orderUc2->applyRefundObj($order_id, $order_sku_id, $order_status, $post['refund_reason'], $post['refund_money'], $post['refund_tel'], $post['status'], $post['refund_remark']);
                $result2 = json_decode($result2, true);
                if ($result2['status'] == ERROR_NONE) {
                    $this->redirect(Yii::app()->createUrl('mall/order/orderDetail', array(
                        'order_id' => $order_id,
                        'encrypt_id' => $encrypt_id
                    )));
                } else {
                    echo $result2['status'] . $result2['errMsg'];
                }
            }
        }

        $this->render('applyRefundObj', array(
            'list' => $list,
            'apply_type' => $apply_type,
            'order_sku' => $order_sku,
            'encrypt_id' => $encrypt_id
        ));
    }

    /** 用户确认收货
     * @param $order_id
     */
    Public function actionConfirmReceipt($order_id)
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);

        $orderUc = new OrderUC ();
        $result = $orderUc->confirmReceipt($order_id);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $this->redirect(Yii::app()->createUrl('mall/order/orderList', array(
                'order_status' => ORDER_STATUS_ACCEPT,
                'encrypt_id' => $encrypt_id
            )));
        }

    }

    /**
     * 用户填写退货物流信息
     */
    public function actionRefundMsg()
    {
        if (!empty($_POST)) {
            $encrypt_id = $_POST['encrypt_id'];
            //判断是否已登录
            $this->isLogin($encrypt_id);
            
            $flag = true;
            $tel = $_POST['Refund_tel'];
            $logistics = $_POST['Refund_logistics'];
            $ordernum = $_POST['Refund_ordernum'];
            $remark = $_POST['Refund_remark'];
            $skuid = $_POST['skuid'];
            $orderid = $_POST['orderid'];
            $orderstatus = $_POST['orderstatus'];

            if (empty($tel)) {
                $flag = false;
                Yii::app()->user->setFlash('error', '电话号码不能为空');
            } else {
                $phoneCheck = preg_match(PHONE_CHECK, $tel);
                if (!$phoneCheck) {
                    $flag = false;
                    Yii::app()->user->setFlash('error', '手机号格式不正确');
                }
            }

            $orderUc = new OrderUC();
            $result = json_decode($orderUc->RefundMsg($skuid, $orderid, $orderstatus, $tel, $logistics, $ordernum, $remark), true);
            if ($result['status'] == ERROR_NONE) {
                $this->redirect(Yii::app()->createUrl('mall/order/orderDetail', array(
                    'order_id' => $orderid,
                    'encrypt_id' => $encrypt_id
                )));
            } else {
                echo $result['errMsg'];
            }
        } else {
            //$this->redirect(Yii::app()->createUrl('mall/order/orderDetail',array('order_id'=>$orderid)));
        }
    }

    /**
     * 买家填写退货快递信息
     */
    public function actionRefundDetails()
    {
        if (!empty($_GET)) {
            $encrypt_id = $_GET['encrypt_id'];
            //判断是否已登录
            $this->isLogin($encrypt_id);
            
            $order_id = $_GET['order_id'];
            $order_sku_id = $_GET['order_sku_id'];
            $order_status = $_GET['order_status'];
            $orderUc = new OrderUC();
            $result = json_decode($orderUc->GetRefundDetails($order_id, $order_sku_id), true);
            $data = array();
            if ($result['status'] == ERROR_NONE) {
                $data = $result['data'];
            }
            $logistics = $GLOBALS['LOGISTICS_COMPANY'];
            $this->render('refundDetails', array(
                'data' => $data,
                'order_id' => $order_id,
                'logistics' => $logistics,
                'order_sku_id' => $order_sku_id,
                'order_status' => $order_status,
                'encrypt_id' => $encrypt_id
            ));
        }
    }

}