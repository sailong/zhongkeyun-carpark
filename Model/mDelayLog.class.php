<?php
class mDelayLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getDelayLogById($delay_log_ids) {
		if (empty($delay_log_ids))
			return false;
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$result = $dDelayLog->getDelayLogById($delay_log_ids);
		
		return $this->mixOtherDelayLog($result);
	}
	
	public function  mixOtherDelayLog($log_list) {
		$card_ids = array();
		foreach ($log_list as &$log) {
			if (!in_array($log['card_id'], $card_ids)) {
				$card_ids[] = $log['card_id'];
			}
		}
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = $mCard->getCardById($card_ids);
		foreach ($log_list as &$log) {
			$log = array_merge($log, $card_info[$log['card_id']]);
		}
		return $log_list;
	}
	
	//$condition_str值：add_time >= 11333 或者 user_name='杨益' 或者user_name like '杨%'或者 card_id=10，可以为空取出全部列表
	public function getDelayLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$log_list = $dDelayLog->getDelayLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherDelayLog($log_list);
	}
	
	//$condition_str同getDelayLogByCond的$condition_str
	public function getDelayLogCount($condition_str = '') {
	
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$count = $dDelayLog->getDelayLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyDelayLogInfo($delay_log_info, $delay_log_id) {
		if (!is_numeric($delay_log_id) || empty($delay_log_info))
			return false;
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$last_info = array_pop($this->getDelayLogById($delay_log_id));
		if ($delay_log_info['charge']) {
			if ($delay_log_info['charge'] != $last_info['charge']) {
			    $info = array_pop($mCard->getCardById($last_info['card_id']));
				$card_info['money'] = $info['money'] - $last_info['charge'] + $delay_log_info['charge'];
			}
		}
		if ($delay_log_info['expire_time']) $card_info['expire_time'] = $delay_log_info['expire_time'];
		$mCard->modifyCardInfo($card_info, $last_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$delay_log_info['admin_id'] = $admin_info['admin_id'];
		$delay_log_info['user_name'] = $admin_info['admin_name'];
		
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$result = $dDelayLog->modifyDelayLogInfo($delay_log_info, $delay_log_id);
		
		return $result;
	}
	
	public function addDelayLog($delay_log_info) {
		if (empty($delay_log_info))
			return false;
		
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$info = array_pop($mCard->getCardById($delay_log_info['card_id']));
		$card_info['money'] = $info['money'] + $delay_log_info['charge'];
		$card_info['expire_time'] = $delay_log_info['expire_time'];
		$mCard->modifyCardInfo($card_info, $delay_log_info['card_id']);
		
		$delay_log_info['old_expire_time'] = $info['expire_time'];
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$delay_log_info['admin_id'] = $admin_info['admin_id'];
		$delay_log_info['user_name'] = $admin_info['admin_name'];
		
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$result = $dDelayLog->addDelayLog($delay_log_info);
		
		return $result;
	}
	//$delay_log_infos为：array($delay_log_info, $delay_log_info, $delay_log_info)，$delay_log_info同addDelayLog的参数
	public function batchAddDelayLog($delay_log_infos) {
		if (empty($delay_log_infos))
			return false;
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		
		$card_ids = array();
		foreach ($delay_log_infos as $log) {
			if (!in_array($log['card_id'], $card_ids)) {
				$card_ids[] = $log['card_id'];
			}
		}
		$mCard = ClsFactory::Create('Model.mCard');
		$infos = $mCard->getCardById($card_ids);
		foreach ($delay_log_infos as &$delay_log_info) {
			//修改卡片的余额
			$card_info = array();
			$info = $infos[$delay_log_info['card_id']];
			$card_info['money'] = $info['money'] + $delay_log_info['charge'];
			$card_info['expire_time'] = $delay_log_info['expire_time'];
			$mCard->modifyCardInfo($card_info, $delay_log_info['card_id']);
		
			$delay_log_info['old_expire_time'] = $info['expire_time'];
			
			$delay_log_info['admin_id'] = $admin_info['admin_id'];
			$delay_log_info['user_name'] = $admin_info['admin_name'];
		}
		
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$result = $dDelayLog->batchAddDelayLog($delay_log_infos);
	
		return $result;
	}
	
	public function delDelayLogInfo($delay_log_id) {
		if (!is_numeric($delay_log_id))
			return false;
		$dDelayLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$result = $dDelayLog->delDelayLogInfo($delay_log_id);
		
		return $result;
	}
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getChargeSum($condition_str = '') {
		$dIssueLog = ClsFactory::Create ( 'Data.dDelayLog' );
		$sum = $dIssueLog->getChargeSum($condition_str);
		return $sum ? $sum : '0';
	}
	
}
