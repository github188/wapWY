<head>
	<title>预订</title>
</head>

<body bgcolor="#F4F5F0">
	<section class="online-set">
		<?php $form = $this->beginWidget('CActiveForm', array(
	        'enableAjaxValidation' => true,
	        'id' => 'bookOperate',
	        'htmlOptions' => array('name' => 'createForm'),
	    ));?>
		<input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id?>">
			<div class="set">
		        <div class="filed">
		        	<span class="empty"></span>
		            <span class="title">人数</span>
		            <span class="choose">
		            	<select name="Book[people_num]">
		            		<?php foreach ($people_num as $num ) {?>
		            			<?php if ($num == 1) { ?>
		            				<?php echo '<option value="'.$num.'" selected=selected>'.$num.'</option>'?>
		            			<?php }else{ ?>
		            				<?php echo '<option value="'.$num.'">'.$num.'</option>'?>
		            			<?php } ?>
		            		<?php } ?>
	                    </select>	
		            </span>        
		        </div>
		        <div class="filed">
		        	<span class="empty"></span>
		            <span class="title">时间</span>
		            <span class="choose">
		            	<?php echo $year?>年
		            	<select name="Book[month]">
		            		<?php foreach ($month_list as $m ) {?>
		            			<?php if ($m < $month) {?>
		            				<?php continue;?>
		            			<?php }elseif ($m == $month) { ?>
		            				<?php echo '<option value="'.$m.'" selected=selected>'.$m.'</option>'?>
		            			<?php }else{ ?>
		            				<?php echo '<option value="'.$m.'">'.$m.'</option>'?>
		            			<?php } ?>
		            		<?php } ?>
		            	</select>月
		            	<select name="Book[day]">
		            		<?php foreach ($day_list as $d ) {?>
		            		    <?php if($d < $day){ ?>
		            		    <?php continue; ?>
		            		    <?php }elseif ($d == $day){?>
		            			<?php //if ($d == $day) { ?>
		            				<?php echo '<option value="'.$d.'" selected=selected>'.$d.'</option>'?>
		            			<?php }else{ ?>
		            				<?php echo '<option value="'.$d.'">'.$d.'</option>'?>
		            			<?php } ?>
		            		<?php } ?>
		            	</select>日
		            	<select name="Book[time]">
		            		<?php foreach ($time_list as $time ) {?>
		            			<?php echo '<option value="'.$time.'">'.$time.'</option>'?>
		            		<?php } ?>
		            	</select>	
		            </span>
		        </div>
		        <?php if( !empty($store) ) { ?>
			        <div class="filed no">
			        	<span class="empty"></span>
			            <span class="title">门店</span>
			            <span class="choose">
			            	<select name="Book[store]">
			            		<?php foreach ($store as $key => $value ) {?>
			            			<?php if ($key == $store_id) { ?>
			            				<?php echo '<option value="'.$key.'" selected=selected>'.$value.'</option>'?>
			            			<?php }else{ ?>
			            				<?php echo '<option value="'.$key.'">'.$value.'</option>'?>
			            			<?php } ?>
			            		<?php } ?>
		                    </select>	
			            </span>
			        </div>
			    <?php } ?>
		    </div>
		    <div class="set">
		        <div class="filed">
		        	<span class="empty"></span>
	            	<span class="input"><input type="text" placeholder="您贵姓" class="txt" name="Book[family_name]"></span>   
		            <span class="sex"><label>
		            	<label>
		                	<input type="radio" name="Book[sex]" value="<?php echo SEX_MALE?>" checked="checked"> 男士
		               	</label>
		             	<label>
		              		<input type="radio" name="Book[sex]" value="<?php echo SEX_FEMALE?>"> 女士
		            	</label>
		            </span>        
		        </div>
		        <div class="filed no">
		        	<span class="empty"></span>
		            <span class="input"><input type="tel" notnull="" pattern="[0-9]*" placeholder="手机号码(必填)" maxlength="11"
                                  value="" name="Book[phone_num]" class="txt"></span>
		        </div>
		    </div>
		    <div class="set">
		        <div class="textarea">
					<textarea class="text" placeholder="如果有需求，可填写，我们将尽量安排" name="Book[remark]"></textarea>     
		        </div>
		    </div>
		    <div class="bottom"><input type="submit" value="提交订单" class="submit"></div>	
    	<?php $this->endWidget(); ?>
	</section>
</body>

<script language="JavaScript">
    $('.submit').click(function () {
        var name = $('input[name = Book\\[family_name\\]]').val();
    	if ('' == name) {
    	    alert('请输入您的姓');
   	     return false;
   		 }
 		 
        var mobile = $('input[name = Book\\[phone_num\\]]').val();
        if ('' == mobile) {
            alert('请输入您的手机');
            return false;
        }

        var reg = /^(13|15|18|14)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号');
            return false;
        }
    });
</script>
