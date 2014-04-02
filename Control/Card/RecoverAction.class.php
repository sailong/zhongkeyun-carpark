<?php

import('Control.Card.CardController');

/**
 * 卡片回收
 * @author JZLJS00
 *
 */
class RecoverAction extends CardController
{
	
	private $tepPrefix = 'recover_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 308;
	
	/**
	 * 回收列表
	 */
	public function index()
	{
		$search = $this->getSearch(array('code','add_time','user_name'));
		extract($search[1]);
		$offset = $this->getOffset();
		if($searchType=='code') $search[0] = $keyword ? "code = '".$keyword."'" : '';
		$data = ClsFactory::Create('Model.mRecoverLog')->getRecoverLogByCond($search[0],$offset,$this->_pageSize);
		//print_r($data);die;
		$total = ClsFactory::Create('Model.mRecoverLog')->getRecoverLogCount($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('data',$data);
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
			if(!$this->checkRecoverAble())
			{
				$this->showMessage(0,$this->_error,'add');
			}
			if(isset($input['return_money'],$input['remark']))
			{
				$mCard = ClsFactory::Create('Model.mCard');
				$hasShareParking = $mCard->hasShareParking($data['card_id']);
				$param = array('card_id'=>$data['card_id'],'code'=>$code,'money'=>$data['money'],'return_money'=>$input['return_money'],'add_time'=>time(),'remark'=>trim($input['remark']));
				if(ClsFactory::Create('Model.mRecoverLog')->addRecoverLog($param)) {
					
					$card = array_pop($mCard->getCardById($data['card_id']));
					$update_card_arr = $card_info = array();
					
					
						$card_info['code'] = $data['code'];
						$card_info['start_time'] = date('Y-m-d', time());
						$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
						$card_info['status'] = 0;
						$card_info['park'] = $data['park'];
					
						$update_card_arr[] = $card_info;
						//--如果不符合共享车位-----------------------------------
					 	if($hasShareParking && !$mCard->hasShareParking($data['card_id']))
						{
							//把其他的卡信息从控制器删除
							$subCardList = $mCard->getFamilyCardList($data['card_id']);
							if($subCardList)
							{
								$master_card_id = 0;
								foreach ($subCardList as $sub)
								{
									if($sub['card_type']==2) 
									{
										$tempData = array();
										$tempData['code'] = $sub['code'];
										$tempData['start_time'] = date('Y-m-d', time());
										$tempData['end_time'] = $sub['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $sub['expire_time']);
										$tempData['status'] = 0;
										$tempData['park'] = $sub['park'];
										$update_card_arr[] = $tempData;
									}
								}
								uasort($update_card_arr, create_function('$a, $b', 'if (intval($a["code"]) == intval($b["code"])) { return 0; } return (intval($a["code"]) < intval($b["code"])) ? -1 : 1;'));
							}
						}	
						//---------------------------------------------------
					if ($data['is_master'])
					{
						$mCard->modifyCardInfo(array('is_master'=>0),$data['card_id']);
						//判断是否还有其他成员卡
						$FamilyCardList = $this->getFamilySubCardList($data['card_id']);
						if($FamilyCardList)
						{
							$this->showMessage(1,'回收成功,由于回收的是主账户卡片，需要重新设置主账户！跳转中......','/card/archive/index/b64/1/searchType/address/keyword/'.base64_encode($data['address']), 2, $update_card_arr);
						}
					}
					$this->showMessage(1,'回收成功',__URL__ . '/index', 1, $update_card_arr);
				}
				else
					$this->showMessage(0,'回收失败');
			}
		}
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'add');
	}
}