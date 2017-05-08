<?php
include('./lib/SecKillService.class.php');

$seckill_id = $_POST['seckillId'];
$md5 = $_POST['md5'];
$user_phone = $_COOKIE['userPhone'];
$service = new SecKillService;
//$result = $service->excuteSecKill($seckill_id,$user_phone,$md5);//单纯事务
$result = $service->excuteSecKillProcedure($seckill_id,$user_phone,$md5);//存储过程

echo json_encode($result);die;