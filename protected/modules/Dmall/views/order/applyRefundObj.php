<head>
	<title>订单退款</title>
</head>
<body>
<section class="refund">
	<article class="proInfo">
    	<div class="filed">
        	<span class="label">商品名称</span>
            <span class="text"><?php echo $order_sku['product_name']; ?></span>
        </div>
        <div class="filed">
        	<span class="label">票张数</span>
            <span class="text"> <?php echo $list['num'] ?></span>
        </div>
        <div class="filed">
        	<span class="label">订单编号</span>
            <span class="text"><?php echo $list['order_no'] ?></span>
        </div>
        <div class="filed">
        	<span class="label">交易时间</span>
            <span class="text"><?php echo date('Y.m.d H:i:s',strtotime($list['create_time']));?></span>
        </div>
          <?php echo CHtml::beginForm(); ?>
        <div class="more clearfix">
            <span class="name">处理方式</span>
            <span class="text">
                <?php echo CHtml::dropDownList('OrderSku[status]', isset($_POST['OrderSku']['status'])?$_POST['OrderSku']['status']:'',$apply_type); ?>
            </span>
            <?php if(Yii::app()->user->hasFlash('status_error')){ ?>
            <font color="red"><?php echo Yii::app()->user->getFlash('status_error');?></font>
            <?php }?>
        </div>
      
        <div class="more clearfix">
            <span class="name">退款原因</span>
            <span class="text">                
                <?php echo CHtml::dropDownList('OrderSku[refund_reason]', isset($_POST['OrderSku']['refund_reason'])?$_POST['OrderSku']['refund_reason']:'', $GLOBALS['REFUND_REASON'],array('prompt'=>'请选择退款原因')); ?>
            </span>
             <?php if(Yii::app()->user->hasFlash('refund_reason_error')){ ?>
            <font color="red"><?php echo Yii::app()->user->getFlash('refund_reason_error');?></font>
            <?php }?>
        </div>
       <div class="more clearfix">
        	<span class="name">退票张数</span>
            <span class="text">
                <select name="ticket_num">
                    <option value=""><?php echo '请选择票数'?></option>
                    <?php for($i=1;$i<=$list['num'];$i++) { ?>
                    <option value="<?php echo $i?>"><?php echo $i?></option>
                    <?php } ?>
                </select>
             <?php //echo CHtml::textField('OrderSku[refund_money]',isset($_POST['OrderSku']['refund_money'])?$_POST['OrderSku']['refund_money']:'',array('placeholder'=>'')); ?>
            </span>
            <?php if(Yii::app()->user->hasFlash('refund_money_error')){ ?>
            <font color="red"><?php echo Yii::app()->user->getFlash('refund_money_error');?></font>
            <?php }?>
        </div>
    </article>
    <article class="proInfo mInfo">
       <div class="more clearfix">
        	<span class="name">手机号码</span>
            <span class="text">
             <?php echo CHtml::textField('OrderSku[refund_tel]',isset($_POST['OrderSku']['refund_tel'])?$_POST['OrderSku']['refund_tel']:'',array('placeholder'=>'请填写您的联系方式')); ?>
            </span>
            <?php if(Yii::app()->user->hasFlash('refund_tel_error')){ ?>
            <font color="red"><?php echo Yii::app()->user->getFlash('refund_tel_error');?></font>
            <?php }?>
        </div>
        <div class="more clearfix">
        	<span class="name">备注信息</span>
            <span class="text">
            <?php echo CHtml::textArea('OrderSku[refund_remark]',isset($_POST['OrderSku']['refund_remark'])?$_POST['OrderSku']['refund_remark']:'',array('placeholder'=>'最多可以填写200个字','style'=>'width:208px')); ?>
            </span>
        </div>
<!--        <div class="more clearfix end" id="upload">
        	<span class="name">图片举证</span>
            <span class="text pic">最多可以添加5张图片</span>
        </div>-->
        <div class="more clearfix end">
        	<span class="name"></span>
            <span class="text"></span>
        </div>
    </article>
    <article class="bottom">
    	<input type="submit" class="btn_com" value="确定">
    </article>
    <?php echo CHtml::endForm(); ?>
</section> 
</body>

<script type="text/javascript">
</script>