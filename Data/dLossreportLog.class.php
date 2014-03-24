<?php
class dLossreportLog extends dBase {
	// 表名
	protected $name = 'lossreport_log';
	protected $pk = '';
	protected $_fields = array (
			'lossreport_log' => array (
					'lossreport_id',
					'card_id',
					'admin_id',
					'user_name',
					'add_time',
					'remark',
			)
	);
	public function _initialize() {
	}
	
	public function addLossreportLog($lossreport_log_info) {
		if (empty ($lossreport_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $lossreport_log_info, 'lossreport_log' );
		$sql = "insert into lossreport_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyLossreportLogInfo($lossreport_log_info, $lossreport_log_id) {
		if (empty ( $lossreport_log_info ) || ! $lossreport_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $lossreport_log_info, 'lossreport_log' );
		$result = $this->execute ("update lossreport_log set $setsql where lossreport_id=$lossreport_log_id");
		return $result;
	}
	
	public function getLossreportLogById($lossreport_log_ids) {
		$lossreport_log_ids = $this->checkIds($lossreport_log_ids);
		if (!$lossreport_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from lossreport_log where lossreport_id in (".implode ( ',', $lossreport_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $lossreport_log) {
			$result[$lossreport_log['lossreport_id']] = $lossreport_log;
		}
	
		return $result;
	}
	
	public function getLossreportLogByCond($condition_str = '', $offset = 0, $length = 24) {
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
	
		$list = $this->query ("select * from lossreport_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getLossreportLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from lossreport_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delLossreportLogInfo($lossreport_log_id) {
		if (!is_numeric($lossreport_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from lossreport_log where lossreport_id=$lossreport_log_id");
		
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
	private function implodeFields($dataarr, $table = 'lossreport_log') {
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
