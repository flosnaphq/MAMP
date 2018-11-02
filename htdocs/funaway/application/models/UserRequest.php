<?php

class UserRequest extends MyAppModel {

    const DB_TBL = 'tbl_user_city_requests';
    const DB_TBL_PREFIX = 'ucrequest_';

    public function __construct($serviceId = 0) {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $serviceId);
    }

    public static function getSearchObject() {

        $srch = new SearchBase(static::DB_TBL);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'user_id=' . self::DB_TBL_PREFIX . 'user_id');
        $srch->joinTable(Country::DB_TBL, 'INNER JOIN', 'country_id=' . self::DB_TBL_PREFIX . 'country_id');
        return $srch;
    }

    public static function getUserRequestDataById($requestId) {
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX."id",'=',$requestId);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }
	
	public static function getUnreadCount() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addCondition(self::DB_TBL_PREFIX.'status','=',0);
		$srch->addFld('count(*) as total_count');
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		return $row['total_count'];
	}

}
