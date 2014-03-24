<?php
class ErrorParse {
	function errorInfo($error_code) {
		$errorInfo = include_once (WEB_ROOT_DIR . '/Config/errorinfo.php');
		$no = substr ( $error_code, 0, 1 );
		$str = $errorInfo [$no] [$error_code];
		if ($str)
			return array (
					'error_code' => $error_code,
					'error' => $str 
			);
		return false;
	}
}
