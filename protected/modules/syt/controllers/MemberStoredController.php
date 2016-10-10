<?php
/*
 * 会员储值
 */
class MemberStoredController extends sytController
{
    /**
     * 会员储值的充值
     */
    public function actionCharge()
    {
        $list = array('0' => '请选择储值活动');
        $operator_id = Yii::app()->session['operator_id'];
        $stored  = new MemberStoredC();
        $ret = $stored->getStoredList($operator_id);
        $result = json_decode($ret, true);
        if ($result['status'] == ERROR_NONE) {
        	foreach ($result['data']['list'] as $k => $v) {
        		$money = $v['money'];
        		$bonus = $v['bonus'];
        		$des = !empty($bonus) ? '充'.$money.'送'.$bonus : '充'.$money;
        		$list[$v['id']] = $des;
        	}
        }
        
        /*
        $rat  = $ret->Back($operator_id);
        $back = json_decode($rat,true);        
        if($back['status'] == ERROR_NONE)
        {
            $merchantId = $back['data']['merchantId'];
            $storeId    = $back['data']['storeId'];
            $select     = $ret->Stored($merchantId);
            $storedlsit = json_decode($select,true);            
            if($storedlsit['status'] == ERROR_NONE)
            {
                $stored = $storedlsit['data'];
            }
        } else {
            $stored = '';
        }
        */
        if (isset($_POST['account']) && isset($_POST['stored_id']) && isset($_POST['num'])) {
        	$flag = true;
        	if (empty($_POST['account'])) {
        		$flag = false;
        		Yii::app()->user->setFlash('account', '请输入会员账号');
        	}
        	if (empty($_POST['stored_id'])) {
        		$flag = false;
        		Yii::app()->user->setFlash('stored', '请选择储值活动');
        	}
        	if (empty($_POST['num'])) {
        		$flag = false;
        		Yii::app()->user->setFlash('num', '请输入正确的数量');
        	}
        	if ($flag) {
        		$ret = $stored->Charge($operator_id, $_POST['stored_id'], $_POST['num'], $_POST['account']);
        		$result = json_decode($ret, true);
        		if ($result['status'] == ERROR_NONE) {
        			Yii::app()->user->setFlash('success', '储值成功');
        		}else {
        			Yii::app()->user->setFlash('error', $result['errMsg']);
        		}
        	}
        }
        $this->render('charge',array('list'=>$list));
    }
    
