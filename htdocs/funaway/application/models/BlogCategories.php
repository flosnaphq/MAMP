<?php 
class BlogCategories extends MyAppModel{

	const DB_TBL 				= 'tbl_blog_post_categories';
	const DB_TBL_PREFIX 		= 'category_';
	const DB_CHILD_TBL 			= 'tbl_blog_meta_data';
	const DB_CHILD_TBL_PREFIX 	= 'bmeta_';
	const DB_REL_TBL 			= 'tbl_blog_post_category_relation';
	const DB_REL_TBL_PREFIX 	= 'relation_';
	
	public function __construct( $Id = 0 ) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id );
	}
	
	public static function search( $alias = '' ) {
		
		$categorySearch = new SearchBase( static::DB_TBL, $alias );
		
		if( $alias ) { 
			$categorySearch->joinTable( static::DB_CHILD_TBL, 'LEFT JOIN', $alias . '.' . static::DB_TBL_PREFIX . 'id' . '=' . static::DB_CHILD_TBL_PREFIX . 'record_id AND ' . '('. static::DB_CHILD_TBL_PREFIX . 'record_type = ' . BlogConstants::BMETA_RECORD_TYPE_CATEGORY . ' OR ISNULL(' . static::DB_CHILD_TBL_PREFIX . 'record_type))' );
		} else {
			$categorySearch->joinTable( static::DB_CHILD_TBL, 'LEFT JOIN', static::DB_TBL_PREFIX . 'id' . '=' . static::DB_CHILD_TBL_PREFIX . 'record_id AND ' . '('. static::DB_CHILD_TBL_PREFIX . 'record_type = ' . BlogConstants::BMETA_RECORD_TYPE_CATEGORY . ' OR ISNULL(' . static::DB_CHILD_TBL_PREFIX . 'record_type))' );
		}
		
		// $categorySearch->addDirectCondition( '('. static::DB_CHILD_TBL_PREFIX . 'record_type = ' . BlogConstants::BMETA_RECORD_TYPE_CATEGORY . ' OR ISNULL(' . static::DB_CHILD_TBL_PREFIX . 'record_type))', 'AND'  );

		return $categorySearch;
		
	}
	
	public static function categoryRelationPostsSearch( ) { 
		$categorySearch = new SearchBase( SELF::DB_REL_TBL );
		$categorySearch->joinTable( SELF::DB_TBL, 'INNER JOIN', SELF::DB_TBL_PREFIX . 'id' . '=' . SELF::DB_REL_TBL_PREFIX . 'category_id' );
		return $categorySearch;
	}
	
	function getAllCategories() { 

		$srch 		= self::search( );
		$srch->joinTable( self::DB_REL_TBL, 'LEFT OUTER JOIN', self::DB_REL_TBL_PREFIX . 'category_id = ' . self::DB_TBL_PREFIX . 'id' );
		
		$srch->joinTable( BlogPosts::DB_TBL, 'LEFT OUTER JOIN', self::DB_REL_TBL_PREFIX . 'post_id = ' . BlogPosts::DB_TBL_PREFIX . 'id AND ' . BlogPosts::DB_TBL_PREFIX . 'status = 1' );
		
		$srch->addCondition( self::DB_TBL_PREFIX . 'status', '=', 1 );
		$srch->addOrder( self::DB_TBL_PREFIX . 'display_order', 'ASC' );
		$srch->doNotCalculateRecords();
		
		$srch->addMultipleFields ( 
			array ( 
				self::DB_TBL_PREFIX . 'id',
				self::DB_TBL_PREFIX . 'title',
				self::DB_TBL_PREFIX . 'parent',
				self::DB_TBL_PREFIX . 'seo_name',
				'count( ' . BlogPosts::DB_TBL_PREFIX . 'id ) as totalpost'
			)
		);
		
		$srch->addGroupBy ( self::DB_TBL_PREFIX . 'id' );
        $rs = $srch->getResultSet();
		
        return ( ( $rs )?FatApp::getDb()->fetchAll( $rs ):array(  ) );
		
	}
	
	function getSortCategories(){
		$allCategories 		= $this->getAllCategories(); 
		$sortCategories 	= $this->sortCategories( $allCategories );
		return $sortCategories;
	}
	
	private function sortCategories( array $elements, $parentId = 0 ) {
		
        $branch = array();
        foreach ($elements as $element) {
            
			if ( $element['category_parent'] == $parentId ) {
                $children = $this->sortCategories( $elements, $element['category_id'] );
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
			
        }
		
        return $branch;
    }
	
	function getPostCategoriesData ( $postId = 0 ) {
		
		$postId = FatUtility::int( $postId );
		if( $postId <= 0 ) return false;
		
		$srch = self::categoryRelationPostsSearch();
		$srch->addCondition( self::DB_TBL_PREFIX . 'status', '=', 1 );
		$srch->addCondition( self::DB_REL_TBL_PREFIX . 'post_id', '=', $postId );
		$srch->addMultipleFields( array( self::DB_TBL_PREFIX . 'id', self::DB_TBL_PREFIX . 'title', self::DB_TBL_PREFIX . 'seo_name' ) );
		$srch->addOrder( self::DB_TBL_PREFIX . 'title' );
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		
		return ( ($rs)?FatApp::getDb()->fetchAll($rs):array() );
		
	}
	
	function getPostCategories( &$posts = array() ) { 
		if($posts) { 
			foreach($posts as $index => $pd){
				$posts[$index][ BlogPosts::DB_TBL_PREFIX . 'categories' ] = $this->getPostCategoriesData( $pd[ BlogPosts::DB_TBL_PREFIX . 'id' ] );
			}
		}
	}
	
	function getBlogPostsByCategory( $data = array() ) { 
		
		if( !isset( $data['seo_name'] ) ) return array();
		elseif( strlen( $data['seo_name'] ) <= 0 ) return array();

        $srch = self::search( );
		$srch->addCondition( self::DB_TBL_PREFIX . 'status', '=', 1 );
		$srch->addCondition( self::DB_TBL_PREFIX . 'seo_name', 'LIKE', $data['seo_name'] );

		$srch->joinTable ( self::DB_REL_TBL, 'INNER JOIN', self::DB_REL_TBL_PREFIX . 'category_id = ' . self::DB_TBL_PREFIX . 'id' );

		$srch->joinTable ( BlogPosts::DB_TBL, 'INNER JOIN', BlogPosts::DB_TBL_PREFIX . 'id = ' . self::DB_REL_TBL_PREFIX . 'post_id AND ' . BlogPosts::DB_TBL_PREFIX . 'status = 1' );
		
		$srch->joinTable ( BlogPosts::DB_IMG_TBL, 'LEFT JOIN', BlogPosts::DB_IMG_TBL_PREFIX . 'post_id = ' . BlogPosts::DB_TBL_PREFIX . 'id' );

		$srch->joinTable ( Blogcomments::DB_TBL, 'LEFT OUTER JOIN', Blogcomments::DB_TBL_PREFIX . 'post_id = ' . BlogPosts::DB_TBL_PREFIX . 'id AND ' . Blogcomments::DB_TBL_PREFIX . 'status = 1' );

		$srch->addMultipleFields( 
					array(
						BlogPosts::DB_TBL_PREFIX . 'id',
						BlogPosts::DB_TBL_PREFIX . 'short_description',
						BlogPosts::DB_TBL_PREFIX . 'view_count',
						BlogPosts::DB_TBL_PREFIX . 'title',
						BlogPosts::DB_TBL_PREFIX . 'seo_name',
						BlogPosts::DB_TBL_PREFIX . 'comment_status',
						BlogPosts::DB_TBL_PREFIX . 'published',
						BlogPosts::DB_TBL_PREFIX . 'contributor_name',
						'count(' . Blogcomments::DB_TBL_PREFIX . 'id) as comment_count',
						'count(' . BlogPosts::DB_IMG_TBL_PREFIX . 'id) as images_count'
					)
				);

		$srch->setPageNumber( $data['page'] );
		$srch->setPageSize( $data['pagesize'] );
		$srch->addOrder( BlogPosts::DB_TBL_PREFIX . 'id', 'DESC');
		$srch->addGroupBy( BlogPosts::DB_TBL_PREFIX . 'id' );

        $rs = $srch->getResultSet();
		
		$record_data['total_records'] 	= $srch->recordCount();
		$record_data['total_pages'] 		= $srch->pages();
		$record_data['records'] = ( ( $rs )?FatApp::getDb()->fetchAll( $rs ):array() );
		
        return $record_data;
		
    }
	
	//-------------------//
	public function categoriesList(){
		$srch = new SearchBase(self::DB_TBL);
		//$srch->addCondition(self::DB_TBL_PREFIX.'status','=', BlogConstants::ENTITY_ACTIVE);
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		$allCategories = FatApp::getDb()->fetchAll($rs);
		return $allCategories;
	}
	public function getCategories(){
		$result = [];
		$srch = new SearchBase(self::DB_TBL);
		$srch->addCondition(self::DB_TBL_PREFIX.'status','=', BlogConstants::ENTITY_ACTIVE);
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		$activeCategories = FatApp::getDb()->fetchAll($rs);
		
		if(!empty($activeCategories)) {
			$catIds = array_column($activeCategories, 'category_id');
			$catParentIds = array_column($activeCategories, 'category_parent');
			$keyValuePair = array_combine($catIds, $catParentIds);
			foreach($activeCategories as $catData) {
				$flag = $catData['category_parent'];
				if(0 == $flag) {
					$result[] = $catData;
				} else {
					while($flag >= 0) {
						$flag = $this->checkCatParentActive($flag, $keyValuePair);
						if($flag == 0) {
							$result[] = $catData;
							$flag = -1;
						}
					}
				}
			}
		}
		return $result;
	}
	
	private function checkCatParentActive($catParentId, $keyValuePair) {
		if(array_key_exists($catParentId, $keyValuePair) &&  0 == $keyValuePair[$catParentId])  {
			return 0;
		} else if(array_key_exists($catParentId, $keyValuePair)) {
			return $keyValuePair[$catParentId];
		} else {
			return -1;
		}
	}
	
	public function inactiveCatRelatedContent($categoryId) {
		if(0 > $categoryId) {
			return false;
		}
		$getAllCategories = $this->categoriesList();
		$listAllCatWithParent = array();
		$allSubcat = array();
		if(!empty($getAllCategories)) {
			foreach($getAllCategories as $catData) {
				$listAllCatWithParent[$catData['category_id']] = $catData['category_parent'];
			}
			$allSubcat = $this->getAllSubCategories($categoryId, $listAllCatWithParent);
		}
		if(!empty($allSubcat)) {
            $data['category_status'] = 0;
			$this->db = FatApp::getDb();
			
			$q = trim(str_repeat('?,', count($allSubcat)), ',');
			/*Make status of category and sub-categories In-active*/
			FatApp::getDb()->updateFromArray(static::DB_TBL, $data, array( 'smt' => "category_id IN ( $q )", 'vals' => $allSubcat ));
			
			/*Delete relation from post_category_relation tbl related to category and sub-categories*/
			FatApp::getDb()->deleteRecords(static::DB_REL_TBL, array( 'smt' => "relation_category_id IN ( $q )", 'vals' => $allSubcat ));
		}
	}
	private function getAllSubCategories($categoryId, $listAllCatWithParent) {
		$final_array = array($categoryId);
		$result = array($categoryId);
		while(!empty($result)) {
			$getresult = $this->getSubCat($listAllCatWithParent, $result);
			$result = array();
			if(!empty($getresult)) {
				foreach($getresult as $res) {
					$final_array = array_merge($final_array, $res);
					$result = array_merge($result, $res);
				}
			}
		}
		return $final_array;
	}
	
	private function getSubCat($listAllCatWithParent, $catidarr) {
		$returnArr = array();
		if(!empty($catidarr)) {
			for($i = 0; $i < count($catidarr); $i++) {
				$subcatkeys = array_keys($listAllCatWithParent, $catidarr[$i]);
				$returnArr[] = $subcatkeys;
			}
		}
		return $returnArr;
	}
}