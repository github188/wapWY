/**
 * 在线店铺
 * onlineShop
 */
function sub() {
    var checkbox_sum = $('#checkbox_sum').val();
    var flag = false;
    for (var i = 0; i < checkbox_sum; i++) {
        if ($('#store' + i).is(":checked")) {
            flag = true;
        }
        $('#store' + i).removeAttr('disabled');
    }

    $('#selectAll').removeAttr('disabled');
    if ($('#if_book').is(':checked')) {
        if (flag)
            $('#sform').submit();
        else
            alert('请选择门店!');
    } else {
        $('#sform').submit();
    }
}

$(function () {
    $("#selectAll").click(function () {
        if ($(this).is(":checked")) {
            //全选
            var checkbox_sum = $('#checkbox_sum').val();
            for (var i = 0; i < checkbox_sum; i++) {
                $('#store' + i).prop('checked', true);
            }
        } else {
            //全不选
            var checkbox_sum = $('#checkbox_sum').val();
            for (var i = 0; i < checkbox_sum; i++) {
                $('#store' + i).prop('checked', false);
            }
        }
    });
});

function onlineshop_uploadImg(id, cover, cover_img, path, static_js, type, folder, list) {
    setTimeout(function () {
        $(function () {
            $('#' + id).uploadify({
                uploader: path,// 服务器处理地址
                swf: static_js + 'uploadify/uploadify.swf',
                buttonText: "点击上传",//按钮文字
                height: 34,  //按钮高度
                width: 82, //按钮宽度
                fileTypeExts: type,//允许的文件类型
                fileTypeDesc: "请选择图片文件", //文件说明
                formData: {'folder': folder}, //提交给服务器端的参数
                onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                    eval("var jsondata = " + data + ";");
                    var key = jsondata['key'];
                    var fileName = jsondata['fileName'];
                    $("input[name=" + cover + "]").val(fileName);
                    $("#" + cover_img).attr('src', list + fileName);

                }
            });
        });
    }, 10);

}

function onlineshop_uploadlogo(id, cover, cover_img, path, static_js, type, folder, list) {
    setTimeout(function () {
        $(function () {
            $('#' + id).uploadify({
                uploader: path,// 服务器处理地址
                swf: static_js + 'uploadify/uploadify.swf',
                buttonText: "点击上传logo",//按钮文字
                height: 34,  //按钮高度
                width: 82, //按钮宽度
                fileTypeExts: type,//允许的文件类型
                fileTypeDesc: "请选择图片文件", //文件说明
                formData: {'folder': folder}, //提交给服务器端的参数
                onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                    eval("var jsondata = " + data + ";");
                    var key = jsondata['key'];
                    var fileName = jsondata['fileName'];
                    $("input[name=" + cover + "]").val(fileName);
                    $("#" + cover_img).attr('src', list + fileName);

                }
            });
        });
    }, 10);

}

/**
 * photoSubclass
 */
function photoSubclass_upload(id, path, js, type, folder, thumb, list) {
    $(function () {
        $('#' + id).uploadify({
            onInit: function () {
                $(".uploadify-queue").hide();
            },//载入时触发，将flash设置到最小
            uploader: path,// 服务器处理地址
            swf: js + 'uploadify/uploadify.swf',
            buttonText: "上传图片",//按钮文字
            height: 34,  //按钮高度
            width: 82, //按钮宽度
            multi: true,
            fileSizeLimit: '3mb',
            fileTypeExts: type,//允许的文件类型
            fileTypeDesc: "请选择图片文件", //文件说明
            formData: {'folder': folder, 'thumb': thumb}, //提交给服务器端的参数
            onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                eval("var jsondata = " + data + ";");
                var key = jsondata['key'];
                var name = file.name;
                var fileName = jsondata['fileName'];
                var imgsrc = list + fileName;
                $('.hidden').prepend('<div class="item"><div class="img"><img src="' + imgsrc + '"></div><div class="text"><a href="javascript:;" class="del"></a><input type="hidden" name="imgname[]"  value="' + name + '"><input type="hidden" name="imgsrc[]"  value="' + fileName + '"></div></div>');
                if ($("input[name='imgsrc1']").val() == '') {
                    $("input[name='imgsrc1']").val(fileName);
                    //$("#upload_img1").html(fileName);
                    $("#img1").attr('src', list + fileName);
                    $("#htcon1").show();
                    $("#del1").show();
                }

            }
        });

    });
}


/**
 * groupPhotoList
 */
$(function () {
    $('#checkall').on('ifChecked', function (event) {
        var sum = $('#photo_sum').val();
        for (var i = 0; i < sum; i++) {
            $('#checkbox' + i).iCheck('check');
        }
    });
});

$(function () {
    $('#checkall').on('ifUnchecked', function (event) {
        var sum = $('#photo_sum').val();
        for (var i = 0; i < sum; i++) {
            $('#checkbox' + i).iCheck('uncheck');
        }
    });
});

$(function () {
    $('#showop').click(function () {
        $('#check').slideToggle();
        if (flag) {
            $('.checkbox').hide();
            flag = false;
        }
        else {
            $('.checkbox').show();
            flag = true;
        }
    });
});

