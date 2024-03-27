<?php

class Fund
{
    /**
     * 买当前金额（花了多少钱）
     */
    public $amount=0.00;

    /**
     * 手续费率
     */
    public $fees_rate=0.001;

    /**
     * 手续费
     */
    public $fees=0.00;

    /**
     * 买入记录
     */
    public $transaction_list = [];

    /**
     * 总份额
     */
    public $quantity=0.00;

    /**
     *
     */
    public $fund_info = null;

    /**
     * FundNetUnit实例
     */
    public $fund_model=null;

    public function __construct($fund_code)
    {
        $this->fund_info = Instance::get('funds')->where(['code'=>$fund_code])->getAll()[0];

        $this->fees_rate = $this->fund_info['free_rate'];

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
            $res = $this->fund_model->select('date,unit_value')->order('date desc')->limit(1)->getAll()[0];
            $unit_values = $res['unit_value'];
            $date = $res['date'];
        }

        $now_amount = sprintf('%.2f',$this->quantity * $unit_values);
        $return = sprintf('%.2f',$now_amount-$this->amount);
        $return_rate = $this->amount ? sprintf('%.2f',100*($now_amount-$this->amount)/$this->amount).'%' : 0;

        echo <<< EOT
<h2>{$this->fund_info['name']}</h2>
<h3>账户总览</h3>
<p>日期：{$date}</p>
<p>买入总金额：{$this->amount}</p>
<p>手续费总计：{$this->fees}</p>
<p>买入总份额：{$this->quantity}</p>
<p>当前净值：{$unit_values}</p>
<p>当前总金额：{$now_amount}</p>
<p>收益：{$return}</p>
<p>收益率：{$return_rate}</p>

EOT;
        if ($detail && $this->transaction_list) {
            echo <<< EOT
<h3>交易详细</h3>
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
            echo '</table><hr/>';
        }

    }

    public function showCompare($date='', $fund2, $start_date, $buy_data)
    {
        $unit_values = 0;
        if ($date) {
            $i = 0;
            while(!($unit_values=$this->fund_model->getUnitValueByDate($date)) && $i++<10) {
                $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            }
        }
        else {
            $res = $this->fund_model->select('date,unit_value')->order('date desc')->limit(1)->getAll()[0];
            $unit_values = $res['unit_value'];
            $date = $res['date'];
        }

        $now_amount = sprintf('%.2f',$this->quantity * $unit_values);
        $return = sprintf('%.2f',$now_amount-$this->amount);
        $return_rate = $this->amount ? sprintf('%.2f',100*($now_amount-$this->amount)/$this->amount).'%' : 0;
        $buy_count = count($this->transaction_list);

        $now_amount2 = sprintf('%.2f',$fund2->quantity * $unit_values);
        $return2 = sprintf('%.2f',$now_amount2-$fund2->amount);
        $return_rate2 = $fund2->amount ? sprintf('%.2f',100*($now_amount2-$fund2->amount)/$fund2->amount).'%' : 0;
        $buy_count2 = count($fund2->transaction_list);

        echo <<< EOT
<h2>{$this->fund_info['name']}({$this->fund_info['code']})</h2>
<div>
<p>买入日期：{$start_date}-{$date}</p>
<p>当前净值：{$unit_values}</p>
</div>

<table class="table table-hover table-bordered">
    <tr>
        <th>买入方式</th>
        <th>买入笔数</th>
        <th>买入总金额</th>
        <th>手续费总计</th>
        <th>买入总份额</th>
        <th>当前总金额</th>
        <th>收益</th>
        <th>收益率</th>
    </tr>

    <tr>
        <td><a href="#" data-toggle="modal" data-target="#detail_{$this->fund_info['code']}">算法推荐</a></td>
        <td>{$buy_count}</td>
        <td>{$this->amount}</td>
        <td>{$this->fees}</td>
        <td>{$this->quantity}</td>
        <td>{$now_amount}</td>
        <td>{$return}</td>
        <td>{$return_rate}</td>
    </tr>
    <tr>
        <td>定投</td>
        <td>{$buy_count2}</td>
        <td>{$fund2->amount}</td>
        <td>{$fund2->fees}</td>
        <td>{$fund2->quantity}</td>
        <td>{$now_amount2}</td>
        <td>{$return2}</td>
        <td>{$return_rate2}</td>
    </tr>
</table>

EOT;

        if ($buy_data['date_data']) {
            $buy_data = json_encode($buy_data);
            echo <<<EOT
<div id="#chart_{$this->fund_info['code']}" style=""></div>
<script>
    var data={$buy_data};
    var chart = Highcharts.chart('#chart_{$this->fund_info['code']}', {
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
        xAxis: {
            categories: data.date_data
        },
        series: [{
            name: '{$this->fund_info['name']}',
            data: data.data
        }],
        exporting: { enabled: false },//隐藏导出图片
        credits: { enabled: false }//隐藏highcharts的站点标志
    });
</script>
EOT;
        }

        echo '<hr/>';


        echo <<< EOT
<div id="detail_{$this->fund_info['code']}"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">{$this->fund_info['name']}({$this->fund_info['code']})</h4>
        </div>
        <div class="modal-body">
        
<table class="table table-hover table-bordered">
<tr>
<th>日期</th>
<th>交易类型</th>
<th>交易金额</th>
<th>交易手续费</th>
<th>交易净值</th>
<th>交易份额</th>
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
        if (!$transaction_list) {
            echo "<tr><td colspan='6' style='text-align: center'>无</td></tr>";
        }
        echo <<<EOT
</table>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal -->
</div>
EOT;



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