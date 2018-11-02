<?php

class UserController extends MyAppController
{

    protected $userId;
    protected $user_type;

    const PAGESIZE = 20;

    public function __construct($action)
    {
        parent::__construct($action);
        if (!User::isUserLogged()) {
            if ($this->_controllerName == 'WishlistController' && $action == 'addToWish') {
                $_SESSION['login_as'] = 'traveler';
            }
            //if($action != 'form' && $this->_controllerName =='MessageController'){
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Session seems to be expired!');
            }
            FatApp::redirectUser(FatUtility::generateUrl('GuestUser', 'loginForm'));
            //}
        }
        $this->userId = User::getLoggedUserId();
        $this->user_type = User::getLoggedUserAttribute("user_type");
        $user_verified = User::getLoggedUserAttribute("user_verified");

        if ($user_verified != 1) {
            $_SESSION[User::SESSION_ELEMENT_NAME]['email_verify_msg'] = Info::t_lang('EMAIL_NOT_VERIFIED.') . '<a href="javascript:;" class="link" onclick="resendVerification()">' . Info::t_lang('CLICK_HERE_') . '</a>' . Info::t_lang('TO_RESEND_EMAIL_VERFICATION_LINK.');
        } elseif (isset($_SESSION[User::SESSION_ELEMENT_NAME]['email_verify_msg'])) {
            unset($_SESSION[User::SESSION_ELEMENT_NAME]['email_verify_msg']);
        }
        if ($this->user_type == 1 && $action != 'logout') {
            $act = new Activity();
            #	var_dump($act->isHostHaveActivity($this->userId));exit;
            /* $validAction = array('action','update','step1','setup1','subService');
              if(!$act->isHostHaveActivity($this->userId)){
              if(!in_array($action,$validAction)){
              FatApp::redirectUser(FatUtility::generateUrl('hostactivity', 'update',array(0)));
              }
              } */
        }

        if ($this->user_type == 1) {
            if (in_array($this->_controllerName, array('TravelerController'))) {
                FatApp::redirectUser(FatUtility::generateUrl('host'));
            }
        } else {
            if (in_array($this->_controllerName, array('HostactivityController', 'HostController', 'HostReportsController'))) {
                FatApp::redirectUser(FatUtility::generateUrl('traveler'));
            }
        }

