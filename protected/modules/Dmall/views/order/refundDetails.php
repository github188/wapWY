<body>
<section class="refund">
    <section class="refundWrap">
        <article>
            <div class="state">商家已同意退款，请退货给商家</div>
        </article>
        <article class="refundAdd clearfix">
            <div class="filed">
                <span class="label">退货地址：</span>
                <span class="text"><?php echo $data['refund_address'];?></span>
            </div>
            <article class="clearfix"><button class="btn_green" onclick="applyRefund(<?php echo $order_sku_id?>,<?php echo $order_id?>,<?php echo $order_status?>);">填写退货物流信息</button></article>
        </article>
        <article class="refundInfo">
            <div class="filed">
                <span class="label">申请时间：</span>
                <span class="text"><?php echo $data['apply_refund_time'];?></span>
            </div>
            <div class="filed">
                <span class="label">退款原因：</span>
                <span class="text"><?php echo $GLOBALS['REFUND_REASON'][$data['refund_reason']];?></span>
            </div>
            <div class="filed">
                <span class="label">处理方式：</span>
                <span class="text">退款<?php if($data['if_return']==IF_RETURN_YES){?>退货<?php }?></span>
            </div>
            <div class="filed filedend">
                <span class="label">退款金额：</span>
                <span class="text"><?php echo $data['refund_money'];?></span>
            </div>
        </article>
        <article class="bottomImg" style="display: none">
            <img src="<?php echo USER_STATIC_IMAGES; ?>user/logo-bottom.png">
        </article>
    </section>
</section>

<!--退货信息-->
<div id="popShadow" style="display: none"></div>
<div class="pop refunded" id="popRefund" style="display:none">
    <div class="pop_con">
        <!--        选择地址--><?php //echo Yii::app()->createUrl('Dmall/order/applyRefundObj',array('order_sku_id'=>$list['order_sku'][$i]['id'],'order_id'=>$list['id'],'order_status'=>$list['order_status'])); ?>
        <?php echo CHtml::beginForm(Yii::app()->createUrl('Dmall/Order/RefundMsg'),'post');?>
        <div class="title">
            <h5>退货信息</h5>
            <a href="javascript:;" class="close">×</a>
        </div>
        <div class="filed">
            <span class="label">手机号</span>
            <span class="text"><input type="tel" name="Refund_tel" class="txt" placeholder="请输入手机号"></span>
        </div>
        <div class="filed">
            <span class="label">物流快递</span>
            <span class="text">
                <select name="Refund_logistics">
                    <option>请选择</option>
                    <?php foreach($logistics as $key=>$value){?>
                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                    <?php }?>
                </select>
            </span>
        </div>
        <div class="filed">
            <span class="label">订单号</span>
            <span class="text">
                <input type="tel" name="Refund_ordernum" class="txt" placeholder="请输入订单号">
            </span>
        </div>
        <div class="filed">
            <span class="label">备注</span>
            <span class="text">
                <textarea name="Refund_remark"></textarea>
            </span>
        </div>
        <!--错误提示-->
        <?php if(Yii::app()->user->hasFlash('error')){ ?>
            <font color="red"><?php echo Yii::app()->user->getFlash('error');?></font>
        <?php }?>
        <input style="display: none" name="skuid" value="">
        <input style="display: none" name="orderid" value="">
        <input style="display: none" name="orderstatus" value="">

        <div class="btn"><button type="submit">提交</button></div>
        <?php echo CHtml::endForm();?>
    </div>
</div>
</body>

<script type="text/javascript">
    function applyRefund(order_skuid,order_id,order_status)
    {
        if(order_skuid!=null&&order_id!=null&&order_status!=null)
        {
            $('#popShadow').slideToggle();
            $('#popRefund').slideToggle();
            $('input[name="skuid"]').val(order_skuid);
            $('input[name="orderid"]').val(order_id);
            $('input[name="orderstatus"]').val(order_status);

        }
    }

    $('.close').click(function(){
        $('#popShadow').slideToggle();
        $('#popRefund').slideToggle();
    });
</script>