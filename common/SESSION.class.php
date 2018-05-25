<?php

/**
 * SESSION类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class SESSION
{

    /**
     * SESSION是否开启
     */
    private static $is_start = false;

    /**
     * 获取SESSION
     * @param $key string 
     */
    public static function get($key=null)
    {
        self::checkStart();
        if (is_null($key)) {
            return $_SESSION;
        }
        else if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        else {
            return null;
        }
    }
    
    /**
     * 设置SESSION
     * @param $key string 
     * @param $val 
     */
    public static function set($key, $val)
    {
        self::checkStart();
        $_SESSION[$key] = $val;
    }
    
    /**
     * 检查SESSION是否开启
     */
    private static function checkStart()
    {
        if (!self::$is_start) {
            session_start();
            self::$is_start = true;
        }
        return true;
    }
    
    /**
     * SESSION销毁
     */
    public static function destory()
    {
        return session_destroy();
    }

}


?>