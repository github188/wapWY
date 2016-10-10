<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 商城首页
 */

class MallHomeC extends mainClass{
	
	public $page = null;
	
	/**
	 * 获取分组信息
	 * @param  $merchant_id  商户id
	 * $group_name           分组名称
	 */
	public function getShopGroupList($merchant_id,$group_name,$pagesize='')
	{
		$result = array ();
		
		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag=:flag and merchant_id=:merchant_id');
		$criteria -> params = array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id);
		
		if(!empty($group_name)){
//			$criteria -> addCondition('name = :name');
//			$criteria -> params[':name'] = $group_name;
            $criteria->compare('name',$group_name,true);
		}
		
		//分页
		$pages = new CPagination(ShopGroup::model()-> count($criteria));
		$pages -> pageSize = 5;
		if(empty($pagesize)){
			$pages -> pageSize = Yii::app()->params['perPage'];
		}else{
			$pages -> pageSize = $pagesize;
		}
		$pages -> applyLimit($criteria);
		$this->page = $pages;
		
		$model = ShopGroup::model()-> findAll($criteria);
		$data = array();
		if (! empty ( $model )) {
			foreach ( $model as $k => $v ) {
				$data ['list'] [$k] ['id'] = $v ['id'];
				$data ['list'] [$k] ['merchant_id'] = $v ['merchant_id'];
				$data ['list'] [$k] ['name'] = $v ['name'];
				$data ['list'] [$k] ['content'] = $v ['content']; 
				$data ['list'] [$k] ['create_time'] = $v ['create_time'];
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
	 *获取商品信息 
	 * * @param  $merchant_id  商户id
	 * $group_name           分组名称
	 */
	public function getShopProductList($merchant_id,$group_name,$pagesize = '')
	{
		$result = array ();
		
		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag=:flag and merchant_id=:merchant_id');
		$criteria -> params = array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id);
		
		if(!empty($group_name)){
			$criteria -> addCondition('name = :name');
			$criteria -> params[':name'] = $group_name;
		}
		
		//分页
		$pages = new CPagination(ShopProduct::model()-> count($criteria));
		$pages -> pageSize = 5;
		if(empty($pagesize)){
			$pages -> pageSize = Yii::app()->params['perPage'];
		}else{
			$pages -> pageSize = $pagesize;
		}
		$pages -> applyLimit($criteria);
		$this->page = $pages;
		
		$model = ShopProduct::model()-> findAll($criteria);
		$data = array();
		if (! empty ( $model )) {
			foreach ( $model as $k => $v ) {
				$data ['list'] [$k] ['id'] = $v ['id'];
				$data ['list'] [$k] ['merchant_id'] = $v ['merchant_id'];
				$data ['list'] [$k] ['name'] = $v ['name'];
				$data ['list'] [$k] ['create_time'] = $v ['create_time'];
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
	 * 获取商城头部轮播信息
	 * @param  $merchant_id  商户id
	 */
	public function getShopIndexHeader($merchant_id)
	{
		$result = array ();
		$model = ShopIndex::model()->find('flag=:flag and merchant_id=:merchant_id',array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id));
		$data = array();
		if(!empty($model)){
			$data ['list'] ['id'] = $model ['id'];
			$data ['list'] ['merchant_id'] = $model ['merchant_id'];
			$data ['list'] ['banner'] = $model ['banner']; // 商城首页轮播图
			$data ['list'] ['banner_info'] = $this->getBannerInfo ( ($model ['banner']) ); //获取banner字段里的详细信息
			//$data ['list'] ['name'] = $this->getName ( ($model ['banner']) );
			$data ['list'] ['group_id'] = $model ['group_id']; // 分组id
			$data ['list'] ['create_time'] = $model ['create_time'];
			
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
	 * 获取banner字段里的详细信息(banner是json数据)
	 * $data    banner字段json数据
	 */
	public function getBannerInfo($data)
	{
		$list = json_decode($data,true);
		foreach ($list as $k=>$v){
			if (! empty ( $v ['type'] )) {
				if ($v ['type'] == SHOP_TYPE_GROUP) {
					$group = ShopGroup::model ()->findByPk ( $v ['url'] );
					$list [$k] ['name'] = $group->name; // 给$list数组添加数据
				} else {
					$product = ShopProduct::model ()->findByPk ( $v ['url'] );
					$list [$k] ['name'] = $product->name;
				}
			}else{
				$list [$k] ['name'] = '';
			}
		}
		return $list;
	}
	
	/**
	 * 获取第一个商品名称
	 */
	public function getName($data)
	{
		$name = '';
		$list = json_decode($data,true);
		
		$id = $list[0]['url'];
		$type = $list[0]['type'];
		if($type == SHOP_TYPE_GROUP){
			if(!empty($id)){
			  $group = ShopGroup::model()->findByPk($id);
			  $name = $group -> name;
			}else{
				$name = '';
			}
		}elseif($type == SHOP_TYPE_PRODUCT) {
			if(!empty($id)){
			$product = ShopProduct::model()->findByPk($id);
			$name = $product -> name;
			}else {
				$name = '';
			}
		}else{
			$name = '';
		}
		
		return $name;
	}
	
	/**
	 * 获取所有商品名称
	 */
	public function getNameArray($data)
	{
		$name = array();
		$list = json_decode($data,true);
		for($i=0;$i<count($list);$i++){
			$id = $list[$i]['url'];
			$type = $list[$i]['type'];
			if($type == SHOP_TYPE_GROUP){
				if (! empty ( $id )) {
					$group = ShopGroup::model ()->findByPk ( $id );
					$name [$i] = $group->name;
				} else {
					$name [$i] = '';
				}
			}elseif ($type == SHOP_TYPE_PRODUCT) {
				if (! empty ( $id )) {
					$product = ShopProduct::model ()->findByPk ( $id );
					$name [$i] = $product->name;
				} else {
					$name [$i] = '';
				}
			}else {
				$name[$i] = '';
			}
		}
		
		return $name;
	}
	
	/**
	 * 删除操作
	 */
	public function delShopIndex($shop_index_id)
	{
		$result = array();
		$model = ShopIndex::model()->findByPk($shop_index_id);
		$model -> flag = FLAG_YES;
		if($model->save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else {
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败'; //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 删除wq_shop_index表banner字段的第一个对象
	 */
	public function delShopIndexChild($shop_index_id)
	{
		$result = array();
		$model = ShopIndex::model()->findByPk($shop_index_id);
		$banner = $model -> banner;
		$list = json_decode($banner,true);
		array_splice($list, 0,1); //删除数组第一个元素     并重新排序索引
		if (count ( $list ) > 0) {
			$model->banner = json_encode ( $list );
			if ($model->save ()) {
				$result ['status'] = ERROR_NONE;
				$result ['errMsg'] = ''; // 错误信息
			} else {
				$result ['status'] = ERROR_SAVE_FAIL;
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		}else{ //数组删除后   如果数组没有元素了     就改变flag
			$model->banner = json_encode ( $list );
			$model -> flag = FLAG_YES;
			if ($model->save ()) {
				$result ['status'] = ERROR_NONE;
				$result ['errMsg'] = ''; // 错误信息
			} else {
				$result ['status'] = ERROR_SAVE_FAIL;
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		}
		return json_encode($result);
	}
	
	/**
	 * 添加数据到wq_shop_index表
	 * $merchant_id  商户id
	 * $data         数据数组
	 * $isData       判断对应商户在数据库是否已经有数据
	 */
	public function addShopIndex($merchant_id,$data,$group_id,$isData)
	{
		$result = array();
		if($isData){ //$isData为true则是修改
			$shopIndex = ShopIndex::model()->find('flag=:flag and merchant_id=:merchant_id',
					                              array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id));
			$arr = json_decode($shopIndex -> banner,true);
// 			if(empty($data)){
// 				$list = $arr;
// 			}else{
// 				$list = array_merge($arr,$data);
// 			}

			$data = $this->isHasNull($data);
			$shopIndex -> banner = json_encode($data);
		}else{  //$isData为false则是新增
		   $shopIndex = new ShopIndex();
		   $shopIndex -> merchant_id = $merchant_id;
		   $data = $this->isHasNull($data);
		   $shopIndex -> banner = json_encode($data);
		}
		
		
		$str = '';
		$group_id=array_filter ($group_id);
		$group_id = array_values($group_id);
		for ($i = 0;$i<count($group_id);$i++){
			if($i==0)
				$str=$group_id[0];
			else 
				$str=$str.','.$group_id[$i];
		}
		
		
		
		$shopIndex -> group_id = $str;
		
		if($shopIndex->save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else {
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败'; //错误信息
		}
		return json_encode($result);
		
	}
	
	/**
	 * 判断数组在某个索引下其键值是否都为空
	 */
	public function isHasNull($data)
	{
		for($i=0;$i<count($data);$i++){
			if($data[$i]['img'] == ''){
				array_splice($data, $i,1);
			}
		}
		return $data;
	}
	
	/**
	 * 获取头部子列表操作
	 */
	public function getHeader($merchant_id)
	{
		$result = array();
		$model = ShopIndex::model()->find('flag=:flag and merchant_id=:merchant_id',array(':flag'=>FLAG_NO,':merchant_id'=>$merchant_id));
		$data = array();
		if(!empty($model)){
			$data['id'] = $model -> id;
			$data['banner'] = $model -> banner;
			$data['group_id'] = $model -> group_id;
			$data['name'] = $this->getNameArray ( ($model ['banner']) );
			
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 修改头部信息
	 * $shop_index_id   数据库id
	 * $data            轮播图json数据
	 * $merchant_id     商户id
	 */
	public function editShopIndex($shop_index_id,$data,$merchant_id,$group_id)
	{
		$result = array();
		$model = ShopIndex::model()->findByPk($shop_index_id);
		$model -> merchant_id = $merchant_id;
		$model -> banner = json_encode($data);
		
	    $str = '';
		$group_id=array_filter ($group_id);
		$group_id = array_values($group_id);
		for ($i = 0;$i<count($group_id);$i++){
			if($i==0)
				$str=$group_id[0];
			else 
				$str=$str.','.$group_id[$i];
		}
		
		$model -> group_id = $str;
		if($model->save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else {
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败'; //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 删除子列表
	 * $shop_index_id   数据库id
	 * $delId           要删除的子列表的数组索引+1
	 */
	public function delShopIndexChildForId($shop_index_id,$delId)
	{
		$result = array();
		$model = ShopIndex::model()->findByPk($shop_index_id);
		$list = json_decode($model -> banner,true);
		array_splice($list, $delId-1,1);
		
		if (count ( $list ) > 0) {
			$model->banner = json_encode ( $list );
			if ($model->save ()) {
				$result ['status'] = ERROR_NONE;
				$result ['errMsg'] = ''; // 错误信息
			} else {
				$result ['status'] = ERROR_SAVE_FAIL;
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		}else{ //数组删除后   如果数组没有元素了     就改变flag
			$model->banner = json_encode ( $list );
			$model -> flag = FLAG_YES;
			if ($model->save ()) {
				$result ['status'] = ERROR_NONE;
				$result ['errMsg'] = ''; // 错误信息
			} else {
				$result ['status'] = ERROR_SAVE_FAIL;
				$result ['errMsg'] = '数据保存失败'; // 错误信息
			}
		}
		
		return json_encode($result);
	}

    /**
     * 保存商城首页
     */
    public function SaveShopMall($json_encode,$merchantId)
    {
        $result=array();
        try{
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $model = ShopIndex::model()->find('flag=:flag and merchant_id=:merchant_id',
                	array(':flag'=>FLAG_NO,':merchant_id'=>$merchantId));
            if(!empty($model))
            {
                $model->banner=$json_encode;
                $model->last_time=date('Y-m-d H:i:s',time());
                if($model->save())
                {
                    $result ['status'] = ERROR_NONE;
                    $result ['errMsg'] = ''; // 错误信息
                }
                else
                {
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result ['errMsg'] = '数据保存失败'; // 错误信息
                }
            }
            else
            {
                $shopIndex=new ShopIndex();
                $shopIndex->merchant_id=$merchantId;
                $shopIndex->banner=$json_encode;
                $shopIndex->create_time=date('Y-m-d H:i:s',time());
                if($shopIndex->save())
                {
                    $result ['status'] = ERROR_NONE;
                    $result ['errMsg'] = ''; // 错误信息
                }
                else
                {
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result ['errMsg'] = '数据保存失败'; // 错误信息
                }
            }

        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取商城首页
     */
    public function getShopMallIndex($merchantId)
    {
        $result=array();
        try{
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $shopIndex=ShopIndex::model()->find('merchant_id=:merchant_id and flag=:flag',array(
               ':merchant_id' =>$merchantId,
                ':flag'=>FLAG_NO
            ));
            $data=array();
            if(!empty($shopIndex))
            {
                $data['merchant_id']=$shopIndex->merchant_id;
                $data['banner']=$shopIndex->banner;
                $data['group_id']=$shopIndex->group_id;
                $data['create_time']=$shopIndex->create_time;
                $data['last_time']=$shopIndex->last_time;
                $result['data']=$data;
                $result ['status'] = ERROR_NONE;
                $result ['errMsg'] = ''; // 错误信息
            }
            else
            {
                $result ['status'] = ERROR_NO_DATA;
                $result ['errMsg'] = '无此数据'; // 错误信息
            }

        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

}