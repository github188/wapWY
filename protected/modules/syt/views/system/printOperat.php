<body>
<div class="kkfm_r_inner">
	<div class="main-right">
    	<div class="status-nav">
           	<ul>
              	<li class=""><a href="<?php echo Yii::app()->createUrl('syt/system/editPwd'); ?>">修改密码</a></li>
              	<li class="cur"><a href="<?php echo Yii::app()->createUrl('syt/system/printOperat'); ?>">打印机管理</a></li>
               	<?php if($role == OPERATOR_ROLE_ADMIN){?>
               	<li class=""><a href="<?php echo Yii::app()->createUrl('syt/system/adminPwd'); ?>">管理员密码</a></li>
              	<?php }?>
           	</ul>
    	</div>
 		<div class="passward">  
 		<?php echo CHtml::beginForm(); ?>
<!-- <div> -->
<!-- 	<br /> -->
<!--    <span class="label">是否启用打印机:</span> -->
     <?php //echo CHtml::radioButtonList('is_print', '', $is_print_array,array('separator'=>'')); ?>
<!--     <div class="filed"> -->
       <!--  <span class="label" style="text-align:left;padding-left:14%">-->
        <?php //echo CHtml::submitButton('提交',array('class'=>'btn_com_gray')); ?>
<!--      </span> -->
<!--      </div> -->
<!-- </div> -->

			<div class="filed">
     			<span class="label fw">启动打印机</span>
				<div class="switch text" data-on-text="开启" data-off-text="关闭">
    				<input type="checkbox" <?php if($print['is_print'] == 2){?>checked<?php }?> name="is_print"/>
					<div class="remark">如需打印交易小票，请开启打印机</div>				
				</div>
				
    		</div>
   			<div class="filed" <?php if($print['is_print'] == 1){?>style="display: none"<?php }?> id="print">
  				<span class="label fw">默认打印机</span>
  				<span class="text">
  					<input type="button" class="btn_border" value="<?php echo empty($print['print_name'])?'选择默认打印机':$print['print_name'];?>" onclick="select()">
  					<div class="remark title">点击可选择或修改默认打印机</div>
  				</span>
  			</div>
  			
  			<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
				<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop.exe"></embed>
			</object>
		<?php echo CHtml::endForm(); ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
    	//检查打印控件安装状态
    	var LODOP = getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'),'title');
    	if(LODOP==null || typeof(LODOP.VERSION)=="undefined") {
    	}else {
        	$(".title").html('点击可选择或修改默认打印机');
    	}
	});

	$.fn.bootstrapSwitch.defaults.onText = '开启';
	$.fn.bootstrapSwitch.defaults.offText = '关闭';
	$('.switch').on('switchChange.bootstrapSwitch',function(event,state){
		if(state){
			$('#print').show();
		}else{
			$('#print').hide();
		}
		//开启或关闭打印
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/System/OpenPrint'));?>',
            data: {state: state},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.status == <?php echo ERROR_NONE?>) {
                }else{
	                alert('设置失败：'+data.errMsg);
	          	}
            }
        });

	});

	//设置打印机名称
	$('.btn_border').click(function(){
		LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
		var index = LODOP.SELECT_PRINTER();
		if (index >= 0) {
			var printname =  LODOP.GET_PRINTER_NAME(index);
			$.ajax({
	            url: '<?php echo(Yii::app()->createUrl('syt/System/SetPrint'));?>',
	            data: {name: printname},
	            type: 'post',
	            dataType: 'json',
	            success: function (data) {
	                if(data.status == <?php echo ERROR_NONE?>) {
		                $('.btn_border').val(printname);
		                alert("选择成功!");
	                }else{
		                alert(data.errMsg);
		          	}
	            }
	        });
		}
	});

</script>
</body>