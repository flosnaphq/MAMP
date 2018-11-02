<?php
class linkedClass {
 //   private $config                         =   array();

    public function __construct()
    {
       
    //    $this->config     =  $config;
       
    }

    public function linkedinGetUserInfo( $requestToken='', $oauthVerifier='', $accessToken=''){
         
		require_once CONF_INSTALLATION_PATH."library/linkedin/oAuth/config.php";
        include_once CONF_INSTALLATION_PATH.'library/linkedin/oAuth/linkedinoAuth.php';
		
        $linkedin = new LinkedIn(LINKEDIN_ACCESS, LINKEDIN_SECRET);
        
		$linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);
		
		

        try{
            $xml_response = $linkedin->getProfile("~:(first-name,last-name,email-address,picture-url)");
			echo $xml_response;
        }
        catch (Exception $o){
            print_r($o);
        }
		return $xml_response;
    }
}
?>
