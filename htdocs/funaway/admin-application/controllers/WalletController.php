<?php
class WalletController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		
		$ajaxCallArray = array('listing');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewWallet($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditWallet($this->admin_id);
		if(!$this->canView){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function walletTransaction(){
		$post = FatApp::getPostedData();
		$user_id = @$post['user_id'];
		$wtran_user_type = @$post['wtran_user_type'];
		$frm = $this->walletForm();
		$frm->fill(array('wtran_user_id'=>$user_id,'wtran_user_type'=>$wtran_user_type));
		$this->set("frm",$frm);
		$htm = $this->_template->render(false,false,"wallet/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function action(){
		/* $form = $this->walletForm(); */
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$data = array();
		$data['wtran_user_id'] = $post['wtran_user_id'];
		$data['wtran_date'] = Info::currentDatetime();
		$data['wtran_amount'] = $post['amount'];
		$data['wtran_desc'] = $post['description'];
		$data['wtran_user_type'] = $post['wtran_user_type'];
		if(Wallet::addToWallet($data)){
			FatUtility::dieJsonSuccess("Wallet updated!");
		}
		FatUtility::dieJsonError('Something went wront. Please try again');
	}
	
	private function walletForm() {
		$frm = new Form('walletForm',array('class'=>'web_form', 'onsubmit'=>'submitForm(this); return false;'));
		$frm->addRequiredField('Amount','amount');
		$frm->addHiddenField('','wtran_user_id');
		$frm->addHiddenField('','wtran_user_type');
		
		$frm->addTextArea('Description','description');
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Submit');
		return $frm;
	}
	
	public function admin(){
		$this->set("wallet",Wallet::getWalletBalanceByUser(0,0));
		$brcmb = new Breadcrumb();
		
		$brcmb->add("Earnings");
		$this->set('breadcrumb',$brcmb->output());
		
		$this->_template->render();
	}
	
	
	
	
	
	public function lists($page=1,$user_id){
		$pagesize = static::PAGESIZE;
		$page = FatUtility::int($page);
		$search = Wallet::getWalletByUser($user_id);
		
	//	$post = $form->getFormDataFromArray(FatApp::getPostedData());
		$post = FatApp::getPostedData();
		$wtran_user_type = @$post['wtran_user_type'];
		$wtran_user_type = FatUtility::int($wtran_user_type);
		if(!empty($post['start_date']) && FatDate::validateDateString($post['start_date'])){
			$search->addDirectCondition('DATE(wtran_date)>="'.$post['start_date'].'"');
		}
		if(isset($post['end_date']) && FatDate::validateDateString($post['end_date'])){
			$search->addDirectCondition('DATE(wtran_date)<="'.$post['end_date'].'"');
		}
		$search->addCondition('wtran_user_type','=',$wtran_user_type);
		$search->setPageSize($pagesize);
		$search->setPageNumber($page);
		$search->addOrder('wtran_id','desc');
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize',$pagesize);
		$htm = $this->_template->render(false,false,"wallet/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getSearchForm() {
		$frm = new Form('frmSearch',array('class'=>'web_form', 'onsubmit'=>'search(this); return false;'));
		$frm->addDateField('Start Date','start_date','',array('readonly'=>'readonly'));
		$frm->addDateField('End Date','end_date','',array('readonly'=>'readonly'));
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		return $frm;
	}
	
}