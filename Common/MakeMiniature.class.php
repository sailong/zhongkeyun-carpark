<?php
/** *instruction： this class is to create small photo，display page and create file
*version 1.0
*@author sanshi(三石)
*QQ: 35047205
*MSN: sanshi0815@tom.com
*Create 2005/6/18
*******************************************************
*@param string $srcFile 源文件 
*@param string $dstFile 目标文件
*@param string $fileType 当前文件类型
*@param string $im 图片打开资源句柄
*@param array $imgType 文件类型定义
*/
class MakeMiniature {
	var $srcFile; // 源文件
	var $dstFile; // 目标文件
	var $fileType; // 文件类型
	var $im; // 图片打开资源句柄
	var $imgType = array (
			"jpg", // 文件类型定义
			"gif",
			"png",
			"bmp" 
	);
	/**
	 * *说明： 获取文件类型
	 * 
	 * @param string $fileName
	 *        	文件名
	 * @return boolean 符合return true
	 *        
	 */
	function findType($fileName) {
		if ($type = substr ( $fileName, strrpos ( $fileName, "." ) )) {
			$type = substr ( $type, 1 );
			if (! strstr ( $type, "." )) {
				$var = strtolower ( $type );
			} else {
				echo "file type error11!";
			}
		} else {
			echo "file type error22!";
		}
		$imgType = $this->imgType;
		if (strtolower ( $imgType [0] ) == $var) {
			$this->fileType = $var;
			return true;
		}
		if (strtolower ( $imgType [1] ) == $var) {
			$this->fileType = $var;
			return true;
		}
		if (strtolower ( $imgType [2] ) == $var) {
			$this->fileType = $var;
			return true;
		}
		if (strtolower ( $imgType [3] ) == $var) {
			$this->fileType = $var;
			return true;
		}
		return false;
		
		// for($i=0;$i<=count($this->imgType);$i++)
		// {
		// print_r($this->imgType);
		// if(Strcmp($this->imgType[$i],strtolower($var))==0)
		// {
		// $this->fileType=$var;
		// return true;
		// }else{
		// return false;
		// }
		// }
	}
	/**
	 *
	 * @param $fileType 文件类型        	
	 * @return resource 打开图片的资源句柄
	 *        
	 */
	function loadImg($fileType) {
		$type = $this->isNull ( $fileType );
		
		switch (strtolower ( $type )) {
			case "jpg" :
				$im = ImageCreateFromjpeg ( $this->srcFile );
				break;
			case "gif" :
				$im = ImageCreateFromGIF ( $this->srcFile );
				break;
			case "png" :
				$im = imagecreatefrompng ( $this->srcFile );
				break;
			case "bmp" :
				$im = imagecreatefromwbmp ( $this->srcFile );
				break;
			default :
				
				$im = 0;
				echo "not you input file type!1";
				
				break;
		}
		$this->im = $im;
		return $im;
	}
	/**
	 * *说明： if $var is not null，then return $var
	 */
	function isNull($var) {
		if (! isset ( $var ) || empty ( $var )) {
			echo "变量值为null！";
			exit ( 0 );
		}
		
		return $var;
	}
	/**
	 * *说明： 设置源文件名和生成文件名，同时完成了文件类型的确定
	 * 还有对文件的打开
	 * 
	 * @param
	 *        	string srcFile 目标文件
	 * @param
	 *        	String dstFile 建立文件
	 *        	
	 */
	function setParam($srcFile, $dstFile) {
		$this->srcFile = $this->isNull ( $srcFile );
		$this->dstFile = $this->isNull ( $dstFile );
		if (! $this->findType ( $srcFile )) {
			echo "file type error33!";
		}
		if (! $this->loadImg ( $this->fileType )) {
			echo "open " . $this->srcFile . "error!";
		}
	}
	/**
	 * *说明 取得图像宽度
	 * 
	 * @param
	 *        	resource im 打开图像资源
	 * @return int width 图像宽度
	 *        
	 */
	function getImgWidth($im) {
		$im = $this->isNull ( $im );
		$width = imagesx ( $im );
		return $width;
	}
	/**
	 * *说明 取得图像高度
	 * 
	 * @param
	 *        	resource im 打开图像资源
	 * @return int height 图像高度
	 *        
	 */
	function getImgHeight($im) {
		$im = $this->isNull ( $im );
		$height = imagesy ( $im );
		return $height;
	}
	/**
	 * *说明 建立图像
	 * 
	 * @param
	 *        	resource im 打开图像资源
	 * @param
	 *        	int scale 生成图像与原图像的比例是百分比
	 * @param
	 *        	boolean page 是否输出到页面
	 *        	
	 */
	function createImg($im, $scale, $page) {
		$im = $this->isNull ( $im );
		$scale = $this->isNull ( $scale );
		$srcW = $this->getImgWidth ( $im );
		$srcH = $this->getImgHeight ( $im );
		$detW = round ( $srcW * $scale / 100 );
		$detH = round ( $srcH * $scale / 100 );
		// $om=ImageCreate($detW,$detH);//普通的使用
		$om = imagecreatetruecolor ( $detW, $detH ); // 真色彩对gd库有要求
		                                       // ImageCopyResized($om,$im,0,0,0,0,$detW,$detH,$srcW,$srcH);
		imagecopyresampled ( $om, $im, 0, 0, 0, 0, $detW, $detH, $srcW, $srcH );
		$this->showImg ( $om, $this->fileType, $page );
	}
	/**
	 * *说明 建立图像
	 * 
	 * @param
	 *        	resource im 打开的图像资源
	 * @param
	 *        	int scale 生成图像与源图像的比例是百分比
	 * @param
	 *        	boolean page 是否输出到页面
	 * @param
	 *        	bolean delOriginalImg
	 *        	
	 */
	function createNewImg($im, $width, $height, $page, $type = 1) {
		$im = $this->isNull ( $im );
		// $scale=$this->isNull($scale);
		$srcW = $this->getImgWidth ( $im );
		$srcH = $this->getImgHeight ( $im );
		$detW = $this->isNull ( $width );
		$detH = $this->isNull ( $height );
		
		// 改变后的故乡比例
		$resize_ratio = ($detW) / ($detH);
		// 实际图像比例
		$ratio = $srcW / $srcH;
		
		if ($type == 1) {
			if (($srcW / $detW) >= ($srcH / $detH)) {
				$temp_height = $srcW / $resize_ratio;
				$temp_width = $srcW;
				$src_X = 0;
				$src_Y = abs ( ($srcH - $temp_height) / 2 );
			} else {
				$temp_width = $srcH * $resize_ratio;
				$temp_height = $srcH;
				$src_X = abs ( ($srcW - $temp_width) / 2 );
				$src_Y = 0;
			}
			
			$om1 = imagecreatetruecolor ( $temp_width, $temp_height );
			$white = imagecolorallocate ( $om1, 255, 255, 255 );
			imagefilledrectangle ( $om1, 0, 0, $temp_width, $temp_height, $white );
			imagecopyresampled ( $om1, $im, $src_X, $src_Y, 0, 0, $srcW, $srcH, $srcW, $srcH );
			$om = imagecreatetruecolor ( $detW, $detH );
			$white = imagecolorallocate ( $om, 255, 255, 255 );
			imagefilledrectangle ( $om, 0, 0, $detW, $detH, $white );
			imagecopyresampled ( $om, $om1, 0, 0, 0, 0, $detW, $detH, $temp_width, $temp_height );
			$this->showImg ( $om, $this->fileType, $page );
		} else {
			
			// asdasdasdasdasd
			if (($srcW / $detW) >= ($srcH / $detH)) {
				$temp_height = $height;
				$temp_width = $srcW / ($srcH / $height);
				$src_X = abs ( ($width - $temp_width) / 2 );
				$src_Y = 0;
			} else {
				$temp_width = $width;
				$temp_height = $srcH / ($srcW / $width);
				$src_X = 0;
				$src_Y = abs ( ($height - $temp_height) / 2 );
			}
			
			// 实际图像比例大于改变后的图像比例
			if ($ratio >= $resize_ratio)
			//高度优先
			{) 

				$start = ($srcW - $detW) / 2;
				$om = imagecreatetruecolor ( $detW, $detH ); // 真色彩对gd库有要求
				imagecopyresampled ( $om, $im, 0, 0, 0, 0, $detW, $detH, ($srcH * $resize_ratio), $srcH );
				$this->showImg ( $om, $this->fileType, $page );
			}
			if ($ratio < $resize_ratio)
			//宽度优先
			{) 

				$start = ($srcH - $detH) / 2;
				$om = imagecreatetruecolor ( $detW, $detH ); // 真色彩对gd库有要求
				imagecopyresampled ( $om, $im, 0, 0, 0, 0, $detW, $detH, $srcW, $srcW / $resize_ratio );
				$this->showImg ( $om, $this->fileType, $page );
			}
		}
		
		// $om=imagecreatetruecolor($detW,$detH);//真色彩对gd库有要求
		// imagecopyresampled($om,$im,0,0,0,0,$detW,$detH,$srcW,$srcH);
		// $this->showImg($om,$this->fileType,$page);
	}
	/**
	 * *说明 输出图像建立失败的提示
	 * 
	 * @param
	 *        	boolean boolean 判断是否输出
	 *        	
	 */
	function inputError($boolean) {
		if (! $boolean) {
			echo "img input error!
";
		}
	}
	/**
	 * *说明 根据条件显示图片输出位置和类型
	 * 
	 * @param resource $om
	 *        	图像输出的资源
	 * @param String $type
	 *        	输出图像的类型，现在使用的图像的类型
	 * @param boolean $page
	 *        	是否在页面上显示
	 *        	
	 */
	function showImg($om, $type, $page) {
		$om = $this->isNull ( $om );
		$type = $this->isNull ( $type );
		switch (strtolower ( $type )) {
			case "jpg" :
				if ($page) {
					$suc = imagejpeg ( $om );
					$this->inputError ( $suc );
				} else {
					$suc = imagejpeg ( $om, $this->dstFile );
					$this->inputError ( $suc );
				}
				break;
			case "gif" :
				if ($page) {
					$suc = imagegif ( $om );
					$this->inputError ( $suc );
				} else {
					$suc = imagegif ( $om, $this->dstFile );
					$this->inputError ( $suc );
				}
				break;
			case "png" :
				if ($page) {
					$suc = imagepng ( $om );
					$this->inputError ( $suc );
				} else {
					$suc = imagepng ( $om, $this->dstFile );
					$this->inputError ( $suc );
				}
				break;
			case "bmp" :
				if ($page) {
					$suc = imagewbmp ( $om );
					$this->inputError ( $suc );
				} else {
					$suc = imagewbmp ( $om, $this->dstFile );
					$this->inputError ( $suc );
				}
				break;
			default :
				echo "not you input file type2!
";
				break;
		}
	}
}
/**使用
$file=new MakeMiniature();
$file->setParam("img/Logo.jpg","img/Logo1.jpg");//设置原文件，跟生成文件
$file->createImg($file->im,200,true);//按比例生成图像，比例为200%，在页面上显示
$file->createImg($file->im,200,false);//按比例生成图像，比例为200%，生成图片保存到上面设置的目录和路径
$file->createNewImg($file->im,100,100,true);//按照自己设置的长宽创建图像，保存或显示在页面
*/