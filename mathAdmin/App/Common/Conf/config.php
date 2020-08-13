<?php
return array(
    //'配置项'=>'配置值'
    'MODULE_ALLOW_LIST'    =>    array('Home','Manager'),
    'DEFAULT_MODULE'       =>    'Manager',
    //数据库配置信息

    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'db_math', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '123456', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'sx_', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增

    'SHOW_PAGE_TRACE'=>false, //调试模式

    'SESSION_AUTO_START' => true, //是否开启session

    'URL_HTML_SUFFIX'=>'', //路由功能
    'URL_MODEL' => '2',


    'URL_CASE_INSENSITIVE' =>true,  //不区分路由的大小写


);