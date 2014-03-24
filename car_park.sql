# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.13-log)
# Database: car_park
# Generation Time: 2013-12-23 09:37:09 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '密码',
  `group_id` int(11) NOT NULL COMMENT '权限组id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `last_time` int(11) DEFAULT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `user_name` (`admin_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;

INSERT INTO `admin` (`admin_id`, `admin_name`, `password`, `group_id`, `create_time`, `last_time`)
VALUES
	(1,'admin','e10adc3949ba59abbe56e057f20f883e',1,0,1387674048);

/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_group`;

CREATE TABLE `admin_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL DEFAULT '' COMMENT '权限组名称',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `levels` varchar(150) NOT NULL DEFAULT '' COMMENT '以逗号分隔开的权限id，如1,3,5',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `admin_group` WRITE;
/*!40000 ALTER TABLE `admin_group` DISABLE KEYS */;

INSERT INTO `admin_group` (`group_id`, `group_name`, `remark`, `levels`)
VALUES
	(1,'管理员','11','1,2');

/*!40000 ALTER TABLE `admin_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table admin_post
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_post`;

CREATE TABLE `admin_post` (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '在岗id',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `door` varchar(50) NOT NULL DEFAULT '' COMMENT '门号，door_id，多个以逗号分隔',
  `start_time` int(11) NOT NULL COMMENT '上班时间',
  `end_time` int(11) DEFAULT NULL COMMENT '下班时间，上下班时间不超过24小时',
  PRIMARY KEY (`post_id`),
  KEY `id_start` (`admin_id`,`start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `admin_post` WRITE;
/*!40000 ALTER TABLE `admin_post` DISABLE KEYS */;

INSERT INTO `admin_post` (`post_id`, `admin_id`, `door`, `start_time`, `end_time`)
VALUES
	(1,1,'1,2',1387633675,1387673994);

/*!40000 ALTER TABLE `admin_post` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table car_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `car_category`;

CREATE TABLE `car_category` (
  `cate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(20) NOT NULL DEFAULT '' COMMENT '车类型',
  PRIMARY KEY (`cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table card
# ------------------------------------------------------------

DROP TABLE IF EXISTS `card`;

CREATE TABLE `card` (
  `card_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '卡片编号',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '卡片内码',
  `card_type` int(11) NOT NULL COMMENT '卡片类别，1临时卡2储值卡3月租卡4贵宾卡',
  `expire_time` int(11) DEFAULT NULL COMMENT '有效期',
  `money` float DEFAULT NULL COMMENT '卡内余额',
  `car_code` varchar(50) DEFAULT '' COMMENT '卡片内码',
  `cate_id` int(11) NOT NULL COMMENT '车辆类型',
  `car_type` varchar(50) DEFAULT '' COMMENT '车辆型号',
  `car_color` varchar(50) DEFAULT NULL COMMENT '车辆颜色',
  `name` varchar(50) DEFAULT NULL COMMENT '车主姓名',
  `person_id` varchar(50) DEFAULT NULL COMMENT '身份证号',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `address` varchar(50) DEFAULT NULL COMMENT '联系地址',
  `add_time` int(11) NOT NULL COMMENT '发行时间',
  `parking` varchar(20) DEFAULT NULL COMMENT '所属车位',
  `park` varchar(100) DEFAULT '' COMMENT '所属停车场，park_id以逗号分隔的串',
  `status` int(11) NOT NULL COMMENT '卡片状态，0正常1挂失2回收',
  PRIMARY KEY (`card_id`),
  UNIQUE KEY `code` (`code`),
  KEY `add_time` (`add_time`),
  KEY `car_code` (`car_code`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `card` WRITE;
/*!40000 ALTER TABLE `card` DISABLE KEYS */;

