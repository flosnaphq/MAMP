<?php
if($_SERVER["SERVER_NAME"] == '192.168.1.25'){
	$url = 'http://192.168.1.25/dv/a/a/verifiedcredible/index.php/t_content_inclusion';
}else{
	$url=$_SERVER["SERVER_NAME"].'/t_content_inclusion';
}
if(trim($url)!=''){
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
}
/**
 * @file
 * A single location to store configuration.
 */

/* define('CONSUMER_KEY', 'VzQew57rA9IhJM7D32J3Q');
define('CONSUMER_SECRET', '8ZZ76KvhPjUOEOg0IoAmmHVl5sOTqqXbgKyY7f0'); */

/*Local server*/


/*Beta server*/
/* define('CONSUMER_KEY', 'sRLFR4LH1vG5j3SvoJo2RQ');
define('CONSUMER_SECRET', 'TOtOn5yS9bL0SG2Vh4AwGETUhzXJjXzhf83EdO6hs4');
define('OAUTH_CALLBACK', 'http://www.beta.verifiedcredible.com/content_inclusion'); */

/*Live server*/
/* define('CONSUMER_KEY', 'l2lDsPT9sDAK1XcWdQXFzw');
define('CONSUMER_SECRET', 'uUUZesosOWRfpWqEDqq4Zx9PM9zwsZY9h43bkcRcw');
define('OAUTH_CALLBACK', 'http://www.verifiedcredible.com/t_content_inclusion') */;