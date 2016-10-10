<?php

/**
 * 会员中心
 *
 */
class UserController extends UCenterController
{
    /**
     * 会员中心
     */
    public function actionMemberCenter()
    {
        //获取加密商户id
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }
        
        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user = new UserUC();

        $merchant = new MerchantC();
        $result = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $result->data->id;

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $res = $user->getPersonalInformation($user_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                if (!isset($result['data']['nickname']) || empty($result['data']['nickname'])) {
                    $result['data']['nickname'] = substr($result['data']['account'], 0, 4) . '****' . substr($result['data']['account'], 7, 4);;
                }
                $data = $result['data'];
            }
        }

        $this->render('memberCenter', array(
            'data' => $data,
            'encrypt_id' => $encrypt_id,
            'merchant_id' => $merchant_id
        ));
    }

    /**
     * 获取短信验证码
     */
    public function actionSendMsgPassword()
    {
        $message = new DuanXin();
        $user = new UserUC();

        $tel_res = false;
        $tel = $_POST['mobile'];
        $is_check = $_POST['check'];

        if (isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
            $encrypt_id = $_POST['encrypt_id'];
            $merchat = new MerchantC();
            $result = json_decode($merchat->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $result->data->id;
        }

        //验证手机号是否存在
        if ($is_check == 'yes') {
            $tel_res = $user->accountExist($merchant_id, $tel);
        }

        if ($tel_res && $is_check == 'yes') {
            $tel_result['status'] = ERROR_DUPLICATE_DATA;
            $tel_result['errMsg'] = '该号码已被注册';

            echo json_encode($tel_result);
        } else {
            //判断商户短信余额是否足够
            $check_res = $user->checkMsgNum($merchant_id);
            $check_result = json_decode($check_res, true);
            if ($check_result['status'] == ERROR_NONE) {
                $res = $message->Sms($tel);
                $result = json_decode($res, true);

                if ($result['status'] == ERROR_NONE) {
                    if (isset($result['data'])) {
                        $phone_num = $result['data']['phone_num'];
                        $msg_pwd = $result['data']['msg_pwd'];
                        // 把短信验证码保存到memcache里保存时间30分钟
                        $this->saveMsgPwd($phone_num, $msg_pwd);
                        //将商户短信余额减少1条
                        $res = $user->minusMsgNum($merchant_id);
                    }
                }
                echo $res;
            } else {
                echo $check_res;
            }
        }
    }

    /**
     * 将短信验证码保存到缓存中30分钟
     */
    public function saveMsgPwd($phone_num, $msg_pwd)
    {
        $key = $phone_num;
        $ckKey = Yii::app()->memcache->get($key);
        if ($ckKey != null) {
            Yii::app()->memcache->delete($key);
            $value = $msg_pwd;
            $expire = 1800;
            Yii::app()->memcache->set($key, $value, $expire);
        } else {
            $value = $msg_pwd;
            $expire = 1800;
            Yii::app()->memcache->set($key, $value, $expire);
        }
    }

    /**
     * 将缓存中的短信验证码删除
     */
    public function delMsgPwd($phone_num)
    {
        $key = $phone_num;
        $ckKey = Yii::app()->memcache->get($key);
        if ($ckKey != null) {
            Yii::app()->memcache->delete($key);
        }
    }

    /**
     * 验证手机号是否存在
     */
    public function actionIsExist()
    {
        $result = '';
        $user = new UserUC();
        if (isset($_POST['account']) && !empty($_POST['account']) && isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
            $result = json_decode($user->getMerchant($_POST['encrypt_id']));
            $merchant_id = $result->data->id;
            $account = $_POST['account'];
            $res = $user->accountExist($merchant_id, $account);
            if ($res) {
                $result = 'exist';
            } else {
                $result = 'not';
            }
        }
        echo json_encode(array('result' => $result));
    }

    /**
     * 优惠券列表
     */
    public function actionCoupons()
    {
        $list = array();
        $user = new UserUC();

        if (isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            $encrypt_id = $_GET['encrypt_id'];
            $merchant = $user->getMerchantWithId($encrypt_id);
            $merchant_id = $merchant->id;
        }

        $goUrl = Yii::app()->createUrl('mobile/uCenter/user/Coupons', array('coupons_status' => COUPONS_USE_STATUS_UNUSE, 'coupons_type' => COUPON_TYPE_CASH, 'encrypt_id' => $encrypt_id));

        //判断用户登录状态
        $this->checkLogin($encrypt_id, $goUrl);

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        //订单状态
        $coupons_status = '';
        if (isset($_GET['coupons_status']) && !empty($_GET['coupons_status'])) {
            $coupons_status = $_GET['coupons_status'];
        }
        //订单类型（优惠券/红包）
        $coupons_type = '';
        if (isset($_GET['coupons_type']) && !empty($_GET['coupons_type'])) {
            $coupons_type = $_GET['coupons_type'];
        }
        //修改优惠券状态
        $user->changeCouponsStatus($user_id);

        $res = $user->getCouponsList($merchant_id, $user_id, $coupons_status, $coupons_type);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data']['list'])) {
                $list = $result['data']['list'];
            }
        }

        $this->render('coupons', array(
            'list' => $list,
            'cur_status' => $coupons_status,
            'coupons_type' => $coupons_type,
            'encrypt_id' => $encrypt_id
        ));
    }


    /**
     * 个人中心
     */
    public function actionPersonalInformation()
    {

        if (isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user = new UserUC();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        //获取个人信息
        $res = $user->getPersonalInformation($user_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $all_account = $result['data']['account'];
                $part_account = substr($all_account, 0, 4) . '****' . substr($all_account, 7, 4);
                $free_secret = $result['data']['free_secret'] + 0;
            }
        }

        $this->render('personalInformation', array(
            'part_account' => $part_account,
            'all_account' => $all_account,
            'free_secret' => $free_secret,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 个人信息详情
     */
    public function actionPersonalInformationDetail()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $list = array();
        $user = new UserUC();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        //获取用户个人信息
        $res_get = $user->getPersonalInformation($user_id);
        $result_get = json_decode($res_get, true);

        if ($result_get['status'] == ERROR_NONE) {
            if (isset($result_get['data'])) {
                $list = $result_get['data'];
            }
        }

        $this->render('personalInformationDetail', array(
            'user' => $list,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 验证账号
     */
    public function actionCheckOldAccount()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user = new UserUC();

        $part_account = isset($_GET['part_account']) ? $_GET['part_account'] : '';
        $all_account = isset($_GET['all_account']) ? $_GET['all_account'] : '';

        if (isset($_POST['User']) && !empty($_POST['User'])) {
            $post = $_POST['User'];
            $account = $post['Account'];
            $msg_pwd = $post['MsgPassword'];

            $flag = $user->checkMsgPwd($account, $msg_pwd);
            if ($flag) {
                $this->redirect(array('setNewAccount', 'encrypt_id' => $encrypt_id));
            } else {
                Yii::app()->user->setFlash('error', '短信验证失败');
            }
        }

        $this->render('checkOldAccount', array(
            'part_account' => $part_account,
            'all_account' => $all_account,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 修改绑定手机
     */
    public function actionSetNewAccount()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();

            $result = json_decode($user->getMerchant($encrypt_id));
            $merchant_id = $result->data->id;
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

            $account = $post['MobilePhone'];
            $msg_pwd = $post['MsgPassword'];

            $res = $user->setNewAccount($merchant_id, $user_id, $account, $msg_pwd);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                //删除缓存中的短信验证码
                $this->delMsgPwd($account);

                $this->redirect(array('personalInformation', 'encrypt_id' => $encrypt_id));
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        }

        $this->render('setNewAccount', array(
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 修改个人信息中的一项（昵称，姓名，身份证号，邮箱，婚姻状况，工作）
     */
    public function actionEditPersonalInformation()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $title = $_GET['title'];
            $data = $_GET['data'];

            if (isset($_POST['user']) && !empty($_POST['user'])) {
                $user = new UserUC();
                $post = $_POST['user'];
                $data_edit = $post['data'];

                $res = $user->editPersonalInformation($id, $title, $data_edit);
                $result = json_decode($res, true);

                if ($result['status'] == ERROR_NONE) {
                    $this->redirect(array('personalInformationDetail', 'encrypt_id' => $encrypt_id));
                } else {
                    Yii::app()->user->setFlash('error', '保存失败');
                }
            }

            $this->render('editPersonalInformation', array(
                'id' => $id,
                'title' => $title,
                'data' => $data,
                'encrypt_id' => $encrypt_id
            ));
        } else {
            $this->render('personalInformation', array(
                'encrypt_id' => $encrypt_id
            ));
        }
    }

    /**
     * 修改性别
     */
    public function actionEditPersonalSex()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user = new UserUC();
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $title = $_GET['title'];
            $data = $_GET['data'];

            if (isset($_POST['sex']) && !empty($_POST['sex'])) {
                $sex = $_POST['sex'];

                $res = $user->editPersonalInformation($id, $title, $sex);
                $result = json_decode($res, true);

                if ($result['status'] == ERROR_NONE) {
                    $this->redirect(array('personalInformationDetail', 'encrypt_id' => $encrypt_id));
                } else {
                    Yii::app()->user->setFlash('error', '保存失败');
                }
            }
        }
        $this->render('editPersonalSex', array(
            'sex' => $data,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 修改婚姻状态
     */
    public function actionEditPersonalMarital()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user = new UserUC();

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $title = $_GET['title'];
            $data = $_GET['data'];

            if (isset($_POST['marital_status']) && !empty($_POST['marital_status'])) {
                $marital_status = $_POST['marital_status'];

                $res = $user->editPersonalInformation($id, $title, $marital_status);
                $result = json_decode($res, true);

                if ($result['status'] == ERROR_NONE) {
                    $this->redirect(array('personalInformationDetail', 'encrypt_id' => $encrypt_id));
                } else {
                    Yii::app()->user->setFlash('error', '保存失败');
                }
            }
        }

        $this->render('editPersonalMarital', array(
            'marital_status' => $data,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 修改生日
     */
    public function actionEditPersonalBirthday()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $arr_year = array(1970, 1971, 1972, 1973, 1974, 1975, 1976, 1977, 1978, 1979, 1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989, 1990, 1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999, 2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015);
        $arr_month = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $arr_day = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);

        $user = new UserUC();

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];
            $title = $_GET['title'];
            $data = $_GET['data'];

            $year = date('Y', strtotime($data));
            $month = date('m', strtotime($data));
            $day = date('d', strtotime($data));

            if (isset($_POST['Birthday']) && !empty($_POST['Birthday'])) {
                $post = $_POST['Birthday'];
                $birthday = $post['year'] . '-' . $post['month'] . '-' . $post['day'] . ' ' . '00:00:00';

                $res = $user->editPersonalInformation($id, $title, $birthday);
                $result = json_decode($res, true);

                if ($result['status'] == ERROR_NONE) {
                    $this->redirect(array('personalInformationDetail', 'encrypt_id' => $encrypt_id));
                } else {
                    Yii::app()->user->setFlash('error', '保存失败');
                }
            }
        }

        $this->render('editPersonalBirthday', array(
            'birthday' => $data,
            'arr_year' => $arr_year,
            'arr_month' => $arr_month,
            'arr_day' => $arr_day,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 修改密码
     */
    public function actionChangePwd()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();

            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

            $old_pwd = $post['OldPassword'];
            $new_pwd = $post['NewPassword'];
            $con_pwd = $post['ConfirmPassword'];

            $res = $user->changePwd($user_id, $old_pwd, $new_pwd, $con_pwd);
            $result = json_decode($res, true);

            if ($result['status'] == ERROR_NONE) {
                Yii::app()->user->setFlash('success', '保存成功');
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }

        }
        $this->render('changePwd', array('encrypt_id' => $encrypt_id));
    }

    /**
     * 我的会员卡
     */
    public function actionMemberShipCard()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $card_data = array();
        $user = new MobileUserUC();

        $res = json_decode($user->getMerchant($encrypt_id));
        $merchant_id = $res->data->id;

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        $res = $user->getMemberShipCard($merchant_id, $user_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $card_data = $result['data'];
            }
        }

        $this->render('memberShipCard', array(
            'data' => $card_data,
            'encrypt_id' => $encrypt_id
        ));
    }


    /**
     * 我的积分
     */
    public function actionMyPoint()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $point = array();
        $user = new UserUC();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $encrypt_id = $_GET['encrypt_id'];

        $rs = $user->getPointList($user_id);
        $result = json_decode($rs, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $point = $result['data'];
            }
        }

        $this->render('myPoint', array(
            'point' => $point,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 我的订单
     */
    public function actionOrderList()
    {
        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $list = array();
        $user = new UserUC();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        //订单状态
        if (isset($_GET['stored_confirm_status']) && !empty($_GET['stored_confirm_status'])) {
            $stored_confirm_status = $_GET['stored_confirm_status'];
        }
        $rs = $user->getPayOrderList($user_id, $stored_confirm_status);
        $result = json_decode($rs, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $list = $result['data'];
            }
        }

        $this->render('orderList', array(
            'list' => $list,
            'stored_confirm_status' => $stored_confirm_status,
            'encrypt_id' => $encrypt_id
        ));
    }

    public function actionStoredConfirm()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        $data = array('error' => 'failure');
        try {
            $user = new UserUC();

            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

            if (isset($_POST['order_no']) && !empty($_POST['order_no'])) {
                $order_no = $_POST['order_no'];
                $ret = $user->storedOrderConfirm($order_no, $user_id);
                $result = json_decode($ret, true);
                if ($result['status'] == ERROR_NONE) {
                    $data['error'] = 'success';
                } else {
                    throw new Exception($result['errMsg']);
                }
            }
        } catch (Exception $e) {
            $data['errMsg'] = $e->getMessage();
        }

        echo json_encode($data);
    }

    /**
     * 订单详情
     */
    public function actionOrderDetail()
    {
        $order = array();
        $user = new UserUC();

        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
        $encrypt_id = $_GET['encrypt_id'];

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $res = $user->getOrderDetail($order_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $order = $result['data'];
            }
        }

        $this->render('orderDetail', array(
            'order' => $order,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 领取红包/优惠券
     */
    public function actionLookCoupons()
    {
        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id']) && isset($_GET['source'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $user = new UserUC();
        $coupons_id = isset($_GET['coupons_id']) ? $_GET['coupons_id'] : '';
        $coupons_type = isset($_GET['coupons_type']) ? $_GET['coupons_type'] : '';
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        //判断是否可以领取
        $jug_res = $user->judgeUserCoupons($user_id, $coupons_id);
        $jug_result = json_decode($jug_res, true);

        if ($jug_result['status'] == ERROR_NONE) {
            $res = $user->couponsDetailNotReceive($coupons_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $coupons = $result['data'];
                }
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        } else {
            $msg = $jug_result['errMsg'];
            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/receiveCouponsFail', array(
                'msg' => $msg,
                'coupons_type' => $coupons_type,
                'encrypt_id' => $encrypt_id
            )));
        }

        $this->render('lookCoupons', array(
            'coupons' => $coupons,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 处理使用说明
     */
    public function actionInstructions()
    {
        $coupons = isset($_GET['coupons']) ? $_GET['coupons'] : '';
        $this->render('instructions', array('coupons' => $coupons));
    }

    /**
     * 领取优惠券
     */
    public function actionReceiveCoupons()
    {
        if (isset($_GET['coupons_id']) && !empty($_GET['coupons_id']) && isset($_GET['coupons_type']) && !empty($_GET['coupons_type'])) {
            if (isset($_GET['encrypt_id']) && isset($_GET['source'])) {
                $encrypt_id = $_GET['encrypt_id'];
            }

            //判断用户登录状态
            $this->checkLogin($encrypt_id);

            $coupons_id = $_GET['coupons_id'];
            $coupons_type = $_GET['coupons_type'];
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            $encrypt_id = $_GET['encrypt_id'];

            $user = new UserUC();
            //判断用户是否可以领取优惠券
            $jug_res = $user->judgeUserCoupons($user_id, $coupons_id);
            $jug_result = json_decode($jug_res, true);
            if ($jug_result['status'] == ERROR_NONE) {
                //领取优惠券
                $receive_res = $user->receiveCoupons($user_id, $coupons_id);
                $receive_result = json_decode($receive_res, true);

                if ($receive_result['status'] == ERROR_NONE) {
                    $id = $receive_result['data'];

                    $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/couponsDetail', array(
                        'coupons_id' => $id,
                        'action' => "receive",
                        'encrypt_id' => $encrypt_id
                    )));
                } else {
                    $msg = $receive_result['errMsg'];

                    $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/receiveCouponsFail', array(
                        'msg' => $msg,
                        'coupons_type' => $coupons_type,
                        'encrypt_id' => $encrypt_id
                    )));
                }
            } else {
                $msg = $jug_result['errMsg'];
                $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/receiveCouponsFail', array(
                    'msg' => $msg,
                    'coupons_type' => $coupons_type,
                    'encrypt_id' => $encrypt_id
                )));
            }
        }
    }

    /**
     * 优惠券详情
     */
    public function actionCouponsDetail()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $user = new UserUC();
        $coupons_id = isset($_GET['coupons_id']) ? $_GET['coupons_id'] : '';
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        $res = $user->couponsDetail($coupons_id, $user_id);
        $result = json_decode($res, true);
        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $coupon = $result['data'];
            }
        } else {
            $msg = $result['errMsg'];
            Yii::app()->user->setFlash('error', $msg);
        }

        $this->render('couponsDetail', array(
            'coupon' => $coupon,
            'action' => $action,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 领取失败
     */
    public function actionReceiveCouponsFail()
    {
        $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $coupons_type = isset($_GET['coupons_type']) ? $_GET['coupons_type'] : '';
        $this->render('receiveCouponsFail', array(
            'msg' => $msg,
            'coupons_type' => $coupons_type,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 设置小额免密设置
     */
    public function actionSetFreeSecret()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $free_secret = '';   //初始化free_secret
        $user = new UserUC();

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        $get_rs = $user->getFreeSecret($user_id);
        $get_result = json_decode($get_rs, true);

        if ($get_result['status'] == ERROR_NONE) {
            if (isset($get_result['data'])) {
                $free_secret = $get_result['data'];
            }
        }

        if (isset($_POST['free_secret']) && !empty($_POST['free_secret'])) {
            $free_secret = $_POST['free_secret'];

            $edit_res = $user->editFreeSecret($user_id, $free_secret);
            $edit_result = json_decode($edit_res, true);

            if ($edit_result['status'] == ERROR_NONE) {
                Yii::app()->user->setFlash('success', '保存成功');
            } else {
                Yii::app()->user->setFlash('error', '保存失败');
            }
        }

        $this->render('setFreeSecret', array(
            'free_secret' => $free_secret + 0,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 储值
     */
    public function actionStoredView()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $user = new UserUC();
        $merchant = $user->getMerchantWithId($encrypt_id);
        $merchant_id = $merchant->id;
        $stored_id = isset($_GET['stored_id']) ? $_GET['stored_id'] : '1';

        //参加次数
        $arr_join_num = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        //储值活动信息
        $stored_res = $user->getStored($merchant_id);
        $stored_result = json_decode($stored_res, true);
        if ($stored_result['status'] == ERROR_NONE) {
            if (isset($stored_result['data'])) {
                $stored = $stored_result['data'];
            }
        }

        $this->render('storedView', array(
            'join_num' => $arr_join_num,
            'stored' => $stored,
            'stored_id' => $stored_id
        ));
    }

    /**
     * 列表页面（储值活动，红包，优惠券）
     */
    public function actionList()
    {
        $title = isset($_GET['title']) ? $_GET['title'] : '';
        $model = isset($_GET['model']) ? $_GET['model'] : '';
        $encrypt_id = isset($_GET['encrypt_id']) ? $_GET['encrypt_id'] : '';
        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $model = json_decode($model, true);
        
        $this->render('list', array(
            'title' => $title, 
            'model' => $model,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 门店列表
     */
    public function actionStoreList()
    {
        $model = isset($_GET['model']) ? $_GET['model'] : '';
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);

        $model = json_decode($model, true);
        
        $this->render('storeList', array(
            'model' => $model,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 登出
     */
    public function actionLogout()
    {
        if (isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        Yii::app()->session->clear();
        Yii::app()->session->destroy();
        Yii::app()->user->logout();

        $this->redirect(Yii::app()->createUrl('mobile/auth/login', array(
            'encrypt_id' => $encrypt_id
        )));
    }

    /**
     * 错误页面
     */
    public function actionError()
    {
        $this->render('error');
    }

    /**
     * 中转页面（未完成页面）
     */
    public function actionTransit()
    {
        $this->render('transit');
    }

    /**
     * 图文素材 页面
     */
    public function actionMaterial()
    {
        $user = new UserUC();
        $material_id = isset($_GET['material_id']) ? $_GET['material_id'] : "";

        $result = $user->getMaterial($material_id);
        if ($result['status'] == ERROR_NONE) {
            $model = $result['data'];
        } else {
            $model = array();
        }

        $this->render('material', array('model' => $model));
    }


    public function actionfail()
    {
        echo 'tande fail';
    }

    //生成条形码
    public function actionCreateBarcode()
    {
        // Including all required classes
        Yii::import('application.extensions.barcodegen.*');
        require_once('class/BCGFontFile.php');
        require_once('class/BCGColor.php');
        require_once('class/BCGDrawing.php');

        // Including the barcode technology
        require_once('class/BCGcode128.barcode.php');

        // Loading Font
        //$font = new BCGFontFile('./font/Arial.ttf', 18);

        // Don't forget to sanitize user inputs
        $text = isset($_GET['text']) ? $_GET['text'] : 'NULL';

        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            $code = new BCGcode128();
            $code->setScale(2); // Resolution
            $code->setThickness(25); // Thickness
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            $code->setFont(0); // Font (or 0)
            $code->parse($text); // Text
        } catch (Exception $exception) {
            $drawException = $exception;
        }

        /* Here is the list of the arguments
        1 - Filename (empty : display on screen)
        2 - Background color */
        $drawing = new BCGDrawing('', $color_white);
        if ($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }

        // Header that says it is an image (remove it if you save the barcode to a file)
        header('Content-Type: image/png');
        header('filename="barcode.png"');

        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
    }

    /**
     * 微信支付同步修改订单号，储值金额
     */
    public function actionPaySuccess()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        if (!empty($_GET['money'])) {
            if (isset($_GET['ordertype']) && $_GET['ordertype'] == 'SC') {
                $this->render('paySuccess', array(
                    'money' => $_GET['money'],
                    'ordertype' => $_GET['ordertype'],
                    'encrypt_id' => $encrypt_id
                ));
            } else {
                $this->render('paySuccess', array(
                    'money' => $_GET['money'],
                    'encrypt_id' => $encrypt_id
                ));
            }
        } else {
            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fail'));
        }
    }

    /**
     * 设置头像
     */
    public function actionSetAvatar()
    {
        $id = $_GET['id'];
        $img = $_GET['img'];

        $user = new UserUC();
        $result = $user->setAvatar($id, $img);
        echo $result;
    }

    /**
     * 完善用户信息
     */
    public function actionFillInfo()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断用户登录状态
        $this->checkLogin($encrypt_id);
        
        $goUrl = $_GET['goUrl'];

        $user = new UserUC();
        $res = $user->getMerchantWithId($encrypt_id);
        $merchant_id = $res->id;

        $merchant = new MerchantC();
        $res = json_decode($merchant->getFillInfo($merchant_id), true);
        if ($res['status'] == ERROR_NONE) {
            //获取必填项
            $info_arr = explode(',', $res['data']);
        }

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];

            if (in_array(MERCHANT_AUTH_SET_NAME, $info_arr)) {
                $name = trim($post['name']);
                if (empty($name)) {
                    Yii::app()->user->setFlash('error', '请填写姓名');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_ADDRESS, $info_arr)) {
                $proCity = trim($post['proCity']);
                $address = trim($post['address']);
                if (empty($proCity) || empty($address)) {
                    Yii::app()->user->setFlash('error', '请填写通讯地址');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_SEX, $info_arr)) {
                $sex = $post['sex'];
                if (empty($sex)) {
                    Yii::app()->user->setFlash('error', '请选择性别');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_BIRTHDAY, $info_arr)) {
                $birthday = $post['birthday'];
                if (empty($birthday)) {
                    Yii::app()->user->setFlash('error', '请选择生日');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_ID, $info_arr)) {
                $socialNumber = trim($post['socialNumber']);
                if (empty($socialNumber)) {
                    Yii::app()->user->setFlash('error', '请填写身份证号');
                } elseif (count($socialNumber) != 18) {
                    Yii::app()->user->setFlash('error', '请填写正确的身份证号');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_EMAIL, $info_arr)) {
                $email = trim($post['email']);
                if (empty($email)) {
                    Yii::app()->user->setFlash('error', '请填写邮箱');
                }
                if (!preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $email)) {
                    Yii::app()->user->setFlash('error', '请填写合法邮箱');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_MARITAL_STATUS, $info_arr)) {
                $marital = $post['marital'];
                if (empty($marital)) {
                    Yii::app()->user->setFlash('error', '请选择婚姻状况');
                }
            }

            if (in_array(MERCHANT_AUTH_SET_WORK, $info_arr)) {
                $work = trim($post['work']);
                if (empty($work)) {
                    Yii::app()->user->setFlash('error', '请填写工作');
                }
            }

            if (!empty($post['name'])) {
                $data['name'] = $post['name'];
            }
            if (!empty($post['proCity']) && !empty($address)) {
                $proCity = explode(' ', $post['proCity']);
                $data['province'] = $proCity[0];
                $data['city'] = $proCity[1];
                $data['address'] = $proCity[2] . $post['address'];
            }
            if (!empty($post['sex'])) {
                $data['sex'] = $post['sex'];
            }
            if (!empty($post['birthday'])) {
                $data['birthday'] = $post['birthday'];
            }
            if (!empty($post['socialNumber'])) {
                $data['social_security_number'] = $post['socialNumber'];
            }
            if (!empty($post['email'])) {
                $data['email'] = $post['email'];
            }
            if (!empty($post['marital'])) {
                $data['marital_status'] = $post['marital'];
            }
            if (!empty($post['work'])) {
                $data['work'] = $post['work'];
            }

            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
            //编辑填写项
            foreach ($data as $key => $v) {
                $user->editPersonalInformation($user_id, $key, $v);
            }
            //修改填写状态
            $user->alterFillInfo($user_id);

            if (!empty($goUrl)) {
                $this->redirect(array($goUrl, 'encrypt_id' => $encrypt_id));
            } else {
                $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array(
                    'encrypt_id' => $encrypt_id
                )));
            }
        }

        $this->render('fillInfo', array(
            'goUrl' => $goUrl,
            'encrypt_id' => $encrypt_id,
            'info_arr' => $info_arr
        ));
    }

}