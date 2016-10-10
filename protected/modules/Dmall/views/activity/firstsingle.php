
<body>
<!-- content ↓ -->
<div class="downTFS">
    <header><img src="<?php echo IMG_GJ_LIST.$img;?>"> </header>
    <section class="dtfs_m">
        <div class="note"><span class="icon"> </span><?php echo $name;?></div>
        <a href="<?php echo $jump_url;?>"><input type="button" class="btn_red" value="去商城"></a>

        <dl class="desc">
            <dt>优惠说明：</dt>
            <dd>
                <p>1. 活动日期：从<?php echo date('Y',$start_time);?>年<?php echo date('m',$start_time);?>月<?php echo date('d',$start_time);?>日至<?php echo date('Y',$end_time);?>年<?php echo date('m',$end_time);?>月<?php echo date('d',$end_time);?>日；</p>
                <p>2. 首次在商城下单的用户享受优惠。</p>
            </dd>
        </dl>
    </section>
</div>
<!-- content ↑ -->
</body>
