<?php
include_once(dirname(__FILE__).'/../mainClass.php');
class AlipayApi extends mainClass{
	/*******************************支付宝接口调用方法**********************************/
	/**
	 * 调用支付宝条码支付接口
	 * @param unknown $orderNo
	 * @param unknown $money
	 * @param unknown $user_code
	 * @param unknown $seller
	 * @param unknown $partner
	 * @param unknown $key
	 * @param unknown $store_id
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function barCodeApi($order_no, $money, $user_code, $seller, $partner, $key, $store_id) {
		Yii::import('application.extensions.alipay.*');
		require_once("lib/alipay_submit.class.php");
		require_once("alipay.config.php");
	
		//卖家支付宝帐户
		$seller_email = $seller;
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
		//商户订单号
		$out_trade_no = $order_no;
		//订单名称
		$subject = '条码支付';
		//付款金额
		$total_fee = $money;
		//订单业务类型
		$product_code = 'BARCODE_PAY_OFFLINE';
		//SOUNDWAVE_PAY_OFFLINE：声波支付，FINGERPRINT_FAST_PAY：指纹支付，BARCODE_PAY_OFFLINE：条码支付；商户代扣：GENERAL_WITHHOLDING
		//动态ID类型
		$dynamic_id_type = 'bar_code';
		//wave_code：声波，qr_code：二维码，bar_code：条码
		//动态ID
		$dynamic_id = $user_code;
		//协议支付信息
		$agreement_info = '';
		$extend_params_arr = array(
				'AGENT_ID' => '7736339a1',
		);
		if (!empty($store_id)) {
			$extend_params_arr['STORE_ID'] = $store_id;
			$extend_params_arr['STORE_TYPE'] = '1';
		}
	
		$extend_params = json_encode($extend_params_arr);
	
		$notify_url = ALIPAY_SYT_ASYNOTIFY;
	
		$parameter = array(
				"service" => "alipay.acquire.createandpay",
				"partner" => trim($alipay_config['partner']),
				"seller_email"	=> $seller_email,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"product_code"	=> $product_code,
				"dynamic_id_type"	=> $dynamic_id_type,
				"dynamic_id"	=> $dynamic_id,
				"agreement_info"	=> $agreement_info,
				"extend_params" => $extend_params,
				"notify_url"    => $notify_url,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
	
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		return $html_text;
	}
	
	/**
	 * 调用支付宝扫码支付接口
	 * @param unknown $order_no
	 * @param unknown $money
	 * @param unknown $seller
	 * @param unknown $partner
	 * @param unknown $key
	 * @param unknown $store_id
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function qrCodeApi($order_no, $money, $seller, $partner, $key, $store_id) {
		Yii::import('application.extensions.alipay.*');
		require_once("lib/alipay_submit.class.php");
		require_once("alipay.config.php");
	
		//卖家支付宝帐户
		$seller_email = $seller;
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
		//商户订单号
		$out_trade_no = $order_no;
		//订单名称
		$subject = '扫码支付';
		//订单业务类型
		$product_code = 'QR_CODE_OFFLINE';
		//付款金额
		$total_fee = $money;
		//订单描述
		$body = '扫码支付';
	
		$extend_params_arr = array(
				'AGENT_ID' => '7736339a1',
		);
		if (!empty($store_id)) {
			$extend_params_arr['STORE_ID'] = $store_id;
			$extend_params_arr['STORE_TYPE'] = '1';
		}
	
		$extend_params = json_encode($extend_params_arr);
		//服务器异步通知页面路径
		$notify_url = ALIPAY_SYT_ASYNOTIFY;
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.acquire.precreate",
				"partner" => trim($alipay_config['partner']),
				"notify_url"	=> $notify_url,
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"product_code"	=> $product_code,
				"total_fee"	=> $total_fee,
				"seller_email"	=> $seller_email,
				"extend_params" => $extend_params,
				"body"	=> $body,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
	
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		return $html_text;
	}
	
	/**
	 * 调用支付宝收单查询接口（商户可查询交易的状态等信息）
	 * @param unknown $order_no
	 * @param unknown $partner
	 * @param unknown $key
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function searchApi($order_no, $partner, $key)
	{
		Yii::import('application.extensions.alipay.*');
		require_once('alipay.config.php');
		require_once('lib/alipay_submit.class.php');
	
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
		//商户订单号
		$out_trade_no = $order_no;
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.acquire.query",
				"partner" => trim($alipay_config['partner']),
				"out_trade_no" => $out_trade_no,
				"_input_charset" => trim(strtolower($alipay_config['input_charset']))
		);
	
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
	
		return $html_text;
	}
	
	/**
	 * 调用支付宝收单撤销接口（商户可撤销已经存在的交易）
	 * @param unknown $order_no
	 * @param unknown $partner
	 * @param unknown $key
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function cancelApi($order_no, $partner, $key)
	{
		Yii::import('application.extensions.alipay.*');
		require_once('alipay.config.php');
		require_once('lib/alipay_submit.class.php');
	
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
		//支付宝交易号
		$trade_no = '';
		//商户订单号
		$out_trade_no = $order_no;
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.acquire.cancel",
				"partner" => trim($alipay_config['partner']),
				"trade_no"      => $trade_no,
				"out_trade_no"  => $out_trade_no,
				"_input_charset"        => trim(strtolower($alipay_config['input_charset']))
		);
	
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		return $html_text;
	}
	
	/**
	 * 调用支付宝收单关闭接口（商户可对已经存在且交易状态为待付款的的交易进行关闭）
	 * @param unknown $order_no
	 * @param unknown $partner
	 * @param unknown $key
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function closeApi($order_no, $partner, $key)
	{
		Yii::import('application.extensions.alipay.*');
		require_once('alipay.config.php');
		require_once('lib/alipay_submit.class.php');
	
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
		//商户订单号
		$out_trade_no = $order_no;
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.acquire.close",
				"partner" => trim($alipay_config['partner']),
				"out_trade_no"	=> $out_trade_no,
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
	
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		return $html_text;
	}
	
	/**
	 * 调用支付宝收单退款接口（商户可对交易进行退款）
	 * @param unknown $order_no
	 * @param unknown $refund_money
	 * @param unknown $rf_order_no
	 * @param unknown $partner
	 * @param unknown $key
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function refundApi($order_no, $refund_money, $rf_order_no, $partner, $key)
	{
		Yii::import('application.extensions.alipay.*');
		require_once('alipay.config.php');
		require_once('lib/alipay_submit.class.php');
	
		//支付宝交易号与商户网站订单号不能同时为空
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
	
		//商户订单号
		$out_trade_no = $order_no;
		//退款金额
		$refund_amount = $refund_money;
		//退款流水号
		$out_request_no = $rf_order_no;
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "alipay.acquire.refund",
				"partner" => trim($alipay_config['partner']),
				"out_trade_no"  => $out_trade_no,
				"refund_amount" => $refund_amount,
				"out_request_no" => $out_request_no,
				"_input_charset"        => trim(strtolower($alipay_config['input_charset']))
		);
	
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		return $html_text;
	}
	
	/**
	 * 通知请求验证
	 * @param unknown $partner
	 * @param unknown $key
	 * @return Ambigous <验证结果, boolean>
	 */
	public function verifyNotify($partner, $key) {
		Yii::import('application.extensions.alipay.*');
		require_once('alipay.config.php');
		require_once("lib/alipay_notify.class.php");
	
		$alipay_config['partner'] = $partner;
		$alipay_config['key'] = $key;
	
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
	
		return $verify_result;
	}
	
