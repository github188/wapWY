<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

class MobileMerchantC extends mainClass
{
    public $page = null;
    //查询商户列表
    /*
	 * $name 商户名
	 * $agentName 所属合作商
	 * $seller_email 支付宝账号
	 * $verify_status 商户审核状态 数组
	 * $status 状态
	 * $gj_open_status  玩券管家开通状态
	 * */
    public function getMerchantList($agentId = '', $name, $agentName, $seller_email, $verify_status, $status, $gj_open_status = '')
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $criteria = new CDbCriteria();
        //商户名搜索
        if (isset($name) && !empty($name)) {
            $criteria->addCondition("name like '%$name%'");
        }


        //所属合作商搜索
        $agentid = array();
        if (isset($agentName) && !empty($agentName)) {
            $criteria_agent = new CDbCriteria();
            $criteria_agent->addCondition("name like '%$agentName%'");
            $criteria_agent->addCondition('flag = :flag');
            $criteria_agent->params[':flag'] = FLAG_NO;
            $agent = Agent::model()->findAll($criteria_agent);
            if ($agent) {
                foreach ($agent as $k => $v) {
                    $agentid[$k] = $v->id;
                }
            }
        } else {
            $agent = Agent::model()->findAll('id=:id and flag = :flag', array(':id' => $agentId, ':flag' => FLAG_NO));
            if ($agent) {
                foreach ($agent as $k => $v) {
                    $agentid[$k] = $v->id;
                }
            }
        }

        //合作商id搜索
        $subagentId = array();
        if (isset($agentId) && !empty($agentId)) {
            $subagent = Agent::model()->findAll('pid=:pid and flag =:flag', array(
                ':pid' => $agentId,
                ':flag' => FLAG_NO
            ));
            if (!empty($subagent)) {
                foreach ($subagent as $k => $v) {
                    $subagentId[$k] = $v->id;
                }
            }
            //合并数组
            $agentid = array_merge($agentid, $subagentId, array($agentId));
            if (!empty($agentid)) {
                $criteria->addInCondition('agent_id', $agentid);
            }
        } else {
            $criteria->addInCondition('agent_id', $agentid);
        }


        //支付宝账号搜索
        if (isset($seller_email) && !empty($seller_email)) {
            $criteria->addCondition('seller_email = :seller_email');
            $criteria->params[':seller_email'] = $seller_email;
        }
        //商户审核状态搜索
        if (isset($status) && !empty($status)) {
            $criteria->addCondition('status = :status');
            $criteria->params[':status'] = $status;
        }
        //商户状态搜索
        if (isset($verify_status) && !empty($verify_status)) {
            $criteria->addInCondition('verify_status', $verify_status);
        }

        //玩券管家开通状态搜索
        if (isset($gj_open_status) && !empty($gj_open_status)) {
            $criteria->addCondition('gj_open_status = :gj_open_status');
            $criteria->params[':gj_open_status'] = $gj_open_status;
        }
        //按创建时间排序
        $criteria->order = 'create_time DESC';


        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;

        //$criteria->addCondition('seller_email != :seller_email');
        //$criteria->params[':seller_email'] = '';

        $pages = new CPagination(Merchant::model()->count($criteria));
        $pages->pageSize = Yii::app()->params['perPage'];
        $pages->applyLimit($criteria);
        $this->page = $pages;
        $merchant = Merchant::model()->findAll($criteria);

        //未录入支付宝商户数量
        $nuinput_merchant = Merchant::model()->findAll('verify_status =:verify_status and flag = :flag', array(
            ':verify_status' => MERCHANT_VERIFY_STATUS_UNINPUT,
            ':flag' => FLAG_NO
        ));
        $nuinput_merchant_num = 0;
        foreach ($nuinput_merchant as $k => $v) {
            if (!empty($v->seller_email)) {
                $nuinput_merchant_num++;
            }
        }

