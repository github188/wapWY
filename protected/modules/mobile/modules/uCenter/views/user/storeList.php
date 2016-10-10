<head>
    <title>门店列表</title>
</head>

<body class="logo">
<?php foreach ($model as $key => $value) { ?>
    <section class="md">
        <div class="item dline">
            <a class="intergral"
               href="<?php echo Yii::app()->createUrl('mobile/myun/yunshop/shop', array('store_id' => $key, 'encrypt_id' => $encrypt_id)) ?>"><?php echo $value['name'] ?></a>
        </div>
        <div class="item item_9">
            <a class="intergral"><span class="ico"></span><?php echo str_replace(",", "", $value['address']) ?></a>
        </div>
        <div class="item item_10">
            <a class="intergral" href="tel:<?php echo $value['telephone'] ?>">
                <span class="ico"></span><?php echo $value['telephone'] ?>
            </a>
        </div>
    </section>
<?php } ?>
</body>
