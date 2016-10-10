<?php

/**
 * 购物车
 * */
class CartController extends DMallController
{

    /**
     * 验证是否登陆
     */
    public function isLogin()
    {
        $user = new UserUC();
        $merchant_id = Yii::app()->session['merchant_id'];
        $user_id = Yii::app()->session['user_id'];
        $result = $user->checkLogin($merchant_id, $user_id);
        return $result;
    }

    //加入购物车
    public function actionAddCart()
    {
        if (isset($_POST['sku_name']) && !empty($_POST['sku_name'])) {

            $user_id = Yii::app()->session['user_id'];
            if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
                $product_id = $_POST['product_id'];
            } else {
                $product_id = '';
            }

            if (isset($_POST['num']) && !empty($_POST['num'])) {
                $num = $_POST['num'];
            } else {
                $num = '';
            }

            if (isset($_POST['sku_name']) && !empty($_POST['sku_name'])) {
                $sku_name = $_POST['sku_name'];
            } else {
                $sku_name = '';
            }

            $mall = new DMallUC();
            $result = $mall->addCart($product_id, $sku_name, $num, $user_id);
            echo $result;
        }
    }


    //购物车列表
    public function actionCartList()
    {
        //验证是否登录
        if (!$this->isLogin()) {
            $this->redirect(Yii::app()->createUrl('uCenter/user/login', array('goUrl' => Yii::app()->createUrl('Dmall/Cart/CartList'))));
            return;
        }

        $user_id = Yii::app()->session['user_id'];
        $mall = new DMallUC();
        $result = json_decode($mall->getUserCart($user_id));
        if ($result->status == ERROR_NONE) {
            $this->render('cartList', array(
                'cart' => $result->data->list
            ));
        }
    }

    //删除购物车
    public function actionDelCart()
    {
        if (isset($_POST['cart_id']) && !empty($_POST['cart_id'])) {
            $cart_id = $_POST['cart_id'];
            $mall = new DMallUC();
            $result = json_decode($mall->delCart($cart_id));
            if ($result->status == ERROR_NONE) {
                $url = Yii::app()->createUrl('mall/Cart/CartList');
                echo "<script>alert('删除成功');window.location.href='$url'</script>";
            } else {
                $url = Yii::app()->createUrl('mall/Cart/CartList');
                echo "<script>alert('" . $result->errMsg . "');window.location.href='$url'</script>";
            }
        }
    }

    /**
     * 删除购物车某一件商品
     */
    public function actionDelCartGoods()
    {
        if (isset($_POST['cart_id']) && !empty($_POST['cart_id'])) {
            $cart_id = $_POST['cart_id'];
            $mall = new DMallUC();
            $result = json_decode($mall->delCartGoods($cart_id));
            if ($result->status == ERROR_NONE) {
                echo json_encode('success');
            } else {
                echo json_encode($result->errMsg);
            }
        } else {
            echo json_encode('error');
        }

    }

    /**
     * 商品结算
     */
    public function actionCartPay()
    {

        if ((isset($_POST['skuid']) && !empty($_POST['skuid'])) || (isset($_GET['skuid']) && !empty($_GET['skuid']))) {
            $encrypt_id = $_GET['encrypt_id'];

            if (isset(Yii::app()->session[$encrypt_id . 'open_id']) && !empty(Yii::app()->session[$encrypt_id . 'open_id'])) {
                //如果session存在wechat_open_id(微信用户openid)则判断是否可以免登，
                //免登规则，用户必须是该商户的用户且手机号注册成为会员
                $userUC = new UserUC();
                $result = json_decode($userUC->checkUserIfLogin(Yii::app()->session[$encrypt_id . 'open_id'], '', $encrypt_id, ''));
                if ($result->status == ERROR_NONE) {
                    Yii::app()->session[$encrypt_id . 'user_id'] = $result->user_id;
                }
            }

            //验证是否登录
            if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
                $this->redirect(Yii::app()->createUrl('mobile/auth/Register', array(
                    'goUrl' => Yii::app()->createUrl('Dmall/Cart/CartPay', array(
                        'skuid' => $_GET['skuid'],
                        'num' => $_GET['num'],
                        'encrypt_id' => $encrypt_id
                    )))));
            }


            $num = empty($_POST['num']) ? $_GET['num'] : $_POST['num'];
            $skuid = empty($_POST['skuid']) ? $_GET['skuid'] : $_POST['skuid'];
            $user_id = Yii::app()->session[$encrypt_id.'user_id'];

            if (isset($_POST['is_cart']) && !empty($_POST['is_cart'])) {
                $is_cart = $_POST['is_cart'];
            } else {
                $is_cart = IS_CART_NO;
            }

            $mall = new DMallUC();
            //获取sku信息 / 计算总价和运费
            $skuinfo = json_decode($mall->getProductSkuInfo($skuid, $encrypt_id));
            $total = 0;
            if ($skuinfo->status == ERROR_NONE) {
                $sku = $skuinfo->data;
                $arr_sku = $skuinfo->arr_sku;
                foreach ($sku as $k => $v) {
                    $total += $v->price * $num[$k];
                }
            } else {
                $url = Yii::app()->createUrl('Dmall/Commodity/index', array('encrypt_id' => $encrypt_id));
                echo "<script>alert('" . $skuinfo->errMsg . "');window.location.href='$url'</script>";
                Yii::app()->end();
            }

            //赠送首单立减优惠券
            $activity = new DMallActivity();
            $ret = $activity->checkFirstActivity($user_id);
            $result = json_decode($ret, true);
            $coupons_id = ''; //首单立减活动的优惠券id
            if (isset($result['user_coupons_id'])) {
                $coupons_id = $result['user_coupons_id'];
            }

            //获取优惠券信息
            $optimal = array('id' => '', 'title' => '', 'value' => '', 'pay' => $total);
            $activity_coupons = array();
            $discount = 0;
            $coupons_arr = array();
            $coupons = new CouponsUC();
            $ret = $coupons->getUserCouponList($user_id);
            $result = json_decode($ret, true);
            if ($result['status'] == ERROR_NONE) {
                $arr = $result['data']['list'];
                foreach ($arr as $k => $v) {
                    $value = '';
                    $min = $v['mini_consumption'];
                    if ($v['type'] == COUPON_TYPE_CASH) {
                        $value = $v['money'] . '元';
                    } elseif ($v['type'] == COUPON_TYPE_DISCOUNT) {
                        $tmp = $v['discount'] * 10;
                        $value = $tmp . '折';
                    } elseif ($v['type'] == COUPON_TYPE_EXCHANGE) {
                        $value = $v['illustrate'];
                    }
                    $coupons_arr[$k]['id'] = $v['id'];
                    $coupons_arr[$k]['title'] = $v['title'];
                    $coupons_arr[$k]['date'] = date('Y.m.d', strtotime($v['start_time'])) . '-' . date('Y.m.d', strtotime($v['end_time']));
                    $coupons_arr[$k]['value'] = $value;

                    //设置默认最优优惠券
                    if ($total < $min) {
                        continue;
                    }
                    if ($v['type'] == COUPON_TYPE_CASH && $v['money'] > $discount) {
                        $money = $v['money'];
                        $optimal['id'] = $v['id'];
                        $optimal['title'] = $v['title'];
                        $optimal['value'] = $money . '元';
                        $optimal['pay'] = $total - $money;
                        $discount = $money;
                        if ($coupons_id == $v['id']) {
                            $activity_coupons['id'] = $v['id'];
                            $activity_coupons['title'] = $v['title'];
                            $activity_coupons['value'] = $money . '元';
                            $activity_coupons['pay'] = $total - $money;
                        }
                    } elseif ($v['type'] == COUPON_TYPE_DISCOUNT) {
                        $money = $total * (1 - $v['discount']);
                        if ($money > $discount) {
                            $optimal['id'] = $v['id'];
                            $optimal['title'] = $v['title'];
                            $tmp = $v['discount'] * 10;
                            $optimal['value'] = $tmp . '折';
                            $optimal['pay'] = $total * $v['discount'];
                            $discount = $money;
                        }
                        if ($coupons_id == $v['id']) {
                            $activity_coupons['id'] = $v['id'];
                            $activity_coupons['title'] = $v['title'];
                            $tmp = $v['discount'] * 10;
                            $activity_coupons['value'] = $tmp . '折';
                            $activity_coupons['pay'] = $total * $v['discount'];
                        }
                    } elseif ($v['type'] == COUPON_TYPE_EXCHANGE && empty($optimal['id'])) {
                        $optimal['id'] = $v['id'];
                        $optimal['title'] = $v['title'];
                        $optimal['value'] = $v['illustrate'];
                        if ($coupons_id == $v['id']) {
                            $activity_coupons['id'] = $v['id'];
                            $activity_coupons['title'] = $v['title'];
                            $activity_coupons['value'] = $v['illustrate'];
                        }
                    }
                }
                if (!empty($activity_coupons)) {
                    $optimal = $activity_coupons;
                }
            }

            if ($optimal['pay'] < 0) {
                $optimal['pay'] = 0;
            }

            $this->render('cartPay', array(
                'sku' => $sku,
                'num' => $num,
                'total' => $total,
                'skuid' => $skuid,
                'coupons_arr' => $coupons_arr,
                'optimal' => $optimal,
                'encrypt_id' => $encrypt_id,
                'is_cart' => $is_cart,
            ));
        }

        if (isset($_POST['sku_name']) && !empty($_POST['sku_name'])) {
            $sku_name = $_POST['sku_name'];
            $product_id = $_POST['product_id'];
            $num = array();
            $num[$product_id] = $_POST['num'];
            $encrypt_id = $_POST['encrypt_id'];
            $mall = new DMallUC();
            $result = json_decode($mall->getSkuByName($sku_name, $product_id));
            if ($result->status == ERROR_NONE) {
                $skuid = array();
                $skuid[$product_id] = $result->data;
                $this->redirect(array('CartPay',
                    'skuid' => $skuid,
                    'num' => $num,
                    'encrypt_id' => $encrypt_id
                ));
            }
        }

    }

    //获取市
    public function actionGetCity()
    {
        if (isset($_POST['code']) && !empty($_POST['code'])) {
            $mall = new DMallUC();
            $city = json_decode($mall->getCity($_POST['code']));
            $c = array();
            if ($city->status == ERROR_NONE) {
                foreach ($city->data as $k => $v) {
                    $c[$k] = $v->name . ',' . $v->code;
                }
            }
            echo json_encode($c);
        }
    }

    //获取区
    public function actionGetArea()
    {
        if (isset($_POST['code']) && !empty($_POST['code'])) {
            $mall = new DMallUC();
            $area = json_decode($mall->getArea($_POST['code']));
            $a = array();
            if ($area->status == ERROR_NONE) {
                foreach ($area->data as $k => $v) {
                    $a[$k] = $v->name . ',' . $v->code;
                }
            }
            echo json_encode($a);
        }
    }

    /**
     * 更新优惠券
     */
    public function actionUpdateCoupons()
    {
        $data = array();
        $data['error'] = 'failure';
        if (isset($_GET['list']) && isset($_GET['money'])) {
            $uc_list = $_GET['list'];
            $user_id = Yii::app()->session['user_id'];
            //剔除m和u
            /*
            $tmp = '';
            $use_discount = false;
            $arr = explode(",", $uc_list);
            foreach ($arr as $k => $v) {
                if (!empty($v) && $v != 'm' && $v != 'u') {
                    $tmp .= $v.',';
                }elseif ($v == 'u') {
                    $use_discount = true;
                }
            }
            if (!empty($tmp)) {
                $tmp = substr($tmp, 0, strlen($tmp) - 1);
            }
            $uc_list = $tmp;
            */

            $money = $_GET['money']; //总金额

            $coupons = new CouponsUC();
            $ret = $coupons->getCouponsPay($user_id, $uc_list, $money, false);
            $result = json_decode($ret, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data']['need'])) {
                    $data['error'] = 'success';
                    $data['need'] = $result['data']['need'] + 0;
                }
            } else {
                $data['errMsg'] = $result['errMsg'];
            }
        }
        echo json_encode($data);
    }

}
	