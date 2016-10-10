<head>
    <title>订单结算</title>
</head>
<body>
<div class="cartWrap cartPay">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mall/Order/CreateOrder', array('sku_id' => $skuid, 'num' => $num)), 'post', array('name' => 'buy')); ?>
    <div class="addrItem clearfix">
        <div class="checkboxCom checkboxComCur">
            <label> </label>
        </div>
        <?php if (!empty($default)) { ?>
            <div class="addrItemL">
                <div class="block clearfix">
                    <div class="left">
                        <span class="name">收货人：<?php echo $default->name ?></span>
                    </div>
                    <span class="right"><?php echo $default->tel ?></span>
                </div>
                <div class="block clearfix">
                    <div class="left">收货地址：<?php echo $default->address ?></div>
                </div>
            </div>
        <?php } else { ?>
            <?php echo '请选择地址' ?>
        <?php } ?>
        <input type="hidden" value="<?php echo empty($default) ? '' : $default->id ?>" name="address_id"/>
        <div class="rArrow"></div>
    </div>

    <div class="itemWrap">
        <?php foreach ($sku as $k => $v) { ?>
            <div class="itemCom clearfix">
                <a href="<?php echo Yii::app()->createUrl('mall/Commodity/CommodityDetails', array('id' => $v->product_id, 'encrypt_id' => $encrypt_id)) ?>">

                    <div class="htcon">
                        <img src="<?php $img_arr = explode(';', $v->img);
                        echo IMG_GJ_80_LIST . $img_arr[0] ?>">
                    </div>
                    <div class="text">
                        <div class="title">
                            <span class="r">¥ <strong id="shopprice"
                                                      class="cPrice"><?php echo $v->price ?></strong></span>
                            <h3><?php echo $v->product_name ?></h3>
                        </div>
                        <div class="block">
                            <div class="attr"><?php echo str_replace(',', ' ', $v->name) ?></div>
                            <div class="num_select">
                                &nbsp;&nbsp;× <?php echo $num[$k] ?>&nbsp;&nbsp;
                                <!--                         <b id="subtract">-</b> -->
                                <!--                     		<input id="num" type="text" class="text" maxlength="2" value="<?php //echo $num[$k]?>" />  -->
                                <!--                         <i id="plus">+</i> -->
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
        <dl class="freight clearfix">
            <dt>运费</dt>
            <dd>¥ <?php echo number_format($freightMoney, 2) ?></dd>
        </dl>
        <dl class="freight clearfix">
            <textarea placeholder="给卖家留言..." name="remark"></textarea>
        </dl>
        <dl class="freight totalP clearfix">
            <dt>合计</dt>
            <dd>¥ <?php echo number_format($total, 2) ?></dd>
        </dl>
    </div>

    <div class="cartTotal1">
        <p id="sumprice1">¥ <?php echo number_format($total, 2) ?> + ¥ <?php echo number_format($freightMoney, 2) ?>
            运费</p>
        <p id="sumprice2" class="color">需付：¥ <?php echo number_format($total + $freightMoney, 2) ?></p>
        <!--        不算上运费的金额-->
    </div>

    <div class="btnWrap"><input type="button" value="去结算" class="btn_red"></div>

    <article class="bottom">
        <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
    </article>
    <!--main end-->
    <input type="hidden" value="<?php echo $is_cart ?>" name="is_cart"/>
    <input type="hidden" value="<?php echo $encrypt_id ?>" name="encrypt_id"/>
    <?php echo CHtml::endForm(); ?>
</div>

<!--添加地址弹出框-->
<div id="popShadow" style="display:none"></div>
<div class="pop addAddress" style="display:none">
    <div class="pop_con">
        <!--选择地址-->
        <div class="title" id="select">
            选择地址
            <a class="close">×</a>
        </div>
        <div class="title" id="add" style="display: none">
            新增地址
            <a class="close">×</a>
        </div>
        <div class="addressWrap" id="selectF">
            <?php foreach ($address as $k => $v) { ?>
                <div class="addressList clearfix">
                    <div class="listL <?php echo $v->if_default == IF_DEFAULT_YES ? 'sure' : '' ?>">
                        <input style="display: none" value="<?php echo $v->id ?>" name="address_id">
                        <span class="ico"></span>
                    </div>
                    <div class="listM">
                        <span class="name"><?php echo $v->name ?><?php echo $v->tel ?></span>
                        <span class="address"><?php echo $v->address ?></span>
                    </div>
                    <div class="listR"><a href="" class="more"></a></div>
                </div>
            <?php } ?>
            <div class="addressMore clearfix">
                <a id="addAddress">
                    <div class="listL"><span class="ico"></span></div>
                    <div class="listM">新增地址</div>
                    <div class="listR"><span class="jt"></span></div>
                </a>
            </div>
        </div>
        <!--添加地址-->
        <div class="addressWrap" id="addF" style="display: none">
            <div class="filed">
                <span class="label">收货人</span>
                <span class="text"><input type="text" class="txt" placeholder="请输入您的姓名" name="name"></span>
            </div>
            <div class="filed">
                <span class="label">联系方式</span>
                <span class="text"><input type="tel" class="txt" placeholder="请输入您的联系方式" name="tel"></span>
            </div>
            <div class="filed">
                <span class="label">选择地址</span>
                <span class="text">
                	<select name="province">
                        <option>请选择</option>
                        <?php foreach ($province as $k => $v) { ?>
                            <option value="<?php echo $v->code ?>"><?php echo $v->name ?></option>
                        <?php } ?>
                    </select>
                    <select id="city">
                        <option>请选择</option>
                    </select>
                    <select id="area">
                        <option>请选择</option>
                    </select>
                </span>
            </div>
            <div class="filed">
                <span class="label">详细地址</span>
                <span class="text"><input type="text" class="txt" placeholder="请输入详细地址" name="detailAddress"></span>
            </div>
            <div class="filed filedEnd">
                <span class="label">邮政编码</span>
                <span class="text"><input type="tel" class="txt" placeholder="请输入邮政编码" name="code"></span>
            </div>
            <div class="btn">
                <button>保存</button>
            </div>
        </div>
        <script>
            $(".listM").click(
                function () {
                    n = $(".listM").index(this);
                    $(".listL").removeClass("sure");
                    $(".listL").eq(n).addClass("sure");
                    var id = $(".listL").eq(n).find('input').val();
                    $.ajax({
                        url: '<?php echo(Yii::app()->createUrl('mobile/uCenter/Address/SetDefaultAddress'));?>',
                        data: {id: id},
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == <?php echo ERROR_NONE?>) {
                                alert("选择地址成功");
                                window.location.href = '<?php echo Yii::app()->createUrl('mall/Cart/CartPay', array('skuid' => $skuid, 'num' => $num, 'encrypt_id' => $encrypt_id))?>';
                            } else {
                                alert(data.errMsg);
                            }
                        }
                    });
                }
            );
        </script>


    </div>
