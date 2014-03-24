<?php

import('Control.Report.CommonController');

class MonthChargeAction extends CommonController {
	
	private $tepPrefix = 'month_charge_';
	
	protected $level = 603;
	
	/**
	 * 列表
	 */
	public function index()
	{	
		$admin_id = trim($this->objInput->getStr('admin_id'));
		$date = trim($this->objInput->getStr('date'));
		$list = array();
		if($date)
		{
			$start_time = strtotime($date);
			$days = date('t', strtotime($date));
			for ($i=1;$i<=$days;$i++)
			{
				$data = array();
				$data['date'] = $date.'-'.($i>=10 ? $i : '0'.$i);
				$s_time = $start_time + ($i-1) * 86400;
				$e_time = $s_time + 86400 - 1;
				
				$data += $this->getCharge($admin_id,$s_time,$e_time);
				//获取
				$list[] = $data;
			}
			
		}
		
		$this->assign('list',$list);
		
		$adminList = $this->mAdmin->getAdminList(0,1000);
		$this->assign('adminList',$adminList);
		
		$this->assign('admin_id',$admin_id);
		$this->assign('date',$date);
		
		$this->display($this->tepPrefix.'index');
	}
}
