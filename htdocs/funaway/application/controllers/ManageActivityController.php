<?php

class ManageActivityController extends UserController
{

    private $activityId;

    public function __construct($action)
    {
        parent::__construct($action);

        $this->activityId = $this->getCurrentActivity();
        $this->set("class", "is--dashboard");
        $this->set("action", $action);
        $this->set("controller", 'hostactivity');
        $activityState = 0;
        if ($this->activityId) {
            $e = new Activity($this->activityId);
            $e->loadFromDb();
            $flds = $e->getFlds();
            $activityState = $flds['activity_state'];
        }
        $this->set('activityId', $this->activityId);
        $this->set('activityState', $activityState);
    }

    function fatActionCatchAll()
    {
        FatUtility::exitWithErrorCode(404);
    }

    private function setCurrentActivity($activityId = 0)
    {
        $act = new Activity();
        if ($activityId != 0) {
            if (!$act->isAssociatedActvity($activityId, $this->userId)) {
                Message::addErrorMessage("In Valid Operation! Login and Try Again...");
                FatApp::redirectUser(FatUtility::generateUrl('user', 'logout'));
            }
        }
        $_SESSION['activity_id'] = $activityId;
    }

    private function getCurrentActivity()
    {
        if (isset($_SESSION['activity_id'])) {
            return $_SESSION['activity_id'];
        }
        return 0;
    }

    public function index()
    {
        $brcmb = new Breadcrumb();
        //	$brcmb->add(Info::t_lang('Account'));
        $brcmb->add(Info::t_lang('LISTING'));
        $brcmb->add(Info::t_lang('MANAGE_LISTING'));
        $this->set('breadcrumb', $brcmb->output());
        $this->set('confirm_status', Info::getActivityConfirmStatus());
        $this->set('status', Info::getStatus());

        $this->_template->addJs(array(
            'js/angular/angular.min.js',
            'js/angular/angular-route.js'
        ));

        $this->_template->addJs(array('js/cropper.min.js', 'js/croper.js', 'js/google-places-auto.js'));
        $this->_template->addCss(array('css/cropper.min.css', 'css/croper.css'));
        $this->_template->render();
    }

    public function basic()
    {
        $e = new Activity();
        $block = new Block();
        $commission_chart = CommissionChart::getCommissionChart();
        $start_date_end_ins = $block->getBlock(12);
        $this->set('start_date_end_ins', $start_date_end_ins);
        $frm = $this->getBasicinfoForm($this->activityId);

        /* $this->set('frm', $frm);
          $this->set('commission_chart', $commission_chart); */

        $displayNextStyle = '';

        if ($this->activityId < 1) {
            $displayNextStyle = 'display:None;';
        }

        $frm->getField('btn_submit')->attachField($frm->addButton('', 'button', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'ng-click' => 'nextPage(1)', 'style' => $displayNextStyle, 'id' => 'js-basicToNext')));

        /* if ($this->activityId != 0) { */
        if ($this->activityId > 0) {

            $e = new Activity($this->activityId);
            if (!$e->loadFromDb()) {

                FatUtility::dieWithError('Error! ' . $e->getError());
            }
            $flds = $e->getFlds();
            $flds['activity_display_price'] = ($flds['activity_display_price'] == 0.00) ? "" : $flds['activity_display_price'];
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
        /* $activity_price->htmlAfterField = '<div class = "field_add-on add-on--right">' . $frm->getField('activity_price_type')->getHtml() . '</div>'; */

        $activity_price->htmlAfterField = '<div><a href="#commission_rate" class = "link field-tool-tip js-commission-popup" >' . Info::t_lang('COMMISSION_PERCENTAGE?') . '</a></div>';
        /* $frm->removeField($frm->getField('activity_price_type')); */

        $this->set('frm', $frm);
        $this->set('commission_chart', $commission_chart);

        $this->_template->render(false, false, 'manage-activity/_partial/basic.php');
    }

