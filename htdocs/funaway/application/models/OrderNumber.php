<?php
class OrderNumber extends MyAppModel {
	const DB_TBL = 'tbl_order_numbers';
	const DB_TBL_PREFIX = 'ordernumber_';

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject($calculateRecord=false,$limitRecord = false) {
		$srch = new SearchBase(static::DB_TBL);
		if(!$calculateRecord){
			$srch->doNotCalculateRecords();
			$srch->doNotLimitRecords();
		}
		return $srch;
	}
	
	static function getNewOrderNumber(){
		$data = array(
					self::DB_TBL_PREFIX.'datetime'=>Info::currentDatetime()
					);
		if(!FatApp::getDb()->insertFromArray(self::DB_TBL, $data)){
			return false;
		}
		$order_id = FatApp::getDb()->getInsertId();
		self::deleteOldOrderNumbers();
		return $order_id;
	}
	
	static function deleteOldOrderNumbers(){
		$current_time = Info::currentDatetime();
		$delete_time = date('Y-m-d 00:00:00',strtotime($current_time)-(60*60*24*30));
		
		return FatApp::getDb()->deleteRecords(self::DB_TBL,array(
										'smt' => self::DB_TBL_PREFIX.'datetime <= ?',
										'vals' => array(
														$delete_time
														)
										));
	}
}