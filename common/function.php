<?php 
/**
 * 常用函数
 * 
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

//获取$_GET参数
function get($key){
    return isset($_GET[$key]) ? $_GET[$key] : null;
}

//获取$_POST参数
function post($key){
    return isset($_POST[$key]) ? $_POST[$key] : null;
}

//获取$_REQUEST参数
function request($key){
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
}

?>