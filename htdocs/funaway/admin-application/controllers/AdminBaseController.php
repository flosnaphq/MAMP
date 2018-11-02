<?php

class AdminBaseController extends FatController {

    const PAGESIZE = 20;

    public function __construct($action) {
        if (!AdminAuthentication::isAdminLogged()) {
			if (FatUtility::isAjaxCall()) {
				FatUtility::dieWithError('Your Session has been expired. Please reload the page.');
			}
            FatApp::redirectUser(FatUtility::generateUrl('AdminGuest', 'loginForm'));
        }
        parent::__construct($action);
		
		if (!FatUtility::isAjaxCall()) {
            $this->set('adminName', AdminAuthentication::getLoggedAdminAttribute('admin_name'));
            // You can set the navigation etc based on permissions here.
        }

        $this->set('admin_layout', AdminAuthentication::getLoggedAdminAttribute('admin_layout'));
        $navigationMenu = new NavigationMenu($this->_controllerName, $this->_actionName);
        $this->set('menus',$navigationMenu->getMenu());
        // echo 'Hi!!! I am in base controller';exit;
    }

    public function isValidRequest($type) {
        
        $RequestType = strtoupper($type);
        if(strtoupper($_SERVER['REQUEST_METHOD'])==$RequestType){
            return true;
        }
        return false;
    }
	
	
	public function renderJsonError($msg = '', $returnContent = true, $convertVariablesToHtmlentities = false)
	{
		$this->set('msg', $msg);
		$this->_template->render(false, false, 'json-error.php', $returnContent, $convertVariablesToHtmlentities);
	}
	
	public function renderJsonSuccess($msg = '', $returnContent = true, $convertVariablesToHtmlentities = false)
	{
		$this->set('msg', $msg);
		$this->_template->render(false, false, 'json-success.php', $returnContent, $convertVariablesToHtmlentities);
	}

}
