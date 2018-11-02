<?php

#error_reporting(E_ERROR);

class ActivitiesController extends AdminBaseController
{

    private $canView;
    private $canEdit;
    private $canViewReview;
    private $canEditReview;
    private $admin_id;

    public function __construct($action)
    {

        $ajaxCallArray = array('adLists', 'requestLists', 'adAction', 'changeDisplayOrder', 'getHostActivities');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewActivity($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditActivity($this->admin_id);
        $this->canViewReview = AdminPrivilege::canViewReview($this->admin_id);
        $this->canEditReview = AdminPrivilege::canEditReview($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $this->set("canViewReview", $this->canViewReview);
        $this->set("canEditReview", $this->canEditReview);
    }

    public function index()
    {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("Activities");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');
        $cities = City::getCities();
        $cities['0'] = 'Does Not Matter';
        $status = Info::getStatus();
        $status['-1'] = 'Does Not Matter';
        $hosts = Users::getHostUsers();
        $hosts['0'] = 'Does Not Matter';
        $confirm = Info::getActivityConfirmStatus();
        $confirm['-1'] = 'Does Not Matter';
        $frm->addTextBox('Activity Name', 'activity_name', '', array('class' => 'search-input'));
        $frm->addDateField('Start Date', 'activity_start_date', '', array('class' => 'search-input'));
        $frm->addDateField('End Date', 'activity_end_date', '', array('class' => 'search-input'));
        $frm->addSelectBox('City', 'activity_city_id', $cities, '0', array('class' => 'search-input'), '');
		
        //$frm->addSelectBox('Host', 'activity_user_id', $hosts, '0', array('class' => 'search-input'), '');
		
		$frm->addHiddenField('', 'activity_user_id',0);
		$frm->addTextBox(Info::t_lang('HOST'),'host_name');
		
        $frm->addSelectBox('Confirmed', 'activity_confirm', $confirm, '-1', array('class' => 'search-input'), '');
        $frm->addSelectBox('Status', 'activity_active', $status, '-1', array('class' => 'search-input'), '');
        $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function listing($page = 1)
    {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Activity::getSearchObject();
        if (!empty($post['activity_name'])) {
            $search->addCondition('activity_name', 'like', '%' . $post['activity_name'] . '%');
        }
        if (!empty($post['activity_start_date'])) {
            $search->addCondition('activity_start_date', '>=', $post['activity_start_date']);
        }
        if (!empty($post['activity_end_date'])) {
            $search->addCondition('activity_end_date', '<=', $post['activity_end_date']);
        }
        if (isset($post['activity_confirm']) && $post['activity_confirm'] > -1 && $post['activity_confirm'] != '') {
            $search->addCondition('activity_confirm', '=', $post['activity_confirm']);
        }
        if (isset($post['activity_active']) && $post['activity_active'] > -1 && $post['activity_active'] != '') {
            $search->addCondition('activity_active', '=', $post['activity_active']);
        }
        if (!empty($post['activity_user_id'])) {
            $search->addCondition('activity_user_id', '=', $post['activity_user_id']);
        }
        if (isset($post['activity_city_id']) && $post['activity_city_id'] > 0 && $post['activity_city_id'] != '') {
            $search->addCondition('activity_city_id', '=', $post['activity_city_id']);
        }
        $search->joinTable(Users::DB_TBL, 'left Join', Users::DB_TBL_PREFIX . 'id = ' . Activity::DB_TBL_PREFIX . 'user_id');
        $search->joinTable(Cities::DB_TBL, 'left Join', Cities::DB_TBL_PREFIX . 'id = ' . Activity::DB_TBL_PREFIX . 'city_id');
        $search->addMultipleFields(array(
            Activity::DB_TBL . '.*',
            "concat(" . Users::DB_TBL_PREFIX . "firstname,' ', " . Users::DB_TBL_PREFIX . "lastname) as user_name",
            Cities::DB_TBL_PREFIX . 'name'
        ));
        $search->addOrder(Activity::DB_TBL_PREFIX . 'id', 'desc');
        $search->addOrder(Activity::DB_TBL_PREFIX . 'name');
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
        $this->set('hosts', Users::getHostUsers());
        $htm = $this->_template->render(false, false, "activities/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function changeConfirmStatus()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;

        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $data['activity_confirm'] = isset($post['status']) ? FatUtility::int($post['status']) : 0;

        $act = new Activity($activity_id);
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }

        if ($data['activity_confirm'] != 0) {
            $act->loadFromDb();
            $act_data = $act->getFlds();
            $user_id = @$act_data['activity_island_id'];
            $island = new Islands($act_data['activity_island_id']);
            $island->loadFromDb();
            $island_data = $island->getFlds();
            $usr = new Users($act_data['activity_user_id']);
            $usr->loadFromDb();
            $user_data = $usr->getFlds();
            $vars = array(
                '{username}' => @$user_data['user_firstname'] . ' ' . @$user_data['user_lastname'],
                '{status}' => Info::getActivityConfirmStatusByKey($act_data['activity_confirm']),
                '{island_name}' => @$island_data['island_name'],
                '{activity_name}' => @$act_data['activity_name'],
                '{activity_price}' => @$act_data['activity_price'],
                '{activity_start_date}' => FatDate::format($act_data['activity_start_date']),
                '{activity_end_date}' => FatDate::format($act_data['activity_end_date']),
            );
            $notification = New Notification();
            $url = FatUtility::generateUrl('hostactivity', 'update', array($activity_id));
            $notification_text = Info::t_lang('YOUR_ACTIVITY-') . ' ' . $act_data['activity_name'] . ' ' . Info::t_lang('HAS_BEEN') . ' ' . Info::getActivityConfirmStatusByKey($act_data['activity_confirm']);
            $notification->notify($act_data['activity_user_id'], 0, $url, $notification_text);
            Email::sendMail($user_data['user_email'], 9, $vars);
            /* send Notification */
            $notification = new Notification();
            $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_HAS_BEEN_') . Info::getActivityConfirmStatusByKey($act_data['activity_confirm']) . Info::t_lang('_BY_ADMIN');
			
			$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
			
            $notification->notify($user_id, 0, $notificationLink, $text);
            /* send Notification */
        }
        FatUtility::dieJsonSuccess("Confirm Status Updated");
    }

    public function changeStatus()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;

        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $data['activity_active'] = isset($post['status']) ? FatUtility::int($post['status']) : 0;

        $act = new Activity($activity_id);
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }
        $act->loadFromDb();
        $act_data = $act->getFlds();
        $island = new Islands($act_data['activity_island_id']);
        $user_id = @$act_data['activity_island_id'];
        $island->loadFromDb();
        $island_data = $island->getFlds();
        $usr = new Users($act_data['activity_user_id']);
        $usr->loadFromDb();
        $user_data = $usr->getFlds();
        $vars = array(
            '{username}' => @$user_data['user_firstname'] . ' ' . @$user_data['user_lastname'],
            '{status}' => Info::getStatusByKey($act_data['activity_active']),
            '{island_name}' => @$island_data['island_name'],
            '{activity_name}' => @$act_data['activity_name'],
            '{activity_price}' => @$act_data['activity_price'],
            '{activity_start_date}' => FatDate::format($act_data['activity_start_date']),
            '{activity_end_date}' => FatDate::format($act_data['activity_end_date']),
        );
        /* send Notification */
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_HAS_BEEN_') . Info::getStatusByKey($act_data['activity_active']) . Info::t_lang('_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($user_id, 0, $notificationLink, $text);
        /* send Notification */
        Email::sendMail($user_data['user_email'], 9, $vars);

        FatUtility::dieJsonSuccess("Status Updated");
    }

    function details($activity_id)
    {

        $activity_id = FatUtility::int($activity_id);
        if (!($activity_id > 0 )) {
            FatApp::redirectUser(FatUtility::generateUrl('activities'));
        }
        $activity = new Activity($activity_id);
        $activity->loadFromDb();
        $activity_name = $activity->getFldValue(Activity::DB_TBL_PREFIX . 'name');
        $brcmb = new Breadcrumb();
        $brcmb->add("Activities", FatUtility::generateUrl('Activities'));
        $brcmb->add($activity_name);
        $this->set('breadcrumb', $brcmb->output());
        $this->set('activity_id', $activity_id);
        $this->_template->render();
    }

    function tab($activity_id, $tab)
    {
        $activity_id = FatUtility::int($activity_id);
        $tab = FatUtility::int($tab);
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $tab = $tab <= 1 ? 1 : $tab;
        switch ($tab) {
            case 2:
                $this->photos($activity_id);
                break;
            case 3:
                $this->videos($activity_id);
                break;
            case 4:
                $this->additionalInfo($activity_id);
                break;
            case 5:
                $this->map($activity_id);
                break;
            case 6:
                $this->availability($activity_id);
                break;
            case 7:
                $this->step7($activity_id);
                break;
            case 8:
                $this->reviews($activity_id);
                break;
            default:
                $this->basicInfomation($activity_id);
                break;
        }
    }

    function basicInfomation($activity_id)
    {
        $frm = $this->getbasicInfomationForm($activity_id);
        $e = new Activity($activity_id);
        if (!$e->loadFromDb()) {
            FatUtility::dieJsonError('Error! ' . $e->getError());
        }
        $flds = $e->getFlds();
		
		// echo '<pre>' . print_r($flds, true);
        if (!empty($flds)) {
            //get City Info

            $cityData = City::getCityById($flds['activity_city_id']);
            if ($cityData) {
				$flds['activity_region_id'] = $cityData['country_region_id'];
                $flds['activity_country_id'] = $cityData['city_country_id'];
                $frm->getField('activity_city_id')->options = City::getAllCitiesByCountryId($cityData['city_country_id']);
				$frm->getField('activity_country_id')->options = Country::getAllCountryByRegionId($cityData['country_region_id']);
            }

            $categoryId = Services::getParentCateogry($flds['activity_category_id']);

            $flds['category_id'] = $categoryId;
            $cats = Services::getCategories($categoryId);
            $fld = $frm->getField('activity_category_id');
            $fld->options = $cats;
            if ($flds['activity_booking'] > 23) {
                $flds['booking_days'] = $flds['activity_booking'] / 24;
                $flds['activity_booking'] = 100;
            }
            if ($flds['activity_duration'] > 23) {
                $flds['duration_days'] = $flds['activity_duration'] / 24;
                $flds['activity_duration'] = 100;
            }
            $act_attributes = $e->getActivityAttributeRelations($activity_id);
            $attributes = ActivityAttributes::getAttributes();
            if (!empty($attributes)) {
                foreach ($attributes as $attr_id => $attr) {
                    if (isset($act_attributes[$attr_id])) {
                        $attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id);

                        $check_box_fld = $frm->getField('attr[' . $attr_id . ']');
                        $check_box_fld->checked = 1;
                        if ($attr[ActivityAttributes::DB_TBL_PREFIX . 'file_required'] == 1) {
                            $check_box_fld->htmlAfterField = '<br><a class="link" href="' . FatUtility::generateUrl('image', 'attribute', array($attr_id, $activity_id), CONF_WEBROOT_URL) . '" target="_blank">' . $attachment[AttachedFile::DB_TBL_PREFIX . 'name'] . '</a>';
                            $frm->getField('attr_file_' . $attr_id)->setWrapperAttribute('style', 'display:block');
                        }
                    }
                }
            }
            $frm->fill($flds);
        }

        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'activities/_partial/basic-info-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function setupBasicInfomation()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
		
        $post = FatApp::getPostedData();

        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
		
		$countryId = FatUtility::int($post['activity_country_id']);
		$cityId = FatUtility::int($post['activity_city_id']);
		
        $frm = $this->getbasicInfomationForm($activity_id);
        $post = $frm->getFormDataFromArray($post, array('activity_category_id'), array());
		
		$post['activity_country_id'] = $countryId;
		$post['activity_city_id'] = $cityId;

		//print_r($post);die;

        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        if ($post['activity_booking'] == 100) {
            $post['activity_booking'] = $post['booking_days'] * 24;
        }

        if ($post['activity_duration'] == 100) {
            $post['activity_duration'] = $post['duration_days'] * 24;
        }
        if ($post == false) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $attributes = ActivityAttributes::getAttributes();
        $valid_file_extension = FatApp::getConfig('CONF_ACTIVITY_ATTRIBUTE_VALID_FILE_EXTENSION');
        $valid_file_extension = explode(',', $valid_file_extension);
        if (!empty($valid_file_extension)) {
            $valid_file_extension = array_map('trim', $valid_file_extension);
            $valid_file_extension = array_map('strtolower', $valid_file_extension);
        }
        if (!empty($attributes) && array_key_exists('attr', $post)) {
            $attr = $post['attr'];
            foreach ($attributes as $attr_id => $attr) {
                if (isset($post['attr'][$attr_id]) && $post['attr'][$attr_id] == 1) {
                    if ($attr[ActivityAttributes::DB_TBL_PREFIX . 'file_required'] == 1) {
                        if (empty($_FILES['attr_file_' . $attr_id])) {
                            $attach_file = array();
                            if ($activity_id > 0) {
                                $attach_file = AttachedFile::getAttachment(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id);
                            }
                            if (empty($attach_file)) {
                                FatUtility::dieJsonError(Info::t_lang('UPLOAD_FILE_FOR : ' . $attr[ActivityAttributes::DB_TBL_PREFIX . 'caption']));
                            }
                        } else {
                            $file_info = pathinfo($_FILES['attr_file_' . $attr_id]['name']);
                            $extension = strtolower($file_info['extension']);
                            if (!empty($valid_file_extension)) {
                                if (!in_array($extension, $valid_file_extension)) {
                                    FatUtility::dieJsonError(Info::t_lang('INVALID_FILE_TYPE._VALID_FILE_TYPE_ARE: ' . implode(',', $valid_file_extension)));
                                }
                            }
                        }
                    }
                }
            }
        }
		
		
        $act = new Activity($activity_id);
        $act->assignValues($post);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }
        $attr_relation = array();
        $attachFile = new AttachedFile();
        $act->deleteActivityAttributeRelation($activity_id);
        if (!empty($attributes) && array_key_exists('attr', $post)) {
            $attr = $post['attr'];
            foreach ($attributes as $attr_id => $attr) {

                if (isset($post['attr'][$attr_id]) && $post['attr'][$attr_id] == 1) {
                    $attr_relation[] = $attr_id;

                    if ($attr[ActivityAttributes::DB_TBL_PREFIX . 'file_required'] == 1 && !empty($_FILES['attr_file_' . $attr_id])) {
                        AttachedFile::removeFiles(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id);
                        if (!$attachFile->uploadAndSaveFile('attr_file_' . $attr_id, AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id, 0, true)) {
                            FatUtility::dieJsonError(Info::t_lang('ERROR : ' . $attachFile->getError() . ' FOR ' . $attr[ActivityAttributes::DB_TBL_PREFIX . 'caption']));
                        }
                    }
                } else {
                    AttachedFile::removeFiles(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id);
                }
            }
        }
        if (!empty($attr_relation)) {
            if (!$act->saveActivityAttributeRelation($activity_id, $attr_relation)) {
                FatUtility::dieJsonError('SOMETHING_WENT_WRONG.PLEASE_TRY_AGAIN.');
            }
        }
        if ($activity_id < 1) {
            $route = new Routes();
            $routeData = array(
                'url_rewrite_custom' => Info::getSlugFromName($post['activity_name']),
                'url_rewrite_record_id' => $act->getMainTableRecordId(),
                'url_rewrite_subrecord_id' => 0,
                'url_rewrite_record_type' => Route::ACTIVITY_ROUTE,
            );
            $route->createNewRoute($routeData);
        }


