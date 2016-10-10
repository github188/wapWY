<div class="prop_wrap prop_uc_wrap">
    <div class="weui_panel prop_uc_head">
        <div class="weui_panel_bd">
            <div class="weui_media_box weui_media_appmsg weui_cells_access">
                <div class="weui_media_hd">
                    <img class="weui_media_appmsg_thumb" src="<?php echo $proprietor['avatar'];?>" alt="">
                </div>
                <div class="weui_media_bd">
                    <a href="<?php echo $this->createUrl('Ucenter/UserDetail', array('encrypt_id' => $encrypt_id));?>">
                    <h4 class="weui_media_title"><?php echo $proprietor['name'];?> <span class="icon_label"><?php echo $GLOBALS['__PROPRIETOR_TYPE'][$proprietor['type']];?></span> </h4>
                    <p class="weui_media_desc"><span class="iconPhone"></span> <?php echo $proprietor['tel'];?></p>
                    </a>
                </div>
                <!--                <div class="weui_cell_ft"><a href="#" class="prop_QACode"></a> </div>-->
            </div>
        </div>
    </div>

<!--    <div class="weui_panel prop_uc_bd">-->
<!--        <div class="weui_panel_bd">-->
<!--            <div class="weui_media_box weui_media_small_appmsg">-->
<!--                <div class="weui_cells weui_cells_access">-->
<!--                    <a class="weui_cell prop_uc_icon01" href="--><?php //echo $this->createUrl('Prestore/AddPrestoreMoney', array('encrypt_id' => $encrypt_id));?><!--">-->
<!--                        <div class="weui_cell_hd"><img src=""></div>-->
<!--                        <div class="weui_cell_bd weui_cell_primary">-->
<!--                            <p>预存金额</p>-->
<!--                        </div>-->
<!--                        <span class="weui_cell_ft"><span class="cOrange">--><?php //echo $proprietor['money']?><!--</span> </span>-->
<!--                    </a>-->
<!--                    <a class="weui_cell prop_uc_icon02" href="--><?php //echo $this->createUrl('Prestore/PrestoreRecord', array('encrypt_id' => $encrypt_id));?><!--">-->
<!--                        <div class="weui_cell_hd"><img src=""></div>-->
<!--                        <div class="weui_cell_bd weui_cell_primary">-->
<!--                            <p>预存记录</p>-->
<!--                        </div>-->
<!--                        <span class="weui_cell_ft"></span>-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!--        </div>-->
<!--    </div>-->

    <div class="weui_panel prop_uc_bd">
        <div class="weui_panel_bd">
            <div class="weui_media_box weui_media_small_appmsg">
                <div class="weui_cells weui_cells_access">
                    <a class="weui_cell prop_uc_icon03" href="<?php echo $this->createUrl('Ucenter/EditFamily', array('encrypt_id' => $encrypt_id));?>">
                        <div class="weui_cell_hd"><img src=""></div>
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>家庭成员</p>
                        </div>
                        <span class="weui_cell_ft"></span>
                    </a>
                    <a class="weui_cell prop_uc_icon04" href="<?php echo $this->createUrl('Ucenter/WaterFeeList', array('encrypt_id' => $encrypt_id));?>">
                        <div class="weui_cell_hd"><img src=""></div>
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>历史账单</p>
                        </div>
                        <span class="weui_cell_ft"></span>
                    </a>
                    <a class="weui_cell prop_uc_icon05" href="<?php echo $this->createUrl('Repair/RepairRecord', array('encrypt_id' => $encrypt_id));?>">
                        <div class="weui_cell_hd"><img src=""></div>
                        <div class="weui_cell_bd weui_cell_primary">
                            <p>报修记录</p>
                        </div>
                        <span class="weui_cell_ft"></span>
                    </a>
                </div>
            </div>

        </div>
    </div>
    <!--底部固定菜单-->
    <div class="weui_tabbar_padding"></div>
    <div class="weui_tabbar prop_weui_tabbar">
        <a href="<?php echo $this->createUrl('Ucenter/Index', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item ">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">首页</p>
        </a>
        <a href="<?php echo $this->createUrl('Repair/Repair', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">小区报修</p>
        </a>
        <a href="<?php echo $this->createUrl('Ucenter/Center', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item weui_bar_item_on">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">个人中心</p>
        </a>
    </div>

</div>
