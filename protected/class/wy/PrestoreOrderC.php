<?php
/**
 * Created by PhpStorm.
 * User: sundi
 * Date: 2016/6/23
 * Time: 15:37
 */
include_once(dirname(__FILE__).'/../mainClass.php');

//预存记录类
class PrestoreOrderC extends mainClass
{
    /**
     * @param $info
     * @param $encrypt_id
     * @param $user_id
     * 创建预存金额订单
     */
    public function addPrestoreOrder($info, $encrypt_id, $user_id) {
        $result = array();
        $merchant = Merchant::model()->find('encrypt_id = :encrypt_id', array(':encrypt_id' => $encrypt_id));

        $prestoreOrder = new PrestoreOrder();
        $prestoreOrder->order_no = $this->getOrderNo();
        $prestoreOrder->merchant_id = $merchant->id;
        $prestoreOrder->user_id = $user_id;
        $prestoreOrder->prestore_money = $info['money'];
        $prestoreOrder->create_time = date('Y-m-d H:i:s', time()); //创建时间
        $prestoreOrder->last_time = date('Y-m-d H:i:s', time()); //修改时间
        if ($prestoreOrder->save()) {
            $result['status'] = APPLY_CLASS_SUCCESS; //状态码
            $result['errMsg'] = ''; //错误信息
            $result['data'] = array('id' => $prestoreOrder->id);
        } else {
            $result['status'] = ERROR_DATA_BASE_ADD;
            throw new Exception('error_database_add');
        }

        return json_encode($result);
    }

    /**
     * @param $user_id
     * @return string
     * 获取预存订单列表
     */
    public function getPrestoreOrderList($user_id) {
        $result = array();
        try {
            $cri = new CDbCriteria();
            $cri -> addCondition('flag = :flag');
            $cri -> params[':flag'] = FLAG_NO;
            $cri -> addCondition('user_id = :user_id');
            $cri -> params[':user_id'] = $user_id;
            $cri ->order = "create_time desc";
            $model = PrestoreOrder::model()->findAll($cri);

            $data = array();
            if (!empty($model)) {
                foreach ($model as $k => $v) {
                    $data[$k]['id'] = $v -> id;
                    $data[$k]['user_id'] = $v -> user_id;
                    $data[$k]['name'] = User::model() -> findByPk($user_id) -> name;
                    $data[$k]['tel'] = User::model() -> findByPk($user_id) -> account;
                    $data[$k]['type'] = Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> type;
                    $data[$k]['merchant_id'] = $v -> merchant_id;
                    $data[$k]['order_no'] = $v -> order_no;
                    $data[$k]['prestore_money'] = $v -> prestore_money;
                    $data[$k]['pay_status'] = $v -> pay_status;
                    $data[$k]['user_prestore'] = $v -> user_prestore;
                    $data[$k]['create_time'] = $v -> create_time;
                    $data[$k]['last_time'] = $v -> last_time;
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
     * @param $prestoreOrder_id
     * @param $user_id
     * 获取预存订单详情
     */
    public function getPrestoreOrderInfo($prestoreOrder_id, $user_id, $order_no = '') {
        $result = array();
        $criteria = new CDbCriteria();
        if (!empty($order_no)) {
            $criteria->addCondition('order_no = :order_no');
            $criteria->params[':order_no'] = $order_no;
        } else {
            $criteria->addCondition('id = :id');
            $criteria->addCondition('user_id = :user_id');
            $criteria->params[':id'] = $prestoreOrder_id;
            $criteria->params[':user_id'] = $user_id;
        }
        $prestoreOrder = PrestoreOrder::model()->find($criteria);
        if (!empty($prestoreOrder)) {
            $data['id'] = $prestoreOrder -> id;
            $data['order_no'] = $prestoreOrder -> order_no;
            $data['merchant_id'] = $prestoreOrder -> merchant_id;
            $data['order_money'] = $prestoreOrder -> prestore_money;
            $data['pay_status'] = $prestoreOrder -> pay_status;
            $result['data'] = $data;
            $result['status'] = APPLY_CLASS_SUCCESS;
        }

        return json_encode($result);
    }

    /**
     * 修改预存订单状态
     */
    public function updatePrestoreOrder($order_no, $pay_channel, $trade_no) {
        $result = array();
        $criteria = new CDbCriteria();
        $criteria->addCondition('order_no = :order_no');
        $criteria->params[':order_no'] = $order_no;
        $prestoreOrder = PrestoreOrder::model()->find($criteria);
        if (!empty($prestoreOrder)) {
            $prestoreOrder->pay_status = ORDER_STATUS_PAID;
            $prestoreOrder->pay_channel = $pay_channel;
            $prestoreOrder->trade_no = $trade_no;
            if ($prestoreOrder->update()) {
                //增加用户的储值金额
                $user = User::model()->findByPk($prestoreOrder->user_id);
                $user->money = $user->money + $prestoreOrder->prestore_money;
                if ($user->update()) {
                    $result['status'] = APPLY_CLASS_SUCCESS;
                } else {
                    $result['status'] = APPLY_CLASS_FAIL;
                }
            } else {
                $result['status'] = APPLY_CLASS_FAIL;
            }
        }

        return json_encode($result);
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


}