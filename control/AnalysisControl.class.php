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
        $handle = Instance::get('FundInfo');
        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }
        $start_time = '2017-06-01';
        $end_time = '2018-01-01';
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

                $yesterday_unit = $fund_model->select('unit_value')->where('date=?', $yesterday)->getALL()[0]['unit_value'] ?? 0;

                if (!$yesterday_unit || !$infos) {
                    continue;
                }
                if ($infos['rising_rate']>65 && $infos['change_total']>0) {
                    //上涨曲线
                }
                else if ($infos['rising_rate']<40 && $infos['change_total']<0) {
                }
                else {
                    //震荡曲线
                    if ($yesterday_unit < $infos['avg']) {
                        $fund->buy($amount, $date);
                    }
                }
            }
            echo "<h2>{$fund_info['name']}[{$fund_info['code']}]</h2>";
            $fund->show($end_time, false);
        }
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
            $fund = new FundNetUnitModel($fund_info['code']);
            $last_data = $fund->order('date desc')->limit('1')->getAll()[0] ?? '';

            echo <<< EOT
<h2>{$fund_info['name']}[{$fund_info['code']}]</h2>
<h4>{$last_data['date']}：{$last_data['unit_value']}</h4>
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

            echo '</table>';
        }

    }

}


