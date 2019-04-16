<br>
您购买的授权码:{{$data[0]->sncode}}
<br>
有效期:90天
<br>
购买时间:<?php echo date('Y-m-d','{{$data[0]->buy_time}}');?>
<br>
支付方式:支付宝
<br>
支付标题:{{$data[0]->subject}}}
<br>
交易号:{{$data[0]->out_trade_no}}}
<br>
订单号:{{$data[0]->trade_no}}}
<br>
支付金额:{{$data[0]->total_amount}}}
<br>