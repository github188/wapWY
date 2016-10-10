<body>
<header class="couponsHead <?php echo $coupon_model['color'] ?>">
    <div class="headImg clearfix">
        <div class="img"><img src="<?php echo IMG_GJ_LIST . $coupon_model['logo_img'] ?>"></div>
        <div
            class="name"><?php echo empty($coupon_model['merchant_short_name']) ? $coupon_model['name'] : $coupon_model['merchant_short_name'] ?></div>
    </div>
    <div class="con">
        <h3><?php echo $coupon_model['title'] ?></h3>
        <h4><?php echo $coupon_model['vice_title'] ?></h4>
        <div class="time">
            <?php if ($coupon_model['time_type'] == VALID_TIME_TYPE_FIXED) { ?>
                有效期：<?php echo date("Y.m.d", strtotime($coupon_model['start_time'])) ?>&nbsp;-&nbsp;<?php echo date("Y.m.d", strtotime($coupon_model['end_time'])) ?>
            <?php } else { ?>
                自领取之日起<?php echo $coupon_model['effective_days'] ?>天内有效
            <?php } ?>
        </div>
    </div>
</header>
<section class="main">
    <section class="couponsCon">
        <a href="<?php echo Yii::app()->createUrl('uCenter/coupon/newCouponDetail', array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id)) ?>"
           class="intergral"><span class="text">优惠券详情</span><span class="jt"></span></a>
    </section>
    <section class="couponGetSucceed clearfix">
        <div class="desc">
            <p><em>该券已放入您的账户</em> <?php echo $account ?></p>
            <?php if ($coupon_model['if_wechat'] == IF_WECHAT_YES && isset(Yii::app()->session['wechat_code'])) { ?>
                <a href="#" class="btn_red" id="addCard" <?php echo empty($flag) ? 'style="display: none"' : '' ?>>同步到微信卡包</a>
            <?php } ?>
            <span id="add_ready" class="btn_red" <?php echo !empty($flag) ? 'style="display: none"' : '' ?>>已同步</span>
            <?php if ($coupon_model['if_share'] == IF_SHARE_YES) { ?>
                <a href="#" class="share">分享给好友&gt;&gt;</a>
            <?php } ?>
        </div>
    </section>
    <section class="couponsCon couponsCon01">
        <a href="#" class="intergral" id="erWeiMa"><span
                class="text">您可关注微信公众号"<?php echo $coupon_model['wechat_name'] ?>"，在会员中查看优惠券</span><span
                class="jt"></span></a>
    </section>
    <!-- 	<section class="couponsCon couponsCon01"> -->
    <!-- 		<a href="" class="intergral"><span class="text">您可关注"玩券管家"微信公众号，查看您所有的优惠券</span><span class="jt"></span></a> -->
    <!-- 	</section> -->
    <?php if (!empty($coupon_record)) { ?>
        <section class="otherFriend">
            <div class="title"><span>看看其他朋友</span></div>
            <?php $i = 0; ?>
            <?php foreach ($coupon_record as $key => $val) { ?>
                <?php $i++; ?>
                <?php if ($i > 10) {
                    break;
                } ?>
                <dl>
                    <dt><img src="<?php echo $val['avatar'] ?>"></dt>
                    <dd>
                        <div class="name"><em><?php echo $val['nickname'] ?></em> <?php echo $val['create_time'] ?>
                        </div>
                        <?php if ($val['type'] == COUPON_TYPE_CASH) { ?>
                            <p>领取了<?php echo $val['money'] ?>元代金券</p>
                        <?php } elseif ($val['type'] == COUPON_TYPE_DISCOUNT) { ?>
                            <p>领取了<?php echo $val['discount'] * 10 ?>折折扣券</p>
                        <?php } elseif ($val['type'] == COUPON_TYPE_EXCHANGE) { ?>
                            <p>领取了<?php echo $val['title'] ?></p>
                        <?php } ?>
                    </dd>
                </dl>
            <?php } ?>
        </section>
    <?php } ?>
    <section class="couponsEnd">
        <img src="<?php echo USER_STATIC_IMAGES ?>user/logo-bottom.png">
    </section>
</section>

<!--关注二维码-->
<div class="pop attention" style="display: none" id="attention">
    <div class="pop_con">
        <div class="title">您可关注微信公众号"公众号"，在会员中查看优惠券</div>
        <div class="ewmimg"><img src="<?php echo IMG_GJ_LIST . $coupon_model['wechat_qrcode'] ?>"></div>
        <div class="remind">长按图片，"识别图中二维码"或扫描关注</div>
    </div>
</div>

<!--分享弹出框-->
<div id="popShadow" style="display: none" class="Shadow">
    <div class="popShare" style="display: none">
        <div class="content">
            <p>请点击右上角</p>
            <p>通过分享功能</p>
            <p>把消息告诉小伙伴吧~</p>
        </div>
        <div class="arrow"></div>
    </div>
</div>
<!--end-->

</body>

<script type="text/javascript">

    <?php if (empty($flag)) { ?>
    $('#addCard').hide();
    $('#add_ready').show();
    <?php }?>

    //二维码
    $('#erWeiMa').click(function () {
        $('#attention').show();
        $('#popShadow').show();
    })

    $('#attention').click(function () {
        $('#attention').hide();
        $('#popShadow').hide();
    })

    //分享提示
    $('.share').click(function () {
        $('.Shadow').show();
        $('.popShare').show();
    });

    $('.Shadow').click(function () {
        $('.Shadow').hide();
        $('.popShare').hide();
        $('#attention').hide();
        $('#popShadow').hide();
    });

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

    wx.hideMenuItems({
        menuList: ['copyUrl'] // 要隐藏的菜单项(复制链接)
    });

    wx.ready(function () {
        //
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ],
            success: function (res) {
//                 alert(JSON.stringify(res));
            }
        });
        //隐藏右上角功能接口
        <?php if ($coupon_model['if_share'] == IF_SHARE_NO) { ?>
        wx.hideOptionMenu();
        <?php } ?>
        //显示右上角功能接口
        <?php if ($coupon_model['if_share'] == IF_SHARE_YES) { ?>
        wx.showOptionMenu();
        <?php } ?>
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
        //添加卡券
        document.querySelector('#addCard').onclick = function () {
            wx.addCard({
                cardList: [
                    {
                        cardId: "<?php echo $coupon_model['card_id']?>",
                        cardExt: '{"timestamp":"<?php echo $cardSign["timestamp"];?>","signature":"<?php echo $cardSign["signature"];?>"}'
                    }
                ],
                success: function (res) {
// 				  alert('已添加卡券：' + JSON.stringify(res.cardList));
                    $('#addCard').hide();
                    $('#add_ready').show();

                    $.ajax({
                        url: '<?php echo Yii::app()->createUrl('uCenter/coupon/wxAddCardFlag')?>',
                    });

                }
            });
        };
    });


</script>
