// JavaScript Document
function openmenu(menu,level,img){
	var obj_menu=document.getElementById(menu);
	var obj_img=document.getElementById(img);	
	var obj_level=document.getElementById(level);	
	if(obj_menu.style.display == 'none'){
		obj_menu.style.display ="block";
		
		obj_img.src = "/kuaikuaifu/mgr/themes/classic/images/minus.gif";
		obj_level.style.borderBottom='none';
	}else{
		obj_menu.style.display="none";
		obj_img.src = "/kuaikuaifu/mgr/themes/classic/images/jia.gif";
		obj_level.style.borderBottom='1px solid #efefef';
	}
	autoSize();
}

function autoSize(){
	
	var objIfrm=parent.document.getElementById('menu');
	var objInIfrm=document.documentElement;
	objIfrm.style.height=objInIfrm.scrollHeight+"px";
}