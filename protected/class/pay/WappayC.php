<?php
/**
 * User: 钮飞虎
 * Date: 2015/7/29
 * Time: 11:51
 */
include_once(dirname(__FILE__).'/../mainClass.php');
class WappayC extends mainClass
{
    /**
     * $orderNo 商品订单号
     * $productName 订单名称
     * $synNotifyUrl 同步页面路径
     * $asyNotifyUrl 异步页面路径
     * $showUrl 商品展示页面
     * $sellerId 商户PID
     * $num 商品数量
     * $money 商品价格
     * $key 安全校验码
     */
    public function ToWappay($orderNo,$productName,$synNotifyUrl,$asyNotifyUrl,$showUrl,$sellerId,$num,$money,$key)
    {
        Yii::import('application.extensions.wappay.*');
        require_once("lib/alipay_submit.class.php");
        require_once("alipay.config.php");
        header("content-Type: text/html; charset=Utf-8");

        /*******************必填参数********************/
        //支付类型
        $payment_type = "1";

        //服务器异步通知页面路径
        $notify_url = $asyNotifyUrl;

        //页面跳转同步通知页面路径
        $return_url = $synNotifyUrl;

        //商户订单号
        $out_trade_no = $orderNo;

        //订单名称
        $subject = $productName;

        //商品展示地址
        $show_url=$showUrl;

        //商户PID
        $alipay_config['seller_id']=$sellerId;

        $alipay_config['partner']=$sellerId;//partner

        $alipay_config['key']=$key;

        //商品数量
        $productNum=$num;
        //商品总价
        $total_fee=$money;
        /*******************选填参数********************/
        //订单描述
        $body=null;
        //超时时间
        $it_b_pay=null;
        //钱包Token
        $extern_token=null;
        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "alipay.wap.create.direct.pay.by.user",
            "partner" => trim($alipay_config['partner']),
            "seller_id" => trim($alipay_config['seller_id']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "show_url"	=> $show_url,
            "body"	=> $body,
            "it_b_pay"	=> $it_b_pay,
            "extern_token"	=> $extern_token,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

    /**
     * 商城支付宝退款
     * @param type $sellerId
     * @param type $trade_no
     * @param type $order_paymoney
     * @param type $remark
     * @param type $key
     * @param type $refund_order_no
     * @throws Exception
     */
    public function ToWappayRefund($sellerId, $trade_no, $order_paymoney, $remark, $key, $refund_order_no,$email)
    {
        //参数检查
        if (empty($sellerId)) {
                throw new Exception('参数sellerId不能为空');
        }
        if (empty($trade_no)) {
                throw new Exception('参数trade_no不能为空');
        }
        if (empty($order_paymoney)) {
                throw new Exception('参数order_paymoney不能为空');
        }
        /*if (empty($remark)) {
                throw new Exception('参数remark不能为空');
        }  */
        if (empty($key)) {
                throw new Exception('参数key不能为空');
        }
        if (empty($refund_order_no)) {
                throw new Exception('参数refund_order_no不能为空');
        }
        
        Yii::import('application.extensions.wappay.*');
        require_once("lib/alipay_submit.class.php");
        require_once("alipay.config.php");
        header("content-Type: text/html; charset=Utf-8");
        /**************************请求参数**************************/

        $alipay_config['seller_id'] = $sellerId;
        $alipay_config['partner']   = $sellerId;//partner
        $alipay_config['key']       = $key;
        //服务器异步通知页面路径
        $notify_url = ACTIVIST_ALIPAY_SYNNOTYFY;
        //需http://格式的完整路径，不允许加?id=123这类自定义参数         //卖家支付宝帐户
        $seller_email = $email;
        //必填         //退款当天日期
        $refund_date = date('Y-m-d H:i:s');
        //必填，格式：年[4位]-月[2位]-日[2位] 小时[2位 24小时制]:分[2位]:秒[2位]，如：2007-10-01 13:13:13         //批次号
        $batch_no = $refund_order_no;
        //必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001         //退款笔数
        $batch_num = '1';
        //必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）         //退款详细数据
        $detail_data = $trade_no.'^'.$order_paymoney.'^'.'协商退款';
        //必填，具体格式请参见接口技术文档
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
                "service" => "refund_fastpay_by_platform_pwd",
                "partner" => trim($alipay_config['partner']),
                "notify_url"	=> $notify_url,
                "seller_email"	=> $seller_email,
                "refund_date"	=> $refund_date,
                "batch_no"	=> $batch_no,
                "batch_num"	=> $batch_num,
                "detail_data"	=> $detail_data,
                "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }     
    
    /**
     * 商城支付宝退款  返回订单数据
     * @param type $order_id
     * @param type $order_sku_id
     * @param type $order_status
     * @throws Exception
     */
    public function wappayRefund($order_id,$order_sku_id,$order_status)
    {
        //参数检查
        if (empty($order_id)) {
                throw new Exception('参数order_id不能为空');
        }
        if (empty($order_sku_id)) {
                throw new Exception('参数order_sku_id不能为空');
        }
        if (empty($order_status)) {
                throw new Exception('参数order_status不能为空');
        }
        $result = array('status'=>'null','errMsg'=>'null','data'=>'null');
        //订单查找
        $order = Order::model()->find('id = :id and flag = :flag', array(
            ':id' => $order_id,
            ':flag' => FLAG_NO
        ));
        if (empty($order)) {
            throw new Exception('未找到相关订单信息');
        }
        //订单支付状态检查
        if ($order['pay_status'] != ORDER_STATUS_PAID) {
            throw new Exception('无效的订单');
        }
        //订单状态检查
        /*if ($order['order_status'] != ORDER_STATUS_NORMAL && $order['order_status'] != ORDER_STATUS_PART_REFUND ) {
            throw new Exception('无效的订单');
        }   */
        //部分退款或退款处理中情况下，计算退款记录的退款金额
        $back_money = 0; //已退金额
        if ($order['order_status'] == ORDER_STATUS_PART_REFUND || $order['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
            //查询退款记录
            $record = RefundRecord::model()->findAll('order_id = :order_id and flag = :flag and status != :status', array(
                ':order_id' => $order['id'],
                ':flag' => FLAG_NO,
                ':status' => REFUND_STATUS_FAIL
            ));
            foreach ($record as $val) {
                $back_money += $val['refund_money'];
            }
        }
        //添加退款记录
        $record = new RefundRecord();
        $record['order_id'] = $order['id']; 
        $record['refund_money'] = $order['order_paymoney'];
        $record['type'] = REFUND_TYPE_REFUND;
        $record['refund_channel'] = $order['pay_channel'];
        $record['refund_no'] = '';
        $record['status'] = REFUND_STATUS_SUCCESS; //退款处理中
        $record['refund_time'] = date('Y-m-d H:i:s');
        $record['create_time'] = $order['pay_time'];
        $record['refund_order_no'] = $this->createRefundOrderNumber();
        if (!$record->save()) {
                throw new Exception('退款记录保存失败');
        }
        $data = array();
        $data['trade_no'] = $order['trade_no'];
        $data['order_paymoney'] = $order['order_paymoney'];
        $data['remark'] = $order['remark'];
        $data['pay_channel'] = $order['pay_channel'];
        $data['refund_order_no'] = $record['refund_order_no'];
        $result['status'] = ERROR_NONE;
        $result['data']   = $data;  
        return json_encode($result);
    }
    
    /**
     * 商城退款 第三方接口退款
     * @param type $order_no
     */
    public function wappayNotifyUrl($order_no)
    {
        $order = Order::model() -> find('order_no=:order_no and flag=:flag',array(
            ':order_no' => $order_no,
            ':flag'     => FLAG_NO,
        ));      
        $refund = RefundRecord::model()->find('order_id=:order_id and flag=:flag',array(
            ':order_id' => $order['id'],
            ':flag' => FLAG_NO,
        ));
        
            $ordersku = OrderSku::model()->find('flag=:flag and order_id=:order_id',array(':flag'=>FLAG_NO,':order_id'=>$order['id']));
            $productsku = DProductSku::model()->find('flag=:flag and id=:id',array(':flag'=>FLAG_NO,':id'=>$ordersku['sku_id']));
            $product = DProduct::model()->find('flag=:flag and id=:id',array(':flag'=>FLAG_NO,':id'=>$productsku['product_id']));
            //天时退款           
                if($product->third_party_source == SHOP_PRODUCT_THIRED_TIANSHI) {
                    $ret = new TianShiApi();
                    $orders_id = $order -> third_party_order_id;//必填 要退票的订单号  
                    $size      = $refund -> refund_money/$ordersku->price;//退票数,缺省退票所有未使用票数 
                    $r = $ret ->Refund($orders_id,$size);
                    Yii::log('天时退款'.$r,'warning');                          
                }
                //智游宝退款
                if($product->third_party_source == SHOP_PRODUCT_THIRED_ZHIYOUBAO) {            
                    $ret = new ZhiYouBaoApi();
                    $size      = $refund -> refund_money/$ordersku->price;
                    $r = $ret->PartRefund($order['order_no'],$size,$refund['refund_order_no']);
                    Yii::log('智游宝退款'.$r,'warning');                                  
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

    /**
    * 商城微信退款
    * @param type $order_id
    * @param type $order_sku_id
    * @param type $order_status
    */
   public function MallRefund($order_id, $order_sku_id, $order_status)
   {
       //参数检查
       if (empty($order_id)) {
               throw new Exception('参数order_id不能为空');
       }
       if (empty($order_sku_id)) {
               throw new Exception('参数order_sku_id不能为空');
       }
       if (empty($order_status)) {
               throw new Exception('参数order_status不能为空');
       }            
       //订单查找
       $order = Order::model()->find('id = :id and flag = :flag', array(
           ':id' => $order_id,
           ':flag' => FLAG_NO
       ));
       if (empty($order)) {
           throw new Exception('未找到相关订单信息');
       }
       //订单支付状态检查
       if ($order['pay_status'] != ORDER_STATUS_PAID) {
           throw new Exception('无效的订单');
       }       
        //查询退款记录
        $record = RefundRecord::model()->find('order_id = :order_id and flag = :flag', array(
            ':order_id' => $order['id'],
            ':flag' => FLAG_NO,
        ));  
       $record['refund_order_no'] = $this->createRefundOrderNumber();
       if (!$record->save()) {
               throw new Exception('退款记录保存失败');
       }       
       //申请成功后调用退款查询接口
       $ret = $this->wxpayRefund($record['refund_order_no']);
       return $ret;           
   }
   
       /**
	 * 微信退款申请，申请成功后需通过退款查询接口查询
	 * @param unknown $refund_order_no
	 * @param unknown $op_account
	 * @throws Exception
	 * @return string
	 */
	public function wxpayRefund($refund_order_no) {
		$result = array();
		try {
			//创建sql语句
			$cmd = Yii::app()->db->createCommand();
			$cmd->select('r.refund_money, o.order_no, o.store_id, o.online_paymoney, k.num, k.price'); //查询字段
			$cmd->from(array('wq_order o', 'wq_refund_record r','wq_order_sku k')); //查询表名
			$cmd->where(array(
					'AND',  //and操作
					'r.order_id = o.id',
                                        'k.order_id = o.id',
					'r.refund_order_no = :refund_order_no',
			));
			//查询参数
			$cmd->params = array(
					':refund_order_no' => $refund_order_no
			);
			//执行sql，获取所有行数据
			$model = $cmd->queryRow();
			if (empty($model)) {
				$result['status'] = ERROR_NO_DATA;
				throw new Exception('退款记录不存在');
			}
			$merchant_id = Yii::app()->session['merchant_id'];
                        $merchant = Merchant::model()->findByPk($merchant_id);
                        if ($merchant) { 
                            $wxpay_mchid = $merchant['wechat_mchid'];
                            $wxpay_merchant_type = $merchant['wxpay_merchant_type'];
                            $wxpay_appid = $merchant['wechat_appid'];
                            $wxpay_appsecret = $merchant['wechat_appsecret'];
                            $wxpay_api_key = $merchant['wechat_key'];
                            $wxpay_mchid = $merchant['wechat_mchid'];
                            $wxpay_apiclient_cert = UPLOAD_SYSTEM_PATH.'cert/'.$merchant['wechat_apiclient_cert'].'/apiclient_cert.pem';
                            $wxpay_apiclient_key = UPLOAD_SYSTEM_PATH.'cert/'.$merchant['wechat_apiclient_key'].'/apiclient_key.pem';	
                        }
                        $appid = $wxpay_appid;
			$mchid = $wxpay_mchid;
			$key = $wxpay_api_key;
			$merchant_type = $wxpay_merchant_type;
            $cert_path = $wxpay_apiclient_cert;
			$key_path = $wxpay_apiclient_key;
			$refund_money = $model['refund_money'];
			$order_no = $model['order_no'];
			$rf_order_no = $refund_order_no;
			$total_money = $model['refund_money'];
			
			//微信接口请求
			$response = $this->refundApi($order_no, $rf_order_no, $total_money, $refund_money, $appid, $mchid, $key, $merchant_type, $cert_path, $key_path);
			
                        if (!$response || $response['return_code'] == _FAIL) {
				$result['status'] = ERROR_EXCEPTION;
				$msg = isset($response['return_msg']) ? $response['return_msg'] : '接口请求失败';
				throw new Exception($msg);
			}
			//返回请求结果
			$result_code = $this->getArrayVal('result_code', $response); //业务结果码
			$err_code = $this->getArrayVal('err_code', $response); //错误码
			$err_code_des = $this->getArrayVal('err_code_des', $response); //错误描述
			$transaction_id = $this->getArrayVal('transaction_id', $response); //微信订单号
			$refund_id = $this->getArrayVal('refund_id', $response); //微信退款单号
			$refund_channel = $this->getArrayVal('refund_channel', $response); //退款渠道
			
			//申请成功
			if ($result_code == _SUCCESS) {
				$result['status'] = ERROR_NONE;
				$result['trade_no'] = $refund_id;
                                //商城退款 第三方接口退款 
                                $this->wappayNotifyUrl($order_no);
// 				$result['trade_no'] = $trade_no;
// 				$result['refund_time'] = $gmt_refund_pay;
			}
			//失败
			if ($result_code == _FAIL) {
				$result['status'] = ERROR_REQUEST_FAIL;
				throw new Exception($err_code_des);
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		
		return json_encode($result);
	}
        
        /**
	 * 调用微信收单退款接口（商户可对交易进行退款）
	 * @param unknown $order_no
	 * @param unknown $rf_order_no
	 * @param unknown $total_money
	 * @param unknown $refund_money
	 * @param unknown $op_account
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
	private function refundApi($order_no, $rf_order_no, $total_money, $refund_money, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
//		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
//		}
		
		//数据封装类
		$input = new WxPayRefund();
		$input->wxpay_config = $wxpay_config;		
		//订单号
		$input->SetOut_trade_no($order_no);
		//订单总金额
		$input->SetTotal_fee($total_money * 100);
		//退款金额
		$input->SetRefund_fee($refund_money * 100);
		//退款订单号
		$input->SetOut_refund_no($rf_order_no);
		//操作员账号，可为商户号MCHID
		$input->SetOp_user_id($mchid);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用退款接口
		$result = $api->refund($input);
		
		return $result;
	}
        
        /**
	 * 获取数组下标的数据
	 * @param unknown $key
	 * @param unknown $array
	 * @return Ambigous <string, unknown>
	 */
	private function getArrayVal($key, $array) {
		$result   = '';
		if ($array && isset($array[$key])) {
			$result = $array[$key];
		}
		return $result;
	}
        
        /**
	 * 退款查询接口
	 * @param unknown $refund_no
	 * @param unknown $appid
	 * @param unknown $mchid
	 * @param unknown $key
	 * @param unknown $type
	 * @param unknown $cert_path
	 * @param unknown $key_path
	 * @return Ambigous <成功时返回，其他抛异常, multitype:>
	 */
        private function refundQueryApi($refund_no, $appid, $mchid, $key, $type, $cert_path, $key_path) {
		Yii::import('application.extensions.wxpay.*');
		require_once "lib/WxPay.Api.php";
		require_once 'log.php';
		require 'wxpay.custom.php';
		
		if ($type == WXPAY_MERCHANT_TYPE_SELF) { //自助商户
			//商户信息配置
			$wxpay_config['APPID'] = $appid;
			$wxpay_config['MCHID'] = $mchid;
			$wxpay_config['KEY'] = $key;
			$wxpay_config['SSLCERT_PATH'] = $cert_path;
			$wxpay_config['SSLKEY_PATH'] = $key_path;
		}
		
		//日志记录
		//TODO
		
		//数据封装类
		$input = new WxPayRefundQuery();
		$input->wxpay_config = $wxpay_config;
		if ($type == WXPAY_MERCHANT_TYPE_AFFILIATE) { //特约商户
			//设置子商户的appid和商户id
			//$input->SetSub_appid($appid);
			$input->SetSub_mch_id($mchid);
		}
		$input->SetOut_refund_no($refund_no);
		//接口类
		$api = new WxPayApi();
		$api->wxpay_config = $wxpay_config;
		//调用订单查询接口
		$result = $api->refundQuery($input);
		
		return $result;
	}
        
    /**
    * 生成退款订单号
    * @return string
    */
    private function createRefundOrderNumber() {
        do {
            $random = mt_rand(10000000, 99999999); //生成8位随机数密码
            $order_no = date('Ymd', time()).$random;
            $criteria = new CDbCriteria();
            $criteria->addCondition('refund_order_no = :refund_order_no');
            $criteria->params[':refund_order_no'] = $order_no;
            $model = RefundRecord::model()->find($criteria);
        }while (!empty($model));
        return $order_no;
    }
}
?>