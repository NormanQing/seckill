<?php
include('./lib/SecKillService.class.php');

$seckill_id = $_GET['id'];

$service = new SecKillService;
$result = $service->exportSecKillUrl($seckill_id);

echo json_encode($result);die;