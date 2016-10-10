<head>
    <title>会员优惠券</title>
</head>
<body>
<section class="couponsLst">
    <?php if (empty($list)) { ?>
        <div class="no-coupon">
            <i class="icon-coupon"></i>
            <p class="title">您还没有优惠券哦</p>
        </div>
    <?php } ?>
    <?php if (!empty($list)) { ?>
        <?php foreach ($list as $key => $value) { ?>
            <?php if (strtotime($now) <= strtotime($value['end_time'])) { ?>
                <a href="<?php echo Yii::app()->createUrl('uCenter/Coupon/CouponDetail', array('id' => $value['id'], 'encrypt_id' => $encrypt_id)) ?>">
                    <article class="listWrap">
                        <div class="top <?php echo empty($value['color']) ? 'Color010' : $value['color'] ?>">
                            <div class="img"><img src="<?php echo IMG_GJ_LIST . $value['logo_img'] ?>"></div>
                            <div class="name">
                                <span><?php echo empty($value['merchant_short_name']) ? $value['name'] : $value['merchant_short_name']; ?></span>
                                <span class="fz"><?php echo $value['title'] ?></span>
                            </div>
                        </div>
                        <div class="end"> 有效期：<?php echo date("Y.m.d", strtotime($value['start_time'])) ?>
                            &nbsp;-&nbsp;<?php echo date("Y.m.d", strtotime($value['end_time'])) ?></div>
                    </article>
                </a>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <?php if (!empty($list)) { ?>
        <?php foreach ($list as $key => $value) { ?>
            <?php if (round((strtotime($now) - strtotime($value['end_time'])) / 3600 / 24) <= 5 && round((strtotime($now) - strtotime($value['end_time'])) / 3600 / 24) >= 0 || $value['status'] == COUPONS_USE_STATUS_EXPIRED) { ?>
                <a href="<?php echo Yii::app()->createUrl('uCenter/Coupon/CouponDetail', array('id' => $value['id'], 'encrypt_id' => $encrypt_id)) ?>">
                    <article class="listWrap">
                        <div class="top <?php echo empty($value['color']) ? 'Color010' : $value['color'] ?>">
                            <div class="img"><img src="<?php echo IMG_GJ_LIST . $value['logo_img'] ?>"></div>
                            <div class="name">
                                <span><?php echo $value['name'] ?></span>
                                <span class="fz"><?php echo $value['title'] ?></span>
                            </div>
                        </div>
                        <div class="end"> 有效期：<?php echo date("Y.m.d", strtotime($value['start_time'])) ?>
                            -<?php echo date("Y.m.d", strtotime($value['end_time'])) ?></div>
                        <div class="over"></div>
                    </article>
                </a>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</section>
</body>
