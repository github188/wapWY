<?php
/**
 * 收银台
 *
 */
class CashierController extends sytController
{
	/**
	 * 收款
	 */
	public function actionPay() {
		
		$this->render('pay');
	}
	
	/**
	 * 获取会员优惠券列表信息
	 */
	public function actionUserCoupons() {
		$data = array();
		$data['error'] = 'failure';
		if (isset($_POST['account']) && !empty($_POST['account']) && isset($_POST['money'])) {
			$total = $_POST['money']; //总金额
			$undiscount = isset($_POST['undiscount']) ? $_POST['undiscount'] : 0; //不参与优惠计算金额
			$money = $total - $undiscount; //参与优惠计算金额
			
			$operator_id = Yii::app()->session['operator_id'];
			//获取会员信息	
			$user_info = $this->getUserInfo($_POST['account'], $operator_id);
			if (!isset($user_info['data'])) {
				$data['errMsg'] = $user_info['error'];
				echo json_encode($data);
				return ;
			}
			$info = $user_info['data'];
			
			//设置用户的相关信息，称呼，会员等级名称，还需支付金额，可得积分
			if ($info['sex'] == SEX_FEMALE) {
				$data['info']['name'] = $info['name'].'女士';
			}else {
				$data['info']['name'] = $info['name'].'先生';
			}
			$data['info']['gname'] = $info['gname']; //会员等级名称
			//获取会员折扣
			if ($info['discount'] > 0 && $info['discount'] <=1 ) {
				$discount = $info['discount'];
			}else {
				$discount = 1;
			}
			$data['info']['money'] = $info['money'] + 0;
			$need = $money * $discount - $data['info']['money'] + $undiscount;
			$data['info']['discount'] = $discount; //会员折扣			
			$data['info']['need'] = $need > 0 ? $need : 0; //还需支付金额
			//获取会员积分规则
			$rule = $info['points_ratio'];
			$data['info']['rule'] = $rule > 0 ? $rule : 0;
			$data['info']['points'] = $rule > 0 ? floor(($money * $discount + $undiscount) * $rule) : 0; //可得积分
			
			//待删
			$dis = '';
			if (!empty($discount) && $discount != 1) {
				$vip = $discount*10;
				$dis = '享受'.$vip.'折';
			}
			$data['info']['vip'] = $dis;
			
			$data['list'] = array();
			
			$coupons = new CouponsSC();
			
			$ret = $coupons->getUserCouponList($info['user_id'], $operator_id, $money);
			$result = json_decode($ret, true);
			if ($result['status'] == ERROR_NONE) {
				$data['error'] = 'success';
				foreach ($result['data']['list'] as $k => $v) {
					$expired = (strtotime($v['end_time']) - time()) / (60 * 60 * 24); //计算过期时间
					$desc = ceil($expired).'天后到期';
					
					$content = array();
					$content['id'] = $v['id']; //用户优惠券表id
					$content['name'] = $v['title']; //优惠券标题
					$content['expire'] = $desc; //到期提示
					if (!isset($data['list'][$v['cid']]['content'])) {
						$data['list'][$v['cid']]['content'] = array();
					}
					array_push($data['list'][$v['cid']]['content'], $content);
					//标题
					$data['list'][$v['cid']]['title'] = $v['title'].'共'.count($data['list'][$v['cid']]['content']).'张（可使用'.$v['use_restriction'].'张）';
				}
			}else {
				$data['errMsg'] = $result['errMsg'];
			}
		}else {
// 			if (!isset($_POST['money']) || empty($_POST['money'])) {
// 				$data['errMsg'] = 'no_money';
// 				echo json_encode($data);
// 				return ;
// 			}
			if (!isset($_POST['account']) || empty($_POST['account'])) {
				$data['errMsg'] = '请输入会员手机号或会员卡号';
				echo json_encode($data);
				return ;
			}
		}
		echo json_encode($data);
	}
	
