<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/5/9
 * Time: 10:38
 */
class DataDownloadC extends mainClass
{
    public $page = null;
    /**
     * @var null
     * 新建内部资料
     */
    public function addInsideMaterial($title,$type,$download_url,$content){
        

            $material = new DataDownload();

            $material->title = $title;
            $material->type = $type;
            $material->content = $content;
            $material->download_url = $download_url;
            $material->staff_id = Yii::app() -> session['staff_id'];
            $material->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
            $material->release_to = SYSTEM_NOTICE_RELEASE_TO_OA;
            $material->create_time = new CDbExpression('NOW()');
            if ($material->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $material->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }


        return json_encode($result);
    }

    /**
     * @return string
     * 获取内部资料
     */
    public function findInsideMaterialList($title=NULL,$staff_name=NULL,$release_time=NULL,$type=NULL){
        $result = array();
        try{
            //分页
            $criteria = new CDbCriteria();
            //关键字查找
            if ($title !== '' && $title !== NULL){
                $criteria -> addCondition('title like :title');
                $criteria->params[':title'] = '%' . $title . '%';
            }
            if ($staff_name !== '' && $staff_name !== NULL){
                //查找发布人的ID
                $name_list = Staff::model()->findAll('name like :name', array(':name' => '%' .$staff_name. '%'));
                $list = array();
                if (!empty($name_list)) {
                    foreach ($name_list as $k => $v) {
                        $list[$k]['id'] = $v->id;
                    }
                }

                $criteria->addInCondition('staff_id',$list[$k]);

            }
            if ($release_time !== '' && $release_time !== NULL){
                $criteria -> addCondition('release_time like :release_time');
                $criteria->params[':release_time'] = '%' . $release_time . '%';
            }
            if ($type !== '' && $type !== NULL){
                if($GLOBALS['DATA_DOWNLOAD_TYPE'][DATA_DOWNLOAD_TYPE_DOC] == $type) {
                    $data = DATA_DOWNLOAD_TYPE_DOC;
                }

                if($GLOBALS['DATA_DOWNLOAD_TYPE'][DATA_DOWNLOAD_TYPE_VIDEO] == $type) {
                    $data = DATA_DOWNLOAD_TYPE_VIDEO;
                }

                $criteria -> addCondition('type = :type');
                $criteria->params[':type'] = $data ;
            }

            $criteria->addCondition("flag = :flag and release_to = :release_to");
            $criteria->params[':flag']=FLAG_NO;
            $criteria->params[':release_to']=SYSTEM_NOTICE_RELEASE_TO_OA;
            $criteria->order = "weight desc ,create_time desc ";


            $pages = new CPagination(DataDownload::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);

            $result = DataDownload::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['staff_id'] = $v -> staff['name'];
                    $data[$k]['type'] = $v -> type;
                    $data[$k]['title'] = $v -> title;
                    $data[$k]['download_url'] = $v -> download_url;
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
     * 新建分销资料
     */
    public function addFxMaterial($title,$type,$add_to,$download_url,$content){

        
            $material = new DataDownload();

            $material->title = $title;
            $material->type = $type;
            $material->add_to = $add_to;
            $material->content = $content;
            $material->download_url = $download_url;
            $material->staff_id = Yii::app() -> session['staff_id'];
            $material->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
            $material->release_to = SYSTEM_NOTICE_RELEASE_TO_FX;
            $material->create_time = new CDbExpression('NOW()');
            if ($material->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $material->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }
        

        return json_encode($result);
    }
    /**
     * @return string
     * 获取分销资料
     */
    public function findFxMaterialList($title=NULL,$staff_name=NULL,$release_time=NULL,$type=NULL){
        $result = array();
        try{
            //分页
            $criteria = new CDbCriteria();
            //关键字查找
            if ($title !== '' && $title !== NULL){
                $criteria -> addCondition('title like :title');
                $criteria->params[':title'] = '%' . $title . '%';
            }
            if ($staff_name !== '' && $staff_name !== NULL){
                //查找发布人的ID
                $name_list = Staff::model()->findAll('name like :name', array(':name' => '%' .$staff_name. '%'));
                $list = array();
                if (!empty($name_list)) {
                    foreach ($name_list as $k => $v) {
                        $list[$k]['id'] = $v->id;
                    }
                }

                $criteria->addInCondition('staff_id',$list[$k]);
            }
            if ($release_time !== '' && $release_time !== NULL){
                $criteria -> addCondition('release_time like :release_time');
                $criteria->params[':release_time'] = '%' . $release_time . '%';
            }
            if ($type !== '' && $type !== NULL){
                if($GLOBALS['DATA_DOWNLOAD_TYPE'][DATA_DOWNLOAD_TYPE_DOC] == $type) {
                    $data = DATA_DOWNLOAD_TYPE_DOC;
                }

                if($GLOBALS['DATA_DOWNLOAD_TYPE'][DATA_DOWNLOAD_TYPE_VIDEO] == $type) {
                    $data = DATA_DOWNLOAD_TYPE_VIDEO;
                }

                $criteria -> addCondition('type = :type');
                $criteria->params[':type'] = $data ;
            }

            $criteria->addCondition("flag = :flag and release_to = :release_to");
            $criteria->params[':flag']=FLAG_NO;
            $criteria->params[':release_to']=SYSTEM_NOTICE_RELEASE_TO_FX;
            $criteria->order = "weight desc ,create_time desc";

            $pages = new CPagination(DataDownload::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);

            $result = DataDownload::model()->findAll($criteria);
            $data = array();
            if(!empty($result)){
                foreach ($result as $k => $v){
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['staff_id'] = $v -> staff['name'];
                    $data[$k]['type'] = $v -> type;
                    $data[$k]['title'] = $v -> title;
                    $data[$k]['download_url'] = $v -> download_url;
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
     * 发布内部资料
     */
    public function releaseInsideMaterial($id){

        $material = DataDownload::model()->findByPk($id);

        $material->status = SYSTEM_NOTICE_STATUS_RELEASE;
        $material->release_time = date('Y-m-d H:i:s');
        if ($material->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
    /**
     * @param $id
     * @return string
     * 取消发布内部资料
     */
    public function unreleaseInsideMaterial($id){

        $material = DataDownload::model()->findByPk($id);

        $material->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
        $material->release_time = date('Y-m-d H:i:s');
        if ($material->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
    /**
     * @param $id
     * @return string
     * 发布分销资料
     */
    public function releaseFxMaterial($id){

        $material = DataDownload::model()->findByPk($id);

        $material->status = SYSTEM_NOTICE_STATUS_RELEASE;
        $material->release_time = date('Y-m-d H:i:s');
        if ($material->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }
    /**
     * @param $id
     * @return string
     * 取消发布分销资料
     */
    public function unreleaseFxMaterial($id){

        $material = DataDownload::model()->findByPk($id);

        $material->status = SYSTEM_NOTICE_STATUS_UNRELEASE;
        $material->release_time = date('Y-m-d H:i:s');
        if ($material->save()) {
            $result ['status'] = ERROR_NONE; // 状态码
        } else {
            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
        }
        return json_encode($result);
    }

    /**
     * 内部资料详细信息
     * @param $id
     * @return string
     */
    public function insideMaterial($id)
    {
        $result = DataDownload::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * @var null
     * 编辑内部资料
     */
    public function editInsideMaterial($id,$title,$type, $download_url,$content){


            $material = DataDownload::model()->findByPk($id);

            $material->title = $title;
            $material->content = $content;
            $material->type = $type;
            $material->download_url = $download_url;
            $material->staff_id = Yii::app() -> session['staff_id'];
            $material->status = DATA_DOWNLOAD_STATUS_UNRELEASE;
            $material->release_time = date('Y-m-d H:i:s');
            $material->last_time = date('Y-m-d H:i:s');

            if ($material->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $material->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }


        return json_encode($result);
    }

    /**
     * 分销资料详细信息
     * @param $id
     * @return string
     */
    public function fxMaterial($id)
    {
        $result = DataDownload::model()->findByPk($id);
        return $result = json_decode(CJSON::encode($result), TRUE);
    }

    /**
     * @var null
     * 编辑分销资料
     */
    public function editFxMaterial($id,$type,$add_to,$download_url, $title, $content){


            $material = DataDownload::model()->findByPk($id);

            $material->title = $title;
            $material->content = $content;
            $material->type = $type;
            $material->add_to = $add_to;
            $material->download_url = $download_url;
            $material->staff_id = Yii::app() -> session['staff_id'];
            $material->status = DATA_DOWNLOAD_STATUS_UNRELEASE;
            $material->release_time = date('Y-m-d H:i:s');
            $material->last_time = date('Y-m-d H:i:s');


            if ($material->save()) {
                $result ['status'] = ERROR_NONE; // 状态码
                $result ['id'] =  $material->id;
            } else {
                $result ['status'] = ERROR_SAVE_FAIL; // 状态码
            }


        return json_encode($result);
    }

    /**
     * @param $id
     * @return string
     * 删除内部资料
     */
    public function deleteInsideMaterial($id)
    {
        $result = array();
        $model = DataDownload::model()->findByPk($id);
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
     * 内部资料置顶
     */
    public function topInsideMaterial($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = DataDownload::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->weight=($model->weight)+1;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('置顶失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该资料不存在');
                    }
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
     * 取消内部资料置顶
     */
    public function cancelTopInsideMaterial($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = DataDownload::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->weight=1;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('取消置顶失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该资料不存在');
                    }
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
     * @param $del_arr
     * @return string
     *批量删除内部资料
     */
    public function delMoreInsideMaterial($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = DataDownload::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->flag=FLAG_YES;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('资料删除失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该资料不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                //添加日志记录
                $oa_log = new OaLogC();
                $arr = json_encode(array(OA_OPERATION_LOG_OBJECT_DATA_DOWNLOAD,$del_arr));
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
     * 删除分销资料
     */
    public function deleteFxMaterial($id)
    {
        $result = array();
        $model = DataDownload::model()->findByPk($id);
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
     * 分销资料置顶
     */
    public function topFxMaterial($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = DataDownload::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->weight=($model->weight)+1;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('置顶失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该资料不存在');
                    }
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
     * 取消分销资料置顶
     */
    public function cancelTopFxMaterial($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = DataDownload::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->weight=1;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('取消置顶失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该资料不存在');
                    }
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
     * @param $del_arr
     * @return string
     *批量删除分销资料
     */
    public function delMoreFxMaterial($del_arr)
    {
        $result = array();
        try {
            //数据库查询
            if(!empty($del_arr)) {
                $count = count($del_arr);
                for ($i = 0; $i < $count; $i++) {
                    $model = DataDownload::model()->findByPk($del_arr[$i]);
                    if($model){
                        $model->flag=FLAG_YES;
                        if($model -> update()){

                        }else{
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('资料删除失败');
                        }
                    }else{
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该资料不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                //添加日志记录
                $oa_log = new OaLogC();
                $arr = json_encode(array(OA_OPERATION_LOG_OBJECT_DATA_DOWNLOAD,$del_arr));
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