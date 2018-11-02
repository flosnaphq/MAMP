<?php

include_once 'php/functions.php';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="shortcut icon" href="./images/favicon.ico" />

		<title><?php echo appName(); ?></title>

		<!-- Bootstrap core CSS -->
		<link href="css/bootstrap.css" rel="stylesheet" />

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
		<![endif]-->

		<!-- Custom styles for this template -->
		<link href="css/style.css?t=<?php echo time(); ?>" rel="stylesheet" />

<?php if($page == "home") {
if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
{
		include_once('../phpMyAdmin/config.inc.php');
}
else
{
	if ($configObject->app == "MAMP")
	{
		include_once('/Applications/MAMP/bin/phpMyAdmin/config.inc.php');
	}
	else
	{
		include_once('/Library/Application Support/appsolute/MAMP PRO/phpMyAdmin/config.inc.php');
	}
}
?>
		<link href="css/screen.css" rel="stylesheet">
<?php } else if($page == "phpinfo") { ?>
		<style>
			table {border-collapse: collapse;}
			.center {text-align: center;}
			.center table { margin-left: auto; margin-right: auto; text-align: left;}
			.center th { text-align: center !important; }
			td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
			h1 {font-size: 150%;}
			h2 {font-size: 125%;}
			.p {text-align: left;}
			.e {background-color: #ccccff; font-weight: bold; color: #000000;}
			.h {background-color: #9999cc; font-weight: bold; color: #000000;}
			.v {background-color: #cccccc; color: #000000;}
			.vr {background-color: #cccccc; text-align: right; color: #000000;}
			img {float: right; border: 0px;}
			hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
		</style>
<?php } ?>
	</head>
<!-- NAVBAR
================================================== -->
	<body>
		<div class="navbar-wrapper">
			<div class="container">
				<div class="navbar navbar-inverse navbar-static-top" role="navigation">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
							<li<?php if($page=="home") { ?> class="active"<?php } ?>><a href="index.php?language=<?php echo $language; ?>"><?php echo tr("Start"); ?></a></li>
							<?php $myWebsite='http://localhost:8888'; if($myWebsite!='') { echo '<li><a class="navbar-brand" href="' . $myWebsite . '" target="_blank">' . tr('My Website') . '</a></li>'; } ?>
							<li<?php if (strpos('apc eaccelerator xcache phpmyadmin phpliteadmin phpinfo', $page) !== false) { ?> class="active dropdown"<?php } else { ?> class="dropdown" <?php } ?>>
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo tr("Tools") ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">

                                    <li<?php if($page=="phpinfo") { ?> class="active"<?php } ?>><a href="index.php?language=<?php echo $language; ?>&amp;page=phpinfo">phpInfo</a></li>

                                    <?php if(version_compare(PHP_VERSION, '5.5.0', '>=')): ?>
                                        <li><a target="_blank" href="<?php echo "phpmyadmin.php?lang=".tr("phpMyAdminLanguage"); ?>">phpMyAdmin</a></li>
                                    <?php else: ?>
                                        <li class="disabled"><a href="#">phpMyAdmin (<?php echo tr("needs at least PHP 5.5.x"); ?>)</a></li>
                                    <?php endif; ?>

                                    <?php if(version_compare(PHP_VERSION, '5.2.4', '>=') and version_compare(PHP_VERSION, '7.0.99', '<=')): ?>
                                        <li><a target="_blank" href="<?php echo "/phpLiteAdmin/"; ?>">phpLiteAdmin</a></li>
                                    <?php else: ?>
                                        <li class="disabled"><a href="#">phpLiteAdmin (<?php echo tr("needs PHP 5.2.4 to 7.0.x"); ?>)</a></li>
                                    <?php endif; ?>

                                    <li class="divider"><a href="#"></a></li>

                                    <?php if(extension_loaded('apc')): ?>
                                        <li><a target="_blank" href="<?php echo "apc.php"; ?>">APC</a></li>
                                    <?php else: ?>
                                        <li class="disabled"><a href="#">APC (<?php echo tr("not loaded"); ?>)</a></li>
                                    <?php endif ?>

                                    <?php if(extension_loaded('eAccelerator')): ?>
                                        <li><a target="_blank" href="<?php echo "eaccelerator.php"; ?>">eAccelerator</a></li>
                                    <?php else: ?>
                                        <li class="disabled"><a href="#">eAccelerator (<?php echo tr("not loaded"); ?>)</a></li>
                                    <?php endif ?>

                                    <?php if(extension_loaded('XCache')): ?>
                                        <li><a target="_blank" href="<?php echo "xcache-admin/"; ?>">XCache</a></li>
                                    <?php else: ?>
                                        <li class="disabled"><a href="#">XCache (<?php echo tr("not loaded"); ?>)</a></li>
                                    <?php endif ?>

                                    <?php if(extension_loaded('Zend OPcache')): ?>
                                        <li><a target="_blank" href="<?php echo "opcache.php"; ?>">OPcache</a></li>
                                    <?php else: ?>
                                        <li class="disabled"><a href="#">OPcache (<?php echo tr("not loaded"); ?>)</a></li>
                                    <?php endif ?>

								</ul>
							</li>
							<li><a class="navbar-brand" href="https://www.mamp.info/en/mamp-pro" target="_blank"><?php printf(tr("%s Website"),appName()); ?></a></li>
							<?php $myFavLink=''; if($myFavLink!='') { echo '<li><a class="navbar-brand" href="' . $myFavLink . '" target="_blank">' . tr('My favorite Link') . '</a></li>'; } ?>
						</ul><!-- end left -->
						<ul class="nav navbar-nav navbar-right">
						<?php if (1 != is_bought()){ ?>
							<li class="btn-buy"><a style="color:white;" href="<?php echo buyLink(); ?>" target="_blank"><?php echo tr("Buy MAMP PRO"); ?></a></li>
						<?php } else { ?>
							<li><a style="margin-top:10px; margin-right:10px;padding:0;" href="http://www.appsolute.de" target="_blank"><img src="images/appsolute-logo.svg" height="30" style="margin:0;padding:0;"/></a></li>
						<?php } ?>
						</ul><!-- end right -->
					</div><!-- end collapse -->
				</div><!-- end navbar -->
			</div><!-- end container -->
		</div><!-- end navbar-wrapper -->

<?php if($page == "phpinfo") { ?>
		<div class="top100">
            <div class="frame" style="width:90%; margin:0px auto;">
				<?php echo $phpinfo; ?>
			</div>
		</div>
<?php } else if($page == "phpmyadmin") { ?>
      <?php print iFrame("phpmyadmin.php?lang=".tr("phpMyAdminLanguage")); ?>
<?php } else if($page == "phpliteadmin") { ?>
      <?php print iFrame("/phpLiteAdmin/"); ?>
<?php } else if($page == "apc") { ?>
      <?php print iFrame("apc.php"); ?>
<?php } else if($page == "eaccelerator") { ?>
      <?php print iFrame("eaccelerator.php"); ?>
<?php } else if($page == "xcache") { ?>
      <?php print iFrame("xcache-admin/"); ?>
<?php } else { ?>

    <!-- Carousel
    ================================================== -->
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
 <?php

	$feed = getFeed();
	$items = createItemsForCarouselFromFeedObject($feed);
        $currVersion = isset($feed->currentVersion) ? $feed->currentVersion : $configObject->version;
        $newsItems = isset($feed->news) ? $feed->news : array();

 ?>
		<!-- Indicators -->
<?php if (count($items) > 0) { ?>
			<ol class="carousel-indicators">
				<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
<?php for ($i=0; $i<count($items);$i++) { ?>
				<li data-target="#myCarousel" data-slide-to="<?php echo (1+$i); ?>"></li>
<?php } ?>
			</ol>
<?php } ?>
			<div class="carousel-inner">
				<div class="item active <?php echo (appName() == "MAMP PRO" ? "carousel-back-blue" : "carousel-back-grey"); ?> ">
					<div class="carousel-caption">
						<div class="container">
							<table border="0">
								<tr>
									<td>
										<h1><?php printf(tr("Welcome to %s"),appName()); ?></h1>
										<h2><?php printf(tr("Your version is %s"),$currVersion); ?>
										<?php if (isset($feed->version) && version_compare($feed->version, $currVersion, '<')) { ?>
											<?php echo $configObject->version . '&nbsp;&rarr;&nbsp;'; ?><a href="http://www.mamp.info/en/downloads/#mac"><?php printf(tr('Latest version: %s'), $feed->currentVersion); ?></a>
										<?php } else { ?>
											<?php echo tr('and up to date.'); ?>
										<?php } ?>
										</h2>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
<?php foreach ($items as $item) {  ?>
				<div class="item<?php echo isset($item->backgroundClass) ? " ".$item->backgroundClass : ""; ?>"<?php echo isset($item->backgroundImage) ? ' style="background-image:url('.$item->backgroundImage.');background-repeat:repeat;"' : ''; ?>>
					<div class="carousel-caption">
						<div class="container">
							<table border="0">
								<tr>
									<td width="30%" class="hidden-xs">
										<img src="<?php print $item->leftImage;?>" style="width:100%">
									</td>
									<td<?php echo isset($item->paddingTop) ?  ' style="padding-top:'.$item->paddingTop.';"' : ''; ?>>
										<?php print $item->centerHTML; ?>
									</td>
									<td width="30%" class="hidden-xs">
										<img src="<?php print $item->rightImage;?>" style="width:100%">
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
<?php } ?>
			</div>
		</div><!-- /.carousel -->

		<div class="container marketing">
			<div class="row">
				<div class="col-sm-4">
					<div class="frame">
						<?php if (1 != is_bought() || appName() == "MAMP"){ ?>
							<h1><?php echo tr("Buy MAMP PRO"); ?></h1>
							<p class="subtitle"><?php echo tr("Buy MAMP PRO SUBTITLE"); ?></p>
							<p><a class="headerlink" href="https://www.mamp.info/macstore" target="_blank"><img src="images/headers/mamppro.png" class="titleimage img-responsive" /></a></p>
							<p><?php echo tr("Buy MAMP PRO TEXT"); ?></p>
							<p>&nbsp;</p>
						<?php } else { ?>
							<h1><?php echo tr("MAMP.TV"); ?></h1>
							<p class="subtitle"><?php echo tr("MAMP.TV SUBTITLE"); ?></p>
							<p><a class="headerlink" href="http://www.mamp.tv" target="_blank"><img src="images/headers/mamptv.png" class="titleimage img-responsive" /></a></p>
							<p><?php echo tr("MAMP.TV TEXT"); ?></p>
							<p>&nbsp;</p>
						<?php } ?>

						<h2>PHP</h2>
						<p><?php printf(tr('%sphpinfo%s shows the current configuration of PHP.'), a('phpinfo'),'</a>'); ?></p><br>

						<h2>PHP-Caches</h2>
						<p>
							<ul class="caches">
                            <?php if(extension_loaded('apc')): ?>
                                <li><a target="_blank" href="<?php echo "apc.php"; ?>">APC</a></li>
                            <?php else: ?>
                                <li class="disabled">APC (<?php echo tr("not loaded"); ?>)</li>
                            <?php endif ?>

                            <?php if(extension_loaded('eAccelerator')): ?>
                                <li><a target="_blank" href="<?php echo "eaccelerator.php"; ?>">eAccelerator</a></li>
                            <?php else: ?>
                                <li class="disabled">eAccelerator (<?php echo tr("not loaded"); ?>)</li>
                            <?php endif ?>

                            <?php if(extension_loaded('XCache')): ?>
                                <li><a target="_blank" href="<?php echo "xcache-admin/"; ?>">XCache</a></li>
                            <?php else: ?>
                                <li class="disabled">XCache (<?php echo tr("not loaded"); ?>)</li>
                            <?php endif ?>

                            <?php if(extension_loaded('Zend OPcache')): ?>
                                <li><a target="_blank" href="<?php echo "opcache.php"; ?>">OPcache</a></li>
                            <?php else: ?>
                                <li class="disabled">OPcache (<?php echo tr("not loaded"); ?>)</li>
                            <?php endif ?>
							</ul>
						</p><br>

						<h2>phpMyAdmin</h2>
                        <?php if(version_compare(PHP_VERSION, '5.5.0', '>=')): ?>
							<p><?php printf(tr('Configure your MySQL databases with %sphpMyAdmin%s.'), '<a href="phpmyadmin.php?lang='.tr("phpMyAdminLanguage").'" target="_blank">','</a>'); ?></p><br>
                        <?php else: ?>
							<p>phpMyAdmin <?php echo tr("needs at least PHP 5.5.x"); ?></p><br>
                        <?php endif; ?>

						<h2>phpLiteAdmin</h2>
                        <?php if(version_compare(PHP_VERSION, '5.2.4', '>=') and version_compare(PHP_VERSION, '7.0.99', '<=')): ?>
							<p><?php printf(tr('Configure your sqlite databases with %sphpLiteAdmin%s.'), a('phpliteadmin'),'</a>'); ?></p><br>
                        <?php else: ?>
							<p class="disabled">phpLiteAdmin <?php echo tr("needs PHP 5.2.4 to 7.0.x"); ?></p><br>
                        <?php endif; ?>

						<!--
						<h2><?php printf(tr('%s Version'),appName()); ?></h2>
<?php if (version_compare($configObject->version, $currVersion, '<')) { ?>
						<h4><?php echo $configObject->version . '&nbsp;&rarr;&nbsp;'; ?><a href="http://www.mamp.info/en/downloads/#mac"><?php printf(tr('Update (%s) available!'), $feed->currentVersion); ?></a></h4>
<?php } else { ?>
						<h4><?php echo $configObject->version; ?>&nbsp;&rarr;&nbsp;<?php echo tr('Your version is up-to-date.'); ?></h4>
<?php } ?>
						-->
					</div>
				</div>
				<div class="col-sm-4">
					<div class="frame">
						<?php if(appName() == "MAMP PRO") { ?>
							<h1><?php echo tr("Support"); ?></h1>
							<p class="subtitle"><?php echo tr("Support SUBTITLE"); ?></p>
							<p><a class="headerlink" href="https://appsolute.zendesk.com/" target="_blank"><img src="images/headers/support.png" class="titleimage img-responsive" /></a></p>
							<p><?php echo tr("Support TEXT"); ?></p>
						<?php } else if (1 != is_cloud_bought()){ ?>
							<h1><?php echo tr("Get MAMP Cloud"); ?></h1>
							<p class="subtitle"><?php echo tr("Get MAMP Cloud SUBTITLE"); ?></p>
							<p><a class="headerlink" href="https://www.mamp.info/<?php echo ($language == "German" ? "de" : "en"); ?>/mamp/cloud/" target="_blank"><img src="images/headers/cloud.png" class="titleimage img-responsive" /></a></p>
							<p><?php echo tr("Get MAMP Cloud TEXT"); ?></p>
						<?php } else { ?>
							<h1><?php echo tr("MAMP.TV"); ?></h1>
							<p class="subtitle"><?php echo tr("MAMP.TV SUBTITLE"); ?></p>
							<p><a class="headerlink" href="http://www.mamp.tv" target="_blank"><img src="images/headers/mamptv.png" class="titleimage img-responsive" /></a></p>
							<p><?php echo tr("MAMP.TV TEXT"); ?></p>
						<?php } ?>
						<p>&nbsp;</p>
						<h2>MySQL</h2>
						<p>
                        <?php if(version_compare(PHP_VERSION, '5.5.0', '>=')): ?>
							<?php printf(tr('MySQL can be administered with %sphpMyAdmin%s.'), '<a href="phpmyadmin.php?lang='.tr("phpMyAdminLanguage").'" target="_blank">','</a>'); ?>
						<?php else: ?>
							<?php printf(tr('MySQL can be administered with %sphpMyAdmin%s.'), '',''); ?>
						<?php endif; ?>
						</p>

						<p><?php echo tr("To connect to the MySQL Server from your own scripts use the following connection parameters:"); ?></p>
						<p><table class="mysql">
							<tr>
								<th><?php echo tr("Host"); ?></th>
								<td><?php echo $cfg['Servers'][1]['host']; ?></td>
							</tr>
							<tr>
								<th><?php echo tr("Port"); ?></th>
								<td><?php echo $cfg['Servers'][1]['port'] ? $cfg['Servers'][1]['port'] : "3306"; ?></td>
							</tr>
							<tr>
								<th><?php echo tr("User"); ?></th>
								<td><?php echo $cfg['Servers'][1]['user']; ?></td>
							</tr>
							<tr>
								<th><?php echo tr("Password"); ?></th>
								<td><?php echo $cfg['Servers'][1]['password']; ?></td>
							</tr>
							<tr>
								<th><?php echo tr("Socket"); ?></th>
								<td>/Applications/MAMP/tmp/mysql/mysql.sock</td>
							</tr>
						</table></p>
						<br>
						<h2><?php echo tr("Examples:"); ?></h2>
						<ul class="nav nav-pills" role="tablist">
							<li class="active"><a href="#php56" role="tab" data-toggle="tab">PHP >= 5.6.x</a></li>
							<li><a href="#php" role="tab" data-toggle="tab">PHP <= 5.5.x</a></li>
							<li><a href="#python" role="tab" data-toggle="tab">Python</a></li>
							<li><a href="#perl" role="tab" data-toggle="tab">Perl</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane" id="php">
							<br>
							<p><?php echo tr("Connect via network:"); ?></p>
<pre>
$user = '<?php echo $cfg['Servers'][1]['user']; ?>';
$password = '<?php echo $cfg['Servers'][1]['password']; ?>';
$db = 'inventory';
$host = 'localhost';
$port = <?php echo $cfg['Servers'][1]['port'] ? $cfg['Servers'][1]['port'] : "3306" ?>;

$link = mysql_connect(
   "$host:$port",
   $user,
   $password
);
$db_selected = mysql_select_db(
   $db,
   $link
);
</pre>

							<?php if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') { ?>
							<br>
							<p><?php echo tr("Connect using an UNIX Socket:"); ?></p>
<pre>
$user = '<?php echo $cfg['Servers'][1]['user']; ?>';
$password = '<?php echo $cfg['Servers'][1]['password']; ?>';
$db = 'inventory';
$socket = 'localhost:/Applications/MAMP/tmp/mysql/mysql.sock';

$link = mysql_connect(
   $socket,
   $user,
   $password
);
$db_selected = mysql_select_db(
   $db,
   $link
);
</pre>
							<?php } ?>
						</div> <!-- PHP tab pane -->

						<div class="tab-pane active" id="php56">
							<br>
							<p><?php echo tr("Connect via network:"); ?></p>
<pre>
$user = '<?php echo $cfg['Servers'][1]['user']; ?>';
$password = '<?php echo $cfg['Servers'][1]['password']; ?>';
$db = 'inventory';
$host = 'localhost';
$port = <?php echo $cfg['Servers'][1]['port'] ? $cfg['Servers'][1]['port'] : 3306 ?>;

$link = mysqli_init();
$success = mysqli_real_connect(
   $link,
   $host,
   $user,
   $password,
   $db,
   $port
);
</pre>

							<?php if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') { ?>
							<br>
							<p><?php echo tr("Connect using an UNIX Socket:"); ?></p>
<pre>
$user = '<?php echo $cfg['Servers'][1]['user']; ?>';
$password = '<?php echo $cfg['Servers'][1]['password']; ?>';
$db = 'inventory';
$host = '127.0.0.1';
$port = <?php echo $cfg['Servers'][1]['port'] ? $cfg['Servers'][1]['port'] : 3306 ?>;
$socket = 'localhost:/Applications/MAMP/tmp/mysql/mysql.sock';

$link = mysqli_init();
$success = mysqli_real_connect(
   $link,
   $host,
   $user,
   $password,
   $db,
   $port,
   $socket
);
</pre>
							<?php } ?>
						</div> <!-- PHP 5.6 tab pane -->

						<div class="tab-pane" id="python">
							<br>
							<p><?php echo tr("Connect via network:"); ?></p>
<pre>import mysql.connector

config = {
  'user': '<?php echo $cfg['Servers'][1]['user']; ?>',
  'password': '<?php echo $cfg['Servers'][1]['password']; ?>',
  'host': 'localhost:<?php echo $cfg['Servers'][1]['port'] ? $cfg['Servers'][1]['port'] : "3306" ?>',
  'database': 'inventory',
  'raise_on_warnings': True,
}

link = mysql.connector.connect(**config)
</pre>

							<?php if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') { ?>
							<br>
							<p><?php echo tr("Connect using an UNIX Socket:"); ?></p>
<pre>import mysql.connector

config = {
  'user': '<?php echo $cfg['Servers'][1]['user']; ?>',
  'password': '<?php echo $cfg['Servers'][1]['password']; ?>',
  'unix_socket': '/Applications/MAMP/tmp/mysql/mysql.sock',
  'database': 'inventory',
  'raise_on_warnings': True,
}

link = mysql.connector.connect(**config)
</pre>
							<?php } ?>
						</div> <!-- Python tab pane -->

						<div class="tab-pane" id="perl">
							<br>
							<p><?php echo tr("Connect via network:"); ?></p>
<pre>
use DBI;

my $user = '<?php echo $cfg['Servers'][1]['user']; ?>';
my $password = '<?php echo $cfg['Servers'][1]['password']; ?>';
my $db = 'inventory';

my $link = DBI->connect(
   "DBI:mysql:database=$db",
   $user,
   $password
);
</pre>
							<br>
							<p><?php echo tr("Connect using an UNIX Socket:"); ?></p>
<pre>
use DBI;

my $user = '<?php echo $cfg['Servers'][1]['user']; ?>';
my $password = '<?php echo $cfg['Servers'][1]['password']; ?>';
my $db = 'inventory';
my $host = 'localhost';
my $port = <?php echo $cfg['Servers'][1]['port'] ? $cfg['Servers'][1]['port'] : "3306" ?>;

my $link = DBI->connect(
   "DBI:mysql:database=$db;host=$host;port=$port",
   $user,
   $password
);
</pre>
							</div> <!-- Perl tab pane -->
						</div> <!-- tab content -->
					</div>
					<!--
					<div class="frame">
						<a href="http://www.appsolute.de" target="_blank"><img src="images/appsolute-logo.svg" height="30" alt="appsolute GmbH"/></a><br />
						<?php printf(tr("<b>MAMP</b> and <b>MAMP PRO</b> are developed and distributed by %s."),'<a href="http://www.appsolute.de" target="_blank">appsolute GmbH</a>'); ?><br /><br />
						<img src="images/logos.png" height="175" alt="Apache, Nginx, MySQL, Memcached, PHP, Perl, Python, Ruby"/>
					</div>
					-->
				</div>
				<div class="col-sm-4">
					<div class="frame">
						<h1><?php echo tr('MAMP News'); ?></h1>
						<p class="subtitle"><?php echo tr("MAMP News SUBTITLE"); ?></p>
						<p><a class="headerlink" href="https://twitter.com/mamp_en" target="_blank"><img src="images/headers/news.png" class="titleimage img-responsive" /></a></p>
						<a class="twitter-timeline" data-width="400" data-theme="light" data-tweet-limit="5" href="https://twitter.com/mamp_en?ref_src=twsrc%5Etfw">Tweets by mamp_en</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
					</div>
				</div>
			</div><!-- /.row -->
			<!-- FOOTER -->
			<footer style="margin-top:40px;">
				<p class="pull-right"><a href="#"><?php echo tr("Back to top"); ?></a></p>
				<p>&copy; 2013 - 2018 <a href="http://www.appsolute.de" target="_blank">appsolute GmbH</a></p>
			</footer>
		</div><!-- /.container -->

<?php } ?>
	    <!-- Bootstrap core JavaScript
    	================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
    	<script src="js/jquery-1.10.2.min.js"></script>
	    <script src="js/bootstrap.min.js"></script>
<?php if($page == "home") { ?>
	    <script type="text/javascript">

			function checkNeedsUpdate()
			{
				$.get( "feed/needsUpdate.txt", function( data ) {
				if (data == 1)
				{
					location.reload();
				}});
			}

			setTimeout(function(){checkNeedsUpdate();},2000);

    	</script>
<?php } ?>
	</body>
</html>
<?php

downloadFeed();
write_tr();

?>
