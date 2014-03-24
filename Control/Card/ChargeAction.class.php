<?php
import('Control.Card.CardController');

/**
 * 卡片充值
 * @author JZLJS00
 *
 */
class ChargeAction extends CardController
{
	/**
	 * 模板前缀
	 * @var unknown_type
	 */
	private $tepPrefix = 'charge_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 304;
	
	/**
	 * 充值列表
	 * @tod0 缺少付款金额
	 */
	public function index()
	{
		$search = $this->getSearch(array('code','add_time','user_name'));
		extract($search[1]);
		$offset = $this->getOffset();
		$data = ClsFactory::Create('Model.mRechargeLog')->getRechargeLogByCond($search[0],$offset,$this->_pageSize);
		$total = ClsFactory::Create('Model.mRechargeLog')->getRechargeLogCount($search[0]);
		$paging = $this->getPagination($total);
		$this->formatListData($data);
		$this->assign('data',$data);
		$this->assign('keyword',$keyword);
		$this->assign('searchType',$searchType);
		$this->assign('paging',$paging);
		$this->display($this->tepPrefix.'index');
	}
	
	/**
	 * 单个卡片充值
	 */
	public function chargeSingle()
	{
		$input = $this->objInput->postArr('data');
		$data = $masterCard = array();
		if(!empty($input))
		{
			$code = $input['code'];
			$data = $this->getCardByCode($code);
			if(empty($data))
			{
				$this->showMessage(0,'卡片不存在','single');
			}
			if(!$this->checkChargeAble())
			{
				$this->showMessage(0,$this->_error,'single');
			}
			$mCard = ClsFactory::Create('Model.mCard');
			if(!$data['is_master'])
			{
				//-查看该卡是否有对应的主卡---------------------------
				$masterCard = $mCard->getMasterCardInfo($data['card_id']);
				if($masterCard) $data = $masterCard;
				//--------------------------------------------
			}
			if(isset($input['charge']) && !empty($input['charge']))
			{
				if(isset($input['sub_card']) && !$input['remark'])
				{
					$input['remark'] = '通过副卡（'.$input['sub_card'].'）充值'.$input['charge'].'元';
				}
				$param = array('card_id'=>$data['card_id'],'charge'=>intval($input['charge']),'add_time'=>time(),'remark'=>$input['remark']);
				if(ClsFactory::Create('Model.mRechargeLog')->addRechargeLog($param)) {
					
					$card = array_pop($mCard->getCardById($data['card_id']));
					$card_info = array();
					if ($card['card_type'] != 1) {
						$card_info['code'] = $data['code'];
						$card_info['start_time'] = date('Y-m-d', time());
						$card_info['end_time'] = $card['card_type'] == 2 ? date('Y-m-d', time() + 5 * 60 * 60 * 24 * 30 * 12) : date('Y-m-d', $card['expire_time']);
						$card_info['status'] = 1;
						$card_info['park'] = $data['park'];
					}
					//--储值充值发送短信-----------------------------------------------------------
					$smsModel = ClsFactory::Create('Model.mSendsms');
					$smsModel->sendSms($card,3,array('charge'=>$input['charge']));
					//---------------------------------------------------------------------
					$this->showMessage(1,'充值成功！',__URL__.'/index', 1, array($card_info));
				}
				else 
					$this->showMessage(1,'充值失败！');
			}
		}
		$this->assign('code',$code);
		$this->assign('data',$data);
		$this->assign('masterCard',$masterCard);
		$this->display($this->tepPrefix.'single');
	}
	
	/**
	 * 批量充值
	 */
	public function chargeMulti()
	{
		$input = $this->objInput->postArr('data');
		if(!empty($input))
		{
			$start = $input['start'];
			$end = $input['end'];
			$card_type = $input['card_type'];
			$card_ids = range($start, $end);
			$batch = array();
			foreach ($card_ids as $cardId)
			{
				$data = $this->getCardById($cardId);
				if(!empty($data) && $data['card_type'] == $card_type)  //储值卡
				{
					if($this->checkChargeAble($cardId))
					{
						$batch[] = array('card_id'=>$cardId,'charge'=>number_format($input['charge'],2),'add_time' =>time(),'remark'=>$input['remark']);
					}
				}
			}
			if(!empty($batch))
			{
				if(ClsFactory::Create('Model.mRechargeLog')->batchAddRechargeLog($batch))
					$this->showMessage(1,'批量充值成功');
				else
					$this->showMessage(0,'批量充值失败');
			}else{
				$this->showMessage(0,'无符合条件的卡');
			}
		}
		$this->display($this->tepPrefix.'multi');
	}
	
	
}