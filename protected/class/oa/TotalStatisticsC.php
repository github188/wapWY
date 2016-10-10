<?php
include_once(dirname(__FILE__) . '/../mainClass.php');
/**
 * oa 统计类
 * User: ly
 * Date: 2016/5/17
 * Time: 14:49
 */
class TotalStatisticsC extends mainClass
{
    private static $_instance = null;

    public static function getInstance(){
        if (!self::$_instance instanceof self){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //获取所有数据
    public function getAllTrade() {
        $result = array();
        try{
            $model = TotalStatistics::model() -> find('date like :date and flag = :flag', array(
                ':date' => date("Y-m-d", strtotime("-1 day")) . '%',
                ':flag' => FLAG_NO,
            ));

            $data = array();
            if (!empty($model)) {
                $data['id'] = $model -> id;
                $data['date'] = $model -> date;
                $data['total_agent_num'] = $model -> total_agent_num; //累计加盟商数量
                $data['new_agent_num'] = $model -> new_agent_num; //新增加盟商数量
                $data['total_merchant_num'] = $model -> total_merchant_num; //累计商户数量
                $data['total_yx_merchant_num'] = $model -> total_yx_merchant_num; //累计营销版商户数量
                $data['total_sy_merchant_num'] = $model -> total_sy_merchant_num; //累计收银版商户数量
                $data['new_merchant_num'] = $model -> new_merchant_num; //新增商户数量
                $data['new_yx_merchant_num'] = $model -> new_yx_merchant_num; //新增营销版商户数
                $data['new_sy_merchant_num'] = $model -> new_sy_merchant_num; //新增收银版商户数
                $data['total_trade_money'] = $model -> total_trade_money; //累计交易金额
                $data['total_trade_num'] = $model -> total_trade_num; //累计交易笔数
                $data['total_actual_trade_money'] = $model -> total_actual_trade_money; //累计实际交易金额
                $data['new_trade_money'] = $model -> new_trade_money; //新增交易金额
                $data['new_trade_num'] = $model -> new_trade_num; //新增交易数量
                $data['new_actual_trade_money'] = $model -> new_actual_trade_money; //新增实际交易金额
                $data['new_trade_refund_money'] = $model -> new_trade_refund_money; //新增退款金额
                $data['new_trade_refund_num'] = $model -> new_trade_refund_num; //新增退款笔数
                $data['total_trade_alipay_money'] = $model -> total_trade_alipay_money; //累计支付宝交易金额
                $data['total_trade_alipay_num'] = $model -> total_trade_alipay_num; //累计支付宝交易数量
                $data['total_actual_alipay_money'] = $model -> total_actual_alipay_money; //累计支付宝实际交易金额
                $data['new_trade_alipay_money'] = $model -> new_trade_alipay_money; //新增支付宝交易金额
                $data['new_trade_alipay_num'] = $model -> new_trade_alipay_num; //新增支付宝数量
                $data['new_actual_alipay_money'] = $model -> new_actual_alipay_money; //新增实际支付宝交易金额
                $data['new_alipay_refund_money'] = $model -> new_alipay_refund_money; //新增支付宝退款金额
                $data['new_alipay_refund_num'] = $model -> new_alipay_refund_num; //新增支付宝退款笔数
                $data['total_trade_wechat_money'] = $model -> total_trade_wechat_money; //累计微信交易金额
                $data['total_trade_wechat_num'] = $model -> total_trade_wechat_num; //累计微信交易数量
                $data['total_actual_wechat_money'] = $model -> total_actual_wechat_money; //累计微信实际交易金额
                $data['new_trade_wechat_money'] = $model -> new_trade_wechat_money; //新增微信交易金额
                $data['new_trade_wechat_num'] = $model -> new_trade_wechat_num; //新增微信交易数量
                $data['new_actual_wechat_money'] = $model -> new_actual_wechat_money; //新增微信实际交易金额
                $data['new_wechat_refund_money'] = $model -> new_wechat_refund_money; //新增微信退款金额
                $data['new_wechat_refund_num'] = $model -> new_wechat_refund_num; //新增微信退款笔数
                $data['total_trade_unionpay_money'] = $model -> total_trade_unionpay_money; //累计银联交易金额
                $data['total_trade_unionpay_num'] = $model -> total_trade_unionpay_num; //累计银联交易数量
                $data['total_actual_unionpay_money'] = $model -> total_actual_unionpay_money; //累计银联实际交易金额
                $data['new_trade_unionpay_money'] = $model -> new_trade_unionpay_money; //新增银联交易数量
                $data['new_trade_unionpay_num'] = $model -> new_trade_unionpay_num; //新增银联交易数量
                $data['new_actual_unionpay_money'] = $model -> new_actual_unionpay_money; //新增银联实际交易金额
                $data['new_unionpay_refund_money'] = $model -> new_unionpay_refund_money; //新增银联退款金额
                $data['new_unionpay_refund_num'] = $model -> new_unionpay_refund_num; //新增银联退款笔数
                $data['total_trade_stored_money'] = $model -> total_trade_stored_money; //累计储值交易金额
                $data['total_trade_stored_num'] = $model -> total_trade_stored_num; //累计储值交易笔数
                $data['total_actual_stored_money'] = $model -> total_actual_stored_money; //累计储值实际交易金额
                $data['new_trade_stored_money'] = $model -> new_trade_stored_money; //新增储值交易金额
                $data['new_trade_stored_num'] = $model -> new_trade_stored_num; //新增储值交易笔数
                $data['new_actual_stored_money'] = $model -> new_actual_stored_money; //新增储值实际交易金额
                $data['new_stored_refund_money'] = $model -> new_stored_refund_money; //新增储值退款金额
                $data['new_stored_refund_num'] = $model -> new_stored_refund_num; //新增储值退款笔数
                $data['total_trade_cash_money'] = $model -> total_trade_cash_money; //累计现金交易金额
                $data['total_trade_cash_num'] = $model -> total_trade_cash_num; //累计现金交易笔数
                $data['total_actual_cash_money'] = $model -> total_actual_cash_money; //累计现金实际交易金额
                $data['new_trade_cash_money'] = $model -> new_trade_cash_money; //新增现金交易金额
                $data['new_trade_cash_num'] = $model -> new_trade_cash_num; //新增现金交易笔数
                $data['new_actual_cash_money'] = $model -> new_actual_cash_money; //新增现金实际交易金额
                $data['new_cash_refund_money'] = $model -> new_cash_refund_money; //新增现金退款金额
                $data['new_cash_refund_num'] = $model -> new_cash_refund_num; //新增现金退款笔数
                $data['total_user_num'] = $model -> total_user_num; //累计客户数量
                $data['total_alipayfans_num'] = $model -> total_alipayfans_num; //累计支付宝粉丝数量
                $data['total_wechatfans_num'] = $model -> total_wechatfans_num; //累计微信粉丝数量
                $data['total_member_num'] = $model -> total_member_num; //累计会员数量
                $data['new_user_num'] = $model -> new_user_num; //新增客户数量
                $data['new_alipayfans_num'] = $model -> new_alipayfans_num; //新增支付宝粉丝数量
                $data['new_wechatfans_num'] = $model -> new_wechatfans_num; //新增微信粉丝数量
                $data['new_member_num'] = $model -> new_member_num; //新增会员数量
                $data['total_store_num'] = $model -> total_store_num; //累计门店数量
                $data['new_store_num'] = $model -> new_store_num; //新增门店数
                $data['day_active_store_num'] = $model -> day_active_store_num; //今日活跃门店数
                $data['day_alipay_commision_money'] = $model -> day_alipay_commision_money; //今日符合支付宝返佣条件金额
                $data['day_wechat_commision_money'] = $model -> day_wechat_commision_money; //今日符合微信返佣条件金额
                $data['day_alipay_commision_num'] = $model -> day_alipay_commision_num; //今日符合支付宝返佣条件的笔数
                $data['day_wechat_commision_num'] = $model -> day_wechat_commision_num; //今日符合微信返佣条件的笔数
                $data['new_trade_coupon_num'] = $model -> new_trade_coupon_num; //新增券核销笔数
                $data['total_trade_coupon_num'] = $model -> total_trade_coupon_num; //累计券核销笔数
                $data['total_alipay_commision_money'] = $model -> total_alipay_commision_money; //累计符合支付宝返佣条件金额
                $data['total_wechat_commision_money'] = $model -> total_wechat_commision_money; //累计符合微信返佣条件金额
                $data['total_alipay_commision_num'] = $model -> total_alipay_commision_num; //累计符合支付宝返佣条件的笔数
                $data['total_wechat_commision_num'] = $model -> total_wechat_commision_num; //累计符合微信返佣条件的笔数
                $data['total_yx_servicecharge'] = $model -> total_yx_servicecharge; //累计营销版服务费
                $data['new_yx_servicecharge'] = $model -> new_yx_servicecharge; //今日新增营销版服务费
                $data['create_time'] = $model -> create_time; //创建时间
                $data['last_time'] = $model -> last_time; //修改时间
                $data['flag'] = $model -> flag; //删除标志位

                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            }else{
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无此数据');
            }

        } catch (Exception $e) {
            $result['data'] = '';
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }






}