<?php
class dCarCategory extends dBase {
	// 表名
	protected $name = 'car_category';
	protected $pk = '';
	protected $_fields = array (
			'car_category' => array (
					'cate_id',
					'cate_name',
			)
	);
	public function _initialize() {
	}
	
	public function addCarCategory($car_category) {
		if (empty ($car_category)) {
			return false;
		}
		$setsql = $this->implodeFields ( $car_category, 'car_category' );
		$sql = "insert into car_category set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyCarCategory($car_category, $cate_id) {
		if (empty ( $car_category ) || ! $cate_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $car_category, 'car_category' );
		$result = $this->execute ("update car_category set $setsql where cate_id=$cate_id");
		return $result;
	}
	
	public function getCarCategoryById($cate_ids) {
		$cate_ids = $this->checkIds($cate_ids);
		if (!$cate_ids) {
			return false;
		}
		
		$list = $this->query ("select * from car_category where cate_id in (".implode ( ',', $cate_ids ).")");
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $cate) {
			$result[$cate['cate_id']] = $cate;
		}
		
		return $result;
	}
	
	public function getCarCategory() {
		$list = $this->query ( "select * from car_category" );
		if (! $list)
			return false;
	
		return $list;
	}
	
	public function delCarCategory($cate_id) {
		if (!is_numeric($cate_id)) {
			return false;
		}
	
		$result = $this->query ("delete from car_category where cate_id=$cate_id");
	
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
	private function implodeFields($dataarr, $table = 'car_category') {
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
