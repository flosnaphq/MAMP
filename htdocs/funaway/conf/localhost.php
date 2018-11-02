<?php
/* * * Database configurations */
define('CONF_DB_SERVER', 'localhost');
define('CONF_DB_USER', 'root');
define('CONF_DB_PASS', 'root');
define('CONF_DB_NAME', 'funaway');

if (strpos($_SERVER['SERVER_NAME'], 'localhost') > 0) {
    define('CONF_CORE_LIB_PATH', '/etc/fatlib/');
} else {
    define('CONF_CORE_LIB_PATH', CONF_INSTALLATION_PATH . 'library/core/');
}
?>