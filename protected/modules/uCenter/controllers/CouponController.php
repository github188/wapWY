<?php

/**
 * 优惠券
 *
 */
class CouponController extends UCenterController
{
    //获取优惠券
    public function actionGetCoupon()
    {
        if (isset($_GET['coupons_id']) && !empty($_GET['coupons_id'])) {
            $errorL = 1;
            if (isset($_GET['errorL']) && !empty($_GET['errorL'])) {
                $errorL = $_GET['errorL'];
            }

            $errorR = 1;
            if (isset($_GET['errorR']) && !empty($_GET['errorR'])) {
                $errorR = $_GET['errorR'];
            }


            if (isset($_GET['phone']) && !empty($_GET['phone'])) {
                $phone = $_GET['phone'];
            } else {
                $phone = '';
            }
            //获取优惠券id
            $coupon_id = $_GET['coupons_id'];
            //判断是否已登录
            $is_login = false;
            if (isset(Yii::app()->session['user_id']) && !empty(Yii::app()->session['user_id'])) {
                $is_login = true;
            }

            $user = new UserUC();
            $user_id = Yii::app()->session['user_id'];
            $res = json_decode($user->couponsDetail($_GET['coupons_id'], $user_id), true);
            $coupons = array();
            if ($res['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $coupons = $result['data'];
                }
            }
            //获取优惠券信息
            $re = json_decode($user->getCouponInfo($_GET['coupons_id']));
            if ($re->status == ERROR_NONE) {
                $merchant_id = $re->data->merchant_id;
                $merchant = Merchant::model()->findByPk($merchant_id);
                //获取商户信息
                $mer = json_decode($user->getWxapp($merchant_id));
                if ($mer->status == ERROR_NONE) {
                    $wechat_appid = $mer->data->appid;
                    $wechat_appsecret = $mer->data->appsecret;
                }
                //微信分享
                $wxlocation = new WxLocation();                
                $resultWxlocation = $wxlocation->Wxshare($merchant);
                $result = json_decode($resultWxlocation, true);
                $signPackage = $result;

                //获取在线商铺信息
                $result = json_decode($user->getOnlineshop($merchant_id));

                if ($result->status == ERROR_NONE) {
                    $onlineshop = $result->data;
                    $this->render('getCoupon', array(
                        'onlineshop' => $onlineshop,
                        'is_login' => $is_login,
                        'merchant_id' => $merchant_id,
                        'coupon_id' => $coupon_id,
                        'couponinfo' => $re->data,
                        'errorL' => $errorL,
                        'errorR' => $errorR,
                        'phone' => $phone,
                        'signPackage' => $signPackage,
                        'coupons' => $coupons
                    ));
                }
            }
        }
    }

    //判断手机号是否已注册
    public function actionIsRegist()
    {
        if (isset($_POST['phonenum']) && !empty($_POST['phonenum'])) {
            $user = new UserUC();
            $result = json_decode($user->IsRegist($_POST['phonenum'], $_POST['merchant_id']));
            if ($result->status == ERROR_NONE) {
                echo json_encode($result);
            } else {
                echo json_encode($result);
            }
        }
    }

