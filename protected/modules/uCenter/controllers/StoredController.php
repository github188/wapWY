<?php

/**
 * 储值
 * */
class StoredController extends UCenterController
{

    //储值详情页
    public function actionStoredView()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $encrypt_id = $_GET['encrypt_id'];
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
            } else {
                $status = $stored_result['status'];
                $msg = $stored_result['errMsg'];
            }

            $result = json_decode($user->getStoredInfo($_GET['id'], Yii::app()->session ['merchant_id']));
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
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;

        $user = new UserUC ();

        $user_id = Yii::app()->session ['user_id'];
        $rs = $user->getMyStored($merchant_id, $user_id);
        $result = json_decode($rs, true);
        $stored = '';
        if ($result ['status'] == ERROR_NONE) {
            if (isset ($result ['data'])) {
                $stored = $result ['data'];
            }
        } else {
            $status = $result ['status'];
            $msg = $result ['errMsg'];
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

        $stored = array();
        $user = new UserUC ();

        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;
        $user_id = Yii::app()->session ['user_id'];

        $rs = $user->getMyStored($merchant_id, $user_id);
        $result = json_decode($rs, true);

        if ($result ['status'] == ERROR_NONE) {
            if (isset ($result ['data'])) {
                $stored = $result ['data'];
            }
        } else {
            $status = $result ['status'];
            $msg = $result ['errMsg'];
        }

        // 根据$merchant_id查询储值活动
        $stored_activity = array();
        $user_activity = new UserUC ();
        $rs_activity = $user_activity->getStoredActivity($merchant_id);
        $result_activity = json_decode($rs_activity, true);
        if ($result_activity ['status'] == ERROR_NONE) {
            if (isset ($result_activity ['data'])) {
                $stored_activity = $result_activity ['data'];
            }
        } else {
            
            $status_activity = $result_activity ['status'];
            $msg_activity = $result_activity ['errMsg'];
        }


        if (!isset ($_GET ['pay_key'])) {

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
        } else if (isset ($_GET ['pay_key'])) {
            // 如果进入商铺再选择储值就进这个URL
            $key = array('key' => $_GET ['pay_key']);
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
        if (!isset(Yii::app()->session['user_id']) || empty(Yii::app()->session['user_id'])) {
            $this->redirect(Yii::app()->createUrl('uCenter/user/Register', array(
                    'goUrl' => Yii::app()->createUrl('uCenter/stored/Payway', array(
                        'stored_id' => $_GET['stored_id'],
                        'stored_num' => $_GET['stored_num'],
                        'encrypt_id' => $_GET['encrypt_id']
                    )),
                'encrypt_id' => $_GET['encrypt_id']
            )));
        }
        $user = new UserUC();
        if (isset($_GET['stored_id']) && isset($_GET['stored_num'])) {
            //得到选择的id
            $stored_id = $_GET['stored_id'];
            //选择的充值数量
            $store_num = $_GET['stored_num'][$stored_id];
            $data = array();
            //根据得到的表stored的id查询对应的stored_money 和get_money
            $result_stored = json_decode($user->querySelectStored($stored_id), true);
            if ($result_stored['status'] == ERROR_NONE) {
                if (isset($result_stored['data'])) {
                    $data = $result_stored['data'];
                }
            } else {
                $status = $result_stored['status'];
                $msg = $result_stored['errMsg'];
            }
            $stored_money = $data['stored_money'];
            $get_money = $data['get_money'];
            $recharge_money = floatval($stored_money) * intval($store_num);//充值的钱
            $obtain_money = floatval($get_money + $stored_money) * intval($store_num);//得到的钱
            //Yii::app()->session['obtain_money'] = $obtain_money; //获得的总金额
            $user_id = Yii::app()->session['user_id'];
            $random = mt_rand(100000, 999999); //生成6位随机数密码
            $order_no = STORED_ORDER_PREFIX.date('Ymd', time()).$random;

            //把数据存到表stored_order
            $rs = $user->saveStoredOrder($user_id, $store_num, $order_no, $stored_id);
            $result = json_decode($rs, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    Yii::app()->session['saved_storedorder_id'] = $result['data'];
                }
            } else {
                $status_activity = $result['status'];
                $msg_activity = $result['errMsg'];
            }
            $model = array(
                'productName' => '充' . $data['stored_money'] . '送' . $data['get_money'],
                'num' => $store_num,
                'recharge_money' => "$recharge_money",
                'order_id' => Yii::app()->session['saved_storedorder_id']
            );

            $wxjspay = new WxpayC();
            $jsApiParameters = json_decode($wxjspay->WxJsPay($order_no, $model['productName'],'',WAPPAY_WQ_WECHAT_ASYNOTIFY_STORED),true);
            $this->render('StoredWay', array(
                'model' => $model, 
                'jsApiParameters' => $jsApiParameters['data'],
                'encrypt_id' => $_GET['encrypt_id']
            ));
        }
    }

    /**
     * 验证是否登陆
     */
    public function isLogin()
    {
        $user = new UserUC();

        $merchant_id = Yii::app()->session['merchant_id'];
        $user_id = Yii::app()->session['user_id'];

        $result = $user->checkLogin($merchant_id, $user_id);

        return $result;
    }

}