	/**
	 * 获取用户信息
	 */
	private function getUserInfo($account, $operator_id) {
		$info = array();
		
		$user = new UserSC();
		$ret = $user->getUserDetails($operator_id, $account);
		$result = json_decode($ret, true);
		
		if ($result['status'] == ERROR_NONE) {
			if (!empty($result['data'])) {
// 				$user_id = $result['data']['user_id']; //用户id
// 				$name = $result['data']['name']; //用户名
// 				$sex = $result['data']['sex']; //用户性别
// 				$money = $result['data']['money']; //用户储值金额
// 				$free_secret = $result['data']['free_secret']; //免密金额
// 				$discount = $result['data']['discount']; //会员折扣
// 				$rule = $result['data']['points_rule']; //积分规则（积分=实收金额/积分规则）
				$info['data'] = $result['data'];
			}else {
				$info['error'] = '该手机号未注册会员或会员卡号无效';
			}
		}else {
			$info['error'] = $result['errMsg'];
		}
		return $info;
	}
	
	/**
	 * 更新优惠券
	 */
	public function actionUpdateCoupons() {
		$data = array();
		$data['error'] = 'failure';
		if (isset($_POST['list']) && isset($_POST['money'])) {
			$operator_id = Yii::app()->session['operator_id'];
			$uc_list = $_POST['list'];
			//剔除m和u
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
			
			$total = $_POST['money']; //总金额
			$undiscount = isset($_POST['undiscount']) ? $_POST['undiscount'] : 0; //不参与优惠计算金额
			$money = $total - $undiscount; //参与优惠计算金额
			
			$coupons = new CouponsSC();
			$ret = $coupons->getCouponsPay('', $uc_list, $money, $use_discount, $operator_id);
			$result = json_decode($ret, true);
			if ($result['status'] == ERROR_NONE) {
				if (isset($result['data']['need'])) {
					$data['error'] = 'success';
					$data['need'] = $result['data']['need'] + 0;
				}
			}else {
				$data['errMsg'] = $result['errMsg'];
			}
		}
		echo json_encode($data);
	}
	
	/**
	 * 查看优惠券详情
	 */
	public function actionCouponsDetail() {
		$this->layout = 'dialog';
		if (isset($_GET['ucid']) && !empty($_GET['ucid'])) {
			$ucid = $_GET['ucid'];
				
			$coupons = new CouponsSC();
			$ret = $coupons->getCouponsDetail($ucid);
			$result = json_decode($ret, true);
			if ($result['status'] == ERROR_NONE) {
				if (!isset($result['data'])) {
					$this->render('error', array('msg' => '系统内部错误'));
					return ;
				}else {
					$info = array();
					$info['title'] = $result['data']['title'];
					$info['type'] = $GLOBALS['COUPON_TYPE'][$result['data']['type']];
					$info['date'] = date('Y.m.d', strtotime($result['data']['start_time'])).'-'.date('Y.m.d', strtotime($result['data']['end_time']));
					$info['store'] = $result['data']['store'];
					$info['min_pay'] = ($result['data']['min_pay'] + 0).'元';
					$info['num'] = $result['data']['use_restriction'].'张';
					$info['with_discount'] = $result['data']['with_discount'] == IF_WITH_USERDISCOUNT_YES ? '是' : '否';
					//$info['with_coupons'] = $result['data']['with_coupons'] == IF_WITH_COUPONS_YES ? '是' : '否';
					switch ($result['data']['type']) {
// 						case COUPON_TYPE_REDENVELOPE: {
// 							$info['for'] = '金  额';
// 							$info['content'] = ($result['data']['money'] + 0).'元';
// 							$info['red'] = '是否可与代金券或折扣券叠加使用：  '.$info['with_coupons'];
// 							break;
// 						}
						case COUPON_TYPE_CASH: {
							$info['for'] = '金  额';
							$info['content'] = $result['data']['money'].'元';
							break;
						}
						case COUPON_TYPE_DISCOUNT: {
							$info['for'] = '折  扣';
							$tmp = $result['data']['discount'] * 10;
							$info['content'] = $tmp.'折';
							break;
						}
						case COUPON_TYPE_EXCHANGE: {
							$info['for'] = '优惠说明';
							$info['content'] = $result['data']['coupon_illustrate'];
							break;
						}
						default: {
							$info['for'] = ' 未知 ';
							$info['content'] = '无';
							break;
						}
					}
					
					$this->render('couponsDetail', array('info' => $info));
				}
			}else {
				$this->render('error', array('msg' => $result['errMsg']));
			}
		}else {
			$this->render('error', array('msg' => '错误的请求'));
		}
	}
	
