<?php
class mAdmin extends mBase {
	const ADMIN_PAGE_SIZE = 24;
	
	//获取cookie中的token
	public function getCookieTokenInfo($token_name) {
		$token = $_COOKIE[$token_name];
		$token_arr = token_decode($token);
		list($passwd, $uid) = empty($token_arr) || count($token_arr) < 2 ? array('', '') : $token_arr;
		$result = array('uid' => $uid, 'passwd'=> $passwd);
	
		return $result;
	}
	
	//获取token中的用户名和密码
	public function getCurrentUser() {
		if(!$this->isLogined()) {
			return false;
		}
		$cookie_info = $this->getCookieTokenInfo(ADMIN_SESSION_TOKEN);
		$uid = $cookie_info['uid'];
		if(!empty($uid)) {
			$userInfo = $this->getAdminById($uid);
		}
		if (empty($userInfo)) {
			return false;
		}
		$userInfo = array_pop($userInfo);
		$isAdminPost = $this->isAdminPost($userInfo['admin_id']);
		if ($isAdminPost) {
			$mDoor = ClsFactory::Create ('Model.mDoor');
			$userInfo['door'] = $mDoor->getDoorById(explode(',', $isAdminPost['door']));
		}
		return $userInfo;
	}
	
	public function uid() {
		if(!$this->isLogined()) {
			return false;
		}
		$cookie_info = $this->getCookieTokenInfo(ADMIN_SESSION_TOKEN);
		$uid = $cookie_info['uid'];
		return $uid;
	}
	
	//判断用户是否登陆
	public function isLogined() {
		$cookie_info = $this->getCookieTokenInfo(ADMIN_SESSION_TOKEN);
		$uid = $cookie_info['uid'];
		$passwd = $cookie_info['passwd'];
		if(!empty($uid)) {
			$info = $this->getAdminById($uid);
			$userInfo = $info[$uid];
			if(!empty($userInfo)) {
				return $userInfo['password'] == $passwd ? true : false;
			}
		}
		return false;
	}
	
	public function getAdminByName($user_name) {
		if (empty($user_name))
			return false;
		
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->getAdminByName($user_name);
		
		return $result;
	}
	
	//admin
	public function getAdminList($offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
		if (! $length)
			$length = self::ADMIN_PAGE_SIZE;
	
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$admin_list = $dAdmin->getAdminList($offset, $length);
		
		$group_ids = array();
		foreach ($admin_list as $admin) {
			$group_ids[] = $admin['group_id'];
		}
		$mAdmin = ClsFactory::Create ( 'Model.mAdmin' );
		$admin_group = $mAdmin->getAdminGroupById(array_unique($group_ids));
		foreach ($admin_list as &$admin) {
			$admin['group_name'] = $admin_group[$admin['group_id']]['group_name'];
		}
	
		return $admin_list;
	}
	
	public function getAdminCount() {
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$count = $dAdmin->getAdminCount();
	
		return $count;
	}
	
	public function getAdminById($admin_ids) {
		if (empty($admin_ids))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$admin_list = $dAdmin->getAdminById($admin_ids);
		
		$group_ids = array();
		foreach ($admin_list as $admin) {
			$group_ids[] = $admin['group_id'];
		}
		$mAdmin = ClsFactory::Create ( 'Model.mAdmin' );
		$admin_group = $mAdmin->getAdminGroupById(array_unique($group_ids));
		foreach ($admin_list as &$admin) {
			$admin['group_name'] = $admin_group[$admin['group_id']]['group_name'];
		}
		
		return $admin_list;
	}
	
	public function modifyAdminInfo($user_info, $admin_id) {
		if (! is_numeric ( $admin_id ) || empty($user_info))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->modifyAdminInfo($user_info, $admin_id);
		if ($result && !empty($user_info['password'])) {
			//cookie
			$user_token = token_encode(array($user_info['password'], $admin_id));
			header(getCookieStr(ADMIN_SESSION_TOKEN, $user_token, time()+3600*24*30,"/"));
		}
		return $result;
	}
	
	public function addAdmin($user_info) {
		if (empty( $user_info ))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->addAdmin($user_info);
		return $result;
	}
	
	public function delAdminInfo($admin_id) {
		if (!is_numeric($admin_id))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->delAdminInfo($admin_id);
	
		return $result;
	}
	
	//admin_group
	public function checkCurrentUserLevel($level_id) {
		$admin_info = $this->getCurrentUser();
		$group_id = $admin_info['group_id'];
		$group_info = $this->getAdminGroupById($group_id);
		if (!$group_info) {
			return false;
		}
		
		$group_info = array_pop($group_info);
		if (in_array(strval($level_id), explode(',', $group_info['levels']))) {
			return true;
		}
		return false;
	}
	
	public function getAdminGroupById($group_ids) {
		if (empty( $group_ids ))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->getAdminGroupById($group_ids);
		return $result;
	}
	
	public function getAdminGroup() {
		$dAdmin = ClsFactory::Create ('Data.dAdmin');
		$result = $dAdmin->getAdminGroup();
	
		return $result;
	}
	
