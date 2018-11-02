<?php

class ProfileController extends AdminBaseController {

    private $_adminProfileObj, $_adminId = 0;

    public function __construct($action) {

        parent::__construct($action);
        if (0 == $this->_adminId) {
            $this->_adminId = AdminAuthentication::getLoggedAdminId();
        }
        $this->_adminProfileObj = new AdminUsers($this->_adminId);
    
    }

    public function index() {
        $data = $this->commonCheckForUpdateProfile();

        $imgForm = $this->getImageForm();
        $profFrm = $this->getProfileFrm();
        $data['admin_id'] = $this->_adminId;
        $profFrm->fill($data);

        $this->set('imgForm', $imgForm);
        $this->set('frmProf', $profFrm);
        $this->set('data', $data);
        $this->set('clss', 'edit_prof');
        $this->_template->render();
    }

    public function changePassword() {

        $data = $this->commonCheckForUpdateProfile();

        $imgForm = $this->getImageForm();
        $pwdFrm = $this->getPasswordForm();
        $this->set('imgForm', $imgForm);
        $this->set('pwdFrm', $pwdFrm);
        $this->set('data', $data);
        $this->set('clss', 'chg_pass');

        $this->_template->render();
    }

    private function commonCheckForUpdateProfile() {

        $data = $this->_adminProfileObj->getAttributesById($this->_adminId);
        if (!$data) {
            Message::addErrorMessage(Info::t_lang('LOGIN_ERROR_ACCOUNT_LOGIN_NOTES'));
            FatApp::redirectUser(FatUtility::generateUrl('adminGuest', 'login'));
        }

        return $data;
    }

    private function getRoleName($roleId) {

        if ($roleId > 0) {
            $adminRoleObj = new Roles( );
            return $adminRoleObj->getAttributesById($roleId, Roles::DB_TBL_PREFIX . 'name');
        } elseif ($roleId === -1) {
            return Info::t_lang('ROLE_NAME_ADMINISTRATOR');
        }

        return false;
    }

    private function getImageForm() {
        $frm = new Form('frmProfImage');
        $frm->setValidatorJsObjectName('imageValidator');
        $frm->addFileUpload('', 'admin_avtar');
        return $frm;
    }

    private function getProfileFrm() {

        $frm = new Form('frmProfFrm');
        
        $frm->addRequiredField("Admin Name", 'admin_name');

        $adminEmailFld = $frm->addEmailField('Admin Email', 'admin_email');
        $adminEmailFld->requirements()->setRequired();
        $adminEmailFld->setUnique(AdminUsers::DB_TBL, AdminUsers::DB_TBL_PREFIX . 'email', AdminUsers::DB_TBL_PREFIX . 'id', AdminUsers::DB_TBL_PREFIX . 'id', AdminUsers::DB_TBL_PREFIX . 'id');

        $frm->addSubmitButton("", 'btn_submit',"Update");
        return $frm;
    }

    private function getPasswordForm() {

        $frm = new Form('getPwdFrm');
        $frm->addPasswordField('Current Password', 'current_password')->requirements()->setRequired();
        $newPasswordFld = $frm->addPasswordField("New Password", 'new_password');
        $newPwdReq = $newPasswordFld->requirements();
        $newPwdReq->setRequired();
        $newPwdReq->setLength(8, 30);
        //$newPwdReq->setRegularExpressionToValidate( "(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}" );

        $conNewPwd = $frm->addPasswordField('Confirm Password', 'conf_new_password');
        $conNewPwdReq = $conNewPwd->requirements();
        $conNewPwdReq->setRequired();
        $conNewPwdReq->setCompareWith('new_password', 'eq');

        $frm->addSubmitButton("", 'btn_submit','Update');

        return $frm;
    }

  

    public function update()
	{
        $post = FatApp::getPostedData();
        $postedAdminId = FatUtility::convertToType($this->_adminId, FatUtility::VAR_INT);
        if ($postedAdminId <= 0) {
            Message::addErrorMessage(Info::t_lang('FRM_ERROR_INVALID_USER_REQUEST'));
            FatApp::redirectUser(FatUtility::generateUrl('Profile'));
        }

        $frm = $this->getProfileFrm();
        $data = array();
        $data[AdminUsers::DB_TBL_PREFIX . 'id'] = $postedAdminId;
        $data[AdminUsers::DB_TBL_PREFIX . 'email'] = $post['admin_email'];
        $data[AdminUsers::DB_TBL_PREFIX . 'name'] = $post['admin_name'];

        if (!$frm->validate($post)) {
            Message::addErrorMessage($frm->getValidationErrors());
        } else {

            $adminUserObj = new AdminUsers($postedAdminId);
            $previousData = $adminUserObj->getAttributesById($postedAdminId);
            $adminUserObj->assignValues($data);

            if ($adminUserObj->save()) {
                Message::addMessage("Data Updated Successfully");
            } else {
                Message::addErrorMessage($adminUserObj->getError());
            }
        }

        FatApp::redirectUser(FatUtility::generateUrl('Profile'));
    }

