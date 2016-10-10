<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');
/**
 * 系统设置类
 */

class SystemSC extends mainClass
{
	/**
	 * 修改密码
	 * $operator_id  操作员id
	 * $newPwd 新密码
	 * $newPwdAgain  确认新密码
	 */
	public function editPwd($post,$operator_id,$newPwd ,$newPwdAgain)
	{
		$model = Operator::model()->findByPk($operator_id);
		
		
		$flag = 0;
		$result = array ();
		$errMsg = '';
		
		//验证旧密码
		if(! isset ( $post['pwd'] ) || empty($post['pwd'])){
			$flag = 1;
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 旧密码必填';
			Yii::app()->user ->setFlash('oldPwd','旧密码必填');
		}
		
		//验证旧密码是否正确
		if(!empty($post['pwd'])){
			if(md5($post['pwd']) != $model -> pwd){
				$flag = 1;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = ' 旧密码不正确';
				Yii::app()->user ->setFlash('oldPwd','旧密码不正确');
			}
		}
		
		//验证新密码
		if(! isset ( $newPwd ) || empty($newPwd)){
			$flag = 1;
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg. ' 新密码必填';
			Yii::app()->user ->setFlash('newPwd','新密码必填');
		}
		
		//验证确认新密码
		if(! isset ( $newPwdAgain ) || empty($newPwdAgain)){
			$flag = 1;
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg. ' 确认新密码必填';
			Yii::app()->user ->setFlash('newPwdAgain','确认新密码必填');
		}
		
		//验证两次密码是否一致
		if(!empty($newPwdAgain) && !empty($newPwd)){
			if($newPwd != $newPwdAgain){
				$flag = 1;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg. ' 两次密码不一致';
				Yii::app()->user ->setFlash('notsame','两次密码不一致');
			}
		}
		
		if ($newPwd === $post['pwd']) {
			$flag = 1;
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$errMsg = $errMsg. ' 新密码不能和旧密码相同';
			Yii::app()->user ->setFlash('newPwd','新密码不能和旧密码相同');
		}
		
		if ($flag == 1) {
			$result ['errMsg'] = $errMsg;
			return json_encode ( $result );
		}
		
		if(!empty($model)){
			$model -> pwd = md5($newPwd);
			if($model -> save()){
				
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
				$result ['data'] = array (
						'id' => $model->id
				);
			}else {
			   $result ['status'] = ERROR_SAVE_FAIL; // 状态码
			   $result ['errMsg'] = '数据保存失败'; // 错误信息
			   $result ['data'] = '';
		    }
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		return json_encode($result);
	}
	
	/**
	 * 打印机管理
	 * $operator_id  操作员id
	 * $is_print 是否启用打印
	 */
	public function printStatus($operator_id)
	{
		$result = array ();
	
		$model = Operator::model()->findByPk($operator_id);
		if(!empty($model)){
			$store_id = $model -> store_id;
			$store = Store::model()->findByPk($store_id);
			if(!empty($store)){
				$data = array();
				$data['is_print'] = $store['is_print'];
				$data['print_name'] = $store['print_name'];
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
				$result['data'] = $data;
			}else {
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '无此数据';
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
	
		return json_encode($result);
	}
	
	/**
	 * 打印机管理
	 * $operator_id  操作员id
	 * $is_print 是否启用打印  
	 */
	public function printOperat($operator_id,$is_print)
	{
		$result = array ();
		$flag = 0;
		$errMsg = '';
		
		$model = Operator::model()->findByPk($operator_id);
		if(!empty($is_print)){
		 if(!empty($model)){
			$store_id = $model -> store_id;
			$store = Store::model()->findByPk($store_id);
			if(!empty($store)){
				$store -> is_print = $is_print;
				if($store -> save()){
					
					$result ['status'] = ERROR_NONE; // 状态码
					$result ['errMsg'] = ''; // 错误信息
				}else {
			       $result ['status'] = ERROR_SAVE_FAIL; // 状态码
			       $result ['errMsg'] = '数据保存失败'; // 错误信息
			       $result ['data'] = '';
		        }
			}
		 }else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		 }
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		return json_encode($result);
	}
	
	/**
	 * 管理员密码
	 * $operator_id  操作员id
	 * $pwd  店长登入密码
	 */
	public function getAdminPwd($operator_id,$pwd)
	{
		$result = array();
		$flag = 0;
		$errMsg = '';
		
		$model = Operator::model()->findByPk($operator_id);
		//验证密码是否输入
		if(!isset($pwd) || empty($pwd)){
			$flag = 1;
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 请输入登录密码';
			Yii::app()->user ->setFlash('pwd','请输入登录密码');
		}
		
		if ($flag == 1) {
			$result ['errMsg'] = $errMsg;
			return json_encode ( $result );
		}
		
		if(!empty($pwd)){
			if(!empty($model)){
				if(md5($pwd) == $model -> pwd){
					
					$result ['status'] = ERROR_NONE; // 状态码
					$result ['errMsg'] = ''; // 错误信息
				}else{
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = ' 密码不正确';
					Yii::app()->user ->setFlash('pwd','密码不正确');
					return json_encode ( $result );
				}
			}else{
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '无此数据';
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取店长退款密码
	 * @param unknown $operator_id
	 */
	public function getRandomPwd($operator_id) {
		$result = array();
		$model = Operator::model()->findByPk($operator_id);
		if (empty($model)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '操作员信息有误';
			return json_encode($result);
		}
		$result ['status'] = ERROR_NONE; // 状态码
		$result ['errMsg'] = ''; // 错误信息
		$result['data'] = $model['admin_pwd'];
		
		return json_encode($result);
	}
	
	/**
	 * 管理员密码(获取退款密码)
	 * $operator_id  操作员id
	 */
	public function resetPwd($operator_id)
	{
		$result = array();
		
		$cmd = Yii::app()->db->createCommand();
		$cmd->select('s.merchant_id'); //查询字段
		$cmd->from(array('wq_operator o','wq_store s')); //查询表名
		$cmd->where(array(
				'AND',  //and操作
				'o.store_id = s.id', //联表
				'o.id = :operator_id', //操作员id
		));
		//查询参数
		$cmd->params = array(
				':operator_id' => $operator_id
		);
		//执行sql，获取所有行数据
		$model = $cmd->queryRow();
		if (empty($model)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '操作员信息有误';
			return json_encode($result);
		}
		
		do {
			$pwd = rand(100000, 999999); //生成6位随机数密码
			$criteria = new CDbCriteria();
			$criteria->join = "JOIN wq_store s on t.store_id=s.id";
			$criteria->addCondition('s.merchant_id = :merchant_id');
			$criteria->params[':merchant_id'] = $model['merchant_id'];
			$criteria->addCondition('s.flag = :sflag');
			$criteria->params[':sflag'] = FLAG_NO;
		
			$criteria->addCondition('t.role = :role');
			$criteria->params[':role'] = OPERATOR_ROLE_ADMIN;
			$criteria->addCondition('t.admin_pwd = :admin_pwd');
			$criteria->params[':admin_pwd'] = $pwd;
			$criteria->addCondition('t.flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$tmp = Operator::model()->find($criteria);
		}while (!empty($tmp) || empty($pwd));
		
		$model = Operator::model()->findByPk($operator_id);
		$model['admin_pwd'] = $pwd;
		if($model -> save()){
				
			$result ['status'] = ERROR_NONE; // 状态码
			$result ['errMsg'] = ''; // 错误信息
			$result['randomNum'] = $pwd;
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取6位随机数字
	 */
	public function getRandomNum()
	{
		$authnum = '';
		srand((double)microtime()*1000000);
		$ychar="0,1,2,3,4,5,6,7,8,9";
		$list=explode(",",$ychar);
		for($i=0;$i<6;$i++){
			$randnum=rand(0,9);
			$authnum.=$list[$randnum];
		}
		return $authnum;
	}
	
	/**
	 * 获取操作员角色
	 */
	public function getRole($operator_id)
	{
		$model = Operator::model()->findByPk($operator_id);
		if(!empty($model)){
		   return $model -> role;
		}
		return '';
	}
}