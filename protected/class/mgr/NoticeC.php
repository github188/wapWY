<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 2015/07/22
 * @author xyf
 */
class NoticeC extends mainClass
{
	public $page = null;
	/**
	 * 获取公告列表
	 * $title 按公告标题搜索
	 * $time  按公告创建时间搜索
	 */
	public function getNoticeList($title,$time)
	{
		$result = array ();
		
		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag = :flag');
		$criteria -> params = array(':flag'=>FLAG_NO);
		
		if(!empty($title)){
			$criteria -> addCondition('title = :title');
			$criteria -> params[':title'] = $title;
		}
		
		if(!empty($time)){
			$Time = array();
			$Time = explode('-', $time);
			$criteria -> addBetweenCondition('create_time', $Time[0].' 00:00:00', $Time[1].' 23:59:59');
		}
		
		$criteria -> order = 'create_time desc';
		
		//分页
		$pages = new CPagination(Notice::model()->count($criteria));
		$pages -> pageSize = Yii::app() -> params['perPage'];
		$pages -> applyLimit($criteria);
		$this -> page = $pages;
		
		$model = Notice::model()->findAll($criteria);
		
		$data = array();
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['title'] = $v -> title; //标题
				$data['list'][$k]['content'] = $v -> content; //内容
				$data['list'][$k]['release_status'] = $v -> release_status; //发布状态
				$data['list'][$k]['release_time'] = $v -> release_time; //发布时间
				$data['list'][$k]['create_time'] = $v -> create_time;
				$data['list'][$k]['last_time'] = $v -> last_time;
				$data['list'][$k]['admin_name'] = isset($v -> admin->name)?$v -> admin->name:'';
			}
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
			$result['data'] = $data;
		}else{
			$data['list'] = array();
			$result['data'] = $data;
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 添加公告
	 * $title  公告标题
	 * $content 公告内容
	 */
	public function addNotice($title,$content)
	{
		$result = array();
		$model = new Notice();
		$model -> title = $title;
		$model -> content = $content;
		$model -> create_time = date('Y-m-d H:i:s');
		
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result ['errMsg'] = '';
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 删除公告
	 * $notice_id  公告表id
	 * 
	 */
	public function delNotice($notice_id)
	{
		$result = array();
		$model = Notice::model()->findByPk($notice_id);
		$model -> flag = FLAG_YES;
		
		if($model->save()){
			$result ['status'] = ERROR_NONE;
			$result ['errMsg'] = '';
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 编辑公告
	 * $notice_id  公告表id
	 * $title  公告标题
	 * $content 公告内容
	 */
	public function editNotice($notice_id,$title,$content)
	{
		$result = array();
		$model = Notice::model()->findByPk($notice_id);
		$model -> title = $title;
		$model -> content = $content;
		$model -> last_time = date('Y-m-d H:i:s');
		
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result ['errMsg'] = '';
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 公告发布
	 * $notice_id  公告表id
	 */
	public function releaseNotice($notice_id)
	{
		$result = array();
		$model = Notice::model()->findByPk($notice_id);
		if($model -> release_status == RELEASE_STATUS_NO){ //发布
			$model -> release_status = RELEASE_STATUS_YES;
			$model -> admin_id = Yii::app()->session['admin_id']; //存入发布人
			$model -> release_time = date('Y-m-d H:i:s');
		}else{ //取消发布
			$model -> release_status = RELEASE_STATUS_NO;
		}
		
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result ['errMsg'] = '';
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
}