function groupPhotoList_upload(id, path, js, type, folder, thumb, list) {
    $(function () {
        $('#' + id).uploadify({
            onInit: function () {
                $(".uploadify-queue").hide();
            },//载入时触发，将flash设置到最小
            uploader: path,// 服务器处理地址
            swf: js + 'uploadify/uploadify.swf',
            buttonText: "上传图片",//按钮文字
            height: 34,  //按钮高度
            width: 82, //按钮宽度
            multi: true,
            fileSizeLimit: '3mb',
            fileTypeExts: type,//允许的文件类型
            fileTypeDesc: "请选择图片文件", //文件说明
            formData: {'folder': folder, 'thumb': thumb}, //提交给服务器端的参数
            onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                eval("var jsondata = " + data + ";");
                var key = jsondata['key'];
                var name = file.name;
                var fileName = jsondata['fileName'];
                var imgsrc = list + fileName;
                $('.hidden').prepend('<div class="item"><div class="img"><img src="' + imgsrc + '"></div><div class="text"><a href="javascript:;" class="del1"></a><input type="hidden" name="imgname[]"  value="' + name + '"><input type="hidden" name="imgsrc[]"  value="' + fileName + '"></div></div>');
                if ($("input[name='imgsrc1']").val() == '') {
                    $("input[name='imgsrc1']").val(fileName);
                    //$("#upload_img1").html(fileName);
                    $("#img1").attr('src', list + fileName);
                    $("#htcon1").show();
                    $("#del1").show();
                }

            }
        });

    });
}

$(function () {
    $('#cancel').click(function () {
        $('#check').slideToggle();
        if (flag) {
            $('.checkbox').hide();
            flag = false;
        }
        else {
            $('.checkbox').show();
            flag = true;
        }
    });
});

function groupPhotoList_delmore(id, delsome, grouplist) {
    $('#' + id).click(function () {
        var sum = $('#photo_sum').val();
        var arr_id = new Array();
        var num = 0;
        for (var i = 0; i < sum; i++) {
            if ($('#checkbox' + i).is(":checked")) {
                arr_id[num] = $('#id' + i).val();
                num++;
            }
        }
        if (num != 0) {
            //ajax请求，删除图片
            var id = $('#photo_id').val();
            $.ajax({
                url: delsome,
                type: 'GET',
                async: false,
                data: {arr: arr_id, photo_id: id},
                dataType: 'json',
                success: function (data) {
                    if (data == 'success') {
                        alert('删除成功');
                    }
                    else if (data == 'error') {
                        alert('删除错误');
                    }
                    window.location.href = grouplist + '?album_group_id=' + id;
                }
            });
        }
    });
}

function groupPhotoList_select(url, geturl) {
    $(document).on("change", "select", function (e) {
        if ($(this).val() == '') {
            return true;
        }
        var albumgroup_id = $(this).val();
        var thisid = $('#photo_id').val();
        var sum = $('#photo_sum').val();
        var arr_id = new Array();
        var num = 0;
        for (var i = 0; i < sum; i++) {
            if ($('#checkbox' + i).is(":checked")) {
                arr_id[num] = $('#id' + i).val();
                num++;
            }
        }

        $.ajax({
            url: url,
            async: false,
            type: 'GET',
            data: {id: albumgroup_id, arr_id: arr_id, this_id: thisid},
            dataType: 'json',
            success: function (data) {
                if (data == 'success') {
                    alert('移动成功');
                }
                else if (data == 'error') {
                    alert('移动失败');
                }
                window.location.href = geturl + '?album_group_id=' + thisid;
            }
        });
    });
}

/**
 *addUserGrade
 */
//点击取消操作
function addUserGrade_card(url) {
    $("#cover_img").attr('src', url);
    $("input[name='cover']").val();
    $('#pop').hide();
}

function addUserGrade_upload(id, path, js, type, folder, list) {
    $(function () {
        $('#' + id).uploadify({
            onInit: function () {
                $(".uploadify-queue").hide();
            },//载入时触发，将flash设置到最小
            uploader: path,// 服务器处理地址
            swf: js + 'uploadify/uploadify.swf',
            buttonText: "上传样式",//按钮文字
            width: 105, //按钮宽度
            fileTypeExts: type,//允许的文件类型
            fileTypeDesc: "请选择图片文件", //文件说明
            formData: {'folder': folder}, //提交给服务器端的参数
            onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                eval("var jsondata = " + data + ";");
                var key = jsondata['key'];
                var fileName = jsondata['fileName'];
                $("input[name='cover']").val(fileName);
                $("#cover_img").attr('src', list + fileName);
                $(".btn_border").removeAttr('onclick');
            }
        });
    });
}
//限制说明字数
$(function () {
    $("#illustrate").keyup(function () {
        var len = $(this).val().length;
        if (len > 254) {
            $(this).val($(this).val().substring(0, 255));
        }
        var num = 255 - len;
        $("#word").text(num);
    });
});

//会员卡预览效果
function addUserGrade_hideword() {
    if ($("#if_hideword").is(":checked")) {
        $("#membership_card_name").hide();
        $("#dengji_card_name").hide();
    } else {
        $("#membership_card_name").show();
        $("#dengji_card_name").show();
    }
}

