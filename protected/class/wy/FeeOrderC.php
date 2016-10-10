<?php
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/6/23
 * Time: 15:36
 */
include_once(dirname(__FILE__).'/../mainClass.php');

//费用订单类
class FeeOrderC extends mainClass
{

    /**
     * 添加费用订单（物业费订单/停车费订单）
     */
    public function addFeeOrder($info,$order_type,$user_id,$merchant_id){
        $result = array();
        $res = array();

        //判断订单类型：物业费订单
        if ($order_type == FEEORDER_TYPE_PROPERTY_FEE) {
            $property = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)); //获取业主信息
            $community_id = $property -> community_id;  //获取小区id
            $property_fee_month_num = $info['property_fee_month_num']; //获取缴纳物业费月数
            $building_number = $property -> building_number; //获取楼号
            $user_account = User::model() -> findByPk($user_id) -> account; //获取业主手机号
            $property_fee_money = $info['property_fee_money'];
            $order_money = $property_fee_money; //订单金额

            try {
                if (!isset($property_fee_month_num) || empty($property_fee_month_num)) {
                    throw new Exception('未填写物业费缴纳月数');
                }

                $transaction = Yii::app()->db->beginTransaction();
                $model = new FeeOrder();
                //保存数据
                $model['user_id'] = $user_id; //用户id
                $model['merchant_id'] = $merchant_id; //商户id
                $model['community_id'] = $community_id; //小区id
                $model['building_number'] = $building_number; //楼号
                $model['order_type'] = $order_type; //订单类型
                $model['user_account'] = $user_account; //手机号
                $model['property_fee_month_num'] = $property_fee_month_num; //缴纳月数
                $model['order_money'] = $order_money; //应付金额
                $model['pay_money'] = $order_money; //应付金额
                $model['order_no'] = $this -> getOrderNo(); //订单号
                $model['create_time'] = date('Y-m-d H:i:s', time()); //创建时间
                $model['last_time'] = date('Y-m-d H:i:s', time()); //修改时间

                if ($model->save()) {
                    $transaction->commit();
                    $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $result['data'] = array('id' => $model->id);
                } else {
                    $transaction->rollBack();
                    $result['status'] = ERROR_DATA_BASE_ADD;
                    throw new Exception('error_database_add');
                }

            } catch (Exception $e) {
                $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
                $result['errMsg'] = $e->getMessage(); //错误信息
            }
        }

        //判断订单类型：停车费订单
        if ($order_type == FEEORDER_TYPE_PARKING_FEE) {
            $car_brand = $info['car_brand'];
            $car_no = $info['car_no'];
            $car_img1 = $info['img1'];
            $car_img2 = $info['img2'];
            $car_img3 = $info['img3'];
            $car_img = [$car_img1,$car_img2,$car_img3];


            $proprietor= Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)); //获取业主信息
            $proprietor_car= ProprietorCar::model() -> find('car_no = :car_no', array(':car_no' => $car_no)); //获取业主车辆信息
            $community_id = $proprietor -> community_id;  //获取小区id
            $parking_month_num = $info['parking_month_num']; //获取缴纳停车费月数
            $building_number = $proprietor -> building_number; //获取楼号
            $user_account = User::model() -> findByPk($user_id) -> account; //获取业主手机号
            $parking_fee_money = $info['parking_fee_money'];
            $order_money = $parking_fee_money; //订单金额
            $parking_type = $info['area_type'];

            try {
                if (!isset($parking_month_num) || empty($parking_month_num)) {
                    throw new Exception('未填写停车费缴纳月数');
                }
                if (!isset($car_brand) || empty($car_brand)) {
                    throw new Exception('未填写车辆品牌');
                }
                if (!isset($car_no) || empty($car_no)) {
                    throw new Exception('未填写车牌号');
                }
                if (!isset($car_img1) || empty($car_img1)) {
                    throw new Exception('未填写车前照');
                }

                //判断是否存在车牌号
                //若存在，则直读取信息；若不存在，则添加一条信息
                if (isset($proprietor_car)&&!empty($proprietor_car) ) {
                    $user_car = $proprietor_car;
                    $user_car['car_brand'] = $car_brand;
                    $user_car['car_no'] = $car_no;
                    $user_car['car_img'] = json_encode($car_img);
                    $user_car['last_time'] = date('Y-m-d H:i:s', time());
                }else{
                    $user_car = new ProprietorCar();
                    $user_car['user_id'] = $user_id;
                    $user_car['car_brand'] = $car_brand;
                    $user_car['car_no'] = $car_no;
                    $user_car['car_img'] = json_encode($car_img);
                    $user_car['create_time'] = date('Y-m-d H:i:s', time());
                    $user_car['last_time'] = date('Y-m-d H:i:s', time());
                }

                if ($user_car->save()) {
                    $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $result['car_id'] = $user_car['id'];
                } else {
                    $result['status'] = ERROR_DATA_BASE_ADD;
                    throw new Exception('error_database_add');
                }

                $model = new FeeOrder();
                //保存数据
                $model['user_id'] = $user_id; //用户id
                $model['community_id'] = $community_id; //小区id
                $model['merchant_id'] = $merchant_id; //商户id
                $model['building_number'] = $building_number; //楼号
                $model['order_type'] = $order_type; //订单类型
                $model['user_account'] = $user_account; //手机号
                $model['parking_month_num'] = $parking_month_num; //缴纳月数
                $model['order_money'] = $order_money; //应付金额
                $model['pay_money'] = $order_money; //应付金额
                $model['parking_type'] = $parking_type; //停车类型
                $model['car_id'] = $result['car_id']; //车辆id
                $model['order_no'] = $this -> getOrderNo(); //订单号
                $model['create_time'] = date('Y-m-d H:i:s', time()); //创建时间
                $model['last_time'] = date('Y-m-d H:i:s', time()); //修改时间

                if ($model->save()) {
                        $result['status'] = APPLY_CLASS_SUCCESS; //状态码
                        $result['errMsg'] = ''; //错误信息
                        $result['data'] = array('id' => $model->id);
                } else {
                    $result['status'] = ERROR_DATA_BASE_ADD;
                    throw new Exception('error_database_add');
                }
            } catch (Exception $e) {
                $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
                $result['errMsg'] = $e->getMessage(); //错误信息
            }
        }

        return $result;
    }

    /**
     * @return string
     * 生成订单号
     */
    private function getOrderNo()
    {
        $flag = FLAG_NO;
        do{
            $Code = 'ZH' . date('Ymd') . $this -> getNumRandChar(4);//订单号生成规则：ZH（智慧）+年/月/日+4位随机数
            $ModelCode = FeeOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
            if (empty($ModelCode)) {
                $flag = FLAG_YES;
            }
        } while($flag == FLAG_NO);

        return $Code;
    }

    /**
     * @param $length
     * @return null|string
     * 生成指定位数随机数（4位）
     */
    private function getNumRandChar($length){
        $str = null;
        $strPol = "0123456789";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /**
     * 获取费用订单列表
     */
    public function getFeeOrderList($order_type, $user_id){

        $result = array();
        try {
            //查询业主所有账单记录
            $list = Yii::app()->db->createCommand()->select('*')->from('wq_fee_order')->where('user_id = :user_id and order_type = :order_type', array(':user_id' => $user_id, ':order_type' => $order_type)) -> queryAll();

            //定义$now_time数组
            $now_time = array();
            if (!empty($list)) {
                foreach($list as $k => $v) {
                    $time = $v['date'];
                    $year = date('Y', strtotime($time));
                    $month = date('m', strtotime($time));
                    $now_time[$year][] = array(
                        'year' => $year,
                        'month' => $month,
                        'id' => $v['id'],
                        'water_ton' => $v['water_ton'],
                        'electricity' => $v['electricity'],
                        'floor_space' => $v['floor_space'],
                        'order_money' => $v['order_money'],
                        'pay_status' => $v['pay_status']
                    );
                }
            }
            $result['status'] = APPLY_CLASS_SUCCESS;
            $result['data'] = $now_time;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取费用订单详情
     */
    public function getFeeOrderInfo($id, $user_id, $order_no = '') {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            if (!empty($order_no)) {
                $criteria->addCondition('order_no = :order_no');
                $criteria->params[':order_no'] = $order_no;
            } else {
                $criteria->addCondition('id = :id');
                $criteria->addCondition('user_id = :user_id');
                $criteria->params[':id'] = $id;
                $criteria->params[':user_id'] = $user_id;
            }

            $model = FeeOrder::model() ->find($criteria);
            $user = User::model() -> find('id = :id', array(':id' => $user_id));
            $proprietor = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id));

            $data = array();
            if (!empty($model)) {
                $data['id'] = $model -> id;
                $data['order_no'] = $model -> order_no;
                $data['merchant_id'] = $model -> merchant_id;
                $data['date'] = $model -> date;
                $data['water_ton'] = $model -> water_ton;
                $data['order_money'] = $model -> order_money;
                $data['pay_money'] =  $model -> pay_money;
                $data['peak'] = $model -> peak;
                $data['valley'] = $model -> valley;
                $data['electricity'] = $model -> electricity;
                $data['pay_status'] = $model->pay_status;
                $data['car_id'] = $model->car_id;
                $data['property_fee_month_num'] = $model -> property_fee_month_num;
                $data['parking_month_num'] = $model -> parking_month_num;
                $data['order_type'] = $model -> order_type;

                if(!empty($user)) {
                    $data['name'] = $user-> name;
                    $data['tel'] = $user-> account;
                }

                if(!empty($proprietor)) {
                    $data['community'] = $proprietor -> community -> name;
                    $data['address'] =  $proprietor -> building_number.'号楼' .$proprietor -> room_number.'室';
                    $data['type'] = $proprietor -> type;

                    $data['water_fee'] =  $proprietor-> community -> water_fee_set;
                    $data['electricity_fee'] =  $proprietor -> community -> water_fee_set;
                }
                $car = ProprietorCar::model()->findByPk($data['car_id']);
                $data['car_brand'] = $car -> car_brand;
                $data['car_no'] = $car -> car_no;
                $data['car_img'] = $car -> car_img;

                $result['data'] = $data;
                $result['status'] = APPLY_CLASS_SUCCESS;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * @param $user_id
     * @return string
     * 获取水费历史账单
     */
    public function getWaterFeeList($user_id) {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition("user_id = :user_id");
            $criteria->params[':user_id'] = $user_id;
            $criteria->addCondition("order_type = :order_type");
            $criteria->params[':order_type'] = FEEORDER_TYPE_WATER_FEE;
            $criteria->addCondition("pay_status = :pay_status");
            $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
            $criteria->order = "create_time desc";
            $feeOrder = FeeOrder::model()->findAll($criteria);

            $data = array();
            if (!empty($feeOrder)) {
                foreach ($feeOrder as $k => $v) {
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['user_id'] = $v['user_id'];
                    $data[$k]['name'] = User::model() -> findByPk($user_id) -> name;
                    $data[$k]['tel'] = User::model() -> findByPk($user_id) -> account;
                    $data[$k]['type'] = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> type;
                    $data[$k]['date'] = $v['date'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                    $data[$k]['water_ton'] = $v['water_ton'];
                    $data[$k]['peak'] = $v['peak'];
                    $data[$k]['valley'] = $v['valley'];
                    $data[$k]['electricity'] = $v['electricity'];
                    $data[$k]['order_money'] = $v['order_money'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                }
            }
            $result['status'] = APPLY_CLASS_SUCCESS;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

     return json_encode($result);
    }

    /**
     * @param $user_id
     * @return string
     * 获取电费历史账单
     */
    public function getPowerFeeList($user_id) {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition("user_id = :user_id");
            $criteria->params[':user_id'] = $user_id;
            $criteria->addCondition("order_type = :order_type");
            $criteria->params[':order_type'] = FEEORDER_TYPE_ELECTRICITY_FEE;
            $criteria->addCondition("pay_status = :pay_status");
            $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
            $criteria->order = "create_time desc";
            $feeOrder = FeeOrder::model()->findAll($criteria);

            $data = array();
            if (!empty($feeOrder)) {
                foreach ($feeOrder as $k => $v) {
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['user_id'] = $v['user_id'];
                    $data[$k]['name'] = User::model() -> findByPk($user_id) -> name;
                    $data[$k]['order_type'] = $v['order_type'];
                    $data[$k]['tel'] = User::model() -> findByPk($user_id) -> account;
                    $data[$k]['type'] = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> type;
                    $data[$k]['date'] = $v['date'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                    $data[$k]['water_ton'] = $v['water_ton'];
                    $data[$k]['peak'] = $v['peak'];
                    $data[$k]['valley'] = $v['valley'];
                    $data[$k]['electricity'] = $v['electricity'];
                    $data[$k]['order_money'] = $v['order_money'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                }
            }

            $result['status'] = APPLY_CLASS_SUCCESS;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $user_id
     * @return string
     * 获取物业费历史账单
     */
    public function getPropertyFeeList($user_id) {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition("user_id = :user_id");
            $criteria->params[':user_id'] = $user_id;
            $criteria->addCondition("order_type = :order_type");
            $criteria->params[':order_type'] = FEEORDER_TYPE_PROPERTY_FEE;
            $criteria->addCondition("pay_status = :pay_status");
            $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
            $criteria->order = "create_time desc";
            $feeOrder = FeeOrder::model()->findAll($criteria);

            $data = array();
            if (!empty($feeOrder)) {
                foreach ($feeOrder as $k => $v) {
                    $user = User::model() -> findByPk($user_id);

                    $data[$k]['id'] = $v['id'];
                    $data[$k]['user_id'] = $v['user_id'];
                    $data[$k]['name'] = $user -> name;
                    $data[$k]['tel'] = $user -> account;
                    $data[$k]['money'] = $user -> money;
                    $data[$k]['type'] = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> type;
                    $data[$k]['date'] = $v['date'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                    $data[$k]['water_ton'] = $v['water_ton'];
                    $data[$k]['peak'] = $v['peak'];
                    $data[$k]['pay_time'] = $v['pay_time'];
                    $data[$k]['property_fee_month_num'] = $v['property_fee_month_num'];
                    $data[$k]['valley'] = $v['valley'];
                    $data[$k]['electricity'] = $v['electricity'];
                    $data[$k]['order_money'] = $v['order_money'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                }
            }
            $result['status'] = APPLY_CLASS_SUCCESS;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $user_id
     * @return string
     * 获取停车费历史账单
     */
    public function getParkingFeeList($user_id) {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition("user_id = :user_id");
            $criteria->params[':user_id'] = $user_id;
            $criteria->addCondition("order_type = :order_type");
            $criteria->params[':order_type'] = FEEORDER_TYPE_PARKING_FEE;
            $criteria->addCondition("pay_status = :pay_status");
            $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
            $criteria->order = "create_time desc";
            $feeOrder = FeeOrder::model()->findAll($criteria);

            $data = array();
            if (!empty($feeOrder)) {
                foreach ($feeOrder as $k => $v) {
                    $user = User::model() -> findByPk($user_id);
                    $proprietor = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id));
                    $data[$k]['id'] = $v['id'];
                    $data[$k]['user_id'] = $v['user_id'];
                    $data[$k]['name'] = $user -> name;
                    $data[$k]['tel'] = $user -> account;
                    $data[$k]['money'] = $user -> money;
                    $data[$k]['type'] = $proprietor -> type;
                    $data[$k]['date'] = $v['date'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                    $data[$k]['water_ton'] = $v['water_ton'];
                    $data[$k]['peak'] = $v['peak'];
                    $data[$k]['pay_time'] = $v['pay_time'];
                    $data[$k]['parking_month_num'] = $v['parking_month_num'];
                    $data[$k]['property_fee_month_num'] = $v['property_fee_month_num'];
                    $data[$k]['valley'] = $v['valley'];
                    $data[$k]['electricity'] = $v['electricity'];
                    $data[$k]['order_money'] = $v['order_money'];
                    $data[$k]['pay_status'] = $v['pay_status'];
                }
            }
            $result['status'] = APPLY_CLASS_SUCCESS;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 统计费用订单类型
     */
    public function getFeeTypeCount($user_id) {
        $result = array();
        try {
            //统计水费订单
            $water_fee = FeeOrder::model() -> count('order_type = :order_type and flag = :flag and user_id = :user_id and pay_status = :pay_status', array(
                ':order_type' => FEEORDER_TYPE_WATER_FEE,
                ':flag' => FLAG_NO,
                ':user_id' => $user_id,
                ':pay_status' => ORDER_STATUS_PAID
            ));
            //统计电费订单
            $power_fee = FeeOrder::model() -> count('order_type = :order_type and flag = :flag and user_id = :user_id and pay_status = :pay_status', array(
                ':order_type' => FEEORDER_TYPE_ELECTRICITY_FEE,
                ':flag' => FLAG_NO,
                ':user_id' => $user_id,
                ':pay_status' => ORDER_STATUS_PAID
            ));
            //统计物业费订单
            $property_fee = FeeOrder::model() -> count('order_type = :order_type and flag = :flag and user_id = :user_id and pay_status = :pay_status', array(
                ':order_type' => FEEORDER_TYPE_PROPERTY_FEE,
                ':flag' => FLAG_NO,
                ':user_id' => $user_id,
                ':pay_status' => ORDER_STATUS_PAID
            ));
            //统计停车费订单
            $parking_fee = FeeOrder::model() -> count('order_type = :order_type and flag = :flag and user_id = :user_id and pay_status = :pay_status', array(
                ':order_type' => FEEORDER_TYPE_PARKING_FEE,
                ':flag' => FLAG_NO,
                ':user_id' => $user_id,
                ':pay_status' => ORDER_STATUS_PAID
            ));

            $result['status'] = APPLY_CLASS_SUCCESS;
            $result['water_fee'] = $water_fee;
            $result['power_fee'] = $power_fee;
            $result['property_fee'] = $property_fee;
            $result['parking_fee'] = $parking_fee;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 添加物业费缴费订单
     */
    public function addPropertyFee($info,$user_id) {
        $result = array();
        $name = User::model() -> find('id = :id', array(':id' => $user_id)) -> name;
        $tel = User::model() -> find('id = :id', array(':id' => $user_id)) -> account;
        $address = Proprietor::model() -> find('id = :id', array(':id' => $user_id)) -> community -> name;
        $date = $info['date'];

        try {
            //判断缴费日期是否选择
            if (!isset($date) && empty($date)) {
                throw new Exception('未选择日期');
            }

            $transaction = Yii::app()->db->beginTransaction();
            $model = new FeeOrder();
            $model -> user_id = $user_id;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : APPLY_CLASS_FAIL;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $order_no
     * @param $pay_money
     * @return bool
     *
     * 修改订单支付金额
     */
    public function editFeeOrderPayMoney($order_no, $pay_money) {
        $rs = FeeOrder::model()->updateAll(array('pay_money' => $pay_money), 'order_no = :order_no', array(':order_no' => $order_no));
        if ($rs >0) {
            return true;
        }
        return false;
    }

    /**
     * @param $order_no
     * @param $pay_channel
     * @param $trade_no
     * @return string
     * 修改订单支付状态
     */
    public function editFeeOrder($order_no, $pay_channel, $trade_no) {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->addCondition('order_no = :order_no');
        $criteria->params[':order_no'] = $order_no;
        $feeOrder = FeeOrder::model()->find($criteria);
        if (!empty($feeOrder)) {
            $feeOrder->pay_status = ORDER_STATUS_PAID;
            //$feeOrder->pay_channel = $pay_channel;
            $feeOrder->trade_no = $trade_no;
            $feeOrder->pay_time = new CDbExpression('now()');
            if ($feeOrder->update()) {
                $result['status'] = APPLY_CLASS_SUCCESS;
            } else {
                $result['status'] = APPLY_CLASS_FAIL;
            }
        }

        return json_encode($result);
    }


}