<?php

class CmsController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array('listing', 'form', 'setup', 'cmsDisplaySetup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewCms($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditCms($this->admin_id);
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

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("CMS");
        $this->set('breadcrumb', $brcmb->output());
        $this->set('fatShortCodes', ApplicationConstants::fatShortCodes());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name', 'cms_name', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Cms::getSearchObject();
        if (!empty($post['cms_name'])) {
            $search->addCondition('cms_name', 'like', '%' . $post['cms_name'] . '%');
        }
        $search->joinTable("tbl_cms_positions", 'LEFT JOIN', 'cms_id = cmsposition_cms_id');
        $search->addOrder('cms_display_order', 'desc');
        $page = empty($page) || $page <= 0 ? 1 : $page;
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "cms/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function form() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $post['cms_id'] = empty($post['cms_id']) ? 0 : FatUtility::int($post['cms_id']);
        $form = $this->getForm($post['cms_id']);
        $tag_form = $this->getMetaTagForm($post['cms_id']);
        if (!empty($post['cms_id'])) {
            $fc = new Cms($post['cms_id']);
            if (!$fc->loadFromDb()) {
                FatUtility::dieWithError('Error! ' . $fc->getError());
            }
            $data = $fc->getFlds();
            $positions = $fc->getPosition($post['cms_id']);
            if (!empty($positions) && !empty($data)) {
                $data = array_merge($data, $positions);
            } elseif (!empty($positions)) {
                $data = $positions;
            }
            $meta = new MetaTags();
            $meta_data = MetaTags::getMetaTag( 'cms', 'view',$post['cms_id'], 0);
            $tag_form->fill($meta_data);
            $form->fill($data);
        }

        $adm = new Admin();
        $this->set("frm", $form);
        $this->set("tag_form", $tag_form);
        $htm = $this->_template->render(false, false, "cms/_partial/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getForm($record_id = 0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $action = 'Add';
        if ($record_id > 0) {
            $action = 'Update';
        }
        $frm = new Form('action_form', array('id' => 'action_form'));

        $frm->addHiddenField("", 'cms_id', $record_id);
        $frm->addHiddenField("", 'fIsAjax', 1);
        $text_area_id = 'text_area';
        $editor_id = 'editor_area';
        $fld = $frm->addRequiredField('Title', 'cms_name');
        $fld->developerTags['col'] = 12;
        if ($record_id > 0) {
            $slugHtml = '<a href="' . Route::getRoute('cms', 'view', array($record_id), true) . '?admin=true" target="_BLANK" >' . Route::getRoute('cms', 'view', array($record_id), true) . '</a>  <a class="button" onClick="editSlug(this)" data-record-id=' . $record_id . ' data-record-type=' . Route::CMS_ROUTE . '><i  class="ion-edit"></i></a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }
  


        $frm->addRequiredField('Sub Heading', 'cms_sub_heading')->developerTags['col'] = 6;
        $frm->addTextBox('Display order', 'cms_display_order')->developerTags['col'] = 6;


        $radio_btn = $frm->addRadioButtons('Show Banner', 'cms_show_banner', Info::getIs(), '0');
        $radio_btn->developerTags['col'] = 6;
        $radio_btn->setFieldTagAttribute('onclick', 'showImageTags()');

        $cms_image = $frm->addHtml('', 'cms_image', '<img src="' . FatUtility::generateUrl('image', 'cmsImage', array($record_id, 150, 150), CONF_BASE_DIR) . '">');

        $cms_image->developerTags['col'] = 6;
        $cms_image->setWrapperAttribute('class', 'cms-image');
        $photo = $frm->addFileUpload('', 'photo');
        $photo->developerTags['col'] = 6;
        $photo->setWrapperAttribute('class', 'cms-image');
        $photo->htmlAfterField = '(Size: 1600X900 or [aspect ratio 4:3])';

        $banner_content = $frm->addTextArea('Banner Content', 'cms_banner_content', '');
        $banner_content->setWrapperAttribute('class', 'cms-image');

        $frm->addTextArea('Content', 'cms_content', '', array('id' => $text_area_id))->htmlAfterField = '<div id="' . $editor_id . '"></div>' . MyHelper::getInnovaEditorObj($text_area_id, $editor_id);



        $frm->addSelectBox('Page Type', 'cms_type', Info::getCmsType(), '0', array(), '')->developerTags['col'] = 6;

        //$frm->addCheckBoxes('Positions','positions',Info::getCmsPositions())->developerTags['col']  = 6;

        $frm->addSelectBox('Status', 'cms_active', Info::getStatus())->developerTags['col'] = 6;

        $frm->setFormTagAttribute('action', FatUtility::generateUrl("cms", "setup"));
        $frm->setFormTagAttribute('onsubmit', 'submitForm(formValidator,"action_form"); return(false);');
        $frm->addSubmitButton('', 'btn_submit', $action, array('class' => 'themebtn btn-default btn-sm'))->htmlAfterField = "<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
        return $frm;
    }

    private function getMetaTagForm($cms_id) {
        $frm = new Form('meta_tag', array('id' => 'meta_form'));
        if (empty($cms_id)) {
            $frm->addHtml("", "", 'Please add CMS Page first.');
            return $frm;
        }
        $frm->addHiddenField('', 'meta_id');

        $frm->addHiddenField('', 'meta_record_id', $cms_id);


        $text_area_id = 'meta_tag_text_area';
        $editor_id = 'meta_tag_editor';
        $title = $frm->addTextBox('Title', 'meta_title');
        $title->requirements()->setRequired();
        $keyword->developerTags['fld_default_col'] = 6;
        $keyword = $frm->addTextArea('Keyword', 'meta_keywords');
        $keyword->requirements()->setRequired();
        $keyword->developerTags['fld_default_col'] = 6;
        $frm->addTextArea('Description', 'meta_description', '', array('id' => $text_area_id));


        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', 'Add/Update', array('class' => 'themebtn btn-default btn-sm'))->htmlAfterField = "<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
        }


        return $frm;
    }

    public function setup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getForm();
        $data = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $data) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
        $isEdit = $cmsId = FatApp::getPostedData('cms_id', FatUtility::VAR_INT);

        unset($data['cms_id']);
        $cms = new Cms($cmsId);
       
        if ($data['cms_show_banner'] == 1) {
            if ($cmsId <= 0 && empty($_FILES['photo']['tmp_name'])) {
                FatUtility::dieJsonError('Image is Mandatory Field');
            }
            $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_CMS_PHOTO, $cmsId);
            if (empty($img) && empty($_FILES['photo']['tmp_name'])) {
                FatUtility::dieJsonError('Image is Mandatory Field');
            }
        }

        $cms->assignValues($data);

        if (!$cms->save()) {
            FatUtility::dieWithError($cms->getError());
        }
        $cmsId = $cms->getMainTableRecordId();
