<head>
    <title>商城订单</title>
</head>
<body class="mallOrder">
<header>
    <nav class="topnav clearfix">
        <ul>
            <li>
                <a href="<?php echo Yii::app()->createUrl('Dmall/order/orderList', array('pay_status' => ORDER_STATUS_UNPAID, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php echo isset($_GET['order_status']) ? '' : 'cur'; ?>">
                    <div class="num"><?php echo !empty($unPaidCount) ? $unPaidCount : '0'; ?></div>
                    <div class="state">待付款</div>
                </a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('Dmall/order/orderList', array('order_status' => ORDER_STATUS_WAITFORDELIVER, 'pay_status' => ORDER_STATUS_PAID, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php if (isset($_GET['order_status'])) {
                       echo ($_GET['order_status']) == ORDER_STATUS_WAITFORDELIVER ? 'cur' : '';
                   } ?>">
                    <div class="num"><?php echo !empty($waitCount) ? $waitCount : '0'; ?></div>
                    <div class="state">待发货</div>
                </a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->createUrl('Dmall/order/orderList', array('order_status' => ORDER_STATUS_DELIVER, 'pay_status' => ORDER_STATUS_PAID, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php if (isset($_GET['order_status'])) {
                       echo ($_GET['order_status']) == ORDER_STATUS_DELIVER ? 'cur' : '';
                   } ?>">
                    <div class="num"><?php echo !empty($deliverCount) ? $deliverCount : '0'; ?></div>
                    <div class="state">已发货</div>
                </a>
            </li>
            <li class="lastLi">
                <a href="<?php echo Yii::app()->createUrl('Dmall/order/orderList', array('order_status' => ORDER_STATUS_PART_COMPLETE, 'pay_status' => ORDER_STATUS_PAID, 'encrypt_id' => $encrypt_id)); ?>"
                   class="<?php if (isset($_GET['order_status'])) {
                       echo ($_GET['order_status'] == ORDER_STATUS_PART_COMPLETE || $_GET['order_status'] == ORDER_STATUS_REFUND) ? 'cur' : '';
                   } ?>">
                    <div class="num"><?php echo !empty($completeCount) ? $completeCount : '0'; ?></div>
                    <div class="state">已完成</div>
                </a>
            </li>
        </ul>
    </nav>
</header>

<section class="orderList">
    <?php if (!empty($list)) { ?>
        <?php foreach ($list as $k => $v) { ?>
            <article class="item clearfix">
                <div class="itemTop clearfix">
                    <div class="itemL">
                        <div class="filed">
                            <span class="label">订单号：</span>
                            <span class="text"><?php echo $v['order_no']; ?></span>
                        </div>
                        <div class="filed pay">
                            <span class="label">下单时间：</span>
                            <span class="text"><?php echo date('Y.m.d H:i:s', strtotime($v['create_time'])); ?></span>
                        </div>
                    </div>
                    <?php if ($v['pay_status'] == ORDER_STATUS_UNPAID) { ?>
                        <div class="itemR">
                            <a href="<?php echo Yii::app()->createUrl('Dmall/order/cancleOrder', array('order_id' => $v['id'], 'encrypt_id' => $encrypt_id)); ?>"
                               onclick="return confirm('确认取消吗？');">取消</a>
                        </div>
                    <?php } ?>
                </div>
                <!--end itemTop-->
                <?php if (!empty($v['order_sku'])) { ?>
                    <?php for ($i = 0; $i < 1; $i++) { ?>
                        <div class="itemMod clearfix">
				<span class="img">
					<img src="<?php $img_arr = explode(';', $v['order_sku'][$i]['product_img']);
                    echo IMG_GJ_80_LIST . $img_arr[0]; ?>">
				</span> 
				<span class="name"> 
					<em><?php echo $v['order_sku'][$i]['product_name']; ?></em> 
					<em class="state"><?php echo $v['order_sku'][$i]['sku_name']; ?></em>
				</span> 
				<span class="price"> 
					<em>¥ <?php echo $v['order_sku'][$i]['price']; ?></em> 
					<em>× <?php echo $v['order_sku'][$i]['num']; ?></em>
				</span>
                        </div>
                        <!--end itemMod-->
                    <?php } ?>
                <?php } ?>
                <div id="allOrder<?php echo $v['id']; ?>" style="display:none">
                    <?php if (!empty($v['order_sku'])) { ?>
                        <?php for ($i = 1; $i < count($v['order_sku']); $i++) { ?>
                            <div class="itemMod clearfix"> 
				<span class="img">
					<img src="<?php $img_arr = explode(';', $v['order_sku'][$i]['product_img']);
                    echo IMG_GJ_80_LIST . $img_arr[0]; ?>">
				</span> 
				<span class="name">
					<em><?php echo $v['order_sku'][$i]['product_name']; ?></em> 
					<em class="state"><?php echo $v['order_sku'][$i]['sku_name']; ?></em>
				</span> 
				<span class="price"> 
					<em>¥ <?php echo $v['order_sku'][$i]['price']; ?></em> 
					<em>× <?php echo $v['order_sku'][$i]['num']; ?></em>
 				</span>
                            </div>
                            <!--end itemMod-->
                        <?php } ?>
                    <?php } ?>

                </div>
                <?php if (!empty($v['order_sku'])) { ?>
                    <?php if (count($v['order_sku']) > 1) { ?><!-- 订单sku大于1才显示查看全部产品按钮 -->
                        <div class="more">
                            <a id="showOrder<?php echo $v['id']; ?>" href="javascript:;"
                               onclick="showOrder(<?php echo $v['id']; ?>)">查看全部产品</a>
                        </div>
                    <?php } ?>
                <?php } ?>
                <div class="itemEnd clearfix">
                    <span class="all">总价：<em><?php if ($v['freight_money'] == 0) {
                                echo number_format($v['real_pay'], 2, '.', '');
                            } else {
                                echo number_format($v['real_pay'] + $v['freight_money'], 2, '.', '') . '(含运费' . $v['freight_money'] . ')';
                            } ?></em></span>
				<span class="btn"> 
				 <?php if ($v['pay_status'] == ORDER_STATUS_UNPAID) { ?>
                     <input type="button" class="btn_com" value="付款"
                            onclick="window.location.href = '<?php echo Yii::app()->createUrl('Dmall/order/OrderPay', array('order_id' => $v['id'], 'encrypt_id' => $encrypt_id)) ?>';">
                 <?php } ?>
                    <?php if ($v['pay_status'] == ORDER_STATUS_PAID && $v['order_status'] == ORDER_STATUS_DELIVER) { ?>
                        <a href="<?php echo Yii::app()->createUrl('Dmall/order/confirmReceipt', array('order_id' => $v['id'], 'encrypt_id' => $encrypt_id)); ?>"
                           onclick="return confirm('确认收货吗');" class="btn_com_gray" style="width:90px">确认收货</a>
                        <!-- <a href="" class="btn_com_gray">物流</a> -->
                    <?php } ?>


                    <a href="<?php echo Yii::app()->createUrl('Dmall/order/orderDetail', array('order_id' => $v['id'], 'encrypt_id' => $encrypt_id)); ?>"
                       class="btn_com_gray">详情</a> 
				 
				</span>
                </div>
            </article>
        <?php } ?>
    <?php } ?>
</section>
<div class="footerH"></div>
<div id="footer">
    <ul>
        <li class="li01">
            <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/Index', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
        <li class="li02">
            <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/CommodityList', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
        <!--        <li class="li04"><a href="<?php //echo Yii::app() -> createUrl('Dmall/Cart/CartList');?>"></a></li> -->
        <li class="li05">
            <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/MemberCenter', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
    </ul>
</div><!--footer end-->
</body>

<script type="text/javascript">
    function showOrder(order_id) {
// 	   $.ajax({
//		   url : '<?php //echo Yii::app()->createUrl('uCenter/order/getMoreOrder'); ?>',
// 		   data : {order_id : order_id},
// 		   type : 'post',
// 		   async : false,
// 		   success : function(data){
// 			   var data = jQuery.parseJSON(data);
// 			   $.each(data.order_sku,function(inx,item){
// 				   if(inx > 0){
// 				     $("#allOrder"+order_id).append('<div class="itemMod clearfix">'+'<span class="img">'+'<img src="'+item.product_img+'">'+'</span>'
// 						                   +'<span class="name">'+'<em>'+item.product_name+'</em>'+'<em class="state">'+item.sku_name+'</em>'+'</span>'
// 						                   +'<span class="price">'+'<em>¥'+item.price+'</em>'+'<em>*'+item.num+'</em>'+'</span>'+'</div>');
// 				   }
// 			   });
// 		   }
// 	   });
        if ($("#allOrder" + order_id).is(":hidden")) { //如果剩余订单是隐藏的
            $("#allOrder" + order_id).show();
            $("#showOrder" + order_id).html("收起隐藏订单");
        } else {  //如果剩余订单是显示的
            $("#allOrder" + order_id).hide();
            $("#showOrder" + order_id).html("查看全部产品");
        }
    }
</script>

