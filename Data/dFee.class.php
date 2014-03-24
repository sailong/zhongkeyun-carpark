<?php
class dFee extends dBase {
	// 表名
	protected $name = 'fee';
	protected $pk = '';
	protected $_fields = array (
			'fee' => array (
					'fee_id',
					'cate_id',
					'free_time',
					'start_time',
					'start_money',
					'step_time',
					'step_money',
					'max_money',
			)
	);
	public function _initialize() {
	}
	
	public function addFee($fee_info) {
		if (empty ($fee_info)) {
			return false;
		}
		$setsql = $this->implodeFields ( $fee_info, 'fee' );
		$sql = "insert into fee set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyFeeInfo($fee_info, $fee_id) {
		if (empty ( $fee_info ) || ! $fee_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $fee_info, 'fee' );
		$result = $this->execute ("update fee set $setsql where fee_id=$fee_id");
		return $result;
	}
	
	public function getFeeById($fee_ids) {
		$fee_ids = $this->checkIds($fee_ids);
		if (!$fee_ids) {
			return false;
		}
		
		$list = $this->query ( "select * from fee where fee_id in (".implode ( ',', $fee_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $fee) {
			$result[$fee['fee_id']] = $fee;
		}
		
		return $result;
	}
	
	public function getFeeInfo() {
		$list = $this->query ( "select * from fee" );
		if (! $list)
			return false;
	
		return $list;
	}
	
	public function delFeeInfo($fee_id) {
		if (!is_numeric($fee_id)) {
			return false;
		}
		
		$result = $this->query ("delete from fee where fee_id=$fee_id");
		
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
	private function implodeFields($dataarr, $table = 'fee') {
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
