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

    obj.css({ 'left': _left, 'top': _top });
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

    obj_b.css({ "min-height": $(document).height() - obj_a.height() - obj_c.outerHeight() });
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
        var pos;
        $('.operate').hover(
            function() {
                //得到当前对象距离底部的距离
                var pos = $(document).height() - $(this).offset().top;
                //当前对象隐藏的子对象的高度
                var height = $(this).find("dd").height();

                //判断假如显示层高度大于该层里底部的距离，则加“oper_hover_up”，否则则加“oper_hover”
                if (height > pos) {
                    $(this).css({ "z-index": 11 }).addClass('oper_hover_up');
                } else {
                    $(this).css({ "z-index": 11 }).addClass('oper_hover');
                }
            },
            function() {
                if ($(this).hasClass("oper_hover_up")) {
                    $(this).css({ "z-index": 10 }).removeClass('oper_hover_up')
                }
                if ($(this).hasClass("oper_hover")) {
                    $(this).css({ "z-index": 10 }).removeClass('oper_hover')
                }
            }
        );

        $(".operate a").click(function() {
            var html = $(this).html();
            $(this).blur();
            //$(this).parent().prev().html(html + '<b></b>');
            $(this).parents("dl").removeClass('oper_hover');
            /*点击下拉“标签”项，获取它父级dl的偏移值,赋给显示值tagAdd的top,left值
            * pos.left-tagAdd.outerWidth()-$(this).parents("dl").outerWidth()/2 - 14 = 左边偏移值-弹出层的宽度-弹出层的箭头的宽度-下拉框2分之一的宽度
            * */
            var tagAdd = $(this).parents("td").find(".tagAdd")
            var pos= $(this).parents("dl").offset();
            var posL = pos.left-tagAdd.outerWidth()-14-$(this).parents("dl").outerWidth()/2;
            var posT = pos.top-tagAdd.outerHeight()-8-$(this).parents("dl").outerHeight();

            //得到当前对象距离底部的距离
            var position = $(document).height() - $(this).parents("dl").offset().top;

            if(html == "标签"){
                if(tagAdd && position>tagAdd.outerHeight()){
                    $(".tagAdd").removeClass("tagAddOther").hide();
                    tagAdd.css({left:posL,top:posT }).show()
                }else{
                    $(".tagAdd").removeClass("tagAddOther").hide();
                    tagAdd.css({left:posL,top:posT-200 }).show().addClass("tagAddOther")
                }
            }

        });

        $(".tagAdd .btn_com_gray").click(function(){
            $(this).parents(".tagAdd").hide();
        })


    }
}

/* 报错 */
$(function() {
	function errorShow() {
		$(".n-error").slideDown().stop(true,true).delay(1000).slideUp();
	};

	$(".login-btn").click(function() {
		errorShow();
	})

	$(document).keydown(function(e) {
		if (e.keyCode == 13) {
			errorShow();
		}
	})

    //模拟多选框点击效果
    $(".checkboxAnalog").click(function() {
        if ($(this).hasClass("checkboxChecked")) {
            $(this).removeClass("checkboxChecked");
            $(this).children("input").prop("checked",false);
        } else {
            $(this).addClass("checkboxChecked");
            $(this).children("input").prop("checked",true);
        }
    })

    /*业务概述-交易管理里 展开或收缩收款内容*/
    $(".td-flexible").click(function(){
        if($(this).find("em").hasClass("arrowUp")){
            $(this).parent().parent().find(".BTListSecend").show();
            $(this).find("em").removeClass("arrowUp").addClass("arrowDown");
        }else if($(this).find("em").hasClass("arrowDown")){
            $(this).parent().parent().find(".BTListSecend").hide();
            $(this).find("em").removeClass("arrowDown").addClass("arrowUp");
        }
    })

})






  