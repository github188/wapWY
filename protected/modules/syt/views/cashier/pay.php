<script type="text/javascript" src="<?php echo SYT_STATIC_JS ?>numkeyboard.js"></script>

<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>artDialog/jquery.artDialog.js?skin=simple"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>artDialog/plugins/iframeTools.js"></script>
<div class="kkfm_r_inner cashierDesk">
	<div class="main-right">
		<div class="contant">
		    <div class="filed">
		         <span class="label">收款金额：</span>
		         <span class="text">
		            <!--这里做修改-->
		            <input type="text" id="amount" onkeydown="return onlyNum(this,event);" class="txt numkeyboard">
		            <!--这里做修改-->
		         </span>
		         <span class="text1" id="money_error">
		         </span>
		     </div>
		    <div class="filed">
		         <span class="label">会员手机：</span>
		         <span class="text"><input type="text" id="barCode" class="txt phoneTxt"></span>
		         <span class="text1" style="width: 200px;position: relative"><input type="submit" value="确认"  class="btn_com_gray" id="user_coupons">
		         	<div class="hd item" style="display: none">
					</div>
		         </span>
		     </div>
		     <div class="clavier">
		     </div>
		     <div class="way">
		      	<ul>
		        	<a href=""><li class="li0">支付宝条码</li></a>
		            <a href=""><li class="li1">支付宝扫码</li></a>
		            <a href=""><li class="li3 borderGreen">微信条码</li></a>
		            <a href=""><li class="li2 borderGreen">微信扫码</li></a>
		            <a href=""><li class="li4 borderOrange">现金支付</li></a>
		            <a href=""><li class="li5 borderOrange">银行卡支付</li></a>
		        </ul>
		     </div>
		</div>
	</div>
</div>