    private function getBasicinfoForm($record_id = 0)
    {
        $frm = new Form('frmRegister');
        //	$frm->addHiddenField('', 'user_id', 0, array('id'=>'user_id'));
        $fld = $frm->addRequiredField(Info::t_lang('ACTIVITY_NAME'), 'activity_name');

        //Add Slug Functionality
        if ($record_id > 0) {
            $slugHtml = '<a class="link modaal-ajax" href="' . Route::getRoute('hostactivity', 'editSlug') . '">Edit Activity Slug</a>';
            $slugField = $frm->addHtml("", "", $slugHtml);
            $fld->attachField($slugField);
        }
        $frm->addSelectBox(Info::t_lang('REGION'), 'activity_region_id', Region::getRegions(), '', array('onChange' => 'getCountries(this.value)'))->requirements()->setRequired();


        $frm->addSelectBox(Info::t_lang('COUNTRY'), 'activity_country_id', array(), '', array('onChange' => 'getCities(this.value)', 'id' => 'countries'))->requirements()->setRequired();

        $fld = $frm->addSelectBox(Info::t_lang('CITY'), 'activity_city_id', array(), '', array('id' => 'cities'));
		$fld->requirements()->setRequired();
        $requestLink = '<a href="' . Route::getRoute('UserRequest', 'create') . '" class="modaal-ajax link" >' . Info::t_lang('REQUEST_FOR_CITY') . '</a>';
        $fld->htmlAfterField = $requestLink;

        $frm->addSelectBox(Info::t_lang('ACTIVITY_TYPE'), 'category_id', Services::getCategories(), '', array('onchange' => 'getSubService(this)'));
        $frm->addSelectBox(Info::t_lang('CATEGORY'), 'activity_category_id', array(), '', array('id' => 'subcat-list'));
        $activity_start_date = $frm->addDateField('', 'activity_start_date', "", array('title' => Info::t_lang('START_DATE'), 'id' => "activity_start_date", 'readonly' => 'readonly'));
        $activity_start_date->requirements()->setRequired(); //date-instruction
        $activity_start_date->captionWrapper = array(Info::t_lang('START_DATE') . ' <a href="#date-instruction" class="date-popup"><svg class="icon icon--info"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use></svg>', '</a>');

        $activity_end_date = $frm->addDateField('', 'activity_end_date', "", array('title' => Info::t_lang('END_DATE'), 'readonly' => 'readonly'));
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
        $fld = $frm->addRequiredField(Info::t_lang('ACTIVITY_SALE_PRICE(Inc. Taxes)'), 'activity_price');
        $fld->requirements()->setRange(1, 999999999);
        $fld->requirements()->setCustomErrorMessage(Info::t_lang('ACTIVE_PRICE_ERROR'));
        $fld->htmlBeforeField = '<div class = "field_add-on add-on--left">' . @$currency['currency_symbol'] . '</div>';


        $fld = $frm->addTextBox(Info::t_lang('ACTIVITY_ORIGINAL_PRICE'), 'activity_display_price');
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

    public function setup1()
    {
        $frm = $this->getBasicinfoForm();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post, array('activity_category_id', 'activity_city_id'));

        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        if ($post['activity_booking'] == 100) {
            $post['activity_booking'] = $post['booking_days'] * 24;
        }

        if ($post['activity_duration'] == 100) {
            $post['activity_duration'] = $post['duration_days'] * 24;
        }

        if (empty($post['activity_display_price'])) {
            $post['activity_display_price'] = 0.00;
        }

        $post['activity_start_date'] = date('Y-m-d', strtotime($post['activity_start_date']));
        $post['activity_end_date'] = date('Y-m-d', strtotime($post['activity_end_date']));

        $post['activity_user_id'] = $this->userId;
        $newActivity = false;
        if ($this->activityId == 0) {
            $post['activity_confirm'] = 0;
            $post['activity_state'] = 1;
			$post['activity_state'] = 2;
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
        /* $attachFile = new AttachedFile(); */

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

        if ($this->activityId == 0) {
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
			Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 16, $vars);
			$notify_msg = Info::t_lang('NEW_ACTIVITY_ADDED_BY_') . ': ' . $host_name;
			$notify->notify(0, 0, '', $notify_msg);
        }

        FatUtility::dieJsonSuccess("Step1 Completed");
    }

    public function photos()
    {
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

        $form = $this->getphotos();
        $this->set('frm', $form);
        $this->_template->render(false, false, 'manage-activity/_partial/img-form.php');
    }

