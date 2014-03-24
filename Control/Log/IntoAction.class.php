<?php

import('Control.Log.CommonController');

class IntoAction extends CommonController {
	
	private $tepPrefix = '';
	
	protected $level = 501;
	
	public function _initialize()
	{
		$this->title = '入场记录';
		
		parent::_initialize();
	}
	
	/**
	 * 入场列表
	 */
	public function index()
	{	
		//var_dump($_POST);
		$type = $this->objInput->postStr('type');
		$str = $this->objInput->postStr('str');
		
		$start_time = $this->objInput->getStr('start_time');
		$end_time = $this->objInput->getStr('end_time');
		if (!$start_time) $start_time = date('Y-m-d 00:00:00');
		if (!$end_time) $end_time = date('Y-m-d H:00:00');
		
		$offset = $this->getOffset();
		$length = $this->_page_size;
		
		$session_list = array();
		$mCard = ClsFactory::Create('Model.mCard');
		$mSession = ClsFactory::Create('Model.mSession');
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		//门信息
		$mDoor = ClsFactory::Create('Model.mDoor');
		$door_list = $mDoor->getDoorInfo();
		$door_info = array();
		foreach ($door_list as $door) {
			$door_info[$door['door_id']] = $door;
		}
		
		if ($type) {
			$cond_str = '';
			$s_time = 0;
			$e_time = 0;
			switch (intval($type)) {
			case 1:
				{
					$cond_str = 'card_id='.$str;
					break;
				}
			case 2:
				{
					$cond_str = 'card_type='.array_search($str, $this->types);
					break;
				}
			case 3:
				{
					$cond_str = 'car_code like "'.$str.'%"';
					break;
				}
			case 4:
				{
					$cond_str = 'name like "'.$str.'%"';
					break;
				}
			case 5:
				{
					$s_time = strtotime($str);
					$e_time = $s_time + 24 * 60 * 60;
					break;
				}
			}
			
			if ($type < 5) {
				$card_list = $mCard->getCardListByCond($cond_str, 0, 0);
				$card_ids = array();
				foreach ($card_list as $card) {
					if (!in_array($card['card_id'], $card_ids)) {
						$card_ids[] = $card['card_id'];
					}
				}
				if ($card_list) {
					$str = 'card_id in ('.join(',', $card_ids).'), start_time desc';
					$session_list = $mSession->getSessionByCond($str, $offset, $length);
					$count = $mSession->getSessionCount($str);
				}
			} else {
				$str = 'start_time >'.$s_time.' and start_time <'.$e_time.', start_time desc';
				$session_list = $mSession->getSessionByCond($str, $offset, $length);
				$count = $mSession->getSessionCount($str);
			}
			
		} else {
			$admin_id = $this->objInput->getStr('admin_id');
			
			$s_time = strtotime($start_time);
			$e_time = strtotime($end_time);
			
			$cond_str = '';
			if (intval($admin_id)) {
				$doors = $this->getManageDoors($admin_id, $s_time, $e_time);
				if ($doors) {
					$post_sql = array();
					foreach ($doors as $post) {
						$s_time = $post['s_time'];
						$e_time = $post['e_time'];
						$post_sql[] = ' (start_door_id in ('.$post['door'].') and start_time >'.$s_time.' and start_time <'.$e_time.')';
					}
					$str = join('or', $post_sql).', start_time desc';
					$session_list = $mSession->getSessionByCond($str, $offset, $length);
					$count = $mSession->getSessionCount($str);
				}
			} else {
				$str = 'start_time >'.$s_time.' and start_time <'.$e_time.', start_time desc';
				$session_list = $mSession->getSessionByCond($str, $offset, $length);
				$count = $mSession->getSessionCount($str);
			}
		}
		
		//卡片信息
		$card_ids = array();
		foreach ($session_list as $session) {
			if (!in_array($session['card_id'], $card_ids)) {
				$card_ids[] = $session['card_id'];
			}
		}
		$card_list = $mCard->getCardById($card_ids);
		
		$list = array();
		foreach ($session_list as &$session) {
			$session['start_time_str'] = date('Y-m-d H:i:s', $session['start_time']);
			$session['expire_time_str'] = date('Y-m-d', $session['expire_time']);
			$session['start_door_name'] = $door_info[$session['start_door_id']]['door_name'];
			$session['card_type_name'] = $this->types[$session['card_type']];
			//获取当班人员值班信息
			$post = $mAdmin->getAdminPostByDoorTime($session['start_door_id'], $session['start_time']);
			$session['admin_name'] = $post['admin_name'];
			
			$list[] = array_merge($session, $card_list[$session['card_id']]);
		}
		
		$this->assign('list',$list);
	
		$this->assign('start_time',$start_time);
		$this->assign('end_time',$end_time);
		
		$adminList = $this->mAdmin->getAdminList(0,1000);
		$this->assign('adminList',$adminList);
		
		$paging = $this->getPagination($count);
		$this->assign('paging',$paging);
		
		$this->display('into');
	}

}
