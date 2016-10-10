<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/4/20
 * Time: 11:43
 */
class DepartmentC extends mainClass
{
    public $page = null;

    /**
     * 新建部门
     * @param $company_id
     * @param $department_name
     * @param $department_tel
     * @param $department_fax
     * @param $department_function
     * @return string
     */
    public function addDepartment($company_id, $department_name, $department_tel,
                                  $department_fax, $department_function)
    {
        $result = array();
        $model = new Department();
        $model->company_id = $company_id;
        $model->name = $department_name;
        $model->tel = $department_tel;
        $model->fax = $department_fax;
        $model->function = $department_function;
        $model->create_time = new CDbExpression('NOW()');
        $model->last_time = date('Y-m-d H:i:s');
        if ($model->save()) {
            $result ['status'] = ERROR_NONE;
            $result ['id'] = $model->id;
            $result ['errMsg'] = '';
        } else {
            $result ['status'] = ERROR_SAVE_FAIL;
            $result ['errMsg'] = '数据保存失败';
        }
        return json_encode($result);
    }

    /**
     * 获取下拉数据
     */
    public function findListDate()
    {
        $date = CHtml::listData(Company::model()->findAll('flag = :flag',array(':flag' => FLAG_NO)), 'id', 'name');
        return json_encode($date);
    }

    /**
     * 部门列表
     * @return mixed
     */
    public function findDepartmentList()
    {
        $result = array();
        try {
            //分页
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = "create_time desc";

            $pages = new CPagination(Department::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);

            $result = Department::model()->findAll($criteria);
            
            $data = array();
            if (!empty($result)) {
                foreach ($result as $k => $v) {
                    $data[$k]['id'] = $v->id;
                    $data[$k]['name'] = $v->name;
                    $data[$k]['tel'] = $v->tel;
                    $data[$k]['fax'] = $v->fax;
                    $data[$k]['if_del'] = $this->checkDepartmentDel($v->id) == false ? "2" : "1";//是否显示删除按钮
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        $this->page = $pages;
        return json_encode($result);
    }
    //私有方法：检查部门是下是否有岗位
    private function checkDepartmentDel($department_id){
        $post = Post::model() -> find('department_id =:department_id and flag =:flag',array(
            ':department_id' => $department_id,
            ':flag' => FLAG_NO
        ));
        //非空，则不可被删除
        if(!empty($post)){
            return false;
        }else{
            return true;
        }
    }


    /**
     * 删除部门信息
     * @param $id
     */
    public function deleteDepartmentInfo($id)
    {
        $result = array();
        $model = Department::model()->findByPk($id);
        if ($model->flag == FLAG_NO) {
            $model->flag = FLAG_YES;
            if ($model->save()) {
                $result ['status'] = ERROR_NONE;
                $result ['errMsg'] = '';
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                $result ['errMsg'] = '数据保存失败'; // 错误信息
            }
        }
        return json_encode($result);
    }

    /**
     * 部门详细信息
     * @param $id
     * @return mixed
     */
    public function departmentDetail($id)
    {
        $result = Department::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * 编辑部门信息
     * @param $department_id
     * @param $company_id
     * @param $department_name
     * @param $department_tel
     * @param $department_fax
     * @param $department_function
     * @return string
     */
    public function editDepartment($department_id, $company_id, $department_name, $department_tel, $department_fax, $department_function)
    {
        $result = array();
        try {
            //验证参数
            if (!isset($department_id) && empty($department_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数department_id缺失");
            }
            //保存数据
            $model = Department::model()->findByPk($department_id);
            $model->company_id = $company_id;
            $model->name = $department_name;
            $model->tel = $department_tel;
            $model->fax = $department_fax;
            $model->function = $department_function;
            $model->last_time = date('Y-m-d H:i:s');
            if ($model->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] = $model->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                throw new Exception('数据保存失败');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}