</div>


</body>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if(Yii::app()->user->hasFlash('error')){ ?>
        alert('该地区不支持配送');
        <?php }?>
    });

    //减号点击
    $('#subtract').click(function (e) {
        var num = $('#num').val();
        if (num != 1) {
            num--;
            $('#num').val(num);//数量减一
            //金额减一个数量级
            var sumprice = $('#sumprice').val();//当前不算上运费的总支付金额
            var price = $('#shopprice').text();//单个商品金额
            sumprice = (num * 1) * (price * 1);
            $('#sumprice').val(sumprice);
            $('#sumprice1').text('¥ ' + sumprice.toFixed(2) + ' + ¥ <?php echo number_format($freightMoney, 2)?>运费');
            $('#sumprice2').text('需付：' + (sumprice + (<?php echo number_format($freightMoney, 2)?>* 1)).toFixed(2)
        )
            ;
        }
    });

    //加号点击
    $('#plus').click(function (e) {
        //设置输入框数量
        var num = $('#num').val();
        num++;
        $('#num').val(num);
        //设置显示金额
        var sumprice = $('#sumprice').val();//当前不算上运费的总支付金额
        var price = $('#shopprice').text();//单个商品金额
        sumprice = (num * 1) * (price * 1);
        $('#sumprice').val(sumprice);
        $('#sumprice1').text('¥ ' + sumprice.toFixed(2) + ' + ¥ <?php echo number_format($freightMoney, 2)?>运费');
        $('#sumprice2').text('需付：' + (sumprice + (<?php echo number_format($freightMoney, 2)?>* 1)).toFixed(2)
        )
        ;
    });
    $('#num').bind('input propertychange', function () {
        var num = $('#num').val();
        var price = $('#shopprice').text();//单个商品金额
        if (num != "" || num != 0) {
            //数量不为空
            $('#num').val(num);
            var sumprice = (num * 1) * (price * 1);
            $('#sumprice').val(sumprice);
            $('#sumprice1').text('¥ ' + sumprice.toFixed(2) + ' + ¥ <?php echo number_format($freightMoney, 2)?>运费');
            $('#sumprice2').text('需付：' + (sumprice + (<?php echo number_format($freightMoney, 2)?>* 1)).toFixed(2)
        )
            ;
        } else {
            //如果数量为空，就设置商品为一件
            $('#num').val(1);
            $('#sumprice').val(price);
            $('#sumprice1').text('¥ ' + price.toFixed(2) + ' + ¥ <?php echo number_format($freightMoney, 2)?>运费');
            $('#sumprice2').text('需付：' + (price(<?php echo number_format($freightMoney, 2)?>* 1)).toFixed(2)
        )
            ;
        }

    });

    //开启弹出框
    $('.addrItem').click(function () {
        $('#popShadow').show();
        $('.pop').show();
        $('#addF').hide();
        $('#selectF').show();
        $('.select').show();
        $('.add').hide();
    });

    //关闭弹出框
    $('.close').click(function () {
        $('#popShadow').hide();
        $('.pop').hide();
    });

    //新增地址
    $('#addAddress').click(function () {
        $('.addressWrap').hide();
        $('.select').hide();
        $('.add').show();
        $('#addF').show();
        $('#selectF').hide();
    });

    //点击弹出框外，隐藏弹出框
    $('#popShadow').click(function () {
        $('#popShadow').hide();
        $('.pop').hide();
    });

    //添加地址
    $('.btn').click(function () {
        var name = $('input[name=name]').val();//收货人
        var tel = $('input[name=tel]').val();//联系方式
        var province = $('select[name=province]').val();//省
        var city = $('#city').val();//市
        var area = $('#area').val();//区
        var address = $('input[name=detailAddress]').val();//详细地址
        var code = $('input[name=code]').val();//联系方式
        var encrypt_id = '<?php echo $encrypt_id ?>';
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('mobile/uCenter/Address/AddAddress'));?>',
            data: {name: name, tel: tel, province: province, city: city, area: area, address: address, code: code, encrypt_id: encrypt_id},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.status == <?php echo ERROR_NONE?>) {
                    alert("添加地址成功");
                    window.location.href = '<?php echo Yii::app()->createUrl('mall/Cart/CartPay', array('skuid' => $skuid, 'num' => $num, 'encrypt_id' => $encrypt_id))?>';
                } else {
                    alert(data.errMsg);
                }
            }
        });
    });

    //选择省 
    $('select[name=province]').change(function () {
        var code = $(this).val();
        $('#city').empty();
        $('#city').append('<option>请选择</option>');
        $('#area').empty();
        $('#area').append('<option>请选择</option>');
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('mall/Cart/GetCity'));?>',
            data: {code: code},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    var code = data[i].split(",")[1];
                    var name = data[i].split(",")[0];
                    var text = '<option value="' + code + '">' + name + '</option>';
                    $('#city').append(text);
                }
            }
        });
    });

    //选择市
    $('#city').change(function () {
        var code = $(this).val();
        $('#area').empty();
        $('#area').append('<option>请选择</option>');
        $.ajax({
            url: '<?php echo(Yii::app()->createUrl('mall/Cart/GetArea'));?>',
            data: {code: code},
            type: 'post',
            dataType: 'json',
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    var code = data[i].split(",")[1];
                    var name = data[i].split(",")[0];
                    var text = '<option value="' + code + '">' + name + '</option>';
                    $('#area').append(text);
                }
            }
        });
    });

    //点击结算
    $('.btn_red').click(function () {
        var address = $('input[name = address_id]').val();
        if (address == '') {
            alert("请选择收货地址");
        } else {
            $('form[name=buy]').submit();
        }
    });

</script>