//        if (!$cms->savePositions($cmsId, $data['positions'])) {
//            FatUtility::dieJsonError($cms->getError());
//            return;
//        }
        if ($data['cms_show_banner'] == 1) {
            $attach = new AttachedFile();

            if (!empty($_FILES['photo']['tmp_name']) && !$attach->saveAttachment($_FILES['photo']['tmp_name'], AttachedFile::FILETYPE_CMS_PHOTO, $cmsId, 0, $_FILES['photo']['name'], 0, true)) {
                FatUtility::dieJsonError('Something went Wrong. Please Try Again');
            }
        } else {
            AttachedFile::removeFiles(AttachedFile::FILETYPE_CMS_PHOTO, $cmsId);
        }

        if (!$isEdit) {
            $route = new Routes();
            $routeData = array(
                'url_rewrite_custom' => Info::getSlugFromName($data['cms_name']),
                'url_rewrite_record_id' => $cmsId,
                'url_rewrite_subrecord_id' => 0,
                'url_rewrite_record_type' => Route::CMS_ROUTE,
            );
            $route->createNewRoute($routeData);
        }


        $this->set('msg', 'CMS Setup Successful');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function metaTagAction() {

        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $post['meta_record_id'] = isset($post['meta_record_id']) ? FatUtility::int($post['meta_record_id']) : 0;
        $meta_id = isset($post['meta_id']) ? FatUtility::int($post['meta_id']) : 0;
        if (empty($post['meta_record_id'])) {
            FatUtility::dieJsonError('Invalid action!');
        }

        $form = $this->getMetaTagForm($post['meta_record_id']);
        $post = $form->getFormDataFromArray($post);
        if ($post === false) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $post[MetaTags::DB_TBL_PREFIX . 'controller'] = 'cms';
        $post[MetaTags::DB_TBL_PREFIX . 'action'] = 'view';
        $meta = new MetaTags($meta_id);
        $meta->assignValues($post);
        if (!$meta->save()) {
            FatUtility::dieJsonError('Something went Wrong. Please Try Again.');
        }
        FatUtility::dieJsonSuccess("Record updated!");
    }

    public function getSlug() {
        $data = FatApp::getPostedData();
        $name = !empty($data['cms_name']) ? $data['cms_name'] : '';
        $cms = new Cms();
        $slug = $cms->getValidSlug($name);
        FatUtility::dieJsonSuccess($slug);
    }

    public function cmsDisplaySetup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $data = FatApp::getPostedData();


        $cmsId = FatApp::getPostedData('cms_id', FatUtility::VAR_INT);
        unset($data['cms_id']);
        $cms = new Cms($cmsId);
        $cms->assignValues($data);

        if (!$cms->save()) {
            FatUtility::dieWithError($cms->getError());
        }

        $this->set('msg', 'Category Setup Successful');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function view($cms_id) {
        $cms_id = FatUtility::int($cms_id);
        if ($cms_id < 0) {
            FatUtility::dieJsonError('Something went wrong!');
        }
        $fc = new Cms($cms_id);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }

        $this->set('records', $fc->getFlds());
        $this->set('positions', $fc->getPosition($cms_id));
        $this->_template->render(false, false, "cms/_partial/view.php");
    }

}
