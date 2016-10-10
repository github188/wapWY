
<body>
<script type="text/javascript">
	$(document).ready(main_obj.list_init);
</script>

<div class="kkfm_r_inner">
	<div class="main-right">
            	<div class="status-nav">
                    <ul>
                        <li class="cur"><a href="<?php echo Yii::app()->createUrl('syt/system/editPwd'); ?>">修改密码</a></li>
                        <li class=""><a href="<?php echo Yii::app()->createUrl('syt/system/printOperat'); ?>">打印机管理</a></li>
                       <?php if($role == OPERATOR_ROLE_ADMIN){?>
                         <li class=""><a href="<?php echo Yii::app()->createUrl('syt/system/adminPwd'); ?>">管理员密码</a></li>
                    <?php }?>
                    </ul>
    			</div>
    			<?php echo CHtml::beginForm();?>
             <div class="passward">
             <br />
                <div class="filed ">
                     <span class="label">
                     	<em class="red">*</em>
                     	旧密码
                     </span>
                     <span class="text">
                        <?php echo CHtml::passwordField('Operator[pwd]','',array('class'=>'txt','style'=>'height:30px;width:200px;'))?>
                     </span>
                     <span class="text1">
                        <font color="red"><?php echo Yii::app()->user->getFlash('oldPwd'); ?></font>
                     </span>
                 </div>
                <div class="filed">
                	<span class="label">
                     	<em class="red">*</em>
                     	新密码
                     </span>
                     <span class="text">
                     <?php echo CHtml::passwordField('newPwd','',array('class'=>'txt','style'=>'height:30px;width:200px'))?>
                     </span>
                     <span class="text1"><font color="red"><?php echo Yii::app()->user->getFlash('newPwd'); ?></font></span>
                 </div>
                 <div class="filed">
                 	 <span class="label">
                     	<em class="red">*</em>
                     	确认新密码
                     </span>
                     <span class="text">
                      <?php echo CHtml::passwordField('newPwdAgain','',array('class'=>'txt','style'=>'height:30px;width:200px'))?>
                     </span>
                     <span class="text1">
                      <font color="red"><?php echo Yii::app()->user->getFlash('newPwdAgain'); ?></font>
                      <font color="red"> <?php echo Yii::app()->user->getFlash('notsame'); ?></font>
                     </span>
                 </div>
                 <div class="filed">
                     
                     <span class="label">
                      
                     </span>
                     <span class="text"><?php echo CHtml::submitButton('确认修改',array('class'=>'btn_com_gray'))?></span>
                 </div>
                 
        	</div>
        	<?php echo CHtml::endForm();?>
        </div>
</div>
</body>
