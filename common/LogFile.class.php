<?php 
/**
 * 自动加载类
 *
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

class LogFile
{
    /**
     * 单个日志文件的大小限制
     * 单位：Mb
     */
    public static $max_size = 10;
    
    /**
     * 日志文件后缀名
     */
    public static $log_ext = 'log';
    
    /**
     * 写入日志
     * @param $msg string|array 记录的内网
     * @param $dir 文件相对路径目录
     * @return int|false 写入的字节
     */
    public static function addLog ($title, $msg, $filename='')
    {
        $dir = date('Y-m-d').'/';
        //获取写入文件路径文件
        $path = self::getWriteFileName(LOG_PATH . $dir, $filename);
        $fp = fopen($path, "a");
        $wstring = self::getWriteString($title, $msg);
        return fwrite($fp, $wstring);
    }
    
    /**
     * 获取可写入的文件路径
     * @param $dir 相对目录路径
     * @return string 写入的文件路径
     */
    public static function getWriteFileName ($dir, $filename) 
    {
        if (!is_dir($dir)) {
            // 如果不是目录则创建之
            mkdir($dir, 0777, 1);
        }
        $path = $dir . '/' . $filename . '_' . date('Y-m-d') . '.' . self::$log_ext;
        if (is_file($path)) {
            //清除filesize的缓存
            clearstatcache();
            
            if (filesize($path) > self::$max_size * 1024 * 1024)
            {
                //日志是过大
                $bak = self::getBakFileName($dir);
                rename($path, $bak);
            }
        }
        
        if (!is_file($path)) {
            //如果不存在，尝试创建文件
            touch($path);
        }
        return $path;
    }
    
    /**
     * 生成备份的文件路径
     * @param $dir 文件目录路径
     * @return string 写入的文件路径
     */
    public static function getBakFileName ($dir)
    {
        $bak = $dir . '/' . date('Y-m-d--b\ak--H-i-s') . '--' . mt_rand(10000,99999) . '.' . self::$log_ext;
        if (is_file($bak)) {
            // 文件已存在，重新生成
            $bak = self::getBakFileName($dir);
        }
        return $bak;
    }
    
    /**
     * 整理需要写入内容
     * @param $title string 记录标题
     * @param $msg string|array 记录的内网
     * @return string 写入的内容
     */
    public static function getWriteString ($title, $msg)
    {
        $tmp_arr = array (
            "[" . $title . "]",
            date('Y-m-d H:i:s'),
        );
        
        if (is_array($msg)) {
            $tmp_arr = array_merge($tmp_arr, $msg);
        }
        else {
            $tmp_arr[] = $msg;
        }
        
        $tmp_arr[] = RemoteInfo::getIP();
        $tmp_arr[] = json_encode(RemoteInfo::request());
        
        return implode("\t", $tmp_arr) . "\r\n";
    }
}


?>