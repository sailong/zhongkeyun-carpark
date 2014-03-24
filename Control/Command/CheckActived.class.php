<?php
import ( '@.Control.Command.Command' );
class CheckActived extends Command {
	public function onCommand() {
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		if (! $mUser->isLogined ())
			return false;
		$userinfo = $mUser->getCurrentUser ();
		$uid = $userinfo ['client_account'];
		// 忽略的页面url
		$filterPage = array (
				strtolower ( "/homeuser/activate/index" ),
				strtolower ( '/Public/Area/getAreaList?area_id' ),
				strtolower ( "/Homeuser/Activate/modifyAccount" ),
				strtolower ( "/login/loginout" ),
				strtolower ( "/Homeuser/Index/getnewscount" ),
				strtolower ( "/Amscontrol/" ),
				strtolower ( "/Basecontrol/" ),
				strtolower ( "/Adminbase/" ),
				strtolower ( "/Admingroup/" ),
				strtolower ( "/sso/user" ),
				strtolower ( "/login/logout?url" ) 
		);
		$currentPage = false;
		foreach ( $filterPage as $f ) {
			if (stripos ( $_SERVER ['REQUEST_URI'], $f ) !== false) {
				$currentPage = $f;
				break;
			}
		}
		if (! in_array ( $currentPage, $filterPage ) && $mUser->isActivated ( $uid )) {
			$data = array ();
			$data ['error'] = array (
					'code' => - 1,
					'message' => '系统繁忙' 
			);
			$data ['data'] = array (
					'location' => '/Homeuser/Activate/index' 
			);
			$this->errorOut ( $data );
		}
		return true;
	}
}
