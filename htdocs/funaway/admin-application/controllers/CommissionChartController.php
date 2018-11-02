<?php
class CommissionChartController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewAdminCommission($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditAdminCommission($this->admin_id);
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
		$brcmb->add("Admin Commission Chart");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	
	
	public function listing(){
		$search = CommissionChart::getSearchObject();
		$search->doNotCalculateRecords();
		$search->doNotLimitRecords();
		$search->addOrder('commissionchart_min_amount');
		$search->addOrder('commissionchart_max_amount');
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$htm = $this->_template->render(false,false,"commission-chart/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['com_id'] = empty($post['com_id'])?0:FatUtility::int($post['com_id']);
		$form = $this->getForm($post['com_id']);
		if(!empty($post['com_id'])){
			$fc = new CommissionChart($post['com_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"commission-chart/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm($com_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($com_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'commissionchart_id',$com_id);
		$min = $frm->addFloatField('Minimum Listing Price','commissionchart_min_amount');
		
	//	$min->requirements()->setRequired();
		$max = $frm->addFloatField('Maximum Listing Price','commissionchart_max_amount');
	//	$max->requirements()->setRequired();
	//	$max->requirements()->setCompareWith('commissionchart_min_amount','ge','Minimum Amount');
		$max->htmlAfterField="<em>Enter 0 for No limit</em>";
		$commissionchart_rate = $frm->addRequiredField('Site Fee','commissionchart_rate');
		$commissionchart_rate->htmlAfterField ='<em>You can set special commission rate to particular host from Hosts > View Detail > Edit > Commission Field</em>';
		
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
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
		$tplId = FatApp::getPostedData('commissionchart_id', FatUtility::VAR_INT);
		unset($data['commissionchart_id']);
		$tmp = new CommissionChart($tplId);
		$tmp->assignValues($data);

		if (!$tmp->save()) {
			FatUtility::dieWithError($tmp->getError());
			
		}
		$this->set('msg', 'Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	function deleteCommission(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$com_id = empty($post['com_id'])?0:FatUtility::int($post['com_id']);
		if($com_id <=0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$com = new CommissionChart($com_id);
		if(!$com->deleteRecord()){
			FatUtility::dieJsonError('Something Went Wrong. Please Try again');
		}
		FatUtility::dieJsonSuccess('Delete Successfully!');
	}
	
	
	
}
