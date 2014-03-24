<?php
class LytUpLoad {
	/*
	 * $waterPos 水印位置，: 0为随机位置； 1为顶端居左，2为顶端居中，3为顶端居右； 4为中部居左，5为中部居中，6为中部居右； 7为底端居左，8为底端居中，9为底端居右；
	 */
	var $upfile_type, $upfile_size, $upfile_name, $upfile;
	var $d_alt, $extention_list, $tmp, $arri;
	var $datetime, $date;
	var $filestr, $size, $ext, $check;
	var $flash_directory, $extention, $file_path, $base_directory;
	var $url; // Save Complete Go Url;
	function LytUpLoad() {
		$this->set_url ( "aa.php" );
		$this->set_extention ();
		$this->set_size ( 50 );
		$this->set_date ();
		$this->set_datetime ();
		$this->set_base_directory ( "" );
	}
	function set_file_type($upfile_type) {
		$this->upfile_type = $upfile_type;
	}
	function set_file_name($upfile_name) {
		$this->upfile_name = $upfile_name;
	}
	function set_upfile($upfile) {
		$this->upfile = $upfile;
	}
	function set_file_size($upfile_size) {
		$this->upfile_size = $upfile_size;
	}
	function get_file_size($upfile_size) {
		return $upfile_size;
	}
	function set_url($url) {
		$this->url = $url;
	}
	function get_extention() {
		$this->extention = preg_replace ( '/.*\.(.*[^\.].*)*/iU', '\\1', $this->upfile_name );
	}
	function set_datetime() {
		$this->datetime = date ( "YmdHis" );
	}
	function set_date() {
		$this->date = date ( "Y-m-d" );
	}
	function set_extention() {
		$this->extention_list = "gif|jpg|png|bmp|rar|doc|zip";
	}
	function set_size($size) {
		$this->size = $size;
	}
	function set_base_directory($directory) {
		$this->base_directory = $directory;
	}
	function set_flash_directory() {
		$this->flash_directory = $this->base_directory . "/" . $this->date;
	}
	function showerror($errstr = "上传错误") {
		echo "<script language=javascript>alert('$errstr');location='javascript:history.go(-1);';</script>";
		exit ();
	}
	function go_to($str, $url) {
		echo "<script language='javascript'>alert('$str');location='$url';</script>";
		exit ();
	}
	function mk_base_dir() {
		if (! file_exists ( $this->base_directory )) {
			@mkdir ( $this->base_directory, 0777 );
		}
	}
	function mk_dir() {
		if (! file_exists ( $this->flash_directory )) {
			@mkdir ( $this->flash_directory, 0777 );
		}
	}
	function get_compare_extention() {
		$this->ext = explode ( "|", $this->extention_list );
	}
	function check_extention() {
		for($i = 0; each ( $this->ext ); $i ++) {
			if ($this->ext [$i] == strtolower ( $this->extention )) {
				$this->check = true;
				break;
			}
		}
		if (! $this->check) {
			$this->showerror ( "只可以上传" . $this->extention_list . "其中一种文件类型" );
		}
	}
	function check_size() {
		if ($this->upfile_size > round ( $this->size * 1024 )) {
			$this->showerror ( "文件大小超出限制" . $this->size . "KB" );
		}
	}
	function set_file_path() {
		$seedarray = microtime ();
		$seedstr = split ( " ", $seedarray, 5 );
		$seed = $seedstr [0] * 10000;
		
		srand ( $seed );
		
		$random = rand ( 1, 99999999 );
		$this->file_path = $this->flash_directory . "/" . $this->datetime . $random . "." . $this->extention;
	}
	function copy_file() {
		if (move_uploaded_file ( $this->upfile, $this->file_path )) {
			// $waterImage="sy.gif";//水印图片路径
			// imageWaterMark($this->upfile,5,$waterImage);
		} else {
			print $this->showerror ( "上传文件过程出现错误" );
		}
	}
	function get_file_name() {
		$this->set_flash_directory ();
		$this->get_extention ();
		$this->get_compare_extention ();
		$this->check_extention ();
		$this->check_size ();
		$this->mk_base_dir ();
		$this->mk_dir ();
		$this->set_file_path ();
		
		$ThisFileName = $this->file_path;
		return $ThisFileName;
	}
	function new_get_file_name() {
		$this->set_flash_directory ();
		$this->get_extention ();
		$this->get_compare_extention ();
		$this->check_extention ();
		$this->check_size ();
		$this->mk_base_dir ();
		$this->mk_dir ();
		$this->set_file_path ();
		
		$ThisFileName = $this->file_path;
		return $ThisFileName;
	}
	function executeUpLoad() {
		// $this->get_file_name();
		$this->copy_file ();
	}
	function imageWaterMark($groundImage, $waterPos = 2, $waterImage = "sy.gif", $waterText = "", $textFont = 5, $textColor = "#FF0000") {
		$isWaterImage = FALSE;
		$formatMsg = "图片支持GIF、JPG、PNG格式。";
		
		// 读取水印文件
		if (! empty ( $waterImage ) && file_exists ( $waterImage )) {
			$isWaterImage = TRUE;
			$water_info = getimagesize ( $waterImage );
			$water_w = $water_info [0]; // 取得水印图片的宽
			$water_h = $water_info [1]; // 取得水印图片的高
			
			switch ($water_info [2]) 			// 取得水印图片的格式
			{
				case 1 :
					$water_im = imagecreatefromgif ( $waterImage );
					break;
				case 2 :
					$water_im = imagecreatefromjpeg ( $waterImage );
					break;
				case 3 :
					$water_im = imagecreatefrompng ( $waterImage );
					break;
				default :
					die ( $formatMsg );
			}
		}
		
		// 读取背景图片
		if (! empty ( $groundImage ) && file_exists ( $groundImage )) {
			$ground_info = getimagesize ( $groundImage );
			$ground_w = $ground_info [0]; // 取得背景图片的宽
			$ground_h = $ground_info [1]; // 取得背景图片的高
			
			switch ($ground_info [2]) 			// 取得背景图片的格式
			{
				case 1 :
					$ground_im = imagecreatefromgif ( $groundImage );
					break;
				case 2 :
					$ground_im = imagecreatefromjpeg ( $groundImage );
					break;
				case 3 :
					$ground_im = imagecreatefrompng ( $groundImage );
					break;
				default :
					die ( $formatMsg );
			}
		} else {
			die ( "需要加水印的图片不存在！" );
		}
		
		// 水印位置
		if ($isWaterImage) { // 图片水印
			$w = $water_w;
			$h = $water_h;
			$label = "图片的";
		} else { // 文字水印
			$temp = imagettfbbox ( ceil ( $textFont * 5 ), 0, "./cour.ttf", $waterText ); // 取得使用 TrueType 字体的文本的范围
			$w = $temp [2] - $temp [6];
			$h = $temp [3] - $temp [7];
			unset ( $temp );
			$label = "文字区域";
		}
		
		if (($ground_w < $w) || ($ground_h < $h)) {
			echo "需要加水印的图片的长度或宽度比水印" . $label . "还小，无法生成水印！";
			return;
		}
		
		switch ($waterPos) {
			case 0 : // 随机
				$posX = rand ( 0, ($ground_w - $w) );
				$posY = rand ( 0, ($ground_h - $h) );
				break;
			case 1 : // 1为顶端居左
				$posX = 0;
				$posY = 0;
				break;
			case 2 : // 2为顶端居中
				$posX = ($ground_w - $w) / 2;
				$posY = 0;
				break;
			case 3 : // 3为顶端居右
				$posX = $ground_w - $w;
				$posY = 0;
				break;
			case 4 : // 4为中部居左
				$posX = 0;
				$posY = ($ground_h - $h) / 2;
				break;
			case 5 : // 5为中部居中
				$posX = ($ground_w - $w) / 2;
				$posY = ($ground_h - $h) / 2;
				break;
			case 6 : // 6为中部居右
				$posX = $ground_w - $w;
				$posY = ($ground_h - $h) / 2;
				break;
			case 7 : // 7为底端居左
				$posX = 0;
				$posY = $ground_h - $h;
				break;
			case 8 : // 8为底端居中
				$posX = ($ground_w - $w) / 2;
				$posY = $ground_h - $h;
				break;
			case 9 : // 9为底端居右
				$posX = $ground_w - $w;
				$posY = $ground_h - $h;
				break;
		}
		
		// 设定图像的混色模式
		imagealphablending ( $ground_im, true );
		
		if ($isWaterImage) { // 图片水印
			imagecopy ( $ground_im, $water_im, $posX, $posY, 0, 0, $water_w, $water_h ); // 拷贝水印到目标文件
		} else { // 文字水印
			if (! empty ( $textColor ) && (strlen ( $textColor ) == 7)) {
				$R = hexdec ( substr ( $textColor, 1, 2 ) );
				$G = hexdec ( substr ( $textColor, 3, 2 ) );
				$B = hexdec ( substr ( $textColor, 5 ) );
			} else {
				die ( "水印文字颜色格式不正确！" );
			}
			imagestring ( $ground_im, $textFont, $posX, $posY, $waterText, imagecolorallocate ( $ground_im, $R, $G, $B ) );
		}
		
		// 生成水印后的图片
		@unlink ( $groundImage );
		switch ($ground_info [2]) { // 取得背景图片的格式
			case 1 :
				imagegif ( $ground_im, $groundImage );
				break;
			case 2 :
				imagejpeg ( $ground_im, $groundImage );
				break;
			case 3 :
				imagepng ( $ground_im, $groundImage );
				break;
			default :
				die ( $errorMsg );
		}
		
		// 释放内存
		if (isset ( $water_info ))
			unset ( $water_info );
		if (isset ( $water_im ))
			imagedestroy ( $water_im );
		unset ( $ground_info );
		imagedestroy ( $ground_im );
	}
}
?>