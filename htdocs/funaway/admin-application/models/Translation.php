<?php
class Translation extends MyAppModel{
	const DB_TBL = 'tbl_translations';
	const DB_TBL_PREFIX = 'trans_';
	
	
	public function __construct($trans_id = 0) {
		$trans_id = FatUtility::convertToType($trans_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $trans_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject()
	{
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	function addUpdate($data)
	{
		$db = FatApp::getDb();
		$tbl = new TableRecord(static::DB_TBL);
		$tbl->assignValues($data);
		

        $cacheAvailable = extension_loaded('apcu') && ini_get('apcu.enabled');

        if ($this->isRecordExist($data['trans_key'])) {
			$success = $tbl->update(array('smt' => 'trans_key = ?', 'vals' => array($data['trans_key'])));
		} else {
		    $success = $tbl->addNew();
		}
		
		if($success) {
			if ($cacheAvailable) {
				$cacheKey = $_SERVER['SERVER_NAME'] . '_' . $data['trans_key'];
				apcu_store($cacheKey, $str);
			}
			return true;
		}
		return false;	
	}
	
	
	
	function getTranslationForForm($key){
		$records=array();
		$srch = self::getSearchObject();
		$srch->addCondition(static::DB_TBL_PREFIX . 'key','=', $key);
		$rs = $srch->getResultSet();
		return $rows = FatApp::getDb()->fetch($rs);
	}
	
	
	
	private function isRecordExist($trans_key){
		$srch = self::getSearchObject();
		$srch->addCondition("trans_key","=",$trans_key);
		$rs = $srch->getResultSet();
		$db = FatApp::getDb();
		$record = $db->fetch($rs);
		if(!empty($record)) 
			return true;
		return false;		
	}
}?>