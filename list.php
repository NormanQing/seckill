<?php
header("Content-type:text/html;charset=utf-8");  

include('./lib/SecKillService.class.php');

$service = new SecKillService;
$result = $service->getList();

include('./html/list.html');