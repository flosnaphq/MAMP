<?php

class CustomController extends MyAppController 
{

    public function __construct($action) {
        parent::__construct($action, false);
    }

	public function requestDemo(){
		$this->_template->render( false, false ); 
	}	
	
}
