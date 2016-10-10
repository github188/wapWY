<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
class DMallUC extends mainClass{
    /**
     * 查询商品列表
     * merchant_id 商户ID
     * placeholder 搜索框内容
     * typename 搜索类型名字，如销量，价格，新品
     * type 分为down和up,降序和升序
     * shopgorup 筛选ID
     */
   public function queryCommodityList($merchant_id,$placeholder,$typename,$type,$shopgorup,$groupid)
   {
       $result = array();
       try {
           /*if (empty($merchantId)) {
               $result['status'] = ERROR_PARAMETER_FORMAT;
               throw new Exception('参数merchant_id不能为空');
           }*/
           //数据库查询
           $criteria = new CDbCriteria();
           $criteria -> addCondition("merchant_id = :merchant_id");
           $criteria -> params[':merchant_id'] = $merchant_id;
           $criteria -> addCondition("flag = :flag");
    	   $criteria -> params[':flag'] = FLAG_NO;
    	   $criteria -> addCondition("status = :status");
    	   $criteria -> params[':status'] = SHOP_PRODUCT_STATUS_UP;
           
           if(!empty($groupid)){
           		//分组
           		$criteria -> addCondition("group_id = :group_id");
           		$criteria -> params[':group_id'] = $groupid;
           }
           if(isset($placeholder)&&!empty($placeholder)){
              	$criteria->compare('name',$placeholder,true);
           }
           if(isset($typename)&&!empty($typename)&&isset($type)&&!empty($type)){
               if($type=='down'){
                   $criteria -> order=$typename." desc";
               }else if($type=='up'){
                   $criteria -> order=$typename." asc";
               }
           }
           $model=DProduct::model()->findAll($criteria);

           if(!empty($model)){
               $data=array();
               foreach($model as $k=>$v)
               {
                   $data[$k]['id']=$v['id'];
                   $data[$k]['name']=$v['name'];
                   $data[$k]['price']=$v['price'];
                   $data[$k]['merchant_id']=$v['merchant_id'];
                   $data[$k]['category_id']=$v['category_id'];
                   $data[$k]['group_id']=$v['group_id'];
                   $data[$k]['standard']=$v['standard'];
                   $data[$k]['page_id']=$v['page_id'];
                   $data[$k]['type']=$v['type'];
                   $data[$k]['img']=$v['img'];
//                    $data[$k]['ts_product_id'] = $v['ts_product_id'];
                   $modelSku=DProductSku::model()->findAll('product_id=:product_id and flag=:flag',array(
            		':product_id'=>$v['id'],
            		':flag'=>FLAG_NO
                    ));
                    $minprice=array();
                    $maxprice=array();
                    $minorgprice=array();
                    $maxorgprice=array();
                    if(!empty($modelSku))
                    {
                        foreach($modelSku as $key=>$value)
                        {
                            //找出最小价格                           
                                $minprice[]=$value['price'];                             
                            //找出最大价格                      
                                $maxprice[]=$value['price'];
                             
                            //找出最小原始价格                         
                                $minorgprice[]=$value['original_price'];                            
                            //找出最大原始价格                           
                                $maxorgprice[]=$value['original_price'];                            
                        }
                    }
                    $data[$k]['minprice']=min($minprice);
                    $data[$k]['maxprice']=max($maxprice);
                    $data[$k]['minorgprice']=min($minorgprice);
                    $data[$k]['maxorgprice']=max($maxorgprice);
               }               
               $result['data'] = $data;
               $result['status'] = ERROR_NONE; //状态码
               $result['errMsg'] = ''; //错误信息
           }else{
               $result['status'] = ERROR_NO_DATA; //状态码
               $result['errMsg'] = '无此数据'; //错误信息
           }

       }catch (Exception $e) {
           $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
           $result['errMsg'] = $e->getMessage(); //错误信息
       }
       return json_encode($result);
   }

