<?php

class MysqlSecKill{
	protected $hostname='127.0.0.1';
	protected $database='seckill';
	protected $port=3306;
	protected $charset='utf8';
	protected $user='root';
	protected $pwd='root';
	
	protected $conn;
	static private $_instance;
	
	private function __construct()
	{
		$this->connect();
	}
	
	private function __clone(){
  }

	protected function connect(){
		$this->conn = mysql_connect($this->hostname, $this->user, $this->pwd);
		$this->query("SET NAMES 'utf8'", $this->conn);
		$this->select_db();
	}
	
	// 连接数据表
	protected function select_db(){
		//$this->result = mysql_select_db($database);
		//return $this->result;
		$this->query("use ".$this->database, $this->conn);
	}
	
	//获取实例对象
	public static function getInstanceof()
     {
		 if (FALSE == (self::$_instance instanceof self)) {
			 self::$_instance = new self();
		}
         return self::$_instance;
	}
	
	public function query($sql){
		return $this->result = mysql_query($sql, $this->conn);
	}
	
	// 将结果集保存为数组
     public function fetch_array($sql)
    {
		$result=array();
		$res=$this->query($sql);
		while($row = mysql_fetch_array($res, MYSQL_ASSOC)){
			$result[]=$row;
		}
		return $result;
	}
	
	public function insert($sql){
		return $this->query($sql);
	}
	
	//事务
	public function transaction($arr_sql){
		if(empty($arr_sql))
		{
			return;
		}
		
		//开启事务
		$this->query('START TRANSACTION');
		$result =true;

		foreach($arr_sql as $val){
			$res = $this->query($val);
			if($res==false){
				$result=false;
			}
		}
		
		if($result==false){
			//没有执行成功
			mysql_query("ROLLBACK");
		}else{
			//执行成功
			mysql_query("COMMIT");
		}
		
		return $result;
	}
	
	//存储过程
	public function procedure($seckill_id,$phone,$now){
		$this->query('set @r_result=-3');
		$this->query("call excute_seckill($seckill_id,$phone,$now,@r_result)");
		$res = $this->query("select @r_result as result");
		$row=mysql_fetch_row($res);
		return $row[0];
	}
}