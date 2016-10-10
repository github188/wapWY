<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 储值类
 */
class StoredC extends mainClass {
	public $page = null;//分页信息
	/**
	 * 添加储值活动
	 * 
	 * @param $merchant_id 商户id
     * @param $name 活动名
     * @param $stored_money 预存金额
	 * @param $get_money 得到金额        	
	 * @param $start_time 活动开始时间        	
	 * @param $end_time 活动结束时间        	
	 */
	public function addStored($merchant_id,$name, $stored_money, $get_money, $start_time, $end_time) {
		$result = array ();
		$errMsg = '';
		$flag = 0;
		// 验证预存金额
		if (! isset ( $stored_money ) || empty ( $stored_money )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = ' 预存金额必填';
			$flag = 1;
			Yii::app()->user -> setFlash('stored_money','预存金额必填');
		}
		// 验证得到金额
// 		if (! isset ( $get_money ) || empty ( $get_money )) {
// 			$result ['status'] = ERROR_PARAMETER_MISS;
// 			$errMsg = $errMsg . ' 得到金额必填';
// 			$flag = 1;
// 			Yii::app()->user -> setFlash('get_money','得到金额必填');
// 		}
		
		//验证活动时间
		if (empty ( $start_time ) && empty ( $end_time )) {
			$result ['status'] = ERROR_PARAMETER_MISS;
			$errMsg = $errMsg . ' 活动时间必填';
			$flag = 1;
			Yii::app()->user -> setFlash('time','活动时间必填');
		}
		
		if ($flag == 1) {
			$result ['errMsg'] = $errMsg;
			return json_encode ( $result );
		}
		
		$model = new Stored ();
		$model->merchant_id = $merchant_id;
        $model->name=$name;
		$model->stored_money = $stored_money;
		$model->get_money = $get_money;
		$model->start_time = $start_time;
		$model->end_time = $end_time . ' 23:59:59';
		$model->create_time = date ( 'Y-m-d H:i:s' );
		
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
	 * 储值活动列表
	 * @param $merchant_id 商户id  
	 * $Time  活动时间搜索
	 */
	public function getStoredList($merchant_id,$time) {
		
		$result = array ();
		try {
			$criteria = new CDbCriteria ();
			if (! empty ( $merchant_id )) {
				$criteria->addCondition ( 'merchant_id=:merchant_id and flag=:flag' );
				$criteria->params = array (
						':merchant_id' => $merchant_id,
						':flag' => FLAG_NO
				);
			}else{
				$result['status'] = ERROR_PARAMETER_FORMAT;
				throw new Exception('商户号不能为空');
			}
			
			if(!empty($time)){
				$arr_time = explode('-', $time);
				$start_time = date('Y-m-d'.' 00:00:00',strtotime($arr_time[0]));
				$end_time = date('Y-m-d'.' 23:59:59',strtotime($arr_time[1]));
				$criteria -> addCondition('start_time>=:start_time and end_time<=:end_time');
				$criteria -> params[':start_time'] = $start_time;
				$criteria -> params[':end_time'] = $end_time;
			}
			
			$criteria -> order = 'create_time desc';
			
			$pages = new CPagination(Stored::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
			$model = Stored::model ()->findAll ( $criteria );
			
			$data = array ();
			foreach ( $model as $k => $v ) {
				$data ['list'] [$k] ['id'] = $v->id;
				$data ['list'] [$k] ['name'] = $v->name;
				$data ['list'] [$k] ['stored_money'] = $v->stored_money;
				$data ['list'] [$k] ['get_money'] = $v->get_money;
				$data ['list'] [$k] ['start_time'] = $v->start_time;
				$data ['list'] [$k] ['end_time'] = $v->end_time;
			}
			
			$result ['status'] = ERROR_NONE;
			$result ['data'] = $data;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 删除储值活动
	 * @param $id  储值活动id  
	 * 
	 */
	public function delStored($id)
	{
		$result = array();
		
		$model = Stored::model()->findByPk($id);
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
	 * 获取储值详情
	 * @param $id  储值活动id 
	 */
	public function getStoredDetails($id)
	{
		$result = array();
		$data = array();
		
		$model = Stored::model()->findByPk($id);
		if(!empty($model)){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';

				$data['list']['id'] = $model -> id;
				$data['list']['merchant_id'] = $model -> merchant_id;
				$data['list']['stored_money'] = $model -> stored_money;
				$data['list']['get_money'] = $model -> get_money;
				$data['list']['start_time'] = $model -> start_time;
				$data['list']['end_time'] = $model -> end_time;
				$data['list']['create_time'] = $model -> create_time;
				$data['list']['last_time'] = $model -> last_time;
			
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		$result['data'] = $data;
		return json_encode($result);
	}
	
	/**
	 * 获取储值明细
	 * @param $merchant_id 商户id
	 */
	public function getStoredOrder($merchant_id)
	{
		$data = array();
		
		$stored_id = array();
		
		$list = Stored::model()->findAll('merchant_id = :merchant_id', array(':merchant_id' => $merchant_id));
		foreach ($list as $k => $v){
			$stored_id[$k] = $v['id'];
		}
		$criteria = new CDbCriteria();
		$criteria->addCondition('pay_status = :pay_status');
		$criteria->params[':pay_status'] = ORDER_STATUS_PAID;
		$criteria -> addInCondition('stored_id', $stored_id);
		$criteria -> order = 'create_time desc';
		
		$pages = new CPagination(StoredOrder::model()->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		
		//$criteria -> order = 'create_time desc';
		
		$model = StoredOrder::model()->findAll($criteria);
		
		foreach ($model as $key => $value) {
			$data['list'][$key]['id'] = $value['id'];
			
			$user_account = '';
			if (!empty($value['user_id'])) {
				$user = User::model()->findByPk($value['user_id']);
				if (!empty($user)) {
					$user_account = $user['account'];
				}
			}
			$data['list'][$key]['user_account'] = $user_account;
			
			$stored_name = '';
			$pay_money = 0;
			if (!empty($value['stored_id'])) {
				//查询储值活动
				$stored = Stored::model()->findByPk($value['stored_id']);
				if (!empty($stored)) {
					$tmp = empty($stored['get_money']) ? '' : '送'.$stored['get_money'];
					$stored_name = '充'.$stored['stored_money'].$tmp;
					$pay_money = $stored['stored_money'];
				}
			}
			$data['list'][$key]['store_name'] = $stored_name;
			
			$operator_number = '无';
			$operator_name = '线上储值';
			if (!empty($value['operator_id'])) {
				//查询操作员
				$operator = Operator::model()->findByPk($value['operator_id']);
				if (!empty($operator)) {
					$operator_number = $operator['number'];
					$operator_name = $operator['name'];
				}
			}
			$data['list'][$key]['operator_number'] = $operator_number;
			$data['list'][$key]['operator_name'] = $operator_name;
			
			$data['list'][$key]['money'] = $value['num'] * $pay_money;
			$data['list'][$key]['order_status'] = $value['order_status'];
			$data['list'][$key]['pay_status'] = $value['pay_status'];
			$data['list'][$key]['pay_channel'] = $value['pay_channel'];
			$data['list'][$key]['create_time'] = $value['pay_time'];
			$data['list'][$key]['num'] = $value['num'];
		}
		
		$result ['status'] = ERROR_NONE;
		$result ['data'] = $data;
		return json_encode ( $result );
	}

    /**
     * 判断储值金额是否有一样的
     */
    public function checkStoredMoneySame($merchant_id,$stored_money)
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
            $model=Stored::model()->findAll($criteria);
            if(isset($model))
            {
                $flag=true;
                foreach($model as $key=>$value)
                {
                    //储值相同
                    if($value['stored_money']==$stored_money)
                    {
                        //有储值相同
                        $flag=false;
                        $result ['status'] = ERROR_DUPLICATE_DATA;
                        $result['errMsg'] = ''; //错误信息
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
    
    /**
     * 判断赠送金额是否有相同
     * return true(有相同)
     */
    public function checkGetMoneySame($merchant_id,$get_money)
    {
    	$model = Stored::model()->findAll('flag=:flag and merchant_id=:merchant_id and end_time>=:end_time and start_time<=:start_time'
    			                          ,array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id,':end_time'=>date('Y-m-d H:i:s'),':start_time'=>date('Y-m-d H:i:s')));
    	$flag = false;
    	foreach ($model as $v){
    		if($v['get_money'] == $get_money){
    			$flag = true;
    		}
    	}
    	
    	return $flag;
    }

    /**
     * 判断储值活动名称
     */
    public function checkStoredName($merchant_id,$name)
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
            $model=Stored::model()->findAll($criteria);
            if(isset($model))
            {
                $flag=true;
                foreach($model as $key=>$value)
                {
                    //储值相同
                    if($value['name']==$name)
                    {
                        //有储值相同
                        $flag=false;
                        $result ['status'] = ERROR_DUPLICATE_DATA;
                        $result['errMsg'] = ''; //错误信息
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
}