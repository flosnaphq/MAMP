<?php

class HostactivityController extends UserController {

    private $activityId;

    public function __construct($action) {
        parent::__construct($action);

        $this->activityId = $this->getCurrentActivity();
        $this->set("class", "is--dashboard");
        $this->set("action", $action);
        $this->set("controller", 'hostactivity');
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    private function setCurrentActivity($activityId = 0) {
        $act = new Activity();
        if ($activityId != 0) {
            if (!$act->isAssociatedActvity($activityId, $this->userId)) {
                Message::addErrorMessage("In Valid Operation! Login and Try Again...");
                FatApp::redirectUser(FatUtility::generateUrl('user', 'logout'));
            }
        }
        $_SESSION['activity_id'] = $activityId;
    }

    private function getCurrentActivity() {
        if (isset($_SESSION['activity_id'])) {
            return $_SESSION['activity_id'];
        }
        return 0;
    }

    public function index() {
        $brcmb = new Breadcrumb();
        //	$brcmb->add(Info::t_lang('Account'));
        $brcmb->add(Info::t_lang('LISTING'));
        $brcmb->add(Info::t_lang('MANAGE_LISTING'));
        $this->set('breadcrumb', $brcmb->output());
        $this->set('confirm_status', Info::getActivityConfirmStatus());
        $this->set('status', Info::getStatus());
        $this->_template->render();
    }

    public function listing() {
        $pagesize = static::PAGESIZE;
        //$pagesize=1;

        $data = FatApp::getPostedData();
        $confirm_status = isset($data['confirm_status']) ? FatUtility::int($data['confirm_status']) : -1;
        $status = isset($data['status']) ? FatUtility::int($data['status']) : -1;
        $e = new Activity();
        $search = Activity::getSearchObject();
        $search->joinTable('tbl_attached_files', 'LEFT JOIN', 'afile_record_id = activity_id and afile_type = ' . AttachedFile::FILETYPE_ACTIVITY_PHOTO);
        $search->addCondition('activity_user_id', '=', $this->userId);
        if ($status > -1) {
            $search->addCondition('activity_active', '=', $status);
        }

        if ($confirm_status > -1) {
            $search->addCondition('activity_confirm', '=', $confirm_status);
        }
        $search->addMultipleFields(array('tbl_activities.*', 'substring_index(group_concat(afile_id),",",3) as activity_images'));
        $search->addGroupBy('activity_id');
        $search->addOrder('activity_id', 'desc');
        $page = $data['page'];
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "hostactivity/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function update($actvityId = 0) {
        $actvityId = intval($actvityId);
        $e = new Activity();
        if ($e->checkUserActivity($actvityId, $this->userId)) {
            $this->setCurrentActivity($actvityId);
        } else {
            $this->setCurrentActivity(0);
        }
        FatApp::redirectUser(FatUtility::generateUrl('manage-activity'));
    }

    public function view() {
        
    }

    public function action() {
        $block = new Block();
        $meeting_points = $block->getBlock(18);
        $invite_team = $block->getBlock(19);
        $brcmb = new Breadcrumb();
        //	$brcmb->add(Info::t_lang('Account'));
        $brcmb->add(Info::t_lang('LISTING'));
        $brcmb->add(Info::t_lang('ADD_LISTING'));
        $this->set('breadcrumb', $brcmb->output());
        $this->set('meeting_points', $meeting_points);
        $this->set('invite_team', $invite_team);
  
        $this->_template->addJs(array('js/cropper.min.js', 'js/croper.js','js/google-places-auto.js'));
        $this->_template->addCss(array('css/cropper.min.css', 'css/croper.css'));
        $this->_template->render();
    }

    public function subService() {
        $post = FatApp::getPostedData();
        $services = Services::getCategories($post['service_id']);
        $option = "<option value=''>Select</option>";
        foreach ($services as $k => $v) {
            $option .= "<option value='{$k}'>{$v}</option>";
        }
        FatUtility::dieJsonSuccess($option);
    }

    public function defaultImage() {
        $post = FatApp::getPostedData();
        $data['activity_image_id'] = $post['image_id'];
        $act = new Activity($this->activityId);
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        FatUtility::dieJsonSuccess("Images Set as Default Image");
    }

    ///////////////////////////////////////////// step 1 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step1() {
		echo 'In Step 1';
        $e = new Activity();
        $block = new Block();
        $commission_chart = CommissionChart::getCommissionChart();
        $start_date_end_ins = $block->getBlock(12);
        $this->set('start_date_end_ins', $start_date_end_ins);
        $frm = $this->getStep1($this->activityId);

        $this->set('frm', $frm);
        $this->set('commission_chart', $commission_chart);
        $frm->getField('btn_submit')->attachField($frm->addButton('', 'button', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'onclick' => 'step2("#second-tb")')));
        if ($this->activityId != 0) {

            $e = new Activity($this->activityId);
            if (!$e->loadFromDb()) {

                FatUtility::dieWithError('Error! ' . $e->getError());
            }
            $flds = $e->getFlds();
            $flds['activity_display_price'] = ($flds['activity_display_price']==0.00)?"":$flds['activity_display_price'];
            $cityData = City::getCityById($flds['activity_city_id']);
            if ($cityData) {
                $flds['activity_country_id'] = $cityData['city_country_id'];
                $flds['activity_region_id'] = $cityData['country_region_id'];
                $frm->getField('activity_city_id')->options = City::getAllCitiesByCountryId($cityData['city_country_id']);
				$frm->getField('activity_country_id')->options = Country::getAllCountryByRegionId($cityData['country_region_id']);
            }


            $act_attributes = $e->getActivityAttributeRelations($this->activityId);
            $attributes = ActivityAttributes::getAttributes();
            if (!empty($attributes)) {
                foreach ($attributes as $attr_id => $attr) {
                    if (isset($act_attributes[$attr_id])) {
                        $attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $this->activityId);

                        $check_box_fld = $frm->getField('attr[' . $attr_id . ']');
                        $check_box_fld->checked = 1;
                        if ($attr[ActivityAttributes::DB_TBL_PREFIX . 'file_required'] == 1) {
                            $check_box_fld->htmlAfterField = '<br><a class="link" href="' . FatUtility::generateUrl('image', 'attribute', array($attr_id, $this->activityId)) . '" target="_blank">' . $attachment[AttachedFile::DB_TBL_PREFIX . 'name'] . '</a>';
                            $frm->getField('attr_file_' . $attr_id)->setWrapperAttribute('style', 'display:block');
                        }
                    }
                }
            }
            if (!empty($flds)) {
                $categoryId = Services::getParentCateogry($flds['activity_category_id']);
                $flds['category_id'] = $categoryId;
                $cats = Services::getCategories($categoryId);
                $fld = $frm->getField('activity_category_id');
                $fld->options = $cats;
                if ($flds['activity_booking'] > 24) {

                    $flds['booking_days'] = $flds['activity_booking'] / 24;
                    $flds['activity_booking'] = 100;
                }
                if ($flds['activity_duration'] > 23) {

                    $flds['duration_days'] = $flds['activity_duration'] / 24;
                    $flds['activity_duration'] = 100;
                }
                $frm->fill($flds);
            }
        }

        $activity_price = $frm->getField('activity_price');
        $activity_price->htmlAfterField = '<div class = "field_add-on add-on--right">' . $frm->getField('activity_price_type')->getHtml() . '</div>';

        $activity_price->htmlAfterField .='<div><a href="#commission_rate" class = "link field-tool-tip js-commission-popup" >' . Info::t_lang('COMMISSION_PERCENTAGE?') . '</a></div>';
        $frm->removeField($frm->getField('activity_price_type'));
        $html = $this->_template->render(false, false, 'hostactivity/_partial/step1.php', true, true);
        die(FatUtility::convertToJson(array('html' => $html)));
    }

    private function getStep1($record_id = 0) {
        $frm = new Form('frmRegister');
        //	$frm->addHiddenField('', 'user_id', 0, array('id'=>'user_id'));
        $fld = $frm->addRequiredField(Info::t_lang('ACTIVITY_NAME'), 'activity_name');

        //Add Slug Functionality
        if ($record_id > 0) {
            $slugHtml = '<a class="link modaal-ajax" href="' . Route::getRoute('hostactivity', 'editSlug') . '">Edit Activity Slug</a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }
		 $frm->addSelectBox(Info::t_lang('REGION'), 'activity_region_id', Region::getRegions(), '', array('onChange' => 'getCountries(this.value)'));


        $frm->addSelectBox(Info::t_lang('COUNTRY'), 'activity_country_id',array(), '', array('onChange' => 'getCities(this.value)','id' => 'countries'));

        $fld = $frm->addSelectBox(Info::t_lang('CITY'), 'activity_city_id', '', '', array('id' => 'cities'));
        $requestLink = '<a href="' . Route::getRoute('UserRequest', 'create') . '" class="modaal-ajax link" >' . Info::t_lang('REQUEST_FOR_CITY') . '</a>';
        $fld->htmlAfterField = $requestLink;

        $frm->addSelectBox(Info::t_lang('ACTIVITY_TYPE'), 'category_id', Services::getCategories(), '', array('onchange' => 'getSubService(this)'));
        $frm->addSelectBox(Info::t_lang('CATEGORY'), 'activity_category_id', array(), '', array('id' => 'subcat-list'));
        $activity_start_date = $frm->addDateField('', 'activity_start_date', "", array('title' => Info::t_lang('START_DATE'), 'id' => "activity_start_date"));
        $activity_start_date->requirements()->setRequired(); //date-instruction
        $activity_start_date->captionWrapper = array(Info::t_lang('START_DATE') . ' <a href="#date-instruction" class="date-popup"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');

        $activity_end_date = $frm->addDateField('', 'activity_end_date', "", array('title' => Info::t_lang('END_DATE')));
        $activity_end_date->requirements()->setRequired();
         $activity_end_date->requirements()->setCompareWith("activity_start_date", 'gt', Info::t_lang('START_DATE'));
        $activity_end_date->captionWrapper = array(Info::t_lang('END_DATE') . '  <a href="#date-instruction" class="date-popup"><svg class="icon icon--info   "><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');
        $fld = $frm->addIntegerField(Info::t_lang('MAX_TRAVELER'), 'activity_members_count', 1);
        $fld->requirements()->setRange(1, 999999999);
        $fld->requirements()->setCustomErrorMessage(Info::t_lang('MAX_TRAVELER_ERROR'));

        $frm->addSelectBox(Info::t_lang('BOOKINGS_ACCEPTED'), 'activity_booking', Info::activityBookings(), '', array('id' => 'booking-day', 'onChange' => "changeBooking(this)"))->requirements()->setRequired();

        $fld = $frm->addIntegerField(Info::t_lang('NO_OF_DAYS'), 'booking_days', 0);



        $fld->fieldWrapper = array('<div id="booking-day-field">', '</div>');

        $frm->addSelectBox(Info::t_lang('ACTIVITY_DURATION'), 'activity_duration', Info::activityDuration(), '', array('id' => 'duration-day', 'onChange' => "changeDuration(this)"))->requirements()->setRequired();

        $fld = $frm->addIntegerField(Info::t_lang('NO_OF_DAYS'), 'duration_days', 1);
        $fld->fieldWrapper = array('<div id="duration-day-field">', '</div>');
        $fld->requirements()->setRange(1, 999999999);
        $fld->requirements()->setCustomErrorMessage(Info::t_lang('NO_OF_DAYS_ERROR'));
        $cur = new Currency();
        $currency = $cur->getAttributesById(FatApp::getConfig('conf_default_currency'));
        $fld = $frm->addRequiredField(Info::t_lang('ACTIVITY_PRICE(Inc. Taxes)'), 'activity_price');
        $fld->requirements()->setRange(1, 999999999);
        $fld->requirements()->setCustomErrorMessage(Info::t_lang('ACTIVE_PRICE_ERROR'));
        $fld->htmlBeforeField = '<div class = "field_add-on add-on--left">' . @$currency['currency_symbol'] . '</div>';
        $price_type = $frm->addSelectBox(Info::t_lang('ACTIVITY_PRICE_AS_PER'), 'activity_price_type', Info::activityType(), 0, array(), '');

        $fld = $frm->addTextBox(Info::t_lang('ACTIVITY_DISPLAY_PRICE'), 'activity_display_price');
        $fld->requirements()->setRange(0, 999999999);
        $fld->captionWrapper = array('', ' <a href="#activity-display-price" class="js-activity-display-price"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg></a>');
      //  $displayPriceRequirement = new FormFieldRequirement('activity_display_price',Info::t_lang('ACTIVITY_DISPLAY_PRICE'));
       // $displayPriceRequirement->setCompareWith("activity_price", 'gt', Info::t_lang('ACTIVITY_PRICE'));
        
       // $fld->requirements()->addOnChangerequirementUpdate('0','gt','activity_display_price',$displayPriceRequirement);
      //  $fld->requirements()->removeOnChangerequirementUpdate('','eq');
        
        
        $fld = $frm->addSelectBox('', 'activity_active', Info::getStatus(), '0', array(), '');
        $fld->captionWrapper = array(Info::t_lang('STATUS') . ' <a href="#status-info" class="js-status-info"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');
        $fld = $frm->addSelectBox('', 'activity_booking_status', Info::getAvtivityBookingStatus(), '0', array(), '');
        $fld->captionWrapper = array(Info::t_lang('AVAILABLE_FOR_BOOKING') . ' <a href="#booking-status-info" class="js-booking-status-info"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');

        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SAVE'), array('class' => 'button button--small button--fill button--red'));
        return $frm;
    }

    public function setup1() {

        $frm = $this->getStep1();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post, array('activity_category_id', 'activity_city_id'));

        if ($post['activity_booking'] == 100) {
            $post['activity_booking'] = $post['booking_days'] * 24;
        }

        if ($post['activity_duration'] == 100) {
            $post['activity_duration'] = $post['duration_days'] * 24;
        }
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['activity_user_id'] = $this->userId;
        $newActivity = false;
        if ($this->activityId == 0) {
            $post['activity_confirm'] = 0;
            $newActivity = true;
            
        }

    
        $activity_data = array();
        $act = new Activity($this->activityId);
        if ($this->activityId > 0) {
            $act->loadFromDb();
            $activity_data = $act->getFlds();
        }
        $act->assignValues($post);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }
        $attr_relation = array();
        $attachFile = new AttachedFile();
 
    


        $usr = new User($this->userId);
        $usr->loadFromDb();
        $host_data = $usr->getFlds();
        $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
        $host_name = ucwords($host_name);

        $city = City::getCityById($post['activity_city_id']);
        $city_name = $city['city_name'];

        $vars = array(
            '{host_name}' => ucwords($host_name),
            '{city_name}' => ucfirst($city_name),
            '{activity_name}' => $post['activity_name'],
            '{activity_price}' => Currency::displayPrice($post['activity_price']),
            '{activity_start_date}' => FatDate::format($post['activity_start_date']),
            '{activity_end_date}' => FatDate::format($post['activity_end_date']),
        );
        $notify = new Notification();
        if ($this->activityId >= 0) {
            Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 16, $vars);
            $notify_msg = Info::t_lang('NEW_ACTIVITY_ADDED_BY_') . ': ' . $host_name;
            $notify->notify(0, 0, '', $notify_msg);
        }


        $_SESSION['activity_id'] = $newActivityId = $act->getMainTableRecordId();
        
        if ($newActivity) {
            $route = new Routes();
            $routeData = array(
                'url_rewrite_custom' => Info::getSlugFromName($post['activity_name']),
                'url_rewrite_record_id' => $newActivityId,
                'url_rewrite_subrecord_id' => 0,
                'url_rewrite_record_type' => Route::ACTIVITY_ROUTE,
            );
            $route->createNewRoute($routeData);
        }
        
        FatUtility::dieJsonSuccess("Step1 Completed");
    }

    ///////////////////////////////////////////// step 2 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step2() {
        $act = new Activity();
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $images = $act->getActivityImages($this->activityId);
        $e = new Activity($this->activityId);
        $e->loadFromDb();
        $flds = $e->getFlds();
        $this->set('activity_detail', $flds);
        $this->set('images', $images);
        $html = $this->_template->render(false, false, 'hostactivity/_partial/images.php', true, true);
        $form = $this->getStep2();
        $this->set('frm', $form);
        $form = $this->_template->render(false, false, 'hostactivity/_partial/img-form.php', true, true);
        die(FatUtility::convertToJson(array('status' => 1, 'form' => $form, 'html' => $html)));
    }

    private function getStep2() {
        $frm = new Form('frmPhoto');
   

        $html = '<em>' . Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)") . ' ' . sprintf(Info::t_lang('Max_Image_Size'), Helper::maxFileUpload(true)) . '</em></br>';
       
        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step1("#first-tb")'));
		
        $fld->attachField($frm->addHtml('', 'btn_submit',"<a class='button button--small button--fill button--red modaal-ajax' href='".FatUtility::generateUrl('croper','load')."'>Upload</a>"));
        $fld->attachField($frm->addButton('', 'button2', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'onclick' => 'step3("#third-tb")')));
        return $frm;
    }

    public function setup2() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }

        if (is_uploaded_file($_FILES['activity_image']['tmp_name'])) {
            if ($_FILES['activity_image']['size'] > Helper::maxFileUpload(true, false)) {
                FatUtility::dieJsonError('Max image upload size is: ' . Helper::maxFileUpload(true));
            }

            $size = getimagesize($_FILES['activity_image']['tmp_name']);

            if ($size[0] < 1600 || $size[1] < 900) {
                FatUtility::dieJsonError(Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)"));
            }

            $attach = new AttachedFile();
            if ($attach->saveAttachment($_FILES['activity_image']['tmp_name'], AttachedFile::FILETYPE_ACTIVITY_PHOTO, $this->activityId, 0, $_FILES['activity_image']['name'], 0, false, 1)) {
                $e = new Activity($this->activityId);
                $e->loadFromDb();
                $flds = $e->getFlds();
                if ($flds['activity_image_id'] == 0) {
                    $data['activity_image_id'] = FatApp::getDb()->getInsertId();
                    $act = new Activity($this->activityId);
                    $act->assignValues($data);
                    $act->save();
                }
                $usr = new User($this->userId);
                $usr->loadFromDb();
                $host_data = $usr->getFlds();
                $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
                $host_name = ucwords($host_name);
                $notify = new Notification();
                $notify_msg = Info::t_lang('NEW_ACTIVITY_IMAGE_UPLOADED_BY_') . ': ' . $host_name;
                $notify_msg .=Info::t_lang('_REGARDING_ACTIVITY_:-') . $flds['activity_name'];
                $notify->notify(0, 0, '', $notify_msg);
                FatUtility::dieJsonSuccess("image Uploaded");
            } else {
                FatUtility::dieJsonError("Something went wrong, please try again");
            }
        } else {
            FatUtility::dieJsonError(Info::t_lang("Invalid_image_uploaded"));
        }
    }

    public function removeImage() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        Helper::deleteFileByRecord($post['file_id'], $this->activityId);
        $e = new Activity($this->activityId);
        $e->loadFromDb();
        $flds = $e->getFlds();
        if ($flds['activity_image_id'] == $post['file_id']) {
            $images = AttachedFile::getAttachment(AttachedFile::FILETYPE_ACTIVITY_PHOTO, $this->activityId);
            if (!empty($images)) {
                $data['activity_image_id'] = $images['afile_id'];
            } else {
                $data['activity_image_id'] = 0;
            }
            $act = new Activity($this->activityId);
            $act->assignValues($data);
            $act->save();
        }
        FatUtility::dieJsonSuccess("Image Deleted");
    }

    ///////////////////////////////////////////// step 3 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step3() {
        $act = new Activity();
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $videos = $act->getActivityVideos($this->activityId);
        $this->set('videos', $videos);
        $html = $this->_template->render(false, false, 'hostactivity/_partial/videos.php', true, true);
        $form = $this->getStep3();
        $this->set('frm', $form);
        $form = $this->_template->render(false, false, 'hostactivity/_partial/video-form.php', true, true);
        die(FatUtility::convertToJson(array('status' => 1, 'form' => $form, 'html' => $html)));
    }

    private function getStep3() {
        $frm = new Form('frmVideo');
        $fld = $frm->addRequiredField(Info::t_lang('URL'), 'activity_video');
        $valid_domain = Info::validVideoDomains();
        $valid_domain = implode(', ', $valid_domain);
        $fld->htmlAfterField = '<i> ' . $valid_domain . ' </i>';

        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step2("#second-tb")'));
        $fld->attachField($frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD_VIDEO'), array('class' => 'button button--small button--fill button--red')));
        $fld->attachField($frm->addButton('', 'button2', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'onclick' => 'step4("#fourth-tb")')));
        return $frm;
    }

    public function setup3() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $act = new Activity($this->activityId);
        $act->loadFromDb();
        $flds = $act->getFlds();
        $post = FatApp::getPostedData();
        $data['activityvideo_activity_id'] = $this->activityId;
        $url = $post['activity_video'];
        $valid_domain = Info::validVideoDomains();
        $valid_domain = implode(', ', $valid_domain);
        if (!$act->isValidVideoUrl($url)) {
            FatUtility::dieJsonError(Info::t_lang('PLEASE_ENTER_VALID_URL.ONLY_') . $valid_domain . Info::t_lang('_VIDEOS_ACCEPTABLE'));
        }
        $data['activityvideo_url'] = $url;
        $video = Info::getVideoDetail($post['activity_video']);
        $data['activityvideo_videoid'] = $video['video_id'];
        $data['activityvideo_type'] = $video['video_type'];
        $data['activityvideo_thumb'] = $video['video_thumb'];
        $data['activityvideo_active'] = 1;


        if ($act->addActivityVideo($data)) {

            $usr = new User($this->userId);
            $usr->loadFromDb();
            $host_data = $usr->getFlds();
            $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
            $host_name = ucwords($host_name);
            $notify = new Notification();
            $notify_msg = Info::t_lang('NEW_ACTIVITY_VIEDO_UPLOADED_BY_') . ': ' . $host_name;
            $notify_msg .=Info::t_lang('_REGARDING_ACTIVITY_:-') . $flds['activity_name'];
            $notify->notify(0, 0, '', $notify_msg);
            FatUtility::dieJsonSuccess("Video Added");
        } else {
            FatUtility::dieJsonError("Something went wrong, please try again");
        }
    }

    public function removeVideo() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $act = new Activity();
        $post = FatApp::getPostedData();
        $act->removeActivityVideo($this->activityId, $post['file_id']);
        FatUtility::dieJsonSuccess("Video Deleted");
    }

    ///////////////////////////////////////////// step 4 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    public function step4() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $e = new Activity();
        $frm = $this->getStep4();
        $block = new Block();
        $cancellation_ins = $block->getBlock(11);
        $this->set('cancellation_ins', $cancellation_ins);
        $this->set('frm', $frm);

        $e = new Activity($this->activityId);
        if (!$e->loadFromDb()) {

            FatUtility::dieWithError('Error! ' . $e->getError());
        }
        $flds = $e->getFlds();
        $flds['activity_contacts'] = array(@$flds['activity_contacts']);
        if (!empty($flds)) {
            //$flds['activity_highlights'] = "<table><tr>	<th>What's included ?</th>	<td>Details</td></tr></table>";
            $flds['activity_languages'] = $e->getActivityLanguages($this->activityId);
            $frm->fill($flds);
        }

        $html = $this->_template->render(false, false, 'hostactivity/_partial/step4.php', true, true);
        die(FatUtility::convertToJson(array('html' => $html)));
    }

    private function getStep4() {
        $frm = new Form('frmExtra');
        $text_area_id = 'high_area';
        $editor_id = 'highlight_area';

        //$html = "<table>		<tr>			<th>What's included ?</th>			<td>Details</td>		</tr>		</table>";
        $html = "";
        $field = $frm->addTextArea(Info::t_lang('HIGHLIGHTS'), 'activity_highlights', $html, array('id' => $text_area_id, 'placeholder' => 'Describe your activity in 3-4 paragraphs'));
        $field->htmlBeforeField = '<div id="' . $editor_id . '"></div>' . Helper::getInnovaEditorObj($text_area_id, $editor_id) . '<small>' . Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS') . '</small>';
        //// Describe your activity in 3-4 paragraphs
        //$field->htmlAfterField ='<em>('.Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS').')</em>';
        //$field->captionWrapper =array('<em>('.Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS').')','</em>');
        //$field->developerTags['noCaptionTag']=true;
        $field->requirements()->setRequired(true);

        $text_area_id = 'text_area';
        $editor_id = 'editor_area';
        $field = $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activity_desc', '', array('id' => $text_area_id));
        $field->htmlBeforeField = '<div id="' . $editor_id . '"></div>' . Helper::getInnovaEditorObj($text_area_id, $editor_id) . '<small>' . Info::t_lang('DETAILED_DESCRIPTION_OF_THE_ACTIVITY') . '</small>';
        $field->requirements()->setRequired(true);

        $fld = $frm->addTextArea(Info::t_lang('INCLUSIONS'), 'activity_inclusions');
        $fld->htmlAfterField = '<small>' . Info::t_lang('PLEASE_PRESS_ENTER_FOR_NEW_LINE') . '</small>';
        $fld->requirements()->setRequired(true);
        $fld = $frm->addTextArea(Info::t_lang('REQUIREMENTS'), 'activity_requirements');
        $fld->htmlAfterField = '<small>' . Info::t_lang('PLEASE_PRESS_ENTER_FOR_NEW_LINE') . '</small>';
        $fld->requirements()->setRequired(true);

        $cancellation_policies = CancellationPolicy::getRecordByUserTypeForForm();

        $fld = $frm->addSelectBox('', 'activity_cancelation', $cancellation_policies, '', array(), Info::t_lang('SELECT_CANCELLATION_POLICY'));
        $fld->htmlAfterField = '<a href="' . FatUtility::generateUrl('CancellationPolicy') . '" target="_blank" class="link">' . Info::t_lang('HOW_WE_HANDLE_CANCELLATIONS') . '</a>';
        $fld->requirements()->setRequired();

        $fld->captionWrapper = array(Info::t_lang('CANCELLATION_POLICY') . ' <a href="#cancellation-policy-instruction" class="link cancellation-popup"><svg class="icon icon--info   "> <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg></a>', '');

        //$frm->addTextArea(Info::t_lang('INSTRUCTIONS'),'activity_instructions')->htmlAfterField = '<span>Tip - Please Split Enter For New Line</span>';//Shared by host after booking
       // $activity_contacts = $frm->addCheckBoxes('', 'activity_contacts', Info::activityMeetingPoints(), array(1), array("class" => "list list--12 list--horizontal"));

        //$activity_contacts->captionWrapper = array(Info::t_lang('LOCATION') . ' <a href="#meeting-points-instruction" class="meeting-points"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');
        $frm->addCheckBoxes(Info::t_lang('LANGUAGES'), 'activity_languages', Languages::getAllLang(), array(), array("class" => "list list--3 list--horizontal"));


        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step3("#third-tb")'));
        $fld->attachField($frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPDATE'), array('class' => 'button button--small button--fill button--red')));
        $fld->attachField($frm->addButton('', 'button', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'onclick' => 'step5("#fifth-tb")')));
        return $frm;
    }

    public function setup4() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $frm = $this->getStep4();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post, array('activity_languages'));
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['activity_contacts'] = @$post['activity_contacts'][0];
        $act = new Activity($this->activityId);
        $act->assignValues($post);
        if (!$act->save()) {
            FatUtility::dieJsonError($act->getError());
        }
        $act->deleteActivityLanguages($this->activityId);

        foreach ($post['activity_languages'] as $lang_id) {
            $arr['activitylanguage_activity_id'] = $this->activityId;
            $arr['activitylanguage_language_id'] = $lang_id;

            $act->addActivityLanguage($arr);
        }

        FatUtility::dieJsonSuccess("Step4 Completed.");
    }

    ///////////////////////////////////////////// step 5 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step5() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $e = new Activity($this->activityId);
        $e->loadFromDb();
        $flds = $e->getFlds();
        $city = City::getCityById($flds['activity_city_id']);
        if ($flds['activity_latitude'] == 0) {
            $lat = $city['city_latitude'];
            $long = $city['city_longitude'];
        } else {
            $lat = $flds['activity_latitude'];
            $long = $flds['activity_longitude'];
        }

        $this->set('lat', $lat);
        $this->set('long', $long);
        $frm = $this->getStep5();
        $frm->fill($flds);
        $this->set('frm', $frm);
        $html = $this->_template->render(false, false, 'hostactivity/_partial/map.php', true, true);
        die(FatUtility::convertToJson(array('status' => 1, 'html' => $html)));
    }

    private function getStep5() {
        $frm = new Form('frmCoords');
        $frm->addHiddenField('', 'activity_latitude', "", array('id' => 'act_lat'));
        $frm->addHiddenField('', 'activity_longitude', "", array('id' => 'act_long'));
        $fldH = $frm->addHiddenField('', 'verify_change', 1, array('id' => 'verify_change'));
        $fldH->requirements()->setRequired(true);
        $fldH->requirements()->setCustomErrorMessage(Info::t_lang('ADDRESS_SHOULD_BE_GOOGLE_VERIFIED'));
        
        $fld = $frm->addTextBox(Info::t_lang('Address'), 'activity_address', '', array('id' => 'autocomplete'));
        $fld->attachField($fldH);
        $fld->requirements()->setRequired(true);
        $html = '<div class="mapbox-container">
	<div id=\'map\' style="height:600px"></div>
</div><br/><br/>';
        $frm->addHtml('', '', $html);

        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step4("#fourth-tb")'));
        $fld2 = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPDATE'), array('class' => 'button button--small button--fill button--red'));
        $fld2->developerTags['noCaptionTag'] = true;
        $fld->attachField($fld2);
        $fld->attachField($frm->addButton('', 'button', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'onclick' => 'step6("#sixth-tb")')));
        return $frm;
    }

    public function setup5() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $frm = $this->getStep5();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $act = new Activity($this->activityId);
        $act->assignValues($post);
        if (!$act->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        FatUtility::dieJsonSuccess(Info::t_lang('LOCATION_HAS_BEEN_UPDATED!'));
    }

    ///////////////////////////////////////////// step 6 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step6() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $e = new Activity($this->activityId);
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
            $yr = $startyear;
            $month = $startmonth;
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

        //	var_dump($month);
        //	var_dump($yr);