        /* Send Notification */
        $act->loadFromDb();
        $act_data = $act->getFlds();
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_CONTENT_HAS_BEEN_CHANGED_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
        /* send Notification end */
        FatUtility::dieJsonSuccess("Record Updated");
    }

    private function getbasicInfomationForm($record_id = 0)
    {
        $frm = new Form('frmRegister');
        $frm->addHiddenField('', 'activity_id');
        $fld = $frm->addRequiredField(Info::t_lang('ACTIVITY_NAME'), 'activity_name');

        //Add Slug Functionality
        if ($record_id > 0) {
            $slugHtml = '<a href="' . Route::getRoute('activity', 'detail', array($record_id), true) . '?admin=true" target="_BLANK" >' . Route::getRoute('activity', 'detail', array($record_id), true) . '</a>  <a class="button" onClick="editSlug(this)" data-record-id=' . $record_id . ' data-record-type=' . Route::ACTIVITY_ROUTE . '><i  class="ion-edit"></i></a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }

        $frm->addSelectBox(Info::t_lang('REGION'), 'activity_region_id', Region::getRegions(), '', array('onChange' => 'getCountries(this.value)'))->requirements()->setRequired();
		
		$frm->addSelectBox(Info::t_lang('COUNTRY'), 'activity_country_id', array(), '', array('onChange' => 'getCities(this.value)', 'id' => 'countries'))->requirements()->setRequired();
        
		$frm->addSelectBox(Info::t_lang('CITY'), 'activity_city_id', array(), '', array('id' => 'cities'))->requirements()->setRequired();
		
        $frm->addSelectBox(Info::t_lang('THEMES'), 'category_id', Services::getCategories(), '', array('onchange' => 'getSubService(this)'));
        $frm->addSelectBox(Info::t_lang('CATEGORIES'), 'activity_category_id', array(), '', array('id' => 'subcat-list'));
        $frm->addDateField(Info::t_lang('START_DATE'), 'activity_start_date')->requirements()->setRequired();
        $frm->addDateField(Info::t_lang('END_DATE'), 'activity_end_date')->requirements()->setRequired();
        $frm->addIntegerField(Info::t_lang('MAX_TRAVELER'), 'activity_members_count', 0);
        $frm->addSelectBox(Info::t_lang('ACTIVITY_BOOKINGS'), 'activity_booking', Info::activityBookings(), '', array('id' => 'booking-day', 'onChange' => "changeBooking(this.value)"))->requirements()->setRequired();

        $frm->addSelectBox(Info::t_lang('ACTIVITY_DURATION'), 'activity_duration', Info::activityDuration(), '', array('id' => 'duration-day', 'onChange' => "changeDuration(this.value)"))->requirements()->setRequired();
        ;

        $price_type = $frm->addSelectBox(Info::t_lang('ACTIVITY_PRICE_AS_PER'), 'activity_price_type', Info::activityType(), 0, array(), '');

        $frm->addRequiredField(Info::t_lang('ACTIVITY_SALE_PRICE'), 'activity_price');
        $frm->addTextBox(Info::t_lang('ACTIVITY_ORIGINAL_PRICE'), 'activity_display_price');
		
        

		$fld = $frm->addIntegerField(Info::t_lang('NO_OF_DAYS'), 'booking_days', 0);

        //$frm->addSelectBox(Info::t_lang('WALK-IN AVAILABLE'), 'activity_walkin',Info::getIs())->requirements()->setRequired();;

        $fld->fieldWrapper = array('<div id="booking-day-field">', '</div>');
		
		$fld = $frm->addIntegerField(Info::t_lang('NO_OF_DAYS'), 'duration_days', 0);
        $fld->fieldWrapper = array('<div id="duration-day-field">', '</div>');

        /* Hide only 

          $attributes = ActivityAttributes::getAttributes();
          if (!empty($attributes)) {
          foreach ($attributes as $attr_id => $attr) {
          $fld = $frm->addCheckBox($attr[ActivityAttributes::DB_TBL_PREFIX . 'caption'], 'attr[' . $attr_id . ']', 1, array('data-attr' => json_encode(array('attr_id' => $attr_id, 'file_required' => $attr[ActivityAttributes::DB_TBL_PREFIX . 'file_required'])), 'onchange' => 'selectAttr(this)'), false, 0);
          if ($attr[ActivityAttributes::DB_TBL_PREFIX . 'file_required'] == 1) {
          $file = $frm->addFileUpload(Info::t_lang('UPLOAD_FILE'), 'attr_file_' . $attr_id, array('id' => 'attr_file_' . $attr_id));

          $file->setWrapperAttribute('style', 'display:none');
          $file->setWrapperAttribute('id', 'attr_file_wrapper_' . $attr_id);
          }
          }
          } */

        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SAVE'), array());
        }
        return $frm;
    }

    public function photos($activity_id)
    {
        $act = new Activity();
        $images = $act->getActivityImages($activity_id);
        $e = new Activity($activity_id);
        $e->loadFromDb();
        $flds = $e->getFlds();
        $this->set('images', $images);
        $this->set('flds', $flds);
        $form = $this->getPhotoForm();
        $form->fill(array('activity_id' => $activity_id));
        $this->set('frm', $form);
        $html = $this->_template->render(false, false, 'activities/_partial/photo-form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    public function setupPhoto()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $frm = $this->getPhotoForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getvalidationErrors()));
        }
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $attach = new AttachedFile();
        if ($attach->saveAttachment($_FILES['activity_image']['tmp_name'], AttachedFile::FILETYPE_ACTIVITY_PHOTO, $activity_id, 0, $_FILES['activity_image']['name'])) {
            $e = new Activity($activity_id);
            $e->loadFromDb();
            $flds = $e->getFlds();
            if ($flds['activity_image_id'] == 0) {
                $data['activity_image_id'] = FatApp::getDb()->getInsertId();
                $act = new Activity($activity_id);
                $act->assignValues($data);
                $act->save();
            }
            FatUtility::dieJsonSuccess("image Uploaded");
        }
        FatUtility::dieJsonError("Something went wrong, please try again");
    }

    function changePhotoStatus()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $afile_id = $post['afile_id'];
        $data['afile_approved'] = $post['afile_approved'];
        if (!AttachedFile::updateByAfileId($afile_id, $data)) {
            FatUtility::dieJsonError('Something Went Wrong!');
        }
        if ($post['afile_approved'] != 0) {

            $act = new Activity($activity_id);
            $act->loadFromDb();
            $act_data = $act->getFlds();
            /* send Notification */
            $notification = new Notification();
            $file_data = AttachedFile::getAttachmentById($afile_id);
            if (AttachedFile::FILETYPE_ACTIVITY_ADDON == $file_data[AttachedFile::DB_TBL_PREFIX . 'type']) {
                $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_ADDON_IMAGE_HAS_BEEN_') . Info::getFileApprovedStatusByKey($post['afile_approved']) . Info::t_lang('_BY_ADMIN');
            } else {
                $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_IMAGE_HAS_BEEN_') . Info::getFileApprovedStatusByKey($post['afile_approved']) . Info::t_lang('_BY_ADMIN');
            }

			$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
			
            $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
            /* send Notification end */
        }
        FatUtility::dieJsonSuccess('Status Changed!');
    }

    function removeFile()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $afile_id = $post['afile_id'];
        if (!AttachedFile::removeFile($afile_id)) {
            FatUtility::dieJsonError('Something Went Wrong!');
        }
        $act = new Activity($activity_id);
        $act->loadFromDb();
        $act_data = $act->getFlds();
        /* send Notification  */
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_IMAGE_HAS_BEEN_REMOVED_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
        /* send Notification end */
        FatUtility::dieJsonSuccess('Image Removed!');
    }

    public function defaultImage()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $data['activity_image_id'] = $post['afile_id'];
        $activity_id = $post['activity_id'];
        $act = new Activity($activity_id);
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        $act = new Activity($activity_id);
        $act->loadFromDb();
        $act_data = $act->getFlds();
        /* send Notification */
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_DEFAULT_IMAGE_HAS_BEEN_CHANGED_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
        /* send Notification end */
        FatUtility::dieJsonSuccess("Images Set as Default Image");
    }

    private function getPhotoForm()
    {
        $frm = new Form('frmPhoto');
        $frm->addHiddenField('', 'activity_id');
        $frm->addFileUpload(Info::t_lang('IMAGE'), 'activity_image');
        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPLOAD'), array());
        }
        return $frm;
    }

    /////////////////////////////////////// videos \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function videos($activity_id)
    {
        $act = new Activity($activity_id);
        $videos = $act->getActivityVideos($activity_id);
        $this->set('videos', $videos);

        $form = $this->getVideoForm();
        $form->fill(array('activityvideo_activity_id' => $activity_id));
        $this->set('frm', $form);
        $html = $this->_template->render(false, false, 'activities/_partial/video-form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    private function getVideoForm()
    {
        $frm = new Form('frmVideo');
        $frm->addHiddenField('', 'activityvideo_activity_id');
        $frm->addRequiredField(Info::t_lang('URL'), 'activity_video');
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD_VIDEO'));
        return $frm;
    }

    public function setupVideo()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $data['activityvideo_activity_id'] = $post['activityvideo_activity_id'];
        $data['activityvideo_url'] = $post['activity_video'];
        $video = Info::getVideoDetail($post['activity_video']);
        $data['activityvideo_videoid'] = $video['video_id'];
        $data['activityvideo_type'] = $video['video_type'];
        $data['activityvideo_thumb'] = $video['video_thumb'];
        $data['activityvideo_active'] = 1;

        $act = new Activity();
        if ($act->addActivityVideo($data)) {
            FatUtility::dieJsonSuccess("Video Added");
        }
        FatUtility::dieJsonError("Something went wrong, please try again");
    }

    public function removeVideo()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $act = new Activity();
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        $activityvideo_id = isset($post['activityvideo_id']) ? FatUtility::int($post['activityvideo_id']) : 0;
        if (!($activity_id > 0) || !($activityvideo_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $act->removeActivityVideo($activity_id, $activityvideo_id);

        $act = new Activity($activity_id);
        $act->loadFromDb();
        $act_data = $act->getFlds();
        /* send Notification */
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_VIDEO_HAS_BEEN_REMOVED_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
        /* send Notification end */
        FatUtility::dieJsonSuccess("Video Deleted");
    }

    function changeVideoStatus()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $video_id = $post['video_id'];
        $data['activityvideo_active'] = $post['status'];
        $activity = new Activity();
        if (!$activity->updateVideo($video_id, $data)) {
            FatUtility::dieJsonError('Something Went Wrong!');
        }
        if ($post['status'] != 0) {
            $act = new Activity($activity_id);
            $act->loadFromDb();
            $act_data = $act->getFlds();
            /* send Notification */
            $notification = new Notification();
            $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_VIDEO_HAS_BEEN_') . Info::getVideoStatusByKey($post['status']) . Info::t_lang('_BY_ADMIN');
			
			$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
			
            $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
            /* send Notification end */
        }
        FatUtility::dieJsonSuccess('Status Changed!');
    }

    ///////////////////////////////////////////// Videos end \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    ////////////////////////////////////additional information  start \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function additionalInfo($activity_id)
    {

        $frm = $this->getAdditionalInfoForm();
        $this->set('frm', $frm);

        $e = new Activity($activity_id);
        if (!$e->loadFromDb()) {

            FatUtility::dieWithError('Error! ' . $e->getError());
        }
        $flds = $e->getFlds();
        $flds['activity_contacts'] = array(@$flds['activity_contacts']);
        if (!empty($flds)) {

            $flds['activity_languages'] = $e->getActivityLanguages($activity_id);
            $frm->fill($flds);
        }
        $html = $this->_template->render(false, false, 'activities/_partial/additional-info-form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    private function getAdditionalInfoForm()
    {
        $frm = new Form('frmExtra');
        $frm->addHiddenField('', 'activity_id');
        $text_area_id = 'high_area';
        $editor_id = 'highlight_area' . '-removed';
        $field = $frm->addTextArea(Info::t_lang('HIGHLIGHTS'), 'activity_highlights', '', array('id' => $text_area_id));
        // $field->htmlAfterField = '<div id="' . $editor_id . '"></div>' . Helper::getInnovaEditorObj($text_area_id, $editor_id) . '<em>' . Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS') . '</em>';
		$field->htmlAfterField ='<em>('.Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS').')</em>';
        $text_area_id = 'text_area';
        $editor_id = 'editor_area';
		
		$field = $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activity_desc', '', array('id' => $text_area_id));
		
		
        // $field = $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activity_desc', '', array('id' => $text_area_id))->htmlAfterField = '<div id="' . $editor_id . '"></div>' . Helper::getInnovaEditorObj($text_area_id, $editor_id) . '<em>' . Info::t_lang('DETAILED_DESCRIPTION_OF_THE_ACTIVITY') . '</em>';
		$field->htmlAfterField ='<em>('.Info::t_lang('DETAILED_DESCRIPTION_OF_THE_ACTIVITY').')</em>';
		
        $frm->addTextArea(Info::t_lang('INCLUSIONS'), 'activity_inclusions')->htmlAfterField = '<span>Tip - Text will appear in bullet points. Press enter for a new line.</span>';
        $frm->addTextArea(Info::t_lang('REQUIREMENTS'), 'activity_requirements')->htmlAfterField = '<span>Tip -  Text will appear in bullet points. Press enter for a new line.</span>';
        
        ///	$frm->addTextArea(Info::t_lang('INSTRUCTIONS'),'activity_instructions')->htmlAfterField = '<span>Tip - Please Split Enter For New Line</span>';
        //$activity_contacts = $frm->addCheckBoxes(Info::t_lang('LOCATION'),'activity_contacts',Info::activityMeetingPoints(),array(1),array("class"=>"list list--12 list--horizontal"));

        $frm->addCheckBoxes(Info::t_lang('LANGUAGES'), 'activity_languages', Languages::getAllLang(), array(), array("class" => "list--3 list--horizontal"));
		
		$cancellation_policies = CancellationPolicy::getRecordByUserTypeForForm();

        $fld = $frm->addSelectBox(Info::t_lang('CANCELLATION_POLICY'), 'activity_cancelation', $cancellation_policies, '', array(), Info::t_lang('SELECT_CANCELLATION_POLICY'));
        $fld->htmlAfterField = '<a href="' . FatUtility::generateUrl('CancellationPolicy', '', array(), '/') . '" target="_blank">Tip</a>';
        $fld->requirements()->setRequired();
		
        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPDATE'));
        }
        return $frm;
    }

    public function setupAdditionalInfo()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access');
        }
        $post = FatApp::getPostedData();
        $frm = $this->getAdditionalInfoForm();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post, array('activity_languages'));
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['activity_contacts'] = @$post['activity_contacts'][0];
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $act = new Activity($activity_id);
        $act->assignValues($post);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }
        $act->deleteActivityLanguages($activity_id);

        foreach ($post['activity_languages'] as $lang_id) {
            $arr['activitylanguage_activity_id'] = $activity_id;
            $arr['activitylanguage_language_id'] = $lang_id;

            $act->addActivityLanguage($arr);
        }
        /* send Notification */
        $act->loadFromDb();
        $act_data = $act->getFlds();
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_CONTENT_HAS_BEEN_CHANGED_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
        /* send Notification end */
        FatUtility::dieJsonSuccess("Record Updated");
    }

    ////////////////////////////////////additional information  end \\\\\\\\\\\\\\\\\\\\\\\\\\\\
    ///////////////////////////////////////////// Map \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function map($activity_id)
    {

        $e = new Activity($activity_id);
        $e->loadFromDb();
        $flds = $e->getFlds();
        $island = City::getCityById($flds['activity_city_id']);
        if ($flds['activity_latitude'] == 0) {
            $lat = $island['city_latitude'];
            $long = $island['city_longitude'];
        } else {
            $lat = $flds['activity_latitude'];
            $long = $flds['activity_longitude'];
        }
        $frm = $this->getMapForm();
        $frm->fill(array('activity_id' => $activity_id));
        $this->set('lat', $lat);
        $this->set('long', $long);
        $this->set('frm', $frm);
        $html = $this->_template->render(false, false, 'activities/_partial/map.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    private function getMapForm()
    {
        $frm = new Form('frmCoords');
        $frm->addHiddenField('', 'activity_id');
        $frm->addHiddenField('', 'activity_latitude', "", array('id' => 'act_lat'));
        $frm->addHiddenField('', 'activity_longitude', "", array('id' => 'act_long'));
        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPDATE_COORDINATES'));
        }
        return $frm;
    }

    public function setupMap()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $frm = $this->getMapForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if (!($activity_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $act = new Activity($activity_id);
        $act->assignValues($post);
        if (!$act->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        $act->loadFromDb();
        $act_data = $act->getFlds();
        /* send Notification */
        $notification = new Notification();
        $text = Info::t_lang('YOUR_') . $act_data['activity_name'] . Info::t_lang('_ACTIVITY_LOCATION_HAS_BEEN_CHANGED_BY_ADMIN');
		
		$notificationLink = FatUtility::generateUrl('Hostactivity', 'update', array($activity_id), CONF_BASE_DIR);
		
        $notification->notify($act_data['activity_user_id'], 0, $notificationLink, $text);
        /* send Notification end */
        FatUtility::dieJsonSuccess(Info::t_lang('LOCATION_HAS_BEEN_UPDATED!'));
    }

    /////////////////////////////// Availability \\\\\\\\\\\\\\\\\\\\\



    public function availability($activity_id)
    {
        $e = new Activity($activity_id);
        $e->loadFromDb();
        $flds = $e->getFlds();

        $post = FatApp::getPostedData();
        $startyear = date('Y', strtotime($flds['activity_start_date']));
        $startmonth = date('m', strtotime($flds['activity_start_date']));

        $endyear = date('Y', strtotime($flds['activity_end_date']));
        $endmonth = date('m', strtotime($flds['activity_end_date']));

        if (isset($post) && !empty($post) && isset($post['type'])) {
            if ($post['type'] == 'prev') {
                $yr = $post['year'];
                $month = $post['month'];
                if ($month == 1) {
                    $month = 12;
                    $yr = $yr - 1;
                } else {
                    $month = $month - 1;
                }
            }

            if ($post['type'] == 'next') {
                $yr = $post['year'];
                $month = $post['month'];
                if ($month == 12) {
                    $month = 1;
                    $yr = $yr + 1;
                } else {
                    $month = $month + 1;
                }
            }

            if ($post['type'] == 'current') {
                $yr = $post['year'];
                $month = $post['month'];
            }
        } else {
            $yr = date('Y');
            $month = date('m');
        }
        $next = 1;
        $prev = 1;

        $pyr = $yr;
        $pmonth = $month;
        if ($pmonth == 1) {
            $pmonth = 12;
            $pyr = $pyr - 1;
        } else {
            $pmonth = $pmonth - 1;
        }

        if ($pyr < $startyear) {
            $prev = 0;
        }
        if ($pyr == $startyear && $pmonth < $startmonth) {
            $prev = 0;
        }


        $nyr = $yr;
        $nmonth = $month;
        if ($nmonth == 12) {
            $nmonth = 1;
            $nyr = $nyr + 1;
        } else {
            $nmonth = $nmonth + 1;
        }


        if ($nyr > $endyear) {
            $next = 0;
        }
        if ($nyr == $endyear && $nmonth > $endmonth) {
            $next = 0;
        }


        $currentDate = Info::currentDate();
        $c = new Calendar($month, $yr);
        $calendar = $c->generateMonthCalendar();

        $cals = array();
        foreach ($calendar as $k => $v) {

            $date = $yr . '-' . $month . '-' . $v;

            $class = "";
            $cals[$k]['date'] = $v;
            $cals[$k]['fulldate'] = date('Y-m-d', strtotime($date));
            if (strtotime($currentDate) == strtotime($date)) {
                $class .= ' current';
            }

            if (strtotime($date) < strtotime($flds['activity_start_date'])) {
                $class .= ' disable';
            }

            if (strtotime($date) > strtotime($flds['activity_end_date'])) {
                $class .= ' disable';
            }

            $cals[$k]['events'] = array();
            if ($v != "") {
                if ($cals[$k]['events'] = $e->getActivityEventByDate($activity_id, date('Y-m-d', strtotime($date)))) {
                    //	Info::test($cals);
                    $class .= ' have-event';
					
					/* New Code added */
					
                     foreach($cals[$k]['events'] as $eves){
						  if($eves['activityevent_confirmation_requrired'] == 1){
							$class .= ' required'; continue;
						  }
                      } 
					  
					/* New Code added */
					  
					  
                }
            }
            $cals[$k]['class'] = $class;
        }


		/* New Code added */
        $block = new Block();
        $prior_ins = $block->getBlock(13);
        $available_ins = $block->getBlock(14);
        $bulk_entry_ins = $block->getBlock(15);
        $this->set("prior_ins", $prior_ins);
        $this->set("available_ins", $available_ins);
        $this->set("bulk_entry_ins", $bulk_entry_ins);
		/* New Code added */
		
        $this->set("year", $yr);
        $dt = DateTime::createFromFormat('!m', $month);

        $this->set("showmonth", $dt->format('M'));
        $this->set("month", $month);

        $this->set("next", $next);

        $this->set("prev", $prev);

        $this->set("calendar", $cals);

        //$html = $this->_template->render(true,true,'activities/_partial/step6.php');
        $html = $this->_template->render(false, false, 'activities/_partial/availability.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    public function setup6($activity_id)
    {

        $post = FatApp::getPostedData();
         	
/* 		echo '<pre>';
		print_r($post);die;  */
		
        $er = new Activity();
		
        $events = $er->getEventByMonth($activity_id, $post['month'], $post['year']);
        if (!empty($events)) {
            FatUtility::dieJsonError("Bulk Entries possible only if no former entry on this month");
        }
		
        $e = new Activity($activity_id);
        $e->loadFromDb();
        $flds = $e->getFlds();

        $strdDate = Calendar::getStartDate($flds['activity_start_date'], $post['month'], $post['year']);

        $endDate = strtotime(Calendar::getEndDate($flds['activity_end_date'], $post['month'], $post['year']));
        $post = FatApp::getPostedData();
        if ($post['service_type'] == 1) {
            unset($post['hour_slot']);
            unset($post['minute_slot']);
            $post['hour_slot'][] = 0;
            $post['minute_slot'][] = 0;
        }
        $enddt = strtotime(date("Y-m-d", strtotime("+1 day", $endDate)));
        if ($post['entry_type'] == "1") {
            for ($dt = strtotime($strdDate); $dt < $enddt; $dt = strtotime(date("Y-m-d", strtotime("+1 day", $dt)))) {

                foreach ($post['hour_slot'] as $k => $hour) {
                    $tslot = date('Y-m-d', $dt) . ' ' . $hour . ':' . $post['minute_slot'][$k] . ':' . '00';
                    $array['activityevent_activity_id'] = $activity_id;
                    $array['activityevent_time'] = $tslot;
                    $array['activityevent_anytime'] = $post['service_type'];
                    $array['activityevent_status'] = 1;
                    $array['activityevent_confirmation_requrired'] = $post['confirm_type'];
                    //	$data[] = $array;
                    //var_dump($array);
                    $e->addTimeSlot($array);
                }
            }
        }

        if ($post['entry_type'] == "2") {
            for ($dt = strtotime($strdDate); $dt < $enddt; $dt = strtotime(date("Y-m-d", strtotime("+1 day", $dt)))) {


                if (in_array((intval(date('w', $dt))), $post['weekdays'])) {
                    foreach ($post['hour_slot'] as $k => $hour) {
                        $tslot = date('Y-m-d', $dt) . ' ' . $hour . ':' . $post['minute_slot'][$k] . ':' . '00';

                        $array['activityevent_activity_id'] = $activity_id;
                        $array['activityevent_time'] = $tslot;
                        $array['activityevent_anytime'] = $post['service_type'];
                        $array['activityevent_status'] = 1;
                        $array['activityevent_confirmation_requrired'] = $post['confirm_type'];
                        //var_dump($array);
                        $e->addTimeSlot($array);
                    }
                }
            }
        }

        FatUtility::dieJsonSuccess("Events Added!");
    }

    function deleteEvent($activity_id)
    {

        $post = FatApp::getPostedData();
        $ord = new Order();
        if (isset($post) && intval($post['event_id']) != 0) {
            $e = new Activity();
            if (!$ord->isEventBooked($post['event_id'])) {
                $e->removeEvent($activity_id, intval($post['event_id']));
                FatUtility::dieJsonSuccess("Event Removed");
            } else {
                FatUtility::dieJsonError(Info::t_lang('BOOKING_IS_BOOKED_FOR_THIS_EVENT._YOU_CAN_NOT_DELETE'));
            }
        }
        FatUtility::dieJsonError("Something Went Wrong!");
    }

    function editEvent($event_id)
    {

        $act = new Activity();
        $ord = new Order();
        $event_id = FatUtility::int($event_id);
        if ($event_id <= 0) {
            FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
        }
        $event_data = $act->getEvent($event_id);
        if (empty($event_data)) {
            FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
        }

        $data = array(
            'event_id' => $event_data['activityevent_id'],
            'service_type' => $event_data['activityevent_anytime'],
            'date' => date('Y-m-d', strtotime($event_data['activityevent_time'])),
            'confirm_type' => $event_data['activityevent_confirmation_requrired'],
            'hour' => date('G', strtotime($event_data['activityevent_time'])),
            'minute' => date('i', strtotime($event_data['activityevent_time'])),
            'status' => $event_data['activityevent_status'],
        );
        $event_status = Info::getEventStatus();

        $total_booking = $ord->getEventBooking($event_id);
        $frm = $this->getEventForm();
        if ($total_booking > 0) {
            unset($event_status[0]);
            $frm->getField('status')->options = $event_status;
        }
        $frm->getField('btn_submit')->value = Info::t_lang('UPDATE_EVENT');
        $frm->getField('btn_submit')->htmlAfterField = '<p>' . Info::t_lang('THERE_ARE_') . count($total_booking) . ' ' . Info::t_lang('BOOKING(S)_FOR_FUTURE') . '</p>';
        $frm->getField('btn_submit')->developerTags['col'] = 6;
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('formHeader', Info::t_lang('UPDATE_A_EVENT'));
        $this->_template->render(false, false, 'activities/_partial/new-event.php', false, true);
    }

    function newEvent($dt)
    {

        $frm = $this->getEventForm();
        $data = array(
            'date' => urldecode($dt)
        );
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('formHeader', Info::t_lang('ADD_A_NEW_EVENT'));
        $this->_template->render(false, false, 'activities/_partial/new-event.php', false, true);
    }

    private function getEventForm()
    {
        $frm = new Form('frmEvent');
        $frm->addHiddenField('', 'event_id');
        $frm->addRadioButtons(Info::t_lang('SERVICE_AVAILABLE_AT_ANY_TIME'), 'service_type', Info::getIs(), "1", array('class' => 'list list--horizontal'), array("onclick" => "onChangeTimeOption();", "class" => "serviceopt-type"));

        //$frm->addSelectBox('', 'hour',Info::hours(),'',array(),'HH')->fieldWrapper = array("<div class='time-opt' style='display:none'>","</div>");

        $fld = $frm->addSelectBox('', 'hour', Info::hours(), '', array(), 'HH');
        $fld->addWrapperAttribute('class', 'time-opt');
        $fld->addWrapperAttribute('style', 'display:none');
        //$frm->addSelectBox('', 'minute',Info::minutes(),'',array(),'MM')->fieldWrapper = array("<div class='time-opt' style='display:none'>","</div>");
        $fld = $frm->addSelectBox('', 'minute', Info::minutes(), '', array(), 'MM');
        $fld->addWrapperAttribute('class', 'time-opt');
        $fld->addWrapperAttribute('style', 'display:none');

        $frm->addRadioButtons(Info::t_lang('PRIOR_CONFIRMATION'), 'confirm_type', Info::getIs(), "1", array('class' => 'list list--horizontal'));
        $frm->addHiddenField('', 'date');
        $frm->addSelectBox(Info::t_lang('STATUS'), 'status', Info::getEventStatus(), 1, array(), '');

        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD EVENT'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    function eventAction($activity_id)
    {
        $event_id = 0;

        $post = FatApp::getPostedData();
        $e = new Activity();
        $ord = new Order();
        $eventType = $e->getEventTypeByDate($activity_id, $post['date']);
        if (!empty($post['event_id'])) {

            $event_id = FatUtility::int($post['event_id']);
            if ($event_id <= 0) {
                FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
            }
            $event_data = $e->getEvent($event_id);
            if (empty($event_data)) {
                FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
            }

            $event_bookings = $ord->getEventBooking($event_id);
            $total_booking = count($event_bookings);
            if ($total_booking > 0 && $post['status'] == 0) {
                FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
            }
            $event = $e->getEvent($event_id);
        }
        if ($eventType == 1 && $post['service_type'] == 1 && $event_id <= 0) {
            FatUtility::dieJsonError("Can't Add More event on this date");
        }

        if (isset($post) && !empty($post)) {
            if ($post['service_type'] == 1) {
                $tslot = date('Y-m-d', strtotime($post['date'])) . ' 00:00:00';
            } else {
                $tslot = date('Y-m-d', strtotime($post['date'])) . ' ' . $post['hour'] . ':' . $post['minute'] . ':' . '00';
            }

            $array['activityevent_activity_id'] = $activity_id;
            $array['activityevent_time'] = $tslot;
            $array['activityevent_anytime'] = $post['service_type'];
            $array['activityevent_confirmation_requrired'] = $post['confirm_type'];
            $array['activityevent_status'] = $post['status'];

            if ($e->addTimeSlot($array, $event_id)) {
                if ($event_id > 0 && $total_booking > 0) {
                    Sms::sendActivityEventUpdateNotification($activity_id, $event_id, $array, $event, $event_bookings);
                }
                FatUtility::dieJsonSuccess("Event Added");
            }
        }
        FatUtility::dieJsonError("Something Went Wrong!");
    }

    function deleteAllEvent($activity_id)
    {

        $post = FatApp::getPostedData();
        $month = $post['month'];
        $year = $post['year'];
        $start_datetime = strtotime($year . '-' . $month . '-01');
        $end_datetime = strtotime(date('Y-m-t', strtotime($year . '-' . $month)));
        $e = new Activity();
        $ord = new Order();
        for ($i = $start_datetime; $i <= $end_datetime; $i = ($i + (60 * 60 * 24))) {
            $date = date('Y-m-d', $i);

            $events = $e->getActivityEventByDate($activity_id, $date);

            if (!empty($events)) {
                foreach ($events as $event) {
                    if (!$ord->isEventBooked($event['activityevent_id'])) {
                        if (!$e->removeEventByDate($activity_id, $date)) {
                            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
                        }
                    }
                }
            }
        }
        FatUtility::dieJsonSuccess("Event Removed");
    }

    ///////////////////////////////////////////// step 7 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step7($activity_id)
    {
        $post = FatApp::getPostedData();
        $act = new Activity();
        if (intval($activity_id) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $addons = $act->getActivityAddons($activity_id);
        $this->set('arr_listing', $addons);
        $html = $this->_template->render(false, false, 'activities/_partial/addons.php', true, true);


        die(FatUtility::convertToJson(array('status' => 1, 'msg' => $html, 'canEdit' => FatUtility::int($this->canEdit))));
    }

    function getAddOnForm($activity_id)
    {
        $post = FatApp::getPostedData();
        $activity_id = FatUtility::int($activity_id);
        if ($activity_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $addon_id = isset($post['addon_id']) ? FatUtility::int($post['addon_id']) : 0;

        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($activity_id, $addon_id);
        if (count($addon) > 0) {
            $addon = array_merge($addon, array('activity_id' => $activity_id));
        } else {
            $addon = array('activity_id' => $activity_id);
        }

        $form = $this->getStep7();
        $form->fill($addon);
        $this->set('frm', $form);
        $form = $this->_template->render(false, false, 'activities/_partial/addons-form.php', true, true);
        FatUtility::dieJsonSuccess($form);
    }

    private function getStep7()
    {
        $frm = new Form('frmAddons');
        $frm->addHiddenField('', 'activityaddon_id');
        $frm->addHiddenField('', 'activity_id');
        $frm->addRequiredField(Info::t_lang('ADD-ON_TITLE'), 'activityaddon_text');
        $frm->addRequiredField(Info::t_lang('ADD-ON_PRICE'), 'activityaddon_price');
        $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activityaddon_comments');
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    public function setup7()
    {

        $post = FatApp::getPostedData();
        $activity_id = @$post['activity_id'];
        $activity_id = FatUtility::int($activity_id);
        if ($activity_id <= 0) {
            FatUtility::dieJsonError('Invalid Request');
        }
        $data['activityaddon_activity_id'] = $activity_id;
        $addon_id = FatUtility::int($post['activityaddon_id']);
        $data['activityaddon_text'] = $post['activityaddon_text'];
        $data['activityaddon_price'] = $post['activityaddon_price'];
        $data['activityaddon_comments'] = $post['activityaddon_comments'];


        $act = new Activity();
        if ($act->addActivityAddons($data, $addon_id)) {
            FatUtility::dieJsonSuccess("Addon Added");
        } else {
            FatUtility::dieJsonError("Something went wrong, please try again");
        }
    }

    public function removeAddons()
    {
        $post = FatApp::getPostedData();
        $activity_id = @$post['activity_id'];
        $activity_id = FatUtility::int($activity_id);
        if ($activity_id == 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $act = new Activity();
        $act->removeActivityAddons($activity_id, $post['addon_id']);
        FatUtility::dieJsonSuccess("Addon Deleted");
    }

    function changeHost()
    {
        $post = FatApp::getPostedData();
        $activity_id = @$post['activity_id'];
        $host_id = @$post['host_id'];
        $host_id = FatUtility::int($host_id);
        $activity_id = FatUtility::int($activity_id);
        if ($activity_id <= 0 || $host_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $data['activity_user_id'] = $host_id;
        $act = new Activity($activity_id);
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }
        FatUtility::dieJsonSuccess('Host Changed Successfully!');
    }

    function addonImages($activity_id)
    {

        $post = FatApp::getPostedData();
        $addon_id = isset($post['addon_id']) ? FatUtility::int($post['addon_id']) : 0;
        if ($addon_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($activity_id, $addon_id);
        if (empty($addon)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $frm = $this->getAddonImageForm();
        $frm->fill(array('addon_id' => $addon_id));
        $images = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_ACTIVITY_ADDON, $addon_id, $activity_id, -1);

        $this->set('addon_id', $addon_id);
        $this->set('activity_id', $activity_id);
        $this->set('addon', $addon);
        $this->set('frm', $frm);
        $this->set('images', $images);
        $htm = $this->_template->render(false, false, 'activities/_partial/addon-images.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setupAddonImage($activity_id)
    {

        $post = FatApp::getPostedData();
        $addon_id = isset($post['addon_id']) ? FatUtility::int($post['addon_id']) : 0;
        if ($addon_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($activity_id, $addon_id);
        if (empty($addon)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }

        if (!isset($_FILES['addon_image']) || $_FILES['addon_image']['error'] != 0 || $_FILES['addon_image']['error'] != 'image/jpeg') {
            FatUtility::dieJsonError(Info::t_lang("WRONG_FORMAT_OF_IMAGE"));
        }
        $size = getimagesize($_FILES['addon_image']['tmp_name']);

        if ($size[0] < 1600 || $size[1] < 900) {
            FatUtility::dieJsonError(Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)"));
        }
        $attach = new AttachedFile();
        if (!$attach->saveAttachment($_FILES['addon_image']['tmp_name'], AttachedFile::FILETYPE_ACTIVITY_ADDON, $addon_id, $activity_id, $_FILES['addon_image']['name'])) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('IMAGE_UPLOADED!'), 'addon_id' => $addon_id));
    }

    private function getAddonImageForm()
    {
        $frm = new Form('frmAddonPhoto');
        $frm->addHiddenField('', 'addon_id');
        $fld = $frm->addFileUpload(Info::t_lang('IMAGE'), 'addon_image', array('title' => Info::t_lang("PLEASE_UPLOAD_AN_IMAGE"), 'id' => 'c-file-upload-input'));
        $fld->htmlAfterField = '<em>' . Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)") . '</em>';
        $fld->requirements()->setRequired();
        //$fld->htmlAfterField='<input type="text"><button type="button">Select File</button>';
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPLOAD'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    function removeAddonImage()
    {

        $post = FatApp::getPostedData();
        $image_id = isset($post['image_id']) ? FatUtility::int($post['image_id']) : 0;
        if ($image_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $file_data = AttachedFile::getAttachmentById($image_id);
        if (empty($file_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if (!AttachedFile::removeFile($image_id)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('IMAGE_DELETED!'), 'addon_id' => $file_data[AttachedFile::DB_TBL_PREFIX . 'record_id']));
    }

    public function setPopular()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $status = FatUtility::int($post['status']);
        $activity_id = FatUtility::int($post['activity_id']);
        if ($activity_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $status = $status <= 0 || $status > 1 ? 0 : $status;
        $act = new Activity($activity_id);
        $data[Activity::DB_TBL_PREFIX . 'popular'] = $status;
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }
        FatCache::delete(CACHE_HOME_FEATURED_ACTIVITIES);
        FatUtility::dieJsonSuccess('Record Updated!');
    }

    public function reviews($activity_id, $page = 1)
    {
        if (!$this->canViewReview) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $activity_id = FatUtility::int($activity_id);
        $page = FatUtility::int($page);
        $page = $page < 1 ? 1 : $page;
        $pagesize = static::PAGESIZE;
        if ($activity_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $src = Reviews::getSearchObject(true);
        $src->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'user_id');
        $src->joinTable(Activity::DB_TBL, 'left join', Activity::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'type_id');
        $src->joinTable(AbuseReport::DB_TBL, 'left join', AbuseReport::DB_TBL_PREFIX . 'record_id = ' . Reviews::DB_TBL_PREFIX . 'id and ' . AbuseReport::DB_TBL_PREFIX . 'record_type = 0');
        $src->setPageNumber($page);
        $src->setPageSize($pagesize);
        $src->addCondition(Reviews::DB_TBL_PREFIX . 'type_id', '=', $activity_id);
        $src->addMultipleFields(array(
            Reviews::DB_TBL . '.*',
            AbuseReport::DB_TBL . '.*',
            Activity::DB_TBL_PREFIX . 'name',
            'concat(' . User::DB_TBL_PREFIX . 'firstname, " ", ' . User::DB_TBL_PREFIX . 'lastname) as user_name',
            'count(' . ReviewMessage::DB_TBL_PREFIX . 'id) as numMessages',
            'group_concat(' . ReviewMessage::DB_TBL_PREFIX . 'user_type) replyUserTypes',
                )
        );
        $src->addOrder(Reviews::DB_TBL_PREFIX . 'date', 'desc');
        $rs = $src->getResultSet();
        //echo $src->getQuery();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $src->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "activities/_partial/review-list.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function reviewForm()
    {
        if (!$this->canEditReview) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if ($activity_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $review_id = isset($post['review_id']) ? FatUtility::int($post['review_id']) : 0;
        $frm = $this->getReviewForm();
        $fields = array();
        if ($review_id > 0) {
            $reviewObj = new Reviews($review_id);
            $reviewObj->loadFromDb();
            $fields = $reviewObj->getFlds();

            if ($fields[Reviews::DB_TBL_PREFIX . 'user_id'] > 0) {
                $frm->removeField($frm->getField('review_user_name'));
                //$frm->removeField($frm->getField('show_image'));
                //$frm->removeField($frm->getField('image'));
            } else {
                //$frm->getField('show_image')->value='<img src="'.FatUtility::generateUrl('image', 'user', array($review_id, 100, 100,1),'/').'">';
            }
        } else {
            $fields[Reviews::DB_TBL_PREFIX . 'type_id'] = $activity_id;
        }
        $frm->fill($fields);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'activities/_partial/review-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function reviewAction()
    {
        if (!$this->canEditReview) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getReviewForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post == false) {
            FatUtility::dieJsonError('Something went wrong');
        }
        $review_id = FatUtility::int($post['review_id']);
        $review_rating = $post['review_rating'];
        $review_rating = $review_rating > 5 ? 5 : $review_rating;
        $review_rating = $review_rating < 0 ? 0 : $review_rating;


        if ($review_id <= 0) {
            $post[Reviews::DB_TBL_PREFIX . 'date'] = Info::currentDatetime();
        }
        unset($post['review_id']);
        $reviewObj = new Reviews($review_id);
        $reviewObj->assignValues($post);

        if (!$reviewObj->save()) {
            FatUtility::dieJsonError('Something went wrong');
        }
        $review_id = $reviewObj->getMainTableRecordId();

        if (!empty($_FILES['image']['tmp_name'])) {
            $attachment = new AttachedFile();
            if ($attachment->saveImage($_FILES['image']['tmp_name'], AttachedFile::FILETYPE_DUMMY_REVIEW_USER, $review_id, 0, $_FILES['image']['name'], 0, true)) {
                FatUtility::dieJsonSuccess('Photo Updated!');
            } else {
                FatUtility::dieJsonError($attachment->getError());
            }
        }
        FatUtility::dieJsonSuccess('Record Updated');
    }

    private function getReviewForm()
    {
        if (!$this->canEditReview) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = new Form('reviewForm');
        $frm->addHiddenField('', 'review_id');
        $frm->addHiddenField('', 'review_type');
        $frm->addHiddenField('', 'review_type_id');
        $frm->addHiddenField('', 'review_user_id');
        //$image = $frm->addHtml('User Image','show_image','<img src="'.FatUtility::generateUrl('image','user',array(0,100,100), '/').'">');
        //$fileUpload = $frm->addFileUpload('','image');
        //$image->attachField($fileUpload);
        $frm->addTextBox('User Name', 'review_user_name');
        $frm->AddTextArea('Content', 'review_content')->requirements()->setRequired();
        $review_rating = $frm->addSelectBox('Rating', 'review_rating', Info::getRatingArray(), 0.5, array(), '');
        //$review_rating->htmlAfterField ='<em>Rating must be 1 to 5 </em>';
        $frm->addSelectBox('Status', 'review_active', Info::getReviewStatus());
        $frm->addSubmitButton('', 'submit_btn', 'ADD / UPDATE');
        return $frm;
    }

    public function getHostActivities($hostId)
    {
        $srch = Activity::getSearchObject();
        $srch->addCondition('activity_user_id', '=', $hostId);
        $srch->addFld(array(
            'activity_id',
            'activity_name'
        ));
        $rs = $srch->getResultSet();

        $list = FatApp::getDb()->fetchAllAssoc($rs);
        FatUtility::dieJsonSuccess(array('msg' => $list));

    }	

}
