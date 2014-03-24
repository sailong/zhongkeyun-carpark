<?php
class LytLib {
	function ThisMoveUrl($url, $second = "0") {
		echo "<meta http-equiv=\"refresh\" content=\"$second; url=$url\">";
		exit ();
	}
	function JavaMoveUrl($Msg, $Url) { // 设置跳转
		echo "<SCRIPT language=javascript>alert('$Msg');";
		if (! $Url)
			echo "history.go(-1);";
		echo "</SCRIPT>";
		if ($Url)
			echo "<meta http-equiv=\"refresh\" content=\"0; url=$Url\">";
		exit ();
	}
	function error_msg($str, $url = "") {
		if ($url == "") {
			$url = "history.go(-1)";
		} elseif ($url == "close") {
			$url = "window.close()";
		} else {
			$url = "document.location.href = '$url'";
		}
		if ($str != "") {
			echo "<script language='javascript'>alert('$str');$url;</script>";
		} else {
			echo "<script language='javascript'>$url;</script>";
		}
		exit ();
	}
	
	/* <br> */
	function make_br($str) {
		$str = str_replace ( "\r\n", "<br>", $str );
		$str = str_replace ( "\n", "<br>", $str );
		return $str;
		// 56595309
	}
	function de_make_br($str) {
		$str = str_replace ( "<br>", "\r\n", $str );
		$str = str_replace ( "<br>", "\n", $str );
		return $str;
	}
	function isnumblank($str) {
		if (eregi ( "[^[:space:]]", $str )) {
			if (is_numeric ( $str )) {
				return 1;
			} else {
				return 0;
			}
		}
		return 0;
	}
	function isblank($str) {
		if (eregi ( "[^[:space:]]", $str )) {
			return 0;
		} else {
			return 1;
		}
		return 0;
	}
	function isnum($str) {
		if (is_numeric ( $str )) {
			return 1;
		} else {
			return 0;
		}
	}
	function isalnum($str) {
		if (eregi ( "[^0-9a-zA-Z\_]", $str )) {
			return 0;
		} else {
			return 1;
		}
	}
	
	// php Output Echo //
	function phpWrite($objechoString) {
		echo $objechoString;
		return phpWrite;
	}
	
	// phpLoctionUrl
	function phpLoctionAlertUrl($alertMsg, $objUrl) {
		echo "<script>alert('$alertMsg');self.location.href='$objUrl';</script>";
		// echo "alert('$alertMsg');self.location.href='$objUrl'";
		return phpLoctionUrl;
	}
	function phpLoctionUrl($objUrl) {
		echo "<script>window.location.href='$objUrl';</script>";
		return phpLoctionUrl;
	}
	// phpSetSession
	function phpSetSession($SessionValue, $objSessionName) {
		$objSessionName = $SessionValue;
		session_register ( $objSessionName );
		return phpSetSession;
	}
	
	// phpGetSession
	function phpGetSession($objSessionName) {
		$objGetSessionValue = $_SESSION [$objSessionName];
		return $objGetSessionValue;
	}
	
	/*
	 * 函数名称：verify_id() 函数作用：校验提交的ID类值是否合法 参 数：$id: 提交的ID值 返 回 值：返回处理后的ID 函数作者：heiyeluren
	 */
	function verify_id($id = null) {
		if (! $id) {
			exit ( '没有提交参数！' );
		} 		// 是否为空判断
		elseif (inject_check ( $id )) {
			exit ( '提交的参数非法！' );
		} 		// 注射判断
		elseif (! is_numeric ( $id )) {
			exit ( '提交的参数非法！' );
		} // 数字判断
		$id = intval ( $id ); // 整型化
		
		return $id;
	}
	
	/*
	 * 函数名称：str_check() 函数作用：对提交的字符串进行过滤 参 数：$var: 要处理的字符串 返 回 值：返回过滤后的字符串 函数作者：heiyeluren
	 */
	function StrFilter($str) {
		if (! get_magic_quotes_gpc ()) 		// 判断magic_quotes_gpc是否打开
		{
			$str = addslashes ( $str ); // 进行过滤
		}
		$str = str_replace ( "_", "\_", $str ); // 把 '_'过滤掉
		$str = str_replace ( "%", "\%", $str ); // 把' % '过滤掉
		return $str;
	}
	
