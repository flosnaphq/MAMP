<?php

class UsersController extends AdminBaseController
{

    private $admin_id;

    const PAGESIZE = 50;

    public function __construct($action)
    {
        /* $ajaxCallArray = array("lists","update",'form');
          if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
          die("Invalid Action");
          } */
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");

        parent::__construct($action);
    }

    public function profile($user_id)
    {
        $user_id = FatUtility::int($user_id);
        $user_type = Users::getAttributesById($user_id, 'user_type');
        $canView = false;
        $canEditProfile = false;
        if ($user_type == 1) {
            $canView = AdminPrivilege::canViewHost($this->admin_id);
            $canEditProfile = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canView = AdminPrivilege::canViewTraveller($this->admin_id);
            $canEditProfile = AdminPrivilege::canEditTraveller($this->admin_id);
        }
        if (!$canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        if ($user_id < 0) {
            FatUtility::dieJsonError('Something went wrong!');
        }
        $fc = new Users($user_id);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }
		
        $this->set('record', $fc->getFlds());
        $this->set('userImageExist', $this->checkUserImageExist($user_id));
        $this->set('canEditProfile', $canEditProfile);
        $htm = $this->_template->render(false, false, "users/_partial/profile.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }
	
	private function checkUserImageExist($user_id = 0) {
		$img = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PHOTO, $user_id);
        $img_name = $img['afile_physical_path'];
		if ($img_name != "" && file_exists(CONF_UPLOADS_PATH . $img_name)) {
			return true;
		} else {
			return false;
		}
	}
	
    public function index()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add("User Management");
        $frm_1 = $this->getUserSearchForm(1);
        $frm_3 = $this->getUserSearchForm(3);
        $this->set('breadcrumb', $brcmb->output());
        $this->set('frm_1', $frm_1);
        $this->set('frm_3', $frm_3);
        $this->_template->render();
    }

