<?php

class GuestUserController extends MyAppController
{

    public function __construct($action)
    {
        if (User::isUserLogged()) {
            if (!in_array($action, array('resendVerificationEmail', 'verifyEmail'))) {
                if (User::getLoggedUserAttribute("user_type") == 0) {
                    FatApp::redirectUser(FatUtility::generateUrl('traveler'));
                } else {
                    FatApp::redirectUser(FatUtility::generateUrl('host'));
                }
            }
        }
        /* $ajaxCallArray = array('listing','form','setup','cmsDisplaySetup');
          if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
          die("Invalid Action");
          } */
        parent::__construct($action);
        $this->set("controller", "guest");
        $this->set('action', $action);
    }

    function fatActionCatchAll()
    {
        FatUtility::exitWithErrorCode(404);
    }

    public function loginForm($uType = '')
    {
        $loginData['username'] = 'traveler@dummyid.com';
        $loginData['password'] = '123456';
        if ($uType == 'host') {
            $loginData['username'] = 'raj@dummyid.com';
        }

        $frm = $this->getLoginForm();

        $frm->fill($loginData);
        $this->set('frm', $frm);
        $this->_template->render();
    }

    public function social()
    {
        $this->_template->render();
    }

    public function login()
    {
        $authentication = new User();
        if (!$authentication->login(FatApp::getPostedData('username'), FatApp::getPostedData('password'), $_SERVER['REMOTE_ADDR'])) {
            FatUtility::dieJsonError($authentication->getError());
        }
        $this->set('msg', 'Login Successful! Redirecting..');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function omise()
    {
        define('OMISE_PUBLIC_KEY', 'pkey_test_53q8mnpryrjjjc53baa');
        define('OMISE_SECRET_KEY', 'skey_test_53q8mnpryiu8n9c7u2m');
        require_once CONF_INSTALLATION_PATH . 'library/omise/vendor/autoload.php';


        $this->_template->render();
        //			
    }

    /* public function checkout() {
      print_r($_POST);
      define('OMISE_PUBLIC_KEY', 'pkey_test_53q8mnpryrjjjc53baa');
      define('OMISE_SECRET_KEY', 'skey_test_53q8mnpryiu8n9c7u2m');
      require_once CONF_INSTALLATION_PATH . 'library/omise/vendor/autoload.php';
      $charge = OmiseCharge::create(array(
      'amount' => 2000,
      'currency' => 'thb',
      'card' => $_POST['omise_token'],
      'return_uri' => 'http://footloos.4demo.biz/guest-user/response',
      'authorize_uri' => 'http://footloos.4demo.biz/guest-user/response'
      ));
      print_r($charge);
      $this->_template->render();
      //
      }

      public function response() {
      $key = "94b9b12d-3daf-4532-b754-6ef9a067ad2c";
      $secret = "qH2ZWR095EmwUbwd7TnflA==";
      $phone_number = "+66833333603";
      $user = "application\\" . $key . ":" . $secret;
      $message = array("message" => "Test");
      $data = json_encode($message);
      $ch = curl_init('https://messagingapi.sinch.com/v1/sms/' . $phone_number);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_USERPWD, $user);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      $result = curl_exec($ch);
      if (curl_errno($ch)) {
      echo 'Curl error: ' . curl_error($ch);
      } else {
      echo $result;
      }
      curl_close($ch);
      } */

    function changeEmailVerify($token, $user_id)
    {
        $user_id = FatUtility::int($user_id);
        $token = trim($token);
        if ($user_id <= 0 || empty($token)) {
            FatUtility::exitWithErrorCode(404);
        }
        $changeRequest = new EmailChangeRequest();
        $changeRequest->deleteOldRequest($user_id);
        $token_data = $changeRequest->getToken($user_id);
        if (empty($token_data)) {
            FatUtility::exitWithErrorCode(404);
        }

        $db_token = @$token_data[EmailChangeRequest::DB_TBL_PREFIX . 'verification_code'];
        $db_token = trim($db_token);

        if ($token != $db_token) {
            FatUtility::exitWithErrorCode(404);
        }
        $usr = new User($user_id);
        if ($usr->getUserByEmail($token_data[EmailChangeRequest::DB_TBL_PREFIX . 'email_id'])) {
            Message::addErrorMessage(Info::t_lang('EMAIL_ALREADY_EXIST!'));
        }
        $data = array(
            User::DB_TBL_PREFIX . 'email' => $token_data[EmailChangeRequest::DB_TBL_PREFIX . 'email_id'],
            User::DB_TBL_PREFIX . 'verified' => 1,
        );

        $usr->assignValues($data);
        if (!$usr->save()) {
            Message::addErrorMessage(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
            //FatApp::redirectUser(FatUtility::generateUrl());
        }

        Message::addMessage(Info::t_lang('EMAIL_SUCCESSFULLY_UPDATED!_PLEASE_LOGIN_NOW_WITH_YOUR_NEW_EMAIL'));
        if (User::isUserLogged()) {
            FatApp::redirectUser(FatUtility::generateUrl('user', 'logout'));
        }
        FatApp::redirectUser(FatUtility::generateUrl('guest-user', 'login-form'));
    }

    public function signupForm()
    {
        $frm = $this->getRegistrationForm();
        $social_data = Helper::getSocialSession();
        $frm->setFormTagAttribute('action', FatUtility::generateUrl('GuestUser', 'register'));
        $frm->fill($social_data);
        $this->set('frm', $frm);
        $this->_template->render(true, true, 'guest-user/signup-form.php');
    }

    public function hostForm()
    {
        $frm = $this->getRegistrationForm();
        $frm->setFormTagAttribute('action', FatUtility::generateUrl('GuestUser', 'host'));

        $this->set('frm', $frm);
        $this->_template->render(true, true, 'guest-user/host-form.php');
    }

    public function register()
    {
        $frm = $this->getRegistrationForm();
        $post = FatApp::getPostedData();
        $post['user_id'] = 0;
        $user_type = 0;
        $post = $frm->getFormDataFromArray($post);
        $user_type = $post['user_type'];
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $userTypeArr = Info::getUserType();
        if (!array_key_exists($user_type, $userTypeArr)) {
            FatUtility::dieJsonError('.');
            FatUtility::dieJsonError(Info::t_lang("INVALID_USER_TYPE"));
        }

        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') && !Helper::verifyCaptcha($_POST['g-recaptcha-response'])) {
            FatUtility::dieJsonError(Info::t_lang("INCORRECT_SECURITY_CODE"));
        }
        
        $social_data = Helper::getSocialSession(); //user_signup_media
        if (!empty($social_data['user_signup_media']) && in_array($social_data['user_signup_media'], array(1, 2)) && !empty($social_data['user_email']) && $social_data['user_email'] == $post['user_email']) {
            $post['user_verified'] = 1;
        } else {
            $post['user_verified'] = 0;
        }
        $post['user_type'] = $user_type;
        $post['user_active'] = 1;

        $user = new User(0);
        $user->assignValues($post);
        if (!$user->save()) {
            FatUtility::dieJsonError($user->getError());
            return;
        }

        if (!$user->setLoginCredentials($post['user_email'], $post['user_password'], 1, 0)) {
            FatUtility::dieJsonError('Login Credentials could not be set. ' . $user->getError());
        }


        $user_id = $user->getMainTableRecordId();
        if (!empty($social_data['twitter_data'])) {
            $twitter = new TwitterToken();

            $social_data['twitter_data']['twittertoken_user_id'] = $user_id;
            $twitter->saveTwitterToken($social_data['twitter_data']);
        }

        $token = User::encryptPassword(FatUtility::getRandomString(15));
        $i = 0;
        while (!$user->isValidVerifyToken($token)) {
            $token = User::encryptPassword(FatUtility::getRandomString(15));
        }
        $verfiy['uverification_token'] = $token;
        $verfiy['uverification_user_id'] = $user_id;
        $user->addUserVerifyToken($verfiy);
        $reset_url = FatUtility::generateFullUrl('guest-user', 'verifyEmail', array($token));
        if ($post['user_verified'] == 1) {
            Email::sendMail($post["user_email"], 3, array("{username}" => $post["user_firstname"] . ' ' . $post["user_lastname"], "{verification_url}" => $reset_url));
        } else {
            Email::sendMail($post["user_email"], 2, array("{username}" => $post["user_firstname"] . ' ' . $post["user_lastname"], "{verification_url}" => $reset_url));
        }

        $adminEmail = FatApp::getConfig('conf_admin_email_id', null, 'fatbit@dummyid.com');
        Email::sendMail($adminEmail, 1, array("{username}" => $post["user_firstname"] . ' ' . $post["user_lastname"], "{useremail}" => $post["user_email"]));

        Helper::unsetSocialSession();
        $user->login($post['user_email'], $post['user_password'], $_SERVER['REMOTE_ADDR']);
        FatUtility::dieJsonSuccess("Registration Successful!");
    }

    function verifyEmail($token)
    {
        if (empty($token)) {
            Message::addErrorMessage(Info::t_lang('INVALID_REQUEST!'));
            FatApp::redirectUser(FatUtility::generateUrl());
        }
        $post = FatApp::getPostedData();
        $usr = new User();
        $verify_data = $usr->getUserVerifyToken($token);
        if (empty($verify_data)) {
            Message::addErrorMessage(Info::t_lang('INVALID_TOKEN!'));
            FatApp::redirectUser(FatUtility::generateUrl());
        }
        $update_data['user_verified'] = 1;
        $user_id = $verify_data['uverification_user_id'];
        $usr = new User($user_id);
        if (!$usr->verifyAccount(1)) {
            Message::addErrorMessage(Info::t_lang('SOMETHING_WENT_WRONG!'));
            FatApp::redirectUser(FatUtility::generateUrl());
        }

        Message::addMessage(Info::t_lang('YOUR_EMAIL_IS_VERIFIED_SUCCESSFULLY!'));
        FatApp::redirectUser(FatUtility::generateUrl('guest-user', 'login-form'));
    }

    public function host()
    {
        $frm = $this->getRegistrationForm();
        $post = FatApp::getPostedData();
        $post['user_id'] = 0;
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') && !Helper::verifyCaptcha($_POST['g-recaptcha-response'])) {
            FatUtility::dieJsonError(Info::t_lang("INCORRECT_SECURITY_CODE"));
        }
        $social_data = Helper::getSocialSession(); //user_signup_media
        if (!empty($social_data['user_signup_media']) && in_array($social_data['user_signup_media'], array(1, 2)) && !empty($social_data['user_email']) && $social_data['user_email'] == $post['user_email']) {
            $post['user_verified'] = 1;
        } else {
            $post['user_verified'] = 0;
        }

        $post['user_type'] = 1;

        $user = new User(0);
        $user->assignValues($post);
        if (!$user->save()) {
            FatUtility::dieJsonError($user->getError());
            $this->signupForm();
            return;
        }

        if (!$user->setLoginCredentials($post['user_email'], $post['user_password'], 1, 0)) {
            FatUtility::dieJsonError('Login Credentials could not be set. ' . $user->getError());
        }
        $user_id = $user->getMainTableRecordId();
        if (!empty($social_data['twitter_data'])) {
            $twitter = new TwitterToken();

            $social_data['twitter_data']['twittertoken_user_id'] = $user_id;
            $twitter->saveTwitterToken($social_data['twitter_data']);
        }
        $token = User::encryptPassword(FatUtility::getRandomString(15));
        $i = 0;
        while (!$user->isValidVerifyToken($token)) {
            $token = User::encryptPassword(FatUtility::getRandomString(15));
        }
        $verfiy['uverification_token'] = $token;
        $verfiy['uverification_user_id'] = $user_id;
        $user->addUserVerifyToken($verfiy);
        $reset_url = FatUtility::generateFullUrl('guest-user', 'verifyEmail', array($token));
        if ($post['user_verified'] == 1) {
            Email::sendMail($post["user_email"], 3, array("{username}" => $post["user_firstname"] . ' ' . $post["user_lastname"], "{verification_url}" => $reset_url));
        } else {
            Email::sendMail($post["user_email"], 2, array("{username}" => $post["user_firstname"] . ' ' . $post["user_lastname"], "{verification_url}" => $reset_url));
        }

        Helper::unsetSocialSession();
        $user->login($post['user_email'], $post['user_password'], $_SERVER['REMOTE_ADDR']);
        $cms = new Cms();
        $how_it_work = $cms->getCms(12);
        $url = FatUtility::generateUrl('hostactivity', 'action');
        if (!empty($how_it_work)) {
            $url = FatUtility::generateUrl('cms', 'view', array($how_it_work[Cms::DB_TBL_PREFIX . 'slug']));
        }
        FatUtility::dieJsonSuccess(array('msg' => "Registration Successful!", 'url' => $url));
    }

