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
<section class="couponsCon">
    <a href="<?php echo Yii::app()->createUrl('uCenter/coupon/newCouponDetail', array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id, 'marketing_activity_type' => $marketing_activity_type, 'marketing_activity_id' => $marketing_activity_id)) ?>"
       class="intergral"><span class="text">优惠券详情</span><span class="jt"></span></a>
</section>
<section class="couponsWrap clearfix">
    <?php echo CHtml::beginForm() ?>
    <div class="details">
        <?php echo CHtml::textField('tel', !empty($mobil_phone) ? $mobil_phone : $tel, array('class' => "txt", 'placeholder' => "请输入手机号")) ?>
        <input style="display: none"
               value="<?php echo isset($marketing_activity_type) ? $marketing_activity_type : ''; ?>"
               name="marketing_activity_type">
        <input style="display: none"
               value="<?php echo isset($marketing_activity_type) ? $marketing_activity_id : ''; ?>"
               name="marketing_activity_id">
        <input type="button" class="btn_com" value="立即领取" onclick="getCoupon()">
    </div>
    <?php echo CHtml::endForm() ?>
</section>
<section class="couponsEnd">
    <img src="<?php echo USER_STATIC_IMAGES ?>user/logo-bottom.png">
</section>

<!-- 阴影效果 -->
<?php if (!empty($user) || !empty($alogin) || !empty($plogin) || !empty($forget)) { ?>
    <div id="popShadow"></div>
<?php } else { ?>
    <div id="popShadow" style="display: none"></div>
<?php } ?>