    public function lists($page = 1, $tab = 1)
    {
        $tab = FatUtility::int($tab);
        $page = FatUtility::int($page);
        $tab = $tab == 0 ? 1 : $tab;
        $page = $page == 0 ? 1 : $page;
        $form = $this->getUserSearchForm($tab);
        $post = $form->getFormDataFromArray(FatApp::getPostedData());
        $tbl = new Users();
        $search = $tbl->getSearch($page, static::PAGESIZE);
        if (!empty($post['keyword'])) {
            $key_con = $search->addCondition('ud.udetails_first_name', 'like', '%' . $post['keyword'] . '%');
            $key_con->attachCondition('ud.udetails_last_name', 'like', '%' . $post['keyword'] . '%', 'or');
            $key_con->attachCondition('u.user_email', 'like', '%' . $post['keyword'] . '%', 'or');
        }
        if (!empty($post['location'])) {
            $key_con = $search->addCondition('rl.region_name', 'like', '%' . $post['location'] . '%');
            $key_con->attachCondition('c.city_name', 'like', '%' . $post['location'] . '%', 'or');
            $key_con->attachCondition('udetails_address1', 'like', '%' . $post['location'] . '%', 'or');
            $key_con->attachCondition('udetails_address2', 'like', '%' . $post['location'] . '%', 'or');
        }
        if (isset($post['user_active']) && $post['user_active'] != '' && $post['user_active'] != -1) {
            $user_active = FatUtility::int($post['user_active']);
            $search->addCondition('user_active', '=', $user_active);
        }
        if ($tab == 1) {
            $search->addCondition('user_verified', '=', 0);
        } elseif (isset($post['user_verified']) && $post['user_verified'] != '' && $post['user_verified'] != -1) {
            $user_verified = FatUtility::int($post['user_verified']);
            $search->addCondition('user_verified', '=', $user_verified);
        }

        $search->addCondition('user_is_merchant', '=', 0);


        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('tab', $tab);
        $this->set('postedData', $post);
        $this->set('uploadForm', $this->getImageUploadForm());
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "users/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function view($user_id)
    {
        $user_id = FatUtility::int($user_id);
        if ($user_id <= 0) {
            FatUtility::dieWithError('Invalid Request!');
        }
        $tbl = new Users();
        $record = $tbl->getUser($user_id);
        $this->set('records', $record);
        $this->_template->render(false, false, 'users/_partial/view.php');
    }

    /* public function photo($user_id, $w, $h) {
      $user_id = FatUtility::int($user_id);
      $row = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PHOTO, $user_id);
      ob_end_clean();
      if ( !empty($row) ) {
      $headers = FatApp::getApacheRequestHeaders();
      if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime(CONF_UPLOADS_PATH . $row ['afile_physical_path']))) {
      header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime(CONF_UPLOADS_PATH . $row ['afile_physical_path'])).' GMT', true, 304);
      exit;
      }
      try {
      $img = new ImageResize ( CONF_UPLOADS_PATH . $row ['afile_physical_path'] );

      header('Cache-Control: public');
      header("Pragma: public");
      header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime(CONF_UPLOADS_PATH . $row ['afile_physical_path'])).' GMT', true, 200);
      header("Expires: " . date('r', strtotime("+30 Day")));

      }
      catch (Exception $e) {
      $img = new ImageResize(CONF_THEME_PATH . 'img/no-photo.png');
      }
      }
      else {
      $img = new ImageResize(CONF_THEME_PATH . 'img/no-photo.png');
      }
      $w = max(1, FatUtility::int($w));
      $h = max(1, FatUtility::int($h));
      $img->setMaxDimensions($w, $h);
      $img->displayImage();
      } */

    private function getImageUploadForm()
    {
        $frm = new Form('form_image_upload', array('class' => 'web_form', 'action' => FatUtility::generateUrl('users', 'upload-photo')));
        $frm->addHiddenField('', 'user_id')->requirements()->setRequired();
        $frm->addFileUpload(Info::t_lang('Change'), 'photo');
        return $frm;
    }

    function bankAccountForm()
    {
        if (!AdminPrivilege::canViewBankAccount($this->admin_id)) {
            FatUtility::dieJsonError('Unauthorized Access');
        }
        $post = FatApp::getPostedData();
        if (!(isset($post['user_id']) && FatUtility::int($post['user_id']) != 0)) {
            FatUtility::dieJsonError("Invalid User");
        }
        $user = new Users($post['user_id']);
        $user->loadFromDb();
        $user_data = $user->getFlds();
        $user = new Users($post['user_id']);
        $user_type = Users::getAttributesById($post['user_id'], 'user_type');
        $canEdit = false;

        $frm = $this->getPayoutForm($post['user_id']);
        $bnkact = new BankAccounts();
        $data = (array) $bnkact->getBankAccount($post['user_id']);
        $data = array_merge($data, $user_data);
        $frm->fill($data);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'users/_partial/bank-account.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getPayoutForm($userId)
    {
        $frm = new Form('payoutFrm');
        $frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_WITH_CAPTION);

        $frm->addHiddenField('', 'bankaccount_user_id');
        $frm->addRequiredField(Info::t_lang('BANK_NAME'), 'bankaccount_bank_name');
        $frm->addRequiredField(Info::t_lang('BRANCH'), 'bankaccount_branch');
        $frm->addRequiredField(Info::t_lang('ACCOUNT_NUMBER'), 'bankaccount_account_no');
        $frm->addRequiredField(Info::t_lang('ACCOUNT_NAME'), 'bankaccount_account_name');


        $fld = $frm->addTextArea(Info::t_lang('ACCOUNT_ADDRESS'), 'bankaccount_account_address');
        $fld->requirements()->setRequired();

        $frm->addRequiredField(Info::t_lang('IFSC_CODE'), 'bankaccount_ifsc_code');
        $user_type = Users::getAttributesById($userId, 'user_type');
        if ($user_type == 1) {
            $frm->addFloatField('Commission', 'user_commission');
        }

        if (AdminPrivilege::canEditBankAccount($this->admin_id)) {
            $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SAVE'));
        }
        return $frm;
    }

    function setupBankAccount()
    {
        if (!AdminPrivilege::canEditBankAccount($this->admin_id)) {
            FatUtility::dieJsonError('Unauthorized Access');
        }
		
        $post = FatApp::getPostedData();
        $user_id = $post['user_id'];
        $frm = $this->getPayoutForm($user_id);
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(cuurent($frm->getValitionErrors()));
        }
        $post['bankaccount_user_id'] = $user_id;
        $bnkact = new BankAccounts();
        if (!$bnkact->saveBankAccount($post)) {
            FatUtility::dieJsonError('Something Went Wrong. Please Try Again');
        }


        $user = new Users($user_id);
        $user->loadFromDb();
        $user_data = $user->getFlds();

        $user_type = Users::getAttributesById($user_id, 'user_type');


        $user->assignValues($post);

        if (!$user->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        if ($user_data['user_commission'] != $post['user_commission'] && $user_type == 1) {
            $notify = new Notification();

            $notify->notify($user_id, 0, 'host/payout', Info::t_lang('ADMIN_UPDATE_YOUR_FEE_CHARGES'));
        }
        FatUtility::dieJsonSuccess('Bank Details Saved');
    }

    function edit()
    {
        $post = FatApp::getPostedData();
        if (!(isset($post['user_id']) && FatUtility::int($post['user_id']) != 0)) {
            FatUtility::dieJsonError("Invalid User");
        }
        $user = new Users($post['user_id']);
        $user_type = Users::getAttributesById($post['user_id'], 'user_type');
        $canEdit = false;
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }
        if (!$user->loadFromDb()) {
            FatUtility::dieJsonError("Invalid User");
        }

        $detail = $user->getFlds();
        $frm = $this->getEditForm();
        if ($user_type != 1) {
            $frm->removeField($frm->getField('user_alternate_email'));
            $frm->removeField($frm->getField('user_company'));
        }
        if (!$canEdit) {
            $frm->removeField($frm->getField('btn_submit'));
        }

        //Country Code
        if (isset($detail['user_country_id']) && $detail['user_country_id'] > 0) {

            $fc = new Countries($detail['user_country_id']);
            $fc->loadFromDb();
            $countryData = $fc->getFlds();
            $fld = $frm->getField('user_phone');
            $fld->htmlBeforeField = "<span id='country_code'>" . $countryData['country_phone_code'] . "</span>";
        }
        $frm->fill($detail);
        $this->set("frm", $frm);
        $htm = $this->_template->render(false, false, "users/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function password()
    {
        $post = FatApp::getPostedData();
        $user_id = isset($post['user_id']) ? FatUtility::int($post['user_id']) : 0;
        if ($user_id <= 0) {
            FatUtility::dieJsonError('Invalid request!');
        }
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }

        $frm = MyHelper::getPasswordForm();
        if (!$canEdit) {
            $frm->removeField($frm->getField('submit_btn'));
        }
        $data = array('user_id' => $user_id);
        $frm->fill($data);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'users/_partial/password-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function updatePassword()
    {
        $post = FatApp::getPostedData();
        $frm = MyHelper::getPasswordForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError('Something went Wrong!');
        }
        $user_id = isset($post['user_id']) ? FatUtility::int($post['user_id']) : 0;
        if ($user_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $user_password = UserAuthentication::encryptPassword($post['user_password']);
        $data['user_password'] = $user_password;
        $user_id = $post['user_id'];
        $user = new Users($post['user_id']);
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }
        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        if (!$user->loadFromDb()) {
            FatUtility::dieJsonError("Invalid User");
        }
        $user->assignValues($data);

        if (!$user->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        /* if(!$user->removeAuthToken($user_id)){
          FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
          } */
        /* 	$login_url = FatUtility::generateFullUrl('guest','guest-login',array(),'/');
          Email::sendMail($user_detail['user_email'],53,array(
          '{site_name}'=>FatApp::getConfig('conf_website_name'),
          '{login_url}'=>$login_url,
          '{username}'=>$user_detail['udetails_first_name'].' '.$user_detail['udetails_last_name'],
          '{password}'=>$post['user_password'],
          ));

          if(!empty($user_parent_id)){
          Email::sendMail($user_detail['user_email'],54,array(
          '{site_name}'=>FatApp::getConfig('conf_website_name'),
          '{login_url}'=>$login_url,
          '{username}'=>$user_parent_detail['udetails_first_name'].' '.$user_parent_detail['udetails_last_name'],
          '{password}'=>$post['user_password'],
          '{sub_user}'=>$user_detail['udetails_first_name'].' '.$user_detail['udetails_last_name'],
          ));
          } */
        FatUtility::dieJsonSuccess('Password Successfully Updated');
    }

    function updateDetail()
    {
        $canEdit = false;
        $post = FatApp::getPostedData();
        $frm = $this->getEditForm();
        /* $post = $frm->getFormDataFromArray($post);
          if($post == false){
          FatUtility::dieJsonError('Something went Wrong!');
          } */
        $data = $post;
        $userId = FatApp::getPostedData('user_id', FatUtility::VAR_INT);
        unset($data['user_id']);
        $user = new User($userId);

        $user_type = Users::getAttributesById($userId, 'user_type');
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }
        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }

        if (isset($data['user_country_id']) && $data['user_country_id'] > 0) {

            $fc = new Country($post['user_country_id']);
            $fc->loadFromDb();
            $countryData = $fc->getFlds();
            $data['user_phone_code'] = $countryData['country_phone_code'];
        }

        $user->assignValues($data);

        if (!$user->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        /* if($user_data['user_commission'] != $data['user_commission'] && $user_type == 1){
          $notify = new Notification();

          $notify->notify($userId,0,'host/payout',Info::t_lang('ADMIN_UPDATE_YOUR_FEE_CHARGES'));
          } */
        FatUtility::dieJsonSuccess('User Successfully Updated');
    }

    function getEditForm()
    {
        $frm = new Form('editform', array('class' => 'web_form', 'id' => 'action_form'));
        $frm->addHiddenField('', 'user_id');
        $frm->addRequiredField('First name', 'user_firstname');
        $frm->addTextBox('Last name', 'user_lastname');
        $email_field = $frm->addEmailField('Email', 'user_email');
        $user_alternate_email = $frm->addTextBox('Alternate Email', 'user_alternate_email');
        $user_alternate_email->requirements()->setEmail();
        $email_field->requirements()->setRequired();
        $email_field->setFieldTagAttribute('id', 'user_email');
        $email_field->setUnique('tbl_users', 'user_email', 'user_id', 'user_email', 'user_email');
        // $user_types = Info::getUserType();
        //$frm->addSelectBox('User Type', 'user_type', $user_types);
        $locations = Countries::getCountries();
        $frm->addSelectBox("Country", 'user_country_id', $locations, '', array('onChange' => 'javascript:loadCountryCodes(this)'));

        //$user_phone_code->developerTags['noCaptionTag'] = true;
        $fld = $frm->addTextBox('Phone number', 'user_phone');
        $fld->htmlBeforeField = "<span id='country_code'></span>";
        $fld->requirements()->getInt();
        $frm->addTextBox('Company', 'user_company');
        $frm->addSelectBox('Status', 'user_active', Info::getStatus());
        $frm->addSelectBox('Email Verify', 'user_verified', Info::getEmailStatus());

        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Update');
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

        $fc = new Countries($countryId);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }
        $data = $fc->getFlds();

        FatUtility::dieJsonSuccess($data['country_phone_code']);
    }

