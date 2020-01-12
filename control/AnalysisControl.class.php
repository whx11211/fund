<?php

class AnalysisControl extends Control
{
    private $sell_conf = 20;//卖出提醒配置，净值超过半年平均的百分比

    /**
     *
     */
    public function myth()
    {
        $fund_code = '000961';
        $buy_record = [
            '2018-03-02'    =>  1000,
            '2018-05-31'    =>  1000,
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
        $start_time = RemoteInfo::get('start') ?: date('Y-m-d', strtotime('-1 month'));
        $end_time = RemoteInfo::get('end') ?: date('Y-m-d');

        echo <<< EOT
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<title>基金买入模拟</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/highcharts.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/exporting.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/series-label.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/oldie.js"></script>
<script src="https://code.highcharts.com.cn/highcharts-plugins/highcharts-zh_CN.js"></script>
</head>
<body style="padding: 15px;">
EOT;

        foreach($fund_infos as $fund_info) {
            $fund_code = $fund_info['code'];
            $amount = 100;

            $fund_model = new FundNetUnitModel($fund_code);

            $fund = new Fund($fund_code);
            $fund2 = new Fund($fund_code);
            $dates = [];

            $tm = strtotime($end_time);
            for ($i = strtotime($start_time); $i <= $tm; $i += 24 * 3600) {
                $date = date('Y-m-d', $i);
                $yesterday = date('Y-m-d', $i- 24 * 3600);
                $sdate = date('Y-m-d', strtotime('-6 months', $i));
                $infos = $fund_model->getStatisticsUnitValue($sdate, $yesterday);

                $before_days = array_column($fund_model->select('*')->where('date<=?', $yesterday)->order('date desc')->limit('7')->getALL(), null, 'date');

                $yesterday_info = $yesterday_info = array_pop($before_days);

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
                    $fund->buy($amount, $date);
                    $dates[] = $date;
                }
                $fund2->buy($amount, $date);
            }

            $unit_data = $fund_model->select('*')->where('date between ? and ?', [$start_time, $end_time])->order('date asc')->getALL();

            $data = [];
            $date_data = [];
            foreach ($unit_data as $v) {
                $data[] = [
                    'y' =>  (float)$v['unit_value'],
                    'name' =>  $v['date'],
                    'color' =>  in_array($v['date'], $dates??[]) ? 'red' : '',
                ];
                $date_data[] = $v['date'];
            }

            $buy_data = [
                'data'  =>  $data,
                'date_data' => $date_data
            ];

            $fund->showCompare($end_time, $fund2, $start_time, $buy_data);
        }
    }

    public function trend()
    {
        $codes = RemoteInfo::get('codes');
        $handle = Instance::get('FundInfo');
        if ($codes) {
            $codes = explode(',', $codes);
            $handle = $handle->where(['code' => [
                'in' => $codes
            ]]);
        }
        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }
        $start_time = RemoteInfo::get('start') ?: date('Y-m-d', strtotime('-1 month'));
        $end_time = RemoteInfo::get('end') ?: date('Y-m-d');

        echo <<< EOT
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<title>基金走势图</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/highcharts.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/exporting.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/series-label.js"></script>
<script src="https://code.highcharts.com.cn/highcharts/modules/oldie.js"></script>
<script src="https://code.highcharts.com.cn/highcharts-plugins/highcharts-zh_CN.js"></script>
</head>
<body style="padding: 15px;">
EOT;

        $date_data = [];
        $data_all = [];
        foreach($fund_infos as $fund_info) {
            $fund_code = $fund_info['code'];
            $amount = 100;

            $fund_model = new FundNetUnitModel($fund_code);

            $unit_data = $fund_model->select('*')->where('date between ? and ?', [$start_time, $end_time])->order('date asc')->getALL();

            $data = [];
            if (empty($date_data)) {
                foreach ($unit_data as $v) {
                    $date_data[] = $v['date'];
                }
            }
            $first_unit = $unit_data[0]['unit_value'] ?? 1;
            $unit_data = array_column($unit_data, null, 'date');
            $v = [
                'unit_value' => $first_unit,
            ];
            foreach ($date_data as $d) {
                if (isset($unit_data[$d])) {
                    $v = $unit_data[$d];
                }

                $data[] = [
                    'y' =>  round($v['unit_value'] / $first_unit, 3),
                    'name' =>  $d . "\t" . $v['unit_value'],
                ];
            }

            $data_all[] = [
                'name' =>  $fund_info['name'],
                'data' =>  $data,
            ];
        }

        $date_data = json_encode($date_data);
        $data_all = json_encode($data_all);

