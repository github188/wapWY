<?php
/*	create by gulei
 * 管家常量表
* 规则：
* 1、常量必须加注释，并且需要标明添加者和添加日期还有用处
* 2、命名规范带上同意前缀以作区分
* 3、需要和管理员报备自己添加的常量
* */

/**************门店*****************/
//门店同步支付宝口碑门店类型  by gulei 2016-1-14
define('STORE_ALIPAY_SYNC_TYPE_SYNC', '1');//同步创建口碑门店
define('STORE_ALIPAY_SYNC_TYPE_RELATION', '2');//关联已有口碑门店
define('STORE_ALIPAY_SYNC_TYPE_NOSYNC_NO_RELATION', '3');//不同步创建也不关联已有

//门店同步口碑门店审核状态 by gulei 2016-1-14
define('STORE_ALIPAY_SYNC_STATUS_NONE', '1');//无审核
define('STORE_ALIPAY_SYNC_STATUS_AUDITING', '2');//审核中
define('STORE_ALIPAY_SYNC_STATUS_REJECT', '3');//审核驳回
define('STORE_ALIPAY_SYNC_STATUS_PASS', '4');//审核通过
$GLOBALS['__STORE_ALIPAY_SYNC_STATUS'] = array(
		STORE_ALIPAY_SYNC_STATUS_NONE => '无审核',
		STORE_ALIPAY_SYNC_STATUS_AUDITING => '审核中',
		STORE_ALIPAY_SYNC_STATUS_REJECT => '审核驳回',
		STORE_ALIPAY_SYNC_STATUS_PASS => '审核通过'
);

