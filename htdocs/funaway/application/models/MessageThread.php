<?php

class MessageThread extends MyAppModel {

    const DB_TBL = 'tbl_messages_thread';
    const DB_TBL_PREFIX = 'messagethread_';

    public function __construct($messageId = 0) {
        $messageId = FatUtility::convertToType($messageId, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $messageId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    static function canMessage($traveler_id, $host_id) {

        $traveler_id = FatUtility::int($traveler_id);
        $host_id = FatUtility::int($host_id);
        if ($traveler_id <= 0 || $host_id <= 0) {
            return false;
        }
        $srch = self::getSearchObject();
        $srch->addDirectCondition('(' . self::DB_TBL_PREFIX . 'first_user_id = ' . $traveler_id . ' and ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $host_id . ') or (' . self::DB_TBL_PREFIX . 'first_user_id = ' . $host_id . ' and ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $traveler_id . ')');
        $srch->addFld('count(' . self::DB_TBL_PREFIX . 'id) as total_thread');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();

        $row = FatApp::getDb()->fetch($rs);
        if ($row['total_thread'] > 0) {
            return true;
        }
        $srch = new SearchBase(Order::ORDER_TBL);
        $srch->joinTable('tbl_order_activities', 'inner join', 'oactivity_order_id = order_id');
        $srch->joinTable(Activity::DB_TBL, 'inner join', 'oactivity_activity_id = activity_id and activity_user_id = ' . $host_id);
        $srch->addCondition('order_user_id', '=', $traveler_id);
        $srch->addCondition('order_payment_status', '=', 1);
        $srch->setPageSize(1);
        $srch->setPageNumber(1);
        $srch->addFld('order_id');
        $srch->addOrder('order_id', 'desc');
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);

        if (!empty($row)) {
            return true;
        }
        return false;
    }

    static function countUnreadMessage($user_id) {
        $user_id = FatUtility::int($user_id);
        $srch = self::getSearchObject();
        $srch->addDirectCondition('( ' . self::DB_TBL_PREFIX . 'first_user_id = ' . $user_id . ' or ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $user_id . ')');
        $srch->joinTable(Chats::DB_TBL, 'inner Join', self::DB_TBL_PREFIX . 'id=' . Chats::DB_TBL_PREFIX . 'thread_id and ' . Chats::DB_TBL_PREFIX . 'user_id != ' . $user_id . ' and ' . Chats::DB_TBL_PREFIX . 'seen = 0');
        $srch->addFld('count(*) as unread_message');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();

        $row = FatApp::getDb()->fetch($rs);
        return $row['unread_message'];
    }

    public function checkIfThreadAlreadyExists($user_id, $other_user_id, $activity_id) {
        $srch = self::getSearchObject();
        $srch->addDirectCondition('( ' . self::DB_TBL_PREFIX . 'first_user_id = ' . $user_id . ' or ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $user_id . ')');
        $srch->addDirectCondition('( ' . self::DB_TBL_PREFIX . 'first_user_id = ' . $other_user_id . ' or ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $other_user_id . ')');
        $srch->addCondition('messagethread_activity_id', '=', $activity_id);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return isset($row['messagethread_id'])?$row['messagethread_id']:0;
    }

    public function createThread($user_id, $other_user_id, $activity_id) {

        if($thread_id = $this->checkIfThreadAlreadyExists($user_id, $other_user_id, $activity_id)){
            $this->mainTableRecordId = $thread_id;
            return true;
        }
        
        $thread = array(
            self::DB_TBL_PREFIX . 'first_user_id'=>$user_id,
            self::DB_TBL_PREFIX . 'second_user_id'=>$other_user_id,
            self::DB_TBL_PREFIX . 'activity_id'=>$activity_id,
            
        );
     
         $this->assignValues($thread);
         if(!$this->save()){
             return false;
         }
        
         return true;
    }

}
