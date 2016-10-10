<div class="prop_wrap prop_property">
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label"><?php echo $GLOBALS['__PROPRIETOR_TYPE'][$order['type']];?></label></div>
            <div class="weui_input">
                <p class="cGray"><?php echo $order['name'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
            <div class="weui_input">
                <p class="cGray"><?php echo $order['tel'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">小区</label></div>
            <div class="weui_input">
                <p class="cGray"><?php echo $order['community'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">地址</label></div>
            <div class="weui_input">
                <p class="cGray"><?php echo $order['address'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">年份</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo substr($order['date'],0,4);?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">物业费</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><span class="cOrange">￥<?php echo $order['order_money'];?></span></p>
<!--                <span class="weui_input" id="property_fee"></span>-->
<!--                <input id="property_fee_money" name="property_fee_money" style="display: none">-->
            </div>
        </div>
    </div>

    <div class="tabbar_pay_55"></div>
    <div class="tabbar_pay flex">
        <div class="flex-box3 tabbar_pay_l">需要支付：<span class="cOrange" id="total_fee">￥<?php echo $order['order_money'];?></span></div>
        <div class="flex-box tabbar_pay_r">
<!--            <input type="button" onclick="checkIn()" class="weui_btn" value="确认支付"> </div>-->
            <a href="<?php echo Yii::app()->createUrl('mobile/pay/WyFeeOrderPay', array('order_id' => $order['id'], 'encrypt_id' => $this->getEncryptId()))?>" class="weui_btn">确认支付</a>
    </div>

</div>

<script>
    //赋值day_fee
    var one_year_fee = '<?php echo isset($fee_type['year_price']) ? $fee_type['year_price'] : '0';?>';
    //计算自定义费用总额
    function checkTime(obj,dayType){
        if (dayType == 1) {
            var sum = one_year_fee;
            $("#property_fee").text("￥" + sum);
            $("#total_fee").text("￥" + sum);
            $("#property_fee_month_num").val(12);
            $("#property_fee_money").val(sum);
        }
    }

    //JS验证表单
    function checkIn() {
        var property_fee_month_num = $("#property_fee_month_num").val();
        if (property_fee_month_num == '') {
            $.alert("缴费月数未填写", "提示");
        }
        if (property_fee_month_num != '') {
            $("#createForm").submit();
        }
    }
</script>
