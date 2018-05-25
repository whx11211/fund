<?php

/**
 * Redis缓存类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class RedisCache extends Cache
{
    /**
     * 构造函数
     * 
     * @param $host 缓存HOST
     * @param $port 缓存端口
     */
    public function __construct($host = CACHE_HOST, $port = CACHE_PORT)
    {
        $this->_cache = new redis();
        $this->_cache->connect(CACHE_HOST, CACHE_PORT);
    }

    /**
     * 设置缓存
     */
    public function set($key, $val, $expire=self::EXPIRE_TIME)
    {
        return $this->_cache->setex($key, $expire, $val);
    }
    
    /**
     * 获取缓存
     */
    public function get($key) {
        return $this->_cache->get($key);
    }
    
    /**
     * 删除缓存
     */
    public function del($key) {
        return $this->_cache->del($key);
    }

    /**
     * 清空缓存
     */
    public function flush($key) {
        return $this->_cache->flushall($key);
    }
    
    /**
     * 设置锁状态
     */
    public function setState($key)
    {
        $key = 'state_'.$key;
        return $this->_cache->setnx($key, 1);
    }
    
    /**
     * 解除锁状态
    */
    public function unsetState($key)
    {
        $key = 'state_'.$key;
        return $this->_cache->del($key);
    }
    
    /**
     * 加锁
    */
    public function lock($key)
    {
        while(!$this->setState($key)) {
            sleep(1);
        }
        return true;
    }
    
    /**
     * 解锁
    */
    public function unlock($key)
    {
        return $this->unsetState($key);
    }
}

?>