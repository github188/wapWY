<div class="prop_wrap prop_powerRate">
        <div class="weui_panel">
            <div class="weui_panel_bd">
                <div class="weui_media_box weui_media_small_appmsg">
                    <div class="weui_cells weui_cells_access">
                        <a class="weui_cell prop_powerrate_selected" href="javascript:;">
                            <div class="weui_cell_hd"><div class="icon_label_detail"></div> </div>
                            <div class="weui_cell_bd weui_cell_primary">
                                <p>物业费账单</p>
                            </div>
                            <span class="weui_cell_ft"></span>
                        </a>
                        <div class="a-detail">
                            <table cellpadding="0" cellspacing="0" class="table_h" width="100%">
                                <thead>
                                <tr>
                                    <th>年份</th>
                                    <th>房屋面积</th>
                                    <th>本年物业费</th>
                                    <th>状态</th>
                                </tr>
                                </thead>
                                <?php foreach($list as $k => $v){?>
                                <tbody>
                                <?php foreach ($v as $data) { ?>
                                    <tr>
                                        <td><?php echo $data['year'];?></td>
                                        <td><?php echo $data['floor_space'] . '平方';?></td>
                                        <td>￥<?php echo $data['order_money'];?></td>
                                        <td>
                                            <?php if($data['pay_status'] == ORDER_STATUS_UNPAID) {?>
                                                <a href="<?php echo $this->createUrl('Fee/ToPropertyFee', array('id' => $data['id'], 'encrypt_id' => $encrypt_id));?>">
                                                    <div style="color: red">
                                                        <?php echo $GLOBALS['ORDER_STATUS_PAY'][$data['pay_status']];?>
                                                    </div>
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo $this->createUrl('Fee/PropertyFeeView', array('id' => $data['id'], 'encrypt_id' => $encrypt_id));?>">
                                                    <?php echo $GLOBALS['ORDER_STATUS_PAY'][$data['pay_status']];?>
                                                </a>
                                            <?php }?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <?php }?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
