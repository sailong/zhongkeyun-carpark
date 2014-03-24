<?php

import('Control.Report.CommonController');

class OutDailyAction extends CommonController {
	
	private $tepPrefix = 'out_daily_';
	
	protected $level = 608;
	
	/**
	 * 列表
	 */
	public function index()
	{	
		$date = trim($this->objInput->getStr('date'));
		$list = array();
		if($date)
		{
			//先获取卡的类型
			$model = ClsFactory::Create('Model.mCardCategory');
			$cardCategoryList = $model->getCardCategory();
			//获取车型
			$model = ClsFactory::Create('Model.mCarCategory');
			$carCategoryList = $model->getCarCategory();
			$thArr[0] = '小时段';
			foreach ($cardCategoryList as $tid=>$card_c)
			{
				foreach ($carCategoryList as $car_c)
				{
					$thArr[$tid.'-'.$car_c['cate_id']] = $card_c.$car_c['cate_name'];
				}
				$thArr[$tid] = '<font color="red">'.$card_c.'小计</font>';
			}
			$thArr['total'] = '合计';
			$start_time = strtotime($date);
			for ($i=0;$i<24;$i++)
			{
				$data = array();
				$data['date'] = $i.':00-'.($i+1).':00';
				$s_time = $start_time + $i * 3600;
				$e_time = $s_time + 3599;
				
				$data += $this->getParkLogStat('out',$s_time,$e_time,$cardCategoryList,$carCategoryList);
				$list[] = $data;
			}
		}
		$this->assign('thArr',$thArr);
		$this->assign('list',$list);
		$this->assign('date',$date);
		$this->display($this->tepPrefix.'index');
	}
}
