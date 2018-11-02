<?php
class Restaurants{
	const CUISINES_TBL ='tbl_cuisines';
	const CUISINES_LANG_TBL ='tbl_cuisines_lang';
	const REST_TBL ='tbl_restaurants';
	const REST_LANG_TBL ='tbl_restaurants_lang';
	const USER_TBL ='tbl_users';
	const USER_DETAIL_TBL ='tbl_user_details';
	const REGION_TBL ='tbl_regions';
	const REGION_LANG_TBL ='tbl_regions_lang';
	const CITY_LANG_TBL ='tbl_cities_lang';
	
	private $error;
	
	public function getSearch($page=1,$pagesize=10){
		$page = FatUtility::int($page);
		$pagesize = FatUtility::int($pagesize);
		$search = new SearchBase(static::REST_TBL);
		$search->joinTable(static::REST_LANG_TBL, 'LEFT JOIN','restaurantlang_restaurant_id=restaurant_id and 	restaurantlang_lang_id = '.Info::defaultLang());
		$search->joinTable(static::USER_DETAIL_TBL, 'LEFT JOIN','udetails_user_id=restaurant_user_id');
		$search->joinTable(static::REGION_TBL, 'LEFT JOIN','region_id=restaurant_region_id');
		$search->joinTable(static::CITY_LANG_TBL, 'LEFT JOIN','citylang_city_id=region_city_id and citylang_lang_id = '.Info::defaultLang());
		$search->joinTable(static::REGION_LANG_TBL, 'LEFT JOIN','regionlang_region_id=restaurant_region_id and regionlang_lang_id = '.Info::defaultLang());
		$search->addMultipleFields(array(static::REST_TBL.'.*',static::REST_LANG_TBL.'.*', "concat(udetails_first_name,' ', udetails_last_name) as user_name",static::REGION_LANG_TBL.'.region_name',static::CITY_LANG_TBL.'.city_name'));
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$search->addOrder('restaurant_id','DESC');
		return $search;
	}
	
	function getRestaurantRegionsSearch(){
		
		$search = new SearchBase('tbl_restaurant_regions');
		$search->joinTable('tbl_regions','INNER JOIN','region_id = restregion_region_id');
		$search->joinTable('tbl_cities','INNER JOIN','city_id = region_city_id');
		$search->joinTable('tbl_cities_lang','INNER JOIN','citylang_city_id = region_city_id and citylang_lang_id = '.Info::defaultLang());
		$search->joinTable('tbl_regions_lang','INNER JOIN','regionlang_region_id = restregion_region_id and regionlang_lang_id = '.Info::defaultLang());
		$search->addCondition('region_active','=',1);
		$search->addCondition('city_active','=',1);
		
		$search->addMultipleFields(array('tbl_restaurant_regions.*','region_name','city_name'));
		
		return $search;
	}
	
	function getRestaurant($restaurant_id){
		$record = array();
		$restaurant_id = FatUtility::int($restaurant_id);
		$search = new SearchBase(static::REST_TBL);
		$search->joinTable(static::USER_DETAIL_TBL, 'LEFT JOIN','udetails_user_id=restaurant_user_id');
		$search->joinTable(static::REGION_TBL, 'LEFT JOIN','region_id=restaurant_region_id');
		$search->joinTable(static::CITY_LANG_TBL, 'LEFT JOIN','citylang_city_id=region_city_id and citylang_lang_id = '.Info::defaultLang());
		$search->joinTable(static::REGION_LANG_TBL, 'LEFT JOIN','regionlang_region_id=restaurant_region_id and regionlang_lang_id = '.Info::defaultLang());
		$search->addCondition('restaurant_id','=',$restaurant_id);
		$search->addMultipleFields(array(static::REST_TBL.'.*', "concat(udetails_first_name,' ', udetails_last_name) as user_name",static::REGION_LANG_TBL.'.region_name',static::CITY_LANG_TBL.'.city_name'));
		
		$record = FatApp::getDb()->fetch($search->getResultSet());
		$lang_record = MyHelper::getLangFields($restaurant_id,"restaurantlang_restaurant_id","restaurantlang_lang_id",array("restaurant_name",'restaurant_description','restaurant_address'),"tbl_restaurants_lang");
		if(empty($lang_record)) $lang_record =array();
		if(empty($record)) $record =array();
		$arr = array_merge($record,$lang_record);
		return $arr;
		
	}
	
	function getCuisines(){
		$srch = new SearchBase(static::CUISINES_TBL,'c');		
		$srch->joinTable(static::CUISINES_LANG_TBL,"LEFT OUTER JOIN","c.cuisines_id=cl.cuisineslang_cuisines_id and cuisineslang_lang_id = ".Info::defaultLang(),"cl");	
		$srch->addOrder('cuisines_name');
		return $srch;
	}
	
	function getCuisine($id){
		$record = array();
		$srch = new SearchBase(static::CUISINES_TBL);
		$srch->addCondition('cuisines_id','=',$id);
		$rs = $srch->getResultSet();
		$db = new Database();
		$record = $db->fetch($rs);
		$lang_record = MyHelper::getLangFields($id,"cuisineslang_cuisines_id","cuisineslang_lang_id",array("cuisines_name"),static::CUISINES_LANG_TBL);
		$arr = array_merge($record,$lang_record);
		return $arr;		
	}
	
	function saveCuisine($data,$cuisines_id=0){
		$cuisines_id = FatUtility::int($cuisines_id);
		$tbl = new TableRecord(static::CUISINES_TBL);
		$tbl->assignValues($data);
		
		if(!empty($cuisines_id)){
			$where =array('smt'=>'cuisines_id = ?','vals'=>array($cuisines_id));
			if(!$tbl->update($where)){
				$this->error = 'Something went wrong.';
				return false;
			}
			return $cuisines_id;
		}
		if(!$tbl->addNew()){
			$this->error = 'Something went wrong.';
			return false;
		}
		return $tbl->getId();
	}
	
	function saveCuisineLang($data){
		$cuisines_id = FatUtility::int($data['cuisineslang_cuisines_id']);
		$lang_id = FatUtility::int($data['cuisineslang_lang_id']);
		$tbl = new TableRecord(static::CUISINES_LANG_TBL);
		$tbl->assignValues($data);
		if($this->isLangDetailExistCusine($cuisines_id,$lang_id)){
			if(!$tbl->update(array('smt'=>'cuisineslang_lang_id = ? and cuisineslang_cuisines_id = ?','vals'=>array($lang_id,$cuisines_id)))){
				$this->error = 'Something went wrong.';
				return false;
			}
			return true;
		}
		if(!$tbl->addNew()){
			$this->error = 'Something went wrong.';
			return false;
		}
		return true;
		
	}
	
	
	function isLangDetailExistCusine($cus_id, $lang_id){
		$srch = new SearchBase(static::CUISINES_LANG_TBL);
		$srch->addCondition("cuisineslang_cuisines_id","=",$cus_id);
		$srch->addCondition("cuisineslang_lang_id","=",$lang_id);
		$rs = $srch->getResultSet();
		$db = new Database();
		$record = $db->fetch($rs);
		if(!empty($record)) 
			return true;
		return false;		
	}	

	function getRestaurantForForm(){
		$srch = new SearchBase(static::REST_LANG_TBL);
		$srch->addCondition('restaurantlang_lang_id','=',Info::defaultLang());
		$srch->addMultipleFields(array('restaurantlang_restaurant_id','restaurant_name'));
		$srch->addOrder('restaurant_name');
		return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
	}
	
	
	
	public function getError(){
		return $this->error;
	}
}

?>