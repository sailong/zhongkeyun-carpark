<?php
class mActivity extends mBase {
	const ACTIVITY_PAGE_SIZE = 24;
	public function setClubActivityMemeberCount($activity_id, $field_name, $offset = 1) {
		if (empty ( $field_name ) || empty ( $activity_id )) {
			return false;
		}
		$dActivity = ClsFactory::Create ( 'Data.dActivity' );
		
		return $dActivity->setClubActivityMemeberCount ( $activity_id, $field_name, $offset );
	}
	public function addClubActivity($activity_info) {
		if (empty ( $activity_info ))
			return false;
		$dActivity = ClsFactory::Create ( 'Data.dActivity' );
		
		return $dActivity->addClubActivity ( $activity_info );
	}
	public function modifyClubActivity($activity_info, $activity_id) {
		if (empty ( $activity_info ) || ! $activity_id) {
			return false;
		}
		$dActivity = ClsFactory::Create ( 'Data.dActivity' );
		
		return $dActivity->modifyClubActivity ( $activity_info, $activity_id );
	}
	public function getActivityList($offset = 0, $length = 24) {
		$offset = intval ( $offset );
		$length = intval ( $length );
		if (! $length)
			$length = self::CLUB_INFO_PAGE_SIZE;
		
		$mActivity = ClsFactory::Create ( 'Data.dActivity' );
		$activity_list = $mActivity->getActivityList ( $offset, $length );
		$club_ids = array ();
		foreach ( $activity_list as $v ) {
			$club_ids [] = $v ['club_id'];
		}
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$club_info = $mClub->getClubInfoById ( $club_ids );
		
		foreach ( $activity_list as &$v ) {
			$v ['is_expired'] = false;
			if ($v ['activity_end_time'] < time ()) {
				$v ['is_expired'] = true;
			}
			$v = array_merge ( $v, $club_info [$v ['club_id']] );
		}
		
		return $activity_list;
	}
	public function getActivityCount() {
		$dActivity = ClsFactory::Create ( 'Data.dActivity' );
		$result = $dActivity->getActivityCount ();
		return $result;
	}
	public function getClubActivityList($club_id, $offset = 0, $length = 24) {
		$offset = intval ( $offset );
		$length = intval ( $length );
		if (! $length)
			$length = self::CLUB_INFO_PAGE_SIZE;
		if (! is_numeric ( $club_id ))
			return false;
		
		$mActivity = ClsFactory::Create ( 'Data.dActivity' );
		$activity_list = $mActivity->getClubActivityList ( $club_id, $offset, $length );
		
		$club_ids = array ();
		foreach ( $activity_list as $v ) {
			$club_ids [] = $v ['club_id'];
		}
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$club_info = $mClub->getClubInfoById ( $club_ids );
		
		foreach ( $activity_list as &$v ) {
			$v ['is_expired'] = false;
			if ($v ['activity_end_time'] < time ()) {
				$v ['is_expired'] = true;
			}
			$v = array_merge ( $v, $club_info [$v ['club_id']] );
		}
		
		return $activity_list;
	}
	public function checkClubActiving($club_ids) {
		if (empty ( $club_ids ))
			return false;
		$mActivity = ClsFactory::Create ( 'Data.dActivity' );
		$result = $mActivity->checkClubActiving ( $club_ids );
		return $result;
	}
	public function getClubActivityCount($club_id) {
		$dActivity = ClsFactory::Create ( 'Data.dActivity' );
		$result = $dActivity->getClubActivityCount ( $club_id );
		return $result;
	}
	public function getActivityById($activity_ids) {
		if (empty ( $activity_ids ))
			return false;
		$dActivity = ClsFactory::Create ( 'Data.dActivity' );
		$activity_info = $dActivity->getActivityById ( $activity_ids );
		
		$club_ids = array ();
		foreach ( $activity_info as $v ) {
			$club_ids [] = $v ['club_id'];
		}
		$mClub = ClsFactory::Create ( 'Model.mClub' );
		$club_info = $mClub->getClubInfoById ( $club_ids );
		$edu_info = json_decode ( file_get_contents ( 'http://api.aipingfang.com/aipf_server/get_eduction_choices/?bearer_token=' . $_GET ['access_token'] ), true );
		$edu_info = $edu_info ['EDUCATION_CHOICES'];
		
		foreach ( $activity_info as &$v ) {
			$v = array_merge ( $v, $club_info [$v ['club_id']] );
			foreach ( $edu_info as $edu ) {
				if ($edu [0] == $v ['activity_eb']) {
					if ($edu [0] == 5) {
						$v ['activity_eb_str'] = $edu [1];
					} else {
						$v ['activity_eb_str'] = $edu [1] . '及以上';
					}
				}
			}
			if (! strpos ( $v ['activity_club'], '|' ) === false) {
				$club_ids = explode ( '|', $v ['activity_club'] );
				$club_info = $mClub->getClubInfoById ( $club_ids );
				$v ['activity_club_info'] = array_values ( $club_info );
			}
		}
		return $activity_info;
	}
}
