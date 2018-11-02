<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<div class="menu-bar">
	<nav class="fl--left" role="navigation">
	  <p id="bread-crumb-label" class="assistive__text"><?php echo Info::t_lang('YOU_ARE_HERE')?>:</p>
	  <?php echo html_entity_decode($breadcrumb); ?>
	</nav>
	<nav class="menu fl--right" role="navigation">
		<ul class="list list--horizontal">
			<li><a href="<?php echo FatUtility::generateUrl('host','profile')?>" <?php if(isset($action) && $action == 'profile'){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('PROFILE')?></a></li>
			<li><a href="<?php echo FatUtility::generateUrl('host','payout')?>" <?php if(isset($action) && $action == 'payout'){ ?>class="active" <?php } ?> ><?php echo Info::t_lang('PAYOUT_SETTINGS')?></a></li>
			
			
		</ul>
	</nav>
</div>