<div class="popWrap" id="pop" style="width:650px;">
	<div class="pop_con" style="text-align:left">
        <div class="title" style="text-align:left"><em>优惠券使用说明</em><?php echo $info['title']?></div>
        <span><em>券类型</em>：<?php echo $info['type']?></span>
        <span><em>有效期</em>：<?php echo $info['date']?></span>
        <span><em><?php echo $info['for']?></em>：<?php echo $info['content']?></span>
        <div class="use-con">
            <div class="title" style="text-align:left">使用条件</div>
            <span>适用门店：  <?php echo $info['store']?></span>
            <span>最低消费：  <?php echo $info['min_pay']?></span>
            <span>单个订单最多使用张数：  <?php echo $info['num']?></span>
            <span>是否可以会员折扣叠加使用：  <?php echo $info['with_discount']?></span>
            <span><?php echo isset($info['red']) ? $info['red'] : '';?></span>
    	</div>
    </div>
</div>