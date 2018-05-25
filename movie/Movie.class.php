<?php 

/**
 * Movie基类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Movie extends Model
{
    /**
     * 构造函数
     */
    public function __construct($table=null, $type=MOVIE_DB_TYPE, $name=MOVIE_DB_NAME, $ip=MOVIE_DB_IP, $port=MOVIE_DB_PORT, $user=MOVIE_DB_USER, $pwd=MOVIE_DB_PWD)
    {
        parent::__construct($table, $type, $name, $ip, $port, $user, $pwd);
    }

}

?>