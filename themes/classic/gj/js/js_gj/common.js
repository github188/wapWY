$(function(){
    autoHeight('kkf_loginBD','kkf_loginBD','footer');
    autoHeight('header','Wqmain','footer');
});

//初始化高度自适应
function autoHeight(a,b,c){
    var obj_a = $("."+a); //头部
    var obj_b = $("."+b); //主体部分
    var obj_c = $("."+c); //尾部
    obj_b.css({"min-height":$(document).height()-obj_a.height()-obj_c.outerHeight()});
}

//左侧功能连接跳转
function onLeft(url,obj) {
    parent.$('#main').attr('src',url);
    $(obj).parent().parent().find('a').each(function(){
        $(this).removeClass('cur');
    });
    $(obj).addClass('cur');
}

function autoResize(id, a) {
    var objiframe = $('#' + id);
    if(isNaN(a)){
        a = 0;
    }
    $(objiframe).height(a);

    var finalHeight = $(objiframe).contents().height();
    $(objiframe).height(finalHeight);
}

function callParAutoResize(name,height){
    autoResize(name,height);
}


// JavaScript Document
//弹出框居中方法
function _pop(id){
    var obj = $('#'+id);

    var _w = $(window).width() //浏览器窗口宽度
    var _w = $(window).height() //浏览器窗口高度
    var _offsetW = obj.width(); //获取弹出框的宽度
    var _offsetH = obj.height(); //获取弹出框的高度

    var _left = ($(window).width()-obj.width())/2;
    var _top = ($(window).height()-obj.height())/2;

    obj.css({'left' : _left, 'top' : _top});
}

//当form表单回车后，回车失效
function shadowPop(){
    $("#popShadow").show();
    $("#pop").show();
    _pop("pop");
}
//二维码点击“打印”后显示弹出框
function myPrint(){
    $("#popShadow").show();
    $("#pop").show();
    $("#btn_com_blue").show();
    _pop("pop");
}

//当form表单回车后，回车失效
function shadowPop1(){
    if ($.trim($("#amount").val()).length<=0){
      $('#money').show();
      $('#money').html('<font color="red">请填写金额!</font>');
      return false;
    }
    $("#barCode").focus();
    $("#popShadow").show();
    $("#pop").show();
    _pop("pop");
}

//禁用回车事件
document.onkeydown = function(e) {
    //捕捉回车事件
    var ev = (typeof event!= 'undefined') ? window.event : e;    
    if(ev.keyCode == 13 && document.activeElement.id == "amount") {  
        if ($.trim($("#amount").val()).length<=0){
            $('#money').show();
            $('#money').html('<font color="red">请填写金额!</font>');
            return false;
        }
        $("#barCode").focus();
        shadowPop();  //弹出框居中显示方法
        return false;//禁用回车事件
    }
}


//关闭弹出框
function closePop(id){
    var obj = $("#"+id);
    clearInterval(intervalid);
    $("#popShadow").hide();
    $("#pop").hide();
    $("#barCode").focus();
}
//检测LODOP打印控件是否存在
function CheckIsInstall() {
    try{
        var LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
        if ((LODOP!=null)&&(typeof(LODOP.VERSION)!="undefined")) {
            location.href = "../alipay/qrcode";
        }
    }catch(err){
        //alert("Error:本机未安装或需要升级!");
    }
}

