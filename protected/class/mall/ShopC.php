<?php
/**
 * 商品管理类
 */
include_once(dirname(__FILE__) . '/../mainClass.php');

class ShopC extends mainClass
{
    //商品分页
    public $page = null;
    //商品编辑分页
    public $pagegroup = null;

    /**
     * 查询商品分组
     * merchantId 商户id
     * shopname 商品名称
     */
    public function shopGroupList($merchantId, $shopname)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $criteria = new CDbCriteria();
            $criteria->order = 'create_time desc';
            if (isset($shopname) && !empty($shopname)) {
                //如果是搜索商品分组
                $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria->params = array(
                    ':merchant_id' => $merchantId,
                    ':flag' => FLAG_NO,
                );
                $criteria->compare('name', $shopname, true);
            } else {
                $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria->params = array(
                    ':merchant_id' => $merchantId,
                    ':flag' => FLAG_NO,
                );
            }

            //显示分页
            $pages = new CPagination(ShopGroup::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $model = ShopGroup::model()->findAll($criteria);
            $data = array();
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $data[$key]['id'] = $value['id'];
                    $data[$key]['merchant_id'] = $value['merchant_id'];
                    $data[$key]['name'] = $value['name'];
                    //商品数
                    $model_count = ShopProduct::model()->findAll('merchant_id=:merchant_id and
                    group_id=:group_id and flag=:flag', array(':merchant_id' => $merchantId, ':group_id' => $value['id'], ':flag' => FLAG_NO));
                    $data[$key]['count'] = count($model_count);
                    $data[$key]['create_time'] = $value['create_time'];
                    $data[$key]['last_time'] = $value['last_time'];
                    $data[$key]['flag'] = $value['flag'];
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 删除商品分组
     * id 商品分组id
     * merchantId  商户id
     */
    public function deleteShopGroup($id, $merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            $model = ShopGroup::model()->findByPk($id);
            if (!empty($model)) {
                $model->flag = FLAG_YES;
                if ($model->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '数据保存错误'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 编辑商品分组
     */
    public function editShopGroup($id, $merchantId, $name)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            if (empty($name)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数name不能为空');
            }
            $shopgroup = ShopGroup::model()->findByPk((int)$id);
            $shopgroup->merchant_id = $merchantId;
            $shopgroup->name = $name;
            $shopgroup->last_time = new CDbExpression('now()');
            $shopgroup->flag = FLAG_NO;
            if ($shopgroup->update()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商品分组
     */

    /**
     * 添加商品分组
     */
    public function addShopGroup($merchantId, $name)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            if (empty($name)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数name不能为空');
            }

            //首先判断该商品分组名是否存在
            $model = ShopGroup::model()->find('name=:name and merchant_id=:merchant_id and flag=:flag', array(
                ':name' => $name,
                ':merchant_id' => $merchantId,
                ':flag' => FLAG_NO
            ));
            if (empty($model)) {
                $shopgroup = new ShopGroup();
                $shopgroup->merchant_id = $merchantId;
                $shopgroup->name = $name;
                $shopgroup->create_time = new CDbExpression('now()');
                $shopgroup->flag = FLAG_NO;
                if ($shopgroup->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                    $result['errMsg'] = '保存失败'; //错误信息
                }
            } else {
                $result['status'] = ERROR_DUPLICATE_DATA; //状态码
                $result['errMsg'] = '保存失败'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //获取商品列表
    public function getProductList($merchant_id, $pro_status, $group_id, $key_word, $arrow, $arrow_type)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);
            if (!empty($pro_status)) {
                if ($pro_status == SHOP_PRODUCT_STATUS_UP) { //出售中
                    $criteria->addCondition('status = :status');
                    $criteria->params[':status'] = SHOP_PRODUCT_STATUS_UP;
//	            	$criteria->addCondition('stock_num > :stock_num');
//	            	$criteria->params[':stock_num'] = 0;
                } elseif ($pro_status == SHOP_PRODUCT_STATUS_SOLDOUT) { //售罄
                    $criteria->addCondition('stock_num = :stock_num');
                    $criteria->params[':stock_num'] = 0;
                } elseif ($pro_status == SHOP_PRODUCT_STATUS_DOWN) { //下架
                    $criteria->addCondition('status = :status');
                    $criteria->params[':status'] = SHOP_PRODUCT_STATUS_DOWN;
                }
            }

            if (!empty($group_id)) {
                $criteria->addCondition('group_id = :group_id');
                $criteria->params[':group_id'] = $group_id;
// 				$criteria->params = array(':group_id'=>$group_id);
            }
            if (!empty($key_word)) {
                $criteria->compare('name', $key_word, true);
            }

            if ($arrow_type == 'create_time') {
                if ($arrow == 'arrowUp') {
                    $criteria->order = 'create_time desc';
                } else {
                    $criteria->order = 'create_time asc';
                }
            } else if ($arrow_type == 'price') {
                if ($arrow == 'arrowUp') {
                    $criteria->order = 'price desc';
                } else {
                    $criteria->order = 'price asc';
                }
            }


            //显示分页
            $pages = new CPagination(ShopProduct::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $product = ShopProduct::model()->findAll($criteria);
            $data = array();
            if ($product) {
                foreach ($product as $k => $v) {

                    $data['list'][$k]['id'] = $v->id;
                    $data['list'][$k]['name'] = $v->name;
                    $data['list'][$k]['price'] = $v->price;
                    $img_arr = explode(";", $v->img);
                    $data['list'][$k]['img'] = $img_arr[0];
                    $data['list'][$k]['create_time'] = $v->create_time;
                    $data['list'][$k]['ts_product_id'] = isset($v->ts_product_id) ? $v->ts_product_id : '';
                    $count_buyed_num = 0;
                    $count_kucun = 0;
                    $product_sku = ShopProductSku::model()->findAll('product_id=:product_id and flag = :flag', array(
                        ':product_id' => $v->id,
                        ':flag' => FLAG_NO
                    ));
                    foreach ($product_sku as $x => $y) {
                        $count_buyed_num += $y->sold_num;
                        $count_kucun += $y->num - $y->sold_num;
                    }
                    $data['list'][$k]['sold_num'] = $count_buyed_num;
                    $data['list'][$k]['num'] = $count_kucun;
                }
                //给list排序
                if ($arrow_type == 'stock') {
                    //按库存排序
                    if ($arrow == 'arrowUp') {
                        //降序
                        for ($i = 0; $i < count($data['list']) - 1; $i++) {
                            for ($j = $i + 1; $j < count($data['list']); $j++) {
                                if ($data['list'][$i]['num'] < $data['list'][$j]['num']) {
                                    $tmp_id = $data['list'][$j]['id'];
                                    $tmp_name = $data['list'][$j]['name'];
                                    $tmp_price = $data['list'][$j]['price'];
                                    $tmp_img = $data['list'][$j]['img'];
                                    $tmp_create_time = $data['list'][$j]['create_time'];
                                    $tmp_sold_num = $data['list'][$j]['sold_num'];
                                    $tmp_num = $data['list'][$j]['num'];

                                    $data['list'][$j]['id'] = $data['list'][$i]['id'];
                                    $data['list'][$j]['name'] = $data['list'][$i]['name'];
                                    $data['list'][$j]['price'] = $data['list'][$i]['price'];
                                    $data['list'][$j]['img'] = $data['list'][$i]['img'];
                                    $data['list'][$j]['create_time'] = $data['list'][$i]['create_time'];
                                    $data['list'][$j]['sold_num'] = $data['list'][$i]['sold_num'];
                                    $data['list'][$j]['num'] = $data['list'][$i]['num'];

                                    $data['list'][$i]['id'] = $tmp_id;
                                    $data['list'][$i]['name'] = $tmp_name;
                                    $data['list'][$i]['price'] = $tmp_price;
                                    $data['list'][$i]['img'] = $tmp_img;
                                    $data['list'][$i]['create_time'] = $tmp_create_time;
                                    $data['list'][$i]['sold_num'] = $tmp_sold_num;
                                    $data['list'][$i]['num'] = $tmp_num;
                                }
                            }
                        }
                    } else if ($arrow == 'arrowDown') {
                        //升序
                        for ($i = 0; $i < count($data['list']) - 1; $i++) {
                            for ($j = $i + 1; $j < count($data['list']); $j++) {
                                if ($data['list'][$i]['num'] > $data['list'][$j]['num']) {
                                    $tmp_id = $data['list'][$j]['id'];
                                    $tmp_name = $data['list'][$j]['name'];
                                    $tmp_price = $data['list'][$j]['price'];
                                    $tmp_img = $data['list'][$j]['img'];
                                    $tmp_create_time = $data['list'][$j]['create_time'];
                                    $tmp_sold_num = $data['list'][$j]['sold_num'];
                                    $tmp_num = $data['list'][$j]['num'];

                                    $data['list'][$j]['id'] = $data['list'][$i]['id'];
                                    $data['list'][$j]['name'] = $data['list'][$i]['name'];
                                    $data['list'][$j]['price'] = $data['list'][$i]['price'];
                                    $data['list'][$j]['img'] = $data['list'][$i]['img'];
                                    $data['list'][$j]['create_time'] = $data['list'][$i]['create_time'];
                                    $data['list'][$j]['sold_num'] = $data['list'][$i]['sold_num'];
                                    $data['list'][$j]['num'] = $data['list'][$i]['num'];

                                    $data['list'][$i]['id'] = $tmp_id;
                                    $data['list'][$i]['name'] = $tmp_name;
                                    $data['list'][$i]['price'] = $tmp_price;
                                    $data['list'][$i]['img'] = $tmp_img;
                                    $data['list'][$i]['create_time'] = $tmp_create_time;
                                    $data['list'][$i]['sold_num'] = $tmp_sold_num;
                                    $data['list'][$i]['num'] = $tmp_num;
                                }
                            }
                        }
                    }
                }
                if ($arrow_type == 'volume') {
                    //按总销量排序
                    if ($arrow == 'arrowUp') {
                        //降序
                        for ($i = 0; $i < count($data['list']) - 1; $i++) {
                            for ($j = $i + 1; $j < count($data['list']); $j++) {
                                if ($data['list'][$i]['sold_num'] < $data['list'][$j]['sold_num']) {
                                    $tmp_id = $data['list'][$j]['id'];
                                    $tmp_name = $data['list'][$j]['name'];
                                    $tmp_price = $data['list'][$j]['price'];
                                    $tmp_img = $data['list'][$j]['img'];
                                    $tmp_create_time = $data['list'][$j]['create_time'];
                                    $tmp_sold_num = $data['list'][$j]['sold_num'];
                                    $tmp_num = $data['list'][$j]['num'];

                                    $data['list'][$j]['id'] = $data['list'][$i]['id'];
                                    $data['list'][$j]['name'] = $data['list'][$i]['name'];
                                    $data['list'][$j]['price'] = $data['list'][$i]['price'];
                                    $data['list'][$j]['img'] = $data['list'][$i]['img'];
                                    $data['list'][$j]['create_time'] = $data['list'][$i]['create_time'];
                                    $data['list'][$j]['sold_num'] = $data['list'][$i]['sold_num'];
                                    $data['list'][$j]['num'] = $data['list'][$i]['num'];

                                    $data['list'][$i]['id'] = $tmp_id;
                                    $data['list'][$i]['name'] = $tmp_name;
                                    $data['list'][$i]['price'] = $tmp_price;
                                    $data['list'][$i]['img'] = $tmp_img;
                                    $data['list'][$i]['create_time'] = $tmp_create_time;
                                    $data['list'][$i]['sold_num'] = $tmp_sold_num;
                                    $data['list'][$i]['num'] = $tmp_num;
                                }
                            }
                        }
                    } else if ($arrow == 'arrowDown') {
                        //升序
                        for ($i = 0; $i < count($data['list']) - 1; $i++) {
                            for ($j = $i + 1; $j < count($data['list']); $j++) {
                                if ($data['list'][$i]['sold_num'] > $data['list'][$j]['sold_num']) {
                                    $tmp_id = $data['list'][$j]['id'];
                                    $tmp_name = $data['list'][$j]['name'];
                                    $tmp_price = $data['list'][$j]['price'];
                                    $tmp_img = $data['list'][$j]['img'];
                                    $tmp_create_time = $data['list'][$j]['create_time'];
                                    $tmp_sold_num = $data['list'][$j]['sold_num'];
                                    $tmp_num = $data['list'][$j]['num'];

                                    $data['list'][$j]['id'] = $data['list'][$i]['id'];
                                    $data['list'][$j]['name'] = $data['list'][$i]['name'];
                                    $data['list'][$j]['price'] = $data['list'][$i]['price'];
                                    $data['list'][$j]['img'] = $data['list'][$i]['img'];
                                    $data['list'][$j]['create_time'] = $data['list'][$i]['create_time'];
                                    $data['list'][$j]['sold_num'] = $data['list'][$i]['sold_num'];
                                    $data['list'][$j]['num'] = $data['list'][$i]['num'];

                                    $data['list'][$i]['id'] = $tmp_id;
                                    $data['list'][$i]['name'] = $tmp_name;
                                    $data['list'][$i]['price'] = $tmp_price;
                                    $data['list'][$i]['img'] = $tmp_img;
                                    $data['list'][$i]['create_time'] = $tmp_create_time;
                                    $data['list'][$i]['sold_num'] = $tmp_sold_num;
                                    $data['list'][$i]['num'] = $tmp_num;
                                }
                            }
                        }
                    }
                }
            } else {
                $data['list'] = array();
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;

        } catch (Exception $e) {

            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取商品规格模板
     * @param $merchant_id
     */
    public function getProStandard($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            $list[0] = "请选择规格模板";
            $model = ShopStandardtemplet::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $list[$value['id']] = $value['name'];
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $list;
            $result['errMsg'] = "";
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 获取商品分组
     * @param $merchant_id
     */
    public function getShopGroup($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            $list[0] = "选择商品分组";
            $model = ShopGroup::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $list[$value['id']] = $value['name'];
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $list;
            $result['errMsg'] = "";
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 获取运费模板
     * @param $merchant_id
     */
    public function getShopFreight($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            $list[0] = "请选择运费模板";
            $model = ShopFreight::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $list[$value['id']] = $value['name'];
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $list;
            $result['errMsg'] = "";
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 保存商品规格模板
     * @param  $name
     * @param  $content
     * @param  $save_type
     * @param  $model_id
     */
    public function saveProStandard($name, $content, $save_type, $model_id, $merchant_id)
    {
        $result = array();
        $falg = true;
        try {
            $transaction = Yii::app()->db->beginTransaction();

            if ($save_type == SHOP_STANDARD_SAVE_OLD) {
                $standardtemplet = ShopStandardtemplet::model()->findByPk($model_id);
                ShopStandard::model()->updateAll(array("flag" => FLAG_YES), "standardtemple_id = :standardtemple_id", array(':standardtemple_id' => $standardtemplet['id']));
                $name = $standardtemplet['name'];
            } else {
                $standardtemplet = new ShopStandardtemplet();
            }
            $standardtemplet['merchant_id'] = $merchant_id;
            $standardtemplet['name'] = $name;
            $standardtemplet['create_time'] = new CDbExpression('now()');
            $standardtemplet['last_time'] = new CDbExpression('now()');
            if ($standardtemplet->save()) {
                $id = $standardtemplet['id'];
                foreach ($content as $key => $value) {
                    if ($key) { //排除颜色属性
                        $attri = "";
                        foreach ($value as $title => $v) {
                            foreach ($v as $att) {
                                $att .= ',';
                                $attri .= $att;
                            }
                        }
                        $model = new ShopStandard();
                        $model['name'] = $title;
                        $model['attribute'] = $attri;
                        $model['standardtemple_id'] = $id;
                        $model['create_time'] = new CDbExpression('now()');
                        $model['last_time'] = new CDbExpression('now()');
                        if ($model->save()) {

                        } else {
                            $falg = false;
                            throw new Exception('商品规格保存失败');
                        }
                    }
                }
            } else {
                $falg = false;
                throw new Exception('模板保存失败');
            }
            if ($falg) {
                $transaction->commit();
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = "";
                $result['id'] = $id;
                $result['name'] = $name;
                $result['type'] = $save_type;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取商品规格模板
     * @param  $standard_id
     */
    public function getSandard($standard_id)
    {
        $result = array();
        $html = "";
        try {
            $model = ShopStandard::model()->findAll('standardtemple_id = :standardtemple_id and flag = :flag', array(':standardtemple_id' => $standard_id, ':flag' => FLAG_NO));
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $html .= '<ul class="ul01">';
                    $html .= '<li class="first" val="' . $value['name'] . '">' . $value['name'] . '<a href="##" name="edit">编辑</a> <a href="##" name="del">删除</a></li>';
                    $list = explode(",", $value['attribute']);
                    foreach ($list as $att) {
                        if (!empty($att)) {
                            $html .= '<li><input type="checkbox" value="' . $att . '">' . $att . '</li>';
                        }
                    }

                    $html .= '</ul>';
                }
            }
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = "";
            $result['data'] = $html;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 保存商品信息
     * @param $id
     * @param $merchant_id
     * @param $category_id
     * @param $group_id
     * @param $standard
     * @param $page_id
     * @param $type
     * @param $name
     * @param $price
     * @param $img_arr
     * @param $freight_type
     * @param $freight_money
     * @param $freight_id
     * @param $if_invoice
     * @param $sku_arr
     * @param $limit_num
     * @param $brief_introduction
     * @param $detailed_introduction
     * @param $if_show_num
     * @param $stock_num
     */
    public function saveProduct($id, $merchant_id, $category_id, $group_id, $standard, $page_id, $type, $name, $price, $img_arr, $freight_type, $freight_money, $freight_id, $if_invoice, $sku_arr, $limit_num, $brief_introduction, $detailed_introduction, $if_show_num, $stock_num)
    {
        $result = array();
        $flag = true;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            if (isset($id) && $id) {
                $model = ShopProduct::model()->findByPk($id);
                ShopProductSku::model()->updateAll(array("flag" => FLAG_YES), "product_id = :product_id", array(':product_id' => $id));
            } else {
                $model = new ShopProduct();
            }
            $model['status'] = SHOP_PRODUCT_STATUS_UP;
            $model['merchant_id'] = $merchant_id;
            $model['category_id'] = $category_id;
            $model['group_id'] = $group_id;
            $model['standard'] = $standard;
            $model['page_id'] = $page_id;
            $model['type'] = $type;
            $model['name'] = $name;
            $model['price'] = $price;
            $model['freight_type'] = $freight_type;
            if ($freight_type == SHOP_FREIGHT_TYPE_UNITE) {
                $model['freight_money'] = $freight_money;
            } elseif ($freight_type == SHOP_FREIGHT_TYPE_MODEL) {
                $model['freight_id'] = $freight_id;
            }
            $model['if_invoice'] = $if_invoice;
            $model['if_show_num'] = $if_show_num;
            $model['stock_num'] = $stock_num;
            //保存图片
            if (!empty($img_arr)) {
                $img = "";
                foreach ($img_arr as $key => $val) {
                    if ($key == 0) {
                        $img = $val;
                    } else {
                        $img .= ';' . $val;
                    }
                }
                $model['img'] = $img;
            } else {
                $model['img'] = "";
            }
            $model['limit_num'] = $limit_num;
            $model['create_time'] = new CDbExpression('now()');
            $model['last_time'] = new CDbExpression('now()');
            $model['brief_introduction'] = $brief_introduction;
            $model['detailed_introduction'] = $detailed_introduction;

            if ($model->save()) {
                $pro_id = $model['id'];
                if (!empty($sku_arr)) {
                    $sku_flag = $this->saveProSku($merchant_id, $sku_arr, $pro_id);
                    if (!$sku_flag) {
                        $flag = false;
                        throw new Exception('商品sku保存失败');
                    }
                } else {
                    $flag = false;
                    throw new Exception('数据空');
                }
            } else {
                $flag = false;
                throw new Exception('商品保存失败');
            }

            if ($flag) {
                $transaction->commit();
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = "";
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }

    /**
     * 保存商品sku
     * @param $merchant_id
     * @param $sku_arr
     * @param $pro_id
     */
    public function saveProSku($merchant_id, $sku_arr, $pro_id)
    {
        $result = true;
        foreach ($sku_arr as $key => $value) {
            $model = new ShopProductSku();
            $model['product_id'] = $pro_id;
            $model['name'] = $value['att'];
            $model['price'] = $value['sku_new_prize'];
            $model['num'] = $value['sku_number'];
            $model['original_price'] = $value['sku_old_prize'];
            $model['merchant_no'] = $value['sku_code'];
            $model['create_time'] = new CDbExpression('now()');
            $model['last_time'] = new CDbExpression('now()');
            if ($model->save()) {

            } else {
                $result = false;
                throw new Exception('商品保存失败');
            }
        }
        return $result;
    }

    /**
     * 获取商品信息
     * @param unknown $id
     */
    public function getProduct($id)
    {
        $result = array();
        $list = array();
        try {
            $pro_model = ShopProduct::model()->findByPk($id);
            $sku_model = ShopProductSku::model()->findAll(array(
                'condition' => 'product_id = :product_id and flag = :flag',
// 				'order' => 'id',
                'params' => array(":product_id" => $id, ":flag" => FLAG_NO)));

            $list['id'] = $id;
            if (!empty($pro_model['category_id'])) {
                $category = explode(",", $pro_model['category_id']);
                $list['category_one'] = $category[0];
                $list['category_two'] = $category[1];
            }


            $list['group_id'] = $pro_model['group_id'];
            $list['pro_standard_att'] = $pro_model['standard'];
            $standard_one = explode(";", $pro_model['standard']);
            if (!empty($standard_one)) {
                foreach ($standard_one as $key => $value) {
                    if (!empty($value) && $value) {
                        $standard_two = explode(":", $value);
                        $title = $standard_two[0];
                        $att = $standard_two[1];
                        $standard_three = explode(",", $att);
                        if (!empty($standard_three) && $standard_three) {
                            foreach ($standard_three as $k => $v) {
                                if (!empty($v) && $v) {
                                    $list['att'][$title][] = $v;
                                }
                            }
                        }
                    } else {
                        $list['att']['颜色'][] = "";
                    }
                }
            }

            $list['page_id'] = $pro_model['page_id'];
            //获取模板信息
            if ($pro_model['page_id']) {
                $stand_temp_model = ShopStandardtemplet::model()->find('id = :id and flag = :flag', array(':id' => $pro_model['page_id'], ':flag' => FLAG_NO));
                if (!empty($stand_temp_model)) {
//     				$list['standard']['name'] = $stand_temp_model['name'];
                    $stand_model = ShopStandard::model()->findAll('standardtemple_id = :standardtemple_id and flag = :flag', array(':standardtemple_id' => $pro_model['page_id'], ':flag' => FLAG_NO));
                    if (!empty($stand_model)) {
                        foreach ($stand_model as $key => $value) {
                            $attribute_arr = explode(",", $value['attribute']);
                            foreach ($attribute_arr as $v) {
                                if (isset($v)) {
                                    $list['standard'][$value['id']]['name'] = $value['name'];
                                    $list['standard'][$value['id']]['attribute'][] = $v;
                                }
                            }
                        }
                    }
                }
            } else {
                $list['standard'] = "";
            }
//     		CVarDumper::dump($list['standard']);
//     		exit();

            //获取产品sku信息
            $num = 0;
            if (!empty($sku_model)) {
                foreach ($sku_model as $key => $value) {
                    $list['pro_sku'][$value['id']]['property'] = $value['name'];
                    $list['pro_sku'][$value['id']]['num'] = $value['num'];
                    $list['pro_sku'][$value['id']]['price'] = $value['price'];
                    $list['pro_sku'][$value['id']]['original_price'] = $value['original_price'];
                    $num += $value['num'];
                }
            }
            $list['num'] = $num;
            //产品图片
            if (!empty($pro_model['img'])) {
                $img_arr = explode(";", $pro_model['img']);
                $list['img'] = $img_arr;
            }

            $list['type'] = $pro_model['type'];
            $list['name'] = $pro_model['name'];
            $list['price'] = $pro_model['price'];
            $list['freight_type'] = $pro_model['freight_type'];
            $list['freight_money'] = $pro_model['freight_money'];
            $list['freight_id'] = $pro_model['freight_id'];
            $list['limit_num'] = $pro_model['limit_num'];
            $list['if_invoice'] = $pro_model['if_invoice'];
            $list['if_show_num'] = $pro_model['if_show_num'];
            $list['brief_introduction'] = $pro_model['brief_introduction'];
            $list['detailed_introduction'] = $pro_model['detailed_introduction'];

            $result['status'] = ERROR_NONE;
            $result['errMsg'] = "";
            $result['data'] = $list;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return $result;
    }


    /**
     * 商品首页编辑列表
     * merchantId
     * name 搜索名字
     */
    public function shopBodyEditList($merchantId, $name = "")
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $criteria = new CDbCriteria();
            $criteria->order = 'create_time desc';
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(
                ':merchant_id' => $merchantId,
                ':flag' => FLAG_NO,
            );
            if (!empty($name)) {
//                $criteria->compare('name',$name,true);
                $criteria->addSearchCondition('name', $name);
            }
            //显示分页
            $pagegroup = new CPagination(ShopGroup::model()->count($criteria));
            $pagegroup->pageSize = 5;
            $pagegroup->applyLimit($criteria);
            $this->page = $pagegroup;

            $shopindex_model = ShopGroup::model()->findAll($criteria);
            if (!empty($shopindex_model)) {
                $data = array();
                foreach ($shopindex_model as $key => $value) {
                    $data[$key]['id'] = $value['id'];
                    $data[$key]['name'] = $value['name'];
                    $data[$key]['create_time'] = $value['create_time'];
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA; //状态码
                $result['errMsg'] = '无此数据'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 保存商品分组编辑
     * id_array 要存放的商品分组ID，用逗号隔开(1,2,3)
     */
    public function saveShopBodyEdit($merchantId, $id_array)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $model = new ShopIndex();
            if (!empty($id_array) && isset($id_array)) {
                $model->merchant_id = $merchantId;
                $model->banner = "json";
                $model->group_id = $id_array;
                $date = new CDbExpression('now()');
                $model->create_time = $id_array;
                if ($model->save()) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['status'] = ERROR_EXCEPTION; //状态码
                    $result['errMsg'] = '保存失败'; //错误信息
                }

            } else {
                $result['status'] = ERROR_EXCEPTION; //状态码
                $result['errMsg'] = '保存失败'; //错误信息
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 查询添加过的商品区分组
     * @param $merchantId
     * @return string
     */
    public function queryShopBody($merchantId)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            //数据库查询
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
            $criteria->params = array(
                ':merchant_id' => $merchantId,
                ':flag' => FLAG_NO,
            );
            $model = ShopIndex::model()->find($criteria);

            if (!empty($model) && isset($model)) {
                $id_array = array();
                $id_array = explode(',', $model['group_id']);
                $data = array();
                if (isset($id_array) && !empty($id_array)) {
                    for ($i = 0; $i < count($id_array); $i++) {
                        $model_group = ShopGroup::model()->findByPk($id_array[$i]);
                        $data[$i]['id'] = $model_group['id'];
                        $data[$i]['merchant_id'] = $model_group['merchant_id'];
                        $data[$i]['name'] = $model_group['name'];
                    }
                    $result['data'] = $data;
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                } else {
                    $result['status'] = ERROR_EXCEPTION; //状态码
                    $result['errMsg'] = '没有数据'; //错误信息
                }
            } else {
                $result['status'] = ERROR_EXCEPTION; //状态码
                $result['errMsg'] = '没有数据'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    public function delProduct($merchantId, $del_arr)
    {
        $transcation = Yii::app()->db->beginTransaction();
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            if (!empty($del_arr)) {
                $flag = true;
                for ($i = 0; $i < count($del_arr); $i++) {
                    $model = ShopProduct::model()->findByPk($del_arr[$i]);
                    if ($model) {
                        $model->flag = FLAG_YES;
                        if ($model->update()) {

                        } else {
                            $result['status'] = ERROR_SAVE_FAIL;
                            throw new Exception('商品删除失败');
                        }
                    } else {
                        $result['status'] = ERROR_NO_DATA;
                        throw new Exception('该商品不存在');
                    }
                }

                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $transcation->commit();
            }

        } catch (Exception $e) {
            $transcation->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商品下架
     */
    public function UnderCarriage($merchantId, $uc_arr)
    {
        $transcation = Yii::app()->db->beginTransaction();
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            if (!empty($uc_arr)) {
                $flag = true;
                for ($i = 0; $i < count($uc_arr); $i++) {
                    $model = ShopProduct::model()->find('id=:id and merchant_id=:merchant_id', array(':merchant_id' => $merchantId, ':id' => $uc_arr[$i]));
                    $model->status = SHOP_PRODUCT_STATUS_DOWN;
                    if (!$model->save()) {
                        $flag = false;
                        $transcation->rollback();
                    }
                }
                if ($flag) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $transcation->commit();
                } else {
                    $result['status'] = ERROR_EXCEPTION; //状态码
                    $result['errMsg'] = '下架失败'; //错误信息
                }
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商品上架
     */
    public function UpCarriage($merchantId, $uc_arr)
    {
        $transcation = Yii::app()->db->beginTransaction();
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            if (!empty($uc_arr)) {
                $flag = true;
                for ($i = 0; $i < count($uc_arr); $i++) {
                    $model = ShopProduct::model()->find('id=:id and merchant_id=:merchant_id', array(':merchant_id' => $merchantId, ':id' => $uc_arr[$i]));
                    $model->status = SHOP_PRODUCT_STATUS_UP;
                    if (!$model->save()) {
                        $flag = false;
                        $transcation->rollback();
                    }
                }
                if ($flag) {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = ''; //错误信息
                    $transcation->commit();
                } else {
                    $result['status'] = ERROR_EXCEPTION; //状态码
                    $result['errMsg'] = '下架失败'; //错误信息
                }
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 查询分组名是否重复
     */
    public function ShopNameIsRepeat($merchantId, $name, $id = null)
    {
        $result = array();
        try {
            if (empty($merchantId)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数merchant_id不能为空');
            }
            //数据库查询
            $model = ShopGroup::model()->find('merchant_id=:merchant_id and name=:name and flag=:flag', array(
                ':merchant_id' => $merchantId,
                ':name' => $name,
                ':flag' => FLAG_NO
            ));
            if (isset($model)) {
                //分组名重复
                if (empty($id))
                    $result['status'] = ERROR_DUPLICATE_DATA;
                else {
                    //编辑判断
                    if ($model->id == $id) {
                        $result['status'] = ERROR_NONE;
                    } else {
                        $result['status'] = ERROR_DUPLICATE_DATA;
                    }
                }
            } else {
                $result['status'] = ERROR_NONE;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 天时添加商品
     * @param type $ts_product_id
     * @param type $name
     * @param type $amount
     * @param type $original_price
     * @param type $image
     * @param type $nett_price
     * @param type $nett_price2
     * @param type $merchant_id
     * @param type $groupId
     * @return type
     */
    public function AddTianShiProduct($ts_product_id, $name, $amount, $original_price, $image, $nett_price, $nett_price2, $merchant_id, $groupId)
    {
        $result = array();
        $shopProduct = ShopProduct::model()->find('ts_product_id=:ts_product_id and flag=:flag and merchant_id=:merchant_id', array(':ts_product_id' => $ts_product_id, ':flag' => FLAG_NO, ':merchant_id' => $merchant_id));
        if (empty($shopProduct)) {
            $shopProduct = new ShopProduct();
        }
        if (empty($shopProduct->group_id)) {
            $shopProduct->group_id = $groupId;
        }
        $shopProduct->ts_product_id = $ts_product_id;
        $shopProduct->type = SHOP_PRODUCT_TYPE_VIRTAL;//虚拟
        $shopProduct->category_id = 'ticket,t1';
        $shopProduct->name = $name;
        $shopProduct->img = $image;
        if (empty($shopProduct->status)) {
            $shopProduct->status = SHOP_PRODUCT_STATUS_DOWN;//下架
        }
        $shopProduct->merchant_id = $merchant_id;
        $shopProduct->freight_type = SHOP_FREIGHT_TYPE_UNITE;
        $shopProduct->freight_money = 0;
        if (empty($shopProduct->create_time)) {
            $shopProduct->create_time = date('Y-m-d H:i:s');
        }
        if ($nett_price) {
            $shopProduct->standard = '类型:成人票,;';
        }
        if ($nett_price2) {
            $shopProduct->standard = '类型:儿童票,;';
        }
        if ($nett_price && $nett_price2) {
            $shopProduct->standard = '类型:成人票,儿童票,;';
        }
        if (empty($nett_price2)) {
            $shopProduct->price = $nett_price;
        }
        if (empty($nett_price)) {
            $shopProduct->price = $nett_price2;
        }
        if (!empty($nett_price) && !empty($nett_price2)) {
            if ($nett_price > $nett_price2) {
                $shopProduct->price = $nett_price2;
            }
            if ($nett_price < $nett_price2) {
                $shopProduct->price = $nett_price;
            }
        }
        if ($shopProduct->save()) {
            if ($nett_price) {
                $shopProductSku = ShopProductSku::model()->find('product_id=:product_id and flag=:flag and name=:name', array(
                    ':flag' => FLAG_NO,
                    ':product_id' => $shopProduct->id,
                    ':name' => '成人票',
                ));
                if (empty($shopProductSku)) {
                    $shopProductSku = new ShopProductSku();
                }
                $shopProductSku->product_id = $shopProduct->id;
                $shopProductSku->num = $amount;
                $shopProductSku->original_price = $original_price;
                $shopProductSku->name = '成人票';
                $shopProductSku->price = $nett_price;
                if (empty($shopProductSku->create_time)) {
                    $shopProductSku->create_time = date('Y-m-d H:i:s');
                }
                $shopProductSku->save();
            }
            if (!empty($nett_price2)) {
                $shopProductSku = ShopProductSku::model()->find('product_id=:product_id and flag=:flag and name=:name', array(
                    ':flag' => FLAG_NO,
                    ':product_id' => $shopProduct->id,
                    ':name' => '儿童票',
                ));
                if (empty($shopProductSku)) {
                    $shopProductSku = new ShopProductSku();
                }
                $shopProductSku->product_id = $shopProduct->id;
                $shopProductSku->num = $amount;
                $shopProductSku->original_price = $original_price;
                $shopProductSku->name = '儿童票';
                $shopProductSku->price = $nett_price2;
                if (empty($shopProductSku->create_time)) {
                    $shopProductSku->create_time = date('Y-m-d H:i:s');
                }
                $shopProductSku->save();
            }
        }
        $result['status'] = ERROR_NONE;
        return json_encode($result);
    }

    /**
     * 添加分组
     */
    public function addGroup($merchant_id)
    {
        $result = array();
        $group = ShopGroup::model()->find('merchant_id=:merchant_id and flag=:flag and name=:name', array(
            ':merchant_id' => $merchant_id,
            ':flag' => FLAG_NO,
            ':name' => '未分组',
        ));
        if (empty($group)) {
            $group = new ShopGroup();
        }
        $group->merchant_id = $merchant_id;
        $group->name = '未分组';
        if (empty($group->create_time)) {
            $group->create_time = date('Y-m-d H:i:s');
        }
        if (empty($group->flag)) {
            $group->flag = FLAG_NO;
        }
        if ($group->save()) {
            $result['id'] = $group->id;
            $result['status'] = ERROR_NONE;
        }
        return json_encode($result);
    }

    /**
     * 商品该分组获取商品分组
     */
    public function getGroup($merchant_id)
    {
        $result = array();
        $list = array();
        try {
            if (empty($merchant_id)) {
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id and flag = :flag');
            $criteria->params = array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO);
            //显示分页
            $pages = new CPagination(ShopGroup::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $model = ShopGroup::model()->findAll($criteria);
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $list[$value['id']] = $value['name'];
                }
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $list;
            $result['errMsg'] = "";
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商品改分组
     */
    public function ChangeGroup($merchant_id, $group_id, $shop_id)
    {
        $result = array();
        try {
            if (empty($merchant_id)) {
                throw new Exception('参数merchant_id不能为空');
            }
            $model = ShopProduct::model()->findByPk($shop_id);
            $model->group_id = $group_id;
            if ($model->update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '保存失败';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
}