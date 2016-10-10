<div class="kkfm_r_inner">
	<div class="main-right">
            	<div class="status-nav">
                    <ul>
                        <li class=""><a href="<?php echo Yii::app()->createUrl('syt/system/editPwd'); ?>">修改密码</a></li>
                        <li class=""><a href="<?php echo Yii::app()->createUrl('syt/system/printOperat'); ?>">打印机管理</a></li>
                        <?php if($role == OPERATOR_ROLE_ADMIN){?>
                         <li class="cur"><a href="<?php echo Yii::app()->createUrl('syt/system/adminPwd'); ?>">管理员密码</a></li>
                    <?php }?>
                    </ul>
    			</div>
<div class="passward">
<br />
 <div class="filed ">
  <?php echo CHtml::beginForm(); ?>
  <span class="label">登录密码</span>
  <span class="text">
     <?php echo CHtml::passwordField('pwd','',array('class'=>'txt','style'=>'height:30px;width:200px','placeholder'=>'请输入登录密码')); ?>
     </span>
      <span class="text1">
    <font color="red"><?php echo Yii::app()->user ->getFlash('pwd');?> </font>
     </span>
      <div class="filed">
      <span class="label"></span>
       <span class="text">
        <?php echo CHtml::submitButton('确定',array('class'=>'btn_com_gray')); ?>
     </span>
     </div>
  <?php echo CHtml::endForm(); ?>
</div>
</div>
</div>
</div>