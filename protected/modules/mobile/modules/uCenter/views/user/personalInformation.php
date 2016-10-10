<head>
    <title>我的账户</title>
</head>

<body class="logo">
<section class="item">
    <a class="intergral"
       href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/personalInformationDetail', array('encrypt_id' => $encrypt_id)) ?>">个人信息
        <span class="jt"></span>
        <span class="id">修改</span>
    </a>
</section>
<!-- 	        <section class="item"> -->
<!--	          	<a class="intergral" href="#">收货地址<span class="jt"></span><span class="id">修改</span></a> -->
<!-- 	        </section> -->

<section class="mid_con">
    <div class="item dline">
        <a class="intergral"
           href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/checkOldAccount', array('part_account' => $part_account, 'all_account' => $all_account, 'encrypt_id' => $encrypt_id)) ?>">已绑定手机<?php echo $part_account ?>
           <span class="jt"></span><span class="id">更换</span>
        </a>
    </div>
    <div class="item">
        <a class="intergral"
           href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/changePwd', array('encrypt_id' => $encrypt_id)) ?>">登录密码<span
           class="jt"></span><span class="id">修改</span></a>
    </div>
</section>

<section class="item">
    <a class="intergral"
       href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/setFreeSecret', array('encrypt_id' => $encrypt_id)) ?>">小额免密设置
       <span class="jt"></span><span class="id"><?php echo $free_secret ?></span>
    </a>
</section>

<section>
    <div class="btn">
        <a class="btn_red"
           href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/logout', array('encrypt_id' => $encrypt_id)) ?>">退出登录</a>
    </div>
</section>
</body>