//支付宝口碑门店全类目 by gulei 2016-1-16
$GLOBALS['__ALIPAY_KOUBEI_STORE_ALL_CATEGORY'] = array(
		'2015062600002758' => array(
				'text' => '购物',
				'sub' => array(
						'2015062600007420' => array('text' => '服装饰品'),
						'2015062600009243' => array('text' => '本地购物'),
						'2015090700035947' => array('text' => '当地特色/保健品'),
						'2015062600006253' => array('text' => '烟酒'),
				)
		),
		'2015050700000000' => array(
				'text' => '美食',
				'sub' => array(
						'2015050700000001' => array(
								'text' => '中餐',
								'sub' => array(
										'2015050700000010' => array('text' => '川菜'),
										'2015050700000011' => array('text' => '湘菜'),
										'2015050700000012' => array('text' => '湖北菜'),
										'2015050700000013' => array('text' => '台湾菜'),
										'2015050700000014' => array('text' => '新疆菜'),
										'2015050700000015' => array('text' => '江浙菜'),
										'2015050700000016' => array('text' => '云南菜'),
										'2015050700000017' => array('text' => '贵州菜'),
										'2015050700000018' => array('text' => '西北菜'),
										'2015050700000019' => array('text' => '东北菜'),
										'2015050700000020' => array('text' => '香锅/烤鱼'),
										'2015050700000021' => array('text' => '海鲜'),
										'2015050700000022' => array('text' => '其它地方菜'),
										'2015052200000062' => array('text' => '粤菜'),
								)
						),
						'2015050700000002' => array(
								'text' => '火锅',
								'sub' => array(
									'2015050700000023' => array('text' => '麻辣烫/串串香'),
									'2015050700000024' => array('text' => '川味/重庆火锅'),
									'2015050700000025' => array('text' => '云南火锅'),
									'2015050700000026' => array('text' => '老北京涮羊肉'),
									'2015050700000027' => array('text' => '港式火锅'),
									'2015050700000028' => array('text' => '鱼火锅'),
									'2015050700000029' => array('text' => '羊蝎子'),
									'2015050700000030' => array('text' => '炭火锅'),
									'2015050700000031' => array('text' => '韩式火锅'),
									'2015050700000032' => array('text' => '豆捞'),
									'2015050700000033' => array('text' => '其它火锅'),
								)
						),
						'2015050700000003' => array(
								'text' => '小吃',
								'sub' => array(
									'2015050700000034' => array('text' => '熟食'),
									'2015050700000035' => array('text' => '面点'),
									'2015050700000036' => array('text' => '米粉/米线'),
									'2015050700000037' => array('text' => '其它小吃'),
								)
						),
						'2015050700000004' => array(
								'text' => '快餐',
								'sub' => array(
									'2015050700000038' => array('text' => '西式快餐'),
									'2015050700000039' => array('text' => '中式快餐'),
									'2015061690000030' => array('text' => '其它快餐'),
								)
						),
						'2015050700000005' => array(
								'text' => '休闲食品',
								'sub' => array(
									'2015050700000040' => array('text' => '零食'),
									'2015050700000041' => array('text' => '生鲜水果'),
									'2015050700000042' => array('text' => '咖啡'),
									'2015050700000043' => array('text' => '奶茶'),
									'2015061690000029' => array('text' => '其它休闲食品'),
								)
						),
						'2015050700000006' => array(
								'text' => '烘焙糕点',
								'sub' => array(
									'2015050700000044' => array('text' => '蛋糕'),
									'2015050700000045' => array('text' => '冰激凌'),
									'2015050700000046' => array('text' => '面包'),
									'2015050700000047' => array('text' => '饮品/甜点'),
									'2015061690000028' => array('text' => '其它烘焙糕点'),
								)
						),
						'2015050700000007' => array(
								'text' => '烧烤',
								'sub' => array(
									'2015050700000048' => array('text' => '中式烧烤'),
									'2015050700000049' => array('text' => '拉美烧烤'),
									'2015050700000050' => array('text' => '日式烧烤'),
									'2015050700000051' => array('text' => '铁板烧'),
									'2015050700000052' => array('text' => '韩式烧烤'),
									'2015061690000026' => array('text' => '其它烧烤'),
								)
						),
						'2015050700000008' => array(
								'text' => '汤/粥/煲/砂锅/炖菜',
								'sub' => array(
									'2015050700000053' => array('text' => '粥'),
									'2015050700000054' => array('text' => '汤'),
									'2015050700000055' => array('text' => '砂锅/煲类/炖菜'),
									'2015061690000025' => array('text' => '其它'),
								)
						),
						'2015050700000009' => array(
								'text' => '其它美食',
								'sub' => array(
									'2015050700000056' => array('text' => '自助餐'),
									'2015050700000057' => array('text' => '创意菜'),
									'2015050700000058' => array('text' => '西餐'),
									'2015050700000059' => array('text' => '日韩料理'),
									'2015050700000060' => array('text' => '东南亚菜'),
									'2015050700000061' => array('text' => '素食'),
									'2015061690000027' => array('text' => '其它餐饮美食'),
								)
						),
				)
		),
		'2015080600000001' => array(
				'text' => '航旅',
				'sub' => array(
						'2015080600000002' => array('text' => '客栈'),
						'2015080600000003' => array('text' => '酒店'),
						'2015080600000004' => array('text' => '旅行社'),
						'2015092200062945' => array('text' => '景区'),
				)
		),
		'2015063000013612' => array(
				'text' => '美发/美容/美甲',
				'sub' => array(
						'2015101000066113' => array('text' => '美容美发'),
						'2015063000015529' => array('text' => '美发'),
						'2015063000019130' => array('text' => 'SPA/美容/美体'),
						'2015101000067631' => array('text' => '美发美甲'),
						'2015063000017354' => array('text' => '美甲/手护'),
						'2015101000064159' => array('text' => '美容美甲'),
				)
		),
		'2015110500071135' => array(
				'text' => '运动健身',
				'sub' => array(
						'2015110500075901' => array('text' => '足球'),
						'2015110500085004' => array('text' => '武术'),
						'2015110500073009' => array('text' => '羽毛球'),
						'2015110500080520' => array('text' => '健身'),
						'2015110500083341' => array('text' => '舞蹈'),
						'2015110500081946' => array('text' => '卡丁赛车'),
						'2015110500078657' => array('text' => '游泳'),
						'2015110500078658' => array('text' => '桌球'),
						'2015110500078659' => array('text' => '瑜伽'),
						'2015110500077463' => array('text' => '网球'),
						'2015110500077464' => array('text' => '乒乓球'),
						'2015110500074890' => array('text' => '篮球'),
				)
		),
		'2015062600004525' => array(
				'text' => '休闲娱乐',
				'sub' => array(
						'2015063000012448' => array('text' => '保健/休闲养生'),
						'2015062600011157' => array('text' => '咖啡厅'),
						'2015090700042466' => array('text' => 'KTV及其他'),
						'2015090700039570' => array('text' => '足疗养生洗浴'),
						'2015091100061275' => array('text' => '酒吧'),
						'2015090700041394' => array('text' => '棋牌休闲'),
				)
		),
		'2015091000052157' => array(
				'text' => '超市便利店',
				'sub' => array(
						'2015091000060134' => array('text' => '便利店'),
						'2015091000056956' => array('text' => '个人护理'),
						'2015091000058486' => array('text' => '超市'),
				)
		),
		'2015063000020189' => array(
				'text' => '生活服务',
				'sub' => array(
						'2015090700048304' => array('text' => '通讯'),
						'2015063000026406' => array('text' => '车服务'),
						'2015090700045519' => array('text' => '家具家装'),
						'2015063000028051' => array('text' => '鲜花'),
						'2015063000024698' => array('text' => '摄影写真'),
						'2015090800051000' => array('text' => '其他'),
				)
		),
);

