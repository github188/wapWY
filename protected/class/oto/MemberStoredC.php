<?php
/*
 * 会员储值
 */
include_once(dirname(__FILE__).'/../mainClass.php');
class MemberStoredC extends mainClass
{
	
	/**
	 * 生成订单号
	 * @return string
	 */
	private function createStoredOrderNumber() {
		do {
			$random = mt_rand(100000, 999999); //生成6位随机数密码
			$order_no = STORED_ORDER_PREFIX.date('Ymd', time()).$random;
			$criteria = new CDbCriteria();
			$criteria->addCondition('order_no = :order_no');
			$criteria->params[':order_no'] = $order_no;
			$model = StoredOrder::model()->find($criteria);
		}while (!empty($model));
		return $order_no;
	}
	
	/**
	 * 获取实收金额
	 * @param unknown $order_no
	 * @return number
	 */
	public function getReceiptAmount($order_no) {
		$receipt_money = 0;
		
		$model = StoredOrder::model()->find('order_no = :order_no', array(':order_no' => $order_no));
		if (!empty($model)) {
			//查询储值活动信息
			$stored = Stored::model()->findByPk($model['stored_id']);
			
			//计算实收金额
			$receipt_money = $stored['stored_money'] * $model['num'];
			if ($receipt_money < 0) { //实收金额为负则为零
				$receipt_money = 0;
			}
			if ($model['pay_status'] != ORDER_STATUS_PAID) { //订单不是已支付则为零
				$receipt_money = 0;
			}
		}
	
		return $receipt_money;
	}
	
