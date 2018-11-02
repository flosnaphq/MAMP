<?php
class UserAuthentication extends FatModel {
	const SESSION_ELEMENT_NAME = 'userSession'; 
	
	public static function encryptPassword($pass) {
		return md5(PASSWORD_SALT . $pass . PASSWORD_SALT);
	}
	
	public function logFailedAttempt($ip, $username) {
		$db = FatApp::getDb();
		
		$db->deleteRecords ( 'tbl_failed_login_attempts', array (
				'smt' => 'attempt_time < ?',
				'vals' => array (
						date ( 'Y-m-d H:i:s', strtotime ( "-7 Day" ) ) 
				) 
		) );
		
		$db->insertFromArray('tbl_failed_login_attempts', array(
				'attempt_username'=>$username,
				'attempt_ip'=>$ip,
				'attempt_time'=>date('Y-m-d H:i:s')
		));
		
		// For improvement, we can send an email about the failed attempt here.
	}
	
	public function isBruteForceAttempt($ip, $username) {
		$db = FatApp::getDb();
		
		$srch = new SearchBase('tbl_failed_login_attempts');
		$srch->addCondition('attempt_ip', '=', $ip)->attachCondition('attempt_username', '=', $username);
		$srch->addCondition('attempt_time', '>=', date('Y-m-d H:i:s', strtotime("-1 Minute")));
		$srch->addFld('COUNT(*) AS total');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return ($row['total'] > 2);
	}
	
	public function login($useremail, $password, $ip, $encryptPassword = true) {
		if ($this->isBruteForceAttempt($ip, $useremail)) {
			$this->error = Info::t_lang('Login attempt limit exceeded. Please try after some time.');
			return false;
		}
	
		if ($encryptPassword) {
			$password = UserAuthentication::encryptPassword($password);
			
		}
		
		$db = FatApp::getDb();
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_email', '=', $useremail);
		$srch->addCondition('user_password', '=', $password);
		$rs = $srch->getResultSet();
		if (!$row = $db->fetch($rs)) {
			$this->logFailedAttempt($ip, $useremail);
			$this->error = Info::t_lang('Invalid Email or Password');
			return false;
		}
		if ( strtolower($row['user_email']) != strtolower($useremail) || $row['user_password'] != $password ) {
			$this->logFailedAttempt($ip, $useremail);
			$this->error = Info::t_lang('Invalid Email or Password');
			return false;
		}
		
		if ($row['user_active'] != 1 ) { // See I am not comparing it to zero.
			$this->error = Info::t_lang('Error! Your account has been deactivated. Please contact administrator.');
			return false;
		}
		/* if ($row['user_verified'] != 1) { // See I am not comparing it to zero.
			$this->error = Info::t_lang('Error! Account verification pending.');
			return false;
		} */
		
		$rowUser = User::getAttributesById($row['user_id']);
		
		return self::setUserLoginSession($rowUser,$ip);
		
	}
	
	public static function setUserLoginSession($rowUser,$ip){
		if(isset($rowUser['user_password'])) unset($rowUser['user_password']);
		if(isset($rowUser['user_registered'])) unset($rowUser['user_registered']);
		if ($rowUser['user_active'] != 1 ) { // See I am not comparing it to zero.
			return false;
		}
		$usr = new User();
		$userDetail = $usr->getUserDetails($rowUser['user_id']);
		$_SESSION[static::SESSION_ELEMENT_NAME] = array(
				'user_id'=>$rowUser['user_id'],
				'user_email'=>$rowUser['user_email'],
				'udetails_first_name'=>$userDetail['udetails_first_name'],
				'udetails_last_name'=>$userDetail['udetails_last_name'],
				'user_ip'=>$ip
		);
		return true;
	}
	
	public static function isUserLogged($ip = '') {
		if ($ip == '') {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		if (! isset ( $_SESSION [static::SESSION_ELEMENT_NAME] ) 
				|| $_SESSION [static::SESSION_ELEMENT_NAME] ['user_ip'] != $ip 
				|| ! is_numeric ( $_SESSION [static::SESSION_ELEMENT_NAME] ['user_id'] ) 
				|| 0 >= $_SESSION [static::SESSION_ELEMENT_NAME] ['user_id'] ) {
			return false;
		}
		
	
		return true;
	}
	
	public static function getUserSession($attr, $returnNullIfNotLogged = false) {
		if ( ! static::isUserLogged() ) {
			if ( $returnNullIfNotLogged ) return null;
				
			FatUtility::dieWithError('User Not Logged.');
		}
	
		if ( array_key_exists($attr, $_SESSION [static::SESSION_ELEMENT_NAME]) ) {
			return $_SESSION [static::SESSION_ELEMENT_NAME][$attr];
		}
	
		return User::getAttributesById($_SESSION[static::SESSION_ELEMENT_NAME]['user_id'], $attr);
	}
	
	public static function getLoggedUserId($returnZeroIfNotLogged = false) {
		return FatUtility::int(static::getUserSession('user_id', $returnZeroIfNotLogged));
	}
}