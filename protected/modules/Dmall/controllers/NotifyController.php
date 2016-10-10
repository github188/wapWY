<?php
/**
 * User: 钮飞虎
 * Date: 2015/7/29
 * Time: 14:47
 */
class NotifyController extends DMallController
{
    /**
     * 异步页面跳转路径
     */
    public function actionAsyNotify()
    {
        Yii::import('application.extensions.wappay.*');
        require_once("lib/alipay_notify.class.php");
        require_once("alipay.config.php");
        $merchantId = Yii::app()->session['merchant_id'];
        //查询商户pid
        $user = new UserUC();
        $result_merchant = $user->findMerchantPid($merchantId);
        $alipay_config['partner']=$result_merchant->partner;
        $alipay_config['key']=$result_merchant->key;
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result)
        {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            //$_POST['trade_status'] == 'TRADE_FINISHED' ||
            if($_POST['trade_status'] == 'TRADE_SUCCESS'){
            	//商城订单
            	if(strstr($out_trade_no, 'SC')){
            		$user=new UserUC();
            		$pay_channel = ORDER_PAY_CHANNEL_ALIPAY;
            		//调用数据库事务方法
            		$rs = json_decode($user->ScPaySuccess($out_trade_no,$trade_no,$pay_channel));
            		if($rs -> status == ERROR_NONE){
            			echo 'order update success';
            		}else{
            			echo 'order update fail';
            		}
            	}else{
            		 //交易成功
                	$stored_order_id = $out_trade_no;
                	$user=new UserUC();
               	 	//调用数据库事务方法
                	$rs=$user->WapTranscation($stored_order_id,$trade_no);
                	if($rs){                            
                		echo 'order update success';                           
                	}else {
                    	//储值失败
                    	echo 'order update fail';
                	}
            	}
            }else {
                echo "success";		//请不要修改或删除
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
        $merchantId = Yii::app()->session['merchant_id'];
        //查询商户pid
        $user = new UserUC();
        $result_merchant = $user->findMerchantPid($merchantId);
        $alipay_config['partner']=$result_merchant->partner;
        $alipay_config['key']=$result_merchant->key;

        $alipayNotify  = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result)
        {
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //交易状态
            $trade_status = $_GET['trade_status'];
            //$trade_status == 'TRADE_FINISHED' ||
            if($trade_status == 'TRADE_SUCCESS')
            {
            	if(strstr($out_trade_no, 'SC')){
            		$user=new UserUC();
            		$pay_channel = ORDER_PAY_CHANNEL_ALIPAY;
            		//调用数据库事务方法
            		$rs = json_decode($user->ScPaySuccess($out_trade_no,$trade_no,$pay_channel));   
                            
            		if($rs -> status == ERROR_NONE){      
                                //第三方支付接口
                                $this ->Thired($out_trade_no, $rs -> data);
                                $this->redirect(Yii::app() -> createUrl('Dmall/order/paysuccess',array(
                                            'money'=>$rs -> data,
                                            'ordertype' => 'SC'
                                )));                             
            		}else{
            			$this->redirect(Yii::app() -> createUrl('mobile/uCenter/user/fail'));
            		}
            		
            	}else{
                	//交易成功
                	$stored_order_id = $out_trade_no;
                	$user=new UserUC();
                	//调用数据库事务方法
               	 	$rs = $user->WapTranscation($stored_order_id,$trade_no);
                	if($rs)
                	{   
                            $this->redirect(Yii::app() -> createUrl('Dmall/order/paysuccess',array(
                                            'money'=>$rs,
                                            'ordertype' => 'SC'
                                )));                                          
                	}else{
                   		$this->redirect(Yii::app() -> createUrl('mobile/uCenter/user/fail'));
//                         echo 'transcation fail';
                	}
            	}
            }else{
                $this->redirect(Yii::app() -> createUrl('mobile/uCenter/user/fail'));
            }
        }else {
            //验证失败
            echo "fail";
        }
    }
    
    

