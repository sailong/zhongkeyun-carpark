<?php
class FrameAction extends Controller {
	
	private $mAdmin;
	public function _initialize() {
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) {
			$this->toLogin();
		}
	}
	
	public function index()
	{	
		$userInfo = $this->mAdmin->getCurrentUser();
		if(in_array($userInfo['group_id'],array(2,8)))//如果是岗亭人员 则直接跳转到监控页面
		{
			$this->display('sentry_monitor');
		}else
		{
			$this->display('index');
		}
	
	}
	
	public function left()
	{
		$this->display('left');
	}
	
	public function head()
	{
	
		$this->display('head');
	}
	
	public function welcome()
	{
	
		$this->display('welcome');
	}
	
}
