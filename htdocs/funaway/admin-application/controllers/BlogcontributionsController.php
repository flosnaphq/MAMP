<?php
class BlogcontributionsController extends AdminBaseController {

	private $canView;
	private $canEdit;
	private $admin_id; 
    public function __construct($action) {		  
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewBlogCont($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditBlogCont($this->admin_id);
		if(!$this->canView){
			if(FatUtility::isAjaxCall()){
				FatUtility::dieJsonError('Unauthorized Access!');
			}
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);;	 
    }

    public function index() {
        
		$brcmb = new Breadcrumb();
		$brcmb->add("Blog Contribution Management");
		$this->set('breadcrumb',$brcmb->output());
        $frm = $this->getSearchForm();
        $this->set('frmContributions', $frm);
		// $this->_template->addCss(array('css/blog.css'));
        $this->_template->render();		
    }

    public function listContributions() {
		if(!FatUtility::isAjaxCall()){
			Message::addErrorMessage('Invalid Request');
			FatApp::redirectUser(FatUtility::generateUrl(''));		
		} 
		$pagesize = static::PAGESIZE;
		$pagesize = ($pagesize > 0 ? $pagesize : 10);
		$post = FatApp::getPostedData();
		$page = 1;
		if (isset($post['page']) && FatUtility::convertToType($post['page'] , FatUtility::VAR_INT) > 0) {
			$page = FatUtility::convertToType($post['page'] , FatUtility::VAR_INT);
		} else {
			$post['page'] = $page;
		}
		$srch =Blogcontributions::search();
		if (isset($post['contribution_author_first_name']) && $post['contribution_author_first_name'] != "") {
            $srch->addCondition('contribution_author_first_name', 'like', '%' . $post['contribution_author_first_name'] . '%');
        }
        if (isset($post['contribution_status']) && $post['contribution_status'] != "") {
            $srch->addCondition('contribution_status', '=', $post['contribution_status']);
        }
        $srch->addOrder('contribution_id', 'DESC');
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$rs = $srch->getResultSet();
		$this->set( 'list', FatApp::getDb()->fetchAll($rs, 'contribution_id') );
		
		$pageCount = $srch->pages();
		$this->set('pageCount', $pageCount);
		
		if($pageCount > 1){
			$this->set('pagination', $this->getPagination($pageCount, $page, 5));
		}
		$this->set('pageNumber', $page);
		$this->set('pageSize', $pagesize);
		$this->set('postedData', $post);
		
		$this->_template->render( false, false, 'blogcontributions/list-contributions.php', false, true );         
    }

    private function getSearchForm() {
		$frm = new Form('frmSearch');
		 
		$frm->addTextBox(Info::t_lang('FIRST_NAME'), 'contribution_author_first_name');
        $frm->addSelectBox(Info::t_lang('CONTRIBUTION_STATUS'), 'contribution_status', BlogConstants::contriStatus(), '', array(), Info::t_lang('DOES_NOT_MATTER'));
        $frm->addHiddenField('', 'page', 1);
		
		$fld = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SEARCH'));
        $frm->addButton('', 'cancel_search', Info::t_lang('SHOW_ALL'));
        
        return $frm;
    }

    private function getContributionStatusUpdateForm() {
		
        $frm = new Form('frmBlogContributions');        
        $frm->addHiddenField('', 'contribution_id', '');
        $frm->addSelectBox(Info::t_lang('CONTRIBUTION_STATUS'), 'contribution_status', BlogConstants::contriStatus(), '', array() , Info::t_lang('DOES_NOT_MATTER') );	 
        $frm->addSubmitButton('', 'update', Info::t_lang('UPDATE'));
        
        return $frm;
    }

    public function getContributionPost($contribution_id=0) {
        $contribution_id = FatUtility::convertToType($contribution_id,FatUtility::VAR_INT);
        if ($contribution_id < 1) {
            return false;
        }
        $srch =Blogcontributions::search();
        $srch->addCondition('contribution_id', '=', $contribution_id);
        $srch->doNotLimitRecords();
        $srch->doNotCalculateRecords();
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function view($contribution_id=0) {
        if ($this->canView != true) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
		$brcmb = new Breadcrumb();
		$brcmb->add("Blog contribution Management",FatUtility::generateUrl('blogcontributions'));
		$brcmb->add("View");
		
        $contribution_id = FatUtility::convertToType($contribution_id,FatUtility::VAR_INT);
        if ($contribution_id < 1) {
			FatUtility::dieWithError( Info::t_lang( 'INVALID_REQUEST' ) );
        }
        $contribution_data = $this->getContributionPost($contribution_id);
	
        $contri_array = BlogConstants::contriStatus();
      //  unset($contri_array[$contribution_data['contribution_status']]);
		$frm = $this->getContributionStatusUpdateForm();
		$frm->fill(array('contribution_status'=>$contribution_data['contribution_status']));
        $frm->getField('contribution_status')->options = $contri_array;
        $frm->getField('contribution_id')->value = $contribution_data['contribution_id'];
        $this->set('frmContributionStatusUpdate', $frm);
        $this->set('contribution_data', $contribution_data);
		$this->set('breadcrumb',$brcmb->output());
        $this->_template->render();
    }

    public function updateStatus() {
        if ($this->canEdit != true) {
			FatUtility::dieJsonError('Unauthorized Access!');
        }
		if(!FatUtility::isAjaxCall()){
			Message::addErrorMessage('Invalid Request');
			FatApp::redirectUser(FatUtility::generateUrl(''));
		}
		
        $frm = $this->getContributionStatusUpdateForm();
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		 
        if ($post == false) {
			FatUtility::dieJsonError($frm->getValidationErrors());
        }
		
		$contribution_id = FatUtility::convertToType($post['contribution_id'],FatUtility::VAR_INT);
        if ($contribution_id < 1) {
           FatUtility::dieWithError( Info::t_lang( 'INVALID_REQUEST' ) );
        }
		$blogcontributions = new Blogcontributions($contribution_id);
		$blogcontributions->assignValues($post);
		if (!$blogcontributions->save()) {
			FatUtility::dieJsonError($blogcontributions->getError());
		}
		
		$contribution_status = $post['contribution_status'];
		$record = $this->getContributionPost($contribution_id);
		$replacements = array(
				'{USER_NAME}' => ucfirst($record['contribution_author_first_name']) . ' ' . ucfirst($record['contribution_author_last_name']),
				'{POSTED_ON}' => FatDate::datePickerFormat($record['contribution_date_time']),
				'{SITE_NAME}' => FatApp::getConfig("CONF_WEBSITE_NAME"),
				'{COMPANY_LOGO}' => ImageHandler::companyLogoForEmail()
		);
		$contri_array = BlogConstants::contriStatus();
		
		if (isset($contri_array[$contribution_status])) {
			$replacements['{STATUS}'] = $contri_array[$contribution_status];
			if(!Email::sendMail($record['contribution_author_email'], 74, $replacements)){
				Message::addErrorMessage(Info::t_lang('SENDING_EMAIL_NOTIFICATION_FAILED_DUE_TO_SOME TECHNICAL_ISSUES'));		
			}
		}
		 
		Message::addMessage(Info::t_lang('STATUS_UPDATED'));
		FatUtility::dieJsonSuccess('');
	}

    public function download( $contributionId = 0 ) {
		
		$contributionId = FatUtility::int( $contributionId );
        if ( $contributionId < 1 ) {
			Message::addErrorMessage( Info::t_lang( 'DOWNLOAD_ERROR_INVALID_CONTRIBUTION_REQUEST' ) );
			FatApp::redirectUser( FatUtility::generateUrl( 'Blogcontributions' ) );
        }
		
		$srch = Blogcontributions::search();
		$srch->addCondition( 'contribution_id', '=', $contributionId );
		$rs = $srch->getResultSet();
		$data = ( $rs )?FatApp::getDb()->fetch( $rs ):array();
		
        if( empty( $data ) ) { 
			Message::addErrorMessage( Info::t_lang( 'DOWNLOAD_ERROR_INVALID_CONTRIBUTION_REQUEST' ) );
			FatApp::redirectUser( FatUtility::generateUrl( 'Blogcontributions' ) );
        } elseif( !isset( $data[ Blogcontribution::DB_TBL_PREFIX . 'file_name'] ) || $data[ Blogcontribution::DB_TBL_PREFIX . 'file_name'] == '' ) {
			Message::addErrorMessage( Info::t_lang( 'DOWNLOAD_ERROR_FILE_NOT_ATTACHED' ) );
			FatApp::redirectUser( FatUtility::generateUrl( 'Blogcontributions' ) );
		}
		
        $filename = CONF_UPLOADS_PATH . AttachedFile::BLOG_POST_CONTRIBUTE_FOLDER . $data[ Blogcontribution::DB_TBL_PREFIX . 'file_name'];
		
		if( !file_exists( $filename ) ) { 
			Message::addErrorMessage( Info::t_lang( 'DOWNLOAD_ERROR_FILE_NOT_FOUND' ) );
			FatApp::redirectUser( FatUtility::generateUrl( 'Blogcontributions' ) );
		}
		
		$display_name = ( isset( $data[ Blogcontribution::DB_TBL_PREFIX . 'file_display_name'] ) && $data[ Blogcontribution::DB_TBL_PREFIX . 'file_display_name'] != '' )?$data[ Blogcontribution::DB_TBL_PREFIX . 'file_display_name']:$data[ Blogcontribution::DB_TBL_PREFIX . 'file_name'];
		
        $finfo = @finfo_open( FILEINFO_MIME_TYPE ); //will return mime type
        $file_mime_type = @finfo_file( $finfo, $filename );

        header( 'Content-Type: ' . $file_mime_type );
        header( 'Content-Disposition: attachment; filename="' . $display_name . '"' );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        header( 'Content-Length: ' . filesize( $filename ) );
        echo @file_get_contents( $filename, true );
        exit(0);
    }

    public function delete($contribution_id = 0, $contribution_file_name = '') {
        if ($this->canEdit != true) {
			FatUtility::dieJsonError('Unauthorized Access!');
        }
		if(!FatUtility::isAjaxCall()){
			Message::addErrorMessage('Invalid Request');
			FatApp::redirectUser(FatUtility::generateUrl(''));
		}
	 
        $contribution_id = FatUtility::convertToType($contribution_id,FatUtility::VAR_INT);
        if ($contribution_id < 1) {
			FatUtility::dieWithError( Info::t_lang( 'INVALID_REQUEST' ) );
        }
        $data = $this->getContributionPost($contribution_id);
		$blogcontribution = new Blogcontribution($contribution_id);
        if (!$blogcontribution->deleteRecord()) {
			 FatUtility::dieJsonError($blogcontribution->getError());
        }  
		$contribution_file_name = $data['contribution_file_name'];
		if (isset($contribution_file_name) && is_string($contribution_file_name) && strlen($contribution_file_name) > 1) {
			FileHandler::deleteFile($contribution_file_name, AttachedFile::BLOG_POST_CONTRIBUTE_FOLDER);
		}
		Message::addMessage(Info::t_lang('CONTRIBUTION_DELETED'));
		FatUtility::dieJsonSuccess('');
	}
	
}