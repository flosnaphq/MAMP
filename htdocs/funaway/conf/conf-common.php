<?php
/**
 *
 * General configurations
 */

define('CONF_DEVELOPMENT_MODE', true);

define('CONF_LIB_HALDLE_ERROR_IN_PRODUCTION', true);

define ('CONF_URL_REWRITING_ENABLED', true);

define ('CONF_DATE_FORMAT_MYSQL', 'YYYY-mm-dd');

define('PASSWORD_SALT', 'ewoiruqojfklajreajflfdsaf');

define ( 'CONF_INSTALLATION_PATH', dirname ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR );
define ( 'CONF_UPLOADS_PATH', CONF_INSTALLATION_PATH . 'user-uploads' . DIRECTORY_SEPARATOR );

if (file_exists(CONF_INSTALLATION_PATH . 'conf/' . $_SERVER['SERVER_NAME'] . '.php')) {
    require_once(CONF_INSTALLATION_PATH . 'conf/' . $_SERVER['SERVER_NAME'] . '.php');
} else {
    die('Domain specific settings file missing');
} 

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('CONF_BASE_URL', $protocol."://".$_SERVER['SERVER_NAME'].CONF_WEBROOT_URL);
define('CONF_BASE_DIR', '/');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache-constants.php';

define('CONF_MINIFY_CONTENT', false);