<head>
	<title>我的会员卡</title>
</head>


<body>
    <div class="cardWrap">
    	<div class="cardHD">
    	<?php $images = explode('/', $data['membercard_img']);
                                      $count = count($images);?>
        	<img src="<?php echo $count == 2 ? IMG_GJ_LIST.$data['membercard_img'] : GJ_STATIC_IMAGES.'card/'.$data['membercard_img']?>">
            <div class="text">
            	<div class="name"><?php echo $data['card_name']?></div>
            	<p><?php echo $data['grade_name']?></p>
            </div>
        </div>
        <div class="cardBarCode">
        	<div class="code"><img src="<?php echo Yii::app() -> createUrl('uCenter/User/CreateBarcode',array('text' => $data['card_no']));?>"></div>
            <div class="num"><?php echo $data['card_no']?></div>
        </div>
        <div class="cardDesc">
        	<div class="title">会员特权 <?php echo number_format($data['discount']*10,1)?>折</div>
            <div class="con">
            	<p><strong>使用说明</strong></p>
            	<?php $arr = explode('#', $data['discount_illustrate'])?>
            	<?php foreach ($arr as $k => $v){?>
            	<p><?php echo $v?></p>
            	<?php }?>
            </div>
        </div>
    </div>
    
</body>

