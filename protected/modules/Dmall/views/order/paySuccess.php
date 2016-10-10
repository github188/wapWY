<body>

<div class="paySucceed">
    <div class="bg">
        <div class="icon"></div>
        <p>成功付款<strong><?php echo $money; ?></strong>元</p>
    </div>
    <div class="btn">
        <a style="width:100%" class="btn_com"
           href="<?php echo Yii::app()->createUrl('Dmall/Order/OrderList', array('encrypt_id' => $encrypt_id)) ?>">确定</a>
    </div>
</div>

<div class="qrcode-pop-mask" style="display: none"></div>
<div class="qrcode-pop" style="display: none">
    <div class="qrcode-img">
        <img src="<?php echo !empty($qrcode) ? IMG_GJ_LIST . $qrcode : ''; ?>" alt="qrcode" class="qrcode">
    </div>
    <div class="qrcode-pop-tips">
        <p>长按二维码关注公众号,<br>否则您无法查看<em>订单</em>和<em>优惠券</em>哦</p>
    </div>
</div>


</body>