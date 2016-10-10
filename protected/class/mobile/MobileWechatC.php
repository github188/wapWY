<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
include_once $_SERVER['DOCUMENT_ROOT'] . '/protected/components/Component.php';


/**
 * 微信公众号类
 */
class MobileWechatC extends mainClass
{
    /**
     * 获取系统地址
     * @param $merchant_id              商户id
     */
    public function getSystemUrl($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            $model = Merchant::model()->findByPk($merchant_id);
            if ($model->wechat_thirdparty_authorizer_if_auth == 2)//授权方式
            {
                $model['wechat_subscription_appid'] = $model['wechat_thirdparty_authorizer_appid'];
            }
            $coupons = Coupons::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));

            if (!empty($model)) {
                $list[0] = "--请选择--";
                $member_center = USER_DOMAIN . '/memberCenter?encrypt_id=' . $model['encrypt_id'] . '&source=wechat';
                $shop = USER_DOMAIN . '/shop?encrypt_id=' . $model['encrypt_id'] . '&source=wechat';
                $book_operate = USER_DOMAIN . '/bookOperate?encrypt_id=' . $model['encrypt_id'] . '&source=wechat';
                $order = USER_DOMAIN . '/orderList?encrypt_id=' . $model['encrypt_id'] . '&source=wechat' . '&stored_confirm_status=' . ORDER_PAY_WAITFORCONFIRM;
                $hotel = WEB_APP_DOMAIN . '/yhotel/Hotel/index?encrypt_id=' . $model['encrypt_id'] . '&source=wechat';
                if (Yii::app()->session['merchant_id'] == TIANSHI_SHOP_API) {
                    $online_shop = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode(COMMODITY_DOMAIN_DQH . '/index?encrypt_id=' . $model['encrypt_id'] . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                    $activity_sdlj = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode(COMMODITY_DOMAIN_DQH_MARKETING . '/FirstSingle?encrypt_id=' . $model['encrypt_id'] . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                    $list[$activity_sdlj] = '首单立减';
                    $activity_zfl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode(COMMODITY_DOMAIN_DQH_MARKETING . '/WelfareList?encrypt_id=' . $model['encrypt_id'] . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                    $list[$activity_zfl] = '粉丝周福利';
                    $activity_dqh = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode(COMMODITY_DOMAIN_DQH_MARKETING . '/WelfareDetail?encrypt_id=' . $model['encrypt_id'] . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                    $list[$activity_dqh] = '首单立减+粉丝周福利';
                } else {
                    $online_shop = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode(COMMODITY_DOMAIN . '/index?encrypt_id=' . $model['encrypt_id'] . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                }
                $list[$online_shop] = '在线商城';
                $list[$member_center] = '会员中心';
                $list[$shop] = '在线商铺';
                $list[$book_operate] = '预订';
                $list[$order] = '我的订单';
                $list[$hotel] = '订房';
                if (!empty($coupons)) {
                    foreach ($coupons as $key => $value) {
                        $redirect_uri = USER_DOMAIN_COUPONS . '/newGetCouponOne?encrypt_id=' . $model['encrypt_id'] . '&coupon_id=' . $value['id'] . '&source=wechat';
                        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                        $list[$url] = $value['title'];
                    }
                }
                //应用活动
                if ($model['wechat_type'] == WECHAT_TYPE_SERVICE_AUTH) {
                    $now = date("Y-m-d h:i:s");
                    $promotions = Activity::model()->findAll('merchant_id = :merchant_id and flag = :flag and end_time > :now', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':now' => $now));
                    if (!empty($promotions)) {
                        foreach ($promotions as $k => $v) {
                            $activity_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $model['wechat_subscription_appid'] . '&redirect_uri=' . urlencode(PROMOTIONS_DOMAIN . '/promotionsActivity?encrypt_id=' . $model['encrypt_id'] . '&promotions_id=' . $v['id'] . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                            if ($v['type'] == PROMOTIONS_TYPE_TURNTABLE) {  //大转盘活动
                                $list[$activity_url] = '大转盘——' . $v['name'];
                            } elseif ($v['type'] == PROMOTIONS_TYPE_SCRATCH) {  //刮刮卡
                                $list[$activity_url] = '刮刮卡——' . $v['name'];
                            }
                        }
                    }
                }

                $result['data'] = $list;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '未找到该商户信息'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取素材url
     * @param $merchant_id
     */
    public function getMaterialUrl($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            $model = Merchant::model()->findByPk($merchant_id);
            $coupons = Coupons::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if (!empty($model)) {
                $list[0] = "--请选择--";
                $list[USER_DOMAIN . '/memberCenter?encrypt_id=' . $model['encrypt_id'] . '&source=wechat'] = '会员中心';
                $list[USER_DOMAIN . '/shop?encrypt_id=' . $model['encrypt_id'] . '&source=wechat'] = '在线商铺';
                $list[USER_DOMAIN . '/bookOperate?encrypt_id=' . $model['encrypt_id'] . '&source=wechat'] = '预定';
                $list[USER_DOMAIN . '/orderList?encrypt_id=' . $model['encrypt_id'] . '&source=wechat' . '&stored_confirm_status=' . ORDER_PAY_WAITFORCONFIRM] = '我的订单';

                if (!empty($coupons)) {
                    foreach ($coupons as $key => $value) {
                        $url = USER_DOMAIN . '/lookCoupons?encrypt_id=' . $model['encrypt_id'] . '&coupons_id=' . $value['id'] . '&coupons_type=' . $value['type'] . '&source=wechat';
                        $list[$url] = $value['title'];
                    }
                }

                $now = date("Y-m-d h:i:s");
                $promotions = Activity::model()->findAll('merchant_id = :merchant_id and flag = :flag and end_time > :now', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':now' => $now));
                if (!empty($promotions)) {
                    foreach ($promotions as $k => $v) {
                        $activity_url = PROMOTIONS_DOMAIN . '/promotionsActivity?encrypt_id=' . $model['encrypt_id'] . '&promotions_id=' . $v['id'] . '&source=wechat';
                        if ($v['type'] == PROMOTIONS_TYPE_TURNTABLE) {  //大转盘活动
                            $list[$activity_url] = '大转盘——' . $v['name'];
                        } elseif ($v['type'] == PROMOTIONS_TYPE_SEARCH) {  //刮刮卡
                            $list[$activity_url] = '刮刮卡——' . $v['name'];
                        }
                    }
                }

                $result['data'] = $list;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '未找到该商户信息'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取图文素材列表
     * @param $merchant_id
     */
    public function getMaterialDownList($merchant_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('*');
            $cmd->from(array('wq_material'));
            $cmd->where(array(
                'AND',
                'merchant_id = :merchant_id',
                'flag = :flag',
//                  'from_platform = :from_platform',
            ));
            $cmd->params = array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
//                  ':from_platform' => FROM_PLATFORM_WECHAT,
            );
            $model = $cmd->queryAll();

            $list = array();
            $list[0] = "---请选择---";
            foreach ($model as $key => $value) {
                if (0 == $value['rate']) {
                    $list[$value['material_id']] = $value['title'];
                }
            }

            $result['data'] = $list;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取会员分组
     * @param $merchant_id
     */
    public function getGroupList($merchant_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('*');
            $cmd->from(array('wq_user_group'));
            $cmd->where(array(
                'AND',
                'merchant_id = :merchant_id',
                'flag = :flag',
            ));
            $cmd->params = array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
            );
            $model = $cmd->queryAll();

            $list = array();
            $list[0] = "所有用户";
            foreach ($model as $key => $value) {
                $list[$value['id']] = $value['name'];
            }

            $result['data'] = $list;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取群发广播历史
     * @param $merchant_id
     */
    public function getBroadcastRecord($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            //数据库查询
            $model = Reply::model()->findAll(array(
                'condition' => 'merchant_id = :merchant_id and type = :type and flag = :flag and from_platform = :from_platform',
                'order' => 'create_time desc',
                'params' => array(':merchant_id' => $merchant_id, ':type' => REPLY_TYPE_BROADCAST, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_WECHAT),
            ));
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    if (!empty($value['material_id'])) {
                        $material = Material::model()->find('material_id = :material_id and rate = :rate', array(':material_id' => $value['material_id'], ':rate' => 0));
                        $list[$value['id']]['title'] = '[图文消息]' . $material['title'];
                        $list[$value['id']]['content'] = $material['abstract'];
                        $list[$value['id']]['img'] = $material['cover_img'];
                    } else {
                        $list[$value['id']]['title'] = '[文字]' . $value['content'];
                    }

                    if (!$value['group_id']) {
                        $list[$value['id']]['group'] = '全部';
                    } else {
                        $group = UserGroup::model()->findByPk($value['group_id']);
                        $list[$value['id']]['group'] = $group['name'];
                    }
                    $list[$value['id']]['time'] = $value['create_time'];
                }
            }

            $result['data'] = $list;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取会员openid
     * @param $merchant_id
     * @param $group_id
     */
    public function getUserOpenId($merchant_id, $group_id, $repeat)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('*');

            if ($repeat == 2) {
                if (!$group_id) {
                    $from = array('wq_user user');
                    $where = array(
                        'AND',
                        'user.merchant_id = :merchant_id',
                        'user.flag = :flag',
                        'user.alipay_fuwu_id is null',
                        'user.wechat_id is not null',
                    );
                    $params = array(
                        ':merchant_id' => $merchant_id,
                        ':flag' => FLAG_NO,
                    );
                } else {
                    $from = array('wq_user user', 'wq_group group');
                    $where = array(
                        'AND',
                        'user.merchant_id = :merchant_id',
                        'group.group_id = :group_id',
                        'group.user_id = user.id',
                        'user.flag = :flag',
                        'user.alipay_fuwu_id is null',
                        'user.wechat_id is not null',
                    );
                    $params = array(
                        ':merchant_id' => $merchant_id,
                        ':group_id' => $group_id,
                        ':flag' => FLAG_NO,
                    );
                }
            } else {
                if (!$group_id) {
                    $from = array('wq_user user');
                    $where = array(
                        'AND',
                        'user.merchant_id = :merchant_id',
                        'user.flag = :flag',
                        'user.wechat_id is not null',
                    );
                    $params = array(
                        ':merchant_id' => $merchant_id,
                        ':flag' => FLAG_NO,
                    );
                } else {
                    $from = array('wq_user user', 'wq_group group');
                    $where = array(
                        'AND',
                        'user.merchant_id = :merchant_id',
                        'group.group_id = :group_id',
                        'group.user_id = user.id',
                        'user.flag = :flag',
                        'user.wechat_id is not null',
                    );
                    $params = array(
                        ':merchant_id' => $merchant_id,
                        ':group_id' => $group_id,
                        ':flag' => FLAG_NO,
                    );
                }

            }

            $cmd->from($from);
            $cmd->where($where);
            $cmd->params = $params;
            $model = $cmd->queryAll();

            $list = array();
            foreach ($model as $key => $value) {
                $list[$key] = $value['wechat_id'];
            }

            $result['data'] = $list;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取消息自动回复信息
     * @param $merchant_id
     */
    public function getMsgReply($merchant_id)
    {
        $result = array();
        try {
            $model = Reply::model()->find('merchant_id = :merchant_id and type = :type and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':type' => REPLY_TYPE_MSG, ':from_platform' => FROM_PLATFORM_WECHAT, ':flag' => FLAG_NO));
            if (empty($model)) {
                $model = new Reply();
            }

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
     * 获取关键词自动回复信息
     * @param $merchant_id
     */
    public function getReplyList($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            //数据库查询
            $model = Reply::model()->findAll('merchant_id = :merchant_id and flag = :flag and from_platform = :from_platform and type = :type order by create_time', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
                ':from_platform' => FROM_PLATFORM_WECHAT,
                ':type' => REPLY_TYPE_KEYWORD
            ));

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $list[$value['rule_id']]['id'] = $value['id'];
                    $list[$value['rule_id']]['rule_name'] = $value['rule_name'];
                    $list[$value['rule_id']]['key_word'][$value['id']] = $value['key_word'];
                    if (!empty($value['material_id'])) {
                        $material = Material::model()->find('material_id = :material_id and rate = :rate', array(':material_id' => $value['material_id'], 'rate' => 0));
                        $list[$value['rule_id']]['content'] = '【图文信息】' . $material['title'];
                    } else {
                        $list[$value['rule_id']]['content'] = $value['content'];
                    }
                }
            }

            $result['data'] = $list;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取回复信息
     * @param $id
     */
    public function getReply($id)
    {
        $result = array();
        try {
            if (!empty($id)) {
                //数据库查询
                $model = Reply::model()->findByPk($id);
            } else {
                $model = new Reply();
            }

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
     * 保存消息自动回复信息
     * @param $model
     * @param $content
     * @param $material_id
     */
    public function saveMsgReply($model, $merchant_id, $content, $material_id)
    {
        $result = array();
        try {
            $model['merchant_id'] = $merchant_id;
            $model['from_platform'] = FROM_PLATFORM_WECHAT;
            if (!empty($content)) {
                $model['content'] = $content;
                $model['material_id'] = 0;
            } elseif (!empty($material_id)) {
                $model['material_id'] = $material_id;
                $model['content'] = '';
            }
            $model['create_time'] = date('Y-m-d H:i:s');
            $model['type'] = REPLY_TYPE_MSG;

            if ($model->save()) {
                $result['data'] = $model;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }

        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 保存群发消息
     * @param $model
     * @param $content
     * @param $material_id
     * @param $group
     */
    public function saveBroadcastReply($model, $merchant_id, $content, $material_id, $group)
    {
        $result = array();
        try {
            $model['merchant_id'] = $merchant_id;
            $model['from_platform'] = FROM_PLATFORM_WECHAT;
            if (!empty($content)) {
                $model['content'] = $content;
                $model['material_id'] = 0;
            } elseif (!empty($material_id)) {
                $model['material_id'] = $material_id;
                $model['content'] = '';
            }
            $model['create_time'] = date('Y-m-d H:i:s');
            $model['type'] = REPLY_TYPE_BROADCAST;
            $model['group_id'] = $group;

            if ($model->save()) {
                $result['data'] = $model;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }

        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 保存关键词自动回复信息
     * @param $model
     * @param $merchant_id
     * @param $rule_name
     * @param $key_word
     * @param $content
     * @param $material_id
     */
    public function saveReply($model, $merchant_id, $rule_name, $key_word, $content, $material_id)
    {
        $result = array();
        try {
            $model['merchant_id'] = $merchant_id;
            $model['rule_id'] = $merchant_id . date('Ymdhis');
            $model['rule_name'] = $rule_name;
            $model['key_word'] = $key_word;
            $model['from_platform'] = FROM_PLATFORM_WECHAT;
            if (!empty($content)) {
                $model['content'] = $content;
                $model['material_id'] = 0;
            } elseif (!empty($material_id)) {
                $model['material_id'] = $material_id;
                $model['content'] = '';
            }
            $model['create_time'] = date('Y-m-d H:i:s');
            $model['type'] = REPLY_TYPE_KEYWORD;

            //参数验证
            if (empty($rule_name)) {
                throw new Exception('规则名为空');
            }
            if (empty($key_word)) {
                throw new Exception('关键词为空');
            }
            if (empty($content) && empty($material_id)) {
                throw new Exception('内容为空');
            }
            if (!$this->checkRuleName($merchant_id, $rule_name) && empty($material_id)) {
                throw new Exception('该规则名已存在');
            }

            if ($model->save()) {
                $result['data'] = $model;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {

            }

        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 坚持规则名是否重复
     * @param $merchant_id
     * @param $rule_name
     */
    public function checkRuleName($merchant_id, $rule_name)
    {
        $result = true;
        $model = Reply::model()->find('merchant_id = :merchant_id and rule_name = :rule_name', array(':merchant_id' => $merchant_id, 'rule_name' => $rule_name));
        if (empty($model)) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * 删除关键词自动回复
     */
    public function delAutoReply($reply_id)
    {
        $result = array();
        try {
            $model = Reply::model()->findByPk($reply_id);
            $model['flag'] = FLAG_YES;
            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 查询数据库关键词
     */
    public function getReplyKeyWord($merchant_id, $text)
    {
        //查询数据库寻找关键词
        $criteria = new CDbCriteria();
        $criteria->addCondition('merchant_id = :merchant_id');
//      $criteria->addSearchCondition('name', $text);
        $criteria->addCondition('key_word like :key_word');
        $criteria->addCondition('from_platform = :from_platform');
        $criteria->addCondition('flag = :flag');
        $criteria->params = array(
            ':merchant_id' => $merchant_id,
            ':key_word' => '%,' . $text . ',%',
            ':from_platform' => FROM_PLATFORM_WECHAT,
            ':flag' => FLAG_NO,
        );
        $word_obj = Reply::model()->find($criteria);

        return $word_obj;
    }

    /**
     * 查询消息 回复内容
     * @param $merchant_id
     */
    public function getFirstMsg($id)
    {
        $model = Reply::model()->find('merchant_id = :merchant_id and type = :type and from_platform = :from_platform and flag = :flag', array(
            ':merchant_id' => $id,
            ':type' => REPLY_TYPE_MSG,
            ':from_platform' => FROM_PLATFORM_WECHAT,
            ':flag' => FLAG_NO
        ));

        return $model;
    }

    /**
     * 获取菜单点击事件
     */
    public function getMenuClickMsg($merchant_id, $menu_name)
    {
        $menu = Menu::model()->find('merchant_id=:merchant_id and menu_name=:menu_name and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':menu_name' => $menu_name, ':from_platform' => FROM_PLATFORM_WECHAT, ':flag' => FLAG_NO));

        return $menu;
    }

    /**
     * 获取素材信息
     */
    public function getMaterial($reply_id)
    {
        $model = Material::model()->findAll('material_id = :material_id and flag = :flag', array(':material_id' => $reply_id, ':flag' => FLAG_NO));

        return $model;
    }

    /**
     * 获取图文素材
     * @param $merchant_id          商户id
     */
    public function getMaterialList($merchant_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('*');
            $cmd->from(array('wq_material'));
            $cmd->where(array(
                'AND',
                'merchant_id = :merchant_id',
                'flag = :flag',
                'from_platform = :from_platform',
            ));
            $cmd->params = array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
                ':from_platform' => FROM_PLATFORM_WECHAT,
            );
            $cmd->order = 'last_time desc';
            $model = $cmd->queryAll();

            $list = array();
            foreach ($model as $key => $value) {
                if (empty($list[$value['material_id']])) {
                    $list[$value['material_id']][$value['rate']]['id'] = $value['id'];
                    $list[$value['material_id']][$value['rate']]['title'] = $value['title'];
                    $list[$value['material_id']][$value['rate']]['cover_img'] = $value['cover_img'];
                    $list[$value['material_id']][$value['rate']]['abstract'] = $value['abstract'];
                } else {
                    $list[$value['material_id']][$value['rate']]['id'] = $value['id'];
                    $list[$value['material_id']][$value['rate']]['title'] = $value['title'];
                    $list[$value['material_id']][$value['rate']]['cover_img'] = $value['cover_img'];
                    $list[$value['material_id']][$value['rate']]['abstract'] = $value['abstract'];
                }
                asort($list[$value['material_id']]);
            }

            $result['data'] = $list;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取单图文信息
     * @param $material_id          素材id
     */
    public function getSingleMaterial($material_id)
    {
        $result = array();
        try {
            if (!empty($material_id)) {
                //数据库查询
                $model = Material::model()->find('material_id = :material_id', array(':material_id' => $material_id));
            } else {
                $model = new Material();
            }

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
     * 获取多图文信息
     * @param $material_id          素材id
     */
    public function getMoreMaterial($material_id)
    {
        $result = array();
        try {
            if (!empty($material_id)) {
                //数据库查询
                $model = Material::model()->findAll(array(
                    'condition' => 'material_id = :material_id and flag = :flag',
                    'order' => 'rate',
                    'params' => array(':material_id' => $material_id, ':flag' => FLAG_NO),
                ));
                $count = Material::model()->count('material_id = :material_id and flag = :flag', array(
                    ':material_id' => $material_id,
                    ':flag' => FLAG_NO
                ));
            }

            $result['data'] = $model;
            $result['num'] = $count;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 修改多图文
     * @param  $id
     * @param  $title
     * @param  $link_content
     * @param  $cover_img
     * @param  $img_path
     * @param  $jump_type
     * @param  $content
     * @param  $rate
     */
    public function editMaterial($id, $title, $link_content, $cover_img, $img_path, $jump_type, $content)
    {
        $result = array();
        try {
            $model = Material::model()->findByPk($id);

            $model['title'] = $title;
            $model['cover_img'] = $cover_img;
            $model['img_path'] = $img_path;
            $model['jump_type'] = $jump_type;
            if ($jump_type == MATERIAL_CONTENT_TEXT) {
                $model['content'] = $content;
            } else {
                $model['link_content'] = $link_content;
            }
            //参数验证

            if ($model->save()) {
                if ($model['jump_type'] == MATERIAL_CONTENT_TEXT) {
                    $model['link_content'] = USER_DOMAIN . '/material?material_id=' . $model['id'];
                }
                if ($model->save()) {
                    $result['data'] = $model;
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['data'] = $model;
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            } else {
                $result['data'] = $model;
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 删除单条图文
     * @param $id
     */
    public function delSingleMaterial($id)
    {
        $result = array();
        try {
            $model = Material::model()->findByPk($id);
            if (!empty($model)) {
                $model->flag = FLAG_YES;
                if ($model->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['data'] = $model;
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }


    /**
     * 保存单图文素材
     * @param  $model
     * @param  $merchant_id          商户id
     * @param  $title                标题
     * @param  $cover_img            图片
     * @param  $img_path             图片路径
     * @param  $abstract             摘要
     * @param  $jump_type            跳转类型
     * @param  $content              正文内容
     * @param  $link_content         跳转链接
     * @param  $material_id          素材id
     * @param  $rate                 排序值
     */
    public function saveSingleMaterial($model, $merchant_id, $title, $cover_img, $img_path, $abstract, $jump_type, $content, $link_content, $material_id, $rate)
    {
        $result = array();
        try {

            $model['merchant_id'] = $merchant_id;
            $model['title'] = $title;
            $model['cover_img'] = $cover_img;
            $model['img_path'] = $img_path;
            $model['abstract'] = $abstract;
            $model['jump_type'] = $jump_type;
            $model['rate'] = $rate;
            $model['create_time'] = date('Y-m-d h:i:s');
            $model['from_platform'] = FROM_PLATFORM_WECHAT;
            $model['material_id'] = $material_id;
            if ($jump_type == MATERIAL_CONTENT_TEXT) {
                $model['content'] = $content;
            } else {
                $model['link_content'] = $link_content;
            }
            //参数验证
            if (!isset($title)) {
                throw new Exception('标题为空');
            }
            if (empty($cover_img)) {
                throw new Exception('图片为空');
            }

            if ($model->save()) {
                if ($model['jump_type'] == MATERIAL_CONTENT_TEXT) {
                    $model['link_content'] = USER_DOMAIN . '/material?material_id=' . $model['id'];
                }
                if ($model->save()) {
                    $result['data'] = $model;
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['data'] = $model;
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            } else {
                $result['data'] = $model;
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 删除图文素材
     * @param $material_id          素材id
     * @author gu
     */
    public function delMaterial($material_id)
    {
        $result = array();
        $flag = 0;
        try {
            $transaction = Yii::app()->db->beginTransaction();
            $material = Material::model()->findAll('material_id =:material_id and flag = :flag', array(
                ':material_id' => $material_id,
                ':flag' => FLAG_NO
            ));
            if ($material) {
                foreach ($material as $k => $v) {
                    $v->flag = FLAG_YES;
                    if ($v->update()) {

                    } else {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 0) {
                    $transaction->commit();
                    $result['status'] = ERROR_NONE; //状态码
                } else {
                    $transaction->rollBack();
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '素材删除失败'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '该素材不存在'; //错误信息
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 群发广播
     */
    public function sendMessage()
    {

    }


    /**
     * 获取菜单信息
     * $merchant_id  商户id
     */
    public function getMenu($merchant_id)
    {
        $data = array();
        $list_parent = Menu::model()->findAll(array(
            'condition' => 'merchant_id=:merchant_id and flag=:flag and from_platform = :from_platform and parent_id = :parent_id',
            'order' => 'sort',
            'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_WECHAT, ':parent_id' => '0')));
        $list_son = Menu::model()->findAll(array(
            'condition' => 'merchant_id=:merchant_id and flag=:flag and from_platform = :from_platform and parent_id != :parent_id',
            'order' => 'sort',
            'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_WECHAT, ':parent_id' => '0')));
        $arr_node = array(); //菜单节点
        $arr = array(); //菜单列表
        $arr_son = array();
        //初始化菜单节点
        $arr_node[0] = "一级菜单";

        //自定义菜单数组
        if (!empty($list_son)) {
            foreach ($list_son as $k => $v) {
                $id = $v->parent_id;
                $son_id = $v->id;
                $arr_son[$id][$son_id]['menu_name'] = $v->menu_name;
            }
        }

        //自定义菜单数组
        if (!empty($list_parent)) {
            foreach ($list_parent as $k => $v) {
                $id = $v->id;
                $arr[$id]['menu_name'] = $v->menu_name;
                if (!empty($arr_son[$id])) {
                    $arr[$id]['son'] = $arr_son[$id];
                }
                $arr_node[$id] = $v->menu_name;
            }
        }
        $data['arr'] = $arr;
        $data['arr_node'] = $arr_node;
        return $data;
    }

    /**
     * 添加自定义菜单
     * $merchant_id  商户id
     * $post  传过来的属性数组
     */
    public function addMenu($merchant_id, $post, $model, $create_time, $last_time)
    {
        $result = array();
        try {
            $model->merchant_id = $merchant_id;

            if ($post['parent_id']) {
                $model->parent_id = $post['parent_id'];
                //统计菜单个数
                $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => $post['parent_id'], ':from_platform' => FROM_PLATFORM_WECHAT, ':flag' => FLAG_NO));

            } else {
                $model->parent_id = 0;
                //统计菜单个数
                $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(
                    ':merchant_id' => $merchant_id,
                    ':parent_id' => 0,
                    ':from_platform' => FROM_PLATFORM_WECHAT,
                    ':flag' => FLAG_NO
                ));
            }

            $model->attributes = $post;
            if (!empty($create_time)) {
                $model->create_time = $create_time;
            }
            if (!empty($last_time)) {
                $model->last_time = $last_time;
            }
            if ($model->type == WQ_MENU_TYPE_WORD) {
                if (empty($post['content'])) {
                    throw new Exception('文字信息为空');
                }
            } elseif ($model->type == WQ_MENU_TYPE_PHOTO) {
                if (empty($post['content_photo'])) {
                    throw new Exception('图文信息为空');
                }
                $model->content = $post['content_photo'];
            } elseif ($model->type == WQ_MENU_TYPE_WWW) {
                if (empty($post['content_www'])) {
                    throw new Exception('链接网址为空');
                }
                if (strstr($post['content_www'], "http")) {
                    $model->content = $post['content_www'];
                } else {
                    $model->content = "http://" . $post['content_www'];
                }
            } elseif ($model->type == WQ_MENU_TYPE_SYSTEM) {
                if (empty($post['content_url'])) {
                    throw new Exception('系统网址为空');
                }
                $model->content = $post['content_url'];
            }

            $model->from_platform = FROM_PLATFORM_WECHAT;

            //参数验证
            if (empty($post['menu_name'])) {
                throw new Exception('菜单名为空');
            }

            $regx = '/[^0-9a-zA-Z一-龥]/u';
            $str_res = preg_match($regx, $post['menu_name']);
            if ($str_res) {
                throw new Exception('有不合法字符');
            }

            $str_long = $this->utf8_strlen($post['menu_name']);
            if (!$post['parent_id'] && $str_long > 8) {
                throw new Exception('超出最大字数限制，一级菜单最多4个汉字或8个字母');
            }
            if ($post['parent_id'] && $str_long > 14) {
                throw new Exception('超出最大字数限制，二级菜单最多7个汉字或14个字母');
            }

            if (!$post['parent_id']) {
                if ($count >= 3) {
                    throw new Exception('当前一级菜单个数已满，不能继续添加');
                }
            } else {
                if ($count >= 5) {
                    throw new Exception('当前二级菜单个数已满，不能继续添加');
                }
            }

            $sort = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => $post['parent_id'], ':from_platform' => FROM_PLATFORM_ALI, ':flag' => FLAG_NO));
            $model->sort = $sort;

            if ($model->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = ''; // 错误信息
                $result ['data'] = array(
                    'id' => $model->id
                );
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                $result ['errMsg'] = '数据保存失败'; // 错误信息
                $result ['data'] = $model;
            }
        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }


    // 计算中文字符串长度
    private function utf8_strlen($string = null)
    {
        // 将字符串分解为单元
        preg_match_all("/./us", $string, $match);
        // 返回单元个数
        $count = 0;
        foreach ($match[0] as $k => $v) {
            if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $v)) {
                $count = $count + 2;
            } else {
                $count++;
            }
        }
        return $count;
    }


    /**
     * 修改自定义菜单
     * $merchant_id  商户id
     * $post  传过来的属性数组
     */
    public function editMenu($merchant_id, $post, $model, $create_time, $last_time, $menu_id)
    {
        $result = array();
        try {
            $model = Menu::model()->find('id=:id and flag=:flag', array(
                ':id' => $menu_id,
                ':flag' => FLAG_NO
            ));
            $before_parent_id = $model->parent_id;

            if ($before_parent_id != $post['parent_id']) {
                if ($post['parent_id']) {
                    //统计菜单个数
                    $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => $post['parent_id'], ':from_platform' => FROM_PLATFORM_WECHAT, ':flag' => FLAG_NO));
                    if ($count >= 5) {
                        throw new Exception('当前二级菜单个数已满，不能继续添加');
                    }
                } else {
                    //统计菜单个数
                    $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => 0, ':from_platform' => FROM_PLATFORM_WECHAT, ':flag' => FLAG_NO));
                    if ($count >= 3) {
                        throw new Exception('当前一级菜单个数已满，不能继续添加');
                    }
                }
            }
            $model->merchant_id = $merchant_id;

            $model->attributes = $post;
            if (!empty($create_time)) {
                $model->create_time = $create_time;
            }
            if (!empty($last_time)) {
                $model->last_time = $last_time;
            }
            if ($model->type == WQ_MENU_TYPE_WORD) {
                if (empty($post['content'])) {
                    throw new Exception('文字信息为空');
                }
            } elseif ($model->type == WQ_MENU_TYPE_PHOTO) {
                if (empty($post['content_photo'])) {
                    throw new Exception('图文信息为空');
                }
                $model->content = $post['content_photo'];
            } elseif ($model->type == WQ_MENU_TYPE_WWW) {
                if (empty($post['content_www'])) {
                    throw new Exception('链接网址为空');
                }
                if (strstr($post['content_www'], "http")) {
                    $model->content = $post['content_www'];
                } else {
                    $model->content = "http://" . $post['content_www'];
                }
            } elseif ($model->type == WQ_MENU_TYPE_SYSTEM) {
                if (empty($post['content_url'])) {
                    throw new Exception('系统网址为空');
                }
                $model->content = $post['content_url'];
            }

            //参数验证
            if (empty($post['menu_name'])) {
                throw new Exception('菜单名为空');
            }

            $regx = '/[^0-9a-zA-Z一-龥]/u';
            $str_res = preg_match($regx, $post['menu_name']);
            if ($str_res) {
                throw new Exception('有不合法字符');
            }

            $str_long = $this->utf8_strlen($post['menu_name']);
            if (!$post['parent_id'] && $str_long > 8) {
                throw new Exception('超出最大字数限制，一级菜单最多4个汉字或8个字母');
            }
            if ($post['parent_id'] && $str_long > 14) {
                throw new Exception('超出最大字数限制，二级菜单最多7个汉字或14个字母');
            }

            if ($model->update()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = ''; // 错误信息
                $result ['data'] = array(
                    'id' => $model->id
                );
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                $result ['errMsg'] = '数据保存失败'; // 错误信息
                $result ['data'] = $model;
            }
        } catch (Exception $e) {
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }


    /**
     * 菜单排序
     * @param $sort_str
     */
    public function sortMenu($sort_str)
    {
        $result = array();
        try {
            $transaction = Yii::app()->db->beginTransaction();
            $root_sort = 0;
            $son_sort = 0;
            $flag = 1;

            $sort_family = explode(";", $sort_str);
            //检查二级菜单个数是否符合要求
            if (!empty($sort_family)) {
                foreach ($sort_family as $value_check_family) {
                    $sort_check_parents = explode(":", $value_check_family);
                    if (!empty($value_check_family)) {
                        $sort_check_son = explode(",", $sort_check_parents[1]);
                        $count_two = sizeof($sort_check_son);
                        if ($count_two > 6) {
                            throw new Exception('二级菜单个数超出限制');
                        }
                    }
                }
            }

            if (!empty($sort_family)) {
                foreach ($sort_family as $value_family) {
                    if (!empty($value_family)) {
                        $sort_parents = explode(":", $value_family);
                        if (!empty($sort_parents)) {
                            $pid = $sort_parents[0];
                            $sort_son = explode(",", $sort_parents[1]);

                            $model = Menu::model()->findByPk($pid);
                            $model->sort = $root_sort;
                            $root_sort++;
                            if ($model->save()) {
                            } else {
                                $flag = 0;
                                break;
                            }

                            $son_sort = 0;
                            if (!empty($sort_son)) {
                                foreach ($sort_son as $value_son) {
                                    if (!empty($value_son)) {
                                        $model = Menu::model()->findByPk($value_son);
                                        $model->sort = $son_sort;
                                        $model->parent_id = $pid;
                                        $son_sort++;
                                        if ($model->save()) {

                                        } else {
                                            $flag = 0;
                                            break;
                                        }
                                    }
                                }
                            }
                            if (!$flag) {
                                break;
                            }
                        }
                    }
                }
                if ($flag) {
                    $transaction->commit();
                    $result['status'] = ERROR_NONE; //状态码
                } else {
                    $transaction->rollBack();
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '素材排序失败'; //错误信息
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 删除菜单
     * $merchant_id  商户id
     * $menu_id  菜单id
     */
    public function delMenu($merchant_id, $menu_id)
    {
        $result = array();
        $menu = Menu::model()->findByPk($menu_id);

        //如果是二级菜单   直接删除
        if (!empty($menu->parent_id)) {
            $menu->flag = FLAG_YES;
            if ($menu->save()) {
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            } else {
                $result ['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据删除失败'; //错误信息
            }
        } else { //如果菜单是一级菜单  则其所属的二级菜单一起删除
            $model = Menu::model()->findAll('parent_id=:parent_id and flag=:flag',
                array(':parent_id' => $menu_id, ':flag' => FLAG_NO));
            $menu->flag = FLAG_YES;
            $menu->save();
            $i = 0;
            $count = count($model);
            foreach ($model as $k => $v) {
                $v->flag = FLAG_YES;
                if ($v->save()) {
                    $i++;
                    if ($i == $count) {
                        $result ['status'] = ERROR_NONE;
                        $result['errMsg'] = ''; //错误信息
                    }
                }
            }
            $result ['status'] = ERROR_NONE;
            $result['errMsg'] = ''; //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取商户信息
     * @param $merchant_id
     */
    public function getMerchant($merchant_id)
    {
        $result = array();
        try {
            //数据库查询
            $cmd = Yii::app()->db->createCommand();
            $cmd->select('*');
            $cmd->from(array('wq_merchant'));
            $cmd->where(array(
                'AND',
                'id = :id',
                'flag = :flag',
            ));
            $cmd->params = array(
                ':id' => $merchant_id,
                ':flag' => FLAG_NO,
            );
            $model = $cmd->queryRow();

            $result['data'] = $model;
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /*
     * action: 获取access_token
    * post: appid,appsecret
    * author: li_junyu
    * date: 2014-8-7
    */
    public function getAccesstoken($appid, $appsecret, $merchant_id = 0)
    {
        Yii::log('<<<start>>>', 'info', 'wechat');
        $access_token = array();
//      $merchant_id = Yii::app()->session['merchant_id'];

        $merchant = Merchant::model()->findByPk($merchant_id);
        //判断是否在第三方授权中，如果在第三方授权,则采用第三方授权的accesstoken
//      if(isset($merchant['wechat_thirdparty_authorizer_if_auth']) && $merchant['wechat_thirdparty_authorizer_if_auth'] == 2 ){
//          Yii::log('<<<third>>>', 'info', 'wechat');
//          $access_token = array();
//          $authorizer_appid = $merchant['wechat_thirdparty_authorizer_appid'];
//          $refresh_token_value = $merchant['wechat_thirdparty_authorizer_refresh_token'];
//          $result = Component::getAuthAccessToken($authorizer_appid,$refresh_token_value);
//          Yii::log('####auth_appid:'.$merchant['wechat_thirdparty_authorizer_appid'].', auth_stoken:'.$result, 'info', 'wechat.getAuthToken');

//          if (isset($result)){
//              $access_token['error'] = true;
//              $access_token['access_token'] = $result;
//          }else{
//              $access_token['error'] = false;
//          }
//          return $access_token;
//      }
        if (isset($appid) && isset($appsecret)) {
            Yii::log('<<<reget>>>', 'info', 'wechat');
            $access_token_url = WECHAT_API_URL . "token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;//获取ACCESS_TOKEN请求的URL地址；
            $res = file_get_contents($access_token_url);
            $result = json_decode($res, true);
            if (isset($result['access_token'])) {
                $access_token['error'] = true;
                $access_token['access_token'] = $result['access_token'];
            } else {
                $access_token['error'] = false;
            }
            return $access_token;
        }
    }

    /**
     * 获取用户openid--使用code获取access_code
     */
    public function getUserAccessCode($code, $wechat_appid, $wechat_appsecret, $merchant_id = 0)
    {
        $access_token = array();
        $merchant = Merchant::model()->findByPk($merchant_id);

        //授权 模式
        if ($merchant->wechat_thirdparty_authorizer_if_auth == 2) {
            $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=' . $wechat_appid . '&code=' . $code . '&grant_type=authorization_code&component_appid=' . Component::getAppId() . '&component_access_token=' . Component::getComponentAccessToken();//获取ACCESS_TOKEN请求的URL地址；
            $res = file_get_contents($access_token_url);
            $result = json_decode($res, true);
            return $result;
        } else {
            $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $wechat_appid . '&secret=' . $wechat_appsecret . '&code=' . $code . '&grant_type=authorization_code';//获取ACCESS_TOKEN请求的URL地址；
            $res = file_get_contents($access_token_url);
            $result = json_decode($res, true);
            return $result;
        }
    }

    public function getAccessCode($wechat_appid, $wechat_appsecret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $wechat_appid . '&secret=' . $wechat_appsecret;
        $ch = curl_init();
        //设置超时
        //curl_setopt($ch, CURLOP_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res, true);

        $re = $data['access_token'];
        return $re;
    }

    /**
     * 获取用户openid--使用access_token 用户openid获取userinfo
     */
    public function getUserInfo($open_id, $access_token)
    {
        $user_info = array();
        //$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$open_id;//获取ACCESS_TOKEN请求的URL地址；
        $user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $open_id;
        //初始化curl
        $ch = curl_init();
        //设置超时
        //curl_setopt($ch, CURLOP_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $user_info_url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res, true);

        return $data;
    }

    /**
     * 将菜单组装成所需的数组
     * @param $merchant_id
     */
    public function installMenu($merchant_id)
    {
        $result = array();
        try {
            $arr_menu = array();
//          $obj_menu = Menu::model()->findAll('merchant_id=:merchant_id and flag=:flag and from_platform=:from_platform', array(':merchant_id' => $merchant_id, ':flag'=>FLAG_NO,':from_platform'=>FROM_PLATFORM_WECHAT));
            $obj_menu = Menu::model()->findAll(array(
                'condition' => 'merchant_id=:merchant_id and flag=:flag and from_platform = :from_platform',
                'order' => 'sort',
                'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_WECHAT)));

            if (!empty($obj_menu)) {
                $i = 0;
                foreach ($obj_menu as $k => $v) {
                    if (!$v['parent_id']) { //是否为一级菜单
                        $arr_menu["button"][$i]["name"] = urlencode($v['menu_name']);
                        $j = 0;
                        foreach ($obj_menu as $key => $value) { //循环找出二级菜单
                            if ($value['parent_id'] == $v->id) {
                                $arr_menu["button"][$i]["sub_button"][$j]["name"] = urlencode($value['menu_name']);
                                if ($value['type'] == WQ_MENU_TYPE_WWW || $value['type'] == WQ_MENU_TYPE_SYSTEM) { //链接网址
                                    $arr_menu["button"][$i]["sub_button"][$j]["type"] = "view";
                                    $arr_menu["button"][$i]["sub_button"][$j]["url"] = urlencode($value['content']);
                                } else { // 图文信息
                                    $arr_menu["button"][$i]["sub_button"][$j]["type"] = "click";
                                    $arr_menu["button"][$i]["sub_button"][$j]["key"] = urlencode($value->menu_name);
                                }
                                $j++;
                            }
                        }
                        if (!isset($arr_menu["button"][$i]["sub_button"])) { //若无二级菜单 则执行以下操作
                            if ($v['type'] == WQ_MENU_TYPE_WWW || $v['type'] == WQ_MENU_TYPE_SYSTEM) { //链接网址
                                $arr_menu["button"][$i]["type"] = "view";
                                $arr_menu["button"][$i]["url"] = urlencode($v['content']);
                            } else { // 图文消息, 文字消息
                                $arr_menu["button"][$i]["type"] = "click";
                                $arr_menu["button"][$i]["key"] = urlencode($v['menu_name']);
                            }
                        }
                        $i++;
                    }
                }
            }
            $result['data'] = $arr_menu;
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /*
     * action: 发布自定义菜单
    * post: assess_token,json_menu
    * author: li_junyu
    * date: 2014-8-27
    */
    public function publishMenu($access_token, $data)
    {
        $url = WECHAT_API_URL . "menu/create?access_token=" . $access_token;
        $result = $this->https_request($url, $data);

        return $result;
    }

    /*
     * action: 群发广播
    * post: assess_token,json_menu
    * author: li_junyu
    * date: 2014-8-27
    */
    public function massSendGroud($access_token, $msg)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=" . $access_token;
        $result = $this->https_request($url, $msg);

        return $result;
    }

    /**
     * 上传图片
     * @param $img     图片地址
     * @param $access_token
     */
    public function uploadImg($img, $access_token)
    {
        $type = "image";
        $filepath = $img;
//      $filepath = 'D:/www/workplace/kuaiguanjia/upload/images/gj/source/'.$img;
        $filedata = array("media" => "@" . $filepath);
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $access_token . "&type=" . $type;
        $result = $this->https_request($url, $filedata);

        return $result;
    }

    /**
     * 上传图文信息
     * @param
     */
    public function uploadNews($material, $img_list, $access_token)
    {
        $news = array();
        foreach ($material as $key => $value) {
            $news[] = array(
                "thumb_media_id" => $img_list[$value['id']],
                "author" => "nnkou",
                "title" => $value['title'],
                "content_source_url" => $value['link_content'],
                "content" => $value['content'],
                "digest" => $value['abstract'],
            );
        }
        //处理乱码
        foreach ($news as &$item) {
            foreach ($item as $k => $v) {
                if ($k == 'content') {
                    $item[$k] = urlencode(htmlspecialchars(str_replace("\"", "'", $v)));
                } else {
                    $item[$k] = urlencode($v);
                }
            }
        }
        $news_data = htmlspecialchars_decode(urldecode(json_encode(array("articles" => $news))));
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=" . $access_token;
        $result = $this->https_request($url, $news_data);

        return $result;

    }

    /**
     * 请求
     * @param $url
     * @param $data
     */
    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * 保存微信用户个人信息
     */
    public function saveUserinfo($id, $userinfo_re, $data)
    {
        $user = new UserUC();

        //修改头像
        if (!isset($data['avatar']) || empty($data['avatar'])) {
            if ($userinfo_re['headimgurl'] == '/132') {
                $user->editPersonalInformation($id, 'avatar', '');
            } else {
                $user->editPersonalInformation($id, 'avatar', $userinfo_re['headimgurl']);
            }
        }

        //修改昵称
        if (!isset($data['nickname']) || empty($data['nickname'])) {
            $user->editPersonalInformation($id, 'nickname', $userinfo_re['nickname']);
        }

        //修改性别
        if (!isset($data['sex']) || empty($data['sex'])) {
            $user->editPersonalInformation($id, 'sex', $userinfo_re['sex']);
        }

        $user->editPersonalInformation($id, 'wechat_nickname', $userinfo_re['nickname']);
        $user->editPersonalInformation($id, 'wechat_sex', $userinfo_re['wechat_sex']);
        $user->editPersonalInformation($id, 'wechat_province', $userinfo_re['wechat_province']);
        $user->editPersonalInformation($id, 'wechat_city', $userinfo_re['wechat_city']);
        $user->editPersonalInformation($id, 'wechat_country', $userinfo_re['wechat_country']);
        //$user->editPersonalInformation($id, 'wechat_privilege', $userinfo_re['wechat_privilege']);//数据库中字段没有
        $user->editPersonalInformation($id, 'wechat_unionid', $userinfo_re['wechat_unionid']);
        $user->editPersonalInformation($id, 'wechat_headimgurl', $userinfo_re['wechat_headimgurl']);

        //授权缓存30天
        Yii::app()->memcache->set($userinfo_re['openid'], 1, 2592000);
        //修改最后更新时间
        //$user->editPersonalInformation($id, 'last_time', date('Y-m-d H:i:s'));
    }

    /**
     * 获取关注者列表
     * @param unknown $access_token
     * @return mixed
     */
    public function getUserList($access_token, $next_openid = '')
    {
        if (empty($next_openid)) {
            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $access_token;
        } else {
            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $access_token . "&next_openid=" . $next_openid;
        }
        $result = $this->https_request($url);
        return $result;
    }

    /**
     * 获取用户基本信息
     */
    public function getUserInfos($access_token, $openid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
        $result = $this->https_request($url);

        return $result;
    }

    /**
     * 生成带参数的二维码
     */
    public function createQrcode($qrcode, $access_token)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;
        $result = $this->https_request($url, $qrcode);

        return $result;
    }

    /**
     * 通过ticket换取二维码
     */
    public function showQrcode($ticket)
    {
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket;
    }

    /**
     * 统计会员数量
     */
    public function getUserNum($merchant_id)
    {
        $count_arr = array();
        $wechat_count = User::model()->count('merchant_id = :merchant_id and wechat_id is not null', array(':merchant_id' => $merchant_id));
        $ali_count = User::model()->count('merchant_id = :merchant_id and alipay_fuwu_id is not null', array(':merchant_id' => $merchant_id));
        $togther_count = User::model()->count('merchant_id = :merchant_id and wechat_id is not null and alipay_fuwu_id is not null', array(':merchant_id' => $merchant_id));
        $count_arr['wechat'] = $wechat_count;
        $count_arr['ali'] = $ali_count;
        $count_arr['togher'] = $togther_count;

        return $count_arr;
    }

    /**
     *
     */
    public function countGroupUserNum($group_id)
    {
        $count_arr = array();
        $wechat_count = Group::model()->count('group_id = :group_id and wechat_id is not null', array(':group_id' => $group_id));
        $ali_count = Group::model()->count('group_id = :group_id and alipay_fuwu_id is not null', array(':group_id' => $group_id));
        $togther_count = Group::model()->count('group_id = :group_id and wechat_id is not null and alipay_fuwu_id is not null', array(':group_id' => $group_id));
        $count_arr['wechat'] = $wechat_count;
        $count_arr['ali'] = $ali_count;
        $count_arr['togher'] = $togther_count;

        return $count_arr;
    }

}