<?php
class UserAction extends Controller {
	public function _initialize() {
	}
	public function index() {
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if ($mAdmin->isLogined()) {
			header('location:/admin/frame');
		}
		
		$this->display("login");
	}
	
	public function login() {
		$user_name = $this->objInput->postStr('username');
		$pass_word = $this->objInput->postStr('password');
		$mac = $this->objInput->postStr('mac');
		if (empty($user_name) || empty($pass_word)) {
			$this->showMessage(0,"账号或者密码不能为空");
		}
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_info = $mAdmin->getAdminByName($user_name);
		if ($admin_info['password'] == md5($pass_word)) {
			$isAdminPost = $mAdmin->isAdminPost($admin_info['admin_id']);
			if ($isAdminPost && $mac != $isAdminPost['mac']) {
				$this->showMessage(0,'你已经在别的岗亭登录了，请不要重复登录');
			}
			$admin_id = $mAdmin->uid();
			if ($mAdmin->isLogined() && $admin_id != $admin_info['admin_id']) {
				$mAdmin->endWork($admin_id);
			}
			
			//记录管理登录行为
			$user_info = array();
			$user_info['last_time'] = time();
			$mAdmin->modifyAdminInfo($user_info, $admin_info['admin_id']);
			//登记上班数据
			if ($admin_info['admin_name'] != 'admin') {
				$mAdmin->startWork($admin_info['admin_id'], $mac);
			}
			//cookie
			$user_token = token_encode(array($admin_info['password'], $admin_info['admin_id']));
			header(getCookieStr(ADMIN_SESSION_TOKEN, $user_token, time()+3600*24*30,"/"));
			
			header("location:/admin/frame");
		} else {
			$this->showMessage(0,'输入的账号或者密码不正确');
		}
	}
	
	public function endWork() {
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$mAdmin->isLogined()) {
			header('location:/admin/frame');
		}
		
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		$admin_list = $mAdmin->getAdminList(0, 100);
		$this->assign('admin_list', $admin_list);
		
		$admin_info = $mAdmin->getCurrentUser();
		$this->assign('admin_info', $admin_info);
		
		$admin_post = $mAdmin->getAdminPost($admin_info['admin_id']);
		$admin_post['format_end_time'] = date('Y-m-d H:i:s', $admin_post['end_time']);
		$admin_post['format_start_time'] = date('Y-m-d H:i:s', $admin_post['start_time']);
		$this->assign('admin_post', $admin_post);
		
		$door_ids = array_keys($admin_info['door']);
		$mSession = ClsFactory::Create('Model.mSession');
		$end_session_info = $mSession->getSessionInfoByEndDoorTime($door_ids, $admin_post['start_time'], $admin_post['end_time']);
		$charge_arr = array();
		$real_arr = array();
		$card_money_arr = array();
		$exit_num = 0;//智能出场数
		$charge_num_all = 0;//发生收费数
		$charge_num = 0;//收费数
		$free_num = 0;//全部免费数
		$man_exit_num = 0;//手工出场数
		
		foreach ($end_session_info as $session_info) {
			foreach ($session_info as $session) {
				$charge_arr[] = $session['charge'];
				$real_arr[] = $session['real_money'];
				if ($session['end_status'] == 0) {
					$card_money_arr[] = $session['real_money'];
					$exit_num ++;
				} else {
					$man_exit_num ++;
				}
				if ($session['real_money'] > 0) {
					$charge_num_all ++;
				} else {
					$free_num ++;
				}
				if ($session['real_money'] > 0 && $session['end_status'] == 1) {
					$charge_num ++;
				}
				
			}
		}
		$this->assign('charge', array_sum($charge_arr));
		$this->assign('real_money', array_sum($real_arr));
		$this->assign('charge_diff', array_sum($charge_arr) - array_sum($real_arr));
		$this->assign('card_money', array_sum($card_money_arr));
		$this->assign('exit_num', array_sum($charge_arr));
		$this->assign('man_exit_num', $man_exit_num);
		$this->assign('charge_num_all', $charge_num_all);
		$this->assign('charge_num', $charge_num);
		$this->assign('free_num', $free_num);
		
		//$session_list = $mSession->getSessionByCond('start_door_id in ('.implode(',', $door_ids).') and start_time >'.$admin_post['start_time'].' and start_time <'.$admin_post['end_time'].', start_time desc', 0, 99999);
		$this->assign('entry_num', $session_list ? count($session_list) : 0);
		$man_entry_num = 0;
		foreach ($session_list as $session) {
			if ($session['start_status'] == 1) {
				$man_entry_num ++;
			}
		}
		$this->assign('man_entry_num',$man_entry_num);
		
		$this->display("end_work");
	}
	
	public function logout() {
		//登记上班数据
// 		$mAdmin = ClsFactory::Create('Model.mAdmin');
// 		$admin_info = $mAdmin->getCurrentUser();
// 		$mAdmin->endWork($admin_info['admin_id']);
		setcookie(ADMIN_SESSION_TOKEN,'',time()-1,"/");
		unset($_COOKIE[ADMIN_SESSION_TOKEN]);
		$this->toLogin();
	}
	
}
