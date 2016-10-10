$(function() {
    /* 首页轮播图 */
    $('.bxslider1').bxSlider({
        auto: true,
        controls: false,
        autoHover: true
    });
    $('.bxslider2').bxSlider({
        autoHover: true,
        pager: false
    });

    /* 关于我们导航切换 */
    $(".about-nav li").click(function() {
        $(this).addClass("about-nav-active")
            .siblings()
            .removeClass("about-nav-active");
        $(".nav-icon").removeClass("nav-icon-active")
        $(this).find(".nav-icon").addClass("nav-icon-active");
        var _index = $(this).index();
        $(".aboutus").eq(_index).show().stop(true, true).siblings(".aboutus").hide();
    })

    /* 商家案例切换 */
    $(".brand-logo").click(function() {
        $(this).addClass("brand-logo-active").siblings().removeClass("brand-logo-active");
        var _index = $(this).index();
        $(this).parents(".case-brand").find(".brand-con-item").eq(_index).show().stop(true, true).siblings().hide();
    })

    $(".case-l li").click(function() {
        $(this).addClass("case-l-active").siblings().removeClass("case-l-active");
        var _index = $(this).index();
        $(".case-brand").eq(_index).show().stop(true, true).siblings().hide();
    })

    /* 微信二维码弹出 */
    $(".wechat").hover(function() {
        $(".weixin_code").toggle();
    })

    var $pagetop = $('#pagetop');
    $pagetop.css({
        'display': 'none',
        'position': 'fixed',
        'bottom': '20px',
        'right': '20px',
        'z-index': '100'
    });
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 500) {
            $pagetop.fadeIn();
        } else {
            $pagetop.fadeOut();
        }
    });
    $pagetop.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 500, 'linear');
    });
 
    $(".picScroll-left").slide({ titCell: ".hd ul", mainCell: ".bd ul", autoPage: true, effect: "left", autoPlay: true, vis: 4 });
});