//支付宝口碑门店有返佣类目
$GLOBALS['__ALIPAY_KOUBEI_STORE_HAS_OWN_COMMISSION'] = array(
		'2015063000020189',
		'2015090800051000',
		'2015090700048304',
		'2015063000026406',
		'2015090700045519',
		'2015063000028051',
		'2015063000024698',
		'2015091000052157',
		'2015091000060134',
		'2015091000056956',
		'2015091000058486',
		'2015062600004525',
		'2015063000012448',
		'2015062600011157',
		'2015090700042466',
		'2015090700039570',
		'2015091100061275',
		'2015090700041394',
		'2015110500071135',
		'2015110500075901',
		'2015110500085004',
		'2015110500073009',
		'2015110500080520',
		'2015110500083341',
		'2015110500081946',
		'2015110500078657',
		'2015110500078658',
		'2015110500078659',
		'2015110500077463',
		'2015110500077464',
		'2015110500074890',
		'2015063000013612',
		'2015101000066113',
		'2015063000015529',
		'2015063000019130',
		'2015101000067631',
		'2015063000017354',
		'2015101000064159',
		'2015080600000001',
		'2015080600000002',
		'2015080600000003',
		'2015080600000004',
		'2015092200062945',
		'2015062600002758',
		'2015062600007420',
		'2015062600009243',
		'2015090700035947',
		'2015062600006253',
);

/*****************支付宝***********************/
//支付宝接口类型by gulei 2016-1-14
define('ALIPAY_API_VERSION_1_API', '1'); //支付宝1.0接口
define('ALIPAY_API_VERSION_2_API', '2'); //支付宝2.0接口
define('ALIPAY_API_VERSION_2_AUTH_API', '3'); //支付宝2.0授权接口

//支付宝门店审核常量by gulei 2016-1-14
define('ALIPAY_STORE_STATUS_AUDITING', 'AUDITING');//审核中
define('ALIPAY_STORE_STATUS_AUDIT_SUCCESS', 'AUDIT_SUCCESS');//审核通过
define('ALIPAY_STORE_STATUS_AUTO_PASS', 'AUTO_PASS');//自动通过
define('ALIPAY_STORE_STATUS_AUTO_FAIL', 'AUTO_FAIL');//支付宝内部的系统错误
define('ALIPAY_STORE_STATUS_AUDIT_FAILED', 'AUDIT_FAILED');//审核驳回

