<?php
function toArray($obj)
{
   if(is_object($obj)) $obj = (array) $obj;
   if(is_array($obj)) {
     $new = array();
     foreach($obj as $key => $val) {
       $new[$key] = toArray($val);
	 }
   }
   else { 
     $new = $obj;
   }   
   return $new;
}
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

//print_r($access_token);
/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	
$oauth_token= $access_token['oauth_token'];
$oauth_token_secret= $access_token['oauth_token_secret'];
$twitter_user_id= $access_token['user_id'];



/* If method is set change API call made. Test is called by default. */
//$content = $connection->get('account/verify_credentials');
$content = $connection->get('statuses/user_timeline', array('screen_name' => $content->screen_name,'count'=>20));

$content=toArray($content);

/* Some example calls */
//$content = $connection->get('users/show', array('screen_name' => $content->screen_name));
//$tweet_post = $connection->post('statuses/update', array('status' => date(DATE_RFC822)));
/* The code to tweet on signed in user profile.. */
//$tweet_post = $connection->post('statuses/update', array('status' => 'http://bitfatdeals.fatbit.com/deal.php?deal=66'));
//$content = $connection->get('users/show', array('screen_name' => $content->screen_name));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));


/* Include HTML to display on the page */
//echo "<pre>";print_r($content);
/* foreach($content as $value){
	
	echo 'Name: '.$value['user']['name'].'<br>';
	echo 'screen_name : '.$value['user']['screen_name'].'<br>';
	echo 'favourites_count: '.$value['user']['favourites_count'].'<br>';
	echo 'followers_count: '.$value['user']['followers_count'].'<br>';
	echo 'retweet_count: '.$value['user']['retweet_count'].'<br>';
	echo "<pre>";print_r($value['entities']['urls']).'<br>';
	echo '<img src="'.$value['user']['profile_image_url'].'"><br>';
	
	echo $value['text'].'<br>';
	echo "<br>===========</br>";
}
echo "<a href='./clearsessions.php'>clearing your session</a>"; */
//include('html.inc');