INSERT INTO `card` (`card_id`, `code`, `card_type`, `expire_time`, `money`, `car_code`, `cate_id`, `car_type`, `car_color`, `name`, `person_id`, `tel`, `address`, `add_time`, `parking`, `park`, `status`)
VALUES
	(1,'124343',1,0,0,'京P13434',2,'s40','红色','杨益','42112619830711311X','18600805024','',1387540998,'1','1,2,3',2),
	(2,'43143',2,NULL,NULL,'',1,'',NULL,NULL,NULL,NULL,NULL,2147483647,NULL,'',0);

/*!40000 ALTER TABLE `card` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table change_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `change_log`;

CREATE TABLE `change_log` (
  `change_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `new_code` int(11) NOT NULL COMMENT '新卡内码',
  `old_code` int(11) NOT NULL COMMENT '旧卡内码',
  `charge` float NOT NULL COMMENT '付款金额',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`change_id`),
  KEY `card_id` (`card_id`),
  KEY `user_name` (`user_name`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table consume_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `consume_log`;

CREATE TABLE `consume_log` (
  `consume_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL COMMENT '卡片id',
  `session_id` int(11) NOT NULL COMMENT '停车id',
  `admin_id` int(11) NOT NULL COMMENT 'admin_id,0时为系统扣费',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名，system时为系统扣费',
  `add_time` int(11) NOT NULL COMMENT '扣费时间',
  `charge` float NOT NULL COMMENT '扣费金额：即为实收金额',
  `remark` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`consume_id`),
  KEY `card_id_time` (`card_id`,`add_time`),
  KEY `add_time` (`add_time`),
  KEY `admin_id` (`admin_id`),
  KEY `user_name` (`user_name`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `consume_log` WRITE;
/*!40000 ALTER TABLE `consume_log` DISABLE KEYS */;

INSERT INTO `consume_log` (`consume_id`, `card_id`, `session_id`, `admin_id`, `user_name`, `add_time`, `charge`, `remark`)
VALUES
	(1,1,6,1,'0',1387542936,0,NULL),
	(2,1,2,1,'0',1387543199,0,NULL),
	(3,1,1,1,'0',1387549214,0,NULL),
	(4,1,2,1,'0',1387552090,0,NULL),
	(5,1,3,1,'0',1387552265,0,NULL),
	(6,1,4,1,'0',1387552946,0,NULL),
	(7,1,5,1,'0',1387553458,0,NULL);

/*!40000 ALTER TABLE `consume_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table delay_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `delay_log`;

CREATE TABLE `delay_log` (
  `delay_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `old_expire_time` int(11) NOT NULL COMMENT '新有效期',
  `expire_time` int(11) NOT NULL COMMENT '新有效期',
  `charge` float NOT NULL COMMENT '付款金额',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`delay_id`),
  KEY `card_id` (`card_id`),
  KEY `add_time` (`add_time`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `delay_log` WRITE;
/*!40000 ALTER TABLE `delay_log` DISABLE KEYS */;

INSERT INTO `delay_log` (`delay_id`, `card_id`, `admin_id`, `user_name`, `old_expire_time`, `expire_time`, `charge`, `add_time`, `remark`)
VALUES
	(1,1,1,'admin',1389949590,1400318122,30,1387358122,'延期'),
	(2,1,1,'admin',1400318122,1400321447,30,1387361447,'延期'),
	(3,1,1,'admin',1400318122,1400321447,30,1387361447,'延期'),
	(4,1,1,'admin',1400318122,1400321447,30,1387361447,'延期'),
	(5,1,1,'admin',1400318122,1400321447,30,1387361447,'延期'),
	(6,1,1,'admin',1400321447,1400378675,30,1387418675,'延期'),
	(7,1,1,'admin',1400321447,1400378675,30,1387418675,'延期'),
	(8,1,1,'admin',1400321447,1400378675,30,1387418675,'延期'),
	(9,1,1,'admin',1400321447,1400378675,30,1387418675,'延期'),
	(10,1,1,'admin',1400378675,1400378870,30,1387418870,'延期'),
	(11,1,1,'admin',1400378675,1400378870,30,1387418870,'延期'),
	(12,1,1,'admin',1400378675,1400378870,30,1387418870,'延期'),
	(13,1,1,'admin',1400378675,1400378870,30,1387418870,'延期'),
	(14,1,1,'admin',1400378870,1400378940,30,1387418940,'延期'),
	(15,1,1,'admin',1400378870,1400378940,30,1387418940,'延期'),
	(16,1,1,'admin',1400378870,1400378940,30,1387418940,'延期'),
	(17,1,1,'admin',1400378870,1400378940,30,1387418940,'延期'),
	(18,1,1,'admin',1400378940,1400381586,30,1387421586,'延期'),
	(19,1,1,'admin',1400378940,1400381586,30,1387421586,'延期'),
	(20,1,1,'admin',1400378940,1400381586,30,1387421586,'延期'),
	(21,1,1,'admin',1400378940,1400381586,30,1387421586,'延期');

