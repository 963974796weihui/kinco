<?php
require_once 'config.php';
require_once 'pagepay/service/AlipayTradeService.php';
$fp = fopen('./log.txt','r+');
$content=12312333333;
fwrite($fp, print_r($content, true));

fclose($fp);