//会员名
function addUserGrade_usergradename(url) {
    $('#addUserGrade_usergradename').blur(function () {
        var n = $('#addUserGrade_usergradename').val();
        $.ajax({
            url: url,
            type: 'GET',
            data: {data: n, type: 'name'},
            dataType: 'json',
            success: function (data) {
                if (data.type == 'name')
                    $('#name').text(data.v);
                else
                    $('#name').text('');
            }
        });
    });
}
//会员特权
function addUserGrade_usergradediscount(url) {
    $('#usergradediscount').blur(function () {
        var n = $('#usergradediscount').val();
        $.ajax({
            url: url,
            type: 'GET',
            data: {data: n, type: 'discount'},
            dataType: 'json',
            success: function (data) {
                if (data.type == 'discount')
                    $('#discount').text(data.v);
                else
                    $('#discount').text('');
            }
        });
    });
}
//积分要求
function addUserGrade_usergradepoints_rule(url) {
    $('#usergradepoints_rule').blur(function () {
        var n = $('#usergradepoints_rule').val();
        $.ajax({
            url: url,
            type: 'GET',
            data: {data: n, type: 'points_rule'},
            dataType: 'json',
            success: function (data) {
                if (data.type == 'points_rule')
                    $('#points_rule').text(data.v);
                else
                    $('#points_rule').text('');
            }
        });
    });
}

//积分规则
function addUserGrade_usergradepoints_ratio(url) {
    $('#usergradepoints_ratio').blur(function () {
        var n = $('#usergradepoints_ratio').val();
        $.ajax({
            url: url,
            type: 'GET',
            data: {data: n, type: 'points_ratio'},
            dataType: 'json',
            success: function (data) {
                if (data.type == 'points_ratio')
                    $('#points_ratio').text(data.v);
                else
                    $('#points_ratio').text('');
            }
        });
    });
}
//生日积分倍率
function addUserGrade_usergradebirthday_rate(url) {
    $('#usergradebirthday_rate').blur(function () {
        var n = $('#usergradebirthday_rate').val();
        $.ajax({
            url: url,
            type: 'GET',
            data: {data: n, type: 'birthday_rate'},
            dataType: 'json',
            success: function (data) {
                if (data.type == 'birthday_rate')
                    $('#birthday_rate').text(data.v);
                else
                    $('#birthday_rate').text('');
            }
        });
    });
}
//会员卡名称
function addUserGrade_card_name(url) {
    $('#card_name').blur(function () {
        var n = $('#card_name').val();
        $.ajax({
            url: url,
            type: 'GET',
            data: {data: n, type: 'span_cardname'},
            dataType: 'json',
            success: function (data) {
                if (data.type == 'span_cardname') {
                    $('#span_cardname').text(data.v);
                }
                else {
                    $('#span_cardname').text('');
                }
            }
        });
    });
}

/**
 *orderList
 */
function orderlist_store_id(url) {
    $('#orderlist_store_id').change(function () {
        var store_id = $(this).val();
        $.ajax({
            url: url,
            data: {id: store_id},
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data != 'error') {
                    $('#operate_id').empty();
                    $('#operate_id').append("<option value=''>查询操作员</option>");
                    $.each(data, function (i, item) {
                        $('#operate_id').append("<option value=" + i + ">" + item + "</option>");
                    });
                } else {
                    $('#operate_id').empty();
                    $('#operate_id').append("<option>查询操作员</option>");
                }
            }
        });
    });
}

/**
 *addOperator
 */
function addOperator_Operator_number(maccount, url) {
    //失去焦点时，拼接账号
    $("#Operator_number").blur(function () {
        var account = maccount + $("#Operator_number").val();
        $("#Operator_account").val(account);
        var Operator_account = $('#Operator_account').val();
        $.ajax({
            url: url,
            type: 'get',
            data: {account: Operator_account},
            dataType: 'json',
            success: function (data) {
                if (data == 'DUPLICATE') {
                    $('#number_error').text('操作员编号重复');
                }
                else {
                    $('#number_error').text('');
                }
            }
        });
    });
}

$(function () {
    //角色选择
    $("#Operator_role input").click(function () {
        $(this).attr("checked", true);
        $(this).siblings("input").attr("checked", false);
        if ($("#Operator_role_0").attr("checked")) {
            $("#admin_pwd").show();
        } else {
            $("#admin_pwd").hide();
        }
    });

    //刷新密码
    $("#Operator_admin_pwd").click(function () {
        createPwd();
    });
});

function addOperator_createPwd(url) {
    $.ajax({
        url: url,
        type: 'get',
        success: function (data) {
            $("#Operator_admin_pwd").val(data);
        }
    });
}

/**
 * 禁止退格
 * @returns {Boolean}
 */
function noBackspace(ev) {
    var event = ev || window.event;
    var currentKey = event.charCode || event.keyCode;  //ie、FF下获取键盘码
    if (currentKey == 8) {
        return false;
    }
}

/**
 *operatorset
 */


$(function () {
    //如果天大于90，就把天变成90
    $("input[name=day]").keyup(function () {
        spinner = $('#spinner').val();
        if (spinner > 90) {
            $('#spinner').val('90');
        }
    });
    //如果小时大于23，就把天变成23
    $("input[name=hour]").keyup(function () {
        spinner = $('#spinner1').val();
        if (spinner > 23) {
            $('#spinner1').val('23');
        }
    });
    //如果分钟大于60，就把分钟变成60
    $("input[name=clock]").keyup(function () {
        spinner = $('#spinner2').val();
        if (spinner > 59) {
            $('#spinner2').val('59');
        }
    });


//如果天大于90，就把天变成90
    $("input[name=store_day]").keyup(function () {
        spinner = $('#spinner3').val();
        if (spinner > 90) {
            $('#spinner3').val('90');
        }
    });
//如果小时大于23，就把天变成23
    $("input[name=store_hour]").keyup(function () {
        spinner = $('#spinner4').val();
        if (spinner > 23) {
            $('#spinner4').val('23');
        }
    });
//如果分钟大于60，就把分钟变成60
    $("input[name=store_clock]").keyup(function () {
        spinner = $('#spinner5').val();
        if (spinner > 59) {
            $('#spinner5').val('59');
        }
    });

});

