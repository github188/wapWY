<body class="logo">
<div class="title">储值记录</div>
<section class="wrap">
    <?php if (!empty($stored)) { ?>
        <?php foreach($stored as $key => $value){?>
        <div class="value">
            <span class="size">订单号：<em><?php echo $value['create_time'];?></em><?php echo $value['order_no'];?></span>
            <span>充<?php echo $value['stored_money']?>得<?php echo $value['get_money']?>活动<ins>×<?php echo $value['num']?></ins></span>
        </div>
            <div class="dline"></div>

        <?php }?>
    <?php }?>
</section>
</body>