
//1.
function material_multi_list_even(){
	$('.mutil .imgSet, .mutil .list').each(function(){
				
		//获取鼠标滑上去显示的层
		var control = $(this).find('.control');
		$(this).mouseover(function(){ control.css({display:'block'}); }); 
		$(this).mouseout(function(){ control.css({display:'none'}); }); 
		
	});
		
	//右边text的值变动后，左边对应的对象跟着改变并赋值给隐藏域
	$('.mod-form input').filter('[name="Material[title]"]').on('keyup paste blur', function(){
		var cur_id=$('.mutil').data('cur_id');  //取出存入的数据值
		$(cur_id+' input').eq(0).val($(this).val());
		$(cur_id+' .txt').html($(this).val());
	})
	//右边radiobutton的值变动后，左边对应的对象跟着改变并赋值给隐藏域
	$('.mod-form input').filter('[name="Material[jump_type]"]').on('change', function(){
		var cur_id=$('.mutil').data('cur_id');  //取出存入的数据值
		$(cur_id+' input').eq(5).val($(this).val());
		
		if( $('.mod-form input[name="Material[jump_type]"]').eq(0).is(":checked")){
	        $("#ckeditor").show();
	        $("#link").hide();
		}else{
	        $("#link").show();
	        $("#ckeditor").hide();
	        var url = $("#Material_link_content").val();
	        if(url == "0"){
	        	$(cur_id+' input').eq(1).val('');
	        }

		}
	})
	
	//右边的select的值改变后，左边对应的对象跟着改变并赋值给隐藏域
	$('.mod-form select').filter('[name="Material[link_content]"]').change(function(){
		var cur_id=$('.mutil').data('cur_id');
		$(cur_id+' input').eq(1).val($(this).val());
	});
	
	//右边textarea的值变动后，左边对应的对象跟着改变并赋值给隐藏域
	$('.mod-form textarea').on('keyup paste blur', function(){
		var cur_id=$('.mutil').data('cur_id');  //取出存入的数据值
		$(cur_id+' input').eq(3).val($(this).val());
	})
}

$(document).ready(function(e) {
    material_multi_list_even();
    
    //默认把第一个id数据存入到multi对象里
	$('.mutil').data('cur_id', '#'+$('.mutil .imgSet').attr('id'));
	
	//默认设置右边的初始值
	$('.mod-form input[name="Material[title]"]').val($('.imgSet input').eq(0).val());
	$('.mod-form select[name="Material[link_content]"]').find("option[value='"+$('.imgSet input').eq(1).val()+"']").prop("selected", true);
	$('.mod-form textarea[name="Material[content]"]').val($('.imgSet input').eq(3).val());
	$('.mod-form input[name="Material[jump_type]"]').removeAttr("checked");
	$('.mod-form input[name="Material[jump_type]"]').eq($('.imgSet input').eq(5).val()-1).prop("checked", true);
	if( $('.mod-form input[name="Material[jump_type]"]').eq(0).is(":checked")){
        $("#ckeditor").show();
        $("#link").hide();
	}else{
        $("#link").show();
        $("#ckeditor").hide();
	}
	$('.mod-form input[name="Material[jump_type]"]').click(function(){
		$("#main", parent.document).height($(".kkfm_r_inner").outerHeight())
	})
});

//添加项
function material_add(obj,i){
	this.blur();
	if($('.mutil .list:visible').size()>=5){
		alert('你最多只可以加入6条图文消息！');
		return false;
	}
	
	//计算并赋值剩余能添加几条
	$("#limitNum").html(4-$('.mutil .list:visible').size())
	$('.mutil .list, a[href*=#mod], a[href*=#del]').off();
	//添加一个随机id
	$('<div class="list" id="id_'+Math.floor(Math.random()*1000000)+'">'+$('.mutil .list:last').html()+'</div>').insertAfter($('.mutil .list:last'));
	//对新添加的默认值进行初始化
	$('.mutil .list:last').children('.txt').html('标题').siblings('.img').html('缩略图');
	$('.mutil .list:last input').each(function(){
		$(this).val('');                                             //清空value值
		var strNum = $(this).attr("name").substr(11,1);              //截取name中的数字
		var newName = $(this).attr("name").replace(strNum, i);       //用变量替换数字 
		$(this).attr("name",newName);                                //把替换后的值赋给name
	});
	//设置最后一个input的默认value
	$('.mutil .list:last input:eq(5)').attr("value", 1);
	$('.mutil .list:last input:eq(4)').attr("value", 1);
	
	material_multi_list_even();
};