/*!40000 ALTER TABLE `delay_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table door
# ------------------------------------------------------------

DROP TABLE IF EXISTS `door`;

CREATE TABLE `door` (
  `door_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `door_name` varchar(30) NOT NULL DEFAULT '' COMMENT '门名称',
  `door_addr` int(11) NOT NULL COMMENT '门控制器地址',
  `door_ip` varchar(30) NOT NULL DEFAULT '' COMMENT '门控制器ip',
  `door_no` int(11) NOT NULL COMMENT '控制器门号',
  `mac` varchar(50) NOT NULL DEFAULT '' COMMENT 'mac地址',
  `brake_no` varchar(50) NOT NULL DEFAULT '' COMMENT '闸序号',
  `reader_no` varchar(50) NOT NULL DEFAULT '' COMMENT '读卡器序号',
  `door_type` int(11) NOT NULL COMMENT '门类型0进口1出口',
  PRIMARY KEY (`door_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `door` WRITE;
/*!40000 ALTER TABLE `door` DISABLE KEYS */;

INSERT INTO `door` (`door_id`, `door_name`, `door_addr`, `door_ip`, `door_no`, `mac`, `brake_no`, `reader_no`, `door_type`)
VALUES
	(1,'1=1',3300,'192.168.0.8',1,'08:00:27:3A:BC:65','','',0),
	(2,'2=2',3330,'192.168.0.9',2,'08:00:27:3A:BC:65','','',1);

/*!40000 ALTER TABLE `door` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table fee
# ------------------------------------------------------------

DROP TABLE IF EXISTS `fee`;

CREATE TABLE `fee` (
  `fee_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL COMMENT '车型类别id',
  `free_time` int(11) NOT NULL COMMENT '免费时间分钟',
  `start_time` float NOT NULL COMMENT '起始时间小时',
  `start_money` float NOT NULL COMMENT '起始金额元',
  `step_time` float NOT NULL COMMENT '后续时间小时',
  `step_money` float NOT NULL COMMENT '后续金额元',
  `max_money` float NOT NULL COMMENT '每天最高收费元',
  PRIMARY KEY (`fee_id`),
  UNIQUE KEY `cateid_feeid` (`cate_id`,`fee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `fee` WRITE;
/*!40000 ALTER TABLE `fee` DISABLE KEYS */;

INSERT INTO `fee` (`fee_id`, `cate_id`, `free_time`, `start_time`, `start_money`, `step_time`, `step_money`, `max_money`)
VALUES
	(1,2,30,1,5,1,2,20);

/*!40000 ALTER TABLE `fee` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table issue_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `issue_log`;

CREATE TABLE `issue_log` (
  `issue_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `money` float NOT NULL COMMENT '余额',
  `charge` float NOT NULL COMMENT '付款金额',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`issue_id`),
  KEY `card_id_time` (`card_id`,`add_time`),
  KEY `add_time` (`add_time`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `issue_log` WRITE;
/*!40000 ALTER TABLE `issue_log` DISABLE KEYS */;

