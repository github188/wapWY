<?php
/*
 * 时间：2015-6-27 创建人：王乃晓
 */
include_once (dirname ( __FILE__ ) . '/../mainClass.php');
class AlipayC extends mainClass {
	// 支付宝
	/**
	 * $orderNo 订单号
	 * $money 金额
	 * $productName 套餐条数名称
	 * $synNotifyUrl 同步通知页面路径
	 * $asyNotifyUrl 异步通知页面路径
	 */
	public function ToAlipay($orderNo, $productName, $synNotifyUrl, $asyNotifyUrl) {
		Yii::import ( 'application.extensions.alipay.*' );
		require_once ("lib/alipay_submit.class.php");
		require_once ("alipay.config.php");
		header ( "content-Type: text/html; charset=Utf-8" );
		
		// 支付类型
		$payment_type = "1";
		// 必填，不能修改
		// 服务器异步通知页面路径
		$notify_url = $asyNotifyUrl;
		// 需http://格式的完整路径，不能加?id=123这类自定义参数
		// 页面跳转同步通知页面路径
		$return_url = $synNotifyUrl;
		// 需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
		
		// 商户订单号
		$out_trade_no = $orderNo;
		// 商户网站订单系统中唯一订单号，必填
		
		// 订单名称
		$subject = $productName;
		// 必填
		
		if (substr ( $orderNo, 0, 2 ) == 'WQ') {
			$order = GjOrder::model ()->find ( 'order_no=:order_no and pay_status =:pay_status and order_status=:order_status', array (
					':order_no' => $orderNo,
					':pay_status' => GJORDER_PAY_STATUS_NUPAID,
					':order_status' => GJORDER_STATUS_NORMAL 
			) );
			if ($order) {
				// 付款金额
				$total_fee = $order->pay_money;
				// 必填
			}
		} elseif (substr ( $orderNo, 0, 2 ) == 'DX') {
			$messageOrder = MessageOrder::model ()->find ( 'order_no=:order_no and pay_status =:pay_status', array (
					':order_no' => $orderNo,
					':pay_status' => ORDER_STATUS_UNPAID 
			) );
			if ($messageOrder) {
				// 付款金额
				$total_fee = $messageOrder->pay_money;
				// 必填
			}
		} elseif (substr ( $orderNo, 0, 2 ) == 'AO') {
			$order = AgentOrder::model ()->find ( 'order_no=:order_no and pay_status =:pay_status', array (
					':order_no' => $orderNo,
					':pay_status' => GJORDER_PAY_STATUS_NUPAID
			) );
			if ($order) {
				// 付款金额
				$total_fee = $order->pay_money;
				// 必填
			}
		}
		
		// 订单描述
		
		$body = $productName;
		// 商品展示地址
		$show_url = '';
		// 需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
		
		// 防钓鱼时间戳
		$anti_phishing_key = "";
		// 若要使用请调用类文件submit中的query_timestamp函数
		
		// 客户端的IP地址
		$exter_invoke_ip = "";
		// 非局域网的外网IP地址，如：221.0.0.1
		
		/**
		 * *********************************************************
		 */
		
		// 构造要请求的参数数组，无需改动
		$parameter = array (
				"service" => "create_direct_pay_by_user",
				"partner" => trim ( $alipay_config ['partner'] ),
				"seller_email" => trim ( $alipay_config ['seller_email'] ),
				"payment_type" => $payment_type,
				"notify_url" => $notify_url,
				"return_url" => $return_url,
				"out_trade_no" => $out_trade_no,
				"subject" => $subject,
				"total_fee" => $total_fee,
				"body" => $body,
				"show_url" => $show_url,
				"anti_phishing_key" => $anti_phishing_key,
				"exter_invoke_ip" => $exter_invoke_ip,
				"_input_charset" => trim ( strtolower ( $alipay_config ['input_charset'] ) ) 
		);
		
		// 建立请求
		$alipaySubmit = new AlipaySubmit ( $alipay_config );
		$html_text = $alipaySubmit->buildRequestForm ( $parameter, "get", "确认" );
		echo $html_text;
	}

