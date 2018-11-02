<?php 
class BlogCategory extends MyAppModel{

	const DB_TBL = 'tbl_blog_post_categories';
	const DB_TBL_PREFIX = 'category_';
	const DB_CHILD_TBL = 'tbl_blog_meta_data';
	const DB_CHILD_TBL_PREFIX = 'bmeta_';
	
	
	public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}
	
	public function save()
	{
		return ( parent::save() ) ? $this->mainTableRecordId : 0 ;
	}
	
	public function saveMetaInfo($data=array())
	{
		return FatApp::getDb()->insertFromArray( static::DB_CHILD_TBL , $data ,false,array(),$data) ;
	}
	
}