<?php
/*************公共**************/

//是否是默认地址
define('IF_DEFAULT_NO', '1');//非默认
define('IF_DEFAULT_YES', '2');//默认

//城市级别
define('CITY_LEVEL_PROVINCE', '1');//省
define('CITY_LEVEL_CITY', '2');//市
define('CITY_LEVEL_AREA', '3');//区

/*************用户**************/
//性别
define('SEX_MALE', '1'); //男性
define('SEX_FEMALE', '2'); //女性
$GLOBALS['__SEX'] = array(
		SEX_MALE => '男',
		SEX_FEMALE => '女'
);
$GLOBALS['__BOOKSEX'] = array(
		SEX_MALE => '先生',
		SEX_FEMALE => '女士'
);

//婚姻状况
define('MARITAL_STATUS_UNMARRIED', '1');//未婚
define('MARITAL_STATUS_MARRIED', '2');//已婚
$GLOBALS['__MARITAL_STATUS'] = array(
		MARITAL_STATUS_UNMARRIED => '未婚',
		MARITAL_STATUS_MARRIED => '已婚'
);

//会员分组
define('USER_GROUP_ALL', 'ALL');//全部分组
define('USER_GROUP_DEFAULT', 'DEFAULT');//未分组

//会员等级
define('USER_GROUP_GRADE', 'GRADE');//会员等级分组

//来源
define('USER_FROM_WECHAT', '1');//微信
define('USER_FROM_ALIPAY', '2');//支付宝
define('USER_FROM_WAP', '3');//wap
define('USER_FROM_OTHER', '4');//其他
$GLOBALS['__USER_FROM'] = array(
		USER_FROM_WECHAT => '微信',
		USER_FROM_ALIPAY => '支付宝',
		USER_FROM_WAP => 'WAP',
		USER_FROM_OTHER => '其他'
);

//会员等级是否受积分限制
define('POINTS_LIMIT', '1');//受限制
define('POINTS_LIMIT_NO', '2');//不受限制


//是否默认会员等级
define('USER_GRADE_DEFAULT_NO', '1');//不是默认会员等级
define('USER_GRADE_DEFAULT_YES', '2');//是默认会员等级

//是否只显示会员卡样式
define('IF_HIDEWORD_NO', '1');
define('IF_HIDEWORD_YES', '2');

/**************合作商**************/
//新合作商审核状态
define('AGENT_AUDIT_STATUS_WAIT', '1');//待审核
define('AGENT_AUDIT_STATUS_PASS', '2');//审核通过
define('AGENT_AUDIT_STATUS_OPEN', '3');//已开通
define('AGENT_AUDIT_STATUS_REJECT', '4');//驳回
$GLOBALS['__AGENT_AUDIT_STATUS'] = array(
		AGENT_AUDIT_STATUS_WAIT => '待审核',
		AGENT_AUDIT_STATUS_PASS => '审核通过',
		AGENT_AUDIT_STATUS_OPEN => '已开通',
		AGENT_AUDIT_STATUS_REJECT => '驳回'
);

//合作商类型
define('AGENT_TYPE_OLD', '1');//旧的
define('AGENT_TYPE_NEW', '2');//新的（下级合作商）

//是否开启推荐合作商功能 1关闭 2开启
define('IF_RECOMMEND_CLOSE', '1'); //关闭
define('IF_RECOMMEND_OPEN', '2'); //开启

//代理商角色
define('AGENT_ROLE_AGENT', '1'); // 1代理商
define('AGENT_ROLE_CHILD', '2'); // 2子账号

 
//子账号功能开启和关闭状态
define('SUBACCOUNT_CLOSE', '1');//关闭
define('SUBACCOUNT_OPEN', '2');//开启
$GLOBALS['SUBACCOUNT'] = array(
    SUBACCOUNT_CLOSE => '关闭',
    SUBACCOUNT_OPEN => '开启',
);
//合作商状态
define('AGENT_STATUS_NORMAL', '1');//正常
define('AGENT_STATUS_LOCK', '2');//锁定
$GLOBALS['__AGENT_STATUS'] = array(
		AGENT_STATUS_NORMAL => '正常',
		AGENT_STATUS_LOCK => '锁定'
);

//合作商权限
define('AGENT_RULE_ADMIN', '1');//合作商总账号
define('AGENT_RULE_SUBACCOUNT', '2');//合作商子账号

//管家订单支付状态
define('GJORDER_PAY_STATUS_NUPAID', '1');//待付款
define('GJORDER_PAY_STATUS_PAID', '2');//已付款

//管家订单支付状态
define('FX_PAY_STATUS_NUPAID', '1');//待付款
define('FX_PAY_STATUS_PAID', '2');//已付款

//管家订单状态
define('GJORDER_STATUS_NORMAL', '1');//正常
define('GJORDER_STATUS_REFUND', '2');//已退款
define('GJORDER_STATUS_NUUSE', '3');//未使用
define('GJORDER_STATUS_USED', '4');//已使用


//玩券管家订单类型
define('GJ_ORDER_TYPE_XZ', '1');//新增订单
define('GJ_ORDER_TYPE_SJ', '2');//升级订单
$GLOBALS['__GJ_ORDER_TYPE'] = array(
		GJ_ORDER_TYPE_XZ => '新增订单',
		GJ_ORDER_TYPE_SJ => '升级订单'
);

//玩券管家试用天数
define('TRYOUT_TIME_LIMIT', '90');

//是否试用
define('IF_TRYOUT_NO', '1');//非试用
define('IF_TRYOUT_YES', '2');//试用

//合作商购买玩券管家折扣数
define('AGENT_DISCOUNT', '0.3');//年后改为0.45
define('NEW_AGENT_DISCOUNT', '0.4');
define('UMS_DISCOUNT', '1');//银联商务折扣数

//银联商户服务商账号id
define('UMS_AGENT_ID', 101);

//合作商分润
define('SUBAGENT_COMMISSION', '0.06');//下级分销商分佣率
define('SUB_SUBAGENT_COMMISSION', '0.04');//下下级分销商分佣率

/*************商户**************/
//会员储值功能开启状态
define('MEMBERSHIP_STORED_STATUS_CLOSE', '1');//关闭
define('MEMBERSHIP_STORED_STATUS_OPEN', '2');//开启
//商户状态
define('MERCHANT_STATUS_NORMAL', '1');//正常
define('MERCHANT_STATUS_LOCK', '2');//锁定
//预定功能开启状态
define('MERCHANT_BOOK_CLOSE', '1');//关闭
define('MERCHANT_BOOK_OPEN', '2');//开启
//买单功能开启状态
define('MERCHANT_CHECK_CLOSE', '1');//关闭
define('MERCHANT_CHECK_OPEN', '2');//开启
//优惠券功能开启状态
define('MERCHANT_COUPONS_CLOSE', '1');//关闭
define('MERCHANT_COUPONS_OPEN', '2');//开启
//红包功能开启状态
define('MERCHANT_HONGBAO_CLOSE', '1');//关闭
define('MERCHANT_HONGBAO_OPEN', '2');//开启
//线上商城功能开启状态
define('MERCHANT_ONLINEMALL_CLOSE', '1');//关闭
define('MERCHANT_ONLINEMALL_OPEN', '2');//开启
//商户审核状态
define('MERCHANT_VERIFY_STATUS_UNINPUT', '1');//未录入支付宝
define('MERCHANT_VERIFY_STATUS_INPUT_SUCCESS', '2');//已录入支付宝
define('MERCHANT_VERIFY_STATUS_NUSIGN', '3');//未签约
define('MERCHANT_VERIFY_STATUS_AUDITING', '4');//签约审核中
define('MERCHANT_VERIFY_STATUS_SIGN_SUCCESS', '5');//已签约
define('MERCHANT_VERIFY_STATUS_REJECT', '6');//驳回
define('MERCHANT_VERIFY_STATUS_OPEN_WQGJ', '7');//已开通玩券管家

$GLOBALS['__MERCHANT_VERIFY'] = array(
		MERCHANT_VERIFY_STATUS_UNINPUT => '未录入支付宝',
		MERCHANT_VERIFY_STATUS_INPUT_SUCCESS => '已录入支付宝',
		MERCHANT_VERIFY_STATUS_NUSIGN => '未签约',
		MERCHANT_VERIFY_STATUS_AUDITING => '签约审核中',
		MERCHANT_VERIFY_STATUS_SIGN_SUCCESS => '已签约',
		MERCHANT_VERIFY_STATUS_REJECT => '驳回',
		MERCHANT_VERIFY_STATUS_OPEN_WQGJ => '已开通玩券管家'
);

//微信商户审核状态//商户审核状态
define('WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT', '1');//未提交
define('WECHATMERCHANT_VERIFY_STATUS_UNCHECK', '2');//待审核
define('WECHATMERCHANT_VERIFY_STATUS_CHECKED', '3');//审核通过，等待商户验证
define('WECHATMERCHANT_VERIFY_STATUS_NOSIGN', '4');//未签署
define('WECHATMERCHANT_VERIFY_STATUS_SIGN', '5');//已签署
define('WECHATMERCHANT_VERIFY_STATUS_REJECT', '6');//驳回
define('WECHATMERCHANT_VERIFY_STATUS_OPEN_WQGJ', '7');//已开通玩券管家
define('WECHATMERCHANT_VERIFY_STATUS_IN', '8');//微信已录入

$GLOBALS['__WECHAT_MERCHANT_VERIFY'] = array(
		WECHATMERCHANT_VERIFY_STATUS_UNSUBMIT => '未提交',
		WECHATMERCHANT_VERIFY_STATUS_UNCHECK => '待审核',
		WECHATMERCHANT_VERIFY_STATUS_CHECKED => '审核通过，等待商户验证',
		WECHATMERCHANT_VERIFY_STATUS_NOSIGN => '未签署',
		WECHATMERCHANT_VERIFY_STATUS_SIGN => '已签署',
		WECHATMERCHANT_VERIFY_STATUS_REJECT => '驳回',
		WECHATMERCHANT_VERIFY_STATUS_OPEN_WQGJ => '已开通玩券管家',
        WECHATMERCHANT_VERIFY_STATUS_IN=>'微信已录入'
);

//商户类型
define('MERCHANT_TYPE_QIYE', '1');//企业
define('MERCHANT_TYPE_GEREN', '2');//个人
$GLOBALS['__MERCHANT_TYPE'] = array(
		MERCHANT_TYPE_QIYE => '企业',
		MERCHANT_TYPE_GEREN => '个人'
);

//储值功能开启状态
define('IF_STORED_NO', '1');//关闭
define('IF_STORED_YES', '2');//开启

//玩券管家试用状态
define('TRYOUT_STATUS_NORMAL', '1');//正常
define('TRYOUT_STATUS_LOCK', '2');//锁定

//玩券管家开通状态
define('GJ_OPEN_STATUS_NULL', '1');//未开通
define('GJ_OPEN_STATUS_OPEN', '2');//已开通
define('GJ_OPEN_STATUS_OVERTIME', '3');//已过期
$GLOBALS['GJ_OPEN_STATUS'] = array(
		GJ_OPEN_STATUS_NULL => '未开通',
		GJ_OPEN_STATUS_OPEN => '已开通',
		GJ_OPEN_STATUS_OVERTIME => '已过期'
);

