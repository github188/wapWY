<head>
    <title><?php echo $GLOBALS['COUPON_TYPE'][$coupon['type']]?>详情</title>
</head>

<body>

<div class="weui_panel weui_panel_access couponElecDetail">
    <div class="weui_panel_bd">
        <div class="weui_panel weui_panel_access couponUseBD">
            <div class="weui_panel_hd">代金券详情</div>
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
                            if ($coupon['time_type'] == VALID_TIME_TYPE_FIXED) {
                                if (date("Ymd", strtotime($coupon['start_time'])) == date("Ymd", strtotime($coupon['end_time']))) {
                                    echo date("Y.m.d", strtotime($coupon['start_time'])) . '，';
                                } else {
                                    echo date("Y.m.d", strtotime($coupon['start_time'])) . '-' . date("Y.m.d", strtotime($coupon['end_time'])) . '，';
                                }
                            } else {
                                echo '自领取之日起' . $coupon['effective_days'] . '天内有效' . '，';
                            } ?>
                        </p>
                    </div>
                </div>
                <div class="weui_media_box weui_media_appmsg">
                    <div class="weui_media_hd">可用时段</div>
                    <div class="weui_media_bd">
                        <p class="weui_media_desc">
                            <em class="cBlue">
                                <?php if (!empty($coupon['use_time_interval'])) {
                                    echo $coupon['use_time_interval'];
                                } ?>
                            </em>
                        </p>
                    </div>
                </div>

                <?php if (!empty($coupon['tel'])) { ?>
                    <div class="weui_media_box weui_media_appmsg">
                        <div class="weui_media_hd">电&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;话</div>
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

        <?php if (!empty($coupon['image_text'])) {
            $image_text = json_decode($coupon['image_text'], true);
        ?>
            <div class="weui_panel weui_panel_access couponElecIntro">
                <div class="weui_panel_hd">图文介绍</div>
                <div class="weui_panel_bd">
                    <?php foreach ($image_text as $v) { ?>
                        <div class="weui_media_box weui_media_text">
                            <h4 class="weui_media_title">
                                <img class="weui_media_appmsg_thumb" src="<?php echo IMG_GJ_LIST . $v['image_url'] ?>" alt="">
                            </h4>
                            <p class="weui_media_desc">
                                <?php echo $v['text'] ?>
                            </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

</div>
</body>
