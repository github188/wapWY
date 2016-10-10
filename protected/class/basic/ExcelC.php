<?php

include_once(dirname(__FILE__).'/../mainClass.php');
class ExcelC extends mainClass
{
    public function Dump()
    {
        set_time_limit(0);
        
        $data = array();
        $criteria = new CDbCriteria();
        $criteria ->addCondition('flag=:flag');
        $criteria -> params[':flag'] = FLAG_NO;
        $merchant = Merchant::model() -> findall($criteria);
        if($merchant)
        {
            foreach ($merchant as $key => $value) //查询商户
            {
                $sum = 0;//总额
                $avg = 0;//总佣金
                $criteria1 = new CDbCriteria();
                $criteria1 ->addCondition('flag=:flag AND merchant_id=:merchant_id');
                $criteria1 ->params[':flag'] = FLAG_NO;
                $criteria1 ->params[':merchant_id'] = $value['id'];
                $store = Store::model() -> findall($criteria1);//查询门店
                if($store)
                {
                    foreach ($store as $k => $v) 
                    {
                        $criteria2 = new CDbCriteria();
                        $criteria2 ->addCondition('flag=:flag and store_id=:store_id');
                        $criteria2 ->params[':flag'] = FLAG_NO;
                        $criteria2 ->params[':store_id'] = $v['id'];
                        $operator = Operator::model() -> findall($criteria2);//查询操作员
                        if($operator)
                        {
                            foreach ($operator as $n => $m) 
                            {
                                $criteria3 = new CDbCriteria();
                                $criteria3 ->addCondition('flag=:flag and operator_id=:operator_id and pay_status=:pay_status');
                                $criteria3 ->params[':flag'] = FLAG_NO;
                                $criteria3 ->params[':operator_id'] = $m['id'];
                                $criteria3 ->params[':pay_status']  = ORDER_STATUS_PAID;
                                $criteria3 ->addCondition('(pay_channel = :payc1 or pay_channel = :payc2)');
                                $criteria3 ->params[':payc1']  = 1;
                                $criteria3 ->params[':payc2']  = 2;
                                $criteria3->addBetweenCondition('pay_time','2015-09-01 00:00:00','2015-09-30 23:59:59');
                                $order = Order::model() -> findall($criteria3);//查询订单
                                
                                if($order)
                                {
                                    foreach ($order as $a => $b) 
                                    {
                                        $money = $b['online_paymoney'];
                                        $yongjin = round($money * 0.006,2);
                                        //查询退款
                                        $refund = RefundRecord::model()->findall('order_id=:order_id and flag=:flag and status=:status',array('order_id'=>$b['id'],'flag'=>FLAG_NO,':status'=>REFUND_STATUS_SUCCESS));
                                        $refundmoney = 0;
                                        $avgrefund   = 0;
                                        foreach ($refund as $c => $d) 
                                        {
                                            $refundmoney = $refundmoney + $d['refund_money'];
                                            $avgrefund   = $avgrefund + (round($d['refund_money'] * 0.006,2));
                                        }
                                        $sum = $sum + ($money - $refundmoney);//总额
                                        $avg = $avg + ($yongjin - $avgrefund);//佣金
                                    }
                                }
                            }
                        }
                    }  
                }
           
                $data[$key]['merchant_id'] = $value['id'];
                $data[$key]['merchant']    = $value['name'];                
                $data[$key]['money']       = $sum;
                $data[$key]['avg']         = $avg;
                $data[$key]['date'] = '9月1-9月30';
            }  
            $this->getExcel($data);
        }        
    }
    
        /**
	 * 获取excel
	 */
	public function getExcel($model)
	{
		include 'PHPExcel/Reader/Excel2007.php';
		include 'PHPExcel/Reader/Excel5.php';
		include 'PHPExcel/IOFactory.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','商户id')
		->setCellValue('B1','商户名称')
		->setCellValue('C1','总额')
		->setCellValue('D1','佣金')
		->setCellValue('E1','交易时间');
		
		//设置列宽
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->getColumnDimension('A')->setWidth(30);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(25);
		$objActSheet->getColumnDimension('E')->setWidth(30);

