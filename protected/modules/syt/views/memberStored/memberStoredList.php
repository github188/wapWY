<script type="text/javascript">
	$(document).ready(main_obj.list_init);
	$(document).ready(statistics_obj.stat_init);
</script>
<div class="kkfm_r_inner">
    <div class="main-right">
     	<div class="status-nav">
            <ul>
                <li class=""><a href="<?php echo $this->createUrl('Charge')?>">会员储值</a></li>
                <li class="cur"><a href="<?php echo $this->createUrl('MemberStoredList')?>">储值记录</a></li>
            </ul>
        </div>
    <div class="stored">
      	 <div class="czjl">
            <div class="search">
                <?php echo CHtml::beginForm(Yii::app()->createUrl($this->route),'get')?>
                <span>
                    <?php echo CHtml::textField('Time',isset($_GET['Time']) ? $_GET['Time'] : '',array('class'=>'txt','placeholder'=>'交易时间段','style'=>'color:#999; width:186px;')); ?>
                </span>
                    <span><input type="text" name="account" value="<?php echo isset($_GET['account']) ? $_GET['account'] : ''?>" placeholder="请输入会员账号"></span>
                    <span><?php echo CHtml::submitButton('搜 索',array('class'=>'btn_com_blue','style'=>'height:35px'))?></span>                      
                    <span><input type="submit"  value="导出excel" name="excel" class="btn_com_gray" style="height:35px"/></span>
                <?php echo CHtml::endForm()?>
            </div>
            <table width="100%" cellspacing="0" cellpadding="0">
              <tr class="order-title" >
                <td >会员账号</td>
                <td>储值活动</td>
                <td>数量</td>
                <td>实收金额</td>
                <td>操作员</td>
                <td>交易时间</td>
                <td>支付渠道</td>
                <td>操作</td>
              </tr>              
              <?php if(!empty($storedorderlist)) { ?>
              <?php foreach ($storedorderlist as $key => $value) { ?>
              <tr <?php echo $key%2==1 ? "class='bg'" : ''?> style="border-bottom:1px solid #dedede">
                <td><?php echo $value['account']?></td>
                <td>充<?php echo $value['money']+0?>送<?php echo $value['bonus']+0?></td>
                <td><?php echo $value['num']?></td>
                <td><?php echo $value['money'] * $value['num']?></td>
                <td><?php echo $value['operator']?></td>
                <td><?php echo $value['pay_time']?></td>
                <td><?php echo $GLOBALS['ORDER_PAY_CHANNEL'][$value['pay_channel']]?></td> 
                <td>
                	<?php if ($value['order_status'] == ORDER_STATUS_NORMAL) { ?>
                		<a href="javascript:;" onclick="if(confirm('确认撤销?')){revoke('<?php echo $value['id']?>')}">撤销</a>
                	<?php }else {?>
                		<a>已撤销</a>
                	<?php }?>
                </td>
              </tr>
              <?php  } ?>
              <?php } else { ?>
                <tr style="border-bottom:1px solid #dedede">
                    <td colspan="5" align="center">没有充值记录</td>
                </tr>
              <?php } ?>
              <!-- 分页开始 -->	
                	<tr style="border:none">
                	<td style="border:none;text-align:right" colspan="8">
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
          </table>
        </div>
     </div>
</div>
</div>
<script>
	function revoke(id) {
		$.ajax({
            url: '<?php echo(Yii::app()->createUrl('syt/memberStored/revoke'));?>',
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
