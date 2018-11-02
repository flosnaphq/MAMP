<?php
	//$path = '/home/staging/sites/yogigs/user-uploads/';
	$path = '/home/amandeep/sites/funaway-dev/public/cache/';

	exec ("find " . $path . " -type d -exec chmod 0777 {} +");
	
	
	
	?>