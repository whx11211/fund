<?php 
/**
 * 通用组件初始化
 * 
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

// 定义跟路径
define('ROOT', str_replace('\\', '/', substr(dirname(__file__), 0, -6)));
// 定义common路径
define('COMMON_PATH', ROOT . 'common/');
// 定义model路径
define('MODEL_PATH', ROOT . 'model/');
// 定义common路径
define('MOVIE_PATH', ROOT . 'movie/');
// 定义tool路径
define('TOOL_PATH', ROOT . 'tool/');
// 定义control路径
define('CONTROL_PATH', ROOT . 'control/');
// 定义common路径
define('LOG_PATH', ROOT . 'logs/');

// 定义文件后缀名
define('EXT', 'php');

include ROOT . 'config/config.php';
include COMMON_PATH . 'AutoLoad.class.php';
include COMMON_PATH . 'function.php';

// 自动加载启动
Load::autoLoad();

if (!defined('ERROR_DEBUG') || !ERROR_DEBUG) {
    // 错误控制
    ErrorHandle::register();
    error_reporting(0);
}
else if (defined('ERROR_LEVEL') && ERROR_LEVEL){
    error_reporting(ERROR_LEVEL);
}
else {
    error_reporting(7);
}


?>