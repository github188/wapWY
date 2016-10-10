<head>
    <title>商户相册</title>
</head>

<body class="album">
<header class="top_title">
    <nav class="header-nav">
        <ul>
            <?php foreach ($album as $k => $v) { ?>
                <li <?php if (isset($_GET['name']) && $_GET['name'] == $v->name){ ?>class="bg"
                    <?php }elseif (!isset($_GET['name']) && $k == 0){ ?>class="bg"<?php } ?>>
                    <a href="<?php echo Yii::app()->createUrl('uCenter/user/Album', array('album_id' => $v->id, 'name' => $v->name, 'encrypt_id' => $encrypt_id)) ?>"
                       <?php if (isset($_GET['name']) && $_GET['name'] == $v->name){ ?>class="cur"<?php } elseif (!isset($_GET['name']) && $k == 0) { ?> class="cur"<?php } ?>><?php echo $v->name ?></a>
                </li>
            <?php } ?>
            <li <?php if (isset($_GET['album_id']) && $_GET['album_id'] == 'all'){ ?>class="bg"<?php } ?>>
                <a href="<?php echo Yii::app()->createUrl('uCenter/user/Album', array('album_id' => 'all', 'name' => 'all', 'encrypt_id' => $encrypt_id)) ?>"
                   <?php if (isset($_GET['album_id']) && $_GET['album_id'] == 'all'){ ?>class="cur"<?php } ?>>全部</a>
            </li>
        </ul>
    </nav>
</header>
<article class="album-con">
    <?php foreach ($album_group as $x => $y) { ?>
        <?php if ($y->num > 0) { ?>

            <section class="mid_con <?php if ($x % 2 == 1) { ?>no<?php } ?>">
                <a href="<?php echo Yii::app()->createUrl('uCenter/user/photolist', array('album_group_id' => $y->id, 'album_name' => $_GET['name'], 'encrypt_id' => $encrypt_id)) ?>">
                    <div class="img"><img src="<?php echo IMG_GJ_LIST . $y->img ?>">
                        <div class="num"><?php echo $y->num ?></div>
                        <div class="text"><?php echo $y->name ?></div>
                    </div>
                </a>
            </section>

        <?php } ?>
    <?php } ?>
</article>

</body>