<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/4/17
 * Time: 21:32
 */
class CompanyC extends mainClass
{
    public $page = null;

    /**
     * @param $company_name
     * @param $company_type
     * @param $pid
     * @param $company_tel
     * @param $company_fax
     * @param $company_address
     * @param $company_website
     * @param $company_email
     * @param $company_profile
     * @return string
     *
     * 新建公司
     */
    public function addCompany($company_name, $company_type, $pid, $company_tel,
                               $company_fax, $company_address, $company_website, $company_email, $company_profile)
    {
        $result = array();
        $model = new Company();
        $model->name = $company_name;
        $model->type = $company_type;
        $model->pid = $pid;
        $model->tel = $company_tel;
        $model->fax = $company_fax;
        $model->address = $company_address;
        $model->website = $company_website;
        $model->email = $company_email;
        $model->company_profile = $company_profile;
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
     * 获取全部公司名称
     */
    public function allCompanyName()
    {
        $allcompanyname = Company::model()->findAll();
        $data = array();
        if(!empty($allcompanyname))
        foreach ($allcompanyname as $k =>$v){
            $data[$k]= $v -> name;
        }
        $result = $data;
        return $result;
    }

    /**
     * 验证公司名称是否重复
     */
    public function countName($name)
    {
        $date = Company::model()->findAll('name = :name',array(':name'=> $name));
        return json_encode($date);
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
     * 公司列表
     */
    public function findCompanyList()
    {
        $result = array();
        try {
            //分页
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = "create_time desc";

            $pages = new CPagination(Company::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);

            $company = Company::model()->findAll($criteria);

            $data = array();
            if (!empty($company)) {
                foreach ($company as $k => $v) {
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['name'] = $v -> name;
                    $data[$k]['type'] = $v -> type;
                    $data[$k]['tel'] = $v -> tel;
                    $data[$k]['address'] = $v -> address;
                    $data[$k]['website'] = $v -> website;
                    $data[$k]['email'] = $v -> email;
                    $data[$k]['if_del'] = $this -> checkCompanyDel($v -> id) == false?"2":"1";//是否显示删除按钮
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
    //私有方法：检查公司是下是否有部门
    private function checkCompanyDel($company_id){
        $dep = Department::model() -> find('company_id =:company_id and flag =:flag',array(
            ':company_id' => $company_id,
            ':flag' => FLAG_NO
        ));
        //非空，则不可被删除
        if(!empty($dep)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 删除公司信息
     * @param $id
     */
    public function deleteCompanyInfo($id)
    {
        $result = array();
        $model = Company::model()->findByPk($id);
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
     * 公司详细信息
     * @param $id
     * @return string
     */
    public function companyDetail($id)
    {
        $result = Company::model()->findByPk($id);
        $pName = Company::model()->findByPk($result->pid);
        $result = json_decode(CJSON::encode($result), TRUE);
        $result['pName'] = !empty($pName) ? $pName->name : '';
        return $result;
    }

    /**
     * 编辑公司信息
     */
    public function editCompany($company_id, $company_name, $company_type, $company_p_id, $company_tel,
                                $company_fax, $company_address, $company_website, $company_email, $company_profile)
    {
        $result = array();
        try {
            //验证参数
            if (!isset($company_id) && empty($company_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数company_id缺失");
            }
            //保存数据
            $model = Company::model()->findByPk($company_id);
            $model->name = $company_name;
            $model->type = $company_type;
            $model->pid = $company_p_id;
            $model->tel = $company_tel;
            $model->fax = $company_fax;
            $model->address = $company_address;
            $model->website = $company_website;
            $model->email = $company_email;
            $model->company_profile = $company_profile;
            $model->last_time = date('Y-m-d H:i:s');

            if ($model->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] = $model->id;
                $result ['errMsg'] = '';
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