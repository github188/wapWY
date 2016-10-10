
<div class="consume_wrap">
	<div class="consume_money">
		<label>消费金额：</label>
		<input id="money" type="number" name="consume_money" placeholder="请询问服务员后输入" style="ime-mode:disabled;" onpaste="return false;" onkeydown="onlyNum(this,event);">
	</div>
	<button class="confirm_pay" disabled="disabled" onclick="order()">确认支付</button>
</div>
<script>
	$("#money").on('input propertychange', function() {
		var money = $(this).val();
		if(money > 0) {
			$(".confirm_pay").attr("disabled", false);
		}else {
			$(".confirm_pay").attr("disabled", true);
		}
	});

	//全局变量:JSAPI支付参数
	var parameters = null;
	//全局变量:订单号
	var order_no = null;

	function jsApiCall(){
	    WeixinJSBridge.invoke(
	        'getBrandWCPayRequest',
	        parameters, //此处是json数据
	        function(res){
	            //WeixinJSBridge.log(res.err_msg); 
				//alert(res.err_msg + ",ok");
	            if(res.err_msg == "get_brand_wcpay_request:ok") { //支付成功后
	            	payDone();
	            	//跳转地址及订单操作,在异步页面也须处理订单,防止同步时失败    
	            }else{
	                if(res.err_msg == 'get_brand_wcpay_request:cancel'){
	                    //alert("取消支付成功");
	                }else{
	                    alert("支付失败");
	                }
	            } 
	        }
	    );
	}
	
	function callpay()
	{
		//唤起微信支付
	    if (typeof WeixinJSBridge == "undefined"){
	        if( document.addEventListener ){
	            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
	        }else if (document.attachEvent){
	            document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
	            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
	        }
	    }else{
	        jsApiCall();
	    }
	}

	//下单
	function order() {
		var money = $("#money").val();
		//创建订单并获取支付参数
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/wap/createOrder'));?>',
            data: {money: money},
            type: 'get',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                    order_no = data.orderNo;
                	parameters = $.parseJSON(data.jsParams);
                	callpay();
                }else {
                	alert(data.errMsg);
                }
            }
        });
	}

	//成功
	function payDone() {
		$("#money").val(''); //清空输入框
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/wap/paySuccess'));?>',
            data: {order_no: order_no}
        });
	}
	
</script>


