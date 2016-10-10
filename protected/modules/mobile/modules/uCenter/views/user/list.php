<head>
    <title>
        <?php if ($title == 'stored') { ?>
            储值活动列表
        <?php } elseif ($title == 'coupons') { ?>
            优惠券列表
        <?php } elseif ($title == 'red_envelope') { ?>
            红包列表
        <?php } else if ($title == 'online') { ?>
            预订列表
        <?php } ?>
    </title>
</head>

<body class="shop">
<section class="list">
    <?php if ($title == 'stored') { ?>
        <?php foreach ($model as $key => $value) { ?>
            <a class="cz-list"
               href="<?php echo Yii::app()->createUrl('mobile/stored/stored/StoredView', array('id' => $key, 'encrypt_id' => $encrypt_id)) ?>">充<?php echo $value['stored_money'] ?>
                得<?php echo $value['get_money'] ?>元</a>
        <?php } ?>
    <?php } elseif ($title == 'coupons') { ?>
        <?php foreach ($model as $key => $value) { ?>
            <div class="hb-list">
                <a href="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/getCoupon', array('qcode' => $value['code'])) ?>">
                    <span class="img">
                        <img src="<?php echo USER_STATIC_IMAGES ?>user/img01.png">
                    </span>
                    <span class="name">
                        <?php echo $value['title'];
                        /*if ($value['type'] == COUPON_TYPE_CASH) {//代金券
                            if (isset($value['fixed_value']) && !empty($value['fixed_value'])) {
                                echo $value['fixed_value'] . '元 (' . $GLOBALS['COUPON_TYPE'][$value['type']] . ')';
                            } else {
                                echo $value['userdefined_value'] . '元 (随机' . $GLOBALS['COUPON_TYPE'][$value['type']] . ')';
                            }
                        } elseif ($value['type'] == COUPON_TYPE_DISCOUNT) {//折扣券
                            echo ($value['discount'] * 10) . '折 (' . $GLOBALS['COUPON_TYPE'][$value['type']] . ')';
                        } elseif ($value['type'] == COUPON_TYPE_EXCHANGE) {//兑换券
                            echo $value['exchange'] . ' (' . $GLOBALS['COUPON_TYPE'][$value['type']] . ')';
                        }*/
                        ?>
                    </span>
                    <span class="receive">点击领取</span>
                </a>
            </div>
        <?php } ?>
    <?php } elseif ($title == 'red_envelope') { ?>
        <?php foreach ($model as $key => $value) { ?>
            <div class="hb-list">
                <a href="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/getCoupon', array('qcode' => $value['code'])) ?>">
                    <span class="img"><img src="<?php echo USER_STATIC_IMAGES ?>user/img02.png"></span>
		            	<span class="name">
		            	<?php if (isset($value['fixed_value']) && !empty($value['fixed_value'])) {
                            echo $value['fixed_value'] . '元 (红包)';
                        } else {
                            echo $value['userdefined_value'] . '元 (随机红包)';
                        } ?>
		            	</span>
                    <span class="receive">点击领取</span>
                </a>
            </div>
        <?php } ?>
    <?php } elseif ($title == 'online') {
        for ($i = 0; $i < count($model); $i++) { ?>
            <div class="hb-list">
                <span class="name"><?php echo $model[$i] ?></span>
            </div>
        <?php }
    } ?>
</section>
</body>
