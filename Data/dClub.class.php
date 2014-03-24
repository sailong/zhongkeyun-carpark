<?php
class dClub extends dBase {
	// 表名
	protected $name = 'club_info';
	protected $pk = 'club_id';
	const CLUB_INFO_PAGE_SIZE = 24;
	protected $_fields = array (
			'club_info' => array (
					"club_id",
					"club_name",
					"club_summary",
					"club_logo",
					"club_equal_age",
					"club_girl_num",
					"club_boy_num",
					"club_create_time" 
			),
			'club_apply' => array (
					"club_apply_id",
					"club_name",
					"club_summary",
					"club_logo",
					"uid",
					"club_apply_time" 
			) 
	);
	public function _initialize() {
		$this->connectDb ( 'club', true );
	}
	public function setClubMemeberCount($club_id, $field_name, $offset = 1) {
		if (empty ( $field_name )) {
			return false;
		}
		$sql = "update club_info set $field_name=$field_name+$offset where club_id=$club_id";
		$result = $this->execute ( $sql );
		return $result;
	}
	public function addClubInfo($club_info) {
		if (empty ( $club_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $club_info, 'club_info' );
		$result = $this->execute ( "insert into club_info set $setsql" );
		return $result ? $this->getLastInsID () : false;
	}
	public function modifyClubInfo($club_info, $club_id) {
		if (empty ( $club_info ) || ! $club_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $club_info, 'club_info' );
		$result = $this->execute ( "update club_info set $setsql where club_id=$club_id" );
		return $result;
	}
	public function getClubInfo($offset = 0, $length = 24) {
		$offset = intval ( $offset );
		$length = intval ( $length );
		if (! $length)
			$length = self::CLUB_INFO_PAGE_SIZE;
		
		$uids = is_array ( $uids ) ? $uids : array (
				$uids 
		);
		$limit_sql = " limit $offset, $length";
		
		$list = $this->query ( "select * from club_info $limit_sql" );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['club_id']] = $v;
		}
		return $result;
	}
	public function getClubInfoCount() {
		$list = $this->query ( "select count(*) as num from club_info " );
		$result = array_pop ( $list );
		return $result ['num'];
	}
	public function getClubInfoById($club_ids) {
		$club_ids = $this->checkIds ( $club_ids );
		if (! $club_ids) {
			return false;
		}
		$where_sql = " where club_id in (" . implode ( ',', $club_ids ) . ")";
		
		$list = $this->query ( "select * from club_info $where_sql" );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['club_id']] = $v;
		}
		return $result;
	}
	
	// club_apply
	public function addClubApply($club_info) {
		if (empty ( $club_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $club_info, 'club_apply' );
		$result = $this->execute ( "insert into club_apply set $setsql" );
		return $result ? $this->getLastInsID () : false;
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'club_info') {
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
