<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/components/Component.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/Wechat.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/WechatWebAuth.php';


/**
 * 会员中心
 *
 */
class UserController extends UCenterController
{
    public function checkLogin($encrypt_id)
    {
        $source = $this->getUserClient();
        $userUC = new UserUC();

        if (isset(Yii::app()->session['user_id']) && !empty(Yii::app()->session['user_id'])) {
            $merchant = Wechat::getMerchantByEncryptId($encrypt_id);
            $merchant_id = $merchant->id;

            //验证用户id和商户id
            if (!$userUC->checkUserId(Yii::app()->session['user_id'], $merchant_id)) {
                unset(Yii::app()->session['user_id']);
                $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            }
        }
        
        if ($source == 'wechat') {
            if (isset(Yii::app()->session['wechat_open_id']) && !empty(Yii::app()->session['wechat_open_id'])) {
                //免登规则，用户必须是该商户的用户且手机号注册成为会员
                $result = json_decode($userUC->checkUserIfLogin(Yii::app()->session['wechat_open_id'], '', $encrypt_id, ''));
            } else {
                $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            }
        } elseif ($source == 'alipay') {
            if (isset(Yii::app()->session['alipay_open_id']) && !empty(Yii::app()->session['alipay_open_id'])) {
                
                //免登规则，用户必须是该商户的用户且手机号注册成为会员
                $result = json_decode($userUC->checkUserIfLogin('', Yii::app()->session['alipay_open_id'], $encrypt_id, ''));
            } else {
                $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            }
        }

        if (!empty($result)) {
            if ($result->status == ERROR_NONE) {
                Yii::app()->session['user_id'] = $result->user_id;
            }
        }
    }

    /**
     * 获取商户信息
     */
    public function getMerchant($encrypt_id)
    {
        $user = new UserUC();
        //获取商户信息
        $merchant_res = $user->getMerchant($encrypt_id, '');
        $merchant_result = json_decode($merchant_res, true);
        if ($merchant_result['status'] == ERROR_NONE) {
            if (isset($merchant_result['data'])) {
                $merchant = $merchant_result['data'];
            } else {
                $this->redirect('error');
            }
        } else {
            $this->redirect('error');
        }
        return $merchant;
    }

    /**
     * 获取 服务窗 OpenId
     */
    public function getAliOpenId($appid)
    {
        //获取auth_code
        if (!empty($_GET['auth_code'])) {
            $auth_code = $_GET['auth_code'];
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

// 			Yii::app()->session['wechat_open_id'] = $open_id;
        }
    }

