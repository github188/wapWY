<?php
/*
 * 时间：2015-6-24
 * 创建人：王乃晓
 * */
include_once(dirname(__FILE__).'/../mainClass.php');
class DuanXinC extends mainClass
{
	public $page = null;
    //短信充值
    /**
     *   
     */
    public function DuanXinCart()
    {
        
    }
    
    //短信条数
    /**
     * $merchantId 商户id
     */
    public function DuanXinNum($merchantId)
    {
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null');  
        $flag = 0;
        //验证商名id
        if(!isset($merchantId) || empty($merchantId))
        {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;            
        } 
        if($flag == 0)
        {
            $merchant = Merchant::model()->find('id=:id and flag=:flag',array(':id'=>$merchantId,':flag' => FLAG_NO));        
            if(!empty($merchant))
            {
                $num = isset($merchant -> msg_num) ? $merchant -> msg_num : '0';
                $result['status']    = ERROR_NONE; 
                $result['data']      = $num;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }  

    //短信购买
    /**
     * $merchantId 商户id
     * $messageNum 购买短信条数
     * $price 短信价格
     * $money 支付金额
     * $num 数量
     */
    public function DuanXinPay($merchantId,$messageNum,$price,$money,$num)
    {    
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag = 0;
        //验证商名id
        if(!isset($merchantId) || empty($merchantId)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;           
        }
        //验证购买短信条数
        if(!isset($messageNum) || empty($messageNum)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数messageNum缺失';
            $flag = 1;           
        }
        //验证短信价格
        if(!isset($price) || empty($price)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数price缺失';
            $flag = 1;            
        }
        //验证短信价格
        if(!isset($money) || empty($money)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数money缺失';
            $flag = 1;          
        }
        //验证短信价格
        if(!isset($num) || empty($num)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数num缺失';
            $flag = 1;            
        }

        if($flag == 0)
        {
            $order = new MessageOrder();
            $order->order_no    = $this->getOrderNo();
            $order->merchant_id = $merchantId;
            $order->message_num = $messageNum;
            $order->price       = $price;
            $order->pay_status  = ORDER_STATUS_UNPAID;
            $order->pay_channel = ORDER_PAY_CHANNEL_ALIPAY;
            $order->pay_money   = $money;
            $order->create_time = new CDbExpression('now()');
            if($order -> save()){
                $result['status']   = ERROR_NONE;
                $result['data']     = $order->order_no;                
            }else{
                //充值失败
                $result['status'] = ERROR_SAVE_FAIL;            
                $result['errMsg'] = '充值失败';               
            }  
        }
        return json_encode($result);
    }
    
    //充值记录
    /**
     * $merchantId 商户id
     */
    public function DuanXinList($merchantId)
    {     
        //返回结果
        $result = array('status'=>1,'errMsg'=>'null','data'=>'null');
        $flag = 0;
         //验证商户id
        if(!isset($merchantId) || empty($merchantId))
        {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数merchantId缺失';
            $flag = 1;            
        }
        if($flag == 0)
        {
            $criteria = new CDbCriteria();
            $criteria->order = 'id desc';
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchantId;
            
            $pages = new CPagination(MessageOrder::model()->count($criteria));
            $pages->pageSize = Yii::app() -> params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;
            $messageOrder = MessageOrder::model()->findall($criteria);
            $data = array();
            if(!empty($messageOrder))
            {
                foreach ($messageOrder as $k => $v) 
                {
                    if($v['price'] == $GLOBALS['__DXTC']['1']['sub']['unit_price']){
                        $productName = $GLOBALS['__DXTC']['1']['sub']['num'];
                    }
                    if($v['price'] == $GLOBALS['__DXTC']['2']['sub']['unit_price']){
                        $productName = $GLOBALS['__DXTC']['2']['sub']['num'];
                    }
                    if($v['price'] == $GLOBALS['__DXTC']['3']['sub']['unit_price']){
                        $productName = $GLOBALS['__DXTC']['3']['sub']['num'];
                    }
                    $data['list'][$k]['product_name'] = $productName;
                    $data['list'][$k]['message_num']  = $v['message_num'];
                    $data['list'][$k]['pay_money']    = $v['pay_money'];
                    $data['list'][$k]['pay_time']     = $v['pay_time'];
                }
                $result['status'] = ERROR_NONE;
                $result['data']   = $data;
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        }
        return json_encode($result);
    }  

    

    /**
     * 生成订单编号
     */
    private function getOrderNo() {
    	$Code = 'DX'.date('Ymd').mt_rand(0001, 9999);
    	$ModelCode = MessageOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
    	if(!empty($ModelCode)) {
    		while($Code == $ModelCode->order_no) {
    			$Code = 'DX'.date('Ymd').mt_rand(0001, 9999);
    			$ModelCode = MessageOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
    		}
    	}
    	return $Code;
    }
    
}

