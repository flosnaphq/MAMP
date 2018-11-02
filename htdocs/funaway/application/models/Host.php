<?php

class Activity extends MyAppModel
{

    const DB_TBL = 'tbl_activities';
    const DB_TBL_PREFIX = 'activity_';

//	const SESSION_ELEMENT_NAME = 'UserSession'; 

    public function __construct($userId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $userId);
        $this->objMainTableRecord->setSensitiveFields(array(
            'user_regdate'
        ));
    }

    public function save()
    {
        if (!($this->mainTableRecordId > 0)) {
            $this->setFldValue('user_regdate', date('Y-m-d H:i:s'));
        }

        return parent::save();
    }

    public function setLoginCredentials($username, $password, $active = null, $verified = null)
    {
        if (!($this->mainTableRecordId > 0)) {
            $this->error = 'Invalid Request! User not initialized.';
            return false;
        }

        $record = new TableRecord('tbl_users');
        $arrFlds = array(
            'user_email' => $username,
            'user_password' => User::encryptPassword($password)
        );

        if (null != $active) {
            $arrFlds ['user_active'] = $active;
        }
        if (null != $verified) {
            $arrFlds ['user_verified'] = $verified;
        }

        $record->setFldValue('user_id', $this->mainTableRecordId);
        $record->assignValues($arrFlds);
        if (!$record->addNew(array(), $arrFlds)) {
            $this->error = $record->getError();
            return false;
        }

        return true;
    }

    public function verifyAccount($v = 1)
    {
        if (!($this->mainTableRecordId > 0)) {
            $this->error = 'User not set.';
            return false;
        }

        $db = FatApp::getDb();
        if (!$db->updateFromArray('tbl_users', array(
                    'user_verified' => $v
                        ), array(
                    'smt' => 'user_id = ?',
                    'vals' => array(
                        $this->mainTableRecordId
                    )
                ))) {
            $this->error = $db->getError();
            return false;
        }

        // You may want to send some email notification to user that his account is verified.

        return true;
    }

    public function activateAccount($v = 1)
    {
        if (!($this->mainTableRecordId > 0)) {
            $this->error = 'User not set.';
            return false;
        }

        $db = FatApp::getDb();
        if (!$db->updateFromArray('tbl_users', array(
                    'user_active' => $v
                        ), array(
                    'smt' => 'user_id = ?',
                    'vals' => array(
                        $this->mainTableRecordId
                    )
                ))) {
            $this->error = $db->getError();
            return false;
        }

        return true;
    }

    public function getProfileData()
    {
        return $this->getAttributesById($this->mainTableRecordId);
    }

    public static function encryptPassword($pass)
    {
        return md5(PASSWORD_SALT . $pass . PASSWORD_SALT);
    }

    public function logFailedAttempt($ip, $username)
    {
        $db = FatApp::getDb();

        $db->deleteRecords('tbl_failed_login_attempts', array(
            'smt' => 'attempt_time < ?',
            'vals' => array(
                date('Y-m-d H:i:s', strtotime("-7 Day"))
            )
        ));

        $db->insertFromArray('tbl_failed_login_attempts', array(
            'attempt_username' => $username,
            'attempt_ip' => $ip,
            'attempt_time' => date('Y-m-d H:i:s')
        ));

        // For improvement, we can send an email about the failed attempt here.
    }

    public function isBruteForceAttempt($ip, $username)
    {
        $db = FatApp::getDb();

        $srch = new SearchBase('tbl_failed_login_attempts');
        $srch->addCondition('attempt_ip', '=', $ip)->attachCondition('attempt_username', '=', $username);
        $srch->addCondition('attempt_time', '>=', date('Y-m-d H:i:s', strtotime("-4 Minute")));
        $srch->addFld('COUNT(*) AS total');

        $rs = $srch->getResultSet();

        $row = $db->fetch($rs);

        return ($row['total'] > 2);
    }

    public function login($username, $password, $ip, $encryptPassword = true)
    {
        if ($this->isBruteForceAttempt($ip, $username)) {
            $this->error = 'Login attempt limit exceeded. Please try after some time.';
            return false;
        }

        if ($encryptPassword) {
            $password = User::encryptPassword($password);
        }

        $db = FatApp::getDb();
        $srch = new SearchBase('tbl_users');
        $srch->addCondition('user_email', '=', $username);
        $srch->addCondition('user_password', '=', $password);
        $rs = $srch->getResultSet();

        if (!$row = $db->fetch($rs)) {
            $this->logFailedAttempt($ip, $username);
            $this->error = 'Invalid Email or Password';
            return false;
        }
        if (strtolower($row['user_email']) != strtolower($username) || $row['user_password'] != $password) {
            $this->logFailedAttempt($ip, $username);
            $this->error = 'Invalid Email or Password';
            return false;
        }

        if ($row['user_active'] != 1) { // See I am not comparing it to zero.
            $this->error = 'Error! Your account has been deactivated. Please contact administrator.';
            return false;
        }
        if ($row['user_verified'] != 1) { // See I am not comparing it to zero.
            $this->error = 'Error! Account verification pending.';
            return false;
        }

        $rowUser = User::getAttributesById($row['user_id']);

        $_SESSION[static::SESSION_ELEMENT_NAME] = array(
            'user_id' => $rowUser['user_id'],
            'user_name' => $rowUser['user_firstname'],
            'user_email' => $rowUser['user_email'],
            'user_type' => $rowUser['user_type'],
            'user_ip' => $ip
        );

        return true;
    }

    public static function isUserLogged($ip = '')
    {
        if ($ip == '') {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!isset($_SESSION [static::SESSION_ELEMENT_NAME]) || $_SESSION [static::SESSION_ELEMENT_NAME] ['user_ip'] != $ip || !is_numeric($_SESSION [static::SESSION_ELEMENT_NAME] ['user_id']) || 0 >= $_SESSION [static::SESSION_ELEMENT_NAME] ['user_id']) {
            return false;
        }


        return true;
    }

    public static function getLoggedUserAttribute($attr, $returnNullIfNotLogged = false)
    {
        if (!static::isUserLogged()) {
            if ($returnNullIfNotLogged)
                return null;

            FatUtility::dieWithError('User Not Logged.');
        }

        if (array_key_exists($attr, $_SESSION [static::SESSION_ELEMENT_NAME])) {
            return $_SESSION [static::SESSION_ELEMENT_NAME][$attr];
        }

        return User::getAttributesById($_SESSION[static::SESSION_ELEMENT_NAME]['user_id'], $attr);
    }

    public static function getLoggedUserId($returnZeroIfNotLogged = false)
    {
        return FatUtility::int(static::getLoggedUserAttribute('user_id', $returnZeroIfNotLogged));
    }

}
