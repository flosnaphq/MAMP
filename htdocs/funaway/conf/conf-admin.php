<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'conf-common.php';

define('CONF_APPLICATION_PATH', CONF_INSTALLATION_PATH . 'admin-application/');
define('CONF_THEME_PATH', CONF_APPLICATION_PATH . 'views/');
define('CONF_UTILITY_PATH', CONF_APPLICATION_PATH . 'utilities/');

define('SYSTEM_FRONT', false);

define('CONF_USER_UPLOADS_PATH', CONF_INSTALLATION_PATH . 'user-uploads/');
define('CONF_DB_BACKUP_DIRECTORY', 'database-backups');
define('CONF_DB_BACKUP_DIRECTORY_FULL_PATH', CONF_USER_UPLOADS_PATH . CONF_DB_BACKUP_DIRECTORY.'/');

define('CONF_WEBROOT_URL', (strlen(CONF_BASE_DIR) > 0 ? CONF_BASE_DIR : '').'admin/');
define('CONF_WEBROOT_URL_TRADITIONAL', '/public/admin.php?url=');