<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/components/Component.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/class/wechat/Wechat.php';

/**
 * 卡券
 */
class MobileCardCouponsC extends mainClass
{
    public $page = null;

    /**
     * 添加券
     * $merchant_id,   商户id
     * $type,          券类型
     * $if_wechat,     是否同步到微信卡包 1不开启 2开启
     * $title,         券标题
     * $vice_title,    副标题
     * $discount       券折扣
     * $prompt,        提示操作
     * $if_share,      用户是否可以分享领取链接 1可以 2不可以
     * $if_give,       可否转增其他好友 1 能 2不能
     * $num,           发放数量
     * $time_type,     有效时间类型1固定时间 2相对时间
     * $start_time,    固定时间时的有效开始时间
     * $end_time,      固定时间有效结束时间
     * $start_days,    领取后几天生效 当天代表0最高90天
     * $effective_days, 有效天数最少1天最多90天
     * $receive_num,    每个用户领取数量
     * $mini_consumption,  最低消费
     * $if_with_userdiscount, 是否能与会员折扣同用1不能 2能
     * $tel,                   客服电话
     * $use_illustrate,        使用须知
     * $discount_illustrate,    优惠说明
     * $money_type,             券金额类型 1固定 2随机
     * $strat_money,            随机金额开始金额
     * $end_money,              随机金额结束金额
     * $money                   固定金额
     * $store_limit             门店限制
     * $use_restriction         使用限制
     * $short_name              商户简称
     * $use_channel                核销渠道
     */
    public function cardCouponsAdd($merchant_id, $type, $if_wechat, $title, $vice_title, $discount,
                                   $prompt, $if_share, $if_give, $num, $time_type, $start_time, $end_time,
                                   $start_days, $effective_days, $receive_num, $mini_consumption, $if_with_userdiscount,
                                   $tel, $use_illustrate, $discount_illustrate, $money_type, $start_money, $end_money, $money, $store_limit, $color, $use_restriction, $short_name, $use_channel = NULL)
    {
        $result = array();
        $errMsg = '';
        $flag = 0;
        $model = new Coupons();

        //验证商户简称
        if (empty($short_name)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $flag = 1;
            $errMsg = $errMsg . ' 商户简称必填';
            Yii::app()->user->setFlash('short_name_error', '商户简称必填');
        } else {
            $len = mb_strlen($short_name, 'utf-8');
            if ($len > 12) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $flag = 1;
                $errMsg = $errMsg . ' 商户简称不得超过12';
                Yii::app()->user->setFlash('short_name_error', '商户简称不得超过12');
            }
        }

        //验证券标题
        if (empty($title)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $flag = 1;
            $errMsg = $errMsg . ' 券标题必填';
            Yii::app()->user->setFlash('title_error', '券标题必填');
        }

