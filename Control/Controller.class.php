<?php
/**
 * 控制层基类
 * @author 杨益(yangyi@wmw.cn)
 * @copyright wmw.cn
 * @package control
 * @since 2011-7-20
 */
require_once (LIBRARIES_DIR . '/Request.class.php');
import ( "@.Common.RequestDecorate" );
session_start ();
header ( "Access-Control-Allow-Origin:*" );
abstract class Controller extends Action {
	// 用户输入对象
	protected $objInput;
	public function __construct() {
		parent::__construct ();
		// 初始化用户输入对象
		if (constant ( 'REQUEST_DECORATE_SWITCH' )) {
			$this->objInput = new RequestDecorate ( Request::getInstance () );
		} else {
			$this->objInput = Request::getInstance ();
		}
		// $commandobjarr = array();
		// //检查是否为冻结用户
		// $commandobjarr[] = ClsFactory::Create('@.Control.Command.CheckFrozen');
		// //检查用户是否激活
		// $commandobjarr[] = ClsFactory::Create('@.Control.Command.CheckActived');
		
		// //执行系列命令
		// $chainobj = ClsFactory::Create('@.Control.Command.CommandChain');
		// $chainobj->addCommand($commandobjarr);
		// if(Db::getDbConf('main')) {
		// $chainobj->runCommand();
		// }
		
		// 检测access_token是否合法
		$access_token = $this->objInput->getStr ( 'access_token' );
		$mToken = ClsFactory::Create ( 'Model.mToken' );
		if ($access_token && ! $mToken->checkToken ( $access_token )) {
			$this->displayError ( 10005 );
		}
	}
	// 用户提交数据验证
	public function checkpara() {
	}
	public function select_smarty() {
		$this->view->select_smarty ();
	}
	
	// 跳转到登录页面
	public function toLogin($url = '') {
		if (! $url) {
			$url = 'http://' . $_SERVER ['SERVER_NAME'] . $_SERVER ["REQUEST_URI"];
		}
		//header ( 'Location:/Homeuser/Login/index?url=' . urlencode ( $url ) );
		header ('Location:/admin/user');exit;
	}
	
	public function toWelcome() {
		if (! $url) {
			$url = 'http://' . $_SERVER ['SERVER_NAME'] . $_SERVER ["REQUEST_URI"];
		}
		//header ( 'Location:/Homeuser/Login/index?url=' . urlencode ( $url ) );
		header ('Location:/admin/frame/welcome');exit;
	}
	
	function displayJson($data) {
		$data = json_encode ( $data );
		if ($this->objInput->getStr ( 'jsfrom' ) == 'web') {
			$callback = $this->objInput->getStr ( 'callback' );
			echo $callback . '(' . $data . ');';
		} else if ($this->objInput->getStr ( 'jsfrom' ) == 'iframe') {
			echo "<script type='text/javascript'>document.domain='aipingfang.com';window.parent.callback('" . $data . "');</script>";
		} else {
			echo $data;
		}
		exit ();
	}
	function displayError($error_code) {
		$error_info = ErrorParse::errorInfo ( $error_code );
		$error_info ['request'] = $_SERVER ["REQUEST_URI"];
		$this->displayJson ( $error_info );
	}
	
