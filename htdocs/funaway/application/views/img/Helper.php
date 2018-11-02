<?php
class Helper extends FatModel { 
	const SOCIAL_SESSION_NAME = 'SocialMedia'; 
	const LAST_URL = 'last_url'; 
    function __construct() {
        parent::__construct();
    }
	
	
	function hostMenu(){
		$menu = array();
		$i = 0;
	
		$menu[++$i] = array("name"=>Info::t_lang("MY_PROFILE"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>Info::t_lang("MESSAGES"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>"Manage Cms","link"=>"javascript:;");
			$menu[$i]['child'][] = array("name"=>"Manage Cms","link"=>FatUtility::generateUrl('cms'));
			
			$menu[$i]['child'][] = array("name"=>"Manage FAQ","link"=>FatUtility::generateUrl('faq'));
			$menu[$i]['child'][] = array("name"=>"Manage Home Page Banner","link"=>FatUtility::generateUrl('Banners'));
		$menu[++$i] = array("name"=>Info::t_lang("PAYOUT_SETTINGS"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>Info::t_lang("MY_BOOKINGS"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>Info::t_lang("REPORTS"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>Info::t_lang("MY_LISTINGS"),"link"=>FatUtility::generateUrl("host"));
		
		return $menu;
	}
	
	function travelerMenu(){
		$menu = array();
		$i = 0;
	
		$menu[++$i] = array("name"=>Info::t_lang("MY_PROFILE"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>Info::t_lang("MESSAGES"),"link"=>FatUtility::generateUrl("host"));
		
		$menu[++$i] = array("name"=>Info::t_lang("MY_BOOKINGS"),"link"=>FatUtility::generateUrl("host"));
		$menu[++$i] = array("name"=>Info::t_lang("WISHLIST"),"link"=>FatUtility::generateUrl("host"));
		
		return $menu;
		
	}
	
	static public function deleteSingleAttachedFile($file_id){
		$db = FatApp::getDb();
		$db->deleteRecords ("tbl_attached_files", array (
					'smt' => 'afile_id = ? ',
					'vals' => array ($file_id) 
			) );
		
	}
	
	static public function deleteFileByRecord($file_id,$record_id){
		$db = FatApp::getDb();
		$db->deleteRecords ("tbl_attached_files", array (
					'smt' => 'afile_id = ? and afile_record_id = ?',
					'vals' => array ($file_id,$record_id) 
			) );
		
	}
	
	static public function deleteMultipleAttachedFile($fileType,$recordId,$recordSubid = 0){
		$db = FatApp::getDb();
		$db->deleteRecords ("tbl_attached_files", array (
					'smt' => 'afile_type = ? AND afile_record_id = ? AND afile_record_subid = ? ',
					'vals' => array ($fileType, $recordId, $recordSubid) 
			) );
	}
	
	
	public static function displayImage($img_name,$type,$w = 0, $h = 0, $folder='') {
		ob_end_clean();
		$folder = !empty($folder)?trim($folder,'/').'/':''; 
		if($w == 0 || $h ==0){
			self::showImage($img_name, $folder);
			exit;
		}
		if($img_name !="" && file_exists(CONF_UPLOADS_PATH.$folder . $img_name)){
			$path = CONF_UPLOADS_PATH . $folder. $img_name;
			
		}else{
			switch($type){
				case "activity":
						$path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
					break;
				case "service":
						$path = CONF_THEME_PATH . 'img/service-no-image.jpg';
					break;
				case "user":
						$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
					break;
				case "admin":
						$path = CONF_THEME_PATH . 'img/admin.png';
					break;
				case "banner":
						$path = CONF_THEME_PATH . 'img/banner-no-image.jpg';
					break;
				default:
					$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
			}	
			
		}
		
		$headers = FatApp::getApacheRequestHeaders();
		 if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			header('Cache-Control: public, max-age=1, must-revalidate');
			header("Pragma: public");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			header("Expires: " . date('r', strtotime("+30 days")));
			exit;
		} 
		$img = new ImageResize($path);
		header('Cache-Control: public, max-age=1, must-revalidate');
		header("Pragma: public");
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
		header("Expires: " . date('r', strtotime("+30 days")));
		$w = max(1, FatUtility::int($w));
		$h = max(1, FatUtility::int($h));
		$img->setMaxDimensions($w, $h);
		$img->displayImage();
	}
	
	
	public static function cropImage($img_name,$type,$w = 0, $h = 0, $folder ='') {
		ob_end_clean();
		$folder = !empty($folder)?trim($folder,'/').'/':''; 
		if($img_name !="" and file_exists(CONF_UPLOADS_PATH .$folder. $img_name)){
			$path = CONF_UPLOADS_PATH.$folder . $img_name;
		}else{
			switch($type){
				case "activity":{
						$path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
					break;
				}
				case "service":{
						$path = CONF_THEME_PATH . 'img/service-no-image.jpg';
					break;
				}
				case "user":{
						$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
					break;
				}
				case "admin":{
						$path = CONF_THEME_PATH . 'img/admin.png';
					break;
				}
				case "blogPost":{
						$path = CONF_THEME_PATH . 'img/service-no-image.png';
					break;
				}
				default:{
					$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
				}
			}	
			
		}
		
		$headers = FatApp::getApacheRequestHeaders();
	 	 if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			header('Cache-Control: public, max-age=1, must-revalidate');
			header("Pragma: public");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			header("Expires: " . date('r', strtotime("+30 days")));
			exit;
		}  
	 	header('Cache-Control: public, max-age=1, must-revalidate');
		header("Pragma: public");
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200); 
		header("Expires: " . date('r', strtotime("+30 days")));
		$width = max(1, FatUtility::int($w));
		$height = max(1, FatUtility::int($h));
		
		$img = new ImageResize ( $path );
		
		$w = max(1, FatUtility::int($width));
		$h = max(1, FatUtility::int($height));
		
		$img->setMaxDimensions($w, $h);
		$img->setResizeMethod(3);
		$img->displayImage();
		// exit;
		/* list($width_orig, $height_orig) = getimagesize($path);
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
	
	public static function catImage($img_name,$type,$w = 0, $h = 0) {
		ob_end_clean();
		
		if($img_name !="" and file_exists(CONF_UPLOADS_PATH . $img_name)){
			$path = CONF_UPLOADS_PATH . $img_name;
		}else{
			switch($type){
				case "activity":{
						$path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
					break;
				}
				case "service":{
						$path = CONF_THEME_PATH . 'img/service-no-image.jpg';
					break;
				}
				case "user":{
						$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
					break;
				}
				case "admin":{
						$path = CONF_THEME_PATH . 'img/admin.png';
					break;
				}
				default:{
					$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
				}
			}	
			
		}
		
		//ob_end_clean();
		// var_dump($imgFile);exit;
		header('Cache-Control: public');
		header("Pragma: public");
		header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
		
		$headers = FatApp::getApacheRequestHeaders();
	 	 if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			// header('Cache-Control: public, max-age=1, must-revalidate');
			// header("Pragma: public");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			// header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
			exit;
		}  
	 	// header('Cache-Control: public, max-age=1, must-revalidate');
		// header("Pragma: public");
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200); 
		// header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
		$width = max(1, FatUtility::int($w));
		$height = max(1, FatUtility::int($h));
		list($width_orig, $height_orig) = getimagesize($path);
				$ratio_orig = 1;
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
		}
		
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		imagejpeg($image_p, null, 100);
		
	}
	
	
	public static function showImage($img_name, $folder='', $type = '') {
		ob_end_clean();
		
		if($img_name != "" and file_exists(CONF_UPLOADS_PATH .$folder. $img_name))
		{
			$path = CONF_UPLOADS_PATH .$folder. $img_name;
		}
		else{
			switch($type){
				case "activity":{
						$path = CONF_THEME_PATH . 'img/activity-no-image.jpg';
					break;
				}
				case "service":{
						$path = CONF_THEME_PATH . 'img/service-no-image.jpg';
					break;
				}
				case "user":{
						$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
					break;
				}
				case "admin":{
						$path = CONF_THEME_PATH . 'img/admin.png';
					break;
				}
				case "banner":{
						$path = CONF_THEME_PATH . 'banner-no-image.jpg';
					break;
				}
				default:{
					$path = CONF_THEME_PATH . 'img/user-no-image.jpg';
				}
			}	
			
		}
		$headers = FatApp::getApacheRequestHeaders();
		
	 	 if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			header('Content-type: image/jpeg');
			header('Cache-Control: public, max-age=1, must-revalidate');
			header("Pragma: public");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
			exit;
		} 
		//
	//	$headers = FatApp::getApacheRequestHeaders();
		header('Content-type: image/jpeg');
		header("Pragma: public");
		header('Cache-Control: public, max-age=1, must-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
		header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
		readfile($path);
	}
	
	public static function showFile($img_name, $real_name='') {
		//ob_end_clean();
		if($real_name == ''){
			$real_name = $img_name;
		}
		$path=CONF_THEME_PATH.'img/activity-no-image.jpg';
		
		if($img_name !="" and file_exists(CONF_UPLOADS_PATH . $img_name)){
			$path = CONF_UPLOADS_PATH . $img_name;
		}
		$headers = FatApp::getApacheRequestHeaders();
	 	 if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			//header('Content-type: image/jpeg');
			header('Cache-Control: public, max-age=1, must-revalidate');
			header("Pragma: public");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
			exit;
		}    
		//
	//	$headers = FatApp::getApacheRequestHeaders();
		$file_info = pathinfo($path);
		$extension = strtolower($file_info['extension']);
		
		switch($extension){
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
				header('Content-Disposition: inline; filename="'.$img_name.'"');
				break;
			case 'docx':
				header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
				header('Content-Disposition: inline; filename="'.$img_name.'"');
				break;
		}
		
		
		header("Pragma: public");
		header('Cache-Control: public, max-age=1, must-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
		header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
		readfile($path);
	}
	
	public static function editorSvg($path) {
		
	//	$headers = FatApp::getApacheRequestHeaders();
		$headers = FatApp::getApacheRequestHeaders();
	 	 if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			header('Content-type: image/svg+xml');
			header('Cache-Control: public, must-revalidate');
			header("Pragma: public");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
			exit;
		} 
		header('Content-type: image/svg+xml');
		header("Pragma: public");
		header('Cache-Control: public, must-revalidate');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200); 
		header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));
		readfile($path);
	}
	
	
	
	static function crop($data,$src) {
		if (!empty($data)) {
		  
		  $size = getimagesize($src);
		  $size_w = $size[0]; // natural width
		  $size_h = $size[1]; // natural height

		  $src_img_w = $size_w;
		  $src_img_h = $size_h;

		  $degrees = $data -> rotate;
		#  test($size);exit;
		  switch($size['mime']){
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
		
	#	echo $src_img;exit;
		 //  $src_img = imagecreatefromjpeg($src);
		  // Rotate the source image
		 if (is_numeric($degrees) && $degrees != 0) {
			// PHP's degrees is opposite to CSS's degrees
			$new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );

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

		  $tmp_img_w = $data -> width;
		  $tmp_img_h = $data -> height;
		  $dst_img_w = 500;
		  $dst_img_h = 500;

		  $src_x = $data -> x;
		  $src_y = $data -> y;

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
		  if($ratio==0) $ratio = 0.00001;
		  
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
			  echo "Failed to save the cropped image file";exit;
			}
		  } else {
			echo $msg = "Failed to crop the image file";exit;
		  }

		  imagedestroy($src_img);
		  imagedestroy($dst_img);
		}
	}
	
	static function setSocialSession($data){
		$_SESSION[static::SOCIAL_SESSION_NAME] =$data;
	}
	
	static function getSocialSession(){
		if(!empty($_SESSION[static::SOCIAL_SESSION_NAME])) return $_SESSION[static::SOCIAL_SESSION_NAME];
		return false;
	}
	
	static function unsetSocialSession(){
		if(!empty($_SESSION[static::SOCIAL_SESSION_NAME])) unset($_SESSION[static::SOCIAL_SESSION_NAME]);
	}
	
	
	
	static function getInnovaEditorObj($textarea_id,$div_id,$js_tag=true){
		$innova_obj = 'window["site_'.$textarea_id.'"] = new InnovaEditor("site_'.$textarea_id.'");
					window["site_'.$textarea_id.'"].mode = "XHTMLBody";
					window["site_'.$textarea_id.'"].width = "100%";
					window["site_'.$textarea_id.'"].groups = [
					["group1", "", ["Bold", "Italic", "Underline", "FontDialog", "ForeColor", "TextDialog", "RemoveFormat"]],
					["group2", "", ["Bullets", "Numbering", "JustifyLeft", "JustifyCenter", "JustifyRight"]],
					
				
					];
					window["site_'.$textarea_id.'"].returnKeyMode = 2;
					window["site_'.  $textarea_id .'"].REPLACE("'.$textarea_id.'", "'.$div_id.'");
					
					';
					
		if($js_tag){
			$innova_obj ='<script>'.$innova_obj.'</script>';
		}
		return $innova_obj;
	}
	static function getCaptchaObject(){
		require_once CONF_INSTALLATION_PATH . 'library/securimage/securimage.php';
    	$img = new Securimage();
		return $img;
	}
	
	static function truncateString($string, $char_limit = 100, $read_more_link=false, $dots='...'){
		if(strlen($string) <=  $char_limit){
			return $string;
		}
		$string = substr($string,0, $char_limit);
		$string .=$dots;
		if(!empty($read_more_link)){
			$string .=$read_more_link;
		}
		return $string;
	}
	
	static function addBrString($string, $char_limit = 100){
		if(strlen($string) <=  $char_limit){
			return $string;
		}
		$br_string = '';
		
		$str_len = strlen($string);
		$last_space_position =0;
		$reset_counter = 0;
		for($i=0;$i<=$str_len;$i++){
			$reset_counter++;
			$char = $string[$i];
			if($reset_counter == $char_limit){
				if($char == ' '){
					$br_string .='<br>';
					$char = '';
				}
				else{
					$br_string .='-<br>';
					$char = $string[$i];
				}
				$reset_counter =0;
			}
			
			$br_string .=$char;
			
			
		}
		
		return $br_string;
	}
	
	static function noRecord($text){
		
		return '<div class="span__row">
								<div class="span span--12">
									<div class="no-record">
										<div>
											<svg class="icon icon--no-record"><use xlink:href="#icon-no-record" /></svg>
											<label>'.$text.'</label>
										</div>
									</div>
								</div>
							</div>';
	}
	
	static function saveLoginRedirctUrl($url){
		$_SESSION[self::LAST_URL]=$url;
	}
	
	static function getLoginRedirctUrl(){
		if(!empty($_SESSION[self::LAST_URL])){
			$url = $_SESSION[self::LAST_URL];
			unset($_SESSION[self::LAST_URL]);
			return $_SESSION[self::LAST_URL];
		}
		return false;
	}
	
	function getVideoType($url, $attr = array()){
		$url = trim($url);
		if(!$this->validDomains($url)){
			return false;
		}
		if(strpos($url, 'youtu') !== false){
			return 1;
		}
		return 2;
	}
	
	static function isValidVideoUrl($url){
		$url = trim($url);
		$validDomains = Info::validVideoDomains();
		
		foreach($validDomains as $domain){
			if(strpos($url, $domain) !== false){
				return true;
			}
		}
		
		return false;
		
	}
	
}
	

?>