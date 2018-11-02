<?php defined('SYSTEM_INIT') or die("INVALID ACCESS"); ?>
<ul class="centered_nav">
	<li><a href="<?php echo FatUtility::generateUrl('profile');?>" class="<?php echo ($clss == 'edit_prof' ? ' active ':'');?>">Profile</a></li>
	<li><a href="<?php echo FatUtility::generateUrl('profile', 'changePassword');?>" class="<?php echo ($clss == 'chg_pass' ? ' active ':'');?>">Change Password</a></li>
</ul>