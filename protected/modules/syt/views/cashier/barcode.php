<div class="popWrap" id="pop" style="width:380px">
    <div class="pop_con" style="text-align:inherit">
    	<?php echo CHtml::beginForm(Yii::app()->createUrl('syt/cashier/barcodePay'))?>
        <div class="pop_content">
           <div class="next">
           	<div class="fw">请扫描客户的条形码或手动输入</div>
           	<input type="hidden" value="<?php echo $_GET['orderNo']?>" name="orderNo">
            <span><input type="tel" class="txt" style="width:80%" id="barcode_input" name="code"></span>
           </div>
           <br>
           <div class="btn"><input class="btn-border" value="确定" type="submit" style="width:45%;float:left"><input id="close_dialog" class="btn-border" value="取消" type="submit" style="width:45%;float:right"></div>  
        </div>
        <?php echo CHtml::endForm()?>
    </div>   
</div>
<script>
	$(function() {
		//条码框获得焦点
		$("input[name=code]").focus();
	});
	$("#close_dialog").click(function() {
		art.dialog.close();
	});
</script>