/**
 * storedlist
 */
//自动提交表单
function storedlis_fun() {
    $("form[name=stored]").submit();
}

function storedlist_sub(url) {
    var n = $('#Stored_name').val();
    var sm = $('#Stored_stored_money').val();
    var gm = $('#Stored_get_money').val();
    var t = $('#Time').val();
    $.ajax({
        url: url,
        type: 'GET',
        data: {name: n, stored_money: sm, get_money: gm, Time: t},
        dataType: 'json',
        success: function (data) {
            var flag = true;
            if (data.name != null) {
                $('#stored_name').text(data.name);
                $('#stored_name').attr('style', '');
                flag = false;
            }
            else {
                $('#stored_name').text('');
                $('#stored_name').attr('style', 'display:none');
            }

//                 if(data.get_money!=null) {
//                     $('#stored_money').text(data.get_money);
//                     $('#stored_money').attr('style','');
//                     flag=false;
//                 }
//                 else {
//                     $('#stored_money').text('');
//                     $('#stored_money').attr('style','display:none');
//                 }

            if (data.stored_money != null) {
                $('#stored_money').text(data.stored_money);
                $('#stored_money').attr('style', '');
                flag = false;
            }
            else {
                //$('#stored_money').text('');
                //$('#stored_money').attr('style','display:none');
                if (data.get_money != null) {
                    $('#stored_money').text(data.get_money);
                    $('#stored_money').attr('style', '');
                    flag = false;
                }
                else {
                    $('#stored_money').text('');
                    $('#stored_money').attr('style', 'display:none');
                }
            }


            if (data.time != null) {
                $('#stroed_time').text(data.time);
                $('#stroed_time').attr('style', '');
                flag = false;
            }
            else {
                $('#stroed_time').text('');
                $('#stroed_time').attr('style', 'display:none');
            }
            if (flag) {
                $('#addform').submit();
            }
        }
    });
}
function storedlist_storedname(url) {
    //活动名失去焦点验证
    $('#Stored_name').blur(function () {

        var n = $('#Stored_name').val();
        var sm = $('#Stored_stored_money').val();
        var gm = $('#Stored_get_money').val();
        var t = $('#Time').val();

        $.ajax({
            url: url,
            type: 'GET',
            data: {name: n, stored_money: sm, get_money: gm, Time: t},
            dataType: 'json',
            success: function (data) {
                if (data.name != null) {
                    $('#stored_name').text(data.name);
                    $('#stored_name').attr('style', '');
                } else {
                    $('#stored_name').text('');
                    $('#stored_name').attr('style', 'display:none');
                }
            }
        });
    });
}

function storedlist_storedmoney(url) {
    //预存金额失去焦点验证
    $('#Stored_stored_money').blur(function () {
        var n = $('#Stored_name').val();
        var sm = $('#Stored_stored_money').val();
        var gm = $('#Stored_get_money').val();
        var t = $('#Time').val();
        var re = /^\d+(\.\d)?$/;
        var bol = re.test(sm);
        if (bol) {
            $('#Stored_stored_money').val(parseInt(sm));
        } else {
            $('#Stored_stored_money').val(sm);
        }
        $.ajax({
            url: url,
            type: 'GET',
            data: {name: n, stored_money: sm, get_money: gm, Time: t},
            dataType: 'json',
            success: function (data) {
                if (data.stored_money != null) {
                    $('#stored_money').text(data.stored_money);
                    $('#stored_money').attr('style', '');
                } else {
                    $('#stored_money').text('');
                    $('#stored_money').attr('style', 'display:none');
                }
            }
        });
    });
}

function storedlist_getmoney(url) {
    //赠送金额失去焦点验证
    $('#Stored_get_money').blur(function () {
        var n = $('#Stored_name').val();
        var sm = $('#Stored_stored_money').val();
        var gm = $('#Stored_get_money').val();
        var t = $('#Time').val();

        var re = /^\d+(\.\d)?$/; //小数点正则
        var bol = re.test(gm);
        if (bol) { //如果不含小数点
            $('#Stored_get_money').val(parseInt(gm));
        } else {
            $('#Stored_get_money').val(gm);
        }

        $.ajax({
            url: url,
            type: 'GET',
            data: {name: n, stored_money: sm, get_money: gm, Time: t},
            dataType: 'json',
            success: function (data) {
                if (data.get_money != null) {
                    $('#stored_money').text(data.get_money);
                    $('#stored_money').attr('style', '');
                } else {
                    $('#stored_money').text('');
                    $('#stored_money').attr('style', 'display:none');
                }
            }
        });
    });
}

/**
 *productlist
 */
//全选
$('input:checkbox[name="ShopProduct_checkAll"]').click(function () {
    if ($(this).is(":checked")) {
        $(".recharge input:checkbox").each(function () {
            $(this).prop("checked", true);
        })
    } else {
        $(".recharge input:checkbox").each(function () {
            $(this).prop("checked", false);
        })
    }
});

function shopproduct_undercarriage(url, none) {
    $('#undercarriage').click(function (e) {
        var tmp = new Array();
        if (confirm('确认下架吗')) {

            $(".recharge input:checkbox").each(function () {
                if ($(this).is(":checked") && $(this).attr("name") != "ShopProduct_checkAll") {
                    var id = $(this).val();
                    tmp.push(id);
                }
            });

            if (tmp == '') {
                alert('请选择商品');
                return false;
            }

            $.ajax({
                url: url,
                data: {content: tmp},
                dataType: "json",
                type: 'get',
                success: function (data) {
                    if (data == none) {
                        alert('下架成功');
                        window.location.reload();
                    } else {
                        alert(data);
                    }
                }
            });
        }
    });
}