    // 支付宝
    /**
     * $orderNo 订单号
     * $money 金额
     * $productName 套餐条数名称
     * $synNotifyUrl 同步通知页面路径
     * $asyNotifyUrl 异步通知页面路径
     */
    public function ToAgentAlipay($orderNo, $productName, $synNotifyUrl, $asyNotifyUrl) {
        Yii::import ( 'application.extensions.alipay.*' );
        require_once ("lib/alipay_submit.class.php");
        require_once ("alipay.config.php");
        header ( "content-Type: text/html; charset=Utf-8" );

        // 支付类型
        $payment_type = "1";
        // 必填，不能修改
        // 服务器异步通知页面路径
        $notify_url = $asyNotifyUrl;
        // 需http://格式的完整路径，不能加?id=123这类自定义参数
        // 页面跳转同步通知页面路径
        $return_url = $synNotifyUrl;
        // 需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        // 商户订单号
        $out_trade_no = $orderNo;
        // 商户网站订单系统中唯一订单号，必填

        // 订单名称
        $subject = $productName;
        // 必填

//        $total_fee = 0.01;
        if (substr ( $orderNo, 0, 2 ) == 'WQ') {
            $order = GjOrder::model ()->find ( 'order_no=:order_no and pay_status =:pay_status and order_status=:order_status', array (
                ':order_no' => $orderNo,
                ':pay_status' => GJORDER_PAY_STATUS_NUPAID,
                ':order_status' => GJORDER_STATUS_NORMAL
            ) );
            if ($order) {
                // 付款金额
                $total_fee = $order->pay_money;
                // 必填
            }
        } elseif (substr ( $orderNo, 0, 2 ) == 'DX') {
            $messageOrder = MessageOrder::model ()->find ( 'order_no=:order_no and pay_status =:pay_status', array (
                ':order_no' => $orderNo,
                ':pay_status' => ORDER_STATUS_UNPAID
            ) );
            if ($messageOrder) {
                // 付款金额
                $total_fee = $messageOrder->pay_money;
                // 必填
            }
        } elseif (substr ( $orderNo, 0, 2 ) == 'AO') {
            $order = AgentOrder::model ()->find ( 'order_no=:order_no and pay_status =:pay_status', array (
                ':order_no' => $orderNo,
                ':pay_status' => FX_PAY_STATUS_NUPAID
            ) );
            $order2 = Agent::model() -> find('pay_status = :pay_status', array(
                ':pay_status' => AGENT_PAY_STATUS_PAID
            ));
            if ($order && $order2) {
                // 付款金额
                $total_fee = $order->pay_money;
                // 必填
            }
        }

        // 订单描述

        $body = $productName;
        // 商品展示地址
        $show_url = '';
        // 需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

        // 防钓鱼时间戳
        $anti_phishing_key = "";
        // 若要使用请调用类文件submit中的query_timestamp函数

        // 客户端的IP地址
        $exter_invoke_ip = "";
        // 非局域网的外网IP地址，如：221.0.0.1

        /**
         * *********************************************************
         */

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            "service" => "create_direct_pay_by_user",
            "partner" => trim ( $alipay_config ['partner'] ),
            "seller_email" => trim ( $alipay_config ['seller_email'] ),
            "payment_type" => $payment_type,
            "notify_url" => $notify_url,
            "return_url" => $return_url,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "show_url" => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,
            "_input_charset" => trim ( strtolower ( $alipay_config ['input_charset'] ) )
        );

        // 建立请求
        $alipaySubmit = new AlipaySubmit ( $alipay_config );
        $html_text = $alipaySubmit->buildRequestForm ( $parameter, "get", "确认" );
        echo $html_text;
    }


    // 短信服务器异步通知//页面跳转同步通知
	/**
	 * out_trade_no 订单号
	 * trade_no 支付交易号
	 * merchantId 商户id
	 * $pay_channel 支付渠道
	 */
	public function DuanXinSearch($out_trade_no, $trade_no, $merchantId, $pay_channel) {
		// 查找短信订单
		$messageOrder = MessageOrder::model ()->find ( 'order_no=:order_no', array (
				':order_no' => $out_trade_no 
		) );
		$transaction = Yii::app ()->db->beginTransaction (); // 开启事务
		try {
			// 如果短信订单为未付款就修改状态
			if ($messageOrder->pay_status == ORDER_STATUS_UNPAID) {
				
				$messageOrder->pay_status = ORDER_STATUS_PAID;
				$messageOrder->pay_time = new CDbExpression ( 'now()' );
				$messageOrder->trade_no = $trade_no;
				$messageOrder->pay_channel = $pay_channel;
				// 短信订单状态修改成功后给商户增加短信条数
				if ($messageOrder->update ()) {
					$merchant = Merchant::model ()->find ( 'id=:id and flag =:flag', array (
							':id' => $merchantId,
							':flag' => FLAG_NO 
					) );
					$merchant->msg_num = $merchant->msg_num + $messageOrder->message_num;
					if ($merchant->update ()) {
						$transaction->commit ();
						$result ['status'] = ERROR_NONE;
						return json_encode ( $result );
					} else {
						$transaction->rollBack ();
						$result ['status'] = ERROR_SAVE_FAIL;
						$result ['errMsg'] = '商户短信条数增加失败';
						return json_encode ( $result );
					}
				} else {
					$transaction->rollBack ();
					$result ['status'] = ERROR_SAVE_FAIL;
					$result ['errMsg'] = '短信订单修改失败';
					return json_encode ( $result );
				}
			}
		} catch ( Exception $e ) {
			$transaction->rollBack ();
		}
	}
}

