<?php

require_once dirname(__DIR__) . '/conf/conf.php';

$filename = CONF_UPLOADS_PATH.'database-restore-progress.txt'; 
if (file_exists($filename)) {
	$filelastmodified = filemtime($filename);   	
	if((time() - $filelastmodified) < 5*60){
		require_once('maintenance.php');
		exit;		
	}
	@unlink(CONF_UPLOADS_PATH.'database-restore-progress.txt');
}

require_once dirname(__FILE__) . '/application-top.php';
define ('CONF_FORM_ERROR_DISPLAY_TYPE', Form::FORM_ERROR_TYPE_AFTER_FIELD);
define('CONF_FORM_REQUIRED_STAR_WITH', Form::FORM_REQUIRED_STAR_WITH_CAPTION);
define('CONF_FORM_REQUIRED_STAR_POSITION', Form::FORM_REQUIRED_STAR_POSITION_AFTER);

FatApp::unregisterGlobals();
FatApplication::getInstance()->setControllersForStaticFileServer(array('images','img','fonts','favicons','innovas')); 
FatApplication::getInstance()->callHook();