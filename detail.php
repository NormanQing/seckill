<?php
header("Content-type:text/html;charset=utf-8");

$seckill_id = $_GET['id'];


include('./lib/SecKillService.class.php');

$service = new SecKillService;
$info = $service->getInfo($seckill_id);

include('./html/detail.html');