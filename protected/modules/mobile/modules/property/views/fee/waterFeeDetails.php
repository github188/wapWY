<div class="prop_wrap prop_wanterRent">
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
                <p class="cGray"><?php echo substr($order['date'],0,7);?></p>
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
                <p class="cGray"><span class="cOrange">￥<?php echo $order['order_money'];?></span></p>
            </div>
        </div>
    </div>
<!--    <div class="weui_cells weui_cells_form">-->
<!--        <div class="weui_cell">-->
<!--            <div class="weui_cell_hd"><label class="weui_label">预存金额</label></div>-->
<!--            <div class="weui_cell_bd weui_cell_primary">-->
<!--                <p class="cGreen">-->
<!--                    --><?php //if($money > 0){?>
<!--                        <span class="fr"><i class="weui_icon_success"></i></span> ￥--><?php //echo $money;?>
<!--                    --><?php //}else{ ?>
<!--                        <span class="cOrange">余额不足</span>-->
<!--                    --><?php //}?>
<!--                </p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <div class="tabbar_pay_55"></div>
    <div class="tabbar_pay flex">
        <div class="flex-box3 tabbar_pay_l">需要支付：<span class="cOrange"> ￥<?php echo $order['order_money'];?></span></div>
        <div class="flex-box tabbar_pay_r">
<!--            <input type="submit" class="weui_btn" value="确认支付"> -->
            <a href="<?php echo Yii::app()->createUrl('mobile/pay/wyOrderPay', array('order_id' => $order['id'], 'encrypt_id' => $this->getEncryptId()))?>" class="weui_btn">确认支付</a>
        </div>
    </div>
</div>
