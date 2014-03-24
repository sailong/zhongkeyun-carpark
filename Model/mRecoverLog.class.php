<?php
class mRecoverLog extends mBase {
	const LOG_PAGE_SIZE = 24;
	
	public function getRecoverLogById($recover_log_ids) {
		if (empty($recover_log_ids))
			return false;
		$dRecoverLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$result = $dRecoverLog->getRecoverLogById($recover_log_ids);
		
		return $this->mixOtherRecoverLog($result);
	}
	
	public function  mixOtherRecoverLog($log_list) {
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
	public function getRecoverLogByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::LOG_PAGE_SIZE;
	
		$dRecoverLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$log_list = $dRecoverLog->getRecoverLogByCond($condition_str, $offset, $length);
	
		return $this->mixOtherRecoverLog($log_list);
	}
	
	//$condition_str同getRecoverLogByCond的$condition_str
	public function getRecoverLogCount($condition_str = '') {
	
		$dRecoverLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$count = $dRecoverLog->getRecoverLogCount($condition_str);
	
		return $count;
	}
	
	public function modifyRecoverLogInfo($recover_log_info, $recover_log_id) {
		if (!is_numeric($recover_log_id) || empty($recover_log_info))
			return false;
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$recover_log_info['admin_id'] = $admin_info['admin_id'];
		$recover_log_info['user_name'] = $admin_info['admin_name'];
		
		$dRecoverLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$result = $dRecoverLog->modifyRecoverLogInfo($recover_log_info, $recover_log_id);
		
		return $result;
	}
	
	public function addRecoverLog($recover_log_info) {
		if (empty($recover_log_info))
			return false;
		
		//修改卡片的余额
		$mCard = ClsFactory::Create('Model.mCard');
		$card_info = array();
		$card_info['status'] = 2;
		$info = array_pop($mCard->getCardById($recover_log_info['card_id']));
		$card_info['code'] = $info['code'].'-'.$info['card_id'];
		$card_info['money'] = 0;
		$card_info['expire_time'] = 0;
		$mCard->modifyCardInfo($card_info, $recover_log_info['card_id']);
		
		//取出原来的余额
		$recover_log_info['money'] = $info['money'];
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getCurrentUser();
		$recover_log_info['admin_id'] = $admin_info['admin_id'];
		$recover_log_info['user_name'] = $admin_info['admin_name'];
		$recover_log_info['add_time'] = time();
		
		$dRecoverLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$result = $dRecoverLog->addRecoverLog($recover_log_info);
		
		return $result;
	}
	
	public function delRecoverLogInfo($recover_log_id) {
		if (!is_numeric($recover_log_id))
			return false;
		$dRecoverLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$result = $dRecoverLog->delRecoverLogInfo($recover_log_id);
		
		return $result;
	}
	
	/**
	 * 收费统计
	 * @param unknown_type $condition_str
	 * @return unknown
	 */
	public function getReturnSum($condition_str = '') {
	
		$dIssueLog = ClsFactory::Create ( 'Data.dRecoverLog' );
		$sum = $dIssueLog->getReturnSum($condition_str);
		return $sum ? $sum : '0';
	}
}
