<?php
class CarCategoryAction extends Controller {
	
	public $nav='系统设置';
	public $subNav = '车型管理';
	public function _initialize() {
		$mAdmin = ClsFactory::Create('Model.mAdmin');
		if (!$mAdmin->isLogined()) {
			$this->toLogin();
		}
		$this->assign('nav',$this->nav);
		$this->assign('subNav',$this->subNav);
	}
	
	private function loadModel()
	{
		return ClsFactory::Create('Model.mCarCategory');
	}
	public function index(){
	
		$data = $this->loadModel()->getCarCategory();//var_dump($data);
		$this->assign('data',$data);
		$this->display('car_category_index');
	}
	/**
	 * 添加/修改
	 */
	public function modify(){
	
		$id = $this->objInput->getStr('id');
		$data = array();
		if($id)
		{
			$data = $this->loadModel()->getCarCategoryById($id);
			if(isset($data[$id])) $data = $data[$id];
		}
		$this->assign('data',$data);
		$this->assign('id',$id);
		$this->display('car_category_modify');
	}
	
	public function modifyDo(){
	
		$id = $this->objInput->postStr('id');
		$data = $this->objInput->postArr('data');
		$model = $this->loadModel();
		if($id)
		{
			if($model->modifyCarCategory($data,$id)) $this->showMessage(1,'修改成功！',__URL__.'/index');
			$this->showMessage(0,'修改失败！');
		}else
		{
			if($model->addCarCategory($data)) $this->showMessage(1,'增加成功！',__URL__.'/index');
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
		$this->loadModel()->delCarCategory($id);
		$this->showMessage(1,'删除成功！',__URL__.'/index');
	}
}
