<head>
    <title></title>
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
                <img src="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/CreateBarcode', array('text' => $user_coupon['code'], 'encrypt_id' => $coupon['encrypt_id'])); ?>">
                <p><?php
                    for ($i = 0; $i < 12; $i++) {
                        $str .= $user_coupon['code'][$i];
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
                            if (date('Ymd', strtotime($user_coupon['start_time'])) == date('Ymd', strtotime($user_coupon['end_time']))) {
                                echo date('Y.m.d', strtotime($user_coupon['start_time']));
                            } else {
                                echo date('Y.m.d', strtotime($user_coupon['start_time'])) . '-' . date('Y.m.d', strtotime($user_coupon['end_time']));
                            }
                            ?>
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

<script>
    /***************************微信分享******************************************/
    //微信js配置
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: '<?php echo $signPackage["timestamp"];?>',
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            "checkJsApi",
            "onMenuShareTimeline",
            "onMenuShareAppMessage",
            "onMenuShareQQ",
        ]
    });

    wx.ready(function () {
        //
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ'
            ],
            success: function (res) {
//                 alert(JSON.stringify(res));
            }
        });

        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: '<?php echo $coupon['title']; ?>', // 分享标题
            link: '<?php echo WAP_DOMAIN . '/mobile/coupon/coupon/getCoupon?qcode=' . $coupon['code'];?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop->logo_img)) {
                echo IMG_GJ_LIST . $onlineshop->logo_img;
            }?>', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享给朋友
        wx.onMenuShareAppMessage({
            title: '<?php echo $coupon['title']; ?>', // 分享标题
            desc: '<?php echo $onlineshop['name']; ?>', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/mobile/coupon/coupon/getCoupon?qcode=' . $coupon['code'];?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop['logo_img'])) {
                echo IMG_GJ_LIST . $onlineshop['logo_img'];
            }?>', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享到QQ
        wx.onMenuShareQQ({
            title: '<?php echo $coupon['title']; ?>', // 分享标题
            desc: '<?php echo $onlineshop['name']; ?>', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/mobile/coupon/coupon/getCoupon?qcode=' . $coupon['code'];?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop['logo_img'])) {
                echo IMG_GJ_LIST . $onlineshop['logo_img'];
            }?>', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
</script>

</body>
