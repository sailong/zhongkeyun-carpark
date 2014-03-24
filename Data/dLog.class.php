<?php
class dLog extends dBase {
	// 表名
	protected $name = 'log';
	protected $pk = 'id';
	protected $_fields = array (
			'log' => array (
					"id",
					"function_name",
					"param",
					"return",
					"create_time",
			),
	);
	public function _initialize() {
	}
	
	public function addLog($user_info) {
		if (empty ( $user_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $user_info, 'log' );
		$sql = "insert into ".$this->name." set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	private function implodeFields($dataarr, $table = 'log') {
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