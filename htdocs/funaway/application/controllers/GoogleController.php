<?php

class GoogleController extends MyAppController
{

    protected $google;
    protected $googleService;

    public function __construct($action)
	{
        parent::__construct($action);
        require_once CONF_INSTALLATION_PATH . 'library/google/Google_Client.php';
        require_once CONF_INSTALLATION_PATH . 'library/google/contrib/Google_Oauth2Service.php';
        $redirect_uri = FatUtility::generateFullUrl('google', 'callback');
        $this->google = new Google_Client();

        $this->google->setClientId(FatApp::getConfig('CONF_GOOGLE_APP_ID'));
        $this->google->setClientSecret(FatApp::getConfig('CONF_GOOGLE_SECRET_KEY'));
        $this->google->setRedirectUri($redirect_uri);
        $this->google->setScopes(array('email', 'profile'));

        $this->googleService = new Google_Oauth2Service($this->google);
    }

    function index()
	{
        $authUrl = $this->google->createAuthUrl();
        FatApp::redirectUser($authUrl);
    }

    function callback()
	{
		if (isset($_GET['error'])) {
            Message::addErrorMessage($_GET['error']);
            FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        }

        if (!isset($_GET['code'])) {
            FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        }
		
        try {
            $this->google->authenticate($_GET['code']);
        } catch (Exception $e) {
            Message::addErrorMessage($e->getMessage());
            FatApp::redirectUser(FatUtility::generateUrl(array('guestUser', 'loginForm')));
        }

        $this->token = $this->google->getAccessToken();
        $this->_getInfo($this->token);
    }

    private function _getInfo($accessToken)
	{
		$this->google->setAccessToken($accessToken);
        $userProfile = $this->googleService->userinfo->get();
        $this->saveUser($userProfile);
    }

    private function format_data($data)
	{
		$saveData = array(
            'user_email' => $data['email'],
            'user_firstname' => $data['given_name'],
            'user_lastname' => $data['family_name'],
            'user_signup_media' => 1,
    
        );

        return $saveData;
    }

    private function saveUser($data)
	{
		$saveData = $this->format_data($data);
        
		$email = $saveData['user_email'];
		
		$usr = new User();
        
		$userData = $usr->getUserByEmail($email);
		if (!empty($userData)) {
            if (!$usr->login($email, $userData['user_password'], $_SERVER['REMOTE_ADDR'], false)) {
                Message::addErrorMessage(Info::t_lang("SOMETHING_WENT_WRONG!"));
                FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
            }
            $url = FatUtility::generateUrl();
            if ($userData['user_type'] == 1) {
                $url = FatUtility::generateUrl('traveler');
            } elseif ($userData['user_type'] == 0) {
                $url = FatUtility::generateUrl('host');
            }
			
            FatApp::redirectUser($url);
        }
        
        Helper::setSocialSession($saveData);
        $signUpUrl = FatUtility::generateUrl('guest-user', 'signup-form');
        FatApp::redirectUser($signUpUrl);
    }
}
