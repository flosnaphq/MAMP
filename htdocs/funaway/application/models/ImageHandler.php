<?php 
class ImageHandler extends MyAppModel {
	
	public static function saveImage($fl, $name, &$response, $pathSuffix = '') {
		 
		$size = getimagesize($fl);
		if ($size === false) {
			$response = Info::t_lang( 'IMAGE_ERROR_COULD_NOT_RECOGNIZED' );
			return false;
		}
		
		$fname = preg_replace( '/[^a-zA-Z0-9.]/', '', $name );
		
		while (file_exists(CONF_UPLOADS_PATH . $pathSuffix . $fname)) { 
			$fname .= '_' . rand(10, 999999);
		}
		
		if ( !copy( $fl, CONF_UPLOADS_PATH . $pathSuffix . $fname ) ) { 
			$response = Info::t_lang( 'IMAGE_ERROR_COULD_NOT_SAVE_FILE' );
			return false;
		}
		
		$response = $fname;
		return true;
		
	}
	
	public static function savePngImage($fl, $name, &$response, $pathSuffix = '') {
		 
		$size = getimagesize($fl);
		if ($size['mime'] !== 'image/png' || $size[2] != 3) {
			$response = Info::t_lang( 'IMAGE_ERROR_COULD_NOT_RECOGNIZED_PNG' );
			return false;
		}
		
		$fname = preg_replace('/[^a-zA-Z0-9]/', '', $name);
		while (file_exists( CONF_UPLOADS_PATH . $pathSuffix . $fname)) {
			$fname .= '_' . rand(10, 999999);
		}

		if (!copy($fl,  CONF_UPLOADS_PATH . $pathSuffix . $fname)) {
			$response = Info::t_lang( 'IMAGE_ERROR_COULD_NOT_SAVE_FILE' );
			return false;
		}

		$response = $fname;
		return true;
	}

	public static function showImage($file = '', $w = 0, $h = 0, $path_suffix = '', $noImg = "noImgSelect.jpg") {
		ob_end_clean();
		
		if ( !empty($file) ) {

			$path = CONF_UPLOADS_PATH . $path_suffix . $file;
			if(!file_exists($path)){
				$path = CONF_UPLOADS_PATH . NO_IMG_FOLDER . $noImg ;
			}
			
			$ext = (string)pathinfo($path, PATHINFO_EXTENSION);
			
			if( (string)$ext == 'png' ) { 
				self::showImageWithoutResize( $file, $path_suffix );
				die;
			}
			
			$headers = FatApp::getApacheRequestHeaders();
			if ( isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
				exit;
			}
			try {
				
				$img = new ImageResize ($path);
			
				header('Cache-Control: public');
				header("Pragma: public");
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
				header("Expires: " . date('r', strtotime("+30 Day")));
				
			}
			catch (Exception $e) { 
				$img = new ImageResize(  CONF_UPLOADS_PATH . NO_IMG_FOLDER . $noImg);
			}
		}
		else {
			$img = new ImageResize(  CONF_UPLOADS_PATH . NO_IMG_FOLDER . $noImg);
		}
		
			
		if ($w == 0 || $h == 0) {
			$image_size = getimagesize($img);
		}
		
		if ($w == 0) {
			$w = (isset($image_size[0]) ? $image_size[0] : 100);
		}
		if ($h == 0) {
			$h = (isset($image_size[1]) ? $image_size[1] : 100);
		}
		 
		$w = max(1, FatUtility::int($w));
		$h = max(1, FatUtility::int($h));
		 
		 
		$img->setMaxDimensions($w, $h);
		$img->displayImage();
	}
	 

    public static function showImageWithoutResize( $file = '', $path_suffix = '', $noImg = "noImgSelect.jpg" ) {
        $pth =  CONF_UPLOADS_PATH . $path_suffix . $file;

        if (!is_file($pth)) {
            $pth =  CONF_UPLOADS_PATH . NO_IMG_FOLDER . $noImg;
        }

        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($pth))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($pth)) . ' GMT', true, 304);
            exit;
        }
        header("Expires: " . date("r", time() + (60 * 60 * 24 * 30)));
        header('Cache-Control: public');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($pth)) . ' GMT', true, 200);

        $size = getimagesize($pth);
        if ($size === false) {
            echo 'INVALID_FILE';
            return false;
        }
        header('pragma: public');
        header("Content-Type: " . $size['mime']);
        readfile($pth);
    }
	
	public static function crop($data,$src) {
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
		
		  $tmp_img_w = $data -> width;
		  $tmp_img_h = $data -> height;
		  $dst_img_w = 220;
		  $dst_img_h = 220;

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
	
	public static function companyLogoForEmail(){
		return '<img src="'.FatUtility::generateFullUrl('Image','companyLogo', array(), '/').'"  />';
	}
	

}
?>