<div class="prop_wrap prop_family prop_family_edit">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mobile/property/Ucenter/EditFamily', array('encrypt_id' => $encrypt_id)), 'post'); ?>
<!--    家庭成员1-->
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="member_name" value="<?php echo $family_members[0];?>" placeholder="请输入家庭成员真实姓名">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="member_phone" value="<?php echo $family_members[1];?>" placeholder="请输入家庭成员手机号">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="member_access_control_card_no" value="<?php echo $family_members[2];?>" placeholder="请输入家庭成员门禁卡号">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">与业主关系 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="relationship" value="<?php echo $family_members[3];?>" placeholder="与业主关系">
            </div>
        </div>
    </div>
<!--    家庭成员2-->
    <?php if(!empty($family_members[4])){?>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="member_name1" value="<?php echo $family_members[4];?>" placeholder="请输入家庭成员真实姓名">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="member_phone1" value="<?php echo $family_members[5];?>" placeholder="请输入家庭成员手机号">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="member_access_control_card_no1" value="<?php echo $family_members[6];?>" placeholder="请输入家庭成员门禁卡号">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">与业主关系 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="relationship1" value="<?php echo $family_members[7];?>" placeholder="与业主关系">
                </div>
            </div>
        </div>
    <?php }?>
<!--    家庭成员3-->
    <?php if(!empty($family_members[8])){?>
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="member_name2" value="<?php echo $family_members[8];?>" placeholder="请输入家庭成员真实姓名">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="member_phone2" value="<?php echo $family_members[9];?>" placeholder="请输入家庭成员手机号">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="member_access_control_card_no2" value="<?php echo $family_members[10];?>" placeholder="请输入家庭成员门禁卡号">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">与业主关系 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" type="text" name="relationship2" value="<?php echo $family_members[11];?>" placeholder="与业主关系">
                </div>
            </div>
        </div>
    <?php }?>
    <div class="weui_cell">
        <span class="weui_cells_title add-btn" id="addFamily"><span class="iconAdd"></span> 增添家庭成员</span>
    </div>

    <div class="weui_btn_area">
        <?php echo CHtml::submitButton('添加', array('class' => 'weui_btn weui_btn_primary','id' => 'login_btn', 'encrypt_id' => $encrypt_id)); ?>
    </div>
    <?php echo CHtml::endForm(); ?>
</div>

<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php }?>

<script type="text/javascript">
    var tmp = $('.prop_family_edit .weui_cells_form').length;
    //添加家庭成员
    $("#addFamily").click(function(){
        var obj = '<div class="weui_cells weui_cells_form"><div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="text" name="member_name'+tmp+'" placeholder="请输入家庭成员真实姓名"></div></div> <div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="number" name="member_phone'+tmp+'" maxlength="11" placeholder="请输入家庭成员手机号"></div></div> <div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="text" name="member_access_control_card_no'+tmp+'" placeholder="请输入家庭成员门禁卡号"></div></div> <div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">与业主关系 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="text" name="relationship'+tmp+'" placeholder="如：父亲"></div></div></div>';
        if($('.prop_family_edit .weui_cells_form').length < 3){
            //在它父级对象的前面插入元素
            $(this).parent().before(obj);
            tmp ++;
        }else{
            $.alert("最多只能添加三位家庭成员", "提示");
        }
    })
</script>