	/**
	 * 创建储值订单
	 * @param unknown $account
	 * @param unknown $operator_id
	 * @param unknown $stored_id
	 * @param unknown $num
	 * @param unknown $channel
	 * @param unknown $terminal_type
	 * @param unknown $terminal_id
	 */
	public function createStoredOrder($account, $operator_id, $stored_id, $num, $channel, $terminal_type=TERMINAL_TYPE_WEB, $terminal_id=NULL) {
		$result = array();
		try {
			//非空验证
			if (empty($operator_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('operator_id不能为空');
			}
			if (empty($stored_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('stored_id不能为空');
			}
			if (empty($num)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('num不能为空');
			}
			if (empty($account)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('account不能为空');
			}
			if (empty($channel)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('channel不能为空');
			}
			//查询储值活动信息
			$stored = Stored::model()->findByPk($stored_id);
			if (empty($stored)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('储值活动不存在');
			}
			//储值金额计算
// 			$money = $stored['get_money'];
// 			$amount = $money * $num;
			//用户信息查询
			$user = new UserSC();
			$re = json_decode($user -> getUserDetails($operator_id, $account));
			if ($re -> status != ERROR_NONE) {
				$result['status'] = $re -> status;
				throw new Exception($re -> errMsg);
			}
// 			//修改会员的储值金额并保存
// 			$user['money'] += $amount;
// 			if (!$user->save()) {
// 				$result['status'] = ERROR_SAVE_FAIL;
// 				throw new Exception('数据保存失败');
// 			}
			//查询操作员信息
			if ($operator_id != 'user') {
				$operator = Operator::model()->findByPk($operator_id);
				if (empty($operator)) {
					$result['status'] = ERROR_NO_DATA;
					throw new Exception('操作员不存在');
				}elseif ($operator['status'] == OPERATOR_STATUS_LOCK) {
					throw new Exception('操作员账号被锁定');
				}
			}
			
			//支付渠道
			if ($channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
				$channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
				$channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
				$channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
				$channel != ORDER_PAY_CHANNEL_CASH &&
				$channel != ORDER_PAY_CHANNEL_UNIONPAY) {
				$result['status'] = ERROR_PARAMETER_FORMAT;
				throw new Exception('错误的支付方式');
			}
			//添加储值订单记录
			$order = new StoredOrder();
			$order['user_id'] = $re -> data -> user_id;
			$order['store_id'] = $operator['store_id'];
			$order['operator_id'] = $operator_id;
			$order['stored_id'] = $stored_id;
			$order['pay_channel'] = $channel;
			$order['pay_status'] = ORDER_STATUS_UNPAID; //未支付
			$order['order_status'] = ORDER_STATUS_NORMAL; //正常
			//$order['pay_time'] = date('Ymd', time());
			$order['num'] = $num;
			$order['order_no'] = $this->createStoredOrderNumber();
			$order['create_time'] = date('Y-m-d H:i:s');
			$order['terminal_type'] = $terminal_type;
			$order['terminal_id'] = $terminal_id;
			
			if (!$order->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
			$result['status'] = ERROR_NONE;
			$result['order_id'] = $order['id'];
			$result['order_no'] = $order['order_no'];
			$result['errMsg'] = '';
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 储值订单支付成功
	 * @param unknown $order_no
	 * @param unknown $pay_time
	 * @param unknown $trade_no
	 * @param unknown $alipay_account
	 * @param unknown $merchant_discount
	 * @param unknown $alipay_discount
	 * @param unknown $alipay_user_id
	 * @param unknown $wxpay_openid
	 * @param unknown $wxpay_p_openid
	 * @param unknown $ums_card_no
	 * @throws Exception
	 * @return multitype:string
	 */
	public function orderPaySuccess($order_no, $pay_time, $trade_no, $alipay_account, $merchant_discount=NULL, $alipay_discount=NULL, $alipay_user_id=NULL, $wxpay_openid=NULL, $wxpay_p_openid=NULL, $ums_card_no=NULL) {
		//参数检查
		if (empty($order_no)) {
			throw new Exception('订单编号不能为空');
		}
		
		//订单查找
		$criteria = new CDbCriteria();
		$criteria->addCondition('order_no = :order_no');
		$criteria->params[':order_no'] = $order_no;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$model = StoredOrder::model()->find($criteria);
		
		if (empty($model)) {
			throw new Exception('未找到相关订单信息');
		}
		
		//查询储值活动
		$stored = Stored::model()->find('id = :id and flag = :flag', array(':id' => $model['stored_id'], ':flag' => FLAG_NO));
		if (empty($stored)) {
			throw new Exception('未找到相关储值活动');
		}
		//计算储值金额
		$money = ($stored['stored_money'] + $stored['get_money']) * $model['num'];
		//查询用户信息
		$user = User::model()->findByPk($model['user_id']);
		if (empty($user)) {
			throw new Exception('未找到相关会员信息');
		}
		$user['money'] += $money;
		if (!$user->save()) {
			throw new Exception('数据修改失败');
		}
		
		//是否获得积分
		$rule = PointsRule::model()->find('merchant_id = :merchant_id and flag = :flag',
				array(':merchant_id' => $stored['merchant_id'], ':flag' => FLAG_NO));
		if (!empty($rule)) {
			$type = $rule['type'];
			$valid = $rule['if_storedpay_get_points'];
			if ($type == '1' && $valid == '2') {
				$user = new UserSC();
					
				//实付金额
				$total_pay = $stored['stored_money'] * $model['num'];
				//储值来源
				$from = USER_POINTS_DETAIL_FROM_STORED;
				//更新用户的积分
				$result = $user->addUserPoints($model['user_id'], $total_pay, $from, $model['id']);
				if ($result['status'] != ERROR_NONE) {
					$msg = isset($result['errMsg']) ? $result['errMsg'] : '系统内部错误';
					throw new Exception($msg);
				}
			}
		}
		
		//订单修改
		$model['trade_no'] = $trade_no;
		$model['pay_status'] = ORDER_STATUS_PAID;
		$model['pay_time'] = $pay_time;
		if (!empty($alipay_account)) {
			$model['alipay_account'] = $alipay_account;
		}
		if (!empty($alipay_user_id)) {
			$model['alipay_user_id'] = $alipay_user_id;
		}
		if (!empty($wxpay_openid)) {
			$model['wechat_user_id'] = $wxpay_openid;
		}
		if (!empty($wxpay_p_openid)) {
			$model['wechat_user_p_id'] = $wxpay_p_openid;
		}
		if (!empty($ums_card_no)) {
			$model['ums_card_no'] = $ums_card_no;
		}
		if (!empty($merchant_discount)) {
			$model['merchant_discount_money'] = $merchant_discount;
		}
		if (!empty($alipay_discount)) {
			$model['alipay_discount_money'] = $alipay_discount;
		}
		
		//订单保存
		if ($model->save()) {
			$data = array('status' => ERROR_NONE);
			return $data;
		}else {
			throw new Exception('修改订单失败');
		}
	}
	
	/**
	 * 订单详情
	 * @param $id
	 * @param $order_no
	 * @return string
	 */
	public function getOrderDetail($id, $order_no) {
		$result = array();
		try {
			//参数验证
			//TODO
			if (empty($id) && empty($order_no)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数缺失');
			}
			$cmd = Yii::app()->db->createCommand();
			$cmd->select('m.name mname, m.wx_name, s.name sname, op.number, s.is_print, o.*'); //查询字段
			$cmd->from(array('wq_stored_order o','wq_store s', 'wq_merchant m', 'wq_operator op')); //查询表名
			$where = array(
					'AND',  //and操作
					'o.store_id = s.id', //联表
					'o.operator_id = op.id',
					's.merchant_id = m.id'
			);
			if (!empty($id)) {
				array_push($where, 'o.id = :id');
				$cmd->params[':id'] = $id;
			}else {
				array_push($where, 'o.order_no = :order_no');
				$cmd->params[':order_no'] = $order_no;
			}
			$cmd->where($where);
			//执行sql，获取数据
			$model = $cmd->queryRow();
	
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('查询的数据不存在');
			}
			//数据封装
			$data = array();
			$data = $model;
			
			//商户名修正：name为支付宝申请的商户名，wx_name为微信申请的商户名
			//商户名的显示为显示非空的商户名
			$data['mname'] = $data['mname'] ? $data['mname'] : $data['wx_name'];
	
			$data['account'] = '无';
			$data['user_name'] = '';
			$data['user_sex'] = '';
			$data['user_avatar'] = '';
			$data['user_grade'] = '';
			//查询用户
			$user = User::model()->findByPk($model['user_id']);
			$data['account'] = $user['account'];
			$data['user_name'] = $user['name'];
			$data['user_sex'] = $user['sex'];
			$data['balance'] = $user['money'];
			$data['user_avatar'] = $user['avatar'];
			//查询会员等级
			$grade = UserGrade::model()->findByPk($user['membershipgrade_id']);
			if (!empty($grade)) {
				$data['user_grade'] = $grade['name'];
			}
	
			//订单是否超过15分钟
			$data['pwd'] = 'noneed';
			$time = strtotime($model['create_time']);
			if ($time < strtotime("-15 minute")) {
				$data['pwd'] = 'need';
			}
			
			//查询储值活动
			$stored = Stored::model()->findByPk($model['stored_id']);
			$data['stored_money'] = $stored['stored_money'];
			$data['get_money'] = $stored['get_money'];
			$data['stored_name'] = $stored['name'];
			 
			//计算实收金额
			$money = $data['stored_money']; //单价
			$num = $data['num']; //数量
			$order_money = $money * $num; //订单总金额
			$merchant_discount = $data['merchant_discount_money']; //商家优惠
			$alipay_discount = $data['alipay_discount_money']; //支付宝优惠
			//实收金额
			$receipt_money = $order_money - $merchant_discount;
			
			$data['receipt_money'] = $receipt_money;
			if ($data['order_status'] == ORDER_STATUS_REVOKE) {
				$data['receipt_money'] = 0;
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
	 * 更新订单user_code
	 * @param unknown $order_no
	 * @param unknown $user_code
	 * @throws Exception
	 * @return string
	 */
	public function updateCode($order_no, $user_code) {
		$result = array();
		try {
			//参数验证
			//TODO
			$criteria = new CDbCriteria();
			$criteria->addCondition('order_no = :order_no');
			$criteria->params[':order_no'] = $order_no;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$model = StoredOrder::model()->find($criteria);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('修改的数据不存在');
			}
	
			$model['user_code'] = $user_code;
			if (!$model->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
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
	 * 订单撤销
	 * @param unknown $id
	 * @param unknown $operator_id
	 * @param $terminal_type
     * @param $terminal_id
	 * @throws Exception
	 * @return multitype:string
	 */
	public function revokeOrder($id, $operator_id, $terminal_type=TERMINAL_TYPE_WEB, $terminal_id=NULL) {
		//参数检查
		//TODO
		//查询订单信息
		$order = StoredOrder::model()->find('id = :id and flag = :flag', array(
				':id' => $id,
				':flag' => FLAG_NO
		));
		//订单是否存在
		if (empty($order)) {
			throw new Exception('未找到相关订单信息');
		}
		 
		//检查订单状态
		if ($order['order_status'] != ORDER_STATUS_NORMAL) {
			throw new Exception('该订单无法进行撤销操作');
		}
		
		$is_refund = false; //是否有退款
		//如果订单支付状态为已付款，进行退款操作
		if ($order['pay_status'] == ORDER_STATUS_PAID) {
			//记录该订单有退款
			$is_refund = true;
			//计算需要扣除的储值金额
			$stored_id = $order['stored_id']; //储值活动id
			$stored = Stored::model()->findByPk($stored_id); //查找储值活动
			if (empty($stored)) {
				throw new Exception('未找到相关储值活动信息');
			}
			//计算储值的金额
			$money = ($stored['stored_money'] + $stored['get_money']) * $order['num'];
			//修改用户储值
			$user = new UserSC();
			$res = $user->updateUserStored($order['user_id'], -$money);
			if ($res['status'] != ERROR_NONE) {
				throw new Exception($res['errMsg']);
			}
		}
		//支付渠道为支付宝条码/扫码时，调用支付宝撤销订单接口
		if ($order['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM ||
			$order['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM) {
			$api = new AlipaySC();
			$ret = $api->alipayRevoke($order['order_no']);
			$result = json_decode($ret, true);
			if ($result['status'] != ERROR_NONE) {
				throw new Exception($result['errMsg']);
			}
			$order['trade_no'] = $result['trade_no'];
		}
		//支付渠道为微信条码/扫码时，调用微信撤销订单接口
		if ($order['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM ||
		$order['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM) {
			$api = new WxpaySC();
			$ret = $api->wxpayRevoke($order['order_no']);
			$result = json_decode($ret, true);
			if ($result['status'] != ERROR_NONE) {
				throw new Exception($result['errMsg']);
			}
			$order['trade_no'] = $result['trade_no'];
		}
		
		//积分撤销
		if ($is_refund) {
			$refund_money = $stored['stored_money'] * $order['num'];
			$from = USER_POINTS_DETAIL_FROM_STORED;
			$user = new UserSC();
			$res = $user->reduceUserPoints($order['user_id'], $refund_money, $from, $order['id']);
			if ($res['status'] != ERROR_NONE) {
				throw new Exception($res['errMsg']);
			}
		}
		
		//修改订单状态：撤销
		$order['order_status'] = ORDER_STATUS_REVOKE;
		//修改订单取消时间
		$order['cancel_time'] = date('Y-m-d H:i:s');
		$order['refund_terminal_type'] = $terminal_type;
		$order['refund_terminal_id'] = $terminal_id;
		if (!$order->save()) {
			throw new Exception('修改订单失败');
		}
		return array('status' => ERROR_NONE, 'is_refund' => $is_refund);
	}
    
    //查找显示储值金额下拉框
    /**
     * $merchantId 商户id
     */
    public function Stored($merchantId)
    {
        //返回结果
        $result     = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag = 0;
        if(isset($merchantId) && empty($merchantId))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if($flag == 0)
        {
            $criteria   = new CDbCriteria();
            $criteria->addCondition('end_time>=:end_time');
            $criteria->params[':end_time'] = date('Y-m-d 23:59:59');
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $stored = Stored::model()->findall($criteria);
            if(!empty($stored))
            {
                $data   = array();        
                foreach ($stored as $key => $value) 
                {
                    $data[$key]['stored_money'] = $value['stored_money'];
                    $data[$key]['get_money']    = $value['get_money']; 
                    $data[$key]['id']           = $value['id'];
                }
                $result['status'] = ERROR_NONE;
                $result['data']   = $data;                
            }else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }
    
    /**
     * 获取储值活动列表
     * @param unknown $operator_id
     * @return string
     */
    public function getStoredList($operator_id) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->select('sd.id, sd.stored_money, sd.get_money'); //查询字段
    		$cmd->from(array('wq_operator o','wq_stored sd', 'wq_store s')); //查询表名
    		$cmd->where(array(
    				'AND',  //and操作
    				'o.store_id = s.id', //联表
    				's.merchant_id = sd.merchant_id',
    				'o.id = :operator_id', //操作员id
    				'sd.flag = :flag',
    				'UNIX_TIMESTAMP(sd.start_time) < UNIX_TIMESTAMP()',
    				'UNIX_TIMESTAMP(sd.end_time) > UNIX_TIMESTAMP()'
    		));
    		 
    		//查询参数
    		$cmd->params = array(
    				':operator_id' => $operator_id,
    				':flag' => FLAG_NO
    		);
    		
    		$model = $cmd->queryAll();
    		
    		if(!empty($model)){
    			//数据封装
    			$data = array();
    			foreach ($model as $key => $value) {
    				$data['list'][$key]['id'] = $value['id']; //活动id
    				$data['list'][$key]['money'] = $value['stored_money']; //充值金额
    				$data['list'][$key]['bonus'] = $value['get_money']; //奖励金额
    			}
    			//分页
    			//TODO
    			$data['page'] = '';
    			$result['data'] = $data;
    			$result['status'] = ERROR_NONE; //状态码
    			$result['errMsg'] = ''; //错误信息
    		}else{
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
    
    //返回merchant_id和store_id
    /**
     * $operator_id 操作员id
     */
    public function Back($operator_id)
    {
        //返回结果
        $result     = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag = 0;
        if(isset($operator_id) && empty($operator_id))
        {  
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数operator_id缺失';
            $flag = 1;
        }
        if($flag == 0)
        {
            $operator   = Operator::model()->find('id=:id and flag=:flag',array(':id'=>$operator_id,':flag'=>FLAG_NO));
            if(!empty($operator)){
                $storeId    = $operator->store_id;
                $store      = Store::model()->find('id=:id and flag=:flag',array(':id'=>$storeId,':flag'=>FLAG_NO));
                if(!empty($store)){
                    $merchantId = $store->merchant_id;
                    $data = array();        
                    $data['storeId']    = $storeId;
                    $data['merchantId'] = $merchantId;
                    $result['status']   = ERROR_NONE;
                    $result['data']     = $data;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }
    
    //储值记录
    /**
     * $operatorId 操作员id
     */
    public function MemberStoredList($operator_id,$start='',$end='',$account='')
    {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		//操作员角色
    		$role = $operator['role'];
    		//操作员所属门店id
    		$store_id = $operator['store_id'];
    		
    		//查询储值记录列表
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('store_id = :store_id');
    		$criteria->params[':store_id'] = $store_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->addCondition('pay_status = :pay_status');
    		$criteria->params[':pay_status'] = 2;
    		$criteria->order = 'create_time desc';
    		//店员只能查看自己的记录
    		if ($role == OPERATOR_ROLE_NORMAL) {
    			$criteria->addCondition('operator_id = :operator_id');
    			$criteria->params[':operator_id'] = $operator_id;
    		}
    		//会员账号
    		if (!empty($account)) {
    			$user_id = '0';
    			//查询会员信息
    			$user = User::model()->find('account = :account', array(':account' => $account));
    			if (!empty($user)) {
    				$user_id = $user['id'];
    			}
    			$criteria->addCondition('user_id = :user_id');
    			$criteria->params[':user_id'] = $user_id;
    		}
    		//开始时间
    		if (!empty($start)) {
    			$criteria->addCondition('pay_time > :start');
    			$criteria->params[':start'] = $start;
    		}
    		//结束时间
    		if (!empty($end)) {
    			$criteria->addCondition('pay_time < :end');
    			$criteria->params[':end'] = $end;
    		}
    		//分页
    		$pages = new CPagination(StoredOrder::model()->count($criteria));
    		$pages->pageSize = Yii::app() -> params['perPage'];
    		$pages->applyLimit($criteria);
    		$this->page = $pages;
    		
    		//查询
    		$model = StoredOrder::model()->findAll($criteria);
    		
    		$data = array();
    		foreach ($model as $k => $v) {
    			$data['list'][$k]['id'] = $v['id'];
    			$user = User::model()->findByPk($v['user_id']);
    			$data['list'][$k]['account'] = isset($user['account']) ? $user['account'] : '';
    			$stored = Stored::model()->findByPk($v['stored_id']);
    			$data['list'][$k]['money'] = isset($stored['stored_money']) ? $stored['stored_money'] : '';
    			$data['list'][$k]['bonus'] = isset($stored['get_money']) ? $stored['get_money'] : '';
    			$data['list'][$k]['num'] = $v['num'];
    			$operator = Operator::model()->findByPk($v['operator_id']);
    			$data['list'][$k]['operator'] = $operator['name'].' ('.$operator['number'].')';
    			$data['list'][$k]['pay_time'] = $v['pay_time'];
    			$data['list'][$k]['order_status'] = $v['order_status'];
    			$data['list'][$k]['pay_channel'] = $v['pay_channel'];
    		}
    		$result['status'] = ERROR_NONE;
    		$result['data'] = $data;
    		$result['errMsg'] = '';
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 获取储值订单列表 （app）
     * @param unknown $operator_id
     * @param unknown $limit_id 限制订单id，结果集订单id小于限制订单id
     * @param unknown $start_date 开始时间
     * @param unknown $end_date 结束时间
     * @param unknown $select_type 筛选的支付类型
     * @param unknown $select_operator 筛选的操作员
     * @param unknown $order_status 筛选的订单状态
     * @param unknown $pay_status 筛选的支付状态
     * @param unknown $keyword 搜索关键词
     */
    public function getOrderList4App($operator_id, $limit_id, $start_date, $end_date, $select_type, $select_operator, $order_status, $pay_status, $keyword) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		 
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		//操作员角色
    		$role = $operator['role'];
    		//操作员所属门店id
    		$store_id = $operator['store_id'];
    		 
    		//查询储值记录列表
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('store_id = :store_id');
    		$criteria->params[':store_id'] = $store_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		//店员只能查看自己的记录
    		if ($role == OPERATOR_ROLE_NORMAL) {
    			$criteria->addCondition('operator_id = :operator_id');
    			$criteria->params[':operator_id'] = $operator_id;
    		}
    		 
    		if (!empty($limit_id)) {
    			$criteria->addCondition('id < :id');
    			$criteria->params[':id'] = $limit_id;
    		}
    		if (!empty($start_date)) {
    			$criteria->addCondition('create_time >= :start_time');
    			$criteria->params[':start_time'] = $start_date;
    		}
    		if (!empty($end_date)) {
    			$criteria->addCondition('create_time <= :end_time');
    			$criteria->params[':end_time'] = $end_date;
    		}
    		if (!empty($select_type)) {
    			$criteria->addCondition('pay_channel = :pay_channel');
    			$criteria->params[':pay_channel'] = $select_type;
    		}
    		if (!empty($order_status)) {
    			$criteria->addCondition('order_status = :order_status');
    			$criteria->params[':order_status'] = $order_status;
    		}
    		if (!empty($pay_status)) {
    			$criteria->addCondition('pay_status = :pay_status');
    			$criteria->params[':pay_status'] = $pay_status;
    		}
    		if (!empty($select_operator)) {
    			$select_model = Operator::model()->find('store_id = :store_id AND number = :number',
    					array(':store_id' => $store_id, ':number' => $select_operator));
    			if (!empty($select_model)) {
    				$select_operator_id = $select_model['id'];
    				$criteria->addCondition('operator_id = :operator_id');
    				$criteria->params[':operator_id'] = $select_operator_id;
    			}
    		}
    		if (!empty($keyword)) {
    			//查询会员信息
    			$select_users = User::model()->findAll("account like :account", array(':account' => '%'.$keyword.'%'));
    			//$select_user_id = !empty($select_user) ? $select_user['id'] : '';
    			$id_str = '';
    			foreach ($select_users as $select_user) {
    				if (empty($id_str)) {
    					$id_str .= $select_user['id'];
    				}else {
    					$id_str .= ','.$select_user['id'];
    				}
    			}
    			if (!$id_str) {
    				$criteria->addCondition("order_no like :order_no");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    			}else {
    				$criteria->addCondition("(order_no like :order_no) OR (user_id IN ($id_str))");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    			}
    		}
    		 
    		//配置的每页显示数量
    		$page_size = Yii::app()->params['perPage'];
    		$criteria->limit = $page_size ? : 10;
    		 
    		//查询
    		$model = StoredOrder::model()->findAll($criteria);
    		 
    		$data = array();
    		foreach ($model as $k => $v) {
    			$user_id = $v['user_id']; //会员id
    			$stored_id = $v['stored_id']; //储值活动id
    			$operator_id = $v['operator_id']; //操作员id
    			
    			$user = User::model()->findByPk($user_id);
    			$stored = Stored::model()->findByPk($stored_id);
    			$operator = Operator::model()->findByPk($operator_id);
    			
    			$id = $v['id'];
    			$title = empty($stored['stored_money']) ? '' : '充'.$stored['stored_money'].'元';
    			$title .= empty($stored['get_money']) ? '' : '送'.$stored['get_money'].'元';
    			$order_no = $v['order_no'];
    			$receipt_money = $this->getReceiptAmount($order_no);
    			$num = $v['num'];
    			$pay_channel = $v['pay_channel'];
    			$create_time = $v['create_time'];
    			$account = $user['account'];
    			$avatar = $user['avatar'];
    			$operator_number = $operator['number']; 
    			
    			$data[] = array(
    					'id' => $id, 
    					'order_no' => $order_no,
    					'title' => $title,
    					'receipt_money' => $receipt_money,
    					'num' => $num,
    					'pay_channel' => $pay_channel,
    					'create_time' => $create_time,
    					'user_account' => $account,
    					'user_avatar' => $avatar,
    					'operator_number' => $operator_number,
    			);
    		}
    
    		$result['item_count'] = count($data);
    		$result['page_count'] = '';
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
     * 获取储值订单列表 （pc）
     * @param unknown $store_id
     * @param unknown $operator_id
     * @param unknown $page_num
     * @param unknown $start_date 开始时间
     * @param unknown $end_date 结束时间
     * @param unknown $select_type 筛选的支付类型
     * @param unknown $select_operator 筛选的操作员
     * @param unknown $order_status 筛选的订单状态
     * @param unknown $pay_status 筛选的支付状态
     * @param unknown $keyword 搜索关键词
     */
    public function getOrderList4PC($operator_id, $page_num, $start_date, $end_date, $select_type, $select_operator, $order_status, $pay_status, $keyword) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    	
    		$operator = Operator::model()->find('id = :id and flag = :flag', array(
    				':id' => $operator_id,
    				':flag' => FLAG_NO
    		));
    		if (empty($operator)) {
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('操作员信息有误');
    		}
    		//操作员角色
    		$role = $operator['role'];
    		//操作员所属门店id
    		$store_id = $operator['store_id'];
    	
    		//查询储值记录列表
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('store_id = :store_id');
    		$criteria->params[':store_id'] = $store_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		//店员只能查看自己的记录
    		if ($role == OPERATOR_ROLE_NORMAL) {
    			$criteria->addCondition('operator_id = :operator_id');
    			$criteria->params[':operator_id'] = $operator_id;
    		}
    		
    		if (!empty($start_date)) {
    			$criteria->addCondition('create_time >= :start_time');
    			$criteria->params[':start_time'] = $start_date;
    		}
    		if (!empty($end_date)) {
    			$criteria->addCondition('create_time <= :end_time');
    			$criteria->params[':end_time'] = $end_date;
    		}
    		if (!empty($select_type)) {
    			$criteria->addCondition('pay_channel = :pay_channel');
    			$criteria->params[':pay_channel'] = $select_type;
    		}
    		if (!empty($order_status)) {
    			$criteria->addCondition('order_status = :order_status');
    			$criteria->params[':order_status'] = $order_status;
    		}
    		if (!empty($pay_status)) {
    			$criteria->addCondition('pay_status = :pay_status');
    			$criteria->params[':pay_status'] = $pay_status;
    		}
    		if (!empty($select_operator)) {
    			$select_model = Operator::model()->find('store_id = :store_id AND number = :number',
    					array(':store_id' => $store_id, ':number' => $select_operator));
    			if (!empty($select_model)) {
    				$select_operator_id = $select_model['id'];
    				$criteria->addCondition('operator_id = :operator_id');
    				$criteria->params[':operator_id'] = $select_operator_id;
    			}
    		}
    		if (!empty($keyword)) {
    			//查询会员信息
    			$select_users = User::model()->findAll("account like :account", array(':account' => '%'.$keyword.'%'));
    			//$select_user_id = !empty($select_user) ? $select_user['id'] : '';
    			$id_str = '';
    			foreach ($select_users as $select_user) {
    				if (empty($id_str)) {
    					$id_str .= $select_user['id'];
    				}else {
    					$id_str .= ','.$select_user['id'];
    				}
    			}
    			if (!$id_str) {
    				$criteria->addCondition("order_no like :order_no");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    			}else {
    				$criteria->addCondition("(order_no like :order_no) OR (user_id IN ($id_str))");
    				$criteria->params[':order_no'] = '%'.$keyword.'%';
    			}
    		}
    		
    		//配置的每页显示数量
    		$page_size = Yii::app()->params['perPage'];
    		//计算总页数
    		$total_num = StoredOrder::model()->count($criteria);
    		$total_page = ceil($total_num / $page_size);
    		//翻页
    		$criteria->limit = $page_size ? : 10;
    		$page_num += 0;
    		if (!empty($page_num) && $page_num > 0) {
    			$criteria->offset = $page_size * ($page_num -1);
    		}
    	
    		//查询
    		$model = StoredOrder::model()->findAll($criteria);
    	
    		$data = array();
    		foreach ($model as $k => $v) {
    			$user_id = $v['user_id']; //会员id
    			$stored_id = $v['stored_id']; //储值活动id
    			$operator_id = $v['operator_id']; //操作员id
    			 
    			$user = User::model()->findByPk($user_id);
    			$stored = Stored::model()->findByPk($stored_id);
    			$operator = Operator::model()->findByPk($operator_id);
    			 
    			$id = $v['id'];
    			$title = empty($stored['stored_money']) ? '' : '充'.$stored['stored_money'].'元';
    			$title .= empty($stored['get_money']) ? '' : '送'.$stored['get_money'].'元';
    			$order_no = $v['order_no'];
    			$receipt_money = $this->getReceiptAmount($order_no);
    			$num = $v['num'];
    			$pay_channel = $v['pay_channel'];
    			$create_time = $v['create_time'];
    			$account = $user['account'];
    			$avatar = $user['avatar'];
    			$operator_number = $operator['number'];
    			 
    			$data[] = array(
    					'id' => $id,
    					'order_no' => $order_no,
    					'title' => $title,
    					'receipt_money' => $receipt_money,
    					'num' => $num,
    					'pay_channel' => $pay_channel,
    					'create_time' => $create_time,
    					'user_account' => $account,
    					'user_avatar' => $avatar,
    					'operator_number' => $operator_number,
    			);
    		}
    		
    		$result['item_count'] = count($data);
    		$result['page_count'] = $total_page;
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	
    	return json_encode($result);
    }
    
    
}

