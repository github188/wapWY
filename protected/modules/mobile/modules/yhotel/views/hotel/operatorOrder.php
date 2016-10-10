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
    <title>我的酒店订单</title>
</head>

<body>
<div class="admin">
    <ul class="order-status">
        <li>
            <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/operatorOrderList', array('order_status' => HOTEL_ORDER_STATUS_WAITING)); ?>"
               class="<?php if (isset($_GET['order_status'])) {
                   echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_WAITING ? 'status-active' : '';
               } else {
                   echo 'status-active';
               } ?>">
                <span><?php echo $unconfirmCount ?></span>
                <p>待确定</p>
            </a>
        </li>
        <li>
            <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/operatorOrderList', array('order_status' => HOTEL_ORDER_STATUS_CONFIRM)); ?>"
               class="<?php if (isset($_GET['order_status'])) {
                   echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_CONFIRM ? 'status-active' : '';
               } ?>">
                <span><?php echo $confirmCount ?></span>
                <p>已确定</p>
            </a>
        </li>
        <li>
            <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/operatorOrderList', array('order_status' => HOTEL_ORDER_STATUS_CANCEL)); ?>"
               class="<?php if (isset($_GET['order_status'])) {
                   echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_CANCEL ? 'status-active' : '';
               } ?>">
                <span><?php echo $noSuccessCount ?></span>
                <p>未成功</p>
            </a>
        </li>
        <li>
            <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/operatorOrderList', array('order_status' => HOTEL_ORDER_STATUS_CHECKIN)); ?>"
               class="last <?php if (isset($_GET['order_status'])) {
                   echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_CHECKIN ? 'status-active' : '';
               } ?>">
                <span><?php echo $checkedCount ?></span>
                <p>已入住</p>
            </a>
        </li>
    </ul>
    <?php foreach ($list as $v) {
        $v['room_img'] = explode(',', $v['room_img']);
        ?>
        <div class="order-item">
            <div class="order-num">
                <span>订单号：<em><?php echo $v['order_no'] ?></em></span>
            </div>
            <div class="so-t">
                <a href="javascript:;">
                    <img src="<?php echo IMG_GJ_125_LIST . $v['room_img'][0] ?>"
                         class="room-img">
                </a>
                <div class="room-info">
                    <a href="javascript:;">
                        <p class="hotel-name">
                            <em><?php /*echo $v['hotel_name'] */?></em><?php echo $v['room_name'] ?>
                        </p>
                    </a>
                    <p class="room-price"><em>¥ <?php echo $v['price'] ?></em>×<?php echo $v['num'] ?></p>

                    <?php if ($v['status'] == HOTEL_ORDER_STATUS_WAITING) { ?>
                        <div class="admin-operate">
                            <a href="javascript:;" class="sure-btn confirm_order"
                               data-order-no="<?php echo $v['order_no'] ?>">确定</a>
                            <a href="javascript:;" class="refuse-btn refuse_order"
                               data-order-no="<?php echo $v['order_no'] ?>">拒绝</a>
                        </div>
                    <?php } elseif ($v['status'] == HOTEL_ORDER_STATUS_CONFIRM) { ?>
                        <div class="admin-operate">
                            <a href="javascript:;" class="sure-btn checkin_order"
                               data-order-no="<?php echo $v['order_no'] ?>">入住</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="user-info">
                <span>预订人：<?php echo $v['person_name'] ?></span>
                <span>联系方式：<?php echo $v['person_tel'] ?></span>
                <div class="order-time">预定时间：<?php echo $v['check_in_time'] ?>
                    -- <?php echo $v['check_out_time'] ?></div>
            </div>
        </div>
    <?php } ?>
</div>
<div class="primary-btn quit-btn">
    <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/operatorLoginOut'); ?>"
       onclick="return confirm('确认退出吗？')">退出</a>
</div>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>
<script>
    $('.confirm_order').click(function () {
        if (confirm('确认确定吗？')) {
            var order_no = $(this).attr('data-order-no');
            $.post("<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/confirmOrder') ?>", {
                order_no: order_no
            }, function (data) {
                if (data == 'success') {
                    alert('确定成功');
                    window.location.reload();
                } else {
                    alert('确定失败');
                }
            });
        }
    });

    $('.refuse_order').click(function () {
        if (confirm('确认拒绝吗？')) {
            var order_no = $(this).attr('data-order-no');
            $.post("<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/refuseOrder') ?>", {
                order_no: order_no
            }, function (data) {
                if (data == 'success') {
                    alert('拒绝成功');
                    window.location.reload();
                } else {
                    alert('拒绝失败');
                }
            });
        }
    });

    $('.checkin_order').click(function () {
        if (confirm('确认入住吗？')) {
            var order_no = $(this).attr('data-order-no');
            $.post("<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/ajaxcheckinOrder') ?>", {
                order_no: order_no
            }, function (data) {
                if (data == 'success') {
                    alert('入住成功');
                    window.location.reload();
                } else {
                    alert('入住失败');
                }
            });
        }
    });
</script>

</html>
