<?php
class dMessageLog extends dBase {
	// 表名
	protected $name = 'message_log';
	protected $pk = '';
	protected $_fields = array (
			'message_log' => array (
					'id',
					'card_code',
					'mobile',
					'action',
					'content',
					'send_at',
					'send_status',
					'return_str',
					'extra'
			)
	);
	public function _initialize() {
	}
	
	public function add($data) {
		if (empty ($data)) {
			return false;
		}
		$setsql = $this->implodeFields ( $data, 'message_log' );
		$sql = "insert into ".$this->name." set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function getLogByCond($cond) {
		if($cond) $cond=' where '.$cond;
		$list = $this->query ( "select * from ".$this->name." ".$cond );
		if (! $list) return false;
		return $list;
	}
	/**
	 * 将对应的字段进行组合
	 *
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'admin') {
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
