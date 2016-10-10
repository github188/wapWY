<?php

/**
 * 购物车
 * */
class OrderController extends DMallController
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
            $remark = $_POST['remark'];
            $is_cart = $_POST['is_cart'];
            $user_name = $_POST['name'];
            $phone = $_POST['phone'];
            $uid = $_POST['coupons_id'];
            $txtBirthday = isset($_POST['txtBirthday']) ? $_POST['txtBirthday'] : '';
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            $orderMall = new DOrderMall();
            $result = json_decode($orderMall->createOrder($user_id, $sku_id, $num, $is_cart, $remark, $merchant_id, $user_name, $phone, $txtBirthday, $uid));
            if ($result->status == ERROR_NONE) {
                $this->redirect(array('OrderPay',
                    'order_id' => $result->data,
                    'merchant_id' => $merchant_id,
                    'encrypt_id' => $encrypt_id
                ));
            } else {
                $url = Yii::app()->createUrl('Dmall/Commodity/index', array('encrypt_id' => $encrypt_id));
                echo "<script>alert('" . $result->errMsg . "');window.location.href='$url'</script>";
            }
        }
    }


    //订单支付
    public function actionOrderPay()
    {
        if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
            $order_id = $_GET['order_id'];
            $jsApiParameters = '';
            $orderMall = new DOrderMall();
            $result = json_decode($orderMall->getOrderInfo($order_id));
            if ($result->status == ERROR_NONE) {
                $order_no = $result->data->order_no;
                //$merchant_id = $_GET['merchant_id'];
                $encrypt_id = $_GET['encrypt_id'];

                //判断是否已登录
                $this->isLogin($encrypt_id);
                
                $merchant = new MerchantC();
                $res = $merchant->getMerchantByEncrypt($encrypt_id);
                $merchant_id = $res->data->id;

                $msg = '';
                if ($result->data->order_paymoney <= 0) {
                    $user = new UserUC();
                    $rs = json_decode($user->ScPaySuccess($order_no, NULL, ORDER_PAY_CHANNEL_NO_MONEY));

                    //第三方支付接口
                    $msg = $this->Thired($order_no, $rs->data);
                    $result = json_decode($msg, true);
                    $thirdorder = $result['info']['orders_id'];
                    $dordermall = new DOrderMall();
                    $dordermall->TianshiOrder($order_no, $thirdorder);

                    if ($rs->status == ERROR_NONE) {
                        $this->redirect(Yii::app()->createUrl('Dmall/order/paySuccess', array(
                            'money' => 0,
                            'ordertype' => 'SC',
                            'encrypt_id' => $encrypt_id
                        )));
                    }
                }
                //调用微信支付
                $wxjspay = new WxpayC();
                $ret = $wxjspay->WxJsPay($order_no, '商城订单', $merchant_id, DWAPPAY_WQ_WECHAT_ASYNOTIFY);
                $result1 = json_decode($ret, true);
                if ($result1['status'] == ERROR_NONE) {
                    $jsApiParameters = $result1['data'];
                } else {
                    $msg = $result1['errMsg'];
                }
                echo $msg;
                $this->render('orderPay', array(
                    'order' => $result->data,
                    'jsApiParameters' => $jsApiParameters,
                    'msg' => $msg,
                    'encrypt_id' => $encrypt_id
                ));
            }
        }
    }


    public function actionOrderList()
    {
        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断是否已登录
        $this->isLogin($encrypt_id);
        
        $orderUc = new DOrderMall();
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        
        $user = new UserUC();
        //获取商户信息
        $merchant_result = json_decode($user->getMerchant($encrypt_id));
        if ($merchant_result->status == ERROR_NONE) {
            $merchant_id = $merchant_result->data->id;
        }

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

    /**
     * 订单详情
     */
    public function actionOrderDetail()
    {
        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断是否已登录
        $this->isLogin($encrypt_id);

        $order_id = $_GET['order_id'];
        
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $orderUc = new DOrderMall ();
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

    /**
     * 订单取消
     */
    public function actionCancleOrder()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $order_id = $_GET['order_id'];
        //判断是否已登录
        $this->isLogin($encrypt_id);
        
        $orderMall = new DOrderMall();

        $result = $orderMall->cancleOrder($order_id);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $this->redirect(array('orderList', 'encrypt_id' => $encrypt_id));
        }
    }

    /**
     * 修改地址
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
            $result = $orderUc->editAddress($order_id, $liveplaceProvince, $liveplaceCity, $liveplaceArea,
                $postCode, $streetAddress, $userName, $userPhone);
            $result = json_decode($result, true);
            if ($result['status'] == ERROR_NONE) {
                $this->redirect(Yii::app()->createUrl('Dmall/order/orderDetail', array(
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
        $result = array();
        if (isset($_POST['order_id']) && $_POST['order_id']) {
            $order_id = $_POST['order_id'];
        }
        $result = $orderUc->getMoreOrder($order_id);
        echo json_encode($result);
    }

    /**
     * 用户申请退款(实物订单)
     * $order_id       订单id
     * $order_sku_id   订单sku  Id
     * $order_status   订单状态
     */
    public function actionApplyRefundObj($order_id, $order_sku_id, $order_status)
    {
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
//         if($list['order_status']!=ORDER_STATUS_WAITFORDELIVER)
//         {
//             $apply_type[ORDER_REFUND_STATUS_APPLY_REFUND_RETURN] = '我要退款，并退货';
//         }
        if (isset($_POST['OrderSku']) && !empty($_POST['OrderSku'])) {
            $post = $_POST['OrderSku'];
            $flag = true;
            if (empty($post['refund_reason'])) {
                $flag = false;
                Yii::app()->user->setFlash('refund_reason_error', '退款原因必填');
            }
            if (empty($_POST['ticket_num'])) {
                $flag = false;
                Yii::app()->user->setFlash('refund_money_error', '退票张数必填');
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
                $orderUc2 = new DOrderMall();
                $result2 = $orderUc2->applyRefundObj($order_id, $order_sku_id, $order_status, $post['refund_reason'], $_POST['ticket_num'], $post['refund_tel'], $post['status'], $post['refund_remark']);
                $result2 = json_decode($result2, true);
                if ($result2['status'] == ERROR_NONE) {
                    $this->redirect(Yii::app()->createUrl('Dmall/order/orderDetail', array('order_id' => $order_id, 'encrypt_id' => $_GET['encrypt_id'])));
                } else {
                    echo $result2['status'] . $result2['errMsg'];
                }
            }
        }

        $this->render('applyRefundObj', array(
            'list' => $list,
            'apply_type' => $apply_type,
            'order_sku' => $order_sku,
            'encrypt_id' => $_GET['encrypt_id']
        ));
    }

    /**
     * 用户确认收货
     */
    Public function actionConfirmReceipt()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $order_id = $_GET['order_id'];

        $orderUc = new OrderUC ();
        $result = $orderUc->confirmReceipt($order_id);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $this->redirect(Yii::app()->createUrl('Dmall/order/orderList', array(
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
        $result = array();
        if (!empty($_POST)) {
            $flag = true;
            $tel = $_POST['Refund_tel'];
            $logistics = $_POST['Refund_logistics'];
            $ordernum = $_POST['Refund_ordernum'];
            $remark = $_POST['Refund_remark'];
            $skuid = $_POST['skuid'];
            $orderid = $_POST['orderid'];
            $orderstatus = $_POST['orderstatus'];

            $encrypt_id = $_POST['encrypt_id'];

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
                $this->redirect(Yii::app()->createUrl('Dmall/order/orderDetail', array(
                    'order_id' => $orderid,
                    'encrypt_id' => $encrypt_id
                )));
            } else {
                echo $result['errMsg'];
            }
        } else {
//            $this->redirect(Yii::app()->createUrl('mall/order/orderDetail',array('order_id'=>$orderid)));
        }
    }

    /**
     * 买家填写退货快递信息
     */
    public function actionRefundDetails()
    {
        $result = array();
        if (!empty($_GET)) {
            $order_id = $_GET['order_id'];
            $order_sku_id = $_GET['order_sku_id'];
            $order_status = $_GET['order_status'];
            $encrypt_id = $_GET['encrypt_id'];
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

    /**
     * 微信支付同步修改订单号，储值金额
     */
    public function actionPaySuccess()
    {
        $merchant_name = '';
        if (isset($_GET['money'])) {
            $encrypt_id = $_GET['encrypt_id'];
            $merchant = new MerchantC();
            $res = $merchant->getMerchantByEncrypt($encrypt_id);
            $merchant_id = $res->data->id;

            if (!empty($merchant_id)) {
                $model = Merchant::model()->findByPK($merchant_id);
                if (!empty($model)) {
                    $merchant_name = $model->name;
                }
            }

            $qrcode = '';
            $merchantC = new MerchantC();
            $result = json_decode($merchantC->getMerchantByEncrypt('', TIANSHI_SHOP_API));
            if ($result->status == ERROR_NONE) {
                $qrcode = $result->data->wechat_qrcode;
            }

            if (isset(Yii::app()->session[$encrypt_id . 'user_id']) && !empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
                $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
                //判断是否关注公众号
                $userUC = new UserUC();
                $res = json_decode($userUC->checkIsFollowWechat('', '', '', $user_id));
                $if_follow = '';
                if ($res->status == ERROR_NONE) {
                    $if_follow = $res->data;
                }
            }

            if (isset($_GET['ordertype']) && $_GET['ordertype'] == 'SC') {
                $this->render('paySuccess', array(
                    'money' => $_GET['money'],
                    'ordertype' => $_GET['ordertype'],
                    'if_follow' => $if_follow,
                    'qrcode' => $qrcode,
                    'encrypt_id' => $encrypt_id
                ));
            } else {
                $this->render('paySuccess', array(
                    'money' => $_GET['money'],
                    'merchant_name' => $merchant_name,
                    'encrypt_id' => $encrypt_id
                ));
            }
        } else {
            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fail'));
        }
    }

    /**
     * 第三方支付接口
     * @param type $out_trade_no
     * @param type $rs
     */
    public function Thired($out_trade_no, $rs)
    {
        $dordermall = new DOrderMall();
        $thired = array();

        $res = json_decode($dordermall->OrderDetail($out_trade_no), true);
        if ($res['status'] == ERROR_NONE) {
            $thired = $res['data'];
            //天时下单
            if ($thired['third_party_source'] == SHOP_PRODUCT_THIRED_TIANSHI) {
                $ret = new TianShiApi();
                $item_id = $thired['item_id'];//必填 要购买的票ID
                $name = $thired['name'];//【必填】 购票人名称
                $mobile = $thired['mobile'];//必填 购票人手机号(成功后短信将发送门票码号到该手机号)
                $is_pay = '1';
                $orders_id = $out_trade_no;
                $size = $thired['num'];
                $start_date = isset($thired['txtBirthday']) ? $thired['txtBirthday'] : '';
                $start_date_auto = empty($thired['txtBirthday']) ? '1' : '';
                $price_type = $thired['sku_id'];
                $remark = '';
                $price = '';
                $back_cash = '';
                $id_number = '';
                $r = $ret->CreateOrder($item_id, $name, $mobile, $is_pay, $orders_id, $size, $start_date, $start_date_auto, $price_type, $remark, $price, $back_cash, $id_number);
                return $r;

            }
            //智游宝下单
            if ($thired['third_party_source'] == SHOP_PRODUCT_THIRED_ZHIYOUBAO) {
                $ret = new ZhiYouBaoApi();
                $txtBirthday = isset($thired['txtBirthday']) ? $thired['txtBirthday'] : '';
                $r = $ret->CreateOrder($thired['online_paymoney'], $out_trade_no, $thired['name'], $thired['mobile'], $thired['social_security_number'], $thired['num'], $thired['item_id'], $txtBirthday);
                $description = $this->getXmlVal('description', $r);
                if ($description == '成功') {
                    $sms = $ret->Sms($out_trade_no);
                    $descript = $this->getXmlVal('description', $r);
                    if ($descript == '成功') {
                        return $r;
                    }
                } else {
                    echo '智游宝下单接口下单失败';
                    exit;
                }
            }
        }
    }

    /**
     * 天时异步推送地址
     */
    public function actionNotifySky()
    {
        $orders_no = $_POST['orders_id'];//天时订单号
        $out_code = $_POST['out_code'];//天时码号
        $format = $_POST['format'];
        $pid = $_POST['_pid'];
        $method = $_POST['method'];
        $out_orders_id = $_POST['out_orders_id'];
        $out_money_send = $_POST['out_money_send'];
        $out_money_one = $_POST['out_money_one'];
        $out_send_content = $_POST['out_send_content'];
        Yii::log('天时码号out_code:' . $out_code . ';' . 'orders_id:' . $orders_no . ';' . 'format:' . $format . ';' . 'pid:' . $pid . ';' . 'method:' . $method . ';' . 'out_orders_id:' . $out_orders_id . ';' . 'out_money_send:' . $out_money_send . ';' . 'out_money_one:' . $out_money_one . ';' . 'out_send_content:' . $out_send_content, 'warning');
        if (!empty($orders_no) && !empty($out_code)) {
            $dordermall = new DOrderMall();
            $dordermall->TianshiOrderCode($orders_no, $out_code);
        }
    }

}