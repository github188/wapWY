<head>
    <title>商城列表</title>
</head>
<body class="commodity">
<header>
    <div class="search">
        <form><input type="search" id="enter" placeholder="搜索"></form>
    </div>
    <div class="top">
        <ul>
            <li class="" id="salesli">
                <a href="<?php echo Yii::app()->createUrl('mall/Commodity/commodityList', array('typename' => 'limit_num', 'type' => $type, 'placeholder' => $placeholder, 'shopgorup' => $shopgroup, 'title' => $title, 'groupid' => $groupid, 'encrypt_id' => $encrypt_id)) ?>"
                   class="" id="salesa">销量<em></em></a>
            </li>
            <li class="" id="priceli">
                <a href="<?php echo Yii::app()->createUrl('mall/Commodity/commodityList', array('typename' => 'price', 'type' => $type, 'placeholder' => $placeholder, 'shopgorup' => $shopgroup, 'title' => $title, 'groupid' => $groupid, 'encrypt_id' => $encrypt_id)) ?>"
                   class="" id="pricea">价格<em></em></a>
            </li>
            <li class="" id="newgoodsli">
                <a href="<?php echo Yii::app()->createUrl('mall/Commodity/commodityList', array('typename' => 'last_time', 'type' => $type, 'placeholder' => $placeholder, 'shopgorup' => $shopgroup, 'title' => $title, 'groupid' => $groupid, 'encrypt_id' => $encrypt_id)) ?>"
                   class="" id="newgoodsa">新品<em></em></a>
            </li>
            <li class="li2">筛选</li>
            <div class="list clearfix">
                <?php if (!empty($group)) {
                    foreach ($group as $k => $v) { ?>
                        <a href="<?php echo Yii::app()->createUrl('mall/Commodity/commodityList', array('typename' => 'limit_num', 'type' => $type, 'placeholder' => $placeholder, 'shopgorup' => $shopgroup, 'title' => $title, 'groupid' => $v->id, 'encrypt_id' => $encrypt_id)) ?>"
                           class="<?php echo $k % 2 == 0 ? 'end' : ''; ?>"><?php echo $v->name ?></a>
                    <?php }
                } ?>
            </div>
        </ul>
        <input style="display: none" id="typename" value="<?php echo $typename; ?>">
        <input style="display: none" id="type" value="<?php echo $type; ?>">
        <input style="display: none" id="placeholder" value="<?php echo $placeholder; ?>">
        <input style="display: none" id="shopgroup" value="<?php echo $shopgroup; ?>">
        <input style="display: none" id="shopgroup" value="<?php echo $groupid; ?>">
    </div>
    <script>
        $("#enter").focus(
            function () {
                $("input").addClass("searchClick");
            }
        );
        $("#enter").blur(
            function () {
                $("input").removeClass("searchClick");
            }
        );
        $(".li2").click(
            function () {
                if ($(".top").hasClass("topClick")) {
                    $(".top").removeClass("topClick");
                } else {
                    $(".top").addClass("topClick");
                }
            }
        );
    </script>
</header>
<section>
    <article id="newgoods_title" class="title"><?php echo $title ?></article>
    <!--    <article id="hotgoods_title" class="title" style="display: none">最热商品</article>-->
    <!--    标题-->
    <input style="display: none" id="goods_title" value="<?php echo $title ?>">
    <?php if (!empty($data)) {
        foreach ($data as $key => $value) { ?>
            <article class="item clearfix" id="goods">
                <div class="img">
                    <a href="<?php echo Yii::app()->createUrl('mall/commodity/commodityDetails', array('id' => $value['id'], 'encrypt_id' => $encrypt_id)) ?>">
                        <?php $img_arr = explode(';', $value['img']) ?>
                        <img src="<?php echo IMG_GJ_125_LIST . $img_arr[0]; ?>">
                    </a>
                </div>
                <div class="text">
                    <a href="<?php echo Yii::app()->createUrl('mall/commodity/commodityDetails', array('id' => $value['id'], 'encrypt_id' => $encrypt_id)) ?>">
                        <span class="name"><?php echo $value['name']; ?></span>
                        <span class="price">
                            <ins>¥<?php echo $value['minprice'] ?></ins>
                            <s id="proshopprice">¥<?php echo $value['minorgprice'] ?></s>
                        </span>
                    </a>
                    <span class="add"><a href="#"><img id="imgadd" src="<?php echo USER_STATIC_IMAGES; ?>user/add.png"></a></span>
                </div>
                <input id="id" style="display:none" type="text" value="<?php echo $value['id']; ?>">
            </article>
        <?php }
    } ?>
    <article class="bottom">
        <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
    </article>
