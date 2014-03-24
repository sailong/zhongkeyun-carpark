<?php
class mIssueLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getIssueLogById($issue_log_ids) {
		if (empty($issue_log_ids))
			return false;
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$result = $dIssueLog->getIssueLogById($issue_log_ids);
		
		return $this->mixOtherIssueLog($result);
	}
	
	//$condition_str值：add_time >= 11333 或者 user_name='杨益' 或者user_name like '杨%'或者 card_id=10，可以为空取出全部列表
	public function getIssueLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$log_list = $dIssueLog->getIssueLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherIssueLog($log_list);
	}
	
	public function  mixOtherIssueLog($log_list) {
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
	
	//$condition_str同getIssueLogByCond的$condition_str
	public function getIssueLogCount($condition_str = '') {
	
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$count = $dIssueLog->getIssueLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyIssueLogInfo($issue_log_info, $issue_log_id) {
		if (!is_numeric($issue_log_id) || empty($issue_log_info))
			return false;
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$result = $dIssueLog->modifyIssueLogInfo($issue_log_info, $issue_log_id);
		
		return $result;
	}
	
	public function addIssueLog($issue_log_info) {
		if (empty($issue_log_info))
			return false;
		
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$result = $dIssueLog->addIssueLog($issue_log_info);
		
		return $result;
	}
	
	public function delIssueLogInfo($issue_log_id) {
		if (!is_numeric($issue_log_id))
			return false;
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$result = $dIssueLog->delIssueLogInfo($issue_log_id);
		
		return $result;
	}
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getChargeSum($condition_str = '') {
	
		$dIssueLog = ClsFactory::Create ( 'Data.dIssueLog' );
		$sum = $dIssueLog->getChargeSum($condition_str);
		return $sum ? $sum : '0';
	}
	
}