//财务管理伸缩
$(function(){
	$(".first").bind("click",function(){
		if($(this).hasClass("clickFirst")){
			$(this).removeClass("clickFirst");
		}
		else{$(this).addClass("clickFirst")
		}
		$(".businessCon").slideToggle()
	})
	
	$(".download").hover(function(){
		$(this).addClass("download01")
		},function(){
			$(this).removeClass("download01")
		})
	
	$(".all").bind("click",function(){
		n=$(".all").index(this)
		if($(this).hasClass("clickAll")){
			$(this).removeClass("clickAll");
		}
		else{$(this).addClass("clickAll")
		}
		$(".c").eq(n).slideToggle()
	})

    //树形菜单
    
    $(".rbArrow").click(function(){
        $(this).parent().find("ul").toggle();
         if ($(this).parent().find("ul").css("display")=="none") {
            $(this).parent().addClass("closeFolder");
        }else{
            $(this).parent().removeClass("closeFolder");
        }
    })
    $(".childBox,.parentBox,.parentsBox").click(function(){
        if ($(this).is(":checked")) {
            $(this).parent().find("input").prop("checked",true);
        }else{
            $(this).parent().find("input").prop("checked",false);
        }
    })
    $(".grandchildBox").click(function(){
    	var $grandchildNode = $(this).parents(".child2");
    	var grandchildBox1=$grandchildNode.children().find(".grandchildBox");
    	var grandchildBox2=$grandchildNode.parent().children().find(".grandchildBox");
    	var grandchildBox3=$(this).parents(".tree1").find(".grandchildBox");
        var arr1=[];
        var arr2=[];
       	var arr=[];
    	if ($(this).is(":checked")) {
    		$grandchildNode.children().prop("checked",true);
    		$(this).parents(".child1").children().prop("checked",true);
    		$(this).parents(".tree1").children().prop("checked",true);
    	}
    	for (var i = 0; i < grandchildBox3.length; i++) {
    		if (grandchildBox3[i].checked==false) {
    			arr.push(grandchildBox3[i]);
    		};
    	};
    	if (arr.length==grandchildBox3.length) {
    		$grandchildNode.children().prop("checked",false);
	    	$(this).parents(".child1").children().prop("checked",false);
	    	$(this).parents(".tree1").children().prop("checked",false);
    	};
	    for (var i = 0; i < grandchildBox1.length; i++) {
	    	if (grandchildBox1[i].checked==false) {
	    		arr1.push(grandchildBox1[i]);
	    	};
	    };
	    if (arr1.length==grandchildBox1.length) {
	    	$grandchildNode.children().prop("checked",false);
	    };
	    for (var i = 0; i < grandchildBox2.length; i++) {
	    	if (grandchildBox2[i].checked==false) {
	    		arr2.push(grandchildBox2[i]);
	    	};
	    };
	    if (arr2.length==grandchildBox2.length) {
	    	$(this).parents(".child1").children().prop("checked",false);
	    };
	    })
    $(".childBox").click(function(){
    	var $childNode = $(this).parent().parent();
    	var childBox1=$childNode.children().find(".childBox");
    	var childBox2=$(this).parents(".tree1").find(".childBox");
    	var arr1=[];
    	var arr=[];
    	if ($(this).is(":checked")) {
    		$childNode.parent().children().prop("checked",true);
    		$(this).parents(".tree1").children().prop("checked",true);
    	}
    	for (var i = 0; i < childBox2.length; i++) {
    		if (childBox2[i].checked==false) {
    			arr1.push(childBox2[i]);
    		};
    	};
    	if (arr1.length==childBox2.length) {
    		$childNode.parent().children().prop("checked",false);
    		$(this).parents(".tree1").children().prop("checked",false);
    	};
	    for (var i = 0; i < childBox1.length; i++) {
	    	if (childBox1[i].checked==false) {
	    		arr.push(childBox1[i]);
	    	};
	    };
	    if (arr.length==childBox1.length) {
	    	$childNode.parent().children().prop("checked",false);
	    };
	    })
    $(".parentBox").click(function(){
    	var $parentNode = $(this).parents(".tree1").children();
    	var parentBox = $(this).parents(".tree1").find(".parentBox");
    	var arr=[];
    	if ($(this).is(":checked")) {
    		$parentNode.prop("checked",true);
    	}
    	for (var i = 0; i < parentBox.length; i++) {
    		if (parentBox[i].checked==false) {
    			arr.push(parentBox[i]);
    		};
    	};
    	if (arr.length==parentBox.length) {
    		$parentNode.prop("checked",false);
    	};
	    })
})