</section>

<!--添加商品弹出框-->
<div id="popShadow" style="display: none"></div>
<div id="popwindow" class="pop addPro" style="display: none">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mall/Cart/CartPay'), 'post', array('name' => 'buy')); ?>
    <div class="pop_con">
        <div class="filed" id="filed_first">
            <span class="img"><img src="<?php echo USER_STATIC_IMAGES; ?>pro.png"></span>
            <span class="text">
            	<em id="shopname">这里是商品名称</em>
                <input style="display: none" id="input_shopname" name="CartPay[shopname]" value="">
                <em id="shopprice">
                    <ins class="orange">¥100.00</ins>
                    <s id="orgprice">¥100.00</s></em>
                <input style="display: none" id="input_shopprice" name="CartPay[shopprice]" value="">
            </span>
            <span class="close"><a href="#" id="dismiss">×</a></span>
        </div>
        <input name="sku_name" style="display:none" value="">
        <input name="product_id" style="display:none" value="">
        <input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id ?>">
        <div class="proList">
        </div>
        <div class="filed num" id="filed_second">
            <span class="label">数量</span>
            <span class="text">
				<button id="plus" class="addone">+</button>
                <input id="num" type="tel" name="num" class="txt" value="1" maxlength="2">
                <button id="subtract" class="add" disabled="disabled">-</button><!--disabled在不能点时候加，如果可以点击就去掉-->
            </span>
        </div>
        <div class="btn">
            <input type="button" class="btn_com" id="buy" value="立即购买">
            <input type="button" class="btn_com_gray" value="加入购物车" id="addcartnow">
        </div>
    </div>
    <input name="CartPay[id]" id="shopid" value="" style="display: none">
    <?php echo CHtml::endForm() ?>
</div>

<div class="footerH"></div>
<div id="footer">
    <ul>
        <li class="li01">
            <a href="<?php echo Yii::app()->createUrl('mall/Commodity/Index', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
        <li class="li02">
            <a href="<?php echo Yii::app()->createUrl('mall/Commodity/CommodityList', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
        <li class="li04">
            <a href="<?php echo Yii::app()->createUrl('mall/Cart/CartList', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
        <li class="li05">
            <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/MemberCenter', array('encrypt_id' => $encrypt_id)); ?>"></a>
        </li>
    </ul>
</div><!--footer end-->

