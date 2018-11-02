<?php
class Blogcontributions extends MyAppModel {
	
	const DB_TBL = 'tbl_blog_contributions';
	const DB_TBL_PREFIX = 'contribution_';
    
	public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}
	
	public static function search()
	{
		$postSearch=new SearchBase(static::DB_TBL);
		return $postSearch;
	}
	
	function addContribution( $post = array() ) { 

		$commentObj = new Blogcontributions();
		$commentObj->assignValues( $post, true );

		if( !$commentObj->save() ) { 
			return false;
		}
		
		return $commentObj->getMainTableRecordId();
		
	}
}