<body>

<section class="couponsDetails">
    <articl class="mainWrap clearfix">
        <div class="title">优惠券详情</div>
        <div class="con">
            <div class="filed">
                <span class="label">优惠说明：</span>
                <span class="text"><?php echo $coupon_model['discount_illustrate'] ?></span>
            </div>
            <div class="filed">
                <span class="label">有效日期：</span>
                <?php if ($coupon_model['time_type'] == VALID_TIME_TYPE_FIXED) { ?>
                    <?php echo date("Y.m.d", strtotime($coupon_model['start_time'])) ?>&nbsp;-&nbsp;<?php echo date("Y.m.d", strtotime($coupon_model['end_time'])) ?>
                <?php } else { ?>
                    <?php echo $coupon_model['effective_days'] ?>天内有效
                <?php } ?>
            </div>
            <div class="filed">
                <span class="label">客服电话：</span>
                <a class="intergral" href="tel:<?php echo $coupon_model['tel'] ?>"> <span
                        class="text"><em><?php echo $coupon_model['tel'] ?></em></span> </a>
            </div>
            <div class="filed">
                <span class="label">使用说明：</span>
	            <span class="text">
	            	<p>每人限领<?php echo $coupon_model['receive_num'] ?>张</p>
	            	<p>该券<?php echo $GLOBALS['IF_WITH_USERDISCOUNT'][$coupon_model['if_with_userdiscount']] ?>
                        与优惠券折扣同时享用</p>
	            	<p>使用门店：<?php echo str_replace(",", " ", $store_name) ?></p>
	            	<p><?php echo str_replace("\n", "<br>", $coupon_model['use_illustrate']) ?></p>
	            </span>
            </div>
        </div>
    </articl>
    <articl class="couponsEnd">
        <img src="<?php echo USER_STATIC_IMAGES ?>user/logo-bottom.png">
    </articl>
</section>
</body>


<script type="text/javascript">
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
            "addCard",
        ]
    });

    wx.ready(function () {
        //
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ],
            success: function (res) {
                //             alert(JSON.stringify(res));
            }
        });
        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: '<?php echo $onlineshop->name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
            link: '<?php echo WAP_DOMAIN . '/uCenter/coupon/newGetCouponOne?coupon_id=' . $coupon_id . '&encrypt_id=' . $encrypt_id;?>', // 分享链接
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
            title: '<?php echo $onlineshop->name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
            desc: '线下支付，出示会员手机号即可使用优惠券和红包', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/uCenter/coupon/newGetCouponOne?coupon_id=' . $coupon_id . '&encrypt_id=' . $encrypt_id;?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop->logo_img)) {
                echo IMG_GJ_LIST . $onlineshop->logo_img;
            }?>', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                alert('ppp');
                // 用户取消分享后执行的回调函数
            }
        });
        //分享到QQ
        wx.onMenuShareQQ({
            title: '<?php echo $onlineshop->name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
            desc: '线下支付，出示会员手机号即可使用优惠券和红包', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/uCenter/coupon/newGetCouponOne?coupon_id=' . $coupon_id . '&encrypt_id=' . $encrypt_id;?>', // 分享链接
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
    });
</script>
