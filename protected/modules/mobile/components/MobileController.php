<?php
include_once(realpath(dirname(__FILE__) . '/../../../../protected/config') . '/constant.php');
include_once(realpath(dirname(__FILE__) . '/../../../../protected/class/config') . '/constant.php');
include_once(realpath(dirname(__FILE__) . '/../../../../protected/class/config') . '/codeMsg.php');
include_once(realpath(dirname(__FILE__) . '/../../../../protected/extensions/wxpay/lib') . '/WxPay.Api.php');

include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/components/Component.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/Wechat.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/WechatWebAuth.php';

class MobileController extends Controller
{
    public $layout = 'main';
    public $menu = array();
    public $breadcrumbs = array();

    public function init()
    {
        //获取加密商户id
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }
        if (isset($_POST['encrypt_id']) && !empty($_POST['encrypt_id'])) {
            $encrypt_id = $_POST['encrypt_id'];
        }

        //优惠券模块
        if (isset($_GET['qcode']) && !empty($_GET['qcode'])) {
            $qcode = $_GET['qcode'];
            $coupon = new MobileCouponsSC();
            //通过code获取商户加密id
            $res = json_decode($coupon->getCouponInfoByCode($qcode), true);
            $encrypt_id = $res['data']['encrypt_id'];
        }

        $userUC = new MobileUserUC();
        //获取商户对象
        $merchant = Wechat::getMerchantByEncryptId($encrypt_id);
        $merchant_id = $merchant->id;

