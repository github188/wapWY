
<div class="prop_wrap prop_powerRate">
    <div class="defined_title">
        <span class="icon_label_pp"></span>
        <div>本月电费合计：￥<?php echo $order['order_money'];?></div>
    </div>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label"><?php echo $GLOBALS['__PROPRIETOR_TYPE'][$order['type']];?></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['name'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['tel'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">小区</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['community'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">地址</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['address'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">日期</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['date'];?></p>
            </div>
        </div>
        <?php if($efee_type == 1) {?>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">本月峰电量</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p class="fl fz12 cGray mr15">时间：<?php echo !empty($efee_info['peak_time']) ? $efee_info['peak_time'] : '';?> <span class="ml15">每度￥<em><?php echo !empty($efee_info['peak_price']) ? $efee_info['peak_price'] : '';?></em></span> </p>
                    <p class="cGray"><?php echo !empty($order['peak']) ? $order['peak'] : '';?>度</p>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">本月谷电量</label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <p class="fl fz12 cGray mr15">时间：<?php echo !empty($efee_info['trough_time']) ? $efee_info['trough_time'] : '';?> <span class="ml15">每度￥<em><?php echo !empty($efee_info['trough_price']) ? $efee_info['trough_price'] : '';?></em></span> </p>
                    <p class="cGray"><?php !empty($order['valley']) ? $order['valley'] : ''?>度</p>
                </div>
            </div>
        <?php }?>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">本月电费合计</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <!--                判断，是否为分时段计算电费-->
                <?php if($efee_type == 1) {?>
                    <p class="fl fz12 cGray"><?php echo $order['peak'] . '*' . !empty($efee_info['peak_price']) ? $efee_info['peak_price'] : '' . '+' . $order['valley'] . '*' . !empty($efee_info['trough_price']) ? $efee_info['trough_price'] : '' . '=';?></p>
                    <p class="cGray">￥<?php echo $order['order_money'];?></p>
                    <!--                    不分时段计算电费-->
                <?php }else{ ?>
                    <p class="fl fz12 cGray"><?php echo $order['electricity'] . '*' . $efee_info['price'] . '=';?></p>
                    <p class="cGray">￥<?php echo $order['order_money'];?></p>
                <?php }?>
            </div>
        </div>
    </div>
</div>
