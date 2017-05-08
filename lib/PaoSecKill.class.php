<?php

//pod 类封装

namespace lib;
class PaoSecKill{
	protected $config=array(
		'hostname'=>'127.0.0.0',
		'database'=>'seckill',
		'port'=>'3306',
		'charset'=>'utf8',
	);
	protected $dbn;
	
	protected $user='root';
	protected $pwd='root';
	
	//远程连接
	public function __construct(){
		$dsn=$this->parseDsn($this->config);
		
		try{
			$this->dbn= new \Pdo($dsn,$this->user,$this->pwd);
		}catch(\PDOException $e){
			echo 'Connectin failed:'.$e->getMessage();
		}		
	}
	
	//dsn 组装
	protected function parseDsn($config){
		$dsn ='mysql:dbname='.$config['database'].';host='.$config['hostname'];
		
		if(!empty($config['port'])){
			$dsn .=';port='.$config['port'];
		}
		
		if(!empty($config['charset'])){
			$dsn .=';charset='.$config['charset'];
		}
		return $dsn;
	}
	
	//查询数据
	protected function queryList($sql, $args){
        $stmt = $dbn->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}