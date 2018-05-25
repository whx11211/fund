<?php

class Control
{
    /**
     * (通用)分页获取数据方法
     * 
     * @param $db_instance
     *        Model实例类
     * @param array $where_args
     *        查询条件数组
     */
    public function getPage($db_instance, $select_strings = '*', $where_args = array())
    {
        $page = (int)RemoteInfo::request('page');
        $num = (int)RemoteInfo::request('num');
        $orderby = RemoteInfo::request('orderby');
        
        $page = $page > 0 ? $page : 1;
        $num = $num > 0 ? $num : 10;
        $orderby_args = $orderby ? $orderby : array();
        
        Output::success($db_instance->getByCondFromDb($select_strings, $where_args, $page, $num, $orderby_args));
    }
    
    /**
     * (通用)删除数据方法
     * 
     * @param CVDB $db_instance
     *        CDbuser::getInstanceTable获取CVDB生成的实例类
     * @param array $where_args
     *        删除条件数组
     */
    public function del($db_instance, $where_args = array())
    {
        Output::success($db_instance->delByCondFromDb($where_args));
    }

    /**
     * (通用)添加数据方法
     * 
     * @param CVDB $db_instance
     *        CDbuser::getInstanceTable获取CVDB生成的实例类
     * @param array $insert_data
     *        插入数据数组
     */
    public function add($db_instance, $insert_data)
    {
        Output::success($db_instance->insertByCondFromDb($insert_data));
    }

    /**
     * (通用)更新数据方法
     * 
     * @param CVDB $db_instance
     *        CDbuser::getInstanceTable获取CVDB生成的实例类
     * @param array $update_data
     *        更新数据数组
     * @param array $where_args
     *        更新数据条件数组
     */
    public function update($db_instance, $update_data, $where_args)
    {
        Output::success($db_instance->updateByCondFromDb($update_data, $where_args));
    }

//     /**
//      * (通用)导出数据方法
//      * 
//      * @param CVDB $db_instance
//      *        CDbuser::getInstanceTable获取CVDB生成的实例类
//      * @param array $where_args
//      *        数据查询条件数组
//      * @param string $file
//      *        导出文件名
//      */
//     public function downloadPage($db_instance, $select_strings = '*', $where_args = array(), $file = 'download')
//     {
//         $orderby = RemoteInfo::request('orderby');
//         $orderby_args = $orderby ? $orderby : array();
        
//         $page = 1; // 初始化页数，1
//         $num = 3000; // 每次获取3000条记录写入文件
        
//         $rs = 1; // 是否还有数据需要读取
//         $file = $file . '_' . date('Y_m_d_H_i_s_ms') . '.csv'; // 导出文件（未压缩）
//         //$header = 0;
//         //ExportData::setCsvHeader(array('id', 'platid', 'userid', 'tm', 'ip', 'uri', 'param', 'res', 'success')); // 设置CSV导出标题
        

//         do {
//             $export_data = $db_instance->getByCondFromDb($select_strings, $where_args, $page, $num, $orderby_args);
//             if ($export_data['page_total']) { // 有数据
//                 // 存入数据
//                 ExportData::addCsv($file, $export_data['items']);
//                 // 指向下页
//                 ++$page;
                
//                 if ($export_data['page_total'] == $export_data['page_current']) {
//                     // 到达最后一页，跳出循环
//                     $rs = 0;
//                 }
//             }
//             else {
//                 // 没有数据，跳出循环
//                 Output::fail(CErrorCode::EXPORT_DATA_NULL);
//             }
//         }
//         while ($rs != 0);
        
//         if ($export_data['page_total']) {
//             // 尝试zip打包
//             $zip_file = ExportData::zip(ExportData::$folder . $file);
//             if ($zip_file !== false) {
//                 // 打包成功
//                 Output::success($zip_file);
//             }
//             // 打包失败
//             Output::success(ExportData::$folder . $file);
//         }
//         Output::fail(ErrorCode::EXPORT_FAILED);
//     }

//     /**
//      * 
//      * @param unknown $db_instance
//      * @param unknown $where_args
//      * @param string $file
//      */
//     public function downloadPageBySid($sid, $db_table, $select_strings = '*', $where_args = array(), $file = 'download')
//     {
//         $orderby = RemoteInfo::request('orderby');
//         $orderby_args = $orderby ? $orderby : array();
        
//         $page = 1; // 初始化页数，1
//         $num = 3000; // 每次获取3000条记录写入文件
        
//         $rs = 1; // 是否还有数据需要读取
//         $file = $file . '_' . date('Y_m_d_H_i_s_ms') . '.csv'; // 导出文件（未压缩）
//         //$header = 0;
//         //ExportData::setCsvHeader(array('id', 'platid', 'userid', 'tm', 'ip', 'uri', 'param', 'res', 'success')); // 设置CSV导出标题
    
//         $sids = array($sid);
//         do {
            
//             $post['cmds'] = array();
//             $post['cmds'][] = array(
//                 'cmd_type' => APICMD::SQL_SELECT,
//                 'cmd_para' => array(
//                     'db' => APICMD::DB_TABLE_GAME,
//                     'sql' => Sql::select('count(1) as sum')->from($db_table)->whereArgs($where_args)->getContextSerialize(),
//                 )
//             );
            
//             $sql = Sql::select($select_strings)->from($db_table)->whereArgs($where_args);
//             if (is_array($orderby_args) && $orderby_args) {
//                 $sql = $sql->orderByArgs($orderby_args);
//             }
//             $sql = $sql->limit(($page - 1) * $num, $num)->getContextSerialize();
            
//             $post['cmds'][] = array(
//                 'cmd_type' => APICMD::SQL_SELECT,
//                 'cmd_para' => array(
//                     'db' => APICMD::DB_TABLE_GAME,
//                     'sql' => $sql,
//                 )
//             );
            
//             $api_res = GetFromGSAPI::post('gapi.api', $sids, $where_args['platid'], $post);
            
//             if ($api_res[$sid]['r']) {
//                 // 返回数据整理
//                 $total = $api_res[$sid]['data'][0];
//                 $data = $api_res[$sid]['data'][1];
            
//                 $page_total = ceil($total[0]['sum'] / $num);
//                 if ($page > $page_total && $page_total > 0) {
//                     $page = $page_total;
//                 }
            
//                 $export_data = Pagination::formatData($page, ceil($total[0]['sum'] / $num), $num, $total[0]['sum'], $data);
                
//                 if ($export_data['page_total']) { // 有数据
//                     // 存入数据
//                     ExportData::addCsv($file, $export_data['items']);
//                     // 指向下页
//                     ++$page;
                
//                     if ($export_data['page_total'] == $export_data['page_current']) {
//                         // 到达最后一页，跳出循环
//                         $rs = 0;
//                     }
//                 }
//                 else {
//                     // 没有数据，跳出循环
//                     Output::fail(CErrorCode::EXPORT_DATA_NULL);
//                 }
//             }
//             else {
//                 // 获取不到数据，跳出循环
//                 $rs = 0;
//             }
//         }
//         while ($rs != 0);
    
//         if ($export_data['page_total']) {
//             // 尝试zip打包
//             $zip_file = ExportData::zip(ExportData::$folder . $file);
//             if ($zip_file !== false) {
//                 // 打包成功
//                 Output::success($zip_file);
//             }
//             // 打包失败
//             Output::success(ExportData::$folder . $file);
//         }
//         Output::fail(ErrorCode::EXPORT_FAILED);
//     }
    
