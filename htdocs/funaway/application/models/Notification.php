<?php
class Notification extends MyAppModel {
	const DB_TBL = 'tbl_notifications';
	const DB_TBL_PREFIX = 'notification_';

	public function __construct($notificationId = 0) {
		$notificationId = FatUtility::convertToType($notificationId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $notificationId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	
	public function notify($user_id,$type,$url,$text){
		$data = array();
		$data[static::DB_TBL_PREFIX .'id'] = '';
		$data[static::DB_TBL_PREFIX .'user_id'] = $user_id;
		$data[static::DB_TBL_PREFIX .'type'] = $type;
		$data[static::DB_TBL_PREFIX .'url'] = $url;
		$data[static::DB_TBL_PREFIX .'content'] = $text;
		$data[static::DB_TBL_PREFIX .'is_read'] = 0;
		$data[static::DB_TBL_PREFIX .'date'] = Info::currentDatetime();
		
		$this->assignValues($data);
		$this->save();
		
		return;
	}
	
	public static function markAsRead($notificationIds=array()){
		if(empty($notificationIds)) return false;
		
		if(is_array($notificationIds)){
			$notificationIds = implode(', ', $notificationIds);
			return FatApp::getDb()->query('update '.static::DB_TBL.' set '.static::DB_TBL_PREFIX.'is_read = 1 where notification_id in ('.$notificationIds.')' );
		}
		$tbl = new TableRecord(static::DB_TBL);
		$data = array(static::DB_TBL_PREFIX.'is_read'=>1);
		$tbl->assignValues($data);
		return $tbl->update(array('smt'=>'notification_id = ?','vals'=>array($notificationIds)));
	}
	
	static function getUnreadCount($user_id, $type=0){
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'is_read','=',0);
		$srch->addCondition(self::DB_TBL_PREFIX.'user_id','=',$user_id);
		$srch->addFld('count(*) as total_count');
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		return $row['total_count'];
	}
	
}
