<?php
class dPark extends dBase {
	// 表名
	protected $name = 'park';
	protected $pk = '';
	protected $_fields = array (
			'park' => array (
					'park_id',
					'park_name',
					'park_remark'
			)
	);
	public function _initialize() {
	}
	
	public function addPark($park_info) {
		if (empty ($park_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $park_info, 'park' );
		$sql = "insert into park set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyParkInfo($park_info, $park_id) {
		if (empty ( $park_info ) || ! $park_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $park_info, 'park' );
		$result = $this->execute ("update park set $setsql where park_id=$park_id");
		return $result;
	}
	
	public function getParkById($park_ids) {
		$park_ids = $this->checkIds($park_ids);
		if (!$park_ids) {
			return false;
		}
		
		$list = $this->query ( "select * from park where park_id in (".implode ( ',', $park_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $park) {
			$result[$park['park_id']] = $park;
		}
		
		return $result;
	}
	
	public function getParkInfo() {
		$list = $this->query ( "select * from park" );
		if (! $list)
			return false;
	
		return $list;
	}
	
	public function delParkInfo($park_id) {
		if (!is_numeric($park_id)) {
			return false;
		}
		
		$result = $this->query ("delete from park where park_id=$park_id");
		
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
	private function implodeFields($dataarr, $table = 'park') {
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
