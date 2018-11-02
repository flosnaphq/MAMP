<?php

class Country extends MyAppModel {

    const DB_TBL = 'tbl_countries';
    const DB_TBL_PREFIX = 'country_';

    public function __construct($countryId = 0) {
        $countryId = FatUtility::convertToType($countryId, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $countryId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
		$srch->joinTable(Region::DB_TBL, 'INNER JOIN', Region::DB_TBL_PREFIX . "id=" . self::DB_TBL_PREFIX . "region_id");
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        return $srch;
    }

    public static function getCountries() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        $srch->addFld("country_id");
        $srch->addFld("country_name");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }


    public static function getCountriesPhoneCode() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
		$srch->addCondition(static::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addFld("country_id");
        $srch->addFld("concat('+', country_phone_code)");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }

	public static function getAllCountryByRegionId($id) {
        $srch = self::getSearchObject();
        $srch->addCondition(Region::DB_TBL_PREFIX . "id", '=', $id);
        $srch->addCondition(static::DB_TBL_PREFIX . "active", '=', 1);
        $srch->addCondition(Region::DB_TBL_PREFIX . "active", '=', 1);
        $srch->addFld(static::DB_TBL_PREFIX . "id");
        $srch->addFld(static::DB_TBL_PREFIX . "name");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }
	
	public static function getCities($country_id = 0,$limit=-1) {
		$srch = new SearchBase('tbl_cities');
		$srch->addCondition('city_country_id','=',  $country_id);
		$srch->addCondition('city_active','=',1);
		$srch->addFld('city_id');
		$srch->addFld('city_name');
		if($limit > -1){
			$srch->setPageSize($limit);
		}
		$srch->addOrder('city_display_order','asc');
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetchAllAssoc($rs);
		return $records;
	}
	
	public static function getPhoneCodes($assoc = true, $countryId = 0, $isActive = true, $codePrefix = '+')
	{
        $srch = new SearchBase(static::DB_TBL);
		
		if (true === $isActive) {
			$srch->addCondition(static::DB_TBL_PREFIX . 'active', '=', 1);
		}
		
		if ($countryId > 0) {
            $srch->addCondition(static::tblFld('id'), '=', FatUtility::int($countryId));
        }
		
		$rs = $srch->getResultSet();
		
		if($srch->recordCount() < 1) {
			return false;
		}
		
		if ($countryId > 0) {
			$srch->setPageSize(1);
			$srch->doNotCalculateRecords();
			$srch->addFld("concat('$codePrefix', country_phone_code) as phone_code");
			$rs = $srch->getResultSet();
			$row = FatApp::getDb()->fetch($rs);
			return $row['phone_code'];
		}
		
		$srch->addFld("country_id");
		$srch->addFld("concat('$codePrefix', country_phone_code) as phone_code");
        
		$srch->addOrder(static::DB_TBL_PREFIX . 'phone_code');
		
		
		// echo $srch->getQuery();exit;
		// $rs = $srch->getResultSet();
		$rs = $srch->getResultSet();
		if ($assoc) {
            return FatApp::getDb()->fetchAllAssoc($rs, "country_id");
        } else {
            return FatApp::getDb()->fetchAll($rs, static::tblFld('id'));
        }
    }
    
}