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
    <title>提交订单</title>
</head>

<body>
<form action="<?php echo Yii::app()->createUrl('mobile/yhotel/hotel/submitOrder', array('encrypt_id' => $encrypt_id, 'id' => $id)) ?>"
    method="post" id="booking_form">
    <input type="hidden" name="store_id" value="<?php echo $roomDetail['store_id'] ?>">
    <div class="so">
        <div class="so-t">
            <?php $banner = explode(',', $roomDetail['room_img']); ?>
            <img src="<?php echo IMG_GJ_LIST . $banner[0] ?>" class="room-img">
            <div class="room-info">
                <p class="hotel-name"><?php echo $roomDetail['hotel_name'] ?>：</p>
                <p><?php echo $roomDetail['name'] ?></p>
                <p class="room-price">¥ <em><?php echo $roomDetail['price'] ?></em>/每晚</p>
            </div>
        </div>
        <div class="so-m">
            <div class="checktime">
                <span>入住时间</span>
                <input name="check_in_time" type="date" class="time check_in_time"
                       value="<?php echo empty($_POST['check_in_time']) ? date('Y-m-d', time()) : $_POST['check_in_time'] ?>">
            </div>
            <div class="total-day">
                <span class="total_day">共<?php
                    if (isset($_POST['check_out_time']) && isset($_POST['check_in_time'])) {
                        echo (strtotime($_POST['check_out_time']) - strtotime($_POST['check_in_time'])) / 86400;
                    } else { echo 1; }?>晚</span>
            </div>
            <div class="checktime">
                <span>退房时间</span>
                <input name="check_out_time" type="date" class="time check_out_time"
                       value="<?php echo empty($_POST['check_out_time']) ? date('Y-m-d', time() + 86400) : $_POST['check_out_time'] ?>">
            </div>
        </div>
        <div class="so-b">
            <div class="so-input">
                <label>房间数量</label>
                <div class="amount">
                    <button type="button" class="amount-btn remove_num">-</button>
                    <input name="room_num" id="room_num" type="text" class="num-text room_num"
                           value="<?php echo empty($_POST['room_num']) ? '1' : $_POST['room_num'] ?>" />
                    <button type="button" class="amount-btn add_num">+</button>
                </div>
            </div>
            <div class="so-input">
                <label>预订人：</label>
                <input name="person_name" id="person_name" type="text" required="required"
                       value="<?php echo empty($_POST['person_name']) ? '' : $_POST['person_name']?>"
                       class="so-text" placeholder="请输入真实姓名">
            </div>
            <div class="so-input">
                <label>手机号码：</label>
                <input name="person_tel" id="person_tel" type="text" maxlength="11" required="required"
                       value="<?php echo empty($_POST['person_tel']) ? '' : $_POST['person_tel']?>"
                       class="so-text" placeholder="请输入手机号码(用于接收通知)">
            </div>
        </div>
    </div>
    <div class="footer">
        <ul class="so-footer">
            <li class="total-price">总计：<em class="total_price">
                    <?php if (empty($_POST['room_num']) && empty($_POST['check_in_time']) && empty($_POST['check_out_time'])) {
                        echo '￥' . $roomDetail['price'];
                    } else {
                        $days = (strtotime($_POST['check_out_time']) - strtotime($_POST['check_in_time'])) / 86400;
                        echo '￥' .  sprintf("%.2f", $_POST['room_num'] * $days * $roomDetail['price']);
                    } ?></em>（到店支付）</li>
            <li class="so-btn"><a href="javascript:;" id="submit_form">提交订单</a></li>
        </ul>
    </div>
</form>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>swiper.js"></script>
<script type="text/javascript" src="<?php echo USER_STATIC_JS ?>hotel.js"></script>
</body>

