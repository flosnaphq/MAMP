<?php
class Chats extends MyAppModel {
	const DB_TBL = 'tbl_messages';
	const DB_TBL_PREFIX = 'message_';

	public function __construct($messageId = 0) {
		$messageId = FatUtility::convertToType($messageId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $messageId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL,"msg1");
		return $srch;
	}
	
	public static function markAsRead($thread_id, $user_id){
		$tbl = new TableRecord(static:: DB_TBL);
		$data[static::DB_TBL_PREFIX.'seen'] = 1;
		$tbl->assignValues($data);
		return $tbl->update(array('smt'=>static::DB_TBL_PREFIX.'thread_id = ? and '.static::DB_TBL_PREFIX.'user_id != ?','vals'=>array($thread_id, $user_id)));
	}
	
	
}