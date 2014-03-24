<?php
class GuestbookAction extends Controller {
	public function _initialize() {
	}
	public function index() {
	}
	
	/**
	 * 某俱乐部留言列表 URL:/club/guestbook/clublist
	 * 某俱乐部留言列表 请求方式GET 错误代码：{"request" : "/club/guestbook/clublist","error_code" : "20001","error" : "access_token不合法"}
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
	 * @param
	 *        	int club_id 俱乐部id
	 * @return mixed {"club_list":[{"club_id":"1","club_name":"\u4ff1\u4e50\u90e8...0","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...0","club_logo":"logo...address.0","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"},{"club_id":"2","club_name":"\u4ff1\u4e50\u90e8...1","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...1","club_logo":"logo...address.1","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"},{"club_id":"3","club_name":"\u4ff1\u4e50\u90e8...2","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...2","club_logo":"logo...address.2","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}],"count":"30","page_count":2}
	 */
	public function clubList() {
		import ( 'Model.mGuestBook' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		$club_id = $this->objInput->getInt ( 'club_id' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		if ($page_size <= 0)
			$page_size = mGuestBook::GUESTBOOK_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mGuestBook = ClsFactory::Create ( 'Model.mGuestBook' );
		$count = $mGuestBook->getClubGuestBookCount ( $club_id );
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		$guestbook_info = $mGuestBook->getClubGuestBookList ( $club_id, $offset, $page_size );
		$uids = array ();
		foreach ( $guestbook_info as $g ) {
			$uids [] = $g ['uid'];
		}
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$user_info = $mUser->getUserInfo ( $uids );
		foreach ( $guestbook_info as &$g ) {
			$g = array_merge ( $g, $user_info [$g ['uid']] );
		}
		$result = array ();
		$result ['guestbook_list'] = array_values ( $guestbook_info );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 发布俱乐部留言 URL:/club/guestbook/publishguestbook
	 * 发布俱乐部留言 请求方式POST 错误代码：{"request" : "/club/guestbook/publishguestbook","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	string club_id 俱乐部id
	 * @param
	 *        	string guestbook_content 留言内容
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function publishGuestBook() {
		$club_id = $this->objInput->postInt ( 'club_id' );
		$guestbook_content = $this->objInput->postStr ( 'guestbook_content' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		if (! $guestbook_content) {
			$this->displayError ( 20028 );
		}
		
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$guestbook_info = array ();
		$guestbook_info ['uid'] = $uid;
		$guestbook_info ['guestbook_content'] = $guestbook_content;
		$guestbook_info ['club_id'] = $club_id;
		$guestbook_info ['guestbook_time'] = time ();
		
		$mGuestBook = ClsFactory::Create ( 'Model.mGuestBook' );
		$result = $mGuestBook->addClubGuestBook ( $guestbook_info );
		if (! $result) {
			$this->displayError ( 20029 );
		}
		$this->displayJson ( $guestbook_info );
	}
	
	/**
	 * 某活动留言列表 URL:/club/guestbook/activitylist
	 * 某活动留言列表 请求方式GET 错误代码：{"request" : "/club/guestbook/activitylist","error_code" : "20001","error" : "access_token不合法"}
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
	 * @param
	 *        	int activity_id 活动id
	 * @return mixed {"club_list":[{"club_id":"1","club_name":"\u4ff1\u4e50\u90e8...0","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...0","club_logo":"logo...address.0","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"},{"club_id":"2","club_name":"\u4ff1\u4e50\u90e8...1","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...1","club_logo":"logo...address.1","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"},{"club_id":"3","club_name":"\u4ff1\u4e50\u90e8...2","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...2","club_logo":"logo...address.2","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}],"count":"30","page_count":2}
	 */
	public function activityList() {
		import ( 'Model.mGuestBook' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		$activity_id = $this->objInput->getInt ( 'activity_id' );
		if (! $activity_id) {
			$this->displayError ( 20002 );
		}
		if ($page_size <= 0)
			$page_size = mGuestBook::GUESTBOOK_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mGuestBook = ClsFactory::Create ( 'Model.mGuestBook' );
		$count = $mGuestBook->getActivityGuestBookCount ( $activity_id );
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		$guestbook_info = $mGuestBook->getActivityGuestBookList ( $activity_id, $offset, $page_size );
		$uids = array ();
		foreach ( $guestbook_info as $g ) {
			$uids [] = $g ['uid'];
		}
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$user_info = $mUser->getUserInfo ( $uids );
		foreach ( $guestbook_info as &$g ) {
			$g = array_merge ( $g, $user_info [$g ['uid']] );
		}
		$result = array ();
		$result ['act_guestbook_list'] = array_values ( $guestbook_info );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 发布活动留言 URL:/club/guestbook/publishactguestbook
	 * 发布活动留言 请求方式POST 错误代码：{"request" : "/club/guestbook/publishactguestbook","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	string activity_id 活动id
	 * @param
	 *        	string act_guestbook_content 留言内容
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function publishActGuestBook() {
		$activity_id = $this->objInput->postInt ( 'activity_id' );
		$guestbook_content = $this->objInput->postStr ( 'act_guestbook_content' );
		if (! $activity_id) {
			$this->displayError ( 20002 );
		}
		if (! $guestbook_content) {
			$this->displayError ( 20030 );
		}
		
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$guestbook_info = array ();
		$guestbook_info ['uid'] = $uid;
		$guestbook_info ['act_guestbook_content'] = $guestbook_content;
		$guestbook_info ['activity_id'] = $activity_id;
		$guestbook_info ['act_guestbook_time'] = time ();
		
		$mGuestBook = ClsFactory::Create ( 'Model.mGuestBook' );
		$result = $mGuestBook->addActivityGuestBook ( $guestbook_info );
		if (! $result) {
			$this->displayError ( 20031 );
		}
		$this->displayJson ( $guestbook_info );
	}
}
