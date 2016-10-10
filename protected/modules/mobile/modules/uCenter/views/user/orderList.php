<head>
    <title>我的订单</title>
</head>

<body class="order">
<header class="top_title">
    <nav class="header-nav">
        <ul>
            <li class="<?php echo ORDER_PAY_WAITFORCONFIRM == $stored_confirm_status ? 'bg' : ''; ?>">
                <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/orderList', array('stored_confirm_status' => ORDER_PAY_WAITFORCONFIRM, 'encrypt_id' => $encrypt_id)) ?>">待确认</a>
            </li>
            <li class="<?php echo ORDER_PAY_WAITFORCONFIRM != $stored_confirm_status ? 'bg' : ''; ?>">
                <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/orderList', array('stored_confirm_status' => ORDER_PAY_CONFIRM, 'encrypt_id' => $encrypt_id)) ?>">已完成</a>
            </li>
        </ul>
    </nav>
</header>
<section class="mid_con">
    <?php if (!empty($list)) {
        foreach ($list as $key => $value) {
            echo CHtml::beginForm() ?>
            <div class="orderid" onclick="detail('<?php echo $value['id'] ?>')">
                <div class="id">
                    <input type="hidden" value="<?php echo $value['order_no'] ?>" name="order_no">
                    <span class="left">订单号：<?php echo $value['order_no'] ?></span>
                    <span class="right"><?php echo $GLOBALS['ORDER_STATUS_PAY'][$value['pay_status']] ?></span>
                </div>
                <div class="order-con">
                    <div class="id">
                        <span class="left">商品名称：</span>
                        <span class="right"><?php echo $value['name'] ?></span>
                    </div>
                    <div class="id">
                        <span class="left">订单金额：</span>
                        <span class="right"><?php echo $value['order_paymoney'] ?></span>
                    </div>
                    <div class="id">
                        <span class="left">下单时间</span>
                        <span class="right"><?php echo $value['create_time'] ?></span>
                    </div>
                    <?php if ($value['stored_confirm_status'] == ORDER_PAY_WAITFORCONFIRM) { ?>
                        <div class="id">
                            <span class="left">储值卡还需支付：</span>
                            <span class="right"><?php echo $value['stored_paymoney'] ?></span>
                        </div>
                        <div class="id noline">
                            <span class="left"></span>
                            <span class="right"><input type="button" value="确认支付" class="btn-pay"></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php echo CHtml::endForm() ?>
        <?php }
    } ?>
</section>
</body>

<script type="text/javascript">
    function detail(order_id) {
        location.href = "<?php echo Yii::app()->createUrl('mobile/uCenter/user/orderDetail'); ?>" + "?order_id=" + order_id + "encrypt_id='<?php echo $encrypt_id ?>'";
    }
    $(".btn-pay").click(function () {
        if (!confirm('确认支付？')) {
            return false;
        }
        var order_no = $("input[name=order_no]").val();
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('mobile/uCenter/user/storedConfirm'));?>',
            data: {order_no: order_no, encrypt_id : '<?php echo $encrypt_id ?>'},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.error == 'success') {
                    alert('确认成功');
                    location.reload();
                } else {
                    alert(data.errMsg);
                }
            }
        });
    });
</script>

