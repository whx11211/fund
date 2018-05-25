<?php

/**
 * 访问控制类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Access
{
    /**
     * 不需验证登录的接口数组
     */
    private static $login_except = array(
        'System/login'
    );

    /**
     * 不需验证的权限的接口数组
     */
    private static $pri_except = array(
        'Common/*',
        'System/menu',
        'System/userInfo',
        'System/loginOut'
    );
    
    /**
     * 访问控制
     * 
     */
    public static function check($class, $method)
    {
        //不需验证登录
        if (in_array($class . '/' . $method, self::$login_except) || in_array($class . '/*', self::$login_except)) {
            return true;
        }
        
        //验证登录
        $admin_userinfo = SESSION::get('admin_user');
        if (!$admin_userinfo) {
            Output::fail(ErrorCode::NEED_LOGIN);
        }
        
        //不需验证权限
        if (in_array($class . '/' . $method, self::$pri_except) || in_array($class . '/*', self::$pri_except)) {
            return true;
        }
        
        //验证权限
        if (in_array($class . '/' . $method, $admin_userinfo['pri'])) {
            return true;
        }
        
        Output::fail(ErrorCode::PRIVILEGE_ERROR);
    }
    
}

?>