//	$month = '12';
//	$yr = '2016';
        $currentDate = Info::currentDate();
        $c = new Calendar($month, $yr);
        $calendar = $c->generateMonthCalendar();

#		Info::test($calendar);exit;
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
                if ($cals[$k]['events'] = $e->getActivityEventByDate($this->activityId, date('Y-m-d', strtotime($date)))) {
                    //	Info::test($cals);
                    $class .= ' have-event';
                    /* foreach($cals[$k]['events'] as $eves){
                      if($eves['activityevent_confirmation_requrired'] == 1){
                      $class .= ' required'; continue;
                      }
                      } */
                }
            }
            $cals[$k]['class'] = $class;
        }

        $block = new Block();
        $prior_ins = $block->getBlock(13);
        $available_ins = $block->getBlock(14);
        $bulk_entry_ins = $block->getBlock(15);
        $this->set("prior_ins", $prior_ins);
        $this->set("available_ins", $available_ins);
        $this->set("bulk_entry_ins", $bulk_entry_ins);
        $this->set("year", $yr);
        $dt = DateTime::createFromFormat('!m', $month);

        $this->set("showmonth", $dt->format('M'));
        $this->set("month", $month);

        $this->set("next", $next);

        $this->set("prev", $prev);

        $this->set("calendar", $cals);

        /* $html = $this->_template->render(true,true,'hostactivity/_partial/step6.php'); */
        /* $html = $this->_template->render(false, false, 'hostactivity/_partial/step6.php', true, true); */
        $html = $this->_template->render(false, false, 'manage-activity/_partial/step6.php', true, true);
        die(FatUtility::convertToJson(array('status' => 1, 'html' => $html)));
    }

    private function getStep6() {
        
    }

    public function setup6() {
		
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
		
        $post = FatApp::getPostedData();
		
         	
/* 		echo '<pre>';
		print_r($post);die;  */
		
		
        $er = new Activity();
        $events = $er->getEventByMonth($this->activityId, $post['month'], $post['year']);
        if (!empty($events)) {
            FatUtility::dieJsonError(Info::t_lang('Bulk entry is only possible when there are no previous entries for this month'));
        }
        $e = new Activity($this->activityId);
        $e->loadFromDb();
        $flds = $e->getFlds();

        $strdDate = Calendar::getStartDate($flds['activity_start_date'], $post['month'], $post['year']);

        $endDate = strtotime(Calendar::getEndDate($flds['activity_end_date'], $post['month'], $post['year']));
        $post = FatApp::getPostedData();
        $enddt = strtotime(date("Y-m-d", strtotime("+1 day", $endDate)));
        if ($post['entry_type'] == "1") {
            for ($dt = strtotime($strdDate); $dt < $enddt; $dt = strtotime(date("Y-m-d", strtotime("+1 day", $dt)))) {

                foreach ($post['hour_slot'] as $k => $hour) {
                    $tslot = date('Y-m-d', $dt) . ' ' . $hour . ':' . $post['minute_slot'][$k] . ':' . '00';
                    $array['activityevent_activity_id'] = $this->activityId;
                    $array['activityevent_time'] = $tslot;
                    $array['activityevent_anytime'] = $post['service_type'];
                    $array['activityevent_status'] = 1;
					$array['activityevent_confirmation_requrired'] = $post['confirm_type'];
                    $e->addTimeSlot($array);
                }
            }
        }

        if ($post['entry_type'] == "2") {
            for ($dt = strtotime($strdDate); $dt < $enddt; $dt = strtotime(date("Y-m-d", strtotime("+1 day", $dt)))) {


                if (in_array((intval(date('w', $dt))), $post['weekdays'])) {
                    foreach ($post['hour_slot'] as $k => $hour) {
                        $tslot = date('Y-m-d', $dt) . ' ' . $hour . ':' . $post['minute_slot'][$k] . ':' . '00';
                        $array['activityevent_activity_id'] = $this->activityId;
                        $array['activityevent_time'] = $tslot;
                        $array['activityevent_anytime'] = $post['service_type'];
                        $array['activityevent_status'] = 1;
						$array['activityevent_confirmation_requrired'] = $post['confirm_type'];
						
                        $e->addTimeSlot($array);
                    }
                }
            }
        }

        FatUtility::dieJsonSuccess("Events Added!");
    }

    function deleteEvent() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $ord = new Order();
        if (isset($post) && intval($post['event_id']) != 0) {
            $e = new Activity();
            if (!$ord->isEventBooked($post['event_id'])) {
                $e->removeEvent($this->activityId, intval($post['event_id']));
                FatUtility::dieJsonSuccess("Event Removed");
            } else {
                FatUtility::dieJsonError(Info::t_lang('BOOKING_IS_BOOKED_FOR_THIS_EVENT._YOU_CAN_NOT_DELETE'));
            }
        }
        FatUtility::dieJsonError("Something Went Wrong!");
    }

    function editEvent($event_id) {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
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
        $activity = $act->getActivity($event_data['activityevent_activity_id'], -1);
        if (empty($activity)) {
            FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
        }
        if ($activity['activity_user_id'] != $this->userId) {
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
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('formHeader', Info::t_lang('UPDATE_A_EVENT'));
        $this->_template->render(false, false, 'hostactivity/_partial/new-event.php', false, true);
    }

    function newEvent($dt) {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $frm = $this->getEventForm();
        $data = array(
            'date' => urldecode($dt)
        );
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('formHeader', Info::t_lang('ADD_A_NEW_EVENT'));
        $this->_template->render(false, false, 'hostactivity/_partial/new-event.php', false, true);
    }

    private function getEventForm() {
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

        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD EVENT'), array('class' => 'button button--small button--fill button--red'));
        return $frm;
    }

    function eventAction() {
        $event_id = 0;
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $e = new Activity();
        $ord = new Order();
        $eventType = $e->getEventTypeByDate($this->activityId, $post['date']);
        if (!empty($post['event_id'])) {

            $event_id = FatUtility::int($post['event_id']);
            if ($event_id <= 0) {
                FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
            }
            $event_data = $e->getEvent($event_id);
            if (empty($event_data)) {
                FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
            }
            $activity = $e->getActivity($event_data['activityevent_activity_id'], -1);
            if (empty($activity)) {
                FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST!'));
            }
            if ($activity['activity_user_id'] != $this->userId) {
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

            $array['activityevent_activity_id'] = $this->activityId;
            $array['activityevent_time'] = $tslot;
            $array['activityevent_anytime'] = $post['service_type'];
			$array['activityevent_confirmation_requrired'] = $post['confirm_type'];
            $array['activityevent_status'] = $post['status'];
			
            if ($e->addTimeSlot($array, $event_id)) {
                if ($event_id > 0 && $total_booking > 0) {
                    Sms::sendActivityEventUpdateNotification($this->activityId, $event_id, $array, $event, $event_bookings);
                }
                FatUtility::dieJsonSuccess("Event Added");
            }
        }
        FatUtility::dieJsonError("Something Went Wrong!");
    }

    function deleteAllEvent() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }

        $post = FatApp::getPostedData();
        $month = $post['month'];
        $year = $post['year'];
        $start_datetime = strtotime($year . '-' . $month . '-01');
        $end_datetime = strtotime(date('Y-m-t', strtotime($year . '-' . $month)));
        $e = new Activity();
        $ord = new Order();
        for ($i = $start_datetime; $i <= $end_datetime; $i = ($i + (60 * 60 * 24))) {
            $date = date('Y-m-d', $i);

            $events = $e->getActivityEventByDate($this->activityId, $date);

            if (!empty($events)) {
                foreach ($events as $event) {
                    if (!$ord->isEventBooked($event['activityevent_id'])) {
                        if (!$e->removeEventByDate($this->activityId, $date)) {
                            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
                        }
                    }
                }
            }
        }
        FatUtility::dieJsonSuccess("Event Removed");
    }

