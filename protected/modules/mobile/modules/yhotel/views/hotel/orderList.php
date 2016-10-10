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
<div class="user-order">
    <div class="cover">
        <div class="user-info">
            <img src="<?php echo empty($avatar) ? GJ_STATIC_IMAGES . 'face_man.png' : $avatar ?>" class="avatar" alt="avatar">
            <p class="user-name"><?php echo $nickname ?></p>
        </div>
        <ul class="order-status">
            <li>
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array('order_status' => HOTEL_ORDER_STATUS_WAITING, 'encrypt_id' => $encrypt_id)); ?>"
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
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array('order_status' => HOTEL_ORDER_STATUS_CONFIRM, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php if (isset($_GET['order_status'])) {
                       echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_CONFIRM ? 'status-active' : '';
                   } ?>">
                    <span><?php echo $confirmCount ?></span>
                    <p>已确定</p>
                </a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array('order_status' => HOTEL_ORDER_STATUS_CANCEL, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php if (isset($_GET['order_status'])) {
                       echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_CANCEL ? 'status-active' : '';
                   } ?>">
                    <span><?php echo $noSuccessCount ?></span>
                    <p>未成功</p>
                </a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array('order_status' => HOTEL_ORDER_STATUS_CHECKIN, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php if (isset($_GET['order_status'])) {
                       echo ($_GET['order_status']) == HOTEL_ORDER_STATUS_CHECKIN ? 'status-active' : '';
                   } ?>">
                    <span><?php echo $checkedCount ?></span>
                    <p>已入住</p>
                </a>
            </li>
        </ul>
    </div>
    <?php foreach ($list as $v) {
        $v['room_img'] = explode(',', $v['room_img']);
        ?>
        <div class="order-item">
            <div class="order-num">
                <span class="order_no" data-order-no="<?php echo $v['order_no'] ?>">订单号：<em><?php echo $v['order_no'] ?></em></span>
                <span class="status fr">
                    <?php if ($v['status'] == HOTEL_ORDER_STATUS_WAITING) {
                        echo '待确定';
                    } elseif ($v['status'] == HOTEL_ORDER_STATUS_CONFIRM) {
                        echo '已确定';
                    } elseif ($v['status'] == HOTEL_ORDER_STATUS_CANCEL || $v['status'] == HOTEL_ORDER_STATUS_REFUSE) {
                        echo '未成功';
                    } elseif ($v['status'] == HOTEL_ORDER_STATUS_CHECKIN) {
                        echo '已入住';
                    } ?>
                </span>
            </div>
            <div class="so-t">
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderDetail', array('encrypt_id' => $encrypt_id, 'order_no' => $v['order_no']))?>">
                    <img src="<?php echo IMG_GJ_125_LIST . $v['room_img'][0] ?>" class="room-img">
                <div class="room-info">
                    <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderDetail', array('encrypt_id' => $encrypt_id, 'order_no' => $v['order_no']))?>">
                        <p class="hotel-name">
                            <em><?php echo $v['hotel_name'] ?>：</em>
                            <?php echo $v['room_name'] ?>
                        </p>
                    </a>
                    <p class="store-name">
                        <?php echo ($v['branch_name'] != '' ? '(' . $v['branch_name'] . ')' : '')?>
                    </p>
                    <p class="room-price"><em>¥ <?php echo $v['price'] ?></em>×<?php echo $v['num'] ?></p>
                </div>
                <?php if ($v['status'] == HOTEL_ORDER_STATUS_WAITING || $v['status'] == HOTEL_ORDER_STATUS_CONFIRM) { ?>
                    <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/cancleOrder', array('order_no' => $v['order_no'], 'encrypt_id' => $encrypt_id)) ?>"
                       onclick="return confirm('确认取消吗？');" class="cancel-btn">
                        取消订单
                    </a>
                <?php } ?>
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
<!--<div class="footer">
    <ul class="booking-footer">
        <li>
            <a href="<?php /*echo Yii::app()->createUrl('mobile/yhotel/hotel/index', array('encrypt_id' => $encrypt_id)) */?>">
                <i class="fa fa-home fa-2x"></i>首页
            </a>
        </li>
        <li class="f-active">
            <a href="<?php /*echo Yii::app()->createUrl('mobile/yhotel/hotel/orderList', array('encrypt_id' => $encrypt_id, 'order_status' => HOTEL_ORDER_STATUS_CONFIRM)) */?>">
                <i class="fa fa-user fa-2x"></i>我的
            </a>
        </li>
    </ul>
</div>-->
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>
<script>
    $('.order_no').click(function () {
        var order_no = $(this).attr('data-order-no');
        window.location.href = "<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/orderDetail')?>?encrypt_id=<?php echo $encrypt_id ?>&order_no=" + order_no;
    })
</script>

</html>
