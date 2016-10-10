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
    <title>客房预订</title>
</head>

<body>
<div class="container">
    <div class="swiper-container" data-space-between='10' data-pagination='.swiper-pagination' data-autoplay="1000"
         style="width: 100%;">
        <div class="swiper-wrapper">
            <?php foreach ($hotelBanner as $v) { ?>
                <div class="swiper-slide">
                    <img src="<?php echo IMG_GJ_600_LIST . $v ?>" alt="">
                </div>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <?php foreach ($roomLists as $v) { ?>
        <div class="booking-hotel">
            <div class="hotel-name">
                <span><?php echo $v[0]['hotel_name'] . ($v[0]['branch_name'] != '' ? '（' . $v[0]['branch_name'] . '）' : '') ?></span>
                <i class="fa fa-angle-down fr"></i>
            </div>
            <?php foreach ($v as $item) {
                $item['room_img'] = explode(',', $item['room_img']);
                ?>
                <div class="room-item">
                    <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/roomDetail', array('id' => $item['id'], 'encrypt_id' => $encrypt_id)) ?>"
                       class="room-img">
                        <img src="<?php echo IMG_GJ_125_LIST . $item['room_img'][0] ?>">
                    </a>
                    <div class="room-info">
                        <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/roomDetail', array('id' => $item['id'], 'encrypt_id' => $encrypt_id)) ?>">
                            <h2><?php echo $item['name'] ?>
                                <i class="fa fa-angle-double-right"></i>
                            </h2>
                        </a>
                        <p class="room-price">￥<?php echo $item['price'] ?></p>
                    </div>
                    <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/submitOrder', array('id' => $item['id'], 'encrypt_id' => $encrypt_id)) ?>" class="order-btn">预订</a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="footer">
        <ul class="booking-footer">
            <li class="f-active">
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/index', array('encrypt_id' => $encrypt_id)) ?>">
                    <i class="fa fa-home fa-2x"></i>首页
                </a>
            </li>
            <li><a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array('encrypt_id' => $encrypt_id)) ?>">
                    <i class="fa fa-user fa-2x"></i>我的
                </a>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>

</html>
