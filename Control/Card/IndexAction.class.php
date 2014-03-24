<?php
import('Control.Card.CardController');


class IndexAction extends Controller {
	
	
	public function index(){
		
		$this->redirect('check/index');
	}

}
