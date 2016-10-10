<title>粉丝福利</title>
<body>
	<div class="welfareList">
        <nav class="wfL-nav clearfix">
            <ul>
                <li class="cur"><a href="#">本周福利</a> </li>
                <li><a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/Coupons', array('encrypt_id' => $encrypt_id, 'coupons_status' => COUPONS_USE_STATUS_UNUSE, 'coupons_type' => COUPON_TYPE_CASH));?>" class="wfL-nav-text">我的福利</a> </li>
                <li><a href="<?php echo Yii::app() -> createUrl('Dmall/Activity/ActivityDetail');?>" class="wfL-nav-text">活动说明</a> </li>
                <!--<li><a href="<?php /*echo Yii::app()->createUrl('Dmall/Commodity/Index',array('encrypt_id' => $encrypt_id));*/?>" class="wfL-nav-text">商城首页</a></li> -->
            </ul>
        </nav>
        <div class="itemWrap">
        <?php foreach ($list as $k => $v){?>
        <?php if($v['status'] != DMALL_ACTIVITY_STATUS_END){?>
         <div class="wfL-item">
                <div class="pic"><a href="#"><img src="<?php echo IMG_GJ_LIST.$v['img'];?>"></a></div>
                <div class="con">
                    <h2>
                    <?php if(isset($v['coupons_type'])){if($v['coupons_type'] == 3){echo "【电子券】";}elseif($v['coupons_type'] == 1 || $v['coupons_type'] == 2){echo "【优惠券】";}}?>
                    <?php echo $v['name']?><?php if($v['status'] == DMALL_ACTIVITY_STATUS_NOT_START){?>(<?php echo date('n月j日 G:i',strtotime($v['start_time']))?>开抢)<?php }?>
                    </h2>
                    <div class="con-r">
                        <?php if($v['status'] == DMALL_ACTIVITY_STATUS_NOT_START){?>
                        <a class="btn noStart">未开始</a>
                        <?php }elseif ($v['status'] == DMALL_ACTIVITY_STATUS_STARTING){?>
                        <?php if(in_array($v['id'], $activity_arr)){?>
                        <a  class="btn over">已抢过</a>
                        <?php }else{?>
                        <a href="<?php echo Yii::app() -> createUrl('mobile/coupon/coupon/getCoupon',array('encrypt_id' => $encrypt_id, 'qcode' => $v['code'],'marketing_activity_type'=> MARKETING_ACTIVITY_TYPE_DMALL_ZFL,'marketing_activity_id' => $v['id']));?>" class="btn started">抢福利</a>
                        <?php }?>
                        <?php }elseif ($v['status'] == DMALL_ACTIVITY_STATUS_NO_STOCK){?>
                        <a class="btn over">抢完了</a>
                        <?php }?>                     
                    </div>
                    <div class="con-l">
                        <div class="price">
                            <span class="curP"><em>0</em>元</span>
                            <s>￥<?php echo !empty($v['original_price']) ? $v['original_price'] : '0';?>元</s>
                        </div>
                        <?php if($v['status'] == DMALL_ACTIVITY_STATUS_NOT_START){?>
                            <?php
                            //PHP计算两个时间差的方法 
                            $startdate=date('Y-m-d H:i:s');
                            $enddate=$v['start_time'];
                            $date=floor((strtotime($enddate)-strtotime($startdate))/86400);
                            $hour=floor((strtotime($enddate)-strtotime($startdate))%86400/3600);
                            $minute=floor((strtotime($enddate)-strtotime($startdate))%86400%3600/60);
                            $second=floor((strtotime($enddate)-strtotime($startdate))%86400%3600%60);
                            ?>
                        <div class="time orange">距离开抢
                        <?php if($date > 0){?>
                        <?php echo $date?>天<?php echo $hour?>小时
                        <?php }elseif ($hour > 0){?>
                        <?php echo $hour?>小时<?php echo $minute?>分
                        <?php }elseif ($minute >0){?>
                        <?php echo $minute?>分<?php echo $second?>秒
                        <?php }?>
                        </div>
                        <?php }elseif ($v['status'] == DMALL_ACTIVITY_STATUS_STARTING){?>
                        <span>共<?php echo $v['num']?>件</span><span>剩余<em class="orange"><?php echo $v['num']-$v['receive_num']?></em>件 </span>
                        <?php }?>
                    </div>
                </div>
            </div>
            <?php }?>
        <?php }?>
        </div>
        <?php $flag = 1;foreach ($list as $k => $v){?>
        <?php if($flag == 1 && $v['status'] == DMALL_ACTIVITY_STATUS_END){?>
        <div class="itemWrap wfL-itemsWPast">
            <div class="title">
                <div class="line"></div>
                <div class="text"><em></em>往期福利</div>
            </div>
            <?php $flag++;}?>
            
            <?php if($v['status'] == DMALL_ACTIVITY_STATUS_END){?>
            <div class="wfL-item">
                <div class="pic"><a href="#"><img src="<?php echo IMG_GJ_LIST.$v['img'];?>"> </a> </div>
                <div class="con">
                    <h2><?php echo $v['name']?></h2>
                    <div class="con-r">
                        <a class="btn over">已结束</a>
                    </div>
                    <div class="con-l">
                        <div class="price">
                            <span class="curP"><em>0</em>元</span>
                        </div>
                        <div class="time">
                            <span>抢完<?php echo $v['num']?>件</span><span>剩余<em class="orange"><?php echo $v['num']-$v['receive_num']?></em>件 </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
            <?php }?>
        </div>
    </div>
    <script>
    /***************************微信分享******************************************/
	
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
	    	title: '【东钱湖旅游】粉丝周福利', // 分享标题
	    	link: '<?php echo WAP_DOMAIN . '/Dmall/Activity/WelfareList?encrypt_id='.$encrypt_id;?>', // 分享链接
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
	    	title: '【东钱湖旅游】粉丝周福利', // 分享标题
	    	desc: '周福利活动，每周推出免费电子门票优惠券发放活动。', // 分享描述
	    	link: '<?php echo WAP_DOMAIN . '/Dmall/Activity/WelfareList?encrypt_id='.$encrypt_id;?>', // 分享链接
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
		    title: '【东钱湖旅游】粉丝周福利', // 分享标题
		    desc: '周福利活动，每周推出免费电子门票优惠券发放活动。', // 分享描述
		    link: '<?php echo WAP_DOMAIN . '/Dmall/Activity/WelfareList?encrypt_id='.$encrypt_id;?>', // 分享链接
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

