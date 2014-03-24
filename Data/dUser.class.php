<?php
class dUser extends dBase {
	// 表名
	protected $name = 'user';
	protected $pk = 'uid';
	protected $_fields = array (
			'user' => array (
					'uid',
					'mobile',
					'sex',
					'name',
					'nick_name',
					'birthday',
					'height',
					'weight',
					'edu',
					'job',
					'revenue',
					'has_car',
					'has_house',
					'avatar',
					'introduce',
					'status',
			)
	);
	public function _initialize() {
	}
	public function addUser($user_info) {
		if (empty ( $user_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $user_info, 'user' );
		$sql = "insert into user set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	public function modifyUserInfo($user_info, $uid) {
		if (empty ( $user_info ) || ! $uid) {
			return false;
		}
		
		$setsql = $this->implodeFields ( $user_info, 'user' );
		$result = $this->execute ( "update user set $setsql where uid=$uid" );
		return $result;
	}
	
	public function getUserList($status, $offset = 0, $length = 24) {
		
		$limit_sql = " limit $offset, $length";
		
		$where_str = $status > 0 ? ' where status ='.$status : '';
		
		$list = $this->query ( "select * from user $where_str $limit_sql" );
		if (! $list)
			return false;
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['uid']] = $v;
		}
		
		return $result;
	}
	
	
	public function getUserCount($status) {
		$list = $this->query ( "select count(*) as num from user where status=$status");
		$result = array_pop ( $list );
		return $result ['num'];
	}
	
	public function getUserInfo($uids) {
		$uids = $this->checkIds ( $uids );
		if (! $uids) {
			return false;
		}
		$this->connectDb ( 'main', true );
		$wheresql = "where uid in (" . implode ( ',', $uids ) . ")";
		$list = $this->query ( "select * from user $wheresql" );
		$result = array ();
		foreach ( $list as $v ) {
			$result [$v ['uid']] = $v;
		}
		return $result ? $result : false;
	}
	
	/**
	 * 将对应的字段进行组合
	 * 
	 * @param
	 *        	$datas
	 * @param
	 *        	$split
	 */
	private function implodeFields($dataarr, $table = 'user') {
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
