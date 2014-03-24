//setting 表 增加一个字段
ALTER TABLE `setting` ADD COLUMN `allow_share_parking_into` TINYINT(1) DEFAULT 0 NULL COMMENT '是否允许共享车位进入车库 0 否 1 允许' AFTER `is_send_sms`; 