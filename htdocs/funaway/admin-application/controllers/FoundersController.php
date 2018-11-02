<?php

class FoundersController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array('listing', 'form', 'setup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewFounder($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditFounder($this->admin_id);
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
        $brcmb->add("Founders");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Keyword', 'keyword', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Founders::getSearchObject();
        if (!empty($post['keyword'])) {
            $con = $search->addCondition('founder_name', 'like', '%' . $post['keyword'] . '%', 'or');
            $con->attachCondition('founder_designation', 'like', '%' . $post['keyword'] . '%', 'or');
        }
        $search->addOrder('founder_name');
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
        $htm = $this->_template->render(false, false, "founders/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function form() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $post['founder_id'] = empty($post['founder_id']) ? 0 : FatUtility::int($post['founder_id']);
        $form = $this->getForm($post['founder_id']);
        if (!empty($post['founder_id'])) {
            $fc = new Founders($post['founder_id']);
            if (!$fc->loadFromDb()) {
                FatUtility::dieWithError('Error! ' . $fc->getError());
            }
            $form->fill($fc->getFlds());
        }

        $adm = new Admin();
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "founders/_partial/form.php", true, true);
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

        $frm->addHiddenField("", 'founder_id', $record_id);
        $frm->addHiddenField("", 'fIsAjax', 1);
        $text_area_id = 'text_area';
        $editor_id = 'editor_area';
        $frm->addHtml('', 'show_image', "<img src='" . FatUtility::generateUrl('image', 'founder', array($record_id, 50, 50, rand(100, 1000)), CONF_WEBROOT_URL) . "'>")->developerTags['col'] = 6;
        $frm->addFileUpload('Image', 'image')->developerTags['col'] = 6;
        $frm->addRequiredField('Name', 'founder_name')->developerTags['col'] = 6;
        $frm->addRequiredField('Designation', 'founder_designation')->developerTags['col'] = 6;
        $frm->addTextArea('Content', 'founder_content', '', array('id' => $text_area_id))->htmlAfterField = '<div id="' . $editor_id . '"></div>' . MyHelper::getInnovaEditorObj($text_area_id, $editor_id);
        $frm->addFloatField('Display Order', 'founder_display_order')->developerTags['col'] = 6;
        $frm->addSelectBox('Status', 'founder_active', Info::getStatus())->developerTags['col'] = 6;

        $frm->addSubmitButton('', 'btn_submit', $action, array())->htmlAfterField = "<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
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
        if (!empty($_FILES['image']['tmp_name']) && !is_uploaded_file($_FILES['image']['tmp_name'])) {
            FatUtility::dieJsonError('Image couldn\'t not uploaded.');
        }
        $founder_id = FatApp::getPostedData('founder_id', FatUtility::VAR_INT);
        unset($data['founder_id']);
        $founder = new Founders($founder_id);
        $founder->assignValues($data);

        if (!$founder->save()) {
            FatUtility::dieWithError($founder->getError());
        }
        $founder_id = $founder->getMainTableRecordId();
        if (!empty($_FILES['image']['tmp_name'])) {
            $attachment = new AttachedFile();
            if (!$attachment->uploadAndSaveFile('image', AttachedFile::FILETYPE_FOUNDER_PHOTO, $founder_id, 0, 0, true)) {
                FatUtility::dieJsonError($attachment->getError());
            }
        }
        $this->set('msg', 'Founder Setup Successful');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function displayOrderSetup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $founder_id = FatApp::getPostedData('founder_id', FatUtility::VAR_INT);
        $data['founder_display_order'] = @$post['display_order'];
        $founder = new Founders($founder_id);
        $founder->assignValues($data);

        if (!$founder->save()) {
            FatUtility::dieWithError($founder->getError());
        }
        $this->set('msg', 'Display Order Changed');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function view($founder_id) {
        $founder_id = FatUtility::int($founder_id);
        if ($founder_id < 0) {
            FatUtility::dieJsonError('Something went wrong!');
        }
        $fc = new Founders($founder_id);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }
        $this->set('records', $fc->getFlds());
        $this->_template->render(false, false, "founders/_partial/view.php");
    }

}
