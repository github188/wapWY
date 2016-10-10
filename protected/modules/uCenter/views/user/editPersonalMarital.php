<head>
	<title>个人信息</title>
</head>

<body class="logo">
	    <?php echo CHtml::beginForm();?>
        	<section class="mid_con">
	            <div class="input">
	            	<em></em>
            		<div class="penny">
            		  <label>
            		    <input type="radio" name="marital_status" value="<?php echo MARITAL_STATUS_MARRIED?>" id="RadioGroup1_0" <?php if ($marital_status == MARITAL_STATUS_MARRIED) { echo 'checked="checked"'; } ?> >
            		   <br> 已婚
                      </label>
            		  <label>
            		    <input type="radio" name="marital_status" value="<?php echo MARITAL_STATUS_UNMARRIED?>" id="RadioGroup1_1" <?php if ($marital_status == MARITAL_STATUS_UNMARRIED) { echo 'checked="checked"'; } ?> >
            		    <br>未婚
                       </label>
             	 </div>
		        </div>
		        
		        <div class="btn">
		            <input type="submit" value="确定" class="btn_com" style="width:100%">
		        </div>
		        <?php if (Yii::app()->user->hasFlash('success')) { ?>
	            	<script>
	            		alert('<?php echo Yii::app()->user->getFlash('success')?>');
	            		location.href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalSex', array('encrypt_id' => $encrypt_id))?>";
	            	</script>
	            	
	            <?php }?>
	            <?php if (Yii::app()->user->hasFlash('error')) { ?>
	            	<script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
	            <?php }?>
		    </section>
	    <?php echo CHtml::endForm();?>
</body>


