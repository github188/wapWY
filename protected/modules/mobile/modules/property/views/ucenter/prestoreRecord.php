<div class="prop_wrap prop_prestore">
    <?php if(!empty($list)){?>
        <?php foreach($list as $k => $v){?>
            <div class="weui_panel weui_panel_access defined_panel">
                <div class="weui_panel_hd lh28 cGray333"><span class="icon_label_date mr10"></span> <span class="fr fz18 cGreen" style="color: red"><?php echo $GLOBALS['ORDER_STATUS_PAY'][$v['pay_status']];?></span><span class="fz18"><?php echo date('Y年m月d日', strtotime($v['create_time']));?></span> <em class="fz12"><?php echo date('H:i:s', strtotime($v['create_time']));?></em></div>
                <div class="weui_panel_bd">
                    <div class="weui_media_box weui_media_text">
                        <h4 class="weui_media_title">
                            <div class="name"><span class="icon_label_people mr10"></span>  <?php echo $v['name'];?> <span class="icon_label"><?php echo $GLOBALS['__PROPRIETOR_TYPE'][$v['type']];?></span></div>
                            <div class="phone"><span class="icon_phone_gray mr10"></span> <?php echo $v['tel'];?></div>
                        </h4>
                        <p class="weui_media_desc">
                            预存金额：<em>￥<?php echo $v['prestore_money'];?></em>
                        </p>
                    </div>
                </div>
            </div>
        <?php }?>
    <?php }?>
</div>
