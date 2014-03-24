<?php
class dAdmin extends dBase {
	// 表名
	protected $name = 'admin';
	protected $pk = 'admin_id';
	protected $_fields = array (
			'admin' => array (
					"admin_id",
					"admin_name",
					"password",
					"group_id",
					"create_time",
					"last_time",
					'mobile',
					'is_receive_msg'
			),
			'admin_group' => array (
					'group_id',
					'group_name',
					'remark',
					'levels',
			),
			'admin_post' => array (
					'post_id',
					'admin_id',
					'door',
					'start_time',
					'end_time',
					'last_admin_id'
			)
	);
	public function _initialize() {
	}
	
	public function addAdmin($user_info) {
		if (empty ( $user_info )) {
			return false;
		}
		$setsql = $this->implodeFields ( $user_info, 'admin' );
		$sql = "insert into admin set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyAdminInfo($user_info, $admin_id) {
		if (empty ( $user_info ) || ! $admin_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $user_info, 'admin' );
		$result = $this->execute ( "update admin set $setsql where admin_id=$admin_id" );
		return $result;
	}
	
	public function delAdminInfo($admin_id) {
		if (!is_numeric($admin_id)) {
			return false;
		}
	
		$result = $this->query ("delete from admin where admin_id=$admin_id");
	
		return $result;
	}
	
	public function getAdminById($admin_ids) {
		$admin_ids = $this->checkIds($admin_ids);
		if (!$admin_ids) {
			return false;
		}
		
		$list = $this->query ( "select * from admin where admin_id in (".implode ( ',', $admin_ids ).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $user) {
			$result[$user['admin_id']] = $user;
		}
		
		return $result;
	}
	
	public function getAdminByName($user_name) {
	
		$list = $this->query ( "select * from admin where admin_name='$user_name'" );
		
		if (! $list)
			return false;
	
		return array_pop($list);
	}
	
	public function getAdminList($offset = 0, $length = 24) {
		$limit_sql = " limit $offset, $length";
	
		$list = $this->query ( "select * from admin" . $limit_sql );
		if (! $list)
			return false;
		return $list;
	}
	
	public function getAdminCount() {
		$list = $this->query ( "select count(*) as num from admin " );
		$result = array_pop ( $list );
		return $result ['num'];
	}
	
	//admin_group
	public function addAdminGroup($admin_group) {
		if (empty ( $admin_group )) {
			return false;
		}
		$setsql = $this->implodeFields ( $admin_group, 'admin_group' );
		$sql = "insert into admin_group set $setsql";
		$result = $this->execute ( $sql );
		return $result ? $this->getLastInsID () : false;
	}
	
	public function modifyAdminGroup($admin_group, $group_id) {
		if (empty ( $admin_group ) || ! $group_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $admin_group, 'admin_group' );
		$result = $this->execute ( "update admin_group set $setsql where group_id=$group_id" );
		return $result;
	}
	
	public function delAdminGroup($group_id) {
		if (!is_numeric($group_id)) {
			return false;
		}
	
		$result = $this->query ("delete from admin_group where group_id=$group_id");
	
		return $result;
	}
	
	public function getAdminGroupById($group_ids) {
		$group_ids = $this->checkIds($group_ids);
		if (!$group_ids) {
			return false;
		}
	
		$list = $this->query ( "select * from admin_group where group_id in (".implode(',', $group_ids).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $group) {
			$result[$group['group_id']] = $group;
		}
	
		return $result;
	}
	
	public function getAdminGroup() {
		$list = $this->query ("select * from admin_group");
		if (!$list)
			return false;
	
		return $list;
	}
	
	//admin_post
	public function addAdminPost($admin_post) {
		if (empty ( $admin_post )) {
			return false;
		}
		$setsql = $this->implodeFields ( $admin_post, 'admin_post' );
		$sql = "insert into admin_post set $setsql";
		$result = $this->execute($sql);
		return $result ? $this->getLastInsID() : false;
	}
	
	public function modifyAdminPost($admin_post, $post_id) {
		if (empty ( $admin_post ) || ! $post_id) {
			return false;
		}
		$setsql = $this->implodeFields ( $admin_post, 'admin_post' );
		$result = $this->execute ( "update admin_post set $setsql where post_id=$post_id" );
		return $result;
	}
	
	public function getAdminPostById($post_ids) {
		$post_ids = $this->checkIds($post_ids);
		if (!$post_ids) {
			return false;
		}
		
		$list = $this->query ( "select * from admin_post where post_id in (".implode(',', $post_ids).")" );
		if (! $list)
			return false;
		$result = array();
		foreach ($list as $post) {
			$result[$post['post_id']] = $post;
		}
	
		return $result;
	}
	
	public function getAdminPostByCond($condition_str = '', $offset = 0, $length = 24) {
		if ($length) {
			$limit_sql = " limit $offset, $length";
		} else {
			$limit_sql = '';
		}
	
		$cond = explode(',', $condition_str);
		$cond_str = $cond[0];
		$order = $cond[1];
		if ($cond_str) {
			$where_str = ' where '.$cond_str;
		} else {
			$where_str = '';
		}
		if ($order) {
			$order_str = ' order by '.$order;
		} else {
			$order_str = '';
		}
	
		$sql = "select * from admin_post" . $where_str . $order_str . $limit_sql;
		$list = $this->query ($sql);
		if (!$list)
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
	private function implodeFields($dataarr, $table = 'admin') {
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
