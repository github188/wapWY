<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * 会员类
 */
class MobileUserUC extends mainClass
{
    /**
     * 获取商户id
     * @param @encrypt_id    商户加密id
     */
    public function getMerchant($encrypt_id, $merchant_id = '')
    {
        $result = array();
        try {
            //数据库查询
            if (!empty($encrypt_id)) {

                $model = Merchant::model()->find('encrypt_id = :encrypt_id and flag =:flag', array(
                    ':encrypt_id' => $encrypt_id,
                    ':flag' => FLAG_NO
                ));
            } elseif (!empty($merchant_id)) {
                $model = Merchant::model()->find('id = :id and flag =:flag', array(
                    ':id' => $merchant_id,
                    ':flag' => FLAG_NO
                ));
            }
            if (!empty($model)) {
                $result['data'] = array(
                    'id' => $model->id,
                    'appid' => $model->appid,
                    'wechat_appid' => $model->wechat_appid,
                    'wechat_appsecret' => $model->wechat_appsecret,
                    'wechat_subscription_appid' => $model->wechat_subscription_appid,
                    'wechat_subscription_appsecret' => $model->wechat_subscription_appsecret
                );
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 根据id获取merchant信息
     */
    public function getMerchantWithId($encrypt_id)
    {

        $model = Merchant::model()->find('encrypt_id = :encrypt_id', array(
            ':encrypt_id' => $encrypt_id
        ));

        return $model;
    }

    /**
     * 根据id和type类型获取商家活动信息
     */
    public function checkActivity($merchant_id, $type)
    {
        $model = MarketingActivity::model()->findAll('merchant_id = :merchant_id and flag =:flag and type=:type and status=:status', array(
            ':merchant_id' => $merchant_id,
            ':flag' => FLAG_NO,
            ':type' => $type,
            ':status' => MARKETING_ACTIVITY_STATUS_IN_PROGRESS,
        ));

        return $model;
    }


    /**
     * 保存服务窗openid
     * @param $user_id         用户id
     * @param $open_id         阿里openId
     */
    public function setAliOpenId($user_id, $ali_open_id)
    {
        $result = array();
        try {
            $model = User::model()->findByPk($user_id);

            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }
            $model['alipay_fuwu_id'] = $ali_open_id;

            $from = ',' . USER_FROM_ALIPAY . ',';
            //记录用户来源
            if (!strstr($model['from'], $from)) {
                $model['from'] .= $from;
            } else {
            }

            $model['login_time'] = date('Y-m-d H:i:s');

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 保存公众号openid
     * @param $user_id         用户id
     * @param $open_id         微信openId
     */
    public function setWechatOpenId($user_id, $wechat_open_id)
    {
        $result = array();
        try {
            $model = User::model()->findByPk($user_id);

            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }
            if (!empty($wechat_open_id)) {
                $model['wechat_id'] = $wechat_open_id;
            }

            $from = ',' . USER_FROM_WECHAT . ',';
            //记录用户来源
            if (!strstr($model['from'], $from)) {
                $model['from'] .= $from;
            } else {
            }

            $model['login_time'] = date('Y-m-d H:i:s');

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取公众号openid
     * @param $user_id         用户id
     */
    public function getWechatOpenId($user_id)
    {

        $result = array();
        try {
            $model = User::model()->findByPk($user_id);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }
            $result['data'] = $model->wechat_id;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 获取支付宝open_id
     * @param $user_id
     * @return string
     */
    public function getAlipayOpenId($user_id)
    {

        $result = array();
        try {
            $model = User::model()->findByPk($user_id);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }
            $result['data'] = $model->alipay_fuwu_id;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 免登操作-服务窗
     * @param $merchant_id           商户id
     * @param $ali_open_id           阿里openid
     */
    public function checkAliOpenId($merchant_id, $ali_open_id)
    {
        $result = array();
        try {
            $model = User::model()->find('merchant_id = :merchant_id and alipay_fuwu_id = :alipay_fuwu_id and account is not null', array(
                'merchant_id' => $merchant_id,
                'alipay_fuwu_id' => $ali_open_id
            ));
            if (!empty($model)) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     *免登操作-公众号
     * @param $merchant_id           商户id
     * @param $ali_open_id           阿里openid
     */
    public function checkWechatOpenId($merchant_id, $wechat_open_id)
    {
        $result = array();
        try {
            $model = User::model()->find('merchant_id = :merchant_id and wechat_id = :wechat_id and account is not null', array(
                'merchant_id' => $merchant_id,
                'wechat_id' => $wechat_open_id
            ));
            if (!empty($model)) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 会员注册
     * @param $merchant_id   商户id
     * @param $account       账号/手机号
     * @param $msg_pwd       账号/手机号
     * @param $pwd           密码
     * @return string
     */
    public function userRegister($merchant_id, $account, $msg_pwd, $pwd)
    {
        $result = array();  //返回值
        try {
            //参数验证
            if (empty($account) || !isset($account)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('账号为空');
            }

            if (empty($msg_pwd) || !isset($msg_pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('短信验证码为空');
            }

            $msg = $this->checkMsgPwd($account, $msg_pwd);
            if (!$msg) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('短信验证码错误');
            }

            if (empty($pwd) || !isset($pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('密码为空');
            }

            $flag = $this->accountExist($merchant_id, $account);
            if ($flag) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                throw new Exception('手机已注册过');
            }

            //保存会员账号密码
            $model = new User();
            $model->merchant_id = $merchant_id;
            $model->account = $account;
            $model->pwd = md5($pwd);
            $model->regist_time = date('Y-m-d H:i:s');
            $model->create_time = date('Y-m-d H:i:s');

            $user_grade = UserGrade::model()->find('merchant_id = :merchant_id and if_default = :if_default', array(':merchant_id' => $merchant_id, ':if_default' => USER_GRADE_DEFAULT_YES));
            $model->membershipgrade_id = $user_grade->id;

            //生成会员卡号
            $member_card_no = $this->getRandChar(8);
            $user = User::model()->find('membership_card_no=:membership_card_no and flag=:flag', array(
                ':membership_card_no' => $member_card_no,
                ':flag' => FLAG_NO
            ));
            while (!empty($user)) {
                $member_card_no = $this->getRandChar(8);
                $user = User::model()->find('membership_card_no=:membership_card_no and flag=:flag', array(
                    ':membership_card_no' => $member_card_no,
                    ':flag' => FLAG_NO
                ));
            }
            $model->membership_card_no = $member_card_no;

            if ($model->save()) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //获取定长的随机字符串 首位不为零
    private function getRandChar($length)
    {
        $str = null;
        $strPol = "012356789";
        $strPolnoZero = "12356789";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $str .= $strPolnoZero[rand(0, $max - 1)];
            } else {
                $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
            }
        }
        return $str;
    }

    /**
     * 验证账号/手机是否存在
     * @param $merchant_id    商户id
     * @param $account        账号/手机
     */
    public function accountExist($merchant_id, $account)
    {
        //检查账号是否已存在
        $user = User::model()->find('merchant_id = :merchant_id and account = :account and flag =:flag', array(
            ':merchant_id' => $merchant_id,
            ':account' => $account,
            ':flag' => FLAG_NO
        ));
        if (isset($user) && !empty($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查商户短信余额
     * @param $merchant_id       商户id
     */
    public function checkMsgNum($merchant_id)
    {
        $result = array();
        $model = Merchant::model()->findByPk($merchant_id);
        if (!empty($model) && $model['msg_num'] > 0) {
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } else {
            $result['status'] = ERROR_REQUEST_FAIL; //状态码
            $result['errMsg'] = '商户短信功能已欠费，请联系商户'; //错误信息;
        }

        return json_encode($result);
    }

    /**
     * 减少商户短信余额
     * @param $merchant_id       商户id
     */
    public function minusMsgNum($merchant_id)
    {
        $result = array();

        try {
            $model = Merchant::model()->findByPk($merchant_id);
            if (!empty($model)) {
                $model['msg_num'] = $model['msg_num'] - 1;

                if ($model->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                }
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                throw new Exception('数据异常，查无此商户');
            }
        } catch (Exception $e) {
            $result['status'] = ERROR_REQUEST_FAIL; //状态码
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 会员登录-检查账号密码
     * @param $merchant_id    商户id
     * @param $account        账号/手机号
     * @param $pwd                          密码
     */
    public function checkAccount($encrypt_id, $account, $pwd)
    {
        //返回结果
        $result = array();
        try {
            $merchant = Merchant::model()->find('encrypt_id =:encrypt_id', array(
                ':encrypt_id' => $encrypt_id
            ));
            $merchant_id = $merchant->id;

            if (empty($account) || !isset($account)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('账号为空');
            }

            if (empty($pwd) || !isset($pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('密码为空');
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('account = :account');
            $criteria->params[':account'] = $account;
			$criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('pwd = :pwd');
            $criteria->params[':pwd'] = md5($pwd);

            $model = User::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账号或密码错误');
            } else {
                //判断用户是否有会员卡号 没有就创建一个
                if (empty($model->membership_card_no)) {
                    //生成会员卡号
                    $member_card_no = $this->getRandChar(8);
                    $user = User::model()->find('membership_card_no=:membership_card_no and flag=:flag', array(
                        ':membership_card_no' => $member_card_no,
                        ':flag' => FLAG_NO
                    ));
                    while (!empty($user)) {
                        $member_card_no = $this->getRandChar(8);
                        $user = User::model()->find('membership_card_no=:membership_card_no and flag=:flag', array(
                            ':membership_card_no' => $member_card_no,
                            ':flag' => FLAG_NO
                        ));
                    }
                    $model->membership_card_no = $member_card_no;
                    if ($model->update()) {

                    } else {
                        throw new Exception('会员卡号生成失败');
                    }
                }


                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = $model['id'];
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 登陆操作-保存session，将购物车中的物品存入数据困
     *
     */
    public function userLogin()
    {

    }

    /*
	 * 登出操作-删除微信的openid或者支付宝服务窗的openid
	 * $user_id 用户id
	 * $source 当前用户来源
	 * */
    public function userLogout($user_id, $source)
    {
        //返回结果
        $result = array();
        try {
            $user = User::model()->findByPk($user_id);
            if (!empty($user)) {
                if (!empty($source) && $source == 'wechat') {
                    $user->wechat_id = '';
                } elseif (!empty($source) && $source == 'alipay_wallet') {
                    $user->alipay_fuwu_id = '';
                }
                if ($user->update()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    throw new Exception('退出登录失败');
                }

            } else {
                throw new Exception('该用户不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 改变过期优惠券状态 --用户
     * @param $user_id          用户id
     */
    public function changeCouponsStatus($user_id)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $now_time = date("Y-m-d h:i:s");
            $userCoupons = UserCoupons::model()->findAll('user_id = :user_id and status = :status and flag = :flag', array(
                ':user_id' => $user_id,
                ':status' => COUPONS_USE_STATUS_UNUSE,
                ':flag' => FLAG_NO
            ));
            foreach ($userCoupons as $k => $v) {
                if (strtotime($now_time) > strtotime($v->end_time)) {
                    $v->status = COUPONS_USE_STATUS_EXPIRED;
                    if ($v->update()) {

                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('数据更新失败');
                    }
                }
            }
            $transaction->commit(); //数据提交
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = "";

        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 改变过期优惠券状态 --商户
     * @param $merchant_id        商户id
     */
    public function changeMerchantCouponsStatus($merchant_id)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $now_time = date("Y-m-d h:i:s");
            $Coupons = Coupons::model()->findAll('merchant_id = :merchant_id and if_invalid = :if_invalid and flag = :flag', array(
                ':merchant_id' => $merchant_id,
                ':if_invalid' => IF_INVALID_NO,
                ':flag' => FLAG_NO,
            ));
            foreach ($Coupons as $k => $v) {
                if (($v->time_type == VALID_TIME_TYPE_FIXED && strtotime($now_time) > strtotime($v->end_time)) || ($v->num <= $v->get_num)) {
                    $v->if_invalid = IF_INVALID_YES;
                    if ($v->update()) {

                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('数据更新失败');
                    }
                }
            }
            $transaction->commit(); //数据提交
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = "";

        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 优惠券/红包列表
     * @param $merchant_id      商户id
     * @param $user_id          用户id
     * @param $coupons_status   优惠券id
     */
    public function getCouponsList($merchant_id, $user_id, $coupons_status, $coupons_type)
    {
        $result = array();
        try {
            //参数验证
            //TODO 
            $criteria = new CDbCriteria();
            $criteria->addCondition('user_id = :user_id');
            $criteria->params[':user_id'] = $user_id;
            $criteria->addCondition('status = :status');
            $criteria->params[':status'] = $coupons_status;

            if ($coupons_type == COUPON_TYPE_REDENVELOPE) {
                $criteria->addCondition('coupons.type = :type');
                $criteria->params[':type'] = $coupons_type;
            } else {
                $criteria->addNotInCondition('coupons.type', array(COUPON_TYPE_REDENVELOPE));
            }

            $criteria->addCondition('coupons.merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;

            $model = UserCoupons::model()->with('coupons')->findAll($criteria);

// 			CVarDumper::dump($model);
// 			exit();

            //数据封装
            $data = array();
            foreach ($model as $key => $value) {
                $data['list'][$key]['id'] = $value['id'];
                $data['list'][$key]['name'] = $value['coupons']['name'];
                $data['list'][$key]['type'] = $value['coupons']['type'];
                //红包、代金券
                if ($value['coupons']['type'] == COUPON_TYPE_REDENVELOPE || $value['coupons']['type'] == COUPON_TYPE_CASH) {
                    $data['list'][$key]['money'] = $value['money'];
                } elseif ($value['coupons']['type'] == COUPON_TYPE_DISCOUNT) {  //折扣券
                    $data['list'][$key]['money'] = $value['coupons']['discount'];
                } else {
                    $data['list'][$key]['money'] = $value['coupons']['exchange'];
                }
                $data['list'][$key]['color'] = $value['coupons']['color'];

                $data['list'][$key]['validity'] = date("Y-m-d", strtotime($value['start_time'])) . '-' . date("Y-m-d", strtotime($value['end_time']));
            }

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取优惠券列表（新）
     */
    public function newGetCouponsList($merchant_id, $user_id)
    {
        $result = array();
        try {
            //改变优惠券过期状态
            $this->changeCouponsStatus($user_id);

            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('user.id, user.status, shop.name, shop.logo_img, coupons.title, user.start_time, user.end_time , coupons.color,coupons.merchant_short_name');
            $cmd->from(array('wq_coupons coupons', 'wq_user_coupons user', 'wq_onlineshop shop'));
            $cmd->where(array(
                'AND',
                'user.user_id = :user_id',
                'user.coupons_id = coupons.id',
                'shop.merchant_id = coupons.merchant_id',
                'coupons.merchant_id = :merchant_id',
                'user.status != :status1',
                'user.status != :status2',
            ));
            $cmd->order = 'user.end_time ASC';
            $cmd->params = array(
                ':user_id' => $user_id,
                ':merchant_id' => $merchant_id,
                ':status1' => COUPONS_USE_STATUS_GAVE,
                ':status2' => COUPONS_USE_STATUS_USED,
            );
            $model = $cmd->queryAll();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 获取个人信息
     * @param $user_id         用户id
     */
    public function getPersonalInformation($user_id, $merchant_id = '')
    {
        $result = array();
        try {
            //改变优惠券过期状态
            $this->changeCouponsStatus($user_id);

            //获取可用优惠券、红包数量
            $db = Yii::app()->db;
            $sql = "SELECT u.*, c.merchant_id,c.type FROM wq_user_coupons `u` LEFT JOIN wq_coupons c ON u.`coupons_id`=c.`id` WHERE u.`user_id`=" . $user_id;
            $command = $db->createCommand($sql);
            $result = $command->queryAll();

            $coupons_hongbao = 0;
            $coupons = 0;
            foreach ($result as $k => $v) {
                if ($v['status'] == 1) {
                    if ($v['type'] == COUPON_TYPE_REDENVELOPE) {
                        $coupons_hongbao++;
                    } else {
                        $coupons++;
                    }
                }
            }

// 			$model = User::model()->findByPk($user_id);
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('user.*');
            $cmd->from(array('wq_user user'));
            $cmd->where(array(
                'AND',
                'user.id = :user_id',
            ));
            $cmd->params = array(
                ':user_id' => $user_id,
            );
            $model = $cmd->queryRow();

            //数据封装
            $data = array();
            if (isset($model['membershipgrade_id'])) {
                $grade = UserGrade::model()->findByPk($model['membershipgrade_id']);
                $data['grade_name'] = $grade['name'];
            } else {
                $data['grade_name'] = "普通会员";
            }
            $data['id'] = $model['id'];
            $data['account'] = $model['account'];
            $data['pwd'] = $model['pwd'];
            $data['nickname'] = $model['nickname'];
            $data['name'] = $model['name'];
            $data['sex'] = $model['sex'];
            $data['birthday'] = $model['birthday'];
            $data['social_security_number'] = $model['social_security_number'];
            $data['email'] = $model['email'];
            $data['marital_status'] = $model['marital_status'];
            $data['work'] = $model['work'];
            $data['points'] = $model['points'];
            $data['avatar'] = $model['avatar'];
//             $data['alipay_avatar'] = $model['alipay_avatar'];
            $data['free_secret'] = $model['free_secret'];
            $data['money'] = $model['money'];
            $data['hongbao_num'] = $coupons_hongbao;
            $data['coupons_num'] = $coupons;

            $merchant = Merchant::model()->findByPk($model['merchant_id']);
            $data['encrypt_id'] = $merchant->encrypt_id;

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);

    }

    /**
     * 修改个人信息
     * @param $id                                                          主键id
     * @param $title                      修改的字段在数据库中的名字
     * @param $data                                                              修改内容
     */
    public function editPersonalInformation($id, $title, $data)
    {
        $result = array();

        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $id;
            $model = User::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }
            $model[$title] = $data;

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 修改密码
     * @param $user_id    主键id
     * @param $old_pwd      当前密码
     * @param $new_pwd        新密码
     * @param $con_pwd                确认新密码
     */
    public function changePwd($user_id, $old_pwd, $new_pwd, $con_pwd)
    {
        $result = array();

        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $user_id;
            $model = User::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }

            if ($model['pwd'] != md5($old_pwd)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('密码错误');
            }
            if ($model['pwd'] == md5($new_pwd)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('新密码不能与原密码一致');
            }
            if ($new_pwd != $con_pwd) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('两次密码输入不一致');
            }


            $model['pwd'] = md5($new_pwd);

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 找回密码 - 设置新密码
     * @param $merchant_id   商户id
     * @param $account       账号
     * @param $new_pwd           新密码
     * @param $con_pwd                   确认新密码
     */
    public function setNewPassword($merchant_id, $account, $new_pwd, $con_pwd)
    {
        $result = array();

        try {
            //参数验证
            //TODO
            if (!isset($account) && empty($account)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');;
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('account = :account');
            $criteria->params[':account'] = $account;

            $model = User::model()->find($criteria);
            //如果查不到该用户则新建一个
            if (empty($model)) {
                /*$model = new User();
				$model['account'] = $account;
				$model['merchant_id'] = $merchant_id;*/
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该账号不存在');
            }

            if ($new_pwd != $con_pwd) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('两次密码输入不一致');
            }

            $model['pwd'] = md5($new_pwd);

            if ($model->update()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = $model['id'];
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 验证短信验证码
     * @param $account     手机号码
     * @param $msg_pwd     短信密码
     */
    public function checkMsgPwd($account, $msg_pwd)
    {
        $check_msg_pwd = Yii::app()->memcache->get($account);
        if ($check_msg_pwd == $msg_pwd) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 修改绑定手机
     * @param $merchant_id      商户id
     * @param $user_id          会员id
     * @param $account          手机号
     * @param $msg_pwd          短信密码
     */
    public function setNewAccount($merchant_id, $user_id, $account, $msg_pwd)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $flag = $this->accountExist($merchant_id, $account);
            if ($flag) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                throw new Exception('手机已注册过');
            }
            $check_msg_pwd = Yii::app()->memcache->get($account);
            if ($check_msg_pwd != $msg_pwd) {
                $result['status'] = ERROR_PARAMETER_FORMAT; //状态码
                throw new Exception('短信验证码错误');
            } else {
                $model = User::model()->findByPk($user_id);
                $model['account'] = $account;
                if ($model->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $result['data'] = '';
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                    $result['data'] = '';
                }
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取会员信息
     * @param $merchant_id    商户id
     * @param $user_id        用户id
     */
    public function getMemberShipCard($merchant_id, $user_id)
    {
        $result = array();
        try {
            //查询用户
            $user = User::model()->find('id=:id and flag =:flag and merchant_id =:merchant_id', array(
                ':id' => $user_id,
                ':flag' => FLAG_NO,
                ':merchant_id' => $merchant_id
            ));


            //数据封装
            $data = array();
            $data['grade_name'] = $user->grade->name;
            $data['card_no'] = $user->membership_card_no;
            $data['card_name'] = $user->grade->membership_card_name;
            $data['discount'] = $user->grade->discount;
            $data['discount_illustrate'] = $user->grade->discount_illustrate;
            $data['membercard_img'] = $user->grade->membercard_img;
            $data['if_hideword'] = $user->grade->if_hideword;
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 我的储值
     * @param $merchant_id    商户id
     * @param $user_id        用户id
     */
    public function getMyStored($merchant_id, $user_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('user.money, order.create_time, stored.stored_money, stored.get_money,order.num,order.order_no');
            $cmd->from(array('wq_user user', 'wq_stored_order order', 'wq_stored stored'));
            $cmd->where(array(
                'AND',
                'user.id = :user_id',
                'user.merchant_id = :merchant_id',
                ' order.user_id=user.id ',
                ' stored.merchant_id =user.merchant_id',
                'order.stored_id=stored.id',
				'order.pay_status = :pay_status'
                //'order.trade_no is not NULL',
            ));
            $cmd->order = 'order.create_time DESC';
            $cmd->params = array(
                ':user_id' => $user_id,
                ':merchant_id' => $merchant_id,
				':pay_status' => 2
            );
            $model = $cmd->queryAll();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /*
	 * 获取储值活动详情
	 * */
    public function getStoredInfo($stored_id, $merchant_id)
    {
        $result = array();
        try {
            $stored = Stored::model()->findByPk($stored_id);
            $onlineshop = Onlineshop::model()->find('merchant_id =:merchant_id', array(
                ':merchant_id' => $merchant_id
            ));
            if ($stored) {
                $data = array();
                $data['id'] = $stored->id;
                $data['stored_money'] = $stored->stored_money;
                $data['get_money'] = $stored->get_money;
                $data['start_time'] = $stored->start_time;
                $data['end_time'] = $stored->end_time;
                $data['shopname'] = $onlineshop->name;

                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该储值活动不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 储值活动
     * @param $merchant_id 商户id
     */
    public function getStoredActivity($merchant_id)
    {
        $result = array();
        try {
             $model = Stored::model() -> findAll('merchant_id =:merchant_id and flag=:flag and start_time <=:start_time and end_time >=:end_time order by create_time desc',array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
                ':start_time' => date('Y-m-d H:i:s'),
                ':end_time' => date('Y-m-d H:i:s'),
            ));
            $data = array();
            $num = 0;
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $data[$num]['id'] = $value['id'];
                    $data[$num]['stored_money'] = $value['stored_money'];
                    $data[$num]['get_money'] = $value['get_money'];
                    $num++;
                }
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * $user_id 用户id
     * $store_num 商品数量
     * $order_no 商品订单号
     * string
     */
    public function saveStoredOrder($user_id, $store_num, $order_no, $stored_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO

            //保存商品购买记录
            $model = new StoredOrder();

            $model['user_id'] = $user_id;
            $model['num'] = $store_num;
            $model['order_no'] = $order_no;
            $model['stored_id'] = $stored_id;
            $model['create_time'] = date('Y-m-d H:i:s');
            $model['pay_status'] = ORDER_STATUS_UNPAID;
            $model['order_status'] = ORDER_STATUS_NORMAL;
            if ($model->save()) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 查询选择的stored
     * $stored_id
     */
    public function querySelectStored($stored_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('stored_money,get_money');
            $cmd->from(array('wq_stored'));
            $cmd->where('id = :id', array(':id' => $stored_id));
//            $cmd->params = array(
//                ':merchant_id'=>$merchant_id);
            $model = $cmd->queryRow();
            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 查找商户记录
     * $merchantId 商户id
     */
    public function findMerchantPid($merchantId)
    {
        $model = Merchant::model();
        $result = $model->findByPk($merchantId);
        return $result;
    }

    /**
     * 事务处理表stored_order和merchant
     */
    public function WapTranscation($stored_order_id, $trade_no)
    {

        //创建一个数据库事务
        $transcation = Yii::app()->db->beginTransaction();
        try {
            //更新stored_order表
            $model_stroedOrder = StoredOrder::model()->find('order_no =:order_no and pay_status=:pay_status and order_status=:order_status', array(
                ':order_no' => $stored_order_id,
                ':pay_status' => ORDER_STATUS_UNPAID,
                ':order_status' => ORDER_STATUS_NORMAL,
            ));
            if (!empty($model_stroedOrder)) {
                $model_stroedOrder->trade_no = $trade_no;
                $model_stroedOrder->pay_time = date('Y-m-d H:i:s');
                $model_stroedOrder->pay_status = ORDER_STATUS_PAID;
                $model_stroedOrder->order_status = ORDER_STATUS_NORMAL;
                //查询获得的钱
                $obtainMoney = $model_stroedOrder->stored->stored_money;
                $getMoney = $model_stroedOrder->stored->get_money;
                $Num = $model_stroedOrder->num;
				
                //修改商户的储值
                $model_Merchant = User::model()->findByPk($model_stroedOrder->user_id);
                $obtain_money = floatval($obtainMoney + $getMoney) * intval($Num);
                if(!empty($model_Merchant->money)){
                    $model_Merchant->money += $obtain_money;
                }else{
                    $model_Merchant->money = $obtain_money;
                }
                $returnMoney = $obtainMoney * $Num;
                if ($model_stroedOrder->update()) {
                    if ($model_Merchant->update()) {
                        //数据库操作成功事务提交
                        $transcation->commit();
                        return $returnMoney;
                    } else {
                         throw  new Exception();
                    }
                } else {
                     throw  new Exception();
                }
            }
        } catch (Exception $e) {
            $transcation->rollback();
            return false;
        }
        return false;
    }

    public function queryWappayInfomation($storedorder_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('stored.stored_money,stored.get_money,order.order_no,order.num');
            $cmd->from(array('wq_stored stored', 'wq_stored_order order'));
            $cmd->where(array(
                'AND',
                'order.id=:id',
                'stored.id=order.stored_id',
            ));
            $cmd->params = array(
                ':id' => $storedorder_id);
            $model = $cmd->queryRow();
            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 我的积分
     * @param $user_id    用户id
     */
    public function getPointList($user_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('user.points, point.create_time, point.from, point.balance_of_payments, point.points');
            $cmd->from(array('wq_user_pointsdetail point', 'wq_user'));
            $cmd->where(array(
                'AND',
                'user.id = :user_id',
                'user.id = point.user_id',
            ));
            $cmd->params = array(
                ':user_id' => $user_id,
            );
            $model = $cmd->queryAll();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 我的预定
     * @param $user_id        用户id
     */
    public function getBookList($user_id)
    {
        $data = array();
        $result = array();

        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('book.*, store.name');
            $cmd->from(array('wq_book_record book', 'wq_store store'));
            $cmd->order = 'book.book_time DESC';
            $cmd->where(array(
                'AND',
                'book.user_id = :user_id',
                'book.store_id = store.id',
            ));

            $cmd->params = array(
                ':user_id' => $user_id,
            );

            $model = $cmd->queryAll();

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $data[$key] = $value;
                    $book_information = explode("@", $value['book_information']);
                    $data[$key]['book_name'] = $book_information[0];
                    $data[$key]['book_phone'] = $book_information[1];
                    $data[$key]['book_sex'] = $book_information[2];
                    $data[$key]['book_num'] = $book_information[3];
                    $data[$key]['want_come_time'] = $book_information[4];
                }
            }

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 预定操作
     * @param $user_id          用户id
     * @param $store_id         门店id
     * @param $book_name        预订人姓
     * @param $people_num       到店人数
     * @param $time             预计到店时间
     * @param $phone_num        预订人电话
     * @param $sex              预订人性别
     * @param $ramark           备注
     */
    public function bookOperate($user_id, $store_id, $book_name, $people_num, $time, $phone_num, $sex, $remark)
    {
        $result = array();
        try {
            //参数验证
            //TODO

            //保存会员账号密码
            $model = new BookRecord();

            $model['user_id'] = $user_id;
            $model['store_id'] = $store_id;
            $model['book_time'] = date('Y-m-d H:i:s');
            $model['create_time'] = date('Y-m-d H:i:s');
            $model['book_information'] = $book_name . '@' . $phone_num . '@' . $GLOBALS['__BOOKSEX'][$sex] . '@' . $people_num . '@' . $time;
            $model['status'] = BOOK_RECORD_STATUS_WAIT;
            $model['remark'] = $remark;
            if ($model->save()) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 预定 - 预定详情
     * @param $record_id      预定id
     */
    public function getBookDetail($record_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('record.*, store.name');
            $cmd->from(array('wq_book_record record', 'wq_store store'));
            $cmd->where(array(
                'AND',
                'record.id = :record_id',
                'record.store_id = store.id',
            ));
            $cmd->params = array(
                ':record_id' => $record_id,
            );
            $model = $cmd->queryRow();


            $arr_information = explode("@", $model['book_information']);
            $model['book_name'] = $arr_information[0];
            $model['phone_num'] = $arr_information[1];
            $model['sex'] = $arr_information[2];
            $model['people_num'] = $arr_information[3];
            $model['time'] = $arr_information[4];

            if (!isset($model) || empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取预定详细信息出错');
            }
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';

            $result['data'] = $model;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 预约 - 取消预约
     * @param $record_id       预约id
     */
    public function bookCancel($record_id)
    {
        $result = array();
        try {
            $model = BookRecord::model()->findByPk($record_id);
            $model['status'] = BOOK_RECORD_STATUS_CANCEL;
            $model['cancel_time'] = date('Y-m-d H:i:s');

            if ($model->save()) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 判断该用户是否可以领取红包
     * @param $user_id        用户id
     * @param $coupons_id     优惠券id
     */
    public function judgeUserCoupons($user_id, $coupons_id)
    {
        $result = array();
        try {
            //参数设置
            //TODO
            $time = date('Y-m-d H:i:s');
            $send_num = UserCoupons::model()->count('coupons_id = :coupons_id', array(':coupons_id' => $coupons_id));
            $coupons = Coupons::model()->findByPk($coupons_id);
            $receive_num = UserCoupons::model()->count('coupons_id = :coupons_id and user_id = :user_id', array(':coupons_id' => $coupons_id, ':user_id' => $user_id));

            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            
            if ($coupons['flag'] == FLAG_YES) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = '已领完';
                //throw new Exception('优惠券发放已结束');
            }
// 			if ($coupons['start_time'] > $time) {
// 				$result['status'] = ERROR_PARAMETER_FORMAT;
// 				throw new Exception('优惠券还未开始发放');
// 			}
// 			if ($coupons['end_time'] < $time) {
// 				$result['status'] = ERROR_PARAMETER_FORMAT;
// 				throw new Exception('优惠券发放已结束');
// 			}
            if ($coupons['time_type'] == VALID_TIME_TYPE_FIXED && $coupons['end_time'] < $time) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = '已领完';
                //throw new Exception('优惠券发放已结束');
            }
            if ($send_num >= $coupons['num']) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = '已领完';
                //throw new Exception('优惠券已被领完');
            }
            if ($receive_num >= $coupons['receive_num']) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = '领取已达到上限';
                //throw new Exception('优惠券已到上限');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 领取红包/优惠券
     * @param $user_id       用户id
     * @param $coupons_id    优惠券id
     */
    public function receiveCoupons($user_id, $coupons_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $coupons = Coupons::model()->findByPk($coupons_id);

            $model = new UserCoupons();
            $model['user_id'] = $user_id;
            $model['coupons_id'] = $coupons_id;
            if (!empty($coupons['validtime_fixed_value'])) {
                $model['start_time'] = date('Y-m-d H:i:s');
                $day = '+' . $coupons['validtime_fixed_value'] . '' . 'day';
                $model['end_time'] = date('Y-m-d H:i:s', strtotime($day));
            } else {
                $model['start_time'] = $coupons['validtime_start'];
                $model['end_time'] = $coupons['validtime_end'];
            }
            if (!empty($coupons['userdefined_value'])) {
                $value_arr = explode("-", $coupons['userdefined_value']);
                $min_value = $value_arr[0];
                $max_value = $value_arr[1];
                $model ['money'] = rand($min_value, $max_value);
            } elseif ($coupons['type'] == COUPON_TYPE_REDENVELOPE || $coupons['type'] == COUPON_TYPE_CASH) {
                $model['money'] = $coupons['fixed_value'];
            }
            $model['create_time'] = date('Y-m-d H:i:s');

            if ($model->save()) {
                $result['data'] = $model['id'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 优惠券详情-未领取
     * @param $coupons_id    优惠券id
     */
    public function couponsDetailNotReceive($coupons_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('coupons.*');
            $cmd->from(array('wq_coupons coupons'));
            $cmd->where(array(
                'AND',
                'coupons.id = :coupons_id',
            ));
            $cmd->params = array(
                ':coupons_id' => $coupons_id,
            );
            $model = $cmd->queryRow();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 优惠券详情-已领取
     * @param $coupons_id    优惠券id
     * @param $user_id       用户id
     */
    public function couponsDetail($coupons_id, $user_id)
    {
        $result = array();
        try {

            $user_coupons = UserCoupons::model()->find('id =:id and flag =:flag', array(
                ':id' => $coupons_id,
                ':flag' => FLAG_NO
            ));
            $data = array();
            if (!empty($user_coupons)) {
                $data['id'] = $user_coupons->id;
                $data['type'] = $user_coupons->coupons->type;
                $data['money'] = $user_coupons->money;
                $data['name'] = $user_coupons->coupons->name;
                $data['fixed_value'] = $user_coupons->coupons->exchange;
                $data['discount'] = $user_coupons->coupons->discount;
                $data['use_illustrate'] = $user_coupons->coupons->use_illustrate;
                $data['validtime_start'] = $user_coupons->coupons->validtime_start;
                $data['validtime_end'] = $user_coupons->coupons->validtime_end;
                $data['use_num'] = $user_coupons->coupons->receive_num;
                $data['validtime_fixed_value'] = $user_coupons->coupons->validtime_fixed_value;
            } else {
                throw new Exception('该优惠券不存在');
            }


            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 优惠券使用说明
     * @param $coupons_id    优惠券id
     * @param $user_id       用户id
     */
    public function getCouponsInstructions($user_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('user.*, coupons.receive_num, coupons.min_pay_money, coupons.use_num, coupons.if_with_userdiscount, coupons.if_with_coupons, coupons.order_use_num, coupons.max_discount_money');
            $cmd->from(array('wq_coupons coupons', 'wq_user_coupons user'));
            $cmd->where(array(
                'AND',
                'user.coupons_id = coupons.id',
                'user.user_id = :user_id',
            ));
            $cmd->params = array(
                ':user_id' => $user_id,
            );
            $model = $cmd->queryRow();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 我的订单(消费订单)
     * @param $user_id                      用户id
     * @param $stored_confirm_status        储值支付确认状态
     */
    public function getPayOrderList($user_id, $stored_confirm_status)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('order.stored_confirm_status, order.id, order.order_no, order.pay_status, store.name, order.order_paymoney, order.create_time, order.stored_paymoney');
            $cmd->from(array('wq_order order', 'wq_store store'));

            //判断储值支付确认状态  
            if ($stored_confirm_status == ORDER_PAY_WAITFORCONFIRM) {
                $sql = "order.stored_confirm_status = :stored_confirm_status";
            } else {
                $sql = "order.stored_confirm_status != :stored_confirm_status";
            }

            $where = array(
                'AND',
                'order.user_id = :user_id',
                'order.store_id = store.id',
                $sql,
            );
            $cmd->where($where);

            $cmd->params = array(
                ':user_id' => $user_id,
                ':stored_confirm_status' => ORDER_PAY_WAITFORCONFIRM,
            );
            $cmd->order = 'create_time desc';
            $model = $cmd->queryAll();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取订单详细信息
     * @param $order_id    订单id
     */
    public function getOrderDetail($order_id)
    {
        $result = array();
        try {
            //参数验证
            if (!isset($order_id) || empty($order_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('读取订单信息出错');
            }
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('order.*, store.name, user.account');
            $cmd->from(array('wq_order order', 'wq_store store', 'wq_user user'));

            $cmd->where(array(
                'AND',
                'order.id = :order_id',
                'order.store_id = store.id',
                'order.user_id = user.id',
            ));

            $cmd->params = array(
                ':order_id' => $order_id,
            );
            $model = $cmd->queryRow();

            if (!isset($model) || empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取订单详细信息出错');
            }

            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
            $result['data'] = $model;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取用户小额免密金额
     * @param $user_id     用户id
     */
    public function getFreeSecret($user_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $model = User::model()->findByPk($user_id);

            $result['data'] = $model['free_secret'];
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 修改用户小额免密金额
     * @param $user_id     用户id
     */
    public function editFreeSecret($user_id, $free_secret)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $model = User::model()->findByPk($user_id);

            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('读取个人信息出错');
            }

            $model['free_secret'] = $free_secret;
            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 线上店铺-基本信息
     * @param $merchant_id    商户id
     */
    public function getShop($merchant_id)
    {
        $result = array();
        try {
            //验证参数
            //TODO
            $model = Onlineshop::model()->find('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));

            $merchant = Merchant::model()->find('id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));

            //数据封装
            $data = array();

            if (!empty($model) && isset($model)) {
                $data['image'] = $model['img'];
                $data['image_logo'] = $model['logo_img'];
                $data['if_book'] = $model['if_book'];
                $data['if_check'] = $model['if_check'];
                $data['if_coupons'] = $model['if_coupons'];
                $data['if_hongbao'] = $model['if_hongbao'];
                $data['if_online_shoppingmall'] = $model['if_online_shoppingmall'];
                $data['introduction'] = $model['introduction'];
                $data['name'] = $model['name'];

                $data['merchant_name'] = $merchant['name'];
                $data['if_stored'] = $merchant['if_stored'];
            }

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 线上店铺-门店信息
     * @param $merchant_id    商户id
     */
    public function getStore($merchant_id)
    {
        $result = array();
        try {
            //验证参数
            //TODO
            $model = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));

            //数据封装
            $data = array();

            if (!empty($model) && isset($model)) {
                foreach ($model as $key => $value) {
// 					$data[$key]['store_id'] = $value['id'];
// 					$data[$value['id']]['store_id'] =  $value['id'];
                    $data[$value['id']]['name'] = $value['name'];
                    $data[$value['id']]['telephone'] = $value['telephone'];
                    $data[$value['id']]['address'] = $value['address'];
                    $data[$value['id']]['logo'] = $value['logo'];
                    $data[$value['id']]['lng'] = $value['lng'];
                    $data[$value['id']]['lat'] = $value['lat'];
                }
            }
            $store_id = $model[0]['id'];

            $result['data'] = $data;
            $result['store_id'] = $store_id;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 线上店铺-优惠券信息
     * @param $merchant_id    商户id
     */
    public function getCoupons($merchant_id, $store_id)
    {
        $result = array();
        try {
            //验证参数
            //TODO
            $criteria = new CDbCriteria();

            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;

            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $criteria->addCondition('if_invalid = :if_invalid');
            $criteria->params[':if_invalid'] = IF_INVALID_NO;

            $criteria->compare('store_limit', ',' . $store_id . ',', true);

            $criteria->order = 'create_time DESC';

            $model = Coupons::model()->findAll($criteria);

            //数据封装
            $data = array();

            if (!empty($model) && isset($model)) {
                foreach ($model as $key => $value) {
                    //如果同步到微信
                    if ($value['if_wechat'] == IF_WECHAT_YES) {
                        //状态需要为已通过
                        if ($value['status'] == WX_CHECK_PASS) {
                            $data['coupons'][$value['id']]['title'] = $value['title'];
                            $data['coupons'][$value['id']]['type'] = $value['type'];
                            $data['coupons'][$value['id']]['code'] = $value['code'];
                        }
                    } else {
                        $data['coupons'][$value['id']]['title'] = $value['title'];
                        $data['coupons'][$value['id']]['type'] = $value['type'];
                        $data['coupons'][$value['id']]['code'] = $value['code'];
                    }
                }
            }

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }


    /**
     * 线上店铺-储值活动
     * @param $merchant_id    商户id
     */
    public function getStored($merchant_id)
    {
        $result = array();
        try {
            //验证参数
            //TODO
            $model = Stored::model()->findAll('merchant_id = :merchant_id and flag = :flag and start_time <= :start_time and end_time >= :end_time', array(
                ':merchant_id' => $merchant_id, 
                ':flag' => FLAG_NO,
                ':start_time' => date('Y-m-d'),
                ':end_time' => date('Y-m-d'),
            ));
            //数据封装
            $data = array();

            if (!empty($model) && isset($model)) {
                foreach ($model as $key => $value) {
                    $data[$value['id']]['stored_money'] = $value['stored_money'];
                    $data[$value['id']]['get_money'] = $value['get_money'];
                }
            }

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }


    /**
     * 线上店铺-获取商户相册数量
     * @param $merchant_id    商户id
     */
    public function getAlbumNum($merchant_id)
    {
        $result = array();
        try {
            $album_group = AlbumGroup::model()->findAll('merchant_id =:merchant_id and flag=:flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            $num = 0;
            foreach ($album_group as $k => $v) {
                $album_img = AlbumImg::model()->findAll('album_group_id=:album_group_id and flag=:flag', array(
                    ':album_group_id' => $v->id,
                    ':flag' => FLAG_NO
                ));
                $num += count($album_img);
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $num;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);

    }


    /**
     * 储值支付确认
     * @param unknown $order_no
     * @param unknown $user_id
     * @return string
     */
    public function storedOrderConfirm($order_no, $user_id)
    {
        $result = array();
        try {
            //验证参数
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_no = :order_no');
            $criteria->params[':order_no'] = $order_no;
            $criteria->addCondition('user_id = :user_id');
            $criteria->params[':user_id'] = $user_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Order::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('订单不存在');
            }
            if ($model['stored_confirm_status'] == ORDER_PAY_CONFIRM) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('订单已确认支付，无需重复确认');
            }

            //修改确认状态
            $model['stored_confirm_status'] = ORDER_PAY_CONFIRM;
            //扣除用户的储值余额
            $user = new UserSC();
            $res = $user->updateUserStored($user_id, -$model['stored_paymoney']);
            if ($res['status'] != ERROR_NONE) {
                throw new Exception($res['errMsg']);
            }

            //保存
            if (!$model->save()) {
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('数据保存失败');
            }
            //支付渠道
            $channel = $model['pay_channel'];
            if ($channel == ORDER_PAY_CHANNEL_STORED ||
                $channel == ORDER_PAY_CHANNEL_CASH ||
                $channel == ORDER_PAY_CHANNEL_UNIONPAY) {
                //订单支付成功
                $pay_time = date('Y-m-d H:i:s');
                $order = new OrderSC();
                $ret1 = $order->orderPaySuccess($order_no, $pay_time, NULL, NULL);
                if (empty($ret1)) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('订单修改失败');
                }
                if ($ret1['status'] != ERROR_NONE) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('订单修改失败');
                }
            }
            if ($channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $channel == ORDER_PAY_CHANNEL_ALIPAY_TM) {
                $api = new AlipaySC(); //支付宝接口
                $ret = $api->alipaySearch($order_no);
            }
            if ($channel == ORDER_PAY_CHANNEL_WXPAY_SM || $channel == ORDER_PAY_CHANNEL_WXPAY_TM) {
                $api = new WxpaySC(); //微信接口
                $ret = $api->wxpaySearch($order_no);
            }
            if ($ret) {
                $result = json_decode($ret, true);
                if ($result['status'] != ERROR_NONE) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception($result['errMsg']);
                }
            }

            $result['data'] = '';
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }


    /**
     * 获取素材信息
     * @param $material_id        素材id
     */
    public function getMaterial($material_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('*');
            $cmd->from(array('wq_material'));
            $cmd->where(array(
                'AND',
                'id = :id',
            ));
            $cmd->params = array(
                ':id' => $material_id,
            );
            $model = $cmd->queryRow();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /*
	 * 判断用户是否登陆
	 */
    public function checkLogin($merchant_id, $user_id)
    {
        if (!empty($user_id) && isset($user_id)) {
            $model = User::model()->findByPk($user_id);
            if (empty($model)) {
                return false;
            } else {
                if ($model['merchant_id'] == $merchant_id) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /*
	 * 
	 * 获取商户信息(优惠券页面)
	 * $merchant_id 商户id 必填
	 * */
    public function getOnlineshop($merchant_id, $encrypt_id = '')
    {
        $result = array();
        try {
            if (!empty($encrypt_id)) {
                $merchant = Merchant::model()->find('encrypt_id = :encrypt_id and flag = :flag', array(
                    ':encrypt_id' => $encrypt_id,
                    ':flag' => FLAG_NO
                ));
                $merchant_id = $merchant->id;
            }
            //获取商户信息
            $Onlineshop = Onlineshop::model()->find('merchant_id = :merchant_id', array(
                ':merchant_id' => $merchant_id
            ));
            $merchant = Merchant::model()->findByPk($merchant_id);
            $data = array();
            if ($Onlineshop) {
                $data['logo_img'] = $Onlineshop->logo_img;
                if (empty($Onlineshop->name)) {
                    $data['name'] = $merchant->name;
                } else {
                    $data['name'] = $Onlineshop->name;
                }
                $data['fuwu_name'] = $merchant->fuwu_name;
                $data['wechat_name'] = $merchant->wechat_name;
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;

            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该商户不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
	 * 判断手机号是否已注册
	 * $phonenum 手机号 必填
	 * $merchant_id 商户id 必填
	 * */
    public function IsRegist($phonenum, $merchant_id)
    {
        $result = array();
        try {
            //参数判断
            if (empty($phonenum)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少参数$phonenum');
            }
            //参数判断
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少参数$merchant_id');
            }

            $user = User::model()->find('account =:account and merchant_id =:merchant_id and flag =:flag', array(
                ':account' => $phonenum,
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            if ($user) {
                $result['data'] = true;
            } else {
                $result['data'] = false;
            }
            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {

            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
	 * 获取优惠券信息
	 * $coupon_id 优惠券id 必填
	 * */
    public function getCouponInfo($coupon_id)
    {
        $result = array();
        try {
            $coupon = Coupons::model()->findByPk($coupon_id);
            if ($coupon) {
                $data = array();
                $data['use_illustrate'] = $coupon->use_illustrate;
                $data['merchant_id'] = $coupon->merchant->id;
                $data['type'] = $coupon->type;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该优惠券不存在');
            }
        } catch (Exception $e) {

            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
	 * 获取会员优惠券信息
	 * $usercoupon_id  会员优惠券id 必填 
	 * */
    public function getUserCouponInfo($usercoupon_id)
    {
        $result = array();
        try {
            $usercoupon = UserCoupons::model()->findByPk($usercoupon_id);
            if ($usercoupon) {
                $data = array();
                $data['money'] = $usercoupon->money;
                $data['type'] = $usercoupon->coupons->type;
                $data['discount'] = $usercoupon->coupons->discount;
                $data['exchange'] = $usercoupon->coupons->exchange;
                $data['coupons_id'] = $usercoupon->coupons->id;
                $data['account'] = $usercoupon->user->account;
                $ucoupons = UserCoupons::model()->findAll('user_id = :user_id and status=:status and flag=:flag', array(
                    ':user_id' => $usercoupon->user->id,
                    'status' => COUPONS_USE_STATUS_UNUSE,
                    ':flag' => FLAG_NO
                ));
                $count = 0;
                foreach ($ucoupons as $k => $v) {
                    if ($v->coupons->type == $data['type']) {
                        $count++;
                    }
                }
                $data['coupon_num'] = $count;
                $data['user_avatar'] = $usercoupon->user->avatar;
                $data['user_alipay_avatar'] = $usercoupon->user->alipay_avatar;
                $data['start_time'] = $usercoupon->coupons->start_time;
                $data['end_time'] = $usercoupon->coupons->end_time;
                $data['use_illustrate'] = $usercoupon->coupons->use_illustrate;
                $data['merchant_id'] = $usercoupon->coupons->merchant_id;

                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该会员优惠券不存在');
            }
        } catch (Exception $e) {

            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);

    }

    /*
	 * 获取领取同一张优惠券的用户
	 * */
    public function getUserList($coupon_id)
    {
        $result = array();
        try {
            if (empty($coupon_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少参数$coupon_id');
            }
            //获取领取同一张优惠券的用户
            $usercoupon = UserCoupons::model()->findByPk($coupon_id);
            $criteria = new CDbCriteria();
            $criteria->addCondition('coupons_id = :coupons_id');
            $criteria->params[':coupons_id'] = $usercoupon->coupons_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = 'create_time DESC';
            $usercoupons = UserCoupons::model()->findAll($criteria);
            $user = array();
            foreach ($usercoupons as $k => $v) {
                if ($k == 8) {
                    break;
                }
                $user[$k]['avatar'] = $v->user->avatar;
                $user[$k]['alipay_avatar'] = $v->user->alipay_avatar;
                $user[$k]['nickname'] = $v->user->nickname;
                $user[$k]['create_time'] = $v->create_time;
                $user[$k]['name'] = $v->coupons->name;

            }
            $result['data'] = $user;
            $result['status'] = ERROR_NONE;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //通过优惠券id获取一张用户优惠券id
    public function getCouponId($coupons_id)
    {
        $result = array();
        try {
            if (empty($coupons_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少参数$coupons_id');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('coupons_id = :coupons_id');
            $criteria->params[':coupons_id'] = $coupons_id;
            $criteria->order = 'create_time DESC';
            $usercoupons = UserCoupons::model()->findAll($criteria);
            $user = array();
            foreach ($usercoupons as $k => $v) {
                if ($k == 8) {
                    break;
                }
                $user[$k]['avatar'] = $v->user->avatar;
                $user[$k]['alipay_avatar'] = $v->user->alipay_avatar;
                $user[$k]['nickname'] = $v->user->nickname;
                $user[$k]['create_time'] = $v->create_time;
                $user[$k]['name'] = $v->coupons->name;
                $user['type'] = $v->coupons->type;
                $user['merchant_id'] = $v->coupons->merchant_id;
            }
            $result['data'] = $user;
            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //获取商户的微信 appid 和 appsecret
    public function getWxapp($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少参数$merchant_id');
            }
            $merchant = Merchant::model()->findByPk($merchant_id);
            if ($merchant) {
                $data = array();
                $data['appid'] = $merchant->wechat_appid;
                $data['appsecret'] = $merchant->wechat_appsecret;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该商户不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //获取用户优惠券数量
    public function getUserCouponNum($user_id, $type)
    {
        $result = array();
        try {
            if (empty($user_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少参数$user_id');
            }
            $ucoupons = UserCoupons::model()->findAll('user_id = :user_id and status=:status and flag=:flag', array(
                ':user_id' => $user_id,
                'status' => COUPONS_USE_STATUS_UNUSE,
                ':flag' => FLAG_NO
            ));
            $count = 0;
            foreach ($ucoupons as $k => $v) {
                if ($v->coupons->type == $type) {
                    $count++;
                }
            }
            $result['data'] = $count;
            $result['status'] = ERROR_NONE;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //获取用户信息
    public function getUserInfo($user_id)
    {
        $result = array();
        try {
            $user = User::model()->findByPk($user_id);
            if ($user) {
                $userinfo = array();
                $userinfo['avatar'] = $user->avatar;
                $userinfo['alipay_avatar'] = $user->alipay_avatar;
                $result['data'] = $userinfo;
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该用户不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //获取用户收货地址
    /*
	 * $user_id 用户id 必填
	 */
    public function getUserAddress($user_id)
    {
        $result = array();
        try {
            $address = UserAddress::model()->findAll('user_id =:user_id and flag =:flag', array(
                ':user_id' => $user_id,
                ':flag' => FLAG_NO
            ));
            $data = array();
            $data['default'] = array();
            $data['list'] = array();
            foreach ($address as $k => $v) {
                $data['list'][$k]['id'] = $v->id;
                $data['list'][$k]['name'] = $v->name;
                $data['list'][$k]['tel'] = $v->tel;
                $arr = explode(',', $v->address);
                $province = ShopCity::model()->find('level =:level and code = :code', array(
                    ':level' => CITY_LEVEL_PROVINCE,
                    ':code' => $arr[0]
                ));
                $city = ShopCity::model()->find('level =:level and code = :code', array(
                    ':level' => CITY_LEVEL_CITY,
                    ':code' => $arr[1]
                ));
                $area = ShopCity::model()->find('level =:level and code = :code', array(
                    ':level' => CITY_LEVEL_AREA,
                    ':code' => $arr[2]
                ));
                $data['list'][$k]['address'] = $province->name . ' ' . $city->name . ' ' . $area->name . ' ' . $arr[3];
                $data['list'][$k]['code'] = $v->code;
                $data['list'][$k]['if_default'] = $v->if_default;
                if ($v->if_default == IF_DEFAULT_YES) {
                    $data['default']['id'] = $v->id;
                    $data['default']['name'] = $v->name;
                    $data['default']['tel'] = $v->tel;
                    $data['default']['address'] = $province->name . ' ' . $city->name . ' ' . $area->name . ' ' . $arr[3];
                    $data['default']['code'] = $v->code;
                }
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
	 * 添加地址
	 * */
    public function addAddress($user_id, $name, $tel, $province, $city, $area, $address, $code)
    {
        $result = array();
        try {
            if (empty($name)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请填写收货人');
            }

            if (empty($tel)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请填写联系方式');
            }

            if (empty($province)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请选择省');
            }

            if (empty($city)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请选择市');
            }

            if (empty($area)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请选择区');
            }

            if (empty($address)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请填写地址');
            }

            if (empty($code)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('请填写邮政编码');
            }

            $useraddress = new UserAddress();
            $useraddress->name = $name;
            $useraddress->tel = $tel;
            $useraddress->address = $province . ',' . $city . ',' . $area . ',' . $address;
            $useraddress->code = $code;
            $useraddress->user_id = $user_id;
            $useraddress->create_time = new CDbExpression('now()');;
            $address_default = UserAddress::model()->find('if_default =:if_default and flag =:flag', array(
                ':if_default' => IF_DEFAULT_YES,
                ':flag' => FLAG_NO
            ));
            if (empty($address_default)) {
                $useraddress->if_default = IF_DEFAULT_YES;
            }
            if ($useraddress->save()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('保存失败');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //修改默认地址
    public function setDefaultAddress($id)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $useraddress = UserAddress::model()->findByPk($id);
            if (!empty($useraddress)) {
                $default_Address = UserAddress::model()->find('user_id = :user_id and if_default =:if_default', array(
                    ':user_id' => $useraddress->user_id,
                    ':if_default' => IF_DEFAULT_YES
                ));
                if (!empty($default_Address)) {
                    $default_Address->if_default = IF_DEFAULT_NO;
                    if ($default_Address->update()) {

                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('默认地址修改失败');
                    }
                }
                $useraddress->if_default = IF_DEFAULT_YES;
                if ($useraddress->update()) {
                    $result['status'] = ERROR_NONE;
                    $transaction->commit(); //数据提交
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('默认地址修改失败');
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该地址不存在');
            }

        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //商城订单支付成功修改支付状态
    public function ScPaySuccess($order_no, $trade_no, $pay_channel)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $order = Order::model()->find('order_no =:order_no and flag =:flag and pay_status =:pay_status', array(
                ':order_no' => $order_no,
                ':flag' => FLAG_NO,
                ':pay_status' => ORDER_STATUS_UNPAID
            ));

            if ($order) {
                //微信卡券核销
                $coupons = new CouponsUC();
                $ret = $coupons->getWxCoupons($order->id);
                $result = json_decode($ret, true);
                if ($result['status'] != ERROR_NONE) {
                    $msg = isset($result['errMsg']) ? $result['errMsg'] : '系统内部错误';
                    throw new Exception($msg);
                }
                $list = $result['list'];
                $cardCoupons = new CardCouponsC();
                foreach ($list as $k => $v) {
                    if (empty($v)) {
                        continue;
                    }
                    //调用微信卡券核销接口
                    $ret = $cardCoupons->consumeCoupons($v['code']);
                    $result = json_decode($ret, true);
                    if ($result['status'] != ERROR_NONE) {
                        $msg = isset($result['errMsg']) ? $result['errMsg'] : '系统内部错误';
                        throw new Exception($msg);
                    }
                }

                //修改支付状态(支付状态：已支付，订单状态待发货)
                $order->pay_status = ORDER_STATUS_PAID;
                if ($order->merchant_id == TIANSHI_SHOP_API) {
                    $order->order_status = ORDER_STATUS_DELIVER;
                } else {
                    $order->order_status = ORDER_STATUS_WAITFORDELIVER;
                }

                $order->trade_no = $trade_no;
                $order->pay_channel = $pay_channel;
                $order->pay_time = date('Y-m-d H:i:s');
                if ($order->update()) {
                    $transaction->commit(); //数据提交
                    $result['data'] = $order->order_paymoney;
                    $result['status'] = ERROR_NONE;
                } else {
                    $transaction->rollback();
                    throw new Exception('订单修改失败');
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该订单不存在');
            }


        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     *
     * @param $id
     * @param $img
     */
    public function setAvatar($id, $img)
    {
        $result = array();
        try {
            $model = User::model()->findByPk($id);
            if (!empty($model)) {
                $model['avatar'] = $img;
                if ($model->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取在线预订信息
     */
    public function getOnline($merchant_id)
    {
        $result = array();
        $data = array();
        try {
            $model = Onlineshop::model()->find('merchant_id=:merchant_id and flag=:flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            if (!empty($model)) {
                $store_id = $model->store_id;// $data['store_id']
                $data['if_book'] = $model->if_book;
                //根据store_id查询具体店铺的名字
                $arr = array();
                $arr = explode(',', $store_id);
                $arr = array_filter($arr);//去掉数组空值
                $arr = array_values($arr);//数组重排序

                $data['store_id'] = $arr;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 根据storeID获取storedname
     */
    public function getOnlineStore($merchant_id, $get_store_id)
    {
        $result = array();
        $data = array();
        $arr_name = array();
        try {
            if (!empty($get_store_id)) {
                for ($i = 0; $i < count($get_store_id); $i++) {
                    $model = Store::model()->find('id=:id and merchant_id=:merchant_id and flag=:flag', array(
                        ':id' => $get_store_id[$i],
                        ':merchant_id' => $merchant_id,
                        ':flag' => FLAG_NO
                    ));
                    if (!empty($model)) {
                        $arr_name[$model->id] = $model->name;
                    }
                }

                $result['data'] = $arr_name;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * new 获取优惠券及商户信息
     */
    public function getMerchantAndCouponInfo($coupon_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('coupon.if_share, coupon.color,coupon.merchant_short_name ,merchant.appid, merchant.wechat_name, merchant.wechat_qrcode, merchant.fuwu_name, merchant.alipay_qrcode, merchant.encrypt_id, merchant.wechat_appid, merchant.wechat_appsecret, shop.logo_img, shop.name, coupon.*');
            $cmd->from(array('wq_onlineshop shop', 'wq_coupons coupon', 'wq_merchant merchant'));
            $cmd->where(array(
                'AND',
                'coupon.merchant_id = shop.merchant_id',
                'coupon.merchant_id = merchant.id',
                'coupon.id = :id',
                'coupon.flag = :flag',
            ));
            $cmd->params = array(
                ':id' => $coupon_id,
                ':flag' => FLAG_NO,
            );
            $model = $cmd->queryRow();

            if (!empty($model)) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = "";
                $result['data'] = $model;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该优惠券不存在');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 判断手机号 是否之前登陆过
     */
    public function coupeCheckLoginBefore($merchant_id, $wechat_open_id)
    {
        $result = array();
        try {
            $model = User::model()->find('merchant_id = :merchant_id and wechat_id = :wechat_id and account is not null', array(
                'merchant_id' => $merchant_id,
                'wechat_id' => $wechat_open_id
            ));
            if (!empty($model)) {
                $result['data'] = $model['id'];
                $result['tel'] = $model['account'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 判断手机号 是否之前登陆过
     */
    public function coupeCheckAliLoginBefore($merchant_id, $ali_open_id)
    {
        $result = array();
        try {
            $model = User::model()->find('merchant_id = :merchant_id and alipay_fuwu_id = :alipay_fuwu_id', array('merchant_id' => $merchant_id, 'alipay_fuwu_id' => $ali_open_id));
            if (!empty($model)) {
                $result['data'] = $model['id'];
                $result['tel'] = $model['account'];
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取用户id
     */
    public function findUserId($merchant_id, $account)
    {
        $model = User::model()->find('merchant_id = :merchant_id and account = :account', array(':merchant_id' => $merchant_id, ':account' => $account));

        return $model['id'];
    }

    /** * 领取红包/优惠券
     * @param $user_id  = ''                        用户id
     * @param $coupons_id                       优惠券id
     * @param $get_openid = ''                       领券人open_id
     * @param string $marketing_activity_type   营销活动类型
     * @param string $marketing_activity_id     营销活动id
     * @param string $wechat_code = ''          微信的code码
     * @return string
     */
    public function newReceiveCoupons($user_id = '', $coupons_id, $get_openid = '', $marketing_activity_type = '', $marketing_activity_id = '', $wechat_code = '')
    {
        $result = array();

        //创建一个数据库事务
        //$transcation = Yii::app()->db->beginTransaction();
        try {
            $coupons = Coupons::model()->findByPk($coupons_id);

            $model = new UserCoupons();
            $model['user_id'] = $user_id;
            $model['coupons_id'] = $coupons_id;
            $model['get_openid'] = $get_openid;
            $model['status'] = COUPONS_USE_STATUS_UNUSE;
            $model['wechat_code'] = $wechat_code;

            if (!empty($marketing_activity_type) && ($marketing_activity_type == MARKETING_ACTIVITY_TYPE_DMALL_SDLJ || $marketing_activity_type == MARKETING_ACTIVITY_TYPE_DMALL_ZFL)) {
                $mallActivity = MallActivity::model()->findByPk($marketing_activity_id);
                $mallActivity->receive_num++;
                if ($mallActivity->update()) {

                } else {
                    throw new Exception('营销活动更新失败');
                }
            }

            $model['marketing_activity_type'] = $marketing_activity_type;
            $model['marketing_activity_id'] = $marketing_activity_id;

            if ($coupons['time_type'] == VALID_TIME_TYPE_FIXED) {
                $model['start_time'] = $coupons['start_time'];
                $model['end_time'] = $coupons['end_time'];
            } else {
                $star_day = '+' . $coupons['start_days'] . ' ' . 'day';
                $effective_days = '+' . $coupons['effective_days'] . ' ' . 'day';

                $start_time = date('Y-m-d H:i:s', strtotime($star_day));
                $model['start_time'] = $start_time;
                $model['end_time'] = date("Y-m-d h:i:s", strtotime($start_time . $effective_days));
            }
            
            if ($coupons['type'] == COUPON_TYPE_CASH) {
                if ($coupons['money_type'] == FACE_VALUE_TYPE_RANDOM) {
                    $value_arr = explode(",", $coupons['money_random']);
                    $min_value = $value_arr[1];
                    $max_value = $value_arr[2];
                    $model ['money'] = rand($min_value, $max_value);
                } else {
                    $model['money'] = $coupons['money'];
                }
            }

            $model['create_time'] = date('Y-m-d H:i:s');
            $model['wechat_coupons_id'] = $coupons['card_id'];
            //创建优惠券核销码12位
            $code = $this->getRandChar(12);
            $usercode = UserCoupons::model()->find('code =:code', array(
                ':code' => $code
            ));
            while (!empty($usercode)) {
                $code = $this->getRandChar(12);
                $usercode = UserCoupons::model()->find('code =:code', array(
                    ':code' => $code
                ));
            }
            $model['code'] = $code;

            $coupons_model = Coupons::model()->findByPk($coupons_id);
            $coupons_model['get_num'] = $coupons_model['get_num'] + 1;

            if ($model->save()) {
                if ($coupons_model->update()) {
                    $result['data'] = $model['id'];
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                    //$transcation->commit(); //数据提交
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                    //$transcation->rollback();
                }
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                //$transcation->rollback();
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
            //$transcation->rollback();
        }

        return json_encode($result);
    }

    /**
     * 活动赠送优惠券
     * @param $user_id       用户id
     * @param $coupons_id    优惠券id
     * @param $activity_id   活动id
     * @param $activity_type 活动类型
     */
    public function sendCoupons($user_id, $coupons_id, $marketing_activity_type = '', $marketing_activity_id = '')
    {
        $result = array();

        //创建一个数据库事务
        $transcation = Yii::app()->db->beginTransaction();
        try {
            //参数验证
            //TODO
            $coupons = Coupons::model()->findByPk($coupons_id);

            $model = new UserCoupons();
            $model['user_id'] = $user_id;
            $model['coupons_id'] = $coupons_id;
            $model['status'] = COUPONS_USE_STATUS_UNUSE;
            if ($coupons['time_type'] == VALID_TIME_TYPE_FIXED) {
                $model['start_time'] = $coupons['start_time'];
                $model['end_time'] = $coupons['end_time'];
            } else {
                $star_day = '+' . $coupons['start_days'] . ' ' . 'day';
                $effective_days = '+' . $coupons['effective_days'] . ' ' . 'day';

                $start_time = date('Y-m-d H:i:s', strtotime($star_day));
                $model['start_time'] = $start_time;
                $model['end_time'] = date("Y-m-d h:i:s", strtotime($start_time . $effective_days));
            }

            if ($coupons['type'] == COUPON_TYPE_CASH) {
                if ($coupons['money_type'] == FACE_VALUE_TYPE_RANDOM) {
                    $value_arr = explode(",", $coupons['money_random']);
                    $min_value = $value_arr[1];
                    $max_value = $value_arr[2];
                    $model ['money'] = rand($min_value, $max_value);
                } else {
                    $model['money'] = $coupons['money'];
                }
            }

            $model['create_time'] = date('Y-m-d H:i:s');
            $model['wechat_coupons_id'] = $coupons['card_id'];
            //创建优惠券核销码12位
            $code = $this->getRandChar(12);
            $usercode = UserCoupons::model()->find('code =:code', array(
                ':code' => $code
            ));
            while (!empty($usercode)) {
                $code = $this->getRandChar(12);
                $usercode = UserCoupons::model()->find('code =:code', array(
                    ':code' => $code
                ));
            }
            $model['code'] = $code;
            $model['marketing_activity_id'] = $marketing_activity_id;
            $model['marketing_activity_type'] = $marketing_activity_type;

            $coupons_model = Coupons::model()->findByPk($coupons_id);
            $coupons_model['get_num'] = $coupons_model['get_num'] + 1;

            if ($model->save()) {

                if ($coupons_model->update()) {
                    $result['data'] = $model['id'];
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $transcation->commit(); //数据提交
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                    $transcation->rollback();
                }
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $transcation->rollback();
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
            $transcation->rollback();
        }

        return json_encode($result);
    }

    /**
     *检查用户是否领过某活动的券了
     * @param $user_id       用户id
     * @param $activity_id       活动id
     */
    public function haveGet($user_id, $marketing_activity_id, $marketing_activity_type)
    {
        $model = UserCoupons::model()->findAll('user_id = :user_id and marketing_activity_id=:marketing_activity_id and marketing_activity_type=:marketing_activity_type and flag =:flag', array(
            ':user_id' => $user_id,
            ':marketing_activity_id' => $marketing_activity_id,
            ':marketing_activity_type' => $marketing_activity_type,
            ':flag' => FLAG_NO,

        ));
        return $model;

    }


    /**
     * 获取优惠券领取信息
     */
    public function getCouponRecord($coupon_id)
    {
        $result = array();
        try {
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('user.avatar, user.nickname, user_coupons.create_time, user_coupons.money, coupons.discount, coupons.type, coupons.title');
            $cmd->from(array('wq_user_coupons user_coupons', 'wq_user user', 'wq_coupons coupons'));
            $cmd->where(array(
                'AND',
                'coupons.id = :coupon_id',
                'user_coupons.coupons_id = coupons.id',
                'user_coupons.user_id = user.id',
            ));
            $cmd->order = 'user_coupons.last_time DESC';
            $cmd->params = array(
                ':coupon_id' => $coupon_id,
            );
            $model = $cmd->queryAll();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /*
     *获取用户优惠券详情 
     * */
    public function getNewUserCouponInfo($usercouponid)
    {
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('coupon.if_share, coupon.color, merchant.encrypt_id, merchant.wechat_appid, merchant.wechat_appsecret, shop.logo_img, shop.name, coupon.*, user.*');
            $cmd->from(array('wq_onlineshop shop', 'wq_coupons coupon', 'wq_merchant merchant', 'wq_user_coupons user'));
            $cmd->where(array(
                'AND',
                'coupon.merchant_id = shop.merchant_id',
                'coupon.merchant_id = merchant.id',
                'coupon.id = user.coupons_id',
                'user.id = :id',
                'coupon.flag = :flag',
            ));
            $cmd->params = array(
                ':id' => $usercouponid,
                ':flag' => FLAG_NO,
            );
            $model = $cmd->queryRow();

            if (!empty($model)) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = "";
                $result['data'] = $model;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该优惠券不存在');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 领取优惠券之后更改优惠券code
     * @param $card_id              微信卡券id
     * @param $code                 code码
     * @param $friendUserName       赠送方OpenID
     * @param $IsGiveByFriend       是否为转赠，1代表是，0代表否
     * @param $OldUserCardCodee     转赠前的code序列号
     * @param $OuterId              领取场景
     * @param $fromUsername         领券方OpenID
     */
    public function changeCode($card_id, $code, $friendUserName, $IsGiveByFriend, $OldUserCardCodee, $OuterId, $fromUsername)
    {

        $result = array();
        //创建一个数据库事务
        $transcation = Yii::app()->db->beginTransaction();
        try {
            if ($IsGiveByFriend === 1) { //转赠卡券
                $model = UserCoupons::model()->find('code = :code order by last_time DESC', array(':code' => $OldUserCardCodee));

                if (!empty($model)) {
                    $model['status'] = COUPONS_USE_STATUS_GAVE;
                    if ($model->save()) {
                    } else {
                        $transcation->rollback();
                    }

                    $coupon_model = Coupons::model()->findByPk($model['coupons_id']);
                    $merchant_id = $coupon_model['merchant_id'];
                }
                $user_model = User::model()->find('merchant_id = :merchant_id and wechat_id = :wechat_id', array(':merchant_id' => $merchant_id, ':wechat_id' => $fromUsername));

                $usr_id = $user_model['id'];
                $new_user_model = new UserCoupons();
                $new_user_model->attributes = $model->attributes;
                $new_user_model['status'] = COUPONS_USE_STATUS_UNUSE;
                $new_user_model['user_id'] = !empty($user_model['id']) ? $user_model['id'] : '';
                $new_user_model['if_give'] = COUPONS_GIVE_YES;
                $new_user_model['get_openid'] = $fromUsername;
                $new_user_model['give_openid'] = $friendUserName;
                $new_user_model['before_code'] = $OldUserCardCodee;
                $new_user_model['code'] = $code;
                $new_user_model['scene'] = $OuterId;
                $new_user_model['flag'] = FLAG_NO;
                $new_user_model['create_time'] = date('Y-m-d h:i:s');
                $new_user_model['last_time'] = date('Y-m-d h:i:s');
                $new_user_model['if_wechat'] = COUPONS_IF_WECHAT_YES;
                if ($new_user_model->save()) {
                    $result['status'] = ERROR_NONE;
                    $transcation->commit(); //数据提交
                } else {
                    $transcation->rollback();
                }

            } else { // 非转赠
                if ($OuterId > 0 && !empty($OuterId)) { //同步微信卡包
                    //保存微信code码
                    $user_coupon = UserCoupons::model()->findByPk($OuterId);
                    $user_coupon->wechat_code = $code;
                    $res = $user_coupon->save();
                    if ($res) {
                        $transcation->commit(); //数据提交
                    }
                } else { //领取微信原生券
                    //查找玩券信息
                    $couponSC = new MobileCouponsSC();
                    $res_coupon = json_decode($couponSC->getCouponByCardId($card_id), true);

                    if ($res_coupon['status'] == ERROR_NONE) {
                        $coupon_id = $res_coupon['data']['id'];
                        $merchant_id = $res_coupon['data']['merchant_id'];
                    }

                    //查找用户
                    $res_user = json_decode($this->getUserByOpenid($fromUsername, '', $merchant_id), true);
                    if ($res_user['status'] == ERROR_NONE) {
                        $user_id = $res_user['data']['id'];
                    } else {
                        $res_fans = json_decode($this->getNewFansByOpenid($fromUsername, '', $merchant_id), true);
                        if ($res_fans['status'] == ERROR_NONE) {
                            $user_id = $res_fans['data']['id'];
                        }
                    }

                    //新增领取优惠券记录
                    $res_getcoupon = json_decode($this->newReceiveCoupons($user_id, $coupon_id, $fromUsername, '', '', $code), true);

                    if ($res_getcoupon['status'] == ERROR_NONE) {
                        $result['status'] = ERROR_NONE;
                        $transcation->commit(); //数据提交
                    } else {
                        $transcation->rollback(); //数据回滚
                    }
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
            $transcation->rollback();
        }
    }

    public function getStoreName($merchant_id)
    {
        $result = array();
        $data = array();
        try {
            $store_model = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if (!empty($store_model)) {
                foreach ($store_model as $key => $value) {
                    $data[$value['id']] = $value['name'];
                }
            }

            $store_num = Store::model()->count('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));

            $result['status'] = ERROR_NONE;
            $result['errMsg'] = "";
            $result['data'] = $data;
            $result['num'] = $store_num;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 用户管理列表
     * @param type $merchant_id
     * @param type $from
     * @param type $group_id
     * @param type $keyword
     * @param type $sex
     * @param type $grade
     * @param type $time
     * @param type $time1
     * @param type $time2
     * @param type $fans
     * @param type $liveplaceProvince
     * @param type $liveplaceCity
     * @param type $sort
     * @param type $Focus
     * @param type $integral
     * @param type $consumption
     * @param type $Ability
     * @param type $age_min
     * @param type $age_max
     * @param type $unknown
     * @return type
     */
    public function getGroupManageList($merchant_id, $from, $group_id, $keyword, $sex, $grade, $time, $time1, $time2, $fans, $liveplaceProvince, $liveplaceCity, $sort, $Focus, $integral, $consumption, $Ability, $age_min, $age_max, $unknown = '')
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            //商户id
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            //年龄段
            if (!empty($age_min) && !empty($age_max)) {
                $criteria->addBetweenCondition('birthday', $age_max, $age_min);
            }
            //消费能力
            if (!empty($Ability) && $Ability == 'desc') {
                $criteria->order = '';
            }
            //消费时间
            if (!empty($consumption) && $consumption == 'desc') {
                $criteria->order = 'order.pay_time desc';
            }
            //积分
            if (!empty($integral) && $integral == 'desc') {
                $criteria->order = 'points desc';
            }
            //关注时间
            if (!empty($Focus) && $Focus == 'desc') {
                $criteria->order = 'alipay_subscribe_time desc or wechat_subscribe_time desc';
            }
            //年龄
            if (!empty($sort) && $sort == 'desc') {
                $criteria->order = 'birthday asc';
            }
            //省
            if (!empty($liveplaceProvince) && empty($liveplaceCity)) {
                $sql = "(province like '%$liveplaceProvince%') or (alipay_province like '$liveplaceProvince') or (wechat_province like '$liveplaceProvince')";
                $criteria->addCondition($sql);
            }
            //省市
            if (!empty($liveplaceProvince) && !empty($liveplaceCity)) {
                $sql = "(province like '$liveplaceProvince' and city like '$liveplaceCity') or (alipay_province like '$liveplaceProvince' and alipay_city like '$liveplaceCity') or (wechat_province like '$liveplaceProvince' and wechat_city like '$liveplaceCity')";
                $criteria->addCondition($sql);
            }
            //根据来源   
            if (!empty($from)) {
                foreach ($from as $k => $v) {
                    if (empty($v) || $v == ',') {
                        continue;
                    }
                    $criteria->addCondition('t.from LIKE :from' . $v);
                    $criteria->params[':from' . $v] = '%' . ',' . $v . ',' . '%';
                }
            }
            //普通分组
            if (!empty($group_id)) {
                $criteria2 = new CDbCriteria();
                $criteria2->addInCondition('id', $group_id);
                $criteria2->addcondition('flag=:flag and merchang_id=:merchant_id');
                $criteria2->params[':flag'] = FLAG_NO;
                $criteria2->params[':merchant_id'] = $merchant_id;
                $group = Group::model()->findall($criteria2);
                $id = array();
                foreach ($group as $v) {
                    $id[] = $v['user_id'];
                }
                $criteria->addInCondition('id', $id);
            }
            //会员等级
            if (!empty($grade)) {
                $criteria->addInCondition('membershipgrade_id', $grade);
            }
            //根据关键字
            if (!empty($keyword)) {
                $criteria->addCondition("account = :account or name = :name or nickname = :nickname or alipay_nickname=:alipay_nickname or wechat_nickname=:wechat_nickname");
                $criteria->params[':account'] = $keyword;
                $criteria->params[':name'] = $keyword;
                $criteria->params[':nickname'] = $keyword;
                $criteria->params[':wechat_nickname'] = $keyword;
                $criteria->params[':alipay_nickname'] = $keyword;
            }
            //根据性别
            if (!empty($sex) && !empty($unknown) && $unknown == 'unknown') {
                $criteria->addInCondition('sex', $sex, 'and');
                $criteria->addCondition('sex is null', 'or');
            } else {
                if (!empty($sex)) {
                    $criteria->addInCondition('sex', $sex);
                }
                if (!empty($unknown) && $unknown == 'unknown') {
                    $criteria->addCondition('sex is null');
                }
            }
            //粉丝
            if (!empty($fans)) {
                $criteria->addcondition('type=:type1 or type=:type2');
                $criteria->params[':type1'] = USER_TYPE_WECHAT_FANS;
                $criteria->params[':type2'] = USER_TYPE_ALIPAY_FANS;
            }
            //服务窗关注时间
            if (!empty($time)) {
                $arr_time = explode('-', $time);
                $start_time = date('Y-m-d' . ' 00:00:00', strtotime($arr_time[0]));
                $end_time = date('Y-m-d' . ' 23:59:59', strtotime($arr_time[1]));
                $criteria->addCondition('alipay_subscribe_time>=:start_time and alipay_subscribe_time<=:end_time');
                $criteria->params[':start_time'] = $start_time;
                $criteria->params[':end_time'] = $end_time;
            }
            //公众号关注时间
            if (!empty($time1)) {
                $arr_time = explode('-', $time1);
                $start_time = date('Y-m-d' . ' 00:00:00', strtotime($arr_time[0]));
                $end_time = date('Y-m-d' . ' 23:59:59', strtotime($arr_time[1]));
                $criteria->addCondition('wechat_subscribe_time>=:start_time and wechat_subscribe_time<=:end_time');
                $criteria->params[':start_time'] = $start_time;
                $criteria->params[':end_time'] = $end_time;
            }
            //注册会员时间
            if (!empty($time2)) {
                $arr_time = explode('-', $time2);
                $start_time = date('Y-m-d' . ' 00:00:00', strtotime($arr_time[0]));
                $end_time = date('Y-m-d' . ' 23:59:59', strtotime($arr_time[1]));
                $criteria->addCondition('regist_time>=:start_time and regist_time<=:end_time');
                $criteria->params[':start_time'] = $start_time;
                $criteria->params[':end_time'] = $end_time;
            }
            //分页
            $pages = new CPagination(User::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $model = User::model()->findAll($criteria);

            //数据封装
            $data = array('list' => array());
            foreach ($model as $key => $value) {
                $data['list'][$key]['id'] = $value['id']; //会员id
                $data['list'][$key]['type'] = $value['type'];//用户类型
                $data['list'][$key]['account'] = $value['account']; //会员账号
                $data['list'][$key]['avatar'] = $value['avatar']; //会员头像
                $data['list'][$key]['name'] = $value['name']; //会员名称
                $data['list'][$key]['nickname'] = $value['nickname'];//昵称
                $data['list'][$key]['sex'] = $value['sex'];//性别
                $data['list'][$key]['birthday'] = !empty($value['birthday']) ? date('Y') - date('Y', strtotime($value['birthday'])) + 1 : '';//年龄
                $data['list'][$key]['points'] = $value['points'];//会员积分
                $data['list'][$key]['grade_id'] = $value['membershipgrade_id']; //会员等级id
                $data['list'][$key]['address'] = $value['address'];//地址
                $data['list'][$key]['alipay_avatar'] = $value['alipay_avatar'];//支付宝服务窗头像
                $data['list'][$key]['alipay_nickname'] = $value['alipay_nickname'];//支付宝用户昵称
                $data['list'][$key]['alipay_province'] = $value['alipay_province'];//支付宝用户注册所填省份
                $data['list'][$key]['alipay_city'] = $value['alipay_city'];//支付宝用户注册所填城市
                $data['list'][$key]['alipay_gender'] = $value['alipay_gender'];//支付宝用户性别
                $data['list'][$key]['alipay_user_type_value'] = $value['alipay_user_type_value'];//支付宝用户类型
                $data['list'][$key]['alipay_is_licence_auth'] = $value['alipay_is_licence_auth'];//支付宝用户是否经过营业执照认证
                $data['list'][$key]['alipay_is_certified'] = $value['alipay_is_certified'];//支付宝用户是否通过实名认证
                $data['list'][$key]['alipay_certified_grade_a'] = $value['alipay_certified_grade_a'];//支付宝用户是否A类认证
                $data['list'][$key]['alipay_is_student_certified'] = $value['alipay_is_student_certified'];//支付宝用户是否是学生
                $data['list'][$key]['alipay_is_bank_auth'] = $value['alipay_is_bank_auth'];//支付宝用户是否经过银行卡认证
                $data['list'][$key]['alipay_is_mobile_auth'] = $value['alipay_is_mobile_auth'];//支付宝用户是否经过手机认证
                $data['list'][$key]['alipay_user_status'] = $value['alipay_user_status'];//支付宝用户状态
                $data['list'][$key]['alipay_subscribe_time'] = $value['alipay_subscribe_time'];//支付宝用户关注时间
                $data['list'][$key]['alipay_cancel_subscribe_time'] = $value['alipay_cancel_subscribe_time'];//支付宝用户取消关注时间
                $data['list'][$key]['alipay_subscribe_store_id'] = $value['alipay_subscribe_store_id'];//支付宝用户关注入口门店
                $data['list'][$key]['register_address'] = $value['register_address'];//注册地址（省,市）
                $data['list'][$key]['wechat_status'] = $value['wechat_status'];//微信用户关注状态
                $data['list'][$key]['wechat_nickname'] = $value['wechat_nickname'];//微信用户昵称
                $data['list'][$key]['wechat_sex'] = $value['wechat_sex'];//微信用户性别
                $data['list'][$key]['wechat_country'] = $value['wechat_country'];//微信用户所在国家
                $data['list'][$key]['wechat_province'] = $value['wechat_province'];//微信用户所在省份
                $data['list'][$key]['wechat_city'] = $value['wechat_city'];//微信用户所在城市
                $data['list'][$key]['wechat_headimgurl'] = $value['wechat_headimgurl'];//微信用户头像
                $data['list'][$key]['wechat_groupid'] = $value['wechat_groupid'];//微信用户所在分组id
                $data['list'][$key]['wechat_subscribe_time'] = $value['wechat_subscribe_time'];//微信用户关注时间
                $data['list'][$key]['wechat_cancel_subscribe_time'] = $value['wechat_cancel_subscribe_time'];//微信用户取消关注时间
                $data['list'][$key]['wechat_subscribe_store_id'] = $value['wechat_subscribe_store_id'];//微信用户关注入口门店
                $data['list'][$key]['alipay_status'] = $value['alipay_status'];//支付宝用户关注状态
                $data['list'][$key]['province'] = $value->province;//省份
                $data['list'][$key]['city'] = $value->city;//城市
                //用户最近消费
                $criteria1 = new CDbCriteria();
                $criteria1->order = 'pay_time desc';
                $criteria1->addcondition('flag=:flag and user_id=:user_id and merchant_id=:merchant_id');
                $criteria1->params[':flag'] = FLAG_NO;
                $criteria1->params[':user_id'] = $value['id'];
                $criteria1->params[':merchant_id'] = $merchant_id;
                $order = Order::model()->find($criteria1);
                if ($order) {
                    $data['list'][$key]['pay_time'] = $order['pay_time'];
                    $data['list'][$key]['store_name'] = $order->store->name;
                } else {
                    $data['list'][$key]['pay_time'] = '';
                    $data['list'][$key]['store_name'] = '';
                }
                //微信最近消费
                $criteria3 = new CDbCriteria();
                $criteria3->order = 'pay_time desc';
                $criteria3->addcondition('flag=:flag and wechat_user_id=:wechat_user_id and merchant_id=:merchant_id');
                $criteria3->params[':flag'] = FLAG_NO;
                $criteria3->params[':wechat_user_id'] = $value['wechat_id'];
                $criteria3->params[':merchant_id'] = $merchant_id;
                $order1 = Order::model()->find($criteria3);
                if ($order1) {
                    $data['list'][$key]['wechat_pay_time'] = $order1['pay_time'];
                    $data['list'][$key]['wechat_store_name'] = $order1->store->name;
                } else {
                    $data['list'][$key]['wechat_pay_time'] = '';
                    $data['list'][$key]['wechat_store_name'] = '';
                }
                //支付宝最近消费
                $criteria4 = new CDbCriteria();
                $criteria4->order = 'pay_time desc';
                $criteria4->addcondition('flag=:flag and alipay_user_id=:alipay_user_id and merchant_id=:merchant_id');
                $criteria4->params[':flag'] = FLAG_NO;
                $criteria4->params[':alipay_user_id'] = $value['alipay_fuwu_id'];
                $criteria4->params[':merchant_id'] = $merchant_id;
                $order2 = Order::model()->find($criteria4);
                if ($order2) {
                    $data['list'][$key]['alipay_pay_time'] = $order2['pay_time'];
                    $data['list'][$key]['alipay_store_name'] = $order2->store->name;
                } else {
                    $data['list'][$key]['alipay_pay_time'] = '';
                    $data['list'][$key]['alipay_store_name'] = '';
                }

                //查询会员等级名称
                $grade = UserGrade::model()->findByPk($value['membershipgrade_id']);
                if (empty($grade)) {
                    $data['list'][$key]['grade_name'] = '无'; //会员等级名称
                } else {
                    $data['list'][$key]['grade_name'] = $grade['name']; //会员等级名称
                }

                $data['list'][$key]['from'] = $value['from']; //会员来源
                $data['list'][$key]['regist_time'] = $value['regist_time'];//注册时间                           
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 用户分组
     */
    public function getManageList($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $data = array();
            //会员等级
            $grade = UserGrade::model()->findAll(array(
                'condition' => 'merchant_id = :merchant_id and flag = :flag',
                'order' => 'points_rule asc',
                'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO)
            ));
            if ($grade) {
                foreach ($grade as $key => $value) {
                    $data['grade'][$key]['id'] = $value['id'];
                    $data['grade'][$key]['name'] = $value['name'];
                }
            } else {
                $data['grade'] = '';
            }
            //自定义分组
            $group = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
                array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if ($group) {
                foreach ($group as $k => $v) {
                    $data['group'][$k]['id'] = $v['id'];
                    $data['group'][$k]['name'] = $v['name'];
                }
            } else {
                $data['group'] = '';
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 对用户重新分组
     * @param unknown $merchant_id
     * @param unknown $operation
     * @param unknown $user
     * @param unknown $old_group
     * @param unknown $new_group
     * @throws Exception
     * @return string
     */
    public function regroupUser($merchant_id, $operation, $user, $old_group, $new_group)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction(); //开启事务
        try {
            //参数验证
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            if (empty($operation)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数operation不能为空');
            }
            if (empty($user)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数user不能为空');
            }
            if (empty($old_group)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数old_group不能为空');
            }
            if (empty($new_group)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数new_group不能为空');
            }
            //参数处理
            $list = explode(",", $user);

            $arr = explode("-", $old_group);
            $old_type = reset($arr); //源分组类型
            $old_id = end($arr); //源分组id

            $arr = explode("-", $new_group);
            $new_type = reset($arr); //目标分组类型
            $new_id = end($arr); //目标分组id

            //分组id非空检查
            if (empty($old_id) || empty($new_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('错误的分组参数');
            }

            $modify = false;
            $add = false;
            $delete = false;

            //目标分组类型判断
            if ($new_type == 'grade') {
                if ($old_type != 'grade') {
                    $result['status'] = ERROR_PARAMETER_FORMAT;
                    throw new Exception('无法将原分组会员添加或移动到该分组下');
                }
                if ($operation != 'move') {
                    $result['status'] = ERROR_PARAMETER_FORMAT;
                    throw new Exception('无法进行添加操作');
                }
                //修改标识
                $modify = true;
            } elseif ($new_type == 'default') {
                if ($old_type != 'custom') {
                    $result['status'] = ERROR_PARAMETER_FORMAT;
                    throw new Exception('无法将原分组会员添加或移动到该分组下');
                }
                if ($operation != 'move') {
                    $result['status'] = ERROR_PARAMETER_FORMAT;
                    throw new Exception('无法进行添加操作');
                }
                //删除原分组标识
                $delete = true;
            } elseif ($new_type == 'custom') {
                if ($operation == 'add') {
                    if ($old_type != 'all' && $old_type != 'grade' && $old_type != 'custom') {
                        $result['status'] = ERROR_PARAMETER_FORMAT;
                        throw new Exception('无法进行添加操作');
                    }
                    //添加标识
                    $add = true;
                } elseif ($operation == 'move') {
                    if ($old_type != 'custom' && $old_type != 'default') {
                        $result['status'] = ERROR_PARAMETER_FORMAT;
                        throw new Exception('无法进行移动操作');
                    }
                    if ($old_type == 'custom') {
                        //删除标识
                        $delete = true;
                    }
                    //添加标识
                    $add = true;
                } else {
                    $result['status'] = ERROR_PARAMETER_FORMAT;
                    throw new Exception('无效的操作');
                }
            } else {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('无效的操作分组');
            }

            if ($modify) {
                //查询会员等级信息
                $grade = UserGrade::model()->find('merchant_id = :merchant_id and flag = :flag and id = :id',
                    array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':id' => $new_id));
                if (empty($grade)) {
                    $result['status'] = ERROR_NO_DATA;
                    throw new Exception('会员等级(' . $new_id . ')不存在，无法进行移动操作');
                }
                foreach ($list as $k => $v) {
                    //查询会员信息
                    $model = User::model()->find('merchant_id = :merchant_id and id = :id',
                        array(':merchant_id' => $merchant_id, ':id' => $v));
                    if (empty($model)) {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('会员(' . $v . ')不存在，无法进行移动操作');
                    }
                    //比较两个等级的高低（积分要求的高低）
                    $old_grade = UserGrade::model()->find('merchant_id = :merchant_id and flag = :flag and id = :id',
                        array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':id' => $old_id));
                    if (empty($old_grade)) {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('会员等级(' . $old_id . ')不存在，无法进行移动操作');
                    }
                    //从高会员等级移到低会员等级且会员受积分限制时，禁止移动
                    if ($grade['points_rule'] < $old_grade['points_rule'] && $model['switch'] == POINTS_LIMIT) {
                        $result['status'] = ERROR_EXCEPTION;
                        throw new Exception('会员(' . $v . ')无法向低的会员等级移动');
                    }
                    //从高会员等级移到低会员等级且会员不受积分限制时，根据会员当前积分设置相应的会员等级
                    if ($grade['points_rule'] < $old_grade['points_rule'] && $model['switch'] == POINTS_LIMIT_NO) {
                        $new_grade = UserGrade::model()->find(array(
                            'condition' => 'merchant_id = :merchant_id and flag = :flag and points_rule <= :points',
                            'order' => 'points_rule desc',
                            'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':points' => $model['points'])
                        ));
                        if (empty($new_grade)) {
                            $result['status'] = ERROR_NO_DATA;
                            throw new Exception('系统内部错误');
                        }
                        //修改会员等级，修改移动标识
                        $model['membershipgrade_id'] = $new_grade['id'];
                        $model['switch'] = POINTS_LIMIT;
                    }

                    //从低会员等级移到高会员等级
                    if ($grade['points_rule'] > $old_grade['points_rule']) {
                        //修改会员等级，修改移动标识
                        $model['membershipgrade_id'] = $new_id;
                        $model['switch'] = POINTS_LIMIT_NO;
                    }

                    if (!$model->save()) {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('会员等级修改失败');
                    }
                }
            }
            if ($delete) {
                //删除会员所属原分组
                foreach ($list as $k => $v) {
                    //查询会员信息
                    $model = User::model()->find('merchant_id = :merchant_id and id = :id',
                        array(':merchant_id' => $merchant_id, ':id' => $v));
                    if (empty($model)) {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('会员(' . $v . ')不存在，无法进行移动操作');
                    }
                    $tmp = $model['group_id'];
                    $tmp = str_replace("," . $old_id . ",", "", $tmp);
                    $model['group_id'] = $tmp;
                    if (!$model->save()) {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('数据修改失败(error:delete)');
                    }
                }
            }
            if ($add) {
                //添加会员新的分组
                foreach ($list as $k => $v) {
                    //查询会员信息
                    $model = User::model()->find('merchant_id = :merchant_id and id = :id',
                        array(':merchant_id' => $merchant_id, ':id' => $v));
                    if (empty($model)) {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('会员(' . $v . ')不存在，无法进行添加操作');
                    }
                    $tmp = $model['group_id'];
                    $tmp .= ',' . $new_id . ',';
                    $model['group_id'] = $tmp;
                    if (!$model->save()) {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('数据修改失败(error:add)');
                    }
                }
            }

            $transaction->commit(); //数据提交

            $result['data'] = '';
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取可添加分组和可移动分组
     * @param unknown $merchant_id
     * @param unknown $from_group_type
     * @param unknown $from_group_id
     * @throws Exception
     * @return string
     */
    public function getOperationGroupList($merchant_id, $group_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //分组参数处理
            $arr = explode("-", $group_id);
            $type = reset($arr);
            $group_id = end($arr);
            //添加到列表
            $add = array();
            //移动到列表
            $move = array();
            if ($type == 'all') { //源分组为全部会员，添加列表为自定义分组，移动列表为空
                $custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
                    array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
                foreach ($custom as $k => $v) {
                    $add['custom-' . $v['id']] = $v['name'];
                }
            } elseif ($type == 'default') { //源分组为未分组，添加列表为空，移动列表为自定义分组
                $custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
                    array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
                foreach ($custom as $k => $v) {
                    $move['custom-' . $v['id']] = $v['name'];
                }
            } elseif ($type == 'grade') { //源分组为会员等级分组，添加列表为自定义分组，移动列表为会员等级分组
                $custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
                    array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
                foreach ($custom as $k => $v) {
                    $add['custom-' . $v['id']] = $v['name'];
                }
                $grade = UserGrade::model()->findAll('merchant_id = :merchant_id and flag = :flag and id != :id',
                    array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':id' => $group_id));
                foreach ($grade as $k => $v) {
                    $move['grade-' . $v['id']] = $v['name'];
                }
            } elseif ($type == 'custom') { //源分组为自定义分组列表，添加列表为自定义分组，移动列表为自定义分组和未分组
                $custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag and id != :id',
                    array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':id' => $group_id));
                foreach ($custom as $k => $v) {
                    $add['custom-' . $v['id']] = $v['name'];
                    $move['custom-' . $v['id']] = $v['name'];
                }
                $k = count($move);
                $move['default-' . USER_GROUP_DEFAULT] = '未分组';
            } else { //不存在的分组类型
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('错误的分组类型');
            }
            $data = array('add' => $add, 'move' => $move);
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 积分规则
     * @param type $merchant_id
     * @return type
     * @throws Exception
     */
    public function IntegrationRule($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $points_rule = PointsRule::model()->findall('flag=:flag and merchant_id=:merchant_id', array(
                ':flag' => FLAG_NO,
                ':merchant_id' => $merchant_id
            ));
            $data = array();
            if ($points_rule) {
                foreach ($points_rule as $k => $v) {
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['name'] = $v['name'];
                    $data[$k]['cycle'] = $v['cycle'];
                    $data[$k]['num'] = $v['num'];
                    $data[$k]['condition'] = $v['condition'];
                    $data[$k]['points'] = $v['points'];
                    $data[$k]['if_storedpay_get_points'] = $v['if_storedpay_get_points'];
                    $data[$k]['create_time'] = $v['create_time'];
                    $data[$k]['last_time'] = $v['last_time'];
                }
                $result['data'] = $data;
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 编辑积分规则
     * @param type $merchant_id
     * @param type $id
     * @param type $period
     * @param type $num
     * @param type $condition
     * @param type $points
     * @param type $if_storedpay_get_points
     */
    public function EditIntegrationRule($merchant_id, $id, $period, $num, $condition, $points, $if_storedpay_get_points)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            if (empty($id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数id不能为空');
            }
            if (empty($period)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数period不能为空');
            }
            $points_rule = PointsRule::model()->find('flag=:flag and merchant_id=:merchant_id and id=:id', array(
                ':flag' => FLAG_NO,
                ':merchant_id' => $merchant_id,
                ':id' => $id
            ));
            if ($points_rule) {
                $points_rule->cycle = $period;
                $points_rule->num = $num;
                if (!empty($condition)) {
                    $points_rule->condition = $condition;
                }
                if (!empty($points)) {
                    $points_rule->points = $points;
                }
                if (!empty($if_storedpay_get_points)) {
                    $points_rule->if_storedpay_get_points = $if_storedpay_get_points;
                }
                if ($points_rule->update()) {
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //用户详情
    /**
     * merchantId 商户id
     * account   账号
     * id       会员id
     */
    public function UserDetail($merchantId, $id)
    {
        //返回结果
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $data = array();
            if (!empty($id)) {
                $user = User::model()->find('id=:id and merchant_id=:merchant_id and flag=:flag', array(':id' => $id, ':merchant_id' => $merchantId, ':flag' => FLAG_NO));
                if ($user) {
                    $data['type'] = $user->type;//用户类型 1会员 2微信粉丝 3支付宝粉丝
                    $data['account'] = $user->account;//账号
                    $data['avatar'] = $user->avatar;//头像
                    $data['nickname'] = $user->nickname;//昵称                
                    $data['name'] = $user->name;//真实姓名
                    $data['sex'] = $user->sex;//性别
                    $data['birthday'] = $user->birthday;//生日
                    $data['social_security_number'] = $user->social_security_number;//身份证
                    $data['email'] = $user->email;//邮箱
                    $data['marital_status'] = $user->marital_status;//婚姻状况
                    $data['work'] = $user->work;//工作
                    $data['free_secret'] = $user->free_secret;//小额免密金额
                    $data['money'] = $user->money;//储值金额
                    $data['points'] = $user->points;//会员积分
                    $data['membershipgrade_id'] = $user->membershipgrade_id;//会员等级id
                    //查询会员等级名称
                    $grade = UserGrade::model()->findByPk($user['membershipgrade_id']);
                    if (empty($grade)) {
                        $data['grade_name'] = '无'; //会员等级名称
                    } else {
                        $data['grade_name'] = $grade['name']; //会员等级名称
                    }
                    $data['membership_card_no'] = $user->membership_card_no;//会员卡号
                    $data['login_time'] = $user->login_time;//最后登录时间
                    $data['login_ip'] = $user->login_ip;//最后登录ip
                    $data['regist_time'] = $user->regist_time;//注册时间
                    $data['address'] = $user->address;//地址
                    $data['from'] = $user->from;//来源(多个来源)
                    $data['alipay_fuwu_id'] = $user->alipay_fuwu_id;//服务窗账号id
                    $data['alipay_status'] = $user->alipay_status;//支付宝用户关注状态
                    $data['alipay_avatar'] = $user->alipay_avatar;//支付宝服务窗头像
                    $data['alipay_nickname'] = $user->alipay_nickname;//支付宝用户昵称
                    $data['alipay_province'] = $user->alipay_province;//支付宝用户注册所填省份
                    $data['alipay_city'] = $user->alipay_city;//支付宝用户注册所填城市
                    $data['alipay_gender'] = $user->alipay_gender;//支付宝用户性别 M男性 F女性
                    $data['alipay_user_type_value'] = $user->alipay_user_type_value;//支付宝用户类型 1公司账号 2个人账号
                    $data['alipay_is_licence_auth'] = $user->alipay_is_licence_auth;//支付宝用户是否经过营业执照认证 T通过 F没有通过
                    $data['alipay_is_certified'] = $user->alipay_is_certified;//支付宝用户是否通过实名认证 T通过 F没有实名认证
                    $data['alipay_certified_grade_a'] = $user->alipay_certified_grade_a;//支付宝用户是否A类认证 T是A类认证 F非A类认证
                    $data['alipay_is_student_certified'] = $user->alipay_is_student_certified;//支付宝用户是否是学生 T是学生 F不是学生
                    $data['alipay_is_bank_auth'] = $user->alipay_is_bank_auth;//支付宝用户是否经过银行卡认证 T经过银行卡认证 F未经过银行卡认证
                    $data['alipay_is_mobile_auth'] = $user->alipay_is_mobile_auth;//支付宝用户是否经过手机认证 T经过手机认证 F未经过手机认证
                    $data['alipay_user_status'] = $user->alipay_user_status;//支付宝用户状态 Q快速注册用户 T已认证用户 B被冻结账户 W已注册未激活账户
                    $data['alipay_is_id_auth'] = $user->alipay_is_id_auth;//支付宝用户是否身份证认证 T身份证认证 F非身份证认证
                    $data['alipay_subscribe_time'] = $user->alipay_subscribe_time;//支付宝用户关注时间
                    $data['alipay_cancel_subscribe_time'] = $user->alipay_cancel_subscribe_time;//支付宝用户取消关注时间
                    $data['alipay_subscribe_store_id'] = $user->alipay_subscribe_store_id;//支付宝用户关注入口门店
                    $data['register_address'] = $user->register_address;//注册地址（省,市）
                    $data['wechat_status'] = $user->wechat_status;//微信用户关注状态 1 未关注 2已关注 3取消关注
                    $data['wechat_id'] = $user->wechat_id;//微信用户openid
                    $data['wechat_nickname'] = $user->wechat_nickname;//微信用户昵称
                    $data['wechat_sex'] = $user->wechat_sex;//微信用户性别 1男性 2女性
                    $data['wechat_country'] = $user->wechat_country;//微信用户所在国家
                    $data['wechat_province'] = $user->wechat_province;//微信用户所在省份
                    $data['wechat_city'] = $user->wechat_city;//微信用户所在城市
                    $data['wechat_language'] = $user->wechat_language;//微信用户的语言
                    $data['wechat_headimgurl'] = $user->wechat_headimgurl;//微信用户头像
                    $data['wechat_unionid'] = $user->wechat_unionid;//微信用户unionid
                    $data['wechat_remark'] = $user->wechat_remark;//微信用户备注
                    $data['wechat_groupid'] = $user->wechat_groupid;//微信用户所在分组id
                    $data['wechat_subscribe_time'] = $user->wechat_subscribe_time;//微信用户关注时间
                    $data['wechat_cancel_subscribe_time'] = $user->wechat_cancel_subscribe_time;//微信用户取消关注时间
                    $data['wechat_subscribe_store_id'] = $user->wechat_subscribe_store_id;//微信用户关注入口门店
                    $data['switch'] = $user->switch;//会员等级是否受积分限制1受限制2不受限制                       
                    $data['create_time'] = $user->create_time;//创建时间
                    $data['last_time'] = $user->last_time;//最近更新时间
                    $data['login_client'] = $user->login_client;//最后登录客户端
                    $data['province'] = $user->province;//省
                    $data['city'] = $user->city;//市
                    $usertag = UserTag::model()->findall('flag=:flag and user_id=:user_id', array(
                        ':flag' => FLAG_NO,
                        ':user_id' => $id
                    ));
                    //标签
                    $tag_value = array();
                    if ($usertag) {
                        foreach ($usertag as $key => $val) {
                            $tag_value[$key] = $val['tag_value'];
                        }
                    }
                    $data['tag_value'] = $tag_value;
                    $group = Group::model()->find('flag=:flag and user_id=:user_id', array(
                        ':flag' => FLAG_NO,
                        ':user_id' => $id,
                    ));
                    //分组
                    if ($group) {
                        $userGroup = UserGroup::model()->find('flag=:flag and merchant_id=:merchant_id and id=:id', array(
                            ':flag' => FLAG_NO,
                            ':merchant_id' => $merchantId,
                            ':id' => $group->group_id
                        ));
                        if ($userGroup) {
                            $data['group'] = $userGroup['name'];
                        } else {
                            $data['group'] = '无';
                        }
                    } else {
                        $data['group'] = '无';
                    }
                    $data['order'] = array();
                    $data['order1'] = array();
                    $data['order2'] = array();
                    $data['order_count'] = '0';
                    $data['order1_count'] = '0';
                    $data['order2_count'] = '0';
                    $data['sum_order'] = '0';
                    $data['sum_order1'] = '0';
                    $data['sum_order2'] = '0';
                    //会员消费记录
                    $criteria = new CDbCriteria();
                    $criteria->order = 'pay_time desc';
                    $criteria->addCondition('flag=:flag and user_id=:user_id and merchant_id=:merchant_id');
                    $criteria->params[':flag'] = FLAG_NO;
                    $criteria->params[':user_id'] = $id;
                    $criteria->params[':merchant_id'] = $merchantId;
                    //分页
                    $pages = new CPagination(Order::model()->count($criteria));
                    $pages->pageSize = Yii::app()->params['perPage'];
                    $pages->applyLimit($criteria);
                    $this->page = $pages;
                    $order = Order::model()->findall($criteria);
                    if ($order) {
                        $online_paymoney = Yii::app()->db->createCommand("
                                    select sum(online_paymoney)
                                    from wq_order where merchant_id=$merchantId and user_id=$id and flag=1                            
                                ")->queryScalar();
                        $unionpay_paymoney = Yii::app()->db->createCommand("
                                    select sum(unionpay_paymoney)
                                    from wq_order where merchant_id=$merchantId and user_id=$id and flag=1                            
                                ")->queryScalar();
                        $cash_paymoney = Yii::app()->db->createCommand("
                                    select sum(cash_paymoney)
                                    from wq_order where merchant_id=$merchantId and user_id=$id and flag=1                            
                                ")->queryScalar();
                        $stored_paymoney = Yii::app()->db->createCommand("
                                    select sum(stored_paymoney)
                                    from wq_order where merchant_id=$merchantId and user_id=$id and flag=1                            
                                ")->queryScalar();
                        //累计消费金额
                        $data['sum_order'] = $online_paymoney + $unionpay_paymoney + $cash_paymoney + $stored_paymoney;
                        $data['order_count'] = Order::model()->count($criteria);
                        foreach ($order as $k => $v) {
                            $data['order'][$k]['pay_time'] = $v['pay_time'];
                            $data['order'][$k]['store_name'] = $v->store->name;
                            $data['order'][$k]['pay_channel'] = $v['pay_channel'];
                            //支付宝和微信
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM || $v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM
                                || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM
                                || $v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY
                            ) {
                                $data['order'][$k]['money'] = $v['online_paymoney'];
                            }
                            //银联
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_UNIONPAY) {
                                $data['order'][$k]['money'] = $v['unionpay_paymoney'];
                            }
                            //现金
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_CASH) {
                                $data['order'][$k]['money'] = $v['cash_paymoney'];
                            }
                            //储值
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_STORED) {
                                $data['order'][$k]['money'] = $v['stored_paymoney'];
                            }
                        }
                    }

                    //微信消费记录
                    $criteria1 = new CDbCriteria();
                    $criteria1->order = 'pay_time desc';
                    $criteria1->addCondition('flag=:flag and wechat_user_id=:wechat_user_id and merchant_id=:merchant_id');
                    $criteria1->params[':flag'] = FLAG_NO;
                    $criteria1->params[':wechat_user_id'] = $user->wechat_id;
                    $criteria1->params[':merchant_id'] = $merchantId;
                    //分页
                    $page1 = new CPagination(Order::model()->count($criteria1));
                    $page1->pageSize = Yii::app()->params['perPage'];
                    $page1->applyLimit($criteria1);
                    $this->page1 = $page1;
                    $order1 = Order::model()->findall($criteria1);
                    if ($order1) {
                        $wechat_user_id = $user->wechat_id;
                        $online_paymoney = Yii::app()->db->createCommand("
                                    select sum(online_paymoney)
                                    from wq_order where merchant_id=$merchantId and wechat_user_id='$wechat_user_id' and flag=1                            
                                ")->queryScalar();
                        $unionpay_paymoney = Yii::app()->db->createCommand("
                                    select sum(unionpay_paymoney)
                                    from wq_order where merchant_id=$merchantId and wechat_user_id='$wechat_user_id' and flag=1                            
                                ")->queryScalar();
                        $cash_paymoney = Yii::app()->db->createCommand("
                                    select sum(cash_paymoney)
                                    from wq_order where merchant_id=$merchantId and wechat_user_id='$wechat_user_id' and flag=1                            
                                ")->queryScalar();
                        $stored_paymoney = Yii::app()->db->createCommand("
                                    select sum(stored_paymoney)
                                    from wq_order where merchant_id=$merchantId and wechat_user_id='$wechat_user_id' and flag=1                            
                                ")->queryScalar();
                        //累计消费金额
                        $data['sum_order1'] = $online_paymoney + $unionpay_paymoney + $cash_paymoney + $stored_paymoney;
                        $data['order1_count'] = Order::model()->count($criteria1);
                        foreach ($order1 as $k => $v) {
                            $data['order1'][$k]['pay_time'] = $v['pay_time'];
                            $data['order1'][$k]['store_name'] = $v->store->name;
                            $data['order1'][$k]['pay_channel'] = $v['pay_channel'];
                            //支付宝和微信
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM || $v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM
                                || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM
                                || $v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY
                            ) {
                                $data['order1'][$k]['money'] = $v['online_paymoney'];
                            }
                            //银联
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_UNIONPAY) {
                                $data['order1'][$k]['money'] = $v['unionpay_paymoney'];
                            }
                            //现金
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_CASH) {
                                $data['order1'][$k]['money'] = $v['cash_paymoney'];
                            }
                            //储值
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_STORED) {
                                $data['order1'][$k]['money'] = $v['stored_paymoney'];
                            }
                        }
                    }
                    //支付宝消费记录
                    $criteria2 = new CDbCriteria();
                    $criteria2->order = 'pay_time desc';
                    $criteria2->addCondition('flag=:flag and alipay_user_id=:alipay_user_id and merchant_id=:merchant_id');
                    $criteria2->params[':flag'] = FLAG_NO;
                    $criteria2->params[':alipay_user_id'] = $user->alipay_fuwu_id;
                    $criteria2->params[':merchant_id'] = $merchantId;
                    //分页
                    $page2 = new CPagination(Order::model()->count($criteria2));
                    $page2->pageSize = Yii::app()->params['perPage'];
                    $page2->applyLimit($criteria2);
                    $this->page2 = $page2;
                    $order2 = Order::model()->findall($criteria2);
                    if ($order2) {
                        $alipay_user_id = $user->alipay_fuwu_id;
                        $online_paymoney = Yii::app()->db->createCommand("
                                    select sum(online_paymoney)
                                    from wq_order where merchant_id=$merchantId and alipay_user_id='$alipay_user_id' and flag=1                            
                                ")->queryScalar();
                        $unionpay_paymoney = Yii::app()->db->createCommand("
                                    select sum(unionpay_paymoney)
                                    from wq_order where merchant_id=$merchantId and alipay_user_id='$alipay_user_id' and flag=1                            
                                ")->queryScalar();
                        $cash_paymoney = Yii::app()->db->createCommand("
                                    select sum(cash_paymoney)
                                    from wq_order where merchant_id=$merchantId and alipay_user_id='$alipay_user_id' and flag=1                            
                                ")->queryScalar();
                        $stored_paymoney = Yii::app()->db->createCommand("
                                    select sum(stored_paymoney)
                                    from wq_order where merchant_id=$merchantId and alipay_user_id='$alipay_user_id' and flag=1                            
                                ")->queryScalar();
                        //累计消费金额
                        $data['sum_order2'] = $online_paymoney + $unionpay_paymoney + $cash_paymoney + $stored_paymoney;
                        $data['order2_count'] = Order::model()->count($criteria);
                        foreach ($order2 as $k => $v) {
                            $data['order2'][$k]['pay_time'] = $v['pay_time'];
                            $data['order2'][$k]['store_name'] = $v->store->name;
                            $data['order2'][$k]['pay_channel'] = $v['pay_channel'];
                            //支付宝和微信
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM || $v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM
                                || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM
                                || $v['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY || $v['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY
                            ) {
                                $data['order2'][$k]['money'] = $v['online_paymoney'];
                            }
                            //银联
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_UNIONPAY) {
                                $data['order2'][$k]['money'] = $v['unionpay_paymoney'];
                            }
                            //现金
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_CASH) {
                                $data['order2'][$k]['money'] = $v['cash_paymoney'];
                            }
                            //储值
                            if ($v['pay_channel'] == ORDER_PAY_CHANNEL_STORED) {
                                $data['order2'][$k]['money'] = $v['stored_paymoney'];
                            }
                        }
                    }
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        } else {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '无此数据';
        }
        return json_encode($result);
    }

    //判断用户是否关注商户服务窗
    public function checkIsFollowWechat($openid = '', $merchant_id = '', $encrypt_id = '', $user_id = '')
    {
        $result = array();
        try {

            if (!empty($user_id)) {
                $user = User::model()->findByPk($user_id);
            } else {
                if (!empty($encrypt_id)) {
                    $merchant = Merchant::model()->find('encrypt_id =:encrypt_id', array(
                        ':encrypt_id' => $encrypt_id
                    ));
                    $merchant_id = $merchant->id;
                }
                $user = User::model()->find('merchant_id =:merchant_id and wechat_id =:wechat_id', array(
                    ':merchant_id' => $merchant_id,
                    ':wechat_id' => $openid,
                ));
            }
            if (empty($user)) {
                //未关注且未注册
                $result['data'] = 1;
            } elseif ($user->wechat_status == WECHAT_USER_SUBSCRIBE && empty($user->account)) {
                //已关注未注册
                $result['data'] = 2;
            } elseif ($user->wechat_status == WECHAT_USER_SUBSCRIBE && !empty($user->account)) {
                //已关注已注册
                $result['data'] = 3;
            } elseif ($user->wechat_status != WECHAT_USER_SUBSCRIBE && !empty($user->account)) {
                //未关注已注册
                $result['data'] = 4;
            }
            $result['status'] = ERROR_NONE;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //判断用户是否已经参与过周福利活动
    public function checkUserIFZfl($merchant_id = '', $encrypt_id = '', $openid)
    {
        $result = array();
        try {
            if (!empty($encrypt_id)) {
                $merchant = Merchant::model()->find('encrypt_id = :encrypt_id', array(
                    ':encrypt_id' => $encrypt_id
                ));
                $merchant_id = $merchant->id;
            }
            $data = array();
            if (!empty($merchant_id)) {
                $user = User::model()->find('merchant_id=:merchant_id and wechat_id = :wechat_id', array(
                    ':merchant_id' => $merchant_id,
                    ':wechat_id' => $openid
                ));
                if (!empty($user)) {
                    $usercoupon = UserCoupons::model()->findAll('user_id = :user_id and marketing_activity_type = :marketing_activity_type', array(
                        ':user_id' => $user->id,
                        ':marketing_activity_type' => MARKETING_ACTIVITY_TYPE_DMALL_ZFL
                    ));
                    foreach ($usercoupon as $k => $v) {
                        $data[$k] = $v->marketing_activity_id;
                    }
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /***********************************************2016-3-31******************************************************/
    //判断用户是否可以免登，如果可以则返回user_id
    public function checkUserIfLogin($wechat_open_id = '', $alipay_open_id = '', $encrypt_id = '', $merchant_id = '')
    {
        $result = array();
        try {
            if (!empty($encrypt_id)) {
                $merchant = Merchant::model()->find('encrypt_id = :encrypt_id', array(
                    ':encrypt_id' => $encrypt_id
                ));
                $merchant_id = $merchant->id;
            }
            if (!empty($wechat_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id and flag =:flag and wechat_id =:wechat_id and type =:type and account is not null', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':wechat_id' => $wechat_open_id,
                    ':type' => USER_TYPE_WANQUAN_MEMBER
                ));
            } elseif (!empty($alipay_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id and flag =:flag and alipay_fuwu_id =:alipay_fuwu_id and type =:type and account is not null', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':alipay_fuwu_id' => $alipay_open_id,
                    ':type' => USER_TYPE_WANQUAN_MEMBER
                ));
            }
            if (!empty($user)) {
                $result['status'] = ERROR_NONE;
                $result['user_id'] = $user->id;
            } else {
                $result['status'] = ERROR_NO_DATA;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 根据open_id获取会员
     * @param $wechat_open_id
     * @param $alipay_open_id
     * @param string $merchant_id
     * @return string
     */
    public function getUserByOpenid($wechat_open_id, $alipay_open_id, $merchant_id = '')
    {
        $result = array();
        try {
            if (!empty($wechat_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id flag =:flag and wechat_id =:wechat_id and type =:type', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':wechat_id' => $wechat_open_id,
                    ':type' => USER_TYPE_WANQUAN_MEMBER
                ));
            } elseif (!empty($alipay_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id flag =:flag and alipay_fuwu_id =:alipay_fuwu_id and type =:type', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':alipay_fuwu_id' => $alipay_open_id,
                    ':type' => USER_TYPE_WANQUAN_MEMBER
                ));
            }
            if (!empty($user)) {
                $data['id'] = $user->id;
                $data['nickname'] = $user->nickname;
                $data['avatar'] = $user->avatar;
                $data['sex'] = $user->sex;
                $data['alipay_avatar'] = $user->alipay_avatar;
                $data['province'] = $user->province;
                $data['city'] = $user->city;
                $data['address'] = $user->address;
                $data['wechat_id'] = $user->wechat_id;
                $data['alipay_fuwu_id'] = $user->alipay_fuwu_id;

                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 根据open_id查找粉丝
     * @param $wechat_open_id
     * @param $alipay_open_id
     * @param string $merchant_id
     * @return string
     */
    public function getNewFansByOpenid($wechat_open_id, $alipay_open_id, $merchant_id = '')
    {
        $result = array();
        try {
            if (!empty($wechat_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id and flag =:flag and wechat_id =:wechat_id and type =:type and wechat_status =:wechat_status', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':wechat_id' => $wechat_open_id,
                    ':type' => USER_TYPE_WECHAT_FANS,
                    ':wechat_status' => WECHAT_USER_SUBSCRIBE
                ));
            } elseif (!empty($alipay_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id and flag =:flag and alipay_fuwu_id =:alipay_fuwu_id and type =:type and alipay_status =:alipay_status', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':alipay_fuwu_id' => $alipay_open_id,
                    ':type' => USER_TYPE_ALIPAY_FANS,
                    ':alipay_status' => ALIPAY_USER_SUBSCRIBE
                ));
            }
            if (!empty($user)) {
                $data['id'] = $user->id;
                $data['wechat_id'] = $user->wechat_id;
                $data['alipay_fuwu_id'] = $user->alipay_fuwu_id;

                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 根据open_id，merchant_id获取已关注的粉丝
     * @param $merchant_id
     * @param $wechat_open_id
     * @param $alipay_open_id
     * @return string
     */
    public function getFansByOpenid($merchant_id, $wechat_open_id, $alipay_open_id)
    {
        $result = array();
        try {
            if (!empty($wechat_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id and flag =:flag and wechat_id =:wechat_id and type =:type and wechat_status =:wechat_status', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':wechat_id' => $wechat_open_id,
                    ':type' => USER_TYPE_WECHAT_FANS,
                    ':wechat_status' => WECHAT_USER_SUBSCRIBE
                ));
            } elseif (!empty($alipay_open_id)) {
                $user = User::model()->find('merchant_id =:merchant_id and flag =:flag and alipay_fuwu_id =:alipay_fuwu_id and type =:type and alipay_status =:alipay_status', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO,
                    ':alipay_fuwu_id' => $alipay_open_id,
                    ':type' => USER_TYPE_ALIPAY_FANS,
                    ':alipay_status' => ALIPAY_USER_SUBSCRIBE
                ));
            }
            if (!empty($user)) {
                $data['id'] = $user->id;
                $data['nickname'] = $user->nickname;
                $data['avatar'] = $user->avatar;
                $data['sex'] = $user->sex;
                $data['alipay_avatar'] = $user->alipay_avatar;
                $data['province'] = $user->province;
                $data['city'] = $user->city;
                $data['address'] = $user->address;

                $data['wechat_nickname'] = $user->wechat_nickname;
                $data['wechat_sex'] = $user->wechat_sex;
                $data['wechat_province'] = $user->wechat_province;
                $data['wechat_city'] = $user->wechat_city;
                $data['wechat_country'] = $user->wechat_country;
                $data['wechat_headimgurl'] = $user->wechat_headimgurl;

                $data['alipay_avatar'] = $user->alipay_avatar;
                $data['alipay_nickname'] = $user->alipay_nickname;
                $data['alipay_province'] = $user->alipay_province;
                $data['alipay_city'] = $user->alipay_city;
                $data['alipay_gender'] = $user->alipay_gender;
                $data['alipay_is_student_certified'] = $user->alipay_is_student_certified;
                $data['alipay_user_type_value'] = $user->alipay_user_type_value;
                $data['alipay_is_licence_auth'] = $user->alipay_is_licence_auth;
                $data['alipay_is_certified'] = $user->alipay_is_certified;
                $data['alipay_certified_grade_a'] = $user->alipay_certified_grade_a;
                $data['alipay_is_bank_auth'] = $user->alipay_is_bank_auth;
                $data['alipay_is_mobile_auth'] = $user->alipay_is_mobile_auth;
                $data['alipay_user_status'] = $user->alipay_user_status;
                $data['alipay_is_id_auth'] = $user->alipay_is_id_auth;

                $data['wechat_id'] = $user->wechat_id;
                $data['alipay_fuwu_id'] = $user->alipay_fuwu_id;

                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 保存会员信息
     * @param $user_id
     * @param $user_info
     * @return string
     */
    public function saveMemberInfo($user_id, $user_info)
    {
        try {
            $user = User::model()->find('id =:id and flag =:flag', array(
                ':id' => $user_id,
                ':flag' => FLAG_NO
            ));

            if (!empty($user)) {
                $user->nickname = $user_info['nickname'];
                $user->avatar = $user_info['avatar'];
                $user->sex = $user_info['sex'];

                $user->wechat_nickname = $user_info['wechat_nickname'];
                $user->wechat_sex = $user_info['wechat_sex'];
                $user->wechat_province = $user_info['wechat_province'];
                $user->wechat_city = $user_info['wechat_city'];
                $user->wechat_country = $user_info['wechat_country'];
                $user->wechat_headimgurl = $user_info['wechat_headimgurl'];

                $user->alipay_avatar = $user_info['alipay_avatar'];
                $user->alipay_nickname = $user_info['alipay_nickname'];
                $user->alipay_province = $user_info['alipay_province'];
                $user->alipay_city = $user_info['alipay_city'];
                $user->alipay_gender = $user_info['alipay_gender'];
                $user->alipay_is_student_certified = $user_info['alipay_is_student_certified'];
                $user->alipay_user_type_value = $user_info['alipay_user_type_value'];
                $user->alipay_is_licence_auth = $user_info['alipay_is_licence_auth'];
                $user->alipay_is_certified = $user_info['alipay_is_certified'];
                $user->alipay_certified_grade_a = $user_info['alipay_certified_grade_a'];
                $user->alipay_is_bank_auth = $user_info['alipay_is_bank_auth'];
                $user->alipay_is_mobile_auth = $user_info['alipay_is_mobile_auth'];
                $user->alipay_user_status = $user_info['alipay_user_status'];
                $user->alipay_is_id_auth = $user_info['alipay_is_id_auth'];

                $user->wechat_id = $user_info['wechat_id'];
                $user->alipay_fuwu_id = $user_info['alipay_fuwu_id'];

                if ($user->save()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 验证用户id和商户id
     * @param $user_id
     * @param $merchant_id
     * @return bool
     */
    public function checkUserId($user_id, $merchant_id)
    {
        //检查用户id和商户id是否匹配
        $user = User::model()->find('merchant_id = :merchant_id and id = :id and flag =:flag', array(
            ':merchant_id' => $merchant_id,
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));
        if (!empty($user)) {
            return true;
        } else {
            return false;
        }
    }

    /** 检查用户是否填写了必填信息
     * @param $user_id
     * @return bool
     */
    public function checkUserFillInfo($user_id)
    {
        //检查用户id和商户id是否匹配
        $user = User::model()->find('id = :id and flag =:flag', array(
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));
        if (!empty($user) && $user->if_perfect == USER_IF_PERFECT_NO) {
            return true;
        } elseif (!empty($user) && $user->if_perfect == USER_IF_PERFECT_YES) {
            return false;
        }
    }

    /** 修改用户填写信息状态
     * @param $user_id
     * @return bool
     */
    public function alterFillInfo($user_id)
    {
        //查找用户
        $user = User::model()->find('id = :id and flag =:flag', array(
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));

        $user->if_perfect = USER_IF_PERFECT_YES;
        if ($user->save()) {
            return true;
        } else {
            return false;
        }
    }

    /** 保存用户登录信息
     * @param $user_id
     * @param $login_ip
     * @param $login_client
     * @return bool
     */
    public function saveLoginInfo($user_id, $login_ip, $login_client)
    {
        //查找用户
        $user = User::model()->find('id = :id and flag =:flag', array(
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));

        $user->login_ip = $login_ip;
        $user->login_client = $login_client;
        $user->login_time = date('Y-m-d H:i:s', time());

        if ($user->save()) {
            return true;
        } else {
            return false;
        }
    }

    /** 解除会员微信粉丝绑定
     * @param $user_id
     * @return bool
     */
    public function clearUserWechatBind($user_id)
    {
        //查找用户
        $user = User::model()->find('id = :id and flag =:flag', array(
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));

        $user->wechat_id = '';

        if ($user->save()) {
            return true;
        } else {
            return false;
        }
    }

    /** 解除会员支付宝粉丝绑定
     * @param $user_id
     * @return bool
     */
    public function clearUserAlipayBind($user_id)
    {
        //查找用户
        $user = User::model()->find('id = :id and flag =:flag', array(
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));

        $user->alipay_fuwu_id = '';

        if ($user->save()) {
            return true;
        } else {
            return false;
        }
    }

    /** 解除粉丝绑定
     * @param $user_id
     * @return bool
     */
    public function clearFansBind($user_id)
    {
        //查找粉丝
        $fans = User::model()->find('id = :id and flag =:flag', array(
            ':id' => $user_id,
            ':flag' => FLAG_NO
        ));

        $fans->bind_status = USER_BIND_STATUS_UNBIND;

        if ($fans->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * post提交数据
     * @param type $url
     * @param type $data
     * @return type
     */
    function  postData($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过SSL证书检查  https方式
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE,不加这句返回始终是1
        curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($ch, CURLOPT_POST, 1); //启用POST提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $file_contents = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno' . curl_error($ch);
        }
        curl_close($ch);

        return $file_contents;
    }
}