//删除项
function material_del(obj,i){
	//假如.list的长度个数小于等于1时就提示不能删除，并返回false；
	if($('.mutil .list:visible').size() <= 1 ){
		alert('无法删除，多条素材至少需要2条信息!');
		return false;
	}else if(confirm('删除后不可恢复，继续吗？')){
		$(obj).parent().parent().hide();
		$('.multi .imgSet a[href*=#mod]').click();
		//如果右输入框是指到要删除的项，当该项删除后就把右输入框指向第一个
		material_mod('.mutil .imgSet a.edit',2);
		var cur_id='#'+ $('.mutil .imgSet a.edit').parent().parent().attr('id');
		last_id = cur_id;
		$('.mod-form').animate({marginTop:21});
		$("#title_error").html("");
		$("#img_error").html("");
		$("#content_error").html("");
	}
	//计算并赋值剩余能添加几条
	$("#limitNum").html(5-$('.mutil .list:visible').size())
	//type=hidden的第四个input对象
	$(obj).parent().parent().find("input").eq(4).val(i);
	
};


//其他点击修改把右边的修改框指向指定的位置, 右边的值做了修改后，左边对应的隐藏域的值也跟着修改
function material_mod(obj,i){	
	var imgSet = $(obj).parent().parent(); 
	var listH = $(obj).parent().height();
	var arrowMT = parseInt($('.mod-form .arrow').css("top") );   //获取右边箭头的top值
	
	//获取它的父元素相对于顶部的偏移
	var _pos = $(obj).parent().parent().offset();
	//获取第一个list的偏移值
	var _pos01 = $('.mutil .list').eq(0).offset();
	
	//获取control的相对于最顶部的偏移高度
	var materical_pos = $('.materical .left').offset();	
	$('.mod-form').animate({marginTop:_pos.top-materical_pos.top});		

	
	//获取当前的对象id
	var cur_id='#'+ $(obj).parent().parent().attr('id');
	//把id存入一个对象里
	$('.mutil').data('cur_id', cur_id);

	
	//把左边的输入值赋给右边的对象里面
	$('.mod-form input[name="Material[title]"]').val($(cur_id+' input').eq(0).val());
	$('.mod-form input[name="Material[cover_img]"]').val($(cur_id+' input').eq(2).val());
	$('.mod-form select[name="Material[link_content]"]').find("option[value='"+$(cur_id+' input').eq(1).val()+"']").prop("selected", true);
	
	//$('.mod-form input[name="Material[jump_type]"]').val($(cur_id+' input').eq(5).val());
	$('.mod-form input[name="Material[jump_type]"]').removeAttr("checked");
	$('.mod-form input[name="Material[jump_type]"]').eq($(cur_id+' input').eq(5).val()-1).prop("checked", true);
//	$(window.frames[0].document).find(".cke_show_borders").html($(cur_id+' input').eq(3).val());
	UE.getEditor('Material_content').setContent($(cur_id+' input').eq(3).val());
	
	if( $('.mod-form input[name="Material[jump_type]"]').eq(0).is(":checked")){
        $("#ckeditor").show();
        $("#link").hide();
	}else{
        $("#link").show();
        $("#ckeditor").hide();
	}
				
	//type=hidden的第5个input对象
	$(obj).parent().parent().find("input").eq(4).val(i);
	
	//自适应iframe的高度
	setTimeout(function(){parent.$('#main').height($(".kkfm_r_inner").outerHeight())},400);	
	
}	