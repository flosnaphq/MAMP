<?php
class JsCssController
{
	
	private function checkModifiedHeader()
	{
		$headers = FatApp::getApacheRequestHeaders();
    	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == $_GET['sid'])) {
    		header('Last-Modified: '. date('r', $_GET['sid']), true, 304);
    		exit;
    	}
	}
	
	private function setHeaders($contentType)
	{
		header('Content-Type: ' . $contentType);
		header('Cache-Control: public, max-age=2592000, stale-while-revalidate=604800');
		header("Expires: " . date('r', strtotime("+30 Day")));
		
		$this->checkModifiedHeader();
		
		if (isset($_GET['sid'])) {
			header('Last-Modified: '. date('r', $_GET['sid']), true, 200);
		}
		
		if (! in_array ( 'ob_gzhandler', ob_list_handlers () )) {
			if (substr_count ( $_SERVER ['HTTP_ACCEPT_ENCODING'], 'gzip' )) {
				ob_start ( "ob_gzhandler" );
			} else {
				ob_start ();
			}
		}
	}
	
    function css()
	{
    	$this->setHeaders('text/css');
        
        $arr = explode(',', $_GET['f']);
        
        $str = '';
        foreach ($arr as $fl){
            if (substr($fl, '-4') != '.css') continue;
        	$file = CONF_THEME_PATH . $fl;
            if (file_exists($file)) $str .= file_get_contents($file);
        }
        
        $str = str_replace('../', '', $str);
        
        if (FatApplication::getInstance()->getQueryStringVar('min', FatUtility::VAR_INT) == 1){
            $str = preg_replace('/([\n][\s]*)+/', " ", $str);
            $str = str_replace("\r", '', $str);
            $str = str_replace("\n", '', $str);
        }
        
		if(true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {
			$cacheKey = $_SERVER['REQUEST_URI'];
			FatCache::set($cacheKey, $str, '.css');
		}
		
        echo $str;
    }

    function cssCommon()
	{
    	$this->setHeaders('text/css');
    	
        if ( isset($_GET['f']) ) {
        	$files = $_GET['f'];
        }
        else {
        	$pth = CONF_THEME_PATH . 'common-css';
        	$dir = opendir($pth);
        	$last_updated = 0;
        	$files = '';
        	
        	$arrCommonfiles = scandir($pth, SCANDIR_SORT_ASCENDING);
        	foreach ($arrCommonfiles as $fl) {
        		if (!is_file($pth . DIRECTORY_SEPARATOR . $fl)) continue;
        		if ( '.css' != substr($fl, -4) ) continue;
        		if ( 'noinc-' == substr($fl, 0, 6) ) continue;
        			
        		if ( '' != $files ) $files .= ',';
        		$files .= $fl;
        	}
        }
        
        
        $arr = explode(',', $files);
        
        $str = '';
        foreach ($arr as $fl){
            if (substr($fl, '-4') != '.css') continue;
        	$file = CONF_THEME_PATH . 'common-css' . DIRECTORY_SEPARATOR . $fl;
            if (file_exists($file)) $str .= file_get_contents($file);
        }
        
        $str = str_replace('../', '', $str);
        
        if ( FatApplication::getInstance()->getQueryStringVar('min', FatUtility::VAR_INT, 0) == 1){
            $str = preg_replace('/([\n][\s]*)+/', " ", $str);
            $str = str_replace("\r", '', $str);
            $str = str_replace("\n", '', $str);
        }
		if(true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {
			$cacheKey = $_SERVER['REQUEST_URI'];
			FatCache::set($cacheKey, $str, '.css');
		}
		
        echo $str;
    }
    
    function js(){
    	$this->checkModifiedHeader();
    	 
    	$this->setHeaders('application/javascript');
    	
        $arr = explode(',', $_GET['f']);
        
        $str = '';
        foreach ($arr as $fl){
            if (substr($fl, '-3') != '.js') continue;
            if (file_exists(CONF_THEME_PATH . $fl)) $str .= file_get_contents(CONF_THEME_PATH . $fl);
        }
        if(true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {
			
			$cacheKey = $_SERVER['REQUEST_URI'];
			FatCache::set($cacheKey, $str, '.js');
		}
		
        echo($str);
    }
    
    function jsCommon(){
        $this->checkModifiedHeader();
    	 
    	$this->setHeaders('application/javascript');
        
        if ( isset($_GET['f']) ) {
        	$files = $_GET['f'];
        }
        else {
        	$pth = CONF_THEME_PATH . 'common-js';
        	$dir = opendir($pth);
        	$last_updated = 0;
        	$files = '';
        	$arrCommonfiles = scandir($pth, SCANDIR_SORT_ASCENDING);
        	foreach ($arrCommonfiles as $fl) {
        		if (!is_file($pth . DIRECTORY_SEPARATOR . $fl)) continue;
        		if ( '.js' != substr($fl, -3) ) continue;
        		if ( 'noinc-' == substr($fl, 0, 6) ) continue;
        		 
        		if ( '' != $files ) $files .= ',';
        		$files .= $fl;
        	}
        }
        
        $arr = explode(',', $files);
        
        $str = '';
        foreach ($arr as $fl){
            if (substr($fl, '-3') != '.js') continue;
            if (file_exists(CONF_THEME_PATH . 'common-js' . DIRECTORY_SEPARATOR . $fl)) $str .= file_get_contents(CONF_THEME_PATH . 'common-js' . DIRECTORY_SEPARATOR . $fl);
        }
        
		if(true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {
			$cacheKey = $_SERVER['REQUEST_URI'];
			FatCache::set($cacheKey, $str, '.js');
		}
		
        echo($str);
    }
}