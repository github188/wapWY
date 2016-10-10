<?php 
include_once(dirname(__FILE__).'/../mainClass.php');
/*
 * 时间：2015-6-24
* 创建人：顾磊
* */
class AdminC extends mainClass{
	public $page = null;
	
	//管理员登录
	/* $account 账号 必填
	 * $pwd 密码 必填
	 * */
	public function adminLogin($account,$pwd){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$flag = 0;
		//验证合作商名
		if(!isset($account) || empty($account)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数account缺失';
			$flag = 1;
		}
		//验证账号
		if(!isset($pwd) || empty($pwd)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg  = $errMsg.'参数pwd缺失';
			$flag = 1;
		}
		
		if($flag == 1){
			$result['errMsg'] = $errMsg;
			return json_encode($result);
		}
		
		$admin = Admin::model() -> find('account = :account and pwd = :pwd and status = :status and flag = :flag',array(
				':account' => $account,
				':pwd' => $pwd,
				':status' => ADMIN_STATUS_NORMAL,
				':flag' => FLAG_NO
		));
		if($admin){
			$admin -> login_time = new CDbExpression('now()');
			$admin -> login_ip = Yii::app()->request->userHostAddress;
			if($admin -> update()){
				$result['status'] = ERROR_NONE;
				$result['data'] = array(
						'id' => $admin -> id,
						'account'=>$admin -> account,
						'name' => $admin -> name,
				);
				return json_encode($result);
			}
				
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '登录失败，账号或密码不正确';
			return json_encode($result);
		}
	}
	
