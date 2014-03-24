<?php
class dConsumeLog extends dBase {
	// 表名
	protected $name = 'consume_log';
	protected $pk = '';
	protected $_fields = array (
			'consume_log' => array (
					'consume_id',
					'card_id',
					'session_id',
					'admin_id',
					'user_name',
					'charge',
					'add_time',
					'remark'
			)
	);
	public function _initialize() {
	}
	
	public function addConsumeLog($consume_log_info) {
		if (empty ($consume_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $consume_log_info, 'consume_log' );
		$sql = "insert into consume_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyConsumeLogInfo($consume_log_info, $consume_log_id) {
		if (empty ( $consume_log_info ) || ! $consume_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $consume_log_info, 'consume_log' );
		$result = $this->execute ("update consume_log set $setsql where consume_id=$consume_log_id");
		return $result;
	}
	
	public function getConsumeLogById($consume_log_ids) {
		$consume_log_ids = $this->checkIds($consume_log_ids);
		if (!$consume_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from consume_log where consume_id in (".implode ( ',', $consume_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $consume_log) {
			$result[$consume_log['consume_id']] = $consume_log;
		}
	
		return $result;
	}
	
	public function getConsumeLogByCond($condition_str = '', $offset = 0, $length = 24) {
		if ($length) {
			$limit_sql = " limit $offset, $length";
		} else {
			$limit_sql = '';
		}
		$keys = array('add_time', 'user_name');
		
		if ($condition_str) {
			$where_str = ' where '.$condition_str.' order by add_time desc';
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select * from consume_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getConsumeLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from consume_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delConsumeLogInfo($consume_log_id) {
		if (!is_numeric($consume_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from consume_log where consume_id=$consume_log_id");
		
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
	private function implodeFields($dataarr, $table = 'consume_log') {
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
	 * 获取储值卡扣款统计
	 */
	public function getConsumelSum($admin_id,$s_time,$e_time,$door){
	
		//-------------------------------------------------------------------------------------------------------------
		$baseCondition = ' end_time between %s and %s  and end_door_id is not null and end_door_id in ('.$door.')';
		$baseCondition = sprintf($baseCondition,$s_time,$e_time);
		//-------------------------------------------------------------------------------------------------------------
		$condition = ' a.add_time between %s and %s and a.card_id = b.card_id and b.card_type = 2';
		$condition = sprintf($condition,$s_time,$e_time);
		if($admin_id) $condition.=' and a.admin_id = '.$admin_id;
		$condition.=' and a.session_id in (select session_id from `session` where '.$baseCondition.')';
		$sql = "select sum(a.charge) as sum_all from " .$this->name.' as a,card as b where '.$condition;
		$list = $this->query ($sql);
		$result = array_pop($list);//echo $sql.'<br>';
		return $result['sum_all'];
	}
}
