<?php 
class Blogcomment extends MyAppModel {

    const DB_TBL = 'tbl_blog_post_comments';
	const DB_TBL_PREFIX = 'comment_';
	
   public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}

}