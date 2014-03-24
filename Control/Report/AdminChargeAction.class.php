<?php

import('Control.Report.CommonController');

class AdminChargeAction extends CommonController {
	
	private $tepPrefix = 'admin_charge_';
	
	protected $level = 601;
	
	/**
	 * 列表
	 */
	public function index()
	{	
		
		$start_time = $this->objInput->getStr('start_time');
		$end_time = $this->objInput->getStr('end_time');
		
		if (!$start_time) $start_time = date('Y-m-d 00:00:00');
		$s_time = strtotime($start_time);
		
		if (!$end_time) $end_time = date('Y-m-d H:00:00');
		$e_time = strtotime($end_time);
		
		$list = array();
		if($start_time || $start_time)
		{
			$adminList = $this->mAdmin->getAdminList(0,1000);
			foreach ($adminList as $a)
			{
				$data = array();
				$data['date'] = $a['admin_name'];
				$data += $this->getCharge($a['admin_id'],$s_time,$e_time);
				//获取
				$list[] = $data;
			}
		}
		
		$this->assign('list',$list);
	
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		$this->display($this->tepPrefix.'index');
	}
}
