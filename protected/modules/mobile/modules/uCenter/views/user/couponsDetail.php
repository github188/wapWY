<head>
    <title><?php echo $coupons['type'] == COUPON_TYPE_REDENVELOPE ? '红包' : '优惠券'; ?>详情</title>
</head>

<body>

<div class="weui_panel weui_panel_access <?php echo $coupon['color'] ?> couponDetail">
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_text couponUseHD">
            <div class="logoSymbol">
                <p><?php echo $onlineshop['name'] ?></p>
            </div>
            <h2 class="weui_media_title"><?php echo $coupon['title'] ?></h2>
            <p class="weui_media_desc"><?php echo $coupon['vice_title'] ?></p>
            <div class="line">
                <i class="lineRadius lineL"></i>
                <hr>
                <i class="lineRadius lineR"></i>
            </div>
            <div class="twoCode">
                <img src="<?php echo Yii::app()->createUrl('mobile/uCenter/user/CreateBarcode', array('text' => $coupon['code'], 'encrypt_id' => $encrypt_id)); ?>">
                <p><?php
                    for ($i = 0; $i < 12; $i++) {
                        $str .= $coupon['code'][$i];
                        if (($i + 1) % 4 == 0) {
                            $str .= ' ';
                        }
                    }
                    echo $str;
                    ?></p>
            </div>
            <p class="weui_media_desc">到门店出示使用</p>
        </div>
        <div class="weui_panel weui_panel_access couponUseBD">
            <div class="weui_panel_hd">优惠券详情</div>
            <div class="weui_panel_bd">
                <div class="weui_media_box weui_media_appmsg">
                    <div class="weui_media_hd">优惠说明</div>
                    <div class="weui_media_bd">
                        <p class="weui_media_desc">
                            <?php if ($coupon['type'] == COUPON_TYPE_CASH) {
                                echo '价值' . $coupon['money'] . '元代金券一张；消费满' . $coupon['mini_consumption'] . '元可用';
                            } elseif ($coupon['type'] == COUPON_TYPE_DISCOUNT) {
                                echo '价值' . $coupon['discount'] * 10 . '折扣代金券一张；消费满' . $coupon['mini_consumption'] . '元可用';
                            } elseif ($coupon['type'] == COUPON_TYPE_EXCHANGE) {
                                echo $coupon['title'] . $coupon['vice_title'];
                            }?>
                        </p>
                    </div>
                </div>
                <div class="weui_media_box weui_media_appmsg">
                    <div class="weui_media_hd">有效日期</div>
                    <div class="weui_media_bd">
                        <p class="weui_media_desc">
                            <?php
                            if (date('Ymd', strtotime($coupon['start_time'])) == date('Ymd', strtotime($coupon['end_time']))) {
                                echo date('Y.m.d', strtotime($coupon['start_time']));
                            } else {
                                echo date('Y.m.d', strtotime($coupon['start_time'])) . '-' . date('Y.m.d', strtotime($coupon['end_time']));
                            } ?>
                        </p>
                    </div>
                </div>
                
                <?php if (!empty($coupon['tel'])) { ?>
                    <div class="weui_media_box weui_media_appmsg">
                        <div class="weui_media_hd">客服电话</div>
                        <div class="weui_media_bd">
                            <p class="weui_media_desc">
                                <em class="cBlue">
                                    <a href="tel:<?php echo $coupon['tel'] ?>">
                                        <?php echo $coupon['tel'] ?>
                                    </a>
                                </em>
                            </p>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($coupon['use_illustrate'])) { ?>
                    <div class="weui_media_box weui_media_appmsg">
                        <div class="weui_media_hd">使用须知</div>
                        <div class="weui_media_bd">
                            <p class="weui_media_desc">
                                <?php echo $coupon['use_illustrate'] ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

</body>