//支付宝用户信息常量by gulei 2016-1-14
//支付宝关注状态
define('ALIPAY_USER_NOTSUBSCRIBE', '1');//未关注
define('ALIPAY_USER_SUBSCRIBE', '2');//已关注
define('ALIPAY_USER_CANCELSUBSCRIBE', '3');//取消关注
//性别
define('ALIPAY_USER_GENDER_M', 'M');//男性
define('ALIPAY_USER_GENDER_F', 'F');//女性
//用户类型
define('ALIPAY_USER_USER_TYPE_VALUE_COMPANY', '1');//公司
define('ALIPAY_USER_USER_TYPE_VALUE_PERSONAL', '2');//个人
//是否经过经营执照认证
define('ALIPAY_USER_IS_LICENCE_AUTH_PASS', 'T');//通过营业执照认证
define('ALIPAY_USER_IS_LICENCE_AUTH_NOTPASS', 'F');//没通过营业执照认证
//是否通过实名认证
define('ALIPAY_USER_IS_CERTIFIED_PASS', 'T');//通过实名认证
define('ALIPAY_USER_IS_CERTIFIED_NOTPASS', 'F');//没有通过实名认证
//是否A类认证
define('ALIPAY_USER_IS_CERTIFY_GRADE_A_PASS', 'T');//通过Alei认证
define('ALIPAY_USER_IS_CERTIFY_GRADE_A_NOTPASS', 'F');//没有通过A类认证
//是否是学生
define('ALIPAY_USER_IS_STUDENT_CERTIFIED_YES', 'T');//是学生
define('ALIPAY_USER_IS_STUDENT_CERTIFIED_NO', 'F');//不是学生
//是否经过银行卡认证
define('ALIPAY_USER_IS_BANK_AUTH_PASS', 'T');//经过银行卡认证
define('ALIPAY_USER_IS_BANK_AUTH_NOTPASS', 'F');//未经过银行卡认证
//是否经过手机认证
define('ALIPAY_USER_IS_MOBILE_AUTH_PASS', 'T');//经过手机认证
define('ALIPAY_USER_IS_MOBILE_AUTH_NOTPASS', 'F');//未经过手机认证
//用户状态(Q/T/B/W)
define('ALIPAY_USER_STATUS_Q', 'Q');//快速注册用户
define('ALIPAY_USER_STATUS_T', 'T');//已认证用户
define('ALIPAY_USER_STATUS_B', 'B');//被冻结账户
define('ALIPAY_USER_STATUS_W', 'W');//注册未激活用户
//是否身份证认证
define('ALIPAY_USER_IS_ID_AUTH_PASS', 'T');//身份证认证
define('ALIPAY_USER_IS_ID_AUTH_NOTPASS', 'F');//非身份证认证

/************************************微信***************************************************/
//微信关注状态
define('WECHAT_USER_NOTSUBSCRIBE', '1');//未关注
define('WECHAT_USER_SUBSCRIBE', '2');//已关注
define('WECHAT_USER_CANCELSUBSCRIBE', '3');//取消关注
//微信用户性别
define('WECHAT_USER_SEX_MAN', '1');//男性
define('WECHAT_USER_SEX_FEMALE', '2');//女性

/************************************用户***************************************************/
//用户类型 微信粉丝 支付宝粉丝 玩券会员
define('USER_TYPE_WANQUAN_MEMBER', '1');//玩券会员
define('USER_TYPE_WECHAT_FANS', '2');//微信粉丝
define('USER_TYPE_ALIPAY_FANS', '3');//支付宝粉丝
//最后登录客户端
define('USER_LOGIN_CLIENT_WECHAT', '1');//微信
define('USER_LOGIN_CLIENT_ALIPAY', '2');//支付宝
define('USER_LOGIN_CLIENT_OTHER', '3');//其他
$GLOBALS['__USER_LOGIN_CLIENT'] = array(
		USER_LOGIN_CLIENT_WECHAT => '微信',
		USER_LOGIN_CLIENT_ALIPAY => '支付宝',
		USER_LOGIN_CLIENT_OTHER => '其他',
);
//微信用户性别
define('USER_SEX_MALE', '1');//男性
define('USER_SEX_FEMALE', '2');//女性

//粉丝绑定状态
define('USER_BIND_STATUS_UNBIND','1');//未绑定
define('USER_BIND_STATUS_BINDED','2');//已绑定

/*******************************************标签***********************************************/
//标签取值类型
define('TAG_VALUE_TYPE_ENUMERATION', '1');//枚举
//define('TAG_VALUE_TYPE_ENUMERATION', '2');//整数范围
//define('TAG_VALUE_TYPE_ENUMERATION', '3');//非整数范围
//define('TAG_VALUE_TYPE_ENUMERATION', '4');//布尔类型

define('TAG_VALUE_TYPE_INTEGER_RANGE', '2');//整数范围
define('TAG_VALUE_TYPE_NOT_INTEGER_RANGE', '3');//非整数范围
define('TAG_VALUE_TYPE_BOOLEAN', '4');//布尔类型

