<head>
	<title>个人信息</title>
</head>


<body class="logo">
    <section class="mid_con">
    	<div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalInformation', array('id'=>$user['id'], 'title'=>"nickname", 'data'=>$user['nickname'], 'encrypt_id' => $encrypt_id))?>">  
          		昵称<span class="jt"></span><span class="id"><?php echo $user['nickname']?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalInformation', array('id'=>$user['id'], 'title'=>"name", 'data'=>$user['name'], 'encrypt_id' => $encrypt_id))?>">  
          		姓名<span class="jt"></span><span class="id"><?php echo $user['name']?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalSex', array('id'=>$user['id'], 'title'=>"sex", 'data'=>$user['sex'], 'encrypt_id' => $encrypt_id))?>">  
          		性别<span class="jt"></span><span class="id"><?php echo $GLOBALS['__SEX'][$user['sex']]?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalBirthday', array('id'=>$user['id'], 'title'=>"birthday", 'data'=>$user['birthday'], 'encrypt_id' => $encrypt_id))?>">  
          		生日<span class="jt"></span><span class="id"><?php echo date('Y-m-d',strtotime($user['birthday']))?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalInformation', array('id'=>$user['id'], 'title'=>"social_security_number", 'data'=>$user['social_security_number'], 'encrypt_id' => $encrypt_id))?>">  
          		身份证<span class="jt"></span><span class="id"><?php echo $user['social_security_number']?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalInformation', array('id'=>$user['id'], 'title'=>"email", 'data'=>$user['email'], 'encrypt_id' => $encrypt_id))?>">  
          		邮箱<span class="jt"></span><span class="id"><?php echo $user['email']?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalMarital', array('id'=>$user['id'], 'title'=>"marital_status", 'data'=>$user['marital_status'], 'encrypt_id' => $encrypt_id))?>">  
          		婚姻状况<span class="jt"></span><span class="id"><?php echo $GLOBALS['__MARITAL_STATUS'][$user['marital_status']]?></span>
          	</a>
        </div>
        <div class="item dline">
          	<a class="intergral" href="<?php echo Yii::app()->createUrl('uCenter/user/editPersonalInformation', array('id'=>$user['id'], 'title'=>"work", 'data'=>$user['work'], 'encrypt_id' => $encrypt_id))?>">  
          		工作<span class="jt"></span><span class="id"><?php echo $user['work']?></span>
          	</a>
        </div>
	</section>

    <div class="f-coupon" id="pop" style="display:none">
        <div style="color:black;" class="f-coupon-con">
            <p>您已填写必要资料，获得一张优惠券，可在个人中心查看该券。</p>
        </div>
        <div class="f-coupon-b">
            
            
	        <?php if ($coupon_model['if_wechat'] == IF_WECHAT_YES && isset(Yii::app()->session['wechat_code'])) { ?>  
	        	<a href="#" class="sync-wechat-btn" id="addCard" <?php echo empty($flag) ? 'style="display: none"' : '' ?>>同步到微信卡包</a>
	        <?php } ?>     
	        <span id="add_ready" class="sync-wechat-btn" <?php echo !empty($flag) ? 'style="display: none"' : '' ?>>已同步</span>
	        <?php echo $flag?>
	        <a href="<?php echo Yii::app()->createUrl('uCenter/user/memberCenter', array('encrypt_id' => $encrypt_id))?>" class="go-center-btn">确定</a>

        </div>
    </div>
</body>

<script type="text/javascript">

    <?php if (empty($flag)) { ?>
    $('#addCard').hide();
    $('#add_ready').show();
    <?php }?>

    $(function() {
        function initpop(){
          var _w = $(window).width() //浏览器窗口宽度
          var _w = $(window).height() //浏览器窗口高度
          var _offsetW = $('#pop').width(); //获取弹出框的宽度
          var _offsetH = $('#pop').height(); //获取弹出框的高度
    
          var _left = ($(window).width()-$('#pop').width())/2;
          var _top = ($(window).height()-$('#pop').height())/2;
    
          $('#pop').css({'left' : _left, 'top' : 80});
        };        
      var showpop = <?php echo $user['pop']?>;
      if (showpop) {
          initpop();
          $('#pop').show("fast");
      };
    });

	//微信js配置
	wx.config({
	   	debug:false,
	    appId: '<?php echo $signPackage["appId"];?>',
	    timestamp: <?php echo $signPackage["timestamp"];?>,
	    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
	    signature: '<?php echo $signPackage["signature"];?>',
	    jsApiList: [
	      "addCard",
	    ]
	  });
	wx.ready(function () {
		//添加卡券
		document.querySelector('#addCard').onclick = function () {
            wx.addCard({
              cardList: [
                {
                  cardId: "<?php echo $coupon_model['card_id']?>",
                  cardExt: '{"timestamp":"<?php echo $cardSign["timestamp"];?>","signature":"<?php echo $cardSign["signature"];?>"}'
                }
              ],
              success: function (res) {
// 				  alert('已添加卡券：' + JSON.stringify(res.cardList));
            	  $('#addCard').hide();
            	  $('#add_ready').show();
            	  
            	  $.ajax({
                      url:'<?php echo Yii::app()->createUrl('uCenter/coupon/wxAddCardFlag')?>',
             	  });
            	  
              }
            });
        };
	});


</script>

