<?php

class AdminAuthentication extends FatModel {

    const SESSION_ELEMENT_NAME = 'admins';

    public static function isAdminLogged($ip = '') {
        if ($ip == '') {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (isset($_SESSION[static::SESSION_ELEMENT_NAME]) && $_SESSION[static::SESSION_ELEMENT_NAME]['admin_ip'] == $ip) {
            return true;
        }
        return false;
    }

    public function login($username, $password, $ip) {
        $objUserAuthentication = new UserAuthentication();
        if ($objUserAuthentication->isBruteForceAttempt($ip, $username)) {
            $this->error = 'Login attempt limit exceeded. Please try after some time.';
            return false;
        }

        $password = UserAuthentication::encryptPassword($password);

        $db = FatApp::getDb();
        $srch = new SearchBase('tbl_admin');
        $srch->addCondition('admin_username', '=', $username);
        $srch->addCondition('admin_password', '=', $password);
        $rs = $srch->getResultSet();

        if (!$row = $db->fetch($rs)) {
            $objUserAuthentication->logFailedAttempt($ip, $username);
            $this->error = 'Invalid Username or Password';
            return false;
        }

        if ($row['admin_active'] !== 1) {
            $objUserAuthentication->logFailedAttempt($ip, $username);
            $this->error = 'Your Account is inactive';
            return false;
        }

        if (strtolower($row['admin_username']) != strtolower($username) || $row['admin_password'] != $password) {
            $objUserAuthentication->logFailedAttempt($ip, $username);
            $this->error = 'Invalid Username or Password';
            return false;
        }

        $_SESSION[static::SESSION_ELEMENT_NAME] = array(
            'admin_id' => $row['admin_id'],
            'admin_name' => $row['admin_name'],
            'admin_ip' => $ip,
            'admin_layout' => $row['admin_layout'],
        );

        return true;
    }

    public static function getLoggedAdminAttribute($key, $returnNullIfNotLogged = false) {
        if (!static::isAdminLogged()) {
            if ($returnNullIfNotLogged) {
                return null;
            }
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Your session seems to be expired.');
            }

            FatApp::redirectUser(FatUtility::generateUrl());
        }

        return $_SESSION[static::SESSION_ELEMENT_NAME][$key];
    }

    public function setAdminLayout($layout) {
        if (!static::isAdminLogged()) {
            return false;
        }
        $_SESSION[static::SESSION_ELEMENT_NAME]['admin_layout'] = $layout;
        return true;
    }

    public static function getLoggedAdminId($returnZeroIfNotLogged = false) {
        return FatUtility::int(static::getLoggedAdminAttribute('admin_id', $returnZeroIfNotLogged));
    }

    public static function getLoggedAdminRoleId($returnZeroIfNotLogged = false) {
        echo $adminId = FatUtility::int(static::getLoggedAdminAttribute('admin_id', $returnZeroIfNotLogged));

        $obj = AdminAuthentication::getInstance();
        $data = $obj->getAdminById($adminId);

        return $data['admin_adminrole_id'];
    }

}
