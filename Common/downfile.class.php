<?php
class downfile {
	function downfile($filename = 'schoolapplication.doc') {
		$old_name = WEB_ROOT_DIR . "/Public/$filename";
		
		$file_name = iconv ( 'UTF-8', 'GB2312', '教育信息化公共服务平台申请表.doc' );
		
		if (! file_exists ( $old_name )) { // 检查文件是否存在
			exit ();
		} else {
			$file = fopen ( $old_name, "r " ); // 打开文件
			                                    
			// 输入文件标签
			Header ( "Content-type:application/octet-stream " );
			Header ( "Accept-Ranges:bytes " );
			Header ( "Accept-Length:" . filesize ( $old_name ) );
			Header ( "Content-Disposition:attachment;filename= " . $file_name );
			// 输出文件内容
			echo fread ( $file, filesize ( $old_name ) );
			fclose ( $file );
			exit ();
		}
	}
}
?>