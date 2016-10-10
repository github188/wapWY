<head>
    <title>Title</title>
</head>
<body>


<script>
    //添加卡券
    $(function () {
        //微信js配置
        wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: '<?php echo $signPackage["timestamp"];?>',
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
                "checkJsApi",
                "onMenuShareTimeline",
                "onMenuShareAppMessage",
                "onMenuShareQQ",
                "addCard"
            ]
        });

        wx.ready(function () {

            var flag = true;
            //添加卡券
            if (flag) {
                wx.addCard({
                    cardList: [
                        {
                            cardId: '<?php echo $card_id?>',
                            cardExt: '<?php echo $cardExt?>',
                        }
                    ],
                    success: function (res) {
                        flag = false;
                    }
                });
            }
        });
    });
</script>
</body>