    /**
     * 创建储值订单
     */
    public function actionCreateStoredOrder() {
    	$data = array();
    	$data['error'] = 'failure';
    	if (isset($_POST['account']) && isset($_POST['stored_id']) && isset($_POST['num']) && isset($_POST['channel'])) {
        	$flag = true;
        	if (empty($_POST['account'])) {
        		$flag = false;
        		$data['account_error'] = '请输入会员账号';
        	}
        	if (empty($_POST['stored_id'])) {
        		$flag = false;
        		$data['stored_error'] = '请选择储值活动';
        	}
        	if (empty($_POST['num'])) {
        		$flag = false;
        		$stored['num_error'] = '请输入正确的数量';
        	}
        	if (!$flag) {
        		$data['errMsg'] = '';
        		echo json_encode($data);
        		return ;
//         		$ret = $stored->Charge($operator_id, $_POST['stored_id'], $_POST['num'], $_POST['account']);
//         		$result = json_decode($ret, true);
//         		if ($result['status'] == ERROR_NONE) {
//         			Yii::app()->user->setFlash('success', '储值成功');
//         		}else {
//         			Yii::app()->user->setFlash('error', $result['errMsg']);
//         		}
        	}
        	$account = $_POST['account'];
        	$stored_id = $_POST['stored_id'];
        	$num = $_POST['num'];
        	$channel = $_POST['channel'];
        	$operator_id = Yii::app()->session['operator_id'];
        	$stored = new MemberStoredC();
        	$ret = $stored->createStoredOrder($account, $operator_id, $stored_id, $num, $channel);
        	$result = json_decode($ret, true);
        	if ($result['status'] == ERROR_NONE) {
        		if (!empty($result['order_id'])) {
        			$data['error'] = 'success';
        			$data['order_no'] = $result['order_no'];
        		}else {
        			$data['errMsg'] = '系统内部错误';
        		}
        	}else {
        		$data['errMsg'] = $result['errMsg'];
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
    			//查询门店信息中的打印机设置
    			$this->render('qrcode', array('img' => $img, 'msg' => $msg, 'print' => $print));
    		}
    		if ($action == ORDER_PAY_CHANNEL_ALIPAY_TM) { //支付宝条码支付
    			$this->render('barcode');
    		}
    		if ($action == ORDER_PAY_CHANNEL_WXPAY_SM) { //微信扫码支付
    			//调用微信扫码支付接口
    			$img = '';
    			$api = new WxpaySC();
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
    			//查询门店信息中的打印机设置
    			$this->render('qrcode', array('img' => $img, 'msg' => $msg, 'print' => $print));
    		}
    		if ($action == ORDER_PAY_CHANNEL_WXPAY_TM) { //微信条码支付
    			$this->render('barcode');
    		}
    		if ($action == ORDER_PAY_CHANNEL_CASH || $action == ORDER_PAY_CHANNEL_UNIONPAY) { //线下支付
    			//$this->offLinePay($order_no);
    			$this->redirect(array('confirm', 'orderNo' => $order_no));
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
    	$order = new MemberStoredC();
    	$ret = $order->getOrderDetail('', $order_no);
    	$result = json_decode($ret, true);
    	if ($result['status'] == ERROR_NONE) {
    		$stored = new StoredC();
    		$ret1 = $stored->getStoredDetails($result['data']['stored_id']);
    		$result1 = json_decode($ret1, true);
    		if ($result1['status'] == ERROR_NONE) {
    			$pay_money = $result1['data']['list']['stored_money'] * $result['data']['num'];
    		}
    	}
    	$this->render('confirm', array('money' => $pay_money));
    }
    
    /**
     * 线下支付或者无需支付
     * @param unknown $order_no
     */
    public function offLinePay($order_no) {
    	$order = new MemberStoredC();
    	//支付成功，修改订单状态
    	try {
    		$result = $order->orderPaySuccess($order_no, date('Y-m-d H:i:s'), NULL, NULL);
    		if (!empty($result) && $result['status'] == ERROR_NONE) {
    			//支付成功
    			$this->redirect(array('success', 'orderNo' => $order_no));
    		}
    	} catch (Exception $e) {
    		//支付失败
    		$msg = $e->getMessage();
    		$this->render('error', array('msg' => $msg));
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
    		$order = new MemberStoredC();
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
    			
    		if (!$ret1) {
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
    			return ;
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
    			
    		$order = new MemberStoredC();
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
    		//$order = new MemberStoredC();
    		$ret1 = $order->getOrderDetail('', $order_no);
    		$result1 = json_decode($ret1, true);
    		if ($result1['status'] == ERROR_NONE) {
    			$data['error'] = 'wait';
    			if ($result1['data']['pay_status'] == ORDER_STATUS_PAID) {
    				$data['error'] = 'success';
    			}
    		}else {
    			$data['errMsg'] = $result1['errMsg'];
    		}
    	}
    	echo json_encode($data);
    }
    
    /**
     * 支付成功页
     */
    public function actionSuccess() {
    	$this->layout = 'dialog';
    	$this->render('success');
    }
    
    /**
     * 订单撤销
     */
    public function actionRevoke() {
    	$data = array();
    	$data['error'] = 'failure';
    	if (isset($_POST['id']) && !empty($_POST['id'])) {
    		$operator_id = Yii::app()->session['operator_id'];
    		$transaction = Yii::app()->db->beginTransaction(); //开启事务
    		try {
    			$order = new MemberStoredC();
    			$ret = $order->revokeOrder($_POST['id'], $operator_id);
    			if (!isset($ret['status']) || $ret['status'] != ERROR_NONE) {
    				throw new Exception('系统内部错误');
    			}
    			$transaction->commit();
    			$data['error'] = 'success';
    		} catch (Exception $e) {
    			$transaction->rollback();
    			$msg = $e->getMessage();
    			$data['errMsg'] = $msg;
    		}
    	}
    	echo json_encode($data);
    }
    
    public function actionAmount() {
    	if (isset($_GET['sid']) && isset($_GET['num']) && !empty($_GET['sid']) && !empty($_GET['num'])) {
    		$operator_id = Yii::app()->session['operator_id'];
    		$data = '';
    		$stored  = new MemberStoredC();
    		$ret = $stored->getStoredList($operator_id);
    		$result = json_decode($ret, true);
    		if ($result['status'] == ERROR_NONE) {
    			foreach ($result['data']['list'] as $k => $v) {
    				if ($v['id'] == $_GET['sid']) {
    					$money = $v['money'] * $_GET['num'];
    					$bonus = $v['bonus'] * $_GET['num'];
    					$data = !empty($bonus) ? '本次充值'.$money.'元送'.$bonus.'元' : '本次充值'.$money.'元';
    				}
    			}
    		}
    		echo $data;
    	}
    }
    
    /**
     * 会员储值记录
     */
    public function actionMemberStoredList()
    {
        $ret = new MemberStoredC();
        $operatorId = Yii::app()->session['operator_id']; 
        $end = '';
        $start = '';
        $storedorderlist = '';
        if (isset($_GET['Time']) && !empty($_GET['Time'])) {
        	$tmp = explode(" - ", $_GET['Time']);
        	$start = $tmp[0];
        	$end = $tmp[1];
        }
        $account = isset($_GET['account']) ? $_GET['account'] : '';
        $model   = $ret->MemberStoredList($operatorId,$start,$end,$account);
        $list    = json_decode($model,true);
        $storedorderlist = array();
        if($list['status'] == ERROR_NONE)
        {
        	if (isset($list['data']['list'])) {
        		$storedorderlist = $list['data']['list'];
        	}     
        } else {
            $storedorderlist = '';
        }
        if(isset($_GET['excel']))
        {
            $data = array();
            $data[0]['account']        = '会员账号';            
            $data[0]['stored']         = '储值活动';
            $data[0]['num']            = '数量';   
            $data[0]['money']          = '实收金额';
            $data[0]['operator']       = '操作员';
            $data[0]['pay_time']       = '交易时间';
            $data[0]['pay_channel']       = '交易时间';
            if(!empty($storedorderlist))
            {
                foreach ($storedorderlist as $k => $v) 
                {
                	$data[$k + 1]['account']        = $v['account'].' ';
                	$data[$k + 1]['stored']         = '充'.$v['money'].'送'.$v['bonus'];
                	$data[$k + 1]['num']            = $v['num'];
                	$data[$k + 1]['money']          = $v['money'] * $v['num'];
                	$data[$k + 1]['operator']       = $v['operator'];
                	$data[$k + 1]['pay_time']       = $v['pay_time'];
                	$data[$k + 1]['pay_channel']         = $v['pay_channel'];
                }
            }
            $this -> excel($data,'充值记录表');
            Yii::app() -> end();
        }
        $this->render('memberStoredList',array('storedorderlist'=>$storedorderlist, 'pages' => $ret->page));
    }
    
    /**
     * 输入手机号查找会员等级并显示信息
     */
    public function actionUserSearch()
    {
        $tel = isset($_GET['tel']) ? $_GET['tel'] : '';
        $ret = new UserRule();
        $rat = $ret->UserSearch($tel);
        $search = json_decode($rat,true);        
        if($search['status'] == ERROR_NONE)
        {
            $rs = array('status'=>'ERROR_NONE','grade_name'=>$search['data']['grade_name'],'name'=>$search['data']['name']);
        } 
        if($search['status'] == ERROR_NO_DATA)
        {
            $rs = array('status'=>'ERROR_NO_DATA','errMsg'=>$search['data']['errMsg']);
        } 
        echo json_encode($rs);
    }
}

