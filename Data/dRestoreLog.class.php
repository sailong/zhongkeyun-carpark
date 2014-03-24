<?php
class dRestoreLog extends dBase {
	// 表名
	protected $name = 'restore_log';
	protected $pk = '';
	protected $_fields = array (
			'restore_log' => array (
					'restore_id',
					'card_id',
					'admin_id',
					'user_name',
					'add_time',
					'remark',
			)
	);
	public function _initialize() {
	}
	
	public function addRestoreLog($restore_log_info) {
		if (empty ($restore_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $restore_log_info, 'restore_log' );
		$sql = "insert into restore_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyRestoreLogInfo($restore_log_info, $restore_log_id) {
		if (empty ( $restore_log_info ) || ! $restore_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $restore_log_info, 'restore_log' );
		$result = $this->execute ("update restore_log set $setsql where restore_id=$restore_log_id");
		return $result;
	}
	
	public function getRestoreLogById($restore_log_ids) {
		$restore_log_ids = $this->checkIds($restore_log_ids);
		if (!$restore_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from restore_log where restore_id in (".implode ( ',', $restore_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $restore_log) {
			$result[$restore_log['restore_id']] = $restore_log;
		}
	
		return $result;
	}
	
	public function getRestoreLogByCond($condition_str = '', $offset = 0, $length = 24) {
		if ($length) {
			$limit_sql = " limit $offset, $length";
		} else {
			$limit_sql = '';
		}
		$keys = array('add_time', 'user_name');
		
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select * from restore_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getRestoreLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from restore_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delRestoreLogInfo($restore_log_id) {
		if (!is_numeric($restore_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from restore_log where restore_id=$restore_log_id");
		
		return $result;
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'restore_log') {
		if (empty ( $dataarr ) || empty ( $table )) {
			return false;
		}
		$dataarr = is_array ( $dataarr ) ? $dataarr : array (
				$dataarr 
		);
		$dataarr = $this->checkFields ( $dataarr, $this->_fields [$table] );
		$arr = array ();
		foreach ( $dataarr as $key => $value ) {
			$arr [] = "`$key`='$value'";
		}
		if (! empty ( $arr )) {
			$str = implode ( ',', $arr );
		}
		return $str ? $str : false;
	}
}
