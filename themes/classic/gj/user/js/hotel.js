$(function(){

	/* 轮播图 */

	 var mySwiper = new Swiper ('.swiper-container', {
      loop: true,
      autoplay: 4000,
      pagination: ".swiper-pagination",
      autoplayDisableOnInteraction: false,
    });  

	/* 客房预订 */

    $(".booking-hotel .hotel-name").click(function(){
    	$(this).siblings().stop(true).fadeToggle();
        $(this).find(".fa").toggleClass("fa-angle-right").toggleClass("fa-angle-down");
    });

    $(".room-d .room-d-inner img").css({"width":"100%","height":"auto"});

    /* 我的订单悬停 需放在最后面*/
    var $os = $(".cover .order-status");
    var offset = $os.offset();
    var h = offset.top;
    $(window).scroll(function(){
    	if ($(this).scrollTop() > h) {
    		$os.addClass("fixed");
    	}else{
    		$os.removeClass("fixed");
    	}
    })

})