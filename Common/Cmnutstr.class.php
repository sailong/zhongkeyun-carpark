<?php
class Cmnutstr {
	/**
	 * 获取汉字首字母
	 * 
	 * @param
	 *        	汉字
	 * @return 首字母大写
	 * @author madubin
	 *         2011-5-6 add
	 */
	public static function getfirstchar($s0) {
		// $s0的编码为utf-8
		// example: echo getfirstchar(“北”);
		$fchar = ord ( $s0 {0} );
		if ($fchar >= ord ( "A" ) and $fchar <= ord ( "z" ))
			return strtoupper ( $s0 {0} );
		if ($s = iconv ( "UTF-8", "gb2312//IGNORE", $s0 )) {
			$asc = ord ( $s {0} ) * 256 + ord ( $s {1} ) - 65536;
			if ($asc >= - 20319 and $asc <= - 20284)
				return "A";
			if ($asc >= - 20283 and $asc <= - 19776)
				return "B";
			if ($asc >= - 19775 and $asc <= - 19219)
				return "C";
			if ($asc >= - 19218 and $asc <= - 18711)
				return "D";
			if ($asc >= - 18710 and $asc <= - 18527)
				return "E";
			if ($asc >= - 18526 and $asc <= - 18240)
				return "F";
			if ($asc >= - 18239 and $asc <= - 17923)
				return "G";
			if ($asc >= - 17922 and $asc <= - 17418)
				return "I";
			if ($asc >= - 17417 and $asc <= - 16475)
				return "J";
			if ($asc >= - 16474 and $asc <= - 16213)
				return "K";
			if ($asc >= - 16212 and $asc <= - 15641)
				return "L";
			if ($asc >= - 15640 and $asc <= - 15166)
				return "M";
			if ($asc >= - 15165 and $asc <= - 14923)
				return "N";
			if ($asc >= - 14922 and $asc <= - 14915)
				return "O";
			if ($asc >= - 14914 and $asc <= - 14631)
				return "P";
			if ($asc >= - 14630 and $asc <= - 14150)
				return "Q";
			if ($asc >= - 14149 and $asc <= - 14091)
				return "R";
			if ($asc >= - 14090 and $asc <= - 13319)
				return "S";
			if ($asc >= - 13318 and $asc <= - 12839)
				return "T";
			if ($asc >= - 12838 and $asc <= - 12557)
				return "W";
			if ($asc >= - 12556 and $asc <= - 11848)
				return "X";
			if ($asc >= - 11847 and $asc <= - 11056)
				return "Y";
			if ($asc >= - 11055 and $asc <= - 10247)
				return "Z";
		}
		return "~";
	}
	
	// 系统发号器
	public static function createCount($num1) {
		if ($num1 <= 0) {
			$num = 8;
		} else {
			$num = $num1 - 1;
		}
		$connt = 0;
		while ( $connt < $num ) {
			$a [] = mt_rand ( 0, 9 ); // 产生随机数
			$connt = count ( $a );
		}
		foreach ( $a as $key => $value ) {
			$val .= $value;
		}
		$one = mt_rand ( 1, 9 );
		$str = $one . $val;
		return $str;
	}
	
