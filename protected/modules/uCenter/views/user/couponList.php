<body>
	<section class="couponsLst">
		<?php if (!empty($list)) { ?>
			<?php foreach ($list as $key => $value) { ?>
				<?php if (strtotime($now) <= strtotime($value['end_time'])) { ?>
					<article class="listWrap">
				        <div class="top Color010">
				            <div class="img"><img src="<?php echo IMG_GJ_LIST.$value['logo_img']?>"></div>
				            <div class="name">
				                <span><?php echo $value['name']?></span>
				                <span class="fz"><?php echo $value['title']?></span>
				            </div>
				        </div>
				        <div class="end"> 有效期：<?php echo date("Y-m-d", strtotime($value['start_time']))?>-<?php echo date("Y-m-d", strtotime($value['end_time']))?></div>
				    </article>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		
		<?php if (!empty($list)) { ?>
			<?php foreach ($list as $key => $value) { ?>
				<?php if (strtotime($now) > strtotime($value['end_time'])) { ?>
					<article class="listWrap">
				        <div class="top Color010">
				            <div class="img"><img src="<?php echo IMG_GJ_LIST.$value['logo_img']?>"></div>
				            <div class="name">
				                <span><?php echo $value['name']?></span>
				                <span class="fz"><?php echo $value['title']?></span>
				            </div>
				        </div>
				        <div class="end"> 有效期：<?php echo date("Y-m-d", strtotime($value['start_time']))?>-<?php echo date("Y-m-d", strtotime($value['end_time']))?></div>
        				<div class="over"></div>
				    </article>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		
		
	
		
	</section>
</body>
