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