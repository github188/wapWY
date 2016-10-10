<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 商城首单立减类
 */

class DMallActivity extends mainClass{
	
	public $page = null;
	
	/**
	 * 获取首单立减信息
	 * @param unknown $encrypt_id
	 * @throws Exception
	 */
	public function getFirstSingle($encrypt_id){
	    $result = array();
	    try {
	        $merchat = Merchant::model() -> find('encrypt_id =:encrypt_id',array(
	            ':encrypt_id' => $encrypt_id
	        ));
	        $mallActivity = MallActivity::model() -> find('merchant_id =:merchant_id and type =:type and flag =:flag',array(
	        				':merchant_id' => $merchat -> id,
	                        ':type' => DMALL_ACTIVITY_TYPE_SDLJ,
	        				':flag' => FLAG_NO
	        ));
	        if($mallActivity){
	            $data = array();
	            $data['id'] = $mallActivity -> id;
	            $data['type'] = $mallActivity -> type;
	            $data['name'] = $mallActivity -> name;
	            $data['num'] = $mallActivity -> num;
	            $data['img'] = $mallActivity -> img;
	            $data['start_time'] = $mallActivity -> start_time;
	            $data['end_time'] = $mallActivity -> end_time;
	            $data['coupons_id'] = $mallActivity -> coupons_id;
	            $data['receive_num'] = $mallActivity -> receive_num;
	            $result['status'] = ERROR_NONE;
	            $result['data'] = $data;
	        }else{
	            $result['status'] = ERROR_NO_DATA;
	            throw new Exception('商户未设置首单立减');
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage(); //错误信息
	    }
	    return json_encode($result);
	    
	}
	
	/**
	 * 获取周福利列表
	 * @param unknown $encrypt_id
	 * @throws Exception
	 */
	public function getWelfareList($encrypt_id)
	{
		$result = array();
		try {
			$merchant = Merchant::model()->find('encrypt_id =:encrypt_id', array(
				':encrypt_id' => $encrypt_id
			));
			$welfare_list = MallActivity::model()->with('coupons')->findAll(array(
				'condition' => 't.merchant_id =:merchant_id and t.type =:type  and t.flag =:flag',
				'params' => array(
					':merchant_id' => $merchant->id,
					':type' => DMALL_ACTIVITY_TYPE_ZFL,
					':flag' => FLAG_NO
				), 'order' => 't.status asc'
			));
			if ($welfare_list) {
				$data = array();
				foreach ($welfare_list as $k => $v) {
					if ($v->status == DMALL_ACTIVITY_STATUS_NOT_START) {
						if (strtotime($v->start_time) < strtotime(date('Y/m/d H:i')) && strtotime($v->end_time) > strtotime(date('Y/m/d H:i'))) {
							$v->status = DMALL_ACTIVITY_STATUS_STARTING;
						} elseif (strtotime($v->end_time) < strtotime(date('Y/m/d H:i'))) {
							$v->status = DMALL_ACTIVITY_STATUS_END;
						}
						$v->update();
					} elseif ($v->status == DMALL_ACTIVITY_STATUS_STARTING || $v->status == DMALL_ACTIVITY_STATUS_NO_STOCK) {
						if (strtotime($v->end_time) < strtotime(date('Y/m/d H:i'))) {
							$v->status = DMALL_ACTIVITY_STATUS_END;
						} elseif ($v->num == $v->receive_num) {
							$v->status = DMALL_ACTIVITY_STATUS_NO_STOCK;
						}
						$v->update();
					}

					$data[$k]['id'] = $v['id'];
					$data[$k]['name'] = $v['name'];
					$data[$k]['num'] = $v['num'];
					$data[$k]['start_time'] = $v['start_time'];
					$data[$k]['end_time'] = $v['end_time'];
					$data[$k]['status'] = $v['status'];
					$data[$k]['img'] = $v['img'];
					$data[$k]['coupons_id'] = $v['coupons_id'];
					$data[$k]['code'] = $v->coupons->code;
					$data[$k]['original_price'] = $v['original_price'];
					$data[$k]['receive_num'] = $v['receive_num'];
				}
				$result['status'] = ERROR_NONE;
				$result['data'] = $data;
			} else {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('商户未设置周福利');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 参与首单活动检查
	 * @param unknown $user_id
	 * @param string $merchant_id
	 * @return string
	 */
	public function checkFirstActivity($user_id, $merchant_id=NULL) {
		$result = array();
		try {
			$user = User::model()->findByPk($user_id);
			if (empty($user)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('会员不存在');
			}
			if (!empty($user['merchant_id'])) {
				$merchant_id = $user['merchant_id'];
			}	
			//查询商户首单立减活动
			$activity = MallActivity::model()->find('merchant_id = :merchant_id and type = :type and flag =:flag', 
					array(
					    ':merchant_id' => $merchant_id, 
					    ':type' => DMALL_ACTIVITY_TYPE_SDLJ,
					    ':flag' => FLAG_NO
					));
			if(!empty($activity)){
    			$activity_id = $activity['id'];
    			//开始时间
    			$start = strtotime($activity['start_time']);
    			//结束时间
    			$end = strtotime($activity['end_time']);
    			//总量
    			$num = $activity['num'];
    			//领取量
    			$r_num = $activity['receive_num'];
    			//首单立减优惠券id
    			$coupons_id = $activity['coupons_id'];
    			if ($start <= time() && $end >= time() && ($num - $r_num) > 0) {
    				//检查用户是否有线上交易
    				$order_num = Order::model()->count('user_id = :user_id and order_type = :order_type and pay_status = :pay_status', 
    						array(':user_id' => $user_id, ':order_type' => ORDER_TYPE_VIRTUAL, ':pay_status' => ORDER_STATUS_PAID));
    				if ($order_num == 0) {
    					//查询是否已领同类优惠券
    					$coupons_num = UserCoupons::model()->count('user_id = :user_id and coupons_id = :coupons_id and flag = :flag', 
    							array(':user_id' => $user_id, ':coupons_id' => $coupons_id, ':flag' => FLAG_NO));
    					if ($coupons_num == 0) {
    						//系统发放优惠券给用户
    						$userC = new UserUC();
    						$ret = $userC->newReceiveCoupons($user_id, $coupons_id, MARKETING_ACTIVITY_TYPE_DMALL_SDLJ, $activity_id);
    						$result = json_decode($ret, true);
    						if ($result['status'] != ERROR_NONE) {
    							$result['status'] = ERROR_EXCEPTION;
    							throw new Exception($result['errMsg']);
    						}
    						//更新领取量
    						$activity['receive_num'] += 1;
    						if (!$activity->save()) {
    							$result['status'] = ERROR_SAVE_FAIL;
    							throw new Exception('数据保存失败');
    						}
    					}
    				}else {
    					//优惠券失效
    					$coupons = UserCoupons::model()->find('user_id = :user_id and coupons_id = :coupons_id and flag = :flag and status = :status',
    							array(':user_id' => $user_id, ':coupons_id' => $coupons_id, ':flag' => FLAG_NO, ':status' => COUPONS_USE_STATUS_UNUSE));
    					if (!empty($coupons)) {
    						$coupons['status'] = COUPONS_USE_STATUS_EXPIRED;
    						$coupons->save();
    					}
    				}
    			}elseif (($num - $r_num) == 0) {
    				$activity['status'] = DMALL_ACTIVITY_STATUS_NO_STOCK;
    				$activity->save();
    			}elseif ($end <= time()) {
    				$activity['status'] = DMALL_ACTIVITY_STATUS_END;
    				$activity->save();
    				//优惠券失效
    				$coupons = UserCoupons::model()->find('user_id = :user_id and coupons_id = :coupons_id and flag = :flag and status = :status',
    						array(':user_id' => $user_id, ':coupons_id' => $coupons_id, ':flag' => FLAG_NO, ':status' => COUPONS_USE_STATUS_UNUSE));
    				if (!empty($coupons)) {
    					$coupons['status'] = COUPONS_USE_STATUS_EXPIRED;
    					$coupons->save();
    				}
    			}
    			//查询可用优惠券
    			$coupons = UserCoupons::model()->find('user_id = :user_id and coupons_id = :coupons_id and flag = :flag and status = :status',
    					array(':user_id' => $user_id, ':coupons_id' => $coupons_id, ':flag' => FLAG_NO, ':status' => COUPONS_USE_STATUS_UNUSE));
    			
    			if (!empty($coupons)) {
    				$result['user_coupons_id'] = $coupons['id'];
    			}
			}
			
			$result['status'] = ERROR_NONE;
			
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
	
		return json_encode($result);
	}
	
	//检查商户是否已经添加首单立减活动
	public function checkSdljActivity($merchant_id){
	    $result = array();
	    try {
	        $mallActivity = MallActivity::model() -> find('merchant_id =:merchant_id and flag =:flag and type =: type',array(
	            ':merchant_id' => $merchant_id,
	            ':flag' => FLAG_NO,
	            ':type' => DMALL_ACTIVITY_TYPE_SDLJ
	        ));
	        if(!empty($mallActivity)){
	            $result['data'] = 1;
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
	
	
}