<?php echo CHtml::beginForm()?>
    <div class="prop_wrap prop_prestore">
        <div class="weui_cells">
            <div class="weui_cell clear_flex">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>充值金额</p>
                </div>
                <div class="weui_cell_ft">
                    <input class="weui_input fz30 fm" type="text" placeholder="￥0.00" name="money">
                </div>
            </div>
        </div>
        <div class="weui_btn_area login_area">
            <input class="weui_btn weui_btn_primary" type="submit" value="确认支付">
        </div>

    </div>
<?php echo CHtml::endForm()?>