	/**
	 * 创建订单
	 */
	public function actionCreateOrder() {
		$data = array();
		$data['error'] = 'failure';
		if (isset($_POST['money']) && isset($_POST['account']) && isset($_POST['list']) && isset($_POST['action'])) {
			$operator_id = Yii::app()->session['operator_id'];
			$account = $_POST['account'];
			$action = $_POST['action'];
			$total = $_POST['money'];
			$undiscount = isset($_POST['undiscount']) ? $_POST['undiscount'] : 0; //不打折金额
			$coupons_list = $_POST['list'];
			
			if (empty($account)) {
				if ($total === '') {
					$data['errMsg'] = '请输入收款金额';
					echo json_encode($data);
					return ;
				}
				if ($total === '0') {
					$data['errMsg'] = '收款金额不能为零';
					echo json_encode($data);
					return ;
				}
			}
			//是否含非法字符
			if (!is_numeric($total)) {
				$data['errMsg'] = '错误的金额格式';
				echo json_encode($data);
				return ;
			}
			//小数点位数是否大于2位
			$tmp = explode(".", $total);
			if (count($tmp) > 1 && 	strlen($tmp[1]) > 2) {
				$data['errMsg'] = '错误的金额格式';
				echo json_encode($data);
				return ;
			}
			//是否含有字符 -
			$tmp = strstr($total, "-");
			if ($tmp) {
				$data['errMsg'] = '错误的金额格式';
				echo json_encode($data);
				return ;
			}
			$total += 0;
			if ($total == 0) {
				$data['errMsg'] = '收款金额不能为零';
				echo json_encode($data);
				return ;
			}
			$list = explode(",", $_POST['list']);
			
			//查询操作员信息获取所属门店id
			$operator = new OperatorC();
			$ret2 = $operator->getOperatorDetails($operator_id);
			$result2 = json_decode($ret2, true);
			if ($result2['status'] == ERROR_NONE) {
				$store_id = $result2['data']['store_id'];
			}else {
				$data['errMsg'] = $result2['errMsg'];
				echo json_encode($data);
				return ;
			}
			
			$money = $total - $undiscount; //可打折金额
			if (!empty($account)) {
				//会员信息查询
				$user = new UserSC();
				$ret = $user->getUserDetails($operator_id, $account);
				$result = json_decode($ret, true);
				if ($result['status'] != ERROR_NONE) {
					$data['errMsg'] = $result['errMsg'];
					echo json_encode($data);
					return ;
				}
				$user_info = $result['data'];
				$user_id = $user_info['user_id']; //获取会员id
				//拼接已选优惠券id
				$arr = explode(",", $coupons_list);
				$tmp = '';
				$use_stored = false;
				$use_discount = false;
				foreach ($arr as $v) {
					if ($v == 'm') {
						$use_stored = true;
					}elseif ($v == 'u') {
						$use_discount = true;
					}else {
						$tmp .= empty($tmp) ? $v : ','.$v;
					}
				}
				
				//优惠券检查并计算
				$coupons = new CouponsSC();
				$ret1 = $coupons->getCouponsPay($user_id, $tmp, $money, $use_discount,$operator_id);
				$result1 = json_decode($ret1, true);
				if ($result1['status'] != ERROR_NONE) {
					$data['errMsg'] = $result1['errMsg'];
					echo json_encode($data);
					return ;
				}
				if (!isset($result1['data']['need'])) {
					$data['errMsg'] = '系统内部错误';
					echo json_encode($data);
					return ;
				}
				
				//得到优惠后的金额，计算会员折扣（如果会员折扣无效则不打折）
				if ($use_discount && $user_info['discount'] > 0 && $user_info['discount'] <=1 ) {
					$discount = $user_info['discount'];
				}else {
					$discount = 1;
				}
				$pay = $result1['data']['need'] * $discount; //优惠后的金额
				$red = $result1['data']['red']; //红包抵扣的金额
				$cou = $result1['data']['coupons']; //优惠券抵扣的金额
				$dis = $result1['data']['need'] - $pay; //会员折扣抵扣的金额
				
				$pay += $undiscount; //实付金额
				//$data['pay'] = $pay;
				$transaction = Yii::app()->db->beginTransaction(); //开启事务
				try {
					//计算会员储值抵扣后的金额
					if ($use_stored) { //是否使用会员储值
						if ($user_info['money'] >= $pay) {
							$need_pay = '0';
							$stored_pay = $pay;
						}else {
							$need_pay = $pay - $user_info['money'];
							$stored_pay = $user_info['money'];
						}
					}else {
						$need_pay = $pay;
						$stored_pay = '0';
					}
					
					/*****modify*****/
					if ($action == ORDER_PAY_CHANNEL_STORED && $need_pay > 0) {
						throw new Exception('储值余额不足');
					}
					
					//是否使用了优惠券
					if (empty($tmp)) {
						$use_coupons = ORDER_IF_USE_COUPONS_NO;
					}else {
						$use_coupons = ORDER_IF_USE_COUPONS_YES;
					}
						
					$order = new OrderSC();
					//获取用户的免密金额
					$secret_money = $user->getTotalSecretMoney($user_id);
					//创建订单
					$order_info = $order->createOrder(
							$store_id,
							$operator_id,
							$user_id,
							$action,
							$stored_pay,
							$secret_money,
							$need_pay,
							$use_stored,
							$use_coupons,
							$total,
							$undiscount,
							$red,
							$cou,
							$dis
					);
						
					if (empty($order_info)) { //返回结果无效
						throw new Exception('系统内部错误');
					}
					//订单创建成功
					//如果使用优惠券则修改优惠券使用状态
					if ($use_coupons == ORDER_IF_USE_COUPONS_YES) {
						$ret3 = $coupons->setUseStatus($tmp, $order_info['id'], COUPONS_USE_STATUS_USED);
						$result3 = json_decode($ret3, true);
						if ($result3['status'] != ERROR_NONE) {
							throw new Exception($result3['errMsg']);
						}
					}
						
					//如果使用会员储值且无需确认直接支付，则修改会员的储值金额
					if ($use_stored && $order_info['stored_confirm_status'] == ORDER_PAY_NUCONFIRM) {
						$res = $user->updateUserStored($user_id, -$stored_pay);
						if ($res['status'] != ERROR_NONE) {
							throw new Exception($res['errMsg']);
						}
					}
						
					$data['error'] = 'success';
					$data['order_no'] = $order_info['order_no'];
					$data['need'] = $need_pay;
					$transaction->commit(); //数据提交
				} catch (Exception $e) {
					$transaction->rollback(); //数据回滚
					$data['errMsg'] = $e->getMessage();
				}
			}else { //非会员身份
				//拼接已选优惠券id
				$arr = explode(",", $coupons_list);
				$tmp = '';
				foreach ($arr as $v) {
					if (empty($v)) {
						continue;
					}
					$tmp .= empty($tmp) ? $v : ','.$v;
				}
				//优惠券检查并计算
				$coupons = new CouponsSC();
				$ret1 = $coupons->getCouponsPay(NULL, $coupons_list, $money, false, $operator_id);
				$result1 = json_decode($ret1, true);
				if ($result1['status'] != ERROR_NONE) {
					$data['errMsg'] = $result1['errMsg'];
					return $data;
				}
				if (!isset($result1['data']['need'])) {
					$data['errMsg'] = '系统内部错误';
					return $data;
				}
					
				$pay = $result1['data']['need']; //优惠后的金额
				$cou = $result1['data']['coupons']; //优惠券抵扣的金额
					
				$pay += $undiscount; //实付金额
				//创建订单
				$transaction = Yii::app()->db->beginTransaction(); //开启事务
				try {
					//是否使用了优惠券
					if (empty($tmp)) {
						$use_coupons = ORDER_IF_USE_COUPONS_NO;
					}else {
						$use_coupons = ORDER_IF_USE_COUPONS_YES;
					}
					
					$order = new OrderSC();
					$order_info = $order->createOrder(
							$store_id, 
							$operator_id, 
							NULL, 
							$action, 
							NULL, 
							NULL, 
							$pay, 
							false, 
							$use_coupons, 
							$total,
							$undiscount, 
							NULL, 
							$cou, 
							NULL
					);
					if (empty($order_info)) { //返回结果无效
						throw new Exception('系统内部错误');
					}
					//订单创建成功
					//如果使用优惠券则修改优惠券使用状态
					if ($use_coupons == ORDER_IF_USE_COUPONS_YES) {
						$ret3 = $coupons->setUseStatus($tmp, $order_info['id'], COUPONS_USE_STATUS_USED);
						$result3 = json_decode($ret3, true);
						if ($result3['status'] != ERROR_NONE) {
							throw new Exception($result3['errMsg']);
						}
					}
					
					$data['error'] = 'success';
					$data['order_no'] = $order_info['order_no'];
					$data['need'] = $pay;
					$transaction->commit(); //数据提交
				} catch (Exception $e) {
					$transaction->rollback(); //数据回滚
					$data['errMsg'] = $e->getMessage();
				}
			}
		}
		echo json_encode($data);
	}
	
	
	/**
	 * 根据支付渠道进行支付
	 */
	public function actionPayChannel() {
		$this->layout = 'dialog';
		if (isset($_GET['action']) && !empty($_GET['action']) && isset($_GET['orderNo']) && !empty($_GET['orderNo'])) {
			$action = $_GET['action'];
			$order_no = $_GET['orderNo'];
			if ($action == ORDER_PAY_CHANNEL_ALIPAY_SM) { //支付宝扫码支付
				//调用支付宝扫码支付接口
				$img = '';
				$api = new AlipaySC();
                                //$api = new AlipaySCv2();
				$ret = $api->qrcodePay($order_no);
				$result = json_decode($ret, true);
				if ($result['status'] == ERROR_NONE) {
					$img = $result['data'];
					$code = $result['code'];
				}else {
					Yii::app()->user->setFlash('error', $result['errMsg']);
				}
				$operator_id = Yii::app()->session['operator_id'];
				//查询操作员信息
				$msg = '';
				$print = '';
				$operator = new OperatorC();
				$ret1 = $operator->getOperatorDetails($operator_id);
				$result1 = json_decode($ret1, true);
				if ($result1['status'] == ERROR_NONE) {
					$store_id = $result1['data']['store_id'];
					$store = new StoreC();
					$ret2 = $store->getStoreDetails($store_id);
					$result2 = json_decode($ret2, true);
					if ($result2['status'] == ERROR_NONE) {
						$print = $result2['data']['is_print'];
					}else {
						$msg = $result2['errMsg'];
					}
				}else {
					$msg = $result1['errMsg'];
				}
				//magic img
				$img = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.$code;
				//查询门店信息中的打印机设置
				$this->render('qrcode', array('img' => $img, 'msg' => $msg, 'print' => $print));
			}
			if ($action == ORDER_PAY_CHANNEL_ALIPAY_TM) { //支付宝条码支付
				$this->render('barcode');
			}
			if ($action == ORDER_PAY_CHANNEL_WXPAY_SM) { //微信扫码
				//调用微信扫码支付接口
				$img = '';
				$api = new WxpaySC();
				$ret = $api->qrcodePay($order_no);
				$result = json_decode($ret, true);
				if ($result['status'] == ERROR_NONE) {
					$img = $result['data'];
				}else {
					Yii::app()->user->setFlash('error', $result['errMsg']);
				}
				$operator_id = Yii::app()->session['operator_id'];
				//查询操作员信息
				$msg = '';
				$print = '';
				$operator = new OperatorC();
				$ret1 = $operator->getOperatorDetails($operator_id);
				$result1 = json_decode($ret1, true);
				if ($result1['status'] == ERROR_NONE) {
					$store_id = $result1['data']['store_id'];
					$store = new StoreC();
					$ret2 = $store->getStoreDetails($store_id);
					$result2 = json_decode($ret2, true);
					if ($result2['status'] == ERROR_NONE) {
						$print = $result2['data']['is_print'];
					}else {
						$msg = $result2['errMsg'];
					}
				}else {
					$msg = $result1['errMsg'];
				}
				//查询门店信息中的打印机设置
				$this->render('qrcode', array('img' => $img, 'msg' => $msg, 'print' => $print));
			}
			if ($action == ORDER_PAY_CHANNEL_WXPAY_TM) { //微信条码
				$this->render('barcode');
			}
			if ($action == ORDER_PAY_CHANNEL_CASH || $action == ORDER_PAY_CHANNEL_UNIONPAY) { //线下支付
				//$this->offLinePay($order_no);
				$this->redirect(array('confirm', 'orderNo' => $order_no));
			}
// 			if ($action == ORDER_PAY_CHANNEL_UNIONPAY) { //银行卡支付
// 				//$this->redirect(array('wait', 'orderNo' => $order_no));
// 				//调用银商支付接口
// 				$api = new UmspaySC();
// 				$ret = $api->umspayOrder($order_no);
// 				$result = json_decode($ret, true);
// 				if ($result['status'] == ERROR_NONE) {
// 					$trade_no = $result['trade_no'];
// 					$ums_code = $result['ums_code'];
// 				}else {
// 					$msg = $result['errMsg'];
// 					$this->render('error', array('msg' => $msg));
// 					return ;
// 				}
// 				//保存银商订单号和特征码
// 				$order = new OrderSC();
// 				$ret = $order->updateCode($order_no, null, $trade_no, $ums_code);
// 				$result = json_decode($ret, true);
// 				if ($result['status'] != ERROR_NONE) {
// 					$this->render('error', array('msg' => $result['errMsg']));
// 					return ;
// 				}
				
// 				$this->render('wait', array('orderNo' => $order_no));
// 			}
			if ($action == ORDER_PAY_CHANNEL_NO_MONEY) { //无需支付
				$this->offLinePay($order_no);
			}
		}else {
			//错误的请求
			$msg = '错误的请求';
			$this->render('error', array('msg' => $msg));
		}
	}
	
