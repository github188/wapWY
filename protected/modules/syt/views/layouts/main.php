<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>收银台</title>

<link type="text/css" rel="stylesheet" href="<?php echo SYT_STATIC_STYLES?>master.css" />
<link type="text/css" rel="stylesheet" href="<?php echo SYT_STATIC_STYLES?>common.css" />
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>common.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>main.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>payfor.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>popwin.js"></script>

<script language="JavaScript" type="text/javascript" src="<?php echo SYT_STATIC_JS?>My97DatePicker/WdatePicker.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo SYT_STATIC_JS?>plus/bootstrap-datepicker/bootstrap-combined.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo SYT_STATIC_JS?>plus/bootstrap-datepicker/datepicker.css"/>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS ?>plus/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS ?>plus/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS ?>plus/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS ?>plus/daterangepicker/daterangepicker.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo SYT_STATIC_JS ?>plus/daterangepicker/daterangepicker.css"/>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>statistics.js"></script>

<!-- bootstrap -->
<link rel="stylesheet" type="text/css" href="<?php echo SYT_STATIC_JS?>bootstrap/css/bootstrap.css"/>
<!-- end -->

<!-- bootstrap switch -->
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>bootstrapswitch/docs/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>bootstrapswitch/docs/js/highlight.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>bootstrapswitch/js/bootstrap-switch.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>bootstrapswitch/docs/js/main.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo SYT_STATIC_JS?>bootstrapswitch/docs/css/highlight.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo SYT_STATIC_JS?>bootstrapswitch/css/bootstrap3/bootstrap-switch.css"/>

<!-- end -->
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>LodopFuncs.js"></script>
</head>

<?php if (Yii::app()->getController()->getId() == 'index') { ?>
	<body>
		<div class="pay">
		    <div class="top">
		        <div class="img"><a href="<?php echo Yii::app() -> createUrl('syt/Index/Index')?>"><img src="<?php echo SYT_STATIC_IMAGES?>logo.png"></a></div>
		        <div class="top_left"><?php echo Yii::app() -> session['merchant_name']?><?php if(Yii::app() -> session['merchant_name'] != "宁波雅戈尔服饰有限公司中心专卖店"){?>| <?php echo Yii::app() -> session['store_name']?><?php }?></div>
		      <div class="top_right">您好，<?php echo Yii::app()->session['operator_name']?> | <a href="<?php echo SYT_STATIC_JS?>url.php">管家快捷方式</a> | <a href="<?php echo Yii::app()->createUrl('syt/auth/logout')?>">安全退出</a></div>
			</div>
			<?php echo $content;?>
			<div class="footer">
				<p>Copyright@2015玩券版权所有 浙ICP备15022147号-1</p>
			</div> 
		</div>
	</body>
<?php }else {
	echo $content;
} ?>

</html>