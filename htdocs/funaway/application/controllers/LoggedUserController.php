<?php
class userController extends MyAppController {
	public function __construct($action) {
		if (!User::isUserLogged()) {
			if (FatUtility::isAjaxCall()) {
				FatUtility::dieWithError('Session seems to be expired!');
			}
			FatApp::redirectUser(FatUtility::generateUrl('GuestUser', 'loginForm'));
		}
		parent::__construct($action);
	}
	
	
}