	/**
	 * 支付确认
	 */
	public function actionConfirm() {
		$this->layout = 'dialog';
		$order_no = $_GET['orderNo'];
		if (isset($_POST['orderNo'])) {
			$this->offLinePay($order_no);
			return ;
		}
		$pay_money = 0;
		$order = new OrderSC();
		$ret = $order->getOrderDetail('', $order_no);
		$result = json_decode($ret, true);
		if ($result['status'] == ERROR_NONE) {
			//$pay_money = $result['data']['unionpay_paymoney'] + $result['data']['cash_paymoney'];
			$pay_money = $result['data']['cash_paymoney'];
		}
		$this->render('confirm', array('money' => $pay_money));
	}
	
	/**
	 * 线下支付或者无需支付
	 * @param unknown $order_no
	 */
	public function offLinePay($order_no) {
		$order = new OrderSC();
		//检查订单的储值支付确认状态
		$ret3 = $order->getOrderDetail('', $order_no);
		$result3 = json_decode($ret3, true);
		if ($result3['status'] == ERROR_NONE) {
			if ($result3['data']['stored_confirm_status'] == ORDER_PAY_WAITFORCONFIRM) {
				$this->render('wait', array('orderNo' => $order_no));
				return ;
			}
			//支付成功，修改订单状态
			$transaction = Yii::app()->db->beginTransaction(); //开启事务
			try {
				$result = $order->orderPaySuccess($order_no, date('Y-m-d H:i:s'), NULL, NULL);
				if (!empty($result) && $result['status'] == ERROR_NONE) {
					$transaction->commit();
					//支付成功
					$this->redirect(array('success', 'orderNo' => $order_no));
				}
			} catch (Exception $e) {
				$transaction->rollback();
				//支付失败
				$msg = $e->getMessage();
				$this->render('error', array('msg' => $msg));
			}
		}else {
			$this->render('error', array('msg' => $result3['errMsg']));
		}
	}
	
