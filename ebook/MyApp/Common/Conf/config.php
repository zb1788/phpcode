<?php
$config = array(
	'MODULE_ALLOW_LIST'    =>    array('Home'),
	'DEFAULT_MODULE'       =>    'Home',
	//'BIND_MODULE'       =>    'Home',
	// 'BUILD_CONTROLLER_LIST' =>  array('Index,User,Menu'),
	// 'BUILD_MODEL_LIST' =>  array('User,Menu'),

	/*数据库连接配置*/

	//数据库配置信息
	'DB_CONFIG' => array(
			'DB_TYPE'   => 'mysql', // 数据库类型
			'DB_HOST'   => '192.168.151.126', // 服务器地址
			'DB_NAME'   => 'db_ebook', // 数据库名
			'DB_USER'   => 'root', // 用户名
			'DB_PWD'    => '123456', // 密码
			'DB_PORT'   => 3306, // 端口
			'DB_PREFIX' => 't_', // 数据库表前缀
			'DB_CHARSET'=> 'utf8', // 字符集
			'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	),

	//数据库配置，中心库信息
	'DB_CONFIG1' => array(
			'DB_TYPE'   => 'mysql', // 数据库类型
			'DB_HOST'   => '192.168.151.50', // 服务器地址
			'DB_NAME'   => 'RMS_Data', // 数据库名
			'DB_USER'   => 'vcom', // 用户名
			'DB_PWD'    => '2012rmsedu', // 密码
			'DB_PORT'   => 3306, // 端口
			'DB_CHARSET'=> 'utf8', // 字符集
			'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	),
		//数据库配置，语文库信息
		'DB_CONFIG2' => array(
				'DB_TYPE'   => 'mysql', // 数据库类型
				'DB_HOST'   => 'localhost', // 服务器地址
				'DB_NAME'   => 'db_yuwen', // 数据库名
				'DB_USER'   => 'root', // 用户名
				'DB_PWD'    => '123456', // 密码
				'DB_PORT'   => 3306, // 端口
				'DB_PREFIX' => 'yw_', // 数据库表前缀
				'DB_CHARSET'=> 'utf8', // 字符集
				'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
		),


	'SHOW_PAGE_TRACE'=>false, //调试模式

	'SESSION_AUTO_START' => true, //是否开启session

	'URL_HTML_SUFFIX'=>'', //路由功能
	'URL_MODEL' => '2',


	'URL_CASE_INSENSITIVE' =>true,  //不区分路由的大小写

);
$config_const = require('config_const.php');
return array_merge($config ,$config_const); //合并配置项，返回