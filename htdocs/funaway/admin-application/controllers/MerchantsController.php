<?php
class MerchantsController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $canViewWallet;
	private $canEditWallet;
	private $admin_id; 
	const PAGESIZE=50;
	public function __construct($action) {
		$ajaxCallArray = array("lists","update",'form');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewUser($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditUser($this->admin_id);
		$this->canEditWallet = AdminPrivilege::canEditWallet($this->admin_id);
		$this->canViewWallet = AdminPrivilege::canViewWallet($this->admin_id);
		$this->canViewOrder = AdminPrivilege::canViewOrder($this->admin_id);
		$this->canEditOrder = AdminPrivilege::canEditOrder($this->admin_id);
		$this->canEditMerchantSubUser = AdminPrivilege::canEditMerchantSubUser($this->admin_id);
		$this->canViewMerchantSubUser = AdminPrivilege::canViewMerchantSubUser($this->admin_id);
		if(!$this->canView){
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
		$this->set("canViewWallet",$this->canViewWallet);
		$this->set("canEditWallet",$this->canEditWallet);
		$this->set("canViewOrder",$this->canViewOrder);
		$this->set("canViewMerchantSubUser",$this->canViewMerchantSubUser);
		$this->set("canEditMerchantSubUser",$this->canEditMerchantSubUser);
	}
	
	public function index() {
		$brcmb = new Breadcrumb();
		$brcmb->add("Merchant Management");
		$frm_2 = $this->getUserSearchForm(2);
		$frm_3 = $this->getUserSearchForm(3);
		$this->set('breadcrumb',$brcmb->output());
		$this->set('frm_2',$frm_2);
		$this->set('frm_3',$frm_3);
		$this->_template->render();
		
	}
	
	function subUsers($merchant_id){
		$brcmb = new Breadcrumb();
		$brcmb->add("Merchant Management",FatUtility::generateUrl('merchants'));
		
		$tbl = new Users();
		$record = $tbl->getUser($merchant_id);
		$user_name = $record['udetails_first_name'].' '.$record['udetails_last_name'];
		$brcmb->add($user_name);
		$frm = $this->SubUserSearchForm();
		$this->set('breadcrumb',$brcmb->output());
		$this->set('search',$frm);
		$this->set('merchant_id',$merchant_id);
		$this->_template->render();
	}
	
	function SubUserSearchForm(){
		$frm = new Form('subUserSearchForm');
		$frm->addTextBox('Name','name');
		$frm->addTextBox('Email','email');
		$frm->addSubmitButton('&nbsp;','submit_btn','Submit');
		return $frm;
	}
	
	function subUserLists($merchant_id=0, $page=1){
		$merchant_id = FatUtility::int($merchant_id);
		$page = FatUtility::int($page);
		$page = $page==0?1:$page;
		if($merchant_id <= 0){
			FatUtility::dieJsonError('Invalid Request');
		}
		$tbl = new Users();
		$search = $tbl->getSearch($page,static::PAGESIZE);
		$form =  $this->SubUserSearchForm();
		$post = $form->getFormDataFromArray(FatApp::getPostedData());
		if(!empty($post['name'])){
			$key_con = $search->addCondition('ud.udetails_first_name','like','%'.$post['name'].'%');
			$key_con->attachCondition('ud.udetails_last_name','like','%'.$post['name'].'%', 'or');
		}
		if(!empty($post['email'])){
			$key_con = $search->addCondition('u.user_email','like','%'.$post['email'].'%');
		}
		$search->addCondition('user_parent_id','=',$merchant_id);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', static::PAGESIZE);
		$htm = $this->_template->render(false,false,"merchants/_partial/sun-user-listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function subUserForm(){
		if(!$this->canEditMerchantSubUser){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
	
		$merchant_id = FatUtility::int($post['merchant_id']);
		if($merchant_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		
		$user_id = isset($post['user_id'])?FatUtility::int($post['user_id']):0;
		$form = $this->getSubUserForm();
		$data['user_parent_id'] = $merchant_id;
		$data['user_is_merchant'] = 2;
		if($user_id >0 ){
			$form->removeField($form->getField('user_password'));
			$tbl = new Users();
			$data = $tbl->getUser($user_id);
			$data['permission'] = Permission::getMerchantPermissionForFillForm($user_id);
			
		}
		
		$form->fill($data);
		$this->set('frm',$form);
		$html = $this->_template->render(false,false,'merchants/_partial/sub-user-form.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	private function getSubUserForm(){
		if(!$this->canEditMerchantSubUser){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = $this->getForm();
		
		$frm->removeField($frm->getField('btn_submit'));
		
		$frm->addHiddenField('','user_parent_id');
		$permissions = Permission::getRestaurantPermissionName();
		$options = array(
					0=>Info::t_lang('NONE'),
					1=>Info::t_lang('READ'),
					2=>Info::t_lang('WRITE'),
					);
		foreach($permissions as $permission){
			switch($permission['permission_id']){
				case 7:
				case 9:
				case 2:
					$opt = $options;
					unset($opt[2]);
					$frm->addRadioButtons(Info::t_lang($permission['permission_name']), 'permission['.$permission['permission_id'].']', $opt,0);
					break;
				default:
					$frm->addRadioButtons(Info::t_lang($permission['permission_name']), 'permission['.$permission['permission_id'].']', $options,0);
					break;
			}
		}
		
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Submit');
		return $frm;
	}
	
	function subUserFormAction(){
		if(!$this->canEditMerchantSubUser){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$form = $this->getSubUserForm();
		$post = $form->getFormDataFromArray(FatApp::getPostedData());
		
		if($post == false){
			FatUtility::dieJsonError($form->getValidationErrors());
		}
		
		$tbl = new Users();
		$user_id = FatUtility::int($post['user_id']);
		$post['user_password'] = !empty($post['user_password'])?UserAuthentication::encryptPassword($post['user_password']):'';
		if(empty($post['user_password'])){
			unset($post['user_password']);
		}
		$email = $post['user_email'];
		
		if($tbl->isEmailExist($email,$user_id)){
			FatUtility::dieJsonError('Email Already Exist!');
		}
		if(!$user_id = $tbl->updateUser($post,$user_id)){
			FatUtility::dieJsonError($tbl->getError());
		}
		
		if(!$tbl->updateUserDetail($post,$user_id)){
			FatUtility::dieJsonError($tbl->getError());
		}
		if(isset($_FILES['photo']['tmp_name'])){
			if (!is_uploaded_file($_FILES['photo']['tmp_name'])){
				FatUtility::dieJsonError('Photo couldn\'t upload.');
			}
			$attachment = new AttachedFile();
			if (!$attachment->saveImage($_FILES['photo']['tmp_name'], AttachedFile::FILETYPE_USER_PHOTO, 
					$user_id, 0, $_FILES['photo']['name'], 0, true)) {
				FatUtility::dieJsonError($attachment->getError());
			}
		
		}
		if(!empty($post['permission'])){
			if(!Permission::deleteMerchantPermission($user_id)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
			if(!Permission::addMerchantPermission($post['permission'],$user_id, $post['user_parent_id'])){
				FatUtility::dieJsonError('Something went wrong!');
			}
		}
		FatUtility::dieJsonSuccess('Record updated!');
	}
	
	function passwordForm(){
		$post = FatApp::getPostedData();
		$user_id = isset($post['user_id'])?FatUtility::int($post['user_id']):0;
		if($user_id <= 0){
			FatUtility::dieJsonError('Invalid request!');
		}
		$frm = MyHelper::getPasswordForm();
		$data = array('user_id'=>$user_id);
		$frm->fill($data);
		$this->set('frm',$frm);
		$htm = $this->_template->render(false,false,'merchants/_partial/password-form.php',true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function updatePassword(){
		$post = FatApp::getPostedData();
		$frm = MyHelper::getPasswordForm();
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError('Something went Wrong!');
		}
		$user_id = isset($post['user_id'])?FatUtility::int($post['user_id']):0;
		if($user_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		
		$user_password = UserAuthentication::encryptPassword($post['user_password']);
		$usr = new Users();
		$user = new User();
		$user_detail = $usr->getUser($user_id);
		if(empty($user_detail)){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$user_parent_id = $user_detail['user_parent_id'];
		if(!empty($user_parent_id)){
			$user_parent_detail = $usr->getUser($user_parent_id);
			if(empty($user_parent_detail)){
				FatUtility::dieJsonError('Invalid Request!');
			}
		}
		$data['user_password'] = $user_password;
		if(!$usr->updateUser($data, $user_id)){
			FatUtility::dieJsonError('Something went Wrong!');
		}
		if(!$user->removeAuthToken($user_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$login_url = FatUtility::generateFullUrl('guest','guest-login',array(),'/');
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
		}
		FatUtility::dieJsonSuccess('Password Successfully Updated');
	}
	
	public function lists($page=1,$tab=1){
		$tab = FatUtility::int($tab);
		$page = FatUtility::int($page);
		$tab = $tab==0?1:$tab;
		$page = $page==0?1:$page;
		$form =  $this->getUserSearchForm($tab);
		$post = $form->getFormDataFromArray(FatApp::getPostedData());
		$tbl = new Users();
		$search = $tbl->getSearch($page,static::PAGESIZE);
		if(!empty($post['keyword'])){
			$key_con = $search->addCondition('ud.udetails_first_name','like','%'.$post['keyword'].'%');
			$key_con->attachCondition('ud.udetails_last_name','like','%'.$post['keyword'].'%', 'or');
			$key_con->attachCondition('u.user_email','like','%'.$post['keyword'].'%','or');
		}
		if(!empty($post['location'])){
			$key_con = $search->addCondition('rl.region_name','like','%'.$post['location'].'%');
			$key_con->attachCondition('c.city_name','like','%'.$post['location'].'%', 'or');
			$key_con->attachCondition('udetails_address1','like','%'.$post['location'].'%','or');
			$key_con->attachCondition('udetails_address2','like','%'.$post['location'].'%','or');
		}
		if(isset($post['user_active']) && $post['user_active'] !='' && $post['user_active'] != -1){
			$user_active = FatUtility::int($post['user_active']);
			$search->addCondition('user_active','=',$user_active);
		}
		if($tab == 1){
			$search->addCondition('user_verified','=',0);
		}
		elseif(isset($post['user_verified']) && $post['user_verified'] !=''  && $post['user_verified'] != -1){
			$user_verified = FatUtility::int($post['user_verified']);
			$search->addCondition('user_verified','=',$user_verified);
		}
		$search->addCondition('user_is_merchant','=',1);
		
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('tab', $tab);
		$this->set('postedData', $post);
		$this->set('uploadForm', $this->getImageUploadForm());
		$this->set('pageSize', static::PAGESIZE);
		$htm = $this->_template->render(false,false,"merchants/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function view($user_id){
		$user_id = FatUtility::int($user_id);
		if($user_id <= 0){
			FatUtility::dieWithError('Invalid Request!');
		}
		$tbl = new Users();
		$record = $tbl->getUser($user_id);
		$this->set('records',$record);
		$this->_template->render(false,false,'merchants/_partial/view.php');
	}
	
	public function photo($user_id, $w, $h) {
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
	}
	
	private function getImageUploadForm(){
		$frm = new Form('form_image_upload',array('class'=>'web_form','action'=>FatUtility::generateUrl('users','upload-photo')));
		$frm->addHiddenField('','user_id')->requirements()->setRequired();
		$frm->addFileUpload(Info::t_lang('Change'),'photo');
		return $frm;
	}
	
	function uploadPhoto(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		if(!isset($_FILES['photo']['tmp_name'])){
			FatUtility::dieJsonError('Photo is mandatory Field');
		}
		if (!is_uploaded_file($_FILES['photo']['tmp_name'])){
			FatUtility::dieJsonError('Photo couldn\'t upload.');
		}
		$post = FatApp::getPostedData();
		$form = $this->getImageUploadForm();
		$post = $form->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError($form->getValidationErrors());
		}
		$post['user_id'] = FatUtility::int($post['user_id']);
		if(empty($post['user_id'])){
			FatUtility::dieJsonError('Invalid request!');
		}
		
		$attachment = new AttachedFile();
		if ($attachment->saveImage($_FILES['photo']['tmp_name'], AttachedFile::FILETYPE_USER_PHOTO, 
				$post['user_id'], 0, $_FILES['photo']['name'], 0, true)) {
			FatUtility::dieJsonSuccess('Photo Updated!');
		}
		else {
			FatUtility::dieJsonError($attachment->getError());
		}
	}
	
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
		$form->removeField($form->getField('user_password'));
		$tbl = new Users();
		$data = $tbl->getUser($user_id);
		$form->fill($data);
		$this->set('frm',$form);
		$html = $this->_template->render(false,false,'merchants/_partial/form.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	function update(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$form = $this->getForm();
		$form->removeField($form->getField('user_password'));
		$post = $form->getFormDataFromArray(FatApp::getPostedData());
		if($post == false){
			FatUtility::dieJsonError($form->getValidationErrors());
		}
		if(isset($_FILES['photo']['tmp_name'])){
			if (!is_uploaded_file($_FILES['photo']['tmp_name'])){
				FatUtility::dieJsonError('Photo couldn\'t upload.');
			}
			$attachment = new AttachedFile();
			if (!$attachment->saveImage($_FILES['photo']['tmp_name'], AttachedFile::FILETYPE_USER_PHOTO, 
					$post['user_id'], 0, $_FILES['photo']['name'], 0, true)) {
				FatUtility::dieJsonError($attachment->getError());
			}
		
		}
		
		$tbl = new Users();
		$user_id = FatUtility::int($post['user_id']);
		if(empty($user_id)){
			FatUtility::dieJsonError('Invalid request!');
		}
		$email = $post['user_email'];
		
		if($tbl->isEmailExist($email,$user_id)){
			FatUtility::dieJsonError('Email Already Exist!');
		}
		if(!$tbl->updateUser($post,$user_id)){
			FatUtility::dieJsonError($tbl->getError());
		}
		if(!$tbl->updateUserDetail($post,$user_id)){
			FatUtility::dieJsonError($tbl->getError());
		}
		FatUtility::dieJsonSuccess('Record updated!');
	}
	
	private function  getForm(){
		$frm = new Form('editform',array('class'=>'web_form','id'=>'action_form'));
		$frm->addHiddenField('','user_id');
		$frm->addHiddenField('','fIsAjax',1);
		$frm->addTextBox(Info::t_lang('First name'),'udetails_first_name');
		$frm->addTextBox(Info::t_lang('Last name'),'udetails_last_name');
		$frm->addEmailField(Info::t_lang('Email'),'user_email')->requirements()->setRequired();
		$frm->addPasswordField('Password','user_password');
		$frm->addFileUpload(Info::t_lang('Change'),'photo');
		$frm->addSelectBox(Info::t_lang('User Type'),'user_is_merchant',Info::getUserType())->requirements()->setRequired();
		$frm->addSelectBox(Info::t_lang('Status'),'user_active',Info::getSearchUserStatus());
		$frm->addSelectBox(Info::t_lang('Email Verify'),'user_verified',Info::getEmailStatus());
		$frm->addSelectBox(Info::t_lang('Confirm'),'user_confirmed',Info::getUserConfirm());
		
		$frm->addSelectBox(Info::t_lang('Gender'),'udetails_sex',Info::getSex());
		$frm->addDateField(Info::t_lang('DOB'),'udetails_dob','',array('readonly'=>'readonly'));
		$frm->addTextBox(Info::t_lang('Phone number'),'udetails_phone')->requirements()->getInt();
		$frm->addTextArea(Info::t_lang('Address Line1'),'udetails_address1');
		$frm->addTextArea(Info::t_lang('Address Line2'),'udetails_address2');
		$location = new Locations();
		$search = $location->getRegions();
		$search->addMultipleFields(array('region_id','region_name'));
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs, 'region_id');
		$regions =array();
		foreach($records as $region_id=>$region_data){
			$regions[$region_id]=$region_data['region_name'];
		}
		$frm->addSelectBox(Info::t_lang('Region'),'udetails_region_id',$regions);
		$frm->addTextBox(Info::t_lang('Zipcode/Pincode'),'udetails_zip');
		$frm->addTextBox(Info::t_lang('Longitude'),'udetails_longitude');
		$frm->addTextBox(Info::t_lang('latitude'),'udetails_latitude');
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Submit');
		return $frm;
	}
	
	
	
	/* public function search($page = 1, $pagesize = 10) {
		$frm = $this->getUserSearchForm();
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		$srch = new UserSearch();
		if (!empty($post['keyword'])) {
			$srch->addCondition('user_name', 'LIKE', '%' . $post['keyword'] . '%')
			->attachCondition('credential_username', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if ($post['user_active'] > -1) {
			$srch->addCondition('credential_active', '=', $post['user_active']);
		}
		if ($post['user_verified'] > -1) {
			$srch->addCondition('credential_verified', '=', $post['user_verified']);
		}
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$rs = $srch->getResultSet();
		
		$this->set('data', FatApp::getDb()->fetchAll($rs, 'user_id'));
		
		$this->set('pageCount', $srch->pages());
		$this->set('pageNumber', $page);
		$this->set('pageSize', $pagesize);
		
		$this->set('postedData', $post);
		
		$this->set('canVerify', AdminPrivilege::canVerifyUsers(0, true));
		$this->set('canEdit', AdminPrivilege::canEditUsers(0, true));
		
		$this->_template->render(false, false);
	}
	
	public function verify() {
		AdminPrivilege::canVerifyUsers();
		
		$userId = FatApp::getPostedData('userId', FatUtility::VAR_INT);
		$v = FatApp::getPostedData('v', FatUtility::VAR_INT);
		
		$user = new User($userId);
		
		if (!$user->verifyAccount($v)) {
			FatUtility::dieWithError($user->getError());
		}
		
		$this->set('msg', ((1 == $v)?'Account Verified!':'Account Unverified!'));
		
		$this->_template->render(false, false, 'json-success.php');
	}
	
	public function activate() {
		AdminPrivilege::canEditUsers();
		
		$userId = FatApp::getPostedData('userId', FatUtility::VAR_INT);
		$v = FatApp::getPostedData('v', FatUtility::VAR_INT);
		
		$user = new User($userId);
		
		if (!$user->activateAccount($v)) {
			FatUtility::dieWithError($user->getError());
		}
		
		$this->set('msg', ((1 == $v)?'Account Activated!':'Account Deactivated!'));
		
		$this->_template->render(false, false, 'json-success.php');
	} */
	
	private function getUserSearchForm($tab=1) {
		$status = Info::getSearchUserStatus();
		$status['-1']='Does not Matter';
		$verified = Info::getEmailStatus();
		$verified['-1']='Does not Matter';
		$user_type = Info::getUserType();
		$user_type['-1'] ='Does not Matter';
		if($tab==1){
			$frm = new Form('frmUserSearch_tab_1',array('class'=>'web_form', 'onsubmit'=>'search(this,1); return false;'));
			$frm->addTextBox('Name or Email', 'keyword','',array('class'=>'search-input'));
			$frm->addSelectBox('Status', 'user_active', $status, '-1',array('class'=>'search-input'), '');
			$frm->addSelectBox('Verified', 'user_verified', $verified, '-1',array('class'=>'search-input'), '');
			$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		}
		elseif($tab==2){
			$frm = new Form('frmUserSearch_tab_2',array('class'=>'web_form', 'onsubmit'=>'search(this, 2); return false;'));
			$frm->addTextBox('Name or Email', 'keyword','',array('class'=>'search-input'));
			$frm->addSelectBox('Status', 'user_active', $status, '-1', array('class'=>'search-input'), '');
			$frm->addSelectBox('Verified', 'user_verified', $verified, '-1',array('class'=>'search-input'), '');
			$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		}
		elseif($tab==3){
			$frm = new Form('frmUserSearch_tab_3',array('class'=>'web_form', 'onsubmit'=>'search(this, 3); return false;'));
			$frm->addTextBox('Name or Email', 'keyword','',array('class'=>'search-input'));
			$frm->addTextBox('Location', 'location','',array('class'=>'search-input'));
			
			$frm->addSelectBox('Status', 'user_active',$status, '-1',array('class'=>'search-input'), '');
			$frm->addSelectBox('Verified', 'user_verified', $verified, '-1', array('class'=>'search-input'), '');
			$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		}

		
		return $frm;
	}
}