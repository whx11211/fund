<?php

/**
 * 获取Model实例类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Instance
{
    /**
     * 
     * Model实例类数组
     */
    private static $instances;
    
    /**
     * 获取数据库实例
     * 
     * @param $table 表名
     * 
     * return 数据库实例
     */
    public static function get($table)
    {
        if (isset(self::$instances[$table])) {
            return self::$instances[$table];
        }
        else {
            $classname = ucfirst($table).'Model';

            if (class_exists($classname)) {
                return self::$instances[$table] = new $classname();
            }
            else {
                return self::$instances[$table] = new Model($table);
            }
        }
    }
    
    /**
     * 获取电影数据库实例
     *
     * @param $table 表名
     *
     * return 数据库实例
     */
    public static function getMovie($table)
    {
        $pre = 'movie_';
        if (isset(self::$instances[$pre.$table])) {
            return self::$instances[$pre.$table];
        }
        else {
            $classname = ucfirst($table).'Movie';
    
            if (class_exists($classname)) {
                return self::$instances[$pre.$table] = new $classname();
            }
            else {
                return self::$instances[$pre.$table] = new Movie($table);
            }
        }
    }
}

?>