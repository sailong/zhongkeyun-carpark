<?php
class dActivity extends dBase {
	protected $name = 'club_activity';
	protected $pk = 'activity_id';
	protected $_fields = array (
			'club_activity' => array (
					"activity_id",
					"club_id",
					"activity_name",
					"activity_content",
					"city_no",
					"activity_address",
					"activity_start_time",
					"activity_end_time",
					"activity_club",
					"activity_sex",
					"activity_min_age",
					"activity_max_age",
					"activity_eb",
					"activity_create_time",
					"activity_boy_num",
					"activity_girl_num",
					"activity_stop_time",
					"activity_status",
					"activity_surplus_boy",
					"activity_surplus_girl" 
			) 
	);
	public function _initialize() {
		$this->connectDb ( 'club', true );
	}
	public function setClubActivityMemeberCount($activity_id, $field_name, $offset = 1) {
		if (empty ( $field_name )) {
			return false;
		}
		$sql = "update club_activity set $field_name=$field_name+$offset where activity_id=$activity_id";
		$result = $this->execute ( $sql );
		return $result;
	}
	public function addClubActivity($activity_info) {
		if (empty ( $activity_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $activity_info, 'club_activity' );
		$result = $this->execute ( "insert into club_activity set $setsql" );
		return $result ? $this->getLastInsID () : false;
	}
	public function modifyClubActivity($activity_info, $activity_id) {
		if (empty ( $activity_info ) || ! $activity_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $activity_info, 'club_activity' );
		$result = $this->execute ( "update club_activity set $setsql where club_id=$uid" );
		return $result;
	}
	public function getActivityList($offset = 0, $length = 24) {
		$limit_sql = " limit $offset, $length";
		
		$list = $this->query ( "select * from club_activity where activity_status = 1 order by is_expired asc, activity_create_time desc" . $limit_sql );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['activity_id']] = $v;
		}
		return $result;
	}
	public function getActivityCount() {
		$list = $this->query ( "select count(*) as num from club_activity " );
		$result = array_pop ( $list );
		return $result ['num'];
	}
	public function getActivityById($activity_ids) {
		$activity_ids = $this->checkIds ( $activity_ids );
		if (! $activity_ids) {
			return false;
		}
		$where_sql = " where activity_id in (" . implode ( ',', $activity_ids ) . ")  and activity_status = 1";
		
		$list = $this->query ( "select * from club_activity $where_sql" );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['activity_id']] = $v;
		}
		return $result;
	}
	public function getClubActivityList($club_id, $offset = 0, $length = 24) {
		$limit_sql = " limit $offset, $length";
		
		$list = $this->query ( "select * from club_activity where club_id=$club_id and activity_status = 1 order by is_expired asc, activity_create_time desc" . $limit_sql );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['activity_id']] = $v;
		}
		return $result;
	}
	public function checkClubActiving($club_ids) {
		$club_ids = $this->checkIds ( $club_ids );
		$list = $this->query ( "select * from club_activity where club_id in (" . implode ( ',', $club_ids ) . ") and activity_status = 1 and activity_end_time >" . time () );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [] = $v ['club_id'];
		}
		return array_unique ( $result );
	}
	public function getClubActivityCount($club_id) {
		$list = $this->query ( "select count(*) as num from club_activity where club_id=$club_id and activity_status = 1" );
		$result = array_pop ( $list );
		return $result ['num'];
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'club_activity') {
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
