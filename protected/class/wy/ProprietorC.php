<?php
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/6/23
 * Time: 15:36
 */
include_once(dirname(__FILE__).'/../mainClass.php');

//业主类
class ProprietorC extends mainClass
{
    /**
     * @param $info
     * @param $merchant_id
     * 业主注册
     */
    public function registerProprietor($info,$merchant_id){

        $name = $info['name'];
        $account = $info['mobile_phone'];
        $pwd = $info['pwd'];
        $msg_pwd = $info['verification_code'];
        $access_control_card_no = $info['access_control_card_no'];
        $type = $info['type'];
        $community_id = $info['community_id'];
        $building_number = $info['building_number'];
        $room_number = $info['room_number'];
        $member_name = $info['member_name'];
        $member_phone = $info['member_phone'];
        $member_access_control_card_no = $info['member_access_control_card_no'];
        $relationship = $info['relationship'];
        $member_name1 = '';
        $member_phone1 = '';
        $member_access_control_card_no1 = '';
        $relationship1 = '';
        $member_name2 = '';
        $member_phone2 = '';
        $member_access_control_card_no2 = '';
        $relationship2 = '';
        if (isset($info['member_name1'])){
            $member_name1 = $info['member_name1'];
        }
        if (isset($info['member_phone1'])){
            $member_phone1 = $info['member_phone1'];
        }
        if (isset($info['member_access_control_card_no1'])){
            $member_access_control_card_no1 = $info['member_access_control_card_no1'];
        }
        if (isset($info['relationship1'])){
            $relationship1 = $info['relationship1'];
        }
        if (isset($info['member_name2'])){
            $member_name2 = $info['member_name2'];
        }
        if (isset($info['member_phone2'])){
            $member_phone2 = $info['member_phone2'];
        }
        if (isset($info['member_access_control_card_no2'])){
            $member_access_control_card_no2 = $info['member_access_control_card_no2'];
        }
        if (isset($info['relationship2'])){
            $relationship2 = $info['relationship2'];
        }
        

        $result = array();  //返回值
        $family_members = array(
            $member_name,
            $member_phone,
            $member_access_control_card_no,
            $relationship,
            $member_name1,
            $member_phone1,
            $member_access_control_card_no1,
            $relationship1,
            $member_name2,
            $member_phone2,
            $member_access_control_card_no2,
            $relationship2
        );

        try {
            //参数验证
            if (empty($name) || !isset($name)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写姓名');
            }

            if (empty($account) || !isset($account)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写手机号');
            }

            if (isset($account)|| !empty($account)) {
                $checkPhone = preg_match(PHONE_CHECK, $account);
                if (!$checkPhone) {
                    $result['status'] = ERROR_PARAMETER_FORMAT;
                    throw new Exception("手机号码格式不正确");
                }
            }

           if (empty($msg_pwd) || !isset($msg_pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写短信验证码');
            }

            $msg = $this->checkMsgPwd($account, $msg_pwd);
            if (!$msg) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('短信验证码错误');
            }

            if (empty($pwd) || !isset($pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写密码');
            }

            $flag = $this->accountExist($merchant_id, $account);
            if ($flag) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                throw new Exception('手机已注册过');
            }

            if (empty($access_control_card_no) || !isset($access_control_card_no)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写门禁卡号');
            }

            if (empty($type) || !isset($type)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写类型');
            }

           if (empty($community_id) || !isset($community_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写小区');
            }

            if (empty($building_number) || !isset($building_number)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写楼号');
            }

            if (empty($room_number) || !isset($room_number)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写房间号');
            }

            if (empty($member_name) || !isset($member_name)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写家庭成员真实姓名');
            }

            if (empty($member_phone) || !isset($member_phone)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写家庭成员手机号');
            }

            if (empty($relationship) || !isset($relationship)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写与业主关系');
            }

            preg_match_all('/\d+/',$building_number,$arr1);
            $arr1 = join('',$arr1[0]);
            preg_match_all('/\d+/',$room_number,$arr2);
            $arr2 = join('',$arr2[0]);

            $transaction = Yii::app()->db->beginTransaction();
            //保存账号密码
            $user_model = new User();
            $user_model->merchant_id = $merchant_id;
            $user_model->account = $account;
            $user_model->pwd = md5($pwd);
            $user_model->name = $name;
            $user_model->regist_time = date('Y-m-d H:i:s');
            $user_model->create_time = date('Y-m-d H:i:s');

            //保存业主信息
            $model = new Proprietor();
            $model->merchant_id = $merchant_id;

            $model->access_control_card_no = $access_control_card_no;
            $model->type = $type;
            $model->community_id = $community_id;
            $model->building_number = $arr1;
            $model->room_number = $arr2;
            $model->family_members = json_encode($family_members);
            $model->create_time = date('Y-m-d H:i:s');
            $model->last_time = date('Y-m-d H:i:s');

            if ($user_model->save()) {
                $model->user_id = $user_model -> id;
                if($model -> save()){
                    $transaction->commit();
                    $result['data'] = $user_model['id'];
                    $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                    $result['errMsg'] = ''; //错误信息
                }else{
                    $result['status'] = APPLY_CLASS_FAIL; //状态码
                    $result['errMsg'] = $model->getErrors(); //错误信息
                }
            } else {
                $transaction->rollBack();
                $result['status'] = APPLY_CLASS_FAIL; //状态码
                $result['errMsg'] = $user_model->getErrors(); //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 审核失败业主信息修改
     */
    public function registerFailProprietor($info,$merchant_id,$user_id){

        $name = $info['name'];
        $account = $info['mobile_phone'];
        $pwd = $info['pwd'];
        $access_control_card_no = $info['access_control_card_no'];
        $type = $info['type'];
        $community_id = $info['community_id'];
        $building_number = $info['building_number'];
        $room_number = $info['room_number'];
        $member_name = $info['member_name'];
        $member_phone = $info['member_phone'];
        $member_access_control_card_no = $info['member_access_control_card_no'];
        $relationship = $info['relationship'];
        $member_name1 = '';
        $member_phone1 = '';
        $member_access_control_card_no1 = '';
        $relationship1 = '';
        $member_name2 = '';
        $member_phone2 = '';
        $member_access_control_card_no2 = '';
        $relationship2 = '';
        if (isset($info['member_name1'])){
            $member_name1 = $info['member_name1'];
        }
        if (isset($info['member_phone1'])){
            $member_phone1 = $info['member_phone1'];
        }
        if (isset($info['member_access_control_card_no1'])){
            $member_access_control_card_no1 = $info['member_access_control_card_no1'];
        }
        if (isset($info['relationship1'])){
            $relationship1 = $info['relationship1'];
        }
        if (isset($info['member_name2'])){
            $member_name2 = $info['member_name2'];
        }
        if (isset($info['member_phone2'])){
            $member_phone2 = $info['member_phone2'];
        }
        if (isset($info['member_access_control_card_no2'])){
            $member_access_control_card_no2 = $info['member_access_control_card_no2'];
        }
        if (isset($info['relationship2'])){
            $relationship2 = $info['relationship2'];
        }


        $result = array();  //返回值
        $family_members = array(
            $member_name,
            $member_phone,
            $member_access_control_card_no,
            $relationship,
            $member_name1,
            $member_phone1,
            $member_access_control_card_no1,
            $relationship1,
            $member_name2,
            $member_phone2,
            $member_access_control_card_no2,
            $relationship2
        );

        try {
            //参数验证
            if (empty($name) || !isset($name)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写姓名');
            }

            if (empty($account) || !isset($account)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写手机号');
            }

            /*if (empty($msg_pwd) || !isset($msg_pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写短信验证码');
            }

            $msg = $this->checkMsgPwd($account, $msg_pwd);
            if (!$msg) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('短信验证码错误');
            }*/

            if (empty($pwd) || !isset($pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写密码');
            }

            if (empty($access_control_card_no) || !isset($access_control_card_no)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写门禁卡号');
            }

            if (empty($type) || !isset($type)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写类型');
            }

            if (empty($community_id) || !isset($community_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写小区');
            }

            if (empty($building_number) || !isset($building_number)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写楼号');
            }

            if (empty($room_number) || !isset($room_number)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写房间号');
            }

            if (empty($member_name) || !isset($member_name)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写家庭成员真实姓名');
            }

            if (empty($member_phone) || !isset($member_phone)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写家庭成员手机号');
            }

            if (empty($relationship) || !isset($relationship)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写与业主关系');
            }

            preg_match_all('/\d+/',$building_number,$arr1);
            $arr1 = join('',$arr1[0]);
            preg_match_all('/\d+/',$room_number,$arr2);
            $arr2 = join('',$arr2[0]);

            if (isset($user_id) && !empty($user_id)) {
                $transaction = Yii::app()->db->beginTransaction();
                //保存账号密码
                $user_model = User::model()->findByPk($user_id);
                $user_model->merchant_id = $merchant_id;
                $user_model->account = $account;
                $user_model->pwd = md5($pwd);
                $user_model->name = $name;
                $user_model->regist_time = date('Y-m-d H:i:s');

                //保存业主信息
                $model = Proprietor::model()->find('user_id = :user_id', array(':user_id' => $user_id));
                if (!empty($model)){
                    $model->merchant_id = $merchant_id;
                    $model->access_control_card_no = $access_control_card_no;
                    $model->type = $type;
                    $model->community_id = $community_id;
                    $model->building_number = $arr1;
                    $model->room_number = $arr2;
                    $model->family_members = json_encode($family_members);
                    $model->verify_status = PROPRIETOR_VERIFY_STATUS_PENDING_AUDIT;
                    $model->last_time = date('Y-m-d H:i:s');
                }else{
                    $model = new Proprietor();
                    $model->merchant_id = $merchant_id;
                    $model->access_control_card_no = $access_control_card_no;
                    $model->type = $type;
                    $model->community_id = $community_id;
                    $model->building_number = $arr1;
                    $model->room_number = $arr2;
                    $model->family_members = json_encode($family_members);
                    $model->verify_status = PROPRIETOR_VERIFY_STATUS_PENDING_AUDIT;
                    $model->create_time = date('Y-m-d H:i:s');
                    $model->last_time = date('Y-m-d H:i:s');
                }

                if ($user_model->save()) {
                    $model->user_id = $user_model->id;
                    if ($model->save()) {
                        $transaction->commit();
                        $result['data'] = $user_model['id'];
                        $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                        $result['errMsg'] = ''; //错误信息
                    } else {
                        $result['status'] = APPLY_CLASS_FAIL; //状态码
                        $result['errMsg'] = $model->getErrors(); //错误信息
                    }
                } else {
                    $transaction->rollBack();
                    $result['status'] = APPLY_CLASS_FAIL; //状态码
                    $result['errMsg'] = $user_model->getErrors(); //错误信息
                }
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $info
     * @param $merchant_id
     * 业主登录-检测账号密码
     */
    public function loginProprietor($info,$merchant_id)
    {

        $account = $info['account'];
        $pwd = $info['pwd'];

        //返回结果
        $result = array();
        try {
            if (empty($account) || !isset($account)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请输入账号');
            }

            if (empty($pwd) || !isset($pwd)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请输入密码');
            }

            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('account = :account');
            $criteria->params[':account'] = $account;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('pwd = :pwd');
            $criteria->params[':pwd'] = md5($pwd);

            $model = User::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_DATA_BASE_SELECT;
                throw new Exception('账号或密码错误');
            }
            $proprietor = Proprietor::model()->find('user_id = :user_id',array(':user_id' =>$model['id'] ));
            if (empty($proprietor)) {
                $result['status'] = '';
                throw new Exception('请完善业主信息');
            }
            $result['status'] = APPLY_CLASS_SUCCESS; //状态码
            $result['errMsg'] = ''; //错误信息
            $result['data'] = $model['id'];
            $result['verify_status'] = $proprietor['verify_status'];
        } catch (Exception $e) {
            $result['data'] = $model['id'];
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $proprietor_id
     * 获取业主审核状态
     */
    public function getProprietorVerifyStatus($proprietor_id){

    }

    /**
     * @param $proprietor_id
     * @param $info
     * 修改业主信息
     */
    public function editProprietor($user_id,$info){
        $name = $info['name'];
        $account = $info['tel'];
        $access_control_card_no = $info['access_control_card_no'];
        $type = $info['type'];
        $community_id = $info['community_id'];
        $building_number = $info['building_number'];
        $room_number = $info['room_number'];

        try {

            //参数验证
            if (empty($name) || !isset($name)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写姓名');
            }

            if (empty($account) || !isset($account)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写手机号');
            }

            if (empty($access_control_card_no) || !isset($access_control_card_no)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写门禁卡号');
            }

            if (empty($type) || !isset($type)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写类型');
            }

            if (empty($community_id) || !isset($community_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写小区');
            }

            if (empty($building_number) || !isset($building_number)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写楼号');
            }

            if (empty($room_number) || !isset($room_number)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('请填写房间号');
            }

            $user_model = User::model()->find('id = :id',array(':id' => $user_id));
            $model = Proprietor::model()->find('user_id = :user_id',array(':user_id' => $user_id));
            
            $transaction = Yii::app()->db->beginTransaction();
            //保存账号密码
            $user_model->account = $account;
            $user_model->name = $name;
            $user_model->last_time = date('Y-m-d H:i:s');

            //保存业主信息
            $model->access_control_card_no = $access_control_card_no;
            $model->type = $type;
            $model->community_id = $community_id;
            $model->building_number = $building_number;
            $model->room_number = $room_number;
            $model->last_time = date('Y-m-d H:i:s');

            if ($user_model->save()) {
                if($model -> save()){
                    $transaction->commit();
                    $result['data'] = $user_model['id'];
                    $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                    $result['errMsg'] = ''; //错误信息
                }else{
                    $result['status'] = APPLY_CLASS_FAIL; //状态码
                    $result['errMsg'] = $model->getErrors(); //错误信息
                }
            } else {
                $transaction->rollBack();
                $result['status'] = APPLY_CLASS_FAIL; //状态码
                $result['errMsg'] = $user_model->getErrors(); //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 验证账号/手机是否存在
     * @param $merchant_id    商户id
     * @param $account        账号/手机
     */
    public function accountExist($merchant_id, $account)
    {
        //检查账号是否已存在
        $user = User::model()->find('merchant_id = :merchant_id and account = :account and flag =:flag', array(
            ':merchant_id' => $merchant_id,
            ':account' => $account,
            ':flag' => FLAG_NO
        ));
        if (isset($user) && !empty($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证短信验证码
     * @param $account     手机号码
     * @param $msg_pwd     短信密码
     */
    public function checkMsgPwd($account, $msg_pwd)
    {
        $check_msg_pwd = Yii::app()->memcache->get($account);
        if ($check_msg_pwd == $msg_pwd) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $proprietor_id
     * @param $user_id
     * @return string
     * 获取用户详情
     */
    public function getProprietorInfo($user_id) {
        $result = array();
        try {
            $model = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id));
            $user = User::model() -> find("id = :id", array(':id' => $user_id));
            
            $data = array();
            if (!empty($model)) {
                $data['name'] =  $user-> name;
                $data['tel'] = $user -> account;
                $data['avatar'] = $user -> avatar;
                //预存金额
                $data['money'] = $user -> money;
                $data['type'] = $model -> type;
                $data['access_control_card_no'] = $model -> access_control_card_no;
                $data['building_number'] = $model -> building_number;
                $data['community_id'] = $model -> community_id;
                $data['room_number'] = $model -> room_number;
                $data['family_members'] = $model -> family_members;
                $data['community_name'] = $model->community->name;
                $data['verify_status'] = $model->verify_status;
                $data['remark'] = $model->remark;

                $data['car_brand'] = isset($model -> car_brand) ? $model -> car_brand : '';
                $data['car_no'] = isset( $model -> car_no) ?  $model -> car_no : '';
                $data['car_img'] = isset($model -> car_img) ? $model -> car_img : '';

                $result['data'] = $data;
                $result['status'] = APPLY_CLASS_SUCCESS;
            }else{
                throw new Exception('请填写业主信息');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     *编辑家庭成员
     */
    public function getEditFamily($user_id,$info) {
        $result = array();
        $member_name = $info['member_name'];
        $member_phone = $info['member_phone'];
        $member_access_control_card_no = $info['member_access_control_card_no'];
        $relationship = $info['relationship'];
        $member_name1 = '';
        $member_phone1 = '';
        $member_access_control_card_no1 = '';
        $relationship1 = '';
        $member_name2 = '';
        $member_phone2 = '';
        $member_access_control_card_no2 = '';
        $relationship2 = '';
        if (isset($info['member_name1'])){
            $member_name1 = $info['member_name1'];
        }
        if (isset($info['member_phone1'])){
            $member_phone1 = $info['member_phone1'];
        }
        if (isset($info['member_access_control_card_no1'])){
            $member_access_control_card_no1 = $info['member_access_control_card_no1'];
        }
        if (isset($info['relationship1'])){
            $relationship1 = $info['relationship1'];
        }
        if (isset($info['member_name2'])){
            $member_name2 = $info['member_name2'];
        }
        if (isset($info['member_phone2'])){
            $member_phone2 = $info['member_phone2'];
        }
        if (isset($info['member_access_control_card_no2'])){
            $member_access_control_card_no2 = $info['member_access_control_card_no2'];
        }
        if (isset($info['relationship2'])){
            $relationship2 = $info['relationship2'];
        }

        $family_members = array(
            $member_name,
            $member_phone,
            $member_access_control_card_no,
            $relationship,
            $member_name1,
            $member_phone1,
            $member_access_control_card_no1,
            $relationship1,
            $member_name2,
            $member_phone2,
            $member_access_control_card_no2,
            $relationship2
        );

        if (empty($member_name) || !isset($member_name)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            throw new Exception('请填写家庭成员真实姓名');
        }

        if (empty($member_phone) || !isset($member_phone)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            throw new Exception('请填写家庭成员手机号');
        }

        if (empty($relationship) || !isset($relationship)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            throw new Exception('请填写与业主关系');
        }

        try {
            $model = Proprietor::model()->find('user_id = :user_id',array(':user_id' => $user_id));

            $model->family_members = json_encode($family_members);

            $transaction = Yii::app()->db->beginTransaction();
            if ($model->save()) {
                $transaction->commit();
                $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $transaction->rollBack();
                $result['status'] = APPLY_CLASS_FAIL; //状态码
                $result['errMsg'] = '添加失败'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * @param $user_id
     * @param $money
     * 修改用户预存金额
     */
    public function editUserMoney($user_id, $money) {
        $user = User::model()->findByPk($user_id);
        $user->money = bcsub($user->money, $money, 2);
        if ($user->update()) {
            return true;
        }
    }

    /**
     * @param $user_id
     * 获取业主车辆信息
     */
    public function getProprietorCars($user_id){
        try{
            $model = ProprietorCar::model() -> findAll('user_id = :user_id', array(':user_id' => $user_id));
            $data = array();
            if (!empty($model)){
                foreach ($model as $k =>$v){
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['car_brand'] = $v['car_brand'];
                    $data[$k]['car_no'] = $v['car_no'];
                    $data[$k]['car_img'] = $v['car_img'];
                }
            }
            $result['data'] = $data;
            $result['status'] = APPLY_CLASS_SUCCESS; //状态码
            $result['errMsg'] = ''; //错误信息

        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

}