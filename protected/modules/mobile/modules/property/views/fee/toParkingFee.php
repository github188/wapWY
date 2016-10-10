<div class="prop_wrap prop_park">
    <?php echo CHtml::beginForm(Yii::app()->createUrl('mobile/property/fee/toParkingFee', array('encrypt_id' => $encrypt_id, 'car_id' => $car_id)),'post', array('id' => 'createForm'));?>
    <div class="weui_cells weui_cells_form">
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">姓名</label></div>
            <div class="weui_input">
                <?php echo User::model() -> find('id  = :id', array(':id' => $user_id)) -> name;?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
            <div class="weui_input">
                <?php echo User::model() -> find('id  = :id', array(':id' => $user_id)) -> account;?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">地址</label></div>
            <div class="weui_input">
                <?php echo $address;?>
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">区域 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <label class="mr15"><input class="js-divide-btn" type="radio" value="<?php echo  COMMUNITY_PARKING_FEE_SET_OVERGROUND?>" <?php echo isset($_POST['area_type']) && $_POST['area_type'] && $_POST['area_type'] == COMMUNITY_PARKING_FEE_SET_OVERGROUND ? 'checked' : '';?> checked name="area_type"> 地上</label>
                <label class="mr15"><input class="js-undivide-btn" type="radio" value="<?php echo COMMUNITY_PARKING_FEE_SET_UNDERGROUND?>" <?php echo isset($_POST['area_type']) && $_POST['area_type'] && $_POST['area_type'] == COMMUNITY_PARKING_FEE_SET_UNDERGROUND ? 'checked' : '';?> name="area_type"> 地下</label>
            </div>
            <input style="display: none" value="<?php echo COMMUNITY_PARKING_FEE_SET_OVERGROUND ?>" name="type" id="type">
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd">
                <label class="weui_label">日期 <em class="cRed">*</em></label>
            </div>
            <div class="weui_cell_bd weui_cell_primary js-divide-wrapper">
<!--                <input class="weui_input width_auto" type="date" value="">-->
<!--                <input class="weui_input width_auto input_box" size="4" onchange="checkTime(this,'3')" maxlength="5" type="number" id="parking_month_num" name="parking_month_num" value="--><?php //echo isset($_POST['parking_month_num']) && $_POST['parking_month_num'] ? $_POST['parking_month_num'] : ''?><!--"> <label class="cGray">个月</label>-->
                <a href="#" class="input_box" onclick="checkTime(this,'1','1')">一个月</a>
                <a href="#" class="input_box" onclick="checkTime(this,'2','1')">半年</a>
                <a href="#" class="input_box" onclick="checkTime(this,'3','1')">一年</a>
            </div>
            <div class="weui_cell_bd weui_cell_primary js-undivide-wrapper" style="display: none;">
                <a href="#" class="input_box" onclick="checkTime(this,'2','2')">半年</a>
                <a href="#" class="input_box" onclick="checkTime(this,'3','2')">一年</a>
                <input id="parking_month_num" name="parking_month_num" style="display: none">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">停车费</label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <span class="weui_input" id="parking_fee"></span>
                <input id="parking_fee_money" name="parking_fee_money" style="display: none">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">车辆品牌 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="car_brand" id="car_brand" value="<?php echo !empty($car_info['car_brand']) ? $car_info['car_brand'] : '';?>" placeholder="请输入车辆品牌...">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">车牌号码 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary">
                <input class="weui_input" type="text" name="car_no" id="car_no" value="<?php echo !empty($car_info['car_no']) ? $car_info['car_no'] : '';?>" placeholder="请输入车牌号...">
            </div>
        </div>
        <div class="weui_cell">
            <div class="weui_cell_hd"><label class="weui_label">车前照 <em class="cRed">*</em></label></div>
            <div class="weui_cell_bd weui_cell_primary prop_pic_upload">
                <div class="prop_pic_upload_hd">
                    <div class="prop_pic_upload_hd_l">请上传图片<a href="#" id="addImg" class="iconAdd ml15"></a> </div>
                </div>
                <ul class="prop_pic_upload_bd">
                    <input id="js_img_url1" name="img1" hidden>
                    <input id="js_img_url2" name="img2" hidden>
                    <input id="js_img_url3" name="img3" hidden>
                    <?php $car_info = json_decode($car_info['car_img'])?>
                    <li><img id="img1" src="<?php echo !empty($car_info[0]) ? (IMG_BASE_PATH).'wy_images/source/'.$car_info[0] : '';?>"><a href="#" class="deleteImg">删除</a> </li>
                    <li><img id="img2" src="<?php echo !empty($car_info[1]) ? (IMG_BASE_PATH).'wy_images/source/'.$car_info[1] : '';?>"><a href="#" class="deleteImg">删除</a> </li>
                    <li><img id="img3" src="<?php echo !empty($car_info[2]) ? (IMG_BASE_PATH).'wy_images/source/'.$car_info[2] : '';?>"><a href="#" class="deleteImg">删除</a> </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="tabbar_pay_55"></div>
    <div class="tabbar_pay flex">
        <div class="flex-box3 tabbar_pay_l">需要支付：<span class="cOrange" id="sum_fee"></span></div>
