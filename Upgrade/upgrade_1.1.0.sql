alter table card add `is_master` int(11) NOT NULL default 0 COMMENT '1为主账户,0为副账户';
//session表增加停车车库字段
alter table `session` add `park_id` int(11) NOT NULL default 0 COMMENT 'park_id为0时为小区大门，不为0时则为停车场的门';
