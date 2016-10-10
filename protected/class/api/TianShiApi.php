<?php

include_once(dirname(__FILE__) . '/../mainClass.php');
class TianShiApi extends mainClass
{
    const URL      = 'http://dqh.sjdzp.com/Api/Seller/api.json';//接口地址
    const AUTOCODE = '0e57b361c9caf790c57748c8158d5984';//授权码（说明用，实际是32位字符串）
    const _PID     = '125490';//合作伙伴ID
    

    /**
     * 门票列表
     * @param type $page
     * @param type $size
     * @param type $g_relate
     * @param type $cate_id
     * @param type $zone
     * @param type $item_id
     * @param type $key_word
     */
    public function TicketList($page, $size, $g_relate, $cate_id, $zone, $item_id, $key_word) 
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'   => 'item_list',
            'format'   => 'xml',
            '_pid'     => $_pid,
            'page'     => $page,//列表页码，缺省获取第1页
            'size'     => $size,//每页获取数量，缺省每页获取发15条信息
            'g_relate' => $g_relate,//是否返回关联产品：0不返回(默认)，1只返回关联产品，2返回搜索的联票产品和关联产品。与item_id一起使用，item_id为联票时才生效
            'cate_id'  => $cate_id,//产品分类ID,缺省无，获取所有分类产品
            'zone'     => $zone,//产品地区ID,缺省无，不作条件
            'item_id'  => $item_id,//产品ID，用于获取确定的产品，多个用英文逗号分隔
            'key_word' => $key_word,//产品标题搜索关键字
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        echo $this -> http($url,$post_data);        
    }
    
    /**
     * 订单列表
     * @param type $page
     * @param type $size
     * @param type $item_id
     * @param type $begin
     * @param type $end
     * @param type $g_relate
     * @param type $orders_id
     */
    public function OrderList($page, $size, $item_id, $begin, $end, $g_relate, $orders_id)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'    => 'orders_list',
            'format'    => '',
            '_pid'      => $_pid,
            'page'      => $page,//列表页码，缺省获取第1页
            'size'      => $size,//每页获取数量，缺省每页获取发15条信息
            'item_id'   => $item_id,//产品ID，缺省不做条件
            'begin'     => $begin,//开始时间戳，与end连用，缺省30天前时间戳
            'end'       => $end,//结束时间戳，与begin连用，缺省当前时间戳
            'g_relate'  => $g_relate,//是否返回关联订单：0不返回(默认)，1返回关联订单。与orders_id一起使用，orders_id为联票订单时才生效
            'orders_id' => $orders_id,//订单ID，用于获取确定的订单，多个用英文逗号分隔        
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }
    
    /**
     * 下单接口
     * @param type $item_id
     * @param type $name
     * @param type $mobile
     * @param type $is_pay
     * @param type $orders_id
     * @param type $size
     * @param type $start_date
     * @param type $start_date_auto
     * @param type $price_type
     * @param type $remark
     * @param type $price
     * @param type $back_cash
     * @param type $id_number
     */
    public function CreateOrder($item_id, $name, $mobile, $is_pay, $orders_id, $size, $start_date, $start_date_auto, $price_type, $remark, $price, $back_cash, $id_number)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'item_orders',
            'format'          => 'json',
            '_pid'            => $_pid,
            'is_pay'          => $is_pay,//是否已付款，1：已付款 0：未付款
            'orders_id'       => $orders_id,//第三方订单ID，可避免网络不好时重复下单
            'item_id'         => $item_id,//必填 要购买的票ID
            'size'            => $size,//购买票数,缺省1
            'name'            => $name,//【必填】 购票人名称
            'mobile'          => $mobile,//必填 购票人手机号(成功后短信将发送门票码号到该手机号)
            'start_date'      => $start_date,//开始游玩时间，缺省当前时间  
            'start_date_auto' => $start_date_auto,//是否自动设置游玩时间，1:是；0:否 
            'price_type'      => $price_type, //价格类型；1成人，2儿童，缺省1
            'remark'          => $remark, //订单备注信息
            'price'           => $price, //产品单价（特殊系统需要，一般不要传），如果有则校验产品单价与本系统当前单价是否一致
            'back_cash'       => $back_cash,//返现金额（特殊系统需要，一般不要传），如果有则校验产品每份返现与本系统当前返现是否一致 
            'id_number'       => $id_number,//身份证号码，是否需要提供由产品决定 
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data);        
    }
    
    /**
     * 退票接口(如果对应的订单退票许可状态为“管理员审核退票”，则接口为“申请退票接口”)
     * @param type $orders_id
     * @param type $size
     */
    public function Refund($orders_id, $size)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'item_refund',
            'format'          => 'json',
            '_pid'            => $_pid,            
            'orders_id'       => $orders_id,//必填 要退票的订单号            
            'size'            => $size,//退票数,缺省退票所有未使用票数            
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }
    
    /**
     * 修改订单接口(重发接口)
     * @param type $orders_id
     * @param type $is_pay
     * @param type $sms_send
     * @param type $name
     * @param type $mobile
     * @param type $re_code
     * @param type $start_time
     * @param type $expire_time
     * @param type $id_number
     */
    public function ModifyOrder($orders_id, $is_pay, $sms_send, $name, $mobile, $re_code, $start_time, $expire_time, $id_number)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'item_orders_modify',
            'format'          => 'xml',
            '_pid'            => $_pid,            
            'orders_id'       => $orders_id,//必填 本系统订单ID或第三方请求下单接口时传的订单ID            
            'is_pay'          => $is_pay,//是否已付款，1：已付款；0：未付款(未付款的订单不可修改)
            'sms_send'        => $sms_send,//是否发送短信,0否，1是，2自动（是支付操作时"是",是修改信息时"否"）
            're_code'         => $re_code,//是否生成新的码号,0:否，1:是，2自动（是支付操作时"否",是修改信息时"是"）缺省0
            'start_time'      => $start_time,//有效期开始时间yyyy-mm-dd，缺省不修改
            'expire_time'     => $expire_time,//有效期结束时间yyyy-mm-dd，缺省不修改
            'id_number'       => $id_number,//身份证号码，缺省不修改
        );
        if(!empty($name)){
            $post_data['name'] = $name;//购票人名称，缺省不修改
        }
        if(!empty($mobile)){
            $post_data['mobile'] = $mobile;//购票人手机号，缺省不修改
        }
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }
    
    /**
     * 码号查询接口
     * @param type $code
     */
    public function OrdersQuery($code)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'orders_query',
            'format'          => 'json',
            '_pid'            => $_pid,            
            'code'            => $code,//必填  码号
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }
    
    /**
     * 推送通知  验证通知
     * @param type $supplier_id
     * @param type $code
     * @param type $amount
     * @param type $my_orders_id
     * @param type $another_orders_id
     */
    public function Validate($supplier_id, $code, $amount, $my_orders_id, $another_orders_id)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'validate',
            'format'          => 'json',
            '_pid'            => $_pid,            
            'supplier_id'     => $supplier_id,//必填  验证景区ID
            'code'            => $code,//必填 验证码号
            'amount'          => $amount,//必填 验证数量
            'my_orders_id'    => $my_orders_id,//必填 本平台订单ID
            'another_orders_id' => $another_orders_id,//第三方平台订单ID，下单时有提交orders_id时才会返回
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }
    
    /**
     * 退票审核通知
     * @param type $serial_no
     * @param type $my_orders_id
     * @param type $type
     * @param type $orders_id
     */
    public function RefundResult($serial_no, $my_orders_id, $type, $orders_id)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'refundResult',
            'format'          => 'json',
            '_pid'            => $_pid,            
            'serial_no'       => $serial_no,//必填  退票记录id（流水号），与调用退票接口返回记录id
            'my_orders_id'    => $my_orders_id,//必填  本平台订单ID
            'type'            => $type,//必填  审核结果： 3退票成功，4退票不通过
            'orders_id'       => $orders_id,//第三方平台订单ID，下单时有提交orders_id时才会返回
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }
    
    /**
     * 出票成功通知
     * @param type $out_code
     * @param type $out_orders_id
     * @param type $orders_id
     */
    public function Send($out_code, $out_orders_id, $orders_id)
    {
        $url      = self::URL; //接口地址
        $autoCode = self::AUTOCODE; //授权码（说明用，实际是32位字符串）
        $_pid     = self::_PID; //合作伙伴ID
        $post_data = array(            
            'method'          => 'send',
            'format'          => 'json',
            '_pid'            => $_pid,            
            'out_code'        => $out_code,//必填  本平台订单码号
            'out_orders_id'   => $out_orders_id,//必填  本平台订单ID
            'orders_id'       => $orders_id,//第三方平台订单ID，下单时有提交orders_id时才会返回
        );
        ksort($post_data);
        //平台组合数据，进行签名计算
        $query = $autoCode;
        foreach ($post_data as $key => $value) {
            $query .= "&" . $key . "=" . $value;
        }
        $query .= "&" . $autoCode;
        $_sign = md5($query);
        $post_data['_sig'] = $_sign; //把签名加入到数据中
        return $this -> http($url,$post_data); 
    }

    /**
     * 自动提交表单方法
     */
    private function create_html($arrayData, $actionUrl){
        $html = '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/></head>'.
            '<body onload="javascript:document.pay_form.submit();">'.
            '<form id="pay_form" name="pay_form" action="'.$actionUrl.'" method="post">';
        foreach($arrayData as $k => $v){
            $html.='<input type="hidden" name="'.$k.'" id="'.$k.'" value="'.$v.'" /><br/>';
        }
        $html.= '</form></body></html>';
        return $html;
    }
    
    /**
     * 模拟提交数据函数
     * @param type $url
     * @param type $post_data
     */
    function Http($url,$post_data){ //       
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_POST, 1);  
        curl_setopt($ch, CURLOPT_URL,$url);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
        ob_start();  
        curl_exec($ch);  
        $result = ob_get_contents() ;  
        ob_end_clean();
        return $result;   
    }
}

