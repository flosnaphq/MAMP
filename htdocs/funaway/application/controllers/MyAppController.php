<?php

class MyAppController extends FatController {

    public $page_class = '';

    public function __construct($action, $isSetDefaultMetaData = true) {
        /* $ajaxCallArray = array('listing','form','setup','cmsDisplaySetup');
          if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
          //die("Invalid Action");
          } */

        parent::__construct($action);
        if ($isSetDefaultMetaData) {
            $this->setDefaultMetaTags();
        }

        $controller = substr($this->_controllerName, 0, (strlen($this->_controllerName)) - strlen('Controller'));
		
        if ((FatApp::getConfig("CONF_MAINTENANCE", FatUtility::VAR_INT, 0) == 1) && (strtolower($controller) != "maintenance")) {
            FatApp::redirectUser(FatUtility::generateUrl('maintenance'));
        }

		$this->set("controller", $controller);
		$this->set("action", $this->_actionName);

        $this->page_class = "is--" . strtolower($controller);
		
        if ($action != "index") {
            $this->page_class.= '  ' . $this->page_class . '-' . strtolower($this->_actionName);
        }
        
        $isUserLogged = false;
        $loggedUserId = 0;
        $user_name = '';
        $loged_user_type = 0;
        if (User::isUserLogged()) {
            $user_name = User::getLoggedUserAttribute('user_firstname');
            $loged_user_type = User::getLoggedUserAttribute('user_type');
            $isUserLogged = true;
            $loggedUserId = User::getLoggedUserId();
        }

        // var_dump($become_a_host);exit;
        $become_a_host_link = Route::getRoute('cms', 'become-a-host');
        $this->set("become_a_host_link", $become_a_host_link);
        $this->set("isUserLogged", $isUserLogged);
        $this->set("loggedUserId", $loggedUserId);
        $this->set("page_class", $this->page_class);
        $this->set("user_name", $user_name);
        $this->set("loged_user_type", $loged_user_type);

        $csrch = Currency::getSearchObject();
        $csrch->addCondition('currency_active', '=', 1);
        $csrch->addFld('currency_id');
        $csrch->addFld('currency_code');
        $rs = $csrch->getResultSet();
        $currency = FatApp::getDb()->fetchAllAssoc($rs);
		
        $this->set('headerRegions', Activity::getHeaderCitiesList());

        $this->set('headerServices', Activity::getHeaderServicesList());
		
		if (User::isUserLogged()) {
			$this->set('unreadNotifications', Notification::getUnreadCount($loggedUserId, $loged_user_type));
			$this->set('unreadMessages', MessageThread::countUnreadMessage($loggedUserId));
		}
        $this->_template->addJs(array('common-js/plugins/slick.min.js', 'js/typeahead/typeahead.bundle.js'));
        //$this->_template->addCss(array('css/typehead.css'));
        $this->set('currencyopt', $currency);
    }

    function setDefaultMetaTags() {
        $metaData['title'] = FatApp::getConfig('meta_title');
        $metaData['keywords'] = FatApp::getConfig('meta_keyword');
        $metaData['description'] = FatApp::getConfig('meta_description');
        $metaData['og:image'] = FatUtility::generateFullUrl('image', 'ogImage', array(FatApp::getConfig('og_image')), '/');
        $metaData['og:type'] = FatApp::getConfig('og_type');
        $metaData['og:title'] = FatApp::getConfig('og_title');
        $metaData['og:url'] = FatUtility::generateFullUrl('', '', array(), '/');
        $metaData['og:description'] = FatApp::getConfig('og_description');
        $metaData['other_tags'] = FatApp::getConfig('CONF_META_OTHER_TAGS');
        $this->set("__metaData", $metaData);
    }

}