<!--注册-->
<?php if (!empty($user)) { ?>
<div class="pop login" id="regist">
    <?php }else{ ?>
    <div class="pop login" id="regist" style="display: none">
        <?php } ?>
        <div class="pop_con clearfix">
            <a href="#" class="close">×</a>
            <?php echo CHtml::beginForm(Yii::app()->createUrl($this->route, array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id, 'marketing_activity_type' => $marketing_activity_type, 'marketing_activity_id' => $marketing_activity_id)), 'post', array('id' => 'form_regist')) ?>
            <div class="error red"><em>该账号未注册,请先注册成为玩券会员</em>授权并登录该商户</div>
			<span class="username">
				<?php echo CHtml::textField('regist_tel', $mobil_phone, array('class' => "txt", 'disabled' => "disabled")) ?>
                <?php echo CHtml::hiddenField('User[MobilePhone]', $mobil_phone, array('class' => "txt")) ?>
			</span>
            <div class="mod">
                <span class="yzm">
                	<?php echo CHtml::textField('User[MsgPassword]', !empty($user['MsgPassword']) ? $user['MsgPassword'] : '', array('class' => "txt", 'placeholder' => "请输入验证码")) ?>
                </span>
                <span class="hqyzm">
                	<a href="javascript:;" onclick="onMobileMsg('yes')" class="btn_com_gray" id="getMsgPwd">获取验证码</a>
                </span>
            </div>
            <span class="password">
            	<?php echo CHtml::passwordField('User[Password]', !empty($user['Password']) ? $user['Password'] : '', array('placeholder' => "请输入密码", 'class' => 'txt')) ?>
            </span>
            <span class="password">
            	<?php echo CHtml::passwordField('User[Confirm]', !empty($user['Confirm']) ? $user['Confirm'] : '', array('placeholder' => "请再次输入密码", 'class' => 'txt')) ?>
            </span>
            <div class="bottom clearfix">
                <input type="button" value="注册" class="btn_com" onclick="regist()">
            </div>
            <?php echo CHtml::endForm() ?>
        </div>
    </div>

    <!--账号密码登录 -->
    <?php if (!empty($alogin)) { ?>
    <div class="pop login" id="account_login">
        <?php }else{ ?>
        <div class="pop login" id="account_login" style="display: none">
            <?php } ?>
            <div class="pop_con clearfix">
                <a href="#" class="close">×</a>
                <?php echo CHtml::beginForm(Yii::app()->createUrl($this->route, array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id, 'marketing_activity_type' => $marketing_activity_type, 'marketing_activity_type' => $marketing_activity_type)), 'post', array('id' => 'form_account_login')) ?>
                <div class="error red"><em>该账号已注册,请直接登录</em>授权并登录该商户</div>
			<span class="username">
				<?php echo CHtml::textField('alogin_tel', $mobil_phone, array('class' => "txt", 'disabled' => "disabled")) ?>
                <?php echo CHtml::hiddenField('Alogin[MobilePhone]', $mobil_phone, array('class' => "txt")) ?>
			</span>
        	<span class="password">
        		<?php echo CHtml::passwordField('Alogin[Password]', !empty($alogin['Password']) ? $alogin['Password'] : '', array('placeholder' => "请输入密码", 'class' => 'txt')) ?>
            </span>
                <div class="bottom clearfix">
                    <button class="btn_com">登录</button>
                </div>
                <?php echo CHtml::endForm() ?>
                <div class="end clearfix">
                    <a href="javascript:;" class="forget" onclick="forgetPassword()">忘记密码</a>
                    <a href="javascript:;" class="registe" onclick="mobilePhoneLogin()">手机登录</a>
                </div>
            </div>
        </div>

        <!-- 手机号登录 -->
        <?php if (!empty($plogin)) { ?>
        <div class="pop login" id="phone_login">
            <?php }else{ ?>
            <div class="pop login" id="phone_login" style="display: none">
                <?php } ?>
                <div class="pop_con clearfix">
                    <a href="#" class="close">×</a>
                    <?php echo CHtml::beginForm(Yii::app()->createUrl($this->route, array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id, 'marketing_activity_type' => $marketing_activity_type, 'marketing_activity_id' => $marketing_activity_id)), 'post', array('id' => 'form_phone_login')) ?>
                    <div class="error red"><em>该账号已注册,请直接登录</em>授权并登录该商户</div>
			<span class="username">
				<?php echo CHtml::textField('plogin_tel', $mobil_phone, array('class' => "txt", 'disabled' => "disabled")) ?>
                <?php echo CHtml::hiddenField('Plogin[MobilePhone]', $mobil_phone, array('class' => "txt")) ?>
			</span>
        	<span class="yzm">
        		<?php echo CHtml::textField('Plogin[MsgPassword]', !empty($plogin['MsgPassword']) ? $plogin['MsgPassword'] : '', array('class' => "txt", 'placeholder' => "请输入验证码")) ?>
            </span>
            <span class="hqyzm">
              	<a href="javascript:;" onclick="onMobileMsg('no')" class="btn_com_gray" id="getMsgPwd">获取验证码</a>
            </span>
                    <div class="bottom clearfix">
                        <button class="btn_com">登录</button>
                    </div>
                    <?php echo CHtml::endForm() ?>
                    <div class="end clearfix">
                        <a href="javascript:;" class="registe" onclick="accountLogin()">密码登录</a>
                    </div>
                </div>
            </div>


            <!--忘记密码-->
            <?php if (!empty($forget)) { ?>
            <div class="pop login" id="forget_password">
                <?php }else{ ?>
                <div class="pop login" id="forget_password" style="display: none">
                    <?php } ?>
                    <div class="pop_con clearfix">
                        <a href="#" class="close">×</a>
                        <?php echo CHtml::beginForm(Yii::app()->createUrl($this->route, array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id, 'marketing_activity_type' => $marketing_activity_type, 'marketing_activity_id' => $marketing_activity_id)), 'post', array('id' => 'form_forget_login')) ?>
                        <div class="error red">忘记密码</div>
			<span class="username">
				<?php echo CHtml::textField('forget_tel', $mobil_phone, array('class' => "txt", 'disabled' => "disabled")) ?>
                <?php echo CHtml::hiddenField('Forget[MobilePhone]', $mobil_phone, array('class' => "txt")) ?>
			</span>
                        <div class="mod">
                <span class="yzm">
	        		<?php echo CHtml::textField('Forget[MsgPassword]', !empty($forget['MsgPassword']) ? $forget['MsgPassword'] : '', array('class' => "txt", 'placeholder' => "请输入验证码")) ?>
	            </span>
	            <span class="hqyzm">
	              	<a href="javascript:;" onclick="onMobileMsg('no')" class="btn_com_gray" id="getMsgPwd">获取验证码</a>
	            </span>
                        </div>
            <span class="password">
            	<?php echo CHtml::passwordField('Forget[Password]', !empty($forget['Password']) ? $forget['Password'] : '', array('placeholder' => "请输入密码", 'class' => 'txt')) ?>
            </span>
            <span class="password">
            	<?php echo CHtml::passwordField('Forget[Confirm]', !empty($forget['Confirm']) ? $forget['Confirm'] : '', array('placeholder' => "请再次输入密码", 'class' => 'txt')) ?>
            </span>
                        <div class="bottom clearfix">
                            <input type="button" value="忘记密码" class="btn_com" onclick="forget()">
                        </div>
                        <?php echo CHtml::endForm() ?>
                    </div>
                </div>
                <?php if (Yii::app()->user->hasFlash('error')) { ?>
                    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
                <?php } ?>
</body>

