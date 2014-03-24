CREATE TABLE `message_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '短信发送日志表',
  `card_code` varchar(50) NOT NULL DEFAULT '',
  `mobile` char(11) NOT NULL DEFAULT '',
  `action` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 储值卡入场时 2 储值卡出场时  3卡片充值后',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
  `send_at` datetime NOT NULL COMMENT '发送时间',
  `send_status` tinyint(1) NOT NULL DEFAULT '0',
  `return_str` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `setting` ADD COLUMN `is_send_sms` TINYINT(1) DEFAULT 1 NULL COMMENT '是否发送短信' AFTER `repeat_park`; 

//增加车库进出门管理
alter table door add `park_id` int(11) NOT NULL default 0 COMMENT 'park_id为0时为小区大门，不为0时则为停车场的门';
