<?php

class OrderController extends MyAppController {

    const SESSION_NAME = 'order_cart_session';

    //------------------- Guest Checkout -------------//

    function payment() {
        $ct = new Cart();
        $detail = $ct->getCartDetail();
        if (empty($detail)) {
            Message::addErrorMessage(Info::t_lang('YOU_CART_WAS_EMPTY!'));
            FatApp::redirectUser(FatUtility::generateUrl('activity'));
        }
        $active_tab = !empty($_SESSION[self::SESSION_NAME]['order_payment_tab']) ? $_SESSION[self::SESSION_NAME]['order_payment_tab'] : 1;
        $this->set('active_tab', $active_tab);
        $this->_template->render();
    }

    function paymentTab($tab = 1) {
        $ct = new Cart();
        $detail = $ct->getCartDetail();
        if (empty($detail)) {
            FatUtility::dieJsonError(Info::t_lang('YOU_CART_IS_EMPTY!'));
        }
        $tab = FatUtility::int($tab);
        $tab = $tab > 2 || $tab < 1 ? 1 : $tab;
        $_SESSION[self::SESSION_NAME]['order_payment_tab'] = 1;
        switch ($tab) {
            case 1: //Account tab
                $this->account();
                break;
            case 2:// Payment form
                $this->paymentMethods();
                break;
        }
    }

    function account() {
        $user_id = 0;

        if (User::isUserLogged()) {
            $_SESSION[self::SESSION_NAME]['order_email'] = User::getLoggedUserAttribute('user_email');
            $this->accountForm();
            return;
        }
        $this->emailForm();
    }

