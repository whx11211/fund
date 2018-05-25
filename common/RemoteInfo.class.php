<?php

/**
 * 获取远程客户端信息
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class RemoteInfo
{

    /**
     * 客户端IP地址
     */
    private static $_ip;

    /**
     * 获取get参数
     *
     * @param string $key 键值 （不传参数时返回$_GET数组）
     * @return string|array|null
     */
    public static function get($key=null)
    {
        return self::_getHttpParam('GET', $key);
    }

    /**
     * 获取post参数
     *
     * @param string $key 键值 （不传参数时返回$_POST数组）
     * @return string|array|null
     */
    public static function post($key=null)
    {
        return self::_getHttpParam('POST', $key);
    }

    /**
     * 获取request参数
     *
     * @param string $key 键值 （不传参数时返回$_REQUEST数组）
     * @return string|array|null
     */
    public static function request($key=null)
    {
        return self::_getHttpParam('REQUEST', $key);
    }

    /**
     * 获取cookie参数/cookie数组
     *
     * @param string $key 键值 （不传参数时返回$_COOKIE数组）
     * @return string|array|null
     */
    public static function cookie($key=null)
    {
        return self::_getHttpParam('COOKIE', $key);
    }

    /**
     * 获取server参数/server数组
     *
     * @param string $key 键值 （不传参数时返回$_SERVER数组）
     * @return string|array|null
     */
    public static function server($key=null)
    {
        return self::_getHttpParam('SERVER', $key);
    }

    /**
     * 获取Http参数值
     *
     * @param $type string http参数类型（如GET，POST，REQUEST，COOKIE，SERVER等）
     * @param $key string 键值
     * @return string|array|null
     */
    private static function _getHttpParam($type, $key)
    {
        switch ($type) {
            case "GET":
                $tmp = & $_GET;
                break;
            case "POST":
                $tmp = & $_POST;
                break;
            case "REQUEST":
                $tmp = & $_REQUEST;
                break;
            case "COOKIE":
                $tmp = & $_COOKIE;
                break;
            case "SERVER":
                $tmp = & $_SERVER;
                break;
            case "POST":
                $tmp = & $_POST;
                break;
        }
        
        if (is_null($key)) {
            //未设置键值，返回整个数组
            return $tmp;
        }
        
        if (isset($tmp[$key])) {
            return $tmp[$key];
        }
        
        return null;
    }

    /**
     * 获取客户端IP地址
     *
     * @param null
     * @return string
     */
    public static function getIP()
    {
        if (self::$_ip) {
            return self::$_ip;
        }
    
        if (getenv('HTTP_CLIENT_IP')) {
            self::$_ip = getenv('HTTP_CLIENT_IP');
        }
        elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            self::$_ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_X_FORWARDED')) {
            self::$_ip = getenv('HTTP_X_FORWARDED');
        }
        elseif (getenv('HTTP_FORWARDED_FOR')) {
            self::$_ip = getenv('HTTP_FORWARDED_FOR');
    
        }
        elseif (getenv('HTTP_FORWARDED')) {
            self::$_ip = getenv('HTTP_FORWARDED');
        }
        else {
            self::$_ip = $_SERVER['REMOTE_ADDR'];
        }
    
        return self::$_ip;
    }

    /**
     * 获取客户端浏览器信息 2016
     * @param  null
     * @return string
     */
    public static function getBroswer(){
        $sys = self::server('HTTP_USER_AGENT');  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif(stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        }else {
            $exp[0] = "unknown browser";
            $exp[1] = "";
        }
        return $exp[0].'('.$exp[1].')';
    }

    /**
     * 获取客户端操作系统信息包括win10 2016
     * 
     * @param
     *        null
     * @return string
     */
    public static function getOs()
    {
        $agent = self::server('HTTP_USER_AGENT');
        $os = false;
        
        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        }
        else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10'; #添加win10判断
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        }
        else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        }
        else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        }
        else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        }
        else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        }
        else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        }
        else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
            $os = 'Macintosh';
        }
        else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        }
        else if (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        }
        else if (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        }
        else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        }
        else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        }
        else if (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        }
        else if (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        }
        else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        }
        else if (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        }
        else if (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        }
        else if (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        }
        else if (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        }
        else {
            $os = 'unknown os';
        }
        return $os;
    }
    

    /**
     * 把表单(request)数据转化为标准查询条件
     *
     * @param array $request_conf_arys
     *        array(
     *        request_key_1 => array(
     *        array(type, para, error),
     *
     *        array('transform', array(class, method), error),
     *        array('auto', array(class, method), error),
     *
     *        'skip_value' => array("", null),
     *        'skip_check' => array("", null),
     *        ),
     *        request_key_2 => array(
     *        array(type, para, error),
     *        )
     *        )
     */
    public static function getSearchFormArgs($request_conf_arys)
    {
        $data = array();
        foreach ($request_conf_arys as $request_key => $request_confs) {
    
            // 获取值
            $request_value = self::request($request_key);
            if (strlen($request_value) == 0) {
                continue;
            }
    
            switch ($request_confs[0]) {
                case 'int': // 强转int
                    $request_value = (int)$request_value;
                    break;
                case 'bool': // 强转boolean
                    $request_value = (bool)$request_value;
                    break;
                case 'rename': // 数据key改名
                    $request_key = $request_confs[1];
                    break;
                case 'transform': // 数据格式化转换
                case 'auto': // 自动生成数据
                    try {
                        $request_value = call_user_func_array($request_confs[1], array($request_value));
                    }
                    catch (Exception $e) {
                        Output::fail($request_confs[2], $request_key);
                    }
                    break;

                default:
                    // 数据检查
                    if (!self::check($request_value, $request_confs[1], $request_confs[0])) {
                        Output::fail($request_confs[2], $request_key);
                    }
                    break;
            }
            $data[$request_key] = $request_value;
        }
        return $data;
    }
    
    /**
     * 把表单(request)数据转化为标准插入/修改数据
     *
     * @param array $request_conf_arys
     *        array(
     *        request_key_1 => array(
     *        array(type, para, error),
     *
     *        array('transform', array(class, method), error),
     *        array('auto', array(class, method), error),
     *
     *        'skip_value' => array("", null),
     *        'skip_check' => array("", null),
     *        ),
     *        request_key_2 => array(
     *        array(type, para, error),
     *        )
     *        )
     */
    public static function getInsertFormArgs($request_conf_arys)
    {
        $data = array();
        foreach ($request_conf_arys as $request_key => $request_confs) {
    
            // 获取值
            $request_value = self::request($request_key);
            if (isset($request_confs[3]) && $request_confs[3]) {
                if($request_confs[3] == 'null_skip' && strlen($request_value) == 0) {
                    continue;
                }
            }
    
            switch ($request_confs[0]) {
                case 'int': // 强转int
                    $request_value = (int)$request_value;
                    break;
                case 'bool': // 强转boolean
                    $request_value = (bool)$request_value;
                    break;
                case 'rename': // 数据key改名
                    $request_key = $request_confs[1];
                    break;
                case 'transform': // 数据格式化转换
                case 'auto': // 自动生成数据
                    try {
                        $request_value = call_user_func_array($request_confs[1], array($request_value));
                    }
                    catch (Exception $e) {
                        Output::fail($request_confs[2], $request_key);
                    }
                    break;
    
                default:
                    // 数据检查
                    if (!self::check($request_value, $request_confs[1], $request_confs[0])) {
                        Output::fail($request_confs[2], $request_key);
                    }
                    break;
            }
            $data[$request_key] = $request_value;
        }
        return $data;
    }
    
    /**
     * 使用正则验证数据
     *
     * @access public
     * @param string $value
     *        要验证的数据
     * @param string $rule
     *        验证规则
     * @return boolean
     */
    public static function regex($value, $rule)
    {
        $validate = array(
            'require'   =>  '/\S+/',
            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency'  =>  '/^\d+(\.\d+)?$/',
            'number'    =>  '/^\d+$/',
            'zip'       =>  '/^\d{6}$/',
            'integer'   =>  '/^[-\+]?\d+$/',
            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
            'english'   =>  '/^[A-Za-z]+$/',
        );
        // 检查是否有内置的正则表达式
        if (isset($validate[strtolower($rule)]))
            $rule = $validate[strtolower($rule)];
        return preg_match($rule, $value) === 1;
    }
    
    /**
     * 验证数据 支持 in between equal length regex expire ip_allow ip_deny
     *
     * @access public
     * @param string $value
     *        验证数据
     * @param mixed $rule
     *        验证表达式
     * @param string $type
     *        验证方式 默认为正则验证
     * @return boolean true：不返回错误；false: 验证失败，输出错误
     */
    public static function check($value, $rule, $type = 'regex')
    {
        $type = strtolower(trim($type));
        switch ($type) {
            case 'in': // 验证是否在某个指定范围之内 逗号分隔字符串或者数组
            case 'notin':
                $range = is_array($rule) ? $rule : explode(',', $rule);
                return $type == 'in' ? in_array($value, $range) : !in_array($value, $range);
            case 'between': // 验证是否在某个范围
            case 'notbetween': // 验证是否不在某个范围
                if (is_array($rule)) {
                    $min = $rule[0];
                    $max = $rule[1];
                }
                else {
                    list($min, $max) = explode(',', $rule);
                }
                return $type == 'between' ? $value >= $min && $value <= $max : $value < $min || $value > $max;
            case 'equal': // 验证是否等于某个值
            case 'notequal': // 验证是否等于某个值
                return $type == 'equal' ? $value == $rule : $value != $rule;
            case 'length': // 验证长度
                $length = mb_strlen($value, 'utf-8'); // 当前数据长度
                if (is_array($rule)) { // 长度区间
                    list($min, $max) = $rule;
                    return $length >= $min && $length <= $max;
                }
                else { // 指定长度
                    return $length == $rule;
                }
            case 'func':
                return call_user_func_array($rule, array($value));
            case 'regex':
            default: // 默认使用正则验证 可以使用验证类中定义的验证名称
                // 检查附加规则
                return self::regex($value, $rule);
        }
    }
}


?>