    /**
     * 生成随机的md5码
     * 
     * @return string
     */
    public function make_md5()
    {
        return md5(RemoteInfo::server('REMOTE_ADDR') . microtime(1) . rand(10000, 99999));
    }

    /**
     * 时间范围格式化（查询条件）
     * 
     * @param string $date
     *        如'2017-09-21 - 2017-10-20'
     * @return array(betwwenn=>array(tm1, tm2)) || null
     */
    public function betweenTimeFormat($date, $separate = ' - ')
    {
        $time_explode_ary = explode($separate, $date);
        if (count($time_explode_ary) == 2) {
            $tm_start = strtotime($time_explode_ary[0]);
            $tm_end = strtotime($time_explode_ary[1]);
            if ($tm_start && $tm_end) {
                return array('between' => array($tm_start, $tm_end + 3600 * 24 - 1));
            }
        }
        
        return null;
    }

    /**
     * ip格式化（查询条件）
     * 
     * @param string $ip        
     * @return array(like=>%ip%) || null
     */
    public function ipFormat($ip)
    {
        if ($ip) {
            return array('like' => '%' . $ip . '%');
        }
        
        return null;
    }
    
    /**
     * 模糊查询格式化（查询条件）
     *
     * @param string $ip
     * @return array(like=>%ip%) || null
     */
    public function likeFormat($val)
    {
        if ($val) {
            return array('like' => '%' . $val . '%');
        }
    
        return null;
    }

    /**
     * uri格式化（查询条件）
     * 
     * @param string $uri        
     * @return array(like=>uri%) || null
     */
    public function uriFormat($uri)
    {
        if ($uri) {
            return array('like' => $uri . '%');
        }
        
        return null;
    }
}