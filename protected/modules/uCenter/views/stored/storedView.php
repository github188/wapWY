<body>
	<div class="storedDesc">
    	<header>商家<?php echo $stored -> shopname?>的储值活动</header>	 
        <div class="content">
        	<div class="title">充<?php echo $stored -> stored_money?>元送<?php echo $stored -> get_money?>元</div>
            <img src="<?php echo USER_STATIC_IMAGES?>icon8.png">
        </div> 
        <footer>
        	<p><a href="<?php echo Yii::app()->createUrl('uCenter/user/list', array('title'=>'stored', 'encrypt_id' => $encrypt_id, 'model'=>$stored_info))?>">更多储值活动更多优惠</a></p>
            <a href="<?php echo Yii::app()->createUrl('uCenter/stored/stored', array('pay_key'=>$stored -> id, 'encrypt_id' => $encrypt_id))?>" class="btn_red">立即储值</a>
        </footer>     
    </div>
    
</body>