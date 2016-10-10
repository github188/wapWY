<?php
//相册类
include_once(dirname(__FILE__).'/../mainClass.php');
class AlbumC extends mainClass
{
    //相册名管理
        /**
         * merchantId  商户id
         */
        public function PhotoManagement($merchantId)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $criteria = new CDbCriteria();
                $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria->params[':merchant_id'] = $merchantId;
                $criteria->params[':flag'] = FLAG_NO;
                $album    = Album::model()->findall($criteria);
                if($album)
                {
                    $data = array();
                    foreach($album as $k => $v)
                    {
                        $data[$k]['id']   = $v['id'];
                        $data[$k]['name'] = $v['name'];
                        $albumgroup = AlbumGroup::model()->findall('merchant_id=:merchant_id and album_id=:album_id',array(':album_id'=>$v['id'],':merchant_id'=>$merchantId));
                        $sum = 0;
                        if($albumgroup)
                        {                            
                            foreach ($albumgroup as $e => $l) 
                            {
                                $sum = $sum + AlbumImg::model()->count('album_group_id=:album_group_id',array(':album_group_id'=>$l['id']));
                                $criteria1 = new CDbCriteria();
                                $criteria1->addCondition('album_group_id=:album_group_id and flag=:flag');
                                $criteria1->params[':album_group_id'] = $l['id'];
                                $criteria1->params[':flag'] = FLAG_NO;
                                $count        = AlbumImg::model() -> count($criteria1);
                                $pages        = new CPagination($count);
                                $pages       -> pageSize = 1;
                                $pages       -> applyLimit($criteria1);
                                $imgs = AlbumImg::model()->findall($criteria1);
                                if($imgs)
                                {
                                    foreach ($imgs as $a => $b) 
                                    {
                                        $data[$k]['img'] = $b['img'];
                                    }
                                }
                            }
                        }
                        $data[$k]['sum'] = $sum;
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
        
        //修改相册名
        /**
         * id 相册id
         * name 相册名
         * merchantId  商户id
         */
        public function EditPhotoManagement($id,$name,$merchantId)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if(isset($name) && empty($name))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数name缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $album = Album::model()->findByPk($id);
                $album->name      = $name;
                $album->last_time = new CDbExpression('now()');
                if($album->update())
                {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '修改失败';
                }
            }
            return json_encode($result);
        }
        
