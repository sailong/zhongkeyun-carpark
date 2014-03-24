<?php
import('Control.Report.CommonController');

class IndexAction extends CommonController {
	
	
	public function index(){
		
		$this->redirect('AdminCharge/index');
	}

}
