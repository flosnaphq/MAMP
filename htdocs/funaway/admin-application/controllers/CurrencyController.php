<?php
class CurrencyController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			FatUtility::dieJsonError("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewCurrency($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditCurrency($this->admin_id);
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
		$brcmb->add("Currency");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
	
		$search = Currency::getSearchObject();
		$page = empty($page) || $page <= 0?1:$page;
		$page = FatUtility::int($page);
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"currency/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['currency_id'] = empty($post['currency_id'])?0:FatUtility::int($post['currency_id']);
		$form = $this->getForm($post['currency_id']);
		if(!empty($post['currency_id'])){
			$fc = new Currency($post['currency_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$this->set("currency_id",$post['currency_id']);
		$htm = $this->_template->render(false,false,"currency/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'fIsAjax','1');
		$frm->addHiddenField("", 'currency_id');
		$frm->addRequiredField('Currency Name','currency_name');
		$frm->addRequiredField('Currency Code','currency_code');
		$frm->addRequiredField('Currency Rate','currency_rate');
		$frm->addRequiredField('Currency Symbol','currency_symbol');
		$frm->addSelectBox('Currency Symbol Location','currency_symbol_location',Info::side());
		$frm->addSelectBox('Status','currency_active',Info::getStatus());
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
		$currency_id = FatApp::getPostedData('currency_id', FatUtility::VAR_INT);
		unset($data['currency_id']);
		$tmp = new Currency($currency_id);
		$tmp->assignValues($data);

		if (!$tmp->save()) {
			FatUtility::dieJsonError($tmp->getError());
			
		}
		$currency_id = $tmp->getMainTableRecordId();
		$this->set('msg', 'Currency Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
}
