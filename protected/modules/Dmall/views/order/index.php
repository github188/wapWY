
<body>
<!--ShearPhoto2.3 免费，开源，兼容目前所有浏览器，纯原生JS和PHP编写
 从shearphoto 1.5直接跳跃到shearphoto 2.0，这是shearphoto重大革新。本来我是想shearphoto 1.6 、1.7、 1.8 慢慢升的，但是这样升级只会让shearphoto慢慢走向灭亡！
结果我又辛苦了一个多星期，把shearphoto 2.0升级完成！
shearphoto2.0之前，我认为没必要加入HTML5，兼容IE6 7 8就够。但是直到后来！我知道这是我一个错误的决定
因为用户并没有为shearphoto 1.5埋单，原因shearphoto 1.5没有采用HTML5截取，用户觉得会增加服务器负载！而且又不是本地加载图片！我一个错误的决定！导致用户份额一直没有明显大增。

   shearphoto 2.0是收集所有用户的意见开发而成的！

   重大的特性就是全面支持HTML5
 
1：支持translate3d 硬件加速移动

2：加入图片预览功能
 
3：加入了压缩数码相机图片， 以及HTML5 canvas本地切图,截图
   但依然继续支持IE6 7  8 哦！有人问IE6 7 8不兼容HTML5啊，你骗人吗？
   先不要急！shearphoto 2.0的机制是这样的：有HTML5则使用HTML5 canvas切图，截图，JS先会截取一张最合理化的截图，然后交给后端，根据用户设置，进行压缩截图
   没有HTML5的浏览器则采用先上传再截取的策略，就是原先1.5的策略。
   当然有些用户如果不喜欢HTML5，也可以根据接口自行关闭

4：加HTML5图片特效，就一如美图秀秀这样的特效！shearphoto一共提供14种漂亮特效，感谢腾讯AI对图片特效提供支持
   shearphoto 2.0增添很多接口，大部份是HTML5的设置！请下载源码体验


  shearphoto外忧内患，已经没退路了。在WEB截图界，shearphoto必须要干个第一！.shearphoto不再局限于头像截取，shearphoto更可用于商城的商品图片编辑。
  shearphoto含HTML5图片压缩功能！一张十M 二十M的图，照样能帮你压成你要的容量和尺寸，
一般情况下！商城的商品图片都是通过数码相机拍摄后，要用PHOTOshop把数码相机内几十M的图片，压缩，截取，然后才能上传到商城服务器！
这样的操作是不是太麻烦了！ 如果你使用shearphoto你就快捷很多了，shearphoto你只需要直接选择图片，就会帮你进行压缩截取，上传，添加到数据库。这样的一条龙服务！
shearphoto 2.0的机制是无可挑剔的！但是不排除有BUG存在，如果用户遇到BUG情况，请到论坛 或官方QQ群进行反馈，我会第一时间发布补丁！
 shearphoto支持PHP和JAVA，JAVA由网友所写，但是JAVA版并不是太完善，使用的是以前的shearphoto1.3！我 一直很想把NET  python nodejs  JAVA的很完善地做出来！
可惜个人能力有限，如果你喜欢shearphoto，你又会玩 NET  python nodejs  JAVA，又想为互联网做贡献，那么你可以加入shearphoto团队，把这些后端版本做出来。但shearphoto没有薪水给你！
shearphoto免费开源的，没有利润可图，纯粹是抱着为互联网做贡献的心态！如果你跟我一样，请加入到shearphoto后端开发



浏览器支持：
兼容IE 6 及以上的所有浏览器

后端支持：
支持PHP5.X 至 PHP7.0或以上
支持JAVA后端（shearphoto1.3开发）

系统支持：
支持linux WINDOW服务器
shearphoto采用原生JS面向对象 + 原生PHP面向对象开发，绝对不含JQ插件，对JQ情有独忠的，这个插件不合适你                                                     
 
*******************************ShearPhoto2.3 免费，开源，兼容目前所有浏览器，纯原生JS和PHP编写-->
<!--头部-->
<div class="header">
        <img src="shearphoto_common/images/logo.png" alt="ShearPhoto官方网站：www.shearphoto.com" title="ShearPhoto官方网站：www.shearphoto.com">
 <a href="http://www.shearphoto.com/bbs" title="shearphoto官方论坛">进入官方论坛</a> 
        <strong>
                ShearPhoto 作者：明哥先生 QQ399195513-兼容所有浏览器，乞今最好的切图，HTML5截图工具！
  </strong>
</div>
<!--头部结束-->
<!--主功能部份 主功能部份的标签请勿随意删除，除非你对shearphoto的原理了如指掌，否则JS找不到DOM对象，会给你抱出错误-->
<div id="shearphoto_loading">程序加载中......</div><!--这是2.2版本加入的缓冲效果，JS方法加载前显示的等待效果-->
 <div id="shearphoto_main">