//微信设置-公众号类型
define('WECHAT_TYPE_SUBSCRIBE', '1');//订阅号
define('WECHAT_TYPE_SUBSCRIBE_AUTH', '2');//订阅认证号
define('WECHAT_TYPE_SERVICE', '3');//服务号
define('WECHAT_TYPE_SERVICE_AUTH', '4');//服务认证号

$GLOBALS['__WECHAT_TYPE'] = array(
		WECHAT_TYPE_SUBSCRIBE => '订阅号',
		WECHAT_TYPE_SUBSCRIBE_AUTH => '订阅认证号',
		WECHAT_TYPE_SERVICE => '服务号',
		WECHAT_TYPE_SERVICE_AUTH => '服务认证号'
);


/**************合同**************/
//合同状态
define('CONTRACT_STATUS_UNSUBMIT', '1');//未提交支付宝
define('CONTRACT_STATUS_SUBMIT', '2');//等待商户确认，已提交支付宝
define('CONTRACT_STATUS_AUDIT', '3');//支付宝审核中
define('CONTRACT_STATUS_EFFECT', '4');//已生效
define('CONTRACT_STATUS_REJECT', '5');//驳回
define('CONTRACT_STATUS_FAILURE', '6');//已失效

$GLOBALS['__CONTRACT_STATUS'] = array(
		CONTRACT_STATUS_UNSUBMIT => '未提交支付宝',
		CONTRACT_STATUS_SUBMIT => '已提交支付宝，等待商户确认',
		CONTRACT_STATUS_AUDIT => '支付宝审核中',
		CONTRACT_STATUS_EFFECT => '已生效',
		CONTRACT_STATUS_REJECT => '驳回',
		CONTRACT_STATUS_FAILURE => '已失效'
);


/**************门店**************/
//门店状态
define('STORE_STATUS_NORMAL', '1');//正常
define('STORE_STATUS_LOCK', '2');//锁定

//是否启用打印   (1:不启用 2:启用)
define('PRINT_NO', '1');//不启用
define('PRINT_YES', '2');//启用

/**************操作员**************/
//角色
define('OPERATOR_ROLE_NORMAL', '1');//店员
define('OPERATOR_ROLE_ADMIN', '2');//店长
$GLOBALS['__OPERATOR_ROLE'] = array(
		OPERATOR_ROLE_NORMAL => '店员',
		OPERATOR_ROLE_ADMIN => '店长'
);
//操作员状态
define('OPERATOR_STATUS_NORMAL', '1');//正常
define('OPERATOR_STATUS_LOCK', '2');//锁定
$GLOBALS['__OPERATOR_STATUS'] = array(
		OPERATOR_STATUS_NORMAL => '正常',
		OPERATOR_STATUS_LOCK => '锁定'
);



/**************管理员**************/
//管理员状态
define('ADMIN_STATUS_NORMAL', '1');//正常
define('ADMIN_STATUS_LOCK', '2');//锁定

/**************订单**************/
//订单类型
define('ORDER_TYPE_OBJECT', '1');//实物
define('ORDER_TYPE_VIRTUAL', '2');//电子
define('ORDER_TYPE_CASHIER', '3');//收款
$GLOBALS['ORDER_TYPE'] = array(
		ORDER_TYPE_OBJECT => '实物',
		ORDER_TYPE_VIRTUAL => '电子',
		ORDER_TYPE_CASHIER => '收款'
);

//订单付款状态
define('ORDER_STATUS_UNPAID', '1');//待付款
define('ORDER_STATUS_PAID', '2');//已付款
$GLOBALS['ORDER_STATUS_PAY'] = array(
		ORDER_STATUS_UNPAID => '待付款',
		ORDER_STATUS_PAID => '已付款'
);

//终端类型
define('TERMINAL_TYPE_WEB', '1');//web终端
define('TERMINAL_TYPE_ANDROID', '2');//安卓
define('TERMINAL_TYPE_IOS', '3');//iOS
define('TERMINAL_TYPE_PC', '4');//pc终端
define('TERMINAL_TYPE_POS', '5');//pos终端
$GLOBALS['__TERMINAL_TYPE_POS'] = array(
    TERMINAL_TYPE_WEB => 'web终端',
    TERMINAL_TYPE_ANDROID => '安卓',
    TERMINAL_TYPE_IOS => 'iOS',
    TERMINAL_TYPE_PC => 'pc终端',
    TERMINAL_TYPE_POS => 'pos终端'
    );

//激活码状态
define('ACTIVATION_CODE_STATUS_NORMAL', '1'); //正常
define('ACTIVATION_CODE_STATUS_LOCK', '2'); //失效
define('ACTIVATION_CODE_STATUS_EXPIRED', '3'); //过期
$GLOBALS['ACTIVATION_CODE_STATUS'] = array(
		ACTIVATION_CODE_STATUS_NORMAL => '正常',
		ACTIVATION_CODE_STATUS_LOCK => '已失效',
		ACTIVATION_CODE_STATUS_EXPIRED => '已过期'
);

//订单状态
define('ORDER_STATUS_CANCEL', '1');//已取消
define('ORDER_STATUS_WAITFORDELIVER', '2');//待发货
define('ORDER_STATUS_WAITFORCREATE', '3');//待生成
define('ORDER_STATUS_REVOKE', '4');//已撤销
define('ORDER_STATUS_DELIVER', '5');//已发货
define('ORDER_STATUS_CREATE', '6');//已生成
define('ORDER_STATUS_NORMAL', '7');//正常
define('ORDER_STATUS_ACCEPT', '8');//已收货
define('ORDER_STATUS_USED', '9');//已使用
define('ORDER_STATUS_HANDLE_REFUND', '10');//退款处理中
define('ORDER_STATUS_REFUND', '11');//已退款
define('ORDER_STATUS_PART_REFUND', '12');//已部分退款
define('ORDER_STATUS_PART_COMPLETE', '13');//已完成交易
$GLOBALS['ORDER_STATUS'] = array(
		ORDER_STATUS_CANCEL => '已取消',
		ORDER_STATUS_WAITFORDELIVER => '待发货',
		ORDER_STATUS_WAITFORCREATE => '待生成',
		ORDER_STATUS_REVOKE => '已撤销',
		ORDER_STATUS_DELIVER => '已发货',
		ORDER_STATUS_CREATE => '已生成',
		ORDER_STATUS_NORMAL => '正常',
		ORDER_STATUS_ACCEPT => '已收货',
		ORDER_STATUS_USED => '已使用',
		ORDER_STATUS_HANDLE_REFUND => '退款处理中',
		ORDER_STATUS_REFUND => '已退款',
		ORDER_STATUS_PART_REFUND => '已部分退款',
		ORDER_STATUS_PART_COMPLETE => '已完成交易',
);

//玩券管家订单管理-订单状态
define('GJ_ORDER_STATUS_CANCEL', '1');//已取消
define('GJ_ORDER_STATUS_WAITFORDELIVER', '2');//待发货
define('GJ_ORDER_STATUS_DELIVER', '5');//已发货
define('GJ_ORDER_STATUS_ACCEPT', '8');//已收货
$GLOBALS['GJ_ORDER_STATUS'] = array(
    GJ_ORDER_STATUS_CANCEL => '已取消',
    GJ_ORDER_STATUS_WAITFORDELIVER => '待发货',
    GJ_ORDER_STATUS_DELIVER => '已发货',
    GJ_ORDER_STATUS_ACCEPT => '已收货',
);

//订单sku状态
define('ORDER_SKU_STATUS_NORMAL', '1');//正常
define('ORDER_SKU_STATUS_CANCEL', '2');//已取消
define('ORDER_SKU_STATUS_REFUND', '3');//退款中
define('ORDER_SKU_STATUS_REFUNDSUCCESS', '4');//已退款
$GLOBALS['ORDER_SKU_STATUS'] = array(
    ORDER_SKU_STATUS_NORMAL=>'正常',
    ORDER_SKU_STATUS_CANCEL=>'已取消',
    ORDER_SKU_STATUS_REFUND=>'退款中',
    ORDER_SKU_STATUS_REFUNDSUCCESS=>'已退款',
);
//订单退款记录状态
define('ORDER_REFUND_STATUS_APPLY_REFUND_NORETURN', '1'); //申请退款无需退货
define('ORDER_REFUND_STATUS_APPLY_REFUND_RETURN', '2'); //申请退款需退货
define('ORDER_REFUND_STATUS_AGREE_NORETURN', '3'); //同意退款无需退货
define('ORDER_REFUND_STATUS_AGREE_RETURN', '4'); //同意退款需退货
define('ORDER_REFUND_STATUS_RETURN_ISSUED', '5'); //退货已发出
define('ORDER_REFUND_STATUS_RETURN_RECEIPT', '6'); //退货已收货
define('ORDER_REFUND_STATUS_FINANCIAL_PLAY', '7'); //财务打款中
define('ORDER_REFUND_STATUS_REFUND_SUCCESS', '8'); //退款成功
define('ORDER_REFUND_STATUS_REFUSE_REFUND', '9'); //拒绝退款
define('ORDER_REFUND_STATUS_REFUSE_RECEIPT', '10'); //拒绝收货
$GLOBALS['ORDER_REFUND_STATUS'] = array(
		ORDER_REFUND_STATUS_APPLY_REFUND_NORETURN => '申请退款无需退货',
		ORDER_REFUND_STATUS_APPLY_REFUND_RETURN => '申请退款需退货',
		ORDER_REFUND_STATUS_AGREE_NORETURN => '同意退款无需退货',
		ORDER_REFUND_STATUS_AGREE_RETURN => '同意退款需退货',
		ORDER_REFUND_STATUS_RETURN_ISSUED => '退货已发出',
		ORDER_REFUND_STATUS_RETURN_RECEIPT => '退货已收货',
		ORDER_REFUND_STATUS_FINANCIAL_PLAY => '财务打款中',
		ORDER_REFUND_STATUS_REFUND_SUCCESS => '退款成功',
		ORDER_REFUND_STATUS_REFUSE_REFUND => '拒绝退款',
        ORDER_REFUND_STATUS_REFUSE_RECEIPT=>'拒绝收货'
);

//发货状态
define('IF_SEND_NO', '1'); //未发货
define('IF_SEND_YES', '2'); //已发货

//商城退款是否需要退货
define('IF_RETURN_NO', '1'); //不需要退货
define('IF_RETURN_YES', '2'); //需要退货


//退款原因
define('REFUND_REASON_SAME', '1'); //买卖双发达成一致
define('REFUND_REASON_WRONG', '2'); //买错/多买/不想要
define('REFUND_REASON_QUALITYPROBLEM', '3'); //商品质量有问题
define('REFUND_REASON_NORECEIVE', '4'); //未收到货品
define('REFUND_REASON_OTHER', '5'); //其他
$GLOBALS['REFUND_REASON'] = array(
		REFUND_REASON_SAME => '买卖双发达成一致',
		REFUND_REASON_WRONG => '买错/多买/不想要',
		REFUND_REASON_QUALITYPROBLEM => '商品质量有问题',
		REFUND_REASON_NORECEIVE => '未收到货品',
		REFUND_REASON_OTHER => '其他'
);

//储值订单编号前缀
define('STORED_ORDER_PREFIX', '8379'); //储值订单前缀为8379

//储值支付确认状态
define('ORDER_PAY_WAITFORCONFIRM', '1');//待确认
define('ORDER_PAY_CONFIRM', '2');//已确认
define('ORDER_PAY_NUCONFIRM', '3');//无需确认

