<?php
class SvgController extends AdminBaseController {
	public function index($svg) {
		if (!file_exists(CONF_THEME_PATH."images/".$svg)) {
			FatUtility::exitWithErrorCode(404);
		}
		$path = CONF_THEME_PATH."images/".$svg;
		//header('Content-Type: '.StaticFileServer::mimeType($path));
		
		$headers = FatApp::getApacheRequestHeaders();
		
		if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 304);
			exit;
		}
		
		header('Cache-Control: public');
		header("Pragma: public");
		
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path)).' GMT', true, 200);
		header("Expires: " . date('r', strtotime("+30 Day")), true);
		
		readfile($path);
		
		exit();
	}
	
}?>