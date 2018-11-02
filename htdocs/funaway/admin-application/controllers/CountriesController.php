<?php

require_once CONF_UTILITY_PATH . "ImagegalleryTrait.php";
require_once CONF_UTILITY_PATH . "MetaTrait.php";

class CountriesController extends AdminBaseController {

    use Imagegallery,
        MetaTrait;

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array('listing');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            FatUtility::dieWithError("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewLocation($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditLocation($this->admin_id);
        if (!$this->canView) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Unauthorized Access!');
            }
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->imageType = AttachedFile::FILETYPE_COUNTRY_IMAGE;
        $this->metaType = MetaTags::META_COUNTRY;

        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $this->set('handlerName', 'countries');
    }

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("COUNTRIES");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name', 'country_name', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Countries::getSearchObject();
        if (!empty($post['country_name'])) {
            $search->addCondition('country_name', 'like', '%' . $post['country_name'] . '%');
        }
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
        $htm = $this->_template->render(false, false, "countries/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function setup($record_id = 0)
	{
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $this->_template->addJs(array('js/custom-tabs.js'));
        $brcmb = new Breadcrumb();
        $country_id = FatUtility::int($record_id);
        $data = Countries::getAttributesById($country_id);
        /* Set Breadcrumb */
        $brcmb->add("Countries", FatUtility::generateUrl('Countries'));
        if (isset($data['country_name'])) {
            $brcmb->add($data['country_name']);
        } else {
            $brcmb->add("Add New");
        }
        $this->set('breadcrumb', $brcmb->output());
        $this->set("countryId", $country_id);
        $this->_template->render();
    }

    public function info($countryId = 0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $countryId = intval($countryId);
        $data = array();
        if ($countryId > 0) {
            $data = Countries::getAttributesById($countryId);
        }
        $form = $this->getForm($countryId);
        $form->fill($data);
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "countries/_partial/form.php", true, true);
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
        $frm->addHiddenField("", 'country_id', $record_id, array('id' => 'country_id'));
        $fld = $frm->addRequiredField('Name', 'country_name');

        //Add Slug Functionality
        if ($record_id > 0) {
            $slugHtml = '<a href="' . Route::getRoute('countries', 'details', array($record_id), true) . '?admin=true" target="_BLANK" >' . Route::getRoute('countries', 'details', array($record_id), true) . '</a>  <a class="button" onClick="editSlug(this)" data-record-id=' . $record_id . ' data-record-type=' . Route::COUNTRY_ROUTE . '><i  class="ion-edit"></i></a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }

        $fld->developerTags['col'] = 6;
        $fld->setUnique('tbl_countries', 'country_name', 'country_id', 'country_id', 'country_id');
        $fld = $frm->addRequiredField('Phone Code', 'country_phone_code');
        $fld->developerTags['col'] = 6;
        $fld->setUnique('tbl_countries', 'country_phone_code', 'country_id', 'country_id', 'country_id');
		$fld->requirements()->setLength(1, 5);
        $frm->addTextArea('Details', 'country_detail')->developerTags['col'] = 12;
        $region = $frm->addSelectBox('Region', 'country_region_id', Regions::getAllNames());
		$region->requirements()->setRequired();
		$region->developerTags['col'] = 6;

        $frm->addSelectBox('Status', 'country_active', Info::getStatus())->developerTags['col'] = 6;
        $frm->setFormTagAttribute('action', FatUtility::generateUrl("countries", "saveinfo"));
		$frm->setValidatorJsObjectName('formValidator');
        $frm->setFormTagAttribute('onsubmit', ' jQuery.fn.submitForm(formValidator,"action_form",successCallback); return(false);');
        $frm->addSubmitButton('', 'btn_submit', $action, array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function saveinfo() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getForm();
        $data = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $data) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
        $reload = true;
        $countryId = FatApp::getPostedData('country_id', FatUtility::VAR_INT);
        if ($countryId > 0) {
            $reload = false;
        }


        unset($data['country_id']);
        $country = new Countries($countryId);
        $country->assignValues($data);

        if (!$country->save()) {
            FatUtility::dieWithError($country->getError());
        }

        if ($countryId < 1) {
            $route = new Routes();
            $routeData = array(
                'url_rewrite_custom' => Info::getSlugFromName($data['country_name']),
                'url_rewrite_record_id' => $country->getMainTableRecordId(),
                'url_rewrite_subrecord_id' => 0,
                'url_rewrite_record_type' => Route::COUNTRY_ROUTE,
            );
            $route->createNewRoute($routeData);
            
        }


        $this->set('msg', 'Country Setup Successful');
        $this->set('reload', $reload);
        $this->set('recordId', $country->getMainTableRecordId());
        $this->_template->render(false, false, 'json-success.php');
    }

}