</body>
<script type="text/javascript">
    var count_standard = 0;
    $(document).ready(function () {
        var typename = $(document).find('#typename').val();
        if (typename == 'limit_num') {
            typename = 'salesa';
            typename1 = 'salesli';
        } else if (typename == 'price') {
            typename = 'pricea';
            typename1 = 'priceli';
        } else if (typename == 'last_time') {
            typename = 'newgoodsa';
            typename1 = 'newgoodsli';
        }
        var type = $(document).find('#type').val();
        $(document).find('#' + typename).attr('class', 'cur');
        if (type == 'up') {
            $(document).find('#' + typename1).attr('class', 'li_up');
        } else if (type == 'down') {
            $(document).find('#' + typename1).attr('class', 'li_down');
        }
        $(document).find('#enter').val($(document).find('#placeholder').val());
        //判断是否显示标题
        var title = $('#goods_title').val();
    });


    //点击商品跳转到商品详情
    $(document).on('click', '#goods', function (e) {
        e.stopPropagation();
        window.location.href = "<?php echo Yii::app()->createUrl('mall/commodity/commodityDetails')?>?id=" + $(this).find('#id').val() + '&encrypt_id=<?php echo $encrypt_id?>';
    });

    //按enter搜索
    $(document).on('keydown', '#enter', function (e) {
            var title = $('#goods_title').val();
            var url = "<?php echo Yii::app()->createUrl('mall/commodity/commodityList')?>?typename=" + $(document).find('#typename').val() + "&type=" + $(document).find('#type').val() + "&placeholder=" + $(document).find('#enter').val() + "&shopgroup=" + $(document).find('#shopgroup').val() + "&title=" + title + '&encrypt_id=<?php echo $encrypt_id?>';
            if (e.keyCode == 13) {
                window.location.href = url;
                return false;
            }
        }
    );

    //弹出商品对话框
    $(document).on('click', '#imgadd', function (e) {
        var name = $(this).parent().parent().prev().find('.name').text();
        var price = $(this).parent().parent().prev().find('ins').text();
        var id = $(this).parent().parent().parent().parent().find('#id').val();//该商品ID
        $(document).find('#shopname').text(name);
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('mall/Commodity/GetProductStandard'));?>',
            data: {id: id},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status == <?php echo ERROR_NONE ?>) {
                    var standard = data.data;
                    var arr_standard = standard.split(';');
                    count_standard = arr_standard.length - 1;
                    for (var i = 0; i < arr_standard.length; i++) {
                        if (arr_standard[i] != '') {
                            var arr = arr_standard[i].split(':');
                            if (arr[1] != '') {
                                $('.proList').append('<div class="filed filedone"><span class="label">' + arr[0] + '</span><span class="text" id="sku_name' + i + '"></span></div>');
                            } else {
                                count_standard = count_standard - 1;
                            }
                            var arr_v = arr[1].split(',');
                            for (var j = 0; j < arr_v.length; j++) {
                                if (arr_v[j] != '') {
                                    $('#sku_name' + i).append('<div class="genre">' + arr_v[j] + '</div>');
                                }
                            }
                        }
                    }
                    $('#shopprice').find('ins').text(data.price);
                    $('#orgprice').text(data.orgprice);
                    $('#filed_first').find('img').attr("src", data.img);
                    $('input[name=product_id]').val(data.id);
                    //选择尺寸
                    $('.genre').bind("click", function () {
                        $(this).parent().find('.genre.over').attr('class', 'genre');
                        $(this).attr('class', 'genre over');
                        var name = $(this).parent().find('.genre.over').text();
                        $.ajax({
                            url: '<?php echo(Yii::app()->createUrl('mall/Commodity/GetProductSkuPrice'));?>',
                            data: {id: id, name: name},
                            type: 'post',
                            dataType: 'json',
                            success: function (data) {
                                if (data.status == <?php echo ERROR_NONE?>) {
                                    $('#shopprice').find('ins').text(data.price);
                                    $('#orgprice').text(data.orgprice);
                                }
                            }
                        });
                        return false;
                    });
                    document.body.addEventListener('touchmove', function (event) {
                        if ($('#popShadow').attr('style') == '') {
                            event.preventDefault();
                        }
                    }, false);
                } else {
                    alert(data.errMsg);
                }
            }
        });


        //设置数量输入框为1
        $('#num').val(1);
        $('#subtract').attr('disabled', true);//设置减号不可点击

        $('#input_shopname').val(name);
        $('#input_shopprice').val(price);
        $('#shopid').val(id);
        $('#popShadow').attr('style', '');
        $('#popwindow').slideToggle(500);
        e.stopPropagation();
    });


    //点击X隐藏商品对话框
    $('#dismiss').click(function (e) {
        $('#popwindow').slideToggle();
        $('#popShadow').attr('style', 'display:none');
        $('.filedone').remove();
        e.stopPropagation();
    });

    //点击阴影部分隐藏商品对话框
    $('#popShadow').click(function (e) {
        $('#popwindow').slideToggle();
        $('#popShadow').attr('style', 'display:none');
        $('.filedone').remove();
        e.stopPropagation();
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
        if (count == count_standard) {
            $('input[name=sku_name]').val(skuname);
            $('form[name=buy]').submit();
        } else {
            alert('请选择商品规格');
        }

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


    //加入购物车
    $('#addcartnow').click(function () {
        var sku_name = '';
        var flag = 1;
        var product_id = $('input[name=product_id]').val();
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
            url: '<?php echo(Yii::app()->createUrl('mall/Cart/AddCart'));?>',
            data: {sku_name: sku_name, num: num, product_id: product_id, encrypt_id: '<?php echo $encrypt_id ?>'},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status == <?php echo ERROR_NONE?>) {
                    alert("成功加入购物车");
                    $('#popwindow').slideToggle();
                    $('#popShadow').attr('style', 'display:none');
                    e.stopPropagation();
                } else {
                    alert(data.errMsg);
                    if (data.status == 'noLogin') {
                        window.location.href = '<?php echo Yii::app()->createUrl('mobile/auth/login', array('goUrl' => Yii::app()->request->getUrl(), 'encrypt_id' => $encrypt_id)) ?>';
                    }
                }
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
            title: '商城列表', // 分享标题
            link: '<?php echo WAP_DOMAIN . '/mall/Commodity/CommodityList?encrypt_id=' . $encrypt_id;?>', // 分享链接
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
            title: '商城列表', // 分享标题
            desc: '在线购物商城', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/mall/Commodity/CommodityList?encrypt_id=' . $encrypt_id;?>', // 分享链接
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
            title: '商城列表', // 分享标题
            desc: '在线购物商城', // 分享描述
            link: '<?php echo WAP_DOMAIN . '/mall/Commodity/CommodityList?encrypt_id=' . $encrypt_id;?>', // 分享链接
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