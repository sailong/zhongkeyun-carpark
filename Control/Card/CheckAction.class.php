<?php

import('Control.Card.CardController');

/**
 * 卡片检测
 * @author JZLJS00
 *
 */
class CheckAction extends CardController
{
	/**
	 * 模板前缀
	 * @var unknown_type
	 */
	private $tepPrefix = 'check_';
	
	/**
	 * 角色权限码
	 * @var unknown_type
	 */
	protected $level = 301;
	
	public function index()
	{
		$data = $this->objInput->postArr('data');
		$code = $data['code'];
		if(!empty($code))
		{
			$data = $this->getCardByCode($code);
			if(empty($data))
			{
				$this->showMessage(0,'卡片不存在','index');
			}
			$park = array();
			if(isset($data['park_info']) && !empty($data['park_info']))
			{
				foreach ($data['park_info'] as $value)
				{
					$park[] = $value['park_name'];
				}
			}
			$data['park'] = join(',',$park);
			$lossReport = $this->checkLossReport();
			$session = ClsFactory::Create('Model.mSession')->isSessionInfo($data['card_id']);
			if($session)
			{
				$session['start_time'] = date('Y/m/d H:i',$session['start_time']);
				$session['end_time'] = date('Y/m/d H:i', $session['end_time']);
			}
			$issueLog = ClsFactory::Create('Model.mIssueLog')->getIssueLogByCond('card_id='.$data['card_id']);
			if(isset($issueLog[0]))
			{
				$data['remark'] = $issueLog[0]['remark'];
			}
			$this->assign('code',$code);
			$this->assign('lossReport',$lossReport);
			$this->assign('session',$session);
		}
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'index');
	}
	
	
	
	
}