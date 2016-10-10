<div class="prop_wrap prop_repair_record">
    <?php if(!empty($list)){?>
        <?php foreach($list as $k => $v){?>
            <div class="weui_panel weui_panel_access defined_panel">
                <div class="weui_panel_hd lh28 cGray333"><span class="icon_label_date mr10"></span> <span class="fr fz18 cGreen"><?php echo $GLOBALS['REPORT_REPAIR_RECORD_STATUS'][$v['status']];?></span><span class="fz18"><?php echo date('Y年m月d日',strtotime($v['create_time']));?></span> <em class="fz12"><?php echo date('H:i:s',strtotime($v['create_time']));?></em></div>
                <div class="weui_panel_bd">
                    <div class="weui_media_box weui_media_text">
                        <h4 class="weui_media_title">
                            <div class="name"><span class="icon_label_people mr10"></span> <?php echo $v['name'];?> <span class="icon_label"><?php echo $GLOBALS['__PROPRIETOR_TYPE'][$v['type']]?></span></div>
                            <div class="address mp10"><span class="icon_label_addr mr10"></span> <?php echo $v['address'];?></div>
                        </h4>
                        <h4 class="weui_media_title">
                            <div class="phone mp10"><span class="icon_phone_gray mr10"></span> <?php echo $v['tel'];?></div>
                            <div class="position mp10"><span class="icon_position_gray mr10"></span> <?php echo $GLOBALS['REPORT_REPAIR_RECORD_TYPE'][$v['area_type']];?></div>
                        </h4>
                    </div>
                    <div class="textarea">报修内容：<?php echo $v['detail']?></div>
                    <?php if ($v['status'] == REPORT_REPAIR_RECORD_STATUS_COMPLETE && $v['remark'] == null){?>
                    <div class="weui_btn_area">
                        <a href="javascript:;" class="weui_btn weui_btn_mini weui_btn_primary" onclick="setProprietorId('<?php echo $v['id']?>')">确定</a>
                    </div>
                    <?php }?>
                </div>
            </div>
        <?php }?>
    <?php }else{ ?>
        <!--没有内容的情况下的提示-->
        <div class="weui_cells_title ac cGray">没有相关内容</div>
    <?php }?>
</div>

<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php }?>

<!--反馈弹出框-->
<div class="popWrap prop_feedbackck_pop" style="display:none ">
        <?php echo CHtml::beginForm($this->createUrl('Repair/RepairComment',array('encrypt_id' => $encrypt_id)), 'post'); ?>
        <div class="wq_panel">
            <div class="weui_cells_title"><i class="weui_icon_cancel popClose"></i>反馈：</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <textarea  name="remark" placeholder='请输入您对此次维修反馈的内容...' class='weui_textarea prop_dialog_textarea'></textarea>
                    </div>
                </div>
            </div>
            <div class="weui_btn_area login_area flex">
                <button type="submit" class="flex-box weui_btn weui_btn_primary">确定</button>
            </div>
        </div>
    <input type="hidden" value="" id="proprietorId" name="id">
    <?php echo CHtml::endForm(); ?>
</div>

<script>
    /**
     *显示反馈框
     */
    $(function () {
        $(".weui_btn_primary").click(function () {
            $(".prop_feedbackck_pop").show();
        });
    })

    /**
     * 驳回 商户id赋值
     * @param val
     */
    function setProprietorId(val) {
        $("#proprietorId").val(val);
    }
</script>