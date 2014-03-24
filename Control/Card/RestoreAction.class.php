<?php

import('Control.Card.CardController');

/**
 * 卡片恢复
 * @author JZLJS00
 *
 */
class RestoreAction extends CardController
{
	
	
	private $tepPrefix = 'restore_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 306;
	
	/**
	 * 恢复列表
	 */
	public function index()
	{
		$search = $this->getSearch(array('code','add_time','user_name'));
		extract($search[1]);
		$offset = $this->getOffset();
		$data = ClsFactory::Create('Model.mRestoreLog')->getRestoreLogByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mRestoreLog')->getRestoreLogCount($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('data',$data);
		$this->assign('paging',$paging);
		$this->display($this->tepPrefix.'index');
	}
	
	/**
	 * 卡片恢复
	 */
	public function add()
	{
		$input = $this->objInput->postArr('data');
		$data = array();
		if(!empty($input))
		{
			$data = $this->getCardByCode($input['code']);
			if(empty($data))
			{
				$this->showMessage(0,'该卡片不存在','add');
			}
			if(!$this->checkRestoreAble())
			{
				$this->showMessage(0,$this->_error,'add');
			}
			if(isset($input['remark']))
			{
				$param = array('card_id'=>$data['card_id'],'add_time'=>time(),'remark'=>trim($input['remark']));
				if(ClsFactory::Create('Model.mRestoreLog')->addRestoreLog($param)) {
					$mCard = ClsFactory::Create('Model.mCard');
					$card = array_pop($mCard->getCardById($data['card_id']));
					$card_info = array();
					if ($card['card_type'] != 1) {
						$card_info['code'] = $data['code'];
						$card_info['start_time'] = date('Y-m-d', time());
						$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
						$card_info['status'] = 1;
						$card_info['park'] = $data['park'];
					}
					
					$this->showMessage(1,'恢复成功',__URL__ . '/index', 1, array($card_info));
				}
				else
					$this->showMessage(0,'恢复失败');
			}
		}
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'add');
	}
	
	
}