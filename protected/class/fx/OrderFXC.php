<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/*
 * 创建时间：2015-6-18
* 创建人：顾磊
* */
class OrderFXC extends mainClass{
	public $page = null;
	//创建订单
	/*
	 * $agentId 合作商id
	 * $wqproductId 玩券管家版本id
	 * $pay_money 应付金额
	 * $time_limit 有效期限
	 * $order_type 订单类型
	 * $if_tryout 是否试用
	 * $remark 备注
	 * 
	 * */
	public function createGjOrder($agentId,$wqproductId,$pay_money,$time_limit,$order_type,$if_tryout,$remark=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$gjorder = new GjOrder();
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数agentId缺失';
			return json_encode($result);
		}else{
			$gjorder -> agent_id = $agentId;
		}
		
		if(!isset($wqproductId) || empty($wqproductId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数wqproductId缺失';
			return json_encode($result);
		}else{
			$gjorder -> wq_product_id = $wqproductId;
		}
		
		if(!isset($time_limit) || empty($time_limit)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数time_limit缺失';
			return json_encode($result);
		}else{
			$gjorder -> time_limit = $time_limit;
		}
		
		if(!isset($order_type) || empty($order_type)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数order_type缺失';
			return json_encode($result);
		}else{
			$gjorder -> order_type = $order_type;
		}
		
		if(!isset($if_tryout) || empty($if_tryout)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数if_tryout缺失';
			return json_encode($result);
		}else{
			$gjorder -> if_tryout = $if_tryout;
		}
		
		if((!isset($pay_money) || empty($pay_money))&& $pay_money != 0){
			
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数pay_money缺失';
			return json_encode($result);
		}else{
			$agent = Agent::model() -> find('id=:id and flag=:flag',array(
					':id' => $agentId,
					':flag' => FLAG_NO
 			));
			//积分计算
			if(isset($agent -> points) && !empty($agent -> points)){
				if($agent -> points > 0){
					if($agent -> points > floor($pay_money)){
						$agent -> points = $agent -> points - floor($pay_money);
						$gjorder -> points_pay = floor($pay_money);
						$pay_money = $pay_money - floor($pay_money);
					}else{
						$gjorder -> points_pay = $agent -> points;
						$pay_money = $pay_money - $agent -> points;
						$agent -> points = 0;
					}
				}
				Yii::app() -> session['agent_points'] = $agent -> points;
			}
			$gjorder -> pay_money = $pay_money;
			
		}
		
		if(isset($remark) && !empty($remark)){
			$gjorder -> remark = $remark;
		}
		$gjorder -> order_no = $this->getWQOrderNo();
		$gjorder -> pay_status = GJORDER_PAY_STATUS_NUPAID;
		$gjorder -> order_status = GJORDER_STATUS_NORMAL;
		$gjorder -> create_time = new CDbExpression('now()');
		//$transaction= Yii::app ()->db->beginTransaction();
		try {
			if($gjorder -> save()){
				//if($agent -> update()){
					//$transaction->commit();
					$result['status'] = ERROR_NONE;
					$result['data'] = array(
							'id' =>$gjorder -> id,
					);
					return json_encode($result);
				//}//else{
					//$transaction->rollBack();
// 					$result['status'] = ERROR_SAVE_FAIL;
// 					$result['errMsg'] = '合作商更新失败';
// 					return json_encode($result);
// 				}
			}else{
				//$transaction->rollBack();
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '订单保存失败';
				return json_encode($result);
			}
		} catch (Exception $e) {
			//$transaction->rollBack();
		}
	}
	
	//获取订单详情
	/*
	 * $order_id 订单id
	 * */
	public function getOrderDetails($order_id='',$order_no=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(!isset($order_id) || empty($order_id)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数order_id缺失';
			return json_encode($result);
		}
		$order = GjOrder::model() -> find('id =:id and flag=:flag',array(
				':id' => $order_id,
				':flag' => FLAG_NO
		));
		if(isset($order_no) && !empty($order_no)){
			$order = GjOrder::model() -> find('order_no = :order_no and flag=:flag',array(
					':order_no' => $order_no,
					':flag' => FLAG_NO
			));
		}
		if($order){
			$result['status'] = ERROR_NONE;
			$result['data'] = array(
					'id' => $order -> id,
					'order_no' => $order -> order_no,
					'agent_id' => $order -> agent_id,
					'merchant_id' => $order -> merchant_id,
					'merchant_name' => isset($order -> merchant_id)?$order -> merchant -> name:'',
					'wq_product_id' => $order -> wq_product_id,
					'wq_product_name' => isset($order -> wq_product_id)?$order -> gjproduct -> name:'',
					'trade_no' => $order -> trade_no,
					'points_pay' => $order -> points_pay,
					'pay_channel' => $order -> pay_channel,
					'pay_money' => $order -> pay_money,
					'pay_status' => $order -> pay_status,
					'order_status' => $order -> order_status,
					'pay_time' => $order -> pay_time,
					'cancel_time' => $order -> cancel_time,
					'flag' => $order -> flag,
					'remark' => $order -> remark,
					'invite_code' => $order -> invite_code,
					'code_use_time' => $order -> code_use_time,
					'code_merchant_id' => $order -> code_merchant_id,
					'create_time' => $order -> create_time,
					'last_time' => $order -> last_time,
					'time_limit' => $order -> time_limit,
					'order_type' => $order -> order_type,
					'if_tryout' => $order -> if_tryout,
			);
			return json_encode($result);
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '该订单不存在';
			return json_encode($result);
		}
	}
	