	/**
	 * 保存条码并请求支付宝条码支付接口
	 */
	public function actionBarcodePay() {
		$this->layout = 'dialog';
		if (isset($_POST['orderNo']) && isset($_POST['code'])) {
			$order_no = $_POST['orderNo'];
			$code = $_POST['code'];
			$operator_id = Yii::app()->session['operator_id'];
			
			//保存auth_code
			$order = new OrderSC();
			$ret = $order->updateCode($order_no, $code);
			$result = json_decode($ret, true);
			if ($result['status'] != ERROR_NONE) {
				$this->render('error', array('msg' => $result['errMsg']));
				return ;
			}
			
			//查询订单信息
			$ret = $order->getOrderDetail('', $order_no);
			$result = json_decode($ret, true);
			if ($result['status'] != ERROR_NONE) {
				$this->render('error', array('msg' => $result['errMsg']));
				return ;
			}
			
			//根据支付渠道请求对应的接口
			$channel = $result['data']['pay_channel'];
			if ($channel == ORDER_PAY_CHANNEL_ALIPAY_TM) {
				$api = new AlipaySC(); //支付宝接口
				$ret1 = $api->barcodePay($order_no);
			}
			if ($channel == ORDER_PAY_CHANNEL_WXPAY_TM) {
				$api = new WxpaySC(); //微信接口
				$ret1 = $api->barcodePay($order_no);
			}
			
			if (!$ret) {
				$this->render('error', array('msg' => '系统内部错误'));
				return ;
			}
			
			$result1 = json_decode($ret1, true);
			if ($result1['status'] == ERROR_NONE) {
				//下单成功
				$pay_status = $result1['pay'];
				if ($pay_status == 'done') {
					$this->redirect(array('success', 'orderNo' => $order_no));
				}
				if ($pay_status == 'wait') {
					$this->render('wait', array('orderNo' => $order_no));
				}
			}else {
				$this->render('error', array('msg' => $result1['errMsg']));
			}
		}else {
			$this->render('error', array('msg' => '错误的请求'));
		}
	}
	
