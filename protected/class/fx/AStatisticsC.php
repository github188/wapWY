<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 数据统计类
 */
class AStatisticsC extends mainClass
{
    private static $_instance = null;

    public static function getInstance(){
        if (!self::$_instance instanceof self){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 获取服务商概况
     * @param unknown $agent_id
     * @return string
     */
    public function getAgentProfile($agent_id) {
        $result = array();
        try {
            //参数验证
            if (!isset($agent_id) && empty($agent_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少agent_id参数');
            }
            //查询数据
            $model = AStatistics::model()->find('agent_id = :agent_id AND date like :date AND flag = :flag',array(
               ':agent_id' => $agent_id,
                ':date' => date("Y-m-d",strtotime("-1 day")).'%',
                ':flag' => FLAG_NO
            ));
    		$data =array();
    		if (!empty($model)){
    		    $data['id'] = $model->id;
    		    $data['agent_id'] = $model->agent_id;//服务运营商id
    		    $data['date'] = $model->date;//日期
    		    $data['total_merchant_num'] = $model->total_merchant_num;//累计商户数
    		    $data['new_merchant_num'] = $model->new_merchant_num;//今日新增商户数
    		    $data['total_yx_merchant_num'] = $model->total_yx_merchant_num;//累计新增营销版商户数
    		    $data['new_yx_merchant_num'] = $model->new_yx_merchant_num;//今日新增营销版商户数
    		    $data['total_yx_servicecharge'] = $model->total_yx_servicecharge;//累计营销版服务费
    		    $data['new_yx_servicecharge'] = $model->new_yx_servicecharge;//今日新增营销版服务费
    		    $data['total_store_num'] = $model->total_store_num;//累计门店数
    		    $data['new_store_num'] = $model->new_store_num;//今日新增门店数
    		    $data['active_store_num'] = $model->active_store_num;//活跃门店数
    		    $data['total_one_level_agent_num'] = $model->total_one_level_agent_num;//累计一级服务运营商数
    		    $data['total_two_level_agent_num'] = $model->total_two_level_agent_num;//累计二级服务运营商数
    		    $data['total_one_level_agent_fee'] = $model->total_one_level_agent_fee;//累计一级服务运营商佣金
    		    $data['total_two_level_agent_fee'] = $model->total_two_level_agent_fee;//累计二级服务运营商佣金
    		    $data['new_trade_money'] = $model->new_trade_money;//新增交易金额
    		    $data['new_trade_alipay_money'] = $model->new_trade_alipay_money;//新增支付宝交易金额
    		    $data['new_trade_wechat_money'] = $model->new_trade_wechat_money;//新增微信交易金额
    		    $data['new_trade_unionpay_money'] = $model->new_trade_unionpay_money;//新增银联交易金额
    		    $data['new_trade_num'] = $model->new_trade_num;//新增交易笔数
    		    $data['new_trade_alipay_num'] = $model->new_trade_alipay_num;//新增支付宝交易笔数
    		    $data['new_trade_wechat_num'] = $model->new_trade_wechat_num;//新增微信交易笔数
    		    $data['new_trade_unionpay_num'] = $model->new_trade_unionpay_num;//新增银联交易笔数
    		    $data['new_trade_coupon_num'] = $model->new_trade_coupon_num;//新增券核销笔数
    		    $data['day_trade_unit_price'] = $model->day_trade_unit_price;//今日订单笔单价
    		    $data['day_trade_alipay_unit_price'] = $model->day_trade_alipay_unit_price;//今日支付宝订单笔单价
    		    $data['day_trade_wechat_unit_price'] = $model->day_trade_wechat_unit_price;//今日微信订单笔单价
    		    $data['day_trade_unionpay_unit_price'] = $model->day_trade_unionpay_unit_price;//今日银联订单笔单价
    		    $data['day_store_unit_yield'] = $model->day_store_unit_yield;//今日门店单产（笔数）
    		    $data['day_alipay_commision_money'] = $model->day_alipay_commision_money;//今日符合支付宝返佣条件金额
    		    $data['day_wechat_commision_money'] = $model->day_wechat_commision_money;//今日符合微信返佣条件金额
    		    $data['day_alipay_commision_num'] = $model->day_alipay_commision_num;//今日符合支付宝返佣条件的笔数
    		    $data['day_wechat_commision_num'] = $model->day_wechat_commision_num;//今日符合微信返佣条件的笔数
    		    $data['day_new_user_num'] = $model->day_new_user_num;//今日新增用户数
    		    $data['day_new_alipay_fans_num'] = $model->day_new_alipay_fans_num;//今日新增支付宝粉丝数
    		    $data['day_new_wechat_fans_num'] = $model->day_new_wechat_fans_num;//今日新增微信粉丝数
    		    $data['day_new_member_num'] = $model->day_new_member_num;//今日新增会员数
    		    $data['total_trade_money'] = $model->total_trade_money;//累计交易金额
    		    $data['total_trade_alipay_money'] = $model->total_trade_alipay_money;//累计支付宝交易金额
    		    $data['total_trade_wechat_money'] = $model->total_trade_wechat_money;//累计微信交易金额
    		    $data['total_trade_unionpay_money'] = $model->total_trade_unionpay_money;//累计银联交易金额
    		    $data['total_trade_num'] = $model->total_trade_num;//累计交易笔数
    		    $data['total_trade_alipay_num'] = $model->total_trade_alipay_num;//累计支付宝交易笔数
    		    $data['total_trade_wechat_num'] = $model->total_trade_wechat_num;//累计微信交易笔数
    		    $data['total_trade_unionpay_num'] = $model->total_trade_unionpay_num;//累计银联交易笔数
    		    $data['total_trade_coupon_num'] = $model->total_trade_coupon_num;//累计券核销笔数
    		    $data['total_trade_unit_price'] = $model->total_trade_unit_price;//累计订单笔单价
    		    $data['total_trade_alipay_unit_price'] = $model->total_trade_alipay_unit_price;//累计支付宝订单笔单价
    		    $data['total_trade_wechat_unit_price'] = $model->total_trade_wechat_unit_price;//累计微信订单笔单价
    		    $data['total_trade_unionpay_unit_price'] = $model->total_trade_unionpay_unit_price;//累计银联订单笔单价
    		    $data['total_store_unit_yield'] = $model->total_store_unit_yield;//累计门店单产（笔数）
    		    $data['total_alipay_commision_money'] = $model->total_alipay_commision_money;//累计符合支付宝返佣条件金额
    		    $data['total_wechat_commision_money'] = $model->total_wechat_commision_money;//累计符合微信返佣条件金额
    		    $data['total_alipay_commision_num'] = $model->total_alipay_commision_num;//累计符合支付宝返佣条件的笔数
    		    $data['total_wechat_commision_num'] = $model->total_wechat_commision_num;//累计符合微信返佣条件的笔数
    		    $data['total_new_user_num'] = $model->total_new_user_num;//累计新增用户数
    		    $data['total_new_alipay_fans_num'] = $model->total_new_alipay_fans_num;//累计新增支付宝粉丝数
    		    $data['total_new_wechat_fans_num'] = $model->total_new_wechat_fans_num;//累计新增微信粉丝数
    		    $data['total_new_member_num'] = $model->total_new_member_num;//累计新增会员数
    		    
    		    $result['data'] = $data;
    		    $result['status'] = ERROR_NONE; //状态码
    		    $result['errMsg'] = ''; //错误信息
    		}else{
    		    $result['status'] = ERROR_NO_DATA;
    		    throw new Exception('无此数据');
    		}
    	} catch (Exception $e) {
    	    $result['data'] = '';
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
        return json_encode($result);
    }
    
    /**
     * 获取服务商七日情况
     * @param unknown $agent_id
     * @throws Exception
     * @return string
     */
    public function getAgentWeekTrading($agent_id){
        $result = array();
        $data =array();
        try {
            //参数验证
            if (!isset($agent_id) && empty($agent_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少agent_id参数');
            }
            //查询数据
            $criteria = new CDbcriteria();
            $start_time = date("Y-m-d",strtotime("-1 week"))." 00:00:00";
            $end_time = date("Y-m-d",strtotime("-1 day"))." 23:59:59";
            $criteria -> addCondition('agent_id = :agent_id');
            $criteria -> addCondition('date >= :start_time');
            $criteria -> addCondition('date <= :end_time');
            $criteria -> addCondition('flag = :flag');
            $criteria -> params = array(
                ':agent_id' => $agent_id,
                ':start_time' => $start_time,
                ':end_time' => $end_time,
                ':flag' => FLAG_NO
            );
            $model = AStatistics::model()->findAll($criteria);
            //七日新增商户数
            if (!empty($model)){
                    $data['week_new_merchant_num'] = 0;//新增商户数
                    $data['week_new_yx_merchant_num'] = 0;//新增营销版商户数
                    $data['week_new_yx_servicecharge'] = 0;//新增营销版服务费
                    $data['week_new_store_num'] = 0;//新增门店数
                    $data['week_new_trade_money'] = 0;//新增交易金额
                    $data['week_new_trade_alipay_money'] = 0;//新增支付宝交易金额
                    $data['week_new_trade_wechat_money'] = 0;//新增微信交易金额
                    $data['week_new_trade_unionpay_money'] = 0;//新增银联交易金额
                    $data['week_new_trade_num'] = 0;//新增交易笔数
                    $data['week_new_trade_alipay_num'] = 0;//新增支付宝交易笔数
                    $data['week_new_trade_wechat_num'] = 0;//新增微信交易笔数
                    $data['week_new_trade_unionpay_num'] = 0;//新增银联交易笔数
                    $data['week_new_trade_coupon_num'] = 0;//新增券核销笔数
                    $data['week_day_trade_unit_price'] = 0;//今日订单笔单价
                    $data['week_day_trade_alipay_unit_price'] = 0;//今日支付宝订单笔单价
                    $data['week_day_trade_wechat_unit_price'] = 0;//今日微信订单笔单价
                    $data['week_day_trade_unionpay_unit_price'] = 0;//今日银联订单笔单价
                    $data['week_day_store_unit_yield'] = 0;//今日门店单产（笔数）
                    $data['week_day_alipay_commision_money'] = 0;//今日符合支付宝返佣条件金额
                    $data['week_day_wechat_commision_money'] = 0;//今日符合微信返佣条件金额
                    $data['week_day_alipay_commision_num'] = 0;//今日符合支付宝返佣条件的笔数
                    $data['week_day_wechat_commision_num'] = 0;//今日符合微信返佣条件的笔数
                    $data['week_day_new_user_num'] = 0;//今日新增用户数
                    $data['week_day_new_alipay_fans_num'] = 0;//今日新增支付宝粉丝数
                    $data['week_day_new_wechat_fans_num'] = 0;//今日新增微信粉丝数
                    $data['week_day_new_member_num'] = 0;//今日新增会员数
                foreach ($model as $k => $v){
                    $data['week_new_merchant_num'] += $v['new_merchant_num'];
                    $data['week_new_yx_merchant_num'] += $v['new_yx_merchant_num'];
                    $data['week_new_yx_servicecharge'] += $v['new_yx_servicecharge'];
                    $data['week_new_store_num'] += $v['new_store_num'];
                    $data['week_new_trade_money'] += $v['new_trade_money'];
                    $data['week_new_trade_alipay_money'] += $v['new_trade_alipay_money'];
                    $data['week_new_trade_wechat_money'] += $v['new_trade_wechat_money'];
                    $data['week_new_trade_unionpay_money'] += $v['new_trade_unionpay_money'];
                    $data['week_new_trade_num'] += $v['new_trade_num'];
                    $data['week_new_trade_alipay_num'] += $v['new_trade_alipay_num'];
                    $data['week_new_trade_wechat_num'] += $v['new_trade_wechat_num'];
                    $data['week_new_trade_unionpay_num'] += $v['new_trade_unionpay_num'];
                    $data['week_new_trade_coupon_num'] += $v['new_trade_coupon_num'];
                    $data['week_day_trade_unit_price'] += $v['day_trade_unit_price'];
                    $data['week_day_trade_alipay_unit_price'] += $v['day_trade_alipay_unit_price'];
                    $data['week_day_trade_wechat_unit_price'] += $v['day_trade_wechat_unit_price'];
                    $data['week_day_trade_unionpay_unit_price'] += $v['day_trade_unionpay_unit_price'];
                    $data['week_day_store_unit_yield'] += $v['day_store_unit_yield'];
                    $data['week_day_alipay_commision_money'] += $v['day_alipay_commision_money'];
                    $data['week_day_wechat_commision_money'] += $v['day_wechat_commision_money'];
                    $data['week_day_alipay_commision_num'] += $v['day_alipay_commision_num'];
                    $data['week_day_wechat_commision_num'] += $v['day_wechat_commision_num'];
                    $data['week_day_new_user_num'] += $v['day_new_user_num'];
                    $data['week_day_new_alipay_fans_num'] += $v['day_new_alipay_fans_num'];
                    $data['week_day_new_wechat_fans_num'] += $v['day_new_wechat_fans_num'];
                    $data['week_day_new_member_num'] += $v['day_new_member_num'];
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }else{
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无此数据');
            }
        } catch (Exception $e) {
            $result['data'] = $data;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 服务商15日交易额和订单量
     * @param unknown $agent_id
     * @throws Exception
     * @return string
     */
    public function getAgentHalfMonthTrading($agent_id){
        $result = array();
        $data =array();
        try {
            //参数验证
            if (!isset($agent_id) && empty($agent_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少agent_id参数');
            }
            //查询15日数据
            $criteria = new CDbcriteria();
            $start_time = date("Y-m-d",strtotime("-15 day"))." 00:00:00";
            $end_time = date("Y-m-d",strtotime("-1 day"))." 23:59:59";
            $criteria -> addCondition('agent_id = :agent_id');
            $criteria -> addCondition('date >= :start_time');
            $criteria -> addCondition('date <= :end_time');
            $criteria -> addCondition('flag = :flag');
            $criteria -> order = "date asc";
            $criteria -> params = array(
                ':agent_id' => $agent_id,
                ':start_time' => $start_time,
                ':end_time' => $end_time,
                ':flag' => FLAG_NO
            );
            $model = AStatistics::model()->findAll($criteria);
            if (!empty($model)){
                foreach ($model as $k => $v){
                    $data[$k]['date'] = $v['date'];//日期
                    $data[$k]['new_trade_alipay_money'] = $v['new_trade_alipay_money'];//新增支付宝交易金额
                    $data[$k]['new_trade_wechat_money'] = $v['new_trade_wechat_money'];//新增微信交易金额
                    $data[$k]['new_trade_unionpay_money'] = $v['new_trade_unionpay_money'];//新增银联交易金额
                    $data[$k]['new_trade_alipay_num'] = $v['new_trade_alipay_num'];//新增支付宝交易笔数
                    $data[$k]['new_trade_wechat_num'] = $v['new_trade_wechat_num'];//新增微信交易笔数
                    $data[$k]['new_trade_unionpay_num'] = $v['new_trade_unionpay_num'];//新增银联交易笔数
                    $data[$k]['new_trade_coupon_num'] = $v['new_trade_coupon_num'];//新增券核销笔数
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }else{
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无此数据');
            }
        } catch (Exception $e) {
            $result['data'] = $data;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 获取top商户15日交易额和订单量
     * @param unknown $agent_id
     * @throws Exception
     * @return string
     */
    public function getTopMerchantHalfMonthTrading($agent_id){
        $result = array();
        $data =array();
        try {
            //参数验证
            if (!isset($agent_id) && empty($agent_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少agent_id参数');
            }
            //获取所有商户id
            $criteria = new CDbcriteria();
            $criteria->addCondition('agent_id = :agent_id');
            $gid_res = Agent::model()->findAll(array(
                'select'=>'id',
                'condition'=>"gid like :gid AND flag = :flag",
                'params'=>array(':gid'=>'%/'.$agent_id.'/%',':flag'=>FLAG_NO)
            ));
            $gid_arr = array();
            if (!empty($gid_res)){
                foreach ($gid_res as $v){
                    $gid_arr[] = $v['id'];
                }
                $criteria->addInCondition('agent_id',$gid_arr,'OR');
            }
            $criteria->addCondition('flag = :flag');
            $criteria->params[':agent_id'] = $agent_id;
            $criteria->params[':flag'] = FLAG_NO;
            $model = Merchant::model()->findAll($criteria);
            if (!empty($model)){
                $data_all = array();
                foreach ($model as $k => $v){
                    $data_all[$k]['merchant_id'] = $v['id'];//商户id
                    $data_all[$k]['name'] = $v['name'];//商户名
                    $moth_trade_res = json_decode($this->getHalfMonthMerchantTrading($v['id']),true);
                    if ($moth_trade_res['status'] == ERROR_NONE){
                        $data_all[$k]['month_trade'] = $moth_trade_res['data']['month_new_trade_money'];//商户15日交易额
                        $data_all[$k]['month_order'] = $moth_trade_res['data']['month_new_trade_num'];//商户15日订单量
                    }else{
                        $data_all[$k]['month_trade'] = 0;
                        $data_all[$k]['month_order'] = 0;
                    }
                }
                //数组排序截取top15的数据
                foreach ($data_all as $k => $v){
                    $month_trade[$k] = $v['month_trade'];
                    $month_order[$k] = $v['month_order'];
                }
                $data_all_2 = $data_all;
                array_multisort($month_trade,SORT_DESC,$data_all);
                $data['trade'] = array_slice($data_all, 0,15);
                array_multisort($month_order,SORT_DESC,$data_all_2);
                $data['order'] = array_slice($data_all_2, 0,15);
                
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }else{
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无此数据');
            }
        } catch (Exception $e) {
            $result['data'] = $data;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    private function getHalfMonthMerchantTrading($merchant_id){
        $result = array();
        $data =array();
        try {
             //参数验证
            if (!isset($merchant_id) && empty($merchant_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('缺少agent_id参数');
            }
            //查询15日数据
            $criteria = new CDbcriteria();
            $start_time = date("Y-m-d",strtotime("-15 day"))." 00:00:00";
            $end_time = date("Y-m-d",strtotime("-1 day"))." 23:59:59";
            $criteria -> addCondition('merchant_id = :merchant_id');
            $criteria -> addCondition('date >= :start_time');
            $criteria -> addCondition('date <= :end_time');
            $criteria -> addCondition('flag = :flag');
            $criteria -> order = "date asc";
            $criteria -> params = array(
                ':merchant_id' => $merchant_id,
                ':start_time' => $start_time,
                ':end_time' => $end_time,
                ':flag' => FLAG_NO
            );
            $model = MStatistics::model()->findAll($criteria);
            if (!empty($model)){
                    $data['month_new_trade_money'] = 0;//新增商户数
                    $data['month_new_trade_num'] = 0;//新增营销版商户数
                foreach ($model as $k => $v){
                    $data['month_new_trade_money'] += $v['new_trade_money'];
                    $data['month_new_trade_num'] += $v['new_trade_num'];

                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }else{
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无此数据');
            }
        } catch (Exception $e) {
            $result['data'] = $data;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    /**
     * @param $agent_id
     * @return string
     * 统计商户总数
     */
    public function getMerchantCount($agent_id) {
        $criteria = new CDbcriteria();
        $criteria->addCondition('agent_id = :agent_id');
        $criteria->params[':agent_id'] = $agent_id;
        $gid_res = Agent::model()->findAll(array(
            'select'=>'id',
            'condition'=>"gid like :gid AND flag = :flag",
            'params'=>array(':gid'=>'%/'.$agent_id.'/%',':flag'=>FLAG_NO)
        ));
        $gid_arr = array();
        if (!empty($gid_res)){
            foreach ($gid_res as $v){
                $gid_arr[] = $v['id'];
            }
            $criteria->addInCondition('agent_id',$gid_arr,'OR');
        }
        $criteria->params[':agent_id'] = $agent_id;
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;
        $count = Merchant::model()->count($criteria);
        return json_encode($count);
    }

    /**
     * @param $agent_id
     * @return string
     * 统计下级加盟商总数
     */
    public function getAgentCount($agent_id) {
        $result = Yii::app()->db->createCommand()->SELECT('count(*)')->FROM('wq_agent')->WHERE ('pid = :pid or ppid = :ppid', array('pid' => $agent_id, 'ppid' => $agent_id))->queryScalar();
        return json_encode($result);
    }

    /**
     * @param $agent_id
     * @return string
     * 统计今日新增商户数
     */
    public function getNewMerchantCount($agent_id) {
        $result= Yii::app()->db->createCommand()->SELECT('count(*)')->FROM('wq_merchant')->WHERE('(agent_id = :agent_id or agent_id in (SELECT id FROM wq_agent WHERE pid = :agent_id)) AND to_days(create_time) = to_days(now())', array(':agent_id'=>$agent_id))->queryScalar();
        return json_encode($result);
    }

    /**
     * @param $agent_id
     * @return string
     * 统计今日新增下级加盟商数
     */
    public function getNewAgentCount($agent_id) {
        $result = Yii::app()->db->createCommand()->SELECT('count(*)')->FROM('wq_agent')->WHERE('(pid = :pid or ppid = :ppid) AND to_days(create_time) = to_days(now())', array('pid' => $agent_id, 'ppid' => $agent_id))->queryScalar();
        return json_encode($result);
    }
    

    /**
     * @param $agent_id
     * @return string
     * 统计门店总数
     */
    public function getStoreCount($agent_id) {
        $result = Yii::app()->db->createCommand()->SELECT('count(*)')->FROM('wq_store')->WHERE('merchant_id IN (SELECT id FROM wq_merchant WHERE agent_id IN (SELECT id FROM wq_agent WHERE pid = :agent_id OR agent_id = :agent_id))',array(':agent_id'=>$agent_id))->queryScalar();
        return json_encode($result);
    }
    
    /**
     * @param $agent_id
     * @return string
     * 统计今日新增门店数
     */
    public function getNewStoreCount($agent_id) {
        $result = Yii::app()->db->createCommand()->SELECT('count(*)')->FROM('wq_store')->WHERE('(merchant_id IN (SELECT id FROM wq_merchant WHERE agent_id IN (SELECT id FROM wq_agent WHERE pid = :agent_id OR agent_id = :agent_id)) AND to_days(create_time) = to_days(now()))',array(':agent_id'=>$agent_id))->queryScalar();
        return json_encode($result);
    }
    
    /**
     * TOP10商户交易额（默认7天）
     * @param unknown $agent_id
     * @return string
     */
    public function getTopMerchantTurnover($agent_id) {
        $sql = "select sum(a.order_paymoney) as sum,b.name from `wq_order` as a LEFT JOIN wq_merchant as b on a.merchant_id = b.id where (agent_id IN (SELECT id FROM wq_agent WHERE pid = $agent_id) OR agent_id = $agent_id) and  DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(pay_time) group by a.merchant_id ORDER BY sum(a.order_paymoney) desc LIMIT 10 ";
        $res1 = Yii::app()->db->createCommand($sql);
        $result = $res1->queryAll();        
        return json_encode($result);
    }
    
    /**
     * TOP10商户交易订单（默认7天）
     * @param unknown $agent_id
     * @return string
     */
    public function getTopMerchantOrder($agent_id) {
        $sql = "select count(a.id) as count,b.name from `wq_order` as a LEFT JOIN wq_merchant as b on a.merchant_id = b.id where (agent_id IN (SELECT id FROM wq_agent WHERE pid = $agent_id) OR agent_id = $agent_id) and DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(pay_time) group by a.merchant_id ORDER BY count(a.id) desc LIMIT 10 ";
        $res2 = Yii::app()->db->createCommand($sql);
        $result = $res2->queryAll();
        return json_encode($result);
    }
    
    
}

