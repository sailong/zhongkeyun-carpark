<?php
class mLossreportLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getLossreportLogById($lossreport_log_ids) {
		if (empty($lossreport_log_ids))
			return false;
		$dLossreportLog = ClsFactory::Create ( 'Data.dLossreportLog' );
		$result = $dLossreportLog->getLossreportLogById($lossreport_log_ids);
		
		return $this->mixOtherLossreportLog($result);
	}
	
	public function  mixOtherLossreportLog($log_list) {
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
	public function getLossreportLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dLossreportLog = ClsFactory::Create ( 'Data.dLossreportLog' );
		$log_list = $dLossreportLog->getLossreportLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherLossreportLog($log_list);
	}
	
	//$condition_str同getLossreportLogByCond的$condition_str
	public function getLossreportLogCount($condition_str = '') {
	
		$dLossreportLog = ClsFactory::Create ( 'Data.dLossreportLog' );
		$count = $dLossreportLog->getLossreportLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyLossreportLogInfo($lossreport_log_info, $lossreport_log_id) {
		if (!is_numeric($lossreport_log_id) || empty($lossreport_log_info))
			return false;
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$lossreport_log_info['admin_id'] = $admin_info['admin_id'];
		$lossreport_log_info['user_name'] = $admin_info['admin_name'];
		
		$dLossreportLog = ClsFactory::Create ( 'Data.dLossreportLog' );
		$result = $dLossreportLog->modifyLossreportLogInfo($lossreport_log_info, $lossreport_log_id);
		
		return $result;
	}
	
	public function addLossreportLog($lossreport_log_info) {
		if (empty($lossreport_log_info))
			return false;
		
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$card_info['status'] = 1;
		$mCard->modifyCardInfo($card_info, $lossreport_log_info['card_id']);
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$lossreport_log_info['admin_id'] = $admin_info['admin_id'];
		$lossreport_log_info['user_name'] = $admin_info['admin_name'];
		
		$dLossreportLog = ClsFactory::Create ( 'Data.dLossreportLog' );
		$result = $dLossreportLog->addLossreportLog($lossreport_log_info);
		
		return $result;
	}
	
	public function delLossreportLogInfo($lossreport_log_id) {
		if (!is_numeric($lossreport_log_id))
			return false;
		$dLossreportLog = ClsFactory::Create ( 'Data.dLossreportLog' );
		$result = $dLossreportLog->delLossreportLogInfo($lossreport_log_id);
		
		return $result;
	}
	
}