    //根据ID查询商品详情
    public function queryCommodityDetails($id)
    {
        $result = array();
        try {
            /*if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }*/
            //数据库查询
            $model=DProduct::model()->findByPk($id);
            $modelSku=DProductSku::model()->findAll('product_id=:product_id and flag=:flag',array(
            		':product_id'=>$id,
            		':flag'=>FLAG_NO
            ));
//             echo $id.' '.$model -> id;exit();
            if(!empty($model))
            {
                $data=array();
                $datasku=array();
                $data['id']=$model->id;
                $data['name']=$model->name;
                $data['price']=$model->price;
                $data['img']=$model->img;
                $data['check_time_type'] = $model->check_time_type;
                $data['check_day'] = $model->check_day;
                $data['check_hour'] = $model->check_hour;
                $data['check_minute'] = $model->check_minute;

                $data['standard'] = $model -> standard;
//                 $data['detailed_introduction'] = $model -> detailed_introduction;
//                 $data['ts_product_id'] = $model['ts_product_id'];
                $minprice=0;
                $maxprice=0;
                $minorgprice=0;
                $maxorgprice=0;
                if(!empty($modelSku))
                {
                    foreach($modelSku as $k=>$v)
                    {
                        $datasku[$k]['id']=$v['id'];
                        $datasku[$k]['product_id']=$v['product_id'];
                        $datasku[$k]['name']=$v['name'];
                        $datasku[$k]['price']=$v['price'];
                        //找出最小价格
                        if($minprice==0)
                            $minprice=$v['price'];
                        else if($minprice>$v['price'])
                            $minprice=$v['price'];
                        //找出最大价格
                        if($maxprice==0)
                            $maxprice=$v['price'];
                        else if($maxprice<$v['price'])
                            $maxprice=$v['price'];
                        $datasku[$k]['original_price']=$v['original_price'];
                        //找出最小原始价格
                        if($minorgprice==0)
                            $minorgprice=$v['original_price'];
                        else if($minprice>$v['original_price'])
                            $minorgprice=$v['original_price'];
                        //找出最大原始价格
                        if($maxorgprice==0)
                            $maxorgprice=$v['original_price'];
                        else if($maxorgprice>$v['original_price'])
                            $maxorgprice=$v['original_price'];
                        $datasku[$k]['sold_num']=$v['sold_num'];
                    }
                }
                $result['minprice']=$minprice;
                $result['maxprice']=$maxprice;
                $result['minorgprice']=$minorgprice;
                $result['maxorgprice']=$maxorgprice;
                $result['data'] = $data;
                $result['datasku'] = $datasku;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                if($data['check_time_type'] == DMALL_DPRODUCT_CHECK_TIME_TYPE_NO_LIMIT){
                    $result['notice'] = '';
                }
                if($data['check_time_type'] == DMALL_DPRODUCT_CHECK_TIME_TYPE_DAY_HOUR_MINUTE){
                    if($data['check_day'] == 0){
                        $result['notice'] = '请至少在入园当天的'.$data['check_hour'].'点'.$data['check_minute'].'分以前购买';
                    } else {
                        $result['notice'] = '请至少在入园前'.$data['check_day'].'天的'.$data['check_hour'].'点'.$data['check_minute'].'分以前购买';
                    }
                    
                }
                if($data['check_time_type'] == DMALL_DPRODUCT_CHECK_TIME_TYPE_HOUR_MINUTE){
                    if($data['check_hour'] == 0){
                        $result['notice'] = '须提前'.$data['check_minute'].'分钟购买';
                    } else {
                        if($result['check_minute'] == 0){
                            $result['notice'] = '须提前'.$data['check_hour'].'个小时购买';
                        } else{
                            $result['notice'] = '须提前'.$data['check_hour'].'个小时'.$data['check_minute'].'分钟购买';    
                        }
                        

                    }
                }

            }else{
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 根据name搜索相应的商品
     */
    public function queryOneCommodity($merchant_id,$name)
    {
        $result = array();
        try {
            /*if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }*/
            //数据库查询
            $criteria = new CDbCriteria();
//            $criteria -> order = 'limit_num desc';
            $criteria->compare('name',$name,true);
            $model=DProduct::model()->findAll($criteria);
            if(!empty($model))
            {
                $data=array();
                foreach($model as $key=>$value) {
                    $data[$key]['id'] = $value['id'];
                    $data[$key]['name'] = $value['name'];
                    $data[$key]['price'] = $value['price'];
                    $data[$key]['img'] = $value['img'];
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息

            }
            else
            {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 根据ID查询该商品金额
     * id 商品ID
     */
    public function queryCommodityMoney($id)
    {
        $result = array();
        try {
            /*if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }*/
            //数据库查询

            $model=DProduct::model()->findByPk($id);
            if(!empty($model))
            {
                $result['data'] = $model->price;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息

            }
            else
            {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

   
    
    
    /*
     * 获取用户购物车信息
     * 
     * */
    public function getUserCart($user_id){
    	$result = array();
    	try {
    		$cart = ShopCart::model() -> findAll('user_id =:user_id and flag =:flag',array(
    				':user_id' => $user_id,
    				':flag' => FLAG_NO
    		));
    		$data = array();
    		if($cart){
    			foreach ($cart as $k => $v){
    				$product = DProduct::model() -> findByPk($v -> product_id);
    				$productsku = DProductSku::model() -> findByPk($v -> sku_id);
    				$data['list'][$k]['id'] = $v -> id;
    				$data['list'][$k]['name'] = $v -> product_name;
    				$data['list'][$k]['sku_name'] = $productsku -> name;
    				$data['list'][$k]['num'] = $v -> num;
    				$data['list'][$k]['sku_price'] = $productsku -> price;
    				$data['list'][$k]['sku_id'] = $productsku -> id;
    				$data['list'][$k]['img'] = $product -> img;
    			}
    		}else{
    			$data['list'] = array();
    		}
    		$result['data'] = $data;
    		$result['status'] = ERROR_NONE;
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /*
     * 删除购物车
     * $cart_id 购物车id 必填
     * */
    public function delCart($cart_id){
    	$result = array();
    	$transaction = Yii::app()->db->beginTransaction();
    	try {
    		foreach ($cart_id as $k => $v){
    			$cart = ShopCart::model() -> findByPk($v);
    			$cart -> flag = FLAG_YES;
    			if($cart -> update()){
    				
    			}else{
    				$transaction->rollback(); //数据回滚
    			}
    		}
    		$result['status'] = ERROR_NONE;
    	} catch (Exception $e) {
    		$transaction->rollback(); //数据回滚
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }

    /**
     * 删除购物车某一件商品
     */
    public function delCartGoods($cart_id){
        $result = array();
        try {
            $cart = ShopCart::model() -> findByPk($cart_id);
            $cart -> flag = FLAG_YES;
            if($cart -> update()){
                $result['status'] = ERROR_NONE;
                $result['errMsg']='';
            }else{
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg']='删除失败，请重试';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    
    
    /*
     * 添加购物车
     * $sku_name sku名称 必填
     * $num 数量 必填
     * $user_id 用户id 必填
     * */
    public function addCart($product_id,$sku_name,$num,$user_id){
    	$result = array();
    	try {
    		if(empty($sku_name) || empty($user_id) || empty($num)){
    			$result['status'] = ERROR_PARAMETER_FORMAT;
    			throw new Exception('参数不全');
    		}
    		$sku = DProductSku::model() -> find('product_id=:product_id and name =:name and flag =:flag',array(
    				':product_id' => $product_id,
    				':name' => $sku_name,
    				':flag' => FLAG_NO
    		));
    		if(empty($sku)){
    			$result['errMsg'] = ERROR_NO_DATA;
    			throw new Exception('该sku不存在');
    		}
    		//判断该商品是否已加入购物车
    		$cart = ShopCart::model() -> find('user_id =:user_id and sku_id =:sku_id and flag =:flag',array(
    				':user_id' => $user_id,
    				':sku_id' => $sku -> id,
    				':flag' => FLAG_NO
    		));
    		//已加入购物车
    		if($cart){
    			$cart -> num = $cart -> num + $num;
    		}else{
    			//未加入购物车
    			$cart = new ShopCart();
    			$cart -> user_id = $user_id;
    			$cart -> product_id = $product_id;
    			$cart -> sku_id = $sku -> id;
    			$cart -> num = $num;
    			$cart -> product_name = $sku -> product -> name;
    			$cart -> create_time = new CDbExpression('now()');
    		}
    		if($cart -> save()){
    			$result['status'] = ERROR_NONE;
    			$result['id'] = $cart -> id;
    		}else{
    			$result['status'] = ERROR_SAVE_FAIL;
    			$result['errMsg'] = '数据保存失败';
    		}
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    
    /*
     * 获取sku信息
     * $arr_sku_id skuid数组 必填
     * */
    public function getProductSkuInfo($arr_sku_id, $encrypt_id)
    {
        $result = array();
        try {
            $data = array();
            $arr_sku = array();
            foreach ($arr_sku_id as $k => $v) {
                $sku = DProductSku::model()->findByPk($v);
                if ($sku->flag == FLAG_YES || $sku->product->flag == FLAG_YES) {
                    $result['status'] = ERROR_NO_DATA;
                    throw new Exception('该商品已删除');
                }

                if ($sku) {
                    $data[$k]['id'] = $sku->id;
                    $data[$k]['name'] = $sku->name;
                    $data[$k]['product_id'] = $sku->product_id;
                    $data[$k]['price'] = $sku->price;
                    $data[$k]['original_price'] = $sku->original_price;
                    $data[$k]['img'] = $sku->product->img;
                    $data[$k]['product_name'] = $sku->product->name;
                    $data[$k]['use_time_type'] = $sku->product->use_time_type;
                    $data[$k]['date_num'] = $sku->product->date_num;
                    $data[$k]['create_time'] = $sku->product->create_time;
                    $data[$k]['third_party_source'] = $sku->product->third_party_source;
                    $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
                    $user = User::model()->find('flag=:flag and id=:id', array(':flag' => FLAG_NO, ':id' => $user_id));
                    $data[$k]['account'] = $user['account'];
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    throw new Exception('该sku不存在');
                }
                $arr_sku[$k] = $sku->id;
            }
            $result['data'] = $data;
            $result['arr_sku'] = $arr_sku;
            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    
    /*
     * 根据sku名字获取sku信息
     * */
    public function getSkuByName($sku_name,$product_id){
    	$result = array();
    	try {
    		if(empty($sku_name) || empty($product_id)){
    			$result['status'] = ERROR_PARAMETER_FORMAT;
    			throw new Exception('参数不全');
    		}
    		$sku = DProductSku::model() -> find('product_id=:product_id and name =:name and flag =:flag',array(
    				':product_id' => $product_id,
    				':name' => $sku_name,
    				':flag' => FLAG_NO
    		));
    		if($sku){ 
    			$result['status'] = ERROR_NONE;
    			$result['data'] = $sku -> id;;
    		}else{
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('该sku不存在');
    		}
    		
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }

    /*
     * 获取省市区信息
     * */
    public function getProvince(){
    	$result = array();
    	try {
    		$p = ShopCity::model() -> findAll('level =:level',array(
    				':level' => CITY_LEVEL_PROVINCE
    		));
    		$data = array();
    		foreach ($p as $k => $v){
    			$data[$k]['name'] = $v -> name;
    			$data[$k]['code'] = $v -> code;
    		}
    		$result['status'] = ERROR_NONE;
    		$result['data'] = $data;
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /*
     * 根据code 获取市
     * */
    public function getCity($code){
    	$result = array();
    	try {
    		$subcode = substr($code, 0,2);
    		$criteria = new CDbCriteria;
    		$criteria->addCondition("code like '$subcode%'");
    		$criteria->addCondition("level = :level");
    		$criteria->params[':level'] = CITY_LEVEL_CITY;

    		$c = ShopCity::model() -> findAll($criteria);
    		$data = array();
    		foreach ($c as $k => $v){
    			$data[$k]['name'] = $v -> name;
    			$data[$k]['code'] = $v -> code;
    		}
    		$result['status'] = ERROR_NONE;
    		$result['data'] = $data;
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /*
     * 根据code 获取区
     * */
    public function getArea($code){
    	$result = array();
    	try {
    		$subcode = substr($code,0,4);
    		$criteria = new CDbCriteria;
    		$criteria->addCondition("code like '$subcode%'");
    		$criteria->addCondition("level = :level");
    		$criteria->params[':level'] = CITY_LEVEL_AREA;
    		
    		$a = ShopCity::model() -> findAll($criteria);
    		$data = array();
    		foreach ($a as $k => $v){
    			$data[$k]['name'] = $v -> name;
    			$data[$k]['code'] = $v -> code;
    		}
    		$result['status'] = ERROR_NONE;
    		$result['data'] = $data;
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /*
     * 获取商城首页信息
     * */
    public function getMallIndex($merchant_id,$encrypt_id = ''){
    	header("Content-type:text/html;charset=utf-8");  
    	$result = array();
    	try {
    		if(empty($merchant_id)){
    			$merchant = Merchant::model() -> find('encrypt_id =:encrypt_id and flag =:flag',array(
    					':encrypt_id' => $encrypt_id,
    					':flag' => FLAG_NO
    			));
    			$merchant_id = $merchant -> id;
    		}
    		$shopindex = ShopIndex::model() -> find('merchant_id =:merchant_id and flag =:flag',array(
    				':merchant_id' => $merchant_id,
    				':flag' => FLAG_NO
    		));
    		if($shopindex){
    			$data = array();
    			$data['id'] = $shopindex -> id;
    			$data['banner'] = $shopindex -> banner;
    			$data['group_id'] = $shopindex -> group_id;
    			$data['product_info'] = $this -> getProductForGroup($shopindex -> group_id,$merchant_id);                        
    			$data['merchant_id'] = $merchant_id;
    			$result['status'] = ERROR_NONE;
    			$result['data'] = $data;
    		}else{
    			$result['status'] = ERROR_NO_DATA;
    			throw new Exception('商户未设置首页');
    		}
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 获取分组下的商品信息
     * $group_id   商品分组的id集（json数据）
     * $merchant_id  商户id
     */
    public function getProductForGroup($group_id,$merchant_id)
    {
    	$product = array();
    	$groupId = explode(',', $group_id);
    	for($i=0;$i<count($groupId);$i++){
    		$group = ShopGroup::model()->findByPk($groupId[$i]);
    		$product[$i]['group_id'] = $group -> id; //分组id
    		$product[$i]['group_name'] = $group -> name; //分组名
    		$product[$i]['product_info'] = $this -> getProductForGroupId($groupId[$i],$merchant_id);
    	}
    	
    	return $product;
    }
    
    /**
     * 获取分组
     * @param type $merchant_id
     * @return type
     */
    public function getGroup($merchant_id,$encrypt_id = '')
    {
        $result = array();
        if(empty($merchant_id)){
            $merchant = Merchant::model() -> find('encrypt_id =:encrypt_id and flag =:flag',array(
                            ':encrypt_id' => $encrypt_id,
                            ':flag' => FLAG_NO
            ));
            $merchant_id = $merchant -> id;
        }
        $group = ShopGroup::model()->findall('flag=:flag and merchant_id=:merchant_id',array(
            ':flag' => FLAG_NO,
            ':merchant_id' => $merchant_id,
        ));
        $data = array();
        foreach($group as $k => $v)
        {
            $data[$v['id']]['group_name'] = $v['name'];  
            $product = DProduct::model() -> findAll('group_id = :group_id and flag =:flag and status = :status',array(
            		':group_id' => $v['id'],
            		':flag' => FLAG_NO,
            		':status' => 2
            )); 
            $count = 0;
            $p = array();
            foreach ($product as $m => $n){
            	$p[$m]['id'] = $n -> id; 
            	$p[$m]['name'] = $n -> name;
            	$p[$m]['img'] = $n -> img;
            	$p[$m]['price'] = $n -> price;
            	$count ++;
            	if($count == 4){
            		break;
            	}
            }
            $data[$v['id']]['product'] = $p;
        }
        $result['status'] = ERROR_NONE;
        $result['data'] = $data;
        return json_encode($result);
    }

        /**
     * 获取某个分组id下的商品信息
     * $groupId   商品分组的id
     * $merchant_id  商户id
     */
    public function getProductForGroupId($groupId,$merchant_id)
    {
    	$list = array();
    	$shop_product = DProduct::model() -> findAll('merchant_id = :merchant_id  and flag = :flag and status =:status',array(
    			':merchant_id' => $merchant_id,
    			':flag' => FLAG_NO,
    			':status' => SHOP_PRODUCT_STATUS_UP
    	));
    	if (! empty ( $shop_product )) {
			foreach ( $shop_product as $k => $v ) {
				$group_id = explode(',',$v['group_id']);
				if(in_array($groupId, $group_id)){ //分组id在商品对应的分组里存在，即该商品属于该分组
					$list[$k]['id'] = $v['id'];
					$list[$k]['name'] = $v['name'];
					$list[$k]['price'] = $v['price'];
					$list[$k]['img'] = $v['img'];
//                     $list[$k]['ts_product_id'] = $v['ts_product_id'];
				}
			}
		}
		
		return $list;
    }
    
    /*
     * 获取商品的规格属性
     * $product_id 商品id
     * */
    public function getProductStandard($product_id){

    	try {
			$product = DProduct::model() -> findByPk($product_id);
                        $productSku = DProductSku::model()->findall('product_id=:product_id and flag=:flag',array(
                            ':product_id' => $product_id,
                            ':flag' => FLAG_NO,
                        ));                        
                        $minorgprice = array();
                        foreach ($productSku as $key => $value) {
                            $minorgprice[] = $value['original_price'];
                        }
                        $min = min($minorgprice);
			if(!empty($product)){
                                $result['orgprice'] = '¥'.$min;
                                $result['price'] = '¥'.$product['price'];
				$result['data'] = $product -> standard;
				$arr = explode(';',$product -> img);
				$result['img'] = !empty($product['ts_product_id']) ? $arr[0] : IMG_GJ_LIST.$arr[0];
				$result['id'] = $product -> id;
				$result['status'] = ERROR_NONE;
				
			}else{
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('该商品不存在');
			}
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
    /**
     * 获取规格价格
     * @param type $product_id
     * @param type $name
     * @return type
     */
    public function getProductSkuPrice($product_id,$name){

    	try {
            $productSku = DProductSku::model()->find('product_id=:product_id and flag=:flag and name=:name',array(
                    ':product_id' => $product_id,
                    ':name' => $name,
                    ':flag' => FLAG_NO,
            ));
            if($productSku){
                $result['price'] = '¥'.$productSku['price']; 
                $result['orgprice'] = '¥'.$productSku['original_price'];
                $result['status'] = ERROR_NONE;				
            }else{
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该sku不存在');
            }
        } catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
        return json_encode($result);
    }
    
    /*
     * 获取商品详情
     * */
    public function getDproductDetailedIntroduction($product_id){
    	$result = array();
    	try {
    		$product = DProduct::model() -> findByPk($product_id);
    		if(!empty($product)){
    			$result['status'] = ERROR_NONE;
    			$result['data'] = $product -> detailed_introduction;
    			$result['errMsg'] = '';
    		}
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
    		$result['errMsg'] = $e->getMessage(); //错误信息
    	}
    	return json_encode($result);
    }
    
}
?>