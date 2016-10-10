<?php

class RunController extends Controller{
//     public function actionTest(){
//         $post = array();
//         $post['touser'] = array('lll','oXZ1iwz8Qj-G8vHwdHY4SmHk6tPc');
//         $post['wxcard']['card_id'] = 'pXZ1iwxqENBrV-z7WtUvpPKaLeRQ';
//         $post['msgtype'] = 'wxcard';
//         echo json_encode($post);
//     }
    
    
	//将pid不为空并且是旧的代理商的权限role字段改成子账号权限（2）
// 	public function actionChangeRole(){
// 		$agent = Agent::model() -> findAll();
// 		$transaction= Yii::app ()->db->beginTransaction();
// 		try {
// 			$count = 0;
// 			foreach ($agent as $k => $v){
// 				if(!empty($v -> pid) && $v -> agent_type == 1){
// 					$v -> role = 2;
// 					if($v -> update()){
// 						$count ++;
// 					}else{
// 						throw new Exception('更新失败');
// 					}
// 				}
// 			}
// 			$transaction->commit();
// 			echo '数据库修改成功影响'.$count.'条';
// 		} catch (Exception $e) {
// 			$transaction->rollBack();
// 			echo $e -> getMessage();
// 		}
// 	}
	
	
// 	//素材修改  将material_id = nul的素材赋值 material_id = id
// 	public function actionChangeMaterialId(){
// 		$material = Material::model() -> findAll();
// 		$transaction= Yii::app ()->db->beginTransaction();
// 		try {
// 			$count = 0;
// 			foreach ($material as $k => $v){
// 				if(empty($v -> material_id)){
// 					$v -> material_id = $v -> id;
// 					if($v -> update()){
// 						$count ++;
// 					}else{
// 						throw new Exception('更新失败');
// 					}
// 				}
// 			}
// 			$transaction->commit();
// 			echo '数据库修改成功影响'.$count.'条';
// 		} catch (Exception $e) {
// 			$transaction->rollBack();
// 			echo $e -> getMessage();
// 		}
// 	}
	
	
// 	//创建玩券对外对接商户号
// 	public function actionCreateMchid(){
// 		$transaction= Yii::app ()->db->beginTransaction();
// 		try {
// 			$merchant = Merchant::model() -> findAll('flag =:flag',array(
// 					':flag' => 1
// 			));
// 			$count = 0;
// 			foreach ($merchant as $k => $v){
// 				if(empty($v -> mchid)){
// 					$v -> mchid = $this->createMchid();
// 					echo $v -> mchid;
// 					if($v -> update()){
// 						$count ++;
// 					}else{
// 						$transaction->rollback(); //数据回滚
// 						throw new Exception('数据更新失败');
// 					}
// 				}
// 			}
// 			$transaction->commit(); //数据提交
// 			echo "影响".$count.'条';
// 		}catch (Exception $e){
// 			$transaction->rollback(); //数据回滚
// 			echo $e->getMessage(); //错误信息
// 		}
// 	}
	
// 	//获取定长的随机字符串
// 	private function getRandChar($length){
// 		$str = null;
// 		$strPol = "012356789";
// 		$strPolnoZero = "12356789";
// 		$max = strlen($strPol)-1;
// 		for($i=0;$i<$length;$i++){
// 			if ($i == 0){
// 				$str.=$strPolnoZero[rand(0,$max-1)];
// 			}else{
// 				$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
// 			}
// 		}
// 		return $str;
// 	}
	
// 	//生成玩券对外接口商户号
// 	private function createMchid(){
// 		$mchid = date('ym').$this->getRandChar(6);
// 		$merchant = Merchant::model() -> find('mchid = :mchid and flag = :flag',array(
// 				':mchid' => $mchid,
// 				':flag' => 1
// 		));
// 		while (!empty($merchant)){
// 			$mchid = date('yd').$this->getRandChar(6);
// 			$merchant = Merchant::model() -> find('mchid = :mchid and flag = :flag',array(
// 					':mchid' => $mchid,
// 					':flag' => FLAG_NO
// 			));
// 		}
// 		return $mchid;
// 	}
// 	//将merchantinfo表的微信商户名复制到merchant表的wx_name字段中
// 	public function actionSetWxmerchantname(){
// 		$transaction= Yii::app ()->db->beginTransaction();
// 		try {
// 			$merchantinfo = Merchantinfo::model() -> findAll();
// 			$count = 0;
// 			foreach ($merchantinfo as $k => $v){
// 				$merchant = Merchant::model() -> findByPk($v -> merchant_id);
// 				if(!empty($merchant)){
// 					$merchant -> wx_name = $v -> wx_merchant_name;
// 					if($merchant -> update()){
// 						$count ++;
// 					}else{
// 						throw new Exception('失败');
// 					}
// 				}
// 			}
// 			$transaction->commit(); //数据提交
// 			echo "影响".$count.'条';
// 		}catch (Exception $e){
// 			$transaction->rollback(); //数据回滚
// 			echo $e->getMessage(); //错误信息
// 		}
// 	}	

