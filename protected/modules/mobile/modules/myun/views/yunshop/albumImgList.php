<head>
    <title>商户相册</title>
</head>
<body class="album">
<header class="top_title">
    <nav class="header-nav">
        <ul>
            <?php foreach ($album as $k => $v) { ?>
                <li <?php if ($album_name == $v->name) { ?>class="bg"<?php } ?>>
                    <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/Album', array('album_id' => $v->id, 'name' => $v->name, 'encrypt_id' => $encrypt_id)) ?>"
                       <?php if ($album_name == $v->name) { ?>class="cur"<?php } ?>><?php echo $v->name ?></a>
                </li>
            <?php } ?>
            <li <?php if ($album_name == 'all'){ ?>class="bg"<?php } ?>>
                <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/Album', array('album_id' => 'all', 'name' => 'all', 'encrypt_id' => $encrypt_id)) ?>"
                   <?php if ($album_name == 'all'){ ?>class="cur"<?php } ?>>全部</a>
            </li>
        </ul>
    </nav>
</header>
<article class="album-con">
    <?php foreach ($imglist as $x => $y) { ?>
        <section class="mid_con <?php if ($x % 2 == 1) { ?>no<?php } ?>">
            <div class="img">
                <a href="<?php echo IMG_GJ_LIST . $y->img ?>" title="Lion Rock">
                    <img src="<?php echo IMG_GJ_LIST . $y->img ?>">
                </a>
                <?php $name = explode('.', $y->name); ?>
                <div class="text"><?php echo $name['0'] ?></div>
            </div>
        </section>
    <?php } ?>
</article>
<script>
    $(".album-con a").touchTouch();
</script>
</body>