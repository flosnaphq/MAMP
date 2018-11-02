<?php
class Advertisement{
	const AD_REQUEST_TBL ='tbl_advertisement_requests';
	const FILE_TBL ='tbl_attached_files';
	const AD_TBL ='tbl_advertisements';
	private $error;
	
	public function getAdRequestSearch(){
		$search = new SearchBase(static::AD_REQUEST_TBL);
		$search->addOrder('adrequest_id','desc');
		return $search;
	}
	
	public function getAdSearch(){
		$search = new SearchBase(static::AD_TBL);
		$search->joinTable(static::FILE_TBL,' LEFT OUTER JOIN ','afile_record_id=ad_id');
		$search->addCondition('afile_type','=',AttachedFile::FILETYPE_AD_PHOTO);
		$search->addOrder('ad_title');
		return $search;
	}
	
	public function getAd($ad_id){
		$ad_id = FatUtility::int($ad_id);
		$search = $this->getAdSearch();
		$search->addCondition('ad_id','=',$ad_id);
		return FatApp::getDb()->fetch($search->getResultSet());
	}
	
	public function saveAd($data,$ad_id=0){
		$tbl = new TableRecord(static::AD_TBL);
		$tbl->assignValues($data);
		
		if(!empty($ad_id)){
			$where =array('smt'=>'ad_id = ?','vals'=>array($ad_id));
			if(!$tbl->update($where)){
				$this->error = 'Something went wrong.';
				return false;
			}
			return $ad_id;
		}
		$tbl->setFldValue('ad_created', Info::currentDate(),true);
		if(!$tbl->addNew()){
			$this->error = 'Something went wrong.';
			return false;
		}
		return $tbl->getId();
	}
	
	public function updateStatusExpireAd(){
		$tbl = new TableRecord(static::AD_TBL);
		$tbl->assignValues(array('ad_active'=>0));
		return $tbl->update(array('smt'=>'ad_ending_date < ? ','vals'=>array(Info::currentDate())));
		
	}
		
	public function getError(){
		return $this->error;
	}
}

?>