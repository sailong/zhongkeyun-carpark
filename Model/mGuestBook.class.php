<?php
class mGuestBook extends mBase {
	const GUESTBOOK_PAGE_SIZE = 24;
	public function addClubGuestBook($guestbook) {
		if (empty ( $guestbook ))
			return false;
		$dGuestBook = ClsFactory::Create ( 'Data.dGuestBook' );
		return $dGuestBook->addClubGuestBook ( $guestbook );
	}
	public function getClubGuestBookList($club_id, $offset = 0, $length = 24) {
		$offset = intval ( $offset );
		$length = intval ( $length );
		if (! $length)
			$length = self::GUESTBOOK_PAGE_SIZE;
		if (! is_numeric ( $club_id ))
			return false;
		
		$dGuestBook = ClsFactory::Create ( 'Data.dGuestBook' );
		$result = $dGuestBook->getClubGuestBookList ( $club_id, $offset, $length );
		
		return $result;
	}
	public function getClubGuestBookCount($club_id) {
		if (! is_numeric ( $club_id ))
			return false;
		$dGuestBook = ClsFactory::Create ( 'Data.dGuestBook' );
		$result = $dGuestBook->getClubGuestBookCount ( $club_id );
		
		return $result;
	}
	public function addActivityGuestBook($guestbook) {
		if (empty ( $guestbook ))
			return false;
		$dGuestBook = ClsFactory::Create ( 'Data.dGuestBook' );
		return $dGuestBook->addActivityGuestBook ( $guestbook );
	}
	public function getActivityGuestBookList($activity_id, $offset = 0, $length = 24) {
		$offset = intval ( $offset );
		$length = intval ( $length );
		if (! $length)
			$length = self::GUESTBOOK_PAGE_SIZE;
		if (! is_numeric ( $activity_id ))
			return false;
		
		$dGuestBook = ClsFactory::Create ( 'Data.dGuestBook' );
		$result = $dGuestBook->getActivityGuestBookList ( $activity_id, $offset, $length );
		
		return $result;
	}
	public function getActivityGuestBookCount($activity_id) {
		if (! is_numeric ( $activity_id ))
			return false;
		$dGuestBook = ClsFactory::Create ( 'Data.dGuestBook' );
		$result = $dGuestBook->getActivityGuestBookCount ( $activity_id );
		
		return $result;
	}
}