<script>
    $(document).ready(function () {
        var check_in_time = Date.parse(new Date($('.check_in_time').val()));
        var check_out_time = Date.parse(new Date($('.check_out_time').val()));
        if (check_in_time < <?php echo strtotime(date('Ymd', time())) * 1000 ?>) {
            alert('入住时间不能小于当前时间');
        }
        if (check_in_time >= check_out_time) {
            alert('退房时间必须大于入住时间');
        } else {
            var time = check_out_time - check_in_time;
            var days = parseInt(time / (1000 * 60 * 60 * 24));
            $('.total-day .total_day').html('共' + days + '晚');
        }

        if ($('#room_num').val() < 1) {
            $(this).val(1);
        }
        $('.total-price .total_price').html('￥' + ($('#room_num').val() * <?php echo $roomDetail['price']?>).toFixed(2));
    });
    //减少房间数量
    $('.remove_num').click(function () {
        var check_in_time = Date.parse(new Date($('.check_in_time').val()));
        var check_out_time = Date.parse(new Date($('.check_out_time').val()));
        var time = check_out_time - check_in_time;
        var days = parseInt(time / (1000 * 60 * 60 * 24));
        var t = $('#room_num');
        if (parseInt(t.val()) > 1) {
            $('#room_num').val(parseInt(t.val()) - 1);
            $('.total-price .total_price').html('￥' + (t.val() * days  * <?php echo $roomDetail['price']?>).toFixed(2));
        }
    });
    //增加房间数量
    $('.add_num').click(function () {
        var check_in_time = Date.parse(new Date($('.check_in_time').val()));
        var check_out_time = Date.parse(new Date($('.check_out_time').val()));
        var time = check_out_time - check_in_time;
        var days = parseInt(time / (1000 * 60 * 60 * 24));
        var t = $('#room_num');
        $('#room_num').val(parseInt(t.val()) + 1);
        $('.total-price .total_price').html('￥' + (t.val() * days * <?php echo $roomDetail['price']?>).toFixed(2));
    });
    //房间数量输入框
    $('#room_num').blur(function () {
        var check_in_time = Date.parse(new Date($('.check_in_time').val()));
        var check_out_time = Date.parse(new Date($('.check_out_time').val()));
        var time = check_out_time - check_in_time;
        var days = parseInt(time / (1000 * 60 * 60 * 24));
        if ($(this).val() < 1) {
            $(this).val(1);
        }
        $('.total-price .total_price').html('￥' + ($(this).val() * days * <?php echo $roomDetail['price']?>).toFixed(2));
    })
    //入住时间选择
    $('.check_in_time').blur(function () {
        var check_in_time = Date.parse(new Date($(this).val()));
        var check_out_time = Date.parse(new Date($('.check_out_time').val()));
        if (check_in_time < <?php echo strtotime(date('Ymd', time())) * 1000 ?>) {
            alert('入住时间不能小于当前时间');
        }
        if (check_in_time >= check_out_time) {
            alert('退房时间必须大于入住时间');
        } else {
            var time = check_out_time - check_in_time;
            var days = parseInt(time / (1000 * 60 * 60 * 24));
            $('.total-day .total_day').html('共' + days + '晚');
            $('.total-price .total_price').html('￥' + ($('#room_num').val() * days * <?php echo $roomDetail['price']?>).toFixed(2));
        }
    });
    //退房时间选择
    $('.check_out_time').blur(function () {
        var check_out_time = Date.parse(new Date($(this).val()));
        var check_in_time = Date.parse(new Date($('.check_in_time').val()));
        if (check_in_time >= check_out_time) {
            alert('退房时间必须大于入住时间');
        } else {
            var time = check_out_time - check_in_time;
            var days = parseInt(time / (1000 * 60 * 60 * 24));
            $('.total-day .total_day').html('共' + days + '晚');
            $('.total-price .total_price').html('￥' + ($('#room_num').val() * days * <?php echo $roomDetail['price']?>).toFixed(2));
        }
    });
    $('#person_tel').blur(function () {
        if ($('#person_tel').val() == '') {
            alert('请输入手机号');
            return false;
        }
        var re = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if (!re.test($('#person_tel').val())) {
            alert('请输入正确的手机号');
            return false;
        }
    })
    //提交表单
    $('#submit_form').click(function () {
        var check_in_time = Date.parse(new Date($('.check_in_time').val()));
        var check_out_time = Date.parse(new Date($('.check_out_time').val()));
        if (check_in_time < '<?php echo strtotime(date('Ymd', time())) * 1000 ?>') {
            alert('入住时间不能小于当前时间');
            return false;
        }
        if (check_in_time >= check_out_time) {
            alert('退房时间必须大于入住时间');
            return false;
        }
        if ($('#room_num').val() < 1) {
            alert('房间数量必须大于等于1');
            return false;
        }
        if ($('#person_name').val() == '') {
            alert('请输入真实姓名');
            return false;
        }
        if ($('#person_tel').val() == '') {
            alert('请输入手机号');
            return false;
        }
        var re = /^1[3|4|5|8][0-9]\d{4,8}$/;
        if (!re.test($('#person_tel').val())) {
            alert('请输入正确的手机号');
            return false;
        }
        $('#booking_form').submit();
    });
</script>

</html>
