<?php
class Newsletters{
	const NEWSLETTER_TBL ='tbl_newsletters';
	private $error;
	
	public function getSearch(){
		$search = new SearchBase(static::NEWSLETTER_TBL);
		$search->addOrder('newsletter_created','desc');
		return $search;
	}
	
	public function getError(){
		return $this->error;
	}
}

?>