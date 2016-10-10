//调整iframe高
/*function autoResize(targObj,frameName){

var targWin = targObj.parent.document.frames[frameName];
try {
	targWin.style.height=targObj.document.body.scrollHeight;

	}catch(e){}
}
*/


/*function subFrameDoc(iframeId){
	try{
		var frameDoc;
	
		if(document.all){
			try{
				frameDoc = window.frames[iframeId].document;
			}catch(e){
				try{
					frameDoc = parent.window.frames[iframeId].document;
				}catch(e){}//alert("Your IE´s version is to old!\nPlease try some new one!");
			}
		}else{
			try{
				frameDoc = document.getElementById(iframeId).contentWindow.document;
			}catch(e){
				try{
				frameDoc = parent.document.getElementById(iframeId).contentWindow.document;
				}catch(e){}//alert("Your Firefox´s version is to old!\nPlease try some new one!");
		}
	}
	
		return frameDoc ? frameDoc : null;
		}catch(e){return false;}
	}
	
}*/

/*设置框架的最小高度*/
function f_frameStyleResize(frameId,frameName){
	var dyniframe=null;
	var iframename=null;
	
	if(document.getElementById){
		if(!frameId) {  
            frameId = "contentFrame";  
			frameName = "contentFrame";  
        }  
		dyniframe=document.getElementById(frameId);
		
		var contentHeight;
		//iframename=window.frames[frameName];
		//alert(iframename.name);
		//iframename.document.getElementById('submenu_goods').style.height='400px';
		if (window.frames[frameName].document.body.scrollHeight > window.frames[frameName].document.body.offsetHeight){ // all but Explorer Mac  
			contentHeight = window.frames[frameName].document.documentElement.scrollHeight + 20;  
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari  
			contentHeight = window.frames[frameName].document.body.offsetHeight;  
		}
		alert(typeof window.frames[frameName].document);
		dyniframe.style.height = ((!contentHeight || contentHeight < 600) ? 600 : contentHeight)+"px"; 
		
	}
 //var targWin = targObj.parent.document.all[targObj.name];
	
}

/*# function iframeResize(frameId, frameName) {  
#     var dyniframe   = null;  
#     var indexwin    = null;  
#     if (document.getElementById){  
#         if(!frameId) {  
#             frameId = "contentFrame";  
#             frameName = "contentFrame";  
#         }  
#         dyniframe       = document.getElementById(frameId);  
#         indexwin        = window;  
#         if (dyniframe){  
#             var contentHeight = window.frames[frameName].document.body.scrollHeight;  
#             dyniframe.height = (!contentHeight || contentHeight < 600) ? 600 : contentHeight;  
#         }  
#     }  
# }  */     


function f_iframeResize(){

	bLoadComplete = true;
 	f_frameStyleResize(self);

}

var bLoadComplete = false;
window.onload = f_iframeResize;
