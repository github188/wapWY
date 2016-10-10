/***
 * ����ԭ��������Ϣ��ʾJquery���
 * ��дʱ�䣺2012��8��23��
 * version:manhua_bubbletips.js
***/
$(function() {
	$.fn.manhua_bubbletips = function(options) {
		var defaults = {			
			position : "t",			//��ͷָ����(t)����ͷָ����(b)����ͷָ����(l)����ͷָ����(r)
			value : 15,				//С��ͷƫ����ߺ��ϱߵ�λ��
			content : ""			//����
			
		};
		var options = $.extend(defaults,options);		
		var offset = $(this).offset();
		var bid = parseInt(Math.random()*100000);		
		$("body").prepend('<div class="docBubble" id="btip'+bid+'"><i class="triangle-'+options.position+'"></i> <i class="close" title="关闭" id="btipc'+bid+'">关闭</i><div class="tl"><div class="inner"><div class="cont">'+options.content+'</div></div></div><div class="tr"></div><div class="bl"></div></div>');
		var $btip = $("#btip"+bid);
		var $btipClose = $("#btipc"+bid);
		var h = $(this).height();
		var w = $(this).width();	
		switch(options.position){
			case "t" ://�����������ʱ��
				$(".triangle-t").css('left',options.value);
				$btip.css({ "left":offset.left+w/2-options.value  ,  "top":offset.top+h+14  });
				break;
			case "b" ://�����������ʱ��
				$(".triangle-b").css('left',options.value);
				$btip.css({ "left":offset.left+w/2-options.value  ,  "top":offset.top-h-7-$btip.height()  });
				break;
			case "l" ://��������ߵ�ʱ��		
				$(".triangle-l").css('top',options.value);
				$btip.css({ "left":offset.left+w+10  ,  "top":offset.top+h/2-7-options.value });
				break;
			case "r" ://�������ұߵ�ʱ��			
				$(".triangle-r").css('top',options.value);
				$btip.css({ "left":offset.left-w+25-$btip.width()  ,  "top":offset.top+h/2-7-options.value });
				break;
		}
		$btipClose.on("click",function(){
		  $btip.hide();	
		});
	}
});