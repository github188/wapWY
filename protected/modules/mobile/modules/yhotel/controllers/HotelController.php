<?php

/**
 * 余姚宾馆订房
 * */
class HotelController extends YHotelController
{
    //订房首页
    public function actionIndex()
    {
        $encrypt_id = $_GET['encrypt_id'];

        //获取商户信息
        $user = new UserUC();
        $merchant_result = json_decode($user->getMerchant($encrypt_id));
        if ($merchant_result->status == ERROR_NONE) {
            $merchant_id = $merchant_result->data->id;
        }

        $Hotel = new HotelC();
        //获取首页轮播图
        $res = json_decode($Hotel->getHotelBanner($merchant_id), true);
        if ($res['status'] == ERROR_NONE) {
            $hotelBanner = explode(',', $res['data'][0]['img']);
        }

        //查询所有房间
        $res = json_decode($Hotel->queryHotelAllRoom($merchant_id), true);
        if ($res['status'] == ERROR_NONE) {
            $this->render('booking', array(
                'hotelBanner' => $hotelBanner,
                'roomLists' => $res['data'],
                'encrypt_id' => $encrypt_id
            ));
        } else {
            die($res['errMsg']);
        }
    }

    //房间详情
    public function actionRoomDetail()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $id = $_GET['id'];

        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/auth/Register', array(
                'goUrl' => Yii::app()->createUrl('mobile/yhotel/hotel/roomDetail', array(
                    'encrypt_id' => $encrypt_id,
                    'id' => $id
                )),
                'encrypt_id' => $encrypt_id
            )));
            return;
        }

        $Hotel = new HotelC();
        //获取房间详情
        $res = $Hotel->getHotelRoomDetail($id);
        $roomDetails = json_decode($res, true);
        if ($roomDetails['status'] == ERROR_NONE) {
            $this->render('roomDetail', array(
                'id' => $id,
                'roomDetail' => $roomDetails['data'],
                'encrypt_id' => $encrypt_id
            ));
        } else {
            die($roomDetails['errMsg']);
        }
    }

    //提交订单
    public function actionSubmitOrder()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $id = $_GET['id'];

        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/auth/Register', array(
                'goUrl' => Yii::app()->createUrl('mobile/yhotel/hotel/submitOrder', array(
                    'encrypt_id' => $encrypt_id,
                    'id' => $id
                )),
                'encrypt_id' => $encrypt_id
            )));
            return;
        }

        $Hotel = new HotelC();
        //获取房间详情
        $res = $Hotel->getHotelRoomDetail($id);
        $roomDetails = json_decode($res, true);

        if (isset($_POST['store_id']) && !empty($_POST['store_id'])) {
            $merchant = new MerchantC();
            $re = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $re->data->id;
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

            $room_id = $_GET['id'];
            $store_id = $_POST['store_id'];
            $check_in_time = $_POST['check_in_time'];
            $check_out_time = $_POST['check_out_time'];
            $room_num = $_POST['room_num'];
            $name = $_POST['person_name'];
            $mobile = $_POST['person_tel'];

            if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
                echo "<script>alert('手机号码格式不正确');</script>";
            } else {
                $Hotel = new HotelC();
                $res = json_decode($Hotel->createHotelOrder($user_id, $merchant_id, $room_id, $store_id, $check_in_time, $check_out_time, $room_num, $name, $mobile), true);
                if ($res['status'] == ERROR_NONE) {
                    //发送提醒
                    $param = array(
                        'first' => '您好，' . $res['data']['name'] . '预订了您的房间，请尽快处理',
                        'keyword1' => $res['data']['order_no'],
                        'keyword2' => $res['data']['room_name'],
                        'keyword3' => '无',
                        'keyword4' => date('Y年m月d日', strtotime($res['data']['check_in_time'])) . '至' . date('Y年m月d日', strtotime($res['data']['check_out_time'])),
                        'keyword5' => '房间单价：' . $res['data']['price'] . '元，房间数：' . $res['data']['num'] . '间。总计：' . ($res['data']['price'] * $res['data']['num']) . '元',
                        'remark' => ''
                    );
                    $notice = new WechatNotice();
                    $notice->send(WechatNotice::NOTICE_TYPE_BOOK, $param, $store_id);

                    $this->redirect(array('orderDetail',
                        'order_no' => $res['data']['order_no'],
                        'encrypt_id' => $encrypt_id
                    ));
                } else {
                    echo "<script>alert('预订失败');</script>";
                }
            }
        }
        if ($roomDetails['status'] == ERROR_NONE) {

            $this->render('submitOrder', array(
                'id' => $id,
                'roomDetail' => $roomDetails['data'],
                'encrypt_id' => $encrypt_id
            ));
        } else {
            die($roomDetails['errMsg']);
        }
    }

    // 订单详情
    public function actionOrderDetail()
    {
        $order_no = $_GET['order_no'];
        $encrypt_id = $_GET['encrypt_id'];

        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/auth/Register', array(
                'goUrl' => Yii::app()->createUrl('mobile/yhotel/hotel/orderDetail', array(
                    'encrypt_id' => $encrypt_id,
                    'order_no' => $order_no
                )),
                'encrypt_id' => $encrypt_id
            )));
            return;
        }

        $Hotel = new HotelC();
        $order = json_decode($Hotel->getHotelOrderDetail($order_no), true);

        if ($order['status'] == ERROR_NONE) {
            //处理时间格式
            $order['data']['check_in_time'] = strtotime($order['data']['check_in_time']);
            $order['data']['check_out_time'] = strtotime($order['data']['check_out_time']);

            $this->render('orderDetail', array(
                'orderDetail' => $order['data'],
                'encrypt_id' => $encrypt_id
            ));
        } else {
            die($order['errMsg']);
        }

    }

    /**
     * 订单列表
     */
    public function actionOrderList()
    {
        $encrypt_id = $_GET['encrypt_id'];

        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('uCenter/user/Register', array(
                'goUrl' => Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array(
                    'encrypt_id' => $encrypt_id
                )),
                'encrypt_id' => $encrypt_id
            )));
            return;
        }

        $merchant = new MerchantC();
        $re = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $re->data->id;
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        $list = array();

        // 订单状态
        if (isset ($_GET ['order_status']) && !empty($_GET ['order_status'])) {
            $order_status = $_GET ['order_status'];
        } else {
            $order_status = HOTEL_ORDER_STATUS_WAITING;
        }

        $hotel = new HotelC();
        $result = $hotel->getHotelOrderList($user_id, $order_status, $merchant_id);
        $result = json_decode($result, true);

        if ($result['status'] == ERROR_NONE) {
            $list = $result['data'];
        }

        $avatar = $result['avatar'];
        $nickname = $result['nickname'];

        $unconfirmCount = $hotel->getHotelOrderStatusCount($user_id, HOTEL_ORDER_STATUS_WAITING, $merchant_id); //获取待确定数量
        $confirmCount = $hotel->getHotelOrderStatusCount($user_id, HOTEL_ORDER_STATUS_CONFIRM, $merchant_id); //获取已确定数量
        $cancelCount = $hotel->getHotelOrderStatusCount($user_id, HOTEL_ORDER_STATUS_CANCEL, $merchant_id); //获取已取消数量
        $refuseCount = $hotel->getHotelOrderStatusCount($user_id, HOTEL_ORDER_STATUS_REFUSE, $merchant_id); //获取已拒绝数量
        $checkedCount = $hotel->getHotelOrderStatusCount($user_id, HOTEL_ORDER_STATUS_CHECKIN, $merchant_id); //获取已入住数量
        $this->render('orderList', array(
            'list' => $list,
            'avatar' => $avatar,
            'nickname' => $nickname,
            'unconfirmCount' => $unconfirmCount,
            'confirmCount' => $confirmCount,
            'noSuccessCount' => $cancelCount + $refuseCount,
            'checkedCount' => $checkedCount,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 订单取消
     */
    public function actionCancleOrder()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $order_no = $_GET['order_no'];

        $hotel = new HotelC();

        $result = $hotel->cancleHotelOrder($order_no);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $this->redirect(array('OrderDetail',
                'order_no' => $order_no,
                'encrypt_id' => $encrypt_id
            ));
        }
    }

    /**
     * 订单入住
     */
    public function actionCheckinOrder()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $order_no = $_GET['order_no'];

        $hotel = new HotelC();

        $result = $hotel->checkinHotelOrder($order_no);
        $result = json_decode($result, true);
        if ($result['status'] == ERROR_NONE) {
            $this->redirect(array('OrderDetail',
                'order_no' => $order_no,
                'encrypt_id' => $encrypt_id
            ));
        }
    }

    /*
     * ajax获取订单状态
     */
    public function actionGetOrderStstus()
    {
        if (isset($_POST['order_no']) && !empty($_POST['order_no'])) {
            $order_no = $_POST['order_no'];
            $hotel = new HotelC();
            $res = json_decode($hotel->getHotelOrderDetail($order_no), true);
            if ($res['status'] == ERROR_NONE) {
                $status = $res['data']['status'];
                echo $status;
            }
        }
    }

    /*
     * 操作员登录
     */
    public function actionOperatorLogin()
    {
        if (isset(Yii::app()->session['operator_id']) && !empty(Yii::app()->session['operator_id'])) {
            $hotel = new HotelC();
            $res = json_decode($hotel->getEncrypt_id(Yii::app()->session['operator_id']), true);

            if ($res['status'] == ERROR_NONE) {
                $this->redirect(array('OperatorOrderList'));
                return;
            }
        } else {
            if (isset($_POST['account']) && !empty($_POST['account'])) {

                $account = $_POST['account'];
                $password = $_POST['password'];

                $hotel = new HotelC();
                $res = json_decode($hotel->operatorLoginCheck($account, $password), true);

                if ($res['status'] == ERROR_NONE) {
                    Yii::app()->session['operator_id'] = $res['data']['operator_id'];
                    $this->redirect(array(
                        'OperatorOrderList',
                        'encrypt_id' => $res['data']['encrypt_id'],
                        'order_status' => HOTEL_ORDER_STATUS_WAITING
                    ));
                } else {
                    $msg = $res['errMsg'];
                    Yii::app()->user->setFlash('account', $account);
                    Yii::app()->user->setFlash('error', $msg);
                }
            }
            $this->render('operatorLogin');
        }
    }

    /*
     * 操作员订单列表
     */
    public function actionOperatorOrderList()
    {
        //判断操作员是否登录
        if (!isset(Yii::app()->session['operator_id']) || empty(Yii::app()->session['operator_id'])) {
            $this->redirect(array('OperatorLogin'));
            return;
        } else {
            $operator = new OperatorC();
            $res = json_decode($operator->getOperatorDetails(Yii::app()->session['operator_id']), true);
            $store_id = $res['data']['store_id'];
        }

        $list = array();

        // 订单状态
        if (isset ($_GET ['order_status']) && $_GET ['order_status']) {
            $order_status = $_GET ['order_status'];
        } else {
            $order_status = HOTEL_ORDER_STATUS_WAITING;
        }

        $hotel = new HotelC();
        $res = json_decode($hotel->getHotelAllOrder($store_id, $order_status), true);

        if ($res['status'] == ERROR_NONE) {
            $list = $res['data'];
        }

        $unconfirmCount = $hotel->getStoreOrderStatusCount($store_id, HOTEL_ORDER_STATUS_WAITING); //获取待确定数量
        $confirmCount = $hotel->getStoreOrderStatusCount($store_id, HOTEL_ORDER_STATUS_CONFIRM); //获取已确定数量
        $cancelCount = $hotel->getStoreOrderStatusCount($store_id, HOTEL_ORDER_STATUS_CANCEL); //获取已取消数量
        $refuseCount = $hotel->getStoreOrderStatusCount($store_id, HOTEL_ORDER_STATUS_REFUSE); //获取已拒绝数量
        $checkedCount = $hotel->getStoreOrderStatusCount($store_id, HOTEL_ORDER_STATUS_CHECKIN); //获取已入住数量

        $this->render('operatorOrder', array(
            'list' => $list,
            'unconfirmCount' => $unconfirmCount,
            'confirmCount' => $confirmCount,
            'noSuccessCount' => $cancelCount + $refuseCount,
            'checkedCount' => $checkedCount,
        ));
    }

    /**
     * 订单确认
     */
    public function actionConfirmOrder()
    {
        if (isset($_POST['order_no']) && !empty($_POST['order_no'])) {
            $order_no = $_POST['order_no'];
            $hotel = new HotelC();
            $result = $hotel->confirmHotelOrder($order_no);
            $result = json_decode($result, true);
            if ($result['status'] == ERROR_NONE) {
                echo 'success';
            } else {
                echo 'error';
            }
        }
    }

    /**
     * 订单拒绝
     */
    public function actionRefuseOrder()
    {
        if (isset($_POST['order_no']) && !empty($_POST['order_no'])) {
            $order_no = $_POST['order_no'];
            $hotel = new HotelC();
            $result = $hotel->refuseHotelOrder($order_no);
            $result = json_decode($result, true);
            if ($result['status'] == ERROR_NONE) {
                echo 'success';
            } else {
                echo 'error';
            }
        }
    }

    /**
     * 订单入住
     */
    public function actionAjaxCheckinOrder()
    {
        if (isset($_POST['order_no']) && !empty($_POST['order_no'])) {
            $order_no = $_POST['order_no'];
            $hotel = new HotelC();
            $result = $hotel->checkinHotelOrder($order_no);
            $result = json_decode($result, true);
            if ($result['status'] == ERROR_NONE) {
                echo 'success';
            } else {
                echo 'error';
            }
        }
    }

    /*
     * 操作员退出
     */
    public function actionOperatorLoginOut()
    {
        unset(Yii::app()->session['operator_id']);
        $this->redirect(array('OperatorLogin'));
    }

}