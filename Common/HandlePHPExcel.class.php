<?php

// PHPExcel操作类
class HandlePHPExcel {
	// 允许处理的文件名后缀
	protected $allow_filesuffix_arr = array (
			'xls',
			'xlsx' 
	);
	protected $memory_limit = 512;
	protected $max_execution_time = 60;
	
	/**
	 * 文件的基本属性信息，包括文件名和文件路径
	 * 
	 * @param
	 *        	$file_attr
	 */
	public function __construct($init_arr = array()) {
		if (isset ( $init_arr ['memory_limit'] )) {
			$this->memory_limit = intval ( $init_arr ['memory_limit'] );
		}
		if (isset ( $init_arr ['max_execution_time'] )) {
			$this->max_execution_time = intval ( $init_arr ['max_execution_time'] );
		}
		$this->_init_env ();
	}
	
	/**
	 * 初始化相应的环境
	 */
	protected function _init_env() {
		// 注册系统默认的自动处理函数
		spl_autoload_register ( "__autoload" );
		include_once WEB_ROOT_DIR . "/Common/PHPExcel.php";
		// 设置内存大小
		$memory_limit = intval ( ini_get ( 'memory_limit' ) );
		if ($memory_limit < $this->memory_limit) {
			ini_set ( 'memory_limit', $this->memory_limit . 'M' );
		}
		// 设置页面的最大执行时间
		$max_execution_time = ini_get ( 'max_execution_time' );
		if ($max_execution_time < $this->max_execution_time) {
			ini_set ( 'max_execution_time', $this->max_execution_time );
		}
	}
	
	/**
	 * 解析和初始化文件的相关信息
	 */
	protected function getFileAttribute($pFileName) {
		if (empty ( $pFileName )) {
			return false;
		}
		
		$pFileName = trim ( $pFileName );
		$file_name = end ( explode ( '/', $pFileName ) );
		$file_path = dirname ( $pFileName );
		
		// 获取excel文件的后缀名和版本信息
		list ( $suffix, $excel_version ) = $this->getExcelSuffixAndVersion ( $file_name );
		
		// 文件的基本属性信息
		$file_attr = array (
				'file_name' => $file_name,
				'file_path' => $file_path,
				'real_file' => $pFileName,
				'suffix' => $suffix,
				'version' => $excel_version 
		);
		
		return $file_attr;
	}
	
	/**
	 * 获取文件的后缀名和excel文件的版本信息
	 * 
	 * @param $file_name excel的文件名        	
	 */
	protected function getExcelSuffixAndVersion($file_name) {
		if (empty ( $file_name )) {
			return false;
		}
		
		$suffix = $excel_version = "";
		if (stripos ( $file_name, '.' ) !== false) {
			$suffix = strtolower ( end ( explode ( '.', $file_name ) ) );
		}
		
		// 检测文件后缀名是否正确
		if (! empty ( $this->allow_filesuffix_arr ) && ! in_array ( $suffix, $this->allow_filesuffix_arr )) {
			throw new Exception ( "文件后缀名错误!", - 1 );
		}
		
		if ($suffix == 'xls') {
			$excel_version = 'Excel5';
		} elseif ($suffix == 'xlsx') {
			$excel_version = 'Excel2007';
		}
		
		return array (
				$suffix,
				$excel_version 
		);
	}
	
	/**
	 * 检测Excel文件是否可读
	 */
	public function canRead($pFileName) {
		if (empty ( $pFileName )) {
			return false;
		}
		
		$canRead = false;
		try {
			$PHPExcel_Reader = PHPExcel_IOFactory::createReaderForFile ( $pFileName );
			$canRead = $PHPExcel_Reader->canRead ( $pFileName );
		} catch ( Exception $e ) {
			$canRead = false;
		}
		
		return $canRead;
	}
	
