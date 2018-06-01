<?php

class AnalysisControl extends Control
{

    /**
     *
     */
    public function myth()
    {
        $fund_code = '000961';
        $buy_record = [
            '2018-03-02'    =>  1000,
        ];

        $timing_buy_conf = [
            [
                'start'     =>  '2018-03-13',//第一次扣款时间
                'end'       =>  '',
                'amount'    =>  200,
                'type'      =>  'week',//day month week
            ]
        ];


        $my_fund = new Fund($fund_code);

        if (isset($buy_record) && $buy_record) {
            foreach ($buy_record as $date => $amount) {
                $my_fund->buy($amount, $date);
            }
        }

        if (isset($timing_buy_conf) && $timing_buy_conf) {
            foreach ($timing_buy_conf as $conf) {
                $end = $conf['end'] ? strtotime($conf['end']) : strtotime('-1 day');
                for($i=strtotime($conf['start']); $i<=$end; $i=strtotime('+1 '.$conf['type'], $i)) {
                    $date = date('Y-m-d', $i);
                    $amount = $conf['amount'];
                    $delay = 0;
                    $cycle = 1;
                    switch($conf['type']) {
                        case 'day':
                            $cycle = 1;
                            break;
                        case 'week':
                            $cycle = 7;
                            break;
                        case 'month':
                            $cycle = 30;
                            break;
                        default:
                            $cycle = 1;
                            break;
                    }
                    while(!$my_fund->buy($amount, $date)){
                        $tm=strtotime('+1 day', strtotime($date));
                        if ($tm > $end) {
                            break;
                        }
                        $date = date('Y-m-d', $tm);
                        if ($delay++ >= $cycle) {
                            break;
                        }
                    }
                }
            }
        }

        $my_fund->show();

    }
	
	public function mygt()
    {
        $fund_code = '001542';
		$reate_fees = 0.0015;
        $buy_record = [
            '2018-05-16'    =>  300,
        ];

        $timing_buy_conf = [
            [
                'start'     =>  '2017-12-18',//第一次扣款时间
                'end'       =>  '2018-01-15',
                'amount'    =>  270,
                'type'      =>  'week',//day month week
            ],
            [
                'start'     =>  '2018-01-22',//第一次扣款时间
                'end'       =>  '2018-01-29',
                'amount'    =>  240,
                'type'      =>  'week',//day month week
            ],
            [
                'start'     =>  '2018-02-05',//第一次扣款时间
                'end'       =>  '2018-03-12',
                'amount'    =>  270,
                'type'      =>  'week',//day month week
            ]
        ];


        $my_fund = new Fund($fund_code, $reate_fees);

        if (isset($buy_record) && $buy_record) {
            foreach ($buy_record as $date => $amount) {
                $my_fund->buy($amount, $date);
            }
        }

        if (isset($timing_buy_conf) && $timing_buy_conf) {
            foreach ($timing_buy_conf as $conf) {
                $end = $conf['end'] ? strtotime($conf['end']) : strtotime('-1 day');
                for($i=strtotime($conf['start']); $i<=$end; $i=strtotime('+1 '.$conf['type'], $i)) {
                    $date = date('Y-m-d', $i);
                    $amount = $conf['amount'];
                    $delay = 0;
                    $cycle = 1;
                    switch($conf['type']) {
                        case 'day':
                            $cycle = 1;
                            break;
                        case 'week':
                            $cycle = 7;
                            break;
                        case 'month':
                            $cycle = 30;
                            break;
                        default:
                            $cycle = 1;
                            break;
                    }
                    while(!$my_fund->buy($amount, $date)){
                        $tm=strtotime('+1 day', strtotime($date));
                        if ($tm > $end) {
                            break;
                        }
                        $date = date('Y-m-d', $tm);
                        if ($delay++ >= $cycle) {
                            break;
                        }
                    }
                }
            }
        }

        $my_fund->show();

    }


    public function test()
    {
        $code = RemoteInfo::get('code');
        $handle = Instance::get('FundInfo');
        if ($code) {
            $handle = $handle->where(['code'=>$code]);
        }
        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }
        $start_time = date('Y-m-d');
        $end_time = date('Y-m-d');

