<head>
    <title>会员中心</title>
</head>

<body class="center">
<article id="card">
    <div class="list_item">
        <section class="info">
            <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/personalInformation', array('encrypt_id' => $encrypt_id)) ?>">
                <div class="img">
                    <?php if (!empty($data['avatar'])) { ?>
                        <img src="<?php echo $data['avatar'] ?>">
                    <?php } else { ?>
                        <img src="<?php echo USER_STATIC_IMAGES ?>user/face.png">
                    <?php } ?>
                </div>
                <div class="text">
                    <span class="fw"><?php echo $data['nickname'] ?></span>
                    <span><?php echo $data['grade_name'] ?>
                        积分:<?php echo isset($data['points']) ? $data['points'] : '0' ?></span>
                </div>
                <span class="jt"></span>
            </a>
        </section>
        <section class="menu">
            <ul>
                <li>
                    <a href="<?php echo Yii::app()->createUrl('mobile/stored/stored/stored', array('encrypt_id' => $encrypt_id)) ?>">
                        <span class="icon1"></span><?php echo number_format($data['money'], 2); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/Coupons', array('encrypt_id' => $encrypt_id, 'coupons_status' => COUPONS_USE_STATUS_UNUSE, 'coupons_type' => COUPON_TYPE_CASH)) ?>">
                        <span class="icon2"></span><?php echo $data['coupons_num'] ?>张
                    </a>
                </li>
                <li>
                    <a href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/memberShipCard', array('encrypt_id' => $encrypt_id)) ?>">
                        <span class="icon3"></span>会员卡
                    </a>
                </li>
            </ul>
        </section>
        <div class="dline"></div>
        <?php if ($merchant_id != TIANSHI_SHOP_API) { ?>
            <section class="item item_2">
                <a class="intergral"
                   href="<?php echo Yii::app()->createUrl('mobile/uCenter/user/orderList', array('stored_confirm_status' => ORDER_PAY_WAITFORCONFIRM, 'encrypt_id' => $encrypt_id)) ?>">
                    <span class="ico"></span>消费订单<span class="jt"></span>
                    <span class="r_bg" style="display: none">1</span>
                </a>
            </section>

            <section class="item item_3">
                <a class="intergral"
                   href="<?php echo Yii::app()->createUrl('mobile/book/book/bookList', array('encrypt_id' => $encrypt_id,)) ?>">
                    <span class="ico"></span>我的预订<span class="jt"></span>
                </a>
            </section>
            <section class="item item_4">
                <a class="intergral"
                   href="<?php echo Yii::app()->createUrl('mobile/myun/yunshop/shop', array('encrypt_id' => $encrypt_id)) ?>">
                    <span class="ico"></span>商家首页<span class="jt"></span>
                </a>
            </section>
            <div class="dline"></div>
        <?php } ?>

        <?php if ($merchant_id == TIANSHI_SHOP_API) { ?>
            <!--<section class="item item_5">
                <a class="intergral"
                   href="<?php /*echo Yii::app()->createUrl('Dmall/Order/OrderList', array('encrypt_id' => $encrypt_id)) */?>">
                    <span class="ico"></span>商城订单<span class="jt"></span>
                </a>
            </section>-->
        <?php } else { ?>
            <section class="item item_5">
                <a class="intergral"
                   href="<?php echo Yii::app()->createUrl('mall/Order/OrderList', array('encrypt_id' => $encrypt_id)) ?>">
                    <span class="ico"></span>商城订单<span class="jt"></span>
                </a>
            </section>
        <?php } ?>
        <?php if ($merchant_id == TIANSHI_SHOP_API) { ?>
            <!--<section class="item item_14">
                <a class="intergral"
                   href="<?php /*echo Yii::app()->createUrl('Dmall/Commodity/Index', array('encrypt_id' => $encrypt_id)); */?>">
                    <span class="ico"></span>进入商城<span class="jt"></span>
                </a>
            </section>-->
        <?php } else { ?>
            <section class="item item_14">
                <a class="intergral"
                   href="<?php echo Yii::app()->createUrl('mall/Commodity/Index', array('encrypt_id' => $encrypt_id)); ?>">
                    <span class="ico"></span>进入商城<span class="jt"></span>
                </a>
            </section>
        <?php } ?>
    </div>
</article>
</body>


<script>
    $(function () {
        var uploadify_onSelectError = function (file, errorCode, errorMsg) {
            var msgText = "上传失败\n";
            switch (errorCode) {
                case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                    msgText += "文件大小超过限制( " + this.settings.fileSizeLimit + " )";
                    break;
                default:
                    msgText += "错误代码：" + errorCode + "\n" + errorMsg;
            }
            alert(msgText);
        };

        $('#upload').uploadify({
            uploader: '<?php echo UPLOAD_TO_PATH?>',// 服务器处理地址
            swf: '<?php echo GJ_STATIC_JS?>' + 'uploadify/uploadify.swf',
            buttonText: "选择图片",//按钮文字
            height: 50,  //按钮高度
            width: 50, //按钮宽度
            fileTypeExts: "<?php echo UPLOAD_IMG_TYPE;?>",//允许的文件类型
            fileTypeDesc: "请选择图片文件", //文件说明
            formData: {'folder': '<?php echo(IMG_GJ_FOLDER)?>'}, //提交给服务器端的参数
            fileSizeLimit: '2MB',
            overrideEvents: ['onDialogClose', 'onUploadSuccess', 'onUploadError', 'onSelectError'],
            onSelectError: uploadify_onSelectError,
            onUploadSuccess: function (file, data, response) {//一个文件上传成功后的响应事件处理
                eval("var jsondata = " + data + ";");
                var key = jsondata['key'];
                var fileName = jsondata['fileName'];
                var id = <?php echo $data['id']?>;

                $.ajax({
                    url: '<?php echo(Yii::app()->createUrl('mobile/uCenter/User/setAvatar'));?>',
                    data: {id: id, img: fileName, encrypt_id: '<?php echo $encrypt_id ?>'},
                    dataType: "json",
                    type: 'get',
                    success: function (data) {
                        if (data.status == <?php echo ERROR_NONE?>) {
                            $("#upload-button").css('background-image', 'url(<?php echo IMG_GJ_LIST?>' + fileName + ')');
                        } else {
                            alert(data.errMsg);
                        }
                    }
                });
            }
        });
        <?php if (!empty($data['avatar'])) { ?>
        $("#upload-button").css('background-image', 'url(<?php echo IMG_GJ_LIST . $data['avatar']?>)');
        <?php } ?>
    });
</script>