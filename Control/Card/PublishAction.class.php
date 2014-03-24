<?php

import('Control.Card.CardController');

/**
 * 卡片发行
 * @author JZLJS00
 *
 */
class PublishAction extends CardController {
	
	private $tepPrefix = 'publish_';
	
	protected $level = 302;
	
	/**
	 * 列表
	 */
	public function index()
	{
		$search = $this->getSearch(array('code','add_time','user_name'));
		extract($search[1]);
		$offset = $this->getOffset();
		$data = ClsFactory::Create('Model.mIssueLog')->getIssueLogByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mIssueLog')->getIssueLogCount($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('data',$data);
		$this->assign('paging',$paging);
		$this->display($this->tepPrefix.'index');
	}
	/**
	 * 添加/修改
	 */
	
	public function modify()
	{
		$id = $this->objInput->getInt('id');   //issueId
		$data = array();
		if(!empty($id))
		{
			$data = ClsFactory::Create('Model.mIssueLog')->getIssueLogById($id);
			$data = $data[$id];
			$park = array();
			if(!empty($data['park_info']))
			{
				foreach ($data['park_info'] as $value)
				{
					$park[] = $value['park_id'];
				}
			}
			$data['park'] = $park;
		}
		$carCategoryList = ClsFactory::Create('Model.mCarCategory')->getCarCategory();
		$parkCategoryList = ClsFactory::Create('Model.mPark')->getParkInfo();
		$cardCategoryList = self::getCardTypes();
		$this->assign('cardCategoryList',$cardCategoryList);
		$this->assign('carCategoryList',$carCategoryList);
		$this->assign('id',$id);
		$this->assign('data',$data);
		$this->assign('parkCategoryList',$parkCategoryList);
		$this->display($this->tepPrefix.'modify');
	}
	
	public function modifyDo()
	{
		$id = $this->objInput->postStr('id');  //IssueId
		$data = $this->objInput->postArr('data');
		if(isset($data['expire_time']))
			$data['expire_time'] = strtotime($data['expire_time']);
		$data['park'] = join(',',$data['park']);
		$model = ClsFactory::Create('Model.mCard');
		
		$data['address'] = trim($data['address']);
		$message_str = '';
		if($id)
		{
			$info = ClsFactory::Create('Model.mIssueLog')->getIssueLogById($id);
			$info = $info[$id];
			$data['issue_id'] = $info['issue_id'];
			$data['code'] = $info['code'];
			
			$mCard = ClsFactory::Create('Model.mCard');
			$card = array_pop($mCard->getCardById($info['card_id']));
			$card_info = array();
			if ($card['card_type'] != 1) {
				$card_info['code'] = $data['code'];
				$card_info['start_time'] = date('Y-m-d', time());
				$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
				$card_info['status'] = 1;
				$card_info['park'] = $data['park'];
			}
			/* if($data['address'] && $data['address'] !=$card['address'] )
			{
				if($model->hasShareParking($info['card_id'])) $this->showMessage(0,'修改失败：共享车位地址冲突,请重新发行！');
				$typeArr = $this->getCurrentCardTypes($data['address']);
				if($typeArr )
				{
					$typeCounts = count($typeArr);
					if($typeCounts > 1 || !isset($typeArr[$card['card_type']]))
					{
						$this->showMessage(0,'修改失败：该地址与其他共享车位地址冲突！');
					}
				}
			} */
			//禁止修改地址
			unset($data['address']);
			if( ($model->modifyCardInfo($data,$info['card_id'])) !== false)
			{
				$this->showMessage(1,'修改成功！',__URL__.'/index', 1, array($card_info));
			}
			$this->showMessage(0,'修改失败！');
		}else
		{
			$data['add_time'] = time();
			if($this->checkPublicAble($data['code']))
			{
				$money = $data['money'];
				//---主账号相关------------------------------------------
				if($data['address'] && $data['card_type'] == 2)
				{
					$master = $this->checkIsSetMasterByAddress($data['address']);
					if($master === -1)
					{
						//设置主账号
						//$data['is_master'] = 1;
						$message_str = '该卡已被设置为主账户';
					}elseif (is_array($master))
					{
						$data['money'] = CardController::SUB_CARD_INIT_MONEY;
					}
				}
				//---------------------------------------------
				if(($id = $model->addCard($data)) != false)
				{
					$mCard = ClsFactory::Create('Model.mCard');
					$card = array_pop($mCard->getCardById($id));
					$update_card_arr = $card_info = array();
					if ($card['card_type'] != 1) {
						$card_info['code'] = $data['code'];
						$card_info['start_time'] = date('Y-m-d', time());
						$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
						$card_info['status'] = 1;
						$card_info['park'] = $data['park'];
					}
					
					$update_card_arr[] = $card_info;
					//--如果有共享车位-----------------------------------
					if($data['address'] && $mCard->hasShareParking($id))
					{
						/* $update_card_arr = array();
						//把其他的卡信息从控制器里删除
						$subCardList = $mCard->getFamilyCardList($id);
						if($subCardList)
						{
							$park_ids = '';
							foreach ($subCardList as $sub)
							{
								if($sub['card_type'] == TEMPORARY_CARD_TYPE) continue;
								$tempData = array();
								$tempData['code'] = $sub['code'];
								$tempData['start_time'] = date('Y-m-d', time());
								$tempData['end_time'] = $sub['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $sub['expire_time']);
								$tempData['status'] = 1;
								$tempData['park'] = $sub['park'];
								$update_card_arr[] = $tempData;
								$park_ids.=$sub['park'].',';
							}
							foreach ($update_card_arr as $key=>$u)
							{
								$update_card_arr[$key]['park'] = $park_ids;
							} 
						} */
					}
					//---------------------------------------------------
					//充值到主账户---------------------------
					if (isset($master) && is_array($master))
					{
						$param = array('card_id'=>$master['card_id'],'charge'=>intval($money),'add_time'=>time(),'remark'=>$data['code'].' 充值到主账户（新发行）,充值   '.$money.'元');
						if(ClsFactory::Create('Model.mRechargeLog')->addRechargeLog($param))
						{
							$message_str = '成功把钱充值到主账户'.$master['code'];
						}else 
						{
							$message_str = '但充值到主账户失败！';
						}
					}
					//-----------------------------------
					$this->showMessage(1,'增加成功！'.$message_str,__URL__.'/index', 1, $update_card_arr);
				}else{
					$this->showMessage(0,'操作失败！');
				}
			}else{
				$this->showMessage(0,$this->_error);
			}
		}
	}
	
