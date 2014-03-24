<?php
class dToken extends dBase {
	// 表名
	protected $name = 'oauth2app_accesstoken';
	protected $pk = 'id';
	protected $_fields = array (
			'oauth2app_accesstoken' => array (
					'id',
					'client_id',
					'user_id',
					'token',
					'refresh_token',
					'mac_key',
					'issue',
					'expire',
					'refreshable' 
			) 
	);
	public function _initialize() {
	}
	
	/**
	 * 根据uid获取用户的相关信息
	 * 单个值时返回1维数组，兼容以前的代码，如果对应返回的数据有多个则返回2维数组
	 * 
	 * @param
	 *        	$uids
	 */
	public function getTokenInfo($access_token) {
		if (empty ( $access_token )) {
			return false;
		}
		
		$uids = is_array ( $uids ) ? $uids : array (
				$uids 
		);
		$sql = "select * from oauth2app_accesstoken where token='" . $access_token . "'";
		
		$list = $this->query ( $sql );
		
		if (empty ( $list ))
			return false;
		
		$result = array_pop ( $list );
		
		if ($result ['expire'] < time ())
			return false;
		
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
	private function implodeFields($dataarr, $table = 'wmw_client_account') {
		if (empty ( $dataarr ) || empty ( $table )) {
			return false;
		}
		$dataarr = is_array ( $dataarr ) ? $dataarr : array (
				$dataarr 
		);
		$dataarr = $this->checkFields ( $dataarr, $table );
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
