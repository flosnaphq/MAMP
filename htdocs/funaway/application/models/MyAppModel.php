<?php

class MyAppModel extends FatModel {

    /**
     * 
     * @var TableRecord
     */
    protected $objMainTableRecord;
    protected $mainTableIdField;
    protected $mainTableRecordId;
    protected $mainTableName;

    public function __construct($tblName, $keyFld, $id) {
        parent::__construct();
        $this->objMainTableRecord = new TableRecord($tblName);
        $this->mainTableIdField = $keyFld;
        $this->mainTableRecordId = FatUtility::convertToType($id, FatUtility::VAR_INT);
        $this->mainTableName = $tblName;
    }

    public static function tblFld($key) {
        return static::DB_TBL_PREFIX . $key;
    }

    public static function getAllNames($assoc = true, $recordId = 0, $activeFld = null, $deletedFld = null, $orderByFld = null, $orderBy = 'ASC') {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addMultipleFields(array(static::tblFld('id'), static::tblFld('name')));
        $srch->addOrder(static::tblFld('name'));
        if ($activeFld != null) {
            $srch->addCondition($activeFld, '=', 1);
        }
        if ($deletedFld != null) {
            $srch->addCondition($deletedFld, '=', 0);
        }

        if ($recordId > 0) {
            $srch->addCondition(static::tblFld('id'), '=', FatUtility::int($recordId));
        }

        if ($orderByFld != null) {
            $srch->addOrder($orderByFld, $orderBy);
        } else {
            $srch->addOrder(static::tblFld('name'));
        }

        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        // echo $srch->getQuery();
        if ($assoc) {
            return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        } else {
            return FatApp::getDb()->fetchAll($srch->getResultSet(), static::tblFld('id'));
        }
    }

    public function assignValues($arr, $handleDates = false, $mysql_date_format = '', $mysql_datetime_format = '', $execute_mysql_functions = false) {
        $this->objMainTableRecord->assignValues($arr, $handleDates, $mysql_date_format, $mysql_datetime_format, $execute_mysql_functions);
    }

    public function deleteRecord() {
        if (!FatApp::getDb()->deleteRecords($this->mainTableName, array('smt' => $this->mainTableIdField . ' = ?', 'vals' => array($this->mainTableRecordId)))) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    public function loadFromDb($prepare_dates_for_display = false) {
        $result = $this->objMainTableRecord->loadFromDb(array(
            'smt' => $this->mainTableIdField . " = ?",
            'vals' => array(
                $this->mainTableRecordId
            )
                ), $prepare_dates_for_display);
        if (!$result) {
            $this->error = $this->objMainTableRecord->getError();
        }

        return $result;
    }

    public static function getAttributesById($recordId, $attr = null) {
        $recordId = FatUtility::convertToType($recordId, FatUtility::VAR_INT);

        $db = FatApp::getDb();

        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::tblFld('id'), '=', $recordId);

        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }

        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);

        if (!is_array($row)) {
            return false;
        }

        if (is_string($attr)) {
            return $row[$attr];
        }

        return $row;
    }

    public function getFlds() {
        return $this->objMainTableRecord->getFlds();
    }

    public function getFldValue($key) {
        return $this->objMainTableRecord->getFldValue($key);
    }

    public function setFlds($arr) {
        $this->objMainTableRecord->setFlds($arr);
    }

    public function setFldValue($key, $val, $execute_mysql_function = false) {
        $this->objMainTableRecord->setFldValue($key, $val, $execute_mysql_function);
    }

    public function save() {
        if (0 < $this->mainTableRecordId) {
            $result = $this->objMainTableRecord->update(array('smt' => $this->mainTableIdField . ' = ?', 'vals' => array($this->mainTableRecordId)));
        } else {
            $result = $this->objMainTableRecord->addNew();
            if ($result) {
                $this->mainTableRecordId = $this->objMainTableRecord->getId();
            }
        }

        if (!$result) {
            $this->error = $this->objMainTableRecord->getError();
        }

        return $result;
    }

    public function getMainTableRecordId() {
        return $this->mainTableRecordId;
    }

}
