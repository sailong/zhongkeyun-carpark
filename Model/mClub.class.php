<?php
class mClub extends mBase {
	const CLUB_INFO_PAGE_SIZE = 24;
	public function setClubMemeberCount($club_id, $field_name, $offset = 1) {
		if (empty ( $field_name ) || empty ( $club_id )) {
			return false;
		}
		$dClub = ClsFactory::Create ( 'Data.dClub' );
		
		return $dClub->setClubMemeberCount ( $club_id, $field_name, $offset );
	}
	public function addClubInfo($club_info) {
		if (empty ( $club_info ))
			return false;
		$dClub = ClsFactory::Create ( 'Data.dClub' );
		
		return $dClub->addClubInfo ( $club_info );
	}
	public function modifyClubInfo($club_info, $club_id) {
		if (empty ( $club_info ) || ! $club_id) {
			return false;
		}
		$dClub = ClsFactory::Create ( 'Data.dClub' );
		
		return $dClub->modifyClubInfo ( $club_info, $club_id );
	}
	public function getClubInfo($offset = 0, $length = 24) {
		$offset = intval ( $offset );
		$length = intval ( $length );
		if (! $length)
			$length = self::CLUB_INFO_PAGE_SIZE;
		
		$mClub = ClsFactory::Create ( 'Data.dClub' );
		$result = $mClub->getClubInfo ( $offset, $length );
		$club_ids = array ();
		foreach ( $result as $v ) {
			$club_ids [] = $v ['club_id'];
		}
		$mActivity = ClsFactory::Create ( 'Model.mActivity' );
		$club_ids = $mActivity->checkClubActiving ( $club_ids );
		foreach ( $result as &$v ) {
			if (in_array ( $v ['club_id'], $club_ids )) {
				$v ['is_activing'] = 1;
			} else {
				$v ['is_activing'] = 0;
			}
			$v ['club_logo'] = 'http://api.club.aipingfang.com/' . $v ['club_logo'];
		}
		return $result;
	}
	public function getClubInfoCount() {
		$mClub = ClsFactory::Create ( 'Data.dClub' );
		$result = $mClub->getClubInfoCount ();
		return $result;
	}
	public function getClubInfoById($club_ids) {
		if (empty ( $club_ids ))
			return false;
		$dClub = ClsFactory::Create ( 'Data.dClub' );
		$result = $dClub->getClubInfoById ( $club_ids );
		foreach ( $result as &$v ) {
			$v ['club_logo'] = 'http://api.club.aipingfang.com/' . $v ['club_logo'];
		}
		return $result;
	}
	
	// club_apply
	public function addClubApply($club_info) {
		if (empty ( $club_info ))
			return false;
		$dClub = ClsFactory::Create ( 'Data.dClub' );
		
		return $dClub->addClubApply ( $club_info );
	}
}

    
