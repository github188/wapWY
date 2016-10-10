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
            <?php if ($coupon['if_invalid'] == IF_INVALID_NO) {
                if (strtotime($coupon['end_time']) > time() || $coupon['time_type'] == VALID_TIME_TYPE_RELATIVE) {
                    if (!empty($jug_res)) {
                        if ($jug_res['status'] == ERROR_NONE) { ?>
                            <div>
                                <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary"
                                   id="<?php if (empty(Yii::app()->session[$coupon['encrypt_id'] . 'user_id'])) {
                                       echo 'show-reg';
                                   } else {
                                       echo 'receive-coupons';
                                   } ?>">
                                    立即领取
                                </a>
                            </div>
                        <?php } else { ?>
                            <div>
                                <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default">
                                    <?php echo $jug_res['errMsg'] ?>
                                </a>
                            </div>
                       <?php }
                    } else { ?>
                        <div>
                            <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary"
                               id="<?php if (empty(Yii::app()->session[$coupon['encrypt_id'] . 'user_id'])) {
                                   echo 'show-reg';
                               } else {
                                   echo 'receive-coupons';
                               } ?>">
                                立即领取
                            </a>
                        </div>
                    <?php }
                } else { ?>
                    <div>
                        <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default">
                            已过期
                        </a>
                    </div>
                <?php }
            } elseif ($coupon['if_invalid'] == IF_INVALID_YES) { ?>
                <div>
                    <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_default">
                        已失效
                    </a>
                </div>
            <?php } ?>
            <!--<p class="weui_media_desc wq_media_desc_al"><span class="hd">使用条件：</span><span class="bd">
                    <?php /*echo $coupon['use_illustrate'] */?>
                </span>
            </p>-->
            <p class="weui_media_desc wq_media_desc_al"><span class="hd">可用时间：</span><span class="bd">
                    <?php if ($coupon['time_type'] == VALID_TIME_TYPE_FIXED) {
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

<!--登录弹出框-->
<div id="login_div" class="popWrap" style="display: none">
    <div class="wq_panel">
        <div class="weui_cells_title"><i class="weui_icon_cancel popClose"></i>请登录</div>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">手机号码</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input id="mobile" class="weui_input" type="number" pattern="[0-9]*" placeholder="请输入手机号码">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">密码</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input id="password" class="weui_input" type="password" placeholder="请输入密码">
                </div>
            </div>
        </div>
        <div class="weui_btn_area login_area">
            <a class="weui_btn weui_btn_primary" href="javascript:;" id="login_btn">登录</a>
            <a href="javascript:;" class="weui_btn weui_btn_default reg_a">注册</a>
        </div>
    </div>
</div>

<!--注册弹出框-->
<div id="reg_div" class="popWrap" style="display: none">
    <div class="wq_panel">
        <div class="weui_cells_title"><i class="weui_icon_cancel popClose"></i>请注册</div>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <input id="mobile" class="weui_input" type="number" pattern="[0-9]*" maxlength="11"
                           placeholder="请输入手机号码">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <input id="msgpassword" class="weui_input" type="number" pattern="[0-9]*" placeholder="请输入验证码">
                </div>
                <div class="weui_cell_ft"><a href="javascript:;" id="vcode" class="vcode"
                                             onclick="onMobileMsg()">获取验证码</a></div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <input id="password" class="weui_input" type="password" placeholder="请输入密码">
                </div>
            </div>
        </div>
        <div class="weui_btn_area login_area">
            <a class="weui_btn weui_btn_primary" href="javascript:;" id="reg_btn">注册</a>
            <a href="javascript:;" class="weui_btn weui_btn_default login_a">登录</a>
        </div>
    </div>
</div>

<script>
    //弹出注册框
    $('#show-reg').click(function () {
        $('#reg_div').show();

        $('#reg_div').find('#mobile').val('');
        $('#reg_div').find('#msgpassword').val('');
        $('#reg_div').find('#password').val('');
    });

    //弹出登录框
    $('.login_a').click(function () {
        $('#reg_div').hide();
        $('#login_div').show();

        $('#login_div').find('#mobile').val('');
        $('#login_div').find('#password').val('');
    });

    //弹出注册框
    $('.reg_a').click(function () {
        $('#login_div').hide();
        $('#reg_div').show();

        $('#reg_div').find('#mobile').val('');
        $('#reg_div').find('#msgpassword').val('');
        $('#reg_div').find('#password').val('');
    });

    //登录
    $('#login_btn').click(function () {
        var mobile = $('#login_div').find('#mobile').val();
        var password = $('#login_div').find('#password').val();
        var encrypt_id = '<?php echo $coupon['encrypt_id'] ?>';

        if (mobile == '') {
            alert('请填写手机号');
            return false;
        }

        if (password == '') {
            alert('请填写密码');
            return false;
        }

        $.post('<?php echo Yii::app()->createUrl('mobile/coupon/coupon/login') ?>', {
            mobile: mobile,
            password: password,
            encrypt_id: encrypt_id
        }, function (data) {
            if (data == 'success') {
                window.location.href = '<?php echo Yii::app()->createUrl('mobile/coupon/coupon/ReceiveCoupon', array('qcode' => $coupon['code'], 'encrypt_id' => $coupon['encrypt_id'])) ?>';
            } else {
                alert('账号不正确');
            }
        });
    });

    //注册
    $('#reg_btn').click(function () {
        var mobile = $('#reg_div').find('#mobile').val();
        var msgpassword = $('#reg_div').find('#msgpassword').val();
        var password = $('#reg_div').find('#password').val();
        var encrypt_id = '<?php echo $coupon['encrypt_id'] ?>';

        if (mobile == '') {
            alert('请填写手机号');
            return false;
        }

        var reg = /^(13|15|18|14|17)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号!');
        }

        if (msgpassword == '') {
            alert('请填写验证码');
            return false;
        }

        if (password == '') {
            alert('请填写密码');
            return false;
        }

        $.post('<?php echo Yii::app()->createUrl('mobile/coupon/coupon/register') ?>', {
            mobile: mobile,
            msgpassword: msgpassword,
            password: password,
            encrypt_id: encrypt_id
        }, function (data) {
            if (data == 'success') {
                window.location.href = '<?php echo Yii::app()->createUrl('mobile/coupon/coupon/ReceiveCoupon', array('qcode' => $coupon['code'], 'encrypt_id' => $coupon['encrypt_id'])) ?>';
            } else {
                alert(data);
            }
        });
    });

    var mins = 59;
    var intervalid;
    //获取验证码
    function ctrlTime() {
        if (mins == 0) {
            clearInterval(intervalid);
            $('#vcode').html('获取验证码');
            $("#vcode").attr("onclick", 'onMobileMsg()');
            mins = 59;
            return;
        }
        $('.vcode').html(mins + '秒后重新获取');
        mins--;
    }

    function onMobileMsg() {
        var mobile = $('#reg_div').find('#mobile').val();
        var reg = /^(13|15|18|14|17)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号!');
        } else {
            $.ajax({
                url: '<?php echo Yii::app()->createUrl('mobile/auth/sendMsgPassword');?>?' + new Date().getTime(),
                data: {mobile: mobile, check: 'yes', encrypt_id: '<?php echo $coupon['encrypt_id'] ?>'},
                dataType: "json",
                type: 'post',
                async: false,
                success: function (res) {
                    if (res.status == '<?php echo ERROR_NONE?>') {
                        intervalid = setInterval("ctrlTime()", 1000);
                        $("#vcode").removeAttr("onclick");
                    } else {
                        alert(res.errMsg);
                        $("#vcode").attr("onclick", 'onMobileMsg()');
                    }
                }
            });
        }
    }

    $('#receive-coupons').click(function () {
        window.location.href = '<?php echo Yii::app()->createUrl('mobile/coupon/coupon/ReceiveCoupon', array('qcode' => $coupon['code'], 'encrypt_id' => $coupon['encrypt_id'],'marketing_activity_type' => $marketing_activity_type,'marketing_activity_id'=>$marketing_activity_id)) ?>';
    });

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
