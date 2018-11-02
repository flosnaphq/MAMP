<?php
class FatController {
	protected $_modelName;
	protected $_controllerName;
	protected $_actionName;
	protected $_template;

	protected $_autoCreateModel = true;

	function __construct($action) {
		
		$this->_controllerName = get_class($this);
		$this->_modelName = substr($this->_controllerName, 0, (strlen($this->_controllerName)) - strlen('Controller'));
		$this->_actionName = $action;

		$model = $this->_modelName;

		if ($this->_autoCreateModel) {
			if (file_exists ( CONF_APPLICATION_PATH . 'models/' . strtolower ( $this->_modelName ) . '.php' )) {
				$this->$model = new $model ();
			} else {
				$this->$model = new FatModel ();
			}
		}

		$this->_template = new FatTemplate($this->_controllerName, $this->_actionName);
		$this->getFooterBlock();
		$this->getCmsFooterLink();
		
	}

	function set($name,$value) {
		$this->_template->set($name, $value);
	}
	
	function getFooterBlock(){
		$block = new Block(2);
		$block->loadFromDb();
		$our_mission = $block->getFlds();
		$this->set('our_mission', $our_mission);
		$block = new Block(8);
		$block->loadFromDb();
		$our_vision = $block->getFlds();
		$this->set('our_vision', $our_vision);
	}
	
	function getCmsFooterLink(){
		 $cms = new Cms();
		$cms_links = $cms->getCmsLink(array(7,1));
		$this->set('cms_links',$cms_links); 
	}
}
