<?php
class mChangeLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getChangeLogById($change_log_ids) {
		if (empty($change_log_ids))
			return false;
		$dChangeLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$result = $dChangeLog->getChangeLogById($change_log_ids);
		
		return $this->mixOtherChangeLog($result);
	}
	
	public function  mixOtherChangeLog($log_list) {
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
	public function getChangeLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dChangeLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$log_list = $dChangeLog->getChangeLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherChangeLog($log_list);
	}
	
	//$condition_str同getChangeLogByCond的$condition_str
	public function getChangeLogCount($condition_str = '') {
	
		$dChangeLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$count = $dChangeLog->getChangeLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyChangeLogInfo($change_log_info, $change_log_id) {
		if (!is_numeric($change_log_id) || empty($change_log_info))
			return false;
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$last_info = array_pop($this->getChangeLogById($change_log_id));
		$card_info['code'] = $change_log_info['new_code'];
		$mCard->modifyCardInfo($card_info, $last_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$change_log_info['admin_id'] = $admin_info['admin_id'];
		$change_log_info['user_name'] = $admin_info['admin_name'];
		
		$dChangeLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$result = $dChangeLog->modifyChangeLogInfo($change_log_info, $change_log_id);
		
		return $result;
	}
	
	public function addChangeLog($change_log_info) {
		if (empty($change_log_info))
			return false;
		
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$card_info['code'] = $change_log_info['new_code'];
		$mCard->modifyCardInfo($card_info, $change_log_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$change_log_info['admin_id'] = $admin_info['admin_id'];
		$change_log_info['user_name'] = $admin_info['admin_name'];
		
		$dChangeLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$result = $dChangeLog->addChangeLog($change_log_info);
		
		return $result;
	}
	
	public function delChangeLogInfo($change_log_id) {
		if (!is_numeric($change_log_id))
			return false;
		$dChangeLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$result = $dChangeLog->delChangeLogInfo($change_log_id);
		
		return $result;
	}
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getChargeSum($condition_str = '') {
	
		$dIssueLog = ClsFactory::Create ( 'Data.dChangeLog' );
		$sum = $dIssueLog->getChargeSum($condition_str);
		return $sum ? $sum : '0';
	}
	
}
