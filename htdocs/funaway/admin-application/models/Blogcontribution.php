<?php
class Blogcontribution extends  MyAppModel {
	
	const DB_TBL = 'tbl_blog_contributions';
	const DB_TBL_PREFIX = 'contribution_';
    
	public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}
	
}