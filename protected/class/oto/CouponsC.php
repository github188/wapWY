<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 红包类
 */

class CouponsC extends mainClass
{
	public $page = null;
	/**
	 * 创建红包
	 * @param $merchant_id 商户id
	 * $post  视图层传过来的数组
	 * $date  固定时间
	 * $start_money  $end_money   随机面额的金额范围
	 * $value_money  券面额类型
	 * $money  固定面额
	 * @param $start_time 发放开始时间
	 * @param $end_time 发放结束时间
	 * @param $valid_time 有效时间类型
	 * 
	 */
	public function createRedEnvelope($post,$date,$start_money,$end_money,$merchant_id,$value_money,$money,$start_time,$end_time,$valid_time)
	{
		$result = array ();
		$errMsg = '';
		$flag = 0;
		
		//验证红包名称
		if (! isset ( $post['name'] ) || empty ( $post['name'] )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 红包名必填';
			$flag = 1;
			Yii::app()->user -> setFlash('name','红包名必填');
		}
        else{
            //验证红包是否已经存在
            if($this->checkRedEnvelopeName($merchant_id,$post['name']))
            {
                $flag = 1;
                Yii::app()->user -> setFlash('name','红包名称已存在');
            }
        }
		// 验证发放量
		if (! isset ( $post['num'] ) || empty ( $post['num'] )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 发放量必填';
			$flag = 1;
			Yii::app()->user -> setFlash('num','发放量必填');
		}else {
			if($post['num'] <= 0){
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = ' 发放量不合法';
				$flag = 1;
				Yii::app()->user -> setFlash('num','发放量不合法');
			}
		}
		// 验证用户领取数量
		if (! isset ( $post['receive_num'] ) || empty ( $post['receive_num'] )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 用户领取数量必填';
			$flag = 1;
			Yii::app()->user -> setFlash('receive_num','用户领取数量必填');
		}else{
			if($post['receive_num'] <= 0){
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 用户领取数量不合法';
				$flag = 1;
				Yii::app()->user -> setFlash('receive_num','用户领取数量不合法');
			}
		}
		
		if(!empty ( $post['num'] ) && !empty ( $post['receive_num'] )){
			if($post['num'] < $post['receive_num']){
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 发放数应大于用户领取数量';
			$flag = 1;
			Yii::app()->user->setFlash('receive_num','发放数应大于用户领取数量');
			}
		}
		
		//验证有效时间类型
		if (! isset ( $valid_time ) || empty ( $valid_time )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 有效时间类型必填';
			$flag = 1;
			Yii::app()->user -> setFlash('valid_time_type','有效时间类型必填');
		}
		//验证发放时间
		if (empty ( $start_time ) && empty ( $end_time )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 发放时间必填';
			$flag = 1;
			Yii::app()->user -> setFlash('time','发放时间必填');
		}
		
		//验证券面额类型
		if (! isset ( $value_money ) || empty ( $value_money )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 券面额类型必填';
			$flag = 1;
			Yii::app()->user -> setFlash('value_money','券面额类型必填');
		}
		
		//验证固定面额
		if(!empty($value_money) && $value_money == FACE_VALUE_TYPE_FIXED){
		if (! isset ( $money ) || empty ( $money )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 固定面额必填';
			$flag = 1;
			Yii::app()->user -> setFlash('money','固定面额必填');
		}
		
		if(!empty($money)){
			$ck = preg_match(POSITIVE_REGEX, trim($money));
			if(!$ck){
			   $result ['status'] = ERROR_PARAMETER_FORMAT;
			   $errMsg = $errMsg . ' 固定面额必须是数字';
			   $flag = 1;
			   Yii::app()->user -> setFlash('money','固定面额必须是数字');
			}
			
		}
		}
		
		//验证随机金额$start_money  $end_money
		if(!empty($value_money) && $value_money == FACE_VALUE_TYPE_RANDOM){
		if ((! isset ( $start_money ) || empty ( $start_money )) || (! isset ( $end_money ) || empty ( $end_money ))) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 随机金额必填';
			$flag = 1;
			Yii::app()->user -> setFlash('sj_money','随机金额必填');
		}
		if(!empty($start_money) || !empty($end_money)){
			$ck_start = preg_match(POSITIVE_REGEX, trim($start_money));
			$ck_end = preg_match(POSITIVE_REGEX, trim($end_money));
			if(!$ck_start || !$ck_end){
				$result ['status'] = ERROR_PARAMETER_FORMAT;
				$errMsg = $errMsg . ' 随机金额必须是数字';
				$flag = 1;
				Yii::app()->user -> setFlash('sj_money','随机金额必须是数字');
			}
		}
		}
		
