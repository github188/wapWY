
<div class="prop_wrap prop_uc_wrap">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mobile/property/Ucenter/UserDetail', array('encrypt_id' => $encrypt_id)), 'post'); ?>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">姓名：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('name', !empty($proprietor_info['name']) ? $proprietor_info['name'] : '', array('class' => 'weui_input')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('tel', !empty($proprietor_info['tel']) ? $proprietor_info['tel'] : '', array('class' => 'weui_input')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">门禁卡号：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('access_control_card_no', !empty($proprietor_info['access_control_card_no']) ? $proprietor_info['access_control_card_no'] : '', array('class' => 'weui_input')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">类型：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php if (isset($proprietor_info['type'])){?>
                <select class="weui_select prop_select_arrow weui_input" name="type" id="type">
                    <option value=<?php echo PROPRIETOR_TYPE_OWNER ?>  <?php if ($proprietor_info['type'] == PROPRIETOR_TYPE_OWNER){ echo 'selected'; };?>>业主</option>
                    <option value=<?php echo PROPRIETOR_TYPE_TENEMENT ?> <?php if ($proprietor_info['type'] == PROPRIETOR_TYPE_TENEMENT){ echo 'selected'; };?>>租户</option>
                </select>
                <?php }?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">小区：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <select class="weui_select prop_select_arrow weui_input" name="community_id" id="community_id">
                    <?php if (!empty($community_list)){?>
                        <?php foreach ($community_list as $k => $v){?>
                            <option value=<?php echo $v['id'] ?> <?php if ($v['id'] == $proprietor_info['community_id']){ echo 'selected'; };?>> <?php echo $v['name'] ?></option>
                        <?php }?>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">楼号：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('building_number', !empty($proprietor_info['building_number']) ? $proprietor_info['building_number'] : '', array('class' => 'weui_input')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">房间：</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('room_number', !empty($proprietor_info['room_number']) ? $proprietor_info['room_number'] : '', array('class' => 'weui_input')); ?>
            </div>
        </div>
    </div>
    <div class="weui_btn_area">
        <button type="submit" class="weui_btn weui_btn_primary">保存</button>
        <a class="weui_btn weui_btn_default" href="<?php echo Yii::app()->createUrl('mobile/property/User/Logout', array('encrypt_id' => $encrypt_id)); ?>" id="login_btn">退出登录</a>
    </div>
    <?php echo CHtml::endForm(); ?>
</div>