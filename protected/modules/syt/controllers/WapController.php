<?php
class WapController extends sytController
{
	/**
	 * 重写init方法， 不执行父类的 未登录就提示登录程序
	 */
	public function init() {
	
	}

	/**
	 * 微信门店扫码付款页面
	 */
	public function actionStoreWxpay() {
		$url = SYT_DOMAIN.'/syt/wap/pay?store='.$_GET['store'];
		$this->redirect($url);
/*		$this->layout = 'wap';

		$msg = '';
		$address = '';
		if (isset($_GET['store']) && !empty($_GET['store'])) {
			$store_id = $_GET['store'];
			Yii::app()->session['store_id'] = $store_id;
			//获取门店相关信息
			$store = new StoreC();
			$ret = $store->getStoreDetails($store_id);
			$result = json_decode($ret, true);
			if ($result['status'] == ERROR_NONE) {
				$store_name = $result['data']['name'];
				$address = $result['data']['address'];
				$merchant_id = $result['data']['merchant_id'];
				
				$wxpay = new WxpayC();
				$ret = $wxpay->getOpenId($merchant_id);
				$result = json_decode($ret, true);
				if ($result['status'] == ERROR_NONE) {
					if (isset($result['data']) && !empty($result['data'])) {
						Yii::app()->session['open_id'] = $result['data'];
					}
				}
				
				Yii::app()->session['store_name'] = $store_name;
			}
		}else {
			$msg = '获取门店相关信息失败';
		}
		
		$this->render('storeWxpay',array('msg' => $msg, 'address' => $address));*/
	}
	
	/**
	 * 下单
	 */
	public function actionCreateOrder() {
		$data = array();
		$data['error'] = 'failure';
		
		$store_id = Yii::app()->session['store_id'];
		if (!$store_id) {
			$data['errMsg'] = '获取门店相关信息失败';
			echo json_encode($data);
			exit();
		}
		$open_id = Yii::app()->session['open_id'];
		if (!$open_id) {
			$data['errMsg'] = '获取用户相关信息失败';
			echo json_encode($data);
			exit();
		}
		
		if (isset($_GET['money']) && !empty($_GET['money'])) {
			$money = $_GET['money'];
			
			$order_no = '';
			$transaction = Yii::app()->db->beginTransaction(); //开启事务
			try {
				$order = new OrderSC();
				//创建订单
				$order_info = $order->createOrder(
						$store_id,
						'user',
						NULL,
						ORDER_PAY_CHANNEL_WXPAY_SM,
						NULL,
						NULL,
						$money,
						false,
						ORDER_IF_USE_COUPONS_NO,
						false,
						$money,
						NULL,
						NULL,
						NULL,
						NULL
				);
				if (empty($order_info)) { //返回结果无效
					throw new Exception('系统内部错误');
				}
				
				$order_no = $order_info['order_no'];
				
				//将订单编号传给页面
				$data['orderNo'] = $order_no;
				
				//调用微信支付
				$wxjspay = new WxpayC();
				$ret = $wxjspay->WxJsPay($order_no,'扫码支付',NULL,WAPPAY_WQ_WECHAT_ASYNOTIFY,$open_id);
				$result = json_decode($ret, true);
				if ($result['status'] == ERROR_NONE) {
					$data['error'] = 'success';
					$data['jsParams'] = $result['data'];
				}else {
					$data['errMsg'] = $result['errMsg'];
				}
				
				$transaction->commit(); //数据提交
				
			} catch (Exception $e) {
				$transaction->rollback(); //数据回滚
				$data['errMsg'] = $e->getMessage();
			}
		}else {
			$data['errMsg'] = '请输入正确的支付金额';
		}
		
		echo json_encode($data);
	}
	
	/**
	 * 支付成功页
	 */
	public function actionPaySuccess() {
		$msg = '';
		if (isset($_GET['order_no']) && !empty($_GET['order_no'])) {
			$order_no = $_GET['order_no'];
			//调用微信支付订单查询接口
			$api = new WxpaySC();
			$ret = $api->wxpaySearch($order_no);
			$result = json_decode($ret, true);
			if ($result['status'] != ERROR_NONE) {
				$msg = $result['errMsg'];
			}else {
				//查询订单支付状态
				$order = new OrderSC();
				$ret = $order->getOrderDetail('', $order_no);
				$result = json_decode($ret, true);
				if ($result['status'] != ERROR_NONE) {
					$msg = $result['errMsg'];
				}else {
					if ($result['data']['pay_status'] == ORDER_STATUS_PAID) {
						//订单已支付
						$msg = '支付成功';
					}else {
						//订单未支付
						$msg = '支付未成功';
					}
				}
			}
		}else {
			$msg = '错误的请求';
		}
		$this->redirect('storeWxpay', array('store' => Yii::app()->session['store_id']));
		//$this->render('paySuccess', array('msg' => $msg));
	}
	
	
}