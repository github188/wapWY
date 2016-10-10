<title>会员注册</title>
<?php echo CHtml::beginForm(Yii::app()->createUrl('mobile/property/User/Sign', array('encrypt_id' => $encrypt_id)), 'post'); ?>
<div class="prop_wrap prop_sign_wrap">
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('name', isset($_POST['name']) ? $_POST['name'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的真实姓名')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::numberField('mobile_phone', isset($_POST['mobile_phone']) ? $_POST['mobile_phone'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的手机号')); ?>
            </div>
        </div>
        <div class="weui_cell weui_vcode">
            <div class="weui_cell_hd"><label class="weui_label">验证码 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::numberField('verification_code', isset($_POST['verification_code']) ? $_POST['verification_code'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的验证码')); ?>
            </div>
            <div class="weui_cell_ft">
                <a href="javascript:;" onclick="onMobileMsg()" class="prop_vcode">获取验证码</a>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">密码 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::passwordField('pwd', isset($_POST['pwd']) ? $_POST['pwd'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的密码')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('access_control_card_no', isset($_POST['access_control_card_no']) ? $_POST['access_control_card_no'] : '', array('class' => 'weui_input', 'placeholder' => '请输入您的门禁卡号')); ?>
            </div>
        </div>
    </div>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">类型 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <select class="weui_select prop_select_arrow" name="type" id="type">
                    <option value=<?php echo PROPRIETOR_TYPE_OWNER ?>>业主</option>
                    <option value=<?php echo PROPRIETOR_TYPE_TENEMENT ?>>租户</option>
                </select>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">小区 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <select class="weui_select prop_select_arrow" name="community_id" id="community_id">
                    <?php if (!empty($community_list)){?>
                        <?php foreach ($community_list as $k => $v){?>
                    <option value=<?php echo $v['id'] ?>><?php echo $v['name'] ?></option>
                        <?php }?>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">楼号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('building_number', isset($_POST['building_number']) ? $_POST['building_number'] : '', array('class' => 'weui_input', 'placeholder' => '如：2号楼或2')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">房间 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('room_number', isset($_POST['room_number']) ? $_POST['room_number'] : '', array('class' => 'weui_input', 'placeholder' => '如：502室或502')); ?>
            </div>
        </div>
    </div>
    <div class="weui_btn_area">
        <a class="weui_btn weui_btn_primary" href="javascript:" id="login_btn">下一步</a>
    </div>
</div>
<div class="prop_wrap prop_family prop_family_add" style="display: none;">
    <div class="weui_cells_title">最少添加一个，最多添加三个</div>
    <div class="weui_cells weui_cells_form" >
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('member_name', isset($_POST['member_name']) ? $_POST['member_name'] : '', array('class' => 'weui_input', 'placeholder' => '请输入家庭成员真实姓名')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::numberField('member_phone', isset($_POST['member_phone']) ? $_POST['member_phone'] : '', array('class' => 'weui_input', 'placeholder' => '请输入家庭成员手机号')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('member_access_control_card_no', isset($_POST['member_access_control_card_no']) ? $_POST['member_access_control_card_no'] : '', array('class' => 'weui_input', 'placeholder' => '请输入家庭成员门禁卡号')); ?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">与业主关系 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <?php echo CHtml::textField('relationship', isset($_POST['relationship']) ? $_POST['relationship'] : '', array('class' => 'weui_input', 'placeholder' => '如：父亲')); ?>
            </div>
        </div>
    </div>
    <div class="weui_cell">
        <span class="weui_cells_title add-btn" id="addFamily"><span class="iconAdd"></span> 增添家庭成员</span>
    </div>

    <div class="weui_btn_area">
        <?php echo CHtml::submitButton('注册', array('class' => 'weui_btn weui_btn_primary','id' => 'login_btn', 'encrypt_id' => $encrypt_id)); ?>
    </div>
</div>
<?php echo CHtml::endForm(); ?>

<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php }?>

<script>
    $(function () {
        $(".weui_btn_primary").click(function () {
            $(".prop_sign_wrap").hide();
            $(".prop_family").show();
        });
    })
</script>

<script language="JavaScript">
    var mins = 59;
    var intervalid;
    function ctrlTime(){
        if(mins == 0){
            clearInterval(intervalid);
            $('.prop_vcode').html('获取验证码');
            $(".prop_vcode").attr("onclick", 'onMobileMsg()');
            mins = 59;
            return;
        }
        $('.prop_vcode').html(mins+'秒后重新获取');
        mins--;
    }

    function onMobileMsg(){
        var mobile = $('input[name = mobile_phone]').val();
        var reg = /^(13|15|18|14|17)\d{9}$/;
        if(!reg.test(mobile)) {
            alert('请填写正确的手机号!');
        }else{
            $.ajax({
                url : '<?php echo Yii::app()->createUrl('mobile/property/User/sendMsgPassword');?>?' + new Date().getTime(),
                data : {mobile : mobile, check : 'yes',encrypt_id:'<?php echo $encrypt_id?>'},
                dataType: "json",
                type : 'post',
                async : false,
                success : function(res){
                    if(res.status == '<?php echo ERROR_NONE?>'){
                        intervalid = setInterval("ctrlTime()", 1000);
                        $(".prop_vcode").removeAttr("onclick");
                    } else {
                        alert(res.errMsg);
                        $(".prop_vcode").attr("onclick", 'onMobileMsg()');
                    }
                }
            });
        }
    }
</script>

<script type="text/javascript">
    var tmp = 1;
    //添加家庭成员
    $("#addFamily").click(function(){
        var obj = '<div class="weui_cells weui_cells_form"><div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">姓名 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="text" name="member_name'+tmp+'" placeholder="请输入家庭成员真实姓名"></div></div> <div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">手机号 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="number" name="member_phone'+tmp+'" maxlength="11" placeholder="请输入家庭成员手机号"></div></div> <div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">门禁卡号 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="text" name="member_access_control_card_no'+tmp+'" placeholder="请输入家庭成员门禁卡号"></div></div> <div class="weui_cell"><div class="weui_cell_hd"><label class="weui_label">与业主关系 <em class="cRed">*</em></label></div><div class="weui_cell_bd weui_cell_primary"><input class="weui_input" type="text" name="relationship'+tmp+'" placeholder="如：父亲"></div></div></div>';
        if($('.prop_family_add .weui_cells_form').length < 3){
            //在它父级对象的前面插入元素
            $(this).parent().before(obj);
            tmp ++;
        }else{
            $.alert("最多只能添加三位家庭成员", "提示");
        }
    })

</script>
