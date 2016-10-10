<div class="login">
    <?php echo CHtml::beginForm();?>
        <input type="text" value=""  name="account" class="txtComm tel" placeholder="手机号">
        <input type="text" value="" name="pwd" class="txtComm pwd" placeholder="密码">
        <input type="submit" value="登 录"  class="btnComm">
        <div class="remarks red"><a href="#" class="forget">忘记密码？</a><a href="#" class="reg">免费注册</a></div>
    <?php echo CHtml::endForm();?>
</div>

<!--<div class="pop_login">手机号不能为空</div>-->