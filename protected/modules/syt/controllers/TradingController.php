<?php
/**
 * 交易明细
 */

class TradingController extends sytController
{
	/**
	 * 交易列表
	 */
	public function actionTradingList()
	{
		$list = array();
		$arr = array();
		
		//搜索关键字:
		//交易时间
		$time = '';
		if(isset($_GET['Time'])){
			$time = $_GET['Time'];
		}
		//支付渠道
		$pay_channel = '';
		if(isset($_GET['pay_channel'])){
			$pay_channel = $_GET['pay_channel'];
		}
		//订单状态
		$order_status = '';
		$pay_status = '';
		if(isset($_GET['order_status'])){
			$tmp = $_GET['order_status'];
			$order_status = $tmp;
			//已付款
			if ($tmp == ORDER_STATUS_PAID) {
				$pay_status = ORDER_STATUS_PAID; //已付款
				$order_status = ORDER_STATUS_NORMAL; //正常
			}
			//待付款
			if ($tmp == ORDER_STATUS_UNPAID) {
				$pay_status = ORDER_STATUS_UNPAID; //待付款
				$order_status = ORDER_STATUS_NORMAL; // 正常
			}
		}
		//订单号
		$order_no = '';
		if(isset($_GET['order_no'])){
			$order_no = $_GET['order_no'];
		}
		//操作员id
		$operator = '';
		if(isset($_GET['operator'])){
			$operator = $_GET['operator'];
		}
		
		$tradingsc = new TradingSC();
		$operator_id = Yii::app ()->session ['operator_id'];
		$result = $tradingsc -> getTradingList($operator_id,$time,$pay_channel,$pay_status,$order_status,$order_no,$operator);
		$result = json_decode($result,true);
		if($result['status'] == ERROR_NONE){
			if(isset($result['data']['list'])){
				$list = $result['data']['list'];
				if(isset($result['arr']['list'])){
				  $arr = $result['arr']['list'];
				}
			}
		}
		foreach ($list as $k => $v) {
			if ($v['pay_status'] == ORDER_STATUS_PAID && $v['order_status'] == ORDER_STATUS_NORMAL) {
				$list[$k]['status'] = '已付款';
			}
			if ($v['pay_status'] == ORDER_STATUS_UNPAID) {
				$list[$k]['status'] = '待付款';
			}
			if ($v['order_status'] == ORDER_STATUS_REFUND) {
				$list[$k]['status'] = '已退款';
			}
			if ($v['order_status'] == ORDER_STATUS_PART_REFUND) {
				$list[$k]['status'] = '已部分退款';
			}
			if ($v['order_status'] == ORDER_STATUS_REVOKE) {
				$list[$k]['status'] = '已撤销';
			}
			if ($v['order_status'] == ORDER_STATUS_HANDLE_REFUND) {
				$list[$k]['status'] = '退款处理中';
			}
			if ($v['order_status'] == ORDER_STATUS_EXIT_REFUND) {
				$list[$k]['status'] = '有退款';
			}
		}
		//支付渠道
		$channel = $GLOBALS['ORDER_PAY_CHANNEL'];
		//隐藏部分
		unset($channel[ORDER_PAY_CHANNEL_ALIPAY]);
		unset($channel[ORDER_PAY_CHANNEL_POINTS]);
		//订单状态
		$status = array();
		$status[ORDER_STATUS_PAID] = $GLOBALS['ORDER_STATUS_PAY'][ORDER_STATUS_PAID];
		$status[ORDER_STATUS_UNPAID] = $GLOBALS['ORDER_STATUS_PAY'][ORDER_STATUS_UNPAID];
		$status[ORDER_STATUS_REFUND] = $GLOBALS['ORDER_STATUS'][ORDER_STATUS_REFUND];
		$status[ORDER_STATUS_PART_REFUND] = $GLOBALS['ORDER_STATUS'][ORDER_STATUS_PART_REFUND];
		$status[ORDER_STATUS_REVOKE] = $GLOBALS['ORDER_STATUS'][ORDER_STATUS_REVOKE];
		$status[ORDER_STATUS_HANDLE_REFUND] = $GLOBALS['ORDER_STATUS'][ORDER_STATUS_HANDLE_REFUND];
		//$status[ORDER_STATUS_EXIT_REFUND] = $GLOBALS['ORDER_STATUS'][ORDER_STATUS_EXIT_REFUND];
		//$status[ORDER_STATUS_CANCEL] = $GLOBALS['ORDER_STATUS'][ORDER_STATUS_CANCEL];
		//操作员
		$operators = array();
		$operatorC = new OperatorC();
		$ret = $operatorC->getOperatorDetails($operator_id);
		$result = json_decode($ret, true);
//        var_dump($result);
		if ($result['status'] == ERROR_NONE) {
			$store_id = $result['data']['store_id'];
            $role=$result['data']['role'];
			$store = new StoreC();
			$ret1 = $store->getStoreDetails($store_id);
			$result1 = json_decode($ret1, true);
//            var_dump($result1);
			if ($result1['status'] == ERROR_NONE) {
				$merchant_id = $result1['data']['merchant_id'];
				$ret2 = $operatorC->getOperators($merchant_id,$store_id,$role);
				$result2 = json_decode($ret2, true);
				if ($result2['status'] == ERROR_NONE) {
					$data = $result2['data']['list'];
					foreach ($data as $v) {
						$operators[$v['id']] = $v['name'].' ('.$v['number'].')';
					}
				}
			}
		}
//		var_dump($operators);
		$this->render('tradingList',array('list'=>$list,'arr'=>$arr, 'channel' => $channel, 'status' => $status, 'operators' => $operators, 'pages' => $tradingsc->page));
	}
	
