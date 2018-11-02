<?php
session_start();

if (extension_loaded('zlib')) {
	ob_end_clean();
}

if ( substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ){
    ob_start("ob_gzhandler");
}
else {
    ob_start();
}

ini_set('display_errors', (CONF_DEVELOPMENT_MODE)?1:0);
error_reporting( (CONF_DEVELOPMENT_MODE)?E_ALL:E_ALL & ~E_NOTICE & ~E_WARNING);
require_once CONF_INSTALLATION_PATH . 'library/autoloader.php';

/*
 *  Events Handling
 */

require_once CONF_INSTALLATION_PATH.'conf/events-conf.php';
/* We must set it before initiating db connection. So that connection timezone is in sync with php */
date_default_timezone_set('America/New_York');
// //date_default_timezone_set('UTC+07:00');
// ini_set('session.cookie_httponly', true);
// ini_set('session.cookie_path', CONF_WEBROOT_URL);
// define('SYSTEM_INIT', true);

// FatCache Settings

$fatCacheEnabled = FatUtility::int(FatApp::getConfig('conf_fat_cache_enabled', FatUtility::VAR_INT, 0));
$fatCacheEnabled = ((1 === $fatCacheEnabled) ? false : false);

/*$fatCacheEnabled = true; */
define('CONF_USE_FAT_CACHE', $fatCacheEnabled);
define('CONF_FAT_CACHE_DIR', CONF_INSTALLATION_PATH . 'public/cache/');
define('CONF_FAT_CACHE_URL', '/cache/');
define('CONF_DEF_CACHE_TIME', 2592200); // in seconds (2592200 = 30 days)