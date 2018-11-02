<?php 
class Blogcomments extends MyAppModel {

    const DB_TBL = 'tbl_blog_post_comments';
	const DB_TBL_PREFIX = 'comment_';
	
	public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}
	
	public static function search()
	{
		$postSearch = new SearchBase(static::DB_TBL);
		return $postSearch;
	}
	
	function getPostComments( $data = array() ) { 

        $srch = self::search();
		$srch->joinTable ( BlogPosts::DB_TBL, 'INNER JOIN', BlogPosts::DB_TBL_PREFIX . 'id = ' . self::DB_TBL_PREFIX . 'post_id AND ' . BlogPosts::DB_TBL_PREFIX . 'status = 1' );
		$srch->addCondition( self::DB_TBL_PREFIX . 'status', '=', 1 );
		$srch->addCondition( BlogPosts::DB_TBL_PREFIX . 'id', '=', $data['post_id'] );
		
		$srch->addMultipleFields( 
					array(
						self::DB_TBL_PREFIX . 'author_name',
						self::DB_TBL_PREFIX . 'author_email',
						self::DB_TBL_PREFIX . 'content',
						self::DB_TBL_PREFIX . 'date_time',
						self::DB_TBL_PREFIX . 'user_id'
					)
				);
		
		$srch->setPageNumber( $data['page'] );
		$srch->setPageSize( $data['pagesize'] );
		$srch->addOrder( self::DB_TBL_PREFIX . 'id', 'DESC');
		$srch->addGroupBy( self::DB_TBL_PREFIX . 'id' );

        $rs = $srch->getResultSet();
		$record_data['total_records'] 	= $srch->recordCount();
		$record_data['total_pages'] 		= $srch->pages();
		$record_data['records'] = ( ( $rs )?FatApp::getDb()->fetchAll( $rs ):array() );
        return $record_data;
		
    }
	
	function addComment( $post = array() ) { 

		$postId = FatUtility::int( $post[ Blogcomments::DB_TBL_PREFIX . 'post_id' ] );
		if( $postId <= 0 ) { 
			return false;
		}
		$this->assignValues( $post, true );
		if( !$this->save() ) { 
			return false;
		}
		return true;
	}
}