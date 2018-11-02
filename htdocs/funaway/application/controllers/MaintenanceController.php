<?php
class MaintenanceController extends MyAppController
{
    function index()
	{
		if ((FatApp::getConfig("CONF_MAINTENANCE", FatUtility::VAR_INT, 0) != 1))
		{
			FatApp::redirectUser(FatUtility::generateUrl());
		}
        $this->set('maintenance_text', FatApp::getConfig('CONF_MAINTENANCE_TEXT', FatUtility::VAR_STRING, Info::t_lang('Site_Under_Maintenance')));
		$this->_template->render(false, false);	
    }
}