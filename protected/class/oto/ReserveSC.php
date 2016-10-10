<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/**
 * 预订类
 *
 */
class ReserveSC extends mainClass{
	/**
	 * 预订
	 * @param unknown $store_id 门店id
	 * @param unknown $surname 	姓
	 * @param unknown $sex 		性别
	 * @param unknown $tel 		联系电话
	 * @param unknown $time 	预订时间
	 * @param unknown $number 	预订人数
	 * @param unknown $remark 	备注
	 * @param unknown $operator_id 操作员id
	 * @param unknown $user_id	用户id
	 * @return string
	 */
	public function reserve($store_id, $surname, $sex, $tel, $time, $number, $remark, $operator_id=NULL, $user_id=NULL) {
		$result = array();
		try {
			//参数验证
			if (empty($store_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数store_id不能为空');
			}
			if (empty($surname)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数surname不能为空');
			}
			if (empty($sex)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数sex不能为空');
			}
			if (empty($tel)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数tel不能为空');
			}
			if (empty($time)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数time不能为空');
			}
			if (empty($number)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数number不能为空');
			}
			//门店验证
			$store = Store::model()->find('id = :id and flag = :flag', 
					array(':id' => $store_id, ':flag' => FLAG_NO));
			if (empty($store)) {
				throw new Exception('传入的门店不存在');
			}
			if ($store['status'] == STORE_STATUS_LOCK) {
				throw new Exception('指定的门店被锁定');
			}
			//操作员验证
			if (!empty($operator_id)) {
				$operator = Operator::model()->findByPk($operator_id);
				if (empty($operator)) {
					throw new Exception('传入的操作员不存在');
				}
				if ($store_id != $operator['store_id']) {
					throw new Exception('指定的操作员非门店下属');
				}
			}
			//用户验证
			if (!empty($user_id)) {
				$user = User::model()->findByPk($user_id);
				if (empty($user)) {
					throw new Exception('传入的用户不存在');
				}
				//用户所属商户
				//TODO
			}
			
			//添加预订记录
			$model = new BookRecord();
			$model['store_id'] = $store_id;
			$model['user_id'] = $user_id;
			$model['book_time'] = $time;
			$model['book_information'] = "$surname@$tel@$sex@$number@$time";
			$model['remark'] = $remark;
			$model['status'] = BOOK_RECORD_STATUS_WAIT;
			$model['operator_id'] = $operator_id;
			$model['create_time'] = date('Y-m-d H:i:s');
			
			if (!$model->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
			$data = array(
					'id' => $model['id'],
					'status' => $model['status'],
					'create_time' => $model['create_time'],
			);
			
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
	 * 处理操作
	 * @param unknown $record_id 		预订记录id
	 * @param unknown $target_status	处理后的状态
	 * @param string $operator_id		操作员id
	 * @throws Exception
	 * @return string
	 */
	public function process($record_id, $target_status, $operator_id=NULL) {
		$result = array();
		try {
			//参数验证
			if (empty($record_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数record_id不能为空');
			}
			if (empty($target_status)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数target_status不能为空');
			}
			//预订记录验证
			$model = BookRecord::model()->find('id = :id and flag = :flag',
					array(':id' => $record_id, ':flag' => FLAG_NO));
			if (empty($model)) {
				throw new Exception('预订记录不存在');
			}
			//操作员验证
			if (!empty($operator_id)) {
				$operator = Operator::model()->findByPk($operator_id);
				if (empty($operator)) {
					throw new Exception('传入的操作员不存在');
				}
				if ($model['store_id'] != $operator['store_id']) {
					throw new Exception('指定的操作员非门店下属');
				}
			}
			//变更状态验证
			$status = $model['status'];
			if ($status != BOOK_RECORD_STATUS_WAIT && $status != BOOK_RECORD_STATUS_ACCEPT) {
				throw new Exception('该记录无法处理');
			}
			if ($status == BOOK_RECORD_STATUS_WAIT) {
				//可变更状态:接单，拒单
				if ($target_status != BOOK_RECORD_STATUS_ACCEPT && $target_status != BOOK_RECORD_STATUS_REFUSE) {
					$result['status'] = ERROR_PARAMETER_FORMAT;
					throw new Exception('无效的操作处理');
				}
			}
			if ($status == BOOK_RECORD_STATUS_ACCEPT) {
				//可变更状态:到店，取消
				if ($target_status != BOOK_RECORD_STATUS_ARRIVE && $target_status != BOOK_RECORD_STATUS_CANCEL) {
					$result['status'] = ERROR_PARAMETER_FORMAT;
					throw new Exception('无效的操作处理');
				}
			}
				
			//修改预订记录
			$model['status'] = $target_status;
				
			if (!$model->save()) {
				$result['status'] = ERROR_SAVE_FAIL;
				throw new Exception('数据保存失败');
			}
			$data = array('id' => $model['id']);
				
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
	 * 获取预订列表
	 * @param unknown $store_id	门店id
	 * @param string $status	筛选状态
	 * @param string $start		筛选开始时间
	 * @param string $end		筛选结束时间
	 * @param string $keyword	筛选关键词
	 * @throws Exception
	 * @return string
	 */
	public function getList($store_id, $status=NULL, $start=NULL, $end=NULL, $keyword=NULL) {
		$result = array();
		try {
			//参数验证
			if (empty($store_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数store_id不能为空');
			}
			
			//门店验证
			$store = Store::model()->find('id = :id and flag = :flag',
					array(':id' => $store_id, ':flag' => FLAG_NO));
			if (empty($store)) {
				throw new Exception('未找到相关门店信息');
			}
			
			$criteria = new CDbCriteria();
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			
			if (!empty($status)) {
				$criteria->addCondition('status = :status');
				$criteria->params[':status'] = $status;
			}
			if (!empty($start)) {
				$criteria->addCondition('book_time >= :start_time');
				$criteria->params[':start_time'] = $start;
			}
			if (!empty($end)) {
				$criteria->addCondition('book_time <= :end_time');
				$criteria->params[':end_time'] = $end;
			}
			if (!empty($keyword)) {
				$criteria->addCondition("book_information like :keyword");
				$criteria->params[':keyword'] = '%'.$keyword.'%';
			}
			
			//分页
			$pages = new CPagination(BookRecord::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$list = BookRecord::model()->findAll($criteria);
			
			$data = array();
			foreach ($list as $model) {
				$info = explode('@', $model['book_information']);
				$name = $info[0];
				$tel = $info[1];
				$sex = $info[2];
				$number = $info[3];
				$data[] = array(
						'id' => $model['id'],
						'book_time' => $model['book_time'],
						'cancel_time' => $model['cancel_time'],
						'create_time' => $model['create_time'],
						'deal_time' => $model['deal_time'],
						'arrive_time' => $model['arrive_time'],
						'name' => $name,
						'tel' => $tel,
						'sex' => $sex,
						'number' => $number,
						'status' => $model['status'],
						'remark' => $model['remark'],
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
	
	/**
	 * 获取预订列表（APP）
	 * @param unknown $store_id	门店id
	 * @param unknown $limit_id 限制id，结果集id小于限制id
	 * @param string $status	筛选状态
	 * @param string $start		筛选开始时间
	 * @param string $end		筛选结束时间
	 * @param string $keyword	筛选关键词
	 * @throws Exception
	 * @return string
	 */
	public function getList4App($store_id, $limit_id, $status=NULL, $start=NULL, $end=NULL, $keyword=NULL) {
		$result = array();
		try {
			//参数验证
			if (empty($store_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数store_id不能为空');
			}
				
			//门店验证
			$store = Store::model()->find('id = :id and flag = :flag',
					array(':id' => $store_id, ':flag' => FLAG_NO));
			if (empty($store)) {
				throw new Exception('未找到相关门店信息');
			}
				
			$criteria = new CDbCriteria();
			$criteria -> order = 'id desc';
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
				
			if (!empty($limit_id)) {
				$criteria->addCondition('id < :id');
				$criteria->params[':id'] = $limit_id;
			}
			if (!empty($status)) {
				$criteria->addCondition('status = :status');
				$criteria->params[':status'] = $status;
			}
			if (!empty($start)) {
				$criteria->addCondition('book_time >= :start_time');
				$criteria->params[':start_time'] = $start;
			}
			if (!empty($end)) {
				$criteria->addCondition('book_time <= :end_time');
				$criteria->params[':end_time'] = $end;
			}
			if (!empty($keyword)) {
				$criteria->addCondition("book_information like :keyword");
				$criteria->params[':keyword'] = '%'.$keyword.'%';
			}
				
			//分页
			$page_size = Yii::app()->params['perPage'];
			$criteria->limit = $page_size ? : 10;
				
			$list = BookRecord::model()->findAll($criteria);
				
			$data = array();
			foreach ($list as $model) {
				$info = explode('@', $model['book_information']);
				$name = $info[0];
				$tel = $info[1];
				$sex = $info[2];
				$number = $info[3];
				$data[] = array(
						'id' => $model['id'],
						'book_time' => $model['book_time'],
						'cancel_time' => $model['cancel_time'],
						'create_time' => $model['create_time'],
						'deal_time' => $model['deal_time'],
						'arrive_time' => $model['arrive_time'],
						'name' => $name,
						'tel' => $tel,
						'sex' => $sex,
						'number' => $number,
						'status' => $model['status'],
						'remark' => $model['remark'],
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
	 * 获取预订列表（PC）
	 * @param unknown $store_id	门店id
	 * @param unknown $page_num 页码
	 * @param string $status	筛选状态
	 * @param string $start		筛选开始时间
	 * @param string $end		筛选结束时间
	 * @param string $keyword	筛选关键词
	 * @throws Exception
	 * @return string
	 */
	public function getList4PC($store_id, $page_num, $status=NULL, $start=NULL, $end=NULL, $keyword=NULL) {
		$result = array();
		try {
			//参数验证
			if (empty($store_id)) {
				$result['status'] = ERROR_PARAMETER_MISS;
				throw new Exception('参数store_id不能为空');
			}
	
			//门店验证
			$store = Store::model()->find('id = :id and flag = :flag',
					array(':id' => $store_id, ':flag' => FLAG_NO));
			if (empty($store)) {
				throw new Exception('未找到相关门店信息');
			}
	
			$criteria = new CDbCriteria();
			$criteria -> order = 'id desc';
			$criteria->addCondition('store_id = :store_id');
			$criteria->params[':store_id'] = $store_id;
			
			if (!empty($status)) {
				$criteria->addCondition('status = :status');
				$criteria->params[':status'] = $status;
			}
			if (!empty($start)) {
				$criteria->addCondition('book_time >= :start_time');
				$criteria->params[':start_time'] = $start;
			}
			if (!empty($end)) {
				$criteria->addCondition('book_time <= :end_time');
				$criteria->params[':end_time'] = $end;
			}
			if (!empty($keyword)) {
				$criteria->addCondition("book_information like :keyword");
				$criteria->params[':keyword'] = '%'.$keyword.'%';
			}
			
			//配置的每页显示数量
			$page_size = Yii::app()->params['perPage'];
			//计算总页数
			$total_num = BookRecord::model()->count($criteria);
			$total_page = ceil($total_num / $page_size);
			//翻页
			$criteria->limit = $page_size ? : 10;
			$page_num += 0;
			if (!empty($page_num) && $page_num > 0) {
				$criteria->offset = $page_size * ($page_num -1);
			}
	
			$list = BookRecord::model()->findAll($criteria);
	
			$data = array();
			foreach ($list as $model) {
				$info = explode('@', $model['book_information']);
				$name = $info[0];
				$tel = $info[1];
				$sex = $info[2];
				$number = $info[3];
				$data[] = array(
						'id' => $model['id'],
						'book_time' => $model['book_time'],
						'cancel_time' => $model['cancel_time'],
						'create_time' => $model['create_time'],
						'deal_time' => $model['deal_time'],
						'arrive_time' => $model['arrive_time'],
						'name' => $name,
						'tel' => $tel,
						'sex' => $sex,
						'number' => $number,
						'status' => $model['status'],
						'remark' => $model['remark'],
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