        echo <<<EOT
<div id="#chart" style="height: 800px"></div>
<script>
    var chart = Highcharts.chart('#chart', {
        title: {
            text: '基金走势'
        },
        yAxis: {
            title: {
                text: '净值'
            }
        },
        plotOptions: {
            series: {
                marker: {
                    radius: 1
                }
            }
        },
        tooltip: {
            crosshairs: [true, false],
            pointFormat: '{series.name}: <b>{point.y}</b><br/>',
            shared: true
        },
        xAxis: {
            categories: {$date_data}
        },
        series: {$data_all},
        exporting: { enabled: false },//隐藏导出图片
        credits: { enabled: false }//隐藏highcharts的站点标志
    });
</script>
EOT;
    }

    public function push()
    {
        $handle = Instance::get('FundInfo');

        $fund_infos = $handle->where('holding', 1)->getAll();
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

            $yesterday_info = array_pop($before_days);

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
            else if(($yesterday_info['unit_value']-$infos['avg'])/$infos['avg']*100>$this->sell_conf) {
                $half_change = intval(($yesterday_info['unit_value']-$infos['avg'])/$infos['avg']*100);
                $suggestion = "已较半年平均上涨{$half_change}%，建议卖出";
            }

            if ($suggestion) {
                $suggestions .= "基金：$fund_name\r\n建议：$suggestion\r\n\r\n";
            }
        }

        if (!$suggestions) {
            $suggestions = "无建议";
        }

        $wx = new WeXinService;

        $url = URL_PATH . '?c=Analysis&m=showDetail';

        $wx->sedFundMessage($suggestions, $url);


    }

    public function showDetail()
    {
        $code = RemoteInfo::get('code');
        $all = RemoteInfo::get('all');
        $handle = Instance::get('FundInfo');
        if ($code) {
            $handle = $handle->where(['code'=>$code]);
        }
        if (!$code && !$all) {
            $handle = $handle->where(['holding'=>1]);
        }
        $fund_infos = $handle->getAll();
        if (!$fund_infos) {
            Output::fail('no fund');
        }


        $dates = [
            '全部'    =>  [null,null],
            '最近三年'=>  [date('Y-m-d', strtotime('-3 year')), null],
            '最近一年'=>  [date('Y-m-d', strtotime('-1 year')), null],
            //'今年'    =>  [date('Y-01-01'), null],
            '最近半年'=>  [date('Y-m-d', strtotime('-6 months')), null],
            //'最近三月'=>  [date('Y-m-d', strtotime('-3 months')), null],
            '最近一月'=>  [date('Y-m-d', strtotime('-1 months')), null],
            //'当月'    =>  [date('Y-m-01'), null],
            //'最近二周'=>  [date('Y-m-d', strtotime('-2 weeks')), null],
            '最近一周'=>  [date('Y-m-d', strtotime('-1 weeks')), null],
        ];

        echo <<< EOT
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8" />
<title>基金涨跌一览</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="padding: 15px;">
EOT;

        foreach($fund_infos as $fund_info) {
            $fund_model = $fund = new FundNetUnitModel($fund_info['code']);
            $last_data = $fund->order('date desc')->limit('1')->getAll()[0] ?? '';

            $history_position = $fund->select("round(sum(if(unit_value>{$last_data['unit_value']},1,0))/count(1)*100, 2) as rate")->getAll()[0]['rate'];

            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $sdate = date('Y-m-d', strtotime('-6 months'));
            $infos = $fund_model->getStatisticsUnitValue($sdate, $yesterday);

            $before_days = array_column($fund_model->select('*')->where('date<=?', $yesterday)->order('date desc')->limit('7')->getALL(), null, 'date');

            $yesterday_info = $yesterday_info = array_pop($before_days);

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
            $half_year_change = 0;
            if ($is_buy) {
                $suggest =  "建议买入";
            }

            $half_year_change = round(($yesterday_info['unit_value']-$infos['avg'])/$infos['avg']*100, 2);
            if ($half_year_change >= $this->sell_conf) {
                $suggest =  "建议卖出";
            }
            else {
            }


            echo <<< EOT
<div style="max-width: 1000px; margin: 0 auto 30px;font-size: 1.5em;">
<div style="border: 1px solid #ddd;border-bottom: none;padding:5px 15px;border-radius: 5px 5px 0 0;background-color: #f7f7f9;">
    <h3>{$fund_info['name']}[{$fund_info['code']}]</h3>
    <h4>{$last_data['date']}：{$last_data['unit_value']}</h4>
    <p>成立时间：{$fund_info['founding_time']}</p>
    <p>低于基金历史 $history_position% 的时间</p>
    <p>已较半年平均上涨 $half_year_change% </p>
    <p style="font-weight: bold;color:red;font-size: large;">$suggest</p>
</div>
<table class="table table-hover table-bordered">
 <thead>
  <tr>
    <th rowspan="2">时间范围</th>
    <th colspan="4">净值数据</th>
    <!--<th colspan="3">涨跌分析</th>-->
  </tr>
  <tr>
    <th>最小净值</th>
    <th>平均净值</th>
    <th>最大净值</th>
    <th>净值变化</th>
    <!--
    <th>上涨天数</th>
    <th>下降天数</th>
    <th>上涨率</th>
    -->
  </tr>
 </thead>
 <tbody>
EOT;


            foreach ($dates as $date => $v) {
                $data = $fund->getStatisticsUnitValue($v[0], $v[1]);
                $max = $last_data['unit_value'] >= $data['max'] ? 'style="color:red;font-weight: bold;"' : '';
                $min = $last_data['unit_value'] <= $data['min'] ? 'style="color:green;font-weight: bold;"' : '';
                $avg = $last_data['unit_value'] <= $data['avg'] ? 'style="color:green;font-weight: bold;"' : '';
                echo <<< EOT
<tr>
<td>{$date}</td>
<td $min>{$data['min']}</td>
<td $avg>{$data['avg']}</td>
<td $max>{$data['max']}</td>
<td>{$data['change_total']}</td>
<!--
<td>{$data['rising_days']}</td>
<td>{$data['falling_days']}</td>
<td>{$data['rising_rate']}</td>
-->
</tr>
EOT;
            }

            echo '</tbody></table></div>';
        }

        echo '</body>';
    }

}