	/**
	 * 获取excel文件的工作区间的个数
	 * 
	 * @param unknown_type $pFileName        	
	 */
	public function getSheetCount($pFileName) {
		if (empty ( $pFileName )) {
			return false;
		}
		
		$canRead = $this->canRead ( $pFileName );
		if (! $canRead) {
			return false;
		}
		$PHPExcel_Reader = PHPExcel_IOFactory::createReaderForFile ( $pFileName );
		// 按照矩阵的方式处理Excel文件,并将数据转换成相应的php数组
		$PHPExcel = $PHPExcel_Reader->load ( $pFileName );
		$SheetCount = $PHPExcel->getSheetCount ();
		return max ( $SheetCount, 0 );
	}
	
	/**
	 * 以工作区间索引的方式获取excel数据
	 * 
	 * @param
	 *        	$pFileName
	 * @param
	 *        	$index
	 */
	public function getSheetDatasByIndex($pFileName, $index = 0) {
		if (empty ( $pFileName )) {
			return false;
		}
		
		$canRead = $this->canRead ( $pFileName );
		if (! $canRead) {
			return false;
		}
		
		$index = max ( $index, 0 );
		$PHPExcel_Reader = PHPExcel_IOFactory::createReaderForFile ( $pFileName );
		// 按照矩阵的方式处理Excel文件,并将数据转换成相应的php数组
		$PHPExcel = $PHPExcel_Reader->load ( $pFileName );
		$currentSheet = $PHPExcel->getSheet ( $index );
		
		$array = array ();
		if (! empty ( $currentSheet )) {
			$allColumn = PHPExcel_cell::columnIndexFromString ( $currentSheet->getHighestColumn () );
			$allRow = $currentSheet->getHighestRow ();
			$array ["title"] = $currentSheet->getTitle ();
			$array ["cols"] = $allColumn;
			$array ["rows"] = $allRow;
			$arr = array ();
			for($currentRow = 1; $currentRow <= $allRow; $currentRow ++) {
				$row = array ();
				for($currentColumn = 0; $currentColumn < $allColumn; $currentColumn ++) {
					$row [$currentColumn] = $currentSheet->getCellByColumnAndRow ( $currentColumn, $currentRow )->getValue ();
				}
				$arr [$currentRow] = $row;
			}
			
			$array ["datas"] = $arr;
		}
		
		return ! empty ( $array ) ? $array : false;
	}
	
	/**
	 * 将Excel中数据转换成数组
	 */
	public function toArray($pFileName) {
		if (empty ( $pFileName )) {
			return false;
		}
		
		// 检测文件是否可读
		$canRead = $this->canRead ( $pFileName );
		if (! $canRead) {
			return false;
		}
		
		$PHPExcel_Reader = PHPExcel_IOFactory::createReaderForFile ( $pFileName );
		// 按照矩阵的方式处理Excel文件,并将数据转换成相应的php数组
		$PHPExcel = $PHPExcel_Reader->load ( $pFileName );
		$SheetCount = $PHPExcel->getSheetCount ();
		
		$array = array ();
		for($i = 0; $i < $SheetCount; $i ++) {
			$currentSheet = $PHPExcel->getSheet ( $i );
			
			$allColumn = PHPExcel_cell::columnIndexFromString ( $currentSheet->getHighestColumn () );
			$allRow = $currentSheet->getHighestRow ();
			
			$array [$i] ["title"] = $currentSheet->getTitle ();
			$array [$i] ["cols"] = $allColumn;
			$array [$i] ["rows"] = $allRow;
			$arr = array ();
			for($currentRow = 1; $currentRow <= $allRow; $currentRow ++) {
				$row = array ();
				for($currentColumn = 0; $currentColumn < $allColumn; $currentColumn ++) {
					$row [$currentColumn] = $currentSheet->getCellByColumnAndRow ( $currentColumn, $currentRow )->getValue ();
				}
				$arr [$currentRow] = $row;
			}
			
			$array [$i] ["datas"] = $arr;
		}
		
		return ! empty ( $array ) ? $array : false;
	}
	
