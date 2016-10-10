<head>
    <title>预订详情</title>
</head>

<body>
<div class="status"><?php echo $GLOBALS['BOOK_RECORD_STATUS'][$book['status']] ?></div>
<section class="set-con">
    <div class="con">
        <div class="filed">
            <span class="set-hol"><?php echo $book['name'] ?></span>
            <span class="set-time"><?php echo $book['time'] ?><em><?php echo $book['people_num'] ?>人</em></span>
            <span class="set-name no"><?php echo $book['book_name'] ?><?php echo $book['sex'] ?>
                <em><?php echo $book['phone_num'] ?></em></span>
        </div>
    </div>
    <div class="con">
        <?php if ($book['status'] == BOOK_RECORD_STATUS_ARRIVE) { ?>
        <p class="cur">
            <label>订座成功</label>
            <label>
                <span><?php echo date('H:i:s', strtotime($book['arrive_time'])) ?></span>
            </label>
        </p>
        <p>
            <?php } elseif ($book['status'] == BOOK_RECORD_STATUS_CANCEL) { ?>
        <p class="cur">
            <label>取消预约</label>
            <label>
                <span><?php echo date('H:i:s', strtotime($book['cancel_time'])) ?></span>
            </label>
        </p>
        <p>
            <?php } elseif ($book['status'] == BOOK_RECORD_STATUS_REFUSE) { ?>
        <p class="cur">
            <label>商户已拒单</label>
            <label>
                <span><?php echo date('H:i:s', strtotime($book['deal_time'])) ?></span>
            </label>
        </p>
        <p>
            <?php } else { ?>
        <p class="cur">
            <?php } ?>
            <?php if (!empty($book['deal_time'])) { ?>
            <label>商户接单</label>
            <span><?php echo date('H:i:s', strtotime($book['deal_time'])) ?></span>
        </p>
        <p>
            <?php } ?>

            <label> 正发送信息给商家</label>
            <span><?php echo date('H:i:s', strtotime($book['book_time'])) ?></span>
        </p>

        <p>
            <label>订单提交</label>
            <span><?php echo date('H:i:s', strtotime($book['book_time'])) ?></span>
        </p>
    </div>
    <div class="bottom">
        <?php if ($book['status'] == BOOK_RECORD_STATUS_WAIT || $book['status'] == BOOK_RECORD_STATUS_ACCEPT) { ?>
            <span><a href="<?php echo Yii::app()->createUrl('uCenter/user/bookCancel', array('record_id' => $book['id'], 'encrypt_id' => $encrypt_id)) ?>"
                    class="btn-com-gray" onclick="if(confirm('确定取消?')==false)return false;">取消订单</a></span>
        <?php } ?>
    </div>
</section>
</body>
</html>
