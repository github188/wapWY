<head>
    <title>修改密码</title>
</head>

<body class="logo">
<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'id' => 'changePwd',
    'htmlOptions' => array('name' => 'createForm'),
)); ?>
<section class="mid_con register">
    <div class="tel">
        <input type="password" class="txt" notnull="" placeholder="当前密码" maxlength="16"
               value="<?php echo isset($_POST['User']['OldPassword']) ? $_POST['User']['OldPassword'] : '' ?>"
               name="User[OldPassword]">
    </div>
    <div class="dline"></div>
    <div class="tel">
        <input type="password" class="txt" notnull="" placeholder="新密码" maxlength="16"
               value="<?php echo isset($_POST['User']['NewPassword']) ? $_POST['User']['NewPassword'] : '' ?>"
               name="User[NewPassword]">
    </div>
    <div class="dline"></div>
    <div class="tel">
        <input type="password" class="txt" notnull="" placeholder="确认新密码" maxlength="16"
               value="<?php echo isset($_POST['User']['ConfirmPassword']) ? $_POST['User']['ConfirmPassword'] : '' ?>"
               name="User[ConfirmPassword]">
    </div>
</section>
<section>
    <div class="btn">
        <input type="submit" value="确认提交" class="btn_com" style="width:100%">
    </div>
    <?php if (Yii::app()->user->hasFlash('success')) { ?>
        <script>
            alert('<?php echo Yii::app()->user->getFlash('success')?>');
            location.href = "<?php echo Yii::app()->createUrl('mobile/uCenter/user/personalInformation', array('encrypt_id' => $encrypt_id))?>";
        </script>
    <?php } ?>
    <?php if (Yii::app()->user->hasFlash('error')) { ?>
        <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
    <?php } ?>
    <?php $this->endWidget(); ?>
</body>

<script language="JavaScript">
    $('.btn_green').click(function () {
        var password = $('input[name = User\\[OldPassword\\]]').val();
        if ('' == password) {
            alert('密码不能为空！');
            return false;
        }

        var password = $('input[name = User\\[NewPassword\\]]').val();
        if ('' == password) {
            alert('密码不能为空！');
            return false;
        }
        var confirm = $('input[name = User\\[ConfirmPassword\\]]').val();
        if ('' == confirm) {
            alert('确认密码不能为空！');
            return false;
        }
        if (password != confirm) {
            alert('两次密码输入的不一样！');
            return false;
        }
    });
</script>
