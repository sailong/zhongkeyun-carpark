<?php
class dRechargeLog extends dBase {
	// 表名
	protected $name = 'recharge_log';
	protected $pk = '';
	protected $_fields = array (
			'recharge_log' => array (
					'recharge_id',
					'card_id',
					'admin_id',
					'user_name',
					'charge',
					'add_time',
					'remark',
			)
	);
	public function _initialize() {
	}
	
	public function addRechargeLog($recharge_log_info) {
		if (empty ($recharge_log_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $recharge_log_info, 'recharge_log' );
		$sql = "insert into recharge_log set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function batchAddRechargeLog($recharge_log_infos) {
		if (empty ($recharge_log_infos)) {
			return false;
		}
		$field_val = $this->lotAdd($recharge_log_infos, 'recharge_log');
		$sql = "insert into recharge_log (".implode(',', $field_val['fields']).") values ".implode(',', $field_val['fields_values']);
		$result = $this->execute ( $sql );
		return $result ? true : false;
	}
	
	public function modifyRechargeLogInfo($recharge_log_info, $recharge_log_id) {
		if (empty ( $recharge_log_info ) || ! $recharge_log_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $recharge_log_info, 'recharge_log' );
		$result = $this->execute ("update recharge_log set $setsql where recharge_id=$recharge_log_id");
		return $result;
	}
	
	public function getRechargeLogById($recharge_log_ids) {
		$recharge_log_ids = $this->checkIds($recharge_log_ids);
		if (!$recharge_log_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from recharge_log where recharge_id in (".implode ( ',', $recharge_log_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $recharge_log) {
			$result[$recharge_log['recharge_id']] = $recharge_log;
		}
	
		return $result;
	}
	
	public function getRechargeLogByCond($condition_str = '', $offset = 0, $length = 24) {
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
	
		$list = $this->query ("select * from recharge_log" . $where_str . $limit_sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getRechargeLogCount($condition_str = '') {
	
		if ($condition_str) {
			$where_str = ' where '.$condition_str;
		} else {
			$where_str = ' order by add_time desc';
		}
	
		$list = $this->query ("select count(*) as num from recharge_log" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delRechargeLogInfo($recharge_log_id) {
		if (!is_numeric($recharge_log_id)) {
			return false;
		}
		
		$result = $this->query ("delete from recharge_log where recharge_id=$recharge_log_id");
		
		return $result;
	}
	
	/**
	 * 同一张表批量增加对应的记录
	 * @param $dataarr
	 *
	 */
	public function lotAdd($dataarr, $table){
		if(empty($dataarr) || !is_array($dataarr)) {
			return false;
		}
		$field_vals = array();
		//获取fields数据
		$fields = array();
		$new_arr = array();
		foreach($dataarr as $key=>$data) {
			//检查并得到正确的数据
			$data = $this->checkFields($data,$table);
			$new_arr[] = $data;
			$fields = array_merge($fields, array_keys($data));
		}
	
		//得到正确的字段名
		$fields = array_unique($fields);
		if(empty($fields)) return false;
		//排序
		sort($fields);
	
		//insert 的values
		$fields_values = array();
		foreach($new_arr as &$user) {
			if(empty($user) || !is_array($user)) {
				continue;
			}
			$keys = array_keys($user);
			$diff = array_diff($fields, $keys);
			if(!empty($diff)) {
				//没有数据的字段默认为空
				$arr = array_combine($diff, array_fill(0, count($diff), null));
				$user = array_merge($user, $arr);
			}
			//字段排序
			ksort($user);
			$vals = array();
			foreach($user as $val) {
				if(is_null($val)) {
					$vals[] = 'DEFAULT';
				} else {
					$vals[] = "'".$val."'";
				}
			}
			$fields_values[] = "(".implode(',', $vals).")";
		}
		$field_vals['fields'] = $fields;
		$field_vals['fields_values'] = $fields_values;
		return $field_vals['fields_values'] ? $field_vals : false;
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'recharge_log') {
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