        foreach($fund_infos as $fund_info) {
            $fund_code = $fund_info['code'];
            $amount = 100;

            $fund_model = new FundNetUnitModel($fund_code);

            $fund = new Fund($fund_code);

            $tm = strtotime($end_time);
            for ($i = strtotime($start_time); $i <= $tm; $i += 24 * 3600) {
                $date = date('Y-m-d', $i);
                $yesterday = date('Y-m-d', $i- 24 * 3600);
                $sdate = date('Y-m-d', strtotime('-6 months', $i));
                $infos = $fund_model->getStatisticsUnitValue($sdate, $yesterday);

                $before_days = array_column($fund_model->select('*')->where('date<=?', $yesterday)->order('date desc')->limit('7')->getALL(), null, 'date');

                $yesterday_info = $before_days[$yesterday] ?? null;
                unset($before_days[$yesterday]);

                $before_days = array_values($before_days);

                if (!$yesterday_info || !$infos) {
                    continue;
                }

                $is_buy = false;
                $before_trend = 0;
                if ($yesterday_info['unit_value']<$infos['avg']) {
                    switch ($yesterday_info['trend']) {
                        case -1://下降
                            $down_count = 0;
                            foreach ($before_days as $v) {
                                switch ($v['trend']) {
                                    case -1://下降
                                        $down_count++;
                                        break;
                                    case 0://震荡
                                        break;
                                    case 1://上涨
                                        break 3;
                                }
                            }
                            if ($down_count >= 4) {
                                $is_buy = true;
                            }
                            break;
                        case 0://震荡
                            foreach ($before_days as $v) {
                                switch ($v['trend']) {
                                    case -1://下降
                                        $is_buy = true;
                                        break 3;
                                    case 0://震荡
                                        break;
                                    case 1://上涨
                                        break 3;
                                }
                            }
                            $is_buy = true;
                            break;
                        case 1://上涨
                            foreach ($before_days as $v) {
                                switch ($v['trend']) {
                                    case -1://下降
                                        $is_buy = true;
                                        break 3;
                                    case 0://震荡
                                        break;
                                    case 1://上涨
                                        break 3;
                                }
                            }
                            break;
                    }
                }
                if ($is_buy) {
                    $fund->buy($amount, $date);
                }
            }
            $fund->show($end_time);
        }
    }

    public function suggest()
    {
        $code = RemoteInfo::get('code');
        $handle = Instance::get('FundInfo');
        if ($code) {
            $handle = $handle->where(['code'=>$code]);
        }
        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }
        $date = date('Y-m-d');

        foreach($fund_infos as $fund_info) {
            $fund_code = $fund_info['code'];

            echo "<h3>{$fund_info['name']}[{$fund_info['code']}]</h3>";

            $fund_model = new FundNetUnitModel($fund_code);

            $fund = new Fund($fund_code);

            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $sdate = date('Y-m-d', strtotime('-6 months'));
            $infos = $fund_model->getStatisticsUnitValue($sdate, $yesterday);

            $before_days = array_column($fund_model->select('*')->where('date<=?', $yesterday)->order('date desc')->limit('7')->getALL(), null, 'date');

            $yesterday_info = $before_days[$yesterday] ?? null;
            unset($before_days[$yesterday]);

            $before_days = array_values($before_days);

            if (!$yesterday_info || !$infos) {
                continue;
            }

            $is_buy = false;
            $before_trend = 0;
            if ($yesterday_info['unit_value']<$infos['avg']) {
                switch ($yesterday_info['trend']) {
                    case -1://下降
                        $down_count = 0;
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $down_count++;
                                    break;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    $down_count--;
                                    break;
                            }
                        }
                        if ($down_count >= 3) {
                            $is_buy = true;
                        }
                        break;
                    case 0://震荡
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $is_buy = true;
                                    break 3;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    break 3;
                            }
                        }
                        $is_buy = true;
                        break;
                    case 1://上涨
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $is_buy = true;
                                    break 3;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    break 3;
                            }
                        }
                        break;
                }
            }
            if ($is_buy) {
                echo "<p>建议买入</p>";
            }
            else if(($yesterday_info['unit_value']-$infos['avg'])/$infos['avg']>0.1) {
                echo "<p>已较半年平均上涨10%，建议卖出</p>";
            }
            else {
                echo "<p>无</p>";
            }
            echo '<hr/>';

        }
    }

    public function push()
    {
        $handle = Instance::get('FundInfo');

        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }
        $date = date('Y-m-d');

        $suggestions = '';

        foreach($fund_infos as $fund_info) {
            $fund_code = $fund_info['code'];

            $fund_name = "{$fund_info['name']}[{$fund_info['code']}]";

            $fund_model = new FundNetUnitModel($fund_code);

            $fund = new Fund($fund_code);

            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $sdate = date('Y-m-d', strtotime('-6 months'));
            $infos = $fund_model->getStatisticsUnitValue($sdate, $yesterday);

            $before_days = array_column($fund_model->select('*')->where('date<=?', $yesterday)->order('date desc')->limit('7')->getALL(), null, 'date');

            $yesterday_info = $before_days[$yesterday] ?? null;
            unset($before_days[$yesterday]);

            $before_days = array_values($before_days);

            if (!$yesterday_info || !$infos) {
                continue;
            }

            $is_buy = false;
            $before_trend = 0;
            if ($yesterday_info['unit_value']<$infos['avg']) {
                switch ($yesterday_info['trend']) {
                    case -1://下降
                        $down_count = 0;
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $down_count++;
                                    break;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    $down_count--;
                                    break;
                            }
                        }
                        if ($down_count >= 3) {
                            $is_buy = true;
                        }
                        break;
                    case 0://震荡
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $is_buy = true;
                                    break 3;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    break 3;
                            }
                        }
                        $is_buy = true;
                        break;
                    case 1://上涨
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $is_buy = true;
                                    break 3;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    break 3;
                            }
                        }
                        break;
                }
            }
            $suggestion = '';
            if ($is_buy) {
                $suggestion =  "建议买入";
            }
            else if(($yesterday_info['unit_value']-$infos['avg'])/$infos['avg']>0.1) {
                $suggestion = "已较半年平均上涨10%，建议卖出";
            }

            if ($suggestion) {
                $suggestions .= "\r\n基金名称：$fund_name\r\n建议：$suggestion\r\n";
            }
        }

        $wx = new WeXinService;

        $wx->sedFundMessage($suggestions, $date);


    }

    public function showDetail()
    {
        $code = RemoteInfo::get('code');
        $handle = Instance::get('FundInfo');
        if ($code) {
            $handle = $handle->where(['code'=>$code]);
        }
        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }


        $dates = [
            '全部'    =>  [null,null],
            '最近一年'=>  [date('Y-m-d', strtotime('-1 year')), null],
            '今年'    =>  [date('Y-01-01'), null],
            '最近半年'=>  [date('Y-m-d', strtotime('-6 months')), null],
            '最近三月'=>  [date('Y-m-d', strtotime('-3 months')), null],
            '最近一月'=>  [date('Y-m-d', strtotime('-1 months')), null],
            '当月'    =>  [date('Y-m-01'), null],
            '最近二周'=>  [date('Y-m-d', strtotime('-2 weeks')), null],
            '最近一周'=>  [date('Y-m-d', strtotime('-1 weeks')), null],
        ];

        foreach($fund_infos as $fund_info) {
            $fund_model = $fund = new FundNetUnitModel($fund_info['code']);
            $last_data = $fund->order('date desc')->limit('1')->getAll()[0] ?? '';

            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $sdate = date('Y-m-d', strtotime('-6 months'));
            $infos = $fund_model->getStatisticsUnitValue($sdate, $yesterday);

            $before_days = array_column($fund_model->select('*')->where('date<=?', $yesterday)->order('date desc')->limit('7')->getALL(), null, 'date');

            $yesterday_info = $before_days[$yesterday] ?? null;
            unset($before_days[$yesterday]);

            $before_days = array_values($before_days);

            if (!$yesterday_info || !$infos) {
                continue;
            }

            $is_buy = false;
            $before_trend = 0;
            if ($yesterday_info['unit_value']<$infos['avg']) {
                switch ($yesterday_info['trend']) {
                    case -1://下降
                        $down_count = 0;
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $down_count++;
                                    break;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    $down_count--;
                                    break;
                            }
                        }
                        if ($down_count >= 3) {
                            $is_buy = true;
                        }
                        break;
                    case 0://震荡
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $is_buy = true;
                                    break 3;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    break 3;
                            }
                        }
                        $is_buy = true;
                        break;
                    case 1://上涨
                        foreach ($before_days as $v) {
                            switch ($v['trend']) {
                                case -1://下降
                                    $is_buy = true;
                                    break 3;
                                case 0://震荡
                                    break;
                                case 1://上涨
                                    break 3;
                            }
                        }
                        break;
                }
            }
            $suggest = '';
            if ($is_buy) {
                $suggest =  "建议买入";
            }
            else if(($yesterday_info['unit_value']-$infos['avg'])/$infos['avg']>0.1) {
                $suggest =  "已较半年平均上涨10%，建议卖出";
            }
            else {
                $suggest =  "无";
            }

            echo <<< EOT
<h2>{$fund_info['name']}[{$fund_info['code']}]</h2>
<h4>{$last_data['date']}：{$last_data['unit_value']}</h4>
<p>建议：$suggest</p>
<table border="1">
  <tr>
    <td>时间范围</td>
    <td>最大净值</td>
    <td>最小</td>
    <td>平均净值</td>
    <td>净值变化</td>
    <td>上涨率</td>
    <td>上涨天数</td>
    <td>上涨合计</td>
    <td>下降天数</td>
    <td>下降合计</td>
 </tr>
EOT;


            foreach ($dates as $date => $v) {
                $data = $fund->getStatisticsUnitValue($v[0], $v[1]);
                echo <<< EOT
<tr>
<td>{$date}</td>
<td>{$data['max']}</td>
<td>{$data['min']}</td>
<td>{$data['avg']}</td>
<td>{$data['change_total']}</td>
<td>{$data['rising_rate']}</td>
<td>{$data['rising_days']}</td>
<td>{$data['rising_total']}</td>
<td>{$data['falling_days']}</td>
<td>{$data['falling_total']}</td>
</tr>
EOT;
            }

            echo '</table><hr/>';
        }

    }

}


