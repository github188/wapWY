<head>
    <title>领取失败</title>
</head>
<body class="wap_color">
<div class="content">
    <div class="lose"><img src="<?php echo USER_STATIC_IMAGES ?>user/lose.png"></div>
    <div class="use"><span><?php echo $msg ?></span>
        <br><br>
        <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/coupons', array('coupons_status' => COUPONS_USE_STATUS_UNUSE, 'coupons_type' => $coupons_type, 'encrypt_id' => $encrypt_id)) ?>">
            <?php if ($coupons_type == COUPON_TYPE_REDENVELOPE) { ?>
                查看红包
            <?php } else { ?>
                查看优惠券
            <?php } ?>
        </a>
    </div>
</div>
</div>
</body>
