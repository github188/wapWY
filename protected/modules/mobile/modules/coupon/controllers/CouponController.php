<?php

/**
 * 优惠券
 *
 */
class CouponController extends CouponsController
{
    /**
     * 将缓存中的短信验证码删除
     * @param $phone_num 手机号
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
     * 领取优惠券页面
     */
    public function actionGetCoupon()
    {
        if (isset($_GET['qcode']) && !empty($_GET['qcode'])) {
            $qcode = $_GET['qcode'];
        }
        
        $marketing_activity_type = '';
        $marketing_activity_id = '';
        if (isset($_GET['marketing_activity_type']) && !empty($_GET['marketing_activity_type'])) {
            $marketing_activity_type = $_GET['marketing_activity_type'];
        }
        
        if (isset($_GET['marketing_activity_id']) && !empty($_GET['marketing_activity_id'])) {
            $marketing_activity_id = $_GET['marketing_activity_id'];
        }

        //通过code获取优惠券信息
        $couponSC = new MobileCouponsSC();
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);
        if ($res_coupon['status'] == ERROR_NONE) {
            $coupon = $res_coupon['data'];
            $coupon_id = $coupon['id'];
            $merchant_id = $coupon['merchant_id'];
            $encrypt_id = $coupon['encrypt_id'];

            $userUC = new MobileUserUC();

            //获取商户信息
            $merchant = $userUC->getMerchantWithId($encrypt_id);

            //微信分享
            $wxlocation = new WxLocation();
            $resultWxlocation = $wxlocation->Wxshare($merchant);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;

            //获取在线商铺信息
            $res_onlineshop = json_decode($userUC->getOnlineshop($merchant_id), true);

            if ($res_onlineshop['status'] == ERROR_NONE) {
                $onlineshop = $res_onlineshop['data'];
            }

            if (!empty($coupon['use_time_interval'])) {
                $use_time = json_decode($coupon['use_time_interval'], true);
                $use_time_interval = '';
                foreach ($use_time as $v) {
                    switch ($v['type']) {
                        case 'MONDAY':
                            $v['type'] = '周一';
                            break;
                        case 'TUESDAY':
                            $v['type'] = '周二';
                            break;
                        case 'WEDNESDAY':
                            $v['type'] = '周三';
                            break;
                        case 'THURSDAY':
                            $v['type'] = '周四';
                            break;
                        case 'FRIDAY':
                            $v['type'] = '周五';
                            break;
                        case 'SATURDAY':
                            $v['type'] = '周六';
                            break;
                        case 'SUNDAY':
                            $v['type'] = '周日';
                            break;
                    }
                    $use_time_interval .= $v['type'] . '、';
                }

                //去除最后一个字符
                //$use_time_interval = mb_substr($use_time_interval, 0, -1);
                if (!empty($use_time[0]['begin_hour'])) {
                    $use_time_interval .= $use_time[0]['begin_hour'] . ':00至';
                } else {
                    $use_time_interval .= '00:00至';
                }
                if (!empty($use_time[0]['end_hour'])) {
                    $use_time_interval .= $use_time[0]['end_hour'] . ':00';
                } else {
                    $use_time_interval .= '24:00';
                }

                $coupon['use_time_interval'] = $use_time_interval;
            }

            //修改优惠券浏览次数
            $couponSC->setCouponPV($coupon['id']);
            //插入优惠券浏览人数
            $couponSC->setCouponIp($coupon['id']);

            //是否显示公众号
            $is_qcode = false;

            if (Yii::app()->session['source'] == 'wechat') {
                if (!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) {
                    $is_qcode = true;
                }
            } elseif (Yii::app()->session['source'] == 'alipay') {
                if (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode)) {
                    $is_qcode = true;
                }
            } else {
                if ((!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) || (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode))) {
                    $is_qcode = true;
                }
            }

            $open_id = Yii::app()->session[$encrypt_id . 'user_id'];
            if (!empty($open_id)) {
                $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
                //判断用户是否可以领取优惠券
                $jug_res = json_decode($userUC->judgeUserCoupons($user_id, $coupon_id), true);
            }

            $this->render('getCoupon', array(
                'signPackage' => $signPackage,
                'onlineshop' => $onlineshop,
                'coupon' => $coupon,
                'is_qcode' => $is_qcode,
                'jug_res' => $jug_res,
                'marketing_activity_type' => $marketing_activity_type,
                'marketing_activity_id' => $marketing_activity_id
            ));
        }
    }

    /**
     * 优惠券图文详情
     */
    public function actionCouponElecDetail()
    {
        if (isset($_GET['qcode']) && !empty($_GET['qcode'])) {
            $qcode = $_GET['qcode'];
        }

        //通过code获取优惠券信息
        $couponSC = new MobileCouponsSC();
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);
        if ($res_coupon['status'] == ERROR_NONE) {
            $coupon = $res_coupon['data'];
            $merchant_id = $coupon['merchant_id'];
            $encrypt_id = $coupon['encrypt_id'];

            $userUC = new MobileUserUC();

            //获取商户信息
            $merchant = $userUC->getMerchantWithId($encrypt_id);

            //微信分享
            $wxlocation = new WxLocation();
            $resultWxlocation = $wxlocation->Wxshare($merchant);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;

            //获取在线商铺信息
            $res_onlineshop = json_decode($userUC->getOnlineshop($merchant_id), true);

            if ($res_onlineshop['status'] == ERROR_NONE) {
                $onlineshop = $res_onlineshop['data'];
            }

            if (!empty($coupon['use_time_interval'])) {
                $use_time = json_decode($coupon['use_time_interval'], true);
                $use_time_interval = '';
                foreach ($use_time as $v) {
                    switch ($v['type']) {
                        case 'MONDAY':
                            $v['type'] = '周一';
                            break;
                        case 'TUESDAY':
                            $v['type'] = '周二';
                            break;
                        case 'WEDNESDAY':
                            $v['type'] = '周三';
                            break;
                        case 'THURSDAY':
                            $v['type'] = '周四';
                            break;
                        case 'FRIDAY':
                            $v['type'] = '周五';
                            break;
                        case 'SATURDAY':
                            $v['type'] = '周六';
                            break;
                        case 'SUNDAY':
                            $v['type'] = '周日';
                            break;
                    }
                    $use_time_interval .= $v['type'] . '、';
                }

                //去除最后一个字符
                //$use_time_interval = mb_substr($use_time_interval, 0, -1);
                if (!empty($use_time[0]['begin_hour'])) {
                    $use_time_interval .= $use_time[0]['begin_hour'] . ':00至';
                } else {
                    $use_time_interval .= '00:00至';
                }
                if (!empty($use_time[0]['end_hour'])) {
                    $use_time_interval .= $use_time[0]['end_hour'] . ':00';
                } else {
                    $use_time_interval .= '24:00';
                }

                $coupon['use_time_interval'] = $use_time_interval;
            }

            $this->render('couponElecDetail', array(
                'signPackage' => $signPackage,
                'onlineshop' => $onlineshop,
                'coupon' => $coupon
            ));
        }
    }

    /**
     * 领取
     */
    public function actionReceiveCoupon()
    {
        $qcode = $_GET['qcode'];
        $encrypt_id = $_GET['encrypt_id'];

        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/coupon/coupon/getCoupon', array(
                'qcode' => $qcode
            )));
        }
        $couponSC = new MobileCouponsSC();
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);
        $coupon_id = $res_coupon['data']['id'];
        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

        $userC = new MobileUserUC();

        //获取营销活动类型
        $marketing_activity_type = $_GET['marketing_activity_type'];
        //获取营销活动id
        $marketing_activity_id = $_GET['marketing_activity_id'];

        $coupon_res = $userC->getMerchantAndCouponInfo($coupon_id);
        if ($coupon_res['status'] == ERROR_NONE) {
            $coupon_model = $coupon_res['data'];

            //判断用户是否可以领取优惠券
            $jug_res = $userC->judgeUserCoupons($user_id, $coupon_id);
            $jug_result = json_decode($jug_res, true);
            if ($jug_result['status'] == ERROR_NONE) {
                //获取access_token
                $merchant = $userC->getMerchantWithId($encrypt_id);
                $access_token = WechatWebAuth::getTokenByMerchant($merchant);

                //随机获取一个未领取的自定义code码
                //$code = $couponSC->getCouponCode($coupon_id);

                //开启事务
                $transcation = Yii::app()->db->beginTransaction();
                //领取优惠券
                $receive_res = $userC->newReceiveCoupons($user_id, $coupon_id, Yii::app()->session[$encrypt_id . 'open_id'], $marketing_activity_type, $marketing_activity_id, '');
                $receive_result = json_decode($receive_res, true);

                if ($receive_result['status'] == ERROR_NONE) {
                    $transcation->commit();
                    //修改对应code的状态为已使用
                    //$couponSC->setCodeStatusByCode($code);

                    //微信库存-1
                    $cardCouponsC = new MobileCardCouponsC();
                    $cardCouponsC->cardModifystock($coupon_id, '', '', 1, $access_token);

                    $user_coupon_id = $receive_result['data'];
                    $this->redirect(array('couponResult',
                        'qcode' => $qcode,
                        'user_coupon_id' => $user_coupon_id
                    ));
                } else {
                    echo "<script>alert('领取失败');history.go(-1);</script>";
                }
            } else {
                echo "<script>alert('已到达领取上限');history.go(-1);</script>";
            }
        } else {
            echo "<script>alert('优惠券不存在');history.go(-1);</script>";
        }
    }

    /**
     * 领取优惠券结果页
     */
    public function actionCouponResult()
    {
        if (isset($_GET['qcode']) && !empty($_GET['qcode'])) {
            $qcode = $_GET['qcode'];
        }

        //通过code获取优惠券信息
        $couponSC = new MobileCouponsSC();
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);
        if ($res_coupon['status'] == ERROR_NONE && !empty($_GET['user_coupon_id'])) {
            $coupon = $res_coupon['data'];
            $merchant_id = $coupon['merchant_id'];
            $encrypt_id = $coupon['encrypt_id'];

            $userUC = new MobileUserUC();

            //获取商户信息
            $merchant = $userUC->getMerchantWithId($encrypt_id);

            //微信分享
            $wxlocation = new WxLocation();
            $resultWxlocation = $wxlocation->Wxshare($merchant);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;

            //获取在线商铺信息
            $res_onlineshop = json_decode($userUC->getOnlineshop($merchant_id), true);

            if ($res_onlineshop['status'] == ERROR_NONE) {
                $onlineshop = $res_onlineshop['data'];
            }

            if (!empty($coupon['use_time_interval'])) {
                $use_time = json_decode($coupon['use_time_interval'], true);
                $use_time_interval = '';
                foreach ($use_time as $v) {
                    switch ($v['type']) {
                        case 'MONDAY':
                            $v['type'] = '周一';
                            break;
                        case 'TUESDAY':
                            $v['type'] = '周二';
                            break;
                        case 'WEDNESDAY':
                            $v['type'] = '周三';
                            break;
                        case 'THURSDAY':
                            $v['type'] = '周四';
                            break;
                        case 'FRIDAY':
                            $v['type'] = '周五';
                            break;
                        case 'SATURDAY':
                            $v['type'] = '周六';
                            break;
                        case 'SUNDAY':
                            $v['type'] = '周日';
                            break;
                    }
                    $use_time_interval .= $v['type'] . '、';
                }

                //去除最后一个字符
                //$use_time_interval = mb_substr($use_time_interval, 0, -1);
                if (!empty($use_time[0]['begin_hour'])) {
                    $use_time_interval .= $use_time[0]['begin_hour'] . ':00至';
                } else {
                    $use_time_interval .= '00:00至';
                }
                if (!empty($use_time[0]['end_hour'])) {
                    $use_time_interval .= $use_time[0]['end_hour'] . ':00';
                } else {
                    $use_time_interval .= '24:00';
                }

                $coupon['use_time_interval'] = $use_time_interval;
            }

            //获取用户领取优惠券记录信息
            $user_coupon_id = $_GET['user_coupon_id'];
            $res_user_coupon = json_decode($couponSC->getUserCoupon($user_coupon_id), true);
            $user_coupon = $res_user_coupon['data'];

            //卡券
            $card_id = $coupon['card_id'];
            $card_res = $wxlocation->Wxcard($merchant, $card_id, '', '');
            $cardSign = json_decode($card_res, true);

            $cardExt = array(
                'outer_id' => $user_coupon_id,
                'timestamp' => $cardSign["timestamp"],
                'nonce_str' => $cardSign['nonceStr'],
                'signature' => $cardSign["signature"]
            );
            $cardExt = json_encode($cardExt);

            //是否显示公众号
            $is_qcode = false;

            if (Yii::app()->session['source'] == 'wechat') {
                if (!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) {
                    $is_qcode = true;
                }
            } elseif (Yii::app()->session['source'] == 'alipay') {
                if (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode)) {
                    $is_qcode = true;
                }
            } else {
                if ((!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) || (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode))) {
                    $is_qcode = true;
                }
            }

            $this->render('couponResult', array(
                'signPackage' => $signPackage,
                'onlineshop' => $onlineshop,
                'coupon' => $coupon,
                'user_coupon' => $user_coupon,
                'cardSign' => $cardSign,
                'cardExt' => $cardExt,
                'is_qcode' => $is_qcode
            ));
        }
    }

    /**
     * 立即使用
     */
    public function actionCouponDetail()
    {
        if (isset($_GET['qcode']) && !empty($_GET['qcode'])) {
            $qcode = $_GET['qcode'];
        }

        //通过code获取优惠券信息
        $couponSC = new MobileCouponsSC();
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);
        if ($res_coupon['status'] == ERROR_NONE) {
            $coupon = $res_coupon['data'];
            $merchant_id = $coupon['merchant_id'];

            $userUC = new UserUC();

            //获取商户信息
            $mer = json_decode($userUC->getWxapp($merchant_id));
            if ($mer->status == ERROR_NONE) {
                $wechat_appid = $mer->data->appid;
                $wechat_appsecret = $mer->data->appsecret;
            }

            //微信分享
            $wxlocation = new WxLocation();
            $resultWxlocation = $wxlocation->Wxshare($wechat_appid, $wechat_appsecret);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;

            //获取在线商铺信息
            $res_onlineshop = json_decode($userUC->getOnlineshop($merchant_id), true);

            if ($res_onlineshop['status'] == ERROR_NONE) {
                $onlineshop = $res_onlineshop['data'];
            }

            if (!empty($coupon['use_time_interval'])) {
                $use_time = json_decode($coupon['use_time_interval'], true);
                $use_time_interval = '';
                foreach ($use_time as $v) {
                    switch ($v['type']) {
                        case 'MONDAY':
                            $v['type'] = '周一';
                            break;
                        case 'TUESDAY':
                            $v['type'] = '周二';
                            break;
                        case 'WEDNESDAY':
                            $v['type'] = '周三';
                            break;
                        case 'THURSDAY':
                            $v['type'] = '周四';
                            break;
                        case 'FRIDAY':
                            $v['type'] = '周五';
                            break;
                        case 'SATURDAY':
                            $v['type'] = '周六';
                            break;
                        case 'SUNDAY':
                            $v['type'] = '周日';
                            break;
                    }
                    $use_time_interval .= $v['type'] . '、';
                }

                //去除最后一个字符
                $use_time_interval = mb_substr($use_time_interval, 0, -1);
                $use_time_interval .= $use_time[0]['begin_hour'] . ':00至' . $use_time[0]['end_hour'] . ':00';
                $coupon['use_time_interval'] = $use_time_interval;
            }

            if (!empty($_GET['user_coupon_id'])) {
                $user_coupon_id = $_GET['user_coupon_id'];
                $res_user_coupon = json_decode($couponSC->getCouponsDetail($user_coupon_id), true);
                $user_coupon = $res_user_coupon['data'];

                $this->render('couponDetail', array(
                    'signPackage' => $signPackage,
                    'onlineshop' => $onlineshop,
                    'coupon' => $coupon,
                    'user_coupon' => $user_coupon
                ));
            }
        }
    }

    //登录
    public function actionLogin()
    {
        if (isset($_POST)) {
            $encrypt_id = $_POST['encrypt_id'];
            $account = $_POST['mobile'];
            $pwd = $_POST['password'];

            $user = new MobileUserUC();
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
                    }
                }

                echo 'success';
            } else {
                echo 'fail';
            }
        }
    }

    //注册
    public function actionRegister()
    {
        if (isset($_POST)) {
            $encrypt_id = $_POST['encrypt_id'];
            $account = $_POST['mobile'];
            $msg_pwd = $_POST['msgpassword'];
            $pwd = $_POST['password'];

            $account = trim($account);
            if (empty($account)) {
                echo '手机号不能为空';
                exit();
            }

            if (!preg_match(PHONE_CHECK, $account)) {
                echo '手机号格式错误';
                exit();
            }

            $msg_pwd = trim($msg_pwd);
            if (empty($msg_pwd)) {
                echo '验证码不能为空';
                exit();
            }

            if (empty($pwd)) {
                echo '密码不能为空';
                exit();
            }

            $user = new MobileUserUC();
            $re = json_decode($user->getMerchant($encrypt_id));
            $merchant_id = $re->data->id;

            $is_res = $user->accountExist($merchant_id, $account);
            if ($is_res) {
                echo '该手机号已被注册';
                exit();
            } else {
                //新增一条记录
                $res = $user->userRegister($merchant_id, $account, $msg_pwd, $pwd, '');

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
                        }
                    }

                    //删除缓存中的短信验证码
                    $this->delMsgPwd($account);

                    echo 'success';
                } else {
                    echo '注册失败';
                }
            }
        }
    }

    /**
     * 门店列表
     */
    public function actionStoreList()
    {
        $qcode = $_GET['qcode'];

        if (isset($_POST['qcode']) && !empty($_POST['qcode'])) {
            $qcode = $_POST['qcode'];
        }

        $encrypt_id = $_GET['encrypt_id'];

        if (isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
            $encrypt_id = $_POST['encrypt_id'];
        }

        $couponSC = new MobileCouponsSC();
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);
        if ($res_coupon['status'] == ERROR_NONE) {
            if ($res_coupon['data']['store_limit_type'] == 1) { //全部门店
                $merchant_id = $res_coupon['data']['merchant_id'];
                $merchantC = new MobileMerchantC();
                $store_arr = $merchantC->getAllStore($merchant_id);
            } else { //部分门店
                $res_coupon['data']['store_limit'] = trim($res_coupon['data']['store_limit'], ',');
                $store_arr = explode(',', $res_coupon['data']['store_limit']);
            }

            $page_size = 5;

            //非ajax分页
            if (!isset($_POST['current_page'])) {
                $current_page = 1; //当前页
                $start_page = ($current_page - 1) * $page_size; //起始页
                $end_page = $current_page * $page_size - 1; //结束页

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if (!empty($store_arr[$i])) {
                        $res_store = json_decode($couponSC->getStoreInfo($store_arr[$i]), true);
                        if ($res_store['status'] == ERROR_NONE) {
                            $stores[] = $res_store['data'];
                        }
                    }
                }

                $this->render('storeList', array(
                    'stores' => $stores,
                    'qcode' => $qcode,
                    'encrypt_id' => $encrypt_id
                ));
            } else { //ajax分页
                $current_page = $_POST['current_page']; //当前页
                $start_page = ($current_page - 1) * $page_size; //起始页
                $end_page = $current_page * $page_size - 1; //结束页

                $stores = '';

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if (!empty($store_arr[$i])) {
                        $res_store = json_decode($couponSC->getStoreInfo($store_arr[$i]), true);
                        if ($res_store['status'] == ERROR_NONE) {
                            $stores[] = $res_store['data'];
                        }
                    }
                }
                $data['data']['lists'] = $stores;
                $data['data']['status'] = ERROR_NONE;

                echo json_encode($data);
            }
        }
    }

    /**
     * 门店详情
     */
    public function actionStoreDetail()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];

            $couponSC = new MobileCouponsSC();
            $res_store = json_decode($couponSC->getStoreInfo($id), true);

            if ($res_store['status'] == ERROR_NONE) {
                $store = $res_store['data'];

                $this->render('storeDetail', array(
                    'store' => $store
                ));
            }
        }
    }

    /**
     * 公众号
     */
    public function actionQRCode()
    {
        $encrypt_id = $_GET['encrypt_id'];

        $userUC = new MobileUserUC();
        $merchant = $userUC->getMerchantWithId($encrypt_id);

        $this->render('qRCode', array('merchant' => $merchant));
    }

    /**
     * 更新同步微信卡包状态，更新微信库存
     */
    public function actionSetIfWechat()
    {
        if (isset($_POST['user_coupon_id']) && !empty($_POST['user_coupon_id'])) {
            $user_coupon_id = $_POST['user_coupon_id'];
            $encrypt_id = $_POST['encrypt_id'];

            $couponSC = new MobileCouponsSC();
            $couponSC->setIfWechat($user_coupon_id);

            //查询优惠券信息
            $coupon = $couponSC->newCouponDetail($user_coupon_id);
            $coupon_id = $coupon->id;

            //查询access_token
            $userUC = new MobileUserUC();
            $merchant = $userUC->getMerchantWithId($encrypt_id);
            $access_token = WechatWebAuth::getTokenByMerchant($merchant);

            //微信库存+1
            $cardCouponsC = new MobileCardCouponsC();
            $cardCouponsC->cardModifystock($coupon_id, '', 1, '', $access_token);
        }
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
     * 批量领取优惠券
     */
    public function actionBatchReceiveCoupon()
    {
        $qcode = $_GET['qcode'];
        $encrypt_id = $_GET['encrypt_id'];

        $userUC = new MobileUserUC();
        $couponSC = new MobileCouponsSC();
        //通过code获取优惠券信息
        $res_coupon = json_decode($couponSC->getCouponInfoByCode($qcode), true);

        //获取商户信息
        $merchant = $userUC->getMerchantWithId($encrypt_id);

        //随机获取一个未领取的自定义code码
        $coupon_id = $res_coupon['data']['id'];
        $code = $couponSC->getCouponCode($coupon_id);

        //微信分享
        $wxlocation = new WxLocation();
        $resultWxlocation = $wxlocation->Wxshare($merchant);
        $result = json_decode($resultWxlocation, true);
        $signPackage = $result;

        //卡券
        $card_id = 'pK3d9wp-mslAqtxX5OP53bQIzknE';
        $open_id = Yii::app()->session[$encrypt_id . 'open_id'];

        $card_res = $wxlocation->Wxcard($merchant, $card_id, $code, $open_id);
        $cardSign = json_decode($card_res, true);

        $cardExt = array(
            'code' => $cardSign['code'],
            'openid' => $cardSign['openid'],
            'timestamp' => $cardSign["timestamp"],
            'nonce_str' => $cardSign['nonceStr'],
            'signature' => $cardSign["signature"]
        );

        $cardExt = json_encode($cardExt);

        $this->render('batchReceiveCoupon', array(
            'signPackage' => $signPackage,
            'cardExt' => $cardExt,
            'card_id' => $card_id
        ));
    }

}			