function shopproduct_upcarriage(url, none) {
    $('#upcarriage').click(function (e) {
        var tmp = new Array();
        if (confirm('确认上架吗')) {

            $(".recharge input:checkbox").each(function () {
                if ($(this).is(":checked") && $(this).attr("name") != "ShopProduct_checkAll") {
                    var id = $(this).val();
                    tmp.push(id);
                }
            });

            if (tmp == '') {
                alert('请选择商品');
                return false;
            }

            $.ajax({
                url: url,
                data: {content: tmp},
                dataType: "json",
                type: 'get',
                success: function (data) {
                    if (data == none) {
                        alert('上架成功');
                        window.location.reload();
                    } else {
                        alert(data);
                    }
                }
            });
        }
    });
}

/*$(function () {
    //商品改分组
    $('#changegroup').click(function (e) {
        var flag = false;
        var tmp = new Array();
        $(".recharge input:checkbox").each(function () {
            if ($(this).is(":checked")) {
                flag = true;
                tmp.push($(this).val());
            }
        });
        $('.popStored input:checkbox').each(function () {
            $(this).prop('checked', false);
        });
        if (flag) {
            //弹出分组框
            $('#pop').toggle();
        }
        else {
            alert('请选择商品');
        }
    });
});*/

function shopproduct_save(url) {
    $('#save').click(function (e) {
        var group_arr = new Array();
        var shop_arr = new Array();
        $('.popStored input:checkbox').each(function () {
            if ($(this).is(':checked')) {
                group_arr.push($(this).val());
            }
        });

        $(".recharge input:checkbox").each(function () {
            if ($(this).is(":checked") && $(this).attr('name') != 'ShopProduct_checkAll') {
                shop_arr.push($(this).val());
            }
        });
        $.ajax({
            url: url,
            data: {group_arr: group_arr, shop_arr: shop_arr},
            dataType: 'json',
            type: 'get',
            success: function (data) {
                if (data == 'success') {
                    alert('修改成功');
                    window.location.reload();
                }
                else {
                    alert(data);
                }
            }
        });
    });
}

function shopproduct_search(url_search) {
    $('#search').click(function (e) {
        var pro_status = $('#shop_product_status').val();
        var group_id = $('#group_id').val();
        var key_word = $('#key_word').val();
        var shop_group = $('#shop_group').val();
        var arrow_type = $('#arrow_type').val();
        var arrow = $('#arrow').val();
        if (arrow == 'arrowUp')
            arrow = 'arrowDown';
        else if (arrow == 'arrowDown')
            arrow = 'arrowUp';
        var url = url_search + '?pro_status=' + pro_status + '&group_id=' + group_id + '&key_word=' + key_word + '&shop_group=' + shop_group + '&arrow=' + arrow + '&arrow_type=' + arrow_type;
        window.location = url;
    });
}

function shopproduct_volume(url_volume) {
    $('#volume').click(function (e) {
        //总销量
        var pro_status = $('#shop_product_status').val();
        var group_id = $('#group_id').val();
        var key_word = $('#key_word').val();
        var shop_group = $('#shop_group').val();
        var arrow = $('#arrow').val();
        var arrow_type = $('#arrow_type').val();
        arrow_type = 'volume';
        var url = url_volume + '?pro_status=' + pro_status + '&group_id=' + group_id + '&key_word=' + key_word +
            '&shop_group=' + shop_group + '&arrow=' + arrow + '&arrow_type=' + arrow_type;
        window.location = url;
    });
}

function shopproduct_stock(url_stock) {
    $('#stock').click(function (e) {
        //库存
        var pro_status = $('#shop_product_status').val();
        var group_id = $('#group_id').val();
        var key_word = $('#key_word').val();
        var shop_group = $('#shop_group').val();
        var arrow = $('#arrow').val();
        var arrow_type = $('#arrow_type').val();
        arrow_type = 'stock';
        var url = url_stock + '?pro_status=' + pro_status + '&group_id=' + group_id + '&key_word=' + key_word +
            '&shop_group=' + shop_group + '&arrow=' + arrow + '&arrow_type=' + arrow_type;
        window.location = url;
    });
}

function shopproduct_create_time(url_createtime) {
    $('#create_time').click(function (e) {
        var pro_status = $('#shop_product_status').val();
        var group_id = $('#group_id').val();
        var key_word = $('#key_word').val();
        var shop_group = $('#shop_group').val();
        var arrow = $('#arrow').val();
        var arrow_type = $('#arrow_type').val();
        arrow_type = 'create_time';
        var url = url_createtime + '?pro_status=' + pro_status + '&group_id=' + group_id + '&key_word=' + key_word +
            '&shop_group=' + shop_group + '&arrow=' + arrow + '&arrow_type=' + arrow_type;
        window.location = url;
    });
}

function shopproduct_price(url_price) {
    $('#price').click(function (e) {
        var pro_status = $('#shop_product_status').val();
        var group_id = $('#group_id').val();
        var key_word = $('#key_word').val();
        var shop_group = $('#shop_group').val();
        var arrow = $('#arrow').val();
        var arrow_type = $('#arrow_type').val();
        arrow_type = 'price';
        var url = url_price + '?pro_status=' + pro_status + '&group_id=' + group_id + '&key_word=' + key_word +
            '&shop_group=' + shop_group + '&arrow=' + arrow + '&arrow_type=' + arrow_type;
        window.location = url;
    });
}

