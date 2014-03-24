<?php
class mToken extends mBase {
	public function getTokenInfo($access_token) {
		if (empty ( $access_token )) {
			return false;
		}
		
		$dUser = ClsFactory::Create ( 'Data.dToken' );
		$token_info = $dUser->getTokenInfo ( $access_token );
		
		return $token_info ? $token_info : false;
	}
	public function getUidByToken($access_token) {
		if (empty ( $access_token )) {
			return false;
		}
		
		$token_info = $this->getTokenInfo ( $access_token );
		
		return $token_info ? $token_info ['user_id'] : false;
	}
	public function checkToken($access_token) {
		if (empty ( $access_token )) {
			return false;
		}
		
		$token_info = $this->getTokenInfo ( $access_token );
		
		return $token_info ? true : false;
	}
}

    
