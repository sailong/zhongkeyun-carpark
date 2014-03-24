<?php
class DoorAction extends Controller {
	
	public $nav='系统设置';
	private $tepPrefix = 'door_';
	public function _initialize() {
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$mAdmin->isLogined()) {
			$this->toLogin();
		}
		$this->assign('nav',$this->nav);
	}
	
	private function loadModel()
	{
		return ClsFactory::Create('Model.mDoor');
	}
	public function index(){
	
		$data = $this->loadModel()->getDoorInfo();
		$this->assign('data',$data);
		$this->display($this->tepPrefix.'index');
	}
	/**
	 * 添加/修改
	 */
	public function modify(){
	
		$id = $this->objInput->getStr('id');
		
		$carCategoryModel = ClsFactory::Create('Model.mCarCategory');
		$carCategoryList = $carCategoryModel->getCarCategory();
		$data = array();
		if($id)
		{
			$data = $this->loadModel()->getDoorById($id);
			if(isset($data[$id])) $data = $data[$id];
		}
		$this->assign('id',$id);
		$this->assign('data',$data);
		
		$park_info = ClsFactory::Create('Model.mPark')->getParkInfo();
		$this->assign('park_info', $park_info);
		
		$controlData = array(
							'mask' => MASK,
							'gateway' => GATEWAY,
							'port' => PORT,
				);
		$this->assign('controlData',$controlData);
		$this->display($this->tepPrefix.'modify');
	}
	
	public function modifyDo(){
	
		$id = $this->objInput->postStr('id');
		$data = $this->objInput->postArr('data');
		$model = $this->loadModel();
		//控制器地址 与 读卡器号判断
		
		$doorInfo = $model->getDoorInfoByAddrReader($data['door_addr'],$data['reader_no']);
		if($id)
		{
			if($doorInfo && $doorInfo['door_id']!=$id) $this->showMessage(0,'控制器地址 与 读卡器序号 组合必须唯一！');
			if($model->modifyDoorInfo($data,$id)) $this->showMessage(1,'修改成功！',__URL__.'/index');
			$this->showMessage(0,'修改失败！');
		}else
		{
			if($doorInfo) $this->showMessage(0,'控制器地址 与 读卡器序号 组合必须唯一！');
			if($model->addDoor($data)) $this->showMessage(1,'增加成功！',__URL__.'/index');
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
		$this->loadModel()->delDoorInfo($id);
		$this->showMessage(1,'删除成功！',__URL__.'/index');
	}
}