	public function modifyAdminGroup($admin_group, $group_id) {
		if (! is_numeric ( $group_id ) || empty($admin_group))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->modifyAdminGroup($admin_group, $group_id);
		return $result;
	}
	
	public function addAdminGroup($admin_group) {
		if (empty( $admin_group ))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->addAdminGroup($admin_group);
		return $result;
	}
	
	public function delAdminGroup($group_id) {
		if (!is_numeric($group_id))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->delAdminGroup($group_id);
	
		return $result;
	}
	
	//admin_post
	/**
	 * 获取某管理员在某段时间所管理的门
	 * @return array
	 *
	 */
	public function getAdminPostByAdminTime($admin_id, $start_time, $end_time) {
		if (empty($admin_id) || $start_time < 0 || $end_time < $start_time) {
			return false;
		}
		$admin_list = $this->getAdminPostByCond('admin_id='.$admin_id.' and start_time<='.$start_time.' and end_time>='.$end_time, 0, 0);
		if (!$admin_id) {
			return false;
		}
		return $admin_list;
	}
	
	public function getAdminPostById($post_ids) {
		if (empty( $post_ids ))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->getAdminPostById($post_ids);
		return $result;
	}
	
	public function modifyAdminPost($admin_post, $post_id) {
		if (! is_numeric ( $post_id ) || empty($admin_post))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->modifyAdminPost($admin_post, $post_id);
		return $result;
	}
	
	public function addAdminPost($admin_post) {
		if (empty( $admin_post ))
			return false;
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$result = $dAdmin->addAdminPost($admin_post);
		return $result;
	}
	
	public function startWork($admin_id, $mac = '') {
		$isAdminPost = $this->isAdminPost($admin_id);
		if ($isAdminPost) {
			return true;
		} else {
			if ($mac) {
				$post = array();
				$post['admin_id'] = $admin_id;
				$post['start_time'] = time();
				$this->uid() && $post['last_admin_id'] = $this->uid();
				$mDoor = ClsFactory::Create('Model.mDoor');
				$door_info = $mDoor->getDoorInfo();
				$door_ids = array();
				foreach ($door_info as $door) {
					if ($mac == $door['mac'] && !in_array($door['door_id'], $door_ids)) {
						$door_ids[] = $door['door_id'];
					}
				}
				if (!empty($door_ids)) {
					$post['door'] = implode(',', $door_ids);
					return $this->addAdminPost($post) ? true : false;
				}
			}
			return false;
		}
	}
	
	public function endWork($admin_id) {
		$isAdminPost = $this->isAdminPost($admin_id);
		if ($isAdminPost) {
			$post = array();
			$post['end_time'] = time();
			return $this->modifyAdminPost($post, $isAdminPost['post_id']) ? true : false;
		} else {
			return false;
		}
	}
	
	public function isAdminPost($admin_id) {
		$post = $this->getAdminPostByCond('admin_id='.$admin_id.', start_time desc', 0, 1);
		if (empty($post)) {
			return false;
		}
		$post = array_pop($post);
		
		if (is_null($post) || is_null($post['end_time'])) {
			$mDoor = ClsFactory::Create('Model.mDoor');
			$door_info = $mDoor->getDoorById(explode(',', $post['door']));
			if ($door_info) {
				$door_info = array_pop($door_info);
				$post['mac'] = $door_info['mac'];
			}
			return $post;
		}
		
		return false;
	}
	
	public function getAdminPost($admin_id) {
		$isAdminPost = $this->isAdminPost($admin_id);
		if (!$isAdminPost) {
			return false;
		}
		
		$isAdminPost['end_time'] = time();
		
		return $isAdminPost;
	}
	
	public function getAdminPostByDoorTime($door_id, $time) {
		$post_list = $this->getAdminPostByCond('start_time<='.$time.' and end_time>='.$time.', start_time desc', 0, 0);
		$post = array();
		foreach ($post_list as $value) {
			if (in_array($door_id, explode(',', $value['door']))) {
				$post = $value;
				break;
			}
		}
		if (empty($post)) {
			return false;
		}
		
		$admin_list = $this->getAdminById($post['admin_id']);
		if ($admin_list) {
			$admin_info = array_pop($admin_list);
			$post = array_merge($post, $admin_info);
		}
		
		return $post;
	}
	
	//$condition_str值：筛选条件可以为表中一个或多个字段条件组合，排序条件只能为start_time的升序或降序
	//筛选条件在前，排序条件在后，中间用逗号分隔
	//如"card_id=$ and start_time > $ and start_time<$, start_time desc"
	//筛选条件可以为空取出全部列表
	public function getAdminPostByCond($condition_str = '', $offset = 0, $length = 24) {
		$offset = intval($offset );
		$length = intval($length );
// 		if (! $length)
// 			$length = 24;
	
		$dAdmin = ClsFactory::Create ( 'Data.dAdmin' );
		$admin_list = $dAdmin->getAdminPostByCond($condition_str, $offset, $length);
	
		return $admin_list;
	}
}

    
