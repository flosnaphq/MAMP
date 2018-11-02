<?php

#error_reporting(E_ERROR);

class AdminController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array("lists", "view", "action");
        if (FatUtility::isAjaxCall() && !in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewAdmin($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditAdmin($this->admin_id);

        if (!$this->canView && $action != 'logout') {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {


        $brcmb = new Breadcrumb();
        $brcmb->add("Admin");
        $this->set('breadcrumb', $brcmb->output());
        $search = $this->searchForm();
        $this->set("search", $search);
        $this->_template->render();
    }

    function forgotPasswordForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access');
        }
        $post = FatApp::getPostedData();
        $admin_id = isset($post['admin_id']) ? FatUtility::int($post['admin_id']) : 0;
        if ($admin_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $frm = $this->getForgotPasswordForm($admin_id);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'admin/_partial/forgot-password-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setupForgotPassword() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $admin_id = isset($post['admin_id']) ? FatUtility::int($post['admin_id']) : 0;
        if ($admin_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $frm = $this->getForgotPasswordForm($admin_id);
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $adm = new Admin();
        $data['admin_id'] = $admin_id;
        $data['admin_password'] = $post['admin_password'];
        if (!$adm->addUpdate($data)) {
            FatUtility::dieJsonError("Something Went Wrong. Please try Again!");
        }
        FatUtility::dieJsonSuccess("Password Updated!");
    }

    private function getForgotPasswordForm($admin_id) {
        $frm = new Form('forgotPasswordFrm');
        $frm->addHiddenField('', 'admin_id', $admin_id);
        $pwd = $frm->addPasswordField('Password', 'admin_password');
        $pwd->requirements()->setRequired();
        $cpwd = $frm->addPasswordField('Confirm Password', 'cpassword');
        $cpwd->requirements()->setRequired();
        $cpwd->requirements()->setCompareWith('admin_password', 'eq');
        $frm->addSubmitButton('', 'btn_submit', 'Update');
        return $frm;
    }

    function menu($val) {
        if (in_array($val, array(1, 2, 3))) {
            $_SESSION['menu_type'] = $val;
        } else {
            $_SESSION['menu_type'] = 1;
        }
        switch ($_SESSION['menu_type']) {
            case 1:
                FatApp::redirectUser(FatUtility::generateUrl('home'));
                break;
            case 2:
                FatApp::redirectUser(FatUtility::generateUrl("home", "merchant-dashboard"));
                break;
            case 3:
                FatApp::redirectUser(FatUtility::generateUrl("home", "user-dashboard"));
                break;
        }
    }

    public function lists() {

        $db = FatApp::getDb();
        $post = FatApp::getPostedData();
        $frm = $this->searchForm();
        $post = $frm->getFormDataFromArray($post);
        $admn = new Admin();
        $srch = $admn->getAdmins();
        $srch->addCondition('admin_id', '>', 1);
        if (!empty($post)) {

            $admin_username = $post['admin_username'];
            $admin_email = $post['admin_email'];
            if (!empty($admin_username)) {
                $srch->addCondition('admin_username', 'like', '%' . $admin_username . '%')->attachCondition('admin_full_name', 'like', '%' . $admin_username . '%');
            }
            if (!empty($admin_email)) {
                $srch->addCondition('admin_email', 'like', '%' . $admin_email . '%');
            }
        }

        $rs = $srch->getResultSet();
        $records = $db->fetchAll($rs);
        $this->set("arr_listing", $records);
        $htm = $this->_template->render(false, false, "admin/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function action() {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        if (isset($post) && !empty($post)) {
            $adm = new Admin();
            if ($adm->addUpdate($post)) {
                FatUtility::dieJsonSuccess("Admn Detail Updated!");
            }
        }
        FatUtility::dieJsonError("Something Went Wrong!");
    }

    function permissions($admin_id) {
        if (!$this->canEdit) {
            die('Unauthorized Access!');
        }
        $admn = new Admin();
        $modules = $admn->getPermissionOption($admin_id);
        $frm = $this->getPermissionOptionForm($modules);
        $frm_values = array('admin_id' => $admin_id);
        $frm->fill($frm_values);
        $admin = $admn->getAdminById($admin_id);
        $admin_detail = $admin['admin_name'] . ' (' . $admin['admin_username'] . ')';
        $this->set('admin_detail', $admin_detail);
        $this->set('frmPermissions', $frm);
        $brcmb = new Breadcrumb();
        $brcmb->add("Admin", Fatutility::generateUrl("admin"));
        $brcmb->add("Permissions");
        $this->set('breadcrumb', $brcmb->output());
        $this->set('admin_id', $admin_id);
        $this->_template->render();
    }

    function permission_action() {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $admn = new Admin();
        $post = FatApp::getPostedData();

        $admin_id = isset($post['admin_id']) ? FatUtility::int($post['admin_id']) : 0;
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['btn_submit'])) {
            $modules = $admn->getPermissionOption($admin_id);

            $frm = $this->getPermissionOptionForm($modules);
            $frm_values = array('admin_id' => $admin_id);
            if (!$frm->validate($post)) {
                Message::addErrorMessage($frm->getValidationErrors());
            } else {
                if ($post['admin_id'] == intval($admin_id)) {
                    $data = array('admin_id' => intval($post['admin_id']), 'permission' => $post['permission']);
                    if ($admn->updatePermissions($data)) {
                        Message::addMessage("Permissions updated successfully.");
                    } else {
                        Message::addErrorMessage($admn->getError());
                    }
                } else {
                    Message::addErrorMessage('Invalid Request!');
                }
            }
        }
        FatApp::redirectUser(FatUtility::generateUrl("admin", "permissions", array($admin_id)));
    }

    private function getPermissionOptionForm($modules = array()) {
        $frm = new Form('frmPermissions');
        $frm->addHiddenField('', 'admin_id', '');
        if (!empty($modules)) {
            foreach ($modules as $m) {
                $frm->addSelectBox(ucfirst($m['permname_name']), 'permission[' . $m['permname_id'] . ']', array('0' => 'None', '1' => 'Read', '2' => 'Read/Write'), $m['permisson_level']);
            }
        }
        $frm->addSubmitButton('', 'btn_submit', 'Update');
        return $frm;
    }

    public function form() {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $form = $this->getAdminForm();
        $data = FatApp::getPostedData();
        $adm = new Admin();
        if (!empty($data) && $data['admin_id'] != "") {
            $record = $adm->getAdminById(intval($data['admin_id']));
            $form->removeField($form->getField("admin_password"));
            $fld = $form->getField("btn_submit")->value = "Update";
            $form->fill($record);
        }
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "admin/_partial/form.php", true, true);

        FatUtility::dieJsonSuccess($htm);
    }

    private function getAdminForm() {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $frm = new Form('frmAdmin');
        $frm->addHiddenField("", 'admin_id');
        $frm->addRequiredField('Username', 'admin_username')->setUnique("tbl_admin", "admin_username", "admin_id", "admin_username", "admin_username");
        ;
        $frm->addPasswordField('Password', 'admin_password')->requirements()->setRequired();
        $frm->addEmailField('Email Id', 'admin_email')->setUnique("tbl_admin", "admin_email", "admin_id", "admin_email", "admin_email");
        $frm->addRequiredField('Name', 'admin_name');
        $frm->addSelectBox('Status', 'admin_active', MyHelper::getStatus());
        $frm->addSubmitButton('', 'btn_submit', 'Add')->html_after_field = '<input type="button"  class="" value="Cancel" onclick = "removeFormBox();">';
        return $frm;
    }

    private function searchForm() {
        $frm = new Form('frmSearch');
        $frm->addTextBox('Username', 'admin_username');
        $frm->addTextBox('Email Id', 'admin_email');
        $frm->addSubmitButton('', 'btn_submit', 'Search');
        return $frm;
    }

    public function view($admin_id) {
        $admin_id = FatUtility::int($admin_id);
        $adm = new Admin();
        $record = $adm->getAdminById(intval($admin_id));
        $this->set('records', $record);
        $this->_template->render(false, false, "admin/_partial/view.php");
    }

    function logout() {
        $admin = new Admin();
        $admin->logout();
        Message::addMessage('You are Logged out successfully.');
        FatApp::redirectUser(FatUtility::generateUrl('admin', 'admin-guest', array('login-form')));
    }

}

?>