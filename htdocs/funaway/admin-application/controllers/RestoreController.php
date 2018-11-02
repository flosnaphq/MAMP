<?php
class RestoreController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewTestimonial($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditTestimonial($this->admin_id);
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
		$brcmb->add("Database Files");
		$this->set('breadcrumb',$brcmb->output());
                $directory = CONF_INSTALLATION_PATH ."restore/*.sql"; 
                $file_dir = glob($directory); 
                if(is_array($file_dir) && count($file_dir)>0){
                foreach (glob($directory) as $filename) {
                        $filesArr[] = array( 'name'=>basename($filename),'created_date'=>date("Y/m/d H:i:s",filemtime($filename)));
                    } 
                }else{
                        $filesArr  = array();
                } 
		$this->set("arr_listing",$filesArr); 
		//$htm = $this->_template->render(false,false,"restore/index.php",true,true); 
		$this->_template->render ();
	}
	
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Keyword', 'keyword','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function listing($page=1){   
		$pagesize=static::PAGESIZE; 
                $records= array('listserial'=>'sda','testimonial_id'=>'fdd','action'=>'dgfdg');
               // print_r($records);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"restore/index.php",true,true); 
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$id = empty($post['id'])?0:FatUtility::int($post['id']);
		$form = $this->getForm();
		if(!empty($id)){
			$fc = new Testimonial ($id);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$photo = $form->getField('profile_pic');
			$photo->value ='<img src="'.FatUtility::generateUrl('image','testimonial',array($id,100,100),CONF_WEBROOT_URL).'">';
			$form->fill($fc->getFlds());
		}
		
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"testimonials/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		
		$frm->addHiddenField("", Testimonial::DB_TBL_PREFIX.'id');
		$frm->addHtml('','profile_pic','<img src="'.FatUtility::generateUrl('image','testimonial',array(0,100,100),CONF_WEBROOT_URL).'">');
		$frm->addFileUpload('Image','image');
		$frm->addRequiredField('Name',Testimonial::DB_TBL_PREFIX.'name');
		
		$fld = $frm->addTextArea('Content',Testimonial::DB_TBL_PREFIX.'content');
		$fld->htmlAfterField='Max 200 Characters';
		$frm->addTextBox('Display Order',Testimonial::DB_TBL_PREFIX.'display_order');
		$frm->addSelectBox('Status', Testimonial::DB_TBL_PREFIX.'status', Info::getStatus(),1, array(),'');
		$frm->addSubmitButton('', 'btn_submit','Add / Update',array())->htmlAfterField="<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
		return $frm;
	}
	
	public function setup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = $this->getForm ();
		$data = $frm->getFormDataFromArray ( FatApp::getPostedData () );
		if(isset($_FILES['image']['tmp_name']) && !is_uploaded_file($_FILES['image']['tmp_name'])){
			FatUtility::dieJsonError('Photo couldn\'t upload.');
		}
		
		
		
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}
		$id = FatApp::getPostedData(Testimonial::DB_TBL_PREFIX.'id', FatUtility::VAR_INT);
		
		$fc = new Testimonial($id);
		$fc->assignValues($data);

		if (!$fc->save()) {
			FatUtility::dieWithError($fc->getError());
			
		}
		$testimonial_id = $fc->getMainTableRecordId();
		if(!empty($_FILES['image']['tmp_name'])){
			$attachment = new AttachedFile();
			if (!$attachment->saveImage($_FILES['image']['tmp_name'], AttachedFile::FILETYPE_TESTIMONIAL, 
					$testimonial_id, 0, $_FILES['image']['name'], 0, true)) {
				FatUtility::dieJsonError($attachment->getError());
			}
		}

		$this->set('msg', 'Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	public function changeDisplayOrder() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$id = isset($post['id'])?FatUtility::int($post['id']):0;
		if(!isset($post['display_order']) || $id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$data[Testimonial::DB_TBL_PREFIX.'display_order'] = $post['display_order'];
		$fc = new Testimonial($id);
		$fc->assignValues($data);

		if (!$fc->save()) {
			FatUtility::dieWithError($fc->getError());
			
		}
		FatUtility::dieJsonSuccess('Display Order Changed!');
	}
	
	public function view($testimonial_id){
		$testimonial_id = FatUtility::int($testimonial_id);
		if($testimonial_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new Testimonial($testimonial_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$records =  $fc->getFlds();
		
		$this->set('records',$records);
		
		$this->_template->render(false,false,"testimonials/_partial/view.php");
	}
	
}
