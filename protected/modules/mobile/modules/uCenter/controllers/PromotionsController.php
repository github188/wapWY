<?php

class PromotionsController extends UCenterController
{
	/**
	 * 促销活动跳转
	 */
	public function actionPromotionsActivity()
	{
		$promotions = new PromotionsC();
		$user = new UserUC();
		$title_arr = array();
		
		$id = $_GET['promotions_id'];
		$encrypt_id = $_GET['encrypt_id'];
		$source = $_GET['source'];
		//微信参数
		$code = !empty($_GET['code']) ? $_GET['code'] : '';
		
		//获取商户信息
		$merchant = $this->getMerchant($encrypt_id);
		$type = $merchant['wechat_type'];
		Yii::app()->session['type'] = $type;
		
		//保存merchant_id source
		Yii::app()->session['merchant_id'] = $merchant['id'];
		$merchant_id = Yii::app()->session['merchant_id'];
		Yii::app()->session['source'] = $source;
			
		$appid = $merchant['appid'];
		$wechat_appid = $merchant['wechat_appid'];
		$wechat_appsecret = $merchant['wechat_appsecret'];
		//获取并保存OpenId
		if ( "alipay_wallet" == $source ) {
			$this->getAliOpenId($appid);
		}elseif ( "wechat" == $source) {
			if( $type == WECHAT_TYPE_SERVICE_AUTH) {
				$this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret);
			}else{
				$fromUsername = $_GET['fromUsername'];
				Yii::app()->session['from_user_name'] = $fromUsername;
			}
		}
		
