<?php

/**
 * 通用输出类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Output
{

    /**
     * 正确返回
     * 
     * @param $data 返回数据
     */
    public static function success($data)
    {
        $res = array(
            'r'     =>  1,
            'data'  =>  $data
        );
        
        exit(json_encode($res));
    }

    /**
     * 错误返回
     * 
     * @param $error 返回数据
     */
    public static function fail($error, $msg=null)
    {
        $res = array(
            'r'     =>  0,
            'error' =>  $error,
        );
        if ($msg) {
            $res['msg'] = $msg;
        }

        exit(json_encode($res));
    }
}

?>