	/********************************V2.0 接口*************************************/
	/**
	 * 调用支付宝条码支付接口 V2.0
	 * @param unknown $orderNo 			订单编号
	 * @param unknown $money			总金额
	 * @param unknown $undiscountable	不打折金额
	 * @param unknown $user_code		用户授权码
	 * @param unknown $appid			应用appid
	 * @param unknown $seller_id		收款账号pid
	 * @param unknown $store_id			门店编号
	 * @param unknown $auth_token		授权令牌（isv模式）
	 * @param unknown $isv				isv账号pid（isv模式）
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function barCodeApiV2($order_no, $money, $undiscountable, $user_code, $appid=NULL, $seller_id=NULL, $store_id=NULL, $auth_token=NULL, $isv='2088701036418655') {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayTradePayRequest.php");
	
		//请求参数
		$parameter = array(
				'out_trade_no' => $order_no, //订单号
				'scene' => 'bar_code', //支付场景
				'auth_code' => $user_code, //支付授权码
				'seller_id' => $seller_id, //卖家支付宝用户ID（如果该值为空，则默认为商户签约账号对应的支付宝用户ID ）
				'total_amount' => $money, //订单总金额
				'discountable_amount' => '', //可打折金额
				'undiscountable_amount' => $undiscountable, //不可打折金额
				'subject' => '条码支付', //订单标题
				'body' => '', //订单描述
				'goods_detail' => array(), //商品明细列表
				'operator_id' => '', //商户操作员编号
				'store_id' => $store_id, //商户门店编号
				'terminal_id' => '', //机具终端编号
				'extend_params' => array('sys_service_provider_id' => $isv), //扩展参数,填ISV的PID
				'time_expire' => '', //支付超时时间,格式为：yyyy-MM-dd HH:mm:ss
				'timeout_express' => '', //支付超时时间表达式,该值优先级低于time_expire
				//'royalty_info' => '', //分账信息
		);
		$bizContent = json_encode($parameter);
	
		//建立请求
		$request = new AlipayTradePayRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
	
		return $this->handle($request, $response);
	}
	
	/**
	 * 调用支付宝扫码支付接口 V2.0
	 * @param unknown $order_no			订单编号
	 * @param unknown $money			总金额
	 * @param unknown $undiscountable	不打折金额
	 * @param unknown $appid			应用appid
	 * @param unknown $seller_id		收款账号pid
	 * @param unknown $store_id			门店编号
	 * @param unknown $auth_token		授权令牌（isv模式）
	 * @param unknown $isv				isv账号pid（isv模式）
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function qrCodeApiV2($order_no, $money, $undiscountable, $appid=NULL, $seller_id=NULL, $store_id=NULL, $auth_token=NULL, $isv='2088701036418655') {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayTradePrecreateRequest.php");
	
		//请求参数
		$parameter = array(
				'out_trade_no' => $order_no, //订单号
				'seller_id' => $seller_id, //卖家支付宝用户ID（如果该值为空，则默认为商户签约账号对应的支付宝用户ID ）
				'total_amount' => $money, //订单总金额
				'discountable_amount' => '', //可打折金额
				'undiscountable_amount' => $undiscountable, //不可打折金额
				'subject' => '扫码支付', //订单标题
				'body' => '', //订单描述
				'goods_detail' => array(), //商品明细列表
				'operator_id' => '', //商户操作员编号
				'store_id' => $store_id, //商户门店编号
				'terminal_id' => '', //机具终端编号
				'extend_params' => array('sys_service_provider_id' => $isv), //扩展参数,填ISV的PID
				'time_expire' => '', //支付超时时间,格式为：yyyy-MM-dd HH:mm:ss
				//'royalty_info' => '', //分账信息
		);
		$bizContent = json_encode($parameter);
	
		//建立请求
		$request = new AlipayTradePrecreateRequest();
		$request->setBizContent($bizContent); //业务内容
		$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 调用支付宝收单查询接口 V2.0（商户可查询交易的状态等信息）
	 * @param unknown $order_no		订单编号
	 * @param unknown $appid		应用appid
	 * @param unknown $auth_token	授权令牌（isv模式）
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function searchApiV2($order_no, $appid=NULL, $auth_token=NULL)
	{
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayTradeQueryRequest.php");
		
		//请求参数
		$parameter = array(
				'trade_no' => '', //支付宝流水号
				'out_trade_no' => $order_no, //订单号
		);
		$bizContent = json_encode($parameter);
		
		//建立请求
		$request = new AlipayTradeQueryRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 调用支付宝收单撤销接口 V2.0（商户可撤销已经存在的交易）
	 * @param unknown $order_no		订单编号
	 * @param unknown $appid		应用appid
	 * @param unknown $auth_token	授权令牌（isv模式）
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function cancelApiV2($order_no, $appid=NULL, $auth_token=NULL)
	{
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayTradeCancelRequest.php");
		
		//请求参数
		$parameter = array(
				'out_trade_no' => $order_no, //订单号
		);
		$bizContent = json_encode($parameter);
		
		//建立请求
		$request = new AlipayTradeCancelRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 调用支付宝收单退款接口 V2.0（商户可对交易进行退款）
	 * @param unknown $trade_no		支付宝交易号
	 * @param unknown $refund_money	退款金额
	 * @param unknown $rf_order_no	退款订单号
	 * @param unknown $appid		应用appid
	 * @param unknown $auth_token	授权令牌（isv模式）
	 * @return Ambigous <支付宝处理结果, mixed>
	 */
	public function refundApiV2($trade_no, $refund_money, $rf_order_no, $appid=NULL, $auth_token=NULL)
	{
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayTradeRefundRequest.php");
		
		//请求参数
		$parameter = array(
				'trade_no' => $trade_no, //支付宝流水号
				'out_request_no' => $rf_order_no, //退款订单号
				'refund_amount' => $refund_money, //退款金额
				'refund_reason' => '正常退款', //退款原因
				'store_id' => '', //商户的门店编号
				'terminal_id' => '', //商户的终端编号
		);
		$bizContent = json_encode($parameter);
		
		//建立请求
		$request = new AlipayTradeRefundRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 通知请求验证 V2.0
	 * @param unknown $partner
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function verifyNotifyV2($partner, $notify_id) {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		
		$verify_result = verifyNotify($partner, $notify_id);
		
		return $verify_result;
	}
	
	/**
	 * 获取授权令牌
	 * @param unknown $app_auth_code	授权码
	 * @param unknown $appid			应用appid
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function appAuthByCodeApi($app_auth_code, $appid=NULL) {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayOpenAuthTokenAppRequest.php");
		
		//请求参数
		$parameter = array(
				'grant_type' => 'authorization_code',
    			'code' => $app_auth_code,
		);
		$bizContent = $parameter;
		
		//建立请求
		$request = new AlipayOpenAuthTokenAppRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 查询签约、认证状态
	 * @param string $appid
	 * @param string $auth_token
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function appAuthQueryApi($appid=NULL, $auth_token=NULL) {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayBossProdArrangementOfflineQueryRequest.php");
		
		//建立请求
		$request = new AlipayBossProdArrangementOfflineQueryRequest();
		
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 上传图片素材接口
	 * @param unknown $image_name	图片名
	 * @param unknown $image_type	图片类型，目前只支持jpg、jpeg、png、gif、bmp图片格式
	 * @param unknown $image_path	图片的系统路径
	 * @param string $appid			应用appid
	 * @param string $auth_token	授权令牌
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function uploadImageApi($image_name, $image_type, $image_path, $appid=NULL, $auth_token=NULL) {
		Yii::import('application.extensions.alifuwu.*');
    	require_once("function.inc.php");
    	require_once("aop/request/AlipayOfflineMaterialImageUploadRequest.php");
		
		//请求参数
    	$request = new AlipayOfflineMaterialImageUploadRequest();
    	$request->setImageName($image_name);
    	$request->setImageType($image_type);
    	$request->setImageContent($image_path);
    	
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 支付宝创建口碑门店接口
	 * @param unknown $store_no			门店编号
	 * @param unknown $category_id		门店类目
	 * @param unknown $brand_name		品牌名
	 * @param unknown $brand_logo		品牌logo
	 * @param unknown $main_name		主门店名
	 * @param unknown $branch_name		分店名
	 * @param unknown $province			省份编码
	 * @param unknown $city				城市编码
	 * @param unknown $district			区县编码
	 * @param unknown $address			详细地址
	 * @param unknown $longitude		经度
	 * @param unknown $latitude			纬度
	 * @param unknown $contact			门店电话
	 * @param unknown $notify_mobile	通知电话
	 * @param unknown $main_image		门店首图
	 * @param unknown $images			门店审核图：至少一张门头照，两张内景照
	 * @param unknown $business_time	营业时间
	 * @param unknown $avg_price		人均消费
	 * @param unknown $licence			营业执照图片
	 * @param unknown $licence_code		营业执照注册号
	 * @param unknown $licence_name		营业执照名称
	 * @param unknown $certificate		经营许可证照片
	 * @param unknown $certificate_expires 经营许可证有效期
	 * @param unknown $auth_letter		授权函图片
	 * @param string $appid				应用appid
	 * @param string $auth_token		授权令牌
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function shopCreateApi($parameter, $appid=NULL, $auth_token=NULL) {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayOfflineMarketShopCreateRequest.php");
		//请求参数
		/*
		$parameter = array(
				//'request_id' => '',
				'store_id' => $store_no,
				'category_id' => $category_id,
				'brand_name' => $brand_name,
				'brand_logo' => $brand_logo,
				'main_shop_name' => $main_name,
				'branch_shop_name' => $branch_name,
				'province_code' => $province,
				'city_code' => $city,
				'district_code' => $district,
				'address' => $address,
				'longitude' => $longitude,
				'latitude' => $latitude,
				'contact_number' => $contact,
				'notify_mobile' => $notify_mobile,
				'main_image' => $main_image,
				'audit_images' => $images,
				'business_time' => $business_time,
				//'wifi' => 'F',
				//'parking' => 'F',
				//'no_smoking' => 'F',
				//'box' => 'F',
				'avg_price' => $avg_price,
				'isv_uid' => '2088701036418655', //mark
				'licence' => $licence,
				'licence_code' => $licence_code,
				'licence_name' => $licence_name,
				'business_certificate' => $certificate,
				'business_certificate_expires' => $certificate_expires,
				'auth_letter' => $auth_letter,
				//'is_operating_online' => '',
				//'online_image' => '',
				'operate_notify_url' => ''
		);
		*/
		$parameter['isv_uid'] = '2088701036418655';
		
		$bizContent = $parameter;
		
		//建立请求
		$request = new AlipayOfflineMarketShopCreateRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * 支付宝修改口碑门店接口
	 * @param unknown $shop_id			支付宝门店id
	 * @param unknown $store_no			门店编号
	 * @param unknown $category_id		门店类目
	 * @param unknown $brand_name		品牌名
	 * @param unknown $brand_logo		品牌logo
	 * @param unknown $main_name		主门店名
	 * @param unknown $branch_name		分店名
	 * @param unknown $province			省份编码
	 * @param unknown $city				城市编码
	 * @param unknown $district			区县编码
	 * @param unknown $address			详细地址
	 * @param unknown $longitude		经度
	 * @param unknown $latitude			纬度
	 * @param unknown $contact			门店电话
	 * @param unknown $notify_mobile	通知电话
	 * @param unknown $main_image		门店首图
	 * @param unknown $images			门店审核图：至少一张门头照，两张内景照
	 * @param unknown $business_time	营业时间
	 * @param unknown $avg_price		人均消费
	 * @param unknown $licence			营业执照图片
	 * @param unknown $licence_code		营业执照注册号
	 * @param unknown $licence_name		营业执照名称
	 * @param unknown $certificate		经营许可证照片
	 * @param unknown $certificate_expires 经营许可证有效期
	 * @param unknown $auth_letter		授权函图片
	 * @param string $appid				应用appid
	 * @param string $auth_token		授权令牌
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function shopModifyApi($shop_id, $parameter, $appid=NULL, $auth_token=NULL) {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayOfflineMarketShopModifyRequest.php");
		//请求参数
		/*
		$parameter = array(
				//'request_id' => '',
				'shop_id' => $shop_id,
				'store_id' => $store_no,
				'category_id' => $category_id,
				'brand_name' => $brand_name,
				'brand_logo' => $brand_logo,
				'main_shop_name' => $main_name,
				'branch_shop_name' => $branch_name,
				'province_code' => $province,
				'city_code' => $city,
				'district_code' => $district,
				'address' => $address,
				'longitude' => $longitude,
				'latitude' => $latitude,
				'contact_number' => $contact,
				'notify_mobile' => $notify_mobile,
				'main_image' => $main_image,
				'audit_images' => $images,
				'business_time' => $business_time,
				//'wifi' => 'F',
				//'parking' => 'F',
				//'no_smoking' => 'F',
				//'box' => 'F',
				'avg_price' => $avg_price,
				'licence' => $licence,
				'licence_code' => $licence_code,
				'licence_name' => $licence_name,
				'business_certificate' => $certificate,
				'business_certificate_expires' => $certificate_expires,
				'auth_letter' => $auth_letter,
				//'is_operating_online' => '',
				//'online_image' => '',
				'operate_notify_url' => ''
		);
		*/
		$parameter['shop_id'] = $shop_id;
		
		$bizContent = $parameter;
	
		//建立请求
		$request = new AlipayOfflineMarketShopModifyRequest();
		$request->setBizContent($bizContent); //业务内容
		//$request->setNotifyUrl(ALIPAY_SYT_ASYNOTIFY); //异步通知地址
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
	
		return $this->handle($request, $response);
	}
	
	/**
	 * 查询门店信息接口
	 * @param unknown $shop_id		支付宝的门店id
	 * @param string $appid			应用appid
	 * @param string $auth_token	授权令牌
	 * @return Ambigous <unknown, string, unknown>
	 */
	public function queryShopDetailApi($shop_id, $appid=NULL, $auth_token=NULL) {
		Yii::import('application.extensions.alifuwu.*');
		require_once("function.inc.php");
		require_once("aop/request/AlipayOfflineMarketShopQuerydetailRequest.php");
		
		//请求参数
		$parameter = array(
				'shop_id' => $shop_id,
		);
		$bizContent = $parameter;
		
		//建立请求
		$request = new AlipayOfflineMarketShopQuerydetailRequest();
		$request->setBizContent($bizContent);
		 
		//响应结果
		$response = aopclient_request_execute($request, $appid, NULL, $auth_token);
		
		return $this->handle($request, $response);
	}
	
	/**
	 * @param $key
	 * @param $xmlData
	 * @return string
	 * 获取支付宝接口返回XML元素的值
	 */
	public function getXmlVal ($key, $xmlData)
	{
		$result   = '';
		$document = new DOMDocument("1.0", "utf-8");
		$document->loadXML($xmlData);
		if (!empty($document->getElementsByTagName("{$key}")->item(0)->nodeValue)) {
			$result = $document->getElementsByTagName("{$key}")->item(0)->nodeValue;
		}
	
		return $result;
	}
	
	/**
	 * 对2.0接口数据进行处理，使业务逻辑可以兼容处理
	 * @param unknown $request
	 * @param unknown $response
	 * @return unknown
	 */
	private function handle($request, $response) {
		$response = json_decode(json_encode($response), true); //对象转换为数组
		//适用于json格式
		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . '_response';
		$response = $response[$rootNodeName]; //获取业务参数
		
		$code = isset($response['code']) ? $response['code'] : ''; //请求结果码
		switch ($code) {
			case '10000':
			$response['result_code'] = ALIPAY_V2_CODE_SUCCESS; //成功
			break;
			
			case '10003':
			$response['result_code'] = ALIPAY_V2_CODE_INPROCESS; //处理中
			break;
			
			case '40004':
			$response['result_code'] = ALIPAY_V2_CODE_FAIL; //失败
			break;
			
			default:
			$response['result_code'] = ALIPAY_V2_CODE_UNKNOWN; //未知
			break;
		}
		
		if (isset($response['sub_code'])) {
			$response['detail_error_code'] = $response['sub_code']; //详细错误码
		}
		
		if (isset($response['msg'])) {
			$response['detail_error_des'] = $response['msg']; //错误描述信息
		}
		if (isset($response['sub_msg'])) {
			$response['detail_error_des'] = $response['sub_msg']; //详细错误描述信息
		}
		
		if (isset($response['qr_code'])) {
			$qr_code = $response['qr_code'];
			$pic_url = '';
			$pic_url = str_replace('https://qr.alipay.com/', 'https://mobilecodec.alipay.com/show.htm?code=', $qr_code);
			$pic_url .= '&picSize=S';
			$response['small_pic_url'] = $pic_url; //二维码图片地址
		}
		
		return $response;
	}
	
	/**
	 * 获取指定节点名的值
	 * @param unknown $data
	 * @param unknown $key
	 * @param string $subKey
	 * @return Ambigous <string, unknown>
	 */
	public function getVal($data, $key, $subKey=NULL) {
		$result = '';
		if (is_array($data)) { //数组
			if (isset($data[$key])) {
				$result = $data[$key];
			}
		}else { //xml
			//正则匹配对应节点的字符串
			preg_match("/<$key>(.+?)<\/$key>/is", $data, $node_match);
			if (!empty($node_match)) {
				$node_string = $node_match[0]; //匹配到的xml字符串
				
				//转化为数组
				$result = json_decode(json_encode(simplexml_load_string($node_string)), true);
				
				if (isset($result[0]) && !is_array($result[0])) {
					$result = $result[0];
				}
			}
// 			$document = new DOMDocument("1.0", "utf-8");
// 			$document->loadXML($data);
// 			$node = $document->getElementsByTagName("{$key}")->item(0);
// 			if ($node) {
// 				//节点还有子节点且不是文本子节点
// 				if ($node->hasChildNodes() && $node->firstChild->nodeName != '#text') {
// 					$result = $this->xml2Array($node);
// 				}else {
// 					if (!empty($node->nodeValue)) {
// 						$result = $node->nodeValue;
// 					}
// 				}
// 			}
// 			if (!empty($document->getElementsByTagName("{$key}")->item(0)->nodeValue)) {
// 				$result = $document->getElementsByTagName("{$key}")->item(0)->nodeValue;
// 			}
		}
		return $result;
	}
	
	/**
	 * xml转数组
	 * @param unknown $node
	 * @return boolean
	 */
	private function xml2Array($node) {
		$array = false;
		
		if ($node->hasAttributes()) {
			foreach ($node->attributes as $attr) {
				$array[$attr->nodeName] = $attr->nodeValue;
			}
		}
		
		if ($node->hasChildNodes()) {
			if ($node->childNodes->length == 1) {
				$array[$node->firstChild->nodeName] = $node->firstChild->nodeValue;
			}else {
				foreach ($node->childNodes as $childNode) {
					if ($childNode->nodeType != XML_TEXT_NODE) {
						$array[$childNode->nodeName][] = $this->xml2Array($childNode);
					}
				}
			}
		}
		
		return $array;
	}
	
}