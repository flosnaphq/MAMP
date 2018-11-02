<?php
class Sitemap extends MyAppModel {
	const DB_TBL = 'tbl_navigations';
	const DB_TBL_PREFIX = 'navigation_';
	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);
		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
	}
	
	
	public function getNavigation(){
	
		$srch 		= new SearchBase('tbl_navigations');
		$srch->addMultipleFields(array("navigation_id","navigation_link"));
		$srch->addOrder("navigation_display_order","asc");
		$rs 		= $srch->getResultSet();
		$records 	= FatApp::getDb()->fetchAll($rs);
		$newRecords = array();
		foreach($records as $recordVal){
			if($recordVal['navigation_id'] ==  72)
				continue;
			$newRecords[] = $recordVal;
				
		}
		return $newRecords;
	}
	
	function getService() {
        $service = new Services();
        $srch = $service->getSearchObject();
        $srch->addCondition('service_active', '=', 1);
        $srch->addCondition('service_parent_id', '=', 0);
        $srch->addMultipleFields(array("service_id"));
        $srch->addOrder('service_display_order');

        $rs = $srch->getResultSet();

        $categories = FatApp::getDb()->fetchAll($rs);
        $data = $categories;
		
        return $data;
    }

	
	public function getCountries() {
	
				
		return Country::getCountries();
	
    }
	
	
	
	public function getActivities(){
		$srch = new SearchBase('tbl_activities');
        $srch->doNotCalculateRecords();
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'confirm', '=', 1);
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'state', '>=', 2);
        $srch->addMultipleFields(array("activity_name,activity_id"));
        $rs = $srch->getResultSet();
        return (FatApp::getDb()->fetchAll($rs));
		
	}
	public function getBlogs() { 
        $cat = new BlogCategories(0);
		$record['categories'] = $cat->getSortCategories();
		$postSearch = new SearchBase( BlogPosts::DB_TBL );
        $postSearch->addCondition( BlogPosts::DB_TBL_PREFIX . 'status', '=', 1);
		$postSearch->addMultipleFields( array(BlogPosts::DB_TBL_PREFIX . 'id', BlogPosts::DB_TBL_PREFIX . 'seo_name', BlogPosts::DB_TBL_PREFIX . 'published' ) );
		$postSearch->addOrder( BlogPosts::DB_TBL_PREFIX . 'published', 'DESC' );
		$postSearch->doNotLimitRecords();
		$rs = $postSearch->getResultSet();
	    $record['recentPost'] = FatApp::getDb()->fetchAll( $rs );		
		return $record;
	}
	public function getCmsLinks(){
		$srch = new SearchBase(Cms::DB_TBL);
		$srch->addCondition(Cms::DB_TBL_PREFIX.'active','=',1);
		$srch->addOrder(Cms::DB_TBL_PREFIX.'display_order');
		$srch->addFld('cms_slug');
		$srch->addFld('cms_id');
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs);
	}
}