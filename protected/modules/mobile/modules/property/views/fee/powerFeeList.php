<div class="prop_wrap prop_powerRate">
    <?php if(!empty($list)){?>
    <?php foreach($list as $k => $v){?>
        <div class="weui_panel">
            <div class="weui_panel_bd">
                <div class="weui_media_box weui_media_small_appmsg">
                    <div class="weui_cells weui_cells_access">
                        <a class="weui_cell prop_powerrate_selected" href="javascript:;">
                            <div class="weui_cell_hd"><div class="icon_label_detail"></div> </div>
                            <div class="weui_cell_bd weui_cell_primary">
                                <p><?php echo $k;?>年电费账单</p>
                            </div>
                            <span class="weui_cell_ft"></span>
                        </a>
                        <div class="a-detail">
                            <table cellpadding="0" cellspacing="0" class="table_h" width="100%">
                                <thead>
                                <tr>
                                    <th>月份</th>
                                    <th>用电量</th>
                                    <th>电费</th>
                                    <th>状态</th>
                                </tr>
                                </thead>
                                <?php foreach($v as $data){?>
                                    <tbody>
                                    <tr>
                                        <td><?php echo $data['month'];?></td>
                                        <td><?php echo $data['electricity'];?></td>
                                        <td>￥<?php echo $data['order_money'];?></td>
                                        <td>
                                            <?php if($data['pay_status'] == 1) {?>
                                                <a href="<?php echo $this->createUrl('Fee/PowerFeeDetails', array('id' => $data['id'], 'encrypt_id' => $encrypt_id));?>">
                                                    <div style="color: red">
                                                        <?php echo $GLOBALS['ORDER_STATUS_PAY'][$data['pay_status']];?>
                                                    </div>
                                                </a>
                                            <?php }else{ ?>
                                            <a href="<?php echo $this->createUrl('Fee/PowerFeeView', array('id' => $data['id'], 'encrypt_id' => $encrypt_id));?>">
                                                <?php echo $GLOBALS['ORDER_STATUS_PAY'][$data['pay_status']];?>
                                                <?php }?>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }?>
    <?php }else{ ?>
        <!--没有内容的情况下的提示-->
        <div class="weui_cells_title ac cGray">没有相关内容</div>
    <?php }?>
</div>