	/**
	 * 导出Excel文件内容
	 */
	public function export($pFileName, $export_file_name) {
		if (empty ( $pFileName )) {
			return false;
		}
		
		// 检测文件是否可读
		$canRead = $this->canRead ( $pFileName );
		if (! $canRead) {
			return false;
		}
		
		$file_attr = $this->getFileAttribute ( $pFileName );
		$file_name = $export_file_name ? $export_file_name : $file_attr ['file_name'];
		
		ob_start ();
		header ( "Content-Type:application/force-download" );
		header ( "Content-Type:application/octet-stream" );
		header ( "Cache-Control:must-revalidata,post-check=0,pre-check=0" );
		header ( "content-type:application/vnd.ms-excel" );
		header ( "Pragma:no-cache" );
		// 根据不同的浏览器设置文件名称
		$user_agent = $_SERVER ['HTTP_USER_AGENT'];
		if (stripos ( $user_agent, 'MSIE' ) !== false) {
			$encoded_filename = str_replace ( '+', '%20', urlencode ( $file_name ) );
			header ( 'Content-Disposition:attachment;filename="' . $encoded_filename . '"' );
		} elseif (stripos ( $user_agent, 'Firefox' ) !== false) {
			header ( 'Content-Disposition:attachment;filename*="utf8\'\'' . $file_name . '"' );
		} else {
			header ( 'Content-Disposition:attachment;filename="' . $file_name . '"' );
		}
		
		$PHPExcel = new PHPExcel ();
		// 要兼容异常的处理
		$PHPExcel_Reader = PHPExcel_IOFactory::createReaderForFile ( $pFileName );
		$PHPExcel = $PHPExcel_Reader->load ( $pFileName );
		$PHPExce_Writer = PHPExcel_IOFactory::createWriter ( $PHPExcel, $file_attr ['version'] );
		
		$PHPExce_Writer->save ( "PHP://output" );
		ob_end_flush ();
		unset ( $PHPExcel, $PHPExce_Writer );
	}
	
	/**
	 * 将相应的数据保存到Excel文件中
	 * 
	 * @param
	 *        	$dataarr
	 * @param
	 *        	$filename
	 */
	public function saveToExcelFile($datas, $filename) {
		if (empty ( $datas )) {
			return false;
		}
		
		list ( $suffix, $excel_version ) = $this->getExcelSuffixAndVersion ( $filename );
		
		$PHPExcel = new PHPExcel ();
		$PHPExcel_Writer = PHPExcel_IOFactory::createWriter ( $PHPExcel, $excel_version );
		$PHPExcel->removeSheetByIndex ( 0 );
		
		$index = 0;
		foreach ( $datas as $sheet ) {
			$cols = $sheet ['cols'];
			$rows = $sheet ['rows'];
			$title = $sheet ['title'];
			
			$PHPExcel->createSheet ( $index );
			$PHPExcel->setActiveSheetIndex ( $index );
			$objActiveSheet = $PHPExcel->getActiveSheet ();
			$objActiveSheet->setTitle ( $title );
			
			// 设置对应的数据显示的格式
			for($k = 0; $k < $cols; $k ++) {
				$colname = chr ( $k + 65 );
				$objActiveSheet->getStyle ( $colname )->getNumberFormat ()->setFormatCode ( PHPExcel_Style_NumberFormat::FORMAT_NUMBER );
				$objActiveSheet->getColumnDimension ( $colname )->setWidth ( 20 );
			}
			
			// 将对应的数字类型的数据转换成相应的字符串，注意为了防止被科学化表示，字符串前面加空格
			for($i = 1; $i <= $rows; $i ++) {
				for($j = 0; $j < $cols; $j ++) {
					$val = " " . strval ( $sheet ['datas'] [$i] [$j] );
					$objActiveSheet->setCellValueByColumnAndRow ( $j, $i, $val );
				}
			}
			
			$index ++;
		}
		
		$PHPExcel_Writer->save ( $filename );
		return file_exists ( $filename ) ? true : false;
	}
}
?>