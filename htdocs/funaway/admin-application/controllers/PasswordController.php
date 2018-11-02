<?php
#error_reporting(E_ERROR);
class PasswordController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		$ajaxCallArray = array("action");
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canEditPassword($this->admin_id);
		$this->canEdit = AdminPrivilege::canViewPassword($this->admin_id);
		
		if(!$this->canView){
			if(FatUtility::isAjaxCall()){
				FatUtility::dieJsonError('Unauthorized Access!');
			}
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index() {
		$brcmb = new Breadcrumb();
		$brcmb->add("Password Management");
		$this->set('breadcrumb',$brcmb->output());
		$frm = $this->getForm();
		$this->set("frm",$frm);	
		$this->_template->render();
	}

	
	public function action(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getForm();
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError("Something Went Wrong!");
		}
		$tblObj = new Admin();
		$current_password = UserAuthentication::encryptPassword($post['current_password']);
		$admin_detail = $tblObj->getAdminById($this->admin_id);
		if($current_password != $admin_detail['admin_password']){
			FatUtility::dieJsonError("Wrong current password!");
		}
		if(!$tblObj->updatePassword($this->admin_id,$post['password'])){
			FatUtility::dieJsonError('Something Went Wrong!');
		}
		FatUtility::dieJsonSuccess("Password Update successfully!");
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		
		$frm->addPasswordField('Current Password', 'current_password')->requirements()->setRequired();
		$frm->addPasswordField('New Password', 'password')->requirements()->setRequired();
		$cpwd = $frm->addPasswordField('Confirm New Password', 'cpassword');
		$cpwd->requirements()->setRequired();
		$cpwd->requirements()->setCompareWith('password','eq');
		$frm->addSubmitButton('', 'btn_submit','Update');
		return $frm;	
	}
	
	
}?>