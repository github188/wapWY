<head>
	<title><?php echo $coupons_type == COUPON_TYPE_REDENVELOPE?'红包':'优惠券';?></title>
</head>

<body class="shop">
	<header class="top_title">
		<nav class="header-nav">
			<ul>
	   			<li class="<?php echo COUPONS_USE_STATUS_UNUSE == $cur_status ? 'bg' : '';?>"><a href="<?php echo Yii::app()->createUrl('uCenter/user/coupons', array('coupons_status'=>COUPONS_USE_STATUS_UNUSE, 'coupons_type'=>$coupons_type)) ?>">可使用</a></li>
	   			<li class="<?php echo COUPONS_USE_STATUS_USED == $cur_status ? 'bg' : '';?>"><a href="<?php echo Yii::app()->createUrl('uCenter/user/coupons', array('coupons_status'=>COUPONS_USE_STATUS_USED, 'coupons_type'=>$coupons_type)) ?>">已使用</a></li>
	   			<li class="<?php echo COUPONS_USE_STATUS_EXPIRED == $cur_status ? 'bg' : '';?>"><a href="<?php echo Yii::app()->createUrl('uCenter/user/coupons', array('coupons_status'=>COUPONS_USE_STATUS_EXPIRED, 'coupons_type'=>$coupons_type)) ?>">已过期</a></li>	   			
	    	</ul>
        </nav>
    </header>
    	<?php foreach ($list as $key => $value) {?>
    	<!--       红包 代金券 -->
		<?php  if( $value['type'] == COUPON_TYPE_REDENVELOPE || $value['type'] == COUPON_TYPE_CASH ) { ?>
        <section class="mid_con 
        <?php if($_GET['coupons_status'] == COUPONS_USE_STATUS_UNUSE){?>
        <?php if($value['type'] == COUPON_TYPE_REDENVELOPE){?>
        hb
        <?php }elseif ($value['type'] == COUPON_TYPE_CASH){?>
        yhq
        <?php }}else{?>gq<?php }?>" onclick="detail('<?php echo $value['id'] ?>')">
             <div class="input">
             	<div class="money">
                	<span class="fh">¥</span>
                    <span class="num"><?php echo intval($value['money'])?></span>
                </div>
                <div class="text">
                	<span class="fw"><?php echo $value['name']?></span>
                    <span class="time"><?php echo $value['validity'] ?></span>
                </div>
                <?php if($_GET['coupons_status'] == COUPONS_USE_STATUS_USED){?>
                <span class="zt">已使用</span>
                <?php }elseif ($_GET['coupons_status'] == COUPONS_USE_STATUS_EXPIRED){?>
                <span class="zt">已过期</span>
                <?php }?>
             </div>
        </section>
        <?php }elseif ($value['type'] == COUPON_TYPE_DISCOUNT){?>
        <section class="mid_con <?php if($_GET['coupons_status'] == COUPONS_USE_STATUS_UNUSE){?>yhq<?php }else{?>gq<?php }?>" onclick="detail('<?php echo $value['id'] ?>')">
             <div class="input">
             	<div class="money">
             		<span class="num"><?php echo $value['money']*10?></span>
                	<span class="fh">折</span>
                </div>
                <div class="text">
                	<span class="fw"><?php echo $value['name']?></span>
                    <span class="time"><?php echo $value['validity'] ?></span>
                </div>
                <?php if($_GET['coupons_status'] == COUPONS_USE_STATUS_USED){?>
                <span class="zt">已使用</span>
                <?php }elseif ($_GET['coupons_status'] == COUPONS_USE_STATUS_EXPIRED){?>
                <span class="zt">已过期</span>
                <?php }?>
             </div>
        </section>
        <?php }elseif($value['type'] == COUPON_TYPE_EXCHANGE){?>
        <section class="mid_con <?php if($_GET['coupons_status'] == COUPONS_USE_STATUS_UNUSE){?>yhq<?php }else{?>gq<?php }?>" onclick="detail('<?php echo $value['id'] ?>')">
             <div class="input">
             	<div class="money">
                    <span class="img"></span>
                </div>
                <div class="text">
                	<span class="fw"><?php echo $value['name']?></span>
                    <span class="time"><?php echo $value['validity'] ?></span>
                </div>
                <?php if($_GET['coupons_status'] == COUPONS_USE_STATUS_USED){?>
                <span class="zt">已使用</span>
                <?php }elseif ($_GET['coupons_status'] == COUPONS_USE_STATUS_EXPIRED){?>
                <span class="zt">已过期</span>
                <?php }?>
             </div>
        </section>
        <?php }?>
    	
    	<?php } ?>

</body>

<script type="text/javascript">
	function detail(coupons_id){
		location.href="<?php echo Yii::app()->createUrl('uCenter/user/couponsDetail'); ?>" + "?coupons_id=" + coupons_id;
	}
</script>