		//验证单个订单使用张数
		if(!empty($post['order_use_num'])){
			$ck_order_use_num = preg_match(POSITIVE_REGEX, trim($post['order_use_num']));
			if(!$ck_order_use_num){
				$result ['status'] = ERROR_PARAMETER_FORMAT;
				$errMsg = $errMsg . ' 单个订单使用张数必须是数字';
				$flag = 1;
				Yii::app()->user -> setFlash('order_use_num','单个订单使用张数必须是数字');
			}
		}
		
		//验证有效时间固定时间是否合理
		if(!empty($date)){
			$Vdate = explode('-', $date);
			//如果有效期开始时间小于券发放时间开始时间
			if(strtotime($Vdate[0]) < strtotime($start_time)){
				$flag = 1;
				Yii::app()->user -> setFlash('birth','有效期不合理');
			}
		}
		
// 		if ($flag == 1) {
// 			$result ['errMsg'] = $errMsg;
// 			return json_encode ( $result );
// 		}
		
		$model = new Coupons();
		$model -> merchant_id = $merchant_id;
		$model -> discount = 1;
		$model -> type = COUPON_TYPE_REDENVELOPE;
		$model -> name = $post['name'];
		$model -> min_pay_money = 0;//最低消费
		$model -> start_time = $start_time;
		$model -> end_time = $end_time.' 23:59:59';
		if($value_money == FACE_VALUE_TYPE_FIXED){//如果选的是  固定面额
			$model -> fixed_value = $money;
		}elseif($value_money == FACE_VALUE_TYPE_RANDOM){//如果选的是  随机面额
			$model -> userdefined_value = $start_money.'-'.$end_money;
		}
		if($valid_time == VALID_TIME_TYPE_FIXED){//固定时间
			//$model -> validtime_end = date('Y-m-d'.' 23:59:59',strtotime($date));
			if (!empty($date)){
				$date_time = explode('-', $date);
				$validtime_start = $date_time[0];
				$validtime_end = $date_time[1];
			
				//$model -> validtime_start = date('Y-m-d'.' 00:00:00',strtotime($validtime_start));
				//$model -> validtime_end = date('Y-m-d'.' 23:59:59',strtotime($validtime_end));
				if(!empty($end_time)){
					//如果发放时间的结束时间小于固定时间的开始时间  则入库
					if((date('Y-m-d'.' 00:00:00',strtotime($start_time)) <= date('Y-m-d'.' 00:00:00',strtotime($validtime_start))) && 
					  (date('Y-m-d'.' 23:59:59',strtotime($end_time))<= date('Y-m-d'.' 23:59:59',strtotime($validtime_end))) ){
						$model -> validtime_start = date('Y-m-d'.' 00:00:00',strtotime($validtime_start));
						$model -> validtime_end = date('Y-m-d'.' 23:59:59',strtotime($validtime_end));
					}else{ //如果发放时间的结束时间大于固定时间的开始时间  则两个日期有冲突
						$result ['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg . ' 有效日期与发放日期不合逻辑';
						$flag = 1;
						Yii::app()->user -> setFlash('fixed_time','有效日期与发放日期不合逻辑');
					}
				}
			}else{
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 有效日期必填';
				$flag = 1;
				Yii::app()->user -> setFlash('fixed_time','有效日期必填');
			}
		}else{//相对时间
			//$model -> validtime_fixed_value = $post['validtime_fixed_value'];//天数
			if(!empty($post['validtime_fixed_value'])){
				$model -> validtime_fixed_value = $post['validtime_fixed_value'];//天数
			}else{
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 有效天数必填';
				$flag = 1;
				Yii::app()->user -> setFlash('relative_time','有效天数必填');
			}
		}
		
		
		
		 $model -> num = $post['num'];
		 $model -> receive_num = $post['receive_num'];
		 
		 //门店限制获取门店id的集合
		 if(!empty($post['use_store'])){
		 	//$model -> use_store = ','.$post['use_store'].',';
		 	$str_store_area_id = '';
		 	for($j=0;$j<count($post['use_store']);$j++){
		 		$str_store_area_id  = $str_store_area_id.$post['use_store'][$j].',';
		 	}
		 	$model -> use_store = ','.$str_store_area_id;
		 }else{
// 		 	$storeId = '';
// 		 	$store = Store::model()->findAll('flag=:flag',array(':flag'=>FLAG_NO));
// 		 	foreach ($store as $key => $value){
// 		 		$storeId = $storeId .','.($value -> id);
// 		 	}
// 		 	$model -> use_store = $storeId.',';

		 	$result ['status'] = ERROR_PARAMETER_MISS;
		 	$errMsg = $errMsg . ' 请勾选门店';
		 	$flag = 1;
		 	Yii::app()->user -> setFlash('use_store','请勾选门店');
		 }
		 
		 if ($flag == 1) {
		 	$result ['errMsg'] = $errMsg;
		 	return json_encode ( $result );
		 }
		 
		 $model -> order_use_num = !empty($post['order_use_num'])?$post['order_use_num']:'1';//单个订单可使用数量
		 $model -> refund_deal = isset($post['refund_deal'])?$post['refund_deal']:'2';//是否退款时退还优惠券
		 $model -> if_with_userdiscount = !empty($post['if_with_userdiscount'])?'2':'1';//是否能与会员折扣同用 
		 $model -> if_with_coupons = !empty($post['if_with_coupons'])?'2':'1';//是否能与优惠券同用 
		 $model -> allow_many = !empty($post['allow_many'])?'2':'1';//是否允许多个优惠券同时使用
		 $model -> use_illustrate = $post['use_illustrate'];
		 $model -> create_time = date('Y-m-d h:i:s');
		 
		 if ($model->save ()) {
			$result ['status'] = ERROR_NONE; // 状态码
			$result ['errMsg'] = ''; // 错误信息
			$result ['data'] = array (
					'id' => $model->id 
			);
		} else {
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
			$result ['data'] = '';
		}
		
		return json_encode ($result);
	}
	
