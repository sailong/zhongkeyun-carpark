<?php
class dSession extends dBase {
	// 表名
	protected $name = 'session';
	protected $pk = '';
	protected $_fields = array (
			'session' => array (
					'session_id',
					'card_id',
					'start_door_id',
					'start_status',
					'start_time',
					'end_door_id',
					'end_status',
					'end_time',
					'charge',
					'real_money',
					'new_cate_id',//阶梯收费类型id,0时无阶梯收费,-1是期卡停车
					'park_id',//停车车库
			)
	);
	public function _initialize() {
	}
	
	public function addSession($session_info) {
		if (empty ($session_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $session_info, 'session' );
		$sql = "insert into session set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifySessionInfo($session_info, $session_id) {
		if (empty ( $session_info ) || ! $session_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $session_info, 'session' );
		$result = $this->execute ("update session set $setsql where session_id=$session_id");
		return $result;
	}
	
	public function getSessionById($session_ids) {
		$session_ids = $this->checkIds($session_ids);
		if (!$session_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from session where session_id in (".implode ( ',', $session_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $session) {
			$result[$session['session_id']] = $session;
		}
	
		return $result;
	}
	
	public function getSessionByCond($condition_str = '', $offset = 0, $length = 24) {
		if ($length) {
			$limit_sql = " limit $offset, $length";
		} else {
			$limit_sql = '';
		}
		
		$matches = array();
		if (preg_match('/(.*?(\(.+\)).*?),(.*)/i', $condition_str, $matches)) {
			$cond_str = $matches[1];
			$order = $matches[3];
		} else {
			$pos = strpos($condition_str, ',');
			if ($pos !== false) {
				$cond_str = substr($condition_str, 0, $pos);
				$order = substr($condition_str, $pos + 1);
			} else {
				$cond_str = $condition_str;
				$order = '';
			}
		}
		
		if ($cond_str) {
			$where_str = ' where '.$cond_str;
		} else {
			$where_str = '';
		}
		if ($order) {
			$order_str = ' order by '.$order;
		} else {
			$order_str = '';
		}
	
		$sql = "select * from session" . $where_str . $order_str . $limit_sql;
		$list = $this->query ($sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getSessionCount($condition_str = '') {
	
		$matches = array();
		if (preg_match('/(.*?(\(.+\)).*?),(.*)/i', $condition_str, $matches)) {
			$cond_str = $matches[1];
			$order = $matches[3];
		} else {
			$pos = strpos($condition_str, ',');
			if ($pos !== false) {
				$cond_str = substr($condition_str, 0, $pos);
				$order = substr($condition_str, $pos + 1);
			} else {
				$cond_str = $condition_str;
				$order = '';
			}
		}
		
		if ($cond_str) {
			$where_str = ' where '.$cond_str;
		} else {
			$where_str = '';
		}
		if ($order) {
			$order_str = ' order by '.$order;
		} else {
			$order_str = '';
		}
	
		$list = $this->query ("select count(*) as num from session" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delSessionInfo($session_id) {
		if (!is_numeric($session_id)) {
			return false;
		}
		
		$result = $this->query ("delete from session where session_id=$session_id");
		
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
	private function implodeFields($dataarr, $table = 'session') {
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
	public function getChargeSum($field,$where_str){
	
		$sql = "select sum(".$field.") as sum_all from " .$this->name.' where '.$where_str;
		$list = $this->query ($sql);
		$result = array_pop($list);//echo $sql.'<br>';
		return $result['sum_all'];
	}
}
