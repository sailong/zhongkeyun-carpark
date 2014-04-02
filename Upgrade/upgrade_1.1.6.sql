ALTER TABLE `message_log` ADD COLUMN `extra` VARCHAR(50) DEFAULT '' NOT NULL COMMENT '扩展字段' AFTER `return_str`; 
ALTER TABLE `message_log` ADD INDEX (`action`); 