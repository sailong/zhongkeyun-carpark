alter table `session` add `park_status` int(11) NOT NULL default 0 COMMENT 'park_status为0时为进车库或者无车库默认状态，1时已经出库';
update `session` set park_status = 1 where `end_status` is not null and park_id > 0;
