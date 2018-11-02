<?php
class Cms extends MyAppModel {
	const DB_TBL = 'tbl_cms';
	const DB_TBL_PREFIX = 'cms_';
	const CMS_POSITION_TBL ='tbl_cms_positions';
	const CMS_BROWSE_POSITION_TYPE = 1;
	const CMS_ABOUT_POSITION_TYPE = 2;
	
	public function __construct($cms_id = 0) {
		$cms_id = FatUtility::convertToType($cms_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $cms_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
		$srch->addOrder(static::DB_TBL_PREFIX . 'name');
		return $srch;
	}
	
	// $active = false if you want to get data without check status
	function getCms($cms_id, $active=1){
		$data = $this->getAttributesById($cms_id);
		if($active !== false){
			if(isset($data['cms_active']) && $data['cms_active'] != $active){
				$data = array();
			}
		}
		return $data;
	}
	
	function getDefaultCms($cms_id){
		$data = $this->getAttributesById($cms_id);
		return $data;
	}
	
	function getCmsLink($cmd_ids = array()){
		$cmd_ids = implode(',',$cmd_ids);
		$cms = new Cms();
		$footer_cms = Cms::getSearchObject();
		$footer_cms->addDirectCondition(static::DB_TBL_PREFIX.'id in ('.$cmd_ids.')');
		$footer_cms->addCondition(static::DB_TBL_PREFIX.'active','=',1);
		$footer_cms->addMultipleFields(array(
										static::DB_TBL_PREFIX.'id',
										static::DB_TBL_PREFIX.'slug',
										static::DB_TBL_PREFIX.'name',
										));
		$rs = $footer_cms->getResultSet();
		return $rows = FatApp::getDb()->fetchAll($rs,'cms_id');
	}
	
	function getCmsBySlug($slug){
		$srch = self::getSearchObject();
		$srch->addCondition(static::DB_TBL_PREFIX.'slug','=',$slug);
		$srch->addCondition(static::DB_TBL_PREFIX.'active','=',1);
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetch($rs);
	}
	
	function getValidSlug($name){
		$srch = new SearchBase(static::DB_TBL);
		$name = strtolower($name);
		$main_slug = preg_replace("/[\s]/", "-", $name);
		$slug = $main_slug;
		$i=1;
		while($this->isExistSlug($slug)){
			$slug =$main_slug.'-'.$i;
			$i++;
		}
		return $slug;
	}
	
	function isExistSlug($slug, $cms_id = 0)
	{
		$srch = new SearchBase(static::DB_TBL);
		
		$srch->addCondition(static::DB_TBL_PREFIX.'slug','LIKE', $slug);
		
		if($cms_id > 0){
			$srch->addCondition(static::DB_TBL_PREFIX.'id','!=', $cms_id);
		}
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		if( $row = FatApp::getDb()->fetch($rs))
		{
			return true;
		}
		
		$srch = new SearchBase(UrlRewrite::DB_TBL);
		
		$srch->addCondition(UrlRewrite::DB_TBL_PREFIX . 'custom', 'LIKE', $slug);
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		
		if( $row = FatApp::getDb()->fetch($rs))
		{
			return true;
		}
		
		return false;
	}
	
	function getTermsPages(){
		$srch =  new SearchBase(static::DB_TBL);
		$srch->addCondition(self::DB_TBL_PREFIX.'type','=',1);
		$srch->addCondition(self::DB_TBL_PREFIX.'active','=',1);
		$srch->addMultipleFields(array(
									self::DB_TBL_PREFIX.'slug',
									self::DB_TBL_PREFIX.'name',
									)
								);
		$srch->addOrder(self::DB_TBL_PREFIX.'display_order','asc');
		$rs = $srch->getResultSet();
		
		return FatApp::getDb()->fetchAll($rs);
	}
	
	public function getPosition($cms_id){
		$return_data = array();
		$cms_id = FatUtility::int($cms_id);
		$search = new SearchBase(static::CMS_POSITION_TBL);
		$search->addCondition('cmsposition_cms_id','=',$cms_id);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs,'cmsposition_position_id');
		$i=0;
		$positions = Info::getCmsPositions();
		foreach($positions as $position_id=>$position_name){
			if(array_key_exists($position_id,$records)){
				$return_data['positions'][$i] = $position_id;
				$return_data['positions_name'][$i] = Info::getCmsPositions($position_id);
			}
			$i++;
		}
		
		return $return_data;
	}
	
	
	public function savePositions($cms_id, $postions=array()){
		$db = FatApp::getDb();
		$db->deleteRecords(static::CMS_POSITION_TBL,array('smt'=>'cmsposition_cms_id = ?','vals'=>array($cms_id)));
		$tbl = new TableRecord(static::CMS_POSITION_TBL);
		foreach($postions as $postion){
			$data['cmsposition_position_id'] = $postion;
			$data['cmsposition_cms_id'] = $cms_id;
			$tbl->assignValues($data);
			if(!$tbl->addNew()){
				$this->error='Something went Wrong!';
				return false;
			}
		}
		return true;
	}
	
	static function getCmsLinks($position = 0){
		$srch = new SearchBase(static::DB_TBL);
		$srch->joinTable(self::CMS_POSITION_TBL,'left Join','cmsposition_cms_id = cms_id');
		
		if($position > 0){
			$srch->addCondition('cmsposition_position_id','=',$position);
		}
		$srch->addCondition(self::DB_TBL_PREFIX.'active','=',1);
		$srch->addOrder(self::DB_TBL_PREFIX.'display_order');
		$srch->addFld('cms_slug');
		$srch->addFld('cms_id');
		$srch->addFld('cms_name');
		$srch->addFld('cms_type');
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs);
	}
	
	function getTermsDefalutPage(){
		$srch =  new SearchBase(static::DB_TBL);
		$srch->addCondition(static::DB_TBL_PREFIX.'active','=',1);
		$srch->addCondition(static::DB_TBL_PREFIX.'type','=',1);
		$srch->addOrder(static::DB_TBL_PREFIX.'display_order','asc');
		$srch->setPageSize(1);
		$srch->setPageNumber(1);
		$rs = $srch->getResultSet();
		
		return FatApp::getDb()->fetch($rs);
	}
}