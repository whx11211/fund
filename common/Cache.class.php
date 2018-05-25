<?php

/**
 * 缓存抽象类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

abstract class Cache
{
    /**
     * 默认过期时间 单位s
     */
    const EXPIRE_TIME = 10800;
    
    /**
     * 连接实例
     */
    protected $_cache;

    /**
     * 构造函数
     * 
     * @param $host 缓存HOST
     * @param $port 缓存端口
     */
    public function __construct($host = CACHE_HOST, $port = CACHE_PORT)
    {
    }

    /**
     * 设置缓存
     */
    abstract public function set($key, $val, $expire);
    
    /**
     * 获取缓存
     */
    abstract public function get($key);
    
    /**
     * 删除缓存
     */
    abstract public function del($key);

    /**
     * 清空缓存
     */
    abstract public function flush($key);
    
    /**
     * 设置锁状态
     */
    abstract public function setState($key);
    
    /**
     * 解除锁状态
    */
    abstract public function unsetState($key);
    
    /**
     * 加锁
     */
    abstract public function lock($key);
    
    /**
     * 解锁
     */
    abstract public function unlock($key);
    
    
    
    /**
     * 魔术方法 可以直接调用 $_cache类方法
     */
    protected function __call($name, $arguments)
    {
        return call_user_func_array(array($this->_cache, $name), $arguments);
    }
}

?>