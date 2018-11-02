<?php
class ShareController extends MyAppController {
	
	
	public function __construct($action){
		parent::__construct($action);
		$this->set("class","is--dashboard");
	}
	
	
	public function image($activityId)
	{
		$activity = new Activity($activityId);
		$activity->loadFromDb();
		$flds = $activity->getFlds();
		$name       = "activity-".$flds['activity_image_id'].".pdf";
		$nameto     = "activity-".$flds['activity_image_id'].".jpg";
		define("DOMPDF_ENABLE_REMOTE",true);

		if(file_exists(CONF_UPLOADS_PATH.$nameto)){
			unlink(CONF_UPLOADS_PATH.$nameto);
		}
	
		$this->set('activity',$flds);
		$this->set('activity_content',FatApp::getConfig('CONF_ACTIVITY_SOCIAL_SHARE_CONTENT'));
		$htm = $this->_template->render(false,false,"share/image.php",true,true);
		
		require_once CONF_INSTALLATION_PATH . 'library/dompdf/dompdf_config.inc.php';
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($htm);
		$customPaper = array(0,0,580,590);
		$dompdf->set_paper($customPaper);	
		$dompdf->render();
		$pdfoutput = $dompdf->output();
		
		$filename = CONF_UPLOADS_PATH.$name;
		
		file_put_contents($filename, $pdfoutput);
		
		// $fp = fopen($filename, "a");
		// fwrite($fp, $pdfoutput);
		// fclose($fp); 
		
		$convert    = CONF_UPLOADS_PATH.$name." ".CONF_UPLOADS_PATH.$nameto;
		// $im = new imagick($filename);
		exec("convert -density 96 -trim -geometry 400x400 ".$convert, $output,$return);
		
		unlink(CONF_UPLOADS_PATH.$name);
		
		ob_end_clean();
		header('Content-type: image/jpeg');
		readfile(CONF_UPLOADS_PATH.$nameto);		
	}
	
	/* public function callback(){
			require_once('oauth/twitteroauth.php');
			require_once('config.php');

			if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
			  $_SESSION['oauth_status'] = 'oldtoken';
			  header('Location: ./destroysessions.php');
			}

			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

			$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			//save new access tocken array in session
			$_SESSION['access_token'] = $access_token;

			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);

			if (200 == $connection->http_code) {
			  $_SESSION['status'] = 'verified';
			  header('Location: ./index.php');
			} else {
			  header('Location: ./destroysessions.php');
			}
	} */
	