        $data = array();
        if (!empty($merchant)) {
            foreach ($merchant as $k => $v) {
                $data['list'][$k]['id'] = $v->id;
                $data['list'][$k]['merchant_no'] = $v->merchant_no;
                $data['list'][$k]['agent_id'] = $v->agent_id;
                $data['list'][$k]['agent_name'] = $v->agent->name;
                $data['list'][$k]['channel_id'] = $v->channel_id;
                $data['list'][$k]['account'] = $v->account;
                $data['list'][$k]['pwd'] = $v->pwd;
                $data['list'][$k]['name'] = $v->name;
                $data['list'][$k]['partner'] = $v->partner;
                $data['list'][$k]['seller_email'] = $v->seller_email;
                $data['list'][$k]['key'] = $v->key;
                $data['list'][$k]['create_time'] = $v->create_time;
                $data['list'][$k]['last_time'] = $v->last_time;
                $data['list'][$k]['status'] = $v->status;
                $data['list'][$k]['flag'] = $v->flag;
                $data['list'][$k]['alipay_code'] = $v->alipay_code;
                $data['list'][$k]['verify_status'] = $v->verify_status;
                $data['list'][$k]['remark'] = $v->remark;
                $data['list'][$k]['msg_num'] = $v->msg_num;
                $data['list'][$k]['if_stored'] = $v->if_stored;
                $data['list'][$k]['points_rule'] = $v->points_rule;
                $data['list'][$k]['gj_open_status'] = $v->gj_open_status; //玩券管家开通状态
                $data['list'][$k]['gj_product_name'] = isset($v->gjproduct->name) ? $v->gjproduct->name : ''; //管家版本名称
                $data['list'][$k]['remaing_day'] = $this->getRemainDay($v->id, $v->gj_end_time); //玩券管家到期剩余天数
                $data['list'][$k]['agent_name'] = isset($v->agent->name) ? $v->agent->name : '';
                $data['list'][$k]['gj_end_time'] = $v->gj_end_time;
                $data['list'][$k]['if_tryout'] = $v->if_tryout; //是否试用版 1:非试用  2：试用


                //获取商户信息
                $merchantInfo = Merchantinfo::model()->find('merchant_id=:merchant_id', array(
                    ':merchant_id' => $v->id
                ));

                $data['list'][$k]['type'] = $merchantInfo['type'];
                $data['list'][$k]['img'] = $merchantInfo['img'];
                $data['list'][$k]['address'] = $merchantInfo['address'];
                $data['list'][$k]['industry'] = $merchantInfo['industry'];
                $data['list'][$k]['fax'] = $merchantInfo['fax'];
                $data['list'][$k]['zip_code'] = $merchantInfo['zip_code'];
                $data['list'][$k]['contact'] = $merchantInfo['contact'];
                $data['list'][$k]['register_money'] = $merchantInfo['register_money'];
                $data['list'][$k]['income'] = $merchantInfo['income'];
                $data['list'][$k]['employees_num'] = $merchantInfo['employees_num'];
                $data['list'][$k]['is_qs'] = $merchantInfo['is_qs'];
                $data['list'][$k]['signed_intention'] = $merchantInfo['signed_intention'];
                $data['list'][$k]['business_area'] = $merchantInfo['business_area'];
                $data['list'][$k]['customer_groups'] = $merchantInfo['customer_groups'];
            }

            $data['nuinput_merchant_num'] = $nuinput_merchant_num;
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
            return json_encode($result);
        } else {
            $result['status'] = ERROR_NONE;
            $data['nuinput_merchant_num'] = $nuinput_merchant_num;
            $data['list'] = array();
            $result['data'] = $data;
            return json_encode($result);
        }
    }

    //设置支付宝api版本
    public function setAlipayApiVersion($merchant_id, $version)
    {
        $result = array();
        try {
            $merchant = Merchant::model()->findByPk($merchant_id);
            if (!empty($merchant)) {
                $merchant->alipay_api_version = $version;
                if ($merchant->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('商户更新失败');
                }
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

    /**
     * 查询微信商户列表
     */
    public function getWxMerchantList($wechat_verify_status, $merchantno, $merchantname)
    {
        $result = array('status' => 'null', 'type' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $criteria = new CDbCriteria();
            //微信商户状态
            if (!empty($wechat_verify_status)) {
                $criteria->addCondition('merchant.wechat_verify_status=:wechat_verify_status');
                $criteria->params['wechat_verify_status'] = $wechat_verify_status;
            }

            if (!empty($merchantname)) {
                $criteria->addCondition('wx_merchant_name like :wx_merchant_name');
                $criteria->params[':wx_merchant_name'] = '%' . $merchantname . '%';
            } else {
                $criteria->addCondition('wx_abbreviation is not null');
//                 $criteria->params[':wx_abbreviation'] = '';
            }

            if (!empty($merchantno)) {
                $criteria->addCondition('merchant.wechat_merchant_no = :wechat_merchant_no');
                $criteria->params[':wechat_merchant_no'] = $merchantno;
            }

            //按创建时间排序
            $criteria->order = 'create_time DESC';

            $pages = new CPagination(Merchantinfo::model()->with('merchant')->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $model = Merchantinfo::model()->with('merchant')->findAll($criteria);
            $data = array();

            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    if (!empty($value->wx_abbreviation)) {
                        $data[$key]['id'] = $value->id;
                        $data[$key]['wx_merchant_name'] = $value->wx_merchant_name;
                        $merchant_model = Merchant::model()->findByPk($value->merchant_id);
                        if (!empty($merchant_model)) {
                            $data[$key]['wechat_merchant_no'] = $merchant_model->wechat_merchant_no;
                            $data[$key]['agent_name'] = $merchant_model->agent->name;
                            $data[$key]['status'] = $merchant_model->status;
                            $data[$key]['wechat_verify_status'] = $merchant_model->wechat_verify_status;//录入状态
                            $data[$key]['create_time'] = $merchant_model->create_time;//创建时间
                            $data[$key]['wechat_appid'] = $merchant_model->wechat_appid;//appid
                        } else {
                            $data[$key]['wechat_merchant_no'] = '';
                            $data[$key]['agent_name'] = '';
                            $data[$key]['status'] = '';
                            $data[$key]['wechat_verify_status'] = '';//录入状态
                        }
                    }

                }


                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 查询微信商户的状态
     */
    public function getWxState()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('wx_abbreviation!=:wx_abbreviation');
        $criteria->params[':wx_abbreviation'] = '';

        $model = Merchantinfo::model()->findAll($criteria);
        $unsubmit = 0;//未提交
        $uncheck = 0;//待审核
        $in = 0;//微信已录入
        $checked = 0;//审核通过
        $nosign = 0;//未签约
        $sign = 0;//已签约
        $reject = 0;//驳回
        $opened = 0;//已开通

        if (!empty($model)) {
            foreach ($model as $key => $value) {
                if (!empty($value->wx_abbreviation)) {
                    $merchant_model = Merchant::model()->findByPk($value->merchant_id);
                    if (!empty($merchant_model)) {
                        if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT)
                            $unsubmit++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_UNCHECK)
                            $uncheck++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_IN)
                            $in++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_CHECKED)
                            $checked++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_NOSIGN)
                            $nosign++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_SIGN)
                            $sign++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_REJECT)
                            $reject++;
                        else if ($merchant_model->wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_OPEN_WQGJ)
                            $opened++;
                    }
                }
            }
        }
        $result['status'] = ERROR_NONE;
        $type = array();
        $type['unsubmit'] = $unsubmit;
        $type['uncheck'] = $uncheck;
        $type['in'] = $in;
        $type['checked'] = $checked;
        $type['nosign'] = $nosign;
        $type['sign'] = $sign;
        $type['reject'] = $reject;
        $type['opened'] = $opened;
        $result['type'] = $type;

        return json_encode($result);
    }

    /**
     * 待审核微信商户审核通过
     */
    public function MerchantCheckPass($merchantInfoId, $merchantNo)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $model = Merchantinfo::model()->findByPk($merchantInfoId);
            if (!empty($model)) {
                $merchantId = $model->merchant_id;
                $merchant_model = Merchant::model()->findByPk($merchantId);
                $merchant_model->wechat_merchant_no = $merchantNo;
                $merchant_model->wechat_verify_status = WECHATMERCHANT_VERIFY_STATUS_CHECKED;
                $merchant_model->wechat_verify_status_auditpass_time = new CDbExpression('now()');

                if ($merchant_model->update()) {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据更新失败');
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('数据为空');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 微信商户验证通过
     */
    public function WxVerifyThrough($merchantInfoId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $model = Merchantinfo::model()->findByPk($merchantInfoId);
            if (!empty($model)) {
                $merchantId = $model->merchant_id;
                $merchant_model = Merchant::model()->findByPk($merchantId);
                $merchant_model->wechat_verify_status = WECHATMERCHANT_VERIFY_STATUS_NOSIGN;
                $merchant_model->wechat_verify_status_verify_time = new CDbExpression('now()');
                if ($merchant_model->save()) {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 微信商户驳回
     */
    public function WxUnsubmit($merchantInfoId, $reson)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $model = Merchantinfo::model()->findByPk($merchantInfoId);
            if (!empty($model)) {
                $merchantId = $model->merchant_id;
                $merchant_model = Merchant::model()->findByPk($merchantId);
                $merchant_model->remark = $reson;
                $merchant_model->wechat_verify_status = WECHATMERCHANT_VERIFY_STATUS_REJECT;
                $merchant_model->wechat_verify_status_reject_time = new CDbExpression('now()');
                if ($merchant_model->save()) {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 微信商户签约成功
     */
    public function WxSign($merchantInfoId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $model = Merchantinfo::model()->findByPk($merchantInfoId);
            if (!empty($model)) {
                $merchantId = $model->merchant_id;
                $merchant_model = Merchant::model()->findByPk($merchantId);
                $merchant_model->wechat_verify_status = WECHATMERCHANT_VERIFY_STATUS_SIGN;
                $merchant_model->wechat_verify_status_sign_time = new CDbExpression('now()');
                if ($merchant_model->save()) {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 微信商户录入成功
     */
    public function WxIn($merchantInfoId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $model = Merchantinfo::model()->findByPk($merchantInfoId);
            if (!empty($model)) {
                $merchantId = $model->merchant_id;

                $merchant_model = Merchant::model()->findByPk($merchantId);

                $merchant_model->wechat_verify_status = WECHATMERCHANT_VERIFY_STATUS_IN;

                if ($merchant_model->save()) {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 计算玩券管家到期剩余天数
     * $merchant_id  商户id
     * $gj_end_time  玩券管家到期时间
     */
    public function getRemainDay($merchant_id, $gj_end_time)
    {
        $remain_day = '--';
        $nowTime = date('Y-m-d H:i:s');
        if (!empty($gj_end_time)) {
            if ($nowTime > $gj_end_time) {
                return $remain_day;
            } else {
                $res = $this->timediff(strtotime($nowTime), strtotime($gj_end_time));
                return $res;
            }
        } else {
            return $remain_day;
        }
    }

    /**
     *
     * @param unknown $begin_time
     * @param unknown $end_time
     * @return multitype:number
     */
    function timediff($begin_time, $end_time)
    {
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime = $end_time;
        } else {
            $starttime = $end_time;
            $endtime = $begin_time;
        }

        //计算天数
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        //计算小时数
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        //计算分钟数
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        //计算秒数
        $secs = $remain % 60;
        $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
        //return $res['day'].'天'.$res['hour'].'时'.$res['min'].'分'.$res['sec'].'秒';
        return $res;
    }

    /**
     * 获取商户对象
     */
    public function getMerchantInfo($merchant_id)
    {
        $model = Merchant::model()->findByPk($merchant_id);
        return $model;
    }

    //查新微信商户详情
    public function getWxMerchantDetails($id)
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        //验证合作商id
        $data = array();
        $model = Merchantinfo::model()->findByPk($id);
        if (!empty($model)) {
            $data['wx_contact'] = $model->wx_contact;
            $data['wx_tel'] = $model->wx_tel;
            $data['wx_email'] = $model->wx_email;
            $data['wx_abbreviation'] = $model->wx_abbreviation;
            $data['wx_business_category'] = $model->wx_business_category;
            $data['wx_qualifications'] = $model->wx_qualifications;
            $data['wx_product_description'] = $model->wx_product_description;
            $data['wx_customer_service'] = $model->wx_customer_service;
            $data['wx_supply'] = $model->wx_supply;
            $data['wx_merchant_name'] = $model->wx_merchant_name;
            $data['wx_registered_address'] = $model->wx_registered_address;
            $data['wx_business_license_no'] = $model->wx_business_license_no;
            $data['wx_operating_range'] = $model->wx_operating_range;
            $data['wx_business_deadline_start'] = $model->wx_business_deadline_start;
            $data['wx_business_deadline_end'] = $model->wx_business_deadline_end;
            $data['wx_business_deadline_longterm'] = $model->wx_business_deadline_longterm;
            $data['wx_business_license_img'] = $model->wx_business_license_img;
            $data['wx_organization_code'] = $model->wx_organization_code;
            $data['wx_organization_code_start'] = $model->wx_organization_code_start;
            $data['wx_organization_code_end'] = $model->wx_organization_code_end;
            $data['wx_organization_code_longterm'] = $model->wx_organization_code_longterm;
            $data['wx_organization_code_img'] = $model->wx_organization_code_img;
            $data['wx_credentials_user_type'] = $model->wx_credentials_user_type;
            $data['wx_credentials_user_name'] = $model->wx_credentials_user_name;
            $data['wx_credentials_type'] = $model->wx_credentials_type;
            $data['wx_credentials_positive'] = $model->wx_credentials_positive;
            $data['wx_credentials_opposite'] = $model->wx_credentials_opposite;
            $data['wx_credentials_start'] = $model->wx_credentials_start;
            $data['wx_credentials_end'] = $model->wx_credentials_end;
            $data['wx_credentials_longterm'] = $model->wx_credentials_longterm;
            $data['wx_credentials_no'] = $model->wx_credentials_no;
            $data['wx_account_type'] = $model->wx_account_type;
            $data['wx_account_name'] = $model->wx_account_name;
            $data['wx_bank_name'] = $model->wx_bank_name;
            $area = explode(',', $model->wx_bank_area);
            $data['wx_bank_province'] = $area[0];
            $data['wx_bank_city'] = $area[1];
            $data['wx_bank_subbranch'] = $model->wx_bank_subbranch;
            $data['wx_bank_account'] = $model->wx_bank_account;

            $result['data'] = $data;
            $result['status'] = ERROR_NONE;

        } else {
            //没有找到合作商
            $result['status'] = ERROR_PARAMETER_FORMAT;
            $result['errMsg'] = '该商户不存在';
        }
        return json_encode($result);
    }

    //查询商户详情
    public function getMerchantDetails($merchantId)
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        //验证合作商id
        if (!isset($merchantId) || empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            return json_encode($result);
        }
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params[':id'] = $merchantId;
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;
        $merchant = Merchant::model()->find($criteria);
        if ($merchant) {
            $result['status'] = ERROR_NONE;
            //获取商户信息
            $merchantInfo = Merchantinfo::model()->find('merchant_id=:merchant_id', array(
                ':merchant_id' => $merchant->id
            ));
            //合作商详情
            $result['data'] = array(
                'id' => $merchant->id,
                'agent_id' => $merchant->agent_id,
                'channel_id' => $merchant->channel_id,
                'account' => $merchant->account,
                'pwd' => $merchant->pwd,
                'name' => $merchant->name,
                'partner' => $merchant->partner,
                'seller_email' => $merchant->seller_email,
                'key' => $merchant->key,
                'create_time' => $merchant->create_time,
                'last_time' => $merchant->last_time,
                'status' => $merchant->status,
                'flag' => $merchant->flag,
                'alipay_code' => $merchant->alipay_code,
                'verify_status' => $merchant->verify_status,
                'remark' => $merchant->remark,
                'msg_num' => $merchant->msg_num,
                'if_stored' => $merchant->if_stored,
                'points_rule' => $merchant->points_rule,
                'merchant_number' => $merchant->merchant_number,
                'agent_name' => $merchant->agent->name,

                'type' => $merchantInfo->type,
                'img' => $merchantInfo->img,
                'address' => $merchantInfo->address,
                'industry' => $merchantInfo->industry,
                'fax' => $merchantInfo->fax,
                'zip_code' => $merchantInfo->zip_code,
                'contact' => $merchantInfo->contact,
                'register_money' => $merchantInfo->register_money,
                'income' => $merchantInfo->income,
                'employees_num' => $merchantInfo->employees_num,
                'is_qs' => $merchantInfo->is_qs,
                'signed_intention' => $merchantInfo->signed_intention,
                'business_area' => $merchantInfo->business_area,
                'customer_groups' => $merchantInfo->customer_groups,


            );
            return json_encode($result);
        } else {
            //没有找到合作商
            $result['status'] = ERROR_PARAMETER_FORMAT;
            $result['errMsg'] = '该商户不存在';
            return json_encode($result);
        }
    }

    //添加商户
    /*$agentId 所属分销商 必填 (默认仁通渠道)
	 * $name 商户名 必填
	 * $type 商户类型 必填
	 * $seller_email 支付宝账号 个体商户必填
	 * $industry 所属行业 必填
	 * $address 地址 必填
	 * $zip_code 邮编
	 * $fax 传真
	 * $img 上传图片
	 * $contact 联系人信息 必填
	 * $register_money 注册资金
	 * $income 预计年收入
	 * $employees_num 员工人数
	 * $is_qs 是否清算类商户
	 * $signed_intention 签约用意
	 * $business_area 营业场所面积
	 * $customer_groups 客户群体
	 *
	 * */
    public function addMerchant($agentId, $name, $type, $seller_email, $industry, $address, $zip_code, $fax, $img, $contact, $register_money, $income, $employees_num, $is_qs, $signed_intention, $business_area, $customer_groups)
    {
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        //验证商户名
        if (!isset($name) || empty($name)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = '参数name缺失';
            $flag = 1;
        } else {
            $model = Merchant::model()->find('name=:name and status=:status and flag=:flag', array(
                ':name' => $name,
                ':status' => MERCHANT_STATUS_NORMAL,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $errMsg = 'name';
                $result['errMsg'] = $errMsg;
                return json_encode($result);
            }
        }
        //验证商户类型
        if (!isset($type) || empty($type)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数type缺失';
            $flag = 1;
        }
        //验证支付宝账号
        if (!isset($seller_email) || empty($seller_email)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数seller_email缺失';
            $flag = 1;
        } else {
            $model = Merchant::model()->find('seller_email=:seller_email and status=:status and flag=:flag', array(
                ':seller_email' => $seller_email,
                ':status' => MERCHANT_STATUS_NORMAL,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $errMsg = 'seller_email';
                $result['errMsg'] = $errMsg;
                return json_encode($result);
            }
        }
        //验证所属行业
        if (!isset($industry) || empty($industry)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数industry缺失';
            $flag = 1;
        }
        //验证地址
        if (!isset($address) || empty($address)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数address缺失';
            $flag = 1;
        }
        //验证联系人信息
        if (!isset($contact) || empty($contact)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数contact缺失';
            $flag = 1;
        }

        if ($flag == 1) {
            $result['errMsg'] = $errMsg;
            return json_encode($result);
        }
        //添加商户
        $merchant = new Merchant();
        $encrypt_id = $this->getEncryptRandChar(6);
        $merchant_temp = Merchant::model()->find('encrypt_id =:encrypt_id and flag =:flag', array(
            ':encrypt_id' => $encrypt_id,
            ':flag' => FLAG_NO
        ));
        while (!empty($merchant_temp)) {
            $encrypt_id = $this->getEncryptRandChar(6);
            $merchant_temp = Merchant::model()->find('encrypt_id =:encrypt_id and flag =:flag', array(
                ':encrypt_id' => $encrypt_id,
                ':flag' => FLAG_NO
            ));
        }
        $merchant->encrypt_id = $encrypt_id;
        $merchant->agent_id = $agentId;
        $merchant->name = $name;
        $merchant->seller_email = $seller_email;
        $merchant->create_time = new CDbExpression('now()');
        //添加商户信息
        $merchantInfo = new Merchantinfo();
        $merchantInfo->merchant_id = $merchant->id;
        $merchantInfo->industry = $industry;
        $merchantInfo->address = $address;
        $merchantInfo->contact = $contact;
        $merchantInfo->type = $type;

        //非必填项
        if (isset($zip_code) && !empty($zip_code)) {
            $merchantInfo->zip_code = $zip_code;
        }
        if (isset($fax) && !empty($fax)) {
            $merchantInfo->fax = $fax;
        }
        if (isset($img) && !empty($img)) {
            $merchantInfo->img = $img;
        }
        if (isset($register_money) && !empty($register_money)) {
            $merchantInfo->register_money = $register_money;
        }

        if (isset($income) && !empty($income)) {
            $merchantInfo->income = $income;
        }

        if (isset($employees_num) && !empty($employees_num)) {
            $merchantInfo->employees_num = $employees_num;
        }
        if (isset($is_qs) && !empty($is_qs)) {
            $merchantInfo->is_qs = $is_qs;
        }
        if (isset($signed_intention) && !empty($signed_intention)) {
            $merchantInfo->signed_intention = $signed_intention;
        }
        if (isset($business_area) && !empty($business_area)) {
            $merchantInfo->business_area = $business_area;
        }
        if (isset($customer_groups) && !empty($customer_groups)) {
            $merchantInfo->customer_groups = $customer_groups;
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            if ($merchant->save()) {
                $merchantInfo->merchant_id = $merchant->id;
                if ($merchantInfo->save()) {
                    $result['status'] = ERROR_NONE;
                    $result['data'] = $merchant->id;
                    $transaction->commit(); //数据提交
                } else {
                    //商户信息保存失败
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('商户信息保存失败');
                }
            } else {
                //商户保存失败
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('商户保存失败');
            }

        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);


    }


    //产生六位 数字+字母随机字符串，首位不为0
    function getEncryptRandChar($length)
    {
        $str = null;
        $strPol = "0123456789abcdefghijklmnopqrstuvwxyz";
        $strPol_nozero = "123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $str .= $strPol_nozero[rand(0, $max - 1)];
            } else {
                $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
            }
        }
        return $str;
    }


    //申请签约
    /* $merchantId 商户id 必填
	 * $register_money 注册资金
	 * $income 预计年收入
	 * $employees_num 员工人数
	 * $is_qs 是否清算类商户
	 * $signed_intention 签约用意
	 * $business_area 营业场所面积
	 * $customer_groups 客户群体
	 * */
    public function applySign($merchantId, $register_money, $income, $employees_num, $is_qs, $signed_intention, $business_area, $customer_groups)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        $errMsg = '';
        //验证商户名
        if (!isset($merchantId) || empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 1) {
            $result['errMsg'] = $errMsg;
            return json_encode($result);
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params[':id'] = $merchantId;
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;
        $merchant = Merchant::model()->find($criteria);
        $merchantInfo = Merchantinfo::model()->find('merchant_id=:merchant_id', array(
            ':merchant_id' => $merchant->id
        ));

        if (!isset($register_money) || empty($register_money)) {
            if (!isset($merchantInfo->register_money) || empty($merchantInfo->register_money)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数register_money缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->register_money = $register_money;
        }


        if (!isset($income) || empty($income)) {
            if (!isset($merchantInfo->income) || empty($merchantInfo->income)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数income缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->income = $income;
        }


        if (!isset($employees_num) || empty($employees_num)) {
            if (!isset($merchantInfo->employees_num) || empty($merchantInfo->employees_num)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数employees_num缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->employees_num = $employees_num;
        }


        if (!isset($is_qs) || empty($is_qs)) {
            if (!isset($merchantInfo->is_qs) || empty($merchantInfo->is_qs)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数is_qs缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->is_qs = $is_qs;
        }


        if (!isset($signed_intention) || empty($signed_intention)) {
            if (!isset($merchantInfo->signed_intention) || empty($merchantInfo->signed_intention)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数signed_intention缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->signed_intention = $signed_intention;
        }


        if (!isset($business_area) || empty($business_area)) {
            if (!isset($merchantInfo->business_area) || empty($merchantInfo->business_area)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数business_area缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->business_area = $business_area;
        }


        if (!isset($customer_groups) || empty($customer_groups)) {
            if (!isset($merchantInfo->customer_groups) || empty($merchantInfo->customer_groups)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . '参数customer_groups缺失';
                $flag = 1;
            }
        } else {
            $merchantInfo->customer_groups = $customer_groups;
        }

        if ($flag == 1) {
            $result['errMsg'] = $errMsg;
            return json_encode($result);
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            if ($merchantInfo->update()) {
                if ($merchant->verify_status != MERCHANT_VERIFY_STATUS_SIGN_SUCCESS) {
                    $merchant->verify_status = MERCHANT_VERIFY_STATUS_NUSIGN;
                }
                if ($merchant->update()) {
                    $result['status'] = ERROR_NONE;
                    $transaction->commit(); //数据提交
                } else {
                    //商户保存失败
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('商户保存失败');
                }

            } else {
                //商户保存失败
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('商户签约信息保存失败');
            }
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 商户登录
     * @param unknown $account
     * @param unknown $pwd
     * @throws Exception
     * @return string
     */
    public function Login($account, $pwd)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            //参数验证
            if (empty($account)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数account不能为空');
            }
            if (empty($pwd)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数pwd不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('account = :account');
            $criteria->params[':account'] = $account;
            $criteria->addCondition('pwd = :pwd');
            $criteria->params[':pwd'] = md5($pwd);
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Merchant::model()->find($criteria);

            if (empty($model)) {
                $manager = Manager::model()->find('account=:account and pwd=:pwd and flag = :flag',
                    array(':account' => $account, ':pwd' => md5($pwd), ':flag' => FLAG_NO));

                if (empty ($manager)) {
                    $result ['status'] = ERROR_NO_DATA;
                    throw new Exception ('账号密码不正确');
                } else {
                    $adminC = new AdminC ();
                    $res = $adminC->managerLogin($account, $pwd);
                    $res = json_decode($res, true);
                    if ($res['status'] == ERROR_NONE) {
                        $result['data'] = array(
                            'id' => $res['data']['list']['id'],  //管理员id
                            'account' => $res['data']['list']['account'],  //管理员账号
                            'name' => $res['data']['list']['name'],    //管理员名称
                            'merchant_id' => $res['data']['list']['merchant_id'],  //管理员所属商户id
                            'right' => $res['data']['list']['right'],  //管理员操作权限
                            'store_id' => $res['data']['list']['store_id'], //操作门店
                            'role' => $res['data']['list']['role'],  //角色：管理员
                            'merchant_name' => $res['data']['list']['merchant_name'] . '-' . $res['data']['list']['name'],
                            'merchant_type_name' => $res['data']['list']['merchant_type_name'], //商户版本
// 						         'merchant_type_id' => $res['data']['list']['merchant_type_id'],
                            'merchant_if_try_out' => $res['data']['list']['merchant_if_try_out'],
                            'merchant_time_limit' => $res['data']['list']['merchant_time_limit']
                        );
                        $result ['status'] = ERROR_NONE;
                    }
                    return json_encode($result);;
                }
            }
// 			$management = Management::model() -> findByPk($merchant -> management_id);
// 			$model = Merchant::model()->findByPk($management -> merchant_id);

            $type_name = $GLOBALS['__WANQUAN_TYPE'][WANQUAN_TYPE_CASH];//玩券管家版本名
            $type_id = WANQUAN_TYPE_CASH;//玩券管家版本
            $if_tryout = IF_TRYOUT_NO;
            //判断状态
            if ($model->status == MERCHANT_STATUS_LOCK) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('登录失败');
            }

            //判断是否是营销版
            if ($model->gj_product_id == WANQUAN_TYPE_MARKETING) {
                //如果是营销版 则 判断是否过期
                if (strtotime(date('Y-m-d 23:59:59', strtotime($model->gj_end_time))) > time()) {
                    //如果未过期 则 判断是否是试用 && 是否使用状态开启 ||非试用
                    if (($model->if_tryout == IF_TRYOUT_YES && $model->tryout_status == TRYOUT_STATUS_NORMAL) || $model->if_tryout == IF_TRYOUT_NO) {
                        //非试用 或 （试用且试用状态正常） 则 营销版登录
                        $type_name = $GLOBALS['__WANQUAN_TYPE'][WANQUAN_TYPE_MARKETING];//玩券管家版本名
                        $type_id = WANQUAN_TYPE_MARKETING;//玩券管家版本
                        if ($model->if_tryout == IF_TRYOUT_YES) {
                            $if_tryout = IF_TRYOUT_YES;
                        }
                    }
                }
            }
// 			//判断是否试用 且判断试用状态
// 			if($model -> if_tryout == IF_TRYOUT_YES && $model -> tryout_status == TRYOUT_STATUS_LOCK){
// 				$result['status'] = ERROR_NO_DATA;

// 			}

// 			if($model -> gj_open_status == GJ_OPEN_STATUS_OPEN){
// 				if($model -> gj_product_id != 1){
// 					//判断是否过期
// 					if(strtotime($model -> gj_end_time) < time()){
// 						$model -> gj_open_status = GJ_OPEN_STATUS_OVERTIME;
// 						if($model -> save()){
// 							$result['status'] = ERROR_NO_DATA;
// 							throw new Exception('玩券管家已过期');
// 						}
// 					}
// 				}
// 			}elseif ($model -> gj_open_status == GJ_OPEN_STATUS_NULL){
// 				$result['status'] = ERROR_NO_DATA;
// 				throw new Exception('未开通玩券管家');
// 			}elseif ($model -> gj_open_status == GJ_OPEN_STATUS_OVERTIME){
// 				$result['status'] = ERROR_NO_DATA;
// 				throw new Exception('玩券管家已过期');
// 			}

            $product = GjProduct::model()->find('id=:id', array(
                ':id' => $model->gj_product_id
            ));
            //如果没有服务窗识别码，就创建一个
            if (empty($model->encrypt_id)) {
                $encrypt_id = $this->getEncryptRandChar(6);
                $merchant_temp = Merchant::model()->find('encrypt_id =:encrypt_id and flag =:flag', array(
                    ':encrypt_id' => $encrypt_id,
                    ':flag' => FLAG_NO
                ));
                while (!empty($merchant_temp)) {
                    $encrypt_id = $this->getEncryptRandChar(6);
                    $merchant_temp = Merchant::model()->find('encrypt_id =:encrypt_id and flag =:flag', array(
                        ':encrypt_id' => $encrypt_id,
                        ':flag' => FLAG_NO
                    ));
                }
                $model->encrypt_id = $encrypt_id;
                if ($model->update()) {

                } else {
                    throw new Exception('服务窗识别码创建失败');
                }
            }

            //如果没有微信token，就创建一个
            if (empty($model->wechat_token)) {
                $token = $this->getEncryptRandChar(8);
                $merchant_token = Merchant::model()->find('wechat_token =:wechat_token and flag =:flag', array(
                    ':wechat_token' => $token,
                    ':flag' => FLAG_NO
                ));
                while (!empty($merchant_token)) {
                    $token = $this->getEncryptRandChar(8);
                    $merchant_token = Merchant::model()->find('wechat_token =:wechat_token and flag =:flag', array(
                        ':wechat_token' => $token,
                        ':flag' => FLAG_NO
                    ));
                }
                $model->wechat_token = $token;
                if ($model->update()) {

                } else {
                    throw new Exception('微信公众号TOKEN创建失败');
                }
            }

            //判断是否有默认的会员等级，没有则添加一个
            $user_grade_default = UserGrade::model()->find('merchant_id =:merchant_id and flag =:flag and if_default =:if_default', array(
                ':merchant_id' => $model->id,
                ':flag' => FLAG_NO,
                ':if_default' => USER_GRADE_DEFAULT_YES
            ));
            if (empty($user_grade_default)) {
                $user_grade = new UserGrade();
                $user_grade->merchant_id = $model->id;
                $user_grade->name = '普通会员';
                $user_grade->membercard_img = 'style1.png';
                $user_grade->points_rule = 0;
                $user_grade->discount = 1;
                $user_grade->create_time = new CDbExpression('now()');
                $user_grade->if_default = USER_GRADE_DEFAULT_YES;

                if ($user_grade->save()) {

                } else {
                    throw new Exception('默认会员等级初始失败');
                }
            }


            //设置默认相册
            $album = Album::model()->findAll('merchant_id =:merchant_id and flag =:flag', array(
                ':merchant_id' => $model->id,
                ':flag' => FLAG_NO
            ));
            if (empty($album)) {
                $album1 = new Album();
                $album1->merchant_id = $model->id;
                $album1->name = '菜品';
                $album1->create_time = new CDbExpression('now()');
                $album1->save();

                $album2 = new Album();
                $album2->merchant_id = $model->id;
                $album2->name = '环境';
                $album2->create_time = new CDbExpression('now()');
                $album2->save();

                $album3 = new Album();
                $album3->merchant_id = $model->id;
                $album3->name = '其他';
                $album3->create_time = new CDbExpression('now()');
                $album3->save();

            }

            $merchantInfo = Merchantinfo::model()->find('merchant_id =:merchant_id', array(
                ':merchant_id' => $model->id,
            ));
            $merchantname = $model->wq_m_name;//empty($model -> name)?$merchantInfo -> wx_merchant_name:$model -> name;
            $result['data'] = array(
                'id' => $model->id,
                'account' => $model->account,
                'name' => $merchantname,
                'type_name' => $type_name,
                'type_id' => $type_id,
                'time_limit' => $model->gj_end_time,
                'if_tryout' => $if_tryout,
                'role' => WQ_ROLE_MERCHANT,
            );

            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }


    //修改商户
    /**
     * $merchantId 商户id 必填
     * $agentId 合作商id
     * $channelId 渠道商id
     * $account 账号
     * $pwd 密码
     * $name 商户名
     * $partner 合作者身份id
     * $sellerEmail 支付宝账号
     * $key 安全校验码
     * $status 状态
     * $flag 删除标志位
     * $alipayCode 商户编号
     * $verify_status 审核状态
     * $remark 备注
     * $msg_num 可使用短信条数
     * $ifStored 是否开启储值
     * $pointsRule 积分规则
     *
     */
    public function editMerchant($merchantId, $agentId = '', $channelId = '', $account = '', $pwd = '', $name = '', $partner = '', $sellerEmail = '', $key = '', $status = '', $flag = '', $alipayCode = '', $verify_status = '', $remark = '', $msg_num = '', $ifStored = '', $pointsRule = '', $merchant_no = '')
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        if (isset($merchantId) && !empty($merchantId)) {
            //数据库商户
            $merchant = Merchant::model()->find('id=:id and flag=:flag', array(
                ':id' => $merchantId,
                ':flag' => FLAG_NO
            ));

            if (empty($merchant)) {
                //商户不存在
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '该商户不存在';
                return json_encode($result);
            }
        } else {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            return json_encode($result);
        }
        //修改所属合作商
        if (isset($agentId) && !empty($agentId)) {
            $model = null;
            //判断合作商id是否有效
            $model = Agent::model()->find('id=:id and flag=:flag', array(
                ':id' => $agentId,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                $merchant->agent_id = $agentId;
            }
        }
        //渠道商id修改
        if (isset($channelId) && !empty($channelId)) {
            $merchant->channel_id = $channelId;
        }

        if (isset($account) && !empty($account)) {
            $model = null;
            //判断账号是否已存在
            $model = Merchant::model()->find('account =:account and flag =:flag', array(
                ':account' => $account,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                //账号已存在
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'account';
                return json_encode($result);
            } else {
                $merchant->account = $account;
            }
        }
        //修改密码
        if (isset($pwd) && !empty($pwd)) {
            $merchant->pwd = $pwd;
        }
        //修改商户名
        if (isset($name) && !empty($name)) {
            $model = null;
            //判断商户是否已存在
            $model = Merchant::model()->find('name =:name and flag =:flag', array(
                ':name' => $name,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                //商户名已存在
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'name';
                return json_encode($result);
            } else {
                $merchant->name = $name;
            }
        }
        //修改合作者身份id
        if (isset($partner) && !empty($partner)) {
            $merchant->partner = $partner;
        }
        //修改支付宝账号
        if (isset($sellerEmail) && !empty($sellerEmail)) {
            $model = null;
            //判断商户是否已存在
            $model = Merchant::model()->find('seller_email =:seller_email and flag =:flag', array(
                ':seller_email' => $sellerEmail,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                //商户名已存在
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'sellerEmail';
                return json_encode($result);
            } else {
                $merchant->seller_email = $sellerEmail;
            }
        }
        //修改安全校验码
        if (isset($key) && !empty($key)) {
            $merchant->key = $key;
        }

        //修改商户状态 正常和锁定
        if (isset($status) && !empty($status)) {
            if (($merchant->status == MERCHANT_STATUS_NORMAL && $status == MERCHANT_STATUS_LOCK)
                || ($merchant->status == MERCHANT_STATUS_LOCK && $status == MERCHANT_STATUS_NORMAL)
            ) {
                $merchant->status = $status;
            } else {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = 'status';
                return json_encode($result);
            }
        }

        //删除标志位修改
        if (isset($flag) && !empty($flag)) {
            if (($merchant->flag == FLAG_NO && $flag == FLAG_YES)
                || ($merchant->flag == FLAG_YES && $flag == FLAG_NO)
            ) {
                $merchant->flag = $flag;
            } else {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = 'flag';
                return json_encode($result);
            }
        }
        //商户编号修改
        if (isset($alipayCode) && !empty($alipayCode)) {
            $merchant->alipay_code = $alipayCode;
        }

        //商户号修改
        if (isset($merchant_no) && !empty($merchant_no)) {
            $merchant->merchant_no = $merchant_no;
        }

        //修改商户审核状态
        if (isset($verify_status) && !empty($verify_status)) {
            //商户未录入支付宝可以修改为 已录入支付宝，未签约，驳回
            if ($merchant->verify_status == MERCHANT_VERIFY_STATUS_UNINPUT) {
                if ($verify_status == MERCHANT_VERIFY_STATUS_INPUT_SUCCESS || $verify_status == MERCHANT_VERIFY_STATUS_REJECT || $verify_status == MERCHANT_VERIFY_STATUS_NUSIGN) {
                    $merchant->verify_status = $verify_status;
                }
                //商户已录入支付宝可以修改为 未签约 ， 驳回
            } elseif ($merchant->verify_status == MERCHANT_VERIFY_STATUS_INPUT_SUCCESS) {
                if ($verify_status == MERCHANT_VERIFY_STATUS_NUSIGN || $verify_status == MERCHANT_VERIFY_STATUS_REJECT) {
                    $merchant->verify_status = $verify_status;
                }
                //商户未签约可以修改为 审核中 ， 驳回
            } elseif ($merchant->verify_status == MERCHANT_VERIFY_STATUS_NUSIGN) {
                if ($verify_status == MERCHANT_VERIFY_STATUS_AUDITING || $verify_status == MERCHANT_VERIFY_STATUS_REJECT) {
                    $merchant->verify_status = $verify_status;
                }
                //商户审核中可修改为 已签约，驳回
            } elseif ($merchant->verify_status == MERCHANT_VERIFY_STATUS_AUDITING) {
                if ($verify_status == MERCHANT_VERIFY_STATUS_SIGN_SUCCESS || $verify_status == MERCHANT_VERIFY_STATUS_REJECT) {
                    $merchant->verify_status = $verify_status;
                }
                //商户驳回状态可修改为 未签约，未录入支付宝
            } elseif ($merchant->verify_status == MERCHANT_VERIFY_STATUS_REJECT) {
                if ($verify_status == MERCHANT_VERIFY_STATUS_NUSIGN || $verify_status == MERCHANT_VERIFY_STATUS_UNINPUT) {
                    $merchant->verify_status = $verify_status;
                }
            }

        }

        //修改备注
        if (isset($remark) && !empty($remark)) {
            $merchant->remark = $remark;
        }

        //可使用短信条数修改
        if (isset($msg_num) && !empty($msg_num)) {
            $merchant->msg_num = $msg_num;
        }

        //开启关闭储值功能
        if (isset($ifStored) && !empty($ifStored)) {
            if (($merchant->if_stored == MEMBERSHIP_STORED_STATUS_CLOSE && $ifStored == MEMBERSHIP_STORED_STATUS_OPEN)
                || ($merchant->if_stored == MEMBERSHIP_STORED_STATUS_OPEN && $ifStored == MEMBERSHIP_STORED_STATUS_CLOSE)
            ) {
                $merchant->if_stored = $ifStored;
            } else {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                $result['errMsg'] = 'ifStored';
                return json_encode($result);
            }
        }

        //修改积分规则
        if (isset($pointsRule) && !empty($pointsRule)) {
            $merchant->points_rule = $pointsRule;
        }

        if ($merchant->update()) {
            $result['status'] = ERROR_NONE;
            return json_encode($result);
        } else {
            $result['status'] = ERROR_SAVE_FAIL;
            $result['errMsg'] = '修改失败';
            return json_encode($result);
        }
    }


    /**
     * 邀请码检查
     * @param unknown $code
     * @throws Exception
     * @return string
     */
    public function checkInviteCode($code)
    {
        $result = array();
        try {
            //参数验证
            if (empty($code)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数code不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('invite_code = :invite_code');
            $criteria->params[':invite_code'] = $code;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = GjOrder::model()->find($criteria);

            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无效的邀请码');
            }
            //判断状态
            if ($model->order_status != GJORDER_STATUS_NUUSE) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('邀请码已被使用');
            }

            $result['data'] = array('order_id' => $model->id);
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }




    //在线店铺管理设置
    /**
     * $merchantId 商户id
     * $store 选择门店id
     * $ifBook 是否开启预定
     * $ifCheck 是否开启
     * $ifCoupons 是否开启优惠券
     * $ifHongbao 是否开启红包
     * $coupons   优惠券和红包id
     */
    public function addOnlineShop($merchantId, $img, $logo_img, $store, $ifBook, $ifCheck = '', $ifCoupons, $ifHongbao, $coupons = '', $introduction = '', $name = '')
    {
        $onlineShop = Onlineshop::model()->find('merchant_id=:merchant_id and flag=:flag', array(
            ':merchant_id' => $merchantId,
            ':flag' => FLAG_NO
        ));
        if (empty($onlineShop)) {
            $onlineShop = new Onlineshop();
            $onlineShop->flag = FLAG_NO;
        }
        $onlineShop->merchant_id = $merchantId;
        $onlineShop->store_id = $store;
        $onlineShop->if_book = $ifBook;
        $onlineShop->if_check = $ifCheck;
        $onlineShop->if_coupons = $ifCoupons;
        $onlineShop->if_hongbao = $ifHongbao;
        $onlineShop->coupons_id = $coupons;
        $onlineShop->introduction = $introduction;
        $onlineShop->name = $name;
        if (!empty($img)) {
            $onlineShop->img = $img;
        }
        if (!empty($logo_img)) {
            $onlineShop->logo_img = $logo_img;
        }
        $onlineShop->create_time = new CDbExpression('now()');
        if ($onlineShop->save()) {
            $result['status'] = ERROR_NONE;
        } else {
            $result['status'] = ERROR_SAVE_FAIL;
            $result['errMsg'] = '添加失败';
        }
        return json_encode($result);
    }


    //显示在线店铺管理所有复选框是否选中
    /**
     * merchantId 商户id
     */
    public function Show($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $onlineshop = Onlineshop::model()->find('merchant_id=:merchant_id and flag=:flag', array(
                ':merchant_id' => $merchantId,
                ':flag' => FLAG_NO
            ));
            if (!empty($onlineshop)) {
                $data = array();
                $data['img'] = $onlineshop->img;
                $data['logo_img'] = $onlineshop->logo_img;
                $data['if_book'] = $onlineshop->if_book;
                $data['if_check'] = $onlineshop->if_check;
                $data['if_coupons'] = $onlineshop->if_coupons;
                $data['if_hongbao'] = $onlineshop->if_hongbao;
                $data['store_id'] = $onlineshop->store_id;
                $data['coupons_id'] = $onlineshop->coupons_id;
                $data['introduction'] = $onlineshop->introduction;
                $data['name'] = $onlineshop->name;
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    //在线店铺管理门店复选框选择的显示
    /**
     * merchantId 商户id
     */
    public function StoreChoose($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $store = Store::model()->findall($criteria);
            $chose = array();
            if (!empty($store)) {
                foreach ($store as $key => $value) {
                    $chose[$key]['id'] = $value['id'];
                    $chose[$key]['name'] = $value['name'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $chose;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无数据';
            }
        }
        return json_encode($result);
    }

    //在线店铺管理红包复选框的显示
    /**
     * merchantId 商户id
     */
    public function CouponsList($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('type = :type');
            $criteria->params[':type'] = COUPON_TYPE_REDENVELOPE;
            $coupons = Coupons::model()->findall($criteria);
            $coupon = array();
            if (!empty($coupons)) {
                foreach ($coupons as $key => $value) {
                    $coupon[$key]['id'] = $value['id'];
                    $coupon[$key]['name'] = $value['name'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $coupon;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无数据';
            }
        }
        return json_encode($result);
    }

    //在线店铺管理优惠券复选框的显示
    /**
     * merchantId 商户id
     */
    public function CoupontList($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('type = :type');
            $criteria->params[':type'] = COUPON_TYPE_CASH;
            $coupons = Coupons::model()->findall($criteria);
            $coupont = array();
            if (!empty($coupons)) {
                foreach ($coupons as $key => $value) {
                    $coupont[$key]['id'] = $value['id'];
                    $coupont[$key]['name'] = $value['name'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $coupont;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无数据';
            }
        }
        return json_encode($result);
    }







    //修改商户签约信息
    /*
         *
         * $merchantId 商户id 必填
         * $register_money 注册资本
         * $income 预计年收入
         * $employees_num 员工人数
         * $is_qs 是否清算类商户
         * $signed_intention 签约用意
         * $business_area 营业场所面积
         * $customer_groups 客户对象
         * */
    public function EditMerchantInfo($merchantId, $register_money = '', $income = '', $employees_num = '', $is_qs = '', $signed_intention = '', $business_area = '', $customer_groups = '')
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        if (isset($merchantId) && !empty($merchantId)) {
            $merchantinfo = Merchantinfo::model()->find('merchant_id = :merchant_id', array(
                ':merchant_id' => $merchantId,
            ));
            if ($merchantinfo) {
                if (isset($register_money) && !empty($register_money)) {
                    $merchantinfo->register_money = $register_money;
                }

                if (isset($income) && !empty($income)) {
                    $merchantinfo->income = $income;
                }

                if (isset($employees_num) && !empty($employees_num)) {
                    $merchantinfo->employees_num = $employees_num;
                }

                if (isset($is_qs) && !empty($is_qs)) {
                    $merchantinfo->is_qs = $is_qs;
                }

                if (isset($signed_intention) && !empty($signed_intention)) {
                    $merchantinfo->signed_intention = $signed_intention;
                }

                if (isset($business_area) && !empty($business_area)) {
                    $merchantinfo->business_area = $business_area;
                }

                if (isset($customer_groups) && !empty($customer_groups)) {
                    $merchantinfo->customer_groups = $customer_groups;
                }

                if ($merchantinfo->update()) {
                    $result['status'] = ERROR_NONE;
                    return json_encode($result);
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '商户修改失败';
                    return json_encode($result);
                }

            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '此商户不存在';
                return json_encode($result);
            }
        } else {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            return json_encode($result);
        }
    }



    //商户驳回
    /*
         * $merchantId 商户id
         * $remark 驳回原因
         * */
    public function rejectMerchant($merchantId, $remark)
    {
        $merchant = Merchant::model()->find('id = :id and flag = :flag', array(
            ':id' => $merchantId,
            ':flag' => FLAG_NO
        ));
        if ($merchant) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                if ($merchant->verify_status == MERCHANT_VERIFY_STATUS_UNINPUT
                    || $merchant->verify_status == MERCHANT_VERIFY_STATUS_INPUT_SUCCESS
                    || $merchant->verify_status == MERCHANT_VERIFY_STATUS_NUSIGN
                    || $merchant->verify_status == MERCHANT_VERIFY_STATUS_AUDITING
                ) {
                    $merchant->verify_status = MERCHANT_VERIFY_STATUS_REJECT;
                    if (isset($remark) && !empty($remark)) {
                        $merchant->remark = $remark;
                    }
                    if ($merchant->update()) {
                        $contract = Contract::model()->findAll('merchant_id =:merchant_id and flag=:flag', array(
                            'merchant_id' => $merchant->id,
                            ':flag' => FLAG_NO
                        ));
                        if (!empty($contract)) {
                            foreach ($contract as $k => $v) {
                                $v->status = CONTRACT_STATUS_REJECT;
                                if ($v->update()) {

                                } else {
                                    $transaction->rollBack();
                                    $result['status'] = ERROR_SAVE_FAIL;
                                    $result['errMsg'] = '合同驳回失败';
                                    return json_encode($result);
                                }
                            }
                        }
                        $transaction->commit();
                        $result['status'] = ERROR_NONE;
                        return json_encode($result);
                    } else {
                        $transaction->rollBack();
                        $result['status'] = ERROR_SAVE_FAIL;
                        $result['errMsg'] = '商户修改失败';
                        return json_encode($result);
                    }
                } else {
                    $result['status'] = ERROR_EXCEPTION;
                    $result['errMsg'] = '商户状态不正确';
                    return json_encode($result);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }

        } else {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '此商户不存在';
            return json_encode($result);
        }
    }


    //支付宝当面付设置

    //系统设置

    /**
     * $merchantId 商户id
     * $email 收款人支付宝账号
     * $pid  合作身份者id
     * $key  安全检验码
     * gateway 开发者网关
     * publickey 开发者公钥
     * appid  appid
     */
    public function SetAlipay($merchantId, $email = '', $pid = '', $key = '', $gateway = '', $publickey = '', $appid = '', $fuwu_name = '', $alipay_qrcode = '')
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $merchant = Merchant::model()->find('id=:id and flag=:flag', array(':id' => $merchantId, ':flag' => FLAG_NO));
            if (!empty($merchant)) {

                if (!empty($merchantId) && !empty($pid) && !empty($key) && !empty($email)) {
                    $merchant->seller_email = $email;
                    $merchant->partner = $pid;
                    $merchant->key = $key;
                    if ($merchant->update()) {
                        $result['status'] = ERROR_NONE;
                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        $result['errMsg'] = '修改失败';
                    }
                }

                if (!empty($merchantId) && !empty($appid)) {
//                            $merchant->developer_gateway    = $gateway;
//                            $merchant->developer_public_key = $publickey;
                    $merchant->appid = $appid;
                    $merchant->fuwu_name = $fuwu_name;
                    $merchant->alipay_qrcode = $alipay_qrcode;
                    if ($merchant->update()) {
                        $result['status'] = ERROR_NONE;
                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        $result['errMsg'] = '修改失败';
                    }
                }

            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    /**
     * 支付宝当面付设置
     * @param unknown $merchant_id
     * @param unknown $api_version
     * @param unknown $email
     * @param unknown $pid
     * @param unknown $key
     * @param unknown $appid
     * @throws Exception
     * @return string
     */
    public function updateAlipay($merchant_id, $api_version, $email, $pid, $key, $appid)
    {
        $result = array();

        try {
            $merchant = Merchant::model()->find('id = :id and flag = :flag',
                array(':id' => $merchant_id, ':flag' => FLAG_NO));
            if (empty($merchant)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无此数据');
            }
            if ($api_version == ALIPAY_API_VERSION_1) {
                $merchant['alipay_api_version'] = ALIPAY_API_VERSION_1;
                //$merchant['seller_email'] = $email;
                $merchant['partner'] = $pid;
                $merchant['key'] = $key;
                if (!$merchant->update()) {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据保存失败');
                }
                $result['status'] = ERROR_NONE;
            }
            if ($api_version == ALIPAY_API_VERSION_2) {
                $merchant['alipay_api_version'] = ALIPAY_API_VERSION_2;
                $merchant['appid'] = $appid;
                //$merchant['partner'] = $pid;
                if (!$merchant->update()) {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据保存失败');
                }
                $result['status'] = ERROR_NONE;
            }
            if ($api_version == ALIPAY_API_VERSION_2_AUTH_API) {
                $merchant['alipay_api_version'] = ALIPAY_API_VERSION_2_AUTH_API;
                if (!$merchant->update()) {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据保存失败');
                }
                $result['status'] = ERROR_NONE;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }

        return json_encode($result);
    }

    //返回系统设置显示保存过的数据
    /**
     * $merchantId 商户id
     */
    public function BackAlipay($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $merchant = Merchant::model()->find('id=:id and flag=:flag', array(
                ':id' => $merchantId,
                ':flag' => FLAG_NO
            ));
            $data = array();
            if (!empty($merchant)) {
                $data['seller_email'] = $merchant->seller_email;
                $data['pid'] = $merchant->partner;
                $data['key'] = $merchant->key;
                $data['appid'] = $merchant->appid;
                $data['encrypt_id'] = $merchant->encrypt_id;
                $data['fuwu_name'] = $merchant->fuwu_name;
                $data['alipay_qrcode'] = $merchant->alipay_qrcode;
                $data['api_version'] = $merchant['alipay_api_version'];
                $data['auth_token'] = $merchant['alipay_auth_token'];
                $data['auth_time'] = $merchant['alipay_auth_time'];
                $data['auth_appid'] = $merchant['alipay_auth_appid'];
                if (empty($merchant->appid) && empty($merchant->alipay_qrcode) && empty($merchant->fuwu_name)) {
                    $data['all_empty'] = true;
                } else {
                    $data['all_empty'] = false;
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

    //重新编辑商户
    /*$merchantId 商户id 必填
         * $name 商户名 必填
        * $type 商户类型 必填
        * $seller_email 支付宝账号 个体商户必填
        * $industry 所属行业 必填
        * $address 地址 必填
        * $zip_code 邮编
        * $fax 传真
        * $img 上传图片
        * $contact 联系人信息 必填
        * $register_money 注册资金
        * $income 预计年收入
        * $employees_num 员工人数
        * $is_qs 是否清算类商户
        * $signed_intention 签约用意
        * $business_area 营业场所面积
        * $customer_groups 客户群体
        *
        * */
    public function ReEditMerchant($merchantId, $name, $type, $seller_email, $industry, $address, $zip_code, $fax, $img, $contact, $register_money, $income, $employees_num, $is_qs, $signed_intention, $business_area, $customer_groups)
    {
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');

        //商户id
        if (!isset($merchantId) || empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            return json_encode($result);
        }
        //添加商户
        $merchant = Merchant::model()->findByPk($merchantId);
        if (isset($name) && !empty($name)) {
            $merchant->name = $name;
            $model = null;
            $model = Merchant::model()->find('name = :name and id !=:id and flag =:flag', array(
                ':name' => $name,
                ':id' => $merchantId,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'name';
                return json_encode($result);
            }
        }

        if (isset($seller_email) && !empty($seller_email)) {
            $merchant->seller_email = $seller_email;
            $model = null;
            $model = Merchant::model()->find('seller_email = :seller_email and id !=:id and flag =:flag', array(
                ':seller_email' => $seller_email,
                ':id' => $merchantId,
                ':flag' => FLAG_NO
            ));
            if ($model) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'seller_email';
                return json_encode($result);
            }
        }

        $merchant->last_time = new CDbExpression('now()');
        //添加商户信息
        $merchantInfo = Merchantinfo::model()->find('merchant_id =:merchant_id', array(
            ':merchant_id' => $merchant->id
        ));
        if (isset($industry) && !empty($industry)) {
            $merchantInfo->industry = $industry;
        }

        if (isset($address) && !empty($address)) {
            $merchantInfo->address = $address;
        }

        if (isset($contact) && !empty($contact)) {
            $merchantInfo->contact = $contact;
        }

        if (isset($type) && !empty($type)) {
            $merchantInfo->type = $type;
        }


        //非必填项
        if (isset($zip_code) && !empty($zip_code)) {
            $merchantInfo->zip_code = $zip_code;
        }
        if (isset($fax) && !empty($fax)) {
            $merchantInfo->fax = $fax;
        }
        if (isset($img) && !empty($img)) {
            $merchantInfo->img = $img;
        }
        if (isset($register_money) && !empty($register_money)) {
            $merchantInfo->register_money = $register_money;
        }

        if (isset($income) && !empty($income)) {
            $merchantInfo->income = $income;
        }

        if (isset($employees_num) && !empty($employees_num)) {
            $merchantInfo->employees_num = $employees_num;
        }
        if (isset($is_qs) && !empty($is_qs)) {
            $merchantInfo->is_qs = $is_qs;
        }
        if (isset($signed_intention) && !empty($signed_intention)) {
            $merchantInfo->signed_intention = $signed_intention;
        }
        if (isset($business_area) && !empty($business_area)) {
            $merchantInfo->business_area = $business_area;
        }
        if (isset($customer_groups) && !empty($customer_groups)) {
            $merchantInfo->customer_groups = $customer_groups;
        }
        $merchant->verify_status = MERCHANT_VERIFY_STATUS_UNINPUT;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if ($merchant->update()) {
                if ($merchantInfo->update()) {
                    $transaction->commit();
                    $result['status'] = ERROR_NONE;
                    $result['data'] = $merchant->id;
                    return json_encode($result);
                } else {
                    $transaction->rollBack();
                    //商户信息保存失败
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '商户信息保存失败';
                    return json_encode($result);
                }
            } else {
                $transaction->rollBack();
                //商户保存失败
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '商户保存失败';
                return json_encode($result);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }

    }


    //相册名管理
    /**
     * merchantId  商户id
     */
    public function PhotoManagement($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->params[':flag'] = FLAG_NO;
            $album = Album::model()->findall($criteria);
            if ($album) {
                $data = array();
                foreach ($album as $k => $v) {
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['name'] = $v['name'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    //修改相册名
    /**
     * id 相册id
     * name 相册名
     * merchantId  商户id
     */
    public function EditPhotoManagement($id, $name, $merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if (isset($id) && empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            $flag = 1;
        }
        if (isset($name) && empty($name)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数name缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $album = Album::model()->findByPk($id);
            $album->name = $name;
            $album->last_time = new CDbExpression('now()');
            if ($album->update()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '修改失败';
            }
        }
        return json_encode($result);
    }

    //创建分组
    /**
     * merchantId 商户id
     * id        相册管理id
     * name     分组名称
     */
    public function AddGroup($merchantId, $id, $name)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if (isset($id) && empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            $flag = 1;
        }
        if (isset($name) && empty($name)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数name缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $albumgroup = new AlbumGroup();
            $albumgroup->name = $name;
            $albumgroup->album_id = $id;
            $albumgroup->merchant_id = $merchantId;
            $albumgroup->create_time = new CDbExpression('now()');
            $albumgroup->flag = FLAG_NO;
            if ($albumgroup->save()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '保存失败';
            }
        }
        return json_encode($result);
    }

    //分组列表
    /**
     * merchantId  商户id
     * id      分组id
     */
    public function PhotoSubclass($merchantId, $id)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if (isset($id) && empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and album_id = :album_id and flag=:flag');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->params[':album_id'] = $id;
            $criteria->params[':flag'] = FLAG_NO;
            $albumgroup = AlbumGroup::model()->findall($criteria);
            if ($albumgroup) {
                $data = array();
                foreach ($albumgroup as $key => $value) {
                    $data[$key]['name'] = $value['name'];
                    $data[$key]['id'] = $value['id'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    //修改分组
    /**
     * merchantId   商户id
     * id     分组id
     * name    分组名称
     */
    public function EditGroup($merchantId, $id, $name)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if (isset($id) && empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            $flag = 1;
        }
        if (isset($name) && empty($name)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数name缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $albumgroup = AlbumGroup::model()->findByPk($id);
            $albumgroup->name = $name;
            $albumgroup->last_time = new CDbExpression('now()');
            if ($albumgroup->update()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '修改失败';
            }
        }
        return json_encode($result);
    }

    //删除分组
    /**
     * merchantId  商户id
     * id       分组id
     */
    public function DelGroup($merchantId, $id)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if (isset($id) && empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $albumgroup = AlbumGroup::model()->findByPk($id);
            if ($albumgroup->delete()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '删除失败';
            }
        }
        return json_encode($result);
    }

    //添加图片的下拉分组
    /**
     * merchantId  商户id
     * albumid    分组id
     */
    public function PhotoGroup($merchantId, $albumId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $albumgroup = AlbumGroup::model()->findall('merchant_id=:merchant_id and album_id=:album_id', array(':merchant_id' => $merchantId, ':album_id' => $albumId));
            if ($albumgroup) {
                $data = array();
                foreach ($albumgroup as $key => $value) {
                    $data[$key]['name'] = $value['name'];
                    $data[$key]['id'] = $value['id'];
                    $data[$key]['album_id'] = $value['album_id'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    //添加相册图片
    /**
     *
     */
    public function AddPhoto($albumgroupid, $img)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($albumgroupid) && empty($albumgroupid)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数albumgroupid缺失';
            $flag = 1;
        }
        if (isset($img) && empty($img)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数img缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $albumimg = new AlbumImg();
            $albumimg->album_group_id = $albumgroupid;
            $albumimg->img = $img;
            $albumimg->create_time = new CDbExpression('now()');
            if ($albumimg->save()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '保存失败';
            }
        }
        return json_encode($result);
    }

    //商户注册玩券管家
    /*$orderId 订单id
         * $account 账号
         * $pwd 密码
         * */
    public function wqRegister($orderId, $account, $pwd)
    {
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        //商户id
        if (!isset($orderId) || empty($orderId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数orderId缺失';
            return json_encode($result);
        }

        //账号
        if (!isset($account) || empty($account)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数account缺失';
            return json_encode($result);
        } else {
            $merchant = Merchant::model()->find('account=:account and flag = :flag', array(
                ':account' => $account,
                ':flag' => FLAG_NO
            ));
            if (!empty($merchant)) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'account';
                return json_encode($result);
            }
        }

        //密码
        if (!isset($pwd) || empty($pwd)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数pwd缺失';
            return json_encode($result);
        }

        //查询玩券管家订单
        $order = GjOrder::model()->find('id=:id and flag=:flag', array(
            ':id' => $orderId,
            ':flag' => FLAG_NO
        ));

        if (empty($order)) {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '该订单不存在';
            return json_encode($result);
        }

        //查询对应商户
        $merchant = Merchant::model()->find('id =:id and flag =:flag', array(
            ':id' => $order->merchant_id,
            ':flag' => FLAG_NO
        ));

        if (empty($merchant)) {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '该商户不存在';
            return json_encode($result);
        }

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $merchant->account = $account;
            $merchant->pwd = $pwd;
            $code = $this->getRandChar(5);
            $mer = Merchant::model()->find('merchant_number =:merchant_number and flag=:flag', array(
                ':merchant_number' => $code,
                ':flag' => FLAG_NO
            ));
            while ($mer) {
                $code = $this->getRandChar(5);
                $mer = Merchant::model()->find('merchant_number =:merchant_number and flag=:flag', array(
                    ':merchant_number' => $code,
                    ':flag' => FLAG_NO
                ));
            }
            $merchant->merchant_number = $code;
            $merchant->gj_start_time = new CDbExpression('now()');
            $merchant->gj_product_id = $order->wq_product_id;//玩券管家产品id
            $merchant->if_tryout = $order->if_tryout; //是否试用
            //商户号（玩券接口对外商户号）
            if (empty($merchant->mchid)) {
                $merchant->mchid = $this->createMchid();
            }
            if ($order->if_tryout == IF_TRYOUT_YES) {
                //试用版送短信200条
                $merchant->msg_num += 200;
            } else {
                //正式版送短信500条
                $merchant->msg_num += 500;
            }
            $merchant->tryout_status = TRYOUT_STATUS_NORMAL;//管家试用状态
            $merchant->gj_open_status = GJ_OPEN_STATUS_OPEN;//管家开通状态

            //设置默认相册
            $album = Album::model()->findAll('merchant_id =:merchant_id and flag =:flag', array(
                ':merchant_id' => $merchant->id,
                ':flag' => FLAG_NO
            ));
            if (empty($album)) {
                $album1 = new Album();
                $album1->merchant_id = $merchant->id;
                $album1->name = '菜品';
                $album1->create_time = new CDbExpression('now()');
                $album1->save();

                $album2 = new Album();
                $album2->merchant_id = $merchant->id;
                $album2->name = '环境';
                $album2->create_time = new CDbExpression('now()');
                $album2->save();

                $album3 = new Album();
                $album3->merchant_id = $merchant->id;
                $album3->name = '其他';
                $album3->create_time = new CDbExpression('now()');
                $album3->save();
            }

            if ($merchant->update()) {
                $order->order_status = GJORDER_STATUS_USED;
                $order->code_use_time = new CDbExpression('now()');
                $order->code_merchant_id = $merchant->id;
                if ($order->update()) {
                    $transaction->commit();
                    $result['data'] = array('merchant_id' => $merchant->id);
                    $result['status'] = ERROR_NONE;
                    return json_encode($result);
                } else {
                    $transaction->rollBack();
                    //商户保存失败
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '订单更新失败';
                    return json_encode($result);
                }
            } else {
                $transaction->rollBack();
                //商户保存失败
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '商户更新失败';
                return json_encode($result);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    //获取定长的随机字符串
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

    //生成玩券对外接口商户号
    private function createMchid()
    {
        $mchid = date('ym') . $this->getRandChar(6);
        $merchant = Merchant::model()->find('mchid = :mchid and flag = :flag', array(
            ':mchid' => $mchid,
            ':flag' => FLAG_NO
        ));
        while (!empty($merchant)) {
            $mchid = date('yd') . $this->getRandChar(6);
            $merchant = Merchant::model()->find('mchid = :mchid and flag = :flag', array(
                ':mchid' => $mchid,
                ':flag' => FLAG_NO
            ));
        }
        return $mchid;
    }

    //修改密码
    /*$merchantId 合作商id 必填
	 * $oldPwd 旧密码 必填
	 * $pwd 新密码 必填
	 * */
    public function editMerchantPwd($merchantId, $oldPwd, $pwd)
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        //验证合作商名
        if (!isset($merchantId) || empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = '参数merchantId缺失';
            $flag = 1;
        }
        //验证账号
        if (!isset($oldPwd) || empty($oldPwd)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数oldPwd缺失';
            $flag = 1;
        }
        //验证密码
        if (!isset($pwd) || empty($pwd)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = $errMsg . '参数pwd缺失';
            $flag = 1;
        }
        if ($flag == 1) {
            $result['errMsg'] = $errMsg;
            return json_encode($result);
        }
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params[':id'] = $merchantId;
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->addCondition('pwd = :pwd');
        $criteria->params[':pwd'] = $oldPwd;
        $merchant = Merchant::model()->find($criteria);
        if ($merchant) {
            $merchant->pwd = $pwd;
            if ($merchant->update()) {
                $result['status'] = ERROR_NONE;
                return json_encode($result);
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '密码修改失败';
                return json_encode($result);
            }
        } else {
            //没有找到合作商
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '旧密码不正确';
            return json_encode($result);
        }

    }



    //获取商户微信公众号信息
    /*
	 * $merchantId 商户id
	 * */
    public function getWechat($merchantId)
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $merchant = Merchant::model()->find('id=:id and flag =:flag', array(
                ':id' => $merchantId,
                ':flag' => FLAG_NO
            ));
            $data = array();
            if ($merchant) {
                $data['id'] = $merchant->id;
                $data['wechat_id'] = $merchant->wechat_id;
                $data['encrypt_id'] = $merchant->encrypt_id;
                $data['wechat'] = $merchant->wechat;
                $data['wechat_type'] = $merchant->wechat_type;
                $data['wechat_qrcode'] = $merchant->wechat_qrcode;
                $data['wechat_appid'] = $merchant->wechat_appid;
                $data['wechat_appsecret'] = $merchant->wechat_appsecret;

                $data['wechat_subscription_appid'] = $merchant->wechat_subscription_appid;
                $data['wechat_subscription_appsecret'] = $merchant->wechat_subscription_appsecret;

                $data['wechat_account'] = $merchant->wechat_account;
                $data['wechat_interface_url'] = $merchant->wechat_interface_url;
                $data['wechat_token'] = $merchant->wechat_token;
                $data['wechat_encodingaeskey'] = $merchant->wechat_encodingaeskey;
                $data['wechat_encrypt_type'] = $merchant->wechat_encrypt_type;
                $data['wechat_name'] = $merchant->wechat_name;
            } else {
                throw new Exception("该商户不存在");
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //保存商户微信公众号信息
    public function saveWechat($merchantId, $wechat_id, $wechat, $wechat_type, $wechat_appid, $wechat_appsecret, $wechat_name, $wechat_qrcode)
    {
        //返回结果
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        //验证商户id
        if (!isset($merchantId) || empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $errMsg = '参数merchantId缺失';
            $result['errMsg'] = $errMsg;
            return json_encode($result);
        }
        $merchant = Merchant::model()->find('id=:id and flag =:flag', array(
            ':id' => $merchantId,
            ':flag' => FLAG_NO
        ));

        if ($merchant) {
            $merchant->wechat_id = $wechat_id;
            $merchant->wechat = $wechat;
            $merchant->wechat_type = $wechat_type;
            $merchant->wechat_subscription_appid = $wechat_appid;
            $merchant->wechat_name = $wechat_name;
            $merchant->wechat_subscription_appsecret = $wechat_appsecret;
            $merchant->wechat_qrcode = $wechat_qrcode;
            if ($merchant->update()) {
                $result['status'] = ERROR_NONE;
                return json_encode($result);
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '更新失败';
                return json_encode($result);
            }
        } else {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '该商户不存在';
            return json_encode($result);
        }
    }


    /**
     * 验证商户手机号是否存在
     * $mobile  商户手机号
     */
    public function isMobile($mobile)
    {
        $model = Merchant::model()->find('account=:account and flag=:flag', array(
            ':account' => $mobile,
            ':flag' => FLAG_NO
        ));
        if (count($model) > 0) { //存在
            return true;
        } else { //不存在
            return false;
        }
    }

    /**
     * 找回密码
     * $mobile  商户手机号
     * $newPwd  商户新密码
     */
    public function retrieve($mobile, $newPwd)
    {
        $result = array();
        $model = Merchant::model()->find('account=:account', array(':account' => $mobile));
        if (!empty($model)) {
            $model->pwd = md5($newPwd);
            $model->last_time = date('Y-m-d H:i:s');
            if ($model->save()) {
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '密码修改失败';
            }
        } else {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '商户不存在';
        }
        return json_encode($result);
    }

    /**
     * 修改商户的玩券管家版本
     */
    public function editGjProduct($gj_order_id, $merchant_id, $gj_product_id, $time_limit)
    {
        $gj_order = GjOrder::model()->findByPk($gj_order_id);
        $model = Merchant::model()->findByPk($merchant_id);
        $model->gj_product_id = $gj_product_id;
        $model->gj_open_status = GJ_OPEN_STATUS_OPEN;
        //if($gj_order -> order_type == GJ_ORDER_TYPE_XZ){
        $model->gj_start_time = date('Y-m-d H:i:s');
        $model->gj_end_time = $this->getVildTime($time_limit);
        //}
        $model->last_time = date('Y-m-d H:i:s');

        $model->update();
    }

    /**
     * 计算有效期
     * $time_limit  天数
     */
    public function getVildTime($time_limit)
    {
        $vildTime = date('Y-m-d H:i:s', strtotime("+$time_limit day"));
        return $vildTime;
    }

    /**
     * 查询微信支付信息
     * $merchantId 商户id
     */
    public function getWechatPayInfo($merchantId)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (!isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $merchant = Merchant::model()->find('id=:id and flag=:flag', array(':id' => $merchantId, ':flag' => FLAG_NO));
            $data = array();
            if (!empty($merchant)) {
                $data['wechat_appid'] = $merchant->wechat_appid;
                $data['wechat_mchid'] = $merchant->wechat_mchid;
                $data['wechat_key'] = $merchant->wechat_key;
                $data['wechat_appsecret'] = $merchant->wechat_appsecret;
                $data['wechat_type'] = $merchant['wxpay_merchant_type'];
                $data['wechat_apiclient_cert'] = $merchant->wechat_apiclient_cert;
                $data['wechat_apiclient_key'] = $merchant->wechat_apiclient_key;
                $data['t_wx_mchid'] = $merchant['t_wx_mchid'];
                $data['t_wx_appid'] = $merchant['t_wx_appid'];
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
            return json_encode($result);
        }
    }

    /**
     * 保存微信支付信息修改的数据
     * $merchantId 商户ID
     * $wechat_appid pid
     * $wechat_key 商户KEY
     * $wechat_appsecret
     * $wechat_mchid
     */
    public function updateWechatPay($merchantId, $merchant_type, $wechat_appid, $wechat_key, $wechat_appsecret, $wechat_mchid, $t_mchid, $t_appid)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $merchant = Merchant::model()->find('id=:id and flag=:flag', array(':id' => $merchantId, ':flag' => FLAG_NO));
            if (!empty($merchant)) {
                $merchant->wechat_appid = $wechat_appid;
                $merchant->wechat_key = $wechat_key;
                $merchant->wechat_appsecret = $wechat_appsecret;
                $merchant->wechat_mchid = $wechat_mchid;
                $merchant->t_wx_mchid = $t_mchid;
                $merchant->t_wx_appid = $t_appid;
                $merchant['wxpay_merchant_type'] = $merchant_type;
                if ($merchant->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '修改失败';
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    public function updateWechatCert($merchantId, $filePath, $type)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $flag = 0;

            if (isset($merchantId) && empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if ($flag == 0) {

                $merchant = Merchant::model()->find('id=:id and flag=:flag', array(':id' => $merchantId, ':flag' => FLAG_NO));
                if (!empty($merchant)) {
                    if (!empty($merchantId) && !empty($filePath)) {
                        if ($type == 'apiclient_cert.pem')
                            $merchant->wechat_apiclient_cert = $filePath;
                        else if ($type == 'apiclient_key.pem')
                            $merchant->wechat_apiclient_key = $filePath;
                        if ($merchant->update()) {
                            $result['status'] = ERROR_NONE;
                        } else {
                            $result['status'] = ERROR_SAVE_FAIL;
                            $result['errMsg'] = '修改失败';
                        }
                    }
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //计算所有订单佣金
    public function countCommissionAll()
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {

            $merchant = Merchant::model()->findAll('gj_open_status !=:gj_open_status', array(
                ':gj_open_status' => GJ_OPEN_STATUS_NULL
            ));
            $data = array();
            foreach ($merchant as $k => $v) {
                $store = Store::model()->findAll('merchant_id =:merchant_id', array(
                    ':merchant_id' => $v->id
                ));
                $countMoney = 0;
                $refundMoney = 0;
                $data[$v->id] = array();
                foreach ($store as $t => $m) {
                    $criteria = new CDbCriteria;

                    $criteria->addCondition("pay_status = :pay_status");
                    $criteria->params[':pay_status'] = ORDER_STATUS_PAID;

                    $criteria->addCondition("store_id = :store_id");
                    $criteria->params[':store_id'] = $m->id;

                    $criteria->addCondition("flag = :flag");
                    $criteria->params[':flag'] = FLAG_NO;

                    $criteria->addCondition("order_type = :order_type");
                    $criteria->params[':order_type'] = ORDER_TYPE_CASHIER;

                    $criteria->addCondition("pay_channel = :pay_channel1 or pay_channel = :pay_channel2");
                    $criteria->params[':pay_channel1'] = ORDER_PAY_CHANNEL_ALIPAY_SM;
                    $criteria->params[':pay_channel2'] = ORDER_PAY_CHANNEL_ALIPAY_TM;
                    //找到所有的已付款订单
                    $order = Order::model()->findAll($criteria);
                    $data[$v->id]['num'] = count($order);
                    foreach ($order as $x => $y) {
                        if (strtotime($y->pay_time) >= strtotime('2015-9-1 00:00:00') && strtotime($y->pay_time) <= strtotime('2015-9-30 23:59:59')) {
                            $countMoney += $y->online_paymoney;

                            $criteria_refund = new CDbCriteria;
                            $criteria_refund->addCondition("status = :status");
                            $criteria_refund->params[':status'] = REFUND_STATUS_SUCCESS;

                            $criteria_refund->addCondition("order_id = :order_id");
                            $criteria_refund->params[':order_id'] = $y->id;

                            $criteria_refund->addCondition("refund_channel = :refund_channel1 or refund_channel = :refund_channel2");
                            $criteria_refund->params[':refund_channel1'] = ORDER_PAY_CHANNEL_ALIPAY_SM;
                            $criteria_refund->params[':refund_channel2'] = ORDER_PAY_CHANNEL_ALIPAY_TM;


                            $refund = RefundRecord::model()->findAll($criteria_refund);
                            foreach ($refund as $a => $b) {
                                if (strtotime($b->refund_time) >= strtotime('2015-9-1 00:00:00') && strtotime($b->refund_time) <= strtotime('2015-9-30 23:59:59')) {
                                    $refundMoney += $b->refund_money;
                                }
                            }
                        }
                    }
                }
                $comm = new Commission();
                $comm->merchant_id = $v->id;
                $comm->agent_id = $v->agent_id;
                $comm->merchant_name = $v->name;
                $comm->amount = $countMoney - $refundMoney;
                $comm->commission = ($countMoney - $refundMoney) * 0.006;
                $comm->create_time = new CDbExpression('now()');;
                $comm->date = '2015-9-1';
                if ($comm->save()) {

                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('保存失败');
                }

            }

            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
            $transaction->commit(); //数据提交
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取银商分配给商户的3des密钥
     * @param unknown $merchant_id
     * @return Ambigous <string, static, unknown, NULL>
     */
    public function getUms3DesKey($merchant_id)
    {
        $result = array();
        $merchant = Merchant::model()->findByPk($merchant_id);
        if (empty($merchant)) {
            $result['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '商户不存在';
        } else {
            $result['status'] = ERROR_NONE;
            $result['key'] = $merchant['ums_3des_key'];
        }
        return json_encode($result);
    }

    /**
     * 商户可用性检查
     * @param unknown $account
     * @throws Exception
     * @return string
     */
    public function checkMerchant($account)
    {
        $result = array();

        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('account = :account');
            $criteria->params[':account'] = $account;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Merchant::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账户不存在');
            }
            //判断状态
            if ($model->status == MERCHANT_STATUS_LOCK) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账户被锁定');
            }
            //判断是否试用 且判断试用状态
            if ($model->if_tryout == IF_TRYOUT_YES && $model->tryout_status == TRYOUT_STATUS_LOCK) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账户试用锁定');
            }

            if ($model->gj_open_status == GJ_OPEN_STATUS_OPEN) {
                //判断是否过期
                if (strtotime($model->gj_end_time) < time()) {
                    $model->gj_open_status = GJ_OPEN_STATUS_OVERTIME;
                    if ($model->save()) {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('账户已过期');
                    }
                }
            } elseif ($model->gj_open_status == GJ_OPEN_STATUS_NULL) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账户未开通');
            } elseif ($model->gj_open_status == GJ_OPEN_STATUS_OVERTIME) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账户已过期');
            }
            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $result['errMsg'] = $msg;
        }

        return json_encode($result);
    }

    /**
     * 获取商户mchid
     * @param unknown $merchant_id
     * @throws Exception
     * @return string
     */
    public function getMchid($merchant_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO

            $model = Merchant::model()->findByPk($merchant_id);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('商户不存在');
            }
            $data = array('mchid' => $model['mchid']);

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
     * 获取api密钥
     * @param unknown $account
     * @throws Exception
     * @return string
     */
    public function getApiKey($mchid, $account = NULL)
    {
        $result = array();

        try {
            $criteria = new CDbCriteria();
            if (!empty($mchid)) {
                $criteria->addCondition('mchid = :mchid');
                $criteria->params[':mchid'] = $mchid;
            }
            if (!empty($account)) {
                $criteria->addCondition('account = :account');
                $criteria->params[':account'] = $account;
            }
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Merchant::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('账户不存在');
            }
            $mchid = $model['mchid'];
            $key = $model['api_key'];
            if (empty($key)) {
                //生成新密钥
                $key = $this->getNonceStr();
                $model['api_key'] = $key;
                if (!$model->save()) {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('系统错误');
                }
            }

            $result['status'] = ERROR_NONE;
            $result['mchid'] = $mchid;
            $result['key'] = $key;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $result['errMsg'] = $msg;
        }

        return json_encode($result);
    }

    /**
     * 支付宝应用授权
     * @param unknown $wq_mchid
     * @param unknown $auth_code
     * @throws Exception
     * @return string
     */
    public function alipayAppAuth($wq_mchid, $auth_code)
    {
        $result = array();

        try {
            if (empty($wq_mchid)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数wq_mchid不能为空');
            }
            if (empty($auth_code)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数auth_code不能为空');
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('wq_mchid = :wq_mchid');
            $criteria->params[':wq_mchid'] = $wq_mchid;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Merchant::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('商户不存在');
            }
            $api = new AlipayApi();
            $response = $api->appAuthByCodeApi($auth_code);
            if (!$response) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('请求接口失败');
            }
            //返回请求结果
            $result_code = $api->getVal($response, 'result_code'); //结果码
            $user_id = $api->getVal($response, 'user_id'); //授权商户的pid
            $auth_app_id = $api->getVal($response, 'auth_app_id'); //授权商户的appid
            $app_auth_token = $api->getVal($response, 'app_auth_token'); //授权令牌
            $app_refresh_token = $api->getVal($response, 'app_refresh_token'); //刷新令牌时使用
            $expires_in = $api->getVal($response, 'expires_in'); //令牌有效期
            $re_expires_in = $api->getVal($response, 're_expires_in'); //刷新令牌有效期
            $detail_error_des = $api->getVal($response, 'detail_error_des'); //错误描述
            $error = $api->getVal($response, 'error'); //其他错误

            //请求成功
            if ($result_code == ALIPAY_V2_CODE_SUCCESS) {
                //刷新授权商户token
                $model['alipay_auth_pid'] = $user_id;
                $model['alipay_auth_appid'] = $auth_app_id;
                $model['alipay_auth_token'] = $app_auth_token;
                $model['alipay_auth_refresh_token'] = $app_refresh_token;
                $model['alipay_auth_time'] = date('Y-m-d H:i:s');
                $model['alipay_auth_token_expires_in'] = date('Y-m-d H:i:s', time() + $expires_in);
                $model['alipay_auth_refresh_token_expires_in'] = date('Y-m-d H:i:s', time() + $re_expires_in);

                if (!$model->save()) {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据保存失败');
                }
            }
            //请求失败
            if ($result_code == ALIPAY_V2_CODE_FAIL || $result_code == ALIPAY_V2_CODE_UNKNOWN) {
                $result['status'] = ERROR_REQUEST_FAIL;
                throw new Exception($detail_error_des);
            }
            //其他接口错误
            if (!empty($error)) {
                $result['status'] = ERROR_REQUEST_FAIL;
                throw new Exception($error);
            }

            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $result['errMsg'] = $msg;
        }

        return $result;
    }


    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    private function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取属性
     */
    public function getMerchantAttributes()
    {
        return Merchantinfo::model()->attributes;
    }

    /**
     * 获取微信商户
     */
    public function getWechatMerchant($id)
    {
        $model = Merchantinfo::model()->findByPk($id);

        return $model;
    }

    /**
     * 添加微信商户-step_one
     */
    public function addWechatMerchantOne($merchant_id, $id, $wx_contact, $wx_tel, $wx_email, $wx_abbreviation, $wx_business_category, $wx_customer_service, $wx_company_website, $wx_qualifications, $wx_supply, $wx_jsydgh_license, $wx_jsgcgh_license, $wx_jzgckg_license, $wx_gytd_license, $wx_spfys_license, $wx_wnjy_license, $wx_wwpm_license, $wx_frdj_license, $wx_organization_code_img, $wx_product_description)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();//开启事务
        try {
            if (empty($id)) {
                $model = new Merchantinfo();

//			    	$model_merchant = new Merchant();
//		    		if ($model_merchant->save()){
//    					$model['merchant_id'] = $model_merchant['id'];
//		    		}else {
//		    			$result['status'] = ERROR_SAVE_FAIL; //状态码
//		    			$result['errMsg'] = '数据保存失败'; //错误信息
//		    			$result['data'] = '';
//		    			throw new Exception('数据保存失败');
//		    		}
            } else {
                $model = Merchantinfo::model()->findByPk($id);
            }
            $model['merchant_id'] = $merchant_id;
            $model['wx_contact'] = $wx_contact;
            $model['wx_tel'] = $wx_tel;
            $model['wx_email'] = $wx_email;
            $model['wx_abbreviation'] = $wx_abbreviation;
            $model['wx_business_category'] = $wx_business_category;
            $model['wx_customer_service'] = $wx_customer_service;
            $model['wx_company_website'] = $wx_company_website;
            $model['wx_qualifications'] = $wx_qualifications;
            $model['wx_supply'] = $wx_supply;
            $model['wx_jsydgh_license'] = $wx_jsydgh_license;
            $model['wx_jsgcgh_license'] = $wx_jsgcgh_license;
            $model['wx_jzgckg_license'] = $wx_jzgckg_license;
            $model['wx_gytd_license'] = $wx_gytd_license;
            $model['wx_spfys_license'] = $wx_spfys_license;
            $model['wx_wnjy_license'] = $wx_wnjy_license;
            $model['wx_wwpm_license'] = $wx_wwpm_license;
            $model['wx_frdj_license'] = $wx_frdj_license;
            $model['wx_organization_code_img'] = $wx_organization_code_img;
            $model['wx_product_description'] = $wx_product_description;
//
//				$merchant_id = $model['merchant_id'];
//				$merchant_model = Merchant::model()->findByPk($merchant_id);
//				$merchant_model['wechat_verify_status'] = WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT;
//				$merchant_model['agent_id'] = Yii::app() -> session['agent_id'];
//				$merchant_model['create_time'] = date('Y-m-d H:i:s');

            if ($model->save()) {
                $transaction->commit(); //数据提交
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
                throw new Exception('数据保存失败');
            }
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 添加微信商户 step_two
     */
    public function addWechatMerchantTwo($id, $wx_merchant_name, $wx_registered_address, $wx_business_license_no, $wx_operating_range, $wx_organization_code, $wx_credentials_user_type, $wx_credentials_user_name, $wx_credentials_type, $wx_credentials_no, $wx_business_deadline_longterm, $wx_organization_code_longterm, $wx_credentials_longterm, $wx_business_deadline_start, $wx_business_deadline_end, $wx_organization_code_start, $wx_organization_code_end, $wx_credentials_start, $wx_credentials_end, $wx_business_license_img, $wx_organization_code_img, $wx_credentials_positive, $wx_credentials_opposite)
    {
        $result = array();
        try {

            $model = Merchantinfo::model()->findByPk($id);
            $mer = Merchant::model()->findByPk($model->merchant_id);
            if (!empty($model)) {
                $model['wx_merchant_name'] = $wx_merchant_name;
                $mer->wx_name = $wx_merchant_name;
                $model['wx_registered_address'] = $wx_registered_address;
                $model['wx_business_license_no'] = $wx_business_license_no;
                $model['wx_operating_range'] = $wx_operating_range;
                $model['wx_organization_code'] = $wx_organization_code;
                $model['wx_credentials_user_type'] = $wx_credentials_user_type;
                $model['wx_credentials_user_name'] = $wx_credentials_user_name;
                $model['wx_credentials_type'] = $wx_credentials_type;
                $model['wx_credentials_no'] = $wx_credentials_no;
                $model['wx_business_deadline_longterm'] = $wx_business_deadline_longterm;
                $model['wx_organization_code_longterm'] = $wx_organization_code_longterm;
                $model['wx_credentials_longterm'] = $wx_credentials_longterm;
                if (!empty($wx_business_deadline_start)) {
                    $model['wx_business_deadline_start'] = $wx_business_deadline_start;
                }
                if (!empty($wx_business_deadline_end)) {
                    $model['wx_business_deadline_end'] = $wx_business_deadline_end;
                }
                if (!empty($wx_organization_code_start)) {
                    $model['wx_organization_code_start'] = $wx_organization_code_start;
                }
                if (!empty($wx_organization_code_end)) {
                    $model['wx_organization_code_end'] = $wx_organization_code_end;
                }
                if (!empty($wx_credentials_start)) {
                    $model['wx_credentials_start'] = $wx_credentials_start;
                }
                if (!empty($wx_credentials_end)) {
                    $model['wx_credentials_end'] = $wx_credentials_end;
                }

                //经营类目
                $category = explode(",", $model['wx_business_category']);
                $category_one = $category["0"];
                if ($category_one == "2") {
                    $model['wx_credentials_user_type'] = CERTIFICATE_HOLDER_TYPE_LEGAL;
                }

                $model['wx_business_license_img'] = $wx_business_license_img;
                $model['wx_organization_code_img'] = $wx_organization_code_img;
                $model['wx_credentials_positive'] = $wx_credentials_positive;
                $model['wx_credentials_opposite'] = $wx_credentials_opposite;

                if ($model->update()) {
                    if ($mer->update()) {
                        $result['status'] = ERROR_NONE; //状态码
                        $result['errMsg'] = ''; //错误信息
                        $result['data'] = array('id' => $model->id);
                    }
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                    $result['data'] = '';
                }

            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }


    /**
     * 添加微信商户-step_three
     */
    public function addWechatMerchantThree($id, $wx_account_type, $wx_account_name, $wx_bank_name, $wx_bank_area, $wx_bank_subbranch, $wx_bank_account)
    {
        $result = array();
        try {
            $model = Merchantinfo::model()->findByPk($id);

            if (!empty($model)) {
                $model['wx_account_type'] = $wx_account_type;
                $model['wx_account_name'] = $wx_account_name;
                $model['wx_bank_name'] = $wx_bank_name;
                $model['wx_bank_area'] = $wx_bank_area;
                $model['wx_bank_subbranch'] = $wx_bank_subbranch;
                $model['wx_bank_account'] = $wx_bank_account;

                //经营类目
                $category = explode(",", $model['wx_business_category']);
                $category_one = $category["0"];
                if ($category_one != "2") {
                    $model['wx_account_type'] = BANK_ACCOUNT_TYPE_DUIGONG;
                }
                if ($category_one == "1") {
                    $model['wx_account_name'] = $model['wx_merchant_name'];
                }

                if ($model->update()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $result['data'] = array('id' => $model->id);
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

        return $result;
    }

    /**
     * 添加微信商户 4
     */
    public function addWechatMerchantFour($merchant_id, $verify_status)
    {
        $result = array();
        try {
            $model = Merchant::model()->findByPk($merchant_id);
            $model['wechat_verify_status_submit_time'] = date('Y-m-d H:i:s');
            $model['wechat_verify_status'] = $verify_status;
            //$model['create_time'] = date('Y-m-d H:i:s');
            if ($model->update()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 获取微信商户列表
     */
    public function getWechatMerchantList($agent_id = '', $name, $wechat_merchant_no, $wechat_verify_status, $gj_open_status)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            if (isset($name) && !empty($name)) {
                $criteria->compare('wx_merchant_name', $name, true);
            }

            if (!empty($wechat_merchant_no)) {
                $criteria->compare('merchant.wechat_merchant_no', $wechat_merchant_no, true);
            }

            //商户审核状态搜索
            if (isset($wechat_verify_status) && !empty($wechat_verify_status)) {
                if ($wechat_verify_status == WECHATMERCHANT_VERIFY_STATUS_UNCHECK) {
                    $criteria->addCondition('merchant.wechat_verify_status = :wechat_verify_status1 or merchant.wechat_verify_status = :wechat_verify_status2');
                    $criteria->params[':wechat_verify_status1'] = WECHATMERCHANT_VERIFY_STATUS_UNCHECK;
                    $criteria->params[':wechat_verify_status2'] = WECHATMERCHANT_VERIFY_STATUS_IN;
                } else {
                    $criteria->addCondition('merchant.wechat_verify_status = :wechat_verify_status');
                    $criteria->params[':wechat_verify_status'] = $wechat_verify_status;
                }
            }
            //玩券管家开通状态搜索
            if (isset($gj_open_status) && !empty($gj_open_status)) {
                $criteria->addCondition('merchant.gj_open_status = :gj_open_status');
                $criteria->params[':gj_open_status'] = $gj_open_status;
            }

            //合作商id搜索
            $agent_arr = array();
            $agent_all_arr = array();
            if (isset($agent_id) && !empty($agent_id)) {
                $agent_model = Agent::model()->findAll('pid=:pid and flag =:flag', array(
                    ':pid' => $agent_id,
                    ':flag' => FLAG_NO
                ));
                if (!empty($agent_model)) {
                    foreach ($agent_model as $k => $v) {
                        $agent_arr[$k] = $v->id;
                    }
                }
                //合并数组
                $agent_all_arr = array_merge($agent_all_arr, $agent_arr, array($agent_id));
                if (!empty($agent_all_arr)) {
                    $criteria->addInCondition('merchant.agent_id', $agent_all_arr);
                }
            }

            //按创建时间排序
            $criteria->order = 'merchant.create_time DESC';
            $criteria->addCondition('merchant.wechat_verify_status is not null');

            $criteria->addCondition('merchant.flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $criteria->addCondition('wx_contact!=:wx_contact');
            $criteria->params[':wx_contact'] = '';


            $pages = new CPagination(Merchantinfo::model()->with('merchant')->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $merchantinfo = Merchantinfo::model()->with('merchant')->findAll($criteria);

            $list = array();
            if (!empty($merchantinfo)) {
                foreach ($merchantinfo as $key => $value) {
                    //获取商户信息
                    $merchant = Merchant::model()->findByPk($value->merchant_id);
                    if (!empty($merchant)) {
                        $id = $value['id'];
                        $list[$id]['account'] = '--';
                        $list[$id]['id'] = $merchant['id'];
                        $list[$id]['wechat_merchant_no'] = $merchant['wechat_merchant_no'];
                        $list[$id]['name'] = $value['wx_merchant_name'];
                        $list[$id]['gj_open_status'] = $merchant->gj_open_status; //玩券管家开通状态
                        $list[$id]['gj_product_name'] = isset($merchant->gjproduct->name) ? $merchant->gjproduct->name : ''; //管家版本名称
                        $list[$id]['remaing_day'] = $this->getRemainDay($merchant->id, $merchant->gj_end_time); //玩券管家到期剩余天数
                        $list[$id]['agent_name'] = isset($merchant->agent->name) ? $merchant->agent->name : '';
                        $list[$id]['gj_end_time'] = $merchant->gj_end_time;
                        $list[$id]['if_tryout'] = $merchant->if_tryout; //是否试用版 1:非试用  2：试用
                        $list[$id]['verify_status'] = $merchant->wechat_verify_status;
                    }
                }


                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
                $result['data'] = $list;
            } else {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
                $result['data'] = $list;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }




    /*---------------------------------------------------------以下是新规则代码格式-----------------------------------------------------------------------------------*/

    /**
     * 获取商户列表
     * @author xyf
     * 2016/01/14
     * $agent_id   服务商id
     * $merchant_name   商户名称
     * $parent_agent_id  所属代理id
     * $gj_product_id    管家版本id
     * $wechat_verify_status  微信支付签约状态
     * $alipay_authorized     支付宝授权状态
     */
    public function getMerchantListW($agent_id, $merchant_name, $parent_agent_id, $gj_product_id, $wechat_verify_status, $alipay_authorized)
    {
        $result = array();
        $data = array();
        try {
            $agent_id_arr = $this->getLowerIdW($agent_id); //获取该$agent_id服务商以及他所有下级服务商的id数组

            $criteria = new CDbCriteria();
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            //商户名称搜索
            if (!empty($merchant_name)) {
                $criteria->addSearchCondition('wq_m_name', $merchant_name);
                //$criteria -> addCondition('wq_m_name=:wq_m_name');
                //$criteria -> params[':wq_m_name'] = $merchant_name;
            }
            //所属代理搜索
            if (!empty($parent_agent_id)) {
                $criteria->addCondition('agent_id=:agent_id');
                $criteria->params[':agent_id'] = $parent_agent_id;
            }
            //玩券版本搜索
            if (!empty($gj_product_id)) {
                $criteria->addCondition('gj_product_id=:gj_product_id');
                $criteria->params[':gj_product_id'] = $gj_product_id;
            }
            //微信支付签约状态搜索
            if (!empty($wechat_verify_status)) {
                $criteria->addCondition('wechat_verify_status=:wechat_verify_status');
                $criteria->params[':wechat_verify_status'] = $wechat_verify_status;
            }
            //支付宝授权状态搜索
            if (!empty($alipay_authorized)) {
                if ($alipay_authorized == 'unauthorized') { //如果是未授权
                    $criteria->addCondition('alipay_auth_token is :alipay_auth_token');
                    $criteria->params[':alipay_auth_token'] = NULL;
                } elseif ($alipay_authorized == 'authorized') { //如果是已授权
                    $criteria->addCondition('alipay_auth_token is not :alipay_auth_token');
                    $criteria->params[':alipay_auth_token'] = NULL;
                }
            }

            $criteria->addInCondition('agent_id', $agent_id_arr);
            $criteria->order = 'create_time DESC';

            //分页
            $pages = new CPagination(Merchant::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $merchant = Merchant::model()->findAll($criteria);
            if (!empty($merchant)) {
                foreach ($merchant as $k => $v) {
                    $data['list'][$k]['id'] = $v['id'];
                    $data['list'][$k]['wq_mchid'] = $v['wq_mchid']; //玩券商户号 1+区号+随机5至6位随机数
                    $data['list'][$k]['wq_m_name'] = $v['wq_m_name']; //玩券商户名
                    $data['list'][$k]['wechat_merchant_no'] = $v['wechat_merchant_no']; //微信商户号
                    $data['list'][$k]['agent_name'] = empty($v->agent->name) ? '' : $v->agent->name; //所属代理名称
                    $data['list'][$k]['wq_m_verify_status'] = $v['wq_m_verify_status']; //玩券商户审核状态
                    $data['list'][$k]['gj_product_name'] = empty($v->gjproduct->name) ? '' : $v->gjproduct->name; //玩券版本
                    $data['list'][$k]['gj_product_id'] = $v['gj_product_id']; //玩券版本id
                    $data['list'][$k]['account'] = $v['account']; //账号
                    $data['list'][$k]['gj_end_time'] = $v['gj_end_time']; //玩券管家到期时间
                    $data['list'][$k]['wechat_verify_status'] = $v['wechat_verify_status']; //微信商户审核状态
                    $data['list'][$k]['alipay_auth_token'] = $v['alipay_auth_token']; //支付宝token
                    $data['list'][$k]['alipay_auth_time'] = $v['alipay_auth_time']; //最近授权时间
                    $data['list'][$k]['create_time'] = $v['create_time'];
                    $data['list'][$k]['all_store_count'] = $this->getAllStoreCountW($v['id']); //获取全部门店数量
                    $data['list'][$k]['ali_store_count'] = $this->getAliStoreCountW($v['id']); //获取首次口碑接口开店数量
                    $data['list'][$k]['wx_contact'] = $this->getWxcontactW($v['id']); //获取微信商户联系人
                    $data['list'][$k]['merchantinfo_id'] = $this->getMerchantInfoIdW($v['id']); //获取详细表id
                    $data['list'][$k]['gj_open_status'] = $v['gj_open_status']; //玩券管家开通状态 1 未开通 2 已开通 3 已过期
                    $data['list'][$k]['wechat_verify_status_submit_time'] = $v['wechat_verify_status_submit_time']; //微信签约提交时间
                    $data['list'][$k]['wechat_verify_status_auditpass_time'] = $v['wechat_verify_status_auditpass_time']; //微信签约审核通过时间
                    $data['list'][$k]['wechat_verify_status_verify_time'] = $v['wechat_verify_status_verify_time']; //微信签约验证时间
                    $data['list'][$k]['wechat_verify_status_sign_time'] = $v['wechat_verify_status_sign_time']; //微信签约签约时间
                    $data['list'][$k]['wechat_verify_status_reject_time'] = $v['wechat_verify_status_reject_time']; //微信签约驳回时间
                    $gjOrder = GjOrder::model()->find('merchant_id=:merchant_id and order_status=:order_status', array(':merchant_id' => $v['id'], ':order_status' => GJORDER_STATUS_NUUSE));
                    $data['list'][$k]['invite_code'] = !empty($gjOrder['invite_code']) ? $gjOrder['invite_code'] : ''; //验证码
                }
                $result ['status'] = ERROR_NONE;
                $result ['data'] = $data;
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
     * 获取详细表id
     *  xuyf
     * 2016/1/16
     */
    private function getMerchantInfoIdW($merchant_id)
    {
        $model = Merchantinfo::model()->find('merchant_id=:merchant_id', array(':merchant_id' => $merchant_id));
        if (!empty($model)) {
            return $model['id'];
        } else {
            return '';
        }
    }

    /**
     * 获取微信商户联系人
     * xuyf
     * 2016/1/16
     */
    private function getWxcontactW($merchant_id)
    {
        $model = Merchantinfo::model()->find('merchant_id=:merchant_id', array(':merchant_id' => $merchant_id));
        if (!empty($model['wx_contact'])) {
            return $model['wx_contact'];
        } else {
            return '';
        }
    }

    /**
     * 获取全部门店数量
     * xuyf
     * 2016/1/16
     */
    private function getAllStoreCountW($merchant_id)
    {
        $store = Store::model()->findAll('flag=:flag and merchant_id=:merchant_id', array(
            ':flag' => FLAG_NO,
            ':merchant_id' => $merchant_id
        ));
        return count($store);
    }

    /**
     * 获取首次口碑接口开店数量
     * xuyf
     * 2016/1/16
     */
    private function getAliStoreCountW($merchant_id)
    {
        $store = Store::model()->findAll('flag=:flag and merchant_id=:merchant_id and alipay_sync_type=:alipay_sync_type and alipay_sync_verify_status=:alipay_sync_verify_status',
            array(
                ':flag' => FLAG_NO,
                ':merchant_id' => $merchant_id,
                ':alipay_sync_type' => STORE_ALIPAY_SYNC_TYPE_SYNC,
                ':alipay_sync_verify_status' => STORE_ALIPAY_SYNC_STATUS_PASS

            ));
        return count($store);
    }

    /**
     * 计算微信签约支付宝签约各个状态商户的数量
     * @author xyf
     * 2016/01/14
     */
    public function getWechatVerifyStatus($agent_id)
    {
        $data = array(
            'wechat' => array(
                WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT => 0, // 未提交
                WECHATMERCHANT_VERIFY_STATUS_UNCHECK => 0, // 待审核
                WECHATMERCHANT_VERIFY_STATUS_CHECKED => 0, // 审核通过，等待商户验证
                WECHATMERCHANT_VERIFY_STATUS_NOSIGN => 0, // 未签约
                WECHATMERCHANT_VERIFY_STATUS_SIGN => 0  //已签约
            ),
            'alipay' => array(
                'authorized' => 0, //已授权
                'unauthorized' => 0 //未授权
            )

        );
        $agent_id_arr = $this->getLowerIdW($agent_id); //获取该$agent_id服务商以及他所有下级服务商的id数组
        $criteria = new CDbCriteria();
        $criteria->addCondition('flag=:flag');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->addInCondition('agent_id', $agent_id_arr);
        $merchant = Merchant::model()->findAll($criteria);
        if (!empty($merchant)) {
            foreach ($merchant as $v) {
                switch ($v['wechat_verify_status']) {
                    case WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT:
                        $data['wechat'][WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT] += 1;
                        break;
                    case WECHATMERCHANT_VERIFY_STATUS_UNCHECK:
                        $data['wechat'][WECHATMERCHANT_VERIFY_STATUS_UNCHECK] += 1;
                        break;
                    case WECHATMERCHANT_VERIFY_STATUS_CHECKED:
                        $data['wechat'][WECHATMERCHANT_VERIFY_STATUS_CHECKED] += 1;
                        break;
                    case WECHATMERCHANT_VERIFY_STATUS_NOSIGN:
                        $data['wechat'][WECHATMERCHANT_VERIFY_STATUS_NOSIGN] += 1;
                        break;
                    case WECHATMERCHANT_VERIFY_STATUS_SIGN:
                        $data['wechat'][WECHATMERCHANT_VERIFY_STATUS_SIGN] += 1;
                        break;
                }

                if (empty($v['alipay_auth_token'])) {
                    $data['alipay']['unauthorized'] += 1;
                } else {
                    $data['alipay']['authorized'] += 1;
                }
            }
        }
        return $data;
    }

    /**
     * 获取该$agent_id服务商以及他所有下级服务商的id数组
     * @author xyf
     * 2016/01/14
     * $agent_id   服务商id
     */
    private function getLowerIdW($agent_id)
    {
        $data = array();
        $data[0] = $agent_id;
        $model = Agent::model()->findAll('id !=:id and flag = :flag', array(':id' => $agent_id, ':flag' => FLAG_NO));
        if (!empty($model)) {
            foreach ($model as $k => $v) {
                if (!empty($v['gid'])) {
                    static $i = 1;
                    $arr = explode('/', $v['gid']);
                    if (in_array($agent_id, $arr)) { //$agent_id是数组的值  即这个服务商是其下级
                        $data[$i] = $v['id'];
                        $i++;
                    }
                }
            }
        }//print_r($data);exit;
        return $data;
    }

    /**
     * 获取该$agent_id服务商以及他所有下级服务商的id以及名称  用于下拉菜单
     * @author xyf
     * 2016/01/14
     * $agent_id   服务商id
     */
    public function getAgentDropList($agent_id)
    {
        $data = array();
        $agent = Agent::model()->findByPk($agent_id);
        $data[$agent_id] = $agent['name'];
        $model = Agent::model()->findAll('id !=:id and flag = :flag', array(':id' => $agent_id, ':flag' => FLAG_NO));
        if (!empty($model)) {
            foreach ($model as $k => $v) {
                if (!empty($v['gid'])) {
                    $arr = explode('/', $v['gid']);
                    if (in_array($agent_id, $arr)) { //$agent_id是数组的值  即这个服务商是其下级
                        $data[$v['id']] = $v['name'];
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 获取玩券版本   用于下拉菜单
     * @author xyf
     * 2016/01/14
     */
    public function getProductDropList()
    {
        $data = array();
        $model = GjProduct::model()->findAll();
        foreach ($model as $k => $v) {
            $data[$v['id']] = $v['name'];
        }
        return $data;
    }

    /**
     * 服务商添加商户--提交资料
     * @author xyf
     * 2016/01/14
     */
    public function addMerchantStepOneW($agent_id, $wq_m_short_name, $wq_m_type, $wq_m_name, $chooseitem1, $chooseitem2,
                                        $chooseitem3, $province, $city, $area, $detail_address, $wq_m_business_license_no,
                                        $wq_m_organization_code, $wq_m_legal_person_name, $wq_m_legal_person_id, $wq_m_contacts_name, $wq_m_contacts_phone, $wq_m_business_license_path, $wq_m_organization_path, $wq_m_legal_person_positive_path, $wq_m_legal_person_opposite_path)
    {
        $result = array();
        $errMsg = '';
        $flag = 0;
        try {
            $merchant = new Merchant();
            //验证玩券商户简称
            if (empty($wq_m_short_name)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 商户简称必填';
                Yii::app()->user->setFlash('wq_m_short_name_error', '商户简称必填');
            } else {
                $checkExit = Merchant::model()->find('wq_m_short_name=:wq_m_short_name and flag=:flag',
                    array(':wq_m_short_name' => trim($wq_m_short_name), ':flag' => FLAG_NO));
                if (count($checkExit) > 0) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 商户简称已存在';
                    Yii::app()->user->setFlash('wq_m_short_name_error', '商户简称已存在');
                }
            }
            //验证商户类型
            if (empty($wq_m_type)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 商户类型必填';
                Yii::app()->user->setFlash('wq_m_type_error', '商户类型必填');
            }
            //验证所属行业
            if (empty($chooseitem1)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 所属行业必填';
                Yii::app()->user->setFlash('wq_m_industry_error', '所属行业必填');
            }
            if (empty($chooseitem2)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 所属行业必填';
                Yii::app()->user->setFlash('wq_m_industry_error', '所属行业必填');
            }
            if (empty($chooseitem3)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 所属行业必填';
                Yii::app()->user->setFlash('wq_m_industry_error', '所属行业必填');
            }
            //验证商户名
            if (empty($wq_m_name)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 商户名必填';
                Yii::app()->user->setFlash('wq_m_name_error', '商户名必填');
            } else {
                $checkExit = Merchant::model()->find('wq_m_name=:wq_m_name and flag=:flag',
                    array(':wq_m_name' => trim($wq_m_name), ':flag' => FLAG_NO));
                if (count($checkExit) > 0) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 商户名已存在';
                    Yii::app()->user->setFlash('wq_m_name_error', '商户名已存在');
                }
            }

            //验证商户地址
            if (empty($province)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 地址未填写完整';
                Yii::app()->user->setFlash('wq_m_address_error', '地址未填写完整');
            }
            if (empty($city)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 地址未填写完整';
                Yii::app()->user->setFlash('wq_m_address_error', '地址未填写完整');
            }
            if (empty($area)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 地址未填写完整';
                Yii::app()->user->setFlash('wq_m_address_error', '地址未填写完整');
            }

            //验证详细地址
            if (empty($detail_address)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 详细地址必填';
                Yii::app()->user->setFlash('detail_address_error', '详细地址必填');
            }

            //验证玩券商户营业执照注册号
//     		if(empty($wq_m_business_license_no)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 营业执照注册号必填';
//     			Yii::app()->user->setFlash('wq_m_business_license_no_error','营业执照注册号必填');
//     		}

            //验证营业执照
//     		if(empty($wq_m_business_license_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 营业执照必填';
//     			Yii::app()->user->setFlash('wq_m_business_license_path_error','营业执照必填');
//     		}

            //验证组织机构代码
//     		if(!empty($wq_m_type) && $wq_m_type != MERCHANT_TYPE_SELF_EMPLOYED){
//     		   if(empty($wq_m_organization_code)){
//     			  $flag = 1;
//     			  $result ['status'] = ERROR_PARAMETER_MISS;
//     			  $errMsg =  $errMsg .' 组织机构代码必填';
//     			  Yii::app()->user->setFlash('wq_m_organization_code_error','营组织机构代码必填');
//     		   }
//     		}

            //验证组织机构代码证
//     		if(empty($wq_m_organization_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 组织机构代码证必填';
//     			Yii::app()->user->setFlash('wq_m_organization_path_error','组织机构代码证必填');
//     		}

            //验证法人姓名
//     		if(empty($wq_m_legal_person_name)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 法人姓名必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_name_error','法人姓名必填');
//     		}

            //验证法人身份证号
            if (empty($wq_m_legal_person_id)) {
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 法人身份证号必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_id_error','法人身份证号必填');
            } else {
                $isCard = preg_match(IDCARD, $wq_m_legal_person_id);
                if (!$isCard) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 身份证号不合法';
                    Yii::app()->user->setFlash('wq_m_legal_person_id_error', '身份证号不合法');
                }
            }

            //验证法人身份证正面
//     		if(empty($wq_m_legal_person_positive_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 证件影印件正面必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_positive_path_error','证件影印件正面必填');
//     		}
//     		//验证法人身份证反面
//     		if(empty($wq_m_legal_person_opposite_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 证件影印件反面必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_opposite_path_error','证件影印件反面必填');
//     		}

            //验证联系人姓名
            if (empty($wq_m_contacts_name)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 联系人姓名必填';
                Yii::app()->user->setFlash('wq_m_contacts_name_error', '联系人姓名必填');
            }
            //验证联系人手机号
            if (empty($wq_m_contacts_phone)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 联系人手机号必填';
                Yii::app()->user->setFlash('wq_m_contacts_phone_error', '联系人手机号必填');
            } else {
                $checkPhone = preg_match(PHONE_CHECK, $wq_m_contacts_phone);
                if (!$checkPhone) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 手机号格式不合法';
                    Yii::app()->user->setFlash('wq_m_contacts_phone_error', '手机号格式不合法');
                }
            }
            if ($flag == 1) {
                $result ['errMsg'] = $errMsg;
                return json_encode($result);
            }

            $merchant->agent_id = $agent_id;
            $merchant->wq_m_short_name = $wq_m_short_name; //玩券商户简称
            $merchant->wq_m_type = $wq_m_type; //玩券商户类型
            $merchant->wq_m_name = $wq_m_name; //玩券商户名
//     		$item1 = $GLOBALS['WECHAT_MERCHANT_JYLM'][$chooseitem1]['text'];
//     		$item2 = $GLOBALS['WECHAT_MERCHANT_JYLM'][$chooseitem1]['sub'][$chooseitem2]['text'];
//     		$item3 = $GLOBALS['WECHAT_MERCHANT_JYLM'][$chooseitem1]['sub'][$chooseitem2]['sub'][$chooseitem3]['text'];
            $merchant->wq_m_industry = $chooseitem1 . ',' . $chooseitem2 . ',' . $chooseitem3; //玩券商户所属行业
            $merchant->wq_m_address = $province . ',' . $city . ',' . $area . ',' . $detail_address; //玩券商户地址
            $merchant->wq_m_business_license_no = $wq_m_business_license_no; //玩券商户营业执照注册号
            $merchant->wq_m_organization_code = $wq_m_organization_code; //玩券商户组织机构代码
            $merchant->wq_m_legal_person_name = $wq_m_legal_person_name; //玩券商户法人姓名
            $merchant->wq_m_legal_person_id = $wq_m_legal_person_id; //玩券商户法人身份证号
            $merchant->wq_m_contacts_name = $wq_m_contacts_name; //玩券商户联系人姓名
            $merchant->wq_m_contacts_phone = $wq_m_contacts_phone; //玩券商户联系人手机号
            $merchant->wq_m_business_license = $wq_m_business_license_path; //玩券商户营业执照
            $merchant->wq_m_organization = $wq_m_organization_path; //玩券商户组织机构代码证
            $merchant->wq_m_legal_person_positive = $wq_m_legal_person_positive_path; //玩券商户法人身份证正面
            $merchant->wq_m_legal_person_opposite = $wq_m_legal_person_opposite_path; //玩券商户法人身份证反面
            $merchant->create_time = date('Y-m-d H:i:s');
            $merchant->last_time = date('Y-m-d H:i:s');
            $merchant->wq_m_verify_status = MERCHANT_VERIFY_STATUS_WAIT;

            $wq_mchid = '1' . $this->getRandChar(9);
            $merchant_tmp = Merchant::model()->find('wq_mchid =:wq_mchid', array(
                ':wq_mchid' => $wq_mchid
            ));
            while (!empty($merchant_tmp)) {
                $wq_mchid = '1' . $this->getRandChar(9);
                $merchant_tmp = Merchant::model()->find('wq_mchid =:wq_mchid', array(
                    ':wq_mchid' => $wq_mchid
                ));
            }
            $merchant->wq_mchid = $wq_mchid;

            if ($merchant->save()) {
                $merchant_id = $merchant->attributes['id']; //得到上次插入的id
                $result['merchant_id'] = $merchant_id;
                $result ['status'] = ERROR_NONE; // 状态码
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 服务商    商户详情
     * @author xyf
     * 2016/01/14
     */
    public function merchantDetailW($merchant_id)
    {
        $result = array();
        $data = array();
        try {
            $merchant = Merchant::model()->findByPk($merchant_id);
            if (!empty($merchant)) {
                $result['status'] = ERROR_NONE;
                $data['list']['id'] = $merchant['id'];
                $data['list']['create_time'] = $merchant['create_time'];
                $data['list']['agent_id'] = $merchant['agent_id'];
                $data['list']['account'] = $merchant['account']; //账号
                $data['list']['wq_m_short_name'] = $merchant['wq_m_short_name']; //玩券商户简称
                $data['list']['wq_m_type'] = $merchant['wq_m_type']; //玩券商户类型
                $data['list']['wq_m_name'] = $merchant['wq_m_name']; //玩券商户名
                $data['list']['wq_m_industry'] = $merchant['wq_m_industry']; //玩券商户所属行业
                $data['list']['wq_m_address'] = $merchant['wq_m_address']; //玩券商户地址
                $data['list']['wq_m_business_license_no'] = $merchant['wq_m_business_license_no']; //玩券商户营业执照注册号
                $data['list']['wq_m_business_license'] = $merchant['wq_m_business_license']; //玩券商户营业执照
                $data['list']['wq_m_organization_code'] = $merchant['wq_m_organization_code']; //玩券商户组织机构代码
                $data['list']['wq_m_organization'] = $merchant['wq_m_organization']; //玩券商户组织机构代码证
                $data['list']['wq_m_legal_person_name'] = $merchant['wq_m_legal_person_name']; //玩券商户法人姓名
                $data['list']['wq_m_legal_person_id'] = $merchant['wq_m_legal_person_id']; //玩券商户法人身份证号
                $data['list']['wq_m_legal_person_positive'] = $merchant['wq_m_legal_person_positive']; //玩券商户法人身份证正面
                $data['list']['wq_m_legal_person_opposite'] = $merchant['wq_m_legal_person_opposite']; //玩券商户法人身份证反面
                $data['list']['wq_m_contacts_name'] = $merchant['wq_m_contacts_name']; //玩券商户联系人姓名
                $data['list']['wq_m_contacts_phone'] = $merchant['wq_m_contacts_phone']; //玩券商户联系人手机号
                $data['list']['wq_m_verify_status'] = $merchant['wq_m_verify_status']; //玩券商户审核状态
                $data['list']['wq_m_verify_pass_time'] = $merchant['wq_m_verify_pass_time']; //玩券商户审核通过时间
                $data['list']['wq_m_reject_remark'] = $merchant['wq_m_reject_remark']; //玩券商户驳回原因
                $data['list']['gj_product_name'] = empty($merchant->gjproduct->name) ? '' : $merchant->gjproduct->name; //玩券管家版本
                $data['list']['gj_product_id'] = $merchant['gj_product_id']; //玩券管家版本id
                $data['list']['gj_end_time'] = $merchant['gj_end_time']; //玩券管家到期时间
                $data['list']['gj_open_status'] = $merchant['gj_open_status']; //玩券管家开通状态 1 未开通 2 已开通 3 已过期
            }
            $result['data'] = $data;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 添加商户被驳回   进行编辑
     */
    public function addMerchantRejectW($merchant_id, $agent_id, $wq_m_short_name, $wq_m_type, $wq_m_name, $chooseitem1, $chooseitem2,
                                       $chooseitem3, $province, $city, $area, $detail_address, $wq_m_business_license_no,
                                       $wq_m_organization_code, $wq_m_legal_person_name, $wq_m_legal_person_id, $wq_m_contacts_name,
                                       $wq_m_contacts_phone, $wq_m_business_license_path, $wq_m_organization_path, $wq_m_legal_person_positive_path, $wq_m_legal_person_opposite_path)
    {
        $result = array();
        $errMsg = '';
        $flag = 0;
        try {
            $merchant = Merchant::model()->findByPk($merchant_id);
            //验证玩券商户简称
            if (empty($wq_m_short_name)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 商户简称必填';
                Yii::app()->user->setFlash('wq_m_short_name_error', '商户简称必填');
            } else {
                $checkExit = Merchant::model()->find('wq_m_short_name=:wq_m_short_name and flag=:flag and id !=:id',
                    array(':wq_m_short_name' => trim($wq_m_short_name), ':flag' => FLAG_NO, ':id' => $merchant_id));
                if (count($checkExit) > 0) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 商户简称已存在';
                    Yii::app()->user->setFlash('wq_m_short_name_error', '商户简称已存在');
                }
            }
            //验证商户类型
            if (empty($wq_m_type)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 商户类型必填';
                Yii::app()->user->setFlash('wq_m_type_error', '商户类型必填');
            }
            //验证所属行业
            if (empty($chooseitem1)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 所属行业必填';
                Yii::app()->user->setFlash('wq_m_industry_error', '所属行业必填');
            }
            if (empty($chooseitem2)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 所属行业必填';
                Yii::app()->user->setFlash('wq_m_industry_error', '所属行业必填');
            }
            if (empty($chooseitem3)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 所属行业必填';
                Yii::app()->user->setFlash('wq_m_industry_error', '所属行业必填');
            }
            //验证商户名
            if (empty($wq_m_name)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 商户名必填';
                Yii::app()->user->setFlash('wq_m_name_error', '商户名必填');
            } else {
                $checkExit = Merchant::model()->find('wq_m_name=:wq_m_name and flag=:flag and id !=:id',
                    array(':wq_m_name' => trim($wq_m_name), ':flag' => FLAG_NO, ':id' => $merchant_id));
                if (count($checkExit) > 0) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 商户名已存在';
                    Yii::app()->user->setFlash('wq_m_name_error', '商户名已存在');
                }
            }

            //验证商户地址
            if (empty($province)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 地址未填写完整';
                Yii::app()->user->setFlash('wq_m_address_error', '地址未填写完整');
            }
            if (empty($city)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 地址未填写完整';
                Yii::app()->user->setFlash('wq_m_address_error', '地址未填写完整');
            }
            if (empty($area)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 地址未填写完整';
                Yii::app()->user->setFlash('wq_m_address_error', '地址未填写完整');
            }

            //验证详细地址
            if (empty($detail_address)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 详细地址必填';
                Yii::app()->user->setFlash('detail_address_error', '详细地址必填');
            }

            //验证玩券商户营业执照注册号
//     		if(empty($wq_m_business_license_no)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 营业执照注册号必填';
//     			Yii::app()->user->setFlash('wq_m_business_license_no_error','营业执照注册号必填');
//     		}

//     		//验证营业执照
//     		if(empty($wq_m_business_license_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 营业执照必填';
//     			Yii::app()->user->setFlash('wq_m_business_license_path_error','营业执照必填');
//     		}

            //验证组织机构代码
//     		if(!empty($wq_m_type) && $wq_m_type != MERCHANT_TYPE_SELF_EMPLOYED){
//     			if(empty($wq_m_organization_code)){
//     				$flag = 1;
//     				$result ['status'] = ERROR_PARAMETER_MISS;
//     				$errMsg =  $errMsg .' 组织机构代码必填';
//     				Yii::app()->user->setFlash('wq_m_organization_code_error','营组织机构代码必填');
//     			}
//     		}

            //验证组织机构代码证
//     		if(empty($wq_m_organization_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 组织机构代码证必填';
//     			Yii::app()->user->setFlash('wq_m_organization_path_error','组织机构代码证必填');
//     		}

//     		//验证法人姓名
//     		if(empty($wq_m_legal_person_name)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 法人姓名必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_name_error','法人姓名必填');
//     		}

            //验证法人身份证号
            if (empty($wq_m_legal_person_id)) {
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 法人身份证号必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_id_error','法人身份证号必填');
            } else {
                $isCard = preg_match(IDCARD, $wq_m_legal_person_id);
                if (!$isCard) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 身份证号不合法';
                    Yii::app()->user->setFlash('wq_m_legal_person_id_error', '身份证号不合法');
                }
            }

            //验证法人身份证正面
//     		if(empty($wq_m_legal_person_positive_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 证件影印件正面必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_positive_path_error','证件影印件正面必填');
//     		}
//     		//验证法人身份证反面
//     		if(empty($wq_m_legal_person_opposite_path)){
//     			$flag = 1;
//     			$result ['status'] = ERROR_PARAMETER_MISS;
//     			$errMsg =  $errMsg .' 证件影印件反面必填';
//     			Yii::app()->user->setFlash('wq_m_legal_person_opposite_path_error','证件影印件反面必填');
//     		}

            //验证联系人姓名
            if (empty($wq_m_contacts_name)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 联系人姓名必填';
                Yii::app()->user->setFlash('wq_m_contacts_name_error', '联系人姓名必填');
            }
            //验证联系人手机号
            if (empty($wq_m_contacts_phone)) {
                $flag = 1;
                $result ['status'] = ERROR_PARAMETER_MISS;
                $errMsg = $errMsg . ' 联系人手机号必填';
                Yii::app()->user->setFlash('wq_m_contacts_phone_error', '联系人手机号必填');
            } else {
                $checkPhone = preg_match(PHONE_CHECK, $wq_m_contacts_phone);
                if (!$checkPhone) {
                    $flag = 1;
                    $result ['status'] = ERROR_PARAMETER_MISS;
                    $errMsg = $errMsg . ' 手机号格式不合法';
                    Yii::app()->user->setFlash('wq_m_contacts_phone_error', '手机号格式不合法');
                }
            }
            if ($flag == 1) {
                $result ['errMsg'] = $errMsg;
                return json_encode($result);
            }

            $merchant->wq_m_short_name = $wq_m_short_name; //玩券商户简称
            $merchant->wq_m_type = $wq_m_type; //玩券商户类型
            $merchant->wq_m_name = $wq_m_name; //玩券商户名
            $merchant->wq_m_industry = $chooseitem1 . ',' . $chooseitem2 . ',' . $chooseitem3; //玩券商户所属行业
            $merchant->wq_m_address = $province . ',' . $city . ',' . $area . ',' . $detail_address; //玩券商户地址
            $merchant->wq_m_business_license_no = $wq_m_business_license_no; // 玩券商户营业执照注册号
            $merchant->wq_m_organization_code = $wq_m_organization_code; //玩券商户组织机构代码
            $merchant->wq_m_legal_person_name = $wq_m_legal_person_name; //玩券商户法人姓名
            $merchant->wq_m_legal_person_id = $wq_m_legal_person_id; //玩券商户法人身份证号
            $merchant->wq_m_contacts_name = $wq_m_contacts_name; //玩券商户联系人姓名
            $merchant->wq_m_contacts_phone = $wq_m_contacts_phone; //玩券商户联系人手机号
            $merchant->wq_m_business_license = $wq_m_business_license_path; //玩券商户营业执照
            $merchant->wq_m_organization = $wq_m_organization_path; //玩券商户组织机构代码证
            $merchant->wq_m_legal_person_positive = $wq_m_legal_person_positive_path; //玩券商户法人身份证正面
            $merchant->wq_m_legal_person_opposite = $wq_m_legal_person_opposite_path; //玩券商户法人身份证反面
            $merchant->last_time = date('Y-m-d H:i:s');
            $merchant->wq_m_verify_status = MERCHANT_VERIFY_STATUS_WAIT; //玩券商户审核状态    待审核

            if ($merchant->update()) {
                $merchant_id = $merchant->id;
                $result['merchant_id'] = $merchant_id;
                $result ['status'] = ERROR_NONE; // 状态码
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商户详情--门店信息    获取门店列表
     * xuyf
     * 2016/1/16
     */
    public function getStoreListW($merchant_id = null, $address = null, $keyword = null)
    {
        $result = array();

        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            if (!empty($address)) {
                $criteria->addSearchCondition('address', $address);
            }

            if (!empty($keyword)) {
                $criteria->addSearchCondition('name', $keyword);
            }

            if (!empty($merchant_id)) {
                $criteria->addCondition('merchant_id = :merchant_id');
                $criteria->params[':merchant_id'] = $merchant_id;
            }


            //按创建时间排序
            $criteria->order = 'create_time DESC';
            $pages = new CPagination(Store::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $model = Store::model()->findAll($criteria);

            if (!empty($model)) {
                //数据封装
                $data = array();
                foreach ($model as $key => $value) {
                    $data['list'][$key]['id'] = $value->id; //门店id
                    $data['list'][$key]['merchant_id'] = $value->merchant_id; //门店id
                    $data['list'][$key]['number'] = $value->number; //门店编号
                    $data['list'][$key]['name'] = $value->name; //门店名称
                    $data['list'][$key]['telephone'] = $value->telephone; //联系电话
                    $data['list'][$key]['address'] = $value->address; //门店地址
                    //门店不启用收款账号
                    if ($value->if_alipay_open == IF_ALIPAY_OPEN_CLOSE) {
                        //使用上级收款账号
                        if ($value->alipay_use_pro == IF_USE_PRO_YES) {
                            //获取上级收款账号
                            if (!empty($value->management_id)) {
                                //如果门店的分组id不为空，则获取门店分组的信息
                                $model_m = Management::model()->findByPk($value->management_id);
                                //如果所属门店分组不启用收银且使用上级账号
                                if ($model_m->if_alipay_open == IF_ALIPAY_OPEN_CLOSE && $model_m->alipay_use_pro == IF_USE_PRO_YES) {
                                    //如果门店分组的上级分组不为空
                                    if (!empty($model_m->p_mid)) {
                                        //获取上级门店分组
                                        $model_m = Management::model()->findByPk($model_m->p_mid);
                                        //如果上级门店分组不启用收银，且使用上级账号
                                        if ($model_m->if_alipay_open == IF_ALIPAY_OPEN_CLOSE && $model_m->alipay_use_pro == IF_USE_PRO_YES) {
                                            $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                        }
                                    } elseif (!empty($model_m->merchant_id)) {
                                        $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                    }
                                }
                            } elseif (!empty($value->merchant_id)) {//如果商户id不为空，则获取商户
                                $model_m = Merchant::model()->findByPk($value->merchant_id);
                            }

                            if ($model_m->alipay_api_version == ALIPAY_API_VERSION_1) {//1.0
                                if (isset($model_m->partner)) {
                                    $data['list'][$key]['alipay'] = empty($model_m->partner) ? '未设置' : 'PID:' . $model_m->partner . '(上级账号)';
                                } else {
                                    $data['list'][$key]['alipay'] = empty($model_m->alipay_pid) ? '未设置' : 'PID:' . $model_m->alipay_pid . '(上级账号)';
                                }
                            } elseif ($model_m->alipay_api_version == ALIPAY_API_VERSION_2) {//2.0
                                if (isset($model_m->appid)) {
                                    $data['list'][$key]['alipay'] = empty($model_m->appid) ? '未设置' : 'APPID:' . $model_m->appid . '(上级账号)';
                                } else {
                                    $data['list'][$key]['alipay'] = empty($model_m->alipay_appid) ? '未设置' : 'APPID:' . $model_m->alipay_appid . '(上级账号)';
                                }
                            }

                        } elseif ($value->alipay_use_pro == IF_USE_PRO_NO) {
                            //不使用上级收款账号
                            $data['list'][$key]['alipay'] = '未设置';
                        }
                    } elseif ($value->if_alipay_open == IF_ALIPAY_OPEN_OPEN) {
                        //门店启用收款账号
                        //1.0
                        if ($value->alipay_api_version == ALIPAY_API_VERSION_1) {
                            if (!empty($value->alipay_pid)) {
                                $data['list'][$key]['alipay'] = 'PID:' . $value->alipay_pid;
                            } else {
                                $data['list'][$key]['alipay'] = '未设置';
                            }
                        } else if ($value->alipay_api_version == ALIPAY_API_VERSION_2) {
                            //2.0
                            if (!empty($value->alipay_appid)) {
                                $data['list'][$key]['alipay'] = 'APPID:' . $value->alipay_appid;
                            } else {
                                $data['list'][$key]['alipay'] = '未设置';
                            }
                        } else {
                            $data['list'][$key]['alipay'] = '未设置';
                        }
                    }

                    //门店不启用收款账号
                    if ($value->if_wx_open == IF_WXPAY_OPEN_NO) {
                        //使用上级收款账号
                        if ($value->wx_use_pro == IF_USE_PRO_YES) {
                            //获取上级收款账号

                            //获取上级收款账号
                            if (!empty($value->management_id)) {
                                //如果门店的分组id不为空，则获取门店分组的信息
                                $model_m = Management::model()->findByPk($value->management_id);
                                //如果所属门店分组不启用收银且使用上级账号
                                if ($model_m->if_wx_open == IF_WXPAY_OPEN_NO && $model_m->wx_use_pro == IF_USE_PRO_YES) {
                                    //如果门店分组的上级分组不为空
                                    if (!empty($model_m->p_mid)) {
                                        //获取上级门店分组
                                        $model_m = Management::model()->findByPk($model_m->p_mid);
                                        //如果上级门店分组不启用收银，且使用上级账号
                                        if ($model_m->if_wx_open == IF_WXPAY_OPEN_NO && $model_m->wx_use_pro == IF_USE_PRO_YES) {
                                            $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                        }
                                    } elseif (!empty($model_m->merchant_id)) {
                                        $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                    }
                                }
                            } elseif (!empty($value->merchant_id)) {//如果商户id不为空，则获取商户
                                $model_m = Merchant::model()->findByPk($value->merchant_id);
                            }
                            //如果为空则未设置
                            if (empty($model_m)) {
                                $data['list'][$key]['wechat'] = '未设置';
                            }

                            if ((isset($model_m->wechat_mchid) && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) || (!isset($model_m->wechat_mchid) && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF)) {
                                if (isset($model_m->wechat_mchid)) {
                                    $data['list'][$key]['wechat'] = empty($model_m->wechat_mchid) ? '未设置' : '商户号:' . $model_m->wechat_mchid . '(上级账号)';
                                } else {
                                    $data['list'][$key]['wechat'] = empty($model_m->wx_mchid) ? '未设置' : '商户号:' . $model_m->wx_mchid . '(上级账号)';
                                }
                            } elseif ((isset($model_m->wechat_mchid) && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) || (!isset($model_m->wechat_mchid) && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE)) {
                                if (isset($model_m->wechat_mchid)) {
                                    $data['list'][$key]['wechat'] = empty($model_m->wechat_mchid) ? '未设置' : '商户号:' . $model_m->wechat_mchid . '(上级账号)';
                                } else {
                                    $data['list'][$key]['wechat'] = empty($model_m->t_wx_mchid) ? '未设置' : '商户号:' . $model_m->t_wx_mchid . '(上级账号)';
                                }

                            }

                        } elseif ($value->wx_use_pro == IF_USE_PRO_NO) {
                            //不使用上级收款账号
                            $data['list'][$key]['wechat'] = '未设置';
                        }
                    } elseif ($value->if_wx_open == IF_ALIPAY_OPEN_OPEN) {
                        //门店启用收款账号
                        //普通商户
                        if ($value->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                            if (!empty($value->wx_mchid)) {
                                $data['list'][$key]['wechat'] = '商户号:' . $value->wx_mchid;
                            } else {
                                $data['list'][$key]['wechat'] = '未设置';
                            }

                        } else if ($value->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
                            //特约商户
                            if (!empty($value->t_wx_mchid)) {
                                $data['list'][$key]['wechat'] = '商户号:' . $value->t_wx_mchid;
                            } else {
                                $data['list'][$key]['wechat'] = '未设置';
                            }

                        } else {
                            $data['list'][$key]['wechat'] = '未设置';
                        }
                    }
                }
                //分页
                $this->page = $pages;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $this->page = $pages;
                $result['status'] = ERROR_NONE; //状态码
                $data['list'] = array();
                $result['data'] = $data;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 商户信息--交易详情
     * xuyf
     * 2016/1/17
     * $merchant_id  商户id
     * $store_id      门店id
     * $pay_type     交易方式
     * $date         日期
     */
    public function getTradeInfoW($merchant_id, $store_id, $pay_type, $date)
    {
        $result = array();
        try {
            $stores = array();
            $store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            foreach ($store as $k => $v) {
                $stores[$k] = $v->id;
            }

            $criteria = new CDbCriteria();

            if (!empty($store_id)) {
                $criteria->addCondition('store_id = :store_id');
                $criteria->params[':store_id'] = $store_id;
            } else {
                $criteria->addInCondition('store_id', $stores);
            }

            if (!empty($date)) {
                $dateArr = explode('-', $date);
                $startTime = $dateArr[0] . ' 00:00:00';
                $endTime = $dateArr[1] . ' 23:59:59';
                $criteria->addBetweenCondition('date', $startTime, $endTime);
            } else {
                $startTime = date('Y-m-d 00:00:00', strtotime("-7 day"));
                $endTime = date('Y-m-d 23:59:59');
                $criteria->addBetweenCondition('date', $startTime, $endTime);
            }
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = 'date asc';
            $model = ReportFormDay::model()->findAll($criteria);
            $data = array(
                'alipay' => array(
                    'money' => 0,
                    'num' => 0
                ),
                'wxpay' => array(
                    'money' => 0,
                    'num' => 0
                ),
                'otherpay' => array(
                    'money' => 0,
                    'num' => 0
                )
            );
            $dataLine = array();
            if (!empty($model)) {
                //$result['status'] = ERROR_NONE; //状态码
                foreach ($model as $k => $v) {
                    if (empty($pay_type)) {
                        $data['alipay']['money'] += $v['alipay_money'];
                        $data['alipay']['num'] += $v['alipay_num'];

                        $data['wxpay']['money'] += $v['wechat_money'];
                        $data['wxpay']['num'] += $v['wechat_num'];

                        $data['otherpay']['money'] += $v['cash_money'] + $v['stored_money'] + $v['unionpay_money'];
                        $data['otherpay']['num'] += $v['cash_num'] + $v['stored_num'] + $v['unionpay_num'];

                        //折线图数据

                        $dataLine[$k]['alipay']['money'] = $v['alipay_money'];
                        $dataLine[$k]['alipay']['num'] = $v['alipay_num'];

                        $dataLine[$k]['wxpay']['money'] = $v['wechat_money'];
                        $dataLine[$k]['wxpay']['num'] = $v['wechat_num'];

                        $dataLine[$k]['otherpay']['money'] = $v['cash_money'] + $v['stored_money'] + $v['unionpay_money'];
                        $dataLine[$k]['otherpay']['num'] = $v['cash_num'] + $v['stored_num'] + $v['unionpay_num'];
                        $dataLine[$k]['date'] = date('Y-m-d', strtotime($v['date']));

                    } else {
                        if ($pay_type == 1) { //支付宝
                            $data['alipay']['money'] += $v['alipay_money'];
                            $data['alipay']['num'] += $v['alipay_num'];

                            //折线图数据
                            $dataLine[$k]['alipay']['money'] = $v['alipay_money'];
                            $dataLine[$k]['alipay']['num'] = $v['alipay_num'];
                            $dataLine[$k]['wxpay']['money'] = 0;
                            $dataLine[$k]['wxpay']['num'] = 0;
                            $dataLine[$k]['otherpay']['money'] = 0;
                            $dataLine[$k]['otherpay']['num'] = 0;
                            $dataLine[$k]['date'] = date('Y-m-d', strtotime($v['date']));
                        } elseif ($pay_type == 2) { //微信
                            $data['wxpay']['money'] += $v['wechat_money'];
                            $data['wxpay']['num'] += $v['wechat_num'];

                            //折线图数据
                            $dataLine[$k]['wxpay']['money'] = $v['wechat_money'];
                            $dataLine[$k]['wxpay']['num'] = $v['wechat_num'];
                            $dataLine[$k]['alipay']['money'] = 0;
                            $dataLine[$k]['alipay']['num'] = 0;
                            $dataLine[$k]['otherpay']['money'] = 0;
                            $dataLine[$k]['otherpay']['num'] = 0;
                            $dataLine[$k]['date'] = date('Y-m-d', strtotime($v['date']));
                        } elseif ($pay_type == 3) { //其他
                            $data['otherpay']['money'] += $v['cash_money'] + $v['stored_money'] + $v['unionpay_money'];
                            $data['otherpay']['num'] += $v['cash_num'] + $v['stored_num'] + $v['unionpay_num'];

                            //折线图数据
                            $dataLine[$k]['otherpay']['money'] = $v['cash_money'] + $v['stored_money'] + $v['unionpay_money'];
                            $dataLine[$k]['otherpay']['num'] = $v['cash_num'] + $v['stored_num'] + $v['unionpay_num'];
                            $dataLine[$k]['alipay']['money'] = 0;
                            $dataLine[$k]['alipay']['num'] = 0;
                            $dataLine[$k]['wxpay']['money'] = 0;
                            $dataLine[$k]['wxpay']['num'] = 0;
                            $dataLine[$k]['date'] = date('Y-m-d', strtotime($v['date']));
                        }
                    }
                }
            } else {
                if (!empty($date)) {
                    $dateArr = explode('-', $date);
                    $starttime = $dateArr[0];
                    $endtime = $dateArr[1];
                } else {
                    $starttime = date('Y-m-d', strtotime("-7 day"));
                    $endtime = date('Y-m-d');
                }
                static $i = 0;
                for ($start = strtotime($starttime); $start <= strtotime($endtime); $start += 3600 * 24) {
                    $dataLine[$i]['date'] = date('Y-m-d', $start);
                    $dataLine[$i]['alipay']['money'] = 0;
                    $dataLine[$i]['alipay']['num'] = 0;

                    $dataLine[$i]['wxpay']['money'] = 0;
                    $dataLine[$i]['wxpay']['num'] = 0;

                    $dataLine[$i]['otherpay']['money'] = 0;
                    $dataLine[$i]['otherpay']['num'] = 0;
                    $i++;
                }
            }
//     		echo '<pre>';
//     		print_r($dataLine);exit;
            $result['data'] = $data;
            $result['dataLine'] = $dataLine;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 搜索商户下的门店数组
     * xuyf
     * 2016/1/17
     */
    public function getStoreArrW($merchant_id)
    {
        $stores = array();
        $store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
            ':merchant_id' => $merchant_id,
            ':flag' => FLAG_NO
        ));
        foreach ($store as $k => $v) {
            $stores[$v->id] = $v->name;
        }
        return $stores;
    }

    /** 获取商户所有门店信息
     * @param $merchant_id
     * @return array
     */
    public function getAllStore($merchant_id)
    {
        $store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
            ':merchant_id' => $merchant_id,
            ':flag' => FLAG_NO
        ));
        foreach ($store as $k => $v) {
            $data[] = $v->id;
            /*$data[$k]['name'] = $v->name;
            $data[$k]['branch_name'] = $v->branch_name;
            $data[$k]['address'] = $v->address;
            $data[$k]['telephone'] = $v->telephone;*/
        }
        
        return $data;
    }

    /*
     * 根据$encrypt_id查找商户
     * $encrypt_id  加密商户号
     *
     */
    public function getMerchantByEncrypt($encrypt_id, $merchant_id = '')
    {
        $result = array();
        try {
            if (!empty($encrypt_id)) {
                $merchant = Merchant::model()->find('encrypt_id = :encrypt_id and flag = :flag', array(
                    ':encrypt_id' => $encrypt_id,
                    ':flag' => FLAG_NO
                ));
            } elseif (!empty($merchant_id)) {
                $merchant = Merchant::model()->findByPk($merchant_id);
            }

            if ($merchant) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
                $result['data'] = array(
                    'id' => $merchant->id,
                    'appid' => $merchant->appid,
                    'wechat_appid' => $merchant->wechat_appid,
                    'wechat_appsecret' => $merchant->wechat_appsecret,
                    'wechat_qrcode' => $merchant->wechat_qrcode
                );
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '';
                $result['data'] = '';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /***********************************后台************************************/
    //获取所有商户列表，供后台使用
    /*
     * $verify_status 审核状态
     *
     * */
    public function getAllMerchantList($verify_status, $merchant_name, $agent_name)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            if (!empty($verify_status)) {
                $criteria->addCondition('wq_m_verify_status = :wq_m_verify_status');
                $criteria->params[':wq_m_verify_status'] = $verify_status;
            }
            //筛选商户名
            if (!empty($merchant_name)) {
                $criteria->addSearchCondition('wq_m_name', $merchant_name);
            }
            //筛选服务商名
            if (!empty($agent_name)) {
                $criteria1 = new CDbCriteria();
                $criteria1->addCondition('flag = :flag');
                $criteria1->params[':flag'] = FLAG_NO;
                $criteria1->addSearchCondition('name', $agent_name);
                //获取符合的服务商
                $query_agents = Agent::model()->findAll($criteria1);
                $query_array = array();
                foreach ($query_agents as $k => $v) {
                    $query_array[] = $v['id'];
                }

                $criteria->addInCondition('agent_id', $query_array);
            }

            //按创建时间排序
            $criteria->order = 'last_time DESC';

            $pages = new CPagination(Merchant::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $merchant = Merchant::model()->findAll($criteria);
            $data = array();
            foreach ($merchant as $k => $v) {
                $data[$k]['id'] = $v->id;
                $data[$k]['wq_mchid'] = $v->wq_mchid;
                $data[$k]['agent_id'] = $v->agent_id;
                $agent = Agent::model()->findByPk($v->agent_id);
                $data[$k]['agent_name'] = $agent->name;
                $data[$k]['wq_m_name'] = $v->wq_m_name;
                $data[$k]['wq_m_verify_status'] = $v->wq_m_verify_status;
                $data[$k]['wq_m_verify_pass_time'] = $v->wq_m_verify_pass_time;
            }

            $result['data'] = $data;
            $result['status'] = STATUS_SUCCESS;
            $result['errMsg'] = ERROR_MSG_NONE;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : STATUS_ERROR;
//     		$result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //过去玩券商户详情
    /*
     * $id  商户id
     * */
    public function getWqMerchantDetails($id)
    {
        $result = array();
        try {
            $merchant = Merchant::model()->findByPk($id);
            if (!empty($merchant)) {
                $data = array();
                $data['id'] = $merchant->id;
                $data['wq_mchid'] = $merchant->wq_mchid;
                $data['wq_m_name'] = $merchant->wq_m_name;
                $data['wq_m_short_name'] = $merchant->wq_m_short_name;
                $data['wq_m_type'] = $merchant->wq_m_type;
                $data['wq_m_industry'] = $merchant->wq_m_industry;
                $data['wq_m_address'] = $merchant->wq_m_address;
                $data['wq_m_business_license_no'] = $merchant->wq_m_business_license_no;
                $data['wq_m_business_license'] = $merchant->wq_m_business_license;
                $data['wq_m_organization_code'] = $merchant->wq_m_organization_code;
                $data['wq_m_organization'] = $merchant->wq_m_organization;
                $data['wq_m_legal_person_name'] = $merchant->wq_m_legal_person_name;
                $data['wq_m_legal_person_id'] = $merchant->wq_m_legal_person_id;
                $data['wq_m_legal_person_positive'] = $merchant->wq_m_legal_person_positive;
                $data['wq_m_legal_person_opposite'] = $merchant->wq_m_legal_person_opposite;
                $data['wq_m_contacts_name'] = $merchant->wq_m_contacts_name;
                $data['wq_m_contacts_phone'] = $merchant->wq_m_contacts_phone;
                $data['wq_m_verify_status'] = $merchant->wq_m_verify_status;
                $data['wq_m_verify_pass_time'] = $merchant->wq_m_verify_pass_time;
                $data['wq_m_reject_remark'] = $merchant->wq_m_reject_remark;
                $agent = Agent::model()->findByPk($merchant->agent_id);
                $data['agent_name'] = $agent->name;

                $result['data'] = $data;
                $result['status'] = STATUS_SUCCESS;
                $result['errMsg'] = ERROR_MSG_NONE;
            } else {
                $result['data'] = '';
                $result['status'] = STATUS_ERROR;
                $result['errMsg'] = ERROR_MSG_NOT_FOUND_MERCHANT;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : STATUS_ERROR;
            //     		$result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
     * 玩券商户审核通过
     * */
    public function setWqMerchantVerifyStatusPass($id)
    {
        $transaction = Yii::app()->db->beginTransaction();
        $result = array();
        try {
            $merchant = Merchant::model()->findByPk($id);
            //判断商户审核状态，如果是待审核，就改成审核通过
            if ($merchant->wq_m_verify_status == MERCHANT_VERIFY_STATUS_WAIT) {
                $merchant->wq_m_verify_status = MERCHANT_VERIFY_STATUS_AUTH;
                $gjorder = new GjOrder();
                $gjorder->merchant_id = $id;
                $gjorder->wq_product_id = 1;
                $gjorder->pay_money = 0;
                $gjorder->pay_status = 2;
                $gjorder->order_status = GJORDER_STATUS_NUUSE;
                $gjorder->flag = FLAG_NO;
                //生成邀请码
                $code = $this->getRandChar(12);
                $model = GjOrder::model()->findAll('invite_code=:invite_code and order_status=:order_status and flag=:flag', array(
                    ':invite_code' => $code,
                    ':order_status' => GJORDER_STATUS_NUUSE,
                    ':flag' => FLAG_NO
                ));
                while ($model) {
                    $code = $this->getRandChar(12);
                    $model = GjOrder::model()->findAll('invite_code=:invite_code and order_status=:order_status and flag=:flag', array(
                        ':invite_code' => $code,
                        ':order_status' => GJORDER_STATUS_NUUSE,
                        ':flag' => FLAG_NO
                    ));
                }
                $gjorder->invite_code = $code;
            }
            if ($merchant->update()) {
                if ($gjorder->save()) {
                    $transaction->commit(); //数据提交
                    $result['data'] = '';
                    $result['status'] = STATUS_SUCCESS;
                    $result['errMsg'] = ERROR_MSG_NONE;
                }
            } else {
                $result['data'] = '';
                $result['status'] = STATUS_ERROR;
                $result['errMsg'] = ERROR_MSG_UPDATE_MERCHANT_VERIFY_STATUS_PASS_FAIL;
            }
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : STATUS_ERROR;
            //     		$result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //判断商户是否支付宝授权
    public function checkMerchantAuth($id)
    {
        $result = array();
        try {
            $merchant = Merchant::model()->findByPk($id);
            $data = array();
            //已经授权
            if (!empty($merchant->alipay_auth_token)) {
                $data['alipay_auth_time'] = $merchant->alipay_auth_time;
                $data['if_auth'] = true;
            } else {
                $data['if_auth'] = false;
            }
            $result['data'] = $data;
            $result['status'] = STATUS_SUCCESS;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : STATUS_ERROR;
// 			$result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * @return string
     * 交易管理（交易额、交易数量）
     */
    public function getMerchantTrade($agent_id, $time)
    {
        $result = array();

        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->addCondition('agent_id = :agent_id');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->params[':agent_id'] = $agent_id;

            $pages = new CPagination(Merchant::model()->count($criteria));
            $pages->pageSize = 9;
            $pages->applyLimit($criteria);
            $this->page = $pages;
            $model = Merchant::model()->findAll($criteria);
            $data = array();

            foreach ($model as $k => $v) {
                $data['list'][$k]['merchant_no'] = $v->merchant_no;
                $data['list'][$k]['name'] = $v->name;
                $data['list'][$k]['id'] = $v->id;
                if (isset($v->agent_id) && !empty($v->agent_id)) {
                    $parentAgent = Agent::model()->find('id = :id and flag = :flag', array(
                        ':id' => $v->agent_id,
                        ':flag' => FLAG_NO
                    ));
                    if ($parentAgent) {
                        $data['list'][$k]['parentAgent_name'] = $parentAgent->name;
                    }
                }
                //支付宝交易额
                $data['list'][$k]['AliPay'] = $this->sumAliPay($v->id, $time);
                //支付宝交易笔数
                $data['list'][$k]['AliPayNum'] = $this->sumAliPayNum($v->id);
                //微信交易额
                $data['list'][$k]['WeChat'] = $this->sumWeChat($v->id);
                //微信交易笔数
                $data['list'][$k]['WeChatNum'] = $this->sumWeChatNum($v->id);
            }

            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
            $result['data'] = $data;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @return string
     * 获取支付宝交易额
     */
    private function sumAliPay($merchant_id, $time)
    {
        $criteria = new CDbCriteria();

        if (!empty($time)) {
            $time_arr = explode('-', $time);
            $criteria->addBetweenCondition('pay_time', $time_arr[0] . '00:00:00', $time_arr[1] . '23:59:59');
        }

        //删除标志位：正常
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;

        //支付状态为已支付
        $criteria->addCondition('pay_status = :pay_status');
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;

        //订单状态为正常
        $criteria->addCondition('order_status = :order_status');
        $criteria->params['order_status'] = ORDER_STATUS_NORMAL;

        //支付方式为支付宝、支付宝扫码、支付宝条码
        $criteria->addCondition('pay_channel = :pay_channel or pay_channel = :pay_channel1 or pay_channel = :pay_channel2');
        $criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_ALIPAY_SM;
        $criteria->params[':pay_channel1'] = ORDER_PAY_CHANNEL_ALIPAY_TM;
        $criteria->params[':pay_channel2'] = ORDER_PAY_CHANNEL_ALIPAY;

        $criteria->addCondition('merchant_id = :merchant_id');
        $criteria->params[':merchant_id'] = $merchant_id;

        $order = Order::model()->findAll($criteria);
        $count = 0;

        //计算支付宝交易额
        foreach ($order as $k => $v) {
            $count += $v->order_paymoney;
        }

        return $count;
    }

    /**
     * @param $merchant_id
     * @return int
     * 获取支付宝交易数
     */
    private function sumAliPayNum($merchant_id)
    {
        $criteria = new CDbCriteria();

        //删除标志位：正常
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;

        //支付状态为已支付
        $criteria->addCondition('pay_status = :pay_status');
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;

        //订单状态为正常
        $criteria->addCondition('order_status = :order_status');
        $criteria->params['order_status'] = ORDER_STATUS_NORMAL;

        //支付方式为支付宝、支付宝扫码、支付宝条码
        $criteria->addCondition('pay_channel = :pay_channel or pay_channel = :pay_channel1 or pay_channel = :pay_channel2');
        $criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_ALIPAY_SM;
        $criteria->params[':pay_channel1'] = ORDER_PAY_CHANNEL_ALIPAY_TM;
        $criteria->params[':pay_channel2'] = ORDER_PAY_CHANNEL_ALIPAY;

        $criteria->addCondition('merchant_id = :merchant_id');
        $criteria->params[':merchant_id'] = $merchant_id;

        $order = Order::model()->findAll($criteria);
        $count = 0;

        //计算支付宝交易订单数
        foreach ($order as $k => $v) {
            $count += count($v->id);
        }

        return $count;
    }

    /**
     * @return string
     * 获取微信交易额
     */
    private function sumWeChat($merchant_id)
    {
        $criteria = new CDbCriteria();

        //删除标志位：正常
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;

        //支付状态为已支付
        $criteria->addCondition('pay_status = :pay_status');
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;

        //订单状态为正常
        $criteria->addCondition('order_status = :order_status');
        $criteria->params['order_status'] = ORDER_STATUS_NORMAL;

        //支付方式为微信、微信扫码、微信条码
        $criteria->addCondition('pay_channel = :pay_channel or pay_channel = :pay_channel1 or pay_channel = :pay_channel2');
        $criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_WXPAY_SM;
        $criteria->params[':pay_channel1'] = ORDER_PAY_CHANNEL_WXPAY_TM;
        $criteria->params[':pay_channel2'] = ORDER_PAY_CHANNEL_WXPAY;

        $criteria->addCondition('merchant_id = :merchant_id');
        $criteria->params[':merchant_id'] = $merchant_id;

        $order = Order::model()->findAll($criteria);
        $count = 0;

        //计算微信交易额
        foreach ($order as $k => $v) {
            $count += $v->order_paymoney;
        }

        return $count;
    }

    /**
     * @param $merchant_id
     * @return int
     * 获取微信交易订单数
     */
    private function sumWeChatNum($merchant_id)
    {
        $criteria = new CDbCriteria();

        //删除标志位：正常
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;

        //支付状态为已支付
        $criteria->addCondition('pay_status = :pay_status');
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;

        //订单状态为正常
        $criteria->addCondition('order_status = :order_status');
        $criteria->params['order_status'] = ORDER_STATUS_NORMAL;

        //支付方式为微信、微信扫码、微信条码
        $criteria->addCondition('pay_channel = :pay_channel or pay_channel = :pay_channel1 or pay_channel = :pay_channel2');
        $criteria->params[':pay_channel'] = ORDER_PAY_CHANNEL_WXPAY_SM;
        $criteria->params[':pay_channel1'] = ORDER_PAY_CHANNEL_WXPAY_TM;
        $criteria->params[':pay_channel2'] = ORDER_PAY_CHANNEL_WXPAY;

        $criteria->addCondition('merchant_id = :merchant_id');
        $criteria->params[':merchant_id'] = $merchant_id;

        $order = Order::model()->findAll($criteria);
        $count = 0;

        //计算微信交易订单数
        foreach ($order as $k => $v) {
            $count += count($v->id);
        }

        return $count;
    }


    /**
     * 获取玩券商户号
     * @param unknown $merchant_id
     * @throws Exception
     * @return multitype:string NULL Ambigous <static, unknown, Ambigous <static, NULL>, unknown>
     */
    public function getWqMchid($merchant_id)
    {
        $result = array();
        try {
            $model = Merchant::model()->findByPk($merchant_id);
            $data = array();
            if (empty($model)) {
                throw new Exception('数据不存在');
            }

            $result['wq_mchid'] = $model['wq_mchid'];
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : STATUS_ERROR;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }


    /*
     * 合并重复商户
     */
    public function mergeMerchant()
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            //服务商
            $agent_id_arr = array(57, 73, 74, 75, 76, 77, 81, 83, 92);
            $criteria = new CDbCriteria;
            $criteria->addInCondition('agent_id', $agent_id_arr);
            // 			$criteria->addCondition('flag=:flag');
            // 			$criteria->params[':flag'] = 1;
            $LeeZ_merchant = Merchant::model()->findAll($criteria);
            $count = 0;
            $arr_merchant_name = array();
            foreach ($LeeZ_merchant as $k => $v) {
                $merchantInfo_k = Merchantinfo::model()->find('merchant_id = :merchant_id and wx_merchant_name is null', array(
                    ':merchant_id' => $v->id
                ));
                $merchantInfo_c = Merchantinfo::model()->find('merchant_id = :merchant_id and wx_merchant_name is not null', array(
                    ':merchant_id' => $v->id
                ));

                if (empty($merchantInfo_c) || empty($merchantInfo_k)) {
                    continue;
                }

                //将要去除的商户信息转移到保留的商户信息里
                $merchantInfo_k->wx_contact = $merchantInfo_c->wx_contact;
                $merchantInfo_k->wx_tel = $merchantInfo_c->wx_tel;
                $merchantInfo_k->wx_email = $merchantInfo_c->wx_email;
                $merchantInfo_k->wx_abbreviation = $merchantInfo_c->wx_abbreviation;
                $merchantInfo_k->wx_business_category = $merchantInfo_c->wx_business_category;
                $merchantInfo_k->wx_qualifications = $merchantInfo_c->wx_qualifications;
                $merchantInfo_k->wx_product_description = $merchantInfo_c->wx_product_description;
                $merchantInfo_k->wx_customer_service = $merchantInfo_c->wx_customer_service;
                $merchantInfo_k->wx_company_website = $merchantInfo_c->wx_company_website;
                $merchantInfo_k->wx_supply = $merchantInfo_c->wx_supply;
                $merchantInfo_k->wx_merchant_name = $merchantInfo_c->wx_merchant_name;

                $merchantInfo_k->wx_registered_address = $merchantInfo_c->wx_registered_address;
                $merchantInfo_k->wx_business_license_no = $merchantInfo_c->wx_business_license_no;
                $merchantInfo_k->wx_operating_range = $merchantInfo_c->wx_operating_range;
                $merchantInfo_k->wx_business_deadline_start = $merchantInfo_c->wx_business_deadline_start;
                $merchantInfo_k->wx_business_deadline_end = $merchantInfo_c->wx_business_deadline_end;
                $merchantInfo_k->wx_business_deadline_longterm = $merchantInfo_c->wx_business_deadline_longterm;
                $merchantInfo_k->wx_business_license_img = $merchantInfo_c->wx_business_license_img;
                $merchantInfo_k->wx_organization_code = $merchantInfo_c->wx_organization_code;
                $merchantInfo_k->wx_organization_code_start = $merchantInfo_c->wx_organization_code_start;
                $merchantInfo_k->wx_organization_code_end = $merchantInfo_c->wx_organization_code_end;
                $merchantInfo_k->wx_organization_code_longterm = $merchantInfo_c->wx_organization_code_longterm;

                $merchantInfo_k->wx_organization_code_img = $merchantInfo_c->wx_organization_code_img;
                $merchantInfo_k->wx_credentials_user_type = $merchantInfo_c->wx_credentials_user_type;
                $merchantInfo_k->wx_credentials_user_name = $merchantInfo_c->wx_credentials_user_name;
                $merchantInfo_k->wx_credentials_type = $merchantInfo_c->wx_credentials_type;
                $merchantInfo_k->wx_credentials_positive = $merchantInfo_c->wx_credentials_positive;
                $merchantInfo_k->wx_credentials_opposite = $merchantInfo_c->wx_credentials_opposite;
                $merchantInfo_k->wx_credentials_start = $merchantInfo_c->wx_credentials_start;
                $merchantInfo_k->wx_credentials_end = $merchantInfo_c->wx_credentials_end;
                $merchantInfo_k->wx_credentials_longterm = $merchantInfo_c->wx_credentials_longterm;
                $merchantInfo_k->wx_credentials_no = $merchantInfo_c->wx_credentials_no;
                $merchantInfo_k->wx_account_type = $merchantInfo_c->wx_account_type;
                $merchantInfo_k->wx_bank_name = $merchantInfo_c->wx_bank_name;

                $merchantInfo_k->wx_bank_area = $merchantInfo_c->wx_bank_area;
                $merchantInfo_k->wx_bank_subbranch = $merchantInfo_c->wx_bank_subbranch;
                $merchantInfo_k->wx_bank_account = $merchantInfo_c->wx_bank_account;
                $merchantInfo_k->wx_account_name = $merchantInfo_c->wx_account_name;
                $merchantInfo_k->wx_jsydgh_license = $merchantInfo_c->wx_jsydgh_license;
                $merchantInfo_k->wx_jsgcgh_license = $merchantInfo_c->wx_jsgcgh_license;
                $merchantInfo_k->wx_jzgckg_license = $merchantInfo_c->wx_jzgckg_license;
                $merchantInfo_k->wx_gytd_license = $merchantInfo_c->wx_gytd_license;
                $merchantInfo_k->wx_spfys_license = $merchantInfo_c->wx_spfys_license;
                $merchantInfo_k->wx_wwpm_license = $merchantInfo_c->wx_wwpm_license;
                $merchantInfo_k->wx_wnjy_license = $merchantInfo_c->wx_wnjy_license;
                $merchantInfo_k->wx_frdj_license = $merchantInfo_c->wx_frdj_license;

                if ($merchantInfo_k->update()) {
                    $count++;
                    $arr_merchant_name[$k] = $v->wq_m_name;
                } else {
                    throw new Exception('商户详细信息保存失败' . $v->wq_m_name);
                }
            }
            $transaction->commit(); //数据提交
            $result['status'] = ERROR_NONE;
            $result['data'] = $count;
            $result['merchant_name'] = $arr_merchant_name;
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    /***************************************问卷调查开始***********************************************/
    /**
     * @param $merchant_id
     * @param $branch_company
     * @param $contacts
     * @param $tel
     * @param $question1
     * @param $question2
     * @param $question3
     * @param $question4
     * @param $question5
     * @param $question6
     * @param $question7
     * @param $question8
     * @param $question9
     * @return string
     *
     * 问卷调查，在merchant C()类里定义一个保存方法saveQuestion（）
     */
    public function saveQuestion($merchant_id, $branch_company, $contacts, $tel, $question1, $question2, $question3, $question4, $question5, $question6, $question7, $question8, $question9)
    {
        //以数组形式返回结果
        $result = array();
        try {
            //实例化模型
            $model = new QuestionnaireInvestigationYoungor();
            $model->merchant_id = $merchant_id;
            $model->create_time = new CDbExpression('now()');
            $model->branch_company = $branch_company;
            $model->contacts = $contacts;
            $model->tel = $tel;
            $model->question1 = $question1;
            $model->question2 = $question2;
            $model->question3 = $question3;
            $model->question4 = $question4;
            $model->question5 = $question5;
            $model->question6 = $question6;
            $model->question7 = $question7;
            $model->question8 = $question8;
            $model->question9 = $question9;
            if (!$model->save()) {
                throw new Exception('保存失败');
            }
            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    public function saveQuestion1($param)
    {
        //以数组形式返回结果
        $result = array();
        try {
            //实例化模型
            $model = new QuestionnaireInvestigationYoungor();
            $model->attributes = $param;
            if (!$model->save()) {
                $result['status'] = ERROR_EXCEPTION;
                $result['errMsg'] = $model->getErrors(); //错误信息
            } else {
                $result['status'] = ERROR_NONE;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //检查手机号是否重复
    public function checkTel($tel)
    {
        $result = array();
        try {
            $question = QuestionnaireInvestigationYoungor::model()->find('tel = :tel', array(
                ':tel' => $tel
            ));
            if (!empty($question)) {
                $result['if_repeat'] = 1;
            } else {
                $result['if_repeat'] = 2;
            }
            $result['status'] = ERROR_NONE;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //查询问卷调查结果
    public function getQuestionResult($merchant_id)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;

            $criteria->order = 'create_time DESC';

            //参与人数
            $question_num = QuestionnaireInvestigationYoungor::model()->count($criteria);

            $pages = new CPagination(QuestionnaireInvestigationYoungor::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $question = QuestionnaireInvestigationYoungor::model()->findAll($criteria);
            $list = array();
            foreach ($question as $k => $v) {
                $list[$k]['contacts'] = $v->contacts;
                $list[$k]['tel'] = $v->tel;
                $list[$k]['branch_company'] = $v->branch_company;
                $list[$k]['question1'] = $v->question1;
                $list[$k]['question2'] = $v->question2;
                $list[$k]['question3'] = $v->question3;
                $list[$k]['question4'] = $v->question4;
                $list[$k]['question5'] = $v->question5;
                $list[$k]['question6'] = $v->question6;
                $list[$k]['question7'] = $v->question7;
                $list[$k]['question8'] = $v->question8;
                $list[$k]['question9'] = $v->question9;
            }


            //question1 选择是的人数
            $q1_num = QuestionnaireInvestigationYoungor::model()->count('merchant_id =:merchant_id and question1 = :question1', array(
                ':merchant_id' => $merchant_id,
                ':question1' => IS_ANSWER_YES
            ));
            //question2 选择是的人数
            $q2_num = QuestionnaireInvestigationYoungor::model()->count('merchant_id =:merchant_id and question2 = :question2', array(
                ':merchant_id' => $merchant_id,
                ':question2' => IS_ANSWER_YES
            ));
            //question3 选择是的人数
            $q3_num = QuestionnaireInvestigationYoungor::model()->count('merchant_id =:merchant_id and question3 = :question3', array(
                ':merchant_id' => $merchant_id,
                ':question3' => IS_ANSWER_YES
            ));
            //question4 选择是的人数
            $q4_num = QuestionnaireInvestigationYoungor::model()->count('merchant_id =:merchant_id and question4 = :question4', array(
                ':merchant_id' => $merchant_id,
                ':question4' => IS_ANSWER_YES
            ));

            //question7 选择是的人数
            $q7_num = QuestionnaireInvestigationYoungor::model()->count('merchant_id =:merchant_id and question7 = :question7', array(
                ':merchant_id' => $merchant_id,
                ':question7' => IS_ANSWER_YES
            ));

            //question9 选择是的人数
            $q9_num = QuestionnaireInvestigationYoungor::model()->count('merchant_id =:merchant_id and question9 = :question9', array(
                ':merchant_id' => $merchant_id,
                ':question9' => IS_ANSWER_YES
            ));
            $data = array(
                'question_num' => $question_num,
                'q1_num' => $q1_num,
                'q2_num' => $q2_num,
                'q3_num' => $q3_num,
                'q4_num' => $q4_num,
                'q7_num' => $q7_num,
                'q9_num' => $q9_num
            );

            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
            $result['list'] = $list;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //导出excel
    public function exportQuestionResult($merchant_id)
    {
        $result = array();
        try {
            $question = QuestionnaireInvestigationYoungor::model()->findAll('merchant_id =:merchant_id', array(
                ':merchant_id' => $merchant_id
            ));
            $list = array();
            foreach ($question as $k => $v) {
                $list[$k]['contacts'] = $v->contacts;
                $list[$k]['tel'] = $v->tel;
                $list[$k]['branch_company'] = $v->branch_company;
                $list[$k]['question1'] = $v->question1 == 1 ? '是' : '否';
                $list[$k]['question2'] = $v->question2 == 1 ? '是' : '否';
                $list[$k]['question3'] = $v->question3 == 1 ? '是' : '否';
                $list[$k]['question4'] = $v->question4 == 1 ? '是' : '否';
                $list[$k]['question5'] = $v->question5;
                $list[$k]['question6'] = $v->question6;
                $list[$k]['question7'] = $v->question7 == 1 ? '是' : '否';
                $list[$k]['question8'] = $v->question8;
                $list[$k]['question9'] = $v->question9 == 1 ? '是' : '否';
            }

            $result['status'] = ERROR_NONE;
            $result['list'] = $list;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /************************************问卷调查结束***************************************/

    /******************************************玩券管家******************************************/
    //获取商户玩券管家信息
    public function getMerchantWqinfo($merchant_id)
    {
        $result = array();
        try {
            $merchant = Merchant::model()->findByPk($merchant_id);
            $data = array();
            if (!empty($merchant)) {
                //判断是否营销版
                if ($merchant->gj_product_id == WANQUAN_TYPE_MARKETING) {
                    //是营销版 则 判断是否过期
                    if (strtotime(date('Y-m-d 23:59:59', strtotime($merchant->gj_end_time))) < time()) {
                        //如果过期 则 为收银版登录
                        $data['gj_product_id'] = WANQUAN_TYPE_CASH;
                        //开通状态改为已过期
                        $merchant->gj_open_status = GJ_OPEN_STATUS_OVERTIME;
                        $merchant->save();
                    } else {
                        //如果未过期 则 判断是否为试用
                        if ($merchant->if_tryout == IF_TRYOUT_NO) {
                            //如果为非试用 则 营销版登录
                            $data['gj_product_id'] = WANQUAN_TYPE_MARKETING;
                        } else {
                            //如果是试用 则 判断试用状态是否正常
                            if ($merchant->tryout_status == TRYOUT_STATUS_NORMAL) {
                                //如果试用正常 则 营销版登录
                                $data['gj_product_id'] = WANQUAN_TYPE_MARKETING;
                            } else {
                                //如果试用关闭 则 收银版登录
                                $data['gj_product_id'] = WANQUAN_TYPE_CASH;
                            }
                        }
                    }
                    $data['if_tryout'] = $merchant->if_tryout;
                    //到期时间
                    $data['gj_end_time'] = $merchant->gj_end_time;
                } else {
                    $data['gj_product_id'] = WANQUAN_TYPE_CASH;
                    $data['gj_end_time'] = $merchant->gj_end_time;
                    $data['if_tryout'] = $merchant->if_tryout;
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

    /** 查询商户会员认证填写信息
     * @param $merchant_id
     * @return string
     */
    public function getFillInfo($merchant_id)
    {
        try {
            $merchant = Merchant::model()->find('id =:merchant_id and flag =:flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));

            if (!empty($merchant) && !empty($merchant['auth_set'])) {
                $result['data'] = $merchant['auth_set'];
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /*******************************************玩券管家结束***********************************************/

}