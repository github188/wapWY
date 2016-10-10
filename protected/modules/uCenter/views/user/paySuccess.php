<body>

<div class="paySucceed">
    <div class="bg">
        <div class="icon"></div>
        <p>成功付款<strong><?php echo $money; ?></strong>元</p>
    </div>
    <div class="btn">
        <?php if (isset($ordertype) && $ordertype == 'SC') { ?>
            <a style="width:100%" class="btn_com"
               href="<?php echo Yii::app()->createUrl('mall/Order/OrderList', array('encrypt_id' => $encrypt_id)) ?>">确定</a>
        <?php } else { ?>
            <a style="width:100%" class="btn_com"
               href="<?php echo Yii::app()->createUrl('uCenter/Stored/stored', array('encrypt_id' => $encrypt_id)) ?>">确定</a>
        <?php } ?>
    </div>
</div>
</body>