<?php
class WishlistController extends UserController {
	
	public function __construct($action){
		/* $ajaxCallArray = array('listing','form','setup','cmsDisplaySetup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			//die("Invalid Action");
		} */
		
		parent::__construct($action);
		$this->set("class","is--dashboard");
		$this->set("action",$action);
		$this->set("controller",'wishlist');
	}
	
	function fatActionCatchAll(){
		FatUtility::exitWithErrorCode(404);
	}
	
	public function index() {
		$brcmb = new Breadcrumb();
		$brcmb->add(Info::t_lang('Account'));
		$brcmb->add(Info::t_lang('Wishlist'));
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render();
	}
	
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
		$data = FatApp::getPostedData();
		$e = new Activity();
		$search = Activity::getSearchObject();
		$search->joinTable('tbl_attached_files','LEFT JOIN','afile_record_id = activity_id and afile_type = '.AttachedFile::FILETYPE_ACTIVITY_PHOTO);
		$search->joinTable('tbl_wishlist','Inner JOIN','wishlist_activity_id = activity_id ');
		$search->addCondition('wishlist_user_id','=',$this->userId);
		$search->addCondition('activity_confirm','=',1);
		$search->addCondition('activity_active','=',1);
		$search->addMultipleFields(array('tbl_activities.*','substring_index(group_concat(afile_id),",",3) as activity_images'));
		$search->addGroupBy('activity_id');
		$search->addOrder('wishlist_date','desc');
		$page = $data['page'];
		$page = FatUtility::int($page);
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"wishlist/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}


	
	function delete(){
		$post = FatApp::getPostedData();
		$activity_id = @$post['activity_id'];
		$activity_id = FatUtility::int($activity_id);
		if(!($activity_id > 0)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$wishlist = new Wishlist();
		if(!$wishlist->deleteActivity($this->userId, $activity_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_PWENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('ACTIVITY_DELETED'));
	
	}
	
	
	
	
}