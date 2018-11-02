<?php

require_once CONF_UTILITY_PATH . "ImagegalleryTrait.php";
require_once CONF_UTILITY_PATH . "MetaTrait.php";

class CitiesController extends AdminBaseController {

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
        $this->canView = AdminPrivilege::canViewCity($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditCity($this->admin_id);
        if (!$this->canView) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Unauthorized Access!');
            }
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->imageType = AttachedFile::FILETYPE_CITY_IMAGE;
        $this->metaType = MetaTags::META_CITY;

        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $this->set('handlerName', 'cities');
    }

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("CITIES");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name', 'city_name', '', array('class' => 'search-input'));
		$frm->addSelectBox('Featured', 'city_featured', ApplicationConstants::getYesNoArray());
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = City::getSearchObject();
		$search->joinTable(Activity::DB_TBL, "LEFT JOIN", CITY::DB_TBL_PREFIX . "id = " . Activity::DB_TBL_PREFIX . "city_id AND ".Activity::DB_TBL_PREFIX."state >=2");

		
        $search->addOrder(City::DB_TBL_PREFIX . 'display_order', 'asc');
		$search->addGroupBy('city_id');
		$search->addMultipleFields(array('count(DISTINCT activity_id) as activities','tbl_cities.*'));
		
		
        if (!empty($post['city_name'])) {
            $search->addCondition('city_name', 'like', '%' . $post['city_name'] . '%');
        }
		
		if (isset($post['city_featured']) && $post['city_featured'] != '') {
            $search->addCondition('city_featured', '=', $post['city_featured']);
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
        $htm = $this->_template->render(false, false, "cities/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function setup($record_id = 0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $this->_template->addJs(array('js/custom-tabs.js'));
        $brcmb = new Breadcrumb();
        $city_id = FatUtility::int($record_id);

        $data = Cities::getAttributesById($city_id);
        /* Set Breadcrumb */
        $brcmb->add("Cities", FatUtility::generateUrl('cities'));
        if (isset($data['city_name'])) {
            $brcmb->add($data['city_name']);
        } else {
            $brcmb->add("Add New");
        }
        $this->set('breadcrumb', $brcmb->output());

        $this->set("cityId", $record_id);
        $this->_template->render();
    }

    public function info($cityId = 0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $data = array();
        $cityId = intval($cityId);
        if ($cityId > 0) {
            $data = Cities::getAttributesById($cityId);
        }
        $form = $this->getForm($cityId);
        $form->fill($data);
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "cities/_partial/form.php", true, true);
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
        $frm->addHiddenField("", 'city_id', $record_id, array('id' => 'city_id'));
        $fld = $frm->addRequiredField('Name', 'city_name');
        $fld->developerTags['col'] = 6;
        if ($record_id > 0) {
            $slugHtml = '<a href="' . Route::getRoute('city', 'details', array($record_id), true) . '?admin=true" target="_BLANK" >' . Route::getRoute('city', 'details', array($record_id), true) . '</a>  <a class="button" onClick="editSlug(this)" data-record-id=' . $record_id . ' data-record-type=' . Route::CITY_ROUTE . '><i  class="ion-edit"></i></a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }

        $frm->addSelectBox('Country', 'city_country_id', Countries::getCountries())->developerTags['col'] = 6;
        $frm->addTextArea('Details', 'city_detail')->developerTags['col'] = 12;
        $frm->addTextBox('Display order', 'city_display_order')->developerTags['col'] = 6;
        $frm->addSelectBox('Status', 'city_active', Info::getStatus())->developerTags['col'] = 6;
        $frm->setFormTagAttribute('action', FatUtility::generateUrl("cities", "saveinfo"));
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
        $cityId = FatApp::getPostedData('city_id', FatUtility::VAR_INT, 0);
        if ($cityId > 0) {
            $reload = false;
        }
        unset($data['city_id']);
	
        $error = null;
        $latLong = Helper::getCityCoordinates($data['city_name'], $error);

        if (!$latLong) {
            FatUtility::dieJsonError($error);
        }
        $data['city_latitude'] = $latLong['lat'];
        $data['city_longitude'] = $latLong['lng'];

        $city = new Cities($cityId);
        $city->assignValues($data);
        if (!$city->save()) {
			
            FatUtility::dieWithError($city->getError());
        }
	
        if ($cityId < 1) {
            $route = new Routes();
            $routeData = array(
                'url_rewrite_custom' => Info::getSlugFromName($data['city_name']),
                'url_rewrite_record_id' => $city->getMainTableRecordId(),
                'url_rewrite_subrecord_id' => 0,
                'url_rewrite_record_type' => Route::CITY_ROUTE,
            );
            $route->createNewRoute($routeData);
			
        }


        $this->set('msg', 'City Setup Successfull');
        $this->set('reload', $reload);
        $this->set('recordId', $city->getMainTableRecordId());
        $this->_template->render(false, false, 'json-success.php');
    }

    public function setFeatured() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $status = FatUtility::int($post['status']);
        $city_id = FatUtility::int($post['city_id']);
        if ($city_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $status = $status <= 0 || $status > 1 ? 0 : $status;
        $cities = new Cities($city_id);
        $data[Cities::DB_TBL_PREFIX . 'featured'] = $status;
        $cities->assignValues($data);
        if (!$cities->save()) {
            FatUtility::dieJsonErrorError($cities->getError());
        }
        FatCache::delete(CACHE_HOME_FEATURED_CITIES);
        FatUtility::dieJsonSuccess('Record Updated!');
    }

    public function cityDisplaySetup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $data = FatApp::getPostedData();
        if (false === $data) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $cityId = FatApp::getPostedData('city_id', FatUtility::VAR_INT);
        unset($data['city_id']);
        $city = new Cities($cityId);
        $city->assignValues($data);
        if (!$city->save()) {
            FatUtility::dieWithError($city->getError());
        }
        $this->set('msg', 'City Setup Successfull');
        $this->_template->render(false, false, 'json-success.php');
    }

}
