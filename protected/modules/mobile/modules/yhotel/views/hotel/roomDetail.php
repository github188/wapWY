<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="yes" name="apple-touch-fullscreen">
    <meta content="telephone=no,email=no" name="format-detection">
    <script src="http://g.tbcdn.cn/mtb/lib-flexible/0.3.4/??flexible_css.js,flexible.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STATIC_STYLES ?>swiper.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo USER_STATIC_STYLES ?>main.css">
    <link href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <title>产品详情</title>
</head>

<body>
<div class="swiper-container" data-space-between='10' data-pagination='.swiper-pagination' data-autoplay="1000"
     style="width: 100%;">
    <div class="swiper-wrapper">
        <?php $banner = explode(',', $roomDetail['room_img']);
        foreach ($banner as $v) { ?>
            <div class="swiper-slide">
                <img src="<?php echo IMG_GJ_600_LIST . $v ?>" alt="">
            </div>
        <?php } ?>
    </div>
    <div class="swiper-pagination"></div>
</div>
<div class="room-d">
    <div class="room-name">
        <h1><span><?php echo $roomDetail['name'] ?></span> - <?php echo $roomDetail['hotel_name'] ?><em>￥<?php echo $roomDetail['price'] ?></em></h1>
        <a href="tel:<?php echo $roomDetail['telephone'] ?>" class="phone">
            <i class="fa fa-phone fa-2x" aria-hidden="true"></i>
        </a>
    </div>
    <div class="room-d-inner">
        <?php echo $roomDetail['room_details'] ?>
    </div>
    <div class="primary-btn order-btn">
        <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/submitOrder', array('id' => $roomDetail['id'], 'encrypt_id' => $encrypt_id)) ?>">预订</a>
    </div>
</div>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>

</html>