//订单支付渠道
define('ORDER_PAY_CHANNEL_ALIPAY_SM', '1'); //支付宝扫码
define('ORDER_PAY_CHANNEL_ALIPAY_TM', '2'); //支付宝条码
define('ORDER_PAY_CHANNEL_UNIONPAY', '3'); //银联支付
define('ORDER_PAY_CHANNEL_CASH', '4'); //现金支付
define('ORDER_PAY_CHANNEL_POINTS', '5'); //积分支付
define('ORDER_PAY_CHANNEL_STORED', '6'); //储值支付
define('ORDER_PAY_CHANNEL_ALIPAY', '7'); //支付宝
define('ORDER_PAY_CHANNEL_NO_MONEY', '8'); //无需付款
define('ORDER_PAY_CHANNEL_WXPAY_SM', '9'); //微信扫码
define('ORDER_PAY_CHANNEL_WXPAY_TM', '10'); //微信条码
define('ORDER_PAY_CHANNEL_WXPAY', '11'); //微信
$GLOBALS['ORDER_PAY_CHANNEL'] = array(
		ORDER_PAY_CHANNEL_ALIPAY_SM => '支付宝扫码',
		ORDER_PAY_CHANNEL_ALIPAY_TM => '支付宝条码',
		ORDER_PAY_CHANNEL_UNIONPAY => '银联支付',
		ORDER_PAY_CHANNEL_CASH => '现金支付',
		ORDER_PAY_CHANNEL_POINTS => '积分支付',
		ORDER_PAY_CHANNEL_STORED => '储值支付',
		ORDER_PAY_CHANNEL_ALIPAY => '支付宝',
		ORDER_PAY_CHANNEL_NO_MONEY => '无需付款',
		ORDER_PAY_CHANNEL_WXPAY_SM => '微信扫码',
		ORDER_PAY_CHANNEL_WXPAY_TM => '微信条码',
		ORDER_PAY_CHANNEL_WXPAY => '微信'
);

$GLOBALS['SHOP_ORDER_PAY_CHANNEL'] = array(
		ORDER_PAY_CHANNEL_ALIPAY => '支付宝',
		ORDER_PAY_CHANNEL_WXPAY => '微信'
);

//是否开启支付宝收款账号
define('IF_ALIPAY_OPEN_NO', '1'); //关闭
define('IF_ALIPAY_OPEN_YES', '2'); //开启

//是否开启微信收款账号
define('IF_WXPAY_OPEN_NO', '1'); //关闭
define('IF_WXPAY_OPEN_YES', '2'); //开启

//微信支付的商户类型
define('WXPAY_MERCHANT_TYPE_SELF', '1'); //自助商户
define('WXPAY_MERCHANT_TYPE_AFFILIATE', '2'); //特约商户

//支付宝支付的接口版本
define('ALIPAY_API_VERSION_1', '1'); //支付宝1.0接口
define('ALIPAY_API_VERSION_2', '2'); //支付宝2.0接口


//门店是否启用收银账号
define("IF_ALIPAY_OPEN_CLOSE", '1');//关闭
define("IF_ALIPAY_OPEN_OPEN", '2');//启用

//门店是否使用上级收银账号
define("IF_USE_PRO_NO", '1');//不使用
define("IF_USE_PRO_YES", '2');//使用

//支付宝支付渠道代码
define('ALIPAY_FUND_CHANNEL_COUPON', 'COUPON'); //支付宝红包
define('ALIPAY_FUND_CHANNEL_ALIPAYACCOUNT', 'ALIPAYACCOUNT'); //支付宝余额
define('ALIPAY_FUND_CHANNEL_POINT', 'POINT'); //积分
define('ALIPAY_FUND_CHANNEL_DISCOUNT', 'DISCOUNT'); //折扣券
define('ALIPAY_FUND_CHANNEL_MCARD', 'MCARD'); //商户店铺卡
define('ALIPAY_FUND_CHANNEL_MDISCOUNT', 'MDISCOUNT'); //商户优惠券
define('ALIPAY_FUND_CHANNEL_MCOUPON', 'MCOUPON'); //商户红包
define('ALIPAY_FUND_CHANNEL_00', '00'); //支付宝红包（1.0）
define('ALIPAY_FUND_CHANNEL_10', '10'); //支付宝余额（1.0）
define('ALIPAY_FUND_CHANNEL_30', '30'); //积分（1.0）
define('ALIPAY_FUND_CHANNEL_40', '40'); //折扣券（1.0）
define('ALIPAY_FUND_CHANNEL_101', '101'); //商户店铺卡（1.0）
define('ALIPAY_FUND_CHANNEL_102', '102'); //商户优惠券（1.0）
define('ALIPAY_FUND_CHANNEL_104', '104'); //商户红包（1.0）


//发货方式  
define('SEND_TYPE_YESLOGISTICS', '1'); //需要物流
define('SEND_TYPE_NOLOGISTICS', '2'); //无需物流
$GLOBALS['SEND_TYPE'] = array(
		SEND_TYPE_YESLOGISTICS => '需要物流',
		SEND_TYPE_NOLOGISTICS => '无需物流'
);

//物流公司
define('LOGISTICS_COMPANY_YT', '1'); //圆通
define('LOGISTICS_COMPANY_ZT', '2'); //中通
define('LOGISTICS_COMPANY_ST', '3'); //申通 
define('LOGISTICS_COMPANY_SF', '4'); //顺丰
$GLOBALS['LOGISTICS_COMPANY'] = array(
		LOGISTICS_COMPANY_YT => '圆通',
		LOGISTICS_COMPANY_ZT => '中通',
		LOGISTICS_COMPANY_ST => '申通',
		LOGISTICS_COMPANY_SF => '顺丰'
);



//交易类型(1:支付宝条码，2:支付宝扫码，3:现金，4:银联刷卡，5:储值)
define('PAY_TYPE_BAR_CODE', '1');
define('PAY_TYPE_SWEEP_CODE', '2');
define('PAY_TYPE_CASH', '3');
define('PAY_TYPE_UNIONPAY_CARD', '4');
define('PAY_TYPE_STORED_VALUE', '5');
$GLOBALS['PAY_TYPE'] = array(
		PAY_TYPE_BAR_CODE => '支付宝条码',
		PAY_TYPE_SWEEP_CODE => '支付宝扫码',
		PAY_TYPE_CASH => '现金',
		PAY_TYPE_UNIONPAY_CARD =>'银联刷卡',
		PAY_TYPE_STORED_VALUE => '储值'
);

//订单有无使用优惠券
define('ORDER_IF_USE_COUPONS_NO', '1');//没有使用
define('ORDER_IF_USE_COUPONS_YES', '2');//有使用

//退款记录类型
define('REFUND_TYPE_REFUND', '1');//退款
define('REFUND_TYPE_REVOKE', '2');//撤销
define('REFUND_TYPE_SCREFUND', '3');//商城退款

//退款状态
define('REFUND_STATUS_SUCCESS', '1'); //退款成功
define('REFUND_STATUS_FAIL', '2'); //退款失败
define('REFUND_STATUS_PROCESSING', '3'); //退款处理中

/*************优惠券****************/

//面额类型
define('FACE_VALUE_TYPE_FIXED', '1');//固定面额
define('FACE_VALUE_TYPE_RANDOM', '2');//随机面额
$GLOBALS['FACE_VALUE_TYPE'] = array(
		FACE_VALUE_TYPE_FIXED => '固定面额',
		FACE_VALUE_TYPE_RANDOM => '随机面额'
);

//有效时间类型
define('VALID_TIME_TYPE_FIXED', '1');//固定时间
define('VALID_TIME_TYPE_RELATIVE', '2');//相对时间
$GLOBALS['VALID_TIME_TYPE'] = array(
		VALID_TIME_TYPE_FIXED => '固定时间',
		VALID_TIME_TYPE_RELATIVE => '相对时间'
);

//优惠券类型
define('COUPON_TYPE_REDENVELOPE', '1');//红包
define('COUPON_TYPE_CASH', '2');//代金券
define('COUPON_TYPE_DISCOUNT', '3');//折扣券
define('COUPON_TYPE_EXCHANGE', '4');//兑换券
$GLOBALS['COUPON_TYPE'] = array(
		COUPON_TYPE_REDENVELOPE => '红包',
		COUPON_TYPE_CASH => '代金券',
		COUPON_TYPE_DISCOUNT => '折扣券',
		COUPON_TYPE_EXCHANGE => '兑换券'
);

//退款处理
define('REFUND_DEAL_REFUND', '1');//退款时退还优惠券
define('REFUND_DEAL_NOTREFUND', '2');//退款时不退还优惠券
$GLOBALS['REFUND_DEAL'] = array(
		REFUND_DEAL_REFUND => '退款时退还优惠券',
		REFUND_DEAL_NOTREFUND => '退款时不退还优惠券'
);

//优惠券使用状态
define('COUPONS_USE_STATUS_UNUSE', '1');//未使用
define('COUPONS_USE_STATUS_USED', '2');//已使用
define('COUPONS_USE_STATUS_EXPIRED', '3');//已过期
define('COUPONS_USE_STATUS_GAVE', '4');//已转赠
$GLOBALS['COUPONS_USE_STATUS'] = array(
		COUPONS_USE_STATUS_UNUSE => '未使用',
		COUPONS_USE_STATUS_USED => '已使用',
		COUPONS_USE_STATUS_EXPIRED => '已过期',
		COUPONS_USE_STATUS_GAVE => '已转赠',
);

//是否同步到微信卡包
define('COUPONS_IF_WECHAT_NO', 1);//否
define('COUPONS_IF_WECHAT_YES', 2);//是

//优惠券转赠状态
define('COUPONS_GIVE_NO', '1');//不是
define('COUPONS_GIVE_YES', '2');//是

//是否使用优惠券和红包
define('IF_USER_COUPONS_YES','1');//有
define('IF_USER_COUPONS_NO','2');//没有

//允许多个优惠券同时使用
define('COUPONS_ALLOW_MANY_YES', '1');//允许多个
define('COUPONS_ALLOW_MANY_NO', '2');//不允许多个

//是否能与会员折扣同用 1 不能 2 能
define('IF_WITH_USERDISCOUNT_NO','1');
define('IF_WITH_USERDISCOUNT_YES','2');
$GLOBALS['IF_WITH_USERDISCOUNT'] = array(
		IF_WITH_USERDISCOUNT_NO => '不能',
		IF_WITH_USERDISCOUNT_YES => '能'
);

//是否能与优惠券（折扣券、代金券）同用 1 不能 2 能  
define('IF_WITH_COUPONS_NO','1');
define('IF_WITH_COUPONS_YES','2');
$GLOBALS['IF_WITH_COUPONS'] = array(
		IF_WITH_COUPONS_NO => '不能',
		IF_WITH_COUPONS_YES => '能'
);

//是否同步到微信卡包 1不开启 2开启
define('IF_WECHAT_NO', '1');
define('IF_WECHAT_YES', '2');

//用户是否可以分享领取链接 1可以 2不可以
define('IF_SHARE_NO', '2');
define('IF_SHARE_YES', '1');

//可否转增其他好友 1 能 2不能
define('IF_GIVE_NO', '2');
define('IF_GIVE_YES', '1');

