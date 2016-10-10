<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 2015-12-29
 * @author xyf
 * 管理单元类
 */
class ManageMentC extends mainClass
{
	/**
	 * 获取分组列表
	 * $merchant_id   商户id
	 */
	public function getStoreGroupList($merchant_id)
	{
		$result = array();
		$data = array();
		try {
			$criteria = new CDbCriteria();
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> addCondition('p_mid is :p_mid');
			$criteria -> params[':p_mid'] = null;
			$criteria -> order = 'create_time desc';

			//这里的数据是一级分组
			$manageMent = Management::model ()->findAll($criteria);
			if (!empty($manageMent)){
				foreach ($manageMent as $k=>$v){
					$data['list'][$k]['id'] = $v['id'];
					$data['list'][$k]['p_mid'] = $v['p_mid']; //上级管理单元id
					$data['list'][$k]['name'] = $v['name']; //管理单元名称
					$data['list'][$k]['countStore'] = $this->getCountStore($v['id'],$merchant_id); //分组下的门店数量
					$data['list'][$k]['create_time'] = $v['create_time']; 
					$data['list'][$k]['isHasXJ'] = $this->isHasXJ($v['id']); //是否有下级分组
					$data['list'][$k]['xjGroup'] = $this->getXjGroup($v['id'],$merchant_id); //获取下级分组
					
					//门店不启用收款账号
					if($v -> if_alipay_open == IF_ALIPAY_OPEN_CLOSE){
						//使用上级收款账号
						if($v -> alipay_use_pro == IF_USE_PRO_YES){
							//获取上级收款账号
							
							$merchant = Merchant::model() -> findByPk($v -> merchant_id);
						    if($merchant -> alipay_api_version == ALIPAY_API_VERSION_1){
							  $data['list'][$k]['alipay'] = empty($merchant -> partner)?'未设置':'PID:'.$merchant -> partner.'(上级账号)';
						    }elseif ($merchant -> alipay_api_version == ALIPAY_API_VERSION_2){
							  $data['list'][$k]['alipay'] = empty($merchant -> appid)?'未设置':'APPID:'.$merchant -> appid.'(上级账号)';
						    }
						    }elseif ($v -> alipay_use_pro == IF_USE_PRO_NO){
							//不使用上级收款账号
							$data['list'][$k]['alipay'] = '未设置';
						}
					}elseif ($v -> if_alipay_open == IF_ALIPAY_OPEN_OPEN){
						//门店启用收款账号
						//1.0
						if($v->alipay_api_version == ALIPAY_API_VERSION_1){
							if(!empty($v->alipay_pid)){
								$data['list'][$k]['alipay'] = 'PID:'.$v->alipay_pid;
							}else{
								$data['list'][$k]['alipay'] = '未设置';
							}
						}else if($v->alipay_api_version == ALIPAY_API_VERSION_2){
							//2.0
							if(!empty($v->alipay_appid)){
								$data['list'][$k]['alipay'] = 'APPID:'.$v->alipay_appid;
							}else{
								$data['list'][$k]['alipay'] = '未设置';
							}
						}else{
							$data['list'][$k]['alipay'] = '未设置';
						}
					}
					
					//门店不启用收款账号
					if($v -> if_wx_open == IF_ALIPAY_OPEN_CLOSE){
						//使用上级收款账号
						if($v -> wx_use_pro == IF_USE_PRO_YES){
							//获取上级收款账号
							$merchant = Merchant::model() -> findByPk($v -> merchant_id);
						if($merchant -> wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF){
							$data['list'][$k]['wechat'] = empty($merchant -> wechat_mchid)?'未设置':'商户号:'.$merchant -> wechat_mchid.'(上级账号)';
						}elseif ($merchant -> wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE){
							$data['list'][$k]['wechat'] = empty($merchant -> wechat_mchid)?'未设置':'商户号:'.$merchant -> wechat_mchid.'(上级账号)';
						}
					
						}elseif ($v -> wx_use_pro == IF_USE_PRO_NO){
							//不使用上级收款账号
							$data['list'][$k]['wechat'] = '未设置';
						}
					}elseif ($v -> if_wx_open == IF_ALIPAY_OPEN_OPEN){
						//门店启用收款账号
						//普通商户
						if($v->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF){
							if(!empty($v->wx_mchid)){
								$data['list'][$k]['wechat'] = '商户号:'.$v->wx_mchid;
							}else{
								$data['list'][$k]['wechat'] = '未设置';
							}
							 
						}else if($v->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE){
							//特约商户
							if(!empty($v->t_wx_mchid)){
								$data['list'][$k]['wechat'] = '商户号:'.$v->t_wx_mchid;
							}else{
								$data['list'][$k]['wechat'] = '未设置';
							}
							 
						}else{
							$data['list'][$k]['wechat'] = '未设置';
						}
					}
				}
				$result ['status'] = ERROR_NONE;
				$result ['data'] = $data;
			}else{
			    $result ['status'] = ERROR_NO_DATA;
			    $result['errMsg'] = '无此数据'; //错误信息
	     	}		
		
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 *获取下级分组 
	 */
	public function getXjGroup($managemgent_id,$merchant_id)
	{
		$data = array();
		$model = Management::model()->findAll('p_mid=:p_mid and flag=:flag',
				array(':p_mid'=>$managemgent_id,':flag'=>FLAG_NO));
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data[$k][$managemgent_id]['id'] = $v['id'];
				$data[$k][$managemgent_id]['name'] = $v['name'];
				$data[$k][$managemgent_id]['p_mid'] = $v['p_mid'];
				$data[$k][$managemgent_id]['countStore'] = $this->getCountStore($v['id'],$merchant_id); //分组下的门店数量;
				$data[$k][$managemgent_id]['create_time'] = $v['create_time'];
				$data[$k][$managemgent_id]['isHasXJ'] = $this->isHasXJ($v['id']); //是否有下级分组
				//门店不启用收款账号
				if($v -> if_alipay_open == IF_ALIPAY_OPEN_CLOSE){
					//使用上级收款账号
					if($v -> alipay_use_pro == IF_USE_PRO_YES){
						//获取上级收款账号
							
						$manager = Management::model() -> findByPk($v -> p_mid);
						if($manager -> alipay_api_version == ALIPAY_API_VERSION_1){
							//$data[$k][$managemgent_id]['alipay'] = empty($merchant -> alipay_pid)?'未设置':'PID:'.$merchant -> alipay_pid.'(上级账号)';
							if(!empty($manager -> alipay_pid)){
								$data[$k][$managemgent_id]['alipay'] = 'PID:'.$manager -> alipay_pid.'(上级账号)';
							}else{
								$merchant = Merchant::model()->findByPk($merchant_id);
								$data[$k][$managemgent_id]['alipay'] = empty($merchant -> partner)?'未设置':'PID:'.$merchant -> partner.'(上级账号)';
							}
						}elseif ($manager -> alipay_api_version == ALIPAY_API_VERSION_2){
							//$data[$k][$managemgent_id]['alipay'] = empty($manager -> alipay_appid)?'未设置':'APPID:'.$manager -> alipay_appid.'(上级账号)';
							if(!empty($manager -> alipay_appid)){
								$data[$k][$managemgent_id]['alipay'] = 'APPID:'.$manager -> alipay_appid.'(上级账号)';
							}else{
								$merchant = Merchant::model()->findByPk($merchant_id);
								$data[$k][$managemgent_id]['alipay'] = empty($merchant -> appid)?'未设置':'APPID:'.$merchant -> appid.'(上级账号)';
							}
						}
					}elseif ($v -> alipay_use_pro == IF_USE_PRO_NO){
						//不使用上级收款账号
						$data[$k][$managemgent_id]['alipay'] = '未设置';
					}
				}elseif ($v -> if_alipay_open == IF_ALIPAY_OPEN_OPEN){
					//门店启用收款账号
					//1.0
					if($v->alipay_api_version == ALIPAY_API_VERSION_1){
						if(!empty($v->alipay_pid)){
							$data[$k][$managemgent_id]['alipay'] = 'PID:'.$v->alipay_pid;
						}else{
							$data[$k][$managemgent_id]['alipay'] = '未设置';
						}
					}else if($v->alipay_api_version == ALIPAY_API_VERSION_2){
						//2.0
						if(!empty($v->alipay_appid)){
							$data[$k][$managemgent_id]['alipay'] = 'APPID:'.$v->alipay_appid;
						}else{
							$data[$k][$managemgent_id]['alipay'] = '未设置';
						}
					}else{
						$data[$k][$managemgent_id]['alipay'] = '未设置';
					}
				}
					
				//门店不启用收款账号
				if($v -> if_wx_open == IF_ALIPAY_OPEN_CLOSE){
					//使用上级收款账号
					if($v -> wx_use_pro == IF_USE_PRO_YES){
						//获取上级收款账号
						
						$manager = Management::model() -> findByPk($v -> p_mid);
						if($manager -> wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF){
							//$data[$k][$managemgent_id]['wechat'] = empty($manager -> wx_mchid)?'未设置':'商户号:'.$manager -> wx_mchid.'(上级账号)';
							if(!empty($manager -> wx_mchid)){
								$data[$k][$managemgent_id]['wechat'] = '商户号:'.$manager -> wx_mchid.'(上级账号)';
							}else{
								$merchant = Merchant::model()->findByPk($merchant_id);
								$data[$k][$managemgent_id]['wechat'] = empty($merchant -> wechat_mchid)?'未设置':'商户号:'.$merchant -> wechat_mchid.'(上级账号)';
							}
						}elseif ($merchant -> wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE){
							//$data[$k][$managemgent_id]['wechat'] = empty($manager -> wx_mchid)?'未设置':'商户号:'.$manager -> wx_mchid.'(上级账号)';
							if(!empty($manager -> wx_mchid)){
								$data[$k][$managemgent_id]['wechat'] = '商户号:'.$manager -> wx_mchid.'(上级账号)';
							}else{
								$merchant = Merchant::model()->findByPk($merchant_id);
								$data[$k][$managemgent_id]['wechat'] = empty($merchant -> wechat_mchid)?'未设置':'商户号:'.$merchant -> wechat_mchid.'(上级账号)';
							}
						}
							
					}elseif ($v -> wx_use_pro == IF_USE_PRO_NO){
						//不使用上级收款账号
						$data['list'][$k]['wechat'] = '未设置';
					}
				}elseif ($v -> if_wx_open == IF_ALIPAY_OPEN_OPEN){
					//门店启用收款账号
					//普通商户
					if($v->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF){
						if(!empty($v->wx_mchid)){
							$data[$k][$managemgent_id]['wechat'] = '商户号:'.$v->wx_mchid;
						}else{
							$data[$k][$managemgent_id]['wechat'] = '未设置';
						}
				
					}else if($v->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE){
						//特约商户
						if(!empty($v->t_wx_mchid)){
							$data[$k][$managemgent_id]['wechat'] = '商户号:'.$v->t_wx_mchid;
						}else{
							$data[$k][$managemgent_id]['wechat'] = '未设置';
						}
				
					}else{
						$data[$k][$managemgent_id]['wechat'] = '未设置';
					}
				}
			}
		}
		return $data;
	}
	
	
	/**
	 * 添加分组
	 * $merchant_id     商户id
	 * $contain_store   包含的门店id集合
	 * $manageMent_name 分组名称
	 * $group_type      分组类型（1.新建分组  2.添加到 ）
	 * $level_group     添加到分组id
	 */
	public function addGroup($merchant_id,$contain_store,$manageMent_name,$group_type,$level_group)
	{
		$result = array();
		$flag = 0;
		$errMsg = '';
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//验证分组名称
			if(empty($manageMent_name)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 分组名称必填';
				Yii::app()->user->setFlash('manageMent_name_error','分组名称必填');
			}else{
				$isExit = $this->isExit($manageMent_name,$merchant_id);
				if($isExit){
					$flag = 1;
					$result ['status'] = ERROR_PARAMETER_MISS;
					$errMsg =  $errMsg .' 分组名已经存在';
					Yii::app()->user->setFlash('manageMent_name_error','分组名已经存在');
				}
			}
			if ($flag == 1) {
				$result ['errMsg'] = $errMsg;
				return json_encode ( $result );
			}
			
			$model = new Management();
			$model -> merchant_id = $merchant_id;
			$model -> name = $manageMent_name;
			if($group_type == 1){ //如果是新建分组
				$model -> p_mid = '';
			}else{ //如果是添加到分组
			    $model -> p_mid = $level_group;
			}
			$model -> create_time = date('Y-m-d H:i:s');
			if($model->save()){
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
				$result ['errMsg'] = '数据保存失败'; // 错误信息
				throw new Exception("分组数据插入失败");
			}
			if(!empty($contain_store)){
				$arr = explode(',',$contain_store);
				for($i=1;$i<count($arr);$i++){
					$store = Store::model()->findByPk($arr[$i]);
					$store -> management_id = $model->id;
					$store -> last_time = date('Y-m-d H:i:s');
					if($store->update()){
						$result ['status'] = ERROR_NONE; // 状态码
						$result ['errMsg'] = ''; // 错误信息
					}else{
						throw new Exception("门店归类失败");
					}
				}
			}
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 编辑分组
	 * $manage_id       分组id
	 * $merchant_id     商户id
	 * $contain_store   包含的门店id集合
	 * $manageMent_name 分组名称
	 * $group_type      分组类型（1.新建分组  2.添加到 ）
	 * $level_group     添加到分组id
	 */
	public function editGroup($manage_id,$merchant_id, $contain_store, $manageMent_name, $group_type, $level_group)
	{
		$result = array();
		$flag = 0;
		$errMsg = '';
		$model = Management::model()->findByPk($manage_id);
		$transaction = Yii::app()->db->beginTransaction();
		try {
			//验证分组名称
			if(empty($manageMent_name)){
				$flag = 1;
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg =  $errMsg .' 分组名称必填';
				Yii::app()->user->setFlash('manageMent_name_error','分组名称必填');
			}else{
				$isExit = $this->isExit($manageMent_name,$merchant_id,$manage_id);
				if($isExit){
					$flag = 1;
					$result ['status'] = ERROR_PARAMETER_MISS;
					$errMsg =  $errMsg .' 分组名已经存在';
					Yii::app()->user->setFlash('manageMent_name_error','分组名已经存在');
				}
			}
			if($flag == 1){
				$result['errMsg'] = $errMsg;
				return json_encode($result);
			}
			
			
			$model -> merchant_id = $merchant_id;
			$model -> name = $manageMent_name;
			if($group_type == 1){ //如果是新建分组
				$model -> p_mid = '';
			}else{ //如果是添加到分组
				$model -> p_mid = $level_group;
			}
			$model -> last_time = date('Y-m-d H:i:s');
			if($model->save()){
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
				$result ['errMsg'] = '数据保存失败'; // 错误信息
				throw new Exception("分组数据插入失败");
			}
			//获取该分组下的原有门店和现在门店（$contain_store）的不同门店,不同门店要么是从该分组剔除的，要么是新增的
			$diff = $this->getDiffStore($manage_id,$contain_store);//print_r($diff);exit;
			if(!empty($diff)){
				for($i=0;$i<count($diff);$i++){
					$store = Store::model()->findByPk($diff[$i]);
					if($store->management_id == $manage_id){ //从该分组剔除的
						$store -> management_id = '';
					}else{ //新增的门店
						$store->management_id = $manage_id;
					}
					$store -> last_time = date('Y-m-d H:i:s');
					if($store->update()){
						$result ['status'] = ERROR_NONE; // 状态码
						$result ['errMsg'] = ''; // 错误信息
					}else{
						throw new Exception("门店归类失败");
					}
				}
			}
			$transaction->commit();
		} catch (Exception $e) {
			$transaction->rollback();
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 删除分组
	 * $manage_id       分组id
	 * $merchant_id     商户id
	 */
	public function delGroup($manage_id,$merchant_id)
	{
		$result = array();
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$manage = Management::model()->findByPk($manage_id);
			$manage -> flag = FLAG_YES;
			$manage -> last_time = date('Y-m-d H:i:s');
			if($manage -> update()){
				$result ['status'] = ERROR_NONE; // 状态码
				$result ['errMsg'] = ''; // 错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
				$result ['errMsg'] = '数据保存失败'; // 错误信息
				throw new Exception("分组删除失败");
			}
			
			//该分组下的门店
			$store = Store::model()->findAll('management_id=:management_id and flag=:flag',
					array(':management_id'=>$manage_id,':flag'=>FLAG_NO));
			//获取该分组的上级分组id
			$sj_manage_id = $manage -> p_mid;
			if(empty($sj_manage_id)){ //如果为空，则该分组是一级分组
				foreach ($store as $k=>$v){
					$v -> management_id = '';
					$v -> last_time = date('Y-m-d H:i:s');
					if($v->update()){
						$result ['status'] = ERROR_NONE; // 状态码
						$result ['errMsg'] = ''; // 错误信息
					}else{
						$result ['status'] = ERROR_SAVE_FAIL; // 状态码
						$result ['errMsg'] = '数据保存失败'; // 错误信息
						throw new Exception("门店重新归类失败");
					}
				}
			}else{
				foreach ($store as $k=>$v){
					$v -> management_id = $sj_manage_id;
					$v -> last_time = date('Y-m-d H:i:s');
					if($v->update()){
						$result ['status'] = ERROR_NONE; // 状态码
						$result ['errMsg'] = ''; // 错误信息
					}else{
						$result ['status'] = ERROR_SAVE_FAIL; // 状态码
						$result ['errMsg'] = '数据保存失败'; // 错误信息
						throw new Exception("门店重新归类失败");
					}
				}
			}
			$transaction->commit();
		} catch (Exception $e) {
			$transaction -> rollback();
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage();
		}
		return json_encode($result);
	}
	
	
	
	/**
	 * 获取该分组下的原有门店和现在门店（$contain_store）的不同门店
	 * $manage_id       分组id
	 * $contain_store   现在包含的门店
	 */
	public function getDiffStore($manage_id,$contain_store)
	{
		$a = array();
		$b = array();
		$store = Store::model()->findAll('management_id=:management_id and flag=:flag',
				array(':management_id'=>$manage_id,':flag'=>FLAG_NO));
		if (!empty($store)){
			foreach ($store as $k=>$v){
				$a[$k] = $v['id'];
			}
		}
		
		if(!empty($contain_store) && $contain_store!=','){
			$arr = explode(',',$contain_store);
			for($i=1;$i<count($arr);$i++){
				$b[$i-1] = $arr[$i];
			}
		}
		
		$c = array_merge(array_diff($a,$b),array_diff($b,$a));
		
		return $c;
	}
	
	/**
	 * 判断该分组是否有下级分组
	 * $manage_id       分组id
	 * return true(有下级)
	 */
	public function isHasXJ($manage_id)
	{
		$model = Management::model()->findAll('p_mid=:p_mid and flag=:flag',
				array(':p_mid'=>$manage_id,':flag'=>FLAG_NO));
		if(count($model)>0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 判断该商户下是否有同名的分组
	 * $manageMent_name        分组名称
	 * $merchant_id            商户id
	 * $manage_id              分组id
	 * return true（存在）   false（不存在）
	 * 
	 */
	public function isExit($manageMent_name,$merchant_id,$manage_id=NULL)
	{
		if(empty($manage_id)){
		$model = Management::model ()->findAll ( 'merchant_id=:merchant_id and flag=:flag and name=:name', array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO,
					':name' => trim ( $manageMent_name ) 
			) );
			
		}else{
			$model = Management::model ()->findAll ( 'merchant_id=:merchant_id and flag=:flag and name=:name and id!=:id', array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO,
					':name' => trim ( $manageMent_name ),
					':id'=>$manage_id
			) );
			
		}
		
		if (count ( $model ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 分组详情
	 */
	public function getGroupDetail($manage_id,$merchant_id)
	{
		$result = array();
		$data = array();
		$model = Management::model()->findByPk($manage_id);
		try {
			if(!empty($model)){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
				$data['list']['id'] = $model['id'];
				$data['list']['name'] = $model['name'];
				$data['list']['p_mid'] = $model['p_mid'];
				$data['list']['create_time'] = $model['create_time'];
				$data['list']['store_name'] = $this->getStoreName($model['id'],$merchant_id); //获取分组里的门店
			}else{
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('数据不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		$result['data'] = $data;
		return json_encode ( $result );
	}
	
	/**
	 * 获取分组里的门店
	 */
	public function getStoreName($manage_id,$merchant_id)
	{
		$store_name = array();
		$store = Store::model()->findAll('management_id=:management_id and merchant_id=:merchant_id and flag=:flag',
				array(':management_id'=>$manage_id,':merchant_id'=>$merchant_id,':flag'=>FLAG_NO));
		foreach ($store as $k=>$v){
			$store_name[$v->id]['id'] = $v->id;
			$store_name[$v->id]['name'] = $v->name;
			$store_name[$v->id]['address'] = $v->address;
			$store_name[$v->id]['number'] = $v->number;
		}
		return $store_name;
	}
	
	
	/**
	 * 分组下的门店数量
	 * $management_id    分组id
	 * $merchant_id      商户id
	 */
	public function getCountStore($management_id,$merchant_id)
	{
		$store = Store::model()->findAll('management_id=:management_id and flag=:flag and merchant_id=:merchant_id',array(
				':merchant_id'=>$merchant_id,
				':management_id'=>$management_id,
				':flag' => FLAG_NO
		));
		return count($store);
	}
	
	/**
	 * 获取一级分组
	 * $merchant_id      商户id
	 */
	public function getFirstGroup($merchant_id)
	{
		$result = array();
		$manageMent = Management::model ()->findAll ( 'merchant_id=:merchant_id and flag=:flag',
				array (
						':merchant_id' => $merchant_id,
						':flag' => FLAG_NO
				) );
		if(!empty($manageMent)){
			foreach ($manageMent as $k=>$v){
				if(empty($v->p_mid)){
			     	$result[$v->id] = $v -> name;
				}
			}
		}
		return $result;
	}
	
	/**
	 * 根据主键获取门店信息
	 * $store_id  门店id
	 */
	public function getStore($store_id)
	{
		$result = array();
		$data = array();
		try {
			$model = Store::model()->findByPk($store_id);
			if(!empty($model)){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
	
				$data['list']['id'] = $model['id'];
				$data['list']['name'] = $model['name'];
				$data['list']['number'] = $model['number'];
				$data['list']['address'] = $model['address'];
			}else{
				$result ['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '无此数据'; //错误信息
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		$result['data'] = $data;
		return json_encode($result);
	}
	
	/**
	 * 获取分组的收款账号信息（支付宝）
	 */
	public function getGroupAlipay($id)
	{
		$result = array();
		try {
			$model = Management::model() -> findByPk($id);
			if(!empty($model)){
				$data = array();
				$data['id'] = $model -> id;
				$data['if_alipay_open'] = $model -> if_alipay_open;
				$data['alipay_use_pro'] = $model -> alipay_use_pro;
				$data['alipay_api_version'] = $model -> alipay_api_version;
				$data['alipay_pid'] = $model -> alipay_pid;
				$data['alipay_key'] = $model -> alipay_key;
				$data['alipay_appid'] = $model -> alipay_appid;

				$merchant = Merchant::model() -> findByPk($model -> merchant_id);
				$data['encrypt_id'] = $merchant -> encrypt_id;
				 
				$result['data'] = $data;
				$result['status'] = ERROR_NONE;
				 
			}else{
				throw new Exception('该分组不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 设置分组支付宝收款账号
	*/
	public function setGroupAlipay($id,$alipay_api_version,$alipay_pid = '',$alipay_key = '',$alipay_appid = ''){
		$result = array();
		try {
			$model =  Management::model() -> findByPk($id);
			if(!empty($model)){
				$model -> alipay_api_version = $alipay_api_version;
				if($alipay_api_version == ALIPAY_API_VERSION_1){
					$model -> alipay_pid = $alipay_pid;
					$model -> alipay_key = $alipay_key;
				}elseif ($alipay_api_version == ALIPAY_API_VERSION_2){
					$model -> alipay_appid = $alipay_appid;
				}
				if($model -> update()){
					$result['status'] = ERROR_NONE;
				}else{
					throw new Exception('数据保存失败');
				}
			}else{
				throw new Exception('该分组不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 获取分组的收款账号信息（微信）
	 */
	public function getGroupWechatPay($id)
	{

		$result = array();
		try {
			$model =  Management::model() -> findByPk($id);
			if(!empty($model)){
				$data = array();
				$data['id'] = $model -> id;
				$data['if_wx_open'] = $model -> if_wx_open;
				$data['wx_use_pro'] = $model -> wx_use_pro;
				$data['wx_merchant_type'] = $model -> wx_merchant_type;
				$data['wx_apiclient_cert'] = $model -> wx_apiclient_cert;
				$data['wx_apiclient_key'] = $model -> wx_apiclient_key;
				$data['wx_appid'] = $model -> wx_appid;
				$data['wx_appsecret'] = $model -> wx_appsecret;
				$data['wx_api'] = $model -> wx_api;
				$data['wx_mchid'] = $model -> wx_mchid;
				$data['t_wx_appid'] = $model -> t_wx_appid;
				$data['t_wx_mchid'] = $model -> t_wx_mchid;
				 
				$result['data'] = $data;
				$result['status'] = ERROR_NONE;
		
			}else{
				throw new Exception('该分组不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
		
	}
	
	/**
	 * 设置分组微信收款账号
	 */
	public function setGroupWechatPay($id,$wx_merchant_type,$appid = '',$appsecret = '',$mchid = '',$api_key='',$t_appid = '',$t_mchid = '')
	{
    	$result = array();
    	try {
    		$model =  Management::model() -> findByPk($id);
    		if(!empty($model)){
    			$model -> wx_merchant_type = $wx_merchant_type;
    			if($wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF){
    				$model -> wx_appid = $appid;
    				$model -> wx_appsecret = $appsecret;
    				$model -> wx_mchid = $mchid;
    				$model -> wx_api = $api_key;
    			}elseif ($wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE){
    				$model -> t_wx_appid = $t_appid;
    				$model -> t_wx_mchid = $t_mchid;
    			}
    			if($model -> update()){
    				$result['status'] = ERROR_NONE;
    			}else{
    				throw new Exception('数据保存失败');
    			}
    		}else{
    			throw new Exception('该分组不存在');
    		}
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 设置分组收款账号是否使用上级收款账号
     * $id 分组id
     * $state 状态
     */
    public function setGroupPayUse($id,$state,$type)
    {

    	$result = array();
    	try {
    		$model =  Management::model() -> findByPk($id);
    		if(!empty($model)){
    			if($state == 'true'){
    				//开启
    				if($type == 'alipay'){
    					$model -> alipay_use_pro = IF_ALIPAY_OPEN_OPEN;
    				}elseif ($type == 'wechat'){
    					$model -> wx_use_pro = IF_ALIPAY_OPEN_OPEN;
    				}
    			}else if($state == 'false'){
    				//关闭
    				if($type == 'alipay'){
    					$model -> alipay_use_pro = IF_ALIPAY_OPEN_CLOSE;
    				}elseif ($type == 'wechat'){
    					$model -> wx_use_pro = IF_ALIPAY_OPEN_CLOSE;
    				}
    			}
    			if($model -> update()){
    				$result['status'] = ERROR_NONE;
    			}else{
    				throw new Exception('数据保存失败');
    			}
    		}else{
    			throw new Exception('该分组不存在');
    		}
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 设置分组收款账号是否启用
     * $id 分组id
     * $state 状态
     */
    public function setGroupPayOpen($id,$state,$type)
    {

    	$result = array();
    	try {
    		$model =  Management::model() -> findByPk($id);
    		if(!empty($model)){
    			if($state == 'true'){
    				//开启
    				if($type == 'alipay'){
    					$model -> if_alipay_open = IF_ALIPAY_OPEN_OPEN;
    				}elseif ($type == 'wechat'){
    					$model -> if_wx_open = IF_ALIPAY_OPEN_OPEN;
    				}
    			}else if($state == 'false'){
    				//关闭
    				if($type == 'alipay'){
    					$model -> if_alipay_open = IF_ALIPAY_OPEN_CLOSE;
    				}elseif ($type == 'wechat'){
    					$model -> if_wx_open = IF_ALIPAY_OPEN_CLOSE;
    				}
    			}
    			if($model -> update()){
    				$result['status'] = ERROR_NONE;
    			}else{
    				throw new Exception('数据保存失败');
    			}
    		}else{
    			throw new Exception('该分组不存在');
    		}
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    //保存证书路径
    public function updateWechatCert($id,$dir_name,$type){
    	$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
    	try {
    		$model = Management::model()->find('id=:id and flag=:flag', array(
    				':id' => $id,
    				':flag' => FLAG_NO
    		));
    		if (!empty($model)) {
    			if (!empty($id) && !empty($dir_name)) {
    				if($type == 'apiclient_cert.pem')
    					$model -> wx_apiclient_cert = $dir_name;
    				else if($type == 'apiclient_key.pem')
    					$model -> wx_apiclient_key = $dir_name;
    				if ($model -> update()) {
    					$result['status'] = ERROR_NONE;
    				} else {
    					$result['status'] = ERROR_SAVE_FAIL;
    					throw new Exception('上传失败');
    				}
    			}
    		} else {
    			$result['status'] = ERROR_NO_DATA;
    			$result['errMsg'] = '无此数据';
    		}
    	}
    	catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    
    //获取商户门店分组
    /*
     * $id 商户id
     * $management_id 门店分组id
     * */
    public function getMerchantManagement($id='',$management_id=''){
    	$result = array();
    	try {
    		if(!empty($id)){
    			$management = Management::model() -> findAll('merchant_id = :merchant_id and flag = :flag',array(
    					':merchant_id' => $id,
    					':flag' => FLAG_NO
    			));
    		}elseif (!empty($management_id)){
    			$management = Management::model() -> findAll('p_mid = :p_mid and flag = :flag',array(
    					':p_mid' => $management_id,
    					':flag' => FLAG_NO
    			));
    		}
    		$data = array();
    		foreach ($management as $k => $v){
    			if(empty($v -> p_mid) && !empty($id)){
    				$data[$k]['id'] = $v -> id;
    				$data[$k]['name'] = $v -> name;
    			}elseif (!empty($management_id)){
    				$data[$k]['id'] = $v -> id;
    				$data[$k]['name'] = $v -> name;
    			}
    		}
    		$result['data'] = $data;
    		$result['status'] = STATUS_SUCCESS;
    		$result['errMsg'] = '';
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
//     		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 获取相关分组
     * @param unknown $management_id
     * @return string
     */
    public function getRelateManagement($management_id) {
    	$result = array();
    	try {
    		$model = Management::model()->find('id = :id and flag = :flag', 
    				array(':id' => $management_id, ':flag' => FLAG_NO));
    		if (empty($model)) {
    			throw new Exception('数据不存在');
    		}
    		
    		$merchant_id = $model['merchant_id'];
    		//查询同商户的父分组
    		$pList = Management::model()->findAll('merchant_id = :merchant_id and flag = :flag', 
    				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
    		$pData = array();
    		foreach ($pList as $k => $v) {
    			$pData[$k]['id'] = $v['id'];
    			$pData[$k]['name'] = $v['name'];
    		}
    		
    		//是否父分组
    		if (empty($model['p_mid'])) {
    			$pid = $model['id'];
    		}else {
    			$pid = $model['p_mid'];
    		}
    		//查询同父分组的子分组
    		$cList = Management::model()->findAll('p_mid = :p_mid and flag = :flag', 
    				array(':p_mid' => $pid, ':flag' => FLAG_NO));
    		$cData = array();
    		foreach ($cList as $k => $v) {
    			$cData[$k]['id'] = $v['id'];
    			$cData[$k]['name'] = $v['name'];
    		}
    		
    		$result['parent'] = $pData;
    		$result['child'] = $cData;
    		$result['pid'] = $pid;
    		$result['status'] = STATUS_SUCCESS;
    		$result['errMsg'] = '';
    	
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
}