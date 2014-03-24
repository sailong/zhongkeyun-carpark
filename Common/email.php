<?php
function Sendmails($emailaddress, $emailcontent) {
	import ( 'phpmailer', './Public/phpmailer/' );
	
	$mail = new PHPMailer ();
	// 设置PHPMailer使用SMTP服务器发送Email
	$mail->IsSMTP ();
	
	// 设置邮件的字符编码，若不指定，则为'UTF-8'
	$mail->CharSet = 'UTF-8';
	
	// 添加收件人地址，可以多次使用来添加多个收件人
	$mail->AddAddress ( $emailaddress );
	
	// 设置邮件正文
	$mail->Body = $emailcontent;
	
	// 设置邮件头的From字段。
	// 对于网易的SMTP服务，这部分必须和你的实际账号相同，否则会验证出错。
	$mail->From = 'service@wmw.cn';
	
	// 设置发件人名字
	$mail->FromName = '我们网';
	
	// 设置邮件标题
	$mail->Subject = '我们网为您服务';
	
	// 设置SMTP服务器。这里使用网易的SMTP服务器。
	$mail->Host = 'smtp.exmail.qq.com';
	
	// 设置为"需要验证"
	$mail->SMTPAuth = true;
	
	// 设置用户名和密码，即网易邮件的用户名和密码。
	$mail->Username = 'service@wmw.cn';
	$mail->Password = 'zhonghai';
	
	// 发送邮件
	$send = $mail->Send ();
	if ($send) {
		return true;
	} else {
		return false;
	}
}
?>