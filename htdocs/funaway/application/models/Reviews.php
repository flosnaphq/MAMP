<?php

class Reviews extends MyAppModel {

    const DB_TBL = 'tbl_reviews';
    const DB_TBL_PREFIX = 'review_';
    const ACTIVITY_REVIEW_TYPE = 0;

    public function __construct($review_id = 0) {
        $review_id = FatUtility::convertToType($review_id, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $review_id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject($joinCountReplies = false) {
        $srch = new SearchBase(static::DB_TBL);
		if($joinCountReplies){
            $srch->joinTable(ReviewMessage::DB_TBL, 'left join', ReviewMessage::DB_TBL_PREFIX . 'review_id = ' . self::DB_TBL_PREFIX . 'id');
            
            $srch->addGroupBy(static::DB_TBL_PREFIX.'id');
		}
        
        return $srch;
    }

    function getReviewWithUser($review_id) {
        $srch = self::getSearchObject();
        $srch->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . self::DB_TBL_PREFIX . 'user_id');
        $srch->addCondition(self::DB_TBL_PREFIX . 'id', '=', $review_id);
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', '=', 1);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    function getUserReview($user_id, $type_id, $type = 0) {
        $user_id = FatUtility::int($user_id);
        $type_id = FatUtility::int($type_id);
        $type = FatUtility::int($type);
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'user_id', '=', $user_id);
        $srch->addCondition(self::DB_TBL_PREFIX . 'type_id', '=', $type_id);
        $srch->addCondition(self::DB_TBL_PREFIX . 'type', '=', $type);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    function isUserReviewExist($user_id, $type_id, $type = 0) {
        $row = $this->getUserReview($user_id, $type_id, $type);
        return !empty($row);
    }

    function saveReview($data = array()) {
        if (empty($data))
            return false;
        $user_id = $data[self::DB_TBL_PREFIX . 'user_id'];
        $type_id = $data[self::DB_TBL_PREFIX . 'type_id'];
        $type = $data[self::DB_TBL_PREFIX . 'type'];
        $tbl = new TableRecord(self::DB_TBL);
        $tbl->assignValues($data);
        if ($this->isUserReviewExist($user_id, $type_id, $type)) {
            return $tbl->update(array('smt' => self::DB_TBL_PREFIX . 'user_id = ? and ' . self::DB_TBL_PREFIX . 'type_id = ? and ' . self::DB_TBL_PREFIX . 'type = ?', 'vals' => array($user_id, $type_id, $type)));
        }
        return $tbl->addNew();
    }

    static function canReviewByUser($user_id, $type_id, $type = 0) {
        $user_id = FatUtility::int($user_id);
        $type_id = FatUtility::int($type_id);
        $type = FatUtility::int($type);
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'user_id', '=', $user_id);
        $srch->addCondition(self::DB_TBL_PREFIX . 'type_id', '=', $type_id);
        $srch->addCondition(self::DB_TBL_PREFIX . 'type', '=', $type);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (!empty($row) && $row[self::DB_TBL_PREFIX . 'active'] == 3) {
            return false;
        }
        if (!empty($row)) {
            return true;
        }
        $act = new Activity($type_id);
        $activity_user_id = $act->getAttributesById($type_id, Activity::DB_TBL_PREFIX . 'user_id');
        if ($activity_user_id == $user_id) {
            return false;
        }

        $srch = new SearchBase(Order::ORDER_TBL);
        $srch->joinTable('tbl_order_activities', 'inner join', 'oactivity_order_id = order_id');
        $srch->joinTable(Activity::DB_TBL, 'inner join', 'oactivity_activity_id = activity_id and activity_user_id = ' . $activity_user_id);
        $srch->addCondition('order_user_id', '=', $user_id);
        $srch->addCondition('order_payment_status', '=', 1);
        $srch->addFld('order_id');
        $srch->setPageSize(1);
        $srch->setPageNumber(1);
        $srch->addOrder('order_id', 'desc');
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row)) {
            return false;
        }

        return true;
    }

    function getActivityRating($activity_id) {
        $activity_id = FatUtility::int($activity_id);
        $src = new SearchBase('tbl_reviews');
        $src->addCondition('review_type_id', '=', $activity_id);
        $src->addCondition('review_type', '=', 0);
        $src->addCondition('review_active', '=', 1);
        $src->addMultipleFields(array(
            'round(sum(review_rating)/count(review_rating),2) as rating',
            'sum(review_rating) as total_rating',
            'count(review_rating) as total_count'
        ));
        $rs = $src->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return $row;
    }

    static function getHostActivityRating($host_id) {
        $host_id = FatUtility::int($host_id);
        $src = new SearchBase('tbl_reviews');
        $src->joinTable(Activity::DB_TBL, 'Inner join', Activity::DB_TBL_PREFIX . 'id = review_type_id and ' . Activity::DB_TBL_PREFIX . 'user_id = ' . $host_id);
        $src->addCondition('review_type', '=', 0);
        $src->addCondition('review_active', '=', 1);
        $src->addMultipleFields(array(
            'round(sum(review_rating)/count(review_rating),2) as rating',
            'sum(review_rating) as total_rating',
            'count(review_rating) as total_count'
        ));
        $rs = $src->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return $row;
    }

    function getAbuseByReviewId($review_id) {
        $src = new SearchBase('tbl_abuse_report');
        $src->addCondition('abreport_record_id', '=', $review_id);
        $src->addCondition('abreport_record_type', '=', 1);
        return FatApp::getDb()->fetch($src->getResultSet());
    }

    function deleteAbuseByReviewId($review_id) {
        $db = FatApp::getDb();
        return $db->deleteRecords('tbl_abuse_report', array('smt' => 'abreport_record_id = ? and abreport_record_type = ?', 'vals' => array($review_id, 1)));
    }

    static function sendReviewNotificationToHost($activity_id, $traveler_name, $review_content, $rating) {
        $usr = new User();
        $activity = new Activity($activity_id);
        $activity->loadFromDb();
        $activity_data = $activity->getFlds();
        $activity_name = @$activity_data[Activity::DB_TBL_PREFIX . 'name'];
        $host_id = $activity_data[Activity::DB_TBL_PREFIX . 'user_id'];
        $host_data = $usr->getUserByUserId($host_id);
        $host_email = $host_data[User::DB_TBL_PREFIX . 'email'];
        $host_name = @$host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . @$host_data[User::DB_TBL_PREFIX . 'lastname'];
        $replace_vars = array(
            '{hostname}' => $host_name,
            '{traveler_name}' => $traveler_name,
            '{activity_name}' => $activity_name,
            '{review_content}' => $review_content,
            '{rating}' => $rating,
            '{review_url}' => FatUtility::generateFullUrl('activity', 'detail', array($activity_id), '/'),
        );
        Email::sendMail($host_email, 12, $replace_vars);
        $notify = new Notification();
        $notify->notify($host_id, 0, FatUtility::generateFullUrl('activity', 'detail', array($activity_id), '/'), Info::t_lang('NEW_REVIEW_ADDED'));
    }

    static function getAdminReport() {
        $current_month = Info::currentMonth();
        $current_year = Info::currentYear();
        $srch = new SearchBase('tbl_reviews');
        $srch->addDirectCondition("month(review_date) = '" . $current_month . "'");
        $srch->addDirectCondition("year(review_date) = '" . $current_year . "'");
        $srch->addFld('count(*) as total_count');
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return FatUtility::int($row['total_count']);
    }

}
