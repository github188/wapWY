
<div class="prop_wrap prop_property">
    <div class="defined_title">
        <span class="icon_label_pp"></span>
        <div>本次物业费：<?php echo '￥' . $order['order_money'];?></div>
    </div>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">姓名</label></div>
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
                <p class="cGray">
                    <?php if($order['property_fee_month_num'] == 6) {
                        echo '半年';
                    } elseif($order['property_fee_month_num'] == 12) {
                        echo '一年';
                    }else{
                        echo $order['property_fee_month_num'];echo '个月';
                    } ?>
                </p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">物业费</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo '￥' . $order['order_money'];?></p>
            </div>
        </div>
    </div>
</div>
