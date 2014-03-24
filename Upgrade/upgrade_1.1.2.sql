//door 表 增加字段
ALTER TABLE `door` ADD COLUMN `show_left_parking` TINYINT(1) DEFAULT 0 NOT NULL COMMENT '0 不显示 1 显示' AFTER `park_id`; 