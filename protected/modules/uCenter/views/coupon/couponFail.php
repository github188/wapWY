<body>
<div class="couponGetSucceed">
	<div class="bg">
    	<dl class="msg">
           
        </dl>
        <div class="money">
        	<div class="h"><?php echo $GLOBALS['COUPON_TYPE'][$type]?></div>
            <div class="price"><?php echo $msg?><strong></strong></div>
        </div>
        <div class="desc">
        	<p><em></em></p>
            <p class="time"></p>
            
            <a class="btn_red" id="seecoupon">查看<?php if($type == COUPON_TYPE_REDENVELOPE){echo $GLOBALS['COUPON_TYPE'][$type];}else{echo '优惠券';}?></a>
            <a  class="share">分享给好友&gt;&gt;</a>
        </div>
        <div class="number clearfix">
        	<a href="<?php echo Yii::app()->createUrl('uCenter/user/coupons', array('coupons_status'=>COUPONS_USE_STATUS_UNUSE, 'coupons_type'=>$type)) ?>">
        	<div class="l">
        	<?php if(Yii::app()->session['source'] == 'alipay_wallet'){?>
        		
        		<?php if(!empty($myuser -> alipay_avatar)){?>
        		<img src="<?php echo $myuser -> alipay_avatar;?>">
        		<?php }elseif (!empty($myuser -> avatar)){?>
        		<img src="<?php echo $myuser -> avatar;?>">
        		<?php }else{?>
        		<img src="<?php echo USER_STATIC_IMAGES?>user/face.png">
        		<?php }?>
                
                <?php }elseif (Yii::app()->session['source'] == 'wechat'){?>
                
                <?php if(!empty($myuser -> avatar)){?>
        		<img src="<?php echo $myuser -> avatar;?>">
        		<?php }elseif (!empty($myuser -> alipay_avatar)){?>
        		<img src="<?php echo $myuser -> alipay_avatar;?>">
        		<?php }else{?>
        		<img src="<?php echo USER_STATIC_IMAGES?>user/face.png">
        		<?php }?>
                
                <?php }else{?>
                
                <?php if(!empty($myuser -> avatar)){?>
        		<img src="<?php echo $myuser -> avatar;?>">
        		<?php }elseif (!empty($myuser -> alipay_avatar)){?>
        		<img src="<?php echo $myuser -> alipay_avatar;?>">
        		<?php }else{?>
        		<img src="<?php echo USER_STATIC_IMAGES?>user/face.png">
        		<?php }?>
                
                <?php }?>
                <span>我领到的<?php if($type == COUPON_TYPE_REDENVELOPE){echo $GLOBALS['COUPON_TYPE'][$type];}else{echo '优惠券';}?></span>
            </div>
        	<div class="r">
            	<?php echo $num?>张
            	<div class="arrow"></div>
            </div>
            </a>
        </div>
    </div>
    <div class="otherFriend">
    	<div class="title"><span>看看其他朋友</span></div>
    	<?php foreach ($user as $k => $v){?>
    	<?php if(is_numeric($k)){?>
        <dl>
        <?php if(Yii::app()->session['source'] == 'alipay_wallet'){?>
        	
        	<?php if(!empty($v -> alipay_avatar)){?>
        	<dt><img src="<?php echo $v -> alipay_avatar;?>"></dt>
        	<?php }elseif (!empty($v -> avatar)){?>
        	<dt><img src="<?php echo $v -> avatar;?>"></dt>
        	<?php }else {?>
        	<dt><img src="<?php echo USER_STATIC_IMAGES?>user/face.png"></dt>
        	<?php }?>

        	<?php }elseif (Yii::app()->session['source'] == 'wechat'){?>
        	
        	<?php if(!empty($v -> avatar)){?>
        	<dt><img src="<?php echo $v -> avatar;?>"></dt>
        	<?php }elseif (!empty($v -> alipay_avatar)){?>
        	<dt><img src="<?php echo $v -> alipay_avatar;?>"></dt>
        	<?php }else {?>
        	<dt><img src="<?php echo USER_STATIC_IMAGES?>user/face.png"></dt>
        	<?php }?>
        	
        	<?php }else{?>
        	
        	<?php if(!empty($v -> avatar)){?>
        	<dt><img src="<?php echo $v -> avatar;?>"></dt>
        	<?php }elseif (!empty($v -> alipay_avatar)){?>
        	<dt><img src="<?php echo $v -> alipay_avatar;?>"></dt>
        	<?php }else {?>
        	<dt><img src="<?php echo USER_STATIC_IMAGES?>user/face.png"></dt>
        	<?php }?>
        	
        	<?php }?>
            <dd>
            <?php $arr = explode(' ', $v -> create_time);?>
            	<div class="name">
            	<em><?php echo $v -> nickname?></em> 
            	<?php echo substr($arr['0'],5);?> 
            	<?php echo $arr['1']?>
            	</div>
                <p>领取了<?php echo $v -> name?></p>
            </dd>
        </dl>
        <?php }?>
        <?php }?>
    </div>
    
    <div class="clear"></div>
    <div class="description"><em></em>使用说明</div>
    <div class="remarkLogo">
    	<img src="<?php echo USER_STATIC_IMAGES?>/logo1.png">
        <p>玩券提供技术支持</p>
    </div>
