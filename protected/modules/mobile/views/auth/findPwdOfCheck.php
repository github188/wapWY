<head>
    <title>找回密码</title>
</head>

<body class="logo">
<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'id' => 'findPwdOfCheck',
    'htmlOptions' => array('name' => 'createForm'),
)); ?>
<section class="mid_con register">
    <div class="tel">
        <input type="tel" class="txt" notnull="" pattern="[0-9]*" placeholder="请输入手机号" maxlength="11"
               value="<?php echo isset($_GET['sjh']) ? $_GET['sjh'] : '' ?>" name="User[MobilePhone]">
    </div>
    <div class="dline"></div>
    <div class="password">
        <input type="tel" class="txt" placeholder="请输验证码" maxlength="6"
               value="<?php echo isset($_POST['User']['MsgPassword']) ? $_POST['User']['MsgPassword'] : '' ?>"
               name="User[MsgPassword]">
        <a href="javascript:;" onclick="onMobileMsg()" class="btn_code">获取验证码</a>
    </div>
</section>
<section>
    <div class="btn">
        <input type="submit" value="下一步" class="btn_com" style="width:100%">
    </div>
    <?php if (Yii::app()->user->hasFlash('error')) { ?>
        <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
    <?php } ?>
    </div>
</section>

<?php $this->endWidget(); ?>
</body>

<script language="JavaScript">
    $('.btn_com').click(function () {
        var mobile = $('input[name = User\\[MobilePhone\\]]').val();
        if ('' == mobile) {
            alert('手机号码不能为空！');
            return false;
        }
        var reg = /^(13|15|18|14)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号');
            return false;
        }
        var msgpwd = $('input[name = User\\[MsgPassword\\]]').val();
        if ('' == msgpwd) {
            alert('短信验证码不能为空！');
            return false;
        }
    });
</script>
<script language="JavaScript">
    var mins = 59;
    var intervalid;
    function ctrlTime() {
        if (mins == 0) {
            clearInterval(intervalid);
            $('.btn_code').html('获取验证码');
            $(".btn_code").attr("onclick", 'onMobileMsg()');
            mins = 59;
            return;
        }
        $('.btn_code').html(mins + '秒后重新获取');
        mins--;
    }

    function onMobileMsg() {
        var mobile = $('input[name = User\\[MobilePhone\\]]').val();
        var reg = /^(13|15|18|14)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号!');
        } else {
            $.ajax({
                url: '<?php echo Yii::app()->createUrl('mobile/auth/sendMsgPassword');?>?' + new Date().getTime(),
                data: {mobile: mobile, check: 'no', encrypt_id: '<?php echo $encrypt_id?>'},
                dataType: "json",
                type: 'post',
                async: false,
                success: function (res) {
                    //var res = jQuery.parseJSON(res);
                    if (res.status == '<?php echo ERROR_NONE?>') {
                        intervalid = setInterval("ctrlTime()", 1000);
                        $(".btn_code").removeAttr("onclick");
                    } else {
                        alert(res.errMsg);
                        $(".btn_code").attr("onclick", 'onMobileMsg()');
                    }
                }
            });
        }
    }
</script>
