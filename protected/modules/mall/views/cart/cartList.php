<body>
<div class="cartWrap">
    <div class="menuCom">
        <ul>
            <li class="cur"><a href="#">购物车</a></li>
            <!--             	<li><a href="#">购物记录</a></li> -->
        </ul>
    </div>
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mall/Cart/CartPay'), 'post', array('name' => 'buy')); ?>
    <div class="itemWrap">
        <?php foreach ($cart as $k => $v) { ?>
            <div class="itemCom clearfix">
                <div class="checkboxCom check"><!--多选框选中加样式名checkboxComCur-->
                    <input type="checkbox" id="checkbox01" class="c_choose" name="skuid[<?php echo $v->id ?>]"
                           value="<?php echo $v->sku_id; ?>">
                    <label for="checkbox01">&nbsp;</label>
                </div>
                <div class="htcon"><img src="<?php $img_arr = explode(';', $v->img);
                    echo IMG_GJ_80_LIST . $img_arr[0] ?>"></div>
                <div class="text">
                    <div class="title">
                        <span class="r">¥ <strong class="cPrice"><?php echo $v->sku_price ?></strong></span>
                        <h3><?php echo $v->name ?></h3>
                    </div>
                    <div class="block">
                        <div class="attr"><?php echo str_replace(',', ' ', $v->sku_name) ?></div>
                        <div class="num_select">
                            <b>-</b>
                            <input type="text" class="text" maxlength="4" value="<?php echo $v->num ?>"
                                   name="num[<?php echo $v->id ?>]"/>
                            <i>+</i>
                            <input type="text" id="id" style="display: none" value="<?php echo $v->id; ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="cartTotal">
        <div class="checkboxCom CK_all">
            <input type="checkbox" id="checkbox-all1" class="cartSelectAll">
            <label for="checkbox-all1">全选</label>
        </div>
        <div class="btnWrap"><input type="button" value="结算" name="buy"></div>
        <div class="totalP">
            总计：<span>¥ <strong class="c_total_all">0</strong></span>
        </div>
    </div>
    <input type="hidden" name="encrypt_id" value="<?php echo $encrypt_id ?>">
    <input type="hidden" name="is_cart" value="<?php echo IS_CART_YES ?>"/>
    <?php echo CHtml::endForm(); ?>
</div>
<!--main end-->
<script type="text/javascript">
    var money = 0;
    var num = 0;
    // 	$(document).ready(function(){
    // 		$('.c_choose').each(function(){
    // 				if($(this).attr('checked')){
    // 				$(this).parent().addClass('checkboxComCur');
    // 			}
    // 		});
    // 	});
    $('.check').click(function () {
        if ($(this).hasClass('checkboxComCur')) {
            $(this).parent().find('.checkboxCom').removeClass('checkboxComCur');
            money -= parseFloat($(this).parent().find('.cPrice').text()) * $(this).parent().find('.cPrice').parent().parent().next().find('.num_select').find('input').val();
            $('.c_total_all').text(money);
            num--;
            if (num == 0) {
                $('input[name=buy]').val('结算');
            } else {
                $('input[name=buy]').val('结算(' + num + ')');
            }
            $(this).find('.c_choose').attr("checked", false);

        } else {
            $(this).parent().find('.checkboxCom').addClass('checkboxComCur');
            money += parseFloat($(this).parent().find('.cPrice').text()) * $(this).parent().find('.cPrice').parent().parent().next().find('.num_select').find('input').val();
            $('.c_total_all').text(money);
            num++;
            $('input[name=buy]').val('结算(' + num + ')');
            $(this).find('.c_choose').attr("checked", true);
        }
        return false;
    });

    //全选，全不选
    $('.CK_all').click(function () {
        money = 0;
        num = 0;
        if ($(this).hasClass('checkboxComCur')) {
            $('.checkboxCom').removeClass('checkboxComCur');
            $('.c_choose').attr("checked", false);
            money = 0;
        } else {
            $('.checkboxCom').addClass('checkboxComCur');
            $('.c_choose').attr("checked", true);
            $('.cPrice').each(function () {
                money += parseFloat($(this).text()) * $(this).parent().parent().next().find('.num_select').find('input').val();
                num++;
            });
        }
        if (num == 0) {
            $('input[name=buy]').val('结算');
        } else {
            $('input[name=buy]').val('结算(' + num + ')');
        }

        $('.c_total_all').text(money);
        return false;
    });

    //减
    $('b').click(function () {
        var n = $(this).next().val();
        n--;
        if (n < 1) {
            n = 1;
            //减到0提示用户是否要删除该购物车的这个商品
            if (confirm("是否要删除该商品")) {
                var cart_id = $(this).parent().find('#id').val();
                var flag = false;
                ;
                $.ajax({
                    url: '<?php echo Yii::app()->createUrl('mall/Cart/DelCartGoods')?>',
                    type: 'POST',
                    data: {cart_id: cart_id},
                    async: false,
                    dataType: 'json',
                    success: function (data) {
                        flag = true;
                        var d = JSON.stringify(data)
                        if (data == 'success')
                            window.location.reload();
                        else
                            alert(data);
                    },
                    error: function (e1, e2, e3) {
                        alert(e1);
                        alert(e2);
                        alert(e3);
                    }
                });
                if (flag) {
                    return true;
                }
            }
        } else {
            if ($(this).parent().parent().parent().parent().find('.checkboxCom').hasClass('checkboxComCur')) {
                money -= parseFloat($(this).parent().parent().parent().find('.cPrice').text());
                $('.c_total_all').text(money.toFixed(2));
            }
        }
        $(this).next().val(n);
    });
    //加
    $('i').click(function () {
        var n = $(this).parent().find('input').val();
        n++;
        $(this).parent().find('input').val(n);
        if ($(this).parent().parent().parent().parent().find('.checkboxCom').hasClass('checkboxComCur')) {
            money += Number($(this).parent().parent().parent().find('.cPrice').text());
            $('.c_total_all').text(money.toFixed(2));
// 			alert(money);
// 			alert(Number($(this).parent().parent().parent().find('.cPrice').text()));
        }
    });

    $('input[name=buy]').click(function () {
        if (num > 0) {
            $('form[name=buy]').submit();
        }
    });
</script>
</body>