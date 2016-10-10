<body class="stored">
<?php echo CHtml::beginForm(Yii::app()->createUrl('mobile/uCenter/wappay/wappay'), 'post', array('name' => 'wappay')); ?>
<section class="mid_con">
    <div class="action">
        <span class="name"><?php echo $model['productName']; ?></span>
        <span class="num">×<?php echo $model['num']; ?></span>
        <span class="price">¥<?php echo $model['recharge_money']; ?></span>
    </div>
</section>
<div class="title">支付方式</div>
<section class="mid_con" name="select_payway">
    <?php if (Yii::app()->session['source'] == 'alipay_wallet') { ?>
        <div class="pay payClick">
            <span class="img"><img src="<?php echo USER_STATIC_IMAGES; ?>user/zfb.png"></span>
            <span class="name">支付宝</span>
            <span class="check"></span>
        </div>
        <input type="text" name="pay" id="input_payway" style="display: none" value="1">
    <?php } elseif (Yii::app()->session['source'] == 'wechat') { ?>
        <div class="pay payClick">
            <span class="img"><img src="<?php echo USER_STATIC_IMAGES; ?>user/wx.png"></span>
            <span class="name">微信支付</span>
            <span class="check"></span>
        </div>
        <input type="text" name="pay" id="input_payway" style="display: none" value="2">
    <?php } else { ?>
        <div class="pay payClick">
            <span class="img"><img src="<?php echo USER_STATIC_IMAGES; ?>user/zfb.png"></span>
            <span class="name">支付宝</span>
            <span class="check"></span>
        </div>
        <div class="dline"></div>
        <div class="pay">
            <span class="img"><img src="<?php echo USER_STATIC_IMAGES; ?>user/wx.png"></span>
            <span class="name">微信支付</span>
            <span class="check"></span>
        </div>
        <input type="text" name="pay" id="input_payway" style="display: none" value="1">
    <?php } ?>

</section>
<script>
    $(".mid_con .pay").click(
        function () {
            $(".mid_con .pay").removeClass("payClick");
            $(this).addClass("payClick");
            if ($("#input_payway").val() == 1) {
                document.getElementById("input_payway").value = "2";
                $('#wechat').show();
                $('#alipay').hide();
            }
            else {
                document.getElementById("input_payway").value = "1";
                $('#wechat').hide();
                $('#alipay').show();
            }
        }
    );
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php if(!empty($jsApiParameters)) {
                echo $jsApiParameters . ','; //此处是json数据
            } ?>
            function (res) {
                //WeixinJSBridge.log(res.err_msg); 
                //alert(res.err_msg + ",ok");
                //alert(<?php //echo $model['recharge_money']?>);
                if (res.err_msg == "get_brand_wcpay_request:ok") { //支付成功后
                    window.location.href = "<?php echo Yii::app()->createUrl('mobile/uCenter/user/paySuccess', array('money' => $model['recharge_money'], 'encrypt_id' => $encrypt_id));?>";
                    //跳转地址及订单操作,在异步页面也须处理订单,防止同步时失败      
                } else {
                    //alert("支付失败" + res.err_code + res.err_desc + res.err_msg);
                    alert("支付失败");
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
<div class="btn"><?php if (Yii::app()->session['source'] == 'alipay_wallet') { ?>
        <input type="submit" value="去结算" class="btn_com" style="width:80%;float:none" id="alipay">
    <?php } elseif (Yii::app()->session['source'] == 'wechat') { ?>
        <input type="button" value="去结算" class="btn_com" style="width:80%;float:none;" id="wechat" onclick="callpay()">
    <?php } else { ?>
        <input type="submit" value="去结算" class="btn_com" style="width:80%;float:none" id="alipay">
        <input type="button" value="去结算" class="btn_com" style="width:80%;float:none;display:none" id="wechat"
               onclick="callpay()">
    <?php } ?>
</div>
<input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id ?>"/>
<?php echo CHtml::endForm(); ?>
</body>
