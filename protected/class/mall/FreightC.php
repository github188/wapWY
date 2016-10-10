<?php
/**
 * 运费模板类
 */
include_once(dirname(__FILE__) . '/../mainClass.php');
class FreightC extends mainClass
{
    //分页
    public $page=null;
    /**
     * 查询添加的运费模板信息
     * merchantId 商户id
     */
    public function queryFreightInfo($merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $criteria = new CDbCriteria();
            $criteria -> order = 'last_time desc';
            $criteria->addCondition ( 'merchant_id=:merchant_id and flag=:flag' );
            $criteria->params = array (
                ':merchant_id' => $merchantId,
                ':flag' => FLAG_NO
            );

            //显示分页
            $pages = new CPagination(ShopFreight::model()->count($criteria));
            $pages->pageSize =3; //Yii::app() -> params['perPage'];
            $pages->applyLimit($criteria);

            $model_freight=ShopFreight::model()->findAll($criteria);
            if(!empty($model_freight))
            {
                $data=array();
                foreach($model_freight as $k=>$v)
                {
                    $model_subfreight=ShopSubfreight::model()->findAll('freight_id=:freight_id and flag=:flag',array(':freight_id'=>$v['id'],':flag'=>FLAG_NO));
                    $model_subfreight_sum=count($model_subfreight);
                    //表ShopFreight
                    $data[$k]['id']=$v['id'];
                    $data[$k]['merchant_id']=$v['merchant_id'];
                    $data[$k]['name']=$v['name'];
                    $data[$k]['create_time']=$v['create_time'];
                    $data[$k]['last_time']=$v['last_time'];
                    $data[$k]['flag']=$v['flag'];
                    $data[$k]['subfreight_sum']=$model_subfreight_sum;//子运费总数量
                    $data[$k]['products']=count(ShopProduct::model()->findAll('freight_id=:freight_id and flag=:flag',array(':freight_id'=>$v['id'],':flag'=>FLAG_NO)));//商品使用此模板数量
                    //表ShopSubFreight
                    if(!empty($model_subfreight))
                    {
                        //查询表subfreight
                        $subdata=array();
                        foreach($model_subfreight as $key=>$value)
                        {
                            $subdata[$key]['id']=$value['id'];
                            $subdata[$key]['freight_id']=$value['freight_id'];
                            $subdata[$key]['area']=$value['area'];
                            $subdata[$key]['first_num']=$value['first_num'];
                            $subdata[$key]['first_freight']=$value['first_freight'];
                            $subdata[$key]['second_num']=$value['second_num'];
                            $subdata[$key]['second_freight']=$value['second_freight'];
                            $subdata[$key]['create_time']=$value['create_time'];
                            $subdata[$key]['last_time']=$value['last_time'];
                            $subdata[$key]['flag']=$value['flag'];
                        }
                        $data[$k]['subfreight']=$subdata;//子运费
                    }
                    else
                    {
                        $data[$k]['subfreight']=null;//子运费
                    }

                }
                $this->page = $pages;
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
     * 删除运费模板
     * id 运费模板id
     */
    public function deleleFreight($id,$merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //删除表Freight
            $model_freight=ShopFreight::model()->findByPK($id);
            $model_freight->flag=FLAG_YES;
            $model_freight->last_time=date('Y-m-d H:i:s', time());
            if($model_freight -> save()){
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            }else{
                $result ['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 修改运费模板
     * id 表Freight的id
     * merchantId 商户id
     * name 运费模板名称
     * post 要更新的数据
     */
    public function updateFreight($id,$merchantId,$name,$post)
    {
        $transaction=Yii::app()->db->beginTransaction();
        $result = array();
        $transactionFlag=true;
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //update表Freight
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = ShopFreight::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('修改的运费模板不存在');
            }
            $model['name'] = $name;
            $data = date('Y-m-d H:i:s', time());
            $model['last_time'] = $data;
            if(isset($post)&&!empty($post)) {
                if ($model->save()) {
                    //子模板总数
                    $subcount=ShopSubfreight::model()->findAll('freight_id=:freight_id and flag=:flag',array(':freight_id'=>$id,':flag'=>FLAG_NO));
                    $oldsubcount=0;
                    $NodeleteId=array();
                    $deleteId=array();
                    foreach($post as $key=>$value)
                    {
                        if($value['id']!='new')
                        {
//                            没有被删掉的原来的子模板数量
                            $oldsubcount++;
                            $NodeleteId[$key]=$value['id'];
                        }
                    }
                    foreach($subcount as $allkey=>$allvalue)
                    {
                        $allId=$allvalue['id'];
                        $flag=true;
                        foreach($NodeleteId as $nodelkey=>$nodelvalue)
                        {
                            if($allId==$nodelvalue)
                            {
                                $flag=false;
                            }
                        }
                        if($flag)
                        {
                            //该子模板被删掉了
                            $deleteId[$allkey]=$allvalue['id'];
                        }
                    }
                    //删除子模板
                    foreach($deleteId as $delkey=>$delvalue)
                    {
                        $subfreight = ShopSubfreight::model()->findByPk($delvalue);
                        $subfreight['flag'] = FLAG_YES;
                        $subfreight['last_time'] = $data;
                        if($subfreight -> save()){
                            $result ['status'] = ERROR_NONE;
                            $result['errMsg'] = ''; //错误信息
                        }else{
                            $transactionFlag=false;
                            $result ['status'] = ERROR_SAVE_FAIL;
                            $result['errMsg'] = '数据保存失败'; //错误信息
                        }
                    }

                    foreach ($post as $k => $v) {
                        //更新数据
                        if ($v['id']!='new') {
                            $subfreight = ShopSubfreight::model()->findByPk($v['id']);//('id=:id and freight_id=:freight_id and flag=:flag',array(':id'=>10,':freight_id'=>$id,':flag'=>FLAG_NO));
                            $subfreight['area'] = $v['area'];
                            $subfreight['first_num'] = $v['first_num'];
                            $subfreight['first_freight'] = $v['first_freight'];
                            $subfreight['second_num'] = $v['second_num'];
                            $subfreight['second_freight'] = $v['second_freight'];
                            $subfreight['last_time'] = $data;
                            if ($subfreight->save()) {
                                $result['status'] = ERROR_NONE; //状态码
                                $result['errMsg'] = ''; //错误信息
                            } else {
                                $transactionFlag=false;
                                $result ['status'] = ERROR_SAVE_FAIL;
                                $result['errMsg'] = '数据保存失败'; //错误信息
                            }
                        } else {
                            //保存数据
                            $addSubFreight = new ShopSubfreight();
                            $addSubFreight['area'] = $v['area'];
                            $addSubFreight['freight_id'] = $id;
                            $addSubFreight['first_num'] = $v['first_num'];
                            $addSubFreight['first_freight'] = $v['first_freight'];
                            $addSubFreight['second_num'] = $v['second_num'];
                            $addSubFreight['second_freight'] = $v['second_freight'];
                            $addSubFreight['create_time'] = $data;
                            if ($addSubFreight->save()) {
                                $result['status'] = ERROR_NONE; //状态码
                                $result['errMsg'] = ''; //错误信息
                            } else {
                                $transactionFlag=false;
                                $result ['status'] = ERROR_SAVE_FAIL;
                                $result['errMsg'] = '数据保存失败'; //错误信息
                            }
                        }
                    }
                } else {
                    $transactionFlag=false;
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            }
            /*else
            {
                //删除父模板
                $model_freight=ShopFreight::model()->findByPK($id);
                $model_freight->flag=FLAG_YES;
                $model_freight->last_time=date('Y-m-d H:i:s', time());
                if($model_freight -> save()){
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }else{
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            }*/
            if($transactionFlag)
            {
                $transaction->commit();
            }
            else
            {
                $transaction->rollback();
            }


        }catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 添加运费模板
     * merchantId 商户id
     * name 运费模板名称
     * post 传递值
     */
    public function addFreight($merchantId,$name,$post)
    {
        $transaction=Yii::app()->db->beginTransaction();
        $transactionFlag=true;
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //添加运费模板
            $date=date('Y-m-d H:i:s', time());
            $model_ShopFreight = new ShopFreight();
            $model_ShopFreight['merchant_id']=$merchantId;
            $model_ShopFreight['name']=$name;
            $model_ShopFreight['flag']=FLAG_NO;
            $model_ShopFreight['create_time'] =$date ;

            if ($model_ShopFreight->save()) {
                $freight_id=$model_ShopFreight->attributes['id'];//刚刚保存在表Freight的id
                foreach($post as $k=>$v)
                {
                    $model_ShopSubFreight=new ShopSubfreight();
                    $model_ShopSubFreight['freight_id']=$freight_id;
                    $model_ShopSubFreight['area']=$v['area'];
                    $model_ShopSubFreight['first_num']=$v['first_num'];
                    $model_ShopSubFreight['first_freight']=$v['first_freight'];
                    $model_ShopSubFreight['second_num']=$v['second_num'];
                    $model_ShopSubFreight['second_freight']=$v['second_freight'];
                    $model_ShopSubFreight['create_time']=$date;
                    $model_ShopSubFreight['flag']=FLAG_NO;

                    if($model_ShopSubFreight->save())
                    {
                        $result['status'] = ERROR_NONE; //状态码
                        $result['errMsg'] = ''; //错误信息
                    }
                    else
                    {
                        $transactionFlag=false;
                        $result['status'] = ERROR_SAVE_FAIL; //状态码
                        $result['errMsg'] = '数据保存失败'; //错误信息
                    }
                }
            }else {
                $transactionFlag=false;
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
            if($transactionFlag)
            {
                $transaction->commit();
            }
            else
            {
                $transaction->rollback();
            }
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 编辑运费模板时查询运费模板信息
     * id 运费模板id
     * merchantId 商户id
     */
    public function queryFreight($id,$merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $criteria = new CDbCriteria();
            $criteria->addCondition ( 'id=:id and merchant_id=:merchant_id and flag=:flag' );
            $criteria->params = array (
                ':id'=>$id,
                ':merchant_id' => $merchantId,
                ':flag' => FLAG_NO
            );
            $data=array();
            $model_freight=ShopFreight::model()->findAll($criteria);
            if(!empty($model_freight))
            {

                $model_subfreight=ShopSubfreight::model()->findAll('freight_id=:freight_id and flag=:flag',array(':freight_id'=>$model_freight[0]['id'],':flag'=>FLAG_NO));
                //表ShopFreight
                foreach($model_subfreight as $k=>$v) {
                    $data[$k]['freight_id'] = $model_freight[0]['id'];
                    $data[$k]['name'] = $model_freight[0]['name'];
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['area'] = $v['area'];
                    $data[$k]['first_num'] = $v['first_num'];
                    $data[$k]['first_freight'] = $v['first_freight'];
                    $data[$k]['second_num'] = $v['second_num'];
                    $data[$k]['second_freight'] = $v['second_freight'];
                    $data[$k]['last_time'] = $v['last_time'];
                }
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 复制模板
     * id 母模板id
     * merchantId 商户id
     */
    public function copyFreight($id,$merchantId)
    {
        $transaction=Yii::app()->db->beginTransaction();
        $transactionFlag=true;
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $copy_freight=array();
            $model_freight=ShopFreight::model()->findByPk($id);
            $save_freight=new ShopFreight();
            $date=date('Y-m-d H:i:s', time());
            $copy_time=date('Y-m-d,H:i:s', time());
            if(!empty($model_freight))
            {
                $muban_id=$model_freight['copy_id'];//模板ID
                $save_freight->merchant_id=$merchantId;
                //根据模板ID找最原始的模板，如果copy_id为空，就设当前模板ID为copy_id
                if(isset($muban_id)&&!empty($muban_id))
                {
                    $save_freight->copy_id=$muban_id;
                    $muban_model=ShopFreight::model()->findByPk($muban_id);
                    $save_freight->name=$muban_model['name']."@".$copy_time;
                }
                else
                {
                    $save_freight->copy_id=$id;
                    $save_freight->name=$model_freight['name']."@".$copy_time;
                }
                $save_freight->create_time=$date;
                $save_freight->last_time=$date;
            }
            //保存表Freight
            if($save_freight->save())
            {
                $freight_id=$save_freight->attributes['id'];
                //保存表SubFreight
                $subfreight=ShopSubfreight::model()->findAll('freight_id=:freight_id and flag=:flag',array(':freight_id'=>$id,':flag'=>FLAG_NO));
                if(!empty($subfreight))
                {
                    $copy_subfreight=array();
                    foreach($subfreight as $k=>$v) {
                        $copy_subfreight = $subfreight[$k];
                        $save_subfreight = new ShopSubfreight();
                        $save_subfreight->freight_id = $freight_id;
                        $save_subfreight->area = $copy_subfreight['area'];
                        $save_subfreight->first_num = $copy_subfreight['first_num'];
                        $save_subfreight->first_freight = $copy_subfreight['first_freight'];
                        $save_subfreight->second_num = $copy_subfreight['second_num'];
                        $save_subfreight->second_freight = $copy_subfreight['second_freight'];
                        $save_subfreight->create_time = $date;
                        if ($save_subfreight->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = ''; //错误信息
                        } else {
                            $transactionFlag=false;
                            $result['status'] = ERROR_SAVE_FAIL; //状态码
                            $result['errMsg'] = '数据保存失败'; //错误信息
                            return 0;
                        }
                    }
                }
            }
            else
            {
                $transactionFlag=false;
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
            if($transactionFlag)
                $transaction->commit();
            else
                $transaction->rollback();

        }catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    public function deleleSubFreight($id,$freight_id,$merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            if($id==0)
            {
                //只删除子模板
                $model_subfreight=ShopSubfreight::model()->findByPk($freight_id);
                $model_subfreight->flag=FLAG_YES;
                $model_subfreight->last_time=date('Y-m-d H:i:s', time());
                if($model_subfreight -> save()){
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }else{
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            }
            else if($freight_id==0)
            {
                //删除母模板
                $model_freight=ShopFreight::model()->findByPk($id);
                $model_freight->flag=FLAG_YES;
                $model_freight->last_time=date('Y-m-d H:i:s', time());
                if($model_freight -> save()){
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }else{
                    $result ['status'] = ERROR_SAVE_FAIL;
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 保存时查询模板名称是否已经相同
     * merchantId 商户ID
     * freightName 模板名称
     * type add是添加时检测，update是编辑时检测
     * mobanid 编辑的模板ID
     */
    public function checkFreightName($merchantId,$freightName,$type,$mobanid='')
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition ( 'merchant_id=:merchant_id and name=:name and flag=:flag' );
            $criteria->params = array (
                ':merchant_id' => $merchantId,
                ':name'=>$freightName,
                ':flag' => FLAG_NO
            );
            //数据库查询
            $model=ShopFreight::model()->findAll($criteria);
            if($type=='add') {
                if (!empty($model)) {
                    $result ['status'] = ERROR_EXCEPTION;
                    $result['errMsg'] = '模板名称重复'; //错误信息
                } else {
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }
            }
            else if($type=='update')
            {
                $moban_flag=false;
                if(!empty($model))
                {
                    foreach($model as $k=>$v)
                    {
                        if($v['id']!=$mobanid&&$v['name']==$freightName)
                        {
                            $moban_flag=true;
                        }
                    }
                }
                if ($moban_flag) {
                    $result ['status'] = ERROR_EXCEPTION;
                    $result['errMsg'] = '模板名称重复'; //错误信息
                } else {
                    $result ['status'] = ERROR_NONE;
                    $result['errMsg'] = ''; //错误信息
                }
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 检查运费模板是否正在被使用
     */
    public function FreightIsUsing($merchantId,$frieghtId){
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition ( 'merchant_id=:merchant_id and freight_type=:freight_type and flag=:flag' );
            $criteria->params = array (
                ':merchant_id' => $merchantId,
                ':freight_type'=>SHOP_FREIGHT_TYPE_MODEL,
                ':flag' => FLAG_NO
            );
            //数据库查询
            $model=ShopProduct::model()->findAll($criteria);
            $flag=false;
            if(!empty($model)){
                foreach($model as $key=>$value)
                {
                    if($value['freight_id']==$frieghtId){
                        $flag=true;
                    }
                }
            }
            if($flag)
            {
                //该模板正在被使用
                $result ['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = '该模板正在被使用'; //错误信息
            }
            else
            {
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}
?>