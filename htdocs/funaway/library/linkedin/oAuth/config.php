<?php


$config['callback_url']         =   FatUtility::generateFullUrl('guestUser','linkedin_login',array(),'/');

//linkedin configuration
$config['linkedin_access']      =   FatApp::getConfig('CONF_LINKEDIN_ACCESS');
$config['linkedin_secret']      =   FatApp::getConfig('CONF_LINKEDIN_SECRET'); 
$config['linkedin_library_path']=   'linkedinoAuth.php';

?>
