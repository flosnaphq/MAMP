<?php
class OrderCancel extends MyAppModel {
	const DB_TBL = 'tbl_order_cancel';
	const DB_TBL_PREFIX = 'ordercancel_';
	const HOST_APPROVED_TYPE_PENDING = 0;
	const HOST_APPROVED_TYPE_APPROVED = 1;
	const HOST_APPROVED_TYPE_CANCELLED = 2;
	const STATUS_PENDING = 0;
	const STATUS_APPROVED = 1;
	const STATUS_CANCELLED = 2;

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->joinTable(Order::ORDER_EVENT_TBL,'INNER JOIN','	oactivity_booking_id = 	ordercancel_booking_id');
		$srch->joinTable(Order::ORDER_TBL,'INNER JOIN','oactivity_order_id = order_id');
		
		return $srch;
	}
	
	function addCancelBooking($data, $user_id, $comment){
		$booking_id = @$data[self::DB_TBL_PREFIX.'booking_id'];
		if($this->isExistCancelBooking($booking_id)){
			return false;
		}
		$data[self::DB_TBL_PREFIX.'datetime'] = Info::currentDatetime();
		$this->assignValues($data);
		if(!$this->save()){
			return false;
		}
		$cmnt = new Comments();
		$comment_data[Comments::DB_TBL_PREFIX.'entity_type'] = Comments::ENTITY_TYPE_ORDER_CANCEL;
		$comment_data[Comments::DB_TBL_PREFIX.'entity_id'] = $this->mainTableRecordId;
		$comment_data[Comments::DB_TBL_PREFIX.'user_id'] = $data[self::DB_TBL_PREFIX.'user_id'];
		$comment_data[Comments::DB_TBL_PREFIX.'comment'] = $comment;
		$comment_data[Comments::DB_TBL_PREFIX.'datetime'] = Info::currentDatetime();
		$cmnt->assignValues($comment_data);
		if(!$cmnt->save()){
			return false;
		}
		return true;
	}
	
	function isExistCancelBooking($booking_id){
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'booking_id','=', $booking_id);
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		$rows = FatApp::getDb()->fetch($rs);
		return (!empty($rows));
	}
	
	function getCancelBooking($value,$fieldName){
		$srch = self::getSearchObject();
		$srch->addCondition($fieldName,'=', $value);
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		
		$rows = FatApp::getDb()->fetch($rs);
		return $rows;
	}
	
	
}