    private function getphotos()
    {
        $frm = new Form('frmPhoto');


        $html = '<em>' . Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)") . ' ' . sprintf(Info::t_lang('Max_Image_Size'), Helper::maxFileUpload(true)) . '</em></br>';

        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'ng-click' => 'prevPage(4)'));

        $fld->attachField($frm->addHtml('', 'btn_submit', "<a class='button button--small button--fill button--red modaal-ajax' href='" . FatUtility::generateUrl('croper', 'load') . "'>Upload</a>"));
        $fld->attachField($frm->addButton('', 'button2', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'ng-click' => 'nextPage(4)')));
        return $frm;
    }

    public function removeImage()
    {
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

    public function defaultImage()
    {
        $post = FatApp::getPostedData();
        $data['activity_image_id'] = $post['image_id'];
        $act = new Activity($this->activityId);
        $act->assignValues($data);
        if (!$act->save()) {
            FatUtility::dieJsonError($user->getError());
        }
        FatUtility::dieJsonSuccess("Images Set as Default Image");
    }

    /*
     * Videos Action
     */

    public function videos()
    {
        $act = new Activity();
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $videos = $act->getActivityVideos($this->activityId);
        $this->set('videos', $videos);
        $form = $this->getVideosForm();
        $this->set('frm', $form);
        $this->_template->render(false, false, 'manage-activity/_partial/video-form.php');
    }

    private function getVideosForm()
    {
        $frm = new Form('frmVideo');
        $fld = $frm->addRequiredField(Info::t_lang('URL'), 'activity_video');
        $valid_domain = Info::validVideoDomains();
        $valid_domain = implode(', ', $valid_domain);
        $fld->htmlAfterField = '<i> ' . $valid_domain . ' </i>';

        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'ng-click' => 'prevPage(5)'));
        $fld->attachField($frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD_VIDEO'), array('class' => 'button button--small button--fill button--red')));
        $fld->attachField($frm->addButton('', 'button2', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'ng-click' => 'nextPage(5)')));
        return $frm;
    }

    public function saveVideo()
    {
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
            $notify_msg .= Info::t_lang('_REGARDING_ACTIVITY_:-') . $flds['activity_name'];
            $notify->notify(0, 0, '', $notify_msg);
            FatUtility::dieJsonSuccess("Video Added");
        } else {
            FatUtility::dieJsonError("Something went wrong, please try again");
        }
    }

    //Videos Block Ends

    /*
     * Activity Brief
     */

    public function activityBrief()
    {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $e = new Activity();
        $frm = $this->getActivityBriefForm();
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

        $this->_template->render(false, false, 'manage-activity/_partial/step4.php');
    }

    private function getActivityBriefForm()
    {
        $frm = new Form('frmExtra');
        $text_area_id = 'high_area';
        $editor_id = 'highlight_area' . '-removed';
        //$html = "<table>		<tr>			<th>What's included ?</th>			<td>Details</td>		</tr>		</table>";
        $html = "";
        $field = $frm->addTextArea(Info::t_lang('HIGHLIGHTS'), 'activity_highlights', $html, array('id' => $text_area_id, 'placeholder' => ''));
        // $field->htmlBeforeField = '<div id="' . $editor_id . '"></div>' . Helper::getInnovaEditorObj($text_area_id, $editor_id) . '<small>' . Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS') . '</small>';
        //// Describe your activity in 3-4 paragraphs
        $field->htmlAfterField ='<em>('.Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS').')</em>';
        //$field->captionWrapper =array('<em>('.Info::t_lang('DESCRIBE_YOUR_ACTIVITY_IN_3-4_PARAGRAPHS').')','</em>');
        //$field->developerTags['noCaptionTag']=true;
        $field->requirements()->setRequired(true);

        $text_area_id = 'text_area';
        $editor_id = 'editor_area' . '-removed';
        $field = $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activity_desc', '', array('id' => $text_area_id));
        // $field->htmlBeforeField = '<div id="' . $editor_id . '"></div>' . Helper::getInnovaEditorObj($text_area_id, $editor_id) . '<small>' . Info::t_lang('DETAILED_DESCRIPTION_OF_THE_ACTIVITY') . '</small>';
        $field->requirements()->setRequired(true);
		
		$field->htmlAfterField ='<em>('.Info::t_lang('DETAILED_DESCRIPTION_OF_THE_ACTIVITY').')</em>';
		
		
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


        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'ng-click' => 'prevPage(2)'));
        $fld->attachField($frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPDATE'), array('class' => 'button button--small button--fill button--red')));
        $fld->attachField($frm->addButton('', 'button', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'ng-click' => 'nextPage(2)')));
        return $frm;
    }

    public function saveActivityBrief()
    {

        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $frm = $this->getActivityBriefForm();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post, array('activity_languages'));

        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['activity_contacts'] = @$post['activity_contacts'][0];
        $act = new Activity($this->activityId);
        $act->loadFromDb();
        $activityData = $act->getFlds();

		// $post['activity_highlights'] = strip_tags($post['activity_highlights']);
		// $post['activity_desc'] = strip_tags($post['activity_desc']);
		
		// $post['activity_inclusions'] = strip_tags($post['activity_inclusions']);
		// $post['activity_requirements'] = strip_tags($post['activity_requirements']);
        
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

        FatUtility::dieJsonSuccess("Step 2 Completed.");
    }

    //Activity Breif Ends

    /*
     * 	Maps Section
     */

    public function map()
    {
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
        $frm = $this->getMapForm();
        $frm->fill($flds);
        $this->set('frm', $frm);
        $this->_template->render(false, false, 'manage-activity/_partial/map.php');
    }

    private function getMapForm()
    {
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

        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'ng-click' => 'prevPage(6)'));
        $fld2 = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPDATE'), array('class' => 'button button--small button--fill button--red'));
        $fld2->developerTags['noCaptionTag'] = true;
        $fld->attachField($fld2);
        $fld->attachField($frm->addButton('', 'button', Info::t_lang('NEXT_STEP'), array('class' => 'button button--small button--fill button--dark fl--right', 'ng-click' => 'nextPage(6)')));
        return $frm;
    }

    public function saveMapInfo()
    {
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $post = FatApp::getPostedData();
        $frm = $this->getMapForm();
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

    /*
     * Availability
     */

    public function availablity()
    {
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
            // $yr = $startyear;
            // $month = $startmonth;
			
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
                if ($cals[$k]['events'] = $e->getActivityEventByDate($this->activityId, date('Y-m-d', strtotime($date)))) {
                    $class .= ' have-event';
                    foreach ($cals[$k]['events'] as $eves) {
                        if ($eves['activityevent_confirmation_requrired'] == 1) {
                            $class .= ' required';
                            continue;
                        }
                    }
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

        if (FatUtility::isAjaxCall()) {
            $html = $this->_template->render(false, false, 'manage-activity/_partial/step6.php', true);
            die(FatUtility::convertToJson(array('status' => 1, 'html' => $html)));
        } else {            
            $this->_template->render(false, false, 'manage-activity/_partial/step6.php');
        }
    }

    /*
     * Add On Form
     */

    public function addon()
    {
        $act = new Activity();
        if (intval($this->activityId) == 0) {
            FatUtility::dieJsonError(array('msg' => Info::t_lang("STEP1_STILL_PENDING"), 'step' => 1));
        }
        $addons = $act->getActivityAddons($this->activityId);
        $this->set('addons', $addons);
        $form = $this->getAddonForm();
        $this->set('frm', $form);
        $this->_template->render(false, false, 'manage-activity/_partial/addons.php');
    }

    private function getAddonForm()
    {
        $frm = new Form('frmAddons');
        $frm->addHiddenField('', 'activityaddon_id');
        $frm->addRequiredField(Info::t_lang('ADD-ON_TITLE'), 'activityaddon_text');
        $frm->addRequiredField(Info::t_lang('ADD-ON_PRICE'), 'activityaddon_price');
        $frm->addTextArea(Info::t_lang('DESCRIPTION'), 'activityaddon_comments');
        $fld = $frm->addButton('', 'button', Info::t_lang('PREV_STEP'), array('class' => 'button button--small button--fill button--dark', 'ng-click' => 'prevPage(7)'));
        $fld2 = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('ADD'), array('class' => 'button button--small button--fill button--red'));
        $fld->attachField($fld2);
        return $frm;
    }

    function editAddon()
    {
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
        $frm = $this->getAddonForm();
        $frm->fill($addon);
        $frm->getField('btn_submit')->value = Info::t_lang('UPDATE');

        $this->set('frm', $frm);

        $html = $this->_template->render(false, false, 'manage-activity/_partial/addons-form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    public function saveAddon()
    {
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

    function addonImages()
    {
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
        $htm = $this->_template->render(false, false, 'manage-activity/_partial/addon-images.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setupAddonImage()
    {
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
        if (!$attach->saveAttachment($_FILES['addon_image']['tmp_name'], AttachedFile::FILETYPE_ACTIVITY_ADDON, $addon_id, $this->activityId, $_FILES['addon_image']['name'])) {
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
        $notify_msg .= Info::t_lang('_REGARDING_ACTIVITY_:-') . $flds['activity_name'];
        $notify->notify(0, 0, '', $notify_msg);
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('IMAGE_UPLOADED!'), 'addon_id' => $addon_id));
    }

    function removeAddonImage()
    {
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

    private function getAddonImageForm()
    {
        $frm = new Form('frmAddonPhoto');
        $frm->addHiddenField('', 'addon_id');
        $fld = $frm->addFileUpload(Info::t_lang('IMAGE'), 'addon_image', array('title' => Info::t_lang("PLEASE_UPLOAD_AN_IMAGE"), 'id' => 'c-file-upload-input'));
        $fld->htmlAfterField = '<em>' . Info::t_lang("IMAGE_SIZE_MUST_BE 1600 X 900 RATIO(4:3)") . '</em>';
        $fld->requirements()->setRequired();
        //$fld->htmlAfterField='<input type="text"><button type="button">Select File</button>';
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('UPLOAD'), array('class' => 'button button--small button--fill button--red'))->attachField($frm->addButton('', 'button', Info::t_lang('BACK'), array('class' => 'button button--small button--fill button--dark', 'onclick' => 'step7()')));
        return $frm;
    }

}
