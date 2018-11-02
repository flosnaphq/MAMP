<?php

class FacebookController extends MyAppController {

    protected $fb;

    public function __construct($action) {
        parent::__construct($action);
        require_once CONF_INSTALLATION_PATH . 'library/facebook/autoload.php';

        $this->fb = new Facebook\Facebook([
            'app_id' => FatApp::getConfig('CONF_FACEBOOK_APP_ID'),
            'app_secret' => FatApp::getConfig('CONF_FACEBOOK_SECRET_KEY'),
            'default_graph_version' => 'v2.10',
			/* 'default_graph_version' => 'v2.2', */
        ]);
    }

    function index() {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email']; // optional
        $loginUrl = $helper->getLoginUrl(FatUtility::generateFullUrl('facebook', 'callback'), $permissions);
        FatApp::redirectUser($loginUrl);
    }

    function callback()
	{
        if (isset($_GET['error_code'])) {
            Message::addErrorMessage($_GET['error_description']);
            FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        }

        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            Message::addErrorMessage($e->getMessage());
            FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            Message::addErrorMessage($e->getMessage());
            FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        }

        if (isset($accessToken)) {
            $this->_getInfo($accessToken);
        }
    }

    private function _getInfo($accessToken)
	{
        $this->fb->setDefaultAccessToken($accessToken);
        try {
            $response = $this->fb->get('/me?fields=name,email,first_name,last_name,gender,birthday');
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            Message::addErrorMessage($e->getMessage());
           FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            Message::addErrorMessage($e->getMessage());
           FatApp::redirectUser(FatUtility::generateUrl('guestUser', 'loginForm'));
        }
        $userData = $response->getDecodedBody();

        $this->saveUser($userData);
    }

    private function format_data($data)
	{
		$saveData = array(
            'user_email' => $data['email'],
            'user_firstname' => $data['first_name'],
            'user_lastname' => $data['last_name'],
            //'user_profile_id' => $data['id'],
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