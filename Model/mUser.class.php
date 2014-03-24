<?php
class mUser extends mBase {
	const USER_PAGE_SIZE = 24;
	public function addUser($user_info) {
		if (empty ( $user_info ))
			return false;
		$dUser = ClsFactory::Create('Data.dUser');
		return $dUser->addUser($user_info);
	}
	
	public function modifyUserInfo($user_info, $uid) {
		if (empty ( $user_info ) || ! $uid) {
			return false;
		}
		$dUser = ClsFactory::Create ( 'Data.dUser' );
		
		return $dUser->modifyUserInfo ( $user_info, $uid );
	}
	public function getUserList($status, $offset = 0, $length = 24) {
		$offset = intval ($offset);
		$length = intval ($length);
		$status = is_numeric($status) ? intval($status) : -1;
		if (! $length)
			$length = self::USER_PAGE_SIZE;
		
		$mUser = ClsFactory::Create ( 'Data.dUser' );
		$result = $mUser->getUserList($status, $offset, $length);
		
		return $result;
	}
	
	public function getUserCount($status) {
		if (! is_numeric ( $status ))
			return false;
		$mUser = ClsFactory::Create ( 'Data.dUser' );
		$count = $mUser->getUserCount ( $status );
		return $count;
	}
	
	public function getUserInfo($uids) {
		if (empty ( $uids )) {
			return false;
		}
		$dUser = ClsFactory::Create ( 'Data.dUser' );
		$user_info = $dUser->getUserInfo ( $uids );
		
		return $user_info;
	}
}

    
