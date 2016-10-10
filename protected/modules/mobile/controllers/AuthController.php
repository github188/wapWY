<?php

class AuthController extends MobileController
{
    //登录
    public function actionLogin()
    {
        if (isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        /*if (!empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array(
                'encrypt_id' => $encrypt_id
            )));
        }*/

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new MobileUserUC();

            $account = $post['MobilePhone'];
            $pwd = $post['Password'];

            $res = $user->checkAccount($encrypt_id, $account, $pwd);
            $result = json_decode($res, true);

            if ($result['status'] == ERROR_NONE) {
                $user_id = $result['data'];
                Yii::app()->session[$encrypt_id . 'user_id'] = $user_id;

                $merchant = $user->getMerchantWithId($encrypt_id);
                $merchant_id = $merchant->id;

                $login_ip = Yii::app()->request->userHostAddress; //ip地址
                $login_client = Yii::app()->session['source']; //客户端
                switch ($login_client) {
                    case 'wechat':
                        $login_client = 1;
                        break;
                    case 'alipay':
                        $login_client = 2;
                        break;
                    case 'web':
                        $login_client = 3;
                        break;
                }
                //保存登录信息
                $user->saveLoginInfo(Yii::app()->session[$encrypt_id . 'user_id'], $login_ip, $login_client);

                //会员绑定
                if (Yii::app()->session['source'] == 'wechat') {
                    //查找open_id对应的粉丝
                    $res_fans = json_decode($user->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                    if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
                        //查找登录用户的open_id
                        $res_user = json_decode($user->getWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
                        if ($res_user['status'] == ERROR_NONE) {
                            $user_open_id = $res_user['data'];
                            //判断登录用户的open_id和session里的open_id是否一致
                            if (Yii::app()->session[$encrypt_id . 'open_id'] != $user_open_id) { //不一致
                                //查找是否存在与该粉丝同样open_id的会员
                                $res_user_same_open_id = json_decode($user->getUserByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                                if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
                                    //解除会员微信粉丝绑定
                                    $user->clearUserWechatBind($res_user_same_open_id['data']['id']);
                                }

                                //判断登录用户是否绑定粉丝
                                if ($user_open_id != '') {
                                    //查找会员绑定的粉丝
                                    $res_fans_same_open_id = json_decode($user->getNewFansByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                                    if ($res_fans_same_open_id['status'] == ERROR_NONE) {
                                        //解除粉丝绑定
                                        $user->clearFansBind($res_fans_same_open_id['data']['id']);
                                    }
                                }

                                //修改会员信息
                                $user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
                            }
                        }
                    } else { //无粉丝
                        //将当前的open_id保存到用户记录上
                        $user->setWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id'], Yii::app()->session[$encrypt_id . 'open_id']);
                    }
                } elseif (Yii::app()->session['source'] == 'alipay') {
                    //查找open_id对应的粉丝
                    $res_fans = json_decode($user->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
                    if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
                        //查找登录用户的open_id
                        $res_user = json_decode($user->getAlipayOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
                        if ($res_user['status'] == ERROR_NONE) {
                            $user_open_id = $res_user['data'];
                            //判断登录用户的open_id和session里的open_id是否一致
                            if (Yii::app()->session[$encrypt_id . 'open_id'] != $user_open_id) { //不一致
                                //查找是否存在与该粉丝同样open_id的会员
                                $res_user_same_open_id = json_decode($user->getUserByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
                                if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
                                    //解除会员支付宝粉丝绑定
                                    $user->clearUserAlipayBind($res_user_same_open_id['data']['id']);
                                }

                                //判断登录用户是否绑定粉丝
                                if ($user_open_id != '') {
                                    //查找会员绑定的粉丝
                                    $res_fans_same_open_id = json_decode($user->getNewFansByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
                                    if ($res_fans_same_open_id['status'] == ERROR_NONE) {
                                        //解除粉丝绑定
                                        $user->clearFansBind($res_fans_same_open_id['data']['id']);
                                    }
                                }

                                //修改会员信息
                                $user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
                            }
                        }
                    } else { //无粉丝
                        //将当前的open_id保存到用户记录上
                        $user->setAliOpenId(Yii::app()->session[$encrypt_id . 'user_id'], Yii::app()->session[$encrypt_id . 'open_id']);
                    }
                }

                //查询用户是否填写了必填项
                $res_flag = $user->checkUserFillInfo(Yii::app()->session[$encrypt_id . 'user_id']);
                //获取商户的必填项
                $merchant = new MerchantC();
                $res = json_decode($merchant->getFillInfo($merchant_id), true);

                //判断跳转地址是否为空,跳转到点击进登陆页面的页面
                if (isset($_POST['goUrl']) && !empty($_POST['goUrl'])) {
                    $url = $_POST['goUrl'];
                    if ($res['status'] == ERROR_NONE && $res_flag) {
                        //完善信息
                        $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fillInfo', array(
                            'goUrl' => $url,
                            'encrypt_id' => $encrypt_id
                        )));
                    } else {
                        $this->redirect($url, array('encrypt_id' => $encrypt_id));
                    }
                } else {
                    if ($res['status'] == ERROR_NONE && $res_flag) {
                        //完善信息
                        $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fillInfo', array(
                            'encrypt_id' => $encrypt_id
                        )));
                    } else {
                        $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array(
                            'encrypt_id' => $encrypt_id
                        )));
                    }
                }
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        }
        if (!empty($_GET['goUrl'])) {
            $this->render('login', array(
                'goUrl' => $_GET['goUrl'],
                'encrypt_id' => $encrypt_id
            ));
        } else {
            $this->render('login', array(
                'encrypt_id' => $encrypt_id
            ));
        }
    }

    //注册
    public function actionRegister()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        /*if (!empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array(
                'encrypt_id' => $encrypt_id
            )));
        }*/

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new MobileUserUC();

            $re = json_decode($user->getMerchant($encrypt_id));
            $merchant_id = $re->data->id;

            $account = $post['MobilePhone'];
            $msg_pwd = $post['MsgPassword'];
            $pwd = $post['Password'];
            $confirm_pwd = $post['Confirm'];

            $is_res = $user->accountExist($merchant_id, $account);
            if ($is_res) {
                Yii::app()->user->setFlash('error', '该手机号已被注册');
            } else {
                //新增一条记录
                $res = $user->userRegister($merchant_id, $account, $msg_pwd, $pwd, $confirm_pwd);

                $result = json_decode($res, true);

                if ($result['status'] == ERROR_NONE) {
                    $user_id = $result['data'];
                    Yii::app()->session[$encrypt_id . 'user_id'] = $user_id;

                    //会员绑定
                    if (Yii::app()->session['source'] == 'wechat') {
                        //查找open_id对应的粉丝
                        $res_fans = json_decode($user->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                        if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
                            //查找是否存在与该粉丝同样open_id的会员
                            $res_user_same_open_id = json_decode($user->getUserByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                            if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
                                //解除会员微信粉丝绑定
                                $user->clearUserWechatBind($res_user_same_open_id['data']['id']);
                            }

                            //判断登录用户是否绑定粉丝（open_id字段是否为空）
                            $res_user = json_decode($user->getWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
                            if ($res_user['status'] == ERROR_NONE) {
                                $user_open_id = $res_user['data'];
                                if ($user_open_id != '') {
                                    //查找会员绑定的粉丝
                                    $res_fans_same_open_id = json_decode($user->getNewFansByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                                    if ($res_fans_same_open_id['status'] == ERROR_NONE) {
                                        //解除粉丝绑定
                                        $user->clearFansBind($res_fans_same_open_id['data']['id']);
                                    }
                                }
                            }

                            //修改会员信息
                            $user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
                        } else { //无粉丝
                            //将当前的open_id保存到用户记录上
                            $user->setWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id'], Yii::app()->session[$encrypt_id . 'open_id']);
                        }
                    } elseif (Yii::app()->session['source'] == 'alipay') {
                        //查找open_id对应的粉丝
                        $res_fans = json_decode($user->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
                        if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
                            //查找是否存在与该粉丝同样open_id的会员
                            $res_user_same_open_id = json_decode($user->getUserByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
                            if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
                                //解除会员支付宝粉丝绑定
                                $user->clearUserAlipayBind($res_user_same_open_id['data']['id']);
                            }

                            //判断登录用户是否绑定粉丝（open_id字段是否为空）
                            $res_user = json_decode($user->getAlipayOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
                            if ($res_user['status'] == ERROR_NONE) {
                                $user_open_id = $res_user['data'];
                                if ($user_open_id != '') {
                                    //查找会员绑定的粉丝
                                    $res_fans_same_open_id = json_decode($user->getNewFansByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
                                    if ($res_fans_same_open_id['status'] == ERROR_NONE) {
                                        //解除粉丝绑定
                                        $user->clearFansBind($res_fans_same_open_id['data']['id']);
                                    }
                                }
                            }

                            //修改会员信息
                            $user->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
                        } else { //无粉丝
                            //将当前的open_id保存到用户记录上
                            $user->setAliOpenId(Yii::app()->session[$encrypt_id . 'user_id'], Yii::app()->session[$encrypt_id . 'open_id']);
                        }
                    }

                    //删除缓存中的短信验证码
                    $this->delMsgPwd($account);

                    //查询用户是否填写了必填项
                    $res_flag = $user->checkUserFillInfo(Yii::app()->session[$encrypt_id . 'user_id']);

                    //查询商户的必填项
                    $merchant = new MerchantC();
                    $res = json_decode($merchant->getFillInfo($merchant_id), true);

                    if (isset($_POST['goUrl']) && !empty($_POST['goUrl'])) {
                        $url = $_POST['goUrl'];
                        if ($res['status'] == ERROR_NONE && $res_flag) {
                            //完善信息
                            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fillInfo', array(
                                'goUrl' => $url,
                                'encrypt_id' => $encrypt_id
                            )));
                        } else {
                            $this->redirect($url, array('encrypt_id' => $encrypt_id));
                        }
                    } else {
                        if ($res['status'] == ERROR_NONE && $res_flag) {
                            //完善信息
                            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fillInfo', array(
                                'encrypt_id' => $encrypt_id
                            )));
                        } else {
                            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array(
                                'encrypt_id' => $encrypt_id
                            )));
                        }
                    }
                } else {
                    $msg = $result['errMsg'];
                    Yii::app()->user->setFlash('error', $msg);
                }
            }
        }

        $goUrl = isset($_GET['goUrl']) && !empty($_GET['goUrl']) ? $_GET['goUrl'] : '';
        $this->render('register', array(
            'goUrl' => $goUrl,
            'encrypt_id' => $encrypt_id
        ));
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
     * 找回密码-验证手机号
     */
    public function actionFindPwdOfCheck()
    {
        $user = new UserUC();

        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        if (isset($_POST['User']) && !empty($_POST['User'])) {
            $post = $_POST['User'];
            $account = $post['MobilePhone'];
            $msg_pwd = $post['MsgPassword'];

            $flag = $user->checkMsgPwd($account, $msg_pwd);
            if ($flag) {
                $this->redirect(Yii::app()->createUrl('mobile/auth/setNewPassword', array(
                    'account' => $account,
                    'encrypt_id' => $encrypt_id
                )));
            } else {
                Yii::app()->user->setFlash('error', '短信验证失败');
            }
        }

        $this->render('findPwdOfCheck', array(
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 找回密码-设置新的密码
     */
    public function actionSetNewPassword()
    {
        $account = isset($_GET['account']) ? $_GET['account'] : '';
        $encrypt_id = isset($_GET['encrypt_id']) ? $_GET['encrypt_id'] : '';

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();

            $merchat = new MerchantC();
            $result = json_decode($merchat->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $result->data->id;

            $new_pwd = $post['NewPassword'];
            $con_pwd = $post['ConfirmPassword'];

            $res = $user->setNewPassword($merchant_id, $account, $new_pwd, $con_pwd);
            $result = json_decode($res, true);

            if ($result['status'] == ERROR_NONE) {
                //删除缓存中的短信验证码
                $this->delMsgPwd($account);
                Yii::app()->user->setFlash('success', '保存成功');
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        }

        $this->render('setNewPassword', array(
            'encrypt_id' => $encrypt_id
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

}