//标签类型
define('TAG_TYPE_ATTR', '1');//属性标签
define('TAG_TYPE_CONDITION', '2');//条件标签
$GLOBALS['__TAG_TYPE'] = array(
		TAG_TYPE_ATTR => '属性标签',
		TAG_TYPE_CONDITION => '条件标签'
);
//是否是组合标签
define('TAG_IF_COMBINATION_TAG_NO', '1');//不是
define('TAG_IF_COMBINATION_TAG_YES', '2');//是

/*****************************************营销活动***********************************************************/
//营销活动类型
define('MARKETING_ACTIVITY_TYPE_BE_MEMBER_GIVE', '1');//新加入会员赠券
define('MARKETING_ACTIVITY_TYPE_COMPLETE_MMEMBER_DATA', '2');//填资料赠券
define('MARKETING_ACTIVITY_TYPE_NEW_MEMBER_GIVE', '3');//新会员赠券
define('MARKETING_ACTIVITY_TYPE_NO_TRADE_MEMBER', '4');//加入未消费会员赠券
define('MARKETING_ACTIVITY_TYPE_REDEEM_LOSE_MEMBER', '5');//挽回流失客户
define('MARKETING_ACTIVITY_TYPE_PROMOTE_MEMBER', '6');//促进未流失客户
define('MARKETING_ACTIVITY_TYPE_OLD_MEMBER_GIVE', '7');//给老会员赠券
define('MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY', '8');//储值活动
define('MARKETING_ACTIVITY_TYPE_BIRTHDAY_GIVE', '9');//生日赠券
define('MARKETING_ACTIVITY_TYPE_MEMBER_GIVE', '10');//会员赠券
define('MARKETING_ACTIVITY_TYPE_CUMULATIVE_GIVE', '11');//累计消费赠券
define('MARKETING_ACTIVITY_TYPE_FULL_GIVE', '12');//消费满赠券
define('MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING', '13');//精准营销


define('MARKETING_ACTIVITY_TYPE_DMALL_SDLJ', '14');//东钱湖营销活动-首单立减
define('MARKETING_ACTIVITY_TYPE_DMALL_ZFL', '15');//东钱湖营销活动-周福利

