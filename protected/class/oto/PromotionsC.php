<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 促销活动类
 */

class PromotionsC extends mainClass
{
	//商品分页
	public $page=null;
	//商品编辑分页
	public $pagegroup=null;
	
	/**
	 * 获取属性
	 */
	public function getPromotionsAttributes()
	{
		return Activity::model()->attributes;
	}
	
	/**
	 * 获取活动信息--编辑页面
	 */
	public function getPromotions($id)
	{
		$model = Activity::model()->find('id = :id', array('id'=>$id));
			
		return $model;
	}
	
	/**
	 * 获取活动信息--wap页面 
	 */
	public function getPromotionsIntime($id)
	{
		$now = date("Y-m-d h:i:s");
		$model = Activity::model()->find('id = :id and end_time > :now', array('id'=>$id, ':now'=>$now));
		 
		return $model;
	}
	
	/**
	 * 获取优惠券信息
	 */
	public function getCoupons($id)
	{
		$model = Coupons::model()->findByPk($id);
			
		return $model;
	}
	
	/**
	 * 获取活动中奖信息
	 */
	public function getPromotionsRecord($id)
	{
		$model = ActivityRecord::model()->findByPk($id);
			
		return $model;
	}
	
	/**
	 * 获取活动列表
	 */
	public function getPromotionsList($merchant_id, $type)
	{
		$result = array();
		$data = array();
		try {
			$criteria = new CDbCriteria();
			$criteria->addCondition('merchant_id=:merchant_id and flag=:flag and type = :type');
			$criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':type' => $type);
			//显示分页
			$pages = new CPagination(Activity::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$criteria->order = 'create_time DESC';
			$model = Activity::model()->findAll($criteria);
			
			$result['status'] = ERROR_NONE;
			$result['data'] = $model;
			$result['errMsg'] = '';
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return $result;
	}

	/**
	 * 删除活动信息
	 */
	public function promotionsDel($id)
	{
		$result = array();
		try {
			$model = Activity::model()->findByPk($id);
			$model['flag'] = FLAG_YES;
			if ($model->update()){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}	
		
		return $result;
	}
	
	/**
	 * 获取活动信息
	 */
	public function addPromotions($id, $type, $name, $start_time, $end_time, $first_prize, $first_prize_num, $first_prize_probability, $second_prize, $second_prize_num, $second_prize_probability, $third_prize, $third_prize_num, $third_prize_probability, $fourth_prize, $fourth_prize_num, $fourth_prize_probability, $fifth_prize, $fifth_prize_num, $fifth_prize_probability, $if_show_num, $everyday_num, $everyone_num, $everyone_everyday_num, $illustrate)
	{
		$result = array();
		try {
			if (!empty($id)){
				$model = Activity::model()->findByPk($id);
			}else {
				$model = new Activity();	
			}
			$model['name'] = $name;
			$model['merchant_id'] = Yii::app()->session['merchant_id'];
			$model['type'] = $type;
			$model['start_time'] = $start_time;
			$model['end_time'] = $end_time;
			$model['first_prize'] = $first_prize;
			$model['first_prize_num'] = $first_prize_num;
			$model['first_prize_probability'] = $first_prize_probability;
			$model['second_prize'] = $second_prize;
			$model['second_prize_num'] = $second_prize_num;
			$model['second_prize_probability'] = $second_prize_probability;
			$model['third_prize'] = $third_prize;
			$model['third_prize_num'] = $third_prize_num;
			$model['third_prize_probability'] = $third_prize_probability;
			$model['fourth_prize'] = $fourth_prize;
			$model['fourth_prize_num'] = $fourth_prize_num;
			$model['fourth_prize_probability'] = $fourth_prize_probability;
			$model['fifth_prize'] = $fifth_prize;
			$model['fifth_prize_num'] = $fifth_prize_num;
			$model['fifth_prize_probability'] = $fifth_prize_probability;
			$model['if_show_num'] = $if_show_num;
			$model['everyday_num'] = $everyday_num;
			$model['everyone_num'] = $everyone_num;
			$model['everyone_everyday_num'] = $everyone_everyday_num;
			$model['illustrate'] = $illustrate;
			$model['create_time'] = date('Y-m-d');
			
			if ($model -> save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
			}
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return $result;
	}
	
	/**
	 * 保存中奖信息
	 */
	public function SavePrizeId($id, $prize_type, $source, $open_id)
	{
		$resutl = array();
		try {
			$model = new ActivityRecord();
			
			$model->prize_type = $prize_type;
			$model->activity_id = $id;
			if ($source == "alipay_wallet") {
				$model->alipay_openid = $open_id;
			}elseif ($source == "wechat"){
				$model->wechat_openid = $open_id;
			}
			$model->create_time = date("Y-m-d H:i:s");
			if ($model->save()){
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
				$result['data'] = $model->id;
			}else {
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return $result;
	}
	
	/**
	 * 保存中奖者号码
	 */
	public function savePhoneNum($id, $user_phone)
	{
		$result = array();
		try {
			$model = ActivityRecord::model()->findByPk($id);
			if (!empty($model)) {
				$model->user_phone = $user_phone;
				if ($model->update()){
					$result['status'] = ERROR_NONE;
				}else {
					$result['status'] = ERROR_SAVE_FAIL;
				}
			}else {
				$result['status'] = ERROR_NO_DATA;
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return $result;
	}
	
	/**
	 * 获取优惠券列表
	 */
	public function getCouponsList($merchant_id, $title)
	{
		$result = array();
		try {
			$time = date('Y-m-d H:i:s');
			
			$criteria = new CDbCriteria();
			$criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
			$criteria->params = array(':merchant_id' => $merchant_id,':flag' => FLAG_NO);
			
			if(!empty($title)) {
				$criteria->compare('title', $title, true);
			}
			
			//显示分页
			$pages = new CPagination(Coupons::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$coupons = Coupons::model() -> findAll($criteria);
			$data = array();
			if (!empty($coupons)) {
				foreach ($coupons as $key => $value){
					if ($value['time_type'] == VALID_TIME_TYPE_FIXED  && $value['end_time'] < $time) {
						continue;
					}
					$data[$value['id']]['title'] = $value['title'];
					$data[$value['id']]['create_time'] = $value['create_time'];
				}
			}
			
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = "";
			$result['data'] = $data;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return $result;
	}
	
	/**
	 * 获取优惠券名字
	 */
	public function getCouponsTitle($id)
	{
		$result = array();
		$coupons_arr = array();
		$title_arr = array();
		try {
			$promotions_model = $this->getPromotions($id);
			$coupons_arr[PRIZE_TYPE_FIRST] = $promotions_model['first_prize'];
			$coupons_arr[PRIZE_TYPE_SECOND] = $promotions_model['second_prize'];
			$coupons_arr[PRIZE_TYPE_THIRD] = $promotions_model['third_prize'];
			$coupons_arr[PRIZE_TYPE_FORTH] = $promotions_model['fourth_prize'];
			$coupons_arr[PRIZE_TYPE_FIFTH] = $promotions_model['fifth_prize'];
			foreach ($coupons_arr as $k => $v){
				$coupons_model = $this->getCoupons($v);
				$title_arr[$k]['id'] = $coupons_model['id'];
				$title_arr[$k]['title'] = $coupons_model['title'];
			}
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = "";
			$result['data'] = $title_arr;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return $result;
	}
	
	/**
	 * 抽奖记录
	 */
	public function getRecord($id, $prize_type, $phone_num)
	{
		$result = array();
		try {
			$criteria = new CDbCriteria();
			$criteria->addCondition('activity_id=:activity_id');
			$criteria->params = array(':activity_id' => $id);
				
			if(!empty($prize_type)) {
				$criteria->addCondition('prize_type=:prize_type');
				$criteria->params[':prize_type'] = $prize_type;
			}
			if(!empty($phone_num)) {
				$criteria->compare('user_phone', $phone_num, true);
			}
			$criteria->order = 'create_time DESC';
			
			//显示分页
			$pages = new CPagination(ActivityRecord::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			
			$record = ActivityRecord::model()->findAll($criteria);
			
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = "";
			$result['data'] = $record;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return $result;
	}
	
}