//微信审核状态   1审核中 2已通过 3未通过
define('WX_CHECK_AUDIT', '1');
define('WX_CHECK_PASS', '2');
define('WX_CHECK_NOTPASS', '3');
$GLOBALS['WX_CHECK'] = array(
		WX_CHECK_AUDIT => '审核中',
		WX_CHECK_PASS => '已通过',
		WX_CHECK_NOTPASS => '未通过'
);

//优惠券是否失效 1未失效 2已失效
define('IF_INVALID_NO', '1');
define('IF_INVALID_YES', '2');

//积分收支类型
define('POINT_PAYMENT_PAY', '1');//支出
define('POINT_PAYMENT_EARN', '2');//收入
$GLOBALS['POINT_PAYMENT'] = array(
		POINT_PAYMENT_PAY => '支出',
		POINT_PAYMENT_EARN => '收入'
);


/****自定义菜单节点****/
define('WQ_MENY_NODE_ROOT', 0);

//菜单类型
define('WQ_MENU_TYPE_WORD', 1);//文字消息
define('WQ_MENU_TYPE_PHOTO', 2);//图文消息
define('WQ_MENU_TYPE_WWW', 3);//链接网址
define('WQ_MENU_TYPE_SYSTEM', 4);//链接网址
$GLOBALS['WQ_MENU_TYPE'] = array(
		WQ_MENU_TYPE_WORD => '文字消息',
		WQ_MENU_TYPE_PHOTO => '图文消息',
		WQ_MENU_TYPE_WWW => '链接网址',
		WQ_MENU_TYPE_SYSTEM => '系统网址',
);


//预定状态
define('BOOK_RECORD_STATUS_WAIT', '1');//待确认
define('BOOK_RECORD_STATUS_ACCEPT', '2');//已接单
define('BOOK_RECORD_STATUS_REFUSE', '3');//已拒单
define('BOOK_RECORD_STATUS_ARRIVE', '4');//已到店
define('BOOK_RECORD_STATUS_CANCEL', '5');//已取消

$GLOBALS['BOOK_RECORD_STATUS'] = array(
		BOOK_RECORD_STATUS_WAIT => '等待商户确认',
		BOOK_RECORD_STATUS_ACCEPT => '商户已接单',
		BOOK_RECORD_STATUS_REFUSE => '商户已拒单',
		BOOK_RECORD_STATUS_ARRIVE => '已到店',
		BOOK_RECORD_STATUS_CANCEL => '已取消',
);

//验证数字
define('POSITIVE_REGEX', '/^\d*(\.\d{1,2})?$/');


//菜单管理链接
define('URL_ZXSP','http://gj.test.51wanquan.com/uCenter/user/shop?');//在线商铺
define('URL_YHZX', 'http://gj.test.51wanquan.com/uCenter/user/memberCenter?');//会员中心
$GLOBALS['__URL']=array(
		URL_ZXSP => '在线商铺',
		URL_YHZX => '会员中心'
);


//素材管理链接
define('MATERIAL_CONTENT_TEXT', 1); //跳转正文
define('MATERIAL_CONTENT_URL', 2); //跳转链接
$GLOBALS['MATERIAL_JUMP_TYPE']=array(
		MATERIAL_CONTENT_TEXT => '跳转到正文',
		MATERIAL_CONTENT_URL => '跳转到链接'
);

//自动回复类型
define('REPLY_TYPE_MSG', 1); //消息自动回复
define('REPLY_TYPE_KEYWORD', 2); //关键词自动回复
define('REPLY_TYPE_BROADCAST', 3); //群发广播
$GLOBALS['REPLY_TYPE']=array(
		REPLY_TYPE_MSG => '消息自动回复',
		REPLY_TYPE_KEYWORD => '关键词自动回复',
);

//自动回复类型
define('FROM_PLATFORM_ALI', 1); //服务窗
define('FROM_PLATFORM_WECHAT', 2); //微信


//发布状态
define('RELEASE_STATUS_NO', 1); // 未发布
define('RELEASE_STATUS_YES', 2); // 已发布
$GLOBALS['RELEASE_STATUS'] = array(
		RELEASE_STATUS_NO => '未发布',
		RELEASE_STATUS_YES => '已发布'
);

//资料下载类型
define('DOWNLOAD_TYPE_DOCUMENT', 1); // 文档 
define('DOWNLOAD_TYPE_VIDEO', 2); // 视频
$GLOBALS['DOWNLOAD_TYPE'] = array(
		DOWNLOAD_TYPE_DOCUMENT => '文档 ',
		DOWNLOAD_TYPE_VIDEO => '视频'
);

//资料发布到
define('PUBLIC_TO_FX', 1); // 分销
define('PUBLIC_TO_GJ', 2); // 管家
$GLOBALS['PUBLIC_TO'] = array(
		PUBLIC_TO_FX => '分销 ',
		PUBLIC_TO_GJ => '管家'
);

//url验证正则
define('URL_LEGAL', '/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is'); 
//邮箱验证正则
define('EMAIL_CHECK', '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/');
//手机号验证
define('PHONE_CHECK', '/^(1(([35][0-9])|(47)|[8][01236789]))\d{8}$/');
//身份证验证
define('IDCARD','/^(\d{6})(18|19|20)?(\d{2})([01]\d)([0123]\d)(\d{3})(\d|X)?$/');
//电话验证正则
define('TEL_CHECK', '/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/');
//网址验证正则
define('WEB_CHECK', '/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/');

//微信
/****微信API****/
define('WECHAT_API_URL', "https://api.weixin.qq.com/cgi-bin/"); //微信API


//wap支付方式
define('WAP_ALIPAY', '1');
define('WAP_WECHATPAY', '2');


//合作商 合作等级
define('COOPERATIVE_LEVEL_COUNTRY',1); //全国
define('COOPERATIVE_LEVEL_PROVINCE',2); //省级
define('COOPERATIVE_LEVEL_CITY',3); //市级
define('COOPERATIVE_LEVEL_AREA',4); //区 县
$GLOBALS['COOPERATIVE_LEVE'] = array(
		COOPERATIVE_LEVEL_COUNTRY => '全国',
		COOPERATIVE_LEVEL_PROVINCE => '省级',
		COOPERATIVE_LEVEL_CITY => '市级',
		COOPERATIVE_LEVEL_AREA => '区县'
);

//合作类型
define('AGENT_TYPE_COMPANY',1); //公司
define('AGENT_TYPE_PERSON',2); //个人
$GLOBALS['AGENT_TYPE'] = array(
		AGENT_TYPE_COMPANY => '公司',
		AGENT_TYPE_PERSON => '个人'
);

//合作等级收费(全国15.8w，省5.8w，市2.8w，区县0.88万)
define('COOPERATIVE_PAY_COUNTRY',1); //15.8
define('COOPERATIVE_PAY_PROVINCE',2); //5.8
define('COOPERATIVE_PAY_CITY',3); //2.8
define('COOPERATIVE_PAY_AREA',4); //0.88
$GLOBALS['COOPERATIVE_PAY'] = array(
		COOPERATIVE_PAY_COUNTRY => 15.8,
		COOPERATIVE_PAY_PROVINCE => 5.8,
		COOPERATIVE_PAY_CITY => 2.8,
		COOPERATIVE_PAY_AREA => 0.88
);

//多图文素材状态
define('MATERIAL_TYPE_ADD',1); //新增
define('MATERIAL_TYPE_EDIT',2); //修改
define('MATERIAL_TYPE_DEL',3); //删除

//pos机接口请求结果码
define('POS_RESULT_ERROR_NULL', '0'); //请求正确
define('POS_RESULT_ERROR_PARAM', '1'); //参数不正确
define('POS_RESULT_ERROR_TOKEN', '2');  //身份验证失败
define('POS_RESULT_ERROR_ACCOUNT', '3');  //账号密码错误
define('POS_RESULT_ERROR_POSNO', '4');  //POS编号与关联门店信息不匹配
define('POS_RESULT_ERROR_NO_DATA', '5');  //订单数据不存在
define('POS_RESULT_ERROR_NO_STORE', '6');  //门店信息不存在
define('POS_RESULT_ERROR_CREATE_ORDER', '7');  //创建订单失败
define('POS_RESULT_ERROR_STORE_ADMIN_PWD_NULL', '8');  //请输入主管密码
define('POS_RESULT_ERROR_STORE_ADMIN_PWD_ERROR', '9');  //主管密码错误
define('POS_RESULT_ERROR_STORE_OLD_PWD_ERROR', '10');  //修改密码原密码错误
define('POS_RESULT_ERROR_STORE_PWD_UPDATE_ERROR', '11');  //修改密码失败
define('POS_RESULT_ERROR_ORDER_REFUND_UNTREATED', '12');  //该订单还有未完成的退款记录
define('POS_RESULT_ERROR_ORDER_REFUND_EXCESSIVE_AMOUNT', '13');  //退款金额大于实际支付金额

//API渠道
define('API_CHANNEL_POS', 'POS'); //POS渠道
define('API_CHANNEL_LYJ', 'LYJ'); //乐优家渠道
define('API_CHANNEL_ANDROID', 'ANDROID'); //安卓渠道
define('API_CHANNEL_IOS', 'IOS'); //iOS渠道
define('API_CHANNEL_PC', 'PC'); //pc渠道

//文章类型
define('ARTICLE_TYPE_HELP', 1); //帮助中心
define('ARTICLE_TYPE_RECRUIT', 2); //玩券招聘
define('ARTICLE_TYPE_ABOUT', 3); //关于我们
$GLOBALS['ARTICLE_TYPE'] = array(
		ARTICLE_TYPE_HELP => '帮助中心',
		ARTICLE_TYPE_RECRUIT => '玩券招聘',
		ARTICLE_TYPE_ABOUT => '关于我们'
);

/************************商城************************/

//使用时间类型
define('DSHOP_TIME_TYPE_DAY',1);//有效天
define('DSHOP_TIME_TYPE', 2);//自选游玩时间 

//商品类型
define('SHOP_TYPE_GROUP', 1); //商品分组
define('SHOP_TYPE_PRODUCT', 2); //商品

//商城常量
define('SHOP_CATEGORY_FOOD', 'food');//食品
define('SHOP_FOOD_TEA', 'f1');//茶叶茶饮
define('SHOP_FOOD_JGCH', 'f2');//坚果炒货
define('SHOP_FOOD_LS', 'f3');//零食
define('SHOP_FOOD_TC', 'f4');//特产
define('SHOP_FOOD_WINE', 'f5');//酒水
define('SHOP_FOOD_FRUIT', 'f6');//水果
define('SHOP_FOOD_SX', 'f7');//生鲜
define('SHOP_FOOD_CAKE', 'f8');//蛋糕
define('SHOP_FOOD_HP', 'f9');//烘焙
define('SHOP_FOOD_YYZBP', 'f10');//营养滋补品
define('SHOP_FOOD_RICE', 'f11');//粮油米面
define('SHOP_FOOD_NBGH', 'f12');//南北干货
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_FOOD] = array(
	SHOP_FOOD_TEA => '茶叶茶饮',
	SHOP_FOOD_JGCH => '坚果炒货',
	SHOP_FOOD_LS => '零食',
	SHOP_FOOD_TC => '特产',
	SHOP_FOOD_WINE => '酒水',
	SHOP_FOOD_FRUIT => '水果',
	SHOP_FOOD_SX => '生鲜',
	SHOP_FOOD_CAKE => '蛋糕',
	SHOP_FOOD_HP => '烘焙',
	SHOP_FOOD_YYZBP => '营养滋补品',
	SHOP_FOOD_RICE => '粮油米面',
	SHOP_FOOD_NBGH => '南北干货',
);