$GLOBALS['__MARKETING_ACTIVITY_TYPE'] = array(
		MARKETING_ACTIVITY_TYPE_BE_MEMBER_GIVE => '新加入会员赠券',
		MARKETING_ACTIVITY_TYPE_COMPLETE_MMEMBER_DATA => '填资料赠券',
		MARKETING_ACTIVITY_TYPE_NEW_MEMBER_GIVE => '新会员赠券',
		MARKETING_ACTIVITY_TYPE_NO_TRADE_MEMBER => '加入未消费会员赠券',
		MARKETING_ACTIVITY_TYPE_REDEEM_LOSE_MEMBER => '挽回流失客户',
		MARKETING_ACTIVITY_TYPE_PROMOTE_MEMBER => '促进未流失客户',
		MARKETING_ACTIVITY_TYPE_OLD_MEMBER_GIVE => '给老会员赠券',
		MARKETING_ACTIVITY_TYPE_STORED_ACTIVITY => '储值活动',
		MARKETING_ACTIVITY_TYPE_BIRTHDAY_GIVE => '生日赠券',
		MARKETING_ACTIVITY_TYPE_MEMBER_GIVE => '会员赠券',
		MARKETING_ACTIVITY_TYPE_CUMULATIVE_GIVE => '累计消费赠券',
		MARKETING_ACTIVITY_TYPE_FULL_GIVE => '消费满赠券',
		MARKETING_ACTIVITY_TYPE_PRECISION_MARKETING => '精准营销'
);
//营销活动-活动时间类型
define('MARKETING_ACTIVITY_TIME_TYPE_SHORT', '1');//短期
define('MARKETING_ACTIVITY_TIME_TYPE_LONG', '2');//长期
$GLOBALS['MARKETING_ACTIVITY_TIME_TYPE'] = array(
		MARKETING_ACTIVITY_TIME_TYPE_SHORT => '短期活动',
		MARKETING_ACTIVITY_TIME_TYPE_LONG => '长期活动'
);
//营销活动-活动群体类型
define('MARKETING_ACTIVITY_TARGET_TYPE_DEFAULT', '1');//默认群体
define('MARKETING_ACTIVITY_TARGET_TYPE_APPOINT', '2');//指定群体
//营销活动-发券方式
define('MARKETING_ACTIVITY_SEND_TYPE_DIRECT_PUT', '1');//直接放入会员卡包
define('MARKETING_ACTIVITY_SEND_TYPE_ALIWX', '2');//服务窗消息公众号消息发券
// define('MARKETING_ACTIVITY_SEND_TYPE_APPOINT', '2');//指定群体
//营销活动-活动状态
define('MARKETING_ACTIVITY_STATUS_NOT_START', '1');//未开始
define('MARKETING_ACTIVITY_STATUS_IN_PROGRESS', '2');//进行中
define('MARKETING_ACTIVITY_STATUS_OVER', '3');//已结束
define('MARKETING_ACTIVITY_STATUS_STOP', '4');//已停止
$GLOBALS['__MARKETING_ACTIVITY_STATUS'] = array(
		MARKETING_ACTIVITY_STATUS_NOT_START => '未开始',
		MARKETING_ACTIVITY_STATUS_IN_PROGRESS => '进行中',
		MARKETING_ACTIVITY_STATUS_OVER => '已结束',
		MARKETING_ACTIVITY_STATUS_STOP => '已停止'
);
//营销活动-完善资料
define('MARKETING_ACTIVITY_CONDITION_FULL_NAME', '1');//姓名
define('MARKETING_ACTIVITY_CONDITION_SEX', '2');//性别
define('MARKETING_ACTIVITY_CONDITION_AVATAR', '3');//头像
define('MARKETING_ACTIVITY_CONDITION_NICKNAME', '4');//昵称
define('MARKETING_ACTIVITY_CONDITION_BIRTHDAY', '5');//生日
define('MARKETING_ACTIVITY_CONDITION_ID', '6');//身份证
define('MARKETING_ACTIVITY_CONDITION_EMAIL', '7');//邮箱
define('MARKETING_ACTIVITY_CONDITION_MARITAL_STATUS', '8');//婚姻状况
define('MARKETING_ACTIVITY_CONDITION_WORK', '9');//工作
define('MARKETING_ACTIVITY_CONDITION_ADDRESS', '10');//地址
$GLOBALS['__MARKETING_ACTIVITY_CONDITION'] = array(
		MARKETING_ACTIVITY_CONDITION_FULL_NAME => '姓名',
		MARKETING_ACTIVITY_CONDITION_SEX => '性别',
		MARKETING_ACTIVITY_CONDITION_AVATAR => '头像',
		MARKETING_ACTIVITY_CONDITION_NICKNAME => '昵称',
		MARKETING_ACTIVITY_CONDITION_BIRTHDAY => '生日',
		MARKETING_ACTIVITY_CONDITION_ID => '身份证',
		MARKETING_ACTIVITY_CONDITION_EMAIL => '邮箱',
		MARKETING_ACTIVITY_CONDITION_MARITAL_STATUS => '婚姻状况',
		MARKETING_ACTIVITY_CONDITION_WORK => '工作',
		MARKETING_ACTIVITY_CONDITION_ADDRESS => '地址'
);


/***************************************东钱湖商城***********************************************/
//入园时间类型
define('DMALL_DPRODUCT_CHECK_TIME_TYPE_NO_LIMIT',1);//不限制
define('DMALL_DPRODUCT_CHECK_TIME_TYPE_DAY_HOUR_MINUTE', 2);//请至少在入院前xx天xx点xx分以前购买
define('DMALL_DPRODUCT_CHECK_TIME_TYPE_HOUR_MINUTE', 3);//请至少在入院前xx个小时xx分钟以前购买


//东钱湖营销活动状态
define('DMALL_ACTIVITY_STATUS_NOT_START', 1);//未开始
define('DMALL_ACTIVITY_STATUS_STARTING', 2);//进行中
define('DMALL_ACTIVITY_STATUS_NO_STOCK', 3);//已抢完
define('DMALL_ACTIVITY_STATUS_END', 4);//已结束
$GLOBALS['__DMALL_ACTIVITY_STATUS'] = array(
    DMALL_ACTIVITY_STATUS_NOT_START => '未开始',
    DMALL_ACTIVITY_STATUS_STARTING => '进行中',
    DMALL_ACTIVITY_STATUS_NO_STOCK => '已抢完',
    DMALL_ACTIVITY_STATUS_END => '已结束',
);


