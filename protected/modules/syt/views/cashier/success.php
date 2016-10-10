<div class="popWrap" id="pop" style="width:300px">
    <div class="pop_con" style="text-align:inherit">
        <div class="pop_content">
           <div class="title">提示</div>
           <div class="con">支付成功</div>
           <div class="btn"><input class="btn-border" value="确定" type="submit" id="close_dialog"></div>  
        </div>
    </div>   
</div>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop.exe"></embed>
</object> 
<script>
$("#close_dialog").click(function() {
	//利用共享数据来设置是否清除页面输入
	art.dialog.data('clear', true);
	art.dialog.close();
});
$(document).ready(function() {      
	art.dialog.open.api.size(300,150); //重新设置大小 
	<?php if ($model['is_print'] == PRINT_YES) { ?>
		print();
	<?php }?>
	
});
    function print() {
    	var orderNo = '<?php echo $_GET['orderNo']?>';
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
        LODOP.ADD_PRINT_IMAGE(18,50,170,30,"<img src="+image+" width='' height='30'>");
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
//         LODOP.PRINT_DESIGN();
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
        LODOP.ADD_PRINT_IMAGE(18,50,170,30,"<img src="+image+" width='' height='30'>");        
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
</script>