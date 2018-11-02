<?php
class Packages{
	const PACKAGE_TBL ='tbl_packages';
	const PACKAGE_LANG_TBL ='tbl_packages_lang';
	const LANG_TBL ='tbl_language';
	const PACKAGE_OPTIONS_TBL ='tbl_package_options';
	private $error;
	
	public function getPackage($package_id){
		$search = new SearchBase(static::PACKAGE_TBL,'package');
		$search->addCondition('package.package_id','=',$package_id);
		$record = FatApp::getDb()->fetch($search->getResultSet());
		$lang_record = MyHelper::getLangFields($package_id,'packagelang_package_id','packagelang_lang_id',array('package_name','package_sub_title','package_description'),static::PACKAGE_LANG_TBL);
		return  array_merge($record,$lang_record);
		
	}
	
		
	public function getPackages($lang_id=1){
		$search = new SearchBase(static::PACKAGE_TBL,'package');
		$search->joinTable(static::PACKAGE_LANG_TBL,'INNER JOIN','package_lang.packagelang_package_id=package.package_id','package_lang');
		$search->addCondition('packagelang_lang_id','=',$lang_id);
		$search->addOrder('package_name');
		return $search;
	}
	
	public function getOptions($package_id=0,$option_id=0){
		$search = new SearchBase(static::PACKAGE_OPTIONS_TBL,'package_opt');
		if(!empty($package_id)){
			$search->addCondition('packageopt_package_id','=',$package_id);
		}
		if(!empty($option_id)){
			$search->addCondition('packageopt_id','=',$option_id);
		}
		$search->addOrder('packageopt_days');
		
		return $search;
	}
	
	public function saveOption($data,$where=array()){
		$tbl = new TableRecord(static::PACKAGE_OPTIONS_TBL);
		$tbl->assignValues($data);
		if(!empty($where)){
			if(!$tbl->update($where)){
				$this->error = 'Something went wrong.';
				return false;
			}
			return true;
		}
		if(!$tbl->addNew()){
			$this->error = 'Something went wrong.';
			return false;
		}
		return true;
	}
		
	public function addPackageLang($data,$lang_id,$package_id){
		$tbl = new TableRecord(static::PACKAGE_LANG_TBL);
		$data['packagelang_package_id']=FatUtility::int($package_id);
		$tbl->assignValues($data);
		if(!$tbl->addNew()){
			$this->error = $tbl->getError();
			return false;
		}
		return true;
	}
	
	public function updatePackageLang($data,$lang_id,$package_id){
		$tbl = new TableRecord(static::PACKAGE_LANG_TBL);
		if($package_id >0){// update
			$tbl->assignValues($data);
			if(!$tbl->update(array('smt'=>'packagelang_package_id = ? and packagelang_lang_id = ? ','vals'=>array($package_id,$lang_id)))){
				$this->error = $tbl->getError();
				return false;
			}
			return $package_id;
		}
	}
	
	
	
	public function savePackage($package_active,$package_id=0){
		$tbl = new TableRecord(static::PACKAGE_TBL);
		$data = array('package_active'=>FatUtility::int($package_active));
		$tbl->assignValues($data);
		if($package_id >0){
			if(!$tbl->update(array('smt'=>'package_id = ?','vals'=>array($package_id)))){
				$this->error = $tbl->getError();
				return false;
			}
			return $package_id;
		}
		if(!$tbl->addNew()){
			$this->error = $tbl->getError();
			return false;
		}
		return $tbl->getId();
	}
	
	public function getError(){
		return $this->error;
	}
}
?>