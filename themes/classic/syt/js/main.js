// JavaScript Document

//iframe框架高度自适应
function autoResize(id,a){
	var objiframe = $('#' + id);
	if(isNaN(a)){
		a = 0;
	}
	$(objiframe).height(a);	
	var finalHeight = $(objiframe).contents().height();
	$(objiframe).height(finalHeight);
}

//弹出框居中方法
function _pop(id){
	var obj = $('#'+id);

	var _w = $(window).width() //浏览器窗口宽度
	var _h = $(window).height() //浏览器窗口高度

	var _offsetW = obj.width(); //获取弹出框的宽度
	var _offsetH = obj.height(); //获取弹出框的高度
	
	var _left = (_w-_offsetW)/2; 
	var _top = (_h-_offsetH)/2; 
			
	obj.css({'left' : _left, 'top' : _top});
}


//当form表单回车后，回车失效
function shadowPop(){
	$("#popShadow").show();
	$("#pop").show();
	_pop("pop");
}


//关闭弹出框
function closePop(id){
	var obj = $("#"+id);
	$("#popShadow").hide();
	$("#pop").hide();
	$("#barCode").focus();
}

//只允许输入整数和小数点
//调用方式：onkeydown = "onlyNum(this,event)"
//8：退格键; 46：delete; 37-40: 方向键
//48-57:小键盘区的数字； 96-105：主键盘区的数字
//110\190:小键盘区和主键盘区的小数
function onlyNum(el,ev) {
	var event = ev || window.event;  
	var currentKey = event.charCode || event.keyCode;  //ie、FF下获取键盘码
	
	//当输入的值为空，按回车是提示输入用户输入金额
	if(currentKey == 13 && el.value == ""){
		alert("请输入收款金额");
	}else if(currentKey == 13 && document.activeElement.id == "amount") { //禁用回车事件
		$("#barCode").focus();
		shadowPop();  //弹出框居中显示方法
		return false;//禁用回车事件			
	}
	
	//除了48~57,96~105,两个小数点，左右键，退格键外其他的键不能输入
    if(!((currentKey>=48&&currentKey<=57)||(currentKey>=96&&currentKey<=105)||(currentKey == 110 && el.value.indexOf(".") < 0)||(currentKey == 190 && el.value.indexOf(".") < 0) || currentKey == 37 || currentKey == 39 || currentKey == 8)){
		if(window.event){
			event.returnValue=false;
			return false;
		}else{
			event.preventDefault(); 
		}
		return;
	}
    //左右键，退格键不限制
    if(currentKey == 37 || currentKey == 39 || currentKey == 8) {
    	return;
    }
    //输入的光标位置
    var pos = '0';
    if(document.selection) {
    	el.focus(); //光标位置不变
    	var sel = document.selection.createRange();
    	sel.moveStart('character', -el.value.length);
    	pos = sel.text.length;
    }else if(el.selectionStart || el.selectionStart == '0') {
    	pos = el.selectionStart;
    }
    //获取输入框的光标左边的值
	var left = el.value.substring(0, pos); //从开始位置到光标位置
	//获取输入框的光标右边的值
	var right = el.value.substring(pos); //从光标位置到结束
	//小键盘键码转换
	if(currentKey >= 96 && currentKey <= 105) {
		currentKey -= 48;
	}
	if(currentKey == 110) {
		currentKey = 190;
	}
	//获取待输入的值
	var middle = String.fromCharCode(currentKey); //将Unicode值转为字符串
	//alert(pos+','+middle)
	//获取输入后将得到的值
	var txt = left + middle + right;
	//金额范围检查，小数点位数检查
	//金额不能大于1亿   小数位数限制2位以内
	if(txt > 99999999.99 || (txt.indexOf(".") > -1 && txt.length - txt.indexOf(".") - 1 > 2)) {
		//撤销输入
		if(window.event){
			event.returnValue=false;
			return false;
		}else{
			event.preventDefault(); 
		}
	}

}

//初始化高度自适应
function autoHeight(a,b,c){
	var obj_a = $("."+a); //头部
	var obj_b = $("."+b); //主体部分
	var obj_c = $("."+c); //尾部
	
	obj_b.css({"min-height":$(document).height()-obj_a.height()-obj_c.outerHeight()});
};

$(function(){
	autoHeight('kkf_loginBD','kkf_loginBD','footer');
	autoHeight('header','kkfmain','footer');
})




function navClick(obj){
	if (!document.getElementsByTagName) return false;
	if (!document.getElementById) return false;
	if (!document.getElementById("handles1")) return false;
	obj.className="current";
	var id;
	var divId;
	var linkid;
	var handle=document.getElementById("handles1");
	var links=handle.getElementsByTagName("a");
	for(i=1; i <= links.length; i++){
		id="nav"+i;
		linkid=document.getElementById(id);
		divid=document.getElementById("b9e43e-"+i);
		if(id != obj.id){
			linkid.className="lnk";
			divid.style.display ="none";
		} else {
			divid.style.display = "block";
		}
	}
}

//
//var main_obj = {
//	list_init:function(){
//		//编辑操作
//		$('.operate').click(
//			function(){ 
//			    if($(this).find("dd").attr("display") == "none"){
//					alert(0)
//					$(this).find("dd").show();
//				}else{
//					alert(1)
//					$(this).find("dd").hide();
//				}
//         
//				
//			}
//		);
//		
//		$(".operate a").click(function(){
//			var html = $(this).html();
//			
//			$(this).blur();
//			$(this).parent().prev().html(html+'<b></b>');
//			$(this).parents("dl").removeClass('oper_hover')
//		});
//			
//		
//	}
//}
var main_obj = {
	list_init:function(){
		//编辑操作
		$('.operate').hover(
			function(){ 
				//得到当前对象距离底部的距离
				var pos = $(document).height() - $(this).offset().top;
				//当前对象隐藏的子对象的高度
				var height = $(this).find("dd").height();	
				
				//判断假如显示层高度大于该层里底部的距离，则加“oper_hover_up”，否则则加“oper_hover”
				if(height > pos){
					$(this).css({"z-index":11}).addClass('oper_hover_up'); 
				}else{
					$(this).css({"z-index":11}).addClass('oper_hover'); 
				}
			},
			function(){ 
				if($(this).hasClass("oper_hover_up")){
					$(this).css({"z-index":10}).removeClass('oper_hover_up') 
				}
				if($(this).hasClass("oper_hover")){
					$(this).css({"z-index":10}).removeClass('oper_hover') 
				}
			}
		);
		
		$(".operate a").click(function(){
			var html = $(this).html();
			
			$(this).blur();
			$(this).parent().prev().html(html+'<b></b>');
			$(this).parents("dl").removeClass('oper_hover')
		});
			
		
	}
}

$(function(){
  if( !('placeholder' in document.createElement('input')) ){  
   
    $('input[placeholder],textarea[placeholder]').each(function(){   
      var that = $(this),   
      text= that.attr('placeholder');   
      if(that.val()===""){   
        that.val(text).addClass('placeholder');   
      }   
      that.focus(function(){   
        if(that.val()===text){   
          that.val("").removeClass('placeholder');   
        }   
      })   
      .blur(function(){   
        if(that.val()===""){   
          that.val(text).addClass('placeholder');   
        }   
      })   
      .closest('form').submit(function(){   
        if(that.val() === text){   
          that.val('');   
        }   
      });   
    });   
  }  
})