<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/5/9
 * Time: 10:36
 */
class SystemNoticeC extends mainClass
{
    public $page = null;
    /**
     * @var null
     * 新建内部公告
     */
    public function addInsideNotice($title,$content){


            $notice = new SystemNotice();

            $notice->title = $title;
            $notice->content = $content;
            $notice->staff_id = Yii::app() -> session['staff_id'];
            $notice->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
            $notice->release_to = SYSTEM_NOTICE_RELEASE_TO_OA;
            $notice->create_time = new CDbExpression('NOW()');
            if ($notice->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $notice->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }
        

        return json_encode($result);
    }

    /**
     * @return string
     * 获取内部公告
     */
    public function findInsideNoticeList($title=NULL,$create_time=NULL){
        $result = array();
        try{
        //分页
        $criteria = new CDbCriteria();
            //关键字查找
            if ($title !== '' && $title !== NULL){
                $criteria -> addCondition('title like :title');
                $criteria->params[':title'] = '%' . $title . '%';
            }
            if ($create_time !== '' && $create_time !== NULL){
                $criteria -> addCondition('create_time like :create_time');
                $criteria->params[':create_time'] = '%' . $create_time . '%';
            }
            
        $criteria->addCondition("flag = :flag and release_to = :release_to");
        $criteria->params[':flag']=FLAG_NO;
        $criteria->params[':release_to']=SYSTEM_NOTICE_RELEASE_TO_OA;
        $criteria->order = "create_time desc";

        $pages = new CPagination(SystemNotice::model()->count($criteria));
        $pages->pageSize = Yii::app()->params['perPage'];
        $pages->applyLimit($criteria);

        $result = SystemNotice::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['staff_id'] = $v -> staff -> name;
                    $data[$k]['title'] = $v -> title;
                    $data[$k]['content'] = $v -> content;
                    $data[$k]['status'] = $v -> status;
                    $data[$k]['release_to'] = $v -> release_to;
                    $data[$k]['release_time'] = $v -> release_time;
                    $data[$k]['create_time'] = $v -> create_time;
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        $this->page = $pages;
        return json_encode($result);
    }
    /**
     * @var null
     * 新建分销公告
     */
    public function addFxNotice($title,$content){

        
            $notice = new SystemNotice();

            $notice->title = $title;
            $notice->content = $content;
            $notice->staff_id = Yii::app() -> session['staff_id'];
            $notice->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
            $notice->release_to = SYSTEM_NOTICE_RELEASE_TO_FX;
            $notice->create_time = new CDbExpression('NOW()');
            if ($notice->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $notice->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }
        

        return json_encode($result);
    }
    /**
     * @return string
     * 获取分销公告
     */
    public function findFxNoticeList($title=NULL,$create_time=NULL){
        $result = array();
        try{
            //分页
            $criteria = new CDbCriteria();
            //关键字查找
            if ($title !== '' && $title !== NULL){
                $criteria -> addCondition('title like :title');
                $criteria->params[':title'] = '%' . $title . '%';
            }
            if ($create_time !== '' && $create_time !== NULL){
                $criteria -> addCondition('create_time like :create_time');
                $criteria->params[':create_time'] = '%' . $create_time . '%';
            }

            $criteria->addCondition("flag = :flag and release_to = :release_to");
            $criteria->params[':flag']=FLAG_NO;
            $criteria->params[':release_to']=SYSTEM_NOTICE_RELEASE_TO_FX;
            $criteria->order = "create_time desc";

            $pages = new CPagination(SystemNotice::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);

            $result = SystemNotice::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['staff_id'] = $v -> staff['name'];
                    $data[$k]['title'] = $v -> title;
                    $data[$k]['content'] = $v -> content;
                    $data[$k]['status'] = $v -> status;
                    $data[$k]['release_to'] = $v -> release_to;
                    $data[$k]['release_time'] = $v -> release_time;
                    $data[$k]['create_time'] = $v -> create_time;
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        $this->page = $pages;
        return json_encode($result);
    }

    /**
     * @param $id
     * @return string
     * 发布内部公告
     */
    public function releaseInsideNotice($id){

        $notice = SystemNotice::model()->findByPk($id);

        $notice->status = SYSTEM_NOTICE_STATUS_RELEASE;
        $notice->release_time = date('Y-m-d H:i:s');
        if ($notice->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
    /**
     * @param $id
     * @return string
     * 取消发布内部公告
     */
    public function unreleaseInsideNotice($id){

        $notice = SystemNotice::model()->findByPk($id);

        $notice->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
        $notice->release_time = date('Y-m-d H:i:s');
        if ($notice->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
    /**
     * @param $id
     * @return string
     * 发布分销公告
     */
    public function releaseFxNotice($id){

        $notice = SystemNotice::model()->findByPk($id);

        $notice->status = SYSTEM_NOTICE_STATUS_RELEASE;
        $notice->release_time = date('Y-m-d H:i:s');
        if ($notice->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
    /**
     * @param $id
     * @return string
     * 取消发布分销公告
     */
    public function unreleaseFxNotice($id){

        $notice = SystemNotice::model()->findByPk($id);

        $notice->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
        $notice->release_time = date('Y-m-d H:i:s');
        if ($notice->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }

    /**
     * 内部公告详细信息
     * @param $id
     * @return string
     */
    public function insideNotice($id)
    {
        $result = SystemNotice::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * @var null
     * 编辑内部公告
     */
    public function editInsideNotice($id,$title,$content){


            $notice = SystemNotice::model()->findByPk($id);

            $notice->title = $title;
            $notice->content = $content;
            $notice->staff_id = Yii::app() -> session['staff_id'];
            $notice->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
            $notice->last_time = date('Y-m-d H:i:s');

            if ($notice->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $notice->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }


        return json_encode($result);
    }

    /**
     * 分销公告详细信息
     * @param $id
     * @return string
     */
    public function fxNotice($id)
    {
        $result = SystemNotice::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * @var null
     * 编辑分销公告
     */
    public function editFxNotice($id,$title,$content){


            $notice = SystemNotice::model()->findByPk($id);

            $notice->title = $title;
            $notice->content = $content;
            $notice->staff_id = Yii::app() -> session['staff_id'];
            $notice->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
            $notice->last_time = date('Y-m-d H:i:s');

            if ($notice->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $notice->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }
        

        return json_encode($result);
    }

    /**
     * @param $id
     * @return string
     * 删除内部公告
     */
    public function deleteInsideNotice($id)
    {
        $result = array();
        $model = SystemNotice::model()->findByPk($id);
        if($model -> flag == FLAG_NO) {
            $model->flag = FLAG_YES;
            if ($model->save()) {
                $result ['status'] = ERROR_NONE;
                $result ['id'] =  $model->id;
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
     *批量删除内部公告
     */
    public function delMoreInsideNotice($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = SystemNotice::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->flag=FLAG_YES;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('公告删除失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该公告不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                //添加日志记录
                $oa_log = new OaLogC();
                $arr = json_encode(array(OA_OPERATION_LOG_OBJECT_SYSTEM_NOTICE,$del_arr));
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
     * @param $id
     * @return string
     * 删除分销公告
     */
    public function deleteFxNotice($id)
    {
        $result = array();
        $model = SystemNotice::model()->findByPk($id);
        if($model -> flag == FLAG_NO) {
            $model->flag = FLAG_YES;
            if ($model->save()) {
                $result ['status'] = ERROR_NONE;
                $result ['id'] =  $model->id;
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
     *批量删除分销公告
     */
    public function delMoreFxNotice($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = SystemNotice::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->flag=FLAG_YES;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('公告删除失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该公告不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                //添加日志记录
                $oa_log = new OaLogC();
                $arr = json_encode(array(OA_OPERATION_LOG_OBJECT_SYSTEM_NOTICE,$del_arr));
                $oa_log->addLog(OA_OPERATION_LOG_TYPE_DEL,$arr);
                $result['errMsg'] = ''; //错误信息
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}