	/**
	 * 红包列表
	 * @param $merchant_id 商户id  
	 * $keyword_name 按红包名称搜索
	 */
	public function getRedEnvelopeList($merchant_id,$keyword_name)
	{
		$result = array ();
		$criteria = new CDbCriteria ();
		if (! empty ( $merchant_id )) {
			$criteria->addCondition ( 'merchant_id=:merchant_id and flag=:flag and type=:type' );
			$criteria->params = array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO,
					':type' => COUPON_TYPE_REDENVELOPE
			);
		}else{
			$criteria->addCondition ( 'flag=:flag and type=:type' );
			$criteria->params = array (
					':flag' => FLAG_NO,
					':type' => COUPON_TYPE_REDENVELOPE
			);
		}
		
		if(!empty($keyword_name)){
			$criteria -> addCondition('name=:name');
			$criteria -> params[':name'] = $keyword_name;
		}
		
		$criteria -> order = 'create_time desc';
		
		$pages = new CPagination(Coupons::model()->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		$model = Coupons::model ()->findAll ( $criteria );
		$data = array ();
		if(!empty($model)){
		foreach ( $model as $k => $v ) {
			$data ['list'] [$k] ['id'] = $v->id;
			$data ['list'] [$k] ['name'] = $v->name;
			$data ['list'] [$k] ['start_time'] = $v->start_time;
			$data ['list'] [$k] ['end_time'] = $v->end_time;
			$data ['list'] [$k] ['num'] = $v->num;
			$data ['list'] [$k] ['receive_num'] = $v->receive_num;
			$data ['list'] [$k] ['create_time'] = $v->create_time;
		}
		
		$result ['status'] = ERROR_NONE;
		$result ['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		//$result['pages'] = $pages;
		return json_encode ($result);
	}
	
	/**
	 * 红包删除
	 */
	public function delRedEnvelope($id)
	{
		$result = array();
		
		$model = Coupons::model()->findByPk($id);
		$model -> flag = FLAG_YES;
		
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据删除失败'; //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 红包(券)详情
	 * $id  券id
	 */
	public function getRedEnvelopeDetails($id)
	{
		$result = array();
		$data = array();
		
		$model = Coupons::model()->findByPk($id);
		if(!empty($model)){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';
		
			$data['list']['id'] = $model -> id;
			$data['list']['merchant_id'] = $model -> merchant_id;
			$data['list']['name'] = $model -> name;
			$data['list']['type'] = $model -> type;
			$data['list']['start_time'] = $model -> start_time;
			$data['list']['end_time'] = $model -> end_time;
			$data['list']['fixed_value'] = $model -> fixed_value;
			$data['list']['userdefined_value'] = $model -> userdefined_value;
			$data['list']['validtime_fixed_value'] = $model -> validtime_fixed_value;
			$data['list']['validtime_end'] = $model -> validtime_end;
			$data['list']['num'] = $model -> num;
			$data['list']['discount'] = $model -> discount;
			$data['list']['max_discount_money'] = $model -> max_discount_money;
			$data['list']['order_use_num'] = $model -> order_use_num;//单个订单可使用数量
			$data['list']['if_with_userdiscount'] = $model -> if_with_userdiscount;//是否能与会员折扣同用
			$data['list']['if_with_coupons'] = $model -> if_with_coupons;//是否能与优惠券（折扣券、代金券）同用
			$data['list']['exchange'] = $model -> exchange;
			$data['list']['receive_num'] = $model -> receive_num;
			$data['list']['min_pay_money'] = $model -> min_pay_money;
			$data['list']['use_store'] = $model -> use_store;
			$data['list']['use_store_name'] = $this->getUserStoreName($model -> use_store);
			$data['list']['refund_deal'] = $model -> refund_deal;
			$data['list']['create_time'] = $model -> create_time;
				
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		$result['data'] = $data;
		return json_encode($result);
	}
	
	/**
	 * 获取门店限制的门店名称
	 * $use_store  门店限制的门店id集
	 */
	public function getUserStoreName($use_store)
	{
		$user_store_name = '';
		$arr = array();
		$arr = explode(',', $use_store);
		for($i = 1;$i<=count($arr)-2;$i++){
			$store = Store::model()->findByPk($arr[$i]);
			$user_store_name = $user_store_name.($store->name).',';
		}
		return substr($user_store_name,0,strlen($user_store_name)-1);
	}
	
	/**
	 * 红包发放明细
	 */
	public function getRedEnvelopeUsed($merchant_id)
	{
		$coupons_id = array();
		$data = array();
		$result = array();

		$criteria = new CDbCriteria();
		$coupons = Coupons::model()->findAll('merchant_id=:merchant_id and flag=:flag and type=:type',
				array(':merchant_id'=>$merchant_id,':flag'=>FLAG_NO,':type'=>COUPON_TYPE_REDENVELOPE));
		foreach ($coupons as $k => $v){
			$coupons_id[] = $v -> id;
		}
		$criteria -> addInCondition('coupons_id', $coupons_id);
	    $criteria -> order = 'use_time desc';
		$pages = new CPagination(UserCoupons::model()->with('coupons')->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;

		$model = UserCoupons::model()->findAll($criteria);
		if(!empty($model)){
			$k = 0;
			foreach ($model as $key => $value){
				//if(!empty($value -> coupons) && ($value -> coupons -> type) == COUPON_TYPE_REDENVELOPE){
				$data['list'][$k]['id'] = $value -> id;
				$data['list'][$k]['user_name'] = !empty($value -> user -> name)?$value -> user -> name:'';
				$data['list'][$k]['coupons_name'] = !empty($value -> coupons -> name)?$value -> coupons -> name:'';
				$data['list'][$k]['status'] = $value -> status;
				$data['list'][$k]['start_time'] = $value -> start_time;
				$data['list'][$k]['end_time'] = $value -> end_time;
				$data['list'][$k]['use_time'] = $value -> use_time;  
                                $user = User::model()->find('merchant_id=:merchant_id and id=:id',array(':merchant_id'=>$merchant_id,':id'=>$value->user_id));
                                        if($user)
                                        {    
                                            $data['list'][$k]['account'] = $user->account;
                                        } else {
                                            $data['list'][$k]['account'] = '';
                                        }
                                        $k++;
				//}
				
			}
		}
		$result ['status'] = ERROR_NONE;
		$result ['data'] = $data;
		return json_encode ( $result );
	}
	
	/**
	 * 创建优惠券
	 * $post  数组参数
	 * $date  有效日期
	 * $start_money 券面额（随机面额） 的起始额
	 * $end_money  券面额（随机面额） 的结束额
	 * $merchant_id 商户id
	 * $value_money  券面额类型
	 * $money 固定面额的值
	 * $start_time  发放时间开始时间
	 * $end_time   发放时间结束时间
	 * $valid_time 有效时间类型
	 */
	public function createYhq($post, $date, $start_money, $end_money, $merchant_id, $value_money, $money, $start_time, $end_time, $valid_time )
	{
		$result = array ();
		$errMsg = '';
		$flag = 0;
		
		//验证红包名称
		if (! isset ( $post['name'] ) || empty ( $post['name'] )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 券名必填';
			$flag = 1;
			Yii::app()->user -> setFlash('name','券名必填');
		}
		// 验证发放量
		if (! isset ( $post['num'] ) || empty ( $post['num'] )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 发放量必填';
			$flag = 1;
			Yii::app()->user -> setFlash('num','发放量必填');
		}else{
			if($post['num'] <= 0){
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = ' 发放量不合法';
				$flag = 1;
				Yii::app()->user -> setFlash('num','发放量不合法');
			}
		}
		// 验证用户领取数量
		if (! isset ( $post['receive_num'] ) || empty ( $post['receive_num'] )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 用户领取数量必填';
			$flag = 1;
			Yii::app()->user -> setFlash('receive_num','用户领取数量必填');
		}else{
			if($post['receive_num'] <= 0){
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 用户领取数量不合法';
				$flag = 1;
				Yii::app()->user -> setFlash('receive_num','用户领取数量不合法');
			}
		}
		
		if(!empty ( $post['num'] ) && !empty ( $post['receive_num'] )){
			if($post['num'] < $post['receive_num']){
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 发放数应大于用户领取数量';
				$flag = 1;
				Yii::app()->user->setFlash('receive_num','发放数应大于用户领取数量');
			}
		}
		
		//验证有效时间类型
		if (! isset ( $valid_time ) || empty ( $valid_time )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 有效时间类型必填';
			$flag = 1;
			Yii::app()->user -> setFlash('valid_time_type','有效时间类型必填');
		}
		//验证发放时间
		if (empty ( $start_time ) && empty ( $end_time )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 发放时间必填';
			$flag = 1;
			Yii::app()->user -> setFlash('time','发放时间必填');
		}
		
		//验证券面额类型
		if (! isset ( $value_money ) || empty ( $value_money )) {
			if($post['type'] == COUPON_TYPE_CASH){
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 券面额类型必填';
			$flag = 1;
			Yii::app()->user -> setFlash('value_money','券面额类型必填');
			}
		}
		
		//验证折扣数
		if(isset($post['type']) && $post['type'] == COUPON_TYPE_DISCOUNT){
		if (isset ( $post['discount'] ) || !empty ( $post['discount'] )) {
			if($post['discount']<=0 || $post['discount'] >10){
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 折扣设置不合法';
			$flag = 1;
			Yii::app()->user -> setFlash('discount','折扣设置不合法');
			}
		}
		}
		
		//验证有效时间固定时间是否合理
		if(!empty($date)){
			$Vdate = explode('-', $date);
			//如果有效期开始时间小于券发放时间开始时间
			if(strtotime($Vdate[0]) < strtotime($start_time)){
				$flag = 1;
				Yii::app()->user -> setFlash('birth','有效期不合理');
			}
		}

		$model = new Coupons();
		$model -> merchant_id = $merchant_id;
		$model -> type = $post['type'];
		$model -> name = $post['name'];
		$model -> start_time = $start_time;
		$model -> end_time = $end_time.' 23:59:59';
		if($value_money == FACE_VALUE_TYPE_FIXED){//如果选的是  固定面额
			$model -> fixed_value = $money;
		}elseif($value_money == FACE_VALUE_TYPE_RANDOM){//如果选的是  随机面额
			$model -> userdefined_value = $start_money.'-'.$end_money;
		}
		if($valid_time == VALID_TIME_TYPE_FIXED){//固定时间
			if (!empty($date)){
				$date_time = explode('-', $date);
				$validtime_start = $date_time[0];
				$validtime_end = $date_time[1];
				
				//$model -> validtime_start = date('Y-m-d'.' 00:00:00',strtotime($validtime_start));
				//$model -> validtime_end = date('Y-m-d'.' 23:59:59',strtotime($validtime_end));
				if(!empty($end_time)){
					//如果发放时间的结束时间小于固定时间的开始时间  则入库
					if(date('Y-m-d'.' 00:00:00',strtotime($start_time)) <= date('Y-m-d'.' 00:00:00',strtotime($validtime_start)) && 
					  (date('Y-m-d'.' 23:59:59',strtotime($end_time))<= date('Y-m-d'.' 23:59:59',strtotime($validtime_end)))){
						$model -> validtime_start = date('Y-m-d'.' 00:00:00',strtotime($validtime_start));
						$model -> validtime_end = date('Y-m-d'.' 23:59:59',strtotime($validtime_end));
					}else{ //如果发放时间的结束时间大于固定时间的开始时间  则两个日期有冲突
						$result ['status'] = ERROR_PARAMETER_MISS;
						$errMsg = $errMsg . ' 有效日期与发放日期不合逻辑';
						$flag = 1;
						Yii::app()->user -> setFlash('fixed_time','有效日期与发放日期不合逻辑');
					}
				}
			}else{
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 有效日期必填';
				$flag = 1;
				Yii::app()->user -> setFlash('fixed_time','有效日期必填');
			}
			
		}else{//相对时间
			//$model -> validtime_fixed_value = $post['validtime_fixed_value'];//天数
			if(!empty($post['validtime_fixed_value'])){
				$model -> validtime_fixed_value = $post['validtime_fixed_value'];//天数
			}else{
				$result ['status'] = ERROR_PARAMETER_MISS;
				$errMsg = $errMsg . ' 有效天数必填';
				$flag = 1;
				Yii::app()->user -> setFlash('relative_time','有效天数必填');
			}
		}
		
		
		
		//门店限制获取门店id的集合
	    if(!empty($post['use_store'])){
		 	$str_store_area_id = '';
		 	for($j=0;$j<count($post['use_store']);$j++){
		 		$str_store_area_id  = $str_store_area_id.$post['use_store'][$j].',';
		 	}
		 	$model -> use_store = ','.$str_store_area_id;
		 }else{
// 		 	$storeId = '';
// 		 	$store = Store::model()->findAll('flag=:flag',array(':flag'=>FLAG_NO));
// 		 	foreach ($store as $key => $value){
// 		 		$storeId = $storeId .','.($value -> id);
// 		 	}
// 		 	$model -> use_store = $storeId.',';

		 	$result ['status'] = ERROR_PARAMETER_MISS;
		 	$errMsg = $errMsg . ' 请勾选门店';
		 	$flag = 1;
		 	Yii::app()->user -> setFlash('use_store','请勾选门店');
		 }
		 
		 if ($flag == 1) {
		 	$result ['errMsg'] = $errMsg;
		 	return json_encode ( $result );
		 }
		
		$model -> num = $post['num'];
		$model -> receive_num = $post['receive_num'];
		
		if($post['type'] == COUPON_TYPE_EXCHANGE){//如果是兑换券   没有任何限制条件
			$model -> order_use_num = 2;
			$model -> if_with_userdiscount = 2;
			$model -> if_with_coupons = 2;
		}else{
		    $model -> order_use_num = !empty($post['order_use_num'])?$post['order_use_num']:'1';//单个订单可使用数量
		    $model -> if_with_userdiscount = !empty($post['if_with_userdiscount'])?'2':'1';//是否能与会员折扣同用 
		}
		$model -> refund_deal = isset($post['refund_deal'])?$post['refund_deal']:'2';
		$model -> use_illustrate = $post['use_illustrate'];
		$model -> create_time = date('Y-m-d h:i:s');
		
		$model -> discount = $post['discount']/10;//折扣数

		$model -> max_discount_money = $post['max_discount_money'];
		$model -> min_pay_money = !empty($post['min_pay_money'])?$post['min_pay_money']:'0';
			
		if ($model->save ()) {
			$result ['status'] = ERROR_NONE; // 状态码
			$result ['errMsg'] = ''; // 错误信息
			$result ['data'] = array (
					'id' => $model->id
			);
		} else {
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
			$result ['data'] = '';
		}
		
		return json_encode ( $result );
	}
	
	/**
	 * 优惠券列表
	 * @param $merchant_id 商户id  
	 * $keyword_name  按优惠券名称搜索
	 * $cou_type  按券类型搜索
	 */
	public function getYhqList($merchant_id,$keyword_name,$cou_type)
	{
		$result = array ();
		$criteria = new CDbCriteria ();
		if (! empty ( $merchant_id )) {
			$criteria->addCondition ( 'merchant_id=:merchant_id and flag=:flag and type!=:type' );
			$criteria->params = array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO,
					':type' => COUPON_TYPE_REDENVELOPE
			);
		}else{
			$criteria->addCondition ( 'flag=:flag and type!=:type' );
			$criteria->params = array (
					':flag' => FLAG_NO,
					':type' => COUPON_TYPE_REDENVELOPE
			);
		}
		
		//关键词搜索
		if(!empty($keyword_name)){
			$criteria -> addCondition("name=:name");
			$criteria -> params[':name'] = $keyword_name;
		}
		if(!empty($cou_type)){
			$criteria -> addCondition("type=:cou_type");
			$criteria -> params[':cou_type'] = $cou_type;
		}
		
		$criteria -> order = 'create_time desc';
		
		
		$pages = new CPagination(Coupons::model()->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		$model = Coupons::model ()->findAll ( $criteria );
		
		$data = array();
		if(!empty($model)){
			foreach ($model as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['name'] = $v -> name;
				$data['list'][$k]['type'] = $v -> type;
				$data['list'][$k]['start_time'] = $v -> start_time;
				$data['list'][$k]['end_time'] = $v -> end_time;
				$data['list'][$k]['create_time'] = $v -> create_time;
			}
			
			$result ['status'] = ERROR_NONE;
			$result ['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}		
		
		return json_encode ( $result );
	}
	
	/**
	 * 优惠券删除
	 */
	public function delYhq($id)
	{
		$result = array();
		$model = Coupons::model()->findByPk($id);
		if(!empty($model)){
			$model -> flag = FLAG_YES;
			if($model -> save()){
				$result ['status'] = ERROR_NONE;
			    $result['errMsg'] = ''; //错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败'; //错误信息
			}
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		
		return json_encode ( $result );
	}
	
	/**
	 * 获取某个商户下的所有门店
	 * $merchant_id  商户id
	 */
	public function getStoreForMerchangt($merchant_id)
	{
		$store_array = array();
		if(!empty($merchant_id)){
			$store = Store::model()->findAll('merchant_id=:merchant_id and flag=:flag',array(
					':merchant_id'=>$merchant_id,
					':flag' => FLAG_NO
			));
			foreach ($store as $k=>$v){
				$store_array[$v -> id] = $v -> name;
			}
		}else{
			$store = Store::model()->findAll();
			foreach ($store as $k=>$v){
				$store_array[$v -> id] = $v -> name;
			}
		}
		
		return $store_array;
	}
	
	/**
	 * 优惠券发放明细
	 */
	public function getYhqUsed($merchant_id)
	{
		$coupons_id = array();
		$data = array();
		$result = array();
	
		$criteria = new CDbCriteria();
		$coupons = Coupons::model()->findAll('merchant_id=:merchant_id and flag=:flag and type!=:type',
				array(':merchant_id'=>$merchant_id,':flag'=>FLAG_NO,':type'=>COUPON_TYPE_REDENVELOPE));
		foreach ($coupons as $k => $v){
			$coupons_id[] = $v -> id;
		}
		$criteria -> addInCondition('coupons_id', $coupons_id);
		$criteria -> order = 'use_time desc';
		$pages = new CPagination(UserCoupons::model()->with('coupons')->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		
		$model = UserCoupons::model()->findAll($criteria);
		if(!empty($model)){
			$k = 0;
			foreach ($model as $key => $value){
				//if(!empty($value -> coupons) && ($value -> coupons -> type) != COUPON_TYPE_REDENVELOPE){
					$data['list'][$k]['id'] = $value -> id;
					$data['list'][$k]['user_name'] = !empty($value -> user -> name)?$value -> user -> name:'';
					$data['list'][$k]['coupons_name'] = !empty($value -> coupons -> name)?$value -> coupons -> name:'';
					$data['list'][$k]['status'] = $value -> status;
					$data['list'][$k]['start_time'] = $value -> start_time;
					$data['list'][$k]['end_time'] = $value -> end_time;
					$data['list'][$k]['use_time'] = $value -> use_time;
                                        $user = User::model()->find('merchant_id=:merchant_id and id=:id',array(':merchant_id'=>$merchant_id,':id'=>$value->user_id));
                                        if($user)
                                        {    
                                            $data['list'][$k]['account'] = $user->account;
                                        } else {
                                            $data['list'][$k]['account'] = '';
                                        }
                                        $k++;
				//}
			}
		}
		$result ['status'] = ERROR_NONE;
		$result ['data'] = $data;
		return json_encode ( $result );
	}

    /**
     * 验证红包明是否相同
     * name 红包名称
     */
    public function checkRedEnvelopeName($merchant_id,$name)
    {
        $criteria = new CDbCriteria();
        $criteria -> addCondition("merchant_id=:merchant_id");
        $criteria -> params[':merchant_id'] = $merchant_id;
//        $criteria -> addCondition("name=:name");
//        $criteria -> params[':name'] = $name;
        $criteria -> addCondition("flag=:flag");
        $criteria -> params[':flag'] = FLAG_NO;
        $model=Coupons::model()->findAll($criteria);
        if(isset($model))
        {
            foreach($model as $key=>$value)
            {
                if($value['name']==$name)
                    return true;
            }
        }
        return false;
    }
    
    /*
     * 获取线上使用红包列表
     */
    public function getUseOnlineCouponList($merchant_id){
        $result = array();
        try {
            $coupons = Coupons::model() -> findAll('merchant_id = :merchant_id  and flag = :flag and if_invalid = :if_invalid',array(
                ':merchant_id' => $merchant_id,
//                 ':use_channel' => COUPONS_USE_CHANNEL_ONLINE,
                ':flag' => FLAG_NO,
                ':if_invalid' => IF_INVALID_NO,
//                 ':end_time' => date('Y-m-d h:i:s')
            ));
            $data = array();
            foreach ($coupons as $k => $v){
                $data[$k]['id'] = $v -> id;
                $data[$k]['title'] = $v -> title;
            }
            
            $result['data'] = $data;
            $result['status'] = ERROR_NONE;
            
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
}