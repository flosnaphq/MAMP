<?php

class BlogPosts extends MyAppModel {

    const DB_TBL = 'tbl_blog_post';
    const DB_TBL_PREFIX = 'post_';
    const DB_CHILD_TBL = 'tbl_blog_meta_data';
    const DB_CHILD_TBL_PREFIX = 'bmeta_';
    const DB_IMG_TBL = 'tbl_blog_post_images';
    const DB_IMG_TBL_PREFIX = 'post_image_';
    const BLOG_SESSION_ELEMENT = 'blog_session';

    public function __construct($Id = 0) {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $Id);
    }

    public static function search($imgJoin = false) {
        $postSearch = new SearchBase(static::DB_TBL);
        $postSearch->joinTable(static::DB_CHILD_TBL, 'LEFT JOIN', static::DB_TBL_PREFIX . 'id = ' . static::DB_CHILD_TBL_PREFIX . 'record_id AND ' . static::DB_CHILD_TBL_PREFIX . 'record_type = ' . BlogConstants::BMETA_RECORD_TYPE_POST);
        // $postSearch->addDirectCondition( static::DB_CHILD_TBL_PREFIX . 'record_type = ' . BlogConstants::BMETA_RECORD_TYPE_POST );
        if ($imgJoin) {
            $postSearch->joinTable(static::DB_IMG_TBL, 'LEFT JOIN', static::DB_IMG_TBL_PREFIX . 'post_id = ' . static::DB_TBL_PREFIX . 'id');
        }
        return $postSearch;
    }

    public static function imgSearch() {
        $postImgSearch = new SearchBase(SELF::DB_IMG_TBL);
        return $postImgSearch;
    }

    function getArchives() {

        $srch = self::search();
        $srch->addFld(array('DATE_FORMAT(' . self::DB_TBL_PREFIX . 'published, "%M-%Y") AS created_month', 'COUNT( ' . self::DB_TBL_PREFIX . 'id ) as total_post'));
        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', 1);
        $srch->addGroupBy('created_month');
        $srch->addOrder(self::DB_TBL_PREFIX . 'published', 'ASC');
        $srch->doNotCalculateRecords();
        $rs = $srch->getResultSet();

        return ( ( $rs ) ? FatApp::getDb()->fetchAll($rs) : array() );
    }

    function getRecentPost($postsLimit) {

        $srch = self::search();

        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', 1);

        $srch->joinTable(Blogcomments::DB_TBL, 'LEFT OUTER JOIN', Blogcomments::DB_TBL_PREFIX . 'post_id = ' . self::DB_TBL_PREFIX . 'id AND ' . Blogcomments::DB_TBL_PREFIX . 'status = 1');

        $srch->setPageSize($postsLimit);

        $srch->addMultipleFields(array(self::DB_TBL_PREFIX . 'id', self::DB_TBL_PREFIX . 'title', self::DB_TBL_PREFIX . 'seo_name', self::DB_TBL_PREFIX . 'comment_status', self::DB_TBL_PREFIX . 'published', 'count(' . Blogcomments::DB_TBL_PREFIX . 'post_id) as comment_count'));


        $srch->addOrder(self::DB_TBL_PREFIX . 'published', 'DESC');
        $srch->addGroupBy(self::DB_TBL_PREFIX . 'id');

        $rs = $srch->getResultSet();
        return ( ( $rs ) ? FatApp::getDb()->fetchAll($rs) : array() );
    }

    function getBlogPosts($data = array()) {

        $srch = self::search(false);
        $srch->joinTable(Blogcomments::DB_TBL, 'LEFT OUTER JOIN', Blogcomments::DB_TBL_PREFIX . 'post_id = ' . self::DB_TBL_PREFIX . 'id AND ' . Blogcomments::DB_TBL_PREFIX . 'status = 1');
        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', 1);

        if (isset($data['month']) && isset($data['year']) && $data['month'] && $data['year']) {
            $srch->addCondition('MONTH( `' . self::DB_TBL_PREFIX . 'published` )', '=', $data['month']);
            $srch->addCondition('YEAR( `' . self::DB_TBL_PREFIX . 'published` )', '=', $data['year']);
        }

        if (isset($data['search']) && $data['search']) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'title', 'like', '%' . $data['search'] . '%');
        }

        $srch->addMultipleFields(
                array(
                    self::DB_TBL_PREFIX . 'id',
                    self::DB_TBL_PREFIX . 'short_description',
                    self::DB_TBL_PREFIX . 'view_count',
                    self::DB_TBL_PREFIX . 'title',
                    self::DB_TBL_PREFIX . 'seo_name',
                    self::DB_TBL_PREFIX . 'comment_status',
                    self::DB_TBL_PREFIX . 'published',
                    self::DB_TBL_PREFIX . 'contributor_name',
                    'count(' . Blogcomments::DB_TBL_PREFIX . 'id) as comment_count',
                //'count(' . self::DB_IMG_TBL_PREFIX . 'id) as images_count'
                )
        );

        $srch->setPageNumber($data['page']);
        $srch->setPageSize($data['pagesize']);
        $srch->addOrder(self::DB_TBL_PREFIX . 'id', 'DESC');
        $srch->addGroupBy(self::DB_TBL_PREFIX . 'id');
        //echo $srch->getQuery();
        $rs = $srch->getResultSet();
        $record_data['total_records'] = $srch->recordCount();
        $record_data['total_pages'] = $srch->pages();
        $record_data['records'] = ( ( $rs ) ? FatApp::getDb()->fetchAll($rs) : array() );
        return $record_data;
    }

    function getBlogPostsByName($postSeoName = '') {

        if (strlen($postSeoName) <= 0)
            return false;

        $srch = self::search();
        $srch->joinTable(Blogcomments::DB_TBL, 'LEFT OUTER JOIN', Blogcomments::DB_TBL_PREFIX . 'post_id = ' . self::DB_TBL_PREFIX . 'id AND ' . Blogcomments::DB_TBL_PREFIX . 'status = 1');
        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', 1);
        $srch->addCondition(self::DB_TBL_PREFIX . 'seo_name', 'LIKE', $postSeoName);

        $srch->addMultipleFields(
                array(
                    self::DB_TBL_PREFIX . 'id',
                    self::DB_TBL_PREFIX . 'content',
                    self::DB_TBL_PREFIX . 'view_count',
                    self::DB_TBL_PREFIX . 'title',
                    self::DB_TBL_PREFIX . 'seo_name',
                    self::DB_TBL_PREFIX . 'comment_status',
                    self::DB_TBL_PREFIX . 'published',
                    self::DB_TBL_PREFIX . 'short_description',
                    self::DB_CHILD_TBL_PREFIX . 'title',
                    self::DB_CHILD_TBL_PREFIX . 'keywords',
                    self::DB_CHILD_TBL_PREFIX . 'description',
                    self::DB_CHILD_TBL_PREFIX . 'others',
                    self::DB_TBL_PREFIX . 'contributor_name',
                    'count(' . Blogcomments::DB_TBL_PREFIX . 'id) as comment_count'
                )
        );
        $srch->doNotCalculateRecords();
        $srch->addGroupBy(self::DB_TBL_PREFIX . 'id');
        // echo $srch->getQuery();exit;
        $rs = $srch->getResultSet();

        return ( ( $rs ) ? FatApp::getDb()->fetch($rs) : array() );
    }

    function getPostCount($postData = array()) {

        if (empty($postData))
            return false;
        elseif (!isset($postData[self::DB_TBL_PREFIX . 'id']) || !isset($postData[self::DB_TBL_PREFIX . 'view_count']))
            return false;


        $postData[self::DB_TBL_PREFIX . 'id'] = FatUtility::int($postData[self::DB_TBL_PREFIX . 'id']);
        if ($postData[self::DB_TBL_PREFIX . 'id'] <= 0)
            return false;

        if (!session_id())
            session_start();

        if (!isset($_SESSION[static::BLOG_SESSION_ELEMENT][$postData[self::DB_TBL_PREFIX . 'id']]['view_count'])) {

            $postData[self::DB_TBL_PREFIX . 'view_count'] = FatUtility::int($postData[self::DB_TBL_PREFIX . 'view_count']);
            $_SESSION[static::BLOG_SESSION_ELEMENT][$postData[self::DB_TBL_PREFIX . 'id']]['view_count'] = $postData[self::DB_TBL_PREFIX . 'view_count'] + 1;

            $postObj = new self($postData[self::DB_TBL_PREFIX . 'id']);
            $postObj->assignValues(array(self::DB_TBL_PREFIX . 'view_count' => $_SESSION[static::BLOG_SESSION_ELEMENT][$postData[self::DB_TBL_PREFIX . 'id']]['view_count']), true);

            if (!$postObj->save()) {
                Message::addErrorMessage($postObj->getError());
            }
        }

        $_SESSION[static::BLOG_SESSION_ELEMENT][$postData[self::DB_TBL_PREFIX . 'id']]['view_count'] = $postData[self::DB_TBL_PREFIX . 'view_count'];
        return $_SESSION[static::BLOG_SESSION_ELEMENT][$postData[self::DB_TBL_PREFIX . 'id']]['view_count'];
    }

    function getAllImagesOfPost($record_id = 0) {

        $record_id = FatUtility::int($record_id);
        if ($record_id < 1)
            return false;

        $srch = self::imgSearch();
        $srch->addCondition(self::DB_IMG_TBL_PREFIX . 'post_id', '=', $record_id);
        $srch->addMultipleFields(array(self::DB_IMG_TBL_PREFIX . 'id'));
        $rs = $srch->getResultSet();
        return ( $rs ) ? FatApp::getDb()->fetchAll($rs) : array();
    }

    function getNextPostSlug($post_id) {
        $post_id = FatUtility::int($post_id);
        $srch = new SearchBase(self::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->addCondition(self::DB_TBL_PREFIX . 'id', '>', $post_id);
        $srch->addFld(self::DB_TBL_PREFIX . 'seo_name');
        $srch->addOrder(self::DB_TBL_PREFIX . 'id', 'desc');
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return empty($row) ? false : $row[self::DB_TBL_PREFIX . 'seo_name'];
    }

    function getPreviousPostSlug($post_id) {
        $post_id = FatUtility::int($post_id);
        $srch = new SearchBase(self::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->addCondition(self::DB_TBL_PREFIX . 'id', '<', $post_id);
        $srch->addFld(self::DB_TBL_PREFIX . 'seo_name');
        $srch->addOrder(self::DB_TBL_PREFIX . 'id', 'desc');
        $srch->setPageNumber(1);
        $srch->setPageSize(1);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        return empty($row) ? false : $row[self::DB_TBL_PREFIX . 'seo_name'];
    }

    function getFeaturedPost($pageNumber = 0, $pageSize = 0) {
        
        $cacheKey = CACHE_HOME_FEATURED_POSTS;
        
        if($list = FatCache::get($cacheKey,CONF_DEF_CACHE_TIME)){
            return json_decode($list,true);
        }
        
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', 1);
        $srch->addOrder(self::DB_TBL_PREFIX . 'id', 'DESC');
        if ($pageNumber > 0) {
            $srch->setPageNumber($pageNumber);
        } else {
            $srch->doNotCalculateRecords();
        }
        if ($pageSize > 0) {
            $srch->setPageSize($pageSize);
        } else {
            $srch->doNotLimitRecords();
        }
        $rs = $srch->getResultSet();
        $list = FatApp::getDb()->fetchAll($rs);
        FatCache::set($cacheKey, json_encode($list,true));
        return ($list) ? $list : array();
    }

}