    static function getPasswordForm()
    {
        $frm = new Form('passwordForm');
        $frm->addHiddenField('', 'user_id');
        $frm->addPasswordField('New Password', 'user_password')->requirements()->setRequired();
        $cpwd = $frm->addPasswordField('Confirm Password', 'cpassword');
        $cpwd->requirements()->setRequired();
        $cpwd->requirements()->setCompareWith('user_password', 'eq');
        return $frm;
    }

    function uploadPhoto()
    {
        $post = FatApp::getPostedData();
        $form = $this->getImageUploadForm();
        $post = $form->getFormDataFromArray($post);
        $user_type = Users::getAttributesById($post['user_id'], 'user_type');
        $canEdit = false;
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }

        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        if (!isset($_FILES['photo']['tmp_name'])) {
            FatUtility::dieJsonError('Photo is mandatory Field');
        }
        if (!is_uploaded_file($_FILES['photo']['tmp_name'])) {
            FatUtility::dieJsonError('Photo couldn\'t upload.');
        }

        if ($post == false) {
            FatUtility::dieJsonError($form->getValidationErrors());
        }
        $post['user_id'] = FatUtility::int($post['user_id']);
        if (empty($post['user_id'])) {
            FatUtility::dieJsonError('Invalid request!');
        }

        $attachment = new AttachedFile();
        if ($attachment->saveImage($_FILES['photo']['tmp_name'], AttachedFile::FILETYPE_USER_PHOTO, $post['user_id'], 0, $_FILES['photo']['name'], 0, true)) {
            FatUtility::dieJsonSuccess('Photo Updated!');
        } else {
            FatUtility::dieJsonError($attachment->getError());
        }
    }

    /*
      function form(){
      if(!$this->canEdit){
      FatUtility::dieJsonError('Unauthorized Access!');
      }
      $post = FatApp::getPostedData();
      if(empty($post['user_id'])){
      FatUtility::dieJsonError('Invalid request!');
      }
      $user_id = FatUtility::int($post['user_id']);
      $form = $this->getForm();
      $tbl = new Users();
      $data = $tbl->getUser($user_id);
      $form->fill($data);
      $this->set('frm',$form);
      $html = $this->_template->render(false,false,'users/_partial/form.php',true,false);
      FatUtility::dieJsonSuccess($html);
      }
     */

    function update()
    {
        $post = FatApp::getPostedData();
        $user_type = Users::getAttributesById($post['user_id'], 'user_type');
        $canEdit = false;
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }

        if (!$canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }

        if ($post['action'] == "avtar") {

            $userId = $post['user_id'];
            $attached = new AttachedFile();
            if ($attached->saveExistAttachment($post['response'], AttachedFile::FILETYPE_USER_PHOTO, $userId, 0, $post['response'], 0, true)) {
                
            }
            $data = json_decode(stripslashes($post['img_data']));
            Helper::crop($data, CONF_UPLOADS_PATH . $post['response']);

            FatApp::redirectUser($_SERVER['HTTP_REFERER']);
        }
        if ($post['action'] == "demo_avatar") {
            if (AttachedFile::uploadImage($_FILES['user_image']['tmp_name'], $_FILES['user_image']['name'], $response)) {
                $link = FatUtility::generateUrl("image", "crop", array($response), CONF_WEBROOT_URL);
                $resp = array("link" => $link, "response" => $response, "status" => 1);
                die(FatUtility::convertToJson($resp));
            } else {
                FatUtility::dieJsonError($response);
            }
        }
    }

    private function getUserSearchForm($tab = 1)
    {
        $status = Info::getSearchUserStatus();
        $status['-1'] = 'Does not Matter';
        $verified = Info::getEmailStatus();
        $verified['-1'] = 'Does not Matter';
        $user_type = Info::getUserType();
        $user_type['-1'] = 'Does not Matter';
        if ($tab == 1) {
            $frm = new Form('frmUserSearch_tab_1', array('class' => 'web_form', 'onsubmit' => 'search(this,1); return false;'));
            $frm->addTextBox('Name or Email', 'keyword', '', array('class' => 'search-input'));
            $frm->addSelectBox('Status', 'user_active', $status, '-1', array('class' => 'search-input'), '');
            $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        } elseif ($tab == 2) {
            $frm = new Form('frmUserSearch_tab_2', array('class' => 'web_form', 'onsubmit' => 'search(this, 2); return false;'));
            $frm->addTextBox('Name or Email', 'keyword', '', array('class' => 'search-input'));
            $frm->addSelectBox('Status', 'user_active', $status, '-1', array('class' => 'search-input'), '');
            $frm->addSelectBox('Verified', 'user_verified', $verified, '-1', array('class' => 'search-input'), '');
            $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        } elseif ($tab == 3) {
            $frm = new Form('frmUserSearch_tab_3', array('class' => 'web_form', 'onsubmit' => 'search(this, 3); return false;'));
            $frm->addTextBox('Name or Email', 'keyword', '', array('class' => 'search-input'));
            $frm->addTextBox('Location', 'location', '', array('class' => 'search-input'));
            $frm->addSelectBox('Status', 'user_active', $status, '-1', array('class' => 'search-input'), '');
            $frm->addSelectBox('Verified', 'user_verified', $verified, '-1', array('class' => 'search-input'), '');
            $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        }


        return $frm;
    }

    private function removeUserImage($user_id)
    {
        Helper::deleteMultipleAttachedFile(AttachedFile::FILETYPE_USER_PHOTO, $user_id);
        return true;
    }

    public function removeImage()
    {
        $data = FatApp::getPostedData();
        $user_type = Users::getAttributesById($data['user_id'], 'user_type');
        $canEdit = false;
        if ($user_type == 1) {
            $canEdit = AdminPrivilege::canEditHost($this->admin_id);
        } elseif ($user_type == 0) {
            $canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        }

        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $this->removeUserImage($data['user_id']);
        FatUtility::dieJsonSuccess('Image Removed');
    }
	
	public function autoComplete(){
		
		$pagesize = 10;
		$post = FatApp::getPostedData();

		$srch = Users::getSearchObject();
		$srch->addOrder(Users::DB_TBL_PREFIX.'firstname');
		$srch->addFld(Users::DB_TBL_PREFIX.'id');
		$srch->addFld('concat('.Users::DB_TBL_PREFIX.'firstname," ", '.Users::DB_TBL_PREFIX.'lastname) as user_name');
		
		if (!empty($post['keyword']['term'])) {
			$srch->addCondition(Users::DB_TBL_PREFIX.'firstname', 'LIKE', '%' . $post['keyword']['term'] . '%')
			->attachCondition(Users::DB_TBL_PREFIX.'lastname', 'LIKE', '%' . $post['keyword']['term'] . '%');
		}
		
		$srch->addCondition(Users::DB_TBL_PREFIX.'active','=',1);
		$srch->addOrder('user_name');
		$srch->setPageSize($pagesize);
		//echo $srch->getQuery();die;
		$rs = $srch->getResultSet();
		
		$db = FatApp::getDb();
		$hosts = $db->fetchAll($rs,Users::DB_TBL_PREFIX.'id');
		$json = array();
		
		foreach( $hosts as $key => $host ){
			$json[] = array(
				'id' => $key,
				'name'      => strip_tags(html_entity_decode($host['user_name'], ENT_QUOTES, 'UTF-8'))
			);
		}
		die(json_encode($json));
	}		

}
