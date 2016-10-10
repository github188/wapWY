<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/*
* 创建时间：2015-6-18
* 创建人：顾磊
* */
class ContractC extends mainClass{
	public $page = null;
	//添加合同
	/* $merchantId 商户id 必填
	 * $product 签约产品 必填
	 * $rate 签约费率 必填
	 * $remark 备注
	 * */
	public function addContract($merchantId,$product,$rate,$remark){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$flag = 0;
		//验证商户id
		if(!isset($merchantId) || empty($merchantId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数merchantId缺失';
			$flag = 1;
		}
		//验证签约产品
		if(!isset($product) || empty($product)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数product缺失';
			$flag = 1;
		}
		//验证签约费率
		if(!isset($rate) || empty($rate)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数rate缺失';
			$flag = 1;
		}
		if($flag == 1){
			$result['errMsg'] = $errMsg;
			return json_encode($result);
		}
		
		$contract = Contract::model() -> find('merchant_id =:merchant_id and flag = :flag and product =:product',array(
				':merchant_id' => $merchantId,
				':flag' => FLAG_NO,
				':product' => $product
		));
		//判断该商户该产品的合同是否已存在，已存在就修改原有合同，不存在就创建一个新合同
		if(empty($contract)){
			$contract = new Contract();
			$contract -> create_time = new CDbExpression('now()');
		}else{
			$contract -> last_time = new CDbExpression('now()');
			$contract -> status = CONTRACT_STATUS_UNSUBMIT;
		}
		
		$contract -> merchant_id = $merchantId;
		$contract -> product = $product;
 		
		if(isset($remark) && !empty($remark)){
			$contract -> remark = $remark;
		}
		$contract -> rate = $rate;
		if($contract -> save()){
			$result['status'] = ERROR_NONE;
			return json_encode($result);
		}else{
			//商户保存失败
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '合同保存失败';
			return json_encode($result);
		}
		
	}
	
	//查询合同详情
	/*
	 * $contractId 合同id 必填
	 * */
	public function getContractDetails($contractId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		//验证合同id
		if(!isset($contractId) || empty($contractId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数contractId缺失';
			return json_encode($result);
		}
		
		$criteria = new CDbCriteria();
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $contractId;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$contract = Contract::model()->find($criteria);
		
		if($contract){
			$result['status'] = ERROR_NONE;
			$result['data'] = array(
					'id' => $contract -> id,
					'merchant_id' => $contract -> merchant_id,
					'merchant_name' => $contract -> merchant -> name,
					'seller_email' => $contract -> merchant -> seller_email,
					'product' => $contract -> product,
					'status' => $contract -> status,
					'create_time' => $contract -> create_time,
					'remark' => $contract -> remark,
					'rate' => $contract -> rate,
					'flag' => $contract -> flag
			);
			return json_encode($result);
		}else{
			//没有找到合同
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该合同不存在';
			return json_encode($result);
		}
	}
	
	//查询合同列表
	/* $agentId 合作商id 必填
	 * $status 状态
	 * $merchantName 商户名
	 * $merchantId 商户id
	 * */
	public function getContractList($agentId,$status,$merchantName,$merchantId=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$criteria = new CDbCriteria();
		//合作商id
		if(isset($agentId) && !empty($agentId)){

			$merchant = Merchant::model() -> findAll('agent_id=:agent_id and flag=:flag',array(
					':agent_id' => $agentId,
					':flag' => FLAG_NO
			));
			$merchant_id = array();
			foreach ($merchant as $k => $v){
				$merchant_id[$k] = $v -> id;
			}
			$criteria -> addInCondition('merchant_id',$merchant_id);
		}
		
		//状态搜索
		if(isset($status) && !empty($status)){
			$criteria->addCondition('status = :status');
			$criteria->params[':status'] = $status;
		}
		
		//商户id搜索
		if(isset($merchantId) && !empty($merchantId)){
			$criteria->addCondition('merchant_id = :merchant_id');
			$criteria->params[':merchant_id'] = $merchantId;
		}
		
		
		
		//商户名搜索找到所有合同
		if(isset($merchantName) && !empty($merchantName)){
			//根据商户名模糊匹配找到所有的商户id
			$criteria_merchant = new CDbCriteria();
			$criteria_merchant->addCondition("name like '%$merchantName%'");
			$merchant = Merchant::model() -> findAll($criteria_merchant);
			$merchant_id = array();
			foreach ($merchant as $k => $v){
				$merchant_id[$k] = $v -> id;
			}
			$criteria -> addInCondition('merchant_id',$merchant_id);
		}
		$criteria->order = 'create_time DESC';
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		
		$pages = new CPagination(Contract::model()->count($criteria));
		$pages->pageSize = Yii::app() -> params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		
		$contract = Contract::model()->findAll($criteria);
		
		//未提交支付宝的合同数量
		$uninput_contract = count(Contract::model() -> findAll('status =:status and flag=:flag',array(
				':status' => CONTRACT_STATUS_UNSUBMIT,
				':flag' => FLAG_NO
		)));
		//已提交支付宝的合同数量
		$inputed_contract = count(Contract::model() -> findAll('status =:status and flag=:flag',array(
				':status' => CONTRACT_STATUS_SUBMIT,
				':flag' => FLAG_NO
		)));
		//支付宝审核中的合同数量
		$auditing_contract = count(Contract::model() -> findAll('status =:status and flag=:flag',array(
				':status' => CONTRACT_STATUS_AUDIT,
				':flag' => FLAG_NO
		)));
		
		
		
		$data = array();
		
		$data['uninput_contract'] = $uninput_contract;
		$data['inputed_contract'] = $inputed_contract;
		$data['auditing_contract'] = $auditing_contract;
		
		if(!empty($contract)){
			foreach ($contract as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['contract_no'] = $v -> contract_no;
				$data['list'][$k]['merchant_id'] = $v -> merchant_id;
				$data['list'][$k]['merchant_name'] = $v -> merchant -> name;
				$data['list'][$k]['seller_email'] = $v -> merchant -> seller_email;
				$data['list'][$k]['product'] = $v -> product;
				$data['list'][$k]['status'] = $v -> status;
				$data['list'][$k]['create_time'] = $v -> create_time;
				$data['list'][$k]['remark'] = $v -> remark;
				$data['list'][$k]['rate'] = $v -> rate;
				$data['list'][$k]['flag'] = $v -> flag;			
			}
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
			return json_encode($result);
		}else{
			$result['status'] = ERROR_NONE;
			$data['list'] = array();
			$result['data'] = $data;
			return json_encode($result);
		}
	}
	
	
	//提交到支付宝
	/*
	 * $contractId 合同id
	 * $contract_no 合同号 提交支付宝时获取
	 * */
	public function InputAlipay($contractId,$contract_no=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(isset($contractId) && !empty($contractId)){
			$contract = Contract::model() -> find('id=:id and flag = :flag',array(
					':id' => $contractId,
					':flag' => FLAG_NO
			));
			if($contract){
				$transaction= Yii::app ()->db->beginTransaction();
				try {
					if($contract -> status == CONTRACT_STATUS_UNSUBMIT){
						$contract -> status = CONTRACT_STATUS_SUBMIT;
						if(isset($contract_no) && !empty($contract_no)){
							$contract -> contract_no = $contract_no;
						}
						if($contract -> update()){
							$merchant = Merchant::model() -> find('id=:id and flag=:flag',array(
									':id' => $contract -> merchant -> id,
									':flag' => FLAG_NO
							));
							if($merchant){
								if($merchant -> verify_status == MERCHANT_VERIFY_STATUS_NUSIGN || $merchant -> verify_status == MERCHANT_VERIFY_STATUS_AUDITING || $merchant -> verify_status == MERCHANT_VERIFY_STATUS_REJECT){
									$merchant -> verify_status = MERCHANT_VERIFY_STATUS_AUDITING;
								}
								if($merchant -> update()){
									$transaction->commit();
									$result['status'] = ERROR_NONE;
									$result['data'] = $contract -> id;
									return json_encode($result);
								}else {
									$transaction->rollBack();
									$result['status'] = ERROR_SAVE_FAIL;
									$result['errMsg'] = '商户修改失败';
									return json_encode($result);
								}
							}else{
								$transaction->rollBack();
								$result['status'] = ERROR_NO_DATA;
								$result['errMsg'] = '该商户不存在';
								return json_encode($result);
							}
						}
						
					}
				} catch (Exception $e) {
					$transaction->rollBack();
				}
				
			}else{
				//没有找到合同
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '该合同不存在';
				return json_encode($result);
			}
		}else{
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数contractId缺失';
			return json_encode($result);
		}
	}
	
	//商户确认
	/*
	 * $contractId 合同id
	 * */
	
	public function MerchantConfirm($contractId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(isset($contractId) && !empty($contractId)){
			$contract = Contract::model() -> find('id=:id and flag = :flag',array(
					':id' => $contractId,
					':flag' => FLAG_NO
			));
			if($contract){
				if($contract -> status == CONTRACT_STATUS_SUBMIT){
					$contract -> status = CONTRACT_STATUS_AUDIT;
					if($contract -> update()){
						$result['status'] = ERROR_NONE;
						$result['data'] = $contract -> id;
						return json_encode($result);
					}
				}
			}else{
				//没有找到合同
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '该合同不存在';
				return json_encode($result);
			}
		}else{
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数contractId缺失';
			return json_encode($result);
		}
	}
	
	
	//审核通过
	/*
	 *$contractId 合同id
	* */
	
	public function PassAudit($contractId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(isset($contractId) && !empty($contractId)){
			$contract = Contract::model() -> find('id=:id and flag = :flag',array(
					':id' => $contractId,
					':flag' => FLAG_NO
			));
			if($contract){
				$transaction= Yii::app ()->db->beginTransaction();
				try {
					if($contract -> status == CONTRACT_STATUS_AUDIT){
						$contract -> status = CONTRACT_STATUS_EFFECT;
						if($contract -> update()){
							$merchant = Merchant::model() -> find('id=:id and flag=:flag',array(
									':id' => $contract -> merchant_id,
									':flag' => FLAG_NO
							));
							if($merchant){
								if($merchant -> verify_status == MERCHANT_VERIFY_STATUS_AUDITING || $merchant -> verify_status == MERCHANT_VERIFY_STATUS_SIGN_SUCCESS || $merchant -> verify_status == MERCHANT_VERIFY_STATUS_INPUT_SUCCESS){
									$merchant -> verify_status = MERCHANT_VERIFY_STATUS_SIGN_SUCCESS;
									if($merchant -> update()){
										$transaction->commit();
										$result['status'] = ERROR_NONE;
										$result['data'] = $contract -> id;
										return json_encode($result);
									}
								}else{
									$transaction->rollBack();
									$result['status'] = ERROR_EXCEPTION;
									$result['errMsg'] = '该商户状态：'.$GLOBALS['__MERCHANT_VERIFY'][$merchant -> verify_status];
									return json_encode($result);
								}
							}else{
								$transaction->rollBack();
								$result['status'] = ERROR_NO_DATA;
								$result['errMsg'] = '该商户不存在';
								return json_encode($result);
							}
							
						}
					}
				} catch (Exception $e) {
					$transaction->rollBack();
				}
				
			}else{
				//没有找到合同
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '该合同不存在';
				return json_encode($result);
			}
		}else{
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数contractId缺失';
			return json_encode($result);
		}
	}
	
	//合同驳回
	/*
	 * $contractId 合同id 必填
	 * $remark 驳回理由
	 * */
	public function rejectContract($contractId,$remark){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(isset($contractId) && !empty($contractId)){
			$contract = Contract::model() -> find('id=:id and flag = :flag',array(
					':id' => $contractId,
					':flag' => FLAG_NO
			));
			if($contract){
				$transaction= Yii::app ()->db->beginTransaction();
				try {
					if($contract -> status == CONTRACT_STATUS_AUDIT || $contract -> status == CONTRACT_STATUS_SUBMIT || $contract -> status == CONTRACT_STATUS_UNSUBMIT){
						$contract -> status = CONTRACT_STATUS_REJECT;
						if(isset($remark) && !empty($remark)){
							$contract -> remark = $remark;
						}
						if($contract -> update()){
							
							$otherContract = Contract::model() -> findAll('merchant_id =:merchant_id and id != :id',array(
									':merchant_id' => $contract -> merchant_id,
									':id' => $contract -> id
							));
							
							if(!empty($otherContract)){
								$flag = 0;
								foreach ($otherContract as $k => $v){
									if($v -> status != CONTRACT_STATUS_REJECT && $v -> flag == FLAG_NO){
										$flag = 1;
										break;
									}
								}
								if($flag == 0){
									$merchant = Merchant::model() -> find('id =:id and flag =:flag',array(
											':id' => $contract -> merchant_id,
											':flag' => FLAG_NO
									));
									if($merchant){
										$merchant -> verify_status = MERCHANT_VERIFY_STATUS_REJECT;
										$merchant -> remark = '合同被驳回-'.$remark;
										if($merchant -> update()){
											
										}else{
											$transaction->rollBack();
											$result['status'] = ERROR_SAVE_FAIL;
											$result['errMsg'] = '商户状态修改失败';
											return json_encode($result);
										}
									}
								}	
							}
							$transaction->commit();
							$result['status'] = ERROR_NONE;
							$result['data'] = $contract -> id;
							return json_encode($result);
						}else{
							$transaction->rollBack();
							$result['status'] = ERROR_SAVE_FAIL;
							$result['errMsg'] = '合同状态修改失败';
							return json_encode($result);
						}
					}
				} catch (Exception $e) {
					$transaction->rollBack();
				}
				
			}else{
				//没有找到合同
				$result['status'] = ERROR_NO_DATA;
				$result['errMsg'] = '该合同不存在';
				return json_encode($result);
			}
		}else{
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数contractId缺失';
			return json_encode($result);
		}
	}
	
	
	//合同重新提交
	public function ResubmitContract($contractId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(!isset($contractId) || empty($contractId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数contractId缺失';
			return json_encode($result);
		}
		$contract = Contract::model() -> find('id=:id and flag=:flag',array(
				':id' => $contractId,
				':flag' => FLAG_NO
		));
		if($contract){
			$contract -> status = CONTRACT_STATUS_UNSUBMIT;
			if($contract -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '合同状态修改失败';
				return json_encode($result);
			}
		}else{
			//没有找到合同
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '该合同不存在';
			return json_encode($result);
		}
		
	}
	
	
}