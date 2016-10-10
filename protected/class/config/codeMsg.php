<?php


//类方法错误信息
define('ERROR_NONE', '1000');//成功
define('ERROR_PARAMETER_MISS', '1002');//参数不全
define('ERROR_PARAMETER_FORMAT', '1003');//参数格式不正确
define('ERROR_SAVE_FAIL', '1004');//数据保存失败
define('ERROR_NO_DATA', '1005');//无此数据
define('ERROR_DUPLICATE_DATA', '1006');//重复数据
define('ERROR_EXCEPTION', '1007');//异常错误
define('ERROR_REQUEST_FAIL', '1008');//请求结果返回错误

//优惠券错误码
define('ERROR_YHQ_INVALID_DATA', '2001');
define('ERROR_YHQ_INVALID_STORE', '2002');
define('ERROR_YHQ_INVALID_DATE', '2003');
define('ERROR_YHQ_INVALID_MONEY', '2004');
define('ERROR_YHQ_INVALID_TYPE', '2005');
define('ERROR_YHQ_NO_USER_DISCOUNT', '2006');
define('ERROR_YHQ_INVALID_NUM', '2007');
define('ERROR_YHQ_NO_REDS', '2008');
define('ERROR_YHQ_NO_COUPONS', '2009');
define('ERROR_YHQ_HAS_RED_ONLY', '2010');
define('ERROR_YHQ_NO_DISCOUNTS', '2011');
define('ERROR_YHQ_INVALID_BELONG', '2012');
define('ERROR_YHQ_INVALID_CHANNEL', '2013');

//错误码对应提示信息
$GLOBALS['MSG_YHQ'] = array(
		ERROR_YHQ_INVALID_DATA => '存在无效的优惠券',
		ERROR_YHQ_INVALID_STORE => '该门店不能使用此券',
		ERROR_YHQ_INVALID_DATE => '该券不在有效期内',
		ERROR_YHQ_INVALID_MONEY => '该订单金额不符合使用条件',
		ERROR_YHQ_INVALID_TYPE => '优惠券类型有误',
		ERROR_YHQ_NO_USER_DISCOUNT => '该券无法与会员折扣同时使用',
		ERROR_YHQ_INVALID_NUM => '该券超出单个订单使用数量',
		ERROR_YHQ_NO_REDS => '该红包无法与其他红包同时使用',
		ERROR_YHQ_NO_COUPONS => '无法与其他优惠券同时使用',
		ERROR_YHQ_HAS_RED_ONLY => '存在无法与优惠券同时使用的红包',
		ERROR_YHQ_NO_DISCOUNTS => '无法与多张折扣券同时使用',
		ERROR_YHQ_INVALID_BELONG => '错误的优惠券拥有者',
		ERROR_YHQ_INVALID_CHANNEL => '无效的核销渠道',
);

/****条码支付下单业务响应码(result_code)*****/
define('ALIPAY_ORDER_FAIL', 'ORDER_FAIL');//下单失败
define('ALIPAY_ORDER_SUCCESS_PAY_SUCCESS', 'ORDER_SUCCESS_PAY_SUCCESS');//下单成功并且支付成功
define('ALIPAY_ORDER_SUCCESS_PAY_FAIL', 'ORDER_SUCCESS_PAY_FAIL');//下单成功支付失败
define('ALIPAY_ORDER_SUCCESS_PAY_INPROCESS', 'ORDER_SUCCESS_PAY_INPROCESS');//下单成功支付处理中
define('ALIPAY_UNKNOWN', 'UNKNOWN');//处理结果未知

/****退款、扫码支付预下单响应码****/
define('_UNKNOWN', 'UNKNOWN');//结果未知
define('_SUCCESS', 'SUCCESS');//成功
define('_FAIL', 'FAIL');//失败

/****支付宝2.0接口结果码****/
define('ALIPAY_V2_CODE_SUCCESS', '10000');//业务处理成功
define('ALIPAY_V2_CODE_INPROCESS', '10003');//业务处理中
define('ALIPAY_V2_CODE_FAIL', '40004');//业务处理失败
define('ALIPAY_V2_CODE_UNKNOWN', '20000');//业务出现未知错误或者系统异常

