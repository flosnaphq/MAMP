<?php

class HomeController extends AdminBaseController
{
    private $admin_id;
    private $canViewOrder;

    public function __construct($action)
	{
        if (AdminAuthentication::isAdminLogged()) {
            $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
            $this->canViewOrder = AdminPrivilege::canViewOrder($this->admin_id);
        }
        parent::__construct($action);
        $this->set("canViewOrder", $this->canViewOrder);
    }

    public function index()
	{
        $rpt = new Reports();
        $unreadMessage = MessageThread::countUnreadMessage(0);

        $user_totals = $rpt->getUserCount();
        $order_totals = $rpt->getOrderCount();
        $review_totals = $rpt->getReviewCount();
        $activity_totals = $rpt->getActivtyCount();

        $unread_notifications = Notification::getUnreadCount(0);
        $this->set('unread_notifications', $unread_notifications);
        $this->set('review_totals', $review_totals);
        $this->set('user_totals', $user_totals);
        $this->set('order_totals', $order_totals);
        $this->set('unreadMessage', $unreadMessage);

        $this->set('activity_totals', $activity_totals);
        $this->_template->render();
    }

    public function orderList()
	{
        if (!$this->canViewOrder) {
            return false;
        }
		
        $page = 1;
        $pageSize = 5;
        $odr = new Orders();
        $search = $odr->getOrderSearch();
        $search->addGroupBy('order_id');
        $search->addOrder('oactivity_id', 'desc');
        $search->addMultipleFields(array(ORDERS::ORDER_TBL . '.*', "group_concat(oactivity_activity_name SEPARATOR ' [-] ') as ordered", "concat(user_firstname, '', user_lastname) as user_name"));
        $search->setPageNumber($page);
        $search->setPageSize($pageSize);
        $rs = $search->getResultSet();
        $db = FatApp::getDb();

        $records = $db->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $htm = $this->_template->render(false, false, "home/_partial/order-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function themesetup()
	{
        if (!FatUtility::isAjaxCall()) {
            die("Invalid Request");
        }
        $post = FatApp::getPostedData();
        if (empty($post)) {
            FatUtility::dieWithError('Invalid Request');
        }

        $layout = $post['layout'];
        $admin = new Admin();
        $admin_id = AdminAuthentication::getLoggedAdminAttribute('admin_id');
        if ($admin->updateAdminLayoutPrefrence($admin_id, $layout)) {
           $adminAuth = new AdminAuthentication();
           $adminAuth->setAdminLayout($layout);
           FatUtility::dieJsonSuccess("Setting Updated Successfully");
        }
        
        FatUtility::dieWithError("Error While Updating. Please Try Again Later");
    }
    
	public function clearFatCache()
	{
		if(false === defined('CONF_USE_FAT_CACHE') || false === CONF_USE_FAT_CACHE) {
			FatUtility::dieWithError('Error: Fat Cache Not Enabled');
		}
		
	    $result = FatCache::clearAll();
	    
	    if(false !== $result) {
	        FatUtility::dieWithError('Success: Cache has been cleared');
	    } else {
	        FatUtility::dieWithError('Error: Somthing went Wrong!!!');	        
	    }
    }
	
	public function copyImages()
	{
		return;
		$srcPath = CONF_UPLOADS_PATH . '/bkp/user-uploads/';
		$dstPath = CONF_UPLOADS_PATH;
		
		$files = AttachedFile::getAllAttachmentsByType(2);
		
		if(count($files) > 0)
		{
			foreach($files as $key => $file) {
				if(!file_exists($srcPath . $file['afile_physical_path']) || file_exists($dstPath . $file['afile_physical_path'])) {
					continue;
				}
				copy($srcPath . $file['afile_physical_path'], $dstPath . $file['afile_physical_path']);
			}
		}
	}
	
	public function userPendingRequests() 
	{
		$cityRequestCount = UserRequest::getUnreadCount();
		FatUtility::dieJsonSuccess(array('count' => (int) $cityRequestCount));
	}
}