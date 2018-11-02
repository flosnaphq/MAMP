<?php
class Helper extends FatModel {

    const SOCIAL_SESSION_NAME = 'SocialMedia';
    const LAST_URL = 'last_url';

    function __construct() {
        parent::__construct();
    }

    function hostMenu() {
        $menu = array();
        $i = 0;

        $menu[++$i] = array("name" => Info::t_lang("MY_PROFILE"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => Info::t_lang("MESSAGES"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => "Manage Cms", "link" => "javascript:;");
        $menu[$i]['child'][] = array("name" => "Manage Cms", "link" => FatUtility::generateUrl('cms'));

        $menu[$i]['child'][] = array("name" => "Manage FAQ", "link" => FatUtility::generateUrl('faq'));
        $menu[$i]['child'][] = array("name" => "Manage Home Page Banner", "link" => FatUtility::generateUrl('Banners'));
        $menu[++$i] = array("name" => Info::t_lang("PAYOUT_SETTINGS"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => Info::t_lang("MY_BOOKINGS"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => Info::t_lang("REPORTS"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => Info::t_lang("MY_LISTINGS"), "link" => FatUtility::generateUrl("host"));

        return $menu;
    }

    function travelerMenu() {
        $menu = array();
        $i = 0;

        $menu[++$i] = array("name" => Info::t_lang("MY_PROFILE"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => Info::t_lang("MESSAGES"), "link" => FatUtility::generateUrl("host"));

        $menu[++$i] = array("name" => Info::t_lang("MY_BOOKINGS"), "link" => FatUtility::generateUrl("host"));
        $menu[++$i] = array("name" => Info::t_lang("WISHLIST"), "link" => FatUtility::generateUrl("host"));

        return $menu;
    }

    static public function deleteSingleAttachedFile($file_id) {
        $db = FatApp::getDb();
        $db->deleteRecords("tbl_attached_files", array(
            'smt' => 'afile_id = ? ',
            'vals' => array($file_id)
        ));
    }

    static public function deleteFileByRecord($file_id, $record_id) {
        $db = FatApp::getDb();
        $db->deleteRecords("tbl_attached_files", array(
            'smt' => 'afile_id = ? and afile_record_id = ?',
            'vals' => array($file_id, $record_id)
        ));
    }

    static public function deleteMultipleAttachedFile($fileType, $recordId, $recordSubid = 0) {
        $db = FatApp::getDb();
        $db->deleteRecords("tbl_attached_files", array(
            'smt' => 'afile_type = ? AND afile_record_id = ? AND afile_record_subid = ? ',
            'vals' => array($fileType, $recordId, $recordSubid)
        ));
    }

    public static function displayImage($img_name, $type, $w = 0, $h = 0, $folder = '') {
        $folder = !empty($folder) ? trim($folder, '/') . '/' : '';
        if ($w == 0 || $h == 0) {
            self::showImage($img_name, $folder);
            exit;
        }
        if ($img_name != "" && file_exists(CONF_UPLOADS_PATH . $folder . $img_name)) {
            $path = CONF_UPLOADS_PATH . $folder . $img_name;
        } else {
            switch ($type) {
                case "activity":
                    $path = CONF_UPLOADS_PATH . 'default-images/activity-no-image.jpg';
                    break;
                case "service":
                    $path = CONF_UPLOADS_PATH . 'default-images/service-no-image.jpg';
                    break;
                case "user":
                    $path = CONF_UPLOADS_PATH . 'default-images/user-no-image.jpg';
                    break;
                case "admin":
                    // $path = CONF_THEME_PATH . 'img/admin.png';
                    $path = CONF_UPLOADS_PATH . $folder . FatApp::getConfig("conf_website_logo");
                    break;
                case "banner":
                    $path = CONF_UPLOADS_PATH . 'default-images/banner-no-image.jpg';
                    break;
                default:
                    $path = CONF_UPLOADS_PATH . 'default-images/user-no-image.jpg';
            }
        }

        if (ob_get_contents())
            ob_end_clean();

        $headers = FatApp::getApacheRequestHeaders();
        header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        header("Pragma: public");
        header("Expires: " . date('r', strtotime("+30 days")));

        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);

        $img = new ImageResize($path);
        $w = max(1, FatUtility::int($w));
        $h = max(1, FatUtility::int($h));
        $img->setMaxDimensions($w, $h);
		
		ob_start();
        $img->displayImage();
		$content = ob_get_clean();
		
		/* if(true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {
			$cacheKey = $_SERVER['REQUEST_URI'];
            FatCache::set($cacheKey, $content, '.jpg');
        } */
		echo $content;
    }

    public static function cropImage($img_name, $type, $w = 0, $h = 0, $folder = '') {

        $folder = !empty($folder) ? trim($folder, '/') . '/' : '';
        if ($img_name != "" and file_exists(CONF_UPLOADS_PATH . $folder . $img_name)) {
            $path = CONF_UPLOADS_PATH . $folder . $img_name;
        } else {
            switch ($type) {
                case "activity": {
                        $path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
                        break;
                    }
                case "service": {
                        $path = CONF_THEME_PATH . 'img/service-no-image.jpg';
                        break;
                    }
                case "user": {
                        $path = CONF_THEME_PATH . 'img/user-no-image.jpg';
                        break;
                    }
                case "admin": {
                        // $path = CONF_THEME_PATH . 'img/admin.png';
                        $path = CONF_UPLOADS_PATH . $folder . FatApp::getConfig("conf_website_logo");
                        break;
                    }
                case "blogPost": {
                        $path = CONF_THEME_PATH . 'img/service-no-image.png';
                        break;
                    }
                case "host-help":
                    $path = CONF_THEME_PATH . 'img/activity-no-image.png';
                    break;
                default: {
                        $path = CONF_THEME_PATH . 'img/user-no-image.jpg';
                    }
            }
        }

        if (ob_get_contents())
            ob_end_clean();

        header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        header("Pragma: public");
        header("Expires: " . date('r', strtotime("+30 days")));

        $headers = FatApp::getApacheRequestHeaders();
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            exit;
        }

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);

        $img = new ImageResize($path);

        $w = max(1, FatUtility::int($w));
        $h = max(1, FatUtility::int($h));

        $img->setMaxDimensions($w, $h);
        $img->displayImage();

        // exit;
        /* $width = max(1, FatUtility::int($w));
          $height = max(1, FatUtility::int($h));
          list($width_orig, $height_orig) = getimagesize($path);
          $ratio_orig = 1.3;
          if ($width/$height > $ratio_orig) {
          $width = $height*$ratio_orig;
          } else {
          $height = $width/$ratio_orig;
          }
          $image_p = imagecreatetruecolor($width, $height);
          $file_info = pathinfo($path);
          $extension = strtolower($file_info['extension']);
          switch($extension){
          case 'jpg':
          case 'jpeg':
          $image = imagecreatefromjpeg($path);
          header('Content-Type: image/jpg');
          break;
          case 'png':
          $image = imagecreatefrompng($path);
          header('Content-Type: image/png');
          break;
          case 'gif':
          $image = imagecreatefromgif($path);
          header('Content-Type: image/gif');
          break;
          default:
          $image = imagecreatefromjpeg($path);
          header('Content-Type: image/jpg');
          break;
          }

          imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
          imagejpeg($image_p, null, 100); */
    }

    public static function catImage($img_name, $type, $w = 0, $h = 0) {
        if ($img_name != "" and file_exists(CONF_UPLOADS_PATH . $img_name)) {
            $path = CONF_UPLOADS_PATH . $img_name;
        } else {
            switch ($type) {
                case "activity": {
                        $path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
                        break;
                    }
                case "service": {
                        $path = CONF_THEME_PATH . 'img/service-no-image.jpg';
                        break;
                    }
                case "user": {
                        $path = CONF_THEME_PATH . 'img/user-no-image.jpg';
                        break;
                    }
                case "admin": {
                        $path = CONF_UPLOADS_PATH . $folder . FatApp::getConfig("conf_website_logo");
                        break;
                    }
                default: {
                        $path = CONF_THEME_PATH . 'img/user-no-image.jpg';
                    }
            }
        }

        if (ob_get_contents())
            ob_end_clean();

        header('Cache-Control: public');
        header("Pragma: public");
        header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));

        $headers = FatApp::getApacheRequestHeaders();
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);

        $width = max(1, FatUtility::int($w));
        $height = max(1, FatUtility::int($h));
        list($width_orig, $height_orig) = getimagesize($path);
        $ratio_orig = 1;
        if ($width / $height > $ratio_orig) {
            $width = $height * $ratio_orig;
        } else {
            $height = $width / $ratio_orig;
        }
        $image_p = imagecreatetruecolor($width, $height);
        $file_info = pathinfo($path);
        $extension = strtolower($file_info['extension']);
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($path);
                header('Content-Type: image/jpg');
                break;
            case 'png':
                $image = imagecreatefrompng($path);
                header('Content-Type: image/png');
                break;
            case 'gif':
                $image = imagecreatefromgif($path);
                header('Content-Type: image/gif');
                break;
        }

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        imagejpeg($image_p, null, 100);
    }

    public static function showImage($img_name, $folder = '', $type = '') {
        if (ob_get_contents())
            ob_end_clean();

        if ($img_name != "" and file_exists(CONF_UPLOADS_PATH . $folder . $img_name)) {
            $path = CONF_UPLOADS_PATH . $folder . $img_name;
        } else {
            switch ($type) {

                case "activity":
                    $path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
                    break;
                case "flag":
                case "service":
                    $path = CONF_THEME_PATH . 'img/service-no-image.jpg';
                    break;
                case "user":
                    $path = CONF_THEME_PATH . 'img/user-no-image.jpg';
                    break;
                case "admin":
                    $path = CONF_UPLOADS_PATH . $folder . FatApp::getConfig("conf_website_logo");
                    break;
                case "banner":
                    $path = CONF_THEME_PATH . 'img/banner-no-image.jpg';
                    break;
                default:
                    $path = CONF_THEME_PATH . 'img/user-no-image.jpg';
            }
        }
        $headers = FatApp::getApacheRequestHeaders();
        header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        header("Pragma: public");
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            exit;
        }
        header('Content-type: image/jpeg');

        header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);

		readfile($path);
    }

    public static function showFile($img_name, $real_name = '') {
        if ($real_name == '') {
            $real_name = $img_name;
        }
        $path = CONF_THEME_PATH . 'img/activity-no-image.jpg';

        if ($img_name != "" and file_exists(CONF_UPLOADS_PATH . $img_name)) {
            $path = CONF_UPLOADS_PATH . $img_name;
        }
        if (ob_get_contents())
            ob_end_clean();

        $headers = FatApp::getApacheRequestHeaders();
        header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        header("Pragma: public");

        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
            exit;
        }

        $file_info = pathinfo($path);
        $extension = strtolower($file_info['extension']);

        switch ($extension) {
            case 'pdf':
                header("Content-type: application/pdf");
                break;
            case 'jpeg':
            case 'jpg':
                header('Content-type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'svg':
                header('Content-type: image/svg+xml');
                break;
            case 'doc':
                header('Content-type: application/msword');
                header('Content-Disposition: inline; filename="' . $img_name . '"');
                break;
            case 'docx':
                header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Disposition: inline; filename="' . $img_name . '"');
                break;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);
        header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
		
		readfile($path);
    }

    public static function editorSvg($path) {
        if (ob_get_contents())
            ob_end_clean();
        header('Content-type: image/svg+xml');
        header("Pragma: public");
        header('Cache-Control: public, must-revalidate');
        header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));

        $headers = FatApp::getApacheRequestHeaders();

        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            exit;
        }

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);
        readfile($path);
    }

    static function crop($data, $src) {
        if (!empty($data)) {

            $size = getimagesize($src);
            $size_w = $size[0]; // natural width
            $size_h = $size[1]; // natural height

            $src_img_w = $size_w;
            $src_img_h = $size_h;

            $degrees = $data->rotate;
            switch ($size['mime']) {
                case "image/gif":
                    $src_img = imagecreatefromgif($src);
                    break;

                case "image/jpeg":
                    $src_img = imagecreatefromjpeg($src);
                    break;

                case "image/png":
                    $src_img = imagecreatefrompng($src);
                    break;
            }

            // Rotate the source image
            if (is_numeric($degrees) && $degrees != 0) {
                // PHP's degrees is opposite to CSS's degrees
                $new_img = imagerotate($src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127));

                imagedestroy($src_img);
                $src_img = $new_img;

                $deg = abs($degrees) % 180;
                $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

                $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
                $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

                // Fix rotated image miss 1px issue when degrees < 0
                $src_img_w -= 1;
                $src_img_h -= 1;
            }

            $tmp_img_w = $data->width;
            $tmp_img_h = $data->height;
            $dst_img_w = 500;
            $dst_img_h = 500;

            $src_x = $data->x;
            $src_y = $data->y;

            if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
                $src_x = $src_w = $dst_x = $dst_w = 0;
            } else if ($src_x <= 0) {
                $dst_x = -$src_x;
                $src_x = 0;
                $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
            } else if ($src_x <= $src_img_w) {
                $dst_x = 0;
                $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
            }

            if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
                $src_y = $src_h = $dst_y = $dst_h = 0;
            } else if ($src_y <= 0) {
                $dst_y = -$src_y;
                $src_y = 0;
                $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
            } else if ($src_y <= $src_img_h) {
                $dst_y = 0;
                $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
            }

            // Scale to destination position and size
            $ratio = $tmp_img_w / $dst_img_w;
            if ($ratio == 0)
                $ratio = 0.00001;

            $dst_x /= $ratio;
            $dst_y /= $ratio;
            $dst_w /= $ratio;
            $dst_h /= $ratio;

            $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

            // Add transparent background to destination image
            imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
            imagesavealpha($dst_img, true);

            $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

            if ($result) {
                if (!imagepng($dst_img, $src)) {
                    echo "Failed to save the cropped image file";
                    exit;
                }
            } else {
                echo $msg = "Failed to crop the image file";
                exit;
            }

            imagedestroy($src_img);
            imagedestroy($dst_img);
        }
    }

    static function setSocialSession($data) {
        $_SESSION[static::SOCIAL_SESSION_NAME] = $data;
    }

    static function getSocialSession() {
        if (!empty($_SESSION[static::SOCIAL_SESSION_NAME]))
            return $_SESSION[static::SOCIAL_SESSION_NAME];
        return false;
    }

    static function unsetSocialSession() {
        if (!empty($_SESSION[static::SOCIAL_SESSION_NAME]))
            unset($_SESSION[static::SOCIAL_SESSION_NAME]);
    }

    static function getInnovaEditorObj($textarea_id, $div_id, $js_tag = true) {
        $innova_obj = 'window["site_' . $textarea_id . '"] = new InnovaEditor("site_' . $textarea_id . '");
					window["site_' . $textarea_id . '"].mode = "XHTMLBody";
					window["site_' . $textarea_id . '"].width = "100%";
					window["site_' . $textarea_id . '"].groups = [
						["group1", "", ["Bold", "Italic", "Underline", "FontDialog", "ForeColor", "TextDialog", "RemoveFormat"]],
						["group2", "", ["Bullets", "Numbering", "JustifyLeft", "JustifyCenter", "JustifyRight"]],
						["group3", "", ["LinkDialog"]],
					];
					window["site_' . $textarea_id . '"].returnKeyMode = 2;
					window["site_' . $textarea_id . '"].REPLACE("' . $textarea_id . '", "' . $div_id . '");
					
					';

        if ($js_tag) {
            $innova_obj = '<script>' . $innova_obj . '</script>';
        }
        return $innova_obj;
    }
	


    static function getCaptchaObject() {
        require_once CONF_INSTALLATION_PATH . 'library/securimage/securimage.php';
        $img = new Securimage();
        return $img;
    }

    static function truncateString($string, $char_limit = 100, $read_more_link = false, $dots = '...') {
        if (strlen($string) <= $char_limit) {
            return $string;
        }
        $string = substr($string, 0, $char_limit);
        $string .=$dots;
        if (!empty($read_more_link)) {
            $string .=$read_more_link;
        }
        return $string;
    }

    static function addBrString($string, $char_limit = 100) {
        if (strlen($string) <= $char_limit) {
            return $string;
        }
        $br_string = '';

        $str_len = strlen($string);
        $last_space_position = 0;
        $reset_counter = 0;
        for ($i = 0; $i <= $str_len; $i++) {
            $reset_counter++;
            $char = $string[$i];
            if ($reset_counter == $char_limit) {
                if ($char == ' ') {
                    $br_string .='<br>';
                    $char = '';
                } else {
                    $br_string .='-<br>';
                    $char = $string[$i];
                }
                $reset_counter = 0;
            }

            $br_string .=$char;
        }

        return $br_string;
    }

    static function noRecord($text) {

        return '<div class="container container--static"><div class="span__row">
					<div class="span span--12">
						<div class="no-record">
							<div>
								<svg class="icon icon--no-record"><use xlink:href="#icon-no-record" /></svg>
								<label>' . $text . '</label>
							</div>
						</div>
					</div>
				</div></div>';
    }

    static function saveLoginRedirctUrl($url) {
        $_SESSION[self::LAST_URL] = $url;
    }

    static function getLoginRedirctUrl() {
        if (!empty($_SESSION[self::LAST_URL])) {
            $url = $_SESSION[self::LAST_URL];
            unset($_SESSION[self::LAST_URL]);
            return $_SESSION[self::LAST_URL];
        }
        return false;
    }

    function getVideoType($url, $attr = array()) {
        $url = trim($url);
        if (!$this->validDomains($url)) {
            return false;
        }
        if (strpos($url, 'youtu') !== false) {
            return 1;
        }
        return 2;
    }

    static function isValidVideoUrl($url) {
        $url = trim($url);
        $validDomains = Info::validVideoDomains();

        foreach ($validDomains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function staticImage($img_name, $w = 0, $h = 0, $folder = '', $type = '') {

        if ($img_name != "" and file_exists(CONF_THEME_PATH . 'images/' . $folder . $img_name)) {
            $fatImage = CONF_THEME_PATH . 'images/' . $folder . $img_name;
        } else {
            return;
        }

        if (ob_get_contents())
            ob_end_clean();

        $headers = FatApp::getApacheRequestHeaders();
        header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        header("Pragma: public");
        header("Expires: " . date('D, d M Y H:i:s', strtotime("+180 days")));
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($fatImage))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($fatImage)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($fatImage)) . ' GMT', true, 200);
        $width = max(1, FatUtility::int($w));
        $height = max(1, FatUtility::int($h));

        list($width_orig, $height_orig) = getimagesize($fatImage);

        switch ($type) {
            case 'fullwidthbanner':
                $ratio_orig = 4.3;
                break;
            default:
                $ratio_orig = 1;
                break;
        }

        if ($width / $height > $ratio_orig) {
            $width = $height_orig * $ratio_orig;
        } else {
            $height = $width_orig / $ratio_orig;
        }
        $img = new ImageResize($fatImage);

        $img->setMaxDimensions($width, $height);
        $img->displayImage(80);
    }

    public static function fat_shortcode($shortcode = '') {
        $shortcodeObj = Shortcodes::getInstance();
        return $shortcodeObj->parse($shortcode);
    }

    public static function getNewsletterForm($params = array('fieldcols' => 9)) {
        $frm = new Form('frmNews');

        /* $frm->setFormTagAttribute('action', FatApp::getConfig('CONF_MAILCHIMP_NEWS_LETTER_URL', FatUtility::VAR_STRING, ''));
          $frm->setFormTagAttribute('id', 'mc-embedded-subscribe-form');
          $frm->setFormTagAttribute('class', 'newsletter__form');
          $frm->setFormTagAttribute('method', 'post');

          $fld->addFieldTagAttribute('title', Info::t_lang('EMAIL_ADDRESS'));
          $fld->addFieldTagAttribute('id', 'mce-EMAIL');
          $fld->addFieldTagAttribute('class', 'mcfat-email');
          $fld->addFieldTagAttribute('placeholder',Info::t_lang("ENTER_YOUR_EMAIL_ADDRESS")); */

        $fld = $frm->addEmailField('', 'EMAIL', '');
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang("Subscribe"));

        $frm->setJsErrorDisplay('afterfield');
        $frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
        return $frm;
    }

    public static function returnBytes($val, $withUnits = true) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val = ((($val * 1024) * 1024) * 1024);
                $ret['val'] = $val;
                break;
            case 'm':
                $val = (($val * 1024) * 1024);
                $ret['val'] = $val;
                break;
            case 'k':
                $val *= 1024;
                $ret['val'] = $val;
                break;
        }

        if (true == $withUnits) {
            $ret['unit'] = $last;
        }
        return $ret;
    }

    public static function maxFileUpload($maxUpload = false, $withUnits = true) {

        //select maximum upload size
        $limit['max_upload'] = self::returnBytes(ini_get('upload_max_filesize'), $withUnits);
        //select post limit
        $limit['max_post'] = self::returnBytes(ini_get('post_max_size'), $withUnits);

        if ($maxUpload == false) {
            //select memory limit
            $limit['memory_limit'] = self::returnBytes(ini_get('memory_limit'), $withUnits);
        }

        // return the smallest of them, this defines the real limit
        if (true == $withUnits) {
            $min_size = -1;
            $unit = '';
            foreach ($limit as $obj) {
                if ($obj['val'] > 0 && ($min_size <= 0 || $min_size > $obj['val'])) {
                    $min_size = $obj['val'];
                    $unit = $obj['unit'];
                }
            }
            switch ($unit) {
                case 'g':
                    $min_size = ((($min_size / 1024) / 1024) / 1024);
                    break;
                case 'm':
                    $min_size = (($min_size / 1024) / 1024);
                    break;
                case 'k':
                    $min_size = ($min_size / 1024);
                    break;
            }
            return $min_size . ' ' . strtoupper($unit);
        } else {
            $limit = array_column($limit, 'val');
            $bytes = min($limit);
            return $bytes;
        }
    }

    /* public static function file_upload_max_size() {
      static $max_size = -1;

      if ($max_size < 0) {
      // Start with post_max_size.
      $max_size = self::parse_size(ini_get('post_max_size'));

      // If upload_max_size is less, then reduce. Except if upload_max_size is
      // zero, which indicates no limit.
      $upload_max = self::parse_size(ini_get('upload_max_filesize'));
      if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
      }
      }
      return $max_size;
      }

      public static function parse_size($size) {
      $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
      $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
      if ($unit) {
      // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
      return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
      }
      else {
      return round($size);
      }
      } */

    /* 0081 [ */

    public static function fatImage($id = 0, $recordId = 0, $recordSubId = 0, $type = 0, $w = 0, $h = 0, $path = '', $defImage = 'img/no-photo.png', $cache = true) {
        $id = FatUtility::int($id);
        $recordId = FatUtility::int($recordId);
        $recordSubId = FatUtility::int($recordSubId);

        if ($id > 0) {
            $row = AttachedFile::getAttributesById($id);
        } else {
            $row = AttachedFile::getAttachment($type, $recordId, $recordSubId);
        }

		if(file_exists(CONF_THEME_PATH . $defImage)) {
			$defImagePath = CONF_THEME_PATH . $defImage;
		} else if(file_exists(CONF_UPLOADS_PATH . 'default-images/' . $defImage)) {
			$defImagePath = CONF_UPLOADS_PATH . 'default-images/' . $defImage;
		}
		
        if (!empty($row)) {
            $fileExt = SELF::getFileExtension($row ['afile_name']);
            if (!in_array($fileExt, array('jpeg', 'gif', 'jpg', 'png', 'svg', 'bmp'))) {
                $imgFile = $defImagePath;
            } else {
                $imgFile = CONF_UPLOADS_PATH . $row ['afile_physical_path'];

                if (!file_exists($imgFile))
                    $imgFile = $defImagePath;
            }
        }
        else {
            $imgFile = $defImagePath;
        }

        // echo $imgFile; exit;

        if (ob_get_contents())
            ob_end_clean();

        header('Cache-Control: public, max-age=604800, stale-while-revalidate=86400');
        header("Pragma: public");
        header("Expires: " . date('r', strtotime("+30 days")));

        $headers = FatApp::getApacheRequestHeaders();
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($imgFile))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($imgFile)) . ' GMT', true, 304);
            header("Expires: " . date('r', strtotime("+30 days")));
            exit;
        }

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($imgFile)) . ' GMT', true, 200);

        $img = new ImageResize($imgFile);

        $w = max(1, FatUtility::int($w));
        $h = max(1, FatUtility::int($h));

        $img->setMaxDimensions($w, $h);
		ob_end_clean();
		/*if ( substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ){*/
		if ( array_key_exists('HTTP_ACCEPT_ENCODING', $_SERVER) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
			ob_start("ob_gzhandler");
		}else {
			ob_start();
		}
		
		$img->displayImage();
		$imqg = ob_get_clean();
			
        if (true == $cache) {
			$cacheKey = $_SERVER['REQUEST_URI'];
            FatCache::set($cacheKey, $imqg, '.jpg');
        }
		echo $imqg;
    }

    public static function getFileExtension($fileName) {
        return strtolower(substr(strrchr($fileName, "."), 1));
    }

    public static function getCityCoordinates($city, &$error) {
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCyDAWsixlw-IFYnoAqLhcz7r_f1h01T6s&address=" . urlencode($city);
        $response = file_get_contents($url);
        if (!$response) {
            $error = "City Name is not Valid";
            return false;
        }
        $result = json_decode($response, true);
        if (strtoupper($result['status']) != "OK" || !isset($result['results'][0])) {

            $error = "City Name is not Valid";
            return false;
        }

        $dataSet = $result['results'][0]['geometry'];
        return $dataSet['location'];
    }

    static function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = Helper::parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = Helper::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    static function parse_size($size) {
        
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
        
    }
	static function filesize_formatted($size) {

		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}
	static function str_replace_last( $search, $replace, $subject ) {
		if ( !$search || !$replace || !$subject )
			return false;
		
		$index = strrpos( $subject, $search );
		if ( $index === false )
			return $subject;
		
		// Grab everything before occurence
		$pre = substr( $subject, 0, $index );
		
		// Grab everything after occurence
		$post = substr( $subject, $index );
		
		// Do the string replacement
		$post = str_replace( $search, $replace, $post );
		
		// Recombine and return result
		return $pre . $post;
	}
		/* ] */
	public static function verifyCaptcha($g_recaptcha_response) {
		if(empty($g_recaptcha_response)) {
			return false;
		}

		$captchaSecretKey	= FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '');
		$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$captchaSecretKey."&response=".$g_recaptcha_response."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
		 
		if($response['success'] == false){
			return false;
		}
		return true;
	}
}

?>