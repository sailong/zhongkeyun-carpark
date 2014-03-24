<?php
class dRecoverLog extends dBase {
	// 表名
	protected $name = 'recover_log';
	protected $pk = '';
	protected $_fields = array (
			'recover_log' => array (
					'recover_id',
					'card_id',
					'admin_id',
					'user_name',
					'code',
					'money',
					'return_money',
					'add_time',
					'remark'
			)
	);
	public function _initialize() {
	}
	
	public function addRecoverLog($recover_log_info) {
		if (empty ($recover_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $recover_log_info, 'recover_log' );
		$sql = "insert into recover_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyRecoverLogInfo($recover_log_info, $recover_log_id) {
		if (empty ( $recover_log_info ) || ! $recover_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $recover_log_info, 'recover_log' );
		$result = $this->execute ("update recover_log set $setsql where recover_id=$recover_log_id");
		return $result;
	}
	
	public function getRecoverLogById($recover_log_ids) {
		$recover_log_ids = $this->checkIds($recover_log_ids);
		if (!$recover_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from recover_log where recover_id in (".implode ( ',', $recover_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $recover_log) {
			$result[$recover_log['recover_id']] = $recover_log;
		}
	
		return $result;
	}
	
	public function getRecoverLogByCond($condition_str = '', $offset = 0, $length = 24) {
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
	
		$list = $this->query ("select * from recover_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getRecoverLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from recover_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delRecoverLogInfo($recover_log_id) {
		if (!is_numeric($recover_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from recover_log where recover_id=$recover_log_id");
		
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
	private function implodeFields($dataarr, $table = 'recover_log') {
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
	 * 获取退款统计
	 */
	public function getReturnSum($where_str){
	
		$list = $this->query ("select sum(return_money) as sum_all from " .$this->name.' where '.$where_str);
		$result = array_pop($list);
		return $result['sum_all'];
	}
}
