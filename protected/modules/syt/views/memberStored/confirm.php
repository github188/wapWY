<div class="popWrap" id="pop" style="width:300px;">
    <div class="pop_con" style="text-align:inherit">
        <div class="pop_content">
           <div class="title">收款信息</div>
           <?php echo CHtml::beginForm()?>
           <input type="hidden" name="orderNo" value="<?php echo $_GET['orderNo']?>">
           <div class="con" style="padding-bottom: 30px">实收金额：￥<?php echo $money ?></div>
           <?php echo CHtml::endForm()?>
           <div class="btn"><input class="btn-border" value="确定" type="submit" style="width:45%;float:left" onclick="$('form').submit()"><input id="close_dialog" class="btn-border" value="取消" type="submit" style="width:45%;float:right"></div>  
        </div>
    </div>   
</div>
<script>
$("#close_dialog").click(function() {
	art.dialog.close();
});
$(document).ready(function() {
	art.dialog.open.api.size(300,150); //重新设置大小
});
</script>