//东钱湖活动类型
define('DMALL_ACTIVITY_TYPE_SDLJ', 1);//首单立减
define('DMALL_ACTIVITY_TYPE_ZFL', 2);//周福利
$GLOBALS['__DMALL_ACTIVITY_TYPE'] = array(
    DMALL_ACTIVITY_TYPE_SDLJ => '首单立减',
    DMALL_ACTIVITY_TYPE_ZFL => '周福利'
);
    

/*******************************************优惠券************************************************/
//优惠券核销渠道
define('COUPONS_USE_CHANNEL_OFFLINE', 1);//线下使用
define('COUPONS_USE_CHANNEL_ONLINE', 2);//线上使用
define('COUPONS_USE_CHANNEL_ALL', 3);//线上线下通用

//券码使用状态
define('COUPON_CODE_STATUS_NOTUSED', 1);//未使用
define('COUPON_CODE_STATUS_USED', 2);//已使用

/*******************************************应用市场-酒店宾馆订房**********************************************/
//预定订单状态
define('HOTEL_ORDER_STATUS_WAITING', '1');//待确定
define('HOTEL_ORDER_STATUS_CONFIRM', '2');//已确定
define('HOTEL_ORDER_STATUS_REFUSE', '3');//已拒绝
define('HOTEL_ORDER_STATUS_CANCEL', '4');//已取消
define('HOTEL_ORDER_STATUS_CHECKIN', '5');//已入住
$GLOBALS['__HOTEL_ORDER_STATUS'] = array(
    HOTEL_ORDER_STATUS_WAITING => '待确定',
    HOTEL_ORDER_STATUS_CONFIRM => '已确定',
    HOTEL_ORDER_STATUS_REFUSE => '已拒绝',
    HOTEL_ORDER_STATUS_CANCEL => '已取消',
    HOTEL_ORDER_STATUS_CHECKIN => '已入住',
    );

/*****************************************玩券管家***************************************************/
//玩券管家版本
define('WANQUAN_TYPE_CASH', '1');//收银版
define('WANQUAN_TYPE_MARKETING', '2');//营销版
// define('WANQUAN_TYPE_CASH', '3');//
// define('WANQUAN_TYPE_CASH', '4');//
$GLOBALS['__WANQUAN_TYPE'] = array(
    WANQUAN_TYPE_CASH => '收银版',
    WANQUAN_TYPE_MARKETING => '营销版',
);

/********************************************积分*****************************************************/
//积分记录类型
define('BALANCE_OF_PAYMENTS_INCOME', 1);//收入
define('BALANCE_OF_PAYMENTS_EXPEND', 2);//支出
$GLOBALS['__BALANCE_OF_PAYMENTS'] = array(
    BALANCE_OF_PAYMENTS_INCOME => '收入',
    BALANCE_OF_PAYMENTS_EXPEND => '支出',
);

//积分来源类型及支出类型
define('USER_POINTS_DETAIL_FROM_TRADE', 1);//消费得积分
define('USER_POINTS_DETAIL_FROM_STORED', 2);//储值得积分
define('USER_POINTS_DETAIL_FROM_SIGN', 3);//签到得积分
define('USER_POINTS_DETAIL_FROM_FOLLOW', 4);//关注得积分
define('USER_POINTS_DETAIL_FROM_PERFECT', 5);//完善资料得积分
define('USER_POINTS_DETAIL_FROM_CLEAN', 6);//定时清积分
$GLOBALS['__USER_POINTS_DETAIL_FROM'] = array(
    USER_POINTS_DETAIL_FROM_TRADE => '消费',
    USER_POINTS_DETAIL_FROM_STORED => '储值',
    USER_POINTS_DETAIL_FROM_SIGN => '签到',
    USER_POINTS_DETAIL_FROM_FOLLOW => '关注',
    USER_POINTS_DETAIL_FROM_PERFECT => '完善资料',
    USER_POINTS_DETAIL_FROM_CLEAN => '积分清理',
);


/******************************************注册完善资料***********************************************/
//资料必填项
define('MERCHANT_AUTH_SET_NAME', 1);//会员姓名
define('MERCHANT_AUTH_SET_ADDRESS', 4);//通讯地址
define('MERCHANT_AUTH_SET_SEX', 2);//会员性别
define('MERCHANT_AUTH_SET_BIRTHDAY', 3);//会员生日
define('MERCHANT_AUTH_SET_ID', 5);//身份证号
define('MERCHANT_AUTH_SET_EMAIL', 6);//邮箱
define('MERCHANT_AUTH_SET_MARITAL_STATUS', 7);//婚姻状况
define('MERCHANT_AUTH_SET_WORK', 8);//工作