    public function updatePassword()
	{
		if ($this->canEdit === false)
            $this->notAuthorized();
		
		$postedAdminId = FatUtility::convertToType($this->_adminId, FatUtility::VAR_INT);
        if ($postedAdminId <= 0) {
            Message::addErrorMessage(Info::t_lang('FRM_ERROR_INVALID_USER_REQUEST'));
            FatApp::redirectUser(FatUtility::generateUrl('Profile'));
        }
		
		$post = FatApp::getPostedData();

        $frm = $this->getPasswordForm();
        if (!$frm->validate($post)) {
            Message::addErrorMessage($frm->getValidationErrors());
        } else {
            $existingPwd = $this->_adminProfileObj->getAttributesById($postedAdminId, AdminUsers::DB_TBL_PREFIX . 'password');
            $postedExistingPwd = User::encryptPassword($post['current_password']);
            $newPassword = User::encryptPassword($post['new_password']);

            if ($existingPwd !== $postedExistingPwd) {
                Message::addErrorMessage(Info::t_lang('FRM_ERROR_PASSWORD_NOT_MATCHED_WITH_DB'));
            } elseif ($newPassword === $postedExistingPwd) {
                Message::addErrorMessage(Info::t_lang('FRM_ERROR_NEW_PASSWORD_SAME'));
            } else {
                $data = array();

                $data[AdminUsers::DB_TBL_PREFIX . 'id'] = $postedAdminId;
                $data[AdminUsers::DB_TBL_PREFIX . 'password'] = $newPassword;

                $adminUserObj = new AdminUsers($postedAdminId);
				$adminUserObj->assignValues($data);

                if ($adminUserObj->save()) {
					/* $previousData = $adminUserObj->getAttributesById($postedAdminId);
                    $website_url = FatUtility::getUrlScheme();

                    EmailHandler::sendMailTpl($previousData[AdminUsers::DB_TBL_PREFIX . 'email'], 'admin_user_password_changed_by_user', array(
                        '{website_name}' => FatApp::getConfig("CONF_WEBSITE_NAME"),
                        '{website_url}' => $website_url,
                        '{site_domain}' => FatUtility::generateFullUrl('', '', array(), '/'),
                        '{admin_user_full_name}' => trim($previousData[AdminUsers::DB_TBL_PREFIX . 'name']),
                        '{admin_user_name}' => trim($previousData[AdminUsers::DB_TBL_PREFIX . 'username']),
                        '{admin_user_password}' => $post['new_password'],
                        '{admin_user_login_url}' => FatUtility::generateFullUrl('AdminGuest', 'login', array(), '/admin/')
                    )); */
					Message::addMessage(Info::t_lang('SUCCESS_PASSWORD_UPDATED'));
                } else {
                    Message::addErrorMessage($adminUserObj->getError());
                }
            }
        }

        FatApp::redirectUser(FatUtility::generateUrl('profile', 'changePassword'));
    }

    public function updateProfileImage()
	{
		if ($this->canEdit === false)
            $this->notAuthorized();

        if (!FatUtility::isAjaxCall()) {
            FatUtility::dieJsonError(CommonHelper::getLabel('FRM_ERROR_INVALID_REQUEST'));
        }
        
		$file = $_FILES['admin_avtar'];

        if (!is_uploaded_file($file['tmp_name'])) {
            Message::addErrorMessage(Info::t_lang('ERROR_SELECT_PROFILE_IMAGE'));
            FatUtility::dieJsonError(Message::getHtml());
        }

        if (!AttachedFile::isValidUploadedImage($file)) {
            Message::addErrorMessage(Info::t_lang('IMAGE_ERROR_COULD_NOT_RECOGNIZED'));
            FatUtility::dieJsonError(Message::getHtml());
        }

        $fileHandlerObj = new AttachedFile;

        // Getting old image which need to delete after successfull uploading of new image.
        $row = AttachedFile::getAttachment(AttachedFile::FILETYPE_ADMIN_USER_IMG, $this->_adminId);
        $isPng = AttachedFile::isPngImage($file);

        if (!$res = $fileHandlerObj->saveImage($file['tmp_name'], AttachedFile::FILETYPE_ADMIN_USER_IMG, $this->_adminId, '', $file['name'], 0, true, AttachedFile::ADMIN_USERS_IMG_FOLDER, $isPng)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }

        // deleting old image from folder
        if ($row['afile_physical_path']) {
            $filePath = CONF_UPLOADS_PATH . AttachedFile::ADMIN_USERS_IMG_FOLDER . $row['afile_physical_path'];
            if (file_exists($filePath))
                @unlink($filePath);
        }

        Message::addMessage(Info::t_lang('FRM_ERROR_PROFILE_PHOTO_UPLOADED'));
        FatUtility::dieJsonSuccess(Message::getHtml());
    }

}