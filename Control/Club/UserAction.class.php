<?php
class UserAction extends Controller {
	public function _initialize() {
	}
	public function index() {
	}
	
	/**
	 * 申请俱乐部成员 URL:/club/user/applyclubmember
	 * 申请俱乐部成员 请求方式GET 错误代码：{"request" : "/club/user/applyclubmember","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int club_id
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function applyClubMember() {
		$club_id = $this->objInput->getInt ( 'club_id' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$member_info = $mUser->getClubApplyMemberById ( $uid, $club_id );
		if ($member_info) {
			$this->displayError ( 20004 );
		}
		$user_info = array ();
		$user_info ['uid'] = $uid;
		$user_info ['club_id'] = $club_id;
		$user_info ['apply_status'] = 0;
		$user_info ['apply_time'] = time ();
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$result = $mUser->addClubApplyMemeber ( $user_info );
		if (! $result) {
			$this->displayError ( 20003 );
		}
		$this->displayJson ( $user_info );
	}
	
	/**
	 * 报名俱乐部活动成员 URL:/club/user/applyactivitymember
	 * 报名俱乐部活动成员 请求方式GET 错误代码：{"request" : "/club/user/applyactivitymember","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int activity_id
	 * @return mixed {"uid":"101","activity_id":5,"activity_member_status":0,"activity_member_time":1366702515,"activity_join_status":0}
	 */
	public function applyActivityMember() {
		$activity_id = $this->objInput->getInt ( 'activity_id' );
		if (! $activity_id) {
			$this->displayError ( 20002 );
		}
		
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$member_info = $mUser->getClubActivityMemberById ( $uid, $activity_id );
		if ($member_info) {
			$this->displayError ( 20006 );
		}
		$mActivity = ClsFactory::Create ( 'Model.mActivity' );
		$activity_info = array_pop ( $mActivity->getActivityById ( $activity_id ) );
		$user = array_pop ( $mUser->getUserInfo ( $uid ) );
		// 性别检查
		if ($activity_info ['activity_sex'] != 2 && $activity_info ['activity_sex'] != $user ['sex']) {
			$this->displayError ( 20032 );
		}
		// 年龄检查
		$age = $mUser->ageForTime ( $user ['birthday'] );
		if ($activity_info ['activity_max_age'] < $age || $age < $activity_info ['activity_min_age']) {
			$this->displayError ( 20033 );
		}
		// 学历检查
		if ($activity_info ['activity_eb'] < $user ['edu_background']) {
			$this->displayError ( 20034 );
		}
		// 报名截止时间
		if ($activity_info ['activity_stop_time'] < time ()) {
			$this->displayError ( 20035 );
		}
		// 名额限制
		if (( int ) $user ['sex'] && $activity_info ['activity_join_boy'] >= $activity_info ['activity_boy_num']) {
			$this->displayError ( 20036 );
		}
		if (( int ) $user ['sex'] == 0 && $activity_info ['activity_join_girl'] >= $activity_info ['activity_girl_num']) {
			$this->displayError ( 20037 );
		}
		// 俱乐部限制
		$activity_club = $activity_info ['activity_club'];
		$club_info = $mUser->getClubInfoByUid ( $uid );
		$club_ids = array_keys ( $club_info );
		if ($activity_club == 1 && empty ( $club_ids )) {
			$this->displayError ( 20038 );
		}
		if ($activity_club == 2 && ! in_array ( $activity_info ['club_id'], $club_ids )) {
			$this->displayError ( 20039 );
		}
		if (strpos ( $v ['activity_club'], '|' ) !== false) {
			$ids = explode ( '|', $activity_club );
			foreach ( $ids as $k => $v ) {
				if (empty ( $v )) {
					unset ( $ids [$k] );
				}
			}
			$result = array_intersect ( $club_ids, $ids );
			if (empty ( $result )) {
				$this->displayError ( 20040 );
			}
		}
		
		$user_info = array ();
		$user_info ['uid'] = $uid;
		$user_info ['activity_id'] = $activity_id;
		$user_info ['activity_member_status'] = 0;
		$user_info ['activity_member_time'] = time ();
		$user_info ['activity_join_status'] = 0;
		
		$result = $mUser->addClubActivityMemeber ( $user_info );
		if (! $result) {
			$this->displayError ( 20005 );
		}
		$this->displayJson ( $user_info );
	}
	
