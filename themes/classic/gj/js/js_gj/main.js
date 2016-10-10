// JavaScript Document

//iframe框架高度自适应
function autoResize(id, a) {
    var objiframe = $('#' + id);
    if (isNaN(a)) {
        a = 0;
    }
    $(objiframe).height(a);
    var finalHeight = $(objiframe).contents().height();
    $(objiframe).height(finalHeight);
}

//弹出框居中方法
function _pop(id) {
    var obj = $('#' + id);

    var _w = $(window).width() //浏览器窗口宽度
    var _h = $(window).height() //浏览器窗口高度

    var _offsetW = obj.width(); //获取弹出框的宽度
    var _offsetH = obj.height(); //获取弹出框的高度

    var _left = (_w - _offsetW) / 2;
    var _top = (_h - _offsetH) / 2;

    obj.css({
        'left': _left,
        'top': _top
    });
}


//当form表单回车后，回车失效
function shadowPop() {
    $("#popShadow").show();
    $("#pop").show();
    _pop("pop");
}


//关闭弹出框
function closePop(id) {
    var obj = $("#" + id);
    $("#popShadow").hide();
    $("#pop").hide();
    $("#barCode").focus();
}

//只允许输入整数和小数点
//调用方式：onkeydown = "onlyNum(this,event)"
//8：退格键; 46：delete; 37-40: 方向键
//48-57:小键盘区的数字； 96-105：主键盘区的数字
//110\190:小键盘区和主键盘区的小数
function onlyNum(el, ev) {
    var event = ev || window.event;
    var currentKey = event.charCode || event.keyCode; //ie、FF下获取键盘码

    //当输入的值为空，按回车是提示输入用户输入金额
    if (ev.keyCode == 13 && el.value == "") {
        alert("请输入收款金额");
    } else if (ev.keyCode == 13 && document.activeElement.id == "amount") { //禁用回车事件
        $("#barCode").focus();
        shadowPop(); //弹出框居中显示方法
        return false; //禁用回车事件          
    }

    //除了48~57,96~105,两个小数点，左右键，退格键外其他的键不能输入
    if (!((currentKey >= 48 && currentKey <= 57) || (currentKey >= 96 && currentKey <= 105) || (currentKey == 110 && el.value.indexOf(".") < 0) || (currentKey == 190 && el.value.indexOf(".") < 0) || currentKey == 37 || currentKey == 39 || currentKey == 8)) {
        if (window.event) {
            event.returnValue = false;
        } else {
            event.preventDefault();
        }
    }

}

//初始化高度自适应
function autoHeight(a, b, c) {
    var obj_a = $("." + a); //头部
    var obj_b = $("." + b); //主体部分
    var obj_c = $("." + c); //尾部

    obj_b.css({
        "min-height": $(document).height() - obj_a.height() - obj_c.outerHeight()
    });
};

$(function() {
    autoHeight('kkf_loginBD', 'kkf_loginBD', 'footer');
    autoHeight('header', 'kkfmain', 'footer');
})



function navClick(obj) {
    if (!document.getElementsByTagName) return false;
    if (!document.getElementById) return false;
    if (!document.getElementById("handles1")) return false;
    obj.className = "current";
    var id;
    var divId;
    var linkid;
    var handle = document.getElementById("handles1");
    var links = handle.getElementsByTagName("a");
    for (i = 1; i <= links.length; i++) {
        id = "nav" + i;
        linkid = document.getElementById(id);
        divid = document.getElementById("b9e43e-" + i);
        if (id != obj.id) {
            linkid.className = "lnk";
            divid.style.display = "none";
        } else {
            divid.style.display = "block";
        }
    }
}

var main_obj = {
    list_init: function() {
        //编辑操作
        $('.operate').hover(
            function() {
                //得到当前对象距离底部的距离
                var pos = $(document).height() - $(this).offset().top;
                //当前对象隐藏的子对象的高度
                var height = $(this).find("dd").height();

                //判断假如显示层高度大于该层里底部的距离，则加“oper_hover_up”，否则则加“oper_hover”
                if (height > pos) {
                    $(this).css({
                        "z-index": 11
                    }).addClass('oper_hover_up');
                } else {
                    $(this).css({
                        "z-index": 11
                    }).addClass('oper_hover');
                }
            },
            function() {
                if ($(this).hasClass("oper_hover_up")) {
                    $(this).css({
                        "z-index": 10
                    }).removeClass('oper_hover_up')
                }
                if ($(this).hasClass("oper_hover")) {
                    $(this).css({
                        "z-index": 10
                    }).removeClass('oper_hover')
                }
            }
        );

       /* $(".operate a").click(function() {
            var html = $(this).html();

            $(this).blur();
            $(this).parent().prev().html(html + '<b></b>');
            $(this).parents("dl").removeClass('oper_hover')
        });*/


    }
}

