<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * wap订单类
 * @author  xyf
 * 2015/9/14
 */

class OrderUc extends mainClass{
	/**
	 * wap获取订单列表
	 * $user_id       用户id
	 * $order_status  订单状态
	 * $pay_status    订单支付状态
	 */
	public function getOrderList($user_id, $order_status, $pay_status,$merchant_id = '')
	{
		
		$result = array ();
		//$user_id = 1;
		$criteria = new CDbCriteria();
		$criteria -> addCondition('order_type!=:order_type and flag=:flag and user_id=:user_id and merchant_id =:merchant_id');
		$criteria -> params = array(
				':order_type' => ORDER_TYPE_CASHIER,
				':flag' => FLAG_NO,
				':user_id' => $user_id,
				':merchant_id' => $merchant_id
		);
		$criteria -> order = 'create_time desc';
		
		if(!empty($order_status)){ //订单状态搜索
			if ($order_status == ORDER_STATUS_WAITFORDELIVER){ //如果是待发货  则显示的是已付款的待发货
				$criteria -> addCondition('order_status=:order_status and pay_status=:pay_status');
				$criteria -> params[':order_status'] = $order_status;
				$criteria -> params[':pay_status'] = ORDER_STATUS_PAID;
			}else if($order_status==ORDER_STATUS_REFUND||$order_status==ORDER_STATUS_PART_COMPLETE) {
                $criteria->addInCondition('order_status',array(ORDER_STATUS_REFUND,ORDER_STATUS_PART_COMPLETE));
            } else{
			    $criteria -> addCondition('order_status=:order_status');
			    $criteria -> params[':order_status'] = $order_status;
			}
		}
		
		if(!empty($pay_status)){ //订单付款状态
			$criteria -> addCondition('pay_status=:pay_status');
			$criteria -> params[':pay_status'] = $pay_status;
		}
		
		$model = Order::model()->findAll($criteria);
		$data = array();
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['order_no'] = $v -> order_no; //订单号
				$data['list'][$k]['order_type'] = $v -> order_type; //订单类型
				$data['list'][$k]['trade_no'] = $v -> trade_no; //交易流水号
				$data['list'][$k]['pay_channel'] = $v -> pay_channel; //支付渠道
				$data['list'][$k]['pay_status'] = $v -> pay_status; //支付状态
				$data['list'][$k]['order_status'] = $v -> order_status; //订单状态
				$data['list'][$k]['order_paymoney'] = $v -> order_paymoney; //订单总金额
				$data['list'][$k]['pay_time'] = $v -> pay_time; //支付时间
				$data['list'][$k]['remark'] = $v -> remark; //买家备注
				$data['list'][$k]['seller_remark'] = $v -> seller_remark; //卖家备注
				$data['list'][$k]['send_time'] = $v -> send_time; //发货时间
				$data['list'][$k]['receive_time'] = $v -> receive_time; //收货时间
				$data['list'][$k]['cancel_time'] = $v -> cancel_time; //取消时间
				$data['list'][$k]['create_time'] = $v -> create_time; //创建时间
				$data['list'][$k]['user_name'] = isset($v -> user -> name)?$v -> user -> name:''; //买家名称
				$data['list'][$k]['user_phone'] = isset($v -> user -> account)?$v -> user -> account:''; //买家手机号
				$data['list'][$k]['order_sku'] = $this->getOrderSku($v -> id); //获取订单id对应的订单sku信息
				$data['list'][$k]['real_pay'] = $this->getRealPay($v -> id); //获取订单id实收金额
				$data['list'][$k]['freight_money'] = $v -> freight_money;
		
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
	 * wap订单详情
	 * $user_id  用户id
	 * $order_id 订单id
	 */
	public function getOrderDetail($order_id, $user_id)
	{
		$result = array ();
		$model = Order::model ()->findByPk ( $order_id );
		$data = array();
		if(!empty($model)){
			$data['id'] = $model -> id;
			$data['order_no'] = $model -> order_no; //订单号
			$data['order_type'] = $model -> order_type; //订单类型
			$data['order_status'] = $model -> order_status; //订单状态
			$data['pay_status'] = $model -> pay_status; //支付状态
			$data['order_paymoney'] = $model -> order_paymoney; //订单总金额
			$data['remark'] = $model -> remark; //买家备注
			$data['seller_remark'] = $model -> seller_remark; //卖家备注
			$data['trade_no'] = $model -> trade_no; //交易流水号
			$data['pay_channel'] = $model -> pay_channel; //支付渠道
			$data['address'] = $model -> address; //收货地址
			$data['send_time'] = $model -> send_time; //发货时间
			$data['receive_time'] = $model -> receive_time; //收货时间
			$data['freight_money'] = $model -> freight_money; //运费
			$data['pay_time'] = $model -> pay_time; //支付时间
			$data['complete_time'] = $model -> complete_time; //交易完成时间
			$data['create_time'] = $model -> create_time; //创建时间
			$data['user_name'] = isset($model -> user -> name)?$model -> user -> name:''; //买家名称;
			$data['user_phone'] = isset($model -> user -> account)?$model -> user -> account:''; //买家手机号;
			$data['order_sku'] = $this->getOrderSku($model -> id); //获取订单id对应的订单sku信息
			$data['real_pay'] = $this->getRealPay($model -> id); //获取订单id实收金额
                        $ordersku = OrderSku::model()->find('flag=:flag and order_id=:order_id',array(':flag'=>FLAG_NO,':order_id'=>$model['id']));
                        $data['num'] = $ordersku -> num;
				
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
		}else{
			$result ['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '无此数据'; //错误信息
		}
		return json_encode($result);
	}
	

	
	/**
	 *  地址修改
	 *  $order_id           订单id
	 *  $liveplaceProvince  省份
	 *	$liveplaceCity      城市
	 *	$liveplaceArea      区域
	 *	$postCode           邮政编码
	 *	$streetAddress      街道地址
	 *	$userName           收件人姓名
	 *	$userPhone          手机号码
	 */
	public function editAddress($order_id,$liveplaceProvince,$liveplaceCity,$liveplaceArea,
			$postCode,$streetAddress,$userName,$userPhone)
	{
		$result = array ();
		$model = Order::model()->findByPk($order_id);
		$address = $liveplaceProvince.','.$liveplaceCity.','.$liveplaceArea.','.$streetAddress.','.$postCode.','.$userName.','.$userPhone;
		$model -> address = $address;
	
		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败'; //错误信息
		}
		return json_encode($result);
	}
	
	
	/**
	 * 获取订单id对应的订单sku信息
	 */
	public function getOrderSku($order_id)
	{
		$array = array();
		$model = OrderSku::model()->findAll('order_id=:order_id',array(':order_id'=>$order_id));
		if(!empty($model)){
			foreach ($model as $k=>$v){
				$array[$k]['id'] = $v->id;
				$array[$k]['num'] = $v->num; //数量
				$array[$k]['price'] = $v->price; //价格
				$array[$k]['status'] = $v->status; //订单sku状态
				//退款中的订单sku
                $refund_record_arr = array();
				if($v->status == ORDER_SKU_STATUS_REFUND||$v->status == ORDER_SKU_STATUS_REFUNDSUCCESS){
                    $criteria=new CDbCriteria();
                    $criteria->addCondition('order_sku_id = :order_sku_id and flag =:flag');
                    $criteria->params=array(
                        ':order_sku_id' => $v -> id,
                        ':flag' => FLAG_NO
                    );
                    $criteria->order='create_time desc';
					$refund_record = RefundRecord::model() -> find($criteria);

					if(!empty($refund_record)){
						$refund_record_arr['status'] = $refund_record -> status;
						$refund_record_arr['refund_reason'] = $refund_record -> refund_reason;
						$refund_record_arr['refund_tel'] = $refund_record -> refund_tel;
						$refund_record_arr['refund_remark'] = $refund_record -> refund_remark;
						$refund_record_arr['refund_img'] = $refund_record -> refund_img;
					}
				}
                $array[$k]['refund_record'] = $refund_record_arr;
				$array[$k]['product_name'] = $v->product_name; //产品名称
				$array[$k]['if_send'] = $v->if_send; //是否发货
				$array[$k]['product_id'] = $v -> shop_product_sku -> product_id;
				$array[$k]['sku_name'] = isset($v -> shop_product_sku -> name)?$v -> shop_product_sku -> name:''; //获取商品属性
				$list = $this->getShopProduct($v -> sku_id);
				$array[$k]['product_img']=$list -> img;//获取商品图片
			}
		}
		return $array;
	}
	
	/**
	 * 获取订单sku对象
	 */
	public function getOrderSkuForId($order_sku_id)
	{
		$order_sku = OrderSku::model()->findByPk($order_sku_id);
		return $order_sku;
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
	 * 获取订单支付状态为$pay_status的数量
	 * $pay_status  订单支付状态
	 * $user_id     用户id
	 */
	public function getPayStatusCount($user_id,$pay_status,$merchant_id = '')
	{
		$model = Order::model()->findAll('user_id=:user_id and pay_status=:pay_status and merchant_id = :merchant_id and flag=:flag and order_status =:order_status and order_no like "SC%"',array(
				':user_id'=>$user_id,
				':pay_status'=>$pay_status,
				':flag'=>FLAG_NO,
				':order_status' => ORDER_STATUS_NORMAL,
				':merchant_id' => $merchant_id
		));
		return count($model);
		
	}
	
	/**
	 * 获取订单状态为$order_status的数量
	 * $order_status  订单状态
	 * $user_id       用户id
	 */
	public function getOrderStatusCount($user_id,$order_status,$merchant_id = '')
	{
        if($order_status!=ORDER_STATUS_ACCEPT) {
            //不是已完成的订单
            $model = Order::model()->findAll('user_id=:user_id and order_status=:order_status and flag=:flag and merchant_id =:merchant_id and pay_status =:pay_status', array(
                ':user_id' => $user_id,
                ':order_status' => $order_status,
                ':flag' => FLAG_NO,
                ':pay_status' => ORDER_STATUS_PAID,
                ':merchant_id' => $merchant_id
            ));
        }else{
            //已完成订单
            $criteria=new CDbCriteria();
            $criteria->addCondition('user_id=:user_id and flag=:flag and merchant_id =:merchant_id and pay_status =:pay_status');
            $criteria->params= array(
                ':user_id' => $user_id,
                ':flag' => FLAG_NO,
                ':pay_status' => ORDER_STATUS_PAID,
                ':merchant_id' => $merchant_id
            );
            $criteria->addInCondition('order_status',array(ORDER_STATUS_REFUND,ORDER_STATUS_PART_COMPLETE));
            $model = Order::model()->findAll($criteria);
        }
		return count($model);
	}
	
	/**
	 * ajax 获得更多订单信息
	 * $order_id  订单id
	 */
	public function getMoreOrder($order_id)
	{
		$result = array ();
		$order = Order::model()->findByPk($order_id);
		$result['id'] = $order['id'];
		$result['order_sku'] = $this->getOrderSku($order_id);
		
		return $result;
	}
	
	/**
	 * 用户确认收货
	 * $order_id  订单id
	 */
	public function confirmReceipt($order_id)
	{
		$result = array ();
		
		$order = Order::model()->findByPk($order_id);
		$order -> receive_time = date('Y-m-d H:i:s');
		$order -> order_status = ORDER_STATUS_ACCEPT;
		
		if($order -> save()){
			$result ['status'] = ERROR_NONE;
			$result['errMsg'] = ''; //错误信息
		}else{
			$result ['status'] = ERROR_SAVE_FAIL;
			$result['errMsg'] = '数据保存失败'; //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 用户申请退款（实物订单）
	 * $order_id       订单Id
	 * $order_sku_id   订单sku  Id
	 * $order_status   订单状态
	 * $refund_reason  退款原因
	 * $refund_money   退款金额
	 * $refund_tel     联系方式
	 * $status         退款处理方式（申请退款无需退货 /申请退款需退货）
	 * $refund_remark  退款备注
	 */
	public function applyRefundObj($order_id,$order_sku_id,$order_status,$refund_reason,$refund_money,$refund_tel,$status,$refund_remark)
	{	
		$result = array();
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$order_sku = OrderSku::model()->findByPk($order_sku_id);
			$order_sku -> status = ORDER_SKU_STATUS_REFUND;
			$order_sku->if_send=$status == ORDER_REFUND_STATUS_APPLY_REFUND_NORETURN?IF_SEND_NO:IF_SEND_YES;
			//订单状态改为退款处理中
 			/*$order = Order::model() -> findByPk($order_sku -> order_id);
 			$order -> order_status = ORDER_STATUS_HANDLE_REFUND;
			
 			if($order -> update()){
				
 			}else{
 				$result ['status'] = ERROR_SAVE_FAIL;
 				throw new Exception('订单数据保存失败');
 			}*/
			if($order_sku -> save()){
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
                //创建退款记录
                $refund_record = new RefundRecord();
                $date=date ( 'Y-m-d H:i:s' );
				$refund_record -> order_sku_id = $order_sku -> id;
				$refund_record -> order_id = $order_sku -> order -> id;
				$refund_record -> refund_money = $refund_money;
				$refund_record -> create_time = $date;
				$refund_record -> type = REFUND_TYPE_SCREFUND;
                $refund_record -> refund_reason = $refund_reason;
                $refund_record -> refund_tel = $refund_tel;
                $refund_record -> refund_remark = $refund_remark;
                $refund_record->apply_refund_time=$date;
                if(!empty($model))
                {
                    $refund_record->refund_address=$model->refund_address;
                }
                $refund_record -> refund_remark = $refund_remark;
                $refund_record->if_return=$status==ORDER_REFUND_STATUS_APPLY_REFUND_RETURN?IF_RETURN_YES:IF_RETURN_NO;
                $refund_record->status=$status;


				if($refund_record -> save()){
					$transaction->commit(); //数据提交
					$result ['status'] = ERROR_NONE;
					$result['errMsg'] = ''; //错误信息
				}else{
                    $transaction->rollback();
					$result ['status'] = ERROR_SAVE_FAIL;
					throw new Exception('退款记录数据保存失败');
				}
			}else{
                $transaction->rollback();
				$result ['status'] = ERROR_SAVE_FAIL;
				throw new Exception('订单sku数据保存失败');
			}
			
		} catch (Exception $e) {
			$transaction->rollback(); //数据回滚
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}
	
	/**
	 * 获取某个sku的总价
	 */
	public function getSkuPrice($order_sku_id)
	{
		$price = 0;
		$order_sku = OrderSku::model()->findByPk($order_sku_id);
		$price = $order_sku['num']*$order_sku['price'];
		return $price;
	}

    /**
     * 用户的退货物流信息
     */
    public function RefundMsg($skuid,$orderid,$orderstatus,$tel,$logistics,$ordernum,$remark)
    {
        $result=array();
        try{


            $criteria=new CDbCriteria();
            $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
            $criteria->params=array(
                ':order_id'=>$orderid,
                ':order_sku_id'=>$skuid,
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
            $refundRecord->refund_time=$date;
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
            $refundRecord->user_express=$logistics;
            $refundRecord->user_express_no=$ordernum;
            $refundRecord->user_remark=$remark;
            $refundRecord->user_tel=$tel;
            $refundRecord->refuse_refund_remark=$model_new_refund->refuse_refund_remark;
            $refundRecord->refund_money=$model_new_refund->refund_money;
            $refundRecord->refund_address=$model_new_refund->refund_address;
            $refundRecord->apply_refund_time=$model_new_refund->apply_refund_time;
            $refundRecord->if_return=IF_RETURN_YES;
            $refundRecord->status=ORDER_REFUND_STATUS_RETURN_ISSUED;



        if($refundRecord->save())
        {
            $result ['status'] = ERROR_NONE;
            $result['errMsg'] = ''; //错误信息
        }else
        {
            $result ['status'] = ERROR_SAVE_FAIL;
            $result['errMsg'] = '保存失败'; //错误信息
        }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 查询退货地址信息
     */
    public function GetRefundDetails($order_id,$order_sku_id)
    {
        $result=array();
        try{
            $criteria=new CDbCriteria();
            $criteria->addCondition('order_id=:order_id and order_sku_id=:order_sku_id and flag=:flag');
            $criteria->params=array(':order_id'=>$order_id,':order_sku_id'=>$order_sku_id,':flag'=>FLAG_NO);
            $criteria->order="create_time desc";//查找最新的记录
            $model=RefundRecord::model()->find($criteria);
            $list=array();
            if(!empty($model))
            {
                $list['refund_address']=$model->refund_address;
                $list['apply_refund_time']=$model->apply_refund_time;
                $list['refund_reason']=$model->refund_reason;
                $list['if_return']=$model->if_return;
                $list['refund_money']=$model->refund_money;

                $result['data']=$list;
                $result['status']=ERROR_NONE;
                $result['errMsg']='';
            }
            else
            {
                $result['status']=ERROR_NO_DATA;
                $result['errMsg']='无此数据';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}