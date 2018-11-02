<?php 
$valid_urls = array(
'ping/wallet',
'ping/subscription',
'ping/order',
);

if (!in_array($_GET['url'], $valid_urls)) die('Unauthorized Access.');
// or any logic to verify here
require_once 'index.php';
?>