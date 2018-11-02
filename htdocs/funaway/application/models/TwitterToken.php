<?php
class TwitterToken extends MyAppModel {
	
	const DB_TBL = 'tbl_twitter_token';
	const DB_TBL_PREFIX = 'twittertoken_';
	
	public function __construct($userId = 0) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'user_id', $userId );
		
	}
	
	public function getTwitterDetail($twitter_id){
		$srch = new SearchBase('tbl_twitter_token');
		$srch->addCondition('twittertoken_twitter_id', '=', $twitter_id);
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		if(empty($row)) return false;
		return $row;
	}
	
	public function saveTwitterToken($data){
		$twitter_id = isset($data['twittertoken_twitter_id'])?$data['twittertoken_twitter_id']:'';
		if(empty($twitter_id)) return false;
		$tbl = new TableRecord('tbl_twitter_token');
		$tbl->assignValues($data);
		if($this->getTwitterDetail($twitter_id) !== false){
			return $tbl->update(array('smt'=>'twittertoken_twitter_id = ?','vals'=>array($twitter_id)));
		}
		return $tbl->addNew();
		
	}
}