define('SHOP_CATEGORY_BEAUTY', 'beauty');//美妆
define('SHOP_BEAUTY_JMHU', 'b1');//洁面护肤
define('SHOP_BEAUTY_MMMSYS', 'b2');//面膜面霜眼霜
define('SHOP_BEAUTY_JYFL', 'b3');//精油芳疗
define('SHOP_BEAUTY_SGZ', 'b4');//手工皂
define('SHOP_BEAUTY_CZXS', 'b5');//彩妆香水
define('SHOP_BEAUTY_GRHL', 'b6');//个人护理
define('SHOP_BEAUTY_LYXH', 'b7');//淋浴洗护
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_BEAUTY] = array(
	SHOP_BEAUTY_JMHU => '洁面护肤',
	SHOP_BEAUTY_MMMSYS => '面膜面霜眼霜',
	SHOP_BEAUTY_JYFL => '精油芳疗',
	SHOP_BEAUTY_SGZ => '手工皂',
	SHOP_BEAUTY_CZXS => '彩妆香水',
	SHOP_BEAUTY_GRHL => '个人护理',
	SHOP_BEAUTY_LYXH => '淋浴洗护',
);

define('SHOP_CATEGORY_LADIES', 'ladies');//女装
define('SHOP_LADIES_NZ', 'l1');//女装
define('SHOP_LADIES_NSNY', 'l2');//女士内衣
define('SHOP_LADIES_JJF', 'l3');//家居服
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_LADIES] = array(
	 SHOP_LADIES_NZ => '女装',
	 SHOP_LADIES_NSNY => '女士内衣',
	SHOP_LADIES_JJF => '家居服',
);

define('SHOP_CATEGORY_GENTLEMAN', 'gentleman');//男装
define('SHOP_GENTLEMAN_NZ', 'g1');//男装
define('SHOP_GENTLEMAN_NSNY', 'g2');//男士内衣
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_GENTLEMAN] = array(
	SHOP_GENTLEMAN_NZ => '男装',
	SHOP_GENTLEMAN_NSNY => '男士内衣',
);

define('SHOP_CATEGORY_PARENTING', 'parenting');//亲子
define('SHOP_PARENTING_MYSP', 'p1');//孕婴食品
define('SHOP_PARENTING_MYYP', 'p2');//母婴用品
define('SHOP_PARENTING_YQCH', 'p3');//孕期产后（孕妇装、产后用品、哺乳内衣、孕装）
define('SHOP_PARENTING_TZTX', 'p4');//童装童鞋
define('SHOP_PARENTING_QZXG', 'p5');//亲子相关（亲子游乐、亲子摄影、幼儿教育、孕妇写真）
define('SHOP_PARENTING_WJAH', 'p6');//玩具爱好
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_PARENTING] = array(
	SHOP_PARENTING_MYSP => '孕婴食品',
	SHOP_PARENTING_MYYP => '母婴用品',
	SHOP_PARENTING_YQCH => '孕期产后',
	SHOP_PARENTING_TZTX => '童装童鞋',
	SHOP_PARENTING_QZXG => '亲子相关',
	SHOP_PARENTING_WJAH => '玩具爱好',
);

define('SHOP_CATEGORY_DIGITAL', 'digital');//数码电器
define('SHOP_DIGITAL_SJ', 'd1');//手机
define('SHOP_DIGITAL_DN', 'd2');//电脑
define('SHOP_DIGITAL_XSQ', 'd3');//显示器
define('SHOP_DIGITAL_SB', 'd4');//鼠标
define('SHOP_DIGITAL_TYY', 'd5');//投影仪
define('SHOP_DIGITAL_DYJ', 'd6');//打印机
define('SHOP_DIGITAL_BGHC', 'd7');//办公耗材
define('SHOP_DIGITAL_LYQ', 'd8');//路由器
define('SHOP_DIGITAL_SMXJ', 'd9');//数码相机
define('SHOP_DIGITAL_DJWJ', 'd10');//电教文具
define('SHOP_DIGITAL_YX', 'd11');//音箱
define('SHOP_DIGITAL_GQHZ', 'd12');//高清盒子
define('SHOP_DIGITAL_EJEM', 'd13');//耳机耳麦
define('SHOP_DIGITAL_YDDY', 'd14');//移动电源
define('SHOP_DIGITAL_YDYP', 'd15');//移动硬盘
define('SHOP_DIGITAL_UP', 'd16');//U盘
define('SHOP_DIGITAL_ZNCD', 'd17');//智能穿戴
define('SHOP_DIGITAL_SJPJ', 'd18');//手机配件
define('SHOP_DIGITAL_YYYL', 'd19');//影音娱乐
define('SHOP_DIGITAL_DJD', 'd20');//大家电
define('SHOP_DIGITAL_CFDQ', 'd21');//厨房电器
define('SHOP_DIGITAL_TXD', 'd22');//剃须刀
define('SHOP_DIGITAL_XCQ', 'd23');//吸尘器
define('SHOP_DIGITAL_DCF', 'd24');//电吹风
define('SHOP_DIGITAL_AMY', 'd25');//按摩椅
define('SHOP_DIGITAL_KQJHSB', 'd26');//空气净化设备
define('SHOP_DIGITAL_JSSB', 'd27');//净水设备
define('SHOP_DIGITAL_DDYS', 'd28');//电动牙刷
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_DIGITAL] = array(
	SHOP_DIGITAL_SJ => '手机',
	SHOP_DIGITAL_DN => '电脑',
	SHOP_DIGITAL_XSQ => '显示器',
	SHOP_DIGITAL_SB => '鼠标',
	SHOP_DIGITAL_TYY => '投影仪',
	SHOP_DIGITAL_DYJ => '打印机',
	SHOP_DIGITAL_BGHC => '办公耗材',
	SHOP_DIGITAL_LYQ => '路由器',
	SHOP_DIGITAL_SMXJ => '数码相机',
	SHOP_DIGITAL_DJWJ => '电教文具',
	SHOP_DIGITAL_YX => '音箱',
	SHOP_DIGITAL_GQHZ => '高清盒子',
	SHOP_DIGITAL_EJEM => '耳机耳麦',
	SHOP_DIGITAL_YDDY => '移动电源',
	SHOP_DIGITAL_YDYP => '移动硬盘',
	SHOP_DIGITAL_UP => 'U盘',
	SHOP_DIGITAL_ZNCD => '智能穿戴',
	SHOP_DIGITAL_SJPJ => '手机配件',
	SHOP_DIGITAL_YYYL => '影音娱乐',
	SHOP_DIGITAL_DJD => '大家电',
	SHOP_DIGITAL_CFDQ => '厨房电器',
	SHOP_DIGITAL_TXD => '剃须刀',
	SHOP_DIGITAL_XCQ => '吸尘器',
	SHOP_DIGITAL_DCF => '电吹风',
	SHOP_DIGITAL_AMY => '按摩椅',
	SHOP_DIGITAL_KQJHSB => '空气净化设备',
	SHOP_DIGITAL_JSSB => '净水设备',
	SHOP_DIGITAL_DDYS => '电动牙刷',
);

define('SHOP_CATEGORY_HOME', 'home');//家居家纺
define('SHOP_HOME_JFZB', 'h1');//家纺被枕
define('SHOP_HOME_MJYJ', 'h2');//毛巾浴巾
define('SHOP_HOME_CRCD', 'h3');//床垫床褥
define('SHOP_HOME_DTDDDT', 'h4');//地毯地垫地拖
define('SHOP_HOME_CZBY', 'h5');//餐桌布艺
define('SHOP_HOME_ZSBJ', 'h6');//装饰摆件
define('SHOP_HOME_ZJZP', 'h7');//纸巾纸品
define('SHOP_HOME_SNYP', 'h8');//收纳用品
define('SHOP_HOME_XDCJ', 'h9');//消毒除菌
define('SHOP_HOME_XKHK', 'h10');//相框画框
define('SHOP_HOME_YQ', 'h11');//乐器
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_HOME] = array(
	SHOP_HOME_JFZB => '家纺被枕',
	SHOP_HOME_MJYJ => '毛巾浴巾',
	SHOP_HOME_CRCD => '床垫床褥',
	SHOP_HOME_DTDDDT => '地毯地垫地拖',
	SHOP_HOME_CZBY => '餐桌布艺',
	SHOP_HOME_ZSBJ => '装饰摆件',
	SHOP_HOME_ZJZP => '纸巾纸品',
	SHOP_HOME_SNYP => '收纳用品',
	SHOP_HOME_XDCJ => '消毒除菌',
	SHOP_HOME_XKHK => '相框画框',
	SHOP_HOME_YQ => '乐器',
);

define('SHOP_CATEGORY_LUGGAGE', 'luggage');//箱包配饰
define('SHOP_LUGGAGE_LXX', 'lu1');//旅行箱
define('SHOP_LUGGAGE_MENB', 'lu2');//男包
define('SHOP_LUGGAGE_LADIESB', 'lu3');//女包
define('SHOP_LUGGAGE_FSPJ', 'lu4');//服饰配件
define('SHOP_LUGGAGE_WJST', 'lu5');//围巾手套
define('SHOP_LUGGAGE_MWSW', 'lu6');//棉袜丝袜
define('SHOP_LUGGAGE_MZ', 'lu7');//帽子
define('SHOP_LUGGAGE_ZBSS', 'lu8');//珠宝首饰
define('SHOP_LUGGAGE_SLZC', 'lu9');//手链串珠
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_LUGGAGE] = array(
	SHOP_LUGGAGE_LXX => '旅行箱',
	SHOP_LUGGAGE_MENB => '男包',
	SHOP_LUGGAGE_LADIESB => '女包',
	SHOP_LUGGAGE_FSPJ => '服饰配件',
	SHOP_LUGGAGE_WJST => '围巾手套',
	SHOP_LUGGAGE_MWSW => '棉袜丝袜',
	SHOP_LUGGAGE_MZ => '帽子',
	SHOP_LUGGAGE_ZBSS => '珠宝首饰',
	SHOP_LUGGAGE_SLZC => '手链串珠',
);

define('SHOP_CATEGORY_SPORTS', 'sports');//运动户外
define('SHOP_SPORTS_HWFS', 's1');//户外服饰
define('SHOP_SPORTS_XXYK', 's2');//休闲衣裤
define('SHOP_SPORTS_DSZB', 's3');//登山装备
define('SHOP_SPORTS_YDQC', 's4');//运动器材
define('SHOP_SPORTS_JSQC', 's5');//健身器材
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_SPORTS] = array(
	SHOP_SPORTS_HWFS => '户外服饰',
	SHOP_SPORTS_XXYK => '休闲衣裤',
	SHOP_SPORTS_DSZB => '登山装备',
	SHOP_SPORTS_YDQC => '运动器材',
	SHOP_SPORTS_JSQC => '健身器材',
);

define('SHOP_CATEGORY_NECESSITIES', 'necessities');//日用百货
define('SHOP_CATEGORY_LUE', 'n1');//略
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_NECESSITIES] = array(
	SHOP_CATEGORY_LUE => '略',
);

