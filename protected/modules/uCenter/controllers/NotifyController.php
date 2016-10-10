<?php

/**
 * User: 钮飞虎
 * Date: 2015/7/29
 * Time: 14:47
 */
class NotifyController extends UCenterController
{
    /**
     * 异步页面跳转路径
     */
    public function actionAsyNotify()
    {
        Yii::import('application.extensions.wappay.*');
        require_once("lib/alipay_notify.class.php");
        require_once("alipay.config.php");
        $encrypt_id = $_GET['encrypt_id'];
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchantId = $res->data->id;
        //$merchantId = Yii::app()->session['merchant_id'];
        //查询商户pid
        $user = new UserUC();
        $result_merchant = $user->findMerchantPid($merchantId);
        $alipay_config['partner'] = $result_merchant->partner;
        $alipay_config['key'] = $result_merchant->key;
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            //$_POST['trade_status'] == 'TRADE_FINISHED' ||
            if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //商城订单
                if (strstr($out_trade_no, 'SC')) {
                    $user = new UserUC();
                    $pay_channel = ORDER_PAY_CHANNEL_ALIPAY;
                    //调用数据库事务方法
                    $rs = json_decode($user->ScPaySuccess($out_trade_no, $trade_no, $pay_channel));
                    if ($rs->status == ERROR_NONE) {
                        echo 'order update success';
                    } else {
                        echo 'order update fail';
                    }
                } else {
                    //交易成功
                    $stored_order_id = $out_trade_no;
                    $user = new UserUC();
                    //调用数据库事务方法
                    $rs = $user->WapTranscation($stored_order_id, $trade_no);
                    if ($rs) {
                        echo 'order update success';
                    } else {
                        //储值失败
                        echo 'order update fail';
                    }
                }
            } else {
                echo "success";        //请不要修改或删除
            }
        } else {
            echo "验证失败";
        }

    }

    /*
     * 页面跳转同步通知页面路径
     */
    public function actionSynNotify()
    {
        Yii::import('application.extensions.wappay.*');
        require_once("lib/alipay_notify.class.php");
        require_once("alipay.config.php");
        $encrypt_id = $_GET['encrypt_id'];
        $merchant = new MerchantC();
        $res = json_decode($merchant->getMerchantByEncrypt($encrypt_id));
        $merchantId = $res->data->id;
        //$merchantId = Yii::app()->session['merchant_id'];
        //查询商户pid
        $user = new UserUC();
        $result_merchant = $user->findMerchantPid($merchantId);
        $alipay_config['partner'] = $result_merchant->partner;
        $alipay_config['key'] = $result_merchant->key;

        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //交易状态
            $trade_status = $_GET['trade_status'];
            //$trade_status == 'TRADE_FINISHED' ||
            if ($trade_status == 'TRADE_SUCCESS') {
                if (strstr($out_trade_no, 'SC')) {
                    $user = new UserUC();
                    $pay_channel = ORDER_PAY_CHANNEL_ALIPAY;
                    //调用数据库事务方法
                    $rs = json_decode($user->ScPaySuccess($out_trade_no, $trade_no, $pay_channel));
                    if ($rs->status == ERROR_NONE) {
                        $this->redirect(Yii::app()->createUrl('uCenter/user/paysuccess', array(
                            'money' => $rs->data,
                            'ordertype' => 'SC',
                            'encrypt_id' => $encrypt_id
                        )));
                    } else {
                        $this->redirect(Yii::app()->createUrl('uCenter/user/fail'));
                    }

                } else {
                    //交易成功
                    $stored_order_id = $out_trade_no;
                    $user = new UserUC();
                    //调用数据库事务方法
                    $rs = $user->WapTranscation($stored_order_id, $trade_no);
                    if ($rs) {
                        $this->redirect(Yii::app()->createUrl('uCenter/user/paysuccess', array(
                            'money' => $rs,
                            'encrypt_id' => $encrypt_id
                        )));
                    } else {
                        $this->redirect(Yii::app()->createUrl('uCenter/user/fail'));
                    }
                }
            } else {
                $this->redirect(Yii::app()->createUrl('uCenter/user/fail'));
            }
        } else {
            //验证失败
            echo "fail";
        }
    }

        /*
     *微信异步通知接受处理
     */
    public function actionWxPayNotify(){
        Yii::import('application.extensions.wxpay.*');
        require_once "WxPayNotify.php";
        $wxpay = new WxpayC();
        $notify = new Notify_pub();
        $xml = file_get_contents("php://input");
        $notify -> saveData($xml);
        //xml解析
        libxml_disable_entity_loader(true);
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $out_trade_no = $arr['out_trade_no']; //获取订单号
        $return_code = $arr['return_code']; //结果码
        $trade_no = $arr['transaction_id'];
        $result_code = $arr['result_code']; //业务结果码
        if ($return_code == "FAIL") {
            //通信出错
            return;
        }elseif($return_code == "SUCCESS"){
            $result = json_decode($wxpay -> getkeyForStoredOrder($out_trade_no));
            if($result->status == ERROR_NONE){
                $key = $result -> data;
                $wxpay_merchant_type = $result -> wxpay_merchant_type;//微信支付的商户类型
            }
            if($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {//如果是自助商户执行
                if($notify->checkSign($key) == FALSE) {
                    $notify->setReturnParameter("return_code","FAIL");//返回状态码
                    $notify->setReturnParameter("return_msg","签名失败");//返回信息
                }else{  
                    $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
                    $notify->setReturnParameter("return_msg","OK");//返回信息
                }
                $returnXml = $notify->returnXml();
                echo $returnXml;    
                
                if($notify->checkSign($key) == TRUE){
                    if($result_code == "FAIL"){
                     	//交易业务出错
                     	return;
                 	}elseif($result_code == "SUCCESS"){
                     	//交易成功
                      	$stored_order_id = $out_trade_no;                                    
                        Yii::log('储值订单：'.$stored_order_id,'warning');
                        $user=new UserUC();
                        //调用数据库事务方法
                        $rs = $user -> WapTranscation($stored_order_id,$trade_no);
              		} 
        		}
            }
            if($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE){ //如果是特约商户执行
                if($result_code == "FAIL"){
                    //交易业务出错
                    return;
                }elseif($result_code == "SUCCESS"){
                    //交易成功
                    $stored_order_id = $out_trade_no;                                    
                    Yii::log('储值订单（特约）：'.$stored_order_id,'warning');
                    $user=new UserUC();
                    //调用数据库事务方法
                    $rs = $user -> WapTranscation($stored_order_id,$trade_no);
                }                    
            }
       	}
    }
}

?>