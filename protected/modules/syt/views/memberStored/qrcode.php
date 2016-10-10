<div class="popWrap" id="pop" style="width:380px;padding:0px 0px 10px 0px;">
    <div class="pop_con" style="text-align:inherit">
		<div class="pop_content">
			<div class="title" style="font-size:12px;font-weight: normal;">
				<?php echo $msg ?>
			</div>
			<div class="con next">
				<?php if (Yii::app()->user->hasFlash('error')) {
					echo Yii::app()->user->getFlash('error');
				}else {?>
					<img src="<?php echo $img ?>" style="width: 256px;height: 256px">
				<?php }?>
			</div>
			<input type="hidden" value="" id="lodop_url">
			<input class="btn-border" value="打印二维码" type="submit" style="width:45%;float:left" onclick="print()">
			<input class="btn-border" value="取消" type="submit" style="width:45%;float:right" id="close_dialog">
		</div>
	</div>  
</div>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop.exe"></embed>
</object> 
<script>
	$("#close_dialog").click(function() {
		art.dialog.close();
	});
	
	$(function() {
		//检查打印设置
		var print = '<?php echo $print?>';
		if(print == '<?php echo PRINT_YES?>') {
			//检查打印控件安装状态
			var LODOP = getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'),'title');
			if(LODOP==null || typeof(LODOP.VERSION)=="undefined") {
				//禁用打印按钮
				$(".btn-border:eq(0)").attr('disabled', true);
			}
		}else {
			$(".title").html('如需打印二维码,请在系统设置中启用打印机');
			//禁用打印按钮
			$(".btn-border:eq(0)").attr('disabled', true);
		}
		//启动轮询
		run();
	});
	//轮询    
    function run(){        
        intervalid = setInterval("ctrlTime()", 5000);        
    }
    /*主函数要使用的函数，进行声明*/ 
    var clock=new clock();  
    /*指向计时器的指针*/  
    var timer;  
    function clocks()
    { 
        clearInterval(timer);
        /*主函数就在每1秒调用1次clock函数中的move方法即可*/ 
        timer=setInterval("clock.move()",1000); 
    }  

    function clock()
    {  
        /*s是clock()中的变量，非var那种全局变量，代表剩余秒数*/  
        this.s=10;  
        this.move=function()
        {  
            /*输出前先调用exchange函数进行秒到分秒的转换，因为exchange并非在主函数window.onload使用，因此不需要进行声明*/ 
            //document.getElementById("timer").innerHTML=exchange(this.s);  
            /*每被调用一次，剩余秒数就自减*/  
            this.s=this.s-1;  
            /*如果时间耗尽，那么，返回clock,重新倒计时*/ 
            if(this.s<0)
            {  
                this.s=10;
                return clocks();
            }  
        }  
    }  

    function exchange(time)
    {  
        /*javascript的除法是浮点除法，必须使用Math.floor取其整数部分*/  
        this.m=Math.floor(time/60); 
        /*存在取余运算*/
        this.s=(time%60);
        this.text=this.s;  
        /*传过来的形式参数time不要使用this，而其余在本函数使用的变量则必须使用this*/ 
        return this.text;  
    }  
    //轮询返回结果
    function ctrlTime(){
        var orderNo = '<?php echo $_GET['orderNo']?>';
    	$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/memberStored/search'));?>',
            data: {orderNo: orderNo},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                	clearInterval(intervalid);//结束查询轮询
                	location.href = "<?php echo Yii::app()->createUrl('syt/memberStored/success')?>";
                }else if(data.error != 'wait'){
                	clearInterval(intervalid);//结束查询轮询
                    alert(data.errMsg);
                }
            }
        });             
    }

    //打印二维码
    function print() {
    	var orderNo = '<?php echo $_GET['orderNo']?>';
    	$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/memberStored/orderInfo'));?>',
            data: {orderNo: orderNo},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                    var pic = '<?php echo $img?>';
                    var image = '<?php echo SYT_STATIC_IMAGES.'wqlogo.png' ?>';
                    var mName = data.m_name;
                    var sName = data.s_name;
                    var operator = data.operator;
                    var orderNo = data.order_no;
                    var type = data.type;
                    var status = data.status;
                    var money = data.money;
                    var time = data.time;
                	printQRcode(pic,image,mName,sName,operator,orderNo,type,status,money,time);
                }else {
                	alert(data.errMsg);
                }
            }
        });
    }

    function printQRcode(pic,image,mName,sName,operator,orderNo,type,status,money,time) {
    	var iCurLine=315;//标题行之后的数据从位置315px开始打印
    	var curX = 0;
    	var curY = 285;
    	var textSpace = 15;
    	var lineSpace = 5;
    	
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
        LODOP.PRINT_INIT("打印图片");
        LODOP.ADD_PRINT_TEXT(20,0,185,30,"请使用支付宝客户端扫描支付");//(扩展型)增加纯文本打印项
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');//打印字体型号
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);//打印样式字体大小
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);//居中
        LODOP.ADD_PRINT_IMAGE(40,curX+10,170,170,"<img border='0' src="+pic+" width='100%' height='170'/>");
        LODOP.ADD_PRINT_IMAGE(229,curX,170,30,"<img src="+image+" width='100%' height='30'>");
        LODOP.ADD_PRINT_LINE(curY,curX+5,curY,190,10,1);//增加直线
         
        curY += lineSpace;
               
        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"商户名称  "+mName);
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        
        curY += mName.length > 12 ? textSpace*2 : textSpace;
        
        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"门店名称  "+sName);
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        
        curY += sName.length > 12 ? textSpace*2 : textSpace;

        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"操  作  员  "+operator);
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        
        curY += textSpace;
        
        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"订  单  号  "+orderNo);
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');   
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);

        curY += textSpace;
        
        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"交易类型  "+type);
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);

        curY += textSpace;
        
        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"交易金额");
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        
		curY += textSpace / 2;
		if(money.length > 8) {
			LODOP.ADD_PRINT_TEXT(curY,curX+60,135,45,"￥ "+money);
			LODOP.SET_PRINT_STYLEA(0,"Alignment",1);
		}else {
			LODOP.ADD_PRINT_TEXT(curY,curX+60,135,30,"￥ "+money);
			LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
		}
		LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
        LODOP.SET_PRINT_STYLEA(0,"Bold",1);//加粗

        curY += textSpace * 2;

	    LODOP.ADD_PRINT_TEXT(curY,curX+5,200,20,"下单时间  "+time);
	    LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
	    LODOP.SET_PRINT_STYLEA(0,"FontSize",8);

		curY += textSpace;
		
	    LODOP.ADD_PRINT_LINE(curY,curX+5,curY,190,10,1);//增加直线

        curY += lineSpace;
        
        LODOP.ADD_PRINT_TEXT(curY,curX+5,200,30,"请确认金额并扫码支付");
        LODOP.SET_PRINT_STYLEA(0,"FontName",'微软雅黑');
        LODOP.SET_PRINT_STYLEA(0,"FontSize",8);
        LODOP.SET_PRINT_PAGESIZE(3,580,0,"");//这里3表示纵向打印且纸高“按内容的高度”；1385表示纸宽138.5mm；45表示页底空白4.5mm
        //clearInterval(intervalid);//结束查询轮询
        LODOP.PRINT();
    }
    
</script>