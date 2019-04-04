<?php

class HandleControl extends Control
{

    public function setNetUnit()
    {
        $fund_infos = Instance::get('FundInfo')->getAll();

        foreach($fund_infos as $fund_info) {
            $method = "set".ucfirst($fund_info['company'])."NetUnit";
            if (method_exists($this, $method)) {
                $this->$method($fund_info['code'], $fund_info);
            }
        }
        Output::success('OK');
    }

    /**
     *  天虹基金
     */
    public function setTianhongNetUnit($code, $fund_info)
    {
        $url = 'http://www.thfund.com.cn/thfund/netvalue/'.$code;

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
            return true;
        }

        $res = file_get_contents($url);
        @$res = json_decode($res);

        $datas = [];

        foreach ($res as $val) {
            $date = date('Y-m-d', substr($val[0], 0, -3)-1);
            $v = $val[1];
            if ($date>$last_date) {
                $tmp = [
                    'date' => $date,
                    'unit_value' => $v,
                    'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                    'trend' => 0
                ];

                $count = count($before_days);
                if ($count==6) {
                    $before_days_tmp = $before_days;
                    sort($before_days_tmp);
                    if ($v <= $before_days_tmp[1]) {
                        $tmp['trend'] = -1;
                    }
                    else if ($v >= $before_days_tmp[4]) {
                        $tmp['trend'] = 1;
                    }
                }

                $datas[] = $tmp;

                $last_unit_value = $v;
                array_push($before_days, $v);
                if ($count+1>6) {
                    array_shift($before_days);
                }
            }
        }

        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }

    /**
     *  国泰基金
     */
    public function setGuotaiNetUnit($code, $fund_info)
    {
        $url = 'https://e.gtfund.com/Etrade/Jijin/navCombo/fundCode/%s/start/%s/end/%s';

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        $start = $end = date('Y-m-d', strtotime('-1 day'));
        if (!$last_date) {
            //默认最多拉取近50年数据
            $start = date('Y-m-d', strtotime('-50 years'));
        }
        else if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
            return true;
        }
        else {
            list($last_year, $last_month) = explode('-', $last_date);
            $now_year = date('Y');
            $now_month = date('m');
            $start = date('Y-m-d', strtotime('+1 day', strtotime($last_date)));
        }

        $url = sprintf($url, $code, $start, $end);

        $res = file_get_contents($url);
        @$res = json_decode($res, true);

        $datas = [];

        if ($res['code']==0) {
            foreach ($res['data']['s2']['data'] as $k => $v) {
                $date = $res['data']['x'][$k];
                if ($date > $last_date) {
                    $tmp = [
                        'date' => $date,
                        'unit_value' => $v,
                        'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                        'trend' => 0
                    ];

                    $count = count($before_days);
                    if ($count==6) {
                        $before_days_tmp = $before_days;
                        sort($before_days_tmp);
                        if ($v <= $before_days_tmp[1]) {
                            $tmp['trend'] = -1;
                        }
                        else if ($v >= $before_days_tmp[4]) {
                            $tmp['trend'] = 1;
                        }
                    }

                    $datas[] = $tmp;

                    $last_unit_value = $v;
                    array_push($before_days, $v);
                    if ($count+1>6) {
                        array_shift($before_days);
                    }

                }
            }
        }


        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }

    /**
     *  易方达基金
     */
    public function setYifangdaNetUnit($code, $fund_info)
    {
        $url = "https://static.efunds.com.cn/market/2.0/{$code}_cy.js?r=".date('YmdHi');

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
           // return true;
        }

        $res = file_get_contents($url);
        $res = explode(';', substr($res, 16, -3));
        if (substr($last_date, 0, 4) < date('Y')) {
            $url2 = "https://static.efunds.com.cn/market/2.0/his/{$code}_all.js?r=".date('YmdHi');
            $res2 = file_get_contents($url2);
            $res2 = explode(';', substr($res2, 17, -3));
            $res = array_merge($res2, $res);
        }

        $datas = [];

        foreach ($res as $val) {
            $val = explode('_', $val);
            if (count($val)==1 || $val[0]==1) {
                continue;
            }
            $date = date('Y-m-d', strtotime($val[0]));
            $v = $val[2];
            if ($date>$last_date) {
                $tmp = [
                    'date' => $date,
                    'unit_value' => $v,
                    'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                    'trend' => 0
                ];

                $count = count($before_days);
                if ($count==6) {
                    $before_days_tmp = $before_days;
                    sort($before_days_tmp);
                    if ($v <= $before_days_tmp[1]) {
                        $tmp['trend'] = -1;
                    }
                    else if ($v >= $before_days_tmp[4]) {
                        $tmp['trend'] = 1;
                    }
                }

                $datas[] = $tmp;

                $last_unit_value = $v;
                array_push($before_days, $v);
                if ($count+1>6) {
                    array_shift($before_days);
                }
            }
        }

        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }


    /**
     *  鹏华基金
     */
    public function setPenghuaNetUnit($code, $fund_info)
    {
        $url = 'https://www.phfund.com.cn/web/fundproducts/getMsNetvalueAndNetvalue?pageInfo.pageSize=%d&pageInfo.sortField=e.fdate&pageInfo.sortDistanct=asc&pageInfo.currentPage=1&entity.fundId=%d&entity.isHistory=-1&beginEffectivedate=%s&endEffectivedate=';

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        $page_size = 36600*5;//一次最多拉50年数据
        $start = '';
        if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
            return true;
        }
        else if ($last_date) {
            $start = date('Y-m-d', strtotime('+1 day', strtotime($last_date)));
        }

        $url = sprintf($url, $page_size, $fund_info['param'], $start);

        $res = file_get_contents($url);
        @$res = json_decode($res, true);

        $datas = [];

        if ($res['code']==0) {
            foreach ($res['content']['list'] as $list) {
                $date = date('Y-m-d', strtotime($list['fdate']));
                $v = $list['fundnav'];
                if ($date > $last_date) {
                    $tmp = [
                        'date' => $date,
                        'unit_value' => $v,
                        'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                        'trend' => 0
                    ];

                    $count = count($before_days);
                    if ($count==6) {
                        $before_days_tmp = $before_days;
                        sort($before_days_tmp);
                        if ($v <= $before_days_tmp[1]) {
                            $tmp['trend'] = -1;
                        }
                        else if ($v >= $before_days_tmp[4]) {
                            $tmp['trend'] = 1;
                        }
                    }

                    $datas[] = $tmp;

                    $last_unit_value = $v;
                    array_push($before_days, $v);
                    if ($count+1>6) {
                        array_shift($before_days);
                    }

                }
            }
        }


        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }

    /**
     * 华夏基金
     */
    public function setHuaxiaNetUnit($code, $fund_info)
    {
        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        $url = 'http://www.chinaamc.com/fund/%s/zoust_all.js';

        $url = sprintf($url, $fund_info['code']);

        $res = file_get_contents($url);
        @$res = json_decode($res, true);

        $dates = [];
        if ($res) {
            $dates = json_decode(str_replace("'", '"', $res['ShowData']), true);
            $units = json_decode(str_replace("'", '"', $res['JingzhiName']), true);
        }

        $datas = [];

        foreach ($dates as $k=>$date) {
            $v = $units[$k] ?? 0;
            if ($date > $last_date) {
                $tmp = [
                    'date' => $date,
                    'unit_value' => $v,
                    'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                    'trend' => 0
                ];

                $count = count($before_days);
                if ($count==6) {
                    $before_days_tmp = $before_days;
                    sort($before_days_tmp);
                    if ($v <= $before_days_tmp[1]) {
                        $tmp['trend'] = -1;
                    }
                    else if ($v >= $before_days_tmp[4]) {
                        $tmp['trend'] = 1;
                    }
                }

                $datas[] = $tmp;

                $last_unit_value = $v;
                array_push($before_days, $v);
                if ($count+1>6) {
                    array_shift($before_days);
                }

            }
        }

        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }

    /**
     *  广发基金
     */
    public function setGuangfaNetUnit($code, $fund_info)
    {
        $url = 'http://www.gffunds.com.cn/apistore/JsonService?service=MarketPerformance&method=NAV&op=queryNAVByFundcode&fundcode=%s&_mode=all&startdate=%s&enddate=%s';

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        $start = $end = date('Ymd', strtotime('-1 day'));
        if (!$last_date) {
            //默认最多拉取近50年数据
            $start = date('Ymd', strtotime('-50 years'));
        }
        else if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
            return true;
        }
        else {
            $start = date('Ymd', strtotime('+1 day', strtotime($last_date)));
        }
        $url = sprintf($url, $code, $start, $end);

        $res = file_get_contents($url);
        @$res = json_decode($res, true);
        $res['data'] = array_column($res['data'] ?: [], 'NAVACCUMULATED', 'NAVDATE');
        ksort($res['data']);
        $datas = [];


        if ($res['errorno']==20000) {
            foreach ($res['data'] as $date=>$v) {
                $date = date('Y-m-d', strtotime($date));
                if ($date > $last_date) {
                    $tmp = [
                        'date' => $date,
                        'unit_value' => $v,
                        'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                        'trend' => 0
                    ];

                    $count = count($before_days);
                    if ($count==6) {
                        $before_days_tmp = $before_days;
                        sort($before_days_tmp);
                        if ($v <= $before_days_tmp[1]) {
                            $tmp['trend'] = -1;
                        }
                        else if ($v >= $before_days_tmp[4]) {
                            $tmp['trend'] = 1;
                        }
                    }

                    $datas[] = $tmp;

                    $last_unit_value = $v;
                    array_push($before_days, $v);
                    if ($count+1>6) {
                        array_shift($before_days);
                    }

                }
            }
        }


        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }

    /**
     *  富国基金
     */
    public function setFuguoNetUnit($code, $fund_info)
    {
        $url = 'http://www.fullgoal.com.cn/chart-web/chart/fundnetchart!getFundNetChartJson?fundcode=%s&from=%s&to=&charttype=2&show=1&titleflag=1&siteId=ea9e215cce3342d3b40721461cd1572d';

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        $start = $end = date('Y-m-d', strtotime('-1 day'));
        if (!$last_date) {
            //默认最多拉取近50年数据
            $start = date('Y-m-d', strtotime('-50 years'));
        }
        else if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
            return true;
        }
        else {
            $start = date('Y-m-d', strtotime('+1 day', strtotime($last_date)));
        }
        $url = sprintf($url, $code, $start);

        $res = file_get_contents($url);
        @$res = json_decode($res, true);

        $datas = [];


        if ($res['seriesData0']) {
            $length = count($res['seriesData0']);
            for ($i=0; $i<$length; $i++) {
                $date =$res['xAxisData'][$i];
                $v = $res['seriesData0'][$i];
                if ($date > $last_date) {
                    $tmp = [
                        'date' => $date,
                        'unit_value' => $v,
                        'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0,
                        'trend' => 0
                    ];

                    $count = count($before_days);
                    if ($count==6) {
                        $before_days_tmp = $before_days;
                        sort($before_days_tmp);
                        if ($v <= $before_days_tmp[1]) {
                            $tmp['trend'] = -1;
                        }
                        else if ($v >= $before_days_tmp[4]) {
                            $tmp['trend'] = 1;
                        }
                    }

                    $datas[] = $tmp;

                    $last_unit_value = $v;
                    array_push($before_days, $v);
                    if ($count+1>6) {
                        array_shift($before_days);
                    }

                }
            }
        }


        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }
}