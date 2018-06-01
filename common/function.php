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

function curl($url, $post=null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HEADER, 0);  //设置头信息
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//重定向
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($post) ? http_build_query($post) : $post);
    }
    $error = curl_error($ch);
    if ($error) {
        Output::fail($error);
    }
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

?>