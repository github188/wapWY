<?php
/**
 * Created by PhpStorm.
 * User: nb-lt
 * Date: 2016/6/28
 * Time: 14:20
 */
class UcenterController extends PropertyController
{
    public function init()
    {
        parent::init();
        $encrypt_id = $this->getEncryptId();
        $this->checkLogin($encrypt_id);
    }

    /**
     * 首页
     */
    public function actionIndex()
    {
        $proprietorC = new ProprietorC();

        $result = $proprietorC -> getProprietorInfo($this->getUserId());
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietorInfo = $result['data'];
        }

        if ($result['data']['verify_status'] == PROPRIETOR_VERIFY_STATUS_PENDING_AUDIT) {
            $this->redirect($this->createUrl('Review', array('encrypt_id' => $this->getEncryptId())));
        }
        if ($result['data']['verify_status'] == PROPRIETOR_VERIFY_STATUS_REJECT) {
            $this->redirect($this->createUrl('ReviewNopass', array('encrypt_id' => $this->getEncryptId())));
        }

        $this->render('index', array(
            'proprietorInfo' => $proprietorInfo
        ));
    }

    /**
     * 个人中心
     */
    public function actionCenter() {
        $proprietorC = new ProprietorC();
        $proprietor = array();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor = $result['data'];
        }
        $this -> render('center', array(
            'proprietor' => $proprietor,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 用户详情
     */
    public function actionUserDetail(){
        $encrypt_id = $this->getEncryptId();
        $proprietorC = new ProprietorC();
        $user_id =$this->getUserId();
        $proprietor_info = '';

        $user = new MobileUserUC();
        $re = json_decode($user->getMerchant($encrypt_id));

        $merchant_id = $re->data->id;

        $communityC = new CommunityC();
        $community_list = '';
        $result = json_decode($communityC -> getCommunityList($merchant_id),true);

        if ($result['status'] == APPLY_CLASS_SUCCESS){
            $community_list = $result['data'];
        }

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor_info = $result['data'];
        }

        if (!empty($_POST) && isset($_POST)) {
            //获取表单
            $post = $_POST;

            $result = json_decode($proprietorC -> editProprietor($user_id,$post),true);
            if ($result['status'] == APPLY_CLASS_SUCCESS) {
                $this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Center', array(
                    'encrypt_id' => $encrypt_id
                )));
            }else{
                Yii::app()->user->setFlash('error', $result['errMsg']);
            }
        }

        $this -> render('userDetail', array(
            'encrypt_id' => $encrypt_id,
            'proprietor_info' => $proprietor_info,
            'community_list' => $community_list
        ));
    }

    /**
     * 用户审核
     */
    public function actionReview()
    {
        $proprietorC = new ProprietorC();
        $encrypt_id = $this->getEncryptId();
        $user_id =$this->getUserId();

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor_info = $result['data'];
            $proprietor_status = $result['data']['verify_status'];
            //判断审核状态是否通过，若通过，则跳转到“审核成功”页
            if ($proprietor_status == PROPRIETOR_VERIFY_STATUS_PASS) {
                $this -> redirect(Yii::app()->createUrl('mobile/property/Ucenter/ReviewPass', array('encrypt_id' => $encrypt_id)));
            }
            //判断审核状态是否通过，若未通过，则跳转到“修改信息”页
            if ($proprietor_status == PROPRIETOR_VERIFY_STATUS_REJECT) {
                $this -> redirect(Yii::app()->createUrl('mobile/property/Ucenter/ReviewNopass', array('encrypt_id' => $encrypt_id)));
            }
        }

        $this->render('review', array(
            'encrypt_id' => $encrypt_id,
            'proprietor_info' => $proprietor_info
        ));
    }

    /**
     * 用户审核失败
     */
    public function actionReviewNopass()
    {
        $proprietorC = new ProprietorC();
        $encrypt_id = $this->getEncryptId();
        $user_id =$this->getUserId();

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor_info = $result['data'];
        }

        $this->render('reviewNopass', array(
            'encrypt_id' => $encrypt_id,
            'proprietor_info' => $proprietor_info
        ));
    }

    /**
     * 用户审核成功
     */
    public function actionReviewPass()
    {
        $proprietorC = new ProprietorC();
        $encrypt_id = $this->getEncryptId();
        $user_id =$this->getUserId();

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor_info = $result['data'];
        }

        $this->render('reviewPass', array(
            'encrypt_id' => $encrypt_id,
            'proprietor_info' => $proprietor_info
        ));
    }

    /**
     *添加家庭成员
     */
    public function actionEditFamily() {
        $proprietorC = new ProprietorC();
        $family_members = '';
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();

        $result = $proprietorC -> getProprietorInfo($user_id);
        $members = array();
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $family_members = json_decode($result['data']['family_members']);
        }


        if (!empty($_POST) && isset($_POST)) {
            //获取表单
            $post = $_POST;
//            var_dump($post);exit;
            $result = $proprietorC -> getEditFamily($user_id,$post);
            if ($result['status'] == APPLY_CLASS_SUCCESS) {
                $this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Center', array(
                    'encrypt_id' => $encrypt_id
                )));
            }else{
                Yii::app()->user->setFlash('error', $result['errMsg']);
            }
        }

        $this -> render('editFamily', array(
            'family_members' => $family_members,
            'encrypt_id' => $encrypt_id,
        ));
    }


    /**
     * 历史账单——水费账单
     */
    public function actionWaterFeeList() {
        $feeOrderC = new FeeOrderC();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $list = array();
        $water_fee = '';
        $power_fee = '';
        $property_fee = '';
        $parking_fee = '';
        $res = json_decode($feeOrderC -> getFeeTypeCount($user_id),true);
        if ($res['status'] == APPLY_CLASS_SUCCESS) {
            $water_fee = $res['water_fee'];
            $power_fee = $res['power_fee'];
            $property_fee = $res['property_fee'];
            $parking_fee = $res['parking_fee'];
        }
        $result = json_decode($feeOrderC -> getWaterFeeList($user_id),true);

        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $list = $result['data'];
        }
        $this -> render('waterFeeList', array(
            'list' => $list,
            'water_fee' => $water_fee,
            'power_fee' => $power_fee,
            'property_fee' => $property_fee,
            'parking_fee' => $parking_fee,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 历史账单——电费账单
     */
    public function actionPowerFeeList() {
        $feeOrderC = new FeeOrderC();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $list = array();
        $water_fee = '';
        $power_fee = '';
        $property_fee = '';
        $parking_fee = '';
        $res = json_decode($feeOrderC -> getFeeTypeCount($user_id),true);
        if ($res['status'] == APPLY_CLASS_SUCCESS) {
            $water_fee = $res['water_fee'];
            $power_fee = $res['power_fee'];
            $property_fee = $res['property_fee'];
            $parking_fee = $res['parking_fee'];
        }
        $result = json_decode($feeOrderC -> getPowerFeeList($user_id),true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $list = $result['data'];
        }
        $this -> render('powerFeeList', array(
            'list' => $list,
            'water_fee' => $water_fee,
            'power_fee' => $power_fee,
            'property_fee' => $property_fee,
            'parking_fee' => $parking_fee,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 历史账单——物业费账单
     */
    public function actionPropertyFeeList() {
        $feeOrderC = new FeeOrderC();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $list = array();
        $water_fee = '';
        $power_fee = '';
        $property_fee = '';
        $parking_fee = '';
        $res = json_decode($feeOrderC -> getFeeTypeCount($user_id),true);
        if ($res['status'] == APPLY_CLASS_SUCCESS) {
            $water_fee = $res['water_fee'];
            $power_fee = $res['power_fee'];
            $property_fee = $res['property_fee'];
            $parking_fee = $res['parking_fee'];
        }
        $result = json_decode($feeOrderC -> getPropertyFeeList($user_id),true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $list = $result['data'];
        }
        $this -> render('propertyFeeList', array(
            'list' => $list,
            'water_fee' => $water_fee,
            'power_fee' => $power_fee,
            'property_fee' => $property_fee,
            'parking_fee' => $parking_fee,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 历史账单——停车费账单
     */
    public function actionParkingFeeList() {
        $feeOrderC = new FeeOrderC();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $list = array();
        $water_fee = '';
        $power_fee = '';
        $property_fee = '';
        $parking_fee = '';
        $res = json_decode($feeOrderC -> getFeeTypeCount($user_id),true);
        if ($res['status'] == APPLY_CLASS_SUCCESS) {
            $water_fee = $res['water_fee'];
            $power_fee = $res['power_fee'];
            $property_fee = $res['property_fee'];
            $parking_fee = $res['parking_fee'];
        }
        $result = json_decode($feeOrderC -> getParkingFeeList($user_id),true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $list = $result['data'];
        }
        $this -> render('parkingFeeList', array(
            'list' => $list,
            'water_fee' => $water_fee,
            'power_fee' => $power_fee,
            'property_fee' => $property_fee,
            'parking_fee' => $parking_fee,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 支付成功
     */
    public function actionPaySuccess() {
        $this -> render('paySuccess');
    }

    /**
     * 修改业主信息
     */
    public function actionEditProprietor(){
        $proprietorC = new ProprietorC();
        $user_id =$this->getUserId();
        $encrypt_id = $this->getEncryptId();

        if (!empty($_POST) && isset($_POST)) {
            //获取表单
            $post = $_POST;

            $result = $proprietorC -> editProprietor($user_id,$post);
            if ($result['status'] == APPLY_CLASS_SUCCESS) {
                $this->redirect(Yii::app()->createUrl('mobile/property/Ucenter/Center', array(
                    'encrypt_id' => $encrypt_id
                )));
            }
        }
    }

}