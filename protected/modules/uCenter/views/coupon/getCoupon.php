<body>

<div class="couponGet">
	<header>
    	<h1>
        	<img src="<?php if(empty($onlineshop -> logo_img)){?><?php echo USER_STATIC_IMAGES?>logo.png<?php }else{?><?php echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>">
            <span class="name"><?php echo $onlineshop -> name?></span>
        </h1>
        <a class="share">分享</a>
    </header>	
    <div class="bd clearfix">
    	<h2>送你<?php if($couponinfo -> type == COUPON_TYPE_REDENVELOPE){echo '一个红包';}else{echo '一张优惠券';}?></h2>
        <?php echo CHtml::beginForm(Yii::app()->createUrl('uCenter/coupon/ReceiveCoupons'),'get');?> 
        <?php if($is_login == false){?>
        	<input type="text" class="txt" placeholder="请输入手机号" name="phonenum">
        	<?php }else{?>
        	<input value="<?php echo $coupon_id?>" style="display: none" name="coupon_id">
        	<?php }?>
            <input <?php if($is_login == false){?>type="button"<?php }else{?>type="submit"<?php }?> value="马上领取" class="btn_red" id="receive">
        <?php echo CHtml::endForm();?>
    </div>
    <div class="clear"></div>
    <div class="use"><a href="javascript:void(0)" id="searchJg"><em></em>使用说明</div>
    <div class="remarkLogo">
    	<img src="<?php echo USER_STATIC_IMAGES?>logo1.png">
        <p>玩券提供技术支持</p>
    </div>
</div>

<!--弹出框-->
<div id="popShadow" style="display: none"></div>
<div class="pop" style="display: none">
	<div class="pop_con">
    	<!--文字说明-->
    	<div class="pop_content" >
        	<a><div class="popCancel"></div></a>
            <div class="title">使用说明</div>
            <?php $arr = explode('#',$couponinfo -> use_illustrate);?>
            <?php foreach ($arr as $k => $v){?>
            <p><?php echo $v?></p>
            <?php }?>
		</div>
        <!--登录框-->
        <form action="<?php echo Yii::app()->createUrl('uCenter/coupon/Login')?>" class="login" style="display:none">
        	<div class="error">该账号注册过，请直接登录</div>
            <div class="label bg">
            	<div class="dt">手机号</div>
                <div class="dd"><input type="text" class="txt" value="" name="phone"></div>
            </div>
            <input value="<?php echo $coupon_id?>" style="display: none" name="coupon_id">
            <input value="<?php echo $merchant_id?>" style="display: none" name="merchant_id">
            <div class="label coding">
            	<div class="dt">验证码</div>
                <div class="dd">
                	<input type="text" class="txt" placeholder="输入短信验证码" name="code">
                	<a href="javascript:;" onclick="onMobileMsg()" id="getmessage">获取验证码</a><!--或写"等待57秒"-->
                </div>
            </div>
            <div class="label">
            	<div class="dt">密码</div>
                <div class="dd"><input type="password" class="txt" placeholder="请输入登录密码" name="pwd"></div>
            </div>
            <div class="btn">
            	<a class="btn_red" id="checkbutton">确认</a>
            	<a href="#" class="forget" style="display: none">忘记密码</a>
            </div>
        </form>
	</div>
</div>

