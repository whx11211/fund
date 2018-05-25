<?php 
/**
 * 配置设置
 *
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

// 设置时区
ini_set('date.timezone', 'Asia/Shanghai');

// 数据库执行语句是否记录
define('DB_DEBUG', 1);

//错误DEGUE，=1表示直接显示错误，=0表示使用自定义错误处理
define('ERROR_DEBUG', 0);

//报错级别设置
define('ERROR_LEVEL', E_ALL);

// 数据库类型，请填写PDO识别的标准名称 如mysql
define('DB_TYPE', 'mysql');
// 数据库IP
define('DB_IP', '127.0.0.1');
// 数据库端口，默认3306，一般不需修改
define('DB_PORT', 33306);
// 数据库IP
define('DB_USER', 'root');
// 数据库密码
define('DB_PWD', 'root');
// 默认数据库名
define('DB_NAME', 'fund');


//缓存配置
define('CACHE_HOST', '127.0.0.1');
define('CACHE_PORT', '6379');



?>