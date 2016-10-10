<?php
/*	create by gulei
 * OA常量表
* 规则：
* 1、常量必须加注释，并且需要标明添加者和添加日期还有用处
* 2、命名规范带上同意前缀以作区分
* 3、需要和管理员报备自己添加的常量
* */


/****************************公司****************************/
//公司类型
define('COMPANY_TYPE_HEADQUARTERS', 1);//总部
define('COMPANY_TYPE_SUBSIDIARY', 2);//子公司
define('COMPANY_TYPE_BRANCHCOMPANY', 3);//分公司
$GLOBALS['__COMPANY_TYPE'] = array(
    COMPANY_TYPE_HEADQUARTERS => '总公司',
    COMPANY_TYPE_SUBSIDIARY => '子公司',
    COMPANY_TYPE_BRANCHCOMPANY => '分公司',
);

/**************************agent****************************/
//agent name
define('AGENT_NAME', '服务运营商');



/****************************权限************************************/
//OA系统权限
$GLOBALS['FUNCTION_SYSTEM_AUTHORITY'] = array(
    'u' => array(
        'name' => '用户管理',
        'sub' => array(
            'u001' => array('name' => '查看公司'),
            'u002' => array('name' => '添加公司'),
            'u003' => array('name' => '删除公司'),
            'u004' => array('name' => '编辑公司'),
            'u005' => array('name' => '查看部门'),
            'u006' => array('name' => '添加部门'),
            'u007' => array('name' => '删除部门'),
            'u008' => array('name' => '编辑部门'),
            'u009' => array('name' => '查看岗位'),
            'u010' => array('name' => '添加岗位'),
            'u011' => array('name' => '删除岗位'),
            'u012' => array('name' => '编辑岗位'),
            'u013' => array('name' => '查看权限'),
            'u014' => array('name' => '编辑权限'),
            'u015' => array('name' => '查看人员'),
            'u016' => array('name' => '添加人员'),
            'u017' => array('name' => '删除人员'),
            'u018' => array('name' => '编辑人员'),
        )
    ),
    'a' => array(
        'name' => AGENT_NAME.'管理',
        'sub' => array(
            'a001' => array('name' => '查看'.AGENT_NAME),
            'a002' => array('name' => '添加'.AGENT_NAME),
            'a003' => array('name' => '删除'.AGENT_NAME),
            'a004' => array('name' => '编辑'.AGENT_NAME),
        )
    ),
    'm' => array(
        'name' => '商户管理',
        'sub' => array(
            'm001' => array('name' => '查看商户'),
            'm002' => array('name' => '导出商户'),
            'm003' => array('name' => '审核商户'),
        )
    ),
    'f' => array(
        'name' => '财务管理',
        'sub' => array(
            'f001' => array('name' => '查看支付宝返佣'),
            'f002' => array('name' => '查看微信返佣'),
            'f003' => array('name' => '查看'.AGENT_NAME.'返佣'),
            'f004' => array('name' => '服务费管理'),
        )
    ),
    'd' => array(
        'name' => '统计管理',
        'sub' => array(
            'd001' => array('name' => AGENT_NAME.'统计'),
            'd002' => array('name' => '商户统计'),
            'd003' => array('name' => '交易统计'),
            'd004' => array('name' => '用户统计'),
        )
    ),
    's' => array(
        'name' => '系统管理',
        'sub' => array(
            's001' => array('name' => '公告管理'),
            's002' => array('name' => '资料管理'),
            's003' => array('name' => '密码设置'),
            's004' => array('name' => '查看个人日志'),
            's005' => array('name' => '查看部门日志'),
            's006' => array('name' => '查看公司日志'),
            's007' => array('name' => '查看全部日志'),
        )
    )
);



/**************************官网-新闻******************************/
//新闻发布状态
define('NEWS_STATUS_UNPUBLISHED', '1');//未发布
define('NEWS_STATUS_PUBLISHED', '2');//已发布
define('NEWS_STATUS_CANCELPUBLISHED', '3');//取消发布
$GLOBALS['__NEWS_STATUS'] = array(
    NEWS_STATUS_UNPUBLISHED => '未发布',
    NEWS_STATUS_PUBLISHED => '已发布',
    NEWS_STATUS_CANCELPUBLISHED => '取消发布'
);

/*****************************日志******************************************/
//日志操作类型
define('OA_OPERATION_LOG_TYPE_LOGIN', '1');//登录
define('OA_OPERATION_LOG_TYPE_LOGOUT', '2');//退出
define('OA_OPERATION_LOG_TYPE_ADD', '3');//添加
define('OA_OPERATION_LOG_TYPE_DEL', '4');//删除
define('OA_OPERATION_LOG_TYPE_EDT', '5');//编辑
define('OA_OPERATION_LOG_TYPE_EXPORT', '6');//导出
define('OA_OPERATION_LOG_TYPE_OPEN_SUBACCOUNT', '7');//开启子账号功能
define('OA_OPERATION_LOG_TYPE_CLOSE_SUBACCOUNT', '8');//关闭子账号功能
define('OA_OPERATION_LOG_TYPE_CONFIRMPAY', '9');//确认付款
define('OA_OPERATION_LOG_TYPE_AUDITING_PASS', '10');//审核通过
define('OA_OPERATION_LOG_TYPE_REJECT', '11');//驳回
$GLOBALS['OA_OPERATION_LOG_TYPE'] = array(
    OA_OPERATION_LOG_TYPE_LOGIN => '登录',
    OA_OPERATION_LOG_TYPE_LOGOUT => '退出',
    OA_OPERATION_LOG_TYPE_ADD => '添加',
    OA_OPERATION_LOG_TYPE_DEL => '删除',
    OA_OPERATION_LOG_TYPE_EDT => '编辑',
    OA_OPERATION_LOG_TYPE_EXPORT => '导出',
    OA_OPERATION_LOG_TYPE_OPEN_SUBACCOUNT => '开启子账号功能',
    OA_OPERATION_LOG_TYPE_CLOSE_SUBACCOUNT => '关闭子账号功能',
    OA_OPERATION_LOG_TYPE_CONFIRMPAY => '确认付款',
    OA_OPERATION_LOG_TYPE_AUDITING_PASS => '审核通过',
    OA_OPERATION_LOG_TYPE_REJECT => '驳回',
);