    /**
     * 免登操作-服务窗
     */
    public function checkAliOpenId($merchant_id)
    {
        $user = new UserUC();

        $ali_open_id = Yii::app()->session['ali_open_id'];

        $res = $user->checkAliOpenId($merchant_id, $ali_open_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                Yii::app()->session['user_id'] = $result['data'];
            }
        }
    }

    /**
     * 免登操作-公众号
     */
    public function checkWechatOpenId($merchant_id)
    {
        $user = new UserUC();

        $wechat_open_id = Yii::app()->session['wechat_open_id'];

        $res = $user->checkWechatOpenId($merchant_id, $wechat_open_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                Yii::app()->session['user_id'] = $result['data'];
            }
        }
    }

    /**
     * 会员中心
     */
    public function actionMemberCenter()
    {
        //获取加密商户id
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        //判断是否已登录
        $this->checkLogin($encrypt_id);

        $userUC = new UserUC();
        $merchant = Wechat::getMerchantByEncryptId($encrypt_id);

        /*$source = $this->getUserClient();
        if ($source == 'wechat') { //微信端
            //如果session里面没有微信用户openid，则获取微信用户openid，并且操作免登陆
            if (!isset(Yii::app()->session['wechat_open_id']) || empty(Yii::app()->session['wechat_open_id'])) {
                if (isset($_GET['code']) && !empty($_GET['code'])) {
                    $code = $_GET['code'];
                } else {
                    WechatWebAuth::getUserCode($merchant, Yii::app()->createUrl('uCenter/user/MemberCenter', array('encrypt_id' => $encrypt_id)), 'snsapi_base', '', $this);
                }
                $re_wechatUser = json_decode(WechatWebAuth::getUserAccessTokenJSON($merchant, $code));
                Yii::app()->session['wechat_open_id'] = $re_wechatUser->openid;
            } else {
                //如果session存在wechat_open_id(微信用户openid)则判断是否可以免登，
                //免登规则，用户必须是该商户的用户且手机号注册成为会员
                $result = json_decode($userUC->checkUserIfLogin(Yii::app()->session['wechat_open_id'], '', $encrypt_id, ''));
                if ($result->status == ERROR_NONE) {
                    Yii::app()->session['user_id'] = $result->user_id;
                }
            }
        } elseif ($source == 'alipay') { //支付宝端

        }*/

        $merchant_id = $merchant->id;

        $user_id = Yii::app()->session['user_id'];
        $res = $userUC->getPersonalInformation($user_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $res_data = $result['data'];
                if (!isset($result['data']['nickname']) || empty($result['data']['nickname'])) {
                    $result['data']['nickname'] = substr($result['data']['account'], 0, 4) . '****' . substr($result['data']['account'], 7, 4);;
                }
                $data = $result['data'];
                $u_id = $data['id'];
            }
        }

        if (isset(Yii::app()->session['wechat_open_id']) && !empty(Yii::app()->session['wechat_open_id'])) {
            if (empty($data['avatar']) || $this->usefulUrl($data['avatar'])) {
                $wechat = new WechatC();
                $get_access_res = Wechat::getTokenByMerchant($merchant);
                if (isset($get_access_res)) {
                    $open_id = Yii::app()->session['wechat_open_id'];
                    $access_token = $get_access_res;
                    //获取用户基本信息
                    $get_info_res = json_decode(WechatWebAuth::getUserInfoJSON($access_token, $open_id), true);
                    $get_info_res['headimgurl'] = substr($get_info_res['headimgurl'], 0, -(strpos(strrev($get_info_res['headimgurl']), "/") + 1)) . "/132";
                    $wechat->saveUserinfo($u_id, $get_info_res, $res_data);
                    $data['avatar'] = $get_info_res['headimgurl'];
                    $data['nickname'] = $get_info_res['nickname'];
                }
            }
        } elseif (isset(Yii::app()->session['alipay_open_id']) && !empty(Yii::app()->session['alipay_open_id'])) {
            //服务窗
            if (empty($data['alipay_avatar']) || $this->usefulUrl($data['alipay_avatar'])) {
                $aliuserinfo = new AliApi('AliApi');
                //获取auth_code
                if (!empty($_GET['auth_code'])) {
                    $auth_code = $_GET['auth_code'];
                    //获取用户信息
                    $userinfo_re = $aliuserinfo->getUserInfo($auth_code);
                    //保存用户信息
                    $alifuwu = new FuwuC();
                    $alifuwu->saveUserInfo($u_id, $userinfo_re, $res_data);
                    $data['avatar'] = $userinfo_re->avatar;
                } else {
                    $data['avatar'] = $data['alipay_avatar'];
                }
            } else {
                $data['avatar'] = $data['alipay_avatar'];
            }
        }

        $this->render('memberCenter', array(
            'data' => $data,
            'encrypt_id' => $encrypt_id,
            'merchant_id' => $merchant_id
        ));
    }

    /*
     * 判断url是否有效
     */
    public function usefulUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $rtn = curl_exec($ch);
        curl_exec($ch);

        if (strpos($rtn, '404 Not Found') == true) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 验证是否登录
     */
    public function checkLogin1()
    {
        if (!isset(Yii::app()->session['user_id']) || empty(Yii::app()->session['user_id'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 会员注册
     */
    public function actionRegister()
    {
        $user = new UserUC();
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        $merchant_model = $user->getMerchantWithId($encrypt_id);
        //授权模式
        if ($merchant_model['wechat_thirdparty_authorizer_if_auth'] == 2) {
            $merchant_model['wechat_subscription_appid'] = $merchant_model['wechat_thirdparty_authorizer_appid'];
        }
        if (!empty($_GET['encrypt_id'])) {
            //Yii::app()->session['encrypt_id'] = $_GET['encrypt_id'];
            //Yii::app()->session['source'] = $_GET['source'];
            //微信
            Yii::app()->session['wechat_code'] = $_GET['code'];
            Yii::app()->session['wechat_state'] = $_GET['state'];
            //服务窗
            Yii::app()->session['ali_app_id'] = $_GET['app_id'];
            Yii::app()->session['ali_auth_code'] = $_GET['auth_code'];
        }

        $source = $this->getUserClient();

        if ($source == 'wechat' && !isset(Yii::app()->session['wechat_code'])) {
            //微信
            if (isset($_GET['goUrl']) && !empty($_GET['goUrl'])) {
                $goU = $_GET['goUrl'];
                //$redirect_uri = USER_DOMAIN . '/register?encrypt_id=' . $merchant_model['encrypt_id'] . '&source=wechat' . "&goUrl=" . urlencode($goU);
                $redirect_uri = USER_DOMAIN . '/register?encrypt_id=' . $merchant_model['encrypt_id'] . "&goUrl=" . urlencode($goU);
            } else {
                $redirect_uri = USER_DOMAIN . '/register?encrypt_id=' . $merchant_model['encrypt_id'];
            }
            if ($merchant_model['wechat_thirdparty_authorizer_if_auth'] == 2) {
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $merchant_model['wechat_thirdparty_authorizer_appid'] . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=wechat&component_appid=' . Component::getAppId() . '#wechat_redirect';
            } else {
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $merchant_model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
            }
            $this->redirect($url);
        } elseif ($source == 'alipay' && !isset(Yii::app()->session['ali_app_id'])) {
            //支付宝服务窗
            if (isset($_GET['goUrl']) && !empty($_GET['goUrl'])) {
                $goU = $_GET['goUrl'];
                $resirect_url = USER_DOMAIN . '/register?encrypt_id=' . $merchant_model['encrypt_id'] . "&goUrl=" . urlencode($goU);
            } else {
                $resirect_url = USER_DOMAIN . '/register?encrypt_id=' . $merchant_model['encrypt_id'];
            }
            $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $merchant_model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode($resirect_url);
            $this->redirect($url);
        }

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();

            $encrypt_id = $_POST['encrypt_id'];
            $re = json_decode($user->getMerchant($encrypt_id));
            $merchant_id = $re->data->id;

            $account = $post['MobilePhone'];
            $msg_pwd = $post['MsgPassword'];
            $pwd = $post['Password'];
            $confirm_pwd = $post['Confirm'];
            $res = $user->userRegister($merchant_id, $account, $msg_pwd, $pwd, $confirm_pwd);
            $result = json_decode($res, true);

            if ($result['status'] == ERROR_NONE) {
                $user_id = $result['data'];
                //注册操作（设置会员等级 之类）
                //获取公众号或者服务窗会员信息
                $this->getUserInfoOperate($merchant_id, $user_id);

                //登陆操作（保存会员session，保存购物车内容）
                $this->loginOperate($user_id);

                //删除缓存中的短信验证码
                $this->delMsgPwd($account);

                if (isset($_POST['goUrl']) && !empty($_POST['goUrl'])) {
                    $url = $_POST['goUrl'];
                    $this->redirect($url, array('encrypt_id' => $encrypt_id));
                } else {
                    $this->redirect(array('memberCenter', 'encrypt_id' => $encrypt_id));
                }
            } else {
                $status = $result['status'];
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        }

        $goUrl = isset($_GET['goUrl']) && !empty($_GET['goUrl']) ? $_GET['goUrl'] : '';
        $this->render('register', array(
            'goUrl' => $goUrl,
            'encrypt_id' => $encrypt_id
        ));
    }


    /**
     * 获取公众号或者服务窗会员信息
     */
    public function getUserInfoOperate($merchant_id, $user_id)
    {
        $user = new UserUC();

        $source = $this->getUserClient();
        $merchant_model = $user->getMerchantWithId($merchant_id);

        $res = $user->getPersonalInformation($user_id, $merchant_id);
        if ($res['status'] == ERROR_NONE) {
            if (isset($res['data'])) {
                $res_data = $res['data'];
            }
        }

        if ("alipay_wallet" == $source) {
            $aliuserinfo = new AliApi('AliApi');
            $alifuwu = new FuwuC();
            //获取auth_code
            if (!empty($_GET['auth_code'])) {
                $auth_code = $_GET['auth_code'];
                //获取用户信息
                $userinfo_re = $aliuserinfo->getUserInfo($auth_code);
                //保存用户信息
                $alifuwu->saveUserInfo($user_id, $userinfo_re, $res_data);
            }
        } elseif ("wechat" == $source) {
            $wechat = new WechatC();
            $get_access_res = Wechat::getTokenByMerchant($merchant_model);
            if (isset($get_access_res)) {
                $open_id = Yii::app()->session['wechat_open_id'];
                $access_token = $get_access_res;
                //获取用户基本信息
                $get_info_res = json_decode(WechatWebAuth::getUserInfoJSON($access_token, $open_id), true);
                $get_info_res['headimgurl'] = substr($get_info_res['headimgurl'], 0, -(strpos(strrev($get_info_res['headimgurl']), "/") + 1)) . "/132";
                $wechat->saveUserinfo($user_id, $get_info_res, $res_data);
            }
        }
    }

    /**
     * 会员登录操作（保存session/购物车）
     */
    public function loginOperate($user_data)
    {
        //$user = new UserUC();
        Yii::app()->session['user_id'] = $user_data['id'];
        $source = $this->getUserClient();

        //将OpenId保存到数据库
        if ($source == 'wechat') {
            Yii::app()->session['wechat_open_id'] = $user_data['wechat_open_id'];
            //$user->setWechatOpenId($user_id, $wechat_open_id);
        } elseif ($source == 'alipay') {
            Yii::app()->session['alipay_open_id'] = $user_data['alipay_open_id'];
            //$user->setWechatOpenId($user_id, $alipay_open_id);
        }
    }

    /**
     * 找回密码-验证手机号
     */
    public function actionFindPwdOfCheck()
    {
        $data = array();
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
                $this->redirect(Yii::app()->createUrl('uCenter/user/setNewPassword', array('account' => $account, 'encrypt_id' => $encrypt_id)));
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

            //TODO
            $merchat = new MerchantC();
            $result = json_decode($merchat->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $result->data->id;

            $new_pwd = $post['NewPassword'];
            $con_pwd = $post['ConfirmPassword'];

            $res = $user->setNewPassword($merchant_id, $account, $new_pwd, $con_pwd);
            $result = json_decode($res, true);

            if ($result['status'] == ERROR_NONE) {
                $user_id = $result['data'];
                //登陆操作（保存会员session，保存购物车内容）
                $this->loginOperate($user_id);
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
        //TODO
        $encrypt_id = $_POST['encrypt_id'];
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;

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
            //TODO
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
     * 会员登录
     */
    public function actionLogin()
    {
        if (isset($_GET['encrypt_id']) && $_GET['encrypt_id']) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        if (isset(Yii::app()->session['user_id']) && !empty(Yii::app()->session['user_id'])) {
            $userUC = new UserUC();
            $merchant = Wechat::getMerchantByEncryptId($encrypt_id);
            $merchant_id = $merchant->id;

            //验证用户id和商户id
            if (!$userUC->checkUserId(Yii::app()->session['user_id'], $merchant_id)) {
                unset(Yii::app()->session['user_id']);
                $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            }

            if (!empty($_GET['goUrl'])) {
                $this->redirect($_GET['goUrl'], array('encrypt_id' => $encrypt_id));
            } else {
                $this->redirect(array('memberCenter', 'encrypt_id' => $encrypt_id));
            }
        }

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();
            $encrypt_id = $_POST['encrypt_id'];

            $account = $post['MobilePhone'];
            $pwd = $post['Password'];

            $res = $user->checkAccount($encrypt_id, $account, $pwd);
            $result = json_decode($res, true);

            if ($result['status'] == ERROR_NONE) {
                $user_data = $result['data'];
                //登陆操作（保存会员session，保存购物车内容）
                $this->loginOperate($user_data);
                //判断跳转地址是否为空,跳转到点击进登陆页面的页面
                if (isset($_POST['goUrl']) && !empty($_POST['goUrl'])) {
                    $this->redirect($_POST['goUrl'], array('encrypt_id' => $encrypt_id));
                } else {
                    $this->redirect(array('memberCenter', 'encrypt_id' => $encrypt_id));
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

    /**
     * 优惠券列表
     */
    public function actionCoupons()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;

        $list = array();
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

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

        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $list = array();
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();

            //TODO
            $result = json_decode($user->getMerchant($encrypt_id));
            $merchant_id = $result->data->id;
            $user_id = Yii::app()->session['user_id'];

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

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
                    $this->redirect('personalInformationDetail', array(
                        'encrypt_id' => $encrypt_id
                    ));
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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        if (!empty($_POST['User']) && isset($_POST['User'])) {
            $post = $_POST['User'];
            $user = new UserUC();
            $encrypt_id = $_POST['encrypt_id'];

            //TODO
            $user_id = Yii::app()->session['user_id'];

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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $card_data = array();
        $user = new UserUC();

        $res = json_decode($user->getMerchant($encrypt_id));
        $merchant_id = $res->data->id;
        $user_id = Yii::app()->session['user_id'];

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
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $point = array();
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

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
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id']) && isset($_GET['source'])) {
            $source = $this->getUserClient();
            //微信参数
            $code = $_GET['code'];

            //获取商户信息
            $merchant = $this->getMerchant($encrypt_id);
            //保存merchant_id source
            $merchant_id = $merchant['id'];

            $appid = $merchant['appid'];
            $wechat_appid = $merchant['wechat_appid'];
            $wechat_appsecret = $merchant['wechat_appsecret'];
            //获取并保存OpenId
            if ("alipay_wallet" == $source) {
                $this->getAliOpenId($appid);
            } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
                $this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret);
            }
        }
        //判断是否登陆过免登操作
        if ("alipay_wallet" == $source) {
            $this->checkAliOpenId($merchant_id);
        } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
            $this->checkWechatOpenId($merchant_id);
        }

        $list = array();
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

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
        $data = array('error' => 'failure');
        try {
            $user = new UserUC();

            $user_id = Yii::app()->session['user_id'];

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
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $order = array();
        $user = new UserUC();

        $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

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
     * 我的预定
     */
    public function actionBookList()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $book = array();
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

        $res = $user->getBookList($user_id);
        $result = json_decode($res, true);
        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $book = $result['data'];
            }
        }

        $this->render('bookList', array(
            'book' => $book,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 预定操作
     */
    public function actionBookOperate()
    {
        $user = new UserUC();
        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            $source = $this->getUserClient();
            //微信参数
            $code = $_GET['code'];

            //获取商户信息
            $merchant = $this->getMerchant($encrypt_id);
            //保存merchant_id source
            $merchant_id = $merchant['id'];

            $appid = $merchant['appid'];
            $wechat_appid = $merchant['wechat_appid'];
            $wechat_appsecret = $merchant['wechat_appsecret'];
            //获取并保存OpenId
            if ("alipay_wallet" == $source) {
                $this->getAliOpenId($appid);
            } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
                $this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret);
            }
        }
        //判断是否登陆过免登操作
        if ("alipay_wallet" == $source) {
            $this->checkAliOpenId();
        } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
            $this->checkWechatOpenId();
        }

        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        //TODO
        $user_id = Yii::app()->session['user_id'];

        //初始化页面
        //人数
        $arr_people_num = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30);
        //日期
        $now_year = date('Y');
        $now_month = date('m');
        $arr_month = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $now_day = date('d');
        $arr_day = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31);
        $arr_time = array("08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30");
        //门店信息
        $get_store_id = isset($_GET['store_id']) ? $_GET['store_id'] : null;

        $store = array();
        $res = $user->getOnlineStore($merchant_id, $get_store_id);
        $result = json_decode($res, true);
        if ($result['status'] == ERROR_NONE) {
            if (!empty($result['data'])) {
                $store = $result['data'];
            }
        }

        if (isset($_POST['Book']) && !empty($_POST['Book'])) {
            $post = $_POST['Book'];
            $store_id = $post['store'];
            $book_name = $post['family_name'];
            $phone_num = $post['phone_num'];
            $people_num = $post['people_num'];
            $time = $now_year . '-' . $post['month'] . '-' . $post['day'] . ' ' . $post['time'] . ':00';
            $sex = $post['sex'];
            $remark = $post['remark'];
            $encrypt_id = $_POST['encrypt_id'];

            $res = $user->bookOperate($user_id, $store_id, $book_name, $people_num, $time, $phone_num, $sex, $remark);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $record_id = $result['data'];

                    $this->redirect(Yii::app()->createUrl('uCenter/user/bookWait', array('record_id' => $record_id, 'encrypt_id' => $encrypt_id)));
                }
            }
        }

        $this->render('bookOperate', array('store' => $store, 'people_num' => $arr_people_num, 'month_list' => $arr_month, 'day_list' => $arr_day, 'time_list' => $arr_time, 'year' => $now_year, 'month' => $now_month, 'day' => $now_day, 'store_id' => $get_store_id, 'encrypt_id' => $encrypt_id));
    }

    /**
     * 等待预定界面
     */
    public function actionBookWait()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $user = new UserUC();
        if (isset($_GET['record_id'])) {
            $record_id = $_GET['record_id'];

            $res = $user->getBookDetail($record_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $book = $result['data'];
                }
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        } else {
            Yii::app()->user->setFlash('error', '读取失败');
        }
        $this->render('bookWait', array('book' => $book, 'encrypt_id' => $encrypt_id));
    }

    /**
     * 预定详情
     */
    public function actionBookDetail()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $user = new UserUC();

        if (isset($_GET['record_id'])) {
            $record_id = $_GET['record_id'];

            $res = $user->getBookDetail($record_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $book = $result['data'];
                }
            } else {
                $msg = $result['errMsg'];
                Yii::app()->user->setFlash('error', $msg);
            }
        } else {
            Yii::app()->user->setFlash('error', '读取失败');
        }
        $this->render('bookDetail', array('book' => $book, 'encrypt_id' => $encrypt_id));
    }

    /**
     * 取消预订
     */
    public function actionBookCancel()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $user = new UserUC();
        if (isset($_GET['record_id'])) {
            $record_id = $_GET['record_id'];

            $res = $user->bookCancel($record_id);
            $result = json_decode($res, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $id = $result['data'];
                    $this->redirect(Yii::app()->createUrl('uCenter/user/bookDetail', array('record_id' => $id, 'encrypt_id' => $encrypt_id)));
                }
            }
        } else {
            Yii::app()->user->setFlash('error', '读取失败');
        }
    }

    /**
     * 领取红包/优惠券
     */
    public function actionLookCoupons()
    {
        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            $source = $this->getUserClient();
            //微信参数
            $code = $_GET['code'];

            //获取商户信息
            $merchant = $this->getMerchant($encrypt_id);
            //保存merchant_id source
            //Yii::app()->session['merchant_id'] = $merchant['id'];
            //Yii::app()->session['source'] = $source;

            $appid = $merchant['appid'];
            $wechat_appid = $merchant['wechat_appid'];
            $wechat_appsecret = $merchant['wechat_appsecret'];
            //获取并保存OpenId
            if ("alipay_wallet" == $source) {
                $this->getAliOpenId($appid);
            } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
                $this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret);
            }
        }
        //判断是否登陆过免登操作
        if ("alipay_wallet" == $source) {
            $this->checkAliOpenId();
        } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
            $this->checkWechatOpenId();
        }

        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $user = new UserUC();
        $coupons_id = isset($_GET['coupons_id']) ? $_GET['coupons_id'] : '';
        $coupons_type = isset($_GET['coupons_type']) ? $_GET['coupons_type'] : '';
        $user_id = Yii::app()->session['user_id'];

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
            $this->redirect(Yii::app()->createUrl('uCenter/user/receiveCouponsFail', array(
                'msg' => $msg,
                'coupons_type' => $coupons_type,
                'encrypt_id' => $encrypt_id
            )));
        }

        $this->render('lookCoupons', array('coupons' => $coupons, 'encrypt_id' => $encrypt_id));
    }

    /**
     * 处理使用说明
     */
    public function actionInstructions()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }
        $coupons = isset($_GET['coupons']) ? $_GET['coupons'] : '';

        $this->render('instructions', array('coupons' => $coupons, 'encrypt_id' => $encrypt_id));
    }

    /**
     * 领取优惠券
     */
    public function actionReceiveCoupons()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        if (isset($_GET['coupons_id']) && !empty($_GET['coupons_id']) && isset($_GET['coupons_type']) && !empty($_GET['coupons_type'])) {
            $coupons_id = $_GET['coupons_id'];
            $coupons_type = $_GET['coupons_type'];
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

                    $this->redirect(Yii::app()->createUrl('uCenter/user/couponsDetail', array(
                        'coupons_id' => $id,
                        'action' => "receive",
                        'encrypt_id' => $encrypt_id
                    )));
                } else {
                    $msg = $receive_result['errMsg'];
                    $this->redirect(Yii::app()->createUrl('uCenter/user/receiveCouponsFail', array(
                        'msg' => $msg,
                        'coupons_type' => $coupons_type,
                        'encrypt_id' => $encrypt_id
                    )));
                }

            } else {
                $msg = $jug_result['errMsg'];

                $this->redirect(Yii::app()->createUrl('uCenter/user/receiveCouponsFail', array(
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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $user = new UserUC();
        $coupons_id = isset($_GET['coupons_id']) ? $_GET['coupons_id'] : '';
        $user_id = Yii::app()->session['user_id'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        $res = $user->couponsDetail($coupons_id, $user_id);
        $result = json_decode($res, true);
        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                $coupons = $result['data'];
            }
        } else {
            $msg = $result['errMsg'];
            Yii::app()->user->setFlash('error', $msg);
        }

        $this->render('couponsDetail', array(
            'coupons' => $coupons,
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
        $coupons_type = isset($_GET['coupons_type']) ? $_GET['coupons_type'] : '';
        $encrypt_id = $_GET['encrypt_id'];

        $this->render('receiveCouponsFail', array(
            'msg' => $msg,
            'coupons_type' => $coupons_type,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 线上店铺
     */
    public function actionShop()
    {
        $user = new UserUC();
        //获取参数 商户id,来源
        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            $source = $this->getUserClient();
            //微信参数
            $code = $_GET['code'];
            //获取商户信息
            $merchant = $this->getMerchant($encrypt_id);
            //保存merchant_id source
            $merchant_id = $merchant['id'];

            $appid = $merchant['appid'];
            $wechat_appid = $merchant['wechat_subscription_appid'];
            $wechat_appsecret = $merchant['wechat_subscription_appsecret'];
            //获取并保存OpenId
            if ("alipay_wallet" == $source) {
                $this->getAliOpenId($appid);
            } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
                $this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret);
            }
        }

        //判断是否登陆过免登操作
//         if ("alipay_wallet" == $source) {
//             $this->checkAliOpenId();
//         } elseif ("wechat" == $source && $merchant['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
//             $this->checkWechatOpenId();
//         }

        //商城基本信息
        $shop_res = $user->getShop($merchant_id);
        $shop_result = json_decode($shop_res, true);
        if ($shop_result['status'] == ERROR_NONE) {
            if (isset($shop_result['data'])) {
                $shop = $shop_result['data'];
            }
        }

        if (empty($shop)) {
            Yii::app()->user->setFlash('error', '商铺建设中。。。');
        }

        //门店信息
        $store_res = $user->getStore($merchant_id);
        $store_result = json_decode($store_res, true);
        if ($store_result['status'] == ERROR_NONE) {
            if (isset($store_result['data'])) {
                $store = $store_result['data'];
                if (isset($_GET['store_id']) && $_GET['store_id']) {
                    $store_id = $_GET['store_id'];
                } else {
                    $store_id = $store_result['store_id'];
                }
            }
        }

        //预订信息
        $onlineShop = json_decode($user->getOnline($merchant_id), true);
        if ($onlineShop['status'] == ERROR_NONE) {
            $online = $onlineShop['data'];
        }
        //优惠券信息
        //修改 已失效的优惠券
        $user->changeMerchantCouponsStatus($merchant_id);
        $coupons_res = $user->getCoupons($merchant_id, $store_id);
        $coupons_result = json_decode($coupons_res, true);
        if ($coupons_result['status'] == ERROR_NONE) {
            if (isset($coupons_result['data'])) {
                $coupons = $coupons_result['data'];
            }
        }

        //储值活动信息
        $stored_res = $user->getStored($merchant_id);
        $stored_result = json_decode($stored_res, true);
        if ($stored_result['status'] == ERROR_NONE) {
            if (isset($stored_result['data'])) {
                $stored = $stored_result['data'];
            }
        }

        //商户相册信息
        $album_res = json_decode($user->getAlbumNum($merchant_id));
        if ($album_res->status == ERROR_NONE) {
            $album_num = $album_res->data;
        }

        $wxlocation = new WxLocation();
        $resultWxlocation = $wxlocation->getWxLocation($store[$store_id]['lat'], $store[$store_id]['lng'],$merchant_id);
        $result = json_decode($resultWxlocation, true);
        $location = $result['location'];
        $signPackage = $result['signPackage'];

        $this->render('shop', array(
            'shop' => $shop,
            'store' => $store,
            'coupons' => $coupons,
            'stored' => $stored,
            'store_id' => $store_id,
            'album_num' => $album_num,
            'online' => $online,
            'location' => $location,
            'signPackage' => $signPackage,
            'encrypt_id' => $encrypt_id,
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
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }

        $free_secret = '';   //初始化free_secret
        $user = new UserUC();

        $user_id = Yii::app()->session['user_id'];

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
                $this->redirect(array('personalInformation', 'encrypt_id' => $encrypt_id));
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
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否已登录
        if (!$this->checkLogin()) {
            $this->redirect(array('login', 'encrypt_id' => $encrypt_id));
            return;
        }
        $user = new UserUC();
        $res = json_decode($user->getMerchant($encrypt_id));
        $merchant_id = $res['data']['id'];
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
            'stored_id' => $stored_id,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 列表页面（储值活动，红包，优惠券）
     */
    public function actionList()
    {
        $title = isset($_GET['title']) ? $_GET['title'] : '';
        $model = isset($_GET['model']) ? $_GET['model'] : '';
        $encrypt_id = $_GET['encrypt_id'];

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

        $this->render('storeList', array('model' => $model, 'encrypt_id' => $encrypt_id));
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

        $this->redirect(Yii::app()->createUrl('uCenter/user/login', array('encrypt_id' => $encrypt_id)));
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
        $encrypt_id = $_GET['encrypt_id'];

        $result = $user->getMaterial($material_id);
        if ($result['status'] == ERROR_NONE) {
            $model = $result['data'];
        } else {
            $model = array();
        }

        $this->render('material', array('model' => $model, 'encrypt_id' => $encrypt_id));
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

    //一级相册显示
    public function actionAlbum()
    {
        $encrypt_id = $_GET['encrypt_id'];
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;
        $albumC = new AlbumC();
        $result = json_decode($albumC->getAlbumList($merchant_id));
        if ($result->status == ERROR_NONE) {
            $album = $result->data;
            if (isset($_GET['album_id']) && !empty($_GET['album_id'])) {
                if ($_GET['album_id'] == 'all') {
                    $album_id = '';
                } else {
                    $album_id = $_GET['album_id'];
                }

            } else {
                $album_id = $result->data['0']->id;
            }
            $albumgroup_res = json_decode($albumC->getAlbumGroupList($album_id, $merchant_id));
            if ($albumgroup_res->status == ERROR_NONE) {
                $album_group = $albumgroup_res->data;
            }
        }

        $this->render('album', array(
            'album' => $album,
            'album_group' => $album_group,
            'encrypt_id' => $encrypt_id
        ));
    }

    //二级相册显示
    public function actionPhotoList()
    {
        if (isset($_GET['album_group_id']) && !empty($_GET['album_group_id'])) {
            $album_name = $_GET['album_name'];
            $albumC = new AlbumC();
            $encrypt_id = $_GET['encrypt_id'];
            $merchant = new MerchantC();
            $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $res->data->id;
            $result = json_decode($albumC->getAlbumList($merchant_id));
            if ($result->status == ERROR_NONE) {
                $result_img = json_decode($albumC->getAlbumImgList($_GET['album_group_id']));
                if ($result_img->status == ERROR_NONE) {
                    $this->render('albumImgList', array(
                        'imglist' => $result_img->data,
                        'album' => $result->data,
                        'album_name' => $album_name,
                        'encrypt_id' => $encrypt_id
                    ));
                }
            }
        }
    }

    /**
     * 微信支付同步修改订单号，储值金额
     */
    public function actionPaySuccess()
    {
        if (!empty($_GET['money'])) {
            if (isset($_GET['ordertype']) && $_GET['ordertype'] == 'SC') {
                $this->render('paySuccess', array(
                    'money' => $_GET['money'],
                    'ordertype' => $_GET['ordertype'],
                    'encrypt_id' => $_GET['encrypt_id']
                ));
            } else {
                $this->render('paySuccess', array(
                    'money' => $_GET['money'],
                    'encrypt_id' => $_GET['encrypt_id']
                ));
            }
        } else {
            $this->redirect(Yii::app()->createUrl('uCenter/user/fail', array('encrypt_id' => $_GET['encrypt_id'])));
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


}