<?php

class BlogPostsController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
		
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewBlogPost($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditBlogPost($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        ;
    }

    public function index() {
        $brcmb = new Breadcrumb();
        $brcmb->add("Blog Post Management");
        $this->set('breadcrumb', $brcmb->output());
        $form = $this->getSearchForm();
        $this->set('frmSearch', $form);
        $this->_template->render();
    }

    public function getSearchForm() {
        $form = new Form('frmSearch');
        $form->addTextBox(Info::t_lang('POST_TITLE'), 'post_title', '');
        $form->addSelectBox(Info::t_lang('POST_STATUS'), BlogPost::DB_TBL_PREFIX . 'status', BlogConstants::blogPostStatus());
        $form->addHiddenField('', 'page', 1);
        $form->addSubmitButton('', 'btn_submit', Info::t_lang('SEARCH'));
        $form->addButton('', 'cancel_search', Info::t_lang('SHOW_ALL'));
        return $form;
    }

    public function listing() {
        if (!FatUtility::isAjaxCall()) {
            FatUtility::dieWithError(Info::t_lang('FRM_ERROR_INVALID_REQUEST'));
        }
        $pagesize = static::PAGESIZE;
        $pagesize = ($pagesize > 0 ? $pagesize : 10);
        $post = FatApp::getPostedData();
        $page = 1;
        if (isset($post['page']) && FatUtility::convertToType($post['page'], FatUtility::VAR_INT) > 0) {
            $page = $post['page'];
        } else {
            $post['page'] = $page;
        }
        $blogPosts = BlogPosts::search();
        $blogPosts->joinTable('tbl_blog_post_category_relation', 'left join', BlogPosts::DB_TBL_PREFIX . 'id = relation_post_id');
        $blogPosts->joinTable(BlogCategories::DB_TBL, 'left join', BlogCategories::DB_TBL_PREFIX . 'id = relation_category_id');
        $blogPosts->addGroupby('relation_post_id');
        $blogPosts->addMultipleFields(array(BlogPosts::DB_TBL_PREFIX . 'title', BlogPosts::DB_TBL_PREFIX . 'published', BlogPosts::DB_TBL_PREFIX . 'status', BlogPosts::DB_TBL_PREFIX . 'date_time', BlogPosts::DB_TBL_PREFIX . 'featured', BlogPosts::DB_TBL_PREFIX . 'id', 'group_concat(' . BlogCategories::DB_TBL_PREFIX . 'title ) as categories'));
        if (!empty($post['post_title'])) {
            $blogPosts->addCondition(BlogPosts::DB_TBL_PREFIX . 'title', 'LIKE', '%' . $post['post_title'] . '%');
        }
        if ($post['post_status'] != '') {
            $blogPosts->addCondition(BlogPosts::DB_TBL_PREFIX . 'status', '=', $post['post_status']);
        }
        $blogPosts->addOrder('post_id', 'desc');
        $blogPosts->setPageNumber($page);
        $blogPosts->setPageSize($pagesize);
        $rs = $blogPosts->getResultSet();
        $this->set('list', FatApp::getDb()->fetchAll($rs, BlogPosts::DB_TBL_PREFIX . 'id'));

        $pageCount = $blogPosts->pages();
        $this->set('pageCount', $pageCount);
        if ($pageCount > 1) {
            $this->set('pagination', $this->getPagination($pageCount, $page, 5));
        }
        $this->set('pageNumber', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);

        $this->_template->render(false, false, 'blog-posts/listing.php', false, true);
    }

    public function getBlogPost($postId = 0) {
        $blogPostSearch = BlogPosts::search();
        $blogPostSearch->addDirectCondition(BlogPosts::DB_TBL_PREFIX . 'id =' . $postId);
        $resultSet = $blogPostSearch->getResultSet();
        $record = (!empty($resultSet)) ? FatApp::getDb()->fetch($resultSet) : array();
        return $record;
    }

    public function getRelativeCategories($postId = 0) {
        if ($postId) {
            $searchBase = new SearchBase('tbl_blog_post_category_relation');
            $searchBase->addDirectCondition('relation_post_id=' . $postId);
            $resultSet = $searchBase->getResultSet();
            $records = FatApp::getDb()->fetchAll($resultSet, 'relation_category_id');
            return array_keys($records);
        }
        return array();
    }

    public function getCategoryTree() {
        $blogCategories = BlogCategories::search();
        $blogCategories->addDirectCondition(BlogCategories::DB_TBL_PREFIX . 'status = ' . BlogConstants::ENTITY_ACTIVE);
        $blogCategories->addGroupby(BlogCategories::DB_TBL_PREFIX . 'id');
        $resultSet = $blogCategories->getResultSet();
        $catArr = (!empty($resultSet)) ? FatApp::getDb()->fetchAll($resultSet) : array();
        $resArr = array();
        foreach ($catArr as &$category) {
            $resArr[$category[BlogCategories::DB_TBL_PREFIX . 'parent']][] = &$category;
        }
        foreach ($catArr as &$category) {
            if (isset($resArr[$category[BlogCategories::DB_TBL_PREFIX . 'id']])) {
                $category['child'] = $resArr[$category[BlogCategories::DB_TBL_PREFIX . 'id']];
            }
        }
        return $resArr;
    }

    public function form($postId = 0) {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $brcmb = new Breadcrumb();
        $brcmb->add("Blog Post Management", FatUtility::generateUrl('blogPosts'));

        $form = $this->getForm($postId);
        $postId = FatUtility::convertToType($postId, FatUtility::VAR_INT);
        if ($postId > 0) {
            $record = $this->getBlogPost($postId);

            $brcmb->add($record['post_title']);
            if (is_array($record) && count($record)) {
                $form->fill($record);
            }
            $postImages = $this->getPostImages($postId);

            $this->set('postImages', $postImages);
        }
        $post = FatApp::getPostedData();
        if ($post != false) {
            $form->fill($post);
        }
        $tree = $this->getCategoryTree();
        $relatedCategories = $this->getRelativeCategories($postId);
        $this->set('breadcrumb', $brcmb->output());
        $this->set('relatedCategories', $relatedCategories);
        $this->set('tree', $tree);
        $this->set('form', $form);
        $this->set('postId', $postId);
        $this->_template->render(true, true, 'blog-posts/form.php');
    }

    public function deleteExistingRelation($postId = 0) {
        return FatApp::getDb()->deleteRecords('tbl_blog_post_category_relation', array('smt' => 'relation_post_id=?', 'vals' => array($postId)));
    }

    public function getPostImages($postId = 0) {

        $postId = FatUtility::convertToType($postId, FatUtility::VAR_INT);
        if ($postId < 1) {
            return false;
        }

        $srch = BlogPosts::imgSearch();
        $srch->addCondition(BlogPosts::DB_IMG_TBL_PREFIX . 'post_id', '=', $postId);
        $srch->addMultipleFields(array(BlogPosts::DB_IMG_TBL_PREFIX . 'id', BlogPosts::DB_IMG_TBL_PREFIX . 'file_name', BlogPosts::DB_IMG_TBL_PREFIX . 'default'));
        $rs = $srch->getResultSet();
        $pimgs = array();
        while ($row = FatApp::getDb()->fetch($rs)) {
            $pimgs['imgs'][$row[BlogPosts::DB_IMG_TBL_PREFIX . 'id']] = $row[BlogPosts::DB_IMG_TBL_PREFIX . 'file_name'];
            if ($row[BlogPosts::DB_IMG_TBL_PREFIX . 'default'] == 1) {
                $pimgs['main_img'] = $row[BlogPosts::DB_IMG_TBL_PREFIX . 'id'];
            }
        }
        return $pimgs;
    }

    public function setup() {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        
        /* $postId = FatUtility::convertToType($post[BlogPost::DB_TBL_PREFIX . 'id'], FatUtility::VAR_INT); */
        $postId = FatApp::getPostedData(BlogPost::DB_TBL_PREFIX . 'id', FatUtility::VAR_INT, 0);
        
        $form = $this->getForm($postId);
        $post = $form->getFormDataFromArray($post);
        if ($post == false) {
            Message::addErrorMessage($form->getValidationErrors());
            $this->form();
            exit;
        } else {
            $blogPost = new BlogPost($postId);
            if ($postId == 0) {
                $post[BlogPost::DB_TBL_PREFIX . 'date_time'] = FatDate::nowInTimezone(date_default_timezone_get(), 'Y-m-d H:i:s');
            }
            if (FatUtility::convertToType($post[BlogPost::DB_TBL_PREFIX . 'status'], FatUtility::VAR_INT) == BlogConstants::POST_PUBLISHED) {
                $post[BlogPost::DB_TBL_PREFIX . 'published'] = FatDate::nowInTimezone(date_default_timezone_get(), 'Y-m-d H:i:s');
            } else if (FatUtility::convertToType($post[BlogPost::DB_TBL_PREFIX . 'status'], FatUtility::VAR_INT) == BlogConstants::POST_DRAFTED) {
                $post[BlogPost::DB_TBL_PREFIX . 'published'] = 0; // to be confirmed
            }
            $blogPost->assignValues($post);
            $count = 0;
            if (isset($_FILES['post_image_file_name'])) {
                $file = $_FILES['post_image_file_name'];
                foreach ($file['name'] as $key => $val) {
                    if ($val != '' && $file['error'][$key] == 0) {
                        $count++;
                    }
                }
            }
            if ($count != 0) {
                $image_names = array();
                if ($this->saveUploadedPostFiles($_FILES, $image_names)) {
                    $post['post_image_file_name'] = $image_names;
                } else {
                    Message::addErrorMessage(Info::t_lang("FORM_ERROR_IMAGE_ERROR"));
                    FatApp::redirectUser(FatUtility::generateUrl('BlogPosts'));
                }
            }
            if ($postId > 0) {
                $post_images = $this->getPostImages($postId);
            }
            if ($postId > 0 && $post['post_id'] != $postId) {
                Message::addErrorMessage(Info::t_lang("FORM_ERROR_UNAUTHORIZED_ACCESS"));
                FatApp::redirectUser(FatUtility::generateUrl('BlogPosts'));
            }

            $remove = array();
            if ($postId > 0 && isset($post['post_removed_images']) && strlen($post['post_removed_images']) > 0 && $this->setRemoveImageData($post['post_removed_images'], $post_images['imgs'], $remove)) {
                $post['remove_post_images'] = $remove;
            }
            if (!empty($post[BlogPost::DB_TBL_PREFIX . 'id']) && !empty($remove)) {
                $this->deleteMarkedImages($postId, $remove);
            }
            if ($postId = $blogPost->save()) {
                $metaInfo = array(BlogPost::DB_CHILD_TBL_PREFIX . 'record_id' => $postId, BlogPost::DB_CHILD_TBL_PREFIX . 'record_type' => BlogConstants::BMETA_RECORD_TYPE_POST, BlogPost::DB_CHILD_TBL_PREFIX .
                    'title' => $post[BlogPost::DB_CHILD_TBL_PREFIX . 'title'], BlogPost::DB_CHILD_TBL_PREFIX . 'keywords' => $post[BlogPost::DB_CHILD_TBL_PREFIX . 'keywords'],
                    BlogPost::DB_CHILD_TBL_PREFIX . 'description' => $post[BlogPost::DB_CHILD_TBL_PREFIX . 'description'],
                    BlogPost::DB_CHILD_TBL_PREFIX . 'others' => $post[BlogPost::DB_CHILD_TBL_PREFIX . 'others'],
                    BlogPost::DB_CHILD_TBL_PREFIX . 'id' => $post[BlogPost::DB_CHILD_TBL_PREFIX . 'id']
                );

                if (!$blogPost->saveMetaInfo($metaInfo)) {
                    Message::addErrorMessage(FatApp::getDb()->getError());
                    $this->form();
                    return;
                }

                $categoriesArr = FatApp::getPostedData('relation_category_id');
                if (count($categoriesArr)) {
                    $this->deleteExistingRelation($postId);
                    foreach ($categoriesArr as $cat) {
                        $relationArr = array('relation_post_id' => $postId, 'relation_category_id' => $cat);
                        if (!$blogPost->savePostCategoryRelation($relationArr)) {
                            Message::addErrorMessage(FatApp::getDb()->getError());
                            $this->form();
                            return;
                        }
                    }
                }

                if (!empty($image_names)) {
                    $isDefault = 1;
                    foreach ($image_names as $img) {
                        $blogPostImgArr = array(BlogPosts::DB_IMG_TBL_PREFIX . 'file_name' => $img, BlogPosts::DB_IMG_TBL_PREFIX . 'post_id' => $postId, BlogPosts::DB_IMG_TBL_PREFIX . 'default' => $isDefault);
                        if (!$blogPost->savePostImage($blogPostImgArr)) {
                            Message::addErrorMessage(FatApp::getDb()->getError());
                            $this->form();
                            return;
                        }
                        $isDefault = 0;
                    }
                }
                if (!empty($post[BlogPost::DB_TBL_PREFIX . 'id'])) {
                    // $this->deleteMarkedImages($postId,$remove);
                    $lblString = 'BLOG_POST_UPDATED';
                } else {
                    $lblString = 'BLOG_POST_SAVED';
                }
                Message::addMessage(Info::t_lang($lblString));
                FatApp::redirectUser(FatUtility::generateUrl('BlogPosts'));
            } else {
                Message::addErrorMessage($blogPost->getError());
                $this->form();
                return;
            }
            Message::addErrorMessage(Info::t_lang('FORM_ERROR_SOMETHING_WENT_WRONG'));
            $this->form();
            return;
        }
    }

    private function saveUploadedPostFiles(&$files, &$image_names) {
        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        if (is_array($files['post_image_file_name']['name'])) {
            foreach ($files['post_image_file_name']['name'] as $id => $filename) {
                if (is_uploaded_file($files['post_image_file_name']['tmp_name'][$id])) {
                    $saved_image_name = '';
                    if (ImageHandler::saveImage($files['post_image_file_name']['tmp_name'][$id], $files['post_image_file_name']['name'][$id], $saved_image_name, AttachedFile::POST_IMG_FOLDER)) {
                        $image_names[] = $saved_image_name;
                    } else {
                        Message::addErrorMessage($files['post_image_file_name']['name'][$id] . ' ' . $saved_image_name);
                        return false;
                    }
                }
            }
        } elseif (is_uploaded_file($files['post_image_file_name']['tmp_name'])) {
            $saved_image_name = '';

            if (ImageHandler::saveImage($files['post_image_file_name']['tmp_name'], $files['post_image_file_name']['name'], $saved_image_name, AttachedFile::POST_IMG_FOLDER)) {
                $image_names[] = $saved_image_name;
            } else {
                Message::addErrorMessage($files['post_image_file_name']['name'] . ' ' . $saved_image_name);
                return false;
            }
        }
        if (sizeof($image_names) > 0)
            return true;
        return false;
    }

    protected function setRemoveImageData(&$img_id_str, &$saved_imgs, &$imgs_to_remove) {
        $img_ids = explode(',', $img_id_str);
        if (is_array($img_ids) && sizeof($img_ids) > 0) {
            foreach ($img_ids as $img_id) {
                if (isset($saved_imgs[$img_id]) && strlen($saved_imgs[$img_id]) > 4) {
                    $imgs_to_remove[$img_id] = $saved_imgs[$img_id];
                }
            }
            if (sizeof($imgs_to_remove) > 0) {
                return true;
            }
        }
        return false;
    }

    public function delete($postId = 0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $postId = FatUtility::convertToType($postId, FatUtility::VAR_INT);
        $this->deleteMarkedImages($postId);
        $this->deleteExistingRelation($postId);
        FatApp::getDb()->deleteRecords(BlogPost::DB_CHILD_TBL, array('smt' => 'bmeta_record_id=?', 'vals' => array($postId)));
        $blogPost = new BlogPost($postId);
        $blogPost->deleteRecord();
        Message::addMessage(Info::t_lang('BLOG_POST_DELETED'));
        FatApp::redirectUser(FatUtility::generateUrl('BlogPosts'));
    }

    public function deleteMarkedImages($postId = 0, $remove = null) {
        $db = FatApp::getDb();
        if (!empty($remove) && is_array($remove) && sizeof($remove) > 0) {

            $rem_img_ids = array_map('intval', array_keys($remove));
            if (is_array($rem_img_ids) && sizeof($rem_img_ids) > 0) {
                $rem_img_ids = implode(',', $rem_img_ids);

                if (strlen($rem_img_ids) > 0 && $db->deleteRecords('tbl_blog_post_images', array('smt' => BlogPosts::DB_IMG_TBL_PREFIX . 'post_id = ? AND ' . BlogPosts::DB_IMG_TBL_PREFIX . 'id IN ( ? )', 'vals' => array($postId, $rem_img_ids))) && $db->rowsAffected() == sizeof($remove)) {
                    foreach ($remove as $img) {
                        $fileHandler = new FileHandler;
                        $fileHandler->deleteFile($img, AttachedFile::POST_IMG_FOLDER);
                    }
                }
            }
        } else {
            $post_images = $this->getPostImages($postId);
            $remove = $post_images['imgs'];
            if ($db->deleteRecords('tbl_blog_post_images', array('smt' => 'post_image_post_id = ?', 'vals' => array($postId)))) {
                foreach ($remove as $img) {
                    $fileHandler = new FileHandler;
                    $fileHandler->deleteFile($img, AttachedFile::POST_IMG_FOLDER);
                }
            }
        }
    }

    public function getForm($postId = 0) {

        $postId = FatUtility::convertToType($postId, FatUtility::VAR_INT);
        $categoriesSearch = BlogCategories::search();
        $categoriesSearch->addCondition(BlogCategories::DB_TBL_PREFIX . 'status', '=', BlogConstants::ENTITY_ACTIVE);
        
        $categoriesSearch->addMultipleFields(array(BlogCategories::DB_TBL_PREFIX . 'id', BlogCategories::DB_TBL_PREFIX . 'title'));
        $categoriesSearch->doNotCalculateRecords();

        $rs = $categoriesSearch->getResultSet();


        $categoriesArr = ($rs) ? FatApp::getDb()->fetchAllAssoc($rs) : array();

        $form = new Form('from');
        $form->addHiddenField('Id', BlogPost::DB_TBL_PREFIX . 'id', '0');
        $form->addRequiredField(Info::t_lang('POST_TITLE'), BlogPost::DB_TBL_PREFIX . 'title');
        $form->addTextBox(Info::t_lang('POST_CONTRIBUTOR_NAME'), BlogPost::DB_TBL_PREFIX . 'contributor_name');
        $form->addRequiredField(Info::t_lang('POST_SEO_NAME'), BlogPost::DB_TBL_PREFIX . 'seo_name');

        $form->addTextarea(Info::t_lang('POST_SHORT_DESCRIPTION'), BlogPost::DB_TBL_PREFIX . 'short_description');

        //$contentField = $form->addHtmlEditor(Info::t_lang('POST_CONTENT') ,BlogPost::DB_TBL_PREFIX.'content');
        $contentField = $form->addTextarea(Info::t_lang('POST_CONTENT'), BlogPost::DB_TBL_PREFIX . 'content');
        $contentField->htmlAfterField = '<div id="tpl_body_editor"></div>' . MyHelper::getInnovaEditorObj('post_content', 'tpl_body_editor');

        $contentField->requirements()->setRequired();
        $statusField = $form->addSelectBox(Info::t_lang('POST_STATUS'), BlogPost::DB_TBL_PREFIX . 'status', BlogConstants::blogPostStatus());
        $statusField->requirements()->setRequired();
        $form->addFileUpload(Info::t_lang('POST_IMAGE'), 'post_image_file_name');

        $form->addSelectBox(Info::t_lang('POST_COMMENT_STATUS'), BlogPost::DB_TBL_PREFIX . 'comment_status', BlogConstants::postCommentStatus());
        $form->addFileUpload('Post Image', 'post_image_file_name[]');
        $fldChk = $form->addCheckboxes('Parent Category', 'relation_category_id', array());
        $fldChk->requirements()->setSelectionRange(1, count($categoriesArr));
        $fldChk->requirements()->setCustomErrorMessage(Info::t_lang('BLOGPOST_CUSTOMERROR_PLEASE_SELECT_ATLEAST_ONE_CATEGORY')); // 'Please Select at least one category'

        $form->addHiddenField('Meta Info Id', BlogPost::DB_CHILD_TBL_PREFIX . 'id', '0');
        $form->addTextbox(Info::t_lang('META_TITLE'), BlogPost::DB_CHILD_TBL_PREFIX . 'title');
        $form->addTextarea(Info::t_lang('META_KEYWORDS'), BlogPost::DB_CHILD_TBL_PREFIX . 'keywords');
        $form->addTextarea(Info::t_lang('META_DESCRIPTION'), BlogPost::DB_CHILD_TBL_PREFIX . 'description');
        $form->addTextarea(Info::t_lang('META_OTHERS'), BlogPost::DB_CHILD_TBL_PREFIX . 'others');
        $form->addHTML('', 'note', Info::t_lang('PAGE_TEXT_NOTE:_META_OTHERS_ARE_HTML_META_TAGS_,_E.G_') . htmlentities(' <meta name="example" content="example" /> .') . Info::t_lang('PAGE_TEXT_WE_ARE_NOT_VALIDATING'));
        $form->addHiddenField('', 'post_removed_images', '');
        $form->addSubmitButton('', 'btn_submit', Info::t_lang('SUBMIT'));
        $form->addButton('', 'btn_cancel', Info::t_lang('CANCEL'));

        return $form;
    }

    public function setMainImage() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $imgId = FatApp::getPostedData('imgid');
        $postId = FatApp::getPostedData('blog_post_id');
        $postId = FatUtility::convertToType($postId, FatUtility::VAR_INT);
        $blogPostImgArr = array(BlogPost::DB_IMG_TBL_PREFIX . 'default' => 0);
        $blogPost = new BlogPost($postId);
        if ($blogPost->updatePostImages($blogPostImgArr, array('smt' => BlogPost::DB_IMG_TBL_PREFIX . 'post_id = ?', 'vals' => array($postId)))) {
            $blogPostImgArr = array(BlogPost::DB_IMG_TBL_PREFIX . 'id' => $imgId, BlogPost::DB_IMG_TBL_PREFIX . 'default' => 1);
            if ($blogPost->savePostImage($blogPostImgArr)) {
                $postImages = $this->getPostImages($postId);
			
                $photo_html = '';
                if (isset($postImages['imgs']) && is_array($postImages['imgs']) && sizeof($postImages['imgs']) > 0) {
                    foreach ($postImages['imgs'] as $id => $img) {
						
                        $id = FatUtility::convertToType($id, FatUtility::VAR_INT);
                        $photo_html .= '<div class="photosquare"><img alt="" src="' . FatUtility::generateUrl('Image', 'post', array($img, BlogConstants::IMG_THUMB_WIDTH, BlogConstants::IMG_THUMB_HEIGHT), CONF_BASE_DIR) . '"> <a class="crossLink" href="javascript:void(0)" onclick="return removeImage(this, ' . $id . ');"></a>';
						 if (!(isset($postImages['main_img']) && $postImages['main_img'] == $id)) {
                        $photo_html .= '<a class="linkset button small black" href="javascript:void(0)" onclick="setMainImage(this, ' . $id . ', ' . $postId . ');">Set Main Image</a>';
						 }
                        $photo_html .= '</div>';
                    }
				
       
                    $obj = new stdClass();
                    $obj->status = 1;
                    $obj->msg = Info::t_lang('BLOGPOSTFRM_SUCCESS_DEFAULT_IMAGE_SET');
                     $obj->imagesHtml = $photo_html;
				
                    die(json_encode($obj));
                }
            }
        }
       
        FatUtility::dieJsonError(Info::t_lang('BLOGPOSTFRM_ERROR_SOMETHING_WENT_WRONG'));
    }

    function markFeatured() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $post_id = FatUtility::int($post['post_id']);
        $featured = FatUtility::int($post['status']);
        if ($post_id <= 0) {
            FatUtility::dieWithError('Invalid Request!');
        }
        $blogPost = new BlogPost($post_id);
        $data[BlogPost::DB_TBL_PREFIX . 'featured'] = $featured;
        $blogPost->assignValues($data);
        if (!$blogPost->save()) {
            FatUtility::dieJsonError('Something Went Wrong. Please try again.');
        }
        FatCache::delete(CACHE_HOME_FEATURED_POSTS);
        FatUtility::dieJsonSuccess('Update Successfully!');
    }

}
