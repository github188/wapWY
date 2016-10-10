<?php

class CommodityController extends MallController
{

    /*获取openid*/
    private function getOpenid($encrypt_id, $source)
    {
        //获取商户信息
        $mer = new MerchantC();
        $merchant = json_decode($mer->getMerchantByEncrypt($encrypt_id));

        //来源是微信
        if ($source == 'wechat') {
            $code = $_GET['code'];
            $wechat_appid = $merchant->data->wechat_appid;
            $wechat_appsecret = $merchant->data->wechat_appsecret;
            $merchant_id = $merchant->data->id;
            $wechat = new WechatC();
            //获取access_token以及用户openid
            $get_access_res = $wechat->getUserAccessCode($code, $wechat_appid, $wechat_appsecret, $merchant_id);

            if (isset($get_access_res['openid'])) {
                $open_id = $get_access_res['openid'];
                $access_token = $get_access_res['access_token'];
                Yii::app()->session['wechat_open_id'] = $open_id;
            }

        } elseif ($source == 'alipay_wallet') {//来源是支付宝
            $auth_code = $_GET['auth_code'];
            $appid = $merchant->appid;
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
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            if (isset($_GET['source']) && !empty($_GET['source'])) {
                $source = $_GET['source'];
                $this->getOpenid($encrypt_id, $source);
                Yii::app()->session['source'] = $_GET['source'];
            }
        }

        $mall = new MallUC();
        $ret = json_decode($mall->getGroup('', $encrypt_id), true);
        if ($ret['status'] == ERROR_NONE) {
            $groups = $ret['data'];
        }

        $result = json_decode($mall->getMallIndex('', $encrypt_id), true);
        if ($result['status'] == ERROR_NONE) {
            $banner_img = json_decode($result['data']['banner']);
            $group = $result['data']['product_info'];
            //Yii::app()->session['merchant_id'] = $result['data']['merchant_id'];

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

        $this->render('index', array(
            'banner_img' => $banner_img,
            'group' => $group,
            'groups' => $groups,
            'signPackage' => $signPackage,
            'onlineshop' => $onlineshop,
            'encrypt_id' => $encrypt_id
        ));
    }


    /**
     * 商品展示列表
     */
    public function actionCommodityList()
    {
        $data = array();
        $model = array();
        //默认销量降序
        $type = 'down';
        $placeholder = null;
        $shopgorup = null;
        $typename = 'limit_num';
        $title = null;
        $groupid = null;
        $encrypt_id = $_GET['encrypt_id'];
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchant_id = $res->data->id;
        $mallC = new MallUC();

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
            'signPackage' => $signPackage,
            'onlineshop' => $onlineshop,
            'encrypt_id' => $encrypt_id
        ));

    }

    /**
     * 详细商品信息
     */
    public function actionCommodityDetails()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $data = array();
            $datasku = array();
            $id = $_GET['id'];
            $mallC = new MallUC();
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
            }

            //微信分享
            $wechatJS = new WechatJS();
            $resultWxlocation = $wechatJS->Wxshare('', $_GET['encrypt_id']);
            $result = json_decode($resultWxlocation, true);
            $signPackage = $result;
            //获取商户云官网的logo获取在线商铺信息
            $userC = new UserUC();
            $result = json_decode($userC->getOnlineshop('', $_GET['encrypt_id']));
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
                'signPackage' => $signPackage,
                'onlineshop' => $onlineshop,
                'encrypt_id' => $_GET['encrypt_id']
            ));
        }

    }

    /*
     * 商品详细情况
     * */
    public function actionProductDetail()
    {
        if (isset($_GET['content']) && !empty($_GET['content'])) {
            $this->render('productDetail', array(
                'content' => $_GET['content']
            ));
        }
    }

    /*
     * 获取商品信息
     * 
     * */
    public function actionGetProductStandard()
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $mallC = new MallUC();
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
            $mallC = new MallUC();
            $product_id = $_POST['id'];
            $name = $_POST['name'];
            $result = $mallC->getProductSkuPrice($product_id, $name);
            echo $result;
        }
    }
}

?>