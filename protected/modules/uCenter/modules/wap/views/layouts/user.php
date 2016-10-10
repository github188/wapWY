<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0" />
    <title>牛扣生活！-登录</title>
    <link rel="stylesheet" href="<?php echo Yii::app()-> baseUrl?>/static/wap/css/basic.css" />

    <!--让IE也支持html5标签-->
    <!--[if IE]>
    <script src="<?php echo Yii::app()-> baseUrl?>/static/wap/js/html5.js"></script>
    <![endif]-->

</head>

<body class="bg">


<header class="clearfix">
    <div class="search">
        <form action="?">
            <input type="text" class="txt">
            <input type="submit" class="btn" value="">
        </form>
    </div>
    <h1><a href="#"><img src="<?php echo Yii::app()->baseUrl?>/static/wap/images/2.png"></a></h1>
</header>

<?php echo $content;?>


</body>
</html>