	/**
	 * 俱乐部成员信息 URL:/club/user/getclubmemberinfo
	 * 俱乐部成员信息 请求方式GET 错误代码：{"request" : "/club/user/getclubmemberinfo","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int club_id
	 * @return mixed {"uid":"101","activity_id":5,"activity_member_status":0,"activity_member_time":1366702515,"activity_join_status":0}
	 */
	public function getClubMemberInfo() {
		$club_id = $this->objInput->getInt ( 'club_id' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$member_info = $mUser->getClubApplyMemberById ( $uid, $club_id );
		$user_club = $mUser->getClubInfoByUid ( $uid );
		$user = $user_club [$club_id];
		$user_info = array ();
		$user_info ['uid'] = $uid;
		$user_info ['club_id'] = '';
		$user_info ['apply_status'] = '';
		$user_info ['apply_time'] = '';
		if ($member_info) {
			$user_info = array_pop ( $member_info );
		}
		$user_info ['member_level'] = $user ['member_level'];
		$this->displayJson ( $user_info );
	}
	
	/**
	 * 俱乐部活动成员信息 URL:/club/user/getactivitymemberinfo
	 * 俱乐部活动成员信息 请求方式GET 错误代码：{"request" : "/club/user/getactivitymemberinfo","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int activity_id
	 * @return mixed {"uid":"101","activity_id":5,"activity_member_status":0,"activity_member_time":1366702515,"activity_join_status":0}
	 */
	public function getActivityMemberInfo() {
		$activity_id = $this->objInput->getInt ( 'activity_id' );
		if (! $activity_id) {
			$this->displayError ( 20002 );
		}
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$member_info = $mUser->getClubActivityMemberById ( $uid, $activity_id );
		$user = $mUser->getUserInfo ( $uid );
		$user_info = array ();
		$user_info ['uid'] = $uid;
		$user_info ['activity_id'] = '';
		$user_info ['activity_member_status'] = '';
		$user_info ['activity_member_time'] = '';
		$user_info ['activity_join_status'] = '';
		if ($member_info) {
			$user_info = array_pop ( $member_info );
		}
		$user_info = array_merge ( $user_info, array_pop ( $user ) );
		$this->displayJson ( $user_info );
	}
	
	/**
	 * 俱乐部活动成员 URL:/club/user/activitymembers
	 * 俱乐部活动成员 请求方式GET 错误代码：{"request" : "/club/user/activitymembers","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int activity_id
	 * @param
	 *        	int page 默认1
	 * @param
	 *        	int pagesize 默认是24
	 * @return mixed {"member_list":[{"member_id":"67","uid":"348","club_id":"2","sex":"1","birthday":"93974400","member_time":"1366555334","member_status":"1","is_single":"0","member_level":"2","id":"350","user_id":"348","sexual":"0","city_no":"44020","nickname":"\u5c0f\u9f8d","avatar":"headpics\/2013\/03\/13\/15800110032.jpg","mobile":"15800110032","area_code":"86","user_type":"1","height":"0","weight":"0","industry":"","job":"","edu_background":"","school":"","from_platform":"iPhone","user_status":"0","score":"0"}],"count":"33","page_count":2}
	 */
	public function activityMembers() {
		$activity_id = $this->objInput->getInt ( 'activity_id' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		if (! $activity_id) {
			$this->displayError ( 20002 );
		}
		import ( 'Model.mUser' );
		if ($page_size <= 0)
			$page_size = mUser::USER_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$count = $mUser->getClubActivityMemberCount ( $activity_id );
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$members = $mUser->getClubActivityMemberList ( $activity_id, $offset, $page_size );
		$result = array ();
		$result ['member_list'] = array_values ( $members );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 俱乐部成员 URL:/club/user/clubmembers
	 * 俱乐部成员 请求方式GET 错误代码：{"request" : "/club/user/clubmembers","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int club_id
	 * @param
	 *        	int page 默认1
	 * @param
	 *        	int pagesize 默认是24
	 * @return mixed {"member_list":[{"member_id":"67","uid":"348","club_id":"2","sex":"1","birthday":"93974400","member_time":"1366555334","member_status":"1","is_single":"0","member_level":"2","id":"350","user_id":"348","sexual":"0","city_no":"44020","nickname":"\u5c0f\u9f8d","avatar":"headpics\/2013\/03\/13\/15800110032.jpg","mobile":"15800110032","area_code":"86","user_type":"1","height":"0","weight":"0","industry":"","job":"","edu_background":"","school":"","from_platform":"iPhone","user_status":"0","score":"0"}],"count":"33","page_count":2}
	 */
	public function clubMembers() {
		$club_id = $this->objInput->getInt ( 'club_id' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		import ( 'Model.mUser' );
		if ($page_size <= 0)
			$page_size = mUser::USER_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$count = $mUser->getClubMemberCount ( $club_id );
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$members = $mUser->getClubMemberList ( $club_id, $offset, $page_size );
		$result = array ();
		$result ['member_list'] = array_values ( $members );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 俱乐部亲友团成员 URL:/club/user/clubfriendmembers
	 * 俱乐部亲友团成员 请求方式GET 错误代码：{"request" : "/club/user/clubfriendmembers","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int club_id
	 * @param
	 *        	int page 默认1
	 * @param
	 *        	int pagesize 默认是24
	 * @return mixed {"member_list":[{"member_id":"67","uid":"348","club_id":"2","sex":"1","birthday":"93974400","member_time":"1366555334","member_status":"1","is_single":"0","member_level":"2","id":"350","user_id":"348","sexual":"0","city_no":"44020","nickname":"\u5c0f\u9f8d","avatar":"headpics\/2013\/03\/13\/15800110032.jpg","mobile":"15800110032","area_code":"86","user_type":"1","height":"0","weight":"0","industry":"","job":"","edu_background":"","school":"","from_platform":"iPhone","user_status":"0","score":"0"}],"count":"33","page_count":2}
	 */
	public function clubFriendMembers() {
		$club_id = $this->objInput->getInt ( 'club_id' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		import ( 'Model.mUser' );
		if ($page_size <= 0)
			$page_size = mUser::USER_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$count = $mUser->getClubFriendMemberCount ( $club_id );
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$members = $mUser->getClubFriendMemberList ( $club_id, $offset, $page_size );
		$result = array ();
		$result ['member_list'] = array_values ( $members );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
}
