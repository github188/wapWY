<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/**
 * 订单类
 *
 */
class OrderC extends mainClass{
	public $page = null;
	/**
	 * 订单列表
	 * @param unknown $merchant_id
	 * @param unknown $operator_id
	 * @param unknown $store_id
	 * @param unknown $pay_channel
	 * @param unknown $order_status
	 * @param unknown $start_time
	 * @param unknown $end_time
	 * @param unknown $keyword
	 * @return string
	 */
	public function getOrderList($merchant_id, $operator_id, $store_id, $pay_channel, $order_status, $start_time, $end_time, $keyword, $pay_status=NULL) {
		$result = array();
		try {
			//参数验证
			//TODO
			$stores = array();
			$store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO
			));
			foreach ($store as $k => $v) {
				$stores[$k] = $v -> id;
			}
			
			$criteria = new CDbCriteria();
			if (!empty($store_id)) {
				$criteria->addCondition('store_id = :store_id');
				$criteria->params[':store_id'] = $store_id;
			}else{
				$criteria->addInCondition('store_id', $stores);
			}
			
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			
			if (!empty($operator_id)) {
				$criteria->addCondition('operator_id = :operator_id');
				$criteria->params[':operator_id'] = $operator_id;
			}

			if (!empty($pay_channel)) {
				$criteria->addCondition('pay_channel = :pay_channel');
				$criteria->params[':pay_channel'] = $pay_channel;
			}
			if (!empty($order_status)) {
				$criteria->addCondition('order_status = :order_status');
				$criteria->params[':order_status'] = $order_status;
			}
			if (!empty($pay_status)) {
				$criteria->addCondition('pay_status = :pay_status');
				$criteria->params[':pay_status'] = $pay_status;
			}
			if (!empty($start_time)) {
				$criteria->addCondition('pay_time >= :start_time');
				$criteria->params[':start_time'] = $start_time.' 00:00:00';
			}
			if (!empty($end_time)) {
				$criteria->addCondition('pay_time <= :end_time');
				$criteria->params[':end_time'] = $end_time.' 23:59:59';
			}
			if (!empty($keyword)) {
				$criteria->addCondition('order_no = :order_no');
				$criteria->params[':order_no'] = $keyword;
			}
			$criteria -> order = 'create_time desc';
			
			$model2 = Order::model()-> findAll($criteria); //过滤分页产生的数据不对
			
