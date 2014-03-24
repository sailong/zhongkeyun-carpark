//admin 表 增加短信相关字段
ALTER TABLE `admin` ADD COLUMN `mobile` VARCHAR(11) DEFAULT '' NOT NULL COMMENT '手机号码' AFTER `last_time`, ADD COLUMN `is_receive_msg` TINYINT(1) DEFAULT 0 NOT NULL COMMENT '是否接受短信' AFTER `mobile`; 
