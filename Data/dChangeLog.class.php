<?php
class dChangeLog extends dBase {
	// 表名
	protected $name = 'change_log';
	protected $pk = '';
	protected $_fields = array (
			'change_log' => array (
					'change_id',
					'card_id',
					'admin_id',
					'user_name',
					'new_code',
					'old_code',
					'charge',
					'add_time',
					'remark',
			)
	);
	public function _initialize() {
	}
	
	public function addChangeLog($change_log_info) {
		if (empty ($change_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $change_log_info, 'change_log' );
		$sql = "insert into change_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyChangeLogInfo($change_log_info, $change_log_id) {
		if (empty ( $change_log_info ) || ! $change_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $change_log_info, 'change_log' );
		$result = $this->execute ("update change_log set $setsql where change_id=$change_log_id");
		return $result;
	}
	
	public function getChangeLogById($change_log_ids) {
		$change_log_ids = $this->checkIds($change_log_ids);
		if (!$change_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from change_log where change_id in (".implode ( ',', $change_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $change_log) {
			$result[$change_log['change_id']] = $change_log;
		}
	
		return $result;
	}
	
	public function getChangeLogByCond($condition_str = '', $offset = 0, $length = 24) {
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
	
		$list = $this->query ("select * from change_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getChangeLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from change_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delChangeLogInfo($change_log_id) {
		if (!is_numeric($change_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from change_log where change_id=$change_log_id");
		
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
	private function implodeFields($dataarr, $table = 'change_log') {
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
	 */
	public function getChargeSum($where_str){
	
		$list = $this->query ("select sum(charge) as sum_all from " .$this->name.' where '.$where_str);
		$result = array_pop($list);
		return $result['sum_all'];
	}
	
}