        /**
     * @param $key
     * @param $xmlData
     * @return string
     * 获取接口返回XML元素的值
     */
    private function getXmlVal ($key, $xmlData)
    {
        $result   = '';
        $document = new DOMDocument();
        $document->loadXML($xmlData);
        if (!empty($document->getElementsByTagName("{$key}")->item(0)->nodeValue)) {
            $result = $document->getElementsByTagName("{$key}")->item(0)->nodeValue;
        }
        return $result;
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
            $result = json_decode($wxpay -> getkey($out_trade_no));
            if($result->status == ERROR_NONE){
                if (empty($result -> data)){
                    return;
                }else{
                    $key = $result -> data;
                    $wxpay_merchant_type = $result -> wxpay_merchant_type;//微信支付的商户类型
                }
            }
            if($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) { //如果是自助商户执行
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
                      	if(strstr($stored_order_id, 'SC')){
                           	$user=new UserUC();
                           	$pay_channel = ORDER_PAY_CHANNEL_WXPAY;
                          	//调用数据库事务方法
                          	$rs = json_decode($user->ScPaySuccess($stored_order_id,$trade_no,$pay_channel));
                           	if($rs -> status == ERROR_NONE){                                               
                             	//第三方支付接口
                             	$msg = $this ->Thired($stored_order_id, $rs -> data); 
                              	$result = json_decode($msg,true);
                              	$thirdorder = $result['info']['orders_id'];
                              	$dordermall = new DOrderMall();
                              	$dordermall -> TianshiOrder($stored_order_id, $thirdorder);
                             	Yii::log('天时或者智游宝接口'.$msg,'warning');                                                    
                         	}else{
                             	Yii::log($rs -> errMsg,'warning');
                               	exit;                                                    
                         	}
                     	}else{
                          	$user=new UserUC();
                          	//调用数据库事务方法
                          	$rs = $user -> WapTranscation($stored_order_id,$trade_no);
                    	}
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
                    if(strstr($stored_order_id, 'SC')){
                            $user=new UserUC();
                            $pay_channel = ORDER_PAY_CHANNEL_WXPAY;
                            //调用数据库事务方法
                            $rs = json_decode($user->ScPaySuccess($stored_order_id,$trade_no,$pay_channel));
                            if($rs -> status == ERROR_NONE){                                               
                                    //第三方支付接口
                                    $msg = $this ->Thired($stored_order_id, $rs -> data); 
                                    $result = json_decode($msg,true);
                                    $thirdorder = $result['info']['id'];//天时支付订单id 
                                    $dordermall = new DOrderMall();
                                    $dordermall -> TianshiOrder($stored_order_id, $thirdorder);
                                    Yii::log('天时或者智游宝接口'.$msg,'warning');
                            }else{
                                    Yii::log($rs -> errMsg);
                                    exit;                                    
                            }                            
                    }else{
                            $user=new UserUC();
                            //调用数据库事务方法
                            $rs = $user -> WapTranscation($stored_order_id,$trade_no);
                    }
                }                    
            }
       	}
    }
    
    /**
     * 第三方支付接口
     * @param type $out_trade_no
     * @param type $rs
     */
    public function Thired($out_trade_no,$rs)
    {
        $dordermall=new DOrderMall();        
        $thired = array();
        
        $res = json_decode($dordermall -> OrderDetail($out_trade_no),true);        
        if($res['status'] == ERROR_NONE){            
            $thired = $res['data'];
            //天时下单
            if($thired['third_party_source'] == SHOP_PRODUCT_THIRED_TIANSHI){
                $ret = new TianShiApi();
                $item_id         = $thired['item_id'];//必填 要购买的票ID
                $name            = $thired['name'];//【必填】 购票人名称
                $mobile          = $thired['mobile'];//必填 购票人手机号(成功后短信将发送门票码号到该手机号)
                $is_pay          = '1';
                $orders_id       = $out_trade_no;
                $size            = $thired['num'];
                $start_date      = isset($thired['txtBirthday']) ? $thired['txtBirthday'] : '';
                $start_date_auto = empty($thired['txtBirthday']) ? '1' : '';
                $price_type      = $thired['sku_id'];
                $remark          = '';
                $price           = '';
                $back_cash       = '';
                $id_number       = '';                
                $r = $ret ->CreateOrder($item_id,$name,$mobile,$is_pay,$orders_id,$size,$start_date,$start_date_auto,$price_type,$remark,$price,$back_cash,$id_number);                
                return $r;

            }
            //智游宝下单
            if($thired['third_party_source'] == SHOP_PRODUCT_THIRED_ZHIYOUBAO){
                $ret = new ZhiYouBaoApi();
                $txtBirthday = isset($thired['txtBirthday']) ? $thired['txtBirthday'] : '';
                $r = $ret->CreateOrder($thired['online_paymoney'],$out_trade_no,$thired['name'],$thired['mobile'],$thired['social_security_number'],$thired['num'],$thired['item_id'],$txtBirthday);
                $description = $this->getXmlVal('description', $r);
                if($description == '成功'){
                    $sms = $ret ->Sms($out_trade_no);
                    $descript = $this->getXmlVal('description', $r);
                    if($descript == '成功'){
                        return $r;
                    }
                } else {
                    echo '智游宝下单接口下单失败';
                    exit;
                }
            }
        }
    }
    
    /**
     * 天时异步推送地址
     */
    public function actionNotifySky()
    {
        $orders_no        = $_POST['orders_id'];//天时订单号
        $out_code         = $_POST['out_code'];//天时码号
        $format           = $_POST['format'];
        $pid              = $_POST['_pid'];
        $method           = $_POST['method'];
        $out_orders_id    = $_POST['out_orders_id'];
        $out_money_send   = $_POST['out_money_send'];
        $out_money_one    = $_POST['out_money_one'];
        $out_send_content = $_POST['out_send_content'];
        Yii::log('天时码号out_code:'.$out_code.';'.'orders_id:'.$orders_no.';'.'format:'.$format.';'.'pid:'.$pid.';'.'method:'.$method.';'.'out_orders_id:'.$out_orders_id.';'.'out_money_send:'.$out_money_send.';'.'out_money_one:'.$out_money_one.';'.'out_send_content:'.$out_send_content,'warning');
        if(!empty($orders_no) && !empty($out_code)){
            $dordermall = new DOrderMall();
            $dordermall -> TianshiOrderCode($orders_no, $out_code);
        }
    }
    
}
?>