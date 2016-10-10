<?php
/*	create by gulei
 * 公共常量表  主要存放一些公共且普遍的常量
 * 规则：
 * 1、常量必须加注释，并且需要标明添加者和添加日期还有用处
 * 2、命名规范带上同意前缀以作区分
 * 3、需要和管理员报备自己添加的常量
 * */

//删除标志位
define('FLAG_NO', '1');//正常
define('FLAG_YES', '2');//删除


/****************************性别****************************/
//性别
define('SEX_TYPE_MALE', 1);//男性
define('SEX_TYPE_FEMALE', 2);//女性
$GLOBALS['__SEX_TYPE'] = array(
    SEX_TYPE_MALE => '男',
    SEX_TYPE_FEMALE => '女',
);

