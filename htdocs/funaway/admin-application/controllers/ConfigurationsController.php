<?php

#error_reporting(E_ERROR);
class ConfigurationsController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {

        $ajaxCallArray = array("form");
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            FatUtility::dieJsonError("Invalid Action");
        }

        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewConfigurations($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditConfigurations($this->admin_id);

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
        $brcmb = new Breadcrumb();
        $brcmb->add("configurations");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function form() {
        $post = FatApp::getPostedData();
        $form = FatUtility::int($post['form']);
        if (empty($form)) {
            FatUtility::dieWithError('Invalid Request');
            return;
        }
        $forms = $this->getForm($form);
        //$this->set('table',$form['table']);
        $this->set('frm', $forms['frm']);
        $this->set('table', $forms['table']);
        $this->set('form_type', $form);
        $htm = $this->_template->render(false, false, 'configurations/_partial/form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function action() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $form_type = FatUtility::int($post['form_type']);
        if ($form_type <= 0) {
            FatUtility::dieWithError('Invalid Request!');
            return;
        }
        $frm = $this->getForm($form_type);
        $frm = $frm['frm'];
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError($frm->getValidationErrors());
            return;
        }
        if (!empty($_FILES)) {

            $uploadFiles = array();
            foreach ($_FILES as $field_name => $file_data) {
                $file_temp_name = $_FILES[$field_name]['tmp_name'];
                $saveName = $_FILES[$field_name]['name'];
                /* if (getimagesize($file_temp_name) === false) {
                  FatUtility::dieJsonError('Unrecognised Image file.');
                  } */
                while (file_exists(CONF_UPLOADS_PATH . $saveName)) {
                    $saveName = rand(10, 999) . '-' . $saveName;
                }
                if (!move_uploaded_file($file_temp_name, CONF_UPLOADS_PATH . $saveName)) {
                    FatUtility::dieJsonError('Could not move file.');
                }
                $post['conf'][$field_name] = $saveName;
            }
        }
        $confObj = new Configurations();

        if (!empty($post['conf'])) {
            foreach ($post['conf'] as $conf_name => $conf_val) {
                if ($conf_name == 'conf_payment_valid_upto') {
                    $conf_val = FatDate::format($conf_val);
                }
                if ($conf_name == 'CONF_HOST_HELP_VIDEO_URL' && $conf_val != '' && Helper::isValidVideoUrl($conf_val) == false) {
                    FatUtility::dieJsonError('Enter Valid Video Url');
                }

                if (!$confObj->update($conf_name, $conf_val)) {
                    FatUtility::dieWithError('Something went wrong!');
                    return;
                }
            }
        }
        FatUtility::dieJsonSuccess('Settings Update Successfully');
    }

    private function getForm($form_type) {
        $settings[1] = array(
            'conf_emails_from' => array('caption' => 'Send emails from Email ID', 'required' => true, 'type' => 'email'),
            'conf_admin_email_id' => array('caption' => 'Site Owner Email', 'required' => true, 'type' => 'email'),
            'CONTACT_US_EMAIL_ID' => array('caption' => 'Contact Us Email', 'required' => true, 'type' => 'email'),
            'SKYPE_ID' => array('caption' => 'Skype ID', 'required' => true, 'type' => 'textbox'),
            'VIBER_LINE' => array('caption' => 'Helpline No', 'required' => true, 'type' => 'textbox'),
            'conf_website_name' => array('caption' => 'Site Name', 'required' => true, 'type' => 'textbox'),
            'conf_website_title' => array('caption' => 'Site Title', 'required' => true, 'type' => 'textbox'),
            'conf_website_logo' => array('caption' => 'Site Logo <span class="mandatory">*</span>', 'required' => true, 'type' => 'fileupload'),
            'conf_website_footer_logo' => array('caption' => 'Site Footer Logo <span class="mandatory">*</span>', 'required' => true, 'type' => 'fileupload'),
            'conf_website_admin_logo' => array('caption' => 'Site Admin Logo <span class="mandatory">*</span>', 'required' => true, 'type' => 'fileupload'),
            'conf_fav_icon' => array('caption' => 'Site Fav Icon <span class="mandatory">*</span>', 'required' => true, 'type' => 'fileupload'),
			'ADMIN_GENERAL_INQUIRY_EMAIL_ID'=>array('caption'=>'General Inquiry Email Id','required'=>true,'type'=>'textbox'),
			'ADMIN_SUPPORT_EMAIL_ID'=>array('caption'=>'Support Inquiry Email Id','required'=>true,'type'=>'textbox'),
			'ADMIN_PARTNER_EMAIL_ID'=>array('caption'=>'Partners Inquiry Email Id','required'=>true,'type'=>'textbox'),
			'ADMIN_CAREER_EMAIL_ID'=>array('caption'=>'Careers Inquiry Email Id','required'=>true,'type'=>'textbox'),
        );

        $settings[2] = array(
            'meta_title' => array('caption' => 'Title', 'required' => true, 'type' => 'textbox'),
            'meta_keyword' => array('caption' => 'Keyword', 'required' => true, 'type' => 'textarea'),
            'meta_description' => array('caption' => 'Description', 'required' => true, 'type' => 'textarea'),
            'og_title' => array('caption' => 'Open Graph Title', 'required' => true, 'type' => 'textarea'),
            'og_description' => array('caption' => 'Open Graph Description', 'required' => true, 'type' => 'textarea'),
            'og_type' => array('caption' => 'Open Graph Type', 'required' => true, 'type' => 'textbox'),
            'og_type' => array('caption' => 'Open Graph Type', 'required' => true, 'type' => 'textbox'),
            'CONF_META_OTHER_TAGS' => array('caption' => 'Other Meta tags', 'required' => true, 'type' => 'textarea', 'htmlAfterField' => 'Enter Html  for other meta tags'),
            'og_image' => array('caption' => 'Open graph image(<em>Used for social sharing</em>)', 'required' => true, 'type' => 'fileupload'),
            'CONF_WEBSITE_TRACKING_CODE' => array('caption' => 'Tracking Code', 'required' => false, 'type' => 'textarea'),
			'CONF_TAWK_TO_CODE' => array('caption' => 'Tawk.to Code', 'required' => false, 'type' => 'textarea'),
        );
        $settings[3] = array(
            'conf_facebook_url' => array('caption' => 'Facebook Url', 'required' => false, 'type' => 'textbox'),
            'conf_youtube_url' => array('caption' => 'Youtube Url', 'required' => false, 'type' => 'textbox'),
            'conf_google_url' => array('caption' => 'Google Url', 'required' => false, 'type' => 'textbox'),
            'conf_twitter_url' => array('caption' => 'Twitter Url', 'required' => false, 'type' => 'textbox'),
            'conf_instagram_url' => array('caption' => 'Instagram Url', 'required' => false, 'type' => 'textbox'),
            'conf_pinterest_url' => array('caption' => 'Pinterest Url', 'required' => false, 'type' => 'textbox'),
            'conf_snapchat_url' => array('caption' => 'Snapchat Url', 'required' => false, 'type' => 'textbox'),
            'CONF_ACTIVITY_SOCIAL_SHARE_CONTENT' => array('caption' => 'Activity social share content', 'required' => false, 'type' => 'textarea'),
            'CONF_FACEBOOK_APP_ID' => array('caption' => 'Facebook App Id', 'required' => false, 'type' => 'textbox'),
            'CONF_FACEBOOK_SECRET_KEY' => array('caption' => 'Facebook Secret Key', 'required' => false, 'type' => 'textbox'),
	
            'CONF_GOOGLE_APP_ID' => array('caption' => 'Google App Id', 'required' => false, 'type' => 'textbox'),
            'CONF_GOOGLE_SECRET_KEY' => array('caption' => 'Google Secret Key', 'required' => false, 'type' => 'textbox'),
			
            'CONF_LINKEDIN_CALLBACK_URL' => array('caption' => 'Linkedin Callback Url', 'required' => false, 'type' => 'textbox', 'htmlAfterField' => 'Don\'t Change. Enter This Url in Linkedin Redirect URLs'),
            'CONF_TWITTER_CONSUMER_KEY' => array('caption' => 'Twitter Consumer key', 'required' => false, 'type' => 'textbox'),
            'CONF_TWITTER_CONSUMER_SECRET' => array('caption' => 'Twitter Consumer Secret', 'required' => false, 'type' => 'textbox', 'htmlAfterField' => 'Twitter Auth Callback Url is : ' . FatUtility::generateFullUrl('guest-user', 'twitter-login', array(), '/')),
            'CONF_MAILCHIMP_NEWS_LETTER_URL' => array('caption' => 'Mailchimp news letter form url', 'required' => false, 'type' => 'textarea', 'htmlAfterField' => 'Get Mailchimp Form: <a target="_blank" href="https://mailchimp.com/">https://mailchimp.com/</a>'),
        );
        $settings[4] = array(
            'mapbox_access_token' => array('caption' => 'MapBox Access Token', 'required' => false, 'type' => 'textbox'),
            'CONF_SMS_API_KEY' => array('caption' => 'Sms API Key', 'required' => false, 'type' => 'textbox', 'htmlAfterField' => 'Messaging API : <a target="_blank" href="https://www.sinch.com/products/sms-api/">https://www.sinch.com/products/sms-api/</a>'),
            'CONF_SMS_SECRET_KEY' => array('caption' => 'Sms Secret Key', 'required' => false, 'type' => 'textbox'),
            'CONF_FACEBOOK_TRACKING_ID' => array('caption' => 'Facebook Tracking Id', 'required' => false, 'type' => 'textbox'),
			'CONF_GOOGLE_API_KEY' => array('caption' => 'Google Api Key', 'required' => true, 'type' => 'textbox','htmlAfterField' => 'Api Key Should Have Geolcation and Google places Permission'),
			
			'CONF_RECAPTCHA_SITEKEY' => array('caption' => 'ReCaptcha Site Key', 'required' => false, 'type' => 'textbox'),
			
			'CONF_RECAPTCHA_SECRETKEY' => array('caption' => 'ReCaptcha Secret Key', 'required' => false, 'type' => 'textbox'),
        );
        $currencies = Currency::getCurrentCurrencyForForm();

        $settings[5] = array(
            'conf_default_currency' => array('caption' => 'Default Currency', 'required' => false, 'type' => 'selectBox', 'options' => $currencies),
            'conf_copyright_text' => array('caption' => 'Copyright Text', 'required' => false, 'type' => 'textbox'),
            //	'USER_DEFAULT_COMMISSION'=>array('caption'=>'Host Default Commission(%)','required'=>false,'type'=>'textbox'),
            'ADMIN_DEFAULT_COMMISSION' => array('caption' => 'Admin Default Commission(%)', 'required' => false, 'type' => 'textbox', 'htmlAfterField' => 'Applicable only If amount not exist in commission chart or commission chart not created'),
            'CONF_ACTIVITY_ATTRIBUTE_VALID_FILE_EXTENSION' => array('caption' => 'Activity Attribute Valid File Extension', 'required' => false, 'type' => 'textbox', 'htmlAfterField' => 'Add file extension separated by comma(,). Leave Blank for all extension'),
			'conf_fat_cache_enabled' => array('caption' => 'Enable FatCache', 'required' => false, 'type' => 'radio', 'arr_list' => ApplicationConstants::getYesNoArray()),
        );
        $settings[6] = array(
            'conf_email_method' => array('caption' => 'Send Email By', 'required' => true, 'type' => 'radio', 'arr_list' => array('PHP', 'SMTP')),
            'conf_smtp_host' => array('caption' => 'SMTP Host', 'required' => true, 'type' => 'textbox'),
            'conf_smtp_port' => array('caption' => 'SMTP Port', 'required' => true, 'type' => 'textbox'),
            'conf_smtp_username' => array('caption' => 'SMTP Username', 'required' => true, 'type' => 'textbox'),
            'conf_smtp_password' => array('caption' => 'SMTP Password', 'required' => true, 'type' => 'passwordfield')
        );
		
		
        $return_forms = array();
        $frm = new Form('configuration_' . $form_type);
        $frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
        $table = new HtmlElement('table', array('border' => 0, 'class' => 'table_form_horizontal', 'width' => '100%', 'cellspacing' => 0, 'cellpadding' => 0));
        $confObj = new Configurations();
        $configurations = $confObj->getConfigurations();
		// var_dump(array_key_exists('conf_fat_cache_enabled', $settings[$form_type]));
		// print_r($configurations);

		// exit;

        $frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
        $frm->getFormHtml();
		
        foreach ($configurations as $configuration) {
            if (array_key_exists($configuration['conf_name'], $settings[$form_type])) {
                //var_dump('conf['.$configuration['conf_name'].']');
                switch ($settings[$form_type][$configuration['conf_name']]['type']) {
                    case 'email':
                        $f1 = $frm->addEmailField('', 'conf[' . $configuration['conf_name'] . ']', $configuration['conf_val'], array('class' => 'medium'));
                        break;
                    case 'selectBox':
                        $f1 = $frm->addSelectBox('', 'conf[' . $configuration['conf_name'] . ']', $settings[$form_type][$configuration['conf_name']]['options'], $configuration['conf_val'], array('class' => 'medium'));
                        break;
                    case 'fileupload':

                        $f1 = $frm->addFileUpload('', $configuration['conf_name'], array('class' => 'medium'), '');

                        break;
                    case 'date':
                        $f1 = $frm->addDateField('', 'conf[' . $configuration['conf_name'] . ']', FatDate::format($configuration['conf_val']), array('class' => 'medium'));
                        break;
                    case 'dateTime':
                        $f1 = $frm->addDateTime('', 'conf[' . $configuration['conf_name'] . ']', $configuration['conf_val'], array('class' => 'medium'));
                        break;

                    case 'integer':
                        $f1 = $frm->addIntegerField('', 'conf[' . $configuration['conf_name'] . ']', $configuration['conf_val'], array('class' => 'medium'));
                        break;
                    case 'textarea':
                        $f1 = $frm->addTextArea('', 'conf[' . $configuration['conf_name'] . ']', $configuration['conf_val'], array('class' => 'medium'));
                        break;
                    case 'radio':
                        $f1 = $frm->addradioButtons('', 'conf[' . $configuration['conf_name'] . ']', $settings[$form_type][$configuration['conf_name']]['arr_list'], $configuration['conf_val'], array('class' => 'medium'));
                        break;
                    case 'passwordfield':
                        $f1 = $frm->addPasswordField('', 'conf[' . $configuration['conf_name'] . ']', $configuration['conf_val'], array('class' => 'medium'));
                        break;

                    default:
                        $f1 = $frm->addTextBox('', 'conf[' . $configuration['conf_name'] . ']', $configuration['conf_val'], array('class' => 'medium'));
                }
                if ($settings[$form_type][$configuration['conf_name']]['required']) {
                    $f1->requirements()->isRequired();
                }
                if (!empty($settings[$form_type][$configuration['conf_name']]['htmlAfterField'])) {
                    $f1->htmlAfterField = $settings[$form_type][$configuration['conf_name']]['htmlAfterField'];
                }
                $tr = $table->appendElement('tr');
                $td = $tr->appendElement('td', '', $settings[$form_type][$configuration['conf_name']]['caption'], true);
                if ($settings[$form_type][$configuration['conf_name']]['type'] == 'fileupload') {
                    $td = $tr->appendElement('td', '', $frm->getFieldHTML($configuration['conf_name']), true);
                } else {
                    $td = $tr->appendElement('td', '', $frm->getFieldHTML('conf[' . $configuration['conf_name'] . ']'), true);
                }
            }
        }
        $tr = $table->appendElement('tr');

        if ($this->canEdit) {
            $frm->addHiddenField('', 'form_type', $form_type);
            $td = $tr->appendElement('td', '', $frm->getFieldHTML('form_type'), true);
            $frm->addSubmitButton('', 'update', 'Update');
            ;
            $td = $tr->appendElement('td', '', $frm->getFieldHTML('update'), true);
        }
        $return_forms['table'] = $table;
        $return_forms['frm'] = $frm;


        return $return_forms;
    }

    public function maintenanceSettings() {
        $conf['CONF_MAINTENANCE_TEXT'] = FatApp::getConfig('CONF_MAINTENANCE_TEXT', FatUtility::VAR_STRING, '');
        $conf['CONF_MAINTENANCE'] = FatApp::getConfig('CONF_MAINTENANCE', FatUtility::VAR_INT, 0);
        $frm = $this->getMaintenanceForm();
        $frm->fill($conf);
        $this->set('frm', $frm);
        $this->_template->render(true, true, '/maintenance-settings/maintenance-settings.php');
    }

    public function setupMaintenanceSettings() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getMaintenanceForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());

        if ($post == false) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            $this->maintenanceSettings();
            return;
        } else {
            $confObj = new Configurations();
            foreach ($post as $conf_name => $conf_val) {
                if (!$confObj->update($conf_name, $conf_val)) {
                    Message::addErrorMessage('Something went wrong!');
                    $this->maintenanceSettings();
                    return;
                }
            }
            Message::addMessage('Maintenance settings updated');
            FatApp::redirectUser(FatUtility::generateUrl('configurations', 'maintenanceSettings'));
        }
    }

    private function getMaintenanceForm() {
        // funaway.4demo.biz/admin/configurations/maintenance-settings
        $frm = new Form('maintenanceFrm');
        $frm->addSelectBox('Maintenance Mode', 'CONF_MAINTENANCE', array(0 => 'No', 1 => 'Yes'), '', array('class' => 'medium'), '');

        $contentField = $frm->addTextArea('Email Body', 'CONF_MAINTENANCE_TEXT', '', array('id' => 'conf_maintenance_text'));
        $contentField->htmlAfterField = '<div id="tpl_body_editor"></div>' . MyHelper::getInnovaEditorObj('conf_maintenance_text', 'tpl_body_editor');
        $frm->addSubmitButton('', 'btn_submit');
        return $frm;
    }

}