</div>

<!--分享弹出框-->
<div id="popShadow" style="display: none">
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

<!--弹出框-->
<div id="popShadow" style="display: none"></div>
<div class="pop" style="display: none" id="popuse_illustrate">
	<div class="pop_con">
    	<!--文字说明-->
    	<div class="pop_content" >
        	<a><div class="popCancel"></div></a>
            <div class="title">使用说明</div>
            <?php $arr = explode('#',$usercoupon -> use_illustrate);?>
            <?php foreach ($arr as $k => $v){?>
            <p><?php echo $v?></p>
            <?php }?>
		</div>
	</div>
</div>

<!--弹出框-->
<div id="popShadow" style="display: none"></div>
<div class="pop" style="display: none" id="pop_see">
	<div class="pop_con">
    	<!--文字说明-->
    	<div class="pop_content" >
            <div class="title">查看优惠券</div>
            <p>方法一：</p>
            <p>使用支付宝钱包关注商家"<?php echo $onlineshop -> fuwu_name?>"服务</p>
            <p>窗，进入会员中心查看优惠券及红包信息。</p>
            <p>方法二：</p>
            <p>使用微信关注商家"<?php echo $onlineshop -> wechat_name?>"微信公众号</p>
            <p>，进入会员中心查看优惠券及红包信息。</p>
            <div class="btn">
            	<a href="#" class="btn_red">我知道了</a>
            </div>
		</div>
	</div>
</div>


<script>

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
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>' //分享跳转的url，当添加此参数时，分享的图片大小不能超过32K
	       }
	     }, {
	       name: 'LaiwangContacts', //来往好友
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true,
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>'
	       }
	     }, {
	       name: 'Weixin', //微信
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true, 
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>' 
	       }
	     }, {
	       name: 'WeixinTimeLine', //微信朋友圈
	       param: {
	         title: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!',
	         content: '线下支付，出示会员手机号即可使用优惠券和红包',
	         imageUrl: '<?php if(!empty($onlineshop -> logo_img)){echo IMG_GJ_LIST.$onlineshop -> logo_img;}?>',
	         captureScreen: true, 
	         url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>' 
	       }
	     },{
	       name: 'SMS', //短信
	       param: {
	           content: '<?php echo $onlineshop -> name?>发福利啦！优惠抢不停，小伙伴快下手啊!<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon?coupons_id='.$coupons_id;?>',
	           //应业务方需求定制功能
	           contentType: '',
	           extData:''
	       }
	     }, {
	       name: 'CopyLink', //复制链接
	       param: {
	           url: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>'
	       }
	     }]
	},function(result){
	});
});

<?php }elseif (Yii::app()->session['source'] == 'wechat'){?>
$('.share').click(function(){
	$('#popShadow').show();
	$('.popShare').show();
});

$('.close').click(function(){
	$('#popShadow').hide();
	$('.popShare').hide();
});
<?php }?>


//关闭查看优惠券
$('.btn_red').click(function(){
	$('#popShadow').hide();
	$('#pop_see').hide();
});
//显示查看优惠券
$('#seecoupon').click(function(){
	$('#popShadow').show();
	$('#pop_see').show();
});

//显示说明显示
$('.description').click(function(){
	$('#popShadow').show();
	$('#popuse_illustrate').show();
});

//关闭使用说明
$('.popCancel').click(function(){
	$('#popShadow').hide();
	$('#popuse_illustrate').hide();
});

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
    	link: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>', // 分享链接
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
    	link: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>', // 分享链接
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
	    link: '<?php echo 'wap.51wanquan.com/uCenter/coupon/GetCoupon.html?coupons_id='.$coupons_id;?>', // 分享链接
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