	//查询管理员日志列表
	/*
	 * $adminId 管理员id
	 * $start_time 开始时间
	 * $end_time 结束时间
	 * */
	public function getAdminLogList($adminId='',$start_time='',$end_time='',$admin_name){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$criteria = new CDbCriteria();
		
		//管理员id搜索
		if(isset($adminId) && !empty($adminId)){
			$criteria->addCondition('admin_id = :admin_id');
			$criteria->params[':admin_id'] = $adminId;
		}
		
		if(isset($start_time) && !empty($start_time)){
			$criteria->addCondition("t.create_time > :start_time");
			$criteria->params[':start_time'] = $start_time.' 00:00:00';
		}
		
		if(isset($end_time) && !empty($end_time)){
			$criteria->addCondition("t.create_time < :end_time");
			$criteria->params[':end_time'] = $end_time.' 23:59:59';
		}
		
		//管理员名字搜索
		if(isset($admin_name) && !empty($admin_name)){
			$criteria -> addCondition("admin.name like '%$admin_name%'");
		}
		
		$criteria->order = 't.create_time DESC';
		
		//分页
		$pages = new CPagination(AdminLog::model()->with('admin')-> count($criteria));
		$pages -> pageSize = Yii::app() -> params['perPage'];
		$pages -> applyLimit($criteria);
		$this->page = $pages;
		
		$adminLog = AdminLog::model()->with('admin')-> findAll($criteria);
		$data = array();
		if(!empty($adminLog)){
			foreach ($adminLog as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['admin_id'] = $v -> admin_id;
				$data['list'][$k]['admin_name'] = $v -> admin -> name;
				$data['list'][$k]['operation'] = $v -> operation;
				$data['list'][$k]['ip'] = $v -> ip;
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
	
	//添加管理员日志
	/*
	 * $adminId 管理员id
	 * $operation 操作
	 * $ip ip
	 * */
	public function addAdminLog($adminId,$operation,$ip=''){
		$adminLog = new AdminLog();
		//验证管理员id
		if(isset($adminId) && !empty($adminId)){
			$adminLog -> admin_id = $adminId;
		}else{
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = 'adminId';
			return json_encode($result);
		}
		//验证操作
		if(isset($operation) && !empty($operation)){
			$adminLog -> operation = $operation;
		}else{
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = 'operation';
			return json_encode($result);
		}
		if(isset($ip) && !empty($ip)){
			$adminLog -> ip = $ip;
		}
		$adminLog -> create_time = new CDbExpression('now()');
		if($adminLog -> save()){
			$result['status'] = ERROR_NONE;
			$result['data'] = $adminLog -> id;
			return json_encode($result);
		}else{
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '管理员操作日志保存失败';
			return json_encode($result);
		}
		
	}
	
	/**
	 * 添加管理员
	 * $merchant_id   商户id 
	 * $store_id      管理门店
	 * $parent_limit  权限
	 * $account       账号
	 * $name          姓名
	 * $pwd           密码
	 */
	public function addAdmin($merchant_id,$store_id,$parent_limit,$account,$name,$pwd)
	{
		$result = array();
		$flag = 0;
		$errMsg = '';
		try {
			//$first_manage = $this->getFirstManage($merchant_id); //获取一级分组
			//验证账号
			if(empty($account)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 手机号必填';
				Yii::app()->user->setFlash('account_error','手机号必填');
			}else{
				$isCheckAccount = $this->isExitAccount ( $merchant_id, $account );
				if ($isCheckAccount) {
					$flag = 1;
					$result ['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg . ' 手机号已经存在';
					Yii::app ()->user->setFlash ( 'account_error', '手机号已经存在' );
				} else {
					$isCheckPhone = preg_match ( PHONE_CHECK, $account );
					if (! $isCheckPhone) {
						$flag = 1;
						$result ['status'] = ERROR_PARAMETER_FORMAT;
						$errMsg = $errMsg . ' 手机号格式不正确';
						Yii::app ()->user->setFlash ( 'account_error', '手机号格式不正确' );
					}
				}
			}
			
			//验证姓名
			if(empty($name)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 姓名必填';
				Yii::app()->user->setFlash('name_error','姓名必填');
			}
			
			//验证密码
			if(empty($pwd)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 密码必填';
				Yii::app()->user->setFlash('pwd_error','密码必填');
			}
			//验证权限分配
			if(empty($parent_limit)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 权限分配必填';
				echo "<script>alert('权限分配必勾')</script>";
				//Yii::app()->user->setFlash('limit_error','权限分配必填');
			}else {
				if((in_array('100',$parent_limit) && !in_array('101',$parent_limit))||(!in_array('100',$parent_limit) && in_array('101',$parent_limit))||(in_array('100',$parent_limit) && in_array('101',$parent_limit))){
					if (empty ( $store_id )) {
						$flag = 1;
						$result ['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg . ' 门店分配必填';
						echo "<script>alert('门店分配必勾')</script>";
					}
				}
			}
			if($flag == 1){
				$result ['errMsg'] = $errMsg;
				return json_encode($result);
			}
			$model = new Manager();
			$model -> create_time = date('Y-m-d H:i:s');
			$model -> account = $account;
			$model -> name = $name;
			$model -> pwd = md5($pwd);
			$model -> merchant_id = $merchant_id;
			$model -> store_id = !empty($store_id)?','.(implode(',',$store_id)).',':'';
			$model -> right = ','.(implode(',',$parent_limit)).',';
			if($model -> save()){
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 判断账号是否存在
	 * $merchant_id   商户id 
	 * $account       账号
	 * return true(存在)
	 */
	public function isExitAccount($merchant_id,$account,$manager_id=null)
	{
		$merchangt = Merchant::model()->findByPk($merchant_id);
		if(!empty($merchangt)){
			if($merchangt->account == $account){
				return true;
			}
		}
		if (empty ( $manager_id )) {
			$manager = Manager::model ()->find ( 'merchant_id=:merchant_id and flag=:flag and account=:account', array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO,
					':account' => $account 
			) );
			
		}else{
			$manager = Manager::model ()->find ( 'merchant_id=:merchant_id and flag=:flag and account=:account and id!=:id', array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO,
					':account' => $account,
					':id'=>$manager_id
			) );
		}
		
		if(count($manager)>0){
			return true;
		}
		
		return false;
	}
	
	/**
	 * 编辑管理员
	 * $merchant_id   商户id 
	 * $manager_id    管理员id
	 * $store_id      管理门店
	 * $parent_limit  权限
	 * $account       账号
	 * $name          姓名
	 * $pwd           密码
	 */
	public function editManager($merchant_id,$manager_id,$store_id, $parent_limit, $account, $name, $pwd)
	{
		$result = array();
		$flag = 0;
		$errMsg = '';
		try {
			//验证账号
			if(empty($account)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 手机号必填';
				Yii::app()->user->setFlash('account_error','手机号必填');
			}else{
				$isCheckAccount = $this->isExitAccount ( $merchant_id, $account ,$manager_id);
				if ($isCheckAccount) {
					$flag = 1;
					$result ['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg . ' 手机号已经存在';
					Yii::app ()->user->setFlash ( 'account_error', '手机号已经存在' );
				} else {
					$isCheckPhone = preg_match ( PHONE_CHECK, $account );
					if (! $isCheckPhone) {
						$flag = 1;
						$result ['status'] = ERROR_PARAMETER_FORMAT;
						$errMsg = $errMsg . ' 手机号格式不正确';
						Yii::app ()->user->setFlash ( 'account_error', '手机号格式不正确' );
					}
				}
			}
				
			//验证姓名
			if(empty($name)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 姓名必填';
				Yii::app()->user->setFlash('name_error','姓名必填');
			}
				
			//验证密码
			if(empty($pwd)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 密码必填';
				Yii::app()->user->setFlash('pwd_error','密码必填');
			}
			
			//验证权限分配
			if(empty($parent_limit)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 权限分配必填';
				echo "<script>alert('权限分配必勾')</script>";
				//Yii::app()->user->setFlash('limit_error','权限分配必填');
			}else {
			if((in_array('100',$parent_limit) && !in_array('101',$parent_limit))||(!in_array('100',$parent_limit) && in_array('101',$parent_limit))||(in_array('100',$parent_limit) && in_array('101',$parent_limit))){
					if (empty ( $store_id )) {
						$flag = 1;
						$result ['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg . ' 门店分配必填';
						echo "<script>alert('门店分配必勾')</script>";
					}
				}
			}
				
			if($flag == 1){
				$result ['errMsg'] = $errMsg;
				return json_encode($result);
			}
			
			$model = Manager::model()->findByPk($manager_id);
			$model -> last_time = date('Y-m-d H:i:s');
			$model -> account = $account;
			$model -> name = $name;
			if($model -> pwd != ($pwd)){
				$model -> pwd = md5($pwd);
			}
			$model -> store_id = !empty($store_id)?','.(implode(',',$store_id)).',':'';
			$model -> right = ','.(implode(',',$parent_limit)).',';
			if($model -> save()){
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 获取管理员详情
	 * $manager_id    管理员id
	 */
	public function getManagerDetail($manager_id)
	{
		$result = array();
		$data = array();
		try {
			$model = Manager::model()->findByPk($manager_id);
			if(!empty($model)){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
				
				$data['list']['id'] = $model -> id;
				$data['list']['account'] = $model -> account;
				$data['list']['name'] = $model -> name;
				$data['list']['pwd'] = $model -> pwd;
				$data['list']['create_time'] = $model -> create_time;
				$data['list']['right'] = $this -> asToArray($model -> right);
				$data['list']['store_id'] = $this -> asToArray($model -> store_id);
			}else{
			    $result['status'] = ERROR_NO_DATA;
			    $result['errMsg'] = '无此数据';
		    }
		       $result['data'] = $data;
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 转数组
	 */
	public function asToArray($str)
	{
		$arr = array();
		if(!empty($str)){
			//去除首尾的','
			$str = substr($str,1,strlen($str)-2);
			$arr = explode(',',$str);
		}
		return $arr;
	}
	
	/**
	 * 获取一级分组
	 */
	public function getFirstManage($merchant_id)
	{
		$data = array();
		$model = Management::model()->findAll('merchant_id=:merchant_id and flag=:flag',
				array(':merchant_id'=>$merchant_id,':flag'=>FLAG_NO));
		if(!empty($model)){
		foreach ( $model as $k => $v ) {
				if (empty ( $v ['p_mid'] )) {
					$data [$v ['id']] ['id'] = $v ['id'];
					$data [$v ['id']] ['name'] = $v ['name'];
					$data [$v ['id']] ['store'] = $this->getStore($merchant_id,$v ['id']);
				}
			}
		}
		//echo '<pre>';
		//print_r($data);
		return $data;
	}
	//得到商户下没有分组的门店
	public function getMerchantStoreWithoutMamagement($merchant_id)
	{
	    $result = array();
	    try {
	        //获取商户下未分组的门店
	        $merchant_store = Store::model()->findAll('merchant_id=:merchant_id and flag=:flag and management_id is null',
	            array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
	        $data = array();
	        if (!empty($merchant_store)) {
	            foreach ($merchant_store as $k => $v) {
	                $data[$k]['id'] = $v->id;
	                $data[$k]['name'] = $v->name;
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

	/**
	 * 获取分组的下级数据
	 */
	public function getManageArr($merchant_id)
	{
		$data = array();
		
		$criteria = new CDbCriteria();
		$criteria -> addCondition('merchant_id=:merchant_id');
		$criteria -> params[':merchant_id'] = $merchant_id;
		$criteria -> addCondition('flag=:flag');
		$criteria -> params[':flag'] = FLAG_NO;
		$criteria -> addCondition('p_mid is :p_mid');
		$criteria -> params[':p_mid'] = null;
		
		$model = Management::model()->findAll($criteria);
		$data1 = '';
		if(!empty($model)){
			foreach ( $model as $k => $v ) {
				  //  $data [$v ['id']] ['id'] = $v ['id'];
				 //   $data [$v ['id']] ['name'] = $v ['name'];
			    $data = $this->getManageForPmid($merchant_id, $v ['id'], $data1);
			}
		}
		return $data;
	}
	
	public function getManageForPmid($merchant_id,$pmid, &$data)
	{

		$man =  Management::model()->findByPk($pmid);
		//$data [$pmid]['store'] = $this->getStore($merchant_id, $pmid);
		$criteria = new CDbCriteria();
		$criteria -> addCondition('merchant_id=:merchant_id');
		$criteria -> params[':merchant_id'] = $merchant_id;
		$criteria -> addCondition('flag=:flag');
		$criteria -> params[':flag'] = FLAG_NO;
		$criteria -> addCondition('p_mid=:p_mid');
		$criteria -> params[':p_mid'] = $pmid;
		$model = Management::model()->findAll($criteria);
		if(!empty($model)){
			foreach ( $model as $k => $v ) {
// 				if(empty($data)) {
// 					$data [$pmid]['sub'] = array('id' => $v ['id'], 'name' => $v ['name']);
// 				} else {
// 					$data [1145]['sub']['sub'] = array('id' => $v ['id'], 'name' => $v ['name']);
// 				}

				$data[$pmid]['name'] = $man['name'];
				$data[$pmid]['firstStore'] = $this->getStore($merchant_id, $pmid);
				$data [$pmid]['sub'][$v ['id']] = array('id' => $v ['id'], 'name' => $v ['name'],'store'=>$this->getStore($merchant_id, $v ['id']));
				//$this->getManageForPmid($merchant_id, $v ['id'], $data);
			}
		} else{
			$data [$pmid] = array('name'=>$man['name'],'firstStore'=>$this->getStore($merchant_id, $man ['id']));
			//$this->getManageForPmid($merchant_id, $pmid, $data);
		}
		
		return $data;
	}
	
	/**
	 * 获取分组下的门店
	 * $merchant_id  商户id
	 * $manage_id    分组id
	 */
	public function getStore($merchant_id,$manage_id)
	{
		$data = array();
		$model = Store::model()->findAll('merchant_id=:merchant_id and management_id=:management_id and flag=:flag',
				array(':merchant_id'=>$merchant_id,':management_id'=>$manage_id,':flag'=>FLAG_NO));
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data[$k]['id'] = $v['id'];
				$data[$k]['name'] = $v['name'];
			}
		}
		return $data;
	}
	
	/**
	 * 获取管理员权限列表
	 * $merchant_id  商户id
	 */
	public function getManageList($merchant_id)
	{
		$result = array();
		$data = array();
		try {
			$criteria = new CDbCriteria();
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> order = 'create_time desc';
			
			$pages = new CPagination(Manager::model()->count($criteria));
			$pages -> pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
		    $this->page = $pages;
		    
			$model = Manager::model()->findAll($criteria);
			
			if(!empty($model)){
				foreach ($model as $k => $v){
					$data['list'][$k]['id'] = $v['id'];
					$data['list'][$k]['account'] = $v['account'];
					$data['list'][$k]['name'] = $v['name'];
					$data['list'][$k]['countStore'] = $this->getCountStore($v['store_id']);
				}
				$result ['status'] = ERROR_NONE;
				$result ['data'] = $data;
			}else{
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '无此数据';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 删除管理员
	 * $manager_id   管理员id
	 */
	public function delManager($manager_id)
	{
		$result = array();
		try {
			$model = Manager::model()->findByPk($manager_id);
			$model -> flag = FLAG_YES;
			$model -> last_time = date('Y-m-d H:i:s');
			if($model -> save()){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	
	/**
	 * 计算管理员管理的门店数量
	 * $store_id            门店id字符串集
	 */
	public function getCountStore($store_id)
	{
		$arr = array();
		if (!empty($store_id)) {
			$arr = explode(',',$store_id);
			$count = 0;
			foreach ($arr as $k => $v){
			    if(!empty($v)){
			        $store_temp = Store::model() -> find('id = :id and flag = :flag',array(
			            ':id' => $v,
			            ':flag' => FLAG_NO
			        ));
			        if(!empty($store_temp)){
			           $count ++;
			        }
			    }
			}
			
			return $count;
		}else{
			return 0;
		}
	}
	
	/**
	 * 玩券管理员登录
	 * $account   管理员账号
	 * $pwd       管理员密码
	 */
	public function managerLogin($account, $pwd)
	{
		$result = array();
		$data = array();
		try {
			$manager = Manager::model()->find('account=:account and pwd=:pwd and flag = :flag',
					array(':account'=>$account,':pwd'=>md5($pwd),':flag'=>FLAG_NO));
			if(!empty($manager)){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
				
				$data['list']['id'] = $manager['id'];
				$data['list']['name'] = $manager['name'];
				$data['list']['account'] = $manager['account'];
				$data['list']['right'] = $manager['right'];
				$data['list']['store_id'] = $manager['store_id'];
				$data['list']['merchant_id'] = $manager['merchant_id'];
				$data['list']['merchant_name'] = isset($manager->merchant->wq_m_name)?$manager->merchant->wq_m_name:'';
				$data['list']['role'] = WQ_ROLE_MANAGER;
				$data['list']['merchant_type_name'] = isset($manager->merchant->gj_product_id)&&!empty($manager->merchant->gj_product_id)?$manager->merchant->gjproduct -> name:'';
				$data['list']['merchant_if_try_out'] = isset($manager->merchant->if_tryout)?$manager->merchant->if_tryout:'';
				//$data['list']['merchant_time_limit'] = isset($manager->merchant->gj_open_status)?$manager->merchant->gj_open_status:'';
				if(isset($manager->merchant->gj_open_status)){
					$data['list']['merchant_time_limit'] = $manager->merchant->gj_open_status == GJ_OPEN_STATUS_OPEN?intval((strtotime($manager->merchant -> gj_end_time)-time())/86400):'';
				}else{
					$data['list']['merchant_time_limit'] = '';
				}
			}else{
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '无此管理员';
			}
			$result['data'] = $data;
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	//管理员创建门店时，将该门店添加到其权限下
	public function AddManageStore($manage_id,$store_id){
	    $result = array();
	    try {
	        $manage = Manager::model() -> findByPk($manage_id);
	        $manage -> store_id = $manage -> store_id.$store_id.',';
	        if($manage -> update()){
	            $result['status'] = ERROR_NONE;
	        }else{
	            throw new Exception('保存失败');
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage(); //错误信息
	    }
	    return json_encode($result);
	}
	
	
}