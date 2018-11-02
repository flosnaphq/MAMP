<?php

require_once CONF_UTILITY_PATH . "PaginateTrait.php";

class MetaTagsController extends AdminBaseController {

    use PaginateTrait;

    protected $canView;
    protected $canEdit;
    protected $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array();
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewMetaTags($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditMetaTags($this->admin_id);

        if (!$this->canView) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);

        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $this->setPaginateSettings();
    }

    function metaForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $meta_id = isset($post['meta_id']) ? $post['meta_id'] : 0;
        $frm = $this->getMetaTagForm();

        if ($meta_id > 0) {
            $meta = new MetaTags();
            $data = $meta->getMetaTagByRecordId($meta_id);

            if ($data) {
                $params = array();
                if (intval($data['meta_record_id']) > 0) {
                    array_unshift($params, $data['meta_record_id']);
                }
                if (intval($data['meta_subrecord_id']) > 0) {
                    array_unshift($params, $data['meta_subrecord_id']);
                }

                $data['page_url'] = Route::getRoute($data['meta_controller'], $data['meta_action'], $params, true);
            }
            $frm->fill($data);
        }

        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'meta-tags/_partial/meta-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getMetaTagForm($record_id = 0) {
        $frm = new Form('meta_tag', array('id' => 'meta_form'));


        $frm->addHiddenField('', 'meta_id');
        $frm->addHiddenField('', 'meta_record_id', $record_id);
        $text_area_id = 'meta_tag_text_area';

        $pageUrl = $frm->addTextBox('Page Url', 'page_url');
        $pageUrl->developerTags['fld_default_col'] = 6;
        $pageUrl->requirements()->setRequired();
        $title = $frm->addTextBox('Title', 'meta_title');
        $keyword->developerTags['fld_default_col'] = 6;
        $title->requirements()->setRequired();
        $keyword->developerTags['fld_default_col'] = 6;
        $keyword = $frm->addTextArea('Keyword', 'meta_keywords');
        $keyword->requirements()->setRequired();
        $keyword->developerTags['fld_default_col'] = 6;
        $frm->addTextArea('Description', 'meta_description', '', array('id' => $text_area_id));
        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', 'Add/Update', array('class' => 'themebtn btn-default btn-sm','onSubmit'=>'submitForm()'));
        }
        return $frm;
    }

    public function getExactRoute($metaUrl, &$controller, &$action, &$queryString) {

        $parsedUrl = parse_url($metaUrl);

        if (!isset($parsedUrl['host'])) {
            FatUtility::dieJsonError('Invalid Url!');
        }
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : "home/index";

        $UrlsParts = explode("/", trim($path, "/"));

        $controller = $UrlsParts[0];
        $action = $UrlsParts[1];

        $queryString = array();
        CustomRouter::getExactRoute($path, $controller, $action, $queryString);
    }

    public function metaTagAction() {

        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getMetaTagForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false == $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }

        $metaUrl = $post['page_url'];
        $controller = "";
        $action = "";
        $queryString = array();
        $this->getExactRoute($metaUrl, $controller, $action, $queryString);

        $post['meta_controller'] = $controller;
        $post['meta_action'] = $action;
        $post['meta_record_id'] = isset($queryString[0]) ? intval($queryString[0]) : 0;
        $post['meta_subrecord_id'] = isset($queryString[1]) ? intval($queryString[1]) : 0;

        $data = MetaTags::getMetaTag($controller, $action, $post['meta_record_id'], $post['meta_subrecord_id']);
        $metaId = 0;
        if ($data) {
            $metaId = $data['meta_id'];
        }

        $meta = new MetaTags($metaId);
        $meta->assignValues($post);
        if (!$meta->save()) {
            FatUtility::dieJsonError('Something went Wrong. Please Try Again.');
        }
        FatUtility::dieJsonSuccess("Record updated!");
    }

    /*
     *  List and Search Functinality
     */

    private function getSearchForm() {
        $act = new Activity();
   
        $frm = new Form('frmReviewSearch', array('class' => 'web_form', 'onsubmit' => 'search(this,1); return false;'));
        $frm->addTextBox('Keyword', 'keyword', '', array('class' => 'search-input'));

        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        return $frm;
    }

    public function index() {

        $brcmb = new Breadcrumb();
        $brcmb->add("Meta Management");
        $frm = $this->getSearchForm();
        $this->set('breadcrumb', $brcmb->output());
        $this->set('search', $frm);
        $this->_template->render();
    }

    public function setPaginateSettings() {
        $this->pageSize = self::PAGESIZE;
        $this->paginateSorting = true;
    }

    public function getSearchObject($page) {

        $src = MetaTags::getSearchObject();
        $src->setPageSize($this->pageSize);
        $src->setPageNumber($page);
        return $src;
    }

    public function addFilters(&$srch, $data) {
        if ($data['keyword']) {
            $controller = "";
            $action = "";
            $queryString = array();
            $this->getExactRoute($data['keyword'], $controller, $action, $queryString);
            $srch->addCondition('meta_controller', '=', $controller);
            $srch->addCondition('meta_action', '=', $action);
        }
    }

    public function listFields() {

        $fields = array(
            'meta_controller' => 'Meta',
            'action' => 'Action',
        );
        return $fields;
    }

    public function sortFields() {
        
    }

    public function getTableRow(&$tr, $arr_flds, $row) {
        foreach ($arr_flds as $key => $val) {
            $td = $tr->appendElement('td');
            switch ($key) {
                case 'meta_controller':
                    $controller = !empty($row['meta_controller']) ? $row['meta_controller'] : 'home';
                    $action = !empty($row['meta_action']) ? $row['meta_action'] : 'index';
                    $recordId = !empty($row['meta_record_id']) ? $row['meta_record_id'] : 0;
                    $subrecordId = !empty($row['meta_subrecord_id']) ? $row['meta_subrecord_id'] : 0;
                    $params = array();
                    if (intval($recordId) > 0) {
                        array_unshift($params, $recordId);
                    }
                    if (intval($subrecordId) > 0) {
                        array_unshift($params, $subrecordId);
                    }

                    $td->appendElement('a', array('href' => Route::getRoute($controller, $action, $params, true), 'target' => '_BLANK'), Route::getRoute($controller, $action, $params, true));
                    break;
                case 'action':
                    $ul = $td->appendElement("ul", array("class" => "actions"));
                    if ($this->canEdit) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => "javascript:;", 'class' => 'button small green', 'title' => 'Edit', "onclick" => "getMetaForm(" . $row['meta_id'] . ")"), '<i class="ion-edit icon"></i>', true);
                    }

                    break;
            }
        }
    }

}

?>