<?php
import ( '@.Control.Command.Command' );
class CheckFrozen extends Command {
	public function onCommand() {
		$mUser = ClsFactory::Create ( 'Model.mUser' );
		if (! $mUser->isLogined ())
			return false;
		$userInfo = $mUser->getCurrentUser ();
		$uid = $userInfo ['client_account'];
		$result = array ();
		$result ['error'] = array (
				'code' => - 1,
				'message' => '系统繁忙' 
		);
		if ($userInfo ['stop_flag'] == 1) {
			$now_date = date ( "Y-m-d" );
			$stop_date = date ( "Y-m-d", strtotime ( $userInfo ['stop_date'] ) );
			if (strtotime ( $now_date ) > strtotime ( $stop_date )) {
				$data = Array (
						'stop_flag' => '0',
						'stop_date' => null,
						'upd_account' => $uid,
						'upd_date' => date ( "Y-m-d H:i:s", time () ) 
				);
				$mUser->modifyUserClientAccount ( $data, $uid );
			} else {
				$result ['data'] ['location'] = '/Tips/stopaccount/stopdate/' . $stop_date;
				$this->errorOut ( $result );
			}
		} elseif ($userInfo ['stop_flag'] == 2) {
			$result ['data'] ['location'] = '/Tips/stopaccountover';
			$this->errorOut ( $result );
		}
		return true;
	}
}
