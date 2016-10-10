<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * 商城订单类
 */
class OrderMall extends mainClass
{

    public $page = null;

    /**
     * 获取商城订单列表
     * $order_no    订单号
     * $order_type  订单类型
     * $user_name   收货人姓名
     * $user_phone  收货人手机
     * $time        下单时间
     * $order_status  订单状态
     * $pay_channel   支付渠道
     * $pay_status    订单付款状态
     */
    public function getShopOrderList($order_no, $order_type, $user_name, $user_phone, $time, $order_status, $pay_channel, $pay_status, $merchant_id = '')
    {
        $result = array();

        $criteria = new CDbCriteria();
        $criteria->addCondition('t.flag = :flag');
        $criteria->params = array(':flag' => FLAG_NO);
        $criteria->order = 't.create_time desc';

        $criteria->addCondition("t.merchant_id=:merchant_id and order_no like 'SC%'");
        $criteria->params[':merchant_id'] = $merchant_id;

        //搜索
        if (!empty($order_no)) { //订单号搜索
            $criteria->addCondition('order_no=:order_no');
            $criteria->params[':order_no'] = $order_no;
        }

        if (!empty($order_type)) { //订单类型搜索
            $criteria->addCondition('order_type=:order_type');
            $criteria->params[':order_type'] = $order_type;
        }/*else{
			$criteria -> addCondition('order_type !=:order_type');
			$criteria -> params[':order_type'] = ORDER_TYPE_CASHIER;
		}*/

        if (!empty($time)) { //下单时间搜索
            $Time = array();
            $Time = explode('-', $time);
            $criteria->addBetweenCondition('t.create_time', $Time[0] . ' 00:00:00', $Time[1] . ' 23:59:59');
        }

        if (!empty($order_status)) { //订单状态搜索
// 			$criteria -> addCondition('order_status=:order_status');
// 			$criteria -> params[':order_status'] = $order_status;
            if ($order_status == ORDER_STATUS_WAITFORDELIVER) { //如果是待发货  则显示的是已付款的待发货
                $criteria->addCondition('order_status=:order_status and pay_status=:pay_status');
                $criteria->params[':order_status'] = $order_status;
                $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
            } else {
                $criteria->addCondition('order_status=:order_status');
                $criteria->params[':order_status'] = $order_status;
            }
        }


        if (!empty($pay_channel)) { //支付渠道搜索
            $criteria->addCondition('pay_channel=:pay_channel');
            $criteria->params[':pay_channel'] = $pay_channel;
        }

        if (!empty($user_name)) { //收货人姓名搜索
            $criteria->addCondition("user.name like '$user_name'");
        }

        if (!empty($user_phone)) { //收货人手机搜索
            $criteria->addCondition("user.account like '$user_phone'");
        }

        if (!empty($pay_status)) { //订单付款状态
            $criteria->addCondition('pay_status=:pay_status');
            $criteria->params[':pay_status'] = $pay_status;
            if ($pay_status == ORDER_STATUS_UNPAID) {
                $criteria->addCondition('order_status!=:order_status');
                $criteria->params[':order_status'] = ORDER_STATUS_CANCEL;
            }
        }

        //分页
        $pages = new CPagination(Order::model()->with('user')->count($criteria));
        $pages->pageSize = Yii::app()->params['perPage'];
        $pages->applyLimit($criteria);
        $this->page = $pages;

        $model = Order::model()->with('user')->findAll($criteria);

        $data = array();
        if (!empty($model)) {
            foreach ($model as $k => $v) {
                $data['list'][$k]['id'] = $v->id;
                $data['list'][$k]['order_no'] = $v->order_no; //订单号
                $data['list'][$k]['order_type'] = $v->order_type; //订单类型
                $data['list'][$k]['trade_no'] = $v->trade_no; //交易流水号
                $data['list'][$k]['pay_channel'] = $v->pay_channel; //支付渠道
                $data['list'][$k]['pay_status'] = $v->pay_status; //支付状态
                $data['list'][$k]['order_status'] = $v->order_status; //订单状态
                $data['list'][$k]['order_paymoney'] = $v->order_paymoney; //订单总金额
                $data['list'][$k]['freight_money'] = $v->freight_money; //运费
                $data['list'][$k]['pay_time'] = $v->pay_time; //支付时间
                $data['list'][$k]['remark'] = $v->remark; //买家备注
                $data['list'][$k]['seller_remark'] = $v->seller_remark; //卖家备注
                $data['list'][$k]['send_time'] = $v->send_time; //发货时间
                $data['list'][$k]['receive_time'] = $v->receive_time; //收货时间
                $data['list'][$k]['cancel_time'] = $v->cancel_time; //取消时间
                $data['list'][$k]['create_time'] = $v->create_time; //创建时间
                $data['list'][$k]['user_name'] = isset($v->user->name) ? $v->user->name : $v->user->account; //买家名称
                $data['list'][$k]['user_phone'] = isset($v->user->account) ? $v->user->account : ''; //买家手机号
                $data['list'][$k]['order_sku'] = $this->getOrderSku($v->id); //获取订单id对应的订单sku信息
                $data['list'][$k]['real_pay'] = $this->getRealPay($v->id); //获取订单id实收金额
                $data['list'][$k]['isSend'] = $this->isSend($v->id); // 判断订单是否有sku可以发货

            }
            $result ['status'] = ERROR_NONE;
            $result['errMsg'] = ''; //错误信息
            $result['data'] = $data;
        } else {
            $result ['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '无此数据'; //错误信息
        }

        return json_encode($result);

    }

    /**
     * 获取商城订单详情
     * $order_id  订单id
     */
    public function getShopOrderDetail($order_id)
    {
        $result = array();
        $model = Order::model()->findByPk($order_id);
        $data = array();
        if (!empty($model)) {
            $data['id'] = $model->id;
            $data['order_no'] = $model->order_no; //订单号
            $data['order_type'] = $model->order_type; //订单类型
            $data['order_status'] = $model->order_status; //订单状态
            $data['pay_status'] = $model->pay_status; //支付状态
            $data['order_paymoney'] = $model->order_paymoney; //订单总金额
            $data['remark'] = $model->remark; //买家备注
            $data['seller_remark'] = $model->seller_remark; //卖家备注
            $data['trade_no'] = $model->trade_no; //交易流水号
            $data['pay_channel'] = $model->pay_channel; //支付渠道
            $data['freight_money'] = $model->freight_money; //运费
            $data['address'] = $model->address; //收货地址
            $data['send_time'] = $model->send_time; //发货时间
            $data['receive_time'] = $model->receive_time; //收货时间
            $data['pay_time'] = $model->pay_time; //支付时间
            $data['complete_time'] = $model->complete_time; //交易完成时间
            $data['create_time'] = $model->create_time; //创建时间
            $data['user_name'] = isset($model->user->name) ? $model->user->name : ''; //买家名称;
            $data['user_phone'] = isset($model->user->account) ? $model->user->account : ''; //买家手机号;
            $data['order_sku'] = $this->getOrderSku($model->id); //获取订单id对应的订单sku信息
            $data['real_pay'] = $this->getRealPay($model->id); //获取订单id实收金额
            $data['isSend'] = $this->isSend($model->id); // 判断订单里是否有sku可以发货

            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        } else {
            $result ['status'] = ERROR_NO_DATA;
            $result['errMsg'] = '无此数据'; //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取订单id对应的订单sku信息
     * $order_id  订单id
     */
    public function getOrderSku($order_id)
    {
        $array = array();
        $model = OrderSku::model()->findAll('order_id=:order_id', array(':order_id' => $order_id));
        if (!empty($model)) {
            foreach ($model as $k => $v) {
                $array[$k]['id'] = $v->id;
                $array[$k]['num'] = $v->num; //数量
                $array[$k]['price'] = $v->price; //价格
                $array[$k]['status'] = $v->status; //订单sku状态
                $array[$k]['product_name'] = $v->product_name; //产品名称
                $array[$k]['if_send'] = $v->if_send; //是否发货
                $array[$k]['sku_name'] = isset($v->shop_product_sku->name) ? $v->shop_product_sku->name : ''; //获取商品属性
                $list = $this->getShopProduct($v->sku_id);
                $array[$k]['product_img'] = $list->img;//获取商品图片
            }
        }
        return $array;
    }

    /**
     * 获取订单id的实收金额
     * $order_id  订单id
     */
    public function getRealPay($order_id)
    {
        $real_pay = 0;
        $model = OrderSku::model()->findAll('order_id=:order_id', array(':order_id' => $order_id));
        if (!empty($model)) {
            foreach ($model as $k => $v) {
                $real_pay = $real_pay + ($v->price) * ($v->num);
            }
        }

        return $real_pay;
    }

    /**
     * 获取商品信息
     */
    public function getShopProduct($shop_product_sku_id)
    {
        $shop_product_sku = ShopProductSku::model()->findByPk($shop_product_sku_id);
        $shop_product = ShopProduct::model()->findByPk($shop_product_sku->product_id);
        return $shop_product;

    }

    /**
     * 订单发货
     * $order_id  订单id
     * $order_sku  sku信息数组 存放订单sku id
     * $express_name  物流公司
     * $express_no  快递单号
     */
    public function orderSend($order_id, $order_sku, $express_name, $express_no)
    {
        $result = array();
        $order = Order::model()->findByPk($order_id);
        $shopExpress = new ShopExpress ();

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $shopExpress->order_id = $order_id;
            $shopExpress->name = isset ($GLOBALS ['LOGISTICS_COMPANY'] [$express_name]) ? $GLOBALS ['LOGISTICS_COMPANY'] [$express_name] : '';
            $shopExpress->express_no = $express_no;
            $shopExpress->send_time = date('Y-m-d H:i:s');
            if ($shopExpress->save()) {
                $express_id = $shopExpress->attributes['id'];
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                throw new Exception('物流数据保存失败');
            }

            for ($i = 0; $i < count($order_sku); $i++) {
                $orderSku = OrderSku::model()->findByPk($order_sku [$i]);
                if ($orderSku->if_send == IF_SEND_NO) {
                    $orderSku->if_send = IF_SEND_YES;
                    if ($orderSku->update()) {
                        $shopExpressSku = new ShopExpressSku();
                        $shopExpressSku->express_id = $express_id;
                        $shopExpressSku->ordersku_id = $order_sku [$i];
                        if ($shopExpressSku->save()) {

                        } else {
                            $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                            throw new Exception('物流sku数据保存失败');
                        }
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL; // 状态码
                        throw new Exception('订单sku数据保存失败');
                    }
                }
            }
            //如果没有订单sku状态为正常发货状态为待发货的订单sku，则将订单状态改为已发货
            $countSku = OrderSku::model()->findAll('order_id=:order_id and flag=:flag and if_send=:if_send and status=:status', array(
                ':order_id' => $order_id,
                ':flag' => FLAG_NO,
                ':if_send' => IF_SEND_NO,
                ':status' => ORDER_SKU_STATUS_NORMAL
            ));
            if (count($countSku) == 0) { //如果已经全部发货     修改订单状态以及发货时间（已付款，已发货）
                $order->order_status = ORDER_STATUS_DELIVER;
                $order->send_time = date('Y-m-d H:i:s');

                if ($order->update()) {

                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    throw new Exception('订单数据保存失败');
                }
            }
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 判断订单是否有sku可以发货
     * $order_id  订单id
     * return true(还有sku可以发货)  false（没有可发货的sku了）
     */
    public function isSend($order_id)
    {
        $model = OrderSku::model()->findAll('order_id=:order_id and flag=:flag and if_send=:if_send and status=:status1 or status=:status2', array(
            ':order_id' => $order_id,
            ':flag' => FLAG_NO,
            ':if_send' => IF_SEND_NO,
            ':status1' => ORDER_SKU_STATUS_NORMAL,
//				':status2'=>ORDER_SKU_STATUS_REFUSE_REFUND
            ':status2' => ORDER_SKU_STATUS_REFUND
        ));
        return count($model);
    }

    /**
     * 订单取消
     * $order_id  订单id
     */
    public function orderCancel($order_id)
    {
        $result = array();
        $model = Order::model()->findByPk($order_id);
        if ($model['pay_status'] == ORDER_STATUS_UNPAID) { //订单付款状态为 待付款
            $model['order_status'] = ORDER_STATUS_CANCEL;
            $model['cancel_time'] = date('Y-m-d H:i:s');
            if ($model->save()) {
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            } else {
                $result ['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败'; //错误信息
            }
        }

        return json_encode($result);
    }

    /**
     *  地址修改
     *  $order_id           订单id
     *  $liveplaceProvince  省份
     *    $liveplaceCity      城市
     *    $liveplaceArea      区域
     *    $postCode           邮政编码
     *    $streetAddress      街道地址
     *    $userName           收件人姓名
     *    $userPhone          手机号码
     */
    public function editAddress($order_id, $liveplaceProvince, $liveplaceCity, $liveplaceArea,
                                $postCode, $streetAddress, $userName, $userPhone)
    {
        $result = array();
        $model = Order::model()->findByPk($order_id);
        $address = $liveplaceProvince . ',' . $liveplaceCity . ',' . $liveplaceArea . ',' . $streetAddress . ',' . $postCode . ',' . $userName . ',' . $userPhone;
        $model->address = $address;

        if ($model->save()) {
            $result ['status'] = ERROR_NONE;
            $result['errMsg'] = ''; //错误信息
        } else {
            $result ['status'] = ERROR_SAVE_FAIL;
            $result['errMsg'] = '数据保存失败'; //错误信息
        }
        return json_encode($result);
    }

    /**
     * 修改卖家备注
     * $order_id       订单id
     * $seller_remark  卖家备注内容
     */
    public function editSellerRemark($order_id, $seller_remark)
    {
        $result = array();
        $model = Order::model()->findByPk($order_id);
        $model->seller_remark = $seller_remark;

        if ($model->save()) {
            $result ['status'] = ERROR_NONE;
            $result['errMsg'] = ''; //错误信息
        } else {
            $result ['status'] = ERROR_SAVE_FAIL;
            $result['errMsg'] = '数据保存失败'; //错误信息
        }
        return json_encode($result);
    }

    /**
     * ajax获取数据
     * $order_id  订单id
     */
    public function getAjaxData($order_id)
    {
        $result = array();
        $model = Order::model()->findByPk($order_id);
        $result['id'] = $model['id'];
        $result['seller_remark'] = $model['seller_remark'];

        return $result;
    }

    /**
     * 发货  ajax获取数据
     */
    public function getAjaxDataForSend($order_id)
    {
        $result = array();
        $order = Order::model()->findByPk($order_id);
        $result['id'] = $order['id'];
        $result['address'] = $this->getAddressForSSQ($order['address']);
        $result['order_sku'] = $this->getOrderSku($order_id);

        return $result;
    }

    /**
     * 获取省市区地址
     */
    public function getAddressForSSQ($address)
    {
        $result = '';
        if (!empty($address)) {
            $arr = explode(',', $address);
            $result = $arr[0] . ' ' . $arr[1] . ' ' . $arr[2] . ' ' . $arr[3];
        }

        return $result;
    }

    /**
     * 导出excel
     * $order_no    订单号
     * $order_type  订单类型
     * $user_name   收货人姓名
     * $user_phone  收货人手机
     * $time        下单时间
     * $order_status  订单状态
     * $pay_channel   支付渠道
     * $pay_status    订单付款状态
     */
    public function exportExcel($merchant_id, $order_no, $order_type, $user_name, $user_phone,
                                $time, $order_status, $pay_channel, $pay_status)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.flag=:flag and t.merchant_id=:merchant_id');
        $criteria->params = array(':flag' => FLAG_NO, ':merchant_id' => $merchant_id);
        $criteria->order = 't.create_time desc';

        //搜索
        if (!empty($order_no)) { //订单号搜索
            $criteria->addCondition('order_no=:order_no');
            $criteria->params[':order_no'] = $order_no;
        }

        if (!empty($order_type)) { //订单类型搜索
            $criteria->addCondition('order_type=:order_type');
            $criteria->params[':order_type'] = $order_type;
        } else {
            $criteria->addCondition('order_type !=:order_type');
            $criteria->params[':order_type'] = ORDER_TYPE_CASHIER;
        }

        if (!empty($time)) { //下单时间搜索
            $Time = array();
            $Time = explode('-', $time);
            $criteria->addBetweenCondition('t.create_time', $Time[0] . ' 00:00:00', $Time[1] . ' 23:59:59');
        }

        if (!empty($order_status)) { //订单状态搜索
            $criteria->addCondition('order_status=:order_status');
            $criteria->params[':order_status'] = $order_status;
        }


        if (!empty($pay_channel)) { //支付渠道搜索
            $criteria->addCondition('pay_channel=:pay_channel');
            $criteria->params[':pay_channel'] = $pay_channel;
        }

        if (!empty($user_name)) { //收货人姓名搜索
            $criteria->addCondition("user.name like '$user_name'");
        }

        if (!empty($user_phone)) { //收货人手机搜索
            $criteria->addCondition("user.account like '$user_phone'");
        }

        if (!empty($pay_status)) { //订单付款状态
            $criteria->addCondition('pay_status=:pay_status');
            $criteria->params[':pay_status'] = $pay_status;
        }

        $model = Order::model()->with('user')->findAll($criteria);
        $list = array();
        foreach ($model as $k => $v) {
            $list[$k]['order_no'] = $v['order_no']; //订单号
            $list[$k]['order_type'] = isset($v['order_type']) ? $GLOBALS['ORDER_TYPE'][$v['order_type']] : ''; //订单类型
            $list[$k]['trade_no'] = $v['trade_no']; //交易流水号
            $list[$k]['pay_channel'] = isset($v['pay_channel']) ? $GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']] : ''; //支付渠道
            $list[$k]['pay_status'] = isset($v['pay_status']) ? $GLOBALS['ORDER_STATUS_PAY'][$v['pay_status']] : ''; //支付状态
            $list[$k]['order_status'] = isset($v['order_status']) ? $GLOBALS['ORDER_STATUS'][$v['order_status']] : ''; //订单状态
            $list[$k]['pay_time'] = $v['pay_time']; //支付时间
            $list[$k]['send_time'] = $v['send_time']; //发货时间
            $list[$k]['receive_time'] = $v['receive_time']; //收货时间
            $list[$k]['address'] = $v['address']; //收货地址
            $list[$k]['remark'] = $v['remark']; //买家备注
            $list[$k]['seller_remark'] = $v['seller_remark']; //卖家备注
            $list[$k]['order_paymoney'] = $v['order_paymoney']; //订单总金额
            $list[$k]['order_sku'] = $this->getOrderSku($v['id']); //订单sku

            $list[$k]['nick_name'] = $v['user']['nickname'];//微信支付宝昵称

            $list[$k]['card_d'] = $v['discount_money'];//卡券折扣
            $list[$k]['real_pay'] = $this->getRealPay($v->id);

        }

        $this->getExcel($list);
    }

    /**
     * 获取excel
     */
    public function getExcel($model)
    {
        include 'PHPExcel/Reader/Excel2007.php';
        include 'PHPExcel/Reader/Excel5.php';
        include 'PHPExcel/IOFactory.php';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单号')
            ->setCellValue('B1', '订单类型')
            ->setCellValue('C1', '订单状态')
            ->setCellValue('D1', '买家会员名')
            ->setCellValue('E1', '订单总金额(元)')
            ->setCellValue('F1', '买家实付金额(元)')
            ->setCellValue('G1', '卡券抵扣(元)')
            ->setCellValue('H1', '支付渠道')
            ->setCellValue('I1', '交易流水号')
            ->setCellValue('J1', '支付时间')
            ->setCellValue('K1', '产品名称')
            ->setCellValue('L1', '产品数量')
            ->setCellValue('M1', '产品单价(元)')
            ->setCellValue('N1', '收货地址');

        //设置列宽
        $objActSheet = $objPHPExcel->getActiveSheet();

        //设置sheet名称
        $objActSheet->setTitle('订单列表');


        //数据添加
        $i = 2;//var_dump($model);exit;
        foreach ($model as $k => $v) {
            $t = $i;
            $length = count($v['order_sku']); //订单对应sku的数量

            //算出合并单元格的截止位置
            $l = $i + $length - 1;
            //合并单元格
            $objActSheet->mergeCells("A$i:A$l");
            $objActSheet->mergeCells("B$i:B$l");
            $objActSheet->mergeCells("C$i:C$l");
            $objActSheet->mergeCells("D$i:D$l");
            $objActSheet->mergeCells("E$i:E$l");
            $objActSheet->mergeCells("F$i:F$l");
            $objActSheet->mergeCells("G$i:G$l");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A' . $i, $v['order_no'], PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('B' . $i, $v['order_type'])
                ->setCellValue('C' . $i, $v['order_status'])
                ->setCellValue('D' . $i, $v['nick_name'])
                ->setCellValue('E' . $i, $v['order_paymoney'])
                ->setCellValue('F' . $i, $v['real_pay'])
                ->setCellValue('G' . $i, $v['card_d'])
                ->setCellValue('H' . $i, $v['pay_channel'])
                ->setCellValue('I' . $i, $v['trade_no'])
                ->setCellValue('J' . $i, $v['pay_time'])
                ->setCellValue('N' . $i, $v['address']);

            for ($j = 0; $j < $length; $j++) { //遍历sku信息
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('K' . $t, $v['order_sku'][$j]['product_name'])
                    ->setCellValue('L' . $t, $v['order_sku'][$j]['num'])
                    ->setCellValue('M' . $t, $v['order_sku'][$j]['price']);
                $t++;
            }

            $i = $i + $length;
        }

        $filename = date('YmdHis');//定义文件名

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        //$objWriter->save(str_replace('.php', '.xls', __FILE__));
        $this->outPut($filename);
        $objWriter->save("php://output");

    }

    /**
     * 到浏览器  浏览器下载excel
     */
    public function outPut($filename)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename={$filename}.xls");
        header("Content-Transfer-Encoding:binary");
    }

    /**
     * 获取订单为待发货的数量
     */
    public function getWaitCount($merchant_id)
    {
        /*$model = Order::model()->findAll('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id', array(
            ':flag' => FLAG_NO,
            ':order_status' => ORDER_STATUS_WAITFORDELIVER,
            ':pay_status' => ORDER_STATUS_PAID,
            ':merchant_id' => $merchant_id
        ));*/

        $criteria = new CDbCriteria();
        $criteria->addCondition('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->params[':order_status'] = ORDER_STATUS_WAITFORDELIVER;
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
        $criteria->params[':merchant_id'] = $merchant_id;
        $criteria->addCondition("order_no like 'SC%' ");
        $model = Order::model()->findAll($criteria);

        return count($model);
    }

    /**
     * 获取订单为待付款的数量
     */
    public function getNoPayCount($merchant_id)
    {
        /*$model = Order::model()->findAll('flag=:flag and pay_status=:pay_status and order_status!=:order_status and merchant_id =:merchant_id', array(
            ':flag' => FLAG_NO,
            ':order_status' => ORDER_STATUS_CANCEL,
            ':pay_status' => ORDER_STATUS_UNPAID,
            ':merchant_id' => $merchant_id
        ));*/

        $criteria = new CDbCriteria();
        $criteria->addCondition('flag=:flag and order_status!=:order_status and pay_status=:pay_status and merchant_id =:merchant_id');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->params[':order_status'] = ORDER_STATUS_CANCEL;
        $criteria->params[':pay_status'] = ORDER_STATUS_UNPAID;
        $criteria->params[':merchant_id'] = $merchant_id;
        $criteria->addCondition("order_no like 'SC%' ");
        $model = Order::model()->findAll($criteria);

        return count($model);
    }

    /*
     * 获取已发货的数量
     */
    public function getDeliverCount($merchant_id)
    {
        /*$model = Order::model()->findAll('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id', array(
            ':flag' => FLAG_NO,
            ':order_status' => ORDER_STATUS_DELIVER,
            ':pay_status' => ORDER_STATUS_PAID,
            ':merchant_id' => $merchant_id
        ));*/

        $criteria = new CDbCriteria();
        $criteria->addCondition('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->params[':order_status'] = ORDER_STATUS_DELIVER;
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
        $criteria->params[':merchant_id'] = $merchant_id;
        $criteria->addCondition("order_no like 'SC%' ");
        $model = Order::model()->findAll($criteria);

        return count($model);
    }

    /*
     * 获取已收货的数量
     */
    public function getAcceptCount($merchant_id)
    {
        /*$model = Order::model()->findAll('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id', array(
            ':flag' => FLAG_NO,
            ':order_status' => ORDER_STATUS_ACCEPT,
            ':pay_status' => ORDER_STATUS_PAID,
            ':merchant_id' => $merchant_id
        ));*/

        $criteria = new CDbCriteria();
        $criteria->addCondition('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->params[':order_status'] = ORDER_STATUS_ACCEPT;
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
        $criteria->params[':merchant_id'] = $merchant_id;
        $criteria->addCondition("order_no like 'SC%' ");
        $model = Order::model()->findAll($criteria);

        return count($model);
    }

    /*
     * 获取已取消的数量
     */
    public function getCancelCount($merchant_id)
    {
        /*$model = Order::model()->findAll('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id', array(
            ':flag' => FLAG_NO,
            ':order_status' => ORDER_STATUS_CANCEL,
            ':pay_status' => ORDER_STATUS_PAID,
            ':merchant_id' => $merchant_id
        ));*/

        $criteria = new CDbCriteria();
        $criteria->addCondition('flag=:flag and order_status=:order_status and pay_status=:pay_status and merchant_id =:merchant_id');
        $criteria->params[':flag'] = FLAG_NO;
        $criteria->params[':order_status'] = ORDER_STATUS_CANCEL;
        $criteria->params[':pay_status'] = ORDER_STATUS_PAID;
        $criteria->params[':merchant_id'] = $merchant_id;
        $criteria->addCondition("order_no like 'SC%' ");
        $model = Order::model()->findAll($criteria);

        return count($model);
    }


    /*
     * 商城下订单
     * $user_id 用户id 必填
     * $sku shuid数组 必填
     * $num sku对应数量 必填
     * $address 收货地址id 必填
     * $is_cart 是否购物车 必填
     * $remark 用户备注 非必填
     * */
    public function createOrder($user_id, $sku, $num, $address, $is_cart, $remark = '', $merchant_id = '')
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {

            //计算订单金额
            $re = json_decode($this->countOrderMoney($sku, $num, $is_cart));
            if ($re->status == ERROR_NONE) {
                $orderMoney = $re->data;
                $type = $re->type;
            } else {
                $result['status'] = $re->status;
                throw new Exception($re->errMsg);
            }

            //计算运费
            $res_freight = json_decode($this->countFreight($address, $sku, $num));
            if ($res_freight->status == ERROR_NONE) {
                $freightMoney = $res_freight->data;
            } else {
                $result['status'] = $res_freight->status;
                throw new Exception($res_freight->errMsg);
            }

            //创建订单（支付状态：待付款，订单状态：正常）
            $order = new Order();
            $order->user_id = $user_id;
            $order->order_no = $this->getOrderNo();//生成订单号
            $order->order_type = $type;//订单类型 实物、虚拟
            $order->pay_status = ORDER_STATUS_UNPAID;//待付款
            $order->order_status = ORDER_STATUS_NORMAL;
            $order->merchant_id = $merchant_id;
// 			if($type == ORDER_TYPE_OBJECT){
// 				//实物类订单 订单状态为待发货
// 				$order -> order_status = ORDER_STATUS_WAITFORDELIVER;
// 			}elseif ($type == ORDER_TYPE_VIRTUAL){
// 				//虚拟类订单 订单状态为待生成
// 				$order -> order_status = ORDER_STATUS_WAITFORCREATE;
// 			}
            $adds = UserAddress::model()->findByPk($address);
            $arr = explode(',', $adds->address);
            $province = ShopCity::model()->find('code =:code', array(
                ':code' => $arr[0]
            ));
            $city = ShopCity::model()->find('code =:code', array(
                ':code' => $arr[1]
            ));
            $area = ShopCity::model()->find('code =:code', array(
                ':code' => $arr[2]
            ));
            //订单收货地址
            $order->address = $province->name . ',' . $city->name . ',' . $area->name . ',' . $arr[3] . ',' . $adds->code . ',' . $adds->name . ',' . $adds->tel;
            $order->remark = $remark;//用户备注
            $order->create_time = new CDbExpression('now()');//创建时间
            $order->order_paymoney = $orderMoney + $freightMoney;
            $order->freight_money = $freightMoney;
            if ($order->save()) {
                //创建ordersku（发货状态：待发货，订单sku状态：正常）
                foreach ($sku as $k => $v) {
                    $product_sku = ShopProductSku::model()->findByPk($v);
                    //检查库存
                    if ($product_sku->num - $product_sku->sold_num < $num[$k]) {
                        $result['status'] = ERROR_EXCEPTION;
                        throw new Exception($product_sku->name . '库存不足');
                    }
                    $ordersku = new OrderSku();
                    $ordersku->order_id = $order->id;//订单id
                    $ordersku->sku_id = $v;//skuid
                    $ordersku->product_name = $product_sku->product->name;//商品名称
                    $ordersku->num = $num[$k];//数量
                    $ordersku->price = $product_sku->price; //价格
                    $ordersku->create_time = new CDbExpression('now()');//创建时间
                    $ordersku->status = ORDER_SKU_STATUS_NORMAL;//状态正常

                    $ordersku->if_send = IF_SEND_NO;

                    if ($ordersku->save()) {

                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('订单sku保存失败');
                    }
                }

                //订单创建成功，减库存
                $change_re = json_decode($this->changeStockNum($order->order_no, '', 1));
                if ($change_re->status == ERROR_NONE) {

                } else {
                    $result['status'] = $change_re->status;
                    throw new Exception($change_re->errMsg);
                }

                $transaction->commit(); //数据提交
                $result['status'] = ERROR_NONE;
                $result['data'] = $order->id;

            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                throw new Exception('订单保存失败');
            }
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 订单取消
     * $order_id 订单id
     */
    public function cancleOrder($order_id)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $model = Order::model()->findByPk($order_id);
            $model->order_status = ORDER_STATUS_CANCEL;
            if ($model->update()) {
                //库存增加
                $re = json_decode($this->changeStockNum('', $order_id, 2));
                if ($re->status == ERROR_NONE) {
                    //修改订单sku状态
                    $order_sku = OrderSku::model()->findAll('order_id =:order_id and flag =:flag', array(
                        ':order_id' => $model->id,
                        ':flag' => FLAG_NO
                    ));
                    foreach ($order_sku as $k => $v) {
                        $v->status = ORDER_SKU_STATUS_CANCEL;
                        if ($v->update()) {

                        } else {
                            $result ['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('订单sku状态修改失败');
                        }
                    }

                    $transaction->commit(); //数据提交
                    $result ['status'] = ERROR_NONE;
                    $result ['errMsg'] = ''; // 错误信息
                } else {
                    $result ['status'] = $re->status;
                    throw new Exception($re->errMsg);
                }
            } else {
                $result ['status'] = ERROR_SAVE_FAIL;
                throw new Exception('数据保存失败');
            }

        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
     * 订单支付成功，修改库存
    * $order_no 订单号
    * $order_id 订单id
    * $type 操作类型  1 减库存 2 加库存
    * */
    private function changeStockNum($order_no, $order_id, $type)
    {
        $result = array();
        try {
            if (!empty($order_no)) {
                //根据订单号找到订单
                $order = Order::model()->find('order_no =:order_no and flag =:flag', array(
                    ':order_no' => $order_no,
                    ':flag' => FLAG_NO
                ));
            } elseif (!empty($order_id)) {
                $order = Order::model()->findByPk($order_id);
            }

            if (!empty($order)) {
                //查询所有订单sku
                $order_sku = OrderSku::model()->findAll('order_id =:order_id and flag =:flag', array(
                    ':order_id' => $order->id,
                    ':flag' => FLAG_NO
                ));
                if (!empty($order_sku)) {
                    foreach ($order_sku as $k => $v) {
                        $sku = ShopProductSku::model()->findByPk($v->sku_id);
                        if (!empty($sku)) {
                            //修改sku的数量
                            if ($type == 1) {
                                $sku->sold_num = $sku->sold_num + $v->num;
                            } elseif ($type == 2) {
                                $sku->sold_num = $sku->sold_num - $v->num;
                            }

                            if ($sku->update()) {

                            } else {
                                $result['status'] = ERROR_SAVE_FAIL;
                                throw new Exception('产品sku修改失败');
                            }
                        } else {
                            $result['status'] = ERROR_NO_DATA;
                            throw new Exception('该产品sku不存在');
                        }
                    }
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    throw new Exception('该订单sku不存在');
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该订单不存在');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    //计算订单金额
    private function countOrderMoney($sku, $num, $is_cart)
    {
        $result = array();
        try {
            $count = 0;
            $type = '';
            foreach ($sku as $k => $v) {

                //如果是购物车，就删除购物车信息
                if ($is_cart == IS_CART_YES) {
                    $cart = ShopCart::model()->findByPk($k);
                    if ($cart) {
                        $cart->flag = FLAG_YES;
                        if ($cart->update()) {

                        } else {

                        }
                    } else {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该购物车信息不存在');
                    }
                }
                $sku = ShopProductSku::model()->findByPk($v);
                if ($sku->product->type == SHOP_PRODUCT_TYPE_OBJECT) {
                    $type = ORDER_TYPE_OBJECT;
                } elseif ($sku->product->type == SHOP_PRODUCT_TYPE_VIRTAL) {
                    $type = ORDER_TYPE_VIRTUAL;
                }

                //验证商品状态
                $product = ShopProduct::model()->findByPk($sku->product_id);
                if ($product->flag == FLAG_YES) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('该商品已被删除');
                }

                if ($sku) {
                    $count += $num[$k] * $sku->price;
                } else {
                    $result['status'] = ERROR_NO_DATA;
                    throw new Exception('该sku不存在');
                }

            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $count;
            $result['type'] = $type;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //计算运费
    public function countFreight($address, $sku, $num)
    {
        $result = array();
        try {
            $freight = 0;
            $ads = UserAddress::model()->findByPk($address);
            foreach ($sku as $k => $v) {
                $sku = ShopProductSku::model()->findByPk($v);
                $subfreight = '';
                if ($sku->product->freight_type == SHOP_FREIGHT_TYPE_UNITE) {
                    $freight = $sku->product->freight_money;
                } elseif ($sku->product->freight_type == SHOP_FREIGHT_TYPE_MODEL) {
                    $arr = explode(',', $ads->address);
                    //获得区的编号
                    $area = $arr[0];
                    $criteria = new CDbCriteria;
                    $criteria->addCondition("freight_id = :freight_id");
                    $criteria->params[':freight_id'] = $sku->product->freight_id;
                    $criteria->addCondition("area like '%$area%'");
                    $subfreight = ShopSubfreight::model()->find($criteria);
                    if (empty($subfreight)) {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该地区不支持配送');
                    }
                }
                break;
            }

            if (!empty($subfreight)) {
                $count_num = 0;
                foreach ($num as $k => $v) {
                    $count_num += $v;
                }
                //满足起始件数
                if ($count_num >= $subfreight->first_num) {
                    $freight += $subfreight->first_freight;
                    if ($subfreight->second_num > 0) {
                        $freight += floor(($count_num - $subfreight->first_num) / $subfreight->second_num) * $subfreight->second_freight;
                    }
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $freight;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 生成商城订单编号
     */
    public function getOrderNo()
    {
        $Code = 'SC' . date('Ymd') . mt_rand(000001, 999999);
        $scOrder = Order::model()->find('order_no = :order_no', array(':order_no' => $Code));
        if (!empty($scOrder)) {
            while ($Code == $scOrder->order_no) {
                $Code = 'SC' . date('Ymd') . mt_rand(000001, 999999);
                $scOrder = Order::model()->find('order_no = :order_no', array(':order_no' => $Code));
            }
        }
        return $Code;
    }


    /*
     * 获取订单信息
     * $order_id 订单id 必填
     * */
    public function getOrderInfo($order_id = '', $order_no = '')
    {
        $result = array();
        try {
            if (!empty($order_id)) {
                $order = Order::model()->findByPk($order_id);
            } else {
                $order = Order::model()->find('order_no =:order_no and flag =:flag', array(
                    ':order_no' => $order_no,
                    ':flag' => FLAG_NO
                ));
            }
            if ($order) {
                $data = array();
                $data['id'] = $order->id;
                $data['order_no'] = $order->order_no;
                $data['order_paymoney'] = $order->order_paymoney;
                $data['freight_money'] = $order->freight_money;

                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('该订单不存在');
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

}