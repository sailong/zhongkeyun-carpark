<?php
class dCard extends dBase {
	// 表名
	protected $name = 'card';
	protected $pk = '';
	protected $_fields = array (
			'card' => array (
					'card_id',
					'code',
					'card_type',
					'expire_time',
					'money',
					'car_code',
					'cate_id',
					'car_type',
					'car_color',
					'name',
					'person_id',
					'tel',
					'address',
					'add_time',
					'parking',
					'park',
					'status',
					'is_master',
					'note',
			)
	);
	public function _initialize() {
	}
	
	public function addCard($card_info) {
		if (empty ($card_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $card_info, 'card' );
		$sql = "insert into card set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyCardInfo($card_info, $card_id) {
		if (empty ( $card_info ) || ! $card_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $card_info, 'card' );
		$result = $this->execute ("update card set $setsql where card_id=$card_id");
		return $result;
	}
	
	public function getCardByCode($codes) {
		$codes = $this->checkIds($codes);
		if (!$codes) {
			return false;
		}
		
		$list = $this->query ( "select * from card where code in (".implode ( ',', $codes ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $card) {
			$result[$card['code']] = $card;
		}
		
		return $result;
	}
	
	public function getCardById($card_ids) {
		$card_ids = $this->checkIds($card_ids);
		if (!$card_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from card where card_id in (".implode ( ',', $card_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $card) {
			$result[$card['card_id']] = $card;
		}
	
		return $result;
	}
	
	public function getCardListByCond($condition_str = '', $offset = 0, $length = 24) {
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
		
		$sql = "select * from card" . $where_str . $order_str . $limit_sql;
		
		$list = $this->query($sql);
		if (!$list)
			return false;
		return $list;
	}
	
	public function getCardListCount($condition_str = '') {
	
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
	
		$list = $this->query ("select count(*) as num from card" . $where_str);
		$result = array_pop($list);
		return $result['num'];
	}
	
	public function delCardInfo($card_id) {
		if (!is_numeric($card_id)) {
			return false;
		}
		
		$result = $this->query ("delete from card where card_id=$card_id");
		
		return $result;
	}
	
	public function modifyCard($card_info, $condition) {
		if (empty ( $card_info ) || ! $condition) {
			return false;
		}
		$setsql = $this->implodeFields ( $card_info, 'card' );
		$result = $this->execute ("update card set $setsql where ".$condition);
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
	private function implodeFields($dataarr, $table = 'card') {
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
