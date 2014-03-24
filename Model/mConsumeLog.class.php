<?php
class mConsumeLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getConsumeLogById($consume_log_ids) {
		if (empty($consume_log_ids))
			return false;
		$dConsumeLog = ClsFactory::Create ( 'Data.dConsumeLog' );
		$result = $dConsumeLog->getConsumeLogById($consume_log_ids);
		
		return $this->mixOtherConsumeLog($result);
	}
	
	public function  mixOtherConsumeLog($log_list) {
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
	
	//$condition_str值：card_id=$，可以为空取出全部列表
	public function getConsumeLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dConsumeLog = ClsFactory::Create ( 'Data.dConsumeLog' );
		$log_list = $dConsumeLog->getConsumeLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherConsumeLog($log_list);
	}
	
	//$condition_str同getConsumeLogByCond的$condition_str
	public function getConsumeLogCount($condition_str = '') {
	
		$dConsumeLog = ClsFactory::Create ( 'Data.dConsumeLog' );
		$count = $dConsumeLog->getConsumeLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyConsumeLogInfo($consume_log_info, $consume_log_id) {
		if (!is_numeric($consume_log_id) || empty($consume_log_info))
			return false;
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$consume_log_info['admin_id'] = $admin_info['admin_id'];
		$consume_log_info['user_name'] = $admin_info['admin_name'];
		
		$dConsumeLog = ClsFactory::Create ( 'Data.dConsumeLog' );
		$result = $dConsumeLog->modifyConsumeLogInfo($consume_log_info, $consume_log_id);
		
		return $result;
	}
	
	public function addConsumeLog($consume_log_info) {
		if (empty($consume_log_info))
			return false;
		
		$consume_log_info['add_time'] = time();
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		if (!isset($consume_log_info['admin_id'])) {
			$consume_log_info['admin_id'] = $admin_info['admin_id'];
			$consume_log_info['user_name'] = $admin_info['admin_name'];
		}
		
		$dConsumeLog = ClsFactory::Create ( 'Data.dConsumeLog' );
		$result = $dConsumeLog->addConsumeLog($consume_log_info);
		
		return $result;
	}
	
	public function delConsumeLogInfo($consume_log_id) {
		if (!is_numeric($consume_log_id))
			return false;
		$dConsumeLog = ClsFactory::Create ( 'Data.dConsumeLog' );
		$result = $dConsumeLog->delConsumeLogInfo($consume_log_id);
		
		return $result;
	}
	
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getConsumelSum($admin_id,$s_time,$e_time,$door){
	
		$model = ClsFactory::Create ( 'Data.dConsumeLog' );
		$sum = $model->getConsumelSum($admin_id,$s_time,$e_time,$door);
		return $sum ? $sum : '0';
	}
}
