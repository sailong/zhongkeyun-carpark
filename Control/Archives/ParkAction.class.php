<?php
class ParkAction extends Controller {
	
	public $nav='档案管理';
	private $tepPrefix = 'park_';
	public function _initialize() {
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$mAdmin->isLogined()) {
			$this->toLogin();
		}
		if (!$mAdmin->checkCurrentUserLevel(203)) {
			$this->toWelcome();
		}
		$this->assign('nav',$this->nav);
	}
	
	private function loadModel()
	{
		return ClsFactory::Create('Model.mPark');
	}
	
	public function index(){
	
		$data = $this->loadModel()->getParkInfo();
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'index');
	}
	/**
	 * 添加/修改
	 */
	public function modify(){
	
		$id = $this->objInput->getStr('id');
		$data = array();
		if($id)
		{
			$data = $this->loadModel()->getParkById($id);
			if(isset($data[$id])) $data = $data[$id];
		}
		$this->assign('id',$id);
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'modify');
	}
	
	public function modifyDo(){
	
		$id = $this->objInput->postStr('id');
		$data = $this->objInput->postArr('data');
		$model = $this->loadModel();
		if($id)
		{
			if($model->modifyParkInfo($data,$id)) $this->showMessage(1,'修改成功！',__URL__.'/index');
			$this->showMessage(0,'修改失败！');
		}else
		{
			if($model->addPark($data)) $this->showMessage(1,'增加成功！',__URL__.'/index');
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
		$this->loadModel()->delParkInfo($id);
		$this->showMessage(1,'删除成功！',__URL__.'/index');
	}
}
