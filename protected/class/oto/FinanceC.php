<?php
include_once(dirname(__FILE__).'/../mainClass.php');
Yii::import('application.extensions.excel.*');
include('PHPExcel.php');
/**
 * 财务管理类
 * xyf  2015/12/16
 */
class FinanceC extends mainClass
{
	public $page = null;
	/**
	 * 交易明细
	 * $merchant_id  商户id
	 * $operator_id  操作员id
	 * $pay_channel  支付渠道
	 * $order_status 订单状态
	 * $start_time   支付时间
	 * $end_time     支付时间
	 * $pay_status   支付状态
	 */
	public function getTransactionDetails($merchant_id,$keyword, $operator_id,$store_id, $pay_channel, $order_status, $start_time, $end_time, $pay_status)
	{
		$result = array();
		try {
			$stores = array();
			$store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO
			));
			foreach ($store as $k => $v) {
				$stores[$k] = $v -> id;
			}
			$criteria = new CDbCriteria();
			//$criteria -> addInCondition('store_id', $stores);
			if (!empty($store_id)) {
				$criteria->addCondition('store_id = :store_id');
				$criteria->params[':store_id'] = $store_id;
			}else{
				if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
					$storeId = Yii::app ()->session ['store_id'];
					$right_arr = array();
					if (! empty ( $storeId )) {
						$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
						$right_arr = explode ( ',', $storeId );
					}
					if(!empty($right_arr)){
				     	$criteria->addInCondition('store_id', $right_arr);
					}else{
						$criteria->addInCondition('store_id', $stores);
					}
				}else{
					$criteria->addInCondition('store_id', $stores);
				}
				
			}
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			if (!empty($keyword)) {
				$criteria->addCondition('order_no = :order_no');
				$criteria->params[':order_no'] = $keyword;
			}else{
			if (! empty ( $operator_id )) {
					$criteria->addCondition ( 'operator_id = :operator_id' );
					$criteria->params [':operator_id'] = $operator_id;
				}
				
				if (! empty ( $pay_channel )) {
					$criteria->addCondition ( 'pay_channel = :pay_channel' );
					$criteria->params [':pay_channel'] = $pay_channel;
				}
				if (! empty ( $order_status )) {
					if ($order_status == 'hasRefund') { // 有退款
						$handle_refund = ORDER_STATUS_HANDLE_REFUND;
						$refund = ORDER_STATUS_REFUND;
						$part_refund = ORDER_STATUS_PART_REFUND;
						$criteria->addCondition ( "order_status like '%$handle_refund%' or order_status like '%$refund%' or order_status like '%$part_refund%'" );
					} else {
						$criteria->addCondition ( 'order_status = :order_status' );
						$criteria->params [':order_status'] = $order_status;
					}
				}
				if (! empty ( $pay_status )) {
					$criteria->addCondition ( 'pay_status = :pay_status' );
					$criteria->params [':pay_status'] = $pay_status;
				}
				if (! empty ( $start_time )) {
					$criteria->addCondition ( 'pay_time >= :start_time' );
					$criteria->params [':start_time'] = $start_time . ' 00:00:00';
				}
				if (! empty ( $end_time )) {
					$criteria->addCondition ( 'pay_time <= :end_time' );
					$criteria->params [':end_time'] = $end_time . ' 23:59:59';
				}
				if (empty ( $start_time ) && empty ( $end_time )) { // 默认7天
					$criteria->addCondition ( 'pay_time >= :start_time' );
// 					$criteria->params [':start_time'] = date ( 'Y-m-d 00:00:00' ,strtotime("-3 day"));
					$criteria->params [':start_time'] = date ( 'Y-m-d 00:00:00');
					$criteria->addCondition ( 'pay_time <= :end_time' );
					$criteria->params [':end_time'] = date ( 'Y-m-d 23:59:59' );
				}
			}
			$criteria -> order = 'create_time desc';
			$arr = array();
