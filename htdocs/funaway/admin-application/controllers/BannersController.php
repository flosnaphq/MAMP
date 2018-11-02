<?php

#error_reporting(E_ERROR);

class BannersController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;
    // public $urlRegExp = "/(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
    public $urlRegExp = "(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})";

    public function __construct($action) {
        $ajaxCallArray = array('adLists', 'requestLists', 'adAction', 'changeDisplayOrder');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewBanners($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditBanners($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }

        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {
        $brcmb = new Breadcrumb();
        $brcmb->add("Banner");
        $search = $this->getSearchForm();
        $this->set('breadcrumb', $brcmb->output());
        $this->set("search", $search);
        $this->_template->render();
    }

    public function lists($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Banner::getSearchObject();
        if (!empty($post['keyword'])) {
            $search_con = $search->addCondition('banner_title', 'like', '%' . $post['keyword'] . '%');
            $search_con->attachCondition('banner_subtitle', 'like', '%' . $post['keyword'] . '%');
            $search_con->attachCondition('banner_text', 'like', '%' . $post['keyword'] . '%');
        }
        if (isset($post['banner_active']) && $post['banner_active'] >= -1 && $post['banner_active'] != '') {
            $search_con = $search->addCondition('banner_active', '=', $post['banner_active']);
        }

        $page = empty($page) || $page <= 0 ? 1 : $page;
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $search->addOrder('banner_title');
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "banners/_partial/lists.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function view($banner_id) {
        $banner_id = FatUtility::int($banner_id);
        if ($banner_id < 0) {
            FatUtility::dieJsonError('Something went wrong!');
        }
        $fc = new Banner($banner_id);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }
        $this->set('records', $fc->getFlds());
        $this->_template->render(false, false, "banners/_partial/view.php");
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Keyword', 'keyword', '', array('class' => 'search-input'));
        $status = Info::getStatus();
        $status['-1'] = 'Does not Matter';
        $frm->addSelectBox('Status', 'banner_active', $status, '-1', array('class' => 'search-input'), '');
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array());
        return $frm;
    }

    public function form() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $post['banner_id'] = empty($post['banner_id']) ? 0 : FatUtility::int($post['banner_id']);
        $banner_id = $post['banner_id'];
        $form = $this->getForm($banner_id);
        if ($banner_id) {
            $banner = new Banner($banner_id);
            if (!$banner->loadFromDb()) {
                FatUtility::dieJsonError('Error! ' . $banner->getError());
            }
            $form->fill($banner->getFlds());
        }
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "banners/_partial/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function setup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }


        if (!empty($_FILES['image']['tmp_name']) && !is_uploaded_file($_FILES['image']['tmp_name'])) {
            FatUtility::dieJsonError('Image couldn\'t not uploaded.');
        } elseif (!empty($_FILES['image']['tmp_name'])) {
            $size = getimagesize($_FILES['image']['tmp_name']);
            if ($size[0] < 1600 || $size[1] < 900) {
                FatUtility::dieJsonError(Info::t_lang("Banner_image_size"));
            }
        }
        $post = FatApp::getPostedData();
        $post['banner_id'] = isset($post['banner_id']) ? FatUtility::int($post['banner_id']) : 0;
        $form = $this->getForm($post['banner_id']);
        $post = $form->getFormDataFromArray($post);
        if ($post === false) {
            FatUtility::dieJsonError($form->getValidationErrors());
        }
        if (empty($_FILES['image']['tmp_name']) && empty($post['banner_id'])) {
            FatUtility::dieJsonError('Image is mandatory. field');
        }
        $banner = new Banner($post['banner_id']);
        unset($post['banner_id']);
        $banner->assignValues($post);
        if (!$banner->save()) {
            FatUtility::dieJsonError($banner->getError());
        }
        $banner_id = $banner->getMainTableRecordId();
        FatCache::delete(CACHE_HOME_PAGE_BANNERS);
        if (!empty($_FILES['image']['tmp_name'])) {
            $attachment = new AttachedFile();
            if (!$attachment->uploadAndSaveFile('image', AttachedFile::FILETYPE_BANNER_PHOTO, $banner_id, 0, 0, true)) {
                FatUtility::dieJsonError($attachment->getError());
            }
        }
        FatUtility::dieJsonSuccess('Setup Successful');
    }

    public function changeDisplayOrder() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $data = FatApp::getPostedData();


        $banner_id = FatApp::getPostedData('record_id', FatUtility::VAR_INT);
        unset($data['banner_id']);
        $data['banner_display_order'] = isset($data['display_order']) ? $data['display_order'] : 0;
        $banner = new Banner($banner_id);
        $banner->assignValues($data);

        if (!$banner->save()) {
            FatUtility::dieWithError($banner->getError());
        }

        $this->set('msg', 'Display Order Change Successful');
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($banner_id) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $action = 'Add';

        $frm = new Form('action_form');
        $frm->addHtml('', 'old_image', "<img src='" . FatUtility::generateUrl('image', 'banner', array($banner_id, 100, 100), CONF_BASE_DIR) . "' >");
        $frm->addFileUpload('Image', 'image');

        $frm->addHiddenField("", 'banner_id');
        $frm->addRequiredField('Title', 'banner_title');

        $frm->addTextBox('Sub Title', 'banner_subtitle', '');
        $linkFld = $frm->addTextBox('Banner Link', 'banner_link');
        $linkFld->requirements()->setRegularExpressionToValidate($this->urlRegExp);
        $linkFld->htmlAfterField = '( Example: http://example.com)';
        $frm->addTextArea('Text', 'banner_text');
        $frm->addTextBox('Display Order', 'banner_display_order');
        $frm->addSelectBox('Status', 'banner_active', Info::getStatus(), '', array(), '');
        $frm->addSubmitButton('', 'btn_submit', 'Add / Updated');

        return $frm;
    }

    public function remove() {
        $post = FatApp::getPostedData();
        $banner = new Banner($post['banner_id']);
        $banner->deleteRecord();
        FatUtility::dieJsonSuccess('Banner is removed successfully.');
    }

}
