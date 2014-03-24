<?php
class SettingAction extends Controller {
	
	public $nav='系统设置';
	private $mAdmin;
	
	public function _initialize() {
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) {
			$this->toLogin();
		}
		$this->assign('nav',$this->nav);
	}
	
	public function index() {
	
		header("location:/setting/setting/view/item/base");
	}
	
	public function view()
	{
		if (!$this->mAdmin->checkCurrentUserLevel(102)) {
			$this->toWelcome();
		}
		$item = $this->objInput->getStr('item');
		$item = strtolower($item);
		$mSetting = ClsFactory::Create('Model.mSetting');
		$setting = $mSetting->getSettingInfo();//var_dump($setting['company_name']);
		$this->assign('setting',$setting);
		$this->assign('item',$item);
		$this->display('setting_'.$item);
	}
	
	/**
	 * 修改操作
	 */
	public function update()
	{
		$item = $this->objInput->postStr('item');
		if(!$item) $this->showMessage(0,'非法请求');
		$settingArr = $this->objInput->postArr('setting');
		
		$mSetting = ClsFactory::Create('Model.mSetting');
		if($mSetting->modifySettingInfo($settingArr)) $this->showMessage(1,'修改成功！',__URL__.'/view/item/'.$item);
		$this->showMessage(0,'修改失败！');
	}
	

	public function password() {
		if (!$this->mAdmin->checkCurrentUserLevel(101)) {
			$this->toWelcome();
		}
	
		if($_POST)
		{
			$old_password = $this->objInput->postStr('old_password');
			$password = $this->objInput->postStr('password');
			$password2 = $this->objInput->postStr('password2');
				
			$userInfo = $this->mAdmin->getCurrentUser();
			if($userInfo['password']!=md5($old_password)) $this->showMessage(0,'旧密码输入错误！');
			if($password != $password2) $this->showMessage(0,'两次输入密码不一致！');
			$return = $this->mAdmin->modifyAdminInfo(array('password'=>md5($password)),$userInfo['admin_id']);
			if($return) $this->showMessage(1,'密码修改成功！');
			$this->showMessage(0,'密码修改失败！');
		}
		$this->display('setting_password');
	}
}