define('SHOP_CATEGORY_GIFT', 'gift');//礼品鲜花
define('SHOP_GIFT_XH', 'g1');//鲜花
define('SHOP_GIFT_LZ', 'g2');//绿植
define('SHOP_GIFT_PZ', 'g3');//盆栽
define('SHOP_GIFT_DRZW', 'g4');//多肉植物
define('SHOP_GIFT_LP', 'g5');//礼品
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_GIFT] = array(
	SHOP_GIFT_XH => '鲜花',
	SHOP_GIFT_LZ => '绿植',
	SHOP_GIFT_PZ => '盆栽',
	SHOP_GIFT_DRZW => '多肉植物',
	SHOP_GIFT_LP => '礼品',
);

define('SHOP_CATEGORY_RESTAURANT', 'restaurant');//餐饮外卖
define('SHOP_RESTAURANT_KCWM', 'r1');//快餐外卖
define('SHOP_RESTAURANT_XCLL', 'r2');//西餐料理
define('SHOP_RESTAURANT_CTTS', 'r3');//餐馆堂食
define('SHOP_RESTAURANT_CAFE', 'r4');//咖啡厅
define('SHOP_RESTAURANT_HG', 'r5');//火锅
define('SHOP_RESTAURANT_ZZC', 'r6');//自助餐
define('SHOP_RESTAURANT_BBQ', 'r7');//烧烤
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_RESTAURANT] = array(
	SHOP_RESTAURANT_KCWM => '快餐外卖',
	SHOP_RESTAURANT_XCLL => '西餐料理',
	SHOP_RESTAURANT_CTTS => '餐馆堂食',
	SHOP_RESTAURANT_CAFE => '咖啡厅',
	SHOP_RESTAURANT_HG => '火锅',
	SHOP_RESTAURANT_ZZC => '自助餐',
	SHOP_RESTAURANT_BBQ => '烧烤',
);

define('SHOP_CATEGORY_PLAY', 'play');//休闲娱乐
define('SHOP_PLAY_JB', 'p1');//酒吧
define('SHOP_PLAY_QB', 'p2');//清吧
define('SHOP_PLAY_KTV', 'p3');//KTV
define('SHOP_PLAY_ZLBJ', 'p4');//足疗按摩
define('SHOP_PLAY_XUWQ', 'p5');//洗浴温泉
define('SHOP_PLAY_YYWB', 'p6');//影院网吧
define('SHOP_PLAY_QGQP', 'p7');//球馆棋牌
define('SHOP_PLAY_YLYY', 'p8');//游乐游艺
define('SHOP_PLAY_ZYMS', 'p9');//桌游密室
define('SHOP_PLAY_CZNJL', 'p10');//采摘农家乐
define('SHOP_PLAY_WHSC', 'p11');//文化收藏
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_PLAY] = array(
	SHOP_PLAY_JB => '酒吧',
	SHOP_PLAY_QB => '清吧',
	SHOP_PLAY_KTV => 'KTV',
	SHOP_PLAY_ZLBJ => '足疗按摩',
	SHOP_PLAY_XUWQ => '洗浴温泉',
	SHOP_PLAY_YYWB => '影院网吧',
	SHOP_PLAY_QGQP => '球馆棋牌',
	SHOP_PLAY_YLYY => '游乐游艺',
	SHOP_PLAY_ZYMS => '桌游密室',
	SHOP_PLAY_CZNJL => '采摘农家乐',
	SHOP_PLAY_WHSC => '文化收藏',
);

define('SHOP_CATEGORY_HOTEL', 'hotel');//酒店客栈
define('SHOP_HOTEL_JD', 'h1');//酒店
define('SHOP_HOTEL_DJC', 'h2');//度假村
define('SHOP_HOTEL_KZLS', 'h3');//客栈旅舍
define('SHOP_HOTEL_LYXL', 'h4');//旅游线路
define('SHOP_HOTEL_JTPJ', 'h5');//交通票据
define('SHOP_HOTEL_JDMP', 'h6');//景点门票
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_HOTEL] = array(
	SHOP_HOTEL_JD => '酒店',
	SHOP_HOTEL_DJC => '度假村',
	SHOP_HOTEL_KZLS => '客栈旅舍',
	SHOP_HOTEL_LYXL => '旅游线路',
	SHOP_HOTEL_JTPJ => '交通票据',
	SHOP_HOTEL_JDMP => '景点门票',
);

define('SHOP_CATEGORY_WEDDING', 'wedding');//婚庆摄影
define('SHOP_WEDDING_HSSY', 'w1');//婚纱摄影
define('SHOP_WEDDING_HSLF', 'w2');//婚纱礼服
define('SHOP_WEDDING_LYQD', 'w3');//礼仪庆典
define('SHOP_WEDDING_GXXZ', 'w4');//个性写真
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_WEDDING] = array(
	SHOP_WEDDING_HSSY => '婚纱摄影',
	SHOP_WEDDING_HSLF => '婚纱礼服',
	SHOP_WEDDING_LYQD => '礼仪庆典',
	SHOP_WEDDING_GXXZ => '个性写真',
);

define('SHOP_CATEGORY_GYM', 'gym');//丽人健身
define('SHOP_GYM_MF', 'g1');//美发
define('SHOP_GYM_MJ', 'g2');//美甲
define('SHOP_GYM_MYSPA', 'g3');//美容SPA
define('SHOP_GYM_YJ', 'g4');//瑜伽
define('SHOP_GYM_JS', 'g5');//健身
define('SHOP_GYM_WD', 'g6');//舞蹈
define('SHOP_GYM_WS', 'g7');//纹身
define('SHOP_GYM_SX', 'g8');//整形
define('SHOP_GYM_QD', 'g9');//祛痘
define('SHOP_GYM_CK', 'g10');//齿科
define('SHOP_GYM_SSQT', 'g11');//瘦身纤体
define('SHOP_GYM_CHSX', 'g12');//产后塑形
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_GYM] = array(
	SHOP_GYM_MF => '美发',
	SHOP_GYM_MJ => '美甲',
	SHOP_GYM_MYSPA => '美容SPA',
	SHOP_GYM_YJ => '瑜伽',
	SHOP_GYM_JS => '健身',
	SHOP_GYM_WD => '舞蹈',
	SHOP_GYM_WS => '纹身',
	SHOP_GYM_SX => '整形',
	SHOP_GYM_QD => '祛痘',
	SHOP_GYM_CK => '齿科',
	SHOP_GYM_SSQT => '瘦身纤体',
	SHOP_GYM_CHSX => '产后塑形',
);

define('SHOP_CATEGORY_HOUSEKEEPING', 'housekeeping');//家政服务
define('SHOP_HOUSEKEEPING_BJ', 'h1');//搬家
define('SHOP_HOUSEKEEPING_GDST', 'h2');//管道疏通
define('SHOP_HOUSEKEEPING_HS', 'h3');//回收
define('SHOP_HOUSEKEEPING_ZDG', 'h4');//钟点工
define('SHOP_HOUSEKEEPING_BMYS', 'h5');//保姆/月嫂
define('SHOP_HOUSEKEEPING_QJQX', 'h6');//清洁清洗
define('SHOP_HOUSEKEEPING_GXGY', 'h7');//干洗改衣
define('SHOP_HOUSEKEEPING_ZC', 'h8');//租车
define('SHOP_HOUSEKEEPING_FWWX', 'h9');//房屋维修
define('SHOP_HOUSEKEEPING_JJWX', 'h10');//家居维修
define('SHOP_HOUSEKEEPING_JDSJWX', 'h11');//家电/手机维修
define('SHOP_HOUSEKEEPING_DNWX', 'h12');//电脑维修
define('SHOP_HOUSEKEEPING_KSHS', 'h13');//开锁换锁
define('SHOP_HOUSEKEEPING_KTWXYJ', 'h14');//空调维修/移机
define('SHOP_HOUSEKEEPING_FPZJ', 'h15');//房铺中介
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_HOUSEKEEPING] = array(
	SHOP_HOUSEKEEPING_BJ => '搬家',
	SHOP_HOUSEKEEPING_GDST => '管道疏通',
	SHOP_HOUSEKEEPING_HS => '回收',
	SHOP_HOUSEKEEPING_ZDG => '钟点工',
	SHOP_HOUSEKEEPING_BMYS => '保姆/月嫂',
	SHOP_HOUSEKEEPING_QJQX => '清洁清洗',
	SHOP_HOUSEKEEPING_GXGY => '干洗改衣',
	SHOP_HOUSEKEEPING_ZC => '租车',
	SHOP_HOUSEKEEPING_FWWX => '房屋维修',
	SHOP_HOUSEKEEPING_JJWX => '家居维修',
	SHOP_HOUSEKEEPING_JDSJWX => '家电/手机维修',
	SHOP_HOUSEKEEPING_DNWX => '电脑维修',
	SHOP_HOUSEKEEPING_KSHS => '开锁换锁',
	SHOP_HOUSEKEEPING_KTWXYJ => '空调维修/移机',
	SHOP_HOUSEKEEPING_FPZJ => '房铺中介',
);

define('SHOP_CATEGORY_TICKET', 'ticket');//票务
define('SHOP_TICKET_MP', 't1');//门票
define('SHOP_TICKET_HYK', 't2');//会员卡
define('SHOP_TICKET_YHQ', 't3');//优惠券
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_TICKET] = array(
	SHOP_TICKET_MP => '门票',
	SHOP_TICKET_HYK => '会员卡',
	SHOP_TICKET_YHQ => '优惠券',
);

define('SHOP_CATEGORY_SEA', 'sea');//海外购
define('SHOP_SEA_LUE', 's1');//略
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_SEA] = array(
	SHOP_SEA_LUE => '略',
);

define('SHOP_CATEGORY_MEN_SHOES', 'menshoes');//男鞋
define('SHOP_MEN_SHOES_LUE', 'ms1');//略
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_MEN_SHOES] = array(
	SHOP_MEN_SHOES_LUE => '略',
);

define('SHOP_CATEGORY_WOMEN_SHOES', 'womenshoes');//女鞋
define('SHOP_WOMEN_SHOES_LUE', 'ws1');//略
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_WOMEN_SHOES] = array(
	SHOP_WOMEN_SHOES_LUE => '略',
);

define('SHOP_CATEGORY_NEWS', 'news');//媒体服务
define('SHOP_NEWS_ZZ', 'n1');//杂志
define('SHOP_NEWS_BK', 'n2');//报刊
define('SHOP_NEWS_CBJG', 'n3');//出版机构
define('SHOP_NEWS_DST', 'n4');//电视台
define('SHOP_NEWS_DT', 'n5');//电台
define('SHOP_NEWS_ZMTDXMTYW', 'n6');//自媒体的新媒体业务
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_NEWS] = array(
	SHOP_NEWS_ZZ => '杂志',
	SHOP_NEWS_BK => '报刊',
	SHOP_NEWS_CBJG => '出版机构',
	SHOP_NEWS_DST => '电视台',
	SHOP_NEWS_DT => '电台',
	SHOP_NEWS_ZMTDXMTYW => '自媒体的新媒体业务',
);

define('SHOP_CATEGORY_MEDICAL', 'medical');//医疗健康
define('SHOP_MEDICAL_JSYP', 'm1');//计生用品
define('SHOP_MEDICAL_YXYJJHLY', 'm2');//隐形眼镜及护理液
define('SHOP_MEDICAL_JTCBYP', 'm3');//家庭常备药品
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_MEDICAL] = array(
	SHOP_MEDICAL_JSYP => '计生用品',
	SHOP_MEDICAL_YXYJJHLY => '隐形眼镜及护理液',
	SHOP_MEDICAL_JTCBYP => '家庭常备药品',
);