        //获取客户端
        $this->getUserClient();
        if (Yii::app()->session['source'] == 'wechat') { //微信
            //判断session是否存在对应加密商户id的open_id
            if (empty(Yii::app()->session[$encrypt_id . 'open_id'])) { //不存在
                //无感获取open_id
                if (isset($_GET['code']) && !empty($_GET['code'])) {
                    $code = $_GET['code'];
                } else {
                    WechatWebAuth::getUserCode($merchant, WAP_DOMAIN . Yii::app()->request->getUrl(), 'snsapi_base', '', $this);
                }
                //通过code获取open_id并存到session里
                $re_wechatUser = json_decode(WechatWebAuth::getUserAccessTokenJSON($merchant, $code));
                Yii::app()->session[$encrypt_id . 'open_id'] = $re_wechatUser->openid;
            }

            if (!empty(Yii::app()->session[$encrypt_id . 'open_id'])) {
                //查找open_id对应的已关注的微信粉丝
                $res_fans = json_decode($userUC->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                if ($res_fans['status'] == ERROR_NONE) {
                    //判断memcache里是否存在open_id，存在则为授权过
                    $a = Yii::app()->session[$encrypt_id . 'open_id'];
                    $b = Yii::app()->memcache->get($a);
                    if (empty($b)) { //没有授权过
                        if (empty($_GET['state']) || $_GET['state'] != 1) {
                            $flag = 1;
                            //有感用户授权
                            WechatWebAuth::getUserCode($merchant, WAP_DOMAIN . Yii::app()->request->getUrl(), 'snsapi_userinfo', $flag, $this);
                        }
                        if (isset($_GET['code']) && !empty($_GET['code'])) {
                            $code = $_GET['code'];
                        }
                        //通过code获取access_token
                        $res_wechatUser = json_decode(WechatWebAuth::getUserAccessTokenJSON($merchant, $code));
                        $access_token = $res_wechatUser->access_token;
                        //通过access_token和open_id获取用户信息
                        $userInfo = json_decode(WechatWebAuth::getUserInfoJSON($access_token, Yii::app()->session[$encrypt_id . 'open_id']), true);
                        $userInfo['headimgurl'] = substr($userInfo['headimgurl'], 0, -(strpos(strrev($userInfo['headimgurl']), "/") + 1)) . "/132";

                        $res_fans_data = $res_fans['data'];
                        //保存信息到粉丝
                        $wechat = new MobileWechatC();
                        $wechat->saveUserinfo($res_fans_data['id'], $userInfo, $res_fans_data);
                    }
                }

                //判断session是否存在对应加密商户id的user_id
                if (empty(Yii::app()->session[$encrypt_id . 'user_id'])) { //不存在
                    //判断session的open_id是否存在对应的会员
                    //判断免登
                    $result = json_decode($userUC->checkUserIfLogin(Yii::app()->session[$encrypt_id . 'open_id'], '', '', $merchant_id), true);
                    if ($result['status'] == ERROR_NONE) {
                        Yii::app()->session[$encrypt_id . 'user_id'] = $result['user_id'];

                        $merchant = $userUC->getMerchantWithId($encrypt_id);
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
                        $userUC->saveLoginInfo(Yii::app()->session[$encrypt_id . 'user_id'], $login_ip, $login_client);

                        //会员绑定
                        //查找open_id对应的粉丝
                        $res_fans = json_decode($userUC->getFansByOpenid($merchant_id, Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                        if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
                            //查找登录用户的open_id
                            $res_user = json_decode($userUC->getWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
                            if ($res_user['status'] == ERROR_NONE) {
                                $user_open_id = $res_user['data'];
                                //判断登录用户的open_id和session里的open_id是否一致
                                if (Yii::app()->session[$encrypt_id . 'open_id'] != $user_open_id) { //不一致
                                    //查找是否存在与该粉丝同样open_id的会员
                                    $res_user_same_open_id = json_decode($userUC->getUserByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                                    if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
                                        //解除会员微信粉丝绑定
                                        $userUC->clearUserWechatBind($res_user_same_open_id['data']['id']);
                                    }

                                    //判断登录用户是否绑定粉丝
                                    if ($user_open_id != '') {
                                        //查找会员绑定的粉丝
                                        $res_fans_same_open_id = json_decode($userUC->getNewFansByOpenid(Yii::app()->session[$encrypt_id . 'open_id'], ''), true);
                                        if ($res_fans_same_open_id['status'] == ERROR_NONE) {
                                            //解除粉丝绑定
                                            $userUC->clearFansBind($res_fans_same_open_id['data']['id']);
                                        }
                                    }

                                    //修改会员信息
                                    $userUC->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
                                }
                            }
                        } else { //无粉丝
                            //将当前的open_id保存到用户记录上
                            $userUC->setWechatOpenId(Yii::app()->session[$encrypt_id . 'user_id'], Yii::app()->session[$encrypt_id . 'open_id']);
                        }

                        //查询用户是否填写了必填项
                        $res_flag = $userUC->checkUserFillInfo(Yii::app()->session[$encrypt_id . 'user_id']);
                        //获取商户的必填项
                        $merchant = new MerchantC();
                        $res = json_decode($merchant->getFillInfo($merchant_id), true);

                        if ($res['status'] == ERROR_NONE && $res_flag) {
                            //完善信息
                            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fillInfo', array(
                                'encrypt_id' => $encrypt_id
                            )));
                        }
                    }
                }
            }
        } elseif (Yii::app()->session['source'] == 'alipay') { //支付宝
            //判断session是否存在对应加密商户id的open_id
            if (empty(Yii::app()->session[$encrypt_id . 'open_id'])) { //不存在
                //无感获取open_id
                if (isset($_GET['auth_code']) && !empty($_GET['auth_code'])) {
                    $auth_code = $_GET['auth_code'];
                } else {
                    $resirect_url = WAP_DOMAIN . Yii::app()->request->getUrl();
                    $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $merchant->appid . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode($resirect_url);
                    $this->redirect($url);
                }
                //通过code获取open_id并存到session里
                $alipay = new AliApi('AliApi');
                $aliuserinfo_token = $alipay->requestToken($auth_code, $merchant);
                Yii::app()->session[$encrypt_id . 'open_id'] = $aliuserinfo_token->alipay_system_oauth_token_response->user_id;
            }

            if (!empty(Yii::app()->session[$encrypt_id . 'open_id'])) {
                //查找open_id对应的已关注的支付宝粉丝
                $res_fans = json_decode($userUC->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
                if ($res_fans['status'] == ERROR_NONE) {
                    //判断memcache里是否存在open_id，存在则为授权过
                    $a = Yii::app()->session[$encrypt_id . 'open_id'];
                    $b = Yii::app()->memcache->get($a);
                    if (empty($b)) { //没有授权过
                        if (!isset($_GET['state']) || empty($_GET['state'])) {
                            //有感授权
                            $resirect_url = WAP_DOMAIN . Yii::app()->request->getUrl();
                            $flag = 1;
                            $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $merchant->appid . '&auth_skip=false&scope=auth_userinfo&state=' . $flag . '&redirect_uri=' . urlencode($resirect_url);
                            $this->redirect($url);
                        }

                        if (isset($_GET['auth_code']) && !empty($_GET['auth_code'])) {
                            $auth_code = $_GET['auth_code'];
                        }
                        //获取access_token
                        $alipay = new AliApi('AliApi');
                        $aliuserinfo_token = $alipay->requestToken($auth_code, $merchant);
                        $access_token = $aliuserinfo_token->alipay_system_oauth_token_response->access_token;
                        //获取用户信息
                        $userinfo_re = $alipay->getUserInfo($access_token, $merchant_id);

                        $res_fans_data = $res_fans['data'];
                        //保存信息到粉丝
                        $alifuwu = new MobileFuwuC();
                        $alifuwu->saveUserInfo($res_fans_data['id'], $userinfo_re, $res_fans_data);
                    }
                }

                //判断session是否存在对应加密商户id的user_id
                if (empty(Yii::app()->session[$encrypt_id . 'user_id'])) { //不存在
                    //判断session的open_id是否存在对应的会员
                    //判断免登
                    $result = json_decode($userUC->checkUserIfLogin('', Yii::app()->session[$encrypt_id . 'open_id'], '', $merchant_id), true);
                    if ($result['status'] == ERROR_NONE) {
                        Yii::app()->session[$encrypt_id . 'user_id'] = $result['user_id'];

                        $merchant = $userUC->getMerchantWithId($encrypt_id);
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
                        $userUC->saveLoginInfo(Yii::app()->session[$encrypt_id . 'user_id'], $login_ip, $login_client);

                        //会员绑定
                        //查找open_id对应的粉丝
                        $res_fans = json_decode($userUC->getFansByOpenid($merchant_id, '', Yii::app()->session[$encrypt_id . 'open_id']), true);
                        if ($res_fans['status'] == ERROR_NONE) { //存在粉丝
                            //查找登录用户的open_id
                            $res_user = json_decode($userUC->getAlipayOpenId(Yii::app()->session[$encrypt_id . 'user_id']), true);
                            if ($res_user['status'] == ERROR_NONE) {
                                $user_open_id = $res_user['data'];
                                //判断登录用户的open_id和session里的open_id是否一致
                                if (Yii::app()->session[$encrypt_id . 'open_id'] != $user_open_id) { //不一致
                                    //查找是否存在与该粉丝同样open_id的会员
                                    $res_user_same_open_id = json_decode($userUC->getUserByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
                                    if ($res_user_same_open_id['status'] == ERROR_NONE) { //存在会员
                                        //解除会员支付宝粉丝绑定
                                        $userUC->clearUserAlipayBind($res_user_same_open_id['data']['id']);
                                    }

                                    //判断登录用户是否绑定粉丝
                                    if ($user_open_id != '') {
                                        //查找会员绑定的粉丝
                                        $res_fans_same_open_id = json_decode($userUC->getNewFansByOpenid('', Yii::app()->session[$encrypt_id . 'open_id']), true);
                                        if ($res_fans_same_open_id['status'] == ERROR_NONE) {
                                            //解除粉丝绑定
                                            $userUC->clearFansBind($res_fans_same_open_id['data']['id']);
                                        }
                                    }

                                    //修改会员信息
                                    $userUC->saveMemberInfo(Yii::app()->session[$encrypt_id . 'user_id'], $res_fans['data']);
                                }
                            }
                        } else { //无粉丝
                            //将当前的open_id保存到用户记录上
                            $userUC->setAliOpenId(Yii::app()->session[$encrypt_id . 'user_id'], Yii::app()->session[$encrypt_id . 'open_id']);
                        }

                        //查询用户是否填写了必填项
                        $res_flag = $userUC->checkUserFillInfo(Yii::app()->session[$encrypt_id . 'user_id']);
                        //获取商户的必填项
                        $merchant = new MerchantC();
                        $res = json_decode($merchant->getFillInfo($merchant_id), true);

                        if ($res['status'] == ERROR_NONE && $res_flag) {
                            //完善信息
                            $this->redirect(Yii::app()->createUrl('mobile/uCenter/user/fillInfo', array(
                                'encrypt_id' => $encrypt_id
                            )));
                        }
                    }
                }
            }
        } else { //web
            //判断session是否存在对应加密商户id的user_id
        }
    }

    /** 获取用户客户端类型，保存session
     * @return string
     */
    public function getUserClient()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            Yii::app()->session['source'] = 'wechat';
            //return 'wechat';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
            Yii::app()->session['source'] = 'alipay';
            //return 'alipay';
        } else {
            Yii::app()->session['source'] = 'web';
            //return 'web';
        }
    }

    /** 判断用户是否登录
     * @param $encrypt_id
     * @param string $goUrl
     */
    public function checkLogin($encrypt_id, $goUrl = '')
    {
        //判断session里是否存在用户id
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/auth/login', array(
                'encrypt_id' => $encrypt_id,
                'goUrl' => $goUrl
            )));
        }
    }
}