<!-- <!--效果开始.............如果你不要特效，可以直接删了........--> -->
<!-- <div class="Effects" id="shearphoto_Effects" autocomplete="off"> -->
<!--   <strong class="EffectsStrong">截图效果</strong> -->
<!--   <a href="javascript:;" StrEvent="原图" class="Aclick"><img src="shearphoto_common/images/Effects/e0.jpg"/>原图</a> -->
<!--   <a href="javascript:;" StrEvent="美肤"><img src="shearphoto_common/images/Effects/e1.jpg"/>美肤效果</a> -->
<!--   <a href="javascript:;" StrEvent="素描"><img src="shearphoto_common/images/Effects/e2.jpg"/>素描效果</a> -->
<!--   <a href="javascript:;" StrEvent="自然增强"><img src="shearphoto_common/images/Effects/e3.jpg" />自然增强</a> -->
<!--   <a href="javascript:;" StrEvent="紫调"><img src="shearphoto_common/images/Effects/e4.jpg" />紫调效果</a> -->
<!--   <a href="javascript:;" StrEvent="柔焦"><img src="shearphoto_common/images/Effects/e5.jpg"/>柔焦效果</a> -->
<!--   <a href="javascript:;" StrEvent="复古"><img src="shearphoto_common/images/Effects/e6.jpg"/>复古效果</a> -->
<!--   <a href="javascript:;" StrEvent="黑白"><img src="shearphoto_common/images/Effects/e7.jpg"/>黑白效果</a> -->
<!--   <a href="javascript:;" StrEvent="仿lomo"><img src="shearphoto_common/images/Effects/e8.jpg"/>仿lomo</a> -->
<!--   <a href="javascript:;" StrEvent="亮白增强"><img src="shearphoto_common/images/Effects/e9.jpg"/>亮白增强</a> -->
<!--   <a href="javascript:;" StrEvent="灰白"><img src="shearphoto_common/images/Effects/e10.jpg"/>灰白效果</a> -->
<!--   <a href="javascript:;" StrEvent="灰色"><img src="shearphoto_common/images/Effects/e11.jpg"/>灰色效果</a> -->
<!--   <a href="javascript:;" StrEvent="暖秋"><img src="shearphoto_common/images/Effects/e12.jpg"/>暖秋效果</a> -->
<!--   <a href="javascript:;" StrEvent="木雕"><img src="shearphoto_common/images/Effects/e13.jpg"/>木雕效果</a> -->
<!--   <a href="javascript:;" StrEvent="粗糙"><img src="shearphoto_common/images/Effects/e14.jpg"/>粗糙效果</a> -->
<!-- </div> -->
<!-- <!--效果结束...........................如果你不要特效，可以直接删了.....................................................--> 
<!--primary范围开始-->
   <div class="primary"> 
     <!--main范围开始-->
        <div id="main">
           <div class="point">
                </div>
                <!--选择加载图片方式开始-->
                <div id="SelectBox">
                <form    id="ShearPhotoForm" enctype="multipart/form-data" method="post"  target="POSTiframe"> 
                <input name="shearphoto" type="hidden" value="我要传参数" autocomplete="off"> <!--示例传参数到服务端，后端文件UPLOAD.php用$_POST['shearphoto']接收,注意：HTML5切图时，这个参数是不会传的-->
                        <a href="javascript:;" id="selectImage"><input type="file"  name="UpFile" autocomplete="off"/></a>
                 </form>           
                        <a href="javascript:;" id="PhotoLoading"></a>
                        <a href="javascript:;" id="camerasImage"></a>
                </div>
                <!--选择加载图片方式结束--->
                <div id="relat">
                        <div id="black">
                        </div>
                        <div id="movebox">
                                <div id="smallbox">
                                        <img src="shearphoto_common/images/default.gif" class="MoveImg" /><!--截框上的小图-->
                                </div>
                                <!--动态边框开始-->
                                 <i id="borderTop">
                                </i>
                                
                                <i id="borderLeft">
                                </i>
                                
                                <i id="borderRight">
                                </i>
                                
                                <i id="borderBottom">
                                </i>
                                <!--动态边框结束-->
                                <i id="BottomRight">
                                </i>
                                <i id="TopRight">
                                </i>
                                <i id="Bottomleft">
                                </i>
                                <i id="Topleft">
                                </i>
                                <i id="Topmiddle">
                                </i>
                                <i id="leftmiddle">
                                </i>
                                <i id="Rightmiddle">
                                </i>
                                <i id="Bottommiddle">
                                </i>
                        </div>
                        <img src="shearphoto_common/images/default.gif" class="BigImg" /><!--MAIN上的大图-->
                </div>
        </div>
         <!--main范围结束-->  
          <div style="clear: both"></div>
        <!--工具条开始-->
        <div id="Shearbar">
                <a id="LeftRotate" href="javascript:;">
                        <em>
                        </em>
                        向左旋转
                </a>
                <em class="hint L">
                </em>
                <div class="ZoomDist" id="ZoomDist">
                        <div id="Zoomcentre">
                        </div>
                        <div id="ZoomBar"> 
                        </div>
                        <span class="progress">
                        </span>
                </div>
                <em class="hint R">
                </em>
                <a id="RightRotate" href="javascript:;">
                        向右旋转
                        <em>
                        </em>
                </a>
                <p class="Psava">
                        <a id="againIMG"  href="javascript:;">重新选择</a>
                        <a id="saveShear" href="javascript:;">保存截图</a>
                </p>
        </div>
        <!--工具条结束-->
    </div>   
     <!--primary范围结束-->
        <div style="clear: both"></div>
        </div>
