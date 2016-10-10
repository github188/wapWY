<script type="text/javascript">
	$(document).ready(main_obj.list_init);
</script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>artDialog/jquery.artDialog.js?skin=simple"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>artDialog/plugins/iframeTools.js"></script>
<div class="kkfm_r_inner">
	<div class="main-right">
            <div class="status-nav">
                <ul>
                    <li class="cur"><a href="<?php echo $this->createUrl('Charge')?>">会员储值</a></li>
                    <li class=""><a href="<?php echo $this->createUrl('MemberStoredList')?>">储值记录</a></li>
                </ul>
            </div>
            <div class="stored">
                <div class="filed">
                	<span class="label">会员账号</span>
                    <span class="text"><input class="txt" type="text" name="account" id="tel" value="<?php echo isset($_POST['tel']) ? $_POST['tel'] : ''?>"  style="height:30px;width:200px;"></span>
                    <span class="text1" style="color:red" id="account_error"></span>
                    <span class="text" id="usersearch"></span>
                </div>
                <div class="filed">
                	<span class="label">储值活动</span>
                     <span class="text">
                     	<?php echo CHtml::dropDownList('stored_id', '', $list, array('class' => 'txt', 'style' => 'width:210px;height:40px'))?>
                     </span>
                     <span class="text1" style="color:red" id="stored_error">
                    </span>
                 </div>
                 <div class="filed">
                     <span class="label">购买数量</span>
                     <span class="text">
                            <input type="text" name="num" value="1" id="qty_item_1" class="txt" placeholder="购买的数量" style="height:30px;width:200px;"/>                                           	
                     </span>
                     <span class="text1" style="color:red" id="num_error"></span>
                 </div>
                 <div class="filed">
                 	<span class="label">储值总计</span>
                    <span class="text">
                    	<input type="text" id="des" class="txt" readonly="readonly" style="height:30px;width:200px;" >
                    </span>
                    <span class="text1"></span>
                 </div>
                 <div class="filed">
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
                     <span class="text"></span>
                 </div>
            </div>
        </div>
</div>
<script>
	$("#stored_id").change(function() {
		if($(this).val() == 0) {
			$("#des").val('');
		}else {
			amount();
		}
	});
	$("#qty_item_1").keyup(function(event) {
		var val = $(this).val();
		var isNumber = val.search(new RegExp("^[0-9]+$"));
		if(isNumber == -1) {
			val = val.replace(/[^0-9]+/g, '');
			$(this).val(val);
		}else if(val.charAt(0) == '0') {
			val = val.substr(1);
			$(this).val(val);
		}
	});
	$("#qty_item_1").blur(function() {
		//输入内容正则验证
		var val = $(this).val();
		var isNumber = val.search(new RegExp("^[0-9]+$"));
		if(isNumber == -1 || !val) {
			$(this).val('1');
		}
		amount();
	});
	function amount() {
		var sid = $("#stored_id").val();
		var num = $("#qty_item_1").val();
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/memberStored/amount'));?>',
            data: {sid: sid, num: num},
            type: 'get',
            success: function (data) {
                $("#des").val(data);
            }
        });
	}

	//输入手机号显示会员信息    
	$("#tel").on('input',function(e){ 
	    search();
	});
	//输入手机号显示会员信息   
	function search()
	{
	        $.ajax({
	            type : 'GET',
	            url : '<?php echo Yii::app()->createUrl('syt/MemberStored/UserSearch')?>',
	            data : {tel : $('#tel').val()},
	            success : function(data){
	                //解析data                
	                ajaxobj=eval("("+data+")"); 
	                
	                if(ajaxobj.status == 'ERROR_NONE')
	                {
		                name = '';
		                gname = '';
		                if(ajaxobj.name != '' && ajaxobj.name) {
			                name = ajaxobj.name;
		                }
		                if(ajaxobj.grade_name != '' && ajaxobj.grade_name) {
			                if(name != '') {
			                	gname = ';'+ajaxobj.grade_name;
			                }else {
			                	gname = ajaxobj.grade_name;
			                }
		                }
	                    $('#usersearch').html(name+gname);
	                } else {
	                    $('#usersearch').html('');
	                }
	                if(ajaobj.status == 'ERROR_NO_DATA')
	                {                    
	                    $('#usersearch').html(ajaxobj.errMsg);
	                } else {
	                    $('#usersearch').html('');
	                }
	            }
	        });
	}
	
	$(".way a").click(function() {
		//清除错误提示
		$("#account_error").html('');
		$("#stored_error").html('');
		$("#num_error").html('');
		
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
		var account = $("input[name=account]").val();
		var stored_id = $("#stored_id").val();
		var num = $("#qty_item_1").val();
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/memberStored/createStoredOrder'));?>',
            data: {account: account, stored_id: stored_id, num: num, channel: action},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                	dialogOpen(action, data.order_no);
                }else {
                    if(data.errMsg != '') {
                        alert(data.errMsg);
                    }else {
                    	$("#account_error").html(data.account_error);
                		$("#stored_error").html(data.stored_error);
                		$("#num_error").html(data.num_error);
                    }
                }
            }
        });
		
		return false;
	});

	function dialogOpen(action,orderNo) {
		art.dialog.open(
            	'<?php echo Yii::app()->createUrl('syt/memberStored/payChannel');?>'+'?action='+action+'&orderNo='+orderNo,
            	 {
                  	 title: '',
                  	 lock: true,
                  	 drag:true, 
                  	 id: 'dialog',
                  	 background: '#fff',
                  	 cancel:false,
	                 resize: false,
	                 close: function() {
		                 //获取共享数据：是否清除输入
		                 var need_clear = art.dialog.data('clear');
			             //关闭前执行
		                 if(need_clear) {
			                 $("#tel").val('');
			                 $("#stored_id").val(0);
			                 $("#qty_item_1").val(1);
			                 $("#des").val('');
			                 $("#usersearch").text('');
		                 }
	                	 //删除共享数据
		                 art.dialog.removeData('clear');
	                 }
                 }
        );
	}
	
</script>

