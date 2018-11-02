<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<ul class="grids--onethird">
<?php foreach($images as $image){ ?>
	<li>
		<div class="logoWrap">
			<div class="logothumb blackclr">
				<img alt="" src="images/adminlogo.png">
				<a class="deleteLink white" href="#"><i class="ion-close-circled icon"></i></a>
			</div>
		</div>
	<li>
<?php } ?>
</ul>
                                                         