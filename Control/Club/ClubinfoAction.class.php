<?php
class ClubInfoAction extends Controller {
	public function _initialize() {
	}
	public function index() {
		$this->allList ();
	}
	
	/**
	 * 俱乐部列表 URL:/club/clubinfo/alllist
	 * 俱乐部列表 请求方式GET 错误代码：{"request" : "/club/clubinfo/alllist","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int page 默认1
	 * @param
	 *        	int pagesize 默认是24
	 * @return mixed {"club_list":[{"club_id":"1","club_name":"\u4ff1\u4e50\u90e8...0","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...0","club_logo":"logo...address.0","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"},{"club_id":"2","club_name":"\u4ff1\u4e50\u90e8...1","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...1","club_logo":"logo...address.1","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"},{"club_id":"3","club_name":"\u4ff1\u4e50\u90e8...2","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...2","club_logo":"logo...address.2","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}],"count":"30","page_count":2}
	 */
	public function allList() {
		import ( 'Model.mClub' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		if ($page_size <= 0)
			$page_size = mClub::CLUB_INFO_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$club_count = $mClub->getClubInfoCount ();
		$page_count = ceil ( $club_count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		$club_info = $mClub->getClubInfo ( $offset, $page_size );
		$result = array ();
		$result ['club_list'] = array_values ( $club_info );
		$result ['count'] = $club_count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 俱乐部详细信息 URL:/club/clubinfo/detail
	 * 俱乐部详细信息 请求方式GET 错误代码：{"request" : "/club/clubinfo/detail","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int club_id
	 * @return mixed {"club_id":"2","club_name":"\u4ff1\u4e50\u90e8...1","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...1","club_logo":"logo...address.1","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}
	 */
	public function detail() {
		$club_id = $this->objInput->getInt ( 'club_id' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$club_info = $mClub->getClubInfoById ( $club_id );
		$this->displayJson ( $club_info [$club_id] );
	}
	
	/**
	 * 申请俱乐部 URL:/club/clubinfo/applyclub
	 * 申请俱乐部 请求方式POST 错误代码：{"request" : "/club/clubinfo/applyclub","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	string club_name 俱乐部名称
	 * @param
	 *        	string club_summary 俱乐部简介
	 * @param
	 *        	file club_logo 俱乐部logo
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function applyClub() {
		$club_name = $this->objInput->postStr ( 'club_name' );
		$club_summary = $this->objInput->postStr ( 'club_summary' );
		if (! $club_name) {
			$this->displayError ( 20007 );
		}
		if (! $club_summary) {
			$this->displayError ( 20008 );
		}
		// logo
		if (! isset ( $_FILES ['club_logo'] ['name'] ) || $_FILES ['club_logo'] ['name'] == "") {
			$this->displayError ( 20009 );
		}
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$club_info = array ();
		$club_info ['uid'] = $uid;
		$club_info ['club_name'] = $club_name;
		$club_info ['club_summary'] = $club_summary;
		$club_info ['club_apply_time'] = time ();
		
		$up_init = array (
				'attachmentspath' => WEB_ROOT_DIR . '/attachment',
				'ifresize' => true,
				'resize_width' => 200,
				'resize_height' => 200,
				'cut' => 1 
		);
		import ( "Libraries.uploadfile" );
		$upload = new uploadfile ( $up_init );
		$upload->allow_type = explode ( ",", 'jpg,gif,png,bmp' );
		$file = $upload->upfile ( 'club_logo' );
		if (empty ( $file )) {
			$this->displayError ( 20010 );
		}
		$club_info ['club_logo'] = '/' . str_replace ( WEB_ROOT_DIR . '/', '', $file ['getfilename'] );
		if ($file ['getsmallfilename']) {
			$club_info ['club_logo'] = '/' . str_replace ( WEB_ROOT_DIR . '/', '', $file ['getsmallfilename'] );
		}
		
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$result = $mClub->addClubApply ( $club_info );
		if (! $result) {
			$this->displayError ( 20011 );
		}
		$this->displayJson ( $club_info );
	}
	
	/**
	 * 获取自己有管理权限的俱乐部 URL:/club/clubinfo/mymanageclubs
	 * 获取自己有管理权限俱乐部 请求方式POST 错误代码：{"request" : "/club/clubinfo/mymanageclubs","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function myManageClubs() {
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$club_info = $mUser->getClubInfoByUid ( $uid );
		foreach ( $club_info as $id => $club ) {
			if ($club ['member_level'] == 2) {
				unset ( $club_info [$id] );
			}
		}
		if (empty ( $club_info )) {
			$this->displayError ( 20027 );
		}
		$club_ids = array_keys ( $club_info );
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$clubs = $mClub->getClubInfoById ( $club_ids );
		$result = array ();
		foreach ( $clubs as $id => $club ) {
			$result [] = array_merge ( $club, $club_info [$id] );
		}
		$this->displayJson ( $result );
	}
	
	/**
	 * 我的俱乐部 URL:/club/clubinfo/myclubs
	 * 获取我的俱乐部 请求方式GET 错误代码：{"request" : "/club/clubinfo/mymanageclubs","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function myClubs() {
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$club_info = $mUser->getClubInfoByUid ( $uid );
		$club_ids = array_keys ( $club_info );
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$clubs = $mClub->getClubInfoById ( $club_ids );
		$result = array ();
		$result ['club_list'] = array_values ( $clubs );
		$result ['count'] = count ( $clubs );
		$result ['page_count'] = 1;
		$this->displayJson ( $result );
	}
	private function initData() {
		for($i = 0; $i < 30; $i ++) {
			// club
			$club_info = array ();
			$club_info ['club_name'] = "俱乐部..." . $i;
			$club_info ['club_summary'] = "俱乐部简介..." . $i;
			$club_info ['club_logo'] = "logo...address." . $i;
			$club_info ['club_equal_age'] = 0;
			$club_info ['club_girl_num'] = 0;
			$club_info ['club_boy_num'] = 0;
			$club_info ['club_create_time'] = time ();
			$mClub = ClsFactory::Create ( 'Model.mClub' );
			$club_id = $mClub->addClubInfo ( $club_info );
			
			// member
			$mUser = ClsFactory::Create ( 'Model.mUser' );
			$uids = range ( 1, 500 );
			$users = $mUser->getUserInfo ( $uids );
			
			$uids = array_rand ( $users, 40 );
			foreach ( $uids as $uid ) {
				$user_info = $users [$uid];
				$user_info ['club_id'] = $club_id;
				$user_info ['member_time'] = time ();
				$user_info ['member_status'] = 1;
				$user_info ['member_level'] = 2;
				
				$mUser->addClubMemeber ( $user_info );
			}
		}
	}
}
