<div class="prop_wrap prop_property">
    <div class="weui_tab weui_tab35">
        <div class="weui_navbar">
            <a class="weui_navbar_item" onclick="window.location.href= '<?php echo Yii::app()->createUrl('mobile/property/Ucenter/waterFeeList',array('encrypt_id' => $this->getEncryptId()));?>'">
                <span>水费<?php if(!empty($water_fee)){echo '(' . $water_fee . ')';}else{echo '(' . '0' . ')';};?></span>
            </a>
            <a class="weui_navbar_item" onclick="window.location.href= '<?php echo Yii::app()->createUrl('mobile/property/Ucenter/powerFeeList',array('encrypt_id' => $this->getEncryptId()));?>'">
                <span>电费<?php if(!empty($power_fee)){echo '(' . $power_fee . ')';}else{echo '(' . '0' . ')';};?></span>
            </a>
            <a class="weui_navbar_item weui_bar_item_on" onclick="window.location.href= '<?php echo Yii::app()->createUrl('mobile/property/Ucenter/propertyFeeList',array('encrypt_id' => $this->getEncryptId()));?>'">
                <span>物业费<?php if(!empty($property_fee)){echo '(' . $property_fee . ')';}else{echo '(' . '0' . ')';};?></span>
            </a>
            <a class="weui_navbar_item" onclick="window.location.href= '<?php echo Yii::app()->createUrl('mobile/property/Ucenter/parkingFeeList',array('encrypt_id' => $this->getEncryptId()));?>'">
                <span>停车费<?php if(!empty($parking_fee)){echo '(' . $parking_fee . ')';}else{echo '(' . '0' . ')';};?></span>
            </a>
        </div>
    </div>
    <?php if(!empty($list)){?>
        <?php foreach($list as $k => $v){?>
            <div class="weui_panel weui_panel_access defined_panel" onclick="window.location.href= '<?php echo Yii::app()->createUrl('mobile/property/fee/propertyFeeView', array('id' => $v['id'], 'encrypt_id' => $encrypt_id));?>' ">
                <div class="weui_panel_hd lh28 cGray333"><span class="icon_label_date mr10"></span>
                        <span class="fr fz18 cGreen"><?php echo $GLOBALS['ORDER_STATUS_PAY'][$v['pay_status']];?></span>
                    <span class="fz18"><?php echo date('Y年m月d日', strtotime($v['pay_time']));?></span>
                </div>
                <div class="weui_panel_bd">
                    <div class="weui_media_box weui_media_text">
                        <h4 class="weui_media_title">
                            <div class="name"><span class="icon_label_people mr10"></span>
                                <?php echo $v['name'];?>
                                <span class="icon_label">
                            <?php echo $GLOBALS['__PROPRIETOR_TYPE'][$v['type']];?>
                        </span>
                            </div>
                            <div class="phone"><span class="icon_phone_gray mr10"></span> <?php echo $v['tel'];?></div>
                        </h4>
                        <p class="weui_media_desc">
<!--                            预存金额：<em>--><?php //echo isset($v['money']) ? '￥' .  $v['money'] :'0.00';?><!--</em>-->
                            <span class="cGray">
                                <?php if($v['property_fee_month_num'] == 6) {
                                    echo '半年';
                                } elseif($v['property_fee_month_num'] == 12) {
                                    echo '一年';
                                }else{
                                    echo $v['property_fee_month_num'];echo '个月';
                                } ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        <?php }?>
    <?php }else{ ?>
        <!--没有内容的情况下的提示-->
        <div class="weui_cells_title ac cGray">没有相关内容</div>
    <?php }?>
</div>