<!--shearphoto_main范围结束-->

<!--主功能部份 主功能部份的标签请勿随意删除，除非你对shearphoto的原理了如指掌，否则JS找不到DOM对象，会给你抱出错误-->        
 <!--相册-->
<div id="photoalbum"><!--假如你不要这个相册功能。把相册标签删除了，JS会抱出一个console.log()给你，注意查收，console.log的内容是告诉，某个DOM对象找不到-->
<h1>假如：这是一个相册--------试试点击图片</h1>
<i id="close"></i>
<ul>
<li><img src="shearphoto_common/file/photo/1.jpg" serveUrl="file/photo/1.jpg" /></li>  <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/2.jpg" serveUrl="file/photo/2.jpg" /></li>   <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/3.jpg" serveUrl="file/photo/3.jpg" /></li>  <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/4.jpg" serveUrl="file/photo/4.jpg" /></li> <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/5.jpg" serveUrl="file/photo/5.jpg" /></li> <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/6.jpg" serveUrl="file/photo/6.jpg" /></li> <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/7.jpg" serveUrl="file/photo/7.jpg" /></li> <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
<li><img src="shearphoto_common/file/photo/8.jpg" serveUrl="file/photo/8.jpg" /></li><!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
</ul>
</div>
<!--相册-->
<!--拍照-->
<div id="CamBox"><!--假如你不要这个拍照功能。把拍照标签删除了，JS会抱出一个console.log()给你，注意查收，console.log的内容是告诉，某个DOM对象找不到-->
<p class="lens"></p>
<div id="CamFlash"></div>
<p class="cambar">
<a href="javascript:;" id="CamOk"  >拍照</a>
<a href="javascript:;" id="setCam">设置</a>
<a href="javascript:;" id="camClose">关闭</a>
<span style="clear:both;"></span>
</p>
<div id="timing"></div>
</div>
<!--拍照-->
<!--底部 尽可能地保留作者肖像不要删，作者的肖像一如天安门毛泽东头一样神圣，请你保留着-->  
  <div class="bottom">
   ShearPhoto作者：
   <span><img src="shearphoto_common/images/Author.png" title="这是shearphoto作者肖像，请你保留，不要删除" /> </span> 明哥先生 QQ399195513&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;ShearPhoto兼容目前所有浏览器&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;ShearPhoto官方网站：
   <a href="http://www.shearphoto.com" >www.shearphoto.com</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;官方QQ群：461550716 
   <br /> Copyright &copy; 2015 明哥先生. All Rights Reserved
 <!--作者统计使用量，你可以删走 -->
  <script language="javascript" type="text/javascript" src="http://js.users.51.la/18250727.js"></script>
<noscript><a href="http://www.51.la/?18250727" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/18250727.asp" style="border:none" /></a></noscript>
   <!--作者统计使用量，你可以删走 -->
  </div>
  <!--底部 尽可能地保留作者肖像不要删,删，作者的肖像一如天安门毛泽东头一样神圣，请你保留着-->
  
  
  <!--这里可以删走，纯粹给文盲看的-->
  <script>
 if(window.location.host ==="")//没有域名，肯定是SB直接点开index.html
{
document.body.innerHTML="";	 //清除那些SB的页面，不让他看
alert("有病是吧！\n\n你直接点开index.html，程序是不可用的。我已经说过无数遍。\n\n必须要后端环境，后端环境！什么叫后端环境，PHP，JAVA等等后端环境！清楚了吗？\n\n2.0版本之前的事，那次一个SB加我QQ，第一句就说：为什么shearphoto用不了，我就问：\"你有乱改过里面代码吗？\",他说：\"没有\"。我就说怪了，有什么可能！我叫他发浏览器URL地址看下，我看到是这种地址:d:\\xxxx\\shearphoto\\index.html!  我当时很生气！我还是忍住，我说要用http(s)打开的。\"他就来一句：\"别人的JS都能直接点开就能用的，shearphoto是垃圾\"我忍不住跟上一句：\"靠你个妈，给我滚\"");
//给SB弹出提示，让他改过自身！摆脱文盲
window.close();//关了SB的页面
}
  </script>
  <!--这里可以删走，纯粹给文盲看的-->
</body>
</html>
