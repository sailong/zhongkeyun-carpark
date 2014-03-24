//card表增加备注字段
ALTER TABLE `card` ADD COLUMN `remark` VARCHAR(200) DEFAULT '' NULL COMMENT '备注' AFTER `is_master`; 
//修改字段名称
ALTER TABLE `card` CHANGE `remark` `note` VARCHAR(200) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NULL COMMENT '备注'; 