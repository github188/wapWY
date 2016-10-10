<head>
    <title>订单详情</title>
</head>
<body class="mallOrder">
<header class="detail">
    订单状态：<?php echo $list['pay_status'] == ORDER_STATUS_PAID ? $GLOBALS['ORDER_STATUS'][$list['order_status']] : '待付款'; ?></header>
<section class="orderDetail">
    <article class="item clearfix">
        <div class="itemone"><img src="<?php echo USER_STATIC_IMAGES; ?>user/address-ico.png"></div>
        <div class="itemtow">

            <div class="filed consignee">
                <span class="label">收货人：</span>
                <span class="text"><?php echo $address_arr[5]; ?></span>
                <span class="text tel"><?php echo isset($address_arr[6]) ? $address_arr[6] : ''; ?></span>
            </div>
            <div class="filed">
                <span class="label">收货地址：</span>
                <span
                    class="text address"><?php echo $address_arr[0] . $address_arr[1] . $address_arr[2] . $address_arr[3]; ?></span>
            </div>

        </div>
        <div class="itemthree"></div>
    </article>

    <article class="item clearfix">
        <?php if (!empty($list['order_sku'])) { ?>
            <?php for ($i = 0; $i < count($list['order_sku']); $i++) { ?>
                <div class="itemMod clearfix">
                    <a href="<?php echo Yii::app()->createUrl('mall/commodity/commodityDetails', array('id' => $list['order_sku'][$i]['product_id'], 'encrypt_id' => $encrypt_id)) ?>">
        	<span class="img">
        		<img src="<?php $arr_img = explode(';', $list['order_sku'][$i]['product_img']);
                echo IMG_GJ_80_LIST . $arr_img[0]; ?>">
        	</span>
            <span class="name">
            	<em><?php echo $list['order_sku'][$i]['product_name']; ?></em>
                <em class="state"><?php echo $list['order_sku'][$i]['sku_name']; ?></em>
            </span>
                    </a>
            <span class="price">
            	<em>¥ <?php echo $list['order_sku'][$i]['price']; ?></em>
                <em>× <?php echo $list['order_sku'][$i]['num']; ?></em>
                <?php if ($list['pay_status'] == ORDER_STATUS_PAID && ($list['order_status'] == ORDER_STATUS_WAITFORDELIVER || $list['order_status'] == ORDER_STATUS_DELIVER || $list['order_status'] == ORDER_STATUS_REFUND)) { ?>
                    <?php if ($list['order_sku'][$i]['status'] == ORDER_SKU_STATUS_NORMAL) { ?>
                        <em>
                            <a href="<?php echo Yii::app()->createUrl('mall/order/applyRefundObj', array('order_sku_id' => $list['order_sku'][$i]['id'], 'order_id' => $list['id'], 'order_status' => $list['order_status'], 'encrypt_id' => $encrypt_id)); ?>"
                               class="btm_com_red">申请退款</a>
                        </em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_APPLY_REFUND_NORETURN || $list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_APPLY_REFUND_RETURN) { ?>
                        <em>已申请退款，等待处理</em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_AGREE_NORETURN) { ?>
                        <em>卖家同意退款</em>
                    <?php } else if ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_AGREE_RETURN || $list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_REFUSE_RECEIPT) { ?>
                        <em><a href="<?php echo Yii::app()->createUrl('mall/Order/RefundDetails', array('order_id' => $list['id'], 'order_sku_id' => $list['order_sku'][$i]['id'], 'order_status' => $list['order_status'], 'encrypt_id' => $encrypt_id)) ?>"
                               class="btm_com_red">退款中</a></em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_RETURN_ISSUED) { ?>
                        <em>退货已发出</em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_RETURN_RECEIPT) { ?>
                        <em>退货已收货</em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_FINANCIAL_PLAY) { ?>
                        <em>财务打款中</em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_REFUND_SUCCESS) { ?>
                        <em>退款成功</em>
                    <?php } elseif ($list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_REFUSE_REFUND) { ?>
                        <em>
                            <a href="<?php echo Yii::app()->createUrl('mall/order/applyRefundObj', array('order_sku_id' => $list['order_sku'][$i]['id'], 'order_id' => $list['id'], 'order_status' => $list['order_status'], 'encrypt_id' => $encrypt_id)); ?>"
                               class="btm_com_red">申请退款</a>
                        </em>
                    <?php } ?>
                <?php } ?>
            </span>
                </div><!--end itemMod-->
            <?php } ?>
        <?php } ?>
        <div class="more clearfix">
            <span class="name">运费</span>
            <span class="price">¥ <?php echo $list['freight_money']; ?></span>
        </div>
        <div class="more clearfix">
            买家留言:<?php echo $list['remark']; ?>
        </div>
        <div class="more clearfix end">
            <a href="">
                <span class="name">合计</span>
                <span class="price"><ins>¥ <?php echo number_format($list['real_pay'], 2, '.', ''); ?></ins></span>
            </a>
        </div>
    </article>
    <article class="item clearfix mod">
        <div class="more">
            <span>¥ <?php echo number_format($list['real_pay'], 2, '.', ''); ?>
                + ¥ <?php echo $list['freight_money']; ?>运费</span>
            <span><ins>
                    需付：¥ <?php echo number_format($list['real_pay'] + $list['freight_money'], 2, '.', ''); ?></ins></span>
        </div>
        <div class="itemL">
            <div class="filed">
                <span class="label">订单号：</span>
                <span class="text"><?php echo $list['order_no']; ?></span>
            </div>
            <div class="filed pay">
                <span class="label">下单时间：</span>
                <span class="text"><?php echo date('Y.m.d H:i:s', strtotime($list['create_time'])); ?></span>
            </div>
        </div>
        </div>
    </article>
    <article class="bottom">
        <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
    </article>
</section>

</body>

