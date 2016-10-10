<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <title> 微信安全支付</title>

    <script type="text/javascript">

        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                <?php echo $jsApiParameters; ?>,

                function (res) {
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code + res.err_desc + res.err_msg);
                    if (res.err_msg == "get_brand_wcpay_request:ok") {
                        window.location.href = '<?php echo $goUrl;?>';
                    }

                    if (res.err_msg == "get_brand_wcpay_request:cancel") {
                        window.location.href = '<?php echo Yii::app()->request->urlReferrer;?>';
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
        window.onload = callpay();
    </script>
</head>