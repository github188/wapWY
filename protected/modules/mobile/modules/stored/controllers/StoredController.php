<?php

/**
 * 储值
 * */
class StoredController extends StoredsController
{

    //储值详情页
    public function actionStoredView()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            //判断用户登录状态
            $this->checkLogin($encrypt_id);

            $merchant = new MerchantC();
            $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $res->data->id;

            $user = new UserUC();
            $stored_res = $user->getStored($merchant_id);
            $stored_result = json_decode($stored_res, true);
            if ($stored_result['status'] == ERROR_NONE) {
                if (isset($stored_result['data'])) {
                    $stored = $stored_result['data'];
                }
            }

            $result = json_decode($user->getStoredInfo($_GET['id'], $merchant_id));
            if ($result->status == ERROR_NONE) {
                $this->render('storedView', array(
                    'stored' => $result->data,
                    'stored_info' => $stored,
                    'encrypt_id' => $encrypt_id
                ));
            }
        }
    }

    //储值订单列表
    public function actionStoredOrderList()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;

        $user = new UserUC ();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $rs = $user->getMyStored($merchant_id, $user_id);
        $result = json_decode($rs, true);
        $stored = '';
        if ($result ['status'] == ERROR_NONE) {
            if (isset ($result ['data'])) {
                $stored = $result ['data'];
            }
        }

        $this->render('storedOrderList', array(
            'stored' => $stored,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 我的储值
     */
    public function actionStored()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $stored = array();
        $user = new UserUC();

        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;
        $user_id = Yii::app()->session [$encrypt_id . 'user_id'];

        $rs = $user->getMyStored($merchant_id, $user_id);
        $result = json_decode($rs, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset ($result['data'])) {
                $stored = $result['data'];
            }
        }

        // 根据$merchant_id查询储值活动
        $stored_activity = array();
        $user_activity = new UserUC ();
        $rs_activity = $user_activity->getStoredActivity($merchant_id);
        $result_activity = json_decode($rs_activity, true);
        if ($result_activity['status'] == ERROR_NONE) {
            if (isset ($result_activity['data'])) {
                $stored_activity = $result_activity['data'];
            }
        }

        if (!isset($_GET['pay_key'])) {
            if (isset(Yii::app()->session['pay_key'])) {
                $key = array('key' => Yii::app()->session['pay_key']);
                $this->render('stored', array(
                    'stored' => $stored,
                    'stored_activity' => $stored_activity,
                    'key' => $key,
                    'encrypt_id' => $encrypt_id
                ));
            } else {
                // 如果是点击我的储值就进这个URL
                $this->render('stored', array(
                    'stored' => $stored,
                    'stored_activity' => $stored_activity,
                    'encrypt_id' => $encrypt_id
                ));
            }
        } elseif (isset($_GET['pay_key'])) {
            // 如果进入商铺再选择储值就进这个URL
            $key = array('key' => $_GET['pay_key']);
            $this->render('stored', array(
                'stored' => $stored,
                'stored_activity' => $stored_activity,
                'key' => $key,
                'encrypt_id' => $encrypt_id
            ));
        }
    }

    public function actionPayway()
    {
        //判断是否已登录
        $user = new UserUC();
        if (empty($_GET['stored_id']) || empty($_GET['stored_num'])) {
            echo "<script>alert('请选择储值活动');history.go(-1)</script>";
            exit();
        }
        if ((isset($_GET['stored_id']) && isset($_GET['stored_num']))) {
            $encrypt_id = $_GET['encrypt_id'];
            //判断用户登录状态
            $this->checkLogin($encrypt_id);

            //得到选择的id
            $stored_id = $_GET['stored_id'];
            //选择的充值数量
            $store_num = $_GET['stored_num'];
            $data = array();
            //根据得到的表stored的id查询对应的stored_money 和get_money
            $result_stored = json_decode($user->querySelectStored($stored_id), true);
            if ($result_stored['status'] == ERROR_NONE) {
                if (isset($result_stored['data'])) {
                    $data = $result_stored['data'];
                }
            }
            $stored_money = $data['stored_money'];
            $get_money = $data['get_money'];
            $recharge_money = floatval($stored_money) * intval($store_num);//充值的钱
            $obtain_money = floatval($get_money + $stored_money) * intval($store_num);//得到的钱
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            $order_no = STORED_ORDER_PREFIX . date('Ymd') . rand(100000, 999999);

            //把数据存到表stored_order
            $rs = $user->saveStoredOrder($user_id, $store_num, $order_no, $stored_id);
            $result = json_decode($rs, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    Yii::app()->session['saved_storedorder_id'] = $result['data'];
                }
            }
            $model = array(
                'productName' => '充' . $data['stored_money'] . '送' . $data['get_money'],
                'num' => $store_num,
                'recharge_money' => "$recharge_money",
                'order_id' => Yii::app()->session['saved_storedorder_id']
            );

            if (Yii::app()->session['source'] == "wechat") {
                $merchant = json_decode($user->getMerchant($encrypt_id), true);
                $merchant_id = $merchant['data']['id'];
                $wxjspay = new WxpayC();
                $jsApiParameters = json_decode($wxjspay->WxJsPay($order_no, $model['productName'], $merchant_id, WAPPAY_WQ_WECHAT_ASYNOTIFY_STORED, Yii::app()->session[$encrypt_id . 'open_id']));

                $this->render('StoredWay', array(
                    'model' => $model,
                    'jsApiParameters' => $jsApiParameters->data,
                    'encrypt_id' => $encrypt_id
                ));
            } else {
                $this->render('StoredWay', array(
                    'model' => $model,
                    'encrypt_id' => $encrypt_id
                ));
            }
        }
    }
}