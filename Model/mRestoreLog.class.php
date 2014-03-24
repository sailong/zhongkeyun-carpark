<?php
class mRestoreLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getRestoreLogById($restore_log_ids) {
		if (empty($restore_log_ids))
			return false;
		$dRestoreLog = ClsFactory::Create ( 'Data.dRestoreLog' );
		$result = $dRestoreLog->getRestoreLogById($restore_log_ids);
		
		return $this->mixOtherRestoreLog($result);
	}
	
	public function  mixOtherRestoreLog($log_list) {
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
	public function getRestoreLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dRestoreLog = ClsFactory::Create ( 'Data.dRestoreLog' );
		$log_list = $dRestoreLog->getRestoreLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherRestoreLog($log_list);
	}
	
	//$condition_str同getRestoreLogByCond的$condition_str
	public function getRestoreLogCount($condition_str = '') {
	
		$dRestoreLog = ClsFactory::Create ( 'Data.dRestoreLog' );
		$count = $dRestoreLog->getRestoreLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyRestoreLogInfo($restore_log_info, $restore_log_id) {
		if (!is_numeric($restore_log_id) || empty($restore_log_info))
			return false;
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$restore_log_info['admin_id'] = $admin_info['admin_id'];
		$restore_log_info['user_name'] = $admin_info['admin_name'];
		
		$dRestoreLog = ClsFactory::Create ( 'Data.dRestoreLog' );
		$result = $dRestoreLog->modifyRestoreLogInfo($restore_log_info, $restore_log_id);
		
		return $result;
	}
	
	public function addRestoreLog($restore_log_info) {
		if (empty($restore_log_info))
			return false;
		
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$card_info['status'] = 0;
		$mCard->modifyCardInfo($card_info, $restore_log_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$restore_log_info['admin_id'] = $admin_info['admin_id'];
		$restore_log_info['user_name'] = $admin_info['admin_name'];
		
		$dRestoreLog = ClsFactory::Create ( 'Data.dRestoreLog' );
		$result = $dRestoreLog->addRestoreLog($restore_log_info);
		
		return $result;
	}
	
	public function delRestoreLogInfo($restore_log_id) {
		if (!is_numeric($restore_log_id))
			return false;
		$dRestoreLog = ClsFactory::Create ( 'Data.dRestoreLog' );
		$result = $dRestoreLog->delRestoreLogInfo($restore_log_id);
		
		return $result;
	}
	
}
