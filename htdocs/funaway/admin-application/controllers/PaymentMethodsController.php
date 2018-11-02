<?php
class PaymentMethodsController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	const PAGESIZE=50;
	
	public function __construct($action) {
		
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewPaymentMehods($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditPaymentMehods($this->admin_id);
		
		if(!$this->canView){
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index(){
		$this->set('canEdit', $this->canEdit);
		$srchFrm = $this->PaymentMethodSearchForm();
		$this->set('srchFrm', $srchFrm);
		$this->_template->render();
	}
	
	private function PaymentMethodSearchForm()
	{
		$frm = new Form('frmPaymentMethodSearchPaging');
		return $frm;
	}
	
	public function listing($page = 1)
	{
		if(!FatUtility::isAjaxCall())
		{
			FatUtility::dieJsonError('Invalid Request');
		}
		
		$this->set('canEdit', $this->canEdit);
		$srchFrm = $this->PaymentMethodSearchForm();
		$post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
		$pagesize = FatApp::getConfig('CONF_DEFAULT_ADMIN_PAGING_SIZE', FatUtility::VAR_INT, 10);
		$pagesize = ($pagesize > 0 ? $pagesize : 10);
		
		$srch = new SearchPaymentMethods();
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$rs = $srch->getResultSet();
		$this->set('data', FatApp::getDb()->fetchAll($rs, 'pmethod_id'));
		
		$pageCount = $srch->pages();
		$this->set('pageCount', $pageCount);
		
		if($pageCount > 1){
			$this->set('pagination', $this->getPagination($pageCount, $page, 2));
		}
		$this->set('pageNumber', $page);
		$this->set('pageSize', $pagesize);
		$this->set('postedData', $post);

		$this->_template->render(false, false, 'payment-methods/listing.php', false);
		
	}
	
	public function form($pmethodId = 0)
	{
		if( $this->canEdit!==true )
		{
			die("Unauthorized Access!");
		}
		$frm = $this->paymentMethodEditForm();
		if($pmethodId > 0)
		{
			$paymentMethodsObj = new PaymentMethods($pmethodId);
			$data = $paymentMethodsObj->getAttributesById($paymentMethodsObj->getMainTableRecordId());
			$afile = AttachedFile::getAttachment(AttachedFile::FILETYPE_PMETHOD_IMAGE, $pmethodId);
			
			$fld = $frm->getField('pmethod_icon');
			if( ! empty($afile)){
				$fld->htmlBeforeField = '<div id="pmethod_image" >';
				$fld->htmlAfterField = '<p><b>Current Icon:</b><br /><img width="75" src="'. FatUtility::generateUrl('image', 'paymentMethod', array($pmethodId, 150,150, '?tid=' . rand()) ,'/') .'" /></p></div>';
			}
			
			$frm->fill( $data );
		}
		$this->set( 'frm',$frm );
		$this->_template->render();
	}
	
	public function updatePaymentMethod()
	{
		if( ! FatUtility::isAjaxCall() )
		{
			FatUtility::dieJsonError('Invalid Request');
		}
		if( $this->canEdit!==true )
		{
			Message::addErrorMessage("Invalid Access.");
			$this->set('msg', Message::getHtml());
			$this->_template->render(false, false, 'json-error.php', true, false);
		}
		
		$frm = $this->paymentMethodEditForm();
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		$pmethod_id = FatUtility::int($post['pmethod_id']);

		if( ! $frm->validate($post))
		{
			Message::addErrorMessage(current($frm->getValidationErrors()));
			$this->set('msg', Message::getHtml());
			$this->_template->render(false, false, 'json-error.php', true, false);
		}
		
		$paymentMethodsObj = new PaymentMethods($pmethod_id);
		$paymentMethodsObj->assignValues($post);
		
		if( ! $paymentMethodsObj->save())
		{
			Message::addErrorMessage($paymentMethodsObj->getError());
			FatUtility::dieJsonError(Message::getHtml());
		}
		
		if ($_FILES['pmethod_icon'])
		{
			$file = $_FILES['pmethod_icon'];
			if($file['error'] == 0 && is_uploaded_file($file['tmp_name']))
			{
				$validArr = ApplicationConstants::validImageMimeTypes();
				if( in_array(mime_content_type($file['tmp_name']), $validArr) )
				{
					$uploadPath = CONF_UPLOADS_PATH . AttachedFile::PMETHOD_IMGS_FOLDER;
					if($pmethod_id < 1)
					{
						$pmethod_id = $paymentMethodsObj->mainTableRecordId;
					}
					$fileHandlerObj = new AttachedFile();
					if( ! $res = $fileHandlerObj->saveAttachment($file['tmp_name'], 
								AttachedFile::FILETYPE_PMETHOD_IMAGE,
								$pmethod_id, 
								0,
								$file['name'], 0, true, $uploadPath))
					{
						Message::addErrorMessage('Error in uploading file:= '. $file['name'] . ' Error: ' . $fileHandlerObj->getError());
					}
					
				}
			}
		}
		
		Message::addMessage('Record saved');
		
		FatUtility::dieJsonSuccess('');
	}
	
	function updateStatus($pmethod_id)
	{
		if( ! FatUtility::isAjaxCall())
		{
			FatUtility::dieJsonError('Invalid Request');
		}
		
		if( $this->canEdit!==true )
		{
			Message::addErrorMessage("Invalid Access.");
			$this->set('msg', Message::getHtml());
			$this->_template->render(false, false, 'json-error.php', true, false);
		}
		
		$paymentMethodsObj = new PaymentMethods($pmethod_id);
		$paymentMethodStatus = $paymentMethodsObj->getAttributesById($pmethod_id, 'pmethod_active');

		if($paymentMethodStatus === false)
		{
			Message::addErrorMessage($paymentMethodsObj->getError());
			$this->set('msg', Message::getHtml());
			$this->_template->render(false, false, 'json-error.php', true, false);
		}
		if($paymentMethodStatus == 0)
		{
			$pmethod_active = 1;
		}
		elseif($paymentMethodStatus == 1)
		{
			$pmethod_active = 0;
		}
		
		$paymentMethodsObj->assignValues(array('pmethod_active' => $pmethod_active));
		if($paymentMethodsObj->save())
		{
			Message::addMessage("Payment Method Status Updated.");
			$this->set('msg', Message::getHtml());
			$this->_template->render(false, false, 'json-success.php', true, false);
		}
		else
		{
			Message::addErrorMessage($paymentMethodsObj->getError());
			$this->set('msg', Message::getHtml());
			$this->_template->render(false, false, 'json-error.php', true, false);
		}
	}
	
	public function settings($pmethod_id)
	{
		if( $this->canEdit !== true )
		{
			Message::addErrorMessage('Invalid Access!');
			FatApp::redirectUser(FatUtility::generateUrl('PaymentMethods'));
		}
		$paymentMethodsObj = new PaymentMethods($pmethod_id);
		
		if( ! $data = $paymentMethodsObj->getAttributesById($pmethod_id) )
		{
			Message::addErrorMessage('Invalid Request!');
			FatApp::redirectUser(FatUtility::generateUrl('PaymentMethods'));
		}
		
		$frm = $this->settingsForm();
		$this->set('frm',$frm);
		$this->_template->render();
	}
	
	private function paymentMethodEditForm(){
		$frm = new Form('paymentMethodEditForm');
		$frm->addTextBox('Name','pmethod_name')->requirements()->setRequired();
		$frm->addTextArea('Details','pmethod_details');
		$frm->addTextBox('Display Order','pmethod_display_order')->requirements()->setRequired();
		
		$fld = $frm->addFileUpload('Icon', 'pmethod_icon');
		
		$frm->addHiddenField('', 'pmethod_id');
		$frm->addSubmitButton('','btn_submit','Submit');
		return $frm;
	}
	
	private function settingsForm(){
		$frm = new Form('paymentMethodSettingsForm');
		return $frm;
	}
}
?>