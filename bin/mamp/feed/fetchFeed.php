<?php

if (count($argv) < 4)
{
	exit("usage: php fetchFeed.php APP_NAME LANGUAGE FILE_NAME_REMOTE (FILE_NAME_LOCAL)\ne.g. php fetchFeed.php MAMPPRO home.json version.json\n");
}

$endpoint = "http://www.mamp.info/feed";
$urlTemplate = "%s/%s/%s/%s/feed/%s";
$debug = false;

$os =  (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')  ? "win" : "mac";

$app = $argv[1];
$language = "all";
$file = $argv[3];
$fileLocal = (isset($argv[4]) && !empty($argv[4]) ? $argv[4] : $file);
$url = sprintf($urlTemplate,$endpoint,$os,$app,($language == "all" ? "English" : $language),$file);
$json = file_get_contents($url);
$feed = json_decode($json);

if (!$feed )
{
    exit();
}

$feedDir = dirname(__FILE__);
$rootDir = realpath($feedDir ."/..");

$feedPath = $feedDir."/".$fileLocal;

if($language != "all") {
	$feedPath = $feedDir."/".$language."/".$fileLocal;
	if(!is_dir(dirname($feedPath))) {
		@mkdir($feedPath,0777,true);
	}
}

$needsUpdatePath = $feedDir."/needsUpdate.txt";

// remove unused data:
if(isset($feed->carousel)) {
	unset($feed->carousel);
}
if(isset($feed->news)) {
	unset($feed->news);
}
$json = json_encode($feed);

$_writeIt = true;
if (file_exists($feedPath))
{
	$contents = file_get_contents($feedPath);
	if ($json == $contents)
	{
		$_writeIt = false;
	}
}

if ($_writeIt)
{
	file_put_contents($feedPath,$json);
	file_put_contents($needsUpdatePath,"1");
}
else
{
	file_put_contents($needsUpdatePath,"0");
}


function getImageFromServer($endpoint,$img)
{
	global $debug,$rootDir;
	if (substr($img,0,5) == "feed/")
	{
		$imagePath = $rootDir . "/" . $img;
		if (!file_exists($imagePath))
		{
			$url = $endpoint."/".substr($img,5);
			if ($debug) echo "fetching: $url\n";
			$image = file_get_contents($url);
			if ($image)
			{
				if ($debug) echo "writing: $imagePath\n";
				file_put_contents($imagePath,$image);
			}
		}
	}
}