        //验证折扣数
        if ($type == COUPON_TYPE_DISCOUNT) {//券类型是折扣券的时候进行验证
            if (empty($discount)) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $flag = 1;
                $errMsg = $errMsg . ' 折扣数必填';
                Yii::app()->user->setFlash('discount_error', '折扣数必填');
            } else {
                if ($discount < 1 || $discount > 9.9) {
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 折扣设置不合法';
                    $flag = 1;
                    Yii::app()->user->setFlash('discount_error', '折扣设置不合法');
                }

                if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $discount)) { //验证数字正则
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 折扣设置不合法';
                    $flag = 1;
                    Yii::app()->user->setFlash('discount_error', '折扣设置不合法');
                }
            }
        }

        //验证券金额
        if ($type == COUPON_TYPE_CASH) { //券类型是代金券的时候进行验证
            if (empty($money_type)) { //券金额类型为空   则默认是随机金额
                //验证随机金额开始金额与结束金额
                if (empty($if_wechat)) {
                    if (empty($start_money) || empty($end_money)) {
                        $result ['status'] = ERROR_PARAMETER_MISS;
                        $flag = 1;
                        $errMsg = $errMsg . ' 随机金额必填';
                        Yii::app()->user->setFlash('money_random_error', '随机金额必填');
                    } else {
                        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $start_money)) {
                            $result ['status'] = ERROR_PARAMETER_MISS;
                            $flag = 1;
                            $errMsg = $errMsg . ' 随机金额设置不合法';
                            Yii::app()->user->setFlash('money_random_error', '随机金额设置不合法');
                        }
                        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $end_money)) {
                            $result ['status'] = ERROR_PARAMETER_MISS;
                            $flag = 1;
                            $errMsg = $errMsg . ' 随机金额设置不合法';
                            Yii::app()->user->setFlash('money_random_error', '随机金额设置不合法');
                        }
                        if ($start_money < 0 || $end_money <= 0) {
                            $result ['status'] = ERROR_PARAMETER_MISS;
                            $flag = 1;
                            $errMsg = $errMsg . ' 随机金额设置不合法';
                            Yii::app()->user->setFlash('money_random_error', '随机金额设置不合法');
                        } else {
                            if ($start_money > $end_money) {
                                $result ['status'] = ERROR_PARAMETER_MISS;
                                $flag = 1;
                                $errMsg = $errMsg . ' 两金额大小设置不合法';
                                Yii::app()->user->setFlash('money_random_error', '两金额大小设置不合法');
                            }
                        }
                    }
                }
            } else { //券金额类型不为空   则是固定金额
                if (empty($money)) {
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $flag = 1;
                    $errMsg = $errMsg . ' 固定金额必填';
                    Yii::app()->user->setFlash('money_error', '固定金额必填');
                } else {
                    if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $money)) {
                        $result ['status'] = ERROR_PARAMETER_MISS;
                        $flag = 1;
                        $errMsg = $errMsg . ' 固定金额设置不合法';
                        Yii::app()->user->setFlash('money_error', '固定金额设置不合法');
                    }
                    if ($money <= 0) {
                        $result ['status'] = ERROR_PARAMETER_MISS;
                        $flag = 1;
                        $errMsg = $errMsg . ' 固定金额设置不合法';
                        Yii::app()->user->setFlash('money_error', '固定金额设置不合法');
                    }
                }
            }
        }

        //验证使用限制
        if ($type == COUPON_TYPE_CASH) {  //券类型是代金券的时候进行验证
            if (!empty($use_restriction)) {
                if (!preg_match('/^[1-9]\d*$/', $use_restriction)) { //判断匹配大于0的正整数正则
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 使用限制不合法';
                    $flag = 1;
                    Yii::app()->user->setFlash('use_restriction_error', '使用限制不合法');
                }
            }
        }

        //验证发放数量
        if (!isset ($num) || empty ($num)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . ' 发放量必填';
            $flag = 1;
            Yii::app()->user->setFlash('num_error', '发放量必填');
        } else {
            if ($num <= 0) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 发放量不合法';
                $flag = 1;
                Yii::app()->user->setFlash('num_error', '发放量不合法');
            }
            if (!preg_match('/^[1-9]\d*$/', $num)) { //判断匹配大于0的正整数正则
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 发放量不合法';
                $flag = 1;
                Yii::app()->user->setFlash('num_error', '发放量不合法');
            }
        }

        //验证提示操作
        if (!isset ($prompt) || empty ($prompt)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . ' 提示操作必填';
            $flag = 1;
            Yii::app()->user->setFlash('prompt_error', '提示操作必填');
        }

        //验证固定时间或相对时间
        if ($time_type == VALID_TIME_TYPE_FIXED) { //如果是固定时间
            if (empty($end_time) || empty($start_time)) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 固定时间必填';
                $flag = 1;
                Yii::app()->user->setFlash('valid_time_error', '固定时间必填');
            }
        } else { //如果是相对时间

        }

        //验证优惠说明
        if ($type == COUPON_TYPE_EXCHANGE) { //券类型是兑换券的时候进行验证
            if (empty($discount_illustrate)) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 优惠说明必填';
                $flag = 1;
                Yii::app()->user->setFlash('discount_illustrate_error', '优惠说明必填');
            }
        }

        //验证使用须知
        if (empty($use_illustrate)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . ' 使用须知必填';
            $flag = 1;
            Yii::app()->user->setFlash('use_illustrate_error', '使用须知必填');
        }

        if ($flag == 1) {
            $result ['errMsg'] = $errMsg;
            return json_encode($result);
        }

        $model->merchant_id = $merchant_id;
        $model->merchant_short_name = $short_name;
        $model->type = $type; //券类型
        $model->if_wechat = !empty($if_wechat) ? IF_WECHAT_YES : IF_WECHAT_NO; //是否同步到微信卡包 1不开启 2开启
        $model->title = $title; //券标题
        $model->vice_title = $vice_title; //副标题
        $model->discount = $discount / 10; //折扣数
        $model->prompt = $prompt; //提示操作
        $model->if_share = !empty($if_share) ? IF_SHARE_YES : IF_SHARE_NO; //用户是否可以分享领取链接 1可以 2不可以
        $model->if_give = !empty($if_give) ? IF_GIVE_YES : IF_GIVE_NO; //可否转增其他好友 1 能 2不能
        $model->num = $num; //发放数量
        $model->time_type = $time_type; //有效时间类型1固定时间 2相对时间
        if (!empty($start_time)) {//固定时间时的有效开始时间
            $model->start_time = $start_time . ' 00:00:00';
        }
        if (!empty($end_time)) {//固定时间有效结束时间
            $model->end_time = $end_time . ' 23:59:59';
        }

        if ($time_type == VALID_TIME_TYPE_RELATIVE) {
            $model->start_days = empty($start_days) ? 0 : $start_days; //领取后几天生效 当天代表0最高90天
            $model->effective_days = $effective_days;  //有效天数最少1天最多90天
        }
        $model->receive_num = $receive_num; //每个用户领取数量
        $model->mini_consumption = $mini_consumption; //最低消费
        $model->if_with_userdiscount = !empty($if_with_userdiscount) ? IF_WITH_USERDISCOUNT_YES : IF_WITH_USERDISCOUNT_NO; //是否能与会员折扣同用1不能 2能
        $model->tel = $tel; //客服电话
        $model->use_illustrate = $use_illustrate; //使用须知

        if ($type == COUPON_TYPE_EXCHANGE) {
            $model->discount_illustrate = $discount_illustrate;  //优惠说明
        } elseif ($type == COUPON_TYPE_DISCOUNT) {
            if (!empty($mini_consumption)) {
                $model->discount_illustrate = $discount . '折折扣券' . ',' . '满' . $mini_consumption . '元可用';
            } else {
                $model->discount_illustrate = $discount . '折折扣券' . ',' . '任意金额可用';
            }
        } else {
            if (empty ($if_wechat)) {
                if (!empty ($money_type)) { // 固定金额
                    if (!empty ($mini_consumption)) {
                        $model->discount_illustrate = $money . '元代金券' . ',' . '满' . $mini_consumption . '元可用';
                    } else {
                        $model->discount_illustrate = $money . '元代金券' . ',' . '任意金额可用';
                    }
                } else { // 随机金额
                    if (!empty ($mini_consumption)) {
                        $model->discount_illustrate = $start_money . '元到' . $end_money . '元随机券' . ',' . '满' . $mini_consumption . '元可用';
                    } else {
                        $model->discount_illustrate = $start_money . '元到' . $end_money . '元随机券' . ',' . '任意金额可用';
                    }
                }
            } else {
                if (!empty ($mini_consumption)) {
                    $model->discount_illustrate = $money . '元代金券' . ',' . '满' . $mini_consumption . '元可用';
                } else {
                    $model->discount_illustrate = $money . '元代金券' . ',' . '任意金额可用';
                }
            }
        }
        if (empty($if_wechat)) {
            $model->money_type = !empty($money_type) ? FACE_VALUE_TYPE_FIXED : FACE_VALUE_TYPE_RANDOM; //券金额类型 1固定 2随机
        } else {
            $model->money_type = FACE_VALUE_TYPE_FIXED;
        }
        if (!empty($start_money) && !empty($end_money)) {
            $model->money_random = ',' . $start_money . ',' . $end_money . ','; //代金券的随机金额，两金额用逗号分开
        }

        $model->money = $money; //代金券的固定金额
        $model->color = $color; //券颜色
        $model->use_restriction = empty($use_restriction) ? '1' : $use_restriction; //使用限制
        $model->use_channel = $use_channel; //核销渠道

        if (empty($store_limit)) {
            $storeId = '';
            $store = Store::model()->findAll('flag=:flag and merchant_id=:merchant_id', array(':flag' => FLAG_NO, ':merchant_id' => $merchant_id));
            foreach ($store as $key => $value) {
                $storeId = $storeId . ',' . ($value->id);
            }
            $model->store_limit = $storeId . ',';
        } else {
            $model->store_limit = $store_limit . ',';
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            if ($model->save()) {
                //如果该券是同步到微信的     则调用创建卡券接口
                if ($model->if_wechat == IF_WECHAT_YES) {
                    $logo_url = $this->getLogoUrl($model->id, $model->color);
                    $t = $this->getToken();
                    $apiResult = $this->getCardId($model->id, $model->color, $logo_url, $t);
                    if ($apiResult['errcode'] == 0) { //微信成功返回码0
                        $coupons = Coupons::model()->findByPk($model->id);
                        $coupons['card_id'] = $apiResult['card_id'];
                        if ($coupons->save()) {
                            $result ['errcode'] = 0;
                        }
                    } else {
//						throw new Exception('微信卡券同步失败');
//	 					Yii::log('创建卡券接口错误信息:'.$apiResult['errmsg'].'创建卡券接口错误码：'.$apiResult['errcode'].'Token:'.$t, 'warning');
                        $result ['errcode'] = 1;
// 						Coupons::model()->findByPk($model->id)->delete();
                    }
                }
                $transaction->commit();
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = ''; // 错误信息
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                $result ['errMsg'] = '数据保存失败'; // 错误信息
            }
        } catch (Exception $e) {
            $transaction->rollback(); //如果操作失败, 数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息

        }

        return json_encode($result);


    }

    /**
     * 获取商户简称
     * $merchant_id   商户id
     */
    public function getMerchantShortName($merchant_id)
    {
        $merchant_short_name = '';
        $onlineShop = Onlineshop::model()->find('merchant_id=:merchant_id and flag=:flag',
            array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        $merchant = Merchant::model()->findByPk($merchant_id);

        if (!empty($onlineShop)) {
            if (!empty($onlineShop->name)) {
                $merchant_short_name = $onlineShop->name;
            }
        } else {
            $merchant_short_name = $merchant->name;
        }
        return $merchant_short_name;
    }

    /**
     * 获取券的信息
     * $coupons_id  券id
     */
    public function getEditCoupons($coupons_id)
    {
        $result = array();
        $data = array();
        try {
            $model = Coupons::model()->findByPk($coupons_id);

            if (!empty($model)) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';

                $data['list']['id'] = $model['id'];
                $data['list']['merchant_short_name'] = $model['merchant_short_name']; //商户简称
                $data['list']['type'] = $model['type']; //券类型
                $data['list']['if_wechat'] = $model['if_wechat']; //是否同步到微信卡包 1不开启 2开启
                $data['list']['title'] = $model['title']; //券标题
                $data['list']['vice_title'] = $model['vice_title']; //副标题
                $data['list']['money_type'] = $model['money_type']; //券金额类型 1固定 2随机
                $data['list']['money_random'] = $model['money_random']; //代金券的随机金额
                $data['list']['money'] = $model['money']; //代金券的固定金额
                $data['list']['discount'] = $model['discount']; //券折扣
                $data['list']['prompt'] = $model['prompt']; //提示操作
                $data['list']['color'] = $model['color']; //创建时间
                $data['list']['if_share'] = $model['if_share']; //用户是否可以分享领取链接 1可以 2不可以
                $data['list']['if_give'] = $model['if_give']; //可否转增其他好友 1 能 2不能
                $data['list']['num'] = $model['num']; //发放数量
                $data['list']['get_num'] = $model['get_num']; //已领取数量
                $data['list']['time_type'] = $model['time_type']; //有效时间类型1固定时间 2相对时间
                $data['list']['start_time'] = $model['start_time']; //固定时间时的有效开始时间
                $data['list']['end_time'] = $model['end_time']; //固定时间有效结束时间
                $data['list']['start_days'] = $model['start_days']; //领取后几天生效 当天代表0最高90天
                $data['list']['effective_days'] = $model['effective_days']; //有效天数最少1天最多90天
                $data['list']['receive_num'] = $model['receive_num']; //每个用户领取数量
                $data['list']['mini_consumption'] = $model['mini_consumption']; //最低消费
                $data['list']['use_restriction'] = $model['use_restriction']; //使用限制
                $data['list']['if_with_userdiscount'] = $model['if_with_userdiscount']; //是否能与会员折扣同用1不能 2能
                $data['list']['store_limit'] = $model['store_limit']; //门店限制
                $data['list']['tel'] = $model['tel']; //客服电话
                $data['list']['use_illustrate'] = $model['use_illustrate']; //使用须知
                $data['list']['discount_illustrate'] = $model['discount_illustrate']; //优惠说明
                $data['list']['if_invalid'] = $model['if_invalid']; //是否失效 1未失效 2已失效
                $data['list']['create_time'] = $model['create_time']; //创建时间
                $data['list']['store_limit_name'] = $this->getStoreName($model['store_limit']); //门店限制名称
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('数据不存在');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        $result['data'] = $data;
        return json_encode($result);
    }

    /**
     * 获取门店限制名称
     * $store_limit   门店id集合（字符串形式）
     */
    public function getStoreName($store_limit)
    {
        $store_name = array();
        $arr = explode(',', $store_limit);
        for ($i = 1; $i < count($arr) - 1; $i++) {
            $store = Store::model()->findByPk($arr[$i]);
            $store_name[$store->id] = $store->name;
        }
        return $store_name;
    }

    /**
     * 获取门店列表
     */
    public function getStoreList($merchant_id)
    {
        $result = array();
        $data = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);

            $store = Store::model()->findAll($criteria);
            if (!empty($store)) {
                foreach ($store as $k => $v) {
                    $data['list'][$k]['id'] = $v['id'];
                    $data['list'][$k]['name'] = $v['name']; //门店名
                    $data['list'][$k]['address'] = $v['address']; //门店地址
                }
                $result['data'] = $data;
                $result ['status'] = ERROR_NONE;
            } else {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 编辑券
     * $coupons_id     券id
     * $merchant_id,   商户id
     * $if_wechat,     是否同步到微信卡包 1不开启 2开启
     * $title,         券标题
     * $vice_title,    副标题
     * $discount       券折扣
     * $prompt,        提示操作
     * $if_share,      用户是否可以分享领取链接 1可以 2不可以
     * $if_give,       可否转增其他好友 1 能 2不能
     * $num,           发放数量
     * $time_type,     有效时间类型1固定时间 2相对时间
     * $start_time,    固定时间时的有效开始时间
     * $end_time,      固定时间有效结束时间
     * $start_days,    领取后几天生效 当天代表0最高90天
     * $effective_days, 有效天数最少1天最多90天
     * $receive_num,    每个用户领取数量
     * $mini_consumption,  最低消费
     * $if_with_userdiscount, 是否能与会员折扣同用1不能 2能
     * $tel,                   客服电话
     * $use_illustrate,        使用须知
     * $discount_illustrate,    优惠说明
     * $money_type,             券金额类型 1固定 2随机
     * $strat_money,            随机金额开始金额
     * $end_money,              随机金额结束金额
     * $money                   固定金额
     * $store_limit             门店限制
     */
    public function editCardCoupons($coupons_id, $merchant_id, $if_wechat, $title, $vice_title, $discount, $prompt,
                                    $if_share, $if_give, $num, $time_type, $start_time, $end_time, $start_days,
                                    $effective_days, $receive_num, $mini_consumption, $if_with_userdiscount, $tel,
                                    $use_illustrate, $discount_illustrate, $money_type, $start_money, $end_money, $money, $store_limit, $color, $use_restriction, $short_name)
    {
        $result = array();
        $errMsg = '';
        $flag = 0;
        $model = Coupons::model()->findByPk($coupons_id);

        //验证商户简称
        if (empty($short_name)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $flag = 1;
            $errMsg = $errMsg . ' 商户简称必填';
            Yii::app()->user->setFlash('short_name_error', '商户简称必填');
        } else {
            $len = mb_strlen($short_name, 'utf-8');
            if ($len > 12) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $flag = 1;
                $errMsg = $errMsg . ' 商户简称不得超过12';
                Yii::app()->user->setFlash('short_name_error', '商户简称不得超过12');
            }
        }

        //验证券标题
        if (empty($title)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $flag = 1;
            $errMsg = $errMsg . ' 券标题必填';
            Yii::app()->user->setFlash('title_error', '券标题必填');
        }

        //验证折扣数
        if ($model->type == COUPON_TYPE_DISCOUNT) {//券类型是折扣券的时候进行验证
            if (empty($discount)) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $flag = 1;
                $errMsg = $errMsg . ' 折扣数必填';
                Yii::app()->user->setFlash('discount_error', '折扣数必填');
            } else {
                if ($discount < 1 || $discount > 9.9) {
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 折扣设置不合法';
                    $flag = 1;
                    Yii::app()->user->setFlash('discount_error', '折扣设置不合法');
                }

                if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $discount)) { //验证数字正则
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 折扣设置不合法';
                    $flag = 1;
                    Yii::app()->user->setFlash('discount_error', '折扣设置不合法');
                }
            }
        }
        //echo $if_wechat;exit;
        //验证券金额
        if (empty($if_wechat)) {
            if ($model->type == COUPON_TYPE_CASH) { //券类型是代金券的时候进行验证
                if (empty($money_type)) { //券金额类型为空   则默认是随机金额
                    //验证随机金额开始金额与结束金额
                    if (empty($if_wechat)) {
                        if (empty($start_money) || empty($end_money)) {
                            $result ['status'] = ERROR_PARAMETER_MISS;
                            $flag = 1;
                            $errMsg = $errMsg . ' 随机金额必填';
                            Yii::app()->user->setFlash('money_random_error', '随机金额必填');
                        } else {
                            if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $start_money)) {
                                $result ['status'] = ERROR_PARAMETER_MISS;
                                $flag = 1;
                                $errMsg = $errMsg . ' 随机金额设置不合法';
                                Yii::app()->user->setFlash('money_error', '随机金额设置不合法');
                            }
                            if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $end_money)) {
                                $result ['status'] = ERROR_PARAMETER_MISS;
                                $flag = 1;
                                $errMsg = $errMsg . ' 随机金额设置不合法';
                                Yii::app()->user->setFlash('money_error', '随机金额设置不合法');
                            }
                            if ($start_money < 0 || $end_money <= 0) {
                                $result ['status'] = ERROR_PARAMETER_MISS;
                                $flag = 1;
                                $errMsg = $errMsg . ' 随机金额设置不合法';
                                Yii::app()->user->setFlash('money_random_error', '随机金额设置不合法');
                            } else {
                                if ($start_money > $end_money) {
                                    $result ['status'] = ERROR_PARAMETER_MISS;
                                    $flag = 1;
                                    $errMsg = $errMsg . ' 两金额大小设置不合法';
                                    Yii::app()->user->setFlash('money_random_error', '两金额大小设置不合法');
                                }
                            }
                        }
                    }
                } else { //券金额类型不为空   则是固定金额

                    if (empty($money)) {
                        echo 'ppp';
                        exit;
                        $result ['status'] = ERROR_PARAMETER_MISS;
                        $flag = 1;
                        $errMsg = $errMsg . ' 固定金额必填';
                        Yii::app()->user->setFlash('money_error', '固定金额必填');
                    } else {
                        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $money)) {
                            $result ['status'] = ERROR_PARAMETER_MISS;
                            $flag = 1;
                            $errMsg = $errMsg . ' 固定金额设置不合法';
                            Yii::app()->user->setFlash('money_error', '固定金额设置不合法');
                        }
                        if ($money <= 0) {
                            $result ['status'] = ERROR_PARAMETER_MISS;
                            $flag = 1;
                            $errMsg = $errMsg . ' 固定金额设置不合法';
                            Yii::app()->user->setFlash('money_error', '固定金额设置不合法');
                        }
                    }

                }
            }
        } else {
            if ($model->type == COUPON_TYPE_CASH) {
                if (empty($money)) {
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $flag = 1;
                    $errMsg = $errMsg . ' 固定金额必填';
                    Yii::app()->user->setFlash('money_error', '固定金额必填');
                } else {
                    if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $money)) {
                        $result ['status'] = ERROR_PARAMETER_MISS;
                        $flag = 1;
                        $errMsg = $errMsg . ' 固定金额设置不合法';
                        Yii::app()->user->setFlash('money_error', '固定金额设置不合法');
                    }
                    if ($money <= 0) {
                        $result ['status'] = ERROR_PARAMETER_MISS;
                        $flag = 1;
                        $errMsg = $errMsg . ' 固定金额设置不合法';
                        Yii::app()->user->setFlash('money_error', '固定金额设置不合法');
                    }
                }
            }
        }


        //验证使用限制
        if ($model->type == COUPON_TYPE_CASH) {  //券类型是代金券的时候进行验证
            if (!empty($use_restriction)) {
                if (!preg_match('/^[1-9]\d*$/', $use_restriction)) { //判断匹配大于0的正整数正则
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 使用限制不合法';
                    $flag = 1;
                    Yii::app()->user->setFlash('use_restriction_error', '使用限制不合法');
                }
            }
        }

        //验证发放数量
        if (!isset ($num) || empty ($num)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . ' 发放量必填';
            $flag = 1;
            Yii::app()->user->setFlash('num_error', '发放量必填');
        } else {
            if ($num <= 0) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 发放量不合法';
                $flag = 1;
                Yii::app()->user->setFlash('num_error', '发放量不合法');
            }

            if (!preg_match('/^[1-9]\d*$/', $num)) { //判断匹配大于0的正整数正则
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 发放量不合法';
                $flag = 1;
                Yii::app()->user->setFlash('num_error', '发放量不合法');
            }
        }

        //验证提示操作
        if (!isset ($prompt) || empty ($prompt)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . ' 提示操作必填';
            $flag = 1;
            Yii::app()->user->setFlash('prompt_error', '提示操作必填');
        }

        //验证固定时间或相对时间
        if ($time_type == VALID_TIME_TYPE_FIXED) { //如果是固定时间
            if (empty($end_time) || empty($start_time)) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 固定时间必填';
                $flag = 1;
                Yii::app()->user->setFlash('valid_time_error', '固定时间必填');
            }
        } else { //如果是相对时间

        }

        //验证优惠说明
        if ($model->type == COUPON_TYPE_EXCHANGE) { //券类型是兑换券的时候进行验证
            if (empty($discount_illustrate)) {
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 优惠说明必填';
                $flag = 1;
                Yii::app()->user->setFlash('discount_illustrate_error', '优惠说明必填');
            }
        }

        //验证使用须知
        if (empty($use_illustrate)) {
            $result ['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . ' 使用须知必填';
            $flag = 1;
            Yii::app()->user->setFlash('use_illustrate_error', '使用须知必填');
        }

        if ($flag == 1) {
            $result ['errMsg'] = $errMsg;
            return json_encode($result);
        }

        $model->if_wechat = !empty($if_wechat) ? IF_WECHAT_YES : IF_WECHAT_NO; //是否同步到微信卡包 1不开启 2开启
        $model->title = $title; //券标题
        $model->merchant_short_name = $short_name;
        $model->vice_title = $vice_title; //副标题
        $model->discount = $discount / 10; //折扣数
        $model->prompt = $prompt; //提示操作
        $model->if_share = !empty($if_share) ? IF_SHARE_YES : IF_SHARE_NO; //用户是否可以分享领取链接 1可以 2不可以
        $model->if_give = !empty($if_give) ? IF_GIVE_YES : IF_GIVE_NO; //可否转增其他好友 1 能 2不能
        $model->num = $num; //发放数量
        $model->time_type = $time_type; //有效时间类型1固定时间 2相对时间
        if (!empty($start_time)) {//固定时间时的有效开始时间
            $model->start_time = $start_time . ' 00:00:00';
        }
        if (!empty($end_time)) {//固定时间有效结束时间
            $model->end_time = $end_time . ' 23:59:59';
        }

        if ($time_type == VALID_TIME_TYPE_RELATIVE) {
            $model->start_days = empty($start_days) ? 0 : $start_days; //领取后几天生效 当天代表0最高90天
            $model->effective_days = $effective_days;  //有效天数最少1天最多90天
        }
        $model->receive_num = $receive_num; //每个用户领取数量
        $model->mini_consumption = $mini_consumption; //最低消费
        $model->if_with_userdiscount = !empty($if_with_userdiscount) ? IF_WITH_USERDISCOUNT_YES : IF_WITH_USERDISCOUNT_NO; //是否能与会员折扣同用1不能 2能
        $model->tel = $tel; //客服电话
        $model->use_illustrate = $use_illustrate; //使用须知

        if ($model->type == COUPON_TYPE_EXCHANGE) {
            $model->discount_illustrate = $discount_illustrate;  //优惠说明
        } elseif ($model->type == COUPON_TYPE_DISCOUNT) {
            if (!empty($mini_consumption)) {
                $model->discount_illustrate = $discount . '折折扣券' . ',' . '满' . $mini_consumption . '元可用';
            } else {
                $model->discount_illustrate = $discount . '折折扣券' . ',' . '任意金额可用';
            }
        } else {
            if (empty ($if_wechat)) {
                if (!empty ($money_type)) { // 固定金额
                    if (!empty ($mini_consumption)) {
                        $model->discount_illustrate = $money . '元代金券' . ',' . '满' . $mini_consumption . '元可用';
                    } else {
                        $model->discount_illustrate = $money . '元代金券' . ',' . '任意金额可用';
                    }
                } else { // 随机金额
                    if (!empty ($mini_consumption)) {
                        $model->discount_illustrate = $start_money . '元到' . $end_money . '元随机券' . ',' . '满' . $mini_consumption . '元可用';
                    } else {
                        $model->discount_illustrate = $start_money . '元到' . $end_money . '元随机券' . ',' . '任意金额可用';
                    }
                }
            } else {
                if (!empty ($mini_consumption)) {
                    $model->discount_illustrate = $money . '元代金券' . ',' . '满' . $mini_consumption . '元可用';
                } else {
                    $model->discount_illustrate = $money . '元代金券' . ',' . '任意金额可用';
                }
            }
        }

        if (empty($if_wechat)) {
            $model->money_type = !empty($money_type) ? FACE_VALUE_TYPE_FIXED : FACE_VALUE_TYPE_RANDOM; //券金额类型 1固定 2随机
        } else {
            $model->money_type = FACE_VALUE_TYPE_FIXED;
        }
        if (!empty($start_money) && !empty($end_money)) {
            $model->money_random = ',' . $start_money . ',' . $end_money . ','; //代金券的随机金额，两金额用逗号分开
        }

        $model->money = $money; //代金券的固定金额
        $model->color = $color; //券颜色
        $model->use_restriction = empty($use_restriction) ? '1' : $use_restriction; //使用限制
        //echo $store_limit.'a';exit;
        if (empty($store_limit)) {
            $storeId = '';
            $store = Store::model()->findAll('flag=:flag and merchant_id=:merchant_id', array(':flag' => FLAG_NO, ':merchant_id' => $merchant_id));
            foreach ($store as $key => $value) {
                $storeId = $storeId . ',' . ($value->id);
            }
            $model->store_limit = $storeId . ',';
        } else {
            $model->store_limit = $store_limit . ',';
        }

        //调用卡券接口
        $coupon = Coupons::model()->findByPk($coupons_id);
        if ($coupon['if_wechat'] == IF_WECHAT_YES) {
            //如果原来是同步微信的     现在（!empty($if_wechat)）也是同步的   调用修改卡券接口
            if (!empty($if_wechat)) {
                //调用修改卡券接口
                $apiResult = $this->cardUpdate($coupons_id, $if_wechat, $title, $vice_title, $discount, $prompt,
                    $if_share, $if_give, $num, $time_type, $start_time, $end_time, $start_days,
                    $effective_days, $receive_num, $mini_consumption, $if_with_userdiscount, $tel,
                    $use_illustrate, $discount_illustrate, $money_type, $start_money, $end_money, $money, $store_limit, $color);
                if ($apiResult['errcode'] == 0) { //微信返回成功码为0
                    if ($model->save()) {
                        $result ['status'] = ERROR_NONE; // 状态码
                        $result ['errMsg'] = ''; // 错误信息
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                        $result ['errMsg'] = '数据保存失败'; // 错误信息
                    }
                } else {
                    $result ['status'] = ERROR_EXCEPTION; // 状态码
                    $result ['errMsg'] = '微信接口返回错误信息' . $apiResult['errcode']; // 错误信息
                }

            } else { //如果原来是同步微信的     现在（empty($if_wechat)）不同步的   调用删除卡券接口
                $apiResult3 = $this->cardDelete($coupons_id);
                if ($apiResult3['errcode'] == 0) { //微信返回成功码为0
                    $model->if_wechat = IF_WECHAT_NO;
                    $model->card_id = '';
                    if ($model->save()) {
                        $result ['status'] = ERROR_NONE; // 状态码
                        $result ['errMsg'] = ''; // 错误信息
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                        $result ['errMsg'] = '数据保存失败'; // 错误信息
                    }
                } else {
                    $result ['status'] = ERROR_EXCEPTION; // 状态码
                    $result ['errMsg'] = '微信接口返回错误信息' . $apiResult3['errcode']; // 错误信息
                }
            }
        } else {
            //如果原来不是同步微信的     现在（!empty($if_wechat)）是同步的   调用创建卡券接口
            if (!empty($if_wechat)) {
                //调用创建卡券接口
                $logo_url = $this->getLogoUrl($coupon->id, $coupon->color);
                $apiResult2 = $this->getCardId($coupon->id, $coupon->color, $logo_url, $this->getToken());
                if ($apiResult2['errcode'] == 0) { //微信成功返回码0
                    $model['card_id'] = $apiResult2['card_id'];
                    if ($model->save()) {
                        $result ['status'] = ERROR_NONE; // 状态码
                        $result ['errMsg'] = ''; // 错误信息
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                        $result ['errMsg'] = '数据保存失败'; // 错误信息
                    }
                } else {
                    $result ['status'] = ERROR_EXCEPTION; // 状态码
                    $result ['errMsg'] = '微信接口返回错误信息' . $apiResult2['errcode']; // 错误信息
                }

            } else { //如果原来不是同步微信的     现在（empty($if_wechat)）不同步的   不调用接口
                if ($model->save()) {
                    $result ['status'] = ERROR_NONE; // 状态码
                    $result ['errMsg'] = ''; // 错误信息
                } else {
                    $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                    $result ['errMsg'] = '数据保存失败'; // 错误信息
                }
            }
        }
        return json_encode($result);
    }

    /**
     * 获取商户名称
     * $merchant_id  商户id
     */
    public function getMerchantName($merchant_id)
    {
        $name = '';
        $model = Merchant::model()->findByPk($merchant_id);
        if (!empty($model)) {
            if (!empty($model['name'])) {
                $name = $model['name'];
            }
        }
        return $name;
    }

    /**
     * 获取商户Logo
     * $merchant_id  商户id
     */
    public function getMerchantLogo($merchant_id)
    {
        $logo_img = '';
        $model = Onlineshop::model()->find('merchant_id=:merchant_id and flag=:flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        if (!empty($model)) {
            if (!empty($model['logo_img'])) {
                $logo_img = $model['logo_img'];
            }
        }
        return $logo_img;
    }

    /**
     * 修改卡券审核状态字段
     * $cardId    卡券cardId
     * $card_check   审核状态
     */
    public function editCheckStatus($cardId, $card_check)
    {
        $result = array();
        try {
            $model = Coupons::model()->find('card_id = :card_id', array(':card_id' => $cardId));
            if (empty ($model)) {
                $result ['status'] = ERROR_NO_DATA;
                throw new Exception ('查询的数据不存在');
            }
            $model ['status'] = $card_check == 'card_pass_check' ? WX_CHECK_PASS : WX_CHECK_NOTPASS;
            if ($model->update()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = ''; // 错误信息
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                $result ['errMsg'] = '数据保存失败'; // 错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
            Yii::log('卡券事件推送错误信息:' . $result['errMsg'], 'warning');
        }

    }


    /**
     * 获取卡券列表
     * $merchant_id  商户id
     * $key_woed     输入优惠券名称搜索
     */
    public function getCardCouponsList($merchant_id, $key_woed)
    {
        $result = array();
        try {

            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);

            //输入优惠券名称搜索
            if (!empty($key_woed)) {
// 				$criteria -> addCondition('title=:title');
// 				$criteria -> params[':title'] = $key_woed;
                $criteria->addSearchCondition('title', $key_woed);
            }

            $criteria->order = 'create_time desc';

            //分页
            $pages = new CPagination(Coupons::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $model = Coupons::model()->findall($criteria);
            $data = array();
            if (!empty($model)) {
                foreach ($model as $k => $v) {
                    $data['list'][$k]['id'] = $v['id'];
                    $data['list'][$k]['type'] = $v['type']; //券类型
                    $data['list'][$k]['if_wechat'] = $v['if_wechat']; //是否同步到微信卡包 1不开启 2开启
                    $data['list'][$k]['title'] = $v['title']; //券标题
                    $data['list'][$k]['vice_title'] = $v['vice_title']; //副标题
                    $data['list'][$k]['money_type'] = $v['money_type']; //券金额类型 1固定 2随机
                    $data['list'][$k]['money_random'] = $v['money_random']; //代金券的随机金额
                    $data['list'][$k]['money'] = $v['money']; //代金券的固定金额
                    $data['list'][$k]['discount'] = $v['discount']; //券折扣
                    $data['list'][$k]['prompt'] = $v['prompt']; //提示操作
                    $data['list'][$k]['if_share'] = $v['if_share']; //用户是否可以分享领取链接 1可以 2不可以
                    $data['list'][$k]['if_give'] = $v['if_give']; //可否转增其他好友 1 能 2不能
                    $data['list'][$k]['num'] = $v['num']; //发放数量
                    $data['list'][$k]['get_num'] = $v['get_num']; //已领取数量
                    $data['list'][$k]['time_type'] = $v['time_type']; //有效时间类型1固定时间 2相对时间
                    $data['list'][$k]['start_time'] = $v['start_time']; //固定时间时的有效开始时间
                    $data['list'][$k]['end_time'] = $v['end_time']; //固定时间有效结束时间
                    $data['list'][$k]['start_days'] = $v['start_days']; //领取后几天生效 当天代表0最高90天
                    $data['list'][$k]['effective_days'] = $v['effective_days']; //有效天数最少1天最多90天
                    $data['list'][$k]['receive_num'] = $v['receive_num']; //每个用户领取数量
                    $data['list'][$k]['mini_consumption'] = $v['mini_consumption']; //最低消费
                    $data['list'][$k]['use_restriction'] = $v['use_restriction']; //使用限制
                    $data['list'][$k]['if_with_userdiscount'] = $v['if_with_userdiscount']; //是否能与会员折扣同用1不能 2能
                    $data['list'][$k]['store_limit'] = $v['store_limit']; //门店限制
                    $data['list'][$k]['tel'] = $v['tel']; //客服电话
                    $data['list'][$k]['use_illustrate'] = $v['use_illustrate']; //使用须知
                    $data['list'][$k]['discount_illustrate'] = $v['discount_illustrate']; //优惠说明
                    $data['list'][$k]['if_invalid'] = $v['if_invalid']; //是否失效 1未失效 2已失效
                    $data['list'][$k]['create_time'] = $v['create_time']; //创建时间
                    $data['list'][$k]['get_receive_num'] = $this->getReceiveNum($v['id']); //券领取次数
                    $data['list'][$k]['use_receive_num'] = $this->getUseReceiveNum($v['id']); //券使用次数
                    $data['list'][$k]['receive_per'] = $this->getReceivePer($v['id']); //券领取人数
                    $data['list'][$k]['status'] = $v['status']; //微信审核状态   1审核中 2已通过 3未通过
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            } else {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息 
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取单张券领取人数
     * $coupons_id  券id
     */
    public function getReceivePer($coupons_id)
    {
        $model = UserCoupons::model()->countBySql('select count(distinct user_id) as num from wq_user_coupons where coupons_id =:coupons_id', array(':coupons_id' => $coupons_id));
        //$sql = 'select count(distinct user_id) as num from wq_user_coupons where coupons_id ='.$coupons_id;
        //$model = Yii::app()->db->createCommand($sql)->queryAll();
        return $model;//[0]['num'];
    }

    /**
     * 获取券领取次数
     *  $coupons_id  券id
     */
    public function getReceiveNum($coupons_id)
    {
        $count = 0;
        $model = UserCoupons::model()->findAll('coupons_id=:coupons_id and flag=:flag', array(':coupons_id' => $coupons_id, ':flag' => FLAG_NO));
        if (!empty($model)) {
            $count = count($model);
        }
        return $count;
    }

    /**
     * 获取领取的券使用的数量
     *  $coupons_id  券id
     */
    public function getUseReceiveNum($coupons_id)
    {
        $count = 0;
        $model = UserCoupons::model()->findAll('coupons_id=:coupons_id and flag=:flag and status=:status'
            , array(':coupons_id' => $coupons_id, ':flag' => FLAG_NO, ':status' => COUPONS_USE_STATUS_USED));
        if (!empty($model)) {
            $count = count($model);
        }
        return $count;
    }

    /**
     * 使失效操作
     * $coupons_id  券id
     */
    public function invalid($coupons_id)
    {
        $result = array();
        try {
            $model = Coupons::model()->findByPk($coupons_id);
            //	if($model -> if_wechat == IF_WECHAT_NO){ //如果该券没有同步到微信的
            if (empty ($model)) {
                $result ['status'] = ERROR_NO_DATA;
                throw new Exception ('删除的数据不存在');
            }
            $model ['if_invalid'] = IF_INVALID_YES;
            if ($model->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = ''; // 错误信息
                $result ['data'] = '';
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                $result ['errMsg'] = '数据保存失败'; // 错误信息
                $result ['data'] = '';
            }
            //}
// 			else{  //如果该券有同步到微信的 

// 			}
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 修改剩余量
     * $coupons_id  券id
     * $edit_num    剩余量
     */
    public function editNum($coupons_id, $edit_num)
    {
        $result = array();
        try {
            $model = Coupons::model()->findByPk($coupons_id);
            if ($model->if_wechat == IF_WECHAT_NO) { //如果该券没有同步到微信的
                if (empty ($model)) {
                    $result ['status'] = ERROR_NO_DATA;
                    throw new Exception ('删除的数据不存在');
                }
                $model ['num'] = $edit_num;
                if ($model->save()) {
                    $result ['status'] = ERROR_NONE; // 状态码
                    $result ['errMsg'] = ''; // 错误信息
                    $result ['data'] = '';
                } else {
                    $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                    $result ['errMsg'] = '数据保存失败'; // 错误信息
                    $result ['data'] = '';
                }
            } else { //如果该券有同步到微信的    则调用修改库存接口
                $apiResult = $this->cardModifystock($coupons_id, $edit_num);
                if ($apiResult['errcode'] == 0) {  //微信返回成功码
                    if (empty ($model)) {
                        $result ['status'] = ERROR_NO_DATA;
                        throw new Exception ('删除的数据不存在');
                    }
                    $model ['num'] = $edit_num;
                    if ($model->save()) {
                        $result ['status'] = ERROR_NONE; // 状态码
                        $result ['errMsg'] = ''; // 错误信息
                        $result ['data'] = '';
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                        $result ['errMsg'] = '数据保存失败'; // 错误信息
                        $result ['data'] = '';
                    }
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 券详情
     * $coupons_id  券id
     */
    public function detail($coupons_id)
    {
        $result = array();
        $data = array();
        try {
            $model = Coupons::model()->findByPk($coupons_id);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('删除的数据不存在');
            } else {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';

                $data['list']['id'] = $model['id'];
                $data['list']['merchant_short_name'] = $model['merchant_short_name'];
                $data['list']['type'] = $model['type']; //券类型
                $data['list']['if_wechat'] = $model['if_wechat']; //是否同步到微信卡包 1不开启 2开启
                $data['list']['title'] = $model['title']; //券标题
                $data['list']['vice_title'] = $model['vice_title']; //副标题
                $data['list']['money_type'] = $model['money_type']; //券金额类型 1固定 2随机
                $data['list']['money_random'] = $model['money_random']; //代金券的随机金额
                $data['list']['money'] = $model['money']; //代金券的固定金额
                $data['list']['discount'] = $model['discount']; //券折扣
                $data['list']['prompt'] = $model['prompt']; //提示操作
                $data['list']['if_share'] = $model['if_share']; //用户是否可以分享领取链接 1可以 2不可以
                $data['list']['if_give'] = $model['if_give']; //可否转增其他好友 1 能 2不能
                $data['list']['num'] = $model['num']; //发放数量
                $data['list']['get_num'] = $model['get_num']; //已领取数量
                $data['list']['time_type'] = $model['time_type']; //有效时间类型1固定时间 2相对时间
                $data['list']['start_time'] = $model['start_time']; //固定时间时的有效开始时间
                $data['list']['end_time'] = $model['end_time']; //固定时间有效结束时间
                $data['list']['start_days'] = $model['start_days']; //领取后几天生效 当天代表0最高90天
                $data['list']['effective_days'] = $model['effective_days']; //有效天数最少1天最多90天
                $data['list']['receive_num'] = $model['receive_num']; //每个用户领取数量
                $data['list']['mini_consumption'] = $model['mini_consumption']; //最低消费
                $data['list']['use_restriction'] = $model['use_restriction']; //使用限制
                $data['list']['if_with_userdiscount'] = $model['if_with_userdiscount']; //是否能与会员折扣同用1不能 2能
                $data['list']['store_limit'] = $model['store_limit']; //门店限制
                $data['list']['tel'] = $model['tel']; //客服电话
                $data['list']['use_illustrate'] = $model['use_illustrate']; //使用须知
                $data['list']['discount_illustrate'] = $model['discount_illustrate']; //优惠说明
                $data['list']['if_invalid'] = $model['if_invalid']; //是否失效 1未失效 2已失效
                $data['list']['create_time'] = $model['create_time']; //创建时间
                $data['list']['color'] = $model['color']; //券颜色
                $data['list']['store_limit_name'] = $this->getStoreName($model['store_limit']); //门店限制名称
                $data['list']['merchant_name'] = $this->getMerchantName($model['merchant_id']); //商户名称
                $data['list']['merchant_logo'] = $this->getMerchantLogo($model['merchant_id']); //商户logo
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        $result['data'] = $data;
        return json_encode($result);
    }

    /**
     * 添加门店
     * $merchant_id  商户id
     * $key_word     门店名搜索
     */
    public function addStore($merchant_id, $key_word)
    {
        $result = array();
        $data = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);

            //门店名搜索
            if (!empty($key_word)) {
                $criteria->addCondition('name=:name');
                $criteria->params[':name'] = $key_word;
            }

            $criteria->order = 'create_time desc';

            //分页
            $pages = new CPagination(Store::model()->count($criteria));
            $pages->pageSize = 5;
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $store = Store::model()->findAll($criteria);
            if (!empty($store)) {
                foreach ($store as $k => $v) {
                    $data['list'][$k]['id'] = $v['id'];
                    $data['list'][$k]['name'] = $v['name']; //门店名
                    $data['list'][$k]['address'] = $v['address']; //门店地址
                }
                $result['data'] = $data;
                $result ['status'] = ERROR_NONE;
            } else {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取门店总数
     */
    public function getCountStore($merchant_id)
    {
        $model = Store::model()->findAll('merchant_id = :merchant_id and flag =:flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        return count($model);
    }

    /**
     * 根据主键获取门店信息
     * $store_id  门店id
     */
    public function getStore($store_id)
    {
        $result = array();
        $data = array();
        try {
            $model = Store::model()->findByPk($store_id);
            if (!empty($model)) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';

                $data['list']['id'] = $model['id'];
                $data['list']['name'] = $model['name'];
            } else {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        $result['data'] = $data;
        return json_encode($result);
    }

    /**
     * 延长固定日期
     * $coupons_id  券id
     * $date        修改后的固定日期
     */
    public function extendedTime($coupons_id, $date)
    {
        $result = array();
        $date_arr = explode('-', $date);

        try {
            $model = Coupons::model()->findByPk($coupons_id);
            if ($model->if_wechat == IF_WECHAT_NO) { //如果没有同步到微信的
                $model->end_time = $date_arr[1] . ' 23:59:59';
                if ($model->save()) {
                    $result ['status'] = ERROR_NONE; // 状态码
                    $result ['errMsg'] = ''; // 错误信息
                } else {
                    $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                    $result ['errMsg'] = '数据保存失败'; // 错误信息
                }
            } else { //如果同步到微信的   调用修改接口
                $apiResult = $this->cardUpdateForDate($model->card_id, $date, $model->type);
                if ($apiResult['errcode'] == 0) {
                    $model->end_time = $date_arr[1] . ' 23:59:59';
                    if ($model->save()) {
                        $result ['status'] = ERROR_NONE; // 状态码
                        $result ['errMsg'] = ''; // 错误信息
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                        $result ['errMsg'] = '数据保存失败'; // 错误信息
                    }
                } else {
                    $result ['status'] = ERROR_EXCEPTION; // 状态码
                    $result ['errMsg'] = '微信接口返回错误信息' . $apiResult['errcode']; // 错误信息
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 延长固定日期接口
     * $card_id     微信卡券id
     * $date        修改后的固定日期
     * $type        券类型
     */
    public function cardUpdateForDate($card_id, $date, $type)
    {
        $date_arr = explode('-', $date);
        $begin_timestamp = strtotime($date_arr[0] . ' 00:00:00'); //固定时间开始时间的时间戳
        $end_timestamp = strtotime($date_arr[1] . ' 23:59:59');  //固定时间结束时间的时间戳

        //生成提交给微信服务器的数据BEGINING
        $array = array();
        $array['card_id'] = $card_id;


        $base_info = array();


        $base_info['date_info'] = array('type' => 'DATE_TYPE_FIX_TIME_RANGE', 'begin_timestamp' => $begin_timestamp, 'end_timestamp' => $end_timestamp);

        if ($type == COUPON_TYPE_CASH) {
            $array['cash']['base_info'] = $base_info;
        } elseif ($type == COUPON_TYPE_DISCOUNT) {
            $array['discount']['base_info'] = $base_info;
        } else {
            $array['gift']['base_info'] = $base_info;
        }

        //生成提交给微信服务器的数据END

        $url = 'https://api.weixin.qq.com/card/update?access_token=' . $this->getToken();
        $postData = json_encode($array);
        $json_data = $this->postData($url, $postData);
        $json_data = json_decode($json_data, true);
        //var_dump($json_data);exit;
        return $json_data;
    }

    /**
     * 获取logo_url
     * $coupons_id  券id
     * $color       券颜色
     */
    public function getLogoUrl($coupons_id, $color)
    {
        $merchant_id = Yii::app()->session['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        $onlineshop = Onlineshop::model()->find('merchant_id=:merchant_id', array(':merchant_id' => $merchant_id));
        $logo_img = '@' . YII::app()->basePath . '/../../upload/images/gj/source/' . $onlineshop->logo_img;//'@http://upload.test.51wanquan.com/images/gj/source/20150921/150921093404282586.jpg';

        //获取token
        $access_token = $this->getToken();

        $postdata = array('buffer' => ($logo_img));
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=' . $access_token;
        $json_data = $this->postData($url, $postdata);
        $json_data = json_decode($json_data, true);
        return $json_data['url'];
        //return $this->getCardId($coupons_id,$color,$json_data['url'],$access_token['access_token']);
    }

    /**
     * 获取imageurl
     */
    public function getImageUrl($image_url)
    {

        $img = '@' . YII::app()->basePath . '/../../upload/images/gj/source/' . $image_url;//'@http://upload.test.51wanquan.com/images/gj/source/20150921/150921093404282586.jpg';

        //获取token
        $access_token = $this->getToken();

        $postdata = array('buffer' => ($img));
        $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=' . $access_token;
        $json_data = $this->postData($url, $postdata);
        $json_data = json_decode($json_data, true);
        return $json_data['url'];
        //return $this->getCardId($coupons_id,$color,$json_data['url'],$access_token['access_token']);
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        $merchant_id = Yii::app()->session['merchant_id'];
        $merchant = Merchant::model()->findByPk($merchant_id);
        //获取access_token
        $access_token = Wechat::getTokenByMerchant($merchant);
        return $access_token;
    }

    /**
     * 创建卡券接口   返回card_id
     * $coupons_id  券id
     * $color       券颜色
     * $logo_url    卡券logo  Url
     * $access_token    token
     */
    public function getCardId($coupons_id, $color, $logo_url, $access_token)
    {
        $model = Coupons::model()->findByPk($coupons_id);
        $card_type = '';
        $time_type = '';
        $begin_timestamp = ''; //固定时间开始时间的时间戳
        $end_timestamp = '';  //固定时间结束时间的时间戳
        $fixed_term = '';    //相对时间有效天数
        $fixed_begin_term = '';  //相对时间领取后几天生效 
        //DATE_TYPE_FIX_TIME_RANGE 表示固定日期区间，DATE_TYPE_FIX_TERM表示固定时长（自领取后按天算
        if ($model->time_type == VALID_TIME_TYPE_FIXED) {
            $time_type = 'DATE_TYPE_FIX_TIME_RANGE';
            $begin_timestamp = strtotime($model->start_time);
            $end_timestamp = strtotime($model->end_time);
        } else {
            $time_type = 'DATE_TYPE_FIX_TERM';
            $fixed_begin_term = $model->start_days;
            $fixed_term = $model->effective_days;
        }
        //券类型。
        if ($model->type == COUPON_TYPE_CASH) {
            $card_type = 'CASH';
        } elseif ($model->type == COUPON_TYPE_DISCOUNT) {
            $card_type = 'DISCOUNT';
        } else {
            $card_type = 'GIFT';
        }

        //卡券领取页面是否可分享。
        $can_share = true;
        if ($model->if_share == IF_SHARE_YES) {
            $can_share = true;
        } else {
            $can_share = false;
        }

        //卡券是否可转赠
        $can_give_friend = true;
        if ($model->if_give == IF_GIVE_YES) {
            $can_give_friend = true;
        } else {
            $can_give_friend = false;
        }

        //生成提交给微信服务器的数据BEGINING
        $array = array();
        $array['card']['card_type'] = $card_type;

        $groupon = array();


        $base_info = array();
        $base_info['logo_url'] = $logo_url;
        $base_info['brand_name'] = urlencode($model->merchant_short_name);
        $base_info['code_type'] = 'CODE_TYPE_BARCODE'; //一维码 
        $base_info['title'] = urlencode($model->title);
        $base_info['sub_title'] = urlencode($model->vice_title);
        $base_info['color'] = $color;
        $base_info['notice'] = urlencode($model->prompt);
        $base_info['service_phone'] = ($model->tel);
        $base_info['description'] = urlencode($model->use_illustrate);

        if ($model->time_type == VALID_TIME_TYPE_FIXED) {
            $base_info['date_info'] = array('type' => 'DATE_TYPE_FIX_TIME_RANGE', 'begin_timestamp' => $begin_timestamp, 'end_timestamp' => $end_timestamp);
        } else {
            $base_info['date_info'] = array('type' => 'DATE_TYPE_FIX_TERM', 'fixed_term' => $fixed_term, 'fixed_begin_term' => $fixed_begin_term);
        }

        $base_info['sku'] = array('quantity' => $model->num);
        $base_info['get_limit'] = $model->receive_num;
        $base_info['use_custom_code'] = false;
        $base_info['bind_openid'] = false;
// 		$base_info['custom_url_name'] = '入口1';
// 		$base_info['custom_url_sub_title'] ='自定义入口1';
// 		$base_info['custom_url'] = 'http://www.baidu.com';
// 		$base_info['promotion_url_name'] = '更多优惠';
// 		$base_info['promotion_url_sub_title'] = '营销活动1';
// 		$base_info['promotion_url'] = 'http://www.baidu.com';
        $base_info['can_share'] = $can_share;
        $base_info['can_give_friend'] = $can_give_friend;


        $groupon['base_info'] = $base_info;

        //券类型不同    参数有所不同
        if ($model->type == COUPON_TYPE_CASH) {
            $groupon['least_cost'] = ($model->mini_consumption) * 100; //代金券专用，表示起用金额（单位为分）,如果无起用门槛则填0。
            $groupon['reduce_cost'] = ($model->money) * 100; //代金券专用，表示减免金额。（单位为分）
        } elseif ($model->type == COUPON_TYPE_DISCOUNT) {
            $groupon['discount'] = 100 - ($model->discount) * 100; //折扣券专用，表示打折额度（百分比）。填30就是七折
        } else {
            $groupon['gift'] = urlencode($model->discount_illustrate);
        }

        if ($model->type == COUPON_TYPE_CASH) { //代金券
            $array['card']['cash'] = $groupon;
        } elseif ($model->type == COUPON_TYPE_DISCOUNT) { //折扣券
            $array['card']['discount'] = $groupon;
        } else { //兑换券
            $array['card']['gift'] = $groupon;
        }

        //生成提交给微信服务器的数据END

        $url = 'https://api.weixin.qq.com/card/create?access_token=' . $access_token;
        //用PHP的json_encode来处理中文的时候，中文都会被编码，变成不可读的, 类似”/u***”的格式，微信服务器端不能接受。
        //PHP 5.4中对json_encode对options可选参数增加JSON_UNESCAPED_UNICODE常量，即不编码为unicode
        $postData = urldecode(json_encode($array));

        $json_data = $this->postData($url, $postData);
        $json_data = json_decode($json_data, true);
        //var_dump($json_data);exit;

        return $json_data;

    }

    /**
     * 修改卡券接口
     */
    public function cardUpdate($coupons_id, $if_wechat, $title, $vice_title, $discount, $prompt,
                               $if_share, $if_give, $num, $time_type, $start_time, $end_time, $start_days,
                               $effective_days, $receive_num, $mini_consumption, $if_with_userdiscount, $tel,
                               $use_illustrate, $discount_illustrate, $money_type, $start_money, $end_money, $money, $store_limit, $color)
    {

        $model = Coupons::model()->findByPk($coupons_id);

        $begin_timestamp = ''; //固定时间开始时间的时间戳
        $end_timestamp = '';  //固定时间结束时间的时间戳

        if (!empty($start_time) && !empty($end_time)) {
            $start_time = $start_time . ' 00:00:00';
            $end_time = $end_time . ' 23:59:59';
            $begin_timestamp = strtotime($start_time);
            $end_timestamp = strtotime($end_time);
        }

        if (!empty($if_share)) { //卡券原生领取页面是否可分享。
            $if_share = true;
        } else { //卡券是否可转赠。
            $if_share = false;
        }

        if (!empty($if_give)) {
            $if_give = true;
        } else {
            $if_give = false;
        }

        //生成提交给微信服务器的数据BEGINING
        $array = array();
        $array['card_id'] = $model['card_id'];


        $base_info = array();
        $base_info['logo_url'] = $this->getLogoUrl($coupons_id, $model->color);
        //echo $base_info['logo_url'];exit;
        $base_info['code_type'] = 'CODE_TYPE_BARCODE';
        $base_info['color'] = $color;
        $base_info['notice'] = urlencode($prompt);
        $base_info['service_phone'] = $tel;
        $base_info['description'] = urlencode($use_illustrate);

        if ($model->time_type == VALID_TIME_TYPE_FIXED) {
            $base_info['date_info'] = array('type' => 'DATE_TYPE_FIX_TIME_RANGE', 'begin_timestamp' => $begin_timestamp, 'end_timestamp' => $end_timestamp);
        } else {
            //$base_info['date_info'] = array('type'=>'DATE_TYPE_FIX_TERM','fixed_term'=>'','fixed_begin_term'=>'');
        }

        //$base_info['sku'] = array('quantity'=>$model->num);
        $base_info['get_limit'] = $receive_num;
        //$base_info['use_custom_code'] = false;
        //$base_info['bind_openid'] = false;
        $base_info['can_share'] = $if_share;
        $base_info['can_give_friend'] = $if_give;

        if ($model->type == COUPON_TYPE_CASH) {
            $array['cash']['base_info'] = $base_info;
        } elseif ($model->type == COUPON_TYPE_DISCOUNT) {
            $array['discount']['base_info'] = $base_info;
        } else {
            $array['gift']['base_info'] = $base_info;
        }

        //生成提交给微信服务器的数据END

        $url = 'https://api.weixin.qq.com/card/update?access_token=' . $this->getToken();
        $postData = urldecode(json_encode($array));
        $json_data = $this->postData($url, $postData);
        $json_data = json_decode($json_data, true);
        //var_dump($json_data);exit;
        return $json_data;

    }

    /**
     * 删除卡券接口
     * $coupons_id  券id
     */
    public function cardDelete($coupons_id)
    {
        $model = Coupons::model()->findByPk($coupons_id);
        $card_id = $model['card_id'];
        $url = 'https://api.weixin.qq.com/card/delete?access_token=' . $this->getToken();
        $postData = array('card_id' => $card_id);
        $postData = json_encode($postData);
        $json_data = $this->postData($url, $postData);
        $json_data = json_decode($json_data, true);
        //var_dump($json_data);exit;
        return $json_data;
    }

    /**
     * 修改卡券库存接口
     * @param $coupons_id  券id
     * @param $edit_num = ''    新的库存量
     * @param $increase_stock_value 增加多少库存
     * @param $reduce_stock_value 减少多少库存
     * @param $access_token
     * @return mixed|type
     */
    public function cardModifystock($coupons_id, $edit_num = '', $increase_stock_value, $reduce_stock_value, $access_token)
    {
        $model = Coupons::model()->findByPk($coupons_id);
        $card_id = $model['card_id'];
        $old_edit_num = $model['num']; //原库存量

        $postData = array();
        if (!empty($edit_num)) {
            if ($edit_num > $old_edit_num) { //增加库存
                $increase_stock_value = $edit_num - $old_edit_num;
                $postData = array(
                    'card_id' => $card_id,
                    'increase_stock_value' => $increase_stock_value
                );
            } else { //减少库存
                $reduce_stock_value = $old_edit_num - $edit_num;
                $postData = array(
                    'card_id' => $card_id,
                    'reduce_stock_value' => $reduce_stock_value
                );
            }
        } else { //库存
            if (!empty($increase_stock_value)) { //增加库存
                $postData = array(
                    'card_id' => $card_id,
                    'increase_stock_value' => $increase_stock_value
                );
            } elseif (!empty($reduce_stock_value)) { //减少库存
                $postData = array(
                    'card_id' => $card_id, 
                    'reduce_stock_value' => $reduce_stock_value
                );
            }
        }

        $url = 'https://api.weixin.qq.com/card/modifystock?access_token=' . $access_token;

        $postData = json_encode($postData);
        $json_data = $this->postData($url, $postData);
        $json_data = json_decode($json_data, true);
        //var_dump($json_data);exit;
        return $json_data;
    }


    /**
     * 调用线下核销接口
     * 步骤一：查询Code接口
     * $code  单张卡券的唯一标准
     */
    public function getCode($code)
    {
        $result = array();
        try {
            $user_coupons = UserCoupons::model()->find('code = :code and flag = :flag', array(
                ':code' => $code,
                ':flag' => FLAG_NO
            ));

            $coupons = Coupons::model()->findByPk($user_coupons->coupons_id);
            if (empty($coupons)) {
                throw new Exception('卡券不存在');
            }
            $card_id = $coupons->card_id;
            Yii::app()->session['merchant_id'] = $coupons['merchant_id'];
            $url = 'https://api.weixin.qq.com/card/code/get?access_token=' . $this->getToken();
            $postData = array(
                'card_id' => $card_id,
                'code' => $code,
                'check_consume' => true
            );
            $postData = json_encode($postData);
            $json_data = $this->postData($url, $postData);
            $json_data = json_decode($json_data, true);
            //return $json_data;
            $result['status'] = ERROR_NONE;
            $result['errcode'] = $json_data['errcode'];
            $result['can_consume'] = $json_data['can_consume'];  //返回can_consume是否可以核销

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);

    }

    /**
     * 调用线下核销接口
     * 步骤二：核销Code接口
     * $code  单张卡券的唯一标准
     */
    public function consumeCoupons($code)
    {
        $result = array();
        try {
            $getCodeResult = json_decode($this->getCode($code), true); // 查询Code接口
            if ($getCodeResult ['errcode'] == 0) { // 查询Code接口返回正常状态
                if ($getCodeResult ['can_consume']) { // 如果可以核销
                    $url = 'https://api.weixin.qq.com/card/code/consume?access_token=' . $this->getToken();
                    $postData = array(
                        'code' => $code
                    );
                    $postData = json_encode($postData);
                    $json_data = $this->postData($url, $postData);
                    $json_data = json_decode($json_data, true);

                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                    $result['openId'] = $json_data['openid'];  //返回openid
                } else {
                    throw new Exception('该卡券不能核销');
                }
            } else {
                $result['status'] = $getCodeResult['errcode'];
                $result['errMsg'] = $getCodeResult['errMsg'];
                throw new Exception('微信返回错误信息：' . $getCodeResult['errMsg']);
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);

    }

    /**
     * post提交数据
     * @param type $url
     * @param type $data
     * @return type
     */
    function postData($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过SSL证书检查  https方式
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果成功只将结果返回，不自动输出任何内容。如果失败返回FALSE,不加这句返回始终是1

        curl_setopt($ch, CURLOPT_POST, 1); //启用POST提交
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $file_contents = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno' . curl_error($ch);
        }
        curl_close($ch);

        return $file_contents;
    }

    public function getData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过SSL证书检查  https方式
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);

        //释放curl句柄
        curl_close($ch);
        return $output;
    }

    //开通当前appid的券点
    public function cardPayActivate()
    {
        $access_token = $this->getToken();
        $url = 'https://api.weixin.qq.com/card/pay/activate?access_token=' . $access_token;
        $json_data = $this->getData($url);
        $result = json_decode($json_data, true);
        return $result;
    }


}