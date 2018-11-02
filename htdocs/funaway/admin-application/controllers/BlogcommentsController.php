<?php
class BlogcommentsController extends AdminBaseController{
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action) {
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewBlogComment($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditBlogComment($this->admin_id);
		if(!$this->canView){
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);;	
    }
	
	public function index() {
        if($this->canView !== true){
			FatUtility::dieWithError('Unauthorized Access!');
		} 
		$brcmb = new Breadcrumb();
		$brcmb->add("Blog Comments Management");
        $frm = $this->getSearchForm();
		$this->set('breadcrumb',$brcmb->output());
        $this->set('frmComments', $frm);
        $this->_template->render();
    }
	
	public function listComments($page = 1) {
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
		$srch =Blogcomments::search();
		if(isset($post['comment_author_name']) && $post['comment_author_name'] != ""){
			$srch->addCondition('comment_author_name', 'like', '%' . $post['comment_author_name'] . '%');
		}
		if(isset($post['comment_status']) && $post['comment_status'] != ""){
			$srch->addCondition('comment_status', '=', $post['comment_status']);
		}
		$srch->joinTable('tbl_blog_post', 'INNER JOIN', '`post_id` = `comment_post_id`');
		$srch->addMultipleFields(array('`comment_id`', '`post_id`', '`post_seo_name`', '`comment_author_name`', '`comment_author_email`', '`comment_content`', '`comment_status`', '`post_title`'));
		$srch->addOrder('comment_id', 'DESC');
		 
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$rs = $srch->getResultSet();
		
		$this->set( 'list', FatApp::getDb()->fetchAll($rs, 'comment_id') );
		
		$pageCount = $srch->pages();
		$this->set('pageCount', $pageCount);
		
		if($pageCount > 1){
			$this->set('pagination', $this->getPagination($pageCount, $page, 5));
		}
		$this->set('pageNumber', $page);
		$this->set('pageSize', $pagesize);
		$this->set('postedData', $post);
		
		$this->_template->render( false, false, 'blogcomments/list-comments.php', false, true );         
        
    }
	
	private function getSearchForm() {
        $frm = new Form('frmSearch');
         
        $frm->addTextBox(Info::t_lang('COMMENT_AUTHOR_NAME'), 'comment_author_name');
        $frm->addSelectBox(Info::t_lang('COMMENT_STATUS'), 'comment_status', BlogConstants::commentStatus() , "", array(),Info::t_lang('DOES_NOT_MATTER'));
        $frm->addHiddenField('', 'page', 1);
		
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SEARCH'));
		$frm->addButton('', 'cancel_search', Info::t_lang('SHOW_ALL'));
        
        return $frm;
    }
	
	public function view($comment_id = 0) {
        $brcmb = new Breadcrumb();
		$brcmb->add("Blog Comments Management", FatUtility::generateUrl('blogcomments'));
		$brcmb->add("View");
		$this->set('breadcrumb',$brcmb->output());
		if ($this->canView != true) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $comment_id = FatUtility::convertToType($comment_id,FatUtility::VAR_INT);
        if ($comment_id < 1) {
            Message::addErrorMessage('Invalid Request');
			FatUtility::dieJsonError(Message::getHtml());
        }
		$blogcomment = new Blogcomment($comment_id);
		if($blogcomment->loadFromDb())
		{
			$comment_data = $blogcomment->getFlds();
			$status_array = BlogConstants::commentStatus() ;
			//unset($status_array[$comment_data['comment_status']]);
			$frm = $this->getCommentStatusUpdateForm();
			$frm->getField('comment_status')->options = $status_array;
			$frm->getField('comment_id')->value = $comment_data['comment_id'];
			$frm->fill(array('comment_status'=>$comment_data['comment_status']));
			$this->set('frmComment', $frm);
			$this->set('comment_data', $comment_data);
		}
        $this->_template->render();
    }
		
	private function getCommentStatusUpdateForm() {
		
        $frm = new Form('frmBlogComments');        
        $frm->addHiddenField('', 'comment_id');
        $status_array =BlogConstants::commentStatus() ;
        $fld = $frm->addSelectBox(Info::t_lang('COMMENT_STATUS'), 'comment_status', $status_array, '', array(), Info::t_lang('DOES_NOT_MATTER'));
		$frm->addSubmitButton('', 'update', Info::t_lang('UPDATE'));
		 
        return $frm;
    }
	
	public function updateStatus() {
        if ($this->canEdit != true) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
		if(!FatUtility::isAjaxCall()){
			Message::addErrorMessage('Invalid Request');
			FatApp::redirectUser(FatUtility::generateUrl(''));
		}
		$frm = $this->getCommentStatusUpdateForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
		if (false == $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
		$comment_id=FatUtility::convertToType($post['comment_id'],FatUtility::VAR_INT);
        $blogcomment=new Blogcomment($comment_id);
		$blogcomment->assignValues($post);
		if (!$blogcomment->save()) {
			FatUtility::dieJsonError($blogcomment->getError());
		}
		 
		//Message::addMessage(Info::t_lang('STATUS_UPDATED'));
		FatUtility::dieJsonSuccess(Info::t_lang('STATUS_UPDATED'));
       
    }
	
	public function delete($comment_id = 0) {
		
		if ($this->canEdit != true) {
			FatUtility::dieJsonError('Unauthorized Access!');
        }
        $comment_id = FatUtility::convertToType($comment_id,FatUtility::VAR_INT);
		if(!FatUtility::isAjaxCall() || $comment_id < 1){
			Message::addErrorMessage('Invalid Request');
			FatApp::redirectUser(FatUtility::generateUrl(''));
		}        
		$blogcomment = new Blogcomment($comment_id);
        if (!$blogcomment->deleteRecord()) {
			 FatUtility::dieJsonError(FatApp::getDb()->getError());
        }
		
		/* Message::addMessage(Info::t_lang('STATUS_DELETED')); */
		FatUtility::dieJsonSuccess(Info::t_lang('STATUS_DELETED'));
	}
}