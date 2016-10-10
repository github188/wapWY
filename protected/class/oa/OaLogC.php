<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/5/6
 * Time: 20:31
 */
class OaLogC extends mainClass
{
    public $page = null;
    
    /**
     * @param $operation
     * @param $object
     * @return string
     * 添加日志记录
     */
    public function addLog($operation,$object){
        $result = array();
        try {
            //参数验证
            if (!isset($operation) && empty($operation)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数operation缺失");
            }
            if (!isset($object) && empty($object)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数object缺失");
            }
            //添加日志
            $oa_log = new OaLog();
            $oa_log->staff_id = Yii::app()->session['staff_id'];
            $oa_log->operation = $operation;
            $oa_log->object = $object;
            $oa_log->ip = $_SERVER["REMOTE_ADDR"];
            $oa_log->operation_time = new CDbExpression('NOW()');
            $oa_log->create_time = new CDbExpression('NOW()');
            if($oa_log->save()){
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['errMsg'] = '';
            }else{
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('保存数据失败');
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取日志列表
     * @author ly
     * @throws Exception
     */
    public function findLog(){
        $result = array();
        try{
            $staff_right = Yii::app()->session['staff_right'];
            if (empty($staff_right)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数staff_right缺失");
            }
            //获取日志权限类型
            $staff_right = json_decode($staff_right,true);
            $type = $this->getOaLogObRightType($staff_right);
            if ($type == 1){
                $result['status'] = ERROR_NO_DATA;
                throw new Exception("没有权限");
            }
            //查询日志
            $staff_id_array = array();
            $criteria = new CDbCriteria();
            $staff_id = Yii::app()->session['staff_id'];
            $staff_ob = Staff::model()->findByPk($staff_id);
            switch ($type){
                case 2: // 查看所有日志
                    break;
                case 3: // 查看公司日志
                    $company_id = $staff_ob -> company_id;
                    $staff_all_ob = Staff::model()->findAll('company_id = :company_id AND flag = :flag',
                        array(':company_id' => $company_id, ':flag' => FLAG_NO));
                    
                    if (!empty($staff_all_ob)){
                        foreach ($staff_all_ob as $k => $v){
                            $staff_id_array[$k] = $v['id'];
                        }
                    }
                    $criteria->addInCondition("staff_id",$staff_id_array);
                    break;
                case 4: // 查看部门日志
                    $department_id = $staff_ob -> department_id;
                    $staff_all_ob = Staff::model()->findAll('department_id = :department_id AND flag = :flag',
                        array(':department_id' => $department_id, ':flag' => FLAG_NO));
                    if (!empty($staff_all_ob)){
                        foreach ($staff_all_ob as $k => $v){
                            $staff_id_array[$k] = $v['id'];
                        }
                    }
                    $criteria->addInCondition("staff_id",$staff_id_array);
                    break;
                case 5: // 查看个人日志
                    $criteria->addCondition("staff_id = :staff_id");
                    $criteria->params[':staff_id'] = $staff_id;
                    break;
            }
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = "create_time desc";
           
            //分页
            $pages = new CPagination(OaLog::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            
            $result = OaLog::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['staff_id'] = $v -> staff['name'];
                    $data[$k]['operation'] = $v -> operation;
                    $data[$k]['object'] = $this->getOaLogObjectName($v -> object);//获取操作对象名称
                    $data[$k]['ip'] = $v -> ip;
                    $data[$k]['operation_time'] = $v -> operation_time;
                }
            }
            $this->page = $pages;
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        
        return json_encode($result);
    }
    /**
     * 获取日志操作对象名称
     * @author ly
     * @param unknown $json_array
     */
    private function getOaLogObjectName($json_array){
        $arr = json_decode($json_array,true);
        $objectName = '';
        if (!empty($arr[0]) && !empty($arr[1])){
            $table = $arr[0];
            $id = $arr[1];
            switch ($table){
                case 1: // 公司
                    if (is_array($id)){
                        $objectName = '[公司] ';
                        foreach ($id as $v){
                            $model = Company::model()->findByPk($v);
                            if (!empty($model)){
                                $objectName .= ($model -> name).',';
                            };
                        }
                        $objectName = substr($objectName, 0,strlen($objectName)-1);
                    }else{
                        $model = Company::model()->findByPk($id);
                        if (!empty($model)){
                            $objectName = '[公司] '.$model -> name;
                        };
                    }
                    break;
                case 2: // 部门
                    if (is_array($id)){
                        $objectName = '[部门] ';
                        foreach ($id as $v){
                            $model = Department::model()->findByPk($v);
                            if (!empty($model)){
                                $objectName .= ($model -> name).',';
                            };
                        }
                        $objectName = substr($objectName, 0,strlen($objectName)-1);
                    }else{
                        $model = Department::model()->findByPk($id);
                        if (!empty($model)){
                            $objectName = '[部门] '.$model -> name;
                        };
                    }
                    break;
                case 3: // 岗位
                    if (is_array($id)){
                        $objectName = '[岗位] ';
                        foreach ($id as $v){
                            $model = Post::model()->findByPk($v);
                            if (!empty($model)){
                                $objectName .= ($model -> name).',';
                            };
                        }
                        $objectName = substr($objectName, 0,strlen($objectName)-1);
                    }else{
                        $model = Post::model()->findByPk($id);
                        if (!empty($model)){
                            $objectName = '[岗位] '.$model -> name;
                        };
                    }
                    break;
                case 4: // 人员
                    if (is_array($id)){
                        $objectName = '[人员] ';
                        foreach ($id as $v){
                            $model = Staff::model()->findByPk($v);
                            if (!empty($model)){
                                $objectName .= ($model -> name).',';
                            };
                        }
                        $objectName = substr($objectName, 0,strlen($objectName)-1);
                    }else{
                        $model = Staff::model()->findByPk($id);
                        if (!empty($model)){
                            $objectName = '[人员] '.$model -> name;
                        };
                    }
                    break;
                case 5: // 系统公告
                    if (is_array($id)){
                        $objectName = '[公告] ';
                        foreach ($id as $v){
                            $model = SystemNotice::model()->findByPk($v);
                            if (!empty($model)){
                                $objectName .= ($model -> title).',';
                            };
                        }
                        $objectName = substr($objectName, 0,strlen($objectName)-1);
                    }else{
                        $model = SystemNotice::model()->findByPk($id);
                        if (!empty($model)){
                            $objectName = '[公告] '.$model -> title;
                        };
                    }
                    break;
                case 6: // 资料下载
                    if (is_array($id)){
                        $objectName = '[资料] ';
                        foreach ($id as $v){
                            $model = DataDownload::model()->findByPk($v);
                            if (!empty($model)){
                                $objectName .= ($model -> title).',';
                            };
                        }
                        $objectName = substr($objectName, 0,strlen($objectName)-1);
                    }else{
                        $model = DataDownload::model()->findByPk($id);
                        if (!empty($model)){
                            $objectName = '[资料] '.$model -> title;
                        };
                    }
                    break;
            }
        }
        return $objectName;
    }
    
    /**
     * 获取日志查看权限类型
     * @author ly
     * @param unknown $staff_id
     */
    private function getOaLogObRightType($staff_right){
        $type = 1;
        if (in_array('s007', $staff_right)){// 查看全部日志
            $type = 2;
        }elseif(in_array('s006', $staff_right)){// 查看公司日志
            $type = 3;
        }elseif(in_array('s005', $staff_right)){// 查看部门日志
            $type = 4;
        }elseif(in_array('s004', $staff_right)){// 查看个人日志
            $type = 5;
        }
        return $type;
    }
}