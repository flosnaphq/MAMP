<?php

class RegionsController extends AdminBaseController {

    private $canView;
    private $admin_id;

    public function __construct($action) {
        
        $ajaxCallArray = array('listing', 'form', 'setup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewRegion($this->admin_id);
        
        if (!$this->canView) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Unauthorized Access!');
            }
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
		
        $this->set("canView", $this->canView);
    }

    public function index()
	{
		$canEdit = AdminPrivilege::canEditRegion($this->admin_id);
		$this->set("canEdit", $canEdit);
		
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("REGIONS");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name', 'region_name', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1)
	{
		$canEdit = AdminPrivilege::canEditRegion($this->admin_id);
		$this->set("canEdit", $canEdit);
		
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Regions::getSearchObject();
        if (!empty($post['region_name'])) {
            $search->addCondition('region_name', 'like', '%' . $post['region_name'] . '%');
        }
        $page = empty($page) || $page <= 0 ? 1 : $page;
        $page = FatUtility::int($page);
		
		$search->addOrder('region_display_order', 'ASC');
		
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
		
        $records = FatApp::getDb()->fetchAll($rs);
		
		$this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "regions/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function form()
	{
		$canEdit = AdminPrivilege::canEditRegion($this->admin_id);
        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $post['region_id'] = empty($post['region_id']) ? 0 : FatUtility::int($post['region_id']);
        $form = $this->getForm($post['region_id']);
        if (!empty($post['region_id'])) {
            $fc = new Regions($post['region_id']);
            if (!$fc->loadFromDb()) {
                FatUtility::dieWithError('Error! ' . $fc->getError());
            }
            $form->fill($fc->getFlds());
        }

        $adm = new Admin();
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "regions/_partial/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getForm($record_id = 0) {
        $canEdit = AdminPrivilege::canEditRegion($this->admin_id);
        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $action = 'Add';
        if ($record_id > 0) {
            $action = 'Update';
        }
        $frm = new Form('action_form', array('id' => 'action_form'));

        $fld = $frm->addHiddenField("", 'region_id', $record_id,array('id' => 'region_id'));
		$fld->requirements()->setIntPositive();
        $fld = $frm->addRequiredField('Name', 'region_name');
        $fld->developerTags['col'] = 4;
        $fld->setUnique('tbl_regions', 'region_name', 'region_id', 'region_id', 'region_id');
        $frm->addSelectBox('Status', 'region_active', Info::getStatus(), '', array('id'=> 'region_active'))->developerTags['col'] = 4;
        $frm->setFormTagAttribute('action', FatUtility::generateUrl("regions", "setup"));
        $frm->setFormTagAttribute('onsubmit', 'submitForm(formValidator,"action_form"); return(false);');
        $frm->addSubmitButton('', 'btn_submit', $action, array('class' => 'themebtn btn-default btn-sm'))->htmlAfterField = "<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
        return $frm;
    }

    public function setup() {
	
        $canEdit = AdminPrivilege::canEditRegion($this->admin_id);
        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getForm();
        $data = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $data) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
		
		
        // $regionId = FatApp::getPostedData('region_id', FatUtility::VAR_INT);
        $regionId = $data['region_id'];
        unset($data['region_id']);
		
		/* if($regionId > 0) {
			if(true === Regions::isActivityAssigned($regionId)) {
				FatUtility::dieWithError('This Region is already assigned to an activity');
			}
		} */
		
        $region = new Regions($regionId);
		
        $region->assignValues($data);

        if (!$region->save()) {
            FatUtility::dieWithError($region->getError());
        }
        $this->set('msg', 'Region Setup Successful');
        $this->_template->render(false, false, 'json-success.php');
    }
	
	public function updateOrder()
	{
		$canEdit = AdminPrivilege::canEditRegion($this->admin_id);
        if (!$canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
		
		$post = FatApp::getPostedData();
		// print_r($post); exit;
		$regionsObj = new Regions();
		
		$data = array_filter($post['regions-list']);
		
		if(empty($data)) {
			$this->renderJsonError('No change in order!!!');
		}
		
		if(!$regionsObj->updateOrder($data)) {
			$this->renderJsonError('Something Went Wrong. Please try again!!!');
		} else {
			$this->renderJsonSuccess('Display order updated!');
		}
		
	}
	
	public static function isActivityAssigned($regionId)
	{
		if(true === Regions::isActivityAssigned($regionId)){
			$ret['status'] = 2;
			$ret['msg'] = 'Region is already associated with an activity. Still you want to Change the status.';
		}
		else {
			FatUtility::dieJsonSuccess();
		}
		FatUtility::dieWithError($ret);
	}

}
