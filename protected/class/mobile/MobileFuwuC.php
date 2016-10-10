<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * 服务窗管理
 */
class MobileFuwuC extends mainClass
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
            $coupons = Coupons::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if (!empty($model)) {
                $list[0] = "--请选择--";
                $member_center = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_userinfo&redirect_uri=' . urlencode(USER_DOMAIN . '/memberCenter?encrypt_id=' . $model['encrypt_id']);
                $shop = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(USER_DOMAIN . '/shop?encrypt_id=' . $model['encrypt_id']);
                $book_operate = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(USER_DOMAIN . '/bookOperate?encrypt_id=' . $model['encrypt_id']);
                $order = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(USER_DOMAIN . '/orderList?encrypt_id=' . $model['encrypt_id'] . '&stored_confirm_status=' . ORDER_PAY_WAITFORCONFIRM);
                if (Yii::app()->session['merchant_id'] == TIANSHI_SHOP_API) {
                    $online_shop = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(COMMODITY_DOMAIN_DQH . '/index?encrypt_id=' . $model['encrypt_id']);
                } else {
                    $online_shop = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(COMMODITY_DOMAIN . '/index?encrypt_id=' . $model['encrypt_id']);
                }
                $list[$online_shop] = '在线商城';
                $list[$member_center] = '会员中心';
                $list[$shop] = '在线商铺';
                $list[$book_operate] = '预订';
                $list[$order] = '我的订单';
                if (!empty($coupons)) {
                    foreach ($coupons as $key => $value) {
                        $url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(USER_DOMAIN_COUPONS . '/newGetCouponOne?encrypt_id=' . $model['encrypt_id'] . '&coupon_id=' . $value['id']);
                        $list[$url] = $value['title'];
                    }
                }
                //应用活动
                $now = date("Y-m-d h:i:s");
                $promotions = Activity::model()->findAll('merchant_id = :merchant_id and flag = :flag and end_time > :now', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':now' => $now));
                if (!empty($promotions)) {
                    foreach ($promotions as $k => $v) {
                        $activity_url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=' . $model['appid'] . '&auth_skip=false&scope=auth_base&redirect_uri=' . urlencode(PROMOTIONS_DOMAIN . '/promotionsActivity?encrypt_id=' . $model['encrypt_id'] . '&promotions_id=' . $v['id']);
                        if ($v['type'] == PROMOTIONS_TYPE_TURNTABLE) {  //大转盘活动
                            $list[$activity_url] = '大转盘——' . $v['name'];
                        } elseif ($v['type'] == PROMOTIONS_TYPE_SCRATCH) {  //刮刮卡
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
     * 获取素材设置中的url
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
                $list[USER_DOMAIN . '/memberCenter?encrypt_id=' . $model['encrypt_id']] = '会员中心';
                $list[USER_DOMAIN . '/shop?encrypt_id=' . $model['encrypt_id']] = '在线商铺';
                $list[USER_DOMAIN . '/bookOperate?encrypt_id=' . $model['encrypt_id']] = '预定';
                $list[USER_DOMAIN . '/orderList?encrypt_id=' . $model['encrypt_id'] . '&stored_confirm_status=' . ORDER_PAY_WAITFORCONFIRM] = '我的订单';
                if (Yii::app()->session['merchant_id'] == TIANSHI_SHOP_API) {
                    $list[COMMODITY_DOMAIN_DQH_MARKETING . '/WelfareDetail?encrypt_id=' . $model['encrypt_id']] = '首单立减+粉丝周福利';
                }
                if (!empty($coupons)) {
                    foreach ($coupons as $key => $value) {
                        $url = USER_DOMAIN . '/lookCoupons?encrypt_id=' . $model['encrypt_id'] . '&coupons_id=' . $value['id'] . '&coupons_type=' . $value['type'];
                        $list[$url] = $value['title'];
                    }
                }

                $now = date("Y-m-d h:i:s");
                $promotions = Activity::model()->findAll('merchant_id = :merchant_id and flag = :flag and end_time > :now', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':now' => $now));
                if (!empty($promotions)) {
                    foreach ($promotions as $k => $v) {
                        $activity_url = PROMOTIONS_DOMAIN . '/promotionsActivity?encrypt_id=' . $model['encrypt_id'] . '&promotions_id=' . $v['id'];
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
// 					'from_platform = :from_platform',
            ));
            $cmd->params = array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
// 					':from_platform' => FROM_PLATFORM_ALI,
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
     * 获取消息自动回复信息
     * @param $merchant_id
     */
    public function getMsgReply($merchant_id)
    {
        $result = array();
        try {
            $model = Reply::model()->find('merchant_id = :merchant_id and type = :type and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':type' => REPLY_TYPE_MSG, ':from_platform' => FROM_PLATFORM_ALI, ':flag' => FLAG_NO));
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
            $model = Reply::model()->findAll('merchant_id = :merchant_id and flag = :flag and from_platform = :from_platform and type = :type', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_ALI, ':type' => REPLY_TYPE_KEYWORD));

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
            $model['from_platform'] = FROM_PLATFORM_ALI;
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
            $model['from_platform'] = FROM_PLATFORM_ALI;
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
            if (!$this->checkRuleName($merchant_id, $rule_name) && empty($model->id)) {
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
     * 获取商户信息
     * @param $encrypt_id
     */
    public function getMerchant($encrypt_id)
    {
        $model = Merchant::model()->find('encrypt_id = :encrypt_id', array(':encrypt_id' => $encrypt_id));

        return $model;
    }

    /**
     * 查询数据库关键词
     */
    public function getReplyKeyWord($merchant_id, $text)
    {
        //查询数据库寻找关键词
        $criteria = new CDbCriteria();
        $criteria->addCondition('merchant_id = :merchant_id');
        $criteria->addCondition('instr(:key_word, key_word) > 0');
        $criteria->addCondition('from_platform = :from_platform');
        $criteria->addCondition('flag = :flag');
        $criteria->params = array(
            ':merchant_id' => $merchant_id,
            ':key_word' => $text,
            ':from_platform' => FROM_PLATFORM_ALI,
            ':flag' => FLAG_NO,
        );
        $word_obj = Reply::model()->find($criteria);

        return $word_obj;
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
     * 获取带有关键词的素材
     */
    public function getSearchMaterial($merchant_id, $title)
    {
        $result = array();
        $list = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);
            $criteria->compare('title', $title, true);
            $search_model = Material::model()->findAll($criteria);

            if (!empty($search_model)) {
                $material_id_arr = array();
                foreach ($search_model as $k => $v) {
                    if (!in_array($v['material_id'], $material_id_arr)) {
                        $material_id_arr[] = $v['material_id'];
                    }
                }

                //数据库查询
                $criteria = new CDbCriteria();
                $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);
                $criteria->addInCondition('material_id', $material_id_arr);
                $criteria->order = 'create_time desc';
                $model = Material::model()->findAll($criteria);

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
// 					'from_platform = :from_platform',
            ));
            $cmd->params = array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO,
// 					':from_platform' =>FROM_PLATFORM_ALI,
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
                if (empty($model)) {
                    $model = array();
                }
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
                $count = Material::model()->count('material_id = :material_id and flag = :flag', array(':material_id' => $material_id, ':flag' => FLAG_NO));
            } else {
                $model = new Material();
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
     * @param  $jump_type
     * @param  $content
     * @param  $rate
     */
    public function editMaterial($id, $title, $link_content, $cover_img, $jump_type, $content)
    {
        $result = array();
        try {
            $model = Material::model()->findByPk($id);

            $model['title'] = $title;
            $model['cover_img'] = $cover_img;
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
     * @param  $abstract             摘要
     * @param  $jump_type            跳转类型
     * @param  $content              正文内容
     * @param  $link_content         跳转链接
     * @param  $material_id          素材id
     * @param  $rate                 排序值
     */
    public function saveSingleMaterial($model, $merchant_id, $title, $cover_img, $abstract, $jump_type, $content, $link_content, $material_id, $rate)
    {
        $result = array();
        try {

            $model['merchant_id'] = $merchant_id;
            $model['title'] = $title;
            $model['cover_img'] = $cover_img;
            $model['abstract'] = $abstract;
            $model['jump_type'] = $jump_type;
            $model['rate'] = $rate;
            $model['create_time'] = date('Y-m-d h:i:s');
// 			$model['from_platform'] = FROM_PLATFORM_ALI;
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
            if (mb_strlen($title, 'UTF8') > 32) {
                throw new Exception('标题长度超出限制');
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
                    $result['errMsg'] = '数据保存失败2'; //错误信息
                }
            } else {
                $result['data'] = $model;
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败1'; //错误信息
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
     * 获取菜单信息
     * $merchant_id  商户id
     */
    public function getMenu($merchant_id)
    {
        $data = array();
        $list_parent = Menu::model()->findAll(array(
            'condition' => 'merchant_id=:merchant_id and flag=:flag and from_platform = :from_platform and parent_id = :parent_id',
            'order' => 'sort',
            'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_ALI, ':parent_id' => '0')));
        $list_son = Menu::model()->findAll(array(
            'condition' => 'merchant_id=:merchant_id and flag=:flag and from_platform = :from_platform and parent_id != :parent_id',
            'order' => 'sort',
            'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_ALI, ':parent_id' => '0')));
        $arr_node = array(); //菜单节点
        $arr = array(); //菜单列表
        $arr_son = array();
        //初始化菜单节点
        $arr_node[0] = "一级菜单";

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
                $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => $post['parent_id'], ':from_platform' => FROM_PLATFORM_ALI, ':flag' => FLAG_NO));

            } else {
                $model->parent_id = 0;
                //统计菜单个数
                $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => 0, ':from_platform' => FROM_PLATFORM_ALI, ':flag' => FLAG_NO));
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

            $model->from_platform = FROM_PLATFORM_ALI;

            //参数验证
            if (empty($post['menu_name'])) {
                throw new Exception('菜单名为空');
            }

            $regx = '/[^0-9a-zA-Z一-龥]/u';
            $str_res = preg_match($regx, $post['menu_name']);
            if ($str_res) {
                throw new Exception('有不合法字符');
            }

            $str_long = mb_strwidth($post['menu_name'], 'utf-8');

            if (!$post['parent_id'] && $str_long > 8) {    //一级
                throw new Exception('超出最大字数限制，一级菜单最多4个汉字或8个字母');
            }
            if ($post['parent_id'] && $str_long > 24) {     //二级
                throw new Exception('超出最大字数限制,二级菜单最多12个汉字或24个字母');
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
                    $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => $post['parent_id'], ':from_platform' => FROM_PLATFORM_ALI, ':flag' => FLAG_NO));
                    if ($count >= 5) {
                        throw new Exception('当前二级菜单个数已满，不能继续添加');
                    }
                } else {
                    //统计菜单个数
                    $count = Menu::model()->count('merchant_id = :merchant_id and parent_id = :parent_id and from_platform = :from_platform and flag = :flag', array(':merchant_id' => $merchant_id, ':parent_id' => 0, ':from_platform' => FROM_PLATFORM_ALI, ':flag' => FLAG_NO));
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

            $str_long = mb_strwidth($post['menu_name'], 'utf-8');
            if (!$post['parent_id'] && $str_long > 8) {    //一级
                throw new Exception('超出最大字数限制，一级菜单最多4个汉字或8个字母');
            }
            if ($post['parent_id'] && $str_long > 24) {     //二级
                throw new Exception('超出最大字数限制,二级菜单最多12个汉字或24个字母');
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
     * 发布菜单
     * $merchant_id  商户id
     */
    public function publishMenu($merchant_id)
    {
        //将菜单及内容包装成调用所需的数组
        $arr_menu = array();
// 		$obj_menu = Menu::model()->findAll('merchant_id=:merchant_id and flag=:flag and from_platform=:from_platform',
// 				array(':merchant_id' => $merchant_id,':flag'=>FLAG_NO,':from_platform'=>FROM_PLATFORM_ALI));
        $obj_menu = Menu::model()->findAll(array(
            'condition' => 'merchant_id=:merchant_id and flag=:flag and from_platform = :from_platform',
            'order' => 'sort',
            'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_ALI)));

        if (!empty($obj_menu)) {
            $i = 0;
            foreach ($obj_menu as $k => $v) {
                if (empty($v->parent_id)) {//是否为一级菜单
                    $arr_menu["button"][$i]["name"] = urlencode($v->menu_name);
                    $j = 0;
                    foreach ($obj_menu as $key => $value) { //循环找出二级菜单
                        if ($value->parent_id == $v->id) {
                            $arr_menu["button"][$i]["subButton"][$j]["name"] = urlencode($value->menu_name);
                            if ($value->type == WQ_MENU_TYPE_WWW || $value->type == WQ_MENU_TYPE_SYSTEM) { //链接网址
                                $arr_menu["button"][$i]["subButton"][$j]["actionType"] = "link";
                                $arr_menu["button"][$i]["subButton"][$j]["actionParam"] = urlencode($value->content);
                            } else { // 图文信息
                                $arr_menu["button"][$i]["subButton"][$j]["actionType"] = "out";
                                $arr_menu["button"][$i]["subButton"][$j]["actionParam"] = urlencode($value->id);
                            }
                            $j++;
                        }
                    }
                    if (!isset($arr_menu["button"][$i]["subButton"])) { //若无二级菜单 则执行以下操作
                        if ($v->type == WQ_MENU_TYPE_WWW || $v->type == WQ_MENU_TYPE_SYSTEM) { //链接网址
                            $arr_menu["button"][$i]["actionType"] = "link";
                            $arr_menu["button"][$i]["actionParam"] = urlencode($v->content);
                        } else { // 图文消息, 文字消息
                            $arr_menu["button"][$i]["actionType"] = "out";
                            $arr_menu["button"][$i]["actionParam"] = urlencode($v->id);
                        }
                    }
                    $i++;
                }
            }
        }
        //数组转化成Json
        $json_menu = urldecode(json_encode($arr_menu));
        // 调用ali接口
        $api = new AliApi('AliApi');
        $response = $api->updateMenu($json_menu);
        $msg = $this->responseMsg($response);
// 		CVarDumper::dump($response);
// 		exit();
        if (!empty($msg)) {
            if ($msg == '修改的菜单不存在') {
                //菜单未被创建过，尝试调用创建菜单接口
                $response = $api->createMenu($json_menu);
                $msg = $this->responseMsg($response);
                if (!empty($msg)) {
                    echo $msg;
                }
            } else {
                echo $msg;
            }
        }
    }

    /**
     * action: 阿里响应结果处理 - 菜单
     */
    public function responseMsg($response = NULL)
    {
        $msg = '';
        if ($response != null) {
            if (isset($response->error_response)) {
                if ($response->error_response->code == 40001) {
                    $msg = "请先在服务窗设置中进行账号绑定";
                } else {
                    //TODO
                    //其他错误情况
                    $msg = $response->error_response->sub_msg;
                }
            } elseif (isset($response->alipay_mobile_public_menu_update_response)) {
                if ($response->alipay_mobile_public_menu_update_response->code == 200) {
                    $msg = "菜单发布成功";
                } else {
                    $msg = $response->alipay_mobile_public_menu_update_response->msg;
                }
            }
        }
        return $msg;
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
            $model['from_platform'] = FROM_PLATFORM_ALI;
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
                'params' => array(':merchant_id' => $merchant_id, ':type' => REPLY_TYPE_BROADCAST, ':flag' => FLAG_NO, ':from_platform' => FROM_PLATFORM_ALI),
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
     * 组装群发广本文字消息内容 - 全体
     * @param $content
     */
    public function setAllBroadcastText($content)
    {
        $msg = array();

        $msg["msgType"] = "text";
        $msg["text"] = array("content" => $content);

        return json_encode($msg);
    }

    /**
     * 组装群发广本图文消息内容 - 全体
     * @param $content
     */
    public function setAllBroadcastImageText($material_id)
    {
        $msg = array();
        $material = Material::model()->findAll('material_id = :material_id', array(':material_id' => $material_id));

        $msg["msgType"] = "image-text";
        $msg["createTime"] = time(date('Ymdhis'));
        $msg["articles"] = array();
        foreach ($material as $key => $value) {
            $msg["articles"][] = array("actionName" => "立即查看",
                "desc" => $value['content'],
                "imageUrl" => IMG_GJ_LIST . $value['cover_img'],
// 									"imageUrl" => 'D:/www/workplace/kuaiguanjia/upload/images/gj/source/'.$material['cover_img'],
                "title" => $value['title'],
                "url" => $value['link_content'],
            );
        }

        return json_encode($msg);
    }

    /**
     * 新增标签
     * @param  $name
     */
    public function addLable($name)
    {
        $lable = array(
            "name" => $name,
        );

        return json_encode($lable);
    }

    /**
     * 新增删除
     * @param  $id
     */
    public function delLable($id)
    {
        $lable = array(
            "id" => $id,
        );

        return json_encode($lable);
    }

    /**
     * 获取分组下的用户
     */
    public function getGroupUser($merchant_id, $group_id, $repeat)
    {
        $user = array();
        //数据库查询
        $cmd = Yii::app()->db->createCommand();
        $cmd->select('*');

        if ($repeat == 1) {
            if (!$group_id) {
                $from = array('wq_user user');
                $where = array(
                    'AND',
                    'user.merchant_id = :merchant_id',
                    'user.flag = :flag',
                    'user.alipay_fuwu_id is not null',
                    'user.wechat_id is null',
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
                    'user.alipay_fuwu_id is not null',
                    'user.wechat_id is null',
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
                    'user.alipay_fuwu_id is not null',
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
                    'user.alipay_fuwu_id is not null',
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
        $list = $cmd->queryAll();

        foreach ($list as $key => $value) {
            $user[$key] = $value['alipay_fuwu_id'];
        }

        return $user;
    }

    /**
     * 标签增加用户
     */
    public function lableUserAdd($lable_id, $user_id)
    {
        $user_lable = array(
            "userId" => $user_id,
            "labelId" => $lable_id,
        );

        return json_encode($user_lable);
    }

    /**
     * action: 阿里响应结果处理 - 新建标签
     */
    public function responseAddLableMsg($response = NULL)
    {
        $result = array();
        if ($response != null) {
            if (isset($response->error_response)) {
                if ($response->error_response->code == 40001) {
                    $result['msg'] = "请先在服务窗设置中进行账号绑定";
                } else {
                    //TODO
                    //其他错误情况
                    $result['msg'] = $response->error_response->sub_msg;
                }
                $result['status'] = $response->error_response->code;
            } elseif (isset($response->alipay_mobile_public_label_add_response)) {
                if ($response->alipay_mobile_public_label_add_response->code == 200) {
                    $result['status'] = ERROR_NONE;
                    $result['lable_id'] = $response->alipay_mobile_public_label_add_response->id;
                } else {
                    $result['status'] = $response->alipay_mobile_public_label_add_response->code;
                }
                $result['msg'] = $response->alipay_mobile_public_label_add_response->msg;
            }
        }
        return $result;
    }

    /**
     * 组装群发广本文字消息内容 - 标签分组
     * @param $content
     * @param $lable_id
     */
    public function setLableBroadcastText($content, $lable_id)
    {
        $msg = array(
            "material" => array(
                "msgType" => "text",
                "text" => array("content" => $content),
            ),

            "filter" => array(
                "template" => '${a}',
                "context" => array(
                    "a" => array(
                        "columnName" => "label_id_list",
                        "op" => "=",
                        "values" => array($lable_id),
                    ),
                ),
            ),
        );

        return json_encode($msg);
    }

    /**
     * 组装群发广本图文消息内容 - 标签分组
     * @param $content
     * @param $lable_id
     */
    public function setLableBroadcastImageText($material_id, $lable_id)
    {
        $material = Material::model()->findAll('material_id = :material_id', array(':material_id' => $material_id));

        $msg = array();
        $msg['material'] = array();
        $msg['material']["msgType"] = "image-text";
        $msg['material']["createTime"] = time(date('Ymdhis'));
        foreach ($material as $key => $value) {
            $msg['material']['articles'][] = array("actionName" => "立即查看",
                "desc" => $value['content'],
                "imageUrl" => IMG_GJ_LIST . $value['cover_img'],
// 									"imageUrl" => 'D:/www/workplace/kuaiguanjia/upload/images/gj/source/'.$material['cover_img'],
                "title" => $value['title'],
                "url" => $value['link_content'],
            );
        }

        $msg['filter'] = array(
            "template" => '${a}',
            "context" => array(
                "a" => array(
                    "columnName" => "label_id_list",
                    "op" => "=",
                    "values" => array($lable_id),
                ),
            ),
        );


        return json_encode($msg);
    }

    /**
     * action: 阿里响应结果处理 - 群发广播（全部用户）
     */
    public function responseBroadcastMsg($response = NULL)
    {
        $msg = '';
        if ($response != null) {
            if (isset($response->error_response)) {
                if ($response->error_response->code == 40001) {
                    $msg = "请先在服务窗设置中进行账号绑定";
                } else {
                    //TODO
                    //其他错误情况
                    $msg = $response->error_response->sub_msg;
                }
            } elseif (isset($response->alipay_mobile_public_message_total_send_response)) {
                if ($response->alipay_mobile_public_message_total_send_response->code == 200) {
                    $msg = "信息群发成功";
                } else {
                    $msg = $response->alipay_mobile_public_message_total_send_response->msg;
                }
            }
        }
        return $msg;
    }

    /**
     * action: 阿里响应结果处理 - 群发广播(标签用户)
     */
    public function responseBroadcastLableMsg($response = NULL)
    {
        $msg = '';
        if ($response != null) {
            if (isset($response->error_response)) {
                if ($response->error_response->code == 40001) {
                    $msg = "请先在服务窗设置中进行账号绑定";
                } else {
                    //TODO
                    //其他错误情况
                    $msg = $response->error_response->sub_msg;
                }
            } elseif (isset($response->alipay_mobile_public_message_label_send_response)) {
                if ($response->alipay_mobile_public_message_label_send_response->code == 200) {
                    $msg = "信息群发成功";
                } else {
                    $msg = $response->alipay_mobile_public_message_label_send_response->msg;
                }
            }
        }
        return $msg;
    }

    /**
     * 保存服务窗用户个人信息
     */
    public function saveUserInfo($id, $userinfo_re, $data)
    {

        $user = new UserUC();

        //修改由服务窗登录后的头像
        if (!isset($data['alipay_avatar']) || empty($data['alipay_avatar'])) {
            if (!isset($userinfo_re->avatar) || empty($userinfo_re->avatar)) {
                $user->editPersonalInformation($id, 'alipay_avatar', '');
            } else {
                $user->editPersonalInformation($id, 'alipay_avatar', $userinfo_re->avatar);
            }
        }

        //若用户头像为空则用支付宝头像填充
        if (!isset($data['avatar']) || empty($data['avatar'])) {
            if (!isset($userinfo_re->avatar) || empty($userinfo_re->avatar)) {
                $user->editPersonalInformation($id, 'avatar', '');
            } else {
                $user->editPersonalInformation($id, 'avatar', $userinfo_re->avatar);
            }
        }

        if (!isset($data['nickname']) || empty($data['nickname'])) {
            if (!isset($userinfo_re->nick_name) || empty($userinfo_re->nick_name)) {
                $user->editPersonalInformation($id, 'nickname', '');
            } else {
                $user->editPersonalInformation($id, 'nickname', $userinfo_re->nick_name);
            }
        }

        if (!isset($data['province']) || empty($data['province'])) {
            if (!isset($userinfo_re->province) || empty($userinfo_re->province)) {
                $user->editPersonalInformation($id, 'province', '');
            } else {
                $user->editPersonalInformation($id, 'province', $userinfo_re->province);
            }
        }

        if (!isset($data['city']) || empty($data['city'])) {
            if (!isset($userinfo_re->city) || empty($userinfo_re->city)) {
                $user->editPersonalInformation($id, 'city', '');
            } else {
                $user->editPersonalInformation($id, 'city', $userinfo_re->city);
            }
        }

        //address
        if (!isset($data['address']) || empty($data['address'])) {
            if (!isset($userinfo_re->province) || empty($userinfo_re->province)) {
                $user->editPersonalInformation($id, 'address', '');
            } else {
                $user->editPersonalInformation($id, 'address', $userinfo_re->province . ' ' . $userinfo_re->city);
            }
        }

        //支付宝服务窗可以获取的字段
        $user->editPersonalInformation($id, 'alipay_avatar', $userinfo_re->avatar);
        $user->editPersonalInformation($id, 'alipay_nickname', $userinfo_re->nick_name);
        $user->editPersonalInformation($id, 'alipay_province', $userinfo_re->province);
        $user->editPersonalInformation($id, 'alipay_city', $userinfo_re->city);
        $user->editPersonalInformation($id, 'alipay_gender', $userinfo_re->gender);
        $user->editPersonalInformation($id, 'alipay_is_student_certified', $userinfo_re->is_student_certified);

        $user->editPersonalInformation($id, 'alipay_user_type_value', $userinfo_re->user_type_value);
        $user->editPersonalInformation($id, 'alipay_is_licence_auth', $userinfo_re->is_licence_auth);
        $user->editPersonalInformation($id, 'alipay_is_certified', $userinfo_re->is_certified);
        $user->editPersonalInformation($id, 'alipay_certified_grade_a', $userinfo_re->is_certify_grade_a);
        $user->editPersonalInformation($id, 'alipay_is_bank_auth', $userinfo_re->is_bank_auth);
        $user->editPersonalInformation($id, 'alipay_is_mobile_auth', $userinfo_re->is_mobile_auth);
        $user->editPersonalInformation($id, 'alipay_user_status', $userinfo_re->user_status);
        $user->editPersonalInformation($id, 'alipay_is_id_auth', $userinfo_re->is_id_auth);
        //修改真实姓名
        //if(!isset($data['name']) || empty($data['name'])){
        //    $re_msg = $user->editPersonalInformation($id, 'name', characet($userinfo_re -> real_name));
        //}

        //修改性别
        if (!isset($data['sex']) || empty($data['sex'])) {
            if ($userinfo_re->gender == 'm' || $userinfo_re->gender == 'M') {
                $user->editPersonalInformation($id, 'sex', 1);
            } elseif ($userinfo_re->gender == 'f' || $userinfo_re->gender == 'F') {
                $user->editPersonalInformation($id, 'sex', 2);
            }
        }

        //授权缓存30天
        Yii::app()->memcache->set($userinfo_re->alipay_user_id, 1, 2592000);

        //修改地址信息
        //if(!isset($data['address']) || empty($data['address'])){
        //    $re_msg = $user->editPersonalInformation($id, 'address', characet($userinfo_re -> address));
        //}
        //修改最后更新时间
        //$re_msg = $user->editPersonalInformation($id, 'last_time', date('Y-m-d H:i:s'));
    }

    /*
     * 转换编码
     */
    function characet($data)
    {
        if (!empty ($data)) {
            $fileType = mb_detect_encoding($data, array(
                'UTF-8',
                'GBK',
                'GB2312',
                'LATIN1',
                'BIG5'
            ));
            if ($fileType != 'UTF-8') {
                $data = mb_convert_encoding($data, 'UTF-8', $fileType);
            }
        }
        return $data;
    }
}