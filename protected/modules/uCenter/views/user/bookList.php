<head>
    <title>我的预订</title>
</head>

<body class="book">
<section class="mid_con">
    <?php if (!empty($book)) { ?>
        <?php foreach ($book as $key => $value) { ?>
            <div class="book-list">
                <a href="<?php echo Yii::app()->createUrl('uCenter/user/bookDetail', array('record_id' => $value['id'], 'encrypt_id' => $encrypt_id)) ?>">
                    <div class="left">
                        <span class="name"><?php echo $value['name'] ?></span>
                        <span class="time"><?php echo $value['want_come_time'] ?></span>
                    </div>
                    <div class="right">
                        <span>
                            <em class="state"><?php echo $GLOBALS['BOOK_RECORD_STATUS'][$value['status']] ?></em></span>
                        <span class="time"><?php echo $value['book_num'] ?>人</span>
                    </div>
                </a>
            </div>
        <?php } ?>
    <?php } ?>
</section>
</body>