		$model = $promotions->getPromotions($id);
		if ($model['type'] == PROMOTIONS_TYPE_TURNTABLE) {
			$this->redirect(Yii::app()->createUrl('uCenter/promotions/turntable', array('id'=>$id, 'type'=>$type)));
		}elseif ($model['type'] == PROMOTIONS_TYPE_SCRATCH){
			$this->redirect(Yii::app()->createUrl('uCenter/promotions/scratch', array('id'=>$id, 'type'=>$type)));
		}
		
	}
	
	/**
	 * 大转盘活动
	 */
	public function actionTurntable()
	{
		$promotions = new PromotionsC();
		
		$id = $_GET['id'];
		$type = $_GET['type'];
		$source = Yii::app()->session['source'];
		$model = $promotions->getPromotionsIntime($id);
		if (!empty($model)) {
			//支付宝
			$now_time = date("Y-m-d");
			$play_times = array();
			if ($source == "alipay_wallet") {
				$open_id = Yii::app()->session['ali_open_id'];
				$record = ActivityRecord::model()->findAll('activity_id = :activity_id and alipay_openid = :alipay_openid', array(':activity_id'=>$id, ':alipay_openid'=>$open_id));
				$play_times['day'] = ActivityRecord::model()->count('activity_id = :activity_id and alipay_openid = :alipay_openid and create_time >= :now', array(':activity_id'=>$id, ':alipay_openid'=>$open_id, ':now'=>$now_time));
				$play_times['total'] = ActivityRecord::model()->count('activity_id = :activity_id and alipay_openid = :alipay_openid', array(':activity_id'=>$id, ':alipay_openid'=>$open_id));
				//尚未填写的中奖纪录
				$empty_record = ActivityRecord::model()->find('alipay_openid = :alipay_openid and prize_type!=:prize_type and activity_id=:activity_id and user_phone is Null order by create_time DESC', array(':alipay_openid'=>$open_id, ':prize_type'=>WN_PRIZE_TYPE_NONE, ':activity_id'=>$id));
			}elseif ($source == "wechat"){            
				if ($type == WECHAT_TYPE_SERVICE_AUTH){     //微信服务号
					$open_id = Yii::app()->session['wechat_open_id'];
				}else{               //订阅号 或者 未认证服务号 
					$open_id = Yii::app()->session['from_user_name'];
				}
				$record = ActivityRecord::model()->findAll('activity_id = :activity_id and wechat_openid = :wechat_openid', array(':activity_id'=>$id, ':wechat_openid'=>$open_id));
				$play_times['day'] = ActivityRecord::model()->count('activity_id = :activity_id and wechat_openid = :wechat_openid and create_time >= :now', array(':activity_id'=>$id, ':wechat_openid'=>$open_id, ':now'=>$now_time));
				$play_times['total'] = ActivityRecord::model()->count('activity_id = :activity_id and wechat_openid = :wechat_openid', array(':activity_id'=>$id, ':wechat_openid'=>$open_id));
				//尚未填写的中奖纪录
				$empty_record = ActivityRecord::model()->find('wechat_openid = :wechat_openid and prize_type!=:prize_type and activity_id=:activity_id and user_phone is Null order by create_time DESC', array(':wechat_openid'=>$open_id, ':prize_type'=>WN_PRIZE_TYPE_NONE, ':activity_id'=>$id));
			}
			if (empty($empty_record)) {
				$empty_record = new ActivityRecord();
			}
			
			$title_res = $promotions -> getCouponsTitle($id);
			if ($title_res['status'] == ERROR_NONE) {
				$title_arr = $title_res['data'];
			}
			
			$this->render('turntable', array('model'=>$model, 'play_times'=>$play_times, 'empty_record'=>$empty_record, 'title_arr'=>$title_arr, 'record'=>$record));
		}else {
			$this->render('turntable', array('model'=>$model));
		}
	}
	
	/*
	 * 刮刮卡
	*/
	public function actionScratch()
	{
		$promotions = new PromotionsC();
		
		$id = $_GET['id'];
		$type = $_GET['type'];
		$source = Yii::app()->session['source'];
		$model = $promotions->getPromotionsIntime($id);
		$type = Yii::app()->session['type'];
		if (!empty($model)){
			
			//支付宝
			$now_time = date("Y-m-d");
			$play_times = array();
			if ($source == "alipay_wallet") {
				$open_id = Yii::app()->session['ali_open_id'];
				$record = ActivityRecord::model()->findAll('activity_id = :activity_id and alipay_openid = :alipay_openid', array(':activity_id'=>$id, ':alipay_openid'=>$open_id));
				$play_times['day'] = ActivityRecord::model()->count('activity_id = :activity_id and alipay_openid = :alipay_openid and create_time >= :now', array(':activity_id'=>$id, ':alipay_openid'=>$open_id, ':now'=>$now_time));
				$play_times['total'] = ActivityRecord::model()->count('activity_id = :activity_id and alipay_openid = :alipay_openid', array(':activity_id'=>$id, ':alipay_openid'=>$open_id));
				//尚未填写的中奖纪录
				$empty_record = ActivityRecord::model()->find('alipay_openid = :alipay_openid and prize_type!=:prize_type and activity_id=:activity_id and user_phone is Null order by create_time DESC', array(':alipay_openid'=>$open_id, ':prize_type'=>WN_PRIZE_TYPE_NONE, ':activity_id'=>$id));
			}elseif ($source == "wechat"){
				if ($type == WECHAT_TYPE_SERVICE_AUTH){     //微信服务号
					$open_id = Yii::app()->session['wechat_open_id'];
				}else{               //订阅号 或者 未认证服务号
					$open_id = Yii::app()->session['from_user_name'];
				}
				$record = ActivityRecord::model()->findAll('activity_id = :activity_id and wechat_openid = :wechat_openid', array(':activity_id'=>$id, ':wechat_openid'=>$open_id));
				$play_times['day'] = ActivityRecord::model()->count('activity_id = :activity_id and wechat_openid = :wechat_openid and create_time >= :now', array(':activity_id'=>$id, ':wechat_openid'=>$open_id, ':now'=>$now_time));
				$play_times['total'] = ActivityRecord::model()->count('activity_id = :activity_id and wechat_openid = :wechat_openid', array(':activity_id'=>$id, ':wechat_openid'=>$open_id));
				//尚未填写的中奖纪录
				$empty_record = ActivityRecord::model()->find('wechat_openid = :wechat_openid and prize_type!=:prize_type and activity_id=:activity_id and user_phone is Null order by create_time DESC', array(':wechat_openid'=>$open_id, ':prize_type'=>WN_PRIZE_TYPE_NONE, ':activity_id'=>$id));
			}
			if (empty($empty_record)) {
				$empty_record = new ActivityRecord();
			}
			
			$title_res = $promotions -> getCouponsTitle($id);
			if ($title_res['status'] == ERROR_NONE) {
				$title_arr = $title_res['data'];
			}
				
			$this->render('scratch', array('model'=>$model, 'play_times'=>$play_times, 'empty_record'=>$empty_record, 'title_arr'=>$title_arr, 'record'=>$record));
		}else {
			$this->render('scratch', array('model'=>$model));
		}
	}
	
	/*
	 * 刮刮卡抽奖
	*/
	public function actionGetScratchPrize()
	{
		$promotions = new PromotionsC();
		
		$id = $_GET['id'];
		$model = $promotions->getPromotionsIntime($id);
		$source = Yii::app()->session['source'];
		$type = Yii::app()->session['type'];
		
		if ($source == "alipay_wallet") {
			$open_id = Yii::app()->session['ali_open_id'];
		}elseif ($source == "wechat"){            
			if ($type == WECHAT_TYPE_SERVICE_AUTH){     //微信服务号
				$open_id = Yii::app()->session['wechat_open_id'];
			}else{               //订阅号 或者 未认证服务号
				$open_id = Yii::app()->session['from_user_name'];
			}
		}
		
		//中奖随机数
		$prize_num = rand(0, 10001);
		//中奖概率
		$first_probability = $model->first_prize_probability*100;
		$second_probability = $first_probability + $model->second_prize_probability*100;
		$third_probability = $second_probability + $model->third_prize_probability*100;
		$fourth_probability = $third_probability + $model->fourth_prize_probability*100;
		$fifth_probability = $fourth_probability + $model->fifth_prize_probability*100;
		//中奖个数
		$first_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_FIRST, ':activity_id'=>$id));
		$second_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_SECOND, ':activity_id'=>$id));
		$third_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_THIRD, ':activity_id'=>$id));
		$fourth_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_FORTH, ':activity_id'=>$id));
		$fifth_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_FIFTH, ':activity_id'=>$id));
		//每天中奖名额
		$today_start_time = date("Y-m-d 00:00:00");
		$today_end_time = date("Y-m-d 23:59:59");
		$prize_num_day = ActivityRecord::model()->count('prize_type!=:prize_type and activity_id=:activity_id and create_time>=:start_time and create_time<=:end_time', array(':prize_type'=>PRIZE_TYPE_NONE, ':activity_id'=>$id, ':start_time'=>$today_start_time, ':end_time'=>$today_end_time));

		$title_res = $promotions -> getCouponsTitle($id);
		if ($title_res['status'] == ERROR_NONE) {
			$title_arr = $title_res['data'];
		}
		
		//返回信息
		$data = array();
		//$data['player'] = $player;
		//$data['state'] = WN_SN_STATE_NEW;
		//$data['promotions_action_id'] = $model->id;
		//$sn_num = $this->GetSnNum($model->id);
		$data['error'] = "no";
		//未达到每日发奖次数限制  切没中过奖
		if ($prize_num_day < $model->everyday_num){
			if ($prize_num <= $first_probability && $first_prize_num < $model->first_prize_num){        //一等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_FIRST, $source, $open_id);
				if( $result ){
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['prizelevel'] = "一等奖";
					$data['message'] = $title_arr[PRIZE_TYPE_FIRST]['title'];
				}
			}elseif ($prize_num <= $second_probability && $prize_num > $first_probability && $second_prize_num < $model->second_prize_num){             //二等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_SECOND, $source, $open_id);
				if( $result ){
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['prizelevel'] = "二等奖";
					$data['message'] = $title_arr[PRIZE_TYPE_SECOND]['title'];
				}
			}elseif ($prize_num <= $third_probability && $prize_num > $second_probability && $third_prize_num < $model->third_prize_num){             //三等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_THIRD, $source, $open_id);
				if( $result ){
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['prizelevel'] = "三等奖";
					$data['message'] = $title_arr[PRIZE_TYPE_THIRD]['title'];
				}
			}elseif ($prize_num <= $fourth_probability && $prize_num > $third_probability && $fourth_prize_num < $model->fourth_prize_num){             //四等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_FORTH, $source, $open_id);
				if( $result ){
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['prizelevel'] = "四等奖";
					$data['message'] = $title_arr[PRIZE_TYPE_FORTH]['title'];
				}
			}elseif ($prize_num <= $fifth_probability && $prize_num > $fourth_probability && $fifth_prize_num < $model->fifth_prize_num){             //五等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_FIFTH, $source, $open_id);
				if( $result ){
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['prizelevel'] = "五等奖";
					$data['message'] = $title_arr[PRIZE_TYPE_FIFTH]['title'];
				}
			}else {      //未中奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_NONE, $source, $open_id);
			}
		}else{   //未中奖
			$promotions->SavePrizeId($id, PRIZE_TYPE_NONE, 'wechat', $open_id);
		}
		echo json_encode($data);
	}
	
	/*
	 * 大转盘抽奖
	*/
	public function actionGetprizelevel()
	{
		$promotions = new PromotionsC();
		
		$id = $_GET['id'];
		$model = $promotions->getPromotionsIntime($id);
		$source = Yii::app()->session['source'];
		$type = Yii::app()->session['type'];
		
		if ($source == "alipay_wallet") {
			$open_id = Yii::app()->session['ali_open_id'];
		}elseif ($source == "wechat"){            
			if ($type == WECHAT_TYPE_SERVICE_AUTH){     //微信服务号
				$open_id = Yii::app()->session['wechat_open_id'];
			}else{               //订阅号 或者 未认证服务号
				$open_id = Yii::app()->session['from_user_name'];
			}
		}
		
		//中奖随机数
		$prize_num = rand(0, 10001);
		//中奖概率
		$first_probability = $model['first_prize_probability']*100;
		$second_probability = $first_probability + $model['second_prize_probability']*100;
		$third_probability = $second_probability + $model['third_prize_probability']*100;
		//已中奖个数
		$first_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_FIRST, ':activity_id'=>$id));
		$second_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_SECOND, ':activity_id'=>$id));
		$third_prize_num = ActivityRecord::model()->count('prize_type = :prize_type and activity_id = :activity_id', array(':prize_type'=>PRIZE_TYPE_THIRD, ':activity_id'=>$id));
		//每天中奖名额
		$today_start_time = date("Y-m-d 00:00:00");
		$today_end_time = date("Y-m-d 23:59:59");
		$prize_num_day = ActivityRecord::model()->count('prize_type!=:prize_type and activity_id=:activity_id and create_time>=:start_time and create_time<=:end_time', array(':prize_type'=>PRIZE_TYPE_NONE, ':activity_id'=>$id, ':start_time'=>$today_start_time, ':end_time'=>$today_end_time));
		//返回信息
		$data = array();
		$data['player'] = $open_id;
		$data['promotions_action_id'] = $id;
		
		$title_res = $promotions -> getCouponsTitle($id);
		if ($title_res['status'] == ERROR_NONE) {
			$title_arr = $title_res['data'];
		}
		//未达到每日发奖次数限制
		if ($prize_num_day < $model->everyday_num){
			if ($prize_num <= $first_probability && $first_prize_num < $model->first_prize_num){        //一等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_FIRST, $source, $open_id);
				if ($result){
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['coupon_id'] = $model['first_prize'];
					$data['message'] = $title_arr[PRIZE_TYPE_FIRST]['title'];
					$data['prize_type'] = PRIZE_TYPE_FIRST;
					$data['prizelevel'] = 1;
					$data['success'] = "y";
				}
			}elseif ($prize_num <= $second_probability && $prize_num > $first_probability && $second_prize_num < $model->second_prize_num){             //二等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_SECOND, $source, $open_id);
				if ($result) {
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['coupon_id'] = $model['second_prize'];
					$data['message'] = $title_arr[PRIZE_TYPE_SECOND]['title'];
					$data['prize_type'] = PRIZE_TYPE_SECOND;
					$data['prizelevel'] =2;
					$data['success'] = "y";
				}
			}elseif ($prize_num <= $third_probability && $prize_num > $second_probability && $third_prize_num < $model->third_prize_num){             //三等奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_THIRD, $source, $open_id);
				if ($result) {
					$data['error'] = "ok";
					$data['id'] = $result['data'];
					$data['coupon_id'] = $model['third_prize']['title'];
					$data['message'] = $title_arr[PRIZE_TYPE_THIRD];
					$data['prize_type'] = PRIZE_TYPE_THIRED;
					$data['prizelevel'] = 3;
					$data['success'] = "y";
				}
			}else {      //未中奖
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_NONE, $source, "1111");
			}
		} else {
				$result = $promotions->SavePrizeId($id, PRIZE_TYPE_NONE, $source, "1111");
				
		}
		echo json_encode($data);
	}
	
	
	/**
	 * 中奖后-保存手机号码
	 */
	public function actionSavePhoneNum()
	{
		$promotions = new PromotionsC();
		if (isset($_POST['ActivityRecord'])){
			$data = array();
			$data['error'] = "no";
				
			$mobilePhone = $_POST['ActivityRecord']['phone_num'];
// 			if(empty($_POST['ActivityRecord']['user_name'])){
// 				$data['message'] = "请输入姓名";
// 			} else {
				$is_phone = $this->is_mobile($mobilePhone);
				if (!$is_phone) {
					$data['message'] = "请输入正确的手机号码";
				}else{
					$post = $_POST['ActivityRecord'];
					$id = $post['id'];
					$result = $promotions->savePhoneNum($id, $mobilePhone);
					
					if ($result['status'] == ERROR_NONE) {
						$data['error'] = "success";
						$url = 
						$data['url'] = USER_DOMAIN_COUPONS.'/newGetCouponOne?coupon_id='.$id;
					}else {
						$data['message'] = "提交失败！";
					}
				}
// 			}
			echo json_encode($data);
		}
	}
	
	/**
	 * 验证手机号码
	*/
	function is_mobile($mobilePhone) {
		$check = preg_match("/^(13|15|18|17)\d{9}$/", $mobilePhone);
		if ($check) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 获取商户信息
	 */
	public function getMerchant($encrypt_id)
	{
		$user = new UserUC();
		//获取商户信息
		$merchant_res = $user->getMerchant($encrypt_id);
		$merchant_result = json_decode($merchant_res, true);
		if ($merchant_result['status'] == ERROR_NONE) {
			if (isset($merchant_result['data'])) {
				$merchant = $merchant_result['data'];
			}else {
				$this->redirect('error');
			}
		}else{
			$this->redirect('error');
		}
		return $merchant;
	}
	
	/**
	 * 获取 服务窗 OpenId
	 */
	public function getAliOpenId($appid)
	{
		//获取链接中的参数
		// 		$url = $_SERVER["REQUEST_URI"];        //输入地址
		// 		$url .= '&';
		// 		$auth_code = '';
		// 		preg_match('/auth_code=(.+?)&/is', $url, $match_auth_code);
		if (!empty($_GET['auth_code'])) {                   //获取auth_code
			$auth_code = $_GET['auth_code'];
		}
	
		//获取并保存OpenId
		$api = new AliApi('AliApi');
		$response = $api->getOpenId($appid, $auth_code);
		if ($response != null) {
			if (isset($response->alipay_system_oauth_token_response)) {
				$open_id = $response->alipay_system_oauth_token_response->alipay_user_id;
				Yii::app()->session['ali_open_id'] = $open_id;
				$access_token = $response->alipay_system_oauth_token_response->access_token;
				Yii::app()->session['access_token'] = $access_token;
			}
		}
	}
	
	/**
	 * 获取微信openid
	 */
	public function getWechatOpenId($code, $wechat_appid, $wechat_appsecret)
	{
		$wechat = new WechatC();
		//获取access_token以及用户openid
		$get_access_res = $wechat->getUserAccessCode($code, $wechat_appid, $wechat_appsecret);
	
		if (isset($get_access_res['openid'])) {
			$open_id = $get_access_res['openid'];
			$access_token = $get_access_res['access_token'];
	
			Yii::app()->session['wechat_open_id'] = $open_id;
		}
	}
}