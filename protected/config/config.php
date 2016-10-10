<?php
define('WEB_DOMAIN','51wanquan.com');//网站域
define('WWW_DOMAIN','http://www.'.WEB_DOMAIN);//主站域
define('MGR_DOMAIN','http://wqmgr.'.WEB_DOMAIN);//后台域
define('FX_DOMAIN','http://fx.'.WEB_DOMAIN);//分销域
define('GJ_DOMAIN','http://gj.'.WEB_DOMAIN);//管家域
define('SYT_DOMAIN','http://syt.'.WEB_DOMAIN);//收银台域
define('WAP_DOMAIN','http://wap.'.WEB_DOMAIN);//wap域

define('USER_DOMAIN', 'http://www.'.WEB_DOMAIN.'uCenter/user');//用户域
//define('USER_DOMAIN_COUPONS', 'http://www.'.WEB_DOMAIN.'uCenter/user');//用户域

// define('UPLOAD_TO_PATH','http://upload.'.WEB_DOMAIN.'/upload.php');//上传附件路径
// define('IMG_BASE_PATH','http://upload.'.WEB_DOMAIN.'/images/');//图片存放基本路径径
define('UPLOAD_TO_PATH','http://localhost/kuaiguanjia/upload'.'/upload.php');//上传附件路径
define('UPLOAD_FILE_TO_PATH','http://localhost/kuaiguanjia/upload'.'/uploadfile.php');//上传PEM附件路径
define('IMG_BASE_PATH','http://localhost/kuaiguanjia/upload'.'/images/');//图片存放基本路径径
define('CERT_BASE_PATH','http://localhost/kuaiguanjia/upload'.'/cert/');//PEM存放基本路径径

define('QRCODE_IMG_URL','http://upload.51wanquan.com'.'/qrcode/');//二维码图片地址
define('UPLOAD_IMG_TYPE','*.jpg;*.gif;*.png;*.bmp;*.jpeg');
define('UPLOAD_CERT_FILE_TYPE','apiclient_cert.pem');
define('UPLOAD_KEY_FILE_TYPE','apiclient_key.pem');
define('IMG_FX_FOLDER','fx');  //分销
define('IMG_FX_LIST',IMG_BASE_PATH.IMG_FX_FOLDER.'/source/');
define('IMG_GJ_FOLDER','gj');  //玩券管家
define('IMG_GJ_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/source/');    //原图路径
define('IMG_GJ_600_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/600/');   //重画后的多图文 二级图文路径
define('IMG_GJ_300_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/300/');   //重画后的多图文 二级图文路径
define('IMG_GJ_250_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/250/');   //重画后的多图文 二级图文路径
define('IMG_GJ_150_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/150/');   //重画后的多图文 二级图文路径
define('IMG_GJ_125_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/125/');   //重画后的多图文 二级图文路径
define('IMG_GJ_80_LIST',IMG_BASE_PATH.IMG_GJ_FOLDER.'/80/');   //重画后的多图文 二级图文路径
define('IMG_GJ_THUMB_600','600@600'); //定义图片大小
define('IMG_GJ_THUMB_150','150@150'); //定义图片大小
define('IMG_GJ_THUMB_NAV','600@600,300@300,250@250,150@150'); //定义导航图片大小
define('IMG_GJ_THUMB_PRODUCT','600@600,125@125,80@80');
define('PEM_GJ_LIST',CERT_BASE_PATH);

define('UPLOAD_SYSTEM_PATH', dirname(dirname(dirname(dirname(__FILE__)))).'/upload/'); //upload系统文件路径


//支付接口回调url
/*****************支付宝*****************/
//玩券管家
define('ALIPAY_WQ_SYNNOTIFY','http://localhost/kuaiguanjia/proj/index.php/aCenter/Notify/SynNotify');//同步
define('ALIPAY_WQ_ASYNOTIFY',FX_DOMAIN.'/aCenter/Notify/AsyNotify');//异步
//合作商
define('ALIPAY_AO_SYNNOTIFY',FX_DOMAIN.'/aCenter/Notify/AgentSynNotify');//同步
define('ALIPAY_AO_ASYNOTIFY',FX_DOMAIN.'/aCenter/Notify/AgentAsyNotify');//异步

//短信
define('ALIPAY_DX_SYNNOTIFY',GJ_DOMAIN.'/mCenter/Notify/SynNotify');//同步
define('ALIPAY_DX_ASYNOTIFY',GJ_DOMAIN.'/mCenter/Notify/AsyNotify');//异步

//微信公众号
define('INTERFACE_URL', 'http://gj.51wanquan.com/WechatGateway?account=');//接口URL

//支付宝服务窗
//开发者网关
define('DEVELOPER_GATEWAY', 'http://gj.51wanquan.com/mCenter/gateway?account=');
//开发者公钥
define('DEVELOPER_PUBLIC_KEY', 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDdJAQqGm0tHaMs0cgHl29N3gFv9aSsCcKFcK+edI4OQFl0iLt6U4In/st9XXJMQjN2Ltun6JsD3cHEx1iNmE26H2Z+C/AU6usaqnLQwmQnAhvik7XE/wkHAhcNRq55qCm6Xt48yrmE6hkO5NH2y6DQIIdiaYC5XhKNqWb7tezLJQIDAQAB');
//Wap支付
define('WAPPAY_WQ_SYNNOTIFY',WAP_DOMAIN.'/uCenter/Notify/SynNotify');//支付宝同步
define('WAPPAY_WQ_ASYNOTIFY',WAP_DOMAIN.'/uCenter/Notify/AsyNotify');//支付宝异步
//wap退款
define('ACTIVIST_ALIPAY_SYNNOTYFY',GJ_DOMAIN.'/mCenter/Notify/NotifyUrl');//支付宝退款异步
define('WAPPAY_WQ_WECHAT_ASYNOTIFY',WAP_DOMAIN.'/mall/Notify/WxPayNotify');//微信异步

//收银台
define('ALIPAY_SYT_SYNNOTIFY',SYT_DOMAIN.'/syt/Notify/SynNotifyAli');//支付宝同步
define('ALIPAY_SYT_ASYNOTIFY',SYT_DOMAIN.'/syt/Notify/AsyNotifyAli');//支付宝异步
define('WXPAY_SYT_SYNNOTIFY',SYT_DOMAIN.'/syt/Notify/SynNotifyWx');//微信同步
define('WXPAY_SYT_ASYNOTIFY',SYT_DOMAIN.'/syt/Notify/AsyNotifyWx');//微信异步

//省市区xml
define('CITY_XML','http://localhost/kuaiguanjia/proj/protected/extensions/city');
?>