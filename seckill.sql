-- 数据库初始化

-- 创建数据库
CREATE DATABASE seckill;

-- 使用数据库
use seckill;

-- 创建秒杀库存表
CREATE TABLE seckill(
seckill_id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
name varchar(120)  NOT NULL COMMENT '商品名称',
number int unsigned NOT NULL COMMENT '库存量',
start_time INT NOT NULL COMMENT '开始时间',
end_time INT NOT NULL COMMENT '结束时间',
create_time INT NOT NULL COMMENT '创建时间时间',
key idx_start_time(start_time),
key idx_end_time(end_time),
key idx_create_time(create_time)
)ENGINE=innoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 COMMENT='秒杀库存表';

-- 初始化数据
INSERT INTO seckill(name,number,start_time,end_time,create_time) 
VALUES
('1000元秒杀ipad',50,1488384000,1488470400,1488384000),
('500元秒杀iphone12',150,1488556800,1488643200,1488384000),
('300元秒杀小米5s',250,1489939200,1490198400,1488384000),
('100元秒杀iwatch',350,1506960000,1507564800,1488384000),
('1元秒杀iphone7',100,1494086400,1495209600,1488384000);

-- 秒杀成功明细
-- 用登陆认证相关的信息
CREATE TABLE success_killed(
seckill_id bigint unsigned NOT NULL,
user_phone bigint unsigned NOT NULL COMMENT '用户手机号',
state tinyint NOT NULL DEFAULT -1 COMMENT '状态表示 -1：无效 0：成功 1：已付款',
create_time INT NOT NULL COMMENT '创建时间时间',
PRIMARY KEY(seckill_id,user_phone),/*联合组键，防止重复秒杀*/
KEY idx_create_time(create_time)
)ENGINE=innoDB DEFAULT CHARSET=utf8 COMMENT='秒杀明细表';


-- 执行秒杀存储过程
DELIMITER $$ -- console;转成$$
-- 定义存储过程
-- 参数 in 输入参数 --out 输出参数
-- row_count() 返回上一条修改类型sql影响的行数
-- row_count() 0:未修改数据 >0:标示修改的行数 <0：表示sql错误 
CREATE PROCEDURE `seckill`.`excute_seckill`
(IN v_seckill_id INT, IN v_phone BIGINT, IN v_kill_time INT,OUT r_result INT)
	BEGIN
	DECLARE insert_count INT DEFAULT 0;
	START TRANSACTION;
	INSERT INTO success_killed
	(seckill_id,user_phone,create_time) VALUES
	(v_seckill_id,v_phone,v_kill_time);
	SELECT row_count() INTO insert_count;
	IF(insert_count=0) THEN
		ROLLBACK ;
		SET r_result=-1;
	ELSEIF(insert_count<0) THEN
		SET r_result=-2;
		ROLLBACK ;
	ELSE
		UPDATE seckill SET number=number-1
		WHERE seckill_id=v_seckill_id
		AND end_time>v_kill_time
		AND start_time<v_kill_time
		AND number>0;
		SELECT row_count() INTO insert_count;
		IF(insert_count=0) THEN
			ROLLBACK ;
			SET r_result=0;
		ELSEIF(insert_count<0) THEN
			ROLLBACK ;
			SET r_result=-2;
		ELSE
			COMMIT ;
			SET r_result=1;
		END IF;
	END IF;
END;
$$
-- 存储过程结束

SET @r_result=-3;
-- 执行存储过程
call excute_seckill(1000,13267744141,unix_timestamp(now()),@r_result);
