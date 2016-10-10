<title>首页</title>
<div class="prop_wrap">
    <div class="prop_index_wrap">
        <div class="weui_cells_title">
            <span class="icon_label_house"></span>
            小区名称：<span class="cGreen"><?php echo $proprietorInfo['community_name'];?></span>
        </div>
        <div class="weui_grids">
            <a href="<?php echo $this->createUrl('Fee/WaterFeeList', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_grid js_grid" data-id="button">
                <div class="weui_grid_icon">
                    <img src="<?php echo WY_STATIC_IMAGES?>property/icon1.png" alt="">
                </div>
                <p class="weui_grid_label">
                    水费
                </p>
            </a>
            <a href="<?php echo $this->createUrl('Fee/PowerFeeList', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_grid js_grid" data-id="cell">
                <div class="weui_grid_icon">
                    <img src="<?php echo WY_STATIC_IMAGES?>property/icon2.png" alt="">
                </div>
                <p class="weui_grid_label">
                    电费
                </p>
            </a>
            <a href="<?php echo $this->createUrl('Fee/chooseCars', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_grid js_grid" data-id="toast">
                <div class="weui_grid_icon">
                    <img src="<?php echo WY_STATIC_IMAGES?>property/icon3.png" alt="">
                </div>
                <p class="weui_grid_label">
                    停车费
                </p>
            </a>
            <a href="<?php echo $this->createUrl('Fee/PropertyFeeList', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_grid js_grid" data-id="toast">
                <div class="weui_grid_icon">
                    <img src="<?php echo WY_STATIC_IMAGES?>property/icon4.png" alt="">
                </div>
                <p class="weui_grid_label">
                    物业费
                </p>
            </a>
        </div>
    </div>
    <!--底部固定菜单-->
    <div class="weui_tabbar_padding"></div>
    <div class="weui_tabbar prop_weui_tabbar">
        <a href="<?php echo $this->createUrl('Ucenter/Index', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item weui_bar_item_on">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">首页</p>
        </a>
        <a href="<?php echo $this->createUrl('Repair/Repair', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">小区报修</p>
        </a>
        <a href="<?php echo $this->createUrl('Ucenter/Center', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">个人中心</p>
        </a>
    </div>
</div>