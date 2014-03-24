<?php
class dDoor extends dBase {
	// 表名
	protected $name = 'door';
	protected $pk = '';
	protected $_fields = array (
			'door' => array (
					'door_id',
					'door_name',
					'door_addr',
					'door_ip',
					'door_no',
					'mac',
					'brake_no',
					'reader_no',
					'door_type',
					'led_ip',
					'lane',
					'park_id',
					'show_left_parking'
			)
	);
	public function _initialize() {
	}
	
	public function addDoor($door_info) {
		if (empty ($door_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $door_info, 'door' );
		$sql = "insert into door set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyDoorInfo($door_info, $door_id) {
		if (empty ( $door_info ) || ! $door_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $door_info, 'door' );
		$result = $this->execute ("update door set $setsql where door_id=$door_id");
		return $result;
	}
	
	public function getDoorById($door_ids) {
		$door_ids = $this->checkIds($door_ids);
		if (!$door_ids) {
			return false;
		}
		
		$list = $this->query ( "select * from door where door_id in (".implode ( ',', $door_ids ).")  order by door_name asc,door_type desc" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $door) {
			$result[$door['door_id']] = $door;
		}
		
		return $result;
	}
	
	public function getDoorInfo() {
		$list = $this->query ( "select * from door order by door_name asc" );
		if (! $list)
			return false;
	
		return $list;
	}
	
	public function delDoorInfo($door_id) {
		if (!is_numeric($door_id)) {
			return false;
		}
		
		$result = $this->query ("delete from door where door_id=$door_id");
		
		return $result;
	}
	
	
	public function getDoorInfoByCond($cond) {
		
		if($cond) $cond=' where '.$cond;
		$list = $this->query ( "select * from door ".$cond );
		if (! $list)
			return false;
	
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
	private function implodeFields($dataarr, $table = 'door') {
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