///////////////////////////////////////////// step 7 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function step7() {
        $act = new Activity();
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $addons = $act->getActivityAddons($this->activityId);
        $this->set('addons', $addons);
        $html = $this->_template->render(false, false, 'hostactivity/_partial/addons.php', true, true);

        $form = $this->getStep7();
        $this->set('frm', $form);
        $form = $this->_template->render(false, false, 'hostactivity/_partial/addons-form.php', true, true);
        die(FatUtility::convertToJson(array('status' => 1, 'form' => $form, 'html' => $html)));
    }

    private function getStep7() {
        $frm = new Form('frmAddons');
        $frm->addHiddenField('', 'activityaddon_id');
        $frm->addRequiredField(Info::t_lang('ADD-ON_TITLE'), 'activityaddon_text');
        $frm->addRequiredField(Info::t_lang('ADD-ON_PRICE'), 'activityaddon_price');
        $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activityaddon_comments');
        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step6("#sixth-tb")'));
        $fld2 = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD'), array('class' => 'button button--small button--fill button--red'));
        $fld->attachField($fld2);
        return $frm;
    }

    function editAddon() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $addon_id = isset($post['addon_id']) ? FatUtility::int($post['addon_id']) : 0;
        if ($addon_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($this->activityId, $addon_id);
        if (empty($addon)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $frm = $this->getStep7();
        $frm->fill($addon);
        $frm->getField('btn_submit')->value = Info::t_lang('UPDATE');

        $this->set('frm', $frm);
        $html = $this->_template->render(false, false, 'hostactivity/_partial/addons-form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    public function setup7() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $data['activityaddon_activity_id'] = $this->activityId;

        $addon_id = FatUtility::int($post['activityaddon_id']);
        $data['activityaddon_text'] = $post['activityaddon_text'];
        $data['activityaddon_price'] = $post['activityaddon_price'];
        $data['activityaddon_comments'] = $post['activityaddon_comments'];
        $act = new Activity();
        if ($addon_id > 0) {
            $addon = $act->getAddonsByActivityAndId($this->activityId, $addon_id);
            if (empty($addon)) {
                FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
            }
        }


        if ($act->addActivityAddons($data, $addon_id)) {
            FatUtility::dieJsonSuccess("Addon Added");
        } else {
            FatUtility::dieJsonError("Something went wrong, please try again");
        }
    }

    function addonImages() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $addon_id = isset($post['addon_id']) ? FatUtility::int($post['addon_id']) : 0;
        if ($addon_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($this->activityId, $addon_id);
        if (empty($addon)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $frm = $this->getAddonImageForm();
        $frm->fill(array('addon_id' => $addon_id));
        $images = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_ACTIVITY_ADDON, $addon_id, $this->activityId);
   
        $this->set('addon', $addon);
        $this->set('frm', $frm);
        $this->set('images', $images);
        $htm = $this->_template->render(false, false, 'hostactivity/_partial/addon-images.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setupAddonImage() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $addon_id = isset($post['addon_id']) ? FatUtility::int($post['addon_id']) : 0;
        if ($addon_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($this->activityId, $addon_id);
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
        if (!$attach->saveAttachment($_FILES['addon_image']['tmp_name'], AttachedFile::FILETYPE_ACTIVITY_ADDON, $addon_id, $this->activityId, $_FILES['addon_image']['name'], 0, false, 1)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        $e = new Activity($this->activityId);
        $e->loadFromDb();
        $flds = $e->getFlds();
        $usr = new User($this->userId);
        $usr->loadFromDb();
        $host_data = $usr->getFlds();
        $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
        $host_name = ucwords($host_name);
        $notify = new Notification();
        $notify_msg = Info::t_lang('NEW_ADDON_IMAGE_UPLOADED_BY_') . ': ' . $host_name;
        $notify_msg .=Info::t_lang('_REGARDING_ACTIVITY_:-') . $flds['activity_name'];
        $notify->notify(0, 0, '', $notify_msg);
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('IMAGE_UPLOADED!'), 'addon_id' => $addon_id));
    }

    function removeAddonImage() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $image_id = isset($post['image_id']) ? FatUtility::int($post['image_id']) : 0;
        if ($image_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $file_data = AttachedFile::getAttachmentById($image_id);
        if (empty($file_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $addon_id = $file_data[AttachedFile::DB_TBL_PREFIX . 'record_id'];
        $act = new Activity();
        $addon = $act->getAddonsByActivityAndId($this->activityId, $addon_id);
        if (empty($addon)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if (!AttachedFile::removeFile($image_id)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('IMAGE_DELETED!'), 'addon_id' => $addon_id));
    }

    private function getAddonImageForm() {
        $frm = new Form('frmAddonPhoto');
        $frm->addHiddenField('', 'addon_id');
        $fld = $frm->addFileUpload(Info::t_lang('IMAGE'), 'addon_image', array('title' => Info::t_lang("PLEASE_UPLOAD_AN_IMAGE"), 'id' => 'c-file-upload-input'));
        $fld->htmlAfterField = '<em>' . Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)") . '</em>';
        $fld->requirements()->setRequired();
        //$fld->htmlAfterField='<input type="text"><button type="button">Select File</button>';
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPLOAD'), array('class' => 'button button--small button--fill button--red'))->attachField($frm->addButton('', 'button', Info::t_lang('BACK'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step7()')));
        return $frm;
    }

    public function removeAddons() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $act = new Activity();
        $post = FatApp::getPostedData();
        $act->removeActivityAddons($this->activityId, $post['addon_id']);
        FatUtility::dieJsonSuccess("Addon Deleted");
    }