//用户是否完善注册资料
define('USER_IF_PERFECT_NO', 1);//未完善
define('USER_IF_PERFECT_YES', 2);//已完善

/*************************************************物业管理****************************************************/
//接口调用状态
define('APPLY_CLASS_SUCCESS', '10000');//接口调用成功
define('APPLY_CLASS_FAIL', '10001');//接口调用失败

//数据库操作
define('ERROR_DATA_BASE_ADD', '20200');//数据库添加失败
define('ERROR_DATA_BASE_EDIT', '20201');//数据库修改失败
define('ERROR_DATA_BASE_SELECT', '20202');//数据库查询失败
define('ERROR_DATA_BASE_DELETE', '20203');//数据库删除失败

//业主审核状态
define('PROPRIETOR_VERIFY_STATUS_PENDING_AUDIT', '1');//待审核
define('PROPRIETOR_VERIFY_STATUS_PASS', '2');//通过
define('PROPRIETOR_VERIFY_STATUS_REJECT', '3');//未通过
$GLOBALS['__PROPRIETOR_VERIFY_STATUS'] = array(
	PROPRIETOR_VERIFY_STATUS_PENDING_AUDIT => '待审核',
	PROPRIETOR_VERIFY_STATUS_PASS => '成功',
	PROPRIETOR_VERIFY_STATUS_REJECT => '不成功',
);

//业主类型
define('PROPRIETOR_TYPE_OWNER', '1');//业主
define('PROPRIETOR_TYPE_TENEMENT', '2');//租户
$GLOBALS['__PROPRIETOR_TYPE'] = array(
	PROPRIETOR_TYPE_OWNER => '业主',
	PROPRIETOR_TYPE_TENEMENT => '租户',
);

//订单类型
define('FEEORDER_TYPE_WATER_FEE', '1');//水费
define('FEEORDER_TYPE_ELECTRICITY_FEE', '2');//电费
define('FEEORDER_TYPE_PROPERTY_FEE', '3');//物业费
define('FEEORDER_TYPE_PARKING_FEE', '4');//停车费

//区域类型
define('REPORT_REPAIR_RECORD_TYPE_OUTDOOR', '1');//室外
define('REPORT_REPAIR_RECORD_TYPE_INDOOR', '2');//室内
$GLOBALS['REPORT_REPAIR_RECORD_TYPE'] = array(
    REPORT_REPAIR_RECORD_TYPE_INDOOR => '室内区域',
    REPORT_REPAIR_RECORD_TYPE_OUTDOOR => '室外区域');

//报修状态
define('REPORT_REPAIR_RECORD_STATUS_WAITING', '1');//待报修
define('REPORT_REPAIR_RECORD_STATUS_COMPLETE','2');//已报修
$GLOBALS['REPORT_REPAIR_RECORD_STATUS'] = array(
    REPORT_REPAIR_RECORD_STATUS_WAITING => '待报修',
    REPORT_REPAIR_RECORD_STATUS_COMPLETE => '已报修'
);

//电费类型
define('COMMUNITY_ELECTRICITY_FEE_SET_DAYPARTING', '1');//分时段
define('COMMUNITY_ELECTRICITY_FEE_SET_NODAYPARTING', '2');//不分时段
$GLOBALS['__COMMUNITY_ELECTRICITY_FEE_SET'] = array(
	COMMUNITY_ELECTRICITY_FEE_SET_DAYPARTING => '分时段',
	COMMUNITY_ELECTRICITY_FEE_SET_NODAYPARTING => '不分时段');

//停车费类型
define('COMMUNITY_PARKING_FEE_SET_OVERGROUND', '1');//地上停车
define('COMMUNITY_PARKING_FEE_SET_UNDERGROUND','2');//地下停车
$GLOBALS['__COMMUNITY_PARKING_FEE_SET'] = array(
	COMMUNITY_PARKING_FEE_SET_OVERGROUND => '地上停车',
	COMMUNITY_PARKING_FEE_SET_UNDERGROUND => '地下停车'
);








