<?php

class ReviewMessage extends MyAppModel {

    const DB_TBL = 'tbl_review_messages';
    const DB_TBL_PREFIX = 'reviewmsg_';
    const REVIEWMSG_USERTYPE_ADMIN = 0;
    const REVIEWMSG_USERTYPE_HOST = 1;

    public function __construct($reviewmsg_id = 0) {
        $reviewmsg_id = FatUtility::convertToType($reviewmsg_id, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $reviewmsg_id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject($joinReviews = false,$joinUser = false,$joinAdmin = false) {
        $srch = new SearchBase(static::DB_TBL);
		if($joinReviews){
			$srch->joinTable(Review::DB_TBL, 'left join', Review::DB_TBL_PREFIX . 'id = ' . self::DB_TBL_PREFIX . 'review_id');
		}
        if($joinUser){
			$srch->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . self::DB_TBL_PREFIX . 'user_id and ' . User::DB_TBL_PREFIX . 'type = 1 and ' . self::DB_TBL_PREFIX . 'user_type = '.self::REVIEWMSG_USERTYPE_HOST);
		}
        if($joinAdmin){
			$srch->joinTable(Admin::DB_TBL, 'left join', Admin::DB_TBL_PREFIX . 'id = ' . self::DB_TBL_PREFIX . 'user_id and ' . self::DB_TBL_PREFIX . 'user_type = '.self::REVIEWMSG_USERTYPE_ADMIN);
		}
        return $srch;
    }

}
