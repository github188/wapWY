
<script type="text/javascript">
	$(document).ready(main_obj.list_init);
</script>

<div class="kkfm_r_inner business">
    <div class="top">
        <div class="name">交易详情：
        <!-- 如果订单状态是正常  则交易详情 显示支付状态      否则显示订单状态-->
        <?php if($list['order_status'] == ORDER_STATUS_NORMAL){ ?>
             <?php echo isset($list['pay_status'])?$GLOBALS['ORDER_STATUS_PAY'][$list['pay_status']]:''; ?>
         <?php }else{?>
             <?php echo isset($list['order_status'])?$GLOBALS['ORDER_STATUS'][$list['order_status']]:''; ?>
         <?php }?>
        </div>
         <?php if($list['pay_status'] == ORDER_STATUS_PAID && ($list['order_status'] == ORDER_STATUS_PART_REFUND || $list['order_status'] == ORDER_STATUS_NORMAL)){ ?>
         
        <div class="cz"><input type="button" value="打印" class="btn_com_gray" id="print" style="margin-right: 20px"><input type="button" value="退款" class="btn_com_gray" onclick="location.href='<?php echo Yii::app()->createUrl('syt/trading/refund', array('id' => $list['id'])) ?>'"></div>
        <?php }elseif (
        		$list['pay_status'] == ORDER_STATUS_UNPAID && 
				(
					$list['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM ||
					$list['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM ||
					$list['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM ||
					$list['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM
				)) {?>
				<div class="cz"><input type="button" value="查询" class="btn_com_gray" onclick="research(<?php echo $list['order_no']?>)" style="margin-right: 20px"></div>
		<?php }?>
    </div>
	<div class="title">
    	<span>订单号：<?php echo $list['order_no']; ?></span>
        <span>商户名称：<?php echo $list['merchant_name']; ?></span>
        <span>操作员：<?php echo $list['operator_name']; ?></span>
    </div>
    <div class="business-list">
    	<div class="name">
        	<span>订单金额</span>
            <span>优惠金额</span>
            <span>退款</span>
            <span>实收金额</span>
        </div>
        <div class="vaule">
        	<span><?php echo $list['order_paymoney']; ?></span>
            <span><?php echo $list['yhq_cash']; ?> </span>
            <span><?php echo $list['refund_cash']; ?></span>
            <span class="ssje"><?php echo $list['paid_amount']; ?></span>
        </div>
    </div>
    <div class="title">
    	<span>支付详情</span>
        <span>会员账号：<?php echo $list['user_account']; ?></span>
        <span>本次积分：<?php echo $list['points']; ?></span>
    </div>
    <div class="business-con">
    	<div class="con-left">
        	<div class="filed">
            	<span class="label">支付方式</span>
                <span class="text">
               <?php if($list['stored_paymoney']!=0){ ?> <?php echo $list['stored_paymoney']; ?>（储值）<br><?php }?>
               <?php if($list['online_paymoney']!=0){ ?> <?php echo $list['online_paymoney']; ?>（<?php echo isset($list['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$list['pay_channel']]:''; ?>）<br><?php }?>
               <?php if($list['unionpay_paymoney']!=0){ ?> <?php echo $list['unionpay_paymoney']; ?>（银联刷卡）<br><?php }?>
               <?php if($list['cash_paymoney']!=0){ ?> <?php echo $list['cash_paymoney']; ?>（现金）<br><?php }?>
                </span>
            </div>
            <?php if(!empty($list['alipay_account'])){ ?>
            <div class="filed">
            	<span class="label">用户支付宝账号</span>
                <span class="text"><?php echo $list['alipay_account']; ?></span>
            </div>
            <?php }?>
            <?php if(!empty($list['trade_no'])){ ?>
            <div class="filed border">
            	<span class="label">第三方交易号</span>
                <span class="text"><?php echo $list['trade_no']; ?></span>
            </div>
            <?php }?>
            <div class="filed">
            	<span class="label">下单时间</span>
                <span class="text"><?php echo $list['create_time'];?></span>
            </div>
            <div class="filed">
            	<span class="label">支付时间</span>
                <span class="text"><?php echo $list['pay_time']; ?></span>
            </div>
        </div>
        <div class="con-right">
        	<div class="filed">
            	<span class="label">优惠详情</span>
                <span class="text">
                  <?php if($list['hongbao_money']!=0) {?>红    包: <?php echo $list['hongbao_money']; ?><br><?php }?>   
                  <?php if($list['coupons_money']!=0) {?> 优惠券 : <?php echo $list['coupons_money']; ?><br><?php }?>
                  <?php if($list['discount_money']!=0) {?>会员 折扣 : <?php echo $list['discount_money']; ?><?php }?>
                   </span>
            </div>
        </div>
    </div>
     <?php if($list['order_status'] == ORDER_STATUS_REFUND || $list['order_status'] == ORDER_STATUS_PART_REFUND){ ?>
    <div class="title">
    	<span>退款信息</span>
        <span>退款笔数<?php echo $list['refund_count']; ?></span>
        <span>退款金额<?php echo $list['refund_cash']; ?></span>
    </div>
    <div class="business-list tk-list">
    	<div class="name">
        	<span>退款操作员</span>
            <span>授权管理员</span>
            <span>退款金额</span>
            <span>退款时间</span>
        </div>
        
       <?php foreach ($list['refund_record'] as $key=>$value){ ?>
        <div class="vaule dline">
        	<span><?php echo $value['operator_name'];?></span>
            <span><?php echo $value['operator_admin_name'];?></span>
            <span><?php echo $value['refund_money'];?></span>
            <span><?php echo $value['refund_time'];?></span>
        </div>
       <?php }?>
        <?php }?>
     </div>
</div> 
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop.exe"></embed>
</object>
<script>
	$("#print").click(function() {
		print();
	});
	function print() {
		var orderNo = '<?php echo $list['order_no'];?>';
		$.ajax({
	        url: '<?php echo(Yii::app()->createUrl('syt/cashier/orderInfo'));?>',
	        data: {orderNo: orderNo},
	        type: 'post',
	        dataType: 'json',
	        success: function (data) {
	            if(data.error == 'success') {                    
	                var image = '<?php echo SYT_STATIC_IMAGES.'wqlogo.png' ?>';
	                var mName = data.m_name;
	                var sName = data.s_name;
	                var operator = data.operator;
	                var printname = data.print_name;
	                var orderNo = data.order_no;
	                var type = data.type;
	                var status = data.status;
	                var total = data.total;
	                var money = data.paymoney;
	                var time = data.pay_time;
	                var account = data.account ? data.account : '';
	                var status = data.status;
	                var trade_no = data.trade_no ? data.trade_no : '';
	            	printMerchant(image,mName,sName,operator,orderNo,type,status,total,money,time,account,status,trade_no,printname);
	            }else {
	            	alert(data.errMsg);
	            }
	        }
	    });
	}
	//商户存根打印
	function printMerchant(image,mName,sName,operator,orderNo,type,status,total,money,time,account,status,trade_no,printname) {
		
	    LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
	    LODOP.PRINT_INIT("商户存根打印");
	    LODOP.SET_PRINTER_INDEXA(printname);        
	    LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');//打印字体型号
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",8);//打印样式字体大小
	    LODOP.SET_PRINT_STYLEA(0,"Alignment",2);//居中        
	    LODOP.ADD_PRINT_IMAGE(18,-5,170,30,"<img src="+image+" width='100%' height='30'>");        
	    LODOP.ADD_PRINT_TEXT(74,0,100,20,"商户存根");
	    LODOP.ADD_PRINT_LINE(88,0,89,190,0,1);
	    LODOP.ADD_PRINT_TEXT(73,96,100,20,"MERCHANT COPY");
	    LODOP.ADD_PRINT_LINE(71,0,72,190,0,1);
	    var x = 0;
	    if(mName.length > 9){
	    	LODOP.ADD_PRINT_TEXT(94,0,198,20,"商户名称 "+mName.substr(0,9));
	    	x += 16;
	    	LODOP.ADD_PRINT_TEXT(94+x,50,198,20,mName.substr(9));
	    }else{
	    	LODOP.ADD_PRINT_TEXT(94+x,0,198,20,"商户名称 "+mName);
	    }
	    var y = 0;
	    if(sName.length > 9){
	    	LODOP.ADD_PRINT_TEXT(110+x,0,198,20,"门店名称 "+sName.substr(0,9));
	    	y += 16;
	    	LODOP.ADD_PRINT_TEXT(110+x+y,50,198,20,sName.substr(9));
	    }else{
	        LODOP.ADD_PRINT_TEXT(110+x+y,0,198,20,"门店名称 "+sName);
	    }
	    
	    LODOP.ADD_PRINT_TEXT(126+x+y,0,198,20,"终端编号");
	    LODOP.ADD_PRINT_TEXT(140+x+y,0,198,20,"操 作 员 "+operator);
	    LODOP.ADD_PRINT_TEXT(156+x+y,0,198,20,"订 单 号 "+orderNo);
	    LODOP.ADD_PRINT_TEXT(171+x+y,0,198,20,"支付账号 "+account);
	    LODOP.ADD_PRINT_TEXT(186+x+y,0,198,20,"交易类型 "+type);
	    LODOP.ADD_PRINT_TEXT(202+x+y,0,198,20,"交易状态 "+status);
	    LODOP.ADD_PRINT_TEXT(218+x+y,0,198,20,"订单金额 "+total);
	    LODOP.ADD_PRINT_TEXT(234+x+y,0,198,20,"交易金额");
	    var curY = 234+x+y;
	    if(money.length > 8) {
	        LODOP.ADD_PRINT_TEXT(curY+12,55,123,20*2+5,"RMB "+money);
	        LODOP.SET_PRINT_STYLEA(0,"Alignment",1);
	        curY += 20;
	    }else {
	    	LODOP.ADD_PRINT_TEXT(curY+12,55,123,20,"RMB "+money);
	        LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
	    }
	    curY += 5;
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",11);//打印样式字体大小
	    LODOP.SET_PRINT_STYLEA(0,"Bold",1);//加粗
	    LODOP.ADD_PRINT_TEXT(curY+25,0,198,20,"交易时间 "+time);
	    LODOP.ADD_PRINT_TEXT(curY+42,0,198,20,"第三方交易号 "+trade_no);
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",6);//打印样式字体大小
	    LODOP.ADD_PRINT_TEXT(curY+56,0,199,20,"客户签名（CUSTOMER SIGNATURE）");
	    LODOP.ADD_PRINT_LINE(curY+98,0,curY+98,190,0,1);
	    LODOP.ADD_PRINT_TEXT(curY+102,0,197,20,"本人已确认交易,对交易无任何交易纠纷意见");
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",7);
	    LODOP.ADD_PRINT_TEXT(curY+112,0,196,45,"I ACKONOWLEDGE SATISFACTORY RECEIPT OF RELATIVE GOODS/SERVICE");
	    LODOP.SET_PRINT_STYLEA(0,"FontName","@Arial Unicode MS");
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",5);//打印样式字体大小
	    
	    LODOP.PRINT();
	    //alert('确定打印客户存根');
	    if(confirm("确定打印客户存根")) {
	    	printSuccess(image,mName,sName,operator,orderNo,type,status,total,money,time,account,status,trade_no,printname);
	    }      
	}
	//客户存根打印
	function printSuccess(image,mName,sName,operator,orderNo,type,status,total,money,time,account,status,trade_no,printname) {
		
	    LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
	    LODOP.PRINT_INIT("客户存根打印"); 
	    LODOP.SET_PRINTER_INDEXA(printname);        
	    LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');//打印字体型号
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",8);//打印样式字体大小
	    LODOP.SET_PRINT_STYLEA(0,"Alignment",2);//居中        
	    LODOP.ADD_PRINT_IMAGE(18,-5,170,30,"<img src="+image+" width='100%' height='30'>");        
	    LODOP.ADD_PRINT_TEXT(74,0,100,20,"客户存根");
	    LODOP.ADD_PRINT_LINE(88,0,89,190,0,1);
	    LODOP.ADD_PRINT_TEXT(73,96,100,20,"MERCHANT COPY");
	    LODOP.ADD_PRINT_LINE(71,0,72,190,0,1);
	    var x = 0;
	    if(mName.length > 9){
	    	LODOP.ADD_PRINT_TEXT(94,0,198,20,"商户名称 "+mName.substr(0,9));
	    	x += 16;
	    	LODOP.ADD_PRINT_TEXT(94+x,50,198,20,mName.substr(9));
	    }else{
	    	LODOP.ADD_PRINT_TEXT(94+x,0,198,20,"商户名称 "+mName);
	    }
	    var y = 0;
	    if(sName.length > 9){
	    	LODOP.ADD_PRINT_TEXT(110+x,0,198,20,"门店名称 "+sName.substr(0,9));
	    	y += 16;
	    	LODOP.ADD_PRINT_TEXT(110+x+y,50,198,20,sName.substr(9));
	    }else{
	        LODOP.ADD_PRINT_TEXT(110+x+y,0,198,20,"门店名称 "+sName);
	    }
	    LODOP.ADD_PRINT_TEXT(126+x+y,0,198,20,"终端编号");
	    LODOP.ADD_PRINT_TEXT(140+x+y,0,198,20,"操 作 员 "+operator);
	    LODOP.ADD_PRINT_TEXT(156+x+y,0,198,20,"订 单 号 "+orderNo);
	    LODOP.ADD_PRINT_TEXT(171+x+y,0,198,20,"支付账号 "+account);
	    LODOP.ADD_PRINT_TEXT(186+x+y,0,198,20,"交易类型 "+type);
	    LODOP.ADD_PRINT_TEXT(202+x+y,0,198,20,"交易状态 "+status);
	    LODOP.ADD_PRINT_TEXT(218+x+y,0,198,20,"订单金额 "+total);
	    LODOP.ADD_PRINT_TEXT(234+x+y,0,198,20,"交易金额");
	    var curY = 234+x+y;
	    if(money.length > 8) {
	        LODOP.ADD_PRINT_TEXT(curY+12,55,123,20*2+5,"RMB "+money);
	        LODOP.SET_PRINT_STYLEA(0,"Alignment",1);
	        curY += 20;
	    }else {
	    	LODOP.ADD_PRINT_TEXT(curY+12,55,123,20,"RMB "+money);
	        LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
	    }
	    curY += 5;
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",11);//打印样式字体大小
	    LODOP.SET_PRINT_STYLEA(0,"Bold",1);//加粗
	    LODOP.ADD_PRINT_TEXT(curY+25,0,198,20,"交易时间 "+time);
	    LODOP.ADD_PRINT_TEXT(curY+42,0,198,20,"第三方交易号 "+trade_no);
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",6);//打印样式字体大小
	    LODOP.ADD_PRINT_LINE(curY+56,0,curY+56,190,0,1);
	    LODOP.ADD_PRINT_TEXT(curY+60,0,255,20,"谢谢惠顾!");       
	    LODOP.PRINT();
	}

	function research(order_no) {
		$.ajax({
	        url: '<?php echo(Yii::app()->createUrl('syt/trading/research'));?>',
	        data: {order_no: order_no},
	        type: 'post',
	        dataType: 'json',
	        success: function (data) {
	            if(data.error == 'success') {                    
	                location.reload();
	            }else {
		            if (data.errMsg != '') {
			            alert(data.errMsg);
		            }
	            }
	        }
	    });
	}
</script>