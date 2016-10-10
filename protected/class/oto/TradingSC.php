<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');
Yii::import('application.extensions.excel.*');
include('PHPExcel.php');

/**
 * 交易明细类
 */
class TradingSC extends mainClass
{
	/**
	 * 获取交易列表
	 * $operator_id  操作员id
	 * ----关键词搜索------
	 * $time 交易时间
	 * $pay_channel  支付渠道
	 * $order_status  订单状态
	 * $order_no 订单号
	 * $operator  操作员id
	 * --------------------
	 */
	public function getTradingList($operator_id,$time,$pay_channel,$pay_status,$order_status,$order_no,$operator)
	{
		$result = array ();
		try {
			$criteria = new CDbCriteria ();
			if (! empty ( $operator_id )) {
				$operatprModel = Operator::model()->findByPk($operator_id);
				if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
					$criteria->addCondition ( 'operator_id=:operator_id and flag=:flag and store_id=:store_id and order_type=:order_type' );
					$criteria->params = array (
							':operator_id' => $operator_id,
							':flag' => FLAG_NO,
							':store_id' => $operatprModel -> store_id,
							':order_type' => ORDER_TYPE_CASHIER
					);
				}else{//如果操作员是店长(可以查看本店所有的订单记录)
					$criteria->addCondition ( 'flag=:flag and store_id=:store_id and order_type=:order_type' );
					$criteria->params = array (
							':flag' => FLAG_NO,
							':store_id' => $operatprModel -> store_id,
							':order_type' => ORDER_TYPE_CASHIER
					);
				}
			}else{
				$criteria->addCondition ( 'flag=:flag and order_type=:order_type' );
				$criteria->params = array (
						':flag' => FLAG_NO,
						':order_type' => ORDER_TYPE_CASHIER
				);
			}
			
			//交易时间搜索
			if(!empty($time)){
				$arr_time = explode('-', $time);
				$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
				$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
				$criteria -> addBetweenCondition('create_time', $start_time, $end_time);
			}
			//支付渠道搜索
			if(!empty($pay_channel)){
				$criteria -> addCondition('pay_channel=:pay_channel');
				$criteria -> params[':pay_channel'] = $pay_channel;
			}
			//支付状态搜索
			if(!empty($pay_status)){
				$criteria -> addCondition('pay_status=:pay_status');
				$criteria -> params[':pay_status'] = $pay_status;
			}
			//订单状态搜索
			if(!empty($order_status)){
				if ($order_status == ORDER_STATUS_EXIT_REFUND) { //如果是有退款  则包括（退款处理中     已退款     已部分退款）
					$refund = ORDER_STATUS_REFUND;
					$handleRefund = ORDER_STATUS_HANDLE_REFUND;
					$partRefund = ORDER_STATUS_PART_REFUND;
					$criteria->addCondition ( "order_status like '$refund' or order_status like '$handleRefund' or order_status like '$partRefund'" );
				} else {
					$criteria->addCondition ( 'order_status=:order_status' );
					$criteria->params [':order_status'] = $order_status;
				}
			}
			//订单号搜索
			if(!empty($order_no)){
				$criteria -> addCondition('order_no=:order_no');
				$criteria -> params[':order_no'] = $order_no;
			}
			//操作员id搜索
			if(!empty($operator)){
				$criteria -> addCondition('operator_id=:search_operator_id');
				$criteria -> params[':search_operator_id'] = $operator;
			}
			
			$criteria -> order = 'create_time desc';

			$pages = new CPagination(Order::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			$model = Order::model()-> findAll($criteria);
			$data = array ();
			foreach ( $model as $k => $v ) {
				$data ['list'] [$k] ['id'] = $v->id;
				$data ['list'] [$k] ['order_no'] = $v->order_no;//订单号
				$data ['list'] [$k] ['order_status'] = $v->order_status;//订单状态
				$data ['list'] [$k] ['pay_channel'] = $v->pay_channel;//支付渠道
				$data ['list'] [$k] ['pay_status'] = $v->pay_status;//支付状态
				$data ['list'] [$k] ['operator_id'] = $v->operator_id;
				$data ['list'] [$k] ['operator_name'] = !empty($v->operator) ? $v->operator->name.' ('.$v->operator->number.')' : '';//操作员名称
				$data ['list'] [$k] ['pay_time'] = $v->pay_time;
				//账号处理
				$alipay_account = $v->alipay_account; //支付宝账号
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
				$data ['list'] [$k] ['alipay_account'] = $alipay_account;//支付宝账号
				$data ['list'] [$k] ['order_paymoney'] = $v->order_paymoney;//订单金额
				$data ['list'] [$k] ['online_paymoney'] = $v->online_paymoney;//线上支付金额
				$data ['list'] [$k] ['unionpay_paymoney'] = $v->unionpay_paymoney;//银联刷卡支付
				$data ['list'] [$k] ['cash_paymoney'] = $v->cash_paymoney;//现金支付
				$data ['list'] [$k] ['stored_paymoney'] = $v->stored_paymoney;//储值支付
				$order = new OrderSC();
				$data ['list'] [$k] ['receipt_money'] = $order->getReceiptAmount($v['order_no']);
			}

			//统计总数  交易金额
			$criteria1 = new CDbCriteria ();
			$criteria1->select = 'sum(online_paymoney) as online_paymoney, sum(unionpay_paymoney) as unionpay_paymoney, sum(cash_paymoney) as cash_paymoney, sum(stored_paymoney) as stored_paymoney';
			if (! empty ( $operator_id )) {
				$operatprModel = Operator::model()->findByPk($operator_id);
				if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
					$criteria1->addCondition ( 'operator_id=:operator_id and flag=:flag and store_id=:store_id and order_type=:order_type' );
					$criteria1->params = array (
						':operator_id' => $operator_id,
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}else{//如果操作员是店长(可以查看本店所有的订单记录)
					$criteria1->addCondition ( 'flag=:flag and store_id=:store_id and order_type=:order_type' );
					$criteria1->params = array (
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}
			}else{
				$criteria1->addCondition ( 'flag=:flag and order_type=:order_type' );
				$criteria1->params = array (
					':flag' => FLAG_NO,
					':order_type' => ORDER_TYPE_CASHIER
				);
			}
			//交易时间搜索
			if(!empty($time)){
				$arr_time = explode('-', $time);
				$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
				$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
				$criteria1 -> addBetweenCondition('create_time', $start_time, $end_time);
			}else {
				$start_time = date('Y-m-d'.' 00:00:00', time());
				$end_time = date('Y-m-d'.' 23:59:59', time());
				$criteria1 -> addBetweenCondition('create_time', $start_time, $end_time);
			}
			//支付渠道搜索
			if(!empty($pay_channel)){
				$criteria1 -> addCondition('pay_channel=:pay_channel');
				$criteria1 -> params[':pay_channel'] = $pay_channel;
			}
			//支付状态搜索
			if(!empty($pay_status) && $pay_status != ORDER_STATUS_UNPAID){
				$criteria1 -> addCondition('pay_status=:pay_status');
				$criteria1 -> params[':pay_status'] = $pay_status;
			} else {
				$criteria1 -> addCondition('pay_status=:pay_status');
				$criteria1 -> params[':pay_status'] = ORDER_STATUS_PAID;
			}
			//订单状态搜索
			if(!empty($order_status)){
				$criteria1->addCondition ( 'order_status=:order_status' );
				$criteria1->params [':order_status'] = $order_status;
			} else {
				$criteria1->addCondition('order_status = :order_status or order_status = :order_status1 or order_status = :order_status2 or order_status = :order_status3');
				$criteria1->params[':pay_status'] = ORDER_STATUS_PAID;
				$criteria1->params[':order_status'] = ORDER_STATUS_NORMAL;
				$criteria1->params[':order_status1'] = ORDER_STATUS_REFUND;
				$criteria1->params[':order_status2'] = ORDER_STATUS_PART_REFUND;
				$criteria1->params[':order_status3'] = ORDER_STATUS_HANDLE_REFUND;
			}
			//订单号搜索
			if(!empty($order_no)){
				$criteria1 -> addCondition('order_no=:order_no');
				$criteria1 -> params[':order_no'] = $order_no;
			}
			//操作员id搜索
			if(!empty($operator)){
				$criteria1 -> addCondition('operator_id=:search_operator_id');
				$criteria1 -> params[':search_operator_id'] = $operator;
			}
			$model1 = Order::model()->find($criteria1);

			//交易次数
			$criteria2 = new CDbCriteria ();
			if (! empty ( $operator_id )) {
				$operatprModel = Operator::model()->findByPk($operator_id);
				if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
					$criteria2->addCondition ( 'operator_id=:operator_id and flag=:flag and store_id=:store_id and order_type=:order_type' );
					$criteria2->params = array (
						':operator_id' => $operator_id,
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}else{//如果操作员是店长(可以查看本店所有的订单记录)
					$criteria2->addCondition ( 'flag=:flag and store_id=:store_id and order_type=:order_type' );
					$criteria2->params = array (
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}
			}else{
				$criteria2->addCondition ( 'flag=:flag and order_type=:order_type' );
				$criteria2->params = array (
					':flag' => FLAG_NO,
					':order_type' => ORDER_TYPE_CASHIER
				);
			}
			//交易时间搜索
			if(!empty($time)){
				$arr_time = explode('-', $time);
				$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
				$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
				$criteria2 -> addBetweenCondition('create_time', $start_time, $end_time);
			}else {
				$start_time = date('Y-m-d'.' 00:00:00', time());
				$end_time = date('Y-m-d'.' 23:59:59', time());
				$criteria2 -> addBetweenCondition('create_time', $start_time, $end_time);
			}
			//支付渠道搜索
			if(!empty($pay_channel)){
				$criteria2 -> addCondition('pay_channel=:pay_channel');
				$criteria2 -> params[':pay_channel'] = $pay_channel;
			}
			//支付状态搜索
			if(!empty($pay_status) && $pay_status != ORDER_STATUS_UNPAID){
				$criteria2 -> addCondition('pay_status=:pay_status');
				$criteria2 -> params[':pay_status'] = $pay_status;
			} else {
				$criteria2 -> addCondition('pay_status=:pay_status');
				$criteria2 -> params[':pay_status'] = ORDER_STATUS_PAID;
			}
			//订单状态搜索
			if(!empty($order_status)){
				$criteria2->addCondition ( 'order_status=:order_status' );
				$criteria2->params [':order_status'] = $order_status;
			} else {
				$criteria2->addCondition('order_status = :order_status or order_status = :order_status1 or order_status = :order_status2 or order_status = :order_status3');
				$criteria2->params[':pay_status'] = ORDER_STATUS_PAID;
				$criteria2->params[':order_status'] = ORDER_STATUS_NORMAL;
				$criteria2->params[':order_status1'] = ORDER_STATUS_REFUND;
				$criteria2->params[':order_status2'] = ORDER_STATUS_PART_REFUND;
				$criteria2->params[':order_status3'] = ORDER_STATUS_HANDLE_REFUND;
			}
			//订单号搜索
			if(!empty($order_no)){
				$criteria2 -> addCondition('order_no=:order_no');
				$criteria2 -> params[':order_no'] = $order_no;
			}
			//操作员id搜索
			if(!empty($operator)){
				$criteria2 -> addCondition('operator_id=:search_operator_id');
				$criteria2 -> params[':search_operator_id'] = $operator;
			}
			$model2 = Order::model()->count($criteria2);

			//统计总数  退款金额
			$criteria3 = new CDbCriteria ();
			$criteria3->select = 'sum(refund_money) as refund_money';
			if (! empty ( $operator_id )) {
				$operatprModel = Operator::model()->findByPk($operator_id);
				if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
					$criteria3->addCondition ( 'store.operator_id=:operator_id and store.flag=:flag and store.store_id=:store_id and store.order_type=:order_type' );
					$criteria3->params = array (
						':operator_id' => $operator_id,
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}else{//如果操作员是店长(可以查看本店所有的订单记录)
					$criteria3->addCondition ( 'store.flag=:flag and store.store_id=:store_id and store.order_type=:order_type' );
					$criteria3->params = array (
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}
			}else{
				$criteria3->addCondition ( 'store.flag=:flag and store.order_type=:order_type' );
				$criteria3->params = array (
					':flag' => FLAG_NO,
					':order_type' => ORDER_TYPE_CASHIER
				);
			}
			//交易时间搜索
			if(!empty($time)){
				$arr_time = explode('-', $time);
				$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
				$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
				$criteria3 -> addBetweenCondition('store.create_time', $start_time, $end_time);
			}else {
				$start_time = date('Y-m-d'.' 00:00:00', time());
				$end_time = date('Y-m-d'.' 23:59:59', time());
				$criteria3 -> addBetweenCondition('store.create_time', $start_time, $end_time);
			}
			//支付渠道搜索
			if(!empty($pay_channel)){
				$criteria3 -> addCondition('store.pay_channel=:pay_channel');
				$criteria3 -> params[':pay_channel'] = $pay_channel;
			}
			//支付状态搜索
			if(!empty($pay_status) && $pay_status != ORDER_STATUS_UNPAID){
				$criteria3 -> addCondition('store.pay_status=:pay_status');
				$criteria3 -> params[':pay_status'] = $pay_status;
			} else {
				$criteria3 -> addCondition('store.pay_status=:pay_status');
				$criteria3 -> params[':pay_status'] = ORDER_STATUS_PAID;
			}
			//订单状态搜索
			if(!empty($order_status)){
				$criteria3->addCondition ( 'store.order_status=:order_status' );
				$criteria3->params [':order_status'] = $order_status;
			} else {
				$criteria3->addCondition('store.order_status = :order_status or store.order_status = :order_status1 or store.order_status = :order_status2 or store.order_status = :order_status3');
				$criteria3->params[':pay_status'] = ORDER_STATUS_PAID;
				$criteria3->params[':order_status'] = ORDER_STATUS_NORMAL;
				$criteria3->params[':order_status1'] = ORDER_STATUS_REFUND;
				$criteria3->params[':order_status2'] = ORDER_STATUS_PART_REFUND;
				$criteria3->params[':order_status3'] = ORDER_STATUS_HANDLE_REFUND;
			}
			//订单号搜索
			if(!empty($order_no)){
				$criteria3 -> addCondition('store.order_no=:order_no');
				$criteria3 -> params[':order_no'] = $order_no;
			}
			//操作员id搜索
			if(!empty($operator)){
				$criteria3 -> addCondition('store.operator_id=:search_operator_id');
				$criteria3 -> params[':search_operator_id'] = $operator;
			}
			$criteria3->addCondition('t.flag=:flag and t.status!=:status');
			$criteria3->params[':flag'] = FLAG_NO;
			$criteria3->params[':status'] = REFUND_STATUS_FAIL;
			$model3 = RefundRecord::model()->with('store')->find($criteria3);

			//统计退款笔数
			$criteria4 = new CDbCriteria ();
			if (! empty ( $operator_id )) {
				$operatprModel = Operator::model()->findByPk($operator_id);
				if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
					$criteria4->addCondition ( 'store.operator_id=:operator_id and store.flag=:flag and store.store_id=:store_id and store.order_type=:order_type' );
					$criteria4->params = array (
						':operator_id' => $operator_id,
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}else{//如果操作员是店长(可以查看本店所有的订单记录)
					$criteria4->addCondition ( 'store.flag=:flag and store.store_id=:store_id and store.order_type=:order_type' );
					$criteria4->params = array (
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
					);
				}
			}else{
				$criteria4->addCondition ( 'store.flag=:flag and store.order_type=:order_type' );
				$criteria4->params = array (
					':flag' => FLAG_NO,
					':order_type' => ORDER_TYPE_CASHIER
				);
			}
			//交易时间搜索
			if(!empty($time)){
				$arr_time = explode('-', $time);
				$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
				$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
				$criteria4 -> addBetweenCondition('store.create_time', $start_time, $end_time);
			}else {
				$start_time = date('Y-m-d'.' 00:00:00', time());
				$end_time = date('Y-m-d'.' 23:59:59', time());
				$criteria4 -> addBetweenCondition('store.create_time', $start_time, $end_time);
			}
			//支付渠道搜索
			if(!empty($pay_channel)){
				$criteria4 -> addCondition('store.pay_channel=:pay_channel');
				$criteria4 -> params[':pay_channel'] = $pay_channel;
			}
			//支付状态搜索
			if(!empty($pay_status) && $pay_status != ORDER_STATUS_UNPAID){
				$criteria4 -> addCondition('store.pay_status=:pay_status');
				$criteria4 -> params[':pay_status'] = $pay_status;
			} else {
				$criteria4 -> addCondition('store.pay_status=:pay_status');
				$criteria4 -> params[':pay_status'] = ORDER_STATUS_PAID;
			}
			//订单状态搜索
			if(!empty($order_status)){
				$criteria4->addCondition ( 'store.order_status=:order_status' );
				$criteria4->params [':order_status'] = $order_status;
			} else {
				$criteria4->addCondition('store.order_status = :order_status or store.order_status = :order_status1 or store.order_status = :order_status2 or store.order_status = :order_status3');
				$criteria4->params[':pay_status'] = ORDER_STATUS_PAID;
				$criteria4->params[':order_status'] = ORDER_STATUS_NORMAL;
				$criteria4->params[':order_status1'] = ORDER_STATUS_REFUND;
				$criteria4->params[':order_status2'] = ORDER_STATUS_PART_REFUND;
				$criteria4->params[':order_status3'] = ORDER_STATUS_HANDLE_REFUND;
			}
			//订单号搜索
			if(!empty($order_no)){
				$criteria4 -> addCondition('store.order_no=:order_no');
				$criteria4 -> params[':order_no'] = $order_no;
			}
			//操作员id搜索
			if(!empty($operator)){
				$criteria4 -> addCondition('store.operator_id=:search_operator_id');
				$criteria4 -> params[':search_operator_id'] = $operator;
			}
			$criteria4->addCondition('t.flag=:flag and t.status!=:status');
			$criteria4->params[':flag'] = FLAG_NO;
			$criteria4->params[':status'] = REFUND_STATUS_FAIL;
			$model4 = RefundRecord::model()->with('store')->count($criteria4);


			//+ $refund['money_day']; //当天交易金额(已付款的+已退款或部分退款的[只在订单表中])
			$arr['list']['successOrderMoney'] = $model1['online_paymoney'] + $model1['unionpay_paymoney'] + $model1['cash_paymoney'] + $model1['stored_paymoney'];
			$arr['list']['successOrderCount'] = $model2;// + $refund['count_day']; //当天交易笔数(已付款的+已退款或部分退款的[只在订单表中])
			$arr['list']['refundRecordMoney'] = $model3['refund_money']; //当天退款金额(退款记录表中的当天退款记录金额)
			$arr['list']['refundRecordCount'] = $model4; //当天退款笔数(退款记录表中的当天退款记录数)


			$result ['status'] = ERROR_NONE;
			$result ['data'] = $data;
			$result ['arr'] = $arr;
		} catch (Exception $e) {
			$result['status'] = ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		
		return json_encode ( $result );
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
	 * 获取当天订单退款金额以及退款笔数
	 * $model  订单数组
	 * $date   日期默认是今天
	 */
	public function getRefundOrderDay($model,$date)
	{
		$refundOrderDay = array();
		$money_day = 0; //当天订单退款金额
		$count_day = 0; //当天退款笔数
		
		foreach ( $model as $k => $v ) {
			if ($v ['pay_status'] == ORDER_STATUS_PAID && ($v ['order_status'] == ORDER_STATUS_REFUND || $v ['order_status'] == ORDER_STATUS_PART_REFUND || $v ['order_status'] == ORDER_STATUS_HANDLE_REFUND)) {
				if (date ( 'Y-m-d', strtotime ( $v ['pay_time'] ) ) == $date) {
					$money_day = $money_day + $v ['online_paymoney'] + $v ['unionpay_paymoney'] + $v ['cash_paymoney'] + $v ['stored_paymoney'];
					$count_day ++;
				}
			}
		}
		
		$refundOrderDay['money_day'] = $money_day;
		$refundOrderDay['count_day'] = $count_day;
		return $refundOrderDay;
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
	 * 订单删除
	 * $order_id  订单id
	 */
	public function delTrading($order_id)
	{
		$result = array();
		
		$model = Order::model()->findByPk($order_id);
		if(!empty($model)){
			$model -> flag = FLAG_YES;
			$model -> last_time = date('Y-m-d H:i:s');
			if($model -> save()){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败';
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		return json_encode ( $result );
	}
	
	/**
	 * 订单详情
	 * $order_id  订单id
	 */
	public function tradingDetails($order_id)
	{
		$result = array();
		$data = array();
		$model = Order::model()->findByPk($order_id);
		if(!empty($model)){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';
			$data['list']['id'] = $model -> id;
			$data['list']['pay_channel'] = $model -> pay_channel;//支付渠道
			$data['list']['if_use_coupons'] = $model -> if_use_coupons;//是否使用优惠券和红包
			$data['list']['yhq_list'] = $this->getYhqList($model -> id,$model -> if_use_coupons);//获取订单中的优惠券使用列表
			$data['list']['refund_record'] = $this->getRefundRecore($model -> id);//获取订单退款详情
			$data['list']['yhq_cash'] = $this->getYhqCash($model -> id,$model -> if_use_coupons);//获取优惠券优惠金额
			$data['list']['refund_cash'] = $this->getRefundCash($model -> id);//获取退款金额
			$data['list']['refund_count'] = $this->getRefundCount($model -> id);//退款笔数
			$data['list']['points'] = $this->getOrderPoints($model -> id);//获得的积分
			$data['list']['order_no'] = $model -> order_no;//订单号
			$data['list']['order_status'] = $model->order_status;//订单状态
			$data['list']['pay_status'] = $model->pay_status;//支付状态
			$data['list']['merchant_name'] = $this -> getMerchantName($model -> store_id);//商户名称
			$data['list']['user_id'] = $model -> user_id;
			$data['list']['user_account'] = isset($model->user->account)?$model->user->account:'';//用户账户
			$data['list']['operator_id'] = $model -> operator_id;
			$data['list']['operator_name'] = isset($model->operator->name)?$model->operator->name:'';//操作员名称
			$data['list']['order_paymoney'] = $model -> order_paymoney;//订单总金额
			$data['list']['stored_paymoney'] = $model -> stored_paymoney;//储值支付金额
			$data['list']['online_paymoney'] = $model -> online_paymoney;//线上支付金额
			$data['list']['unionpay_paymoney'] = $model -> unionpay_paymoney;//银联刷卡支付
			$data['list']['cash_paymoney'] = $model -> cash_paymoney;//现金支付
			$order = new OrderSC();
			$data['list']['paid_amount'] = $order->getReceiptAmount($model['order_no']);//实收金额
			$data['list']['stored_paymoney'] = $model -> stored_paymoney;//储值支付金额
			$data['list']['hongbao_money'] = $model -> hongbao_money;//红包使用金额
			$data['list']['coupons_money'] = $model -> coupons_money;//优惠券使用金额
			$data['list']['discount_money'] = $model -> discount_money;//折扣券使用金额
			$data['list']['alipay_account'] = $model -> alipay_account;//支付宝账号
			$data['list']['trade_no'] = $model -> trade_no;//交易流水号
			$data['list']['create_time'] = $model -> create_time;
			$data['list']['pay_time'] = $model -> pay_time;
			
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		$result['data'] = $data;
		return json_encode ( $result );
	}
	
	
	/**
	 * 获取订单中的优惠券使用列表
	 * $order_id  订单id
	 * $if_use_coupons   该订单是否使用优惠券和红包
	 */
	public function getYhqList($order_id,$if_use_coupons)
	{
		$listYhq = array();
		if($if_use_coupons == IF_USER_COUPONS_YES){//如果使用了优惠券和红包
			$userCoupons = UserCoupons::model()->findAll('order_id=:order_id and flag=:flag',
					array(':order_id'=>$order_id,':flag'=>FLAG_NO));
			foreach ($userCoupons as $k=>$v){
				$coupons = Coupons::model()->findByPk($v -> coupons_id);
				$listYhq[$k]['name'] = $coupons -> name;//优惠券名称
				$listYhq[$k]['money'] = $v -> money;//优惠券面额
			}
		}
		return $listYhq;
	}
	
	/**
	 * 获取优惠券优惠金额
	 * $order_id  订单id
	 * $if_use_coupons   该订单是否使用优惠券和红包
	 */
	public function getYhqCash($order_id,$if_use_coupons)
	{
		$yhqCash = 0;//定义优惠券优惠金额
// 		if($if_use_coupons == IF_USER_COUPONS_YES){//如果使用了优惠券和红包
// 			$userCoupons = UserCoupons::model()->findAll('order_id=:order_id and flag=:flag',
// 					array(':order_id'=>$order_id,':flag'=>FLAG_NO));
// 			foreach ($userCoupons as $k=>$v){
// 				$coupons = Coupons::model()->findByPk($v -> coupons_id);
// 				$yhqCash = $yhqCash + $v -> money;
// 			}
// 		}

		$model = Order::model()->findByPk($order_id);
		$yhqCash = $model['hongbao_money'] + $model['coupons_money'] + $model['discount_money'] + 0;
		return $yhqCash;
	}
	
	/**
	 * 获取订单退款详情
	 * $order_id  订单id
	 */
	public function getRefundRecore($order_id)
	{
		$refundList = array();
		$refund = RefundRecord::model()->findAll('order_id=:order_id and flag=:flag',
				array(':order_id'=>$order_id,':flag'=>FLAG_NO));
		foreach ($refund as $k=>$v){
			$refundList[$k]['operator_name'] = isset($v -> operator -> name)?$v -> operator -> name:'';//退款操作员
			$refundList[$k]['refund_money'] = $v -> refund_money;//退款金额
			$refundList[$k]['refund_time'] = $v -> refund_time;//退款时间
			$refundList[$k]['operator_admin_name'] = isset($v -> operator_admin -> name)?$v -> operator_admin -> name:'';//授权管理员
		}
		return $refundList;
	}
	
	/**
	 * 获取退款金额
	 */
	public function getRefundCash($order_id)
	{
		$refundCash = 0;//定义退款金额
		$refund = RefundRecord::model()->findAll('order_id=:order_id and flag=:flag',
				array(':order_id'=>$order_id,':flag'=>FLAG_NO));
		foreach ($refund as $k=>$v){
			$refundCash = $refundCash + $v -> refund_money;
		}
		return $refundCash;
	}
	
	/**
	 * 获取退款笔数
	 */
	public function getRefundCount($order_id)
	{
		$refund = RefundRecord::model()->findAll('order_id=:order_id and flag=:flag',
				array(':order_id'=>$order_id,':flag'=>FLAG_NO));
		
		return count($refund);
	}
	
	/**
	 * 获取商户名称
	 * $store_id  门店id
	 */
	public function getMerchantName($store_id)
	{
		$merchantName = '';
		if(!empty($store_id)){
		   $store = Store::model()->findByPk($store_id);
		   if(!empty($store)){
		   	  $merchantName = $store -> merchant -> name ? $store -> merchant -> name : $store -> merchant -> wx_name;
		   }
		}
		return $merchantName;
	}
	
	public function getOrderPoints($order_id) {
		$points = 0;
		$model = Order::model()->findByPk($order_id);
		if ($model) {
			$total = $model['stored_paymoney'] + $model['online_paymoney'] + $model['unionpay_paymoney'] + $model['cash_paymoney'];
			$user_id = $model['user_id'];
			if (!empty($user_id)) {
				$user = User::model()->findByPk($user_id);
				if (!empty($user)) {
					$grade_id = $user['membershipgrade_id'];
					if (!empty($grade_id)) {
						$grade = UserGrade::model()->findByPk($grade_id);
						if (!empty($grade)) {
							$ratio = !empty($grade['points_ratio']) ? $grade['points_ratio'] : 0;
							$points += floor($total * $ratio);
						}
					}
				}
			}
		}
		return $points;
	}
	
	/**
	 * 导出Excel
	 * $operator_id  操作员id
	 *  * ----关键词搜索------
	 * $time 交易时间
	 * $pay_channel  支付渠道
	 * $order_status  订单状态
	 * $order_no 订单号
	 * $operator  操作员id
	 * --------------------
	 */
	public function exportExcel($operator_id,$time,$pay_channel,$pay_status,$order_status,$order_no,$operator)
	{
		$criteria = new CDbCriteria ();
		if (! empty ( $operator_id )) {
			$operatprModel = Operator::model()->findByPk($operator_id);
			if($operatprModel -> role == OPERATOR_ROLE_NORMAL){ //如果操作员是店员（只能查看本店员操作产生的订单记录）
				$criteria->addCondition ( 'operator_id=:operator_id and flag=:flag and store_id=:store_id and order_type=:order_type' );
				$criteria->params = array (
						':operator_id' => $operator_id,
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
				);
			}else{//如果操作员是店长(可以查看本店所有的订单记录)
				$criteria->addCondition ( 'flag=:flag and store_id=:store_id and order_type=:order_type' );
				$criteria->params = array (
						':flag' => FLAG_NO,
						':store_id' => $operatprModel -> store_id,
						':order_type' => ORDER_TYPE_CASHIER
				);
			}
		}else{
			$criteria->addCondition ( 'flag=:flag and order_type=:order_type' );
			$criteria->params = array (
					':flag' => FLAG_NO,
					':order_type' => ORDER_TYPE_CASHIER
			);
		}
			
		//交易时间搜索
		if(!empty($time)){
			$arr_time = explode('-', $time);
			$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
			$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
			$criteria -> addBetweenCondition('create_time', $start_time, $end_time);
		}
		//支付渠道搜索
		if(!empty($pay_channel)){
			$criteria -> addCondition('pay_channel=:pay_channel');
			$criteria -> params[':pay_channel'] = $pay_channel;
		}
		//支付状态搜索
		if(!empty($pay_status)){
			$criteria -> addCondition('pay_status=:pay_status');
			$criteria -> params[':pay_status'] = $pay_status;
		}
		//订单状态搜索
		if(!empty($order_status)){
			if ($order_status == ORDER_STATUS_EXIT_REFUND) { //如果是有退款  则包括（退款处理中     已退款     已部分退款）
				$refund = ORDER_STATUS_REFUND;
				$handleRefund = ORDER_STATUS_HANDLE_REFUND;
				$partRefund = ORDER_STATUS_PART_REFUND;
				$criteria->addCondition ( "order_status like '$refund' or order_status like '$handleRefund' or order_status like '$partRefund'" );
			} else {
				$criteria->addCondition ( 'order_status=:order_status' );
				$criteria->params [':order_status'] = $order_status;
			}
		}
		//订单号搜索
		if(!empty($order_no)){
			$criteria -> addCondition('order_no=:order_no');
			$criteria -> params[':order_no'] = $order_no;
		}
		//操作员id搜索
		if(!empty($operator)){
			$criteria -> addCondition('operator_id=:search_operator_id');
			$criteria -> params[':search_operator_id'] = $operator;
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
			$list[$k]['order_money'] = $v['order_paymoney']; //订单金额
			$order = new OrderSC();
			$info = $order->getReceiptAmount($v['order_no'], TRUE);//订单资金明细
			$list[$k]['pay_money'] = $info['receipt_money']; //实收金额
			$list[$k]['refund_money'] = $info['refund_money']; //退款金额
			$list[$k]['discount_amount'] = $v['coupons_money'] + $v['discount_money'] + $v['merchant_discount_money']; //优惠金额
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
		}
		
		$this->getExcel($list);
		
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
		->setCellValue('D1','优惠金额')
		->setCellValue('E1','退款金额')
		->setCellValue('F1','实收金额')
		->setCellValue('G1','订单状态')
		->setCellValue('H1','交易渠道')
		->setCellValue('I1','操作员')
		->setCellValue('J1','交易时间');
		
		//设置列宽
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->getColumnDimension('A')->setWidth(30);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('C')->setWidth(15);
		$objActSheet->getColumnDimension('D')->setWidth(15);
		$objActSheet->getColumnDimension('E')->setWidth(15);
		$objActSheet->getColumnDimension('F')->setWidth(15);
		$objActSheet->getColumnDimension('G')->setWidth(25);
		$objActSheet->getColumnDimension('H')->setWidth(20);
		$objActSheet->getColumnDimension('I')->setWidth(20);
		$objActSheet->getColumnDimension('J')->setWidth(30);
		//设置样式
		$objActSheet->getStyle('C:F')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00); //货币格式
		//设置sheet名称
		$objActSheet -> setTitle('订单明细');
		
		//数据添加
		$i=2;
		foreach($model as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i,$v['order_no'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i,$v['pay_account'])
			->setCellValue('C'.$i,$v['order_money'])
			->setCellValue('D'.$i,$v['discount_amount'])
			->setCellValue('E'.$i,$v['refund_money'])
			->setCellValue('F'.$i,$v['pay_money'])
			->setCellValue('G'.$i,$v['status'])
			->setCellValue('H'.$i,$v['pay_channel'])
			->setCellValue('I'.$i,$v['operator_name'])
			->setCellValue('J'.$i,$v['pay_time']);
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