<script type="text/javascript">

    $('.close').click(function () {
        $(this).parent().parent().hide();
        $('#popShadow').hide();
    })

    //点击立即领取按钮
    function getCoupon() {
        var tel = $('#tel').val();
        var wx_tel = <?php echo json_encode($tel)?>;

        var re = /^(13|15|18|17)\d{9}$/;
        if (tel.length == 0) {
            alert("请填写手机号码")
        } else {
            if (!re.test(tel)) {
                alert("请输入正确的手机号码")
            } else {
                //是否与之前登陆账号一致
                if (tel == wx_tel) {   //一致  直接跳到领奖页面，无需登陆
                    location.href = "<?php echo Yii::app()->createUrl('uCenter/coupon/newGetCouponTwo', array('coupon_id' => $coupon_id, 'encrypt_id' => $encrypt_id, 'user_id' => Yii::app()->session['user_id'], 'account' => $tel, 'marketing_activity_type' => $marketing_activity_type, 'marketing_activity_id' => $marketing_activity_id))?>";
                } else {
                    //判断改手机是否需要注册
                    //注册
                    $('#User_MobilePhone').val(tel);
                    $('#regist_tel').val(tel);
                    //账号登陆
                    $('#Alogin_MobilePhone').val(tel);
                    $('#alogin_tel').val(tel);
                    //手机登陆
                    $('#Plogin_MobilePhone').val(tel);
                    $('#plogin_tel').val(tel);
                    //忘记密码
                    $('#Forget_MobilePhone').val(tel);
                    $('#forget_tel').val(tel);
                    $.post('<?php echo(Yii::app()->createUrl('uCenter/user/isexist'));?>?', {
                        account: tel,
                        encrypt_id: '<?php echo $encrypt_id ?>'
                    }, function (data) {
                        if ('exist' == data.result) {  //账号已存在，直接登录
                            $('#account_login').show();
                            $('#popShadow').show();
                        } else {         //账号不存在,需要注册
                            $('#regist').show();
                            $('#popShadow').show();
                        }
                    }, 'json');
                }
            }
        }
    }

    //注册
    function regist() {
        var mobile = $('input[name = User\\[MobilePhone\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        var flag = true;
        if ('' == mobile) {
            alert('手机号码不能为空！');
            flag = false;
        }
        var reg = /^(13|15|18|14)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号');
            flag = false;
        }
        var msgpwd = $('input[name = User\\[MsgPassword\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        if ('' == msgpwd) {
            alert('短信验证码不能为空！');
            flag = false;
        }
        var password = $('input[name = User\\[Password\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        if ('' == password) {
            alert('密码不能为空！');
            flag = false;
        }
        var confirm = $('input[name = User\\[Confirm\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        if ('' == confirm) {
            alert('确认密码不能为空！');
            flag = false;
        }
        if (password != confirm) {
            alert('两次密码输入的不一样！');
            flag = false;
        }

        if (flag) {
            $('#form_regist').submit();
        }
    }

    //忘记密码
    function forget() {
        var mobile = $('input[name = Forget\\[MobilePhone\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        var flag = true;
        if ('' == mobile) {
            alert('手机号码不能为空！');
            flag = false;
        }
        var reg = /^(13|15|18|14)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号');
            flag = false;
        }
        var msgpwd = $('input[name = Forget\\[MsgPassword\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        if ('' == msgpwd) {
            alert('短信验证码不能为空！');
            flag = false;
        }
        var password = $('input[name = Forget\\[Password\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        if ('' == password) {
            alert('密码不能为空！');
            flag = false;
        }
        var confirm = $('input[name = Forget\\[Confirm\\]]').val().replace(/(^\s*)|(\s*$)/g, "");
        if ('' == confirm) {
            alert('确认密码不能为空！');
            flag = false;
        }
        if (password != confirm) {
            alert('两次密码输入的不一样！');
            flag = false;
        }

        if (flag) {
            $('#form_forget_login').submit();
        }
    }

    //账号登陆-手机登陆
    function mobilePhoneLogin() {
        $('#Alogin_Password').val("");
        $('#account_login').hide();
        $('#phone_login').show();
    }

    //账号登陆-忘记密码
    function forgetPassword() {
        $('#Alogin_Password').val("");
        $('#account_login').hide();
        $('#forget_password').show();
    }

    //账号登录
    function accountLogin() {
        $('#Plogin_Password').val("");
        $('#account_login').show();
        $('#phone_login').hide();
    }

</script>
<script language="JavaScript">
    var mins = 59;
    var intervalid;
    function ctrlTime() {
        if (mins == 0) {
            clearInterval(intervalid);
            $('.btn_com_gray').html('获取验证码');
            $('.btn_com_gray').attr("onclick", 'onMobileMsg()');
            mins = 59;
            return;
        }
        $('.btn_com_gray').html(mins + '秒后重新获取');
        mins--;
    }

    function onMobileMsg(check) {
        var mobile = $('#User_MobilePhone').val().replace(/(^\s*)|(\s*$)/g, "");
        var reg = /^(13|15|18|14)\d{9}$/;
        if (!reg.test(mobile)) {
            alert('请填写正确的手机号!');
        } else {
            $.ajax({
                url: '<?php echo Yii::app()->createUrl('uCenter/user/sendMsgPassword');?>?' + new Date().getTime(),
                data: {mobile: mobile, check: check, encrypt_id: '<?php echo $encrypt_id ?>'},
                dataType: "json",
                type: 'post',
                async: false,
                success: function (res) {
                    if (res.status == '<?php echo ERROR_NONE?>') {
                        intervalid = setInterval("ctrlTime()", 1000);
                        $("#getMsgPwd").removeAttr("onclick");
                    } else {
                        alert(res.errMsg);
                        $("#getMsgPwd").attr("onclick", 'onMobileMsg()');
                    }
                }
            });
            intervalid = setInterval("ctrlTime()", 1000);
            $(".btn_com_gray").removeAttr("onclick");
        }
    }
</script>

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
    });
</script>
