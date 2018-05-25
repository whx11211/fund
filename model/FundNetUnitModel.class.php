<?php 

/**
 * LoginModel 登录管理类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class FundNetUnitModel extends Model
{
    /**
     * 表名
     * string
     */
    protected $table = 'fund_net_unit';

    public function __construct($code)
    {
        $this->table .=  '_'.$code;

        parent::__construct();

        $this->createTableNotExists();
    }

    public function getUnitValueByDate($date)
    {
        return $this->select('unit_value')->where(['date'=>$date])->getAll()[0]['unit_value'] ?? 0;
    }

    private function createTableNotExists()
    {
        if (!$this->getSqlResult("show tables like '$this->table'")) {
            $this->execSql("create table {$this->table} like fund_net_unit");
        }
    }

}
