<head>
	<title><?php echo $coupons['type'] == COUPON_TYPE_REDENVELOPE?'红包':'优惠券';?></title>
</head>
<body class="wap_color">
	<div class="content">
		<section class="content">
		<div class="hd">
	        <h3><?php echo $coupons['name']?></h3>
	        <div class="coupon">
	        	<h1>
	        		<?php if( isset($action) && !empty($action) ) {?>
	        			恭喜获得
	        		<?php } ?>
	        		<?php echo $GLOBALS['COUPON_TYPE'][$coupons['type']]?>
	        	</h1>
	<!--       红包 代金券 -->
	        	<?php  if( $coupons['type'] == COUPON_TYPE_REDENVELOPE || $coupons['type'] == COUPON_TYPE_CASH ) { ?>
	            		<span><?php echo $coupons['money']?>元</span>
		        <?php }elseif ($coupons['type'] == COUPON_TYPE_DISCOUNT) { ?>
	<!-- 	        折扣券 -->
		        	<span><?php echo $coupons['discount']*10?>折</span>
		        <?php }elseif ($coupons['type'] == COUPON_TYPE_EXCHANGE) { ?>
	<!-- 	        兑换券 -->
		        	<span><?php echo $coupons['exchange']?></span>
		        <?php } ?>
	        </div>
	        
	        <div class="op">
	        	<a href="<?php echo Yii::app()->createUrl('uCenter/user/coupons', array('coupons_status'=>COUPONS_USE_STATUS_UNUSE, 'coupons_type'=>$coupons['type']))?>" class="btn">查看<?php echo $coupons['type'] == COUPON_TYPE_REDENVELOPE?'红包':'优惠券';?></a>
	        </div>
	    </div>
	    <div class="use"><a href="javascript:void(0)" id="searchJg">使用说明</a></div>
	</div>
</section>
	<!--弹出框-->
	<div id="popShadow" style="display:none"></div>
	<div id="instructions" class="pop" style="display:none">
		<div class="pop_con">
	    	<div class="pop_content">
	        	<a href="javascript:void(0)" id="close"><div class="popCancel"></div></a>
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
			</div>
		</div>
	</div>
	<!--end-->
</body>

<script type="text/javascript">
	$("#searchJg").click(function(){
		$("#popShadow").show();
		$("#instructions").show(); 
	})
	
	$("#close").click(function(){
		$("#popShadow").hide();
		$("#instructions").hide(); 
	})
</script>

<script type="text/javascript">
//     $("#searchJg").click(function(){
        //art.dialog.open('<?php //echo Yii::app() -> createUrl('uCenter/user/instructions', array('coupons'=>$coupons))?>', {title: '使用说明', lock: true,drag:false});
//     })
</script>