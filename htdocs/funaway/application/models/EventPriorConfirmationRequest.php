<?php

class EventPriorConfirmationRequest extends MyAppModel
{
	const DB_TBL = 'tbl_event_requests';
	const DB_TBL_PREFIX = 'requestevent_';
	const STATUS_PENDING = 0;
	const STATUS_CONFIRMED = 1;
	const STATUS_CANCELLED = 2;
	const STATUS_DELETED = 3;  // In case host does not respond to request within 24hrs request status automatically changed
	
	
	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		//$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public function updatePendingRequests() 
	{
		$previousDate = date('Y-m-d', strtotime(' -1 day'));
		$data = array(
            'requestevent_status' => self::STATUS_DELETED 
        );
        $whr = array('smt' => 'requestevent_status = ? AND requestevent_date <= ?', 'vals' => array(self::STATUS_PENDING, $previousDate));
  
        FatApp::getDb()->updateFromArray(self::DB_TBL, $data, $whr);
	}
	
	public function getRequests($status='', $endDate='') 
	{
		$srch = new SearchBase(static::DB_TBL);
		$srch->addMultipleFields(array( 'activity_name','activity_user_id', 'requestevent_requested_by', 'user_firstname', 'user_email', 'requestevent_content'));
		$srch->joinTable(User::DB_TBL, 'inner join', 'requestevent_requested_by = user_id');$srch->joinTable(Activity::DB_TBL, 'inner join', 'requestevent_activity_id = activity_id');
		
		if('' != $status || self::STATUS_PENDING == $status) {
			$srch->addCondition("requestevent_status", "=", $status);
		}
		if('' != $endDate) {
			$srch->addCondition("requestevent_date", "<=", $endDate);
		}
		$rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs);
	}
    
}