//日志操作对象
define('OA_OPERATION_LOG_OBJECT_COMPANY', 1);//公司
define('OA_OPERATION_LOG_OBJECT_DEPARTMENT', 2);//部门
define('OA_OPERATION_LOG_OBJECT_POST', 3);//岗位
define('OA_OPERATION_LOG_OBJECT_STAFF',4);//人员
define('OA_OPERATION_LOG_OBJECT_SYSTEM_NOTICE',5);//系统公告
define('OA_OPERATION_LOG_OBJECT_DATA_DOWNLOAD',6);//资料


/*****************************************系统管理************************************************/
/*************************公告*************************/
//公告发布状态
define('SYSTEM_NOTICE_STATUS_UNRELEASE', 1);//未发布
define('SYSTEM_NOTICE_STATUS_RELEASE', 2);//已发布
$GLOBALS['SYSTEM_NOTICE_STATUS'] = array(
    SYSTEM_NOTICE_STATUS_UNRELEASE => '未发布',
    SYSTEM_NOTICE_STATUS_RELEASE => '已发布'
);

//公告发布到平台
define('SYSTEM_NOTICE_RELEASE_TO_FX', 1);//发布到分销
define('SYSTEM_NOTICE_RELEASE_TO_OA', 2);//发布到oa内部

/*********************资料****************************/
//资料发布状态
define('DATA_DOWNLOAD_STATUS_UNRELEASE', 1);//未发布
define('DATA_DOWNLOAD_STATUS_RELEASE', 2);//已发布
$GLOBALS['DATA_DOWNLOAD_STATUS'] = array(
    DATA_DOWNLOAD_STATUS_UNRELEASE => '未发布',
    DATA_DOWNLOAD_STATUS_RELEASE => '已发布'
);

//资料类型
define('DATA_DOWNLOAD_TYPE_DOC', 1);//文档
define('DATA_DOWNLOAD_TYPE_VIDEO', 2);//视频
$GLOBALS['DATA_DOWNLOAD_TYPE'] = array(
    DATA_DOWNLOAD_TYPE_DOC => '文档',
    DATA_DOWNLOAD_TYPE_VIDEO => '视频'
);

//资料发布到平台
define('DATA_DOWNLOAD_RELEASE_TO_FX', 1);//发布到分销
define('DATA_DOWNLOAD_RELEASE_TO_OA', 2);//发布到oa内部

//资料添加到
define('DATA_DOWNLOAD_ADD_TO_FX', 1);//添加到分销
define('DATA_DOWNLOAD_ADD_TO_WQ', 2);//添加到玩券管家
$GLOBALS['DATA_DOWNLOAD_ADD_TO'] = array(
    DATA_DOWNLOAD_ADD_TO_FX => '分销',
    DATA_DOWNLOAD_ADD_TO_WQ => '玩券管家'
);

/***************************服务运营商************************************/
//服务运营商状态
define('AGENT_PAY_STATUS_UNPAID', 1);//待付款
define('AGENT_PAY_STATUS_WAITING_CONFIRM', 2);//已付款，待确认
define('AGENT_PAY_STATUS_PAID', 3);//已付款
$GLOBALS['AGENT_PAY_STATUS'] = array(
    AGENT_PAY_STATUS_UNPAID => '待付款',
    AGENT_PAY_STATUS_WAITING_CONFIRM => '已付款，待确认',
    AGENT_PAY_STATUS_PAID => '已付款'
);


/***************************商户**************************************/
//玩券商户审核状态 待审核、驳回、审核通过 by gulei 2016-1-14
define('OA_MERCHANT_VERIFY_STATUS_NUAUTH', '1');//未认证
define('OA_MERCHANT_VERIFY_STATUS_WAIT', '2');//待审核
define('OA_MERCHANT_VERIFY_STATUS_REJECTIT', '3');//驳回
define('OA_MERCHANT_VERIFY_STATUS_AUTH', '4');//已认证
$GLOBALS['__OA_MERCHANT_VERIFY_STATUS'] = array(
    OA_MERCHANT_VERIFY_STATUS_NUAUTH => '未认证',
    OA_MERCHANT_VERIFY_STATUS_WAIT => '待审核',
    OA_MERCHANT_VERIFY_STATUS_REJECTIT => '驳回',
    OA_MERCHANT_VERIFY_STATUS_AUTH => '已认证'
);
