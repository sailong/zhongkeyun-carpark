<?php
class IndexAction extends Controller {
	public function _initialize() {
	}
	
	public function index()
	{
		$this->redirect('/admin/user');
	}
	
}