	/**
	 * 订单删除
	 * $id  订单id
	 */
	public function actionDelTrading($id)
	{
		$tradingsc = new TradingSC();
		$result = $tradingsc -> delTrading($id);
		$result = json_decode($result,true);
		
		if($result['status'] == ERROR_NONE){
			$this-> redirect(array('tradingList'));
		}
		if($result['status'] == ERROR_SAVE_FAIL){
			echo "<script>alert('数据保存失败');</script>";
		}
	}
	
	
	/**
	 * 订单详情
	 * $id  订单id
	 */
	public function actionTradingDetails($id)
	{
		$list = array();
		$tradingsc = new TradingSC();
		$result = $tradingsc -> tradingDetails($id);
		$result = json_decode($result,true);
		if($result['status'] == ERROR_NONE){
			if(isset($result['data']['list'])){
			  $list = $result['data']['list'];
			}
		}
		$this -> render('tradingDetails',array('list'=>$list));
	}
	
	/**
	 * 导出Excel
	 */
	public function actionExportExcel()
	{
		$list = array();
		
		//搜索关键字:
		//交易时间
		$time = '';
		if(isset($_GET['Time'])){
			$time = $_GET['Time'];
		}
		//支付渠道
		$pay_channel = '';
		if(isset($_GET['pay_channel'])){
			$pay_channel = $_GET['pay_channel'];
		}
		//订单状态
		$order_status = '';
		$pay_status = '';
		if(isset($_GET['order_status'])){
			$tmp = $_GET['order_status'];
			$order_status = $tmp;
			//已付款
			if ($tmp == ORDER_STATUS_PAID) {
				$pay_status = ORDER_STATUS_PAID; //已付款
				$order_status = ORDER_STATUS_NORMAL; //正常
			}
			//待付款
			if ($tmp == ORDER_STATUS_UNPAID) {
				$pay_status = ORDER_STATUS_UNPAID; //待付款
				$order_status = ORDER_STATUS_NORMAL; // 正常
			}
		}
		//订单号
		$order_no = '';
		if(isset($_GET['order_no'])){
			$order_no = $_GET['order_no'];
		}
		//操作员id
		$operator = '';
		if(isset($_GET['operator'])){
			$operator = $_GET['operator'];
		}
		
		$tradingsc = new TradingSC();
		$operator_id = Yii::app ()->session ['operator_id'];
		$tradingsc->exportExcel($operator_id, $time, $pay_channel, $pay_status, $order_status, $order_no, $operator);
	}

