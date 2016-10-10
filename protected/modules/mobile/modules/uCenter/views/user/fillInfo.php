<head>
    <title>完善信息</title>
</head>

<body>
<?php $form = $this->beginWidget('CActiveForm', array(
    'enableAjaxValidation' => true,
    'id' => 'fillInfo',
    'htmlOptions' => array('name' => 'createForm'),
)); ?>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">姓名
                    <?php if (in_array(MERCHANT_AUTH_SET_NAME, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" placeholder="请输入姓名" name="User[name]">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">通讯地址
                    <?php if (in_array(MERCHANT_AUTH_SET_ADDRESS, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" placeholder="" id='city-picker' name="User[proCity]">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" placeholder="请输入详细地址" name="User[address]">
            </div>
        </div>
    </div>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">性别
                    <?php if (in_array(MERCHANT_AUTH_SET_SEX, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <label class="wq_radio" for="s11">
                    <input <?php if (in_array(MERCHANT_AUTH_SET_SEX, $info_arr)) { echo 'checked=checked'; } ?> type="radio" class="weui_check" name="User[sex]" id="s11" value="1">
                    <i class="weui_icon_checked"></i><em>男</em>
                </label>
                <label class="wq_radio" for="s12">
                    <input type="radio" class="weui_check" name="User[sex]" id="s12" value="2">
                    <i class="weui_icon_checked"></i><em>女</em>
                </label>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">生日
                    <?php if (in_array(MERCHANT_AUTH_SET_BIRTHDAY, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="date" id='birth-picker' name="User[birthday]">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">身份证号
                    <?php if (in_array(MERCHANT_AUTH_SET_ID, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="number" name="User[socialNumber]">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">邮箱
                    <?php if (in_array(MERCHANT_AUTH_SET_EMAIL, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="email" id='birth-picker' name="User[email]">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">婚姻状况
                    <?php if (in_array(MERCHANT_AUTH_SET_MARITAL_STATUS, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <label class="wq_radio" for="s13">
                    <input <?php if (in_array(MERCHANT_AUTH_SET_MARITAL_STATUS, $info_arr)) { echo 'checked=checked'; }?> type="radio" class="weui_check" name="User[marital]" id="s13" value="2">
                    <i class="weui_icon_checked"></i><em>是</em>
                </label>
                <label class="wq_radio" for="s14">
                    <input type="radio" class="weui_check" name="User[marital]" id="s14" value="1">
                    <i class="weui_icon_checked"></i><em>否</em>
                </label>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">工作
                    <?php if (in_array(MERCHANT_AUTH_SET_WORK, $info_arr)) { ?>
                        <em class="required">*</em>
                    <?php } ?>
                </label>
            </div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="User[work]">
            </div>
        </div>
    </div>
     <div class="weui_btn_area">
        <button type="submit" class="weui_btn weui_btn_primary" id="login_btn">提交</button>
    </div>

<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>alert('<?php echo Yii::app()->user->getFlash('error')?>')</script>
<?php } ?>

<?php $this->endWidget(); ?>
</body>

<script>
    //去除空格
    function Trim(str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }

    $('#login_btn').click(function () {
        <?php if (in_array(MERCHANT_AUTH_SET_NAME, $info_arr)) { ?>
        var name = Trim($('input[name = User\\[name\\]]').val());
        if (name == '') {
            alert('请填写姓名');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_ADDRESS, $info_arr)) { ?>
        var proCity = $('input[name = User\\[proCity\\]]').val();
        var address = Trim($('input[name = User\\[address\\]]').val());
        if (proCity == '' || address == '') {
            alert('请填写通讯地址');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_SEX, $info_arr)) { ?>
        var sex = $('input[name = User\\[sex\\]]').val();
        if (sex == '') {
            alert('请选择性别');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_BIRTHDAY, $info_arr)) { ?>
        var birthday = $('input[name = User\\[birthday\\]]').val();
        if (birthday == '') {
            alert('请选择生日');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_ID, $info_arr)) { ?>
        var socialNumber = $('input[name = User\\[socialNumber\\]]').val();
        if (socialNumber == '') {
            alert('请填写身份证号');
            return false;
        }
        if (socialNumber.length != 18) {
            alert('请填写正确的身份证号');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_EMAIL, $info_arr)) { ?>
        var email = Trim($('input[name = User\\[email\\]]').val());
        if (email == '') {
            alert('请填写邮箱');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_MARITAL_STATUS, $info_arr)) { ?>
        var marital = $('input[name = User\\[marital\\]]').val();
        if (marital == '') {
            alert('请选择婚姻状况');
            return false;
        }
        <?php } ?>

        <?php if (in_array(MERCHANT_AUTH_SET_WORK, $info_arr)) { ?>
        var work = Trim($('input[name = User\\[work\\]]').val());
        if (work == '') {
            alert('请填写工作');
            return false;
        }
        <?php } ?>
    });
</script>

</html>
