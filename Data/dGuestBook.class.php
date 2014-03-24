<?php
class dGuestBook extends dBase {
	// 表名
	protected $name = 'club_guestbook';
	protected $pk = 'guestbook_id';
	protected $_fields = array (
			'club_guestbook' => array (
					"guestbook_id",
					"club_id",
					"uid",
					"guestbook_content",
					"guestbook_time" 
			),
			'club_activity_guestbook' => array (
					"act_guestbook_id",
					"activity_id",
					"uid",
					"act_guestbook_content",
					"act_guestbook_time" 
			) 
	);
	public function _initialize() {
		$this->connectDb ( 'club', true );
	}
	public function addClubGuestBook($guestbook) {
		if (empty ( $guestbook )) {
			return false;
		}
		$setsql = $this->implodeFields ( $guestbook, 'club_guestbook' );
		$sql = "insert into club_guestbook set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	public function getClubGuestBookList($club_id, $offset = 0, $length = 24) {
		$limit_sql = " limit $offset, $length";
		
		$list = $this->query ( "select * from club_guestbook where club_id=$club_id order by guestbook_id desc $limit_sql" );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['guestbook_id']] = $v;
		}
		
		return $result;
	}
	public function getClubGuestBookCount($club_id) {
		$result = $this->query ( "select count(*) as num from club_guestbook where club_id=$club_id" );
		
		return $result [0] ['num'];
	}
	public function addActivityGuestBook($guestbook) {
		if (empty ( $guestbook )) {
			return false;
		}
		$setsql = $this->implodeFields ( $guestbook, 'club_activity_guestbook' );
		$sql = "insert into club_activity_guestbook set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	public function getActivityGuestBookList($activity_id, $offset = 0, $length = 24) {
		$limit_sql = " limit $offset, $length";
		
		$list = $this->query ( "select * from club_activity_guestbook where activity_id=$activity_id order by act_guestbook_id desc $limit_sql" );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['act_guestbook_id']] = $v;
		}
		
		return $result;
	}
	public function getActivityGuestBookCount($activity_id) {
		$result = $this->query ( "select count(*) as num from club_activity_guestbook where activity_id=$activity_id" );
		
		return $result [0] ['num'];
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'club_members') {
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