<!--弹出框-->
<div id="popShadow1" style="display:none"></div>
<div id="instructions" class="pop" style="display:none">
    <div class="pop_con">
        <div class="pop_content">
            <a href="javascript:void(0)" id="close"><div class="popCancel"></div></a>
            <?php  if(!empty($coupons)){ ?>
            <p>
                <?php if (!empty($coupons['validtime_fixed_value'])) { ?>
                    1.有效期：领取之后<?php echo $coupons['validtime_fixed_value']?>天内有效
                <?php }else { ?>
                    1.有效期：<?php echo $coupons['validtime_start']?>至<?php echo $coupons['validtime_end']?>
                <?php } ?>
            </p>
            <p>
                2. 单个用户（同一支付宝账户、同一设备、同一手机号、同一身份证号的，满足前述任一条件均视为同一用户）最多可领<?php echo $coupons['receive_num']?>张券;
            </p>
            <p>
                <?php if ($coupons['type'] == COUPON_TYPE_DISCOUNT) { ?>
                    3. 本券仅在单笔消费金额大于或等于<?php echo $coupons['min_pay_money']?>元时，即可被使用（自动抵扣优惠金额）;
                <?php } elseif ($coupons['type'] == COUPON_TYPE_EXCHANGE){?>
                    3. 本券无需消费即可兑换相应物品;
                <?php }else{ ?>
                    3. 本券仅在单笔消费金额大于或等于券面额时，可被使用（自动抵扣优惠金额）
                <?php } ?>
            </p>
            <p>
                4. 本券在有效期内最多可使用<?php echo $coupons['use_num']?>次;
            </p>
            <p>
                <?php if ($coupons['if_with_userdiscount'] == IF_WITH_USERDISCOUNT_YES) { ?>
                    5. 本券可与会员折扣同时使用。
                <?php }else {?>
                    5. 本券不可可与会员折扣同时使用。
                <?php } ?>
            </p>
            <p>
                <?php if ($coupons['if_with_coupons'] == IF_WITH_COUPONS_YES) { ?>
                    6. 本券可与其他优惠券同时使用。
                <?php }else {?>
                    6. 本券不可可与会其他优惠券同时使用。
                <?php } ?>
            </p>
            <p>
                <?php if($coupons['refund_deal'] == REFUND_DEAL_NOTREFUND) { ?>
                    7. 如果订单发生退款，优惠券无法退还;
                <?php } else {?>
                    7. 如果订单发生退款，退还优惠券;
                <?php } ?>
            </p>
            <?php }else{?>
                <p>
                    1.单个用户（同一支付宝账户、同一设备、同一手机号、同一身份证号的，满足前述任一条件均视为同一用户）
                </p>
            <?php }?>
        </div>
    </div>
</div>
<!--end-->

<!--分享弹出框-->
<div id="popShadow" style="display: none" class="Shadow">
	<div class="popShare" style="display: none">
    	<div class="content">
        	<p>请点击右上角</p>
            <p>通过分享功能</p>
            <p>把消息告诉小伙伴吧~</p>
        </div>
    	<a  class="close">×</a>
        <div class="arrow"></div>
    </div>
</div>
<!--end-->


<script>
<?php if($errorL == 2){?>
$('#popShadow').show();
$('.pop').show();
$('.pop_content').hide();
$('.coding').hide();
$('.login').show();
$('input[name=phone]').val(<?php echo $phone?>);
$('.error').html('该账号注册过，请直接登录');
<?php }?>

<?php if($errorR == 2){?>
$('#popShadow').show();
$('.pop').show();
$('.pop_content').hide();
$('.login').show();
$('input[name=phone]').val(<?php echo $phone?>);
$('.error').html('注册成为会员');
<?php }?>


//显示使用说明
$('.description').click(function(){
	$('#popShadow').show();
	$('.pop').show();
});

//关闭使用说明
$('.popCancel').click(function(){
	$('#popShadow').hide();
	$('.pop').hide();
});

//立马领取
$('.btn_red').click(function(){
	var phonenum = $('input[name=phonenum]').val();
	var merchant_id = '<?php echo $merchant_id?>';
	$.ajax({
        url: '<?php echo(Yii::app()->createUrl('uCenter/coupon/IsRegist'));?>',
        data: {phonenum: phonenum, merchant_id: merchant_id},
        type: 'post',
        dataType: 'json',
        success: function (data) {
            if(data.data == true){
            	$('#popShadow').show();
            	$('.pop').show();
            	$('.pop_content').hide();
            	$('.coding').hide();
            	$('.login').show();
            	$('input[name=phone]').val(phonenum);
            	$('.error').html('该账号注册过，请直接登录');
            }else{
            	$('#popShadow').show();
            	$('.pop').show();
            	$('.pop_content').hide();
            	$('.login').show();
            	$('input[name=phone]').val(phonenum);
            	$('.error').html('注册成为会员');
            }
        }
	});
});

