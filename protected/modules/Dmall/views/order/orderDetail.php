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
            <div class="filed">
                <span class="label">收货人：</span>
                <span class="text"><?php echo $address_arr[0]; ?></span>
            </div>
            <div class="filed">
                <span class="label">手机号：</span>
                <span class="text address"><?php echo $address_arr[1]; ?></span>
            </div>
            <div class="filed">
                <span class="label">游玩时间：</span>
                    <span class="text address">
                        <?php if (!empty($list['order_sku'])) { ?>
                            <?php for ($i = 0; $i < count($list['order_sku']); $i++) { ?>
                                <?php if ($list['order_sku'][$i]['third_party_source'] == SHOP_PRODUCT_THIRED_ZHIYOUBAO) { ?>
                                    <?php echo date('Y.m.d', strtotime($address_arr[2])); ?>
                                <?php } ?>
                                <?php if ($list['order_sku'][$i]['third_party_source'] == SHOP_PRODUCT_THIRED_TIANSHI) { ?>
                                    <?php if ($list['order_sku'][$i]['use_time_type'] == DSHOP_TIME_TYPE) { ?>
                                        <?php echo date('Y.m.d', strtotime($address_arr[2])); ?>
                                    <?php } ?>
                                    <?php if ($list['order_sku'][$i]['use_time_type'] == DSHOP_TIME_TYPE_DAY) { ?>
                                        <?php echo date('Y.m.d', strtotime($list['order_sku'][$i]['create_time'])) ?>起<?php echo $list['order_sku'][$i]['date_num'] ?>天有效
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </span>
            </div>
        </div>
        <div class="itemthree"></div>
    </article>

    <article class="item clearfix">
        <?php if (!empty($list['order_sku'])) { ?>
            <?php for ($i = 0; $i < count($list['order_sku']); $i++) { ?>
                <div class="itemMod clearfix">
                    <a href="<?php echo Yii::app()->createUrl('Dmall/commodity/commodityDetails', array('id' => $list['order_sku'][$i]['product_id'], 'encrypt_id' => $encrypt_id)) ?>">
                        <span class="img">
                            <img src="<?php $arr_img = explode(';', $list['order_sku'][$i]['product_img']); echo IMG_GJ_LIST . $arr_img[0]; ?>">
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
                                    <a href="<?php echo Yii::app()->createUrl('Dmall/order/applyRefundObj', array('order_sku_id' => $list['order_sku'][$i]['id'], 'order_id' => $list['id'], 'order_status' => $list['order_status'], 'encrypt_id' => $encrypt_id)); ?>"
                                       class="btm_com_red">申请退款</a>
                                </em>
                            <?php } elseif ($list['order_sku'][$i]['status'] == ORDER_REFUND_STATUS_APPLY_REFUND_NORETURN || $list['order_sku'][$i]['refund_record']['status'] == ORDER_REFUND_STATUS_APPLY_REFUND_RETURN) { ?>
                                <em>卖家同意退款</em>
                            <?php } elseif ($list['order_sku'][$i]['status'] == ORDER_REFUND_STATUS_AGREE_NORETURN) { ?>
                                <em>已申请退款，等待处理</em>
                                <?php //}else if($list['order_sku'][$i]['status'] == ORDER_REFUND_STATUS_AGREE_RETURN||$list['order_sku'][$i]['status'] ==ORDER_REFUND_STATUS_REFUSE_RECEIPT){?>
                                <!--<em><a href="<?php //echo Yii::app()->createUrl('Dmall/Order/RefundDetails',array('order_id'=>$list['id'],'order_sku_id'=>$list['order_sku'][$i]['id'],'order_status'=>$list['order_status']))?>"  class="btm_com_red">退款中</a></em>-->
                            <?php } elseif ($list['order_sku'][$i]['status'] == ORDER_REFUND_STATUS_RETURN_ISSUED) { ?>
                                <em>退货已发出</em>
                            <?php } elseif ($list['order_sku'][$i]['status'] == ORDER_REFUND_STATUS_RETURN_RECEIPT) { ?>
                                <em>退货已收货</em>
                            <?php } elseif ($list['order_sku'][$i]['status'] == ORDER_REFUND_STATUS_FINANCIAL_PLAY) { ?>
                                <em>财务打款中</em>
                            <?php } elseif ($list['order_sku'][$i]['status'] == ORDER_SKU_STATUS_REFUNDSUCCESS) { ?>
                                <em>退款成功</em>
                                <?php //}elseif ($list['order_sku'][$i]['status'] == ORDER_SKU_STATUS_REFUND){?>
                                <!--                        <em>
                                    <a href="<?php //echo Yii::app()->createUrl('Dmall/order/applyRefundObj',array('order_sku_id'=>$list['order_sku'][$i]['id'],'order_id'=>$list['id'],'order_status'=>$list['order_status'])); ?>"  class="btm_com_red">申请退款</a>
                                </em>-->
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
    <article class="bottom" style="display: none">
        <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
    </article>
</section>

</body>

