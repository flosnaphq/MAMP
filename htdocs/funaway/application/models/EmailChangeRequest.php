<?php
class EmailChangeRequest extends MyAppModel {
	const DB_TBL = 'tbl_email_change_requests';
	const DB_TBL_PREFIX = 'ecr_';

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	function deleteOldRequest($user_id){
		$user_id = FatUtility::int($user_id);
		$timestamp = Info::timestamp();
		$datetime = date('Y-m-d H:i:s',($timestamp-(60*60*24)));
		return FatApp::getDb()->deleteRecords(self::DB_TBL,array('smt'=>self::DB_TBL_PREFIX.'user_id = ? and '.self::DB_TBL_PREFIX.'expiry <= ?','vals'=>array($user_id, $datetime)));
	}
	
	function deleteRequest($user_id){
		$user_id = FatUtility::int($user_id);
		return FatApp::getDb()->deleteRecords(self::DB_TBL,array('smt'=>self::DB_TBL_PREFIX.'user_id = ?','vals'=>array($user_id)));
	}
	
	function getToken($user_id){
		$user_id = FatUtility::int($user_id);
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'user_id','=',$user_id);
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetch($rs);
	}
	
	function getValidToken(){
		$token = User::encryptPassword(FatUtility::getRandomString(15));
		while($this->isExistToken($token)){
			$token = User::encryptPassword(FatUtility::getRandomString(15));
		}
		return $token;
	}
	
	function isExistToken($token){
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'verification_code','=',$token);
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		return !empty($row);
	}
}