    public function forgotForm()
    {
        $frm = $this->getForgotForm();
        $this->set('frm', $frm);
        $this->_template->render();
    }

    private function getForgotForm()
    {
        $frm = new Form('frmForgot');
        $fld = $frm->addEmailField(Info::t_lang('EMAIL_ADDRESS'), 'user_email', '', array('placeholder' => Info::t_lang('EMAIL_ADDRESS')));

        $captchaSiteKey = FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '');
        if ($captchaSiteKey != '') {
            $frm->addHtml('', 'security_code', '<div class="g-recaptcha" data-sitekey="' . $captchaSiteKey . '"></div>');
        }

        $fld->requirements()->setRequired();
        //$fld=$frm->addRequiredField(Info::t_lang('SECURITY_CODE'), 'security_code','',array('placeholder'=>Info::t_lang('SECURITY_CODE'),'autocomplete'=>'off'));
        //$fld->requirements()->setRequired();
        //$fld->htmlAfterField='<div class="captcha-wrapper"><img src="'.FatUtility::generateUrl("image","captcha").'" id="image" class="captcha captchapic"/><a href="javascript:void(0);" class ="reloadpic reloadlink reload" onclick="refreshCaptcha(\'image\')"></a></div>';
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('RESET_PASSWORD'), array('class' => 'button button--fill button--red'));

        return $frm;
    }

    function resendVerificationEmail()
    {
        if (!User::isUserLogged()) {
            FatUtility::dieJsonError(Info::t_lang('SESSION_SEEMS_TO_BE_EXPIRED!'));
        }
        $user_id = User::getLoggedUserId();
        $user = new User($user_id);
        $user->loadFromDb();
        $user_data = $user->getFlds();
        $user_name = '';
        if ($user_data[User::DB_TBL_PREFIX . 'firstname']) {
            $user_name = $user_data[User::DB_TBL_PREFIX . 'firstname'];
        }
        if ($user_data[User::DB_TBL_PREFIX . 'lastname']) {
            $user_name .= ' ' . $user_data[User::DB_TBL_PREFIX . 'lastname'];
        }
        $user_name = trim($user_name);
        if ($user_data[User::DB_TBL_PREFIX . 'verified'] == 1) {
            FatUtility::dieJsonError(Info::t_lang('EMAIL_ALREADY_VERIFIED!'));
        }
        $verfiy = $user->getUserVerifyTokenByUserid($user_id);
        if (empty($verfiy)) {
            $token = User::encryptPassword(FatUtility::getRandomString(15));
            $i = 0;
            while (!$user->isValidVerifyToken($token)) {
                $token = User::encryptPassword(FatUtility::getRandomString(15));
            }
            $verfiy['uverification_token'] = $token;
            $verfiy['uverification_user_id'] = $user_id;
            $user->addUserVerifyToken($verfiy);
        }
        $user_email = $user_data[User::DB_TBL_PREFIX . 'email'];

        $token = $verfiy['uverification_token'];
        $reset_url = FatUtility::generateFullUrl('guest-user', 'verifyEmail', array($token));

        Email::sendMail($user_email, 13, array("{username}" => $user_name, "{reset_url}" => $reset_url));
        FatUtility::dieJsonSuccess(Info::t_lang('EMAIL_VERIFICATION_LINK_SENT'));
    }

    public function forgotPasswordSetup()
    {
        if (User::isUserLogged()) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $img = Helper::getCaptchaObject();
        $frm = $this->getForgotForm();
        $post = $frm->getformDataFromArray(FatApp::getPostedData());

        /*  if(!$img->check($post['security_code'])){
          FatUtility::dieJsonError(Info::t_lang('YOU_ENTERED_INCORRECT_SECURITY_CODE._PLEASE_TRY_AGAIN'));
          } */
        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') && !Helper::verifyCaptcha($_POST['g-recaptcha-response'])) {
            FatUtility::dieJsonError(Info::t_lang("INCORRECT_SECURITY_CODE"));
        }

        $usr = new User();
        $row = $usr->getUserByEmail($post['user_email']);
        if (empty($row)) {
            FatUtility::dieJsonError(Info::t_lang('EMAIL_NOT_REGISTERED_WITH_US'));
        }
        $user_id = $row['user_id'];
        $usr->deleteOldPasswordResetRequest();
        $row_request = $usr->getPasswordResetRequest($user_id);
        if ($row_request) {
            FatUtility::dieJsonSuccess(Info::t_lang('YOUR_REQUEST_TO_RESET_PASSWORD_HAS_ALREADY_BEEN_PLACED_WITHIN_LAST_24_HOURS. PLEASE_CHECK_YOUR_EMAIL_OR_RETRY_AFTER_24_HOURS_OF_YOUR_PREVIOUS_REQUEST'));
        }
        $token = User::encryptPassword(FatUtility::getRandomString(20));
        $request = array(
            'appr_user_id' => $user_id,
            'aprr_tocken' => $token,
            'aprr_expiry' => date('Y-m-d H:i:s', strtotime(Info::currentDatetime() . "+1 DAY"))
        );
        if (!$usr->addPasswordResetRequest($request)) {
            FatUtility::dieJsonError($usr->error);
        } else {
            $reset_url = FatUtility::generateFullUrl('guest-user', 'reset-password', array($token, $user_id));

            Email::sendMail(
                    $post['user_email'], 6, array(
                '{reset_url}' => $reset_url,
                '{username}' => $row['user_firstname'] . ' ' . $row['user_lastname'],
                    )
            );
            FatUtility::dieJsonSuccess(Info::t_lang('YOUR_PASSWORD_RESET_INSTRUCTIONS_HAVE_BEEN_SENT_TO_YOUR_EMAIL. PLEASE_CHECK_YOUR_SPAM_FOLDER_IF_YOU_DO_NOT_FIND_IT_IN_YOUR_INBOX. PLEASE_MIND_THAT_THIS_REQUEST_IN_VALID_ONLY_FOR_NEXT_24_HOURS'));
        }
    }

    function resetPassword($tocken, $user_id)
    {
        if (User::isUserLogged()) {
            FatApp::redirectUser(FatUtility::generateUrl());
        }
        $user_id = FatUtility::int($user_id);
        $usr = new User();
        $usr->deleteOldPasswordResetRequest();
        $row = $usr->getPasswordResetRequest($user_id);
        if ($row == false) {
            FatUtility::dieWithError('Invalid Token');
            return;
        }
        if ($row['aprr_tocken'] != $tocken) {
            FatUtility::dieWithError('Invalid Token');
            return;
        }

        $this->set('frm', $this->getResetPasswordForm($tocken, $user_id));
        $this->set('tocken', $tocken);
        $this->set('user_id', $user_id);

        $this->_template->render();
    }

    public function resetPasswordSetup($token, $user_id)
    {
        if (User::isUserLogged()) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $user_id = FatUtility::int($user_id);
        $usr = new User($user_id);
        $usr->deleteOldPasswordResetRequest();
        $row = $usr->getPasswordResetRequest($user_id);
        if ($row == false) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_TOKEN!'));
            return;
        }
        if ($row['aprr_tocken'] != $token) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_TOKEN!'));
            return;
        }
        if (!$usr->loadFromDb()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
        }
        $row = $usr->getFlds();
        if (empty($row)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $frm = $this->getResetPasswordForm($token, $user_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $data['user_password'] = User::encryptPassword($post['user_password']);

        $usr->assignValues($data);

        if (!$usr->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
        }
        if (!$usr->deletePasswordResetRequest($user_id)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
        }

        Message::addMessage(Info::t_lang('PASSWORD_SUCCESSFULLY_UPDATED. PLEASE_LOGIN_NOW_WITH_YOUR_NEW_PASSWORD'));

        FatUtility::dieJsonSuccess(Info::t_lang('PASSWORD_SUCCESSFULLY_UPDATED. PLEASE_LOGIN_NOW_WITH_YOUR_NEW_PASSWORD!'));
    }

    private function getResetPasswordForm($token, $user_id)
    {
        $frm = new Form('reset_password_form', array('class' => 'web_form'));
        $frm->setRequiredStarWith('none');
        $password = $frm->addPasswordField(Info::t_lang('NEW_PASSWORD'), 'user_password');
        $password->requirements()->setRequired();
        $password->requirements()->setPassword();
        $password->developerTags['col'] = 12;
        $confirm_pwd = $frm->addPasswordField(Info::t_lang('CONFIRM_PASSWORD'), 'cpassword');
        $confirm_pwd->requirements()->setRequired();
        $confirm_pwd->requirements()->setPassword();
        $confirm_pwd->requirements()->setCompareWith('user_password', 'eq');
        $confirm_pwd->developerTags['col'] = 12;
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SUBMIT'));

        return $frm;
    }

    /* 	public function generateImage(){
      define("DOMPDF_ENABLE_REMOTE",true);
      $htm = $this->_template->render(false,false,"guest-user/image.php",true,false);
      require_once CONF_INSTALLATION_PATH . 'library/dompdf/dompdf_config.inc.php';
      $dompdf = new DOMPDF();
      $dompdf->load_html($htm);
      $customPaper = array(0,0,217,302);
      $dompdf->set_paper($customPaper);
      $dompdf->render();
      $pdfoutput = $dompdf->output();
      $filename = CONF_UPLOADS_PATH."n2.pdf";
      $fp = fopen($filename, "a");
      fwrite($fp, $pdfoutput);
      fclose($fp);
      $name       = "n2.pdf";
      $nameto     = "n2.jpg";
      $convert    = CONF_UPLOADS_PATH.$name." ".CONF_UPLOADS_PATH.$nameto;
      exec("convert -density 600 ".$convert);
      unlink(CONF_UPLOADS_PATH.$name);
      } */

    private function getRegistrationForm()
    {

        /* $objCountry = new Country(1);
          $objCountry->loadFromDb();
          $countryCode = $objCountry->getFldValue('country_phone_code');

          var_dump($countryCode); exit; */

        $frm = new Form('frmRegister');
        //	$frm->addHiddenField('', 'user_id', 0, array('id'=>'user_id'));
        $frm->addRadioButtons('User Type', 'user_type', Info::getUserType(), "0", array('class' => 'list list--horizontal'), array("class" => "serviceopt-type"));
        $frm->addRequiredField('Name:', 'user_firstname');
        $frm->addRequiredField('Last Name:', 'user_lastname');
        $fld = $frm->addEmailField('Email:', 'user_email', '', array('id' => 'user_email'));
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_email', 'user_email');
        $fld->requirements()->setRequired();
        // $phoneCodes = Country::getCountriesPhoneCode();
        // $fld = $frm->addSelectBox('', 'user_phone_code', $phoneCodes, current($phoneCodes), array('id' => 'user_phone_code'), Info::t_lang('CODE'));
        // $fld->requirements()->setRequired();
        // $fld->htmlBeforeField = "<span id='country_code'></span>";


        $countries = Country::getCountries();
        $frm->addSelectBox(Info::t_lang('COUNTRY'), 'user_country_id', $countries, '', array('onChange' => 'loadCountryCodes(this); return false;', 'id' => 'user_country_id'), '');

        // $frm->addTextBox('Phone', 'user_phone');
        $fld = $frm->addPasswordField('Password:', 'user_password');
        $fld->requirements()->setPassword();
        $fld1 = $frm->addPasswordField('Confirm Password', 'password1');
        $fld1->requirements()->setRequired();
        $fld1->requirements()->setCompareWith('user_password', 'eq', 'Password');

        $fld = $frm->addRequiredField('Phone', 'user_phone', '', array('maxlength' => '15'));

        $fld->htmlBeforeField = "<span id='country_code' class='field_add-on add-on--left'>+00</span>";

        //$frm->addSubmitButton('', 'btn_submit', Info::t_lang('SIGNUP_AS_TRAVELER'), array('class' => 'button button--fill button--red'));
        //$frm->addSubmitButton('', 'host_signup', Info::t_lang('SIGNUP_AS_HOST'), array('class' => 'button button--fill button--green'));
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SIGNUP'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    private function getHostForm()
    {
        $frm = new Form('frmRegister');
        //	$frm->addHiddenField('', 'user_id', 0, array('id'=>'user_id'));
        $frm->addRequiredField('Name:', 'user_firstname');
        $frm->addRequiredField('Name:', 'user_lastname');
        $fld = $frm->addEmailField('Email:', 'user_email');
        $fld->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
        $fld->requirements()->setRequired();
        $phoneCodes = PhoneCodes::getPhoneCodeArray();
        $user_phone_code = $frm->addSelectBox('', 'user_phone_code', $phoneCodes, current($phoneCodes), array('id' => 'user_phone_code'), Info::t_lang('CODE'));
        //$user_phone_code->requirements()->setRequired();
        //$frm->addTextBox('Phone', 'user_phone');
        $fld = $frm->addPasswordField('Password:', 'user_password');
        $fld->requirements()->setPassword();
        $fld1 = $frm->addPasswordField('Confirm Password', 'password1');
        $fld1->requirements()->setRequired();
        $fld1->requirements()->setCompareWith('user_password', 'eq', 'Password');

        $frm->addSubmitButton('&nbsp;', 'btn_submit', Info::t_lang('BECOME_A_HOST'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    private function getLoginForm()
    {
        $frm = new Form('frmLogin');
        $fld = $frm->addRequiredField(Info::t_lang('EMAIL_ADDRESS'), 'username', '', array('title' => Info::t_lang('EMAIL_ADDRESS')));

        $frm->addPasswordField(Info::t_lang('PASSWORD'), 'password', '', array('title' => Info::t_lang('PASSWORD')))->requirements()->setRequired();

        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('LOGIN_FUN_AWAY'), array('class' => 'button button--fill button--green'));

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