function productlist_delMore(url, none) {
    var tmp = new Array();
    if (confirm('确认删除吗')) {

        $(".recharge input:checkbox").each(function () {
            if ($(this).is(":checked") && $(this).attr("name") != "ShopProduct_checkAll") {
                var id = $(this).val();
                tmp.push(id);
            }
        });

        if (tmp == '') {
            alert('请选择商品');
            return false;
        }

        $.ajax({
            url: url,
            data: {content: tmp},
            dataType: "json",
            type: 'get',
            success: function (data) {
                if (data == none) {
                    alert('删除成功');
                    window.location.reload();
                } else {
                    alert(data);
                }
            }
        });
    }
}

/**
 *addProductOfCategory
 * editProductOfCategory
 */
$(function () {
    $("#addproductofcategory_parent li").click(function () {
        $('input[name="Product[category_two]"]').val("");

        var p = $(this).attr("val");
        $(this).attr("class", "cur").siblings().removeClass();
        var html = '';
        for (var i in arr_two[p]) {
            html += '<li val="' + i + '">' + arr_two[p][i] + '</li>';
        }
        $("#addproductofcategory_child ul").html(html);
        $('input[name="Product[category_one]"]').val(p);
    });

    //二级分类
    $(document).on("click", "#addproductofcategory_child li", function () {
        var son = $(this).attr("val");
        $(this).attr("class", "cur").siblings().removeClass();
        $('input[name="Product[category_two]"]').val(son);

        var par = $('input[name="Product[category_one]"]').val();
        $("#category_name").html(arr_one[par] + ' &gt; ' + arr_two[par][son]);
    });

    //一级分类下一步
    $("#next_step").click(function () {
        var c_one = $('input[name="Product[category_one]"]').val();
        var c_two = $('input[name="Product[category_two]"]').val();
        if (c_one == "" || c_two == "") {
            alert("请选择商品类目")
        } else {
            $("#step_two").show();
            $("#step_one").hide();
            $("#step_three").hide();
            $("#chose_step").attr("class", "contant shop_add");
            $(this).attr("class", "cur");
            $('#addproduct_product').attr("class", 'cur');
            $("#addproduct_category").removeClass();
            $("#addproduct_detail").removeClass();
            $("#main", parent.document).height($(".kkfm_r_inner").outerHeight());
        }
    });

    //添加商品步骤切换
    $("#addproduct_category").click(function () {
        $("#step_one").show();
        $("#step_two").hide();
        $("#step_three").hide();
        $("#chose_step").attr("class", "contant sCategory_add");
        $(this).attr("class", "cur");
        $("#addproduct_product").removeClass();
        $("#addproduct_detail").removeClass();
    });
    $("#addproduct_product").click(function () {
        var c_one = $('input[name="Product[category_one]"]').val();
        var c_two = $('input[name="Product[category_two]"]').val();
        if (c_one == "" || c_two == "") {
            alert("请选择商品类目")
        } else {
            $("#step_two").show();
            $("#step_one").hide();
            $("#step_three").hide();
            $("#chose_step").attr("class", "contant shop_add");
            $(this).attr("class", "cur");
            $("#addproduct_category").removeClass();
            $("#addproduct_detail").removeClass();
            $("#main", parent.document).height($(".kkfm_r_inner").outerHeight());
        }
    });

    $("#addproduct_detail").click(function () {
        addproduct_save();
    });

    //添加规格
    $("#addproduct_add_standard").click(function () {
        $("#standard_form").show();
        window.parent.autoResize('main');
    });

    //删除规格
    $(document).on('click', 'a[name=del]', function () {
        $("#standard_form").hide();
        $(this).parent().parent().remove();
    });

    //编辑规格
    $(document).on('click', 'a[name=edit]', function () {
        $("#standard_form").show();
        var name = $(this).closest(".first").attr("val");
        var html = $("#standard_attr ul").html();
        $('input[name="Product[standard_name]"]').val(name);
        $(this).closest(".ul01").each(function () {
            $(this).find("li:not(.first)").each(function () {
                var attrbutes = $(this).find("input").val();
                html += '<li val="' + attrbutes + '">' + attrbutes + ' <a href="##">×</a></li>';
            });
        });
        $(this).closest(".ul01").attr("id", "edit_standard_model");
        $("#standard_attr ul").html(html);
    });

    //添加规格-确认
    $("#standard_sub").click(function () {
        var arr = new Array();
        var name = $('input[name="Product[standard_name]"]').val().replace(/^\s+|\s+$/g, "");
        if (name != "") {
            var html = '<ul class="ul01" name="else_ul">';
            html += '<li class="first" val="' + name + '">' + name + '<a href="##" name="edit">编辑</a> <a href="##" name="del">删除</a></li>';
            $("#standard_attr li").each(function () {
                var val = $(this).attr("val");
                // 				arr.push(val);
                html += '<li><input type="checkbox" value="' + val + '">' + val + '</li>';
            });
            html += '</ul>';
            if (document.getElementById('edit_standard_model')) {
// 					$('#edit_standard_model').html(html);
                $('#edit_standard_model').replaceWith(html);
                $('#edit_standard_model').removeAttr("id");
            } else {
                $("#standard_form").before(html);
            }
            $('input[name="Product[standard_name]"]').val("");
            $('input[name="Product[standard_attribute]"]').val("");
            $("#standard_attr ul").html("");
            $("#standard_form").hide();
        } else {
            alert("请填写规格名称");
        }
    });

    //添加规格-删除属性
    $(document).on('click', '#standard_attr ul li a', function () {
        $(this).parent().remove();
    });

    //添加规格-添加
    $("#standard_add").click(function () {
        var html = $("#standard_attr ul").html();
        var attrbutes = $('input[name="Product[standard_attribute]"]').val().replace(/^\s+|\s+$/g, "");
        if (attrbutes != "") {
            html += '<li val="' + attrbutes + '">' + attrbutes + ' <a href="##">×</a></li>';
            $("#standard_attr ul").html(html);
            $('input[name="Product[standard_attribute]"]').val("");
        }
    });

    //添加规格-取消
    $("#standard_cel").click(function () {
        $('input[name="Product[standard_name]"]').val("");
        $('input[name="Product[standard_attribute]"]').val("");
        $("#standard_attr ul").html("");
        $("#standard_form").hide();
        window.parent.autoResize('main');
    });

    //点击保存到模板
    $("#save_standard").click(function () {
        $("#standard_model_form").show();
    });

    //取消保存模板
    $("#model_cel").click(function () {
        $('input[name="Smodel[name]"]').val("");
        $('select[name="Smodel[model]"]').val("0");
        $("#standard_model_form").hide();
    });

    //批量设置价格
    $(document).on('click', '#prize', function () {
        $("#setMoreName").hide();
        $('input[name="Set[more_val]"]').attr('placeholder', '请输入价格');
        $("#setMoreVar").show();
        $('input[name="Set[more_type]"]').val("1");
    });

    //批量设置原价
    $(document).on('click', '#cost', function () {
        $("#setMoreName").hide();
        $('input[name="Set[more_val]"]').attr('placeholder', '请输入原价');
        $("#setMoreVar").show();
        $('input[name="Set[more_type]"]').val("2");
    });

    //批量设置库存
    $(document).on('click', '#stock', function () {
        $("#setMoreName").hide();
        $('input[name="Set[more_val]"]').attr('placeholder', '请输入库存');
        $("#setMoreVar").show();
        $('input[name="Set[more_type]"]').val("3");
    });

    //批量设置-取消
    $(document).on('click', '#setMore_cel', function () {
        $("#setMoreName").show();
        $("#setMoreVar").hide();
        $('input[name="Set[more_val]"]').val("");
    });

    //批量设置-确定
    $(document).on('click', '#setMore_sub', function () {
        var num = $('input[name="Set[more_val]"]').val();
        var type = $('input[name="Set[more_type]"]').val();
        var sum = 0;
        if (isNaN(num)) {
            alert("请输入数字");
        } else {
            if (type == 1) {  //设置价格
                $(".pro_sku").each(function () {
                    $(this).find("td:eq(1) input").val(num);
                });
            } else if (type == 2) {  //设置原价
                $(".pro_sku").each(function () {
                    $(this).find("td:eq(2) input").val(num);
                });
            } else if (type == 3) {  //设置库存
                $(".pro_sku").each(function () {
                    $(this).find("td:eq(3) input").val(num);
                    sum += parseInt(num);
                });
            }

            if (type == 3) {
                $('input[name="Product[num]"]').val(sum);
                $('input[name="Product[all_num]"]').val(sum);
            }
            $("#setMoreName").show();
            $("#setMoreVar").hide();
            $('input[name="Set[more_val]"]').val("");
        }
    });


    //点击 删除上传图片
    $('#ShowPic a').on("click", function () {
        $(this).parent().remove();
        imgCount--;
        parent.callParAutoResize('iframe', 0);
    });

});
//点击多选框事件
function product_checkbox(url) {
    $(document).on('change', '.ul01 input:checkbox', function () {
        //修改总库存 todo
        $('input[name="Product[num]"]').val(0);
        $('input[name="Product[all_num]"]').val(0);
        //修改商品价格
        $('input[name="Product[pro_min_price]"]').val(0);
        $('input[name="Product[pro_price]"]').val(0);

        var dic = new Array();
        var pro_standard = "";
        if ($(this).is(":checked")) {
            $(this).attr("checked", "checked");
        } else {
            $(this).removeAttr("checked");
        }

        $(".ul01").each(function () {
            var title = $(this).find(".first").attr("val");
            pro_standard += title + ':';
            var tmp = new Array();
            $(this).find("li:not(.first)").each(function () {
                if ($(this).find("input").is(":checked")) {
                    var name = $(this).find("input").val();
                    tmp.push(name);
                    pro_standard += name + ',';
                }
            });
            var a = {};
            a[title] = tmp;
            dic.push(a);
            pro_standard += ';';
        });
        $('input[name="Product[pro_standard_name]"]').val(pro_standard);
        $.ajax({
            url: url,
            data: {content: dic},
            type: 'get',
            success: function (data) {
                $("#PEPId_6312").html(data);
                $("#main", parent.document).height($(".kkfm_r_inner").outerHeight());
            }
        });
    })
}
//保存到模板
function product_model_sub(save_new, save_old, url, none) {
    $(document).on('click', '#model_sub', function () {
        var model_name = $('input[name="Smodel[name]"]').val().replace(/^\s+|\s+$/g, "");
        var save_type = $('input[name="Smodel[save_type]"]:checked').val();
        var model_id = $('select[name="Smodel[model]"]').val();
        var length = 0;
        if (model_name == "" && save_type == save_new) {
            alert("请输入模板名称");
        } else if (model_id == 0 && save_type == save_old) {
            alert("请选择模板");
        } else {
            var dic = new Array();
            $(".ul01").each(function () {
                var title = $(this).find(".first").attr("val");
                var tmp = new Array();
                $(this).find("li:not(.first)").each(function () {
                    var name = $(this).find("input").val();
                    tmp.push(name);
                });
                var a = {};
                a[title] = tmp;
                dic.push(a);
                length++;
            });
            if (length > 1) {
                $.ajax({
                    url: url,
                    data: {name: model_name, content: dic, save_type: save_type, model_id: model_id},
                    dataType: "json",
                    type: 'get',
                    success: function (data) {
                        if (data.status == none) {
                            $("#standard_model_form").hide();
                            $('input[name="Smodel[name]"]').val("");
                            $('select[name="Smodel[model]"]').val("0");
                            //刷新下拉框数据源
                            if (data.type == save_new) {
                                $('select[name="Product[standard_model]"]').append('<option value="' + data.id + '">' + data.name + '</option>');
                                $('select[name="Smodel[model]"]').append('<option value="' + data.id + '">' + data.name + '</option>');
                            }
                            $('select[name="Product[standard_model]"]').val(data.id);
                            alert('保存成功');
                        }
                    }
                });
            } else {
                $("#standard_model_form").hide();
                $('input[name="Smodel[name]"]').val("");
                alert("请创建除颜色以外的商品规格");
            }
        }
    })
}
//上传图片
function product_upload(path, js, type, folder, list, thumb) {
    $(function () {
        $('#upload').uploadify({
            onInit: function () {
                $(".uploadify-queue").hide();
            },//载入时触发，将flash设置到最小
            uploader: path,// 服务器处理地址
            swf: js + 'uploadify/uploadify.swf',
            buttonText: "选择图片",//按钮文字
            height: 34,  //按钮高度
            width: 82, //按钮宽度
            fileTypeExts: type,//允许的文件类型
            fileTypeDesc: "请选择图片文件", //文件说明
            formData: {'folder': folder, 'thumb': thumb}, //提交给服务器端的参数
            onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                eval("var jsondata = " + data + ";");
                var key = jsondata['key'];
                var fileName = jsondata['fileName'];
                //$("#ShowPic").after("<li><a href='"+"<?php //echo(IMG_GJ_LIST) ?>" + fileName + "'><img src='"+"<?php //echo(IMG_GJ_LIST) ?>" + fileName + "' width='90px' height='90px'/></a><span>删除</span><input type='hidden' name='PicPath[]' value='" + fileName + "'/></li>");
                $("#ShowPic").append("<li><img src='" + list + fileName + "' width='62px' height='62px'/>" + '<a href="##" class="close">×</a>' + "<input type='hidden' name='Product[pro_img][]' value='" + fileName + "'/></li>");
                imgCount++;
                $('#ShowPic a').on("click", function () {
                    $(this).parent().remove();
                    imgCount--;
                });
                parent.callParAutoResize('iframe', 0);
                $("#main", parent.document).height($(".kkfm_r_inner").outerHeight());
            },
            onDialogClose: function (queueData) {
                if (queueData.queueLength + imgCount > 15) {
                    alert("你上传的图片数量已经超过15张，不能再上传了!");
                    var i = 0;
                    for (var s in queueData.files) {
                        i++;
                        //选中多张上传，不超过5张部分可正常上传，超过5张部分，取消上传
                        if (i + imgCount > 15) {
                            $("#pro_picture").uploadify("cancel", s);
                        }
                    }
                    return;
                }
            }
        });
    });
}

