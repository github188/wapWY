<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/protected/class/wechat/Wechat.php';
/**
 * 会员管理类
 */
class UserC extends mainClass {
	/**
	 * 添加会员等级
	 * $merchant_id 商户id
	 * $post 添加属性的数组
         * cover  会员卡图片地址
         * cardName  会员卡名称
	 */
	public function addUserGrade($merchant_id,$name,$points_rule,$discount,$discount_illustrate,$points_ratio,$cover,$cardName,$if_hideword,$rule_type,$birthday_rate) {
		
		$result = array ();
                $usergrade = UserGradeDraft::model()->count('merchant_id=:merchant_id and flag=:flag',array(':merchant_id'=>$merchant_id,':flag'=>FLAG_NO));
                if($usergrade < 5)
                {
                    $model = new UserGradeDraft();
                    $model->merchant_id = $merchant_id;
                    $model->name = $name;
                    $model->points_rule = $points_rule;
                    $model->discount = $discount/10;
                    $model->discount_illustrate = $discount_illustrate;
                    $model->points_ratio = $points_ratio;
                    $model->create_time = date ( 'Y-m-d H:i:s' );
                    $model -> if_hideword = $if_hideword;
                    $model->rule_type = $rule_type;
                    $model->birthday_rate = $birthday_rate;
                    
                    if(!empty($cover))
                    {
                        $model->membercard_img = $cover;
                    }
                    $model->membership_card_name = $cardName;
                    if ($model->save ()) {
                            $result ['status'] = ERROR_NONE;
                            $result ['errMsg'] = '';
                    } else {
                            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                            $result ['errMsg'] = '数据保存失败'; // 错误信息
                    }
                } else {
                    $result ['status'] = ERROR_REQUEST_FAIL; // 状态码
                    $result ['errMsg'] = '会员等级最多可添加5个等级'; // 错误信息
                }
		
		return json_encode($result);
	}
	
	/**
	 * 发布更新会员等级
	 * $merchant_id 商户id
	 * $post 添加属性的数组
	 * cover  会员卡图片地址
	 * cardName  会员卡名称
	 */
	public function releaseUserGrade($merchant_id) {
		$error = 0;
		$result = array ();
		$list = array();
		$default = array();
		$GradeDraft = json_decode($this -> getSetUserGradeDraft($merchant_id),true);
		$list = $GradeDraft['data']['list'];
		$default = $GradeDraft['datas']['list'];
		//更新默认等级
		$id = $default['id'];
		$model = UserGrade::model()->findByPk($id);
		if (empty($model)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '修改的会员等级不存在'; //错误信息
		}else{
			$model -> name = $default['name'];
			if($model -> points_rule != $default['points_rule']) {
				$model->points_rule = $default['points_rule'];
			}

			$model -> discount_illustrate = $default['discount_illustrate'];
			if($model->discount != $default['discount']){
				$model->discount = $default['discount']/10;
			}
			if(!empty($default['membercard_img']))
			{
				$model -> membercard_img = $default['membercard_img'];
			}
			$model -> membership_card_name = $default['membership_card_name'];
			$model -> last_time = date('Y-m-d H:i:s');
			$model -> if_hideword = $default['if_hideword'];
			$model -> rule_type = $default['rule_type'];
			$model -> birthday_rate = $default['birthday_rate'];
		}
		
		if($model -> update()){
		}else{
			$error++;
		}
		
		//更新其他等级
		foreach ($list as $k => $v){
			$id = $v['id'];
			$model = UserGrade::model()->findByPk($id);
			if (empty($model)) {
					$model = new UserGrade();
					$model->id = $v['id'];
					$model->merchant_id = $merchant_id;
					$model->name = $v['name'];
					$model->points_rule = $v['points_rule'];
					$model->discount = $v['discount'];
					$model->discount_illustrate = $v['discount_illustrate'];
					$model->points_ratio = $v['points_ratio'];
					$model -> membercard_img = $v['membercard_img'];
					$model -> membership_card_name = $v['membership_card_name'];
					$model->create_time = $v['create_time'];
	
					$model -> if_hideword = $v['if_hideword'];
					$model -> rule_type = $v['rule_type'];
					$model -> birthday_rate = $v['birthday_rate'];
					$model->save();
			}else{
				$model -> name = $v['name'];
				if($model -> points_rule != $v['points_rule']) {
					$model->points_rule = $v['points_rule'];
				}
				$model -> points_ratio = $v['points_ratio'];
				$model -> discount_illustrate = $v['discount_illustrate'];
				if($model->discount != $v['discount']){
					$model->discount = $v['discount']/10;
				}
				if(!empty($v['membercard_img']))
				{
					$model -> membercard_img = $v['membercard_img'];
				}
				$model -> membership_card_name = $v['membership_card_name'];
				$model -> last_time = date('Y-m-d H:i:s');
				$model -> if_hideword = $v['if_hideword'];
				$model -> rule_type = $v['rule_type'];
				$model -> birthday_rate = $v['birthday_rate'];

				if($model -> update()){
				}else {
					$error++;
				}
			}
		}

		//当草稿有删除等级时
		$Grade = json_decode($this -> getSetUserGrade($merchant_id),true);
		$GradeDraft = json_decode($this -> getSetUserGradeDraft($merchant_id),true);
		$res = $this -> contrastGrade($Grade,$GradeDraft);
		$revertid = array();
		if(!empty($res['revertid']) ){
			
			$revertid = $res['revertid'];
			foreach($revertid as $k => $v) {
				$model = UserGrade::model()->findByPk($v);
				$model -> flag = FLAG_YES;
				$model -> save();
			}
		}
		
// 		//当草稿有新增会员等级
// 		$addid = array();
// 		if(!empty($res['addid'])){
// 			$addid = $res['addid'];
// 			foreach ($addid as $k => $v){
// 				$model = new UserGrade();
// 				$model->id = $v;
// 				$model->merchant_id = $merchant_id;
// 				$model->name = $v['name'];
// 				$model->points_rule = $v['points_rule'];
// 				$model->discount = $v['discount'];
// 				$model->discount_illustrate = $v['discount_illustrate'];
// 				$model->points_ratio = $v['points_ratio'];
// 				$model -> membercard_img = $v['membercard_img'];
// 				$model -> membership_card_name = $v['membership_card_name'];
// 				$model->create_time = $v['create_time'];
				
// 				$model -> if_hideword = $v['if_hideword'];
// 				$model -> rule_type = $v['rule_type'];
// 				$model -> birthday_rate = $v['birthday_rate'];
// 				$model->save();
// 			}
// 		}
		
		//保存上一次的纪录
		
// 		$bak = UserGradeBak::model() -> find('merchant_id =:merchant_id and flag =:flag',array(
// 				':merchant_id' => $merchant_id,
// 				':flag' => FLAG_NO
// 		));
// 		$history = $this->getSetUserGrade($merchant_id);
// 		$model = new UserGradeBak();
// 		if (!empty($bak) && $model->info != $history){
// 			$model->info = $history;
// 			$model -> last_time = date('Y-m-d H:i:s');
// 			if($model->update()){
// 			}else{
// 				$error++;
// 			}
// 		}else{
			
// 			$model->merchant_id = $merchant_id;
// 			$model->info = $history;
// 			$model->create_time = date('Y-m-d H:i:s');
// 			$model -> last_time = date('Y-m-d H:i:s');
// 			if($model->save() > 0){				
// 			}else{
// 				$error++;
// 			}
// 		}
		
		
		if($error == 0){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据更新失败'; //错误信息
		}	
			
		return json_encode($result);
	}
	
	/**
	 * 还原上次的发布点
	 */
	public function RevertUserGrade($merchant_id){
		$error = 0;
		$result = array ();
		$list = array();
		$default = array();
		$Grade = json_decode($this -> getSetUserGrade($merchant_id),true);
		$list = $Grade['data']['list'];
		$default = $Grade['datas']['list'];
		//更新默认等级
		$id = $default['id'];
		$model = UserGradeDraft::model()->findByPk($id);
		if (empty($model)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '修改的会员等级不存在'; //错误信息
		}else{
			$model -> name = $default['name'];
			if($model -> points_rule != $default['points_rule']) {
				$model->points_rule = $default['points_rule'];
			}
		
			$model -> discount_illustrate = $default['discount_illustrate'];
			if($model->discount != $default['discount']){
				$model->discount = $default['discount']/10;
			}
			if(!empty($default['membercard_img']))
			{
				$model -> membercard_img = $default['membercard_img'];
			}
			$model -> membership_card_name = $default['membership_card_name'];
			$model -> last_time = date('Y-m-d H:i:s');
			$model -> if_hideword = $default['if_hideword'];
			$model -> rule_type = $default['rule_type'];
			$model -> birthday_rate = $default['birthday_rate'];
		}
		
		if($model -> update()){
		}else{
			$error++;
		}
		
		//更新其他等级
		foreach ($list as $k => $v){
			$id = $v['id'];
			$model = UserGradeDraft::model()->findByPk($id);
			if (empty($model)) {

			}else{
				$model -> name = $v['name'];
				if($model -> points_rule != $v['points_rule']) {
					$model->points_rule = $v['points_rule'];
				}
				$model -> points_ratio = $v['points_ratio'];
				$model -> discount_illustrate = $v['discount_illustrate'];
				if($model->discount != $v['discount']){
					$model->discount = $v['discount']/10;
				}
				if(!empty($v['membercard_img']))
				{
					$model -> membercard_img = $v['membercard_img'];
				}
				$model -> membership_card_name = $v['membership_card_name'];
				$model -> last_time = date('Y-m-d H:i:s');
				$model -> if_hideword = $v['if_hideword'];
				$model -> rule_type = $v['rule_type'];
				$model -> birthday_rate = $v['birthday_rate'];
				
// 				if($model -> flag == FLAG_YES){
// 					$model -> flag ==FLAG_NO;
// 				}
				if($model -> update()){
				}else {
					$error++;
				}
			}
		}
		//当草稿有新增等级时，把多余的记录删掉
		$Grade = json_decode($this -> getSetUserGrade($merchant_id),true);
		$GradeDraft = json_decode($this -> getSetUserGradeDraft($merchant_id),true);
		$res = $this -> contrastGrade($Grade,$GradeDraft);
		
		$addid = array();
		if(!empty($res['addid'])){
			$addid = $res['addid'];
			foreach($addid as $k => $v) {
				$model = UserGradeDraft::model()->findByPk($v);
				$model -> flag = FLAG_YES;
				$model -> save();
			}
		}
		//当草稿删掉原来的等级时
		$revertid = array();
		if(!empty($res['revertid'])){
			$revertid = $res['revertid'];
			foreach($revertid as $k => $v) {
				$model = UserGradeDraft::model()->findByPk($v);
				$model -> flag = FLAG_NO;
				$model -> save();
			}
		}

		
		if($error == 0){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据更新失败'; //错误信息
		}
			
		return json_encode($result);
		
	}
	
	/**
	 * 获取会员等级设置
	 * $merchant_id 商户id
	 */
	public function getSetUserGrade($merchant_id)
	{
		$result = array();
		$criteria = new CDbCriteria();
                $criteria -> order = 'points_rule asc';
		if (! empty ( $merchant_id )) {
			$criteria->addCondition ( 'merchant_id=:merchant_id and flag=:flag' );
			$criteria->params = array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO
			);
		}else{
			$criteria->addCondition ( 'flag=:flag' );
			$criteria->params = array (
					':flag' => FLAG_NO
			);
		}		
		$model = UserGrade::model()->findAll($criteria);
		$countusergrade = UserGrade::model()->count($criteria);
		$data = array ();
                $datas = array();
		if(!empty($model)){
			foreach ( $model as $k => $v ) {
              	if($v['if_default'] == USER_GRADE_DEFAULT_NO)
              	{
					$data ['list'] [$k] ['id'] = $v->id;
					$data ['list'] [$k] ['name'] = $v->name;
					$data ['list'] [$k] ['points_rule'] = $v->points_rule;
					$data ['list'] [$k] ['points_ratio'] = $v->points_ratio;
					$data ['list'] [$k] ['discount'] = $v->discount;
					$data ['list'] [$k] ['create_time'] = $v->create_time;
               	 	$data ['list'] [$k] ['discount_illustrate'] = $v->discount_illustrate;
               		$data ['list'] [$k] ['membercard_img'] = $v->membercard_img;
            		$data ['list'] [$k] ['membership_card_name'] = $v->membership_card_name;
            		$data ['list'] [$k] ['if_hideword'] = $v->if_hideword;
					$count = $this -> getUserCount($v->id,$merchant_id);
					$data ['list'] [$k] ['count'] = $count;
                  	$data ['list'] [$k] ['if_default'] = $v -> if_default;
                  	$data ['list'] [$k] ['rule_type'] = $v->rule_type;
                  	$data ['list'] [$k] ['birthday_rate'] = $v->birthday_rate;
               	}
			}
           	$grade = UserGrade::model()->find('merchant_id=:merchant_id and if_default=:if_default and flag=:flag',array(':merchant_id'=>$merchant_id,':if_default'=>USER_GRADE_DEFAULT_YES,':flag'=>FLAG_NO));
           	if(!empty($grade)) {      
              	$datas ['list'] ['id'] = $grade->id;
               	$datas ['list'] ['name'] = $grade->name;
               	$datas ['list'] ['points_rule'] = $grade->points_rule;
               	$datas ['list'] ['discount'] = $grade->discount;
              	$datas ['list'] ['create_time'] = $grade->create_time;
              	$datas ['list'] ['discount_illustrate'] = $grade->discount_illustrate;
               	$datas ['list'] ['membercard_img'] = $grade->membercard_img;
               	$datas ['list'] ['membership_card_name'] = $grade->membership_card_name; 
               	$datas ['list'] ['if_hideword'] = $grade->if_hideword;
               	$count = $this -> getUserCount($grade->id,$merchant_id);
              	$datas ['list'] ['count'] = $count;
              	$datas ['list'] ['if_default'] = $grade -> if_default;
              	$datas ['list'] ['rule_type'] = $grade->rule_type;
              	$datas ['list'] ['birthday_rate'] = $grade->birthday_rate;
         	}
			$result ['status'] = ERROR_NONE;
			$result ['data']  = $data;
                        $result ['datas'] = $datas;
                        $result ['countusergrade'] = $countusergrade;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}