	public function twitter($activity_id){
		  error_reporting(0);
		  $activity = new Activity($activity_id);
			$activity->loadFromDb();
			$flds = $activity->getFlds();
		
			require_once CONF_INSTALLATION_PATH . 'library/twitter_api/twitteroauth/twitteroauth/twitteroauth.php';
			require_once CONF_INSTALLATION_PATH . 'library/twitter_api/twitteroauth/twitteroauth/tmhOAuth.php';
			require_once CONF_INSTALLATION_PATH . 'library/twitter_api/twitteroauth/twitteroauth/tmhUtilities.php';
			require_once CONF_INSTALLATION_PATH . 'library/twitter_api/twitteroauth/config.php';
				
				if(isset($_GET["redirect"]))
				{			
					$connection = new TwitterOAuth(FatApp::getConfig('CONF_TWITTER_CONSUMER_KEY'), FatApp::getConfig('CONF_TWITTER_CONSUMER_SECRET'));
					
					$request_token = $connection->getRequestToken(FatUtility::generateFullUrl('share','twitter'));
						
					$_SESSION['twitter']['oauth_token'] = $token = $request_token['oauth_token'];
					$_SESSION['twitter']['oauth_token_secret'] = $request_token['oauth_token_secret'];

					switch ($connection->http_code) {
					  case 200:
						header('Location: ' . FatUtility::generateUrl('share','twitter',array($activity_id))); exit;
						break;
					  default:
						echo 'Could not connect to Twitter. Refresh the page or try again later.';
					}
					
					exit;	
				}

				//if(!isset($_SESSION['twitter'])){				
				
				if((!isset($_SESSION['twitter']) || isset($_SESSION['twitter'])) && (isset($_SESSION['oauth_token']) || trim($_SESSION['oauth_token']) == '') && (isset($_SESSION['oauth_token_secret']) || trim($_SESSION['oauth_token_secret']) == '')){
					
				$connection = new TwitterOAuth(FatApp::getConfig('CONF_TWITTER_CONSUMER_KEY'), FatApp::getConfig('CONF_TWITTER_CONSUMER_SECRET'));
					
				
				$request_token = $connection->getRequestToken(FatUtility::generateFullUrl('share','twitter',array($activity_id)));	
				
					$_SESSION['twitter']['oauth_token'] = $token = $request_token['oauth_token'];
					$_SESSION['twitter']['oauth_token_secret'] = $request_token['oauth_token_secret'];

					switch ($connection->http_code) {
					  case 200:
						$url = $connection->getAuthorizeURL($token);
						header('Location: ' . $url); exit;
						break;
					  default:
						echo 'Could not connect to Twitter. Refresh the page or try again later.';
					}	
				}
				
					
			 	if(isset($_SESSION['twitter']) && !empty($_SESSION['twitter']) && trim($_SESSION['twitter']['oauth_token']) !='' && trim($_SESSION['twitter']['oauth_token_secret']) !='')
				{
					
					$post_id ='';	
					$oauth_token	=	trim($_SESSION['twitter']['oauth_token']);
					$oauth_token_secret	=	trim($_SESSION['twitter']['oauth_token_secret']);			
					$message = 200;					
					if(strlen($message)>100){
						$status='test';
					}else{
						$status="test";
					}
					unset($_SESSION['twitter']);
					
					$filename= CONF_UPLOADS_PATH."activity-".$flds['activity_image_id'].".jpg";
					$connection = new TwitterOAuth(FatApp::getConfig('CONF_TWITTER_CONSUMER_KEY'), FatApp::getConfig('CONF_TWITTER_CONSUMER_SECRET'), $oauth_token, $oauth_token_secret);
				
					$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			
					$oauth_token= $access_token['oauth_token'];
					$oauth_token_secret= $access_token['oauth_token_secret'];
				
						try{
							$tmhOAuth = new tmhOAuth(array(
								'consumer_key' => FatApp::getConfig('CONF_TWITTER_CONSUMER_KEY'),
								'consumer_secret' => FatApp::getConfig('CONF_TWITTER_CONSUMER_SECRET'),
								'user_token' => $oauth_token,
								'user_secret' => $oauth_token_secret,
								'curl_ssl_verifypeer'   => false 
								));								
							try{
								
								
								$code = $tmhOAuth->request('POST','https://api.twitter.com/1.1/statuses/update_with_media.json',
									array( 'media[]' => "@{$filename};type=image/jpeg;filename={$filename}", 'status' => $status ),
									true, // use auth
									true // multipart
									);
								
								if ($code == 200) {
									$tResponse=json_decode($tmhOAuth->response['response'], true);	
									$post_id=$tResponse['id_str'];
								}	
							}catch(exception $e){							print_r($e);
							}
						}catch(exception $e){	print_r($e);						
						}	
					

					if($post_id!=''){
						$arr=array('status'=>true,'msg'=>'Shared on Twitter successfullly.');
					}else{
						$arr=array('status'=>false,'msg'=>'Problem occured with sharing. Please check you Twitter authenticationss.','type'=>'T');
					} 
				}else{
					$arr=array('status'=>false,'msg'=>'Problem occured with sharing. Please check you Twitter authentication.','type'=>'T');
					
				}
		 $this->_template->render();		
	}
	
	
	public function shareActivity($activityId){
		$activity = new Activity($activityId);
		$activity->loadFromDb();
		$flds = $activity->getFlds();
		$this->set('activity',$flds);
		$htm = $this->_template->render(false,false,"share/share.php");
	}
}	
	
