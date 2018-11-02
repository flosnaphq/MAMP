<?php
class User extends MyAppModel {
	
	const DB_TBL = 'tbl_users';
	const DB_TBL_PREFIX = 'user_';
	const SESSION_ELEMENT_NAME = 'UserSession'; 
	
	public function __construct($userId = 0) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $userId );
		$this->objMainTableRecord->setSensitiveFields ( array (
				'user_regdate',
				'user_id',
		) );
	}
	public function save() {
		if (! ($this->mainTableRecordId > 0)) {
			$this->setFldValue ( 'user_regdate', date ( 'Y-m-d H:i:s' ) );
		}
		
		return parent::save ();
	}
	public function setLoginCredentials($username, $password, $active = null, $verified = null) {
		if (! ($this->mainTableRecordId > 0)) {
			$this->error = 'Invalid Request! User not initialized.';
			return false;
		}
		
		$record = new TableRecord ( 'tbl_users' );
		$arrFlds = array (
				'user_email' => $username,
				'user_password' => User::encryptPassword ( $password ) 
		);
		
		if (null != $active) {
			$arrFlds ['user_active'] = $active;
		}
		if (null != $verified) {
			$arrFlds ['user_verified'] = $verified;
		}
		
		$record->setFldValue ( 'user_id', $this->mainTableRecordId );
		$record->assignValues ( $arrFlds );
		if (! $record->addNew ( array (), $arrFlds )) {
			$this->error = $record->getError ();
			return false;
		}
		
		return true;
	}
	/*
		return false if record not exist else return record array
	*/
	function getUserByEmail($email){
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_email','=', $email);
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		if(empty($row)) return false;
		return $row;
	}
	
	function getUserByUserId($user_id){
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_id','=', $user_id);
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		if(empty($row)) return false;
		return $row;
	}
	
	public function verifyAccount($v = 1) {
		if (!($this->mainTableRecordId > 0)) {
			$this->error = 'User not set.';
			return false;
		}
		
		$db = FatApp::getDb();
		if (! $db->updateFromArray ( 'tbl_users', array (
				'user_verified' => $v 
		), array (
				'smt' => 'user_id = ?',
				'vals' => array (
						$this->mainTableRecordId 
				) 
		) )) {
			$this->error = $db->getError();
			return false;
		}
		
		// You may want to send some email notification to user that his account is verified.
		
		return true;
	}
	
	public function activateAccount($v = 1) {
		if (!($this->mainTableRecordId > 0)) {
			$this->error = 'User not set.';
			return false;
		}
		
		$db = FatApp::getDb();
		if (! $db->updateFromArray ( 'tbl_users', array (
				'user_active' => $v 
		), array (
				'smt' => 'user_id = ?',
				'vals' => array (
						$this->mainTableRecordId 
				) 
		) )) {
			$this->error = $db->getError();
			return false;
		}
		
		return true;
	}
	
	public function getProfileData() {
		return $this->getAttributesById($this->mainTableRecordId);
	}
	
	
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
		$srch->addCondition('attempt_time', '>=', date('Y-m-d H:i:s', strtotime("-4 Minute")));
		$srch->addFld('COUNT(*) AS total');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return ($row['total'] > 2);
	}
	
	public function login($username, $password, $ip, $encryptPassword = true) {
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
		if ( strtolower($row['user_email']) != strtolower($username) || $row['user_password'] != $password ) {
			$this->logFailedAttempt($ip, $username);
			$this->error = 'Invalid Email or Password';
			return false;
		}
		
		if ($row['user_active'] != 1 ) { // See I am not comparing it to zero.
			$this->error = 'Error! Your account has been deactivated. Please contact administrator.';
			return false;
		}
		/* if ($row['user_verified'] != 1) { // See I am not comparing it to zero.
			$this->error = 'Error! Account verification pending.';
			return false;
		} */
		
		$rowUser = User::getAttributesById($row['user_id']);
		
		$_SESSION[static::SESSION_ELEMENT_NAME] = array(
				'user_id'=>$rowUser['user_id'],
				'user_name'=>$rowUser['user_firstname'],
				'user_email'=>$rowUser['user_email'],
				'user_type'=>$rowUser['user_type'],
				'user_ip'=>$ip,
				'currency'=>Info::getCurrentCurrency()
		);
		
		$this->updateLoginTime($rowUser['user_id']);
		return true;
	}
	
	public function updateLoginTime($user_id = 0){
		if($user_id > 0){
			$this->mainTableRecordId = $user_id;
		}
		$record = new TableRecord(self::DB_TBL);
		$data[self::DB_TBL_PREFIX.'last_login'] = Info::currentDatetime();
		$record->assignValues($data);
		return $record->update(array('smt' => 'user_d = ?', 'vals'=>array($user_id)));
			
	}
	
	public static function isUserLogged($ip = '') {
		if ($ip == '') {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
$useragent=$_SERVER['HTTP_USER_AGENT'];

if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
{
	/* print_r($_SESSION);
	echo $ip ; */
}
		if (! isset ( $_SESSION [static::SESSION_ELEMENT_NAME] ) 
				|| (int)$_SESSION [static::SESSION_ELEMENT_NAME] ['user_ip'] != (int)$ip 
				|| ! is_numeric ( $_SESSION [static::SESSION_ELEMENT_NAME] ['user_id'] ) 
				|| 0 >= $_SESSION [static::SESSION_ELEMENT_NAME] ['user_id'] ) {
			return false;
		}
		
	
		return true;
	}
	
	public static function getLoggedUserAttribute($attr, $returnNullIfNotLogged = true) {
		if ( ! static::isUserLogged() ) {
			if ( $returnNullIfNotLogged ) return false;
				
			FatUtility::dieWithError('User Not Logged.');
		}
	
		if ( array_key_exists($attr, $_SESSION [static::SESSION_ELEMENT_NAME]) ) {
			return $_SESSION [static::SESSION_ELEMENT_NAME][$attr];
		}
	
		return User::getAttributesById($_SESSION[static::SESSION_ELEMENT_NAME]['user_id'], $attr);
	}
	
	public static function getLoggedUserId($returnZeroIfNotLogged = false) {
		return FatUtility::int(static::getLoggedUserAttribute('user_id', $returnZeroIfNotLogged));
	}
	
	
	function deleteOldPasswordResetRequest(){
		$db = FatApp::getDb();
		if(!$db->deleteRecords('tbl_user_password_resets_requests',array('smt'=>'aprr_expiry < ?','vals'=>array(date('Y-m-d H:i:s'))))){
			$this->error = $db->getError();
			return false;
		}
		return true;
	}
	
	function deletePasswordResetRequest($user_id){
		$db = FatApp::getDb();
		if(!$db->deleteRecords('tbl_user_password_resets_requests',array('smt'=>'appr_user_id = ?','vals'=>array($user_id)))){
			$this->error = $db->getError();
			return false;
		}
		return true;
	}
	
	function getPasswordResetRequest($user_id){
		$search = new SearchBase('tbl_user_password_resets_requests');
		$search->addCondition('appr_user_id','=',$user_id);
		$rs = $search->getResultSet();
		return FatApp::getDb()->fetch($rs);
	}
	
	function addPasswordResetRequest($array){
		$tbl = new TableRecord('tbl_user_password_resets_requests');
		$tbl->assignValues($array);
		if(!$tbl->addNew()){
			$this->error = $db->getError();
			return false;
		}
		return true;
    }
	
	public function isValidVerifyToken($token){
		$db = FatApp::getDb();
		$srch = new SearchBase("tbl_user_verification");
		$srch->addCondition("uverification_token","=",$token);
		$rs = $srch->getResultSet();		
		$record  = $db->fetch($rs);
		if(empty($record))
			return true;
		return false;
	}
	
	function addUserVerifyToken($data){
		$db = FatApp::getDb();
		$success = $db->insertFromArray('tbl_user_verification', $data);
		if($success)
			return true;
		return false;	
	}
	
	function getUserVerifyToken($token){
		$srch = New SearchBase('tbl_user_verification');
		$srch->addCondition('uverification_token','=', $token);
		return FatApp::getDb()->fetch($srch->getResultSet());
	}
	
	function getUserVerifyTokenByUserid($user_id){
		$srch = New SearchBase('tbl_user_verification');
		$srch->addCondition('uverification_user_id','=', $user_id);
		return FatApp::getDb()->fetch($srch->getResultSet());
	}
	public function getTotalUsersCount()
	{
		$srch = new SearchBase(static::DB_TBL);
		
		$srch->addMultipleFields(
				array(
					'sum(if(user_type = 0,1,0)) total_traveler',
					'sum(if(user_type = 1,1,0)) total_host',
				)
			);
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		
		return FatApp::getDb()->fetch($rs);
	}
	
	
}