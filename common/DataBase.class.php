<?php

/**
 * 数据库基类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class DataBase extends PDO
{
    /**
     * 数据库实例数组
     */
    private static $_instances;

    /**
     * 自动加载方法注册
     * 
     * @param $name 数据库名
     * @param $ip 数据库IP
     * @param $user 数据库用户
     * @param $pwd 数据库密码
     * @param $option array 设置数组
     * @return resource
     */
    public function __construct($type, $name, $ip, $port, $user, $pwd, $option)
    {
        $dsn = $type . ":host=" . $ip . ";dbport=" . $port . ";dbname=" . $name;
        parent::__construct($dsn, $user, $pwd, $option);
        $this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
    }
    
    /**
     * 获取数据库实例方法
     * 
     * @param $name 数据库名
     * @param $ip 数据库IP
     * @param $user 数据库用户
     * @param $pwd 数据库密码
     * @param $option array 设置数组
     * @return resource
     */
    public static function getInstance($type, $name, $ip, $port, $user, $pwd, $option=array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';",PDO::MYSQL_ATTR_FOUND_ROWS => true))
    {
        $db_key = $ip . '-' .$user. '-' .$name;
        if (isset(self::$_instances[$db_key])) {
            // 已有实例，直接返回
            return self::$_instances[$db_key];
        }
        // 无实例，创建保存实例，然后后返回
        self::$_instances[$db_key] = new DataBase($type, $name, $ip, $port, $user, $pwd, $option);
        return self::$_instances[$db_key];
    }
}

?>