        $this->set('user_type', $this->user_type);
        $this->set('user_id', $this->userId);
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        unset($_SESSION);
        FatApp::redirectUser(FatUtility::generateUrl('GuestUser', 'loginForm'));
    }

    protected function profileForm()
    {
        $frm = $this->getBasicProfileForm();
        $usr = new User($this->userId);
        $usr->loadFromDb();
        $userData = $usr->getFlds();
        $frm->fill($usr->getFlds());

        //Country Code
        if (isset($userData['user_country_id']) && $userData['user_country_id'] > 0) {

            $fc = new Country($userData['user_country_id']);
            $fc->loadFromDb();
            $countryData = $fc->getFlds();
            $fld = $frm->getField('user_phone');
            $fld->htmlBeforeField = "<span id='country_code' class='field_add-on add-on--left'>+" . $countryData['country_phone_code'] . "</span>";
        }
        $this->set('frm', $frm);
    }

    protected function getBasicProfileForm()
    {

        $frm = new Form('basicProfileFrm');
        $frm->addRequiredField(Info::t_lang('FIRST_NAME'), 'user_firstname');
        $frm->addRequiredField(Info::t_lang('LAST_NAME'), 'user_lastname');
        $frm->addTextBox(Info::t_lang('EMAIL'), 'user_email', '', array('disabled' => 'disabled'));
        $countries = Country::getCountries();
        $frm->addSelectBox(Info::t_lang('COUNTRY'), 'user_country_id', $countries, '', array('onchange' => "loadCountryCodes(this)"), '');



        $user_phone = $frm->addTextBox('', 'user_phone');
        $user_phone->htmlBeforeField = "<span id='country_code'></span>";
        $user_phone->captionWrapper = array(Info::t_lang('PHONE_NO') . ' <a href="#phone-instr" class="phone-instruction"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');
        if ($this->user_type == 1) {
            $fld = $frm->addTextBox('', 'user_alternate_email');
            $fld->requirements()->setEmail();
            $fld->captionWrapper = array(Info::t_lang('ALTERNATE_EMAIL') . ' <a href="#second-email-instruction" class="second-email"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');

            $frm->addTextBox(Info::t_lang('COMPANY'), 'user_company');
            $frm->addTextBox(Info::t_lang('WEBSITE'), 'user_website');
            $user_description = $frm->addTextArea('', 'user_description');
            $user_description->captionWrapper = array(Info::t_lang('INTRODUCE_YOURSELF') . ' <a href="#introduce-yourself-instruction" class="introduce-yourself"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');
            $user_description->htmlAfterField = '<small>' . Info::t_lang('DESCRIBE_YOURSELF_IN_2-3_SENTENCES_TO_ENGAGE_TRAVELER') . '</small>';
            $user_description->requirements()->setRequired();
            $user_description->requirements()->setCustomErrorMessage(Info::t_lang('INTRODUCE_YOURSELF_IS_MANDATORY_FIELD'));
        }


        if ($this->user_type == 0) {
            $frm->addTextArea(Info::t_lang('ABOUT_YOURSELF'), 'user_about');
        }
        $frm->addSubmitButton(Info::t_lang('UPDATE'), 'submit_btn', Info::t_lang('UPDATE'));
        return $frm;
    }

    function updateProfile()
    {
        $frm = $this->getBasicProfileForm();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (isset($post['user_email']))
            unset($post['user_email']);

        //Country Code
        if (isset($post['user_country_id']) && $post['user_country_id'] > 0) {

            $fc = new Country($post['user_country_id']);
            $fc->loadFromDb();
            $countryData = $fc->getFlds();
            $post['user_phone_code'] = $countryData['country_phone_code'];
        }
        
        $usr = new User($this->userId);
        $usr->assignValues($post);
        if (!$usr->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!._PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(Info::t_lang('PROFILE_UPDATED_SUCCESSFULLY'));
    }

    public function profileImageForm()
    {
        $frm = $this->getProfileImageForm();
        $this->set('frm', $frm);
    }

    public function removeImage()
    {
        if (!AttachedFile::removeFiles(AttachedFile::FILETYPE_USER_PHOTO, $this->userId)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
        }
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('PROFILE_PICTURE_REMOVED!'), 'src' => FatUtility::generateUrl('image', 'user', array($this->userId, 200, 200, rand(111, 999)))));
    }

    public function setupProfileImage()
    {
        if (empty($_FILES['photo']['tmp_name']) || !is_uploaded_file($_FILES['photo']['tmp_name'])) {
            FatUtility::dieJsonError(Info::t_lang("Image couldn\'t not uploaded."));
        }
        $attachment = new AttachedFile();
        if (!$attachment->uploadAndSaveFile('photo', AttachedFile::FILETYPE_USER_PHOTO, $this->userId, 0, 0, true)) {
            FatUtility::dieJsonError($attachment->getError());
        }
        $data = json_decode(stripslashes($_POST['img-data']));
        $image_data = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PHOTO, $this->userId, $recordSubid = 0);
        Helper::crop($data, CONF_INSTALLATION_PATH . 'user-uploads/' . $image_data['afile_physical_path']);
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('PROFILE_PICTURE_UPDATED!'), 'src' => FatUtility::generateUrl('image', 'user', array($this->userId, 200, 200, rand(111, 999)))));
    }

    protected function getProfileImageForm()
    {
        $frm = new Form('imageUploadFrm');
        $frm->addHiddenField('', 'fIsAjax', 1);
        $frm->addHiddenField('', 'img-data', '', array('id' => 'img-data'));
        $frm->addHtml(Info::t_lang('PHOTO'), 'profile_photo', "<div class='img-uploader'><img id='profile_photo' src='" . FatUtility::generateUrl('image', 'user', array($this->userId, 200, 200)) . "'><label for='img-uploader' class='upload-label'>" . Info::t_lang('UPLOAD_IMAGE') . "</label></div>");



        $frm->addHtml('', 'remove_img', '');
        $fld = $frm->addFileUpload('', 'photo', array('id' => 'img-uploader', 'style' => 'visibility:hidden; opacity:0; width:0px; height:0px;'));

        //$fld->setWrapperAttribute('style','display:none');
        return $frm;
    }

    function passwordForm()
    {
        $frm = $this->getPasswordForm();
        $this->set('frm', $frm);
    }

    function passwordSetup()
    {
        $post = FatApp::getPostedData();
        $frm = $this->getPasswordForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $usr = new User($this->userId);
        $usr->loadFromDb();
        $db_password = $usr->getFldValue(User::DB_TBL_PREFIX . 'password');
        $current_password = User::encryptPassword($post['current_password']);
        if ($db_password !== $current_password) {
            FatUtility::dieJsonError(Info::t_lang('WRONG_CURRENT_PASSWORD!'));
        }
        $data[User::DB_TBL_PREFIX . 'password'] = User::encryptPassword($post['user_password']);
        $usr->assignValues($data);
        if (!$usr->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
        }
        FatUtility::dieJsonSuccess(Info::t_lang('PASSWORD_UPDATED_SUCCESSFULLY!'));
    }

    protected function getPasswordForm()
    {
        $frm = new Form('frmUpdatePassword');
        $frm->addHiddenField('', 'fIsAjax', 1);
        $frm->addPasswordField(Info::t_lang('CURRENT_PASSWORD'), 'current_password')->requirements()->setRequired();
        $frm->addPasswordField(Info::t_lang('NEW_PASSWORD'), 'user_password')->requirements()->setRequired();
        $fld = $frm->addPasswordField(Info::t_lang('CONFIRM_PASSOWRD'), 'confirm_password');
        $fld->requirements()->setRequired();
        $fld->requirements()->setCompareWith('user_password', 'eq');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    /*
     * Return The Specific Country Code
     */

    public function getCountryCodes($countryId)
    {

        if (!FatUtility::isAjaxCall()) {
            die("Invalid Access");
        }

        $fc = new Country($countryId);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }
        $data = $fc->getFlds();
        $phoneCode = $data['country_phone_code'];
        FatUtility::dieJsonSuccess($phoneCode);
    }

}
