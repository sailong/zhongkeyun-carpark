<?php
class mMenu extends mBase {
	
	//获取参数设置信息
	public function getMenuList($id=0) {
		$menuList = array(
				1=>array(
						'name'=>'维护权限',
						'subMenuList'=> array(
								array('id'=>101,'name'=>'修改密码'),
								array('id'=>102,'name'=>'设置参数'),
								array('id'=>103,'name'=>'系统维护'),
								array('id'=>104,'name'=>'设置收费'),
						)),
				2=>array(
						'name'=>'档案权限',
						'subMenuList'=> array(
								array('id'=>201,'name'=>'权限组档案'),
								array('id'=>202,'name'=>'操作员档案'),
								array('id'=>203,'name'=>'停车场档案'),
						)),
				'card'=>array(
						'name'=>'卡片权限',
						'wrapLength'=>5,
						'subMenuList'=> array(
								array('id'=>301,'name'=>'卡片检测','controller'=>'check'),
								array('id'=>302,'name'=>'卡片发行','controller'=>'publish'),
								array('id'=>303,'name'=>'卡片延期','controller'=>'delay'),
								array('id'=>304,'name'=>'卡片充值','controller'=>'charge'),
								array('id'=>305,'name'=>'卡片挂失','controller'=>'lossreport'),
								array('id'=>306,'name'=>'卡片恢复','controller'=>'restore'),
								array('id'=>307,'name'=>'卡片更换','controller'=>'replace'),
								array('id'=>308,'name'=>'卡片回收','controller'=>'recover'),
								array('id'=>309,'name'=>'卡片档案','controller'=>'archive'),
								array('id'=>310,'name'=>'卡片出场','controller'=>'out'),
						)),
				'monitor'=>array(
						'name'=>'监控权限',
						'subMenuList'=> array(
								array('id'=>401,'name'=>'出入监控','controller'=>'index'),
						)),
				'log'=>array(
						'name'=>'记录权限',
						'wrapLength'=>6,
						'subMenuList'=> array(
								array('id'=>501,'name'=>'入场记录','controller'=>'into'),
								array('id'=>502,'name'=>'出场记录','controller'=>'out'),
								//array('id'=>503,'name'=>'卡片记录'),
								array('id'=>504,'name'=>'在场停车', 'controller'=>'on'),
								array('id'=>505,'name'=>'交接班记录'),
								//array('id'=>506,'name'=>'值班流水账'),
						)),
				'report'=>array(
						'name'=>'报表权限',
						'wrapLength'=>5,
						'subMenuList'=> array(
								array('id'=>601,'name'=>'操作员收费', 'controller'=>'AdminCharge'),
								array('id'=>602,'name'=>'收费日报表', 'controller'=>'DailyCharge'),
								array('id'=>603,'name'=>'收费月报表', 'controller'=>'MonthCharge'),
								array('id'=>604,'name'=>'收费年报表', 'controller'=>'YearCharge'),
								array('id'=>605,'name'=>'入场日报表', 'controller'=>'IntoDaily'),
								array('id'=>606,'name'=>'入场月报表'),
								array('id'=>607,'name'=>'入场年报表'),
								array('id'=>608,'name'=>'出场日报表 ', 'controller'=>'OutDaily'),
								array('id'=>609,'name'=>'出场月报表'),
								array('id'=>610,'name'=>'出场年报表'),
						)),
		);
		return $id ? $menuList[$id]['subMenuList'] : $menuList;
	}
	
	
	
}

    
