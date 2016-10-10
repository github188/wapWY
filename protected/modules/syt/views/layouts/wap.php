<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0" />
<title><?php echo Yii::app()->session['store_name']; ?></title>

<link type="text/css" rel="stylesheet" href="<?php echo SYT_STATIC_STYLES?>master.css" />
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="<?php echo SYT_STATIC_JS?>main.js"></script>

</head>

<body>
	<?php echo $content;?>
</body>

</html>