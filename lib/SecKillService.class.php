<?php

include('./lib/MysqlSecKill.class.php');

class SecKillService{
	private $db;
	
	public function __construct(){
		$this->db = MysqlSecKill::getInstanceof();
	}
	//查询所有秒杀记录
	public function getList(){
		$sql = "SELECT * FROM seckill";
		$result = $this->db->fetch_array($sql);
		return $result;
	}
	
	//查询单个秒杀记录
	public function getInfo($seckill_id){
		$sql = "SELECT * FROM seckill where seckill_id=$seckill_id";
		$info = $this->db->fetch_array($sql);
		return $info[0];
	}
	
	//秒杀开始时输出秒杀接口地址，否则输出系统时间和秒杀时间
	public function exportSecKillUrl($seckill_id){
		
		$result = array(
			'success'=>1,
			'msg'=>'',
			'data'=>array()
		);
		$info = $this->getInfo($seckill_id);
		
		$start_time = $info['start_time'];
		$end_time = $info['end_time'];
		$now_time = $this->nowTime();
		$exposed = true;
		if($now_time<$start_time || $now_time>$end_time)
		{
			$exposed  = false;
		}
		if($exposed)
		{
			$info['md5'] = $this->makeMd5($seckill_id);
		}
		$info['start_time'] = $info['start_time']*1000;
		$info['end_time'] = $info['end_time']*1000;
		$info['now_time'] = $this->nowTime()*1000;
		$info['exposed']=$exposed;
		$result['data']=$info;
		return $result;
	}
	
	//执行秒杀操作
	public function excuteSecKill($seckill_id,$user_phone,$md5){
		$result =array(
			'success'=>0,
			'msg'=>'秒单失败，请重试',
			'data'=>array()
		);

		if($md5 && $this->checkMd5($md5,$seckill_id))
		{
			//执行秒杀逻辑:减库存+记录购买行为，优化，两个行为
			//记录购买行为
			//减库存
			$arr_sql[]="INSERT INTO success_killed (seckill_id,user_phone,create_time) VALUES ($seckill_id,$user_phone,".$this->nowTime().")";
			$arr_sql[]="UPDATE seckill SET number=number-1 WHERE seckill_id=$seckill_id AND number>0";
			$res = $this->db->transaction($arr_sql);
			if($res)
			{
				$result['success']=1;
				$result['msg']='秒单成功！';
				$result['data']=array();
			}
		}
		return $result;
		
	}
	
	//执行秒杀操作 --存储过程实现
	public function excuteSecKillProcedure($seckill_id,$user_phone,$md5){
		$result =array(
			'success'=>0,
			'msg'=>'秒单失败，请重试',
			'data'=>array()
		);

		if($md5 && $this->checkMd5($md5,$seckill_id))
		{
			//执行秒杀逻辑:减库存+记录购买行为，优化，两个行为
			//记录购买行为
			//减库存
			$res = $this->db->procedure($seckill_id,$user_phone,$this->nowTime());
			if($res==1)
			{
				$result['success']=1;
				$result['msg']='秒单成功！';
				$result['data']=array();
			}elseif($res==0 || $res==-1 || $res==-3){
				$result['success']=1;
				$result['msg']='重复秒单！';
				$result['data']=array();
			}
			
		}
		return $result;
		
	}
	
	//返回系统时间
	public function nowTime(){
		return time();
	}
	
	//生成MD5值
	private function makeMd5($seckill_id){
		$str = $seckill_id.'_'.'/'.'%#$#@123asfbghSWR#5%5dfgg#%$^gb';
		return md5($str);
	}
	
	//验证MD5有效性
	private function checkMd5($md5_str,$seckill_id){
		$make_md5_str = $this->makeMd5($seckill_id);
		return $md5_str == $make_md5_str ? true :false;

	}
}