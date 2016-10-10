

<div class="prop_wrap prop_wanterRent">
    <div class="defined_title">
        <span class="icon_label_pp"></span>
        <div>本月水费合计：￥<?php echo $order['order_money'];?></div>
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
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">本月用水量</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['water_ton'] . '吨';?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">每吨水费</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray">￥<?php echo $wfee_info['price'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">本月水费合计</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray">￥<?php echo $order['order_money'];?></p>
            </div>
        </div>
    </div>
</div>
