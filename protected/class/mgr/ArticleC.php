<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 文章管理类
 * 2015/09/02
 * @author xyf
 */

class ArticleC extends mainClass
{
	public $page = null;
	/**
	 * 获取文章列表
	 * $title 文章标题
	 * $time  文章创建时间段
	 */	
	public function getArticleList($title , $time)
	{
		$result = array();
		
		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag=:flag');
		$criteria -> params = array(':flag'=>FLAG_NO);
		
		//搜索
		if(!empty($title)){
			$criteria -> addCondition('title=:title');
			$criteria -> params[':title'] = $title;
		}
		if(!empty($time)){
			$Time = array();
			$Time = explode('-', $time);
			$criteria -> addBetweenCondition('create_time', $Time[0].' 00:00:00', $Time[1].' 23:59:59');
		}
		
		//分页
		$pages = new CPagination(Article::model()->count($criteria));
		$pages -> pageSize = Yii::app()->params['perPage'];
		$pages -> applyLimit($criteria);
		$this -> page = $pages;
		
		$model = Article::model()->findAll($criteria);
		
		$data = array();
		foreach ($model as $k => $v){
			$data['list'][$k]['id'] = $v -> id;
			$data['list'][$k]['title'] = $v -> title; //标题
			$data['list'][$k]['type'] = $v -> type; //类型
			$data['list'][$k]['content'] = $v -> content; //内容
			$data['list'][$k]['status'] = $v -> status; //发布状态
			$data['list'][$k]['release_time'] = $v -> release_time; //发布时间
			$data['list'][$k]['create_time'] = $v -> create_time;
			$data['list'][$k]['last_time'] = $v -> last_time;
			$data['list'][$k]['admin_name'] = isset($v -> admin->name)?$v -> admin->name:''; //管理员名字
		}
		
		$result ['status'] = ERROR_NONE;
		$result['errMsg'] = ''; //错误信息
		$result['data'] = $data;
		
		return json_encode($result);
	}
	
	/**
	 * 添加文章
	 * $title  文章标题
	 * $type   文章类型
	 * $content  文章内容
	 */
	public function addArticle($title,$type,$content)
	{
		$result = array();
		$model = new Article();
		$model -> title = $title;
		$model -> type = $type;
		$model -> content = $content;
		
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
	 * 删除文章
	 * $article_id  文章id
	 */
	public function delArticle($article_id)
	{
		$result = array();
		$model = Article::model()->findByPk($article_id);
		if($model -> flag == FLAG_NO){
			$model -> flag = FLAG_YES;
			if($model -> save()){
				$result ['status'] = ERROR_NONE;
				$result ['errMsg'] = '';
			}else{
				$result ['status'] = ERROR_SAVE_FAIL; // 状态码
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		}
		
		return json_encode($result);
	}
	
	/**
	 * 发布文章
	 * $article_id  文章id
	 */
	public function releaseArticle($article_id)
	{
		$result = array();
		$model = Article::model()->findByPk($article_id);
		
		if($model -> status == RELEASE_STATUS_NO){ //文章如果是未发布
			$model -> status = RELEASE_STATUS_YES;
			$model -> admin_id = Yii::app()->session['admin_id']; //存入发布人
			$model -> release_time = date('Y-m-d H:i:s');
		}else{ //文章如果是已发布
			$model -> status = RELEASE_STATUS_NO;
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
	
	/**
	 * 编辑文章
	 * $article_id  文章id
	 * $title  文章标题
	 * $type   文章类型
	 * $content  文章内容
	 */
	public function editArticle($article_id,$title,$type,$content)
	{
		$result = array();
		$model = Article::model()->findByPk($article_id);
		
		$model -> title = $title;
		$model -> type = $type;
		$model -> content = $content;
		
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
	 * 根据文章id获取数据对象
	 * $article_id  文章id
	 */
	public function getModel($article_id)
	{
		$model = Article::model()->findByPk($article_id);
		return $model;
	}
	
}