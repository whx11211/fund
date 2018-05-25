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
        $fund_code = '001542';
		$reate_fees = 0.0015;
        $buy_record = [
        ];

        $timing_buy_conf = [
            [
                'start'     =>  '2017-01-02',//第一次扣款时间
                'end'       =>  '',
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

}


class Fund
{
    /**
     * 买当前金额（花了多少钱）
     */
    private $amount=0.00;

    /**
     * 手续费率
     */
    private $fees_rate=0.001;

    /**
     * 手续费
     */
    private $fees=0.00;

    /**
     * 买入记录
     */
    private $transaction_list = [];

    /**
     * 总份额
     */
    private $quantity=0.00;

    /**
     * FundNetUnit实例
     */
    private $fund_model=null;

    public function __construct($fund_code, $fees_rate=null)
    {
        if ($fees_rate) {
            $this->fees_rate = $fees_rate;
        }
        $this->fund_model = new FundNetUnitModel($fund_code);
    }

    public function buy($amount, $date=null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $unit_value = $this->fund_model->getUnitValueByDate($date);
        if (!$unit_value) {
            return false;
        }
        $detail = [
            'date'                  => $date,
            'type'                  => 1,
            'transaction_unit_value'=>  $unit_value,
            //'before_amount'         =>  $this->amount,
            //'before_quantity'       =>  $this->quantity
        ];
        $this->amount += $detail['amount'] = $amount;
        $this->fees += $detail['cost_free'] = sprintf('%.2f', $amount * $this->fees_rate);
        $this->quantity += $detail['quantity'] = sprintf('%.2f',  ($amount - $detail['cost_free']) / $detail['transaction_unit_value']);

        //$detail['after_amount'] = $this->amount;
        //$detail['after_quantity'] = $this->quantity;

        $this->transaction_list[] = $detail;

        return true;
    }

    public function show($date='')
    {
        $unit_values = 0;
        if ($date) {
            $i = 0;
            while(!$unit_values=$this->fund_model->getUnitValueByDate($date) && $i++<10) {
                $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            }
        }
        else {
            $unit_values = $this->fund_model->select('unit_value')->order('date desc')->limit(1)->getAll()[0]['unit_value'];
        }

        $now_amount = sprintf('%.2f',$this->quantity * $unit_values);
        $return = $now_amount-$this->amount;
        $return_rate = sprintf('%.2f',100*($now_amount-$this->amount)/$this->amount).'%';

        echo <<< EOT
<h2>账户总览</h2>
<p>买入总金额：{$this->amount}</p>
<p>手续费总计：{$this->fees}</p>
<p>买入总份额：{$this->quantity}</p>
<p>当前总金额：{$now_amount}</p>
<p>收益：{$return}</p>
<p>收益率：{$return_rate}</p>
<hr/>
EOT;
        if ($this->transaction_list) {
            echo <<< EOT
<h2>交易详细</h2>
<table border="1">
  <tr>
    <td>日期</td>
    <td>交易类型</td>
    <td>交易金额</td>
    <td>交易手续费</td>
    <td>交易净值</td>
    <td>交易份额</td>
 </tr>
EOT;

			$transaction_list = array_column($this->transaction_list, null, 'date');
			ksort($transaction_list);
            foreach ($transaction_list as $v) {
                $transaction_type = $v['type'] == 1 ? '买入' : '卖出';
                echo <<< EOT
<tr>
<td>{$v['date']}</td>
<td>{$transaction_type}</td>
<td>{$v['amount']}</td>
<td>{$v['cost_free']}</td>
<td>{$v['transaction_unit_value']}</td>
<td>{$v['quantity']}</td>
</tr>
EOT;
            }
            echo '</table>';
        }

    }
}