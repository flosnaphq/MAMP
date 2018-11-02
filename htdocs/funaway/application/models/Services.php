<?php 
class Services extends MyAppModel{
	
	const DB_TBL = 'tbl_services';
	const DB_TBL_PREFIX = 'service_';
//	const SESSION_ELEMENT_NAME = 'UserSession'; 
	
	public function __construct($serviceId = 0) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $serviceId );
	/* 	$this->objMainTableRecord->setSensitiveFields ( array (
				'user_regdate' 
		) ); */
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL,'cservice');
		return $srch;
	}
	
	public static function getCategories($parent = 0, $limit=-1,$extraFields=false){
		$parent = FatUtility::int($parent);
		$srch = new SearchBase('tbl_services');
		$srch->addCondition('service_parent_id','=',  $parent);
		$srch->addCondition('service_active','=',1);
		$srch->addFld('service_id');
		$srch->addFld('service_name');
		if($extraFields)
		{
			$srch->addFld('service_description'); 
		}		
		if($limit > -1){
			$srch->setPageSize($limit);
		}
		$srch->addOrder('service_display_order','asc');
		$rs = $srch->getResultSet();
		if($extraFields)
		{
			$records = FatApp::getDb()->fetchAll($rs);
		}
		else{
			$records = FatApp::getDb()->fetchAllAssoc($rs);
		}		
		return $records;
	}
	
	public static function getCategoriesForForm($parent = 0){
		$srch = new SearchBase('tbl_services');
		$srch->addCondition('service_parent_id','=',$parent);
		$srch->addCondition('service_active','=',1);
		$srch->addFld('service_id');
		$srch->addFld('service_name');
		$srch->addOrder('service_name','asc');
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetchAllAssoc($rs);
		return $records;
	}
	
	public static function getParentCateogry($service_id = 0){
		$srch = new SearchBase('tbl_services');
		$srch->addCondition('service_id','=',$service_id);
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetch($rs);
		return $records['service_parent_id'];
	}
	
	function getFeaturedService(){
		$srch = $this->getSearchObject();
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$srch->addCondition(self::DB_TBL_PREFIX.'active','=', 1);
		$srch->addCondition(self::DB_TBL_PREFIX.'featured','=', 1);
		$srch->addOrder(self::DB_TBL_PREFIX.'display_order','asc');
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs, self::DB_TBL_PREFIX.'id');
		
	}
	
	/* function getCategoryForForm(){
		$records = array();
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'active','=',1);
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rows = FatApp::getDb()->fetchAll($srch->getResultSet());
		if(empty($rows)) return $records;
			$records = $this->sortCategoriesForForm($rows);
		
		return $records;
		
	}
	
	private function sortCategoriesForForm( array $elements, $parentId = 0 ) {
		
        $branch = array();
		
        foreach ($elements as $element) {
            
			if ( $element[self::DB_TBL_PREFIX.'parent_id'] == $parentId ) {
                $children = $this->sortCategoriesForForm( $elements, $element[self::DB_TBL_PREFIX.'id'] );
                if ($children) {
                    $element['options'] = $children;
					
                }
			//	$branch[] = $element;
				 if($element[self::DB_TBL_PREFIX.'parent_id'] == 0){
					//$branch[]['group_caption'] =$element[self::DB_TBL_PREFIX.'name'];
					$branch[] = array(
									'group_caption' => $element[self::DB_TBL_PREFIX.'name'],
									'options' => $children,
									);
					
					
				}
				else{
					$branch[$element[self::DB_TBL_PREFIX.'id']] =$element[self::DB_TBL_PREFIX.'name'];
				} 
               
            }
			
        }
		
        return $branch;
    } */
	
	
	
}?>