<head>
	<title>个人信息</title>
</head>

<body class="logo">
	    <?php echo CHtml::beginForm();?>
	    	<section class="mid_con register">
        		<div class="tel">
	            	<span>
	            		<select name="Birthday[year]">
		            		<?php foreach ($arr_year as $y ) {?>
		            			<?php if ($y == $year) { ?>
		            				<?php echo '<option value="'.$y.'" selected=selected>'.$y.'</option>'?>
		            			<?php }else{ ?>
		            				<?php echo '<option value="'.$y.'">'.$y.'</option>'?>
		            			<?php } ?>
		            		<?php } ?>
		            	</select>年
	            	</span>
	                <span>
	                	<select name="Birthday[month]">
		            		<?php foreach ($arr_month as $m ) {?>
		            			<?php if ($m == $month) { ?>
		            				<?php echo '<option value="'.$m.'" selected=selected>'.$m.'</option>'?>
		            			<?php }else{ ?>
		            				<?php echo '<option value="'.$m.'">'.$m.'</option>'?>
		            			<?php } ?>
		            		<?php } ?>
		            	</select>月
	                </span>
	                <span>
	                	<select name="Birthday[day]">
		            		<?php foreach ($arr_day as $d ) {?>
		            			<?php if ($d == $day) { ?>
		            				<?php echo '<option value="'.$d.'" selected=selected>'.$d.'</option>'?>
		            			<?php }else{ ?>
		            				<?php echo '<option value="'.$d.'">'.$d.'</option>'?>
		            			<?php } ?>
		            		<?php } ?>
		            	</select>日
	                </span>
	            </div>
	        </section>
		        
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


