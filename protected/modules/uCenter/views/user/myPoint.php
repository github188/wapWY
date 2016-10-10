 <head>
	<title>我的积分</title>
</head>

<body class="stored">
    	<header class="top_title">
        	<div class="input">
            	<div class="value">我的积分<em class="red fw"><?php echo isset($point['points']) ? $point['points'] : 0 ?></em></div>
            </div>
        </header>
        <section class="mid_con">
        	<?php if (!empty($stored)) { ?>
	        	<div class="name">积分记录</div>
	        	<?php foreach ($point as $key => $value) { ?>
	        		<div class="input">
		            	<div class="value">
		                	<span><?php echo $value['create_time']?></span>
		                	<span><?php echo $GLOBALS['POINT_PAYMENT'][$value['balance_of_payments']].':'.$value['points']?></span>
		                </div>
		            </div>
	        	<?php } ?>
	      	<?php } ?>
        </section>
</body>
</html>
