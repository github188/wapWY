<?php

/**
 * 商城优惠活动页面
 */
class ActivityController extends DMallController
{

    /**
     * 首单立减页
     */
    public function actionFirstSingle()
    {
        $encrypt_id = '';
        $name = '';
        $start_time = '';
        $end_time = '';
        $jump_url = '';

        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }
        $activity = new DMallActivity();
        $result = json_decode($activity->getFirstSingle($encrypt_id), true);
        if ($result['status'] == ERROR_NONE) {
            $img = $result['data']['img'];
            $name = $result['data']['name'];
            $start_time = $result['data']['start_time'];
            $end_time = $result['data']['end_time'];
            $jump_url = Yii::app()->createUrl('Dmall/commodity/index', array('encrypt_id' => $encrypt_id));
        } else {
            echo "<script>alert('" . $result['errMsg'] . "')</script>";
            Yii::app()->end();
        }
        $this->render('firstsingle', array(
            'name' => $name,
            'start_time' => strtotime($start_time),
            'end_time' => strtotime($end_time),
            'jump_url' => $jump_url,
            'img' => $img
        ));
    }

    /**
     * 福利列表页
     */
    public function actionWelfareList()
    {
        $activity_arr = array();
        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            //获取微信用户openid
            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $code = $_GET['code'];
                $wechatUser = new WechatUser();
                $re = json_decode($wechatUser->getUserOpenId($code, $encrypt_id, ''));
                if (!isset(Yii::app()->session[$encrypt_id . 'open_id']) || empty(Yii::app()->session[$encrypt_id . 'open_id'])) {
                    Yii::app()->session[$encrypt_id . 'open_id'] = $re->openid;
                }
            }
        }
        
        if (isset(Yii::app() -> session[$encrypt_id.'user_id']) && !empty(Yii::app() -> session[$encrypt_id.'user_id'])){
            //获取已参加活动id
            $userUC = new UserUC();
            $result = json_decode($userUC->checkUserIFZfl('', $encrypt_id,'',Yii::app()->session[$encrypt_id . 'user_id']));
            if ($result->status == ERROR_NONE) {
                $activity_arr = $result->data;
            }
        }elseif (isset(Yii::app()->session[$encrypt_id . 'open_id']) && !empty(Yii::app()->session[$encrypt_id . 'open_id'])) {
            //获取已参加活动id
            $userUC = new UserUC();
            $result = json_decode($userUC->checkUserIFZfl('', $encrypt_id, Yii::app()->session[$encrypt_id . 'open_id'],''));
            if ($result->status == ERROR_NONE) {
                $activity_arr = $result->data;
            }
        }

        $activity = new DMallActivity();
        $result = json_decode($activity->getWelfareList($encrypt_id), true);

        if ($result['status'] == ERROR_NONE) {
            $list = $result['data'];
            $coupons = new CouponsUC();
            foreach ($list as $k => $v) {
                $coupons_id = $list[$k]['coupons_id'];
                $coupons_res = json_decode($coupons->getCouponsById($coupons_id));
                if ($coupons_res->status == ERROR_NONE) {
                    $list[$k]['coupons_type'] = $coupons_res->data->type;
                } else {
                    $list[$k]['coupons_type'] = '';
                }
            }
            $jump_url = Yii::app()->createUrl('Dmall/commodity/index', array('encrypt_id' => $encrypt_id));
        } else {
            echo "<script>alert('" . $result['errMsg'] . "')</script>";
            Yii::app()->end();
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

        $this->render('welfarelist', array(
            'list' => $list,
            'encrypt_id' => $encrypt_id,
            'activity_arr' => $activity_arr,
            'signPackage' => $signPackage,
            'onlineshop' => $onlineshop
        ));
    }

    /**
     * 福利详情页
     */
    public function actionWelfareDetail()
    {

        if (isset($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
            //获取微信用户openid
            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $code = $_GET['code'];
                $wechatUser = new WechatUser();
                $re = json_decode($wechatUser->getUserOpenId($code, $encrypt_id, ''));
                $openid = $re->openid;
                $userUC = new UserUC();
                $res = json_decode($userUC->checkIsFollowWechat($openid, '', $encrypt_id));
                if ($res->status == ERROR_NONE) {
                    $if_follow = $res->data;
                }
            }
            $qrcode = '';
            $merchantC = new MerchantC();
            $result = json_decode($merchantC->getMerchantByEncrypt($encrypt_id));
            if ($result->status == ERROR_NONE) {
                $qrcode = $result->data->wechat_qrcode;
            }
        }
        $this->render('welfaredetail', array(
            'encrypt_id' => $encrypt_id,
            'qrcode' => $qrcode,
            'if_follow' => $if_follow
        ));
    }

    //活动详情
    public function actionActivityDetail()
    {
        $this->render('activityDetail');
    }


}
	