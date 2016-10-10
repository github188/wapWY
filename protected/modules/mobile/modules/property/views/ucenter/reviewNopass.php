<title>审核页面-不通过</title>
<div class="prop_wrap prop_review_wrap">
    <div class="weui_panel">
        <div class="step_wrap">
            <div class="step_num">
                <div class="step_com step_1 highlight_green">1</div>
                <div class="step_line step_line1 highlight_green"></div>
                <div class="step_com step_2 highlight_green">2</div>
                <div class="step_line step_line2 highlight_green"></div>
                <div class="step_com step_3 highlight_red">3</div>
            </div>
            <ul class="step_text">
                <li class="highlight_green">提交信息</li>
                <li class="highlight_green">审核中</li>
                <li class="highlight_red">审核结果</li>
            </ul>
        </div>
    </div>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell bt">
            <div class="weui_cell_hd"><label class="weui_label">姓名</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $proprietor_info['name']?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $proprietor_info['tel']?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">门禁卡号</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $proprietor_info['access_control_card_no']?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">类型</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $GLOBALS['__PROPRIETOR_TYPE'][$proprietor_info['type']]?></p>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">地址</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <p class="cGray"><?php echo $proprietor_info['community_name']. $proprietor_info['building_number'].'号楼'.$proprietor_info['room_number'].'室'?></p>
            </div>
        </div>
    </div>
    <div class="prop_warnning cRed"><i class="weui_icon_warn"></i> 您好，您提交的信息已被驳回，驳回原因：<?php echo $proprietor_info['remark']?></div>
    <div class="weui_btn_area login_area">
        <a class="weui_btn weui_btn_primary" href="<?php echo Yii::app()->createUrl('mobile/property/User/SignFail', array('encrypt_id' => $encrypt_id)); ?>" id="login_btn">修改信息</a>
    </div>
</div>

