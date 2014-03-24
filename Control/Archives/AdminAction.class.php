<?php
class AdminAction extends Controller {
	
	public $nav='档案管理';
	private $tepPrefix = 'admin_';
	private $mAdmin;
	public function _initialize() {
		$this->mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$this->mAdmin->isLogined()) {
			$this->toLogin();
		}
		if (!$this->mAdmin->checkCurrentUserLevel(202)) {
			$this->toWelcome();
		}
		$this->assign('nav',$this->nav);
	}
	
	private function loadModel()
	{
		return $this->mAdmin;
	}
	
	public function index(){
	
		$data = $this->loadModel()->getAdminList();
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'index');
	}
	/**
	 * 添加/修改
	 */
	public function modify(){
	
		$id = $this->objInput->getStr('id');
		$data = array();
		
		$groupList = $this->loadModel()->getAdminGroup();
		
		if($id)
		{
			$data = $this->loadModel()->getAdminById($id);
			if(isset($data[$id])) $data = $data[$id];
		}
		$this->assign('id',$id);
		$this->assign('data',$data);
		$this->assign('groupList',$groupList);
		$this->display($this->tepPrefix.'modify');
	}
	
	public function modifyDo(){
	
		$id = $this->objInput->postStr('id');
		$data = $this->objInput->postArr('data');
		$model = $this->loadModel();
		if(empty($data['password']))
		{
			unset($data['password']);
		}else {
			$data['password'] = md5($data['password']);
		}
		if($id)
		{
			if($model->modifyAdminInfo($data,$id)) $this->showMessage(1,'修改成功！',__URL__.'/index');
			$this->showMessage(0,'修改失败！');
		}else
		{
			if($model->addAdmin($data)) $this->showMessage(1,'增加成功！',__URL__.'/index');
			$this->showMessage(0,'操作失败！');
		}
	}
	/**
	 * 删除操作
	 */
	public function delete()
	{
		$id = $this->objInput->getStr('id');
		if(!$id) return;
		if($id==1) $this->showMessage(0,'系统管理员不能被删除');
		$this->loadModel()->delAdminInfo($id);
		$this->showMessage(1,'删除成功！',__URL__.'/index');
	}
}
