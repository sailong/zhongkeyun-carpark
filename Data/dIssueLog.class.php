<?php
class dIssueLog extends dBase {
	// 表名
	protected $name = 'issue_log';
	protected $pk = '';
	protected $_fields = array (
			'issue_log' => array (
					'issue_id',
					'card_id',
					'admin_id',
					'user_name',
					'money',
					'charge',
					'add_time',
					'remark',
			)
	);
	public function _initialize() {
	}
	
	public function addIssueLog($issue_log_info) {
		if (empty ($issue_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $issue_log_info, 'issue_log' );
		$sql = "insert into issue_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyIssueLogInfo($issue_log_info, $issue_log_id) {
		if (empty ( $issue_log_info ) || ! $issue_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $issue_log_info, 'issue_log' );
		$result = $this->execute ("update issue_log set $setsql where issue_id=$issue_log_id");
		return $result;
	}
	
	public function getIssueLogById($issue_log_ids) {
		$issue_log_ids = $this->checkIds($issue_log_ids);
		if (!$issue_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from issue_log where issue_id in (".implode ( ',', $issue_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $issue_log) {
			$result[$issue_log['issue_id']] = $issue_log;
		}
	
		return $result;
	}
	
	public function getIssueLogByCond($condition_str = '', $offset = 0, $length = 24) {
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
	
		$list = $this->query ("select * from issue_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getIssueLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from issue_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delIssueLogInfo($issue_log_id) {
		if (!is_numeric($issue_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from issue_log where issue_id=$issue_log_id");
		
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
	private function implodeFields($dataarr, $table = 'issue_log') {
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
	/**
	 * 获取收费统计
	 * @param unknown_type $admin_id
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 */
	public function getChargeSum($where_str)
	{
		//if(!$admin_id) return;
		$list = $this->query ("select sum(charge) as sum_all from " .$this->name.' where '.$where_str);
		$result = array_pop($list);
		return $result['sum_all'];
	}
}