// 			$res = $this->getSummary($merchant_id, $keyword,$start_time, $end_time, $pay_channel, $operator_id, $store_id,$order_status, $pay_status);
// 			$res = json_decode($res,true);
			//$list = Order::model()->findAll($criteria);
			$trade_data = $this->getTradeCount($merchant_id, $keyword,$start_time, $end_time, $pay_channel, $operator_id, $store_id,$order_status, $pay_status);
            //var_dump($trade_data);exit();
			$arr['list']['total_receipt_money'] = $trade_data['total_receipt_money']; //实收金额
			//var_dump($arr['list']['total_receipt_money']);exit();
			$arr['list']['total_trade_count'] = $trade_data['total_trade_count']; //交易笔数
			//支付宝实收金额以及交易笔数
			$arr['list']['ali_receipt_money'] = $trade_data['alipay_receipt_money'];
			$arr['list']['ali_trade_count'] = $trade_data['alipay_trade_count'];
			//微信实收金额以及交易笔数
			$arr['list']['wx_receipt_money'] = $trade_data['wxpay_receipt_money'];
			$arr['list']['wx_trade_count'] = $trade_data['wxpay_trade_count'];
			//银联实收金额以及交易笔数
			$arr['list']['unionpay_receipt_money'] = $trade_data['unionpay_receipt_money'];
			$arr['list']['unionpay_trade_count'] = $trade_data['unionpay_trade_count'];
			//现金实收金额以及交易笔数
			$arr['list']['cashpay_receipt_money'] = $trade_data['cashpay_receipt_money'];
			$arr['list']['cashpay_trade_count'] = $trade_data['cashpay_trade_count'];
			//储值实收金额以及交易笔数
			$arr['list']['storedpay_receipt_money'] = $trade_data['storedpay_receipt_money'];
			$arr['list']['storedpay_trade_count'] = $trade_data['storedpay_trade_count'];

			//var_dump($arr);exit;
			$pages = new CPagination(Order::model()->count($criteria));

			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$model = Order::model()-> findAll($criteria);//print_r($model);
			//数据封装
			//var_dump($model);exit;
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
				$user_account = isset($value->user->account)?$value->user->account:'';
				if (!empty($user_account) && strstr($user_account, "*") == false) {
						$tmp = substr($user_account, 0, 3);
						$tmp .= "****";
						$tmp .= substr($user_account, 7);
					$user_account = $tmp;
				}
				$data['list'][$key]['ums_card_no'] = $value['ums_card_no']; //银行卡号
				$data['list'][$key]['user_account'] = $user_account; //会员账号
				$data['list'][$key]['alipay_account'] = $alipay_account; //支付宝账号
				$data['list'][$key]['paymoney'] = $value['order_paymoney']; //订单金额
				$data['list'][$key]['receipt_money'] = $this->getReceiptAmount($value['order_no']); //实收金额
				$data['list'][$key]['order_status'] = $value['order_status']; //订单状态
				$data['list'][$key]['pay_status'] = $value['pay_status']; //支付状态
				$data['list'][$key]['pay_channel'] = $value['pay_channel']; //交易类型支付渠道
				//查询操作员信息
				$operator = Operator::model()->findByPk($value['operator_id']);
				$data['list'][$key]['operator_name'] = $operator['name'].' ('.$operator['number'].')'; //操作员编号
				$data['list'][$key]['pay_time'] = $value['pay_time']; //交易时间
			}
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
	 * 获取日汇总
	 * $merchant_id  商户id
	 * $time     交易日期 (年月份)
	 */
	public function getTransactionSummaryDay($merchant_id,$time,$store_id='',$manager_id='')
	{
		$result = array();
		try {
			$arr = array();
			$res = $this->getSummaryDay($merchant_id, $time,$store_id,$manager_id);
			$res = json_decode($res,true);//var_dump($res);exit;
			
			$result ['arr'] = $res;
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息

			
// 			$data = array();
// 			foreach ($list as $k=>$v){
// 				$data['list'][$k]['id'] = $v['id'];
// 				$data['list'][$k]['total_money'] = $v['total_money']; //总金额
// 				$data['list'][$k]['total_num'] = $v['total_num']; //总笔数
// 				$data['list'][$k]['wechat_money'] = $v['wechat_money']; //微信金额
// 				$data['list'][$k]['wechat_num'] = $v['wechat_num']; //微信笔数
// 				$data['list'][$k]['alipay_money'] = $v['alipay_money']; //支付宝总金额
// 				$data['list'][$k]['alipay_num'] = $v['alipay_num']; //支付宝笔数
// 				$data['list'][$k]['cash_money'] = $v['cash_money']; //现金金额
// 				$data['list'][$k]['cash_num'] = $v['cash_num']; //现金笔数
// 				$data['list'][$k]['stored_money'] = $v['stored_money']; //储值金额
// 				$data['list'][$k]['stored_num'] = $v['stored_num']; //储值笔数
// 				$data['list'][$k]['unionpay_money'] = $v['unionpay_money']; //银联金额
// 				$data['list'][$k]['unionpay_num'] = $v['unionpay_num']; //银联笔数
// 				$data['list'][$k]['date'] = $v['date']; //时间
// 			}
// 			$result ['status'] = ERROR_NONE;
// 			$result ['data'] = $data;
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取月汇总
	 * $merchant_id  商户id
	 * $time     交易日期 (年份)
	 */
	public function getTransactionSummaryMonth($merchant_id,$time,$store_id,$manager_id='')
	{
		$result = array();
		try {
			$arr = array();
			$res = $this->getSummaryMonth($merchant_id, $time,$store_id,$manager_id);
			$res = json_decode($res,true);
			//var_dump($res);exit;
				
			$result ['arr'] = $res;
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 获取月份天数
	 * @param unknown $time
	 * @return number
	 */
	public function getDays($time)
	{
		$days = 0;
		if (!empty($time)) {
			if($time == date('Y-m')){
				//$days = date('d')-1;
				$days = date('d',strtotime('-1 day'));
			}else{
			    $days = date('t',strtotime($time));
			}
		}else{ //如果为空    默认这个月
			//$days = date('d')-1;
			$days = date('d',strtotime('-1 day'));
		}
		return $days;
	}
	
	/**
	 * 获取某年的月份数
	 */
	public function getMonths($time)
	{
		$month = 0;
		if (!empty($time)) {
			if($time == date('Y')){
				//$month = date('m')-1;
				//$month = date('m',strtotime('-1 month'));
				$nowMonth = preg_replace('/^0+/','',date('m'));
				if($nowMonth == 1){
					$month = 0;
				}else{
					$month = date('m',strtotime('-1 month'));
				}
			}else{
				$month = 12;
			}
		}else{ //如果为空    默认今年
			//$month = date('m')-1;
			$nowMonth = preg_replace('/^0+/','',date('m'));
			if($nowMonth == 1){ //当前是一月份，就是今年暂无数据
				$month = 0;
			}else{
			   $month = date('m',strtotime('-1 month'));
			}
		}
		return $month;
	}
	

	

	/**
	 * 获得所有二级分组
	 */
	public function getManageMentSecond($merchant_id)
	{
		$merchant = Merchant::model()->findByPk($merchant_id);
		$data = array();
		$model = Management::model()->findAll('merchant_id=:merchant_id and flag=:flag and p_mid is not :p_mid',array(':merchant_id'=>$merchant_id,':flag'=>FLAG_NO,':p_mid'=>null));
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data[$v['id']] = $v['name'];
			}
		}//print_r($data);exit;
		return $data;
	}
	
	/**
	 * 获取分组对应的门店，用于下拉菜单
	 * $management_id    分组id
	 * $merchant_id      商户id
	 */
	public function getStoreDropList($management_id,$merchant_id)
	{
		$stores = array();
		$store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
				':merchant_id' => $merchant_id,
				':flag' => FLAG_NO
		));
		foreach ($store as $k => $v) {
			$stores[$k] = $v -> id;
		}
		
		$criteria = new CDbCriteria();
		if($management_id != 'merchant'){
		    $criteria -> addCondition('management_id=:management_id');
		    $criteria -> params[':management_id'] = $management_id;
		}else{
			$criteria -> addCondition('management_id is :management_id');
			$criteria -> params[':management_id'] = null;
		}
		
		if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
			$storeId = Yii::app ()->session ['store_id'];
			$right_arr = array();
			if (! empty ( $storeId )) {
				$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
				$right_arr = explode ( ',', $storeId );
			}
			if(!empty($right_arr)){
				$criteria->addInCondition('id', $right_arr);
			}else{  //门店权限为空    可以操作所有门店
				$criteria->addInCondition('id', $stores);
			}
		}else{ //商户可以操作所有门店
			$criteria->addInCondition('id', $stores);
		}
		
		$criteria -> addCondition('flag=:flag');
		$criteria -> params[':flag'] = FLAG_NO;
		$criteria -> addCondition('merchant_id=:merchant_id');
		$criteria -> params[':merchant_id'] = $merchant_id;
		$store = Store::model()->findAll($criteria);
		return $store;
	}

	
	/**
	 * 获取订单月汇总数据
	 * $merchant_id  商户id
	 * $time         交易日期 (年份)
	 */
	public function getSummaryMonth($merchant_id, $time,$store_id,$manager_id='')
	{

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
			    if(!empty($manager_id)){
			        $manager = Manager::model() -> findByPk($manager_id);
			        $store_id = $manager -> store_id;
			        $storeId = substr($store_id, 1, strlen($store_id) - 2);
			        $right_arr = explode(',', $storeId);
			        $criteria->addInCondition('store_id', $right_arr);
			    }else{
			        $criteria->addInCondition('store_id', $stores);
			    }
			}
			$criteria->order = 'month desc';
				
			if (!empty($time)) {
				$criteria->addCondition("substr(month,1,4) = :pay_time"); //substr(month,1,4) 取出年份
				$criteria -> params[':pay_time'] = $time;
			}else{ //如果为空    默认今年
				$criteria->addCondition("substr(month,1,4) = :pay_time");
				$criteria -> params[':pay_time'] = date('Y');
			}
				
			//查询
			  $list = ReportFormMonth::model()->findAll($criteria);
			//var_dump($list);exit;
			foreach ($list as $k => $v) {
				$key = date('m',strtotime($v['month'])); //取出月份
				$key = preg_replace('/^0+/','',$key); //去掉前面的0
				
				if(empty($result['data'][$key])){
					$result['data'][$key] = array(
							'total_money' => 0, //交易总金额
							'total_num' => 0, //交易总笔数
							'wechat_money' => 0, //微信金额
							'wechat_num' => 0, //微信笔数
							'alipay_money' => 0, //支付宝总金额
							'alipay_num' => 0, //支付宝笔数
							'cash_money' => 0, //现金金额
							'cash_num' => 0, //现金笔数
							'stored_money' => 0, //储值金额
							'stored_num' => 0, //储值笔数
							'unionpay_money' => 0, //银联金额
							'unionpay_num' => 0, //银联笔数
								
					);
				}
				
				$result['data'][$key]['total_money'] += $v['total_money'];
				$result['data'][$key]['total_num'] += $v['total_num'];
				$result['data'][$key]['wechat_money'] += $v['wechat_money'];
				$result['data'][$key]['wechat_num'] += $v['wechat_num'];
				$result['data'][$key]['alipay_money'] += $v['alipay_money'];
				$result['data'][$key]['alipay_num'] += $v['alipay_num'];
					
				$result['data'][$key]['cash_money'] += $v['cash_money'];
				$result['data'][$key]['cash_num'] += $v['cash_num'];
				$result['data'][$key]['stored_money'] += $v['stored_money'];
				$result['data'][$key]['stored_num'] += $v['stored_num'];
				$result['data'][$key]['unionpay_money'] += $v['unionpay_money'];
				$result['data'][$key]['unionpay_num'] += $v['unionpay_num'];
			}
						
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
			
		return json_encode($result);
		
	}
	
	/**
	 * 获取订单日汇总数据
	 * $merchant_id  商户id
	 * $time     交易日期 (年月份)
	 */
	public function getSummaryDay($merchant_id,$time,$store_id = '',$manager_id = '') {
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
			
			//查询订单记录列表
			$criteria = new CDbCriteria();
			//$criteria -> addInCondition('store_id', $stores);
			if (!empty($store_id)) {
				$criteria->addCondition('store_id = :store_id');
				$criteria->params[':store_id'] = $store_id;
			}else{
			    if(!empty($manager_id)){
			        $manager = Manager::model() -> findByPk($manager_id);
			        $store_id = $manager -> store_id;
			        $storeId = substr($store_id, 1, strlen($store_id) - 2);
			        $right_arr = explode(',', $storeId);
			        $criteria->addInCondition('store_id', $right_arr);
			    }else{
			        $criteria->addInCondition('store_id', $stores);
			    }
			}
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->order = 'date desc';
			
				
				
			if (!empty($time)) {
				$startTime = $time.'-01 00:00:00';
				if($time == date('Y-m')){
					$endTime = date('Y-m-d 23:59:59',strtotime('-1 day'));
				}else{
					$endTime = date('Y-m-t 23:59:59',strtotime($time)); //获取指定月份的最后一天
				}
				$criteria->addCondition('date >= :pay_time1');
				$criteria->params[':pay_time1'] = $startTime;
			
				$criteria->addCondition('date <= :pay_time2');
				$criteria->params[':pay_time2'] = $endTime;
			}else{ //如果为空    默认这个月
				$startTime = date('Y-m-d 00:00:00',strtotime('first day of today')); //本月第一天
				$endTime = date('Y-m-d 23:59:59', strtotime('-1 day')); //获取本月份今天的前一天
			
				$criteria->addCondition('date >= :pay_time1');
				$criteria->params[':pay_time1'] = $startTime;
			
				$criteria->addCondition('date <= :pay_time2');
				$criteria->params[':pay_time2'] = $endTime;
			}
				
			//查询
			$list = ReportFormDay::model()->findAll($criteria);//var_dump($list);
	
			foreach ($list as $k => $v) {
				
				$key = date('d',strtotime($v['date']));
				$key = preg_replace('/^0+/','',$key); //去掉前面的0
				
				if(empty($result['data'][$key])){
					$result['data'][$key] = array(
							'total_money' => 0, //交易总金额
							'total_num' => 0, //交易总笔数
							'wechat_money' => 0, //微信金额
							'wechat_num' => 0, //微信笔数
							'alipay_money' => 0, //支付宝总金额
							'alipay_num' => 0, //支付宝笔数
							'cash_money' => 0, //现金金额
							'cash_num' => 0, //现金笔数
							'stored_money' => 0, //储值金额
							'stored_num' => 0, //储值笔数
							'unionpay_money' => 0, //银联金额
							'unionpay_num' => 0, //银联笔数
							//'refund_num' => 0, //退款笔数
							
					);
				}
				
				$result['data'][$key]['total_money'] += $v['total_money'];
				$result['data'][$key]['total_num'] += $v['total_num'];
				$result['data'][$key]['wechat_money'] += $v['wechat_money'];
				$result['data'][$key]['wechat_num'] += $v['wechat_num'];
				$result['data'][$key]['alipay_money'] += $v['alipay_money'];
				$result['data'][$key]['alipay_num'] += $v['alipay_num'];
					
				$result['data'][$key]['cash_money'] += $v['cash_money'];
				$result['data'][$key]['cash_num'] += $v['cash_num'];
				$result['data'][$key]['stored_money'] += $v['stored_money'];
				$result['data'][$key]['stored_num'] += $v['stored_num'];
				$result['data'][$key]['unionpay_money'] += $v['unionpay_money'];
				$result['data'][$key]['unionpay_num'] += $v['unionpay_num'];				
				//$result['data'][$key]['refund_num'] = $this->getRefundNum($merchant_id,$time,$store_id,$key); //获取退款笔数
			}
			
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
			
		return json_encode($result);
	}
	
	/**
	 * 获取每日退款笔数
	 */
	public function getRefundNumDay($merchant_id,$time,$store_id)
	{
		$criteria = new CDbCriteria();
		if (!empty($time)) {
			$startTime = $time.'-01 00:00:00';
			if($time == date('Y-m')){
				$endTime = date('Y-m-d 23:59:59',strtotime('-1 day'));
			}else{
				$endTime = date('Y-m-t 23:59:59',strtotime($time)); //获取指定月份的最后一天
			}
			$criteria->addCondition('refund_time >= :pay_time1');
			$criteria->params[':pay_time1'] = $startTime;
				
			$criteria->addCondition('refund_time <= :pay_time2');
			$criteria->params[':pay_time2'] = $endTime;
		}else{ //如果为空    默认这个月
			$startTime = date('Y-m-d 00:00:00',strtotime('first day of today')); //本月第一天
			$endTime = date('Y-m-d 23:59:59', strtotime('-1 day')); //获取本月份今天的前一天
				
			$criteria->addCondition('refund_time >= :pay_time1');
			$criteria->params[':pay_time1'] = $startTime;
				
			$criteria->addCondition('refund_time <= :pay_time2');
			$criteria->params[':pay_time2'] = $endTime;
		}
		$refund_recode = RefundRecord::model() -> findAll($criteria);
		$result = array();
		foreach ($refund_recode as $k => $v){
			$key = date('d',strtotime($v['refund_time']));
			$key = preg_replace('/^0+/','',$key); //去掉前面的0
			//if(date('d',strtotime($v['refund_time']))==$day){
				$order = Order::model() -> findByPk($v -> order_id);
				if(empty($result['data'][$key])){
					$result['data'][$key] = array(
							'refund_num' => 0 //退款笔数
					);
				}
				//如果门店id为空
				if(empty($store_id)){
					if($order -> merchant_id == $merchant_id){
						$result['data'][$key]['refund_num']++;
					}
				}else{
					if($order -> store_id == $store_id){
						$result['data'][$key]['refund_num']++;
					}
				}
			//}
		}
		return $result;
	}
	
	/**
	 * 获得每月退款数组
	 */
	public function getRefundNumMonth($merchant_id, $time, $store_id)
	{
		$criteria = new CDbCriteria();
		if (!empty($time)) {
			$criteria->addCondition("substr(refund_time,1,4) = :refund_time"); //substr(month,1,4) 取出年份
			$criteria -> params[':refund_time'] = $time;
		}else{ //如果为空    默认今年
			$criteria->addCondition("substr(refund_time,1,4) = :refund_time");
			$criteria -> params[':refund_time'] = date('Y');
		}
		$refund_recode = RefundRecord::model() -> findAll($criteria);
		$result = array();
		foreach ($refund_recode as $k => $v){
			$key = date('m',strtotime($v['refund_time']));
			$key = preg_replace('/^0+/','',$key); //去掉前面的0
			//if(date('d',strtotime($v['refund_time']))==$day){
			$order = Order::model() -> findByPk($v -> order_id);
			if(empty($result['data'][$key])){
				$result['data'][$key] = array(
						'refund_num' => 0 //退款笔数
				);
			}
			//如果门店id为空
			if(empty($store_id)){
				if($order -> merchant_id == $merchant_id){
					$result['data'][$key]['refund_num']++;
				}
			}else{
				if($order -> store_id == $store_id){
					$result['data'][$key]['refund_num']++;
				}
			}
			//}
		}
		return $result;
	}
	
	/**
	 * 获取订单汇总数据
	 * @param unknown $operator_id
	 * @param unknown $start_date
	 * @param unknown $end_date
	 * @param unknown $select_type  支付渠道
	 * @param unknown $select_operator
	 * @param unknown $order_status
	 * @param unknown $pay_status
	 * @param unknown $keyword
	 * @throws Exception
	 * @return string
	 */
	public function getSummary($merchant_id, $keyword,$start_date, $end_date, $select_type, $select_operator, $store_id,$order_status, $pay_status) {
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
			 
			//查询订单记录列表
			$criteria = new CDbCriteria();
			//$criteria -> addInCondition('store_id', $stores);
			if (!empty($store_id)) {
				$criteria->addCondition('store_id = :store_id');
				$criteria->params[':store_id'] = $store_id;
			}else{
				if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
					$storeId = Yii::app ()->session ['store_id'];
					$right_arr = array();
					if (! empty ( $storeId )) {
						$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
						$right_arr = explode ( ',', $storeId );
					}
					if(!empty($right_arr)){
				     	$criteria->addInCondition('store_id', $right_arr);
					}else{
						$criteria->addInCondition('store_id', $stores);
					}
				}else{
					$criteria->addInCondition('store_id', $stores);
				}
			}
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->order = 'create_time desc';

			if (!empty($start_date)) {
				$criteria->addCondition('pay_time >= :start_time');
				$criteria->params[':start_time'] = $start_date.' 00:00:00';
			}
			if (!empty($end_date)) {
				$criteria->addCondition('pay_time <= :end_time');
				$criteria->params[':end_time'] = $end_date.' 23:59:59';
			}
			if(empty($start_date) && empty($end_date)){ //默认7天
				$criteria->addCondition('pay_time >= :start_time');
// 				$criteria->params[':start_time'] = date('Y-m-d 00:00:00',strtotime("-3 day"));
				$criteria->params[':start_time'] = date('Y-m-d 00:00:00');
				$criteria->addCondition('pay_time <= :end_time');
				$criteria->params[':end_time'] = date('Y-m-d 23:59:59');
			}
			//支付渠道
			if (!empty($select_type)) {
				$criteria->addCondition('pay_channel = :pay_channel');
				$criteria->params[':pay_channel'] = $select_type;
			}
		    if (!empty($order_status)) {
				if($order_status == 'hasRefund'){ //有退款
					$handle_refund = ORDER_STATUS_HANDLE_REFUND;
					$refund = ORDER_STATUS_REFUND;
					$part_refund = ORDER_STATUS_PART_REFUND;
					$criteria -> addCondition("order_status = '$handle_refund' or order_status = '$refund' or order_status = '$part_refund'");
				}else{
				  $criteria->addCondition('order_status = :order_status');
				  $criteria->params[':order_status'] = $order_status;
				}
			}
			if (!empty($pay_status)) {
				$criteria->addCondition('pay_status = :pay_status');
				$criteria->params[':pay_status'] = $pay_status;
			}
			if (!empty($select_operator)) {
				$criteria->addCondition('operator_id = :operator_id');
				$criteria->params[':operator_id'] = $select_operator;
			}
			if (!empty($keyword)) {
				$criteria->addCondition('order_no = :order_no');
				$criteria->params[':order_no'] = $keyword;
			}
			
			//查询
			//$list = Order::model()->findAll($criteria);
		
				$data = array(
						'total_trade_money' => 0, //交易金额
						'total_receipt_money' => 0, //实收金额
						'total_refund_money' => 0, //退款金额
						'total_trade_count' => 0, //交易笔数
						'total_refund_count' => 0, //退款笔数
						'alipay_qrcode_trade_money' => 0, //支付宝扫码交易金额
						'alipay_qrcode_refund_money' => 0, //支付宝扫码退款金额
						'alipay_qrcode_receipt_money' => 0, //支付宝扫码实收金额
						'alipay_qrcode_trade_count' => 0, //支付宝扫码交易笔数
						'alipay_qrcode_refund_count' => 0, //支付宝扫码退款笔数
						'alipay_barcode_trade_money' => 0, //支付宝条码交易金额
						'alipay_barcode_refund_money' => 0, //支付宝条码退款金额
						'alipay_barcode_receipt_money' => 0, //支付宝条码实收金额
						'alipay_barcode_trade_count' => 0, //支付宝条码交易笔数
						'alipay_barcode_refund_count' => 0, //支付宝条码退款笔数
						'wxpay_qrcode_trade_money' => 0, //微信扫码交易金额
						'wxpay_qrcode_refund_money' => 0, //微信扫码退款金额
						'wxpay_qrcode_receipt_money' => 0, //微信扫码实收金额
						'wxpay_qrcode_trade_count' => 0, //微信扫码交易笔数
						'wxpay_qrcode_refund_count' => 0, //微信扫码退款笔数
						'wxpay_barcode_trade_money' => 0, //微信条码交易金额
						'wxpay_barcode_refund_money' => 0, //微信条码退款金额
						'wxpay_barcode_receipt_money' => 0, //微信条码实收金额
						'wxpay_barcode_trade_count' => 0, //微信条码交易笔数
						'wxpay_barcode_refund_count' => 0, //微信条码退款笔数
						'cashpay_trade_money' => 0, //现金支付交易金额
						'cashpay_refund_money' => 0, //现金支付退款金额
						'cashpay_receipt_money' => 0, //现金支付实收金额
						'cashpay_trade_count' => 0, //现金支付交易笔数
						'cashpay_refund_count' => 0, //现金支付退款笔数
						'unionpay_trade_money' => 0, //银联支付交易金额
						'unionpay_refund_money' => 0, //银联支付退款金额
						'unionpay_receipt_money' => 0, //银联支付实收金额
						'unionpay_trade_count' => 0, //银联支付交易笔数
						'unionpay_refund_count' => 0, //银联支付退款笔数
						'storedpay_trade_money' => 0, //储值支付交易金额
						'storedpay_refund_money' => 0, //储值支付退款金额
						'storedpay_receipt_money' => 0, //储值支付实收金额
						'storedpay_trade_count' => 0, //储值支付交易笔数
						'storedpay_refund_count' => 0, //储值支付退款笔数
				);

			foreach ($list as $k => $v) {
				$order_id = $v['id']; //订单id
				$order_no = $v['order_no']; //订单编号
				$operator_id = $v['operator_id']; //操作员
				$order_type = $v['order_type']; //订单类型
				$pay_status = $v['pay_status']; //支付状态
				$order_status = $v['order_status']; //订单状态
				$pay_channel = $v['pay_channel']; //支付方式
				$order_paymoney = $v['order_paymoney']; //订单总金额
				$online_paymoney = $v['online_paymoney']; //线上支付金额
				$unionpay_paymoney = $v['unionpay_paymoney']; //银联支付金额
				$cash_paymoney = $v['cash_paymoney']; //现金支付金额
				$stored_paymoney = $v['stored_paymoney']; //储值支付的金额
				
				//筛选必要订单
				if ($pay_status != ORDER_STATUS_PAID) {
					continue; //未支付的订单不计入汇总
				}
				if ($order_type != ORDER_TYPE_CASHIER) {
					continue; //非收款订单不计入汇总
				}
				
				//计算实收金额和退款金额
				$order_detail = $this->getReceiptAmount($order_no, TRUE);
				$receipt_money = $order_detail['receipt_money']; //实收金额
				$refund_money = $order_detail['refund_money']; //退款金额
				$refund_count = $order_detail['refund_count']; //退款笔数
				switch ($pay_channel) {
					case ORDER_PAY_CHANNEL_ALIPAY_SM:
						$pay_channel_prefix = 'alipay_qrcode';
						break;
					case ORDER_PAY_CHANNEL_ALIPAY_TM:
						$pay_channel_prefix = 'alipay_barcode';
						break;
					case ORDER_PAY_CHANNEL_WXPAY_SM:
						$pay_channel_prefix = 'wxpay_qrcode';
						break;
					case ORDER_PAY_CHANNEL_WXPAY_TM:
						$pay_channel_prefix = 'wxpay_barcode';
						break;
					case ORDER_PAY_CHANNEL_CASH:
						$pay_channel_prefix = 'cashpay';
						break;
					case ORDER_PAY_CHANNEL_UNIONPAY:
						$pay_channel_prefix = 'unionpay';
						break;
					case ORDER_PAY_CHANNEL_STORED:
						$pay_channel_prefix = 'storedpay';
						break;
					default:
						$pay_channel_prefix = '';
						break;
				}
				 
				//不符合指定支付方式的不计入汇总
				if (empty($pay_channel_prefix)) {
					continue;
				}
				
				
				$data[$pay_channel_prefix.'_trade_money'] += $order_paymoney;
				$data[$pay_channel_prefix.'_refund_money'] += $refund_money;
				$data[$pay_channel_prefix.'_receipt_money'] += $receipt_money;
				$data[$pay_channel_prefix.'_trade_count'] += 1;
				$data[$pay_channel_prefix.'_refund_count'] += $refund_count;
				 
				$data['total_trade_money'] += $order_paymoney;
				$data['total_refund_money'] += $refund_money;
				$data['total_receipt_money'] += $receipt_money;
				$data['total_trade_count'] += 1;
				$data['total_refund_count'] += $refund_count;
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
	 * 获取实收金额
	 * @param unknown $order_no
	 * @param string $detailArray
	 * @return multitype:number Ambigous <static, unknown, NULL> |number
	 */
	public function getReceiptAmount($order_no, $detailArray = FALSE) {
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
			if(!empty($refund_record)){
				foreach ($refund_record as $k => $v) {
					$refund_count ++;
					$refund_money += $v['refund_money'];
				}
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
	
	/**
	 * 财务交易统计
	 * @param unknown $merchant_id
	 * @param unknown $keyword
	 * @param unknown $start_date
	 * @param unknown $end_date
	 * @param unknown $select_type
	 * @param unknown $select_operator
	 * @param unknown $store_id
	 * @param unknown $order_status
	 * @param unknown $pay_status
	 * @return multitype:number unknown Ambigous <static, unknown, NULL>
	 */
	private function getTradeCount($merchant_id, $keyword,$start_date, $end_date, $select_type, $select_operator, $store_id,$order_status, $pay_status) {
		
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
		
		//查询订单记录列表
		$cmd = Yii::app()->db->createCommand();
		
		//$criteria -> addInCondition('store_id', $stores);
		if (!empty($store_id)) {
			$cmd->andWhere('t.store_id = :store_id');
			$cmd->params[':store_id'] = $store_id;
		}else{
			if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
				$storeId = Yii::app ()->session ['store_id'];
				$right_arr = array();
				if (! empty ( $storeId )) {
					$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
					$right_arr = explode ( ',', $storeId );
				}
				if(!empty($right_arr)){
					$cmd->andWhere(array('IN', 't.store_id', $right_arr));
				}else{
					$cmd->andWhere(array('IN', 't.store_id', $stores));
				}
			}else{
				$cmd->andWhere(array('IN', 't.store_id', $stores));
			}
		}
		$cmd->andWhere('t.flag = :flag');
		$cmd->params[':flag'] = FLAG_NO;
		
		if (!empty($start_date)) {
			$cmd->andWhere('t.pay_time >= :start_time');
			$cmd->params[':start_time'] = $start_date.' 00:00:00';
		}
		if (!empty($end_date)) {
			$cmd->andWhere('t.pay_time <= :end_time');
			$cmd->params[':end_time'] = $end_date.' 23:59:59';
		}
		if(empty($start_date) && empty($end_date)){ //默认7天
			$cmd->andWhere('t.pay_time >= :start_time');
			$cmd->params[':start_time'] = date('Y-m-d 00:00:00');
			$cmd->andWhere('t.pay_time <= :end_time');
			$cmd->params[':end_time'] = date('Y-m-d 23:59:59');
		}
		//支付渠道
		if (!empty($select_type)) {
			$cmd->andWhere('t.pay_channel = :pay_channel');
			$cmd->params[':pay_channel'] = $select_type;
		}
		if (!empty($order_status)) {
			if($order_status == 'hasRefund'){ //有退款
				$handle_refund = ORDER_STATUS_HANDLE_REFUND;
				$refund = ORDER_STATUS_REFUND;
				$part_refund = ORDER_STATUS_PART_REFUND;
				$cmd -> andWhere("t.order_status = '$handle_refund' or t.order_status = '$refund' or t.order_status = '$part_refund'");
			}else{
				$cmd->andWhere('t.order_status = :order_status');
				$cmd->params[':order_status'] = $order_status;
			}
		}
		if (!empty($pay_status)) {
			$cmd->andWhere('t.pay_status = :pay_status');
			$cmd->params[':pay_status'] = $pay_status;
		}
		if (!empty($select_operator)) {
			$cmd->andWhere('t.operator_id = :operator_id');
			$cmd->params[':operator_id'] = $select_operator;
		}
		if (!empty($keyword)) {
			$cmd->andWhere('t.order_no = :order_no');
			$cmd->params[':order_no'] = $keyword;
		}
		
		//去除未支付和非收银订单
		$cmd->andWhere('t.pay_status = :custom_pay_status');
		$cmd->params[':custom_pay_status'] = ORDER_STATUS_PAID;
		$cmd->andWhere('t.order_type = :custom_order_type');
		$cmd->params[':custom_order_type'] = ORDER_TYPE_CASHIER;
		
		//分组
		$cmd->group = 'pay_channel';
		
		//指定查询表
		$cmd->from = 'wq_order t';
		
		//退款查询
		$cmd2 = clone $cmd; //深拷贝

		//查询计算
		$select1 = 'pay_channel';
		$select1 .= ', SUM(order_paymoney) AS order_sum';
		$select1 .= ', SUM(coupons_money) AS coupons_sum';
		$select1 .= ', SUM(discount_money) AS discount_sum';
		$select1 .= ', SUM(merchant_discount_money) AS m_discount_sum';
		$select1 .= ', COUNT(pay_channel) AS trade_sum';
		//$select1 .= ', SUM(r.refund_money) AS refund_sum';
		$cmd->select = $select1;
		
		//执行sql查询:统计订单金额，交易笔数
		$list1 = $cmd->queryAll();
		
		//查询计算2
		$select2 = 'pay_channel, SUM(r.refund_money) AS refund_sum';
		$cmd2->select = $select2;
		
		//联表
		$join = 'JOIN wq_refund_record r ON t.id = r.order_id';
		$join .= ' AND r.flag = '.FLAG_NO.' AND r.status != '.REFUND_STATUS_FAIL.' AND r.type = '.REFUND_TYPE_REFUND;
		$cmd2->join = $join;
		
		//执行sql查询:统计退款金额
		$list2 = $cmd2->queryAll();
		
		//查询
		//$list = Order::model()->findAll($criteria);
		
		$data = array(
				'total_receipt_money' => 0, //实收金额
				'total_trade_count' => 0, //交易笔数
				'alipay_receipt_money' => 0, //支付宝实收金额
				'alipay_trade_count' => 0, //支付宝交易笔数
				'wxpay_receipt_money' => 0, //微信实收金额
				'wxpay_trade_count' => 0, //微信交易笔数
				'cashpay_receipt_money' => 0, //现金实收金额
				'cashpay_trade_count' => 0, //现金交易笔数
				'storedpay_receipt_money' => 0, //储值实收金额
				'storedpay_trade_count' => 0, //储值交易笔数
				'unionpay_receipt_money' => 0, //银联实收金额
				'unionpay_trade_count' => 0, //银联交易笔数
		);
		
		foreach ($list1 as $k => $v) {
			$pay_channel = $v['pay_channel']; //支付方式
// 			$online_paymoney = $v['online_sum']; //线上支付金额
// 			$unionpay_paymoney = $v['union_sum']; //银联支付金额
// 			$cash_paymoney = $v['cash_sum']; //现金支付金额
// 			$stored_paymoney = $v['stored_sum']; //储值支付的金额
			$order_money = $v['order_sum']; //订单总金额
			$coupons_discount = $v['coupons_sum']; //优惠券优惠金额
			$member_discount = $v['discount_sum']; //会员优惠
			$merchant_discount = $v['m_discount_sum']; //商家优惠
			$trade_count = $v['trade_sum']; //交易笔数
			//$refund_money = $v['refund_sum']; //退款金额
			
			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM && 
				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM && 
				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM && 
				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM && 
				$pay_channel != ORDER_PAY_CHANNEL_STORED && 
				$pay_channel != ORDER_PAY_CHANNEL_CASH && 
				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
				//非统计的支付数据
				continue;
			}
			//订单金额
			//$total_money = $online_paymoney + $unionpay_paymoney + $cash_paymoney + $stored_paymoney;
			//实收金额
			//$receipt_money = $total_money - $refund_money;
			$receipt_money = $order_money - $coupons_discount - $member_discount - $merchant_discount;
			
			//计算实收金额和交易笔数
			switch ($pay_channel) {
				case ORDER_PAY_CHANNEL_ALIPAY_SM:
					$data['alipay_receipt_money'] += $receipt_money;
					$data['alipay_trade_count'] += $trade_count;
					break;
				case ORDER_PAY_CHANNEL_ALIPAY_TM:
					$data['alipay_receipt_money'] += $receipt_money;
					$data['alipay_trade_count'] += $trade_count;
					break;
				case ORDER_PAY_CHANNEL_WXPAY_SM:
					$data['wxpay_receipt_money'] += $receipt_money;
					$data['wxpay_trade_count'] += $trade_count;
					break;
				case ORDER_PAY_CHANNEL_WXPAY_TM:
					$data['wxpay_receipt_money'] += $receipt_money;
					$data['wxpay_trade_count'] += $trade_count;
					break;
				case ORDER_PAY_CHANNEL_CASH:
					$data['cashpay_receipt_money'] += $receipt_money;
					$data['cashpay_trade_count'] += $trade_count;
					break;
				case ORDER_PAY_CHANNEL_UNIONPAY:
					$data['unionpay_receipt_money'] += $receipt_money;
					$data['unionpay_trade_count'] += $trade_count;
					break;
				case ORDER_PAY_CHANNEL_STORED:
					$data['storedpay_receipt_money'] += $receipt_money;
					$data['storedpay_trade_count'] += $trade_count;
					break;
				default:
					break;
			}
			//总交易统计
			//foreach ($data as $k=>$v ) {
			$data['total_receipt_money'] += $receipt_money;
			$data['total_trade_count'] += $trade_count;
			//}
		}
		
		foreach ($list2 as $k => $v) {
			$pay_channel = $v['pay_channel']; //支付方式
			$refund_money = $v['refund_sum']; //退款金额
			
			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
				//非统计的支付数据
				continue;
			}
			
			//计算实收金额和交易笔数
			switch ($pay_channel) {
				case ORDER_PAY_CHANNEL_ALIPAY_SM:
					$data['alipay_receipt_money'] -= $refund_money;
					break;
				case ORDER_PAY_CHANNEL_ALIPAY_TM:
					$data['alipay_receipt_money'] -= $refund_money;
					break;
				case ORDER_PAY_CHANNEL_WXPAY_SM:
					$data['wxpay_receipt_money'] -= $refund_money;
					break;
				case ORDER_PAY_CHANNEL_WXPAY_TM:
					$data['wxpay_receipt_money'] -= $refund_money;
					break;
				case ORDER_PAY_CHANNEL_CASH:
					$data['cashpay_receipt_money'] -= $refund_money;
					break;
				case ORDER_PAY_CHANNEL_UNIONPAY:
					$data['unionpay_receipt_money'] -= $refund_money;
					break;
				case ORDER_PAY_CHANNEL_STORED:
					$data['storedpay_receipt_money'] -= $refund_money;
					break;
				default:
					break;
			}
			//总交易统计
			$data['total_receipt_money'] -= $refund_money;
		}
		
		return $data;
	}
	
	/**
	 * 导出交易明细excel
	 */
	public function exportDetailExcel($merchant_id, $keyword,$operator_id,$store_id, $pay_channel, $order_status, $start_time, $end_time, $pay_status)
	{
		$stores = array();
		$store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
				':merchant_id' => $merchant_id,
				':flag' => FLAG_NO
		));
		foreach ($store as $k => $v) {
			$stores[$k] = $v -> id;
		}
		$criteria = new CDbCriteria();
		//$criteria -> addInCondition('store_id', $stores);
		if (!empty($store_id)) {
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
		}else{
		    if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
					$storeId = Yii::app ()->session ['store_id'];
					$right_arr = array();
					if (! empty ( $storeId )) {
						$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
						$right_arr = explode ( ',', $storeId );
					}
					if(!empty($right_arr)){
				     	$criteria->addInCondition('store_id', $right_arr);
					}else{
						$criteria->addInCondition('store_id', $stores);
					}
				}else{
					$criteria->addInCondition('store_id', $stores);
				}
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
			if($order_status == 'hasRefund'){ //有退款
				$handle_refund = ORDER_STATUS_HANDLE_REFUND;
				$refund = ORDER_STATUS_REFUND;
				$part_refund = ORDER_STATUS_PART_REFUND;
				$criteria -> addCondition("order_status like '%$handle_refund%' or order_status like '%$refund%' or order_status like '%$part_refund%'");
			}else{
				$criteria->addCondition('order_status = :order_status');
				$criteria->params[':order_status'] = $order_status;
			}
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
		if(empty($start_time) && empty($end_time)){ //默认今天
			$criteria->addCondition('pay_time >= :start_time');
			$criteria->params[':start_time'] = date('Y-m-d 00:00:00');
			$criteria->addCondition('pay_time <= :end_time');
			$criteria->params[':end_time'] = date('Y-m-d 23:59:59');
		}
		if (!empty($keyword)) {
			$criteria->addCondition('order_no = :order_no');
			$criteria->params[':order_no'] = $keyword;
		}
		$criteria -> order = 'create_time desc';
		$model = Order::model()-> findAll($criteria);
		
		//显示数据
		$list = array();
		foreach ($model as $k => $v) {
			$list[$k]['order_no'] = $v['order_no'];
			//账号处理
			$alipay_account = $v['alipay_account']; //支付宝账号
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
			$list[$k]['pay_account'] = $alipay_account;
			$order = new OrderSC();
			$list[$k]['paymoney'] = $v['order_paymoney'];
			$list[$k]['receipt_money'] = $this->getReceiptAmount($v['order_no']);
			$list[$k]['status'] = '';
			if ($v['pay_status'] == ORDER_STATUS_PAID && $v['order_status'] == ORDER_STATUS_NORMAL) {
				$list[$k]['status'] = '已付款';
			}
			if ($v['pay_status'] == ORDER_STATUS_UNPAID) {
				$list[$k]['status'] = '待付款';
			}
			if ($v['order_status'] == ORDER_STATUS_REFUND) {
				$list[$k]['status'] = '已退款';
			}
			if ($v['order_status'] == ORDER_STATUS_PART_REFUND) {
				$list[$k]['status'] = '已部分退款';
			}
			if ($v['order_status'] == ORDER_STATUS_REVOKE) {
				$list[$k]['status'] = '已撤销';
			}
			if ($v['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
				$list[$k]['status'] = '退款处理中';
			}
			$list[$k]['pay_channel'] = isset($v['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']]:'';
			$list[$k]['operator_name'] = !empty($v->operator) ? $v->operator->name.' ('.$v->operator->number.')' : '';
			$list[$k]['pay_time'] = $v['pay_time'];
			$list[$k]['store_name'] = !empty($v->store_id)?$v->store->name:'';
		}
		
		$this->getExcel($list);
	}
	
	/**
	 * 导出日汇总excel
	 * $merchant_id   商户id
	 * $time          年月
	 * $store_id      门店id
	 * $day           具体哪天
	 */
	public function exportDayExcel($merchant_id,$time,$store_id,$day)
	{
		$stores = array();
		$store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
				':merchant_id' => $merchant_id,
				':flag' => FLAG_NO
		));
		foreach ($store as $k => $v) {
			$stores[$k] = $v -> id;
		}
		
	
		$criteria = new CDbCriteria();
		//$criteria -> addInCondition('store_id', $stores);
		if (!empty($store_id)) {
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
		}else{
		if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
					$storeId = Yii::app ()->session ['store_id'];
					$right_arr = array();
					if (! empty ( $storeId )) {
						$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
						$right_arr = explode ( ',', $storeId );
					}
					if(!empty($right_arr)){
				     	$criteria->addInCondition('store_id', $right_arr);
					}else{
						$criteria->addInCondition('store_id', $stores);
					}
				}else{
					$criteria->addInCondition('store_id', $stores);
				}
		}
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		
		$criteria->order = 'pay_time desc';
		
			
			
		if (!empty($time)) {
			$startTime = $time.'-01 00:00:00';
			if($time == date('Y-m')){
				$endTime = date('Y-m-d 23:59:59',strtotime('-1 day'));
			}else{
				$endTime = date('Y-m-t 23:59:59',strtotime($time)); //获取指定月份的最后一天
			}
			$criteria->addCondition('pay_time >= :pay_time1');
			$criteria->params[':pay_time1'] = $startTime;
		
			$criteria->addCondition('pay_time <= :pay_time2');
			$criteria->params[':pay_time2'] = $endTime;
		}else{ //如果为空    默认这个月
			$startTime = date('Y-m-d 00:00:00',strtotime('first day of today')); //本月第一天
			$endTime = date('Y-m-d 23:59:59', strtotime('-1 day')); //获取本月份今天的前一天
		
			$criteria->addCondition('pay_time >= :pay_time1');
			$criteria->params[':pay_time1'] = $startTime;
		
			$criteria->addCondition('pay_time <= :pay_time2');
			$criteria->params[':pay_time2'] = $endTime;
		}
		
			
		//查询
		$model = Order::model()->findAll($criteria);
		//显示数据
		$list = array();
		foreach ($model as $k => $v) {
			if(date('d',strtotime($v['pay_time']))==$day){
				$list[$k]['order_no'] = $v['order_no'];
				//账号处理
				$alipay_account = $v['alipay_account']; //支付宝账号
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
				$list[$k]['pay_account'] = $alipay_account;
				$order = new OrderSC();
				$list[$k]['paymoney'] = $v['order_paymoney'];
				$list[$k]['receipt_money'] = $this->getReceiptAmount($v['order_no']);
				$list[$k]['status'] = '';
				if ($v['pay_status'] == ORDER_STATUS_PAID && $v['order_status'] == ORDER_STATUS_NORMAL) {
					$list[$k]['status'] = '已付款';
				}
				if ($v['pay_status'] == ORDER_STATUS_UNPAID) {
					$list[$k]['status'] = '待付款';
				}
				if ($v['order_status'] == ORDER_STATUS_REFUND) {
					$list[$k]['status'] = '已退款';
				}
				if ($v['order_status'] == ORDER_STATUS_PART_REFUND) {
					$list[$k]['status'] = '已部分退款';
				}
				if ($v['order_status'] == ORDER_STATUS_REVOKE) {
					$list[$k]['status'] = '已撤销';
				}
				if ($v['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
					$list[$k]['status'] = '退款处理中';
				}
				$list[$k]['pay_channel'] = isset($v['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']]:'';
				$list[$k]['operator_name'] = !empty($v->operator) ? $v->operator->name.' ('.$v->operator->number.')' : '';
				$list[$k]['pay_time'] = $v['pay_time'];
				$list[$k]['store_name'] = !empty($v->store_id)?$v->store->name:'';
				$list[$k]['pay_type'] = '交易';
			}
		}
		
		//查找退款记录
		$criteria = new CDbCriteria();
		if (!empty($time)) {
			$startTime = $time.'-01 00:00:00';
			if($time == date('Y-m')){
				$endTime = date('Y-m-d 23:59:59',strtotime('-1 day'));
			}else{
				$endTime = date('Y-m-t 23:59:59',strtotime($time)); //获取指定月份的最后一天
			}
			$criteria->addCondition('refund_time >= :refund_time1');
			$criteria->params[':refund_time1'] = $startTime;
		
			$criteria->addCondition('refund_time <= :refund_time2');
			$criteria->params[':refund_time2'] = $endTime;
		}else{ //如果为空    默认这个月
			$startTime = date('Y-m-d 00:00:00',strtotime('first day of today')); //本月第一天
			$endTime = date('Y-m-d 23:59:59', strtotime('-1 day')); //获取本月份今天的前一天
		
			$criteria->addCondition('refund_time >= :refund_time1');
			$criteria->params[':refund_time1'] = $startTime;
		
			$criteria->addCondition('refund_time <= :refund_time2');
			$criteria->params[':refund_time2'] = $endTime;
		}
		$refund_recode = RefundRecord::model() -> findAll($criteria);
		//遍历退款记录
		$data = array();
		foreach ($refund_recode as $k => $v){
		if(date('d',strtotime($v['refund_time']))==$day){
			$order = Order::model() -> findByPk($v -> order_id);
			//如果门店id为空
			if(empty($store_id)){
				//该商户的订单
				if($order -> merchant_id == $merchant_id){
					//赋值
					$data[$k]['order_no'] = $order['order_no'];
					//账号处理
					$alipay_account = $order['alipay_account']; //支付宝账号
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
					$data[$k]['pay_account'] = $alipay_account;
					$data[$k]['paymoney'] = $order['order_paymoney'];
					$data[$k]['receipt_money'] = '-'.$v['refund_money'] ;//$this->getReceiptAmount($order['order_no']);
					$data[$k]['status'] = '';
					if ($order['pay_status'] == ORDER_STATUS_PAID && $order['order_status'] == ORDER_STATUS_NORMAL) {
						$data[$k]['status'] = '已付款';
					}
					if ($order['pay_status'] == ORDER_STATUS_UNPAID) {
						$data[$k]['status'] = '待付款';
					}
					if ($order['order_status'] == ORDER_STATUS_REFUND) {
						$data[$k]['status'] = '已退款';
					}
					if ($order['order_status'] == ORDER_STATUS_PART_REFUND) {
						$data[$k]['status'] = '已部分退款';
					}
					if ($order['order_status'] == ORDER_STATUS_REVOKE) {
						$data[$k]['status'] = '已撤销';
					}
					if ($order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
						$data[$k]['status'] = '退款处理中';
					}
					$data[$k]['pay_channel'] = isset($order['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$order['pay_channel']]:'';
					$data[$k]['operator_name'] = !empty($order->operator) ? $order->operator->name.' ('.$order->operator->number.')' : '';
					$data[$k]['pay_time'] = $v['refund_time']; //退款时间
					$data[$k]['store_name'] = !empty($order->store_id)?$order->store->name:'';
					$data[$k]['pay_type'] = '退款';
				}
			}else{
				//该门店的订单
				if($order -> store_id == $store_id){
					//赋值
					$data[$k]['order_no'] = $order['order_no'];
					//账号处理
					$alipay_account = $order['alipay_account']; //支付宝账号
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
					$data[$k]['pay_account'] = $alipay_account;
					$data[$k]['paymoney'] = $order['order_paymoney'];
					$data[$k]['receipt_money'] = '-'.$v['refund_money'];
					$data[$k]['status'] = '';
					if ($order['pay_status'] == ORDER_STATUS_PAID && $order['order_status'] == ORDER_STATUS_NORMAL) {
						$data[$k]['status'] = '已付款';
					}
					if ($order['pay_status'] == ORDER_STATUS_UNPAID) {
						$data[$k]['status'] = '待付款';
					}
					if ($order['order_status'] == ORDER_STATUS_REFUND) {
						$data[$k]['status'] = '已退款';
					}
					if ($order['order_status'] == ORDER_STATUS_PART_REFUND) {
						$data[$k]['status'] = '已部分退款';
					}
					if ($order['order_status'] == ORDER_STATUS_REVOKE) {
						$data[$k]['status'] = '已撤销';
					}
					if ($order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
						$data[$k]['status'] = '退款处理中';
					}
					$data[$k]['pay_channel'] = isset($order['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$order['pay_channel']]:'';
					$data[$k]['operator_name'] = !empty($order->operator) ? $order->operator->name.' ('.$order->operator->number.')' : '';
					$data[$k]['pay_time'] = $v['refund_time']; //退款时间
					$data[$k]['store_name'] = !empty($order->store_id)?$order->store->name:'';
					$data[$k]['pay_type'] = '退款';
				}
			}
			
		}
	   }
		$dataList = array_merge($list,$data);
		$this->getExcel($dataList);
	}
	
	/**
	 * 导出月汇总excel
	 * $merchant_id   商户id
	 * $time          年份
	 * $store_id      门店id
	 * $day           具体哪月
	 */
	public function exportMonthExcel($merchant_id,$time,$store_id,$month)
	{
		$stores = array();
		$store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
				':merchant_id' => $merchant_id,
				':flag' => FLAG_NO
		));
		foreach ($store as $k => $v) {
			$stores[$k] = $v -> id;
		}
		
		//查询订单记录列表
		$criteria = new CDbCriteria();
		//$criteria -> addInCondition('store_id', $stores);
		if (!empty($store_id)) {
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
		}else{
		if(Yii::app ()->session ['role'] == WQ_ROLE_MANAGER){ //管理员只能操作分配的门店
					$storeId = Yii::app ()->session ['store_id'];
					$right_arr = array();
					if (! empty ( $storeId )) {
						$storeId = substr ( $storeId, 1, strlen ( $storeId ) - 2 );
						$right_arr = explode ( ',', $storeId );
					}
					if(!empty($right_arr)){
				     	$criteria->addInCondition('store_id', $right_arr);
					}else{
						$criteria->addInCondition('store_id', $stores);
					}
				}else{
					$criteria->addInCondition('store_id', $stores);
				}
		}
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$criteria->order = 'pay_time desc';
		
		
		
		if (!empty($time)) {
			$criteria->addCondition("date_format(pay_time,'%Y') = :pay_time");
			$criteria -> params[':pay_time'] = $time;
		}else{ //如果为空    默认今年
			$criteria->addCondition("date_format(pay_time,'%Y') = :pay_time");
			$criteria -> params[':pay_time'] = date('Y');
		}
		
		//查询
		$model = Order::model()->findAll($criteria);
		
		//显示数据
		$list = array();
		foreach ($model as $k => $v) {
			if(date('m',strtotime($v['pay_time']))==$month){
				$list[$k]['order_no'] = $v['order_no'];
				//账号处理
				$alipay_account = $v['alipay_account']; //支付宝账号
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
				$list[$k]['pay_account'] = $alipay_account;
				$order = new OrderSC();
				$list[$k]['paymoney'] = $v['order_paymoney'];
				$list[$k]['receipt_money'] = $this->getReceiptAmount($v['order_no']);
				$list[$k]['status'] = '';
				if ($v['pay_status'] == ORDER_STATUS_PAID && $v['order_status'] == ORDER_STATUS_NORMAL) {
					$list[$k]['status'] = '已付款';
				}
				if ($v['pay_status'] == ORDER_STATUS_UNPAID) {
					$list[$k]['status'] = '待付款';
				}
				if ($v['order_status'] == ORDER_STATUS_REFUND) {
					$list[$k]['status'] = '已退款';
				}
				if ($v['order_status'] == ORDER_STATUS_PART_REFUND) {
					$list[$k]['status'] = '已部分退款';
				}
				if ($v['order_status'] == ORDER_STATUS_REVOKE) {
					$list[$k]['status'] = '已撤销';
				}
				if ($v['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
					$list[$k]['status'] = '退款处理中';
				}
				$list[$k]['pay_channel'] = isset($v['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']]:'';
				$list[$k]['operator_name'] = !empty($v->operator) ? $v->operator->name.' ('.$v->operator->number.')' : '';
				$list[$k]['pay_time'] = $v['pay_time'];
				$list[$k]['store_name'] = !empty($v->store_id)?$v->store->name:'';
				$list[$k]['pay_type'] = '交易';
			}
		}
		
		//查找退款记录
		$criteria = new CDbCriteria();
		if (!empty($time)) {
			$criteria->addCondition("date_format(refund_time,'%Y') = :refund_time");
			$criteria -> params[':refund_time'] = $time;
		}else{ //如果为空    默认今年
			$criteria->addCondition("date_format(refund_time,'%Y') = :refund_time");
			$criteria -> params[':refund_time'] = date('Y');
		}
		$refund_recode = RefundRecord::model() -> findAll($criteria);
		$data = array();
		foreach ($refund_recode as $k=>$v){

			if(date('m',strtotime($v['refund_time']))==$month){
				$order = Order::model() -> findByPk($v -> order_id);
				//如果门店id为空
				if(empty($store_id)){
					//该商户的订单
					if($order -> merchant_id == $merchant_id){
						//赋值
						$data[$k]['order_no'] = $order['order_no'];
						//账号处理
						$alipay_account = $order['alipay_account']; //支付宝账号
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
						$data[$k]['pay_account'] = $alipay_account;
						$data[$k]['paymoney'] = $order['order_paymoney'];
						$data[$k]['receipt_money'] = '-'.$v['refund_money'] ;
						$data[$k]['status'] = '';
						if ($order['pay_status'] == ORDER_STATUS_PAID && $order['order_status'] == ORDER_STATUS_NORMAL) {
							$data[$k]['status'] = '已付款';
						}
						if ($order['pay_status'] == ORDER_STATUS_UNPAID) {
							$data[$k]['status'] = '待付款';
						}
						if ($order['order_status'] == ORDER_STATUS_REFUND) {
							$data[$k]['status'] = '已退款';
						}
						if ($order['order_status'] == ORDER_STATUS_PART_REFUND) {
							$data[$k]['status'] = '已部分退款';
						}
						if ($order['order_status'] == ORDER_STATUS_REVOKE) {
							$data[$k]['status'] = '已撤销';
						}
						if ($order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
							$data[$k]['status'] = '退款处理中';
						}
						$data[$k]['pay_channel'] = isset($order['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$order['pay_channel']]:'';
						$data[$k]['operator_name'] = !empty($order->operator) ? $order->operator->name.' ('.$order->operator->number.')' : '';
						$data[$k]['pay_time'] = $v['refund_time']; //退款时间
						$data[$k]['store_name'] = !empty($order->store_id)?$order->store->name:'';
						$data[$k]['pay_type'] = '退款';
					}
				}else{
					//该门店的订单
					if($order -> store_id == $store_id){
						//赋值
						$data[$k]['order_no'] = $order['order_no'];
						//账号处理
						$alipay_account = $order['alipay_account']; //支付宝账号
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
						$data[$k]['pay_account'] = $alipay_account;
						$data[$k]['paymoney'] = $order['order_paymoney'];
						$data[$k]['receipt_money'] = '-'.$v['refund_money'];
						$data[$k]['status'] = '';
						if ($order['pay_status'] == ORDER_STATUS_PAID && $order['order_status'] == ORDER_STATUS_NORMAL) {
							$data[$k]['status'] = '已付款';
						}
						if ($order['pay_status'] == ORDER_STATUS_UNPAID) {
							$data[$k]['status'] = '待付款';
						}
						if ($order['order_status'] == ORDER_STATUS_REFUND) {
							$data[$k]['status'] = '已退款';
						}
						if ($order['order_status'] == ORDER_STATUS_PART_REFUND) {
							$data[$k]['status'] = '已部分退款';
						}
						if ($order['order_status'] == ORDER_STATUS_REVOKE) {
							$data[$k]['status'] = '已撤销';
						}
						if ($order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
							$data[$k]['status'] = '退款处理中';
						}
						$data[$k]['pay_channel'] = isset($order['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$order['pay_channel']]:'';
						$data[$k]['operator_name'] = !empty($order->operator) ? $order->operator->name.' ('.$order->operator->number.')' : '';
						$data[$k]['pay_time'] = $v['refund_time']; //退款时间
						$data[$k]['store_name'] = !empty($order->store_id)?$order->store->name:'';
						$data[$k]['pay_type'] = '退款';
					}
				}
					
			}
		}
		$dataList = array_merge($list,$data);
		$this->getExcel($dataList);
	}
	
	/**
	 * 获取excel
	 */
	public function getExcel($model)
	{
		include 'PHPExcel/Reader/Excel2007.php';
		include 'PHPExcel/Reader/Excel5.php';
		include 'PHPExcel/IOFactory.php';
	
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','订单号')
		->setCellValue('B1','支付账号')
		->setCellValue('C1','订单金额')
		->setCellValue('D1','实收金额')
		->setCellValue('E1','交易类型')
		->setCellValue('F1','订单状态')
		->setCellValue('G1','交易渠道')
		->setCellValue('H1','操作员')
		->setCellValue('I1','交易时间')
		->setCellValue('J1','所属门店');
	
		//设置列宽
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->getColumnDimension('A')->setWidth(30);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(25);
		$objActSheet->getColumnDimension('E')->setWidth(20);
		$objActSheet->getColumnDimension('F')->setWidth(20);
		$objActSheet->getColumnDimension('G')->setWidth(30);
		$objActSheet->getColumnDimension('H')->setWidth(30);
		//设置sheet名称
		$objActSheet -> setTitle('订单明细');
	
		//数据添加
		$i=2;
		foreach($model as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, $v['order_no'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i,$v['pay_account'])
			->setCellValue('C'.$i,$v['paymoney'])
			->setCellValue('D'.$i,$v['receipt_money'])
			->setCellValue('E'.$i,$v['pay_type'])
			->setCellValue('F'.$i,$v['status'])
			->setCellValue('G'.$i,$v['pay_channel'])
			->setCellValue('H'.$i,$v['operator_name'])
			->setCellValue('I'.$i,$v['pay_time'])
			->setCellValue('J'.$i,$v['store_name']);
			$i++;
		}
	
		$filename = date('YmdHis');//定义文件名
	
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		//$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$this->outPut($filename);
		$objWriter->save("php://output");
	
	}
	
	/**
	 * 到浏览器  浏览器下载excel
	 */
	public function outPut($filename)
	{
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header("Content-Disposition:attachment;filename={$filename}.xls");
		header("Content-Transfer-Encoding:binary");
	}
}