	/*
	 * 函数名称：post_check() 函数作用：对提交的编辑内容进行处理 参 数：$post: 要提交的内容 返 回 值：$post: 返回过滤后的内容 函数作者：heiyeluren
	 */
	function post_Filter($post) {
		if (! get_magic_quotes_gpc ()) 		// 判断magic_quotes_gpc是否为打开
		{
			$post = addslashes ( $post ); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
		}
		// $post = str_replace("_", "\_", $post); // 把 '_'过滤掉
		$post = str_replace ( "%", "\%", $post ); // 把' % '过滤掉
		$post = make_br ( $post ); // 回车转换
		if (is_string ( $post )) {
			$post = htmlspecialchars ( $post ); // html标记转换
		}
		
		return $post;
	}
	function toHtml($str) {
		$str = str_replace ( "<", "&lt;", $str );
		$str = str_replace ( ">", "&gt;", $str );
		$str = str_replace ( "'", "\"", $str );
		$str = str_replace ( "\n", "<br>", $str );
		$str = str_replace ( " ", "&nbsp;", $str );
	}
	function unhtmlspecialchars($string) {
		$string = str_replace ( '&amp;', '&', $string );
		$string = str_replace ( '&#039;', '\'', $string );
		$string = str_replace ( '&quot;', '"', $string );
		$string = str_replace ( '&lt;', '<', $string );
		$string = str_replace ( '&gt;', '>', $string );
		$string = str_replace ( '&uuml;', '', $string );
		$string = str_replace ( '&Uuml;', '', $string );
		$string = str_replace ( '&auml;', '', $string );
		$string = str_replace ( '&Auml;', '', $string );
		$string = str_replace ( '&ouml;', '', $string );
		$string = str_replace ( '&Ouml;', '', $string );
		return $string;
	}
	function dehtml($str) {
		$str = str_replace ( "&lt;", "<", $str );
		$str = str_replace ( "&gt;", ">", $str );
		$str = str_replace ( "\"", "'", $str );
		$str = str_replace ( "<br>", "\n", $str );
		$str = str_replace ( "&nbsp;", " ", $str );
		
		return $str;
	}
	function show_dehtml($str) {
		$str = str_replace ( "&lt;", "<", $str );
		$str = str_replace ( "&gt;", ">", $str );
		$str = str_replace ( "\"", "'", $str );
		// $str = str_replace("<br>", "\n", $str);
		$str = str_replace ( "&nbsp;", " ", $str );
		
		return $str;
	}
	
