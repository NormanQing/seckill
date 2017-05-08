<?php
//暴露秒杀地址

class Exposer{
	private $exposed;//标示是否开启秒杀
	
	private $secckill_id;
	//加密措施
	private $md5;
	
	//系统当前时间
	private $now;
	
	//开启时间
	private $start;
	
	//结束时间
	private $end;
	
	public function __construct($exposed,$md5,$seckill_id)
	{
		
	}

}