//选择规格模板
function changeStandard(url, none) {
    var id = $('select[name="Product[standard_model]"]').val();
    $('input[name="Product[pro_standard_id]"]').val(id);
    $.ajax({
        url: url,
        data: {standard_id: id},
        dataType: "json",
        type: 'get',
        success: function (data) {
            if (data.status == none) {
// 	                	$("#standard_form").before(data.data);

                $("#ULID_686").html(data.data);
                $('input[name="Product[standard_name]"]').val("");
                $('input[name="Product[standard_attribute]"]').val("");
                $("#standard_attr ul").html("");
                $("#standard_form").hide();
                $("#main", parent.document).height($(".kkfm_r_inner").outerHeight());
            } else {
                alert('加载商品规格模板失败');
            }
        }
    });
}

//选择运费的单选框触发事件
function product_freight_type(unite, model) {
    $('input[name="Product[freight_type]"]').change(function () {
        var ftype = $('input[name="Product[freight_type]"]:checked').val();
        if (ftype == unite) {
            $('input[name="Product[pro_total_inventory]"]').removeAttr("disabled");
            $('select[name="Product[freight_id]"]').attr("disabled", "true");
            $('select[name="Product[freight_id]"]').val("0");
            $('input[name="Product[freight_type]"][value=' + unite + ']').attr('checked', 'checked');
            $('input[name="Product[freight_type]"][value=' + model + ']').removeAttr('checked');

        } else if (ftype == model) {
            $('select[name="Product[freight_id]"]').removeAttr("disabled");
            $('input[name="Product[pro_total_inventory]"]').attr("disabled", "disabled");
            $('input[name="Product[freight_type]"][value=' + unite + ']').removeAttr('checked');
            $('input[name="Product[freight_type]"][value=' + model + ']').attr('checked', 'true');

        }
    });
}