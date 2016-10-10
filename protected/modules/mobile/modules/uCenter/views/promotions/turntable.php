<html>
  <head>
  	<?php if (!empty($model)) {?>
    	<title><?php echo $model->name?></title>
    <?php } else {?>
    	<title>欢乐大转盘</title>
    <?php } ?>
  </head>

  <body class="activity-lottery-winning" >
    <div class="main" >
      <script type="text/javascript">
        var loadingObj = new loading(document.getElementById('loading'),{radius:20,circleLineWidth:8});
        loadingObj.show();
      </script>
      <div id="outercont"  >
        <div id="outer-cont">
          <div id="outer"><img src="<?php echo USER_STATIC_IMAGES?>turntable/activity-lottery-1.png"></div>
        </div>
        <div id="inner-cont">
          <div id="inner"><img src="<?php echo USER_STATIC_IMAGES?>turntable/activity-lottery-2.png"></div>
        </div>
      </div>
      <?php if(!empty($model)) { ?>
      	<div class="content"  >
      <!--       	判断是可以抽奖 -->
       	<?php if($play_times['total'] >= $model->everyone_num) { ?>
			<div class="boxcontent boxyellow">
				<div class="box">
					<div class="Detail">
                    	<?php echo "亲，本次活动的累计可玩次数您已经用完了喔！敬请关注我们的下个活动吧~。"?>
					</div>
				</div>
			</div>
			<script>
				var over = true;
			</script>
		<?php }elseif ($play_times['day'] >= $model->everyone_everyday_num) { ?>
				<div class="boxcontent boxyellow">
					<div class="box">
						<div class="Detail">
                            <?php echo "亲，您今日的参与次数已经用完了，请下次再来吧！"?>
					</div>
				</div>
			</div>
			<script>
				var over = true;
			</script>
		<?php }else { ?>
			<script>
				var over = false;
			</script>
		<?php } ?>
				
      	<?php $form = $this->beginWidget('CActiveForm', array(
            'enableAjaxValidation' => true,
            'id' => 'prizerecord',
            'method' => 'get',
            'htmlOptions' => array('name' => 'prizerecord'),
			'action' => Yii::app()->createUrl('wap/promotions/savephonenum')
		))?>
			<?php if (empty($empty_record->prize_type)) { ?>
				<div id="result" style="display:none" class="boxcontent boxyellow">
			<?php } else { ?>
				<div class="boxcontent boxwhite">
					<div class="box">
						<div class="Detail">
                            <span class="red">请先填写您的中奖信息，才能再次抽奖</span>
						</div>
					</div>
				</div>
				<script>
					var over = true;
				</script>
				<div id="result" style="display:show" class="boxcontent boxyellow">
			<?php } ?>
					<div class="box">
						<div class="title-red"><span>恭喜你中奖了</span></div>
						<div class="Detail">
						
							<p>您中了 <span class="red" id="prizelevel"><?php if (!empty($empty_record->prize_type)) { ?><?php echo $GLOBALS['PRIZE_TYPE'][$empty_record->prize_type]?><?php } ?></span></p>
							<p>奖品为 <span class="red" id="prizename"><?php if (!empty($empty_record->prize_type)) { ?><?php echo $title_arr[$empty_record->prize_type]['title']?><?php } ?></span></p>
						
							
	<!-- <p>SN码<span class="red" id="snnum"></span></p> -->
							<p>发奖说明:<span class="red" id="description_awards">填写信息后即可获得兑奖链接。</span></p>
							<?php echo $form->hiddenField($empty_record, 'id');?> 
<!-- 	<p>请输入您的姓名:</p> -->
							<p><?php echo $form->hiddenField($empty_record, 'user_name', array('placeholder'=>"请输入您的姓名", 'value'=>"大转盘"));?></p>
							<p>请输入您的手机号码:</p>
							<p><?php echo $form->textField($empty_record, 'user_phone', array('placeholder'=>"请输入您的手机号"));?></p>
	<!-- <p>请输入您的地址:</p> -->
	<!-- <p><input type="text" maxlength="20" id="address" class="txt" placeholder="请输入您的地址"></p> -->
							<?php echo CHtml::submitButton('确认提交', array('name'=>"save")); ?>
						</div>
					</div>
				</div>
			<?php $this->endWidget(); ?>
			
        <div class="boxcontent boxyellow">
          <div class="box">
            <div class="title-green"><strong>奖项设置<font color="red"></div>
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
	          	</div>
          </div>
        </div>
        
        <div id="getprize" style="display:none" class="boxcontent boxyellow">
			<div class="box">
				<div class="title-green"><span>兑奖申请：</span></div>
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
			<div class="boxcontent boxyellow">
				<div class="box">
					<div class="title-green">我的中奖记录：</div>
					<div class="Detail">
                     	<?php foreach ($record as $k => $v) { ?>
                     		<?php if (!empty($v->user_phone)) {?>
	                    		<p><?php echo $k+1 ?>、奖项：<?php echo $title_arr[$v->prize_type]['title']?></p>
	                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;中奖者电话:<?php echo $v->user_phone?></p>
	                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;中奖时间:<?php echo $v->create_time?></p>
		                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="red">该券为大转盘活动中奖用户专属：</span><span class="red"></span><a href="<?php echo USER_DOMAIN_COUPONS.'/newGetCouponOne?coupon_id='.$title_arr[$v->prize_type]['id']?>" class="blue">去领取</a></p>
	                    	<?php } ?>
                   		<?php } ?>
					</div>
				</div>
			</div>
        <?php } ?>
        
        <div class="boxcontent boxyellow">
          <div class="box">
            <div class="title-green">活动说明</div>
            <div class="Detail">
              <p> <?php echo $model->illustrate?></p>
            </div>
          </div>
        </div>
      </div>

    </div>

    <script type="text/javascript">
      $(function() {
        window.requestAnimFrame = (function() {
          return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
          function(callback) {
            window.setTimeout(callback, 1000 / 60)
          }
        })();
        var totalAngle  = 0;
        var steps = [];
        var loseAngle = [36, 96, 156, 216, 276,336,66,186,306];
        var winAngle = [6, 126, 246];
        var prizeLevel;
        var count = 0;
        var now = 0;
        var a = 0.01;
        var outter, inner, timer, running = false;
        function countSteps() {
          var t = Math.sqrt(2 * totalAngle / a);
          var v = a * t;
          for (var i = 0; i < t; i++) {
            steps.push((2 * v * i - a * i * i) / 2)
          }
          steps.push(totalAngle)
        }
        function step() {
          outter.style.webkitTransform = 'rotate(' + steps[now++] + 'deg)';
          outter.style.MozTransform = 'rotate(' + steps[now++] + 'deg)';
          outter.style.oTransform = 'rotate(' + steps[now++] + 'deg)';
          outter.style.msTransform = 'rotate(' + steps[now++] + 'deg)';
          if (now < steps.length) {
            requestAnimFrame(step)
          } else {
            running = false;
            setTimeout(function() {
              if (prizeLevel != null) {
                var levelName= new Array("", "一等奖", "二等奖", "三等奖")
                // var levelName = "";
                // if (prizeLevel == 1) {
                  // levelName = "一等奖"
                // } else if (prizeLevel == 2) {
                  // levelName = "二等奖"
                // } else if (prizeLevel == 3) {
                  // levelName = "三等奖"
                // } else if (prizeLevel == 4) {
                  // levelName = "四等奖"
                // } else if (prizeLevel == 5) {
                  // levelName = "五等奖"
                // } else if (prizeLevel == 6) {
                  // levelName = "六等奖"
                // }
                $("#prizelevel").text(levelName[prizeLevel]);
                $("#result").slideToggle(500);  //显示中奖结果
                //$("#outercont").slideUp(500)    //隐藏转盘
              } else {
                if(confirm("亲，继续努力哦！")){
		    		location.reload();
		    	}else{
		    		location.reload();
		    	}
              }
            },
            200)
          }
        }
        function start(deg) {
          deg = deg || loseAngle[parseInt(loseAngle.length * Math.random())];
          running = true;
          clearInterval(timer);
          totalAngle  = 360 * 1 + deg;
          steps = [];
          now = 0;
          countSteps();
          requestAnimFrame(step)
        }
        window.start = start;
        outter = document.getElementById('outer');
        inner = document.getElementById('inner');
        i = 10;
        $("#inner").click(function() {
          if (running) return;
          //没有获得中奖json返回，让用户退出
          if (prizeLevel != null) {
            return
          }
          if (over) return;
          
          var id = <?php echo $model->id?>;
          $.ajax({
            url: "<?php echo Yii::app()->createUrl('uCenter/promotions/getprizelevel') ?>",
            dataType: "json",
            data: { id:id },
            beforeSend: function() {
              running = true;
              timer = setInterval(function() {
                i += 5;
                outter.style.webkitTransform = 'rotate(' + i + 'deg)';
                outter.style.MozTransform = 'rotate(' + i + 'deg)'
              },
              1)
            },
            success: function(data) {
              //已中奖
              if (data.error == "ok") {
                $("#ActivityRecord_id").val(data.id);
                $("#prizename").text(data.message);
                $("#player").val(data.player);
                $("#prize_type").val(data.prize_type);
                // alert(data.message);
                count = 3;
                clearInterval(timer);
                prizeLevel = data.prizelevel;
                start(winAngle[data.prizelevel - 1]);
                return
              }
              //未中奖则累加次数
              running = false;
              count++
              prizeLevel = null;
              start()
            },
            //未获取json返回时
            error: function() {
              // alert(data.message);
              prizeLevel = null;
              start();
              running = false;
              count++
            },
            timeout: 4000
          })
        })
      });

      //保存中奖信息
		$("input[name='save']").click(function(){
			var id = $("#ActivityRecord_id").val();
// 			var name = $("#ActivityRecord_user_name").val();
			var phone_num = $("#ActivityRecord_user_phone").val();
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
    <?php } else { ?>
        <div class="boxcontent boxyellow" >
          <div class="box">
            <div class="Detail">
              	<p>当前没有活动！</p>
            </div>
          </div>
        </div>
		<div style="height:200px;"></div>
    <?php } ?>
  </body>
</html>