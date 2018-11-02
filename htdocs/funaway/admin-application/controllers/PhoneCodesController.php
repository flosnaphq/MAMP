<?php
class PhoneCodesController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewPhoneCode($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditPhoneCode($this->admin_id);
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
		$brcmb->add('Phone Codes');
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	

	
	public function listing(){
		
		$search = PhoneCodes::getSearchObject();
		$search->doNotCalculateRecords();
		$search->doNotLimitRecords();
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$countries = Country::getCountries();
		$this->set("countries",$countries);
		$this->set("arr_listing",$records);
		$htm = $this->_template->render(false,false,"phone-codes/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['code_id'] = empty($post['code_id'])?0:FatUtility::int($post['code_id']);
		$form = $this->getForm($post['code_id']);
		if(!empty($post['code_id'])){
			$fc = new PhoneCodes($post['code_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"phone-codes/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm($id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('phoneCodeFrm',array('id'=>'action_form'));
		$frm->addHiddenField("", PhoneCodes::DB_TBL_PREFIX.'id',$id);
		$countries = Country::getCountries();
		
		$frm->addRequiredField('Phone Code',PhoneCodes::DB_TBL_PREFIX.'code');
		$frm->addSelectBox('Country','phonecode_country_id', $countries,current($countries));
		$frm->addSelectBox('Status',PhoneCodes::DB_TBL_PREFIX.'status',Info::getStatus(),1,array(),'');
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit','Add/Update',array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
	public function setup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = $this->getForm ();
		$data = $frm->getFormDataFromArray ( FatApp::getPostedData () );
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}
		$codeId = FatApp::getPostedData(PhoneCodes::DB_TBL_PREFIX.'id', FatUtility::VAR_INT);
		unset($data[PhoneCodes::DB_TBL_PREFIX.'id']);
		$pCode = new PhoneCodes($codeId);
		$pCode->assignValues($data);

		if (!$pCode->save()) {
			FatUtility::dieWithError($pCode->getError());
			
		}

		$this->set('msg', 'Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
}
