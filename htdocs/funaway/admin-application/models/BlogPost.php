<?php 
class BlogPost extends MyAppModel{

	const DB_TBL = 'tbl_blog_post';
	const DB_TBL_PREFIX = 'post_';
	const DB_CHILD_TBL = 'tbl_blog_meta_data';
	const DB_CHILD_TBL_PREFIX = 'bmeta_';
	const DB_POST_CAT_RELATION_TBL = 'tbl_blog_post_category_relation';
	const DB_POST_CAT_RELATION_TBL_PREFIX = 'relation_';
	const DB_IMG_TBL = 'tbl_blog_post_images';
	const DB_IMG_TBL_PREFIX = 'post_image_';
	
	public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}
	
	public function save()
	{
		return ( parent::save() ) ? $this->mainTableRecordId : 0 ;
	}
	
	public function saveMetaInfo($data=array())
	{
		return FatApp::getDb()->insertFromArray( static::DB_CHILD_TBL , $data ,false,array(),$data ) ;
	}
	public function savePostCategoryRelation($data=array())
	{
		return FatApp::getDb()->insertFromArray(static::DB_POST_CAT_RELATION_TBL,$data,false,array(),$data);
	}
	public function savePostImage($data=array())
	{
		return FatApp::getDb()->insertFromArray(static::DB_IMG_TBL,$data,false,array(),$data);
	}
	public function updatePostImages($data , $whr)
	{
		$tblRecord = new TableRecord(BlogPost::DB_IMG_TBL);
		$tblRecord->assignValues($data);
		return $tblRecord->update($whr);
	}	
}