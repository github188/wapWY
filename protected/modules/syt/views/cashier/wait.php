<div class="popWrap" id="pop" style="width:300px;">
    <div class="pop_con" style="text-align:inherit">
        <div class="pop_content">
           <div class="title">提示</div>
           <div class="con">下单成功，等待用户第三方支付确认或储值支付确认...</div>
           <div class="btn"><input class="btn-border" value="返回收银" type="submit" id="close_dialog"></div>  
        </div>
    </div>   
</div>
<script>
$("#close_dialog").click(function() {
	alert('用户尚未完成付款\n如用户付款成功，请到订单详情中使用查询功能');
	art.dialog.close();
});
$(document).ready(function() {
	art.dialog.open.api.size(300,150); //重新设置大小
	run(); //开始轮询
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
    var orderNo = '<?php echo $orderNo?>';
	$.ajax({
        url: '<?php echo(Yii::app()->createUrl('syt/cashier/search'));?>',
        data: {orderNo: orderNo},
        type: 'post',
        dataType: 'json',
        success: function (data) {
            if(data.error == 'success') {
            	clearInterval(intervalid);//结束查询轮询
            	location.href = "<?php echo Yii::app()->createUrl('syt/cashier/success', array('orderNo' => $orderNo))?>";
            }else if(data.error == 'failure'){
            	clearInterval(intervalid);//结束查询轮询
                alert(data.errMsg);
            }
        }
    });             
}
</script>