<?php 

/**
 * Model基类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class Model
{
    /**
     * 数据量连接指针
     * rescource
     */
    protected $db;
    
    /**
     * 数据量连接指针
     * rescource
     */
    protected $cache;
    
    /**
     * 日志记录路径
     * string
     */
    protected $logfile = 'db_model_sql';
    
    /**
     * 表名
     * string
     */
    protected $table = null;
    
    /**
     * 查询索引
     * string
     */
    protected $select = '*';
    
    /**
     * 查询条件
     * string
     */
    protected $where = '';

    /**
     * 查询条数
     * string
     */
    protected $limit = '';

    /**
     * 分组条件
     * string
     */
    protected $group = '';

    /**
     * having条件
     * string
     */
    protected $having = '';

    /**
     * join联合查询
     * string
     */
    protected $join = null;
    
    /**
     * join联合查询
     * string
     */
    protected $order = null;
    
    /**
     * execute数组
     * array
     */
    protected $execute = null;
    
    /**
     * 构造函数
     */
    public function __construct($table=null, $type=DB_TYPE, $name=DB_NAME, $ip=DB_IP, $port=DB_PORT, $user=DB_USER, $pwd=DB_PWD)
    {
        $this->db = DataBase::getInstance($type, $name, $ip, $port, $user, $pwd);
        if ($table) {
           $this->table = $table;
        }
       
        //$this->cache = new RedisCache();
    }
    
    /**
     * 获取表名
     * 
     */
    public function getTableName()
    {
        return $this->table;
    }
    
    /**
     * 记录日志
     */
    protected function addLog($type, $sql, $execute=[])
    {
        if (defined('DB_DEBUG') && DB_DEBUG) {
            LogFile::addLog($type, array($sql, json_encode($execute)), $this->logfile);
        }
    }
    
    /**
     * 设置查询索引
     * @param string|arrray
     * @return object 对象自身
     */
    public function select($select_key)
    {
        if (is_array($select_key)) {
            $this->select = implode(',', $select_key);
        }
        else {
            $this->select = $select_key;
        }
        
        return $this;
    }
    
    /**
     * 设置查询条件
     * @param string|array  $where_key，
     *      数组只持支 ‘=’ 查询条件，格式：array('id'=>1, 'group'=>2)
     *      其它查询条件如“>,<,in,between”需自己拼接字符串
     * @param array  $execute_key
     * @return object 对象自身
     */
    public function where($where_key, $execute_key=null)
    {
        if (is_scalar($execute_key)) {
            $execute_key = [$execute_key];
        }
        if (is_array($where_key)) {
            $tmp_array = $tmp_execute = array();
            foreach ($where_key as $key => $v) {
                if (!is_array($v)) {
                    $tmp_array[] = "`$key`=?";
                    $tmp_execute[] = $v;
                }
                else {
                    foreach ($v as $type => $vv) {
                        switch ($type) {
                            case 'like':
                                $tmp_array[] = "`$key` like ?";
                                $tmp_execute[] = $vv;
                                break;
                            case 'between':
                                $tmp_array[] = "`$key` between ? AND ?";
                                $tmp_execute[] = $vv[0];
                                $tmp_execute[] = $vv[1];
                                break;
                            case 'in':
                                $tmp = substr(str_repeat('?,', count($vv)), 0 ,-1);
                                $tmp_array[] = "`$key` in ($tmp)";
                                $tmp_execute = array_merge($tmp_execute, $vv);
                                break;
                        }
                        break;
                    }
                }
            }
            $this->where = implode(' AND ', $tmp_array);
            $this->execute = $tmp_execute;
        }
        else {
            $this->where = $this->where ? $this->where . ' and ' . $where_key : $where_key;
            if ($execute_key) {
                $this->execute = $this->execute ? array_merge($this->execute, $execute_key) : $execute_key;
            }

        }
        
        return $this;
    }

    /**
     * 设置查询条数
     * @param string|arrray
     * @return object 对象自身
     */
    public function limit($limit_key)
    {
        if (is_array($limit_key)) {
            $this->limit = implode(',', $limit_key);
        }
        else {
            $this->limit = $limit_key;
        }
        
        return $this;
    }

    /**
     * 设置分组条件
     * @param string|arrray
     * @return object 对象自身
     */
    public function group($group_key)
    {
        if (is_array($group_key)) {
            $this->group = implode(',', $group_key);
        }
        else {
            $this->group = $group_key;
        }
        
        return $this;
    }

    /**
     * 设置having条件
     * @param string|arrray
     * @return object 对象自身
     */
    public function having($having_key)
    {
        if (is_array($having_key)) {
            $tmp_array = array();
            foreach ($having_key as $key => $v) {
                $tmp_array[] = "`$key` = '$v'";
            }
            $this->having = implode(' AND ', $tmp_array);
        }
        else {
            $this->having = $having_key;
        }
        
        return $this;
    }
    
    /**
     * 设置join条件
     * @param string
     * @return object 对象自身
     */
    public function join($join_key)
    {
        $this->join = $join_key;
        
        return $this;
    }
    
    /**
     * 设置join条件
     * @param string
     * @return object 对象自身
     */
    public function order($order_key)
    {
        if (is_array($order_key)) {
            $order_ary = array();
            foreach ($order_key as $k => $v) {
                $order_ary[] = "$k $v";
            }
            $this->order = implode(',', $order_ary);
        }
        else {
            $this->order = $order_key;
        }
    
        return $this;
    }
    
    /**
     * 清除之前设置的参数
     * @return object 对象自身
     */
    public function clean()
    {
        $this->select = '*';
        $this->from = '';
        $this->where = '';
        $this->limit = '';
        $this->having = '';
        $this->join = null;
        $this->order = null;
        $this->execute = null;
    
        return true;
    }
    
    /**
     * 设置查询条件
     * @return array|false
     */
    public function getAll()
    {
        $sql = 'SELECT ' . $this->select . ' FROM ' . $this->table;
        if ($this->join) {
            $sql .= ' ' . $this->join;
        }
        if ($this->where) {
            $sql .= ' WHERE ' . $this->where;
        }
        if ($this->group) {
            $sql .= ' GROUP BY ' . $this->group;
        }
        if ($this->having) {
            $sql .= ' HAVING ' . $this->having;
        }
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        
        if ($this->execute) {
            // 需要预处理
            $rs = $this->db->prepare($sql);
            $rs->execute($this->execute);
        }
        else {
            // 不需要预处理
            $rs = $this->db->query($sql);
        }

        // 记录日志
        $this->addLog('SELECT', $sql, $this->execute);
        
        // 清理查询参数
        $this->clean();
        
        
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 直接查询
     * @param string $sql sql语句
     * @param array $execute 预处理数组
     * @return array|false
     */
    public function getSqlResult($sql, $execute=array())
    {
        if ($execute) {
            // 需要预处理
            $rs = $this->db->prepare($sql);
            $rs->execute($execute);
        }
        else {
            // 不需要预处理
            $rs = $this->db->query($sql);
        }
    
        // 记录日志
        $this->addLog('SELECT', $sql, $execute);
        
        return $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 直接执行
     * @param string $sql sql语句
     * @param array $execute 预处理数组
     * @return array|false
     */
    public function execSql($sql, $execute=array())
    {
        if ($execute) {
            // 需要预处理
            $rs = $this->db->prepare($sql);
            $res = $rs->execute($execute);
        }
        else {
            // 不需要预处理
            $res = $rs = $this->db->query($sql);
        }

        // 记录日志
        $this->addLog('SELECT', $sql, $execute);

        return $rs;

    }
    
    /**
     * 插入（带预处理）
     * @param array $data 插入数组
     * @param srting $type 插入类型 1：普通insert  2：ignore   3：replace
     * @return int 插入的ID
     */
    public function insert($data, $type=1)
    {
        $key = $val = '';
        foreach ($data as $k=> $v) {
            $key = $key ? $key . ',' . "`$k`" : "`$k`";
            $val = $val ? $val . ", :" . $k : ":" . $k;
        }
        $st = 'INSERT INTO ';
        if ($type == 3) {
            $st = 'REPLACE INTO ';
        }
        else if ($type == 2) {
            $st = 'INSERT IGNORE ';
        }
        $sql = $st . $this->table . ' (' . $key . ') values (' . $val . ');';
        $rs = $this->db->prepare($sql);
        $res = $rs->execute($data);
    
        // 记录日志
        $this->addLog('INSERT', $sql, $data);
    
        return $this->db->lastInsertId();
    }
    
    /**
     * 批量插入（无预处理）
     * @param array $datas 插入数组,顺序必须一致匹配
     * @return int 插入的ID
     */
    public function batchInsert($datas, $type=1)
    {
        $keys = array_keys($datas[0]);
        foreach ($keys as $k => $v) {
            $keys[$k] = "`$v`";
        }
        $key = implode(',', $keys);
        $val = array();
        foreach ($datas as $data) {
            $val[] = '(\'' . implode('\',\'', $data) . '\')';
        }
    
        $st = 'INSERT INTO ';
        if ($type == 3) {
            $st = 'REPLACE INTO ';
        }
        else if ($type == 2) {
            $st = 'INSERT IGNORE ';
        }
        $sql = $st . $this->table . ' (' . $key . ') values ' . implode(',', $val) . ';';
        $rs = $this->db->prepare($sql);
        $res = $rs->execute();
    
        // 记录日志
        $this->addLog('INSERT', $sql);
    
        return $res;
    }
    
    /**
     * 删除（带预处理）
     * @param array $cond_ary 查询条件
     * @return bool
     */
    public function delete($cond_ary)
    {
        $tmp_array = array();
        foreach ($cond_ary as $key => $v) {
            $tmp_array[] = "`$key`=:$key";
        }
        $cond = implode(' AND ', $tmp_array);

        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $cond . ';';
        $rs = $this->db->prepare($sql);
    
        // 记录日志
        $this->addLog('DELETE', $sql, $cond_ary);
    
        return $rs->execute($cond_ary);
    }
    
    /**
     * 修改（带预处理）
     * @param array $data 插入数组
     * @return int 插入的ID
     */
    public function update($data, $where_key)
    {
        $tmp_array = array();
        foreach ($data as $key => $v) {
            $tmp_array[] = "`$key`=:$key";
        }
        $set = implode(',', $tmp_array);
        
        $tmp_array = array();
        foreach ($where_key as $key => $v) {
            $tmp_array[] = "`$key`=" . $this->db->quote($v);
        }
        $cond = implode(' AND ', $tmp_array);
    
        $sql = 'UPDATE ' . $this->table . ' SET ' . $set . ' WHERE ' . $cond . ';';
        $rs = $this->db->prepare($sql);
    
        // 记录日志
        $this->addLog('UPDATE', $sql, $data);
    
        return $rs->execute($data);
    }
    

    /**
     * （通用）从数据库查询数据，不带缓存
     *
     * @param array $where_args
     *        查询条件
     * @param array $order_args
     *        排序条件
     * @param int $page
     *        页数
     * @param int $num
     *        每页显示
     */
    public function getByCondFromDb($select_strings = '*', $where_args, $page = 1, $num = 10, $orderby_args = array())
    {
        $total = $this->select('count(*) as sum');
        if ($where_args) {
            $total = $total->where($where_args);
        }
        $total = $total->getAll();
    
        $page_total = ceil($total[0]['sum'] / $num);
        if ($page > $page_total && $page_total > 0) {
            $page = $page_total;
        }
    
        $data = $this->select($select_strings);
        if ($where_args) {
            $data = $data->where($where_args);
        }
        if (is_array($orderby_args) && $orderby_args) {
            $data = $data->order($orderby_args);
        }
        
        $data->limit(array(($page - 1) * $num, $num));

        $data = $data->getAll();
        
        $res = array(
            'page_current'  =>  $page,
            'items_per_page'=>  $num,
            'page_total'    =>  $page_total,
            'items'         =>  $data,
        );
    
        return $res;
    }
    
    /**
     * （通用）从数据库删除数据，不带缓存
     *
     * @param array $where_args
     *        删除条件
     */
    public function delByCondFromDb($where_args)
    {
        return $this->delete($where_args);
    }
    
    /**
     * （通用）从数据库新增数据，不带缓存
     *
     * @param array $where_args
     *        删除条件
     */
    public function insertByCondFromDb($insert_values)
    {
        return $this->insert($insert_values);
    }
    
    /**
     * （通用）从数据库更新数据，不带缓存
     *
     * @param array $update_args
     *        更新数组
     * @param array $where_args
     *        更新条件
     *        删除条件
     */
    public function updateByCondFromDb($update_args, $where_args)
    {
        return $this->update($update_args, $where_args);
    }
    

}

?>