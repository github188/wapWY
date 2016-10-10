<?php

include_once(dirname(__FILE__) . '/../mainClass.php');
class ZhiYouBaoApi extends mainClass
{
    /**
     * 下订单
     * @param type $online_paymoney
     * @param type $out_trade_no
     * @param type $name
     * @param type $mobile
     * @param type $social_security_number
     */
    public function CreateOrder($online_paymoney,$out_trade_no,$name,$mobile,$social_security_number,$num,$item_id,$txtBirthday)
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>SEND_CODE_REQ</transactionName>"//固定值
                . "<header>"
                . "<application>SendCode</application>"//固定值
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>sdzfxnbdqhwx</corpCode>"//企业码
                . "<userName>nbdqhwx</userName>"//用户名
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<certificateNo>".$social_security_number."</certificateNo>"//身份证号
                . "<linkName>".$name."</linkName>"//联系人必填
                . "<linkMobile>".$mobile."</linkMobile>"//必填
                . "<orderCode>".$out_trade_no."</orderCode>"//你们的订单编码（或别的），要求唯一，我回调你们通知检票完了的标识
                . "<orderPrice>".$online_paymoney."</orderPrice>"//订单总价格
                . "<src>weixin</src>"//微信对接必填
                . "<groupNo></groupNo>"//团号
                . "<payMethod></payMethod>"//支付方式值spot现场支付vm备佣金，zyb智游宝支付
                . "<ticketOrders>"
                . "<ticketOrder>"
                . "<orderCode>".$out_trade_no."</orderCode>"//必填 你们的子订单编码
                . "<price>".$online_paymoney."</price>"//票价，必填，线下要统计的
                . "<quantity>".$num."</quantity>"//必填票数量
                . "<totalPrice>".$online_paymoney."</totalPrice>"//必填子订单总价
                . "<occDate>".$txtBirthday."</occDate>"//必填日期（游玩日期）
                . "<goodsCode>".$item_id."</goodsCode>"//必填商品编码，同票型编码
                . "<goodsName>智游宝测试票</goodsName>"//商品名称 
                . "<remark>智游宝测试票</remark>"//备注
                . "</ticketOrder></ticketOrders></order></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'58ACF5F807F2F2BA067540744E72042D');        

        $action = 'http://boss.zhiyoubao.com/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign); 
    }    
        
    /**
     * 查询检票情况
     */
    public function Search()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>CHECK_STATUS_QUERY_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order><orderCode>2015071348486775</orderCode></order>"
                . "</orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign); 
    }
    
    /**
     * 取消订单
     * @param type $order_no
     */
    public function Cancel($order_no)
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>SEND_CODE_CANCEL_NEW_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>".$order_no."</orderCode>"
                . "</order>"
                . "</orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign); 
    }

    /**
     * 发码图片查询
     */
    public function SendCodeImg()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>SEND_CODE_IMG_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>21</orderCode>"
                . "</order>"
                . "</orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }
    
    /**
     * 发短信
     */
    public function Sms($out_trade_no)
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>SEND_SM_REQ</transactionName>"
                . "<header><application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"//分销商用户名
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>".$out_trade_no."</orderCode>"//你们的主订单编码
                . "<tplCode></tplCode>"//模板编号（不填默认模板）
                . "</order>"
                . "</orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }

    /**
     * 发彩信
     */
    public function Mms()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>SEND_MMS_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>21</orderCode>"
                . "<tplCode>20130914000000002</tplCode>"
                . "</order></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }
    
    /**
     * 部分退票
     * @param type $order_no
     * @param type $num
     * @param type $refund_order_no
     */
    public function PartRefund($order_no,$num,$refund_order_no)
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>RETURN_TICKET_NUM_NEW_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>sdzfxnbdqhwx</corpCode>"//企业码
                . "<userName>nbdqhwx</userName>"//用户名
                . "</identityInfo>"
                . "<orderRequest>"
                . "<returnTicket>"
                . "<orderCode>".$order_no."</orderCode>"//子订单号
                . "<returnNum>".$num."</returnNum>"//退票数量
                . "<thirdReturnCode>".$refund_order_no."</thirdReturnCode>"//第三方退单号
                . "</returnTicket></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'58ACF5F807F2F2BA067540744E72042D');        

        $action = 'http://boss.zhiyoubao.com/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }
    
    /**
     * 订单查询
     */
    public function SearchOrder()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>QUERY_ORDER_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo><orderRequest>"
                . "<order>"
                . "<orderCode>WS20140828002</orderCode>"//你们的主订单编码
                . "</order>"
                . "</orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }

    /**
     * 自定义短信
     */
    public function SmReq()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>SM_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d H:i:s')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<sm>"
                . "<tel>18657197553</tel>"//手机号
                . "<msg>test</msg>"//内容
                . "</sm></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }

    /**
     * 获取订单检票信息
     */
    public function SubOrder()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>QUERY_SUB_ORDER_CHECK_RECORD_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d H:i:s')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>201410220003</orderCode>"
                . "</order></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }
    
    /**
     * 到付单取消
     */
    public function CancelSpotOrder()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>CANCEL_SPOT_ORDER_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d H:i:s')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>ZYB2015011400002</orderCode>"
                . "</order></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }
    
    /**
     * 退票情况查询
     */
    public function CancelRetreat()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>QUERY_RETREAT_STATUS_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d H:i:s')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<retreatBatchNo>bbd9971d98f246218ec8bbfe0217ffcc</retreatBatchNo>"
                . "</order></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
    }
    
    /**
     * 获取二维码链接
     */
    public function QrImgUrl()
    {
        $xmlMsg = "<PWBRequest>"
                . "<transactionName>QUERY_IMG_URL_REQ</transactionName>"
                . "<header>"
                . "<application>SendCode</application>"
                . "<requestTime>".date('Y-m-d H:i:s')."</requestTime>"
                . "</header>"
                . "<identityInfo>"
                . "<corpCode>TESTFX</corpCode>"
                . "<userName>admin</userName>"
                . "</identityInfo>"
                . "<orderRequest>"
                . "<order>"
                . "<orderCode>201203280000000760</orderCode>"
                . "</order></orderRequest></PWBRequest>";
        //echo $xmlMsg;die;
        $s = MD5("xmlMsg=" .$xmlMsg.'TESTFX');        

        $action = 'http://mlx.sendinfo.com.cn/boss/service/code.htm';
        $sign = array(
            'sign' => $s,
            'xmlMsg' => $xmlMsg,
        );
        echo $this -> http($action,$sign);
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

