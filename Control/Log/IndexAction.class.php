<?php
import('Control.Log.CommonController');

class IndexAction extends CommonController {
	
	
	public function index(){
		
		$this->redirect('Into/index');
	}

}
