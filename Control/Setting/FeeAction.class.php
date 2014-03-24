<?php
class FeeAction extends Controller {
	
	public $nav='系统设置';
	public function _initialize() {
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$mAdmin->isLogined()) {
			$this->toLogin();
		}
		if (!$mAdmin->checkCurrentUserLevel(104)) {
			$this->toWelcome();
		}
		$this->assign('nav',$this->nav);
	}
	
	private function loadModel()
	{
		return ClsFactory::Create('Model.mFee');
	}
	public function index(){
	
		$data = $this->loadModel()->getFeeInfo();//var_dump($data);
		$this->assign('data',$data);
		$this->display('fee_index');
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
			$data = $this->loadModel()->getFeeById($id);
			if(isset($data[$id])) $data = $data[$id];
		}
		$this->assign('data',$data);
		$this->assign('carCategoryList',$carCategoryList);
		$this->assign('id',$id);
		$this->display('fee_modify');
	}
	
	public function modifyDo(){
	
		$id = $this->objInput->postStr('id');
		$data = $this->objInput->postArr('data');
		$model = $this->loadModel();
		if($id)
		{
			if($model->modifyFeeInfo($data,$id)) $this->showMessage(1,'修改成功！',__URL__.'/index');
			$this->showMessage(0,'修改失败！');
		}else
		{
			if($model->addFee($data)) $this->showMessage(1,'增加成功！',__URL__.'/index');
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
		$this->loadModel()->delFeeInfo($id);
		$this->showMessage(1,'删除成功！',__URL__.'/index');
	}
}