    private function paymentMethods() {
        if (!User::isUserLogged()) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang('INVALID_REQUEST!'), 'next_step' => 1));
        }
        $_SESSION[self::SESSION_NAME]['order_payment_tab'] = 2;
        $ct = new Cart();
        $detail = $ct->getCartDetail();
        /* if(empty($detail['usercarts']))
          {
          FatUtility::dieJsonError(array('msg'=>Info::t_lang('INVALID_REQUEST!')));
          } */

        $attributes = $detail['attributes'];
        // $paymentMethods = PaymentMethods::getAllNames(true, 0, 'pmethod_active', null, 'pmethod_display_order', 'ASC');

        $srchObj = new SearchPaymentMethods();
        $srchObj = PaymentMethods::searchPaymentMethods();

        $srchObj->addCondition(PaymentMethods::DB_TBL_PREFIX . 'active', '=', 1);
        $srchObj->doNotCalculateRecords();
        $srchObj->doNotLimitRecords();
        $srchObj->addOrder(PaymentMethods::DB_TBL_PREFIX . 'display_order', 'ASC');
        $rs = $srchObj->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($rs, PaymentMethods::DB_TBL_PREFIX . 'id');

        // var_dump($paymentMethods );exit;
        $this->set('sub_total', $detail['sub_total']);
        $this->set('total', $detail['total']);
        $this->set('donation', $detail['donation']);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('attributes', $attributes);
        $html = $this->_template->render(false, false, 'order/_partial/payment-methods.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    public function paymentMethod($payMethodId) {
        if (!User::isUserLogged()) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang('INVALID_REQUEST!'), 'next_step' => 1));
        }
        $_SESSION[self::SESSION_NAME]['order_payment_tab'] = 2;
        $ct = new Cart();
        $detail = $ct->getCartDetail();
        if (!$detail || empty($detail)) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang('INVALID_REQUEST!'), 'next_step' => 1));
        }
        $attributes = $detail['attributes'];
        $paymentMethods = PaymentMethods::getAllNames(true, 0, 'pmethod_active', null, 'pmethod_display_order', 'ASC');
        $this->set('sub_total', $detail['sub_total']);
        $this->set('total', $detail['total']);
        $this->set('donation', $detail['donation']);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('attributes', $attributes);
        echo $this->_template->render(false, false, 'order/_partial/payment-method.php', true);
        // FatUtility::dieJsonSuccess($html);
    }

    function success($order_id) {
        $ord = new Order();
        $order = $ord->getOrderDetail($order_id);
        $user_id = User::getLoggedUserId();
        $cart = new Cart();
        $cart->clearCart();
        if (empty($order) || $user_id != $order['order_user_id']) {
            
        } else {
            $this->set('order', $order);
        }
        $track_data = array(
            'booking_amount' => $order['order_net_amount'],
            'received_amount' => $order['order_received_amount'],
            'total_amount' => $order['order_total_amount'],
        );
        $this->set('track_data', $track_data);
        $this->_template->render();
    }

    function paymentFail($order_id) {
        $ord = new Order();
        $order = $ord->getOrderDetail($order_id);
        $user_id = User::getLoggedUserId();

        if (empty($order) || $user_id != $order['order_user_id'] || $order['order_payment_status'] == 1) {
            $this->set('error', 'Invalid order');
        } else {
            $this->set('order_id', $order_id);
             $this->set('order', $order);
            $this->set('error', 'Order cancelled or payment failed');
        }
        $this->_template->render();
    }

    function emailForm() {
        $frm = $this->getEmailForm();
        $data['user_email'] = isset($_SESSION[self::SESSION_NAME]['order_email']) ? $_SESSION[self::SESSION_NAME]['order_email'] : '';
        $frm->fill($data);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'order/_partial/email-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function loginForm() {
        if (empty($_SESSION[self::SESSION_NAME]['order_email'])) {
            $this->emailForm();
        }
        $data['user_email'] = $_SESSION[self::SESSION_NAME]['order_email'];
        $frm = $this->getLoginForm();
        $frm->fill($data);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'order/_partial/login-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function accountForm() {
        if (empty($_SESSION[self::SESSION_NAME]['order_email'])) {
            $this->emailForm();
        }
        $usr = new User();
        $user_data['email'] = $_SESSION[self::SESSION_NAME]['order_email'];
        $frm = $this->getAccountForm();
        if (User::isUserLogged()) {
            $user_id = User::getLoggedUserId();
            $user_data = $usr->getUserByUserId($user_id);
            $user_data['email'] = $user_data['user_email'];
            $frm->removeField($frm->getField('user_password'));
            $frm->removeField($frm->getField('password1'));
        }

        $frm->fill($user_data);
        $countryCode = "+";
          if (isset($user_data['user_country_id']) && $user_data['user_country_id']>0) {
         
            $fc = new Country($user_data['user_country_id']);
            $fc->loadFromDb();
            $countryData = $fc->getFlds();
            $fld = $frm->getField('user_phone');
    
            $countryCode = "+".$countryData['country_phone_code'];
        }
        
        
        $frm->getField('user_phone')->htmlBeforeField = '<div id="country_code" class = "field_add-on add-on--left">' . $countryCode . '</div>';
   
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'order/_partial/account-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setupEmail() {
        $post = FatApp::getPostedData();
        if (User::isUserLogged()) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $frm = $this->getEmailForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $email = $post['user_email'];
        $usr = new User();
        $row = $usr->getUserByEmail($email);


        if (!empty($row)) {
            if ($row['user_type'] != 0) {
                FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
            }
            $_SESSION[self::SESSION_NAME]['order_email'] = $email;
            $this->loginForm();
            return;
        }
        $_SESSION[self::SESSION_NAME]['order_email'] = $email;
        $this->accountForm();
        return false;
    }

    function setupLogin() {
        if (empty($_SESSION[self::SESSION_NAME]['order_email'])) {
            $this->emailForm();
            return;
        }
        $post = FatApp::getPostedData();
        $frm = $this->getLoginForm();
        $frm->removeField($frm->getField('user_email'));
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $email = $_SESSION[self::SESSION_NAME]['order_email'];
        $pwd = $post['user_password'];
        $authentication = new User();
        $row = $authentication->getUserByEmail($email);
        if (empty($row)) {
            $this->accountForm();
            return;
        }
        if (!$authentication->login($email, $pwd, $_SERVER['REMOTE_ADDR'])) {
            FatUtility::dieJsonError($authentication->getError());
        }
        $_SESSION[self::SESSION_NAME]['order_payment_tab'] = 2;
        FatUtility::dieJsonSuccess(Info::t_lang('LOGIN_SUCCESSFULLY'));
    }

    function setupAccountInfo() {
        $user_id = 0;
        $ct = new Cart();

        $detail = $ct->getCartDetail();
        if (empty($detail)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if (empty($_SESSION[self::SESSION_NAME]['order_email'])) {
            $this->emailForm();
            return;
        }
        $post = FatApp::getPostedData();
        $frm = $this->getAccountForm();
        if (User::isUserLogged()) {
            $frm->removeField($frm->getField('user_password'));
            $frm->removeField($frm->getField('password1'));
        }
        $post = $frm->getFormDataFromArray($post);

        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $email = $_SESSION[self::SESSION_NAME]['order_email'];
        if (User::isUserLogged()) {
            $user_id = User::getLoggedUserId();
            if (isset($post['user_password'])) {
                unset($post['user_password']);
            }
        } else {
            $post['user_email'] = $email;
        }

        $usr = new User($user_id);

        if ($user_id <= 0) {
            $post['user_type'] = 0;
            $post['user_active'] = 1;
        }
        $usr->assignValues($post);
        if (!$usr->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!._PLEASE_TRY_AGAIN'));
        }

        if ($user_id <= 0) {
            $user_id = $usr->getMainTableRecordId();
            if (!$usr->setLoginCredentials($post['user_email'], $post['user_password'], 1, 0)) {
                FatUtility::dieJsonError('Login Credentials could not be set. ' . $usr->getError());
            }
            $token = User::encryptPassword(FatUtility::getRandomString(15));
            $i = 0;
            while (!$usr->isValidVerifyToken($token)) {
                $token = User::encryptPassword(FatUtility::getRandomString(15));
            }
            $verfiy['uverification_token'] = $token;
            $verfiy['uverification_user_id'] = $user_id;
            $usr->addUserVerifyToken($verfiy);
            $reset_url = FatUtility::generateFullUrl('guest-user', 'verifyEmail', array($token));
            Email::sendMail($post["user_email"], 2, array("{username}" => $post["user_firstname"] . ' ' . $post["user_lastname"], "{verification_url}" => $reset_url));

            $usr->login($post['user_email'], $post['user_password'], $_SERVER['REMOTE_ADDR']);
        }
        $_SESSION[self::SESSION_NAME]['order_payment_tab'] = 2;
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('UPDATE_SUCCESSFULLY'), 'next_step' => 2));
    }

    function getEmailForm() {
        $frm = new Form('emailFrm');
        $frm->addEmailField(Info::t_lang('EMAIL'), 'user_email');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function getLoginForm() {
        $frm = new Form('loginFrm');
        $frm->addEmailField(Info::t_lang('EMAIL'), 'user_email', '', array('disabled' => 'disabled'));
        $frm->addPasswordField(Info::t_lang('PASSWORD'), 'user_password')->requirements()->setRequired();
        $frm->addButton('', 'back_btn', Info::t_lang('BACK'));
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function loginUser() {
        $post = FatApp::getPostedData();
        $email = @$post['email'];
        $pwd = @$post['pwd'];
        $authentication = new User();
        if (!FatUtility::validateEmailId($email)) {
            FatUtility::dieJsonError(Info::t_lang('ENTER_VALID_EMAIL'));
        }
        $row = $authentication->getUserByEmail($email);
        if (empty($row)) {
            FatUtility::dieJsonError(array('msg' => 'AVAILABLE', 'status' => 2));
        }
        if (!$authentication->login($email, $pwd, $_SERVER['REMOTE_ADDR'])) {
            FatUtility::dieJsonError($authentication->getError());
        }
        FatUtility::dieJsonSuccess(Info::t_lang('LOGIN_SUCCESSFULLY!'));
    }

    function getAccountForm() {
        $frm = new Form('accountFrm');
        $user_email = $frm->addTextBox(Info::t_lang('EMAIL'), 'email', '', array('disabled' => 'disabled'));
        $frm->addRequiredField(Info::t_lang('FIRST_NAME'), 'user_firstname');
        $frm->addRequiredField(Info::t_lang('LAST_NAME'), 'user_lastname');

        $countries = Country::getCountries();
        $frm->addSelectBox(Info::t_lang('COUNTRY'), 'user_country_id', $countries, '', array('onchange' => "loadCountryCodes(this)"), '');
         $frm->addTextBox(Info::t_lang('PHONE_NUMBER'), 'user_phone');


        $fld = $frm->addPasswordField(Info::t_lang('PASSWORD'), 'user_password');
        $fld->requirements()->setPassword();
        $fld1 = $frm->addPasswordField('Confirm Password', 'password1');
        $fld1->requirements()->setRequired();
        $fld1->requirements()->setCompareWith('user_password', 'eq', 'Password');
        $frm->addSubmitButton(Info::t_lang('SUBMIT'), 'submit_btn', Info::t_lang('SUBMIT'));
        return$frm;
    }

    /* function getAccountForm(){
      $frm = new Form('accountFrm');
      $user_email = $frm->addEmailField(Info::t_lang('EMAIL'),'user_email','',array('onblur'=>'checkEmail(this.value)','id'=>'js-email'));
      $user_email->htmlAfterField = '<p id="js-email-msg" style="display:none"></a>';
      $fld = $frm->addPasswordField(Info::t_lang('PASSWORD'),'user_password','',array('id'=>'js-current-password', 'onblur'=>'loginUser()'));
      $fld->setWrapperAttribute('id','js-current-password-wrapper');
      //$fld->setWrapperAttribute('style','display:none');
      $frm->addRequiredField(Info::t_lang('FIRST_NAME'),'user_firstname');
      $frm->addRequiredField(Info::t_lang('LAST_NAME'),'user_lastname');
      $phoneCodes = PhoneCodes::getPhoneCodeArray();
      $frm->addSelectBox(Info::t_lang('PHONE_CODE'),'user_phone_code',$phoneCodes,current($phoneCodes),array(),'');
      $frm->addTextBox(Info::t_lang('PHONE_NUMBER'),'user_phone');
      //$pwd = $frm->addPasswordField(Info::t_lang('PASSWORD'),'user_password');
      //$pwd->setWrapperAttribute('id','js-pwd-wrapper');
      $countries = Country::getCountries();
      $frm->addSelectBox(Info::t_lang('COUNTRY'),'user_country_id',$countries,'',array(),'');
      $frm->addSubmitButton(Info::t_lang('SUBMIT'),'submit_btn',Info::t_lang('SUBMIT'));
      return$frm;
      } */
    /* function account(){
      $user_id =0;
      if(User::isUserLogged()){
      $user_id = User::getLoggedUserId();
      }
      $frm = $this->getAccountForm();
      $usr = new User();
      if($user_id > 0){
      $user_data = $usr->getUserByUserId($user_id);
      $frm->fill($user_data);
      $frm->getField('user_email')->setFieldTagAttribute('disabled','disabled');
      $frm->removeField($frm->getField('user_password'));
      }
      $frm->getField('user_phone')->htmlBeforeField='<div class = "field_add-on add-on--left">'.$frm->getField('user_phone_code')->getHtml().'</div>';
      $frm->removeField($frm->getField('user_phone_code'));

      $this->set('frm', $frm);
      $html = $this->_template->render(false, false, 'order/_partial/account.php', true, true);
      FatUtility::dieJsonSuccess($html);
      } */

    /* function checkEmail(){
      $post = FatApp::getPostedData();
      if(User::isUserLogged()){
      FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
      }
      $email = isset($post['email'])?trim($post['email']):'';
      if($email == ''){
      FatUtility::dieJsonError(Info::t_lang('PLEASE_ENTER_EMAIL'));
      }
      if(!FatUtility::validateEmailId($email)){
      FatUtility::dieJsonError(Info::t_lang('INVALID_EMAIL'));
      }
      $usr = new User();
      $row = $usr->getUserByEmail($email);
      $emailExist = empty($row)?0:1;
      $msg =Info::t_lang('EMAIL_AVAILABLE');
      if($emailExist){
      $msg = Info::t_lang('EMAIL_ALREADY_REGISTERED_WITH_US._PLEASE_ENTER_YOUR_PASSWORD_TO_LOGIN');

      }
      FatUtility::dieJsonSuccess(array('msg' => $msg, 'emailExist' => $emailExist));
      } */
}

?>	