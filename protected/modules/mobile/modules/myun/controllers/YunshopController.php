<?php

/**
 * 云官网
 */
class YunshopController extends MyunController
{
    /**
     * 线上店铺
     */
    public function actionShop()
    {
        $user = new MobileUserUC();
        $merchant_id = '';
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            $merchantC = new MerchantC();
            $re = json_decode($merchantC->getMerchantByEncrypt($encrypt_id));
            $merchant_id = $re->data->id;
        }

        //商城基本信息
        $shop_res = $user->getShop($merchant_id);
        $shop_result = json_decode($shop_res, true);
        if ($shop_result['status'] == ERROR_NONE) {
            if (isset($shop_result['data'])) {
                $shop = $shop_result['data'];
            }
        }

        if (empty($shop)) {
            Yii::app()->user->setFlash('error', '商铺建设中。。。');
        }

        //门店信息
        $store_res = $user->getStore($merchant_id);
        $store_result = json_decode($store_res, true);
        if ($store_result['status'] == ERROR_NONE) {
            if (isset($store_result['data'])) {
                $store = $store_result['data'];
                if (isset($_GET['store_id']) && $_GET['store_id']) {
                    $store_id = $_GET['store_id'];
                } else {
                    $store_id = $store_result['store_id'];
                }
            }
        }

        //预订信息
        $onlineShop = json_decode($user->getOnline($merchant_id), true);
        if ($onlineShop['status'] == ERROR_NONE) {
            $online = $onlineShop['data'];
        }
        //优惠券信息，修改 已失效的优惠券
        $user->changeMerchantCouponsStatus($merchant_id);
        $coupons_res = $user->getCoupons($merchant_id, $store_id);
        $coupons_result = json_decode($coupons_res, true);
        if ($coupons_result['status'] == ERROR_NONE) {
            if (isset($coupons_result['data'])) {
                $coupons = $coupons_result['data'];
            }
        }

        //储值活动信息
        $stored_res = $user->getStored($merchant_id);
        $stored_result = json_decode($stored_res, true);
        if ($stored_result['status'] == ERROR_NONE) {
            if (isset($stored_result['data'])) {
                $stored = $stored_result['data'];
            }
        }

        //商户相册信息
        $album_res = json_decode($user->getAlbumNum($merchant_id));
        if ($album_res->status == ERROR_NONE) {
            $album_num = $album_res->data;
        }

        $merchant = $user->getMerchantWithId($encrypt_id);

        $wxlocation = new WxLocation();
        $resultWxlocation = $wxlocation->getWxLocation($store[$store_id]['lat'], $store[$store_id]['lng'], $merchant);
        $result = json_decode($resultWxlocation, true);
        $location = $result['location'];
        $signPackage = $result['signPackage'];

        $this->render('shop', array(
            'shop' => $shop,
            'store' => $store,
            'coupons' => $coupons,
            'stored' => $stored,
            'store_id' => $store_id,
            'album_num' => $album_num,
            'online' => $online,
            'location' => $location,
            'signPackage' => $signPackage,
            'encrypt_id' => $encrypt_id,
        ));

    }

    //一级相册显示
    public function actionAlbum()
    {
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }
        $user = new UserUC();
        $merchant = $user->getMerchantWithId($encrypt_id);
        $merchant_id = $merchant->id;
        $albumC = new AlbumC();
        $result = json_decode($albumC->getAlbumList($merchant_id));
        if ($result->status == ERROR_NONE) {
            $album = $result->data;
            if (isset($_GET['album_id']) && !empty($_GET['album_id'])) {
                if ($_GET['album_id'] == 'all') {
                    $album_id = '';
                } else {
                    $album_id = $_GET['album_id'];
                }

            } else {
                $album_id = $result->data['0']->id;
            }
            $albumgroup_res = json_decode($albumC->getAlbumGroupList($album_id, $merchant_id));
            if ($albumgroup_res->status == ERROR_NONE) {
                $album_group = $albumgroup_res->data;
            }
        }

        $this->render('album', array(
            'album' => $album,
            'album_group' => $album_group,
            'encrypt_id' => $encrypt_id
        ));
    }

    //二级相册显示
    public function actionPhotoList()
    {
        if (isset($_GET['album_group_id']) && !empty($_GET['album_group_id'])) {
            if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
                $encrypt_id = $_GET['encrypt_id'];
            }
            $album_name = $_GET['album_name'];
            $albumC = new AlbumC();
            $merchant_id = Yii::app()->session['merchant_id'];
            $result = json_decode($albumC->getAlbumList($merchant_id));
            if ($result->status == ERROR_NONE) {
                $result_img = json_decode($albumC->getAlbumImgList($_GET['album_group_id']));
                if ($result_img->status == ERROR_NONE) {
                    $this->render('albumImgList', array(
                        'imglist' => $result_img->data,
                        'album' => $result->data,
                        'album_name' => $album_name,
                        'encrypt_id' => $encrypt_id
                    ));
                }
            }
        }
    }
}