	/**
	 * 订单查询
	 */
	public function actionSearch() {
		$data = array();
		$data['error'] = 'failure';
		if (isset($_POST['orderNo']) && !empty($_POST['orderNo'])) {
			$order_no = $_POST['orderNo'];
			
			$order = new OrderSC();
			//查询订单信息
			$ret1 = $order->getOrderDetail('', $order_no);
			$result1 = json_decode($ret1, true);
			if ($result1['status'] == ERROR_NONE) {
				//根据支付渠道请求对应的接口
				$channel = $result1['data']['pay_channel'];
				if ($channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $channel == ORDER_PAY_CHANNEL_ALIPAY_TM) {
					$api = new AlipaySC(); //支付宝接口
					$ret = $api->alipaySearch($order_no);
				}
				if ($channel == ORDER_PAY_CHANNEL_WXPAY_SM || $channel == ORDER_PAY_CHANNEL_WXPAY_TM) {
					$api = new WxpaySC(); //微信接口
					$ret = $api->wxpaySearch($order_no);
				}
// 				if ($channel == ORDER_PAY_CHANNEL_UNIONPAY) {
// 					$api = new UmspaySC();
// 					$ret = $api->umspaySearch($order_no);
// 				}
				if ($ret) {
					$result = json_decode($ret, true);
					if ($result['status'] != ERROR_NONE) {
						$data['errMsg'] = $result['errMsg'];
						echo json_encode($data);
						return ;
					}
				}
			}
			
			//查询订单支付状态
			$ret1 = $order->getOrderDetail('', $order_no);
			$result1 = json_decode($ret1, true);
			if ($result1['status'] == ERROR_NONE) {
				$data['error'] = 'wait';
				if ($result1['data']['pay_status'] == ORDER_STATUS_PAID && 
					$result1['data']['stored_confirm_status'] != ORDER_PAY_WAITFORCONFIRM) {
					$data['error'] = 'success';
				}elseif ($result1['data']['stored_confirm_status'] == ORDER_PAY_WAITFORCONFIRM &&
						isset($result['trade_status'])) { //如果第三方支付返回交易状态说明第三方支付成功
					$data['error'] = 'wait';
					if ($result['trade_status'] == _SUCCESS || $result['trade_status'] == SEARCH_TRADE_SUCCESS) {
						$data['error'] = 'jump';
					}
				}
			}else {
				$data['errMsg'] = $result1['errMsg'];
			}
		}
		echo json_encode($data);
	}
	
