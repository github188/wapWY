<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 会员管理类
 */
class WebC extends mainClass {
	
	/**
	 * 保存代理商信息
	 * @param $name
	 * @param $phone_num
	 * @param $company
	 * @param $address
	 * @param $advantage
	 */
	public function saveAgent($name, $phone_num, $company, $address, $advantage){
		$result = array();
		try {
			$model = new AgentApply();
			$model['name'] = $name;
			$model['phone_num'] = $phone_num;
			$model['company'] = $company;
			$model['address'] = $address;
			$model['advantage'] = $advantage;
			$model['create_time'] = date("Y-m-d H:i:s");
			
			if (empty($name)) {
				throw new Exception('姓名为空');
			}
			if (empty($phone_num)) {
				throw new Exception('手机号码为空');
			}
			if (empty($company)) {
				throw new Exception('公司名称为空');
			}
			if (empty($address)) {
				throw new Exception('公司地址为空');
			}
			
			if ($model->save()) {
				$result['status'] = ERROR_NONE; //状态码
				$result['errMsg'] = ''; //错误信息
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return $result;
	}
	
}