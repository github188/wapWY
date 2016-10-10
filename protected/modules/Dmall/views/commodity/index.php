<head>
    <title>商城首页</title>
</head>
<body>
<div class="mask"></div>
<header id="header">
    <ul>
        <li class="li0"><a href="javascript:;" onclick="assortment()"></a></li>
        <li class="li1">
            <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/MemberCenter', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
        <!--	<li class="li2"><a href="<?php //echo Yii::app() -> createUrl('Dmall/Cart/CartList');?>"></a></li> -->
    </ul>
</header>
<div class="nothing"></div>
<div class="homePage">

    <?php foreach ($banner_img as $k => $v) { ?>
        <?php if ($v->name == 'shop_search') {//搜索框?>
            <div class="search">
                <form><input type="search" id="enter" placeholder="搜索" value=""></form>
            </div>
        <?php } elseif ($v->name == 'img_adv') {//图片广告?>
            <!-- 广告图-->
            <div class="picAD">
                <?php if ($v->href[0] != 'null') { ?>
                <?php $arr = explode(';', $v->href[0]); ?>
                <a href="<?php if ($arr[2] == 1) {
                    echo Yii::app()->createUrl('Dmall/Commodity/CommodityList', array('groupid' => $arr[0], 'encrypt_id' => $encrypt_id));
                } elseif ($arr[2] == 2) {
                    echo Yii::app()->createUrl('Dmall/Commodity/commodityDetails', array('id' => $arr[0], 'encrypt_id' => $encrypt_id));
                } ?>">
                    <?php } else { ?>
                    <a><?php } ?>
                        <img src="<?php echo IMG_GJ_600_LIST . $v->url[0]; ?>">
                    </a>
            </div>

        <?php } elseif ($v->name == 'shop_carousel') {//轮播图?>

            <!-- 轮播图开始 -->
            <div class="view-big clearfix" id="focus">
                <div class="bigPic clearfix">
                    <ul>
                        <?php foreach ($v->url as $x => $y) { ?>
                            <?php if ($y != 'null') { ?>
                                <li>
                                    <a href="<?php if ($v->href[$x] != 'null') {
                                        $arr = explode(';', $v->href[$x]); ?>
        						        <?php if ($arr[2] == 1) {
                                            echo empty($arr[0]) ? '' : Yii::app()->createUrl('Dmall/Commodity/CommodityList', array('groupid' => $arr[0], 'encrypt_id' => $encrypt_id));
                                        } elseif ($arr[2] == 2) {
                                            echo empty($arr[0]) ? '' : Yii::app()->createUrl('Dmall/Commodity/commodityDetails', array('id' => $arr[0], 'encrypt_id' => $encrypt_id));
                                        } ?>
            					    <?php } ?>">
                                        <img src="<?php echo IMG_GJ_600_LIST . $y; ?>">
                                    </a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
                <div class="thumb">
                    <ul></ul>
                </div>
            </div>
            <!-- 轮播图结束 -->

        <?php } elseif ($v->name == 'img_nav') {//图片导航?>
            <?php $count = 0;
            foreach ($v->url as $x => $y) {
                if ($y != 'null') {
                    $count++;
                }
            } ?>
            <?php if ($count > 0) { ?>
                <section class="picAD">
                    <article class="img-ul<?php echo $count ?> clearfix">
                        <?php foreach ($v->url as $x => $y) { ?>
                            <?php if ($y != 'null') { ?>
                                <div class="img">
                                    <a href="<?php if ($v->href[$x] != 'null') {
                                        $arr = explode(';', $v->href[$x]); ?>
        			                    <?php if ($arr[2] == 1) {
                                            echo Yii::app()->createUrl('Dmall/Commodity/CommodityList', array('groupid' => $arr[0], 'encrypt_id' => $encrypt_id));
                                        } elseif ($arr[2] == 2) {
                                            echo Yii::app()->createUrl('Dmall/Commodity/commodityDetails', array('id' => $arr[0], 'encrypt_id' => $encrypt_id));
                                        } ?>
        			                <?php } ?>">
                                        <img src="<?php if ($count == 1) {
                                            echo IMG_GJ_600_LIST . $y;
                                        } elseif ($count == 2) {
                                            echo IMG_GJ_300_LIST . $y;
                                        } elseif ($count == 3) {
                                            echo IMG_GJ_250_LIST . $y;
                                        } elseif ($count == 4) {
                                            echo IMG_GJ_150_LIST . $y;
                                        } ?>"/>
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </article>
                </section>
            <?php } ?>
        <?php } elseif ($v->name == 'qcord') {//关注展示?>
            <!-- 关注模块 -->
            <section class="follow">
                <div class="followBg">
                    <img src="<?php echo IMG_GJ_LIST . $v->url[0]; ?>">
                </div>
                <div class="followBox clearfix">
                    <div class="wechat">
                        <img src="<?php echo IMG_GJ_LIST . $v->url[1]; ?>">
                        <p><?php echo $v->url[2] ?></p>
                    </div>
                    <div class="phone">
                        <a href="tel:<?php echo $v->url[3] ?>" class="phoneNum">
                            <img src="<?php echo USER_STATIC_IMAGES ?>phone.png"></a>
                        <p><?php echo $v->url[3] ?></p>
                    </div>
                </div>
            </section>

            <section id="wechatPop">
                <span class="close_btn"></span>
                <div class="popInner">
                    <img src="<?php echo IMG_GJ_LIST . $v->url[1]; ?>">
                    <p>长按保存图片<br>或在微信中搜索<?php echo $v->url[2] ?></p>
                </div>
            </section>
        <?php } elseif ($v->name == 'catalog') {//二级目录?>
            <section class="newsList">
                <ul>
                    <?php foreach ($v->url as $x => $y) { ?>
                        <?php if ($x == 0) { ?>
                            <li class="liTitle"><span><?php echo $y[0] ?></span>
                                <span class="rightArrow downArrow"></span></li>
                        <?php } else { ?>
                            <li>
                                <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/ShowContent', array('content' => $y[1], 'encrypt_id' => $encrypt_id)); ?>"><?php echo $y[0] ?>
                                    <span class="rightArrow"></span>
                                </a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </section>
        <?php } elseif ($v->name == 'shop_group') {//商品分组?>
            <?php foreach ($v->href as $x => $y) { ?>
                <?php $arr = explode(';', $y); ?>
                <?php if ($y != 'null' && !empty($groups[$arr[0]]['product'])) { ?>
                    <article class="title">
                        <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/CommodityList', array('groupid' => $arr[0], 'encrypt_id' => $encrypt_id)); ?>">
                            <span class="rArrow"></span><?php echo $arr[1] ?>
                        </a>
                    </article>
                    <?php $count = 0;
                    foreach ($groups[$arr[0]]['product'] as $n => $m) { ?>

                        <article class="item clearfix">
                            <div class="img">
                                <a href="<?php echo Yii::app()->createUrl('Dmall/commodity/commodityDetails', array('id' => $m['id'], 'encrypt_id' => $encrypt_id)) ?>">
                                    <img src="<?php $img_arr = explode(';', $m['img']);
                                    echo IMG_GJ_600_LIST . $img_arr[0] ?>">
                                </a>
                            </div>
                            <div class="text">
                                <a href="<?php echo Yii::app()->createUrl('Dmall/commodity/commodityDetails', array('id' => $m['id'], 'encrypt_id' => $encrypt_id)) ?>">
                                    <span class="name"><?php echo $m['name'] ?></span>
                                    <span class="price">¥<?php echo $m['price'] ?><!--<s>¥100.00</s>  --></span>
                                </a>
                            </div>
                        </article>
                        <?php $count++;
                        if ($count == 4) {
                            break;
                        } ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <article class="bottom" style="display: none">
        <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
    </article>
    <div class="footerH"></div>
    <div id="footer">
        <ul>
            <li class="li01">
                <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/Index', array('encrypt_id' => $encrypt_id)); ?>"></a>
            </li>
            <li class="li02">
                <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/CommodityList', array('encrypt_id' => $encrypt_id)); ?>"></a>
            </li>
            <!--        <li class="li04"><a href="<?php //echo Yii::app() -> createUrl('Dmall/Cart/CartList');?>"></a></li> -->
            <li class="li05">
                <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/MemberCenter', array('encrypt_id' => $encrypt_id)); ?>"></a>
            </li>
        </ul>
    </div><!--footer end-->
</div>
<!--商品列表弹出框-->
<?php if (!empty($groups)) { ?>
    <div id="popShadow" style="display: none"></div>
    <div class="pop assortment" style="display: none" id="assortment">
        <div class="pop_con">
            <dl>
                <?php foreach ($groups as $key => $v) { ?>
                    <dt>
                        <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/commodityList', array('typename' => 'limit_num', 'groupid' => $key, 'encrypt_id' => $encrypt_id)) ?>"><?php echo $v['group_name']; ?></a>
                        <span class="jt"></span>
                    </dt>
                <?php } ?>
            </dl>
        </div>
    </div>
<?php } ?>

</body>
<script type="text/javascript">
    $("input").focus(function () {
        $("input").addClass("searchClick");
    });
    $("input").blur(function () {
        $("input").removeClass("searchClick")
    });
    $(".li2").click(function () {
        $(".top").addClass("topClick")
    });

    TouchSlide({
        slideCell: "#focus",
        titCell: ".thumb ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell: ".bigPic ul",
        effect: "left",
        autoPlay: true,//自动播放
        autoPage: true, //自动分页
        switchLoad: "_src" //切换加载，真实图片路径为"_src"
    });

    //按enter搜索
    $(document).on('keydown', '#enter', function (e) {
        var value = $('#enter').val();
        var encrypt_id = '<?php echo $encrypt_id?>';
        var url = "<?php echo Yii::app()->createUrl('Dmall/commodity/commodityList')?>?typename=limit_num&type=up&placeholder=" + value + "&shopgroup=&title=&encrypt_id=" + encrypt_id;
        if (e.keyCode == 13) {
            window.location.href = url;
            return false;
        }
    });

    function assortment() {
        if ($("#popShadow").is(":hidden") && $('#assortment').is(":hidden")) {
            $('#popShadow').show();
            $('#assortment').show();
        } else {
            $('#popShadow').hide();
            $('#assortment').hide();
        }
    }

    //点击阴影部分隐藏商品对话框
    $('#popShadow').click(function (e) {
        $('#popwindow').slideToggle();
        $('#popShadow').attr('style', 'display:none');
        $('#assortment').attr('style', 'display:none');
        $('.filedone').remove();
        e.stopPropagation();
    });

    /***************************微信分享******************************************/
    //微信js配置
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: '<?php echo $signPackage["timestamp"];?>',
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            "checkJsApi",
            "onMenuShareTimeline",
            "onMenuShareAppMessage",
            "onMenuShareQQ",
        ]
    });

    wx.ready(function () {
        //
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ],
            success: function (res) {
//                 alert(JSON.stringify(res));
            }
        });

        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: '【东钱湖旅游】商城首页', // 分享标题
            link: '<?php echo WAP_DOMAIN . '/Dmall/Commodity/index?encrypt_id=' . $encrypt_id;?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop->logo_img)) {
                echo IMG_GJ_LIST . $onlineshop->logo_img;
            }?>', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享给朋友
        wx.onMenuShareAppMessage({
            title: '【东钱湖旅游】商城首页', // 分享标题
            desc: '东钱湖在线购物商城，在线购买电子票，预约景区', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/Dmall/Commodity/index?encrypt_id=' . $encrypt_id;?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop->logo_img)) {
                echo IMG_GJ_LIST . $onlineshop->logo_img;
            }?>', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享到QQ
        wx.onMenuShareQQ({
            title: '【东钱湖旅游】商城首页', // 分享标题
            desc: '东钱湖在线购物商城，在线购买电子票，预约景区', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/Dmall/Commodity/index?encrypt_id=' . $encrypt_id;?>', // 分享链接
            imgUrl: '<?php if (!empty($onlineshop->logo_img)) {
                echo IMG_GJ_LIST . $onlineshop->logo_img;
            }?>', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });


</script>