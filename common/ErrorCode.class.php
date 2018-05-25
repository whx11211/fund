<?php

/**
 * 错误常量配置
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class ErrorCode
{
    //未登录/登录失效
    const NEED_LOGIN = 1001;
    //用户名错误
    const LOGINNAME_ERROR = 1002;
    //密码错误
    const LOGINPWD_ERROR = 1003;
    //没有权限
    const PRIVILEGE_ERROR = 1004;

    //操作类不存在
    const CLASS_NOT_EXISTS = 1101;
    //操作方法不存在
    const METHOD_NOT_EXISTS = 1102;
    
    const PARAM_ERROR = 1201;
    

}

?>