<head>
    <title>订单支付</title>
</head>
<body class="stored">
<section class="mid_con">
    <div class="action">
        <span class="orderNum">订单号：<?php echo $order->order_no ?></span>
                <span class="orderPrice">
                	<em>¥ <?php echo $order->order_paymoney ?></em>
                	<em><?php echo empty($order->freight_money) || $order->freight_money == 0 ? '' : '(含运费¥ ' . $order->freight_money . ')' ?></em>
             	</span>
    </div>
</section>
<div class="title">支付方式</div>
<section class="mid_con">

    <div class="pay payClick" id="wx">
        <span class="img"><img src="<?php echo USER_STATIC_IMAGES; ?>user/wx.png"></span>
        <span class="name">微信支付</span>
        <span class="check"></span>
    </div>
</section>
<?php echo CHtml::beginForm(Yii::app()->createUrl('Dmall/wappay/ScWappay'), 'post', array('name' => 'scpay')); ?>
<input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id ?>">
<input type="hidden" name="order_no" value="<?php echo $order->order_no ?>"/>
<div class="btn">
    <input type="button" value="去结算" class="btn_com" style="width:80%;float:none;" id="wechat" onclick="callpay()">
</div>
<?php echo CHtml::endForm(); ?>
<script>
    $("#ali").click(function () {
        $(".mid_con .pay").removeClass("payClick");
        $(this).addClass("payClick");

        $('#wechat').hide();
        $('#alipay').show();
        return false;
    });

    $("#wx").click(function () {
        $(".mid_con .pay").removeClass("payClick");
        $(this).addClass("payClick");

        $('#wechat').show();
        $('#alipay').hide();
        return false;
    });

    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $jsApiParameters; ?>, //此处是json数据
            function (res) {
                //WeixinJSBridge.log(res.err_msg);
//                     alert(res.err_msg + ",ok");
                if (res.err_msg == "get_brand_wcpay_request:ok") { //支付成功后
                    window.location.href = "<?php echo Yii::app()->createUrl('Dmall/order/paySuccess', array('money' => $order->order_paymoney, 'ordertype' => 'SC', 'encrypt_id' => $encrypt_id));?>";
                    //跳转地址及订单操作,在异步页面也须处理订单,防止同步时失败
                } else {
                    if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                        alert("取消支付成功");
                    } else {
                        alert("支付失败");
                        //alert("支付失败"+res.err_code+res.err_desc+res.err_msg);
                    }
                }
            }
        );
    }

    function callpay() {
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        } else {
            jsApiCall();
        }
    }


</script>

</body>