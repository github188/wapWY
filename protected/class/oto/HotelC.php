<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * 酒店订房
 */
class HotelC extends mainClass
{
    public $page = null;
    private static $_instance = NULL;

    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 添加酒店
     */
    public function addHotel($merchant_id, $name, $address, $tel, $img)
    {
        $result = array();
        try {
            $transaction = Yii::app()->db->beginTransaction();
            //数据库查询
            $model = Hotel::model()->find('merchant_id = :merchant_id AND flag = :flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO

            ));
            if (!empty($model)) {
                //修改操作
                $model['merchant_id'] = $merchant_id;
                $model['name'] = $name; //酒店名称
                $model['address'] = $address;
                $model['tel'] = $tel;
                $model['img'] = $img;
            } else {
                //保存数据
                $model = new Hotel();
                $model['merchant_id'] = $merchant_id;
                $model['name'] = $name; //酒店名称
                $model['address'] = $address;
                $model['tel'] = $tel;
                $model['img'] = $img;
                $model['create_time'] = date('Y-m-d H:i:s', time());
                $model['last_time'] = date('Y-m-d H:i:s', time());
            }
            if ($model->save()) {
                $transaction->commit();
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败';
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 添加房间
     */
    public function addHotelRoom($store_id, $merchant_id, $name, $price, $room_img, $room_details)
    {
        $result = array();
        try {
            $transaction = Yii::app()->db->beginTransaction();
            //保存数据
            $model = new HotelRoom();
            $model['store_id'] = $store_id;
            $model['merchant_id'] = $merchant_id;
            $model['name'] = $name; //房间类型
            $model['price'] = $price;
            $model['room_img'] = $room_img;
            $model['room_details'] = $room_details;
            $model['create_time'] = date('Y-m-d H:i:s', time());
            if ($model->save()) {
                $transaction->commit();
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $transaction->rollBack();
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败';
                $result['data'] = '';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }


    /**
     * 编辑房间信息
     * @param $room_id
     */
    public function editHotelRoom($id, $store_id, $merchant_id, $name, $price, $room_img, $room_details)
    {
        $result = array();
        try {
            $transaction = Yii::app()->db->beginTransaction();
            $model = HotelRoom::model()->findByPk($id);

            $model['store_id'] = $store_id;
            $model['merchant_id'] = $merchant_id;
            $model['name'] = $name; //房间类型
            $model['price'] = $price;
            $model['room_img'] = $room_img;
            $model['room_details'] = $room_details;

            if ($model->save()) {
                $transaction->commit();
                $result['data'] = $model;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $transaction->rollBack();
                $result['data'] = $model;
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $result['data'] = $model;
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 删除房间
     */
    public function deleteHotelRoom($id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $model = HotelRoom::model()->findByPk($id);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('删除的数据不存在');
            }
            //修改删除标识
            $model['flag'] = FLAG_YES;

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '数据保存失败';
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 获取房间类型列表,根据商户id
     * @param $merchant_id
     */
    public function getHotelRoomList($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数不能为空');
            }
            $data = array();
            //查找商户下所有房间类型    
            $criteria = new CDbCriteria();
            $criteria->addCondition("flag = :flag");
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition("merchant_id = :merchant_id");
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->order = 'create_time DESC';
            //分页
            $count = HotelRoom::model()->count($criteria);
            $pages = new CPagination($count);
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);

            $model = HotelRoom::model()->findAll($criteria);
            if ($model) {
                foreach ($model as $key => $value) {
                    $data['list'][$key]['id'] = $value['id'];
                    $data['list'][$key]['merchant_id'] = $value['merchant_id'];
                    $data['list'][$key]['store_id'] = $value['store_id'];
                    $data['list'][$key]['name'] = $value['name'];
                    $data['list'][$key]['price'] = $value['price'];
                    $data['list'][$key]['room_img'] = $value['room_img'];
                    $data['list'][$key]['room_details'] = $value['room_details'];
                    $data['list'][$key]['create_time'] = $value['create_time'];
                }
            } else {
                $data['list'] = '';
            }
            $this->page = $pages;
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取房间信息,根据房间id
     * @param $room_id
     */
    public function getHotelRoomInfo($room_id)
    {
        $result = array();
        try {
            //验证参数
            if (empty($room_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数不能为空');
            }
            //数据库查询
            $model = HotelRoom::model()->find('id = :id', array(':id' => $room_id));
            $data = array();
            if (!empty($model)) {
                $data['id'] = $model['id'];
                $data['merchant_id'] = $model['merchant_id'];
                $data['store_id'] = $model['store_id'];
                $data['name'] = $model['name'];
                $data['price'] = $model['price'];
                $data['room_img'] = $model['room_img'];
                $data['room_details'] = $model['room_details'];
                $data['create_time'] = $model['create_time'];
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 根据商户id,获取酒店信息
     * @param $store_id
     */
    public function getHotelInfo($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数不能为空');
            }
            $data = array();
            //查找所有房间类型
            $hotel = Hotel::model()->find('merchant_id =:merchant_id and flag =:flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            if ($hotel) {
                $data['id'] = $hotel->id;
                $data['merchant_id'] = $hotel->merchant_id;
                $data['name'] = $hotel->name;
                $data['address'] = $hotel->address;
                $data['tel'] = $hotel->tel;
                $data['img'] = $hotel->img;
                $data['create_time'] = $hotel->create_time;
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取旅馆首页轮播图
     */
    public function getHotelBanner($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $hotel = Hotel::model()->findAll($criteria);
            if ($hotel) {
                foreach ($hotel as $key => $v) {
                    $data[$key]['img'] = $hotel[$key]['img'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取商户所有房间
     */
    public function queryHotelAllRoom($merchant_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $hotel = HotelRoom::model()->findAll($criteria);
            if ($hotel) {
                foreach ($hotel as $key => $v) {
                    $data[$key]['id'] = $hotel[$key]['id'];
                    $data[$key]['store_id'] = $hotel[$key]['store_id'];
                    $data[$key]['name'] = $hotel[$key]['name'];
                    $data[$key]['price'] = $hotel[$key]['price'];
                    $data[$key]['room_img'] = $hotel[$key]['room_img'];
                }

                foreach ($data as $key => $v) {
                    $roomLists[$v['store_id']][] = $v;
                }

                foreach ($roomLists as $key => &$v) {
                    $store = Store::model()->findAllByPk($key);
                    foreach ($v as &$item) {
                        $item['hotel_name'] = $store[0]['name'];
                        $item['branch_name'] = $store[0]['branch_name'];
                    }
                }

                $result['status'] = ERROR_NONE;
                $result['data'] = $roomLists;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 获取房间详情
     * @param $id
     * @return string
     */
    public function getHotelRoomDetail($id)
    {
        $result = array();
        try {
            if (empty($id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $room = HotelRoom::model()->findByPk($id);
            if (!empty($room)) {
                $result['status'] = ERROR_NONE;
                $data['id'] = $room->id;
                $data['name'] = $room->name;
                $data['price'] = $room->price;
                $data['room_img'] = $room->room_img;
                $data['room_details'] = $room->room_details;
                $data['store_id'] = $room->store_id;

                //获取门店名称
                $store = Store::model()->findAllByPk($data['store_id']);
                $data['hotel_name'] = $store[0]['name'];
                $data['telephone'] = $store[0]['telephone'];

                $result['data'] = $data;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 创建订单
     * @param $user_id
     * @param $merchant_id
     * @param $room_id
     * @param $store_id
     * @param $check_in_time
     * @param $check_out_time
     * @param $room_num
     * @param $name
     * @param $mobile
     * @return string
     */
    public function createHotelOrder($user_id, $merchant_id, $room_id, $store_id, $check_in_time, $check_out_time, $room_num, $name, $mobile)
    {
        $result = array();
        try {
            $order = new HotelOrder();
            $order->user_id = $user_id;
            $order->merchant_id = $merchant_id;
            $order->store_id = $store_id;
            $order->order_no = $this->getHotelOrderNo();
            $order->hotel_room_id = $room_id;
            $order->num = $room_num;
            $order->person_name = $name;
            $order->person_tel = $mobile;
            $order->check_in_time = $check_in_time;
            $order->check_out_time = $check_out_time;
            $order->create_time = new CDbExpression('now()');

            if ($order->save()) {
                //获取房间名称
                $room = HotelRoom::model()->findAllByPk($room_id);

                $result['status'] = ERROR_NONE;
                $result['data']['name'] = $order->person_name;
                $result['data']['order_no'] = $order->order_no;
                $result['data']['room_name'] = $room[0]['name'];
                $result['data']['check_in_time'] = $order->check_in_time;
                $result['data']['check_out_time'] = $order->check_out_time;
                $result['data']['price'] = $room[0]['price'];
                $result['data']['num'] = $order->num;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('订单保存失败');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 生成订单编号
     */
    public function getHotelOrderNo()
    {
        $Code = date('Ymd') . mt_rand(000001, 999999);
        $Order = HotelOrder::model()->find('order_no = :order_no', array(':order_no' => $Code));
        if (!empty($Order)) {
            while ($Code == $Order->order_no) {
                $Code = date('Ymd') . mt_rand(000001, 999999);
                $Order = HotelOrder::model()->find('order_no = :order_no', array(':order_no' => $Code));
            }
        }
        return $Code;
    }

    /** 获取订单详情
     * @param $order_no
     * @return string
     */
    public function getHotelOrderDetail($order_no)
    {
        $result = array();
        try {
            if (empty($order_no)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_no = :order_no');
            $criteria->params[':order_no'] = $order_no;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $order = HotelOrder::model()->findAll($criteria);
            if (!empty($order)) {
                $result['status'] = ERROR_NONE;
                $data['id'] = $order[0]['id'];
                $data['merchant_id'] = $order[0]['merchant_id'];
                $data['store_id'] = $order[0]['store_id'];
                $data['order_no'] = $order[0]['order_no'];
                $data['hotel_room_id'] = $order[0]['hotel_room_id'];
                $data['num'] = $order[0]['num'];
                $data['person_name'] = $order[0]['person_name'];
                $data['person_tel'] = $order[0]['person_tel'];
                $data['check_in_time'] = $order[0]['check_in_time'];
                $data['check_out_time'] = $order[0]['check_out_time'];
                $data['status'] = $order[0]['status'];
                $data['refuse_time'] = $order[0]['refuse_time'];
                $data['confirm_time'] = $order[0]['confirm_time'];
                $data['cancel_time'] = $order[0]['cancel_time'];
                $data['create_time'] = $order[0]['create_time'];
                //查询房间类型
                $hotelRoom = HotelRoom::model()->findAllByPk($order[0]['hotel_room_id']);
                $data['room_name'] = $hotelRoom[0]['name'];
                //查询客服电话
                $store = Store::model()->findAllByPk($order[0]['store_id']);
                $data['service_tel'] = $store[0]['telephone'];

                $result['data'] = $data;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 用户获取订单列表
     * @param $user_id
     * @param $order_status
     * @param $merchant_id
     * @return string
     */
    public function getHotelOrderList($user_id, $order_status, $merchant_id)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('user_id=:user_id');
            $criteria->params[':user_id'] = $user_id;
            if ($order_status == HOTEL_ORDER_STATUS_CANCEL) {
                $criteria->addCondition('status = :status or status = :status1');
                $criteria->params[':status'] = HOTEL_ORDER_STATUS_CANCEL;
                $criteria->params[':status1'] = HOTEL_ORDER_STATUS_REFUSE;
            } else {
                $criteria->addCondition('status=:status');
                $criteria->params[':status'] = $order_status;
            }
            $criteria->addCondition('merchant_id=:merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = 'create_time DESC';
            $order = HotelOrder::model()->findAll($criteria);

            $user = User::model()->findAllByPk($user_id);
            $result['avatar'] = $user[0]['avatar'];
            $result['nickname'] = $user[0]['nickname'];
            if (!empty($order)) {
                foreach ($order as $key => $v) {
                    $data[$key]['id'] = $order[$key]['id'];
                    $data[$key]['merchant_id'] = $order[$key]['merchant_id'];
                    $data[$key]['store_id'] = $order[$key]['store_id'];
                    $data[$key]['order_no'] = $order[$key]['order_no'];
                    $data[$key]['hotel_room_id'] = $order[$key]['hotel_room_id'];
                    $data[$key]['num'] = $order[$key]['num'];
                    $data[$key]['person_name'] = $order[$key]['person_name'];
                    $data[$key]['person_tel'] = $order[$key]['person_tel'];
                    $data[$key]['check_in_time'] = date('Y.m.d', strtotime($order[$key]['check_in_time']));
                    $data[$key]['check_out_time'] = date('Y.m.d', strtotime($order[$key]['check_out_time']));
                    $data[$key]['status'] = $order[$key]['status'];
                    $data[$key]['refuse_time'] = $order[$key]['refuse_time'];
                    $data[$key]['confirm_time'] = $order[$key]['confirm_time'];
                    $data[$key]['cancel_time'] = $order[$key]['cancel_time'];
                    $data[$key]['create_time'] = $order[$key]['create_time'];
                    //查询房间类型和房间图片
                    $hotelRoom = HotelRoom::model()->findAllByPk($order[$key]['hotel_room_id']);
                    $data[$key]['room_name'] = $hotelRoom[0]['name'];
                    $data[$key]['room_img'] = $hotelRoom[0]['room_img'];
                    $data[$key]['price'] = $hotelRoom[0]['price'];
                    //查询门店名称和客服电话
                    $store = Store::model()->findAllByPk($order[$key]['store_id']);
                    $data[$key]['hotel_name'] = $store[0]['name'];
                    $data[$key]['branch_name'] = $store[0]['branch_name'];
                    $data[$key]['service_tel'] = $store[0]['telephone'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 商户获取订单列表
     * @param $merchant_id
     * @return string
     */
    public function getHotelOrderListAll($merchant_id, $status = NULL, $keyword = NULL, $time = NULL)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            if (!empty($status)) { //订单状态
                $criteria->addCondition('status=:status');
                $criteria->params[':status'] = $status;
            }

            if (!empty($time)) { //下单时间搜索
                $Time = array();
                $Time = explode('-', $time);
                $criteria->addCondition('create_time >= :time1');
                $criteria->params[':time1'] = date('Y-m-d 00:00:00', strtotime(trim($Time[0])));
                $criteria->addCondition('create_time <= :time2');
                $criteria->params[':time2'] = date('Y-m-d 23:59:59', strtotime(trim($Time[1])));
            }
            //关键字查找
            if (!empty($keyword)) {
                $criteria->addCondition('order_no=:order_no or person_name like :person_name or person_tel like :person_tel');
                $criteria->params[':order_no'] = $keyword;
                $criteria->params[':person_name'] = '%' . $keyword . '%';
                $criteria->params[':person_tel'] = '%' . $keyword . '%';
            }
            $criteria->addCondition('merchant_id=:merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = 'create_time DESC';
            //分页
            $count = HotelOrder::model()->count($criteria);
            $pages = new CPagination($count);
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $order = HotelOrder::model()->findAll($criteria);
            $data = array();
            if (!empty($order)) {
                foreach ($order as $key => $v) {
                    $data[$key]['id'] = $order[$key]['id'];
                    $data[$key]['merchant_id'] = $order[$key]['merchant_id'];
                    $data[$key]['store_id'] = $order[$key]['store_id'];
                    $data[$key]['order_no'] = $order[$key]['order_no'];
                    $data[$key]['hotel_room_id'] = $order[$key]['hotel_room_id'];
                    $data[$key]['num'] = $order[$key]['num'];
                    $data[$key]['person_name'] = $order[$key]['person_name'];
                    $data[$key]['person_tel'] = $order[$key]['person_tel'];
                    $data[$key]['check_in_time'] = $order[$key]['check_in_time'];
                    $data[$key]['check_out_time'] = $order[$key]['check_out_time'];
                    $data[$key]['status'] = $order[$key]['status'];
                    $data[$key]['refuse_time'] = $order[$key]['refuse_time'];
                    $data[$key]['confirm_time'] = $order[$key]['confirm_time'];
                    $data[$key]['cancel_time'] = $order[$key]['cancel_time'];
                    $data[$key]['create_time'] = $order[$key]['create_time'];
                    //查询房间类型和房间图片
                    $hotelRoom = HotelRoom::model()->findAllByPk($order[$key]['hotel_room_id']);
                    $data[$key]['room_name'] = $hotelRoom[0]['name'];
                    $data[$key]['room_img'] = $hotelRoom[0]['room_img'];
                    $data[$key]['price'] = $hotelRoom[0]['price'];
                }

            }
            $this->page = $pages;
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 获取$order_status状态的订单数
     * @param $user_id
     * @param $order_status
     * @param $merchant_id
     * @return int
     */
    public function getHotelOrderStatusCount($user_id, $order_status, $merchant_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id=:user_id');
        $criteria->params[':user_id'] = $user_id;
        $criteria->addCondition('merchant_id=:merchant_id');
        $criteria->params[':merchant_id'] = $merchant_id;
        $criteria->addCondition('status=:status');
        $criteria->params[':status'] = $order_status;
        $criteria->addCondition('flag=:flag');
        $criteria->params[':flag'] = FLAG_NO;
        $model = HotelOrder::model()->findAll($criteria);
        return count($model);
    }

    /** 取消订单
     * @param $order_no
     * @return string
     */
    public function cancleHotelOrder($order_no)
    {
        $result = array();
        try {
            if (empty($order_no)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_no=:order_no');
            $criteria->params[':order_no'] = $order_no;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $order = HotelOrder::model()->find($criteria);
            $order->status = HOTEL_ORDER_STATUS_CANCEL;
            if ($order->update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 操作员登录验证
     * @param $account
     * @param $password
     * @return string
     */
    public function operatorLoginCheck($account, $password)
    {
        $result = array();
        try {
            if (empty($account) || empty($password)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('account=:account');
            $criteria->params[':account'] = $account;
            $criteria->addCondition('pwd=:pwd');
            $criteria->params[':pwd'] = md5($password);
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('status=:status');
            $criteria->params[':status'] = OPERATOR_STATUS_NORMAL;
            $operator = Operator::model()->findAll($criteria);

            if (!empty($operator)) {
                $store_id = $operator[0]['store_id'];
                $store = Store::model()->findAllByPk($store_id);
                $merchant_id = $store[0]['merchant_id'];
                $merchant = Merchant::model()->findAllByPk($merchant_id);
                $encrypt_id = $merchant[0]['encrypt_id'];

                $result['status'] = ERROR_NONE;
                $data['operator_id'] = $operator[0]['id'];
                $data['store_id'] = $store_id;
                $data['encrypt_id'] = $encrypt_id;
                $result['data'] = $data;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '账号或密码错误';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 通过操作员获取商户加密id
     * @param $operator_id
     * @return string
     */
    public function getEncrypt_id($operator_id)
    {
        $result = array();
        try {
            if (empty($operator_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('operator_id=:operator_id');
            $criteria->params[':operator_id'] = $operator_id;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('status=:status');
            $criteria->params[':status'] = OPERATOR_STATUS_NORMAL;
            $operator = Operator::model()->findAllByPk($operator_id);
            
            if (!empty($operator)) {
                $store_id = $operator[0]['store_id'];
                $store = Store::model()->findAllByPk($store_id);
                $merchant_id = $store[0]['merchant_id'];
                $merchant = Merchant::model()->findAllByPk($merchant_id);
                $encrypt_id = $merchant[0]['encrypt_id'];
                
                $result['status'] = ERROR_NONE;
                $result['data'] = $encrypt_id;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 获取所有订单
     * @param $store_id
     * @param $order_status
     * @return string
     */
    public function getHotelAllOrder($store_id, $order_status)
    {
        $result = array();
        try {
            if (empty($store_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('store_id=:store_id');
            $criteria->params[':store_id'] = $store_id;
            if ($order_status == HOTEL_ORDER_STATUS_CANCEL) {
                $criteria->addCondition('status = :status or status = :status1');
                $criteria->params[':status'] = HOTEL_ORDER_STATUS_CANCEL;
                $criteria->params[':status1'] = HOTEL_ORDER_STATUS_REFUSE;
            } else {
                $criteria->addCondition('status=:status');
                $criteria->params[':status'] = $order_status;
            }
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->order = 'create_time DESC';
            $order = HotelOrder::model()->findAll($criteria);

            if (!empty($order)) {
                foreach ($order as $key => $v) {
                    $data[$key]['id'] = $order[$key]['id'];
                    $data[$key]['merchant_id'] = $order[$key]['merchant_id'];
                    $data[$key]['store_id'] = $order[$key]['store_id'];
                    $data[$key]['order_no'] = $order[$key]['order_no'];
                    $data[$key]['hotel_room_id'] = $order[$key]['hotel_room_id'];
                    $data[$key]['num'] = $order[$key]['num'];
                    $data[$key]['person_name'] = $order[$key]['person_name'];
                    $data[$key]['person_tel'] = $order[$key]['person_tel'];
                    $data[$key]['check_in_time'] = date('Y.m.d', strtotime($order[$key]['check_in_time']));
                    $data[$key]['check_out_time'] = date('Y.m.d', strtotime($order[$key]['check_out_time']));
                    $data[$key]['status'] = $order[$key]['status'];
                    $data[$key]['refuse_time'] = $order[$key]['refuse_time'];
                    $data[$key]['confirm_time'] = $order[$key]['confirm_time'];
                    $data[$key]['cancel_time'] = $order[$key]['cancel_time'];
                    $data[$key]['create_time'] = $order[$key]['create_time'];
                    //查询房间类型和房间图片
                    $hotelRoom = HotelRoom::model()->findAllByPk($order[$key]['hotel_room_id']);
                    $data[$key]['room_name'] = $hotelRoom[0]['name'];
                    $data[$key]['room_img'] = $hotelRoom[0]['room_img'];
                    $data[$key]['price'] = $hotelRoom[0]['price'];
                    //查询门店名称和客服电话
                    $store = Store::model()->findAllByPk($order[$key]['store_id']);
                    $data[$key]['hotel_name'] = $store[0]['name'];
                    $data[$key]['service_tel'] = $store[0]['telephone'];
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
                $result['errMsg'] = '';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 获取门店下$order_status状态的订单数
     * @param $store_id
     * @param $order_status
     * @return int
     */
    public function getStoreOrderStatusCount($store_id, $order_status)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('store_id=:store_id');
        $criteria->params[':store_id'] = $store_id;
        $criteria->addCondition('status=:status');
        $criteria->params[':status'] = $order_status;
        $criteria->addCondition('flag=:flag');
        $criteria->params[':flag'] = FLAG_NO;
        $model = HotelOrder::model()->findAll($criteria);
        return count($model);
    }

    /** 确定订单
     * @param $order_no
     * @return string
     */
    public function confirmHotelOrder($order_no)
    {
        $result = array();
        try {
            if (empty($order_no)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_no=:order_no');
            $criteria->params[':order_no'] = $order_no;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $order = HotelOrder::model()->find($criteria);
            $order->status = HOTEL_ORDER_STATUS_CONFIRM;
            if ($order->update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 拒绝订单
     * @param $order_no
     * @return string
     */
    public function refuseHotelOrder($order_no)
    {
        $result = array();
        try {
            if (empty($order_no)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_no=:order_no');
            $criteria->params[':order_no'] = $order_no;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $order = HotelOrder::model()->find($criteria);
            $order->status = HOTEL_ORDER_STATUS_REFUSE;
            if ($order->update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /** 入住订单
     * @param $order_no
     * @return string
     */
    public function checkinHotelOrder($order_no)
    {
        $result = array();
        try {
            if (empty($order_no)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数错误');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_no=:order_no');
            $criteria->params[':order_no'] = $order_no;
            $criteria->addCondition('flag=:flag');
            $criteria->params[':flag'] = FLAG_NO;
            $order = HotelOrder::model()->find($criteria);
            $order->status = HOTEL_ORDER_STATUS_CHECKIN;
            if ($order->update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}