<?php

import('Control.Card.CardController');

/**
 * 卡片延期
 * @author JZLJS00
 *
 */
class DelayAction extends CardController
{
	
	/**
	 * 模板前缀
	 * @var unknown_type
	 */
	private $tepPrefix = 'delay_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 303;
	
	/**
	 * 卡片延期列表
	 */
	public function index()
	{
		$search = $this->getSearch(array('code','add_time','user_name'));
		$offset = $this->getOffset();
		extract($search[1]);
		$data = ClsFactory::Create('Model.mDelayLog')->getDelayLogByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mDelayLog')->getDelayLogCount($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('data',$data);
		$this->assign('paging',$paging);
		$this->display($this->tepPrefix.'index');
	}
	
	/**
	 * 单个卡片延期
	 */
	public function delaySingle()
	{
		$input = $this->objInput->postArr('data');
		$data = array();
		if(!empty($input))
		{
			$data = $this->getCardByCode($input['code']);
			if(empty($data))
			{
				$this->showMessage(0,'该卡不存在');
			}
			if(!$this->checkDelayAble())
			{
				$this->showMessage(0, $this->_error);
			}
			if(isset($input['charge'],$input['new_expire_time']))
			{
				$new_expire_time = strtotime($input['new_expire_time']);
				if($new_expire_time < $data['expire_time'])
				{
					$this->showMessage(0,'新有效期小于原有效期');
				}
				$param = array(
					'card_id'=>$data['card_id'],'charge' => $data['charge'], 
					'old_expire_time' => $data['expire_time'], 'expire_time' => $new_expire_time,
					'add_time'=>time(),'charge' => intval($input['charge']),'remark'=>$input['remark']
				);
				
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
				
				if(ClsFactory::Create('Model.mDelayLog')->addDelayLog($param))
					$this->showMessage(1,'延期成功',__URL__ . '/index', 1, array($card_info));
				else
					$this->showMessage(0,'延期失败');
			}
		}
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'single');
	}
	
	/**
	 * 卡片批量延期
	 */
	public function delayMulti()
	{
		$input = $this->objInput->postArr('data');
		if(!empty($input))
		{
			$start = intval($input['start']);
			$end = intval($input['end']);
			if($start>$end)
			{
				$this->showMessage(0,'起始卡号大于结束卡号');
			}
			$expire_time = strtotime($input['expire_time']);
			if($expire_time<time())
			{
				$this->showMessage(0,'延期时间小于当前时间');
			}
			$charge = intval($input['charge']);
			$remark = trim($input['remark']);
			$card_ids = range($start, $end);
			$card_type = $input['card_type'];  //3 贵宾卡 4 月租卡 5 贵宾和月租
			$batch = array();
			foreach ($card_ids as $cardId)
			{
				$data = $this->getCardById($cardId);
				if($this->checkDelayAble())
				{
					if($card_type == 5 || $data['card_type'] == $card_type)  // 全部延期或只延期贵宾和月租
					{
						$batch[] = array(
							'card_id'=>$cardId,'charge'=>$charge,'old_expire_time'=>$data['expire_time'],
							'expire_time' => $expire_time,'add_time' =>time(),'charge'=>$charge,'remark'=>$remark
						);
					}
				}
			}
			if(!empty($batch))
			{
				if(ClsFactory::Create('Model.mDelayLog')->batchAddDelayLog($batch))
					$this->showMessage(1,'批量延期成功');
				else
					$this->showMessage(0,'批量延期失败');
			}else{
				$this->showMessage(0,'无可延期卡片');
			}
		}
		$this->display($this->tepPrefix.'multi');
	}
	
	
}