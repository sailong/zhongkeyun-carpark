<?php

/**
 * 
 * @author JZLJS00
 *
 */
class CommonController extends Controller
{
	/**
	 * 页大小
	 * @var int
	 */
	protected $_pageSize = 10;
	
	/**
	 * 错误消息
	 * @var unknown_type
	 */
	protected $_error = NULL;
	
	/**
	 * 单个卡的信息
	 * @var unknown_type
	 */
	protected $_data = NULL;
	
	public $mAdmin;
	
	/**
	 * 登录检测
	 */
	public function _initialize()
	{
		
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) 
		{
			$this->toLogin();
		}
		if ($this->level && !$this->mAdmin->checkCurrentUserLevel($this->level))
		{
			$this->toWelcome();
		}
		$this->assign('nav','报表统计');
	}
	
	/**
	 * 分页
	 * @param int $total 总数
	 * @return string 分页字符串
	 */
	protected function getPagination($total)
	{
		$show = '';
		if($total>$this->_pageSize)
		{
			import("ORG.Util.Page");
			$page = new Page($total, $this->_pageSize);
			$page->setConfig('rollPage', 7);
			$page->setConfig('first', '首页');
			$page->setConfig('last', '尾页');
			$show = $page->show();
		}
		return $show;
	}
	
	/**
	 * 获取偏移量
	 */
	protected function getOffset()
	{
		$p = C('VAR_PAGE');
		$page = $this->objInput->getInt($p);
		empty($page) && $page=1;
		return ($page-1) * $this->_pageSize;
	}
	
	/**
	 * 某人某一时间段内管理的门
	 * @param unknown_type $admin_id
	 * @param unknown_type $s_time
	 * @param unknown_type $e_time
	 */
	public function getManageDoors($admin_id,$s_time,$e_time)
	{
		$door_ids = '';
		$adminPostList = $this->mAdmin->getAdminPostByCond('admin_id='.$admin_id.' and end_time>='.$s_time.' and end_time<='.$e_time, 0, 0);
		$time_list = array();
		if($adminPostList)
		{
			$door_id_arr = array();
			foreach ($adminPostList as $key=>$a)
			{
				//$door_id_arr+= explode(',',$a['door']);
				$time_list[$key]['door'] = $a['door'];
				$time_list[$key]['s_time'] = $a['start_time'] < $s_time ? $s_time : $a['start_time']; 
				$time_list[$key]['e_time'] = $a['end_time'];
			}
			/* $door_id_arr = array_unique($door_id_arr);
			$door_ids = implode(',',$door_id_arr);
			$door_ids = trim($door_ids,','); */
		}
		return $time_list;
		return array('door_ids'=>$door_ids,'time_list'=>$time_list); 
	}
	
	public function getCharge($admin_id,$s_time,$e_time)
	{
		$condition .= ' add_time between %s and %s ';
		$condition = sprintf($condition,$s_time,$e_time);
		if($admin_id) $condition .= ' and admin_id = '.$admin_id;
		$data['issue_charge'] = ClsFactory::Create('Model.mIssueLog')->getChargeSum($condition);
		$data['delay_charge'] = ClsFactory::Create('Model.mDelayLog')->getChargeSum($condition);
		$data['recharge']     = ClsFactory::Create('Model.mRechargeLog')->getChargeSum($condition);
		$data['change_charge']= ClsFactory::Create('Model.mChangeLog')->getChargeSum($condition);
		$data['return_money'] = ClsFactory::Create('Model.mRecoverLog')->getReturnSum($condition);
		//----------------------------------------------------------------------------------------------
		$door_ids = '';
		if($admin_id)
		{
			$manageInfo = $this->getManageDoors($admin_id, $s_time, $e_time);
			//$door_ids = $manageInfo['door_ids'];
		}
		$baseCondition = ' end_time between %s and %s  and end_door_id is not null';
		$data['man_charge'] = $data['should_charge']  = $data['real_charge'] = $data['debit_charge'] = 0;
		if($admin_id)
		{
			$sessionModel = ClsFactory::Create('Model.mSession');
			if($manageInfo)
			{
				foreach ($manageInfo as $t)
				{
					if(!$t['door']) continue;
					
					$condition = $baseCondition.' and end_door_id in ('.$t['door'].')';
					
					$_baseCondition = sprintf($condition,$t['s_time'],$t['e_time']);
					//echo $_baseCondition.'<br>';
					//人工出场收费
					$data['man_charge'] += $sessionModel->getChargeSum('real_money',$_baseCondition.' and end_status = 1 ');
					//应收金额
					$data['should_charge'] += $sessionModel->getChargeSum('charge',$_baseCondition);
					//实收金额
					$data['real_charge'] += $sessionModel->getChargeSum('real_money',$_baseCondition);
					//储值卡扣款
					$data['debit_charge'] += ClsFactory::Create('Model.mConsumeLog')->getConsumelSum($admin_id,$t['s_time'],$t['e_time'],$t['door']);
				}
			}
			
		}
		return $data;
	}
	
	/**
	 * 获取出入厂记录统计
	 * @param unknown_type $park_type   into  out 
	 * @param unknown_type $card_type
	 * @param unknown_type $s_time
	 * @param unknown_type $e_time
	 */
	public function getParkLogStat($park_type,$s_time,$e_time,$cardCategoryList,$carCategoryList)
	{
		$park_type = strtolower($park_type);
		if($park_type=='into')
		{
			$condition='start_time >=%s and start_time <= %s';
		}else
		{
			$condition='end_time >=%s and end_time <= %s';
		}
		$condition = sprintf($condition,$s_time,$e_time);
		$model = ClsFactory::Create('Model.mSession');
		$list = $model->getSessionByCond($condition,0,10000000);
		/* var_dump($list);
		
		foreach ($cardCategoryList as $cid=>$type)
		{
			$stat[$cid] = 0;
		} */
		
		
		foreach ($cardCategoryList as $tid=>$card_c)
		{
			$stat[$tid] = 0;
			foreach ($carCategoryList as $car_c)
			{
				$stat[$tid.'-'.$car_c['cate_id']] = 0;
			}
		}
		$data['total'] = 0;
		if($list)
		{
			foreach ($list as $val)
			{
				$stat[$val['card_type']] +=1;
				$stat[$val['card_type'].'-'.$val['cate_id']] +=1;
			}
			$data['total'] = count($list);
		}
		//var_dump($stat['total']);
		$data['stat'] = $stat;
		return $data;
	}
}