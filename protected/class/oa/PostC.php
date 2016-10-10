<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/4/20
 * Time: 16:37
 */
class PostC extends mainClass
{
    public $page = null;
    /**
     * 新建岗位
     * @param $company_id
     * @param $department_id
     * @param $post_name
     * @return string
     */
    public function addPost($company_id, $department_id, $post_name)
    {
        $result = array();
        $model = new Post();
        $model->company_id = $company_id;
        $model->department_id = $department_id;
        $model->name = $post_name;
        $model->create_time = new CDbExpression('NOW()');
        $model->last_time = date('Y-m-d H:i:s');
        if ($model->save()) {
            $result ['status'] = ERROR_NONE;
            $result ['id'] =  $model->id;
            $result ['errMsg'] = '';
        } else {
            $result ['status'] = ERROR_SAVE_FAIL;
            $result ['errMsg'] = '数据保存失败';
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
     * 岗位列表
     * @return mixed
     */
    public function findPostList()
    {
        $result = array();
        try{
        //分页
        $criteria = new CDbCriteria();
        $criteria->addCondition("flag = :flag");
        $criteria->params[':flag']=FLAG_NO;
        $criteria->order = "create_time desc";

        $pages = new CPagination(Post::model()->count($criteria));
        $pages->pageSize = Yii::app()->params['perPage'];
        $pages->applyLimit($criteria);
        $this->page = $pages;

        $result = Post::model()->findAll($criteria);
        $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['company_id'] = $v -> company_id;
                    $data[$k]['department_id'] = $v -> department_id;
                    $data[$k]['name'] = $v -> name;
                    $data[$k]['if_del'] = $this -> checkPostDel($v -> id) == false?"2":"1";//是否显示删除按钮
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

    //私有方法：检查公司是下是否有部门
    private function checkPostDel($post_id){
        $dep = Staff::model() -> find('post_id =:post_id and flag =:flag',array(
            ':post_id' => $post_id,
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
     * 删除岗位信息
     * @param $id
     */
    public function deletePostInfo($id)
    {
        $result = array();
        $model = Post::model()->findByPk($id);
        if($model -> flag == FLAG_NO) {
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
     * @param $del_arr
     * @return string
     *批量删除岗位信息
     */
    public function delPost($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = Post::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->flag=FLAG_YES;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('岗位删除失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该岗位不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                $oa_log = new OaLogC();
                $arr = json_encode(array(OA_OPERATION_LOG_OBJECT_POST,$del_arr));
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
     * 岗位详细信息
     * @param $id
     * @return mixed
     */
    public function postDetail($id)
    {
        $result = Post::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * 编辑岗位信息
     * @return string
     */
    public function editPost($post_id,$company_id, $department_id, $post_name)
    {
        $result = array();
        try {
            //验证参数
            if (!isset($post_id) && empty($post_id)){
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception("参数post_id缺失");
            }
            //保存数据
            $model = Post::model()->findByPk($post_id);
            $model->company_id = $company_id;
            $model->department_id = $department_id;
            $model->name = $post_name;
            $model->last_time = date('Y-m-d H:i:s');
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
     * 设置业务范围及功能权限
     */
    public function addAuthority($id, $check, $checkbox){
        $post = Post::model()->findByPk($id);
        $check = json_encode($check);
        $checkbox = json_encode($checkbox);
        $post-> right = $checkbox;
        $post-> range = $check;
        if ($post->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }

    /**
     * @return string
     * 获取功能权限
     */
    public function findRight($id){
        $result = Post::model()->findByPk($id);
        return $result['right'];
    }

    /**
     * @param $id
     * @return mixed
     * 获取业务范围
     */
    public function findRange($id){
        $result = Post::model()->findByPk($id);
        return $result['range'];
    }

}