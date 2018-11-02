<?php

class NavigationMenu {

    protected $controller = "";
    protected $action = "";

    public function __construct($controller, $action) {
        $this->controller = $controller;
        $this->action = $action;
    }

    public function headerMenu() {
        require_once CONF_APPLICATION_PATH . "config/admin_menu.php";

        return $header;
    }

    public function getMenu() {
        $menu = $this->headerMenu();
        $html = "";
        $this->renderMenu($menu, $html);

        return $html;
    }

    public function renderMenu($menu, &$html, $wraperClass = "class=leftmenu",$isChild = false) {

        $html.="<ul $wraperClass>";
        foreach ($menu as $key => $value) {
            $menuAction = "";
            $menuController = "";
            if (!isset($value['child'])) {
                $menuAction = isset($value['action']) && !empty($value['action']) ? $value['action'] : 'index';
                $menuController = isset($value['controller']) && !empty($value['controller']) ? $value['controller'] : 'home';
            }

            $childs = "";
            $haveChild = "";
            $childsArray = isset($value['child']) && is_array($value['child']) ? $value['child'] : array();

            $is_active = $this->is_menu_active($menuController, $menuAction) || $this->is_menu_child_active($childsArray);

            if (isset($value['callback']) && !$this->checkAccess($value['callback'])) {
                continue;
            }

            $childPermision = false;
            if (isset($value['child']) && is_array($value['child'])) {

                $childPermision = $this->canAccessChilds($value['child']);
                $haveChild = "haschild";
                $this->renderMenu($value['child'], $childs, ($is_active) ? 'style="display:block;"' : '',true);
            }

            if (!empty($haveChild) && !$childPermision) {
                continue;
            }

            if ($haveChild) {
                $link = "javascript:void(0)";
            } else {
                $link = Fatutility::generateUrl($menuController, $menuAction);
            }

            $anchor_id = isset($value['id']) ? $value['id'] : '';

            $is_selected = ($is_active) ? "active" : '';
            $html.="<li class='$haveChild  $is_selected'>";
            $html.="<a href='" . $link . "' class='$is_selected'  id='" . $anchor_id . "' >$key";	
			

			$pendingRecordCount = $this->getPendingRecordsCount($menuController);

			if ($isChild && $pendingRecordCount) {
				$html .= " <span class='badge'>($pendingRecordCount)</span>";
			} 
			
			$html .="</a>";
            $html.=$childs;
            $html.="</li>";
        }



        $html.="</ul>";
    }
	
	public function getPendingRecordsCount($controller)
	{	
		 switch ($controller) {	 
            case 'activities':
                return $this->pendingActivitiesCount();
            case 'activityAbuses':
                return $this->pendingActivityAbuseCount();	
            case 'reviews':
                return $this->pendingReviewsCount();					
            case 'withdrawal-requests':
                return $this->pendingWithdrawalRequestCount();		
            case 'blogcomments':
                return $this->pendingBlogCommentsCount();	
            case 'userRequest':
                return $this->pendingUserRequestCount();					
            case 'notifications':
                return $this->pendingNotificationsCount();					
            default:			
                return 0;
		 }
	}
	
	public function pendingActivitiesCount()
	{
		$db = FatApp::getDb();
		$srchObj = Activity::getSearchObject();
		$srchObj->addCondition("activity_confirm", "=", 0);
		$srchObj->addMultipleFields(array('count(activity_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;		
	}
	
	public function pendingActivityAbuseCount()
	{
		$db = FatApp::getDb();
		$srchObj = AbuseReport::getSearchObject();
		$srchObj->addCondition("abreport_taken_care", "=", 0);
		$srchObj->addMultipleFields(array('count(abreport_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;		
	}	
	
	public function pendingReviewsCount()
	{
		$db = FatApp::getDb();
		$srchObj = AbuseReport::getSearchObject();
		$srchObj->addCondition("abreport_taken_care", "=", 0);
		$srchObj->addMultipleFields(array('count(abreport_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;			
	}	
	
	public function pendingWithdrawalRequestCount()
	{
		$db = FatApp::getDb();
		$srchObj = WithdrawalRequests::getSearchObject();
		$srchObj->addCondition("withdrawalrequest_status", "=", 0);
		$srchObj->addMultipleFields(array('count(withdrawalrequest_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;		
	}	
	
	public function pendingBlogCommentsCount()
	{
		$db = FatApp::getDb();
		$srchObj = Blogcomments::search();
		$srchObj->addCondition("comment_status", "=", 0);
		$srchObj->addMultipleFields(array('count(comment_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;		
	}	
	
	public function pendingUserRequestCount()
	{
		$db = FatApp::getDb();
		$srchObj = UserRequest::getSearchObject();
		$srchObj->addCondition("ucrequest_status", "=", 0);
		$srchObj->addMultipleFields(array('count(ucrequest_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;		
	}	
	
	public function pendingNotificationsCount()
	{
		$db = FatApp::getDb();
		$srchObj = Notification::getSearchObject();
		$srchObj->addCondition("notification_is_read", "=", 0);
		$srchObj->addMultipleFields(array('count(notification_id) as countOfRec'));
		
		$srchResult = $db->fetch($srchObj->getResultset());
		$count = FatUtility::int($srchResult['countOfRec']);
		
		return $count;		
	}	
	
    private function is_menu_child_active($childs = array()) {


        foreach ($childs as $value) {
            $menuAction = isset($value['action']) && !empty($value['action']) ? $value['action'] : 'index';
            $menuController = isset($value['controller']) && !empty($value['controller']) ? $value['controller'] : 'home';

            if ($this->is_menu_active($menuController, $menuAction)) {
                return true;
            }
        }
        return false;
    }

    private function is_menu_active($controller = "home", $action = "index") {
        $ucontroller = str_replace("Controller", '', $this->controller);

        if (FatUtility::camel2dashed($ucontroller) == FatUtility::camel2dashed($controller) && FatUtility::camel2dashed($this->action) == FatUtility::camel2dashed($action)) {

            return true;
        }

        return false;
    }

    private function canAccessChilds($childs) {

     
      
        foreach ($childs as $cont) {
   
            if ($this->checkAccess($cont['callback'])) {
                return true;
            }
        }

        return false;
    }

    protected function checkAccess($callback) {

        $adminId = AdminAuthentication::getLoggedAdminAttribute('admin_id');

        return call_user_func_array(array("AdminPrivilege", $callback), array($adminId));
    }

}
?>

