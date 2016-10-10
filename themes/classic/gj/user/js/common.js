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