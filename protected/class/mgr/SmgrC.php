<?php 
include_once(dirname(__FILE__).'/../mainClass.php');
/*
 * 时间：2015-7-6
* 创建人：顾磊
* */
class SmgrC extends mainClass{
	//添加客服经理
	/*
	 * $name 姓名
	 * $tel 联系电话
	 * $qq 联系QQ
	 * */
	public function addSmgr($name,$tel,$qq){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(!isset($name) || empty($name)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数name缺失';
			return json_encode($result);
		}
		
		if(!isset($tel) || empty($tel)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数tel缺失';
			return json_encode($result);
		}
		
		if(!isset($qq) || empty($qq)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数qq缺失';
			return json_encode($result);
		}
		$smgr = new Smgr();
		$smgr -> name = $name;
		$smgr -> tel = $tel;
		$smgr -> qq = $qq;
		if($smgr -> save()){
			$result['status'] = ERROR_NONE;
			$result['data'] = $smgr -> id;
			return json_encode($result);
		}else{
			$result['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败';
			return json_encode($result);
		}
	}
	
	//获取客服经理列表
	/*
	 * $name 客服经理名称
	 * */
	public function getSmgrList($name=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$criteria = new CDbCriteria();
		//商户名搜索
		if(isset($name) && !empty($name)){
			$criteria->addCondition("name like '%$name%'");
		}
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$smgr = Smgr::model() -> findAll($criteria);
		$data = array();
		if(!empty($smgr)){
			foreach ($smgr as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['name'] = $v -> name;
				$data['list'][$k]['tel'] = $v -> tel;
				$data['list'][$k]['qq'] = $v -> qq;
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
	
	//获取客服经理详情
	/*
	 * $agentId 代理商id
	 * */
	public function getSmgrDetails($agentId){
	//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数agentId缺失';
			return json_encode($result);
		}
		
		
		$model = Agent::model()->findByPk($agentId);
		
		$smgr = Smgr::model() -> find('id =:id and flag =:flag',array(
				':id' => $model['smgr_id'],
				':flag' => FLAG_NO
		));
		if($smgr){
			$result['status'] = ERROR_NONE;
			$result['data'] = array(
					'id' => $smgr -> id,
					'name' => $smgr -> name,
					'tel' => $smgr -> tel,
					'qq' => $smgr -> qq,
			);
			return json_encode($result);
		}else{
			//没有找到客服经理
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该客服经理不存在';
			return json_encode($result);
		}
	}
	

	
	//删除客服经理
	/*
	 * $smgrId 客服经理id
	 * */
	public function deleteSmgr($smgrId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		if(!isset($smgrId) || empty($smgrId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数smgrId缺失';
			return json_encode($result);
		}
		$smgr = Smgr::model() -> find('id=:id and flag =:flag',array(
				':id' => $smgrId,
				':flag' => FLAG_NO
		));
		
		if($smgr){
			$smgr -> flag = FLAG_YES;
			if($smgr -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}
		}else{
			//没有找到客服经理
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该客服经理不存在';
			return json_encode($result);
		}
		
	}
	
	/**
	 * 获取公告列表
	 * $notice_id  公告id
	 */
	public function getNoticeList($notice_id){
		$result = array ();
		$model = Notice::model()->findByPk($notice_id);
		$data = array();
		if(!empty($model)){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';
		    $data['list']['id'] = $model -> id;
		    $data['list']['admin_name'] = isset($model -> admin->name)?$model -> admin->name:'';;
		    $data['list']['title'] = $model -> title;
		    $data['list']['content'] = $model -> content;
		    $data['list']['release_time'] = date('m-d',strtotime($model -> release_time));
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据';
		}
		
		$result['data'] = $data;
		return json_encode($result);
	}
	
}