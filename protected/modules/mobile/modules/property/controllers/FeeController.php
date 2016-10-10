<?php
/**
 * Created by PhpStorm.
 * User: nb-lt
 * Date: 2016/6/28
 * Time: 10:51
 * 个人缴费
 */
class FeeController extends PropertyController
{
    public function init()
    {
        parent::init();
        $encrypt_id = $this->getEncryptId();
        $this->checkLogin($encrypt_id);
    }

    /**
     * 用户缴纳电费——列表
     */
    public function actionPowerFeeList() {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $user_id = $this->getUserId(); //获取user_id
        $encrypt_id = $this->getEncryptId(); //获取encrypt_id

        $order_type = FEEORDER_TYPE_ELECTRICITY_FEE; //订单类型：电费

        $list = array(); //定义list数组

        $result = json_decode($feeOrderC -> getFeeOrderList($order_type,$user_id),true); //调用getFeeOrderList()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $list = $result['data'];
        }

        $this -> render('powerFeeList', array( //渲染powerFeeList页面
            'list' => $list,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 用户缴纳电费——查看
     */
    public function actionPowerFeeView($id) {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $communityC = new CommunityC(); //实例化CommunityC()
        $user_id = $this->getUserId(); //获取user_id
        $order = array(); //定义order数组

        $community_id = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community -> id; //获取小区id
        $community_detail = $communityC -> getCommunityInfo($community_id); //通过$community_id获取小区详情
        $result = json_decode($community_detail->electricity_fee_set,true); //通过小区详情，获取电费设置
        $efee_info = $result; //获取电费详情
        $efee_type = $result['type']; //获取电费类型:1.分时段 2.不分时段

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true); //调用getFeeOrderInfo()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $order = $result['data'];
        }

        $this -> render('powerFeeView', array( //渲染powerFeeView页面
            'order' => $order,
            'efee_info' => $efee_info,
            'efee_type' => $efee_type
        ));
    }

