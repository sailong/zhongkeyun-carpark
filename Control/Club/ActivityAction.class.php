<?php
class ActivityAction extends Controller {
	public function _initialize() {
	}
	public function index() {
		$this->infoList ();
	}
	
	/**
	 * 俱乐部活动列表 URL:/club/activity/infolist
	 * 俱乐部活动列表 请求方式GET 错误代码：{"request" : "/club/activity/infolist","error_code" : "20001","error" : "access_token不合法"}
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
	 * @return mixed {"activity_list":[{"activity_id":"1","club_id":"1","activity_name":"\u4ff1\u4e50\u90e8...0...\u6d3b\u52a8...0","activity_content":"\u5185\u5bb9\uff1aK\u6b4c\u3001\u6e38\u620f\u3001\u5403\u996d.....0","city_no":"11001","activity_address":"\u6d3b\u52a8\u5730\u5740.....0","activity_start_time":"1366583486","activity_end_time":"1366979486","activity_club":"2","activity_sex":"2","activity_min_age":"24","activity_max_age":"40","activity_eb":"3","activity_create_time":"1366619486","activity_boy_num":"30","activity_girl_num":"0","activity_stop_time":"1366637486","activity_status":"1","club_name":"\u4ff1\u4e50\u90e8...0","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...0","club_logo":"http:\/\/apfapi.soulv.com\/media\/headpics\/2013\/03\/13\/13693620656.jpg_r.jpg","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}],"count":"903","page_count":38}
	 */
	public function infoList() {
		import ( 'Model.mActivity' );
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		if ($page_size <= 0)
			$page_size = mActivity::ACTIVITY_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mActivity = ClsFactory::Create ( 'Model.mActivity' );
		$count = $mActivity->getActivityCount ();
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		$activity_info = $mActivity->getActivityList ( $offset, $page_size );
		$result = array ();
		$result ['activity_list'] = array_values ( $activity_info );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 我的活动列表 URL:/club/activity/myactivities
	 * 我的活动列表 请求方式GET 错误代码：{"request" : "/club/activity/myactivities","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @return mixed {"activity_list":[{"activity_id":"1","club_id":"1","activity_name":"\u4ff1\u4e50\u90e8...0...\u6d3b\u52a8...0","activity_content":"\u5185\u5bb9\uff1aK\u6b4c\u3001\u6e38\u620f\u3001\u5403\u996d.....0","city_no":"11001","activity_address":"\u6d3b\u52a8\u5730\u5740.....0","activity_start_time":"1366583486","activity_end_time":"1366979486","activity_club":"2","activity_sex":"2","activity_min_age":"24","activity_max_age":"40","activity_eb":"3","activity_create_time":"1366619486","activity_boy_num":"30","activity_girl_num":"0","activity_stop_time":"1366637486","activity_status":"1","club_name":"\u4ff1\u4e50\u90e8...0","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...0","club_logo":"http:\/\/apfapi.soulv.com\/media\/headpics\/2013\/03\/13\/13693620656.jpg_r.jpg","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}],"count":"903","page_count":38}
	 */
	public function myActivities() {
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		$uid = $mToken->getUidByToken ( $this->objInput->getStr ( 'access_token' ) );
		
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		$activity_info = $mUser->getClubActivityByUid ( $uid );
		$activity_ids = array_keys ( $activity_info );
		import ( 'Model.mActivity' );
		$mActivity = ClsFactory::Create ( 'Model.mActivity' );
		$activities = $mActivity->getActivityById ( $activity_ids );
		$result = array ();
		$result ['activity_list'] = array_values ( $activities );
		$result ['count'] = count ( $activities );
		$result ['page_count'] = 1;
		$this->displayJson ( $result );
	}
	
	/**
	 * 俱乐部活动详细信息 URL:/club/activity/detail
	 * 俱乐部活动详细信息 请求方式GET 错误代码：{"request" : "/club/activity/detail","error_code" : "20001","error" : "access_token不合法"}
	 * 
	 * @param
	 *        	string access_token
	 * @param
	 *        	string from_platform 客户端系统及版本
	 * @param
	 *        	string sv 客户端版本号 如：V1.1.4
	 * @param
	 *        	int activity_id
	 * @return mixed {"activity_id":"100","club_id":"10","activity_name":"\u4ff1\u4e50\u90e8...9...\u6d3b\u52a8...6","activity_content":"\u5185\u5bb9\uff1aK\u6b4c\u3001\u6e38\u620f\u3001\u5403\u996d.....6","city_no":"11001","activity_address":"\u6d3b\u52a8\u5730\u5740.....6","activity_start_time":"1366583989","activity_end_time":"1366979989","activity_club":"2","activity_sex":"2","activity_min_age":"24","activity_max_age":"40","activity_eb":"3","activity_create_time":"1366619989","activity_boy_num":"30","activity_girl_num":"20","activity_stop_time":"1366637989","activity_status":"1","activity_surplus_boy":"0","activity_surplus_girl":"0","club_name":"\u4ff1\u4e50\u90e8...9","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...9","club_logo":"http:\/\/apfapi.soulv.com\/media\/headpics\/2013\/03\/13\/13693620656.jpg_r.jpg","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555335","activity_eb_str":""}
	 */
	public function detail() {
		$activity_id = $this->objInput->getInt ( 'activity_id' );
		if (! $activity_id) {
			$this->displayError ( 20002 );
		}
		$mActivity = ClsFactory::Create ( 'Model.mActivity' );
		$activity_info = $mActivity->getActivityById ( $activity_id );
		$this->displayJson ( $activity_info [$activity_id] );
	}
	
	/**
	 * 某俱乐部活动列表 URL:/club/activity/clubactivityinfo
	 * 俱乐部活动列表 请求方式GET 错误代码：{"request" : "/club/activity/clubactivityinfo","error_code" : "20001","error" : "access_token不合法"}
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
	 * @return mixed {"activity_list":[{"activity_id":"1","club_id":"1","activity_name":"\u4ff1\u4e50\u90e8...0...\u6d3b\u52a8...0","activity_content":"\u5185\u5bb9\uff1aK\u6b4c\u3001\u6e38\u620f\u3001\u5403\u996d.....0","city_no":"11001","activity_address":"\u6d3b\u52a8\u5730\u5740.....0","activity_start_time":"1366583486","activity_end_time":"1366979486","activity_club":"2","activity_sex":"2","activity_min_age":"24","activity_max_age":"40","activity_eb":"3","activity_create_time":"1366619486","activity_boy_num":"30","activity_girl_num":"0","activity_stop_time":"1366637486","activity_status":"1","club_name":"\u4ff1\u4e50\u90e8...0","club_summary":"\u4ff1\u4e50\u90e8\u7b80\u4ecb...0","club_logo":"http:\/\/apfapi.soulv.com\/media\/headpics\/2013\/03\/13\/13693620656.jpg_r.jpg","club_equal_age":"0","club_girl_num":"0","club_boy_num":"0","club_create_time":"1366555334"}],"count":"903","page_count":38}
	 */
	public function clubActivityInfo() {
		import ( 'Model.mActivity' );
		$club_id = $this->objInput->getInt ( 'club_id' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		$page = $this->objInput->getInt ( 'page' );
		$page_size = $this->objInput->getInt ( 'pagesize' );
		if ($page_size <= 0)
			$page_size = mActivity::ACTIVITY_PAGE_SIZE;
		$page = $page <= 0 ? 1 : $page;
		$mActivity = ClsFactory::Create ( 'Model.mActivity' );
		$count = $mActivity->getClubActivityCount ( $club_id );
		$page_count = ceil ( $count / $page_size );
		$page = $page > $page_count ? $page_count : $page;
		$offset = ($page - 1) * $page_size;
		$activity_info = $mActivity->getClubActivityList ( $club_id, $offset, $page_size );
		$result = array ();
		$result ['activity_list'] = array_values ( $activity_info );
		$result ['count'] = $count;
		$result ['page_count'] = $page_count;
		$this->displayJson ( $result );
	}
	
	/**
	 * 申请俱乐部活动 URL:/club/activity/applyactivity
	 * 申请俱乐部活动 请求方式POST 错误代码：{"request" : "/club/activity/applyactivity","error_code" : "20001","error" : "access_token不合法"}
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
	 *        	string activity_name 活动名称
	 * @param
	 *        	string activity_content 活动内容
	 * @param
	 *        	int city_no 城市编码
	 * @param
	 *        	string activity_address 活动地址
	 * @param
	 *        	int activity_start_time 活动开始时间
	 * @param
	 *        	int activity_end_time 活动结束时间
	 * @param
	 *        	string activity_club 活动限定俱乐部
	 * @param
	 *        	int activity_sex 活动限定性别
	 * @param
	 *        	int activity_min_age 活动限定最小年龄
	 * @param
	 *        	int activity_max_age 活动限定最大年龄
	 * @param
	 *        	int activity_eb 活动限定最低学历
	 * @param
	 *        	int activity_boy_num 活动限定男士名额
	 * @param
	 *        	int activity_girl_num 活动限定女士名额
	 * @param
	 *        	int activity_stop_time 活动报名截止时间
	 * @return mixed {"uid":"101","club_id":4,"apply_status":0,"apply_time":1366702219}
	 */
	public function applyActivity() {
		$club_id = $this->objInput->postStr ( 'club_id' );
		$activity_name = $this->objInput->postStr ( 'activity_name' );
		$activity_content = $this->objInput->postStr ( 'activity_content' );
		$city_no = $this->objInput->postInt ( 'city_no' );
		$activity_address = $this->objInput->postStr ( 'activity_address' );
		$activity_start_time = $this->objInput->postInt ( 'activity_start_time' );
		$activity_end_time = $this->objInput->postInt ( 'activity_end_time' );
		$activity_club = $this->objInput->postStr ( 'activity_club' );
		$activity_sex = $this->objInput->postInt ( 'activity_sex' );
		$activity_min_age = $this->objInput->postInt ( 'activity_min_age' );
		$activity_max_age = $this->objInput->postInt ( 'activity_max_age' );
		$activity_eb = $this->objInput->postInt ( 'activity_eb' );
		$activity_boy_num = $this->objInput->postInt ( 'activity_boy_num' );
		$activity_girl_num = $this->objInput->postInt ( 'activity_girl_num' );
		$activity_stop_time = $this->objInput->postInt ( 'activity_stop_time' );
		if (! $club_id) {
			$this->displayError ( 20001 );
		}
		if (! $activity_name) {
			$this->displayError ( 20012 );
		}
		if (! $activity_content) {
			$this->displayError ( 20013 );
		}
		if (! $city_no) {
			$this->displayError ( 20014 );
		}
		if (! $activity_address) {
			$this->displayError ( 20015 );
		}
		if (! $activity_start_time) {
			$this->displayError ( 20016 );
		}
		if (! $activity_end_time) {
			$this->displayError ( 20017 );
		}
		if (! $activity_club) {
			$this->displayError ( 20018 );
		}
		if ($activity_sex == false) {
			$this->displayError ( 20019 );
		}
		if ($activity_min_age == false) {
			$this->displayError ( 20020 );
		}
		if ($activity_max_age == false) {
			$this->displayError ( 20021 );
		}
		if ($activity_eb == false) {
			$this->displayError ( 20022 );
		}
		if ($activity_boy_num == false) {
			$this->displayError ( 20023 );
		}
		if ($activity_girl_num == false) {
			$this->displayError ( 20024 );
		}
		if (! $activity_stop_time) {
			$this->displayError ( 20025 );
		}
		$activity = array (
				"club_id" => $club_id,
				"activity_name" => $activity_name,
				"activity_content" => $activity_content,
				"city_no" => $city_no,
				"activity_address" => $activity_address,
				"activity_start_time" => $activity_start_time,
				"activity_end_time" => $activity_end_time,
				"activity_club" => $activity_club,
				"activity_sex" => $activity_sex,
				"activity_min_age" => $activity_min_age,
				"activity_max_age" => $activity_max_age,
				"activity_eb" => $activity_eb,
				"activity_create_time" => time (),
				"activity_boy_num" => $activity_boy_num,
				"activity_girl_num" => $activity_girl_num,
				"activity_stop_time" => $activity_stop_time,
				"activity_status" => 0 
		);
		$mActivity = ClsFactory::Create ( "Model.mActivity" );
		$activity_id = $mActivity->addClubActivity ( $activity );
		if (! $activity_id) {
			$this->displayError ( 20026 );
		}
		$activity ['activity_id'] = $activity_id;
		$this->displayJson ( $activity );
	}
	private function initData() {
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$club_list = $mClub->getClubInfo ( 0, 100 );
		
		foreach ( $club_list as $v ) {
			for($i = 0; $i < 10; $i ++) {
				$activity = array (
						"club_id" => $v ['club_id'],
						"activity_name" => $v ['club_name'] . '...活动...' . $i,
						"activity_content" => '内容：K歌、游戏、吃饭' . '.....' . $i,
						"city_no" => 11001,
						"activity_address" => '活动地址.....' . $i,
						"activity_start_time" => time () - 10 * 3600,
						"activity_end_time" => time () + 100 * 3600,
						"activity_club" => '2',
						"activity_sex" => 2,
						"activity_min_age" => 24,
						"activity_max_age" => 40,
						"activity_eb" => 3,
						"activity_create_time" => time (),
						"activity_boy_num" => 50,
						"activity_girl_num" => 50,
						"activity_stop_time" => time () + 5 * 3600,
						"activity_status" => 1 
				);
				$mActivity = ClsFactory::Create ( "Model.mActivity" );
				$activity_id = $mActivity->addClubActivity ( $activity );
				// member
				$mUser = ClsFactory::Create ( 'Model.mUser' );
				$uids = range ( 1, 500 );
				$users = $mUser->getUserInfo ( $uids );
				
				$uids = array_rand ( $users, 20 );
				foreach ( $uids as $uid ) {
					$user_info = $users [$uid];
					$user_info ['activity_id'] = $activity_id;
					$user_info ['activity_member_time'] = time ();
					$user_info ['activity_member_status'] = 1;
					$user_info ['activity_join_status'] = 1;
					
					$mUser->addClubActivityMemeber ( $user_info );
				}
			}
		}
	}
}
