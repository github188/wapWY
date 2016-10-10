<?php
include_once(dirname(__FILE__).'/../mainClass.php');

class U extends mainClass
{
	public $page = null;
	/**
	 * 获取会员分组列表
	 * $merchant_id 商户id
	 */
	public function getUserGroupList($merchant_id)
	{
		$result = array();
		$data = array();
		try {
			$criteria = new CDbCriteria();
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			$criteria -> order = 'create_time DESC';
			
			//分页
			$pages = new CPagination(UserGroup::model()->count($criteria));
			$pages -> pageSize = Yii::app() -> params['perPage'];
			$pages -> applyLimit($criteria);
			$this->page = $pages;
			
			$userGroup = UserGroup::model()->findAll($criteria);
			if (!empty($userGroup)){
				foreach ($userGroup as $k => $v){
					$data['list'][$k]['id'] = $v['id'];
					$data['list'][$k]['name'] = $v['name']; //组名称
					$data['list'][$k]['per_count'] = $this->getPerCount($v['id']); //分组下的人数
					$data['list'][$k]['percentage_per_count'] = $this->getPercentagePerCount($v['id'],$merchant_id); //分组下人数占的百分比
					$data['list'][$k]['create_time'] = $v['create_time'];
				}
				$result ['status'] = ERROR_NONE;
				$result ['data'] = $data;
			}else{
			    $result ['status'] = ERROR_NO_DATA;
			    $result['errMsg'] = '无此数据'; //错误信息
		}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取某个分组下的人数
	 */
	private function getPerCount($userGroupId)
	{
		$count = 0;
		$model = Group::model ()->findAll ( 'flag=:flag and group_id=:group_id', array (
				':flag' => FLAG_NO,
				':group_id' => $userGroupId 
		) );
		$count = count($model);
		return $count;
	}
	
	/**
	 * 分组下人数占的百分比
	 */
	private function getPercentagePerCount($userGroupId,$merchant_id)
	{
		$percentage = 0;
		//计算商户下的总会员人数$sum
		$sum = User::model ()->count ( 'flag=:flag and merchant_id=:merchant_id', array (
				':flag' => FLAG_NO,
				':merchant_id' => $merchant_id 
		));

		$count = $this->getPerCount($userGroupId); //分组下的人数
		if($sum == 0){
			$percentage = 0;
		}else{
			$percentage = round(($count/$sum)*100,2);
		}
		return $percentage;
	}
	
	/**
	 * 删除会员管理分组
	 */
	public function delUserGroup($userGroupId)
	{
		$result = array();
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$model = UserGroup::model()->findByPk($userGroupId);
			$model -> flag = FLAG_YES;
			if(!$model -> update()){
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据删除失败';
				throw new Exception('会员管理分组删除失败');
			}
			$group = Group::model()->findAll('flag=:flag and group_id=:group_id',array(':flag'=>FLAG_NO,':group_id'=>$userGroupId));
			foreach ($group as $k=>$v){
				$v['flag'] = FLAG_YES;
				if(!$v->update()){
					$result['status'] = ERROR_SAVE_FAIL;
					$result['errMsg'] = '数据删除失败';
					throw new Exception('会员管理分组删除失败');
				}
			}
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
			$result['data'] = '';
			$transaction->commit(); //数据提交
		} catch (Exception $e) {
			$transaction->rollback();
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 *  新建会员管理分组  (自定义分组)
	 */
	public function addUserGroupSelf($merchant_id,$name)
	{
		$result = array();
		$flag = 1;
		$errMsg = '';
		try {
			$userGroup = new UserGroup();
			if(empty($name)){
				$flag = 2;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 分组名必填';
				Yii::app()->user->setFlash('name_error','分组名必填');
			}else {
				$model = UserGroup::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name', array (
						':flag' => FLAG_NO ,
						':merchant_id' => $merchant_id,
						':name' => trim($name)
				) );
				if(count($model)>0){
					$flag = 2;
					$result ['status'] = ERROR_PARAMETER_MISS;
					$errMsg =  $errMsg .' 分组名已存在';
					Yii::app()->user->setFlash('name_error','分组名已存在');
				}
			}
			
			if ($flag == 2) {
				$result ['errMsg'] = $errMsg;
				return json_encode ( $result );
			}
			
			$userGroup -> merchant_id = $merchant_id;
			$userGroup -> name = $name;
			$userGroup -> create_time = date('Y-m-d H:i:s');
			if($userGroup->save()){
				$result ['status'] = ERROR_NONE; // 状态码
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			}
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 新建会员管理分组  (根据条件筛选)
	 * $merchant_id    商户id
	 * $name           分组名称
	 * $from           来源（数组）
	 * $sex            性别（数组）
	 * $regist_time    注册时间
	 * $addressHide    地址（字符串）
	 * $userGrade      会员等级（数组）
	 */
	public function addUserGroupSearch($merchant_id,$name,$from,$sex,$regist_time,$addressHide,$userGrade)
	{
		$result = array();
		$transaction = Yii::app()->db->beginTransaction();
		try {			
			$userGroup = new UserGroup();
			$userGroup -> merchant_id = $merchant_id;
			$userGroup -> name = $name;
			$userGroup -> create_time = date('Y-m-d H:i:s');
			if($userGroup->save()){
				$userGroupId = $userGroup -> attributes['id'];
			}else{
				throw new Exception('会员分组添加失败');
			}
			//如果筛选条件都没选   相当于添加自定义分组
			if(empty($from) && empty($sex) && empty($regist_time) && empty($addressHide) && empty($userGrade)){
				$result['status'] = ERROR_NONE;
				$transaction -> commit();
				return json_encode($result); 
			}
			$criteria = new CDbCriteria();
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			
			//来源筛选
			$fromArr = array();
			if(!empty($from)){
				for($i=0;$i<count($form);$i++){
					if($from[$i] == 4){ //4代表其他来源
						$fromArr[] = 4;
						$fromArr[] = USER_FROM_WAP;
					}else{
						$fromArr[] = $from[$i];
					}
				}
			}
			if(!empty($fromArr)){
				$criteria -> addInCondition('t.from', $fromArr);
			}
			
			//性别筛选
			$sexArr = array();
			if(!empty($sex)){
				for($i=0;$i<count($sex);$i++){
					$sexArr[] = $sex[$i];
				}
			}

			if(!empty($sexArr)){
				$sex_condition = '';
				foreach ($sexArr as $sex) {
					if ($sex == SEX_MALE || $sex == SEX_FEMALE) {
						$sex_condition .= " sex = '$sex' or";
					}else {
						$sex_condition .= " sex is null or";
					}
				}
				$sex_condition = trim($sex_condition, 'or');
				$criteria -> addCondition($sex_condition);
			}
			//用户等级筛选
			if(!empty($userGrade)){
				$criteria -> addInCondition('membershipgrade_id', $userGrade);
			}
			//注册时间筛选
			if(!empty($regist_time)){
				$regist_time_arr = explode('-',$regist_time);
				$criteria -> addBetweenCondition('regist_time', $regist_time_arr[0].' 00:00:00', $regist_time_arr[1].' 23:59:59');
			}
			
			$user = User::model()->findAll($criteria);//print_r($model);exit;
			if(!empty($user)){
				foreach ($user as $k=>$v){
					$group = new Group();
					//地址筛选
					if(!empty($addressHide)){
						$addressArr = explode('-',$addressHide);
						for($j=0;$j<count($addressArr)-1;$j++){
							$shopCity = ShopCity::model()->find('code=:code',array(':code'=>$addressArr[$j]));
							if($shopCity['level'] == 1){ //如果为省
								if($shopCity['name'] == $v['province']){
									$group -> group_id = $userGroupId;
									$group -> user_id = $v['id'];
									$group -> create_time = date('Y-m-d H:i:s');
									if($group -> save()){
										
									}else{
										throw new Exception('分组添加失败');
									}
								}
							}elseif ($shopCity['level'] == 2){ //如果为市
								if($shopCity['name'] == $v['city']){
									$group -> group_id = $userGroupId;
									$group -> user_id = $v['id'];
									$group -> create_time = date('Y-m-d H:i:s');
									if($group -> save()){
									
									}else{
										throw new Exception('分组添加失败');
									}
								}
							}
						}
					}else{
						$group -> group_id = $userGroupId;
						$group -> user_id = $v['id'];
						$group -> create_time = date('Y-m-d H:i:s');
						if($group -> save()){
								
						}else{
							throw new Exception('分组添加失败');
						}
					}
				}
			}
			
			$result['status'] = ERROR_NONE;
			$transaction -> commit();
			
		} catch (Exception $e) {
			$transaction->rollback();
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 修改会员管理分组  (自定义分组)
	 */
	public function editUserGroupSelf($merchant_id,$name,$userGroupId)
	{
		$result = array();
		$flag = 1;
		$errMsg = '';
		try {
			$userGroup = UserGroup::model()->findByPk($userGroupId);
			if(empty($name)){
				$flag = 2;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 分组名必填';
				Yii::app()->user->setFlash('name_error','分组名必填');
			}else {
				$model = UserGroup::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name and id!=:id', array (
						':flag' => FLAG_NO ,
						':merchant_id' => $merchant_id,
						':name' => trim($name),
						':id'=>$userGroupId
				) );
				if(count($model)>0){
					$flag = 2;
					$result ['status'] = ERROR_PARAMETER_MISS;
					$errMsg =  $errMsg .' 分组名已存在';
					Yii::app()->user->setFlash('name_error','分组名已存在');
				}
			}
			
			if($flag == 2){
				$result['errMsg'] = $errMsg;
				return json_encode($result);
			}
			
			$userGroup -> name = $name;
			$userGroup -> last_time = date('Y-m-d H:i:s');
			if($userGroup->update()){
				$result['status'] = ERROR_NONE;
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			}
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	/**
	 * 会员管理分组详情
	 */
	public function getUserGroupDetail($userGroupId)
	{
		$model = UserGroup::model()->findByPk($userGroupId);
		return $model;
	}
	
	/**
	 * 获取会员等级
	 */
	public function getUserGrade($merchant_id)
	{
		$result = array();
		$data = array();
		try {
			$model = UserGrade::model ()->findAll ( 'flag=:flag and merchant_id=:merchant_id', array (
					':flag' => FLAG_NO,
					':merchant_id' => $merchant_id 
			) );
			if(!empty($model)){
			foreach ( $model as $k => $v ) {
					$data ['list'] [$v ['id']] = $v ['name'];
				}
				$result ['status'] = ERROR_NONE;
				$result ['data'] = $data;
			}else{
				$result ['status'] = ERROR_NO_DATA;
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 根据条件筛选符合条件的会员
	 * $merchant_id     商户id
	 * $from_alipay     支付宝来源
	 * $from_wx         微信来源
	 * $from_other      其他来源
	 * $sex_male        男性
	 * $sex_femal       女性
	 * $sex_other       未知性别
	 * $userGradeArr    会员等级数组
	 */
	public function searchUser($merchant_id,$from_alipay,$from_wx,$from_other,$sex_male,$sex_femal,$sex_other,$userGradeArr,$regist_time,$addressHideval)
	{
		$result = array();
		$percentage = 0;
		try {
			
			//如果筛选条件都没选
			if(empty($from_alipay) && empty($from_wx) && empty($from_other) && empty($sex_male) && empty($sex_femal) && empty($sex_other) && empty($userGradeArr) && empty($regist_time) && empty($addressHideval)){
				$result['status'] = ERROR_NONE;
				$result['data']['count'] = 0;
				$result['data']['percentage'] = 0;
				return json_encode($result);
			}
			$fromArr = array();
			if (!empty($from_alipay)) {
				$fromArr[] = $from_alipay;
			}
			if (!empty($from_wx)) {
				$fromArr[] = $from_wx;
			}
			if (!empty($from_other)) {
				$fromArr[] = $from_other;
				$fromArr[] = USER_FROM_WAP;
			}
			
			$criteria = new CDbCriteria();
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			
			if(!empty($fromArr)){
				$criteria -> addInCondition('`from`', $fromArr);
			}
			
			$sexArr = array();
			if(!empty($sex_male)){
				$sexArr[] = $sex_male;
			}
			
			if(!empty($sex_femal)){
				$sexArr[] = $sex_femal;
			}
			if (!empty($sex_other)) {
				$sexArr[] = $sex_other;
			}
			if(!empty($sexArr)){
				//$criteria -> addInCondition('sex', $sexArr);
				$sex_condition = '';
				foreach ($sexArr as $sex) {
					if ($sex == SEX_MALE || $sex == SEX_FEMALE) {
						$sex_condition .= " sex = '$sex' or";
					}else {
						$sex_condition .= " sex is null or";
					}
				}
				$sex_condition = trim($sex_condition, 'or');
				$criteria -> addCondition($sex_condition);
			}
// 			if(!empty($sex_other)){
// 				$criteria -> addCondition('sex is :sex','or');
// 				$criteria -> params[':sex'] = null;
// 			}
			if(!empty($regist_time)){
				$regist_time_arr = explode('-',$regist_time);
				$criteria -> addBetweenCondition('regist_time', $regist_time_arr[0].' 00:00:00', $regist_time_arr[1].' 23:59:59');
			}
			if (! empty ( $userGradeArr )) {
				$criteria->addInCondition ( 'membershipgrade_id', $userGradeArr );
			}
			$model = User::model()->findAll($criteria);//print_r($model);exit;
			$count = count($model); //选择用户数量
			if(!empty($model)){
				foreach ($model as $k=>$v){
					if(!empty($addressHideval)){
						$addressArr = explode('-',$addressHideval);
						for($j=0;$j<count($addressArr)-1;$j++){
							$shopCity = ShopCity::model()->find('code=:code',array(':code'=>$addressArr[$j]));
							if($shopCity['level'] == 1){ //如果为省
								if($shopCity['name'] != $v['province']){
									$count--;
								}
							}elseif ($shopCity['level'] == 2){ //如果为市
								if($shopCity['name'] != $v['city']){
									$count--;
								}
							}
						}
					}
				}
			}
			$sumModel = User::model()->findAll('flag=:flag and merchant_id=:merchant_id',array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id));
			$sum = count($sumModel); //总会员数量
			if($sum == 0){
				$percentage = 0;
			}else{
				$percentage = round(($count/$sum)*100,2);
			}
			$result['status'] = ERROR_NONE;
			$result['data']['count'] = $count;
			$result['data']['percentage'] = $percentage;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 添加地址加载到页面
	 */
	public function addAddress($merchant_id,$provinceCode,$cityCode)
	{
		$result = array();
		$province = ShopCity::model()->find('code=:code',array(':code'=>$provinceCode));
		if(empty($cityCode)){
			$result['address'] = $province['name'];
			$result['code'] = $province['code'];
		}else{
			$city = ShopCity::model()->find('code=:code',array(':code'=>$cityCode));
			$result['address'] = $province['name'].','.$city['name'];
			$result['code'] = $city['code'];
		}
		return json_encode($result);
	}
	
	/**
	 * ajax判断分组名是否存在
	 */
	public function checkUserGroupName($merchant_id,$name)
	{
		$isCheck = 1;
		$model = UserGroup::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name', array (
				':flag' => FLAG_NO ,
				':merchant_id' => $merchant_id,
				':name'=>trim($name)
		) );
		if(count($model)>0){
			return 1; //存在
		}else {
			return 2;
		}
	}
	
	/**
	 * 根据生日计算年龄
	 */
	public function getAge($birthday)
	{
		$age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;
		if (date('m', time()) == date('m', strtotime($birthday))){
		
			if (date('d', time()) > date('d', strtotime($birthday))){
				$age++;
			}
		}elseif (date('m', time()) > date('m', strtotime($birthday))){
			$age++;
		}
		return $age;
	}
}