	/*
	 * 玩券架构变更
	 * 1、为旧的玩券管家新增一个权限为一级管理单元的管理单元，版本为标准版
	 * 2、为新建的管理单元新建一个管理员，并拥有所有权限
	 * */
// 	public function actionAddManagementAndManger(){
		
// // 		$transaction = Yii::app()->db->beginTransaction();
// 		try {
// 			$merchant = Merchant::model() -> findAll();
// 			$count = 0;
// 			foreach ($merchant as $k => $v){
// 				//开通玩券管家的商户
// 				if(!empty($v -> account)&&!empty($v -> pwd)){
// 					echo $count;
// 					$count ++;
// 					//新建管理单元
// 					$management = new Management();
// 					$management -> merchant_id = $v -> id;
// 					$management -> name = empty($v -> name)?$v -> wx_name:$v -> name;
// 					$management -> create_time = new CDbExpression('now()');
					
// 					//将原商户的支付账号设置给管理单元
// 					$management -> wx_merchant_type = $v -> wxpay_merchant_type;//微信支付的商户类型
// 					/**************普通微信当面付**************/
// 					$management -> wx_apiclient_cert = $v -> wechat_apiclient_cert;//apiclient_cert.pem文件
// 					$management -> wx_apiclient_key = $v -> wechat_apiclient_key;//apiclient_key.pem文件
// 					$management -> wx_appid = $v -> wechat_appid;//AppID应用id
// 					$management -> wx_appsecret = $v -> wechat_appsecret;//AppSecret应用密钥
// 					$management -> wx_api = $v -> wechat_key;//API密钥
// 					$management -> wx_mchid = $v -> wechat_mchid;//微信商户号
// 					/***************特约微信当面付************************/
// 					$management -> t_wx_appsecret = $v -> wechat_appsecret;//特约AppSecret应用密钥
// 					$management -> t_wx_mchid = $v -> wechat_mchid;//特约微信商户号
					
// 					$management -> alipay_api_version = $v -> alipay_api_version;//支付宝接口版本
// 					/***************支付宝1.0*********************/

// 					$management -> alipay_key = $v -> key;//支付宝安全校验码
// 					$management -> alipay_pid = $v -> partner;//支付宝合作身份id
// 					/***************支付宝2.0**********************/
// 					$management -> alipay_appid  = $v -> appid;//2.0 APPID

					
					
					
// 					if($management -> save()){
// 						//添加一个管理员
// 						$manager = new Manager();
// 						$manager -> management_id = $management -> id;
// 						$manager -> account = $v -> account;
// 						$manager -> pwd = $v -> pwd;
// 						$manager -> role = 1;
// // 						$right = array();
// // 						$right['right'] = 1;
// // 						$manager -> right = json_encode($right);
// 						$manager -> create_time =  new CDbExpression('now()');
// 						if($manager -> save()){
// // 							$transaction->commit(); //数据提交
// 						}else{
// 							throw new Exception('管理员保存失败');
// 						}
// 					}else{
// 						throw new Exception('管理单元保存失败');
// 					}
					
// 				}
// 			}
// 			echo '成功操作'.$count.'条s';
// 		} catch (Exception $e) {
// // 			$transaction->rollback(); //数据回滚
// 			echo $e->getMessage(); //错误信息
// 		}
		
// 	}
	
	
	//将门店移到商户的主管理单元
// 	public function actionStoreMoveToManagement(){
// 		$transaction = Yii::app()->db->beginTransaction();
// 		try {
// 			$store = Store::model() -> findAll();
// 			$count = 0;
// 			foreach ($store as $k => $v){
// // 				$merchant = Merchant::model() -> findByPk($v -> merchant_id);
// 				$management = Management::model() -> find('merchant_id =:merchant_id and flag =:flag',array(
// 						':merchant_id' => $v -> merchant_id,
// 						':flag' => 1
// 				));
// 				if(!empty($management)){
// 					$v -> management_id = $management -> id;
// 					if($v -> update()){
// 						$count ++;
// 					}else{
// 						throw new Exception("保存失败");
// 					}
// 				}
// 			}
// 			$transaction->commit(); //数据提交
// 			echo '成功操作'.$count.'条s';
// 		} catch (Exception $e) {
// 			$transaction->rollback(); //数据回滚
// 			echo $e->getMessage(); //错误信息
// 		}
// 	}
	

	
// 	/**
// 	 * 转换
// 	 */
// 	public function actionChange()
// 	{
// 		$id=$_GET['id'];
// 		$model=ShopIndex::model()->findByPk($id);
// 		$banner=json_decode($model->banner,true);
// 		$change_banner=array();
// 		$change_banner[0]['name']='shop_carousel';
// 		$i=0;
// 		foreach($banner as $key=>$value)
// 		{
// 			$change_banner[0]['url'][$i]=$value['img'];
	


