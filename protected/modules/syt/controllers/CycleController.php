<?php
class CycleController extends sytController
{
	/**
	 * 重写init方法， 不执行父类的 未登录就提示登录程序
	 */
	public function init() {
	
	}

	/**
	 * 任务计划
	 * 退款查询及订单更新
	 */
	public function actionRefundQueryTask() {
		set_time_limit(0);
		if (!isset($_GET['Yesterday']) || $_GET['Yesterday'] != 'Qwerkywriter1800') {
			exit();
		}
		$order = new OrderSC();
		$order->refundQuery();
	}
	
	/**
	 * 任务计划
	 * 订单查询
	 */
	public function actionOrderQueryTask() {
		set_time_limit(0);
		if (!isset($_GET['Yesterday']) || $_GET['Yesterday'] != 'Qwerkywriter1800') {
			exit();
		}
		$order = new OrderSC();
		$order->orderSearch();
	}
	
}