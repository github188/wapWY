<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/**
 * 操作员类
 *
 */
class OperatorC extends mainClass{
	
	/**
	 * 操作员列表
	 * @param $merchantId		商户id
	 * @param $keyword		搜索关键词（操作员名称或编号）
	 * @return array		返回数组
	 */
	public function getOperatorList($merchantId, $keyword = NULL,$store_id_arr = ''){
		$result = array();
		try {
			//参数验证
			//TODO
			$criteria = new CDbCriteria();
			if(isset($merchantId) && !empty($merchantId)){
				$store = Store::model() -> findAll('merchant_id=:merchant_id',array(
						':merchant_id' => $merchantId
				));
				$storeid = array();
				if($store){
					
					foreach ($store as $k => $v){
						$storeid[$k] = $v -> id;
					}
				}
				$criteria->addInCondition('store_id',$storeid);
			}
			
			if(!empty($store_id_arr)){
				$criteria->addInCondition('store_id',$store_id_arr);
			}
			
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			
			
			if (isset($keyword)) {
			    if (empty($keyword)){
			      $criteria->addCondition("name like '%$keyword%' or number like '%$keyword%'");
	            }else{
		          $criteria->addCondition("name like '$keyword' or number like '$keyword'");
			    }
			}

			//按创建时间排序
			$criteria->order = 'create_time DESC';
			
			$pages = new CPagination(Operator::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			$model = Operator::model()->findAll($criteria);

			//数据封装
			$data = array('list' => array());
			foreach ($model as $key => $value) {
				$data['list'][$key]['id'] = $value['id']; //操作员id
				$data['list'][$key]['number'] = $value['number']; //操作员编号
				$store_main = !empty($value['store_id']) ? $value -> store -> name : '';
				$store_branch = !empty($value['store_id'])?$value -> store -> branch_name : '';
				$data['list'][$key]['store'] = $store_main.(!empty($store_branch) ? '-'.$store_branch : ''); //所属门店名称
				$data['list'][$key]['name'] = $value['name']; //操作员姓名
				$data['list'][$key]['account'] = $value['account']; //操作员账号
				$data['list'][$key]['role'] = $value['role']; //操作员角色
				$data['list'][$key]['status'] = $value['status']; //操作员状态
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
	
	/**
	 * 获取所有操作员
	 * @param unknown $merchantId
	 * @return string
	 */
	public function getOperators($merchantId,$store_id='',$role='') {
		$result = array();
		try {
			//参数验证
			//TODO
			$criteria = new CDbCriteria();
			if(isset($merchantId) && !empty($merchantId)){
				$store = Store::model() -> findAll('merchant_id=:merchant_id and flag=:flag',array(
						':merchant_id' => $merchantId,
						':flag' => FLAG_NO
				));
				if($store){
					$storeid = array();
					foreach ($store as $k => $v){
						$storeid[$k] = $v -> id;
					}
					$criteria->addInCondition('store_id',$storeid);
				}
			}
            if($role==OPERATOR_ROLE_NORMAL)
            {
                $criteria->addCondition('role = :role');
                $criteria->params[':role'] = OPERATOR_ROLE_NORMAL;
            }
            if (!empty($store_id)) {
            	$criteria->addCondition('store_id = :store_id');
            	$criteria->params[':store_id'] = $store_id;
            }

			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
				
				
// 			if (!empty($keyword)) {
// 				$criteria->addCondition("name like '%$keyword%' or number like '%$keyword%'");
// 			}
			
			$model = Operator::model()->findAll($criteria);
		
			//数据封装
			$data = array('list' => array());
			foreach ($model as $key => $value) {
				$data['list'][$key]['id'] = $value['id']; //操作员id
				$data['list'][$key]['number'] = $value['number']; //操作员编号
				$data['list'][$key]['store'] = !empty($value['store_id'])?$value -> store -> name:''; //所属门店名称
				$data['list'][$key]['name'] = $value['name']; //操作员姓名
				$data['list'][$key]['account'] = $value['account']; //操作员账号
				$data['list'][$key]['role'] = $value['role']; //操作员角色
				$data['list'][$key]['status'] = $value['status']; //操作员状态
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
	
	/**
	 * 操作员详情
	 * @param $id		操作员id
	 * @return array	返回数组
	 */
	public function getOperatorDetails($id){
		$result = array();
		try {
			//参数验证
			//TODO
			$criteria = new CDbCriteria();
			$criteria->addCondition('id = :id');
			$criteria->params[':id'] = $id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$model = Operator::model()->find($criteria);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('查询的数据不存在');
			}
			//数据封装
			$data = array();
			$data['number'] = $model['number']; //操作员编号
			$data['name'] = $model['name']; //操作员名称
			$data['account'] = $model['account']; //操作员账号
			$data['role'] = $model['role']; //角色
            $data['pwd'] = $model['pwd']; //密码
            $data['admin_pwd'] = $model['admin_pwd']; //店长密码
            $data['store_id'] = $model['store_id']; //门店id
			$data['create_time'] = $model['create_time']; //创建时间
			//查询门店
			$store = Store::model()->findByPk($model['store_id']);
			if (empty($store)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('未查询到门店信息');
			}
			$data['store'] = $store['name'];
				
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
	 * 添加操作员
	 * @param $number 		操作员编号
	 * @param $store_id		门店id
	 * @param $name			姓名
	 * @param $account		账号
	 * @param $role			角色
	 * @param $pwd			登录密码
	 * @param $admin_pwd 	退款密码（角色为店长拥有）
	 * @return array		返回数组
	 */
	public function addOperator($number, $store_id, $name, $account, $role, $pwd, $admin_pwd){
		$result = array();
		try {
			//参数验证
			//TODO
			//查询门店名称是否重复
			$criteria = new CDbCriteria();
			$criteria->addCondition('account = :account');
			$criteria->params[':account'] = $account;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$model = Operator::model()->find($criteria);
			if (!empty($model)) {
				$result['status'] = ERROR_DUPLICATE_DATA;
				throw new Exception('操作员编号重复');
			}
			//角色密码检查
			if ($role == OPERATOR_ROLE_ADMIN && $admin_pwd == '') {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('添加店长，管理员密码不能为空');
			}
			if ($role == OPERATOR_ROLE_NORMAL) {
				$admin_pwd = '';
			}
			
			$model = new Operator();
			$model['store_id'] = $store_id;
			$model['number'] = $number;
			$model['name'] = $name;
			$model['account'] = $account;
			$model['role'] = $role;
			$model['pwd'] = md5($pwd);
			$model['admin_pwd'] = $admin_pwd;
			$model['create_time'] = date('Y-m-d H:i:s', time());
				
			if ($model->save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
				$result['data'] = array('id' => $model->id);
			}else {
				$result['status'] = ERROR_SAVE_FAIL; //状态码
				$result['errMsg'] = '数据保存失败'; //错误信息
				$result['data'] = '';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 修改操作员
	 * @param $id			操作员id
	 * @param $store_id		门店id
	 * @param $role			角色
	 * @param $name			姓名
	 * @param $pwd 			密码
	 * @param $admin_pwd	店长密码
	 * @return array 		返回数组
	 */
	public function editOperator($id, $store_id, $role, $name, $pwd, $admin_pwd){
		$result = array();
		try {
			//参数验证
			//TODO
			$criteria = new CDbCriteria();
			$criteria->addCondition('id = :id');
			$criteria->params[':id'] = $id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$model = Operator::model()->find($criteria);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('修改的操作员不存在');
			}
			if ($role == OPERATOR_ROLE_NORMAL) {
				$admin_pwd = '';
			}
			
			$model['store_id'] = $store_id;
			$model['role'] = $role;
			$model['name'] = $name;
			if (!empty($pwd)) {
				$model['pwd'] = md5($pwd);
			}
			$model['admin_pwd'] = $admin_pwd;
		
			if ($model->save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
				$result['data'] = '';
			}else {
				$result['status'] = ERROR_SAVE_FAIL; //状态码
				$result['errMsg'] = '数据保存失败'; //错误信息
				$result['data'] = '';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 删除操作员
	 * @param $id	 操作员id
	 * @return array 返回数组
	 */
	public function deleteOperator($id){
		$result = array();
		try {
			//参数验证
			//TODO
			$model = Operator::model()->findByPk($id);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('删除的数据不存在');
			}
			//修改删除标识
			$model['flag'] = FLAG_YES;
		
			if ($model->save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
				$result['data'] = '';
			}else {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = '数据保存失败'; //错误信息
				$result['data'] = '';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 锁定操作员
	 * @param $id	 操作员id
	 * @return array 返回数组
	 */
	public function lockOperator($id){
		$result = array();
		try {
			//参数验证
			//TODO
			$model = Operator::model()->findByPk($id);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作的数据不存在');
			}
			//修改锁定标识
			$model['status'] = OPERATOR_STATUS_LOCK;
	
			if ($model->save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
				$result['data'] = '';
			}else {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = '数据保存失败'; //错误信息
				$result['data'] = '';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
	
		return json_encode($result);
	}
	
	/**
	 * 解锁操作员
	 * @param $id	 操作员id
	 * @return array 返回数组
	 */
	public function unlockOperator($id){
		$result = array();
		try {
			//参数验证
			//TODO
			$model = Operator::model()->findByPk($id);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作的数据不存在');
			}
			//修改锁定标识
			$model['status'] = OPERATOR_STATUS_NORMAL;
	
			if ($model->save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
				$result['data'] = '';
			}else {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = '数据保存失败'; //错误信息
				$result['data'] = '';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
	
		return json_encode($result);
	}
	
	/**
	 * 操作员日志列表
	 * @param $merchant_id	商户id
	 * @param $operator_id  操作员id
	 * @param $start_time   开始时间
	 * @param $end_time     截止时间
	 * @return array		返回数组
	 */
	public function getOperatorLog($merchant_id, $operator_id=NULL, $start_time=NULL, $end_time=NULL,$manager_id=NULL){
		$result = array();
		try {
			//参数验证
			if (empty($merchant_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数merchant_id不能为空');
			}
			$criteria = new CDbCriteria();
			
			if(!empty($manager_id)){
			    $manager = Manager::model() -> findByPk($manager_id);
			    $store_id = $manager -> store_id;
			    $storeId = substr($store_id, 1, strlen($store_id) - 2);
			    $right_arr = explode(',', $storeId);
			    $criteria1 = new CDbCriteria();
			    $criteria1->addInCondition('store_id', $right_arr);
			    $operator = Operator::model() -> findAll($criteria1);
			}
			
			
			$criteria->addCondition('merchant_id = :merchant_id');
			$criteria->params[':merchant_id'] = $merchant_id;
			if (!empty($operator_id)) {
				$criteria->addCondition('operator_id = :operator_id');
				$criteria->params[':operator_id'] = $operator_id;
			}
			if (!empty($start_time)) {
				$criteria->addCondition('create_time >= :start_time');
				$criteria->params[':start_time'] = $start_time;
			}
			if (!empty($end_time)) {
				$criteria->addCondition('create_time <= :end_time');
				$criteria->params[':end_time'] = $end_time;
			}
			
            //按创建时间排序
            $criteria->order = 'create_time DESC';
			//分页
			$pages = new CPagination(OperatorLog::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$model = OperatorLog::model()->findAll($criteria);
		
			//数据封装
			$data = array();
			foreach ($model as $key => $value) {
				$operator = Operator::model()->findByPk($value['operator_id']);
				$data['list'][$key]['operator'] = $operator['number']; //操作员编号
				$data['list'][$key]['time'] = $value['create_time']; //操作时间
				$data['list'][$key]['operation'] = $value['operation']; //操作
				$data['list'][$key]['client'] = $value['client']; //客户端
				$data['list'][$key]['ip'] = $value['ip']; //操作
			}
			//分页
			//TODO
			$data['page'] = '';
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
	 * 操作员登录
	 * @param $account	账号
	 * @param $pwd		密码
	 * @return array 	返回数组
	 */
	public function operatorLogin($account, $pwd){
		$result = array();
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
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$model = Operator::model()->find($criteria);
		
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('账户不存在');
			}
			//判断密码
			if ($model['pwd'] != $pwd) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('密码错误');
			}
			//判断状态
			if ($model->status == OPERATOR_STATUS_LOCK) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('登录失败,账号被锁定');
			}
			
			$model['login_time'] = date('Y-m-d H:i:s'); //最后登录时间
			$model['login_ip'] = Yii::app()->request->userHostAddress; //最后登录ip
			$model->save(); //数据保存
			
			//通过门店信息获取商户id
			$store = Store::model()->findByPk($model['store_id']);
			//添加操作员日志
			$log = new OperatorLog();
			$log['operator_id'] = $model['id']; //操作员id
			$log['merchant_id'] = $store['merchant_id']; //商户id
			$log['operation'] = '登录'; //操作
			$log['client'] =  ''; //客户端
			$log['ip'] = Yii::app()->request->userHostAddress;
			$log['create_time'] = date('Y-m-d H:i:s');
			$log->save();
			
			$merchant  = Merchant::model() -> find('id=:id and flag=:flag',array(
					':id' => $model -> store -> merchant_id,
					':flag' => FLAG_NO
			));
			
			$result['data'] = array(
					'id' => $model->id,
					'account' => $model->account, 
					'name' => $model->name,
					'store_name' => $model -> store -> name,
					'merchant_name' => $merchant -> wq_m_name,
					'store_id' => $model -> store_id,
			);
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 更新店员登录密码
	 * @param unknown $operator_id
	 * @param unknown $old_pwd
	 * @param unknown $new_pwd
	 * @throws Exception
	 */
	public function updatePwd($operator_id, $old_pwd, $new_pwd) {
		$result = array();
		try {
			if (empty($operator_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数operator_id不能为空');
			}
			if (empty($old_pwd)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数old_pwd不能为空');
			}
			if (empty($new_pwd)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数new_pwd不能为空');
			}
				
			//查询操作员信息
			$model = Operator::model()->findByPk($operator_id);
				
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员不存在');
			}
			
			//验证旧密码
			if ($old_pwd != $model['pwd']) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('原密码错误');
			}
				
			//更新token
			$model['pwd'] = $new_pwd;
				
			if (!$model->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
				
			$result['data'] = array(
					'token' => $model['token'],
					'store_id' => $model['store_id']
			);
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 更新操作员token
	 * @param unknown $operator_id
	 * @throws Exception
	 * @return string
	 */
	public function updateToken($operator_id) {
		$result = array();
		try {
			if (empty($operator_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数operator_id不能为空');
			}
			
			//查询操作员信息
			$model = Operator::model()->findByPk($operator_id);
			
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员不存在');
			}
			
			//更新token
			$model['token'] = md5($operator_id.time());
			
			if (!$model->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
			
			$result['data'] = array(
					'token' => $model['token'],
					'store_id' => $model['store_id']
			);
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * token验证
	 * @param unknown $token
	 * @param unknown $mchid
	 * @throws Exception
	 * @return string
	 */
	public function verifyToken($token, $mchid) {
		$result = array();
		try {
			if (empty($token)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数token不能为空');
			}
			
			//查询操作员信息
			$model = Operator::model()->find('token = :token and flag = :flag',
					array(':token' => $token, ':flag' => FLAG_NO));
				
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员不存在');
			}
			
			if ($model['status'] == OPERATOR_STATUS_LOCK) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('操作员被锁定');
			}
			
			$store_id = $model['store_id'];
			$store = Store::model()->findByPk($store_id);
			if (empty($store)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员所属门店不存在');
			}
			
			$merchant_id = $store['merchant_id'];
			$merchant = Merchant::model()->findByPk($merchant_id);
			if (empty($merchant)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员所属商户不存在');
			}
			if ($merchant['mchid'] != $mchid) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('非本商户下的操作员');
			}
			
			$result['data'] = array('operator_id' => $model['id'], 'store_id' => $model['store_id']);
			
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 该商户下的操作员账号验证
	 * @param unknown $account
	 * @param unknown $mchid
	 * @throws Exception
	 * @return string
	 */
	public function verifyAccount($account, $mchid) {
		$result = array();
		try {
			if (empty($account)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数account不能为空');
			}
				
			//查询操作员信息
			$model = Operator::model()->find('account = :account and flag = :flag',
					array(':account' => $account, ':flag' => FLAG_NO));
	
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员不存在');
			}
			
			if ($model['status'] == OPERATOR_STATUS_LOCK) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('操作员被锁定');
			}
				
			$store_id = $model['store_id'];
			$store = Store::model()->findByPk($store_id);
			if (empty($store)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员所属门店不存在');
			}
				
			$merchant_id = $store['merchant_id'];
			$merchant = Merchant::model()->findByPk($merchant_id);
			if (empty($merchant)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('操作员所属商户不存在');
			}
			if ($merchant['mchid'] != $mchid) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('非本商户下的操作员');
			}
				
			$result['data'] = array('operator_id' => $model['id'], 'store_id' => $model['store_id']);
				
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
	
		return json_encode($result);
	}
	
	/**
	 * 店长密码验证
	 * @param unknown $store_id
	 * @param unknown $pwd
	 * @throws Exception
	 * @return string
	 */
	public function verifyAdminPwd($store_id, $pwd) {
		$result = array();
		try {
			if (empty($store_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数store_id不能为空');
			}
			if (empty($pwd)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数pwd不能为空');
			}
		
			//验证店长密码
			$model = Operator::model()->find('store_id = :store_id and md5(admin_pwd) = :pwd', 
					array(':store_id' => $store_id, ':pwd' => $pwd));
		
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('店长密码错误');
			}
				
			$result['data'] = array('operator_id' => $model['id']);
				
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 生成店长密码
	 * @param $merchant_id 商户id
	 * @return array       返回数组
	 */
	public function createAdminPwd($merchant_id) {
		$result = array();
		try {
			//参数验证
			if (empty($merchant_id)) {
				$result['status'] = ERROR_PARAMETER_FORMAT;
				throw new Exception('参数merchant_id不能为空');
			}
			
			do {
				$pwd = rand(100000, 999999); //生成6位随机数密码
				$criteria = new CDbCriteria();
				$criteria->join = "JOIN wq_store s on t.store_id=s.id";
				$criteria->addCondition('s.merchant_id = :merchant_id');
				$criteria->params[':merchant_id'] = $merchant_id;
				$criteria->addCondition('s.flag = :sflag');
				$criteria->params[':sflag'] = FLAG_NO;
				
				$criteria->addCondition('t.role = :role');
				$criteria->params[':role'] = OPERATOR_ROLE_ADMIN;
				$criteria->addCondition('t.admin_pwd = :admin_pwd');
				$criteria->params[':admin_pwd'] = $pwd;
				$criteria->addCondition('t.flag = :flag');
				$criteria->params[':flag'] = FLAG_NO;
				$model = Operator::model()->find($criteria);
			}while (!empty($model) || empty($pwd));
				
			$result['data'] = array('admin_pwd' => $pwd);
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	
	/**
	 * 获取属性
	 * @return array
	 */
	function getAttributes() {
		return Operator::model()->attributes;
	}
        
                
        /**
         * 操作员设置
         * @param type $merchant_id 商户id
         * @param type $operator_refund_time 操作员允许退款时间
         * @param type $dzoperator_refund_time 店长允许退款时间
         */
        public function OperatorSet($merchant_id,$operator_refund_time,$dzoperator_refund_time)
        {
            $result = array();
		try {
                    //参数验证
                    if (empty($merchant_id)) {
                        $result['status'] = ERROR_PARAMETER_FORMAT;
                        throw new Exception('参数merchant_id不能为空');
                    }
                    $merchant = Merchant::model()->findbypk((int)$merchant_id);//查询商户
                    $merchant -> operator_refund_time = $operator_refund_time;
                    $merchant -> dzoperator_refund_time = $dzoperator_refund_time;
                    if($merchant -> update())//如果修改成功
                    {
                        $result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
                    }
                    } catch (Exception $e) {
                    $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                    $result['errMsg'] = $e->getMessage(); //错误信息
		}
		
            return json_encode($result);
        }
        /**
         * 显示设置操作员数据的传递，
         * @param type $merchant_id
         * @return type
         * @throws Exception
         */
        public function OperatorSetSee($merchant_id)
        {
            $result = array();
            $data = array();
		try {
			//参数验证
			if (empty($merchant_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数merchant_id不能为空');
			}
                        $merchant = Merchant::model()->findbypk((int)$merchant_id);
                        
                        $day = intval($merchant -> dzoperator_refund_time / 86400);
                                                
                        $hour = intval(($merchant -> dzoperator_refund_time % 86400) / 3600);
                       
                        $clock = intval($merchant -> dzoperator_refund_time % 3600 / 60);

                        $store_day = intval($merchant -> operator_refund_time / 86400);

                        $store_hour = intval($merchant -> operator_refund_time % 86400 / 3600);

                        $store_clock = intval($merchant -> operator_refund_time % 3600 / 60);
 
                        $data['day']         = $day;//天
                        $data['hour']        = $hour;//小时
                        $data['clock']       = $clock;//分钟
                        $data['store_day']   = $store_day;//天
                        $data['store_hour']  = $store_hour;//小时
                        $data['store_clock'] = $store_clock;//分钟
                        $result['data']      = $data;
			$result['status']    = ERROR_NONE; //状态码
			$result['errMsg']    = ''; //错误信息
                        } catch (Exception $e) {
                    $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                    $result['errMsg'] = $e->getMessage(); //错误信息
		}
		
            return json_encode($result);
        }

    /**
     * 获取未删除的operator
     */
    public function getOper($merchant_id,$store_id)
    {
        $result = array();
        $data = array();
        try {
            //参数验证
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('store_id = :id');
            $criteria->params[':id'] = $store_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model=Operator::model()->findAll($criteria);
            $num=0;
            if(isset($model)&&!empty($model))
            {
                foreach($model as $key=>$value)
                {
                    $model_log=OperatorLog::model()->find('merchant_id=:merchant_id and operator_id=:operator_id',
                        array(':merchant_id'=>$merchant_id,':operator_id'=>$value['id']));
                    if(isset($model_log)&&!empty($model_log))
                    {
                        //判断ID是否已经存在
                        $flag=true;
                        for($i=0;$i<count($data);$i++)
                        {
                            if($data[$i]==$value['id'])
                                $flag=false;
                        }
                        if($flag)
                        {
                            $data[$num]['id'] = $value['id'];
                            $data[$num]['number'] = $value['number'];
                            $num++;
                        }
                    }
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '数据为空'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 验证操作员编号是否重复
     */
    public function checkOperatorNumber($merchant_id,$account)
    {

        $result = array();
        $data = array();
        try {
            //参数验证
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('account = :account');
            $criteria->params[':account'] = $account;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Operator::model()->find($criteria);
            if (!empty($model)) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = ''; //错误信息
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '数据为空'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 选择门店选出操作员
     */
    public function ChooseStore($merchant_id,$store_id=null){
        $result = array();
        $data = array();
        try {
            //参数验证
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            if(!empty($store_id)) {
                $criteria->addCondition('store_id = :store_id');
                $criteria->params[':store_id'] = $store_id;
            }
            $criteria->addCondition('status = :status');
            $criteria->params[':status'] = ADMIN_STATUS_NORMAL;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Operator::model()->findAll($criteria);
            if (!empty($model)) {
                foreach($model as $key=>$value)
                {
                    $data[$value['id']]=$value['name'].'('.$value['number'].')';
                }
                $result['data']=$data;
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '数据为空'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 获取下属操作员
     * @param unknown $operator_id
     * @return string
     */
    public function getUnderOperators($operator_id) {
    	$result = array();
    	try {
    		//参数验证
    		//TODO
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('id = :id');
    		$criteria->params[':id'] = $operator_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		
    		$model = Operator::model()->find($criteria);
    	
    		if (empty($model)) {
    			$result['status'] = ERROR_NO_DATA;
    			$result['errMsg'] = '查询的数据不存在';
    		}
    		
    		$role = $model['role']; //操作员角色
    		
    		$list = array();
    		if ($role == OPERATOR_ROLE_ADMIN) { //店长角色
    			//所属门店
    			$store_id = $model['store_id'];
    			$list = Operator::model()->findAll('store_id = :store_id and flag = :flag', 
    					array(':store_id' => $store_id, ':flag' => FLAG_NO));
    		}else {
    			$list[] = $model;
    		}
    		
    		//数据封装
    		$data = array();
    		foreach ($list as $key => $value) {
    			$data[] = array(
    					'id' => $value['id'], //操作员id
    					'number' => $value['number'], //操作员编号
    					'store' => $value['store'] ? $value['store']['name'] : '', //所属门店名称
    					'name' => $value['name'], //操作员姓名
    					'account' => $value['account'], //操作员账号
    					'role' => $value['role'], //操作员角色
    					'status' => $value['status'], //操作员状态
    			);
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
    
}