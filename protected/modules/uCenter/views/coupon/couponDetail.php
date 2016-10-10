
<head>
	<title>优惠券详情</title>
</head>
<body>
<header class="couponsHead couponsbg02 <?php echo $data -> color?>">
	<div class="headImg clearfix">
    	<div class="img"><img src="<?php echo IMG_GJ_LIST.$data -> logo_img?>"></div>
        <div class="name"><?php echo empty($data -> merchant_short_name)?$data -> name:$data -> merchant_short_name;?></div>
    </div>
    <div class="con">
    	<h3><?php echo $data -> title?></h3>
        <h4><?php echo $data -> vice_title?></h4>
        <div class="time">有效期：<?php echo date('Y.m.d',strtotime($data -> start_time))?>&nbsp;-&nbsp;<?php echo date('Y.m.d',strtotime($data -> end_time))?></div>
    </div>
</header>
<section class="couponsDetails">
<section class="detailsCon">
	<div class="ewm"><img src="<?php echo Yii::app() -> createUrl('uCenter/User/CreateBarcode',array('text' => $data -> code));?>"></div>
    <div class="num"><?php echo $code?></div>
    <div class="remind"><?php echo $data -> prompt?></div>
</section>

<article class ="mainWrap clearfix">
	<div class="title">优惠券详情</div>
    <div class="con">
    	<div class="filed">
        	<span class="label">优惠说明：</span>
            <span class="text"><?php echo $data -> discount_illustrate?></span>
        </div>
        <div class="filed">
        	<span class="label">有效日期：</span>
            <span class="text"><?php echo date('Y.m.d',strtotime($data -> start_time))?>-<?php echo date('Y.m.d',strtotime($data -> end_time))?></span>
        </div>
        <div class="filed">
        	<span class="label">客服电话：</span>
            <a class="intergral" href="tel:<?php echo $data -> tel?>">  <span class="text"><em><?php echo $data -> tel?></em></span>  </a>
        </div>
        <div class="filed">
        	<span class="label">使用说明：</span>
            <span class="text">
	            	<p>每人限领<?php echo $data->receive_num?>张</p>
	            	<p>该券<?php echo $GLOBALS['IF_WITH_USERDISCOUNT'][$data->if_with_userdiscount] ?>与优惠券折扣同时享用</p>
	            	<p>使用门店：<?php echo str_replace(',', ' ', $store_name)?></p>
	            	<p><?php echo str_replace('/n', '<br>', $data->use_illustrate)?></p>
            </span>
        </div>
    </div>
</article>
<article class="empty"></article>
<section class="couponsEnd">
	<img src="<?php echo USER_STATIC_IMAGES?>user/logo-bottom.png">
</section>
</section>
</body>

<script type="text/javascript">

	//分享提示
	$('.share').click(function(){
		$('.Shadow').show();
		$('.popShare').show();
	});

	$('.Shadow').click(function(){
		$('.Shadow').hide();
		$('.popShare').hide();
	});
	
	//微信js配置
	wx.config({
	   	debug:false,
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
//                 alert(JSON.stringify(res));
            }
        });
		//分享到朋友圈
		wx.onMenuShareTimeline({
	    	title: '<?php echo $data -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
	    	link: '<?php echo 'wap.test.51wanquan.com/uCenter/coupon/newGetCouponOne?coupon_id='.$data->coupons_id;?>', // 分享链接
	    	imgUrl: '<?php if(!empty($data -> logo_img)){echo IMG_GJ_LIST.$data -> logo_img;}?>', // 分享图标
	    	success: function () { 
	        // 用户确认分享后执行的回调函数
	    	},
	    	cancel: function () { 
	        	// 用户取消分享后执行的回调函数
	    	}
		});
		//分享给朋友
		wx.onMenuShareAppMessage({
	    	title: '<?php echo $data -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
	    	desc: '线下支付，出示会员手机号即可使用优惠券和红包', // 分享描述
	    	link: '<?php echo 'wap.test.51wanquan.com/uCenter/coupon/newGetCouponOne?coupon_id='.$data->coupons_id;?>', // 分享链接
	    	imgUrl: '<?php if(!empty($data -> logo_img)){echo IMG_GJ_LIST.$data -> logo_img;}?>', // 分享图标
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
		    title: '<?php echo $data -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!', // 分享标题
		    desc: '线下支付，出示会员手机号即可使用优惠券和红包', // 分享描述
		    link: '<?php echo 'wap.test.51wanquan.com/uCenter/coupon/newGetCouponOne?coupon_id='.$data->coupons_id;?>', // 分享链接
		    imgUrl: '<?php if(!empty($data -> logo_img)){echo IMG_GJ_LIST.$data -> logo_img;}?>', // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		       // 用户取消分享后执行的回调函数
		    }
		});
	});
	
</script>