define('SHOP_CATEGORY_BUILDING', 'building');//家庭建材
define('SHOP_BUILDING_ZXCL', 'b1');//装修材料
define('SHOP_BUILDING_JJDJ', 'b2');//家具灯具
define('SHOP_BUILDING_DBDZ', 'b3');//地板地砖
define('SHOP_BUILDING_JCDD', 'b4');//集成吊顶
define('SHOP_BUILDING_SDGX', 'b5');//水电管线
define('SHOP_BUILDING_WJPJ', 'b6');//五金配件
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_BUILDING] = array(
	SHOP_BUILDING_ZXCL => '装修材料',
	SHOP_BUILDING_JJDJ => '家具灯具',
	SHOP_BUILDING_DBDZ => '地板地砖',
	SHOP_BUILDING_JCDD => '集成吊顶',
	SHOP_BUILDING_SDGX => '水电管线',
	SHOP_BUILDING_WJPJ => '五金配件',
);

define('SHOP_CATEGORY_CAR', 'car');//汽车养护
define('SHOP_CAR_QCPJ', 'c1');//汽车配件
define('SHOP_CAR_CJDH', 'c2');//车载导航
define('SHOP_CAR_AQJB', 'c3');//安全警报
define('SHOP_CAR_DCLD', 'c4');//倒车雷达
define('SHOP_CAR_YHHYTC', 'c5');//养护会员套餐
define('SHOP_CAR_CTCL', 'c6');//车贴车蜡
define('SHOP_CAR_TJJ', 'c7');//添加剂
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_CAR] = array(
	SHOP_CAR_QCPJ => '汽车配件',
	SHOP_CAR_CJDH => '车载导航',
	SHOP_CAR_AQJB => '安全警报',
	SHOP_CAR_DCLD => '倒车雷达',
	SHOP_CAR_YHHYTC => '养护会员套餐',
	SHOP_CAR_CTCL => '车贴车蜡',
	SHOP_CAR_TJJ => '添加剂',
);

define('SHOP_CATEGORY_EDUCATION', 'education');//培训教育
define('SHOP_EDUCATION_YYPX', 'e1');//语言培训
define('SHOP_EDUCATION_ZXXYEJY', 'e2');//中小学/幼儿教育
define('SHOP_EDUCATION_DXKY', 'e3');//大学/考研
define('SHOP_EDUCATION_ITPX', 'e4');//IT培训
define('SHOP_EDUCATION_GLPX', 'e5');//管理培训
define('SHOP_EDUCATION_JNPX', 'e6');//技能培训
define('SHOP_EDUCATION_WTYS', 'e7');//文体艺术
define('SHOP_EDUCATION_XQPX', 'e8');//兴趣培训
define('SHOP_EDUCATION_LCPX', 'e9');//理财培训
define('SHOP_EDUCATION_SHJN', 'e10');//生活技能
define('SHOP_EDUCATION_YM', 'e11');//移民
define('SHOP_EDUCATION_LX', 'e12');//留学
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_EDUCATION] = array(
	SHOP_EDUCATION_YYPX => '语言培训',
	SHOP_EDUCATION_ZXXYEJY => '中小学/幼儿教育',
	SHOP_EDUCATION_DXKY => '大学/考研',
	SHOP_EDUCATION_ITPX => 'IT培训',
	SHOP_EDUCATION_GLPX => '管理培训',
	SHOP_EDUCATION_JNPX => '技能培训',
	SHOP_EDUCATION_WTYS => '文体艺术',
	SHOP_EDUCATION_XQPX => '兴趣培训',
	SHOP_EDUCATION_LCPX => '理财培训',
	SHOP_EDUCATION_SHJN => '生活技能',
	SHOP_EDUCATION_YM => '移民',
	SHOP_EDUCATION_LX => '留学',
);

define('SHOP_CATEGORY_WATCHES', 'watches');//钟表眼镜
define('SHOP_WATCHES_SB', 'wg1');//手表
define('SHOP_WATCHES_SZ', 'wg2');//时钟
define('SHOP_WATCHES_GXPJ', 'wg3');//光学配镜
define('SHOP_WATCHES_YJ', 'wg4');//眼镜
define('SHOP_WATCHES_HJYJ', 'wg5');//火机烟具
define('SHOP_WATCHES_BXJH', 'wg6');//便携酒壶
define('SHOP_WATCHES_RSJD', 'wg7');//瑞士军刀
define('SHOP_WATCHES_YJPJ', 'wg8');//眼镜配件
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_WATCHES] = array(
	SHOP_WATCHES_SB => '手表',
	SHOP_WATCHES_SZ => '时钟',
	SHOP_WATCHES_GXPJ => '光学配镜',
	SHOP_WATCHES_YJ => '眼镜',
	SHOP_WATCHES_HJYJ => '火机烟具',
	SHOP_WATCHES_BXJH => '便携酒壶',
	SHOP_WATCHES_RSJD => '瑞士军刀',
	SHOP_WATCHES_YJPJ => '眼镜配件',
);

define('SHOP_CATEGORY_PET', 'pet');//宠物
define('SHOP_PET_CWSP', 'p1');//宠物食品
define('SHOP_PET_CWWJ', 'p2');//宠物玩具
define('SHOP_PET_CWYP', 'p3');//宠物用品
define('SHOP_PET_CWMY', 'p4');//宠物美容
define('SHOP_PET_YLBZ', 'p5');//医疗保障
define('SHOP_PET_CNSZ', 'p6');//虫鸟水族
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_PET] = array(
	SHOP_PET_CWSP => '宠物食品',
	SHOP_PET_CWWJ => '宠物玩具',
	SHOP_PET_CWYP => '宠物用品',
	SHOP_PET_CWMY => '宠物美容',
	SHOP_PET_YLBZ => '医疗保障',
	SHOP_PET_CNSZ => '虫鸟水族',
);

define('SHOP_CATEGORY_OTHER', 'other');//其他
define('SHOP_OTHER_LUE', 'l1');//略
$GLOBALS['CATEGORY_TWO'][SHOP_CATEGORY_OTHER] = array(
	SHOP_OTHER_LUE => '略',
);

$GLOBALS['CATEGORY_ONE'] = array(
		SHOP_CATEGORY_FOOD => "食品",
		SHOP_CATEGORY_BEAUTY => "美妆",
		SHOP_CATEGORY_LADIES => "女装",
		SHOP_CATEGORY_GENTLEMAN => "男装",
		SHOP_CATEGORY_PARENTING => "亲子",
		SHOP_CATEGORY_DIGITAL => "数码电器",
		SHOP_CATEGORY_HOME => "家居家纺",
		SHOP_CATEGORY_LUGGAGE => "箱包配饰",
		SHOP_CATEGORY_SPORTS => "运动户外",
		SHOP_CATEGORY_NECESSITIES => "日用百货",
		SHOP_CATEGORY_GIFT => "礼品鲜花",
		SHOP_CATEGORY_RESTAURANT => "餐饮外卖",
		SHOP_CATEGORY_PLAY => "休闲娱乐",
		SHOP_CATEGORY_HOTEL => "酒店客栈",
		SHOP_CATEGORY_WEDDING => "婚庆摄影",
		SHOP_CATEGORY_GYM => "丽人健身",
		SHOP_CATEGORY_HOUSEKEEPING => "家政服务",
		SHOP_CATEGORY_TICKET => "票务",
		SHOP_CATEGORY_SEA => "海外购",
		SHOP_CATEGORY_MEN_SHOES => "男鞋",
		SHOP_CATEGORY_WOMEN_SHOES => "女鞋",
		SHOP_CATEGORY_NEWS => "媒体服务",
		SHOP_CATEGORY_MEDICAL => "医疗健康",
		SHOP_CATEGORY_BUILDING => "家庭建材",
		SHOP_CATEGORY_CAR => "汽车养护",
		SHOP_CATEGORY_EDUCATION => "培训教育",
		SHOP_CATEGORY_WATCHES => "钟表眼镜",
		SHOP_CATEGORY_PET => "宠物",
		SHOP_CATEGORY_OTHER => "其他",
);

define('SHOP_PRODUCT_TYPE_OBJECT', 1);//实体物品
define('SHOP_PRODUCT_TYPE_VIRTAL', 2);//虚拟物品
$GLOBALS['SHOP_PRODUCT_TYPE'] = array(
		SHOP_PRODUCT_TYPE_OBJECT=>'实体商品',
		SHOP_PRODUCT_TYPE_VIRTAL=>'虚拟商品',
);

define('SHOP_PRODUCT_THIRED_TIANSHI', 1);//天时
define('SHOP_PRODUCT_THIRED_ZHIYOUBAO', 2);//智游宝
$GLOBALS['SHOP_PRODUCT_THIRED'] = array(
		SHOP_PRODUCT_THIRED_TIANSHI=>'天时',
		SHOP_PRODUCT_THIRED_ZHIYOUBAO=>'智游宝',
);

define('SHOP_FREIGHT_TYPE_UNITE', 1);//统一运费
define('SHOP_FREIGHT_TYPE_MODEL', 2);//运费模板
$GLOBALS['SHOP_FREIGHT_TYPE'] = array(
		SHOP_FREIGHT_TYPE_UNITE=>'统一运费',
		SHOP_FREIGHT_TYPE_MODEL=>'运费模板',
);

define('SHOP_INVOICE_YES', 1);//有发票
define('SHOP_INVOICE_NO', 2);//无发票
$GLOBALS['SHOP_INVOICE_TYPE'] = array(
		SHOP_INVOICE_YES=>'有发票',
		SHOP_INVOICE_NO=>'无发票',
);

define('SHOP_STANDARD_SAVE_OLD', 1);//保存到已有模板
define('SHOP_STANDARD_SAVE_NEW', 2);//创建为新模板
$GLOBALS['SHOP_STANDARD_SAVE'] = array(
		SHOP_STANDARD_SAVE_OLD=>'保存到已有模板',
		SHOP_STANDARD_SAVE_NEW=>'创建为新模板',
);



//商场商品状态
define('SHOP_PRODUCT_STATUS_DOWN', '1');//下架
define('SHOP_PRODUCT_STATUS_UP', '2');//上架
define('SHOP_PRODUCT_STATUS_SOLDOUT', 3);//已售罄
define('SHOP_PRODUCT_STATUS_ALL','');//全部商品
$GLOBALS['SHOP_PRODUCT_STATUS'] = array(
		SHOP_PRODUCT_STATUS_UP => '已上架的商品',                
		SHOP_PRODUCT_STATUS_SOLDOUT => '已售罄的商品',
		SHOP_PRODUCT_STATUS_DOWN => '已下架的商品',   
                SHOP_PRODUCT_STATUS_ALL => '全部商品',
);

//是否购物车订单
define('IS_CART_YES', '1');//购物车
define('IS_CART_NO', '2');//非购物车

//页面是否显示库存
define('IF_SHOW_YES', '1');//显示
define('IF_SHOW_NO', '2');//不显示

/**********接口编码*************/
define("ORDER_UNPAID", "ORDER_UNPAID"); //待付款
define("ORDER_PAID", "ORDER_PAID"); //已付款
define("ORDER_REFUND", "ORDER_REFUND"); //已退款
define("ORDER_PART_REFUND", "ORDER_PART_REFUND"); //部分退款
define("ORDER_HANDLE_REFUND", "ORDER_HANDLE_REFUND"); //退款处理中
define("ORDER_REVOKE", "ORDER_REVOKE"); //已撤销

