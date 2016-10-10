<head>
    <title>扫描二维码</title>
</head>
<body>
    <div class="QRCode">
        <div class="weui_cells_title">
            <h3>欢迎关注<?php if (Yii::app()->session['source'] == 'wechat') {
                    echo $merchant->wechat_name . '公众号';
                } elseif (Yii::app()->session['source'] == 'alipay') {
                    echo $merchant->fuwu_name . '服务窗';
                } else {
                    if (!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) {
                        echo $merchant->wechat_name . '公众号';
                    } elseif (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode)) {
                        echo $merchant->fuwu_name . '服务窗';
                    }
                } ?></h3>
            <p>
                <?php if (Yii::app()->session['source'] == 'wechat') {
                    echo '微信搜索“' . $merchant->wechat_name . '”';
                } elseif (Yii::app()->session['source'] == 'alipay') {
                    echo '支付宝搜索“' . $merchant->fuwu_name . '”';
                } else {
                    if (!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) {
                        echo '微信搜索“' . $merchant->wechat_name . '”';
                    } elseif (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode)) {
                        echo '支付宝搜索“' . $merchant->fuwu_name . '”';
                    }
                }?>关注获取最新优惠</p>
        </div>
        <div class="weui_cell_bd">
            <div class="weui_media_hd">
                <img class="weui_media_appmsg_thumb"
                     src="<?php if (Yii::app()->session['source'] == 'wechat') {
                         echo IMG_GJ_LIST . $merchant->wechat_qrcode;
                     } elseif (Yii::app()->session['source'] == 'alipay') {
                         echo IMG_GJ_LIST . $merchant->alipay_qrcode;
                     } else {
                         if (!empty($merchant->wechat_name) && !empty($merchant->wechat_qrcode)) {
                             echo IMG_GJ_LIST . $merchant->wechat_qrcode;
                         } elseif (!empty($merchant->fuwu_name) && !empty($merchant->alipay_qrcode)) {
                             echo IMG_GJ_LIST . $merchant->alipay_qrcode;
                         }
                     } ?>" alt="">
            </div>
            <div class="weui_btn_area">
                <a href="javascript:;" class="weui_btn weui_btn_primary">长按二维码识别关注</a>
            </div>
        </div>
    </div>
</body>
</html>
