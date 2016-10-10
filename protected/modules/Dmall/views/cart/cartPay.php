<script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo USER_STATIC_STYLES?>mobiscroll.custom-2.5.0.min.css">
<script src="<?php echo USER_STATIC_JS?>mobiscroll.custom-2.5.0.min.js" type="text/javascript"></script>
<head>
	<title>订单结算</title>
</head>
<body>
	<div class="cartWrap cartPay cartPayV">
	<?php echo CHtml::beginForm(Yii::app()->createUrl('Dmall/Order/CreateOrder',array('sku_id' => $skuid,'num' => $num)),'post',array('name' => 'buy','data-ajax'=>"false"));?>   
    <input style="display: none" value="<?php echo $encrypt_id?>" name="encrypt_id">
    <div class="addrItem clearfix"> 
        <div class="checkboxCom checkboxComCur"></div>  
       	<div class="addrItemL">
            <div class="block clearfix">
               	<div class="left">
                   	<span class="name"><em>游玩人：</em><input type="text" class="txt" name="name"> </span>
               	</div>
                <?php foreach ($sku as $k => $v){?>
               	<span class="right"><em>手机号：</em><input type="text" class="txt" name="phone" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : $v -> account?>"> </span>
           	<div class="end">
                    <span class="name"><em>游玩日期：</em>
                        <?php if($v -> third_party_source == SHOP_PRODUCT_THIRED_TIANSHI) { ?>
                        <?php if($v -> use_time_type == DSHOP_TIME_TYPE) { ?>
                        <input type="text" data-role="datebox"   id="txtBirthday" name="txtBirthday" class="txt"/> 
                        <?php } ?>
                        <?php if($v -> use_time_type == DSHOP_TIME_TYPE_DAY ) { ?>
                        <?php echo date('Y.m.d')?>起<?php echo $v -> date_num?>天有效
                        <?php } } ?>
                        <?php if($v -> third_party_source == SHOP_PRODUCT_THIRED_ZHIYOUBAO) { ?>
                        <input type="text" data-role="datebox"   id="txtBirthday" name="txtBirthday" class="txt"/> 
                        <?php } ?>
                    </span>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>   
        <script type="text/javascript">
                $(function () {
                $('input:jqmData(role="datebox")').mobiscroll().date();
                });
        </script>
    <div class="itemWrap">
    	<?php foreach ($sku as $k => $v){?>
        <div class="itemCom clearfix"> 
        	<a href="<?php echo Yii::app()->createUrl('Dmall/Commodity/CommodityDetails',array('id'=>$v -> product_id, 'encrypt_id' => $encrypt_id))?>" data-ajax="false">
            	<div class="htcon">
                	<img src="<?php $img_arr = explode(';', $v -> img);echo IMG_GJ_80_LIST.$img_arr[0]?>">
            	</div>
            	<div class="text">
                	<div class="title">
                    	<span class="r">¥ <strong id="shopprice" class="cPrice"><?php echo $v -> price?></strong></span>
                    	<h3><?php echo $v -> product_name?></h3>
                	</div>
                	<div class="block">
                    	<div class="attr"><?php echo str_replace(',', ' ', $v -> name)?></div>
                    	<div class="num_select">&nbsp;&nbsp;× <?php echo $num[$k]?>&nbsp;&nbsp;</div>
                	</div>
            	</div>
          	</a>
        </div>
  		<?php }?>
        <dl class="freight clearfix">
            <dt>运费 &nbsp;<span style="color: #6D8EFF;font-size: smaller;">电子票，自动出票</span></dt>
            <dd>¥ <?php echo number_format(0,2)?></dd>
        </dl>
        <?php if (!empty($optimal['title'])) { ?>
        <dl class="freight cartCouponed clearfix">
        	<dt><?php echo $optimal['title']?></dt>
        	<dd><?php echo $optimal['value']?><i class="arrow"></i></dd>
        <?php }else { ?>
        <dl class="freight cartCoupon clearfix">
        	<dt>东钱湖景区优惠券</dt>
        	<dd><div class="arrow"></div> </dd>
        <?php }?>
        	<input type="hidden" name="coupons_id" value="<?php echo $optimal['id']?>">
        </dl>
        <dl class="freight clearfix">
            <textarea placeholder="给卖家留言..." name="remark"></textarea>
        </dl>
        <dl class="freight totalP clearfix">
            <dt>合计</dt>
            <dd>¥ <?php echo number_format($total,2)?></dd>
        </dl>
    </div>

    <div class="cartTotal1">
        <p id="sumprice1">¥ <?php echo number_format($total,2)?> + ¥ <?php echo number_format(0,2)?>运费</p>
        <p id="sumprice2" class="color">需付：¥ <?php echo number_format($optimal['pay']+0,2)?></p>
    </div>

    <div class="btnWrap n_btnWrap"><input type="button" value="去结算" class="btn_red" data-ajax="false"></div>

    <article class="bottom" style="display: none">
        <img src="<?php echo USER_STATIC_IMAGES;?>user/logo-bottom.png">
    </article>
    <!--main end-->
    <input type="hidden" value="<?php echo $is_cart?>" name="is_cart"/>
    <?php echo CHtml::endForm();?>  
	</div>
	
	<div id="popShadow" style="display:none"></div>
    <div class="pop popCoupon" style="display:none">
        <div class="pop_con">
            <div class="filed">
                <div class="title1">选择优惠券</div>
                <span class="close"><a href="javascript:;" id="pop_close">×</a></span>
            </div>
            <div class="itemWrap">
            <?php if (!empty($coupons_arr)) {
            	foreach ($coupons_arr as $k => $v) { ?>
	            	<div class="itemCom clearfix">
		                <div class="<?php echo $v['id'] == $optimal['id'] ? 'checkboxCom checkboxComCur' : 'checkboxCom';?>">
		                    <input type="checkbox" value="<?php echo $v['id'];?>" class="c_choose">
		                    <label for="checkbox01">&nbsp;</label>
		                </div>
		                <div class="text">
		                    <div class="title">
		                        <span class="r"><em class="orange"> <?php echo $v['value'];?></em></span>
		                        <h3><span class="coupons_title"><?php echo $v['title'];?></span><span class="time">使用日期：<?php echo $v['date']?></span></h3>
		                    </div>
		                </div>
		            </div>
            	<?php }
            }else { ?>
            	<div style="text-align: center">无可用优惠券</div>
            <?php }?>
            </div>
        </div>
    </div>
    
</body>
<script type="text/javascript">

 	//点击结算
 	$('.btn_red').click(function(){
 	 	var name = $('input[name = name]').val();
 	 	var phone = $('input[name = phone]').val();
                <?php foreach ($sku as $k => $v){?>
                    <?php if($v -> third_party_source == SHOP_PRODUCT_THIRED_TIANSHI) { ?>
                    <?php if($v -> use_time_type == DSHOP_TIME_TYPE_DAY ) { ?>
                        if(name == ''){
                            alert("请输入游玩人"); 
                        }else{
                            if(phone == ''){
                                   alert("请输入收货人手机号"); 
                            }else{                               
                                $('form[name=buy]').submit();                                
                            }
                        }       
                    <?php } ?>
                    <?php if($v -> use_time_type == DSHOP_TIME_TYPE) { ?>
                        var txtBirthday = $('input[name = txtBirthday]').val();
                        if(name == ''){
                            alert("请输入收货人"); 
                        }else{
                            if(phone == ''){
                                   alert("请输入收货人手机号"); 
                            }else{
                                if(txtBirthday == '') {
                                    alert('请选择游玩日期');
                                }else{
                                   $('form[name=buy]').submit();
                                }
                            }
                        }                        
                    <?php }} ?>
                    <?php if($v -> third_party_source == SHOP_PRODUCT_THIRED_ZHIYOUBAO) { ?>
                        var txtBirthday = $('input[name = txtBirthday]').val();
                        if(name == ''){
                            alert("请输入收货人"); 
                        }else{
                            if(phone == ''){
                                   alert("请输入收货人手机号"); 
                            }else{
                                if(txtBirthday == '') {
                                    alert('请选择游玩日期');
                                }else{
                                   $('form[name=buy]').submit();
                                }
                            }
                        }
                    <?php } ?>
                <?php } ?> 	 	
 	 });

	 $(".cartCoupon").click(function() {
		 //显示优惠券
		 $("#popShadow").fadeIn('normal', function() {
			 $(".popCoupon").slideDown();
		 });
	 });
	 $(".cartCouponed").click(function() {
		 //显示优惠券
		 $("#popShadow").fadeIn('normal', function() {
			 $(".popCoupon").slideDown();
		 });
	 });
	 $("#pop_close").click(function() {
		 //隐藏优惠券
		 $(".popCoupon").slideUp('normal', function() {
			 $("#popShadow").hide();
		 });
	 });
	 $("#popShadow").click(function() {
		 //隐藏优惠券
		 $(".popCoupon").slideUp('normal', function() {
			 $("#popShadow").hide();
		 });
	 });
	 $(".pop_con .itemCom").click(function() {
		 var obj = $(this).find(".checkboxCom");
		 var uid = obj.find(".c_choose").val(); //优惠券id
		 var status = obj.hasClass("checkboxComCur"); //选中状态
		 if(status) {
			 uid = 0; //取消选中，则表示计算实付金额时使用的优惠券为空
		 }
		 var money = '<?php echo $total?>';
		 getPay(uid, money, function() {
			 var title = obj.next().find(".coupons_title").html(); //优惠券标题
			 var value = obj.next().find(".orange").html(); //价值
			 if(status) {
				 obj.attr('class', 'checkboxCom'); //设置未选中状态
				 $(".cartCouponed").find('dt').html('东钱湖景区优惠券');
				 $(".cartCouponed").find('dd').html('<div class="arrow"></div>');
				 $(".cartCouponed").find('input').val('');
				 $(".cartCouponed").attr('class', 'freight cartCoupon clearfix');
			 }else {
				 //取消其他优惠券选中状态
				 if($(".checkboxComCur").length > 0) {
					 $(".checkboxComCur").attr('class', 'checkboxCom'); //设置未选中状态
				 }
				 obj.attr('class', 'checkboxCom checkboxComCur'); //设置选中状态
				 $(".cartCoupon").find('dt').html(title);
				 $(".cartCoupon").find('dd').html(value+'<i class="arrow"></i>');
				 $(".cartCoupon").find('input').val(uid);
				 $(".cartCoupon").attr('class', 'freight cartCouponed clearfix');
			 }
			 //隐藏优惠券
			 $(".popCoupon").slideUp('normal', function() {
				 $("#popShadow").hide();
			 });
		 });
	 });

	 function getPay(list, money, callback) {
			//更新金额
			$.ajax({
	            url: '<?php echo(Yii::app()->createUrl('Dmall/cart/updateCoupons'));?>',
	            data: {list: list, money: money},
	            dataType: 'json',
	            success: function (data) {
	                if(data.error == 'success') {
		                var pay = data.need;
		                $("#sumprice2").html('需付：¥ '+pay.toFixed(2));
		                callback();
	                }else {
		                alert(data.errMsg);
	                }
	            }
	        });
	 }
	 

</script>