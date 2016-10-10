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
	<?php echo CHtml::beginForm(); ?>
<div class="filed"><br />
                     <span class="label">管理员密码</span>
                     <span class="text">
                     <?php echo CHtml::textField('randPwd',$data,array('class'=>'txt','readonly'=>'true','style'=>'height:30px;width:90px',)) ?></span>
                     <span class="text1"><input type="submit" value="刷新" class="btn_com_gray" style="height:35px"></span>
</div>
<?php echo CHtml::endForm(); ?>
 </div>
</div>
</div>