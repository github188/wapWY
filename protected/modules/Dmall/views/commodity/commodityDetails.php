<head>
    <title>商品详情</title>
</head>
<body class="commodity">
<header>
    <?php $img_arr = explode(';', $data['img']); ?>
    <div class="view-big" id="focus">
        <div class="bigPic clearfix">
            <ul>
                <?php foreach ($img_arr as $v) { ?>
                    <li><img src="<?php echo !empty($data['ts_product_id']) ? $v : IMG_GJ_600_LIST . $v; ?>"></li>
                <?php } ?>
            </ul>
        </div>
        <div class="thumb" style="display: none">
            <ul></ul>
        </div>
    </div>
</header>
<script type="text/javascript">
    TouchSlide({
        slideCell: "#focus",
        titCell: ".thumb ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell: ".bigPic ul",
        effect: "left",
        autoPlay: true,//自动播放
        autoPage: true, //自动分页
        switchLoad: "_src" //切换加载，真实图片路径为"_src"
    });
</script>
<section class="commodityHD">
    <article class="item clearfix">
        <div class="text">
            <span class="name"><?php echo $data['name']; ?></span>
            <span class="price">
            <?php if (!empty($minprice)) { ?>
                <?php if ($minprice == $maxprice) { ?>
                    <?php echo '¥ ' . $minprice ?>
                <?php } else { ?>
                    <?php echo '¥ ' . $minprice . '-¥ ' . $maxprice; ?>
                <?php } ?>
                <?php if (!empty($minorgprice) && !empty($maxorgprice)) { ?><?php } ?>
                <?php if (!empty($minorgprice)) { ?>
                    <s>
                        <?php if ($minorgprice == $maxorgprice) { ?>
                            <?php echo '¥ ' . $minorgprice ?>
                        <?php } else { ?>
                            <?php echo '¥ ' . $minorgprice . '-¥ ' . $maxorgprice; ?>
                        <?php } ?>
                    </s>
                <?php } ?>
            <?php } ?>
            </span>
            <span style="color: #6D8EFF;font-size: smaller; text-align: right; margin:-27px 0 0 0;">电子票，自动出票</span>
        </div>
        <div class="info">
            <?php foreach ($standard as $k => $v) { ?>
                <?php if (!empty($v)) { ?>
                    <?php $arr = explode(':', $v); ?>
                    <?php if (!empty($arr[1])) { ?>
                        <div class="filed">
                            <span class="label"><?php echo $arr[0] ?>：</span>
                            <span class="text"><?php echo str_replace(',', ' ', $arr[1]) ?></span>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </article>
    <article class="commodityConDetail">
        <div class="swipeTip">
            <span>点击查看商品详情</span>
        </div>
        <div class="swipeCon">

        </div>
    </article>
    <article class="bottom" style="display: none">
        <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
    </article>
    <article class="fasten clearfix">
        <div class="fastenSubmit n_fastenSubmit">
            <a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/index', array('encrypt_id' => $encrypt_id)); ?>">
                <span class="li01"></span>
            </a>
            <input id="buynow" type="button" class="btn_com" value="立即购买">
        </div>
    </article>
</section>

<div class="welfare_mask" style="display: none">
    <div class="welfare_qcode">
        <p>请先关注公众号再购买</p>
        <p>请长按下图二维码关注或进入公众号</p>
        <img src="<?php echo !empty($qrcode) ? IMG_GJ_LIST . $qrcode : ''; ?>">
    </div>
</div>

<!--添加商品弹出框-->
<div id="popShadow" style="display: none"></div>
<div id="popwindow" class="pop addPro" style="display: none">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('Dmall/Cart/CartPay'), 'post', array('name' => 'buy')); ?>
    <div class="pop_con">
        <div class="filed">
            <span class="img"><img src="<?php $img_arr = explode(';', $data['img']);
                echo IMG_GJ_80_LIST . $img_arr[0]; ?>"></span>
            <span class="text">
            	<em id="shopname"><?php echo $data['name'] ?></em>
                <input style="display: none" id="input_shopname" name="CartPay[shopname]"
                       value="<?php echo $data['name'] ?>">
                <em id="shopprice">
                    <ins id="ins_price" class="orange">¥ <?php echo $data['price'] ?></ins>
                    <s id="originalprice"><?php if (!empty($datasku[0]['original_price'])) { ?>¥<?php echo ' ' . $datasku[0]['original_price']; ?><?php } ?></s></em>
                <input style="display: none" id="input_shopprice" name="CartPay[shopprice]"
                       value="<?php echo $data['price'] ?>">
            </span>
            <span class="close"><a href="#" id="dismiss">×</a></span>
            <input type="hidden" value="<?php echo $data['id'] ?>" name="product_id">
            <input type="hidden" value="<?php echo $encrypt_id ?>" name="encrypt_id">
        </div>
        <div class="proList">
            <?php $count = 0;
            foreach ($standard as $k => $v) { ?>
                <?php if (!empty($v)) { ?>
                    <?php $arr = explode(':', $v); ?>
                    <?php if (!empty($arr[1])) {
                        $count++; ?>
                        <div class="filed filedone">
                            <span class="label"><?php echo $arr[0] ?></span>
            <span class="text">
            <?php $array = explode(',', $arr[1]);
            $sku_count = 0; ?>
                <?php foreach ($array as $x => $y) { ?>
                    <?php if (!empty($y)) { ?>
                        <div class="genre"><?php echo $y ?></div>
                    <?php } ?>
                <?php } ?>
            </span>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <div id="sku_arr" style="display: none;">
                <?php foreach ($datasku as $key => $value) { ?>
                    <div id="filed" class="sku_filed">
                        <input id="skuname" value="<?php echo $value['name'] ?>">
                        <input id="skuprice" value="<?php echo $value['price'] ?>">
                        <input id="skuoriginal_price" value="<?php echo $value['original_price'] ?>">
                    </div>
                <?php } ?>
            </div>
        </div>
        <input name="sku_name" style="display:none" value="">
        <input name="product_id" style="display:none" value="<?php echo $data['id'] ?>">
        <div class="filed num">
            <span class="label">数量</span>
            <span class="text">
                <button id="plus" class="addone">+</button>
                <input id="num" type="tel" name="num" class="txt" value="1" maxlength="2">
                <button id="subtract" class="add" disabled="disabled">-</button>
            </span>
        </div>
        <div class="filed notice">
            <span class="label">须知：</span>
            <span class="text">
                <!--须知 后台读取-->
                <!--
                至少需提前1天，并在20点30分前购买门票
                -->
                <?php
                echo $notice;
                ?>
            </span>
        </div>
    </div>
    <div class="btn new_btn">
        <input type="button" class="btn_com" value="立即购买" id="buy">
        <!--    <input type="button" class="btn_com_gray" value="加入购物车" id="addcartnow"> -->
    </div>
    <?php echo CHtml::endForm(); ?>
</div>
</body>

<script type="text/javascript">


    $('.welfare_mask').click(function () {
        $('.welfare_mask').hide();
    });

    $(document).on('click', '#buynow', function (e) {

        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {

        } else {
            $('.welfare_mask').show();
            return false;
        }

        //设置数量输入框为1
        $('#num').val(1);
        $('#subtract').attr('disabled', true);//设置减号不可点击
        $('#popShadow').attr('style', '');
        $('#popwindow').slideToggle(500);
        e.stopPropagation();
    });

    $(document).on('click', '#addcart', function (e) {
        //设置数量输入框为1
        $('#num').val(1);
        $('#subtract').attr('disabled', true);//设置减号不可点击
        $('#popShadow').attr('style', '');
        $('#popwindow').slideToggle(500);
        e.stopPropagation();
    });

    //点击X隐藏商品对话框
    $('#dismiss').click(function (e) {
        $('#popwindow').slideToggle();
        $('#popShadow').attr('style', 'display:none');
        e.stopPropagation();
    });

    //点击阴影部分隐藏商品对话框
    $('#popShadow').click(function (e) {
        $('#popwindow').slideToggle();
        $('#popShadow').attr('style', 'display:none');
        e.stopPropagation();
    });

    //    function choosesku(name,sku,sku_name_num,sku_num)
    //    {
    //        var skucount=<?php //echo $sku_count;?>
    //        var skuarr=<?php //$datasku;?>
    //        if($('#genre'+sku_name_num+""+sku_num).attr('class')=='genre over')
    //        {
    //            $('#genre'+sku_name_num+""+sku_num).attr('class','genre over');
    //        }else
    //        {
    //            $('#genre'+sku_name_num+""+sku_num).attr('class','genre');
    //        }
    //
    //    }

    //选择尺寸
    $('.genre').click(function () {
        $(this).parent().find('.genre.over').attr('class', 'genre');
        $(this).attr('class', 'genre over');
        var count = 0;
        $('.genre.over').each(function () {
            count++;
        });
        var name = '';
        if (count ==  <?php echo $count?>) {
            $('.genre.over').each(function () {
                if (name == '')
                    name = $(this).text();
                else
                    name = name + ',' + $(this).text();
            });
            $('.sku_filed').each(function () {
                if ($(this).find('#skuname').val() == name) {
                    //等于就改变sku价格
                    $('#originalprice').text('¥ ' + $(this).find('#skuoriginal_price').val());
                    $('#ins_price').text('¥ ' + $(this).find('#skuprice').val());
                    $('#input_shopprice').val($(this).find('#skuprice').val());

                }
            });
        }
    });

    //减号点击
    $('#subtract').click(function (e) {
        var num = $('#num').val();
        if (num == 1) {
            $('#subtract').attr('disabled', true);
        }
        else {
            num--;
            $('#num').val(num);
        }
        e.stopPropagation();
        return false;
    });

    //加号点击
    $('#plus').click(function (e) {
        var num = $('#num').val();
        num++;
        if (num >= 2) {
            $('#subtract').attr('disabled', false);
        }
        $('#num').val(num);
        e.stopPropagation();
        return false;
    });

    $('#num').bind('input propertychange', function () {
        var num = $('#num').val();
        if (num == "" || num == 0) {
            //数量修改成0或空时，默认设置为1
            $('#num').val(1);
        }
    });
    //数量输入框失去焦点时
    $('#num').blur(function () {
        if ($('#num').val() == 1) {
//            输入框内的数字=1，设置减号为不可点击
            $('#subtract').attr('disabled', true);
        }
        else {
            $('#subtract').attr('disabled', false);
        }
    });

    //立即购买
    $('#buy').click(function () {
        var skuname = '';
        var flag = 1;
        var count = 0;
        $('.genre.over').each(function () {
            if (flag == 1) {
                skuname += $(this).text();
            } else {
                skuname += ',' + $(this).text();
            }
            flag++;
            count++;
        });
        if (count ==  <?php echo $count?>) {
            $('input[name=sku_name]').val(skuname);
            $('form[name=buy]').submit();
        } else {
            alert('请选择商品规格');
        }
    });

    //加入购物车
    $('#addcartnow').click(function () {
        var sku_name = '';
        var flag = 1;
        $('.genre.over').each(function () {
            if (flag == 1) {
                sku_name += $(this).text();
            } else {
                sku_name += ',' + $(this).text();
            }
            flag++;
        });
        var num = $('#num').val();
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('Dmall/Cart/AddCart'));?>',
            data: {sku_name: sku_name, num: num, product_id: '<?php echo $data['id']?>'},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status == <?php echo ERROR_NONE?>) {
                    alert("成功加入购物车");
                    $('#popwindow').slideToggle();
                    $('#popShadow').attr('style', 'display:none');
                    e.stopPropagation();
                } else {
                    if (data.status == <?php echo ERROR_PARAMETER_FORMAT?>) {
                        window.location.href = "<?php echo Yii::app()->createUrl('mobile/auth/login', array('goUrl' => Yii::app()->createUrl('Dmall/Commodity/CommodityDetails', array('id' => $data['id'])), 'encrypt_id' => $encrypt_id));?>";
                    } else {
                        alert(data.errMsg);
                    }
                }
            }
        });
    });

    $('.swipeTip').click(function () {
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('Dmall/Commodity/ProductDetail'));?>',
            data: {product_id: '<?php echo $data['id']?>'},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                $('.swipeTip').hide();
                $('.swipeCon').append(data.data);
            }
        });
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
            title: '【东钱湖旅游】<?php echo $data['name'];?>', // 分享标题
            link: '<?php echo WAP_DOMAIN . '/Dmall/Commodity/CommodityDetails?encrypt_id=' . $encrypt_id . '&id=' . $data['id'];?>', // 分享链接
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
            title: '【东钱湖旅游】<?php echo $data['name'];?>', // 分享标题
            desc: '东钱湖在线购物商城，在线购买电子票，预约景区', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/Dmall/Commodity/CommodityDetails?encrypt_id=' . $encrypt_id . '&id=' . $data['id'];;?>', // 分享链接
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
            title: '【东钱湖旅游】<?php echo $data['name'];?>', // 分享标题
            desc: '东钱湖在线购物商城，在线购买电子票，预约景区', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/Dmall/Commodity/CommodityDetails?encrypt_id=' . $encrypt_id . '&id=' . $data['id'];;?>', // 分享链接
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
