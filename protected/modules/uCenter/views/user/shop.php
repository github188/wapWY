<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <script>
        alert('<?php echo Yii::app()->user->getFlash('error')?>')
        location.href = "<?php echo Yii::app()->createUrl('uCenter/user/memberCenter', array('encrypt_id' => $encrypt_id))?>";
    </script>
<?php } ?>


<?php if (!empty($shop)) { ?>
    <head>
        <title><?php echo empty($shop['name']) ? $shop['merchant_name'] : $shop['name'] ?></title>
    </head>

    <body class="shop">
    <header class="top_title">
        <div class="banner">
            <a href="<?php echo Yii::app()->createUrl('uCenter/user/Album', array('encrypt_id' => $encrypt_id)) ?>">
                <?php if (!empty($shop['image'])) { ?>
                    <img src="<?php echo IMG_GJ_LIST . $shop['image'] ?>">
                <?php } else { ?>
                    <img src="<?php echo USER_STATIC_IMAGES ?>user/banner.png">
                <?php } ?>
            </a>
            <span class="name"><?php echo empty($shop['name']) ? $shop['merchant_name'] : $shop['name'] ?></span>
            <span class="num"><?php echo $album_num ?></span>
            <div class="logo">
                <?php if (!empty($shop['image_logo'])) { ?>
                    <img src="<?php echo IMG_GJ_LIST . $shop['image_logo'] ?>">
                <?php } ?>
            </div>
            <div class="shadow"></div>
        </div>
    </header>
    <article>
        <section class="sh-con">
            <div class="item item notxt">
                <a class="intergral" href="#">
                    <span class="border">当前</span>

                    <select name="Store" onChange="changeStore()">
                        <?php foreach ($store as $key => $value) { ?>
                            <?php if ($key == $store_id) { ?>
                                <?php echo '<option value="' . $key . '" selected=selected>' . $value['name'] . '</option>' ?>
                            <?php } else { ?>
                                <?php echo '<option value="' . $key . '">' . $value['name'] . '</option>' ?>
                            <?php } ?>
                        <?php } ?>
                    </select>

                    <span class="jt" onChange="changeStore()"></span>
                </a>
            </div>
            <div class="item item_9">
                <?php if ('wechat' == Yii::app()->session['source']) { ?>
                    <a class="intergral" id="location">
	          			<span class="ico">
	          			</span>
                        <?php echo str_replace(",", "", $store[$store_id]['address']) ?>
                        <span class="jt">
	          			</span>
                    </a>
                <?php } else { ?>
                    <a class="intergral" id="location" onclick="tz()">
                  		<span class="ico">
                  		</span>
                        <?php echo str_replace(",", "", $store[$store_id]['address']) ?>
                        <span class="jt">
                  		</span>
                    </a>
                <?php } ?>
            </div>
            <div class="item item_10">
                <a class="intergral" href="tel:18758363316<?php //echo $store[$store_id]['telephone']?>">
                    <span class="ico"></span><?php echo $store[$store_id]['telephone'] ?><span class="jt"></span></a>
            </div>
            <?php if (count($store) > 1) { ?>
                <div class="more">
                    <a href="<?php echo Yii::app()->createUrl('uCenter/user/storeList', array('model' => $store, 'encrypt_id' => $encrypt_id)) ?>">更多门店</a>
                </div>
            <?php } ?>
        </section>

        <section class="item item_14">
            <a class="intergral"
               href="<?php echo Yii::app()->createUrl('uCenter/user/memberCenter', array('encrypt_id' => $encrypt_id)) ?>">
                <span class="ico"></span>会员中心<span class="jt"></span></a>
        </section>

        <?php if ($shop['if_book'] == 2) { ?>
            <section class="item item_8" style="<?php if (!empty($online)) {
                if ($online['if_book'] == MERCHANT_BOOK_OPEN) {
                    echo '';
                } else if ($online['if_book'] == MERCHANT_BOOK_CLOSE) {
                    echo 'display:none';
                }
            } ?>">
                <a class="intergral"
                   href="<?php echo Yii::app()->createUrl('uCenter/user/bookOperate', array('store_id' => $online['store_id'], 'encrypt_id' => $encrypt_id)) ?>">
                    <span class="ico"></span>预订<span class="jt"></span></a>
                <!--                        <a class="intergral" href="-->
                <?php //echo Yii::app()->createUrl('uCenter/user/list', array('title'=>'online','model'=>$online['store_id']))?><!--"><span class="ico"></span>预订<span class="jt"></span></a>-->
            </section>
        <?php } ?>

        <?php //if ($shop['if_check']) { ?>
        <!-- 		    <section class="item item_7"> -->
        <!-- 		          <a class="intergral" href="#"><span class="ico"></span>买单<span class="jt"></span></a> -->
        <!-- 		    </section> -->
        <?php //} ?>
        <div class="item item_2" style="margin-bottom:0">
            <lable class="intergral"><span class="ico"></span>储值</lable>
        </div>
        <section class="list">
            <?php $count_stored = 0; ?>
            <?php if (isset($stored) && !empty($stored)) { ?>
                <?php foreach ($stored as $key => $value) { ?>
                    <?php $count_stored++ ?>
                    <?php if ($count_stored <= 2) { ?>
                        <a class="cz-list"
                           href="<?php echo Yii::app()->createUrl('uCenter/stored/StoredView', array('id' => $key, 'encrypt_id' => $encrypt_id)) ?>">充<?php echo $value['stored_money'] ?>
                            得<?php echo $value['get_money'] ?>元</a>
                    <?php } else { ?>
                        <?php break; ?>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <lable class="cz-list" href="#">当前无储值活动</lable>
            <?php } ?>
            <div class="clear"></div>
            <?php if ($count_stored > 2) { ?>
                <div class="more"><a
                        href="<?php echo Yii::app()->createUrl('uCenter/user/list', array('title' => 'stored', 'model' => $stored, 'encrypt_id' => $encrypt_id)) ?>">更多储值活动</a>
                </div>
            <?php } ?>
        </section>

        <?php if ($shop['if_coupons'] == 2) { ?>
            <div class="item item_3" style="margin-bottom:0">
                <lable class="intergral"><span class="ico"></span>领取优惠券</lable>
            </div>
            <section class="list">

                <?php $count_coupons = 0; ?>
                <?php if (isset($coupons['coupons']) && !empty($coupons['coupons'])) { ?>
                    <?php foreach ($coupons['coupons'] as $key => $value) { ?>
                        <?php $count_coupons++; ?>
                        <?php if ($count_coupons <= 2) { ?>
                            <div class="hb-list">
                                <a href="<?php echo Yii::app()->createUrl('uCenter/coupon/newGetCouponOne', array('coupon_id' => $key, 'encrypt_id' => $encrypt_id)) ?>">
                                    <span class="img"><img src="<?php echo USER_STATIC_IMAGES ?>user/img01.png"></span>
					            <span class="name">
					            <?php echo $value['title']
                                // 					            if($value['type'] == COUPON_TYPE_CASH){//代金券
                                // 					            	if(isset($value['fixed_value']) && !empty($value['fixed_value'])){
                                // 					            	 	echo $value['fixed_value'].'元 ('.$GLOBALS['COUPON_TYPE'][$value['type']].')';
                                // 					             	}else {
                                // 										echo $value['userdefined_value'].'元 (随机'.$GLOBALS['COUPON_TYPE'][$value['type']].')';
                                // 									}
                                // 					             }elseif ($value['type'] == COUPON_TYPE_DISCOUNT){//折扣券
                                // 					            	echo ($value['discount']*10).'折 ('.$GLOBALS['COUPON_TYPE'][$value['type']].')';
                                // 					             }elseif ($value['type'] == COUPON_TYPE_EXCHANGE){//兑换券
                                // 					            	echo $value['exchange'].' ('.$GLOBALS['COUPON_TYPE'][$value['type']].')';
                                // 					             }
                                ?>
					            </span>
                                    <span class="receive">点击领取</span>
                                </a>
                            </div>
                        <?php } else { ?>
                            <?php break; ?>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <lable class="cz-list" href="#">当前无优惠券</lable>
                <?php } ?>
                <?php if ($count_coupons > 2) { ?>
                    <div class="more"><a
                            href="<?php echo Yii::app()->createUrl('uCenter/user/list', array('title' => 'coupons', 'model' => $coupons['coupons'], 'encrypt_id' => $encrypt_id)) ?>">更多优惠券</a>
                    </div>
                <?php } ?>
            </section>
        <?php } ?>


        <section class="introduce">
            <div class="title">商家介绍</div>
            <div class="con" style="line-height: 20px;"><?php echo $shop['introduction'] ?></div>
        </section>
        <div class="remarkLogo">
            <img src="<?php echo USER_STATIC_IMAGES ?>logo1.png">
            <p>玩券提供技术支持</p>
        </div>
        <br>
    </article>

    </body>
<?php } ?>

<script type="text/javascript">
    function changeStore() {
        var store_id = $('select[name = Store]').val();
        var source = '<?php echo Yii::app()->session['source']?>';
        window.location.href = "<?php echo Yii::app()->createUrl('uCenter/user/shop')?>" + "?store_id=" + store_id + "&encrypt_id=" + <?php echo $encrypt_id; ?> +"&source=" + source;
    }
</script>

<script>
    function tz() {
        window.location.href = 'http://api.map.baidu.com/marker?location=<?php echo $store[$store_id]['lat'];?>,<?php echo $store[$store_id]['lng'];?>&title=<?php echo $store[$store_id]['address']?>&content=<?php echo $store[$store_id]['name']?>&output=html';
    }
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            "openLocation",
        ]
    });
    wx.ready(function () {
        document.getElementById("location").onclick = function () {
            wx.openLocation({
                latitude: <?php echo (float)$location["lat"];?>,// 纬度，浮点数，范围为90 ~ -90
                longitude: <?php echo (float)$location["lng"];?>,// 经度，浮点数，范围为180 ~ -180。
                name: '<?php echo $store[$store_id]['name']?>', // 位置名
                address: '<?php echo $store[$store_id]['address']?>', // 地址详情说明
                scale: 20, // 地图缩放级别,整形值,范围从1~28。默认为最大
                infoUrl: 'http://www.baidu.com' // 在查看位置界面底部显示的超链接,可点击跳转
            });
        };
    });
</script>
