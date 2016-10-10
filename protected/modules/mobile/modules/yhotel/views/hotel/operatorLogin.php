<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="yes" name="apple-touch-fullscreen">
    <meta content="telephone=no,email=no" name="format-detection">
    <script src="http://g.tbcdn.cn/mtb/lib-flexible/0.3.4/??flexible_css.js,flexible.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STATIC_STYLES ?>swiper.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STATIC_STYLES ?>main.css">
    <link href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <title>登录</title>
</head>

<body>
<div class="login">
    <form
        action="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/operatorLogin', array('encrypt_id' => $encrypt_id)) ?>"
        id="login_form" method="post">
        <div class="login-form">
            <div class="account-wrap">
                <i class="icon-account"></i>
                <input type="text" value="<?php if (Yii::app()->user->hasFlash('account')) {
                    echo Yii::app()->user->getFlash('account');
                } ?>" required="required" name="account" class="account" placeholder="请输入您的账号">
            </div>
            <div class="pw-wrap">
                <i class="icon-pw"></i>
                <input type="password" required="required" name="password" class="password" placeholder="请输入您的密码">
            </div>
        </div>
        <button type="submit" class="login-btn">登录</button>
        <?php if (Yii::app()->user->hasFlash('error')) { ?>
            <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
        <?php } ?>
    </form>
</div>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>

</html>
