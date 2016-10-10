
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

    //导航内容跳转
function onNav(leftUrl,mainUrl,obj,tag){
    $('#left').attr('src', leftUrl);
    $('#main').attr('src', mainUrl);
    $(obj).parent().parent().find('a').each(function(){
        $(this).removeClass('current');
    });
    $(obj).addClass('current');
    $('#curTab').html(tag);
    return false;
}

//左侧功能连接跳转
function onLeft(url,tag,obj){
    parent.$('#main').attr('src',url);
    parent.$('#curTab').html(tag);
    $(obj).parent().parent().find('a').each(function(){
        $(this).removeClass('red');
    });
    $(obj).parent().parent().parent().parent().find('a').each(function(){
        $(this).removeClass('red');
    });
    $(obj).addClass('red');
}

//左侧功能展开收缩
function openmenu(_this){
    if($(_this).parent().parent().children('.uldiv').is(":hidden")){
        $(_this).parent().parent().children('.uldiv').show();
        $(_this).children('img').attr('src','<?php echo MGR_STATIC_IMAGES?>/minus.gif');
    }else{
        $(_this).parent().parent().children('.uldiv').hide();
        $(_this).children('img').attr('src','<?php echo MGR_STATIC_IMAGES?>/jia.gif');
    }
    callParAutoResize('left',$('.sidercontent').height());
}

function autoResize(id,a){
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

function strtotime(strings){
    var arr1 = strings.split("-");
    var year = arr1[0];
    var month = arr1[1]-1;
    var day = arr1[2];
    var timestamp = new Date(year,month,day).getTime()/1000;
    return timestamp;
}