	public function showMessage($status='1',$message='操作成功',$url=false,$time=1, $card_info = array())
	{
	
		$message = ($status==1 ? 'SUCCESS:' : 'ERROR:').$message;
		$image = $status==1 ? 'success.png' : 'error.png';
		header('content-type:text/html;charset=utf-8');
		$back_color ='#ff0000';
		if($status =='1')
		{
			$back_color= 'blue';
		}
		if ($url)
		{
			$url = "window.location.href='{$url}'";
		}
		else
		{
			$url = "history.back();";
		}
		
		
		$control_info = '';
		if ($card_info) {
			$control_info = '<object classid="clsid:F5C7248A-79F7-4866-9836-31227D69570B" codebase="/PSC.cab#version=1,0,0,1" id="px" width="0" height="0" VIEWASTEXT>
</object>';
		}
		
		//---获取控制器列表-------------------------------------------------------------------------
		$doorModel = ClsFactory::Create('Model.mDoor');
		$doorList = $doorModel->getDoorInfo();
		$realDoorList = $door_id_arr = array();
		foreach ($doorList as $key=>$val)
		{
			$realDoorList[] = $val;
		}
		$realDoorList = $realDoorList && $control_info ? json_encode($realDoorList) : '""';
		
		//获取是否共享车位卡
		$mCard = ClsFactory::Create('Model.mCard');
		foreach ($card_info as &$card) {
			$c = array_pop($mCard->getCardByCode($card['code']));
			$card['hasShare'] = $mCard->hasShareParking($c['card_id']);
			$family_list = $mCard->getFamilyCardList($c['card_id']);
			$park_str = $c['park'];
			foreach ($family_list as $ca) {
				$park_str .= ','.$ca['park'];
			}
			$card['park'] = $park_str;
		}
		
		$card_info = $card_info && $control_info ? json_encode($card_info) : '""';
		
		$jsStr = <<<js
		var doorJson = {$realDoorList};
		var port = 60000;
		var door_addr;
		var ret;
		var ip;
		var card_info = {$card_info};
		function in_array(item, array) {
			for(var i = 0;i<array.length;i++){  
				if(array[i] == item){  
					return i;  
				}  
			}  
			return -1; 
		}
		function updateControl()
		{
			if(doorJson != '' && card_info != '')
			{
				alert('卡片信息写入控制器中。。。，请耐心等待！');
				$.each(doorJson, function(index, content) {
					door_addr = parseInt(content.door_addr);
					ip = content.door_ip;
					try{
						$.each(card_info, function(index, card) {
							var park = card.park.split(',');
							if (card.code.indexOf('-') == -1 && ((parseInt(content.park_id) == 0) || (parseInt(content.park_id) > 0 && in_array(content.park_id, park) != -1))) {
								//alert('--'+card.code+'--'+card.status+'--'+content.park_id+'has'+card.hasShare);
								var res = px.addOrModifyPrivilege(content.door_addr, ip, port, content.brake_no, card.code, card.start_time, card.end_time, card.status, 123456);
								res = JSON.parse(res);
								if (res.ErrorCode != 0)
								{
									res = px.addOrModifyPrivilege(content.door_addr, ip, port, content.brake_no, card.code, card.start_time, card.end_time, card.status, 123456);
									res = JSON.parse(res);
								}
								if (res.ErrorCode != 0) {
									alert('写入控制器失败，请重试');
								}
							}
						});
					} catch(e) {
						alert(e);
					}
					
					});
				alert('卡片信息写入控制器成功！');
			}
			window.setInterval("run();", 1000);
		}
		
		jQuery(document).ready(function(){
			updateControl();
		});
js;
		
		echo <<<HTML
			
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>-</title>
			<script type="text/javascript" src="/Public/js/jquery-1.7.2.min.js"></script>
			<script src="/Public/js/json2.js"></script>
			</head>
			<body>
    		<div style="background:#C9F1FF; margin:0 auto; height:200px; width:600px; text-align:center;">
    		<div style="margin-top:50px;">
    		<h5 style="color:{$back_color};font-size:14px; padding-top:20px;" >{$message}</h5>
    		页面正在跳转请等待<span id="sec" style="color:blue;">{$time}</span>秒
    		</div>
    		</div>
    		{$control_info}

    		<script type="text/javascript">
    		function run()
    		{
    		    var s = document.getElementById("sec");
    			if(s.innerHTML == 0)
    			{
	    			{$url}
	    			return false;
                }
    			s.innerHTML = s.innerHTML * 1 - 1;
            }
	    					{$jsStr}
    		
    		</script>
	    	</body>
	    	</html>
HTML;
		    			die;
	}
	
	public function getMenuList($id=0)
	{
		$model = ClsFactory::Create('Model.mMenu');
		return $model->getMenuList($id);
	}
}