	public function actionWait() {
		$this->layout = 'dialog';
		if (isset($_GET['orderNo']) && !empty($_GET['orderNo'])) {
			$this->render('wait', array('orderNo' => $_GET['orderNo']));
		}
	}
	
	/**
	 * 支付成功页
	 */
	public function actionSuccess() {
		$this->layout = 'dialog';
		$model = array();
		if (isset($_GET['orderNo']) && !empty($_GET['orderNo'])) {
			$order_no = $_GET['orderNo'];
			$order = new OrderSC();
			$ret = $order->getOrderDetail('', $order_no);
			$result = json_decode($ret, true);
			if ($result['status'] == ERROR_NONE) {
				$model = $result['data'];
			}
		}
		$this->render('success', array('model' => $model));
	}
	
	/**
	 * 获取打印信息参数
	 */
	public function actionOrderInfo() {
		$data = array();
		$data['error'] = 'failure';
		if (isset($_POST['orderNo']) && !empty($_POST['orderNo'])) {
			$order_no = $_POST['orderNo'];
			$order = new OrderSC();
			$ret = $order->getOrderDetail('', $order_no);
			$result = json_decode($ret, true);
			if ($result['status'] == ERROR_NONE) {
				$data['error'] = 'success';
				$data['s_name'] = $result['data']['sname']; //门店名
				$data['m_name'] = $result['data']['mname']; //商户名
				$data['operator'] = $result['data']['number']; //操作员编号
				$data['print_name'] = $result['data']['print_name']; //打印机名称
				$data['order_no'] = $result['data']['order_no']; //订单编号
				$data['type'] = $GLOBALS['ORDER_PAY_CHANNEL'][$result['data']['pay_channel']]; //交易类型
				$data['total'] = $result['data']['order_paymoney']; //订单总金额
				$data['money'] = $result['data']['online_paymoney']; //线上支付金额
				//实付金额
				$data['paymoney'] = $result['data']['stored_paymoney'] + $result['data']['online_paymoney'] + $result['data']['unionpay_paymoney'] + $result['data']['cash_paymoney'] + 0;
				$data['paymoney'] = sprintf("%.2f", $data['paymoney']); //保留2位小数
				$data['time'] = $result['data']['create_time']; //下单时间
				$data['now'] =  date('Y-m-d H:i:s'); //当前时间
				$alipay_account = $result['data']['alipay_account'];
				if (!empty($alipay_account) && strstr($alipay_account, "*") == false) {
					if (strstr($alipay_account, "@")) { //邮箱账号
						$tmp = substr($alipay_account, 0, 3);
						$tmp .= "***";
						$tmp .= strstr($alipay_account, "@");
					}else { //手机账号
						$tmp = substr($alipay_account, 0, 3);
						$tmp .= "****";
						$tmp .= substr($alipay_account, 7);
					}
					$alipay_account = $tmp;
				}
				$data['account'] = $alipay_account; //买家支付宝账号
				$pay_status = $result['data']['pay_status']; //支付状态
				$order_status = $result['data']['order_status']; //订单状态
                $data['pay_time'] = $result['data']['pay_time'];//支付时间
                $data['trade_no'] = $result['data']['trade_no'];//支付宝交易号
				$data['status'] = '';
				if ($pay_status == ORDER_STATUS_PAID && $order_status == ORDER_STATUS_NORMAL) {
					$data['status'] = '已付款';
				}
				if ($pay_status == ORDER_STATUS_UNPAID) {
					$data['status'] = '待付款';
				}
				if ($order_status == ORDER_STATUS_REFUND) {
					$data['status'] = '已退款';
				}
				if ($order_status == ORDER_STATUS_PART_REFUND) {
					$data['status'] = '已部分退款';
				}
				if ($order_status == ORDER_STATUS_REVOKE) {
					$data['status'] = '已撤销';
				}
				if ($order_status == ORDER_STATUS_HANDLE_REFUND) {
					$data['status'] = '退款处理中';
				}
			}else {
				$data['errMsg'] = $result['errMsg'];
			}
		}
		echo json_encode($data);
	}
	
	
}