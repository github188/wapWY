$(function(){
	/* 布局页-添加当前 nav 效果*/
	cur_url = window.location.href;
	var pathname = window.location.pathname;
	if (cur_url.indexOf('website/index') != -1 || pathname == '/'){
		nav_id=0;
	}else if (cur_url.indexOf('website/solution') != -1){
		nav_id=1;
	}else if (cur_url.indexOf('website/checkout') != -1 || cur_url.indexOf('website/channel') != -1 || cur_url.indexOf('website/marketing') != -1 || cur_url.indexOf('website/shop') != -1 || cur_url.indexOf('website/data') != -1 || cur_url.indexOf('website/crm') != -1 ){
		nav_id=2;
	}else if (cur_url.indexOf('website/case') != -1){
		nav_id=3;
	}else if (cur_url.indexOf('website/join') != -1 || cur_url.indexOf('website/apply') != -1){
		nav_id=4;
	}else if (cur_url.indexOf('website/news') != -1 || cur_url.indexOf('website/post') != -1){
		nav_id=5;
	}else if (cur_url.indexOf('website/about') != -1){
		nav_id=6;
	}else{
		nav_id = 'no';
	}
	if(nav_id != 'no'){
		$(".h-nav ul>li").eq(nav_id).find("a:first").addClass("h-nav-active");
	}

	/* 合作加盟 - 切换图片*/
	$(".join-help .help-item i").click(function(){
		//	先前亮图变暗
		var img_url = $('.join-help .cur_help_icon').css('background-image').replace("-c.png",".png");
		$('.join-help .cur_help_icon').css('background-image',img_url);
		$(".join-help .help-item i").removeClass('cur_help_icon');
		//	当前图片变亮
		var img_url2 = $(this).css('background-image').replace(".png","-c.png");
		$(this).addClass('cur_help_icon').css('background-image',img_url2);
		//	指针图标位移
		var cur_id = $(".join-help .help-item i").index(this);
		var left_away = 36+(29.5+96)*cur_id;
		$(".help-con .help-con-item").eq(cur_id).css("display","block").siblings().css("display","none");
		$(".join-line .icon-location").css("left",left_away+"px")
	})
		
});