<script>

	$("#amount").numkeyboard({
		keyboardRadix:600,//键盘大小基数
		mainbackground:'#ffffff', //主背景色
		menubackground:'#4A81B0', //头背景色
		exitbackground:'#4376A0', //关闭按钮背景色
		buttonbackground:'#fd8b37', //键盘背景色
		topOffset:85, //与输入框的top偏移量
		leftOffset:'26%', //与输入框的left偏移量
		clickeve:true,//是否绑定元素click事件
		type:'money' //输入框类型
	});
	$("#amount").focus();
	
	//切换输入框
	$("#amount").focus(function() {
		$(this).numkeyboard({
			keyboardRadix:600,//键盘大小基数
			mainbackground:'#ffffff', //主背景色
			menubackground:'#4A81B0', //头背景色
			exitbackground:'#4376A0', //关闭按钮背景色
			buttonbackground:'#fd8b37', //键盘背景色
			topOffset:85, //与输入框的top偏移量
			leftOffset:'26%', //与输入框的left偏移量
			clickeve:true,//是否绑定元素click事件
			type:'money' //输入框类型
		});
	});
	$("#barCode").focus(function() {
		$(this).numkeyboard({
			keyboardRadix:600,//键盘大小基数
			mainbackground:'#ffffff', //主背景色
			menubackground:'#4A81B0', //头背景色
			exitbackground:'#4376A0', //关闭按钮背景色
			buttonbackground:'#fd8b37', //键盘背景色
			topOffset:26, //与输入框的top偏移量
			leftOffset:'26%', //与输入框的left偏移量
			clickeve:true,//是否绑定元素click事件
			type:'account' //输入框类型
		});
	});

	$(document).on('input onpropertychange', 'input', function() {
		inputChange($(this));
	});
	
	$("#user_coupons").click(function() {
		$("#money_error").html('');
		var amount = $("#amount").val();
		var undiscount = 0;
		var number = $("#barCode").val();
		if(amount > 99999999.99) {
			alert('输入的收款金额过大');
			return true;
		}
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/cashier/userCoupons'));?>',
            data: {account: number, money: amount, undiscount: undiscount},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                    var name = data.info.name;
                    var gname = data.info.gname;
                    if(gname != ''){
                        gname = '，'+gname;
                    }
                    var top = '<span class="hy-name">'+name+gname+'  '+data.info.vip+'</span>';
                    var middle = '<dl class="hb-con">';
                    $.each(data.list, function(i,v1) {
                        var element = 'dt';
                        if(v1.content.length > 1) {
                            middle += '<dt>'+v1.title+'</dt>';
                            element = 'dd';
                        }
                        $.each(v1.content, function(j,v2) {
                            var part = '<'+element+'> \
                                			<span class="label"> \
                                				<input type="checkbox" value="'+v2.id+'"> \
                                				<a href="javascript:;" value="'+v2.id+'">'+v2.name+'</a> \
                                	 		</span> \
                                	 		<span class="text">'+v2.expire+'</span> \
                                	 	</'+element+'>';
                    	 	middle += part;
                        });
                    });
                    if(data.list.length == 0) {
                        middle += '无可用优惠券';
                    }
                    middle += '</dl>';
                    var discount = '';
                    var attr1 = '';
                    if(data.info.discount == '1') {
                        discount = '无优惠折扣';
                        attr1 = 'disabled="true"';
                    }else {
                        discount = data.info.discount*10+'折';
                        attr1 = 'checked="true"';
                    }
                    var stored = '';
                    var attr2 = '';
                    if(data.info.money == '0') {
                        stored = '无可用余额';
                        attr2 = 'disabled="true"';
                    }else {
                        stored = '可用余额'+data.info.money;
                        attr2 = 'checked="true"';
                    }
                    var src = '<?php echo SYT_STATIC_IMAGES?>'+'ico.png';
					var bottom ='<dl> \
									<dt> \
										<span class="label"><input type="checkbox" value="u" '+attr1+'>会员折扣</span> \
										<span class="text">'+discount+'</span> \
									</dt> \
									<dt> \
										<span class="label"><input type="checkbox" value="m" '+attr2+'>储值支付</span> \
										<span class="text">'+stored+'</span> \
									</dt> \
								</dl> \
								<div class="hd-pay"> \
									<span class="momeny">还需实际支付金额<em class="orange fw">'+data.info.need.toFixed(2)+'</em>元</span> \
									<span>可得积分:'+data.info.points+'</span> \
								</div> \
								<div class="img"><img src="'+src+'"></div>';
					bottom += '<input type="hidden" value="'+data.info.money+'" id="stored">';
					bottom += '<input type="hidden" value="'+data.info.rule+'" id="rule">';
					bottom += '<input type="hidden" value="'+data.info.discount+'" id="discount">';
                    $(".hd").html(top+middle+bottom);
                    //$(".hd").show();
                    $(".hd").fadeIn(300, function() {
                        $(".hd").show();
                    });
                }else if(data.error == 'failure') {
                    if(data.errMsg == 'no_money') {
                        $("#money_error").html('请填写收款金额');
                    }else {
                        alert(data.errMsg);
                    }
                }
            }
        });
		return false;
	});

	//查看优惠券详情
	$(".hd").on('click', ' dl a', function() {
		var val = $(this).attr('value');
		art.dialog.open(
            	'<?php echo Yii::app()->createUrl('syt/cashier/couponsDetail');?>'+'?ucid='+val,
            	 {
                  	 title: '',
                  	 lock: true,
                  	 drag:true, 
                  	 id: 'dialog',
                  	 background: '#fff',
	                 resize: false
                 }
        );
	});

	//选择/取消优惠选项的触发事件
	$(".hd").on('click', ' dl input', function() {
		var obj = $(this);
		
// 		var id = '';
// 		if(obj.is(':checked')){
// 			id = obj.val();
// 		}
		
		var list = '';
		var money = $("#amount").val();
		var undiscount = 0;
		var stored = $("#stored").val();
		var rule = $("#rule").val();
		var discount = $("#discount").val();
		//遍历所有复选框
		$(".hd dl input").each(function() {
			//拼接所有已选中非储值的优惠券id
			if($(this).is(':checked')) {
				list += $(this).val() + ',';
			}
		});
		if(list != '') {
			list = list.substr(0, list.length - 1); //去掉最后一个逗号
		}
		//更新列表
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/cashier/updateCoupons'));?>',
            data: {list: list, money: money, undiscount: undiscount},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
	                var pay = data.need;
	                $(".hd dl input").each(function() {
		                if($(this).val() == 'u' && $(this).is(':checked')) {
		                	pay *= discount;
		                }
	                });
	                pay += undiscount;
	                var points = 0;
	                if(rule > 0) {
		                points = Math.floor(pay * rule);
	                }
	                $(".hd dl input").each(function() {
		                if($(this).val() == 'm' && $(this).is(':checked')) {
		                	if(stored < parseFloat(pay)) {
				                pay -= stored;
			                }else {
				                pay = 0;
			                }
		                }
	                });
	                
	                $(".momeny em").html(pay.toFixed(2));
	                $(".hd-pay span:eq(-1)").html('可得积分:'+points );
	                
                }else {
	                alert(data.errMsg);
	                obj.attr('checked', false);
                }
            }
        });
		
		return true;
	});

	$(".way a").click(function() {
		var cls = $(this).children('li').attr('class');
		var action = '';
		if (cls == 'li0') {
			action = '<?php echo ORDER_PAY_CHANNEL_ALIPAY_TM?>';
		}
		if (cls == 'li1') {
			action = '<?php echo ORDER_PAY_CHANNEL_ALIPAY_SM?>';
		}
		if (cls == 'li3 borderGreen') {
			action = '<?php echo ORDER_PAY_CHANNEL_WXPAY_TM?>';
		}
		if (cls == 'li2 borderGreen') {
			action = '<?php echo ORDER_PAY_CHANNEL_WXPAY_SM?>';
		}
		if (cls == 'li4 borderOrange') {
			action = '<?php echo ORDER_PAY_CHANNEL_CASH?>';
		}
		if (cls == 'li5 borderOrange') {
			action = '<?php echo ORDER_PAY_CHANNEL_UNIONPAY?>';
		}
		var money = $("#amount").val();
		var undiscount = 0;
		var account = $("#barCode").val();
		var list = '';
		$(".hd dl input").each(function() {
			if($(this).is(':checked')) {
				list += $(this).val() + ',';
			}
		});
		if(list != '') {
			list = list.substr(0, list.length - 1); //去掉最后一个逗号
		}

		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/cashier/createOrder'));?>',
            data: {action: action, money: money, undiscount: undiscount, account: account, list: list},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                	dialogOpen(action, data.order_no, data.need);
                }else {
                    alert(data.errMsg);
                }
            }
        });
		
		return false;
	});

	function dialogOpen(action,orderNo,need_pay) {
		if(need_pay == 0) {
			action = '<?php echo ORDER_PAY_CHANNEL_NO_MONEY?>';
			art.dialog.open(
	            	'<?php echo Yii::app()->createUrl('syt/cashier/payChannel');?>'+'?action='+action+'&orderNo='+orderNo,
	            	 {
	                  	 title: '',
	                  	 lock: true,
	                  	 drag:true, 
	                  	 id: 'dialog',
	                  	 background: '#fff',
	                  	 cancel:false,
		                 resize: false,
		                 close: function() {
			                 //获取共享数据：是否清除手机号
			                 var need_clear = art.dialog.data('clear');
				             //关闭前执行
			                 if(need_clear) {
				                 //清除会员手机号
				                 $("#barCode").val('');
			                 }
				             //清除金额、优惠券列表
		                	 inputChange();
		                	 $("#amount").val('');
		                	 //删除共享数据
			                 art.dialog.removeData('clear');
		                 }
	                 }
	        );
		}else {
			art.dialog.open(
	            	'<?php echo Yii::app()->createUrl('syt/cashier/payChannel');?>'+'?action='+action+'&orderNo='+orderNo,
	            	 {
	                  	 title: '',
	                  	 lock: true,
	                  	 drag:true, 
	                  	 id: 'dialog',
	                  	 background: '#fff',
	                  	 cancel:false,
		                 resize: false,
		                 close: function() {
			                 //获取共享数据：是否清除手机号
			                 var need_clear = art.dialog.data('clear');
				             //关闭前执行
			                 if(need_clear) {
				                 //清除会员手机号
				                 $("#barCode").val('');
			                 }
				             //清除金额、优惠券列表
		                	 inputChange();
		                	 $("#amount").val('');
		                	 //删除共享数据
			                 art.dialog.removeData('clear');
		                 }
	                 }
	        );
		}
	}
	
	
</script>
