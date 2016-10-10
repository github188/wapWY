<head>
    <title>个人信息</title>
</head>

<body class="logo">
<?php echo CHtml::beginForm(); ?>
<section class="mid_con register">
    <div class="tel">
        <?php echo CHtml::textField('user[data]', $data, array('class' => 'txt')) ?>
    </div>
    <div class="dline"></div>
</section>
<section>
    <div class="btn">
        <input type="submit" value="确定" class="btn_com" style="width:100%">
    </div>
</section>

<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php } ?>
<?php echo CHtml::endForm(); ?>
</body>


