<?php

class SystemRestoreController extends AdminBaseController {

    private $admin_id;

    public function __construct($action) {
        parent::__construct($action);
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        if(!AdminPrivilege::canViewSystemRestore($this->admin_id)){
            die("Unauthorized Access");
        }
    }

    function index() {
        $settingsObj = new Configurations();
        $restore_point_frm = $this->getRestorePointForm();
        $post = FatApp::getPostedData();
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['submit_restore_point']) && $restore_point_frm->getFormDataFromArray($post)) {
            if ($settingsObj->compress(CONF_INSTALLATION_PATH . "restore", CONF_INSTALLATION_PATH . "restore-backups/")) {
                $target = CONF_INSTALLATION_PATH . "restore/user-uploads";
                $source = CONF_USER_UPLOADS_PATH;
                $settingsObj->full_copy($source, $target);
                $settingsObj->backupDatabase("funaway-dbfile", false, false, CONF_INSTALLATION_PATH . "restore/database");
                Message::addMessage("Restore Point Updated Successfully!!");
                $settingsObj->reloadPage();
            }
        }

        $restoreEnabled = FatApp::getConfig('CONF_AUTO_RESTORE_ON', FatUtility::VAR_INT, 0);

        $this->_template->set('restore_point_frm', $restore_point_frm);
        $this->_template->render();
    }

    function updateSetting($val) {
        $settingsObj = new Configurations();
        if ($settingsObj->update("CONF_AUTO_RESTORE_ON", $val)) {
            Message::addMessage("Setting Updated");
            FatUtility::dieJsonSuccess(Message::getHtml());
        }
        Message::addErrorMessage("Something went wrong.");
        FatUtility::dieJsonSuccess(Message::getHtml());
    }

    protected function getRestorePointForm() {
        $frm = new Form('frmdatabaseBackup');
        $frm->addFormTagAttribute('class', "web_form");
        $fld = $frm->addSubmitButton('', 'submit_restore_point', 'Create Restore Point');
        $fld->htmlAfterField = '<p><strong>Notes</strong>: On clicking the above button, system restore point will change to current database & uploads folder and current restore folder will be moved to backup folder with current date attached to it.</p>';
        $isChecked = (FatApp::getConfig("CONF_AUTO_RESTORE_ON") ? "checked" : "");
        $frm->addHTML('', '', '<strong>Auto Restore</strong>&nbsp;<div class="onoffswitch">
						<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" ' . $isChecked . '>
						<label class="onoffswitch-label" for="myonoffswitch">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>'
        );
        return $frm;
    }

}
