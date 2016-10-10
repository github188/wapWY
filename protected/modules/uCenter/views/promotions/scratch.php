
<html>
	<head>
		<?php if(!empty($model)) { ?>
			<title><?php echo $model->name?></title>
		<?php }else { ?>
			<title>刮刮卡</title>
		<?php } ?>
	
		<script>
			document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
				WeixinJSBridge.call('hideOptionMenu');
			});
			document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
				WeixinJSBridge.call('hideToolbar');
			});
		</script>
	</head>

	<body data-role="page" class="activity-scratch-card-winning">
		<div class="main">
			<div class="cover">
				<img src="<?php echo USER_STATIC_IMAGES?>scratch/activity-scratch-card-bannerbg.png">
				<div id="prize"> </div>
				<div id="scratchpad">
					<div style="position: absolute; width: 150px; height: 40px; cursor: default;">
						<canvas width="150" height="40" style="cursor: default;"></canvas>
					</div>
				</div>
			</div>
			<?php if (!empty($model)) { ?>
				<div class="content">
					<?php $form = $this->beginWidget('CActiveForm', array(
			            'enableAjaxValidation' => true,
			            'id' => 'prizerecord',
			            'method' => 'get',
			            'htmlOptions' => array('name' => 'prizerecord'),
						'action' => Yii::app()->createUrl('wap/promotions/savephonenum')
					))?>
					<?php if (empty($empty_record->prize_type)) { ?>
						<div id="winprize" style="display:none" class="boxcontent">
					<?php } else { ?>
						<div class="boxcontent boxwhite">
							<div class="box">
								<div class="Detail">
		                            <span class="red">请先填写您的中奖信息，才能再次抽奖</span>
								</div>
							</div>
						</div>
						<script>
							$("#scratchpad").hide();
						</script>
						<div id="winprize" style="display:show" class="boxcontent">
					<?php } ?>
							<div class="box">
								<div class="title-red"><span>恭喜你中奖了</span></div>
								<div class="Detail">
									<p>您中了 <span class="red" id="prizelevel"><?php if (!empty($empty_record->prize_type)) { ?><?php echo $GLOBALS['PRIZE_TYPE'][$empty_record->prize_type]?><?php } ?></span></p>
									<p>奖品为 <span class="red" id="prizename"><?php if (!empty($empty_record->prize_type)) { ?><?php echo $title_arr[$empty_record->prize_type]['title']?><?php } ?></span></p>
	<!-- <p>SN码<span class="red" id="snnum"></span></p> -->
									<p>发奖说明:<span class="red" id="description_awards">填写信息后即可获得兑奖链接。</span></p>
									<?php echo $form->hiddenField($empty_record, 'id');?>
<!-- 									<p>请输入您的姓名:</p> -->
									<p><?php echo $form->hiddenField($empty_record, 'user_name', array('placeholder'=>"请输入您的姓名"));?></p>
									<p>请输入您的手机号码:</p>
									<p><?php echo $form->textField($empty_record, 'user_phone', array('placeholder'=>"请输入您的手机号"));?></p>
	<!-- <p>请输入您的地址:</p> -->
	<!-- <p><input type="text" maxlength="20" id="address" class="txt" placeholder="请输入您的地址"></p> -->
									<?php echo CHtml::submitButton('确认提交', array('name'=>"save")); ?>
								</div>
							</div>
						</div>
					<?php $this->endWidget(); ?>
					
					<?php if($play_times['total'] >= $model->everyone_num) { ?>
						<div class="boxcontent boxwhite">
							<div class="box">
								<div class="Detail">
		                            <span class="red"><?php echo "亲，本次活动的累计可玩次数您已经用完了喔！敬请关注我们的下个活动吧~。"?></span>
								</div>
							</div>
						</div>
						<script>
							$("#scratchpad").hide();
						</script>
					<?php }elseif ($play_times['day'] >= $model->everyone_everyday_num) { ?>
						<div class="boxcontent boxwhite">
							<div class="box">
								<div class="Detail">
		                            <span class="red"><?php echo "亲，您今日的参与次数已经用完了，请下次再来吧！"?></span>
								</div>
							</div>
						</div>
						<script>
							$("#scratchpad").hide();
						</script>
					<?php } ?>
					
					<div id="prizenameset" " class="boxcontent">
						<div class="box">
							<div class="title-brown"><span>奖项设置：</span></div>
					            <div class="Detail">
					            	<?php if (!empty($model->first_prize_num)) { ?>
					            		<p>一等奖： <?php echo $title_arr[PRIZE_TYPE_FIRST]['title']?>。 <?php if ($model->if_show_num ==SHOW_PRIZE_NUM) { ?> 奖品数量： <?php echo $model->first_prize_num?></p> <?php } ?>
					            	<?php } ?>
					            	<?php if (!empty($model->second_prize_num)) { ?>
					            		<p>二等奖： <?php echo $title_arr[PRIZE_TYPE_SECOND]['title']?>。 <?php if ($model->if_show_num ==SHOW_PRIZE_NUM) { ?> 奖品数量： <?php echo $model->second_prize_num?></p> <?php } ?>
					            	<?php } ?>
					            	<?php if (!empty($model->third_prize_num)) { ?>
					            		<p>三等奖： <?php echo $title_arr[PRIZE_TYPE_THIRD]['title']?>。 <?php if ($model->if_show_num ==SHOW_PRIZE_NUM) { ?> 奖品数量： <?php echo $model->third_prize_num?></p> <?php } ?>
					            	<?php } ?>
					            	<?php if (!empty($model->fourth_prize_num)) { ?>
					            		<p>四等奖： <?php echo $title_arr[PRIZE_TYPE_FORTH]['title']?>。 <?php if ($model->if_show_num ==SHOW_PRIZE_NUM) { ?> 奖品数量： <?php echo $model->fourth_prize_num?></p> <?php } ?>
					            	<?php } ?>
					            	<?php if (!empty($model->fifth_prize_num)) { ?>
					            		<p>五等奖： <?php echo $title_arr[PRIZE_TYPE_FIFTH]['title']?>。 <?php if ($model->if_show_num ==SHOW_PRIZE_NUM) { ?> 奖品数量： <?php echo $model->fifth_prize_num?></p> <?php } ?>
					            	<?php } ?>
					          	</div>
						</div>
					</div>
					<div id="time" " class="boxcontent">
						<div class="box">
							<div class="title-brown"><span>活动时间：</span></div>
							<div class="Detail">
								<p><?php echo date('Y年m月d日', strtotime($model->start_time))?>至<?php echo date('Y年m月d日', strtotime($model->end_time))?></p>	
							</div>
						</div>
					</div>
					
<!-- 					<div class="boxcontent"> -->
<!-- 						<div class="box"> -->
<!-- 							<div class="title-brown">活动说明：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><font color="red"></font></strong> -->
<!-- 							</div> -->
<!-- 							<div class="Detail"> -->
<!-- 	                            <p>1、登陆牛扣生活官方网站（www.nnkou.com），进入年货大礼包专场购买1047大礼包，下单时在备注中留言【<span class="red">买一送一</span>】，我们在验证获奖信息后会为您安排发货。</p> -->
<!-- 		                        <p>2、领奖有效时间为48小时，逾期视为自动放弃</p> -->
<!-- 		                        <p>3、下单收货人手机号码需与中奖时登记的手机号码一致</p> -->
<!-- 							</div> -->
<!-- 						</div> -->
<!-- 					</div> -->
					<div id="getprize" style="display:none" class="boxcontent">
						<div class="box">
							<div class="title-red"><span>兑奖申请：</span></div>
							<div class="Detail">
								<p><strong>兑奖申请：(SN码<span class="red" id="prize_sn_num"></span>)</strong></p>
								<p><input type="hidden" maxlength="20" id="action_id" class="txt"></p>
								<p><input type="hidden" maxlength="20" id="record_id" class="txt"></p>
								<p><input type="text" maxlength="20" id="merchant_password" class="txt" placeholder="请输入商家兑换码"></p>
								<p><input type="submit" value="提交" name="get"><input type="button" value="关闭" name="close"></p>
							</div>
						</div>
					</div>
					
	                <?php if (!empty($record)) { ?>
						<div class="boxcontent boxwhite">
							<div class="box">
								<div class="title-brown">我的中奖记录：
								</div>
								<div class="Detail">
		                           	<?php foreach ($record as $k => $v) { ?>
			                     		<?php if (!empty($v->user_phone)) {?>
				                    		<p><?php echo $k+1 ?>、奖项：<?php echo $title_arr[$v->prize_type]['title']?></p>
				                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;中奖者电话:<?php echo $v->user_phone?></p>
				                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;中奖时间:<?php echo $v->create_time?></p>
					                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="red">该券为刮刮卡活动中奖用户专属：</span><span class="red"></span><a href="<?php echo USER_DOMAIN_COUPONS.'/newGetCouponOne?coupon_id='.$title_arr[$v->prize_type]['id']?>" class="blue">去领取</a></p>
				                    	<?php } ?>
			                   		<?php } ?>
								</div>
							</div>
						</div>
		            <?php } ?>
					<div class="boxcontent">
						<div class="box">
							<div class="title-brown">活动说明：
							</div>
							<div class="Detail">
	                            <p><?php echo $model->illustrate?></p>
							</div>
						</div>
					</div>
	
				</div>
				<div style="clear:both;"></div>
			</div>
			
			<div style="height:60px;"></div>
			<script type="text/javascript">
				var display = false;
				var num = 0;
				var win = true;
				var first_probability = <?php echo ($model->first_prize_probability*100) ?>;
				var second_probability = first_probability + <?php echo ($model->second_prize_probability*100) ?>;
				var third_probability = second_probability + <?php echo ($model->third_prize_probability*100) ?>;
				var fourth_probability = third_probability + <?php echo ($model->fourth_prize_probability*100) ?>;
				var fifth_probability = fourth_probability + <?php echo ($model->fifth_prize_probability*100) ?>;
				$(function(){
					$("#scratchpad").wScratchPad({
						width : 150,
						height : 40,
						color : "#a9a9a7",
						scratchMove : function(e, percent){
							num++;
							//80%时自动清除
							if(percent > 40){
								this.clear();
							}
							//开始时请求中奖结果
							if(num > 10 && win) {
								win = false;
						        var id = <?php echo $model->id?>;
								$.ajax({
									url: "<?php echo Yii::app()->createUrl('uCenter/promotions/getScratchPrize') ?>",
									dataType: "json",
									data: { id:id },
									success: function(data) {
										//已中奖
										if (data.error == "ok") {
											document.getElementById('prize').innerHTML = data.prizelevel;
											$("#ActivityRecord_id").val(data.id);
							                $("#prizename").text(data.message);
							                $("#prizelevel").text(data.prizelevel);
											$("#winprize").slideToggle(500);
										}else{
											document.getElementById('prize').innerHTML="谢谢参与";
											if(confirm("谢谢参与")){
												location.reload();
											}else{
												location.reload();
											}
										}
									}
								})
							}
							
						}
					});
				});
	
				//保存中奖信息
				$("input[name='save']").click(function(){
					var id = $("#ActivityRecord_id").val();
					//var player = $("#PromotionsRecord_player").val();
					//var promotions_action_id = $("#PromotionsRecord_promotions_action_id").val();
					//var sn_num = $("#PromotionsRecord_sn_num").val();
					//var state = $("#PromotionsRecord_state").val();
					//var prize_type = $("#PromotionsRecord_prize_type").val();
// 					var name = $("#PromotionsRecord_name").val();
					var phone_num = $("#ActivityRecord_user_phone").val();
					//var address = $("#address").val();
					var data = {id:id, phone_num:phone_num};
					//保存中奖纪录
					$.ajax({
				    	url: '<?php echo(Yii::app()->createUrl('uCenter/promotions/savePhoneNum'));?>',
				    	data: {ActivityRecord:data},
				    	type: 'post',
				    	success : function(data) {
				    		var result = eval("("+data+")");
					    	if (result.error == "success"){
					    		if(confirm("是否跳转到领取界面")){
						    		window.location.href=result.url;
						    	}else{
						    		location.reload();
						    	}
					    	}else{
						    	alert(result.message);
					    	}
				    	}
				    });
					return false;
				});
	
				//申请兑奖
				$("input[name='get']").click(function(){
					var merchant_password = $("#merchant_password").val();
					var action_id = $("#action_id").val();
					var record_id = $("#record_id").val();
					//保存中奖纪录
					$.ajax({
				    	url: '<?php echo(Yii::app()->createUrl('wap/promotions/getprize'));?>',
				    	data: {merchant_password:merchant_password, action_id:action_id, record_id:record_id},
				    	type: 'post',
				    	success : function(data) {
					    	if(data == "success"){
					    		location.reload();
					    		if(confirm("兑奖成功")){
						    	}
					    	}else{
				    			$("#getprize").slideUp();
					    		if(confirm("商家兑换码错误")){
						    	}
					    	}
					    }
				    });
					return false;
				});
	
				//关闭申请兑奖
				$("input[name='close']").click(function(){
					$("#getprize").slideUp();
				});
	
				//申请兑奖弹出框
				$("a[name='askprize']").click(function(){
					$("#prize_sn_num").html($(this).prev().html());
					$("#action_id").val($(this).next().val());
					$("#record_id").val($(this).next().next().val());
					//$("#getprize").slideToggle(500);
					$("#getprize").slideDown();
	        	})
			</script>
			
		<?php } else {?>
			<div class="content">
				<div class="boxcontent boxwhite">
					<div class="box">
						<div class="Detail">
                            <p>当前没有活动！</p>
						</div>
					</div>
				</div>
			</div>
			<div style="height:222px;"></div>
		<?php }?>
	</body></html>