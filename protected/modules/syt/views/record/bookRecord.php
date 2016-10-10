<script type="text/javascript">
	$(document).ready(main_obj.list_init);
	$(document).ready(statistics_obj.stat_init);
</script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>artDialog/jquery.artDialog.js?skin=simple"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>artDialog/plugins/iframeTools.js"></script>
<div class="kkfm_r_inner">
    <div class="main-right">
            <div class="status-nav">
                <ul>
                    <li class="cur"><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord')?>">预定信息</a></li>
                    <!--li class=""><a href="#">桌位信息</a></li-->
                </ul>
            </div>
             <div class="stored">              
                 <div class="czjl">
                    <?php $form=$this->beginWidget('CActiveForm',array('method'=>'get'))?>
                    <div class="search clearfix">
                    	<span>
                        <?php echo CHtml::textField('Time',isset($_GET['Time']) ? $_GET['Time'] : '',array('class'=>'txt','placeholder'=>'预订时间段','style'=>'color:#999; width:186px;')); ?>	
                        </span>
                        <span><input type="text" name="phone" class="txt" placeholder="预约号码"></span>
                        <span><input type="submit" class="btn_com_blue" value="搜索" style="height:35px"></span>  
                        <span style="float:right"><a  href="javascript:void(0);" id="outside" class="btn_com_blue" >添加预约</a></span>
                    </div>
                    <?php $form=$this->endWidget()?>
                    <div id='content'>
                    <div class="status-nav">
                        <ul>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord')?>">全部(<?php echo !empty($count) ? $count : 0?>)</a></li>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord',array('status'=>BOOK_RECORD_STATUS_WAIT))?>">待确认(<?php echo !empty($wait) ? $wait : 0?>)</a></li>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord',array('status'=>BOOK_RECORD_STATUS_ACCEPT))?>">已接单(<?php echo !empty($accept) ? $accept : 0?>)</a></li>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord',array('status'=>BOOK_RECORD_STATUS_REFUSE))?>">已拒单(<?php echo !empty($refuse) ? $refuse : 0?>)</a></li>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord',array('status'=>BOOK_RECORD_STATUS_ARRIVE))?>">已到店(<?php echo !empty($arrive) ? $arrive : 0?>)</a></li>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord',array('status'=>BOOK_RECORD_STATUS_CANCEL))?>">已取消(<?php echo !empty($cancel) ? $cancel : 0?>)</a></li>
                            <li class=""><a href="<?php echo Yii::app()->createUrl('syt/Record/bookRecord',array('time'=>'today'))?>">预约今日(<?php echo !empty($today) ? $today : 0?>)</a></li>
                        </ul>
                    </div>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr class="order-title" >
                            <td >下单时间</td>
                            <td>联系人</td>
                            <td>预约号码</td>
                            <td width="30%">需求</td>
                            <td>预约人数</td>                            
                            <td>预约时间</td>
                            <td>状态</td>
                            <td>操作</td>
                        </tr>
                        <?php if(!empty($record)) { ?>
                        <?php foreach ($record as $key => $value) { ?>
                        <tr <?php echo $key%2==1 ? "class='bg'" : ''?> style="border-bottom:1px solid #dedede">
                            <td><?php echo $value['create_time'];?></td>
                            <td><?php                                 
                                $information = explode('@', $value['book_information']);
                                $sex = isset($information[2]) ? $information[2] : SEX_MALE;
                                $sex = $sex == $GLOBALS['__BOOKSEX'][SEX_FEMALE] ? SEX_FEMALE : $sex;
                                $sex = $sex == $GLOBALS['__BOOKSEX'][SEX_MALE] ? SEX_MALE : $sex;
                                $name = isset($information[0]) ? ($information[0]) : '';
                                $name .= $sex == SEX_FEMALE ? $GLOBALS['__BOOKSEX'][SEX_FEMALE] : $GLOBALS['__BOOKSEX'][SEX_MALE];
                                echo $name;
                                ?>
                            </td>
                            <td><?php echo isset($information[1]) ? $information[1] : '';?></td>
                            <td><?php echo $value['remark']?></td>
                            <td><?php echo isset($information[3]) ? $information[3] : '';?></td>                            
                            <td><?php echo $value['book_time']?></td>
                            <td><?php echo $GLOBALS['BOOK_RECORD_STATUS'][$value['status']]?></td>
                            <td>
                                <dl class="operate">  
                                    <?php if($value['status'] != BOOK_RECORD_STATUS_REFUSE) { ?>
                                    
                                        <?php if($value['status'] == BOOK_RECORD_STATUS_WAIT) { ?>
                                            <a href="<?php echo Yii::app()->createUrl('syt/Record/Accept',array('accept'=>BOOK_RECORD_STATUS_ACCEPT,'id'=>$value['id']))?>" onclick="return confirm('确定此操作吗？');">接单</a>
                                        <?php } ?>
                                            
                                        <?php if($value['status'] == BOOK_RECORD_STATUS_WAIT) { ?>
                                            <a href="<?php echo Yii::app()->createUrl('syt/Record/Accept',array('accept'=>BOOK_RECORD_STATUS_REFUSE,'id'=>$value['id']))?>" onclick="return confirm('确定此操作吗？');">拒单</a>
                                        <?php } ?>
                                            
                                        <?php if($value['status'] == BOOK_RECORD_STATUS_ACCEPT) { ?>
                                            <a href="<?php echo Yii::app()->createUrl('syt/Record/Accept',array('accept'=>BOOK_RECORD_STATUS_ARRIVE,'id'=>$value['id']))?>" onclick="return confirm('确定此操作吗？');">到店</a>
                                        <?php } ?>
                                            
                                        <?php if($value['status'] == BOOK_RECORD_STATUS_ACCEPT) { ?>
                                            <a href="<?php echo Yii::app()->createUrl('syt/Record/Accept',array('accept'=>BOOK_RECORD_STATUS_CANCEL,'id'=>$value['id']))?>" onclick="return confirm('确定此操作吗？');">取消</a>
                                        <?php } ?>
                                            
                                    <?php } ?>
                                </dl>
                            </td>
                        </tr> 
                        <?php } ?>
                        <?php } else { ?>
                        <tr style="border-bottom:1px solid #dedede">
                            <td colspan="5" align="center">没有预定记录</td>
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
</div>
<script>
    //局部自动刷新
    setInterval(function() {
        $("#content").load(location.href+" #content>*","");
    }, 8000);
    //弹出窗口
	$('#outside').click(function(){		
            art.dialog.open(
            	'<?php echo Yii::app()->createUrl('syt/Record/addRecord')?>',
            	 {
                  	 title: '添加预定',
                  	 lock: true,
                  	 drag:true, 
                  	 id: 'dialog',
                  	 background: '#fff',
                         //width: 500,
                         //height:500,
	                 resize: false
                 }
            );
	});

    $(function() {
        var status = '<?php echo isset($_GET['status']) ? $_GET['status'] : '0';?>';
        var time = '<?php echo isset($_GET['time']) ? $_GET['time'] : '';?>';
        if(time == 'today') {
            status = 6;
        }
        status = parseInt(status) + 1;
        var obj = $(".status-nav ul li:eq("+status+")");
        obj.siblings().attr("class", "");
        obj.attr("class", "cur");
    }); 
    
</script>