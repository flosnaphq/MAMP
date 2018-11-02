<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'conf-common.php';

define('CONF_APPLICATION_PATH', CONF_INSTALLATION_PATH . 'application/');
define('CONF_THEME_PATH', CONF_APPLICATION_PATH . 'views/');

define('SYSTEM_FRONT', TRUE);
define('CONF_WEBROOT_URL', '/');
define('CONF_WEBROOT_URL_TRADITIONAL', CONF_WEBROOT_URL . 'public/index.php?url=');


if (CONF_URL_REWRITING_ENABLED){
    define('CONF_USER_ROOT_URL', CONF_WEBROOT_URL);
}
else {
    define('CONF_USER_ROOT_URL', CONF_WEBROOT_URL . 'public/');
}