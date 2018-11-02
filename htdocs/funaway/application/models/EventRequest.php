<?php
class EventRequest extends FatModel {
	private $db;
	const REQUEST_PENDING_STATUS=0;
	const REQUEST_APPROVED_STATUS=1;
	const REQUEST_CANCEL_STATUS=2;
	const EVENT_TBL ='tbl_event_requests';
	public function __construct() {
        parent::__construct();
		$this->db = FatApp::getDb();
	}
	
	
	public function addEventRequest($request = array()){
		$this->db->insertFromArray(EventRequest::EVENT_TBL,$request);
		Sms::requestConfirmationSmsToHost($this->db->getInsertId());
	}
	
	public function updateEventRequest($request = array()){
		return $this->db->updateFromArray(EventRequest::EVENT_TBL,$request,array('smt'=>'requestevent_id = ?','vals'=>array($request['requestevent_id'])));
	}
	
	public function validRequest($request_id,$user_id){
		$srch = new SearchBase(static::EVENT_TBL);
		$srch->joinTable('tbl_activities','inner join','activity_id = requestevent_activity_id and activity_user_id = '.$user_id);
		$srch->addCondition('requestevent_id','=',$request_id);
		$rs = $srch->getResultSet();
		$db = FatApp::getDb();
		$records = $db->fetch($rs);
		if($records) return true;
		return false;
	}
	
	public function validRequestByMerchant($request_id,$user_id){
		$srch = new SearchBase(static::EVENT_TBL);
		$srch->addCondition('requestevent_id','=',$request_id);
		$rs = $srch->getResultSet();
		$db = FatApp::getDb();
		$records = $db->fetch($rs);
		if($records) return true;
		return false;
	}
	
	public function getEventRequestByMerchant($user_id){
		$srch = new SearchBase(static::EVENT_TBL);
		$srch->joinTable('tbl_activities','inner join','activity_id = requestevent_activity_id');
		$srch->joinTable('tbl_activity_events','inner join','requestevent_event_id = activityevent_id');
		$srch->addCondition('requestevent_requested_by','=',$user_id);
		return $srch;
		
	}
	
	public function getEventRequestById($request_id){
		$srch = new SearchBase(static::EVENT_TBL);
		$srch->addCondition('requestevent_id','=',$request_id);
		$rs = $srch->getResultSet();
		$db = FatApp::getDb();
		$record = $db->fetch($rs);
		return $record;
		
	}
	
	public function getEventRequestByActivity($user_id){
		$srch = new SearchBase(static::EVENT_TBL);
		$srch->joinTable('tbl_activities','inner join','activity_id = requestevent_activity_id');
		$srch->joinTable('tbl_activity_events','inner join','requestevent_event_id = activityevent_id');
		$srch->joinTable('tbl_users','inner join','host.user_id = activity_user_id and host.user_id = '.$user_id,'host');
		$srch->joinTable('tbl_users','inner join','requestevent_requested_by = traveler.user_id','traveler');
		$srch->addCondition('host.user_id','=',$user_id);
		return $srch;
	}
	
	function markRequestAsCompleted($request_id){
		$request_id = FatUtility::int($request_id);
		if($request_id <= 0){
			return false;
		}
		$data['requestevent_is_order'] = 1;
		$data['requestevent_id'] = $request_id;
		return  $this->updateEventRequest($data);
	}
	
}
?>
