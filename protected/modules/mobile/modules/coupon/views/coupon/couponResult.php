<head>
    <title><?php echo $GLOBALS['COUPON_TYPE'][$coupon['type']] ?></title>
</head>

<body>

<div class="weui_panel weui_panel_access <?php echo $coupon['color'] ?> couponUse">
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_text couponUseHD">
            <div class="logoSymbol">
                <img src="<?php echo IMG_GJ_LIST . $coupon['logo'] ?>" class=""/>
                <p><?php echo $onlineshop['name'] ?></p>
            </div>
            <h2 class="weui_media_title"><?php echo $coupon['title'] ?></h2>
            <p class="weui_media_desc"><?php echo $coupon['vice_title'] ?></p>
            <div>
                <?php if (strtotime($user_coupon['start_time']) <= time() && strtotime($user_coupon['end_time']) >= time()) { ?>
                    <a href="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/CouponDetail', array(
                        'qcode' => $coupon['code'],
                        'user_coupon_id' => $user_coupon['id'],
                        'encrypt_id' => $coupon['encrypt_id']
                    )) ?>" class="weui_btn weui_btn_mini weui_btn_primary" id="show-custom">
                        立即使用
                    </a>
                <?php } else { ?>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default" id="show-custom">
                        未到使用时间
                    </a>
                <?php } ?>
            </div>
            <?php if ($coupon['status'] == WX_CHECK_PASS && Yii::app()->session['source'] == 'wechat') {
                if ($coupon['num'] != $coupon['get_num']) { ?>
                    <div>
                        <?php if ($user_coupon['if_wechat'] != COUPONS_IF_WECHAT_NO) { ?>
                            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default">
                                已同步
                            </a>
                        <?php } else { ?>
                            <a href="javascript:;" id="addCard" class="weui_btn weui_btn_mini weui_btn_default">
                                添加至微信卡包
                            </a>
                        <?php } ?>
                    </div>
                <?php
                }
            } ?>
            <p class="weui_media_desc wq_media_desc_al"><span class="hd">使用条件：</span><span class="bd">
                    <?php if ($coupon['type'] == COUPON_TYPE_CASH || $coupon['type'] == COUPON_TYPE_DISCOUNT) {
                        echo !empty($coupon['mini_consumption']) ? '满' . $coupon['mini_consumption'] . '可用' : '不限金额';
                    } elseif ($coupon['type'] == COUPON_TYPE_EXCHANGE) {
                        echo $coupon['use_illustrate'];
                    } ?>
                </span>
            </p>
            <p class="weui_media_desc wq_media_desc_al"><span class="hd">可用时间：</span><span class="bd">
                    <?php 
                    if ($coupon['time_type'] == VALID_TIME_TYPE_FIXED) {
                        if (date("Ymd", strtotime($coupon['start_time'])) == date("Ymd", strtotime($coupon['end_time']))) {
                            echo date("Y.m.d", strtotime($coupon['start_time'])) . '，';
                        } else {
                            echo date("Y.m.d", strtotime($coupon['start_time'])) . '-' . date("Y.m.d", strtotime($coupon['end_time'])) . '，';
                        }
                    } else {
                        echo '自领取之日起' . $coupon['effective_days'] . '天内有效' . '，';
                    }
                    if (!empty($coupon['use_time_interval'])) {
                        echo $coupon['use_time_interval'];
                    } else {
                        echo '全部时段';
                    } ?>
                </span>
            </p>
        </div>
        <div class="weui_cells weui_cells_access couponUseBD">
            <?php if (!empty($coupon['cover_img'])) { ?>
                <a class="weui_cell"
                   href="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/CouponElecDetail', array('qcode' => $coupon['code'], 'encrypt_id' => $coupon['encrypt_id'])) ?>">
                    <div class="weui_cell_bd weui_cell_primary">
                        <img src="<?php echo IMG_GJ_LIST . $coupon['cover_img'] ?>">
                        <div class="weui_panel_ft" href="javascript:void(0);">
                            <?php echo $coupon['cover_title'] ?>
                        </div>
                    </div>
                </a>
            <?php } ?>

            <a class="weui_cell"
               href="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/storeList', array('qcode' => $coupon['code'], 'encrypt_id' => $coupon['encrypt_id'])) ?>">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>适用门店</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>

            <?php if ($is_qcode) { ?>
                <a class="weui_cell"
                   href="<?php echo Yii::app()->createUrl('mobile/coupon/coupon/QRCode', array('encrypt_id' => $coupon['encrypt_id'])) ?>">
                    <div class="weui_cell_bd weui_cell_primary">
                        <p>公众号</p>
                    </div>
                    <div class="weui_cell_ft"></div>
                </a>
            <?php } ?>

            <a class="weui_cell"
               href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array('encrypt_id' => $coupon['encrypt_id'])) ?>">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>会员中心</p>
                </div>
                <div class="weui_cell_ft"></div>
            </a>

        </div>
    </div>

    <?php
    if (!empty($coupon['marketing_entrance'])) {
        $marketing_entrance = json_decode($coupon['marketing_entrance'], true);
        if (!empty($marketing_entrance['custom_url_name'])) {
            ?>
            <div class="weui_panel_bd">
                <div class="weui_cells weui_cells_access couponUseBD">
                    <a class="weui_cell" href="<?php echo $marketing_entrance['custom_url'] ?>">
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>
                                <?php echo $marketing_entrance['custom_url_name'] ?>
                                <span style="float: right">
                                <?php echo $marketing_entrance['custom_url_sub_title'] ?>
                            </span>
                            </p>
                        </div>
                        <div class="weui_cell_ft"></div>
                    </a>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($marketing_entrance['promotion_url_name'])) { ?>
            <div class="weui_panel_bd">
                <div class="weui_cells weui_cells_access couponUseBD">
                    <a class="weui_cell" href="<?php echo $marketing_entrance['promotion_url'] ?>">
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>
                                <?php echo $marketing_entrance['promotion_url_name'] ?>
                            </p>
                        </div>
                        <div class="weui_cell_ft"></div>
                    </a>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
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
            "addCard"
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

        var flag = true;
        //添加卡券
        $('#addCard').click(function () {
            if (flag) {
                wx.addCard({
                    cardList: [
                        {
                            cardId: '<?php echo $coupon['card_id']?>',
                            cardExt: '<?php echo $cardExt?>'
                        }
                    ],
                    success: function (res) {
                        flag = false;
                        $('#addCard').html('已同步');
                        var encrypt_id = '<?php echo $coupon['encrypt_id'] ?>';
                        var user_coupon_id = '<?php echo $user_coupon['id'] ?>';
                        $.post('<?php echo Yii::app()->createUrl('mobile/coupon/coupon/SetIfWechat') ?>', {
                            encrypt_id: encrypt_id,
                            user_coupon_id: user_coupon_id
                        }, function (data) {

                        });
                    }
                });
            }
        });
    });
</script>

</body>