	/**
	 * 订单退款
	 * @throws Exception
	 */
    public function actionRefund() {

        $operator_id = Yii::app()->session['operator_id'];

        $need_pwd = false;
        $data = array();
        $order = new OrderSC();
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $ret = $order->getOrderDetail($_GET['id'],'');
            $result = json_decode($ret, true);
            if ($result['status'] == ERROR_NONE) {
                if (isset($result['data'])) {
                    $data = $result['data'];
                    $data['money'] = $data['stored_paymoney']+$data['online_paymoney']+$data['unionpay_paymoney']+$data['cash_paymoney'];
                    if ($data['pwd'] == 'need') {
                        $need_pwd = true;
                    }
                    $alipay_account = $data['alipay_account'];
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
                    	$data['alipay_account'] = $tmp;
                    }
                    
                    //查询已退金额
                    $refund_money = 0;
                    $ret = $order->getRefundAmount($_GET['id']);
                    $result = json_decode($ret, true);
                    if ($result['status'] == ERROR_NONE) {
                    	$refund_money = $result['data'];
                    }
                }
            }else {
                $this->redirect('tradingList');
            }
        }else {
            $this->redirect('tradingList');
        }
        $money = 0; //退款金额
        $refund_time = ''; //退款时间
        if (isset($_POST['Refund']) && !empty($_POST['Refund'])) {
            $order_no = $_POST['Refund']['order_no'];
            $money = $_POST['Refund']['money'];
            $pwd = isset($_POST['Refund']['pwd']) ? $_POST['Refund']['pwd'] : '';
            $transaction = Yii::app()->db->beginTransaction(); //开启事务
            try {
            	//非空
            	if ($money === '') {
            		throw new Exception('退款金额不能为空');
            	}
            	//非零
            	if ($money === '0') {
            		throw new Exception('退款金额不能为零');
            	}
            	//非法字符
            	if (!is_numeric($money)) {
            		throw new Exception('错误的金额格式');
            	}
            	//小数点位数是否大于2位
            	$tmp = explode(".", $money);
            	if (count($tmp) > 1 && 	strlen($tmp[1]) > 2) {
            		throw new Exception('错误的金额格式');
            	}
            	//是否含有字符 -
            	$tmp = strstr($money, "-");
            	if ($tmp) {
            		throw new Exception('错误的金额格式');
            	}
            	$money += 0;
            	if ($money == 0) {
            		throw new Exception('退款金额不能为零');
            	}
            	
                $ret = $order->refundOrder($order_no,$money,$operator_id,$pwd);
                if (!isset($ret['status'])) {
                	throw new Exception('系统内部错误');
                }
                if ($ret['status'] != ERROR_NONE) {
                    throw new Exception('系统内部错误');
                }
                $transaction->commit();
                //用于打印的信息
                $money = sprintf("%.2f", $money); //保留2位小数
                $refund_time = $ret['refund_time'];
                
                if ($ret['refund_status'] == REFUND_STATUS_SUCCESS) {
                	Yii::app()->user->setFlash('success', '退款成功');
                }
                if ($ret['refund_status'] == REFUND_STATUS_FAIL) {
                	Yii::app()->user->setFlash('success', '退款失败');
                }
                if ($ret['refund_status'] == REFUND_STATUS_PROCESSING) {
                	Yii::app()->user->setFlash('success', '退款成功，请等待第三方支付平台处理该笔退款');
                }
            } catch (Exception $e) {
                $transaction->rollback();
                $msg = $e->getMessage();
                Yii::app()->user->setFlash('error', $msg);
            }
        }

        $this->render('refund', array('need_pwd' => $need_pwd, 'model' => $data, 'refund_money' => $refund_money, 'money' => $money, 'time' => $refund_time));
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
    			$order = new OrderSC();
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
    
    /**
     * 订单查询
     * @throws Exception
     */
    public function actionResearch() {
    	$data = array();
    	$data['error'] = 'failure';
    	if (isset($_POST['order_no']) && !empty($_POST['order_no'])) {
    		$order_no = $_POST['order_no'];
    		$order = new OrderSC();
    		$ret = $order->getOrderDetail('', $order_no);
    		$result = json_decode($ret, true);
    		if ($result['status'] != ERROR_NONE) {
    			$data['errMsg'] = $result['errMsg'];
    		}else {
    			$pay_channel = $result['data']['pay_channel']; //支付方式
    			if ($pay_channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $pay_channel == ORDER_PAY_CHANNEL_ALIPAY_TM) {
    				$api = new AlipaySC();
    				$ret = $api->alipaySearch($order_no);
    			}
    			if ($pay_channel == ORDER_PAY_CHANNEL_WXPAY_SM || $pay_channel == ORDER_PAY_CHANNEL_WXPAY_TM) {
    				$api = new WxpaySC();
    				$ret = $api->wxpaySearch($order_no);
    			}
    			$result = json_decode($ret, true);
    			if ($result['status'] != ERROR_NONE) {
    				$data['errMsg'] = $result['errMsg'];
    			}else {
    				$data['error'] = 'success';	
    			}
    		}
    	}
    	echo json_encode($data);
    }
    
}