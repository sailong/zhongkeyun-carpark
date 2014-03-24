<?php

/**
 * 卡片相关的基类
 * @author JZLJS00
 *
 */
class CardController extends Controller
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
	
	//子账户初始金额
	const SUB_CARD_INIT_MONEY = 0;
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
			$this->toWelcome();
		}
		$this->assign('nav','卡片管理');
	}
	
	/**
	 * 卡片类型
	 * @return multitype:string
	 */
	public static function getCardTypes($type=null)
	{
		$maps =  array(
				1 => '临时卡',
				2 => '储值卡',
				3 => '月租卡',
				4 => '贵宾卡'
		);
		return !empty($type)&&isset($maps[$type]) ? $maps[$type] : $maps;
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
		$b64 = $this->objInput->getStr('b64');
		if(in_array($searchType, $item))
		{
			$keyword = $this->objInput->getStr('keyword');
			if($b64) $keyword = base64_decode($keyword);
			if(!empty($keyword))
			{
				if($searchType == 'code')
				{
					$card = $this->getCardByCode($keyword);
					$condition = 'card_id='.$card['card_id'];
					//$condition = 'card_id='. $card[ltrim(trim($keyword), '0')]['card_id'];
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
				} else if ($searchType == 'name') {
					$condition = "name like '%$keyword%'";
				} else if ($searchType == 'car_code') {
					$condition = "car_code like '%$keyword%'";
				}elseif($searchType == 'address')
				{
					$condition = "address like '%$keyword%'";
				}elseif($searchType == 'parking')
				{
					$condition = "parking='$keyword'";
				}elseif($searchType == 'card_status')
				{
					if($keyword==4)
					{
						$condition = 'money < 0';
					}elseif ($keyword==5)
					{
						$condition = ' card_type = 3 and expire_time <= '.strtotime(date('Y-m-d'));
					}
				}else{
					$condition = $searchType . '=' . $keyword;
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
			$key = intval($_GET['keyword']);
			if(!empty($key) && is_numeric($key))
				return $key;
		}
		return false;
	}
	
	/**
	 * 根据内码获取card_id
	 */
	protected function getCardIdByCode($code)
	{
		$codel = ltrim($code,'0');
		$card = ClsFactory::Create('Model.mCard')->getCardByCode();
		return $card[$code]['card_id'];
	}
	
	/**
	 * 主要为了处理变量封装
	 */
	protected function getCardByCode($code)
	{
		$code = ltrim($code,'0');
		$data = ClsFactory::Create('Model.mCard')->getCardByCode($code);
		$data = isset($data[$code]) ? $data[$code] : array();
		if(!empty($data))
		{
			if(!empty($data['expire_time']))
			{
				$data['format_expire_time'] = date('Y/m/d',$data['expire_time']);
			}
			$data['format_card_type'] = self::getCardTypes($data['card_type']);
			$data['format_add_time'] = date('Y/m/d H:i:s', $data['add_time']);
		}
		$this->_data = $data;
		return $data;
	}
	
	/**
	 * 
	 * @param int $id
	 * @return Ambigous <string, multitype:, unknown>
	 */
	protected function getCardById($id)
	{
		$data = ClsFactory::Create('Model.mCard')->getCardById($id);
		$data = isset($data[$id]) ? $data[$id] : array();
		if(!empty($data))
		{
			if(!empty($data['expire_time']))
			{
				$data['format_expire_time'] = date('Y/m/d',$data['expire_time']);
			}
			$data['format_card_type'] = self::getCardTypes($data['card_type']);
			$data['format_add_time'] = date('Y/m/d H:i:s', $data['add_time']);
		}
		$this->_data = $data;
		return $data;
	}
	
	/**
	 * 为了模板显示,格式化一些字段
	 */
	protected function formatListData(&$data=array())
	{
		$card_ids = array();
		foreach ($data as $card) {
			if (!in_array($card['card_id'], $card_ids)) {
				$card_ids[] = $card['card_id'];
			}
		}
		$mCard = ClsFactory::Create('Model.mCard');
		$status_info = $mCard->getCardStatusById($card_ids);
		
		foreach ($data as &$card)
		{
			
			if(isset($card['expire_time']) && !empty($card['expire_time']))
				$card['expire_time'] = date('Y/m/d',$card['expire_time']);
			if(isset($card['card_type']))
			{
				$card['card_type_code'] = $card['card_type']; 
				$card['card_type'] = self::getCardTypes($card['card_type']);
			}
			if(isset($card['add_time']))
				$card['add_time'] = date('Y/m/d H:i:s',$card['add_time']);
			if(isset($card['old_expire_time']) && !empty($card['old_expire_time']))
			{
				$card['old_expire_time'] = date('Y/m/d',$card['old_expire_time']);
			}
			if(isset($card['status']))
			{
				$card['status_code'] = $card['status'];
				$card['status'] = self::getCardStatusMsg($status_info[$card['card_id']]);
			}
			if(isset($card['start_time']))
			{
				$card['start_time'] = date('Y/m/d H:i:s',$card['start_time']);
			}
			if(isset($card['end_time']))
			{
				$card['end_time'] = date('Y/m/d H:i:s',$card['end_time']);
			}
		}
		
	}
	
	/**
	 * 获取卡片状态对应的描述信息
	 */
	public static function getCardStatusMsg($status)
	{
		$map = array(
			0 => '正常',
			1 => '该卡不存在',
			2 => '该卡已经挂失',
			3 => '该卡已经回收',
			4 => '该卡欠费',
			5 => '该卡过期'	
		);
		return isset($map[$status]) ? $map[$status] : '未知状态';
	}
	
	/**
	 * 检测卡片是否能够发行
	 */
	protected function checkPublicAble($code)
	{
		$data = $this->getCardByCode($code);
		if(!empty($data))
		{
			$this->_error = '该卡已经发行了';
			return false;
		}
		return true;
	}
	
	/**
	 * 检测是否可以延期
	 * @param int $cardId 卡ID
	 * @return bool 成功返回true 失败返回false
	 */
	protected function checkDelayAble($cardId=null)
	{
		if($this->_data['card_type'] == 1 || $this->_data['card_type'] == 2)
		{
			$this->_error = '临时卡和储值卡不能延期';
			return false;
		}
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		if(!in_array($status, array(0,5)))
		{
			$this->_error = self::getCardStatusMsg($status);
			return false;
		}
		return true;
	}
	
	/**
	 * 检测是否可以挂失
	 */
	protected function checkLossreportAble($cardId=null )
	{
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		if(!in_array($status, array(0,4,5)))
		{
			$this->_error = self::getCardStatusMsg($status);
			return false;
		}
		return true;
	}
	
	/**
	 * 检测卡片是否可以恢复
	 */
	protected function checkRestoreAble($cardId=null)
	{
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		if($status != 2)
		{
			if($status == 0)
			{
				$this->_error = '卡片尚未挂失过！,无法执行恢复操作';
			}else{
				$this->_error = self::getCardStatusMsg($status);
			}
			return false;
		}
		return true;
	}
	
	/**
	 * 检测卡片是否可以更换
	 * @param unknown_type $cardId
	 */
	protected function checkReplaceAble($cardId=null)
	{
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		if(!in_array($status, array(0,4,5)))
		{
			$this->_error = self::getCardStatusMsg($status);
			return false;
		}
		return true;
	}
	
	/**
	 * 检测就卡是否可以被替换
	 * @param unknown_type $cardId
	 */
	protected function checkOldReplaceAble($cardId)
	{
		
	}
	
	/**
	 * 检测该卡是否可以用来替换旧卡
	 * @param unknown_type $cardId
	 */
	protected function checkCanReplace($new_code)
	{
		$data = $this->getCardByCode($new_code);
		if(!empty($data))
		{
			$this->_error = '该卡已经存在';
			return false;
		}
		return true;
	}
	
	/**
	 * 检测卡片是否可以回收
	 * @param unknown_type $cardId
	 */
	protected function checkRecoverAble($cardId)
	{
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		if(!in_array($status, array(0,4,5)))
		{
			$this->_error = self::getCardStatusMsg($status);
			return false;
		}
		return true;
	}
	
	/**
	 * 检测卡片是否可以充值,只储值卡可以充值
	 * @param unknown_type $cardId
	 */
	protected function checkChargeAble($cardId)
	{
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = empty($this->_data) ? $this->_data : $this->getCardById($cardId);
		if($data['card_type'] != 2)
		{
			$this->_error = '非储值卡不可以充值';
			return false;
		}
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		if(!in_array($status, array(0,4)))
		{
			$this->_error = self::getCardStatusMsg($status);
			return false;
		}
		return true;
	}
	
	/**
	 * 检测卡片是否已经挂失
	 */
	protected function checkLossReport($cardId)
	{
		$cardId = !empty($cardId) ? $cardId : $this->_data['card_id'];
		$data = ClsFactory::Create('Model.mCard')->getCardStatusById($cardId);
		$status = isset($data[$cardId]) ? $data[$cardId] : 'N';
		return $status == 2 ? true : false;
	}
	
	/**
	 * 根据业主联系地址判断是否设置了主卡
	 */
	protected function checkIsSetMasterByAddress($address)
	{
		$data = ClsFactory::Create('Model.mCard')->getCardListByCond('address="'.$address.'" and card_type = 2 and `status` != 2', 0, 0);
		if($data)
		{
			foreach ($data as $val)
			{
				if($val['is_master'] == 1) return $val;
			}
		}
		return -1;
	}
	/**
	 * 家庭成员副卡（储值卡）
	 * @param unknown_type $card_id
	 */
	public function getFamilySubCardList($card_id)
	{
		$subCardList = array();
		$list = ClsFactory::Create('Model.mCard')->getFamilyCardList($card_id);
		if($list)
		{
			foreach ($list as $sub)
			{
				if($sub['card_type'] != 2 || $sub['is_master']) continue;
				$subCardList[$sub['card_id']] = $sub;
			}
		}
		return $subCardList;
	}
	/**
	 * 判断是否符合共享车位的条件
	 * @param unknown_type $address
	 * @return unknown|number
	 */
	public function getCurrentCardTypes($address) 
	{
		$data = ClsFactory::Create('Model.mCard')->getCardListByCond('address="'.$address.'" and card_type != 1 and `status` != 2', 0, 0);
		if($data)
		{
			$types = array();
			foreach ($data as $val)
			{
				if(isset($types[$val['card_type']]))
				{
					$types[$val['card_type']]++;
				}else
				{
					$types[$val['card_type']] = 1;
				}
			}
			return $types;
		}
		return false;
	}
}