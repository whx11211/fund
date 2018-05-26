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
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(1)->getAll()[0] ?? [];
        $last_date = $last_list['date'] ?? '';
        $last_unit_value = $last_list['unit_value'] ?? 0;

        if ($last_date >= date('Y-m-d', strtotime('-1 days'))) {
            return true;
        }

        $res = file_get_contents($url);
        @$res = json_decode($res);

        $datas = [];

        foreach ($res as $v) {
            $date = date('Y-m-d', substr($v[0], 0, -3)-1);
            if ($date>$last_date) {
                $datas[] = [
                    'date'          =>  $date,
                    'unit_value'    =>  $v[1],
                    'unit_change'   =>  $last_unit_value ? sprintf('%4f', $v[1]-$last_unit_value) : 0
                ];
                $last_unit_value = $v[1];
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
        $last_list = $FundNetUnitInstance->select('*')->order('date desc')->limit(1)->getAll()[0] ?? [];
        $last_date = $last_list['date'] ?? '';
        $last_unit_value = $last_list['unit_value'] ?? 0;

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
                    $datas[] = [
                        'date' => $date,
                        'unit_value' => $v,
                        'unit_change' => $last_unit_value ? sprintf('%4f', $v - $last_unit_value) : 0
                    ];
                    $last_unit_value = $v;
                }
            }
        }


        if ($datas) {
            $FundNetUnitInstance->batchInsert($datas);
        }

        return true;
    }
}