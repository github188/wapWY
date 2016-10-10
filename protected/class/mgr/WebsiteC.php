<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 官网管理类
 * 2016/04/22
 * @author ly
 */

class WebsiteC extends mainClass
{
	public $page = null;
	private static $_instance = NULL;
	
	public static function getInstance()
	{
	    if (!self::$_instance instanceof self) {
	        self::$_instance = new self();
	    }
	    return self::$_instance;
	}
	
	/**
	 * 根据id,获取新闻分组信息
	 * @param unknown $id
	 */
    public function getNewsGroup($id){
        $result = array();
        try {
            if (empty($id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $newsGroup = NewsGroup::model()->findByPk($id);
            $data = array();
            if (!empty($newsGroup)) {
		        $data['id'] = $newsGroup -> id;
		        $data['name'] = $newsGroup -> name; //分组名
		        $data['rate'] = $newsGroup -> rate; //排序值
		        $data['create_time'] = $newsGroup -> create_time;
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
	/**
	 * 获取新闻分组列表
	 */	
	public function getNewsGroupList($keyword=NULL,$unpages=NULL,$web=NULL)
	{
		$result = array();
		
		$criteria = new CDbCriteria();
		//关键字查找
		if ($keyword !== '' && $keyword !== NULL){
		    $criteria -> addCondition('name like :name');
		    $criteria->params[':name'] = '%' . $keyword . '%';
		}
		$criteria -> addCondition('flag=:flag');
		$criteria->params[':flag'] = FLAG_NO;
		if (!empty($web)){
		    $criteria->order = 'rate DESC,create_time DESC';
		}else{
		    $criteria->order = 'create_time DESC';
		}
		//分页
		if (empty($unpages)){
		    $pages = new CPagination(NewsGroup::model()->count($criteria));
		    $pages -> pageSize = Yii::app()->params['perPage'];
		    $pages -> applyLimit($criteria);
		    $this->page = $pages;
		}

		
		$model = NewsGroup::model()->findAll($criteria);
		$data = array();
		if ($model){
		    foreach ($model as $k => $v){
		        $data[$k]['id'] = $v -> id;
		        $data[$k]['name'] = $v -> name; //分组名
		        $data[$k]['rate'] = $v -> rate; //排序值
		        $data[$k]['create_time'] = $v -> create_time;
		        $news = News::model() -> find('flag=:flag and news_group_id =:news_group_id',array(
		            ':news_group_id' => $v -> id,
		            ':flag' => FLAG_NO
		        ));
		        if(!empty($news)){
		            //不可删除
		            $data[$k]['if_del'] = 2;
		        }else{
		            //可删除
		            $data[$k]['if_del'] = 1;
		        }
		    }
		}
		$result ['status'] = ERROR_NONE;
		$result['errMsg'] = ''; //错误信息
		$result['data'] = $data;
		
		return json_encode($result);
	}
	
    /**
     * 添加新闻分组
     * @param unknown $name
     * @param string $rate
     */
	public function addNewsGroup($id=NULL,$name=NULL,$rate=NULL){
	    $result = array();
	    try {
	        $transaction = Yii::app()->db->beginTransaction();
	        if (!empty($id)){
	            $model = NewsGroup::model()->find('id = :id AND flag = :flag',array(
	                ':id' => $id,
	                ':flag' => FLAG_NO
	            ));
	            if (!empty($name)){
	                $model['name'] = $name; //分组名称
	            }
	            if (!empty($rate)){
	                $model['rate'] = $rate; //分组名称
	            }
	        }else{
	            $model = new NewsGroup();
	            $model['name'] = $name;
	            if (!empty($rate)){
	                $model['rate'] = $rate;
	            }
	            $model['create_time'] = date('Y-m-d H:i:s', time());
	            $model['last_time'] = date('Y-m-d H:i:s', time());
	        }
	        if ($model->save()) {
	            $transaction->commit();
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = array('id' => $model->id);
	        } else {
	            $result['status'] = ERROR_SAVE_FAIL;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
	
	/**
	 * 删除新闻分组
	 */
	public function deleteNewsGroup($id){
	    $result = array();
	    try {
	        //参数验证
	        //TODO
	        $model = NewsGroup::model()->findByPk($id);
	        if (empty($model)) {
	            $result['status'] = ERROR_NO_DATA;
	            throw new Exception('删除的数据不存在');
	        }
	        //修改删除标识
	        $model['flag'] = FLAG_YES;
	
	        if ($model->save()) {
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = '';
	        } else {
	            $result['status'] = ERROR_NONE;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
	
	/**
	 * 根据id,获取新闻信息
	 * @param unknown $id
	 */
	public function getNews($id){
	    $result = array();
	    try {
	        if (empty($id)) {
	            $result['status'] = ERROR_PARAMETER_FORMAT;
	            throw new Exception('参数错误');
	        }
	        $news = News::model()->findByPk($id);
	        $data = array();
	        if (!empty($news)) {
	            foreach ($news as $k=>$v){
	                $data[$k] = $v;
	            }
	        }
	        $result['status'] = ERROR_NONE;
	        $result['data'] = $data;
	        $result['errMsg'] = '';
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage(); //错误信息
	    }
	    return json_encode($result);
	}
	
	/**
	 * 获取新闻列表
	 */
	public function getNewsList($keyword=NULL,$status=NULL,$hot=NULL,$unpage=NULL,
	    $group_id=NULL,$web=NULL){
	    $result = array();
	    $criteria = new CDbCriteria();
	    //关键字查找
	    if ($keyword !== '' && $keyword !== NULL){
	        $criteria -> addCondition('title like :title');
	        $criteria->params[':title'] = '%' . $keyword . '%';
	    }
	    //是不是头条
	    if (!empty($hot)){
	        $criteria -> addCondition('if_hot=:if_hot');
	        $criteria->params[':if_hot'] = $hot;
	    }
	    //状态查找
	    if (!empty($status)){
	        $criteria -> addCondition('status=:status');
	        $criteria->params[':status'] = $status;
	    }
	    //状态查找
	    if (!empty($group_id)){
	        $criteria -> addCondition('news_group_id=:news_group_id');
	        $criteria->params[':news_group_id'] = $group_id;
	    }
	    $criteria -> addCondition('flag=:flag');
	    $criteria->params[':flag'] = FLAG_NO;
	    if (!empty($web)){
	        $criteria->order = 'hot_rate DESC,pv DESC,create_time DESC';
	    }else{
	        $criteria->order = 'create_time DESC';
	    }
	    //分页
	    if (empty($unpage)){
	        $pages = new CPagination(News::model()->count($criteria));
	        $pages -> pageSize = Yii::app()->params['perPage'];
	        $pages -> applyLimit($criteria);
	        $this->page = $pages;
	    }
	    $model = News::model()->findAll($criteria);
	    $data = array();
	    if ($model){
	        foreach ($model as $k => $v){
	            $data[$k]['id'] = $v -> id;
	            $data[$k]['if_hot'] = $v -> if_hot;
	            $news_group = NewsGroup::model() -> findByPk($v -> news_group_id);
	            $data[$k]['news_group_name'] = $news_group -> name;
	            $data[$k]['news_group_id'] = $v -> news_group_id;
	            $data[$k]['title'] = $v -> title;
	            $data[$k]['author'] = $v -> author;
	            $data[$k]['information_from'] = $v -> information_from;
// 	            $data[$k]['show_time'] = $v -> show_time;
	            $data[$k]['img'] = $v -> img;
	            $data[$k]['abstract'] = $v -> abstract;
	            $data[$k]['main_body'] = $v -> main_body;
	            $data[$k]['hot_rate'] = $v -> hot_rate;
	            $data[$k]['pv'] = $v -> pv;
	            $data[$k]['publish_time'] = $v -> publish_time;
// 	            $data[$k]['publisher'] = $v -> publisher;
	            $data[$k]['status'] = $v -> status;
	            $data[$k]['create_time'] = $v -> create_time;
	            $data[$k]['last_time'] = $v -> last_time;
	        }
	    }
	    $result ['status'] = ERROR_NONE;
	    $result['errMsg'] = ''; //错误信息
	    $result['data'] = $data;
	
	    return json_encode($result);
	}
	
	/**
	 * 获取所有新闻,不分页
	 */
	public function getNewsAll($hot=NULL){
	    $result = array();
	    $criteria = new CDbCriteria();
	    //是不是头条
	    if (!empty($hot)){
	        $criteria -> addCondition('if_hot=:if_hot');
	        $criteria->params[':if_hot'] = $hot;
	    }
	    $criteria -> addCondition('flag=:flag');
	    $criteria->params[':flag'] = FLAG_NO;
	    $criteria->order = 'create_time DESC';
	    $model = News::model()->findAll($criteria);
	    $data = array();
	    if ($model){
	        foreach ($model as $k => $v){
	            $data[$k]['id'] = $v -> id;
	            $data[$k]['if_hot'] = $v -> if_hot;
	            
	            
	            $data[$k]['news_group_id'] = $v -> news_group_id;
	            
	            $data[$k]['title'] = $v -> title;
	            $data[$k]['author'] = $v -> author;
	            $data[$k]['information_from'] = $v -> information_from;
	            // 	            $data[$k]['show_time'] = $v -> show_time;
	            $data[$k]['img'] = $v -> img;
	            $data[$k]['abstract'] = $v -> abstract;
	            $data[$k]['main_body'] = $v -> main_body;
	            $data[$k]['hot_rate'] = $v -> hot_rate;
	            $data[$k]['pv'] = $v -> pv;
	            $data[$k]['publish_time'] = $v -> publish_time;
	            // 	            $data[$k]['publisher'] = $v -> publisher;
	            $data[$k]['status'] = $v -> status;
	            $data[$k]['create_time'] = $v -> create_time;
	            $data[$k]['last_time'] = $v -> last_time;
	        }
	    }
	    $result ['status'] = ERROR_NONE;
	    $result['errMsg'] = ''; //错误信息
	    $result['data'] = $data;
	
	    return json_encode($result);
	}
	
	/**
	 * 添加及修改新闻
	 */
	public function addNews($id=NULL,$if_hot=NULL,$news_group_id=NULL,$title=NULL,$author=NULL,
	    $information_from=NULL,$img=NULL,$abstract=NULL,$main_body=NULL,$hot_rate=NULL,$pv=NULL,
	    $publish_time=NULL,$publisher=NULL,$status=NULL){
	    $result = array();
	    try {
	        $transaction = Yii::app()->db->beginTransaction();
	        if (!empty($id)){
	            
	            $model = News::model()->find('id = :id AND flag = :flag',array(
	                ':id' => $id,
	                ':flag' => FLAG_NO
	            ));
	            if (empty($model)){
	                throw new Exception('数据不存在');
	            }
	            if (!empty($if_hot)){
	                $model['if_hot'] = $if_hot;
	            }
	            if (!empty($news_group_id)){
	                $model['news_group_id'] = $news_group_id;
	            }
	            if (!empty($title)){
	                $model['title'] = $title;
	            }
	            if (!empty($author)){
	                $model['author'] = $author;
	            }
	            if (!empty($information_from)){
	                $model['information_from'] = $information_from;
	            }
	            if (!empty($img)){
	                $model['img'] = $img;
	            }
	            if (!empty($abstract)){
	                $model['abstract'] = $abstract;
	            }
	            if (!empty($main_body)){
	                $model['main_body'] = $main_body;
	            }
	            if (!empty($hot_rate)){
	                $model['hot_rate'] = $hot_rate;
	            }
	            if (!empty($pv)){
	                $model['pv'] = $pv;
	            }
	            
	            if (!empty($publisher)){
	                $model['publisher'] = $publisher;
	            }
	            if (!empty($status)){
	                $model['status'] = $status;
	                if ($status == 2){
	                   $model['publish_time'] = date('Y-m-d H:i:s', time());
	                }
	            }
	            if (!empty($publish_time)){
	                $model['publish_time'] = $publish_time;
	            }
	        }else{
	            //保存数据
	            $model = new News();
	            $model['if_hot'] = $if_hot;
	            $model['news_group_id'] = $news_group_id;
	            $model['title'] = $title;
	            $model['author'] = $author;
	            $model['information_from'] = $information_from;
	            $model['img'] = $img;
	            $model['abstract'] = $abstract;
	            $model['main_body'] = $main_body;
	            if (!empty($hot_rate)){
	                $model['hot_rate'] = $hot_rate;
	            }
	            if (!empty($hot_rate)){
	                $model['pv'] = $pv;
	            }
	            if (!empty($publisher)){
	                $model['publisher'] = $publisher;
	            }
	            if (!empty($status)){
	                $model['status'] = $status;
	               if ($status == 2){
	                   $model['publish_time'] = date('Y-m-d H:i:s', time());
	                }
	            }
	            if (!empty($publish_time)){
	                $model['publish_time'] = $publish_time;
	            }
	            $model['create_time'] = date('Y-m-d H:i:s', time());
	            $model['last_time'] = date('Y-m-d H:i:s', time());
	        }
	        if ($model->save()) {
	            $transaction->commit();
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = array('id' => $model->id);
	        } else {
	            $result['status'] = ERROR_SAVE_FAIL;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}

	/**
	 * 发布新闻
	 */
	public function releaseNews($id,$status=NULL){
		$result = array();
		$model = News::model()->findByPk($id);
		if(($model -> status == NEWS_STATUS_PUBLISHED) && empty($status)){ //文章如果是已发布
			$model -> status = NEWS_STATUS_CANCELPUBLISHED;
		}elseif($status == 3){
		    $model -> status = NEWS_STATUS_CANCELPUBLISHED;
		}else{//文章如果是未发布或者取消发布
		    $model -> status = NEWS_STATUS_PUBLISHED;
		    $model -> publish_time = date('Y-m-d H:i:s');
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
	 * 删除新闻
	 */
	public function deleteNews($id){
	    $result = array();
	    try {
	        //参数验证
	        //TODO
	        $model = News::model()->findByPk($id);
	        if (empty($model)) {
	            $result['status'] = ERROR_NO_DATA;
	            throw new Exception('删除的数据不存在');
	        }
	        //修改删除标识
	        $model['flag'] = FLAG_YES;
	        if ($model->save()) {
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = '';
	        } else {
	            $result['status'] = ERROR_NONE;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
	
	/**
	 * 添加商家案例分组
	 * @param unknown $name
	 * @param string $rate
	 */
	public function addMcaseGroup($id=NULL,$name=NULL,$rate=NULL){
	    $result = array();
	    try {
	        $transaction = Yii::app()->db->beginTransaction();
	        if (!empty($id)){
	            $model = McaseGroup::model()->find('id = :id AND flag = :flag',array(
	                ':id' => $id,
	                ':flag' => FLAG_NO
	            ));
	            if (!empty($name)){
	                $model['name'] = $name; //分组名称
	            }
	            if (!empty($rate)){
	                $model['rate'] = $rate; //排序值
	            }
	        }else{
	            $model = new McaseGroup();
	            $model['name'] = $name;
	            if (!empty($rate)){
	                $model['rate'] = $rate;
	            }
	            $model['create_time'] = date('Y-m-d H:i:s', time());
	            $model['last_time'] = date('Y-m-d H:i:s', time());
	        }
	        if ($model->save()) {
	            $transaction->commit();
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = array('id' => $model->id);
	        } else {
	            $result['status'] = ERROR_SAVE_FAIL;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
	
	/**
	 * 根据id,获取商家案例分组信息
	 * @param unknown $id
	 */
	public function getMcaseGroup($id){
	    $result = array();
	    try {
	        if (empty($id)) {
	            $result['status'] = ERROR_PARAMETER_FORMAT;
	            throw new Exception('参数错误');
	        }
	        $model = McaseGroup::model()->findByPk($id);
	        $data = array();
	        if (!empty($model)) {
	            $data['id'] = $model -> id;
	            $data['name'] = $model -> name; //分组名
	            $data['rate'] = $model -> rate; //排序值
	            $data['create_time'] = $model -> create_time;
	        }
	        $result['status'] = ERROR_NONE;
	        $result['data'] = $data;
	        $result['errMsg'] = '';
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage(); //错误信息
	    }
	    return json_encode($result);
	}
	
	/**
	 * 获取商家案例分组列表
	 */
	public function getMcaseGroupList($keyword=NULL,$unpages=NULL,$web=NULL){
	    $result = array();
	
	    $criteria = new CDbCriteria();
	    //关键字查找
	    if ($keyword !== '' && $keyword !== NULL){
	        $criteria -> addCondition('name like :name');
	        $criteria->params[':name'] = '%' . $keyword . '%';
	    }
	    $criteria -> addCondition('flag=:flag');
	    $criteria->params[':flag'] = FLAG_NO;
	    if (!empty($web)){
	        $criteria->order = 'rate DESC,create_time DESC';
	    }else{
	        $criteria->order = 'create_time DESC';
	    }
	    //分页
	    if (empty($unpages)){
    	    $pages = new CPagination(McaseGroup::model()->count($criteria));
    	    $pages -> pageSize = Yii::app()->params['perPage'];
    	    $pages -> applyLimit($criteria);
    	    $this->page = $pages;
	    }
	    $model = McaseGroup::model()->findAll($criteria);
	    $data = array();
	    if ($model){
	        foreach ($model as $k => $v){
	            $data[$k]['id'] = $v -> id;
	            $data[$k]['name'] = $v -> name; //分组名
	            $data[$k]['rate'] = $v -> rate; //排序值
	            $data[$k]['create_time'] = $v -> create_time;
	            $mcase = Mcase::model() -> find('flag=:flag and mcase_group_id =:mcase_group_id',array(
	                ':mcase_group_id' => $v -> id,
	                ':flag' => FLAG_NO
	            ));
	            if(!empty($mcase)){
	                //不可删除
	                $data[$k]['if_del'] = 2;
	            }else{
	                //可删除
	                $data[$k]['if_del'] = 1;
	            }
	        }
	    }
	    $result ['status'] = ERROR_NONE;
	    $result['errMsg'] = ''; //错误信息
	    $result['data'] = $data;
	
	    return json_encode($result);
	}
	
	/**
	 * 删除商家案例分组
	 */
	public function deleteMcaseGroup($id){
	    $result = array();
	    try {
	        //参数验证
	        //TODO
	        $model = McaseGroup::model()->findByPk($id);
	        if (empty($model)) {
	            $result['status'] = ERROR_NO_DATA;
	            throw new Exception('删除的数据不存在');
	        }
	        //修改删除标识
	        $model['flag'] = FLAG_YES;
	
	        if ($model->save()) {
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = '';
	        } else {
	            $result['status'] = ERROR_NONE;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
	
	
	/**
	 * 根据id,获取商家案例信息
	 * @param unknown $id
	 */
	public function getMcase($id){
	    $result = array();
	    try {
	        if (empty($id)) {
	            $result['status'] = ERROR_PARAMETER_FORMAT;
	            throw new Exception('参数错误');
	        }
	        $model = Mcase::model()->findByPk($id);
	        $data = array();
	        if (!empty($model)) {
	            foreach ($model as $k=>$v){
	                $data[$k] = $v;
	            }
	        }
	        $result['status'] = ERROR_NONE;
	        $result['data'] = $data;
	        $result['errMsg'] = '';
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage(); //错误信息
	    }
	    return json_encode($result);
	}
	
	/**
	 * 获取商家案例列表
	 */
	public function getMcaseList($keyword=NULL,$status=NULL,$unpage=NULL,$group=NULL,$web=NULL){
	    $result = array();
	    $criteria = new CDbCriteria();
	    //关键字查找
	    if ($keyword !== '' && $keyword !== NULL){
	        $criteria -> addCondition('merchant_name like :merchant_name');
	        $criteria->params[':merchant_name'] = '%' . $keyword . '%';
	    }
	    //状态查找
	    if (!empty($status)){
	        $criteria -> addCondition('status=:status');
	        $criteria->params[':status'] = $status;
	    }
	    //分组查找
	    if (!empty($group)){
	        $criteria -> addCondition('mcase_group_id=:mcase_group_id');
	        $criteria->params[':mcase_group_id'] = $group;
	    }
	    $criteria -> addCondition('flag=:flag');
	    $criteria->params[':flag'] = FLAG_NO;
	    if (!empty($web)){
	        $criteria->order = 'rate DESC,create_time DESC';
	    }else{
	        $criteria->order = 'create_time DESC';
	    }
	    //是否分页
	    if (empty($unpage)){
	        $pages = new CPagination(Mcase::model()->count($criteria));
	        $pages -> pageSize = Yii::app()->params['perPage'];
	        $pages -> applyLimit($criteria);
	        $this->page = $pages;
	    }
	    $model = Mcase::model()->findAll($criteria);
	    $data = array();
	    if ($model){
	        foreach ($model as $k => $v){
	            $data[$k]['id'] = $v -> id;
	            $data[$k]['mcase_group_id'] = $v -> mcase_group_id;
	            $mcase_group = McaseGroup::model() -> findByPk($v -> mcase_group_id);
	            $data[$k]['mcase_group_name'] = $mcase_group -> name;
	            $data[$k]['merchant_name'] = $v -> merchant_name;
	            $data[$k]['logo'] = $v -> logo;
	            $data[$k]['qrcode'] = $v -> qrcode;
	            $data[$k]['content'] = $v -> content;
	            $data[$k]['img'] = $v -> img;
	            $data[$k]['publish_time'] = $v -> publish_time;
	            $data[$k]['rate'] = $v -> rate;
	            $data[$k]['status'] = $v -> status;
	            $data[$k]['create_time'] = $v -> create_time;
	            $data[$k]['last_time'] = $v -> last_time;
	        }
	    }
	    $result ['status'] = ERROR_NONE;
	    $result['errMsg'] = ''; //错误信息
	    $result['data'] = $data;
	    return json_encode($result);
	}
	
	/**
	 * 添加及修改商家案例
	 */
	public function addMcase($id=NULL,$mcase_group_id,$merchant_name,$logo,$qrcode,$content,$img,$rate=NULL,$status=NULL){
	    $result = array();
	    try {
	        $transaction = Yii::app()->db->beginTransaction();
	        if (!empty($id)){
	             
	            $model = Mcase::model()->find('id = :id AND flag = :flag',array(
	                ':id' => $id,
	                ':flag' => FLAG_NO
	            ));
	            if (empty($model)){
	                throw new Exception('数据不存在');
	            }
	            if (!empty($mcase_group_id)){
	                $model['mcase_group_id'] = $mcase_group_id;
	            }
	            if (!empty($merchant_name)){
	                $model['merchant_name'] = $merchant_name;
	            }
	            if (!empty($logo)){
	                $model['logo'] = $logo;
	            }
	            if (!empty($qrcode)){
	                $model['qrcode'] = $qrcode;
	            }
	            if (!empty($content)){
	                $model['content'] = $content;
	            }
	            if (!empty($img)){
	                $model['img'] = $img;
	            }
	            if (!empty($rate)){
	                $model['rate'] = $rate;
	            }
	            if (!empty($status)){
	                $model['status'] = $status;
	                if ($status == 2){
	                    $model['publish_time'] = date('Y-m-d H:i:s', time());
	                }
	            }
	        }else{
	            //保存数据
	            $model = new Mcase();
	            $model['mcase_group_id'] = $mcase_group_id;
	            $model['merchant_name'] = $merchant_name;
	            $model['logo'] = $logo;
	            $model['qrcode'] = $qrcode;
	            $model['content'] = $content;
	            $model['img'] = $img;
	            if (!empty($status)){
	                $model['status'] = $status;
	                if ($status == 2){
	                    $model['publish_time'] = date('Y-m-d H:i:s', time());
	                }
	            }
	            $model['create_time'] = date('Y-m-d H:i:s', time());
	            $model['last_time'] = date('Y-m-d H:i:s', time());
	        }
	
	        if ($model->save()) {
	            $transaction->commit();
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = array('id' => $model->id);
	        } else {
	            $result['status'] = ERROR_SAVE_FAIL;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
	
	/**
	 * 发布商家案例
	 */
	public function releaseMcase($id,$status=NULL){
	    $result = array();
	    $model = Mcase::model()->findByPk($id);
	    if(($model -> status == NEWS_STATUS_PUBLISHED) && empty($status)){ //文章如果是已发布
	        $model -> status = NEWS_STATUS_CANCELPUBLISHED;
	    }elseif($status == 3){
	        $model -> status = NEWS_STATUS_CANCELPUBLISHED;
	    }else{//文章如果是未发布或者取消发布
	        $model -> status = NEWS_STATUS_PUBLISHED;
	        $model -> publish_time = date('Y-m-d H:i:s');
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
	 * 删除商家案例
	 */
	public function deleteMcase($id){
	    $result = array();
	    try {
	        $model = Mcase::model()->findByPk($id);
	        if (empty($model)) {
	            $result['status'] = ERROR_NO_DATA;
	            throw new Exception('删除的数据不存在');
	        }
	        //修改删除标识
	        $model['flag'] = FLAG_YES;
	        if ($model->save()) {
	            $result['status'] = ERROR_NONE; //状态码
	            $result['errMsg'] = ''; //错误信息
	            $result['data'] = '';
	        } else {
	            $result['status'] = ERROR_NONE;
	            $result['errMsg'] = '数据保存失败';
	            $result['data'] = '';
	        }
	    } catch (Exception $e) {
	        $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
	        $result['errMsg'] = $e->getMessage();
	    }
	    return json_encode($result);
	}
}