<?php
include_once (dirname ( __FILE__ ) . '/../mainClass.php');

/**
 * 商城订单维权类
 */

class ActivistC extends mainClass{
	
	public $page = null;
	
	/**
	 * 获取维权订单列表
	 */
	public function getActivistList()
	{
		$result = array ();
		$criteria = new CDbCriteria();

//        $criteria->with = 'order_sku';
        $criteria->join = 'LEFT JOIN wq_order_sku ON wq_order_sku.order_id=t.id';
//        $criteria -> addCondition('wq_order_sku.status = :status');
//        $criteria -> params[':status'] = ORDER_SKU_STATUS_REFUNDSUCCESS;
        $criteria->addInCondition('wq_order_sku.status',array(ORDER_SKU_STATUS_REFUND,ORDER_SKU_STATUS_REFUNDSUCCESS));
//        $criteria->params[':status']=ORDER_SKU_STATUS_REFUNDSUCCESS;
        $criteria -> addCondition('t.flag = :flag');
        $criteria -> params[':flag'] = FLAG_NO;
        $criteria -> order = 't.create_time desc';
        $criteria->distinct='t.id';
		//分页
		$pages = new CPagination(Order::model()->count($criteria));
		$pages->pageSize = Yii::app()->params['perPage'];
		$pages->applyLimit($criteria);
		$this->page = $pages;
		
		$model = Order::model()->findAll($criteria);
		
		$data = array();
        $data['list']=null;
		if(!empty($model)){
			foreach ($model as $k=>$v){
                    $order_sku = $this->getOrderSku($v->id); //获取订单id对应的订单sku信息
                    $realpay = $this->getRealPay($v->id); //获取订单id实收金额
                    if(!empty($order_sku)) {
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
                        $data['list'][$k]['user_name'] = isset($v->user->name) ? $v->user->name : ''; //买家名称
                        $data['list'][$k]['user_phone'] = isset($v->user->account) ? $v->user->account : ''; //买家手机号
                        $data['list'][$k]['order_sku'] = $order_sku;
                        $data['list'][$k]['real_pay'] = $realpay;
                    }
			}
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
			$result['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		
		return json_encode($result);
	}
	
	/**
	 * 退款订单sku详情
	 * $order_sku_id  订单sku Id
	 * $order_status  订单状态
	 */
	public function refundOrderSkuDetail($order_id,$order_sku_id,$order_status)
	{
		$result = array ();
		$order_sku = OrderSku::model()->findByPk($order_sku_id);
		$data = array();
	    if(!empty($order_sku)){
	    	$data['id'] = $order_sku -> id;
            $model_refund=RefundRecord::model()->findAll('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag',array(
                ':order_id'=>$order_id,
                ':order_sku_id'=>$order_sku -> id,
                ':flag'=>FLAG_NO
            ));
            $maxtime=null;
            $new_id=null;
            if(!empty($model_refund))
            {
                foreach($model_refund as $k=>$v)
                {
                    if(empty($maxtime))
                    {
                        $maxtime=$v['create_time'];
                        $new_id=$v['id'];
                    }
                    else if($maxtime<$v['create_time'])
                    {
                        $maxtime=$v['create_time'];
                        $new_id=$v['id'];
                    }
                }
            }
            if(!empty($new_id))
            {
                $model_new_refund=RefundRecord::model()->findByPk($new_id);
            }
	    	$data['status'] = $model_new_refund -> status; //处理方式(申请退款无需退货/申请退款需退货)
            //按时间排序
            $criteria=new CDbCriteria();
            $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
            $criteria->params=array(
                ':order_id'=>$order_id,
                ':order_sku_id'=>$order_sku_id,
                ':flag'=>FLAG_NO
            );
            $criteria->order='create_time desc';
            $model=RefundRecord::model()->findAll($criteria);
            if(!empty($model))
            {
                foreach($model as $key=>$value)
                {
                    //所有的拒绝退款记录
                    $data['refund'][$key]['refund_id']=$value->id;
                    $data['refund'][$key]['refund_money']=$value->refund_money;//退款金额
                    $data['refund'][$key]['refund_reason'] = $value -> refund_reason; //退款原因
                    $data['refund'][$key]['refund_tel'] = $value -> refund_tel; //退款联系方式
                    $data['refund'][$key]['refund_remark'] = $value -> refund_remark; //退款备注
//                $data['refund_address'] = $model -> refund_address; //退款地址
                    $data['refund'][$key]['refund_hope'] = $value -> if_return; //期望结果
                    $data['refund'][$key]['refund_address']=$value->refund_address;
                    $data['refund'][$key]['if_return']=$value->if_return;
                    $data['refund'][$key]['status']=$value->status;
                    $data['refund'][$key]['refund_tel']=$value->refund_tel;
                    $data['refund'][$key]['user_express']=$value->user_express;
                    $data['refund'][$key]['user_express_no']=$value->user_express_no;
                    $data['refund'][$key]['user_tel']=$value->user_tel;
                    $data['refund'][$key]['user_remark']=$value->user_remark;
                    $data['refund'][$key]['create_time']=$value->create_time;
                    $data['refund'][$key]['apply_refund_time']=$value->apply_refund_time;
                    $data['refund'][$key]['agree_refund_time']=$value->agree_refund_time;
                    $data['refund'][$key]['refuse_refund_remark']=$value->refuse_refund_remark;
                }
            }
	    	$data['product_name'] = $order_sku -> product_name; //产品名称
	    	$data['sku_price'] = $order_sku -> num * $order_sku -> price; //sku总价
	    	$data['if_send'] = $order_sku -> if_send; //是否发货 1未发货 2已发货
	    	$data['order_info'] = $this->getOrderInfo($order_sku -> order_id); //获取sku对应的订单详情
	    	$data['shopProductSku_name'] = isset($order_sku -> shop_product_sku -> name)?$order_sku -> shop_product_sku -> name:'';
	    	$sku_name=explode(',',$data['shopProductSku_name']);
            $data['color']=$sku_name[0];
            $data['size']=$sku_name[1];
            $data['shopProduct_img'] = $this->getShopProduct($order_sku -> sku_id);
	    	if($order_sku -> if_send == IF_SEND_YES){//已经发货才有物流
	    	  $data['skuExpress'] = $this -> getSkuExpress($order_sku -> id); //订单sku物流信息
	    	}
	    	
	    	$result['status'] = ERROR_NONE;
	    	$result['data'] = $data;
	    }else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		return CJSON::encode($result);
	}
	
	/**
	 * 获取sku对应的订单详情
	 * $order_id   订单id
	 */
	public function getOrderInfo($order_id)
	{
		$order = Order::model()->findByPk($order_id);
		return $order;
	}
	
	/**
	 * 订单sku物流信息
	 */
	public function getSkuExpress($order_sku_id)
	{
		$shop_express_sku = ShopExpressSku::model()->find('ordersku_id=:ordersku_id and flag=:flag'
				                                          ,array(':ordersku_id'=>$order_sku_id,':flag'=>FLAG_NO));
		$shop_express = ShopExpress::model()->findByPk($shop_express_sku -> express_id);
		return $shop_express;
	}
	
	
	/**
	 * 商家同意退款
	 * $order_sku_id  订单sku Id
	 * $order_status  订单状态
	 */
	public function agreeRefund($order_id,$order_sku_id,$order_status)
	{
		$result = array ();
        try {
            $order_sku = OrderSku::model()->findByPk($order_sku_id);
            $order_sku->status = ORDER_SKU_STATUS_REFUNDSUCCESS;
            $order_id = $order_sku->order_id;


            //按时间排序
            $criteria = new CDbCriteria();
            $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
            $criteria->params = array(
                ':order_id' => $order_id,
                ':order_sku_id' => $order_sku_id,
                ':flag' => FLAG_NO
            );
            $criteria->order = 'create_time desc';
            $model = RefundRecord::model()->find($criteria);

            $date = date('Y-m-d H:i:s', time());
            $refundRecord = new RefundRecord();
            $refundRecord->order_id = $model->order_id;
            $refundRecord->operator_id = $model->operator_id;
            $refundRecord->operator_admin_id = $model->operator_admin_id;
            $refundRecord->refund_money = $model->refund_money;
            $refundRecord->refund_channel = $model->refund_channel;
            $refundRecord->refund_no = $model->refund_no;
            $refundRecord->refund_time = $model->refund_time;
            $refundRecord->create_time = $date;
            $refundRecord->refund_order_no = $model->refund_order_no;
            $refundRecord->type = $model->type;
            $refundRecord->status = ORDER_REFUND_STATUS_REFUND_SUCCESS;//拒绝退款
            $refundRecord->terminal_type = $model->terminal_type;
            $refundRecord->terminal_id = $model->terminal_id;
            $refundRecord->refund_reason = $model->refund_reason;
            $refundRecord->refund_tel = $model->refund_tel;
            $refundRecord->refund_remark = $model->refund_remark;
            $refundRecord->refund_img = $model->refund_img;
            $refundRecord->order_sku_id = $model->order_sku_id;
            $refundRecord->if_return = $model->if_return;
            $refundRecord->refund_address = $model->refund_address;
            $refundRecord->user_express = $model->user_express;
            $refundRecord->user_express_no = $model->user_express_no;
            $refundRecord->user_remark = $model->user_remark;
            $refundRecord->user_tel = $model->user_tel;
            $refundRecord->refuse_refund_remark = $model->refuse_refund_remark;
            $refundRecord->apply_refund_time = $model->apply_refund_time;
            $refundRecord->agree_refund_time = $date;


            if ($order_sku->save() && $refundRecord->save()) {
                //判断所有SKU，都已经退款完成，总的订单状态改成已退款(ORDER_STATUS_REFUND)
                $sku_model = OrderSku::model()->findAll('order_id=:order_id and flag=:flag', array(
                    ':order_id' => $order_id,
                    ':flag' => FLAG_NO
                ));
                $flag = true;
                if (!empty($sku_model)) {
                    foreach ($sku_model as $key => $value) {
                        if ($value['status'] != ORDER_SKU_STATUS_REFUNDSUCCESS) {
                            $flag = false;
                        }
                    }
                }
                if ($flag) {
                    $order = Order::model()->findByPk($order_id);
                    $order->order_status = ORDER_STATUS_REFUND;
                    if ($order->save()) {
                        $result ['status'] = ERROR_NONE;
                        $result['errMsg'] = ''; //错误信息
                    } else {
                        $result ['status'] = ERROR_SAVE_FAIL;
                        $result['errMsg'] = '数据保存失败'; //错误信息
                        throw new Exception('数据保存失败');
                    }
                }
                $result ['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息

            } else {
                $result ['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据'; //错误信息
                throw new Exception('无此数据');
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
		return json_encode($result);
	}
	
	/**
	 * 商家拒绝退款
	 * $order_sku_id  订单sku Id
     * $refuse_refund_remark 拒绝理由
	 */
	public function refuseRefund($order_id,$order_sku_id,$refuse_refund_remark)
	{
        $transaction=Yii::app()->db->beginTransaction();
		$result = array ();
		$flag=true;
		$order_sku = OrderSku::model()->findByPk($order_sku_id);
		$order_sku -> status = ORDER_SKU_STATUS_NORMAL;
        //按时间排序
        $criteria=new CDbCriteria();
        $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
        $criteria->params=array(
            ':order_id'=>$order_id,
            ':order_sku_id'=>$order_sku_id,
            ':flag'=>FLAG_NO
        );
        $criteria->order='create_time desc';
        $model=RefundRecord::model()->find($criteria);
        $model_new_refund=RefundRecord::model()->findByPk($model->id);
        //如果不是第一次拒绝退款，产生一条新的退款记录
        $refundRecord=new RefundRecord();
        $refundRecord->order_id=$model_new_refund->order_id;
        $refundRecord->operator_id=$model_new_refund->operator_id;
        $refundRecord->operator_admin_id=$model_new_refund->operator_admin_id;
        $refundRecord->refund_money=$model_new_refund->refund_money;
        $refundRecord->refund_channel=$model_new_refund->refund_channel;
        $refundRecord->refund_no=$model_new_refund->refund_no;
        $refundRecord->refund_time=$model_new_refund->refund_time;
        $refundRecord->create_time=date('Y-m-d H:i:s', time());
        $refundRecord->refund_order_no=$model_new_refund->refund_order_no;
        $refundRecord->type=$model_new_refund->type;
        $refundRecord->status=ORDER_REFUND_STATUS_REFUSE_REFUND;//拒绝退款
        $refundRecord->terminal_type=$model_new_refund->terminal_type;
        $refundRecord->terminal_id=$model_new_refund->terminal_id;
        $refundRecord->refund_reason=$model_new_refund->refund_reason;
        $refundRecord->refund_tel=$model_new_refund->refund_tel;
        $refundRecord->refund_remark=$model_new_refund->refund_remark;
        $refundRecord->refund_img=$model_new_refund->refund_img;
        $refundRecord->order_sku_id=$model_new_refund->order_sku_id;
        $refundRecord->if_return=$model_new_refund->if_return;
        $refundRecord->refund_address=$model_new_refund->refund_address;
        $refundRecord->user_express=$model_new_refund->user_express;
        $refundRecord->user_express_no=$model_new_refund->user_express_no;
        $refundRecord->user_remark=$model_new_refund->user_remark;
        $refundRecord->user_tel=$model_new_refund->user_tel;
        $refundRecord->refuse_refund_remark=$refuse_refund_remark;
        if($refundRecord->save())
        {

        }else
        {
            $flag=false;
        }

        if($order_sku->save())
        {

        }
        else
        {
            $flag=false;
        }
		if($flag){
            $transaction->commit();
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
            $transaction->rollback();
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 商家填写退款地址
	 */
	public function addRefundAddress($orderid,$order_sku_id,$refund_id,$refund_address,$refund_status,$refund_money)
	{
		$result = array ();
        //按时间排序
        $criteria=new CDbCriteria();
        $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
        $criteria->params=array(
            ':order_id'=>$orderid,
            ':order_sku_id'=>$order_sku_id,
            ':flag'=>FLAG_NO
        );
        $criteria->order='create_time desc';
        $model=RefundRecord::model()->find($criteria);
        $model_new_refund=RefundRecord::model()->findByPk($model->id);
        //如果不是第一次拒绝退款，产生一条新的退款记录
        $refundRecord=new RefundRecord();
        $refundRecord->order_id=$model_new_refund->order_id;
        $refundRecord->operator_id=$model_new_refund->operator_id;
        $refundRecord->operator_admin_id=$model_new_refund->operator_admin_id;
        $refundRecord->refund_channel=$model_new_refund->refund_channel;
        $refundRecord->refund_no=$model_new_refund->refund_no;
        $refundRecord->refund_time=$model_new_refund->refund_time;
        $refundRecord->create_time=date('Y-m-d H:i:s', time());
        $refundRecord->refund_order_no=$model_new_refund->refund_order_no;
        $refundRecord->type=$model_new_refund->type;
        $refundRecord->terminal_type=$model_new_refund->terminal_type;
        $refundRecord->terminal_id=$model_new_refund->terminal_id;
        $refundRecord->refund_reason=$model_new_refund->refund_reason;
        $refundRecord->refund_tel=$model_new_refund->refund_tel;
        $refundRecord->refund_remark=$model_new_refund->refund_remark;
        $refundRecord->refund_img=$model_new_refund->refund_img;
        $refundRecord->order_sku_id=$model_new_refund->order_sku_id;
        $refundRecord->if_return=$model_new_refund->if_return;
        $refundRecord->user_express=$model_new_refund->user_express;
        $refundRecord->user_express_no=$model_new_refund->user_express_no;
        $refundRecord->user_remark=$model_new_refund->user_remark;
        $refundRecord->user_tel=$model_new_refund->user_tel;
        $refundRecord->refuse_refund_remark=$model_new_refund->refuse_refund_remark;
        $refundRecord->refund_money=$refund_money;
        $refundRecord->refund_address=$refund_address;
        $refundRecord->apply_refund_time=$model_new_refund->apply_refund_time;
        if($model_new_refund->if_return==IF_RETURN_NO)
        {
            $refundRecord->status=ORDER_REFUND_STATUS_AGREE_NORETURN;
        }
        else if($model_new_refund->if_return==IF_RETURN_YES)
        {
            $refundRecord->status=ORDER_REFUND_STATUS_AGREE_RETURN;
        }
		if($refundRecord -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 获取订单id对应的订单sku信息
	 */
	public function getOrderSku($order_id)
	{
		$array = array();
		$model = OrderSku::model()->findAll('order_id=:order_id and flag=:flag'
				                             ,array(':order_id'=>$order_id,':flag'=>FLAG_NO));
		if(!empty($model)){
			foreach ($model as $k=>$v){
                if($v->status==ORDER_SKU_STATUS_REFUND||$v->status==ORDER_SKU_STATUS_REFUNDSUCCESS) {
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
		}
		return $array;
	}
	
	/**
	 * 获取订单id的实收金额
	 */
	public function getRealPay($order_id)
	{
		$real_pay = 0;
		$model = OrderSku::model()->findAll('order_id=:order_id',array(':order_id'=>$order_id));
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$real_pay = $real_pay + ($v->price)*($v->num);
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
		$shop_product = ShopProduct::model()->findByPk($shop_product_sku -> product_id);
		return $shop_product;
	
	}

    /**
     * 获取退款详情
     */
    public function getRefundDetail($merchant_id,$order_id)
    {
        $result=array();
        try{
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria=new CDbCriteria();
            $criteria->addCondition('flag=:flag and
            id=:id and merchant_id=:merchant_id
            ');
            $criteria->params=array(
                ':flag'=>FLAG_NO,
                ':id'=>$order_id,
                ':merchant_id'=>$merchant_id,
            );
            $model=Order::model()->findAll($criteria);
            if(!empty($model))
            {
                foreach($model as $key=>$value)
                {

                }
            }

            $result['status'] = ERROR_NONE;
            $result['errMsg']='';
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 同意退款
     */
    public function AgreeTake($order_id,$order_sku_id,$order_status)
    {
        $result=array();
        try{
            $ordersku=OrderSku::model()->findByPk($order_sku_id);
            if(!empty($ordersku))
            {
                $ordersku->status=ORDER_SKU_STATUS_REFUNDSUCCESS;
                //按时间排序
                $criteria=new CDbCriteria();
                $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
                $criteria->params=array(
                    ':order_id'=>$order_id,
                    ':order_sku_id'=>$order_sku_id,
                    ':flag'=>FLAG_NO
                );
                $criteria->order='create_time desc';
                $model=RefundRecord::model()->find($criteria);
                $model_new_refund=RefundRecord::model()->findByPk($model->id);
                $date=date('Y-m-d H:i:s', time());
                $refundRecord=new RefundRecord();
                $refundRecord->order_id=$model_new_refund->order_id;
                $refundRecord->operator_id=$model_new_refund->operator_id;
                $refundRecord->operator_admin_id=$model_new_refund->operator_admin_id;
                $refundRecord->refund_channel=$model_new_refund->refund_channel;
                $refundRecord->refund_no=$model_new_refund->refund_no;
                $refundRecord->refund_time=$model_new_refund->refund_time;
                $refundRecord->create_time=$date;
                $refundRecord->refund_order_no=$model_new_refund->refund_order_no;
                $refundRecord->type=$model_new_refund->type;
                $refundRecord->terminal_type=$model_new_refund->terminal_type;
                $refundRecord->terminal_id=$model_new_refund->terminal_id;
                $refundRecord->refund_reason=$model_new_refund->refund_reason;
                $refundRecord->refund_tel=$model_new_refund->refund_tel;
                $refundRecord->refund_remark=$model_new_refund->refund_remark;
                $refundRecord->refund_img=$model_new_refund->refund_img;
                $refundRecord->order_sku_id=$model_new_refund->order_sku_id;
                $refundRecord->user_express=$model_new_refund->user_express;
                $refundRecord->user_express_no=$model_new_refund->user_express_no;
                $refundRecord->user_remark=$model_new_refund->user_remark;
                $refundRecord->user_tel=$model_new_refund->user_tel;
                $refundRecord->refuse_refund_remark=$model_new_refund->refuse_refund_remark;
                $refundRecord->refund_money=$model_new_refund->refund_money;
                $refundRecord->refund_address=$model_new_refund->refund_address;
                $refundRecord->if_return=$model_new_refund->if_return;
                $refundRecord->apply_refund_time=$model_new_refund->apply_refund_time;
                $refundRecord->agree_refund_time=$date;
                $refundRecord->status=ORDER_REFUND_STATUS_REFUND_SUCCESS;

                if($refundRecord->save()&&$ordersku->save())
                {
                    $result['status'] = ERROR_NONE;
                    $result['errMsg']='';

                    //判断所有SKU是否都退款成功
                    $model_sku=OrderSku::model()->findAll('order_id=:order_id and flag=:flag',array(
                        ':order_id'=>$order_id,
                        ':flag'=>FLAG_NO
                    ));
                    $flag=true;
                    foreach($model_sku as $k=>$v)
                    {
                        if($v['status']!=ORDER_SKU_STATUS_REFUNDSUCCESS)
                        {
                            $flag=false;
                        }
                    }
                    if($flag)
                    {
                        $order=Order::model()->findByPk($order_id);
                        $order->order_status=ORDER_STATUS_REFUND;
                        if($order->save())
                        {
                            $result['status'] = ERROR_NONE;
                            $result['errMsg']='';
                        }
                        else
                        {
                            $result['status'] = ERROR_SAVE_FAIL;
                            $result['errMsg']='保存失败';
                        }
                    }
                }
                else
                {
                    $result['status'] = ERROR_NO_DATA;
                    $result['errMsg']='无此数据';
                }
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg']='无此数据';
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
            return json_encode($result);
    }

    /**
     * 拒绝收货
     */
    public function RefuseTake($order_id,$order_sku_id,$order_status)
    {
        $result=array();
        try{
            //按时间排序
            $criteria=new CDbCriteria();
            $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
            $criteria->params=array(
                ':order_id'=>$order_id,
                ':order_sku_id'=>$order_sku_id,
                ':flag'=>FLAG_NO
            );
            $criteria->order='create_time desc';
            $model=RefundRecord::model()->find($criteria);
            $model_new_refund=RefundRecord::model()->findByPk($model->id);
            $date=date('Y-m-d H:i:s', time());

            $refundRecord=new RefundRecord();
            $refundRecord->order_id=$model_new_refund->order_id;
            $refundRecord->operator_id=$model_new_refund->operator_id;
            $refundRecord->operator_admin_id=$model_new_refund->operator_admin_id;
            $refundRecord->refund_channel=$model_new_refund->refund_channel;
            $refundRecord->refund_no=$model_new_refund->refund_no;
            $refundRecord->refund_time=$model_new_refund->refund_time;
            $refundRecord->create_time=$date;
            $refundRecord->refund_order_no=$model_new_refund->refund_order_no;
            $refundRecord->type=$model_new_refund->type;
            $refundRecord->terminal_type=$model_new_refund->terminal_type;
            $refundRecord->terminal_id=$model_new_refund->terminal_id;
            $refundRecord->refund_reason=$model_new_refund->refund_reason;
            $refundRecord->refund_tel=$model_new_refund->refund_tel;
            $refundRecord->refund_remark=$model_new_refund->refund_remark;
            $refundRecord->refund_img=$model_new_refund->refund_img;
            $refundRecord->order_sku_id=$model_new_refund->order_sku_id;
            $refundRecord->user_express=$model_new_refund->user_express;
            $refundRecord->user_express_no=$model_new_refund->user_express_no;
            $refundRecord->user_remark=$model_new_refund->user_remark;
            $refundRecord->user_tel=$model_new_refund->user_tel;
            $refundRecord->refuse_refund_remark=$model_new_refund->refuse_refund_remark;
            $refundRecord->refund_money=$model_new_refund->refund_money;
            $refundRecord->refund_address=$model_new_refund->refund_address;
            $refundRecord->if_return=$model_new_refund->if_return;
            $refundRecord->apply_refund_time=$model_new_refund->apply_refund_time;
            $refundRecord->status=ORDER_REFUND_STATUS_REFUSE_RECEIPT;

            if($refundRecord->save())
            {
                $result['status'] = ERROR_NONE;
                $result['errMsg']='';
            }
            else
            {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg']='保存失败';
            }
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 调用退款接口前检验该订单是否已经退款
     */
    public function getRefundInfo($merchantId,$order_id,$order_sku_id,$order_status)
    {
        $result=array();
        try{
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $data=array();
            $merchant=Merchant::model()->findByPk($merchantId);
            if(!empty($merchant))
            {
                $data['merchant']['partner']=$merchant->partner;
                $data['merchant']['seller_email']=$merchant->seller_email;
                $data['merchant']['key']=$merchant->key;
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg']='数据为空';
                throw new Exception('数据为空');
            }

            $order=Order::model()->findByPk($order_id);
            if(!empty($order))
            {
                $data['order']['trade_no']=$order->trade_no;
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg']='数据为空';
                throw new Exception('数据为空');
            }

            //按时间排序
            $criteria=new CDbCriteria();
            $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
            $criteria->params=array(
                ':order_id'=>$order_id,
                ':order_sku_id'=>$order_sku_id,
                ':flag'=>FLAG_NO
            );
            $criteria->order='create_time desc';
            $refund=RefundRecord::model()->find($criteria);
            if(!empty($refund))
            {
                $data['refund']['refund_money']=$refund->refund_money;
                $data['refund']['refund_reason']=$refund->refund_reason;
            }
            else
            {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg']='数据为空';
                throw new Exception('数据为空');
            }

            $result['data']=$data;
            $result['status'] = ERROR_NONE;
            $result['errMsg']='';
        }catch (Exception $e){
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    public function ShopTest()
    {
        $model=Order::model()->find('order_no=:order_no',array(
           ':order_no'=>'SC20151214787109'
        ));
        $orderid=$model->id;
        $sku=OrderSku::model()->findAll('order_id=:order_id',array(
           ':order_id'=>$orderid
        ));
        foreach($sku as $key=>$value)
        {
            $sku_model=OrderSku::model()->findByPk($value['id']);
            $sku_model->status=ORDER_SKU_STATUS_NORMAL;
            $sku_model->if_send=IF_SEND_NO;
            if($sku_model->save())
            {
                echo "success";
            }
        }


        /*$refund=RefundRecord::model()->find('order_id=:order_id and order_sku_id=:order_sku_id',array(
            ':order_id'=>$orderid,
            ':order_sku_id'=>$sku->id
        ));
        $refund->status=ORDER_REFUND_STATUS_RETURN_ISSUED;*/

        $order=Order::model()->find('id=:id',array(':id'=>$orderid));
        $order->order_status=ORDER_STATUS_WAITFORDELIVER;
        $order->pay_status=ORDER_STATUS_PAID;
        $order->pay_time=date('Y-m-d H:i:s', time());
        if(/*$sku->save()&&$refund->delete()&&*/$order->save())
        {
            var_dump('success');
        }
        else
        {
            var_dump('fail');
        }
    }
}