////////////////////////////////////////////////////////////////////////	

    /*
     *  Slug Functionality
     */
    public function editSlug() {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("ACTIVITY_IS_REQUIRED"), 'step' => 1));
        }

        $routeData = Route::searchActiveRoute(Route::ACTIVITY_ROUTE, $this->activityId, 0);
        $form = $this->getForm();
        $form->fill($routeData);
        $this->set('frm', $form);
        $this->set('formHeader', Info::t_lang('EDIT_SLUG'));
        $this->_template->render(false, false, "hostactivity/_partial/edit-slug.php");
    }

    private function getForm() {
        $frm = new Form('RequestForm');
        $frm->addHiddenField("", 'url_rewrite_record_type');
        $frm->addHiddenField("", 'url_rewrite_record_id');
        $frm->addHiddenField("", 'url_rewrite_subrecord_id');
        $fld = $frm->addRequiredField('Slug', 'url_rewrite_custom');
        $frm->addSubmitButton('', 'button', Info::t_lang('SEND_REQUEST'), array('class' => 'button button--small button--fill button--red'));
        return $frm;
    }
    public function updateSlug() {
        $frm = $this->getForm();
        $data = $frm->getFormDataFromArray(FatApp::getPostedData());
        $route = new Routes();
        if (!$route->createNewRoute($data)) {
            FatUtility::dieWithError($route->getError());
        }
        $this->set('reload', true);
        $this->set('msg', 'Route Setup Successfull');
        $this->_template->render(false, false, 'json-success.php');
    }
}
