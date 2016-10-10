<?php
include_once(dirname(__FILE__).'/../mainClass.php');

class ActivationC extends mainClass{
	public $page = null;
	
	/**
	 * 激活终端
	 * @param unknown $code
	 * @throws Exception
	 * @return string
	 */
	public function activateTerminal($code) {
		$result = array();
		try {
			//参数验证
			if (empty($code)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数code不能为空');
			}
			
			$model = ActivationCode::model()->find('code = :code and flag = :flag', array(':code' => $code, ':flag' => FLAG_NO));
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('激活码不存在');
			}
			
			//状态检查
			if (strtotime($model['start_time']) > time()) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('激活码未生效');
			}
			if (strtotime($model['end_time']) < time()) {
				if ($model['status'] == ACTIVATION_CODE_STATUS_NORMAL) {
					$model['status'] = ACTIVATION_CODE_STATUS_EXPIRED;
					$model->save();
				}
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('激活码已过期');
			}
			if ($model['status'] == ACTIVATION_CODE_STATUS_LOCK) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('激活码已失效');
			}
			if ($model['status'] == ACTIVATION_CODE_STATUS_EXPIRED) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('激活码已过期');
			}
			if ($model['status'] != ACTIVATION_CODE_STATUS_NORMAL) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('无效的激活码');
			}
			if ($model['used_num'] >= $model['num']) {
				$result['status'] = ERROR_EXCEPTION;
				throw new Exception('可激活数量已达上限');
			}
			
			//使用激活码
			$model['used_num'] += 1;
			if (!$model->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
			$data = array('merchant_id' => $model['merchant_id']);
			
			$result['data'] = $data;
			$result['status'] = ERROR_NONE; //状态码
			$result['errMsg'] = ''; //错误信息
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
	
	
	/*
	 * 获取商户激活码列表
	 * $merchant_id 商户id
	 * */
	public function getActivationCodeList($merchant_id){
		$result = array();
		try {
			//更新激活码状态
			$criteria = new CDbCriteria();
			$criteria->addCondition('merchant_id = :merchant_id');
			$criteria->params[':merchant_id'] = $merchant_id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->addCondition('status = :status');
			$criteria->params[':status'] = ACTIVATION_CODE_STATUS_NORMAL;
			$criteria->addCondition('num = used_num');
			ActivationCode::model()->updateAll(array('status' => ACTIVATION_CODE_STATUS_LOCK), $criteria);
			
			$criteria = new CDbCriteria();
			$criteria->addCondition('merchant_id = :merchant_id');
			$criteria->params[':merchant_id'] = $merchant_id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$criteria->addCondition('status = :status');
			$criteria->params[':status'] = ACTIVATION_CODE_STATUS_NORMAL;
			$criteria->addCondition('end_time < NOW()');
			ActivationCode::model()->updateAll(array('status' => ACTIVATION_CODE_STATUS_EXPIRED), $criteria);
			
			$criteria = new CDbCriteria();
			$criteria->addCondition('merchant_id = :merchant_id');
			$criteria->params[':merchant_id'] = $merchant_id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			
			$criteria->order = 'create_time DESC';
			
			//分页
			$pages = new CPagination(ActivationCode::model()->count($criteria));
			$pages -> pageSize = Yii::app() -> params['perPage'];
			$pages -> applyLimit($criteria);
			$this -> page = $pages;
			
			$code = ActivationCode::model() -> findAll($criteria);
			$data = array();
			foreach ($code as $k => $v){
				$data[$k]['id'] = $v -> id;
				$data[$k]['code'] = $v -> code;
				$data[$k]['num'] = $v -> num;
				$data[$k]['used_num'] = $v -> used_num;
				$data[$k]['status'] = $v -> status;
				$data[$k]['start_time'] = $v -> start_time;
				$data[$k]['end_time'] = $v -> end_time;
				$data[$k]['create_time'] = $v -> create_time;
			}
			$result['data'] = $data;
			$result['status'] = ERROR_NONE;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/*
	 * 创建激活码
	 * 
	 * */
	public function createActivationCode($merchant_id,$num,$start_time,$end_time){
		$result = array();
		try {
			$model = new ActivationCode();
			$model -> merchant_id = $merchant_id;
			//生成9位验证码
			$code =  rand(000000000, 999999999);
			$activationCode = ActivationCode::model() -> find('code =:code and flag =:flag',array(
					':code' => $code,
					':flag' => FLAG_NO
			));
			while (!empty($activationCode)){
				$code =  rand(000000000, 999999999);
				$activationCode = ActivationCode::model() -> find('code =:code and flag =:flag',array(
						':code' => $code,
						':flag' => FLAG_NO
				));
			}
			$model -> code = $code;
			$model -> num = $num;
			$model -> start_time = $start_time;
			$model -> end_time = $end_time;
			$model -> status = ACTIVATION_CODE_STATUS_NORMAL;
			$model -> create_time = new CDbExpression('now()');
			if($model -> save()){
				$result['status'] = ERROR_NONE;
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据库保存失败');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/*
	 * 验证码使失效
	 * $id 验证码id
	 * */
	public function invalidCode($id){
		$result = array();
		try {
			$code = ActivationCode::model() -> findbyPk($id);
			if(!empty($code)){
				$code -> status = ACTIVATION_CODE_STATUS_LOCK;
				if($code -> update()){
					$result['status'] = ERROR_NONE;
				}else{
					$result['status'] = ERROR_SAVE_FAIL;
					throw new Exception('使失效操作失败');
				}
			}else{
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('该验证码不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/*
	 * 删除验证码
	* $id 验证码id
	* */
	public function delectCode($id){
		$result = array();
		try {
			$code = ActivationCode::model() -> findbyPk($id);
			if(!empty($code)){
				$code -> flag = FLAG_YES;
				if($code -> update()){
					$result['status'] = ERROR_NONE;
				}else{
					$result['status'] = ERROR_SAVE_FAIL;
					throw new Exception('删除操作失败');
				}
			}else{
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('该验证码不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	
	
}