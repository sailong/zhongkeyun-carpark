<?php

import('Control.Report.CommonController');

class YearChargeAction extends CommonController {
	
	private $tepPrefix = 'year_charge_';
	
	protected $level = 604;
	
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
			for ($i=1;$i<=12;$i++)
			{
				$data = array();
				$data['date'] = $i;
				$s_time = strtotime($date.'-'.$i);
				$e_time =strtotime($date.'-'.($i+1)); - 1;
				
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