    //登录
    public function actionLogin()
    {
        if (isset($_GET['phone']) && !empty($_GET['phone'])) {
            //登录账号
            $account = $_GET['phone'];
            //登录密码
            if (isset($_GET['pwd']) && !empty($_GET['pwd'])) {
                $pwd = $_GET['pwd'];
            } else {
                $pwd = '';
            }
            //商户id
            if (isset($_GET['merchant_id']) && !empty($_GET['merchant_id'])) {
                $merchant_id = $_GET['merchant_id'];
            } else {
                $merchant_id = '';
            }
            //获取优惠券id
            if (isset($_GET['coupon_id']) && !empty($_GET['coupon_id'])) {
                $coupon_id = $_GET['coupon_id'];
            }

            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $msg_pwd = $_GET['code'];
                //用户注册
                $user = new UserUC();
                $res = json_decode($user->userRegister($merchant_id, $account, $msg_pwd, $pwd, $pwd));
                if ($res->status == ERROR_NONE) {
                    $user_id = $res->data;
                    //登陆操作（保存会员session，保存购物车内容）
                    $this->loginOperate($user_id);
                    echo 'ppp';
                    //删除缓存中的短信验证码
                    $this->delMsgPwd($account);
                    $this->redirect(Yii::app()->createUrl('uCenter/coupon/ReceiveCoupons', array(
                        'coupon_id' => $coupon_id,
                        'merchant_id' => $merchant_id
                    )));
                } else {
                    $url = Yii::app()->createUrl('uCenter/coupon/GetCoupon', array(
                        'coupons_id' => $coupon_id,
                        'phone' => $account,
                        'errorR' => 2
                    ));
                    echo "<script>alert('" . $res->errMsg . "');window.location.href='$url'</script>";
                }
            } else {
                //验证账号和密码并登陆
                $user = new UserUC();
                $res = $user->checkAccount($merchant_id, $account, $pwd);
                $result = json_decode($res);
                if ($result->status == ERROR_NONE) {
                    $user_id = $result->data;
                    //登陆操作（保存会员session，保存购物车内容）
                    $this->loginOperate($user_id);
                    $this->redirect(Yii::app()->createUrl('uCenter/coupon/ReceiveCoupons', array(
                        'coupon_id' => $coupon_id,
                        'merchant_id' => $merchant_id
                    )));
                } else {
                    $url = Yii::app()->createUrl('uCenter/coupon/GetCoupon', array(
                        'coupons_id' => $coupon_id,
                        'phone' => $account,
                        'errorL' => 2
                    ));
                    echo "<script>alert('" . $result->errMsg . "');window.location.href='$url'</script>";
                }
            }

        }
    }

    /**
     * 会员登录操作（保存session/购物车）
     */
    public function loginOperate($user_id)
    {
        $user = new UserUC();

        Yii::app()->session['user_id'] = $user_id;
        $ali_open_id = Yii::app()->session['ali_open_id'];
        $wechat_open_id = Yii::app()->session['wechat_open_id'];
        $source = Yii::app()->session['source'];

        //将OpenId保存到数据库
        if ("alipay_wallet" == $source) {
            $user->setAliOpenId($user_id, $ali_open_id);
        } elseif ("wechat" == $source) {
            $res = $user->setWechatOpenId($user_id, $wechat_open_id);
        }


        //保存购物车操作
        //TODO
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
     * 领取优惠券
     */
    public function actionReceiveCoupons()
    {
        if (isset($_GET['coupon_id']) && !empty($_GET['coupon_id'])) {

            $coupons_id = $_GET['coupon_id'];
            $user_id = Yii::app()->session['user_id'];

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
                    $this->redirect(Yii::app()->createUrl('uCenter/coupon/CouponResult', array(
                        'coupon_id' => $id,
                    )));
                } else {
                    $status = $receive_result['status'];
                    $msg = $receive_result['errMsg'];
                    $this->redirect(Yii::app()->createUrl('uCenter/coupon/CouponFail', array(
                        'coupons_id' => $coupons_id,
                        'msg' => $msg,
                    )));
                }

            } else {
                $status = $jug_result['status'];
                $msg = $jug_result['errMsg'];
                $this->redirect(Yii::app()->createUrl('uCenter/coupon/CouponFail', array(
                    'coupons_id' => $coupons_id,
                    'msg' => $msg
                )));
            }
        }

    }

    //领取优惠券成功
    public function actionCouponResult()
    {
        if (isset($_GET['coupon_id']) && !empty($_GET['coupon_id'])) {
            $user = new UserUC();

            $result_user = json_decode($user->getUserCouponInfo($_GET['coupon_id']));
            if ($result_user->status == ERROR_NONE) {
                //获取商户信息
                $merchant_id = $result_user->data->merchant_id;
                $merchant = Merchant::model()->findByPk($merchant_id);
                $mer = json_decode($user->getWxapp($merchant_id));
                if ($mer->status == ERROR_NONE) {
                    $wechat_appid = $mer->data->appid;
                    $wechat_appsecret = $mer->data->appsecret;
                }
                //微信分享
                $wxlocation = new WxLocation();
                $resultWxlocation = $wxlocation->Wxshare($merchant);
                $result = json_decode($resultWxlocation, true);
                $signPackage = $result;

                $re = json_decode($user->getOnlineshop($merchant_id));
                $couponuser = json_decode($user->getUserList($_GET['coupon_id']));
                if ($re->status == ERROR_NONE) {
                    $this->render('couponResult', array(
                        'onlineshop' => $re->data,
                        'usercoupon' => $result_user->data,
                        'user' => $couponuser->data,
                        'signPackage' => $signPackage,
                        'merchant_id' => $merchant_id
                    ));
                }
            }
        }
    }

    //领取失败
    public function actionCouponFail()
    {
        if (isset($_GET['coupons_id']) && !empty($_GET['coupons_id'])) {
            $user = new UserUC();
            $couponuser = json_decode($user->getCouponId($_GET['coupons_id']));
            if ($couponuser->status == ERROR_NONE) {
                //获取商户信息
                $merchent_id = $couponuser->data->merchant_id;
                $mer = json_decode($user->getWxapp($merchent_id));
                $merchant = Merchant::model()->findByPk($merchant_id);
                if ($mer->status == ERROR_NONE) {
                    $wechat_appid = $mer->data->appid;
                    $wechat_appsecret = $mer->data->appsecret;
                }

                //微信分享
                $wxlocation = new WxLocation();
                $resultWxlocation = $wxlocation->Wxshare($merchant);
                $result = json_decode($resultWxlocation, true);
                $signPackage = $result;
                //获取在线商铺信息
                $re = json_decode($user->getOnlineshop($merchent_id));
                //获取领取过用户信息
                $myuser = json_decode($user->getUserInfo(Yii::app()->session['user_id']));
                //获取用户优惠券数量
                $num = json_decode($user->getUserCouponNum(Yii::app()->session['user_id'], $couponuser->data->type));
                if ($couponuser->status == ERROR_NONE) {
                    $this->render('couponFail', array(
                        'msg' => $_GET['msg'],
                        'user' => $couponuser->data,
                        'type' => $couponuser->data->type,
                        'num' => $num->data,
                        'coupons_id' => $_GET['coupons_id'],
                        'signPackage' => $signPackage,
                        'onlineshop' => $re->data,
                        'myuser' => $myuser->data
                    ));
                }
            }
        }
    }

    /**
     * 领取优惠券-one
     */
    public function actionNewGetCouponOne()
    {
        $userC = new UserUC();
        
        $mobil_phone = "";
        $tel = "";
        $user_arr = array();
        $alogin_arr = array();
        $plogin_arr = array();
        $forget_arr = array();

        if (isset($_GET['coupon_id']) && !empty($_GET['coupon_id'])) {
            $coupon_id = CHtml::encode($_GET['coupon_id']);
            $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
            $encrypt_id = $_GET['encrypt_id'];

            //营销活动类型
            if (isset($_GET['marketing_activity_type']) && !empty($_GET['marketing_activity_type'])) {
                $marketing_activity_type = $_GET['marketing_activity_type'];
            } else {
                $marketing_activity_type = '';
            }
            //营销活动id
            if (isset($_GET['marketing_activity_id']) && !empty($_GET['marketing_activity_id'])) {
                $marketing_activity_id = $_GET['marketing_activity_id'];
            } else {
                $marketing_activity_id = '';
            }

            if ($coupon_res['status'] == ERROR_NONE) {
                $coupon_model = $coupon_res['data'];

                if (!empty($_GET['encrypt_id'])) {
                    //Yii::app()->session['encrypt_id'] = $_GET['encrypt_id'];
                    Yii::app()->session['source'] = $_GET['source'];
                    //微信
                    Yii::app()->session['wechat_code'] = $_GET['code'];
                    Yii::app()->session['wechat_state'] = $_GET['state'];
                    //服务窗
                    Yii::app()->session['ali_app_id'] = $_GET['app_id'];
                    Yii::app()->session['ali_auth_code'] = $_GET['auth_code'];
                }

                //微信
                if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) && !isset(Yii::app()->session['wechat_code'])) {
                    $redirect_uri = USER_DOMAIN_COUPONS . '/newGetCouponOne?encrypt_id=' . $coupon_model['encrypt_id'] . '&coupon_id=' . $coupon_id . '&source=wechat' . '&marketing_activity_type=' . $marketing_activity_type . '&marketing_activity_id=' . $marketing_activity_id;
                    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $coupon_model['wechat_appid'] . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                    $this->redirect($url);
                }
                //服务窗
                if ((strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) && !isset(Yii::app()->session['ali_app_id'])) {
                    $resirect_url = USER_DOMAIN_COUPONS . '/newGetCouponOne?encrypt_id=' . $coupon_model['encrypt_id'] . '&coupon_id=' . $coupon_id . '&marketing_activity_type=' . $marketing_activity_type . '&marketing_activity_id=' . $marketing_activity_id;
                    $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $coupon_model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode($resirect_url);
                    $this->redirect($url);
                }

                $merchant_id = $coupon_model['merchant_id'];
                //Yii::app()->session['merchant_id'] = $merchant_id;
                //判断用户来源
                //微信
                if (!empty(Yii::app()->session['wechat_code'])) {
                    $code = Yii::app()->session['wechat_code'];
                    $wechat_appid = $coupon_model['wechat_appid'];
                    $wechat_appsecret = $coupon_model['wechat_appsecret'];
                    $this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret);

                    $wechat_open_id = Yii::app()->session['wechat_open_id'];
                    $wechat_res = json_decode($userC->coupeCheckLoginBefore($merchant_id, $wechat_open_id), true);
                    if ($wechat_res['status'] == ERROR_NONE) {
                        $tel = $wechat_res['tel'];
                        Yii::app()->session['user_id'] = $wechat_res['data'];
                    } else {
                        $tel = "";
                    }

                } elseif (!empty(Yii::app()->session['ali_app_id'])) { //服务窗
                    $appid = Yii::app()->session['ali_app_id'];
                    $this->getAliOpenId($appid);

                    $ali_open_id = Yii::app()->session['ali_open_id'];
                    $ali_res = $userC->coupeCheckAliLoginBefore($merchant_id, $ali_open_id);
                    if ($ali_res['status'] == ERROR_NONE) {
                        $tel = $ali_res['tel'];
                        Yii::app()->session['user_id'] = $ali_res['data'];
                    } else {
                        $tel = "";
                    }
                } else {//网页
                    $tel = "";
                }


                //注册
                if (!empty($_POST['User']) && isset($_POST['User'])) {
                    $post = $_POST['User'];

                    $mobil_phone = $post['MobilePhone'];
                    $user_arr['MobilePhone'] = $account = $post['MobilePhone'];
                    $user_arr['MsgPassword'] = $msg_pwd = $post['MsgPassword'];
                    $user_arr['Password'] = $pwd = $post['Password'];
                    $user_arr['Confirm'] = $confirm_pwd = $post['Confirm'];

                    $res = $userC->userRegister($merchant_id, $account, $msg_pwd, $pwd, $confirm_pwd);
                    $result = json_decode($res, true);

                    if ($result['status'] == ERROR_NONE) {

                        $user_id = $result['data'];
                        //注册操作（设置会员等级 之类）
                        //TODO

                        //登陆操作（保存会员session，保存购物车内容）
                        $this->loginOperate($user_id);

                        //删除缓存中的短信验证码
                        $this->delMsgPwd($account);

                        //跳转到领取页面
                        $this->redirect(Yii::app()->createUrl('uCenter/coupon/newGetCouponTwo', array(
                            'coupon_id' => $coupon_id,
                            'user_id' => $user_id,
                            'account' => $mobil_phone,
                            'marketing_activity_type' => $marketing_activity_type,
                            'marketing_activity_id' => $marketing_activity_id)));
                    } else {
                        $status = $result['status'];
                        $msg = $result['errMsg'];
                        Yii::app()->user->setFlash('error', $msg);
                    }
                }

                //账号密码登陆
                if (!empty($_POST['Alogin']) && isset($_POST['Alogin'])) {
                    $post_alogin = $_POST['Alogin'];

                    $mobil_phone = $post_alogin['MobilePhone'];
                    $alogin_arr['MobilePhone'] = $account = $post_alogin['MobilePhone'];
                    $alogin_arr['Password'] = $pwd = $post_alogin['Password'];

                    $res = $userC->checkAccount($merchant_id, $account, $pwd);
                    $result = json_decode($res, true);

                    if ($result['status'] == ERROR_NONE) {
                        $user_id = $result['data'];
                        //登陆操作（保存会员session，保存购物车内容）
                        $this->loginOperate($user_id);

                        //跳转到领取页面
                        $this->redirect(Yii::app()->createUrl('uCenter/coupon/newGetCouponTwo', array(
                            'coupon_id' => $coupon_id,
                            'user_id' => $user_id,
                            'account' => $mobil_phone,
                            'marketing_activity_type' => $marketing_activity_type,
                            'marketing_activity_id' => $marketing_activity_id)));
                    } else {
                        $status = $result['status'];
                        $msg = $result['errMsg'];
                        Yii::app()->user->setFlash('error', $msg);
                    }
                }
                //手机号登陆
                if (!empty($_POST['Plogin']) && isset($_POST['Plogin'])) {
                    $post_plogin = $_POST['Plogin'];

                    $mobil_phone = $post_plogin['MobilePhone'];
                    $plogin_arr['MobilePhone'] = $account = $post_plogin['MobilePhone'];
                    $plogin_arr['MsgPassword'] = $msg_pwd = $post_plogin['MsgPassword'];

                    $flag = $userC->checkMsgPwd($account, $msg_pwd);
                    if ($flag) {
                        $user_id = $userC->findUserId($merchant_id, $account);
                        //登陆操作（保存会员session，保存购物车内容）
                        $this->loginOperate($user_id);

                        //删除缓存中的短信验证码
                        $this->delMsgPwd($account);

                        $this->redirect(Yii::app()->createUrl('uCenter/coupon/newGetCouponTwo', array(
                            'coupon_id' => $coupon_id,
                            'user_id' => $user_id,
                            'account' => $mobil_phone,
                            'marketing_activity_type' => $marketing_activity_type,
                            'marketing_activity_id' => $marketing_activity_id)));
                    } else {
                        Yii::app()->user->setFlash('error', '短信验证失败');
                    }
                }
                //忘记密码
                if (!empty($_POST['Forget']) && isset($_POST['Forget'])) {
                    $post_forget = $_POST['Forget'];

                    $mobil_phone = $post_forget['MobilePhone'];
                    $forget_arr['MobilePhone'] = $account = $post_forget['MobilePhone'];
                    $forget_arr['MsgPassword'] = $msg_pwd = $post_forget['MsgPassword'];
                    $forget_arr['Password'] = $pwd = $post_forget['Password'];
                    $forget_arr['Confirm'] = $confirm_pwd = $post_forget['Confirm'];

                    $flag = $userC->checkMsgPwd($account, $msg_pwd);
                    if ($flag) {
                        $res = $userC->setNewPassword($merchant_id, $account, $pwd, $confirm_pwd);
                        $result = json_decode($res, true);
                        if ($result['status'] == ERROR_NONE) {
                            $user_id = $result['data'];
                            //登陆操作（保存会员session，保存购物车内容）
                            $this->loginOperate($user_id);

                            //删除缓存中的短信验证码
                            $this->delMsgPwd($account);

                            $this->redirect(Yii::app()->createUrl('uCenter/coupon/newGetCouponTwo', array(
                                'coupon_id' => $coupon_id,
                                'user_id' => $user_id,
                                'account' => $mobil_phone,
                                'marketing_activity_type' => $marketing_activity_type,
                                'marketing_activity_id' => $marketing_activity_id

                            )));
                        } else {
                            $status = $result['status'];
                            $msg = $result['errMsg'];
                            Yii::app()->user->setFlash('error', $msg);
                        }

                    } else {
                        Yii::app()->user->setFlash('error', '短信验证失败');
                    }
                }
                //获取商户信息
                $mer = json_decode($userC->getWxapp($merchant_id));
                $merchant = Merchant::model()->findByPk($merchant_id);
                if ($mer->status == ERROR_NONE) {
                    $wechat_appid = $mer->data->appid;
                    $wechat_appsecret = $mer->data->appsecret;
                }
                //微信分享
                $wxlocation = new WxLocation();
                $resultWxlocation = $wxlocation->Wxshare($merchant);
                $result = json_decode($resultWxlocation, true);
                $signPackage = $result;

                //获取在线商铺信息
                $result = json_decode($userC->getOnlineshop($merchant_id));

                if ($result->status == ERROR_NONE) {
                    $onlineshop = $result->data;
                }


                $this->render('newGetCouponOne', array(
                    'signPackage' => $signPackage,
                    'onlineshop' => $onlineshop,
                    'coupon_id' => $coupon_id,
                    'coupon_model' => $coupon_model,
                    'tel' => $tel,
                    'mobil_phone' => $mobil_phone,
                    'user' => $user_arr,
                    'alogin' => $alogin_arr,
                    'plogin' => $plogin_arr,
                    'forget' => $forget_arr,
                    'marketing_activity_type' => $marketing_activity_type,
                    'marketing_activity_id' => $marketing_activity_id,
                    'encrypt_id' => $encrypt_id
                ));
            } else {

            }

        } else {
            //todo
        }
    }

    /**
     * 领取
     */
    public function actionNewGetCouponTwo()
    {
        $userC = new UserUC();
        $coupon_id = $_GET['coupon_id'];
        $user_id = $_GET['user_id'];
        $account = $_GET['account'];

        //获取营销活动类型
        $marketing_activity_type = $_GET['marketing_activity_type'];
        //获取营销活动id
        $marketing_activity_id = $_GET['marketing_activity_id'];

        $model = UserCoupons::model()->findAll('user_id = :user_id', array(':user_id' => $user_id));

        $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
        if ($coupon_res['status'] == ERROR_NONE) {
            $coupon_model = $coupon_res['data'];

            //判断用户是否可以领取优惠券
            $jug_res = $userC->judgeUserCoupons($user_id, $coupon_id);
            $jug_result = json_decode($jug_res, true);
            if ($jug_result['status'] == ERROR_NONE) {
                //领取优惠券
                $receive_res = $userC->newReceiveCoupons($user_id, $coupon_id, $marketing_activity_type, $marketing_activity_id);
                $receive_result = json_decode($receive_res, true);

                if ($receive_result['status'] == ERROR_NONE) {
                    $id = $receive_result['data'];
                    Yii::app()->session['card_add_flag'] = '1';
                    $this->redirect(Yii::app()->createUrl('uCenter/coupon/newCouponSuccess', array(
                        'coupon_id' => $coupon_id,
                        'account' => $account,
                    )));
                } else {
                    $status = $receive_result['status'];
                    $msg = $receive_result['errMsg'];
                    $this->redirect(Yii::app()->createUrl('uCenter/coupon/newCouponFail', array(
                        'coupon_id' => $coupon_id,
                        'msg' => $msg,
                    )));
                }

            } else {
                $status = $jug_result['status'];
                $msg = $jug_result['errMsg'];
                $this->redirect(Yii::app()->createUrl('uCenter/coupon/newCouponFail', array(
                    'coupon_id' => $coupon_id,
                    'msg' => $msg
                )));
            }
        }

        $this->render('newGetCouponTwo', array('coupon_id' => $coupon_id, 'coupon_model' => $coupon_model, 'account' => $account));
    }

    /**
     * 判断是否登陆过
     */
    public function checkWechatOpenId()
    {
        $user = new UserUC();

        $merchant_id = Yii::app()->session['merchant_id'];
        $wechat_open_id = Yii::app()->session['wechat_open_id'];

        $res = $user->checkWechatOpenId($merchant_id, $wechat_open_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                Yii::app()->session['user_id'] = $result['data'];
            }
        } else {

        }
    }

    /**
     * 领取失败
     */
    public function actionNewCouponFail()
    {
        $userC = new UserUC();
        if (!empty($_GET['coupon_id']) && isset($_GET['coupon_id'])) {
            $coupon_id = $_GET['coupon_id'];
            $errMsg = $_GET['msg'];

            $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
            if ($coupon_res['status'] == ERROR_NONE) {
                $coupon_model = $coupon_res['data'];
            }

            $this->render('newCouponFail', array('coupon_id' => $coupon_id, 'coupon_model' => $coupon_model, 'msg' => $errMsg));
        }
    }

    /**
     * 领取成功
     */
    public function actionNewCouponSuccess()
    {
        $userC = new UserUC();
        if (!empty($_GET['coupon_id']) && isset($_GET['coupon_id'])) {
            $coupon_id = $_GET['coupon_id'];
            $account = $_GET['account'];
            $flag = Yii::app()->session['card_add_flag'];

            $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
            if ($coupon_res['status'] == ERROR_NONE) {
                $coupon_model = $coupon_res['data'];
            }

            //获取优惠券的领取信息
            $coupon_record_res = $userC->getCouponRecord($coupon_id);
            if ($coupon_record_res['status'] == ERROR_NONE) {
                $coupon_record = $coupon_record_res['data'];
            }
            $merchant_id = Yii::app()->session['merchant_id'];
            $merchant = Merchant::model()->findByPk($merchant_id);
            //获取商户信息
            $mer = json_decode($userC->getWxapp($merchant_id));
            if ($mer->status == ERROR_NONE) {
                $wechat_appid = $mer->data->appid;
                $wechat_appsecret = $mer->data->appsecret;
            }
            //微信分享
            $wxlocation = new WxLocation();
            $resultWxlocation = $wxlocation->Wxshare($merchant);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;

            //卡券
            $wx_card = new WxLocation();
            $card_id = $coupon_model['card_id'];
            $card_res = $wxlocation->Wxcard($merchant, $card_id);
            $cardSign = json_decode($card_res, true);

            //获取在线商铺信息
            $result = json_decode($userC->getOnlineshop($merchant_id));

            if ($result->status == ERROR_NONE) {
                $onlineshop = $result->data;
            }

            $this->render('newCouponSuccess', array('cardSign' => $cardSign, 'signPackage' => $signPackage, 'onlineshop' => $onlineshop, 'coupon_id' => $coupon_id, 'coupon_model' => $coupon_model, 'account' => $account, 'coupon_record' => $coupon_record, 'flag' => $flag));
// 			$this->render('newCouponSuccess', array('coupon_id'=>$coupon_id, 'coupon_model'=>$coupon_model, 'account'=>$account, 'coupon_record'=>$coupon_record));
        }
    }

    public function actionWxAddCardFlag()
    {
        Yii::app()->session['card_add_flag'] = 0;
    }

    /**
     * 同步到微信卡包
     */
    public function tongBu()
    {
        $userC = new UserUC();
        if (!empty($_GET['coupon_id']) && isset($_GET['coupon_id'])) {
            $coupon_id = $_GET['coupon_id'];
            $account = $_GET['account'];
            $flag = Yii::app()->session['card_add_flag'];

            $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
            if ($coupon_res['status'] == ERROR_NONE) {
                $coupon_model = $coupon_res['data'];
            }

            //获取商户信息
            $merchant = Merchant::model()->findByPk($merchant_id);
            $mer = json_decode($userC->getWxapp($merchant_id));
            if ($mer->status == ERROR_NONE) {
                $wechat_appid = $mer->data->appid;
                $wechat_appsecret = $mer->data->appsecret;
            }
            //微信分享
            $wxlocation = new WxLocation();
            $resultWxlocation = $wxlocation->Wxshare($merchant);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;
            //卡券
            $wx_card = new WxLocation();
            $card_id = $coupon_model['card_id'];
            $card_res = $wxlocation->Wxcard($merchant, $card_id);
            $cardSign = json_decode($card_res, true);

            $this->render('personaldetail', array('cardSign' => $cardSign, 'coupon_id' => $coupon_id, 'signPackage' => $signPackage, 'coupon_model' => $coupon_model, 'account' => $account, 'flag' => $flag));

        }
    }

    /**
     * 优惠券详情页面
     */
    public function actionNewCouponDetail()
    {
        $userC = new UserUC();
        $coupon_id = $_GET['coupon_id'];
        //优惠券信息
        $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
        if ($coupon_res['status'] == ERROR_NONE) {
            $coupon_model = $coupon_res['data'];
        }
        $merchant_id = Yii::app()->session['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        //获取商户信息
        $mer = json_decode($userC->getWxapp($merchant_id));
        if ($mer->status == ERROR_NONE) {
            $wechat_appid = $mer->data->appid;
            $wechat_appsecret = $mer->data->appsecret;
        }
        //获取商户门店信息
        $store = $userC->getStoreName($merchant_id);
        if ($store['status'] == ERROR_NONE) {
            $store_arr = $store['data'];
            $store_num = $store['num'];
        }
        $st = explode(",", $coupon_model['store_limit']);
        $num = count($st) - 2;
        $store_name = "";

        if ($num == $store_num) {
            $store_name = "全部门店";
        } else {
            foreach ($st as $k => $v) {
                if (!empty($store_name)) {
                    $store_name .= ',';
                }
                if (!empty($v)) {
                    $store_name .= $store_arr[$v];
                }
            }
        }

        //微信分享
        $wxlocation = new WxLocation();
        $resultWxlocation = $wxlocation->Wxshare($merchant);
        $result = json_decode($resultWxlocation, true);
        $signPackage = $result;
        //获取在线商铺信息
        $result = json_decode($userC->getOnlineshop($merchant_id));

        if ($result->status == ERROR_NONE) {
            $onlineshop = $result->data;
        }

        $this->render('newCouponDetail', array('coupon_model' => $coupon_model, 'signPackage' => $signPackage, 'onlineshop' => $onlineshop, 'coupon_id' => $coupon_id, 'store_name' => $store_name));
    }

    /**
     * 获取微信openid
     */
    public function getWechatOpenId($code, $wechat_appid, $wechat_appsecret)
    {
        $wechat = new WechatC();
        //获取access_token以及用户openid
        $get_access_res = $wechat->getUserAccessCode($code, $wechat_appid, $wechat_appsecret);
        if (isset($get_access_res['openid'])) {
            $open_id = $get_access_res['openid'];
            $access_token = $get_access_res['access_token'];

            Yii::app()->session['wechat_open_id'] = $open_id;
        }
    }

    /**
     * 获取 服务窗 OpenId
     */
    public function getAliOpenId($appid)
    {
        if (!empty(Yii::app()->session['ali_auth_code'])) {                   //获取auth_code
            $auth_code = Yii::app()->session['ali_auth_code'];
        }

        //获取并保存OpenId
        $api = new AliApi('AliApi');
        $response = $api->getOpenId($appid, $auth_code);
        if ($response != null) {
            if (isset($response->alipay_system_oauth_token_response)) {
                $open_id = $response->alipay_system_oauth_token_response->alipay_user_id;
                Yii::app()->session['ali_open_id'] = $open_id;
                $access_token = $response->alipay_system_oauth_token_response->access_token;
                Yii::app()->session['access_token'] = $access_token;
            }
        }
    }

    /**
     * 新优惠券列表
     */
    public function actionCouponList()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $merchant = new MerchantC();
        $result = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $result->data->id;

        $list = array();
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

        //修改优惠券状态（过期）
        $user->changeCouponsStatus($user_id);

        $list_res = $user->newGetCouponsList($merchant_id, $user_id);

        if ($list_res['status'] == ERROR_NONE) {
            $list = $list_res['data'];
        }
        $now = date("y-m-d h:i:s");
        $this->render('couponList', array(
            'list' => $list,
            'now' => $now,
            'encrypt_id' => $encrypt_id
        ));
    }

    /*
     * 优惠券详情
     * */
    public function actionCouponDetail()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $user = new UserUC();
            $result = json_decode($user->getNewUserCouponInfo($_GET['id']));

            if ($result->status == ERROR_NONE) {
                $data = $result->data;
                $wechat_appid = $data->wechat_appid;
                $wechat_appsecret = $data->wechat_appsecret;

                $num_arr = str_split($data->code);
                $code = '';
                for ($i = 0; $i < 12; $i++) {
                    if ($i % 3 == 0 && $i != 0) {
                        $code .= '-';
                    }
                    $code .= $num_arr[$i];
                }

                //获取商户门店信息
                $merchant_id = Yii::app()->session['merchant_id'];
                $merchant = Merchant::model()->findByPk($merchant_id);
                $store = $user->getStoreName($merchant_id);
                if ($store['status'] == ERROR_NONE) {
                    $store_arr = $store['data'];
                    $store_num = $store['num'];
                }
                $st = explode(",", $data->store_limit);
                $num = count($st) - 2;
                $store_name = "";
                if ($num == $store_num) {
                    $store_name = "全部门店";
                } else {
                    foreach ($st as $k => $v) {
                        if (!empty($store_name)) {
                            $store_name .= ',';
                        }
                        if (!empty($v)) {
                            $store_name .= $store_arr[$v];
                        }
                    }
                }

                //微信分享
                $wxlocation = new WxLocation();
                $resultWxlocation = $wxlocation->Wxshare($merchant);
                $result = json_decode($resultWxlocation, true);
                $signPackage = $result;


                $this->render('couponDetail', array(
                    'data' => $data,
                    'signPackage' => $signPackage,
                    'code' => $code,
                    'store_name' => $store_name,
                ));
            }
        }
    }


}			
