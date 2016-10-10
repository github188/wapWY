<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>LodopFuncs.js"></script>
<div class="kkfm_r_inner">
    <div class="main-right">
        <div class="status-nav">

        </div>
        <?php echo CHtml::beginForm()?>
        <div class="stored">
            <div class="filed">
                <span class="label">退款账号</span>
                <span class="text"><?php echo !empty($model['alipay_account']) ? $model['alipay_account'] : $model['account']; ?></span>
            </div>
            <div class="filed">
                <span class="label">订单号</span>
                <span class="text"><?php echo $model['order_no'] ?></span>
                <input type="hidden" value="<?php echo $model['order_no'] ?>" name="Refund[order_no]">
            </div>
            <div class="filed">
                <span class="label">订单金额</span>
                <span class="text"><?php echo $model['money'] ?></span>
            </div>
            <div class="filed">
                <span class="label">退款金额</span>
                <span class="text">
                	<?php $refundable = isset($_POST['money']) ? $_POST['money'] : ($model['money']-$model['unionpay_paymoney']-$refund_money > 0 ? $model['money']-$model['unionpay_paymoney']-$refund_money : '0');?>
                	<input class="txt" type="text" name="Refund[money]" onkeydown="onlyNum(this,event);" value="<?php echo $refundable?>"  style="height:30px;width:200px;" placeholder="请输入退款金额" >
                </span>
                <span class="text1" style="color:red">
                    <?php if(Yii::app()->user->hasFlash('money')){
                        echo Yii::app()->user->getFlash('money');
                    } ?>
                </span>
            </div>
            <?php if ($need_pwd) { ?>
                <div class="filed">
                    <span class="label">管理员密码</span>
                    <span class="text"><input class="txt" type="password" name="Refund[pwd]" value=""  style="height:30px;width:200px;"></span>
                    <span class="text1" style="color:red">
                        <?php if(Yii::app()->user->hasFlash('pwd')){
                            echo Yii::app()->user->getFlash('pwd');
                        } ?>
                    </span>
                </div>
            <?php } ?>
            <div class="filed">
                <span class="label" style="padding-left:11%"><?php echo CHtml::submitButton('确认退款',array('class'=>'btn_com_blue'))?></span>
                <span class="text"></span>
            </div>
        </div>
    </div>
    <?php echo CHtml::endForm()?>
</div>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop.exe"></embed>
</object>

<script>
    function doPrint(callback) {
    	var orderNo = '<?php echo isset($_POST['Refund']['order_no']) ? $_POST['Refund']['order_no'] : ''?>';
    	$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/cashier/orderInfo'));?>',
            data: {orderNo: orderNo},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {                    
                    var image = '<?php echo SYT_STATIC_IMAGES.'wqlogo.png';?>';
                    var mName = data.m_name;
                    var sName = data.s_name;
                    var operator = data.operator;
                    var printname = data.print_name;
                    var orderNo = data.order_no;
                    var type = "退款";
                    var status = data.status;
                    var total = data.total;
                    var refund = '<?php echo $money?>';
                    var time = '<?php echo $time?>';
                    var account = data.account ? data.account : '';
                    var status = data.status;
                    var trade_no = data.trade_no ? data.trade_no : '';
                	printMerchant(image,mName,sName,operator,orderNo,type,status,total,refund,time,account,status,trade_no,printname);
                }else {
                	alert(data.errMsg);
                }
                callback();
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
        LODOP.ADD_PRINT_TEXT(171+x+y,0,198,20,"买家支付账号 "+account);
        LODOP.ADD_PRINT_TEXT(186+x+y,0,198,20,"交易类型 "+type);
        LODOP.ADD_PRINT_TEXT(202+x+y,0,198,20,"交易状态 "+status);
        LODOP.ADD_PRINT_TEXT(218+x+y,0,198,20,"退款金额");
        var curY = 218+x+y;
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
        LODOP.ADD_PRINT_TEXT(curY+25,0,198,20,"退款时间 "+time);
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
        LODOP.ADD_PRINT_TEXT(171+x+y,0,198,20,"买家支付账号 "+account);
        LODOP.ADD_PRINT_TEXT(186+x+y,0,198,20,"交易类型 "+type);
        LODOP.ADD_PRINT_TEXT(202+x+y,0,198,20,"交易状态 "+status);
        LODOP.ADD_PRINT_TEXT(218+x+y,0,198,20,"退款金额");
        var curY = 218+x+y;
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
        LODOP.ADD_PRINT_TEXT(curY+25,0,198,20,"退款时间 "+time);
        LODOP.ADD_PRINT_TEXT(curY+42,0,198,20,"第三方交易号 "+trade_no);
        LODOP.SET_PRINT_STYLEA(0,"FontSize",6);//打印样式字体大小
        LODOP.ADD_PRINT_LINE(curY+56,0,curY+56,190,0,1);
        LODOP.ADD_PRINT_TEXT(curY+60,0,255,20,"谢谢惠顾!");
               
        LODOP.PRINT();
	}
	$(document).ready(function() {
		<?php if (Yii::app()->user->hasFlash('success')) { ?>
			var msg = '<?php echo Yii::app()->user->getFlash('success');?>';
			alert(msg);
			//检查打印控件安装状态
			var LODOP = getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'),'title');
			if(LODOP==null || typeof(LODOP.VERSION)=="undefined") {
				location.href="<?php echo Yii::app()->createUrl('syt/trading/tradingList') ?>";
			}else {
				var if_print = '<?php echo $model['is_print'];?>';
				if (if_print == '<?php echo PRINT_YES?>') {
					doPrint(function() {
			            location.href="<?php echo Yii::app()->createUrl('syt/trading/tradingList') ?>";
			        });
				}
			}
		<?php }elseif (Yii::app()->user->hasFlash('error')){ ?>
			alert('<?php echo Yii::app()->user->getFlash('error');?>');
		<?php }?>
	});
	
</script>