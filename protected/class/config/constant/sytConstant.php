<?php
/*	create by gulei
 * 收银台常量表
* 规则：
* 1、常量必须加注释，并且需要标明添加者和添加日期还有用处
* 2、命名规范带上同意前缀以作区分
* 3、需要和管理员报备自己添加的常量
* */

/********************订单*************************/
//订单支付通道 无通道，支付宝1.0，支付宝2.0 by gulei
define('ORDER_PAY_PASSAGEWAY_NULL', '1');//无通道
define('ORDER_PAY_PASSAGEWAY_ALIPAY1', '2');//支付宝1.0
define('ORDER_PAY_PASSAGEWAY_ALIPAY2', '3');//支付宝2.0
define('ORDER_PAY_PASSAGEWAY_WECHAT1', '4');//微信普通商户
define('ORDER_PAY_PASSAGEWAY_WECHAT2', '5');//微信特约商户

$GLOBALS['__ORDER_PAY_PASSAGEWAY'] = array(
    ORDER_PAY_PASSAGEWAY_NULL => '非支付宝',
    ORDER_PAY_PASSAGEWAY_ALIPAY1 => '支付宝1.0',
    ORDER_PAY_PASSAGEWAY_ALIPAY2 => '支付宝2.0',
    ORDER_PAY_PASSAGEWAY_WECHAT1 => '微信普通商户',
    ORDER_PAY_PASSAGEWAY_WECHAT2 => '微信特约商户'
    );
    
//是否获得积分
define('IF_HAS_POINTS_YES','1');//是
define('IF_HAS_POINTS_NO','2');//否



//订单所得佣金比率 0%，0.3%，0.6%
define('ORDER_COMMISSION_RATIO_NULL', '0');//0佣金
define('ORDER_COMMISSION_RATIO_ALIPAY1', '0.006');//支付宝1.0,2016-3-1之前所得佣金
define('ORDER_COMMISSION_RATIO_ALIPAY2', '0.003');//支付宝2.0,门店同步创建成功,且门店类目是符合佣金标准的

/**************************交易消息模板***********************************/
//操作员是否开启接受交易消息模板
define('OPERATOR_IF_ACCEPT_TEMPLATE_ACCEPT', '1');//接收
define('OPERATOR_IF_ACCEPT_TEMPLATE_NO_ACCEPT', '2');//不接收

/***************************积分*************************************/














