<?php

class Regions extends MyAppModel {

    const DB_TBL = 'tbl_regions';
    const DB_TBL_PREFIX = 'region_';

    public function __construct($countryId = 0)
	{
        $regionId = FatUtility::convertToType($countryId, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $regionId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject()
	{
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        return $srch;
    }

    public static function getRegions()
	{
        $srch = new SearchBase(static::DB_TBL);
        $srch->addFld(static::DB_TBL_PREFIX."id");
        $srch->addFld(static::DB_TBL_PREFIX."name");
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }

	public function updateOrder($order)
	{
		foreach($order as $i => $regionId) {
			$i = FatUtility::int($i);
			$regionId = FatUtility::int($regionId);
			if($regionId < 1) continue;
			
			$result = FatApp::getDb()->updateFromArray(
									static::DB_TBL, 
									array(static::DB_TBL_PREFIX . 'display_order' => $i),
									array(
										'smt' => static::DB_TBL_PREFIX . 'id = ? ', 
										'vals' => array($regionId)
									)
								);
			if(!$result){
				return false;
			}
		}
		return true;
	}
	
	public static function isActivityAssigned($regionId)
	{
		$srch = new SearchBase(Activity::DB_TBL);
		$srch->joinTable('tbl_cities', 'Inner Join', 'city_id = activity_city_id');
		$srch->joinTable('tbl_countries', 'Inner Join', 'country_id = city_country_id');
		$srch->joinTable(static::DB_TBL, 'Inner Join', 'region_id = country_region_id');
		$srch->addCondition('region_id', '=', $regionId);
		$rs = $srch->getResultSet();
		if($srch->recordCount() > 0)
		{
			return true;
		}
		return false;
	}

}
