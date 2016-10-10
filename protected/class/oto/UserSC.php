<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/**
 * 会员类
 *
 */
class UserSC extends mainClass{
	/**
	 * 会员列表
	 */
	public function getUserList() {
		
	}
	
	/**
	 * 会员详情
	 * @param $operator_id  用户id
	 * @param $account		账号（手机号或会员卡号）
	 * @param $user_id      会员id
	 * @return array
	 */
	public function getUserDetails($operator_id, $account, $user_id = NULL) {
		$result = array();
		try {
			//参数验证
			//TODO
			/*
			$cmd = Yii::app()->db->createCommand();
			$cmd->select('u.id, u.name, u.sex, u.money, u.free_secret, g.discount, m.points_rule, g.name gname'); //查询字段
			$cmd->from(array('wq_operator o','wq_store s', 'wq_user u', 'wq_user_grade g', 'wq_merchant m')); //查询表名
			$cmd->where(array(
					'AND',  //and操作
					'o.store_id = s.id', //联表
					's.merchant_id = u.merchant_id = m.id',
					'u.membershipgrade_id = g.id',
					'o.id = :operator_id', //操作员id
					array(
							'OR', 
							'u.account = :account', 
							'u.membership_card_no = :account'
					)
			));
			//查询参数
			$cmd->params = array(
					':operator_id' => $operator_id,
					':account' => $account,
					':card_no' => $account
			);
			//执行sql，获取所有行数据
			$model = $cmd->queryRow();
			$data = array();
			if (!empty($model)) {
				$data['user_id'] = $model['id']; //用户id
				$data['name'] = $model['name']; //用户名
				$data['sex'] = $model['sex']; // 用户性别
				$data['money'] = $model['money']; //用户储值金额
				$data['free_secret'] = $model['free_secret'] ; //免密金额
				$data['discount'] = $model['discount']; //会员折扣
				$data['points_rule'] = $model['points_rule']; //积分规则(积分=实收金额*积分规则)
				$data['gname'] = $model['gname']; //会员折扣
			}
			*/
			
			$data = array();
			//查询操作员所属门店id
			$operator = Operator::model()->findByPk($operator_id);
			if (empty($operator)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('无效的操作员信息');
			}
			//查询门店所属商户id
			$store = Store::model()->findByPk($operator['store_id']);
			if (empty($store)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('所属门店不存在');
			}
			if (!empty($account)) {
				//查询会员信息(根据商户id和会员手机号/卡号)
				$user = User::model()->find('merchant_id = :merchant_id AND (account = :account OR membership_card_no = :account)',
						array(':merchant_id' => $store['merchant_id'], ':account' => $account));
			}else {
				$user = User::model()->findByPk($user_id);
			}
			if (empty($user)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('该会员不存在');
			}
			$data['user_id'] = $user['id']; //用户id
			$data['account'] = $user['account']; //会员账号
			$data['name'] = $user['name']; //用户名
			$data['sex'] = $user['sex']; // 用户性别
			$data['money'] = $user['money']; //用户储值金额
			$data['free_secret'] = $user['free_secret'] ; //免密金额
			//查询商户信息
// 			$merchant = Merchant::model()->findByPk($user['merchant_id']);
// 			if (empty($merchant)) {
// 				$result['status'] = ERROR_NO_DATA;
// 				throw new Exception('会员所属商户不存在');
// 			}
// 			$data['points_rule'] = $merchant['points_rule']; //积分规则(积分=实收金额*积分规则)
			//查询会员等级信息
			$data['discount'] = 1; //会员折扣
			$data['gname'] = '';
			$data['points_ratio'] = 0; //积分比率
			$grade = UserGrade::model()->findByPk($user['membershipgrade_id']);
			if (!empty($grade)) {
				$data['discount'] = $grade['discount']; //会员折扣
				$data['gname'] = $grade['name'];
				$data['points_ratio'] = empty($grade['points_ratio']) ? 0 : $grade['points_ratio'];
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
	 * 获取用户今日已免密的总金额
	 * @param unknown $user_id
	 * @return string
	 */
	public function getTotalSecretMoney($user_id) {
		$criteria = new CDbCriteria();
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$criteria->addCondition('user_id = :user_id');
		$criteria->params[':user_id'] = $user_id;
		$criteria->addCondition('stored_confirm_status = :confirm');
		$criteria->params[':confirm'] = ORDER_PAY_NUCONFIRM;
		$criteria->addCondition('create_time > TO_DAYS(NOW())');
			
		$model = Order::model()->findAll($criteria);
			
		$total = 0;
		foreach ($model as $v) {
			$total += $v['stored_paymoney'];
		}
		return $total;
	}

	/**
	 * 增加用户消费积分
	 * @param unknown $user_id
	 * @param unknown $money
	 * @param unknown $from
	 * @param string $order_id
	 * @return multitype:string
	 */
	public function addUserPoints($user_id, $money, $from, $order_id) {
		$result = array();
		//查询用户信息
		$user = User::model()->findByPk($user_id);
		if (empty($user)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '用户不存在';
			return $result;
		}
		$merchant_id = $user['merchant_id'];
		//查询商户信息
// 		$merchant = Merchant::model()->findByPk($merchant_id);
// 		if (empty($merchant)) {
// 			$result['status'] = ERROR_NO_DATA;
// 			$result['errMsg'] = '该用户所属商户不存在';
// 			return $result;
// 		}
// 		$rule = $merchant['points_rule'];

		//查询用户的会员等级
		$criteria = new CDbCriteria();
		$criteria->addCondition('merchant_id = :merchant_id');
		$criteria->params[':merchant_id'] = $merchant_id;
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $user['membershipgrade_id'];
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$user_grade = UserGrade::model()->find($criteria);
		if (empty($user_grade)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '用户所属会员等级不存在';
			return $result;
		}
		$ratio = !empty($user_grade['points_ratio']) ? $user_grade['points_ratio'] : 0;

		if ($from == USER_POINTS_DETAIL_FROM_STORED) {
			//如果是储值获得积分，则使用points_rule表的积分比率
			$rule = PointsRule::model()->find('merchant_id = :merchant_id and flag = :flag',
				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
			if (!empty($rule)) {
				$type = $rule['type'];
				if ($type == '1') {
					$ratio = !empty($rule['stored_points']) ? $rule['stored_points'] : 0;
				}
			}
		}

		//计算积分
		$points = $user['points'] + 0;
		$increase = floor($money * $ratio); //金额*积分比率，向下取整
		//所得积分小于或等于0，则不累计
		if ($increase <= 0) {
			$result['status'] = ERROR_NONE;
			return $result;
		}
		$points += $increase;
		$user['points'] = $points;
		//更新用户等级
		//查询商户的会员
		$criteria = new CDbCriteria();
		$criteria->order = 'points_rule desc';
		$criteria->addCondition('merchant_id = :merchant_id');
		$criteria->params[':merchant_id'] = $merchant_id;
		$criteria->addCondition('points_rule <= :points');
		$criteria->params[':points'] = $points;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$grade = UserGrade::model()->find($criteria);
		if (!empty($grade)) {
			//会员等级是否受积分限制
			//受限制：会员积分必须大于等于会员等级的积分要求
			//不受限制：会员积分可以小于会员等级的积分要求，且等级不会改变，但当积分大于等于更高等级的积分要求时，会员依然可以升级
			if ($user['switch'] == POINTS_LIMIT) { //受限制
				$user['membershipgrade_id'] = $grade['id'];
			}else { //不受限制
				//当前会员等级是否高于可升级的会员等级
				if ($user_grade['points_rule'] < $grade['points_rule']) { //会员可升级
					$user['membershipgrade_id'] = $grade['id'];
				}
			}
		}
		if ($user->save()) {
			//添加用户积分记录
			$record = new UserPointsdetail();
			$record['create_time'] = date('Y-m-d H:i:s');
			$record['user_id'] = $user_id;
			$record['order_id'] = $order_id;
			$record['points'] = $increase;
			$record['ratio'] = $ratio;
			$record['balance_of_payments'] = BALANCE_OF_PAYMENTS_INCOME;
			$record['from'] = $from;

			if ($record->save()) {
				$result['status'] = ERROR_NONE;
			}else {
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败';
			}
		}else {
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败';
		}

		return $result;
	}

	/**
	 * 扣除用户积分
	 * @param unknown $user_id
	 * @param unknown $money
	 * @param unkonwn $from
	 * @param unknown $order_id
	 * @return multitype:string
	 */
	public function reduceUserPoints($user_id, $money, $from, $order_id) {
		$result = array();
		//查询用户信息
		$user = User::model()->findByPk($user_id);
		if (empty($user)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '用户不存在';
			return $result;
		}
		$merchant_id = $user['merchant_id'];

		//查询用户的会员等级
		$criteria = new CDbCriteria();
		$criteria->addCondition('merchant_id = :merchant_id');
		$criteria->params[':merchant_id'] = $merchant_id;
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $user['membershipgrade_id'];
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$user_grade = UserGrade::model()->find($criteria);
		if (empty($user_grade)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '用户所属会员等级不存在';
			return $result;
		}

		//查询积分的消费比率
		$record = UserPointsdetail::model()->find('user_id = :user_id and order_id = :order_id and t.from = :from',
			array(':user_id' => $user_id, ':order_id' => $order_id, ':from' => $from));
		$ratio = $record['ratio'];

		//计算积分
		$points = $user['points'] + 0;
		$reduce = floor($money * $ratio); //金额*积分比率，向下取整
		//所得积分小于或等于0，则不累计
		if ($reduce <= 0) {
			$result['status'] = ERROR_NONE;
			return $result;
		}
		$points -= $reduce;
		//用户积分不能小于0
		$points = $points < 0 ? 0 : $points;
		$user['points'] = $points;
		//更新用户等级
		//查询商户的会员
		$criteria = new CDbCriteria();
		$criteria->order = 'points_rule desc';
		$criteria->addCondition('merchant_id = :merchant_id');
		$criteria->params[':merchant_id'] = $merchant_id;
		$criteria->addCondition('points_rule <= :points');
		$criteria->params[':points'] = $points;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$grade = UserGrade::model()->find($criteria);
		if (!empty($grade)) {
			//会员等级是否受积分限制
			//受限制：会员积分必须大于等于会员等级的积分要求
			//不受限制：会员积分可以小于会员等级的积分要求，且等级不会改变，但当积分大于等于更高等级的积分要求时，会员依然可以升级
			if ($user['switch'] == POINTS_LIMIT) { //受限制
				$user['membershipgrade_id'] = $grade['id'];
			}else { //不受限制
				//当前会员等级是否高于可升级的会员等级
				if ($user_grade['points_rule'] < $grade['points_rule']) { //会员可升级
					$user['membershipgrade_id'] = $grade['id'];
				}
			}
		}
		if ($user->save()) {
			$result['status'] = ERROR_NONE;
		}else {
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败';
		}

		return $result;
	}
	
	/**
	 * 更新用户储值
	 * @param unknown $user_id
	 * @param unknown $add_stored
	 * @return multitype:string
	 */
	public function updateUserStored($user_id, $add_stored) {
		$result = array();
		//查询用户信息
		$user = User::model()->findByPk($user_id);
		if (empty($user)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '用户不存在';
			return $result;
		}
		//当为扣除储值时，检查储值金额是否小于扣除金额
		if (bcsub(-$add_stored, $user['money'], 2) > 0) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '储值金额不足，无法进行扣除';
			return $result;
		}

		$user['money'] += $add_stored;

		if ($user->save()) {
			$result['status'] = ERROR_NONE;
		}else {
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败';
		}

		return $result;
	}
	
}