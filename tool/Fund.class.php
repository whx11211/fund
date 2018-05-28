<?php

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
        $this->quantity += $detail['quantity'] = $detail['transaction_unit_value'] ? sprintf('%.2f',  ($amount - $detail['cost_free']) / $detail['transaction_unit_value']) : 0;

        //$detail['after_amount'] = $this->amount;
        //$detail['after_quantity'] = $this->quantity;

        $this->transaction_list[] = $detail;

        return true;
    }

    public function show($date='', $detail=true)
    {
        $unit_values = 0;
        if ($date) {
            $i = 0;
            while(!($unit_values=$this->fund_model->getUnitValueByDate($date)) && $i++<10) {
                $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            }
        }
        else {
            $unit_values = $this->fund_model->select('unit_value')->order('date desc')->limit(1)->getAll()[0]['unit_value'];
        }

        $now_amount = sprintf('%.2f',$this->quantity * $unit_values);
        $return = sprintf('%.2f',$now_amount-$this->amount);
        $return_rate = $this->amount ? sprintf('%.2f',100*($now_amount-$this->amount)/$this->amount).'%' : 0;

        echo <<< EOT
<h2>账户总览$unit_values</h2>
<p>日期：{$date}</p>
<p>买入总金额：{$this->amount}</p>
<p>手续费总计：{$this->fees}</p>
<p>买入总份额：{$this->quantity}</p>
<p>当前总金额：{$now_amount}</p>
<p>收益：{$return}</p>
<p>收益率：{$return_rate}</p>
<hr/>
EOT;
        if ($detail && $this->transaction_list) {
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

    public function getReturnRate($date=null)
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
        return $this->amount ? sprintf('%.2f',100*($now_amount-$this->amount)/$this->amount).'%' : 0;
    }
}