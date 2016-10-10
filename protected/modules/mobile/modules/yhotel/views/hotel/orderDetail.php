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
    <title>订单详情页</title>
</head>

<body>
<div class="status-wrap <?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_WAITING) {
    echo 'unconfirm';
} elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_CONFIRM) {
    echo 'confirm';
} elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_CHECKIN) {
    echo 'checked';
} elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_CANCEL || $orderDetail['status'] == HOTEL_ORDER_STATUS_REFUSE) {
    echo 'cancel';
} ?>">
    <span class="status">订单状态：<?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_WAITING) {
            echo '待确定';
        } elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_CONFIRM) {
            echo '已确定';
        } elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_CHECKIN) {
            echo '已入住';
        } elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_CANCEL) {
            echo '已取消';
        } elseif ($orderDetail['status'] == HOTEL_ORDER_STATUS_REFUSE) {
            echo '已拒绝';
        } ?></span>
    <?php if ($orderDetail['status'] != HOTEL_ORDER_STATUS_CANCEL && $orderDetail['status'] != HOTEL_ORDER_STATUS_REFUSE) { ?>
        <div class="status-line">
            <ul>
                <li class="status-active">
                    <span class="circule">1</span>
                    <p>提交订单</p>
                </li>
                <li class="es <?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_CONFIRM || $orderDetail['status'] == HOTEL_ORDER_STATUS_CHECKIN) {
                    echo 'status-active';
                } ?>">
                    <span class="circule">2</span>
                    <p>订单确认</p>
                </li>
                <li class="es <?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_CHECKIN) {
                    echo 'status-active';
                } ?>">
                    <span class="circule">3</span>
                    <p>入住</p>
                </li>
            </ul>
        </div>
    <?php } ?>
    <div class="order-detail">
        <div class="order-detail-item">
            <span class="q">联系人姓名：</span>
            <span class="a"><?php echo $orderDetail['person_name'] ?></span>
        </div>
        <div class="order-detail-item">
            <span class="q">联系人手机：</span>
            <span class="a"><?php echo $orderDetail['person_tel'] ?></span>
        </div>
        <div class="order-detail-item">
            <span class="q">房间数量：</span>
            <span class="a"><?php echo $orderDetail['room_name'] ?><em><?php echo $orderDetail['num'] ?></em>间</span>
        </div>
        <div class="order-detail-item">
            <span class="q">入住时间：</span>
            <span class="a">
                <?php echo date('m月d日', $orderDetail['check_in_time']) ?> --
                <?php echo date('m月d日', $orderDetail['check_out_time']) ?>
                共<?php echo ($orderDetail['check_out_time'] - $orderDetail['check_in_time']) / 86400 ?>晚
            </span>
        </div>
        <div class="order-detail-item">
            <span class="q">订单号：</span>
            <span class="a"><?php echo $orderDetail['order_no'] ?></span>
        </div>
        <div class="order-detail-item last">
            <span class="q">客服电话：</span>
            <span class="a"><?php echo $orderDetail['service_tel'] ?></span>
            <a href="tel:<?php echo $orderDetail['service_tel'] ?>" class="service fr">联系客服</a>
        </div>
    </div>
    <?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_WAITING || $orderDetail['status'] == HOTEL_ORDER_STATUS_CONFIRM) { ?>
        <div class="primary-btn">
            <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/cancleOrder', array('order_no' => $orderDetail['order_no'], 'encrypt_id' => $encrypt_id)) ?>"
               onclick="return confirm('确认取消吗？');">取消订单</a>
        </div>
        <?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_CONFIRM) { ?>
            <div class="check-btn">
                <a href="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/checkinOrder', array('order_no' => $orderDetail['order_no'], 'encrypt_id' => $encrypt_id)) ?>"
                   onclick="return confirm('确认入住吗？');">入住</a>
            </div>
        <?php }
    } ?>
</div>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>
<script>
    <?php if ($orderDetail['status'] == HOTEL_ORDER_STATUS_WAITING) { ?>
    function getOrderStatus() {
        $.post("<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/getOrderStstus') ?>", {
            order_no: '<?php echo $orderDetail['order_no']?>'
        }, function (data) {
            if (data == <?php echo HOTEL_ORDER_STATUS_CONFIRM ?> || data == <?php echo HOTEL_ORDER_STATUS_REFUSE ?>) {
                window.location.reload();
            }
        });
    }
    setInterval(getOrderStatus, 3000);
    <?php } ?>
</script>
</html>
