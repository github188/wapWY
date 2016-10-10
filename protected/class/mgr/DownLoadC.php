<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 资料下载类
 * 2015/07/28
 * @author xyf
 */
class DownLoadC extends mainClass
{
	public $page = null;
	/**
	 * 获取资料下载列表
	 * $title  标题
	 * $time   创建时间
	 * 
	 */
	public function getDownLoadList($title,$time)
	{
		$result = array();
		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag = :flag');
		$criteria -> params = array(':flag'=>FLAG_NO);
		$criteria -> order = 'create_time desc';
		
		if(!empty($title)){
			$criteria -> addCondition('title = :title');
			$criteria -> params[':title'] = $title;
		}
		
		if(!empty($time)){
			$Time = array();
			$Time = explode('-', $time);
			$criteria -> addBetweenCondition('create_time', $Time[0].' 00:00:00', $Time[1].' 23:59:59');
		}
		
		//分页
		$pages = new CPagination(Download::model()->count($criteria));
		$pages -> pageSize = Yii::app()->params['perPage'];
		$pages -> applyLimit($criteria);
		$this-> page = $pages;
		
		$model = Download::model()->findAll($criteria);
		
		$data = array();
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['title'] = $v -> title;
				$data['list'][$k]['type'] = $v -> type;
				$data['list'][$k]['download_url'] = $v -> download_url;
				$data['list'][$k]['publish_to'] = $this->getPublicTo($v -> publish_to);
				$data['list'][$k]['create_time'] = $v -> create_time;
				$data['list'][$k]['last_time'] = $v -> last_time;
			}
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
			$result['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取资料发布到
	 * $publish_to(字符串)  资料发布到的标志（1 FX 2 GJ  1,2FX GJ）
	 */
	public function getPublicTo($publish_to)
	{
		$str = '';
		$array_public_to = explode(',', $publish_to);
		for($j=0;$j<count($array_public_to);$j++){
			$str = $str.$GLOBALS['PUBLIC_TO'][$array_public_to[$j]].',';
		}
		return substr($str, 0 ,strlen($str)-1);
	}
	
	/**
	 * 添加下载资料
	 * $title  标题
	 * $type   类型
	 * $download_url  下载链接
	 * $publish_to (数组)   发布到
	 */
	public function addDownLoad($title,$type,$download_url,$publish_to)
	{
		$result = array();
		$model = new Download();
		$model -> title = $title;
		$model -> type = $type;
		$model -> download_url = $download_url;
		$model -> admin_id = Yii::app() -> session['admin_id'];
		
		$str_publish_to = '';
		if(!empty($publish_to)){
			for($j=0;$j<count($publish_to);$j++){
				$str_publish_to = $str_publish_to .$publish_to[$j].',';
			}
		}
		$model -> publish_to = substr($str_publish_to,0,  strlen($str_publish_to)-1);
		
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 删除操作
	 * $download_id  下载表id
	 */
	public function delDownLoad($download_id)
	{
		$result = array();
		$model = Download::model()->findByPk($download_id);
		$model -> flag = FLAG_YES;
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 编辑下载资料
	 * $download_id  下载表id
	 * $title  标题
	 * $type   类型
	 * $download_url  下载链接
	 * $publish_to (数组)   发布到
	 */
	public function editDownLoad($download_id,$title,$type,$download_url,$publish_to)
	{
		$result = array();
		$model = Download::model()->findByPk($download_id);
		
		$model -> title = $title;
		$model -> type = $type;
		$model -> download_url = $download_url;
		
		$str_publish_to = '';
		if(!empty($publish_to)){
			for($j=0;$j<count($publish_to);$j++){
				$str_publish_to = $str_publish_to .$publish_to[$j].',';
			}
		}
		$model -> publish_to = substr($str_publish_to,0,  strlen($str_publish_to)-1);
		
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 获取$download_id的数据
	 * @param  $download_id   $download_id  下载表id
	 * @return 模型对象
	 */
	public function getData($download_id)
	{
		$model = Download::model()->findByPk($download_id);
		return $model;
	}
	
	/**
	 * 获取文档资料(视频)下载列表(分销平台/管家)
	 * $type  资料类型
	 * $fx_gj 分销平台/管家
	 */
	public function dataList($type,$fx_gj)
	{
		$result = array();
		
		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag = :flag and type=:type');
		$criteria -> params = array(':flag'=>FLAG_NO,':type'=>$type);
		$criteria -> order = 'create_time desc';
		
		$model = Download::model()->findAll($criteria);
		
		$data = array();
		
		if(!empty($model)){
			foreach ($model as $k=>$v){
				if($this->isInFx($v -> publish_to,$fx_gj)){
				$data[$k]['id'] = $v -> id;
				$data[$k]['title'] = $v -> title;
				$data[$k]['type'] = $v -> type;
				$data[$k]['admin_name'] = isset($v -> admin -> name)?$v -> admin -> name:'';
				$data[$k]['download_url'] = $v -> download_url;
				//$data[$k]['isInFx'] = $this->isInFx($v -> publish_to);
				$data[$k]['create_time'] = $v -> create_time;
				$data[$k]['last_time'] = $v -> last_time;
				}
			}
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
			$result['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 判断链接资料是否可以在分销平台/管家显示
	 * $publish_to(字符串)  资料发布到的标志（1 FX 2 GJ  1,2FX GJ）
	 */
	public function isInFx($publish_to,$fx_gj)
	{
		$array_public_to = explode(',', $publish_to);
		if(in_array($fx_gj, $array_public_to)){ //可以在分销显示
			return true;
		}else{
			return false;
		}
	}
}