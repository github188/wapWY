<head>
	<title>订单详情</title>
</head>

<body class="order">
	<header class="top_title">
		<div class="name">交易详情： <?php echo $GLOBALS['ORDER_STATUS_PAY'][$order['pay_status']]?></div>
	</header>
	<section class="mid_con">
		<div class="orderid ">
			<div class="detail">
				<span class="left">订单号：<?php echo $order['order_no']?></span>
			</div>
			<div class="detail-con">
				<div class="id">
					<span class="left">商户名称：</span>
					<span class="right"><?php echo $order['name']?></span>
				</div>
				<div class="id">
					<span class="left">会员账号：</span>
					<span class="right"><?php echo $order['account']?></span>
				</div>
				<div class="id">
					<span class="left">收款操作员:</span>
					<span class="right"><?php echo $order['operator_id']?></span>
				</div>
			</div>
			<div class="detail-con">
				<div class="id">
					<span class="left">订单金额：</span>
					<span class="right"><?php echo $order['order_paymoney']?></span>
				</div>
				<div class="id">
					<span class="left">优惠金额：</span>
					<span class="right">
						<em>红包-<?php echo isset($order['hongbao_money']) ? $order['hongbao_money'] : '0'?></em>
						<em>优惠券-<?php echo isset($order['coupons_money']) ? $order['coupons_money'] : '0'?></em>
						<em>会员折扣-<?php echo isset($order['discount_money']) ? $order['discount_money'] : '0'?></em>
					</span>
				</div>
				<div class="id">
					<span class="left">实收金额:</span>
					<span class="right"><?php echo $order['order_paymoney']-$order['hongbao_money']-$order['coupons_money']-$order['discount_money']?></span>
				</div>
			</div>
			<div class="detail-con">
				<div class="id">
					<span class="left">储值卡：</span>
					<span class="right"><?php echo $order['stored_paymoney']?></span>
				</div>
				<div class="id">
					<span class="left">支付方式：</span>
					<span class="right"><?php echo $GLOBALS['ORDER_PAY_CHANNEL'][$order['pay_channel']]?></span>
				</div>
				<div class="id">
					<span class="left">用户支付宝账号:</span>
					<span class="right"><?php echo $order['alipay_account']?></span>
				</div>
				<div class="id">
					<span class="left">交易号:</span>
					<span class="right"><?php echo $order['trade_no']?></span>
				</div>
			</div>
			<div class="detail-con">
				<div class="id">
					<span class="left">下单时间：</span>
					<span class="right"><?php echo $order['create_time']?></span>
				</div>
				<div class="id">
					<span class="left">交易时间：</span>
					<span class="right"><?php echo $order['pay_time']?></span>
				</div>
			</div>
		</div>
	</section>
</body>
