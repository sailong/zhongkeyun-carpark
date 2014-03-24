alter table session add `new_cate_id` int(11) NOT NULL default 0 COMMENT '阶梯收费类型id,0时无阶梯收费';
create index address_index on card (address);
ALTER TABLE `door` ADD COLUMN `led_ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'led ip地址';
ALTER TABLE `door` ADD COLUMN `lane` varchar(50) NOT NULL DEFAULT '' COMMENT '车道'; 