/******查询交易状态*******/
define('SEARCH_TRADE_SUCCESS', 'TRADE_SUCCESS');//交易成功，且可对该交易做操作，如：多级分润、退款等
define('SEARCH_WAIT_BUYER_PAY', 'WAIT_BUYER_PAY');//交易创建，等待买家付款
define('SEARCH_TRADE_FINISHED', 'TRADE_FINISHED');//交易成功且结束，即不可再做任何操作
define('SEARCH_TRADE_CLOSED', 'TRADE_CLOSED');//在指定时间段内未支付时关闭的交易,在交易完成全额退款成功时关闭的交易

/****业务错误码(detail_error_code)*****/
define('ALIPAY_INVALID_PARAMETER', 'INVALID_PARAMETER');//参数无效
define('ALIPAY_TRADE_ROLE_ERROR', 'TRADE_ROLE_ERROR');//没有该笔交易的退款或撤销权限
define('ALIPAY_DISCORDANT_REPEAT_REQUEST', 'DISCORDANT_REPEAT_REQUEST');//同一笔退款或撤销单号金额不一致
define('ALIPAY_TRADE_HAS_CLOSE', 'TRADE_HAS_CLOSE');//交易已经关闭
define('ALIPAY_REASON_TRADE_BEEN_FREEZEN', 'REASON_TRADE_BEEN_FREEZEN');//交易已经被冻结
define('ALIPAY_BUYER_ERROR', 'BUYER_ERROR');//买家不存在
define('ALIPAY_SELLER_ERROR', 'SELLER_ERROR');//卖家不存在
define('ALIPAY_TRADE_NOT_EXIST', 'TRADE_NOT_EXIST');//交易不存在
define('ALIPAY_TRADE_STATUS_ERROR', 'TRADE_STATUS_ERROR');//交易状态不合法
define('ALIPAY_TRADE_HAS_FINISHED', 'TRADE_HAS_FINISHED');//交易已结束
define('ALIPAY_REFUND_AMT_NOT_EQUAL_TOTAL', 'REFUND_AMT_NOT_EQUAL_TOTAL');//撤销或退款金额与订单金额不一致
define('ALIPAY_SELLER_BALANCE_NOT_ENOUGH', 'SELLER_BALANCE_NOT_ENOUGH');//卖家余额不足

/****接口错误码****/
define('API_ERROR_CODE_LOGIN_FAIL', 'LOGIN_FAIL'); //登录失败
define('API_ERROR_CODE_NO_TOKEN', 'NO_TOKEN'); //获取token失败
define('API_ERROR_CODE_UNEXPECTED_ERROR', 'UNEXPECTED_ERROR'); //系统内部异常错误
define('API_ERROR_CODE_LACK_PARAMS', 'LACK_PARAMS'); //缺少必要参数
define('API_ERROR_CODE_INVALID_TOKEN', 'INVALID_TOKEN'); //无效的token
define('API_ERROR_CODE_INVALID_SIGN', 'INVALID_SIGN'); //无效的签名
define('API_ERROR_CODE_NO_STORE', 'NO_STORE'); //无相关门店信息
define('API_ERROR_CODE_NO_USER', 'NO_USER'); //无相关会员信息
define('API_ERROR_CODE_NO_ORDER', 'NO_ORDER'); //无相关订单信息
define('API_ERROR_CODE_ORDER_CREATE_FAIL', 'ORDER_CREATE_FAIL'); //创建订单失败
define('API_ERROR_CODE_THIRD_PARTY_ERROR', 'THIRD_PARTY_ERROR'); //第三方返回错误
define('API_ERROR_CODE_PASSWORD_ERROR', 'PASSWORD_ERROR'); //密码错误
define('API_ERROR_CODE_MESSAGE_ERROR', 'MESSAGE_ERROR'); //验证码错误
define('API_ERROR_CODE_CHANNEL_ERROR', 'CHANNEL_ERROR'); //渠道错误


/*************************************************新错误码 create by gulei 2016-1-15******************************************************************/
/***********状态码*****************/
define('STATUS_SUCCESS', '10000');//调用成功
define('STATUS_ERROR', '10001');//调用失败

//玩券商户管理-错误码
define('ERROR_MSG_NONE', '10002');//无错误
define('ERROR_MSG_NOT_FOUND_MERCHANT', '10003');//未找到该商户
define('ERROR_MSG_UPDATE_MERCHANT_VERIFY_STATUS_PASS_FAIL', '10004');//将商户审核状态改为审核通过操作失败