		//设置sheet名称
		$objActSheet -> setTitle('订单明细');
		
		//数据添加
		$i=2;
		foreach($model as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, $v['merchant_id'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i,$v['merchant'])
			->setCellValue('C'.$i,$v['money'])
			->setCellValue('D'.$i,$v['avg'])
			->setCellValue('E'.$i,$v['date']);
			$i++;
		}
		
		$filename = date('YmdHis');//定义文件名
		
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		//$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$this->outPut($filename);
		$objWriter->save("php://output");
	
	}
	
	/**
	 * 到浏览器  浏览器下载excel
	 */
	public function outPut($filename)
	{
		//header("Pragma: public");
		header("Expires: 0");
                header("Pragma:no-cache");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header("Content-Disposition:attachment;filename={$filename}.xls");
		header("Content-Transfer-Encoding:binary");
	}
        
        /**
         * 导出订单明细   
         */
        public function MianXiOrder()
        {
            $datas = array();
            $i = 1;
            $merchant = Merchant::model() -> findall('flag=:flag and id=:id',array(':flag'=>FLAG_NO,':id'=>41));
            if($merchant){
                foreach($merchant as $kk => $vv){
                    if($i%100==0){
                        usleep(100);
                    }
                    $criteria1 = new CDbCriteria();
                    $starttime = '2015-11-1 00:00:00';
                    $endtime   =  '2016-1-31 23:59:59';
                    //查找门店信息
                    $store = Store::model()->findAll('flag=:flag and merchant_id = :merchant_id', array(':merchant_id' => $vv['id'],':flag'=>FLAG_NO));
                    $arr = array();
                    if(!empty($store)) {
                        foreach($store as $v) {
                            $arr[] = $v->id;
                        }
                    }                    
                    $criteria1->addInCondition('store_id', $arr);
                    $criteria1->addBetweenCondition('pay_time', $starttime, $endtime);
                    $criteria1 ->addCondition('flag=:flag and pay_status=:pay_status');
                    $criteria1 -> addCondition('((((order_status=:order_status1) or order_status=:order_status2) or order_status=:order_status3))');
                    $criteria1 ->params[':flag'] = FLAG_NO;                                
                    $criteria1 ->params[':pay_status'] = ORDER_STATUS_PAID;
                    $criteria1 ->params[':order_status1'] = ORDER_STATUS_NORMAL;
                    $criteria1 ->params[':order_status2'] = ORDER_STATUS_REFUND;
                    $criteria1 ->params[':order_status3'] = ORDER_STATUS_PART_REFUND;                    
                    $order = Order::model()->findall($criteria1);                     
                    if($order){
                        foreach($order as $key => $value){  
                            $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                            $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name']; 
                            $datas[$i]['type']            = '交易';
                            $datas[$i]['createDate']      = $value->create_time;
                            $datas[$i]['successDate']     = $value->pay_time;
                            $datas[$i]['money']           = $value->order_paymoney;
                            $datas[$i]['stored_paymoney'] = $value->stored_paymoney;
                            $datas[$i]['online_paymoney'] = $value->online_paymoney;
                            $datas[$i]['unionpay_paymoney'] = $value->unionpay_paymoney;
                            $datas[$i]['cash_paymoney']   = $value->cash_paymoney;
                            $datas[$i]['orderno']         = '`'.$value['order_no'];
                            $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                            $i++;
                        }
                    }
                    $m = new CDbCriteria();
                    $m->addBetweenCondition('t.create_time', $starttime, $endtime);
                    $m->addCondition('t.status = :status and t.type = :type');
                    $m->addInCondition('store.store_id', $arr);
                    $m -> params[':status'] = '1'; 
                    $m -> params[':type'] = REFUND_TYPE_REFUND;
                    $refund_m = RefundRecord::model()->with('store')->findAll($m);
                    if($refund_m){
                        foreach ($refund_m as $key => $value) {                      
                            $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                            $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                                
                            $datas[$i]['type']            = '退款';
                            $datas[$i]['createDate']      = $value->create_time;
                            $datas[$i]['successDate']     = $value->refund_time;
                            $datas[$i]['money']           = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_STORED){
                                $datas[$i]['stored_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                            }else{
                                $datas[$i]['stored_paymoney'] = '';
                            }
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $value->store->pay_channel == ORDER_PAY_CHANNEL_ALIPAY_TM
                                || $value->store->pay_channel == ORDER_PAY_CHANNEL_ALIPAY || $value->store->pay_channel == ORDER_PAY_CHANNEL_WXPAY_SM
                                || $value->store->pay_channel == ORDER_PAY_CHANNEL_WXPAY_TM || $value->store->pay_channel == ORDER_PAY_CHANNEL_WXPAY){
                                $datas[$i]['online_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                            }else{
                                $datas[$i]['online_paymoney'] = '';
                            }
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_UNIONPAY){
                                $datas[$i]['unionpay_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                            }else{
                                $datas[$i]['unionpay_paymoney'] = '';
                            }
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_CASH){
                                $datas[$i]['cash_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                            }else{
                                $datas[$i]['cash_paymoney']   = '';
                            }
                            $datas[$i]['orderno']         = '`'.$value->store->order_no;
                            $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                            $i++;
                        }
                    }
                }
            }
            return $datas;
            //$this->orderExcel($datas);
        }
        
        /**
	 * 获取excel
	 */
	public function orderExcel($model)
	{
		include 'PHPExcel/Reader/Excel2007.php';
		include 'PHPExcel/Reader/Excel5.php';
		include 'PHPExcel/IOFactory.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','商户编号')
		->setCellValue('B1','商户名称')
		->setCellValue('C1','交易类型')
		->setCellValue('D1','创建时间')
		->setCellValue('E1','完成时间')
                ->setCellValue('F1','订单金额')
                ->setCellValue('G1','储值支付金额')
                ->setCellValue('H1','线上支付金额')
                ->setCellValue('I1','银联刷卡支付')
                ->setCellValue('J1','现金支付')
                ->setCellValue('K1','订单号')
                ->setCellValue('L1','代理商');
                
		
		//设置列宽
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->getColumnDimension('A')->setWidth(30);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(25);
		$objActSheet->getColumnDimension('E')->setWidth(30);
                $objActSheet->getColumnDimension('F')->setWidth(30);
                $objActSheet->getColumnDimension('G')->setWidth(30);
                $objActSheet->getColumnDimension('H')->setWidth(30);
                $objActSheet->getColumnDimension('I')->setWidth(30);
                $objActSheet->getColumnDimension('J')->setWidth(30);
                $objActSheet->getColumnDimension('K')->setWidth(30);
                $objActSheet->getColumnDimension('L')->setWidth(30);

		//设置sheet名称
		$objActSheet -> setTitle('订单明细');
		$filename = date('YmdHis');//定义文件名
		//数据添加
		$i=2;      
                
		foreach($model as $k=>$v){                    
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValueExplicit('A'.$i, $v['merchantnumber'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('B'.$i,$v['name'])
                    ->setCellValue('C'.$i,$v['type'])
                    ->setCellValue('D'.$i,$v['createDate'])
                    ->setCellValue('E'.$i,$v['successDate'])
                    ->setCellValue('F'.$i,$v['money'])
                    ->setCellValue('G'.$i,$v['stored_paymoney'])
                    ->setCellValue('H'.$i,$v['online_paymoney'])
                    ->setCellValue('I'.$i,$v['unionpay_paymoney'])
                    ->setCellValue('J'.$i,$v['cash_paymoney'])
                    ->setCellValue('K'.$i,$v['orderno'])
                    ->setCellValue('L'.$i,$v['agent']);                    
                    $i++;                        
		}    

		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		//$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$this->outPut($filename);
		$objWriter->save("php://output");
//                $finalFileName = Yii::app()->basePath.'/runtime/'.$filename.'.csv';
//                $objWriter->save($finalFileName);
	}
        
        /***
         * 支付宝，微信，银联订单明细
         */
        public function AlipayWechat()
        {
            $datas = array();
            $i = 1;
            $merchant = Merchant::model() -> findall('flag=:flag',array(':flag'=>FLAG_NO));
            if($merchant){
                foreach($merchant as $kk => $vv){                    
                    $criteria1 = new CDbCriteria();
                    $starttime = '2015-11-1 00:00:00';
                    $endtime   =  '2015-11-31 23:59:59';
                    //查找门店信息
                    $store = Store::model()->findAll('flag=:flag and merchant_id = :merchant_id', array(':merchant_id' => $vv['id'],':flag'=>FLAG_NO));
                    $arr = array();
                    if(!empty($store)) {
                        foreach($store as $v) {
                            $arr[] = $v->id;
                        }
                    }                    
                    $criteria1->addInCondition('store_id', $arr);
                    $criteria1->addBetweenCondition('pay_time', $starttime, $endtime);
                    $criteria1 ->addCondition('flag=:flag and pay_status=:pay_status');
                    $criteria1 -> addCondition('((((order_status=:order_status1) or order_status=:order_status2) or order_status=:order_status3))');
                    $criteria1 ->params[':flag'] = FLAG_NO;                                
                    $criteria1 ->params[':pay_status'] = ORDER_STATUS_PAID;
                    $criteria1 ->params[':order_status1'] = ORDER_STATUS_NORMAL;
                    $criteria1 ->params[':order_status2'] = ORDER_STATUS_REFUND;
                    $criteria1 ->params[':order_status3'] = ORDER_STATUS_PART_REFUND;                    
                    $order = Order::model()->findall($criteria1);                     
                    if($order){
                        foreach($order as $key => $value){  
                            if($value['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_SM || $value['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY_TM || $value['pay_channel'] == ORDER_PAY_CHANNEL_ALIPAY) {
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                       
                                $datas[$i]['type']            = '交易';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->pay_time;
                                $datas[$i]['money']           = $value->order_paymoney;
                                $datas[$i]['stored_paymoney'] = $value->stored_paymoney;
                                $datas[$i]['online_paymoney'] = $value->online_paymoney;
                                $datas[$i]['unionpay_paymoney'] = $value->unionpay_paymoney;
                                $datas[$i]['cash_paymoney']   = $value->cash_paymoney;
                                $datas[$i]['orderno']         = '`'.$value['order_no'];
                                $datas[$i]['channel']         = '支付宝';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            } 
                            if($value['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_SM || $value['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY_TM || $value['pay_channel'] == ORDER_PAY_CHANNEL_WXPAY) {
                                
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                                 
                                $datas[$i]['type']            = '交易';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->pay_time;
                                $datas[$i]['money']           = $value->order_paymoney;
                                $datas[$i]['stored_paymoney'] = $value->stored_paymoney;
                                $datas[$i]['online_paymoney'] = $value->online_paymoney;
                                $datas[$i]['unionpay_paymoney'] = $value->unionpay_paymoney;
                                $datas[$i]['cash_paymoney']   = $value->cash_paymoney;
                                $datas[$i]['orderno']         = '`'.$value['order_no'];
                                $datas[$i]['channel']         = '微信';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            } 
                            if($value['pay_channel'] == ORDER_PAY_CHANNEL_UNIONPAY){
                                
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                            
                                $datas[$i]['type']            = '交易';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->pay_time;
                                $datas[$i]['money']           = $value->order_paymoney;
                                $datas[$i]['stored_paymoney'] = $value->stored_paymoney;
                                $datas[$i]['online_paymoney'] = $value->online_paymoney;
                                $datas[$i]['unionpay_paymoney'] = $value->unionpay_paymoney;
                                $datas[$i]['cash_paymoney']   = $value->cash_paymoney;
                                $datas[$i]['orderno']         = '`'.$value['order_no'];
                                $datas[$i]['channel']         = '银联';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            }
                            if($value['pay_channel'] == ORDER_PAY_CHANNEL_CASH){
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                            
                                $datas[$i]['type']            = '交易';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->pay_time;
                                $datas[$i]['money']           = $value->order_paymoney;
                                $datas[$i]['stored_paymoney'] = $value->stored_paymoney;
                                $datas[$i]['online_paymoney'] = $value->online_paymoney;
                                $datas[$i]['unionpay_paymoney'] = $value->unionpay_paymoney;
                                $datas[$i]['cash_paymoney']   = $value->cash_paymoney;
                                $datas[$i]['orderno']         = '`'.$value['order_no'];
                                $datas[$i]['channel']         = '现金';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            }
                        }
                    }
                    $m = new CDbCriteria();
                    $m->addBetweenCondition('t.create_time', $starttime, $endtime);
                    $m->addCondition('t.status = :status and t.type = :type');
                    $m->addInCondition('store.store_id', $arr);
                    $m -> params[':status'] = '1'; 
                    $m -> params[':type'] = REFUND_TYPE_REFUND;
                    $refund_m = RefundRecord::model()->with('store')->findAll($m);
                    if($refund_m){
                        foreach ($refund_m as $key => $value) {
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_ALIPAY_SM || $value->store->pay_channel == ORDER_PAY_CHANNEL_ALIPAY_TM || $value->store->pay_channel == ORDER_PAY_CHANNEL_ALIPAY) {
                                
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                            
                                $datas[$i]['type']            = '退款';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->refund_time;
                                $datas[$i]['money']           = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['stored_paymoney'] = '';
                                $datas[$i]['online_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['unionpay_paymoney'] = '';
                                $datas[$i]['cash_paymoney']   = '';
                                $datas[$i]['orderno']         = '`'.$value->store->order_no;                                                         
                                $datas[$i]['channel']         = '支付宝';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            }
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_WXPAY_SM || $value->store->pay_channel == ORDER_PAY_CHANNEL_WXPAY_TM || $value->store->pay_channel == ORDER_PAY_CHANNEL_WXPAY) {                             
                                
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                              
                                $datas[$i]['type']            = '退款';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->refund_time;
                                $datas[$i]['money']           = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['stored_paymoney'] = '';
                                $datas[$i]['online_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['unionpay_paymoney'] = '';
                                $datas[$i]['cash_paymoney']   = '';
                                $datas[$i]['orderno']         = '`'.$value->store->order_no; 
                                $datas[$i]['channel']         = '微信';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            } 
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_UNIONPAY) { 
                               
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                               
                                $datas[$i]['type']            = '退款';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->refund_time;
                                $datas[$i]['money']           = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['stored_paymoney'] = '';
                                $datas[$i]['online_paymoney'] = '';
                                $datas[$i]['unionpay_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['cash_paymoney']   = '';
                                $datas[$i]['orderno']         = '`'.$value->store->order_no; 
                                $datas[$i]['channel']         = '银联';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            }
                            if($value->store->pay_channel == ORDER_PAY_CHANNEL_CASH){
                                $datas[$i]['merchantnumber']  = $vv['merchant_number'];
                                $datas[$i]['name']            = !empty($vv['name']) ? $vv['name'] : $vv['wx_name'];                               
                                $datas[$i]['type']            = '退款';
                                $datas[$i]['createDate']      = $value->create_time;
                                $datas[$i]['successDate']     = $value->refund_time;
                                $datas[$i]['money']           = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['stored_paymoney'] = '';
                                $datas[$i]['online_paymoney'] = '';
                                $datas[$i]['unionpay_paymoney'] = isset($value['refund_money']) ? '-'. $value['refund_money'] : '';
                                $datas[$i]['cash_paymoney']   = '';
                                $datas[$i]['orderno']         = '`'.$value->store->order_no; 
                                $datas[$i]['channel']         = '现金';
                                $datas[$i]['agent']           = Agent::model()->find('id=:id',array(':id'=>$vv['agent_id']))->name;
                                $i++;
                            }
                        }
                    }
                }
            }
            return $datas;
            //$this->alipayExcel($datas);
        }
        
        /**
	 * 获取excel
	 */
	public function alipayExcel($model)
	{
		include 'PHPExcel/Reader/Excel2007.php';
		include 'PHPExcel/Reader/Excel5.php';
		include 'PHPExcel/IOFactory.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','商户编号')
		->setCellValue('B1','商户名称')
		->setCellValue('C1','交易类型')
		->setCellValue('D1','创建时间')
		->setCellValue('E1','完成时间')
                ->setCellValue('F1','订单金额')
                ->setCellValue('G1','储值支付金额')
                ->setCellValue('H1','线上支付金额')
                ->setCellValue('I1','银联刷卡支付')
                ->setCellValue('J1','现金支付')
                ->setCellValue('K1','订单号')
                ->setCellValue('L1','支付渠道')
                ->setCellValue('M1','代理商号');
		
		//设置列宽
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->getColumnDimension('A')->setWidth(30);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('D')->setWidth(25);
		$objActSheet->getColumnDimension('E')->setWidth(30);
                $objActSheet->getColumnDimension('F')->setWidth(30);
                $objActSheet->getColumnDimension('G')->setWidth(30);
                $objActSheet->getColumnDimension('H')->setWidth(30);
                $objActSheet->getColumnDimension('I')->setWidth(30);
                $objActSheet->getColumnDimension('J')->setWidth(30);
                $objActSheet->getColumnDimension('K')->setWidth(30);
                $objActSheet->getColumnDimension('L')->setWidth(30);
                $objActSheet->getColumnDimension('M')->setWidth(30);

		//设置sheet名称
		$objActSheet -> setTitle('订单明细');
		
		//数据添加
		$i=2;
		foreach($model as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, $v['merchantnumber'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i,$v['name'])
			->setCellValue('C'.$i,$v['type'])
			->setCellValue('D'.$i,$v['createDate'])
			->setCellValue('E'.$i,$v['successDate'])
                        ->setCellValue('F'.$i,$v['money'])
                        ->setCellValue('G'.$i,$v['stored_paymoney'])
                        ->setCellValue('H'.$i,$v['online_paymoney'])
                        ->setCellValue('I'.$i,$v['unionpay_paymoney'])
                        ->setCellValue('J'.$i,$v['cash_paymoney'])
                        ->setCellValue('K'.$i,$v['orderno'])
                        ->setCellValue('L'.$i,$v['channel'])
                        ->setCellValue('M'.$i,$v['agent']);
			$i++;
		}
		
		$filename = date('YmdHis');//定义文件名
		
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		//$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$this->outPut($filename);
		$objWriter->save("php://output");	
	}
        
        /**
         * 导出商户下的商户和门店
         */
        public function merchantStore()
        {
            $i = 1;
            $data = array();
            $criteria = new CDbCriteria();
            $criteria ->addCondition('flag=:flag');
            $criteria -> params[':flag'] = FLAG_NO;
            $merchant = Merchant::model() -> findall($criteria);
            if($merchant){
                foreach ($merchant as $key => $value) {
                    $store = Store::model()->findall('flag=:flag and merchant_id=:merchant_id',array(':flag'=>FLAG_NO,':merchant_id'=>$value['id']));
                    if($store){
                        foreach ($store as $k => $v) {
                            if($k == 0){
                                $data[$i]['merchant_name'] = isset($value['name']) ? $value['name'] : $value['wq_m_name'];
                            } else {
                                $data[$i]['merchant_name'] = '';
                            }
                            $data[$i]['store_name'] = $v['name'];
                            $data[$i]['address'] = $v['address'];
                            $i++;
                        }
                    }
                }
            }
            $this->merchantExcel($data);
        }
        
        /**
	 * 获取excel
	 */
	public function merchantExcel($model)
	{
		include 'PHPExcel/Reader/Excel2007.php';
		include 'PHPExcel/Reader/Excel5.php';
		include 'PHPExcel/IOFactory.php';
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1','商户名称')
		->setCellValue('B1','门店名称')
		->setCellValue('C1','门店地址');
		
		//设置列宽
		$objActSheet = $objPHPExcel->getActiveSheet();
		$objActSheet->getColumnDimension('A')->setWidth(30);
		$objActSheet->getColumnDimension('B')->setWidth(20);
		$objActSheet->getColumnDimension('C')->setWidth(30);		

		//设置sheet名称
		$objActSheet -> setTitle('商户');
		
		//数据添加
		$i=2;
		foreach($model as $k=>$v){
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$i, $v['merchant_name'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('B'.$i,$v['store_name'])
			->setCellValue('C'.$i,$v['address'])
			;
			$i++;
		}
		
		$filename = date('YmdHis');//定义文件名
		
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		//$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$this->outPut($filename);
		$objWriter->save("php://output");	
	}
}

