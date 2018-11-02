<?php
class CmsController extends MyAppController {

    protected $shortcodeObj = null;

    public function __construct($action) {
        parent::__construct($action, false);

        $this->set('action', $action);

        $this->shortcodeObj = Shortcodes::getInstance();
    }

    public function view($id) {
        $cms = new Cms();
        $meta = new MetaTags();
        $cmsData = $cms->getCms($id);
        if (empty($cmsData)) {
            FatUtility::exitWithErrorCode(404);
        }

        $SCObj = Shortcodes::getInstance();

        $cmsData['cms_content'] = $SCObj->parse($cmsData['cms_content']);

        $metaTags = $meta->getMetaTagsValues($cmsData['cms_id'], 0, 'cms', 'view');
	
        if (empty($metaTags)) {
           $this->setDefaultMetaTags();
        } else {
            $this->set("__metaData", $metaTags);
        }
        $this->set('pageTitle', $cmsData['cms_name']);
        $this->set('cmsData', $cmsData);
        $this->_template->render();
    }

    function terms($slug = 'terms') {
        $cms_data = array();
        $cms = new Cms();
        if ($slug != '') {
            $cms_data = $cms->getCmsBySlug($slug);
        }

        if (empty($cms_data)) {
            $cms_data = $cms->getTermsDefalutPage();
            $slug = $cms_data[Cms::DB_TBL_PREFIX . 'slug'];
        }
        if (empty($cms_data)) {
            FatUtility::exitWithErrorCode(404);
        }

        $terms_pages = $cms->getTermsPages();
        $this->set('terms_pages', $terms_pages);
        $this->set('slug', $slug);
        $this->set('cms_data', $cms_data);
        $this->set('pageTitle', Info::t_lang('TERMS') . ' | ' . $cms_data['cms_name']);
        $this->_template->render();
    }

    function submitInquiry() {
        if (!FatUtility::isAjaxCall()) {
            FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST'));
        }

        $form = $this->shortcodeObj->getContactForm();
        $post = $form->getFormDataFromArray(FatApp::getPostedData());

        if (false == $post) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        } else {
		
            if(FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') && !Helper::verifyCaptcha($_POST['g-recaptcha-response']))
			{
				FatUtility::dieJsonError(Info::t_lang("INCORRECT_SECURITY_CODE"));
			}
		
			$admin_email = FatApp::getConfig('conf_admin_email_id', FatUtility::VAR_STRING);
			if (in_array($post['option'], array(2, 3))) {// traveler and host support
				$admin_email = FatApp::getConfig('ADMIN_SUPPORT_EMAIL_ID', FatUtility::VAR_STRING);
			} elseif ($post['option'] == 5) {//career
				$admin_email = FatApp::getConfig('ADMIN_CAREER_EMAIL_ID', FatUtility::VAR_STRING);
			} elseif ($post['option'] == 1) {//general inquiry
				$admin_email = FatApp::getConfig('ADMIN_GENERAL_INQUIRY_EMAIL_ID', FatUtility::VAR_STRING);
			} elseif ($post['option'] == 4) {//Partner inquiry
				$admin_email = FatApp::getConfig('ADMIN_PARTNER_EMAIL_ID', FatUtility::VAR_STRING);
			}
			$emailData = array(
				"{name}" => $post['name'],
				"{email}" => $post['email'],
				"{message}" => $post['message'],
				"{option}" => Info::contactUsOptionsByKey($post['option'])
			);
			Email::sendMail($admin_email, 8, $emailData);

			FatUtility::dieJsonSuccess(Info::t_lang("Our_team_will_be_in_touch_soon"));
        }
        FatUtility::dieWithError(Info::t_lang('INVALID_REQUEST'));
    }

    public function map($lat, $lang) {
        $this->_template->addJs('common-js/01-jquery-2.1.4.min.js');
        $this->_template->addJs('common-js/rn-common.js');

        $this->set('lat', $lat);
        $this->set('long', $lang);
        $this->_template->render(false, false, "cms/map.php");
    }

    function help() {
        $faqCat = new FaqCategory();
        $faqObj = new Faq();
        $faqCategories = $faqCat->getFaqCategories(1);
        $faqs = $faqObj->getFaqWithCategory();
        $this->set('faqCategories', $faqCategories);
        $this->set('faqs', $faqs);
        $this->set('pageTitle', Info::t_lang('HELP'));
        $this->_template->render();
    }

    function hostHelp() {
        $video_url = FatApp::getConfig('CONF_HOST_HELP_VIDEO_URL');
        $help_image = FatApp::getConfig('CONF_HOST_HELP_IMAGE');
        $faqCat = new FaqCategory();
        $faqObj = new Faq();
        $faqCategories = $faqCat->getFaqCategories(1, 1);
        $faqs = $faqObj->getFaqWithCategory();
        $this->set('faqCategories', $faqCategories);
        $this->set('video_url', $video_url);
        $this->set('help_image', $help_image);
        $this->set('faqs', $faqs);
        $this->set('pageTitle', Info::t_lang('HOST-HELP'));
        $this->_template->render();
    }

}
