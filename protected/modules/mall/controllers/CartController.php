<?php

/**
 * 购物车
 * */
class CartController extends MallController
{

    /** 验证是否登陆
     * @param $encrypt_id
     */
    public function isLogin($encrypt_id)
    {
        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $this->redirect(Yii::app()->createUrl('mobile/auth/login', array('encrypt_id' => $encrypt_id)));
            return;
        }
    }

    //加入购物车
    public function actionAddCart()
    {
        $encrypt_id = $_POST['encrypt_id'];
        //判断是否已登录
        if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
            $result['status'] = 'noLogin';
            $result['errMsg'] = '请进行登录操作';
            echo json_encode($result);
            exit();
        }

        if (isset($_POST['sku_name']) && !empty($_POST['sku_name'])) {

            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
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

            $sku_name = $_POST['sku_name'];

            $mall = new MallUC();
            $result = $mall->addCart($product_id, $sku_name, $num, $user_id);
            echo $result;
        }
    }


    //购物车列表
    public function actionCartList()
    {
        $encrypt_id = $_GET['encrypt_id'];
        //判断是否登录
        $this->isLogin($encrypt_id);

        $user_id = Yii::app()->session[$encrypt_id . 'user_id'];
        $mall = new MallUC();
        $result = json_decode($mall->getUserCart($user_id));
        if ($result->status == ERROR_NONE) {
            $this->render('cartList', array(
                'cart' => $result->data->list,
                'encrypt_id' => $encrypt_id
            ));
        }
    }

    //删除购物车
    public function actionDelCart()
    {
        if (isset($_POST['cart_id']) && !empty($_POST['cart_id'])) {
            $cart_id = $_POST['cart_id'];
            $mall = new MallUC();
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
            $mall = new MallUC();
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
        $encrypt_id = $_POST['encrypt_id'];
        if (isset($_GET['encrypt_id']) && !empty($_GET['encrypt_id'])) {
            $encrypt_id = $_GET['encrypt_id'];
        }

        if ((isset($_POST['skuid']) && !empty($_POST['skuid'])) || (isset($_GET['skuid']) && !empty($_GET['skuid']))) {
            if (!isset(Yii::app()->session[$encrypt_id . 'user_id']) || empty(Yii::app()->session[$encrypt_id . 'user_id'])) {
                if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
                    //购物车
                } else {
                    if (isset($_GET['skuid']) && !empty($_GET['skuid'])) {
                        $this->redirect(Yii::app()->createUrl('mobile/auth/register', array(
                            'goUrl' => Yii::app()->createUrl('mall/Cart/CartPay', array(
                                'skuid' => $_GET['skuid'],
                                'num' => $_GET['num'],
                                'encrypt_id' => $encrypt_id
                            )),
                            'encrypt_id' => $encrypt_id
                        )));
                    } else {
                        $this->redirect(Yii::app()->createUrl('mobile/auth/register', array('encrypt_id' => $encrypt_id)));
                    }
                }
                return;
            }
            
            $num = empty($_POST['num']) ? $_GET['num'] : $_POST['num'];
            $skuid = empty($_POST['skuid']) ? $_GET['skuid'] : $_POST['skuid'];
            $user_id = Yii::app()->session[$encrypt_id . 'user_id'];

            if (isset($_POST['is_cart']) && !empty($_POST['is_cart'])) {
                $is_cart = $_POST['is_cart'];
            } else {
                $is_cart = IS_CART_NO;
            }

            //获取收货地址
            $user = new UserUC();
            $result = json_decode($user->getUserAddress($user_id));
            if ($result->status == ERROR_NONE) {
                $address = $result->data->list;
                if (!empty($result->data->default)) {
                    $defaultaddress = $result->data->default;
                } else {
                    $defaultaddress = '';
                }
            }
            $mall = new MallUC();
            //获取sku信息 / 计算总价和运费
            $skuinfo = json_decode($mall->getProductSkuInfo($skuid, $num));
            $total = 0;
            if ($skuinfo->status == ERROR_NONE) {
                $sku = $skuinfo->data;
                $arr_sku = $skuinfo->arr_sku;
                foreach ($sku as $k => $v) {
                    $total += $v->price * $num[$k];
                }
            } else {
                $url = Yii::app()->createUrl('mall/Commodity/index', array('encrypt_id' => $encrypt_id));
                echo "<script>alert('" . $skuinfo->errMsg . "');window.location.href='$url'</script>";
                Yii::app()->end();
            }

            //读取省市区
            $province = json_decode($mall->getProvince());

            //计算运费
            $orderMall = new OrderMall();
            $res_freight = json_decode($orderMall->countFreight($defaultaddress->id, $arr_sku, $num));
            if ($res_freight->status == ERROR_NONE) {
                $freightMoney = $res_freight->data;
            } else {
                Yii::app()->user->setFlash('error', $res_freight->errMsg);
            }

            $this->render('cartPay', array(
                'sku' => $sku,
                'address' => $address,
                'num' => $num,
                'total' => $total,
                'default' => $defaultaddress,
                'skuid' => $skuid,
                'province' => $province->data,
                'is_cart' => $is_cart,
                'freightMoney' => $freightMoney,
                'encrypt_id' => $encrypt_id
            ));
        }

        if (isset($_POST['sku_name']) && !empty($_POST['sku_name'])) {
            $sku_name = $_POST['sku_name'];
            $product_id = $_POST['product_id'];
            $num = array();
            $num[$product_id] = $_POST['num'];
            $mall = new MallUC();
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
            $mall = new MallUC();
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
            $mall = new MallUC();
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


}
	