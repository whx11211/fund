<?php

class HandleControl extends Control
{

    public function setNetUnit()
    {
        $fund_infos = Instance::get('FundInfo')->getAll();

        foreach($fund_infos as $fund_info) {
            $method = "set".ucfirst($fund_info['company'])."NetUnit";
            if (method_exists($this, $method)) {
                $this->$method($fund_info['code']);
            }
        }
        Output::success('OK');
    }

    /**
     *  天虹基金
     */
    public function setTianhongNetUnit($code)
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
     *  国泰基金基金
     */
    public function setGuotaiNetUnit($code)
    {
        $url = 'https://e.gtfund.com/Etrade/Jijin/navCombo/fundCode/%s/start/%s/end/%s';

        $FundNetUnitInstance = new FundNetUnitModel($code);
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(6)->getAll();
        $last_date = $last_list[0]['date'] ?? '';
        $last_unit_value = $last_list[0]['unit_value'] ?? 0;
        $before_days = array_reverse(array_column($last_list, 'unit_value'));

        $start = $end = date('Y-m-d', strtotime('-1 day'));
        if (!$last_date) {
            //默认最多拉取近5年数据
            $start = date('Y-m-d', strtotime('-5 years'));
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
            foreach ($res['data']['s1']['data'] as $k => $v) {
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
}