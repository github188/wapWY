<?php
include_once(dirname(__FILE__).'/../mainClass.php');

/**
 * 数据统计类
 */
class StatisticsC extends mainClass
{
    /**
     * 统计昨天数据
     */
    public function Flow($today)
    {
        //返回结果
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        $criteria9 = new CDbCriteria();
        $criteria9->addCondition('flag=:flag and status=:status');
        $criteria9->params[':flag'] = FLAG_NO;
        $criteria9->params[':status'] = MERCHANT_STATUS_NORMAL;
        $criteria9->addCondition("gj_end_time >= '%$today%'");
        $merchant = Merchant::model()->findall($criteria9);
        foreach ($merchant as $key => $value) {
            if (date('Y-m-d', strtotime($value['gj_end_time'])) >= $today)//判断是否到期
            {
                //昨天累计会员
                $criteria = new CDbCriteria();
                $criteria->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria->params[':merchant_id'] = $value['id'];
                $criteria->params[':flag'] = FLAG_NO;
                $usernum = User::model()->count($criteria);

                //昨天新增会员
                $criteria1 = new CDbCriteria();
                $criteria1->addSearchCondition('regist_time', $today);
                $criteria1->addCondition('merchant_id=:merchant_id AND flag=:flag');
                $criteria1->params[':merchant_id'] = $value['id'];
                $criteria1->params[':flag'] = FLAG_NO;
                $todayusernum = User::model()->count($criteria1);

                //pv计算
                $criteria7 = new CDbCriteria();
                $criteria7->addSearchCondition('visit_date', $today);
                $criteria7->addCondition('merchant_id=:merchant_id');
                $criteria7->params[':merchant_id'] = $value['id'];
                $pv = Pv::model()->count($criteria7);

                //查找门店
                //按商户id查找 ， 记录状态为FLAG_NO
                $criteria2 = new CDbCriteria();
                $criteria2->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria2->params[':merchant_id'] = $value['id'];
                $criteria2->params[':flag'] = FLAG_NO;
                $store = Store::model()->findall($criteria2);
                $ordernum = 0;
                $ordermoney = 0;
                $storedmoney = 0;
                $storednum = 0;
                if ($store) {
                    foreach ($store as $k => $v) {
                        //门店id查找 ，记录状态为FLAG_NO， 支付状态为已支付 ， 支付时间为$today
                        $criteria3 = new CDbCriteria();
                        $criteria3->addCondition('store_id=:store_id and flag=:flag and pay_status=:pay_status');
                        $criteria3->params[':store_id'] = $v['id'];
                        $criteria3->params[':flag'] = FLAG_NO;
                        $criteria3->addSearchCondition('pay_time', $today);
                        $criteria3->params[':pay_status'] = ORDER_STATUS_PAID;
                        //新增订单//新增订单金额
                        $order = Order::model()->findall($criteria3);
                        if ($order) {
                            //订单金额包括(储值支付金额,线上支付金额,银联刷卡支付,现金支付)
                            foreach ($order as $n => $m) {
                                $ordernum = $ordernum + 1;
                                $ordermoney = $ordermoney + $m['order_paymoney'] + $m['cash_paymoney'] + $m['stored_paymoney'] + $m['unionpay_paymoney'];
                            }
                        }

                        //储值 //按门店查找， 订单状态正常 ，订单付款状态为已付款 ，记录状态为FLAG_NO ，支付时间为$today
                        $criteria8 = new CDbCriteria();
                        $criteria8->addCondition('store_id=:store_id');
                        $criteria8->params[':store_id'] = $v['id'];
                        $criteria8->addSearchCondition('pay_time', $today);
                        $criteria8->addCondition('flag=:flag');
                        $criteria8->params[':flag'] = FLAG_NO;
                        $criteria8->addCondition('pay_status=:pay_status');
                        $criteria8->params[':pay_status'] = ORDER_STATUS_PAID;
                        $criteria8->addCondition('order_status=:order_status');
                        $criteria8->params[':order_status'] = ORDER_STATUS_NORMAL;
                        $storedorder = StoredOrder::model()->findall($criteria8);
                        if ($storedorder) {
                            //储值数量//储值金额
                            foreach ($storedorder as $g => $h) {
                                $storednum = $storednum + $h['num'];
                                //按储值活动id查找 ， 记录状态为FLAG_NO
                                $stored = Stored::model()->find('id=:id and flag=:flag', array(':id' => $h['stored_id'], ':flag' => FLAG_NO));
                                if ($stored) {
                                    $storedmoney = $storedmoney + $stored->stored_money * $h['num'];
                                }
                            }
                        }
                    }
                }

                $hongbaonum = 0;
                $hongbaouse = 0;
                $couponsnum = 0;
                $couponsuse = 0;
                //按商户id查找， 记录状态为FLAG_NO
                $criteria4 = new CDbCriteria();
                $criteria4->addCondition('merchant_id=:merchant_id and flag=:flag');
                $criteria4->params[':merchant_id'] = $value['id'];
                $criteria4->params['flag'] = FLAG_NO;
                $coupons = Coupons::model()->findall($criteria4);
                if ($coupons) {
                    foreach ($coupons as $i => $j) {
                        //红包使用量//红包领取量
                        if ($j['type'] == COUPON_TYPE_REDENVELOPE) {
                            //优惠券id查找  记录状态为FLAG_NO 添加时间为$today
                            $criteria5 = new CDbCriteria();
                            $criteria5->addCondition('coupons_id=:coupons_id and flag=:flag');
                            $criteria5->params[':coupons_id'] = $j['id'];
                            $criteria5->params[':flag'] = FLAG_NO;
                            $criteria5->addSearchCondition('create_time', $today);
                            $redenvelope = UserCoupons::model()->findall($criteria5);
                            if ($redenvelope) {
                                foreach ($redenvelope as $e => $a) {
                                    if (!empty($a['status'])) {
                                        //未使用红包
                                        if ($a['status'] == COUPONS_USE_STATUS_UNUSE) {
                                            $hongbaonum = $hongbaonum + 1;
                                        }
                                        //已使用红包
                                        if ($a['status'] == COUPONS_USE_STATUS_USED) {
                                            $hongbaouse = $hongbaouse + 1;
                                        }
                                    }
                                }
                            }
                        }

                        //优惠券使用量//优惠券领取量
                        //优惠券id查找  记录状态为FLAG_NO 添加时间为$today
                        $criteria6 = new CDbCriteria();
                        $criteria6->addCondition('coupons_id=:coupons_id and flag=:flag');
                        $criteria6->params[':coupons_id'] = $j['id'];
                        $criteria6->params[':flag'] = FLAG_NO;
                        $criteria6->addSearchCondition('create_time', $today);
                        $conpous = UserCoupons::model()->findall($criteria6);

                        if (!empty($coupons)) {
                            foreach ($conpous as $b => $c) {
                                if (!empty($c['status'])) {
                                    //优惠券未使用
                                    if ($c['status'] == COUPONS_USE_STATUS_UNUSE) {
                                        $couponsnum = $couponsnum + 1;
                                    }
                                    //优惠券已使用
                                    if ($c['status'] == COUPONS_USE_STATUS_USED) {
                                        $couponsuse = $couponsuse + 1;
                                    }
                                }
                            }
                        }
                    }


                    //查找当天是否存在，如果不存在，则新建
                    $data = DataStatistics::model()->find('date=:date and merchant_id=:merchant_id', array(':date' => $today, ':merchant_id' => $value['id']));
                    if (empty($data)) {
                        $data = new DataStatistics();
                    }
                    $data->new_user_num = $todayusernum;
                    $data->merchant_id = $value['id'];
                    $data->user_num = $usernum;
                    $data->new_order_num = $ordernum;
                    $data->new_order_money = $ordermoney;
                    if ($ordermoney == 0 || $ordernum == 0) {
                        $avg = 0;
                    } else {
                        $avg = $ordermoney / $ordernum;
                    }
                    $data->pct = $avg;
                    $data->pv = $pv;
                    $data->day_hongbao_num = $hongbaonum;
                    $data->day_hongbao_use = $hongbaouse;
                    $data->day_coupons_num = $couponsnum;
                    $data->day_coupons_use = $couponsuse;
                    $data->day_stored_money = $storedmoney;
                    $data->day_stored_num = $storednum;
                    $data->date = $today;
                    if ($data->save()) {
                        $result['status'] = ERROR_NONE;
                        $result['errMsg'] = '统计成功';
                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        $result['errMsg'] = '统计失败';
                    }
                }
            }
        }
        return json_encode($result);
    }

