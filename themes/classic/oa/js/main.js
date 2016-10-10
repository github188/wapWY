$(function() {

    /* 第一级切换 */

    $(".nav-item > a").click(function(e) {
        e.stopPropagation();
        $(this).toggleClass("active");
        $(this).parents().siblings("ul").stop(true, true).slideToggle();
        $(this).parents(".nav-item-wrap").siblings().find(".second-nav").slideUp();
        $(this).parents(".nav-item-wrap").siblings().find(".nav-item > a")
            .removeClass("active")
            .find(".icon-arrow")
            .html("&#xe608");
        if ($(this).hasClass("active")) {
            $(this).find(".icon-arrow").html("&#xe609");
        } else {
            $(this).find(".icon-arrow").html("&#xe608");
        }
    });

    /* 第二级切换 */

    $(".second-nav > li > a").click(function(e) {
        e.stopPropagation();
        $(this).toggleClass("active");
        $(this).siblings("ul").stop(true, true).slideToggle();
        $(this).parent().siblings().find(".third-nav").slideUp();
        $(this).parent().siblings().children("a")
            .removeClass("active")
            .find(".icon-arrow")
            .html("&#xe608");
        if ($(this).hasClass("active")) {
            $(this).find(".icon-arrow").html("&#xe609");
        } else {
            $(this).find(".icon-arrow").html("&#xe608");
        }
    });

    /* 人员列表下拉显示 */

    $(".staff-list-item").click(function(e) {
        e.stopPropagation();
        $(this).toggleClass("active");
        $(this).siblings("ol").stop(true, true).slideToggle();
        $(this).parent().siblings().find("ol").slideUp();
        $(this).parent().siblings().find(".staff-list-item")
            .removeClass("active")
            .find(".icon-arrow")
            .html("&#xe608");
        if ($(this).hasClass("active")) {
            $(this).find(".icon-arrow").html("&#xe609");
        } else {
            $(this).find(".icon-arrow").html("&#xe608");
        }
    });

    $(".staff-com-name").click(function(e) {
        e.stopPropagation();
        $(this).toggleClass("active");
        $(this).siblings("ul").stop(true, true).slideToggle();
        $(this).parent().siblings().find("ul").slideUp();
        $(this).parent().siblings().find(".staff-com-name")
            .removeClass("active")
            .find(".icon-arrow")
            .html("&#xe608");
        if ($(this).hasClass("active")) {
            $(this).find(".icon-arrow").html("&#xe609");
        } else {
            $(this).find(".icon-arrow").html("&#xe608");
        }
    });

    /* 使左侧的高度与右侧相等 */

    var lheight = $(".h-con-l").height();
    var winheight = $(document).height();
    if (lheight < winheight) {
        $(".h-con-l").height(winheight - 100);
    } else {
        $(".h-con-l").height(winheight);
    }

    /* 全选 */

    $(".checkboxall").click(function() {
        var input = $(this).children("input");
        var checkbox = $(this).parent().siblings(".table").find("input[type='checkbox']");
        if (input.is(":checked")) {
            checkbox.prop("checked", "checked");
        } else {
            checkbox.prop("checked", "");
        }
    });

    //更多筛选

    $("#iconfont-more").click(function() {
        $(this).siblings("#more-area").stop(true).toggle();
        var display = $("#more-area").css("display");
        var iconfont = $(this).find(".iconfont");
        if (display === "block") {
            $(this).children("span").text("精简筛选");
            iconfont.html("&#xe61e");
        } else {
            $(this).children("span").text("更多筛选");
            iconfont.html("&#xe609");
        }
    });

    // 服务运营商切换

    $(".detail-switch .btn").click(function() {
        $(this).addClass("active-wq")
            .siblings()
            .removeClass("active-wq");
        var _index = $(this).index();
        $(this).parent()
            .siblings(".detail-inner").eq(_index).show()
            .siblings(".detail-inner").hide();
    });

    //左边导航选中高亮

    var urlstr = location.href;
    var urlstatus = false;
    $(".link").each(function() {
        if ((urlstr + '/').indexOf($(this).attr('href')) > -1 && $(this).attr('href') != '') {
            $(this).addClass('nav-active');
            urlstatus = true;
            $(this).parents().show();
        } else {
            $(this).removeClass('nav-active');
        }
    });
    if (!urlstatus) {$(".home-item .link").addClass('nav-active')};
})