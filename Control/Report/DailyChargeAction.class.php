<?php

import('Control.Report.CommonController');

class DailyChargeAction extends CommonController {
	
	private $tepPrefix = 'daily_charge_';
	
	protected $level = 602;
	
	/**
	 * 列表
	 */
	public function index()
	{	
		$admin_id = trim($this->objInput->getStr('admin_id'));
		$day = $this->objInput->getStr('day');
		$list = array();
		if($day)
		{
			$start_time = strtotime($day);
			for ($i=0;$i<24;$i++)
			{
				$data = array();
				$data['date'] = $i.':00-'.($i+1).':00';
				$s_time = $start_time + $i * 3600;
				$e_time = $s_time + 3599;
				
				$data += $this->getCharge($admin_id,$s_time,$e_time);
				//获取
				$list[] = $data;
			}
			
		}
		
		$this->assign('list',$list);
		
		$adminList = $this->mAdmin->getAdminList(0,1000);
		$this->assign('adminList',$adminList);
		
		$this->assign('admin_id',$admin_id);
		$this->assign('day',$day);
		
		$this->display($this->tepPrefix.'index');
	}
}