	// GET 数据验证
	function inUnion_Filter($sql_str) {
		// 进行过滤
		return eregi ( 'select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str );
		
		// if($thisValue==1){echo "error";exit;}
		// return $thisValue;
	}
	
	// QueryParams from form:
	function RequestGetparams($strQueryParamsName) {
		if (inUnion_Filter ( $_REQUEST [$strQueryParamsName] ) == 1) {
			echo "<script>window.location.href='../error.html';</script>";
		} else {
			$objQueryValue = $_REQUEST [$strQueryParamsName];
			return $objQueryValue;
		}
	}
	
	// QueryParams from form:
	function phpRequestQueryString($strQueryParamsName) {
		$objQueryValue = $_REQUEST [$strQueryParamsName];
		return $objQueryValue;
	}
	function phpRequestpost($strQueryParamsName) {
		$objQueryValue = post_Filter ( $_POST [$strQueryParamsName] );
		return $objQueryValue;
	}
	
	// ////////////////////////////////////////////////////////////////////
	
	// 当前日期加N天后的日期
	function phpVarDateAddDay($strDate, $strDateAddDayValue) {
		$strTheDate = date ( "Y-m-d", strtotime ( $strDate ) + $strDateAddDayValue * 86400 );
		return $strTheDate;
	}
	
	// 两日期相减的日期
	function phpVarDatelessDay($strDate, $strDateAddDayValue) {
		$strTheDate = date ( "Y-m-d", strtotime ( $strDate ) - $strDateAddDayValue * 86400 );
		return $strTheDate;
	}
	
	// 两日期相隔的天数
	function diffDay($d1, $d2) {
		$t1 = strtotime ( $d1 );
		$t2 = strtotime ( $d2 );
		$t = $t1 - $t2;
		if ($t < 0)
			$t = $t * (- 1);
		$day = $t / 3600 / 24;
		return $day;
	}
	function phpCurrentDate() {
		$strTheCurrentDate = date ( 'Y-m-d', time () );
		return $strTheCurrentDate;
	}
	function CurrentTime() {
		$strTheCurrentDate = date ( "H:i:s" );
		return $strTheCurrentDate;
	}
	function Getyear() {
		$thisDateNumber = getdate ();
		$thisMonth = $thisDateNumber [year];
		return $thisMonth;
	}
	function GetMonth() {
		$thisDateNumber = getdate ();
		$thisMonth = $thisDateNumber [mon];
		return $thisMonth;
	}
	function GetDay() {
		$thisDateNumber = getdate ();
		$thisMonth = $thisDateNumber [mday];
		return $thisMonth;
	}
	function GetDayweek($DateValue) {
		$week = date ( "D", strtotime ( $DateValue ) );
		switch ($week) {
			case "Mon" :
				$current = "一";
				break;
			case "Tue" :
				$current = "二";
				break;
			case "Wed" :
				$current = "三";
				break;
			case "Thu" :
				$current = "四";
				break;
			case "Fri" :
				$current = "五";
				break;
			case "Sat" :
				$current = "六";
				break;
			case "Sun" :
				$current = "日";
				break;
		}
		return $current;
	}
	function GetDayweek_numShow($DateValue) {
		$week = date ( "D", strtotime ( $DateValue ) );
		switch ($week) {
			case "Mon" :
				$current = "1";
				break;
			case "Tue" :
				$current = "2";
				break;
			case "Wed" :
				$current = "3";
				break;
			case "Thu" :
				$current = "4";
				break;
			case "Fri" :
				$current = "5";
				break;
			case "Sat" :
				$current = "6";
				break;
			case "Sun" :
				$current = "7";
				break;
		}
		return $current;
	}
	function GetDayweekByNum($DateValue) {
		switch ($DateValue) {
			case "1" :
				$current = "一";
				break;
			case "2" :
				$current = "二";
				break;
			case "3" :
				$current = "三";
				break;
			case "4" :
				$current = "四";
				break;
			case "5" :
				$current = "五";
				break;
			case "6" :
				$current = "六";
				break;
			case "7" :
				$current = "日";
				break;
		}
		
		return $current;
	}
	function GetDDxRiqi($DateValue) {
		switch ($DateValue) {
			case "1" :
				$current = "一";
				break;
			case "2" :
				$current = "二";
				break;
			case "3" :
				$current = "三";
				break;
			case "4" :
				$current = "四";
				break;
			case "5" :
				$current = "五";
				break;
			case "6" :
				$current = "六";
				break;
			case "7" :
				$current = "七";
				break;
			case "8" :
				$current = "八";
				break;
			case "9" :
				$current = "九";
				break;
			case "10" :
				$current = "十";
				break;
			case "11" :
				$current = "十一";
				break;
			case "12" :
				$current = "十二";
				break;
		}
		
		return $current;
	}
	function GetCurrentDayweek($DateValue) {
		$week = date ( "l", strtotime ( $DateValue ) );
		switch ($week) {
			case "Monday" :
				$currentweek = "星期一";
				break;
			case "Tuesday" :
				$currentweek = "星期二";
				break;
			case "Wednesday" :
				$currentweek = "星期三";
				break;
			case "Thursday" :
				$currentweek = "星期四";
				break;
			case "Friday" :
				$currentweek = "星期五";
				break;
			case "Saturday" :
				$currentweek = "星期六";
				break;
			case "Sunday" :
				$currentweek = "星期日";
				break;
		}
		
		return $currentweek;
	}
	function ShortGetCurrentDayweek($DateValue) {
		$week = date ( "l", strtotime ( $DateValue ) );
		switch ($week) {
			case "Monday" :
				$currentweek = "一";
				break;
			case "Tuesday" :
				$currentweek = "二";
				break;
			case "Wednesday" :
				$currentweek = "三";
				break;
			case "Thursday" :
				$currentweek = "四";
				break;
			case "Friday" :
				$currentweek = "五";
				break;
			case "Saturday" :
				$currentweek = "六";
				break;
			case "Sunday" :
				$currentweek = "日";
				break;
		}
		
		return $currentweek;
	}
	function GetMonth_Day($DateValue) {
		$GetMonth_DayValue = date ( "t", strtotime ( $DateValue ) );
		return $GetMonth_DayValue;
	}
	
	// 当前日期加一天
	function phpCurrentDateAddDay($strDateAddDayValue) {
		$strTheDate = date ( "Y-m-d", strtotime ( "$strDateAddDayValue day" ) );
		return $strTheDate;
	}
	function phpCurrentTime() {
		$strTempCodeNumber = getdate ();
		$strTempCodeNumber = $strTempCodeNumber [hours] . ":" . $strTempCodeNumber [minutes] . ":" . $strTempCodeNumber [seconds];
		return $strTempCodeNumber;
	}
	function TempCodeNumber() {
		$strTempCodeNumber = getdate ();
		$strTempCodeNumber = $strTempCodeNumber [year] . $strTempCodeNumber [mon] . $strTempCodeNumber [mday] . $strTempCodeNumber [hours] . $strTempCodeNumber [minutes] . $strTempCodeNumber [seconds];
		return $strTempCodeNumber;
	}
	function thisDate() {
		$strTempCodeNumber = getdate ();
		$strTempCodeNumber = $strTempCodeNumber [year] . "年" . $strTempCodeNumber [mon] . "月" . $strTempCodeNumber [mday] . "日";
		return $strTempCodeNumber;
	}
	function TheDateTime() {
		$strTempCodeNumber = date ( 'Y-m-d H:i:s' );
		return $strTempCodeNumber;
	}
	function ReplaceChar($mystring) {
		$findme = "\'|#|;|$|select|delete|update|\%20";
		$splitThisFileName = explode ( "|", $findme );
		for($i = 0; $i < count ( $splitThisFileName ); $i ++) {
			$mystring = str_replace ( "$splitThisFileName[$i]", "", $mystring );
		}
		return $mystring;
	}
	function delhtml($str) { // 清除HTML标签
		$st = - 1; // 开始
		$et = - 1; // 结束
		$stmp = array ();
		$stmp [] = "&nbsp;";
		$len = strlen ( $str );
		for($i = 0; $i < $len; $i ++) {
			$ss = substr ( $str, $i, 1 );
			if (ord ( $ss ) == 60) { // ord("<")==60
				$st = $i;
			}
			if (ord ( $ss ) == 62) { // ord(">")==62
				$et = $i;
				if ($st != - 1) {
					$stmp [] = substr ( $str, $st, $et - $st + 1 );
				}
			}
		}
		$str = str_replace ( $stmp, "", $str );
		return $str;
	}
	function time_tran($the_time) {
		$now_time = date ( "Y-m-d H:i:s" );
		$now_time = strtotime ( $now_time );
		$show_time = strtotime ( $the_time );
		$dur = $now_time - $show_time;
		if ($dur < 60) {
			return $dur . '秒前';
		} else {
			if ($dur < 3600) {
				return floor ( $dur / 60 ) . '分钟前';
			} else {
				if ($dur < 86400) {
					return floor ( $dur / 3600 ) . '小时前';
				} else {
					if ($dur < 259200) { // 3天内
						return floor ( $dur / 86400 ) . '天前';
					} else {
						$the_time = date ( "m-d", strtotime ( $the_time ) );
						return $the_time;
					}
				}
			}
		}
	}
	
	// ///////////////////////////////////////////////////////////////////
	
	// javascript escape 传递解析
	function uniDecode($str, $charcode) {
		$text = preg_replace_callback ( "/%u[0-9A-Za-z]{4}/", toUtf8, $str );
		// return mb_convert_encoding($text, $charcode, 'utf-8');
		return iconv ( $charcode, 'utf-8', $text );
	}
	function toUtf8($ar) {
		foreach ( $ar as $val ) {
			$val = intval ( substr ( $val, 2 ), 16 );
			if ($val < 0x7F) { // 0000-007F
				$c .= chr ( $val );
			} elseif ($val < 0x800) { // 0080-0800
				$c .= chr ( 0xC0 | ($val / 64) );
				$c .= chr ( 0x80 | ($val % 64) );
			} else { // 0800-FFFF
				$c .= chr ( 0xE0 | (($val / 64) / 64) );
				$c .= chr ( 0x80 | (($val / 64) % 64) );
				$c .= chr ( 0x80 | ($val % 64) );
			}
		}
		return $c;
	}
	// ///////////////////////////////////////////////////////////////////
	function Gbcsubstr($str, $start, $len) {
		$strlen = strlen ( $str );
		$tmpstr = '';
		$clen = 0;
		for($i = 0; $i < $strlen; $i ++, $clen ++) {
			if ($clen >= $start + $len)
				break;
			if (ord ( substr ( $str, $i, 1 ) ) > 0xa0) {
				if ($clen >= $start)
					$tmpstr .= substr ( $str, $i, 2 );
				$i ++;
			} else {
				if ($clen >= $start)
					$tmpstr .= substr ( $str, $i, 1 );
			}
		}
		return $tmpstr;
	}
	function msubstr($fStr, $fStart, $fLen, $fCode = "") {
		switch ($fCode) {
			case "UTF-8" :
				preg_match_all ( "/[x01-x7f]|[xc2-xdf][x80-xbf]|xe0[xa0-xbf][x80-xbf]|[xe1-xef][x80-xbf][x80-xbf]|xf0[x90-xbf][x80-xbf][x80-xbf]|[xf1-xf7][x80-xbf][x80-xbf][x80-xbf]/", $fStr, $ar );
				
				if (func_num_args () >= 3) {
					if (count ( $ar [0] ) > $fLen) {
						return join ( "", array_slice ( $ar [0], $fStart, $fLen ) ) . "...";
					}
					return join ( "", array_slice ( $ar [0], $fStart, $fLen ) );
				} else {
					return join ( "", array_slice ( $ar [0], $fStart ) );
				}
				break;
			
			default :
				$fStart = $fStart * 2;
				$fLen = $fLen * 2;
				$strlen = strlen ( $fStr );
				for($i = 0; $i < $strlen; $i ++) {
					if ($i >= $fStart && $i < ($fStart + $fLen)) {
						if (ord ( substr ( $fStr, $i, 1 ) ) > 129)
							$tmpstr .= substr ( $fStr, $i, 2 );
						else
							$tmpstr .= substr ( $fStr, $i, 1 );
					}
					if (ord ( substr ( $fStr, $i, 1 ) ) > 129)
						$i ++;
				}
				if (strlen ( $tmpstr ) < $strlen)
					$tmpstr .= "...";
				Return $tmpstr;
		}
	}
	function getrandomnum($n) {
		$dataset = array (
				0,
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9 
		);
		$randomNO = implode ( '', array_rand ( $dataset, $n ) );
		return $randomNO;
	}
	function get_rand_code($code_length, $code_mode) {
		if (is_numeric ( $code_length ) && ! empty ( $code_mode )) {
			switch ($code_mode) {
				case '1' :
					$str = '1234567890';
					break;
				case '2' :
					$str = 'abcdefghijklmnopqrstuvwxyz';
					break;
				case '3' :
					$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case '4' :
					$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
					break;
				case '5' :
					$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
					break;
				case '6' :
					$str = 'abcdefghijklmnopqrstuvwxyz1234567890';
					break;
				default :
					$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
					break;
			}
			$result = '';
			$str_length = strlen ( $str ) - 1;
			for($i = 0; $i < $code_length; $i ++) {
				$num = mt_rand ( 0, $str_length );
				$result .= $str [$num];
			}
			return $result;
		} else {
			return false;
		}
	}
}

?>