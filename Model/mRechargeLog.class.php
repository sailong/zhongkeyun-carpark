<?php
class mRechargeLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getRechargeLogById($recharge_log_ids) {
		if (empty($recharge_log_ids))
			return false;
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$result = $dRechargeLog->getRechargeLogById($recharge_log_ids);
		
		return $this->mixOtherRechargeLog($result);
	}
	
	public function  mixOtherRechargeLog($log_list) {
		$card_ids = array();
		foreach ($log_list as &$log) {
			if (!in_array($log['card_id'], $card_ids)) {
				$card_ids[] = $log['card_id'];
			}
		}
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = $mCard->getCardById($card_ids);
		foreach ($log_list as &$log) {
			$log = array_merge($card_info[$log['card_id']],$log);
		}
		return $log_list;
	}
	
	//$condition_str值：add_time >= 11333 或者 user_name='杨益' 或者user_name like '杨%' 或者 card_id=10，可以为空取出全部列表
	public function getRechargeLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$log_list = $dRechargeLog->getRechargeLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherRechargeLog($log_list);
	}
	
	//$condition_str同getRechargeLogByCond的$condition_str
	public function getRechargeLogCount($condition_str = '') {
	
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$count = $dRechargeLog->getRechargeLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyRechargeLogInfo($recharge_log_info, $recharge_log_id) {
		if (!is_numeric($recharge_log_id) || empty($recharge_log_info))
			return false;
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$last_info = array_pop($this->getRechargeLogById($recharge_log_id));
		if ($recharge_log_info['charge']) {
			if ($recharge_log_info['charge'] != $last_info['charge']) {
			    $info = array_pop($mCard->getCardById($last_info['card_id']));
				$card_info['money'] = $info['money'] - $last_info['charge'] + $recharge_log_info['charge'];
			}
		}
		$mCard->modifyCardInfo($card_info, $last_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$recharge_log_info['admin_id'] = $admin_info['admin_id'];
		$recharge_log_info['user_name'] = $admin_info['admin_name'];
		
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$result = $dRechargeLog->modifyRechargeLogInfo($recharge_log_info, $recharge_log_id);
		
		return $result;
	}
	
	public function addRechargeLog($recharge_log_info) {
		if (empty($recharge_log_info))
			return false;
		
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$info = array_pop($mCard->getCardById($recharge_log_info['card_id']));
		$card_info['money'] = $info['money'] + $recharge_log_info['charge'];
		$mCard->modifyCardInfo($card_info, $recharge_log_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$recharge_log_info['admin_id'] = $admin_info['admin_id'];
		$recharge_log_info['user_name'] = $admin_info['admin_name'];
		
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$result = $dRechargeLog->addRechargeLog($recharge_log_info);
		
		return $result;
	}
	
	//$recharge_log_infos为：array($recharge_log_info, $recharge_log_info, $recharge_log_info)，$recharge_log_info同addRechargeLog的参数
	public function batchAddRechargeLog($recharge_log_infos) {
		if (empty($recharge_log_infos))
			return false;
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
	
		$card_ids = array();
		foreach ($recharge_log_infos as $log) {
			if (!in_array($log['card_id'], $card_ids)) {
				$card_ids[] = $log['card_id'];
			}
		}
		//------------------------------------------------
		$smsModel = ClsFactory::Create('Model.mSendsms');
		//------------------------------------------------
		$mCard = ClsFactory::Create('Model.mCard');
		$infos = $mCard->getCardById($card_ids);
		foreach ($recharge_log_infos as &$recharge_log_info) {
			//修改卡片的余额
			$card_info = array();
			$info = $infos[$recharge_log_info['card_id']];
			$card_info['money'] = $info['money'] + $recharge_log_info['charge'];
			$ret = $mCard->modifyCardInfo($card_info, $recharge_log_info['card_id']);
			if($ret && $info['tel'])
			{
				//--储值充值发送短信-----------------------------------------------------------
				$card_info['tel'] = $info['tel'];
				$smsModel->sendSms($card_info,3,array('charge'=>$recharge_log_info['charge']));
				//---------------------------------------------------------------------
			}	
			$recharge_log_info['admin_id'] = $admin_info['admin_id'];
			$recharge_log_info['user_name'] = $admin_info['admin_name'];
		}
	
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$result = $dRechargeLog->batchAddRechargeLog($recharge_log_infos);
	
		return $result;
	}
	
	public function delRechargeLogInfo($recharge_log_id) {
		if (!is_numeric($recharge_log_id))
			return false;
		$dRechargeLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$result = $dRechargeLog->delRechargeLogInfo($recharge_log_id);
		
		return $result;
	}
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getChargeSum($condition_str = '') {
	
		$dIssueLog = ClsFactory::Create ( 'Data.dRechargeLog' );
		$sum = $dIssueLog->getChargeSum($condition_str);
		return $sum ? $sum : '0';
	}
	
}