        //创建分组
        /**
         * merchantId 商户id
         * id        相册管理id
         * name     分组名称
         */
        public function AddGroup($merchantId,$id,$name)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if(isset($name) && empty($name))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数name缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $albumgroup = new AlbumGroup();
                $albumgroup->name        = $name;
                $albumgroup->album_id    = $id;
                $albumgroup->merchant_id = $merchantId;
                $albumgroup->create_time = new CDbExpression('now()');
                $albumgroup->flag        = FLAG_NO;
                if($albumgroup->save())
                {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '保存失败';
                }
            }
            return json_encode($result);
        }
        
        //分组列表
        /**
         * merchantId  商户id
         * id      分组id
         */
        public function PhotoSubclass($merchantId,$id)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null','datas'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
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
                $album = Album::model()->find('merchant_id=:merchant_id and flag=:flag and id=:id',array(':merchant_id'=>$merchantId,':flag'=>FLAG_NO,':id'=>$id));
                $result['datas']  = $album->name;
                $criteria = new CDbCriteria();
                $criteria->addCondition('merchant_id=:merchant_id and album_id = :album_id and flag=:flag');
                $criteria->params[':merchant_id'] = $merchantId;
                $criteria->params[':album_id']    = $id;
                $criteria->params[':flag']        = FLAG_NO;
                $albumgroup = AlbumGroup::model()->findall($criteria);
                if($albumgroup)
                {
                    $data = array();
                    foreach ($albumgroup as $key => $value) 
                    {
                        $data[$key]['name'] = $value['name'];
                        $data[$key]['id']   = $value['id'];
                        $albumimg = AlbumImg::model()->count('album_group_id=:album_group_id and flag=:flag',array(':album_group_id'=>$value['id'],':flag'=>FLAG_NO));
                        $data[$key]['sum']  = isset($albumimg) ? $albumimg : '0';
                        $criteria1 = new CDbCriteria();
                        $criteria1->addCondition('album_group_id=:album_group_id and flag=:flag');
                        $criteria1->params[':album_group_id'] = $value['id'];
                        $criteria1->params[':flag'] = FLAG_NO;
                        $count        = AlbumImg::model() -> count($criteria1);
                        $pages        = new CPagination($count);
                        $pages       -> pageSize = 1;
                        $pages       -> applyLimit($criteria1);
                        $imgs = AlbumImg::model()->findall($criteria1);
                        if($imgs)
                        {
                            foreach ($imgs as $a => $b) 
                            {
                                $data[$key]['img'] = $b['img'];
                            }
                        }
                    } 
                    $result['status'] = ERROR_NONE;
                    $result['data']   = $data;
                }  else {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg'] = '无此数据';
                }
            }
            return json_encode($result);
        }
        
        //修改分组
        /**
         * merchantId   商户id
         * id     分组id
         * name    分组名称
         */
        public function EditGroup($merchantId,$id,$name)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if(isset($name) && empty($name))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数name缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $albumgroup = AlbumGroup::model()->findByPk($id);
                $albumgroup->name        = $name;
                $albumgroup->last_time   = new CDbExpression('now()');                
                if($albumgroup->update())
                {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '修改失败';
                }
            }
            return json_encode($result);
        }
        
        //删除分组
        /**
         * merchantId  商户id
         * id       分组id
         */
        public function DelGroup($merchantId,$id)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
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
                $albumgroup = AlbumGroup::model()->findByPk($id);
                if($albumgroup->delete())
                {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '删除失败';
                }
            }
            return json_encode($result);
        }

        //添加图片的下拉分组
        /**
         * merchantId  商户id
         * albumid    分组id
         */
        public function PhotoGroup($merchantId,$albumId)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($merchantId) && empty($merchantId))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数merchantId缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $albumgroup = AlbumGroup::model()->findall('merchant_id=:merchant_id and album_id=:album_id',array(':merchant_id'=>$merchantId,':album_id'=>$albumId));
                if($albumgroup)
                {
                    $data = array();
                    foreach ($albumgroup as $key => $value) 
                    {
                        $data[$key]['name']     = $value['name'];
                        $data[$key]['id']       = $value['id'];
                        $data[$key]['album_id'] = $value['album_id'];
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

        //添加相册图片
        /**
         * albumgroupid   分组id
         * img     图片地址
         */
        public function AddPhoto($albumgroupid,$img,$name)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($albumgroupid) && empty($albumgroupid))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数albumgroupid缺失';
                $flag = 1;
            }
            if(isset($img) && empty($img))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数img缺失';
                $flag = 1;
            }
            if(isset($name) && empty($name))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数name缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $img = explode(';', $img);
                $name = explode(';', $name);
                $num = count($img);
                for($i=0;$i<($num-1);$i++)
                {
                    $albumimg = new AlbumImg();
                    $albumimg->album_group_id = $albumgroupid;
                    $albumimg->img = $img[$i];
                    $albumimg->name = $name[$i];
                    $albumimg->create_time = new CDbExpression('now()');
                    $albumimg->save();
                }                 
                $data['albumgroupid'] = $albumgroupid;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            }     
            return json_encode($result);
        }
        
        //分组图片列表
        /**
         * albumgroupid  分组id
         */
        public function GroupPhotoList($albumgroupid)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(!isset($albumgroupid) && empty($albumgroupid))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数albumgroupid缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $albumimg = AlbumImg::model()->findAll('album_group_id=:album_group_id',array(':album_group_id'=>$albumgroupid));
                if(isset($albumimg))
                {
                    $data = array();
                    foreach ($albumimg as $key => $value) 
                    {
                        $data[$key]['id'] = $value['id'];
                        $data[$key]['img'] = $value['img'];
                        $data[$key]['name'] = $value['name'];
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
        
        //删除分组内图片
        /**
         * id   分组内图片id
         */
        public function DelGroupPhoto($id)
        {
            $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
            $flag = 0;
            if(isset($id) && empty($id))
            {  
                $result['status'] = ERROR_PARAMETER_MISS;
                $result['errMsg'] = '参数id缺失';
                $flag = 1;
            }
            if($flag == 0)
            {
                $groupimg = AlbumImg::model()->findByPk($id);
                if($groupimg)
                {
                    if($groupimg->delete())
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
        
        //创建默认相册
        /*
         * $merchantId 商户id
         * */
        public function createAlbum($merchantId){
        	$album = Album::model() -> findAll('merchant_id =:merchant_id and flag =:flag',array(
        			':merchant_id' => $merchantId,
        			':flag' => FLAG_NO
        	));
        	if(empty($album)){
        		$flag = 0;
        		$album1 = new Album();
        		$album1 -> merchant_id = $merchantId;
        		$album1 -> name = '菜品';
        		$album1 -> create_time = new CDbExpression('now()');
        		if(!$album1 -> save()){
        			$flag = 1;
        		}
        		$album2 = new Album();
        		$album2 -> merchant_id = $merchantId;
        		$album2 -> name = '环境';
        		$album2 -> create_time = new CDbExpression('now()');
        		if(!$album2 -> save()){
        			$flag = 1;
        		}
        		$album3 = new Album();
        		$album3 -> merchant_id = $merchantId;
        		$album3 -> name = '其他';
        		$album3 -> create_time = new CDbExpression('now()');
        		if(!$album3 -> save()){
        			$flag = 1;
        		}
        		$album4 = new Album();
        		$album4 -> merchant_id = $merchantId;
        		$album4 -> name = '全部';
        		$album4 -> create_time = new CDbExpression('now()');
        		if(!$album4 -> save()){
        			$flag = 1;
        		}
        		if($flag == 0){
        			$result['status'] = ERROR_NONE;
        			return json_encode($result);
        		}
        	}
        }
        
        
        /*在线商铺 - 获取相册
         * $merchant_id 商户id
         * */
        public function getAlbumList($merchant_id){
        	$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
        	if(!isset($merchant_id) || empty($merchant_id))
        	{
        		$result['status'] = ERROR_PARAMETER_MISS;
        		$result['errMsg'] = '参数merchant_id缺失';
        		return json_encode($result);
        	}
        	
        	$album = Album::model() -> findAll('merchant_id =:merchant_id and flag=:flag',array(
        			':merchant_id' => $merchant_id,
        			':flag' => FLAG_NO
        	));
        	$list = array();
        	foreach ($album as $k => $v){
        		$list[$k]['id'] = $v -> id;
       			$list[$k]['merchant_id'] = $v -> merchant_id;
       			$list[$k]['name'] = $v -> name;
       		}
       		$result['data'] = $list;
       		$result['status'] = ERROR_NONE;
       		return json_encode($result);
        }
        
        /*在线商铺 - 获取相册分组
         * $album_id 相册id
         * $merchant_id 商户id
        * */
		public function getAlbumGroupList($album_id,$merchant_id){
			$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
			if(empty($album_id))
			{
				$albumgroup = AlbumGroup::model() -> findAll('flag=:flag and merchant_id =:merchant_id',array(
						':flag' => FLAG_NO,
						':merchant_id' => $merchant_id
				));
			}else{
				$albumgroup = AlbumGroup::model() -> findAll('album_id =:album_id and flag=:flag and merchant_id =:merchant_id',array(
						':album_id' => $album_id,
						':flag' => FLAG_NO,
						':merchant_id' => $merchant_id
				));
			}
			
			
			$list = array();
			foreach ($albumgroup as $k => $v){
				$albumimg = AlbumImg::model() -> findAll('album_group_id =:album_group_id and flag =:flag',array(
						':album_group_id' => $v -> id,
						':flag' => FLAG_NO
				));
					
				$list[$k]['id'] = $v -> id;
				$list[$k]['num'] = count($albumimg);
				if($list[$k]['num'] > 0){
					$list[$k]['img'] = $albumimg['0'] -> img;
				}
				$list[$k]['merchant_id'] = $v -> merchant_id;
				$list[$k]['name'] = $v -> name;
			}
			$result['data'] = $list;
			$result['status'] = ERROR_NONE;
			return json_encode($result);
			
		}
		
		/*获取分组图片列表
		 * $album_group_id 分组id
		 * */
		public function getAlbumImgList($album_group_id){
			$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
			$flag = 0;
			if(!isset($album_group_id) || empty($album_group_id))
			{
				$result['status'] = ERROR_PARAMETER_MISS;
				$result['errMsg'] = '参数album_group_id缺失';
				return json_encode($result);
			}
			$album_img = AlbumImg::model() -> findAll('album_group_id =:album_group_id and flag=:flag',array(
					':album_group_id' => $album_group_id,
					':flag' => FLAG_NO
			));
			$list = array();
			foreach ($album_img as $k => $v){
				$list[$k]['id'] = $v -> id;
				$list[$k]['name'] = $v -> name;
				$list[$k]['img'] = $v -> img;
			}
			$result['data'] = $list;
			$result['status'] = ERROR_NONE;
			return json_encode($result);
		}


    /**
     * 点击全选删除所有的图片和子相册
     */
    public function delAlbumGroup($merchantId,$album_id)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and album_id = :album_id and flag=:flag');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->params[':album_id']    = $album_id;
            $criteria->params[':flag']        = FLAG_NO;
            $model=AlbumGroup::model()->find($criteria);
            if(!empty($model))
            {
                foreach($model as $key=>$value)
                {
                    $value['flag']=FLAG_YES;
                }
                if($model -> update()){
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }else{
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            }else
            {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
            }

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    public function getAlbumId($albumgroupid,$merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('id=:id and merchant_id = :merchant_id and flag=:flag');
            $criteria->params[':id'] = $albumgroupid;
            $criteria->params[':merchant_id']    = $merchantId;
            $criteria->params[':flag']        = FLAG_NO;
            $model=AlbumGroup::model()->find($criteria);
            if(!empty($model))
            {
                $albumid=$model->album_id;
                $result['data']=$albumid;
                $result['albumgroup_name']=$model->name;
                $model_album=Album::model()->findByPk($albumid);
                $result['album_name']=$model_album->name;
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '无此数据'; //错误信息
            }else
            {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
            }

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 相册添加图片那里的下拉列表数据查询
     * @param $merchantId
     * @param $albumid
     */
    public function getAlbumGroup($merchantId,$albumid,$albumgroupid)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and album_id = :album_id and flag=:flag');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->params[':album_id']    = $albumid;
            $criteria->params[':flag']        = FLAG_NO;
            $model=AlbumGroup::model()->findAll($criteria);
            $data=array();
            if(!empty($model))
            {
                foreach($model as $key =>$value)
                {
                    if($value['id']!=$albumgroupid)
                    {
                        //ID不等于本身就添加到数组
                        $data[$value['id']]=$value['name'];
                    }
                }
                $result ['data']=$data;
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            }else
            {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
            }

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 移动图片
     */
    public function movePhoto($merchantId,$this_id,$arr_id,$albumgroup_id)
    {
        $transaction = Yii::app()->db->beginTransaction();
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //首先添加图片DelGroupPhoto
            for($i=0;$i<count($arr_id);$i++)
            {
                $id=$arr_id[$i];
                if(!($this->AddGroupPhoto($id,$albumgroup_id)))
                {
                    $transaction->rollback(); //数据回滚
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('图片移动失败，请重新移动');
                }
            }

            //首先删除图片
            for($i=0;$i<count($arr_id);$i++)
            {
                $id=$arr_id[$i];
                $r=json_decode($this->DelGroupPhoto($id),true);
                if($r['status']!=ERROR_NONE)
                {
                    $transaction->rollback(); //数据回滚
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('图片移动失败，请重新移动');
                }
            }
            $transaction->commit(); //数据提交
            $result ['status'] = ERROR_NONE;
            $result['errMsg'] = ''; //错误信息

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    public function AddGroupPhoto($id,$albumgroup_id)
    {

        $model=AlbumImg::model()->findByPk($id);
        $album_img=new AlbumImg();
        $album_img->album_group_id=$albumgroup_id;
        $album_img->img=$model->img;
        $album_img->name=$model->name;
        $album_img->create_time=date('Y-m-d H:i:s', time());
        if($album_img->save())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