INSERT INTO `issue_log` (`issue_id`, `card_id`, `admin_id`, `user_name`, `money`, `charge`, `add_time`, `remark`)
VALUES
	(1,1,1,'admin',100,120,1387357590,'开卡');

/*!40000 ALTER TABLE `issue_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table lossreport_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lossreport_log`;

CREATE TABLE `lossreport_log` (
  `lossreport_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`lossreport_id`),
  KEY `card_id` (`card_id`),
  KEY `user_name` (`user_name`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table park
# ------------------------------------------------------------

DROP TABLE IF EXISTS `park`;

CREATE TABLE `park` (
  `park_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '停车场编号',
  `park_name` varchar(100) NOT NULL DEFAULT '' COMMENT '停车场名称',
  `park_remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`park_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `park` WRITE;
/*!40000 ALTER TABLE `park` DISABLE KEYS */;

INSERT INTO `park` (`park_id`, `park_name`, `park_remark`)
VALUES
	(1,'12','1212'),
	(2,'32','3232'),
	(3,'42','4242');

/*!40000 ALTER TABLE `park` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table recharge_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recharge_log`;

CREATE TABLE `recharge_log` (
  `recharge_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `charge` float NOT NULL COMMENT '充值金额',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`recharge_id`),
  KEY `card_id` (`card_id`),
  KEY `user_name` (`user_name`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table recover_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recover_log`;

CREATE TABLE `recover_log` (
  `recover_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `code` int(11) NOT NULL COMMENT '卡内码',
  `money` float NOT NULL COMMENT '卡片余额',
  `return_money` float NOT NULL COMMENT '退还金额',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`recover_id`),
  KEY `card_id` (`card_id`),
  KEY `code` (`code`),
  KEY `user_name` (`user_name`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `recover_log` WRITE;
/*!40000 ALTER TABLE `recover_log` DISABLE KEYS */;

INSERT INTO `recover_log` (`recover_id`, `card_id`, `admin_id`, `user_name`, `code`, `money`, `return_money`, `add_time`, `remark`)
VALUES
	(1,1,1,'admin',124343,280,0,1387553458,'临时卡片出口人工开闸时回收');

