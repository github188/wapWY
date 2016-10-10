<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/4/21
 * Time: 9:47
 */
class StaffC extends mainClass
{
    public $page = null;
    private static $_instance = null;
    public static function getInstance(){
        if (!self::$_instance instanceof self){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * 新建人员
     * @param $staff_account
     * @param $staff_pwd
     * @param $staff_name
     * @param $staff_sex
     * @param $company_id
     * @param $department_id
     * @param $post_id
     * @param $staff_tel
     * @param $staff_qq
     * @return string
     */
    public function addStaff($staff_account,$staff_pwd,$staff_name,$staff_sex,$company_id,
        $department_id,$post_id,$staff_tel,$staff_qq)
    {
        $result = array();
        try {
            //验证参数
            $model = Staff::model()->find('account = :account AND flag = :flag',array(
                ':account' =>  $staff_account,
                ':flag' => FLAG_NO
            ));
            if ($model){
                $result['status'] = ERROR_DUPLICATE_DATA;
                throw new Exception("账号重复");
            }
            //保存数据
            $model = new Staff();
            $model->account = $staff_account;
            $model->pwd = md5($staff_pwd);
            $model->name = $staff_name;
            $model->sex = $staff_sex;
            $model->company_id = $company_id;
            $model->department_id = $department_id;
            $model->post_id = $post_id;
            $model->tel = $staff_tel;
            $model->qq = $staff_qq;
            $model->create_time = date('Y-m-d H:i:s');
            $model->last_time = date('Y-m-d H:i:s');
            if ($model->save()) {
                $result ['status'] = ERROR_NONE;
                $result ['id'] =  $model->id;
                $result ['errMsg'] = '';
            } else {
                $result ['status'] = ERROR_SAVE_FAIL;
                throw new Exception("数据保存失败");
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    /**
     * 获取所属公司下拉数据
     */
    public function findListComDate()
    {
        $date = CHtml::listData(Company::model()->findAll('flag = :flag',array(':flag' => FLAG_NO)), 'id', 'name');
        return json_encode($date);
    }
    /**
     * 获取所属部门下拉数据
     */
    public function findListDepDate()
    {
        $date = CHtml::listData(Department::model()->findAll('flag = :flag',array(':flag' => FLAG_NO)), 'id', 'name');
        return json_encode($date);
    }
    /**
     * 获取所属岗位下拉数据
     */
    public function findListPostDate()
    {
        $date = CHtml::listData(Post::model()->findAll('flag = :flag',array(':flag' => FLAG_NO)), 'id', 'name');
        return json_encode($date);
    }

    /**
     * 左侧菜单列表
     * @return string
     */
    public function getLeft(){
        $arr = array();
        $company = Company::model() ->findAll('flag = :flag',array(':flag' => FLAG_NO,));
        foreach ($company as $k => $v){
            $arr[$k]['id'] = $v -> id;
            $arr[$k]['name'] = $v -> name;
            $dep = Department::model() -> findAll('company_id =:company_id and flag = :flag' ,array(
                ':company_id' => $v -> id,
                ':flag' => FLAG_NO,
            ));
            $arr[$k]['data'] = array();
            foreach ($dep as $a => $b){
                $arr[$k]['data'][$a]['id'] = $b -> id;
                $arr[$k]['data'][$a]['name'] = $b -> name;
                $staff = Staff::model() -> findAll('department_id =:department_id and flag = :flag',array(
                    ':department_id' => $b -> id,
                    ':flag' => FLAG_NO,
                ));
                $arr[$k]['data'][$a]['data'] = array();
                foreach ($staff as $i => $j){
                    $arr[$k]['data'][$a]['data'][$i]['id'] = $j -> id;
                    $arr[$k]['data'][$a]['data'][$i]['name'] = $j -> name;
                }
            }
        }


        return json_encode($arr);
    }





    /**
     * 人员列表
     * @return mixed
     */
    public function findStaffList()
    {
        $result = array();
        try{
            //分页
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag']=FLAG_NO;
            $criteria->order = "create_time desc";

            $pages = new CPagination(Staff::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;
            $result = Staff::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['account'] = $v -> account;
                    $data[$k]['name'] = $v -> name;
                    $data[$k]['sex'] = $v -> sex;
                    $data[$k]['company_id'] = $v -> company_id;
                    $data[$k]['department_id'] = $v -> department -> name;
                    $data[$k]['post_id'] = $v -> post -> name;
                    $data[$k]['tel'] = $v -> tel;
                    $data[$k]['qq'] = $v -> qq;
                    $data[$k]['last_time'] = $v -> last_time;
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;


        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 列表人员用户
     * @return mixed
     */
    public function findStaffName($post_id)
    {
        $result = array();
        try{
            $criteria = new CDbCriteria();
            $criteria->addCondition("post_id = :post_id");
            $criteria->params=array(':post_id'=>$post_id);

            $result = Staff::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['account'] = $v -> account;
                    $data[$k]['name'] = $v -> name;
                    $data[$k]['sex'] = $v -> sex;
                    $data[$k]['company_id'] = $v -> company_id;
                    $data[$k]['department_id'] = $v -> department -> name;
                    $data[$k]['post_id'] = $v -> post -> name;
                    $data[$k]['tel'] = $v -> tel;
                    $data[$k]['qq'] = $v -> qq;
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;


        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * @param $del_arr
     * @return string
     *批量删除人员信息
     */
    public function delStaff($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = Staff::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->flag=FLAG_YES;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('人员信息删除失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该人员信息不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                //添加日志记录
                $oa_log = new OaLogC();
                $arr = json_encode(array(OA_OPERATION_LOG_OBJECT_STAFF,$del_arr));
                $oa_log->addLog(OA_OPERATION_LOG_TYPE_DEL,$arr);
                $result['errMsg'] = ''; //错误信息
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    /**
     * 重置密码
     */
    public function resPwd($res_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($res_arr)) {
                $count = count($res_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = Staff::model()->findByPk($res_arr[$i]);
                    $model->pwd = md5(123456);
                    $model->last_time = date('Y-m-d H:i:s');
                    $model->save();
                }

                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 人员详情
     */
    public function staffDetail($id)
    {
        $result = Staff::model()->findByPk($id);
        $cName = Company::model()->findByPk($result->company_id);
        $result->company_id = !empty($cName)  ? $cName->name : '';
        $dName = Department::model()->findByPk($result->department_id);
        $result->department_id = !empty($dName)  ? $dName->name : '';
        $pName = Post::model()->findByPk($result->post_id);
        $result->post_id = !empty($pName)  ? $pName->name : '';
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * 编辑人员信息
     * @param $staff_id
     * @param $staff_account
     * @param $staff_pwd
     * @param $staff_name
     * @param $staff_sex
     * @param $company_id
     * @param $department_id
     * @param $post_id
     * @param $staff_tel
     * @param $staff_qq
     * @return string
     */
    public function editStaff($staff_id,$staff_account,$staff_pwd,$staff_name,$staff_sex,
        $company_id,$department_id,$post_id,$staff_tel,$staff_qq)
    {
        $result = array();
        try {
            //验证参数
            if (!isset($staff_id) && empty($staff_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数staff_id缺失");
            }
            //保存数据
            $model = Staff::model()->findByPk($staff_id);
            $model->account = $staff_account;
            $model->name = $staff_name;
            $model->sex = $staff_sex;
            $model->company_id = $company_id;
            $model->department_id = $department_id;
            $model->post_id = $post_id;
            $model->tel = $staff_tel;
            $model->qq = $staff_qq;
            if ($model->save()) {
                $result ['status'] = ERROR_NONE;
                $result ['id'] =  $model->id;
                $result ['errMsg'] =  '';
            } else {
                $result ['status'] = ERROR_SAVE_FAIL;
                throw new Exception('数据保存失败');
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
    
    /**
     * 员工登录
     * @author ly
     * $account 账号 必填
     * $pwd 密码 必填
     * */
    public function Login($account,$pwd){
        $result = array();
        $date = array();
        try {
            //参数验证
            if(!isset($account) || empty($account)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写账号');
            }
            if(!isset($pwd) || empty($pwd)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写密码');
            }
            //验证帐密
            $staff_res = Staff::model() -> find('account = :account and flag = :flag',array(
                ':account' => $account,
                ':flag' => FLAG_NO
            ));
            if($staff_res){
                if ($staff_res->pwd !== $pwd){
                    throw new Exception('登录失败，账号或密码不正确');
                }
                $staff_res->last_time = date('Y-m-d H:i:s');
                if ($staff_res->save()){
                    foreach ($staff_res as $k=>$v){
                        $date[$k] = $v;
                    }
                    $date['company_name'] = '';
                    $date['department_name'] = '';
                    $date['post_name'] = '';
                    $date['right'] = '';
                    //获取公司名
                    if (!empty($staff_res->company_id)){
                        $res = Company::model()->findByPk($staff_res->company_id);
                        if ($res){
                            $date['company_name'] = $res->name;
                        }
                    }
                    //获取部门名
                    if (!empty($staff_res->department_id)){
                        $res = Department::model()->findByPk($staff_res->department_id);
                        if ($res){
                            $date['department_name'] = $res->name;
                        }
                    }
                    //获取岗位名
                    if (!empty($staff_res->post_id)){
                        $res = Post::model()->findByPk($staff_res->post_id);
                        if ($res){
                            $date['post_name'] = $res->name;
                            $date['right'] = $res->right;
                        }
                    }
                    $result['date'] = $date;
                    $result['status'] = ERROR_NONE;
                    $result['errMsg'] = '';
                }else{
                    throw new Exception('登录更新失败');
                }
            }else{
                throw new Exception('登录失败，账号或密码不正确');
            }
        }catch (Exception $e){
            $result['date'] = '';
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 修改密码
     * @param $newpwd 新密码
     * @return string
     */
    public function changePwd($newpwd){
        $staff = Staff::model()->findByPk(Yii::app() -> session['staff_id']);
        $staff->pwd = md5($newpwd);
        $staff->last_time = date('Y-m-d H:i:s');
        if ($staff->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
}