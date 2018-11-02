<?php

class ImageController extends FatController
{

    public function fatImages($imageName, $type = '', $w = 0, $h = 0, $folder = '')
    {
        Helper::staticImage($imageName, $w, $h, $folder, $type);
    }

    /* public function service($service_id,$w=0,$h=0){
      $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_SERVICE_PHOTO,$service_id);
      Helper::catImage($img['afile_physical_path'],"service",$w,$h);
      } */

    public function service($service_id, $w = 0, $h = 0)
    {
        Helper::fatImage(0, $service_id, 0, AttachedFile::FILETYPE_SERVICE_PHOTO, $w, $h, '', 'activity-no-image.jpg');
    }

    public function founder($founder_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_FOUNDER_PHOTO, $founder_id);
        Helper::displayImage($img['afile_physical_path'], "service", $w, $h);
    }

    public function investor($investor_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_INVESTOR_PHOTO, $investor_id);
        Helper::displayImage($img['afile_physical_path'], "service", $w, $h);
    }

    public function cmsImage($cms_id, $w = 0, $h = 0)
    {

        $img = '';
        if ($imgData = AttachedFile::getAttachment(AttachedFile::FILETYPE_CMS_PHOTO, $cms_id)) {
            $img = $imgData['afile_physical_path'];
        }

        if ($w != 0)
            Helper::displayImage($img, "banner", $w, $h);
        else
            Helper::showImage($img, '', 'banner');

        /* $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_CMS_PHOTO,$cms_id);

          if($w != 0)
          Helper::displayImage($img['afile_physical_path'],"banner",$w,$h);
          else
          Helper::showImage($img['afile_physical_path']); */
    }

    public function office($office_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_OFFICE_PHOTO, $office_id);
        Helper::displayImage($img['afile_physical_path'], "service", $w, $h);
    }

    public function flag($lang_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_LANGUAGE_PHOTO, $lang_id);

        Helper::showImage($img['afile_physical_path'], "", "flag");
    }

    public function paymentMethod($pm_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_PMETHOD_IMAGE, $pm_id);

        Helper::showImage($img['afile_physical_path']);
    }

    public function dactivity($act_id, $w = 0, $h = 0)
    {
        $act = new Activity();
        $acts = $act->getActivity($act_id, -2);
        $this->activity($acts['activity_image_id'], $w, $h);
    }

    public function activity($afile_id, $w = 0, $h = 0)
    {

        $img = AttachedFile::getAttachmentById($afile_id);
        $image_name = @$img['afile_physical_path'];

        // var_dump($img);exit;
        if (isset($img[AttachedFile::DB_TBL_PREFIX . 'approved']) && $img[AttachedFile::DB_TBL_PREFIX . 'approved'] != 1) {
            if (User::isUserLogged()) {
                $user_type = User::getLoggedUserAttribute('user_type');
                if ($user_type == 1) {
                    $activity_id = @$img[AttachedFile::DB_TBL_PREFIX . 'record_id'];

                    $act = new Activity($activity_id);
                    $activity_data = $act->getAttributesById($activity_id);
                    if (empty($activity_data)) {
                        $image_name = '';
                    } elseif ($activity_data[Activity::DB_TBL_PREFIX . 'user_id'] != User::getLoggedUserId()) {
                        $image_name = '';
                    }
                } else {
                    $image_name = '';
                }
            } else {
                $image_name = '';
            }
        }

        Helper::fatImage($afile_id, 0, 0, AttachedFile::FILETYPE_ACTIVITY_PHOTO, $w, $h, '', 'activity-no-image.jpg');

        /* if($w != 0)
          Helper::cropImage($image_name,"activity",$w,$h);
          else
          Helper::showImage($image_name); */
    }

    public function addon($afile_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachmentById($afile_id);
        $image_name = @$img['afile_physical_path'];
        if (isset($img[AttachedFile::DB_TBL_PREFIX . 'approved']) && $img[AttachedFile::DB_TBL_PREFIX . 'approved'] != 1) {
            if (User::isUserLogged()) {
                $user_type = User::getLoggedUserAttribute('user_type');
                if ($user_type == 1) {
                    $activity_id = @$img[AttachedFile::DB_TBL_PREFIX . 'record_subid'];

                    $act = new Activity($activity_id);
                    $activity_data = $act->getAttributesById($activity_id);
                    if (empty($activity_data)) {
                        $image_name = '';
                    } elseif ($activity_data[Activity::DB_TBL_PREFIX . 'user_id'] != User::getLoggedUserId()) {
                        $image_name = '';
                    }
                } else {
                    $image_name = '';
                }
            } else {
                $image_name = '';
            }
        }

        if ($w != 0)
            Helper::cropImage($image_name, "activity", $w, $h);
        else
            Helper::showImage($image_name);
    }

    public function adminAddon($afile_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachmentById($afile_id, -1);
        $image_name = @$img['afile_physical_path'];


        if ($w != 0)
            Helper::cropImage($image_name, "activity", $w, $h);
        else
            Helper::showImage($image_name);
    }

    public function adminActivity($activty_id, $w = 0, $h = 0)
    {


        $img = AttachedFile::getAttachmentById($activty_id);

        if ($w != 0)
            Helper::cropImage($img['afile_physical_path'], "activity", $w, $h);
        else
            Helper::showImage($img['afile_physical_path']);
    }

    public function user($user_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PHOTO, $user_id);
        $default_img = '';
        if ($user_id > 0) {
            $default_img = 'user';
        }

        Helper::displayImage($img['afile_physical_path'], $default_img, $w, $h);
    }

    public function banner($banner_id, $w = 0, $h = 0)
    {
        Helper::fatImage(0, $banner_id, 0, AttachedFile::FILETYPE_BANNER_PHOTO, $w, $h, '', 'activity-no-image.jpg');
    }

    public function homepageBanner($banner_type, $w = 0, $h = 0)
    {
        Helper::fatImage(0, 0, 0, $banner_type, $w, $h, '', 'activity-no-image.jpg');
    }

    function demoPhoto($image = "")
    {
        self::showImage($image);
    }

    function uploadDemoPhoto()
    {

        if (!is_array($_FILES) || empty($_FILES) || $_FILES['photo']['tmp_name'] == '') {
            FatUtility::dieJsonError(Info::t_lang('Select_a_valid_image_or_select_again'));
        }

        if (is_uploaded_file($_FILES['photo']['tmp_name'])) {
            if (AttachedFile::uploadImage($_FILES['photo']['tmp_name'], $_FILES['photo']['name'], $response)) {
                FatUtility::dieJsonSuccess(FatUtility::generateUrl("image", "demoPhoto", array($response)));
            }
        }
        FatUtility::dieJsonError(Info::t_lang('Something went Wrong!'));
    }

    public static function showImage($img = "")
    {
        ob_end_clean();
        $pth = CONF_INSTALLATION_PATH . 'user-uploads/' . $img;
        header('Cache-Control: public, must-revalidate');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($pth)) . ' GMT', true, 200);
        header("Pragma: public");
        header("Expires: " . date('r', strtotime("+30 days")));
        ob_end_clean();
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }
        //ob_end_clean();
        readfile($pth);
    }

    public function crop($img_name)
    {
        Helper::showImage($img_name);
    }

    public function captcha()
    {
        $img = Helper::getCaptchaObject();
        $s = $img->show();
    }

    function ogImage()
    {
        $logo_name = FatApp::getConfig('og_image');
        Helper::showImage($logo_name);
    }

    function attribute($attr_id, $activity_id)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id);
        Helper::showFile($img[AttachedFile::DB_TBL_PREFIX . 'physical_path'], $img[AttachedFile::DB_TBL_PREFIX . 'name']);
    }

    function hostStarterKit()
    {
        $img = FatApp::getConfig('CONF_HOST_STARTER_KIT');
        Helper::showFile($img, 'Host starter kit');
    }

    function hostHelp($w = 1000, $h = 250)
    {
        $img = FatApp::getConfig('CONF_HOST_HELP_IMAGE');
        if ($img == '') {
            return;
        }
        Helper::cropImage($img, 'host-help', $w, $h);
    }

    public function post($img = '', $w = 0, $h = 0)
    {
        if (!empty($img)) {
            return Helper::displayImage($img, 'blogPost', $w, $h, AttachedFile::POST_IMG_FOLDER);
        }
    }

    public function postDefaultImage($record_id = '', $w = 0, $h = 0)
    {
        $srch = BlogPosts::imgSearch();

        $srch->addCondition(BlogPosts::DB_IMG_TBL_PREFIX . 'post_id', '=', $record_id);
        $srch->addCondition(BlogPosts::DB_IMG_TBL_PREFIX . 'default', '=', 1);
        $srch->addMultipleFields(array(BlogPosts::DB_IMG_TBL_PREFIX . 'file_name'));
        $rs = $srch->getResultSet();
        $row = ( $rs ) ? FatApp::getDb()->fetch($rs) : array();

        if (!empty($row)) {
            return Helper::displayImage($row[BlogPosts::DB_IMG_TBL_PREFIX . 'file_name'], 'blogPost', $w, $h, AttachedFile::POST_IMG_FOLDER);
        }
    }

    public function postImage($record_id = '', $w = 0, $h = 0, $imageRecordIdFlg = 0)
    {

        $srch = BlogPosts::imgSearch();
        if ($imageRecordIdFlg)
            $srch->addCondition(BlogPosts::DB_IMG_TBL_PREFIX . 'id', '=', $record_id);
        else
            $srch->addCondition(BlogPosts::DB_IMG_TBL_PREFIX . 'post_id', '=', $record_id);
        /*  $srch->addCondition( BlogPosts::DB_IMG_TBL_PREFIX . 'default', '=', BLOG_POST_DEFAULT ); */
        $srch->addMultipleFields(array(BlogPosts::DB_IMG_TBL_PREFIX . 'file_name'));
        $rs = $srch->getResultSet();
        $row = ( $rs ) ? FatApp::getDb()->fetch($rs) : array();

        if (!empty($row)) {
            return Helper::displayImage($row[BlogPosts::DB_IMG_TBL_PREFIX . 'file_name'], 'blogPost', $w, $h, AttachedFile::POST_IMG_FOLDER);
        }
    }

    public function testimonial($testimonial_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_TESTIMONIAL, $testimonial_id);
        $default_img = 'user';
        Helper::displayImage($img['afile_physical_path'], $default_img, $w, $h);
    }

    function companyLogo($logo_type = 'conf_website_logo')
    {
        //echo Info::timestamp(); exit;
        $logo_name = FatApp::getConfig("$logo_type");
        Helper::displayImage($logo_name, '', 512, 512);
    }

    function siteFavicon($logo_type = 'conf_website_logo')
    {
        $logo_name = FatApp::getConfig("$logo_type");
        Helper::showImage($logo_name);
    }

    /*
     *  New Functions Added
     */

    public function city($afile_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachmentById($afile_id);
        if ($w != 0)
            Helper::displayImage($img['afile_physical_path'], "activity", $w, $h);
        else
            Helper::showImage($img['afile_physical_path']);
    }

    public function country($afile_id, $w = 0, $h = 0)
    {
        $img = AttachedFile::getAttachmentById($afile_id);
        if ($w != 0)
            Helper::displayImage($img['afile_physical_path'], "activity", $w, $h);
        else
            Helper::showImage($img['afile_physical_path']);
    }

    public function cityRandom($city_id, $w = 0, $h = 0)
    {

        $rows = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_CITY_IMAGE, $city_id);
        $afile_id = array_rand($rows);

        Helper::fatImage($afile_id, 0, 0, AttachedFile::FILETYPE_CITY_IMAGE, $w, $h, '', 'activity-no-image.jpg', true);
    }

}

?>