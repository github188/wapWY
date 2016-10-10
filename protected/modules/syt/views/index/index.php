
<div class="pay-main">
	<div class="nav">
		<ul>
			<?php 
				$url = Yii::app()->session['lastRequestUrl'];
				$id = 'cashier';
				$arr = explode("/", $url);
				$arr = array_reverse($arr);
				if (isset($arr[1])) {
					$id = strtolower($arr[1]);
				}
			?>
			
			<li class="icon<?php echo $id == 'cashier' ? '0' : ''; ?>"><a href="javascript:;" onclick="onLeft('<?php echo Yii::app()->createUrl('syt/cashier/pay') ?>')">收款</a></li>
			<li class="icon1<?php echo $id == 'trading' ? '0' : ''; ?>"><a href="javascript:;" onclick="onLeft('<?php echo Yii::app()->createUrl('syt/trading/tradingList') ?>')">交易明细</a></li>
			<li class="icon2<?php echo $id == 'memberstored' ? '0' : ''; ?>"><a href="javascript:;" onclick="onLeft('<?php echo Yii::app()->createUrl('syt/MemberStored/charge') ?>')">会员储值</a></li>
			<li class="icon3<?php echo $id == 'record' ? '0' : ''; ?>"><a href="javascript:;" onclick="onLeft('<?php echo Yii::app()->createUrl('syt/Record/bookRecord') ?>')">预订管理</a></li>
			<li class="icon4<?php echo $id == 'system' ? '0' : ''; ?>"><a href="javascript:;" onclick="onLeft('<?php echo Yii::app()->createUrl('syt/system/editPwd');?>')">系统设置</a></li>
		</ul>
	</div>
	<div class="kkfmain_r">
		<iframe src="<?php echo empty(Yii::app()->session['lastRequestUrl']) ? Yii::app()->createUrl('syt/cashier/pay') : Yii::app()->session['lastRequestUrl'];?>" style="border:none; width:100%;" scrolling="no" id="main" onload="autoResize('main',520)" frameborder="0"></iframe>
	</div>
</div>

<script>
	//左侧导航按钮点击高亮
	$(".pay-main .nav ul li").click(function() {
		var cur = $(this);
		$.each($(".pay-main .nav ul li"), function() {
			var cls = $(this).attr('class');
			if(cls.indexOf('0') >= 0) {
				$(this).attr('class', cls.substring(0, cls.length - 1));
			}
		});
		cur.attr('class', cur.attr('class') + '0');
	});
</script>