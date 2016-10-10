<div class="prop_wrap prop_repair_wrap">
    <?php echo CHtml::beginForm($this->createUrl('Repair/Repair',array('encrypt_id' => $encrypt_id)), 'post', array('id' => 'createForm'));?>
    <div class="repair_indoor">
        <div class="weui_cells weui_cells_form">
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>报修人</p>
                </div>
                <div class="weui_cell_ft">
                    <?php echo User::model() -> find('id  = :id', array(':id' => $user_id)) -> name;?>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>联系电话</p>
                </div>
                <div class="weui_cell_ft">
                    <?php echo User::model() -> find('id  = :id', array(':id' => $user_id)) -> account;?>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>小区</p>
                </div>
                <div class="weui_cell_ft">
                    <?php echo Proprietor::model() -> find('user_id = :user_id', array(':user_id' => $user_id)) -> community -> name;;?>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">报修区域 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <label class="mr15"><input type="radio" value="<?php echo  REPORT_REPAIR_RECORD_TYPE_INDOOR?>" <?php echo isset($_POST['area_type']) && $_POST['area_type'] && $_POST['area_type'] == REPORT_REPAIR_RECORD_TYPE_INDOOR ? 'checked' : '';?> checked name="area_type"> 室内</label>
                    <label class="mr15"><input type="radio" value="<?php echo REPORT_REPAIR_RECORD_TYPE_OUTDOOR?>" <?php echo isset($_POST['area_type']) && $_POST['area_type'] && $_POST['area_type'] == REPORT_REPAIR_RECORD_TYPE_OUTDOOR ? 'checked' : '';?> name="area_type"> 室外</label>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">报修地址 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <input class="weui_input" name="address" value="<?php echo isset($_POST['address']) && $_POST['address'] ? $_POST['address'] : '';?>" type="text" id="address" placeholder="请输入详细地址">
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">报修内容 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary">
                    <textarea class="weui_textarea" name="detail" id="detail" placeholder="报修区域房屋，水电，网络，其它..." rows="3"><?php echo isset($_POST['detail']) && $_POST['detail'] ? $_POST['detail'] : '';?></textarea>
                </div>
            </div>
            <div class="weui_cell">
                <div class="weui_cell_hd"><label class="weui_label">图片 <em class="cRed">*</em></label></div>
                <div class="weui_cell_bd weui_cell_primary prop_pic_upload">
                    <div class="prop_pic_upload_hd">
                        <div class="prop_pic_upload_hd_l" >请上传图片<a href="#" id="addImg" class="iconAdd ml15"></a> </div>
<!--                        <div class="prop_pic_upload_hd_r"><span class="sp0">3</span>/<span class="sp1">3</span></div>-->
                    </div>
                    <ul>
                        <div id="preview_image" class="prop_pic_upload_bd">
                            <input id="js_img_url1" name="img1" hidden>
                            <input id="js_img_url2" name="img2" hidden>
                            <input id="js_img_url3" name="img3" hidden>
                            <li><img class="preview-image-wrap" id="img1" src=""><a href="#" class="deleteImg">删除</a></li>
                            <li><img class="preview-image-wrap" id="img2" src=""><a href="#" class="deleteImg">删除</a></li>
                            <li><img class="preview-image-wrap" id="img3" src=""><a href="#" class="deleteImg">删除</a></li>
                        </div>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <div class="weui_btn_area login_area">
        <button class="weui_btn weui_btn_primary" type="button" onclick="checkIn()" id="login_btn">提交</button>
    </div>
    <?php echo CHtml::endForm();?>
    <!--底部固定菜单-->
    <div class="weui_tabbar_padding"></div>
    <div class="weui_tabbar prop_weui_tabbar">
        <a href="<?php echo $this->createUrl('Ucenter/Index', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item ">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">首页</p>
        </a>
        <a href="<?php echo $this->createUrl('Repair/Repair', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item weui_bar_item_on">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">小区报修</p>
        </a>
        <a href="<?php echo $this->createUrl('Ucenter/Center', array('encrypt_id' => $this->getEncryptId()));?>" class="weui_tabbar_item">
            <div class="weui_tabbar_icon"></div>
            <p class="weui_tabbar_label">个人中心</p>
        </a>
    </div>
</div>


<script>
    //JS验证表单
    function checkIn() {
        var address = $("#address").val();
        var detail = $("#detail").val();
        if (address == '' && detail == '') {
            alert("请填写报修地址及内容", "提示");
        }
        if (address == '' && detail != '') {
            alert("报修地址未填写", "提示");
        }
        if (detail == '' && address != '') {
            alert("报修内容未填写", "提示");
        }
        if (address != '' && detail != '') {
            $("#createForm").submit();
            alert("提交成功,您可前往个人中心-报修记录，查看进度","提示");
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