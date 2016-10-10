<?php
/*	create by gulei
 * 地址常量表 主要是第三方回调地址或第三方链接
* 规则：
* 1、常量必须加注释，并且需要标明添加者和添加日期还有用处
* 2、命名规范带上同意前缀以作区分
* 3、需要和管理员报备自己添加的常量
* */

/*************************************支付宝************************************************/
//支付宝授权回调链接by gulei 2016-1-14
define('ALIPAY_AUTH_REDIRECT_URI', GJ_DOMAIN.'/mCenter/gateway/alipayAuth');//支付宝授权回调链接

//支付宝授权链接by gulei 2016-1-14
define('ALIPAY_AUTH_URI', 'https://openauth.alipay.com/oauth2/appToAppAuth.htm');//支付宝2.0授权链接

//支付宝门店审核回调url by gulei 2016-1-14
define('ALIPAY_STORE_AUDIT_REDIRECT_URI', GJ_DOMAIN.'/mCenter/gateway/alipayAudit');//门店审核回调url






/****************************************微信********************************************************/
//微信图片上传路径
define('WECHAT_UPLOAD_IMG_PATH', UPLOAD_SYSTEM_PATH.'images/gj/source/');
//微信支付
define('WXPAY_QRCODE_URL',WAP_DOMAIN.'/syt/wap/storeWxpay'); //微信支付-扫码支付地址

/***********************************东钱湖商城*************************************************/
//商城第三方同步数据接口   天时接口
define('TIANSHI_SHOP_API', 440);//440


/****************************************优惠券*******************************************************/
//优惠券链接
define('COUPON_RECEIVE_URL', WAP_DOMAIN.'/uCenter/coupon/newGetCouponOne');