/*!40000 ALTER TABLE `recover_log` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table restore_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `restore_log`;

CREATE TABLE `restore_log` (
  `restore_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `add_time` int(11) NOT NULL,
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`restore_id`),
  KEY `card_id` (`card_id`),
  KEY `user_name` (`user_name`),
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `session_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `card_id` int(11) NOT NULL COMMENT '卡片id',
  `start_door_id` int(11) NOT NULL COMMENT '进门门id',
  `start_status` int(11) NOT NULL COMMENT '进门状态，0自动开闸1人工开闸',
  `start_time` int(11) NOT NULL COMMENT '进门时间',
  `end_door_id` int(11) DEFAULT NULL COMMENT '出门id',
  `end_status` int(11) DEFAULT NULL COMMENT '出门状态，0自动开闸1人工开闸',
  `end_time` int(11) DEFAULT NULL COMMENT '出门时间',
  `charge` float DEFAULT NULL COMMENT '应收金额',
  `real_money` float DEFAULT NULL COMMENT '实收金额',
  PRIMARY KEY (`session_id`),
  KEY `id_start` (`card_id`,`start_time`),
  KEY `id_end` (`card_id`,`end_time`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;

INSERT INTO `session` (`session_id`, `card_id`, `start_door_id`, `start_status`, `start_time`, `end_door_id`, `end_status`, `end_time`, `charge`, `real_money`)
VALUES
	(1,1,1,1,1387542394,2,1,1387549214,7,0),
	(2,1,1,1,1387542394,2,1,1387552090,8,0),
	(3,1,1,1,1387542394,2,1,1387552265,8,0),
	(4,1,1,1,1387552708,2,1,1387552946,0,0),
	(5,1,1,0,1387553386,2,1,1387553458,0,0);

/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `setting`;

CREATE TABLE `setting` (
  `company_name` varchar(50) DEFAULT '' COMMENT '公司名称',
  `capture_stay` int(11) DEFAULT NULL COMMENT '抓拍图像保留天数',
  `del_capture` int(11) DEFAULT NULL COMMENT '自动删除过期图像，0否1是',
  `gocome_stay` int(11) DEFAULT NULL COMMENT '出入记录保留天数',
  `del_gocome` int(11) DEFAULT NULL COMMENT '自动删除过期出入记录，0否1是',
  `work_stay` int(11) DEFAULT NULL COMMENT '交接班记录保留天数',
  `del_work` int(11) DEFAULT NULL COMMENT '自动删除过期交接记录，0否1是',
  `park_count` int(11) DEFAULT NULL COMMENT '停车场总车位',
  `code_len` int(11) DEFAULT NULL COMMENT '初始编号长度',
  `least_money` int(11) DEFAULT NULL COMMENT '储值车最少余额',
  `image_contrast` int(11) DEFAULT NULL COMMENT '是否带图像对比0否1是',
  `voice_tips` int(11) DEFAULT NULL COMMENT '是否语音提示，0否1是',
  `display_carcode` int(11) DEFAULT NULL COMMENT '是否播放车牌,0不播放1播放',
  `entrance_image_addr` varchar(200) DEFAULT NULL COMMENT '入场抓拍图像存放地址',
  `export_image_addr` varchar(200) DEFAULT NULL COMMENT '出场抓拍图像存放地址',
  `entrance_reader` varchar(11) DEFAULT NULL COMMENT '入口台式读卡器',
  `export_reader` varchar(11) DEFAULT NULL COMMENT '出口台式读卡器',
  `card_type` varchar(11) DEFAULT NULL COMMENT '发卡器型号',
  `is_issue_check` int(11) DEFAULT NULL COMMENT '是否允许控制器发行及检测卡片，0不允许1允许',
  `repeat_entrance` int(11) DEFAULT NULL COMMENT '是否允许重复入场，0不允许，1允许',
  `repeat_export` int(11) DEFAULT NULL COMMENT '是否允许重复出场，0不允许1允许',
  `nontemp_contrast` int(11) DEFAULT NULL COMMENT '非临时车是否需要对比确认才可出场，0不允许1允许',
  `abb` varchar(1) DEFAULT NULL COMMENT '本地区车牌简写',
  `warn_days` int(11) DEFAULT NULL COMMENT '月租、贵宾车预警天数',
  `is_far_ready` int(11) DEFAULT NULL COMMENT '是否采用远距离读卡器，0不允许1允许',
  `seconds1` int(11) DEFAULT NULL COMMENT '间隔秒数',
  `artificial_brake` int(11) DEFAULT NULL COMMENT '是否允许人工控制道闸，0不允许1允许',
  `display_carcode1` int(11) DEFAULT NULL COMMENT '票箱屏显示车牌号码，0不显示1显示',
  `print_ticket` int(11) DEFAULT NULL COMMENT '是否出场自动打印票据,0不打印1打印',
  `record_artificial` int(11) DEFAULT NULL COMMENT '是否记录出门按钮动作，0不记录1记录',
  `repeat_park` int(11) DEFAULT NULL COMMENT '是否相同车位禁止重复入场，0不是1是'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;

INSERT INTO `setting` (`company_name`, `capture_stay`, `del_capture`, `gocome_stay`, `del_gocome`, `work_stay`, `del_work`, `park_count`, `code_len`, `least_money`, `image_contrast`, `voice_tips`, `display_carcode`, `entrance_image_addr`, `export_image_addr`, `entrance_reader`, `export_reader`, `card_type`, `is_issue_check`, `repeat_entrance`, `repeat_export`, `nontemp_contrast`, `abb`, `warn_days`, `is_far_ready`, `seconds1`, `artificial_brake`, `display_carcode1`, `print_ticket`, `record_artificial`, `repeat_park`)
VALUES
	('云健康',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