define("REQUEST_ORDER_STATUS_UNPAID", "1"); //待付款
define("REQUEST_ORDER_STATUS_PAID", "2"); //已付款
define("REQUEST_ORDER_STATUS_REFUND", "3"); //已退款
define("REQUEST_ORDER_STATUS_PART_REFUND", "4"); //部分退款
define("REQUEST_ORDER_STATUS_HANDLE_REFUND", "5"); //退款处理中
define("REQUEST_ORDER_STATUS_REVOKE", "6"); //已撤销

define("BALANCE_PAY_CONFIRMING", "BALANCE_PAY_CONFIRMING"); //等待储值支付确认
define("BALANCE_PAY_CONFIRMED", "BALANCE_PAY_CONFIRMED"); //储值支付已确认
define("BALANCE_PAY_NO_CONFIRM", "BALANCE_PAY_NO_CONFIRM"); //储值支付无需确认

define('COUPON_TYPE_CODE_REDENVELOPE', 'COUPON_REDENVELOPE');//红包
define('COUPON_TYPE_CODE_CASH', 'COUPON_CASH');//代金券
define('COUPON_TYPE_CODE_DISCOUNT', 'COUPON_DISCOUNT');//折扣券
define('COUPON_TYPE_CODE_EXCHANGE', 'COUPON_EXCHANGE');//兑换券

define("REQUEST_RESERVE_STATUS_WAIT", "1"); //待确认
define("REQUEST_RESERVE_STATUS_ACCEPT", "2"); //已接单
define("REQUEST_RESERVE_STATUS_REFUSE", "3"); //已拒单
define("REQUEST_RESERVE_STATUS_ARRIVE", "4"); //已到店
define("REQUEST_RESERVE_STATUS_CANCEL", "5"); //已取消

define('RESERVE_WAIT', 'RESERVE_WAIT');//待确认
define('RESERVE_ACCEPT', 'RESERVE_ACCEPT');//已接单
define('RESERVE_REFUSE', 'RESERVE_REFUSE');//已拒单
define('RESERVE_ARRIVE', 'RESERVE_ARRIVE');//已到店
define('RESERVE_CANCEL', 'RESERVE_CANCEL');//已取消

/**********证件持有人类型*************/
define("CERTIFICATE_HOLDER_TYPE_LEGAL", "1");//法人
define("CERTIFICATE_HOLDER_TYPE_ATTN", "2");//经办人
$GLOBALS['CERTIFICATE_HOLDER_TYPE'] = array(
		CERTIFICATE_HOLDER_TYPE_LEGAL => "法人",
		CERTIFICATE_HOLDER_TYPE_ATTN => "经办人",
	);


/**********证件类型*************/
define("CERTIFICATE_TYPE_ID_CARD", "1");//身份证
define("CERTIFICATE_TYPE_PASSPORT", "2");//护照
$GLOBALS['CERTIFICATE_TYPE'] = array(
		CERTIFICATE_TYPE_ID_CARD => "身份证",
		CERTIFICATE_TYPE_PASSPORT => "护照",
	);

/**********账户类型*************/
define("BANK_ACCOUNT_TYPE_DUIGONG", "1");//对公账号
define("BANK_ACCOUNT_TYPE_FAREN", "2");//法人账号
$GLOBALS['BANK_ACCOUNT_TYPE'] = array(
		BANK_ACCOUNT_TYPE_DUIGONG => "对公账号",
		BANK_ACCOUNT_TYPE_FAREN => "法人账号",
);

/**********开户银行*************/
define("BANK_ZSYH", "1");//招商银行
define("BANK_ZGGSYH", "2");//中国工商银行
define("BANK_ZGJSYH", "3");//中国建设银行
define("BANK_PFYH", "4");//浦发银行
define("BANK_ZGNYYH", "5");//中国农业银行
define("BANK_ZGMSYH", "6");//中国民生银行
define("BANK_PAYH", "7");//平安银行
define("BANK_XYYH", "8");//兴业银行
define("BANK_JTYH", "9");//交通银行
define("BANK_ZXYH", "10");//中信银行
define("BANK_ZGGDYH", "11");//中国光大银行
define("BANK_NCHZXYS", "12");//农村合作信用社
define("BANK_SHYH", "13");//上海银行
define("BANK_HXYH", "14");//华夏银行
define("BANK_ZGYH", "15");//中国银行
define("BANK_GFYH", "16");//广发银行
define("BANK_BJYH", "17");//北京银行
define("BANK_YZCXYH", "18");//邮政储蓄银行
define("BANK_NJYH", "19");//南京银行
define("BANK_NBYH", "20");//宁波银行
define("BANK_SHNSYH", "21");//上海农商银行
define("BANK_HRYH", "22");//华润银行
define("BANK_JSYH", "23");//江苏银行
define("BANK_GDNYYH", "24");//广东南粤银行
define("BANK_QTYH", "25");//其他银行
$GLOBALS['BANK'] = array(
	BANK_ZSYH => "招商银行",
	BANK_ZGGSYH => "中国工商银行",
	BANK_ZGJSYH => "中国建设银行",
	BANK_PFYH => "浦发银行",
	BANK_ZGNYYH => "中国农业银行",
	BANK_ZGMSYH => "中国民生银行",
	BANK_PAYH => "平安银行",
	BANK_XYYH => "兴业银行",
	BANK_JTYH => "交通银行",
	BANK_ZXYH => "中信银行",
	BANK_ZGGDYH => "中国光大银行",
	BANK_NCHZXYS => "农村合作信用社",
	BANK_SHYH => "上海银行",
	BANK_HXYH => "华夏银行",
	BANK_ZGYH => "中国银行",
	BANK_GFYH => "广发银行",
	BANK_BJYH => "北京银行",
	BANK_YZCXYH => "邮政储蓄银行",
	BANK_NJYH => "南京银行",
	BANK_NBYH => "宁波银行",
	BANK_SHNSYH => "上海农商银行",
	BANK_HRYH => "华润银行",
	BANK_JSYH => "江苏银行",
	BANK_GDNYYH => "广东南粤银行",
	BANK_QTYH => "其他银行",
);
/**********证件期限*************/
define("LONG_TERM","2");//长期


/**********促销活动*************/
/**********是否显示奖品数量************/
define("SHOW_PRIZE_NUM","2");//显示


/**********促销活动类型************/
define("PROMOTIONS_TYPE_TURNTABLE","1");//大转盘
define("PROMOTIONS_TYPE_SCRATCH","2");//刮刮卡
/**********促销活动奖项************/
define('PRIZE_TYPE_FIRST', 1); //一等奖
define('PRIZE_TYPE_SECOND', 2); //二等奖
define('PRIZE_TYPE_THIRD', 3); //三等奖
define('PRIZE_TYPE_FORTH', 4); //四等奖
define('PRIZE_TYPE_FIFTH', 5); //五等奖
define('PRIZE_TYPE_NONE', 6); //未中奖
$GLOBALS['PRIZE_TYPE'] = array(
		PRIZE_TYPE_FIRST => '一等奖',
		PRIZE_TYPE_SECOND => '二等奖',
		PRIZE_TYPE_THIRD => '三等奖',
		PRIZE_TYPE_FORTH => '四等奖',
		PRIZE_TYPE_FIFTH => '五等奖',
		PRIZE_TYPE_NONE => '未中奖',
);

/***********管理员***************/
//角色
define("MANAGER_ROLE_SP", '1');//超级管理员
define("MANAGER_ROLE_CW", '2');//财务
define("MANAGER_ROLE_YY", '3');//运营
//权限
// define("MANAGER_RIGHT_ALL", '1');//所有权限
// define("MANAGER_RIGHT_PART", '2');//部分权限

/************管理员权限**********/
$GLOBALS['MANAGER_LIMIT'] = array(
		'100'=>array(
			'text'=>'门店管理',
			'route' => 'index/store'
        ),
		'101'=>array(
			'text'=>'财务管理',
			'route' => 'index/finance'
		),
		'107'=>array(
				'text'=>'CRM管理',
				'route' => 'index/crm'
		),
		'102'=>array(
			'text'=>'营销管理',
			'route' => 'index/common'
		),
		
		'109'=>array(
				'text'=>'商城管理',
				'route' => 'index/mall'
		),
		'110'=>array(
				'text'=>'应用市场',
				'route' => 'index/appmarket'
		),
		'108'=>array(
				'text'=>'渠道管理',
				'route' => 'index/channel'
		),
// 		'103'=>array(
// 			'text'=>'服务窗管理',
// 			'route' => 'index/alipay'
// 		),
// 		'104'=>array(
// 			'text'=>'公众号管理',
// 			'route' => 'index/wechat'
// 		),
		'105'=>array(
			'text'=>'统计管理',
			'route' => 'index/statistics'
		),
		'106'=>array(
			'text'=>'系统设置',
			'route' => 'index/install'
		),
);



//玩券账户角色(1:商户       2：管理员)
define("WQ_ROLE_MERCHANT",1);
define("WQ_ROLE_MANAGER",2);

/***************************问卷调查*************************************/
//问卷调查答案
define('IS_ANSWER_YES', '1');//是
define('IS_ANSWER_NO', '2');//否

/***************************在线签约*************************************/
////支付方式
//define('TYPE_IS_ALI_PAY', '1');//支付宝
//define('TYPE_IS_BANK_ACCOUNT', '2');//账号打款
//
////发票类型
//define('TYPE_IS_ORDINARY_INVOICE', '1');//普通发票
//define('TYPE_IS_VAT_INVOICE', '2');//增值税发票
//
////激活码状态
//define('CODE_STATUS_NOT_USED', '1');//未使用
//define('CODE_STATUS_USED', '2');//已使用
//
////合同状态
//define('CONTRACT_STATUS_NO','1');//未同意
//define('CONTRACT_STATUS_YES','2');//已同意
//
////合作方
//define('PARTNER_TYPE_SHST','1');//上海世途
//define('PARTNER_TYPE_ZJWQ','2');//浙江玩券
//
////加盟类型
//define('LEAGUE_TYPE_HAVE_RESOURCES','1');//有资源加盟
//define('LEAGUE_TYPE_NO_RESOURCES','2');//无资源加盟
//$GLOBALS['LEAGUE_TYPE'] = array(
//    LEAGUE_TYPE_HAVE_RESOURCES => '有资源加盟',
//    LEAGUE_TYPE_NO_RESOURCES => '无资源加盟',
//);
//
////门店数量
//define('STORE_NUM_LESS_THAN_100','1');//少于100家
//define('STORE_NUM_101_200','2');//101~200家
//define('STORE_NUM_201_300','3');//201~300家
//define('STORE_NUM_301_400','4');//301~400家
//define('STORE_NUM_401_500','5');//401~500家
//define('STORE_NUM_501_600','6');//501~600家
//define('STORE_NUM_601_700','7');//601~700家
//define('STORE_NUM_701_800','8');//701~800家
//define('STORE_NUM_801_900','9');//801~900家
//define('STORE_NUM_901_1000','10');//901~1000家
//define('STORE_NUM_MORE_THAN_1000','11');//大于1000家
//
////加盟费类型
//define('LEAGUE_FEE_TYPE_ONE','1');//系统计算
//define('LEAGUE_FEE_TYPE_TWO','2');//自定义