$(function() {
    $(".gl li").click(function(e) {
        e.stopPropagation();
        $(this).siblings().removeClass("on");
        $(this).addClass("on");
        var _index = $(this).index();
        $(".guide_nav").eq(_index).removeClass("gl_active")
            .siblings().addClass("gl_active");
    })
    $(".koubeiLogo").click(function(e) {
        e.stopPropagation();
        $(".gl").fadeToggle();
    })
    $(document).click(function() {
        $(".gl").fadeOut();
    })
    $(".gl").click(function(e) {
        e.stopPropagation();
        $(this).show();
    })
    $(".btn_sure_next").click(function() {
        $(this).parent().next().removeClass("gl_active")
            .siblings().addClass("gl_active");
        $(".gl li").eq(1).addClass("on").siblings().removeClass("on");
    })

    /*$(".btn_filtrate").click(function() {
        $(this).toggleClass("up_arrow");
        if ($(this).hasClass("up_arrow")) {
            $(this).val("收起筛选");
        } else {
            $(this).val("更多筛选");
        }
        $(".more_area").stop(true,true).slideToggle();
    })*/

    $(".btn_attr_tag").click(function() {
        $('.new_group_attr').show();
        $('.new_group_term').hide();
        $(this).addClass("btn_com_blue").removeClass("btn_com_gray").siblings().addClass("btn_com_gray").removeClass("btn_com_blue");
    })
    $(".btn_term_tag").click(function() {
        $('.new_group_term').show();
        $('.new_group_attr').hide();
        $(this).addClass("btn_com_blue").removeClass("btn_com_gray").siblings().addClass("btn_com_gray").removeClass("btn_com_blue");
    })

    /*7天、14天、30天按钮切换*/
    $("#new_user_btn input[type='button']").click(function() {
        $(this).siblings("input[type='button']")
            .addClass("btn_com_gray")
            .removeClass("btn_com_blue");
        $(this).addClass("btn_com_blue")
            .removeClass("btn_com_gray");
    })

    //点击删除城市

    /*$(".chosen-select").chosen();*/

    $(".broadcast_box_top .icon1 a").click(function() {
        $(".broadcast_box_con").show();
        $(".broadcast_textarea").hide();
    })

    $(".broadcast_box_top .icon2 a").click(function() {
        $(".broadcast_box_con").hide();
        $(".broadcast_textarea").show();
    })

    /*选择素材时的瀑布流排列*/
   /* $(".material_pop_box_inner").masonry({});*/

    $(".material_pop_top .close_btn").click(function() {
        $(".material_pop_content").hide();
    })

    $(document).on("click", ".btn_add_from", function() {
            $(".material_pop_content").show();
        })

    //素材选中状态

    var $mask = $('<div class="material_pop_mask"></div>');
    var $selected = $('<i class="icon_card_selected"></i>');
    var $_mask = $mask.clone();
    $(".material_pop_box_item").hover(function() {
        $(this).append($mask);
    })

    $(document).on("click", ".material_pop_mask", function() {
        $(this).parent().append($selected);
        $(this).parent().append($_mask);
    });

    $(".user_list_order span").click(function() {
        $(this).find("i").toggleClass("arrow_up_active");
    })

    //回复关键字添加
    $(".btn_com_keyword").click(function() {
        var keyword = $("#Reply_key_word").val();
        if (keyword == "") {
            return false;
        } else {
           $(".keyword_name").append("<span>" + keyword + "<i>×</i><input style='display:none' value="+keyword+" name='Reply[key_word][]'></span>")
        }
    })

    //回复关键字删除
    $(document).on("click", ".keyword_name span i", function() {
        $(this).parent().remove();
    });

    $(".dropdown_f span,.dropdown_s span").hover(function(){
        $(this).addClass("bg").siblings().removeClass("bg");
    })

    $("#date_num1").click(function(e){
        e.stopPropagation();
        $(".dropdown_f").show();
        $(".dropdown_s").hide();
    })

    $("#date_num1").bind("input onpropertychange",function(){
        $(".dropdown_f").hide();
    })

    $(".dropdown_f span").click(function(){
        var text = $(this).html();
        $("#date_num1").val(text);
        $(".dropdown_f").hide();
    })

    $("#date_num2").click(function(e){
         e.stopPropagation();
        $(".dropdown_s").show();
        $(".dropdown_f").hide();
    })

    $("#date_num2").bind("input onpropertychange",function(){
        $(".dropdown_s").hide();
    })

    $(".dropdown_s span").click(function(){
        var text = $(this).html();
        $("#date_num2").val(text);
        $(".dropdown_s").hide();
    })

   $(document).click(function(){
     $(".dropdown_s").hide();
     $(".dropdown_f").hide();
   })

   /* 左侧客服电话 */
    $(".cellphone").hover(
        function() {
            $(".phone").show(500)
        },
        function() {
            $(".phone").hide()
        }
    )
    $(".backToptel .tel").hover(
        function() {
            $(".tel span").show(250)
        },
        function() {
            $(".tel span").hide()
        }
    )

    /* 用户管理添加分组 打标签*/
    
    $("#add_tag").click(function(){
        $("#pop_tag").toggle();
    })

    $("#add_group").click(function(){
        $("#pop_group").toggle();
    })

    $(".cancel_btn").click(function(){
        $(this).parents(".pop_tag").hide();
    })

    $(".pop_sure_btn").click(function(){
        $(this).parents(".pop_tag").hide();
    })

    /* 酒店图集 */
    $(document).on("mouseover",".gallery-list li",function(){
        $(this).find(".icon-hotel").addClass("icon-hotel-6");
    });

    $(document).on("mouseout",".gallery-list li",function(){
        $(this).find(".icon-hotel").removeClass("icon-hotel-6");
    });

    $(document).on("click",".icon-hotel-6",function(){
        $(this).parent().remove();
    });
})