	/**
	 * 生成WQ订单编号
	 */
	public function getWQOrderNo() {
		$Code = 'WQ'.date('Ymd').$this -> getRandChar(4);
		$gjOrder= GjOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
		if(!empty($gjOrder)) {
			while($Code == $gjOrder->order_no) {
				$Code = 'WQ'.date('Ymd').$this -> getRandChar(4);
				$gjOrder = GjOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
			}
		}
		return $Code;
	}
	
	/**
	 * 生成AO订单编号
	 */
	public function getAOOrderNo() {
		$Code = 'AO'.date('Ymd').$this -> getRandChar(4);
		$aoOrder= AgentOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
		if(!empty($aoOrder)) {
			while($Code == $aoOrder->order_no) {
				$Code = 'AO'.date('Ymd').$this -> getRandChar(4);
				$aoOrder = AgentOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
			}
		}
		return $Code;
	}
	
	/**
	 * 更改商户信息
	 * 用于通过积分抵消     不经过支付宝
	 */
	public function updateMerchant($orderId)
	{
		$result = array();
		if(!isset($orderId) || empty($orderId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数orderId缺失';
			return json_encode($result);
		}
		$order = GjOrder::model() -> find('id =:id and flag=:flag',array(
				':id' => $orderId,
				':flag' => FLAG_NO
		));
		
		if (! empty ( $order )) {
			$time_limit = $order['time_limit'];
			$merchant = Merchant::model ()->findByPk ( $order->merchant_id );
			$merchant->if_tryout = IF_TRYOUT_NO;
			$merchant->gj_product_id = $order->wq_product_id;
            $merchant->last_time = date ( 'Y-m-d H:i:s' );
            $merchant ->gj_end_time = date('Y-m-d H:i:s',strtotime("+$time_limit day"));
			if ($merchant->update ()) {
				$result ['status'] = ERROR_NONE;
				$result ['id'] = $order->id;
				$result ['order_type'] = $order->order_type;
				return json_encode ( $result );
			} else {
				$result ['status'] = ERROR_SAVE_FAIL;
				$result ['errMsg'] = '商户信息修改失败';
				return json_encode ( $result );
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '该订单不存在';
			return json_encode($result);
		}
	}
	
	
	//产生玩券管家验证码
	/*
	 * $orderId 订单id
	 * $merchantId
	 * */
	public function createWqCode($orderId,$merchantId=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$gjorder = new GjOrder();
		if(!isset($orderId) || empty($orderId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数orderId缺失';
			return json_encode($result);
		}
		$order = GjOrder::model() -> find('id =:id and flag=:flag',array(
				':id' => $orderId,
				':flag' => FLAG_NO
		));
		
		if($order){
			$code = $this -> getRandChar(12);
			$gjorder = GjOrder::model() -> findAll('invite_code=:invite_code and order_status=:order_status and flag=:flag',array(
					':invite_code' => $code,
					':order_status' => GJORDER_STATUS_NUUSE,
					':flag' => FLAG_NO
			));
			while ($gjorder){
				$code = $this -> getRandChar(12);
				$gjorder = GjOrder::model() -> findAll('invite_code=:invite_code and order_status=:order_status and flag=:flag',array(
						':invite_code' => $code,
						':order_status' => GJORDER_STATUS_NUUSE,
						':flag' => FLAG_NO
				));
			}
			$order -> invite_code = $code;
			$order -> order_status = GJORDER_STATUS_NUUSE;
			$order -> pay_status = GJORDER_PAY_STATUS_PAID;
			if(isset($merchantId) && !empty($merchantId)){
				$order -> merchant_id = $merchantId;
 			}
 			if($order -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '订单更新失败';
				return json_encode($result);
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '该订单不存在';
			return json_encode($result);
		}
	}
	
	
	private function getRandChar($length){
		$str = null;
		$strPol = "0123456789";
		$max = strlen($strPol)-1;
	
		for($i=0;$i<$length;$i++){
			$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}
		return $str;
	}
	
	
	//获取玩券管家订单列表
	public function getOrderList($agentId,$order_no='',$merchantName='',$order_status='',$pay_status=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$criteria = new CDbCriteria();
		//订单号搜索
		if(isset($order_no) && !empty($order_no)){
			$criteria->addCondition('order_no = :order_no');
			$criteria->params[':order_no'] = $order_no;
		}
		//商户名搜索
		
		//合作商id
		if(isset($agentId) && !empty($agentId)){
			$criteria->addCondition('agent_id = :agent_id');
			$criteria->params[':agent_id'] = $agentId;
		}
		
		//所属合作商搜索
		if(isset($merchantName) && !empty($merchantName)){
			$criteria_merchant = new CDbCriteria();
			$criteria_merchant->addCondition("name like '%$merchantName%'");
			$criteria_merchant->addCondition('flag = :flag');
			$criteria_merchant->params[':flag'] = FLAG_NO;
			$merchant = Merchant::model() -> findAll($criteria_merchant);
			if($merchant){
				$mid = array();
				foreach ($merchant as $k => $v){
					$mid[$k] = $v -> id;
				}
				$criteria->addInCondition('merchant_id',$mid);
			}
		}
		
		//订单状态搜索
		if(isset($order_status) && !empty($order_status)){
			$criteria->addCondition('order_status = :order_status');
			$criteria->params[':order_status'] = $order_status;
		}
		
		//订单支付状态搜索
		if(isset($pay_status) && !empty($pay_status)){
			$criteria->addCondition('pay_status = :pay_status');
			$criteria->params[':pay_status'] = $pay_status;
		}
		
		$criteria->order = 'create_time DESC';
		
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		
		$pages = new CPagination(GjOrder::model()->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		
		$order = GjOrder::model() -> findAll($criteria);
		$data = array();
		if(!empty($order)){
			
			foreach ($order as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['order_no'] = $v -> order_no;
				$data['list'][$k]['merchant_name'] = empty($v -> merchant_id)?'':$v -> merchant -> name;
				$data['list'][$k]['product_name'] = empty($v -> wq_product_id)?'':$v -> gjproduct -> name;
				$data['list'][$k]['time_limit'] = $v -> time_limit;
				$data['list'][$k]['pay_status'] = $v -> pay_status;
				$data['list'][$k]['order_status'] = $v -> order_status;
				$data['list'][$k]['create_time'] = $v -> create_time;
			}
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
			return json_encode($result);
		}else{
			$result['status'] = ERROR_NONE;
			$data['list'] = array();
			$result['data'] = $data;
			return json_encode($result);
		}
		
	}
	
	
	//订单支付
	/*
	 * $order_no 订单号
	 * $pay_channel 支付渠道
	 * */
	public function orderPay($order_no,$pay_channel,$trade_no){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$order = GjOrder::model() -> find('order_no =:order_no and flag=:flag',array(
				':order_no' => $order_no,
				':flag' => FLAG_NO,
		));
		if($order){
			if($order -> pay_status == ORDER_STATUS_UNPAID){
				$order -> pay_status = ORDER_STATUS_PAID;
			}
			if(isset($pay_channel) && !empty($pay_channel)){
				$order -> pay_channel = $pay_channel;
			}
			
			$order -> trade_no = $trade_no;
			$order -> pay_time = new CDbExpression('now()');
			$transaction= Yii::app ()->db->beginTransaction();
			try {
				if($order -> update()){
				 if($order -> order_type == GJ_ORDER_TYPE_XZ){ //如果订单类型是新增订单   则产生验证码
					$re = json_decode($this->createWqCode($order -> id,''));
					if($re -> status == ERROR_NONE){
						$transaction->commit();
						$result['status'] = ERROR_NONE;
						$result['id'] = $order -> id;
						$result['merchant_id'] = $order -> merchant_id;
						$result['wq_product_id'] = $order -> wq_product_id;
						$result['time_limit'] = $order -> time_limit;
						$result['order_type'] = $order -> order_type;
						return json_encode($result);
					}else{
						$transaction->rollBack();
						$result['status'] = ERROR_SAVE_FAIL;
						$result['errMsg'] = '创建验证码失败';
						return json_encode($result);
					}
				 }elseif($order -> order_type == GJ_ORDER_TYPE_SJ){ //如果订单类型是升级订单   则修改商户信息
						$merchant = Merchant::model()->findByPk($order -> merchant_id);
						$merchant -> if_tryout = IF_TRYOUT_NO;
						$merchant -> gj_product_id = $order -> wq_product_id;
						$merchant -> last_time = date('Y-m-d H:i:s');
						if(isset(Yii::app()->session['agent_id'])){
							$agent = Agent::model()->findByPk(Yii::app()->session['agent_id']);
							$agent -> points = Yii::app() -> session['agent_points'];
							$agent -> update();
						}
						if($merchant -> update()){
							$transaction->commit();
							$result['status'] = ERROR_NONE;
							$result['id'] = $order -> id;
							$result['merchant_id'] = $order -> merchant_id;
							$result['wq_product_id'] = $order -> wq_product_id;
							$result['time_limit'] = $order -> time_limit;
							$result['order_type'] = $order -> order_type;
							return json_encode($result);
						}else{
							$transaction->rollBack();
							$result['status'] = ERROR_SAVE_FAIL;
							$result['errMsg'] = '商户信息修改失败';
							return json_encode($result);
						}
				  }
					
				}else{
					$transaction->rollBack();
					$result['status'] = ERROR_SAVE_FAIL;
					$result['errMsg'] = '订单更新失败';
					return json_encode($result);
				}
				
			} catch (Exception $e) {
				$transaction->rollBack();
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = $e -> getMessage();
				return json_encode($result);
			}
			
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '该订单不存在';
			return json_encode($result);
		}
	}
	
	/**
	 * 合作商支付成功   修改订单
	 * @param  $order_no  订单号
	 * @param  $pay_channel  支付渠道
	 * @param  $trade_no  交易流水号
	 */
	public function agentOrderPay($order_no,$pay_channel,$trade_no){
		
		$result = array();
		$transaction= Yii::app ()->db->beginTransaction();
		try {
			$order = AgentOrder::model()->find('order_no=:order_no and flag=:flag',
					array(':order_no'=>$order_no,':flag'=>FLAG_NO));
			//判断订单支付状态
			if($order -> pay_status == ORDER_STATUS_UNPAID){
				$order -> pay_status = ORDER_STATUS_PAID;
			}
			$order -> pay_channel = $pay_channel;
			$order -> trade_no = $trade_no;
			$order -> pay_time = date('Y-m-d H:i:s');
			
			if($order -> update()){
				$agent = Agent::model()->findByPk($order -> agent_id);
				//判断合作商审核状态
				if($agent -> audit_status == AGENT_AUDIT_STATUS_PASS){
					$agent -> audit_status = AGENT_AUDIT_STATUS_OPEN;
				}
				
				if($agent->update()){
					$transaction->commit();
					$result['status'] = ERROR_NONE;
					$result['errMsg'] = '';
				}else{
					$transaction->rollBack();
					$result['status'] = ERROR_SAVE_FAIL;
					throw new Exception('数据保存失败');
				}
			}else{
				$transaction->rollBack();
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
		} catch (Exception $e) {
			$transaction->rollBack();
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}

		return json_encode($result);
	}
	
	
	//设置订单指定商户
	public function setMerchant($orderId,$merchantId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$order = GjOrder::model() -> find('id=:id and flag=:flag',array(
				':id' => $orderId,
				':flag' => FLAG_NO
		));
		if($order){
			$order -> merchant_id = $merchantId;
			if($order -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '订单更新失败';
				return json_encode($result);
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '该订单不存在';
			return json_encode($result);
		}
	}
	
	/**
	 * 创建合作商订单
	 * $agent_id  合作商id
	 * $pay_money 支付金额
	 */
	public function createAoOrder($agent_id,$pay_money)
	{
		$result = array();
		$model = new AgentOrder();
		
		if(!isset($agent_id) || empty($agent_id)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数agent_id缺失';
			return json_encode($result);
		}else{
			$model -> agent_id = $agent_id;
		}
		
		if(!isset($pay_money) || empty($pay_money)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数pay_money缺失';
			return json_encode($result);
		}else{
			$model -> pay_money = $pay_money*10000;
		}
		
		$order_no = $this->getAOOrderNo();
		$model -> order_no = $order_no;
		$model -> pay_status = GJORDER_PAY_STATUS_NUPAID;
		
		if($model->save()){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';
			$result['order_no'] = $order_no;
			$result['agent_order_id'] = Yii::app()->db->getLastInsertID();
		}else{
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败';
		}
		return json_encode($result);
	}
	
	/**
	 * 获取AO订单对象
	 */
	public function getAoObj($agent_order_id)
	{
		$model = AgentOrder::model()->findByPk($agent_order_id);
		return $model;
	}
	
}