    //导出交易流水（月报）
    public function actionExportOrder()
    {
        set_time_limit(0);
        ini_set('memory_limit', '4000M');
        include_once 'PHPExcel.php';
        include_once 'PHPExcel/Reader/Excel2007.php';
        include_once 'PHPExcel/Reader/Excel5.php';
        include_once 'PHPExcel/IOFactory.php';

        $criteria=new CDbCriteria();
        $criteria->addCondition('create_time>=:time1 and create_time<=:time2 and order_type=3 and pay_status = :pay_status');
        $criteria->params=array(
            ':time1' => '2016-04-28 00:00:00',
            ':time2' => '2016-05-04 23:59:59',
            ':pay_status' => ORDER_STATUS_PAID
        );
        $result = Order::model()->findAll($criteria);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','订单号')
            ->setCellValue('B1','商户名称')
            ->setCellValue('C1','商户所在区域')
            ->setCellValue('D1','用户年龄')
            ->setCellValue('E1','用户性别')
            ->setCellValue('F1','门店名称')
            ->setCellValue('G1','操作员名称')
            ->setCellValue('H1','交易号')
            ->setCellValue('I1','支付渠道')
            ->setCellValue('J1','储值支付')
            ->setCellValue('K1','线下支付')
            ->setCellValue('L1','银联支付')
            ->setCellValue('M1','现金支付')
            ->setCellValue('N1','支付状态')
            ->setCellValue('O1','订单状态')
            ->setCellValue('P1','储值确认状态')
            ->setCellValue('Q1','支付时间')
            ->setCellValue('R1','创建时间')
            ->setCellValue('S1','订单金额')
            ->setCellValue('T1','支付宝账号')
            ->setCellValue('U1','支付宝条码')
            ->setCellValue('V1','代金券金额')
            ->setCellValue('W1','折扣券金额')
            ->setCellValue('X1','不打折金额')
            ->setCellValue('Y1','银联卡号')
            ->setCellValue('Z1','终端类型')
            ->setCellValue('AA1','商家折扣')
            ->setCellValue('AB1','支付宝折扣')
            ->setCellValue('AC1','支付通道')
            ->setCellValue('AD1','返佣费率')
            ->setCellValue('AE1','微信用户openid')
            ->setCellValue('AF1','支付宝用户openid')
            ->setCellValue('AG1','微信唯一标示openid')
            ->setCellValue('AH1','所属加盟商')
            ->setCellValue('AI1','行业')
            ->setCellValue('AJ1','玩券管家版本')
            ->setCellValue('AK1','商户号')
            ->setCellValue('AL1','退款金额')
            ->setCellValue('AM1','交易类型');
//             ->setCellValue('AI1','加盟商所在区域');
        
        //设置列宽
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(20);
        $objActSheet->getColumnDimension('D')->setWidth(20);
        $objActSheet->getColumnDimension('E')->setWidth(20);
        $objActSheet->getColumnDimension('F')->setWidth(20);
        $objActSheet->getColumnDimension('G')->setWidth(20);
        $objActSheet->getColumnDimension('H')->setWidth(20);
        $objActSheet->getColumnDimension('I')->setWidth(20);
        $objActSheet->getColumnDimension('J')->setWidth(20);
        $objActSheet->getColumnDimension('K')->setWidth(20);
        $objActSheet->getColumnDimension('L')->setWidth(20);
        $objActSheet->getColumnDimension('M')->setWidth(20);
        $objActSheet->getColumnDimension('N')->setWidth(20);
        $objActSheet->getColumnDimension('O')->setWidth(20);
        $objActSheet->getColumnDimension('P')->setWidth(20);
        $objActSheet->getColumnDimension('Q')->setWidth(20);
        $objActSheet->getColumnDimension('R')->setWidth(20);
        $objActSheet->getColumnDimension('S')->setWidth(20);
        $objActSheet->getColumnDimension('T')->setWidth(20);
        $objActSheet->getColumnDimension('U')->setWidth(20);
        $objActSheet->getColumnDimension('V')->setWidth(20);
        $objActSheet->getColumnDimension('W')->setWidth(20);
        $objActSheet->getColumnDimension('X')->setWidth(20);
        $objActSheet->getColumnDimension('Y')->setWidth(20);
        $objActSheet->getColumnDimension('Z')->setWidth(20);
        
        //设置sheet名称
        $objActSheet -> setTitle('订单流水表');

            //数据添加
            $i=2;
            foreach($result as $k=>$v)
            {
                if(!empty($v -> merchant -> agent_id)){
                    $agent = Agent::model() -> findByPk($v -> merchant -> agent_id);
                }
                if(!empty($v -> merchant_id)){
                    $merchantinfo = Merchantinfo::model() -> find('merchant_id =:merchant_id',array(
                        ':merchant_id' => $v -> merchant_id
                    ));
                }
                $category = '';
                if(!empty($merchantinfo -> wx_business_category)){
                    $arr = explode(',', $merchantinfo -> wx_business_category);
                    $category = $GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['text'].$GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['sub'][$arr[1]]['text'].$GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['sub'][$arr[1]]['sub'][$arr[2]]['text'];
                }
            
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$i,$v -> order_no, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('B'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_m_name:'')
                ->setCellValue('C'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_m_address:'')
                ->setCellValue('D'.$i,!empty($v -> user_id) && !empty($v -> user -> birthday)?$this -> age($v -> user -> birthday):' ')
                ->setCellValue('E'.$i,!empty($v -> user_id) && !empty($v -> user -> sex)?$GLOBALS['__SEX'][$v -> user -> sex]:' ')
                ->setCellValue('F'.$i,$v -> store -> name)
                ->setCellValue('G'.$i,$v -> operator -> name)
                ->setCellValueExplicit('H'.$i,$v -> trade_no, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('I'.$i,$GLOBALS['ORDER_PAY_CHANNEL'][$v -> pay_channel])
                ->setCellValue('J'.$i,$v -> stored_paymoney)
                ->setCellValue('K'.$i,$v -> online_paymoney)
                ->setCellValue('L'.$i,$v -> unionpay_paymoney)
                ->setCellValue('M'.$i,$v -> cash_paymoney)
                ->setCellValue('N'.$i,$v -> pay_status == 1?'待付款':'已付款')
                ->setCellValue('O'.$i,$GLOBALS['ORDER_STATUS'][$v -> order_status])
                ->setCellValue('P'.$i,$v -> stored_confirm_status)
                ->setCellValue('Q'.$i,$v -> pay_time)
                ->setCellValue('R'.$i,$v -> create_time)
                ->setCellValue('S'.$i,$v -> order_paymoney)
                ->setCellValue('T'.$i,$v -> alipay_account)
                ->setCellValueExplicit('U'.$i,$v -> user_code, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('V'.$i,$v -> coupons_money)
                ->setCellValue('W'.$i,$v -> discount_money)
                ->setCellValue('X'.$i,$v -> undiscount_paymoney)
                ->setCellValue('Y'.$i,$v -> ums_card_no)
                ->setCellValue('Z'.$i,$GLOBALS['__TERMINAL_TYPE_POS'][$v -> terminal_type])
                ->setCellValue('AA'.$i,$v -> merchant_discount_money)
                ->setCellValue('AB'.$i,$v -> alipay_discount_money)
                ->setCellValue('AC'.$i,$GLOBALS['__ORDER_PAY_PASSAGEWAY'][$v -> pay_passageway])
                ->setCellValue('AD'.$i,$v -> commission_ratio)
                ->setCellValue('AE'.$i,$v -> wechat_user_id)
                ->setCellValueExplicit('AF'.$i,$v -> alipay_user_id, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('AG'.$i,$v -> wechat_user_p_id)
                ->setCellValue('AH'.$i,!empty($agent)?$agent -> name:'')
                ->setCellValue('AI'.$i,$category)
                ->setCellValue('AJ'.$i,!empty($v -> merchant_id)?$v -> merchant -> gj_product_id:'')
                ->setCellValue('AK'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_mchid:'')//商户号
                ->setCellValue('AL'.$i,'')//退款金额
                ->setCellValue('AM'.$i,'收款');//交易类型
            
                $i++;
            }
            
            //查找退款记录
            $criteria=new CDbCriteria();
            $criteria->addCondition('create_time>=:time1 and create_time<=:time2 and status = :status');
            $criteria->params=array(
                ':time1' => '2016-04-28 00:00:00',
                ':time2' => '2016-05-04 23:59:59',
                ':status' => 1
            );
            $result = RefundRecord::model()->findAll($criteria);
            foreach ($result as $k => $m){
                
                $v = Order::model() -> findByPk($m -> order_id);
                if($v -> order_type != 3){
                    continue;
                }
                
                if(!empty($v -> merchant -> agent_id)){
                    $agent = Agent::model() -> findByPk($v -> merchant -> agent_id);
                }
                if(!empty($v -> merchant_id)){
                    $merchantinfo = Merchantinfo::model() -> find('merchant_id =:merchant_id',array(
                        ':merchant_id' => $v -> merchant_id
                    ));
                }
                $category = '';
                if(!empty($merchantinfo -> wx_business_category)){
                    $arr = explode(',', $merchantinfo -> wx_business_category);
                    $category = $GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['text'].$GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['sub'][$arr[1]]['text'].$GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['sub'][$arr[1]]['sub'][$arr[2]]['text'];
                }
                
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$i,$v -> order_no, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('B'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_m_name:'')
                ->setCellValue('C'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_m_address:'')
                ->setCellValue('D'.$i,!empty($v -> user_id) && !empty($v -> user -> birthday)?$this -> age($v -> user -> birthday):' ')
                ->setCellValue('E'.$i,!empty($v -> user_id) && !empty($v -> user -> sex)?$GLOBALS['__SEX'][$v -> user -> sex]:' ')
                ->setCellValue('F'.$i,$v -> store -> name)
                ->setCellValue('G'.$i,$v -> operator -> name)
                ->setCellValueExplicit('H'.$i,$v -> trade_no, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('I'.$i,$GLOBALS['ORDER_PAY_CHANNEL'][$v -> pay_channel])
                ->setCellValue('J'.$i,$v -> stored_paymoney)
                ->setCellValue('K'.$i,$v -> online_paymoney)
                ->setCellValue('L'.$i,$v -> unionpay_paymoney)
                ->setCellValue('M'.$i,$v -> cash_paymoney)
                ->setCellValue('N'.$i,$v -> pay_status == 1?'待付款':'已付款')
                ->setCellValue('O'.$i,$GLOBALS['ORDER_STATUS'][$v -> order_status])
                ->setCellValue('P'.$i,$v -> stored_confirm_status)
                ->setCellValue('Q'.$i,$v -> pay_time)
                ->setCellValue('R'.$i,$v -> create_time)
                ->setCellValue('S'.$i,$v -> order_paymoney)
                ->setCellValue('T'.$i,$v -> alipay_account)
                ->setCellValueExplicit('U'.$i,$v -> user_code, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('V'.$i,$v -> coupons_money)
                ->setCellValue('W'.$i,$v -> discount_money)
                ->setCellValue('X'.$i,$v -> undiscount_paymoney)
                ->setCellValue('Y'.$i,$v -> ums_card_no)
                ->setCellValue('Z'.$i,$GLOBALS['__TERMINAL_TYPE_POS'][$v -> terminal_type])
                ->setCellValue('AA'.$i,$v -> merchant_discount_money)
                ->setCellValue('AB'.$i,$v -> alipay_discount_money)
                ->setCellValue('AC'.$i,$GLOBALS['__ORDER_PAY_PASSAGEWAY'][$v -> pay_passageway])
                ->setCellValue('AD'.$i,$v -> commission_ratio)
                ->setCellValue('AE'.$i,$v -> wechat_user_id)
                ->setCellValueExplicit('AF'.$i,$v -> alipay_user_id, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('AG'.$i,$v -> wechat_user_p_id)
                ->setCellValue('AH'.$i,!empty($agent)?$agent -> name:'')
                ->setCellValue('AI'.$i,$category)
                ->setCellValue('AJ'.$i,!empty($v -> merchant_id)?$v -> merchant -> gj_product_id:'')
                ->setCellValue('AK'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_mchid:'')//商户号
                ->setCellValue('AL'.$i,$m -> refund_money)//退款金额
                ->setCellValue('AM'.$i,'退款');//交易类型
                
                $i++;
            }
            
            
            $filename = "交易流水表".date("YmdHis");//定义文件名
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $this->outPut($filename);
            $objWriter->save("php://output");
    }
    
    
    
    //导出交易流水(对账)
    public function actionExportOrderForBalance ()
    {
        set_time_limit(0);
        ini_set('memory_limit', '4000M');
        include_once 'PHPExcel.php';
        include_once 'PHPExcel/Reader/Excel2007.php';
        include_once 'PHPExcel/Reader/Excel5.php';
        include_once 'PHPExcel/IOFactory.php';
    
        $criteria=new CDbCriteria();
        $criteria->addCondition('create_time>=:time1 and create_time<=:time2 and order_type=3 and pay_status = :pay_status');
        $criteria->params=array(
            ':time1' => '2016-04-16 00:00:00',
            ':time2' => '2016-04-31 23:59:59',
            ':pay_status' => ORDER_STATUS_PAID
        );
        $result = Order::model()->findAll($criteria);
    
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','订单号')
        ->setCellValue('B1','商户名称')
        ->setCellValue('C1','商户所在区域')
        ->setCellValue('D1','用户年龄')
        ->setCellValue('E1','用户性别')
        ->setCellValue('F1','门店名称')
        ->setCellValue('G1','操作员名称')
        ->setCellValue('H1','交易号')
        ->setCellValue('I1','支付渠道')
        ->setCellValue('J1','储值支付')
        ->setCellValue('K1','线下支付')
        ->setCellValue('L1','银联支付')
        ->setCellValue('M1','现金支付')
        ->setCellValue('N1','支付状态')
        ->setCellValue('O1','订单状态')
        ->setCellValue('P1','储值确认状态')
        ->setCellValue('Q1','支付时间')
        ->setCellValue('R1','创建时间')
        ->setCellValue('S1','订单金额')
        ->setCellValue('T1','支付宝账号')
        ->setCellValue('U1','支付宝条码')
        ->setCellValue('V1','代金券金额')
        ->setCellValue('W1','折扣券金额')
        ->setCellValue('X1','不打折金额')
        ->setCellValue('Y1','银联卡号')
        ->setCellValue('Z1','终端类型')
        ->setCellValue('AA1','商家折扣')
        ->setCellValue('AB1','支付宝折扣')
        ->setCellValue('AC1','支付通道')
        ->setCellValue('AD1','返佣费率')
        ->setCellValue('AE1','微信用户openid')
        ->setCellValue('AF1','支付宝用户openid')
        ->setCellValue('AG1','微信唯一标示openid')
        ->setCellValue('AH1','所属加盟商')
        ->setCellValue('AI1','行业')
        ->setCellValue('AJ1','玩券管家版本');
        //             ->setCellValue('AI1','加盟商所在区域');
    
        //设置列宽
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(20);
        $objActSheet->getColumnDimension('D')->setWidth(20);
        $objActSheet->getColumnDimension('E')->setWidth(20);
        $objActSheet->getColumnDimension('F')->setWidth(20);
        $objActSheet->getColumnDimension('G')->setWidth(20);
        $objActSheet->getColumnDimension('H')->setWidth(20);
        $objActSheet->getColumnDimension('I')->setWidth(20);
        $objActSheet->getColumnDimension('J')->setWidth(20);
        $objActSheet->getColumnDimension('K')->setWidth(20);
        $objActSheet->getColumnDimension('L')->setWidth(20);
        $objActSheet->getColumnDimension('M')->setWidth(20);
        $objActSheet->getColumnDimension('N')->setWidth(20);
        $objActSheet->getColumnDimension('O')->setWidth(20);
        $objActSheet->getColumnDimension('P')->setWidth(20);
        $objActSheet->getColumnDimension('Q')->setWidth(20);
        $objActSheet->getColumnDimension('R')->setWidth(20);
        $objActSheet->getColumnDimension('S')->setWidth(20);
        $objActSheet->getColumnDimension('T')->setWidth(20);
        $objActSheet->getColumnDimension('U')->setWidth(20);
        $objActSheet->getColumnDimension('V')->setWidth(20);
        $objActSheet->getColumnDimension('W')->setWidth(20);
        $objActSheet->getColumnDimension('X')->setWidth(20);
        $objActSheet->getColumnDimension('Y')->setWidth(20);
        $objActSheet->getColumnDimension('Z')->setWidth(20);
    
        //设置sheet名称
        $objActSheet -> setTitle('订单流水表');
    
        //数据添加
        $i=2;
        foreach($result as $k=>$v)
        {
            if(!empty($v -> merchant -> agent_id)){
                $agent = Agent::model() -> findByPk($v -> merchant -> agent_id);
            }
            if(!empty($v -> merchant_id)){
                $merchantinfo = Merchantinfo::model() -> find('merchant_id =:merchant_id',array(
                    ':merchant_id' => $v -> merchant_id
                ));
            }
            $category = '';
            if(!empty($merchantinfo -> wx_business_category)){
                $arr = explode(',', $merchantinfo -> wx_business_category);
                $category = $GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['text'].$GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['sub'][$arr[1]]['text'].$GLOBALS['WECHAT_MERCHANT_JYLM'][$arr[0]]['sub'][$arr[1]]['sub'][$arr[2]]['text'];
            }
    
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit('A'.$i,$v -> order_no, PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValue('B'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_m_name:'')
            ->setCellValue('C'.$i,!empty($v -> merchant_id)?$v -> merchant -> wq_m_address:'')
            ->setCellValue('D'.$i,!empty($v -> user_id) && !empty($v -> user -> birthday)?$this -> age($v -> user -> birthday):' ')
            ->setCellValue('E'.$i,!empty($v -> user_id) && !empty($v -> user -> sex)?$GLOBALS['__SEX'][$v -> user -> sex]:' ')
            ->setCellValue('F'.$i,$v -> store -> name)
            ->setCellValue('G'.$i,$v -> operator -> name)
            ->setCellValueExplicit('H'.$i,$v -> trade_no, PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValue('I'.$i,$GLOBALS['ORDER_PAY_CHANNEL'][$v -> pay_channel])
            ->setCellValue('J'.$i,$v -> stored_paymoney)
            ->setCellValue('K'.$i,$v -> online_paymoney)
            ->setCellValue('L'.$i,$v -> unionpay_paymoney)
            ->setCellValue('M'.$i,$v -> cash_paymoney)
            ->setCellValue('N'.$i,$v -> pay_status == 1?'待付款':'已付款')
            ->setCellValue('O'.$i,$GLOBALS['ORDER_STATUS'][$v -> order_status])
            ->setCellValue('P'.$i,$v -> stored_confirm_status)
            ->setCellValue('Q'.$i,$v -> pay_time)
            ->setCellValue('R'.$i,$v -> create_time)
            ->setCellValue('S'.$i,$v -> order_paymoney)
            ->setCellValue('T'.$i,$v -> alipay_account)
            ->setCellValueExplicit('U'.$i,$v -> user_code, PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValue('V'.$i,$v -> coupons_money)
            ->setCellValue('W'.$i,$v -> discount_money)
            ->setCellValue('X'.$i,$v -> undiscount_paymoney)
            ->setCellValue('Y'.$i,$v -> ums_card_no)
            ->setCellValue('Z'.$i,$GLOBALS['__TERMINAL_TYPE_POS'][$v -> terminal_type])
            ->setCellValue('AA'.$i,$v -> merchant_discount_money)
            ->setCellValue('AB'.$i,$v -> alipay_discount_money)
            ->setCellValue('AC'.$i,$GLOBALS['__ORDER_PAY_PASSAGEWAY'][$v -> pay_passageway])
            ->setCellValue('AD'.$i,$v -> commission_ratio)
            ->setCellValue('AE'.$i,$v -> wechat_user_id)
            ->setCellValueExplicit('AF'.$i,$v -> alipay_user_id, PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValue('AG'.$i,$v -> wechat_user_p_id)
            ->setCellValue('AH'.$i,!empty($agent)?$agent -> name:'')
            ->setCellValue('AI'.$i,$category)
            ->setCellValue('AJ'.$i,!empty($v -> merchant_id)?$v -> merchant -> gj_product_id:'');
    
            $i++;
        }
        $filename = "交易流水表".date("YmdHis");//定义文件名
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $this->outPut($filename);
        $objWriter->save("php://output");
    }
    
    
    //拉取商户用户粉丝数量
    //导出交易流水
    public function actionExportUserNum()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1500M');
        include_once 'PHPExcel.php';
        include_once 'PHPExcel/Reader/Excel2007.php';
        include_once 'PHPExcel/Reader/Excel5.php';
        include_once 'PHPExcel/IOFactory.php';
    
        $merchant = Merchant::model() -> findAll('flag=:flag',array(
            ':flag' => FLAG_NO
        ));
    
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1','玩券商户号')
        ->setCellValue('B1','商户名称')
        ->setCellValue('C1','日期')
        ->setCellValue('D1','新增支付宝粉丝数')
        ->setCellValue('E1','新增微信粉丝数')
        ->setCellValue('F1','新增会员数')
        ->setCellValue('G1','累计支付宝粉丝数')
        ->setCellValue('H1','累计微信粉丝数')
        ->setCellValue('I1','累计会员数')
        ->setCellValue('J1','玩券版本');
        
    
        //设置列宽
        $objActSheet = $objPHPExcel->getActiveSheet();
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(20);
        $objActSheet->getColumnDimension('D')->setWidth(20);
        $objActSheet->getColumnDimension('E')->setWidth(20);
        $objActSheet->getColumnDimension('F')->setWidth(20);
        $objActSheet->getColumnDimension('G')->setWidth(20);
        $objActSheet->getColumnDimension('H')->setWidth(20);
        $objActSheet->getColumnDimension('I')->setWidth(20);
    
        //设置sheet名称
        $objActSheet -> setTitle('商户粉丝会员统计表');
        $i=2;
        $start_time = '2016-04-28 00:00:00';
        $end_time = '2016-05-04 23:59:59';
        //数据添加
        $i=2;
        foreach($merchant as $k=>$v)
        {
            $j = 0;
            while (strtotime($end_time) >= strtotime($start_time)+$j*24*60*60){
                
                //新增支付宝粉丝数
                $new_num_alipayfans = User::model() -> count(
                    'merchant_id = :merchant_id and flag =:flag and create_time >= :time1 and create_time <= :time2 and alipay_status = :alipay_status',
                    array(
                        ':flag' => FLAG_NO,
                        ':time1' => date('Y-m-d 00:00:00',strtotime($start_time)+$j*24*60*60),
                        ':time2' => date('Y-m-d 23:59:59',strtotime($start_time)+$j*24*60*60),
                        ':alipay_status' => 2,
                        ':merchant_id' => $v -> id
                    )
                ); 
                //新增微信粉丝数
                $new_num_wechatfans = User::model() -> count(
                    'merchant_id = :merchant_id and flag =:flag and create_time >= :time1 and create_time <= :time2 and wechat_status = :wechat_status',
                    array(
                        ':flag' => FLAG_NO,
                        ':time1' => date('Y-m-d 00:00:00',strtotime($start_time)+$j*24*60*60),
                        ':time2' => date('Y-m-d 23:59:59',strtotime($start_time)+$j*24*60*60),
                        ':wechat_status' => 2,
                        ':merchant_id' => $v -> id
                    )
                );
                //新增会员数
                $new_num_memberfans = User::model() -> count(
                    'merchant_id = :merchant_id and flag =:flag and create_time >= :time1 and create_time <= :time2 and account is not null',
                    array(
                        ':flag' => FLAG_NO,
                        ':time1' => date('Y-m-d 00:00:00',strtotime($start_time)+$j*24*60*60),
                        ':time2' => date('Y-m-d 23:59:59',strtotime($start_time)+$j*24*60*60),
                        ':merchant_id' => $v -> id
                    )
                );
                //累计支付宝粉丝数
                $total_num_alipayfans = User::model() -> count(
                    'merchant_id = :merchant_id and flag =:flag  and create_time <= :time2 and alipay_status = :alipay_status',
                    array(
                        ':flag' => FLAG_NO,
                        ':time2' => date('Y-m-d 23:59:59',strtotime($start_time)+$j*24*60*60),
                        ':alipay_status' => 2,
                        ':merchant_id' => $v -> id
                    )
                );
                //累计微信粉丝数
                $total_num_wechatfans = User::model() -> count(
                    'merchant_id = :merchant_id and flag =:flag and create_time <= :time2 and wechat_status = :wechat_status',
                    array(
                        ':flag' => FLAG_NO,
                        ':time2' => date('Y-m-d 23:59:59',strtotime($start_time)+$j*24*60*60),
                        ':wechat_status' => 2,
                        ':merchant_id' => $v -> id
                    )
                );
                //累计会员数
                $total_num_memberfans = User::model() -> count(
                    'merchant_id = :merchant_id and flag =:flag and create_time <= :time2 and account is not null',
                    array(
                        ':flag' => FLAG_NO,
                        ':time2' => date('Y-m-d 23:59:59',strtotime($start_time)+$j*24*60*60),
                        ':merchant_id' => $v -> id
                    )
                );
                $p = $i+$j;
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueExplicit('A'.$p,$v -> wq_mchid, PHPExcel_Cell_DataType::TYPE_STRING)
                ->setCellValue('B'.$p,$v -> wq_m_name)
                ->setCellValue('C'.$p,date('Y-m-d',strtotime($start_time)+$j*24*60*60))
                
                ->setCellValue('D'.$p,$new_num_alipayfans)
                ->setCellValue('E'.$p,$new_num_wechatfans)
                ->setCellValue('F'.$p,$new_num_memberfans)
                ->setCellValue('G'.$p,$total_num_alipayfans)
                ->setCellValue('H'.$p,$total_num_wechatfans)
                ->setCellValue('I'.$p,$total_num_memberfans)
                ->setCellValue('J'.$p,$v -> gj_product_id);
                $j ++;  
            }
    
            $i += $j;
        }
        $filename = "粉丝会员统计表".date("YmdHis");//定义文件名
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $this->outPut($filename);
        $objWriter->save("php://output");
    }
    
    
    
    
    
    //计算年龄函数
    function age($birthday) {
        $age = date('Y', time()) - date('Y', strtotime($birthday)) - 1;  
        if (date('m', time()) == date('m', strtotime($birthday))){  
            if (date('d', time()) > date('d', strtotime($birthday))){  
                $age++;  
            }  
        }elseif (date('m', time()) > date('m', strtotime($birthday))){  
            $age++;  
        }  
        return $age; 
    }

    /**
     * 到浏览器  浏览器下载excel
     */
    public function outPut($filename)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename={$filename}.xls");
        header("Content-Transfer-Encoding:binary");
    }
    
    //修复用户表粉丝创建时间为空的错误
//     public function actionfixUserCreateTime(){
//         set_time_limit(0);
//         ini_set('memory_limit', '1500M');
//         $user = User::model() -> findAll('create_time is null');
//         foreach ($user as $k => $v){
//             if(!empty($v -> wechat_subscribe_time)){
//                 $v -> create_time = $v -> wechat_subscribe_time;
//                 $v -> update();
//             }elseif (!empty($v -> alipay_subscribe_time)){
//                 $v -> create_time = $v -> alipay_subscribe_time;
//                 $v -> update();
//             }
//         }
//     }

    //导出商户交易信息
//    public function actionExportMerchantMoney()
//    {
//        include_once 'PHPExcel.php';
//        include_once 'PHPExcel/Reader/Excel2007.php';
//        include_once 'PHPExcel/Reader/Excel5.php';
//        include_once 'PHPExcel/IOFactory.php';
//        $data=array();
//        $month_count=0;//月份计数
//        $criteria=new CDbCriteria();
//        $criteria->addCondition('');
//    }




// 			$shop_type=intval($value['type']);
// 			if($shop_type==SHOP_TYPE_GROUP)
// 			{
// 				$group=ShopGroup::model()->findByPk(intval($value['url']));
// 				$change_banner[0]['href'][$i]=$group->id.";".$group->name.";".$shop_type;
// 			}
// 			else if($shop_type==SHOP_TYPE_PRODUCT)
// 			{
// 				$product=ShopProduct::model()->findByPk(intval($value['url']));
// 				$change_banner[0]['href'][$i]=$product->id.";".$product->name.";".$shop_type;
// 			}
// 			else
// 			{
// 				$change_banner[0]['href'][$i]='null';
// 			}
// 			$i++;
// 			if($i==4)
// 				break;
// 		}
	
// 		$change_banner[1]['name']='shop_group';
// 		$change_banner[1]['url']='null';
// 		$group_id=$model->group_id;
// 		$groupid=array();
// 		$groupid=explode(',',$group_id);
// 		$j=0;
// 		var_dump($groupid);
// 		foreach($groupid as $k=>$v)
// 		{
// 			if(!empty($v))
// 			{
// 				$shop_group=ShopGroup::model()->findByPk($v);
// 				$change_banner[1]['href'][$j]=$shop_group->id.";".$shop_group->name.";1";
// 				$j++;
// 			}
// 		}
	
// 		$model->banner=json_encode($change_banner);
// 		if($model->save())
// 			echo "success";
// 	}
	
 
	//将旧商户商户信息跑到新字段中
// 	public function actionRefrshMerchant(){
		
// 		$transaction = Yii::app()->db->beginTransaction();
// 		try {
// 			$merchant = Merchant::model() -> findAll('flag =:flag',array(
// 					':flag' => 1
// 			));
// 			foreach ($merchant as $k => $v){
// 				if(!empty($v -> wx_name)){
// 					$v -> wq_m_name = $v -> wx_name;
// 				}else{
// 					if(!empty($v -> name)){
// 						$v -> wq_m_name = $v -> name;
// 					}
// 				}
// 				if($v -> update()){
					
// 				}else{
// 					throw new Exception('更新失败');
// 				}
// 			}
// 			$transaction->commit(); //数据提交
// 			echo '操作成功';
// 		} catch (Exception $e) {
// 			$transaction->rollback(); //数据回滚
// 			echo $e->getMessage(); //错误信息
// 		}
// 	}

	//门店地址设置地址编码
// 	public function actionSetStoreAddress(){
// 		set_time_limit(0);
// 		ini_set('memory_limit', '1500M');
// 		$model = Store::model() -> findAll();
// 		$count = 0;
// 		foreach ($model as $k => $v){
// 			if(!empty($v -> address)){
// 				$add_arr = explode(',', $v -> address);
// 				$p = ShopCity::model() -> find('name =:name and level =:level',array(
// 						':name' => $add_arr[0],
// 						':level' => 1
// 				));
// 				$c = ShopCity::model() -> find('name =:name and level =:level',array(
// 						':name' => $add_arr[1],
// 						':level' => 2
// 				));
// 				$a = ShopCity::model() -> find('name =:name and level =:level',array(
// 						':name' => $add_arr[2],
// 						':level' => 3
// 				));
// 				if(!empty($p) && !empty($c) && !empty($a) ){
// 					if(!empty($add_arr[3])){
// 						$code = $p -> code.','.$c -> code.','.$a -> code.','.$add_arr[3];
// 					}else{
// 						$code = $p -> code.','.$c -> code.','.$a -> code.',';
// 					}
// 				}
// 				$v -> address_code = $code;
// 				if($v -> update()){
// 					$count ++;
// 				}else{
// 					break;
// 				}
// 			}
// 		}
// 		echo $count;
// 	}
	
	
// 	public function actionSetStoreAlipaySyncType(){
// 		set_time_limit(0);
// 		ini_set('memory_limit', '1500M');
// 		$model = Store::model() -> findAll();
// 		$count = 0;
// 		foreach ($model as $k => $v){
// 			if(!empty($v -> alipay_store_id)){
// 				$v -> alipay_sync_type = 2;
// 			}else{
// 				$v -> alipay_sync_type = 3;
// 			}
// 			if($v -> update()){
// 				$count ++;
// 			}else{
// 				break;
// 			}
// 		}
// 		echo $count;
// 	}

	//将东钱湖门店进行关联操作
// 	public function actionSetDqhStore(){
// 		$merchant_id_arr = array(234,224,233,229,237,226,285,230,235,225,238,228,236,232);
// 		$merchant_dqh_id = 440;
// 		$total_m = 0;
// 		$total_s = 0;
// 		$total_success_s = 0;
// 		foreach ($merchant_id_arr as $k => $v){
// 			$store = Store::model() -> findAll('merchant_id =:merchant_id and flag =:flag',array(
// 					':merchant_id' => $v,
// 					':flag' => 1
// 			));
// 			$total_m ++;
// 			$store_success = 0;
// 			foreach ($store as $key => $value){
// 				$total_s++;
// 				$store_dqh = new Store();
// 				$store_dqh -> merchant_id = $merchant_dqh_id;
// 				$store_dqh -> name = $value -> name;
// 				$store_dqh -> branch_name = $value -> branch_name;
// 				$store_dqh -> number = $value -> number;
// 				$store_dqh -> alipay_store_id = $value -> alipay_store_id;
// 				$store_dqh -> telephone = $value -> telephone;
// 				$store_dqh -> address = $value -> address;
// 				$store_dqh -> address_code = $value -> address_code;
// 				$store_dqh -> lng = $value -> lng;
// 				$store_dqh -> lat = $value -> lat;
// 				$store_dqh -> logo = $value -> logo;
// 				$store_dqh -> introduction = $value -> introduction;
// 				$store_dqh -> alipay_sync_type = $value -> alipay_sync_type;
// 				$store_dqh -> alipay_sync_time = $value -> alipay_sync_time;
// 				$store_dqh -> alipay_sync_verify_status = $value -> alipay_sync_verify_status;
// 				$store_dqh -> first_img = $value -> first_img;
// 				$store_dqh -> first_img_id = $value -> first_img_id;
				
// 				$store_dqh -> open_time = $value -> open_time;
// 				$store_dqh -> brand = $value -> brand;
// 				$store_dqh -> brand_logo = $value -> brand_logo;
// 				$store_dqh -> brand_logo_id = $value -> brand_logo_id;
// 				$store_dqh -> phone_num = $value -> phone_num;
// 				$store_dqh -> per_capita = $value -> per_capita;
// 				$store_dqh -> image = $value -> image;
// 				$store_dqh -> image_id = $value -> image_id;
// 				$store_dqh -> category_id = $value -> category_id;
// 				$store_dqh -> category = $value -> category;
// 				$store_dqh -> business_license = $value -> business_license;
// 				$store_dqh -> business_license_id = $value -> business_license_id;
// 				$store_dqh -> auth_letter = $value -> auth_letter;
// 				$store_dqh -> auth_letter_id = $value -> auth_letter_id;
// 				$store_dqh -> status = $value -> status;
// 				$store_dqh -> flag = $value -> flag;
// 				$store_dqh -> is_print = $value -> is_print;
// 				$store_dqh -> create_time = $value -> create_time;
// 				$store_dqh -> last_time = $value -> last_time;
// 				$store_dqh -> print_name = $value -> print_name;
// 				$store_dqh -> alipay_seller_id = $value -> alipay_seller_id;
// 				$store_dqh -> if_wx_open = $value -> if_wx_open;
// 				$store_dqh -> wx_use_pro = $value -> wx_use_pro;
// 				$store_dqh -> wx_merchant_type = $value -> wx_merchant_type;
// 				$store_dqh -> wx_apiclient_cert = $value -> wx_apiclient_cert;
				
// 				$store_dqh -> wx_apiclient_key = $value -> wx_apiclient_key;
// 				$store_dqh -> wx_appid = $value -> wx_appid;
// 				$store_dqh -> wx_appsecret = $value -> wx_appsecret;
// 				$store_dqh -> wx_api = $value -> wx_api;
// 				$store_dqh -> wx_mchid = $value -> wx_mchid;
// // 				$store_dqh -> t_wx_appid = $value -> t_wx_appid;
// 				$store_dqh -> t_wx_mchid = $value -> t_wx_mchid;
// 				$store_dqh -> if_alipay_open = $value -> if_alipay_open;
// 				$store_dqh -> alipay_use_pro = $value -> alipay_use_pro;
// 				$store_dqh -> alipay_api_version = $value -> alipay_api_version;
// 				$store_dqh -> alipay_pid = $value -> alipay_pid;
// 				$store_dqh -> alipay_key = $value -> alipay_key;
// 				$store_dqh -> alipay_appid = $value -> alipay_appid;
// 				$store_dqh -> licence_code = $value -> licence_code;
// 				$store_dqh -> licence_name = $value -> licence_name;
				
// 				$store_dqh -> business_certificate = $value -> business_certificate;
// 				$store_dqh -> business_certificate_id = $value -> business_certificate_id;
// 				$store_dqh -> business_certificate_expires = $value -> business_certificate_expires;
// 				$store_dqh -> koubei_store_id = $value -> koubei_store_id;
// 				$store_dqh -> audit_desc = $value -> audit_desc;
// 				$store_dqh -> relation_store_id = $value -> id;
// 				if($store_dqh ->save()){
// 					$total_success_s++;
// 					$store_success ++;
// 				}else{
					
// 				}
				
// 			}
// 			echo '商户id：'.$v.' '.'成功同步门店数：'.$store_success;
// 		}
// 		echo '商户数：'.$total_m.' '.'成功同步门店数：'.$total_success_s;
// 	}

	
// 	public function actionWxpayUpdate() {
// 		$merchant = Merchant::model()->findAll();
// 		$success_count = 0;
// 		$fail_count = 0;
// 		foreach ($merchant as $k => $v) {
// 			if ($v['wxpay_merchant_type'] == 2) {
// 				$v['t_wx_appid'] = $v['wechat_appid'];
// 				$v['t_wx_mchid'] = $v['wechat_mchid'];
// 				if($v->save()){
// 					$success_count ++;
// 				}else{
// 					$fail_count ++;
// 				}
// 			}
// 		}
// 		echo '成功：'.$success_count.' '.'失败：'.$fail_count;
// 	}
	
	//东钱湖商品编号复制到商品sku中
// 	public function actionDqhMerchantNoMoveToSku(){
// 		$d_product = DProduct::model() -> findAll();
// 		$count = 0;
// 		foreach ($d_product as $k => $v){
// 			$sku = DProductSku::model() -> findAll('product_id = :product_id and flag = :flag',array(
// 					':product_id' => $v -> id,
// 					':flag' => 1
// 			));
// 			foreach ($sku as $m => $n){
// 				$n -> merchant_no = $v -> third_party_product_id;
// 				$n -> update();
// 			}
// 			$count ++;
// 		}
// 		echo $count;
// 	}
	
	//生成订单的商户id
// 	public function actionCreateMerchantId(){
// 		set_time_limit(0);
// 		ini_set('memory_limit', '1500M');
// 		$order = Order::model() -> findAll();
// 		$count = 0;
// 		foreach ($order as $k => $v){
// 			$store = Store::model() -> findByPk($v -> store_id);
// 			$v -> merchant_id = $store -> merchant_id;
// 			if($v -> update()){
// 				$count ++;
// 			}
// 		}
// 		echo $count;
// 	}

// 	//合并李仲及其子账号的商户名重复的商户
// 	public function actionMergeLeeZMerchant(){
// 				set_time_limit(0);
// 				ini_set('memory_limit', '1500M');
// 		try {
// 			//服务商
// 			$agent_id_arr = array(57,73,74,75,76,77,81,83,92);
// 			$criteria = new CDbCriteria;
// 			$criteria->addInCondition('agent_id',$agent_id_arr);
// // 			$criteria->addCondition('flag=:flag');
// // 			$criteria->params[':flag'] = 1;
// 			$LeeZ_merchant = Merchant::model() -> findAll($criteria);
// 			$count = 0;
// 			foreach ($LeeZ_merchant as $k => $v){
// 				$merchant = Merchant::model() -> find('wq_m_name =:wq_m_name and flag =:flag and account is null',array(
// 						':wq_m_name' => $v -> wq_m_name,
// 						':flag' => 1
// 				));
// 				if(!empty($merchant)){
// // 					print_r($merchant);
// 					$v -> wechat_merchant_no = $merchant -> wechat_merchant_no;
// 					$v -> wx_name = $merchant -> wx_name;
// 					$v -> wechat_verify_status = $merchant -> wechat_verify_status;
// 					$merchantInfo = Merchantinfo::model() -> find('merchant_id =:merchant_id',array(
// 							':merchant_id' => $merchant -> id
// 					));
// 					if(empty($merchantInfo)){
// 						continue;
// 					}
// 					$merchantInfo -> merchant_id = $v -> id;
// 					$merchant -> flag = 2;
// 					if($v -> update()){
						
// 						if($merchantInfo -> update()){
// 							if($merchant -> update()){
// 								$count ++;
// 							}
// 						}
// 					}
// 				}
// 			}
// 			echo $count;
// 		} catch (Exception $e) {
// 			echo $e -> getMessage();
// 		}
// 	}
	
// 	//生成玩券商户编号
// 	public function actionCreateWqMchid(){
// 		$merchant = Merchant::model() -> findAll('wq_mchid is null');
// 		$count = 0;
// 		foreach ($merchant as $k => $v){
// 			$wq_mchid = '1'.$this -> getRandChar(9);
// 			$merchant_tmp = Merchant::model() -> find('wq_mchid =:wq_mchid',array(
// 					':wq_mchid' => $wq_mchid
// 			));
// 			while (!empty($merchant_tmp)){
// 				$wq_mchid = '1'.$this -> getRandChar(9);
// 				$merchant_tmp = Merchant::model() -> find('wq_mchid =:wq_mchid',array(
// 						':wq_mchid' => $wq_mchid
// 				));
// 			}
// 			$v -> wq_mchid = $wq_mchid;
// 			if($v -> update()){
// 				$count ++;
// 			}
// 		}
// 		echo $count;
// 	}
	
// 	//获取定长的随机字符串
// 	private function getRandChar($length){
// 		$str = null;
// 		$strPol = "012356789";
// 		$strPolnoZero = "12356789";
// 		$max = strlen($strPol)-1;
// 		for($i=0;$i<$length;$i++){
// 			if ($i == 0){
// 				$str.=$strPolnoZero[rand(0,$max-1)];
// 			}else{
// 				$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
// 			}
// 		}
// 		return $str;
// 	}
	
// 	public function actionGetmerchant(){
// 		$agent_id_arr = array(57,73,74,75,76,77,81,83,92);
// 		$criteria = new CDbCriteria;
// 		$criteria->addInCondition('agent_id',$agent_id_arr);
// 		$merchant = Merchant::model() -> findAll($criteria);
// 		$arr_merchant_id = array();
// 		foreach ($merchant as $k => $v){
// 			$merchantInfo = Merchantinfo::model() -> findAll('merchant_id =:merchant_id',array(
// 					':merchant_id' => $v -> id
// 			));
// 			if(count($merchantInfo) > 1){
// 				$arr_merchant_id[$k] = $v -> id;
// 			}
// 		}	
// 		print_r($arr_merchant_id);
// 		echo count($arr_merchant_id);
// 	}
// 	public function actionCopyAppidAndAppsecret(){
// 		set_time_limit(0);
// 		ini_set('memory_limit', '1500M');
// 		$merchant = Merchant::model() -> findAll();
// 		$count = 0;
// 		foreach ($merchant as $k => $v){
// 			$v -> wechat_subscription_appsecret = $v -> wechat_appsecret;
// 			$v -> wechat_subscription_appid = $v -> wechat_appid;
// 			if($v -> update()){
// 				$count ++;
// 			}
// 		}
// 		echo $count;
// 	}
	
    /**
     * 生成二维码
     */
    public function actionCreateQrcode() {
    	//引入phpqrcode库文件
    	Yii::import('application.extensions.qrcode.*');
    	include('phpqrcode.php');
    	
    	// 二维码数据
    	$api = new WxpaySC();
    	$ret = $api->prePayUrl('201605061118');
    	$result = json_decode($ret, true);
    	if ($result['status'] != ERROR_NONE) {
    		exit('二维码生成失败');
    	}
    	$url = $result['url'];
//     	echo $url;exit;
    	
    	//输入二维码到浏览器
    	QRcode::png($url);
    }
    
    public function actionNativeNotify() {
    	$xml = file_get_contents("php://input");
    	//xml解析
    	libxml_disable_entity_loader(true);
    	$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    	
    	$appid = $arr['appid']; //appid
    	$mchid = $arr['mch_id']; //mchid
    	$product_id = $arr['product_id']; //商品id
    	$is_subscribe = $arr['is_subscribe']; //是否关注
    	$open_id = $arr['openid']; //用户标识
    	$sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //用户子标识
    	
    	if ($product_id != '201605061118') {
    		exit();
    	}
    	
    	//处理结果
    	$flag = false;
    	$msg = 'OK';
    	
    	//回调函数
    	$callback = function () use($product_id){
    		$array = array('flag' => false, 'msg' => '');
    		$wxpay = new WxpaySC();
    		$ret = $wxpay->qrCodePay1();
    		$result = json_decode($ret, true);
    		if ($result['status'] == ERROR_NONE) {
    			$prepay_id = $result['prepay_id'];
    			$array['flag'] = true;
    			$array['prepay_id'] = $prepay_id;
    		}
    		return $array;
    	};
    	
    	
    	$api = new WxpaySC();
    	$api->wxpayNativeNotify($product_id, $callback);
    }
    
    /**
     * 微信支付结果异步通知
     */
    public function actionPayNotify() {
        Yii::log('debug_wechat_demo','warning');
    	$xml = file_get_contents("php://input");
    	//xml解析
    	libxml_disable_entity_loader(true);
    	$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    	
    	$out_trade_no = $arr['out_trade_no']; //获取订单号
    	$result_code = $arr['result_code']; //业务结果码
    	$trade_no = $arr['transaction_id']; //微信支付订单号
    	$open_id = $arr['openid']; //用户标识
    	$sub_open_id = isset($arr['sub_openid']) ? $arr['sub_openid'] : ''; //用户子标识
    	$pay_time = isset($arr['time_end']) ? $arr['time_end'] : date('Y-m-d H:i:s');
    	Yii::log('debug_wechat_demo_'.$open_id,'warning');
    	$api = new WxpaySC();
    	$re = json_decode($api->wxpaySampleHandle($open_id));
    	Yii::log('debug_wechat_demo_'.$re -> errMsg,'warning');
    	
    	//会场通过扫二维码成功支付5元的用户，如果该用户已关注且未发送红包，则发红包给该用户
    	$userC = new UserC();
    	$re = json_decode($userC -> checkOpenid($open_id));
    	if($re -> status == ERROR_NONE){
    	    if($re -> data == 1){
    	        $packet = new Packet();
    	        //调用发红包接口
    	       $re = $packet -> wxpacket($open_id);
    	       if($re -> result_code == "SUCCESS"){
    	           $userC -> changeIfget($open_id);
    	       }
    	    }
    	}
    }

    //用户领券的code批处理
    public function actionSetUserCouponCode()
    {
        $user_coupon = UserCoupons::model()->findAll('LENGTH(code) > 12');
        foreach ($user_coupon as $v) {
            //创建优惠券核销码12位
            $code = $this->getRandChar(12);
            $usercode = UserCoupons::model()->find('code =:code', array(
                ':code' => $code
            ));
            while (!empty($usercode)) {
                $code = $this->getRandChar(12);
                $usercode = UserCoupons::model()->find('code =:code', array(
                    ':code' => $code
                ));
            }
            $v['code'] = $code;
            $v->save();
        }
        exit('成功');
    }

    //获取定长的随机字符串 首位不为零
    private function getRandChar($length)
    {
        $str = null;
        $strPol = "012356789";
        $strPolnoZero = "12356789";
        $max = strlen($strPol) - 1;

        for ($i = 0; $i < $length; $i++) {
            if ($i == 0) {
                $str .= $strPolnoZero[rand(0, $max - 1)];
            } else {
                $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
            }
        }
        return $str;
    }
}