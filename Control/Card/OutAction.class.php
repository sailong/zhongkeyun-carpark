<?php
import('Control.Card.CardController');

/**
 * 卡片出场
 * @author JZLJS00
 *
 */
class OutAction extends CardController
{
	
	/**
	 * 模板前缀
	 * @var unknown_type
	 */
	private $tepPrefix = 'out_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 310;
	
	public function index()
	{
		$search = $this->getSearch(array('code','start_time','user_name','car_code'));
		extract($search[1]);
		$offset = $this->getOffset();
		if($this->objInput->getStr('searchType') == 'car_code')
		{
			$search[0] = $this->checkByCarCode($keyword);
		}
		$data = ClsFactory::Create('Model.mConsumeLog')->getConsumeLogByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mConsumeLog')->getConsumeLogCount($search[0]);
		$this->formatListData($data);
		foreach ($data as $key => &$value)
		{
			$else = ClsFactory::Create('Model.mSession')->getSessionById($value['session_id']);
			$value['start_time'] = date('Y/m/d H:i:s',$else[$value['session_id']]['start_time']);
			$value['end_time'] = date('Y/m/d H:i:s', $else[$value['session_id']]['end_time']);
			$data[$key] = $value;
		}
		$paging = $this->getPagination($total);
		$this->assign('data',$data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('paging',$paging);
		$this->display($this->tepPrefix.'index');
	}
	
	public function add()
	{
		$input = $this->objInput->postArr('data');
		$data = array();
		if(!empty($input))
		{
			$code = $input['code'];
			$data = $this->getCardByCode($code);
			if(empty($data))
			{
				$this->showMessage(0,'卡片不存在','add');
			}
			$session = ClsFactory::Create('Model.mSession')->isSessionInfo($data['card_id']);
			if(empty($session))
			{
				$this->showMessage(0,'暂未入场','add');
			}
			$session['start_time'] = date('Y/m/d H:i',$session['start_time']);
			$door = ClsFactory::Create('Model.mDoor')->getDoorById($session['start_door_id']);
			$data['startDoor'] = $door[$session['start_door_id']]['door_name'];
			$this->assign('session',$session);
			if(isset($input['charge'],$input['real_money']))
			{
				$end_time = strtotime($input['end_time']);
				if(empty($end_time))
				{
					$this->showMessage(0,'出场时间不能为空','add');
				}
				$param = array(
					'end_door_id'=>$input['end_door_id'],'end_status'=>1,'end_time'=>$end_time,
					'charge' => $input['charge'],'real_money' => $input['real_money'],'remark' => $input['remark']
				);
				if(ClsFactory::Create('Model.mSession')->addExitPark($param,$data['card_id']))
					$this->showMessage(0,'添加成功','index');
				else 
					$this->showMessage(0,'添加失败','add');
			}
		}
		$outDoors = array();
		$doors = ClsFactory::Create('Model.mDoor')->getDoorInfo();
		foreach ($doors as $door)
		{
			if($door['door_type'] == 1)
			{
				$outDoors[$door['door_id']] = $door['door_name'] . '__' . $door['door_addr'];
			}
		}
		$this->assign('outDoors',$outDoors);
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'add');
	}
	/**
	 * 根据车牌号筛选
	 * @param unknown_type $code
	 * @return string
	 */
	private function checkByCarCode($code)
	{
		$cardList = ClsFactory::Create('Model.mCard')->getCardListByCond("car_code like '%".$code."%'",0,0);
		if(!$cardList) return 'consume_id = 0';
		$cardIdArr = array();
		foreach ($cardList as $card)
		{
			$cardIdArr[] = $card['card_id'];
		}
		return " card_id in (".implode(',', $cardIdArr).")";
	}
}