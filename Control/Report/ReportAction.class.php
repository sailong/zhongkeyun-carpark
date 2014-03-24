<?php

/**
 * 报表类
 *
 */
class ReportAction extends Controller
{
	/**
	 * 页大小
	 * @var int
	 */
	protected $_pageSize = 10;
	
	/**
	 * 登录检测
	 */
	public function _initialize()
	{
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$mAdmin->isLogined()) 
		{
			$this->toLogin();
		}
		if (!$mAdmin->checkCurrentUserLevel($this->level))
		{
			//$this->toWelcome();
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
	 * 
	 * @param unknown_type $item
	 * @return array 0 返回搜索字符串,用来获取列表,1 搜索条件,用于填充搜索框
	 */
	protected function getSearch($item=array())
	{
		$condition = '';
		$extract = array();
		$searchType = $this->objInput->getStr('searchType');
		if(in_array($searchType, $item))
		{
			$keyword = $this->objInput->getStr('keyword');
			if(!empty($keyword))
			{
				if($searchType == 'code')
				{
					$card = ClsFactory::Create('Model.mCard')->getCardByCode($keyword);
					$condition = 'card_id='. $card[$keyword]['card_id'];
				}elseif($searchType == 'user_name')
				{
					$condition = "user_name='$keyword'";
				}elseif($searchType == 'add_time')
				{
					$condition = 'add_time >='.strtotime($keyword);
				}elseif($searchType == 'card_type')
				{
					$maps = self::getCardTypes();
					$condition = 'card_type='.array_search($keyword, $maps);
					
				}else{
					$condition = $searchType . '="' . $keyword.'"';
				}
			}
			$extract = array('searchType' => $searchType,'keyword' => $keyword);
		}
		return array($condition,$extract);
	}
	
	/**
	 * 是否是通过卡内码扫描搜索的
	 */
	protected function getSearchCode()
	{
		if($_GET['searchType'] == 'code')
		{
			$key = $_GET['keyword'];
			if(!empty($key) && is_numeric($key))
				return $key;
		}
		return false;
	}
	
	public function index()
	{
		header('location:/report/report/admincharge');exit;
	}
	
	public function adminCharge()
	{
		
		$this->display('charge');
	}
	
}