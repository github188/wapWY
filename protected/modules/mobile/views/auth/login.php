<head>
    <title>登录</title>
</head>

<body class="logo">
<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'id' => 'login',
    'htmlOptions' => array('name' => 'createForm'),
)); ?>

<div class="weui_cells weui_cells_form">
    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">手机号码</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input class="weui_input" type="number" pattern="[0-9]*" placeholder="请输入手机号码" maxlength="11"
                   value="<?php echo isset($_POST['User']['MobilePhone']) ? $_POST['User']['MobilePhone'] : '' ?>"
                   name="User[MobilePhone]">
        </div>
    </div>
    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">密码</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input class="weui_input" type="password" placeholder="请输入密码" maxlength="16"
                   value="<?php echo !empty($_POST['User']['Password']) ? $_POST['User']['Password'] : '' ?>"
                   name="User[Password]">
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo isset($goUrl) ? $goUrl : ''; ?>" name="goUrl">
<a href="javascript:return 0;" onclick="tiaozhuan();" class="forget_pw_link">忘记密码?</a>
<div class="weui_btn_area login_area">
    <button type="submit" class="weui_btn weui_btn_primary" href="javascript:;" id="login_btn">登录</button>
</div>
<a href="<?php echo Yii::app()->createUrl('mobile/auth/register', array('goUrl' => isset($_GET['goUrl']) ? $_GET['goUrl'] : '', 'encrypt_id' => $encrypt_id)) ?>"
   class="sign_link">注册</a>
<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php } ?>

<!--<section class="mid_con">
    <div class="tel">
        <span class="name">手机号</span>
        <input type="tel" class="txt" notnull="" pattern="[0-9]*" placeholder="请输入手机号码" maxlength="11"
               value="<?php /*echo isset($_POST['User']['MobilePhone']) ? $_POST['User']['MobilePhone'] : '' */?>"
               name="User[MobilePhone]">
    </div>
    <div class="dline"></div>
    <div class="password">
        <span class="name">登录密码</span>
        <input type="password" class="txt" notnull="" placeholder="请输入登录密码" maxlength="16"
               value="<?php /*echo !empty($_POST['User']['Password']) ? $_POST['User']['Password'] : '' */?>" class="txt"
               name="User[Password]">
    </div>
    <input type="hidden" value="<?php /*echo isset($goUrl) ? $goUrl : ''; */?>" name="goUrl">
    <input type="hidden" value="<?php /*echo $encrypt_id */?>" name="encrypt_id">
</section>
<section>
    <form id="subform" name="subform" method="post"
          action="<?php /*echo Yii::app()->createUrl('uCenter/user/findPwdOfCheck', array('encrypt_id' => $encrypt_id)) */?>">
        <input id="sjh" type="hidden" value="123456" name="sjh"/>
    </form>
    <a id="forget" href="javascript:return 0;" onclick="tiaozhuan();" class="remove">忘记密码</a>
    <div class="btn">
        <input type="submit" value="立即登录" class="btn_com">
        <a class="btn_border"
           href="<?php /*echo Yii::app()->createUrl('uCenter/user/register', array('goUrl' => isset($_GET['goUrl']) ? $_GET['goUrl'] : '', 'encrypt_id' => $encrypt_id)) */?>">注册</a>
    </div>
    <?php /*if (Yii::app()->user->hasFlash('error')) { */?>
        <script>alert('<?php /*echo Yii::app()->user->getFlash('error')*/?>')</script>
    <?php /*} */?>
</section>-->
<?php $this->endWidget(); ?>
</body>

<script language="JavaScript">
    //验证手机号和密码非空
    $('#login_btn').click(function () {
        var mobile = $('input[name = User\\[MobilePhone\\]]').val();
        var password = $('input[name = User\\[Password\\]]').val();
        if ('' == mobile || '' == password) {
            alert('用户名或密码不能为空！');
            return false;
        }
    });

    //验证账号是否存在
    $('input[name = User\\[MobilePhone\\]').bind('blur', function () {
        $.post('<?php echo(Yii::app()->createUrl('mobile/auth/isexist'));?>?', {
            account: $(this).val(),
            encrypt_id: '<?php echo $encrypt_id?>'
        }, function (data) {
            if ('not' == data.result) {
                alert('账号不存在!');
            }
        }, 'json');
    });

    //将本页输入的手机号带到忘记密码页
    function tiaozhuan() {
        var hs = $('input[name = User\\[MobilePhone\\]]').val();
        window.location.href = "<?php echo Yii::app()->createUrl('mobile/auth/findPwdOfCheck')?>" + '?sjh=' + hs + '&encrypt_id=' + '<?php echo $encrypt_id?>';
    }

</script>
