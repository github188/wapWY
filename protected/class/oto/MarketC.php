<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 营销管理类
 * xuyangfeng
 * 2016/1/27
 */
class MarketC extends mainClass
{
	public $page = null;
	/**
	 * 获取营销活动列表
	 * $merchant_id  商户id
	 * $name         活动名称
	 * $type         活动类型
	 * $status       活动状态
	 */
	public function getMarketingList($merchant_id, $name, $type, $status)
	{
		$result = array();
		$data = array();
		try {
			$criteria = new CDbCriteria();
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			
			if(!empty($name)){
				$criteria -> addSearchCondition('name', $name);
			}
			if(!empty($type)){
				$criteria -> addCondition('type=:type');
				$criteria -> params[':type'] = $type;
			}
			if(!empty($status)){
				$criteria -> addCondition('status=:status');
				$criteria -> params[':status'] = $status;
			}
			$criteria -> order = 'create_time desc';
			//分页
			$pages = new CPagination(MarketingActivity::model()->count($criteria));
			$pages -> pageSize = Yii::app() -> params['perPage'];
			$pages -> applyLimit($criteria);
			$this -> page = $pages;
			
			$model = MarketingActivity::model()->findAll($criteria);
			if (! empty ( $model )) {
				foreach ( $model as $k => $v ) {
					$data ['list'] [$k] ['id'] = $v ['id'];
					$data ['list'] [$k] ['name'] = $v ['name']; // 营销活动名称
					$data ['list'] [$k] ['type'] = $v ['type']; // 营销活动类型
					$data ['list'] [$k] ['time_type'] = $v ['time_type']; // 活动时间类型 1短期 2长期
					$data ['list'] [$k] ['start_time'] = $v ['start_time']; // 活动开始时间
					$data ['list'] [$k] ['end_time'] = $v ['end_time']; // 活动结束时间
					$data ['list'] [$k] ['target_type'] = $v ['target_type']; // 活动群体类型 1默认群体 2指定群体
					$data ['list'] [$k] ['condition_money'] = $v ['condition_money']; // 赠券满金额
					$data ['list'] [$k] ['status'] = $v ['status']; // 活动状态 1未开始 2进行中 3已结束 4已停用
					$data ['list'] [$k] ['create_time'] = $v ['create_time']; // 创建时间
				}
				$result['status'] = ERROR_NONE;
				$result['data'] = $data;
			}else{
				$result['status'] = ERROR_NO_DATA;
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 创建-- 新加入会员赠券,填资料赠券,生日赠券,累积消费赠券
	 * $merchant_id   商户id
	 * $type          营销活动类型
	 * $name          营销活动名称
	 * $time_type     活动时间类型 1短期 2长期
	 * $Time          活动开始时间  活动结束时间
	 * $send_type     发券方式
	 * $couponIdHide  所赠券id
	 * $condition     赠券要求
	 * $condition_money  赠券满金额
	 */
	public function addBeMemberGive($merchant_id,$type,$name,$time_type,$Time,$send_type,$couponIdHide,$condition,$condition_money)
	{
		$result = array();
		$flag = 1;
		$errMsg = '';
		$startTime = '';
		$endTime = '';
		$conditionStr = '';
		try {
			$model = new MarketingActivity();
			if(empty($name)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 活动名称必填';
				Yii::app()->user->setFlash('name_error','活动名称必填');
			}else{
				$check = MarketingActivity::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name', array (
						':flag' => FLAG_NO ,
						':merchant_id' => $merchant_id,
						':name' => trim($name)
				) );
				if(count($check)>0){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 活动名称已存在';
					Yii::app()->user->setFlash('name_error','活动名称已存在');
				}
			}
			if($time_type == MARKETING_ACTIVITY_TIME_TYPE_SHORT){  //如果是短期的
				if(empty($Time)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 创建时间必填';
					Yii::app()->user->setFlash('time_error','创建时间必填');
				}else {
					$timeArr = explode('-',$Time);
					$startTime = $timeArr[0].' 00:00:00';
					$endTime = $timeArr[1].' 23:59:59';
				}
			}
			if (empty($couponIdHide)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 赠券必填';
				Yii::app()->user->setFlash('sendCoupons_error','所赠券必填');
			}
			
			if($type == MARKETING_ACTIVITY_TYPE_COMPLETE_MMEMBER_DATA){ //如果是填资料赠券
				if(empty($condition)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 赠券要求必填';
					Yii::app()->user->setFlash('condition_error','赠券要求必填');
				}else {
					$conditionStr = implode(',',$condition);
					$conditionStr = ','.$conditionStr.',';
				}
			}
			
			if($type == MARKETING_ACTIVITY_TYPE_CUMULATIVE_GIVE || $type == MARKETING_ACTIVITY_TYPE_FULL_GIVE){ //如果是累计消费赠券或者消费满赠券
				if(empty($condition_money)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 消费额必填';
					Yii::app()->user->setFlash('condition_money_error','消费额必填');
				}else{
					$isCheckNum = preg_match(POSITIVE_REGEX,$condition_money);
					if(!$isCheckNum){
						$flag = 2;
						$result['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg.' 消费额格式不合法';
						Yii::app()->user->setFlash('condition_money_error','消费额格式不合法');
					}
				}
			}
			
			if($flag == 2){
				$result ['errMsg'] = $errMsg;
				return json_encode($result);
			}
			
			$model -> merchant_id = $merchant_id;
			$model -> name = $name;
			$model -> type = $type;
			$model -> time_type = $time_type;
			if($time_type == MARKETING_ACTIVITY_TIME_TYPE_SHORT){
			  $model -> start_time = $startTime;
			  $model -> end_time = $endTime;
			  if(date('Y-m-d H:i:s') > date('Y-m-d H:i:s',strtotime($endTime))){
			  	$model -> status = MARKETING_ACTIVITY_STATUS_OVER;
			  }elseif (date('Y-m-d H:i:s') < date('Y-m-d H:i:s',strtotime($startTime))){
			  	$model -> status = MARKETING_ACTIVITY_STATUS_NOT_START;
			  }else{
			  	$model -> status = MARKETING_ACTIVITY_STATUS_IN_PROGRESS;
			  }
			}else {
				$model -> status = MARKETING_ACTIVITY_STATUS_IN_PROGRESS;
			}
			if($type == MARKETING_ACTIVITY_TYPE_CUMULATIVE_GIVE || $type == MARKETING_ACTIVITY_TYPE_FULL_GIVE){ //如果是累计消费赠券或者消费满赠券
				$model -> condition_money = $condition_money;
			}
			$model -> coupon_id = $couponIdHide;
			$model -> send_type = MARKETING_ACTIVITY_SEND_TYPE_DIRECT_PUT;
			$model -> condition = $conditionStr;
			$model -> create_time = date('Y-m-d H:i:s');
			
			if($model -> save()){
				$result ['status'] = ERROR_NONE; // 状态码
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			}
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 获取详情
	 */
	public function getMarketDetail($id)
	{
		$result = array();
		$data = array();
		try {
			$model = MarketingActivity::model()->findByPk($id);
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('数据不存在');
			}else{
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
				$data['list']['id'] = $model['id'];
				$data['list']['name'] = $model['name']; //营销活动名称
				$data['list']['type'] = $model['type']; //营销活动类型
				$data['list']['time_type'] = $model['time_type']; //活动时间类型 1短期 2长期
				$data['list']['start_time'] = $model['start_time']; //活动开始时间
				$data['list']['end_time'] = $model['end_time']; //活动结束时间
				$data['list']['target_type'] = $model['target_type']; //活动群体类型 1默认群体 2指定群体
				$data['list']['group_id'] = $model['group_id']; //用户分组id
				$data['list']['condition_money'] = $model['condition_money']; //赠券满金额
				$data['list']['condition'] = $model['condition']; //赠券要求（完善资料）
				$data['list']['coupon_id'] = $model['coupon_id']; //所赠券id
				$data['list']['stored_id'] = $model['stored_id']; //储值活动id
				$data['list']['send_type'] = $model['send_type']; //发券方式
				$data['list']['image_text_imageurl'] = $model['image_text_imageurl']; //图文首图
				$data['list']['image_text_title'] = $model['image_text_title']; //图文标题
				$data['list']['send_time'] = $model['send_time']; //发券时间
				$data['list']['create_time'] = $model['create_time'];
				$data['list']['coupon_name'] = isset($model->coupons->title)?$model->coupons->title:''; //获取券名称
				$data['list']['userGroup_name'] = isset($model->userGroup->name)?$model->userGroup->name:''; //获取分组名称
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		$result['data'] = $data;
		return json_encode($result);
	}
	
	/**
	 * 编辑--新加入会员赠券,填资料赠券,生日赠券,累积消费赠券,消费满赠券
	 */
	public function editBeMemberGive($id,$merchant_id,$type,$name,$time_type,$Time,$send_type,$couponIdHide,$condition,$condition_money)
	{
		$result = array();
		$flag = 1;
		$errMsg = '';
		$startTime = '';
		$endTime = '';
		$conditionStr = '';
		try {
			$model = MarketingActivity::model()->findByPk($id);
			if(empty($name)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 活动名称必填';
				Yii::app()->user->setFlash('name_error','活动名称必填');
			}else{
				$check = MarketingActivity::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name and id!=:id', array (
						':flag' => FLAG_NO ,
						':merchant_id' => $merchant_id,
						':name' => trim($name),
						':id'=>$id
				) );
				if(count($check)>0){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 活动名称已存在';
					Yii::app()->user->setFlash('name_error','活动名称已存在');
				}
			}
			if($time_type == MARKETING_ACTIVITY_TIME_TYPE_SHORT){  //如果是短期的
				if(empty($Time)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 创建时间必填';
					Yii::app()->user->setFlash('time_error','创建时间必填');
				}else {
					$timeArr = explode('-',$Time);
					$startTime = $timeArr[0].' 00:00:00';
					$endTime = $timeArr[1].' 23:59:59';
				}
			}
			if (empty($couponIdHide)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 赠券必填';
				Yii::app()->user->setFlash('sendCoupons_error','所赠券必填');
			}
				
			if($type == MARKETING_ACTIVITY_TYPE_COMPLETE_MMEMBER_DATA){ //如果是填资料赠券
				if(empty($condition)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 赠券要求必填';
					Yii::app()->user->setFlash('condition_error','赠券要求必填');
				}else {
					$conditionStr = implode(',',$condition);
					$conditionStr = ','.$conditionStr.',';
				}
			}
				
			if($type == MARKETING_ACTIVITY_TYPE_CUMULATIVE_GIVE || $type == MARKETING_ACTIVITY_TYPE_FULL_GIVE){ //如果是累计消费赠券或者消费满赠券
				if(empty($condition_money)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 消费额必填';
					Yii::app()->user->setFlash('condition_money_error','消费额必填');
				}else{
					$isCheckNum = preg_match(POSITIVE_REGEX,$condition_money);
					if(!$isCheckNum){
						$flag = 2;
						$result['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg.' 消费额格式不合法';
						Yii::app()->user->setFlash('condition_money_error','消费额格式不合法');
					}
				}
			}
				
			if($flag == 2){
				$result ['errMsg'] = $errMsg;
				return json_encode($result);
			}
				
			$model -> merchant_id = $merchant_id;
			$model -> name = $name;
			$model -> type = $type;
			$model -> time_type = $time_type;
			if($time_type == MARKETING_ACTIVITY_TIME_TYPE_SHORT){
				$model -> start_time = $startTime;
				$model -> end_time = $endTime;
				if(date('Y-m-d H:i:s') > date('Y-m-d H:i:s',strtotime($endTime))){
					$model -> status = MARKETING_ACTIVITY_STATUS_OVER;
				}elseif (date('Y-m-d H:i:s') < date('Y-m-d H:i:s',strtotime($startTime))){
					$model -> status = MARKETING_ACTIVITY_STATUS_NOT_START;
				}else{
					$model -> status = MARKETING_ACTIVITY_STATUS_IN_PROGRESS;
				}
			}else{
				$model -> status = MARKETING_ACTIVITY_STATUS_IN_PROGRESS;
			}
			if($type == MARKETING_ACTIVITY_TYPE_CUMULATIVE_GIVE || $type == MARKETING_ACTIVITY_TYPE_FULL_GIVE){ //如果是累计消费赠券或者消费满赠券
				$model -> condition_money = $condition_money;
			}
			$model -> coupon_id = $couponIdHide;
			$model -> send_type = MARKETING_ACTIVITY_SEND_TYPE_DIRECT_PUT;
			$model -> condition = $conditionStr;
			$model -> last_time = date('Y-m-d H:i:s');
				
			if($model -> save()){
				$result ['status'] = ERROR_NONE; // 状态码
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			}
				
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 创建--新会员赠券,加入未消费会员赠券,挽回流失客户,促进未流失客户,给老会员赠券,会员赠券,储值活动
	 * $merchant_id   商户id
	 * $type          营销活动类型
	 * $name          营销活动名称
	 * $Time          活动开始时间  活动结束时间
	 * $send_type     发券方式
	 * $couponIdHide  所赠券id
	 * $image_text_title   图文标题
	 * $image_text_imageurl  图文url
	 * $userGroupIdHide      分组id
	 * $target_type          活动群体类型 1默认群体 2指定群体
	 * $stored               储值活动
	 */
	public function addNewMember($merchant_id,$type,$name,$Time,$send_type,$couponIdHide,$image_text_title,$image_text_imageurl,$userGroupIdHide,$target_type,$stored)
	{
		$result = array();
		$flag = 1;
		$errMsg = '';
	
		$conditionStr = '';
		try {
			$model = new MarketingActivity();
			if(empty($name)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 活动名称必填';
				Yii::app()->user->setFlash('name_error','活动名称必填');
			}else{
				$check = MarketingActivity::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name', array (
						':flag' => FLAG_NO ,
						':merchant_id' => $merchant_id,
						':name' => trim($name)
				) );
				if(count($check)>0){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 活动名称已存在';
					Yii::app()->user->setFlash('name_error','活动名称已存在');
				}
			}
		
			if($type != MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY){ //如果不是储值活动
			if (empty($couponIdHide)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 赠券必填';
				Yii::app()->user->setFlash('sendCoupons_error','所赠券必填');
			}
			}
			
			if($type == MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY){ //储值活动
				if($target_type == MARKETING_ACTIVITY_TARGET_TYPE_APPOINT){
					if(empty($userGroupIdHide)){
						$flag = 2;
						$result['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg.' 分组必填';
						Yii::app()->user->setFlash('userGroup_error','分组必填');
					}
				}
			}
			
			if($type == MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){ //精准营销
				if($target_type == MARKETING_ACTIVITY_TARGET_TYPE_APPOINT){
					if(empty($userGroupIdHide)){
						$flag = 2;
						$result['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg.' 分组必填';
						Yii::app()->user->setFlash('userGroup_error','分组必填');
					}
				}
			}

			if($send_type == MARKETING_ACTIVITY_SEND_TYPE_ALIWX){ //服务窗消息公众号消息发券
			 if(empty($image_text_title)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 图文标题必填';
				Yii::app()->user->setFlash('image_text_title_error','图文标题必填');
			 }
			 if(empty($image_text_imageurl)){
			 	$flag = 2;
			 	$result['status'] = ERROR_PARAMETER_MISS;
			 	$errMsg = $errMsg.' 图片必需上传';
			 	Yii::app()->user->setFlash('image_text_imageurl_error','图片必需上传');
			 }
			}
			
			if($type != MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY && $type != MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
			if(empty($Time)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 发券时间必填';
				Yii::app()->user->setFlash('time_error','发券时间必填');
			}
			}
				
			if($flag == 2){
				$result ['errMsg'] = $errMsg;
				return json_encode($result);
			}
				
			$model -> merchant_id = $merchant_id;
			$model -> name = $name;
			$model -> type = $type;
			$model -> time_type = MARKETING_ACTIVITY_TIME_TYPE_LONG;
			$model -> status = MARKETING_ACTIVITY_STATUS_IN_PROGRESS;
			if($send_type == MARKETING_ACTIVITY_SEND_TYPE_ALIWX){ //服务窗消息公众号消息发券
			  $model -> image_text_title = $image_text_title;
			  $model -> image_text_imageurl = $image_text_imageurl;
			}
			$model -> coupon_id = $couponIdHide;
			$model -> send_type = $send_type;
			if($type != MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY  && $type != MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
			  $model -> target_type = MARKETING_ACTIVITY_TARGET_TYPE_DEFAULT;
			  $model -> send_time = $Time;
			}elseif ($type == MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY){ //如果是储值活动
				$model -> target_type = $target_type;
				$model -> group_id = $userGroupIdHide;
				$model -> stored_id = $stored;
			}elseif ($type == MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
				$model -> target_type = $target_type;
				$model -> group_id = $userGroupIdHide;
			}
			$model -> condition = $conditionStr;
			$model -> create_time = date('Y-m-d H:i:s');
				
			if($model -> save()){
				$result ['status'] = ERROR_NONE; // 状态码
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			}
				
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 编辑--新会员赠券,加入未消费会员赠券,挽回流失客户,促进未流失客户,给老会员赠券,会员赠券,储值活动
	 */
	public function editNewMember($id,$merchant_id,$type,$name,$Time,$send_type,$couponIdHide,$image_text_title,$image_text_imageurl,$userGroupIdHide,$target_type,$stored)
	{
		$result = array();
		$flag = 1;
		$errMsg = '';
		
		$conditionStr = '';
		try {
			$model = MarketingActivity::model()->findByPk($id);
			if(empty($name)){
				$flag = 2;
				$result['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg.' 活动名称必填';
				Yii::app()->user->setFlash('name_error','活动名称必填');
			}else{
				$check = MarketingActivity::model ()->find ( 'flag=:flag and merchant_id=:merchant_id and name=:name and id!=:id', array (
						':flag' => FLAG_NO ,
						':merchant_id' => $merchant_id,
						':name' => trim($name),
						':id' => $id
				) );
				if(count($check)>0){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 活动名称已存在';
					Yii::app()->user->setFlash('name_error','活动名称已存在');
				}
			}
		
			if($type != MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY){ //如果不是储值活动
				if (empty($couponIdHide)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 赠券必填';
					Yii::app()->user->setFlash('sendCoupons_error','所赠券必填');
				}
				
			}
				
			if($type == MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY){
				if($target_type == MARKETING_ACTIVITY_TARGET_TYPE_APPOINT){
					if(empty($userGroupIdHide)){
						$flag = 2;
						$result['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg.' 分组必填';
						Yii::app()->user->setFlash('userGroup_error','分组必填');
					}
				}
			}
			
			if($type == MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
				if($target_type == MARKETING_ACTIVITY_TARGET_TYPE_APPOINT){
					if(empty($userGroupIdHide)){
						$flag = 2;
						$result['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg.' 分组必填';
						Yii::app()->user->setFlash('userGroup_error','分组必填');
					}
				}
			}

			if($send_type == MARKETING_ACTIVITY_SEND_TYPE_ALIWX){ //服务窗消息公众号消息发券
				if(empty($image_text_title)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 图文标题必填';
					Yii::app()->user->setFlash('image_text_title_error','图文标题必填');
				}
				if(empty($image_text_imageurl)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 图片必需上传';
					Yii::app()->user->setFlash('image_text_imageurl_error','图片必需上传');
				}
			}
				
			if($type != MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY && $type != MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
				if(empty($Time)){
					$flag = 2;
					$result['status'] = ERROR_PARAMETER_MISS;
					$errMsg = $errMsg.' 发券时间必填';
					Yii::app()->user->setFlash('time_error','发券时间必填');
				}
			}
		
			if($flag == 2){
				$result ['errMsg'] = $errMsg;
				return json_encode($result);
			}
		
			$model -> merchant_id = $merchant_id;
			$model -> name = $name;
			$model -> type = $type;
			$model -> time_type = MARKETING_ACTIVITY_TIME_TYPE_LONG;
			$model -> status = MARKETING_ACTIVITY_STATUS_IN_PROGRESS;
			if($send_type == MARKETING_ACTIVITY_SEND_TYPE_ALIWX){ //服务窗消息公众号消息发券
				$model -> image_text_title = $image_text_title;
				$model -> image_text_imageurl = $image_text_imageurl;
			}
			$model -> coupon_id = $couponIdHide;
			$model -> send_type = $send_type;
			if($type != MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY && $type != MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
				$model -> target_type = MARKETING_ACTIVITY_TARGET_TYPE_DEFAULT;
				$model -> send_time = $Time;
			}elseif ($type == MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY){ //如果是储值活动
				$model -> target_type = $target_type;
				$model -> group_id = $userGroupIdHide;
				$model -> stored_id = $stored;
			}elseif ($type == MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING){
				$model -> target_type = $target_type;
				$model -> group_id = $userGroupIdHide;
			}
			$model -> condition = $conditionStr;
			$model -> last_time = date('Y-m-d H:i:s');
		
			if($model -> save()){
				$result ['status'] = ERROR_NONE; // 状态码
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			}
		
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 删除活动操作
	 */
	public function delMarketing($id)
	{
		$result = array();
		try {
			$model = MarketingActivity::model()->findByPk($id);
			$model -> flag = FLAG_YES;
			$model -> last_time = date('Y-m-d H:i:s');
			if($model -> update()){
				$result['status'] = ERROR_NONE;
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 停止活动操作
	 */
	public function changeStatus($id)
	{
		$result = array();
		try {
			$model = MarketingActivity::model()->findByPk($id);
			$model -> status = MARKETING_ACTIVITY_STATUS_STOP;
			$model -> last_time = date('Y-m-d H:i:s');
			if($model -> update()){
				$result['status'] = ERROR_NONE;
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 获取优惠券名称
	 */
	public function getYhqName($couponId)
	{
		if(!empty($couponId)){
			$coupon = Coupons::model()->findByPk($couponId);
			return $coupon['title'];
		}else{
			return '';
		}
	}
	
	/**
	 * 获取默认群体
	 */
	public function  getDefaultGroup($merchant_id)
	{
		if(!empty($merchant_id)){
		    
		    //注册30天
		    $now_time = date("Y-m-d h:i:s");
		    $time1 = strtotime($now_time)-(30*24*3600);
		    $time1 = date('Y-m-d h:i:s',$time1);
		    //注册60天
		    $time2 = strtotime($now_time)-(60*24*3600);
		    $time2 = date('Y-m-d h:i:s',$time2);
		    
            //查出消费用户id数组
 			$criteria = new CDbCriteria;
            $criteria->select= 'user_id';
            $criteria->distinct = TRUE;
            $model = UserPointsdetail::model()->findAll($criteria);
            $consume = array();
            foreach ($model as $k => $v){
                $consume[] = $v['user_id'];
            }
            //查出只消费一次用户id数组
            $criteria = new CDbCriteria;
            $criteria->select= 'user_id';
            $criteria->group = 'user_id';
            $criteria->having = 'count(*)=1';
            $model = UserPointsdetail::model()->findAll($criteria);
            $consumeonce = array();
            foreach ($model as $k => $v){
                $consumeonce[] = $v['user_id'];
            }
            //查出30天消费一次的群体
            $criteria = new CDbCriteria;
            $criteria -> addCondition('merchant_id=:merchant_id');
            $criteria -> params[':merchant_id'] = $merchant_id;
            $criteria -> addCondition('type=:type');
            $criteria -> params[':type'] =1;
            $criteria -> addCondition('regist_time>=:time');
            $criteria -> params[':time'] = $time1;
            $criteria->addInCondition('id', $consumeonce);
            $model = User::model()->findAll($criteria);
            $num = User::model()->count($criteria);
            $res = array();
            $res['model1'] = $model;
            $res['num1'] = $num;
            //查出30天未消费次的群体
            $criteria = new CDbCriteria;
 		    $criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			$criteria -> addCondition('type=:type');
			$criteria -> params[':type'] =1;
			$criteria -> addCondition('regist_time>=:time');
			$criteria -> params[':time'] = $time1;
			$criteria->addNotInCondition('id', $consume);
 			$model = User::model()->findAll($criteria);
 			$num = User::model()->count($criteria);
 			$res['model2'] = $model;
 			$res['num2'] = $num;
 			//查出加入30天以上，60内只消费过一次的群体
 			$criteria = new CDbCriteria;
 			$criteria -> addCondition('merchant_id=:merchant_id');
 			$criteria -> params[':merchant_id'] = $merchant_id;
 			$criteria -> addCondition('type=:type');
 			$criteria -> params[':type'] =1;
 			$criteria -> addCondition('regist_time <=:time1');
 			$criteria -> params[':time1'] = $time1;
 			$criteria -> addCondition('regist_time >=:time2');
 			$criteria -> params[':time2'] = $time2;
 			$criteria->addInCondition('id', $consumeonce);
 			$model = User::model()->findAll($criteria);
 			$num = User::model()->count($criteria);
 			$res['model3'] = $model;
 			$res['num3'] = $num;
 			
 			//会员群体
 			$criteria = new CDbCriteria;
 		    $criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			$criteria -> addCondition('type=:type');
			$criteria -> params[':type'] =1;
 			$model = User::model()->findAll($criteria);
 			$num = User::model()->count($criteria);
 			$res['model4'] = $model;
 			$res['num4'] = $num;
 			
			return $res;
		}else{
			return '';
		}
	}	

	/**
	 * 获取分组名称
	 */
	public function  getUserGroupName($userGroupId)
	{
		if(!empty($userGroupId)){
			$userGroup = UserGroup::model()->findByPk($userGroupId);
			return $userGroup['name'];
		}else{
			return '';
		}
	}
	
	/**
	 * 获取储值活动
	 */
	public function getStored($merchant_id)
	{
		$data = array();
		$model = Stored::model()->findAll('flag=:flag and merchant_id=:merchant_id',array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id));
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data[$v['id']] = $v['name'];
			}
		}
		return $data;
	}
	
	/**
	 * 获取分组列表
	 */
	public function getUserGroupList($merchant_id)
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
			//分页
			$pages = new CPagination(UserGroup::model()->count($criteria));
			$pages -> pageSize = Yii::app() -> params['perPage'];
			$pages -> applyLimit($criteria);
			$this -> page = $pages;
			$model = UserGroup::model ()->findAll ($criteria);
			if(!empty($model)){
				foreach ($model as $k=>$v){
					$data['list'][$k]['id'] = $v['id'];
					$data['list'][$k]['name'] = $v['name'];
					$data['list'][$k]['create_time'] = $v['create_time'];
				}
				$result['status'] = ERROR_NONE;
				$result['data'] = $data;
			}else{
				$result['status'] = ERROR_NO_DATA;
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
			$result['errMsg'] = $e -> getMessage();
		}
		return json_encode($result);
	}
	
	/**
	 * 获取卡券列表
	 * $merchant_id  商户id
	 */
	public function getCardCouponsList($merchant_id)
	{
		$result = array();
		try {
				
			$criteria = new CDbCriteria();
			$criteria -> addCondition('merchant_id=:merchant_id');
			$criteria -> params[':merchant_id'] = $merchant_id;
			$criteria -> addCondition('flag=:flag');
			$criteria -> params[':flag'] = FLAG_NO;
			$criteria -> addCondition('if_invalid=:if_invalid');
			$criteria -> params[':if_invalid'] = IF_INVALID_NO;
				
			$criteria -> order = 'create_time desc';
				
			//分页
			$pages = new CPagination(Coupons::model()->count($criteria));
			$pages -> pageSize = 5;
			$pages -> applyLimit($criteria);
			$this->page = $pages;
				
			$model = Coupons::model()->findAll($criteria);
			$data = array ();
			if(!empty($model)){
				foreach ($model as $k => $v){
					if($v['time_type'] == VALID_TIME_TYPE_FIXED && $v['end_time'] < date('Y-m-d H:i:s')){ //排除过期的优惠券
						continue;
					}
					$data['list'][$k]['id'] = $v['id'];
					$data['list'][$k]['type'] = $v['type']; //券类型
					$data['list'][$k]['if_wechat'] = $v['if_wechat']; //是否同步到微信卡包 1不开启 2开启
					$data['list'][$k]['title'] = $v['title']; //券标题
					$data['list'][$k]['vice_title'] = $v['vice_title']; //副标题
					$data['list'][$k]['money_type'] = $v['money_type']; //券金额类型 1固定 2随机
					$data['list'][$k]['money_random'] = $v['money_random']; //代金券的随机金额
					$data['list'][$k]['money'] = $v['money']; //代金券的固定金额
					$data['list'][$k]['discount'] = $v['discount']; //券折扣
					$data['list'][$k]['prompt'] = $v['prompt']; //提示操作
					$data['list'][$k]['if_share'] = $v['if_share']; //用户是否可以分享领取链接 1可以 2不可以
					$data['list'][$k]['if_give'] = $v['if_give']; //可否转增其他好友 1 能 2不能
					$data['list'][$k]['num'] = $v['num']; //发放数量
					$data['list'][$k]['get_num'] = $v['get_num']; //已领取数量
					$data['list'][$k]['time_type'] = $v['time_type']; //有效时间类型1固定时间 2相对时间
					$data['list'][$k]['start_time'] = $v['start_time']; //固定时间时的有效开始时间
					$data['list'][$k]['end_time'] = $v['end_time']; //固定时间有效结束时间
					$data['list'][$k]['start_days'] = $v['start_days']; //领取后几天生效 当天代表0最高90天
					$data['list'][$k]['effective_days'] = $v['effective_days']; //有效天数最少1天最多90天
					$data['list'][$k]['receive_num'] = $v['receive_num']; //每个用户领取数量
					$data['list'][$k]['mini_consumption'] = $v['mini_consumption']; //最低消费
					$data['list'][$k]['use_restriction'] = $v['use_restriction']; //使用限制
					$data['list'][$k]['if_with_userdiscount'] = $v['if_with_userdiscount']; //是否能与会员折扣同用1不能 2能
					$data['list'][$k]['store_limit'] = $v['store_limit']; //门店限制
					$data['list'][$k]['tel'] = $v['tel']; //客服电话
					$data['list'][$k]['use_illustrate'] = $v['use_illustrate']; //使用须知
					$data['list'][$k]['discount_illustrate'] = $v['discount_illustrate']; //优惠说明
					$data['list'][$k]['if_invalid'] = $v['if_invalid']; //是否失效 1未失效 2已失效
					$data['list'][$k]['create_time'] = $v['create_time']; //创建时间
					$data['list'][$k]['get_receive_num'] = $this->getReceiveNum($v['id']); //券领取次数
					$data['list'][$k]['use_receive_num'] = $this->getUseReceiveNum($v['id']); //券使用次数
					$data['list'][$k]['receive_per'] = $this->getReceivePer($v['id']); //券领取人数
					$data['list'][$k]['status'] = $v['status']; //微信审核状态   1审核中 2已通过 3未通过
				}
				
				$result['status'] = ERROR_NONE;
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
	 * 获取券领取次数
	 *  $coupons_id  券id
	 */
	public function getReceiveNum($coupons_id)
	{
		$count = 0;
		$model = UserCoupons::model()->findAll('coupons_id=:coupons_id and flag=:flag',array(':coupons_id'=>$coupons_id,':flag'=>FLAG_NO));
		if(!empty($model)){
			$count = count($model);
		}
		return $count;
	}
	
	/**
	 * 获取领取的券使用的数量
	 *  $coupons_id  券id
	 */
	public function getUseReceiveNum($coupons_id)
	{
		$count = 0;
		$model = UserCoupons::model()->findAll('coupons_id=:coupons_id and flag=:flag and status=:status'
				,array(':coupons_id'=>$coupons_id,':flag'=>FLAG_NO,':status'=>COUPONS_USE_STATUS_USED));
		if(!empty($model)){
			$count = count($model);
		}
		return $count;
	}
	/**
	 * 获取单张券领取人数
	 * $coupons_id  券id
	 */
	public function getReceivePer($coupons_id)
	{
		$model = UserCoupons::model()->countBySql('select count(distinct user_id) as num from wq_user_coupons where coupons_id =:coupons_id',array(':coupons_id'=>$coupons_id));
		//$sql = 'select count(distinct user_id) as num from wq_user_coupons where coupons_id ='.$coupons_id;
		//$model = Yii::app()->db->createCommand($sql)->queryAll();
		return $model;//[0]['num'];
	}
}