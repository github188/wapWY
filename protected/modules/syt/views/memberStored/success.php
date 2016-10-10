<div class="popWrap" id="pop" style="width:300px">
    <div class="pop_con" style="text-align:inherit">
        <div class="pop_content">
           <div class="title">提示</div>
           <div class="con">支付成功</div>
           <div class="btn"><input class="btn-border" value="确定" type="submit" id="close_dialog"></div>  
        </div>
    </div>   
</div>
<script>
$("#close_dialog").click(function() {
	//利用共享数据来设置是否清除页面输入
	art.dialog.data('clear', true);
	art.dialog.close();
});
$(document).ready(function() {
	art.dialog.open.api.size(300,150); //重新设置大小
});
</script>