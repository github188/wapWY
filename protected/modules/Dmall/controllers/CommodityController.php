<?php

class CommodityController extends DMallController
{

    //首页二级目录内容显示
    public function actionShowContent()
    {
        $this->render('content', array(
            'content' => $_GET['content']
        ));
    }

    /** 免登操作-公众号
     * @param $encrypt_id
     */
    public function checkWechatOpenId($encrypt_id)
    {
        $user = new UserUC();
        $wechat_open_id = Yii::app()->session[$encrypt_id . 'open_id'];

        $merchant = $user->getMerchantWithId($encrypt_id);
        $merchant_id = $merchant->id;

        $res = $user->checkWechatOpenId($merchant_id, $wechat_open_id);
        $result = json_decode($res, true);

        if ($result['status'] == ERROR_NONE) {
            if (isset($result['data'])) {
                Yii::app()->session[$encrypt_id . 'user_id'] = $result['data'];
            }
        } else {

        }
    }


    /** 获取微信openid
     * @param $code
     * @param $wechat_appid
     * @param $wechat_appsecret
     * @param $encrypt_id
     */
    public function getWechatOpenId($code, $wechat_appid, $wechat_appsecret, $encrypt_id)
    {
        $wechat = new WechatC();
        //获取access_token以及用户openid
        $get_access_res = $wechat->getUserAccessCode($code, $wechat_appid, $wechat_appsecret);
        if (isset($get_access_res['openid'])) {
            $open_id = $get_access_res['openid'];
            Yii::app()->session[$encrypt_id . 'open_id'] = $open_id;
        }
    }


    /**
     * 获取 服务窗 OpenId
     */
    public function getAliOpenId($appid)
    {
        //获取链接中的参数
        if (!empty($_GET['auth_code'])) {                   //获取auth_code
            $auth_code = $_GET['auth_code'];
        }

        //获取并保存OpenId
        $api = new AliApi('AliApi');
        $response = $api->getOpenId($appid, $auth_code);
        if ($response != null) {
            if (isset($response->alipay_system_oauth_token_response)) {
                $open_id = $response->alipay_system_oauth_token_response->alipay_user_id;
                Yii::app()->session['ali_open_id'] = $open_id;
                $access_token = $response->alipay_system_oauth_token_response->access_token;
                Yii::app()->session['access_token'] = $access_token;
            }
        }
    }

    /**
     * 商品展示首页
     */
    public function actionIndex()
    {
        $banner_img = array();//轮播图数组
        $group = array();//分组商品
        $groups = array();
        $encrypt_id = '';
        $wechat_appid = '';
        $wechat_appsecret = '';

        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }
        $user = new UserUC();
        //获取商户信息
        $merchant_result = json_decode($user->getMerchant($encrypt_id));
        if ($merchant_result->status == ERROR_NONE) {
            $wechat_appid = $merchant_result->data->wechat_subscription_appid;
            $wechat_appsecret = $merchant_result->data->wechat_subscription_appsecret;
            $merchant_id = $merchant_result->data->id;
        }

