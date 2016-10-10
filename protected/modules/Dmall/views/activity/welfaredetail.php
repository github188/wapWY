
</head>
<body>
	<div class="welfare_mask" style ="display: none">
        <div class="welfare_qcode">
            <p>你还没有关注此公众号</p>
            <p>请长按下图二维码关注</p>
            <img src="<?php echo !empty($qrcode)?IMG_GJ_LIST.$qrcode:'';?>">
        </div>
    </div>
	<div class="welfareD">
        <h2>春夏三月柳如烟，踏青日暖钱湖边</h2>
        <div class="summary">
            <span class="flag">原创</span>
            <span class="time">2016-3-16</span>
            <span class="blue">东钱湖景区</span>
        </div>
        <div class="pic">
            <img src="<?php echo USER_STATIC_IMAGES;?>3.jpg">
        </div>
        <article>
            <h3><span class="icon"></span> 新用户首单立减</h3>
            <div class="con">在活动期间，关注我们的微信公众号，在线上商城中首次下单购买门票的用户，将享受优惠，</div>
            <div class="btn"><a href="<?php echo Yii::app()->createUrl('Dmall/activity/firstSingle',array('encrypt_id' => $encrypt_id));?>" class="btn_com">立即购买</a> </div>
        </article>
        <article>
            <h3><span class="icon"></span> 周末福利</h3>
            <div class="con">们每周都会准备免费的门票和优惠券进行赠送，还不速来领取？</div>
            <div class="btn"><a href="<?php echo Yii::app()->createUrl('Dmall/activity/welfareList',array('encrypt_id' => $encrypt_id));?>" class="btn_com">马上领取</a> </div>
        </article>
        <div class="QRCode">
            <div class="img"><img src="<?php echo !empty($qrcode)?IMG_GJ_LIST.$qrcode:'';?>"></div>
            <p>
                活动最终解释权归东钱湖营销团队所有。<br>
                更多好玩新鲜的东钱湖景区咨询，<br>
                长按旁边的二维码，进入我们的公众号了解。
            </p>
        </div>
    </div>
    <script type="text/javascript">
    <?php if($if_follow != 1){?>
    $('.welfare_mask').show();
    <?php }?>
     $('.welfare_mask').click(function(){
    	 $('.welfare_mask').hide();
     });
    </script>
    
   
    
</body>
</html>
