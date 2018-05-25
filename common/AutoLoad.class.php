<?php

/**
 * 自动加载类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Load
{

    /**
     * 自动加载方法注册
     * 
     * @param
     *        null
     * @return null
     */
    public static function autoLoad()
    {
        spl_autoload_register(array('Load', 'loadCommon'));
        spl_autoload_register(array('Load', 'loadModel'));
        spl_autoload_register(array('Load', 'loadTool'));
        spl_autoload_register(array('Load', 'loadControl'));
        spl_autoload_register(array('Load', 'movieControl'));
    }

    /**
     * common类自动加载
     * 
     * @param
     *        类名
     * @return null
     */
    public static function loadCommon($class)
    {
        $path = COMMON_PATH . $class . '.class.' . EXT;
        if (is_file($path)) {
            include_once $path;
        }
    }

    /**
     * model类自动加载
     * 
     * @param
     *        类名
     * @return null
     */
    public static function loadModel($class)
    {
        $path = MODEL_PATH . $class . '.class.' . EXT;
        if (is_file($path)) {
            include_once $path;
        }
    }
    
    /**
     * tool类自动加载
     *
     * @param
     *        类名
     * @return null
     */
    public static function loadTool($class)
    {
        $path = TOOL_PATH . $class . '.class.' . EXT;
        if (is_file($path)) {
            include_once $path;
        }
    }
    
    /**
     * movie类自动加载
     *
     * @param
     *        类名
     * @return null
     */
    public static function movieControl($class)
    {
        $path = MOVIE_PATH . $class . '.class.' . EXT;
        if (is_file($path)) {
            include_once $path;
        }
    }
    
    /**
     * control类自动加载
     *
     * @param
     *        类名
     * @return null
     */
    public static function loadControl($class)
    {
        $path = CONTROL_PATH . $class . '.class.' . EXT;
        if (is_file($path)) {
            include_once $path;
        }
    }
    
}

?>