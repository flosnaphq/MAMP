<?php

class AdminGuestController extends FatController
{
	public function __construct($action){
		if (AdminAuthentication::isAdminLogged()) {
			FatApp::redirectUser(FatUtility::generateUrl('home'));
		}
		parent::__construct($action);
	} 
	public function loginForm() {

        $loginInfo['username'] = 'admin';
        $loginInfo['password'] = 'developer';
		$frm = $this->getLoginForm();
		$frm->fill($loginInfo);
		$forgot_frm = $this->getForgotPassword();
		$this->set('frm', $frm);
		$this->set('forgot', $forgot_frm);
		$this->_template->render(false, false);
	}
	
	public function login() {
		
		$username = FatApp::getPostedData('username');
		$password = FatApp::getPostedData('password');
		
		$authentication = new AdminAuthentication();
		if (!$authentication->login($username, $password, $_SERVER['REMOTE_ADDR'])) {
			FatUtility::dieJsonError($authentication->getError());
		}
		FatUtility::dieJsonSuccess('Login Successful.');
	}
	
	private function getLoginForm() {
		
		$frm = new Form('frmLogin');
		$frm->setFormTagAttribute("action",FatUtility::generateUrl("admin-guest","login"));
		$frm->addTextBox('Username:', 'username')->requirements()->setRequired();
		$frm->addPasswordField('Password:', 'password')->requirements()->setRequired();
		$frm->addSubmitButton('', 'btn_submit');
		
		return $frm;
	}
	
	public function forgotPassword() {
		require_once CONF_INSTALLATION_PATH . 'library/securimage/securimage.php';
    	$img = new Securimage();
		$frm = $this->getForgotPassword();
		$post = $frm->getformDataFromArray(FatApp::getPostedData());
		if(!$img->check($post['security_code'])){
			FatUtility::dieJsonError("You entered incorrect Security Code. Please Try Again.");
    	}  
		$admin = new Admin();
		$row = $admin->isEmailExist($post['admin_email']);
		if($row == false){
			FatUtility::dieJsonError('Invalid Email ID!');
		}
		
		$admin_id = $row['admin_id'];
		$admin->deleteOldPasswordResetRequest();
		$row_request =$admin->getPasswordResetRequest($admin_id);
		if ($row_request){
			FatUtility::dieJsonSuccess("Your request to reset password has already been placed within last 24 hours. Please check your emails or retry after 24 hours of your previous request");
    	}
		$token = UserAuthentication::encryptPassword(FatUtility::getRandomString(20));
		$request = array(
				'appr_admin_id'=>$admin_id,
				'aprr_token'=>$token,
				'aprr_expiry'=>date('Y-m-d H:i:s', strtotime("+1 DAY"))
				);
		$admin->addPasswordResetRequest($request);
		$reset_url = 'http://' . $_SERVER['SERVER_NAME'] . FatUtility::generateUrl('admin-guest', 'reset-password', array($token,$admin_id));
    	
		Email::sendMail($post['admin_email'],5,array('{site_name}'=>FatApp::getConfig('conf_website_name'),'{reset_url}'=>$reset_url));
		FatUtility::dieJsonSuccess('Your password reset instructions have been sent to your email. Please check your spam folder if you do not find it in your inbox. Please mind that this request is valid only for next 24 hours.');
	}
	
	/* public function captcha() {
		require_once CONF_INSTALLATION_PATH . 'library/securimage/securimage.php';
		$img = new Securimage();
		$img->show(); 
	} */
	
	function resetPassword($tocken,$admin_id){
    	$admin_id = FatUtility::int($admin_id);
		$admin = new Admin();
		$admin->deleteOldPasswordResetRequest();
		$row = $admin->getPasswordResetRequest($admin_id);
		if($row == false){
			FatUtility::dieWithError('Invalid Token');
			return;
		}
    	if($row['aprr_token'] != $tocken){
			FatUtility::dieWithError('Invalid Token');
			return;
		}
		
    	$this->set('frm_password', $this->getResetPasswordForm($tocken,$admin_id));
    	
    	$this->_template->render(false,false);
    }
	
	public function updatePassword($token,$admin_id){
		$admin_id = FatUtility::int($admin_id);
		$admin = new Admin();
		$admin->deleteOldPasswordResetRequest();
		$row = $admin->getPasswordResetRequest($admin_id);
		if($row == false){
			FatUtility::dieWithError('Invalid Token');
			return;
		}
    	if($row['aprr_token'] != $token){
			FatUtility::dieWithError('Invalid Token');
			return;
		}
		$frm = $this->getResetPasswordForm($token,$admin_id);
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		if($post == false){
			FatUtility::dieJsonError('Something went worng!');
		}
		 $data['admin_id'] = $admin_id;
		 $data['admin_password'] = $post['admin_password'];
		if(!$admin->addUpdate($data)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		if(!$admin->deletePasswordResetRequest($admin_id)){
			FatUtility::dieJsonError('Something went wrong!'.$admin->error);
		}
    	
		FatUtility::dieJsonSuccess(array('redirect_url'=>FatUtility::generateFullUrl('admin-guest','login-form'),'msg'=>'Password successfully updated! Please login now with your new password.'));
	}
	
	private function getResetPasswordForm($token,$admin_id){
		$frm = new Form('reset_password_form',array('class'=>'web_form'));
		$frm->setRequiredStarWith('none');
		$frm->addPasswordField('New Password:','admin_password')->requirements()->setRequired();
		$confirm_pwd = $frm->addPasswordField('Confirm Password:','cpassword');
		$confirm_pwd->requirements()->setRequired();
		$confirm_pwd->requirements()->setCompareWith('admin_password','eq');
		$frm->addSubmitButton('','btn_submit');
		$frm->setFormTagAttribute('action',FatUtility::generateUrl('admin-guest','update-password',array($token, $admin_id)));
		$frm->setValidatorJsObjectName('resetValidator');
		$frm->setFormTagAttribute('onSubmit','updatePassword(resetValidator,this); return false;');
		return $frm;
	}
	
	private function getForgotPassword(){
		
		$frm = new Form('frmForgotPassword',array('class'=>'web_form'));
		$frm->setRequiredStarWith('none');
		$frm->setRequiredStarPosition('none');
    	$frm->addEmailField('', 'admin_email','')->requirements()->setRequired();
    	 $fld=$frm->addRequiredField('', 'security_code','')->requirements()->setRequired();
    	$frm->addHtml("","captcha",'<img src="'.FatUtility::generateUrl("info","captcha").'" id="image" class="captcha captchapic"/><a href="javascript:void(0);" class ="reloadpic reloadlink reload" onclick="document.getElementById(\'image\').src = \'' . FatUtility::generateUrl("info","captcha") . '?sid=\' + Math.random(); return false"></a>');
		
		/* $frm->addHTML("",'captcha','<img src="' . CONF_WEB_BASE_URL . 'securimage/securimage_show.php" id="image" class="captchapic"/>
    	<a href="javascript:void(0);" onclick="document.getElementById(\'image\').src = \'' .  CONF_WEB_BASE_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false" class="reloadlink"></a>', 'captcha');
    	 */
    	$frm->addSubmitButton('', 'btn_submit', '', array('class'=>'login_btn'));
    	$frm->setFormTagAttribute('action', FatUtility::generateUrl('admin-guest', 'forgot-password'));
    	$frm->setValidatorJsObjectName('forgotValidator');
		$frm->setFormTagAttribute('onsubmit','forgotPassword(forgotValidator,this); return false;'); 
		return $frm;
    	
    }
}