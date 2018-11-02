<?php

class Thread extends MyAppModel {

    const DB_TBL = 'tbl_messages_thread';
    const DB_TBL_PREFIX = 'messagethread_';

    public function __construct($messagethreadId = 0) {
        $messagethreadId = FatUtility::convertToType($messagethreadId, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $messagethreadId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public function checkIfThreadAlreadyExists($user_id, $other_user_id, $activity_id) {
        $srch = self::getSearchObject();
        $srch->addDirectCondition('( ' . self::DB_TBL_PREFIX . 'first_user_id = ' . $user_id . ' or ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $user_id . ')');
        $srch->addDirectCondition('( ' . self::DB_TBL_PREFIX . 'first_user_id = ' . $other_user_id . ' or ' . self::DB_TBL_PREFIX . 'second_user_id = ' . $other_user_id . ')');
        $srch->addCondition('messagethread_activity_id', '=', $activity_id);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return isset($row['messagethread_id']) ? $row['messagethread_id'] : 0;
    }

    public function createThread($user_id, $other_user_id, $activity_id) {

        if ($thread_id = $this->checkIfThreadAlreadyExists($user_id, $other_user_id, $activity_id)) {
            $this->mainTableRecordId = $thread_id;
            return true;
        }

        $thread = array(
            'first_user_id' => $user_id,
            'second_user_id' => $other_user_id,
            'messagethread_activity_id' => $activity_id,
        );

        $this->assignValues($thread);
        if (!$this->save()) {
            return false;
        }

        return true;
    }

}