			$pages = new CPagination(Order::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$model = Order::model()->findAll($criteria);
			$arr = array();
			if(empty($start_time) && empty($end_time)){
				$success = $this->getSuccessOrderDay($model2,date('Y-m-d'));
				$arr['list']['successOrderMoney'] = $success['money_day']; //+ $refund['money_day']; //当天交易金额(已付款的+已退款或部分退款的[只在订单表中])
				$arr['list']['successOrderCount'] = $success['count_day'];// + $refund['count_day']; //当天交易笔数(已付款的+已退款或部分退款的[只在订单表中])
				$refundRecord = $this->getRefundRecordDay($model2, date('Y-m-d'));
				$arr['list']['refundRecordMoney'] = $refundRecord['money_day']; //当天退款金额(退款记录表中的当天退款记录金额)
				$arr['list']['refundRecordCount'] = $refundRecord['count_day']; //当天退款笔数(退款记录表中的当天退款记录数)
			}else{
				$success = $this->getSuccessOrder($model2);
				$refund = $this->getRefundOrder($model2);
				$arr['list']['successOrderMoney'] = $success['money'];// + $refund['money']; //交易金额
				$arr['list']['successOrderCount'] = $success['count'];// + $refund['count']; //交易笔数
				$refundRecord = $this->getRefundRecord($model2);
				$arr['list']['refundRecordMoney'] = $refundRecord['money']; //退款金额
				$arr['list']['refundRecordCount'] = $refundRecord['count']; //退款笔数
			}
			
		
			//数据封装
			$data = array();
			foreach ($model as $key => $value) {
				$data['list'][$key]['id'] = $value['id']; //id
				$data['list'][$key]['order_no'] = $value['order_no']; //订单号
				//账号处理
				$alipay_account = $value['alipay_account']; //支付宝账号
				if (!empty($alipay_account) && strstr($alipay_account, "*") == false) {
					if (strstr($alipay_account, "@")) { //邮箱账号
						$tmp = substr($alipay_account, 0, 3);
						$tmp .= "***";
						$tmp .= strstr($alipay_account, "@");
					}else { //手机账号
						$tmp = substr($alipay_account, 0, 3);
						$tmp .= "****";
						$tmp .= substr($alipay_account, 7);
					}
					$alipay_account = $tmp;
				}
				$data['list'][$key]['alipay_account'] = $alipay_account; //支付宝账号
				$data['list'][$key]['paymoney'] = $value['order_paymoney']; //订单金额
				$data['list'][$key]['order_status'] = $value['order_status']; //订单状态
				$data['list'][$key]['pay_status'] = $value['pay_status']; //支付状态
				$data['list'][$key]['pay_channel'] = $value['pay_channel']; //交易类型
				//查询操作员信息
				$operator = Operator::model()->findByPk($value['operator_id']);
				$data['list'][$key]['operator_name'] = $operator['name'].' ('.$operator['number'].')'; //操作员编号
				$data['list'][$key]['pay_time'] = $value['pay_time']; //交易时间
			}
			//分页
			//TODO
			$data['page'] = '';
			$result['data'] = $data;
			$result ['arr'] = $arr;
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取当天订单成功交易金额以及交易笔数
	 * $model  订单数组
	 * $date   日期默认是今天
	 */
	public function getSuccessOrderDay($model,$date)
	{
		$successOrderDay = array();
		$money_day = 0; //当天成功交易金额
		$count_day = 0; //当天成功交易笔数
	
		foreach ( $model as $k => $v ) {
			if ($v ['pay_status'] == ORDER_STATUS_PAID && ($v['order_status'] == ORDER_STATUS_NORMAL || $v['order_status'] == ORDER_STATUS_REFUND || $v['order_status'] == ORDER_STATUS_PART_REFUND || $v['order_status'] == ORDER_STATUS_HANDLE_REFUND)) {
				if (date ( 'Y-m-d', strtotime ( $v ['pay_time'] ) ) == $date) { //如果支付日期是今天
					$money_day = $money_day + $v ['online_paymoney'] + $v ['unionpay_paymoney'] + $v ['cash_paymoney'] + $v ['stored_paymoney'];
					$count_day ++;
				}
			}
		}
	
		$successOrderDay['money_day'] = $money_day;
		$successOrderDay['count_day'] = $count_day;
		return $successOrderDay;
	}
	
	/**
	 * 获取退款记录表中的当天订单退款金额以及退款笔数
	 * $model  订单数组
	 * $date   日期默认是今天
	 */
	public function getRefundRecordDay($model,$date)
	{
		$refundRecordDay = array();
		$money_day = 0; //当天订单退款金额
		$count_day = 0; //当天退款笔数
	
		foreach ( $model as $k => $v ) {
			if ($v ['pay_status'] == ORDER_STATUS_PAID && ($v ['order_status'] == ORDER_STATUS_REFUND || $v ['order_status'] == ORDER_STATUS_PART_REFUND || $v ['order_status'] == ORDER_STATUS_HANDLE_REFUND)) {
				$list = RefundRecord::model ()->find ( 'order_id=:order_id and flag=:flag and status!=:status', array (
						':order_id' => $v ['id'],
						':flag' => FLAG_NO,
						':status'=>REFUND_STATUS_FAIL
				) );
				if(!empty($list)){
					if (date ( 'Y-m-d', strtotime ( $list ['refund_time'] ) ) == $date) { // 如果退款日期是今天
						$count_day ++;
					}
				}
					
				$list2 = RefundRecord::model ()->findAll ( 'order_id=:order_id and flag=:flag and status!=:status', array (
						':order_id' => $v ['id'],
						':flag' => FLAG_NO,
						':status'=>REFUND_STATUS_FAIL
				));
				foreach ( $list2 as $key => $val ) {
					if (date ( 'Y-m-d', strtotime ( $val ['refund_time'] ) ) == $date) { // 如果退款日期是今天
						$money_day = $money_day + $val ['refund_money'];
					}
				}
			}
		}
	
		$refundRecordDay['money_day'] = $money_day;
		$refundRecordDay['count_day'] = $count_day;
		return $refundRecordDay;
	}
	
	/**
	 * 获取订单成功交易金额以及交易笔数
	 * $model  订单数组
	 */
	public function getSuccessOrder($model)
	{
		$successOrder = array();
	
		$money = 0; //成功交易金额
		$count = 0; //成功交易笔数
		foreach ( $model as $k => $v ) {
			if($v['pay_status'] == ORDER_STATUS_PAID && ($v['order_status'] == ORDER_STATUS_NORMAL || $v['order_status'] == ORDER_STATUS_REFUND || $v['order_status'] == ORDER_STATUS_PART_REFUND || $v['order_status'] == ORDER_STATUS_HANDLE_REFUND)){
				$money = $money +  $v['online_paymoney'] + $v['unionpay_paymoney'] + $v['cash_paymoney']+$v['stored_paymoney'];
				$count++;
			}
		}
	
		$successOrder['money'] = $money;
		$successOrder['count'] = $count;
		return $successOrder;
	}
	
	/**
	 * 获取订单退款金额以及退款笔数
	 * $model  订单数组
	 */
	public function getRefundOrder($model)
	{
		$refundOrder = array();
	
		$money = 0; //成功交易金额
		$count = 0; //成功交易笔数
		foreach ( $model as $k => $v ) {
			if($v['pay_status'] == ORDER_STATUS_PAID && ($v ['order_status'] == ORDER_STATUS_REFUND || $v ['order_status'] == ORDER_STATUS_PART_REFUND || $v ['order_status'] == ORDER_STATUS_HANDLE_REFUND)){
				$money = $money +  $v['online_paymoney'] + $v['unionpay_paymoney'] + $v['cash_paymoney']+$v['stored_paymoney'];
				$count++;
			}
		}
	
		$refundOrder['money'] = $money;
		$refundOrder['count'] = $count;
		return $refundOrder;
	}
	
	/**
	 * 获取退款记录表中的订单退款金额以及退款笔数
	 * $model  订单数组
	 */
	public function getRefundRecord($model)
	{
		$refundRecordDay = array();
		$money = 0; //订单退款金额
		$count = 0; //退款笔数
	
		foreach ( $model as $k => $v ) {
			if ($v ['pay_status'] == ORDER_STATUS_PAID && ($v ['order_status'] == ORDER_STATUS_REFUND || $v ['order_status'] == ORDER_STATUS_PART_REFUND || $v ['order_status'] == ORDER_STATUS_HANDLE_REFUND)) {
				$list = RefundRecord::model ()->find ( 'order_id=:order_id and flag=:flag and status!=:status', array (
						':order_id' => $v ['id'],
						':flag' => FLAG_NO,
						':status'=>REFUND_STATUS_FAIL
				) );
				//foreach ( $list as $key => $val ) {
				//if ($val ['status'] == REFUND_STATUS_SUCCESS) { //如果退款成功
				if(!empty($list)){
					//$money = $money + $list ['refund_money'] ;
					$count ++;
				}
				//计算退款金额要把所有的退款记录都加上
				$list2 = RefundRecord::model ()->findAll ( 'order_id=:order_id and flag=:flag and status!=:status', array (
						':order_id' => $v ['id'],
						':flag' => FLAG_NO,
						':status'=>REFUND_STATUS_FAIL
				) );
				foreach ( $list2 as $key => $val ) {
					$money = $money + $val ['refund_money'] ;
				}
				//}
				//}
			}
		}
	
		$refundRecordDay['money'] = $money;
		$refundRecordDay['count'] = $count;
		return $refundRecordDay;
	}
	

    /**
     * 导出excel
     */
    public function OrderList($merchant_id, $operator_id, $store_id, $pay_channel, $order_status, $start_time, $end_time, $keyword, $pay_status=NULL) {
        $result = array();
        try {
            //参数验证
            //TODO
            $stores = array();
            $store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            foreach ($store as $k => $v) {
                array_push($stores, $v['id']);
            }

            $criteria = new CDbCriteria();
            $criteria->addInCondition('store_id', $stores);
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            if (!empty($operator_id)) {
                $criteria->addCondition('operator_id = :operator_id');
                $criteria->params[':operator_id'] = $operator_id;
            }
            if (!empty($store_id)) {
                $criteria->addCondition('store_id = :store_id');
                $criteria->params[':store_id'] = $store_id;
            }
            if (!empty($pay_channel)) {
                $criteria->addCondition('pay_channel = :pay_channel');
                $criteria->params[':pay_channel'] = $pay_channel;
            }
            if (!empty($order_status)) {
                $criteria->addCondition('order_status = :order_status');
                $criteria->params[':order_status'] = $order_status;
            }
            if (!empty($pay_status)) {
                $criteria->addCondition('pay_status = :pay_status');
                $criteria->params[':pay_status'] = $pay_status;
            }
            if (!empty($start_time)) {
                $criteria->addCondition('pay_time >= :start_time');
                $criteria->params[':start_time'] = $start_time;
            }
            if (!empty($end_time)) {
                $criteria->addCondition('pay_time <= :end_time');
                $criteria->params[':end_time'] = $end_time;
            }
            if (!empty($keyword)) {
                $criteria->addCondition('order_no = :order_no');
                $criteria->params[':order_no'] = $keyword;
            }
            $criteria -> order = 'create_time desc';
            $model = Order::model()->findAll($criteria);

            //数据封装
            $data = array();
            foreach ($model as $key => $value) {
                $data['list'][$key]['id'] = $value['id']; //id
                $data['list'][$key]['order_no'] = $value['order_no']; //订单号
                //账号处理
                $alipay_account = $value['alipay_account']; //支付宝账号
                if (!empty($alipay_account) && strstr($alipay_account, "*") == false) {
                    if (strstr($alipay_account, "@")) { //邮箱账号
                        $tmp = substr($alipay_account, 0, 3);
                        $tmp .= "***";
                        $tmp .= strstr($alipay_account, "@");
                    }else { //手机账号
                        $tmp = substr($alipay_account, 0, 3);
                        $tmp .= "****";
                        $tmp .= substr($alipay_account, 7);
                    }
                    $alipay_account = $tmp;
                }
                $data['list'][$key]['alipay_account'] = $alipay_account; //支付宝账号
                $data['list'][$key]['paymoney'] = $value['order_paymoney']; //订单金额
                $data['list'][$key]['order_status'] = $value['order_status']; //订单状态
                $data['list'][$key]['pay_status'] = $value['pay_status']; //支付状态
                $data['list'][$key]['pay_channel'] = $value['pay_channel']; //交易类型
                $data['list'][$key]['create_time'] = $value['create_time']; //交易类型
                //查询操作员信息
                $operator = Operator::model()->findByPk($value['operator_id']);
                $data['list'][$key]['operator_name'] = $operator['name'].' ('.$operator['number'].')'; //操作员编号
                if(empty($operator['name'])&&empty($operator['number']))
                {
                    $data['list'][$key]['operator_name']="";
                }
                $data['list'][$key]['pay_time'] = $value['pay_time']; //交易时间
            }
            //分页
            //TODO
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }
    
    /*
     * 计算今日交易总金额、今日支付宝交易金额及笔数、今日微信交易金额及笔数
     * $merchant_id 商户id
     */
    public function countTotalAndAlipayAndWechatToday($merchant_id,$manager_id=''){
    	$result = array();
    	try {
    	    //如果是管理员角色
    	    if(!empty($manager_id)){
    	        $manager = Manager::model() -> findByPk($manager_id);
    	        $store_right = $manager -> store_id;
    	        $storeId = substr($store_right, 1, strlen($store_right) - 2);
    	        $right_arr = explode(',', $storeId);
    	        $criteria = new CDbCriteria();
    	        $criteria->addInCondition('id', $right_arr);
    	        $criteria->addCondition('flag = :flag');
    	        $criteria->params[':flag'] = FLAG_NO;
    	        $store = Store::model() -> findAll($criteria);
    	        
    	    }else{
        		$store = Store::model() -> findAll('merchant_id =:merchant_id and flag = :flag',array(
        				':merchant_id' => $merchant_id,
        		          ':flag' => FLAG_NO
        		));
    	    }
    		
    		$store_id_arr = array();
    		$count = 0;
    		foreach ($store as $k => $v){
    		    $store_id_arr[$count] = $v -> id;
    		    if(!empty($v -> relation_store_id)){
    		        $count ++;
    		        $store_id_arr[$count] = $v -> relation_store_id;
    		    }
    			$count ++;
    		}
    		
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('pay_time >= :time_start and pay_time <= :time_end');
    		$cmd->params[':time_start'] =  date('Y-m-d 00:00:00');
    		$cmd->params[':time_end'] =  date('Y-m-d 23:59:59');
    		$cmd->andWhere('pay_status = :pay_status');
    		$cmd->params[':pay_status'] =  ORDER_STATUS_PAID;
    		
    		//该商户下的门店
    		$cmd->andWhere(array('in','store_id',$store_id_arr));
    		
    		$cmd->from = 'wq_order';
    		
    		$cmd1 = clone $cmd;//总
    		$cmd2 = clone $cmd;//支付宝
    		$cmd3 = clone $cmd;//微信
    		
    		$cmd1 -> select(array('SUM(order_paymoney) AS total'));
    		$list1 = $cmd1->queryRow();
    		
    		$cmd2 -> andWhere('pay_channel =:pay_channel1 or pay_channel =:pay_channel2');
    		$cmd2->params[':pay_channel1'] =  ORDER_PAY_CHANNEL_ALIPAY_SM;
    		$cmd2->params[':pay_channel2'] =  ORDER_PAY_CHANNEL_ALIPAY_TM;
    		$cmd2 -> select(array('SUM(online_paymoney) AS alipay_paymoney','count(*) AS alipay_order_num'));
    		$list2 = $cmd2->queryRow();
    		
    		
    		$cmd3 -> andWhere('pay_channel =:pay_channel1 or pay_channel =:pay_channel2');
    		$cmd3->params[':pay_channel1'] =  ORDER_PAY_CHANNEL_WXPAY_SM;
    		$cmd3->params[':pay_channel2'] =  ORDER_PAY_CHANNEL_WXPAY_TM;
    		$cmd3 -> select(array('SUM(online_paymoney) AS wechat_paymoney','count(*) AS wechat_order_num'));
    		$list3 = $cmd3->queryRow();

    		$result['data']['total_money_count'] = !empty($list1['total'])?$list1['total']:0;
    		$result['data']['alipay_money_count'] = !empty($list2['alipay_paymoney'])?$list2['alipay_paymoney']:0;
    		$result['data']['alipay_num_count'] = !empty($list2['alipay_order_num'])?$list2['alipay_order_num']:0;
    		$result['data']['wechat_money_count'] = !empty($list3['wechat_paymoney'])?$list3['wechat_paymoney']:0;
    		$result['data']['wechat_num_count'] = !empty($list3['wechat_order_num'])?$list3['wechat_order_num']:0;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    		
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 获取实收金额
     * @param unknown $order_no
     * @param string $detailArray
     * @return multitype:number Ambigous <static, unknown, NULL> |number
     */
   	private function getReceiptAmount($order_no, $detailArray = FALSE) {
    	$receipt_money = 0; //实收金额
    	$refund_money = 0; //已退金额
    	$refund_count = 0; //退款笔数
    
    	$model = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
    	if (!empty($model)) {
    		//查询退款记录
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('order_id = :order_id');
    		$criteria->params[':order_id'] = $model['id'];
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('type = :type');
    		$criteria->params[':type'] = REFUND_TYPE_REFUND;
    		$criteria->addCondition('status = :status1 or status = :status2');
    		$criteria->params[':status1'] = REFUND_STATUS_SUCCESS;
    		$criteria->params[':status2'] = REFUND_STATUS_PROCESSING;
    		$refund_record = RefundRecord::model()->findAll($criteria);
    		$record = array();
    		foreach ($refund_record as $k => $v) {
    			if (empty($v)) {
    				continue;
    			}
    			$refund_count ++;
    			$refund_money += $v['refund_money'];
    		}
    
    		//计算实收金额
    		$order_money = $model['order_paymoney']; //订单总金额
    		$coupons_discount = $model['coupons_money']; //优惠券优惠金额
    		$member_discount = $model['discount_money']; //会员优惠
    		$merchant_discount = $model['merchant_discount_money']; //商家优惠
    		$alipay_discount = $model['alipay_discount_money']; //支付宝优惠
    		//实收金额
    		$receipt_money = $order_money - $coupons_discount - $member_discount - $merchant_discount;
    		$receipt_money = $receipt_money - $refund_money; //减去已退金额
    		if ($receipt_money < 0) { //实收金额为负则为零
    			$receipt_money = 0;
    		}
    		if ($model['pay_status'] != ORDER_STATUS_PAID) { //订单不是已支付则为零
    			$receipt_money = 0;
    		}
    	}
    
    	if ($detailArray) {
    		return array(
    				'receipt_money' => $receipt_money,
    				'refund_money' => $refund_money,
    				'refund_count' => $refund_count
    		);
    	}else {
    		return $receipt_money;
    	}
    }
    
    //获取日汇总
    /*
     * $time_start 开始时间
     * $time_end 结束时间
     * $store_id 门店id
     */
    public function getReportFormDay($time_start,$time_end,$store_id,$merchant_id,$manager_id=''){
    	try {
    		//默认七天
    		if(empty($time_start) && empty($time_end)){
    			$time_start = date('y-m-d 00:00:00',strtotime('-7 days'));
    			$time_end = date('y-m-d 23:59:59',strtotime('-1 days'));
    		}
    		$criteria = new CDbCriteria();
    		//时间筛选
    		$criteria->addCondition('date >= :time_start');
    		$criteria->params[':time_start'] = date('y-m-d 00:00:00',strtotime($time_start));
    		$criteria->addCondition('date <= :time_end');
    		$criteria->params[':time_end'] = date('y-m-d 00:00:00',strtotime($time_end));
    		
    		if(!empty($manager_id)){
    		    $manager = Manager::model() -> findByPk($manager_id);
    		    $store_right = $manager -> store_id;
    		    $storeId = substr($store_right, 1, strlen($store_right) - 2);
    		    $right_arr = explode(',', $storeId);
    		}
    		
    		//门店筛选
    		$store_id_arr = array();
    		if(!empty($store_id)){
    			$store_temp = Store::model() -> findByPk($store_id);
    			if(!empty($store_temp -> relation_store_id)){
    				$store_id_arr[0] = $store_id;
    				$store_id_arr[1] = $store_temp -> relation_store_id;
    			}else{
    				$store_id_arr[0] = $store_id;
    			}
    		}else{
    			$store = Store::model() -> findAll('merchant_id =:merchant_id',array(
    					':merchant_id' => $merchant_id
    			));
    			$count = 0;
    			foreach ($store as $k => $v){
    			    if(!empty($manager_id)){
    			        if(in_array($v -> id, $right_arr)){
    			            $store_id_arr[$count] = $v -> id;
    			            if(!empty($v -> relation_store_id)){
    			                $count++;
    			                $store_id_arr[$count] = $v -> relation_store_id;
    			            }
    			        }
    			    }else{
    			        $store_id_arr[$count] = $v -> id;
    			        if(!empty($v -> relation_store_id)){
    			            $count++;
    			            $store_id_arr[$count] = $v -> relation_store_id;
    			        }
    			    }
    				$count ++ ;
    			}
    		}
    		$criteria->addInCondition('store_id', $store_id_arr);
    		$criteria -> order = "date ASC";
    		
    		$report_form_day = ReportFormDay::model() -> findAll($criteria);
    		$data = array('report' => array());
    		
    		for ($i = strtotime($time_start);$i<=strtotime($time_end);$i += 60*60*24){
        		//初始化起始时间的数据，保证图表显示包含这个时间点
        		$data['report'][date('y-m-d',$i)]['total'] = 0;
        		$data['report'][date('y-m-d',$i)]['total_num'] = 0;
        		$data['report'][date('y-m-d',$i)]['alipay'] = 0;
        		$data['report'][date('y-m-d',$i)]['alipay_num'] = 0;
        		$data['report'][date('y-m-d',$i)]['wechat'] = 0;
        		$data['report'][date('y-m-d',$i)]['wechat_num'] = 0;
    	    }
    		
    		foreach ($report_form_day as $k => $v){
    			if(!isset($data['report'][date('y-m-d',strtotime($v -> date))])){
    				$data['report'][date('y-m-d',strtotime($v -> date))]['total'] = 0;
    				$data['report'][date('y-m-d',strtotime($v -> date))]['total_num'] = 0;
    				$data['report'][date('y-m-d',strtotime($v -> date))]['alipay'] = 0;
    				$data['report'][date('y-m-d',strtotime($v -> date))]['alipay_num'] = 0;
    				$data['report'][date('y-m-d',strtotime($v -> date))]['wechat'] = 0;
    				$data['report'][date('y-m-d',strtotime($v -> date))]['wechat_num'] = 0;
    			}	
    			$data['report'][date('y-m-d',strtotime($v -> date))]['total'] += $v -> total_money;
    			$data['report'][date('y-m-d',strtotime($v -> date))]['total_num'] += $v -> total_num;
    			$data['report'][date('y-m-d',strtotime($v -> date))]['alipay'] += $v -> alipay_money;
    			$data['report'][date('y-m-d',strtotime($v -> date))]['alipay_num'] += $v -> alipay_num;
    			$data['report'][date('y-m-d',strtotime($v -> date))]['wechat'] += $v -> wechat_money;
    			$data['report'][date('y-m-d',strtotime($v -> date))]['wechat_num'] += $v -> wechat_num;
    		}

    		
//     		//今日数据
//     		if(time() < strtotime($time_end)){
//     			$criteria = new CDbCriteria();
//     			//今日
//     			$criteria->addCondition('pay_time >= :time_start');
//     			$criteria->params[':time_start'] = date('Y-m-d 00:00:00');
//     			$criteria->addCondition('pay_time <= :time_end');
//     			$criteria->params[':time_end'] = date('Y-m-d 23:59:59');
//     			//已支付
//     			$criteria->addCondition('pay_status = :pay_status');
//     			$criteria->params[':pay_status'] = ORDER_STATUS_PAID;
    			
//     			//门店筛选
//     			if(!empty($store_id)){
//     				$criteria->addCondition('store_id = :store_id');
//     				$criteria->params[':store_id'] = $store_id;
//     			}else{
//     				$store = Store::model() -> findAll('merchant_id =:merchant_id',array(
//     						':merchant_id' => $merchant_id
//     				));
//     				$store_id_arr = array();
//     				$count = 0;
//     				foreach ($store as $k => $v){
//     					$store_id_arr[$count] = $v -> id;
//     					if(!empty($v -> relation_store_id)){
//     						$count++;
//     						$store_id_arr[$count] = $v -> relation_store_id;
//     					}
//     					$count ++ ;
//     				}
//     				$criteria->addInCondition('store_id', $store_id_arr);
//     			}
    			
    			
//     			$order = Order::model() -> findAll($criteria);
//     			$data['report'][date('y-m-d')]['total'] = 0;
//     			$data['report'][date('y-m-d')]['total_num'] = 0;
//     			$data['report'][date('y-m-d')]['alipay'] = 0;
//     			$data['report'][date('y-m-d')]['alipay_num'] = 0;
//     			$data['report'][date('y-m-d')]['wechat'] = 0;
//     			$data['report'][date('y-m-d')]['wechat_num'] = 0;
//     			foreach ($order as $k => $v){
//     				$receipt_money = $this->getReceiptAmount($v -> order_no);
//     				$data['report'][date('y-m-d')]['total'] += $v -> order_paymoney;
//     				$data['report'][date('y-m-d')]['total_num'] ++;
//     				//支付渠道为支付宝条码或者支付宝扫码
//     				if($v -> pay_channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $v -> pay_channel == ORDER_PAY_CHANNEL_ALIPAY_TM){
//     					//计算支付宝今日交易金额和笔数
//     					$data['report'][date('y-m-d')]['alipay'] += $receipt_money;
//     					$data['report'][date('y-m-d')]['alipay_num'] ++;
//     				}
//     				//支付渠道为微信条码或者微信扫码
//     				if($v -> pay_channel == ORDER_PAY_CHANNEL_WXPAY_SM || $v -> pay_channel == ORDER_PAY_CHANNEL_WXPAY_TM){
//     					//计算微信今日交易金额和笔数
//     					$data['report'][date('y-m-d')]['wechat'] += $receipt_money;
//     					$data['report'][date('y-m-d')]['wechat_num'] ++;
//     				}
//     			}
//     		}
    		$date = array();
    		$total = array();
    		$total_num = array();
    		$alipay = array();
    		$alipay_num = array();
    		$wechat = array();
    		$wechat_num = array();
    		$count = 0;
    		foreach ($data['report'] as $k => $v){
    			$date[$count] = $k;
    			$total[$count] = $v['total'];
    			$total_num[$count] = $v['total_num'];
    			$alipay[$count] = $v['alipay'];
    			$alipay_num[$count] = $v['alipay_num'];
    			$wechat[$count] = $v['wechat'];
    			$wechat_num[$count] = $v['wechat_num'];
    			$count ++;
    		}
    		
    		$result['data']['date'] = $date;
    		$result['data']['total'] = $total;
    		$result['data']['total_num'] = $total_num;
    		$result['data']['alipay'] = $alipay;
    		$result['data']['alipay_num'] = $alipay_num;
    		$result['data']['wechat'] = $wechat;
    		$result['data']['wechat_num'] = $wechat_num;

    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /*
     * 获取门店交易情况
     */
    public function getStoreTrade($time_start,$time_end,$merchant_id,$manager_id=''){
    	$result = array();
    	try {
    	    if(!empty($manager_id)){
    	        $manager = Manager::model() -> findByPk($manager_id);
    	        $store_right = $manager -> store_id;
    	        $storeId = substr($store_right, 1, strlen($store_right) - 2);
    	        $right_arr = explode(',', $storeId);
    	    }
    	    
    		//默认七天
    		if(empty($time_start) && empty($time_end)){
    			$time_start = date('y-m-d 00:00:00',strtotime('-7 days'));
    			$time_end = date('y-m-d 23:59:59',strtotime('-1 days'));
    		}
//     		$criteria = new CDbCriteria();
    		$cmd = Yii::app()->db->createCommand();
    		//时间筛选
//     		$criteria->addCondition('date >= :time_start');
//     		$criteria->params[':time_start'] = date('Y-m-d 00:00:00',strtotime($time_start));
//     		$criteria->addCondition('date <= :time_end');
//     		$criteria->params[':time_end'] = date('Y-m-d 23:59:59',strtotime($time_end));
    		$cmd->andWhere('date >= :time_start');
    		$cmd->params[':time_start'] = date('Y-m-d 00:00:00',strtotime($time_start));
    		$cmd->andWhere('date <= :time_end');
    		$cmd->params[':time_end'] = date('Y-m-d 23:59:59',strtotime($time_end));
    		
    		$store = Store::model() -> findAll('merchant_id =:merchant_id',array(
    				':merchant_id' => $merchant_id
    		));
    		$store_id_arr = array();
    		$count = 0;
    		foreach ($store as $k => $v){
    		    if(!empty($manager_id)){
    		        if(in_array($v -> id, $right_arr)){
    		            $store_id_arr[$count] = $v -> id;
    		            if(!empty($v -> relation_store_id)){
    		                $count ++;
    		                $store_id_arr[$count] = $v -> relation_store_id;
    		            }
    		        }
    		    }else{
    		        $store_id_arr[$count] = $v -> id;
    		        if(!empty($v -> relation_store_id)){
    		            $count ++;
    		            $store_id_arr[$count] = $v -> relation_store_id;
    		        }
    		    }
    			$count++;
    		}
//     		$criteria->addInCondition('store_id', $store_id_arr);
			$cmd->andWhere(array('IN', 'store_id', $store_id_arr));
    		
    		$select = 'store_id';
    		$select .= ', SUM(total_money) AS total_money_sum';
    		$select .= ', SUM(total_num) AS total_num_sum';
    		$select .= ', SUM(wechat_money) AS wechat_money_sum';
    		$select .= ', SUM(wechat_num) AS wechat_num_sum';
    		$select .= ', SUM(alipay_money) AS alipay_money_sum';
    		$select .= ', SUM(alipay_num) AS alipay_num_sum';
    		$select .= ', SUM(cash_money) AS cash_money_sum';
    		$select .= ', SUM(cash_num) AS cash_num_sum';
    		$select .= ', SUM(stored_money) AS stored_money_sum';
    		$select .= ', SUM(stored_num) AS stored_num_sum';
    		$select .= ', SUM(unionpay_money) AS unionpay_money_sum';
    		$select .= ', SUM(unionpay_num) AS unionpay_num_sum';
    		$cmd->select = $select;
    		
    		$cmd->from = 'wq_report_form_day';
    		
    		//深拷贝
    		$cmd1 = clone $cmd; //查询该商户的指定时间段的总交易金额明细及订单数明细
    		$cmd2 = clone $cmd; //查询该商户的指定时间段的交易金额top10的门店的交易金额明细
    		$cmd3 = clone $cmd; //查询该商户的指定时间段的订单数top10的门店的订单数明细
    		
    		//cmd1
    		$report_total = $cmd1->queryRow();
    		
    		//cmd2
    		$cmd2->group = 'store_id';
    		$cmd2->order = 'total_money_sum desc';
    		$cmd2->limit = '10'; //排名前十
    		//查询top10数据
    		$report_money_top10 = $cmd2->queryAll();
    		
    		//cmd3
    		$cmd3->group = 'store_id';
    		$cmd3->order = 'total_num_sum desc';
    		$cmd3->limit = '10'; //排名前十
    		//查询top10数据
    		$report_num_top10 = $cmd3->queryAll();
    		
    		$store_name_by_money = array();
    		$store_name_by_num = array();
    		$total = array();
    		$total_num = array();
    		$alipay = array();
    		$alipay_num = array();
    		$wechat = array();
    		$wechat_num = array();
    		$cash = array();
    		$cash_num = array();
    		$stored = array();
    		$stored_num = array();
    		$unionpay = array();
    		$unionpay_num = array();
    		
    		//设置总交易
    		$store_name_by_money[] = '总交易';
    		$store_name_by_num[] = '总交易';
    		if (!empty($report_total)) {
    			$total[] = $report_total['total_money_sum'];
    			$total_num[] = $report_total['total_num_sum'];
    			$alipay[] = $report_total['alipay_money_sum'];
    			$alipay_num[] = $report_total['alipay_num_sum'];
    			$wechat[] = $report_total['wechat_money_sum'];
    			$wechat_num[] = $report_total['wechat_num_sum'];
    			$cash[] = $report_total['cash_money_sum'];
    			$cash_num[] = $report_total['cash_num_sum'];
    			$stored[] = $report_total['stored_money_sum'];
    			$stored_num[] = $report_total['stored_num_sum'];
    			$unionpay[] = $report_total['unionpay_money_sum'];
    			$unionpay_num[] = $report_total['unionpay_num_sum'];
    		}else {
    			$total[] = 0;
    			$total_num[] = 0;
    			$alipay[] = 0;
    			$alipay_num[] = 0;
    			$wechat[] = 0;
    			$wechat_num[] = 0;
    			$cash[] = 0;
    			$cash_num[] = 0;
    			$stored[] = 0;
    			$stored_num[] = 0;
    			$unionpay[] = 0;
    			$unionpay_num[] = 0;
    		}
    		
    		//交易金额top10
    		foreach ($report_money_top10 as $k => $v){
    			$store = Store::model() -> findByPk($v['store_id']);
    			if(!empty($store -> branch_name)){
    				$store_name_by_money[] = $store -> name.'-'.$store -> branch_name;
    			}else{
    				$store_name_by_money[] = $store -> name;
    			}
    			$total[] = $v['total_money_sum'];
    			$alipay[] = $v['alipay_money_sum'];
    			$wechat[] = $v['wechat_money_sum'];
    			$cash[] = $v['cash_money_sum'];
    			$stored[] = $v['stored_money_sum'];
    			$unionpay[] = $v['unionpay_money_sum'];
    		}
    		//订单数top10
    		foreach ($report_num_top10 as $k => $v){
    			$store = Store::model() -> findByPk($v['store_id']);
    			if(!empty($store -> branch_name)){
    				$store_name_by_num[] = $store -> name.'-'.$store -> branch_name;
    			}else{
    				$store_name_by_num[] = $store -> name;
    			}
    			$total_num[] = $v['total_num_sum'];
    			$alipay_num[] = $v['alipay_num_sum'];
    			$wechat_num[] = $v['wechat_num_sum'];
    			$cash_num[] = $v['cash_num_sum'];
    			$stored_num[] = $v['stored_num_sum'];
    			$unionpay_num[] = $v['unionpay_num_sum'];
    		}
    		//数组反转
    		$store_name_by_money = array_reverse($store_name_by_money);
    		$store_name_by_num = array_reverse($store_name_by_num);
    		$total = array_reverse($total);
    		$total_num = array_reverse($total_num);
    		$alipay = array_reverse($alipay);
    		$alipay_num = array_reverse($alipay_num);
    		$wechat = array_reverse($wechat);
    		$wechat_num = array_reverse($wechat_num);
    		$cash = array_reverse($cash);
    		$cash_num = array_reverse($cash_num);
    		$stored = array_reverse($stored);
    		$stored_num = array_reverse($stored_num);
    		$unionpay = array_reverse($unionpay);
    		$unionpay_num = array_reverse($unionpay_num);
    		
    		$result['data']['store_name_by_money'] = $store_name_by_money;
    		$result['data']['store_name_by_num'] = $store_name_by_num;
    		$result['data']['total'] = $total;
    		$result['data']['total_num'] = $total_num;
    		$result['data']['alipay'] = $alipay;
    		$result['data']['alipay_num'] = $alipay_num;
    		$result['data']['wechat'] = $wechat;
    		$result['data']['wechat_num'] = $wechat_num;
    		$result['data']['cash'] = $cash;
    		$result['data']['cash_num'] = $cash_num;
    		$result['data']['stored'] = $stored;
    		$result['data']['stored_num'] = $stored_num;
    		$result['data']['unionpay'] = $unionpay;
    		$result['data']['unionpay_num'] = $unionpay_num;
    		
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }

//    /**
//     * 计算交易额
//     */
//    public function getSumOrder($merchant_id) {
//        $result = array();
//        try {
//            $store = Store::model()->findAll('merchant_id = :merchant_id', array(
//                ':merchant_id' => $merchant_id,
//            ));
//            $store_id_arr = array();
//            $count = 0;
//            foreach($store as $k => $v) {
//                $store_id_arr[$count] = $v -> id;
//                if (!empty($v -> relation_store_id)) {
//                    $count ++;
//                    $store_id_arr[$count] = $v -> relation_store_id;
//                }
//                $count ++;
//            }
//            $criteria = new CDbCriteria();
//
//            //订单状态，已支付
//            $criteria -> addCondition('pay_status = :pay_status');
//            $criteria -> params['pay_status'] = ORDER_STATUS_PAID;
//
//            //该商户下的门店
//            $criteria -> addInCondition('store_id', $store_id_arr);
//
//            $order = Order::model() -> findAll($criteria);
//            $total_money_count = 0;
//            $alipay_money_count = 0;
//            $alipay_num_count = 0;
//            $wechat_money_count = 0;
//            $wechat_num_count = 0;
//
//            foreach ($order as $k => $v) {
//                $receipt_money = $this -> getCountOrder($v -> order_no);
//                $total_money_count += $v -> order_paymoney;
//
//                //支付宝支付
//                if ($v -> pay_channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $v -> pay_channel == ORDER_PAY_CHANNEL_ALIPAY_TM || $v -> pay_channel == ORDER_PAY_CHANNEL_ALIPAY) {
//                    $alipay_money_count += $receipt_money;
//                    $alipay_num_count ++ ;
//                }
//
//                //微信支付
//                if ($v -> pay_channel == ORDER_PAY_CHANNEL_WXPAY_SM || $v -> pay_channel == ORDER_PAY_CHANNEL_WXPAY_TM || $v -> pay_channel == ORDER_PAY_CHANNEL_WXPAY) {
//                    $wechat_money_count += $receipt_money;
//                    $wechat_num_count ++ ;
//                }
//            }
//            $result['data']['alipay_money_count'] = $alipay_money_count;
//            $result['data']['alipay_num_count'] = $alipay_num_count;
//            $result['data']['wechat_money_count'] = $wechat_money_count;
//            $result['data']['wechat_num_count'] = $wechat_num_count;
//
//            $result['status'] = ERROR_NONE;
//            $result['errMsg'] = '';
//        } catch(Exception $e) {
//            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
//            $result['errMsg'] = $e -> getMessage();
//        }
//
//        return json_encode($result);
//    }
//
//    /**
//     * 统计实收金额
//     */
//    public function getCountOrder($order_no, $detailArray = FALSE) {
//        $receipt_money = 0; //实收金额
//        $refund_money = 0; //已退金额
//        $refund_count = 0; //退款笔数
//
//        $model = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
//        if (!empty($model)) {
//            //查询退款记录
//            $criteria = new CDbCriteria();
//            $criteria->addCondition('order_id = :order_id');
//            $criteria->params[':order_id'] = $model['id'];
//            $criteria->addCondition('flag = :flag');
//            $criteria->params[':flag'] = FLAG_NO;
//            $criteria->addCondition('type = :type');
//            $criteria->params[':type'] = REFUND_TYPE_REFUND;
//            $criteria->addCondition('status = :status1 or status = :status2');
//            $criteria->params[':status1'] = REFUND_STATUS_SUCCESS;
//            $criteria->params[':status2'] = REFUND_STATUS_PROCESSING;
//            $refund_record = RefundRecord::model()->findAll($criteria);
//            $record = array();
//            foreach ($refund_record as $k => $v) {
//                if (empty($v)) {
//                    continue;
//                }
//                $refund_count++;
//                $refund_money += $v['refund_money'];
//            }
//
//            //计算实收金额
//            $order_money = $model['order_paymoney']; //订单总金额
//            $coupons_discount = $model['coupons_money']; //优惠券优惠金额
//            $member_discount = $model['discount_money']; //会员优惠
//            $merchant_discount = $model['merchant_discount_money']; //商家优惠
//            $alipay_discount = $model['alipay_discount_money']; //支付宝优惠
//            //实收金额
//            $receipt_money = $order_money - $coupons_discount - $member_discount - $merchant_discount;
//            $receipt_money = $receipt_money - $refund_money; //减去已退金额
//            if ($receipt_money < 0) { //实收金额为负则为零
//                $receipt_money = 0;
//            }
//            if ($model['pay_status'] != ORDER_STATUS_PAID) { //订单不是已支付则为零
//                $receipt_money = 0;
//            }
//        }
//
//        if ($detailArray) {
//            return array(
//                'receipt_money' => $receipt_money,
//                'refund_money' => $refund_money,
//                'refund_count' => $refund_count
//            );
//        } else {
//            return $receipt_money;
//        }
//    }
}
