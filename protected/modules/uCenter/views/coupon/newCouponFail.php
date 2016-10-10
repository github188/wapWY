<body>
	<header class="couponsHead <?php echo $coupon_model['color'] ?>">
		<div class="headImg clearfix">
	    	<div class="img"><img src="<?php echo IMG_GJ_LIST.$coupon_model['logo_img']?>"></div>
	        <div class="name"><?php echo empty($coupon_model['merchant_short_name'])?$coupon_model['name']:$coupon_model['merchant_short_name']?></div>
	    </div>
	    <div class="con">
	    	<h3><?php echo $coupon_model['title']?></h3>
	        <h4><?php echo $coupon_model['vice_title']?></h4>
	        <div class="time">
		        <?php if ($coupon_model['time_type'] == VALID_TIME_TYPE_FIXED) {?>
		       		 有效期：<?php echo date("Y.m.d", strtotime($coupon_model['start_time']))?>&nbsp;-&nbsp;<?php echo date("Y.m.d", strtotime($coupon_model['end_time']))?>
		        <?php }else{?>
		        	自领取之日起<?php echo $coupon_model['effective_days']?>天内有效
	        	<?php } ?>
	        </div>
	    </div>
	</header>
	<section class="couponsCon">
		<a href="<?php echo Yii::app()->createUrl('uCenter/coupon/newCouponDetail', array('coupon_id'=>$coupon_id))?>" class="intergral"><span class="text">优惠券详情</span><span class="jt"></span></a>
	</section>
	<section class="couponsWrap clearfix">
		<div class="details">
	        <button disabled="disabled" class="btn_com_gray"><?php echo $msg?></button>
	    </div>
	</section>
	<section class="couponsEnd">
		<img src="<?php echo USER_STATIC_IMAGES?>user/logo-bottom.png">
	</section>
</body>
