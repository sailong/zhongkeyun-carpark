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
	protected $_page_size = 10;
	
	/**
	 * 错误消息
	 * @var unknown_type
	 */
	protected $_error = NULL;
	
	protected $types =  array(
			1 => '临时卡',
			2 => '储值卡',
			3 => '月租卡',
			4 => '贵宾卡'
	);
	
	protected $title;
	
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
		$this->assign('nav',$this->title);
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
			foreach ($adminPostList as $key=>$a)
			{
				$time_list[$key]['door'] = $a['door'];
				$time_list[$key]['s_time'] = $a['start_time'] < $s_time ? $s_time : $a['start_time']; 
				$time_list[$key]['e_time'] = $a['end_time'];
			}
		}
		return $time_list; 
	}
	
	protected function getOffset()
	{
		$p = C('VAR_PAGE');
		$page = $this->objInput->getInt($p);
		empty($page) && $page=1;
		return ($page-1) * $this->_page_size;
	}
	
	protected function getPagination($total)
	{
		$show = '';
		if($total>$this->_page_size)
		{
			import("ORG.Util.Page");
			$page = new Page($total, $this->_page_size);
			$page->setConfig('rollPage', 7);
			$page->setConfig('first', '首页');
			$page->setConfig('last', '尾页');
			$show = $page->show();
		}
		return $show;
	}
}