<script type="text/javascript">
	$(document).ready(main_obj.list_init);
	$(document).ready(statistics_obj.stat_init);
</script>

<div class="kkfm_r_inner">
  <div class="main-right">
               <div class="status-nav">
                    <ul>
                    </ul>
    			</div>
    			 <div class="stored">
         <div class="czjl">
                 	<div class="search">
            <?php echo CHtml::beginForm(Yii::app()->createUrl($this->route),'get');?>
            <span> <?php echo CHtml::textField('Time',isset($_GET['Time']) ? $_GET['Time'] : date('Y/m/d').' - '.date('Y/m/d'),array('class'=>'txt','placeholder'=>'交易时间段','style'=>'color:#999; width:186px;')); ?></span>
        	 <span>支付渠道：<?php echo CHtml::dropDownList('pay_channel', isset($_GET['pay_channel']) ? $_GET['pay_channel'] : '', $channel,array('class'=>'txt','prompt' => '请选择')); ?></span>
        	<span> 订单状态：<?php echo CHtml::dropDownList('order_status', isset($_GET['order_status']) ? $_GET['order_status'] : '', $status,array('class'=>'txt','prompt' => '请选择')); ?></span>
        	<span>操作员：<?php echo CHtml::dropDownList('operator', isset($_GET['operator']) ? $_GET['operator'] : '', $operators,array('class'=>'txt','prompt' => '请选择')); ?></span>
            <br>
        	<span><input type="text" class="txt" name="order_no" style="color:#999; width:186px;" value="<?php echo isset($_GET['order_no'])?$_GET['order_no']:'' ?>" placeholder="请输入订单号" /></span>
        	
        	<span><?php echo CHtml::submitButton('搜索',array('class'=>'btn_com_blue','stytle'=>'height:35px')); ?></span>
        	
        	<span><a href="<?php echo Yii::app()->createUrl('syt/trading/exportExcel',
        			array('Time'=>isset($_GET['Time']) ? $_GET['Time'] : '','pay_channel'=>isset($_GET['pay_channel']) ? $_GET['pay_channel'] : '','order_status'=>isset($_GET['order_status']) ? $_GET['order_status'] : '','order_no'=>isset($_GET['order_no'])?$_GET['order_no']:'','operator'=>isset($_GET['operator']) ? $_GET['operator'] : '')); ?>" class="btn_com_gray" style="height:auto">导出</a></span>
       	<?php echo CHtml::endForm();?>
     </div>
  
   <div class="menuWrap">
        	<ul class="clearfix">
        		<?php $params = $_GET; ?>
            	<li class="<?php if(!empty($_GET['order_status'])){echo '';}else{echo 'cur';} ?>">
            	<a href="<?php unset($params['order_status']);echo Yii::app()->createUrl('syt/trading/tradingList', $params); ?>">全部</a></li>
            	<li class="<?php if(isset($_GET['order_status'])){echo $_GET['order_status']==ORDER_STATUS_UNPAID?'cur':'';} ?>">
            	<a href="<?php $params['order_status']=ORDER_STATUS_UNPAID;echo Yii::app()->createUrl('syt/trading/tradingList',$params); ?>">待付款</a></li>
            	<li class="<?php if(isset($_GET['order_status'])){echo $_GET['order_status']==ORDER_STATUS_PAID?'cur':'';} ?>">
            	<a href="<?php $params['order_status']=ORDER_STATUS_PAID;echo Yii::app()->createUrl('syt/trading/tradingList',$params); ?>">已付款</a></li>
            	<li class="<?php if(isset($_GET['order_status'])){echo ($_GET['order_status']==ORDER_STATUS_EXIT_REFUND || $_GET['order_status']==ORDER_STATUS_REFUND || $_GET['order_status']==ORDER_STATUS_PART_REFUND || $_GET['order_status']==ORDER_STATUS_HANDLE_REFUND)?'cur':'';} ?>">
            	<a href="<?php $params['order_status']=ORDER_STATUS_EXIT_REFUND;echo Yii::app()->createUrl('syt/trading/tradingList',$params); ?>">有退款</a></li>
            </ul>
   </div>
   
    <div class="moneyTotal clearfix">
        	<div class="r">
            	<span>交易金额（<strong class="red"><?php echo isset($arr['successOrderCount'])?$arr['successOrderCount']:'0'; ?></strong>笔）<strong class="red total"><?php echo isset($arr['successOrderMoney'])?number_format($arr['successOrderMoney'],2,'.',''):'0.00'; ?></strong></span>
       			<span>退款金额（<strong class="blue"><?php echo isset($arr['refundRecordCount'])?$arr['refundRecordCount']:'0'; ?></strong>笔）<strong class="blue total"><?php echo isset($arr['refundRecordMoney'])?number_format($arr['refundRecordMoney'],2,'.',''):'0.00'; ?></strong></span>         
            </div>
    </div>

    <div class="recharge">
    	<table width="100%" cellspacing="0" cellpadding="0">
        	
            <tr class="order-title">
                <td align="center" nowrap="nowrap">订单号</td>
                <td align="center" nowrap="nowrap">支付账号</td>
                <td align="center" nowrap="nowrap">订单金额</td>
                <td align="center" nowrap="nowrap">实收金额</td>
                <td align="center" nowrap="nowrap">订单状态</td>
                <td align="center" nowrap="nowrap">支付渠道</td>
                <td align="center" nowrap="nowrap">操作员</td>
                <td align="center" nowrap="nowrap">交易时间</td>
                <td align="center" nowrap="nowrap">操作</td>
            </tr>
            
            <?php if (!empty($list)) {
            	foreach ($list as $k => $v) { ?>
		            <tr <?php echo $k%2==1 ? "class='bg'" : ''?> style="border-bottom:1px solid #dedede">
		                <td align="center" valign="middle"><?php echo $v['order_no'];?></td>
		                <td align="center" valign="middle"><?php echo $v['alipay_account'];?></td>
		                <td align="center" valign="middle"><?php echo $v['order_paymoney']; ?></td>
		                <td align="center" valign="middle"><?php echo sprintf("%.2f", $v['receipt_money']); ?></td>
		                <td align="center" valign="middle"><?php echo isset($v['status'])? $v['status'] : ''; ?></td>
		                <td align="center" valign="middle"><?php echo isset($v['pay_channel'])?$GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']]:'';?></td>
		                <td align="center" valign="middle"><?php echo $v['operator_name'];?></td>
		                <td align="center" valign="middle"><?php echo $v['pay_time'];?></td>
		                <td align="center" valign="middle">
			                <dl class="operate">
<!-- 			                	<dt>操作</dt> -->
			                    <dd>
			                    	<!--  <a href="<?php //echo Yii::app()->createUrl('syt/trading/delTrading', array('id' => $v['id']))?>" 
			                    	onclick="return confirm('删除后不可恢复，继续吗？');">删除</a>-->
			                    	<a href="<?php echo Yii::app()->createUrl('syt/trading/tradingDetails', array('id' => $v['id'])); ?>">详情</a>
			                    	<?php if($v['pay_status'] == ORDER_STATUS_PAID && ($v['order_status'] == ORDER_STATUS_PART_REFUND || $v['order_status'] == ORDER_STATUS_NORMAL || $v['order_status'] == ORDER_STATUS_HANDLE_REFUND)){ ?>
			                    	<a href="<?php echo Yii::app()->createUrl('syt/trading/refund', array('id' => $v['id'])); ?>">退款</a>
			                    	<?php }?>
			                    	<?php if($v['pay_status'] == ORDER_STATUS_UNPAID && $v['order_status'] == ORDER_STATUS_NORMAL) {?>
			                    	<a href="javascript:;" onclick="if(confirm('确定撤销?'))revoke('<?php echo $v['id']?>');">撤销</a>
			                        <?php }?>
			                </dl>
		                </td>
		            </tr>
            	<?php }
            }else { ?>
            	
            <?php }?>
          <!-- 分页开始 -->	
                	<tr style="border:none">
                	<td style="border:none;text-align:right" colspan="9">
                    	<?php $this -> widget('CLinkPager',array(
                    			'pages'=>$pages,
                                'header'=>'共&nbsp;<span class="yellow">'.$pages -> getItemCount().'</span>&nbsp;条&nbsp;',
                                'prevPageLabel' => '上一页',
                                'nextPageLabel'=>'下一页',
                                'maxButtonCount'=>8
                    	));?>
                    	</td>
                   	</tr>  	
          	<!-- 分页结束 -->
               
           </tr>
        </table>
        <?php if (empty($list)) { ?>
            <?php echo '<div align="center"><font color="red" size="3">没有找到相关信息</font></div>';
        } ?>
        <div style="text-align: right">
        </div>
  	</div>
 
</div> 
</div> 
</div> 
</div> 
<script>
	function revoke(id) {
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/trading/revoke'));?>',
            data: {id: id},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.error == 'success') {
                    alert('撤销成功');
                    location.reload();
                }else {
                    alert(data.errMsg);
                }
            }
        });
	}
</script>