	/*
	 * 获取用户头像显示路径 $photoname 数据库存储的头像名称 $account 用户账号 @return 头像显示路径 @author madubin 2011-6-1
	 */
	public static function getphotourl($photoname, $account) {
		return '/' . UPLOAD_HEAD . '/' . $account . '/' . $photoname;
	}
	public static function getpfaceico($icoparams) {
		return '/' . UPLOAD_FACEICO . $icoparams;
	}
	function formatedateparams($the_time) {
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
						$the_time = date ( "Y-m-d H:m:s", strtotime ( $the_time ) );
						// $the_time = $the_time;
						return $the_time;
					}
				}
			}
		}
	}
	public static function getfacelist() {
		$talk_facelist = array (
				0 => "/惊讶",
				1 => "/撇嘴",
				2 => "/色",
				3 => "/发呆",
				4 => "/得意",
				5 => "/大哭",
				6 => "/害羞",
				7 => "/闭嘴",
				8 => "/睡",
				9 => "/流泪",
				10 => "/尴尬",
				11 => "/发怒",
				12 => "/调皮",
				13 => "/呲牙",
				14 => "/微笑",
				15 => "/难过",
				16 => "/酷",
				17 => "/冷汗",
				18 => "/抓狂",
				19 => "/吐",
				20 => "/偷笑",
				21 => "/可爱",
				22 => "/白眼",
				23 => "/傲慢",
				24 => "/饥饿",
				25 => "/困",
				26 => "/惊恐",
				27 => "/流汗",
				28 => "/憨笑",
				29 => "/大兵",
				30 => "/奋斗",
				31 => "/咒骂",
				32 => "/疑问",
				33 => "/嘘",
				34 => "/晕",
				35 => "/折磨",
				36 => "/衰",
				37 => "/骷髅",
				38 => "/敲打",
				39 => "/再见",
				40 => "/擦汗",
				41 => "/抠鼻",
				42 => "/鼓掌",
				43 => "/糗大了",
				44 => "/坏笑",
				
				45 => "/左哼哼",
				46 => "/右哼哼",
				47 => "/哈欠",
				48 => "/鄙视",
				49 => "/委屈",
				50 => "/快哭了",
				51 => "/阴险",
				52 => "/亲亲",
				53 => "/吓",
				54 => "/可怜",
				55 => "/菜刀",
				56 => "/西瓜",
				57 => "/啤酒",
				58 => "/篮球",
				59 => "/乒乓" 
		);
		
		return $talk_facelist;
	}
	
	/*
	 * 获取用户相册封面路径 $photoname 数据库存储的相册封面名称 $account 用户账号 @return 相册封面显示路径 @author madubin 2011-6-8
	 */
	public static function getxcurl($xcname, $account) {
		return '/' . UPLOAD_PHOTO . '/' . $account . '/' . $xcname;
	}
	
	/*
	 * 图片裁切主函数 $thumb_image_name 裁剪后的图片 $image 裁剪前的图片 $width 裁剪的图片宽度 $height 裁剪的图片高度 $start_width	距离左边框的宽度 $start_height 距离上边框的高度 $scale	裁剪后的图片压缩比 @return 裁剪后的图片 @author madubin 2011-6-1
	 */
	public static function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale) {
		list ( $imagewidth, $imageheight, $imageType ) = getimagesize ( $image );
		$imageType = image_type_to_mime_type ( $imageType );
		
		$newImageWidth = ceil ( $width * $scale );
		$newImageHeight = ceil ( $height * $scale );
		$newImage = imagecreatetruecolor ( $newImageWidth, $newImageHeight );
		switch ($imageType) {
			case "image/gif" :
				$source = imagecreatefromgif ( $image );
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				$source = imagecreatefromjpeg ( $image );
				break;
			case "image/png" :
			case "image/x-png" :
				$source = imagecreatefrompng ( $image );
				break;
		}
		imagecopyresampled ( $newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height );
		switch ($imageType) {
			case "image/gif" :
				imagegif ( $newImage, $thumb_image_name );
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				imagejpeg ( $newImage, $thumb_image_name, 90 );
				break;
			case "image/png" :
			case "image/x-png" :
				imagepng ( $newImage, $thumb_image_name );
				break;
		}
		chmod ( $thumb_image_name, 0777 );
		return $thumb_image_name;
	}
	
	/*
	 * 按照规定的图片宽度重新调整上传图片 $image 上传的图片 $width 图片原始宽度 $height 图片原始高度 $scale 上传后需要调整的图片宽度比 @return 调整后的图片 @author madubin 2011-6-1
	 */
	public static function resizeImage($image, $width, $height, $scale) {
		list ( $imagewidth, $imageheight, $imageType ) = getimagesize ( $image );
		$imageType = image_type_to_mime_type ( $imageType );
		$newImageWidth = ceil ( $width * $scale );
		$newImageHeight = ceil ( $height * $scale );
		$newImage = imagecreatetruecolor ( $newImageWidth, $newImageHeight );
		switch ($imageType) {
			case "image/gif" :
				$source = imagecreatefromgif ( $image );
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				$source = imagecreatefromjpeg ( $image );
				break;
			case "image/png" :
			case "image/x-png" :
				$source = imagecreatefrompng ( $image );
				break;
		}
		imagecopyresampled ( $newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height );
		
		switch ($imageType) {
			case "image/gif" :
				imagegif ( $newImage, $image );
				break;
			case "image/pjpeg" :
			case "image/jpeg" :
			case "image/jpg" :
				imagejpeg ( $newImage, $image, 90 );
				break;
			case "image/png" :
			case "image/x-png" :
				imagepng ( $newImage, $image );
				break;
		}
		
		chmod ( $image, 0777 );
		return $image;
	}
	
	/*
	 * 获取图片高度 $image 图片路径 @return 图片高度 @author madubin 2011-6-1
	 */
	public static function getHeight($image) {
		$size = getimagesize ( $image );
		$height = $size [1];
		return $height;
	}
	
	/*
	 * 获取图片宽度 $image 图片路径 @return 图片宽度 @author madubin 2011-6-1
	 */
	public static function getWidth($image) {
		$size = getimagesize ( $image );
		$width = $size [0];
		return $width;
	}
	
	/*
	 * 删除文件夹中的所有文件,保留传入的文件$large和$thumb $path 文件夹路径 $large 原始图片 $thumb 裁剪图片 @author madubin 2011-6-1
	 */
	public static function clear_files($path, $large, $thumb) {
		$large = substr ( $large, strrpos ( $large, '/' ) + 1 );
		$thumb = substr ( $thumb, strrpos ( $thumb, '/' ) + 1 );
		
		foreach ( glob ( $path . '\*' ) as $item ) {
			$photoname = substr ( $item, strrpos ( $item, '\\' ) + 1 );
			if ($photoname != $large && $photoname != $thumb) {
				unlink ( $item );
			}
		}
	}
	
	/*
	 * 删除文件夹中的所有文件 $path 文件夹路径 $delstr 要删除的文件名称字符串 @author madubin 2011-6-1
	 */
	public static function clear_all_files($path, $delstr) {
		if ($delstr != "") {
			foreach ( glob ( $path . '\*' ) as $item ) {
				$photoname = substr ( $item, strrpos ( $item, '\\' ) + 1 );
				if (strpos ( $delstr, $photoname )) {
					unlink ( $item );
				}
			}
		} else {
			foreach ( glob ( $path . '\*' ) as $item ) {
				unlink ( $item );
			}
		}
	}
	public static function clear_work_files($path, $delstr) {
		unlink ( "/" . $path . "/" . $delstr );
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
	function successTips($Msg, $url, $tiptime, $msgType) {
		switch ($msgType) {
			case "success" :
				$pathImg = "/Public/images/new/success.gif";
				break;
			case "error" :
				$pathImg = "/Public/images/new/error.jpg";
				break;
		}
		
		echo "<meta http-equiv=\"refresh\" content=\"$tiptime; url=$url\">";
		echo "<style>";
		echo "body{font-size:12px;}
				#notice { margin: 200px auto 0; background: #FFF; border-style: solid; border-color: #86B9D6 #B2C9D3 #B2C9D3; border-width: 4px 1px 1px;font-size:12px; }
				#notice_message { padding: 1.5em 1em; font-size: 1.17em; font-size:12px;}
				#notice_message.warning { color:red; }
				#notice_links { margin: 0; line-height: 2em; border-top: 1px solid #F5F5F5; background: #F5FBFF; padding: 0 1em; }
				#notice_links a { margin: 0 2px;color:black; }";
		echo "</style>";
		echo "<center>";
		echo "<table id='notice' cellpadding='0' cellspacing='0' border='0'>";
		echo "<tr>";
		echo "<td id='notice_message' class='warning'><img src='" . $pathImg . "'></td>";
		echo "<td id='notice_message' class='warning'><span style='font-size:18px;color:#9C0D3F;font-weight:bod;'>" . $Msg . "</span></td>";
		echo "</tr>";
		echo "</table>";
		echo "<center/>";
	}
	function urlencode_js($str) {
		$str_len = strlen ( $str );
		
		$new = array ();
		for($i = 0; $i < $str_len; $i ++) {
			$ch = $str [$i];
			if (strpos ( "#$&+,/:;=?@", $ch ) !== FALSE) {
				$new [] = $ch;
			} else {
				$new [] = urlencode ( $ch );
			}
		}
		
		return implode ( "", $new );
	}
	function toHtml($str) {
		$str = str_replace ( "<", "&lt;", $str );
		$str = str_replace ( ">", "&gt;", $str );
		$str = str_replace ( "'", "\"", $str );
		$str = str_replace ( "\n", "<br>", $str );
		$str = str_replace ( " ", "&nbsp;", $str );
	}
	function toStr($str) {
		$str = str_replace ( "&lt;", "<", $str );
		$str = str_replace ( "&gt;", ">", $str );
		$str = str_replace ( "\"", "'", $str );
		$str = str_replace ( "<br>", "\n", $str );
		$str = str_replace ( "&nbsp;", " ", $str );
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
	function unhtmlspecialchars($string) {
		$string = str_replace ( '&amp;', '&', $string );
		$string = str_replace ( 'amp;', '', $string );
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
	function getNumsUppercase($intNums) {
		switch ($intNums) {
			case 1 :
				$outStr = "一";
				break;
			case 2 :
				$outStr = "二";
				break;
			case 3 :
				$outStr = "三";
				break;
			case 4 :
				$outStr = "四";
				break;
			case 5 :
				$outStr = "五";
				break;
			case 6 :
				$outStr = "六";
				break;
			case 7 :
				$outStr = "七";
				break;
			case 8 :
				$outStr = "八";
				break;
			case 9 :
				$outStr = "九";
				break;
			case 10 :
				$outStr = "十";
				break;
		}
		return $outStr;
	}
}
?>