<title>会员登录</title>
<div class="prop_wrap prop_login_wrap">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mobile/property/User/Login',array('encrypt_id' => $encrypt_id)), 'post'); ?>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell bt">
            <div class="weui_cell_hd"><label class="weui_label">账号</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::numberField('account', isset($_POST['account']) ? $_POST['account'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的账号……')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">密码</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::passwordField('pwd', isset($_POST['pwd']) ? $_POST['pwd'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的密码……')); ?>
            </div>
        </div>
    </div>
    <div class="weui_btn_area login_area">
        <?php echo CHtml::submitButton('登录', array('class' => 'weui_btn weui_btn_primary','id' => 'login_btn')); ?>
        <a href="<?php echo Yii::app()->createUrl('mobile/property/User/Sign', array('encrypt_id' => $encrypt_id))?>" class="weui_btn weui_btn_default">注册</a>
    </div>
    <?php echo CHtml::endForm(); ?>

    <?php if (Yii::app()->user->hasFlash('error')) { ?>
        <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
    <?php }?>
    
</div>

