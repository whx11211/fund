<?php 

/**
 * LoginModel 登录管理类
 *
 * @copyright Copyright (c) 2017-2018
 * @author whx
 * @version ver 1.0
 */

class FundInfoModel extends Model
{
    /**
     * 表名
     * string
     */
    protected $table = 'fund_info';

    public function checkCodeExists($code)
    {
        return (bool)$this->select('code')->where('code=?', [$code])->getAll();
    }
}

?>