		return json_encode ( $result );
	}
	
	/**
	 * 获取会员等级设置(草稿)
	 * $merchant_id 商户id
	 */
	public function getSetUserGradeDraft($merchant_id)
	{
		$result = array();
		$criteria = new CDbCriteria();
		$criteria -> order = 'points_rule asc';
		if (! empty ( $merchant_id )) {
			$criteria->addCondition ( 'merchant_id=:merchant_id and flag=:flag' );
			$criteria->params = array (
					':merchant_id' => $merchant_id,
					':flag' => FLAG_NO
			);
		}else{
			$criteria->addCondition ( 'flag=:flag' );
			$criteria->params = array (
					':flag' => FLAG_NO
			);
		}
		$model = UserGradeDraft::model()->findAll($criteria);
		$countusergrade = UserGradeDraft::model()->count($criteria);
		$data = array ();
		$datas = array();
		if(!empty($model)){
			foreach ( $model as $k => $v ) {
				if($v['if_default'] == USER_GRADE_DEFAULT_NO)
				{
					$data ['list'] [$k] ['id'] = $v->id;
					$data ['list'] [$k] ['name'] = $v->name;
					$data ['list'] [$k] ['points_rule'] = $v->points_rule;
					$data ['list'] [$k] ['discount'] = $v->discount;
					$data ['list'] [$k] ['points_ratio'] = $v->points_ratio;
					$data ['list'] [$k] ['create_time'] = $v->create_time;
					$data ['list'] [$k] ['discount_illustrate'] = $v->discount_illustrate;
					$data ['list'] [$k] ['membercard_img'] = $v->membercard_img;
					$data ['list'] [$k] ['membership_card_name'] = $v->membership_card_name;
					$data ['list'] [$k] ['if_hideword'] = $v->if_hideword;
					$count = $this -> getUserCount($v->id,$merchant_id);
					$data ['list'] [$k] ['count'] = $count;
					$data ['list'] [$k] ['if_default'] = $v -> if_default;
					$data ['list'] [$k] ['rule_type'] = $v->rule_type;
					$data ['list'] [$k] ['birthday_rate'] = $v->birthday_rate;
				}
			}
			$grade = UserGradeDraft::model()->find('merchant_id=:merchant_id and if_default=:if_default and flag=:flag',array(':merchant_id'=>$merchant_id,':if_default'=>USER_GRADE_DEFAULT_YES,':flag'=>FLAG_NO));
			if(!empty($grade)) {
				$datas ['list'] ['id'] = $grade->id;
				$datas ['list'] ['name'] = $grade->name;
				$datas ['list'] ['points_rule'] = $grade->points_rule;
				$datas ['list'] ['discount'] = $grade->discount;
				$datas ['list'] ['create_time'] = $grade->create_time;
				$datas ['list'] ['discount_illustrate'] = $grade->discount_illustrate;
				$datas ['list'] ['membercard_img'] = $grade->membercard_img;
				$datas ['list'] ['membership_card_name'] = $grade->membership_card_name;
				$datas ['list'] ['if_hideword'] = $grade->if_hideword;
				$count = $this -> getUserCount($grade->id,$merchant_id);
				$datas ['list'] ['count'] = $count;
				$datas ['list'] ['if_default'] = $grade -> if_default;
				$datas ['list'] ['rule_type'] = $grade->rule_type;
				$datas ['list'] ['birthday_rate'] = $grade->birthday_rate;
			}
			$result ['status'] = ERROR_NONE;
			$result ['data']  = $data;
			$result ['datas'] = $datas;
			$result ['countusergrade'] = $countusergrade;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}

		return json_encode ( $result );
	}	
	
	
	/**
	 * 删除会员等级（会员人数为0的才能删）
	 * $id  会员等级id
	 */
	public function delUserGrade($id,$merchant_id)
	{
		$result = array();
		
		$is_exit = $this->isUserCount($id,$merchant_id);
		if($is_exit){//存在会员人数  不能删除
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
			$url = Yii::app()->createUrl('mCenter/user/setUserGrade');
			echo "<script>alert('该会员等级存在会员，不能删除');window.location.href='$url'</script>";
		}else{//不存在  进行删除
			$model = UserGradeDraft::model()->findByPk($id);
			$model -> flag = FLAG_YES;
			if($model -> save()){
				$result ['status'] = ERROR_NONE;
				$result['errMsg'] = ''; //错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败'; //错误信息
			}
		}
		return json_encode($result);
	}
	
	/**
	 * 编辑会员等级
	 * $id  会员等级id
	 * $post 添加属性的数组
	 */
	public function editUserGrade($name,$points_rule='',$points_ratio,$discount_illustrate,$discount,$id,$cover='',$cardName,$if_hideword,$rule_type,$birthday_rate)
	{
		$result = array();
		$model = UserGradeDraft::model()->findByPk($id);
		if (empty($model)) {
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '修改的会员等级不存在'; //错误信息
		}else{			
                        $model -> name = $name;
                        if(!empty($points_rule))
                        {
                            if($model -> points_rule != $points_rule) {
                                $model->points_rule = $points_rule;
                            }
                        }
                        $model -> points_ratio = $points_ratio;
                        $model -> discount_illustrate = $discount_illustrate;
                        if($model->discount != $discount){
                            $model->discount = $discount/10;
                        }
                        if(!empty($cover))
                        {
                            $model -> membercard_img = $cover;
                        }
                        $model -> membership_card_name = $cardName;
			$model -> last_time = date('Y-m-d H:i:s');
			$model -> if_hideword = $if_hideword;
			$model -> rule_type = $rule_type;
			$model -> birthday_rate = $birthday_rate;
			
			if($model -> update()){
				$result ['status'] = ERROR_NONE;
				$result['errMsg'] = ''; //错误信息
			}else{
				$result ['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败'; //错误信息
			}
		}
		
		return json_encode($result);
	}
	
	
	/**
	 * 对比会员等级正式和草稿的数据
	 * $Grade 当前使用的会员等级规则数据
	 * $GradeDraft 修改会员等级的草稿
	 */
	public function contrastGrade($Grade,$GradeDraft){
		$res = array();
		//会员等级数量
		$count1 = $Grade['countusergrade'];
		$count2 = $GradeDraft['countusergrade'];
		//其他等级的二维数组
		$now = $Grade['data']['list'];
		$draft = $GradeDraft['data']['list'];
		//默认等级的数组
		$now1 = $Grade['datas']['list'];
		$draft1 = $GradeDraft['datas']['list'];
		//其他会员等级的id数组
		$arr1 = array();
		$arr2 = array();
		foreach ($now as $k=>$v){
			$arr1[] = $v['id'];
		}
		foreach ($draft as $k=>$v){
			$arr2[] = $v['id'];
		}
		//给数组将维
		$rtnow =array();
		$rtdraft = array();
		$this->reduceDimension($now,$rtnow);
		$this->reduceDimension($draft,$rtdraft);
	
		
		//对比数组
		$now1['if_hideword']=$now1['if_hideword']*10;
		$now1['rule_type']=$now1['rule_type']*10;
		$draft1['if_hideword']=$draft1['if_hideword']*10;
		$draft1['rule_type']=$draft1['rule_type']*10;
		$diff = array_diff($rtdraft, $rtnow);
		$diff1 = array_diff($draft1, $now1);				
		$now1['if_hideword']=$now1['if_hideword']/10;
		$now1['rule_type']=$now1['rule_type']/10;
		$draft1['if_hideword']=$draft1['if_hideword']/10;
		$draft1['rule_type']=$draft1['rule_type']/10;
		$addid = array();		
		foreach ($arr2 as $k => $v){
			if(!in_array($v, $arr1)){
				$addid[] = $v;
			}
		}
		$revertid = array();
		foreach ($arr1 as $k => $v){
			if(!in_array($v, $arr2)){
				$revertid[] = $v;
			}
		}
		
		//找出剩余需修改的类型的等级
		$num = 0;
		$change_type = 5;
		foreach ($draft as $k => $v){
			if ($v['rule_type'] == 5){
				$num++;
			}else{
				$change_type = $v['rule_type'];
			}
		}
		if ($draft1['rule_type'] == 5){
			$num++;
		}else{
			$change_type = $draft1['rule_type'];
		}

		
		// 		var_dump($arr1);
		// 		var_dump($arr2);
 		// 		var_dump($now1);
 		//	  	var_dump($draft1);			  	
		//		var_dump($rtdraft);
 		//		var_dump($rtnow);
		// 		var_dump($addid);
		// 		var_dump($revertid);
 		//		var_dump($diff);
 		//		var_dump($diff1);

		if ( empty($diff) && empty($diff1)){
			$res['grade'] = $Grade;
			$res['change'] = 0;
			$res['change_type'] = $change_type;
		}else {
			$res['grade'] = $GradeDraft;
			$res['change'] = 1;
			$res['diff'] = $diff;
			$res['diff1'] = $diff1;
			$res['addid'] = $addid;
			$res['revertid'] = $revertid;
			$res['num'] = $num;
			$res['change_type'] = $change_type;
		}
		return $res; 
	}
	
	function myarray_diff($array_1, $array_2) {
		$array_2 = array_flip($array_2);
		foreach ($array_1 as $key => $item) {
			if (isset($array_2[$item])) {
				unset($array_1[$key]);
			}
		}
	
		return $array_1;
	}
	
	public function reduceDimension($arr, &$rt) {
		if (is_array($arr)) {
			foreach ($arr as $v) {
				if (is_array($v)) {
					$this->reduceDimension($v, $rt);
				} else {
					$rt[] = $v;
				}
			}
		}
		return $rt;
	}
	
	/**
	 * 清除草稿的会员等级条件
	 */
	public function clearGradeType($GradeDraft){
		$default = $GradeDraft['datas']['list'];
		$another = $GradeDraft['data']['list'];
		//清空默认等级的条件
		$model = UserGradeDraft::model()->findByPk($default['id']);
		$model->rule_type = 5;
		$model->save();
		foreach ($another as $k => $v){
			$model = UserGradeDraft::model()->findByPk($v['id']);
			$model->rule_type = 5;
			$model->points_rule = null;
			$model->save();
		}
	}
	
	/**
	 * 获取会员等级详情
	 * $id  会员等级id
	 */
	public function getUserGradeDetails($id)
	{
		$result = array();
		$data = array();
		
		$model = UserGrade::model()->findByPk($id);
		if(!empty($model)){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';
		
			$data ['list'] ['id'] = $model->id;
			$data ['list'] ['name'] = $model->name;
			$data ['list'] ['points_rule'] = $model->points_rule;
			$data ['list'] ['discount'] = $model->discount;
			$data ['list'] ['discount_illustrate'] = $model->discount_illustrate;
           	$data ['list'] ['membercard_img'] = $model->membercard_img;
           	$data ['list'] ['membership_card_name'] = $model->membership_card_name;
			$data ['list'] ['create_time'] = $model->create_time;
          	$data ['list'] ['points_ratio'] = $model->points_ratio;
          	$data ['list'] ['if_hideword'] = $model->if_hideword;
          	$data ['list'] ['rule_type'] = $model->rule_type;
          	$data ['list'] ['birthday_rate'] = $model->birthday_rate;
          	
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		$result['data'] = $data;
		return json_encode($result);
	}
	
	/**
	 * 获取会员等级详情(草稿)
	 * $id  会员等级id
	 */
	public function getUserGradeDraftDetails($id)
	{	
		$model = UserGradeDraft::model()->findByPk($id);
		return $model;
	}
	
	/**
	 * 基本设置
	 * $merchant_id 商户id
	 * $post 添加属性的数组
	 */
	public function basicSet($merchant_id,$post)
	{
		$result = array();
                if(empty($post))
                {
                    $post = IF_STORED_NO;
                }
		$model = Merchant::model()->findByPk($merchant_id);		
		if(!empty($model)){
			$model -> if_stored = $post;			
			$model -> last_time = date('Y-m-d H:i:s');
			if($model -> save()){
				$result['status'] = ERROR_NONE;
				$result['errMsg'] = '';
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '数据保存失败';
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		return json_encode($result);
	}
	
	//判断储值表是否为空，不为空开启的储值功能不允许关闭
        /**
         * 
         * @param type $merchant_id 商户id
         */
        public function getStored($merchant_id)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchant_id) && empty($merchant_id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $stored = Stored::model()->findall('merchant_id=:merchant_id and flag=:flag',array(':merchant_id'=>$merchant_id,':flag'=>FLAG_NO));
                if($stored)
                {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '储值数据不为空，不允许修改';
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据，可关闭储值功能';
                }
            }
            return json_encode($result);
        }

        /**
	 * 判断会员等级名称是否存在
	 * $name  会员等级名称
	 * $merchant_id 商户id
	 * return  true：存在       false：不存在
	 */
	public function isUserGradeNameExit($name,$id='',$merchant_id)
	{
		if(empty($id)){

		  $model = UserGrade::model()->findAll('name=:name and flag=:flag and merchant_id=:merchant_id',array(
		  		':name'=>trim($name),
		  		':flag' => FLAG_NO,
		  		':merchant_id' => $merchant_id
		  ));

		  if(count($model)>0){
			 return true;
		  }
		     return false;
		}else{
			$model = UserGrade::model()->findAll('name=:name and id!=:id and flag=:flag and merchant_id=:merchant_id',array(
					':name'=>trim($name),
					':id'=>$id,
					':flag' => FLAG_NO,
                                        ':merchant_id' => $merchant_id
			));
			if(count($model)>0){
				return true;
			}
			return false;
		}
	}
	
	/**
	 * 根据id获取会员等级名称
	 * @param  $userGrandId  
	 * @return string
	 */
	public function getUserGrandName($userGrandId)
	{
		$model = UserGrade::model()->findByPk($userGrandId);
		if(!empty($model)){
			return $model -> name;
		}else{
			return '';
		}
	}
	
	/**
	 * 根据id获取积分要求
	 * @param unknown $userGrandId
	 * @return string
	 */
	public function getUserGrandPointRule($userGrandId)
	{
		$model = UserGrade::model()->findByPk($userGrandId);
		if(!empty($model)){
			return $model -> points_rule;
		}else{
			return '';
		}
	}
	
	/**
	 * 根据id获取生日积分倍率
	 * @param unknown $userGrandId
	 * @return string
	 */
	public function getUserGrandBirthdayRate($userGrandId)
	{
		$model = UserGrade::model()->findByPk($userGrandId);
		if(!empty($model)){
			return $model -> birthday_rate;
		}else{
			return '';
		}
	}
	
	
	/**
	 * 判断会员等级是否存在会员
	 * $membershipgrade_id  用户表的会员等级id
	 * $merchant_id 商户id
	 * return  true：存在       false：不存在
	 */
	public function isUserCount($membershipgrade_id,$merchant_id)
	{
		$model = User::model()->findAll('membershipgrade_id=:membershipgrade_id and merchant_id=:merchant_id',
				              array(':membershipgrade_id'=>$membershipgrade_id,':merchant_id'=>$merchant_id));
		if(count($model)>0){
			return true;
		}
		return false;
	}
	
	/**
	 * 获取会员等级的会员人数
	 * $membershipgrade_id  会员等级id
	 * $merchant_id 商户id
	 * 返回人数
	 */
	public function getUserCount($membershipgrade_id,$merchant_id)
	{
		$model = User::model()->findAll('membershipgrade_id=:membershipgrade_id and merchant_id=:merchant_id',
				              array(':membershipgrade_id'=>$membershipgrade_id,':merchant_id'=>$merchant_id));
		
		return count($model);
	}
        
        //会员列表
        /**
         * merchantId 商户id
         * account   账号
         * id       会员id
         */
        public function UserList($merchantId,$id='',$account='',$groups='',$usergroups='')
        {   
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $criteria = new CDbCriteria();
                if(!empty($account))
                {
                    $criteria->addCondition("account like '%$account%'");
                }
                if(!empty($groups))
                {
                    $criteria->addCondition('membershipgrade_id = :membershipgrade_id');
                    $criteria->params[':membershipgrade_id'] = $groups;
                }
                if(!empty($usergroups))
                {
                    $criteria->addCondition('group_id = :group_id');
                    $criteria->params[':group_id'] = $usergroups;
                }
                $criteria->addCondition('merchant_id = :merchant_id');
                $criteria->params[':merchant_id'] = $merchantId; 
                $user = User::model()->findall($criteria);
                if($user) 
                {             
                    $data = array();
                    if(!empty($id))
                    {
                        $user = User::model()->find('id=:id',array(':id'=>$id));
                        $data['account']                = $user->account;
                        $data['pwd']                    = $user->pwd;
                        $data['avatar']                 = $user->avatar;
                        $data['alipay_avatar']          = $user->alipay_avatar;
                        $data['nickname']               = $user->nickname;                
                        $data['name']                   = $user->name;
                        $data['sex']                    = $user->sex;
                        $data['birthday']               = $user->birthday;
                        $data['social_security_number'] = $user->social_security_number;
                        $data['email']                  = $user->email;
                        $data['marital_status']         = $user->marital_status;
                        $data['work']                   = $user->work;
                        $data['free_secret']            = $user->free_secret;
                        $data['money']                  = $user->money;
                        $data['points']                 = $user->points;
                        $data['group_id']               = $user->group_id;
                        $data['create_time']            = $user->create_time;
                        $data['last_time']              = $user->last_time;
                        $data['membership_card_no']     = $user->membership_card_no;
                        $data['login_time']             = $user->login_time;
                        $data['login_ip']               = $user->login_ip;
                        $data['regist_time']            = $user->regist_time;
                        $data['wechat_id']              = $user->wechat_id;
                        $data['alipay_fuwu_id']         = $user->alipay_fuwu_id;
                        $data['address']                = $user->address;  
                        $data['from']                	= $user->from;
                    } else { 
                        foreach ($user as $key => $value) 
                        {
                            $data[$key]['id']                     = $value['id'];
                            $data[$key]['account']                = $value['account'];
                            $data[$key]['pwd']                    = $value['pwd'];
                            $data[$key]['avatar']                 = $value['avatar'];
                            $data[$key]['nickname']               = $value['nickname'];
                            $data[$key]['name']                   = $value['name'];
                            $data[$key]['sex']                    = $value['sex'];
                            $data[$key]['birthday']               = $value['birthday'];
                            $data[$key]['social_security_number'] = $value['social_security_number'];
                            $data[$key]['email']                  = $value['email'];
                            $data[$key]['marital_status']         = $value['marital_status'];
                            $data[$key]['work']                   = $value['work'];
                            $data[$key]['free_secret']            = $value['free_secret'];
                            $data[$key]['money']                  = $value['money'];
                            $data[$key]['points']                 = $value['points'];
                            $data[$key]['group_id']               = $value['group_id'];
                            $data[$key]['create_time']            = $value['create_time'];
                            $data[$key]['last_time']              = $value['last_time'];
                            $data[$key]['membership_card_no']     = $value['membership_card_no'];
                            $data[$key]['login_time']             = $value['login_time'];
                            $data[$key]['login_ip']               = $value['login_ip'];
                            $data[$key]['regist_time']            = $value['regist_time'];
                            $data[$key]['wechat_id']              = $value['wechat_id'];
                            $data[$key]['alipay_fuwu_id']         = $value['alipay_fuwu_id'];
                            $data[$key]['address']                = $value['address'];
                            $data[$key]['membershipgrade_id']     = $value['membershipgrade_id'];
                            $usergrade = UserGrade::model()->find('id=:id and merchant_id=:merchant_id',array(':id'=>$value['membershipgrade_id'],':merchant_id'=>$merchantId));
                            $data[$key]['grade_name']             = isset($usergrade->name) ? $usergrade->name : '';
                            $data[$key]['points_rule']            = isset($usergrade->points_rule) ? $usergrade->points_rule : '';
                            $data[$key]['discount']               = isset($usergrade->discount) ? $usergrade->discount : '';
                            $data[$key]['discount_illustrate']    = isset($usergrade->discount_illustrate) ? $usergrade->discount_illustrate : '';
                            $data[$key]['membercard_img']         = isset($usergrade->membercard_img) ? $usergrade->membercard_img : '';
                            $data[$key]['membership_card_name']   = isset($usergrade->membership_card_name) ? $usergrade->membership_card_name : '';
                        }
                    }
                    $result['status'] = ERROR_NONE;
                    $result['data']   = $data;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }
        
        //分组会员等级
        /**
         * merchantId
         */
        public function Group($merchantId)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $grade = UserGrade::model()->findall('merchant_id=:merchant_id and flag = :flag',array(':merchant_id'=>$merchantId,':flag'=>FLAG_NO));
                if($grade)
                {
                    $data = array();
                    foreach ($grade as $key => $value) 
                    {
                        $data[$key]['id']                   = $value['id'];
                        $data[$key]['name']                 = $value['name'];
                        $data[$key]['points_rule']          = $value['points_rule'];
                        $data[$key]['discount']             = $value['discount'];
                        $data[$key]['discount_illustrate']  = $value['discount_illustrate'];
                        $data[$key]['membercard_img']       = $value['membercard_img'];
                        $data[$key]['create_time']          = $value['create_time'];
                        $data[$key]['membership_card_name'] = $value['membership_card_name'];
                        $num = User::model()->count('membershipgrade_id=:membershipgrade_id',array(':membershipgrade_id'=>$value['id']));
                        if($num)
                        {
                            $data[$key]['num'] = $num;
                        } else {
                            $data[$key]['num'] = 0;
                        }
                    }            
                    $result['status'] = ERROR_NONE;
                    $result['data']   = $data;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }
        
        //自定义分组
        /**
         * $merchantId 商户id
         */
        public function UserGroup($merchantId)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $usergroup = UserGroup::model()->findAll('merchant_id=:merchant_id and flag = :flag',array(':merchant_id'=>$merchantId,':flag'=>FLAG_NO));
                if($usergroup)
                {
                    $data = array();
                    foreach ($usergroup as $key => $value) 
                    {
                        $data[$key]['group_id'] = $value['id'];
                        $data[$key]['group_name'] = $value['name']; 
                        $num = User::model()->count('group_id=:group_id',array(':group_id'=>$value['id']));
                        if($num)
                        {
                            $data[$key]['num'] = $num;
                        } else {
                            $data[$key]['num'] = 0;
                        }
                    }
                    $result['status'] = ERROR_NONE;
                    $result['data']   = $data;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }
        
        //添加分组
        /**
         * $merchantId 商户id
         * groupname   分组名称
         */
        public function AddGroup($merchantId,$groupname)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if(isset($groupname) && empty($groupname))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数groupname缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $usergroup              = new UserGroup();
                $usergroup->name        = $groupname;
                $usergroup->merchant_id = $merchantId;
                $usergroup->create_time = new CDbExpression('now()');
                $usergroup->flag        = FLAG_NO;
                if($usergroup->save())
                {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '保存失败';
                }
            }
            return json_encode($result);
        }
        
        //修改分组
        /**
         * $merchantId 商户id
         * groupname   分组名称
         * id       分组id
         */
        public function EditGroup($merchantId,$groupname,$id)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if(isset($groupname) && empty($groupname))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数groupname缺失';
                $flag = 1;
            }
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $usergroup              = UserGroup::model()->find('id=:id and flag=:flag',array(':id'=>$id,':flag'=>FLAG_NO));
                $usergroup->name        = $groupname;
                $usergroup->merchant_id = $merchantId;
                $usergroup->last_time   = new CDbExpression('now()');
                if($usergroup->save())
                {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '修改失败';
                }
            }
            return json_encode($result);
        }
        
        //修改页面显示数据
        /**
         * id   分组id
         */
        public function SeeGroup($id)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $usergroup = UserGroup::model()->find('id=:id and flag=:flag',array(':id'=>$id,':flag'=>FLAG_NO));
                if($usergroup)
                {
                    $data = array();
                    $data['group_name'] = $usergroup->name;
                    $result['status']   = ERROR_NONE;
                    $result['data']     = $data;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }
        
        //删除分组
        /**
         * id  分组id
         */
        public function Del($id)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $usergroup = UserGroup::model()->find('id=:id and flag=:flag',array(':id'=>$id,':flag'=>FLAG_NO));
                if($usergroup)
                {
                    if($usergroup->delete())
                    {
                        $result['status'] = ERROR_NONE;
                    } else {
                        $result['status'] = ERROR_REQUEST_FAIL;
                        $result['errMsg'] = '删除失败';
                    }
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }
        
        //下拉框修改会员状态
        /**
         * $grade  等级id
         * id       会员id
         */
        public function Grade($grade,$id)
        {
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if(isset($grade) && empty($grade))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数grade缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $user = User::model()->find('id= :id',array(':id'=>$id));
                if($user)
                {
                    $user->membershipgrade_id = $grade;
                    if($user->update())
                    {
                        $result['status'] = ERROR_NONE;
                    } else {
                        $result['status'] = ERROR_REQUEST_FAIL;
                        $result['errMsg'] = '修改失败';
                    }
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }        
        
        /**
         * 分组会员列表
         * @param unknown $merchant_id
         * @param unknown $from
         * @param unknown $group_id
         * @param unknown $keyword
         * @throws Exception
         * @return string
         */
        public function getGroupUserList($merchant_id, $from, $group_id, $keyword) {
        	$result = array();
        	try {
        		//参数验证
        		//TODO
        		$criteria = new CDbCriteria();
        		//商户id
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		//根据来源
        		$client = explode(",", $from);
        		foreach ($client as $k => $v) {
        			if (empty($v) || $v == ',') {
        				continue;
        			}
        			$criteria->addCondition('t.from LIKE :from'.$v);
        			$criteria->params[':from'.$v] = '%'.','.$v.','.'%';
        		}
        		
        		//分组参数处理
        		$type = '';
        		$arr = explode("-", $group_id);
        		if (reset($arr) == 'grade') {
        			$type = USER_GROUP_GRADE;
        		}
        		$group_id = end($arr);
        		//根据分组
        		if ($type == USER_GROUP_GRADE) { //会员等级分组
        			$criteria->addCondition('membershipgrade_id = :membershipgrade_id');
        			$criteria->params[':membershipgrade_id'] = $group_id;
        		}elseif ($group_id == USER_GROUP_ALL) {
        			//全部分组
        		}elseif ($group_id == USER_GROUP_DEFAULT) { //未分组
        			$criteria->addCondition('group_id IS NULL OR group_id = :group_id');
        			$criteria->params[':group_id'] = '';
        		}else { //自定义分组
        			$criteria->addCondition('group_id LIKE :group_id');
        			$criteria->params[':group_id'] = '%'.','.$group_id.','.'%';
        		}
        		//根据关键字
        		if (!empty($keyword)) {
        			$criteria->addCondition("account = :account");
        			$criteria->params[':account'] = $keyword;
        		}
        		//分页
        		$pages = new CPagination(User::model()->count($criteria));
        		$pages->pageSize = Yii::app() -> params['perPage'];
        		$pages->applyLimit($criteria);
        		$this->page = $pages;
        		
        		$model = User::model()->findAll($criteria);
        	
        		//数据封装
        		$data = array('list' => array());
        		foreach ($model as $key => $value) {
        			$data['list'][$key]['id'] = $value['id']; //会员id
        			$data['list'][$key]['account'] = $value['account']; //会员账号
        			$data['list'][$key]['name'] = $value['name']; //会员名称
        			$data['list'][$key]['group_id'] = $value['group_id']; //会员所属分组id
        			$data['list'][$key]['grade_id'] = $value['membershipgrade_id']; //会员等级id
        			//查询会员等级名称
        			$grade = UserGrade::model()->findByPk($value['membershipgrade_id']);
        			if (empty($grade)) {
        				$data['list'][$key]['grade_name'] = '无'; //会员等级名称
        			}else {
        				$data['list'][$key]['grade_name'] = $grade['name']; //会员等级名称
        			}
        			$data['list'][$key]['avatar'] = $value['avatar']; //会员头像
        			$data['list'][$key]['from'] = $value['from']; //会员来源
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
         * 获取当前分组名
         * @param unknown $merchant_id
         * @param unknown $group_id
         * @throws Exception
         * @return string
         */
        public function getGroupName($merchant_id, $group_id) {
        	$result = array();
        	try {
        		//参数验证
        		//TODO
        		$name = '';
        		//分组参数处理
        		$type = '';
        		$arr = explode("-", $group_id);
        		if (reset($arr) == 'grade') {
        			$type = USER_GROUP_GRADE;
        		}
        		$group_id = end($arr);
        		if ($type == USER_GROUP_GRADE) {
        			//查询会员等级信息
        			$grade = UserGrade::model()->find('merchant_id = :merchant_id and id = :id and flag = :flag',
        					array(':merchant_id' => $merchant_id, ':id' => $group_id, ':flag' => FLAG_NO));
        			if (empty($grade)) {
        				$result['status'] = ERROR_NO_DATA;
        				throw new Exception('未找到相关分组信息');
        			}
        			$name = $grade['name'];
        		}elseif ($group_id == USER_GROUP_ALL) {
        			$name = '全部会员';
        		}elseif ($group_id == USER_GROUP_DEFAULT) {
        			$name = '未分组';
        		}else {
        			$group = UserGroup::model()->find('merchant_id = :merchant_id and id = :id and flag = :flag',
        					array(':merchant_id' => $merchant_id, ':id' => $group_id, ':flag' => FLAG_NO));
        			if (empty($group)) {
        				$result['status'] = ERROR_NO_DATA;
        				throw new Exception('未找到相关分组信息');
        			}
        			$name = $group['name'];
        		}
        		//数据封装
        		$data = array('name' => $name);
        		
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
         * 获取分组及分组人数列表
         * @param unknown $merchant_id
         * @return string
         */
        public function getGroupList($merchant_id) {
        	$result = array();
        	try {
        		//参数验证
        		//TODO
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		
        		$data = array('list' => array());
        		//全部会员数量
        		$count_all = User::model()->count('merchant_id = :merchant_id',
        				array(':merchant_id' => $merchant_id));
        		$all = array('id' => 'all-'.USER_GROUP_ALL, 'name' => '全部会员', 'num' => $count_all);
        		array_push($data['list'], $all);
        		
        		//未分组会员数量
        		$count_default = User::model()->count('merchant_id = :merchant_id and (group_id is null or length(group_id) = 0)',
        				array(':merchant_id' => $merchant_id));
        		$default = array('id' => 'default-'.USER_GROUP_DEFAULT, 'name' => '未分组', 'num' => $count_default);
        		array_push($data['list'], $default);
        		
        		//会员等级
        		$grade = UserGrade::model()->findAll(array(
        				'condition' => 'merchant_id = :merchant_id and flag = :flag',
        				'order' => 'points_rule asc',
        				'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO)
        				));
        		foreach ($grade as $k => $v) {
        			 $grade_count = User::model()->count('merchant_id = :merchant_id and membershipgrade_id = :membershipgrade_id',
        			 		array(':merchant_id' => $merchant_id, ':membershipgrade_id' => $v['id']));
        			 $tmp = array('id' => 'grade-'.$v['id'], 'name' => $v['name'], 'num' => $grade_count);
        			 array_push($data['list'], $tmp);
        		}
        		
        		//自定义分组
        		$group = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
        				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        		foreach ($group as $k => $v) {
        			$group_count = User::model()->count('merchant_id = :merchant_id and group_id like :group_id',
        					array(':merchant_id' => $merchant_id, ':group_id' => '%'.$v['id'].','.'%'));
        			$tmp = array('id' => 'custom-'.$v['id'], 'name' => $v['name'], 'num' => $group_count);
        			array_push($data['list'], $tmp);
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
         * 创建自定义分组
         * @param unknown $merchant_id
         * @param unknown $group_name
         * @return string
         */
        public function createCustomGroup($merchant_id, $group_name) {
        	$result = array();
        	try {
        		//参数验证
        		if (empty($merchant_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数merchant_id不能为空');
        		}
        		if (empty($group_name)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数group_name不能为空');
        		}
        		//检查命名的合法性
        		//字符长度检查
        		if (mb_strlen($group_name,'gb2312') > 16) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('分组名过长');
        		}
        		//是否与系统分组冲突
        		if ($group_name == '全部会员' || $group_name == '未分组') {
        			$result['status'] = ERROR_DUPLICATE_DATA;
        			throw new Exception('非法的分组名');
        		}
        		//查询同名的会员等级
        		$grade = UserGrade::model()->find('merchant_id = :merchant_id and flag = :flag and name = :name',
        				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':name' => $group_name));
        		if (!empty($grade)) {
        			$result['status'] = ERROR_DUPLICATE_DATA;
        			throw new Exception('不能与已有会员等级名称重复');
        		}
        		//查询同名的自定义分组
        		$group = UserGroup::model()->find('merchant_id = :merchant_id and flag = :flag and name = :name',
        				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':name' => $group_name));
        		if (!empty($group)) {
        			$result['status'] = ERROR_DUPLICATE_DATA;
        			throw new Exception('不能与已有自定义分组名称重复');
        		}
        		
        		//创建分组
        		$model = new UserGroup();
        		$model['merchant_id'] = $merchant_id;
        		$model['name'] = $group_name;
        		$model['create_time'] = date('Y-m-d H:i:s');
        		if (!$model->save()) {
        			$result['status'] = ERROR_SAVE_FAIL;
        			throw new Exception('数据保存失败');
        		}
        		$data = array('group_id' => $model['id']);
        	
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
         * 修改分组名
         * @param unknown $merchant_id
         * @param unknown $group_id
         * @param unknown $name
         * @throws Exception
         * @return string
         */
        public function modifyCustomGroup($merchant_id, $group_id, $group_name) {
        	$result = array();
        	try {
        		//参数验证
        		if (empty($merchant_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数merchant_id不能为空');
        		}
        		if (empty($group_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数group_id不能为空');
        		}
        		if (empty($group_name)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数group_name不能为空');
        		}
        		//检查命名的合法性
        		//字符长度检查
        		if (mb_strlen($group_name,'gb2312') > 16) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('分组名过长');
        		}
        		//是否与系统分组冲突
        		if ($group_name == '全部会员' || $group_name == '未分组') {
        			$result['status'] = ERROR_DUPLICATE_DATA;
        			throw new Exception('非法的分组名');
        		}
        		//查询同名的会员等级
        		$grade = UserGrade::model()->find('merchant_id = :merchant_id and flag = :flag and name = :name',
        				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':name' => $group_name));
        		if (!empty($grade)) {
        			$result['status'] = ERROR_DUPLICATE_DATA;
        			throw new Exception('不能与已有会员等级名称重复');
        		}
        		//查询同名的自定义分组
        		$group = UserGroup::model()->find('merchant_id = :merchant_id and flag = :flag and name = :name and id != :group_id',
        				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':name' => $group_name, ':group_id' => $group_id));
        		if (!empty($group)) {
        			$result['status'] = ERROR_DUPLICATE_DATA;
        			throw new Exception('不能与已有自定义分组名称重复');
        		}
        	
        		//修改分组
        		$model = UserGroup::model()->find('id = :group_id and flag = :flag',
        				array(':group_id' => $group_id, ':flag' => FLAG_NO));
        		if (empty($model)) {
        			$result['status'] = ERROR_NO_DATA;
        			throw new Exception('修改的分组不存在');
        		}
        		$model['name'] = $group_name;
        		if (!$model->save()) {
        			$result['status'] = ERROR_SAVE_FAIL;
        			throw new Exception('数据保存失败');
        		}
        		$data = array('group_id' => $model['id']);
        		 
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
         * 删除自定义分组
         * @param unknown $merchant_id
         * @param unknown $group_id
         * @throws Exception
         * @return string
         */
        public function deleteCustomGroup($merchant_id, $group_id) {
        	$result = array();
        	$transaction = Yii::app()->db->beginTransaction(); //开启事务
        	try {
        		//参数验证
        		if (empty($merchant_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数merchant_id不能为空');
        		}
        		if (empty($group_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数group_id不能为空');
        		}
        		//查询分组信息
        		$group = UserGroup::model()->find('merchant_id = :merchant_id and id = :id',
        				array(':merchant_id' => $merchant_id, ':id' => $group_id));
        		if (empty($group)) {
        			$result['status'] = ERROR_NO_DATA;
        			throw new Exception('未找到相关分组信息');
        		}
        		//修改flag
        		$group['flag'] = FLAG_YES;
        		if (!$group->save()) {
        			$result['status'] = ERROR_SAVE_FAIL;
        			throw new Exception('分组删除失败');
        		}
        		
        		//修改该分组下的会员信息
        		$user = User::model()->findAll("merchant_id = :merchant_id and group_id like :group_id",
        				array(':merchant_id' => $merchant_id, ':group_id' => '%'.','.$group_id.','.'%'));
        		foreach ($user as $k => $v) {
        			$tmp = $v['group_id'];
        			$tmp = str_replace(",".$group_id.",", "", $tmp);
        			$v['group_id'] = $tmp;
        			if (!$v->save()) {
        				$result['status'] = ERROR_SAVE_FAIL;
        				throw new Exception('数据修改失败');
        			}
        		}
        		
        		$transaction->commit(); //数据提交
        		//查询未分组会员数量
        		$count_default = User::model()->count('merchant_id = :merchant_id and (group_id is null or length(group_id) = 0)',
        				array(':merchant_id' => $merchant_id));
        		
        		$result['data'] = array('count' => $count_default);
        		$result['status'] = ERROR_NONE; //状态码
        		$result['errMsg'] = ''; //错误信息
        	} catch (Exception $e) {
        		$transaction->rollback(); //数据回滚
        		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
        		$result['errMsg'] = $e->getMessage(); //错误信息
        	}
        	
        	return json_encode($result);
        }
        
        /**
         * 获取可添加分组和可移动分组
         * @param unknown $merchant_id
         * @param unknown $from_group_type
         * @param unknown $from_group_id
         * @throws Exception
         * @return string
         */
        public function getOperationGroupList($merchant_id, $group_id) {
        	$result = array();
        	try {
        		//参数验证
        		//TODO
        		//分组参数处理
        		$arr = explode("-", $group_id);
        		$type = 'all';
        		$group_id = end($arr);
        		//添加到列表
        		$add = array();
        		//移动到列表
        		$move = array();
        		if ($type == 'all') { //源分组为全部会员，添加列表为自定义分组，移动列表为空
        			$custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        			foreach ($custom as $k => $v) {
        				$add['custom-'.$v['id']] = $v['name'];
        			}
        		}elseif ($type == 'default') { //源分组为未分组，添加列表为空，移动列表为自定义分组
        			$custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        			foreach ($custom as $k => $v) {
        				$move['custom-'.$v['id']] = $v['name'];
        			}
        		}elseif ($type == 'grade') { //源分组为会员等级分组，添加列表为自定义分组，移动列表为会员等级分组
        			$custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        			foreach ($custom as $k => $v) {
        				$add['custom-'.$v['id']] = $v['name'];
        			}
        			$grade = UserGrade::model()->findAll('merchant_id = :merchant_id and flag = :flag',
        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));        			
                                foreach ($grade as $k => $v) {
        				$move['grade-'.$v['id']] = $v['name'];
        			}
        		}elseif ($type == 'custom') { //源分组为自定义分组列表，添加列表为自定义分组，移动列表为自定义分组和未分组
        			$custom = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag ',
        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
        			foreach ($custom as $k => $v) {
        				$add['custom-'.$v['id']] = $v['name'];
        				$move['custom-'.$v['id']] = $v['name'];
        			}
        			$k = count($move);        			
        		}else { //不存在的分组类型
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('错误的分组类型');
        		}
        		$data = array('add' => $add, 'move' => $move);
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
         * 获取会员等级
         */
        public function getOperationGradeList($merchant_id)
        {
            $result = array();
            try {
                $grade = UserGrade::model()->findAll('merchant_id = :merchant_id and flag = :flag',
                                array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));                 
                foreach ($grade as $k => $v) {
                        $move['grade-'.$v['id']] = $v['name'];
                }
                $data = array('move' => $move);
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
         * 对用户重新分组
         * @param unknown $merchant_id
         * @param unknown $operation
         * @param unknown $user
         * @param unknown $old_group
         * @param unknown $new_group
         * @throws Exception
         * @return string
         */
        public function regroupUser($merchant_id, $operation, $user, $old_group, $new_group) {
//             var_dump($user);
        	$result = array();
        	$transaction = Yii::app()->db->beginTransaction(); //开启事务
        	try {
        		//参数验证
        		if (empty($merchant_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数merchant_id不能为空');
        		}
        		if (empty($operation)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数operation不能为空');
        		}
        		if (empty($user)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数user不能为空');
        		}
        		if (empty($old_group)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数old_group不能为空');
        		}
        		if (empty($new_group)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('参数new_group不能为空');
        		}
        		//参数处理
        		$list = explode(",", $user);
        		
        		$arr = explode("-", $old_group);
        		$old_type = reset($arr); //源分组类型
        		$old_id = end($arr); //源分组id
        		
        		$arr = explode("-", $new_group);
        		$new_type = reset($arr); //目标分组类型
        		$new_id = end($arr); //目标分组id
        		
        		//分组id非空检查
        		if (empty($old_id) || empty($new_id)) {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('错误的分组参数');
        		}
        		
        		$modify = false;
        		$add = false;
        		$delete = false;

        		//目标分组类型判断
        		if ($new_type == 'grade') {
                            foreach ($list as $k => $v) {
        				//查询会员信息
        				$model = User::model()->find('merchant_id = :merchant_id and id = :id',
        						array(':merchant_id' => $merchant_id, ':id' => $v));
//        				if (empty($model)) {
//        					$result['status'] = ERROR_NO_DATA;
//        					throw new Exception('会员('.$v.')不存在，无法进行移动操作');
//        				}
        				//比较两个等级的高低（积分要求的高低）
//        				$old_grade = UserGrade::model()->find('merchant_id = :merchant_id and flag = :flag and id = :id',
//        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':id' => $old_id));
//        				if (empty($old_grade)) {
//        					$result['status'] = ERROR_NO_DATA;
//        					throw new Exception('会员等级('.$old_id.')不存在，无法进行移动操作');
//        				}
        				//从高会员等级移到低会员等级且会员受积分限制时，禁止移动
//        				if ($grade['points_rule'] < $old_grade['points_rule'] && $model['switch'] == POINTS_LIMIT) {
//        					$result['status'] = ERROR_EXCEPTION;
//        					throw new Exception('会员('.$v.')无法向低的会员等级移动');
//        				}
        				//从高会员等级移到低会员等级且会员不受积分限制时，根据会员当前积分设置相应的会员等级
//        				if ($grade['points_rule'] < $old_grade['points_rule'] && $model['switch'] == POINTS_LIMIT_NO) {
        					$new_grade = UserGrade::model()->find(array(
        							'condition' => 'merchant_id = :merchant_id and flag = :flag and points_rule <= :points',
        							'order' => 'points_rule desc',
        							'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':points' => $model['points'])
        					));
//        					if (empty($new_grade)) {
//        						$result['status'] = ERROR_NO_DATA;
//        						throw new Exception('系统内部错误');
//        					}
        					//修改会员等级，修改移动标识
//        					$model['membershipgrade_id'] = $new_grade['id'];
//        					$model['switch'] = POINTS_LIMIT;
//        				}

        				//从低会员等级移到高会员等级
//        				if ($grade['points_rule'] > $old_grade['points_rule']) {
//        					//修改会员等级，修改移动标识
        					$model['membershipgrade_id'] = $new_id;
        					$model['switch'] = POINTS_LIMIT_NO;
//        				}
        				
        				if (!$model->save()) {
        					$result['status'] = ERROR_SAVE_FAIL;
        					throw new Exception('会员等级修改失败');
        				}
        			}
//        			if ($old_type != 'grade') {
//        				$result['status'] = ERROR_PARAMETER_FORMAT;
//        				throw new Exception('无法将原分组会员添加或移动到该分组下');
//        			}
//        			if ($operation != 'move') {
//        				$result['status'] = ERROR_PARAMETER_FORMAT;
//        				throw new Exception('无法进行添加操作');
//        			}
//        			//修改标识
//        			$modify = true;
        		}elseif ($new_type == 'default') {
//        			if ($old_type != 'custom') {
//        				$result['status'] = ERROR_PARAMETER_FORMAT;
//        				throw new Exception('无法将原分组会员添加或移动到该分组下');
//        			}
//        			if ($operation != 'move') {
//        				$result['status'] = ERROR_PARAMETER_FORMAT;
//        				throw new Exception('无法进行添加操作');
//        			}
//        			//删除原分组标识
//        			$delete = true;
        		}else
                        if ($new_type == 'custom') {
        			if ($operation == 'add') {
        				if ($old_type != 'all' && $old_type != 'grade' && $old_type != 'custom') {
        					$result['status'] = ERROR_PARAMETER_FORMAT;
        					throw new Exception('无法进行添加操作');
        				}
        				//添加标识
        				$add = true;
        			}elseif ($operation == 'move') {
        				if ($old_type != 'custom' && $old_type != 'default') {
        					$result['status'] = ERROR_PARAMETER_FORMAT;
        					throw new Exception('无法进行移动操作');
        				}
        				if ($old_type == 'custom') {
        					//删除标识
        					$delete = true;
        				}
        				//添加标识
        				$add = true;
        			}else {
        				$result['status'] = ERROR_PARAMETER_FORMAT;
        				throw new Exception('无效的操作');
        			}
        		}else {
        			$result['status'] = ERROR_PARAMETER_FORMAT;
        			throw new Exception('无效的操作分组');
        		}
        		
        		if ($modify) {
        			//查询会员等级信息
        			$grade = UserGrade::model()->find('merchant_id = :merchant_id and flag = :flag and id = :id',
        					array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO, ':id' => $new_id));
        			if (empty($grade)) {
        				$result['status'] = ERROR_NO_DATA;
        				throw new Exception('会员等级('.$new_id.')不存在，无法进行移动操作');
        			}
        			
        		}
        		if ($delete) {
        			//删除会员所属原分组
//        			foreach ($list as $k => $v) {
//        				//查询会员信息
//        				$model = User::model()->find('merchant_id = :merchant_id and id = :id',
//        						array(':merchant_id' => $merchant_id, ':id' => $v));
//        				if (empty($model)) {
//        					$result['status'] = ERROR_NO_DATA;
//        					throw new Exception('会员('.$v.')不存在，无法进行移动操作');
//        				}
//        				$tmp = $model['group_id'];
//        				$tmp = str_replace(",".$old_id.",", "", $tmp);
//        				$model['group_id'] = $tmp;
//        				if (!$model->save()) {
//        					$result['status'] = ERROR_SAVE_FAIL;
//        					throw new Exception('数据修改失败(error:delete)');
//        				}
//        			}
        		}
        		if ($add) {
        			//添加会员新的分组
        			foreach ($list as $k => $v) {
        				//查询会员信息
        				$model = User::model()->find('merchant_id = :merchant_id and id = :id',
        						array(':merchant_id' => $merchant_id, ':id' => $v));
        				if (empty($model)) {
        					$result['status'] = ERROR_NO_DATA;
        					throw new Exception('会员('.$v.')不存在，无法进行添加操作');
        				}
//        				$tmp = $model['group_id'];
//        				$tmp .= ','.$new_id.',';
                                        $group = Group::model()->find('flag=:flag and group_id=:group_id and user_id=:user_id',array(
                                            ':flag'=>FLAG_NO,
                                            ':group_id' => $new_id,
                                            ':user_id' => $model -> id
                                        ));
                                        if(empty($group)){
                                            $group = new Group();
                                            $group -> user_id = $model -> id;
                                            $group -> create_time = new CDbExpression('NOW()');
                                            
                                        } 
                                            $group -> group_id = $new_id;
                                            $group -> wechat_id = $model -> wechat_id;
                                            $group -> alipay_fuwu_id = $model -> alipay_fuwu_id;
                                        
//        				$model['group_id'] = $tmp;
        				if (!$group->save()) {
        					$result['status'] = ERROR_SAVE_FAIL;
        					throw new Exception('数据修改失败(error:add)');
        				}
        			}
        		}
        	
        		$transaction->commit(); //数据提交
        	
        		$result['data'] = '';
        		$result['status'] = ERROR_NONE; //状态码
        		$result['errMsg'] = ''; //错误信息
        	} catch (Exception $e) {
        		$transaction->rollback(); //数据回滚
        		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
        		$result['errMsg'] = $e->getMessage(); //错误信息
        	}
        	 
        	return json_encode($result);
        }

    /**
     * 校验积分要求是否重复
     * id 编辑的ID
     * point_rule 积分要求
     */
    public function checkUserPointRule($merchant_id,$point_rule,$id='')
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :id');
            $criteria->params[':id'] = $merchant_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model=UserGrade::model()->findAll($criteria);
            if(isset($model))
            {
                $flag=true;
                foreach($model as $key=>$value)
                {
                    if(isset($id))
                    {
                        //编辑会员等级
                        if($value['points_rule']==$point_rule&&$value['id']!=$id)
                        {
                            //有积分要求相同
                            $flag=false;
                            $result ['status'] = ERROR_DUPLICATE_DATA;
                            $result['errMsg'] = ''; //错误信息
                        }
                    }
                    else
                    {
                        //添加会员等级
                        if($value['points_rule']==$point_rule)
                        {
                            //有积分要求相同
                            $flag=false;
                            $result ['status'] = ERROR_DUPLICATE_DATA;
                            $result['errMsg'] = ''; //错误信息
                        }
                    }

                }
                if($flag)
                {
                    //没有相同数据
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }
            }
            else
            {
                $result ['status'] = ERROR_EXCEPTION;
                $result['errMsg'] = ''; //错误信息
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    //微信粉丝关注事件推送处理
    /*$merchant_id 商户id
     * $fromUsername 发送方账号 openid
     * */
    public function saveWechatFansInfo($merchant_id,$fromUsername){
    	$result = array();
    	try {
    		//根据商户id和wechat openid 查找用户
    		$user = User::model() -> find('wechat_id =:wechat_id and merchant_id =:merchant_id',array(
    				':wechat_id' => $fromUsername,
    				':merchant_id' => $merchant_id
    		));
    		//找到该商户
    		$merchant = Merchant::model() -> findByPk($merchant_id);

    		//获取access_token
    		$access_token = Wechat::getTokenByMerchant($merchant);
    		//获取微信用户信息
    		$wechat = new WechatC();
    		$user_info_api = $wechat->getUserInfos($access_token, $fromUsername);
    		
    		$user_info_arr = json_decode($user_info_api, true);
    		//判断该用户是否存在
    		if(!empty($user)){
    			//该用户存在
    			$user['wechat_status'] = WECHAT_USER_SUBSCRIBE;
    			$user['wechat_nickname'] = $user_info_arr['nickname'];
    			$user['wechat_sex'] = $user_info_arr['sex'];
    			$user['wechat_country'] = $user_info_arr['country'];
    			$user['wechat_province'] = $user_info_arr['province'];
    			$user['wechat_city'] = $user_info_arr['city'];
    			$user['wechat_language'] = $user_info_arr['language'];
    			$user['wechat_headimgurl'] = $user_info_arr['headimgurl'];
    			$user['wechat_remark'] = $user_info_arr['remark'];
    			$user['wechat_groupid'] = $user_info_arr['groupid'];
    			$user['wechat_subscribe_time'] = date('Y-m-d H:i:s',$user_info_arr['subscribe_time']);
    			//玩券头像为空赋值	
				if(empty($user['avatar'])){
					$user['avatar'] = $user_info_arr['headimgurl'];
				}
				//玩券昵称为空赋值
				if(empty($user['nickname'])){
					$user['nickname'] = $user_info_arr['nickname'];
				}
				//玩券性别为空
				if(empty($user['sex'])){
					$user['sex'] = $user_info_arr['sex'];
				}
				//玩券国家为空
				if(empty($user['country'])){
					$user['country'] = $user_info_arr['country'];
				}
    			
    			
    				
    			if($user->update()){
    				
    			}else{
    				throw new Exception('修改失败');
    			}
    		}else{
    			//该用户不存在，则新建一个用户
    			$new_user = new User();
    			$new_user -> type = USER_TYPE_WECHAT_FANS;
    			$new_user -> merchant_id = $merchant_id;
    			$new_user -> wechat_status = WECHAT_USER_SUBSCRIBE;
    			$new_user -> wechat_id = $fromUsername;
    			$new_user -> wechat_nickname = $user_info_arr['nickname'];
    			$new_user -> wechat_sex = $user_info_arr['sex'];
    			$new_user -> wechat_country = $user_info_arr['country'];
    			$new_user -> wechat_province = $user_info_arr['province'];
    			$new_user -> wechat_city = $user_info_arr['city'];
    			$new_user -> wechat_language = $user_info_arr['language'];
    			$new_user -> wechat_headimgurl = $user_info_arr['headimgurl'];
    			$new_user -> wechat_remark = $user_info_arr['remark'];
    			$new_user -> wechat_groupid = $user_info_arr['groupid'];
    			$new_user -> wechat_subscribe_time = date('Y-m-d H:i:s',$user_info_arr['subscribe_time']);
    			$new_user -> avatar = $user_info_arr['headimgurl'];
    			$new_user -> nickname = $user_info_arr['nickname'];
    			$new_user -> sex = $user_info_arr['sex'];
    			$new_user -> country = $user_info_arr['country'];
    			$new_user -> province = $user_info_arr['province'];
    			$new_user -> create_time = new CDbExpression('now()');
    			if($new_user -> save()){

    			}else{
    				throw new Exception('创建失败');
    			}
    		}
    		$result['data'] = $user_info_arr['nickname'];
			$result['status'] = ERROR_NONE;
			
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    //微信粉丝取消关注事件推送处理
    /*
     * 
     * */
    public function cancelWechatSubscribe($merchant_id,$fromUsername){
    	try {
    		$user = User::model() -> find('merchant_id =:merchant_id and wechat_id =:wechat_id',array(
    				':merchant_id' => $merchant_id,
    				':wechat_id' => $fromUsername
    		));
    		//保存为取消关注和取消关注时间
    		$user -> wechat_status = WECHAT_USER_CANCELSUBSCRIBE;
    		$user -> wechat_cancel_subscribe_time = date ( 'Y-m-d H:i:s' );
    		$user -> update();
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 用户管理列表
     * @param type $merchant_id
     * @param type $from
     * @param type $group_id
     * @param type $keyword
     * @param type $sex
     * @param type $grade
     * @param type $time
     * @param type $time1
     * @param type $time2
     * @param type $fans
     * @param type $liveplaceProvince
     * @param type $liveplaceCity
     * @param type $sort
     * @param type $Focus
     * @param type $integral
     * @param type $consumption
     * @param type $Ability
     * @param type $age_min
     * @param type $age_max
     * @param type $unknown
     * @return type
     */                  
   public function getGroupManageList($merchant_id, $from, $group_id, $keyword, $sex, $grade, $time, $time1, $time2, $fans, $liveplaceProvince, $liveplaceCity, $sort, $Focus, $integral, $consumption, $Ability, $birth_min, $birth_max, $unknown='', $wechat_status=array(), $alipay_status=array()) {
           $result = array();
           try {
                   //参数验证
                   //TODO
                   $criteria = new CDbCriteria();
                   //由于粉丝和会员等级查询条件比较特殊，所以提到最前面。
                   //会员等级
                   if(!empty($grade)){
                       $criteria->addInCondition('membershipgrade_id', $grade);
                   }
                   //粉丝                   
                   if(!empty($fans) && !empty($grade)){
                       $criteria -> addcondition('type=:type1 or type=:type2','OR');
                       $criteria -> params[':type1'] = USER_TYPE_WECHAT_FANS;
                       $criteria -> params[':type2'] = USER_TYPE_ALIPAY_FANS;
                   }else if(!empty($fans)){
                       $criteria -> addcondition('type=:type1 or type=:type2');
                       $criteria -> params[':type1'] = USER_TYPE_WECHAT_FANS;
                       $criteria -> params[':type2'] = USER_TYPE_ALIPAY_FANS;                   		
                   }   
                   //type 1会员 2微信粉丝 3支付宝粉丝
                   if(!empty($type) && !empty($type)){
                       $criteria -> addcondition('type=:type');
                       $criteria -> params[':type'] = $type;
                   }
                   //微信支付宝 已关注，已取消，未关注检索
                   if(!empty($wechat_status) && !empty($wechat_status)){
                       $criteria -> addInCondition('wechat_status', $wechat_status);
                   }
                   if (!empty($alipay_status) && !empty($alipay_status)){
                       $criteria -> addInCondition('alipay_status', $alipay_status);
                   }
                  
                   //商户id
                   $criteria->addCondition('merchant_id = :merchant_id');
                   $criteria->params[':merchant_id'] = $merchant_id;                   
                   //年龄段
                   if(!empty($birth_min) && !empty($birth_max)){
                       $criteria->addBetweenCondition('year(birthday)', $birth_min, $birth_max);
//                        $criteria->addCondition('year(birthday)>='.$birth_min.' and year(birthday)<='.$birth_max);                       
                   }
                   //消费能力
                   if(!empty($Ability) && $Ability == 'desc'){
                       $criteria -> order = '';
                   }                   
                   //积分
                   if(!empty($integral) && $integral == 'desc'){
                       $criteria -> order = 'points desc';
                   }
                   if(!empty($integral) && $integral == 'asc'){
                       $criteria -> order = 'points asc';
                   }
                   //关注时间
                   if(!empty($Focus) && $Focus == 'desc'){
                       $criteria -> order = 'alipay_subscribe_time desc , wechat_subscribe_time desc';
                   }
                   if(!empty($Focus) && $Focus == 'asc'){
                       $criteria -> order = 'alipay_subscribe_time asc , wechat_subscribe_time asc';
                   }
                   //年龄
                   if(!empty($sort) && $sort == 'desc'){
                       $criteria -> order = 'birthday asc';
                   }
                   if(!empty($sort) && $sort == 'asc'){
                       $criteria -> order = 'birthday desc';
                   }
                   //省
                   if(!empty($liveplaceProvince) && empty($liveplaceCity)) {
                       $sql = "(province_code like '$liveplaceProvince')";
                       $criteria ->addCondition($sql);
                   }
                   //省市
                   if(!empty($liveplaceProvince) && !empty($liveplaceCity)) {
                       $sql = "(province_code like '$liveplaceProvince' and city_code like '$liveplaceCity')";
                       $criteria ->addCondition($sql);
                   }
                   //根据来源   
                   if(!empty($from)){
                        foreach ($from as $k => $v) {
                                if (empty($v) || $v == ',') {
                                        continue;
                                }
                                $criteria->addCondition('t.from LIKE :from'.$v);
                                $criteria->params[':from'.$v] = '%'.','.$v.','.'%';
                        }
                   }
                   //普通分组
                   if(!empty($group_id)){
                       $criteria2 = new CDbCriteria();                       
                       $criteria2->addInCondition('group_id', $group_id);
                       $criteria2->addcondition('flag=:flag');
                       $criteria2->params[':flag'] = FLAG_NO;
                       $group = Group::model()->findall($criteria2);
                       $id = array();
                       foreach($group as $v){
                           $id[] = $v['user_id'];
                       }                       
                       $criteria ->addInCondition('id', $id);
                   }

                   //根据关键字
                   if (!empty($keyword)) {                           
                           $criteria->addCondition("nickname like '%$keyword%' or account like '%$keyword%' or name like '%$keyword%' or wechat_nickname like '%$keyword%' or alipay_nickname like '%$keyword%'");
                   }
                   //根据性别
                   if(!empty($sex) && !empty($unknown) && $unknown == 'unknown') { 
                       $criteria ->addInCondition('sex', $sex,'and'); 
                       $criteria ->addCondition('sex is null','or'); 
                   } else {
                        if(!empty($sex)) {   
                            $criteria ->addInCondition('sex', $sex);                       
                        }
                        if(!empty($unknown) && $unknown == 'unknown') {                       
                            $criteria ->addCondition('sex is null');                        
                        }
                   }

                   //服务窗关注时间
                   if(!empty($time)){
                            $arr_time = explode('-', $time);
                            $start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
                            $end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
                            $criteria ->addBetweenCondition('alipay_subscribe_time',$start_time,$end_time);                            
                    }
                    //公众号关注时间
                    if(!empty($time1)){
                            $arr_time = explode('-', $time1);
                            $start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
                            $end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
                            $criteria -> addBetweenCondition('wechat_subscribe_time',$start_time,$end_time);
                    }
                    //注册会员时间
                    if(!empty($time2)){
                            $arr_time = explode('-', $time2);
                            $start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
                            $end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
                            $criteria -> addBetweenCondition('regist_time',$start_time,$end_time);
                    }
                   if (empty($criteria -> order)){
                       $criteria -> order = 'last_time DESC';
                   }
                   $count = User::model()->count($criteria);                   
                   //分页
                   $pages = new CPagination(User::model()->count($criteria));
                   $pages->pageSize = Yii::app() -> params['perPage'];
                   $pages->applyLimit($criteria);
                   $this->page = $pages;

                   $model = User::model()->findAll($criteria);
          
                   //数据封装
                   $data = array('list' => array());
                   foreach ($model as $key => $value) {
                           $data['list'][$key]['id']       = $value['id']; //会员id
                           $data['list'][$key]['type']     = $value['type'];//用户类型
                           $data['list'][$key]['account']  = $value['account']; //会员账号
                           $data['list'][$key]['avatar']   = $value['avatar']; //会员头像
                           $data['list'][$key]['name']     = $value['name']; //会员名称
                           $data['list'][$key]['nickname'] = $value['nickname'];//昵称
                           $data['list'][$key]['sex']      = $value['sex'];//性别
                           $data['list'][$key]['birthday'] = !empty($value['birthday']) ? date('Y')- date('Y',strtotime($value['birthday'])) + 1 : '';//年龄
                           $data['list'][$key]['points']   = $value['points'];//会员积分
                           $data['list'][$key]['grade_id'] = $value['membershipgrade_id']; //会员等级id
                           $data['list'][$key]['address']  = $value['address'];//地址
                           $data['list'][$key]['alipay_avatar'] = $value['alipay_avatar'];//支付宝服务窗头像
                           $data['list'][$key]['alipay_nickname'] = $value['alipay_nickname'];//支付宝用户昵称
                           $data['list'][$key]['alipay_province'] = $value['alipay_province'];//支付宝用户注册所填省份
                           $data['list'][$key]['alipay_city'] = $value['alipay_city'];//支付宝用户注册所填城市
                           $data['list'][$key]['alipay_gender'] = $value['alipay_gender'];//支付宝用户性别
                           $data['list'][$key]['alipay_user_type_value'] = $value['alipay_user_type_value'];//支付宝用户类型
                           $data['list'][$key]['alipay_is_licence_auth'] = $value['alipay_is_licence_auth'];//支付宝用户是否经过营业执照认证
                           $data['list'][$key]['alipay_is_certified'] = $value['alipay_is_certified'];//支付宝用户是否通过实名认证
                           $data['list'][$key]['alipay_certified_grade_a'] = $value['alipay_certified_grade_a'];//支付宝用户是否A类认证
                           $data['list'][$key]['alipay_is_student_certified'] = $value['alipay_is_student_certified'];//支付宝用户是否是学生
                           $data['list'][$key]['alipay_is_bank_auth'] = $value['alipay_is_bank_auth'];//支付宝用户是否经过银行卡认证
                           $data['list'][$key]['alipay_is_mobile_auth'] = $value['alipay_is_mobile_auth'];//支付宝用户是否经过手机认证
                           $data['list'][$key]['alipay_user_status'] = $value['alipay_user_status'];//支付宝用户状态
                           $data['list'][$key]['alipay_subscribe_time'] = $value['alipay_subscribe_time'];//支付宝用户关注时间
                           $data['list'][$key]['alipay_cancel_subscribe_time'] = $value['alipay_cancel_subscribe_time'];//支付宝用户取消关注时间
                           $data['list'][$key]['alipay_subscribe_store_id'] = $value['alipay_subscribe_store_id'];//支付宝用户关注入口门店
                           $data['list'][$key]['register_address'] = $value['register_address'];//注册地址（省,市）
                           $data['list'][$key]['wechat_status'] = $value['wechat_status'];//微信用户关注状态
                           $data['list'][$key]['wechat_nickname'] = $value['wechat_nickname'];//微信用户昵称
                           $data['list'][$key]['wechat_sex'] = $value['wechat_sex'];//微信用户性别
                           $data['list'][$key]['wechat_country'] = $value['wechat_country'];//微信用户所在国家
                           $data['list'][$key]['wechat_province'] = $value['wechat_province'];//微信用户所在省份
                           $data['list'][$key]['wechat_city'] = $value['wechat_city'];//微信用户所在城市
                           $data['list'][$key]['wechat_headimgurl'] = $value['wechat_headimgurl'];//微信用户头像
                           $data['list'][$key]['wechat_groupid'] = $value['wechat_groupid'];//微信用户所在分组id
                           $data['list'][$key]['wechat_subscribe_time'] = $value['wechat_subscribe_time'];//微信用户关注时间
                           $data['list'][$key]['wechat_cancel_subscribe_time'] = $value['wechat_cancel_subscribe_time'];//微信用户取消关注时间
                           $data['list'][$key]['wechat_subscribe_store_id'] = $value['wechat_subscribe_store_id'];//微信用户关注入口门店
                           $data['list'][$key]['alipay_status'] = $value['alipay_status'];//支付宝用户关注状态
                           $data['list'][$key]['province']               = $value->province;//省份
                           $data['list'][$key]['city']                   = $value->city;//城市                           
                           $store = Store::model()->findall('flag=:flag and merchant_id=:merchant_id',array(
                               ':flag' => FLAG_NO,
                               ':merchant_id' => $merchant_id
                           ));
                           $store_id = array();
                            if($store){
                                foreach($store as $v){
                                    $store_id[] = $v['id'];
                                }
                            }
                           //最近消费
                           $criteria1 = new CDbCriteria();
                           $criteria1 -> order = 'pay_time desc';
                           $criteria1 -> addcondition('flag=:flag and (user_id=:user_id or wechat_user_id=:wechat_user_id or alipay_user_id=:alipay_user_id)');
                           $criteria1 -> params[':flag'] = FLAG_NO;
                           $criteria1 -> params[':user_id'] = $value['id'];
                           $criteria1 -> params[':wechat_user_id'] = $value['wechat_id'];
                           $criteria1 -> params[':alipay_user_id'] = $value['alipay_fuwu_id'];
                           $criteria1 -> addInCondition('store_id', $store_id);
                           $order = Order::model()->find($criteria1);
                           if($order){
                                $data['list'][$key]['pay_time'] = $order['pay_time'];
                                $data['list'][$key]['store_name'] = $order -> store -> name;
                           } else { 
                                $data['list'][$key]['pay_time'] = '';
                                $data['list'][$key]['store_name'] = '';
                           }                           
                           
                           //查询会员等级名称
                           $grade = UserGrade::model()->findByPk($value['membershipgrade_id']);                           
                           if (empty($grade)) {
                                   $data['list'][$key]['grade_name'] = '无'; //会员等级名称
                           }else {
                                   $data['list'][$key]['grade_name'] = $grade['name']; //会员等级名称
                           }
                           
                           $data['list'][$key]['from'] = $value['from']; //会员来源
                           $data['list'][$key]['regist_time'] = $value['regist_time'];//注册时间     
                           $usertag = UserTag::model()->findall('user_id=:user_id',array(':user_id'=>$value['id']));
                            if(!empty($usertag)){
                                foreach($usertag as $g => $t){
                                    $data['list'][$key]['usertag'][$g]['id']        = $t['id'];
                                    $data['list'][$key]['usertag'][$g]['user_id']   = $t['user_id'];
                                    $data['list'][$key]['usertag'][$g]['tag_value'] = $t['tag_value'];
                                }
                            } 
                   }
                   //消费时间
                   if(!empty($consumption) && $consumption == 'desc'){
                       $data['list'] = $this -> arr_sort_desc($data['list'],'pay_time');
                   }
                   if(!empty($consumption) && $consumption == 'asc'){
                       $data['list'] = $this -> arr_sort_asc($data['list'],'pay_time');
                   }
                   $result['count'] = $count;
                   $result['data']  = $data;
                   $result['status'] = ERROR_NONE; //状态码
                   $result['errMsg'] = ''; //错误信息
           } catch (Exception $e) {
                   $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                   $result['errMsg'] = $e->getMessage(); //错误信息
           }

           return json_encode($result);
   }
   
   /**
    * 消费时间排序降序
    * @param type $array
    * @param type $key
    * @param type $order
    * @return type
    */
   function arr_sort_desc($array,$key,$order="desc"){//asc是升序 desc是降序
            $arr_nums=$arr=array();
            foreach($array as $k=>$v){
                $arr_nums[$k]=$v[$key];
            }
            if($order=='asc'){
                asort($arr_nums);
            }else{
                arsort($arr_nums);
            }
            foreach($arr_nums as $k=>$v){
                $arr[$k]=$array[$k];
            }
            return $arr;
    }
    
    /**
     * 消费时间排序升序
     * @param type $array
     * @param type $key
     * @param type $order
     * @return type
     */
    function arr_sort_asc($array,$key,$order="asc"){//asc是升序 desc是降序
            $arr_nums=$arr=array();
            foreach($array as $k=>$v){
                $arr_nums[$k]=$v[$key];
            }
            if($order=='asc'){
                asort($arr_nums);
            }else{
                arsort($arr_nums);
            }
            foreach($arr_nums as $k=>$v){
                $arr[$k]=$array[$k];
            }
            return $arr;
    }
   
   /**
    * 用户分组
    */
   public function getManageList($merchant_id)
   {
       $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $data = array();
            //会员等级
            $grade = UserGrade::model()->findAll(array(
                'condition' => 'merchant_id = :merchant_id and flag = :flag',
                'order' => 'points_rule asc',
                'params' => array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO)
            ));
            if($grade){
                foreach ($grade as $key => $value) {
                    $data['grade'][$key]['id'] = $value['id'];
                    $data['grade'][$key]['name'] = $value['name'];
                }
            } else {
                $data['grade'] = '';                
            }
            //自定义分组
            $group = UserGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag',
                            array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if($group){
                foreach ($group as $k => $v) {
                    $data['group'][$k]['id'] = $v['id'];
                    $data['group'][$k]['name'] = $v['name'];
                }
            }else{
                $data['group'] = '';                
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
        * 积分规则
        * @param type $merchant_id
        * @return type
        * @throws Exception
        */
       public function IntegrationRule($merchant_id)
       {
           $result = array();
           try {
               if (empty($merchant_id)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数merchant_id不能为空');
               }
               $points_rule = PointsRule::model()->findall('flag=:flag and merchant_id=:merchant_id',array(
                   ':flag' => FLAG_NO,
                   ':merchant_id' => $merchant_id
               ));
               $data = array();
               if($points_rule){
                   foreach($points_rule as $k => $v){
                       $data[$k]['id']          = $v['id'];
                       $data[$k]['name']        = $v['name'];
                       $data[$k]['cycle']       = $v['cycle'];
                       $data[$k]['num']         = $v['num'];
                       $data[$k]['condition']   = $v['condition'];
                       $data[$k]['points']      = $v['points'];
                       $data[$k]['if_storedpay_get_points'] = $v['if_storedpay_get_points'];
                       $data[$k]['create_time'] = $v['create_time'];
                       $data[$k]['last_time']   = $v['last_time'];
                   }
                   $result['data'] = $data;
                   $result ['status'] = ERROR_NONE;
                   $result['errMsg'] = ''; //错误信息
               }
           }catch (Exception $e) {
               $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
               $result['errMsg'] = $e->getMessage(); //错误信息
           }
           return json_encode($result);
       }

       /**
        * 编辑积分规则
        * @param type $merchant_id
        * @param type $id
        * @param type $period
        * @param type $num
        * @param type $condition
        * @param type $points
        * @param type $if_storedpay_get_points
        */
       public function EditIntegrationRule($merchant_id, $id, $period, $num, $condition, $points, $if_storedpay_get_points)
       {
           $result = array();
           try {
               if (empty($merchant_id)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数merchant_id不能为空');
               }
               if (empty($id)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数id不能为空');
               }
               if (empty($period)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数period不能为空');
               }
               $points_rule = PointsRule::model()->find('flag=:flag and merchant_id=:merchant_id and id=:id',array(
                   ':flag' => FLAG_NO,
                   ':merchant_id' => $merchant_id,
                   ':id' => $id
               ));
               if($points_rule){
                   $points_rule -> cycle = $period;
                   $points_rule -> num = $num;
                   if(!empty($condition)){
                       $points_rule -> condition = $condition;
                   }
                   if(!empty($points)){
                       $points_rule -> points = $points;
                   }
                   if(!empty($if_storedpay_get_points)){
                       $points_rule -> if_storedpay_get_points = $if_storedpay_get_points;
                   }
                   if($points_rule -> update()) {
                       $result ['status'] = ERROR_NONE;
                       $result['errMsg'] = ''; //错误信息
                   }
               }
           }catch (Exception $e) {
               $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
               $result['errMsg'] = $e->getMessage(); //错误信息
           }
           return json_encode($result);
       }
       
       //用户详情
        /**
         * merchantId 商户id
         * account   账号
         * id       会员id
         */
        public function UserDetail($merchantId,$id)
        {   
            //返回结果
            $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
            $flag   = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {                       
                    $data = array();
                    if(!empty($id))
                    {
                        $user = User::model()->find('id=:id and merchant_id=:merchant_id and flag=:flag',array(':id'=>$id,':merchant_id'=>$merchantId,':flag'=>FLAG_NO));
                        if($user){
                            $data['type']                   = $user->type;//用户类型 1会员 2微信粉丝 3支付宝粉丝
                            $data['account']                = $user->account;//账号
                            $data['avatar']                 = $user->avatar;//头像
                            $data['nickname']               = $user->nickname;//昵称                
                            $data['name']                   = $user->name;//真实姓名
                            $data['sex']                    = $user->sex;//性别
                            $data['birthday']               = $user->birthday;//生日
                            $data['social_security_number'] = $user->social_security_number;//身份证
                            $data['email']                  = $user->email;//邮箱
                            $data['marital_status']         = $user->marital_status;//婚姻状况
                            $data['work']                   = $user->work;//工作
                            $data['free_secret']            = $user->free_secret;//小额免密金额
                            $data['money']                  = $user->money;//储值金额
                            $data['points']                 = $user->points;//会员积分
                            $data['membershipgrade_id']     = $user->membershipgrade_id;//会员等级id
                            //查询会员等级名称
                            $grade = UserGrade::model()->findByPk($user['membershipgrade_id']);                           
                            if (empty($grade)) {
                                    $data['grade_name'] = '无'; //会员等级名称
                            }else {
                                    $data['grade_name'] = $grade['name']; //会员等级名称
                            }
                            $data['membership_card_no']     = $user->membership_card_no;//会员卡号
                            $data['login_time']             = $user->login_time;//最后登录时间
                            $data['login_ip']               = $user->login_ip;//最后登录ip
                            $data['regist_time']            = $user->regist_time;//注册时间
                            $data['address']                = $user->address;//地址
                            $data['from']                   = $user->from;//来源(多个来源)
                            $data['alipay_fuwu_id']         = $user->alipay_fuwu_id;//服务窗账号id
                            $data['alipay_status']          = $user->alipay_status;//支付宝用户关注状态
                            $data['alipay_avatar']          = $user->alipay_avatar;//支付宝服务窗头像
                            $data['alipay_nickname']        = $user->alipay_nickname;//支付宝用户昵称
                            $data['alipay_province']        = $user->alipay_province;//支付宝用户注册所填省份
                            $data['alipay_city']            = $user->alipay_city;//支付宝用户注册所填城市
                            $data['alipay_gender']          = $user->alipay_gender;//支付宝用户性别 M男性 F女性
                            $data['alipay_user_type_value'] = $user->alipay_user_type_value;//支付宝用户类型 1公司账号 2个人账号
                            $data['alipay_is_licence_auth'] = $user->alipay_is_licence_auth;//支付宝用户是否经过营业执照认证 T通过 F没有通过
                            $data['alipay_is_certified']    = $user->alipay_is_certified;//支付宝用户是否通过实名认证 T通过 F没有实名认证
                            $data['alipay_certified_grade_a'] = $user->alipay_certified_grade_a;//支付宝用户是否A类认证 T是A类认证 F非A类认证
                            $data['alipay_is_student_certified'] = $user->alipay_is_student_certified;//支付宝用户是否是学生 T是学生 F不是学生
                            $data['alipay_is_bank_auth']    = $user->alipay_is_bank_auth;//支付宝用户是否经过银行卡认证 T经过银行卡认证 F未经过银行卡认证
                            $data['alipay_is_mobile_auth']  = $user->alipay_is_mobile_auth;//支付宝用户是否经过手机认证 T经过手机认证 F未经过手机认证
                            $data['alipay_user_status']     = $user->alipay_user_status;//支付宝用户状态 Q快速注册用户 T已认证用户 B被冻结账户 W已注册未激活账户
                            $data['alipay_is_id_auth']      = $user->alipay_is_id_auth;//支付宝用户是否身份证认证 T身份证认证 F非身份证认证
                            $data['alipay_subscribe_time']  = $user->alipay_subscribe_time;//支付宝用户关注时间
                            $data['alipay_cancel_subscribe_time'] = $user->alipay_cancel_subscribe_time;//支付宝用户取消关注时间
                            $data['alipay_subscribe_store_id'] = $user->alipay_subscribe_store_id;//支付宝用户关注入口门店
                            $data['register_address']       = $user->register_address;//注册地址（省,市）
                            $data['wechat_status']          = $user->wechat_status;//微信用户关注状态 1 未关注 2已关注 3取消关注
                            $data['wechat_id']              = $user->wechat_id;//微信用户openid
                            $data['wechat_nickname']        = $user->wechat_nickname;//微信用户昵称
                            $data['wechat_sex']             = $user->wechat_sex;//微信用户性别 1男性 2女性
                            $data['wechat_country']         = $user->wechat_country;//微信用户所在国家
                            $data['wechat_province']        = $user->wechat_province;//微信用户所在省份
                            $data['wechat_city']            = $user->wechat_city;//微信用户所在城市
                            $data['wechat_language']        = $user->wechat_language;//微信用户的语言
                            $data['wechat_headimgurl']      = $user->wechat_headimgurl;//微信用户头像
                            $data['wechat_unionid']         = $user->wechat_unionid;//微信用户unionid
                            $data['wechat_remark']          = $user->wechat_remark;//微信用户备注
                            $data['wechat_groupid']         = $user->wechat_groupid;//微信用户所在分组id
                            $data['wechat_subscribe_time']  = $user->wechat_subscribe_time;//微信用户关注时间
                            $data['wechat_cancel_subscribe_time'] = $user->wechat_cancel_subscribe_time;//微信用户取消关注时间
                            $data['wechat_subscribe_store_id'] = $user->wechat_subscribe_store_id;//微信用户关注入口门店
                            $data['switch']                 = $user->switch;//会员等级是否受积分限制1受限制2不受限制                       
                            $data['create_time']            = $user->create_time;//创建时间
                            $data['last_time']              = $user->last_time;//最近更新时间
                            $data['login_client']           = $user->login_client;//最后登录客户端
                            $data['province']               = $user->province;//省
                            $data['city']                   = $user->city;//市
                            $criteri = new CDbCriteria();
                            $criteri -> order = 'create_time asc';
                            $criteri->addcondition('flag=:flag and user_id=:user_id');
                            $criteri->params[':flag'] = FLAG_NO;
                            $criteri->params[':user_id'] = $id;
                            $usergrowuprecord = UserGrowupRecord::model()->findall($criteri);
                            if($usergrowuprecord) {
                                foreach($usergrowuprecord as $a => $b){
                                    $data['list'][$a]['user_grade_name'] = $b['user_grade_name'];
                                    $data['list'][$a]['create_time'] = $b['create_time'];
                                }
                            } else {
                                $data['list'] = array();
                            }
                            $usertag = UserTag::model()->findall('flag=:flag and user_id=:user_id',array(
                                ':flag' => FLAG_NO,
                                ':user_id' => $id
                            ));
                            //标签
                            $tag_value = array();
                            if($usertag){
                                foreach($usertag as $key =>$val){
                                    $tag_value[$key] = $val['tag_value'];
                                }                                 
                            }   
                            $data['tag_value'] = $tag_value;
                            $group = Group::model()->findall('flag=:flag and user_id=:user_id',array(
                                ':flag' => FLAG_NO,
                                ':user_id' => $id,
                            ));
                            //分组
                            $groupname = array();
                            if($group){
                                foreach($group as $k => $v) {
                                    $userGroup = UserGroup::model()->find('flag=:flag and merchant_id=:merchant_id and id=:id',array(
                                        ':flag' => FLAG_NO,
                                        ':merchant_id' => $merchantId,
                                        ':id' => $v -> group_id
                                    ));
                                    $groupname[] = $userGroup['name'];
                                }
                                if($userGroup){
                                    $data['group'] = $groupname;
                                } else {
                                    $data['group'] = '';
                                }
                            } else {
                                $data['group'] = '';
                            }
                            
                            $data['order'] = array();                           
                            $data['order_count'] = '0';                            
                            $data['sum_order'] = '0';                            
                            $pay_status = ORDER_STATUS_PAID;
                            $store = Store::model()->findall('flag=:flag and merchant_id=:merchant_id',array(
                                ':flag' => FLAG_NO,
                                ':merchant_id' => $merchantId,
                            ));
                            $store_id = array();
                            if($store){
                                foreach($store as $v){
                                    $store_id[] = $v['id'];
                                }
                            }
                            //消费记录
                            $criteria = new CDbCriteria();
                            $criteria -> order = 'pay_time desc';
                            $criteria->addCondition('flag=:flag and pay_status=:pay_status');
                            $criteria->params[':flag'] = FLAG_NO;                            
                            $criteria->addInCondition('store_id', $store_id);
                            $criteria->params[':pay_status'] = $pay_status;
                            /*
                            if ($id){
                                $criteria->addCondition('user_id=:user_id');
                                $criteria->params[':user_id'] = $id;
                            }
                            */
                            $wechat_user_id = $data['wechat_id'];
                            $alipay_user_id = $data['alipay_fuwu_id'];
                            if ($wechat_user_id){
                                $criteria->addCondition('wechat_user_id=:wechat_user_id');
                                $criteria->params[':wechat_user_id'] = $wechat_user_id;
                            }
                            if ($alipay_user_id){
                                $criteria->addCondition('alipay_user_id=:alipay_user_id');
                                $criteria->params[':alipay_user_id'] = $alipay_user_id;
                            }
                            /*
                            $criteria->addCondition('user_id=:user_id or wechat_user_id=:wechat_user_id or alipay_user_id=:alipay_user_id');
                            $criteria->params[':user_id'] = $id;
                            $wechat_user_id = $user->wechat_id;
                            $criteria->params[':wechat_user_id'] = "$wechat_user_id";
                            $alipay_user_id = $user->alipay_fuwu_id;
                            $criteria->params[':alipay_user_id'] = "$alipay_user_id";
                            */
                            //分页
                            $pages = new CPagination(Order::model()->count($criteria));
                            $pages->pageSize = Yii::app() -> params['perPage'];
                            $pages->applyLimit($criteria);
                            $this->page = $pages;
                            $order = Order::model()->findall($criteria);
                            if($order){ 
                                $order_paymoney = Yii::app()->db->createCommand("
                                    select sum(order_paymoney)
                                    from wq_order where merchant_id=$merchantId and pay_status=$pay_status and flag=1 and (user_id=$id or wechat_user_id='$wechat_user_id' or alipay_user_id='$alipay_user_id')                            
                                ")->queryScalar();
                                //累计消费金额
                                $data['sum_order'] = $order_paymoney;
                                $data['order_count'] = Order::model()->count($criteria);
                                foreach($order as $k => $v){
                                    $data['order'][$k]['pay_time'] = $v['pay_time'];
                                    $data['order'][$k]['store_name'] = $v->store->name;
                                    $data['order'][$k]['pay_channel'] = $v['pay_channel'];                                    
                                    $data['order'][$k]['money'] = $v['order_paymoney'];
                                }
                            }
                        }
                    }
                    $result['status'] = ERROR_NONE;
                    $result['data']   = $data;            
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }            
            return json_encode($result);
        }
        
        
        //支付宝用户关注事件推送
        public function saveAlipayFansInfo($merchant_id,$fromUsername,$info){
        	$result = array();
        	try {
        		//根据商户id和wechat openid 查找用户
        		$user = User::model() -> find('alipay_fuwu_id =:alipay_fuwu_id and merchant_id =:merchant_id',array(
        				':alipay_fuwu_id' => $fromUsername,
        				':merchant_id' => $merchant_id
        		));
        		$info = json_decode($info);

        		if(!empty($user)){
        			$user -> alipay_status = ALIPAY_USER_SUBSCRIBE;//已关注状态
        			$user -> alipay_subscribe_time = date ( 'Y-m-d H:i:s' );
        			$user -> alipay_logon_id = $info -> logon_id;
        			$user -> alipay_user_name = $info -> user_name;
        			if(empty($user -> nickname)){
        				$user -> nickname = $info -> user_name;
        			}
        			if($user -> update()){
        				
        			}else {
        				throw new Exception('修改失败');
        			}
        		}else{
        			$user = new User();
        			$user -> alipay_fuwu_id = $fromUsername;
        			$user -> alipay_subscribe_time = date ( 'Y-m-d H:i:s' );
        			$user -> merchant_id = $merchant_id;
        			$user -> type = USER_TYPE_ALIPAY_FANS;
        			$user -> alipay_status = ALIPAY_USER_SUBSCRIBE;//已关注状态
        			$user -> alipay_logon_id = $info -> logon_id;
        			$user -> alipay_user_name = $info -> user_name;
        			$user -> nickname = $info -> user_name;
        			$user -> create_time = new CDbExpression('now()');
        			if($user -> save()){
        				
        			}else{
        				throw new Exception('保存失败');
        			}
        		}
        		$result['status'] = ERROR_NONE;
        			
        	} catch (Exception $e) {
        		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
        		$result['errMsg'] = $e->getMessage(); //错误信息
        	}
        	return json_encode($result);
        }
        
        //服务窗粉丝取消关注事件推送处理
        /*
         *
        * */
        public function cancelAlipaySubscribe($merchant_id,$fromUsername){
        	try {
        		$user = User::model() -> find('merchant_id =:merchant_id and alipay_fuwu_id =:alipay_fuwu_id',array(
        				':merchant_id' => $merchant_id,
        				':alipay_fuwu_id' => $fromUsername
        		));
        		//保存为取消关注和取消关注时间
        		$user -> alipay_status = ALIPAY_USER_CANCELSUBSCRIBE;
        		$user -> alipay_cancel_subscribe_time = date ( 'Y-m-d H:i:s' );
        		$user -> update();
        
        	} catch (Exception $e) {
        		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
        		$result['errMsg'] = $e->getMessage(); //错误信息
        	}
        	return json_encode($result);
        }

        /**
         * 获取标签信息
         * @param type $merchant_id
         * @return type
         * @throws Exception
         */
        public function getUserTag($merchant_id)
        {
            $result = array();
            $data = array();
            try {
                if (empty($merchant_id)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数merchant_id不能为空');
                }
                $tag = Tag::model()->findall('flag=:flag and merchant_id=:merchant_id',array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id));
                if($tag){
                    foreach($tag as $k => $v) {
                        $data['list'][$k]['name']  = $v['name'];
                        $data['list'][$k]['value'] = $v['value'];
                    }
                    $result['data'] = $data;
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }                     
            } catch (Exception $e) {
                $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                $result['errMsg'] = $e->getMessage(); //错误信息
            }
            return json_encode($result);
        }
        
        /**
         * 为用户设置标签
         * @param type $user_id
         * @param type $tag_value
         * @return type
         * @throws Exception
         */
        public function UserTag($user_id, $tag_value)
        {
            $result = array();
            try {
                if (empty($user_id)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数user_id不能为空');
                }
                if (empty($tag_value)) {
                   $result['status'] = ERROR_PARAMETER_FORMAT;
                   throw new Exception('参数tag_value不能为空');
                }
                $usertags = UserTag::model()->findall('flag=:flag and user_id=:user_id',array(':flag'=>FLAG_NO,':user_id'=>$user_id));
                if(!empty($usertags)){
                    foreach($usertags as $val){
                        $user_tag = UserTag::model()->find('flag=:flag and id=:id',array(':flag'=>FLAG_NO,':id'=>$val['id']));
                        $user_tag -> delete();
                    }
                }
                foreach($tag_value as $v){
                    $usertag = new UserTag();
                    $usertag -> user_id = $user_id;
                    $usertag -> tag_value = $v;
                    $usertag -> create_time = new CDbExpression('now()');
                    if(!$usertag -> save()){
                        $result['status'] = ERROR_NO_DATA;
                        $result['errMsg'] = '保存失败';
                    }
                }
                $result['status'] = ERROR_NONE;
             } catch (Exception $e) {
                $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                $result['errMsg'] = $e->getMessage(); //错误信息
            }
            return json_encode($result);
        }
        
        /*
         * 用户统计 - 获取累计用户，今日新增用户，今日到店消费用户
         */
        public function getUserNumAndNewuserNumAndPurchaseUser($merchant_id){
        	$result = array();
        	try {
        		/****************wechat****************/
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
			
        		//获取正在关注的粉丝
        		$criteria->addCondition('wechat_status = :wechat_status');
        		$criteria->params[':wechat_status'] = WECHAT_USER_SUBSCRIBE;

        		$criteria->params[':flag'] = FLAG_NO;
        		
        		$criteria->addCondition('type = :type');
        		$criteria->params[':type'] = USER_TYPE_WECHAT_FANS;
        		
        		//累计微信粉丝数
        		$total_wechat_fans = User::model() -> count($criteria);
        		
        		//时间筛选(今日)
        		$criteria->addCondition('create_time >= :time_start');
        		$criteria->params[':time_start'] = date('y-m-d 00:00:00');
        		$criteria->addCondition('create_time <= :time_end');
        		$criteria->params[':time_end'] = date('y-m-d 23:59:59');
        		//今日新增微信粉丝数
        		$wechat_fans_today = User::model() -> count($criteria);
        		
        		
        		/*******************alipay*********************/
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;

        		//获取正在关注的支付宝粉丝
        		$criteria->addCondition('alipay_status = :alipay_status');
        		$criteria->params[':alipay_status'] = ALIPAY_USER_SUBSCRIBE;
        		 
        		$criteria->addCondition('type = :type');
        		$criteria->params[':type'] = USER_TYPE_ALIPAY_FANS;
        		
        		//累计支付宝粉丝数
        		$total_alipay_fans = User::model() -> count($criteria);
        		
        		//时间筛选(今日)
        		$criteria->addCondition('create_time >= :time_start');
        		$criteria->params[':time_start'] = date('y-m-d 00:00:00');
        		$criteria->addCondition('create_time <= :time_end');
        		$criteria->params[':time_end'] = date('y-m-d 23:59:59');
        		//今日新增支付宝粉丝数
        		$alipay_fans_today = User::model() -> count($criteria);
        		
        		/*******************会员********************/
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;
        		
        		$criteria->addCondition('type = :type');
        		$criteria->params[':type'] = USER_TYPE_WANQUAN_MEMBER;
        		
        		//累计会员数
        		$total_member_num = User::model() -> count($criteria);
        		
        		//时间筛选(今日)
        		$criteria->addCondition('create_time >= :time_start');
        		$criteria->params[':time_start'] = date('y-m-d 00:00:00');
        		$criteria->addCondition('create_time <= :time_end');
        		$criteria->params[':time_end'] = date('y-m-d 23:59:59');
        		//今日新增会员数
        		$member_num_today = User::model() -> count($criteria);
        		
        		/**********************今日到店消费数*************************/
        		$criteria = new CDbCriteria();
        		$store = Store::model() -> findAll('merchant_id =:merchant_id',array(
        				':merchant_id' => $merchant_id
        		));
        		$store_id_arr = array();
        		$count = 0;
        		foreach ($store as $k => $v){
        			$store_id_arr[$count] = $v -> id;
        			if(!empty($v -> relation_store_id)){
        				$count ++;
        				$store_id_arr[$count] = $v -> relation_store_id;
        			}
        			$count++;
        		}
        		$criteria->addInCondition('store_id', $store_id_arr);
        		//已支付
        		$criteria->addCondition('pay_status = :pay_status');
        		$criteria->params[':pay_status'] = ORDER_STATUS_PAID;
        		//今日
        		$criteria->addCondition('create_time >= :time_start');
        		$criteria->params[':time_start'] = date('y-m-d 00:00:00');
        		$criteria->addCondition('create_time <= :time_end');
        		$criteria->params[':time_end'] = date('y-m-d 23:59:59');

        		$order = Order::model() -> findAll($criteria);
        		$purchase_user = array();
        		foreach ($order as $k => $v){
        			if(!empty($v -> user_id)){
        				$purchase_user[$k] = $v -> user_id;
        			}elseif (!empty($v -> alipay_user_id)){
        				$purchase_user[$k] = $v -> alipay_user_id;
        			}elseif (!empty($v -> wechat_user_id)){
        				$purchase_user[$k] = $v -> wechat_user_id;
        			}
        		}
        		//数组去重
        		$purchase_user_num = count(array_unique($purchase_user));
        		
        		$data =array();
        		$data['total_wechat_fans'] = $total_wechat_fans;
        		$data['wechat_fans_today'] = $wechat_fans_today;
        		$data['total_alipay_fans'] = $total_alipay_fans;
        		$data['alipay_fans_today'] = $alipay_fans_today;
        		$data['total_member_num'] = $total_member_num;
        		$data['member_num_today'] = $member_num_today;
        		$data['purchase_user_num'] = $purchase_user_num;
        		
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
         * 获取用户来源和用户性别
         * */
        public function getUserFromNumAndUserSexNum($merchant_id){
        	$result = array();
        	try {
        		/****************wechat****************/
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;
        		
        		$criteria->addCondition('type = :type');
        		$criteria->params[':type'] = USER_TYPE_WECHAT_FANS;
        		// 正在关注
        		$criteria->addCondition('wechat_status = :wechat_status');
        		$criteria->params[':wechat_status'] = WECHAT_USER_SUBSCRIBE;
        		
        		//累计微信粉丝数
        		$total_wechat_from = User::model() -> count($criteria);
        		
        		/*******************alipay*********************/
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;
        		
        		$criteria->addCondition('type = :type');
        		$criteria->params[':type'] = USER_TYPE_ALIPAY_FANS;

        		// 正在关注
        		$criteria->addCondition('alipay_status = :alipay_status');
        		$criteria->params[':alipay_status'] = ALIPAY_USER_SUBSCRIBE;

        		//累计支付宝粉丝数
        		$total_alipay_from = User::model() -> count($criteria);
        		
        		/*******************会员********************/
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;
        		
        		$criteria->addCondition('type = :type');
        		$criteria->params[':type'] = USER_TYPE_WANQUAN_MEMBER;

        		$user = User::model() -> findAll($criteria);
        		//来源未知
        		$total_unknown_from = 0;
        		foreach ($user as $k => $v){
        			if($v -> from == USER_FROM_WECHAT){
        				$total_wechat_from ++;
        			}elseif ($v -> from == USER_FROM_ALIPAY){
        				$total_alipay_from ++;
        			}else{
        				$total_unknown_from ++;
        			}
        		}

        		/*********************男/女性数量**********************/
        		$criteria = new CDbCriteria();
        		// 获取有效人数
        		// SELECT * FROM wq_user WHERE (type=1 OR (type=2 AND wechat_status=2) OR (type=3 AND alipay_status=2)) AND merchant_id=13;
				$criteria->addCondition('type='.USER_TYPE_WANQUAN_MEMBER.' OR (type='.USER_TYPE_WECHAT_FANS.' AND wechat_status='.WECHAT_USER_SUBSCRIBE.') OR (type='.USER_TYPE_ALIPAY_FANS.' AND alipay_status='.ALIPAY_USER_SUBSCRIBE.')');

        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;

        		// var_dump($criteria);exit;

        		//总用户数
        		$total_user_num = User::model() -> count($criteria);
        		$criteria->addCondition('sex = :sex');
        		$criteria->params[':sex'] = USER_SEX_MALE;
        		//男性数量
        		$total_male_num = User::model() -> count($criteria);
        		
        		$criteria = new CDbCriteria();
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;
        		$criteria->addCondition('sex = :sex');
        		$criteria->params[':sex'] = USER_SEX_FEMALE;
        		//男性数量
        		$total_female_num = User::model() -> count($criteria);
        		//未知性别人数
        		$total_unknown_sex_num = $total_user_num - $total_male_num - $total_female_num;
        		
        		
        		$data = array();
        		$data['total_wechat_from'] = $total_wechat_from;
        		$data['total_alipay_from'] = $total_alipay_from;
        		$data['total_unknown_from'] = $total_unknown_from;
        		
        		$data['total_male_num'] = $total_male_num;
        		$data['total_female_num'] = $total_female_num;
        		$data['total_unknown_sex_num'] = $total_unknown_sex_num;
        		
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
         * 获取用户各个年龄段数量
         * 0-18,18-30,30-50,50-100
         */
        public function getUserAgeNum($merchant_id){
        	$result = array();
        	try {
        		$criteria = new CDbCriteria();
        		// 获取有效人数
        		$criteria->addCondition('type='.USER_TYPE_WANQUAN_MEMBER.' OR (type='.USER_TYPE_WECHAT_FANS.' AND wechat_status='.WECHAT_USER_SUBSCRIBE.') OR (type='.USER_TYPE_ALIPAY_FANS.' AND alipay_status='.ALIPAY_USER_SUBSCRIBE.')');
        		$criteria->addCondition('merchant_id = :merchant_id');
        		$criteria->params[':merchant_id'] = $merchant_id;
        		$criteria->addCondition('flag = :flag');
        		$criteria->params[':flag'] = FLAG_NO;
        		$user = User::model() -> findAll($criteria);
        		
        		$num_unknown = 0;//未知
        		$num_0_18 = 0;//0-18岁
        		$num_18_30 = 0;//18-30岁
        		$num_30_50 = 0;//30-50岁
        		$num_50_m = 0;//50以上岁
        		
        		$now = time();//今天
        		foreach ($user as $k => $v){
        			if(empty($v -> birthday)){
        				$num_unknown ++;
        			}else{
        				$age = date('Y', time()) - date('Y', strtotime($v -> birthday)) - 1;
        				if (date('m', time()) == date('m', strtotime($v -> birthday))){
        				
        					if (date('d', time()) > date('d', strtotime($v -> birthday))){
        						$age++;
        					}
        				}elseif (date('m', time()) > date('m', strtotime($v -> birthday))){
        					$age++;
        				}
        				if($age <= 18){
        					$num_0_18 ++;
        				}elseif ($age >18 && $age <=30){
        					$num_18_30 ++;
        				}elseif ($age >30 && $age <=50){
        					$num_30_50 ++;
        				}elseif($age > 50){
        					$num_50_m ++;
        				}
        			}
        		}
        		
        		$data = array();
        		$data['num_unknown'] = $num_unknown;
        		$data['num_0_18'] = $num_0_18;
        		$data['num_18_30'] = $num_18_30;
        		$data['num_30_50'] = $num_30_50;
        		$data['num_50m'] = $num_50_m;
        		$result['data'] = $data;
        		
        		$result['status'] = ERROR_NONE; //状态码
        		$result['errMsg'] = ''; //错误信息
        		
        	} catch (Exception $e) {
        		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
        		$result['errMsg'] = $e->getMessage(); //错误信息
        	}
        	return json_encode($result);
        }
        
        
        //判断是否符合发红包要求
        public function checkOpenid($openid){
            $result = array();
            try {
                $temp = TempWechatOpenid::model() -> find('openid=:openid and flag =:flag',array(
                    ':openid' => $openid,
                    ':flag' => FLAG_NO
                ));
                if(!empty($temp)){
                    if($temp -> if_get == 1){
                        $user = User::model() -> find('wechat_id =:wechat_id and flag =:flag and wechat_status = 2 and merchant_id = 8',array(
                            ':wechat_id' => $openid,
                            ':flag' => FLAG_NO
                        ));
                        if(!empty($user)){
                            $result['data'] = 1;
                        }else {
                            $result['data'] = 2;
                        }
                    }else {
                        $result['data'] = 2;
                    }
                }else{
                    $result['data'] = 2;
                }
                $result['status'] = ERROR_NONE;
                
            } catch (Exception $e) {
                $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                $result['errMsg'] = $e->getMessage(); //错误信息
            }
            return json_encode($result);
        }
        
        //成功发送红包后，将if_get字段修改成已发送
        public function changeIfget($openid){
            $result = array();
            try {
                $temp = TempWechatOpenid::model() -> find('openid=:openid and flag =:flag',array(
                    ':openid' => $openid,
                    ':flag' => FLAG_NO
                ));
                if(!empty($temp)){
                    $temp -> if_get = 2;
                    if($temp -> update()){
                        $result['status'] = ERROR_NONE;
                    }else{
                        throw new Exception('状态修改失败');
                    }
                }
            } catch (Exception $e) {
                $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
                $result['errMsg'] = $e->getMessage(); //错误信息
            }
            return json_encode($result);
        }
        
}