
<div class="prop_wrap prop_park">
    <div class="defined_title">
        <span class="icon_label_pp"></span>
        <div>本次停车费<?php echo '￥' . $order['order_money'];?></div>
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
            <div class="weui_cell_hd"><label class="weui_label">地址</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['address'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">日期 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray">
                    <?php if($order['parking_month_num'] == 6) {
                        echo '半年';
                    } elseif($order['parking_month_num'] == 12) {
                        echo '一年';
                    }else{
                        echo $order['parking_month_num'];echo '个月';
                    } ?>
                </p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">停车费</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo '￥' . $order['order_money'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">车辆品牌 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['car_brand'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">车牌号码 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $order['car_no'];?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">车前照 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary prop_pic_upload">
                <ul class="prop_pic_upload_bd">
                    <?php $car_info = json_decode($order['car_img'])?>
                    <li><img id="img1" src="<?php echo !empty($car_info[0]) ? (IMG_BASE_PATH).'wy_images/source/'.$car_info[0] : '';?>"></li>
                    <li><img id="img2" src="<?php echo !empty($car_info[1]) ? (IMG_BASE_PATH).'wy_images/source/'.$car_info[1] : '';?>"></li>
                    <li><img id="img3" src="<?php echo !empty($car_info[2]) ? (IMG_BASE_PATH).'wy_images/source/'.$car_info[2] : '';?>"></li>
                </ul>
            </div>
        </div>
    </div>
</div>
