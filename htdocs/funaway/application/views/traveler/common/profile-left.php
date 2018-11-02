<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<nav class="menu menu--large menu--bordered">
	<ul class="list list--vertical">
		<li><a href="javascript:;" onclick="profileForm(1)"><?php echo Info::t_lang('PROFILE_INFO');?></a></li>
		<li><a href="javascript:;" onclick="profileForm(2)"><?php echo Info::t_lang('PROFILE_PHOTO');?></a></li>
		<li><a href="javascript:;" onclick="profileForm(3)"><?php echo Info::t_lang('UPDATE_PASSWORD');?></a></li>
		<li><a href="javascript:;" onclick="profileForm(4)"><?php echo Info::t_lang('UPDATE_EMAIL');?></a></li>
	</ul>
</nav>