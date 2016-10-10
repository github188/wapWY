<?php

include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 标签类
 */
class LabelC extends mainClass{
    
    /**
     * 添加属性标签
     * @param type $merchant_id
     * @param type $label_name
     * @param type $values
     * @return type
     * @throws Exception
     */
    public function AddLabel($merchant_id, $label_name, $values)
    {
        $result = array();
        try {
            if(empty($merchant_id)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数merchant_id不存在');
            }
            if(empty($label_name)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数label_name不存在');
            }
            if(empty($values)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数values不存在');
            }
            $tag = new Tag();
            $tag -> merchant_id = $merchant_id;
            $tag -> name        = $label_name;
            $tag -> value       = $values;
            $tag -> create_time = new CDbExpression('now()');
            $tag -> type        = TAG_TYPE_ATTR;
            if($tag -> save()){
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 编辑属性标签
     * @param type $merchant_id
     * @param type $label_name
     * @param type $values
     * @param type $id
     * @return type
     * @throws Exception
     */
    public function EditLabel($merchant_id, $label_name, $values, $id)
    {
        $result = array();
        try {
            if(empty($merchant_id)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数merchant_id不存在');
            }
            if(empty($label_name)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数label_name不存在');
            }
            if(empty($values)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数values不存在');
            }
            $tag = Tag::model()->find('flag=:flag and merchant_id=:merchant_id and id=:id',array(
                ':flag' => FLAG_NO,
                ':merchant_id' => $merchant_id,
                ':id' => $id
            ));  
            if($tag){
                $tag -> name        = $label_name;
                $tag -> value       = $values;
                $tag -> last_time   = new CDbExpression('now()');            
                if($tag -> update()){
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['status'] = ERROR_NO_DATA; //状态码
                    $result['errMsg'] = '数据保存失败'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '数据不存在'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 获取编辑标签信息
     */
    public function getLabel($merchant_id, $id)
    {
        $result = array();
        $data = array();
        try {
            if(empty($merchant_id)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数merchant_id不存在');
            }
            if(empty($id)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数id不存在');
            }
            $tag = Tag::model()->find('flag=:flag and merchant_id=:merchant_id and id=:id',array(
                ':flag' => FLAG_NO,
                ':merchant_id' => $merchant_id,
                ':id' => $id
            ));
            if($tag){
                $data['name']        = $tag['name'];
                $data['code']        = $tag['code'];
                $data['value_type']  = $tag['value_type'];
                $data['tag_explain'] = $tag['tag_explain'];                    
                $data['value']       = json_decode($tag['value']);
                $data['logic_type']  = isset($tag['logic_type']) ? $tag['logic_type'] : '';
                $data['parameter']   = isset($tag['parameter']) ? $tag['parameter'] : '';
                $data['if_combination_tag'] = isset($tag['if_combination_tag']) ? $tag['if_combination_tag'] : '';
                $data['type']        = isset($tag['type']) ? $tag['type'] : '';
                $data['create_time'] = $tag['create_time'];
                $data['last_time']   = $tag['last_time'];
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '数据不存在'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 标签列表
     * @param type $merchant_id
     * @return type
     * @throws Exception
     */
    public function LabelList($merchant_id)
    {
        $result = array();
        $data = array();
        try {
            if(empty($merchant_id)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('参数merchant_id不存在');
            }
            $criteria = new CDbCriteria();
            $criteria -> addCondition('flag=:flag and merchant_id=:merchant_id');
            $criteria -> params[':flag'] = FLAG_NO;
            $criteria -> params[':merchant_id'] = $merchant_id;
            $tag = Tag::model()->findall($criteria);
            if($tag){
                foreach($tag as $k => $v) {
                    $data[$k]['id']          = $v['id'];
                    $data[$k]['name']        = $v['name'];
                    $data[$k]['code']        = $v['code'];
                    $data[$k]['value_type']  = $v['value_type'];
                    $data[$k]['tag_explain'] = $v['tag_explain'];                    
                    $data[$k]['value']       = $v['value'];
                    $data[$k]['logic_type']  = isset($v['logic_type']) ? $v['logic_type'] : '';
                    $data[$k]['parameter']   = isset($v['parameter']) ? $v['parameter'] : '';
                    $data[$k]['if_combination_tag'] = isset($v['if_combination_tag']) ? $v['if_combination_tag'] : '';
                    $data[$k]['type']        = isset($v['type']) ? $v['type'] : '';
                    $data[$k]['create_time'] = $v['create_time'];
                    $data[$k]['last_time']   = $v['last_time'];
                }
                $result['data']   = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '数据不存在'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}

