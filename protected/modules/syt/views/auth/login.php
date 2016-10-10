<body class="pay-login">
<div class="header clearfix">
	<div class="top">
      <div class="logo"><img src="<?php echo SYT_STATIC_IMAGES?>logo.png"></div>
     
      <div class="header_right">
      	<ul>
      		<li>客服电话：400-882-9998</li>
        </ul>
	</div>
   </div>
</div>
<div class="pay-con">
	<div class="left">
    	<img src="<?php echo SYT_STATIC_IMAGES?>banner.png">
    </div>
    <div class="right">
    	<?php echo CHtml::beginForm()?>
        	<h2>登录收银台</h2>
            <div class="error">
            <?php if (Yii::app()->user->hasFlash('error')) {
            	echo Yii::app()->user->getFlash('error');
            }?>
            </div>
           	<span class="name"><input type="text" placeholder="请输入账号" class="txt" name="account" value="<?php echo isset($_POST['account']) ? $_POST['account'] : '';?>" AUTOCOMPLETE="off"></span>
            <span class="sales"><input type="text" placeholder="请输入密码" class="txt" name="password" onpaste="return false;" AUTOCOMPLETE="off" onfocus="this.type='password'"></span>
<!--             <span class="resales"><a href="">忘记密码？</a></span> -->
            <span class="btn"><input type="submit" value="登录" class="btn_com"></span>
        <?php echo CHtml::endForm()?>
  </div>
</div>