<!--        <button class="weui_btn weui_btn_primary" type="subimt" id="login_btn">提交</button>-->
        <div class="flex-box tabbar_pay_r"><input type="button" onclick="checkIn()" class="weui_btn" value="确认支付"> </div>
    </div>
    <?php echo CHtml::endForm();?>
</div>

<script>
    //赋值
    var over_one_month_fee = '<?php echo isset($fee_type['over_day_price']) ? $fee_type['over_day_price'] : '0';?>';
    var over_half_year_fee = '<?php echo isset($fee_type['over_half_year_price']) ? $fee_type['over_half_year_price'] : '0';?>';
    var over_year_fee = '<?php echo isset($fee_type['over_year_price']) ? $fee_type['over_year_price'] : '0';?>';
    var under_half_year_fee = '<?php echo isset($fee_type['under_half_year_price']) ? $fee_type['under_half_year_price'] : '0';?>';
    var under_year_fee = '<?php echo isset($fee_type['under_year_price']) ? $fee_type['under_year_price'] : '0';?>';
    var car_img1 = '<?php echo isset($car_info['car_img'][0]) ? $car_info['car_img'][0] : '';?>';
    var car_img2 = '<?php echo isset($car_info['car_img'][1]) ? $car_info['car_img'][1]: '';?>';
    var car_img3 = '<?php echo isset($car_info['car_img'][2]) ? $car_info['car_img'][2] : '';?>';
    $("input[name='img1']").val(car_img1);
    $("input[name='img2']").val(car_img2);
    $("input[name='img3']").val(car_img3);

    $(function () {
        //停车费分区域
        $(".js-divide-btn").click(function () {
            $(".js-undivide-wrapper").hide();
            $(".js-divide-wrapper").show();
            $("#type").val(1);
        });
        $(".js-undivide-btn").click(function () {
            $(".js-undivide-wrapper").show();
            $(".js-divide-wrapper").hide();
            $("#type").val(2);
        })
    })

    //计算自定义费用总额
    function checkTime(obj,dayType,type){
        $(obj).siblings().removeClass('input_box_green');
        if (dayType == 1 || dayType == 2 || dayType == 3){
            $(obj).addClass('input_box_green');
        }
        if (dayType == 1) {
            var sum = over_one_month_fee;
            $("#parking_fee").text("￥" + sum);
            $("#sum_fee").text("￥" + sum);
            $("#parking_fee_money").val(sum);
            $("#parking_month_num").val(1);
        }
        if (dayType == 2) {
            if(type==1){
                var sum = over_half_year_fee;
                $("#parking_fee").text("￥" + sum);
                $("#sum_fee").text("￥" + sum);
                $("#parking_fee_money").val(sum);
            }else{
                var sum = under_half_year_fee;
                $("#parking_fee").text("￥" + sum);
                $("#sum_fee").text("￥" + sum);
                $("#parking_fee_money").val(sum);
            }
            $("#parking_month_num").val(6);
        }
        if (dayType == 3) {
            if(type==1){
                var sum = over_year_fee;
                $("#parking_fee").text("￥" + sum);
                $("#sum_fee").text("￥" + sum);
                $("#parking_fee_money").val(sum);
            }else{
                var sum = under_year_fee;
                $("#parking_fee").text("￥" + sum);
                $("#sum_fee").text("￥" + sum);
                $("#parking_fee_money").val(sum);
            }
            $("#parking_month_num").val(12);
        }
    }


    //JS验证表单
    function checkIn() {
        var parking_month_num = $("#parking_month_num").val();
        var car_brand = $("#car_brand").val();
        var car_no = $("#car_no").val();
        if (parking_month_num == '' && car_brand == '' && car_no == '') {
            $.alert("请填写完整的信息", "提示");
        }
        if (parking_month_num == '' && car_brand != '' && car_no != '') {
            $.alert("缴费月数未填写", "提示");
        }
        if (parking_month_num == '' && car_brand != '' && car_no == '') {
            $.alert("请填写缴费月数及车牌号", "提示");
        }
        if (parking_month_num == '' && car_brand == '' && car_no != '') {
            $.alert("请填写缴费月数及车辆品牌", "提示");
        }
        if (parking_month_num != '' && car_brand == '' && car_no == '') {
            $.alert("请填写车辆品牌及车牌号", "提示");
        }
        if (parking_month_num != '' && car_brand != '' && car_no == '') {
            $.alert("车牌号未填写", "提示");
        }

        if (parking_month_num != '' && car_brand == '' && car_no != '') {
            $.alert("请填写车辆品牌", "提示");
        }
        if (parking_month_num != '' && car_brand != '' && car_no != '') {
            $("#createForm").submit();
        }
    }

    $('#addImg').Huploadify({
        auto:true,
        fileTypeExts:'<?php echo UPLOAD_IMG_TYPE; ?>',
        formData: { 'folder': 'wy_images'}, //提交给服务器端的参数
        uploader: '<?php echo UPLOAD_TO_PATH ?>',// 服务器处理地址
        buttonText : '+',
        onUploadComplete:function(file, data, response) {
            onUploadSuccess(file, data, response);
        }
    });

    var onUploadSuccess = function(file, data, response){
        eval("var jsondata = " + data + ";");
        var fileName = jsondata['fileName'];

        var img1 = $("input[name='img1']").val();
        var img2 = $("input[name='img2']").val();
        var img3 = $("input[name='img3']").val();
        if (img1 == '') {
            $("#img1").attr("src", '<?php echo(IMG_BASE_PATH)?>wy_images/source/' + fileName).parent().show();
            $('#js_img_url1').val(fileName)
        } else if(img2 == '') {
            $("#img2").attr("src", '<?php echo(IMG_BASE_PATH)?>wy_images/source/' + fileName).parent().show();
            $('#js_img_url2').val(fileName)
        } else  if(img3 == '') {
            $("#img3").attr("src", '<?php echo(IMG_BASE_PATH)?>wy_images/source/' + fileName).parent().show();
            $('#js_img_url3').val(fileName)
        }
        show();
    }

    $('.deleteImg').click(function(){
        $('input[name="'+ $(this).prev().attr("id") +'"]').val("");
        $(this).prev().attr("src","").parent().hide();
        $(this).parent().parent().append($(this).parent());
        $("#addImg").show();
    })

    function show() {
        var img1 = $("input[name='img1']").val();
        var img2 = $("input[name='img2']").val();
        var img3 = $("input[name='img3']").val();

        if((img1 != '') && (img2 != '') && (img3 != '')) {
            $("#addImg").hide();
        } else  {
            $("#addImg").show();
        }

        _hide();
    }

    function _hide(){
        $(".prop_pic_upload_bd img").each(function(){
            if($(this).attr("src") == ""){
                $(this).parent().hide();
            }else{
                $(this).parent().show()
            }
        })
    }

    $(function(){
        _hide();
    })

</script>