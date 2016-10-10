<?php if (Yii::app()->user->hasFlash('error')) { ?>
	<script>
		alert('<?php echo Yii::app()->user->getFlash('error')?>')
    	location.href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/memberCenter', array('encrypt_id' => $encrypt_id))?>";
	</script>
<?php }?>

<head>
	<title>等待商户审核</title>
</head>
<body>
	<div class="status">等待商户审核</div>
	<section class="set-con">
		<div class="con">
	    	<div class="img"><img src="<?php echo USER_STATIC_IMAGES?>user/wait_03.png"></div>
	    </div>
		<div class="con">
	    	<div class="filed">
	        	<span class="set-hol"><?php echo $book['name']?></span>
	            <span class="time"><?php echo $book['time']?><em><?php echo $book['people_num']?>人</em></span>
	            <span class="set-name no"><?php echo $book['book_name']?><?php echo $book['sex']?><em><?php echo $book['phone_num']?></em></span>
	        </div>
	    </div>
	    <div class="bottom">
	    	<span><a href="<?php echo Yii::app()->createUrl('mobile/book/book/bookDetail', array('record_id'=>$book['id'], 'encrypt_id' => $encrypt_id)) ?>" class="submit">查看订座详情</a></span>
	    </div>
	</section>
</body>
