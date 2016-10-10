/*
* SWFUpload上传图片
* @param buttonId 图片按钮id
* @param imgId    存储图片值得id
* @param imgDivId 显示上传图片的div ID
* @param floder   保存的文件夹
* @param showFloder  显示图片的文件夹
* @param imgThumb 图片重画大小
* */
function uploadImg(setting){

	$("#"+setting.buttonId).uploadify({
        uploader: setting.upload_path,// 服务器处理地址
        swf: setting.swf,
        buttonText: "选择图片",//按钮文字
        height: 30,  //按钮高度
        width: 120, //按钮宽度
        //uploadLimit: 5,//最多上传5张
        fileTypeExts: setting.fileTypeExts,//允许的文件类型
        fileTypeDesc: "请选择图片文件", //文件说明
        formData: { 'folder': setting.folder, 'encrypt': setting.encrypt }, //提交给服务器端的参数
        onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
            eval("var jsondata = " + data + ";");
            var fileName = jsondata['fileName'];
            $("#"+setting.showId).append("<li><a href='"+setting.src + fileName + "'><img src='"+setting.src + fileName + "' width='"+setting.imgW+"px' height='"+setting.imgH+"px'/></a><input type='hidden' name='PicPath[]' value='" + fileName + "'/></li>");
            parent.autoResize('iframe', 0);
        },
        onDialogClose: function (queueData) {
//            if (queueData.queueLength + imgCount > 5) {
//                alert("你上传的图片数量已经超过5张，不能再上传了!");
//                var i = 0;
//                for (var s in queueData.files) {
//                    i++;
//                    //选中多张上传，不超过5张部分可正常上传，超过5张部分，取消上传
//                    if (i + imgCount > 5) {
//                        $("#pro_picture").uploadify("cancel", s);
//                    }
//                }
//                return;
//            }
        }
    });
}
