<head>
	<title>我的储值</title>
</head>
<body>
	<?php echo CHtml::beginForm(Yii::app()->createUrl('uCenter/stored/payway',array('encrypt_id' => $encrypt_id)),'get',array('name'=>'user_payway'));?>
	<div class="storedWrap">
    	<div class="bgWhite">
    	<?php if (!empty($stored_activity)) { ?>
    	<?php foreach($stored_activity as $k=>$v){?>
        	<div class="rItem clearfix">
                <div class="l">
                    <div class="checkboxCom <?php if($key['key'] == $v['id']){?>checkboxComCur<?php }?>">
                        <input type="checkbox" id="checkbox01"  <?php if($key['key'] == $v['id']){?>checked<?php }?> value="<?php echo $v['id'];?>" name="stored_id"> 
                        <label for="checkbox01">充<?php echo $v['stored_money'];?>元送<?php echo $v['get_money']?>元</label>
                    </div>
                </div>
                <div class="r" <?php if($key['key'] != $v['id']){?>style="display:none"<?php }?>>
                    <div class="num_select">
                        <b class="minus-light">-</b>
                        <input type="text" class="text" maxlength="4" value="1" name="stored_num[<?php echo $v['id']?>]"/>
                        <i class="plus-light">+</i>
                    </div>
                </div>
            </div>
            <?php }?>
		<?php }?>

        </div>
        
        <div class="description"><a href="<?php echo Yii::app() -> createUrl('uCenter/stored/StoredOrderList', array('encrypt_id' => $encrypt_id));?>">储值明细</a></div>
        <br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br>

        <div class="btnwrap">
        	<input type="submit" class="btn_red" value="确认储值">
        </div>
    </div>
    <?php echo CHtml::endForm();?>
</body>

<script type="text/javascript">
$('.minus-light').click(function(){
	var v = $(this).parent().find("input").val();
	if(v > 1){
		$(this).parent().find("input").val(parseInt(v)-parseInt(1));
	}
});

$('.plus-light').click(function(){
	var v = $(this).parent().find("input").val();
	$(this).parent().find("input").val(parseInt(v)+parseInt(1));
});

$('.l').bind("click",function(){
	$('.l').children().removeClass('checkboxComCur');
	$('.l').children().find('#checkbox01').attr("checked",false);
	$('.r').hide();
	
	$(this).children().find('#checkbox01').attr("checked",true);
	$(this).children().addClass('checkboxComCur');
	
	$(this).next().show();
	
	return false;
});
</script>