        //微信参数
        if (empty(Yii::app()->session[$encrypt_id . 'open_id'])) {
            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $code = $_GET['code'];
                //获取并保存OpenId
                $this->getWechatOpenId($code, $wechat_appid, $wechat_appsecret, $encrypt_id);
            } else {
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $wechat_appid . '&redirect_uri=' . urlencode(COMMODITY_DOMAIN_DQH . '/index?encrypt_id=' . $encrypt_id . '&source=wechat') . '&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect';
                $this->redirect($url);
            }
        }

        //判断是否登陆过免登操作
        $this->checkWechatOpenId($encrypt_id);

        $mall = new DMallUC();
        $ret = json_decode($mall->getGroup($merchant_id, $encrypt_id), true);
        if ($ret['status'] == ERROR_NONE) {
            $groups = $ret['data'];
        }
        $result = json_decode($mall->getMallIndex($merchant_id, $encrypt_id), true);
        if ($result['status'] == ERROR_NONE) {
            $banner_img = json_decode($result['data']['banner']);
            $group = $result['data']['product_info'];
            $merchant_id = $result['data']['merchant_id'];
            Yii::app()->session['source'] = $_GET['source'];
        }

        //微信分享
        $wechatJS = new WechatJS();
        $resultWxlocation = $wechatJS->Wxshare('', $encrypt_id);
        $result = json_decode($resultWxlocation, true);
        $signPackage = $result;
        //获取商户云官网的logo获取在线商铺信息
        $userC = new UserUC();
        $result = json_decode($userC->getOnlineshop($merchant_id));
        if ($result->status == ERROR_NONE) {
            $onlineshop = $result->data;
        }
        $this->render('index', array(
            'banner_img' => $banner_img,
            'group' => $group,
            'groups' => $groups,
            'encrypt_id' => $encrypt_id,
            'signPackage' => $signPackage,
            'onlineshop' => $onlineshop
        ));
    }


    /**
     * 商品展示列表
     */
    public function actionCommodityList()
    {
        if (isset($_GET['encrypt_id']) || isset($_POST['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        $merchantC = new MerchantC();
        $result = json_decode($merchantC->getMerchantByEncrypt($encrypt_id));
        if ($result->status == ERROR_NONE) {
            $merchant_id = $result->data->id;
        }

        $data = array();
        $model = array();
        //默认销量降序
        $type = 'down';
        $placeholder = null;
        $shopgorup = null;
        $typename = 'limit_num';
        $title = null;
        $groupid = null;
        $mallC = new DMallUC();

        if (isset($_GET['placeholder']) && !empty($_GET['placeholder'])) {
            //搜索框内容
            $placeholder = $_GET['placeholder'];
        }
        if (isset($_GET['typename']) && !empty($_GET['typename']) && isset($_GET['type']) && !empty($_GET['type'])) {
            //排序名字和方式
            $typename = $_GET['typename'];
            $type = $_GET['type'];
            if ($type == 'down')
                $type = 'up';
            else if ($type == 'up')
                $type = 'down';
        }
        //获取分组信息
        $shop = new ShopC();
        $result_group = json_decode($shop->shopGroupList($merchant_id, ''));
        if ($result_group->status == ERROR_NONE) {
            $group = $result_group->data;
        }


        if (isset($_GET['shopgorup']) && !empty($_GET['shopgorup'])) {
            $shopgorup = $_GET['shopgorup'];
        }

        if (isset($_GET['title']) && !empty($_GET['title'])) {
            $title = $_GET['title'];
        }

        if (isset($_GET['groupid']) && !empty($_GET['groupid'])) {
            $groupid = $_GET['groupid'];
        }

        $result = json_decode($mallC->queryCommodityList($merchant_id, $placeholder, $typename, $type, $shopgorup, $groupid), true);
        if ($result['status'] == ERROR_NONE) {
            $data = $result['data'];
        }


        //微信分享
        $wechatJS = new WechatJS();
        $resultWxlocation = $wechatJS->Wxshare('', $encrypt_id);
        $result = json_decode($resultWxlocation, true);
        $signPackage = $result;
        //获取商户云官网的logo获取在线商铺信息
        $userC = new UserUC();
        $result = json_decode($userC->getOnlineshop('', $encrypt_id));
        if ($result->status == ERROR_NONE) {
            $onlineshop = $result->data;
        }

        $this->render('commodityList', array(
            'data' => $data,
            'typename' => $typename,
            'type' => $type,
            'placeholder' => $placeholder,
            'shopgroup' => $shopgorup,
            'title' => $title,
            'group' => $group,
            'groupid' => $groupid,
            'encrypt_id' => $encrypt_id,
            'signPackage' => $signPackage,
            'onlineshop' => $onlineshop
        ));

    }

    /**
     * 详细商品信息
     */
    public function actionCommodityDetails()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            if (isset($_GET['encrypt_id']) || isset($_POST['encrypt_id'])) {
                $encrypt_id = $_GET['encrypt_id'];
                $openid = Yii::app()->session[$encrypt_id . 'open_id'];
            }

            $qrcode = '';
            $merchantC = new MerchantC();
            $result = json_decode($merchantC->getMerchantByEncrypt($encrypt_id));
            if ($result->status == ERROR_NONE) {
                $qrcode = $result->data->wechat_qrcode;
            }

            //判断是否关注公众号
            $userUC = new UserUC();
            $res = json_decode($userUC->checkIsFollowWechat($openid, '', $encrypt_id));
            $if_follow = '';
            if ($res->status == ERROR_NONE) {
                $if_follow = $res->data;
            }

            if (empty($openid)) {
                $if_follow = 5;
            }

            $data = array();
            $datasku = array();
            $id = $_GET['id'];
            $mallC = new DMallUC();
            $result = json_decode($mallC->queryCommodityDetails($id), true);
            $standard = array();
            if ($result['status'] == ERROR_NONE) {
                $standard = explode(';', $result['data']['standard']);
                $data = $result['data'];
                $datasku = $result['datasku'];
                $minprice = $result['minprice'];
                $maxprice = $result['maxprice'];
                $minorgprice = $result['minorgprice'];
                $maxorgprice = $result['maxorgprice'];
                $notice = $result['notice'];
            }
            //微信分享
            $wechatJS = new WechatJS();
            $resultWxlocation = $wechatJS->Wxshare('', $encrypt_id);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;
            //获取商户云官网的logo获取在线商铺信息
            $userC = new UserUC();
            $result = json_decode($userC->getOnlineshop('', $encrypt_id));
            if ($result->status == ERROR_NONE) {
                $onlineshop = $result->data;
            }
            $this->render('commodityDetails', array(
                'data' => $data,
                'datasku' => $datasku,
                'minprice' => $minprice,
                'maxprice' => $maxprice,
                'minorgprice' => $minorgprice,
                'maxorgprice' => $maxorgprice,
                'standard' => $standard,
                'notice' => $notice,
                'if_follow' => $if_follow,
                'qrcode' => $qrcode,
                'encrypt_id' => $encrypt_id,
                'signPackage' => $signPackage,
                'onlineshop' => $onlineshop

            ));
        }

    }

    /*
     * ajax获取商品详情
     * */
    public function actionProductDetail()
    {
        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            $product_id = $_POST['product_id'];
            $dmallUC = new DMallUC();
            echo $dmallUC->getDproductDetailedIntroduction($product_id);
        }
    }

    /*
     * 获取商品信息
     * 
     * */
    public function actionGetProductStandard()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $mallC = new DMallUC();
            $result = $mallC->getProductStandard($_POST['id']);
            echo $result;
        }
    }

    /**
     * 获取商品sku的价格
     */
    public function actionGetproductSkuPrice()
    {
        if (isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['name']) && !empty($_POST['name'])) {
            $mallC = new DMallUC();
            $product_id = $_POST['id'];
            $name = $_POST['name'];
            $result = $mallC->getProductSkuPrice($product_id, $name);
            echo $result;
        }
    }
}

?>