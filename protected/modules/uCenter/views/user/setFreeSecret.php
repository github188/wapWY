<head>
	<title>小额免密设置</title>
</head>

<body>
	<div class="login_box">
	    <?php echo CHtml::beginForm();?>
        	<section class="mid_con">
	            <div class="input">
	            	<em>储值支付金额小于自定义金额/每日时，无须确认</em>
            		<div class="penny">
            		  <label>
            		    <input type="radio" name="free_secret" value="200" id="RadioGroup1_0" <?php if ($free_secret == 200) { echo 'checked="checked"'; } ?> >
            		   <br> 200
                      </label>
            		  <label>
            		    <input type="radio" name="free_secret" value="300" id="RadioGroup1_1" <?php if ($free_secret == 300) { echo 'checked="checked"'; } ?> >
            		    <br>300
                       </label>
            		  <label>
            		    <input type="radio" name="free_secret" value="500" id="RadioGroup1_2" <?php if ($free_secret == 500) { echo 'checked="checked"'; } ?> >
            		   <br> 500
                      </label>
            		  <label>
            		    <input type="radio" name="free_secret" value="800" id="RadioGroup1_3" <?php if ($free_secret == 800) { echo 'checked="checked"'; } ?> >
            		    <br>800
                      </label>
            		  <label>
            		    <input type="radio" name="free_secret" value="1000" id="RadioGroup1_4" <?php if ($free_secret == 1000) { echo 'checked="checked"'; } ?> >
            		   <br> 1000
                      </label>
              </div>
		        </div>
		        
		        <div class="btn">
		            <input type="submit" value="确定" class="btn_com" style="width:100%">
		        </div>
		        <?php if (Yii::app()->user->hasFlash('success')) { ?>
	            	<script>
	            		alert('<?php echo Yii::app()->user->getFlash('success')?>');
	            		location.href="<?php echo Yii::app()->createUrl('uCenter/user/personalInformation', array('encrypt_id' => $encrypt_id))?>";
	            	</script>
	            	
	            <?php }?>
	            <?php if (Yii::app()->user->hasFlash('error')) { ?>
	            	<script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
	            <?php }?>
		    </section>
	    <?php echo CHtml::endForm();?>
	</div>
</body>


