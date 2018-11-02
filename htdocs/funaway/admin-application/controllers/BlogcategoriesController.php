<?php

class BlogcategoriesController extends AdminBaseController
{

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action)
    {
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewBlogCategory($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditBlogCategory($this->admin_id);
        if (!$this->canView) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Unauthorized Access!');
            }
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add("Blog category Management");
        $this->set('breadcrumb', $brcmb->output());
        $frm = $this->getSearchForm();
        $this->set('frmCategory', $frm);
        $this->set('catId', 0);
        $this->_template->render();
    }

    public function listBlogCategories()
    {
        if (!FatUtility::isAjaxCall()) {
            Message::addErrorMessage(Info::t_lang('INVALID_REQUEST'));
            FatApp::redirectUser(FatUtility::generateUrl(''));
        }


        $post = FatApp::getPostedData();
        $srch = BlogCategories::search('bpc');
        if (isset($post['category_title']) && $post['category_title'] != "") {
            $srch->addCondition('bpc.category_title', 'like', '%' . $post['category_title'] . '%');
        }
        if (isset($post['category_status']) && $post['category_status'] != "") {
            $srch->addCondition('bpc.category_status', '=', $post['category_status']);
        }

        if (isset($post['category_parent']) && $post['category_parent'] != "") {
            $srch->addCondition('bpc.category_parent', '=', $post['category_parent']);
        }
        $srch->addMultipleFields(array('bpc.*', 'bpc1.`category_title` AS cat_parent', 'COUNT(bpc2.`category_id`) AS cat_level'));
        $srch->joinTable('tbl_blog_post_categories', 'LEFT OUTER JOIN', 'bpc1.`category_id` = bpc.category_parent', 'bpc1');
        $srch->joinTable('tbl_blog_post_categories', 'LEFT OUTER JOIN', 'bpc2.`category_code` LIKE CONCAT("%",bpc.category_code, "%")', 'bpc2');

        $srch->addOrder('bpc.category_status', 'DESC');
        $srch->addOrder('bpc.category_display_order', 'ASC');
        $srch->addGroupBy('bpc.category_id');

        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);

        $this->set('records', $records);

        $this->set('category_parent', $post['category_parent']);
        $this->_template->render(false, false);
    }

    public function filteredBlogCatogries()
    {
        if (!FatUtility::isAjaxCall()) {
            Message::addErrorMessage(Info::t_lang('INVALID_REQUEST'));
            FatApp::redirectUser(FatUtility::generateUrl(''));
        }
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }

        $post = FatApp::getPostedData();

        $srch = BlogCategories::search('bpc');
        if (isset($post['category_title']) && $post['category_title'] != "") {
            $srch->addCondition('bpc.category_title', 'like', '%' . $post['category_title'] . '%');
        }
        if (isset($post['category_status']) && $post['category_status'] != "") {
            $srch->addCondition('bpc.category_status', '=', $post['category_status']);
        }

        if (isset($post['category_parent']) && $post['category_parent'] != "") {
            // $srch->addCondition('bpc.category_parent', '=', $post['category_parent']);
        }
        $srch->addMultipleFields(array('bpc.*', 'bpc1.`category_title` AS cat_parent', 'COUNT(bpc2.`category_id`) AS cat_level'));
        $srch->joinTable('tbl_blog_post_categories', 'LEFT OUTER JOIN', 'bpc1.`category_id` = bpc.category_parent', 'bpc1');
        $srch->joinTable('tbl_blog_post_categories', 'LEFT OUTER JOIN', 'bpc2.`category_code` LIKE CONCAT("%",bpc.category_code, "%")', 'bpc2');

        $srch->addOrder('bpc.category_status', 'DESC');
        $srch->addOrder('bpc.category_display_order', 'ASC');
        $srch->addGroupBy('bpc.category_id');

        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set('records', $records);

        // $this->set('category_parent', $post['category_parent']);		 
        $this->_template->render(false, false);
    }

    public function setCatDisplayOrder()
    {
        if (!FatUtility::isAjaxCall()) {
            Message::addErrorMessage('Invalid Request');
            FatApp::redirectUser(FatUtility::generateUrl(''));
        }
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $catId = FatUtility::convertToType($post['catId'], FatUtility::VAR_INT);
        foreach ($post['category'] as $key => $value) {
            if (!empty($value)) {
                $cat[$key]['category_id'] = $value;
                $cat[$key]['category_display_order'] = $key;
                $category = new BlogCategory($value);
                $category->assignValues($cat[$key]);
                if (!($category->save())) {
                    FatUtility::dieJsonError(FatApp::getDb()->getError());
                }
            }
        }
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');

        $frm->addTextBox(Info::t_lang('CATEGORY_TITLE'), 'category_title');
        $frm->addSelectBox(Info::t_lang('CATEGORY_STATUS'), 'category_status', Info::getStatus(), "", array(), Info::t_lang('DOES_NOT_MATTER'));

        $frm->addSubmitButton('', 'btn_submit', 'Search');
        $frm->addButton('', 'cancel_search', 'Reset');

        return $frm;
    }

    public function blogChildCategories($catId = 0)
    {
        $cat = new BlogCategory($catId);
        $cat->loadFromDb();
        $category = $cat->getFlds();
        $brcmb = new Breadcrumb();
        $brcmb->add("Blog category Management", FatUtility::generateUrl('blogcategories'));
        $brcmb->add($category['category_title']);
        $this->set('breadcrumb', $brcmb->output());
        $frm = $this->getSearchForm();
        $this->set('frmCategory', $frm);
        $this->set('catId', $catId);

        $this->_template->render(true, true, 'blogcategories/index.php');
    }

    public function getCategory($categoryId = 0)
    {
        $categorySearch = BlogCategories::search();
        $categorySearch->addDirectCondition(BlogCategories::DB_TBL_PREFIX . 'id =' . $categoryId);
        $resultSet = $categorySearch->getResultSet();
        $record = (!empty($resultSet)) ? FatApp::getDb()->fetch($resultSet) : array();
        return $record;
    }

    public function form($categoryId = 0, $parent_category_id = 0)
    {
        $brcmb = new Breadcrumb();
        $brcmb->add("Blog category Management", FatUtility::generateUrl('blogcategories'));

        if (!$this->canEdit) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $blogCategory = new BlogCategory($categoryId);
        $categoryId = FatUtility::convertToType($categoryId, FatUtility::VAR_INT);
        $parent_category_id = FatUtility::convertToType($parent_category_id, FatUtility::VAR_INT);


        $form = $this->getForm($categoryId);
        $form->getField('category_parent')->value = $parent_category_id;

        if ($categoryId > 0) {

            if ($blogCategory->loadFromDb()) {

                $record = $this->getCategory($categoryId);
                $brcmb->add($record['category_title']);
                if (is_array($record) && count($record)) {
                    $form->fill($record);
                }
            }
        }
        $post = FatApp::getPostedData();
        if ($post != false) {
            $form->fill($post);
        }
        $this->set('breadcrumb', $brcmb->output());
        $this->set('form', $form);
        $this->set('categoryId', $categoryId);
        $this->_template->render(true, true, 'blogcategories/form.php');
    }

    public function getDisplayOrder()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $blogCategories = BlogCategories::search();
        $blogCategories->addFld('ifnull(max(' . BlogCategories::DB_TBL_PREFIX . 'display_order),0)+1 as display_order');
        $resultSet = $blogCategories->getResultSet();
        $record = (!empty($resultSet)) ? FatApp::getDb()->fetch($resultSet) : null;
        return !empty($record['display_order']) ? FatUtility::convertToType($record['display_order'], FatUtility::VAR_INT) + 1 : 1;
    }

    public function setup()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
			}
        $post = FatApp::getPostedData();
        $categoryId = FatUtility::convertToType($post[BlogCategories::DB_TBL_PREFIX . 'id'], FatUtility::VAR_INT);
        $form = $this->getForm($categoryId);
        if ($post == false) {
            Message::addErrorMessage($form->getValidationErrors());
            FatUtility::dieJsonError(Message::getHtml());
        } else {

            $blogCategory = new BlogCategory($categoryId);
            if ($categoryId == 0) {
                $post[BlogCategories::DB_TBL_PREFIX . 'display_order'] = $this->getDisplayOrder();
                $post[BlogCategories::DB_TBL_PREFIX . 'date_time'] = FatDate::nowInTimezone(date_default_timezone_get(), 'Y-m-d H:i:s');
            } else if ($categoryId > 0) {
                if (!empty($post['category_parent'])) {
                    $category_parent_code = $this->getCategoryCode($post['category_parent']);
                    $post['category_code'] = $category_parent_code . str_pad($categoryId, 5, "0", STR_PAD_LEFT);
                } else {
                    $assign_fields['category_code'] = str_pad($categoryId, 5, "0", STR_PAD_LEFT);
                }
            }
            if ($categoryId == 0) {
                unset($post['category_id']);
            }
            $blogCategory->assignValues($post);
            if ($categoryId = $blogCategory->save()) {
                if (empty($post['category_code'])) {
                    $category_parent_code = $this->getCategoryCode($post['category_parent']);
                    $assign_fields_category_code['category_code'] = $category_parent_code . str_pad($categoryId, 5, "0", STR_PAD_LEFT);
                }
                $blogCategory->assignValues($assign_fields_category_code);
                $blogCategory->save();
                $arrData = array(BlogCategory::DB_CHILD_TBL_PREFIX . 'record_id' => $categoryId, BlogCategory::DB_CHILD_TBL_PREFIX . 'record_type' => BlogConstants::BMETA_RECORD_TYPE_CATEGORY, BlogCategory::DB_CHILD_TBL_PREFIX .
                    'title' => $post[BlogCategory::DB_CHILD_TBL_PREFIX . 'title'], BlogCategory::DB_CHILD_TBL_PREFIX . 'keywords' => $post[BlogCategory::DB_CHILD_TBL_PREFIX . 'keywords'],
                    BlogCategory::DB_CHILD_TBL_PREFIX . 'description' => $post[BlogCategory::DB_CHILD_TBL_PREFIX . 'description'],
                    BlogCategory::DB_CHILD_TBL_PREFIX . 'others' => $post[BlogCategory::DB_CHILD_TBL_PREFIX . 'others'],
                    BlogCategory::DB_CHILD_TBL_PREFIX . 'id' => $post[BlogCategory::DB_CHILD_TBL_PREFIX . 'id']
                );

                if (!$blogCategory->saveMetaInfo($arrData)) {
                    Message::addErrorMessage(FatApp::getDb()->getError());
                    $this->form();
                    return;
                }

                if (!empty($post[BlogCategory::DB_TBL_PREFIX . 'id'])) {
                    $lblString = 'SUCCESS_BLOG_CATEGORY_UPDATED';
                } else {
                    $lblString = 'SUCCESS_BLOG_CATEGORY_SAVED';
                }
                Message::addMessage(Info::t_lang($lblString));
                FatApp::redirectUser(FatUtility::generateUrl('Blogcategories'));
            } else {
                Message::addErrorMessage($blogCategory->getError());
                $this->form();
                return;
            }
            Message::addErrorMessage(Info::t_lang('ERROR_SOMETHING_WENT_WRONG'));
            $this->form();
            return;
        }
    }

    public function getCategoryCode($category_id = 0)
    {
        $category_id = FatUtility::convertToType($category_id, FatUtility::VAR_INT);
        if ($category_id < 1) {
            return false;
        }
        $record = $this->getCategory($category_id);
        return !empty($record['category_code']) ? $record['category_code'] : '';
    }

    public function changeStatus()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        if (!FatUtility::isAjaxCall()) {
            Message::addErrorMessage(Info::t_lang('ERROR_INVALID_REQUEST'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post = FatApp::getPostedData();
        $categoryId = FatUtility::convertToType($post['category_id'], FatUtility::VAR_INT);
        $blogCategory = new BlogCategory($categoryId);
        if ($blogCategory->loadFromDb()) {
            $status = $blogCategory->getFldValue(BlogCategory::DB_TBL_PREFIX . 'status');
            if ($status == BlogConstants::ENTITY_ACTIVE) {
                $newStatus = BlogConstants::ENTITY_INACTIVE;
				$blogCategories = new BlogCategories($categoryId);
				$blogCategories->inactiveCatRelatedContent($categoryId);
            } else {
                $newStatus = BlogConstants::ENTITY_ACTIVE;
            }
            $blogCategory->setFlds(array(BlogCategory::DB_TBL_PREFIX . 'status' => $newStatus));
            if ($blogCategory->save()) {
                //Message::addMessage(Info::t_lang('STATUS_UPDATED'));
                FatUtility::dieJsonSuccess(Info::t_lang('STATUS_UPDATED'));
            }
        }
    }

    public function checkUniqueSeoName($data = array(), $num = 0)
    {
        static $seoName;
        if (count($data) > 0) {
            $post = $data;
        } else {
            if (!FatUtility::isAjaxCall()) {
                Message::addErrorMessage('Invalid Request');
                FatApp::redirectUser(FatUtility::generateUrl(''));
            }
            $post = FatApp::getPostedData();
            $seoName = $post['cat_seo_name'];
        }

        $categoryId = FatUtility::convertToType($post['category_id'], FatUtility::VAR_INT);

        $srch = BlogCategories::search();
        if ($categoryId >= 1) {
            $srch->addCondition('category_id', '!=', $categoryId);
        }
        $srch->addCondition('category_seo_name', '=', $post['cat_seo_name']);
        $srch->addFld('category_seo_name');
        $srch->doNotLimitRecords();

        $rs = $srch->getResultSet();        
        $total_records = $srch->recordCount();
        if ($total_records >= 1) {
            $num++;
            $post['cat_seo_name'] = $seoName . "-" . $num;
            $this->checkUniqueSeoName($post, $num);       
        }        
        $json['category_seo_name_']=$post['cat_seo_name'];
        FatUtility::dieJsonSuccess($json);
    }

    public function getForm($categoryId = 0)
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $categoryId = FatUtility::convertToType($categoryId, FatUtility::VAR_INT);
        $categoriesSearch = BlogCategories::search();
        $categoriesSearch->addCondition(BlogCategories::DB_TBL_PREFIX . 'id', '!=', $categoryId);
        $categoriesSearch->addCondition(BlogCategories::DB_TBL_PREFIX . 'status', '=', BlogConstants::ENTITY_ACTIVE);
        $categoriesSearch->addMultipleFields(array(BlogCategories::DB_TBL_PREFIX . 'id', BlogCategories::DB_TBL_PREFIX . 'title'));

        $categoriesArr = FatApp::getDb()->fetchAllAssoc($categoriesSearch->getResultSet());

        $form = new Form('from');
        $form->addHiddenField('Id', BlogCategory::DB_TBL_PREFIX . 'id', '0');
        $form->addRequiredField(Info::t_lang('CATEGORY_TITLE'), BlogCategory::DB_TBL_PREFIX . 'title');
        $form->addRequiredField(Info::t_lang('CATEGORY_SEO_NAME'), BlogCategory::DB_TBL_PREFIX . 'seo_name');
        $form->addTextarea(Info::t_lang('CATEGORY_DESCRIPTION'), BlogCategory::DB_TBL_PREFIX . 'description');
        $statusField = $form->addSelectBox(Info::t_lang('CATEGORY_STATUS'), BlogCategory::DB_TBL_PREFIX . 'status', Info::getStatus());
        $statusField->requirements()->setRequired();

        $form->addSelectBox(Info::t_lang('CATEGORY_PARENT'), BlogCategory::DB_TBL_PREFIX . 'parent', $categoriesArr);

        $form->addHiddenField('Meta Info Id', BlogCategory::DB_CHILD_TBL_PREFIX . 'id', '0');
        $form->addTextbox(Info::t_lang('META_TITLE'), BlogCategory::DB_CHILD_TBL_PREFIX . 'title');
        $form->addTextarea(Info::t_lang('META_KEYWORDS'), BlogCategory::DB_CHILD_TBL_PREFIX . 'keywords');
        $form->addTextarea(Info::t_lang('META_DESCRIPTION'), BlogCategory::DB_CHILD_TBL_PREFIX . 'description');
        $form->addTextarea(Info::t_lang('META_OTHERS'), BlogCategory::DB_CHILD_TBL_PREFIX . 'others');
        $form->addHTML('', 'note', Info::t_lang('PAGE_TEXT_NOTE:_META_OTHERS_ARE_HTML_META_TAGS_,_E.G_') . htmlentities(' <meta name="example" content="example" /> .') . Info::t_lang('PAGE_TEXT_WE_ARE_NOT_VALIDATING'));
        $form->addSubmitButton('', 'btn_submit', Info::t_lang('SUBMIT'));
        $form->addButton('', 'btn_cancel', Info::t_lang('CANCEL'));

        return $form;
    }

}