//登录、注册（确认）
$('#checkbutton').click(function(){
	var phone = $('input[name=phone]').val();
	if(phone == ''){
		alert('请输入手机号');
		return false;
	}else{
		var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
		if(!myreg.test(phone)) 
		{ 
		    alert('请输入有效的手机号码'); 
		    return false;
		} 
	}
	var pwd = $('input[name=pwd]').val();
	if(pwd == ''){
		alert('请输入密码');
		return false;
	}
	$('.login').submit();

});

//验证码发送计时
var mins = 29;
var intervalid;
function ctrlTime(){
    if(mins == 0){
        clearInterval(intervalid);
        $('#getmessage').html('获取验证码');
        $("#getmessage").attr("onclick", 'onMobileMsg()');
        mins = 29;
        return;
    }
    $('#getmessage').html(mins);
    mins--;
}
//获取验证码
function onMobileMsg(){
	var mobile = $('input[name=phone]').val();
  	var reg = /^(13|15|18|14)\d{9}$/;
    if(!reg.test(mobile)) {
    	alert('请填写正确的手机号!');
    }else{
        $.ajax({
            url : '<?php echo Yii::app()->createUrl('uCenter/user/sendMsgPassword');?>?' + new Date().getTime(),
            data : {mobile : mobile, check : 'yes'},
            dataType: "json",
            type : 'post',
            async : false,
            success : function(res){
                if(res.status == '<?php echo ERROR_NONE?>'){
               		intervalid = setInterval("ctrlTime()", 1000);
                    $("#getmessage").removeAttr("onclick"); 
                } else {
                    alert(res.errMsg);
                    $("#getmessage").attr("onclick", 'onMobileMsg()');
                }
            }
        });
    }
}

<?php if(Yii::app()->session['source'] == 'alipay_wallet'){?>
$('.share').click(function(){
	AlipayJSBridge.call('share', {

	     'channels': [{
	       name: 'Weibo', //新浪微博
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true, //分享当前屏幕截图(和imageUrl同时存在时，优先imageUrl)
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>' //分享跳转的url，当添加此参数时，分享的图片大小不能超过32K
	       }
	     }, {
	       name: 'LaiwangContacts', //来往好友
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true,
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>'
	       }
	     }, {
	       name: 'Weixin', //微信
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true, 
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>' 
	       }
	     }, {
	       name: 'WeixinTimeLine', //微信朋友圈
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true, 
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>' 
	       }
	     },{
	       name: 'SMS', //短信
	       param: {
	           content: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon?coupons_id='.$coupon_id;?>',
	           //应业务方需求定制功能
	           contentType: '',
	           extData:''
	       }
	     }, {
	       name: 'CopyLink', //复制链接
	       param: {
	           url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>'
	       }
	     }]
	},function(result){
	});
});

<?php }elseif (Yii::app()->session['source'] == 'wechat'){?>


//分享提示
$('.share').click(function(){
	$('.Shadow').show();
	$('.popShare').show();
});

$('.close').click(function(){
	$('.Shadow').hide();
	$('.popShare').hide();
});
<?php }?>

$("#searchJg").click(function(){
    $("#popShadow1").show();
    $("#instructions").show();
})

$("#close").click(function(){
    $("#popShadow1").hide();
    $("#instructions").hide();
})

//微信js配置
wx.config({
   	debug:false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      "onMenuShareTimeline",
      "onMenuShareAppMessage",
      "onMenuShareQQ",
    ]
  });

wx.ready(function () {
	//分享到朋友圈
	wx.onMenuShareTimeline({
    	title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
    	link: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>', // 分享链接
    	imgUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>', // 分享图标
    	success: function () { 
        // 用户确认分享后执行的回调函数
    	},
    	cancel: function () { 
        	// 用户取消分享后执行的回调函数
    	}
	});
	//分享给朋友
	wx.onMenuShareAppMessage({
    	title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
    	desc: '线下支付，出示会员手机号即可使用优惠券和红包', // 分享描述
    	link: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>', // 分享链接
    	imgUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>', // 分享图标
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
	    title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
	    desc: '线下支付，出示会员手机号即可使用优惠券和红包', // 分享描述
	    link: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupon_id;?>', // 分享链接
	    imgUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>', // 分享图标
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