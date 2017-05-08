<?php

include('./lib/SecKillService.class.php');

$service = new SecKillService;
$info = $service->nowTime();

$result =array(
	'success'=>1,
	'data'=>$info*1000
);

echo json_encode($result);die;