	/**
	 * 批量发行
	 */
	public function addMulti()
	{
		$cardCategoryList = self::getCardTypes();
		$carCategoryList = ClsFactory::Create('Model.mCarCategory')->getCarCategory();
		$parkCategoryList = ClsFactory::Create('Model.mPark')->getParkInfo();
		$this->assign('cardCategoryList',$cardCategoryList);
		$this->assign('carCategoryList',$carCategoryList);
		$this->assign('parkCategoryList',$parkCategoryList);
		$data = $this->objInput->postArr('data');
		if(!empty($data))
		{
			$code = $data['code'];
			$model = ClsFactory::Create('Model.mCard');
			$expireTime = $data['expire_time'];
			if($data['card_typ'] == 3 || $data['card_type'] == 4)
			{
				if(strtotime($expireTime)<time())
				{
					$this->showMessage(0,'有效期小于当前日期');
				}
			}
			$park = $data['park'];
			$data['expire_time'] = strtotime($data['expire_time']);
			if($this->checkPublicAble($code))
			{
				$data['expire_time'] = strtotime($expireTime);
				$data['park'] = join(',',$data['park']);
				if(!$model->addCard($data))
				{
					$this->showMessage(0,'发卡失败','addMulti');
				}
				
			}else{
				$this->showMessage(0,$this->_error,'addMulti');
			}
			$data['expire_time'] = $expireTime;
			$data['park'] = $park;
		}
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'multi');
	}
	
	/**
	 * 检测卡片是否已经存在
	 */
	public function checkExist()
	{
		$response = array();
		$code = $this->objInput->getInt('code');
		$data = $this->getCardByCode($code);
		$response = !empty($data) ? array('status'=>0,'msg'=>'该卡已经存在') : array('status'=>1,'该卡不存在');
		echo json_encode($response);
	}
}
