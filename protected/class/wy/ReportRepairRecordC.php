<?php
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/6/23
 * Time: 15:36
 */
include_once(dirname(__FILE__).'/../mainClass.php');

//报修记录类
class ReportRepairRecordC extends mainClass
{

    /**
     * @param $user_id
     * @param $merchant_id
     * @return string
     * 报修记录列表
     */
    public function getReportRepairRecordList($user_id) {
        $result = array();

        try{
            $cri = new CDbCriteria();
            $cri -> addCondition('flag = :flag');
            $cri -> params[':flag'] = FLAG_NO;
            $cri -> addCondition('user_id = :user_id');
            $cri -> params[':user_id'] = $user_id;
            $cri -> order = "create_time desc";
            $model = ReportRepairRecord::model() -> findAll($cri);

            $data = array();
            if (!empty($model)) {
                foreach($model as $k => $v) {
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['create_time'] = $v -> create_time;
                    $data[$k]['name'] = User::model() -> find('id = :id', array(':id' => $v -> user_id)) -> name;
                    $data[$k]['tel'] = User::model() -> find('id = :id', array(':id' => $v -> user_id)) -> account;
                    $data[$k]['address'] = $v -> address;
                    $data[$k]['area_type'] = $v -> area_type;
                    $data[$k]['detail'] = $v -> detail;
                    $data[$k]['remark'] = $v -> remark;
                    $data[$k]['status'] = $v -> status;
                    $data[$k]['type'] = Proprietor::model()->find('user_id = :user_id', array(':user_id'=>$v->user_id))->type;
                }
            }
            $result['data'] = $data;
            $result['status'] = APPLY_CLASS_SUCCESS;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $info
     * @param $community_id
     * 添加报修
     */
    public function addReportRepairRecord($info, $user_id,$merchant_id) {
        $repair_person = User::model() -> find('id = :id', array(':id' => $user_id)) -> name; //报修人
        $community_id = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community_id; //小区id
        $tel = User::model() -> find('id = :id', array(':id' => $user_id)) -> account; //联系人电话
        $area_type = $info['area_type']; //报修区域
        $address = $info['address']; //报修地址
        $detail = $info['detail']; //报修详情
        $img1 = $info['img1']; //报修图片
        $img2 = $info['img2']; //报修图片
        $img3 = $info['img3']; //报修图片
        $img = [$img1,$img2,$img3];

        try {
            //判断是否获取用户id
            if (!isset($user_id) || empty($user_id)) {
                throw new Exception('未获取user_id参数');
            }
            //判断是否获取小区id
            if (!isset($community_id) || empty($community_id)) {
                throw new Exception('未获取community_id参数');
            }
            //判断报修地址是否填写
            if (!isset($address) || empty($address)) {
                throw new Exception('请填写报修地址');
            }
            //判断报修区域是否勾选
            if (!isset($area_type) || empty($area_type)) {
                throw new Exception('请选择报修区域');
            }
            //判断报修内容是否为空
            if (!isset($detail) || empty($detail)) {
                throw new Exception('请填写报修内容');
            }
            //判断是否上传报修图片
            if (!isset($img1) || empty($img1)) {
                throw new Exception('请上传图片');
            }

            $transaction = Yii::app()->db->beginTransaction();
            $model = new ReportRepairRecord();

            $model -> community_id = $community_id;
            $model -> user_id = $user_id;
            $model -> merchant_id = $merchant_id;
            $model -> repair_person = $repair_person;
            $model -> tel = $tel;
            $model -> area_type = $area_type;
            $model -> address = $address;
            $model -> detail = $detail;
            $model -> img = json_encode($img);
            $model -> status = REPORT_REPAIR_RECORD_STATUS_WAITING;
            $model -> create_time = date('Y-m-d H:i:s', time());
            $model -> repair_time = date('Y-m-d H:i:s', time());

            if ($model->save()) {
                $transaction->commit();
                $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $transaction->rollBack();
                $result['status'] = ERROR_DATA_BASE_ADD;
                throw new Exception('数据库添加失败');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 维修反馈
     */
    public function addRemark($post){
        $remark = $post['remark'];
        $id = $post['id'];

        try {
            //判断报修反馈是否填写
            if (!isset($remark) || empty($remark)){
                throw new Exception('请填写报修反馈');
            }
            
            $transaction = Yii::app()->db->beginTransaction();
            $model = ReportRepairRecord::model() -> findByPk($id);

            $model -> remark = $remark;
            $model -> status = REPORT_REPAIR_RECORD_STATUS_COMPLETE;
            $model -> last_time = new CDbExpression('now()');

            if ($model->save()) {
                $transaction->commit();
                $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $transaction->rollBack();
                $result['status'] = ERROR_DATA_BASE_ADD;
                throw new Exception('提交反馈失败');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }
}