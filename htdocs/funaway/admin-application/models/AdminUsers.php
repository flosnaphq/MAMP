<?php

class AdminUsers extends MyAppModel {

    const DB_TBL = 'tbl_admin';
    const DB_TBL_PREFIX = 'admin_';
    const DB_TBL_ALIAS = 'adm';
    const DB_ROLE_TBL = 'tbl_admin_roles';
    const DB_ROLE_TBL_PREFIX = 'adminrole_';
    const DB_ROLE_TBL_ALIAS = 'adminrole';

    public function __construct($adminId = 0) {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $adminId);
        $this->objMainTableRecord->setSensitiveFields(array(''));
    }

    public function getUserSearchObj($roleJoin = false) {
        $srch = new SearchBase(SELF::DB_TBL, SELF::DB_TBL_ALIAS);
        if ($roleJoin) {
            $srch->joinTable(
                    SELF::DB_ROLE_TBL, 'LEFT OUTER JOIN', SELF::DB_TBL_ALIAS . '.' . SELF::DB_TBL_PREFIX . 'adminrole_id=' . SELF::DB_ROLE_TBL_ALIAS . '.' . SELF::DB_ROLE_TBL_PREFIX . 'id', SELF::DB_ROLE_TBL_ALIAS
            );
        }

        return $srch;
    }

    public function addUpdateAdminUsers() {
        $roleObj = new Roles($roleId);
        $roleObj->assignValues($post);
        if ($roleObj->save()) {

            if ($roleId > 0)
                Message::addMessage(CommonHelper::getLabel('FRM_SUCCESS_ROLE_ADDED'));
            else
                Message::addMessage(CommonHelper::getLabel('FRM_SUCCESS_ROLE_UPDATED'));

            FatUtility::dieJsonSuccess(Message::getHtml());
        }else {
            Message::addErrorMessage($roleObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
    }

}