    /**
     * 用户缴纳电费——支付
     */
    public function actionPowerFeeDetails($id) {
        $feeOrderC = new FeeOrderC();
        $communityC = new CommunityC();
        $proprietorC = new ProprietorC();
        $user_id = $this->getUserId();
        $order = array();
        //获取小区id
        $community_id = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community -> id;
        //获取小区详情
        $community_detail = $communityC -> getCommunityInfo($community_id);
        //获取电费类型
        $result = json_decode($community_detail->electricity_fee_set,true);
        $efee_info = $result;
        $efee_type = $result['type'];

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor = $result['data'];
        }

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $order = $result['data'];
        }

        $this -> render('powerFeeDetails', array(
            'efee_info' => $efee_info,
            'efee_type' => $efee_type,
            'money' => $proprietor['money'],
            'order' => $order
        ));
    }

    /**
     * 用户缴纳水费——列表
     */
    public function actionWaterFeeList() {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $user_id = $this->getUserId(); //获取user_id
        $encrypt_id = $this->getEncryptId(); //获取encrypt_id

        $order_type = FEEORDER_TYPE_WATER_FEE; //订单类型：水费
        $list = array(); //定义list数组

        $result = json_decode($feeOrderC -> getFeeOrderList($order_type, $user_id),true); //调用getFeeOrderList()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $list = $result['data'];
        }

        $this -> render('waterFeeList', array( //渲染waterFeeList页面
            'list' => $list,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 用户缴纳水费——查看
     */
    public function actionWaterFeeView($id) {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $communityC = new CommunityC(); //实例化CommunityC()
        $user_id = $this->getUserId(); //获取user_id
        $order = array(); //定义order数组

        $community_id = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community -> id; //获取小区id
        $community_detail = $communityC -> getCommunityInfo($community_id); //通过$community_id获取小区详情
        $result = json_decode($community_detail->water_fee_set,true);//通过小区详情，获取水费设置
        $wfee_info = $result; //获取水费详情

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true); //调用getFeeOrderInfo()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $order = $result['data'];
        }

        $this -> render('waterFeeView', array( //渲染waterFeeView页面
            'order' => $order,
            'wfee_info' => $wfee_info,
        ));
    }

    /**
     * 用户缴纳水费——支付
     */
    public function actionWaterFeeDetails($id) {
        $feeOrderC = new FeeOrderC();
        $communityC = new CommunityC();
        $proprietorC = new ProprietorC();
        $user_id = $this->getUserId();
        $order = array();
        //获取小区id
        $community_id = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community -> id;
        //获取小区详情
        $community_detail = $communityC -> getCommunityInfo($community_id);
        //获取电费类型
        $result = json_decode($community_detail->water_fee_set,true);
        $wfee_info = $result;

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor = $result['data'];
        }

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $order = $result['data'];
        }
        $this -> render('waterFeeDetails', array(
            'order' => $order,
            'wfee_info' => $wfee_info,
            'money' => $proprietor['money']
        ));
    }

    /**
     * 用户缴纳物业费——列表
     */
    public function actionPropertyFeeList() {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $user_id = $this->getUserId(); //获取user_id
        $encrypt_id = $this->getEncryptId(); //获取encrypt_id

        $order_type = FEEORDER_TYPE_PROPERTY_FEE; //订单类型：物业费
        $list = array(); //定义list数组

        $result = json_decode($feeOrderC -> getFeeOrderList($order_type, $user_id),true); //调用getFeeOrderList()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $list = $result['data'];
        }

        $this -> render('propertyFeeList', array( //渲染propertyFeeList页面
            'list' => $list,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 用户缴纳物业费——查看
     */
    public function actionPropertyFeeView($id) {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $user_id = $this->getUserId(); //获取user_id
        $order = array(); //定义order数组

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true); //调用getFeeOrderInfo()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $order = $result['data'];
        }

        $this -> render('propertyFeeView', array( //渲染propertyFeeView页面
            'order' => $order,
        ));
    }

    /**
     *用户缴纳物业费——支付
     */
    public function actionToPropertyFee($id) {
        $feeOrderC = new FeeOrderC();
        $communityC = new CommunityC();
        $proprietorC = new ProprietorC();
        $user_id = $this->getUserId();
        $order = array();
        //获取小区id
        $community_id = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community -> id;
        //获取小区详情
        $community_detail = $communityC -> getCommunityInfo($community_id);
        //获取电费类型
        $result = json_decode($community_detail->water_fee_set,true);
        $profee_info = $result;

        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor = $result['data'];
        }

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $order = $result['data'];
        }
        $this -> render('toPropertyFee', array(
            'order' => $order,
            'profee_info' => $profee_info,
            'money' => $proprietor['money']
        ));
