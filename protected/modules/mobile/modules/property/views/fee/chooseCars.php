<div class="prop_wrap">
    <div class="prop_index_wrap vehicle_index_wrap">
        <div class="weui_grids">
            <?php if (!empty($proprietor_car)) {?>
            <?php foreach ($proprietor_car as $k=>$v) {?>
            <a href="<?php echo $this->createUrl('Fee/ToParkingFee', array('encrypt_id' => $encrypt_id,'car_id' => $v['id']));?>" class="weui_grid js_grid" data-id="">
                <div class="weui_grid_icon">
                    <img src="<?php echo WY_STATIC_IMAGES?>property/icon6.png" alt="">
                </div>
                <p class="weui_grid_label">
                    <?php echo !empty($proprietor_car) ? $v['car_no'] : '车辆'?>
                </p>
            </a>
            <?php }?>
            <?php }else{?>
                <a href="<?php echo $this->createUrl('Fee/ToParkingFee', array('encrypt_id' => $encrypt_id,'car_id' => ''));?>" class="weui_grid js_grid" data-id="">
                    <div class="weui_grid_icon">
                        <img src="<?php echo WY_STATIC_IMAGES?>property/icon6.png" alt="">
                    </div>
                    <p class="weui_grid_label">
                        车辆一
                    </p>
                </a>
            <?php }?>
            <a href="javascript:;" class="weui_grid js_grid" data-id="toast" id="increaseVehicle">
                <div class="weui_grid_icon">
                    <img src="<?php echo WY_STATIC_IMAGES?>property/icon9.png" alt="">
                </div>
                <p class="weui_grid_label">
                    增加车辆
                </p>
            </a>
        </div>
    </div>

</div>
<script>
    $("#increaseVehicle").click(function(){
        var html = "";
        html += '<a href="'+ '<?php echo $this->createUrl("Fee/ToParkingFee", array("encrypt_id" => $encrypt_id,'car_id' => ''));?>' +'" class="weui_grid js_grid" data-id="toast">';
        html += '<div class="weui_grid_icon">';
        html += '<img src="<?php echo WY_STATIC_IMAGES?>property/icon6.png" alt="">';
        html += '</div>';
        html += '<p class="weui_grid_label">车辆</p></a>';
        $(this).before(html);
    })
</script>

