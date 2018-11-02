<?php

class ServiceController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array('listing', '	', 'setup', 'serviceDisplaySetup');
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewService($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditService($this->admin_id);
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
        $brcmb->add("THEMES");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function services($service_parent_id) {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("Themes", FatUtility::generateUrl('service'));
        $serv = new Service($service_parent_id);
        if (!$serv->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $serv->getError());
        }

        $record = $serv->getFlds();
        $brcmb->add($record['service_name']);
        $this->set('breadcrumb', $brcmb->output());
        $this->set('pservice_name', $record['service_name']);
        $this->set('service_parent_id', $service_parent_id);
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name', 'service_name', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $service_parent = $data['service_parent_id'];
        $post = $searchForm->getFormDataFromArray($data);

        $search = Service::getSearchObject();
        //	$search->joinTable("tbl_attached_files","left join","service_id = afile_record_id and afile_physical_path =".AttachFile::FILETYPE_SERVICE_PHOTO);
        if (!empty($post['service_name'])) {
            $search->addCondition('service_name', 'like', '%' . $post['service_name'] . '%');
        }
        $search->addCondition('service_parent_id', '=', $service_parent);
        $search->addOrder('service_display_order', 'desc');
        $page = empty($page) || $page <= 0 ? 1 : $page;
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set("service_parent_id", $service_parent);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "service/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function form() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $post['service_id'] = empty($post['service_id']) ? 0 : FatUtility::int($post['service_id']);
        $service_parent_id = empty($post['service_parent_id']) ? 0 : FatUtility::int($post['service_parent_id']);
        $form = $this->getForm($post['service_id'],$service_parent_id);
        if (!empty($post['service_id'])) {
            $fc = new Service($post['service_id']);
            if (!$fc->loadFromDb()) {
                FatUtility::dieWithError('Error! ' . $fc->getError());
            }
            $form->fill($fc->getFlds());
        } else {
            $form->fill(array('service_parent_id' => $service_parent_id));
        }

        $adm = new Admin();
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "service/_partial/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getForm($record_id = 0,$parentId=0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $action = 'Add';
        if ($record_id > 0) {
            $action = 'Update';
        }
        $frm = new Form('action_form', array('id' => 'action_form'));

        $frm->addHiddenField("", 'service_id', $record_id);
        $text_area_id = 'text_area';
        $editor_id = 'editor_area';
        $fld = $frm->addRequiredField('Title', 'service_name');
        $fld->developerTags['col'] = 6;
                //Add Slug Functionality
        if ($record_id > 0 && $parentId==0) {
            $slugHtml = '<a href="' . Route::getRoute('services', 'index', array($record_id), true) . '?admin=true" target="_BLANK" >' . Route::getRoute('services', 'index', array($record_id), true) . '</a>  <a class="button" onClick="editSlug(this)" data-record-id=' . $record_id . ' data-record-type=' . Route::ACTIVITYTYPE_ROUTE . '><i  class="ion-edit"></i></a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }
        $frm->addTextBox('Display order', 'service_display_order')->developerTags['col'] = 6;
        
        $frm->addTextArea('Content', 'service_description', '', array('id' => $text_area_id))->htmlAfterField = '<div id="' . $editor_id . '"></div>' . MyHelper::getInnovaEditorObj($text_area_id, $editor_id);

        $frm->addSelectBox('Status', 'service_active', Info::getStatus())->developerTags['col'] = 6;
        ;
        $filefld = $frm->addFileUpload("Image", "service_image");
        $filefld->developerTags['col'] = 4;
		$filefld->htmlAfterField = 'Max Image Size ' . Helper::maxFileUpload(true);

		if ($record_id) {
			$html = '<div class="photosquare img-container"><img alt="" src="'. FatUtility::generateUrl("image", "service", array($record_id, 75, 75), CONF_BASE_DIR) . "?" . Info::timestamp().'"> <a class="crossLink" href="javascript:void(0)" onclick="removeImage(' . $record_id . ')"></a></div>';
			$frm->addHtml("", "", $html)->developerTags['col'] = 2;	
		}
		
        $frm->addHiddenField("", "service_parent_id");
        $frm->setFormTagAttribute('action', FatUtility::generateUrl("service", "setup"));
        $frm->setFormTagAttribute('onsubmit', 'submitForm(formValidator,"action_form"); return(false);');
        $frm->addSubmitButton('', 'btn_submit', $action, array('class' => 'themebtn btn-default btn-sm'))->htmlAfterField = "<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
        return $frm;
    }

    public function setup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }

        $frm = $this->getForm();
        $post = FatApp::getPostedData();
        $parent = $post['service_parent_id'];
       
        $data = $frm->getFormDataFromArray($post);
        $data['service_parent_id'] = $parent;
        if (false === $data) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
       $isEdit = $serviceId = FatApp::getPostedData('service_id', FatUtility::VAR_INT);
        unset($data['service_id']);
        $service = new Service($serviceId);
        $service->assignValues($data);

        if (!($result = $service->save())) {

            FatUtility::dieJsonError($service->getError());
        } else {

            $serviceId = $service->getMainTableRecordId();
            if (isset($_FILES) && !empty($_FILES)) {
                if (is_uploaded_file($_FILES['service_image']['tmp_name'])) {
                    if ($_FILES['service_image']['size'] > Helper::maxFileUpload(true, false)) {
                        FatUtility::dieJsonError('Max image upload size is: ' . Helper::maxFileUpload(true));
                    } else {
                        $attachment = new AttachedFile();
                        if (!$attachment->saveImage($_FILES['service_image']['tmp_name'], AttachedFile::FILETYPE_SERVICE_PHOTO, $serviceId, 0, $_FILES['service_image']['name'], 0, true)) {
                            $this->set('msg', $attachment->getError());
                        }
                    }
                } else {
                    FatUtility::dieJsonError('Inavlid Image');
                }
            }
        }
       if ($isEdit < 1 && $parent==0) {
             $route = new Routes();
            $routeData = array(
                'url_rewrite_custom' => Info::getSlugFromName($data['service_name']),
                'url_rewrite_record_id' => $service->getMainTableRecordId(),
                'url_rewrite_subrecord_id' => 0,
                'url_rewrite_record_type' => Route::ACTIVITYTYPE_ROUTE,
            );
            $route->createNewRoute($routeData);
           
           
       }
        $this->set('msg', 'Themes Setup Successful');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function serviceDisplaySetup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $data = FatApp::getPostedData();

        $serviceId = FatApp::getPostedData('service_id', FatUtility::VAR_INT);
        unset($data['service_id']);
        $service = new Service($serviceId);
        $service->assignValues($data);

        if (!$service->save()) {
            FatUtility::dieWithError($service->getError());
        }

        $this->set('msg', 'Display Order Changed Successful');
        $this->_template->render(false, false, 'json-success.php');
    }

    public function view($service_id) {
        $service_id = FatUtility::int($service_id);
        if ($service_id < 0) {
            FatUtility::dieJsonError('Something went wrong!');
        }
        $fc = new Service($service_id);
        if (!$fc->loadFromDb()) {
            FatUtility::dieWithError('Error! ' . $fc->getError());
        }
        $this->set('records', $fc->getFlds());
        $this->_template->render(false, false, "service/_partial/view.php");
    }

    private function removeServiceImage($service_id) {
        Helper::deleteMultipleAttachedFile(AttachedFile::FILETYPE_SERVICE_PHOTO, $service_id);
        return true;
    }

    public function removeImage() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $data = FatApp::getPostedData();
        $this->removeServiceImage($data['service_id']);
        FatUtility::dieJsonSuccess('Image Removed');
    }

    public function setFeatured() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $status = FatUtility::int($post['status']);
        $service_id = FatUtility::int($post['service_id']);
        if ($service_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $status = $status <= 0 || $status > 1 ? 0 : $status;
        $service = new Service($service_id);
        $data[Service::DB_TBL_PREFIX . 'featured'] = $status;
        $service->assignValues($data);

        if (!$service->save()) {
            FatUtility::dieJsonError($service->getError());
        }
        FatUtility::dieJsonSuccess('Record Updated!');
    }

}