//        $feeOrderC = new FeeOrderC();
//        $user_id = $this->getUserId();
//        $encrypt_id = $this->getEncryptId();
//        $merchant_id = Proprietor::model()->find('user_id = :user_id', array(':user_id' => $user_id)) -> merchant_id;
//        //订单类型：物业费订单
//        $order_type = FEEORDER_TYPE_PROPERTY_FEE;
//        $con = Proprietor::model() -> find("user_id = :user_id", array(':user_id' => $user_id)) -> community -> property_fee_set;
//
//        //todo 判断小区物业费是否设置
//        if (!empty($con)) {
//            $fee_type = json_decode(($con),true);
//
//            //拼接小区地址
//            $user_address = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id));
//            $address[0] = $user_address -> community -> name;
//            $address[1] = $user_address -> building_number;
//            $address[2] = $user_address -> room_number;
//            $address = $address[0] . $address[1] . '号楼' . $address[2] . '室';
//            //获取用户类型（业主/租户）
//            $type = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> type;
//
//            //获取表单提交数据
//            if (isset($_POST) && $_POST) {
//                $info = $_POST;
//                //调用addFeeOrder（）方法
//                $result = json_decode($feeOrderC -> addFeeOrder($info,$order_type,$user_id,$merchant_id), true);
//                //若保存成功，页面重定向到首页
//                if ($result['status'] == APPLY_CLASS_SUCCESS) {
//                    $this -> redirect(Yii::app()->createUrl('mobile/pay/WyFeeOrderPay', array('order_id' => $result['data']['id'], 'encrypt_id' => $this->getEncryptId())));
//                }
//            }
//
//            //渲染“toPropertyFee”页面
//            $this -> render('toPropertyFee', array(
//                'user_id' => $user_id,
//                'address' => $address,
//                'type' => $type,
//                'encrypt_id' => $encrypt_id,
//                'fee_type' => $fee_type
//            ));
//        } else {
//            $url = Yii::app () -> createUrl( 'mobile/property/Ucenter/index', array('encrypt_id' => $encrypt_id) );
//            header("Content-type: text/html; charset=utf-8");
//            echo "<script>alert('该功能暂时关闭！');window.location.href='$url'</script>";
//        }
    }

    /**
     * 用户缴纳停车费——查看
     */
    public function actionParkingFeeView($id) {
        $feeOrderC = new FeeOrderC(); //实例化FeeOrderC()
        $user_id = $this->getUserId(); //获取user_id
        $order = array(); //定义order数组

        $result = json_decode($feeOrderC -> getFeeOrderInfo($id, $user_id), true); //调用getFeeOrderInfo()方法
        if ($result['status'] == APPLY_CLASS_SUCCESS) { //返回结果
            $order = $result['data'];
        }

        $this -> render('parkingFeeView', array( //渲染parkingFeeView页面
            'order' => $order,
        ));
    }

    /**
     * 选择车辆
     */
    public function actionChooseCars(){
        $user_id = $this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $proprietorC = new ProprietorC();
        $proprietor_car = '';

        //获取业主车辆信息
        $result= $proprietorC ->getProprietorCars($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor_car = $result['data'];
        }

        $this->render('chooseCars',array(
            'proprietor_car' => $proprietor_car,
            'encrypt_id' => $encrypt_id
        ));
    }

    /**
     * 用户缴纳停车费——支付
     */
    public function actionToParkingFee($car_id) {
        $feeOrderC = new FeeOrderC();
        $user_id = $this->getUserId();
        $encrypt_id = $this->getEncryptId();
        $merchant_id = Proprietor::model()->find('user_id = :user_id', array(':user_id' => $user_id)) -> merchant_id;
        $car_info = ProprietorCar::model()->find('id = :car_id', array(':car_id' => $car_id));

        $proprietorC = new ProprietorC();
        $proprietor_info = '';

        //获取业主信息
        $result = $proprietorC -> getProprietorInfo($user_id);
        if ($result['status'] == APPLY_CLASS_SUCCESS) {
            $proprietor_info = $result['data'];
        }

        //订单类型：停车费
        $order_type = FEEORDER_TYPE_PARKING_FEE;
        $con = Proprietor::model() -> find("user_id = :user_id", array(':user_id' => $user_id)) -> community -> parking_fee_set;

        //todo 判断小区停车费是否设置
        if (!empty($con)) {
            $fee_type = json_decode(($con),true);
           
            //拼接小区地址
            $user_address = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id));
            $address[0] = $user_address -> community -> name;
            $address[1] = $user_address -> building_number;
            $address[2] = $user_address -> room_number;
            $address = $address[0] . $address[1] . '号楼' . $address[2] . '室';

            //获取表单提交数据
            if (isset($_POST) && $_POST) {
                $info = $_POST;

                //调用addFeeOrder（）方法
                $result = $feeOrderC -> addFeeOrder($info,$order_type,$user_id,$merchant_id);

                //保存成功，则页面重定向
                if ($result['status'] == APPLY_CLASS_SUCCESS) {
                    $this -> redirect(Yii::app()->createUrl('mobile/pay/WyFeeOrderPay', array('order_id' => $result['data']['id'], 'encrypt_id' => $this->getEncryptId())));
                }
            }

            $this -> render('toParkingFee', array(
                'user_id' => $user_id,
                'address' => $address,
                'encrypt_id' => $encrypt_id,
                'fee_type' => $fee_type,
                'proprietor_info' => $proprietor_info,
                'car_info' => $car_info,
                'car_id' => $car_id
            ));
        } else {
            $url = Yii::app () -> createUrl( 'mobile/property/Ucenter/index', array('encrypt_id' => $encrypt_id) );
            header("Content-type: text/html; charset=utf-8");
            echo "<script>alert('该功能暂时关闭！');window.location.href='$url'</script>";
        }
    }

    /**
     * 支付成功页
     */
    public function actionPaySuccess() {
        $this->render('paySuccess');
    }

}