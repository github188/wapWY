<?php
/*	create by gulei
 * 分销常量表 
 * 规则：
 * 1、常量必须加注释，并且需要标明添加者和添加日期还有用处
 * 2、命名规范带上同意前缀以作区分
 * 3、需要和管理员报备自己添加的常量
 * */

/*****************商户*************************/
//玩券商户类型 企业、个体工商户、事业单位 by gulei 2016-1-14
define('MERCHANT_TYPE_COMPANY', '1');//企业
define('MERCHANT_TYPE_SELF_EMPLOYED', '2');//个体工商户
define('MERCHANT_TYPE_COMPANY_GOVERNMENT', '3');//事业单位
$GLOBALS['__MERCHANT_VERIFY'] = array(
		MERCHANT_TYPE_COMPANY => '企业',
		MERCHANT_TYPE_SELF_EMPLOYED => '个体工商户',
		MERCHANT_TYPE_COMPANY_GOVERNMENT => '事业单位'
);

//玩券商户审核状态 待审核、驳回、审核通过 by gulei 2016-1-14
define('MERCHANT_VERIFY_STATUS_NUAUTH', '1');//未认证
define('MERCHANT_VERIFY_STATUS_WAIT', '2');//待审核
define('MERCHANT_VERIFY_STATUS_REJECTIT', '3');//驳回
define('MERCHANT_VERIFY_STATUS_AUTH', '4');//已认证
$GLOBALS['__MERCHANT_VERIFY_STATUS'] = array(
		MERCHANT_VERIFY_STATUS_NUAUTH => '未认证',
		MERCHANT_VERIFY_STATUS_WAIT => '待审核',
		MERCHANT_VERIFY_STATUS_REJECTIT => '驳回',
		MERCHANT_VERIFY_STATUS_AUTH => '已认证'
);
/**************************************服务商************************************************/
//服务商分销品台是否显示支付宝1.0产物
define('AGENT_IF_SHOW_OLD_SHOW', '1');//显示
define('AGENT_IF_SHOW_OLD_HIDE', '2');//隐藏

//合作方
define('PARTNER_TYPE_SHST','1');//上海世途
define('PARTNER_TYPE_ZJWQ','2');//浙江玩券

//加盟费类型
define('LEAGUE_FEE_TYPE_DEFINE','1');//系统计算
define('LEAGUE_FEE_TYPE_CUSTOM','2');//自定义

//加盟类型
define('LEAGUE_TYPE_HAVE_RESOURCES','1');//有资源加盟
define('LEAGUE_TYPE_NO_RESOURCES','2');//无资源加盟
$GLOBALS['LEAGUE_TYPE'] = array(
    LEAGUE_TYPE_HAVE_RESOURCES => '有资源加盟',
    LEAGUE_TYPE_NO_RESOURCES => '无资源加盟',
);

//门店数量
define('STORE_NUM_LESS_THAN_100','1');//少于100家
define('STORE_NUM_101_200','2');//101~200家
define('STORE_NUM_201_300','3');//201~300家
define('STORE_NUM_301_400','4');//301~400家
define('STORE_NUM_401_500','5');//401~500家
define('STORE_NUM_501_600','6');//501~600家
define('STORE_NUM_601_700','7');//601~700家
define('STORE_NUM_701_800','8');//701~800家
define('STORE_NUM_801_900','9');//801~900家
define('STORE_NUM_901_1000','10');//901~1000家
define('STORE_NUM_MORE_THAN_1000','11');//大于1000家



//支付方式
define('PAY_TYPE_ALIPAY', '1');//支付宝
define('PAY_TYPE_BANKACCOUNT', '2');//账号打款

//发票类型
define('INVOICE_TYPE_ORDINARY_INVOICE', '1');//普通发票
define('INVOICE_TYPE_VAT_INVOICE', '2');//增值税发票

//激活码状态
define('CODE_STATUS_NOT_USED', '1');//未使用
define('CODE_STATUS_USED', '2');//已使用
//define('ACTIVATION_CODE_STATUS_NOT_USED', '1');//未使用
//define('ACTIVATION_CODE_STATUS_USED', '2');//已使用

//合同状态
define('AGENT_CONTRACT_STATUS_DISAGREE','1');//未同意
define('AGENT_CONTRACT_STATUS_AGREE','2');//已同意



/*****************************************************************************/


















