<?php
header ( "Content-Type:text/html; charset=utf-8" );
include_once (dirname ( dirname ( __FILE__ ) ) . '/Daemon.inc.php');
class SendsmsAction extends Controller {
	/*
	 * -------------------------------- 功能:HTTP接口 发送短信 修改日期:	2009-04-08 说明:		http://http.chinasms.com.cn/tx/?uid=用户账号&pwd=MD5位32密码&mobile=号码&content=内容 状态: 100 发送成功 101 验证失败 102 短信不足 103 操作失败 104 非法字符 105 内容过多 106 号码过多 107 频率过快 108 号码内容空 109 账号冻结 110 禁止频繁单条发送 111 系统暂定发送 112 号码不正确 113 连接失败 120 系统升级 --------------------------------
	 */
	
	// 即时发送
	// $res = sendSMS($_uid,$pwd,$mobile,$content);
	// echo $res;
	
	// 定时发送
	/*
	 * $time = '2010-05-27 12:11'; $res = SendsmsAction.class($uid,$pwd,$mobile,$content,$time); echo $res;
	 */
	public function _initialize() {
		$this->sendMessage ();
	}
	function sendSMS($mobile, $content, $time = '', $mid = '') {
		$http = 'http://http.chinasms.com.cn/tx/';
		$data = array (
				'uid' => '52201488', // 用户账号
				'pwd' => strtolower ( md5 ( 'wmwchangzhou' ) ), // MD5位32密码
				'mobile' => $mobile, // 号码
				'content' => $content, // 内容
				'time' => $time, // 定时发送
				'mid' => $mid,
				'encode' => 'utf8'  // 子扩展号
				);
		$re = $this->postSMS ( $http, $data ); // POST方式提交
		return $re;
	}
	function postSMS($url, $data = '') {
		$row = parse_url ( $url );
		$host = $row ['host'];
		$port = $row ['port'] ? $row ['port'] : 80;
		$file = $row ['path'];
		while ( list ( $k, $v ) = each ( $data ) ) {
			$post .= rawurlencode ( $k ) . "=" . rawurlencode ( $v ) . "&"; // 转URL标准码
		}
		$post = substr ( $post, 0, - 1 );
		$len = strlen ( $post );
		$fp = @fsockopen ( $host, $port, $errno, $errstr, 10 );
		if (! $fp) {
			return "$errstr ($errno)\n";
		} else {
			$receive = '';
			$out = "POST $file HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post;
			fwrite ( $fp, $out );
			while ( ! feof ( $fp ) ) {
				$receive .= fgets ( $fp, 128 );
			}
			fclose ( $fp );
			$receive = explode ( "\r\n\r\n", $receive );
			unset ( $receive [0] );
			return implode ( "", $receive );
		}
	}
	
	// 短信发送状态说明
	function checkstatus($num) {
		$num = intval ( $num );
		$statusstr = "";
		switch ($num) {
			case 100 :
				$statusstr = "发送成功";
				break;
			case 101 :
				$statusstr = "验证失败";
				break;
			case 102 :
				$statusstr = "短信不足";
				break;
			case 103 :
				$statusstr = "操作失败";
				break;
			case 104 :
				$statusstr = "非法字符";
				break;
			case 105 :
				$statusstr = "内容过多";
				break;
			case 106 :
				$statusstr = "号码过多";
				break;
			case 107 :
				$statusstr = "频率过快";
				break;
			case 108 :
				$statusstr = "号码内容空";
				break;
			case 109 :
				$statusstr = "账号冻结";
				break;
			case 110 :
				$statusstr = "禁止频繁单条发送";
				break;
			case 111 :
				$statusstr = "系统暂定发送";
				break;
			case 112 :
				$statusstr = "号码不正确";
				break;
			case 113 :
				$statusstr = "连接失败";
				break;
			case 120 :
				$statusstr = "系统升级";
				break;
			case 444 :
				$statusstr = "更新数据库失败";
				break;
			default :
				$statusstr = "当前没有待发短信";
				break;
		}
		return $statusstr;
	}
	
	// 发送短信
	function sendMessage($sms_send_bussiness_type = OPERATION_STRATEGY_CZ) {
		$flag = true;
		$mUnicomInterface = ClsFactory::Create ( 'Model.mSmsSend' );
		$message = $mUnicomInterface->getOutgoingMessage ( $sms_send_bussiness_type );
		$statusarr = array (
				'102',
				'109',
				'111',
				'113',
				'120' 
		);
		if (! $message) {
			$flag = false;
		} else {
			foreach ( $message as $messageinfo ) {
				$rs = $this->sendSMS ( $this->changemobiletype ( $messageinfo ['sms_send_mphone'] ), $messageinfo ['sms_send_content'] );
				if (intval ( $rs )) {
					$dataarr = array (
							'sms_send_result_info' => $rs,
							'db_updatetime' => date ( "Y-m-d H:i:s" ),
							'sms_send_type' => 1 
					);
					if (in_array ( $rs, $statusarr )) {
						require ('./Common/email.php');
						$send = Sendmails ( 'zhaosheng@wmw.cn', $this->checkstatus ( $rs ) );
						$flag = false;
						break;
					}
					if ($rs != "100") {
					}
					$result = $mUnicomInterface->modifysmssend ( $messageinfo ['sms_send_id'], $dataarr );
					if (! $result) {
						$flag = false;
						break;
					}
				} else {
					continue;
				}
			}
		}
		if ($flag)
			return true;
		else
			return false;
	}
	
	// 处理手机号码
	function changemobiletype($mobile) {
		$mobile_number = explode ( " ", $mobile );
		foreach ( $mobile_number as $m_umber ) {
			$mark = preg_match ( '/^1[1-9]{1}[0-9]{9}/', $m_umber );
			if (! $mark) {
				continue;
			}
			$mobile_num [] = $m_umber;
		}
		return implode ( ",", $mobile_num );
	}
}
new SendsmsAction ();