    //会员数据统计
    /**
     * $merchantId   商户id
     */
    public function DataStatistics($merchantId, $day = '', $start = '', $end = '')
    {
        //返回结果
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            if (!empty($day)) {
                switch ($day) {
                    case '-7day':
                        $start = date('Y-m-d', strtotime('-7 day'));
                        $end = date('Y-m-d');
                        break;
                    case '-30day':
                        $start = date('Y-m-d', strtotime('-1 months'));
                        $end = date('Y-m-d');
                        break;
                }

            }
            if (!empty($start) && !empty($end)) {
                $criteria->addBetweenCondition('date', $start, $end);
            }
            $criteria->addCondition('merchant_id=:merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $criteria->order = 'date asc';
            $datastatistics = DataStatistics::model()->findall($criteria);
            if ($datastatistics) {
                $data = array();
                foreach ($datastatistics as $key => $value) {
                    $data[$key]['new_user_num'] = $value['new_user_num'];
                    $data[$key]['user_num'] = $value['user_num'];
                    $data[$key]['new_order_num'] = $value['new_order_num'];
                    $data[$key]['new_order_money'] = $value['new_order_money'];
                    $data[$key]['pct'] = $value['pct'];
                    $data[$key]['pv'] = $value['pv'];
                    $data[$key]['day_hongbao_num'] = $value['day_hongbao_num'];
                    $data[$key]['day_hongbao_use'] = $value['day_hongbao_use'];
                    $data[$key]['day_coupons_num'] = $value['day_coupons_num'];
                    $data[$key]['day_coupons_use'] = $value['day_coupons_use'];
                    $data[$key]['day_stored_money'] = $value['day_stored_money'];
                    $data[$key]['day_stored_num'] = $value['day_stored_num'];
                    $data[$key]['date'] = date('m-d', strtotime($value['date']));
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    //统计性别
    /**
     * $merchantId   商户id
     */
    public function Sex($merchantId)
    {
        //返回结果
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $user = User::model()->findall($criteria);
            if ($user) {
                $male = 0;
                $female = 0;
                $unsex = 0;
                $ybman = 0;
                $esman = 0;
                $ejman = 0;
                $sjman = 0;
                $xjman = 0;
                $wjman = 0;
                $lsman = 0;
                $unman = 0;
                $ybwoman = 0;
                $eswoman = 0;
                $ejwoman = 0;
                $sjwoman = 0;
                $xjwoman = 0;
                $wjwoman = 0;
                $lswoman = 0;
                $unwoman = 0;
                $ybnoman = 0;
                $esnoman = 0;
                $ejnoman = 0;
                $sjnoman = 0;
                $xjnoman = 0;
                $wjnoman = 0;
                $lsnoman = 0;
                $unnoman = 0;
                foreach ($user as $key => $value) {
                    $birthday = explode('-', $value['birthday']);
                    if ($value['sex'] == SEX_MALE) {
                        $male = $male + 1;
                        if ($birthday[0] > '1998' && $birthday[0] != '') {
                            $ybman = $ybman + 1;
                        }
                        if ($birthday[0] <= '1998' && $birthday[0] > '1991') {
                            $esman = $esman + 1;
                        }
                        if ($birthday[0] <= '1991' && $birthday[0] > '1987') {
                            $ejman = $ejman + 1;
                        }
                        if ($birthday[0] <= '1987' && $birthday[0] > '1977') {
                            $sjman = $sjman + 1;
                        }
                        if ($birthday[0] <= '1977' && $birthday[0] > '1967') {
                            $xjman = $xjman + 1;
                        }
                        if ($birthday[0] <= '1967' && $birthday[0] > '1957') {
                            $wjman = $wjman + 1;
                        }
                        if ($birthday[0] <= '1956' && $birthday[0] != '') {
                            $lsman = $lsman + 1;
                        }
                        if ($birthday[0] == '') {
                            $unman = $unman + 1;
                        }
                    }
                    if ($value['sex'] == SEX_FEMALE) {
                        $female = $female + 1;
                        if ($birthday[0] > '1998' && $birthday[0] != '') {
                            $ybwoman = $ybwoman + 1;
                        }
                        if ($birthday[0] <= '1998' && $birthday[0] > '1991') {
                            $eswoman = $eswoman + 1;
                        }
                        if ($birthday[0] <= '1991' && $birthday[0] > '1987') {
                            $ejwoman = $ejwoman + 1;
                        }
                        if ($birthday[0] <= '1987' && $birthday[0] > '1977') {
                            $sjwoman = $sjwoman + 1;
                        }
                        if ($birthday[0] <= '1977' && $birthday[0] > '1967') {
                            $xjwoman = $xjwoman + 1;
                        }
                        if ($birthday[0] <= '1967' && $birthday[0] > '1957') {
                            $wjwoman = $wjwoman + 1;
                        }
                        if ($birthday[0] <= '1956' && $birthday[0] != '') {
                            $lswoman = $lswoman + 1;
                        }
                        if ($birthday[0] == '') {
                            $unwoman = $unwoman + 1;
                        }
                    }
                    if ($value['sex'] == '') {
                        $unsex = $unsex + 1;
                        if ($birthday[0] < '1998' && $birthday[0] != '') {
                            $ybnoman = $ybnoman + 1;
                        }
                        if ($birthday[0] <= '1998' && $birthday[0] > '1991') {
                            $esnoman = $esnoman + 1;
                        }
                        if ($birthday[0] <= '1991' && $birthday[0] > '1987') {
                            $ejnoman = $ejnoman + 1;
                        }
                        if ($birthday[0] <= '1987' && $birthday[0] > '1977') {
                            $sjnoman = $sjnoman + 1;
                        }
                        if ($birthday[0] <= '1977' && $birthday[0] > '1967') {
                            $xjnoman = $xjnoman + 1;
                        }
                        if ($birthday[0] <= '1967' && $birthday[0] > '1957') {
                            $wjnoman = $wjnoman + 1;
                        }
                        if ($birthday[0] <= '1956' && $birthday[0] != '') {
                            $lsnoman = $lsnoman + 1;
                        }
                        if ($birthday[0] == '') {
                            $unnoman = $unnoman + 1;
                        }
                    }
                }
                $data['ybman'] = $ybman;
                $data['esman'] = $esman;
                $data['ejman'] = $ejman;
                $data['sjman'] = $sjman;
                $data['xjman'] = $xjman;
                $data['wjman'] = $wjman;
                $data['lsman'] = $lsman;
                $data['unman'] = $unman;
                $data['ybwoman'] = $ybwoman;
                $data['eswoman'] = $eswoman;
                $data['ejwoman'] = $ejwoman;
                $data['sjwoman'] = $sjwoman;
                $data['xjwoman'] = $xjwoman;
                $data['wjwoman'] = $wjwoman;
                $data['lswoman'] = $lswoman;
                $data['unwoman'] = $unwoman;
                $data['ybnoman'] = $ybnoman;
                $data['esnoman'] = $esnoman;
                $data['ejnoman'] = $ejnoman;
                $data['sjnoman'] = $sjnoman;
                $data['xjnoman'] = $xjnoman;
                $data['wjnoman'] = $wjnoman;
                $data['lsnoman'] = $lsnoman;
                $data['unnoman'] = $unnoman;
                $data['male'] = $male;
                $data['female'] = $female;
                $data['unsex'] = $unsex;
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    //来源
    /**
     * $merchantId   商户id
     */
    public function From($merchantId)
    {
        //返回结果
        $result = array('status' => 1, 'errMsg' => 'null', 'data' => 'null');
        $flag = 0;
        if (isset($merchantId) && empty($merchantId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;
        }
        if ($flag == 0) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id=:merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            $user = User::model()->findall($criteria);
            if ($user) {
                $wechat = 0;
                $alipay = 0;
                $wap = 0;
                $other = 0;
                $ar = array();
                $from = array();
                foreach ($user as $key => $value) {
                    $from = explode(',', $value['from']);
                    $count = count($from);
                    for ($i = 0; $i < $count; $i++) {
                        if ($from[$i] == USER_FROM_WECHAT) {
                            $wechat = $wechat + 1;
                        }
                        if ($from[$i] == USER_FROM_ALIPAY) {
                            $alipay = $alipay + 1;
                        }
                        if ($from[$i] == USER_FROM_WAP) {
                            $wap = $wap + 1;
                        }
                        if ($from[$i] == USER_FROM_OTHER) {
                            $other = $other + 1;
                        }
                    }

                    $address = explode(',', $value['register_address']);
                    $ar[$key] = isset($address[1]) ? $address[1] : '';
                }
                $ar = array_unique($ar);//去除相同的
                $ar = array_values($ar);//重组
                $count = count($ar);
                $addrsum = array();
                for ($i = 0; $i < $count; $i++) {
                    if ($ar[$i]) {
                        $criteria1 = new CDbCriteria();
                        $criteria1->addSearchCondition('register_address', $ar[$i]);
                        $criteria1->addCondition('merchant_id=:merchant_id');
                        $criteria1->params[':merchant_id'] = $merchantId;
                        $addrsum[$i] = User::model()->count($criteria1);
                    }
                }
                $data['count'] = $count;
                $data['ar'] = $ar;
                $data['addrsum'] = $addrsum;
                $data['wechat'] = $wechat;
                $data['alipay'] = $alipay;
                $data['wap'] = $wap;
                $data['other'] = $other;
                $result['status'] = ERROR_NONE;
                $result['data'] = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }

    /**
     * 统计日汇总
     */
    public function summaryDay()
    {
        $result = array();
        try {
            $time = date('Y-m-d', strtotime('-1 day')); //前一天
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $criteria->addCondition('pay_time >= :pay_time1');
            $criteria->params[':pay_time1'] = $time . ' 00:00:00';

            $criteria->addCondition('pay_time <= :pay_time2');
            $criteria->params[':pay_time2'] = $time . ' 23:59:59';

            $list = Order::model()->findAll($criteria);//var_dump($list);exit;

            foreach ($list as $k => $v) {
                $order_id = $v['id']; //订单id
                $order_no = $v['order_no']; //订单编号
                $operator_id = $v['operator_id']; //操作员
                $order_type = $v['order_type']; //订单类型
                $pay_status = $v['pay_status']; //支付状态
                $order_status = $v['order_status']; //订单状态
                $pay_channel = $v['pay_channel']; //支付方式
                $order_paymoney = $v['order_paymoney']; //订单总金额
                $online_paymoney = $v['online_paymoney']; //线上支付金额
                $unionpay_paymoney = $v['unionpay_paymoney']; //银联支付金额
                $cash_paymoney = $v['cash_paymoney']; //现金支付金额
                $stored_paymoney = $v['stored_paymoney']; //储值支付的金额
                $store_id = $v['store_id'];//门店id


                //筛选必要订单
                if ($pay_status != ORDER_STATUS_PAID) {
                    continue; //未支付的订单不计入汇总
                }
                if ($order_type != ORDER_TYPE_CASHIER) {
                    continue; //非收款订单不计入汇总
                }
                //计算实收金额和退款金额
                $order_detail = $this->getReceiptAmount($order_no, TRUE);
                $receipt_money = $order_detail['receipt_money']; //实收金额

                switch ($pay_channel) {
                    case ORDER_PAY_CHANNEL_ALIPAY_SM:
                        $pay_channel_prefix = 'alipay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_ALIPAY_TM:
                        $pay_channel_prefix = 'alipay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_SM:
                        $pay_channel_prefix = 'wxpay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_TM:
                        $pay_channel_prefix = 'wxpay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_CASH:
                        $pay_channel_prefix = 'cashpay';
                        break;
                    case ORDER_PAY_CHANNEL_UNIONPAY:
                        $pay_channel_prefix = 'unionpay';
                        break;
                    case ORDER_PAY_CHANNEL_STORED:
                        $pay_channel_prefix = 'storedpay';
                        break;
                    default:
                        $pay_channel_prefix = '';
                        break;
                }

                //不符合指定支付方式的不计入汇总
                if (empty($pay_channel_prefix)) {
                    continue;
                }
                if (empty($result['data'][$store_id])) {
                    $result['data'][$store_id] = array(
                        'total_trade_money' => 0, //交易金额
                        'total_receipt_money' => 0, //实收金额
                        'total_trade_count' => 0, //交易笔数
                        'alipay_qrcode_trade_money' => 0, //支付宝扫码交易金额
                        'alipay_qrcode_receipt_money' => 0, //支付宝扫码实收金额
                        'alipay_qrcode_trade_count' => 0, //支付宝扫码交易笔数
                        'alipay_barcode_trade_money' => 0, //支付宝条码交易金额
                        'alipay_barcode_receipt_money' => 0, //支付宝条码实收金额
                        'alipay_barcode_trade_count' => 0, //支付宝条码交易笔数
                        'wxpay_qrcode_trade_money' => 0, //微信扫码交易金额
                        'wxpay_qrcode_receipt_money' => 0, //微信扫码实收金额
                        'wxpay_qrcode_trade_count' => 0, //微信扫码交易笔数
                        'wxpay_barcode_trade_money' => 0, //微信条码交易金额
                        'wxpay_barcode_receipt_money' => 0, //微信条码实收金额
                        'wxpay_barcode_trade_count' => 0, //微信条码交易笔数
                        'cashpay_trade_money' => 0, //现金支付交易金额
                        'cashpay_receipt_money' => 0, //现金支付实收金额
                        'cashpay_trade_count' => 0, //现金支付交易笔数
                        'unionpay_trade_money' => 0, //银联支付交易金额
                        'unionpay_receipt_money' => 0, //银联支付实收金额
                        'unionpay_trade_count' => 0, //银联支付交易笔数
                        'storedpay_trade_money' => 0, //储值支付交易金额
                        'storedpay_receipt_money' => 0, //储值支付实收金额
                        'storedpay_trade_count' => 0, //储值支付交易笔数
                    );
                }

                $result['data'][$store_id][$pay_channel_prefix . '_trade_money'] += $order_paymoney;
                $result['data'][$store_id][$pay_channel_prefix . '_receipt_money'] += $receipt_money;
                $result['data'][$store_id][$pay_channel_prefix . '_trade_count'] += 1;

                $result['data'][$store_id]['total_trade_money'] += $order_paymoney;
                $result['data'][$store_id]['total_receipt_money'] += $receipt_money;
                $result['data'][$store_id]['total_trade_count'] += 1;
            }

            //日汇总入库
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();

            $model = ReportFormDay::model()->findAll("date_format(date,'%Y-%m-%d')=:date", array(':date' => $time));
            if (count($model) <= 0) {
                if (!empty($result['data'])) {
                    foreach ($result['data'] as $key => $val) {
                        $reportFormDay = new ReportFormDay();
                        $reportFormDay->store_id = $key;
                        $reportFormDay->total_money = $val['total_receipt_money']; //总金额
                        $reportFormDay->total_num = $val['total_trade_count']; //总笔数
                        $reportFormDay->wechat_money = $val['wxpay_qrcode_receipt_money'] + $val['wxpay_barcode_receipt_money']; //微信金额
                        $reportFormDay->wechat_num = $val['wxpay_qrcode_trade_count'] + $val['wxpay_barcode_trade_count']; //微信笔数
                        $reportFormDay->alipay_money = $val['alipay_qrcode_receipt_money'] + $val['alipay_barcode_receipt_money']; //支付宝金额
                        $reportFormDay->alipay_num = $val['alipay_barcode_trade_count'] + $val['alipay_qrcode_trade_count']; //支付宝笔数
                        $reportFormDay->cash_money = $val['cashpay_receipt_money']; //现金金额
                        $reportFormDay->cash_num = $val['cashpay_trade_count']; //现金笔数
                        $reportFormDay->stored_money = $val['storedpay_receipt_money']; //储值金额
                        $reportFormDay->stored_num = $val['storedpay_trade_count']; //储值笔数
                        $reportFormDay->unionpay_money = $val['unionpay_receipt_money']; //银联金额
                        $reportFormDay->unionpay_num = $val['unionpay_trade_count']; //银联笔数
                        $reportFormDay->date = $time;
                        $reportFormDay->create_time = date('Y-m-d H:i:s');
                        if ($reportFormDay->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = '执行成功'; //错误信息
                        } else {
                            throw new Exception('数据保存失败');
                        }
                    }
                } else {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = '昨日无数据'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = '重复执行'; //错误信息
            }
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 统计月汇总
     */
    public function summaryMonth()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1500M');
        $result = array();
        try {
            $time = date('Y-m', strtotime('-1 month')); //前一月
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $criteria->addCondition("date_format(pay_time,'%Y-%m')= :pay_time");
            $criteria->params[':pay_time'] = $time;

            $list = Order::model()->findAll($criteria);

            foreach ($list as $k => $v) {
                $order_id = $v['id']; //订单id
                $order_no = $v['order_no']; //订单编号
                $operator_id = $v['operator_id']; //操作员
                $order_type = $v['order_type']; //订单类型
                $pay_status = $v['pay_status']; //支付状态
                $order_status = $v['order_status']; //订单状态
                $pay_channel = $v['pay_channel']; //支付方式
                $order_paymoney = $v['order_paymoney']; //订单总金额
                $online_paymoney = $v['online_paymoney']; //线上支付金额
                $unionpay_paymoney = $v['unionpay_paymoney']; //银联支付金额
                $cash_paymoney = $v['cash_paymoney']; //现金支付金额
                $stored_paymoney = $v['stored_paymoney']; //储值支付的金额
                $store_id = $v['store_id'];//门店id


                //筛选必要订单
                if ($pay_status != ORDER_STATUS_PAID) {
                    continue; //未支付的订单不计入汇总
                }
                if ($order_type != ORDER_TYPE_CASHIER) {
                    continue; //非收款订单不计入汇总
                }
                //计算实收金额和退款金额
                $order_detail = $this->getReceiptAmount($order_no, TRUE);
                $receipt_money = $order_detail['receipt_money']; //实收金额

                switch ($pay_channel) {
                    case ORDER_PAY_CHANNEL_ALIPAY_SM:
                        $pay_channel_prefix = 'alipay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_ALIPAY_TM:
                        $pay_channel_prefix = 'alipay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_SM:
                        $pay_channel_prefix = 'wxpay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_TM:
                        $pay_channel_prefix = 'wxpay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_CASH:
                        $pay_channel_prefix = 'cashpay';
                        break;
                    case ORDER_PAY_CHANNEL_UNIONPAY:
                        $pay_channel_prefix = 'unionpay';
                        break;
                    case ORDER_PAY_CHANNEL_STORED:
                        $pay_channel_prefix = 'storedpay';
                        break;
                    default:
                        $pay_channel_prefix = '';
                        break;
                }

                //不符合指定支付方式的不计入汇总
                if (empty($pay_channel_prefix)) {
                    continue;
                }
                if (empty($result['data'][$store_id])) {
                    $result['data'][$store_id] = array(
                        'total_trade_money' => 0, //交易金额
                        'total_receipt_money' => 0, //实收金额
                        'total_trade_count' => 0, //交易笔数
                        'alipay_qrcode_trade_money' => 0, //支付宝扫码交易金额
                        'alipay_qrcode_receipt_money' => 0, //支付宝扫码实收金额
                        'alipay_qrcode_trade_count' => 0, //支付宝扫码交易笔数
                        'alipay_barcode_trade_money' => 0, //支付宝条码交易金额
                        'alipay_barcode_receipt_money' => 0, //支付宝条码实收金额
                        'alipay_barcode_trade_count' => 0, //支付宝条码交易笔数
                        'wxpay_qrcode_trade_money' => 0, //微信扫码交易金额
                        'wxpay_qrcode_receipt_money' => 0, //微信扫码实收金额
                        'wxpay_qrcode_trade_count' => 0, //微信扫码交易笔数
                        'wxpay_barcode_trade_money' => 0, //微信条码交易金额
                        'wxpay_barcode_receipt_money' => 0, //微信条码实收金额
                        'wxpay_barcode_trade_count' => 0, //微信条码交易笔数
                        'cashpay_trade_money' => 0, //现金支付交易金额
                        'cashpay_receipt_money' => 0, //现金支付实收金额
                        'cashpay_trade_count' => 0, //现金支付交易笔数
                        'unionpay_trade_money' => 0, //银联支付交易金额
                        'unionpay_receipt_money' => 0, //银联支付实收金额
                        'unionpay_trade_count' => 0, //银联支付交易笔数
                        'storedpay_trade_money' => 0, //储值支付交易金额
                        'storedpay_receipt_money' => 0, //储值支付实收金额
                        'storedpay_trade_count' => 0, //储值支付交易笔数
                    );
                }

                $result['data'][$store_id][$pay_channel_prefix . '_trade_money'] += $order_paymoney;
                $result['data'][$store_id][$pay_channel_prefix . '_receipt_money'] += $receipt_money;
                $result['data'][$store_id][$pay_channel_prefix . '_trade_count'] += 1;

                $result['data'][$store_id]['total_trade_money'] += $order_paymoney;
                $result['data'][$store_id]['total_receipt_money'] += $receipt_money;
                $result['data'][$store_id]['total_trade_count'] += 1;
            }

            //月汇总入库
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();

            $model = ReportFormMonth::model()->findAll('month=:month', array(':month' => $time));
            if (count($model) <= 0) {

                if (!empty($result['data'])) {
                    foreach ($result['data'] as $key => $val) {
                        $reportFormMonth = new ReportFormMonth();
                        $reportFormMonth->store_id = $key;
                        $reportFormMonth->total_money = $val['total_receipt_money']; //总金额
                        $reportFormMonth->total_num = $val['total_trade_count']; //总笔数
                        $reportFormMonth->wechat_money = $val['wxpay_qrcode_receipt_money'] + $val['wxpay_barcode_receipt_money']; //微信金额
                        $reportFormMonth->wechat_num = $val['wxpay_qrcode_trade_count'] + $val['wxpay_barcode_trade_count']; //微信笔数
                        $reportFormMonth->alipay_money = $val['alipay_qrcode_receipt_money'] + $val['alipay_barcode_receipt_money']; //支付宝金额
                        $reportFormMonth->alipay_num = $val['alipay_barcode_trade_count'] + $val['alipay_qrcode_trade_count']; //支付宝笔数
                        $reportFormMonth->cash_money = $val['cashpay_receipt_money']; //现金金额
                        $reportFormMonth->cash_num = $val['cashpay_trade_count']; //现金笔数
                        $reportFormMonth->stored_money = $val['storedpay_receipt_money']; //储值金额
                        $reportFormMonth->stored_num = $val['storedpay_trade_count']; //储值笔数
                        $reportFormMonth->unionpay_money = $val['unionpay_receipt_money']; //银联金额
                        $reportFormMonth->unionpay_num = $val['unionpay_trade_count']; //银联笔数
                        $reportFormMonth->month = $time;
                        $reportFormMonth->create_time = date('Y-m-d H:i:s');
                        if ($reportFormMonth->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = '执行成功'; //错误信息
                        } else {
                            throw new Exception('数据保存失败');
                        }
                    }
                } else {
                    $result['status'] = ERROR_NONE; //状态码
                    $result['errMsg'] = '上月无数据'; //错误信息
                }
            } else {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = '重复执行'; //错误信息
            }
            $transaction->commit();


        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);

    }

    /**
     * 统计所有日汇总
     */
    public function summaryAllDay()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1500M');
        $result = array();
        try {
            $date = date('Y-m-d', strtotime('-1 day')); //前一天
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $criteria->addCondition("date_format(pay_time,'%Y-%m-%d') <= :pay_time");
            $criteria->params[':pay_time'] = $date;
            $list = Order::model()->findAll($criteria);

            foreach ($list as $k => $v) {
                $order_id = $v['id']; //订单id
                $order_no = $v['order_no']; //订单编号
                $operator_id = $v['operator_id']; //操作员
                $order_type = $v['order_type']; //订单类型
                $pay_status = $v['pay_status']; //支付状态
                $order_status = $v['order_status']; //订单状态
                $pay_channel = $v['pay_channel']; //支付方式
                $order_paymoney = $v['order_paymoney']; //订单总金额
                $online_paymoney = $v['online_paymoney']; //线上支付金额
                $unionpay_paymoney = $v['unionpay_paymoney']; //银联支付金额
                $cash_paymoney = $v['cash_paymoney']; //现金支付金额
                $stored_paymoney = $v['stored_paymoney']; //储值支付的金额
                $store_id = $v['store_id'];//门店id
                $time = date('Y-m-d', strtotime($v['pay_time']));


                //筛选必要订单
                if ($pay_status != ORDER_STATUS_PAID) {
                    continue; //未支付的订单不计入汇总
                }
                if ($order_type != ORDER_TYPE_CASHIER) {
                    continue; //非收款订单不计入汇总
                }
                //计算实收金额和退款金额
                $order_detail = $this->getReceiptAmount($order_no, TRUE);
                $receipt_money = $order_detail['receipt_money']; //实收金额

                switch ($pay_channel) {
                    case ORDER_PAY_CHANNEL_ALIPAY_SM:
                        $pay_channel_prefix = 'alipay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_ALIPAY_TM:
                        $pay_channel_prefix = 'alipay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_SM:
                        $pay_channel_prefix = 'wxpay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_TM:
                        $pay_channel_prefix = 'wxpay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_CASH:
                        $pay_channel_prefix = 'cashpay';
                        break;
                    case ORDER_PAY_CHANNEL_UNIONPAY:
                        $pay_channel_prefix = 'unionpay';
                        break;
                    case ORDER_PAY_CHANNEL_STORED:
                        $pay_channel_prefix = 'storedpay';
                        break;
                    default:
                        $pay_channel_prefix = '';
                        break;
                }

                //不符合指定支付方式的不计入汇总
                if (empty($pay_channel_prefix)) {
                    continue;
                }
                if (empty($result['data'][$time][$store_id])) {
                    $result['data'][$time][$store_id] = array(
                        'total_trade_money' => 0, //交易金额
                        'total_receipt_money' => 0, //实收金额
                        'total_trade_count' => 0, //交易笔数
                        'alipay_qrcode_trade_money' => 0, //支付宝扫码交易金额
                        'alipay_qrcode_receipt_money' => 0, //支付宝扫码实收金额
                        'alipay_qrcode_trade_count' => 0, //支付宝扫码交易笔数
                        'alipay_barcode_trade_money' => 0, //支付宝条码交易金额
                        'alipay_barcode_receipt_money' => 0, //支付宝条码实收金额
                        'alipay_barcode_trade_count' => 0, //支付宝条码交易笔数
                        'wxpay_qrcode_trade_money' => 0, //微信扫码交易金额
                        'wxpay_qrcode_receipt_money' => 0, //微信扫码实收金额
                        'wxpay_qrcode_trade_count' => 0, //微信扫码交易笔数
                        'wxpay_barcode_trade_money' => 0, //微信条码交易金额
                        'wxpay_barcode_receipt_money' => 0, //微信条码实收金额
                        'wxpay_barcode_trade_count' => 0, //微信条码交易笔数
                        'cashpay_trade_money' => 0, //现金支付交易金额
                        'cashpay_receipt_money' => 0, //现金支付实收金额
                        'cashpay_trade_count' => 0, //现金支付交易笔数
                        'unionpay_trade_money' => 0, //银联支付交易金额
                        'unionpay_receipt_money' => 0, //银联支付实收金额
                        'unionpay_trade_count' => 0, //银联支付交易笔数
                        'storedpay_trade_money' => 0, //储值支付交易金额
                        'storedpay_receipt_money' => 0, //储值支付实收金额
                        'storedpay_trade_count' => 0, //储值支付交易笔数
                    );
                }

                $result['data'][$time][$store_id][$pay_channel_prefix . '_trade_money'] += $order_paymoney;
                $result['data'][$time][$store_id][$pay_channel_prefix . '_receipt_money'] += $receipt_money;
                $result['data'][$time][$store_id][$pay_channel_prefix . '_trade_count'] += 1;

                $result['data'][$time][$store_id]['total_trade_money'] += $order_paymoney;
                $result['data'][$time][$store_id]['total_receipt_money'] += $receipt_money;
                $result['data'][$time][$store_id]['total_trade_count'] += 1;
            }

            //日汇总入库
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();

            if (!empty($result['data'])) {
                foreach ($result['data'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        $reportFormDay = new ReportFormDay();
                        $reportFormDay->store_id = $k;
                        $reportFormDay->total_money = $v['total_receipt_money']; //总金额
                        $reportFormDay->total_num = $v['total_trade_count']; //总笔数
                        $reportFormDay->wechat_money = $v['wxpay_qrcode_receipt_money'] + $v['wxpay_barcode_receipt_money']; //微信金额
                        $reportFormDay->wechat_num = $v['wxpay_qrcode_trade_count'] + $v['wxpay_barcode_trade_count']; //微信笔数
                        $reportFormDay->alipay_money = $v['alipay_qrcode_receipt_money'] + $v['alipay_barcode_receipt_money']; //支付宝金额
                        $reportFormDay->alipay_num = $v['alipay_barcode_trade_count'] + $v['alipay_qrcode_trade_count']; //支付宝笔数
                        $reportFormDay->cash_money = $v['cashpay_receipt_money']; //现金金额
                        $reportFormDay->cash_num = $v['cashpay_trade_count']; //现金笔数
                        $reportFormDay->stored_money = $v['storedpay_receipt_money']; //储值金额
                        $reportFormDay->stored_num = $v['storedpay_trade_count']; //储值笔数
                        $reportFormDay->unionpay_money = $v['unionpay_receipt_money']; //银联金额
                        $reportFormDay->unionpay_num = $v['unionpay_trade_count']; //银联笔数
                        $reportFormDay->date = $key;
                        $reportFormDay->create_time = date('Y-m-d H:i:s');
                        if ($reportFormDay->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = '执行成功'; //错误信息
                        } else {
                            throw new Exception('数据保存失败');
                        }
                    }
                }
            } else {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = '暂无数据'; //错误信息
            }
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);

    }

    /**
     * 统计所有月汇总
     */
    public function summaryAllMonth()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1500M');
        $result = array();
        try {

            $date = date('Y-m', strtotime('-1 month')); //前一月
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition("date_format(pay_time,'%Y-%m') <= :pay_time");
            $criteria->params[':pay_time'] = $date;

            $list = Order::model()->findAll($criteria);

            foreach ($list as $k => $v) {
                $order_id = $v['id']; //订单id
                $order_no = $v['order_no']; //订单编号
                $operator_id = $v['operator_id']; //操作员
                $order_type = $v['order_type']; //订单类型
                $pay_status = $v['pay_status']; //支付状态
                $order_status = $v['order_status']; //订单状态
                $pay_channel = $v['pay_channel']; //支付方式
                $order_paymoney = $v['order_paymoney']; //订单总金额
                $online_paymoney = $v['online_paymoney']; //线上支付金额
                $unionpay_paymoney = $v['unionpay_paymoney']; //银联支付金额
                $cash_paymoney = $v['cash_paymoney']; //现金支付金额
                $stored_paymoney = $v['stored_paymoney']; //储值支付的金额
                $store_id = $v['store_id'];//门店id
                $time = date('Y-m', strtotime($v['pay_time']));

                //筛选必要订单
                if ($pay_status != ORDER_STATUS_PAID) {
                    continue; //未支付的订单不计入汇总
                }
                if ($order_type != ORDER_TYPE_CASHIER) {
                    continue; //非收款订单不计入汇总
                }
                //计算实收金额和退款金额
                $order_detail = $this->getReceiptAmount($order_no, TRUE);
                $receipt_money = $order_detail['receipt_money']; //实收金额

                switch ($pay_channel) {
                    case ORDER_PAY_CHANNEL_ALIPAY_SM:
                        $pay_channel_prefix = 'alipay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_ALIPAY_TM:
                        $pay_channel_prefix = 'alipay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_SM:
                        $pay_channel_prefix = 'wxpay_qrcode';
                        break;
                    case ORDER_PAY_CHANNEL_WXPAY_TM:
                        $pay_channel_prefix = 'wxpay_barcode';
                        break;
                    case ORDER_PAY_CHANNEL_CASH:
                        $pay_channel_prefix = 'cashpay';
                        break;
                    case ORDER_PAY_CHANNEL_UNIONPAY:
                        $pay_channel_prefix = 'unionpay';
                        break;
                    case ORDER_PAY_CHANNEL_STORED:
                        $pay_channel_prefix = 'storedpay';
                        break;
                    default:
                        $pay_channel_prefix = '';
                        break;
                }

                //不符合指定支付方式的不计入汇总
                if (empty($pay_channel_prefix)) {
                    continue;
                }
                if (empty($result['data'][$time][$store_id])) {
                    $result['data'][$time][$store_id] = array(
                        'total_trade_money' => 0, //交易金额
                        'total_receipt_money' => 0, //实收金额
                        'total_trade_count' => 0, //交易笔数
                        'alipay_qrcode_trade_money' => 0, //支付宝扫码交易金额
                        'alipay_qrcode_receipt_money' => 0, //支付宝扫码实收金额
                        'alipay_qrcode_trade_count' => 0, //支付宝扫码交易笔数
                        'alipay_barcode_trade_money' => 0, //支付宝条码交易金额
                        'alipay_barcode_receipt_money' => 0, //支付宝条码实收金额
                        'alipay_barcode_trade_count' => 0, //支付宝条码交易笔数
                        'wxpay_qrcode_trade_money' => 0, //微信扫码交易金额
                        'wxpay_qrcode_receipt_money' => 0, //微信扫码实收金额
                        'wxpay_qrcode_trade_count' => 0, //微信扫码交易笔数
                        'wxpay_barcode_trade_money' => 0, //微信条码交易金额
                        'wxpay_barcode_receipt_money' => 0, //微信条码实收金额
                        'wxpay_barcode_trade_count' => 0, //微信条码交易笔数
                        'cashpay_trade_money' => 0, //现金支付交易金额
                        'cashpay_receipt_money' => 0, //现金支付实收金额
                        'cashpay_trade_count' => 0, //现金支付交易笔数
                        'unionpay_trade_money' => 0, //银联支付交易金额
                        'unionpay_receipt_money' => 0, //银联支付实收金额
                        'unionpay_trade_count' => 0, //银联支付交易笔数
                        'storedpay_trade_money' => 0, //储值支付交易金额
                        'storedpay_receipt_money' => 0, //储值支付实收金额
                        'storedpay_trade_count' => 0, //储值支付交易笔数
                    );
                }

                $result['data'][$time][$store_id][$pay_channel_prefix . '_trade_money'] += $order_paymoney;
                $result['data'][$time][$store_id][$pay_channel_prefix . '_receipt_money'] += $receipt_money;
                $result['data'][$time][$store_id][$pay_channel_prefix . '_trade_count'] += 1;

                $result['data'][$time][$store_id]['total_trade_money'] += $order_paymoney;
                $result['data'][$time][$store_id]['total_receipt_money'] += $receipt_money;
                $result['data'][$time][$store_id]['total_trade_count'] += 1;
            }
            //var_dump($result['data']);exit;

            //月汇总入库
            $connection = Yii::app()->db;
            $transaction = $connection->beginTransaction();

            if (!empty($result['data'])) {
                foreach ($result['data'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        $reportFormMonth = new ReportFormMonth();
                        $reportFormMonth->store_id = $k;
                        $reportFormMonth->total_money = $v['total_receipt_money']; //总金额
                        $reportFormMonth->total_num = $v['total_trade_count']; //总笔数
                        $reportFormMonth->wechat_money = $v['wxpay_qrcode_receipt_money'] + $v['wxpay_barcode_receipt_money']; //微信金额
                        $reportFormMonth->wechat_num = $v['wxpay_qrcode_trade_count'] + $v['wxpay_barcode_trade_count']; //微信笔数
                        $reportFormMonth->alipay_money = $v['alipay_qrcode_receipt_money'] + $v['alipay_barcode_receipt_money']; //支付宝金额
                        $reportFormMonth->alipay_num = $v['alipay_barcode_trade_count'] + $v['alipay_qrcode_trade_count']; //支付宝笔数
                        $reportFormMonth->cash_money = $v['cashpay_receipt_money']; //现金金额
                        $reportFormMonth->cash_num = $v['cashpay_trade_count']; //现金笔数
                        $reportFormMonth->stored_money = $v['storedpay_receipt_money']; //储值金额
                        $reportFormMonth->stored_num = $v['storedpay_trade_count']; //储值笔数
                        $reportFormMonth->unionpay_money = $v['unionpay_receipt_money']; //银联金额
                        $reportFormMonth->unionpay_num = $v['unionpay_trade_count']; //银联笔数
                        $reportFormMonth->month = $key;
                        $reportFormMonth->create_time = date('Y-m-d H:i:s');
                        if ($reportFormMonth->save()) {
                            $result['status'] = ERROR_NONE; //状态码
                            $result['errMsg'] = '执行成功'; //错误信息
                        } else {
                            throw new Exception('数据保存失败');
                        }
                    }

                }
            } else {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = '暂无数据'; //错误信息
            }
            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);


    }

    /**
     * 计算实收金额
     */
    public function getReceiptAmount($order_no, $detailArray = FALSE)
    {

        $receipt_money = 0; //实收金额
        $refund_money = 0; //已退金额
        $refund_count = 0; //退款笔数

        $other_receipt_money = 0; //非储值支付方式的实收金额
        $other_refund_money = 0; //非储值支付方式的退款金额
        $other_refund_count = 0; //非储值支付方式的退款笔数

        $stored_receipt_money = 0; //储值实收金额
        $stored_refund_money = 0; //储值退款金额
        $stored_refund_count = 0; //储值退款笔数

        $model = Order::model()->find('order_no = :order_no', array(':order_no' => $order_no));
        if (!empty($model)) {
            $pay_channel = $model['pay_channel']; //支付方式
            //计算实收金额
            $order_money = $model['order_paymoney']; //订单总金额
            $stored_money = $model['stored_paymoney']; //储值支付金额
            $coupons_discount = $model['coupons_money']; //优惠券优惠金额
            $member_discount = $model['discount_money']; //会员优惠
            $merchant_discount = $model['merchant_discount_money']; //商家优惠
            $alipay_discount = $model['alipay_discount_money']; //支付宝优惠
            //订单实收金额（包含所有支付方式的实收金额）
            $receipt_money = $order_money - $coupons_discount - $member_discount - $merchant_discount;

            //计算订单的储值支付的金额和非储值支付的金额
            $stored_receipt_money = $stored_money;
            $other_receipt_money = $receipt_money - $stored_money;

            //查询退款记录
            $criteria = new CDbCriteria();
            $criteria->order = 'create_time asc';
            $criteria->addCondition('order_id = :order_id');
            $criteria->params[':order_id'] = $model['id'];
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $criteria->addCondition('type = :type');
            $criteria->params[':type'] = REFUND_TYPE_REFUND;
            $criteria->addCondition('status = :status1 or status = :status2');
            $criteria->params[':status1'] = REFUND_STATUS_SUCCESS;
            $criteria->params[':status2'] = REFUND_STATUS_PROCESSING;
            $refund_record = RefundRecord::model()->findAll($criteria);
            $record = array();
            foreach ($refund_record as $k => $v) {
                if (empty($v)) {
                    continue;
                }
                $record_refund_money = $v['refund_money'];
                $receipt_money -= $record_refund_money; //订单实收金额统计
                $refund_money += $record_refund_money; //订单已退金额统计
                $refund_count++; //订单退款笔数统计

                if (!$detailArray) { //是否需要统计退款明细
                    continue;
                }
                //退款明细统计（储值支付与非储值支付两部分）
                if (round($stored_receipt_money, 2) > 0) { //有可退的储值金额
                    $stored_receipt_money -= $record_refund_money; //储值支付实收统计
                    $stored_refund_money += $record_refund_money; //储值支付已退统计
                    $stored_refund_count++; //储值支付退款笔数统计
                    if (round($stored_receipt_money, 2) < 0) { //可退的储值金额不足，需要退非储值支付金额
                        $other_receipt_money -= -($stored_receipt_money); //非储值支付实收统计
                        $other_refund_money += -($stored_receipt_money); //非储值支付已退统计
                        $other_refund_count++; //非储值支付退款笔数统计

                        $stored_receipt_money = 0; //储值支付实收归零
                        $stored_refund_money = $stored_money; //储值支付已全退
                    }
                } else {
                    $other_receipt_money -= $record_refund_money; //非储值支付实收统计
                    $other_refund_money += $record_refund_money; //非储值支付已退统计
                    $other_refund_count++; //非储值支付退款笔数统计
                }
            }

            // 			if ($receipt_money < 0) { //实收金额为负则为零
            // 				$receipt_money = 0;
            // 			}
            if ($model['pay_status'] != ORDER_STATUS_PAID) { //订单不是已支付则为零
                $receipt_money = 0;
            }
        }

        if ($detailArray) {
            return array(
                'receipt_money' => $receipt_money,
                'refund_money' => $refund_money,
                'refund_count' => $refund_count,
                'stored_receipt_money' => $stored_receipt_money,
                'stored_refund_money' => $stored_refund_money,
                'stored_refund_count' => $stored_refund_count,
                'other_receipt_money' => $other_receipt_money,
                'other_refund_money' => $other_refund_money,
                'other_refund_count' => $other_refund_count,
            );
        } else {
            return $receipt_money;
        }

    }

    /**
     *新会员赠券--给加入30天内并消费一次的会员发券
     */
    public function sendCouponsNewUser()
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction();
        $type = MARKETING_ACTIVITY_TYPE_NEW_MEMBER_GIVE;
        try {
            $nowTime = date('Y-m-d H:i:s');
            $user = User::model()->findAll('flag=:flag', array(':flag' => FLAG_NO));
            foreach ($user as $k => $v) {
                if ($this->diffBetweenDays($nowTime, $v['regist_time']) <= 30) {
                    $order = Order::model()->find('user_id=:user_id and pay_status=:pay_status and flag=:flag', array(
                        ':user_id' => $v ['id'],
                        ':pay_status' => GJORDER_PAY_STATUS_PAID,
                        ':flag' => FLAG_NO
                    ));
                    if (count($order) > 0) { // 消费一次的会员
                        if (!$this->isSendYhq($v['id'], $type)) {
                            $user_coupons = new UserCoupons();
                            $market = MarketingActivity::model()->find('flag=:flag and type=:type', array(
                                ':flag' => FLAG_NO,
                                ':type' => MARKETING_ACTIVITY_TYPE_NEW_MEMBER_GIVE
                            ));
                            if (!empty($market)) {
                                $coupon = Coupons::model()->findByPk($market['coupon_id']);
                                $user_coupons->user_id = $v['id'];
                                $user_coupons->coupons_id = $market['coupon_id'];
                                if ($coupon->money_type == FACE_VALUE_TYPE_FIXED) { //固定面额
                                    $user_coupons->money = $coupon->money;
                                } else {  //随机面额
                                    $user_coupons->money = $coupon->money_random;
                                }
                                $user_coupons->start_time = $coupon->start_time;
                                $user_coupons->end_time = $coupon->end_time;
                                if ($coupon->if_wechat == IF_WECHAT_YES) {  //是否同步到微信卡包 1不开启 2开启
                                    $user_coupons->if_wechat = IF_WECHAT_YES;
                                    $user_coupons->wechat_coupons_id = $coupon->card_id;
                                } else {
                                    $user_coupons->if_wechat = IF_WECHAT_NO;
                                }
                                $user_coupons->if_give = $coupon->if_give;
                                $user_coupons->marketing_activity_id = $market['id'];
                                $user_coupons->create_time = date('Y-m-d H:i:s');

                                if (!$user_coupons->save()) {
                                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                                    $result['errMsg'] = '数据保存失败'; //错误信息
                                    throw new Exception('会员赠券失败');
                                }
                            }
                        }
                    }
                }
            }
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 加入未消费会员赠券--给已经是加入但没有消费过的会员赠券
     */
    public function sendCouponsNotConsume()
    {
        $result = array();
        $type = MARKETING_ACTIVITY_TYPE_NO_TRADE_MEMBER;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findAll('flag=:flag', array(':flag' => FLAG_NO));
            foreach ($user as $k => $v) {
                $order = Order::model()->find('user_id=:user_id and pay_status=:pay_status and flag=:flag', array(
                    ':user_id' => $v ['id'],
                    ':pay_status' => GJORDER_PAY_STATUS_PAID,
                    ':flag' => FLAG_NO
                ));
                if (count($order) == 0) { //没有消费过
                    if (!$this->isSendYhq($v['id'], $type)) {
                        $user_coupons = new UserCoupons();
                        $market = MarketingActivity::model()->find('flag=:flag and type=:type', array(
                            ':flag' => FLAG_NO,
                            ':type' => MARKETING_ACTIVITY_TYPE_NO_TRADE_MEMBER
                        ));
                        if (!empty($market)) {
                            $coupon = Coupons::model()->findByPk($market['coupon_id']);
                            $user_coupons->user_id = $v['id'];
                            $user_coupons->coupons_id = $market['coupon_id'];
                            if ($coupon->money_type == FACE_VALUE_TYPE_FIXED) { //固定面额
                                $user_coupons->money = $coupon->money;
                            } else {  //随机面额
                                $user_coupons->money = $coupon->money_random;
                            }
                            $user_coupons->start_time = $coupon->start_time;
                            $user_coupons->end_time = $coupon->end_time;
                            if ($coupon->if_wechat == IF_WECHAT_YES) {  //是否同步到微信卡包 1不开启 2开启
                                $user_coupons->if_wechat = IF_WECHAT_YES;
                                $user_coupons->wechat_coupons_id = $coupon->card_id;
                            } else {
                                $user_coupons->if_wechat = IF_WECHAT_NO;
                            }
                            $user_coupons->if_give = $coupon->if_give;
                            $user_coupons->marketing_activity_id = $market['id'];
                            $user_coupons->create_time = date('Y-m-d H:i:s');

                            if (!$user_coupons->save()) {
                                $result['status'] = ERROR_SAVE_FAIL; //状态码
                                $result['errMsg'] = '数据保存失败'; //错误信息
                                throw new Exception('会员赠券失败');
                            }
                        }
                    }
                }
            }
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 生日赠券--建立会员生日送券活动
     */
    public function sendCouponsBirthday()
    {
        $result = array();
        $type = MARKETING_ACTIVITY_TYPE_BIRTHDAY_GIVE;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $nowTime = date('m-d');
            $user = User::model()->findAll('flag=:flag', array(':flag' => FLAG_NO));
            foreach ($user as $k => $v) {
                if (!empty($v['birthday'])) {
                    if ($nowTime == date('m-d', strtotime($v['birthday']))) { //如果今天生日
                        if (!$this->isSendYhq($v['id'], $type)) {
                            $user_coupons = new UserCoupons();
                            $market = MarketingActivity::model()->find('flag=:flag and type=:type', array(
                                ':flag' => FLAG_NO,
                                ':type' => MARKETING_ACTIVITY_TYPE_BIRTHDAY_GIVE
                            ));
                            if (!empty($market)) {
                                $coupon = Coupons::model()->findByPk($market['coupon_id']);
                                $user_coupons->user_id = $v['id'];
                                $user_coupons->coupons_id = $market['coupon_id'];
                                if ($coupon->money_type == FACE_VALUE_TYPE_FIXED) { //固定面额
                                    $user_coupons->money = $coupon->money;
                                } else {  //随机面额
                                    $user_coupons->money = $coupon->money_random;
                                }
                                $user_coupons->start_time = $coupon->start_time;
                                $user_coupons->end_time = $coupon->end_time;
                                if ($coupon->if_wechat == IF_WECHAT_YES) {  //是否同步到微信卡包 1不开启 2开启
                                    $user_coupons->if_wechat = IF_WECHAT_YES;
                                    $user_coupons->wechat_coupons_id = $coupon->card_id;
                                } else {
                                    $user_coupons->if_wechat = IF_WECHAT_NO;
                                }
                                $user_coupons->if_give = $coupon->if_give;
                                $user_coupons->marketing_activity_id = $market['id'];
                                $user_coupons->create_time = date('Y-m-d H:i:s');

                                if (!$user_coupons->save()) {
                                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                                    $result['errMsg'] = '数据保存失败'; //错误信息
                                    throw new Exception('会员赠券失败');
                                }
                            }
                        }
                    }
                }
            }
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 给老会员赠券--给注册会员一个月以上，且60天内消费过一次的会员赠券
     */
    public function sendCouponsOld()
    {
        $result = array();
        $type = MARKETING_ACTIVITY_TYPE_OLD_MEMBER_GIVE;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $nowTime = date('Y-m-d H:i:s');
            $user = User::model()->findAll('flag=:flag', array(':flag' => FLAG_NO));
            foreach ($user as $k => $v) {

                if ($this->diffBetweenDays($nowTime, $v['regist_time']) > 30) {

                    if ($this->isFee($v['id'], $nowTime)) { // 60天内消费过一次
                        if (!$this->isSendYhq($v['id'], $type)) {
                            $user_coupons = new UserCoupons();
                            $market = MarketingActivity::model()->find('flag=:flag and type=:type', array(
                                ':flag' => FLAG_NO,
                                ':type' => MARKETING_ACTIVITY_TYPE_OLD_MEMBER_GIVE
                            ));
                            if (!empty($market)) {
                                $coupon = Coupons::model()->findByPk($market['coupon_id']);
                                $user_coupons->user_id = $v['id'];
                                $user_coupons->coupons_id = $market['coupon_id'];
                                if ($coupon->money_type == FACE_VALUE_TYPE_FIXED) { //固定面额
                                    $user_coupons->money = $coupon->money;
                                } else {  //随机面额
                                    $user_coupons->money = $coupon->money_random;
                                }
                                $user_coupons->start_time = $coupon->start_time;
                                $user_coupons->end_time = $coupon->end_time;
                                if ($coupon->if_wechat == IF_WECHAT_YES) {  //是否同步到微信卡包 1不开启 2开启
                                    $user_coupons->if_wechat = IF_WECHAT_YES;
                                    $user_coupons->wechat_coupons_id = $coupon->card_id;
                                } else {
                                    $user_coupons->if_wechat = IF_WECHAT_NO;
                                }
                                $user_coupons->if_give = $coupon->if_give;
                                $user_coupons->marketing_activity_id = $market['id'];
                                $user_coupons->create_time = date('Y-m-d H:i:s');

                                if (!$user_coupons->save()) {
                                    $result['status'] = ERROR_SAVE_FAIL; //状态码
                                    $result['errMsg'] = '数据保存失败'; //错误信息
                                    throw new Exception('会员赠券失败');
                                }
                            }
                        }
                    }
                }

            }
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 会员赠券--给会员发券
     */
    public function sendCouponsUser()
    {
        $result = array();
        $type = MARKETING_ACTIVITY_TYPE_MEMBER_GIVE;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = User::model()->findAll('flag=:flag', array(':flag' => FLAG_NO));
            foreach ($user as $v) {
                if (!$this->isSendYhq($v['id'], $type)) {
                    $user_coupons = new UserCoupons();
                    $market = MarketingActivity::model()->find('flag=:flag and type=:type', array(
                        ':flag' => FLAG_NO,
                        ':type' => MARKETING_ACTIVITY_TYPE_MEMBER_GIVE
                    ));
                    if (!empty($market)) {
                        $coupon = Coupons::model()->findByPk($market['coupon_id']);
                        $user_coupons->user_id = $v['id'];
                        $user_coupons->coupons_id = $market['coupon_id'];
                        if ($coupon->money_type == FACE_VALUE_TYPE_FIXED) { //固定面额
                            $user_coupons->money = $coupon->money;
                        } else {  //随机面额
                            $user_coupons->money = $coupon->money_random;
                        }
                        $user_coupons->start_time = $coupon->start_time;
                        $user_coupons->end_time = $coupon->end_time;
                        if ($coupon->if_wechat == IF_WECHAT_YES) {  //是否同步到微信卡包 1不开启 2开启
                            $user_coupons->if_wechat = IF_WECHAT_YES;
                            $user_coupons->wechat_coupons_id = $coupon->card_id;
                        } else {
                            $user_coupons->if_wechat = IF_WECHAT_NO;
                        }
                        $user_coupons->if_give = $coupon->if_give;
                        $user_coupons->marketing_activity_id = $market['id'];
                        $user_coupons->create_time = date('Y-m-d H:i:s');

                        if (!$user_coupons->save()) {
                            $result['status'] = ERROR_SAVE_FAIL; //状态码
                            $result['errMsg'] = '数据保存失败'; //错误信息
                            throw new Exception('会员赠券失败');
                        }
                    }
                }
            }
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage();
        }
        return json_encode($result);
    }

    /**
     * 判断会员是否60天内消费过一次
     * return true(已经消费过一次)    false
     */
    public function isFee($user_id, $nowTime)
    {
        $flag = 0;
        $order = Order::model()->findAll('user_id=:user_id and pay_status=:pay_status and flag=:flag', array(
            ':user_id' => $user_id,
            ':pay_status' => GJORDER_PAY_STATUS_PAID,
            ':flag' => FLAG_NO
        ));
        foreach ($order as $v) {
            if ($this->diffBetweenDays($nowTime, $v['pay_time']) <= 60) {
                $flag++;
            }
        }
        if ($flag > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断会员是否已经赠送过券了
     * return true(已经送过)    false
     */
    public function isSendYhq($user_id, $type)
    {
        $flag = 0;
        $user_coupons = UserCoupons::model()->findAll('flag=:flag and user_id=:user_id and marketing_activity_id is not :marketing_activity_id', array(
            ':flag' => FLAG_NO,
            ':user_id' => $user_id,
            ':marketing_activity_id' => null
        ));
        if (empty($user_coupons)) {
            return false;
        } else {
            foreach ($user_coupons as $val) {
                $activity = MarketingActivity::model()->findByPk($val['marketing_activity_id']);
                if (!empty($activity)) {
                    if ($activity['type'] == $type) {
                        $flag++;
                    }
                }
            }
            if ($flag > 0) { //说明已经送过了
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 求两个日期之间相差的天数
     */
    public function diffBetweenDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return round(($second1 - $second2) / 86400);
    }


    /**
     * @param $merchant_id
     * @param string $start
     * @param string $end
     * @param string $day
     * @return string
     * 交易额统计
     */
    public function TurnOverStatistics($agent_id, $start, $end, $arrow = 1, $arrow_type = 1)
    {
        $result = array();

        //查询数据
        try {
            if (empty($agent_id)) {
                throw new Exception('缺少参数');
            }
            $criteria = new CDbCriteria();
            $criteria->addCondition('agent_id = :agent_id');
            $criteria->params[':agent_id'] = $agent_id;


            $merchant = Merchant::model()->findAll($criteria);
            $merchant_id_arr = array();
            foreach ($merchant as $k => $v) {
                $merchant_id_arr[$k] = $v->id;
            }
            $criteria = new CDbCriteria();
            $criteria->select = 'merchant_id,sum(new_trade_alipay_money) new_trade_alipay_money,sum(new_trade_alipay_num) new_trade_alipay_num,sum(new_trade_wechat_money) new_trade_wechat_money,sum(new_trade_wechat_num) new_trade_wechat_num'; //代表了要查询的字段，默认select='*';
            $criteria->addCondition('date >= :start');
            $criteria->params[':start'] = date('Y-m-d 00:00:00', strtotime($start));
            $criteria->addCondition('date <= :end');
            $criteria->params[':end'] = date('Y-m-d 23:59:59', strtotime($end));
            $criteria->addInCondition('merchant_id', $merchant_id_arr);
            $criteria->group = 'merchant_id';

            switch ($arrow_type) {
                case $arrow_type = 1: //按支付宝交易额排序
                    if ($arrow == 1) {
                        $criteria->order = 'new_trade_alipay_money DESC';//排序条件，降序
                    } else {
                        $criteria->order = 'new_trade_alipay_money ASC';//排序条件，升序
                    }

                    break;
                case $arrow_type = 2: //按支付宝交易笔数排序
                    if ($arrow == 1) {
                        $criteria->order = 'new_trade_alipay_num DESC';//排序条件，降序
                    } else {
                        $criteria->order = 'new_trade_alipay_num ASC';//排序条件，升序
                    }
                    break;
                case $arrow_type = 3:
                    if ($arrow == 1) {
                        $criteria->order = 'new_trade_wechat_money DESC';//排序条件，降序
                    } else {
                        $criteria->order = 'new_trade_wechat_money ASC';//排序条件，升序
                    }
                    break;
                case $arrow_type = 4:
                    if ($arrow == 1) {
                        $criteria->order = 'new_trade_wechat_num DESC';//排序条件，降序
                    } else {
                        $criteria->order = 'new_trade_wechat_num ASC';//排序条件，升序
                    }
                    break;
            }

            //分页
            $pages = new CPagination(MStatistics::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;


            $ms = MStatistics::model()->findAll($criteria);

            $data = array();
            foreach ($ms as $k => $v) {
                $data[$k]['new_trade_alipay_money'] = 0;
                $data[$k]['new_trade_alipay_num'] = 0;
                $data[$k]['new_trade_wechat_money'] = 0;
                $data[$k]['new_trade_wechat_num'] = 0;
                $data[$k]['merchant_id'] = $v->merchant_id;
                $data[$k]['merchant_no'] = $v->merchant->wq_mchid;
                $data[$k]['merchant_name'] = $v->merchant->wq_m_name;
                $data[$k]['agent_name'] = $v->merchant->agent->name;
                $data[$k]['new_trade_alipay_money'] += $v->new_trade_alipay_money;
                $data[$k]['new_trade_alipay_num'] += $v->new_trade_alipay_num;
                $data[$k]['new_trade_wechat_money'] += $v->new_trade_wechat_money;
                $data[$k]['new_trade_wechat_num'] += $v->new_trade_wechat_num;
            }

            $result['status'] = ERROR_NONE;
            $result['data'] = $data;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    public function doStatistics() {
    	//使用事务
    	$transaction = Yii::app()->db->beginTransaction();
    	try {
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$list = Merchant::model()->findAll($criteria);
    		
    		foreach ($list as $k => $v) {
    			//统计该商户下所有门店的订单数据并保存
    			$ret = $this->saveStoreStatistics($v['id']);
    			$result = json_decode($ret, true);
    			if ($result['status'] != ERROR_NONE) {
    				throw new Exception($result['errMsg']);
    			}
    		}
    		
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$list = Agent::model()->findAll($criteria);
    		foreach ($list as $k => $v) {
    			//统计该服务商下所有商户的订单数据并保存
    			$ret = $this->saveMerchantStatistics($v['id']);
    			$result = json_decode($ret, true);
    			if ($result['status'] != ERROR_NONE) {
    				throw new Exception($result['errMsg']);
    			}
    		}
    		
    		//统计所有服务商的订单数据并保存
    		$ret = $this->saveAgentStatistics();
    		$result = json_decode($ret, true);
    		if ($result['status'] != ERROR_NONE) {
    			throw new Exception($result['errMsg']);
    		}
    		
    		//统计所有数据
    		$ret = $this->saveTotalStatistics();
    		$result = json_decode($ret, true);
    		if ($result['status'] != ERROR_NONE) {
    			throw new Exception($result['errMsg']);
    		}
    		
    		//事务提交
    		$transaction->commit();
    	} catch (Exception $e) {
    		//事务回滚
    		$transaction->rollback();
    		$msg = $e->getMessage();
    		Yii::log("统计错误日志：\n时间：".date('Y-m-d H:i:s')."\n错误信息：".$msg, 'warning');
    	}
    }
    
    public function saveStatistics($merchant_id) {
    	$result = array();
    	$transaction = Yii::app()->db->beginTransaction();
    	try {
    		//查询商户信息
    		$merchant = Merchant::model()->findByPk($merchant_id);
    		if (empty($merchant)) {
    			throw new Exception('商户不存在');
    		}
    
    		//统计的开始时间
    		$start_time = '';
    		//统计的结束时间：昨日的时间
    		$end_time = date('Y-m-d 23:59:59', strtotime("-1 day"));
    		$yesterday = strtotime($end_time);
    
    		//查询最新的统计数据
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('merchant_id = :merchant_id');
    		$criteria->params[':merchant_id'] = $merchant_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		$m_statistics = MStatistics::model()->find($criteria);
    		if (!empty($m_statistics)) {
    			$last_time = $m_statistics['date'];
    			//昨日的数据已经统计
    			if (strtotime($last_time) == $yesterday) {
    				throw new Exception('昨日数据已统计');
    			}
    			$start_time = date('Y-m-d 00:00:00', strtotime("+1 day", $last_time));
    		}
    
    		//获取门店交易数据
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('t.flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('pay_status = :custom_pay_status');
    		$cmd->params[':custom_pay_status'] = ORDER_STATUS_PAID; //已支付订单
    		$cmd->andWhere('order_type = :custom_order_type');
    		$cmd->params[':custom_order_type'] = ORDER_TYPE_CASHIER; //收银订单
    		$cmd->andWhere('merchant_id = :merchant_id');
    		$cmd->params[':merchant_id'] = $merchant_id;
    		if (!empty($start_time)) {
    			$cmd->andWhere('pay_time >= :start_time');
    			$cmd->params[':start_time'] = $start_time;
    		}
    		if (!empty($end_time)) {
    			$cmd->andWhere('pay_time <= :end_time');
    			$cmd->params[':end_time'] = $end_time;
    		}
    		//分组
    		$cmd->group = 'store_id, pay_channel';
    		//指定查询表
    		$cmd->from = 'wq_order t';
    
    		//退款查询
    		$cmd2 = clone $cmd; //深拷贝
    		//今日数据查询
    		$cmd3 = clone $cmd; //深拷贝
    		//今日退款查询
    		$cmd4 = clone $cmd; //深拷贝
    
    		//查询计算1
    		$select1 = 'store_id, pay_channel';
    		$select1 .= ', SUM(order_paymoney) AS order_sum';
    		$select1 .= ', SUM(coupons_money) AS coupons_sum';
    		$select1 .= ', SUM(discount_money) AS discount_sum';
    		$select1 .= ', SUM(merchant_discount_money) AS m_discount_sum';
    		$select1 .= ', COUNT(pay_channel) AS trade_sum';
    		$cmd->select = $select1;
    
    		//执行sql查询:统计订单金额，优惠金额，交易笔数
    		$list1 = $cmd->queryAll();
    
    		//查询计算2
    		$select2 = 'pay_channel, SUM(r.refund_money) AS refund_sum';
    		$cmd2->select = $select2;
    
    		//联表
    		$join = 'JOIN wq_refund_record r ON t.id = r.order_id';
    		$join .= ' AND r.flag = '.FLAG_NO.' AND r.status != '.REFUND_STATUS_FAIL.' AND r.type = '.REFUND_TYPE_REFUND;
    		$cmd2->join = $join;
    
    		//执行sql查询:统计退款金额
    		$list2 = $cmd2->queryAll();
    
    		//查询条件3
    		$cmd3->andWhere('pay_time >= :start_time1');
    		$cmd3->params[':start_time1'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    
    		//查询3
    		$select3 = 'store_id, pay_channel';
    		$select3 .= ', SUM(order_paymoney) AS order_sum';
    		$select3 .= ', SUM(coupons_money) AS coupons_sum';
    		$select3 .= ', SUM(discount_money) AS discount_sum';
    		$select3 .= ', SUM(merchant_discount_money) AS m_discount_sum';
    		$select3 .= ', COUNT(pay_channel) AS trade_sum';
    		$cmd3->select = $select3;
    
    		//执行sql查询:统计今日订单金额，优惠金额，交易笔数
    		$list3 = $cmd3->queryAll();
    
    		//查询条件4
    		$cmd4->andWhere('pay_time >= :start_time1');
    		$cmd4->params[':start_time1'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    
    		//查询计算4
    		$select4 = 'pay_channel, SUM(r.refund_money) AS refund_sum';
    		$cmd4->select = $select4;
    
    		//联表4
    		$join = 'JOIN wq_refund_record r ON t.id = r.order_id';
    		$join .= ' AND r.flag = '.FLAG_NO.' AND r.status != '.REFUND_STATUS_FAIL.' AND r.type = '.REFUND_TYPE_REFUND;
    		$cmd4->join = $join;
    
    		//执行sql查询:统计昨日退款金额
    		$list4 = $cmd4->queryAll();
    
    		//获取该商户下所有门店并初始化交易数组
    		$stores = Store::model()->findAll('merchant_id = :merchant_id AND flag = :flag',
    				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
    		$data = array();
    		foreach ($stores as $k => $v) {
    			$store_id = $v['id'];
    			$data[$store_id] = array(
    					'total_trade_money' => 0, //交易金额
    					'total_receipt_money' => 0, //实收金额
    					'total_yesterday_trade_money' => 0, //昨日交易金额
    					'total_yesterday_receipt_money' => 0, //昨日实收金额
    					'total_trade_count' => 0, //交易笔数
    					'total_yesterday_trade_count' => 0, //昨日交易笔数
    					'alipay_trade_money' => 0, //支付宝交易金额
    					'alipay_receipt_money' => 0, //支付宝实收金额
    					'alipay_yesterday_trade_money' => 0, //支付宝昨日交易金额
    					'alipay_yesterday_receipt_money' => 0, //支付宝昨日实收金额
    					'alipay_trade_count' => 0, //支付宝交易笔数
    					'alipay_yesterday_trade_count' => 0, //支付宝昨日交易笔数
    					'wxpay_trade_money' => 0, //微信交易金额
    					'wxpay_receipt_money' => 0, //微信实收金额
    					'wxpay_yesterday_trade_money' => 0, //微信昨日交易金额
    					'wxpay_yesterday_receipt_money' => 0, //微信昨日实收金额
    					'wxpay_trade_count' => 0, //微信交易笔数
    					'wxpay_yesterday_trade_count' => 0, //微信昨日交易笔数
    					'cashpay_trade_money' => 0, //现金交易金额
    					'cashpay_receipt_money' => 0, //现金实收金额
    					'cashpay_yesterday_trade_money' => 0, //现金昨日交易金额
    					'cashpay_yesterday_receipt_money' => 0, //现金昨日实收金额
    					'cashpay_trade_count' => 0, //现金交易笔数
    					'cashpay_yesterday_trade_count' => 0, //现金昨日交易笔数
    					'unionpay_trade_money' => 0, //银联支付交易金额
    					'unionpay_receipt_money' => 0, //银联支付实收金额
    					'unionpay_yesterday_trade_money' => 0, //银联支付昨日交易金额
    					'unionpay_yesterday_receipt_money' => 0, //银联支付昨日实收金额
    					'unionpay_trade_count' => 0, //银联支付交易笔数
    					'unionpay_yesterday_trade_count' => 0, //银联支付昨日交易笔数
    					'storedpay_trade_money' => 0, //储值支付交易金额
    					'storedpay_receipt_money' => 0, //储值支付实收金额
    					'storedpay_yesterday_trade_money' => 0, //储值支付昨日交易金额
    					'storedpay_yesterday_receipt_money' => 0, //储值支付昨日实收金额
    					'storedpay_trade_count' => 0, //储值支付交易笔数
    					'storedpay_yesterday_trade_count' => 0, //储值支付昨日交易笔数
    			);
    		}
    
    		foreach ($list1 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$order_money = $v['order_sum']; //订单总金额
    			$coupons_discount = $v['coupons_sum']; //优惠券优惠金额
    			$member_discount = $v['discount_sum']; //会员优惠
    			$merchant_discount = $v['m_discount_sum']; //商家优惠
    			$trade_count = $v['trade_sum']; //交易笔数
    
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    			//优惠金额
    			$discount_money = $coupons_discount + $member_discount + $merchant_discount;
    			//实收金额
    			$receipt_money = $order_money - $discount_money;
    			//计算交易金额和实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data[$store_id]['alipay_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data[$store_id]['alipay_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data[$store_id]['wxpay_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data[$store_id]['wxpay_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data[$store_id]['cashpay_trade_money'] += $order_money;
    					//$data[$store_id]['cashpay_discount_money'] += $discount_money;
    					$data[$store_id]['cashpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['cashpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data[$store_id]['unionpay_trade_money'] += $order_money;
    					//$data[$store_id]['unionpay_discount_money'] += $discount_money;
    					$data[$store_id]['unionpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['unionpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data[$store_id]['storedpay_trade_money'] += $order_money;
    					//$data[$store_id]['storedpay_discount_money'] += $discount_money;
    					$data[$store_id]['storedpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['storedpay_trade_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data[$store_id]['total_trade_money'] += $order_money;
    			//$data[$store_id]['total_discount_money'] += $discount_money;
    			$data[$store_id]['total_receipt_money'] += $receipt_money;
    			$data[$store_id]['total_trade_count'] += $trade_count;
    		}
    
    		foreach ($list2 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$refund_money = $v['refund_sum']; //退款金额
    			$refund_count = $v['record_sum']; //退款笔数
    
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    
    			//计算实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					//$data[$store_id]['alipay_refund_count'] += $refund_count;
    					//$data[$store_id]['alipay_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					//$data[$store_id]['alipay_refund_count'] += $refund_count;
    					//$data[$store_id]['alipay_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					//$data[$store_id]['wxpay_refund_count'] += $refund_count;
    					//$data[$store_id]['wxpay_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					//$data[$store_id]['wxpay_refund_count'] += $refund_count;
    					//$data[$store_id]['wxpay_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					//$data[$store_id]['cashpay_refund_count'] += $refund_count;
    					//$data[$store_id]['cashpay_refund_money'] += $refund_money;
    					$data[$store_id]['cashpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					//$data[$store_id]['unionpay_refund_count'] += $refund_count;
    					//$data[$store_id]['unionpay_refund_money'] += $refund_money;
    					$data[$store_id]['unionpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					//$data[$store_id]['storedpay_refund_count'] += $refund_count;
    					//$data[$store_id]['storedpay_refund_money'] += $refund_money;
    					$data[$store_id]['storedpay_receipt_money'] -= $refund_money;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			//$data[$store_id]['total_refund_count'] += $refund_count;
    			//$data[$store_id]['total_refund_money'] += $refund_money;
    			$data[$store_id]['total_receipt_money'] -= $refund_money;
    		}
    
    		foreach ($list3 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$order_money = $v['order_sum']; //订单总金额
    			$coupons_discount = $v['coupons_sum']; //优惠券优惠金额
    			$member_discount = $v['discount_sum']; //会员优惠
    			$merchant_discount = $v['m_discount_sum']; //商家优惠
    			$trade_count = $v['trade_sum']; //交易笔数
    
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    			$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    			$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    			$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    			$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    			$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    			$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    			//优惠金额
    			$discount_money = $coupons_discount + $member_discount + $merchant_discount;
    			//实收金额
    			$receipt_money = $order_money - $discount_money;
    			//计算交易金额和实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data[$store_id]['alipay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data[$store_id]['alipay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data[$store_id]['wxpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data[$store_id]['wxpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data[$store_id]['cashpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['cashpay_discount_money'] += $discount_money;
    					$data[$store_id]['cashpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['cashpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data[$store_id]['unionpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['unionpay_discount_money'] += $discount_money;
    					$data[$store_id]['unionpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['unionpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data[$store_id]['storedpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['storedpay_discount_money'] += $discount_money;
    					$data[$store_id]['storedpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['storedpay_yesterday_trade_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data[$store_id]['total_yesterday_trade_money'] += $order_money;
    			//$data[$store_id]['total_discount_money'] += $discount_money;
    			$data[$store_id]['total_yesterday_receipt_money'] += $receipt_money;
    			$data[$store_id]['total_yesterday_trade_count'] += $trade_count;
    		}
    
    		foreach ($list4 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$refund_money = $v['refund_sum']; //退款金额
    			$refund_count = $v['record_sum']; //退款笔数
    
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    
    			//计算实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					//$data[$store_id]['alipay_refund_count'] += $refund_count;
    					//$data[$store_id]['alipay_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					//$data[$store_id]['alipay_refund_count'] += $refund_count;
    					//$data[$store_id]['alipay_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					//$data[$store_id]['wxpay_refund_count'] += $refund_count;
    					//$data[$store_id]['wxpay_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					//$data[$store_id]['wxpay_refund_count'] += $refund_count;
    					//$data[$store_id]['wxpay_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					//$data[$store_id]['cashpay_refund_count'] += $refund_count;
    					//$data[$store_id]['cashpay_refund_money'] += $refund_money;
    					$data[$store_id]['cashpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					//$data[$store_id]['unionpay_refund_count'] += $refund_count;
    					//$data[$store_id]['unionpay_refund_money'] += $refund_money;
    					$data[$store_id]['unionpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					//$data[$store_id]['storedpay_refund_count'] += $refund_count;
    					//$data[$store_id]['storedpay_refund_money'] += $refund_money;
    					$data[$store_id]['storedpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			//$data[$store_id]['total_refund_count'] += $refund_count;
    			//$data[$store_id]['total_refund_money'] += $refund_money;
    			$data[$store_id]['total_yesterday_receipt_money'] -= $refund_money;
    		}
    
    		$m_model = new MStatistics();
    
    		foreach ($data as $k => $v) {
    			//跳过错误数据
    			if (empty($k) || empty($v)) {
    				continue;
    			}
    			$store_id = $k;
    			$date = $end_time;
    			$total_trade_money = $v['total_trade_money'];
    			$total_receipt_money = $v['total_receipt_money'];
    			$total_trade_count = $v['total_trade_count'];
    			$total_yesterday_trade_money = $v['total_yesterday_trade_money'];
    			$total_yesterday_receipt_money = $v['total_yesterday_receipt_money'];
    			$total_yesterday_trade_count = $v['total_yesterday_trade_count'];
    			$alipay_trade_money = $v['alipay_trade_money'];
    			$alipay_receipt_money = $v['alipay_receipt_money'];
    			$alipay_trade_count = $v['alipay_trade_count'];
    			$alipay_yesterday_trade_money = $v['alipay_yesterday_trade_money'];
    			$alipay_yesterday_receipt_money = $v['alipay_yesterday_receipt_money'];
    			$alipay_yesterday_trade_count = $v['alipay_yesterday_trade_count'];
    			$wxpay_trade_money = $v['wxpay_trade_money'];
    			$wxpay_receipt_money = $v['wxpay_receipt_money'];
    			$wxpay_trade_count = $v['wxpay_trade_count'];
    			$wxpay_yesterday_trade_money = $v['wxpay_yesterday_trade_money'];
    			$wxpay_yesterday_receipt_money = $v['wxpay_yesterday_receipt_money'];
    			$wxpay_yesterday_trade_count = $v['wxpay_yesterday_trade_count'];
    			$cashpay_trade_money = $v['cashpay_trade_money'];
    			$cashpay_receipt_money = $v['cashpay_receipt_money'];
    			$cashpay_trade_count = $v['cashpay_trade_count'];
    			$cashpay_yesterday_trade_money = $v['cashpay_yesterday_trade_money'];
    			$cashpay_yesterday_receipt_money = $v['cashpay_yesterday_receipt_money'];
    			$cashpay_yesterday_trade_count = $v['cashpay_yesterday_trade_count'];
    			$unionpay_trade_money = $v['unionpay_trade_money'];
    			$unionpay_receipt_money = $v['unionpay_receipt_money'];
    			$unionpay_trade_count = $v['unionpay_trade_count'];
    			$unionpay_yesterday_trade_money = $v['unionpay_yesterday_trade_money'];
    			$unionpay_yesterday_receipt_money = $v['unionpay_yesterday_receipt_money'];
    			$unionpay_yesterday_trade_count = $v['unionpay_yesterday_trade_count'];
    			$storedpay_trade_money = $v['storedpay_trade_money'];
    			$storedpay_receipt_money = $v['storedpay_receipt_money'];
    			$storedpay_trade_count = $v['storedpay_trade_count'];
    			$storedpay_yesterday_trade_money = $v['storedpay_yesterday_trade_money'];
    			$storedpay_yesterday_receipt_money = $v['storedpay_yesterday_receipt_money'];
    			$storedpay_yesterday_trade_count = $v['storedpay_yesterday_trade_count'];
    			 
    			//如果已统计之前的数据，则加上之前的统计数据
    			if (!empty($m_statistics)) {
    				//查询该门店的最新的统计记录
    				$criteria = new CDbCriteria();
    				$criteria->addCondition('store_id = :store_id');
    				$criteria->params[':store_id'] = $store_id;
    				$criteria->addCondition('flag = :flag');
    				$criteria->params[':flag'] = FLAG_NO;
    				$criteria->order = 'create_time desc';
    				$s_statistics = SStatistics::model()->find($criteria);
    
    				$total_trade_money += $s_statistics['total_trade_money'];
    				$total_receipt_money += $s_statistics['total_trade_actual_money'];
    				$total_trade_count += $s_statistics['total_trade_num'];
    				$total_yesterday_trade_money += $s_statistics['new_trade_money'];
    				$total_yesterday_receipt_money += $s_statistics['new_trade_actual_money'];
    				$total_yesterday_trade_count += $s_statistics['new_trade_num'];
    				$alipay_trade_money += $s_statistics['total_trade_alipay_money'];
    				$alipay_receipt_money += $s_statistics['total_trade_actual_alipay_money'];
    				$alipay_trade_count += $s_statistics['total_trade_alipay_num'];
    				$alipay_yesterday_trade_money += $s_statistics['new_trade_alipay_money'];
    				$alipay_yesterday_receipt_money += $s_statistics['new_trade_actual_alipay_money'];
    				$alipay_yesterday_trade_count += $s_statistics['new_trade_alipay_num'];
    				$wxpay_trade_money += $s_statistics['total_trade_wechat_money'];
    				$wxpay_receipt_money += $s_statistics['total_trade_actual_wechat_money'];
    				$wxpay_trade_count += $s_statistics['total_trade_wechat_num'];
    				$wxpay_yesterday_trade_money += $s_statistics['new_trade_wechat_money'];
    				$wxpay_yesterday_receipt_money += $s_statistics['new_trade_actual_wechat_money'];
    				$wxpay_yesterday_trade_count += $s_statistics['new_trade_wechat_num'];
    				$cashpay_trade_money += $s_statistics['total_trade_cash_money'];
    				$cashpay_receipt_money += $s_statistics['total_trade_actual_cash_money'];
    				$cashpay_trade_count += $s_statistics['total_trade_cash_num'];
    				$cashpay_yesterday_trade_money += $s_statistics['new_trade_cash_money'];
    				$cashpay_yesterday_receipt_money += $s_statistics['new_trade_actual_cash_money'];
    				$cashpay_yesterday_trade_count += $s_statistics['new_trade_cash_num'];
    				$unionpay_trade_money += $s_statistics['total_trade_unionpay_money'];
    				$unionpay_receipt_money += $s_statistics['total_trade_actual_unionpay_money'];
    				$unionpay_trade_count += $s_statistics['total_trade_unionpay_num'];
    				$unionpay_yesterday_trade_money += $s_statistics['new_trade_unionpay_money'];
    				$unionpay_yesterday_receipt_money += $s_statistics['new_trade_actual_unionpay_money'];
    				$unionpay_yesterday_trade_count += $s_statistics['new_trade_unionpay_num'];
    				$storedpay_trade_money += $s_statistics['total_trade_stored_money'];
    				$storedpay_receipt_money += $s_statistics['total_trade_actual_stored_money'];
    				$storedpay_trade_count += $s_statistics['total_trade_stored_num'];
    				$storedpay_yesterday_trade_money += $s_statistics['new_trade_stored_money'];
    				$storedpay_yesterday_receipt_money += $s_statistics['new_trade_actual_stored_money'];
    				$storedpay_yesterday_trade_count += $s_statistics['new_trade_stored_num'];
    			}
    			//保存门店统计数据
    			$model = new SStatistics();
    			$model['create_time'] = date('Y-m-d H:i:s');
    			$model['store_id'] = $store_id;
    			$model['date'] = $end_time;
    			 
    			$model['total_trade_money'] = $total_trade_money;
    			$model['total_trade_actual_money'] = $total_receipt_money;
    			$model['total_trade_num'] = $total_trade_count;
    			$model['new_trade_money'] = $total_yesterday_trade_money;
    			$model['new_trade_actual_money'] = $total_yesterday_receipt_money;
    			$model['new_trade_num'] = $total_yesterday_trade_count;
    			 
    			$model['total_trade_alipay_money'] = $alipay_trade_money;
    			$model['total_trade_actual_alipay_money'] = $alipay_receipt_money;
    			$model['total_trade_alipay_num'] = $alipay_trade_count;
    			$model['new_trade_alipay_money'] = $alipay_yesterday_trade_money;
    			$model['new_trade_actual_alipay_money'] = $alipay_yesterday_receipt_money;
    			$model['new_trade_alipay_num'] = $alipay_yesterday_trade_count;
    			 
    			$model['total_trade_wechat_money'] = $wxpay_trade_money;
    			$model['total_trade_actual_wechat_money'] = $wxpay_receipt_money;
    			$model['total_trade_wechat_num'] = $wxpay_trade_count;
    			$model['new_trade_wechat_money'] = $wxpay_yesterday_trade_money;
    			$model['new_trade_actual_wechat_money'] = $wxpay_yesterday_receipt_money;
    			$model['new_trade_wechat_num'] = $wxpay_yesterday_trade_count;
    			 
    			$model['total_trade_cash_money'] = $cashpay_trade_money;
    			$model['total_trade_actual_cash_money'] = $cashpay_receipt_money;
    			$model['total_trade_cash_num'] = $cashpay_trade_count;
    			$model['new_trade_cash_money'] = $cashpay_yesterday_trade_money;
    			$model['new_trade_actual_cash_money'] = $cashpay_yesterday_receipt_money;
    			$model['new_trade_cash_num'] = $cashpay_yesterday_trade_count;
    			 
    			$model['total_trade_unionpay_money'] = $unionpay_trade_money;
    			$model['total_trade_actual_unionpay_money'] = $unionpay_receipt_money;
    			$model['total_trade_unionpay_num'] = $unionpay_trade_count;
    			$model['new_trade_unionpay_money'] = $unionpay_yesterday_trade_money;
    			$model['new_trade_actual_unionpay_money'] = $unionpay_yesterday_receipt_money;
    			$model['new_trade_unionpay_num'] = $unionpay_yesterday_trade_count;
    			 
    			$model['total_trade_stored_money'] = $storedpay_trade_money;
    			$model['total_trade_actual_stored_money'] = $storedpay_receipt_money;
    			$model['total_trade_stored_num'] = $storedpay_trade_count;
    			$model['new_trade_stored_money'] = $storedpay_yesterday_trade_money;
    			$model['new_trade_actual_stored_money'] = $storedpay_yesterday_receipt_money;
    			$model['new_trade_stored_num'] = $storedpay_yesterday_trade_count;
    			 
    			if (!$model->save()) {
    				throw new Exception('门店统计数据保存失败');
    			}
    			 
    			//统计全部门店总和
    			$m_model['total_trade_money'] += $total_trade_money;
    			$m_model['total_trade_actual_money'] += $total_receipt_money;
    			$m_model['total_trade_num'] += $total_trade_count;
    			$m_model['new_trade_money'] += $total_yesterday_trade_money;
    			$m_model['new_trade_actual_money'] += $total_yesterday_receipt_money;
    			$m_model['new_trade_num'] += $total_yesterday_trade_count;
    			 
    			$m_model['total_trade_alipay_money'] += $alipay_trade_money;
    			$m_model['total_trade_actual_alipay_money'] += $alipay_receipt_money;
    			$m_model['total_trade_alipay_num'] += $alipay_trade_count;
    			$m_model['new_trade_alipay_money'] += $alipay_yesterday_trade_money;
    			$m_model['new_trade_actual_alipay_money'] += $alipay_yesterday_receipt_money;
    			$m_model['new_trade_alipay_num'] += $alipay_yesterday_trade_count;
    			 
    			$m_model['total_trade_wechat_money'] += $wxpay_trade_money;
    			$m_model['total_trade_actual_wechat_money'] += $wxpay_receipt_money;
    			$m_model['total_trade_wechat_num'] += $wxpay_trade_count;
    			$m_model['new_trade_wechat_money'] += $wxpay_yesterday_trade_money;
    			$m_model['new_trade_actual_wechat_money'] = $wxpay_yesterday_receipt_money;
    			$m_model['new_trade_wechat_num'] += $wxpay_yesterday_trade_count;
    			 
    			$m_model['total_trade_cash_money'] += $cashpay_trade_money;
    			$m_model['total_trade_actual_cash_money'] += $cashpay_receipt_money;
    			$m_model['total_trade_cash_num'] += $cashpay_trade_count;
    			$m_model['new_trade_cash_money'] += $cashpay_yesterday_trade_money;
    			$m_model['new_trade_actual_cash_money'] += $cashpay_yesterday_receipt_money;
    			$m_model['new_trade_cash_num'] += $cashpay_yesterday_trade_count;
    			 
    			$m_model['total_trade_unionpay_money'] += $unionpay_trade_money;
    			$m_model['total_trade_actual_unionpay_money'] += $unionpay_receipt_money;
    			$m_model['total_trade_unionpay_num'] += $unionpay_trade_count;
    			$m_model['new_trade_unionpay_money'] += $unionpay_yesterday_trade_money;
    			$m_model['new_trade_actual_unionpay_money'] += $unionpay_yesterday_receipt_money;
    			$m_model['new_trade_unionpay_num'] += $unionpay_yesterday_trade_count;
    			 
    			$m_model['total_trade_stored_money'] += $storedpay_trade_money;
    			$m_model['total_trade_actual_stored_money'] += $storedpay_receipt_money;
    			$m_model['total_trade_stored_num'] += $storedpay_trade_count;
    			$m_model['new_trade_stored_money'] += $storedpay_yesterday_trade_money;
    			$m_model['new_trade_actual_stored_money'] += $storedpay_yesterday_receipt_money;
    			$m_model['new_trade_stored_num'] += $storedpay_yesterday_trade_count;
    		}
    
    		//统计商户客户数
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('merchant_id = :merchant_id');
    		$cmd->params[':merchant_id'] = $merchant_id;
    		//分组
    		$cmd->group = 'type';
    		//指定查询表
    		$cmd->from = 'wq_user';
    		//查询
    		$select = 'COUNT(*) AS user_count';
    		$cmd->select = $select;
    
    		//设置多个查询语句
    		$cmd1 = clone $cmd; //全部会员
    		$cmd2 = clone $cmd; //昨日会员
    		$cmd3 = clone $cmd; //支付宝粉丝
    		$cmd4 = clone $cmd; //昨日支付宝粉丝
    		$cmd5 = clone $cmd; //微信粉丝
    		$cmd6 = clone $cmd; //昨日微信粉丝
    		$cmd7 = clone $cmd; //全部客户（包括会员、支付宝微信粉丝）
    		$cmd8 = clone $cmd; //昨日客户（包括会员、支付宝微信粉丝）
    
    		//cmd1筛选全部会员
    		$cmd1->andWhere('type = :type');
    		$cmd1->params[':type'] = USER_TYPE_WANQUAN_MEMBER;
    
    		//cmd2筛选昨日会员
    		$cmd2->andWhere('type = :type');
    		$cmd2->params[':type'] = USER_TYPE_WANQUAN_MEMBER;
    		$cmd2->andWhere('create_time >= :create_time');
    		$cmd2->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    
    		//cmd3筛选支付宝粉丝（已关注的）
    		$cmd3->andWhere('type = :type');
    		$cmd3->params[':type'] = USER_TYPE_ALIPAY_FANS;
    		$cmd3->andWhere('alipay_status = :alipay_status');
    		$cmd3->params[':alipay_status'] = ALIPAY_USER_SUBSCRIBE;
    
    		//cmd4筛选昨日支付宝粉丝（已关注的）
    		$cmd4->andWhere('type = :type');
    		$cmd4->params[':type'] = USER_TYPE_ALIPAY_FANS;
    		$cmd4->andWhere('alipay_status = :alipay_status');
    		$cmd4->params[':alipay_status'] = ALIPAY_USER_SUBSCRIBE;
    		$cmd4->andWhere('create_time >= :create_time');
    		$cmd4->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    
    		//cmd5筛选微信粉丝（已关注的）
    		$cmd5->andWhere('type = :type');
    		$cmd5->params[':type'] = USER_TYPE_WECHAT_FANS;
    		$cmd5->andWhere('wechat_status = :wechat_status');
    		$cmd5->params[':wechat_status'] = WECHAT_USER_SUBSCRIBE;
    
    		//cmd6筛选昨日微信粉丝（已关注的）
    		$cmd6->andWhere('type = :type');
    		$cmd6->params[':type'] = USER_TYPE_WECHAT_FANS;
    		$cmd6->andWhere('wechat_status = :wechat_status');
    		$cmd6->params[':wechat_status'] = WECHAT_USER_SUBSCRIBE;
    		$cmd6->andWhere('create_time >= :create_time');
    		$cmd6->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    
    		//cmd8筛选昨日客户
    		$cmd8->andWhere('create_time >= :create_time');
    		$cmd8->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    
    		//执行sql
    		$member_all_num = $cmd1->queryRow();
    		$member_yesterday_num = $cmd2->queryRow();
    		$alipay_all_num = $cmd3->queryRow();
    		$alipay_yesterday_num = $cmd4->queryRow();
    		$wxpay_all_num = $cmd5->queryRow();
    		$wxpay_yesterday_num = $cmd6->queryRow();
    		$user_all_num = $cmd7->queryRow();
    		$user_yesterday_num = $cmd8->queryRow();
    
    		//保存
    		$m_model['total_user_num'] = $user_all_num['user_count'];
    		$m_model['total_alipayfans_num'] = $alipay_all_num['user_count'];
    		$m_model['total_wechatfans_num'] = $wxpay_all_num['user_count'];
    		$m_model['total_member_num'] = $member_all_num['user_count'];
    		$m_model['new_user_num'] = $user_yesterday_num['user_count'];
    		$m_model['new_alipayfans_num'] = $alipay_yesterday_num['user_count'];
    		$m_model['new_wechatfans_num'] = $wxpay_yesterday_num['user_count'];
    		$m_model['new_member_num'] = $member_yesterday_num['user_count'];
    
    		$m_model['merchant_id'] = $merchant_id;
    		$m_model['date'] = $end_time;
    		$m_model['create_time'] = date('Y-m-d H:i:s');
    
    		if (!$m_model->save()) {
    			throw new Exception('商户统计数据保存失败');
    		}
    
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    		$transaction -> commit();
    	} catch (Exception $e) {
    		$transaction -> rollback();
    		$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
    		$result['errMsg'] = $e -> getMessage();
    	}
    	return json_encode($result);
    }
    
    public function getStatisticsByToday($merchant_id, $store_id, $withAlipay=FALSE, $withWxpay=FALSE, $withUnionpay=FALSE, $withCashpay=FALSE, $withStoredpay=FALSE) {
    	$result = array();
    	$transaction = Yii::app()->db->beginTransaction();
    	try {
    		if (empty($merchant_id) && empty($store_id)) {
    			throw new Exception('商户id或门店id不能同时为空');
    		}
    
    		$store_arr = array(); //待查询门店id的数组
    		if (!empty($store_id)) {
    			//查询该门店数据
    			$store_arr[] = $store_id;
    		}else {
    			//查询该商户数据
    			 
    		}
    
    		//统计的开始时间：今日0点
    		$start_time = date('Y-m-d 00:00:00');
    		//统计的结束时间：今日24点
    		$end_time = date('Y-m-d 23:59:59');
    
    	} catch (Exception $e) {
    		$transaction -> rollback();
    		$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
    		$result['errMsg'] = $e -> getMessage();
    	}
    	 
    	return json_encode($result);
    }
    
    /**
     * 统计门店数据并记录
     * @param unknown $merchant_id
     * @throws Exception
     * @return string
     */
    public function saveStoreStatistics($merchant_id) {
    	$result = array();
    	try {
    		//统计的开始时间
    		$start_time = '';
    		//统计的结束时间：昨日的时间
    		$end_time = date('Y-m-d 23:59:59', strtotime("-1 day"));
    		$yesterday = strtotime($end_time);
    	
    		//查询最新的统计数据
    		$criteria = new CDbCriteria();
    		$criteria->addCondition('merchant_id = :merchant_id');
    		$criteria->params[':merchant_id'] = $merchant_id;
    		$criteria->addCondition('flag = :flag');
    		$criteria->params[':flag'] = FLAG_NO;
    		$criteria->order = 'create_time desc';
    		$m_statistics = MStatistics::model()->find($criteria);
    		if (!empty($m_statistics)) {
    			$last_time = $m_statistics['date'];
    			//昨日的数据已经统计
    			if (strtotime($last_time) == $yesterday) {
    				throw new Exception('昨日数据已统计');
    			}
    			$start_time = date('Y-m-d 00:00:00', strtotime("+1 day", $last_time));
    		}
    		
    		//获取该商户下所有门店并初始化交易数组
    		$stores = Store::model()->findAll('merchant_id = :merchant_id AND flag = :flag',
    				array(':merchant_id' => $merchant_id, ':flag' => FLAG_NO));
    		$data = array();
    		foreach ($stores as $k => $v) {
    			$store_id = $v['id'];
    			$data[$store_id] = array(
    					'total_trade_money' => 0, //交易金额
    					'total_receipt_money' => 0, //实收金额
    					'total_refund_money' => 0, //退款金额
    					'total_yesterday_trade_money' => 0, //昨日交易金额
    					'total_yesterday_receipt_money' => 0, //昨日实收金额
    					'total_yesterday_refund_money' => 0, //昨日退款金额
    					'total_trade_count' => 0, //交易笔数
    					'total_refund_count' => 0, //退款笔数
    					'total_yesterday_trade_count' => 0, //昨日交易笔数
    					'total_yesterday_refund_count' => 0, //昨日退款笔数
    					'alipay_trade_money' => 0, //支付宝交易金额
    					'alipay_receipt_money' => 0, //支付宝实收金额
    					'alipay_refund_money' => 0, //支付宝退款金额
    					'alipay_yesterday_trade_money' => 0, //支付宝昨日交易金额
    					'alipay_yesterday_receipt_money' => 0, //支付宝昨日实收金额
    					'alipay_yesterday_refund_money' => 0, //支付宝昨日退款金额
    					'alipay_trade_count' => 0, //支付宝交易笔数
    					'alipay_refund_count' => 0, //支付宝退款笔数
    					'alipay_yesterday_trade_count' => 0, //支付宝昨日交易笔数
    					'alipay_yesterday_refund_count' => 0, //支付宝昨日退款笔数
    					'wxpay_trade_money' => 0, //微信交易金额
    					'wxpay_receipt_money' => 0, //微信实收金额
    					'wxpay_refund_money' => 0, //微信退款金额
    					'wxpay_yesterday_trade_money' => 0, //微信昨日交易金额
    					'wxpay_yesterday_receipt_money' => 0, //微信昨日实收金额
    					'wxpay_yesterday_refund_money' => 0, //微信昨日退款金额
    					'wxpay_trade_count' => 0, //微信交易笔数
    					'wxpay_refund_count' => 0, //微信退款笔数
    					'wxpay_yesterday_trade_count' => 0, //微信昨日交易笔数
    					'wxpay_yesterday_refund_count' => 0, //微信昨日退款笔数
    					'cashpay_trade_money' => 0, //现金交易金额
    					'cashpay_receipt_money' => 0, //现金实收金额
    					'cashpay_refund_money' => 0, //现金退款金额
    					'cashpay_yesterday_trade_money' => 0, //现金昨日交易金额
    					'cashpay_yesterday_receipt_money' => 0, //现金昨日实收金额
    					'cashpay_yesterday_refund_money' => 0, //现金昨日退款金额
    					'cashpay_trade_count' => 0, //现金交易笔数
    					'cashpay_refund_count' => 0, //现金退款笔数
    					'cashpay_yesterday_trade_count' => 0, //现金昨日交易笔数
    					'cashpay_yesterday_refund_count' => 0, //现金昨日退款笔数
    					'unionpay_trade_money' => 0, //银联支付交易金额
    					'unionpay_receipt_money' => 0, //银联支付实收金额
    					'unionpay_refund_money' => 0, //银联支付退款金额
    					'unionpay_yesterday_trade_money' => 0, //银联支付昨日交易金额
    					'unionpay_yesterday_receipt_money' => 0, //银联支付昨日实收金额
    					'unionpay_yesterday_refund_money' => 0, //银联支付昨日退款金额
    					'unionpay_trade_count' => 0, //银联支付交易笔数
    					'unionpay_refund_count' => 0, //银联支付退款笔数
    					'unionpay_yesterday_trade_count' => 0, //银联支付昨日交易笔数
    					'unionpay_yesterday_refund_count' => 0, //银联支付昨日退款笔数
    					'storedpay_trade_money' => 0, //储值支付交易金额
    					'storedpay_receipt_money' => 0, //储值支付实收金额
    					'storedpay_refund_money' => 0, //储值支付退款金额
    					'storedpay_yesterday_trade_money' => 0, //储值支付昨日交易金额
    					'storedpay_yesterday_receipt_money' => 0, //储值支付昨日实收金额
    					'storedpay_yesterday_refund_money' => 0, //储值支付昨日退款金额
    					'storedpay_trade_count' => 0, //储值支付交易笔数
    					'storedpay_refund_count' => 0, //储值支付退款笔数
    					'storedpay_yesterday_trade_count' => 0, //储值支付昨日交易笔数
    					'storedpay_yesterday_refund_count' => 0, //储值支付昨日退款笔数
    					'total_coupons_count' => 0, //累计卡券核销笔数
    					'alipay_commision_money' => 0, //累计支付宝符合返佣条件金额
    					'alipay_commision_count' => 0, //累计支付宝符合返佣条件笔数
    					'wxpay_commision_money' => 0, //累计微信符合返佣条件金额
    					'wxpay_commision_count' => 0, //累计微信符合返佣条件笔数
    					'yesterday_coupons_count' => 0, //昨日卡券核销笔数
    					'alipay_yesterday_commision_money' => 0, //支付宝昨日符合返佣条件金额
    					'alipay_yesterday_commision_count' => 0, //支付宝昨日符合返佣条件笔数
    					'wxpay_yesterday_commision_money' => 0, //微信昨日符合返佣条件金额
    					'wxpay_yesterday_commision_count' => 0, //微信昨日符合返佣条件笔数
    			);
    		}
    	
    		//获取门店交易数据
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('pay_status = :custom_pay_status');
    		$cmd->params[':custom_pay_status'] = ORDER_STATUS_PAID; //已支付订单
    		$cmd->andWhere('order_type = :custom_order_type');
    		$cmd->params[':custom_order_type'] = ORDER_TYPE_CASHIER; //收银订单
    		$cmd->andWhere('merchant_id = :merchant_id');
    		$cmd->params[':merchant_id'] = $merchant_id;
    		if (!empty($start_time)) {
    			$cmd->andWhere('pay_time >= :start_time');
    			$cmd->params[':start_time'] = $start_time;
    		}
    		$cmd->andWhere('pay_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		
    		//分组
    		$cmd->group = 'store_id, pay_channel';
    		//指定查询表
    		$cmd->from = 'wq_order';
	    	
    		//累计订单数据统计
    		$cmd1 = clone $cmd; //深拷贝
    		//累计卡券核销笔数查询
    		$cmd2 = clone $cmd; //深拷贝
    		//累计返佣数据查询
    		$cmd3 = clone $cmd; //深拷贝
    		//昨日订单数据统计
    		$cmd4 = clone $cmd; //深拷贝
    		//昨日卡券核销笔数查询
    		$cmd5 = clone $cmd; //深拷贝
    		//昨日返佣数据查询
    		$cmd6 = clone $cmd; //深拷贝
    	
    		//查询计算1
    		$select1 = 'store_id, pay_channel';
    		$select1 .= ', SUM(order_paymoney) AS order_sum';
    		$select1 .= ', SUM(coupons_money) AS coupons_sum';
    		$select1 .= ', SUM(discount_money) AS discount_sum';
    		$select1 .= ', SUM(merchant_discount_money) AS m_discount_sum';
    		$select1 .= ', COUNT(*) AS trade_sum';
    		$cmd1->select = $select1;
    		
    		//查询条件2
    		$cmd2->andWhere('if_use_coupons = :if_use_coupons');
    		$cmd2->params[':if_use_coupons'] = ORDER_IF_USE_COUPONS_YES;
    		//查询计算2
    		$select2 = 'store_id';
    		$select2 .= ', COUNT(*) AS coupons_sum';
    		$cmd2->select = $select2;
    		//分组2
    		$cmd2->group = 'store_id';
    		
    		//查询条件3
    		$cmd3->andWhere('pay_passageway != :pay_passageway');
    		$cmd3->params[':pay_passageway'] = ORDER_PAY_PASSAGEWAY_NULL;
    		//查询3
    		$select3 = 'store_id, pay_passageway, commission_ratio';
    		$select3 .= ', SUM(order_paymoney) AS order_sum';
    		$select3 .= ', COUNT(*) AS trade_sum';
    		$cmd3->select = $select3;
    		//分组3
    		$cmd3->group = 'store_id, pay_passageway';
    		
    		//查询条件4
    		$cmd4->andWhere('pay_time >= :start_time1');
    		$cmd4->params[':start_time1'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		//查询4
    		$select4 = 'store_id, pay_channel';
    		$select4 .= ', SUM(order_paymoney) AS order_sum';
    		$select4 .= ', SUM(coupons_money) AS coupons_sum';
    		$select4 .= ', SUM(discount_money) AS discount_sum';
    		$select4 .= ', SUM(merchant_discount_money) AS m_discount_sum';
    		$select4 .= ', COUNT(*) AS trade_sum';
    		$cmd4->select = $select4;
    		
    		//查询条件5
    		$cmd5->andWhere('if_use_coupons = :if_use_coupons');
    		$cmd5->params[':if_use_coupons'] = ORDER_IF_USE_COUPONS_YES;
    		$cmd5->andWhere('pay_time >= :start_time1');
    		$cmd5->params[':start_time1'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		//查询计算5
    		$select5 = 'store_id';
    		$select5 .= ', COUNT(*) AS coupons_sum';
    		$cmd5->select = $select5;
    		//分组5
    		$cmd5->group = 'store_id';
    		
    		//查询条件6
    		$cmd6->andWhere('pay_time >= :start_time1');
    		$cmd6->params[':start_time1'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		$cmd6->andWhere('pay_passageway != :pay_passageway');
    		$cmd6->params[':pay_passageway'] = ORDER_PAY_PASSAGEWAY_NULL;
    		//查询6
    		$select6 = 'store_id, pay_passageway, commission_ratio';
    		$select6 .= ', SUM(order_paymoney) AS order_sum';
    		$select6 .= ', COUNT(*) AS trade_sum';
    		$cmd6->select = $select6;
    		//分组6
    		$cmd6->group = 'store_id, pay_passageway';
    		
    		//执行sql查询:统计订单金额，优惠金额，交易笔数
    		$list1 = $cmd1->queryAll();
    		//执行sql查询:统计累计核销数据
    		$list2 = $cmd2->queryAll();
    		//执行sql查询:统计累计符合返佣的订单金额，订单数
    		$list3 = $cmd3->queryAll();
    		//执行sql查询:统计昨日订单金额，优惠金额，交易笔数
    		$list4 = $cmd4->queryAll();
    		//执行sql查询:统计昨日核销数据
    		$list5 = $cmd5->queryAll();
    		//执行sql查询:统计昨日符合返佣的订单金额，订单数
    		$list6 = $cmd6->queryAll();
    		
    		
    		//退款查询
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('type = :type');
    		$cmd->params[':type'] = REFUND_TYPE_REFUND;
    		$cmd->andWhere('status != :status');
    		$cmd->params[':status'] = REFUND_STATUS_FAIL;
    		$cmd->andWhere('merchant_id = :merchant_id');
    		$cmd->params[':merchant_id'] = $merchant_id;
    		$cmd->andWhere('store_id IS NOT NULL');
    		if (!empty($start_time)) {
    			$cmd->andWhere('refund_time >= :start_time');
    			$cmd->params[':start_time'] = $start_time;
    		}
    		$cmd->andWhere('refund_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		
    		//分组
    		$cmd->group = 'store_id, refund_channel';
    		//指定查询表
    		$cmd->from = 'wq_refund_record';
    		
    		//商户全部退款查询
    		$cmd7 = clone $cmd; //深拷贝
    		//商户昨日退款查询
    		$cmd8 = clone $cmd; //深拷贝
    		
    		//查询计算7
    		$select7 = 'store_id, refund_channel';
    		$select7 .= ', SUM(refund_money) AS refund_sum';
    		$select7 .= ', COUNT(*) AS record_sum';
    		$cmd7->select = $select7;
    		
    		//查询条件8
    		$cmd8->andWhere('refund_time >= :start_time1');
    		$cmd8->params[':start_time1'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		//查询8
    		$select8 = 'store_id, refund_channel';
    		$select8 .= ', SUM(refund_money) AS refund_sum';
    		$select8 .= ', COUNT(*) AS trade_sum';
    		$cmd8->select = $select8;
    		
    		//执行sql查询:统计退款金额，退款笔数
    		$list7 = $cmd7->queryAll();
    		//执行sql查询:统计昨日退款金额，退款笔数
    		$list8 = $cmd8->queryAll();
    	
    		foreach ($list1 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$order_money = $v['order_sum']; //订单总金额
    			$coupons_discount = $v['coupons_sum']; //优惠券优惠金额
    			$member_discount = $v['discount_sum']; //会员优惠
    			$merchant_discount = $v['m_discount_sum']; //商家优惠
    			$trade_count = $v['trade_sum']; //交易笔数
    	
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    			//优惠金额
    			$discount_money = $coupons_discount + $member_discount + $merchant_discount;
    			//实收金额
    			$receipt_money = $order_money - $discount_money;
    			//计算交易金额和实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data[$store_id]['alipay_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data[$store_id]['alipay_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data[$store_id]['wxpay_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data[$store_id]['wxpay_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data[$store_id]['cashpay_trade_money'] += $order_money;
    					//$data[$store_id]['cashpay_discount_money'] += $discount_money;
    					$data[$store_id]['cashpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['cashpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data[$store_id]['unionpay_trade_money'] += $order_money;
    					//$data[$store_id]['unionpay_discount_money'] += $discount_money;
    					$data[$store_id]['unionpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['unionpay_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data[$store_id]['storedpay_trade_money'] += $order_money;
    					//$data[$store_id]['storedpay_discount_money'] += $discount_money;
    					$data[$store_id]['storedpay_receipt_money'] += $receipt_money;
    					$data[$store_id]['storedpay_trade_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data[$store_id]['total_trade_money'] += $order_money;
    			//$data[$store_id]['total_discount_money'] += $discount_money;
    			$data[$store_id]['total_receipt_money'] += $receipt_money;
    			$data[$store_id]['total_trade_count'] += $trade_count;
    		}
    		
    		foreach ($list2 as $k => $v) {
    			$store_id = $v['store_id'];
    			$coupons_count = $v['coupons_sum']; //卡券核销笔数
    			
    			$data[$store_id]['total_coupons_count'] += $coupons_count;
    		}
    		
    		foreach ($list3 as $k => $v) {
    			$store_id = $v['store_id'];
    			$passageway = $v['pay_passageway'];
    			$ratio = $v['commission_ratio'];
    			$order_money = $v['order_sum'];
    			$trade_count = $v['trade_sum'];
    			 
    			if ($passageway != ORDER_PAY_PASSAGEWAY_ALIPAY1 &&
    				$passageway != ORDER_PAY_PASSAGEWAY_ALIPAY2 &&
    				$passageway != ORDER_PAY_PASSAGEWAY_WECHAT1 &&
    				$passageway != ORDER_PAY_PASSAGEWAY_WECHAT2) {
    				continue;
    			}
    			 
    			if (($passageway == ORDER_PAY_PASSAGEWAY_ALIPAY1 || $passageway == ORDER_PAY_PASSAGEWAY_ALIPAY2) && $ratio <= 0) {
    				continue;
    			}
    			 
    			switch ($passageway) {
    				case ORDER_PAY_PASSAGEWAY_ALIPAY1:
    					$data[$store_id]['alipay_commision_money'] += $order_money;
    					$data[$store_id]['alipay_commision_count'] += $trade_count;
    					break;
    				case ORDER_PAY_PASSAGEWAY_ALIPAY2:
    					$data[$store_id]['alipay_commision_money'] += $order_money;
    					$data[$store_id]['alipay_commision_count'] += $trade_count;
    					break;
    				case ORDER_PAY_PASSAGEWAY_WECHAT2:
    					$data[$store_id]['wxpay_commision_money'] += $order_money;
    					$data[$store_id]['wxpay_commision_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    		}
    		
    		foreach ($list4 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$order_money = $v['order_sum']; //订单总金额
    			$coupons_discount = $v['coupons_sum']; //优惠券优惠金额
    			$member_discount = $v['discount_sum']; //会员优惠
    			$merchant_discount = $v['m_discount_sum']; //商家优惠
    			$trade_count = $v['trade_sum']; //交易笔数
    			 
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    			//优惠金额
    			$discount_money = $coupons_discount + $member_discount + $merchant_discount;
    			//实收金额
    			$receipt_money = $order_money - $discount_money;
    			//计算交易金额和实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data[$store_id]['alipay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data[$store_id]['alipay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['alipay_discount_money'] += $discount_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['alipay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data[$store_id]['wxpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data[$store_id]['wxpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['wxpay_discount_money'] += $discount_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['wxpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data[$store_id]['cashpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['cashpay_discount_money'] += $discount_money;
    					$data[$store_id]['cashpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['cashpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data[$store_id]['unionpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['unionpay_discount_money'] += $discount_money;
    					$data[$store_id]['unionpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['unionpay_yesterday_trade_count'] += $trade_count;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data[$store_id]['storedpay_yesterday_trade_money'] += $order_money;
    					//$data[$store_id]['storedpay_discount_money'] += $discount_money;
    					$data[$store_id]['storedpay_yesterday_receipt_money'] += $receipt_money;
    					$data[$store_id]['storedpay_yesterday_trade_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data[$store_id]['total_yesterday_trade_money'] += $order_money;
    			//$data[$store_id]['total_discount_money'] += $discount_money;
    			$data[$store_id]['total_yesterday_receipt_money'] += $receipt_money;
    			$data[$store_id]['total_yesterday_trade_count'] += $trade_count;
    		}
    		
    		foreach ($list5 as $k => $v) {
    			$store_id = $v['store_id'];
    			$coupons_count = $v['coupons_sum']; //卡券核销笔数
    			 
    			$data[$store_id]['yesterday_coupons_count'] += $coupons_count;
    		}
    		
    		foreach ($list6 as $k => $v) {
    			$store_id = $v['store_id'];
    			$passageway = $v['pay_passageway'];
    			$ratio = $v['commission_ratio'];
    			$order_money = $v['order_sum'];
    			$trade_count = $v['trade_sum'];
    		
    			if ($passageway != ORDER_PAY_PASSAGEWAY_ALIPAY1 &&
    				$passageway != ORDER_PAY_PASSAGEWAY_ALIPAY2 &&
    				$passageway != ORDER_PAY_PASSAGEWAY_WECHAT1 &&
    				$passageway != ORDER_PAY_PASSAGEWAY_WECHAT2) {
    				continue;
    			}
    		
    			if (($passageway == ORDER_PAY_PASSAGEWAY_ALIPAY1 || $passageway == ORDER_PAY_PASSAGEWAY_ALIPAY2) && $ratio <= 0) {
    				continue;
    			}
    		
    			switch ($passageway) {
    				case ORDER_PAY_PASSAGEWAY_ALIPAY1:
    					$data[$store_id]['alipay_yesterday_commision_money'] += $order_money;
    					$data[$store_id]['alipay_yesterday_commision_count'] += $trade_count;
    					break;
    				case ORDER_PAY_PASSAGEWAY_ALIPAY2:
    					$data[$store_id]['alipay_yesterday_commision_money'] += $order_money;
    					$data[$store_id]['alipay_yesterday_commision_count'] += $trade_count;
    					break;
    				case ORDER_PAY_PASSAGEWAY_WECHAT2:
    					$data[$store_id]['wxpay_yesterday_commision_money'] += $order_money;
    					$data[$store_id]['wxpay_yesterday_commision_count'] += $trade_count;
    					break;
    				default:
    					break;
    			}
    		}
    	
    		foreach ($list7 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['refund_channel']; //支付方式
    			$refund_money = $v['refund_sum']; //退款金额
    			$refund_count = $v['record_sum']; //退款笔数
    	
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    	
    			//计算实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data[$store_id]['alipay_refund_count'] += $refund_count;
    					$data[$store_id]['alipay_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data[$store_id]['alipay_refund_count'] += $refund_count;
    					$data[$store_id]['alipay_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data[$store_id]['wxpay_refund_count'] += $refund_count;
    					$data[$store_id]['wxpay_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data[$store_id]['wxpay_refund_count'] += $refund_count;
    					$data[$store_id]['wxpay_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data[$store_id]['cashpay_refund_count'] += $refund_count;
    					$data[$store_id]['cashpay_refund_money'] += $refund_money;
    					$data[$store_id]['cashpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data[$store_id]['unionpay_refund_count'] += $refund_count;
    					$data[$store_id]['unionpay_refund_money'] += $refund_money;
    					$data[$store_id]['unionpay_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data[$store_id]['storedpay_refund_count'] += $refund_count;
    					$data[$store_id]['storedpay_refund_money'] += $refund_money;
    					$data[$store_id]['storedpay_receipt_money'] -= $refund_money;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data[$store_id]['total_refund_count'] += $refund_count;
    			$data[$store_id]['total_refund_money'] += $refund_money;
    			$data[$store_id]['total_receipt_money'] -= $refund_money;
    		}
    	
    		foreach ($list8 as $k => $v) {
    			$store_id = $v['store_id'];
    			$pay_channel = $v['pay_channel']; //支付方式
    			$refund_money = $v['refund_sum']; //退款金额
    			$refund_count = $v['record_sum']; //退款笔数
    	
    			if ($pay_channel != ORDER_PAY_CHANNEL_ALIPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_ALIPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_SM &&
    				$pay_channel != ORDER_PAY_CHANNEL_WXPAY_TM &&
    				$pay_channel != ORDER_PAY_CHANNEL_STORED &&
    				$pay_channel != ORDER_PAY_CHANNEL_CASH &&
    				$pay_channel != ORDER_PAY_CHANNEL_UNIONPAY) {
    				//非统计的支付数据
    				continue;
    			}
    	
    			//计算实收金额和交易笔数
    			switch ($pay_channel) {
    				case ORDER_PAY_CHANNEL_ALIPAY_SM:
    					$data[$store_id]['alipay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['alipay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_ALIPAY_TM:
    					$data[$store_id]['alipay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['alipay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['alipay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_SM:
    					$data[$store_id]['wxpay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['wxpay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_WXPAY_TM:
    					$data[$store_id]['wxpay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['wxpay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['wxpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_CASH:
    					$data[$store_id]['cashpay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['cashpay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['cashpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_UNIONPAY:
    					$data[$store_id]['unionpay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['unionpay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['unionpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				case ORDER_PAY_CHANNEL_STORED:
    					$data[$store_id]['storedpay_yesterday_refund_count'] += $refund_count;
    					$data[$store_id]['storedpay_yesterday_refund_money'] += $refund_money;
    					$data[$store_id]['storedpay_yesterday_receipt_money'] -= $refund_money;
    					break;
    				default:
    					break;
    			}
    			//总交易统计
    			$data[$store_id]['total_yesterday_refund_count'] += $refund_count;
    			$data[$store_id]['total_yesterday_refund_money'] += $refund_money;
    			$data[$store_id]['total_yesterday_receipt_money'] -= $refund_money;
    		}
    	
    		foreach ($data as $k => $v) {
    			//跳过错误数据
    			if (empty($k) || empty($v)) {
    				continue;
    			}
    			$store_id = $k;
    			$date = $end_time;
    			$total_trade_money = $v['total_trade_money'];
    			$total_receipt_money = $v['total_receipt_money'];
    			$total_refund_money = $v['total_refund_money'];
    			$total_trade_count = $v['total_trade_count'];
    			$total_refund_count = $v['total_refund_count'];
    			
    			$alipay_trade_money = $v['alipay_trade_money'];
    			$alipay_receipt_money = $v['alipay_receipt_money'];
    			$alipay_refund_money = $v['alipay_refund_money'];
    			$alipay_trade_count = $v['alipay_trade_count'];
    			$alipay_refund_count = $v['alipay_refund_count'];
    			
    			$wxpay_trade_money = $v['wxpay_trade_money'];
    			$wxpay_receipt_money = $v['wxpay_receipt_money'];
    			$wxpay_refund_money = $v['wxpay_refund_money'];
    			$wxpay_trade_count = $v['wxpay_trade_count'];
    			$wxpay_refund_count = $v['wxpay_refund_count'];
    			
    			$cashpay_trade_money = $v['cashpay_trade_money'];
    			$cashpay_receipt_money = $v['cashpay_receipt_money'];
    			$cashpay_refund_money = $v['cashpay_refund_money'];
    			$cashpay_trade_count = $v['cashpay_trade_count'];
    			$cashpay_refund_count = $v['cashpay_refund_count'];
    			
    			$unionpay_trade_money = $v['unionpay_trade_money'];
    			$unionpay_receipt_money = $v['unionpay_receipt_money'];
    			$unionpay_refund_money = $v['unionpay_refund_money'];
    			$unionpay_trade_count = $v['unionpay_trade_count'];
    			$unionpay_refund_count = $v['unionpay_refund_count'];
    			
    			$storedpay_trade_money = $v['storedpay_trade_money'];
    			$storedpay_receipt_money = $v['storedpay_receipt_money'];
    			$storedpay_refund_money = $v['storedpay_refund_money'];
    			$storedpay_trade_count = $v['storedpay_trade_count'];
    			$storedpay_refund_count = $v['storedpay_refund_count'];
    			
    			$total_coupons_count = $v['total_coupons_count'];
    			$alipay_commision_money = $v['alipay_commision_money'];
    			$alipay_commision_count = $v['alipay_commision_count'];
    			$wxpay_commision_money = $v['wxpay_commision_money'];
    			$wxpay_commision_count = $v['wxpay_commision_count'];
    	
    			//如果已统计之前的数据，则加上之前的统计数据
    			if (!empty($m_statistics)) {
    				//查询该门店的最新的统计记录
    				$criteria = new CDbCriteria();
    				$criteria->addCondition('store_id = :store_id');
    				$criteria->params[':store_id'] = $store_id;
    				$criteria->addCondition('flag = :flag');
    				$criteria->params[':flag'] = FLAG_NO;
    				$criteria->order = 'create_time desc';
    				$s_statistics = SStatistics::model()->find($criteria);
    	
    				$total_trade_money += $s_statistics['total_trade_money'];
    				$total_receipt_money += $s_statistics['total_trade_actual_money'];
    				$total_refund_money += 0;
    				$total_trade_count += $s_statistics['total_trade_num'];
    				$total_refund_count += 0;
    				
    				$alipay_trade_money += $s_statistics['total_trade_alipay_money'];
    				$alipay_receipt_money += $s_statistics['total_trade_actual_alipay_money'];
    				$alipay_refund_money += 0;
    				$alipay_trade_count += $s_statistics['total_trade_alipay_num'];
    				$alipay_refund_count += 0;
    				
    				$wxpay_trade_money += $s_statistics['total_trade_wechat_money'];
    				$wxpay_receipt_money += $s_statistics['total_trade_actual_wechat_money'];
    				$wxpay_refund_money += 0;
    				$wxpay_trade_count += $s_statistics['total_trade_wechat_num'];
    				$wxpay_refund_count += 0;
    				
    				$cashpay_trade_money += $s_statistics['total_trade_cash_money'];
    				$cashpay_receipt_money += $s_statistics['total_trade_actual_cash_money'];
    				$cashpay_refund_money += 0;
    				$cashpay_trade_count += $s_statistics['total_trade_cash_num'];
    				$cashpay_refund_count += 0;
    				
    				$unionpay_trade_money += $s_statistics['total_trade_unionpay_money'];
    				$unionpay_receipt_money += $s_statistics['total_trade_actual_unionpay_money'];
    				$unionpay_refund_money += 0;
    				$unionpay_trade_count += $s_statistics['total_trade_unionpay_num'];
    				$unionpay_refund_count += 0;
    				
    				$storedpay_trade_money += $s_statistics['total_trade_stored_money'];
    				$storedpay_receipt_money += $s_statistics['total_trade_actual_stored_money'];
    				$storedpay_refund_money += 0;
    				$storedpay_trade_count += $s_statistics['total_trade_stored_num'];
    				$storedpay_refund_count += 0;
    				
    				$total_coupons_count += $s_statistics['total_trade_coupon_num'];
    				$alipay_commision_money += $s_statistics['total_alipay_commision_money'];
    				$alipay_commision_count += $s_statistics['total_alipay_commision_num'];
    				$wxpay_commision_money += $s_statistics['total_wechat_commision_money'];
    				$wxpay_commision_count += $s_statistics['total_wechat_commision_num'];
    			}
    			//保存门店统计数据
    			$model = new SStatistics();
    			$model['create_time'] = date('Y-m-d H:i:s');
    			$model['merchant_id'] = $merchant_id;
    			$model['store_id'] = $store_id;
    			$model['date'] = $end_time;
    	
    			$model['total_trade_money'] = $total_trade_money;
    			$model['total_trade_actual_money'] = $total_receipt_money;
    			$model['total_trade_num'] = $total_trade_count;
    			$model['new_trade_money'] = $v['total_yesterday_trade_money'];
    			$model['new_trade_actual_money'] = $v['total_yesterday_receipt_money'];
    			$model['new_trade_num'] = $v['total_yesterday_trade_count'];
    			$model['new_trade_refund_money'] = $v['total_yesterday_refund_money'];
    			$model['new_trade_refund_num'] = $v['total_yesterday_refund_count'];
    	
    			$model['total_trade_alipay_money'] = $alipay_trade_money;
    			$model['total_trade_actual_alipay_money'] = $alipay_receipt_money;
    			$model['total_trade_alipay_num'] = $alipay_trade_count;
    			$model['new_trade_alipay_money'] = $v['alipay_yesterday_trade_money'];
    			$model['new_trade_actual_alipay_money'] = $v['alipay_yesterday_receipt_money'];
    			$model['new_trade_alipay_num'] = $v['alipay_yesterday_trade_count'];
    			$model['new_trade_alipay_refund_money'] = $v['alipay_yesterday_refund_money'];
    			$model['new_trade_alipay_refund_num'] = $v['alipay_yesterday_refund_count'];
    	
    			$model['total_trade_wechat_money'] = $wxpay_trade_money;
    			$model['total_trade_actual_wechat_money'] = $wxpay_receipt_money;
    			$model['total_trade_wechat_num'] = $wxpay_trade_count;
    			$model['new_trade_wechat_money'] = $v['wxpay_yesterday_trade_money'];
    			$model['new_trade_actual_wechat_money'] = $v['wxpay_yesterday_receipt_money'];
    			$model['new_trade_wechat_num'] = $v['wxpay_yesterday_trade_count'];
    			$model['new_trade_wechat_refund_money'] = $v['wxpay_yesterday_refund_money'];
    			$model['new_trade_wechat_refund_num'] = $v['wxpay_yesterday_refund_count'];
    	
    			$model['total_trade_cash_money'] = $cashpay_trade_money;
    			$model['total_trade_actual_cash_money'] = $cashpay_receipt_money;
    			$model['total_trade_cash_num'] = $cashpay_trade_count;
    			$model['new_trade_cash_money'] = $v['cashpay_yesterday_trade_money'];
    			$model['new_trade_actual_cash_money'] = $v['cashpay_yesterday_receipt_money'];
    			$model['new_trade_cash_num'] = $v['cashpay_yesterday_trade_count'];
    			$model['new_trade_cash_refund_money'] = $v['cashpay_yesterday_refund_money'];
    			$model['new_trade_cash_refund_num'] = $v['cashpay_yesterday_refund_count'];
    	
    			$model['total_trade_unionpay_money'] = $unionpay_trade_money;
    			$model['total_trade_actual_unionpay_money'] = $unionpay_receipt_money;
    			$model['total_trade_unionpay_num'] = $unionpay_trade_count;
    			$model['new_trade_unionpay_money'] = $v['unionpay_yesterday_trade_money'];
    			$model['new_trade_actual_unionpay_money'] = $v['unionpay_yesterday_receipt_money'];
    			$model['new_trade_unionpay_num'] = $v['unionpay_yesterday_trade_count'];
    			$model['new_trade_unionpay_refund_money'] = $v['unionpay_yesterday_refund_money'];
    			$model['new_trade_unionpay_refund_num'] = $v['unionpay_yesterday_refund_count'];
    	
    			$model['total_trade_stored_money'] = $storedpay_trade_money;
    			$model['total_trade_actual_stored_money'] = $storedpay_receipt_money;
    			$model['total_trade_stored_num'] = $storedpay_trade_count;
    			$model['new_trade_stored_money'] = $v['storedpay_yesterday_trade_money'];
    			$model['new_trade_actual_stored_money'] = $v['storedpay_yesterday_receipt_money'];
    			$model['new_trade_stored_num'] = $v['storedpay_yesterday_trade_count'];
    			$model['new_trade_stored_refund_money'] = $v['storedpay_yesterday_refund_money'];
    			$model['new_trade_stored_refund_num'] = $v['storedpay_yesterday_refund_count'];
    			
    			$model['total_trade_coupon_num'] = $total_coupons_count;
    			$model['total_alipay_commision_money'] = $alipay_commision_money;
    			$model['total_alipay_commision_num'] = $alipay_commision_count;
    			$model['total_wechat_commision_money'] = $wxpay_commision_money;
    			$model['total_wechat_commision_num'] = $wxpay_commision_count;
    			
    			$model['new_trade_coupon_num'] = $v['yesterday_coupons_count'];
    			$model['new_alipay_commision_money'] = $v['alipay_yesterday_commision_money'];
    			$model['new_wechat_commision_money'] = $v['wxpay_yesterday_commision_money'];
    			$model['new_alipay_commision_num'] = $v['alipay_yesterday_commision_count'];
    			$model['new_wechat_commision_num'] = $v['wxpay_yesterday_commision_count'];
    	
    			if (!$model->save()) {
    				throw new Exception('门店统计数据保存失败');
    			}
    		}
    	
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
    		$result['errMsg'] = $e -> getMessage();
    	}
    	return json_encode($result);
    }
    
    /**
     * 统计商户数据并保存
     * @param unknown $agent_id
     * @throws Exception
     * @return string
     */
    public function saveMerchantStatistics($agent_id) {
    	$result = array();
    	try {
    		//统计的开始时间
    		//$start_time = '';
    		//统计的结束时间：昨日的时间
    		$end_time = date('Y-m-d 23:59:59', strtotime("-1 day"));
    		
    		//查询该服务商下所有商户
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('create_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		$cmd->andWhere('agent_id = :agent_id');
    		$cmd->params[':agent_id'] = $agent_id;
    		
    		//指定查询表
    		$cmd->from = 'wq_merchant';
    		//查询字段
    		$cmd->select = 'id';
    		//执行sql查询:获取服务商下所有商户的id
    		$list = $cmd->queryAll();
    		$merchants = array();
    		foreach ($list as $k => $v) {
    			$merchants[] = $v['id'];
    		}
    		
    		//获取该服务商下所有商户并初始化交易数组
    		$data = array();
    		foreach ($merchants as $k => $v) {
    			$data[$v] = array(
    					'total_trade_money' => 0, //交易金额
    					'total_receipt_money' => 0, //实收金额
    					'total_refund_money' => 0, //退款金额
    					'total_yesterday_trade_money' => 0, //昨日交易金额
    					'total_yesterday_receipt_money' => 0, //昨日实收金额
    					'total_yesterday_refund_money' => 0, //昨日退款金额
    					'total_trade_count' => 0, //交易笔数
    					'total_refund_count' => 0, //退款笔数
    					'total_yesterday_trade_count' => 0, //昨日交易笔数
    					'total_yesterday_refund_count' => 0, //昨日退款笔数
    					'alipay_trade_money' => 0, //支付宝交易金额
    					'alipay_receipt_money' => 0, //支付宝实收金额
    					'alipay_refund_money' => 0, //支付宝退款金额
    					'alipay_yesterday_trade_money' => 0, //支付宝昨日交易金额
    					'alipay_yesterday_receipt_money' => 0, //支付宝昨日实收金额
    					'alipay_yesterday_refund_money' => 0, //支付宝昨日退款金额
    					'alipay_trade_count' => 0, //支付宝交易笔数
    					'alipay_refund_count' => 0, //支付宝退款笔数
    					'alipay_yesterday_trade_count' => 0, //支付宝昨日交易笔数
    					'alipay_yesterday_refund_count' => 0, //支付宝昨日退款笔数
    					'wxpay_trade_money' => 0, //微信交易金额
    					'wxpay_receipt_money' => 0, //微信实收金额
    					'wxpay_refund_money' => 0, //微信退款金额
    					'wxpay_yesterday_trade_money' => 0, //微信昨日交易金额
    					'wxpay_yesterday_receipt_money' => 0, //微信昨日实收金额
    					'wxpay_yesterday_refund_money' => 0, //微信昨日退款金额
    					'wxpay_trade_count' => 0, //微信交易笔数
    					'wxpay_refund_count' => 0, //微信退款笔数
    					'wxpay_yesterday_trade_count' => 0, //微信昨日交易笔数
    					'wxpay_yesterday_refund_count' => 0, //微信昨日退款笔数
    					'cashpay_trade_money' => 0, //现金交易金额
    					'cashpay_receipt_money' => 0, //现金实收金额
    					'cashpay_refund_money' => 0, //现金退款金额
    					'cashpay_yesterday_trade_money' => 0, //现金昨日交易金额
    					'cashpay_yesterday_receipt_money' => 0, //现金昨日实收金额
    					'cashpay_yesterday_refund_money' => 0, //现金昨日退款金额
    					'cashpay_trade_count' => 0, //现金交易笔数
    					'cashpay_refund_count' => 0, //现金退款笔数
    					'cashpay_yesterday_trade_count' => 0, //现金昨日交易笔数
    					'cashpay_yesterday_refund_count' => 0, //现金昨日退款笔数
    					'unionpay_trade_money' => 0, //银联支付交易金额
    					'unionpay_receipt_money' => 0, //银联支付实收金额
    					'unionpay_refund_money' => 0, //银联支付退款金额
    					'unionpay_yesterday_trade_money' => 0, //银联支付昨日交易金额
    					'unionpay_yesterday_receipt_money' => 0, //银联支付昨日实收金额
    					'unionpay_yesterday_refund_money' => 0, //银联支付昨日退款金额
    					'unionpay_trade_count' => 0, //银联支付交易笔数
    					'unionpay_refund_count' => 0, //银联支付退款笔数
    					'unionpay_yesterday_trade_count' => 0, //银联支付昨日交易笔数
    					'unionpay_yesterday_refund_count' => 0, //银联支付昨日退款笔数
    					'storedpay_trade_money' => 0, //储值支付交易金额
    					'storedpay_receipt_money' => 0, //储值支付实收金额
    					'storedpay_refund_money' => 0, //储值支付退款金额
    					'storedpay_yesterday_trade_money' => 0, //储值支付昨日交易金额
    					'storedpay_yesterday_receipt_money' => 0, //储值支付昨日实收金额
    					'storedpay_yesterday_refund_money' => 0, //储值支付昨日退款金额
    					'storedpay_trade_count' => 0, //储值支付交易笔数
    					'storedpay_refund_count' => 0, //储值支付退款笔数
    					'storedpay_yesterday_trade_count' => 0, //储值支付昨日交易笔数
    					'storedpay_yesterday_refund_count' => 0, //储值支付昨日退款笔数
    					'total_coupons_count' => 0, //累计卡券核销笔数
    					'alipay_commision_money' => 0, //累计支付宝符合返佣条件金额
    					'alipay_commision_count' => 0, //累计支付宝符合返佣条件笔数
    					'wxpay_commision_money' => 0, //累计微信符合返佣条件金额
    					'wxpay_commision_count' => 0, //累计微信符合返佣条件笔数
    					'yesterday_coupons_count' => 0, //昨日卡券核销笔数
    					'alipay_yesterday_commision_money' => 0, //支付宝昨日符合返佣条件金额
    					'alipay_yesterday_commision_count' => 0, //支付宝昨日符合返佣条件笔数
    					'wxpay_yesterday_commision_money' => 0, //微信昨日符合返佣条件金额
    					'wxpay_yesterday_commision_count' => 0, //微信昨日符合返佣条件笔数
    					'total_user_count' => 0, //总客户量
    					'total_alifans_count' => 0, //支付宝粉丝总量
    					'total_wxfans_count' => 0, //微信粉丝总量
    					'total_member_count' => 0, //会员总量
    					'yesterday_user_count' => 0, //昨日新增客户量
    					'yesterday_alifans_count' => 0, //昨日新增支付宝粉丝量
    					'yesterday_wxfans_count' => 0, //昨日新增微信粉丝量
    					'yesterday_member_count' => 0, //昨日新增会员粉丝量
    					'total_store_count' => 0, //累计门店数
    					'yesterday_store_count' => 0, //昨日新增门店数
    					'active_store_count' => 0, //活跃门店数
    			);
    		}
    		
    		//获取商户交易数据
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('date = :date');
    		$cmd->params[':date'] = $end_time;
    		$cmd->andWhere(array('IN', 'merchant_id', $merchants));
    		//分组
    		$cmd->group = 'merchant_id';
    		//指定查询表
    		$cmd->from = 'wq_s_statistics';
    		//查询计算
    		$select = 'merchant_id';
    		$column = array(
    				'total_trade_money','total_trade_actual_money','total_trade_num',
    				'new_trade_money','new_trade_actual_money','new_trade_num',
    				'new_trade_refund_money','new_trade_refund_num',
    				'total_trade_alipay_money','total_trade_actual_alipay_money','total_trade_alipay_num',
    				'new_trade_alipay_money','new_trade_actual_alipay_money','new_trade_alipay_num',
    				'new_trade_alipay_refund_money','new_trade_alipay_refund_num',
    				'total_trade_wechat_money','total_trade_actual_wechat_money','total_trade_wechat_num',
    				'new_trade_wechat_money','new_trade_actual_wechat_money','new_trade_wechat_num',
    				'new_trade_wechat_refund_money','new_trade_wechat_refund_num',
    				'total_trade_unionpay_money','total_trade_actual_unionpay_money','total_trade_unionpay_num',
    				'new_trade_unionpay_money','new_trade_actual_unionpay_money','new_trade_unionpay_num',
    				'new_trade_unionpay_refund_money','new_trade_unionpay_refund_num',
    				'total_trade_stored_money','total_trade_actual_stored_money','total_trade_stored_num',
    				'new_trade_stored_money','new_trade_actual_stored_money','new_trade_stored_num',
    				'new_trade_stored_refund_money','new_trade_stored_refund_num',
    				'total_trade_cash_money','total_trade_actual_cash_money','total_trade_cash_num',
    				'new_trade_cash_money','new_trade_actual_cash_money','new_trade_cash_num',
    				'new_trade_cash_refund_money','new_trade_cash_refund_num',
    				'total_trade_coupon_num',
    				'total_alipay_commision_money','total_wechat_commision_money',
    				'total_alipay_commision_num','total_wechat_commision_num',
    				'new_trade_coupon_num',
    				'new_alipay_commision_money','new_wechat_commision_money',
    				'new_alipay_commision_num','new_wechat_commision_num'
    		);
    		foreach ($column as $name) {
    			$select .= ", SUM($name) AS $name";
    		}
    		$cmd->select = $select;
    		
    		//执行sql查询:统计服务商下所有商户的交易数据
    		$list = $cmd->queryAll();
    		
    		foreach ($list as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			
    			$data[$merchant_id]['total_trade_money'] = $v['total_trade_money'];
    			$data[$merchant_id]['total_receipt_money'] = $v['total_trade_actual_money'];
    			$data[$merchant_id]['total_trade_count'] = $v['total_trade_num'];
    			$data[$merchant_id]['total_yesterday_trade_money'] = $v['new_trade_money'];
    			$data[$merchant_id]['total_yesterday_receipt_money'] = $v['new_trade_actual_money'];
    			$data[$merchant_id]['total_yesterday_refund_money'] = $v['new_trade_refund_money'];
    			$data[$merchant_id]['total_yesterday_trade_count'] = $v['new_trade_num'];
    			$data[$merchant_id]['total_yesterday_refund_count'] = $v['new_trade_refund_num'];
    			
    			$data[$merchant_id]['alipay_trade_money'] = $v['total_trade_alipay_money'];
    			$data[$merchant_id]['alipay_receipt_money'] = $v['total_trade_actual_alipay_money'];
    			$data[$merchant_id]['alipay_trade_count'] = $v['total_trade_alipay_num'];
    			$data[$merchant_id]['alipay_yesterday_trade_money'] = $v['new_trade_alipay_money'];
    			$data[$merchant_id]['alipay_yesterday_receipt_money'] = $v['new_trade_actual_alipay_money'];
    			$data[$merchant_id]['alipay_yesterday_refund_money'] = $v['new_trade_alipay_refund_money'];
    			$data[$merchant_id]['alipay_yesterday_trade_count'] = $v['new_trade_alipay_num'];
    			$data[$merchant_id]['alipay_yesterday_refund_count'] = $v['new_trade_alipay_refund_num'];
    			
    			$data[$merchant_id]['wxpay_trade_money'] = $v['total_trade_wechat_money'];
    			$data[$merchant_id]['wxpay_receipt_money'] = $v['total_trade_actual_wechat_money'];
    			$data[$merchant_id]['wxpay_trade_count'] = $v['total_trade_wechat_num'];
    			$data[$merchant_id]['wxpay_yesterday_trade_money'] = $v['new_trade_wechat_money'];
    			$data[$merchant_id]['wxpay_yesterday_receipt_money'] = $v['new_trade_actual_wechat_money'];
    			$data[$merchant_id]['wxpay_yesterday_refund_money'] = $v['new_trade_wechat_refund_money'];
    			$data[$merchant_id]['wxpay_yesterday_trade_count'] = $v['new_trade_wechat_num'];
    			$data[$merchant_id]['wxpay_yesterday_refund_count'] = $v['new_trade_wechat_refund_num'];
    			
    			$data[$merchant_id]['cashpay_trade_money'] = $v['total_trade_cash_money'];
    			$data[$merchant_id]['cashpay_receipt_money'] = $v['total_trade_actual_cash_money'];
    			$data[$merchant_id]['cashpay_trade_count'] = $v['total_trade_cash_num'];
    			$data[$merchant_id]['cashpay_yesterday_trade_money'] = $v['new_trade_cash_money'];
    			$data[$merchant_id]['cashpay_yesterday_receipt_money'] = $v['new_trade_actual_cash_money'];
    			$data[$merchant_id]['cashpay_yesterday_refund_money'] = $v['new_trade_cash_refund_money'];
    			$data[$merchant_id]['cashpay_yesterday_trade_count'] = $v['new_trade_cash_num'];
    			$data[$merchant_id]['cashpay_yesterday_refund_count'] = $v['new_trade_cash_refund_num'];
    			
    			$data[$merchant_id]['unionpay_trade_money'] = $v['total_trade_unionpay_money'];
    			$data[$merchant_id]['unionpay_receipt_money'] = $v['total_trade_actual_unionpay_money'];
    			$data[$merchant_id]['unionpay_trade_count'] = $v['total_trade_unionpay_num'];
    			$data[$merchant_id]['unionpay_yesterday_trade_money'] = $v['new_trade_unionpay_money'];
    			$data[$merchant_id]['unionpay_yesterday_receipt_money'] = $v['new_trade_actual_unionpay_money'];
    			$data[$merchant_id]['unionpay_yesterday_refund_money'] = $v['new_trade_unionpay_refund_money'];
    			$data[$merchant_id]['unionpay_yesterday_trade_count'] = $v['new_trade_unionpay_num'];
    			$data[$merchant_id]['unionpay_yesterday_refund_count'] = $v['new_trade_unionpay_refund_num'];
    			
    			$data[$merchant_id]['storedpay_trade_money'] = $v['total_trade_stored_money'];
    			$data[$merchant_id]['storedpay_receipt_money'] = $v['total_trade_actual_stored_money'];
    			$data[$merchant_id]['storedpay_trade_count'] = $v['total_trade_stored_num'];
    			$data[$merchant_id]['storedpay_yesterday_trade_money'] = $v['new_trade_stored_money'];
    			$data[$merchant_id]['storedpay_yesterday_receipt_money'] = $v['new_trade_actual_stored_money'];
    			$data[$merchant_id]['storedpay_yesterday_refund_money'] = $v['new_trade_stored_refund_money'];
    			$data[$merchant_id]['storedpay_yesterday_trade_count'] = $v['new_trade_stored_num'];
    			$data[$merchant_id]['storedpay_yesterday_refund_count'] = $v['new_trade_stored_refund_num'];
    			
    			$data[$merchant_id]['total_coupons_count'] = $v['total_trade_coupon_num'];
    			$data[$merchant_id]['alipay_commision_money'] = $v['total_alipay_commision_money'];
    			$data[$merchant_id]['alipay_commision_count'] = $v['total_alipay_commision_num'];
    			$data[$merchant_id]['wxpay_commision_money'] = $v['total_wechat_commision_money'];
    			$data[$merchant_id]['wxpay_commision_count'] = $v['total_wechat_commision_num'];
    			
    			$data[$merchant_id]['yesterday_coupons_count'] = $v['new_trade_coupon_num'];
    			$data[$merchant_id]['alipay_yesterday_commision_money'] = $v['new_alipay_commision_money'];
    			$data[$merchant_id]['alipay_yesterday_commision_count'] = $v['new_alipay_commision_num'];
    			$data[$merchant_id]['wxpay_yesterday_commision_money'] = $v['new_wechat_commision_money'];
    			$data[$merchant_id]['wxpay_yesterday_commision_count'] = $v['new_wechat_commision_num'];
    		}
    		
    		//统计商户客户数
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('create_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		$cmd->andWhere(array('IN', 'merchant_id', $merchants));
    		//分组
    		$cmd->group = 'merchant_id';
    		//指定查询表
    		$cmd->from = 'wq_user';
    		//查询
    		$select = 'merchant_id, COUNT(*) AS user_count';
    		$cmd->select = $select;
    		
    		//设置多个查询语句
    		$cmd1 = clone $cmd; //全部会员
    		$cmd2 = clone $cmd; //昨日会员
    		$cmd3 = clone $cmd; //支付宝粉丝
    		$cmd4 = clone $cmd; //昨日支付宝粉丝
    		$cmd5 = clone $cmd; //微信粉丝
    		$cmd6 = clone $cmd; //昨日微信粉丝
    		$cmd7 = clone $cmd; //全部客户（包括会员、支付宝微信粉丝）
    		$cmd8 = clone $cmd; //昨日客户（包括会员、支付宝微信粉丝）
    		
    		//cmd1筛选全部会员
    		$cmd1->andWhere('type = :type');
    		$cmd1->params[':type'] = USER_TYPE_WANQUAN_MEMBER;
    		
    		//cmd2筛选昨日会员
    		$cmd2->andWhere('type = :type');
    		$cmd2->params[':type'] = USER_TYPE_WANQUAN_MEMBER;
    		$cmd2->andWhere('create_time >= :create_time');
    		$cmd2->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		
    		//cmd3筛选支付宝粉丝（已关注的）
    		$cmd3->andWhere('type = :type');
    		$cmd3->params[':type'] = USER_TYPE_ALIPAY_FANS;
    		$cmd3->andWhere('alipay_status = :alipay_status');
    		$cmd3->params[':alipay_status'] = ALIPAY_USER_SUBSCRIBE;
    		
    		//cmd4筛选昨日支付宝粉丝（已关注的）
    		$cmd4->andWhere('type = :type');
    		$cmd4->params[':type'] = USER_TYPE_ALIPAY_FANS;
    		$cmd4->andWhere('alipay_status = :alipay_status');
    		$cmd4->params[':alipay_status'] = ALIPAY_USER_SUBSCRIBE;
    		$cmd4->andWhere('create_time >= :create_time');
    		$cmd4->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		
    		//cmd5筛选微信粉丝（已关注的）
    		$cmd5->andWhere('type = :type');
    		$cmd5->params[':type'] = USER_TYPE_WECHAT_FANS;
    		$cmd5->andWhere('wechat_status = :wechat_status');
    		$cmd5->params[':wechat_status'] = WECHAT_USER_SUBSCRIBE;
    		
    		//cmd6筛选昨日微信粉丝（已关注的）
    		$cmd6->andWhere('type = :type');
    		$cmd6->params[':type'] = USER_TYPE_WECHAT_FANS;
    		$cmd6->andWhere('wechat_status = :wechat_status');
    		$cmd6->params[':wechat_status'] = WECHAT_USER_SUBSCRIBE;
    		$cmd6->andWhere('create_time >= :create_time');
    		$cmd6->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		
    		//cmd8筛选昨日客户
    		$cmd8->andWhere('create_time >= :create_time');
    		$cmd8->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		
    		//执行sql
    		$member_all_num = $cmd1->queryAll();
    		$member_yesterday_num = $cmd2->queryAll();
    		$alipay_all_num = $cmd3->queryAll();
    		$alipay_yesterday_num = $cmd4->queryAll();
    		$wxpay_all_num = $cmd5->queryAll();
    		$wxpay_yesterday_num = $cmd6->queryAll();
    		$user_all_num = $cmd7->queryAll();
    		$user_yesterday_num = $cmd8->queryAll();
    		
    		foreach ($member_all_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['total_member_count'] = $user_count;
    		}
    		foreach ($member_yesterday_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['yesterday_member_count'] = $user_count;
    		}
    		foreach ($alipay_all_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['total_alifans_count'] = $user_count;
    		}
    		foreach ($alipay_yesterday_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['yesterday_alifans_count'] = $user_count;
    		}
    		foreach ($wxpay_all_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['total_wxfans_count'] = $user_count;
    		}
    		foreach ($wxpay_yesterday_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['yesterday_wxfans_count'] = $user_count;
    		}
    		foreach ($user_all_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['total_user_count'] = $user_count;
    		}
    		foreach ($user_yesterday_num as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$user_count = $v['user_count'];
    			$data[$merchant_id]['yesterday_user_count'] = $user_count;
    		}
    		
    		//统计门店数据
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('s.flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('s.create_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		$cmd->andWhere(array('IN', 's.merchant_id', $merchants));
    		//分组
    		$cmd->group = 's.merchant_id';
    		//指定查询表
    		$cmd->from = 'wq_store s';
    		
    		//统计商户的总门店数
    		$cmd1 = clone $cmd; //深拷贝
    		//统计商户的昨日新增门店数
    		$cmd2 = clone $cmd; //深拷贝
    		//统计商户的活跃门店数（7日内有交易或核销的门店）
    		$cmd3 = clone $cmd; //深拷贝
    		
    		//查询计算1
    		$select1 = 'merchant_id, COUNT(*) AS store_count';
    		$cmd1->select = $select1;
    		
    		//查询条件2
    		$cmd2->andWhere('create_time >= :create_time');
    		$cmd2->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		//查询计算2
    		$select2 = 'merchant_id, COUNT(*) AS store_count';
    		$cmd2->select = $select2;
    		
    		//联表查询3
    		$join3 = 'INNER JOIN wq_order o ON s.id = o.store_id';
    		$cmd3->join = $join3;
    		//查询条件3
    		$cmd3->andWhere('o.flag = :flag1');
    		$cmd3->params[':flag1'] = FLAG_NO;
    		$cmd3->andWhere('o.order_type = :order_type');
    		$cmd3->params[':order_type'] = ORDER_TYPE_CASHIER;
    		$cmd3->andWhere('o.pay_status = :pay_status');
    		$cmd3->params[':pay_status'] = ORDER_STATUS_PAID;
    		$cmd3->andWhere('o.pay_time >= :pay_time');
    		$cmd3->params[':pay_time'] = date('Y-m-d 00:00:00', strtotime("-7 day"));
    		//查询计算3
    		$select3 = 's.merchant_id, COUNT(*) AS store_count';
    		$cmd3->select = $select3;
    		
    		//执行sql查询:统计总门店数
    		$list1 = $cmd1->queryAll();
    		//执行sql查询:统计昨日新增门店数
    		$list2 = $cmd2->queryAll();
    		//执行sql查询:统计活跃门店数
    		$list3 = $cmd3->queryAll();
    		
    		foreach ($list1 as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$store_count = $v['store_count'];
    			
    			$data[$merchant_id]['total_store_count'] = $store_count;
    		}
    		foreach ($list2 as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$store_count = $v['store_count'];
    			 
    			$data[$merchant_id]['yesterday_store_count'] = $store_count;
    		}
    		foreach ($list3 as $k => $v) {
    			$merchant_id = $v['merchant_id'];
    			$store_count = $v['store_count'];
    			
    			$data[$merchant_id]['active_store_count'] = $store_count;
    		}
    		
    		foreach ($data as $k => $v) {
    			//跳过错误数据
    			if (empty($k) || empty($v)) {
    				continue;
    			}
    			
    			//保存商户统计数据
    			$model = new MStatistics();
    			$model['create_time'] = date('Y-m-d H:i:s');
    			$model['agent_id'] = $agent_id;
    			$model['merchant_id'] = $k;
    			$model['date'] = $end_time;
    			
    			$model['total_trade_money'] = $v['total_trade_money'];
    			$model['total_trade_actual_money'] = $v['total_receipt_money'];
    			$model['total_trade_num'] = $v['total_trade_count'];
    			$model['new_trade_money'] = $v['total_yesterday_trade_money'];
    			$model['new_trade_actual_money'] = $v['total_yesterday_receipt_money'];
    			$model['new_trade_num'] = $v['total_yesterday_trade_count'];
    			$model['new_trade_refund_money'] = $v['total_yesterday_refund_money'];
    			$model['new_trade_refund_num'] = $v['total_yesterday_refund_count'];
    			 
    			$model['total_trade_alipay_money'] = $v['alipay_trade_money'];
    			$model['total_trade_actual_alipay_money'] = $v['alipay_receipt_money'];
    			$model['total_trade_alipay_num'] = $v['alipay_trade_count'];
    			$model['new_trade_alipay_money'] = $v['alipay_yesterday_trade_money'];
    			$model['new_trade_actual_alipay_money'] = $v['alipay_yesterday_receipt_money'];
    			$model['new_trade_alipay_num'] = $v['alipay_yesterday_trade_count'];
    			$model['new_trade_alipay_refund_money'] = $v['alipay_yesterday_refund_money'];
    			$model['new_trade_alipay_refund_num'] = $v['alipay_yesterday_refund_count'];
    			 
    			$model['total_trade_wechat_money'] = $v['wxpay_trade_money'];
    			$model['total_trade_actual_wechat_money'] = $v['wxpay_receipt_money'];
    			$model['total_trade_wechat_num'] = $v['wxpay_trade_count'];
    			$model['new_trade_wechat_money'] = $v['wxpay_yesterday_trade_money'];
    			$model['new_trade_actual_wechat_money'] = $v['wxpay_yesterday_receipt_money'];
    			$model['new_trade_wechat_num'] = $v['wxpay_yesterday_trade_count'];
    			$model['new_trade_wechat_refund_money'] = $v['wxpay_yesterday_refund_money'];
    			$model['new_trade_wechat_refund_num'] = $v['wxpay_yesterday_refund_count'];
    			 
    			$model['total_trade_cash_money'] = $v['cashpay_trade_money'];
    			$model['total_trade_actual_cash_money'] = $v['cashpay_receipt_money'];
    			$model['total_trade_cash_num'] = $v['cashpay_trade_count'];
    			$model['new_trade_cash_money'] = $v['cashpay_yesterday_trade_money'];
    			$model['new_trade_actual_cash_money'] = $v['cashpay_yesterday_receipt_money'];
    			$model['new_trade_cash_num'] = $v['cashpay_yesterday_trade_count'];
    			$model['new_trade_cash_refund_money'] = $v['cashpay_yesterday_refund_money'];
    			$model['new_trade_cash_refund_num'] = $v['cashpay_yesterday_refund_count'];
    			 
    			$model['total_trade_unionpay_money'] = $v['unionpay_trade_money'];
    			$model['total_trade_actual_unionpay_money'] = $v['unionpay_receipt_money'];
    			$model['total_trade_unionpay_num'] = $v['unionpay_trade_count'];
    			$model['new_trade_unionpay_money'] = $v['unionpay_yesterday_trade_money'];
    			$model['new_trade_actual_unionpay_money'] = $v['unionpay_yesterday_receipt_money'];
    			$model['new_trade_unionpay_num'] = $v['unionpay_yesterday_trade_count'];
    			$model['new_trade_unionpay_refund_money'] = $v['unionpay_yesterday_refund_money'];
    			$model['new_trade_unionpay_refund_num'] = $v['unionpay_yesterday_refund_count'];
    			 
    			$model['total_trade_stored_money'] = $v['storedpay_trade_money'];
    			$model['total_trade_actual_stored_money'] = $v['storedpay_receipt_money'];
    			$model['total_trade_stored_num'] = $v['storedpay_trade_count'];
    			$model['new_trade_stored_money'] = $v['storedpay_yesterday_trade_money'];
    			$model['new_trade_actual_stored_money'] = $v['storedpay_yesterday_receipt_money'];
    			$model['new_trade_stored_num'] = $v['storedpay_yesterday_trade_count'];
    			$model['new_trade_stored_refund_money'] = $v['storedpay_yesterday_refund_money'];
    			$model['new_trade_stored_refund_num'] = $v['storedpay_yesterday_refund_count'];
    			 
    			$model['total_trade_coupon_num'] = $v['total_coupons_count'];
    			$model['total_alipay_commision_money'] = $v['alipay_commision_money'];
    			$model['total_wechat_commision_money'] = $v['wxpay_commision_money'];
    			$model['total_alipay_commision_num'] = $v['alipay_commision_count'];
    			$model['total_wechat_commision_num'] = $v['wxpay_commision_count'];
    			
    			$model['new_trade_coupon_num'] = $v['yesterday_coupons_count'];
    			$model['new_alipay_commision_money'] = $v['alipay_yesterday_commision_money'];
    			$model['new_wechat_commision_money'] = $v['wxpay_yesterday_commision_money'];
    			$model['new_alipay_commision_num'] = $v['alipay_yesterday_commision_count'];
    			$model['new_wechat_commision_num'] = $v['wxpay_yesterday_commision_count'];
    			
    			$model['total_user_num'] = $v['total_user_count'];
    			$model['total_alipayfans_num'] = $v['total_alifans_count'];
    			$model['total_wechatfans_num'] = $v['total_wxfans_count'];
    			$model['total_member_num'] = $v['total_member_count'];
    			$model['new_user_num'] = $v['yesterday_user_count'];
    			$model['new_alipayfans_num'] = $v['yesterday_alifans_count'];
    			$model['new_wechatfans_num'] = $v['yesterday_wxfans_count'];
    			$model['new_member_num'] = $v['yesterday_member_count'];
    			
    			$model['total_store_num'] = $v['total_store_count'];
    			$model['new_store_num'] = $v['yesterday_store_count'];
    			$model['active_store_num'] = $v['active_store_count'];
    			
    			if (!$model->save()) {
    				throw new Exception('商户统计数据保存失败');
    			}
    		}
    		
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
    		$result['errMsg'] = $e -> getMessage();
    	}
    	return json_encode($result);
    }
    
    /**
     * 统计服务商数据并记录
     * @return string
     */
    public function saveAgentStatistics() {
    	$result = array();
    	try {
    		//统计的开始时间
    		//$start_time = '';
    		//统计的结束时间：昨日的时间
    		$end_time = date('Y-m-d 23:59:59', strtotime("-1 day"));
    		
    		//获取所有服务商并初始化交易数组
    		$agents = Agent::model()->findAll('flag = :flag and create_time <= :create_time',
    				array(':flag' => FLAG_NO, ':create_time' => $end_time));
    		$data = array();
    		foreach ($agents as $k => $v) {
    			$agent_id = $v['id'];
    			$data[$agent_id] = array(
    					'total_trade_money' => 0, //交易金额
    					'total_receipt_money' => 0, //实收金额
    					'total_refund_money' => 0, //退款金额
    					'total_yesterday_trade_money' => 0, //昨日交易金额
    					'total_yesterday_receipt_money' => 0, //昨日实收金额
    					'total_yesterday_refund_money' => 0, //昨日退款金额
    					'total_trade_count' => 0, //交易笔数
    					'total_refund_count' => 0, //退款笔数
    					'total_yesterday_trade_count' => 0, //昨日交易笔数
    					'total_yesterday_refund_count' => 0, //昨日退款笔数
    					'alipay_trade_money' => 0, //支付宝交易金额
    					'alipay_receipt_money' => 0, //支付宝实收金额
    					'alipay_refund_money' => 0, //支付宝退款金额
    					'alipay_yesterday_trade_money' => 0, //支付宝昨日交易金额
    					'alipay_yesterday_receipt_money' => 0, //支付宝昨日实收金额
    					'alipay_yesterday_refund_money' => 0, //支付宝昨日退款金额
    					'alipay_trade_count' => 0, //支付宝交易笔数
    					'alipay_refund_count' => 0, //支付宝退款笔数
    					'alipay_yesterday_trade_count' => 0, //支付宝昨日交易笔数
    					'alipay_yesterday_refund_count' => 0, //支付宝昨日退款笔数
    					'wxpay_trade_money' => 0, //微信交易金额
    					'wxpay_receipt_money' => 0, //微信实收金额
    					'wxpay_refund_money' => 0, //微信退款金额
    					'wxpay_yesterday_trade_money' => 0, //微信昨日交易金额
    					'wxpay_yesterday_receipt_money' => 0, //微信昨日实收金额
    					'wxpay_yesterday_refund_money' => 0, //微信昨日退款金额
    					'wxpay_trade_count' => 0, //微信交易笔数
    					'wxpay_refund_count' => 0, //微信退款笔数
    					'wxpay_yesterday_trade_count' => 0, //微信昨日交易笔数
    					'wxpay_yesterday_refund_count' => 0, //微信昨日退款笔数
    					'cashpay_trade_money' => 0, //现金交易金额
    					'cashpay_receipt_money' => 0, //现金实收金额
    					'cashpay_refund_money' => 0, //现金退款金额
    					'cashpay_yesterday_trade_money' => 0, //现金昨日交易金额
    					'cashpay_yesterday_receipt_money' => 0, //现金昨日实收金额
    					'cashpay_yesterday_refund_money' => 0, //现金昨日退款金额
    					'cashpay_trade_count' => 0, //现金交易笔数
    					'cashpay_refund_count' => 0, //现金退款笔数
    					'cashpay_yesterday_trade_count' => 0, //现金昨日交易笔数
    					'cashpay_yesterday_refund_count' => 0, //现金昨日退款笔数
    					'unionpay_trade_money' => 0, //银联支付交易金额
    					'unionpay_receipt_money' => 0, //银联支付实收金额
    					'unionpay_refund_money' => 0, //银联支付退款金额
    					'unionpay_yesterday_trade_money' => 0, //银联支付昨日交易金额
    					'unionpay_yesterday_receipt_money' => 0, //银联支付昨日实收金额
    					'unionpay_yesterday_refund_money' => 0, //银联支付昨日退款金额
    					'unionpay_trade_count' => 0, //银联支付交易笔数
    					'unionpay_refund_count' => 0, //银联支付退款笔数
    					'unionpay_yesterday_trade_count' => 0, //银联支付昨日交易笔数
    					'unionpay_yesterday_refund_count' => 0, //银联支付昨日退款笔数
    					'storedpay_trade_money' => 0, //储值支付交易金额
    					'storedpay_receipt_money' => 0, //储值支付实收金额
    					'storedpay_refund_money' => 0, //储值支付退款金额
    					'storedpay_yesterday_trade_money' => 0, //储值支付昨日交易金额
    					'storedpay_yesterday_receipt_money' => 0, //储值支付昨日实收金额
    					'storedpay_yesterday_refund_money' => 0, //储值支付昨日退款金额
    					'storedpay_trade_count' => 0, //储值支付交易笔数
    					'storedpay_refund_count' => 0, //储值支付退款笔数
    					'storedpay_yesterday_trade_count' => 0, //储值支付昨日交易笔数
    					'storedpay_yesterday_refund_count' => 0, //储值支付昨日退款笔数
    					'total_coupons_count' => 0, //累计卡券核销笔数
    					'alipay_commision_money' => 0, //累计支付宝符合返佣条件金额
    					'alipay_commision_count' => 0, //累计支付宝符合返佣条件笔数
    					'wxpay_commision_money' => 0, //累计微信符合返佣条件金额
    					'wxpay_commision_count' => 0, //累计微信符合返佣条件笔数
    					'yesterday_coupons_count' => 0, //昨日卡券核销笔数
    					'alipay_yesterday_commision_money' => 0, //支付宝昨日符合返佣条件金额
    					'alipay_yesterday_commision_count' => 0, //支付宝昨日符合返佣条件笔数
    					'wxpay_yesterday_commision_money' => 0, //微信昨日符合返佣条件金额
    					'wxpay_yesterday_commision_count' => 0, //微信昨日符合返佣条件笔数
    					'total_user_count' => 0, //总客户量
    					'total_alifans_count' => 0, //支付宝粉丝总量
    					'total_wxfans_count' => 0, //微信粉丝总量
    					'total_member_count' => 0, //会员总量
    					'yesterday_user_count' => 0, //昨日新增客户量
    					'yesterday_alifans_count' => 0, //昨日新增支付宝粉丝量
    					'yesterday_wxfans_count' => 0, //昨日新增微信粉丝量
    					'yesterday_member_count' => 0, //昨日新增会员粉丝量
    					'total_store_count' => 0, //累计门店数
    					'yesterday_store_count' => 0, //昨日新增门店数
    					'active_store_count' => 0, //活跃门店数
    					'total_merchant_count' => 0, //累计商户数
    					'total_yx_merchant_count' => 0, //累计营销版商户数
    					'total_sy_merchant_count' => 0, //累计收银版商户数
    					'total_yx_service_money' => 0, //累计营销版服务费
    					'yesterday_merchant_count' => 0, //昨日新增商户数
    					'yesterday_yx_merchant_count' => 0, //昨日新增营销版商户数
    					'yesterday_sy_merchant_count' => 0, //昨日新增收银版商户数
    					'yesterday_yx_service_money' => 0, //昨日新增营销版服务费
    					'total_level1_agent_count' => 0, //累计一级服务商数量
    					'total_level1_agent_fee' => 0, //累计一级服务商佣金
    					'total_level2_agent_count' => 0, //累计二级服务商数量
    					'total_level2_agent_fee' => 0, //累计二级服务商佣金
    			);
    			
    			//记录服务商的pid和ppid
    			$data[$agent_id]['pid'] = $v['pid'];
    			$data[$agent_id]['ppid'] = $v['ppid'];
    		}
    		
    		//统计所有服务商的交易数据
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('date = :date');
    		$cmd->params[':date'] = $end_time;
    		//分组
    		$cmd->group = 'agent_id';
    		//指定查询表
    		$cmd->from = 'wq_m_statistics';
    		//查询计算
    		$select = 'agent_id';
    		$column = array(
    				'total_trade_money','total_trade_actual_money','total_trade_num',
    				'new_trade_money','new_trade_actual_money','new_trade_num',
    				'new_trade_refund_money','new_trade_refund_num',
    				'total_trade_alipay_money','total_trade_actual_alipay_money','total_trade_alipay_num',
    				'new_trade_alipay_money','new_trade_actual_alipay_money','new_trade_alipay_num',
    				'new_trade_alipay_refund_money','new_trade_alipay_refund_num',
    				'total_trade_wechat_money','total_trade_actual_wechat_money','total_trade_wechat_num',
    				'new_trade_wechat_money','new_trade_actual_wechat_money','new_trade_wechat_num',
    				'new_trade_wechat_refund_money','new_trade_wechat_refund_num',
    				'total_trade_unionpay_money','total_trade_actual_unionpay_money','total_trade_unionpay_num',
    				'new_trade_unionpay_money','new_trade_actual_unionpay_money','new_trade_unionpay_num',
    				'new_trade_unionpay_refund_money','new_trade_unionpay_refund_num',
    				'total_trade_stored_money','total_trade_actual_stored_money','total_trade_stored_num',
    				'new_trade_stored_money','new_trade_actual_stored_money','new_trade_stored_num',
    				'new_trade_stored_refund_money','new_trade_stored_refund_num',
    				'total_trade_cash_money','total_trade_actual_cash_money','total_trade_cash_num',
    				'new_trade_cash_money','new_trade_actual_cash_money','new_trade_cash_num',
    				'new_trade_cash_refund_money','new_trade_cash_refund_num',
    				'total_trade_coupon_num',
    				'total_alipay_commision_money','total_wechat_commision_money',
    				'total_alipay_commision_num','total_wechat_commision_num',
    				'new_trade_coupon_num',
    				'new_alipay_commision_money','new_wechat_commision_money',
    				'new_alipay_commision_num','new_wechat_commision_num',
    				'total_user_num','total_alipayfans_num','total_wechatfans_num','total_member_num',
    				'new_user_num','new_alipayfans_num','new_wechatfans_num','new_member_num',
    				'total_store_num','new_store_num','active_store_num'
    		);
    		foreach ($column as $name) {
    			$select .= ", SUM($name) AS $name";
    		}
    		$cmd->select = $select;
    		
    		//执行sql查询:统计服务商下所有商户的交易数据
    		$list = $cmd->queryAll();
    		
    		foreach ($list as $k => $v) {
    			$agent_id = $v['agent_id'];
    			
    			$data[$agent_id]['total_trade_money'] = $v['total_trade_money'];
    			$data[$agent_id]['total_receipt_money'] = $v['total_trade_actual_money'];
    			$data[$agent_id]['total_trade_count'] = $v['total_trade_num'];
    			$data[$agent_id]['total_yesterday_trade_money'] = $v['new_trade_money'];
    			$data[$agent_id]['total_yesterday_receipt_money'] = $v['new_trade_actual_money'];
    			$data[$agent_id]['total_yesterday_refund_money'] = $v['new_trade_refund_money'];
    			$data[$agent_id]['total_yesterday_trade_count'] = $v['new_trade_num'];
    			$data[$agent_id]['total_yesterday_refund_count'] = $v['new_trade_refund_num'];
    			 
    			$data[$agent_id]['alipay_trade_money'] = $v['total_trade_alipay_money'];
    			$data[$agent_id]['alipay_receipt_money'] = $v['total_trade_actual_alipay_money'];
    			$data[$agent_id]['alipay_trade_count'] = $v['total_trade_alipay_num'];
    			$data[$agent_id]['alipay_yesterday_trade_money'] = $v['new_trade_alipay_money'];
    			$data[$agent_id]['alipay_yesterday_receipt_money'] = $v['new_trade_actual_alipay_money'];
    			$data[$agent_id]['alipay_yesterday_refund_money'] = $v['new_trade_alipay_refund_money'];
    			$data[$agent_id]['alipay_yesterday_trade_count'] = $v['new_trade_alipay_num'];
    			$data[$agent_id]['alipay_yesterday_refund_count'] = $v['new_trade_alipay_refund_num'];
    			 
    			$data[$agent_id]['wxpay_trade_money'] = $v['total_trade_wechat_money'];
    			$data[$agent_id]['wxpay_receipt_money'] = $v['total_trade_actual_wechat_money'];
    			$data[$agent_id]['wxpay_trade_count'] = $v['total_trade_wechat_num'];
    			$data[$agent_id]['wxpay_yesterday_trade_money'] = $v['new_trade_wechat_money'];
    			$data[$agent_id]['wxpay_yesterday_receipt_money'] = $v['new_trade_actual_wechat_money'];
    			$data[$agent_id]['wxpay_yesterday_refund_money'] = $v['new_trade_wechat_refund_money'];
    			$data[$agent_id]['wxpay_yesterday_trade_count'] = $v['new_trade_wechat_num'];
    			$data[$agent_id]['wxpay_yesterday_refund_count'] = $v['new_trade_wechat_refund_num'];
    			 
    			$data[$agent_id]['cashpay_trade_money'] = $v['total_trade_cash_money'];
    			$data[$agent_id]['cashpay_receipt_money'] = $v['total_trade_actual_cash_money'];
    			$data[$agent_id]['cashpay_trade_count'] = $v['total_trade_cash_num'];
    			$data[$agent_id]['cashpay_yesterday_trade_money'] = $v['new_trade_cash_money'];
    			$data[$agent_id]['cashpay_yesterday_receipt_money'] = $v['new_trade_actual_cash_money'];
    			$data[$agent_id]['cashpay_yesterday_refund_money'] = $v['new_trade_cash_refund_money'];
    			$data[$agent_id]['cashpay_yesterday_trade_count'] = $v['new_trade_cash_num'];
    			$data[$agent_id]['cashpay_yesterday_refund_count'] = $v['new_trade_cash_refund_num'];
    			 
    			$data[$agent_id]['unionpay_trade_money'] = $v['total_trade_unionpay_money'];
    			$data[$agent_id]['unionpay_receipt_money'] = $v['total_trade_actual_unionpay_money'];
    			$data[$agent_id]['unionpay_trade_count'] = $v['total_trade_unionpay_num'];
    			$data[$agent_id]['unionpay_yesterday_trade_money'] = $v['new_trade_unionpay_money'];
    			$data[$agent_id]['unionpay_yesterday_receipt_money'] = $v['new_trade_actual_unionpay_money'];
    			$data[$agent_id]['unionpay_yesterday_refund_money'] = $v['new_trade_unionpay_refund_money'];
    			$data[$agent_id]['unionpay_yesterday_trade_count'] = $v['new_trade_unionpay_num'];
    			$data[$agent_id]['unionpay_yesterday_refund_count'] = $v['new_trade_unionpay_refund_num'];
    			 
    			$data[$agent_id]['storedpay_trade_money'] = $v['total_trade_stored_money'];
    			$data[$agent_id]['storedpay_receipt_money'] = $v['total_trade_actual_stored_money'];
    			$data[$agent_id]['storedpay_trade_count'] = $v['total_trade_stored_num'];
    			$data[$agent_id]['storedpay_yesterday_trade_money'] = $v['new_trade_stored_money'];
    			$data[$agent_id]['storedpay_yesterday_receipt_money'] = $v['new_trade_actual_stored_money'];
    			$data[$agent_id]['storedpay_yesterday_refund_money'] = $v['new_trade_stored_refund_money'];
    			$data[$agent_id]['storedpay_yesterday_trade_count'] = $v['new_trade_stored_num'];
    			$data[$agent_id]['storedpay_yesterday_refund_count'] = $v['new_trade_stored_refund_num'];
    			
    			$data[$agent_id]['total_coupons_count'] = $v['total_trade_coupon_num'];
    			$data[$agent_id]['alipay_commision_money'] = $v['total_alipay_commision_money'];
    			$data[$agent_id]['alipay_commision_count'] = $v['total_alipay_commision_num'];
    			$data[$agent_id]['wxpay_commision_money'] = $v['total_wechat_commision_money'];
    			$data[$agent_id]['wxpay_commision_count'] = $v['total_wechat_commision_num'];
    			
    			$data[$agent_id]['yesterday_coupons_count'] = $v['new_trade_coupon_num'];
    			$data[$agent_id]['alipay_yesterday_commision_money'] = $v['new_alipay_commision_money'];
    			$data[$agent_id]['alipay_yesterday_commision_count'] = $v['new_alipay_commision_num'];
    			$data[$agent_id]['wxpay_yesterday_commision_money'] = $v['new_wechat_commision_money'];
    			$data[$agent_id]['wxpay_yesterday_commision_count'] = $v['new_wechat_commision_num'];
    			
    			$data[$agent_id]['total_user_count'] = $v['total_user_num'];
    			$data[$agent_id]['total_alifans_count'] = $v['total_alipayfans_num'];
    			$data[$agent_id]['total_wxfans_count'] = $v['total_wechatfans_num'];
    			$data[$agent_id]['total_member_count'] = $v['total_member_num'];
    			$data[$agent_id]['yesterday_user_count'] = $v['new_user_num'];
    			$data[$agent_id]['yesterday_alifans_count'] = $v['new_alipayfans_num'];
    			$data[$agent_id]['yesterday_wxfans_count'] = $v['new_wechatfans_num'];
    			$data[$agent_id]['yesterday_member_count'] = $v['new_member_num'];
    			
    			$data[$agent_id]['total_store_count'] = $v['total_store_num'];
    			$data[$agent_id]['yesterday_store_count'] = $v['new_store_num'];
    			$data[$agent_id]['active_store_count'] = $v['active_store_num'];
    		}
    		
    		//获取服务商下的商户数量统计数据
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('create_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		//分组
    		$cmd->group = 'agent_id';
    		//指定查询表
    		$cmd->from = 'wq_merchant';
    		//查询
    		$select = 'agent_id, COUNT(*) AS merchant_count';
    		$cmd->select = $select;
    		
    		//设置多个查询语句
    		$cmd1 = clone $cmd; //累计全部商户
    		$cmd2 = clone $cmd; //累计营销版商户
    		$cmd3 = clone $cmd; //累计收银版商户
    		$cmd4 = clone $cmd; //昨日新增商户
    		$cmd5 = clone $cmd; //昨日新增营销版商户
    		$cmd6 = clone $cmd; //昨日新增收银版商户
    		
    		//cmd2筛选营销版商户
    		$cmd2->andWhere('gj_open_status != :gj_open_status');
    		$cmd2->params[':gj_open_status'] = GJ_OPEN_STATUS_NULL;
    		$cmd2->andWhere('gj_product_id = :gj_product_id');
    		$cmd2->params[':gj_product_id'] = '2'; //营销版
    		
    		//cmd3筛选收银版商户
    		$cmd2->andWhere('gj_open_status != :gj_open_status');
    		$cmd2->params[':gj_open_status'] = GJ_OPEN_STATUS_NULL;
    		$cmd2->andWhere('gj_product_id = :gj_product_id');
    		$cmd2->params[':gj_product_id'] = '1'; //收银版
    		
    		//cmd4筛选昨日新增商户
    		$cmd4->andWhere('create_time >= :create_time');
    		$cmd4->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		
    		//cmd5筛选昨日新增营销版商户
    		$cmd5->andWhere('create_time >= :create_time');
    		$cmd5->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		$cmd5->andWhere('gj_open_status != :gj_open_status');
    		$cmd5->params[':gj_open_status'] = GJ_OPEN_STATUS_NULL;
    		$cmd5->andWhere('gj_product_id = :gj_product_id');
    		$cmd5->params[':gj_product_id'] = '2'; //营销版
    		
    		//cmd6筛选昨日新增收银版商户
    		$cmd6->andWhere('create_time >= :create_time');
    		$cmd6->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		$cmd6->andWhere('gj_open_status != :gj_open_status');
    		$cmd6->params[':gj_open_status'] = GJ_OPEN_STATUS_NULL;
    		$cmd6->andWhere('gj_product_id = :gj_product_id');
    		$cmd6->params[':gj_product_id'] = '1'; //收银版
    		
    		//执行sql:统计累计商户数量
    		$list1 = $cmd1->queryAll();
    		//执行sql:统计累计营销版商户数量
    		$list2 = $cmd2->queryAll();
    		//执行sql:统计累计收银版商户数量
    		$list3 = $cmd3->queryAll();
    		//执行sql:统计昨日新增商户数量
    		$list4 = $cmd4->queryAll();
    		//执行sql:统计昨日新增营销版商户数量
    		$list5 = $cmd5->queryAll();
    		//执行sql:统计昨日新增收银版商户数量
    		$list6 = $cmd6->queryAll();
    		
    		foreach ($list1 as $k => $v) {
    			$agent_id = $v['agent_id'];
    			$merchant_count = $v['merchant_count'];
    			$data[$agent_id]['total_merchant_count'] = $merchant_count;
    		}
    		foreach ($list2 as $k => $v) {
    			$agent_id = $v['agent_id'];
    			$merchant_count = $v['merchant_count'];
    			$data[$agent_id]['total_yx_merchant_count'] = $merchant_count;
    		}
    		foreach ($list3 as $k => $v) {
    			$agent_id = $v['agent_id'];
    			$merchant_count = $v['merchant_count'];
    			$data[$agent_id]['total_sy_merchant_count'] = $merchant_count;
    		}
    		foreach ($list4 as $k => $v) {
    			$agent_id = $v['agent_id'];
    			$merchant_count = $v['merchant_count'];
    			$data[$agent_id]['yesterday_merchant_count'] = $merchant_count;
    		}
    		foreach ($list5 as $k => $v) {
    			$agent_id = $v['agent_id'];
    			$merchant_count = $v['merchant_count'];
    			$data[$agent_id]['yesterday_yx_merchant_count'] = $merchant_count;
    		}
    		foreach ($list6 as $k => $v) {
    			$agent_id = $v['agent_id'];
    			$merchant_count = $v['merchant_count'];
    			$data[$agent_id]['yesterday_sy_merchant_count'] = $merchant_count;
    		}
    		
    		//获取服务商下的一级/二级服务商数量统计数据
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('master.flag = :master_flag');
    		$cmd->params[':master_flag'] = FLAG_NO;
    		$cmd->andWhere('master.create_time <= :master_end_time');
    		$cmd->params[':master_end_time'] = $end_time;
    		$cmd->andWhere('slave.flag = :slave_flag');
    		$cmd->params[':slave_flag'] = FLAG_NO;
    		$cmd->andWhere('slave.create_time <= :slave_end_time');
    		$cmd->params[':slave_end_time'] = $end_time;
    		//分组
    		$cmd->group = 'master.id';
    		//指定查询表
    		$cmd->from = 'wq_agent master, wq_agent slave';
    		//查询
    		$select = 'master.id, COUNT(*) AS agent_count';
    		$cmd->select = $select;
    		
    		//设置多个查询语句
    		$cmd1 = clone $cmd; //累计一级服务运营商数量
    		$cmd2 = clone $cmd; //累计二级服务运营商数量
    		
    		//cmd1筛选一级服务运营商
    		$cmd1->andWhere('master.id = slave.pid');
    		
    		//cmd2筛选二级服务运营商
    		$cmd2->andWhere('master.id = slave.ppid');
    		
    		//执行sql:统计累计一级服务运营商数量
    		$list1 = $cmd1->queryAll();
    		//执行sql:统计累计二级服务运营商数量
    		$list2 = $cmd2->queryAll();
    		
    		foreach ($list1 as $k => $v) {
    			$agent_id = $v['id'];
    			$agent_count = $v['agent_count'];
    			$data[$agent_id]['total_level1_agent_count'] = $agent_count;
    		}
    		foreach ($list2 as $k => $v) {
    			$agent_id = $v['id'];
    			$agent_count = $v['agent_count'];
    			$data[$agent_id]['total_level2_agent_count'] = $agent_count;
    		}
    		
    		//统计数据处理
    		//需要计入到上级服务商的数据字段
    		$handle = array(
    				'total_trade_money', //交易金额
    				'total_receipt_money', //实收金额
    				'total_refund_money', //退款金额
    				'total_yesterday_trade_money', //昨日交易金额
    				'total_yesterday_receipt_money', //昨日实收金额
    				'total_yesterday_refund_money', //昨日退款金额
    				'total_trade_count', //交易笔数
    				'total_refund_count', //退款笔数
    				'total_yesterday_trade_count', //昨日交易笔数
    				'total_yesterday_refund_count', //昨日退款笔数
    				'alipay_trade_money', //支付宝交易金额
    				'alipay_receipt_money', //支付宝实收金额
    				'alipay_refund_money', //支付宝退款金额
    				'alipay_yesterday_trade_money', //支付宝昨日交易金额
    				'alipay_yesterday_receipt_money', //支付宝昨日实收金额
    				'alipay_yesterday_refund_money', //支付宝昨日退款金额
    				'alipay_trade_count', //支付宝交易笔数
    				'alipay_refund_count', //支付宝退款笔数
    				'alipay_yesterday_trade_count', //支付宝昨日交易笔数
    				'alipay_yesterday_refund_count', //支付宝昨日退款笔数
    				'wxpay_trade_money', //微信交易金额
    				'wxpay_receipt_money', //微信实收金额
    				'wxpay_refund_money', //微信退款金额
    				'wxpay_yesterday_trade_money', //微信昨日交易金额
    				'wxpay_yesterday_receipt_money', //微信昨日实收金额
    				'wxpay_yesterday_refund_money', //微信昨日退款金额
    				'wxpay_trade_count', //微信交易笔数
    				'wxpay_refund_count', //微信退款笔数
    				'wxpay_yesterday_trade_count', //微信昨日交易笔数
    				'wxpay_yesterday_refund_count', //微信昨日退款笔数
    				'cashpay_trade_money', //现金交易金额
    				'cashpay_receipt_money', //现金实收金额
    				'cashpay_refund_money', //现金退款金额
    				'cashpay_yesterday_trade_money', //现金昨日交易金额
    				'cashpay_yesterday_receipt_money', //现金昨日实收金额
    				'cashpay_yesterday_refund_money', //现金昨日退款金额
    				'cashpay_trade_count', //现金交易笔数
    				'cashpay_refund_count', //现金退款笔数
    				'cashpay_yesterday_trade_count', //现金昨日交易笔数
    				'cashpay_yesterday_refund_count', //现金昨日退款笔数
    				'unionpay_trade_money', //银联支付交易金额
    				'unionpay_receipt_money', //银联支付实收金额
    				'unionpay_refund_money', //银联支付退款金额
    				'unionpay_yesterday_trade_money', //银联支付昨日交易金额
    				'unionpay_yesterday_receipt_money', //银联支付昨日实收金额
    				'unionpay_yesterday_refund_money', //银联支付昨日退款金额
    				'unionpay_trade_count', //银联支付交易笔数
    				'unionpay_refund_count', //银联支付退款笔数
    				'unionpay_yesterday_trade_count', //银联支付昨日交易笔数
    				'unionpay_yesterday_refund_count', //银联支付昨日退款笔数
    				'storedpay_trade_money', //储值支付交易金额
    				'storedpay_receipt_money', //储值支付实收金额
    				'storedpay_refund_money', //储值支付退款金额
    				'storedpay_yesterday_trade_money', //储值支付昨日交易金额
    				'storedpay_yesterday_receipt_money', //储值支付昨日实收金额
    				'storedpay_yesterday_refund_money', //储值支付昨日退款金额
    				'storedpay_trade_count', //储值支付交易笔数
    				'storedpay_refund_count', //储值支付退款笔数
    				'storedpay_yesterday_trade_count', //储值支付昨日交易笔数
    				'storedpay_yesterday_refund_count', //储值支付昨日退款笔数
    				'total_coupons_count', //累计卡券核销笔数
    				'alipay_commision_money', //累计支付宝符合返佣条件金额
    				'alipay_commision_count', //累计支付宝符合返佣条件笔数
    				'wxpay_commision_money', //累计微信符合返佣条件金额
    				'wxpay_commision_count', //累计微信符合返佣条件笔数
    				'yesterday_coupons_count', //昨日卡券核销笔数
    				'alipay_yesterday_commision_money', //支付宝昨日符合返佣条件金额
    				'alipay_yesterday_commision_count', //支付宝昨日符合返佣条件笔数
    				'wxpay_yesterday_commision_money', //微信昨日符合返佣条件金额
    				'wxpay_yesterday_commision_count', //微信昨日符合返佣条件笔数
    				'total_user_count', //总客户量
    				'total_alifans_count', //支付宝粉丝总量
    				'total_wxfans_count', //微信粉丝总量
    				'total_member_count', //会员总量
    				'yesterday_user_count', //昨日新增客户量
    				'yesterday_alifans_count', //昨日新增支付宝粉丝量
    				'yesterday_wxfans_count', //昨日新增微信粉丝量
    				'yesterday_member_count', //昨日新增会员粉丝量
    				'total_store_count', //累计门店数
    				'yesterday_store_count', //昨日新增门店数
    				'active_store_count', //活跃门店数
    				'total_merchant_count', //累计商户数
    				'total_yx_merchant_count', //累计营销版商户数
    				'total_sy_merchant_count', //累计收银版商户数
    				'total_yx_service_money', //累计营销版服务费
    				'yesterday_merchant_count', //昨日新增商户数
    				'yesterday_yx_merchant_count', //昨日新增营销版商户数
    				'yesterday_sy_merchant_count', //昨日新增收银版商户数
    				'yesterday_yx_service_money', //昨日新增营销版服务费
    		);
    		//将二级服务商的部分统计数据计入到上级服务商中
    		foreach ($data as $k => $v) {
    			$pid = $v['pid'];
    			$ppid = $v['ppid'];
    			if (empty($ppid)) {
    				continue; //跳过一级服务商
    			}
    			if (!isset($data[$ppid])) {
    				continue; //跳过错误上级服务商
    			}
    			foreach ($handle as $key_name) {
    				$data[$ppid][$key_name] += $v[$key_name];
    			}
    		}
    		//将一级服务商的部分统计数据计入到上级服务商中
    		foreach ($data as $k => $v) {
    			$pid = $v['pid'];
    			$ppid = $v['ppid'];
    			if (!empty($ppid)) {
    				continue; //跳过二级服务商
    			}
    			if (!isset($data[$pid])) {
    				continue; //跳过错误上级服务商
    			}
    			foreach ($handle as $key_name) {
    				$data[$pid][$key_name] += $v[$key_name];
    			}
    		}
    		
    		foreach ($data as $k => $v) {
    			//跳过错误数据
    			if (empty($k) || empty($v)) {
    				continue;
    			}
    			
    			//保存服务商统计数据
    			$model = new AStatistics();
    			$model['create_time'] = date('Y-m-d H:i:s');
    			$model['agent_id'] = $k;
    			$model['date'] = $end_time;
    			
    			$model['total_trade_money'] = $v['total_trade_money'];
    			$model['total_trade_actual_money'] = $v['total_receipt_money'];
    			$model['total_trade_num'] = $v['total_trade_count'];
    			$model['new_trade_money'] = $v['total_yesterday_trade_money'];
    			$model['new_trade_actual_money'] = $v['total_yesterday_receipt_money'];
    			$model['new_trade_num'] = $v['total_yesterday_trade_count'];
    			$model['new_trade_refund_money'] = $v['total_yesterday_refund_money'];
    			$model['new_trade_refund_num'] = $v['total_yesterday_refund_count'];
    			
    			$model['total_trade_alipay_money'] = $v['alipay_trade_money'];
    			$model['total_trade_actual_alipay_money'] = $v['alipay_receipt_money'];
    			$model['total_trade_alipay_num'] = $v['alipay_trade_count'];
    			$model['new_trade_alipay_money'] = $v['alipay_yesterday_trade_money'];
    			$model['new_trade_actual_alipay_money'] = $v['alipay_yesterday_receipt_money'];
    			$model['new_trade_alipay_num'] = $v['alipay_yesterday_trade_count'];
    			$model['new_trade_alipay_refund_money'] = $v['alipay_yesterday_refund_money'];
    			$model['new_trade_alipay_refund_num'] = $v['alipay_yesterday_refund_count'];
    			
    			$model['total_trade_wechat_money'] = $v['wxpay_trade_money'];
    			$model['total_trade_actual_wechat_money'] = $v['wxpay_receipt_money'];
    			$model['total_trade_wechat_num'] = $v['wxpay_trade_count'];
    			$model['new_trade_wechat_money'] = $v['wxpay_yesterday_trade_money'];
    			$model['new_trade_actual_wechat_money'] = $v['wxpay_yesterday_receipt_money'];
    			$model['new_trade_wechat_num'] = $v['wxpay_yesterday_trade_count'];
    			$model['new_trade_wechat_refund_money'] = $v['wxpay_yesterday_refund_money'];
    			$model['new_trade_wechat_refund_num'] = $v['wxpay_yesterday_refund_count'];
    			
    			$model['total_trade_cash_money'] = $v['cashpay_trade_money'];
    			$model['total_trade_actual_cash_money'] = $v['cashpay_receipt_money'];
    			$model['total_trade_cash_num'] = $v['cashpay_trade_count'];
    			$model['new_trade_cash_money'] = $v['cashpay_yesterday_trade_money'];
    			$model['new_trade_actual_cash_money'] = $v['cashpay_yesterday_receipt_money'];
    			$model['new_trade_cash_num'] = $v['cashpay_yesterday_trade_count'];
    			$model['new_trade_cash_refund_money'] = $v['cashpay_yesterday_refund_money'];
    			$model['new_trade_cash_refund_num'] = $v['cashpay_yesterday_refund_count'];
    			
    			$model['total_trade_unionpay_money'] = $v['unionpay_trade_money'];
    			$model['total_trade_actual_unionpay_money'] = $v['unionpay_receipt_money'];
    			$model['total_trade_unionpay_num'] = $v['unionpay_trade_count'];
    			$model['new_trade_unionpay_money'] = $v['unionpay_yesterday_trade_money'];
    			$model['new_trade_actual_unionpay_money'] = $v['unionpay_yesterday_receipt_money'];
    			$model['new_trade_unionpay_num'] = $v['unionpay_yesterday_trade_count'];
    			$model['new_trade_unionpay_refund_money'] = $v['unionpay_yesterday_refund_money'];
    			$model['new_trade_unionpay_refund_num'] = $v['unionpay_yesterday_refund_count'];
    			
    			$model['total_trade_stored_money'] = $v['storedpay_trade_money'];
    			$model['total_trade_actual_stored_money'] = $v['storedpay_receipt_money'];
    			$model['total_trade_stored_num'] = $v['storedpay_trade_count'];
    			$model['new_trade_stored_money'] = $v['storedpay_yesterday_trade_money'];
    			$model['new_trade_actual_stored_money'] = $v['storedpay_yesterday_receipt_money'];
    			$model['new_trade_stored_num'] = $v['storedpay_yesterday_trade_count'];
    			$model['new_trade_stored_refund_money'] = $v['storedpay_yesterday_refund_money'];
    			$model['new_trade_stored_refund_num'] = $v['storedpay_yesterday_refund_count'];
    			
    			$model['total_trade_coupon_num'] = $v['total_coupons_count'];
    			$model['total_alipay_commision_money'] = $v['alipay_commision_money'];
    			$model['total_wechat_commision_money'] = $v['wxpay_commision_money'];
    			$model['total_alipay_commision_num'] = $v['alipay_commision_count'];
    			$model['total_wechat_commision_num'] = $v['wxpay_commision_count'];
    			 
    			$model['new_trade_coupon_num'] = $v['yesterday_coupons_count'];
    			$model['new_alipay_commision_money'] = $v['alipay_yesterday_commision_money'];
    			$model['new_wechat_commision_money'] = $v['wxpay_yesterday_commision_money'];
    			$model['new_alipay_commision_num'] = $v['alipay_yesterday_commision_count'];
    			$model['new_wechat_commision_num'] = $v['wxpay_yesterday_commision_count'];
    			 
    			$model['total_user_num'] = $v['total_user_count'];
    			$model['total_alipayfans_num'] = $v['total_alifans_count'];
    			$model['total_wechatfans_num'] = $v['total_wxfans_count'];
    			$model['total_member_num'] = $v['total_member_count'];
    			$model['new_user_num'] = $v['yesterday_user_count'];
    			$model['new_alipayfans_num'] = $v['yesterday_alifans_count'];
    			$model['new_wechatfans_num'] = $v['yesterday_wxfans_count'];
    			$model['new_member_num'] = $v['yesterday_member_count'];
    			 
    			$model['total_store_num'] = $v['total_store_count'];
    			$model['new_store_num'] = $v['yesterday_store_count'];
    			$model['active_store_num'] = $v['active_store_count'];
    			
    			$model['total_merchant_num'] = $v['total_merchant_count'];
    			$model['new_merchant_num'] = $v['yesterday_merchant_count'];
    			$model['total_yx_merchant_num'] = $v['total_yx_merchant_count'];
    			$model['total_sy_merchant_num'] = $v['total_sy_merchant_count'];
    			$model['new_yx_merchant_num'] = $v['yesterday_yx_merchant_count'];
    			$model['new_sy_merchant_num'] = $v['yesterday_sy_merchant_count'];
    			
    			$model['total_one_level_agent_num'] = $v['total_level1_agent_count'];
    			$model['total_two_level_agent_num'] = $v['total_level2_agent_count'];
    			
    			if (!$model->save()) {
    				throw new Exception('服务商统计数据保存失败');
    			}
    		}
    		
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
    		$result['errMsg'] = $e -> getMessage();
    	}
    	return json_encode($result);
    }
    
    /**
     * 统计全部并保存
     * @throws Exception
     * @return string
     */
    public function saveTotalStatistics() {
    	$result = array();
    	try {
    		//统计的开始时间
    		//$start_time = '';
    		//统计的结束时间：昨日的时间
    		$end_time = date('Y-m-d 23:59:59', strtotime("-1 day"));
    		
    		//获取所有服务商并初始化交易数组
    		$data = array(
    				'total_trade_money' => 0, //交易金额
    				'total_receipt_money' => 0, //实收金额
    				'total_refund_money' => 0, //退款金额
    				'total_yesterday_trade_money' => 0, //昨日交易金额
    				'total_yesterday_receipt_money' => 0, //昨日实收金额
    				'total_yesterday_refund_money' => 0, //昨日退款金额
    				'total_trade_count' => 0, //交易笔数
    				'total_refund_count' => 0, //退款笔数
    				'total_yesterday_trade_count' => 0, //昨日交易笔数
    				'total_yesterday_refund_count' => 0, //昨日退款笔数
    				'alipay_trade_money' => 0, //支付宝交易金额
    				'alipay_receipt_money' => 0, //支付宝实收金额
    				'alipay_refund_money' => 0, //支付宝退款金额
    				'alipay_yesterday_trade_money' => 0, //支付宝昨日交易金额
    				'alipay_yesterday_receipt_money' => 0, //支付宝昨日实收金额
    				'alipay_yesterday_refund_money' => 0, //支付宝昨日退款金额
    				'alipay_trade_count' => 0, //支付宝交易笔数
    				'alipay_refund_count' => 0, //支付宝退款笔数
    				'alipay_yesterday_trade_count' => 0, //支付宝昨日交易笔数
    				'alipay_yesterday_refund_count' => 0, //支付宝昨日退款笔数
    				'wxpay_trade_money' => 0, //微信交易金额
    				'wxpay_receipt_money' => 0, //微信实收金额
    				'wxpay_refund_money' => 0, //微信退款金额
    				'wxpay_yesterday_trade_money' => 0, //微信昨日交易金额
    				'wxpay_yesterday_receipt_money' => 0, //微信昨日实收金额
    				'wxpay_yesterday_refund_money' => 0, //微信昨日退款金额
    				'wxpay_trade_count' => 0, //微信交易笔数
    				'wxpay_refund_count' => 0, //微信退款笔数
    				'wxpay_yesterday_trade_count' => 0, //微信昨日交易笔数
    				'wxpay_yesterday_refund_count' => 0, //微信昨日退款笔数
    				'cashpay_trade_money' => 0, //现金交易金额
    				'cashpay_receipt_money' => 0, //现金实收金额
    				'cashpay_refund_money' => 0, //现金退款金额
    				'cashpay_yesterday_trade_money' => 0, //现金昨日交易金额
    				'cashpay_yesterday_receipt_money' => 0, //现金昨日实收金额
    				'cashpay_yesterday_refund_money' => 0, //现金昨日退款金额
    				'cashpay_trade_count' => 0, //现金交易笔数
    				'cashpay_refund_count' => 0, //现金退款笔数
    				'cashpay_yesterday_trade_count' => 0, //现金昨日交易笔数
    				'cashpay_yesterday_refund_count' => 0, //现金昨日退款笔数
    				'unionpay_trade_money' => 0, //银联支付交易金额
    				'unionpay_receipt_money' => 0, //银联支付实收金额
    				'unionpay_refund_money' => 0, //银联支付退款金额
    				'unionpay_yesterday_trade_money' => 0, //银联支付昨日交易金额
    				'unionpay_yesterday_receipt_money' => 0, //银联支付昨日实收金额
    				'unionpay_yesterday_refund_money' => 0, //银联支付昨日退款金额
    				'unionpay_trade_count' => 0, //银联支付交易笔数
    				'unionpay_refund_count' => 0, //银联支付退款笔数
    				'unionpay_yesterday_trade_count' => 0, //银联支付昨日交易笔数
    				'unionpay_yesterday_refund_count' => 0, //银联支付昨日退款笔数
    				'storedpay_trade_money' => 0, //储值支付交易金额
    				'storedpay_receipt_money' => 0, //储值支付实收金额
    				'storedpay_refund_money' => 0, //储值支付退款金额
    				'storedpay_yesterday_trade_money' => 0, //储值支付昨日交易金额
    				'storedpay_yesterday_receipt_money' => 0, //储值支付昨日实收金额
    				'storedpay_yesterday_refund_money' => 0, //储值支付昨日退款金额
    				'storedpay_trade_count' => 0, //储值支付交易笔数
    				'storedpay_refund_count' => 0, //储值支付退款笔数
    				'storedpay_yesterday_trade_count' => 0, //储值支付昨日交易笔数
    				'storedpay_yesterday_refund_count' => 0, //储值支付昨日退款笔数
    				'total_coupons_count' => 0, //累计卡券核销笔数
    				'alipay_commision_money' => 0, //累计支付宝符合返佣条件金额
    				'alipay_commision_count' => 0, //累计支付宝符合返佣条件笔数
    				'wxpay_commision_money' => 0, //累计微信符合返佣条件金额
    				'wxpay_commision_count' => 0, //累计微信符合返佣条件笔数
    				'yesterday_coupons_count' => 0, //昨日卡券核销笔数
    				'alipay_yesterday_commision_money' => 0, //支付宝昨日符合返佣条件金额
    				'alipay_yesterday_commision_count' => 0, //支付宝昨日符合返佣条件笔数
    				'wxpay_yesterday_commision_money' => 0, //微信昨日符合返佣条件金额
    				'wxpay_yesterday_commision_count' => 0, //微信昨日符合返佣条件笔数
    				'total_user_count' => 0, //总客户量
    				'total_alifans_count' => 0, //支付宝粉丝总量
    				'total_wxfans_count' => 0, //微信粉丝总量
    				'total_member_count' => 0, //会员总量
    				'yesterday_user_count' => 0, //昨日新增客户量
    				'yesterday_alifans_count' => 0, //昨日新增支付宝粉丝量
    				'yesterday_wxfans_count' => 0, //昨日新增微信粉丝量
    				'yesterday_member_count' => 0, //昨日新增会员粉丝量
    				'total_store_count' => 0, //累计门店数
    				'yesterday_store_count' => 0, //昨日新增门店数
    				'active_store_count' => 0, //活跃门店数
    				'total_merchant_count' => 0, //累计商户数
    				'total_yx_merchant_count' => 0, //累计营销版商户数
    				'total_sy_merchant_count' => 0, //累计收银版商户数
    				'total_yx_service_money' => 0, //累计营销版服务费
    				'yesterday_merchant_count' => 0, //昨日新增商户数
    				'yesterday_yx_merchant_count' => 0, //昨日新增营销版商户数
    				'yesterday_sy_merchant_count' => 0, //昨日新增收银版商户数
    				'yesterday_yx_service_money' => 0, //昨日新增营销版服务费
    				'total_level1_agent_count' => 0, //累计一级服务商数量
    				'total_level1_agent_fee' => 0, //累计一级服务商佣金
    				'total_level2_agent_count' => 0, //累计二级服务商数量
    				'total_level2_agent_fee' => 0, //累计二级服务商佣金
    				'total_agent_count' => 0, //累计服务商数
    				'yesterday_agent_count' => 0, //新增服务商数
    		);
    		
    		//统计所有的交易数据
    		$cmd = Yii::app()->db->createCommand();
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('date = :date');
    		$cmd->params[':date'] = $end_time;
    		
    		//指定查询表
    		$cmd->from = 'wq_a_statistics';
    		//查询计算
    		$select = 'id';
    		$column = array(
    				'total_trade_money','total_trade_actual_money','total_trade_num',
    				'new_trade_money','new_trade_actual_money','new_trade_num',
    				'new_trade_refund_money','new_trade_refund_num',
    				'total_trade_alipay_money','total_trade_actual_alipay_money','total_trade_alipay_num',
    				'new_trade_alipay_money','new_trade_actual_alipay_money','new_trade_alipay_num',
    				'new_trade_alipay_refund_money','new_trade_alipay_refund_num',
    				'total_trade_wechat_money','total_trade_actual_wechat_money','total_trade_wechat_num',
    				'new_trade_wechat_money','new_trade_actual_wechat_money','new_trade_wechat_num',
    				'new_trade_wechat_refund_money','new_trade_wechat_refund_num',
    				'total_trade_unionpay_money','total_trade_actual_unionpay_money','total_trade_unionpay_num',
    				'new_trade_unionpay_money','new_trade_actual_unionpay_money','new_trade_unionpay_num',
    				'new_trade_unionpay_refund_money','new_trade_unionpay_refund_num',
    				'total_trade_stored_money','total_trade_actual_stored_money','total_trade_stored_num',
    				'new_trade_stored_money','new_trade_actual_stored_money','new_trade_stored_num',
    				'new_trade_stored_refund_money','new_trade_stored_refund_num',
    				'total_trade_cash_money','total_trade_actual_cash_money','total_trade_cash_num',
    				'new_trade_cash_money','new_trade_actual_cash_money','new_trade_cash_num',
    				'new_trade_cash_refund_money','new_trade_cash_refund_num',
    				'total_trade_coupon_num',
    				'total_alipay_commision_money','total_wechat_commision_money',
    				'total_alipay_commision_num','total_wechat_commision_num',
    				'new_trade_coupon_num',
    				'new_alipay_commision_money','new_wechat_commision_money',
    				'new_alipay_commision_num','new_wechat_commision_num',
    				'total_user_num','total_alipayfans_num','total_wechatfans_num','total_member_num',
    				'new_user_num','new_alipayfans_num','new_wechatfans_num','new_member_num',
    				'total_store_num','new_store_num','active_store_num',
    				'total_merchant_num','new_merchant_num',
    				'total_yx_merchant_num','total_sy_merchant_num',
    				'new_yx_merchant_num','new_sy_merchant_num',
    				'total_yx_servicecharge','new_yx_servicecharge'
    		);
    		foreach ($column as $name) {
    			$select .= ", SUM($name) AS $name";
    		}
    		$cmd->select = $select;
    		
    		//执行sql查询:统计所有的交易数据
    		$list = $cmd->queryRow();
    		
    		$data['total_trade_money'] = $list['total_trade_money'];
    		$data['total_receipt_money'] = $list['total_trade_actual_money'];
    		$data['total_trade_count'] = $list['total_trade_num'];
    		$data['total_yesterday_trade_money'] = $list['new_trade_money'];
    		$data['total_yesterday_receipt_money'] = $list['new_trade_actual_money'];
    		$data['total_yesterday_refund_money'] = $list['new_trade_refund_money'];
    		$data['total_yesterday_trade_count'] = $list['new_trade_num'];
    		$data['total_yesterday_refund_count'] = $list['new_trade_refund_num'];
    		
    		$data['alipay_trade_money'] = $list['total_trade_alipay_money'];
    		$data['alipay_receipt_money'] = $list['total_trade_actual_alipay_money'];
    		$data['alipay_trade_count'] = $list['total_trade_alipay_num'];
    		$data['alipay_yesterday_trade_money'] = $list['new_trade_alipay_money'];
    		$data['alipay_yesterday_receipt_money'] = $list['new_trade_actual_alipay_money'];
    		$data['alipay_yesterday_refund_money'] = $list['new_trade_alipay_refund_money'];
    		$data['alipay_yesterday_trade_count'] = $list['new_trade_alipay_num'];
    		$data['alipay_yesterday_refund_count'] = $list['new_trade_alipay_refund_num'];
    		
    		$data['wxpay_trade_money'] = $list['total_trade_wechat_money'];
    		$data['wxpay_receipt_money'] = $list['total_trade_actual_wechat_money'];
    		$data['wxpay_trade_count'] = $list['total_trade_wechat_num'];
    		$data['wxpay_yesterday_trade_money'] = $list['new_trade_wechat_money'];
    		$data['wxpay_yesterday_receipt_money'] = $list['new_trade_actual_wechat_money'];
    		$data['wxpay_yesterday_refund_money'] = $list['new_trade_wechat_refund_money'];
    		$data['wxpay_yesterday_trade_count'] = $list['new_trade_wechat_num'];
    		$data['wxpay_yesterday_refund_count'] = $list['new_trade_wechat_refund_num'];
    		
    		$data['cashpay_trade_money'] = $list['total_trade_cash_money'];
    		$data['cashpay_receipt_money'] = $list['total_trade_actual_cash_money'];
    		$data['cashpay_trade_count'] = $list['total_trade_cash_num'];
    		$data['cashpay_yesterday_trade_money'] = $list['new_trade_cash_money'];
    		$data['cashpay_yesterday_receipt_money'] = $list['new_trade_actual_cash_money'];
    		$data['cashpay_yesterday_refund_money'] = $list['new_trade_cash_refund_money'];
    		$data['cashpay_yesterday_trade_count'] = $list['new_trade_cash_num'];
    		$data['cashpay_yesterday_refund_count'] = $list['new_trade_cash_refund_num'];
    		
    		$data['unionpay_trade_money'] = $list['total_trade_unionpay_money'];
    		$data['unionpay_receipt_money'] = $list['total_trade_actual_unionpay_money'];
    		$data['unionpay_trade_count'] = $list['total_trade_unionpay_num'];
    		$data['unionpay_yesterday_trade_money'] = $list['new_trade_unionpay_money'];
    		$data['unionpay_yesterday_receipt_money'] = $list['new_trade_actual_unionpay_money'];
    		$data['unionpay_yesterday_refund_money'] = $list['new_trade_unionpay_refund_money'];
    		$data['unionpay_yesterday_trade_count'] = $list['new_trade_unionpay_num'];
    		$data['unionpay_yesterday_refund_count'] = $list['new_trade_unionpay_refund_num'];
    		
    		$data['storedpay_trade_money'] = $list['total_trade_stored_money'];
    		$data['storedpay_receipt_money'] = $list['total_trade_actual_stored_money'];
    		$data['storedpay_trade_count'] = $list['total_trade_stored_num'];
    		$data['storedpay_yesterday_trade_money'] = $list['new_trade_stored_money'];
    		$data['storedpay_yesterday_receipt_money'] = $list['new_trade_actual_stored_money'];
    		$data['storedpay_yesterday_refund_money'] = $list['new_trade_stored_refund_money'];
    		$data['storedpay_yesterday_trade_count'] = $list['new_trade_stored_num'];
    		$data['storedpay_yesterday_refund_count'] = $list['new_trade_stored_refund_num'];
    		 
    		$data['total_coupons_count'] = $list['total_trade_coupon_num'];
    		$data['alipay_commision_money'] = $list['total_alipay_commision_money'];
    		$data['alipay_commision_count'] = $list['total_alipay_commision_num'];
    		$data['wxpay_commision_money'] = $list['total_wechat_commision_money'];
    		$data['wxpay_commision_count'] = $list['total_wechat_commision_num'];
    		 
    		$data['yesterday_coupons_count'] = $list['new_trade_coupon_num'];
    		$data['alipay_yesterday_commision_money'] = $list['new_alipay_commision_money'];
    		$data['alipay_yesterday_commision_count'] = $list['new_alipay_commision_num'];
    		$data['wxpay_yesterday_commision_money'] = $list['new_wechat_commision_money'];
    		$data['wxpay_yesterday_commision_count'] = $list['new_wechat_commision_num'];
    		 
    		$data['total_user_count'] = $list['total_user_num'];
    		$data['total_alifans_count'] = $list['total_alipayfans_num'];
    		$data['total_wxfans_count'] = $list['total_wechatfans_num'];
    		$data['total_member_count'] = $list['total_member_num'];
    		$data['yesterday_user_count'] = $list['new_user_num'];
    		$data['yesterday_alifans_count'] = $list['new_alipayfans_num'];
    		$data['yesterday_wxfans_count'] = $list['new_wechatfans_num'];
    		$data['yesterday_member_count'] = $list['new_member_num'];
    		 
    		$data['total_store_count'] = $list['total_store_num'];
    		$data['yesterday_store_count'] = $list['new_store_num'];
    		$data['active_store_count'] = $list['active_store_num'];
    		
    		$data['total_merchant_count'] = $list['total_merchant_num'];
    		$data['total_yx_merchant_count'] = $list['total_yx_merchant_num'];
    		$data['total_sy_merchant_count'] = $list['total_sy_merchant_num'];
    		
    		$data['yesterday_merchant_count'] = $list['new_merchant_num'];
    		$data['yesterday_yx_merchant_count'] = $list['new_yx_merchant_num'];
    		$data['yesterday_sy_merchant_count'] = $list['new_sy_merchant_num'];
    		
    		$data['total_yx_service_money'] = $list['total_yx_servicecharge'];
    		$data['yesterday_yx_service_money'] = $list['new_yx_servicecharge'];
    		
    		//获取服务商统计数量
    		$cmd = Yii::app()->db->createCommand();
    		//查询条件
    		$cmd->andWhere('flag = :flag');
    		$cmd->params[':flag'] = FLAG_NO;
    		$cmd->andWhere('create_time <= :end_time');
    		$cmd->params[':end_time'] = $end_time;
    		
    		//指定查询表
    		$cmd->from = 'wq_agent';
    		//查询
    		$select = 'COUNT(*) AS agent_count';
    		$cmd->select = $select;
    		
    		//设置多个查询语句
    		$cmd1 = clone $cmd; //累计服务运营商数量
    		$cmd2 = clone $cmd; //昨日新增服务运营商数量
    		
    		//cmd2筛选昨日服务运营商
    		$cmd2->andWhere('create_time >= :create_time');
    		$cmd2->params[':create_time'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
    		
    		//执行sql:统计累计服务运营商数量
    		$list1 = $cmd1->queryRow();
    		//执行sql:统计昨日新增服务运营商数量
    		$list2 = $cmd2->queryRow();
    		
    		$data['total_agent_count'] = $list1['agent_count'];
    		$data['yesterday_agent_count'] = $list2['agent_count'];
    		
    		//保存总统计数据
    		$model = new TotalStatistics();
    		$model['create_time'] = date('Y-m-d H:i:s');
    		$model['date'] = $end_time;
    		
    		$model['total_trade_money'] = $data['total_trade_money'];
    		$model['total_trade_actual_money'] = $data['total_receipt_money'];
    		$model['total_trade_num'] = $data['total_trade_count'];
    		$model['new_trade_money'] = $data['total_yesterday_trade_money'];
    		$model['new_trade_actual_money'] = $data['total_yesterday_receipt_money'];
    		$model['new_trade_num'] = $data['total_yesterday_trade_count'];
    		$model['new_trade_refund_money'] = $data['total_yesterday_refund_money'];
    		$model['new_trade_refund_num'] = $data['total_yesterday_refund_count'];
    		 
    		$model['total_trade_alipay_money'] = $data['alipay_trade_money'];
    		$model['total_trade_actual_alipay_money'] = $data['alipay_receipt_money'];
    		$model['total_trade_alipay_num'] = $data['alipay_trade_count'];
    		$model['new_trade_alipay_money'] = $data['alipay_yesterday_trade_money'];
    		$model['new_trade_actual_alipay_money'] = $data['alipay_yesterday_receipt_money'];
    		$model['new_trade_alipay_num'] = $data['alipay_yesterday_trade_count'];
    		$model['new_trade_alipay_refund_money'] = $data['alipay_yesterday_refund_money'];
    		$model['new_trade_alipay_refund_num'] = $data['alipay_yesterday_refund_count'];
    		 
    		$model['total_trade_wechat_money'] = $data['wxpay_trade_money'];
    		$model['total_trade_actual_wechat_money'] = $data['wxpay_receipt_money'];
    		$model['total_trade_wechat_num'] = $data['wxpay_trade_count'];
    		$model['new_trade_wechat_money'] = $data['wxpay_yesterday_trade_money'];
    		$model['new_trade_actual_wechat_money'] = $data['wxpay_yesterday_receipt_money'];
    		$model['new_trade_wechat_num'] = $data['wxpay_yesterday_trade_count'];
    		$model['new_trade_wechat_refund_money'] = $data['wxpay_yesterday_refund_money'];
    		$model['new_trade_wechat_refund_num'] = $data['wxpay_yesterday_refund_count'];
    		 
    		$model['total_trade_cash_money'] = $data['cashpay_trade_money'];
    		$model['total_trade_actual_cash_money'] = $data['cashpay_receipt_money'];
    		$model['total_trade_cash_num'] = $data['cashpay_trade_count'];
    		$model['new_trade_cash_money'] = $data['cashpay_yesterday_trade_money'];
    		$model['new_trade_actual_cash_money'] = $data['cashpay_yesterday_receipt_money'];
    		$model['new_trade_cash_num'] = $data['cashpay_yesterday_trade_count'];
    		$model['new_trade_cash_refund_money'] = $data['cashpay_yesterday_refund_money'];
    		$model['new_trade_cash_refund_num'] = $data['cashpay_yesterday_refund_count'];
    		 
    		$model['total_trade_unionpay_money'] = $data['unionpay_trade_money'];
    		$model['total_trade_actual_unionpay_money'] = $data['unionpay_receipt_money'];
    		$model['total_trade_unionpay_num'] = $data['unionpay_trade_count'];
    		$model['new_trade_unionpay_money'] = $data['unionpay_yesterday_trade_money'];
    		$model['new_trade_actual_unionpay_money'] = $data['unionpay_yesterday_receipt_money'];
    		$model['new_trade_unionpay_num'] = $data['unionpay_yesterday_trade_count'];
    		$model['new_trade_unionpay_refund_money'] = $data['unionpay_yesterday_refund_money'];
    		$model['new_trade_unionpay_refund_num'] = $data['unionpay_yesterday_refund_count'];
    		 
    		$model['total_trade_stored_money'] = $data['storedpay_trade_money'];
    		$model['total_trade_actual_stored_money'] = $data['storedpay_receipt_money'];
    		$model['total_trade_stored_num'] = $data['storedpay_trade_count'];
    		$model['new_trade_stored_money'] = $data['storedpay_yesterday_trade_money'];
    		$model['new_trade_actual_stored_money'] = $data['storedpay_yesterday_receipt_money'];
    		$model['new_trade_stored_num'] = $data['storedpay_yesterday_trade_count'];
    		$model['new_trade_stored_refund_money'] = $data['storedpay_yesterday_refund_money'];
    		$model['new_trade_stored_refund_num'] = $data['storedpay_yesterday_refund_count'];
    		 
    		$model['total_trade_coupon_num'] = $data['total_coupons_count'];
    		$model['total_alipay_commision_money'] = $data['alipay_commision_money'];
    		$model['total_wechat_commision_money'] = $data['wxpay_commision_money'];
    		$model['total_alipay_commision_num'] = $data['alipay_commision_count'];
    		$model['total_wechat_commision_num'] = $data['wxpay_commision_count'];
    		
    		$model['new_trade_coupon_num'] = $data['yesterday_coupons_count'];
    		$model['new_alipay_commision_money'] = $data['alipay_yesterday_commision_money'];
    		$model['new_wechat_commision_money'] = $data['wxpay_yesterday_commision_money'];
    		$model['new_alipay_commision_num'] = $data['alipay_yesterday_commision_count'];
    		$model['new_wechat_commision_num'] = $data['wxpay_yesterday_commision_count'];
    		
    		$model['total_user_num'] = $data['total_user_count'];
    		$model['total_alipayfans_num'] = $data['total_alifans_count'];
    		$model['total_wechatfans_num'] = $data['total_wxfans_count'];
    		$model['total_member_num'] = $data['total_member_count'];
    		$model['new_user_num'] = $data['yesterday_user_count'];
    		$model['new_alipayfans_num'] = $data['yesterday_alifans_count'];
    		$model['new_wechatfans_num'] = $data['yesterday_wxfans_count'];
    		$model['new_member_num'] = $data['yesterday_member_count'];
    		
    		$model['total_store_num'] = $data['total_store_count'];
    		$model['new_store_num'] = $data['yesterday_store_count'];
    		$model['active_store_num'] = $data['active_store_count'];
    		 
    		$model['total_merchant_num'] = $data['total_merchant_count'];
    		$model['new_merchant_num'] = $data['yesterday_merchant_count'];
    		$model['total_yx_merchant_num'] = $data['total_yx_merchant_count'];
    		$model['total_sy_merchant_num'] = $data['total_sy_merchant_count'];
    		$model['new_yx_merchant_num'] = $data['yesterday_yx_merchant_count'];
    		$model['new_sy_merchant_num'] = $data['yesterday_sy_merchant_count'];
    		
    		$model['total_yx_servicecharge'] = $data['total_yx_service_money'];
    		$model['new_yx_servicecharge'] = $data['yesterday_yx_service_money'];
    		
    		$model['total_agent_num'] = $data['total_agent_count'];
    		$model['new_agent_num'] = $data['yesterday_agent_count'];
    		
    		if (!$model->save()) {
    			throw new Exception('总统计数据保存失败');
    		}
    		
    		$result['status'] = ERROR_NONE; //状态码
    		$result['errMsg'] = ''; //错误信息
    	} catch (Exception $e) {
    		$result['status'] = isset($result['status'])?$result['status']:ERROR_EXCEPTION;
    		$result['errMsg'] = $e -> getMessage();
    	}
    	
    	return json_encode($result);
    }
    
}

