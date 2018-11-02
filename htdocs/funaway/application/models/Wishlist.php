<?php 
class Wishlist extends MyAppModel {
	const DB_TBL = 'tbl_wishlist';
	const DB_TBL_PREFIX = 'wishlist_';

	public function __construct($wishlistId = 0) {
		$cmsId = FatUtility::convertToType($wishlistId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $wishlistId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
		$srch->addOrder(static::DB_TBL_PREFIX . 'name');
		return $srch;
	}
	
	
	function wishlistAction($activity_id,$user_id){
		$user_id = intval($user_id);
		$activity_id = intval($activity_id);
		$type = "";
		$db = FatApp::getDb();
		if(Wishlist::isAlreadyWished($activity_id,$user_id)){
			$db->deleteRecords("tbl_wishlist",array("smt"=>"wishlist_user_id = ? and wishlist_activity_id = ?","vals"=>array($user_id,$activity_id)));
			$type = "delete";
		}else{
			$data['wishlist_activity_id'] = $activity_id;
			$data['wishlist_user_id'] = $user_id;
			$data['wishlist_date'] = Info::currentDatetime();
			$db->insertFromArray('tbl_wishlist', $data);
			$type = "add";
		}
		return $type;
	}

	function isAlreadyWished($activity_id,$user_id){
		$user_id = intval($user_id);
		$activity_id = intval($activity_id);
		$db = FatApp::getDb();
		$record = array();
		$srch = new SearchBase('tbl_wishlist');
		$srch->addCondition("wishlist_user_id","=",$user_id);
		$srch->addCondition("wishlist_activity_id","=",$activity_id);
		$rs = $srch->getResultSet();
		$record  = $db->fetch($rs);
		if(!empty($record)){
			return true;
		}
		return false;
	}

	function deleteActivity($user_id, $activity_id){
		$activity_id = FatUtility::int($activity_id);
		$user_id = FatUtility::int($user_id);
		if (!FatApp::getDb()->deleteRecords(static::DB_TBL, array('smt'=>static::DB_TBL_PREFIX.'user_id = ? and '.static::DB_TBL_PREFIX.'activity_id = ?', 'vals'=>array($user_id, $activity_id)))) {
			return false;
		}
		return true;
	}
}